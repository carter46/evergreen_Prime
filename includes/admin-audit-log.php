<?php
/**
 * Admin audit log — records who changed what across admin actions.
 */
declare(strict_types=1);

function ensure_admin_audit_log_schema(PDO $pdo): void
{
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS admin_audit_log (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            admin_id INT UNSIGNED NOT NULL,
            admin_email VARCHAR(255) NULL,
            admin_name VARCHAR(120) NULL,
            action VARCHAR(64) NOT NULL,
            entity_type VARCHAR(64) NOT NULL,
            entity_id INT UNSIGNED NULL,
            summary VARCHAR(500) NOT NULL,
            before_json LONGTEXT NULL,
            after_json LONGTEXT NULL,
            meta_json LONGTEXT NULL,
            ip_address VARCHAR(45) NULL,
            user_agent VARCHAR(500) NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_admin_audit_admin (admin_id),
            INDEX idx_admin_audit_entity (entity_type, entity_id),
            INDEX idx_admin_audit_action (action),
            INDEX idx_admin_audit_created (created_at)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci"
    );
}

function admin_audit_redact(mixed $data): mixed
{
    if (!is_array($data)) {
        return $data;
    }
    $sensitive = ['password', 'password_hash', 'card_cvc', 'mail_smtp_password', 'mail_imap_password', 'new_password'];
    $out = [];
    foreach ($data as $key => $value) {
        $keyLower = strtolower((string) $key);
        if (in_array($keyLower, $sensitive, true) || str_contains($keyLower, 'password')) {
            $out[$key] = ($value !== null && $value !== '') ? '[REDACTED]' : $value;
        } elseif (is_array($value)) {
            $out[$key] = admin_audit_redact($value);
        } else {
            $out[$key] = $value;
        }
    }
    return $out;
}

function admin_audit_encode(?array $data): ?string
{
    if ($data === null) {
        return null;
    }
    $flags = JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES;
    if (defined('JSON_INVALID_UTF8_SUBSTITUTE')) {
        $flags |= JSON_INVALID_UTF8_SUBSTITUTE;
    }
    $json = json_encode(admin_audit_redact($data), $flags);
    return $json === false ? null : $json;
}

function admin_audit_request_context(): array
{
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? $_SERVER['REMOTE_ADDR'] ?? null;
    if (is_string($ip) && str_contains($ip, ',')) {
        $ip = trim(explode(',', $ip)[0]);
    }
    $ua = $_SERVER['HTTP_USER_AGENT'] ?? null;
    if (is_string($ua) && strlen($ua) > 500) {
        $ua = substr($ua, 0, 500);
    }
    return [
        'ip_address' => is_string($ip) ? $ip : null,
        'user_agent' => is_string($ua) ? $ua : null,
    ];
}

function admin_audit_admin_info(PDO $pdo): array
{
    $adminId = (int) ($_SESSION['user_id'] ?? 0);
    $email = (string) ($_SESSION['email'] ?? '');
    $name = '';
    if ($adminId > 0) {
        try {
            $stmt = $pdo->prepare('SELECT name, email FROM users WHERE id = ? LIMIT 1');
            $stmt->execute([$adminId]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $name = (string) ($row['name'] ?? '');
                if ($email === '') {
                    $email = (string) ($row['email'] ?? '');
                }
            }
        } catch (Throwable $e) {
        }
    }
    return ['admin_id' => $adminId, 'admin_email' => $email, 'admin_name' => $name];
}

function admin_audit_log(
    PDO $pdo,
    string $action,
    string $entityType,
    ?int $entityId,
    string $summary,
    ?array $before = null,
    ?array $after = null,
    ?array $meta = null
): void {
    try {
        ensure_admin_audit_log_schema($pdo);
        $admin = admin_audit_admin_info($pdo);
        $ctx = admin_audit_request_context();
        $summary = trim($summary);
        if ($summary === '') {
            $summary = ucfirst($action) . ' ' . $entityType;
        }
        if (strlen($summary) > 500) {
            $summary = substr($summary, 0, 497) . '...';
        }
        $stmt = $pdo->prepare(
            'INSERT INTO admin_audit_log
            (admin_id, admin_email, admin_name, action, entity_type, entity_id, summary, before_json, after_json, meta_json, ip_address, user_agent)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $admin['admin_id'],
            $admin['admin_email'] !== '' ? $admin['admin_email'] : null,
            $admin['admin_name'] !== '' ? $admin['admin_name'] : null,
            substr($action, 0, 64),
            substr($entityType, 0, 64),
            $entityId,
            $summary,
            admin_audit_encode($before),
            admin_audit_encode($after),
            admin_audit_encode($meta),
            $ctx['ip_address'],
            $ctx['user_agent'],
        ]);
    } catch (Throwable $e) {
        // Never break admin flows if audit logging fails.
    }
}

