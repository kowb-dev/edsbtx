/**
 * EDS Calculators Core JavaScript v1.0
 * Основная функциональность для калькуляторов
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 */

(function(window, document) {
    'use strict';

    // Configuration
    const EDSYS_CALC_CONFIG = {
        mobileBreakpoint: 1024,
        animationDuration: 300,
        debounceDelay: 300,
        throttleLimit: 100
    };

    // State management
    let edsysCalcState = {
        isMobile: window.innerWidth <= EDSYS_CALC_CONFIG.mobileBreakpoint,
        mobileNavOpen: false,
        activeCalculator: null,
        lastFocusedElement: null,
        initialized: false
    };

    /**
     * EDS Calculators Core Class
     * Основной класс для управления функциональностью калькуляторов
     */
    class EDSCalculatorsCore {
        constructor() {
            this.elements = {};
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
                this.setupMobileNavigation();
                this.setupResizeHandler();
                this.setupKeyboardNavigation();
                this.updateMobileState();

                this.initialized = true;
                edsysCalcState.initialized = true;

                // Trigger custom event
                this.triggerEvent('edsys:calculators:initialized', {
                    timestamp: Date.now(),
                    config: EDSYS_CALC_CONFIG
                });

            } catch (error) {
                console.error('Calculators core initialization failed:', error);
            }
        }

        /**
         * Setup DOM elements
         * Настройка DOM элементов
         */
        setupElements() {
            this.elements = {
                // Desktop navigation
                desktopNav: document.querySelector('.edsys-calculators-navigation'),
                desktopNavItems: document.querySelectorAll('.edsys-calculators-navigation__item'),

                // Mobile navigation
                mobileBtn: document.querySelector('.edsys-calculators-mobile-nav-btn'),
                mobileMenu: document.querySelector('.edsys-calculators-mobile-nav-menu'),
                mobileClose: document.querySelector('.edsys-calculators-mobile-nav-close'),
                mobileOverlay: document.querySelector('.edsys-calculators-mobile-nav-overlay'),
                mobileNavItems: document.querySelectorAll('.edsys-calculators-mobile-nav-item'),

                // Calculator form elements
                calculatorForm: document.querySelector('.edsys-calculator-form'),
                calculatorInputs: document.querySelectorAll('.edsys-calculator-form__input'),
                calculatorButtons: document.querySelectorAll('.edsys-calculator-form__button'),

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
                    setTimeout(() => this.closeMobileMenu(), 150);
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
         * Open mobile menu
         * Открытие мобильного меню
         */
        openMobileMenu() {
            if (edsysCalcState.mobileNavOpen) return;

            try {
                edsysCalcState.mobileNavOpen = true;
                edsysCalcState.lastFocusedElement = document.activeElement;

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
                this.triggerEvent('edsys:calculators:mobileOpen');

            } catch (error) {
                console.error('Failed to open mobile menu:', error);
            }
        }

        /**
         * Close mobile menu
         * Закрытие мобильного меню
         */
        closeMobileMenu() {
            if (!edsysCalcState.mobileNavOpen) return;

            try {
                edsysCalcState.mobileNavOpen = false;

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
                this.triggerEvent('edsys:calculators:mobileClose');

            } catch (error) {
                console.error('Failed to close mobile menu:', error);
            }
        }

        /**
         * Set focus in mobile menu
         * Установка фокуса в мобильном меню
         */
        setMobileFocus() {
            const firstLink = this.elements.mobileMenu.querySelector('.edsys-calculators-mobile-nav-item');
            if (firstLink) {
                setTimeout(() => {
                    firstLink.focus();
                }, 300);
            }
        }

        /**
         * Restore focus after closing mobile menu
         * Восстановление фокуса после закрытия мобильного меню
         */
        restoreFocus() {
            if (edsysCalcState.lastFocusedElement && this.elements.mobileBtn) {
                this.elements.mobileBtn.focus();
            }
            edsysCalcState.lastFocusedElement = null;
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
            if (e.key === 'Escape' && edsysCalcState.mobileNavOpen) {
                e.preventDefault();
                this.closeMobileMenu();
                return;
            }

            // Tab trap in mobile menu
            if (edsysCalcState.mobileNavOpen && e.key === 'Tab') {
                this.handleTabTrap(e);
            }

            // Arrow keys navigation in mobile menu
            if (edsysCalcState.mobileNavOpen && (e.key === 'ArrowUp' || e.key === 'ArrowDown')) {
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
            const wasMobile = edsysCalcState.isMobile;
            edsysCalcState.isMobile = window.innerWidth <= EDSYS_CALC_CONFIG.mobileBreakpoint;

            // Close menu if switched to desktop
            if (wasMobile && !edsysCalcState.isMobile && edsysCalcState.mobileNavOpen) {
                this.closeMobileMenu();
            }

            // Track state change
            if (wasMobile !== edsysCalcState.isMobile) {
                this.trackEvent('breakpoint_change', {
                    new_mode: edsysCalcState.isMobile ? 'mobile' : 'desktop',
                    width: window.innerWidth
                });
            }
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
         * Calculator utilities
         * Утилиты для калькуляторов
         */
        validateNumber(value, min = null, max = null) {
            const num = parseFloat(value);
            if (isNaN(num) || !isFinite(num)) {
                return { valid: false, error: 'Введите корректное число' };
            }
            if (min !== null && num < min) {
                return { valid: false, error: `Значение должно быть больше ${min}` };
            }
            if (max !== null && num > max) {
                return { valid: false, error: `Значение должно быть меньше ${max}` };
            }
            return { valid: true, value: num };
        }

        /**
         * Format number for display
         * Форматирование числа для отображения
         */
        formatNumber(num, decimals = 2) {
            if (typeof num !== 'number' || isNaN(num)) {
                return '0';
            }

            return num.toFixed(decimals).replace(/\.?0+$/, '');
        }

        /**
         * Calculate electrical values
         * Расчет электрических величин
         */
        calculateCurrent(power, voltage, phases = 1) {
            if (phases === 1) {
                // Single phase: I = P / U
                return power / voltage;
            } else {
                // Three phase: I = P / (√3 × U × cos φ)
                // Assuming cos φ = 1 for simplicity
                return power / (Math.sqrt(3) * voltage);
            }
        }

        calculatePower(current, voltage, phases = 1) {
            if (phases === 1) {
                // Single phase: P = I × U
                return current * voltage;
            } else {
                // Three phase: P = √3 × I × U × cos φ
                // Assuming cos φ = 1 for simplicity
                return Math.sqrt(3) * current * voltage;
            }
        }

        calculateVoltage(power, current, phases = 1) {
            if (phases === 1) {
                // Single phase: U = P / I
                return power / current;
            } else {
                // Three phase: U = P / (√3 × I × cos φ)
                // Assuming cos φ = 1 for simplicity
                return power / (Math.sqrt(3) * current);
            }
        }

        /**
         * Event tracking
         * Отслеживание событий
         */
        trackEvent(eventName, data = {}) {
            try {
                const eventData = {
                    ...data,
                    calculator_state: {
                        active_calculator: edsysCalcState.activeCalculator,
                        is_mobile: edsysCalcState.isMobile,
                        menu_open: edsysCalcState.mobileNavOpen
                    },
                    timestamp: Date.now()
                };

                // Trigger custom event
                this.triggerEvent('edsys:calculators:analytics', { eventName, data: eventData });

                // Send to analytics if available
                if (typeof gtag !== 'undefined') {
                    gtag('event', eventName, {
                        event_category: 'calculators',
                        event_label: window.location.pathname,
                        custom_parameters: eventData
                    });
                }

            } catch (error) {
                console.error('Calculator event tracking failed:', error);
            }
        }

        /**
         * Trigger custom event
         * Запуск пользовательского события
         */
        triggerEvent(eventName, data = {}) {
            try {
                const event = new CustomEvent(eventName, {
                    detail: { ...data, calculatorState: edsysCalcState },
                    bubbles: true,
                    cancelable: true
                });
                document.dispatchEvent(event);
            } catch (error) {
                console.error(`Failed to trigger calculator event: ${eventName}`, error);
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
        openMenu() {
            this.openMobileMenu();
        }

        closeMenu() {
            this.closeMobileMenu();
        }

        toggleMenu() {
            if (edsysCalcState.mobileNavOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        }

        getCurrentMode() {
            return edsysCalcState.isMobile ? 'mobile' : 'desktop';
        }

        getActiveCalculator() {
            return edsysCalcState.activeCalculator;
        }

        setActiveCalculator(calculatorId) {
            edsysCalcState.activeCalculator = calculatorId;
        }

        getState() {
            return { ...edsysCalcState };
        }

        /**
         * Destroy and cleanup
         * Уничтожение и очистка
         */
        destroy() {
            try {
                // Close mobile menu
                if (edsysCalcState.mobileNavOpen) {
                    this.closeMobileMenu();
                }

                // Remove event listeners
                this.eventListeners.forEach(({ element, event, handler }) => {
                    element.removeEventListener(event, handler);
                });

                // Reset state
                edsysCalcState = {
                    isMobile: false,
                    mobileNavOpen: false,
                    activeCalculator: null,
                    lastFocusedElement: null,
                    initialized: false
                };

                this.initialized = false;

            } catch (error) {
                console.error('Calculator destroy failed:', error);
            }
        }
    }

    // Global API
    window.EDSCalculatorsCore = {
        instance: null,

        init: function() {
            if (!this.instance) {
                this.instance = new EDSCalculatorsCore();
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

        getActiveCalculator: function() {
            return this.instance ? this.instance.getActiveCalculator() : null;
        },

        setActiveCalculator: function(calculatorId) {
            if (this.instance) {
                this.instance.setActiveCalculator(calculatorId);
            }
        },

        getState: function() {
            return this.instance ? this.instance.getState() : {};
        },

        // Utility functions
        validateNumber: function(value, min, max) {
            return this.instance ? this.instance.validateNumber(value, min, max) : { valid: false, error: 'Not initialized' };
        },

        formatNumber: function(num, decimals) {
            return this.instance ? this.instance.formatNumber(num, decimals) : '0';
        },

        calculateCurrent: function(power, voltage, phases) {
            return this.instance ? this.instance.calculateCurrent(power, voltage, phases) : 0;
        },

        calculatePower: function(current, voltage, phases) {
            return this.instance ? this.instance.calculatePower(current, voltage, phases) : 0;
        },

        calculateVoltage: function(power, current, phases) {
            return this.instance ? this.instance.calculateVoltage(power, current, phases) : 0;
        },

        trackEvent: function(eventName, data) {
            if (this.instance && this.instance.initialized) {
                this.instance.trackEvent(eventName, data);
            }
        },

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
            window.EDSCalculatorsCore.init();
        });
    } else {
        window.EDSCalculatorsCore.init();
    }

})(window, document);