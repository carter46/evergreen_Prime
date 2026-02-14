<?php
/**
 * Bloombit - Database Connection Helper
 * Include when database is needed.
 */

$configPath = dirname(__DIR__) . '/config.php';
if (!file_exists($configPath)) {
    throw new RuntimeException('config.php not found');
}
$config = include $configPath;
$dbConfig = $config['db'] ?? [];

// Stub: Returns null until DB is configured
// In production: return new PDO(...)
return null;
