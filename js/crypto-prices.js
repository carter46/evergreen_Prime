/**
 * Bloombit - Crypto Price Fetching & DOM Updates
 * Integrates with CoinGecko API via /api/coingecko.php
 */
(function (global) {
    'use strict';

    const API_BASE = (typeof global.BLOOMBIT_API_BASE !== 'undefined') ? global.BLOOMBIT_API_BASE : '';
    const COINGECKO_URL = API_BASE + '/api/coingecko.php';
    const CACHE_KEY = 'bloombit_crypto_prices_cache';
    const CACHE_TTL_MS = 30000; // 30 seconds
    const REFRESH_INTERVAL_MS = 120000; // 2 min for public, 5 min for dashboard - configurable
    const RETRY_DELAY_MS = 5000;

    const Config = global.BloombitCryptoConfig || {};
    const COINS_TOP = Config.COINS_TOP || ['bitcoin', 'ethereum', 'tether', 'binancecoin', 'solana'];
    const CRYPTO_LOGOS = Config.CRYPTO_LOGOS || {};
    const FALLBACK_PRICES = Config.FALLBACK_PRICES || {};

    let cachedPrices = {};
    let refreshTimer = null;

    function getCached() {
        try {
            const raw = sessionStorage.getItem(CACHE_KEY);
            if (!raw) return null;
            const { data, timestamp } = JSON.parse(raw);
            if (Date.now() - timestamp < CACHE_TTL_MS) return data;
            sessionStorage.removeItem(CACHE_KEY);
        } catch (e) { /* ignore */ }
        return null;
    }

    function setCached(prices) {
        try {
            sessionStorage.setItem(CACHE_KEY, JSON.stringify({ data: prices, timestamp: Date.now() }));
        } catch (e) { /* ignore */ }
    }

    function formatPrice(price) {
        if (price == null || isNaN(price) || price <= 0) return '--';
        if (price >= 1000) return '$' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
        if (price >= 1) return '$' + price.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 4 });
        if (price >= 0.01) return '$' + price.toFixed(4);
        return '$' + price.toFixed(6);
    }

    function formatChange(change) {
        if (change == null || isNaN(change)) return '--';
        const sign = change >= 0 ? '+' : '';
        return sign + Number(change).toFixed(2) + '%';
    }

    async function fetchCryptoPrices(coinIds) {
        const ids = Array.isArray(coinIds) ? coinIds : (typeof coinIds === 'string' ? coinIds.split(',').map(s => s.trim()).filter(Boolean) : COINS_TOP);
        const idsParam = ids.join(',');

        const cached = getCached();
        if (cached && ids.every(id => cached[id]?.usd != null)) {
            cachedPrices = cached;
            return cached;
        }

        const url = `${COINGECKO_URL}?path=/simple/price&ids=${encodeURIComponent(idsParam)}&vs_currencies=usd&include_24hr_change=true`;

        const doFetch = async () => {
            const res = await fetch(url);
            if (res.status === 429) {
                await new Promise(r => setTimeout(r, RETRY_DELAY_MS));
                return fetch(url);
            }
            return res;
        };

        try {
            const res = await doFetch();
            if (!res.ok) throw new Error('API error: ' + res.status);
            const data = await res.json();
            if (data && data.error) throw new Error(data.error);

            const hasValid = data && typeof data === 'object' && Object.values(data).some(c => c && typeof c === 'object' && c.usd != null);
            if (!hasValid) throw new Error('No valid price data');

            cachedPrices = data;
            setCached(data);
            return data;
        } catch (err) {
            console.warn('Bloombit crypto prices: API failed, using fallback:', err.message);
            const fallback = {};
            ids.forEach(id => {
                if (FALLBACK_PRICES[id]) fallback[id] = FALLBACK_PRICES[id];
            });
            cachedPrices = Object.keys(fallback).length ? fallback : cachedPrices;
            return cachedPrices;
        }
    }

    function updateTickerDOM(prices, containerSelector) {
        const container = document.querySelector(containerSelector || '.crypto-ticker');
        if (!container) return;

        const coins = (Config.COINS_TOP || COINS_TOP).slice(0, 10);
        const items = container.querySelectorAll('.crypto-ticker-item');
        items.forEach((el, i) => {
            const coinId = el.getAttribute('data-coin') || coins[i];
            if (!coinId) return;
            const p = (prices || cachedPrices)[coinId];
            const meta = Config.getMeta ? Config.getMeta(coinId) : { symbol: coinId.toUpperCase().slice(0, 3), name: coinId };
            const priceEl = el.querySelector('.crypto-price');
            const changeEl = el.querySelector('.crypto-change');
            const imgEl = el.querySelector('.crypto-logo');
            const logo = (Config.getLogo ? Config.getLogo(coinId) : CRYPTO_LOGOS[coinId]) || '';
            if (imgEl && logo) {
                imgEl.src = logo;
                imgEl.alt = meta.name;
                imgEl.style.display = '';
            }
            if (priceEl && p) priceEl.textContent = formatPrice(p.usd);
            if (changeEl && p) {
                changeEl.textContent = formatChange(p.usd_24h_change);
                changeEl.className = 'crypto-change ' + (p.usd_24h_change >= 0 ? 'text-green-500' : 'text-red-500');
            }
        });
    }

    function updateTableDOM(prices, tableSelector) {
        const table = document.querySelector(tableSelector || '.crypto-table');
        if (!table) return;

        const rows = table.querySelectorAll('tr[data-coin]');
        rows.forEach(row => {
            const coinId = row.getAttribute('data-coin');
            if (!coinId) return;
            const p = (prices || cachedPrices)[coinId];
            const priceEl = row.querySelector('.crypto-price');
            const changeEl = row.querySelector('.crypto-change');
            const imgEl = row.querySelector('.crypto-logo');
            const meta = Config.getMeta ? Config.getMeta(coinId) : { symbol: coinId.toUpperCase().slice(0, 3), name: coinId };
            const logo = (Config.getLogo ? Config.getLogo(coinId) : CRYPTO_LOGOS[coinId]) || '';
            if (imgEl && logo) {
                imgEl.src = logo;
                imgEl.alt = meta.name;
            }
            if (priceEl && p) priceEl.textContent = formatPrice(p.usd);
            if (changeEl && p) {
                changeEl.textContent = formatChange(p.usd_24h_change);
                changeEl.className = 'crypto-change ' + (p.usd_24h_change >= 0 ? 'text-green-500' : 'text-red-500');
            }
        });
    }

    function updateDataElements(prices) {
        const data = prices || cachedPrices;
        document.querySelectorAll('[data-coin][data-price]').forEach(el => {
            const coinId = el.getAttribute('data-coin');
            const p = data[coinId];
            if (p && p.usd != null) el.textContent = formatPrice(p.usd);
        });
        document.querySelectorAll('[data-coin][data-change]').forEach(el => {
            const coinId = el.getAttribute('data-coin');
            const p = data[coinId];
            if (p && p.usd_24h_change != null && !isNaN(p.usd_24h_change)) {
                el.textContent = formatChange(p.usd_24h_change);
                el.className = (el.className || '').replace(/\btext-(green|red|emerald)-\d+\b/g, '') + ' ' + (p.usd_24h_change >= 0 ? 'text-emerald-500' : 'text-red-500');
            }
        });
    }

    async function initPrices(coinIds, options) {
        const opts = options || {};
        const coins = coinIds || COINS_TOP;
        const prices = await fetchCryptoPrices(coins);
        if (opts.tickerSelector) updateTickerDOM(prices, opts.tickerSelector);
        if (opts.tableSelector) updateTableDOM(prices, opts.tableSelector);
        updateDataElements(prices);

        const interval = opts.refreshInterval !== undefined ? opts.refreshInterval : REFRESH_INTERVAL_MS;
        if (interval > 0 && !refreshTimer) {
            refreshTimer = setInterval(async () => {
                const p = await fetchCryptoPrices(coins);
                if (opts.tickerSelector) updateTickerDOM(p, opts.tickerSelector);
                if (opts.tableSelector) updateTableDOM(p, opts.tableSelector);
                updateDataElements(p);
            }, interval);
        }
        return prices;
    }

    function stopRefresh() {
        if (refreshTimer) {
            clearInterval(refreshTimer);
            refreshTimer = null;
        }
    }

    global.BloombitCryptoPrices = {
        fetch: fetchCryptoPrices,
        getCached: () => cachedPrices,
        formatPrice,
        formatChange,
        updateTickerDOM,
        updateTableDOM,
        updateDataElements,
        init: initPrices,
        stopRefresh,
        getLogo: (id) => (Config.getLogo ? Config.getLogo(id) : CRYPTO_LOGOS[id]) || '',
        getMeta: (id) => (Config.getMeta ? Config.getMeta(id) : { symbol: (id || '').toUpperCase().slice(0, 4), name: id || '' })
    };
})(typeof window !== 'undefined' ? window : this);
