<?php
/**
 * Market instruments registry — single source for homepage cards and /markets/{slug} pages.
 */

require_once __DIR__ . '/helpers.php';

function market_instruments_all(): array {
    static $all = null;
    if ($all !== null) return $all;

    $all = [
        market_instrument_bitcoin(),
        market_instrument_ethereum(),
        market_instrument_binancecoin(),
        market_instrument_solana(),
        market_instrument_tsla(),
        market_instrument_msft(),
        market_instrument_googl(),
        market_instrument_meta(),
        market_instrument_audcad(),
        market_instrument_usdjpy(),
        market_instrument_eurjpy(),
        market_instrument_nzdusd(),
    ];
    return $all;
}

function get_market_instrument(string $slug): ?array {
    $slug = strtolower(trim($slug));
    foreach (market_instruments_all() as $item) {
        if ($item['slug'] === $slug) return $item;
    }
    return null;
}

function get_markets_by_category(string $category): array {
    return array_values(array_filter(market_instruments_all(), fn($m) => $m['category'] === $category));
}

function get_related_markets(string $slug): array {
    $item = get_market_instrument($slug);
    if (!$item) return [];
    $related = [];
    foreach ($item['related_slugs'] as $relSlug) {
        $r = get_market_instrument($relSlug);
        if ($r) $related[] = $r;
    }
    return $related;
}

function market_illustration_src(array $instrument): string {
    $path = $instrument['illustration'] ?? '';
    $full = dirname(__DIR__) . $path;
    if ($path && is_file($full)) {
        return $path;
    }
    return '/uploads/images/markets/placeholder.svg';
}

function market_benefits_cards(): array {
    return [
        ['icon' => 'monitoring', 'title' => 'Real-Time Market Monitoring', 'text' => 'Track price action and volatility across assets from one professional dashboard.'],
        ['icon' => 'psychology', 'title' => 'AI Investment Insights', 'text' => 'Machine learning surfaces patterns and sentiment shifts to support informed decisions.'],
        ['icon' => 'pie_chart', 'title' => 'Portfolio Diversification', 'text' => 'Balance exposure across crypto, equities, and forex with guided allocation views.'],
        ['icon' => 'shield', 'title' => 'Risk Management', 'text' => 'Automated alerts and risk-aware monitoring help protect capital in volatile markets.'],
        ['icon' => 'schedule', 'title' => '24/7 Market Tracking', 'text' => 'Global markets never sleep — our systems monitor opportunities around the clock.'],
        ['icon' => 'dashboard', 'title' => 'Professional Investment Dashboard', 'text' => 'Institutional-grade tools designed for clarity, speed, and confident execution.'],
    ];
}

