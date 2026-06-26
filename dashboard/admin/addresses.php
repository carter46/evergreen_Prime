<?php
require_once __DIR__ . '/../../includes/admin-check.php';
$currentPage = 'addresses';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();

$pageTitle = $siteName . ' | Wallet Addresses';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
?>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
.material-icons-round { font-size: 24px; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; }
</style>
<?php
$pageHeading = 'Wallet Addresses';
$pageSubtitle = 'Manage deposit addresses for each supported coin. Users will send crypto to these addresses.';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<div class="flex justify-end mb-8">
<button type="button" id="add-address-btn" class="w-fit shrink-0 bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:shadow-lg transition-all">
<span class="material-icons-round text-lg">add</span> Add New Address
</button>
</div>
<div id="messageContainer" class="mb-4"></div>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div id="addressesContainer" class="p-4 sm:p-6">
<div class="text-center py-8 sm:py-10 text-slate-500">Loading addresses...</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>

<!-- Modal -->
<div id="address-modal" class="fixed inset-0 z-50 hidden">
<div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="address-modal-backdrop"></div>
<div id="address-modal-overlay" class="absolute inset-0 flex items-center justify-center p-4">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-md border border-slate-200 dark:border-zinc-800 overflow-hidden relative">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800">
<h2 id="address-modal-title" class="text-xl font-bold">Add Wallet Address</h2>
</div>
<form id="address-form" class="p-6 space-y-4">
<div>
<label class="block text-sm font-medium mb-2">Coin</label>
<div class="flex items-center gap-3 min-w-0">
<select id="address-coin-id" required class="flex-1 min-w-0 bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"></select>
<div id="address-coin-logo" class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-zinc-800 shrink-0 hidden"></div>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-2">Wallet Address</label>
<input type="text" id="address-value" required class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 font-mono text-sm" placeholder="Enter wallet address"/>
</div>
<div class="flex gap-3 pt-4">
<button type="button" id="address-modal-cancel" class="flex-1 px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-zinc-800">Cancel</button>
<button type="submit" class="flex-1 px-4 py-2 bg-primary text-zinc-900 rounded-lg font-semibold hover:shadow-lg">Save</button>
</div>
</form>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
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

function closeAllDropdowns() {
  document.querySelectorAll('.addr-actions-dropdown').forEach(function(d){ d.classList.add('hidden'); });
}

