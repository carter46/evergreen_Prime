<?php
require_once __DIR__ . '/includes/helpers.php';
$pageTitle = 'Wealth Management Offerings | Fidelity';
?>
<!DOCTYPE html>
<html class="light" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
</head>
<body class="fidelity-subpage bg-background text-on-surface font-body-md antialiased overflow-x-hidden">
<?php $currentPage = 'wealth'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<main>
<!-- Hero Section -->
<section class="relative h-[600px] flex items-center overflow-hidden">
<div class="absolute inset-0 z-0">
<img class="w-full h-full object-cover" alt="Wealth management hero" src="https://lh3.googleusercontent.com/aida/AP1WRLsR85l8D372eDfmXNecliBwLlDNuClsvxZfCboMYaypMvuFgDsGI-B6DWB4WOe0EXzPpM48VEV5xcMs8kHhEjMEg6S0O0I278DWma30sPUtCImTb_B8LymGEk1O8SGNIeQVqsVdnAiKFa52sNIQHBpJuJ8TUe8-Xyaz-92n1zhsJdoq6HV_QF2Gpbjk3HNg1fAIj0qulpJdo3ght8vFXm-1a09gkq8ARe81WtvKuUsT96mCkW2zH8-i2S0o"/>
<div class="absolute inset-0 bg-gradient-to-r from-black/60 via-black/40 to-transparent"></div>
</div>
<div class="relative z-10 w-full px-margin-desktop max-w-[1152px] mx-auto">
<div class="max-w-2xl text-white">
<h1 class="font-display-lg text-display-lg mb-sm">A Wealth Management partner that can help turn your dreams into reality</h1>
<p class="font-body-lg text-body-lg text-white/90 mb-lg">With our personalized planning and investment management capabilities, we can help you reach your goals, no matter where you are in life.</p>
<div class="flex flex-wrap gap-sm">
<button class="bg-fidelity-green text-white px-xl py-md rounded font-label-md text-[16px] hover:opacity-90 transition-all shadow-lg active:scale-95">Find an advisor</button>
<button class="bg-white/10 backdrop-blur-md border border-white/30 text-white px-xl py-md rounded font-label-md text-[16px] hover:bg-white/20 transition-all active:scale-95">Connect with your advisor</button>
</div>
</div>
</div>
</section>
<!-- Offerings Grid -->
<section class="py-xl bg-surface-container-lowest">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<div class="text-center mb-xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xs">Ways to plan and invest for anything you want to achieve</h2>
<p class="text-on-surface-variant max-w-2xl mx-auto font-body-md">We support a broad range of financial needs with service and dedication. From retirement to building an enduring legacy.</p>
</div>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-gutter">
<!-- Fidelity Go -->
<div class="bento-card bg-white p-md rounded-lg flex flex-col h-full">
<div class="mb-lg">
<span class="text-fidelity-green text-label-md font-bold uppercase tracking-widest mb-xs block">Digital Advice</span>
<h3 class="font-headline-md text-headline-md mb-xs">Fidelity Go®</h3>
<p class="text-on-surface-variant text-body-sm h-12">Professional, affordable money management with automatic rebalancing.</p>
</div>
<div class="mt-auto border-t border-surface-gray pt-md">
<div class="flex justify-between items-center mb-md">
<span class="text-label-md text-on-surface-variant">General Eligibility</span>
<span class="font-bold text-on-surface">$0</span>
</div>
<a href="/register" class="block w-full text-center bg-fidelity-green text-white py-sm rounded font-label-md hover:opacity-90 transition-all">Get started</a>
</div>
</div>
<!-- Advisory Services -->
<div class="bento-card bg-white p-md rounded-lg flex flex-col h-full">
<div class="mb-lg">
<span class="text-institutional-blue text-label-md font-bold uppercase tracking-widest mb-xs block">Team Advice</span>
<h3 class="font-headline-md text-headline-md mb-xs">Advisory Services</h3>
<p class="text-on-surface-variant text-body-sm h-12">Connect with a team of advisors about your plans and portfolio strategy.</p>
</div>
<div class="mt-auto border-t border-surface-gray pt-md">
<div class="flex justify-between items-center mb-md">
<span class="text-label-md text-on-surface-variant">General Eligibility</span>
<span class="font-bold text-on-surface">$50,000</span>
</div>
<button class="w-full border border-institutional-blue text-institutional-blue py-sm rounded font-label-md hover:bg-institutional-blue/5 transition-all">Talk to a team member</button>
</div>
</div>
<!-- Wealth Management -->
<div class="bento-card bg-white p-md rounded-lg flex flex-col h-full border-fidelity-green/30 ring-1 ring-fidelity-green/5">
<div class="mb-lg">
<span class="text-fidelity-green text-label-md font-bold uppercase tracking-widest mb-xs block">Dedicated Advisor</span>
<h3 class="font-headline-md text-headline-md mb-xs">Wealth Management</h3>
<p class="text-on-surface-variant text-body-sm h-12">One-to-one proactive planning for your full financial picture.</p>
</div>
<div class="mt-auto border-t border-surface-gray pt-md">
<div class="flex justify-between items-center mb-md">
<span class="text-label-md text-on-surface-variant">General Eligibility</span>
<span class="font-bold text-on-surface">$500,000</span>
</div>
<button class="w-full bg-fidelity-green text-white py-sm rounded font-label-md hover:opacity-90 transition-all">Find an advisor</button>
</div>
</div>
<!-- Private Wealth -->
<div class="bento-card bg-on-surface text-white p-md rounded-lg flex flex-col h-full">
<div class="mb-lg">
<span class="text-primary-fixed-dim text-label-md font-bold uppercase tracking-widest mb-xs block">Advisor-Led Team</span>
<h3 class="font-headline-md text-headline-md mb-xs">Private Wealth</h3>
<p class="text-white/70 text-body-sm h-12">Concierge service and advanced planning for complex wealth needs.</p>
</div>
<div class="mt-auto border-t border-white/10 pt-md">
<div class="flex justify-between items-center mb-md">
<span class="text-label-md text-white/50">General Eligibility</span>
<span class="font-bold text-white">$2 Million</span>
</div>
<a href="/live_chat" class="block w-full text-center bg-white text-on-surface py-sm rounded font-label-md hover:opacity-90 transition-all">Contact us</a>
</div>
</div>
</div>
</div>
</section>
<!-- Features Section -->
<section class="py-xl bg-background">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-lg text-center">What Fidelity Wealth Management can offer you</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-lg">
<div class="text-center group">
<div class="w-20 h-20 mx-auto bg-surface-container rounded-full flex items-center justify-center mb-md group-hover:bg-institutional-blue/10 transition-colors">
<span class="material-symbols-outlined text-institutional-blue text-4xl" style="font-variation-settings: 'FILL' 1;">person</span>
</div>
<h4 class="font-headline-md text-headline-md mb-xs">A dedicated advisor</h4>
<p class="text-on-surface-variant text-body-md mb-sm">Your advisor will get to know what's important to you and create a customized wealth plan that evolves as your life does.</p>
<a class="text-institutional-blue font-label-md hover:underline inline-flex items-center gap-xs" href="#">
                            Learn how an advisor can help <span class="material-symbols-outlined text-[16px]">chevron_right</span>
