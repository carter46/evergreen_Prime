<?php
// Public-site translator (GTranslate) with 50 languages (curated set).
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
      'tr','ar','fa','he','el','ro','sv','no','da','fi',
      'cs','hu','bg','sr','hr','sk','lt','lv','et',
      'zh-CN','zh-TW','ja','ko','vi','th','id','ms','tl',
      'hi','sw','af','am','ha','yo','ig','zu','km','my','ne','uz'
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

  // Portal target (prevents sticky/backdrop-blur stacking context issues).
  var menuHomeParent = menu.parentNode;
  var menuHomeNext = menu.nextSibling;

  var labels = {
    'en':'English','es':'Español','fr':'Français','de':'Deutsch','it':'Italiano','pt':'Português','nl':'Nederlands',
    'ru':'Русский','uk':'Українська','pl':'Polski','tr':'Türkçe','ar':'العربية','fa':'فارسی','he':'עברית',
    'el':'Ελληνικά','ro':'Română','sv':'Svenska','no':'Norsk','da':'Dansk','fi':'Suomi','cs':'Čeština','hu':'Magyar',
    'bg':'Български','sr':'Српски','hr':'Hrvatski','sk':'Slovenčina','lt':'Lietuvių','lv':'Latviešu','et':'Eesti',
    'zh-CN':'简体中文','zh-TW':'繁體中文','ja':'日本語','ko':'한국어','vi':'Tiếng Việt','th':'ไทย','id':'Bahasa Indonesia',
    'ms':'Bahasa Melayu','tl':'Filipino','hi':'हिन्दी','sw':'Kiswahili','af':'Afrikaans','am':'አማርኛ','ha':'Hausa',
    'yo':'Yorùbá','ig':'Igbo','zu':'isiZulu','km':'ភាសាខ្មែរ','my':'မြန်မာ','ne':'नेपाली','uz':'Oʻzbek'
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
    'he': 'https://flagcdn.com/24x18/il.png',
    'el': 'https://flagcdn.com/24x18/gr.png',
    'ro': 'https://flagcdn.com/24x18/ro.png',
    'sv': 'https://flagcdn.com/24x18/se.png',
    'no': 'https://flagcdn.com/24x18/no.png',
    'da': 'https://flagcdn.com/24x18/dk.png',
    'fi': 'https://flagcdn.com/24x18/fi.png',
    'cs': 'https://flagcdn.com/24x18/cz.png',
    'hu': 'https://flagcdn.com/24x18/hu.png',
    'bg': 'https://flagcdn.com/24x18/bg.png',
    'sr': 'https://flagcdn.com/24x18/rs.png',
    'hr': 'https://flagcdn.com/24x18/hr.png',
    'sk': 'https://flagcdn.com/24x18/sk.png',
    'lt': 'https://flagcdn.com/24x18/lt.png',
    'lv': 'https://flagcdn.com/24x18/lv.png',
    'et': 'https://flagcdn.com/24x18/ee.png',
    'zh-CN': 'https://flagcdn.com/24x18/cn.png',
    'zh-TW': 'https://flagcdn.com/24x18/tw.png',
    'ja': 'https://flagcdn.com/24x18/jp.png',
    'ko': 'https://flagcdn.com/24x18/kr.png',
    'vi': 'https://flagcdn.com/24x18/vn.png',
    'th': 'https://flagcdn.com/24x18/th.png',
    'id': 'https://flagcdn.com/24x18/id.png',
    'ms': 'https://flagcdn.com/24x18/my.png',
    'tl': 'https://flagcdn.com/24x18/ph.png',
    'hi': 'https://flagcdn.com/24x18/in.png',
    'sw': 'https://flagcdn.com/24x18/ke.png',
    'af': 'https://flagcdn.com/24x18/za.png',
    'am': 'https://flagcdn.com/24x18/et.png',
    'ha': 'https://flagcdn.com/24x18/ng.png',
    'yo': 'https://flagcdn.com/24x18/ng.png',
    'ig': 'https://flagcdn.com/24x18/ng.png',
    'zu': 'https://flagcdn.com/24x18/za.png',
    'km': 'https://flagcdn.com/24x18/kh.png',
    'my': 'https://flagcdn.com/24x18/mm.png',
    'ne': 'https://flagcdn.com/24x18/np.png',
    'uz': 'https://flagcdn.com/24x18/uz.png'
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

  var languageOrder = [
    'en','es','fr','de','it','pt','nl','ru','uk','pl',
    'tr','ar','fa','he','el','ro','sv','no','da','fi',
    'cs','hu','bg','sr','hr','sk','lt','lv','et',
    'zh-CN','zh-TW','ja','ko','vi','th','id','ms','tl',
    'hi','sw','af','am','ha','yo','ig','zu','km','my','ne','uz'
  ];

  function renderLanguageMenu() {
    var list = menu.querySelector('[data-bb-lang-items]') || menu.querySelector('.max-h-72') || menu;
    if (!list) return;
    list.innerHTML = '';

    languageOrder.forEach(function(lang) {
      var label = labels[lang] || lang;
      var flag = flags[lang] || flags['en'];
      var b = document.createElement('button');
      b.type = 'button';
      b.setAttribute('data-bb-lang', lang);
      b.className = 'w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate flex items-center gap-2';
      b.setAttribute('translate', 'no');

      var img = document.createElement('img');
      img.src = flag;
      img.alt = label + ' flag';
      img.className = 'w-4 h-4 rounded-sm object-cover shrink-0';
      img.onerror = function() { img.style.display = 'none'; };

      var span = document.createElement('span');
      span.textContent = label;
      span.className = 'notranslate';
      span.setAttribute('translate', 'no');

      b.appendChild(img);
      b.appendChild(span);
      list.appendChild(b);
    });
  }

  function protectIconLigatures(root) {
    var scope = root || document;
    scope.querySelectorAll('.material-icons, .material-icons-round, .material-symbols-outlined, .material-symbols-rounded, .material-icons-outlined')
      .forEach(function(el) {
        el.classList.add('notranslate');
        el.setAttribute('translate', 'no');
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
    var spaceBelow = window.innerHeight - rect.bottom;
    var spaceAbove = rect.top;
    var openDown = spaceBelow >= 260 || spaceBelow >= spaceAbove;
    if (openDown) {
      menu.style.bottom = 'auto';
      menu.style.top = (rect.bottom + 8) + 'px';
    } else {
      menu.style.top = 'auto';
      menu.style.bottom = Math.max(8, (window.innerHeight - rect.top + 8)) + 'px';
    }
    menu.style.width = 'auto';
    menu.style.minWidth = Math.max(224, rect.width) + 'px';
    menu.style.zIndex = '2147483647';

    // Keep within viewport height.
    var maxH = openDown ? Math.max(160, spaceBelow - 16) : Math.max(160, spaceAbove - 16);
    var inner = menu.querySelector('div');
    if (inner) inner.style.maxHeight = Math.min(480, maxH) + 'px';
  }

  function portalMenuToBody() {
    if (menu.parentNode !== document.body) {
      document.body.appendChild(menu);
    }
  }

  function restoreMenuHome() {
    if (menuHomeParent && menu.parentNode === document.body) {
      if (menuHomeNext && menuHomeNext.parentNode === menuHomeParent) {
        menuHomeParent.insertBefore(menu, menuHomeNext);
      } else {
        menuHomeParent.appendChild(menu);
      }
    }
  }

  function openMenu() {
    renderLanguageMenu();
    portalMenuToBody();
    positionMenu();
    menu.classList.remove('hidden');
  }
  function closeMenu() {
    menu.classList.add('hidden');
    restoreMenuHome();
  }
  function toggleMenu() { menu.classList.contains('hidden') ? openMenu() : closeMenu(); }

  btn.addEventListener('click', function(e) {
    e.preventDefault();
    e.stopPropagation();
    toggleMenu();
  });

  menu.addEventListener('click', function(e) {
    var item = e.target && e.target.closest ? e.target.closest('[data-bb-lang]') : null;
    if (!item) return;
    e.preventDefault();
    var lang = item.getAttribute('data-bb-lang') || 'en';
    closeMenu();
    applyLanguage(lang);
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
  protectIconLigatures(document);
  renderLanguageMenu();

  // Keep protecting icons after translation DOM rewrites.
  try {
    var mo = new MutationObserver(function(muts) {
      for (var i = 0; i < muts.length; i++) {
        var m = muts[i];
        if (m.addedNodes && m.addedNodes.length) {
          m.addedNodes.forEach(function(n) {
            if (n && n.nodeType === 1) protectIconLigatures(n);
          });
        }
      }
    });
    mo.observe(document.documentElement, { childList: true, subtree: true });
  } catch (e) {}

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
