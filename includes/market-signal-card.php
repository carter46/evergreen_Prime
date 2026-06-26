<?php
/**
 * AI trading signal card — used on trading_signals.php for all registry markets.
 * Expects $instrument from market-instruments registry.
 */
if (empty($instrument) || !is_array($instrument)) return;

$signal = get_market_signal($instrument);
$slug = $instrument['slug'];
$href = '/markets/' . htmlspecialchars($slug);
$label = htmlspecialchars($instrument['name']);
$pairLabel = htmlspecialchars($instrument['pair_label'] ?? '');
$isBuy = ($signal['direction'] ?? 'buy') === 'buy';
$signalLabel = $isBuy ? 'Buy Signal' : 'Sell Signal';
$signalClass = $isBuy ? 'bg-emerald-500/10 text-emerald-600' : 'bg-red-500/10 text-red-600';
$risk = $signal['risk'] ?? 'Medium';
$riskClass = $risk === 'Low' ? 'text-emerald-500' : ($risk === 'High' ? 'text-red-500' : 'text-amber-500');
$confidence = (int) ($signal['confidence'] ?? 70);
$dashOffset = market_confidence_offset($confidence);
$coingeckoId = $instrument['coingecko_id'] ?? null;
$priceAttr = $coingeckoId ? ' data-coin="' . htmlspecialchars($coingeckoId) . '" data-price=""' : '';
?>
<div class="market-signal-card bg-white rounded-xl border border-gray-100 p-5 shadow-sm hover:shadow-md transition-all flex flex-col h-full">
<div class="flex justify-between items-start mb-4 gap-2">
<div class="flex items-center gap-3 min-w-0">
<?php if ($coingeckoId): ?>
<div class="w-10 h-10 rounded-full overflow-hidden bg-yellow-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="" alt="<?php echo $label; ?>"/>
</div>
<?php else: ?>
<div class="w-10 h-10 rounded-lg bg-surface-container-lowest/5 flex items-center justify-center shrink-0">
<span class="material-symbols-outlined text-primary-container text-xl">show_chart</span>
</div>
<?php endif; ?>
<div class="min-w-0">
<h3 class="font-bold text-surface-container-lowest truncate"><?php echo $pairLabel; ?><?php if ($coingeckoId): ?><span class="text-xs font-normal text-gray-500 ml-1"<?php echo $priceAttr; ?>></span><?php endif; ?></h3>
<span class="text-[10px] text-gray-400 uppercase font-semibold">Timeframe: <?php echo htmlspecialchars($signal['timeframe'] ?? '4H'); ?></span>
</div>
</div>
<div class="flex flex-col items-end shrink-0">
<span class="px-3 py-1 <?php echo $signalClass; ?> text-xs font-bold rounded uppercase tracking-wide"><?php echo $signalLabel; ?></span>
<span class="text-[10px] text-gray-400 mt-1"><?php echo htmlspecialchars($signal['ago'] ?? ''); ?></span>
</div>
</div>
<div class="grid grid-cols-2 gap-4 mb-5">
<div class="relative w-24 h-24 mx-auto">
<svg class="w-full h-full transform -rotate-90" viewBox="0 0 96 96">
<circle class="text-gray-100" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-width="6"></circle>
<circle class="text-primary-container" cx="48" cy="48" fill="transparent" r="40" stroke="currentColor" stroke-dasharray="251.2" stroke-dashoffset="<?php echo $dashOffset; ?>" stroke-linecap="round" stroke-width="6"></circle>
</svg>
<div class="absolute inset-0 flex flex-col items-center justify-center">
<span class="text-xl font-bold text-surface-container-lowest"><?php echo $confidence; ?>%</span>
<span class="text-[8px] uppercase text-gray-400 font-bold">Confidence</span>
</div>
</div>
<div class="flex flex-col justify-center gap-2">
<div class="flex justify-between text-xs">
<span class="text-gray-500">Risk Level</span>
<span class="font-bold <?php echo $riskClass; ?>"><?php echo htmlspecialchars($risk); ?></span>
</div>
<div class="flex justify-between text-xs">
<span class="text-gray-500">Expected ROI</span>
<span class="font-bold text-surface-container-lowest"><?php echo htmlspecialchars($signal['roi'] ?? '—'); ?></span>
</div>
</div>
</div>
<div class="space-y-2 border-t border-gray-100 pt-4 flex-1">
<div class="flex justify-between text-xs items-center p-2 rounded bg-gray-50">
<span class="text-gray-500">Entry Zone</span>
<span class="font-mono font-semibold text-surface-container-lowest"><?php echo htmlspecialchars($signal['entry'] ?? '—'); ?></span>
</div>
<div class="flex justify-between text-xs items-center px-2">
<span class="text-gray-500">Target Profit (TP1)</span>
<span class="font-mono font-semibold text-emerald-500"><?php echo htmlspecialchars($signal['tp1'] ?? '—'); ?></span>
</div>
<div class="flex justify-between text-xs items-center px-2">
<span class="text-gray-500">Target Profit (TP2)</span>
<span class="font-mono font-semibold text-emerald-500"><?php echo htmlspecialchars($signal['tp2'] ?? '—'); ?></span>
</div>
<div class="flex justify-between text-xs items-center px-2">
<span class="text-gray-500">Stop Loss (SL)</span>
<span class="font-mono font-semibold text-red-500"><?php echo htmlspecialchars($signal['sl'] ?? '—'); ?></span>
</div>
</div>
<a href="<?php echo $href; ?>" class="market-view-btn mt-4 w-full text-center">View Market</a>
</div>
