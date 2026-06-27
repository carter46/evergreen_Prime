(function () {
    var indexEl = document.getElementById('marketing-search-index');
    if (!indexEl) return;

    var index = [];
    try {
        index = JSON.parse(indexEl.textContent || '[]');
    } catch (e) {
        return;
    }

    function scoreItem(item, query) {
        var q = query.trim().toLowerCase();
        if (!q) return 0;

        var title = (item.title || '').toLowerCase();
        var text = (item.text || '').toLowerCase();
        var subtitle = (item.subtitle || '').toLowerCase();

        if (title === q) return 100;
        if (title.indexOf(q) === 0) return 85;
        if (title.indexOf(q) !== -1) return 70;
        if (subtitle.indexOf(q) !== -1) return 55;
        if (text.indexOf(q) !== -1) return 45;

        var words = q.split(/\s+/).filter(Boolean);
        var score = 0;
        words.forEach(function (word) {
            if (title.indexOf(word) !== -1) score += 22;
            else if (subtitle.indexOf(word) !== -1) score += 14;
            else if (text.indexOf(word) !== -1) score += 8;
        });
        return score;
    }

    function search(query, limit) {
        return index
            .map(function (item) {
                return { item: item, score: scoreItem(item, query) };
            })
            .filter(function (row) {
                return row.score > 0;
            })
            .sort(function (a, b) {
                return b.score - a.score;
            })
            .slice(0, limit || 3)
            .map(function (row) {
                return row.item;
            });
    }

    function escapeHtml(str) {
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;');
    }

    function renderSuggestions(listEl, results) {
        if (!results.length) {
            listEl.innerHTML = '<p class="px-3 py-2 text-xs text-gray-500">No matches found</p>';
            listEl.classList.remove('hidden');
            return;
        }

        listEl.innerHTML = results.map(function (item) {
            var meta = item.subtitle ? item.subtitle : item.type;
            return (
                '<a href="' + escapeHtml(item.url) + '" class="marketing-search-suggestion block px-3 py-2 hover:bg-gray-50 transition-colors" role="option">' +
                '<span class="block text-sm font-medium text-fidelityDark truncate">' + escapeHtml(item.title) + '</span>' +
                '<span class="block text-[11px] text-gray-500 truncate">' + escapeHtml(meta) + '</span>' +
                '</a>'
            );
        }).join('');
        listEl.classList.remove('hidden');
    }

    function initSearch(wrap) {
        var input = wrap.querySelector('.marketing-search-input');
        var form = wrap.querySelector('.marketing-search-form');
        var list = wrap.querySelector('.marketing-search-suggestions');
        if (!input || !form || !list) return;

        function hideSuggestions() {
            list.classList.add('hidden');
            list.innerHTML = '';
        }

        function updateSuggestions() {
            var query = input.value.trim();
            if (query.length < 2) {
                hideSuggestions();
                return;
            }
            renderSuggestions(list, search(query, 3));
        }

        function goToFirstResult() {
            var query = input.value.trim();
            if (!query) return false;
            var results = search(query, 1);
            if (results.length) {
                window.location.href = results[0].url;
                return true;
            }
            renderSuggestions(list, []);
            return false;
        }

        input.addEventListener('input', updateSuggestions);
        input.addEventListener('focus', updateSuggestions);

        input.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') {
                hideSuggestions();
                input.blur();
            }
        });

        form.addEventListener('submit', function (e) {
            e.preventDefault();
            goToFirstResult();
        });

        document.addEventListener('click', function (e) {
            if (!wrap.contains(e.target)) hideSuggestions();
        });

        list.addEventListener('click', function () {
            hideSuggestions();
        });
    }

    document.querySelectorAll('.marketing-search-wrap').forEach(initSearch);
})();
