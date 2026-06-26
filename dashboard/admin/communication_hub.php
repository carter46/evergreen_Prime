<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
$currentPage = 'communication';
$usersList = [];
$imapSentFolder = (string) (trim((string)(get_site_setting('mail_imap_sent_folder', '') ?? '')) ?: 'Sent');
try {
    $pdo = require __DIR__ . '/../../includes/db.php';
    $stmt = $pdo->query("SELECT id, name, email FROM users WHERE role = 'user' AND active = 1 ORDER BY id DESC LIMIT 500");
    $usersList = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
} catch (Throwable $e) {}

$pageTitle = $siteName . ' | Admin Broadcast Hub';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
?>
<style>
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 10px; }
</style>
<?php
$pageHeading = 'Broadcast & Communication Hub';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<div id="mail-modal" class="fixed inset-0 bg-black/50 z-50 p-4 flex items-center justify-center overflow-y-auto hidden">
  <div class="w-full max-w-3xl max-h-[90vh] my-auto flex flex-col bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 shadow-2xl overflow-hidden shrink-0">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between gap-4 shrink-0">
      <div class="min-w-0">
        <p id="mail-modal-label" class="text-xs text-slate-500 dark:text-slate-400"></p>
        <h3 id="mail-modal-subject" class="text-lg font-bold text-slate-900 dark:text-white truncate"></h3>
      </div>
      <button type="button" id="mail-modal-close" class="shrink-0 px-3 py-1.5 text-xs font-bold rounded-lg border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800">Close</button>
    </div>
    <div id="mail-modal-body" class="p-4 space-y-3 overflow-y-auto min-h-0 flex-1">
      <div class="text-center py-8 text-slate-500"><span class="material-symbols-outlined animate-spin">sync</span> Loading...</div>
    </div>
  </div>
</div>
<div class="grid grid-cols-12 gap-8">
<!-- Composition Area -->
<div class="col-span-12 space-y-6">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div class="px-6 py-4 border-b border-slate-100 dark:border-zinc-700 flex items-center gap-2">
<span class="material-symbols-outlined text-xl text-primary">email</span>
<h2 class="text-base font-bold text-slate-900 dark:text-white">Compose Email</h2>
</div>
<form id="broadcast-form" class="p-6 space-y-6">
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
  <div class="space-y-2">
    <label class="text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Audience</label>
    <select id="compose-audience" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary">
      <option value="all">All Users</option>
      <option value="single">Single User</option>
      <option value="external">External Email(s)</option>
    </select>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Choose who should receive this email.</p>
  </div>

  <div id="compose-user-wrap" class="space-y-2 hidden">
    <label class="text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">Select User</label>
    <select id="compose-user-id" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary">
      <option value="">Choose a user…</option>
      <?php foreach ($usersList as $u): ?>
        <option value="<?php echo (int)$u['id']; ?>"><?php echo htmlspecialchars(($u['name'] ?? 'User') . ' — ' . ($u['email'] ?? '')); ?></option>
      <?php endforeach; ?>
    </select>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">This will send only to the selected user.</p>
  </div>

  <div id="compose-external-wrap" class="space-y-2 hidden md:col-span-2">
    <label class="text-sm font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">External Emails</label>
    <input id="compose-external-to" type="text" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg focus:ring-primary focus:border-primary" placeholder="e.g. user@gmail.com, partner@company.com"/>
    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2">Comma or space separated.</p>
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
  <button type="submit" class="sm:w-auto px-6 py-2 bg-primary text-slate-900 font-bold rounded-lg flex items-center justify-center gap-2 hover:opacity-90 transition-opacity">
    <span class="material-symbols-outlined text-base">send</span> Send Email
  </button>
</div>
</form>
</div>
</div>

<!-- Mailbox Actions (Refresh / Archive) -->
<div class="col-span-12 flex flex-wrap items-center gap-3 mb-4">
  <button type="button" id="mail-refresh-all" class="w-fit shrink-0 px-6 py-2 bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-slate-200 rounded-lg flex items-center justify-center gap-2 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors">
    <span class="material-symbols-outlined text-base">sync</span> Refresh Mailbox
  </button>
  <button type="button" id="mail-archive-db" class="w-fit shrink-0 px-4 py-2 text-sm text-slate-500 hover:text-slate-700" title="Import to database">Archive to DB</button>
  <span id="mail-sync-msg" class="text-sm hidden"></span>
</div>

