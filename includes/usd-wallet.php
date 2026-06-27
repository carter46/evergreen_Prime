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
 * Returns spendable USD balance (consolidates legacy stables when present).
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
