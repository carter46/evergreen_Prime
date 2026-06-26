<?php
/**
 * Dynamic Web App Manifest for PWA installation.
 */
require_once __DIR__ . '/includes/helpers.php';

header('Content-Type: application/manifest+json; charset=utf-8');
header('Cache-Control: public, max-age=3600');

$siteName = get_site_name();
$shortName = get_pwa_short_name();

$manifest = [
    'name' => $siteName,
    'short_name' => $shortName,
    'description' => $siteName . ' — professional trading and investment platform.',
    'start_url' => '/dashboard',
    'scope' => '/',
    'display' => 'standalone',
    'orientation' => 'portrait-primary',
    'theme_color' => '#ffc35c',
    'background_color' => '#0b0e11',
    'icons' => [
        [
            'src' => get_pwa_icon_url(180),
            'sizes' => '180x180',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => get_pwa_icon_url(192),
            'sizes' => '192x192',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => get_pwa_icon_url(512),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'any',
        ],
        [
            'src' => get_pwa_icon_url(512),
            'sizes' => '512x512',
            'type' => 'image/png',
            'purpose' => 'maskable',
        ],
    ],
];

echo json_encode($manifest, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
