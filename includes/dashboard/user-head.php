<?php
/**
 * Shared <head> for user dashboard pages. Set $pageTitle before including layout-start.
 */
$pageTitle = $pageTitle ?? (get_site_name() . ' | Dashboard');
?>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php if (function_exists('output_favicon_tags')) { output_favicon_tags(); } ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;600;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "surface-dim": "#111417",
        "primary-container": "#ffc35c",
        "on-surface": "#e1e2e7",
        "text-secondary": "#A0A7B4",
        "text-primary": "#FFFFFF",
        "on-surface-variant": "#d4c4b0",
        "surface-container-low": "#191c1f",
        "surface-container-high": "#272a2e",
        "surface-container": "#1d2023",
        "surface-container-highest": "#323538",
        "surface-container-lowest": "#0b0e11",
        "border-low": "rgba(255, 255, 255, 0.08)",
        "primary": "#ffe6c3",
        "primary-fixed-dim": "#f8bc56",
        "on-primary": "#432c00",
        "on-primary-container": "#755000",
        "success": "#20B26C",
        "critical": "#EF454A",
        "bg-subtle": "#161B22",
        "surface-bright": "#37393d",
        "surface": "#111417"
      },
      borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
      spacing: {
        "margin-desktop": "32px",
        "margin-mobile": "16px",
        "gutter": "16px",
        "container-max": "1440px"
      },
      fontFamily: {
        "headline-lg": ["Plus Jakarta Sans", "sans-serif"],
        "headline-md": ["Plus Jakarta Sans", "sans-serif"],
        "body-md": ["Inter", "sans-serif"],
        "body-lg": ["Inter", "sans-serif"],
        "label-xs": ["Inter", "sans-serif"],
        "label-sm": ["Inter", "sans-serif"],
        "data-mono": ["Inter", "sans-serif"],
        "display": ["Plus Jakarta Sans", "sans-serif"]
      },
      fontSize: {
        "headline-lg": ["32px", {"lineHeight": "1.2", "fontWeight": "700"}],
        "headline-md": ["24px", {"lineHeight": "1.3", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "1.5", "fontWeight": "400"}],
        "body-lg": ["18px", {"lineHeight": "1.6", "fontWeight": "400"}],
        "label-xs": ["12px", {"lineHeight": "1", "letterSpacing": "0.1em", "fontWeight": "800"}],
        "label-sm": ["14px", {"lineHeight": "1", "letterSpacing": "0.05em", "fontWeight": "700"}],
        "data-mono": ["16px", {"lineHeight": "1", "letterSpacing": "-0.02em", "fontWeight": "500"}],
        "display": ["64px", {"lineHeight": "1.1", "letterSpacing": "-0.04em", "fontWeight": "800"}]
      }
    }
  }
};
</script>
<style>
body.user-dashboard {
  background-color: #0B0E11;
  color: #e1e2e7;
  font-family: 'Inter', sans-serif;
  overflow-x: hidden;
}
.font-headline { font-family: 'Plus Jakarta Sans', sans-serif; }
.glass-panel {
  background: rgba(30, 35, 41, 0.8);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.balance-gradient-card {
  background: linear-gradient(135deg, #0f172a 0%, #1e293b 45%, #020617 100%);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.chart-gradient, .trading-graph-bg {
  background: linear-gradient(180deg, rgba(255, 195, 92, 0.15) 0%, rgba(255, 195, 92, 0) 100%);
}
.scanning-animation {
  background: linear-gradient(90deg, transparent 0%, rgba(255, 195, 92, 0.1) 50%, transparent 100%);
  background-size: 200% 100%;
  animation: dash-scan 2s infinite linear;
}
@keyframes dash-scan {
  from { background-position: 200% 0; }
  to { background-position: -200% 0; }
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  vertical-align: middle;
}
.dash-scrollbar::-webkit-scrollbar { width: 4px; }
.dash-scrollbar::-webkit-scrollbar-track { background: transparent; }
.dash-scrollbar::-webkit-scrollbar-thumb { background: rgba(255,195,92,0.25); border-radius: 10px; }
.user-dash-main {
  padding-top: calc(5rem + env(safe-area-inset-top, 0px));
  box-sizing: border-box;
}
.user-dash-content {
  max-width: 1440px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
.user-topbar {
  padding-top: env(safe-area-inset-top, 0px);
  min-height: calc(4rem + env(safe-area-inset-top, 0px));
  height: calc(4rem + env(safe-area-inset-top, 0px));
}
</style>
<?php if (!empty($pageExtraStyles)) { echo $pageExtraStyles; } ?>
