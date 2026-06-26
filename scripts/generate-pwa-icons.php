<?php
/**
 * One-time generator for bundled PWA icons (180, 192, 512).
 * Run: php scripts/generate-pwa-icons.php
 */
if (!function_exists('imagecreatetruecolor')) {
    fwrite(STDERR, "GD extension required.\n");
    exit(1);
}

$dir = dirname(__DIR__) . '/pwa/icons';
if (!is_dir($dir) && !mkdir($dir, 0777, true)) {
    fwrite(STDERR, "Cannot create $dir\n");
    exit(1);
}

foreach ([180, 192, 512] as $size) {
    $im = imagecreatetruecolor($size, $size);
    imagealphablending($im, true);
    imagesavealpha($im, true);

    $bg = imagecolorallocate($im, 11, 14, 17);
    $gold = imagecolorallocate($im, 255, 195, 92);
    $inner = imagecolorallocate($im, 20, 24, 28);

    imagefilledrectangle($im, 0, 0, $size, $size, $bg);

    $pad = (int) round($size * 0.12);
    imagefilledellipse($im, (int) ($size / 2), (int) ($size / 2), $size - $pad, $size - $pad, $gold);
    imagefilledellipse($im, (int) ($size / 2), (int) ($size / 2), (int) round($size * 0.55), (int) round($size * 0.55), $inner);

    $path = $dir . '/icon-' . $size . '.png';
    if (!imagepng($im, $path)) {
        fwrite(STDERR, "Failed to write $path\n");
        exit(1);
    }
    imagedestroy($im);
    echo "Wrote $path\n";
}

echo "Done.\n";
