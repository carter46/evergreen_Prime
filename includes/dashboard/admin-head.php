<?php
$pageTitle = $pageTitle ?? (get_site_name() . ' | Admin');
?>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php if (function_exists('output_favicon_tags')) { output_favicon_tags(); } ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Hanken+Grotesk:wght@300;400;600;700;800&amp;family=Inter:wght@400;500;600&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
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
        "surface-dim": "#f7f9ff",
        "error": "#ba1a1a",
        "surface-bright": "#f7f9ff",
        "primary-fixed": "#aaf690",
        "on-primary-container": "#ffffff",
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
        "border-low": "#E9E9E9",
        "bg-subtle": "#f1f4fa"
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
        "container-max": "1440px"
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
body.admin-dashboard {
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
.material-icons, .material-icons-round {
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  vertical-align: middle;
  -webkit-font-smoothing: antialiased;
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
.admin-dash-main {
  padding-top: calc(4rem + env(safe-area-inset-top, 0px)) !important;
  box-sizing: border-box;
}
.admin-dash-content {
  max-width: 1440px;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}
.admin-topbar {
  padding-top: env(safe-area-inset-top, 0px);
  min-height: calc(4rem + env(safe-area-inset-top, 0px));
  height: calc(4rem + env(safe-area-inset-top, 0px));
  position: fixed;
}
.admin-scrollbar::-webkit-scrollbar,
.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.admin-scrollbar::-webkit-scrollbar-track,
.custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; }
.admin-scrollbar::-webkit-scrollbar-thumb,
.custom-scrollbar::-webkit-scrollbar-thumb { background: #E9E9E9; border-radius: 10px; }
.admin-dashboard input:not([type=checkbox]):not([type=radio]):not([type=file]),
.admin-dashboard select,
.admin-dashboard textarea {
  background-color: #f1f4fa;
  border-color: #E9E9E9;
  color: #181c20;
}
.admin-dashboard input::placeholder,
.admin-dashboard textarea::placeholder { color: #717a6b; }
.admin-dashboard .bg-primary,
.admin-dashboard button.bg-primary,
.admin-dashboard a.bg-primary {
  background-color: #337722 !important;
  color: #ffffff !important;
}
.admin-dashboard .bg-primary.text-zinc-900,
.admin-dashboard button.bg-primary.text-zinc-900,
.admin-dashboard a.bg-primary.text-zinc-900 {
  color: #ffffff !important;
}
.admin-dashboard .bg-primary\/20.text-zinc-900,
.admin-dashboard .bg-primary\/20.hover\:bg-primary\/30 {
  color: #185e08 !important;
}
.admin-dashboard .bg-primary:hover {
  opacity: 0.92;
}
.admin-dashboard .text-primary-container,
.admin-dashboard .hover\:text-primary-container:hover {
  color: #337722 !important;
}
.admin-dashboard .bg-primary-container {
  background-color: #337722 !important;
  color: #ffffff !important;
}
.admin-dashboard .border-primary-container,
.admin-dashboard .border-l-primary-container {
  border-color: #337722 !important;
}
.admin-dashboard .bg-primary-container\/10,
.admin-dashboard .hover\:bg-primary-container\/10:hover {
  background-color: rgba(51, 119, 34, 0.1) !important;
}
.admin-dashboard .bg-primary-container\/20 { background-color: rgba(51, 119, 34, 0.15) !important; }
.admin-dashboard .bg-primary-container\/30 { background-color: rgba(51, 119, 34, 0.2) !important; }
.admin-dashboard .bg-primary-container\/40 { background-color: rgba(51, 119, 34, 0.28) !important; }
.admin-dashboard .bg-primary-container\/60 { background-color: rgba(51, 119, 34, 0.45) !important; }
.admin-dashboard .border-primary-container\/20,
.admin-dashboard .border-primary-container\/30 { border-color: rgba(51, 119, 34, 0.25) !important; }
.admin-dashboard .shadow-primary-container\/20 { box-shadow: 0 10px 25px rgba(51, 119, 34, 0.15) !important; }
.admin-dashboard .text-success { color: #337722 !important; }
.admin-dashboard .text-text-primary { color: #181c20 !important; }
.admin-dashboard .text-background-dark { color: #ffffff !important; }
.admin-dashboard .bg-primary\/20 { background-color: rgba(51, 119, 34, 0.12) !important; }
.admin-dashboard .border-primary { border-color: #185e08 !important; }
.admin-dashboard .focus\:ring-primary:focus { --tw-ring-color: rgba(51, 119, 34, 0.35) !important; }
.admin-dashboard .text-critical,
.admin-dashboard .text-error { color: #ba1a1a !important; }
.admin-dashboard .bg-white,
.admin-dashboard .dark\:bg-zinc-900,
.admin-dashboard .dark\:bg-white\/5 {
  background: #ffffff !important;
}
.admin-dashboard .bg-slate-50,
.admin-dashboard .dark\:bg-zinc-800,
.admin-dashboard .dark\:bg-zinc-800\/50,
.admin-dashboard .bg-background-light,
.admin-dashboard .dark\:bg-white\/5,
.admin-dashboard .bg-bg-subtle,
.admin-dashboard .hover\:bg-bg-subtle:hover,
.admin-dashboard .bg-slate-100,
.admin-dashboard .bg-slate-200,
.admin-dashboard .dark\:bg-zinc-700 {
  background-color: #f1f4fa !important;
}
.admin-dashboard .bg-zinc-900,
.admin-dashboard .dark\:bg-zinc-800.text-white {
  background-color: #185e08 !important;
  color: #ffffff !important;
}
.admin-dashboard .border-slate-200,
.admin-dashboard .dark\:border-zinc-800,
.admin-dashboard .dark\:border-zinc-700,
.admin-dashboard .border-primary\/10,
.admin-dashboard .divide-primary\/5 > :not([hidden]) ~ :not([hidden]),
.admin-dashboard .divide-slate-100,
.admin-dashboard .dark\:divide-zinc-800 {
  border-color: #E9E9E9 !important;
}
.admin-dashboard .text-slate-500,
.admin-dashboard .dark\:text-zinc-400,
.admin-dashboard .text-slate-400,
.admin-dashboard .text-zinc-400,
.admin-dashboard .text-zinc-500,
.admin-dashboard .text-text-secondary { color: #41493c !important; }
.admin-dashboard .text-zinc-900,
.admin-dashboard .text-slate-900,
.admin-dashboard .dark\:text-slate-100,
.admin-dashboard .dark\:text-white,
.admin-dashboard .text-slate-700,
.admin-dashboard .dark\:text-zinc-300,
.admin-dashboard .text-slate-600 { color: #181c20 !important; }
.admin-dashboard .hover\:bg-slate-100:hover,
.admin-dashboard .dark\:hover\:bg-zinc-800:hover,
.admin-dashboard .hover\:bg-slate-50:hover,
.admin-dashboard .hover\:bg-zinc-700:hover {
  background-color: #ebeef4 !important;
}
.admin-dashboard .file\:bg-primary { --tw-bg-opacity: 1; }
.admin-dashboard input[type=file]::file-selector-button {
  background-color: #337722 !important;
  color: #ffffff !important;
}
body.admin-dashboard .gtranslate_wrapper {
  display: none !important;
  visibility: hidden !important;
  opacity: 0 !important;
  pointer-events: none !important;
}
</style>
<?php if (!empty($pageExtraStyles)) { echo $pageExtraStyles; } ?>
