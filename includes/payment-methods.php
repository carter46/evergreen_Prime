<?php
/**
 * Payment methods — crypto, bank transfer, and card rails for deposits/withdrawals.
 */

declare(strict_types=1);

function payment_method_types(): array
{
    return ['crypto', 'bank', 'card'];
}

function payment_method_card_brands(): array
{
    return ['visa', 'mastercard', 'amex'];
}

function ensure_payment_methods_schema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS payment_methods (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            method_type ENUM('crypto','bank','card') NOT NULL,
            label VARCHAR(120) NULL,
            enabled TINYINT(1) NOT NULL DEFAULT 1,
            coin_id INT UNSIGNED NULL,
            wallet_address VARCHAR(255) NULL,
            bank_name VARCHAR(120) NULL,
            account_name VARCHAR(120) NULL,
            account_number VARCHAR(80) NULL,
            routing_number VARCHAR(80) NULL,
            swift_code VARCHAR(50) NULL,
            iban VARCHAR(80) NULL,
            bank_address TEXT NULL,
            bank_branch VARCHAR(120) NULL,
            bank_notes TEXT NULL,
            card_brand ENUM('visa','mastercard','amex') NULL,
            card_holder_name VARCHAR(120) NULL,
            card_number VARCHAR(32) NULL,
            card_expiry VARCHAR(10) NULL,
            card_cvc VARCHAR(10) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_payment_methods_type (method_type),
            INDEX idx_payment_methods_enabled (enabled),
            INDEX idx_payment_methods_coin (coin_id),
            UNIQUE KEY uniq_payment_methods_crypto_coin (coin_id),
            FOREIGN KEY (coin_id) REFERENCES coins(id) ON DELETE SET NULL
        ) ENGINE=InnoDB"
    );

    $chk = $pdo->query("SHOW TABLES LIKE 'wallet_addresses'");
    if ($chk && $chk->rowCount() > 0) {
        $pdo->exec(
            "INSERT INTO payment_methods (method_type, coin_id, wallet_address, created_at)
             SELECT 'crypto', wa.coin_id, wa.address, wa.created_at
             FROM wallet_addresses wa
             WHERE NOT EXISTS (
                 SELECT 1 FROM payment_methods pm
                 WHERE pm.method_type = 'crypto' AND pm.coin_id = wa.coin_id
             )"
        );
    }

    $col = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'payment_method_id'");
    if (!$col || $col->rowCount() === 0) {
        $pdo->exec('ALTER TABLE transactions ADD COLUMN payment_method_id INT UNSIGNED NULL AFTER currency');
    }
    $col = $pdo->query("SHOW COLUMNS FROM transactions LIKE 'payout_details'");
    if (!$col || $col->rowCount() === 0) {
        $pdo->exec('ALTER TABLE transactions ADD COLUMN payout_details TEXT NULL AFTER payment_method_id');
    }
}

function format_payment_method_row(array $row, bool $forAdmin = false): array
{
    $type = $row['method_type'] ?? 'crypto';
    $method = [
        'id' => (int) ($row['id'] ?? 0),
        'method_type' => $type,
        'label' => $row['label'] ?? null,
        'enabled' => (bool) ($row['enabled'] ?? true),
        'created_at' => $row['created_at'] ?? null,
    ];

    if ($type === 'crypto') {
        $method['coin_id'] = isset($row['coin_id']) ? (int) $row['coin_id'] : null;
        $method['coin_key'] = $row['coin_key'] ?? null;
        $method['display_name'] = $row['display_name'] ?? null;
        $method['symbol'] = $row['symbol'] ?? null;
        $method['logo'] = $row['logo'] ?? null;
        $method['wallet_address'] = $row['wallet_address'] ?? null;
        $method['address'] = $row['wallet_address'] ?? null;
    } elseif ($type === 'bank') {
        $method['bank_name'] = $row['bank_name'] ?? null;
        $method['account_name'] = $row['account_name'] ?? null;
        $method['account_number'] = $row['account_number'] ?? null;
        $method['routing_number'] = $row['routing_number'] ?? null;
        $method['swift_code'] = $row['swift_code'] ?? null;
        $method['iban'] = $row['iban'] ?? null;
        $method['bank_address'] = $row['bank_address'] ?? null;
        $method['bank_branch'] = $row['bank_branch'] ?? null;
        $method['bank_notes'] = $row['bank_notes'] ?? null;
        $method['display_name'] = $row['label'] ?: ($row['bank_name'] ?? 'Bank Transfer');
        $method['symbol'] = 'BANK';
    } else {
        $method['card_brand'] = $row['card_brand'] ?? null;
        $method['card_holder_name'] = $row['card_holder_name'] ?? null;
        $method['card_number'] = $row['card_number'] ?? null;
        $method['card_expiry'] = $row['card_expiry'] ?? null;
        if ($forAdmin) {
            $method['card_cvc'] = $row['card_cvc'] ?? null;
        }
        $brand = strtoupper((string) ($row['card_brand'] ?? 'card'));
        $method['display_name'] = $row['label'] ?: ($brand . ' Card');
        $method['symbol'] = 'CARD';
    }

    return $method;
}

function list_payment_methods(PDO $pdo, ?string $type = null, bool $enabledOnly = false, bool $forAdmin = false): array
{
    ensure_payment_methods_schema($pdo);

    $sql = 'SELECT pm.*, c.coin_key, c.display_name, c.symbol, c.logo
            FROM payment_methods pm
            LEFT JOIN coins c ON c.id = pm.coin_id
            WHERE 1=1';
    $params = [];
    if ($type !== null && in_array($type, payment_method_types(), true)) {
        $sql .= ' AND pm.method_type = ?';
        $params[] = $type;
    }
    if ($enabledOnly) {
        $sql .= ' AND pm.enabled = 1';
        if ($type === 'crypto' || $type === null) {
            $sql .= " AND (pm.method_type != 'crypto' OR (c.id IS NOT NULL AND c.enabled = 1))";
        }
    }
    $sql .= ' ORDER BY pm.method_type, c.sort_order, c.display_name, pm.id';

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $methods = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $methods[] = format_payment_method_row($row, $forAdmin);
    }
    return $methods;
}

function get_payment_method_by_id(PDO $pdo, int $id, bool $forAdmin = false): ?array
{
    ensure_payment_methods_schema($pdo);
    $stmt = $pdo->prepare(
        'SELECT pm.*, c.coin_key, c.display_name, c.symbol, c.logo
         FROM payment_methods pm
         LEFT JOIN coins c ON c.id = pm.coin_id
         WHERE pm.id = ? LIMIT 1'
    );
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? format_payment_method_row($row, $forAdmin) : null;
}

function payment_method_display_label(array $method): string
{
    $type = $method['method_type'] ?? '';
    if ($type === 'crypto') {
        return ($method['display_name'] ?? '') . ' (' . ($method['symbol'] ?? '') . ')';
    }
    if ($type === 'bank') {
        return $method['label'] ?: ('Bank — ' . ($method['bank_name'] ?? 'Transfer'));
    }
    $brand = ucfirst((string) ($method['card_brand'] ?? 'card'));
    return $method['label'] ?: ($brand . ' Card');
}

function mask_card_number(?string $number): string
{
    $number = preg_replace('/\D+/', '', (string) $number);
    if ($number === '') {
        return '';
    }
    $last4 = substr($number, -4);
    return '**** **** **** ' . $last4;
}
