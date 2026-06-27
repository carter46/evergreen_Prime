<?php
/**
 * Bloombit - Deduplicate transaction.reference values before unique index.
 *
 * Usage:
 *   php scripts/cleanup-duplicate-transaction-references.php           # dry run (report only)
 *   php scripts/cleanup-duplicate-transaction-references.php --apply     # rename duplicates
 *   php scripts/cleanup-duplicate-transaction-references.php --apply --index  # dedupe + add unique index
 */

if (php_sapi_name() !== 'cli') {
    http_response_code(403);
    exit("CLI only\n");
}

require_once dirname(__DIR__) . '/includes/investment-lifecycle.php';

$apply = in_array('--apply', $argv ?? [], true);
$addIndex = in_array('--index', $argv ?? [], true);

try {
    $pdo = require dirname(__DIR__) . '/includes/db.php';
} catch (Throwable $e) {
    fwrite(STDERR, 'DB unavailable: ' . $e->getMessage() . PHP_EOL);
    exit(1);
}

echo date('Y-m-d H:i:s') . ' | Duplicate transaction reference cleanup' . PHP_EOL;
echo $apply ? "Mode: APPLY\n" : "Mode: DRY RUN (pass --apply to rename duplicates)\n";

$cleanup = cleanup_duplicate_transaction_references($pdo, $apply);
echo 'Duplicate reference groups: ' . $cleanup['duplicate_groups'] . PHP_EOL;

if (empty($cleanup['details'])) {
    echo "No duplicate references found.\n";
} else {
    foreach ($cleanup['details'] as $detail) {
        echo sprintf(
            "  tx #%d: %s -> %s (kept #%d)\n",
            $detail['id'],
            $detail['old_reference'],
            $detail['new_reference'],
            $detail['kept_id']
        );
    }
    if ($apply) {
        echo 'Rows updated: ' . $cleanup['rows_updated'] . PHP_EOL;
    }
}

if ($addIndex) {
    if (!$apply) {
        echo "\n--index requires --apply. Re-run with both flags.\n";
        exit(1);
    }
    try {
        $result = ensure_unique_transaction_reference_index($pdo);
        if (!empty($result['index_exists'])) {
            echo "\nUnique index uniq_transactions_reference already exists.\n";
        } else {
            echo "\nUnique index uniq_transactions_reference created.\n";
        }
    } catch (Throwable $e) {
        fwrite(STDERR, 'Failed to create unique index: ' . $e->getMessage() . PHP_EOL);
        exit(1);
    }
} elseif ($apply && $cleanup['duplicate_groups'] > 0) {
    echo "\nTip: run with --apply --index to add uniq_transactions_reference after cleanup.\n";
}

exit(0);