function market_signals_by_slug(): array {
    return [
        'bitcoin' => ['timeframe' => '4H', 'direction' => 'buy', 'confidence' => 85, 'risk' => 'Low', 'roi' => '+12.4%', 'entry' => '64,250 - 64,800', 'tp1' => '67,200', 'tp2' => '69,500', 'sl' => '62,100', 'ago' => '2 mins ago'],
        'ethereum' => ['timeframe' => '1D', 'direction' => 'sell', 'confidence' => 60, 'risk' => 'Medium', 'roi' => '-5.2%', 'entry' => '3,450 - 3,480', 'tp1' => '3,320', 'tp2' => '3,150', 'sl' => '3,550', 'ago' => '14 mins ago'],
        'binancecoin' => ['timeframe' => '4H', 'direction' => 'buy', 'confidence' => 72, 'risk' => 'Medium', 'roi' => '+6.8%', 'entry' => '575 - 590', 'tp1' => '615', 'tp2' => '640', 'sl' => '558', 'ago' => '8 mins ago'],
        'solana' => ['timeframe' => '1H', 'direction' => 'buy', 'confidence' => 92, 'risk' => 'Medium', 'roi' => '+8.7%', 'entry' => '142.10 - 144.50', 'tp1' => '155.00', 'tp2' => '168.00', 'sl' => '135.20', 'ago' => '42 mins ago'],
        'tsla' => ['timeframe' => '1D', 'direction' => 'buy', 'confidence' => 78, 'risk' => 'High', 'roi' => '+9.1%', 'entry' => '248 - 255', 'tp1' => '272', 'tp2' => '288', 'sl' => '238', 'ago' => '18 mins ago'],
        'msft' => ['timeframe' => '4H', 'direction' => 'buy', 'confidence' => 81, 'risk' => 'Low', 'roi' => '+4.6%', 'entry' => '415 - 422', 'tp1' => '438', 'tp2' => '452', 'sl' => '408', 'ago' => '25 mins ago'],
        'googl' => ['timeframe' => '1D', 'direction' => 'buy', 'confidence' => 74, 'risk' => 'Medium', 'roi' => '+5.3%', 'entry' => '172 - 176', 'tp1' => '184', 'tp2' => '192', 'sl' => '168', 'ago' => '31 mins ago'],
        'meta' => ['timeframe' => '4H', 'direction' => 'sell', 'confidence' => 66, 'risk' => 'Medium', 'roi' => '-3.8%', 'entry' => '518 - 528', 'tp1' => '502', 'tp2' => '488', 'sl' => '538', 'ago' => '11 mins ago'],
        'audcad' => ['timeframe' => '4H', 'direction' => 'buy', 'confidence' => 70, 'risk' => 'Low', 'roi' => '+2.1%', 'entry' => '0.8820 - 0.8845', 'tp1' => '0.8890', 'tp2' => '0.8935', 'sl' => '0.8785', 'ago' => '6 mins ago'],
        'usdjpy' => ['timeframe' => '1H', 'direction' => 'buy', 'confidence' => 83, 'risk' => 'Medium', 'roi' => '+1.8%', 'entry' => '156.20 - 156.80', 'tp1' => '157.45', 'tp2' => '158.10', 'sl' => '155.60', 'ago' => '4 mins ago'],
        'eurjpy' => ['timeframe' => '4H', 'direction' => 'sell', 'confidence' => 68, 'risk' => 'Medium', 'roi' => '-1.4%', 'entry' => '163.80 - 164.30', 'tp1' => '162.90', 'tp2' => '162.10', 'sl' => '165.00', 'ago' => '22 mins ago'],
        'nzdusd' => ['timeframe' => '1D', 'direction' => 'buy', 'confidence' => 76, 'risk' => 'Low', 'roi' => '+2.6%', 'entry' => '0.5980 - 0.6010', 'tp1' => '0.6065', 'tp2' => '0.6120', 'sl' => '0.5940', 'ago' => '35 mins ago'],
    ];
}

function get_market_signal(array $instrument): array {
    $map = market_signals_by_slug();
    $slug = $instrument['slug'] ?? '';
    return $map[$slug] ?? [
        'timeframe' => '4H',
        'direction' => 'buy',
        'confidence' => 70,
        'risk' => 'Medium',
        'roi' => '+5.0%',
        'entry' => '—',
        'tp1' => '—',
        'tp2' => '—',
        'sl' => '—',
        'ago' => 'Just now',
    ];
}

function market_confidence_offset(int $confidence): float {
    $circ = 251.2;
    return round($circ - ($circ * max(0, min(100, $confidence)) / 100), 1);
}

function market_instrument_base(string $slug, array $overrides): array {
    $site = get_site_name();
    $defaults = [
        'slug' => $slug,
        'illustration' => '/uploads/images/markets/' . $slug . '.png',
        'cta_headline' => 'Ready to Explore Smarter Investing?',
        'cta_body' => "Join investors using {$site}'s AI-assisted tools to monitor markets, analyze opportunities, and build diversified portfolios.",
    ];
    return array_merge($defaults, $overrides);
}

