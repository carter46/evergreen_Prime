<?php
/**
 * Bloombit - Helper Functions
 */

/**
 * Get a site setting from DB. Falls back to config or default.
 */
function get_site_setting(string $key, ?string $default = null): ?string {
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        try {
            $pdo = require __DIR__ . '/db.php';
            $stmt = $pdo->query('SELECT `key`, value FROM site_settings');
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cache[$row['key']] = $row['value'];
            }
        } catch (Throwable $e) {
            // DB not ready
        }
    }
    return $cache[$key] ?? $default;
}

/**
 * Get site name - from DB, config, or default.
 */
function get_site_name(): string {
    $config = include dirname(__DIR__) . '/config.php';
    return get_site_setting('site_name') ?? $config['site']['name'] ?? 'Bloombit';
}

/**
 * Format datetime as relative time (e.g. "2 minutes ago").
 */
function time_ago(string $datetime): string {
    $ts = strtotime($datetime);
    $diff = time() - $ts;
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 604800) return floor($diff / 86400) . ' days ago';
    return date('M j, Y', $ts);
}

/**
 * Get base site URL (protocol + host) - dynamic from current request.
 * Use getenv('SITE_URL') to override when needed (e.g. behind proxy).
 */
function get_base_url(): string {
    if ($url = getenv('SITE_URL')) {
        return rtrim($url, '/');
    }
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https');
    $protocol = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'] ?? 'localhost';
    return $protocol . '://' . $host;
}
