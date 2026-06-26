/**
 * PWA install flow — beforeinstallprompt, iOS A2HS modal, smart button states.
 */
(function (global) {
    'use strict';

    var deferredPrompt = null;
    var state = 'idle'; // idle | installing | installed | standalone

    function isStandalone() {
        return global.matchMedia('(display-mode: standalone)').matches
            || global.navigator.standalone === true;
    }

    function isIOS() {
        return /iphone|ipad|ipod/i.test(global.navigator.userAgent)
            && !global.MSStream;
    }

    function isAndroid() {
        return /android/i.test(global.navigator.userAgent);
    }

    function shouldShowNavInstall() {
        return state === 'installing' || !!deferredPrompt || isIOS();
    }

    function updateNavInstallButton(btn, mode) {
        if (mode !== 'menu' && mode !== 'footer') return;
        if (state === 'standalone' || state === 'installed') {
            btn.classList.add('hidden');
            return;
        }
        if (shouldShowNavInstall()) {
            btn.classList.remove('hidden');
        } else {
            btn.classList.add('hidden');
        }
    }

    function getButtons() {
        return Array.prototype.slice.call(document.querySelectorAll('[data-pwa-install]'));
    }

    function getModal() {
        return document.getElementById('pwa-install-modal');
    }

    function openModal(mode) {
        var modal = getModal();
        if (!modal) return;
        var iosPanel = modal.querySelector('[data-pwa-panel="ios"]');
        var desktopPanel = modal.querySelector('[data-pwa-panel="desktop"]');
        var mobilePanel = modal.querySelector('[data-pwa-panel="mobile"]');
        [iosPanel, desktopPanel, mobilePanel].forEach(function (p) {
            if (p) p.classList.add('hidden');
        });
        if (isIOS() && iosPanel) {
            iosPanel.classList.remove('hidden');
        } else if (mode === 'desktop' && desktopPanel) {
            desktopPanel.classList.remove('hidden');
        } else if (mobilePanel) {
            mobilePanel.classList.remove('hidden');
        } else if (desktopPanel) {
            desktopPanel.classList.remove('hidden');
        }
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }

    function closeModal() {
        var modal = getModal();
        if (!modal) return;
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = '';
    }

    function setState(next) {
        state = next;
        var buttons = getButtons();
        buttons.forEach(function (btn) {
            var mode = btn.getAttribute('data-pwa-install');
            var labelEl = btn.querySelector('[data-pwa-label]') || btn;
            var subEl = btn.querySelector('[data-pwa-sub]');

            if (state === 'standalone' || state === 'installed') {
                if (mode === 'menu' || mode === 'footer') {
                    btn.classList.add('hidden');
                    return;
                }
                btn.disabled = true;
                btn.classList.remove('opacity-50');
                if (labelEl) labelEl.textContent = '✓ App Installed';
                if (subEl) subEl.textContent = 'Installed on your device';
                return;
            }

            if (mode === 'menu' || mode === 'footer') {
                updateNavInstallButton(btn, mode);
            } else {
                btn.classList.remove('hidden');
            }
            btn.disabled = state === 'installing';

            if (state === 'installing') {
                if (labelEl) labelEl.textContent = 'Installing...';
                if (subEl) subEl.textContent = '';
                btn.classList.add('opacity-70');
                return;
            }

            btn.classList.remove('opacity-70');
            if (mode === 'mobile') {
                if (labelEl) labelEl.textContent = 'Download for Mobile';
                if (subEl) subEl.textContent = 'Install directly to your device';
            } else if (mode === 'desktop') {
                if (labelEl) labelEl.textContent = 'Download for Desktop';
                if (subEl) subEl.textContent = 'Install directly to your device';
            } else if (mode === 'menu' || mode === 'footer') {
                if (labelEl) labelEl.textContent = 'Install App';
            }
        });
    }

    function showInstallUI() {
        if (state === 'standalone' || state === 'installed') return;
        getButtons().forEach(function (btn) {
            updateNavInstallButton(btn, btn.getAttribute('data-pwa-install'));
        });
    }

    function triggerInstall(mode) {
        if (state === 'installed' || state === 'standalone') return;

        if (isIOS() || (!deferredPrompt && (mode === 'mobile' || mode === 'menu' || mode === 'footer'))) {
            openModal(isIOS() ? 'ios' : mode);
            return;
        }

        if (!deferredPrompt) {
            openModal(mode);
            return;
        }

        setState('installing');
        deferredPrompt.prompt();
        deferredPrompt.userChoice.then(function (choice) {
            deferredPrompt = null;
            if (choice.outcome === 'accepted') {
                setState('installed');
            } else {
                setState('idle');
            }
        }).catch(function () {
            setState('idle');
        });
    }

    function init() {
        if (isStandalone()) {
            setState('standalone');
            getButtons().forEach(function (b) { b.classList.add('hidden'); });
            return;
        }

        if (global.localStorage && global.localStorage.getItem('pwa-installed') === '1') {
            setState('installed');
        } else {
            setState('idle');
        }

        global.addEventListener('beforeinstallprompt', function (e) {
            e.preventDefault();
            deferredPrompt = e;
            if (global.localStorage) global.localStorage.removeItem('pwa-installed');
            setState('idle');
            showInstallUI();
        });

        global.addEventListener('appinstalled', function () {
            deferredPrompt = null;
            if (global.localStorage) global.localStorage.setItem('pwa-installed', '1');
            setState('installed');
            closeModal();
        });

        getButtons().forEach(function (btn) {
            btn.addEventListener('click', function () {
                triggerInstall(btn.getAttribute('data-pwa-install') || 'desktop');
            });
        });

        var closeBtn = document.getElementById('pwa-install-modal-close');
        var modal = getModal();
        if (closeBtn) closeBtn.addEventListener('click', closeModal);
        if (modal) {
            modal.addEventListener('click', function (e) {
                if (e.target === modal) closeModal();
            });
        }
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') closeModal();
        });

        if (isIOS()) {
            getButtons().forEach(function (btn) {
                updateNavInstallButton(btn, btn.getAttribute('data-pwa-install'));
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    global.PwaInstall = { trigger: triggerInstall, getState: function () { return state; } };
})(typeof window !== 'undefined' ? window : this);
