<?php
/**
 * TradingView mini-chart + widget manager. Include once per page (before </body>).
 */
$mwVer = (int) @filemtime(dirname(__DIR__) . '/js/market-widgets.js');
?>
<script type="module" src="https://widgets.tradingview-widget.com/w/en/tv-mini-chart.js" data-tv-mini-chart-loader="1"></script>
<script src="/js/market-widgets.js?v=<?php echo $mwVer; ?>" defer></script>
