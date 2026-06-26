<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); ?>
<!DOCTYPE html>
<html class="dark" lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<?php
$pageTitle = 'Live Chat | ' . $siteName;
require_once __DIR__ . '/includes/marketing-head.php';
?>
<style>
        .smartsupp-widget {
            --smartsupp-primary-color: #ffc35c !important;
        }
    </style>
</head>
<body class="marketing-page font-body-md text-body-md overflow-x-hidden">
<?php $currentPage = 'live_chat'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-slate-200 dark:border-white/5 p-8">
<h1 class="text-3xl font-bold mb-6 flex items-center gap-3">
<span class="material-icons text-primary">forum</span>
Live Chat — <?php echo htmlspecialchars($siteName); ?>
</h1>
<div class="bg-slate-50 dark:bg-zinc-800 rounded-xl p-6 mb-6">
<div class="flex items-center gap-3 mb-4">
<span class="w-3 h-3 rounded-full bg-green-500 animate-pulse"></span>
<span class="font-semibold">Support agents are online</span>
</div>
<p class="text-slate-600 dark:text-slate-400 text-sm">
                Our team is available 24/7 to assist you with any questions about your account, investments, or technical issues. Use the chat widget in the bottom right corner to start a conversation.
            </p>
</div>
<div class="mt-6 pt-6 border-t border-slate-200 dark:border-zinc-700">
<p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                Need immediate assistance? <a href="mailto:<?php echo htmlspecialchars(get_site_setting('contact_email', 'support@example.com')); ?>" class="text-primary font-semibold hover:underline">Email us</a> or call our support line.
            </p>
</div>
</div>
</main>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
</body></html>
