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
 * Normalize YouTube URL to embed URL. Returns null if not a valid YouTube URL.
 */
function get_youtube_embed_url(?string $url): ?string {
    if (empty($url) || !is_string($url)) return null;
    $url = trim($url);
    $id = null;
    if (preg_match('#(?:youtube\.com/watch\?v=|youtu\.be/)([a-zA-Z0-9_-]{11})#', $url, $m)) {
        $id = $m[1];
    }
    return $id ? 'https://www.youtube.com/embed/' . $id : null;
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
 * Get current logged-in user (name, email, avatar_url) - for dashboard pages.
 * Returns null if not logged in or user not found.
 */
function get_current_user_data(): ?array {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (!isset($_SESSION['user_id'])) return null;
    static $cache = null;
    if ($cache !== null) return $cache;
    try {
        $pdo = require __DIR__ . '/db.php';
        $cols = 'name, email, email_verified';
        foreach (['avatar_url', 'phone_number', 'country', 'kyc_status', 'two_factor_enabled'] as $c) {
            try {
                $chk = $pdo->query("SHOW COLUMNS FROM users LIKE '{$c}'");
                if ($chk && $chk->rowCount() > 0) $cols .= ', ' . $c;
            } catch (Throwable $e) {}
        }
        $stmt = $pdo->prepare("SELECT {$cols} FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$row) {
            $cache = null;
            return null;
        }
        $cache = [
            'name' => $row['name'] ?? '',
            'email' => $row['email'] ?? '',
            'avatar_url' => isset($row['avatar_url']) ? $row['avatar_url'] : null,
            'verified' => (bool) ($row['email_verified'] ?? false),
            'phone_number' => isset($row['phone_number']) ? $row['phone_number'] : null,
            'country' => isset($row['country']) ? $row['country'] : null,
            'kyc_status' => isset($row['kyc_status']) ? $row['kyc_status'] : 'none',
            'two_factor_enabled' => isset($row['two_factor_enabled']) ? (bool) $row['two_factor_enabled'] : false,
        ];
        return $cache;
    } catch (Throwable $e) {
        return null;
    }
}

/**
 * Fetch crypto prices in USD from CoinGecko API.
 * Returns map: 'bitcoin' => price, 'ethereum' => price, 'tether' => price.
 * Caches per-request to avoid repeated API calls.
 */
function get_coingecko_prices_usd(): array {
    static $cache = null;
    if ($cache !== null) return $cache;
    $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bloombit_prices_usd.json';
    $cacheMaxAgeSeconds = 600; // 10 minutes
    $ids = 'bitcoin,ethereum,tether,usd-coin,solana,binancecoin,ripple,cardano,dogecoin,tron';
    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=' . $ids . '&vs_currencies=usd';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'User-Agent: bloombit-server'],
    ]);
    $json = curl_exec($ch);
    curl_close($ch);
    if (!$json) {
        // Fallback to a recent local cache, otherwise return stable defaults.
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) <= $cacheMaxAgeSeconds) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                $cache = $cached;
                return $cache;
            }
        }
        $cache = ['tether' => 1.0, 'usd-coin' => 1.0];
        return $cache;
    }
    $data = json_decode($json, true);
    if (!is_array($data)) {
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) <= $cacheMaxAgeSeconds) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                $cache = $cached;
                return $cache;
            }
        }
        $cache = ['tether' => 1.0, 'usd-coin' => 1.0];
        return $cache;
    }
    // CoinGecko may return an error JSON (e.g. rate limit) that is still an array.
    // Treat missing core keys as a fetch failure and fall back to cached values.
    $looksValid = isset($data['bitcoin']['usd']) || isset($data['ethereum']['usd']) || isset($data['tether']['usd']);
    if (!$looksValid) {
        if (is_file($cacheFile) && (time() - filemtime($cacheFile)) <= $cacheMaxAgeSeconds) {
            $cached = json_decode((string) @file_get_contents($cacheFile), true);
            if (is_array($cached)) {
                $cache = $cached;
                return $cache;
            }
        }
        $cache = ['tether' => 1.0, 'usd-coin' => 1.0];
        return $cache;
    }
    $cache = [
        'bitcoin' => (float) ($data['bitcoin']['usd'] ?? 0),
        'ethereum' => (float) ($data['ethereum']['usd'] ?? 0),
        'tether' => (float) ($data['tether']['usd'] ?? 1),
        'usd-coin' => (float) ($data['usd-coin']['usd'] ?? 1),
        'solana' => (float) ($data['solana']['usd'] ?? 0),
        'binancecoin' => (float) ($data['binancecoin']['usd'] ?? 0),
        'ripple' => (float) ($data['ripple']['usd'] ?? 0),
        'cardano' => (float) ($data['cardano']['usd'] ?? 0),
        'dogecoin' => (float) ($data['dogecoin']['usd'] ?? 0),
        'tron' => (float) ($data['tron']['usd'] ?? 0),
    ];
    // Store a short-lived local cache to reduce UI inconsistencies on CoinGecko hiccups.
    @file_put_contents($cacheFile, json_encode($cache));
    return $cache;
}

