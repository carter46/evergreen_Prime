/**
 * TradingView mini-chart manager — one TV script per page, skeleton + fallback handling.
 */
(function (global) {
    'use strict';

    var TV_SCRIPT = 'https://widgets.tradingview-widget.com/w/en/tv-mini-chart.js';
    var WIDGET_READY_MS = 20000;
    var POLL_MS = 250;

    function getHosts() {
        return Array.prototype.slice.call(document.querySelectorAll('tv-mini-chart'));
    }

    function getWidgetRoot(host) {
        return host.closest('[data-tv-widget]') || host.parentElement;
    }

    function clearSkeleton(root) {
        if (!root) return;
        root.querySelectorAll('.market-chart-skeleton').forEach(function (el) {
            el.classList.remove('market-chart-skeleton-active');
            el.hidden = true;
            el.style.display = 'none';
        });
    }

    function showFallback(root, message) {
        if (!root) return;
        clearSkeleton(root);
        var fallback = root.querySelector('.market-chart-fallback');
        if (fallback) {
            if (message) {
                var text = fallback.querySelector('.market-chart-fallback-text');
                if (text) text.textContent = message;
            }
            fallback.classList.remove('hidden');
            fallback.hidden = false;
        }
        if (hostInRoot(root)) hostInRoot(root).hidden = true;
    }

    function markReady(root) {
        if (!root) return;
        clearSkeleton(root);
        var fallback = root.querySelector('.market-chart-fallback');
        if (fallback) {
            fallback.classList.add('hidden');
            fallback.hidden = true;
        }
        var host = hostInRoot(root);
        if (host) host.hidden = false;
    }

    function hostInRoot(root) {
        return root.querySelector('tv-mini-chart');
    }

    function hostIsReady(host) {
        if (!host) return false;
        var sr = host.shadowRoot;
        if (sr) {
            if (sr.querySelector('iframe')) return true;
            if (sr.querySelector('div, canvas')) return sr.childElementCount > 0;
        }
        return !!host.querySelector('iframe');
    }

    function findTvScript() {
        return document.querySelector('script[data-tv-mini-chart-loader]')
            || document.querySelector('script[src*="tv-mini-chart.js"]');
    }

    function whenTvElementDefined() {
        if (!global.customElements || !global.customElements.whenDefined) {
            return Promise.resolve();
        }
        if (global.customElements.get('tv-mini-chart')) {
            return Promise.resolve();
        }
        return global.customElements.whenDefined('tv-mini-chart');
    }

    function waitForTvScript() {
        var tag = findTvScript();
        if (!tag) {
            tag = document.createElement('script');
            tag.type = 'module';
            tag.src = TV_SCRIPT;
            tag.setAttribute('data-tv-mini-chart-loader', '1');
            document.head.appendChild(tag);
        }

        if (tag.getAttribute('data-loaded') === '1') {
            return whenTvElementDefined();
        }

        return new Promise(function (resolve, reject) {
            var settled = false;
            function done() {
                if (settled) return;
                settled = true;
                tag.setAttribute('data-loaded', '1');
                whenTvElementDefined().then(resolve).catch(resolve);
            }
            function fail() {
                if (settled) return;
                settled = true;
                reject(new Error('TradingView script failed'));
            }
            tag.addEventListener('load', done);
            tag.addEventListener('error', fail);
            if (global.customElements && global.customElements.get('tv-mini-chart')) {
                done();
            }
        });
    }

    function watchHost(host) {
        var root = getWidgetRoot(host);
        var symbol = (host.getAttribute('symbol') || '').trim();

        if (!symbol) {
            showFallback(root, 'Chart configuration is unavailable for this market.');
            return;
        }

        if (hostIsReady(host)) {
            markReady(root);
            return;
        }

        var deadline = Date.now() + WIDGET_READY_MS;
        var timer = setInterval(function () {
            if (hostIsReady(host)) {
                clearInterval(timer);
                markReady(root);
                return;
            }
            if (Date.now() >= deadline) {
                clearInterval(timer);
                showFallback(root, 'Live chart could not be loaded. Please refresh or try again later.');
            }
        }, POLL_MS);
    }

    function initCharts() {
        var hosts = getHosts();
        if (!hosts.length) return;

        waitForTvScript()
            .then(function () {
                hosts.forEach(watchHost);
            })
            .catch(function () {
                hosts.forEach(function (host) {
                    showFallback(getWidgetRoot(host), 'Live chart could not be loaded. Please refresh or try again later.');
                });
            });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initCharts);
    } else {
        initCharts();
    }

    global.BloombitMarketWidgets = {
        init: initCharts,
        hostIsReady: hostIsReady,
        getScriptCount: function () {
            return document.querySelectorAll('script[src*="tv-mini-chart.js"]').length;
        }
    };
})(typeof window !== 'undefined' ? window : this);
