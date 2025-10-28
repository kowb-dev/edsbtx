/**
 * EDS Articles Utilities JavaScript v1.1 - CLEANED VERSION
 * Основные утилиты без лишних модулей
 *
 * @author EDS Development Team
 * @version 1.1
 * @since 2024
 */

(function(window, document) {
    'use strict';

    /**
     * EDS Articles Utilities Class
     * Класс основных утилит
     */
    class EDSArticlesUtils {
        constructor() {
            this.cache = new Map();
            this.initialized = false;
            this.init();
        }

        /**
         * Initialize utilities
         * Инициализация утилит
         */
        init() {
            try {
                this.setupAccessibility();
                this.setupLazyLoading();

                this.initialized = true;

            } catch (error) {
                console.error('Utils initialization failed:', error);
            }
        }

        /**
         * Accessibility improvements
         * Улучшения доступности
         */
        setupAccessibility() {
            // Keyboard focus management
            const focusableElements = document.querySelectorAll(
                'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
            );

            focusableElements.forEach(element => {
                // Add keyboard focus indicator
                element.addEventListener('keydown', (e) => {
                    if (e.key === 'Tab') {
                        element.setAttribute('data-edsys-keyboard-focus', 'true');
                    }
                });

                element.addEventListener('mousedown', () => {
                    element.removeAttribute('data-edsys-keyboard-focus');
                });

                element.addEventListener('blur', () => {
                    element.removeAttribute('data-edsys-keyboard-focus');
                });
            });

            // Add focus styles
            this.addAccessibilityCSS();

            // Skip to content link
            this.addSkipToContentLink();
        }

        /**
         * Add accessibility CSS
         * Добавление CSS для доступности
         */
        addAccessibilityCSS() {
            if (document.getElementById('edsys-accessibility-styles')) return;

            const style = document.createElement('style');
            style.id = 'edsys-accessibility-styles';
            style.textContent = `
                [data-edsys-keyboard-focus="true"]:focus {
                    outline: 2px solid var(--edsys-accent) !important;
                    outline-offset: 2px !important;
                    box-shadow: 0 0 0 4px rgba(255, 37, 69, 0.2) !important;
                }

                .edsys-skip-to-content {
                    position: absolute;
                    top: -40px;
                    left: 6px;
                    background: var(--edsys-accent);
                    color: var(--edsys-white);
                    padding: 8px 16px;
                    text-decoration: none;
                    border-radius: 4px;
                    z-index: 100000;
                    transition: top 0.3s ease;
                }

                .edsys-skip-to-content:focus {
                    top: 6px;
                }

                @media (prefers-reduced-motion: reduce) {
                    .edsys-skip-to-content {
                        transition: none;
                    }
                }

                /* High contrast mode support */
                @media (prefers-contrast: high) {
                    [data-edsys-keyboard-focus="true"]:focus {
                        outline: 3px solid ButtonText !important;
                        outline-offset: 2px !important;
                    }
                }
            `;
            document.head.appendChild(style);
        }
        
        /**
         * Setup lazy loading for images
         * Настройка ленивой загрузки изображений
         */
        setupLazyLoading() {
            // Native lazy loading support check
            if ('loading' in HTMLImageElement.prototype) {
                return;
            }

            // Polyfill for older browsers
            const lazyImages = document.querySelectorAll('img[loading="lazy"]');
            if (!lazyImages.length) return;

            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;

                        // Load image
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                        }

                        img.classList.remove('edsys-lazy');
                        img.classList.add('edsys-lazy-loaded');
                        imageObserver.unobserve(img);
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            lazyImages.forEach(img => {
                img.classList.add('edsys-lazy');
                imageObserver.observe(img);
            });

            // Add lazy loading styles
            this.addLazyLoadingCSS();
        }

        /**
         * Add lazy loading CSS
         * Добавление CSS для ленивой загрузки
         */
        addLazyLoadingCSS() {
            if (document.getElementById('edsys-lazy-loading-styles')) return;

            const style = document.createElement('style');
            style.id = 'edsys-lazy-loading-styles';
            style.textContent = `
                .edsys-lazy {
                    opacity: 0;
                    transition: opacity 0.3s ease;
                }

                .edsys-lazy-loaded {
                    opacity: 1;
                }

                @media (prefers-reduced-motion: reduce) {
                    .edsys-lazy {
                        opacity: 1;
                        transition: none;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        /**
         * Cache utilities
         * Утилиты кэширования
         */
        setCache(key, value, ttl = 300000) { // 5 minutes default
            const expiry = Date.now() + ttl;
            this.cache.set(key, { value, expiry });
        }

        getCache(key) {
            const cached = this.cache.get(key);
            if (!cached) return null;

            if (Date.now() > cached.expiry) {
                this.cache.delete(key);
                return null;
            }

            return cached.value;
        }

        clearCache() {
            this.cache.clear();
        }

        /**
         * Performance utilities
         * Утилиты производительности
         */
        debounce(func, wait, immediate = false) {
            let timeout;
            return function executedFunction(...args) {
                const later = () => {
                    timeout = null;
                    if (!immediate) func(...args);
                };
                const callNow = immediate && !timeout;
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
                if (callNow) func(...args);
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
         * Destroy and cleanup
         * Уничтожение и очистка
         */
        destroy() {
            try {
                // Clear cache
                this.clearCache();

                this.initialized = false;

            } catch (error) {
                console.error('Utils destroy failed:', error);
            }
        }
    }

    // Global API
    window.EDSArticlesUtils = {
        instance: null,

        init: function() {
            if (!this.instance) {
                this.instance = new EDSArticlesUtils();
            }
            return this.instance;
        },

        destroy: function() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },

        // Expose utility functions
        debounce: function(func, wait, immediate) {
            return this.instance ? this.instance.debounce(func, wait, immediate) : func;
        },

        throttle: function(func, limit) {
            return this.instance ? this.instance.throttle(func, limit) : func;
        },

        // Cache utilities
        setCache: function(key, value, ttl) {
            if (this.instance) this.instance.setCache(key, value, ttl);
        },

        getCache: function(key) {
            return this.instance ? this.instance.getCache(key) : null;
        },

        clearCache: function() {
            if (this.instance) this.instance.clearCache();
        }
    };

    // Auto-initialize on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.EDSArticlesUtils.init();
        });
    } else {
        window.EDSArticlesUtils.init();
    }

})(window, document);