/**
 * Payment and Delivery Page Scripts
 * Version: 1.0.0
 * Date: 2025-07-16
 * Description: Интерактивность для страницы оплаты и доставки EDS
 */

(function() {
    'use strict';

    // DOM Elements
    let processSteps = null;
    let infoCards = null;
    let ctaButtons = null;
    let contactLinks = null;

    /**
     * Initialize the payment and delivery page functionality
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupEventListeners);
        } else {
            setupEventListeners();
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Get DOM elements
        processSteps = document.querySelectorAll('.edsys-process-step');
        infoCards = document.querySelectorAll('.edsys-info-card');
        ctaButtons = document.querySelectorAll('.edsys-cta-button');
        contactLinks = document.querySelectorAll('.edsys-contact-link, .edsys-pickup-address');

        // Setup scroll animations
        setupScrollAnimations();

        // Setup contact link enhancements
        setupContactLinks();

        // Setup click tracking
        setupClickTracking();

        // Setup smooth scrolling
        setupSmoothScrolling();

        // Setup keyboard navigation
        setupKeyboardNavigation();

        console.info('EDS Payment & Delivery: Page initialized successfully');
    }

    /**
     * Setup scroll animations using Intersection Observer
     */
    function setupScrollAnimations() {
        // Check if browser supports Intersection Observer
        if (!window.IntersectionObserver) {
            // Fallback: show all elements immediately
            showAllElements();
            return;
        }

        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    animateElement(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Initially hide elements for animation
        const animatedElements = [
            ...processSteps,
            ...infoCards,
            document.querySelector('.edsys-additional-info'),
            document.querySelector('.edsys-cta-section')
        ].filter(Boolean);

        animatedElements.forEach(function(element, index) {
            // Add initial hidden state
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';

            // Observe element
            observer.observe(element);
        });
    }

    /**
     * Animate element into view
     */
    function animateElement(element) {
        const delay = element.dataset.animationDelay || 0;

        setTimeout(function() {
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, delay);
    }

    /**
     * Fallback: show all elements without animation
     */
    function showAllElements() {
        const elements = [
            ...processSteps,
            ...infoCards,
            document.querySelector('.edsys-additional-info'),
            document.querySelector('.edsys-cta-section')
        ].filter(Boolean);

        elements.forEach(function(element) {
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        });
    }

    /**
     * Setup contact link enhancements
     */
    function setupContactLinks() {
        if (!contactLinks.length) return;

        contactLinks.forEach(function(link) {
            const href = link.getAttribute('href');

            // Add click tracking for contact links
            link.addEventListener('click', function(e) {
                const linkType = getLinkType(href);

                // Dispatch custom event for analytics
                const event = new CustomEvent('edsContactClick', {
                    detail: {
                        type: linkType,
                        value: href
                    }
                });
                document.dispatchEvent(event);

                // Handle special link types
                if (href && href.startsWith('tel:')) {
                    // For telephone links, add small delay for analytics
                    e.preventDefault();
                    setTimeout(function() {
                        window.location.href = href;
                    }, 100);
                } else if (href && href.startsWith('mailto:')) {
                    // For email links, add small delay for analytics
                    e.preventDefault();
                    setTimeout(function() {
                        window.location.href = href;
                    }, 100);
                }
            });

            // Add hover effect enhancement
            link.addEventListener('mouseenter', function() {
                this.style.transform = 'translateX(4px)';
            });

            link.addEventListener('mouseleave', function() {
                this.style.transform = 'translateX(0)';
            });
        });
    }

    /**
     * Get link type for analytics
     */
    function getLinkType(href) {
        if (!href) return 'unknown';

        if (href.startsWith('tel:')) return 'phone';
        if (href.startsWith('mailto:')) return 'email';
        if (href.includes('yandex.ru/maps')) return 'map';
        if (href.startsWith('http')) return 'external';
        return 'internal';
    }

    /**
     * Setup click tracking for various elements
     */
    function setupClickTracking() {
        // Track process step interactions
        processSteps.forEach(function(step, index) {
            step.addEventListener('click', function() {
                const event = new CustomEvent('edsProcessStepClick', {
                    detail: {
                        step: index + 1,
                        title: step.querySelector('.edsys-process-step__title')?.textContent || 'Unknown'
                    }
                });
                document.dispatchEvent(event);
            });
        });

        // Track info card interactions
        infoCards.forEach(function(card, index) {
            card.addEventListener('click', function(e) {
                // Don't track if clicking on links
                if (e.target.closest('a')) return;

                const event = new CustomEvent('edsInfoCardClick', {
                    detail: {
                        card: index + 1,
                        title: card.querySelector('.edsys-info-card__title')?.textContent || 'Unknown'
                    }
                });
                document.dispatchEvent(event);
            });
        });

        // Track CTA button clicks
        ctaButtons.forEach(function(button) {
            button.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const text = this.textContent.trim();

                const event = new CustomEvent('edsCTAClick', {
                    detail: {
                        text: text,
                        href: href
                    }
                });
                document.dispatchEvent(event);
            });
        });
    }

    /**
     * Setup smooth scrolling for internal links
     */
    function setupSmoothScrolling() {
        const internalLinks = document.querySelectorAll('a[href^="#"]');

        internalLinks.forEach(function(link) {
            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const targetId = href.substring(1);
                const target = document.getElementById(targetId);

                if (target) {
                    e.preventDefault();
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    }

    /**
     * Setup keyboard navigation enhancements
     */
    function setupKeyboardNavigation() {
        // Handle arrow key navigation for process steps
        processSteps.forEach(function(step, index) {
            step.setAttribute('tabindex', '0');

            step.addEventListener('keydown', function(e) {
                if (e.key === 'ArrowRight' || e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextStep = processSteps[index + 1];
                    if (nextStep) nextStep.focus();
                } else if (e.key === 'ArrowLeft' || e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevStep = processSteps[index - 1];
                    if (prevStep) prevStep.focus();
                } else if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    step.click();
                }
            });
        });

        // Handle keyboard navigation for info cards
        infoCards.forEach(function(card, index) {
            card.setAttribute('tabindex', '0');

            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    // Focus first link in card if exists
                    const firstLink = card.querySelector('a');
                    if (firstLink) {
                        firstLink.focus();
                    } else {
                        card.click();
                    }
                }
            });
        });
    }

    /**
     * Add visual feedback for form interactions
     */
    function addVisualFeedback() {
        // Add loading states for external links
        const externalLinks = document.querySelectorAll('a[target="_blank"]');

        externalLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                // Add loading indicator
                const originalText = this.textContent;
                this.style.opacity = '0.7';
                this.style.pointerEvents = 'none';

                // Reset after short delay
                setTimeout(function() {
                    link.style.opacity = '1';
                    link.style.pointerEvents = 'auto';
                }, 1000);
            });
        });
    }

    /**
     * Setup responsive behavior
     */
    function setupResponsiveBehavior() {
        let resizeTimeout;

        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Adjust layouts on resize if needed
                adjustLayoutForViewport();
            }, 150);
        });

        // Initial adjustment
        adjustLayoutForViewport();
    }

    /**
     * Adjust layout based on viewport
     */
    function adjustLayoutForViewport() {
        const isMobile = window.innerWidth <= 768;

        // Adjust animations for mobile
        if (isMobile) {
            // Reduce animation delays on mobile
            processSteps.forEach(function(step, index) {
                step.dataset.animationDelay = index * 100;
            });
        } else {
            // Standard delays for desktop
            processSteps.forEach(function(step, index) {
                step.dataset.animationDelay = index * 200;
            });
        }
    }

    /**
     * Error handling wrapper
     */
    function handleError(error, context) {
        console.error('EDS Payment & Delivery Error (' + context + '):', error);

        // Dispatch error event for monitoring
        const event = new CustomEvent('edsError', {
            detail: {
                context: context,
                message: error.message,
                stack: error.stack
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Utility function to debounce function calls
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Public API for external access
     */
    window.EDSPaymentDelivery = {
        init: init,
        animateElement: animateElement,
        version: '1.0.0'
    };

    // Auto-initialize
    init();

})();