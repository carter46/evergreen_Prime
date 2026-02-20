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
  var currentFlag = document.querySelector('[data-bb-lang-flag]');
  if (!btn || !menu || !current) return;

  var labels = {
    'en':'English','es':'Español','fr':'Français','de':'Deutsch','it':'Italiano','pt':'Português','nl':'Nederlands',
    'ru':'Русский','uk':'Українська','pl':'Polski','tr':'Türkçe','ar':'العربية','fa':'فارسی','hi':'हिन्दी',
    'bn':'বাংলা','ur':'اردو','zh-CN':'简体中文','zh-TW':'繁體中文','ja':'日本語','ko':'한국어','vi':'Tiếng Việt',
    'th':'ไทย','id':'Bahasa Indonesia','ms':'Bahasa Melayu','tl':'Filipino','sw':'Kiswahili','he':'עברית',
    'el':'Ελληνικά','ro':'Română','sv':'Svenska'
  };

  var flags = {
    'en': 'https://flagcdn.com/24x18/us.png',
    'es': 'https://flagcdn.com/24x18/es.png',
    'fr': 'https://flagcdn.com/24x18/fr.png',
    'de': 'https://flagcdn.com/24x18/de.png',
    'it': 'https://flagcdn.com/24x18/it.png',
    'pt': 'https://flagcdn.com/24x18/pt.png',
    'nl': 'https://flagcdn.com/24x18/nl.png',
    'ru': 'https://flagcdn.com/24x18/ru.png',
    'uk': 'https://flagcdn.com/24x18/ua.png',
    'pl': 'https://flagcdn.com/24x18/pl.png',
    'tr': 'https://flagcdn.com/24x18/tr.png',
    'ar': 'https://flagcdn.com/24x18/sa.png',
    'fa': 'https://flagcdn.com/24x18/ir.png',
    'hi': 'https://flagcdn.com/24x18/in.png',
    'bn': 'https://flagcdn.com/24x18/bd.png',
    'ur': 'https://flagcdn.com/24x18/pk.png',
    'zh-CN': 'https://flagcdn.com/24x18/cn.png',
    'zh-TW': 'https://flagcdn.com/24x18/tw.png',
    'ja': 'https://flagcdn.com/24x18/jp.png',
    'ko': 'https://flagcdn.com/24x18/kr.png',
    'vi': 'https://flagcdn.com/24x18/vn.png',
    'th': 'https://flagcdn.com/24x18/th.png',
    'id': 'https://flagcdn.com/24x18/id.png',
    'ms': 'https://flagcdn.com/24x18/my.png',
    'tl': 'https://flagcdn.com/24x18/ph.png',
    'sw': 'https://flagcdn.com/24x18/ke.png',
    'he': 'https://flagcdn.com/24x18/il.png',
    'el': 'https://flagcdn.com/24x18/gr.png',
    'ro': 'https://flagcdn.com/24x18/ro.png',
    'sv': 'https://flagcdn.com/24x18/se.png'
  };

  function getStoredLang() {
    try { return localStorage.getItem('gt_selected_lang') || 'en'; } catch (e) { return 'en'; }
  }

  function setStoredLang(lang) {
    try { localStorage.setItem('gt_selected_lang', lang); } catch (e) {}
  }

  function updateLabel(lang) {
    current.textContent = labels[lang] || 'English';
    if (currentFlag) {
      var src = flags[lang] || flags['en'];
      currentFlag.src = src;
      currentFlag.alt = (labels[lang] || 'English') + ' flag';
      currentFlag.onerror = function() { currentFlag.style.display = 'none'; };
      currentFlag.style.display = '';
    }
  }

  function ensureMenuFlags() {
    menu.querySelectorAll('[data-bb-lang]').forEach(function(item) {
      var lang = item.getAttribute('data-bb-lang') || 'en';
      item.classList.add('flex', 'items-center', 'gap-2');
      if (item.querySelector('img')) return;
      var img = document.createElement('img');
      img.src = flags[lang] || flags['en'];
      img.alt = (labels[lang] || 'English') + ' flag';
      img.className = 'w-4 h-4 rounded-sm object-cover shrink-0';
      img.onerror = function() { img.style.display = 'none'; };
      item.prepend(img);
    });
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

  function positionMenu() {
    var rect = btn.getBoundingClientRect();
    menu.style.position = 'fixed';
    menu.style.left = Math.max(8, rect.left) + 'px';
    menu.style.top = (rect.bottom + 8) + 'px';
    menu.style.width = 'auto';
    menu.style.minWidth = Math.max(224, rect.width) + 'px';
    menu.style.zIndex = '999999';
  }

  function openMenu() {
    ensureMenuFlags();
    positionMenu();
    menu.classList.remove('hidden');
  }
  function closeMenu() {
    menu.classList.add('hidden');
  }
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

  window.addEventListener('scroll', function() {
    if (!menu.classList.contains('hidden')) positionMenu();
  }, { passive: true });
  window.addEventListener('resize', function() {
    if (!menu.classList.contains('hidden')) positionMenu();
  });

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeMenu();
  });

  updateLabel(getStoredLang());
  ensureMenuFlags();

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
