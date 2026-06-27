<?php
/**
 * Shared marketing page head assets (fonts, Tailwind config, base styles).
 * Include inside <head> on marketing pages.
 */
$pageTitle = $pageTitle ?? get_site_name();
?>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php output_favicon_tags(); ?>
<?php output_site_brand_meta_tags(); ?>
<?php require_once __DIR__ . '/pwa-head.php'; ?>
<?php if (!defined('BB_TV_MINI_CHART_SCRIPT')) { define('BB_TV_MINI_CHART_SCRIPT', true); ?>
<script type="module" src="https://widgets.tradingview-widget.com/w/en/tv-mini-chart.js"></script>
<?php } ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@400;600;700&amp;family=Inter:wght@400;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        fidelityGreen: '#337722',
        fidelityGreenHover: '#285e1b',
        fidelityLightGreen: '#f4f9f2',
        fidelityGray: '#666666',
        fidelityDark: '#333333',
        'fidelity-green': '#337722',
        'on-tertiary-fixed': '#3d0023',
        'on-primary': '#ffffff',
        'on-primary-container': '#b1fd96',
        'on-secondary-fixed': '#001e2f',
        'on-primary-fixed': '#022100',
        'inverse-primary': '#8fd977',
        'surface-container-highest': '#dfe3e8',
        'background': '#f7f9ff',
        'primary-fixed-dim': '#8fd977',
        'institutional-blue': '#0078AE',
        'primary-fixed': '#aaf690',
        'surface-bright': '#f7f9ff',
        'inverse-surface': '#2d3135',
        'error': '#ba1a1a',
        'on-background': '#181c20',
        'surface-container-high': '#e5e8ee',
        'surface': '#f7f9ff',
        'on-tertiary-container': '#ffe2eb',
        'on-tertiary-fixed-variant': '#7e2253',
        'tertiary-fixed': '#ffd8e6',
        'surface-container-low': '#f1f4fa',
        'outline-variant': '#c0c9b8',
        'inverse-on-surface': '#eef1f7',
        'surface-variant': '#dfe3e8',
        'surface-container-lowest': '#ffffff',
        'outline': '#717a6b',
        'on-secondary-container': '#005076',
        'deep-onyx': '#000000',
        'primary': '#185e08',
        'secondary-fixed-dim': '#8cceff',
        'on-surface': '#181c20',
        'tertiary-container': '#a94576',
        'tertiary-fixed-dim': '#ffb0d0',
        'secondary-fixed': '#cae6ff',
        'tertiary': '#8b2d5d',
        'primary-container': '#337722',
        'surface-tint': '#286c18',
        'surface-gray': '#E9E9E9',
        'on-secondary-fixed-variant': '#004b6f',
        'surface-dim': '#d7dae0',
        'error-container': '#ffdad6',
        'secondary-container': '#70c4fe',
        'on-error': '#ffffff',
        'on-primary-fixed-variant': '#0d5300',
        'surface-container': '#ebeef4',
        'on-secondary': '#ffffff',
        'on-surface-variant': '#41493c',
        'secondary': '#006492',
        'on-error-container': '#93000a',
        'on-tertiary': '#ffffff',
        'border-low': '#e5e7eb',
        'text-primary': '#181c20',
        'text-secondary': '#6b7280',
        'text-text-primary': '#181c20',
        'text-text-secondary': '#6b7280',
        'bg-subtle': '#f3f4f6',
        'success': '#337722'
      },
      borderRadius: {
        DEFAULT: '0.125rem',
        lg: '0.25rem',
        xl: '0.5rem',
        full: '0.75rem'
      },
      spacing: {
        xs: '8px',
        md: '24px',
        'margin-mobile': '16px',
        xl: '64px',
        sm: '16px',
        'margin-desktop': '48px',
        base: '4px',
        lg: '40px',
        gutter: '24px',
        'container-max': '1440px',
        'section-padding': '96px',
        unit: '4px'
      },
      fontFamily: {
        sans: ['Helvetica', 'Arial', 'sans-serif'],
        serif: ['Georgia', 'serif'],
        'display-lg': ['Hanken Grotesk'],
        'headline-lg-mobile': ['Hanken Grotesk'],
        'label-md': ['Inter'],
        'headline-md': ['Hanken Grotesk'],
        'body-md': ['Inter'],
        'headline-lg': ['Hanken Grotesk'],
        'body-sm': ['Inter'],
        'body-lg': ['Inter'],
        'display': ['Hanken Grotesk', 'sans-serif'],
        'label-sm': ['Inter', 'sans-serif'],
        'label-xs': ['Inter', 'sans-serif']
      },
      fontSize: {
        'display-lg': ['48px', { lineHeight: '56px', letterSpacing: '-0.02em', fontWeight: '700' }],
        'headline-lg-mobile': ['28px', { lineHeight: '36px', fontWeight: '600' }],
        'label-md': ['12px', { lineHeight: '16px', letterSpacing: '0.05em', fontWeight: '600' }],
        'headline-md': ['24px', { lineHeight: '32px', fontWeight: '600' }],
        'body-md': ['16px', { lineHeight: '24px', fontWeight: '400' }],
        'headline-lg': ['32px', { lineHeight: '40px', fontWeight: '600' }],
        'body-sm': ['14px', { lineHeight: '20px', fontWeight: '400' }],
        'body-lg': ['18px', { lineHeight: '28px', fontWeight: '400' }],
        'display': ['64px', { lineHeight: '1.1', letterSpacing: '-0.04em', fontWeight: '800' }],
        'label-sm': ['14px', { lineHeight: '1', letterSpacing: '0.05em', fontWeight: '700' }],
        'label-xs': ['12px', { lineHeight: '1', letterSpacing: '0.1em', fontWeight: '800' }]
      }
    }
  }
}
</script>
<style data-purpose="custom-typography">
@font-face {
  font-family: 'Site Sans';
  src: local('Arial');
}
.fidelity-homepage h1,
.fidelity-homepage h2,
.fidelity-homepage h3 {
  font-family: 'Georgia', serif;
  color: #333;
}
.fidelity-subpage h1,
.fidelity-subpage h2,
.fidelity-subpage h3,
.fidelity-subpage h4 {
  font-family: inherit;
  color: inherit;
}
body.marketing-page {
  background-color: #ffffff;
  color: #333333;
  -webkit-font-smoothing: antialiased;
}
.border-border-low {
  border-color: #e5e7eb;
}
.btn-get-started {
  background: #337722;
  color: #ffffff;
  box-shadow: 0 2px 8px rgba(51, 119, 34, 0.2);
}
.btn-get-started:hover {
  background: #285e1b;
}
.pulse-live {
  box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7);
  animation: pulse-red 2s infinite;
}
@keyframes pulse-red {
  0% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); }
  70% { transform: scale(1); box-shadow: 0 0 0 10px rgba(239, 68, 68, 0); }
  100% { transform: scale(0.95); box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
}
.text-glow {
  text-shadow: none;
}
.market-hero-glow {
  background: radial-gradient(ellipse 80% 60% at 20% 50%, rgba(51, 119, 34, 0.08), transparent);
}
.market-ticker-scroll {
  animation: ticker 30s linear infinite;
}
@keyframes ticker {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.fidelity-nav-text {
  font-size: 0.85rem;
  font-weight: 500;
}
/* overflow-x: hidden on body breaks position:sticky */
body.overflow-x-hidden {
  overflow-x: clip !important;
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  vertical-align: middle;
}
.bento-grid {
  display: grid;
  grid-template-columns: repeat(12, 1fr);
  gap: 24px;
}
.glass-card {
  background: rgba(255, 255, 255, 0.8);
  backdrop-filter: blur(12px);
  border: 1px solid #E9E9E9;
}
.hover-lift {
  transition: transform 0.3s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.3s ease;
}
.hover-lift:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.hero-gradient {
  background: linear-gradient(90deg, #f7f9ff 0%, #ffffff 100%);
}
.fidelity-header-green {
  background-color: #337722;
}
.bento-card {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  border: 1px solid #E9E9E9;
}
.bento-card:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  transform: translateY(-2px);
}
.card-shadow {
  border: 1px solid #E9E9E9;
  transition: all 0.3s ease;
}
.card-shadow:hover {
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
}
.max-content {
  max-width: 1152px;
  margin-left: auto;
  margin-right: auto;
}
#bb-global-loader,
#bb-global-loader-style {
  display: none !important;
  visibility: hidden !important;
  pointer-events: none !important;
}
</style>
<script>
(function () {
  function stripLoader() {
    var el = document.getElementById('bb-global-loader');
    var style = document.getElementById('bb-global-loader-style');
    if (el) el.remove();
    if (style) style.remove();
  }
  stripLoader();
  document.addEventListener('DOMContentLoaded', stripLoader);
  if (typeof MutationObserver !== 'undefined' && document.documentElement) {
    new MutationObserver(stripLoader).observe(document.documentElement, { childList: true, subtree: true });
  }
})();
</script>
