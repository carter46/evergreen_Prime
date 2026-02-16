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
<title>Register | <?php echo htmlspecialchars($siteName); ?></title>
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
                    fontFamily: {
                        "display": ["Space Grotesk"]
                    },
                    borderRadius: {
                        "DEFAULT": "0.25rem",
                        "lg": "0.5rem",
                        "xl": "0.75rem",
                        "full": "9999px"
                    },
                },
            },
        }
    </script>
<style>
        body {
            font-family: 'Space Grotesk', sans-serif;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark min-h-screen flex items-center justify-center p-4 overflow-x-hidden">
<div class="max-w-4xl w-full flex flex-col md:flex-row bg-white dark:bg-zinc-900 rounded-xl shadow-2xl overflow-hidden border border-zinc-200/50 dark:border-zinc-800/50">
<!-- Left Side: Branding/Visual -->
<div class="hidden md:flex md:w-5/12 bg-primary p-12 flex-col justify-between relative overflow-hidden">
<!-- Decorative Pattern -->
<div class="absolute inset-0 opacity-10 pointer-events-none">
<svg height="100%" width="100%" xmlns="http://www.w3.org/2000/svg">
<defs>
<pattern height="40" id="grid" patternunits="userSpaceOnUse" width="40">
<path d="M 40 0 L 0 0 0 40" fill="none" stroke="black" stroke-width="1"></path>
</pattern>
</defs>
<rect fill="url(#grid)" height="100%" width="100%"></rect>
</svg>
</div>
<div class="relative z-10">
<a href="/" class="inline-flex items-center gap-2 text-black/70 hover:text-black transition-colors mb-12 group" aria-label="Back to home">
<span class="material-symbols-outlined group-hover:-translate-x-1 transition-transform">arrow_back</span>
<span class="text-sm font-semibold">Back to home</span>
</a>
<h1 class="text-4xl font-bold text-black leading-tight mb-6">
                    Start your <br/>journey with <br/>Bloombit.
                </h1>
<p class="text-black/70 text-lg">
                    Join thousands of professionals managing their digital assets with precision and ease.
                </p>
</div>
<div class="relative z-10">
<div class="flex -space-x-3 mb-4">
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" alt="User" src="/uploads/images/user1.jpg" onerror="this.style.display='none'"/>
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" alt="User" src="/uploads/images/user2.jpg" onerror="this.style.display='none'"/>
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" alt="User" src="/uploads/images/user3.jpg" onerror="this.style.display='none'"/>
</div>
<p class="text-black text-sm font-medium">Join 10k+ active users worldwide</p>
</div>
</div>
<!-- Right Side: Registration Form -->
<div class="flex-1 p-8 lg:p-12">
<div class="flex justify-between items-center mb-10">
<div>
<h2 class="text-2xl font-bold text-zinc-900 dark:text-white">Create Account</h2>
<p class="text-zinc-500 dark:text-zinc-400 text-sm mt-1">Get started with your free account today.</p>
</div>
<div class="md:hidden shrink-0">
<a href="/" class="inline-flex items-center gap-2 text-zinc-500 hover:text-primary transition-colors p-2" aria-label="Back to home"><span class="material-symbols-outlined">arrow_back</span></a>
</div>
</div>
<form id="register-form" class="space-y-5">
<!-- Progress Line -->
<div class="w-full h-1 bg-zinc-100 dark:bg-zinc-800 rounded-full overflow-hidden mb-8">
<div class="bg-primary h-full w-1/4"></div>
</div>
<div class="grid grid-cols-1 gap-5">
<!-- Full Name -->
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="name">Full Name</label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">person</span>
<input name="name" class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white" id="name" placeholder="John Doe" type="text"/>
</div>
</div>
<!-- Email -->
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="email">Email Address</label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">mail</span>
<input name="email" class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white" id="email" placeholder="john@example.com" type="email" required/>
</div>
</div>
<!-- Phone Number -->
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="phone">Phone Number <span class="text-zinc-400">(Optional)</span></label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">phone</span>
<input name="phone" class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white" id="phone" placeholder="+1 234 567 8900" type="tel"/>
</div>
</div>
<!-- Password Grid -->
<div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="password">Password</label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">lock</span>
<input name="password" class="w-full pl-10 pr-10 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white" id="password" placeholder="••••••••" type="password" required/>
<button type="button" data-password-toggle class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-primary p-1" aria-label="Toggle password visibility"><span class="material-icons text-lg">visibility</span></button>
</div>
</div>
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="confirm-password">Confirm Password</label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">lock_reset</span>
<input name="confirm_password" class="w-full pl-10 pr-10 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white" id="confirm-password" placeholder="••••••••" type="password" required/>
<button type="button" data-password-toggle class="absolute right-3 top-1/2 -translate-y-1/2 text-zinc-400 hover:text-primary p-1" aria-label="Toggle password visibility"><span class="material-icons text-lg">visibility</span></button>
</div>
</div>
</div>
<!-- Referral Code (Optional) -->
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="referral">Referral Code <span class="text-zinc-400">(Optional)</span></label>
<div class="relative">
<span class="material-icons absolute left-3 top-1/2 -translate-y-1/2 text-zinc-400 text-lg">card_giftcard</span>
<input name="referral" class="w-full pl-10 pr-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white uppercase tracking-widest text-sm" id="referral" placeholder="CODE2024" type="text"/>
</div>
</div>
<!-- Profile Photo (Optional) -->
<div>
<label class="block text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1.5" for="avatar">Profile Photo <span class="text-zinc-400">(Optional)</span></label>
<div class="flex items-center gap-4">
<div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0" id="avatar-preview">
<span class="material-icons text-zinc-400 text-2xl">person</span>
</div>
<input name="avatar" id="avatar" class="flex-1 text-sm text-zinc-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:bg-primary file:text-black file:font-medium file:cursor-pointer hover:file:bg-primary/90" accept="image/png,image/jpeg,image/webp" type="file"/>
</div>
<p class="text-xs text-zinc-400 mt-1">PNG, JPEG or WEBP. Max 2MB.</p>
</div>
<!-- Terms -->
<div class="flex items-start gap-3 mt-2">
<div class="flex items-center h-5">
<input class="w-4 h-4 text-primary border-zinc-300 rounded focus:ring-primary accent-primary" id="terms" type="checkbox"/>
</div>
<label class="text-sm text-zinc-600 dark:text-zinc-400" for="terms">
                            I agree to the <a class="text-primary hover:underline font-medium" href="/legal_centre">Terms of Service</a> and <a class="text-primary hover:underline font-medium" href="/legal_centre">Privacy Policy</a>.
                        </label>
</div>
<div id="register-form-message" class="text-sm hidden"></div>
<!-- Submit Button -->
<button class="w-full py-3.5 px-4 bg-primary hover:bg-primary/90 text-black font-bold rounded-lg shadow-lg shadow-primary/20 transition-all transform hover:-translate-y-0.5 active:translate-y-0 mt-4" type="submit">
                        Create My Account
                    </button>
</div>
<p class="text-center text-sm text-zinc-600 dark:text-zinc-400 mt-8">
                    Already have an account? 
                    <a class="text-primary font-bold hover:underline" href="/login">Log in here</a>
</p>
</form>
</div>
</div>
<script src="/js/app.js"></script>
<script>
document.getElementById('avatar')?.addEventListener('change', function(){
  var f = this.files[0];
  var p = document.getElementById('avatar-preview');
  if (!p) return;
  if (f && /^image\/(png|jpeg|webp)$/.test(f.type)) {
    var r = new FileReader();
    r.onload = function(){ p.innerHTML = '<img src="'+r.result+'" alt="" class="w-full h-full object-cover"/>'; };
    r.readAsDataURL(f);
  } else { p.innerHTML = '<span class="material-icons text-zinc-400 text-2xl">person</span>'; }
});
</script>
</body></html>