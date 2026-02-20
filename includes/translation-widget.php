<?php
// Public-site translator (GTranslate) with 30 languages.
?>
<style>
.bb-gtranslate-hidden {
  position: absolute !important;
  left: -99999px !important;
  top: 0 !important;
  width: 1px !important;
  height: 1px !important;
  overflow: hidden !important;
  opacity: 0 !important;
  pointer-events: none !important;
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
    wrapper_selector: '.bb-gtranslate-hidden',
    flag_size: 16,
    flag_style: '2d',
    switcher_horizontal_position: 'inline',
    switcher_open_direction: 'bottom'
  };
})();
document.addEventListener('DOMContentLoaded', function() {
  var btn = document.querySelector('[data-bb-lang-button]');
  var menu = document.querySelector('[data-bb-lang-menu]');
  var current = document.querySelector('[data-bb-lang-current]');
  if (!btn || !menu || !current) return;

  var labels = {
    'en':'English','es':'Español','fr':'Français','de':'Deutsch','it':'Italiano','pt':'Português','nl':'Nederlands',
    'ru':'Русский','uk':'Українська','pl':'Polski','tr':'Türkçe','ar':'العربية','fa':'فارسی','hi':'हिन्दी',
    'bn':'বাংলা','ur':'اردو','zh-CN':'简体中文','zh-TW':'繁體中文','ja':'日本語','ko':'한국어','vi':'Tiếng Việt',
    'th':'ไทย','id':'Bahasa Indonesia','ms':'Bahasa Melayu','tl':'Filipino','sw':'Kiswahili','he':'עברית',
    'el':'Ελληνικά','ro':'Română','sv':'Svenska'
  };

  function getStoredLang() {
    try { return localStorage.getItem('gt_selected_lang') || 'en'; } catch (e) { return 'en'; }
  }

  function setStoredLang(lang) {
    try { localStorage.setItem('gt_selected_lang', lang); } catch (e) {}
  }

  function updateLabel(lang) {
    current.textContent = labels[lang] || 'English';
  }

  function applyLanguage(lang) {
    setStoredLang(lang);
    updateLabel(lang);
    if (typeof window.doGTranslate === 'function') {
      try { window.doGTranslate('en|' + lang); } catch (e) {}
      return;
    }
    var select = document.querySelector('.bb-gtranslate-hidden select');
    if (select) {
      select.value = lang;
      select.dispatchEvent(new Event('change', { bubbles: true }));
    }
  }

  function openMenu() { menu.classList.remove('hidden'); }
  function closeMenu() { menu.classList.add('hidden'); }
  function toggleMenu() { menu.classList.contains('hidden') ? openMenu() : closeMenu(); }

  btn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    toggleMenu();
  });

  menu.querySelectorAll('[data-bb-lang]').forEach(function(item) {
    item.addEventListener('click', function(e) {
      e.preventDefault();
      var lang = item.getAttribute('data-bb-lang') || 'en';
      closeMenu();
      applyLanguage(lang);
    });
  });

  document.addEventListener('click', function(e) {
    if (!menu.contains(e.target) && !btn.contains(e.target)) closeMenu();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMenu();
  });

  updateLabel(getStoredLang());

  // If a language was previously selected, re-apply once the engine is ready.
  (function ensureApplied() {
    var desired = getStoredLang();
    if (!desired || desired === 'en') return;
    var attempts = 0;
    var timer = setInterval(function() {
      attempts++;
      var hasEngine = (typeof window.doGTranslate === 'function') || !!document.querySelector('.bb-gtranslate-hidden select');
      if (hasEngine) {
        clearInterval(timer);
        applyLanguage(desired);
      } else if (attempts > 30) {
        clearInterval(timer);
      }
    }, 200);
  })();
});
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
