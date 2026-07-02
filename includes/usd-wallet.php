<?php
/**
 * Centralized USD wallet — single spendable balance for invest / display.
 * Deposits and withdrawals still use crypto rails on transactions; ledger is USD.
 */

require_once __DIR__ . '/helpers.php';

function user_usd_wallet_currency(): string
{
    return 'USD';
}

/** @return string[] */
function legacy_stable_wallet_currencies(): array
{
    return ['USDT', 'USDC', 'BUSD', 'DAI'];
}

/**
 * Move legacy stablecoin rows into the USD wallet row (one-time per user when needed).
 * Call inside an existing transaction when possible.
 */
function consolidate_legacy_wallet_to_usd(PDO $pdo, int $userId): void
{
    $legacy = array_merge([user_usd_wallet_currency()], legacy_stable_wallet_currencies());
    $placeholders = implode(',', array_fill(0, count($legacy), '?'));
    $params = array_merge([$userId], $legacy);

    $stmt = $pdo->prepare(
        "SELECT currency, amount FROM wallet_balances WHERE user_id = ? AND UPPER(currency) IN ($placeholders) FOR UPDATE"
    );
    $stmt->execute($params);

    $totalUsd = 0.0;
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as $row) {
        $amt = (float) ($row['amount'] ?? 0);
        if ($amt > 0) {
            $totalUsd += $amt;
        }
    }

    foreach (legacy_stable_wallet_currencies() as $cur) {
        $pdo->prepare('UPDATE wallet_balances SET amount = 0 WHERE user_id = ? AND UPPER(currency) = ?')
            ->execute([$userId, $cur]);
    }

    if ($totalUsd > 0) {
        $str = number_format($totalUsd, 18, '.', '');
        $pdo->prepare(
            'INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?)
             ON DUPLICATE KEY UPDATE amount = VALUES(amount)'
        )->execute([$userId, user_usd_wallet_currency(), $str]);
    }

    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        if ($chk && $chk->rowCount() > 0) {
            $pdo->prepare('UPDATE users SET last_balance_usd = ?, last_balance_usd_updated_at = NOW() WHERE id = ?')
                ->execute([number_format($totalUsd, 2, '.', ''), $userId]);
        }
    } catch (Throwable $e) {
        // ignore
    }
}

function user_has_legacy_stable_balances(PDO $pdo, int $userId): bool
{
    $legacyCheck = $pdo->prepare(
        'SELECT COALESCE(SUM(amount), 0) FROM wallet_balances
         WHERE user_id = ? AND UPPER(currency) IN (' . implode(',', array_fill(0, count(legacy_stable_wallet_currencies()), '?')) . ')
         AND amount > 0'
    );
    $legacyCheck->execute(array_merge([$userId], legacy_stable_wallet_currencies()));
    return (float) $legacyCheck->fetchColumn() > 0;
}

/** Merge legacy USDT/USDC/etc. into USD row when needed. Safe inside or outside a transaction. */
function maybe_consolidate_legacy_usd(PDO $pdo, int $userId): void
{
    if (!user_has_legacy_stable_balances($pdo, $userId)) {
        return;
    }
    $ownedTx = false;
    if (!$pdo->inTransaction()) {
        $pdo->beginTransaction();
        $ownedTx = true;
    }
    try {
        if (user_has_legacy_stable_balances($pdo, $userId)) {
            consolidate_legacy_wallet_to_usd($pdo, $userId);
        }
        if ($ownedTx) {
            $pdo->commit();
        }
    } catch (Throwable $e) {
        if ($ownedTx && $pdo->inTransaction()) {
            $pdo->rollBack();
        }
        throw $e;
    }
}

/**
 * Payouts created before this UTC time were credited to the USD wallet on each distribution.
 * After this cutoff, payouts accrue until the investment matures or is liquidated.
 * Cutoff is the deploy time of earnings-engine.php (updated on each upload).
 */
