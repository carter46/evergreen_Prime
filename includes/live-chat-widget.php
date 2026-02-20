<?php
require_once __DIR__ . '/helpers.php';
$smartsuppKey = trim((string) get_site_setting('smartsupp_key', ''));
if ($smartsuppKey === '') return;
?>
<script>
(function() {
  if (window.__bbSmartsuppLoaded) return;
  window.__bbSmartsuppLoaded = true;
  window._smartsupp = window._smartsupp || {};
  window._smartsupp.key = <?php echo json_encode($smartsuppKey); ?>;
  window._smartsupp.widget = {
    colors: {
      primary: '#f9bd0b',
      secondary: '#231e0f'
    }
  };
  window.smartsupp || (function(d) {
    var s, c, o = window.smartsupp = function() { o._.push(arguments); };
    o._ = [];
    s = d.getElementsByTagName('script')[0];
    c = d.createElement('script');
    c.type = 'text/javascript';
    c.charset = 'utf-8';
    c.async = true;
    c.src = 'https://www.smartsuppchat.com/loader.js?';
    s.parentNode.insertBefore(c, s);
  })(document);
})();
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank" rel="noopener">Smartsupp</a></noscript>