<!-- Mailbox -->
<div class="col-span-12 w-full grid grid-cols-1 xl:grid-cols-2 gap-6">
  <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden min-w-[280px]">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-base">inbox</span> Inbox</h3>
      <button type="button" id="inbox-refresh" class="text-xs font-bold text-primary hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">refresh</span> Refresh</button>
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
        <tbody id="inbox-tbody" class="divide-y divide-slate-100 dark:divide-zinc-700">
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Click Refresh to load inbox</td></tr>
        </tbody>
      </table>
    </div>
  </div>

  <div class="bg-white dark:bg-zinc-900 rounded-xl shadow-sm border border-slate-200 dark:border-zinc-800 overflow-hidden min-w-[280px]">
    <div class="p-4 border-b border-slate-100 dark:border-zinc-700 flex items-center justify-between">
      <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2"><span class="material-symbols-outlined text-base">send</span> Sent</h3>
      <button type="button" id="sent-refresh" class="text-xs font-bold text-primary hover:underline flex items-center gap-1"><span class="material-symbols-outlined text-sm">refresh</span> Refresh</button>
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
        <tbody id="sent-tbody" class="divide-y divide-slate-100 dark:divide-zinc-700">
          <tr><td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">Click Refresh to load sent</td></tr>
        </tbody>
      </table>
    </div>
  </div>
</div>

