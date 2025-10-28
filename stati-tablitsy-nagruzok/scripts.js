/**
 * Cable Tables JavaScript - Updated for 1024px breakpoint integration
 * Интерактивность для страницы таблиц кабелей с обновленным breakpoint
 */

(function() {
    'use strict';

    // Configuration - Updated breakpoint to match useful-info-navigation
    const CONFIG = {
        SCROLL_OFFSET: 80,
        ANIMATION_DURATION: 300,
        INTERSECTION_THRESHOLD: 0.3,
        mobileBreakpoint: 1024 // Updated from 768 to 1024
    };

    // DOM Elements
    let elements = {};

    // State
    let state = {
        isMobile: window.innerWidth <= CONFIG.mobileBreakpoint,
        activeSection: null
    };

    /**
     * Initialize the page
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initializeElements);
        } else {
            initializeElements();
        }
    }

    /**
     * Initialize DOM elements and setup
     */
    function initializeElements() {
        elements = {
            cableCards: document.querySelectorAll('.edsys-cable-card'),
            cableSections: document.querySelectorAll('.edsys-cable-section'),
            tableWrappers: document.querySelectorAll('.edsys-cable-table-wrapper'),
            // Integration with useful info navigation
            usefulNavQuickItems: document.querySelectorAll('.edsys-useful-nav__quick-item'),
            usefulNavMobileQuickItems: document.querySelectorAll('.edsys-useful-nav__mobile-quick-item')
        };

        setupCableCardNavigation();
        setupSmoothScrolling();
        setupIntersectionObserver();
        setupKeyboardNavigation();
        setupTableEnhancements();
        setupIntegrationWithUsefulNav();
        setupResponsiveHandling();

        console.log('Cable Tables page with 1024px breakpoint initialized');
    }

    /**
     * Setup cable card navigation
     */
    function setupCableCardNavigation() {
        elements.cableCards.forEach(card => {
            card.addEventListener('click', handleCardClick);
            card.addEventListener('keydown', handleCardKeydown);
            card.setAttribute('tabindex', '0');
            card.setAttribute('role', 'button');
        });
    }

    /**
     * Handle cable card click
     */
    function handleCardClick(event) {
        const cable = event.currentTarget.dataset.cable;
        if (cable) {
            scrollToSection(cable);
        }
    }

    /**
     * Handle cable card keyboard navigation
     */
    function handleCardKeydown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            handleCardClick(event);
        }
    }

    /**
     * Setup smooth scrolling
     */
    function setupSmoothScrolling() {
        document.addEventListener('click', event => {
            const link = event.target.closest('a[href^="#"]');
            if (link) {
                event.preventDefault();
                const targetId = link.getAttribute('href').substring(1);
                scrollToSection(targetId);
            }
        });
    }

    /**
     * Scroll to specific section
     */
    function scrollToSection(sectionId) {
        const targetSection = document.getElementById(sectionId);
        if (!targetSection) return;

        const offsetTop = targetSection.offsetTop - CONFIG.SCROLL_OFFSET;

        if ('scrollBehavior' in document.documentElement.style) {
            window.scrollTo({
                top: offsetTop,
                behavior: 'smooth'
            });
        } else {
            animatedScrollTo(offsetTop);
        }

        history.replaceState(null, null, `#${sectionId}`);
        updateActiveNavigation(sectionId);
    }

    /**
     * Animated scroll fallback
     */
    function animatedScrollTo(targetY) {
        const startY = window.pageYOffset;
        const difference = targetY - startY;
        const startTime = performance.now();

        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / CONFIG.ANIMATION_DURATION, 1);
            const easeOut = 1 - Math.pow(1 - progress, 3);

            window.scrollTo(0, startY + difference * easeOut);

            if (progress < 1) {
                requestAnimationFrame(step);
            }
        }

        requestAnimationFrame(step);
    }

    /**
     * Setup intersection observer
     */
    function setupIntersectionObserver() {
        const observerOptions = {
            root: null,
            rootMargin: `-${CONFIG.SCROLL_OFFSET}px 0px -50% 0px`,
            threshold: CONFIG.INTERSECTION_THRESHOLD
        };

        const sectionObserver = new IntersectionObserver(handleSectionIntersection, observerOptions);

        elements.cableSections.forEach(section => {
            sectionObserver.observe(section);
        });
    }

    /**
     * Handle section intersection
     */
    function handleSectionIntersection(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const sectionId = entry.target.id;
                state.activeSection = sectionId;
                updateActiveNavigation(sectionId);
            }
        });
    }

    /**
     * Update active navigation states for both local and useful info navigation
     */
    function updateActiveNavigation(activeSectionId) {
        // Update useful info navigation quick items (desktop)
        elements.usefulNavQuickItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === '#' + activeSectionId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Update useful info navigation quick items (mobile)
        elements.usefulNavMobileQuickItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href === '#' + activeSectionId) {
                item.classList.add('active');
            } else {
                item.classList.remove('active');
            }
        });

        // Integration with useful info navigation
        if (window.EDSUsefulNavigation && window.EDSUsefulNavigation.instance) {
            const instance = window.EDSUsefulNavigation.instance;
            if (instance.updateActiveQuickNav) {
                instance.updateActiveQuickNav(activeSectionId);
            }
        }
    }

    /**
     * Setup integration with useful info navigation
     */
    function setupIntegrationWithUsefulNav() {
        // Wait for useful info navigation to be ready
        const checkUsefulNav = () => {
            if (window.EDSUsefulNavigation && window.EDSUsefulNavigation.instance) {
                console.log('Integrated with Useful Info Navigation (1024px breakpoint)');
                coordinateNavigations();
            } else {
                setTimeout(checkUsefulNav, 100);
            }
        };

        checkUsefulNav();
    }

    /**
     * Coordinate cable tables navigation with useful info navigation
     */
    function coordinateNavigations() {
        const usefulNavInstance = window.EDSUsefulNavigation.instance;

        if (usefulNavInstance && usefulNavInstance.scrollToSection) {
            const originalScrollToSection = usefulNavInstance.scrollToSection;

            usefulNavInstance.scrollToSection = function(targetId) {
                // Check if it's a cable section
                const cableSection = document.getElementById(targetId);
                if (cableSection && cableSection.classList.contains('edsys-cable-section')) {
                    // Use our enhanced scroll function
                    scrollToSection(targetId);
                } else {
                    // Use original function for other sections
                    originalScrollToSection.call(this, targetId);
                }
            };
        }
    }

    /**
     * Setup responsive handling
     */
    function setupResponsiveHandling() {
        window.addEventListener('resize', debounce(() => {
            const wasMobile = state.isMobile;
            state.isMobile = window.innerWidth <= CONFIG.mobileBreakpoint;

            if (wasMobile !== state.isMobile) {
                console.log('Cable Tables responsive mode changed:', state.isMobile ? 'Mobile' : 'Desktop', `(${window.innerWidth}px)`);

                // Refresh table scroll indicators
                elements.tableWrappers.forEach(wrapper => {
                    wrapper.dispatchEvent(new Event('scroll'));
                });
            }
        }, 250));
    }

    /**
     * Setup keyboard navigation
     */
    function setupKeyboardNavigation() {
        document.addEventListener('keydown', event => {
            // Quick navigation with number keys for cable sections
            if (event.key >= '1' && event.key <= '3' && !event.ctrlKey && !event.metaKey) {
                const sectionIds = ['xtrem', 'kgtp', 'pugv'];
                const targetId = sectionIds[parseInt(event.key) - 1];
                if (targetId) {
                    scrollToSection(targetId);
                }
            }
        });
    }

    /**
     * Setup table enhancements
     */
    function setupTableEnhancements() {
        elements.tableWrappers.forEach(wrapper => {
            const table = wrapper.querySelector('.edsys-cable-table');
            if (!table) return;

            // Add row highlighting and keyboard navigation
            const rows = table.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.setAttribute('tabindex', '0');
                row.addEventListener('keydown', event => {
                    if (event.key === 'ArrowDown' && rows[index + 1]) {
                        rows[index + 1].focus();
                    } else if (event.key === 'ArrowUp' && rows[index - 1]) {
                        rows[index - 1].focus();
                    }
                });
            });

            // Handle horizontal scroll indicators
            if (wrapper.scrollWidth > wrapper.clientWidth) {
                wrapper.classList.add('has-scroll');

                wrapper.addEventListener('scroll', () => {
                    const isAtStart = wrapper.scrollLeft <= 0;
                    const isAtEnd = wrapper.scrollLeft >= wrapper.scrollWidth - wrapper.clientWidth;

                    wrapper.classList.toggle('scroll-at-start', isAtStart);
                    wrapper.classList.toggle('scroll-at-end', isAtEnd);
                });

                wrapper.dispatchEvent(new Event('scroll'));
            }
        });
    }

    /**
     * Utility function to debounce function calls
     */
    function debounce(func, wait) {
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
     * Get current mobile state
     */
    function isMobile() {
        return window.innerWidth <= CONFIG.mobileBreakpoint;
    }

    /**
     * Get current breakpoint name
     */
    function getCurrentBreakpoint() {
        return state.isMobile ? 'mobile' : 'desktop';
    }

    // Initialize the page
    init();

    // Expose utility functions with integration support
    window.EdsysCableTables = {
        scrollToSection,
        updateActiveNavigation,

        // Integration methods
        coordinateWith: function(externalNavigation) {
            console.log('Cable Tables coordinating with external navigation');
            return {
                scrollToSection: scrollToSection,
                updateActiveNavigation: updateActiveNavigation,
                getCurrentBreakpoint: getCurrentBreakpoint
            };
        },

        // Get current state
        getCurrentSection: function() {
            return state.activeSection;
        },

        getCurrentBreakpoint: getCurrentBreakpoint,

        isMobile: function() {
            return state.isMobile;
        },

        // Configuration access
        getConfig: function() {
            return { ...CONFIG };
        },

        // Re-initialize (useful for dynamic content)
        reinitialize: function() {
            console.log('Reinitializing Cable Tables');
            initializeElements();
        }
    };

    // Auto-coordination with useful info navigation when it becomes available
    document.addEventListener('usefulNavReady', function() {
        console.log('Useful Info Navigation ready, setting up coordination with 1024px breakpoint');
        setupIntegrationWithUsefulNav();
    });

})();