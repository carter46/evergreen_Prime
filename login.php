<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/session-bootstrap.php';
if (isset($_SESSION['user_id'])) {
    $role = $_SESSION['role'] ?? 'user';
    header('Location: ' . ($role === 'admin' ? '/dashboard/admin' : '/dashboard'));
    exit;
}
$siteName = get_site_name();
?>
<!DOCTYPE html>

<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Secure Login</title>
<?php output_favicon_tags(); ?>
<!-- Tailwind CSS with plugins -->
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<!-- Material Icons and Symbols -->
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<!-- Google Fonts: Space Grotesk -->
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
      tailwind.config = {
        darkMode: "class",
        theme: {
          extend: {
            colors: {
              "primary": "#ffc105",
              "background-light": "#f8f8f5",
              "background-dark": "#231e0f",
              "neutral-soft": "#f4f1e6",
              "text-main": "#1d180c",
              "text-muted": "#a18a45",
            },
            fontFamily: {
              "display": ["Space Grotesk", "sans-serif"]
            },
            borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
          },
        },
      }
    </script>
<style>
        body {
            font-family: 'Space Grotesk', sans-serif;
            background-color: #f8f8f5;
        }
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .checkbox-custom:checked {
            background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='rgb(29,24,12)' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M12.207 4.793a1 1 0 010 1.414l-5 5a1 1 0 01-1.414 0l-2-2a1 1 0 011.414-1.414L6.5 9.086l4.293-4.293a1 1 0 011.414 0z'/%3e%3c/svg%3e");
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-text-main antialiased overflow-x-hidden min-h-screen touch-manipulation">
<div class="flex flex-col lg:flex-row min-h-screen w-full">
<!-- Left Section: Login Form -->
<div class="w-full lg:w-[45%] xl:w-[40%] flex flex-col bg-white dark:bg-background-dark px-6 py-8 md:px-12 lg:px-20">
<!-- Back to Home -->
<a href="/" class="inline-flex items-center gap-2 text-text-muted hover:text-primary transition-colors mb-16 lg:mb-24 group" aria-label="Back to home">
<span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
<span class="text-sm font-semibold">Back to home</span>
</a>
<div class="max-w-md w-full mx-auto lg:mx-0">
<div class="mb-10">
<h1 class="text-text-main dark:text-white text-4xl font-black leading-tight tracking-[-0.033em] mb-3">Welcome back</h1>
<p class="text-text-muted text-lg font-normal">Enter your details to manage your digital assets securely.</p>
</div>
<form id="login-form" class="space-y-6">
<!-- Email Field -->
<div class="flex flex-col gap-2">
<label class="text-text-main dark:text-white text-sm font-semibold uppercase tracking-wider">Email Address</label>
<input name="email" class="w-full rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary h-14 px-4 text-text-main dark:text-white placeholder:text-text-muted transition-all" placeholder="e.g. name@company.com" type="email" required/>
</div>
<!-- Password Field -->
<div class="flex flex-col gap-2">
<div class="flex justify-between items-center">
<label class="text-text-main dark:text-white text-sm font-semibold uppercase tracking-wider">Password</label>
</div>
<div class="relative flex items-center group">
<input name="password" class="w-full rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary h-14 px-4 pr-12 text-text-main dark:text-white placeholder:text-text-muted transition-all" placeholder="Enter your password" type="password" required/>
<button type="button" data-password-toggle class="absolute right-4 text-text-muted hover:text-primary transition-colors p-1" aria-label="Toggle password visibility"><span class="material-symbols-outlined" data-icon="visibility">visibility</span></button>
</div>
</div>
<!-- Utilities -->
<div class="flex items-center justify-between py-2">
<label class="flex items-center gap-3 cursor-pointer group">
<input class="checkbox-custom h-5 w-5 rounded border-neutral-soft dark:border-neutral-800 text-primary focus:ring-0 focus:ring-offset-0 transition-all cursor-pointer bg-white dark:bg-neutral-900" type="checkbox"/>
<span class="text-text-main dark:text-neutral-300 text-sm font-medium group-hover:text-primary transition-colors">Remember me</span>
</label>
<a class="text-sm font-semibold text-text-main dark:text-neutral-300 hover:text-primary transition-colors decoration-primary underline-offset-4 decoration-2" href="/forgot-password">Forgot password?</a>
</div>
<?php if (!empty($_GET['timeout'])): ?>
<div class="text-sm text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 px-4 py-3 rounded-lg mb-4">You were logged out due to inactivity. Please sign in again.</div>
<?php endif; ?>
<div id="login-form-message" class="text-sm hidden"></div>
<!-- Sign In Button -->
<button class="w-full bg-primary hover:brightness-105 active:scale-[0.98] text-text-main font-bold py-4 rounded-xl shadow-lg shadow-primary/20 transition-all flex items-center justify-center gap-2 group" type="submit">
<span>Sign In to <?php echo htmlspecialchars($siteName); ?></span>
<span class="material-symbols-outlined transition-transform group-hover:translate-x-1" data-icon="arrow_forward">arrow_forward</span>
</button>
</form>
<!-- OTP Verification Step (hidden initially) -->
<div id="login-otp-step" class="space-y-6 hidden mt-8">
<h2 class="text-xl font-bold text-text-main dark:text-white">Verify your identity</h2>
<p class="text-text-muted text-sm" id="login-otp-email-display"></p>
<p class="text-sm text-text-muted">Enter the 6-digit code we sent to your email.</p>
<div class="flex gap-2 justify-center my-6" id="login-otp-inputs">
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" autocomplete="one-time-code" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 1"/>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 2"/>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 3"/>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 4"/>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 5"/>
<input type="tel" inputmode="numeric" pattern="[0-9]*" maxlength="1" class="w-12 h-14 text-center text-xl font-bold rounded-lg border border-neutral-soft dark:border-neutral-800 bg-background-light dark:bg-neutral-900 focus:ring-2 focus:ring-primary focus:border-primary text-text-main dark:text-white" data-otp-digit aria-label="Digit 6"/>
</div>
<div id="login-otp-message" class="text-sm hidden"></div>
<button type="button" id="login-otp-resend" class="text-primary hover:underline text-sm font-medium disabled:opacity-50" disabled>Resend code (60s)</button>
<button type="button" id="login-otp-submit" class="w-full bg-primary hover:brightness-105 text-text-main font-bold py-4 rounded-xl flex items-center justify-center gap-2">
                        Verify & Sign In
                    </button>
</div>
<!-- Footer Link -->
<p class="mt-12 text-center text-text-muted text-sm" id="login-have-account">
                    Don't have an account? 
                    <a class="text-text-main dark:text-white font-bold hover:text-primary transition-colors ml-1" href="/register">Create an account</a>
</p>
</div>
</div>
<!-- Right Section: Branded Illustration -->
<div class="hidden lg:flex lg:w-[55%] xl:w-[60%] bg-background-dark relative overflow-hidden items-center justify-center">
<!-- Background Decorations -->
<div class="absolute top-0 right-0 w-full h-full opacity-30">
<div class="absolute -top-24 -right-24 size-[600px] bg-primary/10 rounded-full blur-[120px]"></div>
<div class="absolute -bottom-48 -left-48 size-[800px] bg-primary/5 rounded-full blur-[150px]"></div>
</div>
<!-- Main Graphic Content -->
<div class="relative z-10 w-full h-full flex flex-col items-center justify-center p-12">
<div class="relative w-full max-w-2xl aspect-square flex items-center justify-center">
<!-- Placeholder for 3D AI Branded Graphic -->
<div class="absolute w-4/5 h-4/5 bg-gradient-to-br from-primary/20 to-transparent rounded-full animate-pulse blur-3xl"></div>
<div class="relative z-20 w-full h-full rounded-3xl overflow-hidden shadow-2xl border border-white/10 group">
<div class="absolute inset-0 bg-neutral-900/40 mix-blend-overlay"></div>
<img alt="Fintech AI Graphic" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110" data-alt="Abstract 3D digital golden data structures representing AI and growth" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCz2XR3IJemsQH6Ekarn2ATRGKY__az2d3uIrODDs34QsYPfc8MdkdpuANzNvTANyRxCQhfKkXWDbXED0QsMa05b89aRPHtAIRRayxSjjj5FuR0vHZgF0oRzoNtATZ61JtURIHMaIODyGkYkfCFnN23V5yWmMlGAeId5NStX3u9x_BMMI3LYvB2xSCLntCCgrDUTvKkhVwHmsYL9hGthRWNn1k5wr5Re1TkKmkg6b27OcF1A4M-gFVmcsnkOMYKIDEXimGaRPuIeSU"/>
<!-- Floating Fintech UI Element Over Graphic -->
<div class="absolute bottom-8 left-8 right-8 glass-panel p-6 rounded-2xl border border-white/20">
<div class="flex items-center gap-4 mb-4">
<div class="size-10 rounded-full bg-primary flex items-center justify-center">
<span class="material-symbols-outlined text-text-main font-bold" data-icon="trending_up">trending_up</span>
</div>
<div>
<p class="text-white font-bold text-lg">AI-Powered Insights</p>
<p class="text-white/60 text-xs uppercase tracking-widest font-medium">Real-time asset optimization</p>
</div>
</div>
<div class="space-y-3">
<div class="h-2 w-full bg-white/10 rounded-full overflow-hidden">
<div class="h-full w-3/4 bg-primary"></div>
</div>
<div class="h-2 w-1/2 bg-white/10 rounded-full overflow-hidden">
<div class="h-full w-2/3 bg-primary/60"></div>
</div>
</div>
</div>
</div>
</div>
<div class="mt-12 text-center max-w-md">
<h3 class="text-white text-3xl font-black mb-4">Master your digital fortune</h3>
<p class="text-neutral-400 text-lg">Join 2M+ users who trust <?php echo htmlspecialchars($siteName); ?>'s AI intelligence for smarter asset management and secure high-yield growth.</p>
</div>
</div>
<!-- Mesh Pattern Overlay -->
<div class="absolute inset-0 opacity-[0.03] pointer-events-none" style="background-image: radial-gradient(#fff 1px, transparent 0); background-size: 40px 40px;"></div>
</div>
</div>

<!-- Translation widget (GTranslate) -->
<div class="gtranslate_wrapper"></div>
<?php require_once __DIR__ . '/includes/translation-widget.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
</body></html>