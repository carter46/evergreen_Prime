<?php
/**
 * Investment maturity and user-initiated liquidation.
 */

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/usd-wallet.php';

function ensure_investment_lifecycle_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    try {
        $stmt = $pdo->prepare(
            'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
        );
        $stmt->execute(['plans', 'liquidation_cost']);
        if ((int) $stmt->fetchColumn() === 0) {
            $pdo->exec('ALTER TABLE plans ADD COLUMN liquidation_cost DECIMAL(18,2) NOT NULL DEFAULT 0.00 AFTER withdrawal_days');
        }
    } catch (Throwable $e) {
    }

    try {
        $col = $pdo->query("SELECT COLUMN_TYPE FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'user_investments' AND COLUMN_NAME = 'status'");
        $type = $col ? (string) $col->fetchColumn() : '';
        if ($type !== '' && stripos($type, 'liquidated') === false) {
            $pdo->exec("ALTER TABLE user_investments MODIFY COLUMN status ENUM('active', 'paused', 'completed', 'cancelled', 'liquidated') NOT NULL DEFAULT 'active'");
        }
    } catch (Throwable $e) {
    }

    try {
        $idx = $pdo->query(
            "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND INDEX_NAME = 'uniq_transactions_reference'"
        );
        if ($idx && (int) $idx->fetchColumn() === 0) {
            cleanup_duplicate_transaction_references($pdo, true);
            $pdo->exec('ALTER TABLE transactions ADD UNIQUE KEY uniq_transactions_reference (reference)');
        }
    } catch (Throwable $e) {
    }
}

/**
 * Find transaction rows that share the same non-empty reference.
 *
 * @return array<int, array{reference: string, cnt: int, keep_id: int, ids: string}>
 */
function find_duplicate_transaction_references(PDO $pdo): array
{
    $stmt = $pdo->query(
        "SELECT reference, COUNT(*) AS cnt, MIN(id) AS keep_id, GROUP_CONCAT(id ORDER BY id) AS ids
         FROM transactions
         WHERE reference IS NOT NULL AND TRIM(reference) <> ''
         GROUP BY reference
         HAVING COUNT(*) > 1
         ORDER BY reference"
    );
    return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
}

/**
 * Rename duplicate references so the earliest row keeps the canonical value.
 *
 * @return array{duplicate_groups: int, rows_updated: int, details: array<int, array<string, int|string>>}
 */
function cleanup_duplicate_transaction_references(PDO $pdo, bool $apply = false): array
{
    $dupes = find_duplicate_transaction_references($pdo);
    $result = ['duplicate_groups' => count($dupes), 'rows_updated' => 0, 'details' => []];

    foreach ($dupes as $row) {
        $ref = (string) ($row['reference'] ?? '');
        $keepId = (int) ($row['keep_id'] ?? 0);
        if ($ref === '' || $keepId <= 0) {
            continue;
        }
        $ids = array_map('intval', explode(',', (string) ($row['ids'] ?? '')));
        foreach ($ids as $id) {
            if ($id <= 0 || $id === $keepId) {
                continue;
            }
            $newRef = dedupe_transaction_reference_value($ref, $id);
            $result['details'][] = [
                'id' => $id,
                'kept_id' => $keepId,
                'old_reference' => $ref,
                'new_reference' => $newRef,
            ];
            if ($apply) {
                $upd = $pdo->prepare('UPDATE transactions SET reference = ? WHERE id = ? AND reference = ?');
                $upd->execute([$newRef, $id, $ref]);
                $result['rows_updated'] += $upd->rowCount();
            }
        }
    }

    return $result;
}

function dedupe_transaction_reference_value(string $reference, int $transactionId): string
{
    $suffix = '_dup_' . $transactionId;
    $maxLen = 255;
    $base = $reference;
    if (strlen($base) + strlen($suffix) > $maxLen) {
        $base = substr($base, 0, $maxLen - strlen($suffix));
    }
    return $base . $suffix;
}

function ensure_unique_transaction_reference_index(PDO $pdo): array
{
    $idx = $pdo->query(
        "SELECT COUNT(*) FROM INFORMATION_SCHEMA.STATISTICS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'transactions' AND INDEX_NAME = 'uniq_transactions_reference'"
    );
    if ($idx && (int) $idx->fetchColumn() > 0) {
        return ['index_exists' => true, 'cleanup' => ['duplicate_groups' => 0, 'rows_updated' => 0, 'details' => []]];
    }

    $cleanup = cleanup_duplicate_transaction_references($pdo, true);
    $pdo->exec('ALTER TABLE transactions ADD UNIQUE KEY uniq_transactions_reference (reference)');
    return ['index_exists' => false, 'cleanup' => $cleanup, 'index_created' => true];
}