function market_instrument_bitcoin(): array {
    return market_instrument_base('bitcoin', [
        'name' => 'Bitcoin',
        'symbol' => 'BINANCE:BTCUSDT',
        'category' => 'crypto',
        'coingecko_id' => 'bitcoin',
        'pair_label' => 'BTC / USD',
        'snapshot' => [
            'market_type' => 'Cryptocurrency',
            'sector' => 'Digital Assets',
            'exchange' => 'Global (24/7)',
            'hours' => '24/7',
            'volatility' => 'High',
            'suitable_for' => 'Long-term holders, active crypto traders',
        ],
        'seo' => [
            'title' => 'Bitcoin (BTC) Live Price & AI Crypto Investing',
            'description' => 'Explore Bitcoin market data, educational insights, and how AI-assisted tools help monitor BTC volatility and trends.',
            'og_title' => 'Bitcoin (BTC) — Live Market & AI Investing',
            'og_description' => 'Learn about Bitcoin and discover AI-assisted crypto investment monitoring.',
        ],
        'intro' => 'Bitcoin is the world\'s largest cryptocurrency by market capitalization and the benchmark digital asset for the broader crypto ecosystem.',
        'about_paragraphs' => [
            'Launched in 2009, Bitcoin introduced decentralized peer-to-peer value transfer without intermediaries. Its fixed supply cap of 21 million coins is a core part of its scarcity narrative.',
            'Institutional adoption, ETF products, and macro liquidity conditions increasingly influence BTC alongside traditional risk assets. Volatility remains elevated compared to major equities.',
            'Investors monitor Bitcoin for portfolio diversification, inflation-hedge debates, and as a leading indicator for overall crypto market sentiment.',
        ],
        'why_watch_intro' => 'Bitcoin often sets the tone for the entire digital asset market.',
        'why_watch' => [
            'Halving cycles and supply dynamics affect long-term scarcity narratives.',
            'Institutional flows and ETF demand can shift liquidity and volatility.',
            'Macro rates, dollar strength, and risk appetite influence BTC alongside equities.',
            'On-chain activity and exchange flows are widely tracked by traders and analysts.',
        ],
        'ai_help' => [
            'Our AI engine monitors Bitcoin volatility regimes, momentum shifts, and cross-asset correlations so you can stay informed without watching charts around the clock.',
            'Automated alerts highlight unusual volume and sentiment changes — supporting disciplined decision-making, not emotional reactions.',
        ],
        'ai_help_bullets' => ['Volatility regime detection', 'Trend and momentum monitoring', 'Cross-market correlation alerts', '24/7 BTC surveillance'],
        'related_slugs' => ['ethereum', 'solana', 'binancecoin', 'msft'],
    ]);
}

function market_instrument_ethereum(): array {
    return market_instrument_base('ethereum', [
        'name' => 'Ethereum',
        'symbol' => 'BINANCE:ETHUSDT',
        'category' => 'crypto',
        'coingecko_id' => 'ethereum',
        'pair_label' => 'ETH / USD',
        'snapshot' => [
            'market_type' => 'Cryptocurrency',
            'sector' => 'Smart Contracts / DeFi',
            'exchange' => 'Global (24/7)',
            'hours' => '24/7',
            'volatility' => 'High',
            'suitable_for' => 'Crypto investors, DeFi participants',
        ],
        'seo' => [
            'title' => 'Ethereum (ETH) Live Price & AI Crypto Investing',
            'description' => 'Ethereum market overview, ecosystem trends, and AI tools for monitoring ETH price action and DeFi activity.',
            'og_title' => 'Ethereum (ETH) — Live Market & AI Investing',
            'og_description' => 'Educational Ethereum insights and AI-assisted crypto monitoring.',
        ],
        'intro' => 'Ethereum is the leading smart-contract platform powering DeFi, NFTs, and thousands of decentralized applications.',
        'about_paragraphs' => [
            'ETH is both a digital asset and the fuel for transactions on the Ethereum network. Network upgrades have shifted the protocol toward a proof-of-stake consensus model.',
            'Developer activity, total value locked in DeFi, and Layer-2 scaling solutions are key fundamentals investors follow alongside price.',
            'Ethereum\'s correlation with Bitcoin and broader tech risk assets makes it a closely watched barometer of crypto innovation cycles.',
        ],
        'why_watch_intro' => 'Ethereum reflects the health of on-chain finance and Web3 development.',
        'why_watch' => [
            'Network usage, gas fees, and staking yields affect holder economics.',
            'DeFi and NFT activity cycles drive demand for block space.',
            'Competition from alternative L1 chains influences market share narratives.',
            'Regulatory clarity around digital assets impacts institutional participation.',
        ],
        'ai_help' => [
            'AI models track Ethereum network metrics, ETH/BTC ratio trends, and DeFi sentiment indicators to help you understand when conditions are shifting.',
            'Rather than manual chart-watching, automated monitoring surfaces meaningful changes in volatility and momentum.',
        ],
        'ai_help_bullets' => ['ETH/BTC ratio tracking', 'DeFi sentiment indicators', 'Volatility alerts', 'Trend analysis'],
        'related_slugs' => ['bitcoin', 'solana', 'binancecoin', 'googl'],
    ]);
}

