<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); ?>
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
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" data-alt="User profile picture of a woman smiling" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDojSM2HqTrpeC5VVY-cxL_QomPkUsMDHIdsfC-gVOLeHMDbSvGrcTMZYpX6y_Le4PIh8vI_g-gSPV7mugtxS1mYqq003HO4xE9JzlMLMBdaSMlc6tAoXq2MkBpCrH58La-Y9qJdE2vkcSJa96TZh-rIAr0IQ7ymoL0eCsVfyTlRRugx0yLJuDdpkocrFhePGnrvz1iK6NeXcKsKJt5eofVHqSdi5735q8TYsJNvakbVAENdPgv6injQaemvNjKjE5i7z6SMx54mhY"/>
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" data-alt="User profile picture of a man smiling" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAAcI5lm5bMLx_M4d_pGzX-GTv2BhbPY4Ufch3WRB6GVm46E-oNjZkVGBqaV7Awyhz6kIyYZPoTeA0N0tfFzPg1Sha1Cfn0kDpXo-lBtKnhHZy1B3G4RY-yTQWLZTpD8A42VdpZ5JEsV-wtyzmrpmUKFuueTMDB6d2Zf1OgpRNb8_btkQX4TiIAOi1DbILvkwlhBNNXkkwIpAgSlXZs5U00jVzp_VRHWzVrwmpUlmqQLAAoa3ObYtWBWZ-j4F_uh9-dpMBDI0xhx2Q"/>
<img class="w-10 h-10 rounded-full border-2 border-primary object-cover" data-alt="User profile picture of a young professional" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBfM8rU9XLOpaM_s9EAWsTD8mFRtkoo3dfF8SRB9eAckaTe736OoO2-DM8YrRZFblHuL8qnoqPiPbE4JlYr2ZUOpF8ab4wWZFHYNICu79glSLTGtrZrc-9kxQdKMcKQIsbokqKBc4Yz6yz143l5FbhSYBLpy5nU5_8XolWypJIsspiTeo5y26I-x0eGtWG6UtDchKsoOl5xyPQ5F7UXQmg9w2gTpl1Wg4f8e9T8su3cJ8I7YAWb6-i1YYaubCfT6DPB9AxpVOnVkts"/>
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
<details class="group">
<summary class="text-sm font-medium text-zinc-500 dark:text-zinc-400 cursor-pointer flex items-center gap-1 hover:text-primary transition-colors">
<span class="material-icons text-sm transition-transform group-open:rotate-180">expand_more</span>
                            Have a referral code? (Optional)
                        </summary>
<div class="mt-3">
<input class="w-full px-4 py-2.5 bg-zinc-50 dark:bg-zinc-800/50 border border-zinc-200 dark:border-zinc-700 rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-all dark:text-white uppercase tracking-widest text-sm" id="referral" placeholder="CODE2024" type="text"/>
</div>
</details>
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
<!-- Divider -->
<div class="relative my-8">
<div class="absolute inset-0 flex items-center">
<div class="w-full border-t border-zinc-200 dark:border-zinc-800"></div>
</div>
<div class="relative flex justify-center text-sm">
<span class="px-3 bg-white dark:bg-zinc-900 text-zinc-500">Or register with</span>
</div>
</div>
<!-- Social Sign-on -->
<div class="grid grid-cols-2 gap-4">
<button class="flex items-center justify-center gap-2 py-2.5 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" type="button">
<img alt="Google" class="w-5 h-5" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAcIUFDK10ge7WEserb9VH9ArXVF0Es2wLTY01x0H7RVu315sgFazQ9CQd04lAhXSpVNflzIlJeRdCZ7BphRfM4Co5yv3Y6FY3VeoN0640U1TYNIRLOIuNsN-lS-sEToRxxj6X7XKT9TS3Hzk8sOo59eTC4Vpsd6S4XRm2_rWPBfOAtrHVVXaFKQGgzHgtZ-7qugj8g6uZ6JYZMIbL6yhfZFXCZiPR640g0d1ENUceVbMEA_dRWuuXPi_j66IiU__uhtq1EX2Hwv5o"/>
<span class="text-sm font-medium dark:text-white">Google</span>
</button>
<button class="flex items-center justify-center gap-2 py-2.5 border border-zinc-200 dark:border-zinc-700 rounded-lg hover:bg-zinc-50 dark:hover:bg-zinc-800 transition-colors" type="button">
<img alt="Apple" class="w-5 h-5 dark:invert" src="https://lh3.googleusercontent.com/aida-public/AB6AXuB1ysG680Wxo700e2AHkJgOKv6HiiudjYEOwH5ZJK0AA55Q7VbdeWy9DXMzKHpUFG_QULFbdlWCRwN692_6yRjgAOkZ79WdJNjT-v3_JY70PCud7baptR735V4jTXYBGypz6niNVlYu8gdKtlpX532ePDhVe6C_93j9r7hp2SBGWBh39pxymZK6FzakEQbNlul-dETMIjNVYLaLh3kCECyZAYaDZV7M7EFzpkFNjJ40LtJ-2OSJ8Cx8_41ZKHWuva0WR9NSOCCABrc"/>
<span class="text-sm font-medium dark:text-white">Apple</span>
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
</body></html>