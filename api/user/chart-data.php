<?php
/**
 * Bloombit - Chart Data API
 * GET /api/user/chart-data.php?period=1D|1W|1M|1Y
 */

header('Content-Type: application/json');

require_once dirname(__DIR__, 2) . '/includes/session-bootstrap.php';
if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

$period = $_GET['period'] ?? '1M';
$days = match($period) {
    '1D' => 1,
    '1W' => 7,
    '1M' => 30,
    '1Y' => 365,
    default => 30
};

try {
    $pdo = require dirname(__DIR__, 2) . '/includes/db.php';
    $userId = $_SESSION['user_id'];
    $stmt = $pdo->prepare("SELECT DATE(created_at) as date, type, SUM(amount) as total FROM transactions WHERE user_id = ? AND type IN ('deposit', 'withdrawal') AND created_at >= DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at), type ORDER BY date ASC");
    $stmt->execute([$userId, $days]);
    $dailyData = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $date = $row['date'];
        if (!isset($dailyData[$date])) $dailyData[$date] = ['deposit' => 0, 'withdrawal' => 0];
        $dailyData[$date][$row['type']] = (float)$row['total'];
    }
    $chartData = [];
    $cumulative = 0;
    foreach ($dailyData as $date => $amounts) {
        $cumulative += $amounts['deposit'] - $amounts['withdrawal'];
        $chartData[] = ['date' => $date, 'value' => $cumulative];
    }
    echo json_encode(['success' => true, 'data' => $chartData]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to load chart data']);
}
