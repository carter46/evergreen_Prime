<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Forgot Password</title>
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
<div class="bg-white dark:bg-[#2d2716] p-8 md:p-10 rounded-xl border border-[#eae2cd] dark:border-[#423b26] shadow-sm">
<div class="flex flex-col gap-2 mb-8">
<div class="w-12 h-12 bg-primary/10 rounded-lg flex items-center justify-center mb-2">
<span class="material-symbols-outlined text-primary text-2xl">lock_reset</span>
</div>
<h2 class="text-2xl font-bold leading-tight tracking-tight dark:text-white">Forgot Password?</h2>
<p class="text-[#a18a45] text-sm leading-relaxed">No worries, it happens. Enter your email and we'll send you a link to get back into your account.</p>
</div>
<form id="forgot-password-form" class="flex flex-col gap-6">
<div class="flex flex-col gap-2">
<label class="text-sm font-semibold text-[#1d180c] dark:text-[#eae2cd]">Email Address</label>
<input name="email" class="w-full pl-10 pr-4 py-3 bg-background-light dark:bg-[#1d180c] border border-[#eae2cd] dark:border-[#423b26] rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent dark:text-white placeholder:text-[#a18a45]/60" placeholder="e.g. name@company.com" type="email" required/>
</div>
<div id="forgot-password-message" class="text-sm hidden"></div>
<button type="submit" class="w-full py-3.5 bg-primary hover:bg-[#e6ae00] text-[#1d180c] font-bold rounded-lg transition-all shadow-lg shadow-primary/10 flex items-center justify-center gap-2">
<span>Send Reset Link</span>
<span class="material-symbols-outlined text-lg">arrow_forward</span>
</button>
</form>
<div class="mt-8 text-center">
<a class="text-sm font-medium text-[#a18a45] hover:text-[#1d180c] dark:hover:text-primary underline decoration-primary/30 underline-offset-4 transition-colors" href="/login">Back to Login</a>
</div>
</div>
<div class="mt-8 text-center">
<p class="text-xs font-medium uppercase tracking-widest text-[#a18a45] flex items-center justify-center gap-2">
<span class="material-symbols-outlined text-sm">lock</span> Secure Bank-Grade Encryption
</p>
</div>
</div>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<script src="/js/app.js"></script>
</body>
</html>