function market_instrument_binancecoin(): array {
    return market_instrument_base('binancecoin', [
        'name' => 'BNB',
        'symbol' => 'BINANCE:BNBUSDT',
        'category' => 'crypto',
        'coingecko_id' => 'binancecoin',
        'pair_label' => 'BNB / USD',
        'snapshot' => [
            'market_type' => 'Cryptocurrency',
            'sector' => 'Exchange Token',
            'exchange' => 'Global (24/7)',
            'hours' => '24/7',
            'volatility' => 'High',
            'suitable_for' => 'Active crypto traders',
        ],
        'seo' => [
            'title' => 'BNB Live Price & AI Crypto Market Insights',
            'description' => 'BNB market data and educational content on exchange-token dynamics and AI-assisted monitoring.',
            'og_title' => 'BNB — Live Market & AI Investing',
            'og_description' => 'Learn about BNB and AI-powered crypto market monitoring.',
        ],
        'intro' => 'BNB is the native token of the BNB Chain ecosystem, widely used for trading fee discounts and on-chain applications.',
        'about_paragraphs' => [
            'As an exchange-linked ecosystem token, BNB price action often reflects trading volumes and broader crypto market liquidity.',
            'Burn mechanisms and chain activity influence supply-side narratives that investors monitor alongside BTC and ETH trends.',
            'BNB remains among the most liquid altcoins, making it a common holding for active crypto portfolios.',
        ],
        'why_watch_intro' => 'BNB bridges exchange activity and Layer-1 blockchain utility.',
        'why_watch' => [
            'Trading volume trends on major platforms affect token demand.',
            'BNB Chain DeFi and gaming activity supports network usage.',
            'Regulatory developments around exchanges can impact sentiment.',
            'Correlation with Bitcoin drives short-term volatility.',
        ],
        'ai_help' => [
            'Our platform monitors BNB liquidity, burn-rate narratives, and correlation with major crypto assets using AI-driven alerts.',
            'Stay informed on momentum shifts without constant manual monitoring.',
        ],
        'ai_help_bullets' => ['Liquidity monitoring', 'Correlation tracking', 'Momentum alerts', 'Risk-aware notifications'],
        'related_slugs' => ['bitcoin', 'ethereum', 'solana', 'meta'],
    ]);
}

function market_instrument_solana(): array {
    return market_instrument_base('solana', [
        'name' => 'Solana',
        'symbol' => 'BINANCE:SOLUSDT',
        'category' => 'crypto',
        'coingecko_id' => 'solana',
        'pair_label' => 'SOL / USD',
        'snapshot' => [
            'market_type' => 'Cryptocurrency',
            'sector' => 'High-Performance L1',
            'exchange' => 'Global (24/7)',
            'hours' => '24/7',
            'volatility' => 'High',
            'suitable_for' => 'Growth-oriented crypto investors',
        ],
        'seo' => [
            'title' => 'Solana (SOL) Live Price & AI Crypto Investing',
            'description' => 'Solana ecosystem overview, SOL market data, and AI tools for monitoring high-performance blockchain trends.',
            'og_title' => 'Solana (SOL) — Live Market & AI Investing',
            'og_description' => 'Educational Solana insights and AI-assisted monitoring.',
        ],
        'intro' => 'Solana is a high-throughput blockchain known for fast transactions and a vibrant ecosystem of DeFi, NFT, and memecoin activity.',
        'about_paragraphs' => [
            'SOL attracted developers and traders seeking low-cost, high-speed on-chain execution. Ecosystem growth cycles have historically driven significant price volatility.',
            'Network reliability and uptime narratives periodically influence investor confidence alongside broader crypto market trends.',
            'Solana is often viewed as a higher-beta alternative to Ethereum within the smart-contract sector.',
        ],
        'why_watch_intro' => 'Solana captures speculative energy and developer momentum in crypto.',
        'why_watch' => [
            'Ecosystem app launches and TVL trends signal adoption cycles.',
            'Network performance and outage history affect long-term confidence.',
            'Memecoin and retail trading waves can drive rapid price swings.',
            'SOL/BTC ratio is tracked as a risk-on indicator within crypto.',
        ],
        'ai_help' => [
            'AI analytics watch Solana ecosystem activity, SOL volatility spikes, and relative strength versus BTC and ETH.',
            'Automated monitoring helps you respond to market changes with data — not impulse.',
        ],
        'ai_help_bullets' => ['Ecosystem activity signals', 'Volatility spike detection', 'Relative strength vs BTC', 'Automated trend alerts'],
        'related_slugs' => ['bitcoin', 'ethereum', 'binancecoin', 'tsla'],
    ]);
}