/** SQL fragment: exclude principal-return refs from performance charts. */
function portfolio_chart_reference_exclude_sql(): string
{
    return " AND (reference IS NULL OR (reference NOT LIKE 'maturity_inv_%' AND reference NOT LIKE 'liquidation_return_inv_%'))";
}

function investment_duration_days_row(array $inv): int
{
    $days = (int) ($inv['investment_duration_days'] ?? $inv['duration_days'] ?? $inv['plan_duration_days'] ?? 30);
    return $days > 0 ? $days : 30;
}

function investment_end_datetime(array $inv): ?DateTime
{
    if (empty($inv['start_date'])) {
        return null;
    }
    try {
        $start = new DateTime($inv['start_date'] . ' 00:00:00', new DateTimeZone('UTC'));
        $end = clone $start;
        $end->modify('+' . investment_duration_days_row($inv) . ' days');
        return $end;
    } catch (Throwable $e) {
        return null;
    }
}

function investment_is_matured(array $inv, ?DateTime $now = null): bool
{
    $end = investment_end_datetime($inv);
    if (!$end) {
        return false;
    }
    $now = $now ?? new DateTime('now', new DateTimeZone('UTC'));
    return $now >= $end;
}

function investment_has_transaction_reference(PDO $pdo, string $ref): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM transactions WHERE reference = ? LIMIT 1');
    $stmt->execute([$ref]);
    return (bool) $stmt->fetchColumn();
}

function investment_principal_returned(PDO $pdo, int $invId): bool
{
    return investment_has_transaction_reference($pdo, 'maturity_inv_' . $invId)
        || investment_has_transaction_reference($pdo, 'liquidation_return_inv_' . $invId);
}

function investment_lifecycle_has_amount_usd(PDO $pdo): bool
{
    static $has = null;
    if ($has !== null) {
        return $has;
    }
    $has = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'amount_usd'");
        $has = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) {
    }
    return $has;
}

function investment_lifecycle_is_duplicate_key(Throwable $e): bool
{
    if ($e instanceof PDOException) {
        $code = (string) $e->getCode();
        if ($code === '23000' || $code === '1062') {
            return true;
        }
    }
    $msg = strtolower($e->getMessage());
    return str_contains($msg, 'duplicate') || str_contains($msg, '1062');
}

function investment_lifecycle_insert_transaction(
    PDO $pdo,
    int $userId,
    string $type,
    string $amountStr,
    float $amountUsd,
    string $currency,
    string $ref
): bool {
    $hasAmountUsd = investment_lifecycle_has_amount_usd($pdo);
    try {
        if ($hasAmountUsd) {
            $pdo->prepare('INSERT INTO transactions (user_id, type, amount, amount_usd, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?, ?)')
                ->execute([$userId, $type, $amountStr, round($amountUsd, 2), $currency, 'completed', $ref]);
        } else {
            $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$userId, $type, $amountStr, $currency, 'completed', $ref]);
        }
        return true;
    } catch (Throwable $e) {
        if (investment_lifecycle_is_duplicate_key($e)) {
            return false;
        }
        throw $e;
    }
}

