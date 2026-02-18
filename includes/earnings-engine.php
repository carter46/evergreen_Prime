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
               ui.duration_days AS investment_duration_days,
               p.yield_min, p.yield_max, p.duration_days AS plan_duration_days, p.enabled AS plan_enabled
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

        // Auto-stop earnings when investment duration has ended.
        $durationDays = (int) ($inv['investment_duration_days'] ?? $inv['plan_duration_days'] ?? 30);
        if ($durationDays <= 0) $durationDays = 30;
        $endDate = clone $startDate;
        $endDate->modify('+' . $durationDays . ' days');
        $matured = $now >= $endDate;
        $capNow = $matured ? $endDate : $now;
        if ($refDateTime >= $endDate) {
            if ($matured) {
                // Mark completed so it no longer counts as an active plan.
                try { $pdo->prepare('UPDATE user_investments SET status = ? WHERE id = ?')->execute(['completed', $invId]); } catch (Throwable $e) {}
            }
            continue;
        }

        $toCredit = 0.0;
        $newLastAt = null;

        if ($manual) {
            $daysSince = $refDateTime->diff($capNow)->days;
            // Manual distribution should only credit what has actually accumulated since last credit.
            if ($daysSince < 1) {
                continue;
            }
            $toCredit = $dailyEarning * (float) $daysSince;
            $newLastAt = clone $capNow;
        } elseif (in_array($interval, ['5min', '12h'], true)) {
            list($toCredit, $newLastAt) = compute_continuous_earnings($interval, $refDateTime, $capNow, $dailyEarning);
        } else {
            list($toCredit, $newLastAt) = compute_batch_earnings($interval, $startTime, $refDateTime, $capNow, $dailyEarning);
        }

        if ($toCredit <= 0 || $newLastAt === null) {
            continue;
        }

        // Normalize precision for DECIMAL(36,18) storage.
        $toCredit = (float) $toCredit;
        if ($toCredit <= 0) continue;
        $toCreditStr = number_format($toCredit, 18, '.', '');

        $pdo->beginTransaction();
        try {
            $pdo->prepare('INSERT INTO wallet_balances (user_id, currency, amount) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE amount = amount + VALUES(amount)')
                ->execute([$userId, $currency, $toCreditStr]);
            $pdo->prepare('INSERT INTO transactions (user_id, type, amount, currency, status, reference) VALUES (?, ?, ?, ?, ?, ?)')
                ->execute([$userId, 'payout', $toCreditStr, $currency, 'completed', 'earnings_inv_' . $invId]);
            // Keep cached USD balance stable without live pricing (USDT is 1:1)
            if ($currency === 'USDT') {
                bump_user_last_balance_usd($pdo, $userId, (float) $toCredit);
            } else {
                refresh_user_last_balance_usd($pdo, $userId);
            }
            if ($matured && $newLastAt >= $endDate) {
                $pdo->prepare('UPDATE user_investments SET last_earnings_at = ?, status = ? WHERE id = ?')
                    ->execute([$newLastAt->format('Y-m-d H:i:s'), 'completed', $invId]);
            } else {
                $pdo->prepare('UPDATE user_investments SET last_earnings_at = ? WHERE id = ?')
                    ->execute([$newLastAt->format('Y-m-d H:i:s'), $invId]);
            }
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
    if ($diff <= 0) {
        return [0.0, null];
    }
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

    // Compute the most recent scheduled boundary (UTC) for each interval.
    $boundary = null;
    if ($interval === 'daily') {
        $boundary = clone $now;
        $boundary->setTime($hour, $minute, $sec);
        if ($now < $boundary) {
            $boundary->modify('-1 day');
        }
    } elseif ($interval === 'weekly') {
        // Weekly boundary is Monday at distribution_start_time.
        $dow = (int) $now->format('w'); // 0=Sun..6=Sat
        $daysToMonday = ($dow === 0) ? 6 : ($dow - 1);
        $boundary = clone $now;
        $boundary->modify('-' . $daysToMonday . ' days');
        $boundary->setTime($hour, $minute, $sec);
        if ($now < $boundary) {
            $boundary->modify('-7 days');
        }
    } elseif ($interval === 'monthly') {
        // Monthly boundary is the 1st day of the month at distribution_start_time.
        $boundary = clone $now;
        $boundary->setDate((int) $now->format('Y'), (int) $now->format('m'), 1);
        $boundary->setTime($hour, $minute, $sec);
        if ($now < $boundary) {
            $boundary->modify('-1 month');
        }
    } else {
        return [0.0, null];
    }

    // Credit all full days accumulated between the last credited time and the boundary.
    $boundaryTs = $boundary->getTimestamp();
    $refTs = $ref->getTimestamp();
    if ($refTs >= $boundaryTs) {
        return [0.0, null];
    }
    $days = (int) floor(($boundaryTs - $refTs) / 86400);
    if ($days < 1) {
        return [0.0, null];
    }

    $creditable = $dailyEarning * (float) $days;
    return [$creditable, $boundary];
}
