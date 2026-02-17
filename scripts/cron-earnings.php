<?php
/**
 * Bloombit - Earnings Distribution Cron
 * Run via cron every 5 minutes: php /path/to/scripts/cron-earnings.php
 * CLI only for security.
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit('CLI only');
}

require_once dirname(__DIR__) . '/includes/earnings-engine.php';

try {
    $pdo = require dirname(__DIR__) . '/includes/db.php';
} catch (Throwable $e) {
    echo "DB unavailable: " . $e->getMessage() . "\n";
    exit(1);
}

$result = run_earnings_distribution($pdo, false);

echo date('Y-m-d H:i:s') . " | Credits: {$result['credits']} | Total: \$" . number_format($result['total_amount'], 2) . " USDT\n";
if (!empty($result['errors'])) {
    foreach ($result['errors'] as $err) {
        echo "  Error: $err\n";
    }
}
