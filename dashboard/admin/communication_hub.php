<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
$currentPage = 'communication';
$inbox = [];
$sentMail = [];
$selectedMail = null;
$selectedBox = '';
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $chk2 = $pdo->query("SHOW TABLES LIKE 'admin_mailbox'");
    if ($chk2 && $chk2->rowCount() > 0) {
        $stmt = $pdo->query("SELECT id, source, from_email, from_name, subject, status, created_at FROM admin_mailbox WHERE direction = 'in' ORDER BY created_at DESC LIMIT 20");
        $inbox = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
        $stmt = $pdo->query("SELECT id, source, to_emails, subject, status, created_at FROM admin_mailbox WHERE direction = 'out' ORDER BY created_at DESC LIMIT 20");
        $sentMail = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];

        $selectedBox = (string)($_GET['box'] ?? '');
        $selectedId = (int)($_GET['id'] ?? 0);
        if ($selectedId > 0 && in_array($selectedBox, ['in', 'out'], true)) {
            $stmt = $pdo->prepare('SELECT * FROM admin_mailbox WHERE id = ? AND direction = ? LIMIT 1');
            $stmt->execute([$selectedId, $selectedBox]);
            $selectedMail = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }
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
<?php if (!empty($selectedMail)): ?>
<div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4">
  <div class="w-full max-w-3xl bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-2xl overflow-hidden">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between gap-4">
      <div class="min-w-0">
        <p class="text-xs text-slate-500 dark:text-slate-400"><?php echo $selectedMail['direction'] === 'in' ? 'Inbox' : 'Sent'; ?></p>
        <h3 class="text-lg font-bold text-slate-900 dark:text-white truncate"><?php echo htmlspecialchars($selectedMail['subject'] ?? ''); ?></h3>
      </div>
      <a href="/dashboard/admin/communication_hub" class="shrink-0 px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800">Close</a>
    </div>
    <div class="p-4 space-y-3">
      <?php if (($selectedMail['direction'] ?? '') === 'in'): ?>
        <div class="text-sm text-slate-700 dark:text-slate-300 space-y-1">
          <div><span class="font-bold">From:</span> <?php echo htmlspecialchars(trim(($selectedMail['from_name'] ?? '') . ' <' . ($selectedMail['from_email'] ?? '') . '>')); ?></div>
          <div><span class="font-bold">To:</span> <?php echo htmlspecialchars($selectedMail['to_emails'] ?? (get_site_setting('contact_email', '') ?? '')); ?></div>
        </div>
      <?php else: ?>
        <div class="text-sm text-slate-700 dark:text-slate-300 space-y-1">
          <div><span class="font-bold">To:</span> <?php echo htmlspecialchars($selectedMail['to_emails'] ?? ''); ?></div>
        </div>
      <?php endif; ?>
      <div class="text-xs text-slate-500 dark:text-slate-400"><?php echo htmlspecialchars($selectedMail['created_at'] ?? ''); ?></div>
      <div class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed">
        <?php
          $bt = (string)($selectedMail['body_text'] ?? '');
          echo $bt !== '' ? nl2br(htmlspecialchars($bt)) : '<span class="text-slate-500">No message body stored.</span>';
        ?>
      </div>
      <?php if (($selectedMail['direction'] ?? '') === 'in' && !empty($selectedMail['from_email'])): ?>
        <div class="pt-2 border-t border-slate-100 dark:border-zinc-700">
          <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Reply</label>
          <textarea id="reply-body" class="w-full bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg p-3 text-sm" rows="5" placeholder="Type your reply..."></textarea>
          <div class="flex items-center gap-3 mt-3">
            <button type="button" id="reply-send" data-reply-id="<?php echo (int)$selectedMail['id']; ?>" class="px-4 py-2 bg-primary text-slate-900 font-bold rounded-lg hover:opacity-90">Send Reply</button>
            <div id="reply-msg" class="text-sm hidden"></div>
          </div>
        </div>
      <?php endif; ?>
      <?php if (!empty($selectedMail['error_text'])): ?>
        <div class="text-xs text-red-600">Error: <?php echo htmlspecialchars($selectedMail['error_text']); ?></div>
      <?php endif; ?>
    </div>
  </div>
</div>
<?php endif; ?>
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
<button type="button" id="mail-sync" class="px-4 py-2 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-slate-200 rounded-lg flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
<span class="material-icons-outlined text-base">sync</span> Sync Mailbox
</button>
<button type="submit" form="broadcast-form" class="px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center gap-2 hover:opacity-90 transition-opacity">
<span class="material-icons-outlined text-base">send</span> Send Broadcast
                </button>
</div>
</header>
<div id="mail-sync-msg" class="text-sm hidden mb-4"></div>
<div class="grid grid-cols-12 gap-8">
<!-- Composition Area -->
<div class="col-span-12 space-y-6">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-700 flex items-center gap-2">
<span class="material-icons-outlined text-xl text-primary">email</span>
<h2 class="text-base font-bold text-slate-900 dark:text-white">Compose Email</h2>
</div>
<form id="broadcast-form" class="p-6 space-y-6">
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
<div class="lg:col-span-2 space-y-2">
<label class="text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">To (External Emails)</label>
<input name="to" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. user@gmail.com, partner@company.com" type="text"/>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Comma or space separated. Leave empty if sending only to registered users.</p>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Send To Registered Users</label>
<select name="recipients" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary">
  <option value="">No (external only)</option>
  <option value="all">All Users</option>
  <option value="active_investors">Active Investors</option>
  <option value="kyc_verified">KYC Verified</option>