function fetch_user_investment(PDO $pdo, int $invId, int $userId): ?array
{
    ensure_investment_lifecycle_schema($pdo);
    $stmt = $pdo->prepare(
        'SELECT ui.*, ui.duration_days AS investment_duration_days,
                p.name AS plan_name, p.duration_days AS plan_duration_days,
                p.liquidation_cost, p.yield_min, p.yield_max
         FROM user_investments ui
         INNER JOIN plans p ON p.id = ui.plan_id
         WHERE ui.id = ? AND ui.user_id = ?
         LIMIT 1'
    );
    $stmt->execute([$invId, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Move accrued (not yet wallet-credited) earnings into the spendable USD balance.
 */
function release_investment_accrued_earnings_to_wallet(PDO $pdo, int $userId, int $invId): float
{
    $releaseRef = 'earnings_release_inv_' . $invId;
    if (investment_has_transaction_reference($pdo, $releaseRef)) {
        return 0.0;
    }

    $amount = get_investment_accrued_payout_not_in_wallet($pdo, $invId);
    if ($amount <= 0) {
        return 0.0;
    }

    $currency = user_usd_wallet_currency();
    $amountStr = number_format($amount, 18, '.', '');
    credit_user_usd($pdo, $userId, $amount);
    investment_lifecycle_insert_transaction($pdo, $userId, 'deposit', $amountStr, $amount, $currency, $releaseRef);

    return $amount;
}

function lock_user_investment_row(PDO $pdo, int $invId): ?array
{
    $stmt = $pdo->prepare(
        'SELECT ui.*, ui.duration_days AS investment_duration_days,
                p.name AS plan_name, p.duration_days AS plan_duration_days,
                p.liquidation_cost, p.yield_min, p.yield_max
         FROM user_investments ui
         INNER JOIN plans p ON p.id = ui.plan_id
         WHERE ui.id = ?
         LIMIT 1
         FOR UPDATE'
    );
    $stmt->execute([$invId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Credit principal back to USD wallet when a plan matures.
 */
function settle_matured_investment(PDO $pdo, int $invId, bool $manageTransaction = true): array
{
    ensure_investment_lifecycle_schema($pdo);

    $started = false;
    if ($manageTransaction && !$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $started = true;
    }

    try {
        $inv = lock_user_investment_row($pdo, $invId);
        if (!$inv) {
            if ($started && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Investment not found'];
        }
        if (!in_array($inv['status'], ['active', 'paused'], true)) {
            if ($started && $pdo->inTransaction()) {
                $pdo->commit();
            }
            return ['success' => false, 'error' => 'Investment is not active'];
        }
        if (!investment_is_matured($inv)) {
            if ($started && $pdo->inTransaction()) {
                $pdo->commit();
            }
            return ['success' => false, 'error' => 'Investment has not matured yet'];
        }

        $ref = 'maturity_inv_' . $invId;
        if (investment_principal_returned($pdo, $invId) || investment_has_transaction_reference($pdo, $ref)) {
            release_investment_accrued_earnings_to_wallet($pdo, (int) $inv['user_id'], $invId);
            $pdo->prepare("UPDATE user_investments SET status = 'completed' WHERE id = ? AND status IN ('active', 'paused')")
                ->execute([$invId]);
            if ($started && $pdo->inTransaction()) {
                $pdo->commit();
            }
            return ['success' => true, 'already_settled' => true];
        }

        $userId = (int) $inv['user_id'];
        $principal = (float) $inv['amount'];
        if ($principal <= 0) {
            if ($started && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Invalid investment amount'];
        }

        $currency = user_usd_wallet_currency();
        $amountStr = number_format($principal, 18, '.', '');

        credit_user_usd($pdo, $userId, $principal);
        if (!investment_lifecycle_insert_transaction($pdo, $userId, 'deposit', $amountStr, $principal, $currency, $ref)) {
            if ($started && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return settle_matured_investment($pdo, $invId, true);
        }

        $upd = $pdo->prepare("UPDATE user_investments SET status = 'completed' WHERE id = ? AND status IN ('active', 'paused')");
        $upd->execute([$invId]);
        if ($upd->rowCount() < 1) {
            if ($started && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['success' => false, 'error' => 'Investment status changed during settlement'];
        }

        release_investment_accrued_earnings_to_wallet($pdo, $userId, $invId);

        if ($started && $pdo->inTransaction()) {
            $pdo->commit();
        }
        return ['success' => true, 'principal' => $principal];
    } catch (Throwable $e) {
        if ($started && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to settle matured investment'];
    }
}

/**
 * User early liquidation: charge plan liquidation fee, return principal.
 */
function liquidate_user_investment(PDO $pdo, int $userId, int $invId): array
{
    ensure_investment_lifecycle_schema($pdo);

    $pdo->beginTransaction();
    try {
        $stmt = $pdo->prepare(
            'SELECT ui.*, ui.duration_days AS investment_duration_days,
                    p.name AS plan_name, p.duration_days AS plan_duration_days,
                    p.liquidation_cost, p.yield_min, p.yield_max
             FROM user_investments ui
             INNER JOIN plans p ON p.id = ui.plan_id
             WHERE ui.id = ? AND ui.user_id = ?
             LIMIT 1
             FOR UPDATE'
        );
        $stmt->execute([$invId, $userId]);
        $inv = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$inv) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'Investment not found'];
        }
        if (!in_array($inv['status'], ['active', 'paused'], true)) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'Only active plans can be liquidated'];
        }
        if (investment_is_matured($inv)) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'This plan has matured. It will be settled automatically.'];
        }

        $returnRef = 'liquidation_return_inv_' . $invId;
        $feeRef = 'liquidation_fee_inv_' . $invId;
        if (investment_has_transaction_reference($pdo, $returnRef)) {
            release_investment_accrued_earnings_to_wallet($pdo, $userId, $invId);
            $pdo->prepare("UPDATE user_investments SET status = 'liquidated' WHERE id = ? AND status IN ('active', 'paused')")
                ->execute([$invId]);
            $pdo->commit();
            return ['success' => true, 'already_liquidated' => true, 'balance_usd' => get_user_spendable_usd_balance($pdo, $userId)];
        }
        if (investment_principal_returned($pdo, $invId)) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'Principal has already been returned for this investment'];
        }

        $fee = max(0, (float) ($inv['liquidation_cost'] ?? 0));
        if (investment_has_transaction_reference($pdo, $feeRef)) {
            $fee = 0;
        }
        $principal = (float) $inv['amount'];
        $currency = user_usd_wallet_currency();
        $feeStr = number_format($fee, 18, '.', '');
        $principalStr = number_format($principal, 18, '.', '');

        if ($fee > 0) {
            if (!debit_user_usd($pdo, $userId, $fee)) {
                $pdo->rollBack();
                return [
                    'success' => false,
                    'error' => 'Insufficient USD balance for the liquidation operation fee',
                    'balance_usd' => get_user_spendable_usd_balance($pdo, $userId),
                    'liquidation_fee' => $fee,
                    'sufficient' => false,
                ];
            }
            if (!investment_lifecycle_insert_transaction($pdo, $userId, 'withdrawal', $feeStr, $fee, $currency, $feeRef)) {
                $pdo->rollBack();
                return liquidate_user_investment($pdo, $userId, $invId);
            }
        }

        credit_user_usd($pdo, $userId, $principal);
        if (!investment_lifecycle_insert_transaction($pdo, $userId, 'deposit', $principalStr, $principal, $currency, $returnRef)) {
            $pdo->rollBack();
            return liquidate_user_investment($pdo, $userId, $invId);
        }

        $upd = $pdo->prepare("UPDATE user_investments SET status = 'liquidated' WHERE id = ? AND status IN ('active', 'paused')");
        $upd->execute([$invId]);
        if ($upd->rowCount() < 1) {
            $pdo->rollBack();
            return ['success' => false, 'error' => 'Investment status changed during liquidation'];
        }

        release_investment_accrued_earnings_to_wallet($pdo, $userId, $invId);

        $pdo->commit();
        return [
            'success' => true,
            'message' => 'Plan liquidated successfully',
            'principal_returned' => $principal,
            'liquidation_fee' => $fee,
            'balance_usd' => get_user_spendable_usd_balance($pdo, $userId),
        ];
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        return ['success' => false, 'error' => 'Failed to liquidate plan'];
    }
}

function process_due_maturities(PDO $pdo, ?int $userId = null): int
{
    ensure_investment_lifecycle_schema($pdo);
    if ($userId !== null) {
        $stmt = $pdo->prepare(
            "SELECT ui.id FROM user_investments ui
             WHERE ui.user_id = ? AND ui.status IN ('active', 'paused')"
        );
        $stmt->execute([$userId]);
    } else {
        $stmt = $pdo->query(
            "SELECT ui.id FROM user_investments ui
             WHERE ui.status IN ('active', 'paused')"
        );
    }

    $settled = 0;
    while ($invId = $stmt->fetchColumn()) {
        $invStmt = $pdo->prepare(
            'SELECT ui.*, ui.duration_days AS investment_duration_days, p.duration_days AS plan_duration_days
             FROM user_investments ui INNER JOIN plans p ON p.id = ui.plan_id WHERE ui.id = ?'
        );
        $invStmt->execute([(int) $invId]);
        $inv = $invStmt->fetch(PDO::FETCH_ASSOC);
        if ($inv && investment_is_matured($inv)) {
            $result = settle_matured_investment($pdo, (int) $invId, true);
            if (!empty($result['success'])) {
                $settled++;
            }
        }
    }
    return $settled;
}

function process_user_due_maturities(PDO $pdo, int $userId): void
{
    process_due_maturities($pdo, $userId);
}

function process_all_due_maturities(PDO $pdo): int
{
    return process_due_maturities($pdo, null);
}

function fetch_portfolio_active_investments(PDO $pdo, int $userId): array
{
    ensure_investment_lifecycle_schema($pdo);
    $stmt = $pdo->prepare(
        'SELECT ui.id, ui.amount, ui.start_date, ui.created_at, ui.status,
                ui.duration_days AS investment_duration_days,
                p.name AS plan_name, p.yield_min, p.yield_max,
                p.duration_days AS plan_duration_days, p.liquidation_cost
         FROM user_investments ui
         INNER JOIN plans p ON p.id = ui.plan_id
         WHERE ui.user_id = ? AND ui.status IN (\'active\', \'paused\')
         ORDER BY ui.created_at DESC'
    );
    $stmt->execute([$userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function fetch_portfolio_investments(PDO $pdo, int $userId, string $status): array
{
    $stmt = $pdo->prepare(
        'SELECT ui.id, ui.amount, ui.start_date, ui.created_at, ui.status,
                ui.duration_days AS investment_duration_days,
                p.name AS plan_name, p.yield_min, p.yield_max,
                p.duration_days AS plan_duration_days, p.liquidation_cost
         FROM user_investments ui
         INNER JOIN plans p ON p.id = ui.plan_id
         WHERE ui.user_id = ? AND ui.status = ?
         ORDER BY ui.created_at DESC'
    );
    $stmt->execute([$userId, $status]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