/**
 * Get a USD price snapshot WITHOUT making any external requests.
 * Priority:
 * 1) site_settings.prices_usd_json (if present and valid JSON object)
 * 2) local temp file cache (bloombit_prices_usd.json)
 * 3) stable defaults only
 */
function get_prices_usd_snapshot_no_fetch(): array {
    $json = (string) (get_site_setting('prices_usd_json', '') ?? '');
    if ($json !== '') {
        $data = json_decode($json, true);
        if (is_array($data)) return $data;
    }
    $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bloombit_prices_usd.json';
    if (is_file($cacheFile)) {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (is_array($cached)) return $cached;
    }
    return ['tether' => 1.0, 'usd-coin' => 1.0];
}

/**
 * Get USD price for a single coin (for deposit/withdraw USD-to-coin quoting).
 * Resolves coin_key from DB (coins table) or fallback map. Stablecoins return 1.0.
 * Uses short file cache (60s) to reduce CoinGecko rate limits.
 * Returns null if price unavailable.
 */
function get_coin_usd_price(PDO $pdo, string $symbol): ?float {
    $symbol = strtoupper(trim($symbol));
    if (empty($symbol)) return null;
    $stable = ['USD', 'USDT', 'USDC', 'BUSD', 'DAI'];
    if (in_array($symbol, $stable, true)) return 1.0;

    $coinKey = null;
    try {
        $stmt = $pdo->prepare('SELECT coin_key FROM coins WHERE UPPER(symbol) = ? AND enabled = 1 LIMIT 1');
        $stmt->execute([$symbol]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && !empty($row['coin_key'])) $coinKey = $row['coin_key'];
    } catch (Throwable $e) {}
    if (!$coinKey) $coinKey = currency_to_coingecko($symbol);
    if (!$coinKey) return null;

    $cacheFile = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bloombit_price_' . preg_replace('/[^a-z0-9_-]/', '', strtolower($coinKey)) . '.json';
    $cacheMaxAge = 60;
    if (is_file($cacheFile) && (time() - filemtime($cacheFile)) <= $cacheMaxAge) {
        $cached = json_decode((string) @file_get_contents($cacheFile), true);
        if (isset($cached['usd']) && (float)$cached['usd'] > 0) return (float) $cached['usd'];
    }

    $url = 'https://api.coingecko.com/api/v3/simple/price?ids=' . rawurlencode($coinKey) . '&vs_currencies=usd';
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 8,
        CURLOPT_HTTPHEADER => ['Accept: application/json', 'User-Agent: bloombit-server'],
    ]);
    $json = curl_exec($ch);
    curl_close($ch);
    if (!$json) return null;
    $data = json_decode($json, true);
    if (!is_array($data) || empty($data[$coinKey]['usd'])) return null;
    $price = (float) $data[$coinKey]['usd'];
    if ($price <= 0) return null;
    @file_put_contents($cacheFile, json_encode(['usd' => $price, 'ts' => time()]));
    return $price;
}

/**
 * Quote coin amount from USD amount (for deposit/withdraw).
 * Returns ['coin_amount' => string (18 decimals), 'price_usd' => float] or empty array on failure.
 */
function quote_coin_amount_from_usd(PDO $pdo, string $symbol, float $usd): array {
    if ($usd <= 0) return [];
    $price = get_coin_usd_price($pdo, $symbol);
    if ($price === null || $price <= 0) return [];
    $coinAmount = $usd / $price;
    return [
        'coin_amount' => number_format($coinAmount, 18, '.', ''),
        'price_usd' => $price,
    ];
}

/**
 * Map wallet currency code to CoinGecko price key.
 */
