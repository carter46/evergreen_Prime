<?php
require_once __DIR__ . '/includes/helpers.php';
$siteName = get_site_name();
$contactEmail = get_site_setting('contact_email', 'support@example.com');
$officeAddress = get_site_setting('office_address', '40 Bank Street, Canary Wharf<br/>London, E14 5NR<br/>United Kingdom');
$officeTitle = get_site_setting('office_title', 'London Office');
$pageTitle = 'Help Center | ' . $siteName;
?>
<!DOCTYPE html>
<html class="dark scroll-smooth" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php require_once __DIR__ . '/includes/marketing-head.php'; ?>
<style>
.help-hero-mesh {
  background-color: #0b0e11;
  background-image: radial-gradient(at 0% 0%, rgba(255, 195, 92, 0.08) 0px, transparent 50%), radial-gradient(at 100% 0%, rgba(255, 195, 92, 0.05) 0px, transparent 50%);
}
.help-card {
  background: #1d2023;
  border: 1px solid rgba(255, 255, 255, 0.08);
  border-radius: 1rem;
}
.help-input {
  width: 100%;
  background: #ffffff;
  border: 1px solid rgba(0, 0, 0, 0.1);
  border-radius: 0.5rem;
  padding: 0.75rem 1rem;
  color: #111417;
  font-size: 1rem;
  transition: border-color 0.2s, box-shadow 0.2s;
}
.help-input:focus {
  outline: none;
  border-color: #ffc35c;
  box-shadow: 0 0 0 2px rgba(255, 195, 92, 0.25);
}
.help-input::placeholder { color: #9ca3af; }
.help-faq-btn[aria-expanded="true"] .help-faq-chevron { transform: rotate(180deg); }
.help-faq-panel { display: none; }
.help-faq-panel.is-open { display: block; }
.help-cat-btn.is-active {
  background: rgba(255, 195, 92, 0.12);
  border-color: rgba(255, 195, 92, 0.35);
  color: #ffc35c;
}
</style>
</head>
<body class="marketing-page font-body-md text-body-md overflow-x-hidden bg-surface-container-lowest text-on-surface">
<?php $currentPage = 'help_centre'; require_once __DIR__ . '/includes/marketing-header.php'; ?>

<header class="help-hero-mesh pt-28 pb-12 md:pb-16 text-center border-b border-border-low">
<div class="max-w-container-max mx-auto px-4 md:px-margin-desktop">
<span class="px-4 py-1.5 bg-primary-container/10 text-primary-container text-label-xs font-bold rounded-full uppercase tracking-widest">Support Center</span>
<h1 class="mt-6 font-headline-lg text-headline-lg md:text-5xl font-bold text-on-surface">How can we help?</h1>
<p class="mt-4 text-on-secondary-container text-body-lg max-w-2xl mx-auto">
Our team is here around the clock for account, investment, and technical questions about <?php echo htmlspecialchars($siteName); ?>.
</p>
<div class="mt-8 flex flex-wrap items-center justify-center gap-3">
<a href="/live_chat" class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-container text-on-primary text-sm font-bold rounded-lg hover:opacity-90 transition-all">
<span class="material-symbols-outlined text-lg">forum</span>Start Live Chat
</a>
<a href="mailto:<?php echo htmlspecialchars($contactEmail); ?>" class="inline-flex items-center gap-2 px-5 py-2.5 border border-border-low text-on-surface text-sm font-bold rounded-lg hover:bg-surface-container-high transition-colors">
<span class="material-symbols-outlined text-lg">mail</span><?php echo htmlspecialchars($contactEmail); ?>
</a>
</div>
</div>
</header>

<main class="max-w-container-max mx-auto px-4 md:px-margin-desktop py-12 md:py-16 space-y-16 md:space-y-20">

<!-- Contact -->
<section class="grid lg:grid-cols-12 gap-8 lg:gap-10">
<div class="lg:col-span-7 help-card p-6 md:p-8">
<h2 class="font-headline-md text-headline-md text-on-surface mb-2">Send us a message</h2>
<p class="text-on-secondary-container text-sm mb-6">We typically respond within 2 hours during business days.</p>
<form id="contact-form" class="space-y-5">
<div class="grid sm:grid-cols-2 gap-5">
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest" for="contact-name">Full Name</label>
<input class="help-input" id="contact-name" name="name" placeholder="John Doe" type="text" required autocomplete="name"/>
</div>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest" for="contact-email">Email Address</label>
<input class="help-input" id="contact-email" name="email" placeholder="name@company.com" type="email" required autocomplete="email"/>
</div>
</div>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest" for="contact-subject">Subject</label>
<select class="help-input" id="contact-subject" name="subject" required>
<option value="Account Access">Account Access</option>
<option value="Investment Inquiry">Investment Inquiry</option>
<option value="Technical Issue">Technical Issue</option>
<option value="Verification (KYC)">Verification (KYC)</option>
<option value="Other">Other</option>
</select>
</div>
<div class="space-y-1.5">
<label class="font-label-sm text-label-sm text-on-surface-variant uppercase tracking-widest" for="contact-message">Message</label>
<textarea class="help-input resize-none min-h-[140px]" id="contact-message" name="message" placeholder="How can we help you?" rows="5" required></textarea>
</div>
<div id="contact-form-message" class="text-sm hidden"></div>
<button type="submit" class="w-full py-3.5 bg-primary-container text-on-primary font-bold rounded-lg hover:opacity-90 transition-all flex items-center justify-center gap-2">
<span>Send Message</span>
<span class="material-symbols-outlined text-lg">send</span>
</button>
</form>
</div>

<div class="lg:col-span-5 flex flex-col gap-5">
<div class="grid sm:grid-cols-2 lg:grid-cols-1 gap-5">
<div class="help-card p-6 hover:border-primary-container/30 transition-colors">
<div class="w-11 h-11 bg-primary-container/10 rounded-lg flex items-center justify-center mb-4 text-primary-container">
<span class="material-symbols-outlined">alternate_email</span>
</div>
<h3 class="font-bold text-on-surface">Email Support</h3>
<p class="text-on-secondary-container text-sm mt-1">Response within 2 hours</p>
<a class="mt-3 inline-block font-medium text-primary-container break-all" href="mailto:<?php echo htmlspecialchars($contactEmail); ?>"><?php echo htmlspecialchars($contactEmail); ?></a>
</div>
<div class="help-card p-6 hover:border-primary-container/30 transition-colors">
<div class="w-11 h-11 bg-primary-container/10 rounded-lg flex items-center justify-center mb-4 text-primary-container">
<span class="material-symbols-outlined">chat</span>
</div>
<div class="flex items-center gap-2">
<h3 class="font-bold text-on-surface">Live Chat</h3>
<span class="w-2 h-2 rounded-full bg-success animate-pulse"></span>
</div>
<p class="text-on-secondary-container text-sm mt-1">Available now</p>
<a href="/live_chat" class="mt-3 inline-flex items-center gap-1 font-medium text-primary-container">Start a conversation <span class="material-symbols-outlined text-sm">arrow_forward</span></a>
</div>
</div>
<div class="help-card overflow-hidden relative min-h-[220px] flex-1">
<div class="absolute inset-0 opacity-20 grayscale">
<img alt="" class="w-full h-full object-cover" src="https://lh3.googleusercontent.com/aida-public/AB6AXuBBaCT97QHY8jczDdMpk-_coaOuIUFij91kNWDyR-yPWqW1qvRuVWCxS8N4cX_WqBwjOlzvrPF8r7MZ6YvqpgjETnrThpa3eTlP5-LMg7NP8-aiHtBkQOTUlp9CsH8HuvOK3qtM0x8a0DS0gSXBjPv4xvTzYuLq_n8pWzxr5s4o1MzQI9SddnbbetV3JLGBcN6fFaeURdk1gXf_6ZLYKSflKAI1gqEPTPR8pNqd-lIW4DAP6_6ga1FPktTYaJz4XhIEPSXi67RxbwE"/>
</div>
<div class="relative z-10 p-6 h-full flex flex-col justify-end">
<div class="help-card p-5 max-w-xs bg-surface-dim/90 backdrop-blur-sm">
<div class="flex items-center gap-2 text-primary-container mb-2">
<span class="material-symbols-outlined text-lg">location_on</span>
<span class="font-label-xs text-label-xs uppercase tracking-widest">Global Headquarters</span>
</div>
<h3 class="font-bold text-on-surface mb-1"><?php echo htmlspecialchars($officeTitle); ?></h3>
<p class="text-on-secondary-container text-sm leading-relaxed"><?php echo $officeAddress; ?></p>
</div>
</div>
</div>
</div>
</section>

<!-- FAQ -->
<section id="faq">
<div class="flex flex-col lg:flex-row lg:items-end justify-between gap-6 mb-8">
<div class="max-w-xl">
<h2 class="font-headline-lg text-headline-lg text-on-surface">Frequently Asked Questions</h2>
<p class="text-on-secondary-container mt-3">Everything you need to know about the <?php echo htmlspecialchars($siteName); ?> platform and our investment tools.</p>
</div>
<div class="relative w-full lg:max-w-md">
<span class="material-symbols-outlined absolute left-4 top-1/2 -translate-y-1/2 text-on-surface-variant">search</span>
<input id="faq-search" class="help-input pl-12" placeholder="Search questions..." type="search" autocomplete="off"/>
</div>
</div>

<div class="grid lg:grid-cols-12 gap-8">
<aside class="lg:col-span-3 space-y-2" id="faq-categories">
<button type="button" class="help-cat-btn is-active w-full flex items-center justify-between p-4 rounded-lg border border-border-low text-left font-label-sm text-label-sm text-on-secondary-container transition-colors" data-faq-cat="all">
<span>All topics</span><span class="material-symbols-outlined text-lg">apps</span>
</button>
<button type="button" class="help-cat-btn w-full flex items-center justify-between p-4 rounded-lg border border-border-low text-left font-label-sm text-label-sm text-on-secondary-container transition-colors" data-faq-cat="general">
<span>General</span><span class="material-symbols-outlined text-lg">info</span>
</button>
<button type="button" class="help-cat-btn w-full flex items-center justify-between p-4 rounded-lg border border-border-low text-left font-label-sm text-label-sm text-on-secondary-container transition-colors" data-faq-cat="investments">
<span>Investments</span><span class="material-symbols-outlined text-lg">monitoring</span>
</button>
<button type="button" class="help-cat-btn w-full flex items-center justify-between p-4 rounded-lg border border-border-low text-left font-label-sm text-label-sm text-on-secondary-container transition-colors" data-faq-cat="security">
<span>Security</span><span class="material-symbols-outlined text-lg">verified_user</span>
</button>
<button type="button" class="help-cat-btn w-full flex items-center justify-between p-4 rounded-lg border border-border-low text-left font-label-sm text-label-sm text-on-secondary-container transition-colors" data-faq-cat="withdrawals">
<span>Withdrawals</span><span class="material-symbols-outlined text-lg">payments</span>
</button>
</aside>

<div class="lg:col-span-9 space-y-3" id="faq-list">
<?php
$faqs = [
  ['cat' => 'general', 'q' => 'What exactly is ' . $siteName . ' and how does the AI work?', 'a' => $siteName . ' is a fintech platform that uses proprietary algorithms to analyze crypto market volatility. The system helps execute trades across liquidity pools to pursue optimal returns for investors.'],
  ['cat' => 'investments', 'q' => 'What is the minimum investment required?', 'a' => 'You can start investing with as little as $500. Plans and minimums may vary — check Investment Plans in your dashboard for current options.'],
  ['cat' => 'investments', 'q' => 'Are my funds insured against market volatility?', 'a' => 'All investments carry risk. ' . $siteName . ' uses risk-management strategies and reserve policies designed to mitigate extreme events, but returns are not guaranteed and principal may be at risk.'],
  ['cat' => 'security', 'q' => 'How are my private keys and data secured?', 'a' => 'We use cold storage for the majority of funds and AES-256 encryption for sensitive data. We do not store full private keys on our servers.'],
  ['cat' => 'withdrawals', 'q' => 'How long do withdrawals take?', 'a' => 'Withdrawal processing times depend on asset type and network conditions. Most requests are reviewed within 24–48 hours after submission from your wallet page.'],
  ['cat' => 'general', 'q' => 'Do I need KYC to use the platform?', 'a' => 'KYC may be required for certain features depending on jurisdiction and account activity. Check Settings → KYC in your dashboard for your verification status.'],
];
foreach ($faqs as $i => $faq):
?>
<div class="help-card faq-item overflow-hidden" data-faq-category="<?php echo htmlspecialchars($faq['cat']); ?>">
<button type="button" class="help-faq-btn w-full flex items-center justify-between gap-4 p-5 md:p-6 text-left hover:bg-white/[0.02] transition-colors" aria-expanded="false" aria-controls="faq-panel-<?php echo $i; ?>">
<span class="font-bold text-on-surface pr-4"><?php echo htmlspecialchars($faq['q']); ?></span>
<span class="material-symbols-outlined text-primary-container help-faq-chevron shrink-0 transition-transform">expand_more</span>
</button>
<div id="faq-panel-<?php echo $i; ?>" class="help-faq-panel px-5 md:px-6 pb-5 md:pb-6 text-on-secondary-container leading-relaxed border-t border-border-low pt-4">
<?php echo htmlspecialchars($faq['a']); ?>
</div>
</div>
<?php endforeach; ?>
<p id="faq-empty" class="hidden text-center text-on-secondary-container py-12 help-card">No questions match your search. Try another keyword or contact support.</p>
</div>
</div>
</section>

<!-- CTA -->
<section>
<div class="help-card p-8 md:p-12 relative overflow-hidden">
<div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 bg-primary-container/15 rounded-full blur-3xl pointer-events-none"></div>
<div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
<div class="max-w-xl text-center md:text-left">
<h2 class="font-headline-md text-headline-md text-on-surface mb-3">Still have questions?</h2>
<p class="text-on-secondary-container text-body-lg">Our support team can walk you through deposits, plans, withdrawals, and account security.</p>
</div>
<div class="flex flex-col sm:flex-row gap-3 shrink-0 w-full sm:w-auto">
<a href="/live_chat" class="px-6 py-3.5 bg-primary-container text-on-primary font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-all">
<span class="material-symbols-outlined">forum</span>Start Live Chat
</a>
<a href="/register" class="px-6 py-3.5 border border-border-low text-on-surface font-bold rounded-lg hover:bg-surface-container-high transition-colors text-center">Create Account</a>
</div>
</div>
</div>
</section>

</main>

<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<?php require_once __DIR__ . '/includes/live-chat-widget.php'; ?>
<?php require_once __DIR__ . '/includes/app-script.php'; ?>
<script>
(function () {
  var faqButtons = document.querySelectorAll('.help-faq-btn');
  faqButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      var panel = document.getElementById(btn.getAttribute('aria-controls'));
      var isOpen = btn.getAttribute('aria-expanded') === 'true';
      faqButtons.forEach(function (b) {
        b.setAttribute('aria-expanded', 'false');
        var p = document.getElementById(b.getAttribute('aria-controls'));
        if (p) p.classList.remove('is-open');
      });
      if (!isOpen && panel) {
        btn.setAttribute('aria-expanded', 'true');
        panel.classList.add('is-open');
      }
    });
  });

  var catButtons = document.querySelectorAll('.help-cat-btn');
  var items = document.querySelectorAll('.faq-item');
  var search = document.getElementById('faq-search');
  var empty = document.getElementById('faq-empty');
  var activeCat = 'all';

  function applyFaqFilter() {
    var q = (search && search.value ? search.value : '').trim().toLowerCase();
    var visible = 0;
    items.forEach(function (item) {
      var cat = item.getAttribute('data-faq-category');
      var text = item.textContent.toLowerCase();
      var catOk = activeCat === 'all' || cat === activeCat;
      var searchOk = !q || text.indexOf(q) !== -1;
      var show = catOk && searchOk;
      item.style.display = show ? '' : 'none';
      if (show) visible++;
    });
    if (empty) empty.classList.toggle('hidden', visible > 0);
  }

  catButtons.forEach(function (btn) {
    btn.addEventListener('click', function () {
      catButtons.forEach(function (b) { b.classList.remove('is-active'); });
      btn.classList.add('is-active');
      activeCat = btn.getAttribute('data-faq-cat') || 'all';
      applyFaqFilter();
    });
  });
  if (search) search.addEventListener('input', applyFaqFilter);
})();
</script>
</body>
</html>