function list_admin_audit_logs(
    PDO $pdo,
    int $page = 1,
    int $perPage = 30,
    ?string $entityType = null,
    ?string $action = null,
    ?string $search = null
): array {
    ensure_admin_audit_log_schema($pdo);
    $perPage = max(10, min(100, $perPage));
    $page = max(1, $page);
    $offset = ($page - 1) * $perPage;
    $where = ['1=1'];
    $params = [];

    if ($entityType !== null && $entityType !== '' && $entityType !== 'all') {
        $where[] = 'entity_type = ?';
        $params[] = $entityType;
    }
    if ($action !== null && $action !== '' && $action !== 'all') {
        $where[] = 'action = ?';
        $params[] = $action;
    }
    if ($search !== null && trim($search) !== '') {
        $where[] = '(summary LIKE ? OR admin_email LIKE ? OR admin_name LIKE ?)';
        $term = '%' . trim($search) . '%';
        $params[] = $term;
        $params[] = $term;
        $params[] = $term;
    }

    $whereClause = implode(' AND ', $where);
    $countStmt = $pdo->prepare("SELECT COUNT(*) FROM admin_audit_log WHERE {$whereClause}");
    $countStmt->execute($params);
    $total = (int) $countStmt->fetchColumn();

    $sql = "SELECT id, admin_id, admin_email, admin_name, action, entity_type, entity_id, summary,
                   before_json, after_json, meta_json, ip_address, user_agent, created_at
            FROM admin_audit_log
            WHERE {$whereClause}
            ORDER BY created_at DESC
            LIMIT " . (int) $perPage . ' OFFSET ' . (int) $offset;
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    $rows = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $rows[] = [
            'id' => (int) $row['id'],
            'admin_id' => (int) $row['admin_id'],
            'admin_email' => $row['admin_email'],
            'admin_name' => $row['admin_name'],
            'action' => $row['action'],
            'entity_type' => $row['entity_type'],
            'entity_id' => $row['entity_id'] !== null ? (int) $row['entity_id'] : null,
            'summary' => $row['summary'],
            'before' => $row['before_json'] ? json_decode($row['before_json'], true) : null,
            'after' => $row['after_json'] ? json_decode($row['after_json'], true) : null,
            'meta' => $row['meta_json'] ? json_decode($row['meta_json'], true) : null,
            'ip_address' => $row['ip_address'],
            'user_agent' => $row['user_agent'],
            'created_at' => $row['created_at'],
        ];
    }

    return [
        'data' => $rows,
        'pagination' => [
            'page' => $page,
            'per_page' => $perPage,
            'total' => $total,
            'total_pages' => $total > 0 ? (int) ceil($total / $perPage) : 1,
        ],
    ];
}

function admin_audit_entity_labels(): array
{
    return [
        'payment_method' => 'Payment method',
        'plan' => 'Plan',
        'user' => 'User',
        'transaction' => 'Transaction',
        'settings' => 'Site settings',
        'ai_config' => 'AI bot config',
        'kyc' => 'KYC',
        'broadcast' => 'Broadcast',
        'session' => 'Session',
        'investment' => 'Investment',
    ];
}

function admin_audit_action_labels(): array
{
    return [
        'create' => 'Create',
        'update' => 'Update',
        'delete' => 'Delete',
        'approve' => 'Approve',
        'reject' => 'Reject',
        'block' => 'Block',
        'unblock' => 'Unblock',
        'credit' => 'Credit',
        'debit' => 'Debit',
        'impersonate' => 'Impersonate',
        'stop_impersonate' => 'Stop impersonate',
        'send' => 'Send',
        'toggle' => 'Toggle',
        'cancel' => 'Cancel',
        'pause' => 'Pause',
        'resume' => 'Resume',
    ];
}
