<?php require_once __DIR__ . '/includes/helpers.php'; $siteName = get_site_name(); ?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title>Live Chat | <?php echo htmlspecialchars($siteName); ?></title>
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
                Our team is available 24/7 to assist you with any questions about your account, investments, or technical issues.
            </p>
</div>
<div id="chat-container" class="h-[500px] overflow-y-auto border border-slate-200 dark:border-zinc-700 rounded-lg p-4 mb-4 bg-slate-50 dark:bg-zinc-800/50 space-y-4">
<div class="flex items-start gap-3">
<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0">
<span class="material-icons text-white text-sm">support_agent</span>
</div>
<div class="flex-1 bg-white dark:bg-zinc-900 rounded-lg p-4 shadow-sm">
<p class="text-sm text-slate-700 dark:text-slate-300">Hello! How can we help you today?</p>
<span class="text-xs text-slate-400 mt-1 block">Just now</span>
</div>
</div>
</div>
<form id="chat-form" class="flex gap-2">
<input type="text" id="chat-input" placeholder="Type your message..." class="flex-1 px-4 py-3 rounded-lg border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-900 focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none" required/>
<button type="submit" class="px-6 py-3 bg-primary text-white font-bold rounded-lg hover:bg-opacity-90 transition-all flex items-center gap-2">
<span class="material-icons text-sm">send</span>
                Send
            </button>
</form>
<div class="mt-6 pt-6 border-t border-slate-200 dark:border-zinc-700">
<p class="text-sm text-slate-500 dark:text-slate-400 text-center">
                Need immediate assistance? <a href="mailto:<?php echo htmlspecialchars(get_site_setting('contact_email', 'support@bloombit.com')); ?>" class="text-primary font-semibold hover:underline">Email us</a> or call our support line.
            </p>
</div>
</div>
</main>
<?php require_once __DIR__ . '/includes/marketing-footer.php'; ?>
<script src="/js/app.js"></script>
<script>
(function(){
  var form = document.getElementById('chat-form');
  var input = document.getElementById('chat-input');
  var container = document.getElementById('chat-container');
  if (!form || !input || !container) return;
  form.addEventListener('submit', function(e){
    e.preventDefault();
    var msg = input.value.trim();
    if (!msg) return;
    // Add user message
    var userMsg = document.createElement('div');
    userMsg.className = 'flex items-start gap-3 justify-end';
    userMsg.innerHTML = '<div class="flex-1 bg-primary text-white rounded-lg p-4 shadow-sm max-w-[80%] text-right"><p class="text-sm">' + msg + '</p><span class="text-xs opacity-70 mt-1 block">Just now</span></div>';
    container.appendChild(userMsg);
    container.scrollTop = container.scrollHeight;
    input.value = '';
    // Simulate response
    setTimeout(function(){
      var botMsg = document.createElement('div');
      botMsg.className = 'flex items-start gap-3';
      botMsg.innerHTML = '<div class="w-8 h-8 rounded-full bg-primary flex items-center justify-center shrink-0"><span class="material-icons text-white text-sm">support_agent</span></div><div class="flex-1 bg-white dark:bg-zinc-900 rounded-lg p-4 shadow-sm"><p class="text-sm text-slate-700 dark:text-slate-300">Thank you for your message. Our support team will respond shortly. In the meantime, you can check our <a href="/help_centre" class="text-primary underline">Help Center</a> for answers to common questions.</p><span class="text-xs text-slate-400 mt-1 block">Just now</span></div>';
      container.appendChild(botMsg);
      container.scrollTop = container.scrollHeight;
    }, 1000);
  });
})();
</script>
</body></html>
