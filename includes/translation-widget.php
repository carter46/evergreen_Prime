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
    // Languages used by the 50-country picker (one language per country).
    languages: [
      'en','fr','es','pt','de','it','nl','sv','pl','el',
      'ar','fa','he','sw','am','zh-CN','ja','ko','hi','ur',
      'bn','th','vi','id','tl'
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

  // 50 unique countries by region:
  // - Africa: 5
  // - Asia: 10
  // - Europe: 10
  // - South America: 10
  // - North America: 10
  // - Australia: 1
  // - Middle East: 4
  var countries = [
    // Africa (5)
    { id: 'za', name: 'South Africa', lang: 'en', flag: 'za' },
    { id: 'ng', name: 'Nigeria', lang: 'en', flag: 'ng' },
    { id: 'ke', name: 'Kenya', lang: 'sw', flag: 'ke' },
    { id: 'et', name: 'Ethiopia', lang: 'am', flag: 'et' },
    { id: 'eg', name: 'Egypt', lang: 'ar', flag: 'eg' },

    // Asia (10)
    { id: 'cn', name: 'China', lang: 'zh-CN', flag: 'cn' },
    { id: 'jp', name: 'Japan', lang: 'ja', flag: 'jp' },
    { id: 'kr', name: 'South Korea', lang: 'ko', flag: 'kr' },
    { id: 'in', name: 'India', lang: 'hi', flag: 'in' },
    { id: 'pk', name: 'Pakistan', lang: 'ur', flag: 'pk' },
    { id: 'bd', name: 'Bangladesh', lang: 'bn', flag: 'bd' },
    { id: 'th', name: 'Thailand', lang: 'th', flag: 'th' },
    { id: 'vn', name: 'Vietnam', lang: 'vi', flag: 'vn' },
    { id: 'id', name: 'Indonesia', lang: 'id', flag: 'id' },
    { id: 'ph', name: 'Philippines', lang: 'tl', flag: 'ph' },

    // Europe (10)
    { id: 'gb', name: 'United Kingdom', lang: 'en', flag: 'gb' },
    { id: 'fr', name: 'France', lang: 'fr', flag: 'fr' },
    { id: 'de', name: 'Germany', lang: 'de', flag: 'de' },
    { id: 'it', name: 'Italy', lang: 'it', flag: 'it' },
    { id: 'es', name: 'Spain', lang: 'es', flag: 'es' },
    { id: 'pt', name: 'Portugal', lang: 'pt', flag: 'pt' },
    { id: 'nl', name: 'Netherlands', lang: 'nl', flag: 'nl' },
    { id: 'se', name: 'Sweden', lang: 'sv', flag: 'se' },
    { id: 'pl', name: 'Poland', lang: 'pl', flag: 'pl' },
    { id: 'gr', name: 'Greece', lang: 'el', flag: 'gr' },

    // South America (10)
    { id: 'br', name: 'Brazil', lang: 'pt', flag: 'br' },
    { id: 'ar', name: 'Argentina', lang: 'es', flag: 'ar' },
    { id: 'co', name: 'Colombia', lang: 'es', flag: 'co' },
    { id: 'pe', name: 'Peru', lang: 'es', flag: 'pe' },
    { id: 'cl', name: 'Chile', lang: 'es', flag: 'cl' },
    { id: 'ec', name: 'Ecuador', lang: 'es', flag: 'ec' },
    { id: 'bo', name: 'Bolivia', lang: 'es', flag: 'bo' },
    { id: 'py', name: 'Paraguay', lang: 'es', flag: 'py' },
    { id: 'uy', name: 'Uruguay', lang: 'es', flag: 'uy' },
    { id: 've', name: 'Venezuela', lang: 'es', flag: 've' },

    // North America (10)
    { id: 'us', name: 'United States', lang: 'en', flag: 'us' },
    { id: 'ca', name: 'Canada', lang: 'fr', flag: 'ca' },
    { id: 'mx', name: 'Mexico', lang: 'es', flag: 'mx' },
    { id: 'gt', name: 'Guatemala', lang: 'es', flag: 'gt' },
    { id: 'hn', name: 'Honduras', lang: 'es', flag: 'hn' },
    { id: 'sv', name: 'El Salvador', lang: 'es', flag: 'sv' },
    { id: 'ni', name: 'Nicaragua', lang: 'es', flag: 'ni' },
    { id: 'cr', name: 'Costa Rica', lang: 'es', flag: 'cr' },
    { id: 'pa', name: 'Panama', lang: 'es', flag: 'pa' },
    { id: 'jm', name: 'Jamaica', lang: 'en', flag: 'jm' },

    // Australia (1)
    { id: 'au', name: 'Australia', lang: 'en', flag: 'au' },

    // Middle East (4)
    { id: 'sa', name: 'Saudi Arabia', lang: 'ar', flag: 'sa' },
    { id: 'ae', name: 'United Arab Emirates', lang: 'ar', flag: 'ae' },
    { id: 'ir', name: 'Iran', lang: 'fa', flag: 'ir' },
    { id: 'il', name: 'Israel', lang: 'he', flag: 'il' }
  ];

  function getStoredLang() {
    try { return localStorage.getItem('gt_selected_lang') || 'en'; } catch (e) { return 'en'; }
  }
  function getStoredCountry() {
    try { return localStorage.getItem('bb_selected_country') || ''; } catch (e) { return ''; }
  }

  function setStoredLang(lang) {
    try { localStorage.setItem('gt_selected_lang', lang); } catch (e) {}
  }
  function setStoredCountry(countryId) {
    try { localStorage.setItem('bb_selected_country', countryId); } catch (e) {}
  }

  function countryFlagUrl(cc) {
    return 'https://flagcdn.com/24x18/' + String(cc || 'us').toLowerCase() + '.png';
  }

  function updateLabel(countryId) {
    var c = countries.find(function(x){ return x.id === countryId; }) || countries.find(function(x){ return x.id === 'us'; }) || countries[0];
    if (!c) return;
    current.textContent = c.name;
    if (currentFlag) {
      currentFlag.src = countryFlagUrl(c.flag);
      currentFlag.alt = c.name + ' flag';
      currentFlag.onerror = function() { currentFlag.style.display = 'none'; };
      currentFlag.style.display = '';
    }
  }

  function renderCountryMenu() {
    var list = menu.querySelector('[data-bb-lang-items]') || menu.querySelector('[data-bb-country-items]') || menu.querySelector('.max-h-72') || menu;
    if (!list) return;
    list.innerHTML = '';
    countries.forEach(function(c) {
      var b = document.createElement('button');
      b.type = 'button';
      b.setAttribute('data-bb-country', c.id);
      b.setAttribute('data-bb-lang', c.lang);
      b.className = 'w-full text-left px-4 py-2 text-sm hover:bg-primary/10 notranslate flex items-center gap-2';
      b.setAttribute('translate', 'no');

      var img = document.createElement('img');
      img.src = countryFlagUrl(c.flag);
      img.alt = c.name + ' flag';
      img.className = 'w-4 h-4 rounded-sm object-cover shrink-0';
      img.onerror = function() { img.style.display = 'none'; };

      var span = document.createElement('span');
      span.textContent = c.name;
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

  function applyCountry(countryId) {
    var c = countries.find(function(x){ return x.id === countryId; });
    if (!c) return;
    setStoredCountry(c.id);
    setStoredLang(c.lang);
    updateLabel(c.id);
    if (typeof window.doGTranslate === 'function') {
      try { window.doGTranslate('en|' + c.lang); } catch (e) {}
      return;
    }
    var select = document.querySelector('.bb-gtranslate-hidden select');
    if (select) {
      select.value = c.lang;
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
    renderCountryMenu();
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
    var item = e.target && e.target.closest ? e.target.closest('[data-bb-country]') : null;
    if (!item) return;
    e.preventDefault();
    var countryId = item.getAttribute('data-bb-country') || 'us';
    closeMenu();
    applyCountry(countryId);
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

  var initialCountry = getStoredCountry() || 'us';
  updateLabel(initialCountry);
  protectIconLigatures(document);
  renderCountryMenu();

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
    var desiredCountry = getStoredCountry();
    if (!desiredCountry) return;
    var attempts = 0;
    var timer = setInterval(function() {
      attempts++;
      var hasEngine = (typeof window.doGTranslate === 'function') || !!document.querySelector('.bb-gtranslate-hidden select');
      if (hasEngine) {
        clearInterval(timer);
        applyCountry(desiredCountry);
      } else if (attempts > 30) {
        clearInterval(timer);
      }
    }, 200);
  })();
});
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
