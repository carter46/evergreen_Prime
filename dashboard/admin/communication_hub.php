<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
$currentPage = 'communication';
$broadcastHistory = [];
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $chk = $pdo->query("SHOW TABLES LIKE 'broadcast_campaigns'");
    if ($chk && $chk->rowCount() > 0) {
        $stmt = $pdo->query('SELECT id, subject, recipients_filter, total_recipients, status, sent_at FROM broadcast_campaigns ORDER BY sent_at DESC LIMIT 20');
        $broadcastHistory = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }
} catch (Throwable $e) {}
?>
<!DOCTYPE html>
<html lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Admin Broadcast Hub</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
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
                        "display": ["Inter", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },
        }
    </script>
<style>
        body { font-family: 'Inter', sans-serif; }
        .editor-toolbar button:hover { background-color: rgba(249, 189, 11, 0.2); }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
    </style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 transition-colors duration-300 overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8 min-h-screen">
<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<nav class="flex text-xs text-slate-400 gap-2 mb-1">
<span>Admin</span>
<span>/</span>
<span class="text-slate-600">Communications Hub</span>
</nav>
<h1 class="text-2xl font-bold text-slate-900">Broadcast &amp; Communication Hub</h1>
</div>
<div class="flex items-center gap-3">
<button type="submit" form="broadcast-form" class="px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-icons-outlined text-base">send</span> Send Broadcast
                </button>
