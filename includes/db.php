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

$host = $dbConfig['host'] ?? 'localhost';
$dbName = $dbConfig['name'] ?? 'bloombit';
$user = $dbConfig['user'] ?? '';
$pass = $dbConfig['pass'] ?? '';

if (empty($user) || empty($pass)) {
    throw new RuntimeException('Database credentials not configured. Set DB_USER and DB_PASS in config or environment.');
}

$dsn = "mysql:host={$host};dbname={$dbName};charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

return new PDO($dsn, $user, $pass, $options);
