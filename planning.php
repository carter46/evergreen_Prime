<?php
require_once __DIR__ . '/includes/helpers.php';
require_once __DIR__ . '/includes/blog-posts.php';
$siteName = get_site_name();
$pageTitle = 'Retirement Planning | ' . $siteName;
$portfolioUrl = '/dashboard/user/analytics';
$planningBlogSlugs = ['three-as-of-saving', 'what-is-an-ira', 'five-keys-retirement-income'];
$planningBlogPosts = get_blog_posts_by_slugs($planningBlogSlugs);
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
</head>
<body class="fidelity-subpage bg-background text-on-surface font-body-md text-body-md overflow-x-hidden">
<?php $currentPage = 'planning'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main>
<!-- Breadcrumbs -->
<nav class="max-w-[1152px] mx-auto px-margin-desktop py-sm">
<div class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md">
<a class="hover:text-primary transition-colors" href="/">Home</a>
<span class="material-symbols-outlined text-[14px]">chevron_right</span>
<span class="text-on-surface">Retirement Planning</span>
</div>
</nav>
<!-- Hero Section -->
<section class="max-w-[1152px] mx-auto px-margin-desktop py-xl flex flex-col md:flex-row items-center gap-xl">
<div class="md:w-1/2 order-2 md:order-1">
<h1 class="font-display-lg text-display-lg text-on-surface leading-tight mb-md">
                    Retirement planning to match your stage of life
                </h1>
<p class="font-body-lg text-body-lg text-on-surface-variant mb-lg max-w-md">
                    We'll help you take on retirement with a clear plan to get there, whether you're saving for or living in retirement.
                </p>