function renderAddresses(addresses) {
  var c = document.getElementById('addressesContainer');
  if (!addresses || addresses.length === 0) {
    c.innerHTML = '<div class="text-center py-8 sm:py-10 text-slate-500">No wallet addresses yet. Add one to get started.</div>';
    return;
  }
  function safeLogo(url) { return (url && /^https?:\/\//i.test(url)) ? '<img src="' + url.replace(/"/g,'&quot;') + '" alt="" class="w-8 h-8 rounded-full object-cover shrink-0"/>' : ''; }
  var rows = addresses.map(function(a){
    var logo = safeLogo(a.logo);
    var actionsHtml = '<div class="relative inline-block"><button type="button" class="addr-actions-btn p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-500" data-id="' + a.id + '" aria-label="Actions"><span class="material-icons text-lg">more_vert</span></button><div class="addr-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg shadow-lg z-10 min-w-[100px]"><button type="button" class="addr-action-edit block w-full text-left px-3 py-2 text-sm text-primary hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="' + a.id + '">Edit</button><button type="button" class="addr-action-delete block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="' + a.id + '">Delete</button></div></div>';
    return '<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50">' +
      '<td class="px-4 sm:px-6 py-3 text-sm">' + a.id + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm"><div class="flex items-center gap-3">' + logo + '<span><span class="font-semibold">' + escapeHtml(a.display_name || a.coin_key) + '</span> <span class="text-slate-500">' + escapeHtml(a.symbol || '') + '</span></span></div></td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm font-mono text-xs break-all max-w-xs">' + escapeHtml(a.address) + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-right">' + actionsHtml + '</td>' +
    '</tr>';
  }).join('');
  c.innerHTML = '<div class="overflow-x-auto"><table class="w-full text-left"><thead class="bg-slate-50 dark:bg-zinc-800"><tr><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">ID</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Coin</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Address</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-zinc-800">' + rows + '</tbody></table></div>';
  c.querySelectorAll('.addr-actions-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){ e.stopPropagation(); closeAllDropdowns(); var dd = btn.nextElementSibling; dd.classList.toggle('hidden'); });
  });
  document.addEventListener('click', function(){ closeAllDropdowns(); });
  c.querySelectorAll('.addr-action-edit').forEach(function(b){ b.addEventListener('click', function(e){ e.stopPropagation(); openEdit(parseInt(b.getAttribute('data-id'), 10)); closeAllDropdowns(); }); });
  c.querySelectorAll('.addr-action-delete').forEach(function(b){ b.addEventListener('click', function(e){ e.stopPropagation(); confirmDelete(parseInt(b.getAttribute('data-id'), 10)); closeAllDropdowns(); }); });
}

function getCoinsAlreadyUsed() {
  return allAddresses.map(function(a){ return a.coin_id; });
}

function openModal(title, addressId) {
  editingId = addressId || null;
  document.getElementById('address-modal-title').textContent = title;
  var sel = document.getElementById('address-coin-id');
  var usedCoinIds = getCoinsAlreadyUsed();
  var currentAddr = addressId ? allAddresses.find(function(a){ return a.id === addressId; }) : null;
  var currentCoinId = currentAddr ? currentAddr.coin_id : null;
  var options = allCoins.map(function(coin){
    var alreadyUsed = usedCoinIds.indexOf(coin.id) >= 0;
    var isCurrent = currentCoinId === coin.id;
    if (!addressId && alreadyUsed) return '';
    return '<option value="' + coin.id + '"' + (isCurrent ? ' selected' : '') + (alreadyUsed && !isCurrent ? ' disabled' : '') + '>' + escapeHtml(coin.display_name) + ' (' + escapeHtml(coin.symbol) + ')' + (alreadyUsed && !isCurrent ? ' — already added' : '') + '</option>';
  }).filter(function(o){ return o.length > 0; });
  sel.innerHTML = '<option value="">Select a coin</option>' + options.join('');
  sel.required = options.length > 0;
  document.getElementById('address-value').value = addressId ? (currentAddr || {}).address || '' : '';
  var logoEl = document.getElementById('address-coin-logo');
  function updateLogo() {
    var id = parseInt(sel.value, 10);
    var coin = allCoins.find(function(c){ return c.id === id; });
    if (coin && coin.logo && /^https?:\/\//i.test(coin.logo)) {
      logoEl.innerHTML = '<img src="' + coin.logo.replace(/"/g,'&quot;') + '" alt="" class="w-full h-full object-cover"/>';
      logoEl.classList.remove('hidden');
    } else {
      logoEl.innerHTML = '';
      logoEl.classList.add('hidden');
    }
  }
  sel.onchange = updateLogo;
  updateLogo();
  modal.classList.remove('hidden');
}

function closeModal() {
  modal.classList.add('hidden');
  editingId = null;
}

function openAdd() {
  if (allCoins.length === 0) { showMessage('Loading coins...', 'error'); loadCoins().then(openAdd); return; }
  var used = getCoinsAlreadyUsed();
  if (allCoins.length > 0 && used.length >= allCoins.length) {
    showMessage('All coins already have addresses. Delete one first to add a different coin.', 'error');
    return;
  }
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
var overlayEl = document.getElementById('address-modal-overlay');
if (overlayEl) overlayEl.addEventListener('click', function(ev){ if (ev.target === overlayEl) closeModal(); });
document.getElementById('address-modal-cancel').addEventListener('click', closeModal);
document.getElementById('address-form').addEventListener('submit', saveAddress);

Promise.all([loadCoins(), loadAddresses()]);
})();
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>
