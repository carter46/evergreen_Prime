<?php
/**
 * Bloombit - Earnings Distribution Engine
 * All plans use daily ROI. Credits are made in USDT.
 * Supports continuous (5min, 12h) and batch (daily, weekly, monthly) intervals.
 */

require_once __DIR__ . '/helpers.php';

/**
 * Run earnings distribution.
 * @param PDO $pdo
 * @param bool $manual If true, ignore schedule and credit all eligible immediately.
 * @return array ['credits' => int, 'total_amount' => float, 'errors' => string[]]
 */
function run_earnings_distribution(PDO $pdo, bool $manual = false): array {
    $result = ['credits' => 0, 'total_amount' => 0.0, 'errors' => []];

    $earningsPaused = get_site_setting('earnings_paused', '0');
    if ($earningsPaused === '1' && !$manual) {
        return $result;
    }

    $interval = get_site_setting('distribution_interval', 'daily');
    $startTime = get_site_setting('distribution_start_time', '09:00:00');

    $hasLastEarnings = false;
    try {
        $chk = $pdo->query("SHOW COLUMNS FROM user_investments LIKE 'last_earnings_at'");
        $hasLastEarnings = $chk && $chk->rowCount() > 0;
    } catch (Throwable $e) {
        $result['errors'][] = 'Schema check failed';
        return $result;
    }
    if (!$hasLastEarnings) {
        $result['errors'][] = 'last_earnings_at column not found';
        return $result;
    }

    $stmt = $pdo->query("
        SELECT ui.id, ui.user_id, ui.plan_id, ui.amount, ui.start_date, ui.last_earnings_at, ui.created_at,
               p.yield_min, p.yield_max, p.enabled AS plan_enabled
        FROM user_investments ui
        INNER JOIN plans p ON p.id = ui.plan_id AND p.enabled = 1
        WHERE ui.status = 'active' AND ui.amount > 0
    ");
    $investments = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $now = new DateTime('now', new DateTimeZone('UTC'));
    $currency = 'USDT';

    foreach ($investments as $inv) {
        $invId = (int) $inv['id'];
        $userId = (int) $inv['user_id'];
        $amount = (float) $inv['amount'];
        $yieldMin = (float) ($inv['yield_min'] ?? 0);
        $yieldMax = (float) ($inv['yield_max'] ?? 0);
        $dailyRoiPct = ($yieldMin + $yieldMax) / 2;
        if ($dailyRoiPct <= 0) $dailyRoiPct = $yieldMin;
        $dailyEarning = $amount * ($dailyRoiPct / 100);

        $lastAt = $inv['last_earnings_at'] ? new DateTime($inv['last_earnings_at'], new DateTimeZone('UTC')) : null;
        $startDate = new DateTime($inv['start_date'] . ' 00:00:00', new DateTimeZone('UTC'));
        $refDateTime = $lastAt ?? $startDate;

        $toCredit = 0.0;
        $newLastAt = null;

        if ($manual) {
            $daysSince = $refDateTime->diff($now)->days;
            if ($daysSince < 1) {
                $daysSince = 1;
            }
            $toCredit = $dailyEarning * $daysSince;
            $newLastAt = clone $now;
        } elseif (in_array($interval, ['5min', '12h'], true)) {
            list($toCredit, $newLastAt) = compute_continuous_earnings($interval, $refDateTime, $now, $dailyEarning);
        } else {
            list($toCredit, $newLastAt) = compute_batch_earnings($interval, $startTime, $refDateTime, $now, $dailyEarning);
        }

        if ($toCredit <= 0 || $newLastAt === null) {
            continue;
        }

        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$userId, $currency, $toCredit]);
            $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$userId, 'payout', $toCredit, $currency, 'completed', 'earnings_inv_' . $invId]);
            $pdo->prepare('UPDATE user_investments SET last_earnings_at = ? WHERE id = ?')
                ->execute([$newLastAt->format('Y-m-d H:i:s'), $invId]);
            $pdo->commit();
            $result['credits']++;
            $result['total_amount'] += $toCredit;
        } catch (Throwable $e) {
            $pdo->rollBack();
            $result['errors'][] = 'Inv ' . $invId . ': ' . $e->getMessage();
        }
    }

    return $result;
}

/**
 * Continuous: 5min or 12h intervals from reference time.
 * @return array [amount, new LastAt]
 */
function compute_continuous_earnings(string $interval, DateTime $ref, DateTime $now, float $dailyEarning): array {
    $intervalMinutes = $interval === '5min' ? 5 : (12 * 60);
    $intervalsPerDay = $interval === '5min' ? 288 : 2;
    $perInterval = $dailyEarning / $intervalsPerDay;

    $diff = $now->getTimestamp() - $ref->getTimestamp();
    $diffMinutes = (int) floor($diff / 60);
    $elapsedIntervals = (int) floor($diffMinutes / $intervalMinutes);
    if ($elapsedIntervals < 1) {
        return [0.0, null];
    }

    $creditable = $perInterval * $elapsedIntervals;
    $newLastSeconds = $ref->getTimestamp() + ($elapsedIntervals * $intervalMinutes * 60);
    $newLastAt = new DateTime('@' . $newLastSeconds, new DateTimeZone('UTC'));
    return [$creditable, $newLastAt];
}

/**
 * Batch: daily, weekly, monthly. Use distribution_start_time.
 * @return array [amount, new LastAt]
 */
function compute_batch_earnings(string $interval, string $startTime, DateTime $ref, DateTime $now, float $dailyEarning): array {
    $parts = array_map('intval', explode(':', trim($startTime)));
    $hour = $parts[0] ?? 0;
    $minute = $parts[1] ?? 0;
    $sec = $parts[2] ?? 0;

    $todayStart = clone $now;
    $todayStart->setTime($hour, $minute, $sec);

    $daysAccumulated = 0;
    $boundary = null;

    if ($interval === 'daily') {
        if ($now < $todayStart) {
            return [0.0, null];
        }
        $lastDate = $ref->format('Y-m-d');
        $todayDate = $now->format('Y-m-d');
        if ($lastDate >= $todayDate) {
            return [0.0, null];
        }
        $daysAccumulated = 1;
        $boundary = clone $todayStart;
    } elseif ($interval === 'weekly') {
        $dow = (int) $now->format('w');
        $daysToMonday = ($dow === 0) ? 6 : ($dow - 1);
        $weeklyBoundary = clone $now;
        $weeklyBoundary->modify('-' . $daysToMonday . ' days');
        $weeklyBoundary->setTime($hour, $minute, $sec);
        if ($now < $weeklyBoundary) {
            $weeklyBoundary->modify('-7 days');
        }
        if ($ref >= $weeklyBoundary) {
            return [0.0, null];
        }
        $daysAccumulated = 7;
        $boundary = clone $weeklyBoundary;
    } elseif ($interval === 'monthly') {
        $firstOfMonth = clone $now;
        $firstOfMonth->setDate((int) $now->format('Y'), (int) $now->format('m'), 1);
        $firstOfMonth->setTime($hour, $minute, $sec);
        if ($now < $firstOfMonth) {
            $firstOfMonth->modify('-1 month');
        }
        if ($ref >= $firstOfMonth) {
            return [0.0, null];
        }
        $daysAccumulated = (int) $firstOfMonth->format('t');
        $boundary = clone $firstOfMonth;
    } else {
        return [0.0, null];
    }

    $creditable = $dailyEarning * $daysAccumulated;
    return [$creditable, $boundary];
}
