<?php
// Public-site translator (GTranslate) with 30 languages.
?>
<style>
.bb-gtranslate-wrapper {
  min-width: 112px;
  position: relative;
  z-index: 120;
  overflow: visible !important;
}
.bb-gtranslate-wrapper .gt_switcher-popup,
.bb-gtranslate-wrapper .gt_container,
.bb-gtranslate-wrapper select {
  font-size: 13px !important;
}
.bb-gtranslate-wrapper .gt_switcher-popup,
.bb-gtranslate-wrapper .gt_container,
.bb-gtranslate-wrapper .gt_options,
.bb-gtranslate-wrapper .gt_options a,
.bb-gtranslate-wrapper ul,
.bb-gtranslate-wrapper li {
  position: relative;
  z-index: 99999 !important;
}

/* Force dropdown to open downward when embedded in top bar. */
.bb-gtranslate-wrapper .gt_switcher-popup .gt_options,
.bb-gtranslate-wrapper .gt_container .gt_options,
.bb-gtranslate-wrapper .gt_options {
  top: calc(100% + 6px) !important;
  bottom: auto !important;
  transform: none !important;
}
</style>
<script>
(function() {
  if (window.__bbTranslatorLoaded) return;
  window.__bbTranslatorLoaded = true;
  window.gtranslateSettings = {
    default_language: 'en',
    detect_browser_language: true,
    languages: [
      'en','es','fr','de','it','pt','nl','ru','uk','pl',
      'tr','ar','fa','hi','bn','ur','zh-CN','zh-TW','ja','ko',
      'vi','th','id','ms','tl','sw','he','el','ro','sv'
    ],
    wrapper_selector: '.bb-gtranslate-wrapper',
    flag_size: 16,
    flag_style: '2d',
    switcher_horizontal_position: 'inline',
    switcher_open_direction: 'bottom'
  };
})();

// Some widget skins still force upward panels; normalize to downward at runtime.
document.addEventListener('DOMContentLoaded', function() {
  function forceTranslatorDownward() {
    var wrapper = document.querySelector('.bb-gtranslate-wrapper');
    if (!wrapper) return;
    var candidates = wrapper.querySelectorAll(
      '.gt_options, .gt_list, .gt_dropdown, .gt_switcher-popup ul, .gt_container ul'
    );
    candidates.forEach(function(el) {
      el.style.top = 'calc(100% + 6px)';
      el.style.bottom = 'auto';
      el.style.transform = 'none';
      if (!el.style.position) el.style.position = 'absolute';
      el.style.zIndex = '99999';
    });
  }

  forceTranslatorDownward();
  document.addEventListener('click', function() {
    setTimeout(forceTranslatorDownward, 0);
    setTimeout(forceTranslatorDownward, 100);
  });
  setInterval(forceTranslatorDownward, 1200);
});
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
