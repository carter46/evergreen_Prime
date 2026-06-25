<?php
/**
 * Shared marketing page head assets (fonts, Tailwind config, base styles).
 * Include inside <head> on marketing pages.
 */
$pageTitle = $pageTitle ?? get_site_name();
?>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php output_favicon_tags(); ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "primary": "#ffe6c3",
        "surface-container-low": "#191c1f",
        "inverse-primary": "#7e5700",
        "surface-dim": "#111417",
        "surface-container-lowest": "#0b0e11",
        "on-secondary-container": "#b1b5bd",
        "surface-bright": "#37393d",
        "surface-container-high": "#272a2e",
        "on-tertiary": "#003544",
        "outline": "#9c8f7d",
        "surface-variant": "#323538",
        "on-background": "#e1e2e7",
        "secondary-fixed-dim": "#c2c7cf",
        "on-tertiary-fixed-variant": "#004d61",
        "on-error": "#690005",
        "on-surface": "#e1e2e7",
        "inverse-surface": "#e1e2e7",
        "primary-container": "#ffc35c",
        "error-container": "#93000a",
        "text-secondary": "#A0A7B4",
        "on-secondary-fixed-variant": "#42474e",
        "background": "#111417",
        "on-primary-fixed": "#281900",
        "surface-container-highest": "#323538",
        "on-primary": "#432c00",
        "on-error-container": "#ffdad6",
        "on-tertiary-container": "#005f76",
        "on-secondary-fixed": "#171c22",
        "error": "#ffb4ab",
        "primary-fixed-dim": "#f8bc56",
        "inverse-on-surface": "#2e3134",
        "border-low": "rgba(255, 255, 255, 0.08)",
        "secondary-fixed": "#dee3eb",
        "secondary": "#c2c7cf",
        "primary-fixed": "#ffdead",
        "tertiary-fixed-dim": "#6ed3f7",
        "bg-subtle": "#161B22",
        "success": "#20B26C",
        "on-secondary": "#2c3137",
        "on-surface-variant": "#d4c4b0",
        "secondary-container": "#42474e",
        "critical": "#EF454A",
        "outline-variant": "#504536",
        "on-tertiary-fixed": "#001f28",
        "text-primary": "#FFFFFF",
        "on-primary-fixed-variant": "#604100",
        "tertiary-fixed": "#b8eaff",
        "surface": "#111417",
        "tertiary": "#cbefff",
        "on-primary-container": "#755000",
        "surface-container": "#1d2023",
        "tertiary-container": "#75dafe",
        "surface-tint": "#f8bc56"
      },
      borderRadius: {
        "DEFAULT": "0.125rem",
        "lg": "0.25rem",
        "xl": "0.5rem",
        "full": "0.75rem"
      },
      spacing: {
        "container-max": "1440px",
        "section-padding": "96px",
        "unit": "4px",
        "margin-desktop": "32px",
        "margin-mobile": "16px",
        "gutter": "16px"
      },
      fontFamily: {
        "label-sm": ["Inter", "sans-serif"],
        "body-lg": ["Inter", "sans-serif"],
        "data-mono": ["Inter", "sans-serif"],
        "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
        "headline-md": ["Plus Jakarta Sans", "sans-serif"],
        "body-md": ["Inter", "sans-serif"],
        "headline-lg-mobile": ["Plus Jakarta Sans", "sans-serif"],
        "label-xs": ["Inter", "sans-serif"],
        "display": ["Plus Jakarta Sans", "sans-serif"]
      },
      fontSize: {
        "label-sm": ["14px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}],
        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
        "data-mono": ["16px", {"lineHeight": "1", "letterSpacing": "-0.02em", "fontWeight": "500"}],
        "headline-lg": ["32px", {"lineHeight": "1.2", "fontWeight": "700"}],
        "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
        "headline-lg-mobile": ["40px", {"lineHeight": "1.1", "fontWeight": "800"}],
        "label-xs": ["12px", {"lineHeight": "1", "letterSpacing": "0.1em", "fontWeight": "800"}],
        "display": ["64px", {"lineHeight": "1.1", "letterSpacing": "-0.04em", "fontWeight": "800"}]
      }
    }
  }
}
</script>
<style>
.glass-panel {
  background: rgba(30, 35, 41, 0.8);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.text-glow {
  text-shadow: 0 0 20px rgba(255, 195, 92, 0.3);
}
.hero-gradient {
  background: radial-gradient(circle at top right, rgba(255, 195, 92, 0.1), transparent 50%),
              radial-gradient(circle at bottom left, rgba(17, 20, 23, 1), transparent 80%);
}
body.marketing-page {
  background-color: #0b0e11;
  color: #e1e2e7;
  -webkit-font-smoothing: antialiased;
}
.market-ticker-scroll {
  animation: ticker 30s linear infinite;
}
@keyframes ticker {
  0% { transform: translateX(0); }
  100% { transform: translateX(-50%); }
}
.btn-get-started {
  background: linear-gradient(135deg, #0a3d22 0%, #145c36 45%, #20B26C 100%);
  color: #ffffff;
  box-shadow: 0 4px 14px rgba(32, 178, 108, 0.35);
}
.btn-get-started:hover {
  background: linear-gradient(135deg, #0d4a2a 0%, #187a48 45%, #24c97a 100%);
  box-shadow: 0 6px 20px rgba(32, 178, 108, 0.45);
}
</style>
