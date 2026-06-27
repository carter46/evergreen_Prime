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
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@300;400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        "surface-container": "#ebeef4",
        "tertiary-fixed-dim": "#ffb0d0",
        "tertiary-container": "#a94576",
        "outline-variant": "#c0c9b8",
        "deep-onyx": "#000000",
        "surface-container-high": "#e5e8ee",
        "background": "#f7f9ff",
        "surface-container-highest": "#dfe3e8",
        "tertiary-fixed": "#ffd8e6",
        "on-surface-variant": "#41493c",
        "surface-container-low": "#f1f4fa",
        "on-tertiary": "#ffffff",
        "secondary-fixed": "#cae6ff",
        "secondary-container": "#70c4fe",
        "surface-variant": "#dfe3e8",
        "secondary": "#006492",
        "surface-tint": "#286c18",
        "inverse-on-surface": "#eef1f7",
        "outline": "#717a6b",
        "on-error": "#ffffff",
        "inverse-primary": "#8fd977",
        "primary": "#185e08",
        "on-surface": "#181c20",
        "on-secondary-fixed-variant": "#004b6f",
        "primary-container": "#337722",
        "on-primary": "#ffffff",
        "surface": "#f7f9ff",
        "institutional-blue": "#0078AE",
        "on-background": "#181c20",
        "surface-container-lowest": "#ffffff",
        "on-secondary-container": "#005076",
        "inverse-surface": "#2d3135",
        "fidelity-green": "#337722",
        "surface-gray": "#E9E9E9",
        "on-tertiary-fixed": "#3d0023",
        "on-tertiary-container": "#ffe2eb",
        "on-primary-fixed-variant": "#0d5300",
        "on-error-container": "#93000a",
        "surface-dim": "#d7dae0",
        "error": "#ba1a1a",
        "surface-bright": "#f7f9ff",
        "primary-fixed": "#aaf690",
        "on-primary-container": "#b1fd96",
        "tertiary": "#8b2d5d",
        "primary-fixed-dim": "#8fd977",
        "on-secondary-fixed": "#001e2f",
        "on-primary-fixed": "#022100",
        "on-tertiary-fixed-variant": "#7e2253",
        "on-secondary": "#ffffff",
        "error-container": "#ffdad6",
        "secondary-fixed-dim": "#8cceff",
        "text-primary": "#181c20",
        "text-secondary": "#41493c",
        "success": "#337722",
        "critical": "#ba1a1a",
        "border-low": "#E9E9E9"
      },
      borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
      spacing: {
        "gutter": "24px",
        "lg": "40px",
        "md": "24px",
        "margin-mobile": "16px",
        "xl": "64px",
        "xs": "8px",
        "margin-desktop": "48px",
        "sm": "16px",
        "base": "4px",
        "container-max": "1152px"
      },
      fontFamily: {
        "headline-lg-mobile": ["Hanken Grotesk", "sans-serif"],
        "display-lg": ["Hanken Grotesk", "sans-serif"],
        "body-lg": ["Inter", "sans-serif"],
        "headline-lg": ["Hanken Grotesk", "sans-serif"],
        "body-sm": ["Inter", "sans-serif"],
        "label-md": ["Inter", "sans-serif"],
        "body-md": ["Inter", "sans-serif"],
        "headline-md": ["Hanken Grotesk", "sans-serif"],
        "label-xs": ["Inter", "sans-serif"],
        "label-sm": ["Inter", "sans-serif"],
        "data-mono": ["Inter", "sans-serif"],
        "display": ["Hanken Grotesk", "sans-serif"]
      },
      fontSize: {
        "headline-lg-mobile": ["28px", {"lineHeight": "36px", "fontWeight": "600"}],
        "display-lg": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}],
        "body-lg": ["18px", {"lineHeight": "28px", "fontWeight": "400"}],
        "headline-lg": ["32px", {"lineHeight": "40px", "fontWeight": "600"}],
        "body-sm": ["14px", {"lineHeight": "20px", "fontWeight": "400"}],
        "label-md": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
        "body-md": ["16px", {"lineHeight": "24px", "fontWeight": "400"}],
        "headline-md": ["24px", {"lineHeight": "32px", "fontWeight": "600"}],
        "label-xs": ["12px", {"lineHeight": "16px", "letterSpacing": "0.05em", "fontWeight": "600"}],
        "label-sm": ["14px", {"lineHeight": "20px", "letterSpacing": "0.05em", "fontWeight": "600"}],
        "data-mono": ["16px", {"lineHeight": "1", "letterSpacing": "-0.02em", "fontWeight": "500"}],
        "display": ["48px", {"lineHeight": "56px", "letterSpacing": "-0.02em", "fontWeight": "700"}]
      }
    }
  }
};
</script>
<style>
body.user-dashboard {
  background-color: #f7f9ff;
  color: #181c20;
  font-family: 'Inter', sans-serif;
  overflow-x: hidden;
}
.font-hanken, .font-headline { font-family: 'Hanken Grotesk', sans-serif; }
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  vertical-align: middle;
}
.glass-panel, .bento-card {
  background: #ffffff;
  border: 1px solid #E9E9E9;
  border-radius: 4px;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.glass-panel:hover, .bento-card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}
