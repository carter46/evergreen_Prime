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