function market_instrument_tsla(): array {
    return market_instrument_base('tsla', [
        'name' => 'Tesla',
        'symbol' => 'NASDAQ:TSLA',
        'category' => 'stock',
        'coingecko_id' => null,
        'pair_label' => 'TSLA / USD',
        'snapshot' => [
            'market_type' => 'Stock',
            'sector' => 'Consumer Discretionary / EV',
            'exchange' => 'NASDAQ',
            'hours' => '09:30–16:00 EST',
            'volatility' => 'High',
            'suitable_for' => 'Growth investors, active traders',
        ],
        'seo' => [
            'title' => 'Tesla (TSLA) Live Stock Price & AI Investing',
            'description' => 'Tesla stock overview, EV industry trends, and AI-assisted tools for monitoring TSLA market sentiment.',
            'og_title' => 'Tesla (TSLA) — Live Market & AI Investing',
            'og_description' => 'Learn about Tesla stock and AI-powered investment monitoring.',
        ],
        'intro' => 'Tesla is a global leader in electric vehicles, energy storage, and autonomous driving technology — one of the most widely traded growth stocks.',
        'about_paragraphs' => [
            'Tesla\'s valuation reflects expectations for EV adoption, manufacturing scale, energy products, and future autonomy revenue streams.',
            'Delivery numbers, margin trends, and competitive dynamics in China and Europe are closely watched quarterly catalysts.',
            'TSLA often trades with high volatility, attracting both long-term believers in the energy transition and short-term speculators.',
        ],
        'why_watch_intro' => 'Tesla sits at the intersection of technology, manufacturing, and consumer trends.',
        'why_watch' => [
            'Quarterly delivery and earnings reports drive significant price reactions.',
            'EV competition and pricing strategy affect margin expectations.',
            'CEO visibility and product roadmap announcements move sentiment quickly.',
            'Interest rates and growth-stock multiples influence TSLA alongside the NASDAQ.',
        ],
        'ai_help' => [
            'Our AI tools monitor TSLA volatility, sector-relative performance, and sentiment shifts around earnings and product events.',
            'Automated analysis helps you track a fast-moving stock without manual chart monitoring.',
        ],
        'ai_help_bullets' => ['Earnings-season alerts', 'Volatility monitoring', 'Tech sector correlation', 'Sentiment trend analysis'],
        'related_slugs' => ['msft', 'googl', 'meta', 'bitcoin'],
    ]);
}

function market_instrument_msft(): array {
    return market_instrument_base('msft', [
        'name' => 'Microsoft',
        'symbol' => 'NASDAQ:MSFT',
        'category' => 'stock',
        'coingecko_id' => null,
        'pair_label' => 'MSFT / USD',
        'snapshot' => [
            'market_type' => 'Stock',
            'sector' => 'Technology',
            'exchange' => 'NASDAQ',
            'hours' => '09:30–16:00 EST',
            'volatility' => 'Medium',
            'suitable_for' => 'Long-term investors, active traders',
        ],
        'seo' => [
            'title' => 'Microsoft (MSFT) Live Stock Price & AI Investing',
            'description' => 'Microsoft stock analysis, cloud and AI trends, and tools for monitoring MSFT with AI-assisted insights.',
            'og_title' => 'Microsoft (MSFT) — Live Market & AI Investing',
            'og_description' => 'Educational Microsoft stock insights and AI investment monitoring.',
        ],
        'intro' => 'Microsoft is a global technology leader in cloud computing, enterprise software, gaming, and artificial intelligence.',
        'about_paragraphs' => [
            'Azure cloud growth, Office 365 subscriptions, and LinkedIn contribute to a diversified recurring-revenue base that investors prize for stability.',
            'Microsoft\'s partnership with OpenAI and Copilot integrations across products position it at the center of the enterprise AI adoption wave.',
            'MSFT is a core holding in many institutional portfolios and a bellwether for large-cap technology performance.',
        ],
        'why_watch_intro' => 'Microsoft reflects enterprise IT spending and the AI platform race.',
        'why_watch' => [
            'Azure and cloud revenue growth rates vs. Amazon and Google Cloud.',
            'AI product monetization and Copilot adoption metrics.',
            'Enterprise spending cycles during economic slowdowns or recoveries.',
            'Regulatory scrutiny of big-tech concentration and acquisitions.',
        ],
        'ai_help' => [
            'AI analytics track Microsoft\'s sector performance, earnings sentiment, and technology mega-cap trends relevant to MSFT holders.',
            'Our platform surfaces macro and sector signals so you can focus on strategy rather than manual data gathering.',
        ],
        'ai_help_bullets' => ['Sector trend analysis', 'Earnings sentiment monitoring', 'Cloud/AI narrative tracking', 'Portfolio risk alerts'],
        'related_slugs' => ['googl', 'meta', 'tsla', 'bitcoin'],
    ]);
}

