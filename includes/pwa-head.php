<?php
/**
 * PWA head tags and service worker registration script.
 */
if (!function_exists('output_pwa_head_tags')) {
    require_once __DIR__ . '/helpers.php';
}
output_pwa_head_tags();
$pwaRegVer = (int) @filemtime(dirname(__DIR__) . '/js/pwa-register.js');
?>
<script src="/js/pwa-register.js?v=<?php echo $pwaRegVer; ?>" defer></script>
