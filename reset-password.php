<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Set New Password</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
  tailwind.config = {
    darkMode: "class",
    theme: {
      extend: {
        colors: {
          "primary": "#ffc105",
          "background-light": "#f8f8f5",
          "background-dark": "#231e0f",
        },
        fontFamily: { "display": ["Space Grotesk"] },
        borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem"},
      },
    },
  }
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display min-h-screen flex items-center justify-center p-4 sm:p-6 overflow-x-hidden">
<div class="w-full max-w-[440px]">
<a href="/" class="inline-flex items-center gap-2 text-[#a18a45] hover:text-primary transition-colors mb-8 group" aria-label="Back to home">
<span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
<span class="text-sm font-semibold">Back to home</span>
</a>
<div id="invalid-token" class="hidden bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl p-6 text-center">
<p class="text-red-700 dark:text-red-400 font-medium">Invalid or expired reset link.</p>
<a class="inline-block mt-4 text-sm font-semibold text-primary hover:underline" href="/forgot-password">Request a new link</a>
</div>
<div id="reset-form-wrapper" class="bg-white dark:bg-[#2d2716] p-8 md:p-10 rounded-xl border border-[#eae2cd] dark:border-[#423b26] shadow-sm">
<div class="flex flex-col gap-2 mb-8">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-2">
<span class="material-symbols-outlined text-primary text-2xl">shield</span>
</div>
<h2 class="text-2xl font-bold leading-tight tracking-tight dark:text-white">Set New Password</h2>
<p class="text-[#a18a45] text-sm leading-relaxed">Almost there! Create a new strong password for your <?php echo htmlspecialchars($siteName); ?> account.</p>
</div>
<form id="reset-password-form" class="flex flex-col gap-5">
<input type="hidden" name="token" id="reset-token"/>
<input type="hidden" name="email" id="reset-email"/>
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-[#1d180c] dark:text-[#eae2cd]">New Password</label>
<div class="relative">
<input name="password" class="w-full pl-4 pr-10 py-3 bg-background-light dark:bg-[#1d180c] border border-[#eae2cd] dark:border-[#423b26] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:text-white" placeholder="••••••••" type="password" required minlength="8"/>
<button type="button" data-password-toggle class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8a7d] hover:text-primary p-1" aria-label="Toggle password visibility"><span class="material-icons text-lg">visibility</span></button>
</div>
</div>
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-[#1d180c] dark:text-[#eae2cd]">Confirm Password</label>
<div class="relative">
<input name="confirm_password" class="w-full pl-4 pr-10 py-3 bg-background-light dark:bg-[#1d180c] border border-[#eae2cd] dark:border-[#423b26] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary dark:text-white" placeholder="••••••••" type="password" required/>
<button type="button" data-password-toggle class="absolute right-3 top-1/2 -translate-y-1/2 text-[#8a8a7d] hover:text-primary p-1" aria-label="Toggle password visibility"><span class="material-icons text-lg">visibility</span></button>
</div>
</div>
<div id="reset-password-message" class="text-sm hidden"></div>
<button type="submit" class="w-full mt-2 py-3.5 bg-primary hover:bg-[#e6ae00] text-[#1d180c] font-bold rounded-lg transition-all shadow-lg shadow-primary/10 flex items-center justify-center gap-2">
<span>Update Password</span>
<span class="material-symbols-outlined text-lg">check_circle</span>
</button>
</form>
<div class="mt-8 text-center">
<a class="text-sm font-medium text-[#a18a45] hover:text-[#1d180c] dark:hover:text-primary underline decoration-primary/30 underline-offset-4 transition-colors" href="/login">Back to Login</a>
</div>
</div>
</div>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<script src="/js/app.js"></script>
</body>
</html>
