/**
 * Calculators Page JavaScript
 * Version: 1.0.0
 * Author: EdSys Development Team
 * Description: Interactive functionality for calculators page
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        selectors: {
            calculatorCard: '.edsys-calculator-card',
            calculatorGrid: '.edsys-calculators-grid',
            loadingClass: 'edsys-loading'
        },
        animation: {
            duration: 300,
            easing: 'cubic-bezier(0.4, 0, 0.2, 1)'
        },
        analytics: {
            enabled: true,
            eventCategory: 'Calculator Navigation'
        }
    };

    // State management
    let isNavigating = false;
    let observers = [];

    /**
     * Initialize the calculator page functionality
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupCalculators);
        } else {
            setupCalculators();
        }
    }

    /**
     * Setup calculator cards functionality
     */
    function setupCalculators() {
        const calculatorCards = document.querySelectorAll(CONFIG.selectors.calculatorCard);

        if (!calculatorCards.length) {
            console.warn('EdSys: No calculator cards found');
            return;
        }

        // Setup each calculator card
        calculatorCards.forEach(setupCalculatorCard);

        // Setup keyboard navigation
        setupKeyboardNavigation();

        // Setup intersection observer for animations
        setupIntersectionObserver();

        // Setup analytics if enabled
        if (CONFIG.analytics.enabled) {
            setupAnalytics();
        }

        console.log(`EdSys: Initialized ${calculatorCards.length} calculator cards`);
    }

    /**
     * Setup individual calculator card
     * @param {HTMLElement} card - Calculator card element
     * @param {number} index - Card index
     */
    function setupCalculatorCard(card, index) {
        const calculatorId = card.dataset.calculatorId;
        const calculatorUrl = card.dataset.calculatorUrl;
        const calculatorCategory = card.dataset.calculatorCategory;

        if (!calculatorId || !calculatorUrl) {
            console.warn('EdSys: Invalid calculator data for card', card);
            return;
        }

        // Add tabindex for keyboard navigation
        card.setAttribute('tabindex', '0');
        card.setAttribute('role', 'button');
        card.setAttribute('aria-label', `Перейти к калькулятору: ${card.querySelector('.edsys-calculator-card__title')?.textContent || calculatorId}`);

        // Add click handler
        card.addEventListener('click', (e) => handleCardClick(e, calculatorUrl, calculatorId, calculatorCategory));

        // Add keyboard handler
        card.addEventListener('keydown', (e) => handleCardKeydown(e, calculatorUrl, calculatorId, calculatorCategory));

        // Add hover effects for accessibility
        card.addEventListener('mouseenter', () => handleCardHover(card, true));
        card.addEventListener('mouseleave', () => handleCardHover(card, false));

        // Add focus/blur handlers
        card.addEventListener('focus', () => handleCardFocus(card, true));
        card.addEventListener('blur', () => handleCardFocus(card, false));

        // Store card data for later use
        card.calculatorData = {
            id: calculatorId,
            url: calculatorUrl,
            category: calculatorCategory,
            index: index
        };
    }

    /**
     * Handle calculator card click
     * @param {Event} e - Click event
     * @param {string} url - Calculator URL
     * @param {string} id - Calculator ID
     * @param {string} category - Calculator category
     */
    function handleCardClick(e, url, id, category) {
        e.preventDefault();

        if (isNavigating) return;

        const card = e.currentTarget;
        navigateToCalculator(card, url, id, category);
    }

    /**
     * Handle calculator card keyboard navigation
     * @param {Event} e - Keyboard event
     * @param {string} url - Calculator URL
     * @param {string} id - Calculator ID
     * @param {string} category - Calculator category
     */
    function handleCardKeydown(e, url, id, category) {
        if (e.key === 'Enter' || e.key === ' ') {
            e.preventDefault();

            if (isNavigating) return;

            const card = e.currentTarget;
            navigateToCalculator(card, url, id, category);
        }
    }

    /**
     * Handle card hover states
     * @param {HTMLElement} card - Calculator card
     * @param {boolean} isHovering - Hover state
     */
    function handleCardHover(card, isHovering) {
        const icon = card.querySelector('.edsys-calculator-card__icon i');

        if (icon) {
            if (isHovering) {
                icon.style.transform = 'scale(1.1)';
            } else {
                icon.style.transform = '';
            }
        }
    }

    /**
     * Handle card focus states
     * @param {HTMLElement} card - Calculator card
     * @param {boolean} isFocused - Focus state
     */
    function handleCardFocus(card, isFocused) {
        if (isFocused) {
            card.setAttribute('aria-expanded', 'true');
        } else {
            card.removeAttribute('aria-expanded');
        }
    }

    /**
     * Navigate to calculator with loading state
     * @param {HTMLElement} card - Calculator card
     * @param {string} url - Calculator URL
     * @param {string} id - Calculator ID
     * @param {string} category - Calculator category
     */
    function navigateToCalculator(card, url, id, category) {
        isNavigating = true;

        // Add loading state
        card.classList.add(CONFIG.selectors.loadingClass.substring(1));

        // Track analytics
        trackCalculatorClick(id, category);

        // Animate card before navigation
        animateCardExit(card).then(() => {
            // Navigate to calculator
            window.location.href = url;
        }).catch((error) => {
            console.error('EdSys: Navigation animation failed', error);
            // Fallback navigation
            window.location.href = url;
        });
    }

    /**
     * Animate card exit
     * @param {HTMLElement} card - Calculator card
     * @returns {Promise} Animation promise
     */
    function animateCardExit(card) {
        return new Promise((resolve) => {
            card.style.transform = 'scale(0.98)';
            card.style.opacity = '0.8';

            setTimeout(() => {
                resolve();
            }, CONFIG.animation.duration);
        });
    }

    /**
     * Setup keyboard navigation for the grid
     */
    function setupKeyboardNavigation() {
        const grid = document.querySelector(CONFIG.selectors.calculatorGrid);

        if (!grid) return;

        grid.addEventListener('keydown', (e) => {
            const focusedCard = document.activeElement;
            const cards = Array.from(grid.querySelectorAll(CONFIG.selectors.calculatorCard));
            const currentIndex = cards.indexOf(focusedCard);

            if (currentIndex === -1) return;

            let nextIndex = currentIndex;

            switch (e.key) {
                case 'ArrowRight':
                    e.preventDefault();
                    nextIndex = Math.min(currentIndex + 1, cards.length - 1);
                    break;
                case 'ArrowLeft':
                    e.preventDefault();
                    nextIndex = Math.max(currentIndex - 1, 0);
                    break;
                case 'ArrowDown':
                    e.preventDefault();
                    nextIndex = Math.min(currentIndex + 4, cards.length - 1);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    nextIndex = Math.max(currentIndex - 4, 0);
                    break;
                case 'Home':
                    e.preventDefault();
                    nextIndex = 0;
                    break;
                case 'End':
                    e.preventDefault();
                    nextIndex = cards.length - 1;
                    break;
            }

            if (nextIndex !== currentIndex) {
                cards[nextIndex].focus();
            }
        });
    }

    /**
     * Setup intersection observer for animations
     */
    function setupIntersectionObserver() {
        if (!window.IntersectionObserver) return;

        const cards = document.querySelectorAll(CONFIG.selectors.calculatorCard);

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    const card = entry.target;
                    const delay = card.calculatorData?.index * 100 || 0;

                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, delay);

                    observer.unobserve(card);
                }
            });
        }, {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        });

        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = `opacity ${CONFIG.animation.duration}ms ${CONFIG.animation.easing}, transform ${CONFIG.animation.duration}ms ${CONFIG.animation.easing}`;

            observer.observe(card);
        });

        observers.push(observer);
    }

    /**
     * Setup analytics tracking
     */
    function setupAnalytics() {
        // Google Analytics tracking (if available)
        if (typeof gtag !== 'undefined') {
            window.edsysTrackCalculatorView = () => {
                gtag('event', 'page_view', {
                    event_category: CONFIG.analytics.eventCategory,
                    event_label: 'Calculators Page'
                });
            };
        }

        // Yandex.Metrika tracking (if available)
        if (typeof ym !== 'undefined') {
            window.edsysTrackCalculatorView = () => {
                ym(window.ymCounterId, 'hit', '/kalkulyatory/', {
                    title: 'Калькуляторы - Просмотр страницы'
                });
            };
        }
    }

    /**
     * Track calculator click event
     * @param {string} calculatorId - Calculator ID
     * @param {string} category - Calculator category
     */
    function trackCalculatorClick(calculatorId, category) {
        // Google Analytics
        if (typeof gtag !== 'undefined') {
            gtag('event', 'calculator_click', {
                event_category: CONFIG.analytics.eventCategory,
                event_label: calculatorId,
                calculator_category: category
            });
        }

        // Yandex.Metrika
        if (typeof ym !== 'undefined') {
            ym(window.ymCounterId, 'reachGoal', 'calculator_click', {
                calculator_id: calculatorId,
                calculator_category: category
            });
        }

        // Custom analytics
        if (window.edsysAnalytics) {
            window.edsysAnalytics.track('calculator_navigation', {
                calculator_id: calculatorId,
                calculator_category: category,
                timestamp: Date.now()
            });
        }
    }

    /**
     * Cleanup function
     */
    function cleanup() {
        observers.forEach(observer => observer.disconnect());
        observers = [];
        isNavigating = false;
    }

    /**
     * Handle page visibility changes
     */
    function handleVisibilityChange() {
        if (document.hidden) {
            cleanup();
        } else {
            // Re-initialize if needed
            if (observers.length === 0) {
                setupIntersectionObserver();
            }
        }
    }

    // Event listeners
    document.addEventListener('visibilitychange', handleVisibilityChange);
    window.addEventListener('beforeunload', cleanup);

    // Error handling
    window.addEventListener('error', (e) => {
        console.error('EdSys Calculators: Unexpected error', e);
    });

    // Initialize
    init();

    // Export for external use
    window.EdSysCalculators = {
        init,
        cleanup,
        trackCalculatorClick,
        version: '1.0.0'
    };

})();