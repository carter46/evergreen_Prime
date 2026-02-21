/**
 * Bloombit - Frontend Application JS
 * API fetch wrapper, form handlers, and page-specific init.
 */
(function () {
    'use strict';

    const API_BASE = '/api';

    function getSiteNameFromTitle() {
        if (typeof window.__SITE_NAME__ === 'string' && window.__SITE_NAME__.trim()) {
            return window.__SITE_NAME__.trim();
        }
        const metaSiteName = document.querySelector('meta[name="site-name"]');
        if (metaSiteName && metaSiteName.content && metaSiteName.content.trim()) {
            return metaSiteName.content.trim();
        }
        const t = (document.title || '').trim();
        if (!t) return 'Site';
        const looksLikePageTitle = function (value) {
            return /(register|secure login|login|forgot password|set new password|live chat|about|plans?|dashboard|admin|kyc|transactions?|wallet|profile|analytics|support|contact|legal|signals?|investment|comparison|management|center|hub|command|settings)/i.test(value);
        };
        const pipe = t.split('|').map(s => s.trim()).filter(Boolean);
        if (pipe.length >= 2) {
            const sitePart = pipe.find(s => !looksLikePageTitle(s));
            if (sitePart) return sitePart;
            return pipe[0];
        }
        const dash = t.split('—').map(s => s.trim()).filter(Boolean);
        if (dash.length >= 2) {
            const sitePart = dash.find(s => !looksLikePageTitle(s));
            if (sitePart) return sitePart;
            return dash[0];
        }
        return t;
    }

    function splitBrandName(siteName) {
        const n = (siteName || '').trim();
        if (!n) return ['Bloom', 'bit'];
        const normalized = n.replace(/\s+/g, ' ').trim();
        if (!normalized) return ['Bloom', 'bit'];
        if (normalized.includes(' ')) {
            const parts = normalized.split(' ');
            const suffix = parts.pop() || '';
            const base = parts.join(' ');
            return [base ? (base + ' ') : '', suffix];
        }
        if (normalized.length <= 3) return ['', normalized];
        return [normalized.slice(0, -3), normalized.slice(-3)];
    }

    function ensureGlobalLoader() {
        if (document.getElementById('bb-global-loader')) return;

        const style = document.createElement('style');
        style.id = 'bb-global-loader-style';
        style.textContent = `
            #bb-global-loader{position:fixed;inset:0;z-index:99999;display:none;align-items:center;justify-content:center;background:rgba(255,255,255,0.97);backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);transition:opacity .18s ease;opacity:0}
            html.dark #bb-global-loader{background:rgba(35,30,15,0.92)}
            #bb-global-loader.bb-show{display:flex;opacity:1}
            #bb-global-loader .bb-name{font-family:'Space Grotesk',system-ui,-apple-system,'Segoe UI',Roboto,Arial,sans-serif;font-weight:800;font-size:44px;letter-spacing:-0.02em;line-height:1.05;text-align:center}
            @media (max-width:640px){#bb-global-loader .bb-name{font-size:34px}}
            #bb-global-loader .bb-base{color:#1d180c}
            html.dark #bb-global-loader .bb-base{color:#ffffff}
            #bb-global-loader .bb-suffix{color:#ffc105}
            #bb-global-loader .bb-wave{display:inline-flex;gap:1px;align-items:flex-end}
            #bb-global-loader .bb-char{display:inline-block;transform:translateY(0);animation:bb-wave 1.05s ease-in-out infinite;animation-delay:calc(var(--i)*70ms)}
            @keyframes bb-wave{0%,100%{transform:translateY(0);opacity:.75}50%{transform:translateY(-12px);opacity:1}}
        `;
        document.head.appendChild(style);

        const [base, suffix] = splitBrandName(getSiteNameFromTitle());
        const makeWave = (text, cls, startIndex) => {
            const wrap = document.createElement('span');
            wrap.className = 'bb-wave ' + cls;
            const chars = Array.from(text);
            chars.forEach((ch, i) => {
                const s = document.createElement('span');
                s.className = 'bb-char';
                s.style.setProperty('--i', String(startIndex + i));
                s.textContent = ch;
                wrap.appendChild(s);
            });
            return wrap;
        };

        const root = document.createElement('div');
        root.id = 'bb-global-loader';
        root.setAttribute('aria-hidden', 'true');
        const name = document.createElement('div');
        name.className = 'bb-name';
        name.appendChild(makeWave(base, 'bb-base', 0));
        if (suffix) name.appendChild(makeWave(suffix, 'bb-suffix', base.length));
        root.appendChild(name);
        document.body.appendChild(root);
    }

    function showGlobalLoader() {
        ensureGlobalLoader();
        const el = document.getElementById('bb-global-loader');
        if (!el) return;
        // If hideGlobalLoader previously set display:none, clear it before showing.
        el.style.display = 'flex';
        el.classList.add('bb-show');
        el.setAttribute('aria-hidden', 'false');
    }

    function hideGlobalLoader() {
        const el = document.getElementById('bb-global-loader');
        if (!el) return;
        el.classList.remove('bb-show');
        el.setAttribute('aria-hidden', 'true');
        // keep in DOM for later transitions
        setTimeout(() => { if (!el.classList.contains('bb-show')) el.style.display = 'none'; }, 220);
    }

    function initGlobalLoader() {
        // Always create it (so we can reuse on transitions)
        ensureGlobalLoader();

        // Always hide on full load
        window.addEventListener('load', () => hideGlobalLoader(), { once: true });

        // Show loader immediately so the white overlay and centered animation are visible
        // while the page finishes loading (app.js often runs at end of body).
        showGlobalLoader();

        let fallbackHideTimer = null;
        const scheduleFallbackHide = () => {
            if (fallbackHideTimer) clearTimeout(fallbackHideTimer);
            fallbackHideTimer = setTimeout(() => {
                // If navigation didn't happen, ensure we don't get stuck.
                if (document.visibilityState === 'visible') hideGlobalLoader();
            }, 1800);
        };

        // Show on internal navigation clicks
        document.addEventListener('click', function (e) {
            const a = e.target && e.target.closest ? e.target.closest('a') : null;
            if (!a) return;
            const href = a.getAttribute('href') || '';
            if (!href || href.startsWith('#') || href.startsWith('javascript:')) return;
            if (a.hasAttribute('download')) return;
            if ((a.getAttribute('target') || '').toLowerCase() === '_blank') return;
            if (e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
            let url;
            try { url = new URL(href, window.location.origin); } catch { return; }
            if (url.origin !== window.location.origin) return;
            showGlobalLoader();
            scheduleFallbackHide();
        }, true);

        // Show on real (non-AJAX) form navigations
        document.addEventListener('submit', function (e) {
            const form = e.target;
            if (!form || form.tagName !== 'FORM') return;
            if (e.defaultPrevented) return; // our JS form handlers are AJAX
            const target = (form.getAttribute('target') || '').toLowerCase();
            if (target === '_blank') return;
            showGlobalLoader();
            scheduleFallbackHide();
        });

        // Ensure loader is hidden if a navigation was cancelled
        window.addEventListener('pageshow', () => hideGlobalLoader());
    }

    function setButtonLoading(btn, loading, text) {
        if (!btn) return;
        if (loading) {
            if (btn.dataset.loading === '1') return;
            btn.dataset.loading = '1';
            btn.dataset.originalHtml = btn.innerHTML;
            btn.disabled = true;
            const label = text || 'Loading...';
            btn.innerHTML =
                '<span class="inline-block w-4 h-4 border-2 border-black/40 border-t-black rounded-full animate-spin"></span>' +
                '<span>' + label + '</span>';
        } else {
            btn.dataset.loading = '0';
            btn.disabled = false;
            if (btn.dataset.originalHtml) btn.innerHTML = btn.dataset.originalHtml;
        }
    }

    function bindOtpInputs(otpInputs, onEnterOrComplete) {
        const inputs = Array.from(otpInputs || []);
        if (!inputs.length) return;
        if (inputs[0]?.dataset?.otpBound === '1') return;
        if (inputs[0]) inputs[0].dataset.otpBound = '1';

        function setAt(idx, val) {
            if (!inputs[idx]) return;
            inputs[idx].value = val;
        }

        function focusAt(idx) {
            if (!inputs[idx]) return;
            inputs[idx].focus();
            try { inputs[idx].select(); } catch { /* ignore */ }
        }

        inputs.forEach((inp, i) => {
            inp.addEventListener('input', function () {
                const raw = String(this.value || '');
                const digit = raw.replace(/\D/g, '').slice(-1);
                this.value = digit;
                if (digit && i < inputs.length - 1) focusAt(i + 1);
                if (inputs.every(x => (x.value || '').match(/^\d$/))) {
                    if (typeof onEnterOrComplete === 'function') onEnterOrComplete();
                }
            });
            inp.addEventListener('keydown', function (e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    if (typeof onEnterOrComplete === 'function') onEnterOrComplete();
                    return;
                }
                if (e.key === 'Backspace') {
                    if (this.value) {
                        this.value = '';
                        return;
                    }
                    if (i > 0) focusAt(i - 1);
                    return;
                }
                if (e.key === 'ArrowLeft' && i > 0) { e.preventDefault(); focusAt(i - 1); return; }
                if (e.key === 'ArrowRight' && i < inputs.length - 1) { e.preventDefault(); focusAt(i + 1); return; }
            });
            inp.addEventListener('paste', function (e) {
                const text = (e.clipboardData || window.clipboardData)?.getData('text') || '';
                const digits = text.replace(/\D/g, '').slice(0, inputs.length);
                if (!digits) return;
                e.preventDefault();
                digits.split('').forEach((d, idx) => setAt(idx, d));
                focusAt(Math.min(digits.length, inputs.length - 1));
                if (digits.length === inputs.length && typeof onEnterOrComplete === 'function') onEnterOrComplete();
            });
        });
    }

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
        const otpStep = document.getElementById('login-otp-step');
        const otpEmailDisplay = document.getElementById('login-otp-email-display');
        const otpInputs = document.querySelectorAll('#login-otp-inputs [data-otp-digit]');
        const otpMessage = document.getElementById('login-otp-message');
        const otpResend = document.getElementById('login-otp-resend');
        const otpSubmit = document.getElementById('login-otp-submit');
        const haveAccount = document.getElementById('login-have-account');

        let loginEmail = null;
        let loginRedirect = '/dashboard';

        function showLoginOtpStep(email, redirect) {
            loginEmail = email;
            loginRedirect = redirect || '/dashboard';
            form.classList.add('hidden');
            if (haveAccount) haveAccount.classList.add('hidden');
            otpEmailDisplay.textContent = 'Code sent to ' + email;
            otpStep.classList.remove('hidden');
            otpInputs.forEach(inp => { inp.value = ''; });
            otpInputs[0]?.focus();
            otpMessage.classList.add('hidden');
            let s = 60;
            if (otpResend) {
                otpResend.disabled = true;
                otpResend.textContent = 'Resend code (' + s + 's)';
                const iv = setInterval(function () {
                    s--;
                    otpResend.textContent = 'Resend code (' + s + 's)';
                    if (s <= 0) {
                        clearInterval(iv);
                        otpResend.disabled = false;
                        otpResend.textContent = 'Resend code';
                    }
                }, 1000);
            }
        }

        function getLoginOtpValue() {
            return Array.from(otpInputs || []).map(inp => inp.value).join('');
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const email = form.querySelector('[name="email"]')?.value?.trim() || '';
            const password = form.querySelector('[name="password"]')?.value || '';
            const params = new URLSearchParams(window.location.search);
            const redirect = params.get('redirect') || '/dashboard';
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            apiFetch('/auth/login.php', { method: 'POST', body: JSON.stringify({ email, password, redirect }) })
                .then(data => {
                    if (data.data?.step === 'verify_otp') {
                        showLoginOtpStep(data.data.email, data.data.redirect);
                    } else {
                        showGlobalLoader();
                        window.location.href = data.data?.redirect || redirect;
                    }
                })
                .catch(err => {
                    showMessage(msgEl, err.message || 'Login failed. Try again.', true);
                    if (btn) btn.disabled = false;
                });
        });

        if (otpResend) otpResend.addEventListener('click', function () {
            if (!loginEmail || otpResend.disabled) return;
            otpResend.disabled = true;
            fetch(API_BASE + '/auth/send-otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ email: loginEmail, purpose: 'login' })
            }).then(r => r.json()).then(function (res) {
                if (res.success) {
                    if (otpMessage) {
                        otpMessage.textContent = 'Code sent. Check your email.';
                        otpMessage.className = 'text-sm text-green-600';
                        otpMessage.classList.remove('hidden');
                    }
                    let s = 60;
                    otpResend.textContent = 'Resend code (' + s + 's)';
                    const iv = setInterval(function () {
                        s--;
                        otpResend.textContent = 'Resend code (' + s + 's)';
                        if (s <= 0) {
                            clearInterval(iv);
                            otpResend.disabled = false;
                            otpResend.textContent = 'Resend code';
                        }
                    }, 1000);
                } else {
                    if (otpMessage) {
                        otpMessage.textContent = res.error || 'Failed to resend';
                        otpMessage.className = 'text-sm text-red-500';
                        otpMessage.classList.remove('hidden');
                    }
                    otpResend.disabled = false;
                }
            }).catch(function () {
                if (otpMessage) {
                    otpMessage.textContent = 'Failed to resend. Try again.';
                    otpMessage.className = 'text-sm text-red-500';
                    otpMessage.classList.remove('hidden');
                }
                otpResend.disabled = false;
            });
        });

        if (otpSubmit) otpSubmit.addEventListener('click', function () {
            if (otpSubmit.dataset.loading === '1') return;
            const otp = getLoginOtpValue().replace(/\D/g, '');
            if (otp.length !== 6) {
                if (otpMessage) {
                    otpMessage.textContent = 'Please enter all 6 digits.';
                    otpMessage.className = 'text-sm text-red-500';
                    otpMessage.classList.remove('hidden');
                }
                return;
            }
            setButtonLoading(otpSubmit, true, 'Verifying...');
            if (otpMessage) {
                otpMessage.textContent = 'Verifying code...';
                otpMessage.className = 'text-sm text-slate-500';
                otpMessage.classList.remove('hidden');
            }
            fetch(API_BASE + '/auth/verify-login-otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ email: loginEmail, otp, redirect: loginRedirect })
            }).then(r => r.json()).then(function (res) {
                if (res.success) {
                    showGlobalLoader();
                    window.location.href = res.data?.redirect || loginRedirect;
                } else {
                    if (otpMessage) {
                        otpMessage.textContent = res.error || 'Invalid code. Try again.';
                        otpMessage.className = 'text-sm text-red-500';
                        otpMessage.classList.remove('hidden');
                    }
                    setButtonLoading(otpSubmit, false);
                }
            }).catch(function () {
                if (otpMessage) {
                    otpMessage.textContent = 'Verification failed. Try again.';
                    otpMessage.className = 'text-sm text-red-500';
                    otpMessage.classList.remove('hidden');
                }
                setButtonLoading(otpSubmit, false);
            });
        });

        bindOtpInputs(otpInputs, () => otpSubmit?.click());
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
        const otpStep = document.getElementById('register-otp-step');
        const thankYou = document.getElementById('register-thank-you');
        const otpEmailDisplay = document.getElementById('register-otp-email-display');
        const otpInputs = document.querySelectorAll('#register-otp-inputs [data-otp-digit]');
        const otpMessage = document.getElementById('register-otp-message');
        const otpResend = document.getElementById('register-otp-resend');
        const otpSubmit = document.getElementById('register-otp-submit');
        const haveAccount = document.getElementById('register-have-account');

        let registerEmail = null;

        function showOtpStep(email) {
            registerEmail = email;
            form.classList.add('hidden');
            if (haveAccount) haveAccount.classList.add('hidden');
            otpEmailDisplay.textContent = 'Code sent to ' + email;
            otpStep.classList.remove('hidden');
            otpInputs.forEach(inp => { inp.value = ''; });
            otpInputs[0]?.focus();
            otpMessage.classList.add('hidden');
            startResendCooldown();
        }

        function startResendCooldown() {
            otpResend.disabled = true;
            let s = 60;
            otpResend.textContent = 'Resend code (' + s + 's)';
            const iv = setInterval(function () {
                s--;
                otpResend.textContent = 'Resend code (' + s + 's)';
                if (s <= 0) {
                    clearInterval(iv);
                    otpResend.disabled = false;
                    otpResend.textContent = 'Resend code';
                }
            }, 1000);
        }

        function getOtpValue() {
            return Array.from(otpInputs).map(inp => inp.value).join('');
        }

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const name = form.querySelector('[name="name"]')?.value?.trim() || '';
            const email = form.querySelector('[name="email"]')?.value?.trim() || '';
            const password = form.querySelector('[name="password"]')?.value || '';
            const confirm = form.querySelector('[name="confirm_password"]')?.value || '';
            const phone = form.querySelector('[name="phone"]')?.value?.trim() || '';
            const referral = form.querySelector('[name="referral"]')?.value?.trim() || '';
            const termsCheckbox = document.getElementById('terms');
            const avatarInput = form.querySelector('[name="avatar"]');
            const hasAvatar = avatarInput?.files?.length && avatarInput.files[0];
            if (!name) {
                showMessage(msgEl, 'Full name is required.', true);
                return;
            }
            if (!termsCheckbox || !termsCheckbox.checked) {
                showMessage(msgEl, 'You must agree to the Terms of Service and Privacy Policy to continue.', true);
                return;
            }
            if (password !== confirm) {
                showMessage(msgEl, 'Passwords do not match.', true);
                return;
            }
            const btn = form.querySelector('button[type="submit"]');
            if (btn) btn.disabled = true;
            const doRequest = (body) => {
                const opts = { method: 'POST' };
                if (typeof body === 'string') {
                    opts.headers = { 'Content-Type': 'application/json' };
                    opts.body = body;
                } else {
                    opts.body = body;
                    opts.headers = { 'Accept': 'application/json' };
                }
                return fetch(API_BASE + '/auth/register.php', { ...opts, credentials: 'same-origin' })
                    .then(r => r.json().then(data => ({ ok: r.ok, data })))
                    .then(({ ok, data }) => {
                        if (!ok) throw new Error(data.error || 'Request failed');
                        return data;
                    });
            };
            const onSuccess = (data) => {
                if (data.data?.step === 'verify_otp') {
                    showOtpStep(data.data.email);
                } else {
                    showGlobalLoader();
                    window.location.href = data.data?.redirect || '/dashboard';
                }
            };
            if (hasAvatar) {
                const fd = new FormData(form);
                fd.delete('confirm_password');
                fd.delete('terms');
                doRequest(fd).then(onSuccess).catch(err => {
                    showMessage(msgEl, err.message || 'Registration failed. Try again.', true);
                    if (btn) btn.disabled = false;
                });
            } else {
                doRequest(JSON.stringify({ name, email, password, phone: phone || undefined, referral: referral || undefined }))
                    .then(onSuccess).catch(err => {
                        showMessage(msgEl, err.message || 'Registration failed. Try again.', true);
                        if (btn) btn.disabled = false;
                    });
            }
        });

        if (otpResend) otpResend.addEventListener('click', function () {
            if (!registerEmail || otpResend.disabled) return;
            otpResend.disabled = true;
            fetch(API_BASE + '/auth/send-otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ email: registerEmail, purpose: 'register' })
            }).then(r => r.json()).then(function (res) {
                if (res.success) {
                    otpMessage.textContent = 'Code sent. Check your email.';
                    otpMessage.className = 'text-sm text-green-600';
                    otpMessage.classList.remove('hidden');
                    startResendCooldown();
                } else {
                    otpMessage.textContent = res.error || 'Failed to resend';
                    otpMessage.className = 'text-sm text-red-500';
                    otpMessage.classList.remove('hidden');
                    otpResend.disabled = false;
                }
            }).catch(function () {
                otpMessage.textContent = 'Failed to resend. Try again.';
                otpMessage.className = 'text-sm text-red-500';
                otpMessage.classList.remove('hidden');
                otpResend.disabled = false;
            });
        });

        if (otpSubmit) otpSubmit.addEventListener('click', function () {
            if (otpSubmit.dataset.loading === '1') return;
            const otp = getOtpValue().replace(/\D/g, '');
            if (otp.length !== 6) {
                otpMessage.textContent = 'Please enter all 6 digits.';
                otpMessage.className = 'text-sm text-red-500';
                otpMessage.classList.remove('hidden');
                return;
            }
            setButtonLoading(otpSubmit, true, 'Verifying...');
            otpMessage.textContent = 'Verifying code...';
            otpMessage.className = 'text-sm text-slate-500';
            otpMessage.classList.remove('hidden');
            fetch(API_BASE + '/auth/verify-registration-otp.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                credentials: 'same-origin',
                body: JSON.stringify({ email: registerEmail, otp })
            }).then(r => r.json()).then(function (res) {
                if (res.success) {
                    otpStep.classList.add('hidden');
                    thankYou.classList.remove('hidden');
                    setTimeout(function () {
                        showGlobalLoader();
                        window.location.href = res.data?.redirect || '/dashboard';
                    }, 1500);
                } else {
                    otpMessage.textContent = res.error || 'Invalid code. Try again.';
                    otpMessage.className = 'text-sm text-red-500';
                    otpMessage.classList.remove('hidden');
                    setButtonLoading(otpSubmit, false);
                }
            }).catch(function () {
                otpMessage.textContent = 'Verification failed. Try again.';
                otpMessage.className = 'text-sm text-red-500';
                otpMessage.classList.remove('hidden');
                setButtonLoading(otpSubmit, false);
            });
        });

        bindOtpInputs(otpInputs, () => otpSubmit?.click());
    }

    function initPasswordToggle() {
        document.addEventListener('click', function (e) {
            const btn = e.target.closest('[data-password-toggle]');
            if (!btn) return;
            e.preventDefault();
            e.stopPropagation();
            const wrap = btn.closest('.relative') || btn.parentElement;
            if (!wrap) return;
            let inp = wrap.querySelector('input[type="password"]') || wrap.querySelector('input[type="text"]') || wrap.querySelector('input');
            if (!inp) inp = btn.previousElementSibling;
            if (!inp || inp.tagName !== 'INPUT') return;
            const icon = btn.querySelector('.material-icons, .material-symbols-outlined, [class*="material"]');
            const iconEl = icon || btn;
            const isHidden = inp.type === 'password';
            inp.type = isHidden ? 'text' : 'password';
            iconEl.textContent = isHidden ? 'visibility_off' : 'visibility';
        }, true);
    }

    function initLogoutButtons() {
        document.querySelectorAll('[data-logout]').forEach(btn => {
            btn.addEventListener('click', function (e) {
                e.preventDefault();
                apiFetch('/auth/logout.php', { method: 'POST' })
                    .then(() => { showGlobalLoader(); window.location.href = '/'; })
                    .catch(() => { showGlobalLoader(); window.location.href = '/'; });
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
        initGlobalLoader();
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
