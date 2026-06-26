<?php
/**
 * Homepage market preview card — official TradingView mini-chart + View Market CTA.
 * Expects $instrument from market-instruments registry.
 */
if (empty($instrument) || !is_array($instrument)) return;

$slug = $instrument['slug'];
$href = '/markets/' . htmlspecialchars($slug);
$label = htmlspecialchars($instrument['name']);
$category = $instrument['category'];
$pairLabel = htmlspecialchars($instrument['pair_label'] ?? '');
$symbol = htmlspecialchars($instrument['symbol'] ?? '');
?>
<div class="market-card-link relative bg-white p-6 rounded-xl shadow-sm border border-gray-100 group hover:shadow-md transition-shadow flex flex-col">
<?php if ($category === 'crypto'): ?>
<div class="market-card-preview crypto-market-card flex-1" data-coin="<?php echo htmlspecialchars($instrument['coingecko_id'] ?? $slug); ?>">
<div class="flex justify-between items-start mb-4">
<div class="flex items-center gap-3">
<div class="w-10 h-10 rounded-full overflow-hidden bg-yellow-500/10 flex items-center justify-center shrink-0">
<img class="crypto-logo w-7 h-7 object-contain" src="" alt="<?php echo $label; ?>"/>
</div>
<div>
<div class="font-bold text-surface-container-lowest crypto-symbol"><?php echo $pairLabel; ?></div>
<div class="text-xs text-gray-400 crypto-name"><?php echo $label; ?></div>
</div>
</div>
<div class="crypto-change font-bold font-data-mono text-gray-400">--</div>
</div>
<div class="text-2xl font-bold text-surface-container-lowest font-data-mono crypto-price">--</div>
<div class="mt-4 h-1 bg-gray-50 rounded-full overflow-hidden">
<div class="h-full bg-success w-[50%] market-bar"></div>
</div>
</div>
<?php else: ?>
<div class="market-card-preview flex-1 <?php echo $category === 'forex' ? 'forex-market-card' : 'stock-market-card'; ?>">
<tv-mini-chart symbol="<?php echo $symbol; ?>" style="width: 100%; height: 240px"></tv-mini-chart>
</div>
<?php endif; ?>
<a href="<?php echo $href; ?>" class="market-view-btn mt-4 w-full text-center">View Market</a>
</div>
