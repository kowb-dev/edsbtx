/**
 * Useful Information Navigation JavaScript - Updated with new breakpoint
 * Функциональность навигации по полезной информации - обновленная с новым breakpoint
 */

(function(window, document) {
    'use strict';

    // Configuration - Updated breakpoint
    const CONFIG = {
        mobileBreakpoint: 1024, // Changed from 768 to 1024
        animationDuration: 300,
        scrollOffset: 100
    };

    // State
    let state = {
        isMobile: window.innerWidth <= CONFIG.mobileBreakpoint,
        sideMenuOpen: false,
        activeQuickNavItem: null
    };

    // Navigation class
    class UsefulInfoNavigation {
        constructor() {
            this.elements = {};
            this.init();
        }

        init() {
            try {
                this.setupElements();
                this.setupEventListeners();
                this.setupQuickNavigation();
                this.updateMobileState();
                console.log('Useful Info Navigation initialized with 1024px breakpoint');
            } catch (error) {
                console.error('Error initializing Useful Info Navigation:', error);
            }
        }

        setupElements() {
            // Desktop elements
            this.elements.desktopNav = document.getElementById('usefulInfoNav');
            this.elements.quickNavItems = document.querySelectorAll('.edsys-useful-nav__quick-item');
            this.elements.mainNavItems = document.querySelectorAll('.edsys-useful-nav__item');

            // Mobile elements
            this.elements.mobileBtn = document.getElementById('usefulNavMobileBtn');
            this.elements.mobileMenu = document.getElementById('usefulNavMobileMenu');
            this.elements.mobileClose = document.getElementById('usefulNavMobileClose');
            this.elements.mobileOverlay = document.getElementById('usefulNavMobileOverlay');
            this.elements.mobileQuickItems = document.querySelectorAll('.edsys-useful-nav__mobile-quick-item');
            this.elements.mobileMainItems = document.querySelectorAll('.edsys-useful-nav__mobile-main-item');
        }

        setupEventListeners() {
            // Mobile button events
            if (this.elements.mobileBtn) {
                this.elements.mobileBtn.addEventListener('click', this.toggleMobileMenu.bind(this));

                // Touch events for better mobile experience
                if (this.isTouchDevice()) {
                    this.elements.mobileBtn.addEventListener('touchstart', this.handleTouchStart.bind(this));
                    this.elements.mobileBtn.addEventListener('touchend', this.handleTouchEnd.bind(this));
                }
            }

            // Mobile close button
            if (this.elements.mobileClose) {
                this.elements.mobileClose.addEventListener('click', this.closeMobileMenu.bind(this));
            }

            // Mobile overlay
            if (this.elements.mobileOverlay) {
                this.elements.mobileOverlay.addEventListener('click', this.closeMobileMenu.bind(this));
            }

            // Keyboard events
            document.addEventListener('keydown', this.handleKeydown.bind(this));

            // Window resize - updated to use new breakpoint
            window.addEventListener('resize', this.debounce(() => {
                this.updateMobileState();
            }, 250));

            // Quick navigation smooth scrolling
            this.setupSmoothScrolling();
        }

        setupQuickNavigation() {
            // Desktop quick nav
            this.elements.quickNavItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    this.handleQuickNavClick(e, item);
                });
            });

            // Mobile quick nav
            this.elements.mobileQuickItems.forEach(item => {
                item.addEventListener('click', (e) => {
                    this.handleQuickNavClick(e, item);
                    this.closeMobileMenu();
                });
            });

            // Observe sections for active state
            this.observeSections();
        }

        setupSmoothScrolling() {
            const links = document.querySelectorAll('a[href^="#"]');

            links.forEach(link => {
                link.addEventListener('click', (e) => {
                    const targetId = link.getAttribute('href').substring(1);
                    if (targetId && document.getElementById(targetId)) {
                        e.preventDefault();
                        this.scrollToSection(targetId);
                    }
                });
            });
        }

        handleQuickNavClick(e, item) {
            const targetId = item.getAttribute('href').substring(1);
            if (targetId && document.getElementById(targetId)) {
                e.preventDefault();
                this.scrollToSection(targetId);
                this.updateActiveQuickNav(targetId);
            }
        }

        scrollToSection(targetId) {
            const targetElement = document.getElementById(targetId);
            if (targetElement) {
                const elementPosition = targetElement.getBoundingClientRect().top;
                const offsetPosition = elementPosition + window.pageYOffset - CONFIG.scrollOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                // Update URL
                history.pushState(null, null, '#' + targetId);
            }
        }

        updateActiveQuickNav(activeId) {
            // Update desktop quick nav
            this.elements.quickNavItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href === '#' + activeId) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            // Update mobile quick nav
            this.elements.mobileQuickItems.forEach(item => {
                const href = item.getAttribute('href');
                if (href === '#' + activeId) {
                    item.classList.add('active');
                } else {
                    item.classList.remove('active');
                }
            });

            state.activeQuickNavItem = activeId;
        }

        observeSections() {
            // Fixed: Use exact ID matches instead of prefix matches
            const sections = document.querySelectorAll('#bal, #nbal, #insrt, #dmx, #xtrem, #kgtp, #pugv');

            if (sections.length === 0) return;

            const observerOptions = {
                root: null,
                rootMargin: '-20% 0px -60% 0px',
                threshold: 0
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.id;
                        this.updateActiveQuickNav(sectionId);
                    }
                });
            }, observerOptions);

            sections.forEach(section => {
                observer.observe(section);
            });
        }

        toggleMobileMenu() {
            if (state.sideMenuOpen) {
                this.closeMobileMenu();
            } else {
                this.openMobileMenu();
            }
        }

        openMobileMenu() {
            if (!this.elements.mobileMenu || !this.elements.mobileOverlay) return;

            state.sideMenuOpen = true;

            // Show overlay
            this.elements.mobileOverlay.classList.add('show');

            // Open menu
            this.elements.mobileMenu.classList.add('open');

            // Prevent body scroll
            this.toggleBodyScroll(true);

            // Focus management
            const firstLink = this.elements.mobileMenu.querySelector('.edsys-useful-nav__mobile-quick-item, .edsys-useful-nav__mobile-main-item');
            if (firstLink) {
                setTimeout(() => firstLink.focus(), 300);
            }

            // Analytics
            this.trackEvent('mobile_menu_open');
        }

        closeMobileMenu() {
            if (!this.elements.mobileMenu || !this.elements.mobileOverlay) return;

            state.sideMenuOpen = false;

            // Hide overlay
            this.elements.mobileOverlay.classList.remove('show');

            // Close menu
            this.elements.mobileMenu.classList.remove('open');

            // Restore body scroll
            this.toggleBodyScroll(false);

            // Return focus to button
            if (this.elements.mobileBtn) {
                this.elements.mobileBtn.focus();
            }

            // Analytics
            this.trackEvent('mobile_menu_close');
        }

        handleTouchStart(e) {
            e.currentTarget.style.transform = 'translateY(-50%) scale(0.95)';
        }

        handleTouchEnd(e) {
            e.currentTarget.style.transform = 'translateY(-50%) scale(1)';
        }

        handleKeydown(e) {
            // ESC key closes mobile menu
            if (e.key === 'Escape' && state.sideMenuOpen) {
                this.closeMobileMenu();
            }

            // Arrow keys for quick navigation
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    this.navigateQuickSections(e.key === 'ArrowUp' ? 'prev' : 'next');
                }
            }
        }

        navigateQuickSections(direction) {
            // Updated: Use exact ID matches for both schemes and cable sections
            const sections = Array.from(document.querySelectorAll('#bal, #nbal, #insrt, #dmx, #xtrem, #kgtp, #pugv'));
            if (sections.length === 0) return;

            const currentIndex = sections.findIndex(section => section.id === state.activeQuickNavItem);
            let targetIndex;

            if (direction === 'next') {
                targetIndex = Math.min(currentIndex + 1, sections.length - 1);
            } else {
                targetIndex = Math.max(currentIndex - 1, 0);
            }

            if (sections[targetIndex]) {
                this.scrollToSection(sections[targetIndex].id);
            }
        }

        updateMobileState() {
            const wasMobile = state.isMobile;
            state.isMobile = window.innerWidth <= CONFIG.mobileBreakpoint;

            // Close menu if switched to desktop
            if (wasMobile && !state.isMobile && state.sideMenuOpen) {
                this.closeMobileMenu();
            }

            // Log state change for debugging
            if (wasMobile !== state.isMobile) {
                console.log('Navigation mode changed:', state.isMobile ? 'Mobile' : 'Desktop', `(${window.innerWidth}px)`);
            }
        }

        toggleBodyScroll(disable) {
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

        isTouchDevice() {
            return 'ontouchstart' in window || navigator.maxTouchPoints > 0;
        }

        trackEvent(eventName) {
            // Integration with analytics
            if (typeof gtag !== 'undefined') {
                gtag('event', eventName, {
                    event_category: 'useful_info_navigation',
                    event_label: window.location.pathname,
                    breakpoint: state.isMobile ? 'mobile' : 'desktop'
                });
            }

            if (typeof ym !== 'undefined' && window.ymCounterId) {
                ym(window.ymCounterId, 'reachGoal', eventName);
            }

            console.log('Navigation event:', eventName, 'Mode:', state.isMobile ? 'Mobile' : 'Desktop');
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

        // Public API methods
        goToSection(sectionId) {
            this.scrollToSection(sectionId);
        }

        openMenu() {
            this.openMobileMenu();
        }

        closeMenu() {
            this.closeMobileMenu();
        }

        getCurrentBreakpoint() {
            return state.isMobile ? 'mobile' : 'desktop';
        }

        destroy() {
            // Clean up event listeners
            if (this.elements.mobileBtn) {
                this.elements.mobileBtn.removeEventListener('click', this.toggleMobileMenu);
            }
            if (this.elements.mobileClose) {
                this.elements.mobileClose.removeEventListener('click', this.closeMobileMenu);
            }
            if (this.elements.mobileOverlay) {
                this.elements.mobileOverlay.removeEventListener('click', this.closeMobileMenu);
            }

            document.removeEventListener('keydown', this.handleKeydown);

            // Restore body scroll
            this.toggleBodyScroll(false);
        }
    }

    // Initialize and expose global API
    window.EDSUsefulNavigation = {
        instance: null,

        init: function() {
            if (!this.instance) {
                this.instance = new UsefulInfoNavigation();
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
            if (this.instance) {
                this.instance.goToSection(sectionId);
            }
        },

        openMenu: function() {
            if (this.instance) {
                this.instance.openMenu();
            }
        },

        closeMenu: function() {
            if (this.instance) {
                this.instance.closeMenu();
            }
        },

        getCurrentBreakpoint: function() {
            return this.instance ? this.instance.getCurrentBreakpoint() : 'unknown';
        }
    };

    // Auto-initialize on DOMContentLoaded if not already initialized
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            if (!window.EDSUsefulNavigation.instance) {
                window.EDSUsefulNavigation.init();
            }
        });
    } else {
        if (!window.EDSUsefulNavigation.instance) {
            window.EDSUsefulNavigation.init();
        }
    }

    // Handle page load from history (back/forward buttons)
    window.addEventListener('popstate', () => {
        const hash = window.location.hash.substring(1);
        if (hash && window.EDSUsefulNavigation.instance) {
            const element = document.getElementById(hash);
            if (element) {
                window.EDSUsefulNavigation.instance.scrollToSection(hash);
            }
        }
    });

})(window, document);