function market_instrument_googl(): array {
    return market_instrument_base('googl', [
        'name' => 'Alphabet (Google)',
        'symbol' => 'NASDAQ:GOOGL',
        'category' => 'stock',
        'coingecko_id' => null,
        'pair_label' => 'GOOGL / USD',
        'snapshot' => [
            'market_type' => 'Stock',
            'sector' => 'Technology',
            'exchange' => 'NASDAQ',
            'hours' => '09:30–16:00 EST',
            'volatility' => 'Medium',
            'suitable_for' => 'Long-term investors, growth traders',
        ],
        'seo' => [
            'title' => 'Alphabet (GOOGL) Live Stock Price & AI Investing',
            'description' => 'Google parent Alphabet stock overview, digital advertising trends, and AI-assisted market monitoring.',
            'og_title' => 'Alphabet (GOOGL) — Live Market & AI Investing',
            'og_description' => 'Learn about GOOGL and AI-powered investment tools.',
        ],
        'intro' => 'Alphabet is the parent company of Google, dominating search, digital advertising, Android, YouTube, and cloud services.',
        'about_paragraphs' => [
            'Search and advertising revenue remain the financial engine, while Google Cloud and Other Bets represent diversification into infrastructure and innovation.',
            'AI integration across Search, Workspace, and Cloud is a central narrative for future growth and competitive positioning.',
            'Antitrust cases and privacy regulation in the US and EU are ongoing factors investors monitor.',
        ],
        'why_watch_intro' => 'Alphabet\'s ad business ties directly to global economic activity.',
        'why_watch' => [
            'Digital advertising spend correlates with consumer and business confidence.',
            'AI search competition from OpenAI and others may shift traffic dynamics.',
            'Cloud growth rates compared to AWS and Azure.',
            'Regulatory fines and antitrust remedies can impact valuation.',
        ],
        'ai_help' => [
            'Our AI engine analyzes technology sector momentum, ad-spend proxies, and GOOGL-specific sentiment around AI product launches.',
            'Automated monitoring supports long-term holders and active traders alike.',
        ],
        'ai_help_bullets' => ['Ad-sector proxy tracking', 'AI competitive monitoring', 'Earnings volatility alerts', 'Mega-cap correlation'],
        'related_slugs' => ['msft', 'meta', 'tsla', 'ethereum'],
    ]);
}

function market_instrument_meta(): array {
    return market_instrument_base('meta', [
        'name' => 'Meta Platforms',
        'symbol' => 'NASDAQ:META',
        'category' => 'stock',
        'coingecko_id' => null,
        'pair_label' => 'META / USD',
        'snapshot' => [
            'market_type' => 'Stock',
            'sector' => 'Technology / Social Media',
            'exchange' => 'NASDAQ',
            'hours' => '09:30–16:00 EST',
            'volatility' => 'Medium / High',
            'suitable_for' => 'Growth investors, active traders',
        ],
        'seo' => [
            'title' => 'Meta (META) Live Stock Price & AI Investing',
            'description' => 'Meta Platforms stock insights, social media and metaverse trends, and AI investment monitoring tools.',
            'og_title' => 'Meta (META) — Live Market & AI Investing',
            'og_description' => 'Educational Meta stock content and AI-assisted analysis.',
        ],
        'intro' => 'Meta Platforms operates Facebook, Instagram, WhatsApp, and Reality Labs — a leader in social media and digital advertising.',
        'about_paragraphs' => [
            'Family-of-apps engagement and ad pricing drive revenue, while Reels competes with TikTok for user attention and advertiser budgets.',
            'Heavy investment in AI infrastructure and the metaverse (Reality Labs) shapes long-term capex and profitability debates.',
            'META demonstrated sharp recovery after efficiency-focused restructuring, making it a case study in tech turnaround narratives.',
        ],
        'why_watch_intro' => 'Meta balances mature social platforms with expensive frontier bets.',
        'why_watch' => [
            'Daily active user growth across regions, especially emerging markets.',
            'Ad revenue per user and competition from TikTok and Apple privacy changes.',
            'Reality Labs losses and metaverse adoption timelines.',
            'AI recommendation algorithms affecting engagement and monetization.',
        ],
        'ai_help' => [
            'AI tools monitor META volatility, social-media sector trends, and sentiment around earnings and user-growth metrics.',
            'Stay aligned with market shifts through automated analysis rather than reactive trading.',
        ],
        'ai_help_bullets' => ['Social sector monitoring', 'Earnings volatility tracking', 'Ad-revenue sentiment', 'Risk-aware alerts'],
        'related_slugs' => ['googl', 'msft', 'tsla', 'solana'],
    ]);
}

