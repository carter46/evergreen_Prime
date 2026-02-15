<?php
require_once __DIR__ . '/../../includes/admin-check.php';
$currentPage = 'addresses';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();
?>
<!DOCTYPE html>
<html class="light" lang="en"><head>
<meta charset="utf-8"/>
<meta content="width=device-width, initial-scale=1.0" name="viewport"/>
<title><?php echo htmlspecialchars($siteName); ?> | Wallet Addresses</title>
<script src="https://cdn.tailwindcss.com?plugins=forms,container-queries"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&amp;display=swap" rel="stylesheet"/>
<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet"/>
<script id="tailwind-config">
tailwind.config = { darkMode: "class", theme: { extend: { colors: { "primary": "#f9bd0b", "background-light": "#f8f8f5", "background-dark": "#231e0f" }, fontFamily: { "display": ["Inter", "sans-serif"] } } } };
</script>
<style>body { font-family: 'Inter', sans-serif; }</style>
</head>
<body class="bg-background-light dark:bg-background-dark font-display text-slate-900 dark:text-slate-100 min-h-screen overflow-x-hidden">
<div class="flex min-h-screen">
<?php include __DIR__ . '/../../includes/dashboard/admin-sidebar.php'; ?>
<main class="flex-1 overflow-y-auto min-w-0">
<?php include __DIR__ . '/../../includes/dashboard/admin-header.php'; ?>
<div class="p-4 sm:p-6 lg:p-8">
<header class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
<div>
<h1 class="text-2xl font-bold">Wallet Addresses</h1>
<p class="text-slate-500 dark:text-zinc-400">Manage deposit addresses for each supported coin. Users will send crypto to these addresses.</p>
</div>
<button type="button" id="add-address-btn" class="bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:shadow-lg transition-all">
<span class="material-icons-round text-lg">add</span> Add New Address
</button>
</header>
<div id="messageContainer" class="mb-4"></div>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div id="addressesContainer" class="p-4 sm:p-6">
<div class="text-center py-8 sm:py-10 text-slate-500">Loading addresses...</div>
</div>
</div>
</div>
</main>
</div>

<!-- Modal -->
<div id="address-modal" class="fixed inset-0 z-50 hidden">
<div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="address-modal-backdrop"></div>
<div class="absolute inset-0 flex items-center justify-center p-4">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-zinc-800">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800">
<h2 id="address-modal-title" class="text-xl font-bold">Add Wallet Address</h2>
</div>
<form id="address-form" class="p-6 space-y-4">
<div>
<label class="block text-sm font-medium mb-2">Coin</label>
<select id="address-coin-id" required class="w-full bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"></select>
</div>
<div>
<label class="block text-sm font-medium mb-2">Wallet Address</label>
<input type="text" id="address-value" required class="w-full bg-slate-50 dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 font-mono text-sm" placeholder="Enter wallet address"/>
</div>
<div class="flex gap-3 pt-4">
<button type="button" id="address-modal-cancel" class="flex-1 px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-zinc-800">Cancel</button>
<button type="submit" class="flex-1 px-4 py-2 bg-primary text-zinc-900 rounded-lg font-semibold hover:shadow-lg">Save</button>
</div>
</form>
</div>
</div>
</div>

