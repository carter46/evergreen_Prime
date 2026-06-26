<?php
/**
 * Shared head assets for auth pages (login, register, forgot/reset password).
 * Set $pageTitle before including.
 */
$pageTitle = $pageTitle ?? get_site_name();
?>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php output_favicon_tags(); ?>
<?php require_once __DIR__ . '/pwa-head.php'; ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
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
        "on-surface": "#e1e2e7",
        "primary-container": "#ffc35c",
        "text-secondary": "#A0A7B4",
        "on-primary": "#432c00",
        "border-low": "rgba(255, 255, 255, 0.08)",
        "background": "#111417",
        "on-primary-fixed": "#281900",
        "primary-fixed-dim": "#f8bc56",
        "on-surface-variant": "#d4c4b0",
        "on-secondary-fixed-variant": "#42474e",
        "bg-subtle": "#161B22",
        "success": "#20B26C",
        "outline-variant": "#504536",
        "text-primary": "#FFFFFF",
        "surface": "#111417",
        "on-primary-container": "#755000",
        "surface-container": "#1d2023",
        "tertiary-container": "#75dafe",
        "critical": "#EF454A"
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
        "margin-desktop": "32px",
        "margin-mobile": "16px",
        "gutter": "16px"
      },
      fontFamily: {
        "label-sm": ["Inter", "sans-serif"],
        "body-lg": ["Inter", "sans-serif"],
        "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
        "headline-md": ["Plus Jakarta Sans", "sans-serif"],
        "body-md": ["Inter", "sans-serif"],
        "label-xs": ["Inter", "sans-serif"],
        "display": ["Plus Jakarta Sans", "sans-serif"]
      },
      fontSize: {
        "label-sm": ["14px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}],
        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
        "headline-lg": ["32px", {"lineHeight": "1.2", "fontWeight": "700"}],
        "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
        "label-xs": ["12px", {"lineHeight": "1", "letterSpacing": "0.1em", "fontWeight": "800"}],
        "display": ["64px", {"lineHeight": "1.1", "letterSpacing": "-0.04em", "fontWeight": "800"}]
      }
    }
  }
}
</script>
<style>
body.auth-page {
  background-color: #0b0e11;
  color: #e1e2e7;
  -webkit-font-smoothing: antialiased;
}
.auth-glass-card {
  background: rgba(30, 35, 41, 0.85);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.auth-glass-panel {
  background: rgba(22, 27, 34, 0.75);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.auth-input {
  width: 100%;
  background: #161B22;
  border: 1px solid rgba(80, 69, 54, 0.3);
  border-radius: 0.5rem;
  padding: 1rem 1rem 1rem 3rem;
  color: #e1e2e7;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.auth-input:focus {
  outline: none;
  border-color: #ffc35c;
  box-shadow: 0 0 0 2px rgba(255, 195, 92, 0.2);
}
.auth-input-wrap:focus-within {
  border-color: #ffc35c;
  box-shadow: 0 0 0 1px #ffc35c;
}
.auth-input-plain {
  background: transparent;
  border: none;
  padding: 0;
  width: 100%;
  color: #ffe6c3;
}
.auth-input-plain:focus { outline: none; box-shadow: none; }
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
}
@keyframes auth-glow {
  0%, 100% { opacity: 0.5; }
  50% { opacity: 0.85; }
}
.auth-glow { animation: auth-glow 4s ease-in-out infinite; }
</style>
