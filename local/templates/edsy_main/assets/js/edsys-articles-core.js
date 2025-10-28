/**
 * EDS Articles Core JavaScript v1.4 - CLEANED VERSION
 * Основная функциональность для статей без лишних модулей
 *
 * @author EDS Development Team
 * @version 1.4
 * @since 2024
 */

(function(window, document) {
    'use strict';

    // Configuration
    const EDSYS_CONFIG = {
        mobileBreakpoint: 1024,
        animationDuration: 300,
        scrollOffset: 100,
        debounceDelay: 250,
        throttleLimit: 100
    };

    // State management
    let edsysState = {
        isMobile: window.innerWidth <= EDSYS_CONFIG.mobileBreakpoint,
        mobileNavOpen: false,
        activeElement: null,
        scrollPosition: 0,
        initialized: false
    };

    /**
     * EDS Articles Core Class
     * Основной класс для управления функциональностью статей
     */
    class EDSArticlesCore {
        constructor() {
            this.elements = {};
            this.observers = {};
            this.eventListeners = [];
            this.initialized = false;

            this.init();
        }

        /**
         * Initialize core functionality
         * Инициализация основной функциональности
         */
        init() {
            try {
                if (this.initialized) {
                    return;
                }

                this.setupElements();
                this.setupSmoothScroll();
                this.setupScrollAnimations();
                this.setupResizeHandler();
                this.setupCategoryBlocks();

                this.initialized = true;
                edsysState.initialized = true;

                // Trigger custom event
                this.triggerEvent('edsys:core:initialized', {
                    timestamp: Date.now(),
                    config: EDSYS_CONFIG
                });

            } catch (error) {
                console.error('Core initialization failed:', error);
            }
        }

        /**
         * Setup DOM elements
         * Настройка DOM элементов
         */
        setupElements() {
            this.elements = {
                body: document.body,
                html: document.documentElement,
                articlePage: document.querySelector('.edsys-article-page'),
                articleContent: document.querySelector('.edsys-article-content'),
                animatedSections: document.querySelectorAll('.edsys-content-section, .edsys-step-item, .edsys-comparison-card, .edsys-category-card'),
                scrollLinks: document.querySelectorAll('a[href^="#"]'),
                categoryBlocks: document.querySelectorAll('.edsys-product-categories'),
                categoryCards: document.querySelectorAll('.edsys-category-card')
            };

            if (!this.elements.body || !this.elements.html) {
                throw new Error('Critical DOM elements not found');
            }
        }

        /**
         * Setup category blocks functionality
         * Настройка функциональности блоков категорий
         */
        setupCategoryBlocks() {
            // Ensure category blocks are visible
            this.elements.categoryBlocks.forEach((block, index) => {
                if (!block) return;

                // Force visibility
                block.style.display = 'block';
                block.style.visibility = 'visible';
                block.style.opacity = '1';

                // Add loading class temporarily
                block.classList.add('edsys-categories-loading');

                // Remove loading class after a delay
                setTimeout(() => {
                    block.classList.remove('edsys-categories-loading');
                    block.classList.add('edsys-categories-loaded');
                }, 300);
            });

            // Setup category card interactions
            this.elements.categoryCards.forEach((card, index) => {
                if (!card) return;

                // Add click tracking
                const clickHandler = (e) => {
                    const categoryName = card.querySelector('.edsys-category-name')?.textContent?.trim();
                    const categoryUrl = card.getAttribute('href');

                    this.trackEvent('category_card_click', {
                        category_name: categoryName,
                        category_url: categoryUrl,
                        card_index: index
                    });
                };

                card.addEventListener('click', clickHandler);
                this.eventListeners.push({ element: card, event: 'click', handler: clickHandler });
            });
        }

        /**
         * Setup smooth scroll for anchor links
         * Настройка плавной прокрутки для якорных ссылок
         */
        setupSmoothScroll() {
            this.elements.scrollLinks.forEach((anchor, index) => {
                const clickHandler = (e) => {
                    const href = anchor.getAttribute('href');
                    if (!href || !href.startsWith('#')) return;

                    const targetId = href.substring(1);
                    if (!targetId) return;

                    const targetElement = document.getElementById(targetId);
                    if (targetElement) {
                        e.preventDefault();
                        this.scrollToElement(targetElement, targetId, index);
                    }
                };

                anchor.addEventListener('click', clickHandler);
                this.eventListeners.push({ element: anchor, event: 'click', handler: clickHandler });
            });
        }

        /**
         * Scroll to specific element
         * Прокрутка к определенному элементу
         */
        scrollToElement(element, targetId = null, linkIndex = null) {
            try {
                const elementPosition = element.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - EDSYS_CONFIG.scrollOffset;

                // Use smooth scrolling
                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update URL if targetId provided
                if (targetId) {
                    history.pushState(null, null, '#' + targetId);
                }

                // Focus management for accessibility
                element.focus({ preventScroll: true });

                // Track event
                this.trackEvent('smooth_scroll', {
                    target_id: targetId,
                    element_tag: element.tagName.toLowerCase(),
                    link_index: linkIndex
                });

            } catch (error) {
                console.error('Smooth scroll failed:', error);
            }
        }

        /**
         * Setup scroll animations
         * Настройка анимаций при прокрутке
         */
        setupScrollAnimations() {
            if (!window.IntersectionObserver) {
                return;
            }

            try {
                const observerOptions = {
                    threshold: [0.1, 0.5],
                    rootMargin: '0px 0px -50px 0px'
                };

                this.observers.scrollAnimation = new IntersectionObserver((entries) => {
                    entries.forEach((entry, index) => {
                        if (entry.isIntersecting && entry.intersectionRatio > 0.1) {
                            // Add animation class
                            entry.target.classList.add('edsys-animate-in');

                            // Add staggered animation for child elements
                            const children = entry.target.querySelectorAll('.edsys-step-item, .edsys-comparison-card, .edsys-danger-level, .edsys-solution-card, .edsys-category-card');
                            children.forEach((child, childIndex) => {
                                setTimeout(() => {
                                    child.classList.add('edsys-animate-in');
                                }, childIndex * 100);
                            });

                            // Unobserve after animation
                            this.observers.scrollAnimation.unobserve(entry.target);
                        }
                    });
                }, observerOptions);

                // Observe sections
                this.elements.animatedSections.forEach(section => {
                    if (section) {
                        this.observers.scrollAnimation.observe(section);
                    }
                });

                // Add CSS for animations
                this.addScrollAnimationCSS();

            } catch (error) {
                console.error('Failed to setup scroll animations:', error);
            }
        }

        /**
         * Add CSS for scroll animations
         * Добавление CSS для анимаций прокрутки
         */
        addScrollAnimationCSS() {
            if (document.getElementById('edsys-scroll-animations')) return;

            const style = document.createElement('style');
            style.id = 'edsys-scroll-animations';
            style.textContent = `
                .edsys-content-section,
                .edsys-step-item,
                .edsys-comparison-card,
                .edsys-danger-level,
                .edsys-solution-card,
                .edsys-category-card,
                .edsys-product-categories {
                    opacity: 0;
                    transform: translateY(30px);
                    transition: opacity 0.6s ease, transform 0.6s ease;
                }

                .edsys-animate-in {
                    opacity: 1 !important;
                    transform: translateY(0) !important;
                }

                .edsys-categories-loading {
                    opacity: 0.5;
                    transform: translateY(10px);
                }

                .edsys-categories-loaded {
                    opacity: 1;
                    transform: translateY(0);
                    transition: opacity 0.4s ease, transform 0.4s ease;
                }

                @media (prefers-reduced-motion: reduce) {
                    .edsys-content-section,
                    .edsys-step-item,
                    .edsys-comparison-card,
                    .edsys-danger-level,
                    .edsys-solution-card,
                    .edsys-category-card,
                    .edsys-product-categories {
                        opacity: 1 !important;
                        transform: none !important;
                        transition: none !important;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        /**
         * Setup resize handler
         * Настройка обработчика изменения размера окна
         */
        setupResizeHandler() {
            const resizeHandler = this.debounce(() => {
                const wasMobile = edsysState.isMobile;
                edsysState.isMobile = window.innerWidth <= EDSYS_CONFIG.mobileBreakpoint;

                if (wasMobile !== edsysState.isMobile) {
                    this.handleBreakpointChange(edsysState.isMobile);
                }

                // Update scroll position
                edsysState.scrollPosition = window.pageYOffset;

                // Trigger custom event
                this.triggerEvent('edsys:resize', {
                    isMobile: edsysState.isMobile,
                    width: window.innerWidth,
                    height: window.innerHeight
                });

            }, EDSYS_CONFIG.debounceDelay);

            window.addEventListener('resize', resizeHandler);
            this.eventListeners.push({ element: window, event: 'resize', handler: resizeHandler });
        }

        /**
         * Handle breakpoint change
         * Обработка изменения точки останова
         */
        handleBreakpointChange(isMobile) {
            // Close any open mobile elements when switching to desktop
            if (!isMobile && edsysState.mobileNavOpen) {
                this.triggerEvent('edsys:closeMobileNav');
            }

            // Track change
            this.trackEvent('breakpoint_change', {
                new_mode: isMobile ? 'mobile' : 'desktop',
                width: window.innerWidth
            });
        }

        /**
         * Toggle body scroll
         * Переключение прокрутки body
         */
        toggleBodyScroll(disable) {
            if (disable) {
                edsysState.scrollPosition = window.pageYOffset;
                this.elements.body.style.overflow = 'hidden';
                this.elements.body.style.position = 'fixed';
                this.elements.body.style.top = `-${edsysState.scrollPosition}px`;
                this.elements.body.style.width = '100%';
                this.elements.body.style.left = '0';
                this.elements.body.style.right = '0';
            } else {
                this.elements.body.style.overflow = '';
                this.elements.body.style.position = '';
                this.elements.body.style.top = '';
                this.elements.body.style.width = '';
                this.elements.body.style.left = '';
                this.elements.body.style.right = '';
                window.scrollTo(0, edsysState.scrollPosition);
            }
        }

        /**
         * Event tracking
         * Отслеживание событий
         */
        trackEvent(eventName, data = {}) {
            try {
                const eventData = {
                    event_category: 'edsys_articles',
                    event_label: window.location.pathname,
                    custom_data: data,
                    timestamp: Date.now(),
                    viewport: {
                        width: window.innerWidth,
                        height: window.innerHeight
                    },
                    page_url: window.location.href
                };

                // Custom analytics
                this.triggerEvent('edsys:analytics', { eventName, data: eventData });

            } catch (error) {
                console.error('Analytics tracking failed:', error);
            }
        }

        /**
         * Trigger custom event
         * Запуск пользовательского события
         */
        triggerEvent(eventName, data = {}) {
            try {
                const event = new CustomEvent(eventName, {
                    detail: data,
                    bubbles: true,
                    cancelable: true
                });
                document.dispatchEvent(event);
            } catch (error) {
                console.error(`Failed to trigger event: ${eventName}`, error);
            }
        }

        /**
         * Debounce utility
         * Утилита debounce
         */
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

        /**
         * Throttle utility
         * Утилита throttle
         */
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
         * Публичные методы API
         */
        goToSection(sectionId) {
            const element = document.getElementById(sectionId);
            if (element) {
                this.scrollToElement(element, sectionId);
            }
        }

        getCurrentBreakpoint() {
            return edsysState.isMobile ? 'mobile' : 'desktop';
        }

        getState() {
            return { ...edsysState };
        }

        forceShowCategories() {
            this.elements.categoryBlocks.forEach(block => {
                if (block) {
                    block.style.display = 'block';
                    block.style.visibility = 'visible';
                    block.style.opacity = '1';
                    block.classList.add('edsys-categories-loaded');
                }
            });
        }

        /**
         * Destroy and cleanup
         * Уничтожение и очистка
         */
        destroy() {
            try {
                // Remove event listeners
                this.eventListeners.forEach(({ element, event, handler }) => {
                    if (element && element.removeEventListener) {
                        element.removeEventListener(event, handler);
                    }
                });

                // Disconnect observers
                Object.values(this.observers).forEach(observer => {
                    if (observer && observer.disconnect) {
                        observer.disconnect();
                    }
                });

                // Restore body scroll
                this.toggleBodyScroll(false);

                // Clear state
                edsysState = {
                    isMobile: false,
                    mobileNavOpen: false,
                    activeElement: null,
                    scrollPosition: 0,
                    initialized: false
                };

                this.initialized = false;

            } catch (error) {
                console.error('Destroy failed:', error);
            }
        }
    }

    // Initialize and expose global API
    window.EDSArticlesCore = {
        instance: null,

        init: function() {
            if (!this.instance) {
                this.instance = new EDSArticlesCore();
            }
            return this.instance;
        },

        destroy: function() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },

        goToSection: function(sectionId) {
            if (this.instance && this.instance.initialized) {
                this.instance.goToSection(sectionId);
            }
        },

        getCurrentBreakpoint: function() {
            return this.instance ? this.instance.getCurrentBreakpoint() : 'unknown';
        },

        getState: function() {
            return this.instance ? this.instance.getState() : {};
        },

        trackEvent: function(eventName, data = {}) {
            if (this.instance && this.instance.initialized) {
                this.instance.trackEvent(eventName, data);
            }
        },

        forceShowCategories: function() {
            if (this.instance && this.instance.initialized) {
                this.instance.forceShowCategories();
            }
        },

        // Utility functions
        debounce: function(func, wait) {
            return this.instance ? this.instance.debounce(func, wait) : func;
        },

        throttle: function(func, limit) {
            return this.instance ? this.instance.throttle(func, limit) : func;
        }
    };

    // Auto-initialize on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.EDSArticlesCore.init();
        });
    } else {
        // Document already loaded
        window.EDSArticlesCore.init();
    }

    // Handle page load from history (back/forward buttons)
    window.addEventListener('popstate', () => {
        const hash = window.location.hash.substring(1);
        if (hash && window.EDSArticlesCore.instance) {
            window.EDSArticlesCore.goToSection(hash);
        }
    });

    // Handle page visibility changes
    document.addEventListener('visibilitychange', () => {
        if (document.visibilityState === 'visible' && window.EDSArticlesCore.instance) {
            // Force show categories when page becomes visible
            setTimeout(() => {
                window.EDSArticlesCore.forceShowCategories();
            }, 100);
        }
    });

})(window, document);