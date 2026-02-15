/**
 * Bloombit - Frontend Application JS
 * API fetch wrapper, form handlers, and page-specific init.
 */
(function () {
    'use strict';

    const API_BASE = '/api';

    function apiFetch(url, options = {}) {
        const opts = {
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                ...options.headers
            },
            ...options
        };
        return fetch(API_BASE + url, opts)
            .then(res => res.json().then(data => ({ ok: res.ok, status: res.status, data })))
            .then(({ ok, status, data }) => {
                if (!ok) throw new Error(data.error || 'Request failed');
                return data;
            });
    }

    function apiPostForm(url, formData) {
        return fetch(API_BASE + url, {
            method: 'POST',
            credentials: 'same-origin',
            body: formData,
            headers: { 'Accept': 'application/json' }
        })
            .then(res => res.json().then(data => ({ ok: res.ok, data })))
            .then(({ ok, data }) => {
                if (!ok) throw new Error(data.error || 'Request failed');
                return data;
            });
    }

    function showMessage(el, text, isError = false) {
        if (!el) return;
        el.textContent = text;
        el.className = 'text-sm mt-2 ' + (isError ? 'text-red-500' : 'text-green-600');
        el.style.display = 'block';
    }

    function initContactForm() {
        const form = document.getElementById('contact-form');
        if (!form) return;
        const msgEl = document.getElementById('contact-form-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn ? btn.innerHTML : '';
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span>Sending...</span>';
            }
            const fd = new FormData(form);
            apiPostForm('/mail/contact.php', fd)
                .then(data => {
                    showMessage(msgEl, data.message || 'Message sent successfully!', false);
                    form.reset();
                })
                .catch(err => showMessage(msgEl, err.message || 'Failed to send. Try again.', true))
                .finally(() => {
                    if (btn) {
                        btn.disabled = false;
                        btn.innerHTML = originalText;
                    }
                });
        });
    }

    function initWithdrawalForm() {
        const form = document.getElementById('withdrawal-form');
        if (!form) return;
        const msgEl = document.getElementById('withdrawal-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const fd = new FormData(form);
            const data = { currency: fd.get('currency'), amount: fd.get('amount'), address: fd.get('address') };
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/user/withdraw.php', { method: 'POST', body: JSON.stringify(data) })
                .then(() => showMessage(msgEl, 'Withdrawal request submitted.', false))
                .catch(err => showMessage(msgEl, err.message, true))
                .finally(() => { if (btn) btn.disabled = false; });
        });
    }

    function initAdminPlanForm() {
        const form = document.getElementById('admin-plan-form');
        if (!form) return;
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const fd = new FormData(form);
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/admin/plans.php', { method: 'POST', body: JSON.stringify(Object.fromEntries(fd)) })
                .then(() => {
                    const drawer = document.getElementById('plan-drawer');
                    if (drawer) drawer.classList.add('hidden');
                    window.location.reload();
                })
                .catch(err => alert(err.message))
                .finally(() => { if (btn) btn.disabled = false; });
        });
    }

    function initBroadcastForm() {
        const form = document.getElementById('broadcast-form');
        if (!form) return;
        const msgEl = document.getElementById('broadcast-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const subject = form.querySelector('[name="subject"]')?.value || '';
            const body = form.querySelector('[name="body"]')?.value || form.querySelector('textarea')?.value || '';
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/admin/broadcast.php', { method: 'POST', body: JSON.stringify({ subject, body, recipients: 'all' }) })
                .then(() => showMessage(msgEl, 'Broadcast queued for delivery.', false))
                .catch(err => showMessage(msgEl, err.message, true))
                .finally(() => { if (btn) btn.disabled = false; });
        });
    }

    function initLoginForm() {
        const form = document.getElementById('login-form');
        if (!form) return;
        const msgEl = document.getElementById('login-form-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = form.querySelector('[name="email"]')?.value?.trim() || '';
            const password = form.querySelector('[name="password"]')?.value || '';
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/auth/login.php', { method: 'POST', body: JSON.stringify({ email, password }) })
                .then(data => {
                    const params = new URLSearchParams(window.location.search);
                    const redirect = params.get('redirect') || data.data?.redirect || '/dashboard';
                    window.location.href = redirect;
                })
                .catch(err => {
                    showMessage(msgEl, err.message || 'Login failed. Try again.', true);
                    if (btn) btn.disabled = false;
                });
        });
    }

    function initForgotPasswordForm() {
        const form = document.getElementById('forgot-password-form');
        if (!form) return;
        const msgEl = document.getElementById('forgot-password-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = form.querySelector('[name="email"]')?.value?.trim() || '';
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/auth/forgot-password.php', { method: 'POST', body: JSON.stringify({ email }) })
                .then(() => showMessage(msgEl, 'If that email exists, we\'ve sent a reset link.', false))
                .catch(err => showMessage(msgEl, err.message, true))
                .finally(() => { if (btn) btn.disabled = false; });
        });
    }

    function initResetPasswordForm() {
        const params = new URLSearchParams(window.location.search);
        const token = params.get('token');
        const email = params.get('email');
        const formWrapper = document.getElementById('reset-form-wrapper');
        const invalidEl = document.getElementById('invalid-token');
        const form = document.getElementById('reset-password-form');
        if (!form) return;

        if (!token || !email) {
            if (formWrapper) formWrapper.classList.add('hidden');
            if (invalidEl) invalidEl.classList.remove('hidden');
            return;
        }
        document.getElementById('reset-token').value = token;
        document.getElementById('reset-email').value = email;

        const msgEl = document.getElementById('reset-password-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const password = form.querySelector('[name="password"]')?.value || '';
            const confirm = form.querySelector('[name="confirm_password"]')?.value || '';
            if (password !== confirm) {
                showMessage(msgEl, 'Passwords do not match.', true);
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/auth/reset-password.php', {
                method: 'POST',
                body: JSON.stringify({ token, email, password, confirm_password: confirm })
            })
                .then(data => {
                    showMessage(msgEl, data.message || 'Password updated! Redirecting...', false);
                    setTimeout(() => { window.location.href = data.redirect || '/login'; }, 1500);
                })
                .catch(err => {
                    showMessage(msgEl, err.message, true);
                    if (btn) btn.disabled = false;
                });
        });
    }

    function initRegisterForm() {
        const form = document.getElementById('register-form');
        if (!form) return;
        const msgEl = document.getElementById('register-form-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = form.querySelector('[name="name"]')?.value?.trim() || '';
            const email = form.querySelector('[name="email"]')?.value?.trim() || '';
            const password = form.querySelector('[name="password"]')?.value || '';
            const confirm = form.querySelector('[name="confirm_password"]')?.value || '';
            if (password !== confirm) {
                showMessage(msgEl, 'Passwords do not match.', true);
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/auth/register.php', { method: 'POST', body: JSON.stringify({ name, email, password }) })
                .then(data => {
                    window.location.href = data.data?.redirect || '/dashboard';
                })
                .catch(err => {
                    showMessage(msgEl, err.message || 'Registration failed. Try again.', true);
                    if (btn) btn.disabled = false;
                });
        });
    }

    function initPasswordToggle() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-password-toggle]');
            if (!btn) return;
            e.preventDefault();
            const wrap = btn.closest('.relative') || btn.parentElement;
            const inp = wrap ? wrap.querySelector('input[type="password"], input[type="text"]') : null;
            if (!inp) return;
            const icon = btn.querySelector('.material-icons, .material-symbols-outlined');
            if (inp.type === 'password') {
                inp.type = 'text';
                if (icon) icon.textContent = 'visibility_off';
            } else {
                inp.type = 'password';
                if (icon) icon.textContent = 'visibility';
            }
        });
    }

    function initLogoutButtons() {
        document.querySelectorAll('[data-logout]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                apiFetch('/auth/logout.php', { method: 'POST' })
                    .then(() => { window.location.href = '/'; })
                    .catch(() => { window.location.href = '/'; });
            });
        });
    }

    function requireAuth() {
        const path = window.location.pathname;
        if (!path.startsWith('/dashboard')) return;
        fetch(API_BASE + '/auth/check.php', { credentials: 'same-origin' })
            .then(r => r.json())
            .then(data => {
                if (!data.authenticated) {
                    window.location.href = '/login?redirect=' + encodeURIComponent(path);
                }
            })
            .catch(() => { window.location.href = '/login'; });
    }

    function initNewsletterForm() {
        const form = document.getElementById('newsletter-form');
        if (!form) return;
        const msgEl = document.getElementById('newsletter-message');
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const input = form.querySelector('input[type="email"]');
            const btn = form.querySelector('button[type="submit"]');
            const email = input ? input.value.trim() : '';
            if (!email) {
                showMessage(msgEl, 'Please enter your email.', true);
                return;
            }
            if (btn) btn.disabled = true;
            const fd = new FormData();
            fd.append('email', email);
            apiPostForm('/mail/newsletter.php', fd)
                .then(data => {
                    showMessage(msgEl, data.message || 'Thank you for subscribing!', false);
                    if (input) input.value = '';
                })
                .catch(err => showMessage(msgEl, err.message || 'Subscription failed. Try again.', true))
                .finally(() => {
                    if (btn) btn.disabled = false;
                });
        });
    }

    function init() {
        requireAuth();
        initPasswordToggle();
        initLogoutButtons();
        initLoginForm();
        initRegisterForm();
        initForgotPasswordForm();
        initResetPasswordForm();
        initContactForm();
        initNewsletterForm();
        initWithdrawalForm();
        initAdminPlanForm();
        initBroadcastForm();
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    window.BloombitAPI = { apiFetch, apiPostForm };
})();