<div class="flex gap-sm">
<a href="/register" class="bg-fidelity-green text-white font-label-md text-label-md py-sm px-lg rounded-lg shadow-sm hover:opacity-90 transition-all">Start Planning</a>
<a href="/register" class="border border-institutional-blue text-institutional-blue font-label-md text-label-md py-sm px-lg rounded-lg hover:bg-surface-container-low transition-all">Explore IRAs</a>
</div>
</div>
<div class="md:w-1/2 relative order-1 md:order-2 mb-lg md:mb-0">
<div class="aspect-[4/3] rounded-xl overflow-hidden shadow-lg border border-surface-gray">
<img class="w-full h-full object-cover" data-alt="A professional, high-quality photograph of two women of different generations, perhaps a mother and daughter, smiling while sitting together on a comfortable modern sofa. They are looking at a sleek silver laptop together in a bright, airy living room with large windows showing lush greenery outside. The lighting is soft and natural, creating a warm, trustworthy atmosphere consistent with a premium financial institution's branding." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCFZ6IgNIGjVyf4zoO6M1BJvh9BZsxKdjH277DJq9mKH5SX2-VWDPhnCMHRGrY-CPpcP5Xl02h8ii5-_95Y6uRah9kdoKkhmLc8b-VMHu2Pd42d4-3qjijK2wZmy53whrWSdoVspcZWBYg3M5cQZaOzoUq2vNTuw548O9rPmLvotQwsX2ntz3KgAk-zdHIw-QAy2wIwuKFje29-ovdzAwpOgV3FA1CgeGvimNXdh13HmwAeuOG7EOg_sA"/>
</div>
<!-- Decorative elements -->
<div class="absolute -bottom-6 -left-6 w-24 h-24 bg-primary/10 rounded-full blur-2xl z-[-1]"></div>
</div>
</section>
<!-- Account Cards Section -->
<section class="bg-surface-container-lowest py-xl">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<div class="text-center mb-xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Accounts to help with your retirement savings</h2>
<p class="font-body-md text-body-md text-on-surface-variant">You have retirement goals; we've got the solutions to help you achieve them.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<!-- Roth IRA -->
<div class="flex flex-col border border-surface-gray rounded-xl overflow-hidden hover:shadow-lg transition-shadow bg-white">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" data-alt="A high-end lifestyle image representing long-term financial security and freedom. A close-up of a person's hands holding a cup of coffee while looking out a large window at a serene mountain landscape. The aesthetic is minimalist and luxurious, with a palette of soft whites and natural wood tones, reflecting a calm and secure retirement lifestyle." src="https://lh3.googleusercontent.com/aida-public/AB6AXuBC9qWSMDm2fomT_qmXJ-7OZOFmrfF63rCPXRwQqDG4d8VlfUqrrgFMZ6FulwR--6B4PIV7JplK2Pq380TMu3O5M1zg41peLF87YWdBASBnjknWun922u8S2xxyyhvPbQzJU8FG_i7c9j3pnGdeZqmWqW-2auuhEHZt6Xnx0SRIAQ_dywqnp5V4Ci1GBkQkkU7FCfGoxO606RED28fDy3TlLfQ48v6K5ya_7rP3Tdhi7qMNW2omnNgWBA"/>
</div>
<div class="p-md flex flex-col flex-grow">
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Roth IRA</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow">Save tax-free with access to your contributions. Potential growth within the account is tax-free.</p>
<div class="flex flex-col gap-xs mt-auto">
<a href="/register" class="bg-fidelity-green text-white font-label-md text-label-md py-xs rounded-lg text-center">Open an account</a>
<a class="text-institutional-blue font-label-md text-label-md text-center hover:underline" href="#">Explore Roth IRA</a>
</div>
</div>
</div>
<!-- Traditional IRA -->
<div class="flex flex-col border border-surface-gray rounded-xl overflow-hidden hover:shadow-lg transition-shadow bg-white">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" data-alt="A professional office setting showing a clean desk with a laptop, a notebook, and a pair of reading glasses. The scene is illuminated by warm morning sunlight, suggesting focus and deliberate planning. The style is modern and corporate with a high-key lighting approach, conveying clarity and expertise in retirement planning." src="https://lh3.googleusercontent.com/aida-public/AB6AXuArIC0IDcJGkUew8Ssd_cXpxw2Y4fYuSZ7eTCpGjozFbejkMqn067cz2nJjjlrobzmAn5nYL1ARu7ryvKkIUOWj2DjwHYVQJKJty1qr7o6MpcnYZBwoool2Y74Sdz5p7wjMh9WntP3M8HpOcEhs1yWEWzuKENrmRXYx3_S0Fl_i_JT8IVlDbW_Oa-3rpug7Oe-moLK7ccmEcnA0Q8teKJ4oaBM-VO5mUzs1quny2KikWIqxlnRlrVGehw"/>
</div>
<div class="p-md flex flex-col flex-grow">
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Traditional IRA</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow">Potentially reduce taxable income today. Reduce your taxable income by deducting your contributions.</p>
<div class="flex flex-col gap-xs mt-auto">
<a href="/register" class="bg-fidelity-green text-white font-label-md text-label-md py-xs rounded-lg text-center">Open an account</a>
<a class="text-institutional-blue font-label-md text-label-md text-center hover:underline" href="#">Explore traditional IRA</a>
</div>
</div>
</div>
<!-- Rollover IRA -->
<div class="flex flex-col border border-surface-gray rounded-xl overflow-hidden hover:shadow-lg transition-shadow bg-white">
<div class="h-48 overflow-hidden">
<img class="w-full h-full object-cover transition-transform duration-700 hover:scale-105" data-alt="An abstract representation of financial growth and consolidation. A collection of smooth, polished stones of different sizes stacked in a stable, vertical tower against a soft, out-of-focus natural background. The image uses a serene palette of grays and greens, symbolizing balance, stability, and the focused growth of one's retirement assets." src="https://lh3.googleusercontent.com/aida-public/AB6AXuCfDai8jJ2HtdGfZ9TwOJ_CFp9vDjYhyIf2_rQ5PWf2fYdxbBdph_0bnX9iywcRFfe2hrJ4-OsXGFQzOlMNKTFYr14F9bz-oi-upxLWKKMVBA8QEIuApGJ7_G_vS7aNSgeHug1Zd9Jn8YgW4ZdH1D0NTZBbkb2npdMx5TFqVXs91uIodhV9U1DMA-XEeEM8PAciq75GwCC5e7ARvXzAYGpdzCTO5DKFUFPlBbdFS61MmPm5CZ6aUz0reA"/>
</div>
<div class="p-md flex flex-col flex-grow">
<h3 class="font-headline-md text-headline-md text-on-surface mb-xs">Rollover IRA</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg flex-grow">Combine your accounts and focus on growth in one place. Move your old 401(k) without taxes or penalties.</p>
<div class="flex flex-col gap-xs mt-auto">
<a href="/register" class="bg-fidelity-green text-white font-label-md text-label-md py-xs rounded-lg text-center">Open an account</a>
<a class="text-institutional-blue font-label-md text-label-md text-center hover:underline" href="#">Explore rollover IRA</a>
</div>
</div>
</div>
</div>
</div>
</section>
<!-- Planning Tools Section (Bento Grid) -->
<section class="py-xl bg-surface">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<div class="flex flex-col md:flex-row justify-between items-end mb-xl">
<div class="max-w-2xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Tools to help you plan</h2>
<p class="font-body-md text-body-md text-on-surface-variant">From creating a roadmap to calculating your future income, our digital tools provide the precision you need.</p>
</div>
<a class="text-institutional-blue font-label-md text-label-md hover:underline flex items-center gap-1" href="#">View all tools <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
</div>
<div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-sm">
<!-- Tool 1: Planning Center -->
<div class="md:col-span-2 bg-white p-lg rounded-xl border border-surface-gray flex flex-col justify-between hover:shadow-md transition-shadow">
<div>
<div class="w-12 h-12 bg-primary/10 text-primary rounded-full flex items-center justify-center mb-md">
<span class="material-symbols-outlined">assignment</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Planning Center</h3>
<p class="font-body-md text-body-md text-on-surface-variant mb-lg">Ready to create a free plan for your finances? We'll show you where you stand and provide guidance on how to reach your goals.</p>
<ul class="space-y-xs mb-lg">
<li class="flex items-center gap-xs font-body-sm text-body-sm"><span class="material-symbols-outlined text-fidelity-green text-[18px]">check_circle</span> Map out retirement scenarios</li>
<li class="flex items-center gap-xs font-body-sm text-body-sm"><span class="material-symbols-outlined text-fidelity-green text-[18px]">check_circle</span> Strategy for smarter saving</li>
<li class="flex items-center gap-xs font-body-sm text-body-sm"><span class="material-symbols-outlined text-fidelity-green text-[18px]">check_circle</span> Guidance on next steps</li>
</ul>
</div>
<a href="<?php echo htmlspecialchars($portfolioUrl); ?>" class="inline-block bg-fidelity-green text-white font-label-md text-label-md py-sm px-lg rounded-lg self-start hover:opacity-90 transition-all">Create your plan</a>
</div>
<!-- Tool 2: IRA Calculator -->
<div class="bg-white p-lg rounded-xl border border-surface-gray flex flex-col hover:shadow-md transition-shadow">
<div class="w-12 h-12 bg-secondary/10 text-secondary rounded-full flex items-center justify-center mb-md">
<span class="material-symbols-outlined">calculate</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-sm">IRA Calculator</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg">See which IRA you're eligible for and how much you could add to your account this year.</p>
<a class="mt-auto text-institutional-blue font-label-md text-label-md flex items-center gap-1 group" href="<?php echo htmlspecialchars($portfolioUrl); ?>">Calculate contribution <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">chevron_right</span></a>
</div>
<!-- Tool 3: Income Calculator -->
<div class="bg-white p-lg rounded-xl border border-surface-gray flex flex-col hover:shadow-md transition-shadow">
<div class="w-12 h-12 bg-tertiary/10 text-tertiary rounded-full flex items-center justify-center mb-md">
<span class="material-symbols-outlined">payments</span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface mb-sm">Income Calculator</h3>
<p class="font-body-sm text-body-sm text-on-surface-variant mb-lg">Find out how much money you'll have each month in retirement based on your current trajectory.</p>
<a class="mt-auto text-institutional-blue font-label-md text-label-md flex items-center gap-1 group" href="<?php echo htmlspecialchars($portfolioUrl); ?>">View monthly estimate <span class="material-symbols-outlined text-[16px] group-hover:translate-x-1 transition-transform">chevron_right</span></a>
</div>
</div>
</div>
</section>
<!-- Article Grid Section -->
<section class="py-xl">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-lg">Learn about saving and planning</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<?php foreach ($planningBlogPosts as $post):
    $blogUrl = '/blog/' . rawurlencode($post['slug']);
