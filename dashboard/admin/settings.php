<?php
require_once __DIR__ . '/../../includes/admin-check.php';
require_once __DIR__ . '/../../includes/helpers.php';
$currentPage = 'settings';
$siteName = get_site_name();

$settings = [
    'site_name' => get_site_setting('site_name', $siteName),
    'site_logo' => get_site_setting('site_logo', ''),
    'site_favicon' => get_site_setting('site_favicon', ''),
];
$adminEmail = '';
$adminName = '';
if (isset($_SESSION['user_id'])) {
    try {
        $pdo = require __DIR__ . '/../../includes/db.php';
        $stmt = $pdo->prepare('SELECT email, name FROM users WHERE id = ? AND role = ?');
        $stmt->execute([$_SESSION['user_id'], 'admin']);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $adminEmail = $row['email'] ?? '';
            $adminName = $row['name'] ?? '';
        }
    } catch (Throwable $e) {}
}
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Admin Settings</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght,FILL@100..700,0..1&amp;display=swap" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#f9bd0b", "background-light": "#f8f8f5", "background-dark": "#231e0f" }, fontFamily: { "display": ["Inter", "sans-serif"] } } } };
</script>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 antialiased overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8">
<div class="mb-8">
<nav class="flex text-xs text-slate-400 gap-2 mb-1"><span>Admin</span><span>/</span><span class="text-slate-600">Settings</span></nav>
<h1 class="text-2xl font-bold">Admin Settings</h1>
<p class="text-slate-500 dark:text-zinc-400 mt-1">Site branding, admin account, and testing tools.</p>
</div>

<div class="space-y-8">
<!-- Site Branding -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="material-icons text-primary">palette</span> Site Branding</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Site Name</label>
<input id="settings-site-name" type="text" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($settings['site_name']); ?>" placeholder="Bloombit"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Logo</label>
<div class="flex items-center gap-4">
<div id="settings-logo-preview" class="w-16 h-16 rounded-lg bg-slate-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0">
<?php if (!empty($settings['site_logo'])): ?><img src="<?php echo htmlspecialchars($settings['site_logo']); ?>" alt="Logo" class="w-full h-full object-contain"/><?php else: ?><span class="material-icons text-slate-400">image</span><?php endif; ?>
</div>
<input type="file" id="settings-logo-input" accept="image/png,image/jpeg,image/webp" class="text-sm"/>
</div>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Favicon</label>
<div class="flex items-center gap-4">
<div id="settings-favicon-preview" class="w-10 h-10 rounded bg-slate-100 dark:bg-zinc-800 flex items-center justify-center overflow-hidden shrink-0">
<?php if (!empty($settings['site_favicon'])): ?><img src="<?php echo htmlspecialchars($settings['site_favicon']); ?>" alt="Favicon" class="w-full h-full object-contain"/><?php else: ?><span class="material-icons text-slate-400 text-lg">star</span><?php endif; ?>
</div>
<input type="file" id="settings-favicon-input" accept="image/png,image/x-icon,image/ico" class="text-sm"/>
</div>
</div>
</div>
<button type="button" id="settings-save-branding" class="mt-4 px-6 py-2.5 bg-primary text-slate-900 font-bold rounded-lg hover:opacity-90">Save Branding</button>
<div id="settings-branding-msg" class="text-sm mt-2 hidden"></div>
</section>

<!-- Admin Account -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="material-icons text-primary">admin_panel_settings</span> Admin Account</h2>
<div class="grid grid-cols-1 md:grid-cols-2 gap-6">
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Admin Email</label>
<input id="settings-admin-email" type="email" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($adminEmail); ?>" placeholder="admin@example.com"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Current Password</label>
<input id="settings-current-pw" type="password" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" placeholder="Required to change password"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">New Password</label>
<input id="settings-new-pw" type="password" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" placeholder="Leave blank to keep"/>
</div>
<div>
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Confirm New Password</label>
<input id="settings-confirm-pw" type="password" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" placeholder="Confirm new password"/>
</div>
</div>
<button type="button" id="settings-save-admin" class="mt-4 px-6 py-2.5 bg-primary text-slate-900 font-bold rounded-lg hover:opacity-90">Update Admin Account</button>
<div id="settings-admin-msg" class="text-sm mt-2 hidden"></div>
</section>

