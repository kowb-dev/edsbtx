/**
 * Contact Load Classification Page JavaScript
 * Функциональность страницы классификации типов нагрузки контактов
 */

(function(window, document) {
    'use strict';

    // Configuration - Updated breakpoint to match useful-info-navigation
    const CONFIG = {
        mobileBreakpoint: 1024, // Updated to match useful-info-navigation
        animationDuration: 300,
        scrollOffset: 100,
        intersectionThreshold: 0.3
    };

    // State
    let state = {
        isMobile: window.innerWidth <= CONFIG.mobileBreakpoint,
        activeSection: null,
        tableObserver: null
    };

    // DOM Elements
    let elements = {};

    /**
     * Classification page class
     */
    class ClassificationPage {
        constructor() {
            this.init();
        }

        init() {
            try {
                this.setupElements();
                this.setupTableEnhancements();
                this.setupScrollAnimations();
                this.setupKeyboardNavigation();
                this.setupIntegrationWithUsefulNav();
                this.setupResponsiveHandling();
                this.setupAnalytics();
                console.log('Classification page initialized with 1024px breakpoint');
            } catch (error) {
                console.error('Error initializing Classification page:', error);
            }
        }

        setupElements() {
            elements = {
                table: document.querySelector('.edsys-classification__table'),
                tableWrapper: document.querySelector('.edsys-classification__table-wrapper'),
                tableRows: document.querySelectorAll('.edsys-classification__table tbody tr'),
                noteCards: document.querySelectorAll('.edsys-classification__note-card'),
                productCards: document.querySelectorAll('.edsys-classification__product-card'),
                categories: document.querySelectorAll('.edsys-classification__category'),
                sections: document.querySelectorAll('.edsys-classification__table-section, .edsys-classification__notes, .edsys-classification__products')
            };
        }

        /**
         * Setup table enhancements for better UX
         */
        setupTableEnhancements() {
            if (!elements.tableWrapper || !elements.table) return;

            // Add scroll indicators for horizontal scrolling
            this.setupScrollIndicators();

            // Enhanced keyboard navigation for table rows
            this.setupTableKeyboardNavigation();

            // Category cell interactions
            this.setupCategoryInteractions();
        }

        setupScrollIndicators() {
            if (elements.tableWrapper.scrollWidth > elements.tableWrapper.clientWidth) {
                elements.tableWrapper.classList.add('has-scroll');

                elements.tableWrapper.addEventListener('scroll', this.handleTableScroll.bind(this));

                // Initial scroll state
                elements.tableWrapper.dispatchEvent(new Event('scroll'));
            }
        }

        handleTableScroll() {
            if (!elements.tableWrapper) return;

            const isAtStart = elements.tableWrapper.scrollLeft <= 0;
            const isAtEnd = elements.tableWrapper.scrollLeft >=
                elements.tableWrapper.scrollWidth - elements.tableWrapper.clientWidth;

            elements.tableWrapper.classList.toggle('scroll-at-start', isAtStart);
            elements.tableWrapper.classList.toggle('scroll-at-end', isAtEnd);
        }

        setupTableKeyboardNavigation() {
            elements.tableRows.forEach((row, index) => {
                row.setAttribute('tabindex', '0');
                row.setAttribute('role', 'button');
                row.setAttribute('aria-label', `Строка таблицы ${index + 1}`);

                row.addEventListener('keydown', (e) => {
                    this.handleTableRowKeydown(e, index);
                });

                row.addEventListener('click', () => {
                    this.highlightTableRow(row);
                });
            });
        }

        handleTableRowKeydown(event, currentIndex) {
            const { key } = event;

            switch (key) {
                case 'ArrowDown':
                    event.preventDefault();
                    if (elements.tableRows[currentIndex + 1]) {
                        elements.tableRows[currentIndex + 1].focus();
                    }
                    break;

                case 'ArrowUp':
                    event.preventDefault();
                    if (elements.tableRows[currentIndex - 1]) {
                        elements.tableRows[currentIndex - 1].focus();
                    }
                    break;

                case 'Home':
                    event.preventDefault();
                    elements.tableRows[0]?.focus();
                    break;

                case 'End':
                    event.preventDefault();
                    elements.tableRows[elements.tableRows.length - 1]?.focus();
                    break;

                case 'Enter':
                case ' ':
                    event.preventDefault();
                    this.highlightTableRow(elements.tableRows[currentIndex]);
                    break;
            }
        }

        highlightTableRow(row) {
            // Remove previous highlights
            elements.tableRows.forEach(r => r.classList.remove('highlighted'));

            // Add highlight to selected row
            row.classList.add('highlighted');

            // Analytics
            const category = row.querySelector('.edsys-classification__category')?.textContent?.trim();
            this.trackEvent('table_row_highlight', { category });

            // Remove highlight after 3 seconds
            setTimeout(() => {
                row.classList.remove('highlighted');
            }, 3000);
        }

        setupCategoryInteractions() {
            elements.categories.forEach(category => {
                category.addEventListener('click', () => {
                    const categoryText = category.textContent.trim();
                    this.showCategoryDetails(categoryText);
                });
            });
        }

        showCategoryDetails(categoryText) {
            // Find the row containing this category
            const row = Array.from(elements.tableRows).find(row =>
                row.querySelector('.edsys-classification__category')?.textContent?.trim() === categoryText
            );

            if (row) {
                this.highlightTableRow(row);
                row.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        /**
         * Setup scroll animations for cards and sections
         */
        setupScrollAnimations() {
            if (!window.IntersectionObserver) return;

            const observerOptions = {
                threshold: CONFIG.intersectionThreshold,
                rootMargin: '-20% 0px -20% 0px'
            };

            const sectionObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                    }
                });
            }, observerOptions);

            // Observe all sections and cards
            [...elements.sections, ...elements.noteCards, ...elements.productCards].forEach(element => {
                sectionObserver.observe(element);
            });

            state.tableObserver = sectionObserver;
        }

        /**
         * Setup keyboard navigation shortcuts
         */
        setupKeyboardNavigation() {
            document.addEventListener('keydown', (e) => {
                // Global keyboard shortcuts
                if (e.ctrlKey || e.metaKey) {
                    switch (e.key) {
                        case 'f':
                            // Focus search (if useful nav is open)
                            e.preventDefault();
                            this.focusSearch();
                            break;

                        case '1':
                        case '2':
                        case '3':
                        case '4':
                        case '5':
                        case '6':
                        case '7':
                            // Quick navigation to categories
                            e.preventDefault();
                            this.navigateToCategory(parseInt(e.key) - 1);
                            break;
                    }
                }

                // ESC key handling
                if (e.key === 'Escape') {
                    this.clearHighlights();
                }
            });
        }

        focusSearch() {
            // Try to focus search in useful info navigation if available
            const searchInput = document.querySelector('.edsys-useful-nav input[type="search"]');
            if (searchInput) {
                searchInput.focus();
            }
        }

        navigateToCategory(index) {
            if (elements.tableRows[index]) {
                elements.tableRows[index].focus();
                elements.tableRows[index].scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        clearHighlights() {
            elements.tableRows.forEach(row => row.classList.remove('highlighted'));
        }

        /**
         * Setup integration with useful info navigation
         */
        setupIntegrationWithUsefulNav() {
            // Wait for useful info navigation to be ready
            const checkUsefulNav = () => {
                if (window.EDSUsefulNavigation && window.EDSUsefulNavigation.instance) {
                    console.log('Integrated with Useful Info Navigation (1024px breakpoint)');
                    this.coordinateWithUsefulNav();
                } else {
                    setTimeout(checkUsefulNav, 100);
                }
            };

            checkUsefulNav();
        }

        coordinateWithUsefulNav() {
            const usefulNavInstance = window.EDSUsefulNavigation.instance;

            if (usefulNavInstance) {
                // Share responsive state
                state.isMobile = usefulNavInstance.isMobile ? usefulNavInstance.isMobile() : state.isMobile;

                console.log('Classification page coordinated with navigation:',
                    state.isMobile ? 'Mobile' : 'Desktop');
            }
        }

        /**
         * Setup responsive handling
         */
        setupResponsiveHandling() {
            window.addEventListener('resize', this.debounce(() => {
                const wasMobile = state.isMobile;
                state.isMobile = window.innerWidth <= CONFIG.mobileBreakpoint;

                if (wasMobile !== state.isMobile) {
                    console.log('Classification responsive mode changed:',
                        state.isMobile ? 'Mobile' : 'Desktop', `(${window.innerWidth}px)`);

                    // Refresh table scroll indicators
                    if (elements.tableWrapper) {
                        elements.tableWrapper.dispatchEvent(new Event('scroll'));
                    }

                    // Update keyboard navigation for mobile
                    this.updateMobileKeyboardHandling();
                }
            }, 250));
        }

        updateMobileKeyboardHandling() {
            if (state.isMobile) {
                // Disable some keyboard shortcuts on mobile
                elements.tableRows.forEach(row => {
                    row.setAttribute('tabindex', '-1');
                });
            } else {
                // Re-enable keyboard navigation on desktop
                elements.tableRows.forEach(row => {
                    row.setAttribute('tabindex', '0');
                });
            }
        }

        /**
         * Setup analytics and tracking
         */
        setupAnalytics() {
            // Track product card clicks
            elements.productCards.forEach(card => {
                card.addEventListener('click', () => {
                    const title = card.querySelector('.edsys-classification__product-title')?.textContent;
                    const url = card.href;

                    this.trackEvent('product_click', {
                        title: title,
                        url: url,
                        category: 'classification_products'
                    });
                });
            });

            // Track table interactions
            elements.tableRows.forEach((row, index) => {
                row.addEventListener('focus', () => {
                    const category = row.querySelector('.edsys-classification__category')?.textContent?.trim();

                    this.trackEvent('table_row_focus', {
                        category: category,
                        position: index + 1
                    });
                });
            });

            // Track scroll depth
            this.setupScrollDepthTracking();
        }

        setupScrollDepthTracking() {
            let maxScrollDepth = 0;
            const trackingPoints = [25, 50, 75, 100];
            const trackedPoints = new Set();

            const trackScrollDepth = this.throttle(() => {
                const scrollTop = window.pageYOffset;
                const docHeight = document.documentElement.scrollHeight - window.innerHeight;
                const scrollPercent = Math.round((scrollTop / docHeight) * 100);

                if (scrollPercent > maxScrollDepth) {
                    maxScrollDepth = scrollPercent;
                }

                trackingPoints.forEach(point => {
                    if (scrollPercent >= point && !trackedPoints.has(point)) {
                        trackedPoints.add(point);
                        this.trackEvent('scroll_depth', {
                            depth_percent: point,
                            page: 'classification'
                        });
                    }
                });
            }, 250);

            window.addEventListener('scroll', trackScrollDepth);
        }

        /**
         * Utility functions
         */
        trackEvent(eventName, data = {}) {
            // Integration with analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'classification_page',
                    event_label: data.title || data.category || '',
                    ...data
                });
            }

            if (typeof ym !== 'undefined' && window.ymCounterId) {
                ym(window.ymCounterId, 'reachGoal', eventName, data);
            }

            console.log('Classification event:', eventName, data);
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

        throttle(func, limit) {
            let inThrottle;
            return function() {
                const args = arguments;
                const context = this;
                if (!inThrottle) {
                    func.apply(context, args);
                    inThrottle = true;
                    setTimeout(() => inThrottle = false, limit);
                }
            };
        }

        /**
         * Public API methods
         */
        highlightCategory(categoryName) {
            const category = Array.from(elements.categories).find(cat =>
                cat.textContent.trim() === categoryName
            );

            if (category) {
                this.showCategoryDetails(categoryName);
            }
        }

        scrollToSection(sectionName) {
            const sectionMap = {
                'table': elements.tableWrapper,
                'notes': document.querySelector('.edsys-classification__notes'),
                'products': document.querySelector('.edsys-classification__products')
            };

            const section = sectionMap[sectionName];
            if (section) {
                section.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        getCurrentBreakpoint() {
            return state.isMobile ? 'mobile' : 'desktop';
        }

        destroy() {
            // Clean up observers and event listeners
            if (state.tableObserver) {
                state.tableObserver.disconnect();
            }

            // Remove event listeners
            window.removeEventListener('resize', this.setupResponsiveHandling);
            document.removeEventListener('keydown', this.setupKeyboardNavigation);

            console.log('Classification page destroyed');
        }
    }

    // Initialize when DOM is ready
    let classificationInstance = null;

    function initClassificationPage() {
        if (!classificationInstance) {
            classificationInstance = new ClassificationPage();
        }
        return classificationInstance;
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initClassificationPage);
    } else {
        initClassificationPage();
    }

    // Expose global API
    window.EDSClassification = {
        instance: classificationInstance,

        init: function() {
            return initClassificationPage();
        },

        highlightCategory: function(categoryName) {
            if (classificationInstance) {
                classificationInstance.highlightCategory(categoryName);
            }
        },

        scrollToSection: function(sectionName) {
            if (classificationInstance) {
                classificationInstance.scrollToSection(sectionName);
            }
        },

        getCurrentBreakpoint: function() {
            return classificationInstance ? classificationInstance.getCurrentBreakpoint() : 'unknown';
        },

        destroy: function() {
            if (classificationInstance) {
                classificationInstance.destroy();
                classificationInstance = null;
            }
        }
    };

    // Auto-coordination with useful info navigation when it becomes available
    document.addEventListener('usefulNavReady', function() {
        console.log('Useful Info Navigation ready, setting up coordination with 1024px breakpoint');
        if (classificationInstance) {
            classificationInstance.setupIntegrationWithUsefulNav();
        }
    });

    // Add custom CSS for animations and interactions
    const customStyles = `
        .edsys-classification__table tbody tr.highlighted {
            background-color: color-mix(in srgb, var(--edsys-accent) 10%, transparent) !important;
            animation: highlightPulse 0.5s ease-in-out;
        }

        .animate-in {
            animation: slideInUp 0.6s ease forwards;
        }

        @keyframes highlightPulse {
            0% { background-color: color-mix(in srgb, var(--edsys-accent) 20%, transparent); }
            50% { background-color: color-mix(in srgb, var(--edsys-accent) 5%, transparent); }
            100% { background-color: color-mix(in srgb, var(--edsys-accent) 10%, transparent); }
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

        @media (prefers-reduced-motion: reduce) {
            .edsys-classification__table tbody tr.highlighted,
            .animate-in {
                animation: none !important;
            }
        }
    `;

    // Inject custom styles
    const styleSheet = document.createElement('style');
    styleSheet.textContent = customStyles;
    document.head.appendChild(styleSheet);

})(window, document);