.balance-gradient-card {
  background: linear-gradient(135deg, #ffffff 0%, #f1f4fa 100%);
  border: 1px solid #E9E9E9;
}
.active-glow:hover { box-shadow: 0 0 15px rgba(24, 94, 8, 0.2); }
.hide-scrollbar::-webkit-scrollbar { display: none; }
.hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
.chart-gradient, .trading-graph-bg {
  background: linear-gradient(180deg, rgba(51, 119, 34, 0.12) 0%, rgba(51, 119, 34, 0) 100%);
}
.scanning-animation {
  background: linear-gradient(90deg, transparent 0%, rgba(51, 119, 34, 0.15) 50%, transparent 100%);
  background-size: 200% 100%;
  animation: dash-scan 2s infinite linear;
}
@keyframes dash-scan {
  from { background-position: 200% 0; }
  to { background-position: -200% 0; }
}
.dash-scrollbar::-webkit-scrollbar { width: 4px; }
.dash-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.dash-scrollbar::-webkit-scrollbar-thumb { background: #E9E9E9; border-radius: 10px; }
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E9E9E9; border-radius: 10px; }
.user-dash-main {
  padding-top: calc(4rem + env(safe-area-inset-top, 0px));
  box-sizing: border-box;
}
.user-dash-content {
  max-width: 1152px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
.user-topbar {
  padding-top: env(safe-area-inset-top, 0px);
  min-height: calc(4rem + env(safe-area-inset-top, 0px));
  height: calc(4rem + env(safe-area-inset-top, 0px));
  position: fixed;
}
.user-ticker-wrap {
  background: #ebeef4;
  border-radius: 9999px;
  padding: 0.25rem 1rem;
  display: flex;
  align-items: center;
  gap: 0.5rem;
  max-width: min(20rem, calc(100vw - 12rem));
  overflow: hidden;
}
.user-ticker-dot {
  flex-shrink: 0;
  width: 0.5rem;
  height: 0.5rem;
  border-radius: 9999px;
  background: #337722;
  animation: user-social-pulse 1.8s ease-in-out infinite;
}
.user-ticker-marquee {
  font-size: 12px;
  line-height: 16px;
  font-weight: 600;
  letter-spacing: 0.05em;
  color: #41493c;
  white-space: nowrap;
  overflow: hidden;
  flex: 1;
  min-width: 0;
}
@keyframes user-social-pulse {
  0%, 100% { opacity: 1; transform: scale(1); }
  50% { opacity: 0.55; transform: scale(0.85); }
}
.bento-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 24px;
}
.asset-card {
  transition: transform 0.2s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.2s ease;
}
.asset-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
}
</style>
<?php if (!empty($pageExtraStyles)) { echo $pageExtraStyles; } ?>
