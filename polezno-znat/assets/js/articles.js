/**
 * EDS Articles Page JavaScript with Mobile Navigation
 * /stati-tablitsy-nagruzok/assets/js/articles.js
 */

document.addEventListener('DOMContentLoaded', function() {
    'use strict';

    // Configuration
    const CONFIG = {
        searchDelay: 300,
        loadMoreCount: 6,
        animationDuration: 300,
        scrollOffset: 100,
        mobileBreakpoint: 768
    };

    // DOM Elements
    const elements = {
        searchInput: document.getElementById('articlesSearch'),
        filterTags: document.querySelectorAll('.edsys-tag'),
        articlesGrid: document.getElementById('articlesGrid'),
        loadMoreBtn: document.getElementById('loadMoreBtn'),
        articleCards: document.querySelectorAll('.edsys-article-card'),
        mobileNavItems: document.querySelectorAll('.edsys-mobile-nav__item'),

        // Mobile navigation elements - existing HTML elements
        mobileSideNav: document.getElementById('mobileSideNavBtn'),
        mobileSideMenu: document.getElementById('mobileSideMenu'),
        mobileSideMenuClose: document.getElementById('mobileSideMenuClose'),
        mobileOverlay: document.getElementById('mobileOverlay')
    };

    // State
    let state = {
        currentFilter: 'all',
        searchQuery: '',
        visibleCount: 6,
        totalArticles: elements.articleCards.length,
        isLoading: false,
        isMobile: window.innerWidth <= CONFIG.mobileBreakpoint,
        sideMenuOpen: false
    };

    // Utility Functions
    const utils = {
        /**
         * Debounce function for search input
         */
        debounce: function(func, wait) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        },

        /**
         * Check if device is mobile
         */
        isMobile: function() {
            return window.innerWidth <= CONFIG.mobileBreakpoint;
        },

        /**
         * Check if device supports touch
         */
        isTouchDevice: function() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        },

        /**
         * Animate element with CSS transitions
         */
        animate: function(element, className, duration = CONFIG.animationDuration) {
            return new Promise(resolve => {
                element.classList.add(className);
                setTimeout(() => {
                    element.classList.remove(className);
                    resolve();
                }, duration);
            });
        },

        /**
         * Smooth scroll to element
         */
        scrollTo: function(element, offset = CONFIG.scrollOffset) {
            const elementPosition = element.offsetTop - offset;
            window.scrollTo({
                top: elementPosition,
                behavior: 'smooth'
            });
        },

        /**
         * Create loading state
         */
        showLoading: function(element, text = 'Загрузка...') {
            element.style.opacity = '0.6';
            element.style.pointerEvents = 'none';
            element.innerHTML = `<i class="ph ph-thin ph-spinner"></i> ${text}`;
        },

        /**
         * Remove loading state
         */
        hideLoading: function(element, originalText) {
            element.style.opacity = '1';
            element.style.pointerEvents = 'auto';
            element.innerHTML = originalText;
        },

        /**
         * Get article data from card element
         */
        getArticleData: function(card) {
            return {
                title: card.querySelector('.edsys-article-card__title')?.textContent?.toLowerCase() || '',
                excerpt: card.querySelector('.edsys-article-card__excerpt')?.textContent?.toLowerCase() || '',
                category: card.getAttribute('data-category') || '',
                element: card
            };
        },

        /**
         * Prevent body scroll when menu is open
         */
        toggleBodyScroll: function(disable) {
            if (disable) {
                document.body.style.overflow = 'hidden';
                document.body.style.position = 'fixed';
                document.body.style.width = '100%';
            } else {
                document.body.style.overflow = '';
                document.body.style.position = '';
                document.body.style.width = '';
            }
        }
    };

    // Mobile Navigation Class
    class MobileNavigation {
        constructor() {
            this.init();
        }

        init() {
            // Check if mobile navigation elements exist
            if (!elements.mobileSideNav || !elements.mobileSideMenu || !elements.mobileOverlay) {
                console.warn('Mobile navigation elements not found in DOM');
                return;
            }

            try {
                this.setupEventListeners();
                console.log('Mobile navigation initialized');
            } catch (error) {
                console.error('Error initializing mobile navigation:', error);
            }
        }

        setupEventListeners() {
            // Side navigation button click
            if (elements.mobileSideNav) {
                elements.mobileSideNav.addEventListener('click', this.toggleMenu.bind(this));

                // Touch events for better mobile experience
                if (utils.isTouchDevice()) {
                    elements.mobileSideNav.addEventListener('touchstart', this.handleTouchStart.bind(this));
                    elements.mobileSideNav.addEventListener('touchend', this.handleTouchEnd.bind(this));
                }
            }

            // Close button click
            if (elements.mobileSideMenuClose) {
                elements.mobileSideMenuClose.addEventListener('click', this.closeMenu.bind(this));
            }

            // Overlay click
            if (elements.mobileOverlay) {
                elements.mobileOverlay.addEventListener('click', this.closeMenu.bind(this));
            }

            // Escape key
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && state.sideMenuOpen) {
                    this.closeMenu();
                }
            });

            // Window resize
            window.addEventListener('resize', utils.debounce(() => {
                state.isMobile = utils.isMobile();

                // Close menu if switched to desktop
                if (!state.isMobile && state.sideMenuOpen) {
                    this.closeMenu();
                }
            }, 250));
        }

        handleTouchStart(e) {
            e.currentTarget.style.transform = 'translateY(-50%) scale(0.95)';
        }

        handleTouchEnd(e) {
            e.currentTarget.style.transform = 'translateY(-50%) scale(1)';
        }

        toggleMenu() {
            if (state.sideMenuOpen) {
                this.closeMenu();
            } else {
                this.openMenu();
            }
        }

        openMenu() {
            if (!elements.mobileSideMenu || !elements.mobileOverlay) return;

            state.sideMenuOpen = true;

            // Show overlay
            elements.mobileOverlay.classList.add('show');

            // Open menu
            elements.mobileSideMenu.classList.add('open');

            // Prevent body scroll
            utils.toggleBodyScroll(true);

            // Focus management
            const firstLink = elements.mobileSideMenu.querySelector('.edsys-mobile-side-menu__link');
            if (firstLink) {
                setTimeout(() => firstLink.focus(), 300);
            }

            // Analytics
            this.trackEvent('mobile_menu_open');
        }

        closeMenu() {
            if (!elements.mobileSideMenu || !elements.mobileOverlay) return;

            state.sideMenuOpen = false;

            // Hide overlay
            elements.mobileOverlay.classList.remove('show');

            // Close menu
            elements.mobileSideMenu.classList.remove('open');

            // Restore body scroll
            utils.toggleBodyScroll(false);

            // Return focus to button
            if (elements.mobileSideNav) {
                elements.mobileSideNav.focus();
            }

            // Analytics
            this.trackEvent('mobile_menu_close');
        }

        trackEvent(eventName) {
            // Integration with analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'mobile_navigation',
                    event_label: 'articles_page'
                });
            }

            if (typeof ym !== 'undefined' && window.ymCounterId) {
                ym(window.ymCounterId, 'reachGoal', eventName);
            }

            console.log('Mobile navigation event:', eventName);
        }
    }

    // Search Functionality
    const search = {
        /**
         * Initialize search functionality
         */
        init: function() {
            if (!elements.searchInput) return;

            const debouncedSearch = utils.debounce(this.performSearch.bind(this), CONFIG.searchDelay);
            elements.searchInput.addEventListener('input', debouncedSearch);
            elements.searchInput.addEventListener('keydown', this.handleKeydown.bind(this));
        },

        /**
         * Handle keydown events in search input
         */
        handleKeydown: function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                this.performSearch();
            }
        },

        /**
         * Perform search operation
         */
        performSearch: function() {
            state.searchQuery = elements.searchInput.value.toLowerCase().trim();
            this.filterArticles();
            this.updateURL();
        },

        /**
         * Filter articles based on search query and active filter
         */
        filterArticles: function() {
            let visibleCount = 0;
            let hasResults = false;

            elements.articleCards.forEach(card => {
                const articleData = utils.getArticleData(card);
                const matchesSearch = !state.searchQuery ||
                    articleData.title.includes(state.searchQuery) ||
                    articleData.excerpt.includes(state.searchQuery);
                const matchesFilter = state.currentFilter === 'all' ||
                    articleData.category.includes(state.currentFilter);

                if (matchesSearch && matchesFilter) {
                    card.style.display = 'block';
                    hasResults = true;

                    // Animate card appearance
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(20px)';

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, visibleCount * 50);

                    visibleCount++;
                } else {
                    card.style.display = 'none';
                }
            });

            this.updateNoResults(hasResults);
            this.updateLoadMoreButton(visibleCount);
        },

        /**
         * Update no results message
         */
        updateNoResults: function(hasResults) {
            let noResultsElement = document.querySelector('.edsys-no-results');

            if (!hasResults) {
                if (!noResultsElement) {
                    noResultsElement = document.createElement('div');
                    noResultsElement.className = 'edsys-no-results';
                    noResultsElement.innerHTML = `
                        <div class="edsys-no-results__content">
                            <i class="ph ph-thin ph-magnifying-glass"></i>
                            <h3>Статьи не найдены</h3>
                            <p>Попробуйте изменить поисковый запрос или фильтр</p>
                        </div>
                    `;
                    if (elements.articlesGrid) {
                        elements.articlesGrid.appendChild(noResultsElement);
                    }
                }
                noResultsElement.style.display = 'block';
            } else if (noResultsElement) {
                noResultsElement.style.display = 'none';
            }
        },

        /**
         * Update URL with search parameters
         */
        updateURL: function() {
            const url = new URL(window.location);

            if (state.searchQuery) {
                url.searchParams.set('search', state.searchQuery);
            } else {
                url.searchParams.delete('search');
            }

            if (state.currentFilter !== 'all') {
                url.searchParams.set('filter', state.currentFilter);
            } else {
                url.searchParams.delete('filter');
            }

            window.history.replaceState({}, '', url);
        },

        /**
         * Load search parameters from URL
         */
        loadFromURL: function() {
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            const filterParam = urlParams.get('filter');

            if (searchParam && elements.searchInput) {
                elements.searchInput.value = searchParam;
                state.searchQuery = searchParam.toLowerCase();
            }

            if (filterParam) {
                state.currentFilter = filterParam;
                this.updateFilterButtons();
            }

            if (searchParam || filterParam) {
                this.filterArticles();
            }
        },

        /**
         * Update filter button states
         */
        updateFilterButtons: function() {
            elements.filterTags.forEach(tag => {
                const category = tag.getAttribute('data-category');
                tag.classList.toggle('active', category === state.currentFilter);
            });
        }
    };

    // Filter Functionality
    const filter = {
        /**
         * Initialize filter functionality
         */
        init: function() {
            elements.filterTags.forEach(tag => {
                tag.addEventListener('click', this.handleFilterClick.bind(this));
            });
        },

        /**
         * Handle filter tag click
         */
        handleFilterClick: function(e) {
            const tag = e.target;
            const category = tag.getAttribute('data-category');

            if (category === state.currentFilter) return;

            state.currentFilter = category;
            search.updateFilterButtons();
            search.filterArticles();
            search.updateURL();

            // Animate filter change
            if (elements.articlesGrid) {
                utils.animate(elements.articlesGrid, 'edsys-filter-change');
            }
        }
    };

    // Load More Functionality
    const loadMore = {
        /**
         * Initialize load more functionality
         */
        init: function() {
            if (!elements.loadMoreBtn) return;

            elements.loadMoreBtn.addEventListener('click', this.handleLoadMore.bind(this));
            this.updateButton();
        },

        /**
         * Handle load more button click
         */
        handleLoadMore: function() {
            if (state.isLoading) return;

            state.isLoading = true;
            const originalText = elements.loadMoreBtn.innerHTML;

            utils.showLoading(elements.loadMoreBtn, 'Загружаем статьи...');

            // Simulate loading delay
            setTimeout(() => {
                this.loadMoreArticles();
                utils.hideLoading(elements.loadMoreBtn, originalText);
                state.isLoading = false;
            }, 1000);
        },

        /**
         * Load more articles (simulate pagination)
         */
        loadMoreArticles: function() {
            state.visibleCount += CONFIG.loadMoreCount;
            this.updateButton();

            // Here you would typically make an AJAX request to load more articles
            console.log('Loading more articles...', state.visibleCount);
        },

        /**
         * Update load more button state
         */
        updateButton: function() {
            if (!elements.loadMoreBtn) return;

            const visibleArticles = document.querySelectorAll('.edsys-article-card[style*="display: block"], .edsys-article-card:not([style*="display: none"])').length;

            if (visibleArticles >= state.totalArticles) {
                elements.loadMoreBtn.style.display = 'none';
            } else {
                elements.loadMoreBtn.style.display = 'inline-flex';
            }
        }
    };

    // Animation and Visual Effects
    const effects = {
        /**
         * Initialize visual effects
         */
        init: function() {
            this.initScrollAnimations();
            this.initHoverEffects();
        },

        /**
         * Initialize scroll-based animations
         */
        initScrollAnimations: function() {
            if (!window.IntersectionObserver) return;

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('edsys-animate-in');
                    }
                });
            }, observerOptions);

            elements.articleCards.forEach(card => {
                observer.observe(card);
            });
        },

        /**
         * Initialize hover effects
         */
        initHoverEffects: function() {
            if (utils.isTouchDevice()) return; // Skip hover effects on touch devices

            elements.articleCards.forEach(card => {
                card.addEventListener('mouseenter', this.handleCardHover.bind(this));
                card.addEventListener('mouseleave', this.handleCardLeave.bind(this));
            });
        },

        /**
         * Handle card hover
         */
        handleCardHover: function(e) {
            const card = e.currentTarget;
            const sparks = card.querySelector('.edsys-article-card__sparks');

            if (sparks) {
                sparks.style.animationPlayState = 'running';
            }
        },

        /**
         * Handle card leave
         */
        handleCardLeave: function(e) {
            const card = e.currentTarget;
            const sparks = card.querySelector('.edsys-article-card__sparks');

            if (sparks) {
                sparks.style.animationPlayState = 'paused';
            }
        }
    };

    // Performance Optimization
    const performance = {
        /**
         * Initialize performance optimizations
         */
        init: function() {
            this.lazyLoadImages();
            this.optimizeAnimations();
        },

        /**
         * Lazy load images
         */
        lazyLoadImages: function() {
            const images = document.querySelectorAll('img[loading="lazy"]');

            if ('IntersectionObserver' in window) {
                const imageObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const img = entry.target;
                            img.src = img.dataset.src || img.src;
                            img.classList.remove('lazy');
                            imageObserver.unobserve(img);
                        }
                    });
                });

                images.forEach(img => imageObserver.observe(img));
            }
        },

        /**
         * Optimize animations for performance
         */
        optimizeAnimations: function() {
            // Pause animations when page is not visible
            document.addEventListener('visibilitychange', () => {
                const animations = document.querySelectorAll('.edsys-article-card__sparks');

                animations.forEach(animation => {
                    if (document.hidden) {
                        animation.style.animationPlayState = 'paused';
                    } else {
                        animation.style.animationPlayState = 'running';
                    }
                });
            });
        }
    };

    // Analytics and Tracking
    const analytics = {
        /**
         * Initialize analytics tracking
         */
        init: function() {
            this.trackSearchEvents();
            this.trackFilterEvents();
            this.trackArticleClicks();
        },

        /**
         * Track search events
         */
        trackSearchEvents: function() {
            if (!elements.searchInput) return;

            elements.searchInput.addEventListener('input', utils.debounce(() => {
                if (elements.searchInput.value.length > 2) {
                    this.sendEvent('search', {
                        query: elements.searchInput.value,
                        results_count: document.querySelectorAll('.edsys-article-card[style*="display: block"]').length
                    });
                }
            }, 1000));
        },

        /**
         * Track filter events
         */
        trackFilterEvents: function() {
            elements.filterTags.forEach(tag => {
                tag.addEventListener('click', () => {
                    this.sendEvent('filter', {
                        category: tag.getAttribute('data-category'),
                        results_count: document.querySelectorAll('.edsys-article-card[style*="display: block"]').length
                    });
                });
            });
        },

        /**
         * Track article clicks
         */
        trackArticleClicks: function() {
            elements.articleCards.forEach(card => {
                card.addEventListener('click', () => {
                    const title = card.querySelector('.edsys-article-card__title')?.textContent;
                    const url = card.querySelector('.edsys-article-card__link')?.href;

                    this.sendEvent('article_click', {
                        title: title,
                        url: url,
                        position: Array.from(elements.articleCards).indexOf(card) + 1
                    });
                });
            });
        },

        /**
         * Send analytics event
         */
        sendEvent: function(eventName, data) {
            // Integration with Google Analytics, Yandex.Metrika, etc.
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, data);
            }

            if (typeof ym !== 'undefined' && window.ymCounterId) {
                ym(window.ymCounterId, 'reachGoal', eventName, data);
            }

            console.log('Analytics event:', eventName, data);
        }
    };

    // Initialization
    const init = function() {
        try {
            // Update mobile state
            state.isMobile = utils.isMobile();

            // Initialize all modules
            search.init();
            filter.init();
            loadMore.init();
            effects.init();
            performance.init();
            analytics.init();

            // Initialize mobile navigation - always initialize, but elements will only exist on mobile
            const mobileNav = new MobileNavigation();

            // Load initial state from URL
            search.loadFromURL();

            // Add custom CSS for animations
            const style = document.createElement('style');
            style.textContent = `
                .edsys-article-card {
                    transition: opacity 0.3s ease, transform 0.3s ease;
                }
                
                .edsys-animate-in {
                    animation: slideInUp 0.6s ease forwards;
                }
                
                .edsys-filter-change {
                    animation: filterChange 0.3s ease;
                }
                
                .edsys-no-results {
                    grid-column: 1 / -1;
                    text-align: center;
                    padding: 3rem;
                    color: var(--edsys-text-muted);
                }
                
                .edsys-no-results__content h3 {
                    margin: 1rem 0 0.5rem;
                    font-size: 1.5rem;
                }
                
                .edsys-no-results__content i {
                    font-size: 3rem;
                    opacity: 0.5;
                }
                
                @keyframes slideInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }
                
                @keyframes filterChange {
                    0% { opacity: 1; }
                    50% { opacity: 0.7; }
                    100% { opacity: 1; }
                }
                
                @media (prefers-reduced-motion: reduce) {
                    .edsys-article-card,
                    .edsys-animate-in,
                    .edsys-filter-change {
                        animation: none !important;
                        transition: none !important;
                    }
                }
            `;
            document.head.appendChild(style);

            console.log('EDS Articles page initialized successfully');

        } catch (error) {
            console.error('Error initializing EDS Articles page:', error);
        }
    };

    // Start initialization
    init();

    // Expose public API
    window.EDSArticles = {
        search: search,
        filter: filter,
        loadMore: loadMore,
        utils: utils,
        state: state
    };
});