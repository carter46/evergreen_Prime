<?php
if (defined('APP_SCRIPT_LOADED')) {
    return;
}
define('APP_SCRIPT_LOADED', true);
$appJsVersion = (int) @filemtime(dirname(__DIR__) . '/js/app.js');
?>
<style>#bb-global-loader,#bb-global-loader-style{display:none!important;visibility:hidden!important;pointer-events:none!important}</style>
<script>
(function () {
  function stripLoader() {
    var el = document.getElementById('bb-global-loader');
    var style = document.getElementById('bb-global-loader-style');
    if (el) el.remove();
    if (style) style.remove();
  }
  stripLoader();
  document.addEventListener('DOMContentLoaded', stripLoader);
  if (typeof MutationObserver !== 'undefined' && document.documentElement) {
    new MutationObserver(stripLoader).observe(document.documentElement, { childList: true, subtree: true });
  }
})();
</script>
<script src="/js/app.js?v=<?php echo $appJsVersion; ?>"></script>