?>
<article class="group">
<a href="<?php echo htmlspecialchars($blogUrl); ?>" class="block">
<div class="aspect-video rounded-lg overflow-hidden mb-sm border border-surface-gray">
<img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="<?php echo htmlspecialchars($post['title']); ?>" src="<?php echo htmlspecialchars($post['image']); ?>">
</div>
<div class="flex items-center gap-xs text-on-surface-variant font-label-md text-label-md mb-xs">
<span class="uppercase tracking-widest text-[10px]">Article</span>
<span class="w-1 h-1 bg-outline rounded-full"></span>
<span><?php echo htmlspecialchars($post['read_time']); ?></span>
</div>
<h3 class="font-headline-md text-headline-md text-on-surface group-hover:text-institutional-blue transition-colors mb-xs"><?php echo htmlspecialchars($post['title']); ?></h3>
<p class="font-body-sm text-body-sm text-on-surface-variant"><?php echo htmlspecialchars($post['excerpt']); ?></p>
</a>
</article>
<?php endforeach; ?>
</div>
<div class="mt-lg text-center">
<a class="inline-flex items-center gap-1 text-institutional-blue font-label-md hover:underline" href="/blog">View all articles <span class="material-symbols-outlined text-[18px]">arrow_forward</span></a>
</div>
</div>
</section>
<!-- CTA Section -->
<section class="bg-institutional-blue py-xl text-white">
<div class="max-w-[1152px] mx-auto px-margin-desktop text-center">
<h2 class="font-headline-lg text-headline-lg mb-sm">Let's plan your retirement, together.</h2>
<p class="font-body-lg text-body-lg mb-lg opacity-90">Our advisors can help you align your goals with a personalized strategy.</p>
<div class="flex flex-col md:flex-row gap-md justify-center">
<button class="bg-white text-institutional-blue font-label-md text-label-md py-sm px-xl rounded-lg font-bold hover:bg-surface-container-low transition-all">Find an Advisor</button>
<button class="border border-white/30 bg-white/10 backdrop-blur-sm text-white font-label-md text-label-md py-sm px-xl rounded-lg font-bold hover:bg-white/20 transition-all">Call 800-343-3548</button>
</div>
</div>
</section>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body>
</html>