<!-- Testing Section -->
<section class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 p-6">
<h2 class="text-lg font-bold mb-4 flex items-center gap-2"><span class="material-icons text-primary">science</span> Testing</h2>
<p class="text-sm text-slate-500 dark:text-zinc-400 mb-4">Send a test email to verify mail configuration. Enter an email address to receive the test.</p>
<div class="flex flex-wrap gap-4 items-end mb-4">
<div class="flex-1 min-w-[200px]">
<label class="block text-sm font-medium text-slate-700 dark:text-zinc-300 mb-2">Email to receive test</label>
<input id="settings-test-email-to" type="email" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-4 py-2.5 focus:ring-primary focus:border-primary" value="<?php echo htmlspecialchars($adminEmail); ?>" placeholder="test@example.com"/>
</div>
<button type="button" id="settings-send-test-email" class="px-6 py-2.5 bg-slate-800 text-white font-bold rounded-lg hover:bg-slate-700">Send Test Email</button>
</div>
<div id="settings-test-msg" class="text-sm mt-2 hidden"></div>
</section>
</div>
</div>
</main>
</div>
<script>
(function(){
  var showMsg = function(el, text, ok){
    if (!el) return;
    el.textContent = text;
    el.className = 'text-sm mt-2 ' + (ok ? 'text-green-600' : 'text-red-600');
    el.classList.remove('hidden');
  };

  document.getElementById('settings-save-branding').addEventListener('click', function(){
    var siteName = document.getElementById('settings-site-name').value.trim();
    var btn = this;
    btn.disabled = true;
    fetch('/api/admin/site-settings.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ site_name: siteName || 'Bloombit' })
    }).then(function(r){ return r.json(); }).then(function(res){
      showMsg(document.getElementById('settings-branding-msg'), res.success ? 'Branding saved.' : (res.error || 'Failed'), res.success);
      btn.disabled = false;
    }).catch(function(){
      showMsg(document.getElementById('settings-branding-msg'), 'Request failed.', false);
      btn.disabled = false;
    });
  });

  function uploadAsset(type, fileInput, previewEl){
    if (!fileInput.files || !fileInput.files[0]) return Promise.resolve();
    var fd = new FormData();
    fd.append('type', type);
    fd.append('file', fileInput.files[0]);
    return fetch('/api/admin/upload-site-asset.php', { method: 'POST', credentials: 'same-origin', body: fd })
      .then(function(r){ return r.json(); })
      .then(function(res){
        if (res.success && res.data && res.data.url && previewEl) {
          previewEl.innerHTML = '<img src="' + res.data.url + '" alt="" class="w-full h-full object-contain"/>';
        }
        return res;
      });
  }

  document.getElementById('settings-logo-input').addEventListener('change', function(){
    var p = document.getElementById('settings-logo-preview');
    uploadAsset('logo', this, p).then(function(res){
      if (res && res.success) {
        fetch('/api/admin/site-settings.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ site_logo: res.data.url }) });
      }
    });
  });
  document.getElementById('settings-favicon-input').addEventListener('change', function(){
    var p = document.getElementById('settings-favicon-preview');
    uploadAsset('favicon', this, p).then(function(res){
      if (res && res.success) {
        fetch('/api/admin/site-settings.php', { method: 'POST', headers: { 'Content-Type': 'application/json' }, credentials: 'same-origin', body: JSON.stringify({ site_favicon: res.data.url }) });
      }
    });
  });

  document.getElementById('settings-save-admin').addEventListener('click', function(){
    var email = document.getElementById('settings-admin-email').value.trim();
    var curr = document.getElementById('settings-current-pw').value;
    var pw = document.getElementById('settings-new-pw').value;
    var conf = document.getElementById('settings-confirm-pw').value;
    var msgEl = document.getElementById('settings-admin-msg');
    if (pw && pw.length < 8) { showMsg(msgEl, 'New password must be at least 8 characters.', false); return; }
    if (pw && pw !== conf) { showMsg(msgEl, 'Passwords do not match.', false); return; }
    if (pw && !curr) { showMsg(msgEl, 'Current password required to change password.', false); return; }
    var payload = { email: email || undefined };
    if (pw) { payload.current_password = curr; payload.password = pw; }
    var btn = this;
    btn.disabled = true;
    fetch('/api/admin/admin-account.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify(payload)
    }).then(function(r){ return r.json(); }).then(function(res){
      showMsg(msgEl, res.success ? 'Account updated.' : (res.error || 'Failed'), res.success);
      btn.disabled = false;
    }).catch(function(){
      showMsg(msgEl, 'Request failed.', false);
      btn.disabled = false;
    });
  });

  document.getElementById('settings-send-test-email').addEventListener('click', function(){
    var btn = this;
    var msgEl = document.getElementById('settings-test-msg');
    var emailTo = (document.getElementById('settings-test-email-to') || {}).value.trim();
    if (!emailTo) { showMsg(msgEl, 'Enter an email address to receive the test.', false); return; }
    btn.disabled = true;
    fetch('/api/admin/send-test-email.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      credentials: 'same-origin',
      body: JSON.stringify({ to: emailTo })
    })
      .then(function(r){ return r.json(); })
      .then(function(res){
        showMsg(msgEl, res.success ? (res.data && res.data.message) : (res.error || 'Failed'), res.success);
        btn.disabled = false;
      })
      .catch(function(){
        showMsg(msgEl, 'Request failed.', false);
        btn.disabled = false;
      });
  });
})();
</script>
</body></html>