</div>
</header>
<div class="grid grid-cols-12 gap-8">
<!-- Composition Area -->
<div class="col-span-12 space-y-6">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-700 flex items-center gap-2">
<span class="material-icons-outlined text-xl text-primary">email</span>
<h2 class="text-base font-bold text-slate-900 dark:text-white">Email Broadcast</h2>
</div>
<form id="broadcast-form" class="p-6 space-y-6">
<!-- Message Metadata -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Campaign Name</label>
<input name="campaign" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. Q4 Growth Update" type="text"/>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Subject Line</label>
<input name="subject" class="w-full bg-slate-50 border-slate-200 rounded-lg focus:ring-primary focus:border-primary" placeholder="Exciting updates for your Bloombit portfolio" type="text" required/>
</div>
</div>
<!-- Rich Text Editor -->
<div class="space-y-2">
<div class="flex items-center justify-between">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Message Body</label>
<div class="flex gap-2">
<button type="button" class="px-2 py-1 bg-slate-100 dark:bg-zinc-700 text-[10px] font-bold text-slate-500 dark:text-slate-400 rounded hover:bg-slate-200 dark:hover:bg-zinc-600 uppercase tracking-tighter" data-insert-placeholder="{user_name}">Insert {user_name}</button>
<button type="button" class="px-2 py-1 bg-slate-100 dark:bg-zinc-700 text-[10px] font-bold text-slate-500 dark:text-slate-400 rounded hover:bg-slate-200 dark:hover:bg-zinc-600 uppercase tracking-tighter" data-insert-placeholder="{balance}">Insert {balance}</button>
</div>
</div>
<div class="border border-slate-200 rounded-lg overflow-hidden">
<div class="editor-toolbar bg-slate-50 border-b border-slate-200 p-2 flex gap-1">
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_bold</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_italic</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">format_list_bulleted</span></button>
<div class="w-px h-4 bg-slate-300 mx-1 self-center"></div>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">link</span></button>
<button class="p-1.5 rounded hover:bg-slate-200 text-slate-600"><span class="material-icons-outlined text-sm">image</span></button>
</div>
<textarea name="body" class="w-full border-none focus:ring-0 p-4 text-slate-700 leading-relaxed" placeholder="Write your message here. Use placeholders for dynamic content..." rows="8" required></textarea>
</div>
</div>
<div id="broadcast-message" class="text-sm hidden"></div>
<div class="flex flex-col sm:flex-row gap-3">
<button type="button" id="broadcast-send-test" class="sm:w-auto px-6 py-2 bg-white dark:bg-zinc-800 text-slate-700 dark:text-slate-200 font-bold rounded-lg border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 flex items-center justify-center gap-2">
<span class="material-icons-outlined text-base">send_to_mobile</span> Send Test to My Email
</button>
<button type="submit" class="sm:w-auto px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-icons-outlined text-base">send</span> Send Broadcast
</button>
</div>
</form>
</div>
</div>
<!-- History Table -->
<div class="col-span-12">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div class="p-6 border-b border-slate-100 flex items-center justify-between">
<h3 class="text-lg font-bold text-slate-900">Broadcast History</h3>
<div class="flex gap-2">
<div class="relative">
<span class="material-icons-outlined absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm">search</span>
<input class="pl-9 pr-4 py-1.5 text-xs bg-slate-50 border-slate-200 rounded-lg" placeholder="Search campaigns..." type="text"/>
</div>
</div>
</div>
<div class="overflow-x-auto">
<table class="w-full text-left">
<thead class="bg-slate-50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
<tr>
<th class="px-6 py-4">Campaign Name &amp; Type</th>
<th class="px-6 py-4">Total Recipients</th>
<th class="px-6 py-4">Performance</th>
<th class="px-6 py-4">Status</th>
<th class="px-6 py-4">Date Sent</th>
<th class="px-6 py-4 text-right">Actions</th>
</tr>
</thead>
<tbody class="divide-y divide-slate-100 dark:divide-zinc-700">
<?php if (empty($broadcastHistory)): ?>
<tr><td colspan="6" class="px-6 py-12 text-center text-slate-500 dark:text-slate-400">No broadcasts sent yet.</td></tr>
<?php else: ?>
<?php foreach ($broadcastHistory as $bc): ?>
<tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/50 transition-colors">
<td class="px-6 py-4">
<div class="flex items-center gap-3">
<div class="p-2 bg-primary/10 text-primary rounded-lg"><span class="material-icons-outlined text-sm">email</span></div>
<div>
<p class="text-sm font-semibold text-slate-900 dark:text-white"><?php echo htmlspecialchars($bc['subject']); ?></p>
<p class="text-xs text-slate-400"><?php echo htmlspecialchars($bc['recipients_filter']); ?></p>
</div>
</div>
</td>
<td class="px-6 py-4"><span class="text-sm font-medium text-slate-700 dark:text-slate-300"><?php echo (int)$bc['total_recipients']; ?></span></td>
<td class="px-6 py-4">—</td>
<td class="px-6 py-4"><span class="px-2 py-1 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-[10px] font-bold rounded-full uppercase">Sent</span></td>
<td class="px-6 py-4"><p class="text-xs text-slate-600 dark:text-slate-400"><?php echo date('M j, Y', strtotime($bc['sent_at'])); ?></p><p class="text-[10px] text-slate-400"><?php echo date('g:i A', strtotime($bc['sent_at'])); ?></p></td>
<td class="px-6 py-4 text-right"></td>
</tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>
</div>
<div class="p-4 border-t border-slate-100 dark:border-zinc-700">
<p class="text-xs text-slate-500 dark:text-slate-400 font-medium"><?php echo count($broadcastHistory); ?> campaign(s)</p>
</div>
</div>
</div>
</div>
</div>
</main>
</div>
<script src="/js/app.js"></script>
<script>
(function(){
  var form = document.getElementById('broadcast-form');
  var msgEl = document.getElementById('broadcast-message');
  var testBtn = document.getElementById('broadcast-send-test');

  function doBroadcast(isTest){
    var subj = form.querySelector('[name="subject"]').value.trim();
    var body = form.querySelector('[name="body"]').value.trim();
    if (!subj || !body) { if (msgEl) { msgEl.textContent = 'Subject and body required.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
    if (msgEl) msgEl.classList.add('hidden');
    var btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
    var payload = { subject: subj, body: body };
    if (isTest) payload.test = true;
    fetch('/api/admin/broadcast.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    }).then(function(r){ return r.json(); }).then(function(res){
      if (msgEl) {
        msgEl.textContent = res.success ? (res.data && res.data.message) : (res.error || 'Failed');
        msgEl.className = 'text-sm ' + (res.success ? 'text-green-600' : 'text-red-600');
        msgEl.classList.remove('hidden');
      }
      if (res.success && !isTest) setTimeout(function(){ location.reload(); }, 1500);
      if (btn) btn.disabled = false;
    }).catch(function(){
      if (msgEl) { msgEl.textContent = 'Request failed.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); }
      if (btn) btn.disabled = false;
    });
  }

  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      doBroadcast(false);
    });
  }
  if (testBtn) {
    testBtn.addEventListener('click', function(){
      var subj = form.querySelector('[name="subject"]').value.trim();
      var body = form.querySelector('[name="body"]').value.trim();
      if (!subj || !body) { alert('Enter subject and body first.'); return; }
      testBtn.disabled = true;
      doBroadcast(true);
      setTimeout(function(){ testBtn.disabled = false; }, 2000);
    });
  }

  document.querySelectorAll('button[data-insert-placeholder]').forEach(function(btn){
    btn.addEventListener('click', function(){
      var ta = form.querySelector('[name="body"]');
      if (ta) ta.value += ' ' + btn.dataset.insertPlaceholder;
    });
  });
})();
</script>
</body></html>