/**
 * TradingView mini-chart manager — one TV script per page, skeleton + fallback handling.
 */
(function (global) {
    'use strict';

    var WIDGET_READY_MS = 18000;
    var FORCE_CLEAR_MS = 1500;
    var POLL_MS = 200;

    function getHosts() {
        return Array.prototype.slice.call(document.querySelectorAll('tv-mini-chart'));
    }

    function getWidgetRoot(host) {
        return host.closest('[data-tv-widget]') || host.parentElement;
    }

    function removeSkeleton(root) {
        if (!root) return;
        root.querySelectorAll('.market-chart-skeleton').forEach(function (el) {
            el.remove();
        });
    }

    function showFallback(root, message) {
        if (!root) return;
        removeSkeleton(root);
        var fallback = root.querySelector('.market-chart-fallback');
        if (fallback) {
            if (message) {
                var text = fallback.querySelector('.market-chart-fallback-text');
                if (text) text.textContent = message;
            }
            fallback.classList.remove('hidden');
            fallback.hidden = false;
        }
        var host = hostInRoot(root);
        if (host) host.hidden = true;
    }

    function markReady(root) {
        if (!root || root.getAttribute('data-tv-ready') === '1') return;
        root.setAttribute('data-tv-ready', '1');
        removeSkeleton(root);
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

    function hostHasContent(host) {
        if (!host) return false;
        var rect = host.getBoundingClientRect();
        if (rect.width > 0 && rect.height > 0 && host.childElementCount > 0) {
            return true;
        }
        var sr = host.shadowRoot;
        if (sr) {
            if (sr.querySelector('iframe')) return true;
            if (sr.querySelector('div, canvas, svg')) return sr.childElementCount > 0;
        }
        return !!host.querySelector('iframe');
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

    function waitForTvElement() {
        var tag = document.querySelector('script[data-tv-mini-chart-loader]')
            || document.querySelector('script[src*="tv-mini-chart.js"]');

        if (!tag) {
            return Promise.reject(new Error('TradingView script tag missing'));
        }

        if (global.customElements && global.customElements.get('tv-mini-chart')) {
            return Promise.resolve();
        }

        return new Promise(function (resolve, reject) {
            var settled = false;
            var timeout = setTimeout(function () {
                if (settled) return;
                if (global.customElements && global.customElements.get('tv-mini-chart')) {
                    settled = true;
                    resolve();
                    return;
                }
                settled = true;
                reject(new Error('TradingView custom element not defined'));
            }, WIDGET_READY_MS);

            whenTvElementDefined()
                .then(function () {
                    if (settled) return;
                    settled = true;
                    clearTimeout(timeout);
                    resolve();
                })
                .catch(function () {
                    if (settled) return;
                    settled = true;
                    clearTimeout(timeout);
                    reject(new Error('TradingView custom element failed'));
                });
        });
    }

    function watchHost(host) {
        var root = getWidgetRoot(host);
        var symbol = (host.getAttribute('symbol') || '').trim();
        var cleaned = false;

        function finishReady() {
            if (cleaned) return;
            cleaned = true;
            if (observer) observer.disconnect();
            if (pollTimer) clearInterval(pollTimer);
            if (forceTimer) clearTimeout(forceTimer);
            if (failTimer) clearTimeout(failTimer);
            markReady(root);
        }

        function finishFallback(message) {
            if (cleaned) return;
            cleaned = true;
            if (observer) observer.disconnect();
            if (pollTimer) clearInterval(pollTimer);
            if (forceTimer) clearTimeout(forceTimer);
            if (failTimer) clearTimeout(failTimer);
            showFallback(root, message);
        }

        if (!symbol) {
            showFallback(root, 'Chart configuration is unavailable for this market.');
            return;
        }

        if (hostHasContent(host)) {
            markReady(root);
            return;
        }

        var observer = null;
        var pollTimer = null;
        var forceTimer = null;
        var failTimer = null;

        var shadowObserved = false;

        if (typeof MutationObserver !== 'undefined') {
            observer = new MutationObserver(function () {
                if (!shadowObserved && host.shadowRoot) {
                    observer.observe(host.shadowRoot, { childList: true, subtree: true });
                    shadowObserved = true;
                }
                if (hostHasContent(host)) finishReady();
            });
            observer.observe(host, { childList: true, subtree: true });
            if (host.shadowRoot) {
                observer.observe(host.shadowRoot, { childList: true, subtree: true });
                shadowObserved = true;
            }
        }

        pollTimer = setInterval(function () {
            if (!shadowObserved && host.shadowRoot && observer) {
                observer.observe(host.shadowRoot, { childList: true, subtree: true });
                shadowObserved = true;
            }
            if (hostHasContent(host)) finishReady();
        }, POLL_MS);

        forceTimer = setTimeout(function () {
            if (hostHasContent(host)) finishReady();
        }, FORCE_CLEAR_MS);

        failTimer = setTimeout(function () {
            if (!hostHasContent(host)) {
                finishFallback('Live chart could not be loaded. Please refresh or try again later.');
            } else {
                finishReady();
            }
        }, WIDGET_READY_MS);
    }

    function initCharts() {
        var hosts = getHosts();
        if (!hosts.length) return;

        waitForTvElement()
            .then(function () {
                hosts.forEach(watchHost);
            })
            .catch(function () {
                hosts.forEach(function (host) {
                    showFallback(
                        getWidgetRoot(host),
                        'Live chart could not be loaded. Please refresh or try again later.'
                    );
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
        hostHasContent: hostHasContent,
        getScriptCount: function () {
            return document.querySelectorAll('script[src*="tv-mini-chart.js"]').length;
        }
    };
})(typeof window !== 'undefined' ? window : this);
