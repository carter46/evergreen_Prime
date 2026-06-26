<?php
$pageTitle = $pageTitle ?? (get_site_name() . ' | Admin');
?>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($pageTitle); ?></title>
<?php if (function_exists('output_favicon_tags')) { output_favicon_tags(); } ?>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&amp;family=Inter:wght@400;500;700;800&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
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
        "on-primary": "#432c00",
        "on-primary-container": "#755000",
        "success": "#20B26C",
        "critical": "#EF454A",
        "bg-subtle": "#161B22",
        "surface": "#111417",
        "surface-bright": "#37393d",
        "error-container": "#93000a",
        "on-error": "#690005"
      },
      borderRadius: { "DEFAULT": "0.125rem", "lg": "0.25rem", "xl": "0.5rem", "full": "0.75rem" },
      spacing: {
        "margin-desktop": "32px",
        "margin-mobile": "16px",
        "gutter": "16px",
        "container-max": "1440px",
        "section-padding": "96px"
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
body.admin-dashboard {
  background-color: #111417;
  color: #e1e2e7;
  font-family: 'Inter', sans-serif;
}
.glass-panel {
  background: rgba(30, 35, 41, 0.8);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.08);
}
.material-symbols-outlined {
  font-variation-settings: 'FILL' 0, 'wght' 400, 'GRAD' 0, 'opsz' 24;
  vertical-align: middle;
}
.admin-dash-main {
  padding-top: calc(5rem + env(safe-area-inset-top, 0px));
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
}
.admin-scrollbar::-webkit-scrollbar { width: 4px; }
.admin-scrollbar::-webkit-scrollbar-track { background: #111417; }
.admin-scrollbar::-webkit-scrollbar-thumb { background: #323538; border-radius: 10px; }
.admin-dashboard input:not([type=checkbox]):not([type=radio]):not([type=file]),
.admin-dashboard select,
.admin-dashboard textarea {
  background-color: #161B22;
  border-color: rgba(255, 255, 255, 0.08);
  color: #e1e2e7;
}
.admin-dashboard input::placeholder,
.admin-dashboard textarea::placeholder { color: rgba(212, 196, 176, 0.45); }
.admin-dashboard .bg-white,
.admin-dashboard .dark\:bg-zinc-900,
.admin-dashboard .bg-white.dark\:bg-white\/5 {
  background: rgba(30, 35, 41, 0.8) !important;
  backdrop-filter: blur(12px);
}
.admin-dashboard .border-slate-200,
.admin-dashboard .dark\:border-zinc-800,
.admin-dashboard .border-primary\/10,
.admin-dashboard .divide-primary\/5 > :not([hidden]) ~ :not([hidden]) {
  border-color: rgba(255, 255, 255, 0.08) !important;
}
.admin-dashboard .text-slate-500,
.admin-dashboard .dark\:text-zinc-400,
.admin-dashboard .text-slate-400 { color: #A0A7B4 !important; }
.admin-dashboard .text-slate-900,
.admin-dashboard .dark\:text-slate-100,
.admin-dashboard .text-slate-700,
.admin-dashboard .dark\:text-zinc-300 { color: #e1e2e7 !important; }
.admin-dashboard .bg-slate-50,
.admin-dashboard .dark\:bg-zinc-800,
.admin-dashboard .bg-background-light,
.admin-dashboard .dark\:bg-white\/5 { background-color: #161B22 !important; }
.admin-dashboard .bg-primary:not(.admin-sidebar-active) {
  background-color: #ffc35c !important;
  color: #432c00 !important;
}
</style>
<?php if (!empty($pageExtraStyles)) { echo $pageExtraStyles; } ?>
