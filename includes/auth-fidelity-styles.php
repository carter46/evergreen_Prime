<style>
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  display: inline-block;
  vertical-align: middle;
}
.fidelity-input-focus:focus,
.input-focus-ring:focus {
  outline: none;
  border-color: #337722;
  box-shadow: 0 0 0 1px rgba(51, 119, 34, 0.35);
}
.auth-fidelity-page {
  background: linear-gradient(165deg, #eef6eb 0%, #f7f9ff 42%, #f3faf0 100%);
  font-family: 'Inter', sans-serif;
}
.auth-hero-title {
  font-size: 1.375rem;
  line-height: 1.3;
  font-weight: 700;
  letter-spacing: -0.02em;
}
@media (min-width: 640px) {
  .auth-hero-title { font-size: 1.625rem; }
}
@media (min-width: 1024px) {
  .auth-hero-title { font-size: 1.875rem; }
}

/* Glass card with green tint — not solid green */
.auth-form-card {
  position: relative;
  overflow: hidden;
  background: rgba(255, 255, 255, 0.78);
  backdrop-filter: blur(18px);
  -webkit-backdrop-filter: blur(18px);
  border: 1px solid rgba(51, 119, 34, 0.22);
  padding: 1.25rem;
  border-radius: 0.75rem;
  box-shadow:
    0 10px 36px rgba(51, 119, 34, 0.1),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
}
.auth-form-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: linear-gradient(145deg, rgba(51, 119, 34, 0.07) 0%, transparent 55%, rgba(51, 119, 34, 0.04) 100%);
  pointer-events: none;
}
.auth-form-card > * {
  position: relative;
  z-index: 1;
}
@media (min-width: 768px) {
  .auth-form-card { padding: 1.5rem; }
}

/* CRITICAL: don't let flex layout override Tailwind hidden */
.auth-form-card .hidden,
.auth-form-card form.hidden {
  display: none !important;
}

.auth-form-card .auth-form-intro h2 {
  color: #1f4d16;
  font-size: 1.375rem;
  line-height: 1.3;
  font-weight: 600;
  margin-bottom: 0.375rem;
}
.auth-form-card .auth-form-intro p {
  color: #4b5563;
  font-size: 0.875rem;
  line-height: 1.4;
  margin-bottom: 0.875rem;
}
.auth-form-card .auth-field-label {
  color: #181c20 !important;
  font-size: 0.6875rem;
  font-weight: 600;
  letter-spacing: 0.05em;
  text-transform: uppercase;
}
.auth-form-card .auth-form-note,
.auth-form-card .auth-form-step-label {
  color: #4d6b45;
  font-size: 0.6875rem;
}
.auth-form-card .auth-terms-label {
  color: #374151;
  font-size: 0.8125rem;
}
.auth-form-card .auth-terms-label a {
  color: #337722;
  text-decoration: underline;
}
.auth-form-input {
  width: 100%;
  background: rgba(255, 255, 255, 0.95);
  border: 1px solid rgba(51, 119, 34, 0.18);
  border-radius: 0.5rem;
  padding: 0.4375rem 0.75rem;
  font-size: 0.875rem;
  line-height: 1.25rem;
  min-height: 2.125rem;
  color: #181c20;
}
.auth-form-input.auth-form-input-icon {
  padding-left: 2.5rem;
}
.auth-form-input.auth-form-input-toggle {
  padding-right: 2.5rem;
}
.auth-form-card .auth-form-stack:not(.hidden) {
  display: flex;
  flex-direction: column;
  gap: 0.625rem;
}
.auth-form-card .auth-form-stack-tight {
  display: flex;
  flex-direction: column;
  gap: 0.5rem;
}
.auth-form-card .auth-upload-box {
  background: rgba(255, 255, 255, 0.92);
  border-color: rgba(51, 119, 34, 0.25);
  padding: 0.625rem;
}
.auth-form-card .auth-progress-track {
  background: rgba(51, 119, 34, 0.15);
}
.auth-form-card .auth-progress-fill {
  background: #337722;
}
.auth-form-card .auth-btn-primary {
  background: #337722;
  color: #ffffff;
  font-weight: 700;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.9375rem;
  min-height: 2.25rem;
  border: none;
  cursor: pointer;
}
.auth-form-card .auth-btn-primary:hover {
  background: #285e1b;
}
.auth-form-card .auth-btn-outline {
  background: rgba(255, 255, 255, 0.6);
  color: #337722;
  border: 1px solid rgba(51, 119, 34, 0.45);
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.875rem;
  min-height: 2.25rem;
  cursor: pointer;
}
.auth-form-card .auth-btn-outline:hover {
  background: rgba(51, 119, 34, 0.08);
}
.auth-form-card .auth-link-accent {
  color: #337722;
  text-decoration: underline;
  font-weight: 600;
}
.auth-form-card .auth-link-accent:hover {
  color: #285e1b;
}
.auth-form-card .auth-divider {
  border-color: rgba(51, 119, 34, 0.15);
}
.auth-form-card .auth-footer-note {
  color: #4d6b45;
  background: rgba(51, 119, 34, 0.08);
  border: 1px solid rgba(51, 119, 34, 0.12);
  border-radius: 0.5rem;
  padding: 0.5rem 0.625rem;
  font-size: 0.8125rem;
}
.auth-form-card .auth-otp-title {
  color: #1f4d16;
  font-size: 1.125rem;
  font-weight: 600;
}
.auth-form-card .auth-otp-text {
  color: #4b5563;
  font-size: 0.875rem;
}
.auth-form-card .auth-form-message {
  font-size: 0.875rem;
}
.auth-form-card .auth-form-message.is-error {
  color: #b91c1c;
}
.auth-form-card .auth-form-message.is-success {
  color: #15803d;
}
.auth-form-card .auth-register-cta {
  color: #4b5563;
}
.auth-form-card .auth-register-cta a {
  display: block;
  width: 100%;
  text-align: center;
  border: 1px solid rgba(51, 119, 34, 0.45);
  color: #337722;
  padding: 0.5rem 1rem;
  border-radius: 0.5rem;
  font-size: 0.75rem;
  font-weight: 600;
  background: rgba(255, 255, 255, 0.55);
}
.auth-form-card .auth-register-cta a:hover {
  background: rgba(51, 119, 34, 0.08);
}
.auth-form-card .auth-icon-badge {
  background: rgba(51, 119, 34, 0.12);
  color: #337722;
  border: 1px solid rgba(51, 119, 34, 0.18);
}
.auth-form-card .auth-timeout-note {
  font-size: 0.875rem;
  color: #1f4d16;
  background: rgba(51, 119, 34, 0.1);
  border: 1px solid rgba(51, 119, 34, 0.2);
  padding: 0.5rem 0.75rem;
  border-radius: 0.5rem;
}
</style>