function earnings_payout_wallet_credit_cutoff(): string
{
    static $cutoff = null;
    if ($cutoff === null) {
        $stored = trim((string) get_site_setting('earnings_accrual_cutoff_utc', ''));
        if ($stored !== '') {
            $cutoff = $stored;
        } else {
            $cutoff = gmdate('Y-m-d H:i:s');
            try {
                $pdo = require __DIR__ . '/db.php';
                $pdo->prepare(
                    "INSERT INTO site_settings (`key`, value) VALUES ('earnings_accrual_cutoff_utc', ?)
                     ON DUPLICATE KEY UPDATE value = IF(value = '' OR value IS NULL, VALUES(value), value)"
                )->execute([$cutoff]);
            } catch (Throwable $e) {
                // Fall back to in-memory cutoff for this request.
            }
        }
    }
    return $cutoff;
}

function parse_earnings_investment_id_from_reference(?string $reference): ?int
{
    if ($reference === null || $reference === '') {
        return null;
    }
    if (preg_match('/^earnings_inv_(\d+)(?:_|$)/', $reference, $m)) {
        return (int) $m[1];
    }
    return null;
}

function payout_transaction_was_wallet_credited(array $tx): bool
{
    $createdAt = $tx['created_at'] ?? '';
    if ($createdAt === '') {
        return true;
    }
    return strtotime($createdAt) < strtotime(earnings_payout_wallet_credit_cutoff());
}

/** @return array<int, true> */
function get_active_investment_ids_for_user(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT id FROM user_investments WHERE user_id = ? AND status IN ('active', 'paused')");
    $stmt->execute([$userId]);
    $ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ids[(int) $row['id']] = true;
    }
    return $ids;
}

/**
 * Earnings payouts that were incorrectly credited to wallet while the plan is still active.
 */
function get_wallet_credited_earnings_on_active_investments(PDO $pdo, int $userId): float
{
    $activeIds = get_active_investment_ids_for_user($pdo, $userId);
    if ($activeIds === []) {
        return 0.0;
    }

    $stmt = $pdo->prepare(
        "SELECT reference, amount, amount_usd, created_at
         FROM transactions
         WHERE user_id = ? AND type = 'payout' AND status = 'completed'"
    );
    $stmt->execute([$userId]);
    $total = 0.0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (!payout_transaction_was_wallet_credited($row)) {
            continue;
        }
        $invId = parse_earnings_investment_id_from_reference($row['reference'] ?? '');
        if ($invId === null || !isset($activeIds[$invId])) {
            continue;
        }
        $total += (float) ($row['amount_usd'] ?? $row['amount'] ?? 0);
    }
    return max(0.0, $total);
}

/**
 * Spendable USD — excludes investment earnings that have not matured or been liquidated.
 */
function get_user_spendable_usd_balance(PDO $pdo, int $userId): float
{
    $balance = get_user_usd_balance($pdo, $userId);
    $locked = get_wallet_credited_earnings_on_active_investments($pdo, $userId);
    return max(0.0, $balance - $locked);
}

function get_investment_accrued_payout_usd(PDO $pdo, int $invId): float
{
    $exact = 'earnings_inv_' . $invId;
    $like = 'earnings_inv_' . $invId . '_%';
    $stmt = $pdo->prepare(
        "SELECT COALESCE(SUM(COALESCE(amount_usd, amount)), 0)
         FROM transactions
         WHERE type = 'payout' AND status = 'completed'
         AND (reference = ? OR reference LIKE ?)"
    );
    $stmt->execute([$exact, $like]);
    return max(0.0, (float) $stmt->fetchColumn());
}

/**
 * Accrued payouts not yet released to the USD wallet (post-cutoff accrual model).
 */
function get_investment_accrued_payout_not_in_wallet(PDO $pdo, int $invId): float
{
    $exact = 'earnings_inv_' . $invId;
    $like = 'earnings_inv_' . $invId . '_%';
    $stmt = $pdo->prepare(
        "SELECT amount, amount_usd, created_at
         FROM transactions
         WHERE type = 'payout' AND status = 'completed'
         AND (reference = ? OR reference LIKE ?)"
    );
    $stmt->execute([$exact, $like]);
    $total = 0.0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        if (payout_transaction_was_wallet_credited($row)) {
            continue;
        }
        $total += (float) ($row['amount_usd'] ?? $row['amount'] ?? 0);
    }
    return max(0.0, $total);
}

