<?php
/**
 * Investment plan types and display helpers.
 */

function get_plan_types(): array
{
    return [
        'crypto' => 'Crypto',
        'stocks' => 'Stocks',
        'equities' => 'Equities',
        'shares' => 'Shares',
        'real_estate' => 'Real Estate',
        'commodities' => 'Commodities',
    ];
}

function normalize_plan_type(?string $type): string
{
    $types = get_plan_types();
    $key = strtolower(trim((string) $type));
    return isset($types[$key]) ? $key : 'crypto';
}

function plan_type_label(?string $type): string
{
    $types = get_plan_types();
    $key = normalize_plan_type($type);
    return $types[$key];
}

function plan_display_initial(string $name): string
{
    $name = trim($name);
    if ($name === '') {
        return '?';
    }
    if (preg_match('/\(([^)]+)\)/', $name, $m)) {
        $inner = trim($m[1]);
        if ($inner !== '') {
            return strtoupper(substr($inner, 0, 1));
        }
    }
    return strtoupper(substr($name, 0, 1));
}

function plan_logo_markup(?string $logoUrl, string $name, string $sizeClass = 'w-10 h-10', string $textClass = 'text-sm'): string
{
    $initial = htmlspecialchars(plan_display_initial($name), ENT_QUOTES, 'UTF-8');
    if (!empty($logoUrl)) {
        return '<img src="' . htmlspecialchars($logoUrl, ENT_QUOTES, 'UTF-8') . '" alt="" class="' . htmlspecialchars($sizeClass, ENT_QUOTES, 'UTF-8') . ' rounded-full object-cover shrink-0 bg-surface-container"/>';
    }
    return '<div class="' . htmlspecialchars($sizeClass, ENT_QUOTES, 'UTF-8') . ' rounded-full bg-surface-container flex items-center justify-center text-primary-container font-bold shrink-0 ' . htmlspecialchars($textClass, ENT_QUOTES, 'UTF-8') . '">' . $initial . '</div>';
}

function ensure_plan_schema(PDO $pdo): void
{
    static $done = false;
    if ($done) {
        return;
    }
    $done = true;

    $columns = [
        'plan_type' => "ALTER TABLE plans ADD COLUMN plan_type VARCHAR(32) NOT NULL DEFAULT 'crypto' AFTER slug",
        'logo_url' => 'ALTER TABLE plans ADD COLUMN logo_url VARCHAR(255) NULL AFTER icon',
    ];

    foreach ($columns as $column => $ddl) {
        try {
            $stmt = $pdo->prepare(
                'SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = ? AND COLUMN_NAME = ?'
            );
            $stmt->execute(['plans', $column]);
            if ((int) $stmt->fetchColumn() === 0) {
                $pdo->exec($ddl);
            }
        } catch (Throwable $e) {
            // Ignore if INFORMATION_SCHEMA is restricted; API save may still fail with a clear error.
        }
    }
}

function format_plan_expected_return(float $yieldMin, float $yieldMax): string
{
    $annualMin = $yieldMin * 365;
    $annualMax = $yieldMax * 365;
    if (abs($yieldMin - $yieldMax) < 0.01) {
        if ($annualMin >= 70) {
            return '> ' . number_format($annualMin, 0) . '% p.a.';
        }
        if ($annualMin < 30) {
            return '< ' . number_format(max($annualMin, 1), 0) . '% p.a.';
        }
        return number_format($annualMin, 0) . '% p.a.';
    }
    return number_format($annualMin, 0) . '–' . number_format($annualMax, 0) . '% p.a.';
}