function market_instrument_audcad(): array {
    return market_instrument_base('audcad', [
        'name' => 'AUD/CAD',
        'symbol' => 'OANDA:AUDCAD',
        'category' => 'forex',
        'coingecko_id' => null,
        'pair_label' => 'AUD / CAD',
        'snapshot' => [
            'market_type' => 'Forex',
            'sector' => 'Commodity Currencies',
            'exchange' => 'Global FX',
            'hours' => '24/5',
            'volatility' => 'Medium',
            'suitable_for' => 'Forex traders, macro investors',
        ],
        'seo' => [
            'title' => 'AUD/CAD Live Forex Rate & AI Trading Insights',
            'description' => 'AUD/CAD currency pair overview, commodity currency dynamics, and AI-assisted forex monitoring.',
            'og_title' => 'AUD/CAD — Live Forex & AI Investing',
            'og_description' => 'Learn about AUD/CAD and AI-powered forex analysis.',
        ],
        'intro' => 'AUD/CAD is a cross pair linking the Australian and Canadian dollars — both influenced by commodity exports and global risk sentiment.',
        'about_paragraphs' => [
            'Australia\'s economy is tied to iron ore and China demand, while Canada relies heavily on oil and natural resources.',
            'The pair offers exposure to commodity cycles without direct USD positioning, popular among macro forex traders.',
            'Interest rate differentials between the RBA and Bank of Canada drive carry-trade interest over longer horizons.',
        ],
        'why_watch_intro' => 'AUD/CAD reflects global commodity demand and risk appetite.',
        'why_watch' => [
            'Iron ore and oil price trends affect AUD and CAD differently.',
            'China economic data impacts Australian export expectations.',
            'RBA vs. BoC rate decisions shift yield spreads.',
            'Risk-on/risk-off flows move commodity currencies in tandem or divergence.',
        ],
        'ai_help' => [
            'AI systems track macroeconomic releases, commodity correlations, and AUD/CAD volatility to support informed forex exposure decisions.',
            'Automated alerts highlight regime changes in currency markets.',
        ],
        'ai_help_bullets' => ['Macro event alerts', 'Commodity correlation tracking', 'Volatility monitoring', 'Cross-pair analysis'],
        'related_slugs' => ['usdjpy', 'eurjpy', 'nzdusd', 'bitcoin'],
    ]);
}

function market_instrument_usdjpy(): array {
    return market_instrument_base('usdjpy', [
        'name' => 'USD/JPY',
        'symbol' => 'OANDA:USDJPY',
        'category' => 'forex',
        'coingecko_id' => null,
        'pair_label' => 'USD / JPY',
        'snapshot' => [
            'market_type' => 'Forex',
            'sector' => 'Major Pair',
            'exchange' => 'Global FX',
            'hours' => '24/5',
            'volatility' => 'Medium / High',
            'suitable_for' => 'Forex traders, macro strategists',
        ],
        'seo' => [
            'title' => 'USD/JPY Live Forex Rate & AI Market Analysis',
            'description' => 'USD/JPY educational overview, BoJ policy context, and AI-assisted forex monitoring tools.',
            'og_title' => 'USD/JPY — Live Forex & AI Investing',
            'og_description' => 'USD/JPY insights and AI-powered currency analysis.',
        ],
        'intro' => 'USD/JPY is among the most traded currency pairs globally, sensitive to US–Japan interest rate differentials and risk sentiment.',
        'about_paragraphs' => [
            'The Bank of Japan\'s yield curve control and intervention history make JPY uniquely policy-driven among G10 currencies.',
            'USD/JPY often rises when US yields climb and falls during flight-to-safety episodes — though correlations evolve.',
            'Carry traders have historically favored long USD/JPY when Japan maintains ultra-low rates.',
        ],
        'why_watch_intro' => 'USD/JPY is a macro barometer for rates and risk.',
        'why_watch' => [
            'Federal Reserve vs. Bank of Japan policy divergence.',
            'US Treasury yield movements and inflation data.',
            'Risk-off events strengthening the yen as a safe haven.',
            'Official FX intervention headlines from Japanese authorities.',
        ],
        'ai_help' => [
            'Our AI monitors USD/JPY volatility, yield-spread proxies, and risk-sentiment indicators to help you understand currency moves in context.',
            'Macro-aware alerts reduce the need for constant manual monitoring.',
        ],
        'ai_help_bullets' => ['Yield-spread monitoring', 'Safe-haven flow detection', 'Volatility alerts', 'Macro calendar integration'],
        'related_slugs' => ['eurjpy', 'audcad', 'nzdusd', 'msft'],
    ]);
}

