<?php
require_once __DIR__ . '/../../includes/admin-check.php';
$currentPage = 'addresses';
require_once __DIR__ . '/../../includes/helpers.php';
$siteName = get_site_name();

$pageTitle = $siteName . ' | Payment Methods';
require_once __DIR__ . '/../../includes/dashboard/admin-layout-start.php';
?>
<style>
.custom-scrollbar::-webkit-scrollbar { width: 8px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }
.dark .custom-scrollbar::-webkit-scrollbar-thumb { background: #475569; }
.material-symbols-outlined { font-size: 24px; overflow: hidden; display: inline-flex; align-items: center; justify-content: center; }
.pm-type-btn { transition: all 0.15s ease; }
.pm-type-btn:hover { border-color: rgb(var(--primary) / 0.5); background: rgb(var(--primary) / 0.05); }
.pm-type-btn.selected { border-color: rgb(var(--primary)); background: rgb(var(--primary) / 0.1); }
</style>
<?php
$pageHeading = 'Payment Methods';
$pageSubtitle = 'Configure crypto, bank transfer, and card options for user deposits and withdrawals.';
include __DIR__ . '/../../includes/dashboard/admin-page-title.php';
?>
<div class="flex justify-end mb-8">
<button type="button" id="add-method-btn" class="w-fit shrink-0 bg-primary text-zinc-900 px-6 py-2.5 rounded-lg font-semibold flex items-center gap-2 hover:shadow-lg transition-all">
<span class="material-symbols-outlined text-lg">add</span> Add Method
</button>
</div>
<div id="messageContainer" class="mb-4"></div>
<div class="bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800 overflow-hidden">
<div id="methodsContainer" class="p-4 sm:p-6">
<div class="text-center py-8 sm:py-10 text-slate-500">Loading payment methods...</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-end.php'; ?>

<!-- Modal -->
<div id="method-modal" class="fixed inset-0 z-50 hidden">
<div class="absolute inset-0 bg-black/40 backdrop-blur-sm" id="method-modal-backdrop"></div>
<div id="method-modal-overlay" class="absolute inset-0 flex items-center justify-center p-4 overflow-y-auto">
<div class="bg-white dark:bg-zinc-900 rounded-xl shadow-2xl w-full max-w-lg border border-slate-200 dark:border-zinc-800 overflow-hidden relative my-8">
<div class="p-6 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between gap-3">
<h2 id="method-modal-title" class="text-xl font-bold">Add Payment Method</h2>
<button type="button" id="method-modal-close" class="p-1 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800"><span class="material-symbols-outlined">close</span></button>
</div>

<div id="method-step-type" class="p-6 space-y-4">
<p class="text-sm text-slate-500">Select the type of payment method to add.</p>
<div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
<button type="button" class="pm-type-btn border-2 border-slate-200 dark:border-zinc-700 rounded-xl p-4 text-left" data-type="crypto">
<span class="material-symbols-outlined text-primary mb-2">currency_bitcoin</span>
<p class="font-bold text-sm">Crypto</p>
<p class="text-xs text-slate-500 mt-1">Wallet address per coin</p>
</button>
<button type="button" class="pm-type-btn border-2 border-slate-200 dark:border-zinc-700 rounded-xl p-4 text-left" data-type="bank">
<span class="material-symbols-outlined text-primary mb-2">account_balance</span>
<p class="font-bold text-sm">Bank Transfer</p>
<p class="text-xs text-slate-500 mt-1">Bank account details</p>
</button>
<button type="button" class="pm-type-btn border-2 border-slate-200 dark:border-zinc-700 rounded-xl p-4 text-left" data-type="card">
<span class="material-symbols-outlined text-primary mb-2">credit_card</span>
<p class="font-bold text-sm">Card</p>
<p class="text-xs text-slate-500 mt-1">Visa, Mastercard, Amex</p>
</button>
</div>
</div>

<form id="method-form" class="hidden p-6 space-y-4 max-h-[70vh] overflow-y-auto">
<input type="hidden" id="method-type" value=""/>

<div id="fields-crypto" class="hidden space-y-4">
<div>
<label class="block text-sm font-medium mb-2">Coin</label>
<div class="flex items-center gap-3 min-w-0">
<select id="method-coin-id" class="flex-1 min-w-0 bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"></select>
<div id="method-coin-logo" class="w-10 h-10 rounded-full overflow-hidden bg-slate-100 dark:bg-zinc-800 shrink-0 hidden"></div>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-2">Wallet Address</label>
<input type="text" id="method-wallet-address" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 font-mono text-sm" placeholder="Enter wallet address"/>
</div>
</div>

<div id="fields-bank" class="hidden space-y-4">
<div>
<label class="block text-sm font-medium mb-2">Label <span class="text-slate-400 font-normal">(optional)</span></label>
<input type="text" id="method-bank-label" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2" placeholder="e.g. USD Wire"/>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-sm font-medium mb-2">Bank Name <span class="text-red-500">*</span></label>
<input type="text" id="method-bank-name" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">Account Name <span class="text-red-500">*</span></label>
<input type="text" id="method-account-name" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-2">Account Number <span class="text-red-500">*</span></label>
<input type="text" id="method-account-number" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 font-mono text-sm"/>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-sm font-medium mb-2">Routing Number</label>
<input type="text" id="method-routing-number" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">SWIFT / BIC</label>
<input type="text" id="method-swift-code" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
</div>
<div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
<div>
<label class="block text-sm font-medium mb-2">IBAN</label>
<input type="text" id="method-iban" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">Branch</label>
<input type="text" id="method-bank-branch" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
</div>
<div>
<label class="block text-sm font-medium mb-2">Bank Address</label>
<textarea id="method-bank-address" rows="2" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm"></textarea>
</div>
<div>
<label class="block text-sm font-medium mb-2">Notes</label>
<textarea id="method-bank-notes" rows="2" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 text-sm" placeholder="Optional instructions for users"></textarea>
</div>
</div>

<div id="fields-card" class="hidden space-y-4">
<div>
<label class="block text-sm font-medium mb-2">Label <span class="text-slate-400 font-normal">(optional)</span></label>
<input type="text" id="method-card-label" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2" placeholder="e.g. Corporate Visa"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">Card Brand <span class="text-red-500">*</span></label>
<select id="method-card-brand" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2">
<option value="">Select brand</option>
<option value="visa">Visa</option>
<option value="mastercard">Mastercard</option>
<option value="amex">American Express</option>
</select>
</div>
<div>
<label class="block text-sm font-medium mb-2">Cardholder Name</label>
<input type="text" id="method-card-holder" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">Card Number <span class="text-red-500">*</span></label>
<input type="text" id="method-card-number" inputmode="numeric" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2 font-mono text-sm" placeholder="4111 1111 1111 1111"/>
</div>
<div class="grid grid-cols-2 gap-3">
<div>
<label class="block text-sm font-medium mb-2">Expiry</label>
<input type="text" id="method-card-expiry" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2" placeholder="MM/YY"/>
</div>
<div>
<label class="block text-sm font-medium mb-2">CVC</label>
<input type="text" id="method-card-cvc" inputmode="numeric" class="w-full bg-slate-50 dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg px-3 py-2" placeholder="123"/>
</div>
</div>
</div>

<div class="flex gap-3 pt-4 border-t border-slate-200 dark:border-zinc-800">
<button type="button" id="method-form-back" class="px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-zinc-800">Back</button>
<button type="button" id="method-modal-cancel" class="flex-1 px-4 py-2 border border-slate-200 dark:border-zinc-700 rounded-lg font-medium hover:bg-slate-50 dark:hover:bg-zinc-800">Cancel</button>
<button type="submit" class="flex-1 px-4 py-2 bg-primary text-zinc-900 rounded-lg font-semibold hover:shadow-lg">Save</button>
</div>
</form>
</div>
</div>
</div>

<?php require_once __DIR__ . '/../../includes/app-script.php'; ?>
<script>
(function(){
var allMethods = [];
var allCoins = [];
var modal = document.getElementById('method-modal');
var stepType = document.getElementById('method-step-type');
var methodForm = document.getElementById('method-form');
var editingId = null;
var selectedType = null;

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

function typeLabel(t) {
  if (t === 'crypto') return 'Crypto';
  if (t === 'bank') return 'Bank Transfer';
  if (t === 'card') return 'Card';
  return t;
}

function methodSummary(m) {
  if (m.method_type === 'crypto') {
    return '<span class="font-mono text-xs break-all">' + escapeHtml(m.wallet_address || m.address || '') + '</span>';
  }
  if (m.method_type === 'bank') {
    var parts = [m.bank_name, m.account_name, m.account_number ? '•••' + String(m.account_number).slice(-4) : ''].filter(Boolean);
    return escapeHtml(parts.join(' · '));
  }
  var brand = (m.card_brand || 'card').toUpperCase();
  var num = m.card_number ? '•••• ' + String(m.card_number).slice(-4) : '';
  return escapeHtml(brand + (num ? ' — ' + num : ''));
}

function loadCoins() {
  return fetch('/api/admin/coins.php').then(function(r){ return r.json(); }).then(function(d){
    if (d.success && d.coins) allCoins = d.coins;
  });
}

function loadMethods() {
  return fetch('/api/admin/addresses.php').then(function(r){ return r.json(); }).then(function(d){
    if (d.success && (d.methods || d.addresses)) {
      allMethods = d.methods || d.addresses;
      renderMethods(allMethods);
    } else {
      document.getElementById('methodsContainer').innerHTML = '<div class="text-center py-10 text-red-500">Failed to load payment methods</div>';
    }
  }).catch(function(){
    document.getElementById('methodsContainer').innerHTML = '<div class="text-center py-10 text-red-500">Error loading payment methods</div>';
  });
}

function closeAllDropdowns() {
  document.querySelectorAll('.pm-actions-dropdown').forEach(function(d){ d.classList.add('hidden'); });
}

function renderMethods(methods) {
  var c = document.getElementById('methodsContainer');
  if (!methods || methods.length === 0) {
    c.innerHTML = '<div class="text-center py-8 sm:py-10 text-slate-500">No payment methods yet. Add one to get started.</div>';
    return;
  }
  function safeLogo(url) { return (url && /^https?:\/\//i.test(url)) ? '<img src="' + url.replace(/"/g,'&quot;') + '" alt="" class="w-8 h-8 rounded-full object-cover shrink-0"/>' : ''; }
  var rows = methods.map(function(m){
    var logo = m.method_type === 'crypto' ? safeLogo(m.logo) : '';
    var icon = !logo ? '<span class="material-symbols-outlined text-slate-400 text-xl">' + (m.method_type === 'bank' ? 'account_balance' : (m.method_type === 'card' ? 'credit_card' : 'currency_bitcoin')) + '</span>' : logo;
    var name = m.method_type === 'crypto' ? ((m.display_name || m.symbol || '') + ' (' + (m.symbol || '') + ')') : (m.label || m.display_name || typeLabel(m.method_type));
    var actionsHtml = '<div class="relative inline-block"><button type="button" class="pm-actions-btn p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-700 text-slate-500" data-id="' + m.id + '"><span class="material-symbols-outlined text-lg">more_vert</span></button><div class="pm-actions-dropdown hidden absolute right-0 top-full mt-1 py-1 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 rounded-lg shadow-lg z-10 min-w-[100px]"><button type="button" class="pm-action-edit block w-full text-left px-3 py-2 text-sm text-primary hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="' + m.id + '">Edit</button><button type="button" class="pm-action-delete block w-full text-left px-3 py-2 text-sm text-red-600 hover:bg-slate-50 dark:hover:bg-zinc-700" data-id="' + m.id + '">Delete</button></div></div>';
    return '<tr class="hover:bg-slate-50 dark:hover:bg-zinc-800/50">' +
      '<td class="px-4 sm:px-6 py-3 text-sm">' + m.id + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm"><span class="text-xs font-bold uppercase px-2 py-0.5 rounded bg-slate-100 dark:bg-zinc-800">' + escapeHtml(typeLabel(m.method_type)) + '</span></td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm"><div class="flex items-center gap-3">' + icon + '<span class="font-semibold">' + escapeHtml(name) + '</span></div></td>' +
      '<td class="px-4 sm:px-6 py-3 text-sm max-w-md">' + methodSummary(m) + '</td>' +
      '<td class="px-4 sm:px-6 py-3 text-right">' + actionsHtml + '</td>' +
    '</tr>';
  }).join('');
  c.innerHTML = '<div class="overflow-x-auto"><table class="w-full text-left"><thead class="bg-slate-50 dark:bg-zinc-800"><tr><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">ID</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Type</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Name</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500">Details</th><th class="px-4 sm:px-6 py-3 text-xs font-bold uppercase text-slate-500 text-right">Actions</th></tr></thead><tbody class="divide-y divide-slate-200 dark:divide-zinc-800">' + rows + '</tbody></table></div>';
  c.querySelectorAll('.pm-actions-btn').forEach(function(btn){
    btn.addEventListener('click', function(e){ e.stopPropagation(); closeAllDropdowns(); btn.nextElementSibling.classList.toggle('hidden'); });
  });
  document.addEventListener('click', function(){ closeAllDropdowns(); });
  c.querySelectorAll('.pm-action-edit').forEach(function(b){ b.addEventListener('click', function(e){ e.stopPropagation(); openEdit(parseInt(b.getAttribute('data-id'), 10)); closeAllDropdowns(); }); });
  c.querySelectorAll('.pm-action-delete').forEach(function(b){ b.addEventListener('click', function(e){ e.stopPropagation(); confirmDelete(parseInt(b.getAttribute('data-id'), 10)); closeAllDropdowns(); }); });
}

function getCoinsAlreadyUsed() {
  return allMethods.filter(function(m){ return m.method_type === 'crypto'; }).map(function(m){ return m.coin_id; });
}

function showTypeStep() {
  stepType.classList.remove('hidden');
  methodForm.classList.add('hidden');
  selectedType = null;
  document.querySelectorAll('.pm-type-btn').forEach(function(b){ b.classList.remove('selected'); });
}

function showFormStep(type) {
  selectedType = type;
  document.getElementById('method-type').value = type;
  stepType.classList.add('hidden');
  methodForm.classList.remove('hidden');
  document.getElementById('fields-crypto').classList.toggle('hidden', type !== 'crypto');
  document.getElementById('fields-bank').classList.toggle('hidden', type !== 'bank');
  document.getElementById('fields-card').classList.toggle('hidden', type !== 'card');
}

function populateCryptoCoins(currentCoinId) {
  var sel = document.getElementById('method-coin-id');
  var used = getCoinsAlreadyUsed();
  var options = allCoins.map(function(coin){
    var usedAlready = used.indexOf(coin.id) >= 0;
    var isCurrent = currentCoinId === coin.id;
    if (!editingId && usedAlready) return '';
    return '<option value="' + coin.id + '"' + (isCurrent ? ' selected' : '') + '>' + escapeHtml(coin.display_name) + ' (' + escapeHtml(coin.symbol) + ')' + (usedAlready && !isCurrent ? ' — already added' : '') + '</option>';
  }).filter(function(o){ return o.length > 0; });
  sel.innerHTML = '<option value="">Select a coin</option>' + options.join('');
  var logoEl = document.getElementById('method-coin-logo');
  function updateLogo() {
    var id = parseInt(sel.value, 10);
    var coin = allCoins.find(function(c){ return c.id === id; });
    if (coin && coin.logo && /^https?:\/\//i.test(coin.logo)) {
      logoEl.innerHTML = '<img src="' + coin.logo.replace(/"/g,'&quot;') + '" alt="" class="w-full h-full object-cover"/>';
      logoEl.classList.remove('hidden');
    } else { logoEl.innerHTML = ''; logoEl.classList.add('hidden'); }
  }
  sel.onchange = updateLogo;
  updateLogo();
}

function clearFormFields() {
  ['method-wallet-address','method-bank-label','method-bank-name','method-account-name','method-account-number','method-routing-number','method-swift-code','method-iban','method-bank-branch','method-bank-address','method-bank-notes','method-card-label','method-card-holder','method-card-number','method-card-expiry','method-card-cvc'].forEach(function(id){
    var el = document.getElementById(id);
    if (el) el.value = '';
  });
  document.getElementById('method-card-brand').value = '';
}

function fillFormFromMethod(m) {
  if (m.method_type === 'crypto') {
    populateCryptoCoins(m.coin_id);
    document.getElementById('method-wallet-address').value = m.wallet_address || m.address || '';
  } else if (m.method_type === 'bank') {
    document.getElementById('method-bank-label').value = m.label || '';
    document.getElementById('method-bank-name').value = m.bank_name || '';
    document.getElementById('method-account-name').value = m.account_name || '';
    document.getElementById('method-account-number').value = m.account_number || '';
    document.getElementById('method-routing-number').value = m.routing_number || '';
    document.getElementById('method-swift-code').value = m.swift_code || '';
    document.getElementById('method-iban').value = m.iban || '';
    document.getElementById('method-bank-branch').value = m.bank_branch || '';
    document.getElementById('method-bank-address').value = m.bank_address || '';
    document.getElementById('method-bank-notes').value = m.bank_notes || '';
  } else if (m.method_type === 'card') {
    document.getElementById('method-card-label').value = m.label || '';
    document.getElementById('method-card-brand').value = m.card_brand || '';
    document.getElementById('method-card-holder').value = m.card_holder_name || '';
    document.getElementById('method-card-number').value = m.card_number || '';
    document.getElementById('method-card-expiry').value = m.card_expiry || '';
    document.getElementById('method-card-cvc').value = m.card_cvc || '';
  }
}

function openModal(title, methodId) {
  editingId = methodId || null;
  document.getElementById('method-modal-title').textContent = title;
  clearFormFields();
  if (methodId) {
    var m = allMethods.find(function(x){ return x.id === methodId; });
    if (!m) return;
    showFormStep(m.method_type);
    fillFormFromMethod(m);
  } else {
    showTypeStep();
  }
  modal.classList.remove('hidden');
}

function closeModal() {
  modal.classList.add('hidden');
  editingId = null;
  selectedType = null;
}

function openAdd() {
  if (allCoins.length === 0) { loadCoins().then(openAdd); return; }
  openModal('Add Payment Method');
}

function openEdit(id) {
  if (allCoins.length === 0) { loadCoins().then(function(){ openEdit(id); }); return; }
  openModal('Edit Payment Method', id);
}

function buildPayload() {
  var type = editingId ? (allMethods.find(function(m){ return m.id === editingId; }) || {}).method_type : selectedType;
  if (!type) return null;
  var payload = { method_type: type };
  if (type === 'crypto') {
    payload.coin_id = parseInt(document.getElementById('method-coin-id').value, 10);
    payload.wallet_address = document.getElementById('method-wallet-address').value.trim();
    if (!payload.coin_id || !payload.wallet_address) return { error: 'Coin and wallet address are required' };
  } else if (type === 'bank') {
    payload.label = document.getElementById('method-bank-label').value.trim();
    payload.bank_name = document.getElementById('method-bank-name').value.trim();
    payload.account_name = document.getElementById('method-account-name').value.trim();
    payload.account_number = document.getElementById('method-account-number').value.trim();
    payload.routing_number = document.getElementById('method-routing-number').value.trim();
    payload.swift_code = document.getElementById('method-swift-code').value.trim();
    payload.iban = document.getElementById('method-iban').value.trim();
    payload.bank_branch = document.getElementById('method-bank-branch').value.trim();
    payload.bank_address = document.getElementById('method-bank-address').value.trim();
    payload.bank_notes = document.getElementById('method-bank-notes').value.trim();
    if (!payload.bank_name || !payload.account_name || !payload.account_number) return { error: 'Bank name, account name, and account number are required' };
  } else {
    payload.label = document.getElementById('method-card-label').value.trim();
    payload.card_brand = document.getElementById('method-card-brand').value;
    payload.card_holder_name = document.getElementById('method-card-holder').value.trim();
    payload.card_number = document.getElementById('method-card-number').value.trim();
    payload.card_expiry = document.getElementById('method-card-expiry').value.trim();
    payload.card_cvc = document.getElementById('method-card-cvc').value.trim();
    if (!payload.card_brand || !payload.card_number) return { error: 'Card brand and number are required' };
  }
  return payload;
}

function saveMethod(e) {
  e.preventDefault();
  var payload = buildPayload();
  if (!payload) { showMessage('Select a payment method type', 'error'); return; }
  if (payload.error) { showMessage(payload.error, 'error'); return; }
  var url = '/api/admin/addresses.php';
  var method = editingId ? 'PUT' : 'POST';
  if (editingId) url += '?id=' + editingId;
  fetch(url, { method: method, headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(payload) })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { closeModal(); showMessage(editingId ? 'Payment method updated' : 'Payment method added', 'success'); loadMethods(); }
      else showMessage(d.error || 'Failed', 'error');
    })
    .catch(function(){ showMessage('Error', 'error'); });
}

function confirmDelete(id) {
  if (!confirm('Delete this payment method? This cannot be undone.')) return;
  fetch('/api/admin/addresses.php?id=' + id, { method: 'DELETE' })
    .then(function(r){ return r.json(); })
    .then(function(d){
      if (d.success) { showMessage('Payment method deleted', 'success'); loadMethods(); }
      else showMessage(d.error || 'Failed', 'error');
    })
    .catch(function(){ showMessage('Error', 'error'); });
}

document.getElementById('add-method-btn').addEventListener('click', openAdd);
document.getElementById('method-modal-backdrop').addEventListener('click', closeModal);
document.getElementById('method-modal-overlay').addEventListener('click', function(ev){ if (ev.target.id === 'method-modal-overlay') closeModal(); });
document.getElementById('method-modal-cancel').addEventListener('click', closeModal);
document.getElementById('method-modal-close').addEventListener('click', closeModal);
document.getElementById('method-form-back').addEventListener('click', function(){ if (editingId) closeModal(); else showTypeStep(); });
document.getElementById('method-form').addEventListener('submit', saveMethod);
document.querySelectorAll('.pm-type-btn').forEach(function(btn){
  btn.addEventListener('click', function(){
    var type = btn.getAttribute('data-type');
    if (type === 'crypto') {
      var used = getCoinsAlreadyUsed();
      if (allCoins.length > 0 && used.length >= allCoins.length) {
        showMessage('All coins already have crypto methods. Delete one first or add a bank/card method.', 'error');
        return;
      }
      populateCryptoCoins(null);
    }
    showFormStep(type);
  });
});

Promise.all([loadCoins(), loadMethods()]);
})();
</script>
<?php require_once __DIR__ . '/../../includes/dashboard/admin-layout-close.php'; ?>
