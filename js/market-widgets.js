/**
 * Lazy-load TradingView mini-chart widgets when market sections enter the viewport.
 */
(function (global) {
    'use strict';

    var TV_SCRIPT = 'https://widgets.tradingview-widget.com/w/en/tv-mini-chart.js';
    var scriptLoaded = false;
    var scriptLoading = false;
    var pendingCallbacks = [];

    function loadTradingViewScript(callback) {
        if (scriptLoaded) {
            if (callback) callback();
            return;
        }
        if (callback) pendingCallbacks.push(callback);
        if (scriptLoading) return;
        scriptLoading = true;
        var script = document.createElement('script');
        script.type = 'module';
        script.src = TV_SCRIPT;
        script.onload = function () {
            scriptLoaded = true;
            scriptLoading = false;
            var cbs = pendingCallbacks.slice();
            pendingCallbacks = [];
            cbs.forEach(function (cb) { cb(); });
        };
        script.onerror = function () {
            scriptLoading = false;
            pendingCallbacks = [];
        };
        document.head.appendChild(script);
    }

    function clearSkeleton(container) {
        container.classList.remove('market-chart-skeleton-active');
        var skeleton = container.querySelector('.market-chart-skeleton');
        if (skeleton) skeleton.setAttribute('aria-hidden', 'true');
    }

    function observeLazyContainers(selector) {
        var containers = document.querySelectorAll(selector || '[data-lazy-tv]');
        if (!containers.length) return;

        if (!('IntersectionObserver' in global)) {
            loadTradingViewScript(function () {
                containers.forEach(clearSkeleton);
            });
            return;
        }

        var observer = new IntersectionObserver(function (entries) {
            entries.forEach(function (entry) {
                if (!entry.isIntersecting) return;
                observer.unobserve(entry.target);
                loadTradingViewScript(function () {
                    clearSkeleton(entry.target);
                });
            });
        }, { rootMargin: '240px 0px', threshold: 0.05 });

        containers.forEach(function (el) { observer.observe(el); });
    }

    function init() {
        observeLazyContainers('[data-lazy-tv]');
        observeLazyContainers('[data-lazy-tv-detail]');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    global.BloombitMarketWidgets = {
        loadTradingViewScript: loadTradingViewScript,
        init: init
    };
})(typeof window !== 'undefined' ? window : this);