</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>
<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
(function(){
  var form = document.getElementById('broadcast-form');
  var msgEl = document.getElementById('broadcast-message');
  var refreshBtn = document.getElementById('mail-refresh-all');
  var syncMsg = document.getElementById('mail-sync-msg');
  var audienceSel = document.getElementById('compose-audience');
  var userWrap = document.getElementById('compose-user-wrap');
  var userSel = document.getElementById('compose-user-id');
  var extWrap = document.getElementById('compose-external-wrap');
  var extTo = document.getElementById('compose-external-to');

  function showInline(el, text, ok){
    if (!el) return;
    el.textContent = text;
    el.className = 'text-sm ' + (ok ? 'text-green-600' : 'text-red-600');
    el.classList.remove('hidden');
  }
  var sentFolder = <?php echo json_encode($imapSentFolder); ?>;
  function esc(s){ var d=document.createElement('div'); d.textContent=s; return d.innerHTML; }
  function fmtDate(d){ try{ var t=new Date(d); return t.toLocaleDateString()+' '+t.toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}); }catch(e){ return d||''; } }
  function loadInbox(){
    var tbody=document.getElementById('inbox-tbody'); if(!tbody) return;
    tbody.innerHTML='<tr><td colspan="4" class="px-4 py-6 text-center text-slate-500"><span class="material-symbols-outlined animate-spin">sync</span> Loading…</td></tr>';
    fetch('/api/admin/imap-list.php?folder=INBOX&limit=20').then(function(r){return r.json();}).then(function(res){
      if(!res.success){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">'+(res.error||'Failed')+'</td></tr>'; return; }
      var emails=res.emails||[];
      if(emails.length===0){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No messages</td></tr>'; return; }
      tbody.innerHTML=emails.map(function(m){
        var from=(m.from&&m.from.name)?m.from.name:(m.from&&m.from.email?m.from.email:'');
        return '<tr class="hover:bg-slate-50/50"><td class="px-4 py-3 text-sm">'+esc(from)+'</td><td class="px-4 py-3 text-sm font-medium">'+esc(m.subject||'')+'</td><td class="px-4 py-3 text-xs text-slate-500">'+esc(fmtDate(m.date))+'</td><td class="px-4 py-3 text-right"><button type="button" class="text-xs font-bold text-primary hover:underline mail-open" data-folder="INBOX" data-uid="'+(m.uid||'')+'">Open</button></td></tr>';
      }).join('');
    }).catch(function(){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">Request failed</td></tr>'; });
  }
  function loadSent(){
    var tbody=document.getElementById('sent-tbody'); if(!tbody) return;
    tbody.innerHTML='<tr><td colspan="4" class="px-4 py-6 text-center text-slate-500"><span class="material-symbols-outlined animate-spin">sync</span> Loading…</td></tr>';
    fetch('/api/admin/imap-list.php?folder='+encodeURIComponent(sentFolder)+'&limit=20').then(function(r){return r.json();}).then(function(res){
      if(!res.success){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">'+(res.error||'Failed')+'</td></tr>'; return; }
      var emails=res.emails||[];
      if(emails.length===0){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-slate-500">No sent messages</td></tr>'; return; }
      tbody.innerHTML=emails.map(function(m){
        var to=(m.to||[]).join(', ');
        return '<tr class="hover:bg-slate-50/50"><td class="px-4 py-3 text-xs">'+esc(to)+'</td><td class="px-4 py-3 text-sm font-medium">'+esc(m.subject||'')+'</td><td class="px-4 py-3 text-xs text-slate-500">'+esc(fmtDate(m.date))+'</td><td class="px-4 py-3 text-right"><button type="button" class="text-xs font-bold text-primary hover:underline mail-open" data-folder="'+esc(sentFolder)+'" data-uid="'+(m.uid||'')+'">Open</button></td></tr>';
      }).join('');
    }).catch(function(){ tbody.innerHTML='<tr><td colspan="4" class="px-4 py-8 text-center text-red-600">Request failed</td></tr>'; });
  }

  function closeModal(){
    var m = document.getElementById('mail-modal');
    if (m) { m.classList.add('hidden'); }
  }
  function openMessage(folder, uid){
    var modal = document.getElementById('mail-modal');
    var lbl = document.getElementById('mail-modal-label');
    var subj = document.getElementById('mail-modal-subject');
    var body = document.getElementById('mail-modal-body');
    if (!modal || !lbl || !subj || !body) return;
    lbl.textContent = folder === 'INBOX' ? 'Inbox' : 'Sent';
    subj.textContent = 'Loading…';
    body.innerHTML = '<div class="text-center py-8 text-slate-500"><span class="material-symbols-outlined animate-spin">sync</span> Loading…</div>';
    modal.classList.remove('hidden');
    fetch('/api/admin/imap-message.php?folder=' + encodeURIComponent(folder) + '&uid=' + encodeURIComponent(uid))
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (!res.success) {
          body.innerHTML = '<div class="text-red-600">' + (res.error || 'Failed') + '</div>';
          subj.textContent = 'Error';
          return;
        }
        var e = res.email || {};
        subj.textContent = e.subject || '(no subject)';
        var fromName = (e.from && e.from.name) ? e.from.name : (e.from && e.from.email ? e.from.email : '');
        var fromEmail = (e.from && e.from.email) || '';
        var fromStr = esc(fromName) + ' &lt;' + esc(fromEmail) + '&gt;';
        var toStr = esc((e.to || []).join(', '));
        var bodyContent = e.is_html ? (e.body || '') : esc(e.body || '').replace(/\n/g, '<br>');
        var html = '<div class="text-sm text-slate-700 dark:text-slate-300 space-y-1"><div><span class="font-bold">From:</span> ' + fromStr + '</div><div><span class="font-bold">To:</span> ' + toStr + '</div></div><div class="text-xs text-slate-500">' + esc(e.date || '') + '</div><div class="text-sm text-slate-800 dark:text-slate-200 leading-relaxed" id="mail-modal-content">' + bodyContent + '</div>';
        if (folder === 'INBOX' && ((e.reply_to && e.reply_to.email) || (e.from && e.from.email))) {
          html += '<div class="pt-2 border-t border-slate-100 dark:border-zinc-700" data-reply-folder="INBOX" data-reply-uid="' + esc(String(uid)) + '"><label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2">Reply</label><textarea id="reply-body" class="w-full bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg p-3 text-sm" rows="5" placeholder="Type your reply…"></textarea><div class="flex items-center gap-3 mt-3"><button type="button" id="reply-send" class="px-4 py-2 bg-primary text-slate-900 font-bold rounded-lg hover:opacity-90">Send Reply</button><div id="reply-msg" class="text-sm hidden"></div></div>';
        }
        body.innerHTML = html;
      })
      .catch(function(){ body.innerHTML = '<div class="text-red-600">Request failed</div>'; subj.textContent = 'Error'; });
  }

  document.getElementById('mail-modal-close') && document.getElementById('mail-modal-close').addEventListener('click', closeModal);
  document.getElementById('mail-modal') && document.getElementById('mail-modal').addEventListener('click', function(ev){ if (ev.target === document.getElementById('mail-modal')) closeModal(); });
  document.body.addEventListener('click', function(ev){
    var openBtn = ev.target.closest('.mail-open');
    if (openBtn && openBtn.dataset.folder && openBtn.dataset.uid) { ev.preventDefault(); openMessage(openBtn.dataset.folder, openBtn.dataset.uid); }
    var replyBtn = ev.target.closest('#reply-send');
    if (replyBtn) {
      var wrap = replyBtn.closest('[data-reply-folder]');
      if (!wrap) return;
      var rb = document.getElementById('reply-body');
      var rm = document.getElementById('reply-msg');
      var b = (rb && rb.value || '').trim();
      if (!b) { if (rm) { rm.textContent = 'Reply body required.'; rm.className = 'text-sm text-red-600'; rm.classList.remove('hidden'); } return; }
      replyBtn.disabled = true;
      fetch('/api/admin/imap-reply.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ folder: wrap.dataset.replyFolder, uid: parseInt(wrap.dataset.replyUid, 10), body: b }) })
        .then(function(r){ return r.json(); })
        .then(function(res){
          if (rm) { rm.textContent = (res && res.success) ? 'Reply sent.' : (res && res.error || 'Failed'); rm.className = 'text-sm ' + ((res && res.success) ? 'text-green-600' : 'text-red-600'); rm.classList.remove('hidden'); }
          replyBtn.disabled = false;
          if (res && res.success) { setTimeout(function(){ closeModal(); loadInbox(); loadSent(); }, 800); }
        })
        .catch(function(){ if (rm) { rm.textContent = 'Request failed'; rm.className = 'text-sm text-red-600'; rm.classList.remove('hidden'); } replyBtn.disabled = false; });
    }
  });

  if (refreshBtn) {
    refreshBtn.addEventListener('click', function(){
      showInline(syncMsg, 'Refreshing…', true);
      loadInbox(); loadSent();
      setTimeout(function(){ syncMsg.classList.add('hidden'); }, 1500);
    });
  }
  var archiveBtn = document.getElementById('mail-archive-db');
  if (archiveBtn) {
    archiveBtn.addEventListener('click', function(){
      archiveBtn.disabled = true;
      showInline(syncMsg, 'Archiving to DB…', true);
      fetch('/api/admin/mail-sync.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ limit: 50 }) })
        .then(function(r){ return r.json(); })
        .then(function(res){
          showInline(syncMsg, res && res.success ? 'Archived ' + (res.data && res.data.imported || 0) + ' message(s).' : (res && res.error || 'Failed'), !!(res && res.success));
          archiveBtn.disabled = false;
        })
        .catch(function(){ showInline(syncMsg, 'Failed', false); archiveBtn.disabled = false; });
    });
  }

  function doBroadcast(isTest){
    var subj = form.querySelector('[name="subject"]').value.trim();
    var body = form.querySelector('[name="body"]').value.trim();
    var audience = audienceSel ? audienceSel.value : 'all';
    var selectedUserId = userSel ? (userSel.value || '') : '';
    var to = extTo ? (extTo.value || '') : '';
    if (!subj || !body) { if (msgEl) { msgEl.textContent = 'Subject and body required.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
    if (!isTest) {
      if (audience === 'single' && !selectedUserId) { if (msgEl) { msgEl.textContent = 'Select a user.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
      if (audience === 'external' && !(to && to.trim())) { if (msgEl) { msgEl.textContent = 'Enter at least one external email.'; msgEl.className = 'text-sm text-red-600'; msgEl.classList.remove('hidden'); } return; }
    }
    if (msgEl) msgEl.classList.add('hidden');
    var btn = form.querySelector('button[type="submit"]');
    if (btn) btn.disabled = true;
    var payload = { subject: subj, body: body };
    if (audience === 'all') {
      payload.include_users = true;
      payload.recipients = 'all';
    } else if (audience === 'single') {
      payload.include_users = true;
      payload.user_ids = [parseInt(selectedUserId, 10)];
      payload.recipients = 'all';
    } else if (audience === 'external') {
      payload.include_users = false;
      payload.to = to.trim();
    }
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
  function refreshAudienceUI(){
    var a = audienceSel ? audienceSel.value : 'all';
    if (userWrap) userWrap.classList.toggle('hidden', a !== 'single');
    if (extWrap) extWrap.classList.toggle('hidden', a !== 'external');
    if (a !== 'external' && extTo) extTo.value = '';
    if (a !== 'single' && userSel) userSel.value = '';
  }
  if (audienceSel) {
    audienceSel.addEventListener('change', refreshAudienceUI);
    refreshAudienceUI();
  }

  document.querySelectorAll('button[data-insert-placeholder]').forEach(function(btn){
    btn.addEventListener('click', function(){
      var ta = form.querySelector('[name="body"]');
      if (ta) ta.value += ' ' + (btn.dataset.insertPlaceholder || '');
    });
  });

  document.getElementById('inbox-refresh') && document.getElementById('inbox-refresh').addEventListener('click', loadInbox);
  document.getElementById('sent-refresh') && document.getElementById('sent-refresh').addEventListener('click', loadSent);
  loadInbox();
  loadSent();
})();
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>