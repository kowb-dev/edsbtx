/**
 * EDS Articles Navigation JavaScript v1.1 - CLEANED VERSION
 * Функциональность навигации для статей без лишних модулей
 *
 * @author EDS Development Team
 * @version 1.1
 * @since 2024
 */

(function(window, document) {
    'use strict';

    // Configuration
    const EDSYS_NAV_CONFIG = {
        mobileBreakpoint: 1024,
        animationDuration: 300,
        scrollOffset: 100,
        autoCloseDelay: 150,
        focusDelay: 300
    };

    // Navigation state
    let edsysNavState = {
        isMobile: window.innerWidth <= EDSYS_NAV_CONFIG.mobileBreakpoint,
        mobileMenuOpen: false,
        activeArticle: null,
        lastFocusedElement: null
    };

    /**
     * EDS Articles Navigation Class
     * Класс управления навигацией по статьям
     */
    class EDSArticlesNavigation {
        constructor() {
            this.elements = {};
            this.eventListeners = [];
            this.initialized = false;

            this.init();
        }

        /**
         * Initialize navigation functionality
         * Инициализация функциональности навигации
         */
        init() {
            try {
                this.setupElements();
                this.setupMobileNavigation();
                this.setupDesktopNavigation();
                this.setupResizeHandler();
                this.setupKeyboardNavigation();
                this.updateMobileState();

                this.initialized = true;

                // Trigger custom event
                this.triggerEvent('edsys:navigation:initialized', {
                    timestamp: Date.now(),
                    activeArticle: this.detectActiveArticle()
                });

            } catch (error) {
                console.error('Navigation initialization failed:', error);
            }
        }

        /**
         * Setup DOM elements
         * Настройка DOM элементов
         */
        setupElements() {
            this.elements = {
                // Desktop navigation
                desktopNav: document.querySelector('.edsys-articles-navigation'),
                desktopNavItems: document.querySelectorAll('.edsys-articles-navigation__item'),

                // Mobile navigation
                mobileBtn: document.querySelector('.edsys-mobile-nav-btn'),
                mobileMenu: document.querySelector('.edsys-mobile-nav-menu'),
                mobileClose: document.querySelector('.edsys-mobile-nav-close'),
                mobileOverlay: document.querySelector('.edsys-mobile-nav-overlay'),
                mobileNavItems: document.querySelectorAll('.edsys-mobile-nav-item'),

                // Common elements
                body: document.body,
                html: document.documentElement
            };
        }

        /**
         * Setup mobile navigation
         * Настройка мобильной навигации
         */
        setupMobileNavigation() {
            if (!this.elements.mobileBtn || !this.elements.mobileMenu) {
                return;
            }

            // Mobile button click
            const openHandler = () => this.openMobileMenu();
            this.elements.mobileBtn.addEventListener('click', openHandler);
            this.addEventListener(this.elements.mobileBtn, 'click', openHandler);

            // Close button
            if (this.elements.mobileClose) {
                const closeHandler = () => this.closeMobileMenu();
                this.elements.mobileClose.addEventListener('click', closeHandler);
                this.addEventListener(this.elements.mobileClose, 'click', closeHandler);
            }

            // Overlay click
            if (this.elements.mobileOverlay) {
                const overlayHandler = () => this.closeMobileMenu();
                this.elements.mobileOverlay.addEventListener('click', overlayHandler);
                this.addEventListener(this.elements.mobileOverlay, 'click', overlayHandler);
            }

            // Mobile navigation items
            this.elements.mobileNavItems.forEach(link => {
                const linkHandler = () => {
                    setTimeout(() => this.closeMobileMenu(), EDSYS_NAV_CONFIG.autoCloseDelay);
                };
                link.addEventListener('click', linkHandler);
                this.addEventListener(link, 'click', linkHandler);
            });

            // Touch events for better mobile experience
            if (this.isTouchDevice()) {
                this.setupTouchEvents();
            }
        }

        /**
         * Setup touch events
         * Настройка тач-событий
         */
        setupTouchEvents() {
            const touchStartHandler = (e) => {
                e.currentTarget.style.transform = 'translateY(-50%) scale(0.95)';
            };

            const touchEndHandler = (e) => {
                e.currentTarget.style.transform = 'translateY(-50%) scale(1)';
            };

            this.elements.mobileBtn.addEventListener('touchstart', touchStartHandler);
            this.elements.mobileBtn.addEventListener('touchend', touchEndHandler);

            this.addEventListener(this.elements.mobileBtn, 'touchstart', touchStartHandler);
            this.addEventListener(this.elements.mobileBtn, 'touchend', touchEndHandler);
        }

        /**
         * Setup desktop navigation
         * Настройка десктопной навигации
         */
        setupDesktopNavigation() {
            if (!this.elements.desktopNav) {
                return;
            }

            // Add hover effects and tracking
            this.elements.desktopNavItems.forEach(item => {
                const hoverHandler = () => {
                    this.trackEvent('navigation_hover', {
                        item_text: item.textContent.trim(),
                        item_url: item.getAttribute('href')
                    });
                };

                const clickHandler = () => {
                    this.trackEvent('navigation_click', {
                        item_text: item.textContent.trim(),
                        item_url: item.getAttribute('href'),
                        navigation_type: 'desktop'
                    });
                };

                item.addEventListener('mouseenter', hoverHandler);
                item.addEventListener('click', clickHandler);

                this.addEventListener(item, 'mouseenter', hoverHandler);
                this.addEventListener(item, 'click', clickHandler);
            });
        }

        /**
         * Open mobile menu
         * Открытие мобильного меню
         */
        openMobileMenu() {
            if (edsysNavState.mobileMenuOpen) return;

            try {
                edsysNavState.mobileMenuOpen = true;
                edsysNavState.lastFocusedElement = document.activeElement;

                // Show overlay
                if (this.elements.mobileOverlay) {
                    this.elements.mobileOverlay.classList.add('show');
                }

                // Open menu
                this.elements.mobileMenu.classList.add('open');

                // Prevent body scroll
                this.toggleBodyScroll(true);

                // Focus management
                this.setMobileFocus();

                // Track event
                this.trackEvent('mobile_menu_open', {
                    trigger: 'button_click',
                    viewport_width: window.innerWidth
                });

                // Trigger custom event
                this.triggerEvent('edsys:navigation:mobileOpen');

            } catch (error) {
                console.error('Failed to open mobile menu:', error);
            }
        }

        /**
         * Close mobile menu
         * Закрытие мобильного меню
         */
        closeMobileMenu() {
            if (!edsysNavState.mobileMenuOpen) return;

            try {
                edsysNavState.mobileMenuOpen = false;

                // Hide overlay
                if (this.elements.mobileOverlay) {
                    this.elements.mobileOverlay.classList.remove('show');
                }

                // Close menu
                this.elements.mobileMenu.classList.remove('open');

                // Restore body scroll
                this.toggleBodyScroll(false);

                // Restore focus
                this.restoreFocus();

                // Track event
                this.trackEvent('mobile_menu_close', {
                    method: 'manual',
                    viewport_width: window.innerWidth
                });

                // Trigger custom event
                this.triggerEvent('edsys:navigation:mobileClose');

            } catch (error) {
                console.error('Failed to close mobile menu:', error);
            }
        }

        /**
         * Set focus in mobile menu
         * Установка фокуса в мобильном меню
         */
        setMobileFocus() {
            const firstLink = this.elements.mobileMenu.querySelector('.edsys-mobile-nav-item');
            if (firstLink) {
                setTimeout(() => {
                    firstLink.focus();
                }, EDSYS_NAV_CONFIG.focusDelay);
            }
        }

        /**
         * Restore focus after closing mobile menu
         * Восстановление фокуса после закрытия мобильного меню
         */
        restoreFocus() {
            if (edsysNavState.lastFocusedElement && this.elements.mobileBtn) {
                this.elements.mobileBtn.focus();
            }
            edsysNavState.lastFocusedElement = null;
        }

        /**
         * Setup keyboard navigation
         * Настройка клавиатурной навигации
         */
        setupKeyboardNavigation() {
            const keydownHandler = (e) => this.handleKeydown(e);
            document.addEventListener('keydown', keydownHandler);
            this.addEventListener(document, 'keydown', keydownHandler);
        }

        /**
         * Handle keyboard events
         * Обработка клавиатурных событий
         */
        handleKeydown(e) {
            // ESC key closes mobile menu
            if (e.key === 'Escape' && edsysNavState.mobileMenuOpen) {
                e.preventDefault();
                this.closeMobileMenu();
                return;
            }

            // Tab trap in mobile menu
            if (edsysNavState.mobileMenuOpen && e.key === 'Tab') {
                this.handleTabTrap(e);
            }

            // Arrow keys navigation in mobile menu
            if (edsysNavState.mobileMenuOpen && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
                this.handleArrowNavigation(e);
            }
        }

        /**
         * Handle tab trapping in mobile menu
         * Обработка захвата Tab в мобильном меню
         */
        handleTabTrap(e) {
            const focusableElements = this.elements.mobileMenu.querySelectorAll(
                'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
            );

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            if (e.shiftKey) {
                // Shift + Tab
                if (document.activeElement === firstElement) {
                    e.preventDefault();
                    lastElement.focus();
                }
            } else {
                // Tab
                if (document.activeElement === lastElement) {
                    e.preventDefault();
                    firstElement.focus();
                }
            }
        }

        /**
         * Handle arrow key navigation
         * Обработка навигации стрелками
         */
        handleArrowNavigation(e) {
            e.preventDefault();

            const navItems = Array.from(this.elements.mobileNavItems);
            const currentIndex = navItems.indexOf(document.activeElement);

            let nextIndex;
            if (e.key === 'ArrowDown') {
                nextIndex = currentIndex < navItems.length - 1 ? currentIndex + 1 : 0;
            } else {
                nextIndex = currentIndex > 0 ? currentIndex - 1 : navItems.length - 1;
            }

            navItems[nextIndex].focus();
        }

        /**
         * Setup resize handler
         * Настройка обработчика изменения размера
         */
        setupResizeHandler() {
            const resizeHandler = this.debounce(() => {
                this.updateMobileState();
            }, 250);

            window.addEventListener('resize', resizeHandler);
            this.addEventListener(window, 'resize', resizeHandler);
        }

        /**
         * Update mobile state
         * Обновление мобильного состояния
         */
        updateMobileState() {
            const wasMobile = edsysNavState.isMobile;
            edsysNavState.isMobile = window.innerWidth <= EDSYS_NAV_CONFIG.mobileBreakpoint;

            // Close menu if switched to desktop
            if (wasMobile && !edsysNavState.isMobile && edsysNavState.mobileMenuOpen) {
                this.closeMobileMenu();
            }

            // Track state change
            if (wasMobile !== edsysNavState.isMobile) {
                this.trackEvent('navigation_breakpoint_change', {
                    new_mode: edsysNavState.isMobile ? 'mobile' : 'desktop',
                    width: window.innerWidth
                });
            }
        }

        /**
         * Detect active article
         * Определение активной статьи
         */
        detectActiveArticle() {
            const activeItem = document.querySelector('.edsys-articles-navigation__item.active, .edsys-mobile-nav-item.active');

            if (activeItem) {
                const articleName = activeItem.querySelector('.edsys-articles-navigation__name, .edsys-mobile-nav-name');
                edsysNavState.activeArticle = articleName ? articleName.textContent.trim() : null;
            }

            return edsysNavState.activeArticle;
        }

        /**
         * Toggle body scroll
         * Переключение прокрутки body
         */
        toggleBodyScroll(disable) {
            if (disable) {
                const scrollY = window.pageYOffset;
                this.elements.body.style.position = 'fixed';
                this.elements.body.style.top = `-${scrollY}px`;
                this.elements.body.style.left = '0';
                this.elements.body.style.right = '0';
                this.elements.body.style.overflow = 'hidden';
            } else {
                const scrollY = parseInt(this.elements.body.style.top || '0') * -1;
                this.elements.body.style.position = '';
                this.elements.body.style.top = '';
                this.elements.body.style.left = '';
                this.elements.body.style.right = '';
                this.elements.body.style.overflow = '';
                if (scrollY) {
                    window.scrollTo(0, scrollY);
                }
            }
        }

        /**
         * Check if device supports touch
         * Проверка поддержки тач-событий
         */
        isTouchDevice() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        }

        /**
         * Add event listener and track it
         * Добавление обработчика события с отслеживанием
         */
        addEventListener(element, event, handler) {
            this.eventListeners.push({ element, event, handler });
        }

        /**
         * Event tracking
         * Отслеживание событий
         */
        trackEvent(eventName, data = {}) {
            try {
                // Add common navigation data
                const eventData = {
                    ...data,
                    navigation_state: {
                        active_article: edsysNavState.activeArticle,
                        is_mobile: edsysNavState.isMobile,
                        menu_open: edsysNavState.mobileMenuOpen
                    },
                    timestamp: Date.now()
                };

                // Send to core tracking
                if (window.EDSArticlesCore && window.EDSArticlesCore.trackEvent) {
                    window.EDSArticlesCore.trackEvent(`navigation_${eventName}`, eventData);
                }

            } catch (error) {
                console.error('Navigation event tracking failed:', error);
            }
        }

        /**
         * Trigger custom event
         * Запуск пользовательского события
         */
        triggerEvent(eventName, data = {}) {
            try {
                const event = new CustomEvent(eventName, {
                    detail: { ...data, navigationState: edsysNavState },
                    bubbles: true,
                    cancelable: true
                });
                document.dispatchEvent(event);
            } catch (error) {
                console.error(`Failed to trigger navigation event: ${eventName}`, error);
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
         * Public API methods
         * Публичные методы API
         */
        openMenu() {
            this.openMobileMenu();
        }

        closeMenu() {
            this.closeMobileMenu();
        }

        toggleMenu() {
            if (edsysNavState.mobileMenuOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        }

        getCurrentMode() {
            return edsysNavState.isMobile ? 'mobile' : 'desktop';
        }

        getActiveArticle() {
            return edsysNavState.activeArticle;
        }

        getState() {
            return { ...edsysNavState };
        }

        /**
         * Destroy and cleanup
         * Уничтожение и очистка
         */
        destroy() {
            try {
                // Close mobile menu
                if (edsysNavState.mobileMenuOpen) {
                    this.closeMobileMenu();
                }

                // Remove event listeners
                this.eventListeners.forEach(({ element, event, handler }) => {
                    element.removeEventListener(event, handler);
                });

                // Reset state
                edsysNavState = {
                    isMobile: false,
                    mobileMenuOpen: false,
                    activeArticle: null,
                    lastFocusedElement: null
                };

                this.initialized = false;

            } catch (error) {
                console.error('Navigation destroy failed:', error);
            }
        }
    }

    // Global API
    window.EDSArticlesNavigation = {
        instance: null,

        init: function() {
            if (!this.instance) {
                this.instance = new EDSArticlesNavigation();
            }
            return this.instance;
        },

        destroy: function() {
            if (this.instance) {
                this.instance.destroy();
                this.instance = null;
            }
        },

        openMenu: function() {
            if (this.instance && this.instance.initialized) {
                this.instance.openMenu();
            }
        },

        closeMenu: function() {
            if (this.instance && this.instance.initialized) {
                this.instance.closeMenu();
            }
        },

        toggleMenu: function() {
            if (this.instance && this.instance.initialized) {
                this.instance.toggleMenu();
            }
        },

        getCurrentMode: function() {
            return this.instance ? this.instance.getCurrentMode() : 'unknown';
        },

        getActiveArticle: function() {
            return this.instance ? this.instance.getActiveArticle() : null;
        },

        getState: function() {
            return this.instance ? this.instance.getState() : {};
        }
    };

    // Auto-initialize on DOMContentLoaded
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            window.EDSArticlesNavigation.init();
        });
    } else {
        window.EDSArticlesNavigation.init();
    }

    // Listen for close menu event from core
    document.addEventListener('edsys:closeMobileNav', () => {
        if (window.EDSArticlesNavigation.instance) {
            window.EDSArticlesNavigation.closeMenu();
        }
    });

})(window, document);