</a>
</div>
<div class="text-center group">
<div class="w-20 h-20 mx-auto bg-surface-container rounded-full flex items-center justify-center mb-md group-hover:bg-fidelity-green/10 transition-colors">
<span class="material-symbols-outlined text-fidelity-green text-4xl" style="font-variation-settings: 'FILL' 1;">analytics</span>
</div>
<h4 class="font-headline-md text-headline-md mb-xs">Comprehensive planning</h4>
<p class="text-on-surface-variant text-body-md mb-sm">Prepare for what's next with an approach to planning built around what matters to you, from retirement to estate management.</p>
<a class="text-fidelity-green font-label-md hover:underline inline-flex items-center gap-xs" href="/planning">
                            Explore our planning approach <span class="material-symbols-outlined text-[16px]">chevron_right</span>
</a>
</div>
<div class="text-center group">
<div class="w-20 h-20 mx-auto bg-surface-container rounded-full flex items-center justify-center mb-md group-hover:bg-institutional-blue/10 transition-colors">
<span class="material-symbols-outlined text-institutional-blue text-4xl" style="font-variation-settings: 'FILL' 1;">query_stats</span>
</div>
<h4 class="font-headline-md text-headline-md mb-xs">Personalized strategy</h4>
<p class="text-on-surface-variant text-body-md mb-sm">We'll create an investment strategy tailored around your overall preferences and full household financial picture.</p>
<a class="text-institutional-blue font-label-md hover:underline inline-flex items-center gap-xs" href="/investing">
                            Explore our investing capabilities <span class="material-symbols-outlined text-[16px]">chevron_right</span>
