<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); 
$smartsuppKey = get_site_setting('smartsupp_key', '6fe6ebe5789e92d09f1a2fd405bd5b7d7967835d');
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Live Chat | <?php echo htmlspecialchars($siteName); ?></title>
<?php output_favicon_tags(); ?>
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
                        "primary": "#f9bd0b",
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
        /* Smartsupp widget customization */
        .smartsupp-widget {
            --smartsupp-primary-color: #f9bd0b !important;
        }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark text-slate-900 dark:text-slate-100 font-display transition-colors duration-300 overflow-x-hidden">
<?php $currentPage = 'live_chat'; require_once __DIR__ . '/includes/marketing-header.php'; ?>
<main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
<div class="bg-white dark:bg-zinc-900 rounded-2xl shadow-xl border border-slate-200 dark:border-white/5 p-8">
<h1 class="text-3xl font-bold mb-6 flex items-center gap-3">
<span class="material-icons text-primary">forum</span>
                Live Chat Support
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
                Need immediate assistance? <a href="mailto:<?php echo htmlspecialchars(get_site_setting('contact_email', 'support@bloombit.com')); ?>" class="text-primary font-semibold hover:underline">Email us</a> or call our support line.
            </p>
</div>
</div>
</main>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<script src="/js/app.js"></script>
<!-- Smartsupp Live Chat script -->
<script type="text/javascript">
var _smartsupp = _smartsupp || {};
_smartsupp.key = '<?php echo htmlspecialchars($smartsuppKey); ?>';
_smartsupp.widget = {
    colors: {
        primary: '#f9bd0b',
        secondary: '#231e0f'
    }
};
window.smartsupp||(function(d) {
  var s,c,o=smartsupp=function(){ o._.push(arguments)};o._=[];
  s=d.getElementsByTagName('script')[0];c=d.createElement('script');
  c.type='text/javascript';c.charset='utf-8';c.async=true;
  c.src='https://www.smartsuppchat.com/loader.js?';s.parentNode.insertBefore(c,s);
})(document);
</script>
<noscript>Powered by <a href="https://www.smartsupp.com" target="_blank">Smartsupp</a></noscript>
</body></html>
