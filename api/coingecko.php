<?php
// CoinGecko API proxy to avoid CORS on shared hosting
// Usage: /api/coingecko.php?path=/simple/price&ids=bitcoin&vs_currencies=usd

// Security: Only allow from same origin - NO wildcards for crypto security
$origin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
$host = $_SERVER['HTTP_HOST'] ?? '';
$isHttps = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
$protocol = $isHttps ? 'https' : 'http';
$allowedOrigin = ($origin === $protocol . '://' . $host) ? $origin : ($protocol . '://' . $host);

header('Access-Control-Allow-Origin: ' . $allowedOrigin);
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: false');
header('Access-Control-Max-Age: 86400');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$base = 'https://api.coingecko.com/api/v3';
$path = isset($_GET['path']) ? $_GET['path'] : '/simple/price';

// Whitelist allowed paths
$allowed = ['/simple/price', '/coins/markets', '/coins/bitcoin/market_chart', '/coins/ethereum/market_chart'];
if (!in_array($path, $allowed) && !preg_match('#^/coins/[^/]+/market_chart$#', $path)) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Path not allowed']);
    exit;
}

$query = $_GET;
unset($query['path']);
$url = $base . $path;
if (!empty($query)) {
    $url .= '?' . http_build_query($query);
}

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Accept: application/json',
    'User-Agent: bloombit-proxy'
]);

$response = curl_exec($ch);
$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);

header('Content-Type: application/json');

if ($response && $status >= 200 && $status < 300) {
    header('Cache-Control: public, max-age=30');
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 30) . ' GMT');
}

http_response_code($status > 0 ? $status : 502);
if ($response && $status >= 200 && $status < 500) {
    echo $response;
} else {
    if ($status === 429) {
        header('Cache-Control: public, max-age=10');
        header('Retry-After: 60');
    }
    echo json_encode(['error' => 'Upstream error', 'status' => $status, 'detail' => $err]);
}