<script src="/js/app.js"></script>
<script>
(function(){
var allAddresses = [];
var allCoins = [];
var modal = document.getElementById('address-modal');
var editingId = null;

function escapeHtml(text) {
  if (text == null) return '';
  var d = document.createElement('div');
  d.textContent = String(text);
  return d.innerHTML;
}

function showMessage(msg, type) {
  var el = document.getElementById('messageContainer');
  var bg = type === 'success' ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400' : 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400';
  el.innerHTML = '<div class="' + bg + ' px-4 py-3 rounded-lg text-sm">' + escapeHtml(msg) + '</div>';
  setTimeout(function(){ el.innerHTML = ''; }, 5000);
}

function loadCoins() {
  return fetch('/api/admin/coins.php').then(function(r){ return r.json(); }).then(function(d){
    if (d.success && d.coins) allCoins = d.coins;
  });
}

function loadAddresses() {
  return fetch('/api/admin/addresses.php').then(function(r){ return r.json(); }).then(function(d){
    if (d.success && d.addresses) {
      allAddresses = d.addresses;
      renderAddresses(d.addresses);
    } else {
      document.getElementById('addressesContainer').innerHTML = '<div class="text-center py-10 text-red-500">Failed to load addresses</div>';
    }
  }).catch(function(){
    document.getElementById('addressesContainer').innerHTML = '<div class="text-center py-10 text-red-500">Error loading addresses</div>';
  });
}

function renderAddresses(addresses) {
  var c = document.getElementById('addressesContainer');
  if (!addresses || addresses.length === 0) {
    c.innerHTML = '<div class="text-center py-8 sm:py-10 text-slate-500">No wallet addresses yet. Add one to get started.</div>';
    return;
  }
  var rows = addresses.map(function(a){
    return '<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50">' +
      '<td class="px-4 sm:px-6 py-3 text-sm">' + a.id + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm"><span class="font-semibold">' + escapeHtml(a.display_name || a.coin_key) + '</span> <span class="text-slate-500">' + escapeHtml(a.symbol || '') + '</span></td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm font-mono text-xs break-all max-w-xs">' + escapeHtml(a.address) + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-xs text-slate-500">' + (a.created_at ? new Date(a.created_at).toLocaleDateString() : '') + '</td>' +
      '<td class="px-4 sm:px-6 py-3"><button type="button" class="text-primary hover:underline text-sm mr-3" data-edit="' + a.id + '">Edit</button><button type="button" class="text-red-600 hover:underline text-sm" data-delete="' + a.id + '">Delete</button></td>' +
    '</tr>';
  }).join('');
  c.innerHTML = '<div class="overflow-x-auto"><table class="w-full text-left"><thead class="bg-slate-50 dark:bg-zinc-800"><tr><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">ID</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Coin</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Address</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Created</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Actions</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-zinc-800">' + rows + '</tbody></table></div>';
  c.querySelectorAll('[data-edit]').forEach(function(b){ b.addEventListener('click', function(){ openEdit(parseInt(b.getAttribute('data-edit'), 10)); }); });
  c.querySelectorAll('[data-delete]').forEach(function(b){ b.addEventListener('click', function(){ confirmDelete(parseInt(b.getAttribute('data-delete'), 10)); }); });
}

function openModal(title, addressId) {
  editingId = addressId || null;
  document.getElementById('address-modal-title').textContent = title;
  var sel = document.getElementById('address-coin-id');
  sel.innerHTML = '<option value="">Select a coin</option>' + allCoins.map(function(coin){
    return '<option value="' + coin.id + '"' + (addressId && allAddresses.find(function(a){ return a.id === addressId; }) && allAddresses.find(function(a){ return a.id === addressId; }).coin_id === coin.id ? ' selected' : '') + '>' + escapeHtml(coin.display_name) + ' (' + escapeHtml(coin.symbol) + ')</option>';
  }).join('');
  document.getElementById('address-value').value = addressId ? (allAddresses.find(function(a){ return a.id === addressId; }) || {}).address || '' : '';
  modal.classList.remove('hidden');
}

function closeModal() {
  modal.classList.add('hidden');
  editingId = null;
}

function openAdd() {
  if (allCoins.length === 0) { showMessage('Loading coins...', 'error'); loadCoins().then(openAdd); return; }
  openModal('Add New Wallet Address');
}

function openEdit(id) {
  if (allCoins.length === 0) { loadCoins().then(function(){ openEdit(id); }); return; }
  openModal('Edit Wallet Address', id);
}

function saveAddress(e) {
  e.preventDefault();
  var coinId = parseInt(document.getElementById('address-coin-id').value, 10);
  var addr = document.getElementById('address-value').value.trim();
  if (!coinId || !addr) { showMessage('Coin and address are required', 'error'); return; }
  var url = '/api/admin/addresses.php';
  var method = 'POST';
  var body = JSON.stringify({ coin_id: coinId, address: addr });
  if (editingId) {
    url += '?id=' + editingId;
    method = 'PUT';
    body = JSON.stringify({ coin_id: coinId, address: addr });
  }
  fetch(url, { method: method, headers: { 'Content-Type': 'application/json' }, body: body })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { closeModal(); showMessage(editingId ? 'Address updated' : 'Address added', 'success'); loadAddresses(); }
      else showMessage(d.error || 'Failed', 'error');
    })
    .catch(function(){ showMessage('Error', 'error'); });
}

function confirmDelete(id) {
  if (!confirm('Delete this wallet address? This cannot be undone.')) return;
  fetch('/api/admin/addresses.php?id=' + id, { method: 'DELETE' })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { showMessage('Address deleted', 'success'); loadAddresses(); }
      else showMessage(d.error || 'Failed', 'error');
    })
    .catch(function(){ showMessage('Error', 'error'); });
}

document.getElementById('add-address-btn').addEventListener('click', openAdd);
document.getElementById('address-modal-backdrop').addEventListener('click', closeModal);
document.getElementById('address-modal-cancel').addEventListener('click', closeModal);
document.getElementById('address-form').addEventListener('submit', saveAddress);

Promise.all([loadCoins(), loadAddresses()]);
})();
</script>
</body></html>
