/**
 * Bloombit - Shared Crypto Configuration
 * CoinGecko IDs and logo URLs (same pattern as node spacedebugger / wyomingtrust)
 */
(function (global) {
    'use strict';

    // Top coins for index ticker and Top 10 table
    const COINS_TOP = [
        'bitcoin',
        'ethereum',
        'tether',
        'binancecoin',
        'solana',
        'ripple',
        'usd-coin',
        'cardano',
        'dogecoin',
        'tron'
    ];

    // Coin metadata: CoinGecko ID -> { symbol, name }
    const COIN_META = {
        'bitcoin': { symbol: 'BTC', name: 'Bitcoin' },
        'ethereum': { symbol: 'ETH', name: 'Ethereum' },
        'tether': { symbol: 'USDT', name: 'Tether' },
        'binancecoin': { symbol: 'BNB', name: 'BNB' },
        'solana': { symbol: 'SOL', name: 'Solana' },
        'ripple': { symbol: 'XRP', name: 'XRP' },
        'usd-coin': { symbol: 'USDC', name: 'USD Coin' },
        'cardano': { symbol: 'ADA', name: 'Cardano' },
        'dogecoin': { symbol: 'DOGE', name: 'Dogecoin' },
        'tron': { symbol: 'TRX', name: 'TRON' },
        'polkadot': { symbol: 'DOT', name: 'Polkadot' },
        'polygon': { symbol: 'POL', name: 'Polygon' },
        'litecoin': { symbol: 'LTC', name: 'Litecoin' },
        'bitcoin-cash': { symbol: 'BCH', name: 'Bitcoin Cash' },
        'avalanche-2': { symbol: 'AVAX', name: 'Avalanche' },
        'shiba-inu': { symbol: 'SHIB', name: 'Shiba Inu' },
        'chainlink': { symbol: 'LINK', name: 'Chainlink' },
        'uniswap': { symbol: 'UNI', name: 'Uniswap' },
        'stellar': { symbol: 'XLM', name: 'Stellar' },
        'cosmos': { symbol: 'ATOM', name: 'Cosmos' },
        'internet-computer': { symbol: 'ICP', name: 'Internet Computer' },
        'optimism': { symbol: 'OP', name: 'Optimism' },
        'arbitrum': { symbol: 'ARB', name: 'Arbitrum' },
        'aptos': { symbol: 'APT', name: 'Aptos' },
        'filecoin': { symbol: 'FIL', name: 'Filecoin' },
        'hedera-hashgraph': { symbol: 'HBAR', name: 'Hedera' },
        'algorand': { symbol: 'ALGO', name: 'Algorand' },
        'vechain': { symbol: 'VET', name: 'VeChain' },
        'fantom': { symbol: 'FTM', name: 'Fantom' },
        'monero': { symbol: 'XMR', name: 'Monero' },
        'the-open-network': { symbol: 'TON', name: 'Toncoin' }
    };

    // Real crypto logos from CoinGecko
    const CRYPTO_LOGOS = {
        'bitcoin': 'https://assets.coingecko.com/coins/images/1/large/bitcoin.png',
        'ethereum': 'https://assets.coingecko.com/coins/images/279/large/ethereum.png',
        'tether': 'https://assets.coingecko.com/coins/images/325/large/Tether.png',
        'binancecoin': 'https://assets.coingecko.com/coins/images/825/large/bnb-icon2_2x.png',
        'solana': 'https://assets.coingecko.com/coins/images/4128/large/solana.png',
        'ripple': 'https://assets.coingecko.com/coins/images/44/large/xrp-symbol-white-128.png',
        'usd-coin': 'https://assets.coingecko.com/coins/images/6319/large/USD_Coin_icon.png',
        'cardano': 'https://assets.coingecko.com/coins/images/975/large/cardano.png',
        'dogecoin': 'https://assets.coingecko.com/coins/images/5/large/dogecoin.png',
        'tron': 'https://assets.coingecko.com/coins/images/1094/large/tron-logo.png',
        'polkadot': 'https://assets.coingecko.com/coins/images/12171/large/polkadot.png',
        'polygon': 'https://assets.coingecko.com/coins/images/4713/large/polygon.png',
        'litecoin': 'https://assets.coingecko.com/coins/images/2/large/litecoin.png',
        'bitcoin-cash': 'https://assets.coingecko.com/coins/images/780/large/bitcoin-cash-circle.png',
        'avalanche-2': 'https://assets.coingecko.com/coins/images/12559/large/coin-round-red.png',
        'shiba-inu': 'https://assets.coingecko.com/coins/images/11939/large/shiba.png',
        'chainlink': 'https://assets.coingecko.com/coins/images/877/large/chainlink-new-logo.png',
        'uniswap': 'https://assets.coingecko.com/coins/images/12504/large/uniswap-uni.png',
        'stellar': 'https://assets.coingecko.com/coins/images/100/large/Stellar_symbol_black.png',
        'cosmos': 'https://assets.coingecko.com/coins/images/1481/large/cosmos_hub.png',
        'internet-computer': 'https://assets.coingecko.com/coins/images/14495/large/Internet_Computer_logo.png',
        'optimism': 'https://assets.coingecko.com/coins/images/25244/large/Optimism.png',
        'arbitrum': 'https://assets.coingecko.com/coins/images/16547/large/arb.jpg',
        'aptos': 'https://assets.coingecko.com/coins/images/26455/large/aptos_round.png',
        'filecoin': 'https://assets.coingecko.com/coins/images/12817/large/filecoin.png',
        'hedera-hashgraph': 'https://assets.coingecko.com/coins/images/3688/large/hbar.png',
        'algorand': 'https://assets.coingecko.com/coins/images/4380/large/download.png',
        'vechain': 'https://assets.coingecko.com/coins/images/1167/large/VET_Token_Icon.png',
        'fantom': 'https://assets.coingecko.com/coins/images/4001/large/Fantom_round.png',
        'monero': 'https://assets.coingecko.com/coins/images/69/large/monero_logo.png',
        'the-open-network': 'https://assets.coingecko.com/coins/images/17980/large/ton_symbol.png'
    };

    // Fallback prices when API fails
    const FALLBACK_PRICES = {
        'bitcoin': { usd: 67000, usd_24h_change: -1.5 },
        'ethereum': { usd: 3200, usd_24h_change: -2.1 },
        'tether': { usd: 1, usd_24h_change: 0 },
        'binancecoin': { usd: 582, usd_24h_change: 1.5 },
        'solana': { usd: 100, usd_24h_change: 2.0 },
        'ripple': { usd: 0.55, usd_24h_change: -0.5 },
        'usd-coin': { usd: 1, usd_24h_change: 0 },
        'cardano': { usd: 0.45, usd_24h_change: 1.2 },
        'dogecoin': { usd: 0.15, usd_24h_change: 3.2 },
        'tron': { usd: 0.08, usd_24h_change: 0.5 }
    };

    global.BloombitCryptoConfig = {
        COINS_TOP,
        COIN_META,
        CRYPTO_LOGOS,
        FALLBACK_PRICES,
        getLogo: function (coinId) {
            return CRYPTO_LOGOS[coinId] || '';
        },
        getMeta: function (coinId) {
            return COIN_META[coinId] || { symbol: (coinId || '').toUpperCase().slice(0, 4), name: coinId || '' };
        }
    };
})(typeof window !== 'undefined' ? window : this);
