<?php
/**
 * TRANSLATION WIDGET
 *
 * GTranslate widget for multi-language support (Cosmopolitan Bank implementation).
 * Translates all content on the site.
 */
?>
<style>
/* Fixed position translation widget - bottom left corner */
.gtranslate_wrapper {
    position: fixed !important;
    bottom: 20px !important;
    left: 20px !important;
    z-index: 9998 !important;
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
    background: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px) !important;
    border-radius: 8px !important;
    padding: 8px !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    border: 1px solid rgba(0, 0, 0, 0.1) !important;
    min-width: 120px !important;
}

/* Ensure GTranslate dropdown is visible */
.gtranslate_wrapper select,
.gtranslate_wrapper .gt_container,
.gtranslate_wrapper .gt_select,
.gtranslate_wrapper .gt_current,
.gtranslate_wrapper .gt_flag {
    display: block !important;
    visibility: visible !important;
    opacity: 1 !important;
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    .gtranslate_wrapper {
        bottom: 15px !important;
        left: 15px !important;
        padding: 6px !important;
        min-width: 100px !important;
        font-size: 14px !important;
    }
}

/* Ensure widget is above other elements but below modals */
.gtranslate_wrapper { z-index: 9998 !important; }
</style>
<script>
window.gtranslateSettings = {
    "default_language": "en",
    "detect_browser_language": true,
    // 15 languages total (keeps existing ones + adds more)
    "languages": ["en", "ko", "zh-CN", "es", "pt", "it", "tl", "ms", "vi", "ja", "ru", "fr", "de", "ar", "hi"],
    "wrapper_selector": ".gtranslate_wrapper",
    "flag_size": 24,
    "flag_style": "2d",
    "switcher_text_color": "#333333",
    "switcher_background_color": "#ffffff",
    "switcher_hover_background_color": "#f5f5f5"
};

document.addEventListener('DOMContentLoaded', function() {
    function waitForGTranslate(callback, maxAttempts) {
        var attempts = 0;
        var maxA = typeof maxAttempts === 'number' ? maxAttempts : 50;
        var checkInterval = setInterval(function() {
            attempts++;
            if (window.gtranslate || window.doGTranslate) {
                clearInterval(checkInterval);
                if (callback) callback();
            } else if (attempts >= maxA) {
                clearInterval(checkInterval);
                try { console.warn('[GTranslate] Widget did not load within expected time'); } catch (e) {}
            }
        }, 100);
    }

    waitForGTranslate(function() {
        if (window.gtranslate && typeof window.gtranslate.install === 'function') {
            try {
                var wrapper = document.querySelector('.gtranslate_wrapper');
                if (wrapper && !wrapper.querySelector('select')) {
                    window.gtranslate.install();
                }
            } catch (e) {
                try { console.log('[GTranslate] Reinstall check:', e); } catch (e2) {}
            }
        }

        var lastLanguage = (function(){ try { return localStorage.getItem('gt_selected_lang') || 'en'; } catch(e){ return 'en'; } })();
        setInterval(function() {
            var currentLanguage = (function(){ try { return localStorage.getItem('gt_selected_lang') || 'en'; } catch(e){ return 'en'; } })();
            if (currentLanguage !== lastLanguage) {
                lastLanguage = currentLanguage;
                var wrap = document.querySelector('.gtranslate_wrapper');
                if (wrap) {
                    var select = wrap.querySelector('select');
                    if (select && select.disabled) select.disabled = false;
                }
            }
        }, 500);
    });

    if (window.doGTranslate) {
        var originalDoGTranslate = window.doGTranslate;
        window.doGTranslate = function() {
            try {
                return originalDoGTranslate.apply(this, arguments);
            } catch(e) {
                try { console.error('[GTranslate] Error during translation:', e); } catch (e2) {}
                if (window.gtranslate && typeof window.gtranslate.install === 'function') {
                    setTimeout(function() {
                        try { window.gtranslate.install(); } catch (e3) {}
                    }, 1000);
                }
            }
        };
    }
});
</script>
<script src="https://cdn.gtranslate.net/widgets/latest/dwf.js" defer></script>
