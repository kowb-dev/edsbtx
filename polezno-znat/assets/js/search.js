/**
 * EDS Advanced Search Functionality
 * /stati-tablitsy-nagruzok/assets/js/search.js
 */

(function(window, document) {
    'use strict';

    // Search configuration
    const SEARCH_CONFIG = {
        minQueryLength: 2,
        maxSuggestions: 5,
        searchDelay: 300,
        highlightClass: 'edsys-search-highlight',
        cacheExpiration: 5 * 60 * 1000, // 5 minutes
        apiEndpoint: '/ajax/search-articles.php' // Для будущих AJAX запросов
    };

    // Search cache
    let searchCache = new Map();
    let searchHistory = JSON.parse(localStorage.getItem('edsys_search_history') || '[]');

    // Advanced search class
    class AdvancedSearch {
        constructor(config = {}) {
            this.config = { ...SEARCH_CONFIG, ...config };
            this.init();
        }

        init() {
            try {
                this.setupElements();
                this.setupEventListeners();
                this.loadSearchHistory();
                console.log('Advanced search initialized');
            } catch (error) {
                console.error('Error initializing advanced search:', error);
            }
        }

        setupElements() {
            this.searchInput = document.getElementById('articlesSearch');
            this.searchContainer = document.querySelector('.edsys-search-box');
            this.articlesGrid = document.getElementById('articlesGrid');
            this.noResultsElement = null;

            if (!this.searchInput || !this.searchContainer) {
                throw new Error('Required search elements not found');
            }

            this.createSearchDropdown();
            this.createSearchStats();
        }

        createSearchDropdown() {
            this.dropdown = document.createElement('div');
            this.dropdown.className = 'edsys-search-dropdown';
            this.dropdown.innerHTML = `
                <div class="edsys-search-suggestions">
                    <div class="edsys-search-section">
                        <h4>Популярные запросы</h4>
                        <div class="edsys-popular-searches"></div>
                    </div>
                    <div class="edsys-search-section">
                        <h4>История поиска</h4>
                        <div class="edsys-search-history"></div>
                    </div>
                </div>
            `;
            this.searchContainer.appendChild(this.dropdown);
        }

        createSearchStats() {
            this.statsElement = document.createElement('div');
            this.statsElement.className = 'edsys-search-stats';
            this.searchContainer.appendChild(this.statsElement);
        }

        setupEventListeners() {
            // Main search input
            this.searchInput.addEventListener('input', this.debounce(this.handleSearch.bind(this), this.config.searchDelay));
            this.searchInput.addEventListener('focus', this.showDropdown.bind(this));
            this.searchInput.addEventListener('blur', this.hideDropdownDelayed.bind(this));
            this.searchInput.addEventListener('keydown', this.handleKeydown.bind(this));

            // Click outside to close dropdown
            document.addEventListener('click', (e) => {
                if (!this.searchContainer.contains(e.target)) {
                    this.hideDropdown();
                }
            });

            // Dropdown clicks
            this.dropdown.addEventListener('click', this.handleDropdownClick.bind(this));
        }

        handleSearch(event) {
            const query = event.target.value.trim();

            if (query.length >= this.config.minQueryLength) {
                this.performSearch(query);
                this.addToHistory(query);
            } else if (query.length === 0) {
                this.clearSearch();
            }
        }

        performSearch(query) {
            const normalizedQuery = this.normalizeQuery(query);

            // Check cache first
            if (searchCache.has(normalizedQuery)) {
                const cachedResult = searchCache.get(normalizedQuery);
                if (Date.now() - cachedResult.timestamp < this.config.cacheExpiration) {
                    this.displayResults(cachedResult.data, query);
                    return;
                }
            }

            // Perform actual search
            const results = this.searchArticles(query);

            // Cache results
            searchCache.set(normalizedQuery, {
                data: results,
                timestamp: Date.now()
            });

            this.displayResults(results, query);
        }

        searchArticles(query) {
            const articles = Array.from(document.querySelectorAll('.edsys-article-card'));
            const searchTerms = this.tokenizeQuery(query);
            const results = [];

            articles.forEach(article => {
                const articleData = this.extractArticleData(article);
                const score = this.calculateRelevanceScore(articleData, searchTerms);

                if (score > 0) {
                    results.push({
                        element: article,
                        data: articleData,
                        score: score,
                        matches: this.findMatches(articleData, searchTerms)
                    });
                }
            });

            // Sort by relevance score
            return results.sort((a, b) => b.score - a.score);
        }

        extractArticleData(article) {
            const titleElement = article.querySelector('.edsys-article-card__title');
            const excerptElement = article.querySelector('.edsys-article-card__excerpt');
            const categoryElement = article.getAttribute('data-category');

            return {
                title: titleElement ? titleElement.textContent.toLowerCase() : '',
                excerpt: excerptElement ? excerptElement.textContent.toLowerCase() : '',
                category: categoryElement || '',
                element: article
            };
        }

        tokenizeQuery(query) {
            return query.toLowerCase()
                .split(/\s+/)
                .filter(term => term.length > 1)
                .map(term => term.replace(/[^\w\u0400-\u04FF]/g, '')); // Keep Cyrillic and Latin
        }

        calculateRelevanceScore(articleData, searchTerms) {
            let score = 0;

            searchTerms.forEach(term => {
                // Title matches (highest weight)
                if (articleData.title.includes(term)) {
                    score += 10;

                    // Exact title match bonus
                    if (articleData.title === term) {
                        score += 15;
                    }

                    // Title starts with term bonus
                    if (articleData.title.startsWith(term)) {
                        score += 5;
                    }
                }

                // Excerpt matches
                if (articleData.excerpt.includes(term)) {
                    score += 3;
                }

                // Category matches
                if (articleData.category.includes(term)) {
                    score += 2;
                }

                // Fuzzy matching for typos
                const fuzzyScore = this.fuzzyMatch(term, articleData.title) +
                    this.fuzzyMatch(term, articleData.excerpt);
                score += fuzzyScore * 0.5;
            });

            return score;
        }

        fuzzyMatch(term, text) {
            if (term.length < 3) return 0;

            let matches = 0;
            const words = text.split(/\s+/);

            words.forEach(word => {
                const distance = this.levenshteinDistance(term, word);
                if (distance <= Math.floor(term.length * 0.3)) {
                    matches += 1;
                }
            });

            return matches;
        }

        levenshteinDistance(str1, str2) {
            const matrix = Array(str2.length + 1).fill(null).map(() => Array(str1.length + 1).fill(null));

            for (let i = 0; i <= str1.length; i++) matrix[0][i] = i;
            for (let j = 0; j <= str2.length; j++) matrix[j][0] = j;

            for (let j = 1; j <= str2.length; j++) {
                for (let i = 1; i <= str1.length; i++) {
                    const indicator = str1[i - 1] === str2[j - 1] ? 0 : 1;
                    matrix[j][i] = Math.min(
                        matrix[j][i - 1] + 1,
                        matrix[j - 1][i] + 1,
                        matrix[j - 1][i - 1] + indicator
                    );
                }
            }

            return matrix[str2.length][str1.length];
        }

        findMatches(articleData, searchTerms) {
            const matches = [];

            searchTerms.forEach(term => {
                if (articleData.title.includes(term)) {
                    matches.push({ field: 'title', term });
                }
                if (articleData.excerpt.includes(term)) {
                    matches.push({ field: 'excerpt', term });
                }
            });

            return matches;
        }

        displayResults(results, query) {
            // Hide all articles first
            const allArticles = document.querySelectorAll('.edsys-article-card');
            allArticles.forEach(article => {
                article.style.display = 'none';
            });

            // Show matching articles
            results.forEach((result, index) => {
                result.element.style.display = 'block';
                result.element.style.order = index;
                this.highlightMatches(result.element, result.matches);

                // Add animation delay
                result.element.style.animationDelay = `${index * 50}ms`;
            });

            // Update search stats
            this.updateSearchStats(results.length, query);

            // Show no results message if needed
            this.toggleNoResults(results.length === 0, query);

            // Update URL
            this.updateURL(query);
        }

        highlightMatches(article, matches) {
            // Remove previous highlights
            this.removeHighlights(article);

            matches.forEach(match => {
                const element = article.querySelector(`.edsys-article-card__${match.field}`);
                if (element) {
                    this.highlightText(element, match.term);
                }
            });
        }

        highlightText(element, term) {
            const regex = new RegExp(`(${this.escapeRegex(term)})`, 'gi');
            const html = element.innerHTML;
            element.innerHTML = html.replace(regex, `<span class="${this.config.highlightClass}">$1</span>`);
        }

        removeHighlights(article) {
            const highlights = article.querySelectorAll(`.${this.config.highlightClass}`);
            highlights.forEach(highlight => {
                highlight.outerHTML = highlight.innerHTML;
            });
        }

        escapeRegex(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        updateSearchStats(count, query) {
            if (count > 0) {
                this.statsElement.innerHTML = `
                    <div class="edsys-search-stats__content">
                        <i class="ph ph-thin ph-check-circle"></i>
                        Найдено ${count} ${this.pluralize(count, 'статья', 'статьи', 'статей')} по запросу "${query}"
                    </div>
                `;
                this.statsElement.style.display = 'block';
            } else {
                this.statsElement.style.display = 'none';
            }
        }

        toggleNoResults(show, query) {
            if (show) {
                if (!this.noResultsElement) {
                    this.noResultsElement = document.createElement('div');
                    this.noResultsElement.className = 'edsys-no-results';
                    this.articlesGrid.appendChild(this.noResultsElement);
                }

                this.noResultsElement.innerHTML = `
                    <div class="edsys-no-results__content">
                        <i class="ph ph-thin ph-magnifying-glass-minus"></i>
                        <h3>По запросу "${query}" ничего не найдено</h3>
                        <p>Попробуйте изменить поисковый запрос или воспользуйтесь предложениями:</p>
                        <div class="edsys-search-suggestions-inline">
                            ${this.generateSearchSuggestions(query)}
                        </div>
                    </div>
                `;
                this.noResultsElement.style.display = 'block';
            } else if (this.noResultsElement) {
                this.noResultsElement.style.display = 'none';
            }
        }

        generateSearchSuggestions(query) {
            const commonTerms = ['заземление', 'кабель', 'напряжение', 'ток', 'мультиметр', 'реле'];
            const suggestions = commonTerms
                .filter(term => !query.toLowerCase().includes(term))
                .slice(0, 3)
                .map(term => `<button class="edsys-suggestion-btn" data-query="${term}">${term}</button>`)
                .join('');

            return suggestions;
        }

        clearSearch() {
            const allArticles = document.querySelectorAll('.edsys-article-card');
            allArticles.forEach(article => {
                article.style.display = 'block';
                article.style.order = '';
                this.removeHighlights(article);
            });

            this.statsElement.style.display = 'none';

            if (this.noResultsElement) {
                this.noResultsElement.style.display = 'none';
            }

            this.updateURL('');
        }

        showDropdown() {
            this.updateDropdownContent();
            this.dropdown.style.display = 'block';
            setTimeout(() => {
                this.dropdown.classList.add('show');
            }, 10);
        }

        hideDropdown() {
            this.dropdown.classList.remove('show');
            setTimeout(() => {
                this.dropdown.style.display = 'none';
            }, 200);
        }

        hideDropdownDelayed() {
            setTimeout(() => {
                this.hideDropdown();
            }, 200);
        }

        updateDropdownContent() {
            const popularSearches = this.dropdown.querySelector('.edsys-popular-searches');
            const historyContainer = this.dropdown.querySelector('.edsys-search-history');

            // Popular searches
            const popularQueries = ['заземление', 'кабель витая пара', 'мультиметр', 'реле напряжения'];
            popularSearches.innerHTML = popularQueries
                .map(query => `<button class="edsys-search-suggestion" data-query="${query}">${query}</button>`)
                .join('');

            // Search history
            if (searchHistory.length > 0) {
                historyContainer.innerHTML = searchHistory
                    .slice(-5)
                    .reverse()
                    .map(query => `
                        <div class="edsys-history-item">
                            <button class="edsys-search-suggestion" data-query="${query}">
                                <i class="ph ph-thin ph-clock-counter-clockwise"></i>
                                ${query}
                            </button>
                            <button class="edsys-remove-history" data-query="${query}">
                                <i class="ph ph-thin ph-x"></i>
                            </button>
                        </div>
                    `)
                    .join('');
            } else {
                historyContainer.innerHTML = '<p class="edsys-empty-history">История поиска пуста</p>';
            }
        }

        handleDropdownClick(event) {
            const suggestion = event.target.closest('.edsys-search-suggestion');
            const removeBtn = event.target.closest('.edsys-remove-history');
            const suggestionBtn = event.target.closest('.edsys-suggestion-btn');

            if (suggestion) {
                const query = suggestion.getAttribute('data-query');
                this.searchInput.value = query;
                this.performSearch(query);
                this.hideDropdown();
            } else if (removeBtn) {
                const query = removeBtn.getAttribute('data-query');
                this.removeFromHistory(query);
                this.updateDropdownContent();
            } else if (suggestionBtn) {
                const query = suggestionBtn.getAttribute('data-query');
                this.searchInput.value = query;
                this.performSearch(query);
            }
        }

        handleKeydown(event) {
            if (event.key === 'Escape') {
                this.searchInput.blur();
                this.hideDropdown();
            } else if (event.key === 'Enter') {
                event.preventDefault();
                this.hideDropdown();
            }
        }

        addToHistory(query) {
            if (query.length < this.config.minQueryLength) return;

            // Remove if already exists
            searchHistory = searchHistory.filter(item => item !== query);

            // Add to beginning
            searchHistory.unshift(query);

            // Keep only last 10 searches
            searchHistory = searchHistory.slice(0, 10);

            // Save to localStorage
            try {
                localStorage.setItem('edsys_search_history', JSON.stringify(searchHistory));
            } catch (e) {
                console.warn('Could not save search history:', e);
            }
        }

        removeFromHistory(query) {
            searchHistory = searchHistory.filter(item => item !== query);
            try {
                localStorage.setItem('edsys_search_history', JSON.stringify(searchHistory));
            } catch (e) {
                console.warn('Could not update search history:', e);
            }
        }

        loadSearchHistory() {
            try {
                const saved = localStorage.getItem('edsys_search_history');
                if (saved) {
                    searchHistory = JSON.parse(saved);
                }
            } catch (e) {
                console.warn('Could not load search history:', e);
                searchHistory = [];
            }
        }

        normalizeQuery(query) {
            return query.toLowerCase().trim().replace(/\s+/g, ' ');
        }

        updateURL(query) {
            const url = new URL(window.location);
            if (query) {
                url.searchParams.set('search', query);
            } else {
                url.searchParams.delete('search');
            }
            window.history.replaceState({}, '', url);
        }

        pluralize(count, one, few, many) {
            const mod10 = count % 10;
            const mod100 = count % 100;

            if (mod100 >= 11 && mod100 <= 19) {
                return many;
            }

            if (mod10 === 1) return one;
            if (mod10 >= 2 && mod10 <= 4) return few;
            return many;
        }

        debounce(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }

        // Public API
        search(query) {
            this.searchInput.value = query;
            this.performSearch(query);
        }

        clear() {
            this.searchInput.value = '';
            this.clearSearch();
        }

        destroy() {
            // Clean up event listeners and elements
            this.dropdown.remove();
            this.statsElement.remove();

            // Clear cache
            searchCache.clear();
        }
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.EDSAdvancedSearch = new AdvancedSearch();
        });
    } else {
        window.EDSAdvancedSearch = new AdvancedSearch();
    }

    // Add CSS for search components
    const searchStyles = `
        .edsys-search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: var(--edsys-white);
            border: 1px solid var(--edsys-border);
            border-radius: var(--radius-md);
            box-shadow: var(--edsys-shadow);
            z-index: 1000;
            display: none;
            opacity: 0;
            transform: translateY(-10px);
            transition: all 0.2s ease;
            margin-top: 4px;
        }

        .edsys-search-dropdown.show {
            opacity: 1;
            transform: translateY(0);
        }

        .edsys-search-suggestions {
            padding: var(--space-md);
            max-height: 300px;
            overflow-y: auto;
        }

        .edsys-search-section {
            margin-bottom: var(--space-lg);
        }

        .edsys-search-section:last-child {
            margin-bottom: 0;
        }

        .edsys-search-section h4 {
            font-size: var(--fs-sm);
            font-weight: var(--edsys-font-bold);
            color: var(--edsys-text-muted);
            margin: 0 0 var(--space-sm);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .edsys-search-suggestion {
            display: flex;
            align-items: center;
            gap: var(--space-sm);
            width: 100%;
            padding: var(--space-sm);
            background: none;
            border: none;
            border-radius: var(--radius-sm);
            text-align: left;
            cursor: pointer;
            transition: var(--edsys-transition);
            font-size: var(--fs-sm);
        }

        .edsys-search-suggestion:hover {
            background: var(--edsys-bg-light);
        }

        .edsys-history-item {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
        }

        .edsys-history-item .edsys-search-suggestion {
            flex: 1;
        }

        .edsys-remove-history {
            background: none;
            border: none;
            padding: var(--space-xs);
            cursor: pointer;
            color: var(--edsys-text-muted);
            border-radius: var(--radius-sm);
            transition: var(--edsys-transition);
        }

        .edsys-remove-history:hover {
            background: var(--edsys-accent);
            color: var(--edsys-white);
        }

        .edsys-empty-history {
            color: var(--edsys-text-muted);
            font-size: var(--fs-sm);
            font-style: italic;
            margin: 0;
            padding: var(--space-sm);
        }

        .edsys-search-stats {
            display: none;
            margin-top: var(--space-sm);
        }

        .edsys-search-stats__content {
            display: flex;
            align-items: center;
            gap: var(--space-xs);
            font-size: var(--fs-sm);
            color: var(--edsys-circuit);
            font-weight: var(--edsys-font-bold);
        }

        .edsys-search-highlight {
            background: var(--edsys-spark);
            color: var(--edsys-black);
            padding: 0 2px;
            border-radius: 2px;
            font-weight: var(--edsys-font-bold);
        }

        .edsys-search-suggestions-inline {
            display: flex;
            flex-wrap: wrap;
            gap: var(--space-sm);
            margin-top: var(--space-md);
        }

        .edsys-suggestion-btn {
            padding: var(--space-xs) var(--space-sm);
            background: var(--edsys-circuit);
            color: var(--edsys-white);
            border: none;
            border-radius: var(--radius-sm);
            font-size: var(--fs-xs);
            cursor: pointer;
            transition: var(--edsys-transition);
        }

        .edsys-suggestion-btn:hover {
            background: var(--edsys-voltage);
        }

        @media (max-width: 480px) {
            .edsys-search-dropdown {
                left: -1rem;
                right: -1rem;
            }
        }
    `;

    // Inject styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = searchStyles;
    document.head.appendChild(styleSheet);

})(window, document);