</a>
</div>
</div>
</div>
</section>
<!-- Awards Section -->
<section class="py-lg bg-on-surface text-white">
<div class="max-w-[1152px] mx-auto px-margin-desktop flex flex-col md:flex-row items-center gap-lg">
<div class="flex-shrink-0 bg-white p-sm rounded-lg">
<img class="w-24 h-24 object-contain" alt="Kiplinger Readers Choice Award" src="https://lh3.googleusercontent.com/aida-public/AB6AXuAPnyfEwK6_jjCUdf9Xd7kw2S4J6ethZcT8E5mByV8_4FWOAsaQyyB9hOFmQLJdFhHMhm0YY8Z1k-inlwf5XC6h7fNtlZ28tOURA1MWqvn7Ktq8yqFLGPhuw2xwJoh7QjQUwZTO-BE153P-FDD9j1FGpsbvCRR0dffxtQcgy3JB5Qcatp_QotvMiL7CbKi0p9Wsn9s7IBddXa3lSL4fWc9ZwpB9Xz9ddkTdIZ_bskZRaCMStdVn06OcwQ"/>
</div>
<div>
<h3 class="font-headline-md text-headline-md mb-xs">Award-winning service that sets us apart</h3>
<p class="text-white/80 font-body-md">Fidelity Wealth Management was named the overall winner in Kiplinger's Readers' Choice Awards for its exceptional financial advice, trustworthiness, and client satisfaction.</p>
</div>
</div>
</section>
<!-- Insights/Articles Grid -->
<section class="py-xl bg-surface-container-low">
<div class="max-w-[1152px] mx-auto px-margin-desktop">
<h2 class="font-headline-lg text-headline-lg text-on-surface mb-xl">Insights and strategies from Fidelity Wealth Management</h2>
<div class="grid grid-cols-1 md:grid-cols-3 gap-gutter">
<!-- Article 1 -->
<div class="bg-white rounded-lg overflow-hidden bento-card border border-surface-gray flex flex-col">
<div class="h-48 relative">
<img class="w-full h-full object-cover" alt="Financial planning" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBL6aZIZdLW1QHvXAEess1G5kIjBKCka0-BpFRm4r0BrHSAV5bB0dEPPULUmGILVmJUl_dHIgGjv78_qszucLvEQiSmm72RPYEMbuiyS7Qo4XB9GgrOg3XtwpSp6UXvXH4ObbrzMKE4SePJPCS1H-PiFLW4tPssaM7r97gAl4LaNPWvGy3hAWh5A0lyX4RSMRpdXUGjHN1OfbbeqS75D1wapQZP1khv38oSpYiqiODZ2LppySrwFrIdiw"/>
</div>
<div class="p-md flex-grow flex flex-col">
<div class="flex items-center gap-sm mb-xs">
<span class="text-label-md text-on-surface-variant bg-surface-container px-xs py-base rounded">Article</span>
<span class="text-label-md text-on-surface-variant">6 min read</span>
</div>
<h4 class="font-headline-md text-headline-md mb-sm">What is financial planning?</h4>
<p class="text-on-surface-variant text-body-sm mb-md">Learn how a financial plan could help you reach your goals through strategic capital allocation.</p>
<a class="mt-auto text-institutional-blue font-label-md font-bold uppercase tracking-wider hover:opacity-70 transition-opacity" href="#">Read more</a>
</div>
</div>
<!-- Article 2 -->
<div class="bg-white rounded-lg overflow-hidden bento-card border border-surface-gray flex flex-col">
<div class="h-48 relative">
<img class="w-full h-full object-cover" alt="Building wealth" src="https://lh3.googleusercontent.com/aida-public/AB6AXuCBAww1P81qYKAvHS6oMvf0EK4bz-awnZAy0wLKhv2278WkzczeCQeigIV1IcUmdlLX7s7mq28Ga1W6fvExAG9UtrRxScJwGT8c0HNsk1m2_sisEXpOWm_RwxbB9CDnaEgPUE6Zh-rTi5QxOv8tQ9-TvkAeXt4ecYmS8sxEzVnlADqwKpq3Z3ZQEvqCqJfOYDCGoy2neHvtg7Mo-8_PPRdKyXxKwsAmvS094tlFP635xCHuoHB9Q-m5iw"/>
</div>
<div class="p-md flex-grow flex flex-col">
<div class="flex items-center gap-sm mb-xs">
<span class="text-label-md text-on-surface-variant bg-surface-container px-xs py-base rounded">Article</span>
<span class="text-label-md text-on-surface-variant">5 min read</span>
</div>
<h4 class="font-headline-md text-headline-md mb-sm">3 effective strategies for building wealth</h4>
<p class="text-on-surface-variant text-body-sm mb-md">Discover core principles that drive long-term asset accumulation and risk management.</p>
<a class="mt-auto text-institutional-blue font-label-md font-bold uppercase tracking-wider hover:opacity-70 transition-opacity" href="#">Read more</a>
</div>
</div>
<!-- Article 3 -->
<div class="bg-white rounded-lg overflow-hidden bento-card border border-surface-gray flex flex-col">
<div class="h-48 relative">
<img class="w-full h-full object-cover" alt="Planning together" src="https://lh3.googleusercontent.com/aida-public/AB6AXuDfo2hnQ4UvfDZ-hQFhIWf0ewrjlcAElJLPrmtJc8f2eBBy42qnYLsldttedGlayn6qJ5ZGqbK_3x6-VpDHpSy8Xe-h1BhPepKOy7fVvJd2wTq-hr5wBNhnA6YKfneXMj2RCFk10tXsGZke_ylSdmmUcYUVGT7Y9XTS9cKN2DKbRvLvOsIv9SvF7Na1ut9AaTIhYbZJJ6zwXfr78O589Gam6637sacT2tkyMzikDUE-kkST7Ibu8jiEuw"/>
</div>
<div class="p-md flex-grow flex flex-col">
<div class="flex items-center gap-sm mb-xs">
<span class="text-label-md text-on-surface-variant bg-surface-container px-xs py-base rounded">Article</span>
<span class="text-label-md text-on-surface-variant">6 min read</span>
</div>
<h4 class="font-headline-md text-headline-md mb-sm">The power of planning together</h4>
<p class="text-on-surface-variant text-body-sm mb-md">Why transparent financial communication within households is critical for generational wealth.</p>
<a class="mt-auto text-institutional-blue font-label-md font-bold uppercase tracking-wider hover:opacity-70 transition-opacity" href="#">Read more</a>
</div>
</div>
</div>
</div>
</section>
<!-- CTA Section -->
<section class="py-xl bg-fidelity-green text-white relative overflow-hidden">
<div class="absolute inset-0 opacity-10 pointer-events-none">
<svg class="w-full h-full" preserveAspectRatio="none" viewBox="0 0 100 100">
<path d="M0 100 Q 25 25 50 50 T 100 0" fill="none" stroke="white" stroke-width="0.5"></path>
</svg>
</div>
<div class="max-w-[1152px] mx-auto px-margin-desktop text-center relative z-10">
<h2 class="font-display-lg text-display-lg mb-sm">Let's talk about your goals</h2>
<p class="font-body-lg text-body-lg text-white/90 mb-lg max-w-2xl mx-auto">A conversation with an advisor is the first step to helping you find the right approach to reach your life's ambitions.</p>
<div class="flex flex-wrap justify-center gap-md">
<button class="bg-white text-fidelity-green px-xl py-md rounded font-label-md text-[16px] font-bold hover:bg-surface-gray transition-all shadow-xl active:scale-95">Find an advisor</button>
<button class="border-2 border-white text-white px-xl py-md rounded font-label-md text-[16px] font-bold hover:bg-white/10 transition-all active:scale-95">Call 800-343-3548</button>
</div>
</div>
</section>
</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>

<script>
(function () {
    var observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('opacity-100', 'translate-y-0');
                entry.target.classList.remove('opacity-0', 'translate-y-8');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);
    document.querySelectorAll('.bento-card').forEach(function (card) {
        card.classList.add('opacity-0', 'translate-y-8', 'transition-all', 'duration-700');
        observer.observe(card);
    });
})();
</script>
</body>
</html>