</select>
<p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Choose a segment to include users.</p>
</div>
</div>
<div class="space-y-2">
<label class="text-sm font-semibold text-slate-600 uppercase tracking-wider">Subject</label>
<input name="subject" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" placeholder="Subject line" type="text" required/>
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
<textarea name="body" class="w-full bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary p-4 text-slate-700 dark:text-slate-200 leading-relaxed" placeholder="Write your message here. Use placeholders for dynamic content..." rows="10" required></textarea>
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

<!-- Mailbox -->
<div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
  <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2"><span class="material-icons-outlined text-base">inbox</span> Inbox (Contact Form)</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-zinc-800 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
          <tr>
            <th class="px-4 py-3">From</th>
            <th class="px-4 py-3">Subject</th>
            <th class="px-4 py-3">When</th>
            <th class="px-4 py-3 text-right"> </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-700">
        <?php if (empty($inbox)): ?>
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No inbox messages yet.</td></tr>
        <?php else: foreach ($inbox as $m): ?>
          <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/50">
            <td class="px-4 py-3 text-sm text-slate-700 dark:text-slate-300"><?php echo htmlspecialchars(($m['from_name'] ?: $m['from_email']) ?? ''); ?></td>
            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($m['subject'] ?? ''); ?></td>
            <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400"><?php echo time_ago($m['created_at']); ?></td>
            <td class="px-4 py-3 text-right"><a class="text-xs font-bold text-primary hover:underline" href="/dashboard/admin/communication_hub?box=in&amp;id=<?php echo (int)$m['id']; ?>">Open</a></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2"><span class="material-icons-outlined text-base">send</span> Sent</h3>
    </div>
    <div class="overflow-x-auto">
      <table class="w-full text-left">
        <thead class="bg-slate-50 dark:bg-zinc-800 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
          <tr>
            <th class="px-4 py-3">To</th>
            <th class="px-4 py-3">Subject</th>
            <th class="px-4 py-3">When</th>
            <th class="px-4 py-3 text-right"> </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 dark:divide-zinc-700">
        <?php if (empty($sentMail)): ?>
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No sent emails yet.</td></tr>
        <?php else: foreach ($sentMail as $m): ?>
          <tr class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/50">
            <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300"><?php echo htmlspecialchars($m['to_emails'] ?? ''); ?></td>
            <td class="px-4 py-3 text-sm font-medium text-slate-900 dark:text-white"><?php echo htmlspecialchars($m['subject'] ?? ''); ?></td>
            <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400"><?php echo time_ago($m['created_at']); ?></td>
            <td class="px-4 py-3 text-right"><a class="text-xs font-bold text-primary hover:underline" href="/dashboard/admin/communication_hub?box=out&amp;id=<?php echo (int)$m['id']; ?>">Open</a></td>
          </tr>
        <?php endforeach; endif; ?>
        </tbody>
      </table>
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
  var syncBtn = document.getElementById('mail-sync');
  var syncMsg = document.getElementById('mail-sync-msg');

  function showInline(el, text, ok){
    if (!el) return;
    el.textContent = text;
    el.className = 'text-sm ' + (ok ? 'text-green-600' : 'text-red-600');
    el.classList.remove('hidden');
  }

  if (syncBtn) {
    syncBtn.addEventListener('click', function(){
      syncBtn.disabled = true;
      showInline(syncMsg, 'Syncing mailbox…', true);
      fetch('/api/admin/mail-sync.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ limit: 30 })
      }).then(function(r){ return r.json(); }).then(function(res){
        if (res && res.success) {
          showInline(syncMsg, 'Mailbox synced. Imported ' + (res.data && res.data.imported) + ' message(s).', true);
          setTimeout(function(){ location.reload(); }, 800);
        } else {
          showInline(syncMsg, (res && res.error) ? res.error : 'Sync failed.', false);
          syncBtn.disabled = false;
        }
      }).catch(function(){
        showInline(syncMsg, 'Sync failed.', false);
        syncBtn.disabled = false;
      });
    });
  }

  function doBroadcast(isTest){
    var subj = form.querySelector('[name="subject"]').value.trim();
    var body = form.querySelector('[name="body"]').value.trim();
    var to = (form.querySelector('[name="to"]') || {}).value || '';
    var recSel = form.querySelector('[name="recipients"]');
    var rec = recSel ? (recSel.value || '') : '';
    if (!subj || !body) { if (msgEl) { msgEl.textContent = 'Subject and body required.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
    if (!isTest && !(to && to.trim()) && !rec) { if (msgEl) { msgEl.textContent = 'Add at least one recipient (external emails or a user segment).'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
    if (msgEl) msgEl.classList.add('hidden');
    var btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
    var payload = { subject: subj, body: body };
    if (to && to.trim()) payload.to = to.trim();
    if (rec) { payload.recipients = rec; payload.include_users = true; }
    else { payload.include_users = false; }
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

  var replySend = document.getElementById('reply-send');
  if (replySend) {
    replySend.addEventListener('click', function(){
      var id = parseInt(replySend.getAttribute('data-reply-id') || '0', 10);
      var body = (document.getElementById('reply-body') || {}).value || '';
      var rMsg = document.getElementById('reply-msg');
      if (!id || !body.trim()) { showInline(rMsg, 'Reply body is required.', false); return; }
      replySend.disabled = true;
      fetch('/api/admin/mail-reply.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        credentials: 'same-origin',
        body: JSON.stringify({ id: id, body: body.trim() })
      }).then(function(r){ return r.json(); }).then(function(res){
        showInline(rMsg, res && res.success ? 'Reply sent.' : ((res && res.error) || 'Failed to send reply.'), !!(res && res.success));
        if (res && res.success) setTimeout(function(){ location.href = '/dashboard/admin/communication_hub'; }, 800);
        replySend.disabled = false;
      }).catch(function(){
        showInline(rMsg, 'Failed to send reply.', false);
        replySend.disabled = false;
      });
    });
  }
})();
</script>
</body></html>