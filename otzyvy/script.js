/**
 * Reviews Page Scripts
 * Version: 1.0.0
 * Date: 2025-07-16
 * Description: Интерактивность для страницы отзывов EDS
 */

(function() {
    'use strict';

    // DOM Elements
    let sidebar = null;
    let sidebarHeader = null;
    let sidebarToggle = null;
    let testimonials = null;

    /**
     * Initialize the reviews page functionality
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
        sidebar = document.querySelector('.edsys-sidebar');
        sidebarHeader = document.querySelector('.edsys-sidebar__header');
        sidebarToggle = document.querySelector('.edsys-sidebar__toggle');
        testimonials = document.querySelectorAll('.edsys-testimonial');

        if (!sidebar || !sidebarHeader || !sidebarToggle) {
            console.warn('EDS Reviews: Required sidebar elements not found');
            return;
        }

        // Mobile sidebar toggle
        setupSidebarToggle();

        // Testimonials enhancement
        setupTestimonials();

        // Smooth scrolling for internal links
        setupSmoothScrolling();

        // Intersection Observer for animations
        setupScrollAnimations();

        // Keyboard navigation
        setupKeyboardNavigation();

        console.info('EDS Reviews: Page initialized successfully');
    }

    /**
     * Setup mobile sidebar toggle functionality
     */
    function setupSidebarToggle() {
        // Toggle sidebar on mobile
        sidebarHeader.addEventListener('click', function(e) {
            e.preventDefault();

            // Only toggle on mobile screens
            if (window.innerWidth <= 768) {
                toggleSidebar();
            }
        });

        // Handle window resize
        let resizeTimeout;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(function() {
                // Reset sidebar state on desktop
                if (window.innerWidth > 768) {
                    sidebar.classList.remove('edsys-sidebar--open');
                }
            }, 150);
        });
    }

    /**
     * Toggle sidebar open/closed state
     */
    function toggleSidebar() {
        const isOpen = sidebar.classList.contains('edsys-sidebar--open');

        if (isOpen) {
            sidebar.classList.remove('edsys-sidebar--open');
            sidebarToggle.setAttribute('aria-label', 'Показать категории');
        } else {
            sidebar.classList.add('edsys-sidebar--open');
            sidebarToggle.setAttribute('aria-label', 'Скрыть категории');
        }

        // Dispatch custom event for analytics
        const event = new CustomEvent('edsSidebarToggle', {
            detail: { isOpen: !isOpen }
        });
        document.dispatchEvent(event);
    }

    /**
     * Setup testimonials enhancements
     */
    function setupTestimonials() {
        if (!testimonials.length) return;

        testimonials.forEach(function(testimonial, index) {
            // Add loading state initially
            testimonial.style.opacity = '0';
            testimonial.style.transform = 'translateY(20px)';

            // Enhance external links
            const externalLink = testimonial.querySelector('.edsys-testimonial__link[target="_blank"]');
            if (externalLink) {
                enhanceExternalLink(externalLink);
            }

            // Add click tracking for analytics
            testimonial.addEventListener('click', function(e) {
                // Don't track if clicking on links
                if (e.target.closest('a')) return;

                // Dispatch custom event for analytics
                const event = new CustomEvent('edsTestimonialView', {
                    detail: {
                        index: index,
                        author: testimonial.querySelector('.edsys-testimonial__author-name')?.textContent || 'Unknown'
                    }
                });
                document.dispatchEvent(event);
            });
        });
    }

    /**
     * Enhance external links with additional attributes and behavior
     */
    function enhanceExternalLink(link) {
        // Add rel attributes for security
        const currentRel = link.getAttribute('rel') || '';
        if (!currentRel.includes('noopener')) {
            link.setAttribute('rel', currentRel + ' noopener');
        }

        // Add click tracking
        link.addEventListener('click', function(e) {
            const href = this.getAttribute('href');

            // Dispatch custom event for analytics
            const event = new CustomEvent('edsExternalLinkClick', {
                detail: { url: href }
            });
            document.dispatchEvent(event);

            // Add small delay for analytics
            if (href && href.startsWith('http')) {
                e.preventDefault();
                setTimeout(function() {
                    window.open(href, '_blank', 'noopener,noreferrer');
                }, 100);
            }
        });
    }

    /**
     * Setup smooth scrolling for internal links
     */
    function setupSmoothScrolling() {
        const internalLinks = document.querySelectorAll('a[href^="#"], a[href^="/"]');

        internalLinks.forEach(function(link) {
            // Skip external links and links that open in new window
            if (link.getAttribute('target') === '_blank') return;

            link.addEventListener('click', function(e) {
                const href = this.getAttribute('href');

                // Handle anchor links
                if (href.startsWith('#')) {
                    const targetId = href.substring(1);
                    const target = document.getElementById(targetId);

                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                }
            });
        });
    }

    /**
     * Setup scroll animations using Intersection Observer
     */
    function setupScrollAnimations() {
        // Check if browser supports Intersection Observer
        if (!window.IntersectionObserver) {
            // Fallback: show all testimonials immediately
            showAllTestimonials();
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
                    animateTestimonial(entry.target);
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Observe all testimonials
        testimonials.forEach(function(testimonial) {
            observer.observe(testimonial);
        });
    }

    /**
     * Animate testimonial into view
     */
    function animateTestimonial(testimonial) {
        // Use CSS transitions for smooth animation
        testimonial.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        testimonial.style.opacity = '1';
        testimonial.style.transform = 'translateY(0)';
    }

    /**
     * Fallback: show all testimonials without animation
     */
    function showAllTestimonials() {
        testimonials.forEach(function(testimonial) {
            testimonial.style.opacity = '1';
            testimonial.style.transform = 'translateY(0)';
        });
    }

    /**
     * Setup keyboard navigation enhancements
     */
    function setupKeyboardNavigation() {
        // Handle escape key to close mobile sidebar
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                if (sidebar && sidebar.classList.contains('edsys-sidebar--open')) {
                    toggleSidebar();
                }
            }
        });

        // Enhance focus management for sidebar links
        const sidebarLinks = document.querySelectorAll('.edsys-sidebar__link');
        sidebarLinks.forEach(function(link, index) {
            link.addEventListener('keydown', function(e) {
                // Arrow key navigation within sidebar
                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const nextLink = sidebarLinks[index + 1];
                    if (nextLink) nextLink.focus();
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prevLink = sidebarLinks[index - 1];
                    if (prevLink) prevLink.focus();
                }
            });
        });
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
     * Error handling wrapper
     */
    function handleError(error, context) {
        console.error('EDS Reviews Error (' + context + '):', error);

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
     * Public API for external access
     */
    window.EDSReviews = {
        init: init,
        toggleSidebar: toggleSidebar,
        version: '1.0.0'
    };

    // Auto-initialize
    init();

})();