/** @return array<int, true> */
function get_settled_investment_ids_for_user(PDO $pdo, int $userId): array
{
    $stmt = $pdo->prepare("SELECT id FROM user_investments WHERE user_id = ? AND status IN ('completed', 'liquidated', 'cancelled')");
    $stmt->execute([$userId]);
    $ids = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $ids[(int) $row['id']] = true;
    }
    return $ids;
}

/**
 * Profit credited to spendable balance — matured, liquidated, or cancelled plans, plus admin adjustments.
 * Excludes earnings still accruing on active investments.
 */
function get_user_realized_profit(PDO $pdo, int $userId, ?int $days = null): float
{
    $sqlAdj = "SELECT COALESCE(SUM(COALESCE(amount_usd, amount)), 0) FROM transactions
               WHERE user_id = ? AND type = 'profit_adjustment' AND status = 'completed'";
    $paramsAdj = [$userId];
    if ($days !== null && $days > 0) {
        $sqlAdj .= ' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)';
        $paramsAdj[] = $days;
    }
    $stmt = $pdo->prepare($sqlAdj);
    $stmt->execute($paramsAdj);
    $total = (float) $stmt->fetchColumn();

    $settledIds = get_settled_investment_ids_for_user($pdo, $userId);
    if ($settledIds === []) {
        return max(0.0, $total);
    }

    $sqlPay = "SELECT reference, amount, amount_usd, created_at FROM transactions
               WHERE user_id = ? AND type = 'payout' AND status = 'completed'";
    $paramsPay = [$userId];
    if ($days !== null && $days > 0) {
        $sqlPay .= ' AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY)';
        $paramsPay[] = $days;
    }
    $stmt = $pdo->prepare($sqlPay);
    $stmt->execute($paramsPay);
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $invId = parse_earnings_investment_id_from_reference($row['reference'] ?? '');
        if ($invId !== null && isset($settledIds[$invId])) {
            $total += (float) ($row['amount_usd'] ?? $row['amount'] ?? 0);
        }
    }

    return max(0.0, $total);
}

/**
 * Returns raw USD wallet balance (consolidates legacy stables when present).
 */
function get_user_usd_balance(PDO $pdo, int $userId): float
{
    maybe_consolidate_legacy_usd($pdo, $userId);

    $stmt = $pdo->prepare('SELECT amount FROM wallet_balances WHERE user_id = ? AND UPPER(currency) = ? LIMIT 1');
    $stmt->execute([$userId, user_usd_wallet_currency()]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        return max(0.0, (float) $row['amount']);
    }

    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        if ($chk && $chk->rowCount() > 0) {
            $s = $pdo->prepare('SELECT last_balance_usd FROM users WHERE id = ?');
            $s->execute([$userId]);
            return max(0.0, (float) ($s->fetchColumn() ?? 0));
        }
    } catch (Throwable $e) {
        // ignore
    }

    return 0.0;
}

function credit_user_usd(PDO $pdo, int $userId, float $amountUsd): void
{
    if ($amountUsd <= 0) {
        return;
    }
    maybe_consolidate_legacy_usd($pdo, $userId);
    $str = number_format($amountUsd, 18, '.', '');
    $pdo->prepare(
        'INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)'
    )->execute([$userId, user_usd_wallet_currency(), $str]);
    bump_user_last_balance_usd($pdo, $userId, $amountUsd);
}

/**
 * Debit USD wallet. Call inside a transaction with FOR UPDATE when possible.
 * @return bool true if debited, false if insufficient funds
 */
function debit_user_usd(PDO $pdo, int $userId, float $amountUsd): bool
{
    if ($amountUsd <= 0) {
        return false;
    }

    maybe_consolidate_legacy_usd($pdo, $userId);

    $stmt = $pdo->prepare(
        'SELECT amount FROM wallet_balances WHERE user_id = ? AND UPPER(currency) = ? FOR UPDATE'
    );
    $stmt->execute([$userId, user_usd_wallet_currency()]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $balance = $row ? (float) $row['amount'] : 0.0;

    if ($balance < $amountUsd) {
        return false;
    }

    $str = number_format($amountUsd, 18, '.', '');
    $pdo->prepare(
        'INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE amount = amount - VALUES(amount)'
    )->execute([$userId, user_usd_wallet_currency(), $str]);

    bump_user_last_balance_usd($pdo, $userId, -1 * $amountUsd);
    return true;
}