function currency_to_coingecko(string $currency): ?string {
    $map = [
        'BTC' => 'bitcoin', 'ETH' => 'ethereum', 'USDT' => 'tether', 'USDC' => 'usd-coin',
        'SOL' => 'solana', 'BNB' => 'binancecoin', 'XRP' => 'ripple', 'ADA' => 'cardano',
        'DOGE' => 'dogecoin', 'TRX' => 'tron',
    ];
    return $map[strtoupper($currency)] ?? null;
}

/**
 * Convert wallet balances to total USD using CoinGecko prices.
 * $balances: array of ['currency' => 'BTC', 'amount' => 1.5], ...
 */
function wallet_balances_to_usd(array $balances, array $prices = null): float {
    $prices = $prices ?? get_coingecko_prices_usd();
    $total = 0.0;
    foreach ($balances as $b) {
        $cur = strtoupper($b['currency'] ?? '');
        $amt = (float) ($b['amount'] ?? 0);
        if ($amt <= 0) continue;
        if (in_array($cur, ['USD', 'USDT', 'USDC', 'BUSD', 'DAI'], true)) {
            // Treat stable settlement currencies as $1.00 USD (even if CoinGecko is unavailable)
            $total += $amt;
        } elseif ($key = currency_to_coingecko($cur)) {
            $total += $amt * ($prices[$key] ?? 0);
        }
    }
    return $total;
}

/**
 * Refresh user's cached "last known" USD balance stored on users table.
 * This prevents admin pages from depending on live CoinGecko at render time.
 *
 * Behavior:
 * - Always counts stable currencies 1:1.
 * - Non-stable currencies only included if a valid USD price is available.
 * - If user has non-stables but no valid prices are available, we DO NOT overwrite the cached value.
 */
function refresh_user_last_balance_usd(PDO $pdo, int $userId): void {
    // Ensure columns exist (safe on older DBs)
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        if (!$chk || $chk->rowCount() === 0) return;
    } catch (Throwable $e) {
        return;
    }

    $stmt = $pdo->prepare('SELECT currency, amount FROM wallet_balances WHERE user_id = ?');
    $stmt->execute([$userId]);
    $balances = [];
    while ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $balances[] = ['currency' => $r['currency'], 'amount' => (float) $r['amount']];
    }

    if (empty($balances)) {
        $pdo->prepare('UPDATE users SET last_balance_usd = 0, last_balance_usd_updated_at = NOW() WHERE id = ?')->execute([$userId]);
        return;
    }

    $prices = get_prices_usd_snapshot_no_fetch();
    $stable = ['USD', 'USDT', 'USDC', 'BUSD', 'DAI'];
    $total = 0.0;
    $hasNonStable = false;
    $pricedNonStable = false;
    $hasStable = false;

    foreach ($balances as $b) {
        $cur = strtoupper($b['currency'] ?? '');
        $amt = (float) ($b['amount'] ?? 0);
        if ($amt <= 0) continue;

        if (in_array($cur, $stable, true)) {
            $hasStable = true;
            $total += $amt;
        } else {
            $hasNonStable = true;
            $cg = currency_to_coingecko($cur);
            if ($cg && isset($prices[$cg]) && (float)$prices[$cg] > 0) {
                $total += $amt * (float) $prices[$cg];
                $pricedNonStable = true;
            }
        }
    }

    // If user has ONLY non-stable assets AND we can't price any of them from a snapshot,
    // keep last known cached value (do not overwrite to 0).
    if (!$hasStable && $hasNonStable && !$pricedNonStable) {
        return;
    }

    $pdo->prepare('UPDATE users SET last_balance_usd = ?, last_balance_usd_updated_at = NOW() WHERE id = ?')
        ->execute([number_format($total, 2, '.', ''), $userId]);
}

/**
 * Efficiently bump cached USD balance by a known USD delta (e.g. USDT payout).
 */
function bump_user_last_balance_usd(PDO $pdo, int $userId, float $deltaUsd): void {
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM users LIKE 'last_balance_usd'");
        if (!$chk || $chk->rowCount() === 0) return;
    } catch (Throwable $e) {
        return;
    }
    $pdo->prepare('UPDATE users SET last_balance_usd = GREATEST(0, COALESCE(last_balance_usd, 0) + ?), last_balance_usd_updated_at = NOW() WHERE id = ?')
        ->execute([number_format($deltaUsd, 2, '.', ''), $userId]);
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