function market_instrument_eurjpy(): array {
    return market_instrument_base('eurjpy', [
        'name' => 'EUR/JPY',
        'symbol' => 'OANDA:EURJPY',
        'category' => 'forex',
        'coingecko_id' => null,
        'pair_label' => 'EUR / JPY',
        'snapshot' => [
            'market_type' => 'Forex',
            'sector' => 'Cross Pair',
            'exchange' => 'Global FX',
            'hours' => '24/5',
            'volatility' => 'Medium / High',
            'suitable_for' => 'Forex traders, carry traders',
        ],
        'seo' => [
            'title' => 'EUR/JPY Live Forex Rate & AI Investing Tools',
            'description' => 'EUR/JPY cross pair education, ECB/BoJ policy context, and AI-assisted forex analysis.',
            'og_title' => 'EUR/JPY — Live Forex & AI Investing',
            'og_description' => 'EUR/JPY market insights and AI monitoring.',
        ],
        'intro' => 'EUR/JPY crosses the euro and Japanese yen — popular among traders seeking exposure to ECB policy and Asian risk flows.',
        'about_paragraphs' => [
            'The pair combines eurozone growth and inflation dynamics with Japan\'s unique monetary policy stance.',
            'EUR/JPY often appreciates in risk-on environments and weakens during global stress when yen strengthens.',
            'Carry dynamics depend on the spread between ECB and BoJ rate expectations.',
        ],
        'why_watch_intro' => 'EUR/JPY blends European macro with Japan\'s policy exceptionalism.',
        'why_watch' => [
            'ECB rate decisions and eurozone PMI data.',
            'BoJ policy shifts and yen intervention risk.',
            'Global equity sentiment as a risk proxy.',
            'Energy prices affecting eurozone trade balances.',
        ],
        'ai_help' => [
            'AI analytics correlate EUR/JPY moves with European data surprises, risk indices, and yen strength patterns.',
            'Automated monitoring supports disciplined forex participation.',
        ],
        'ai_help_bullets' => ['ECB event tracking', 'Risk sentiment correlation', 'Cross-pair momentum', 'Volatility regime alerts'],
        'related_slugs' => ['usdjpy', 'audcad', 'nzdusd', 'googl'],
    ]);
}

function market_instrument_nzdusd(): array {
    return market_instrument_base('nzdusd', [
        'name' => 'NZD/USD',
        'symbol' => 'FX:NZDUSD',
        'category' => 'forex',
        'coingecko_id' => null,
        'pair_label' => 'NZD / USD',
        'snapshot' => [
            'market_type' => 'Forex',
            'sector' => 'Commodity Currency',
            'exchange' => 'Global FX',
            'hours' => '24/5',
            'volatility' => 'Medium',
            'suitable_for' => 'Forex traders, macro investors',
        ],
        'seo' => [
            'title' => 'NZD/USD Live Forex Rate & AI Market Insights',
            'description' => 'NZD/USD kiwi dollar overview, dairy and risk sentiment drivers, and AI forex monitoring.',
            'og_title' => 'NZD/USD — Live Forex & AI Investing',
            'og_description' => 'NZD/USD educational content and AI analysis tools.',
        ],
        'intro' => 'NZD/USD — the "Kiwi" — reflects New Zealand\'s export economy, dairy prices, and global risk appetite versus the US dollar.',
        'about_paragraphs' => [
            'New Zealand\'s economy is relatively small but open, making NZD sensitive to global growth and commodity price cycles.',
            'RBNZ rate decisions and Chinese demand for exports are key fundamental drivers.',
            'NZD/USD often behaves as a higher-beta risk proxy among major commodity currencies.',
        ],
        'why_watch_intro' => 'The Kiwi dollar signals risk appetite in Asia-Pacific hours.',
        'why_watch' => [
            'Dairy auction prices and agricultural export data.',
            'RBNZ monetary policy and OCR changes.',
            'China growth indicators affecting regional demand.',
            'USD strength cycles driven by Fed policy.',
        ],
        'ai_help' => [
            'Our AI tracks NZD/USD volatility, commodity proxies, and USD index correlation to surface meaningful shifts for forex participants.',
            'Let automation handle continuous monitoring while you focus on strategy.',
        ],
        'ai_help_bullets' => ['Commodity proxy alerts', 'USD strength tracking', 'RBNZ event monitoring', 'Risk-on/off detection'],
        'related_slugs' => ['audcad', 'usdjpy', 'eurjpy', 'ethereum'],
    ]);
}
