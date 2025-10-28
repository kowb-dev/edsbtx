/**
 * Professional Mobile-First Image Modal Handler v4.0
 * Optimized for touch devices with enhanced mobile UX
 * File: assets/js/modal.js
 *
 * @author EDS Development Team
 * @version 4.0
 * @created 2025-06-27
 * @updated 2025-06-28
 */

(function() {
    'use strict';

    // Enhanced mobile-first configuration
    const CONFIG = {
        modalSelector: '#imageModal',
        modalImageSelector: '#modalImage',
        triggerSelector: '[data-modal-trigger]',
        closeSelector: '.edsys-modal-close',
        overlaySelector: '.edsys-modal-overlay',
        containerSelector: '.edsys-modal-container',
        activeClass: 'active',
        focusDelay: 150,
        touchThreshold: 10, // Minimum distance for touch gestures
        swipeThreshold: 50, // Minimum distance for swipe to close
        animationDuration: 300
    };

    // Enhanced state management with mobile support
    const state = {
        modal: null,
        modalImage: null,
        triggerElement: null,
        previousFocus: null,
        isOpen: false,
        isMobile: false,
        touchStartY: 0,
        touchStartX: 0,
        isDragging: false,
        hasPassiveSupport: false
    };

    /**
     * Initialize modal functionality with mobile detection
     */
    function init() {
        // Detect mobile environment
        detectMobileEnvironment();

        // Test for passive event listener support
        testPassiveSupport();

        // Cache DOM elements
        state.modal = document.querySelector(CONFIG.modalSelector);
        state.modalImage = document.querySelector(CONFIG.modalImageSelector);
        state.triggerElement = document.querySelector(CONFIG.triggerSelector);

        if (!state.modal || !state.modalImage || !state.triggerElement) {
            console.warn('EDS Modal: Required elements not found');
            return;
        }

        // Setup event listeners with mobile optimizations
        setupEventListeners();

        // Setup mobile-specific optimizations
        if (state.isMobile) {
            setupMobileOptimizations();
        }

        console.log('EDS Modal: Initialized successfully', {
            isMobile: state.isMobile,
            hasPassiveSupport: state.hasPassiveSupport
        });
    }

    /**
     * Detect mobile environment and capabilities
     */
    function detectMobileEnvironment() {
        // Multiple mobile detection methods for better accuracy
        state.isMobile = (
            /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ||
            'ontouchstart' in window ||
            navigator.maxTouchPoints > 0 ||
            window.innerWidth <= 768
        );

        // Add mobile class to document for CSS targeting
        if (state.isMobile) {
            document.documentElement.classList.add('eds-mobile');
        }
    }

    /**
     * Test for passive event listener support
     */
    function testPassiveSupport() {
        try {
            const options = Object.defineProperty({}, 'passive', {
                get: function() {
                    state.hasPassiveSupport = true;
                    return false;
                }
            });
            window.addEventListener('test', null, options);
            window.removeEventListener('test', null, options);
        } catch (err) {
            state.hasPassiveSupport = false;
        }
    }

    /**
     * Setup enhanced event listeners with mobile support
     */
    function setupEventListeners() {
        const passiveOptions = state.hasPassiveSupport ? { passive: false } : false;

        // Image interaction handlers
        state.triggerElement.addEventListener('click', handleImageClick);
        state.triggerElement.addEventListener('keydown', handleImageKeydown);

        // Touch events for mobile
        if (state.isMobile) {
            state.triggerElement.addEventListener('touchstart', handleTouchStart, passiveOptions);
            state.triggerElement.addEventListener('touchend', handleTouchEnd, passiveOptions);
        }

        // Modal close handlers
        const closeButton = state.modal.querySelector(CONFIG.closeSelector);
        if (closeButton) {
            closeButton.addEventListener('click', closeModal);

            // Touch optimization for close button
            if (state.isMobile) {
                closeButton.addEventListener('touchstart', handleCloseTouch, passiveOptions);
            }
        }

        // Overlay interaction
        state.modal.addEventListener('click', handleOverlayClick);

        // Prevent modal content clicks from closing modal
        const container = state.modal.querySelector(CONFIG.containerSelector);
        if (container) {
            container.addEventListener('click', handleContainerClick);

            // Touch events for swipe-to-close on mobile
            if (state.isMobile) {
                container.addEventListener('touchstart', handleModalTouchStart, passiveOptions);
                container.addEventListener('touchmove', handleModalTouchMove, passiveOptions);
                container.addEventListener('touchend', handleModalTouchEnd, passiveOptions);
            }
        }

        // Browser navigation and lifecycle
        window.addEventListener('popstate', handlePopState);
        window.addEventListener('beforeunload', cleanup);
        window.addEventListener('orientationchange', handleOrientationChange);
        window.addEventListener('resize', debounce(handleResize, 250));
    }

    /**
     * Setup mobile-specific optimizations
     */
    function setupMobileOptimizations() {
        // Disable zoom on modal open
        const viewport = document.querySelector('meta[name=viewport]');
        if (viewport) {
            state.originalViewport = viewport.getAttribute('content');
        }

        // Improve touch responsiveness
        if (state.triggerElement) {
            state.triggerElement.style.touchAction = 'manipulation';
        }

        // Add mobile-specific ARIA labels
        state.triggerElement.setAttribute('aria-label', 'Коснитесь для увеличения изображения');
    }

    /**
     * Handle touch start on trigger image
     */
    function handleTouchStart(event) {
        const touch = event.touches[0];
        state.touchStartX = touch.clientX;
        state.touchStartY = touch.clientY;

        // Add touch feedback
        event.currentTarget.style.opacity = '0.8';
    }

    /**
     * Handle touch end on trigger image
     */
    function handleTouchEnd(event) {
        event.preventDefault();

        // Remove touch feedback
        event.currentTarget.style.opacity = '';

        const touch = event.changedTouches[0];
        const deltaX = Math.abs(touch.clientX - state.touchStartX);
        const deltaY = Math.abs(touch.clientY - state.touchStartY);

        // Check if it's a tap (small movement)
        if (deltaX < CONFIG.touchThreshold && deltaY < CONFIG.touchThreshold) {
            openModal();
        }
    }

    /**
     * Handle touch on close button
     */
    function handleCloseTouch(event) {
        event.preventDefault();
        event.currentTarget.style.backgroundColor = 'rgba(255, 255, 255, 0.2)';

        setTimeout(() => {
            closeModal();
        }, 100);
    }

    /**
     * Handle touch start on modal for swipe-to-close
     */
    function handleModalTouchStart(event) {
        const touch = event.touches[0];
        state.touchStartY = touch.clientY;
        state.touchStartX = touch.clientX;
        state.isDragging = false;
    }

    /**
     * Handle touch move on modal
     */
    function handleModalTouchMove(event) {
        if (!state.touchStartY) return;

        const touch = event.touches[0];
        const deltaY = touch.clientY - state.touchStartY;
        const deltaX = Math.abs(touch.clientX - state.touchStartX);

        // Only handle vertical swipes
        if (Math.abs(deltaY) > CONFIG.touchThreshold && deltaX < CONFIG.touchThreshold * 2) {
            state.isDragging = true;

            // Apply transform for visual feedback
            if (deltaY > 0) { // Swipe down
                const container = state.modal.querySelector(CONFIG.containerSelector);
                const opacity = Math.max(0.3, 1 - (deltaY / 200));
                const scale = Math.max(0.8, 1 - (deltaY / 1000));

                container.style.transform = `translateY(${deltaY}px) scale(${scale})`;
                state.modal.style.backgroundColor = `rgba(0, 0, 0, ${opacity * 0.4})`;
            }
        }
    }

    /**
     * Handle touch end on modal
     */
    function handleModalTouchEnd(event) {
        if (!state.isDragging || !state.touchStartY) return;

        const touch = event.changedTouches[0];
        const deltaY = touch.clientY - state.touchStartY;

        // Reset transforms
        const container = state.modal.querySelector(CONFIG.containerSelector);
        container.style.transform = '';
        state.modal.style.backgroundColor = '';

        // Close if swipe distance exceeds threshold
        if (deltaY > CONFIG.swipeThreshold) {
            closeModal();
        }

        state.isDragging = false;
        state.touchStartY = 0;
        state.touchStartX = 0;
    }

    /**
     * Handle image click with mobile considerations
     */
    function handleImageClick(event) {
        event.preventDefault();

        // Prevent double-tap zoom on mobile
        if (state.isMobile) {
            event.stopPropagation();
        }

        openModal();
    }

    /**
     * Handle keyboard navigation for image
     */
    function handleImageKeydown(event) {
        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            openModal();
        }
    }

    /**
     * Handle overlay click with touch consideration
     */
    function handleOverlayClick(event) {
        if (event.target === state.modal && !state.isDragging) {
            closeModal();
        }
    }

    /**
     * Handle container click (prevent event bubbling)
     */
    function handleContainerClick(event) {
        event.stopPropagation();
    }

    /**
     * Handle browser back button
     */
    function handlePopState() {
        if (state.isOpen) {
            closeModal();
        }
    }

    /**
     * Handle orientation change on mobile
     */
    function handleOrientationChange() {
        if (state.isOpen && state.isMobile) {
            // Small delay to let orientation change complete
            setTimeout(() => {
                adjustModalForOrientation();
            }, 100);
        }
    }

    /**
     * Handle window resize
     */
    function handleResize() {
        if (state.isOpen) {
            adjustModalSize();
        }
    }

    /**
     * Adjust modal for orientation changes
     */
    function adjustModalForOrientation() {
        const modalImage = state.modal.querySelector(CONFIG.modalImageSelector);
        if (modalImage) {
            // Recalculate image dimensions
            modalImage.style.maxHeight = window.innerHeight < 500 ? '40vh' : '60vh';
        }
    }

    /**
     * Adjust modal size for different screen sizes
     */
    function adjustModalSize() {
        const container = state.modal.querySelector(CONFIG.containerSelector);
        if (container && state.isMobile && window.innerWidth <= 768) {
            container.style.maxHeight = '95vh';
        }
    }

    /**
     * Handle global keyboard events when modal is open
     */
    function handleModalKeydown(event) {
        // Close on Escape
        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
            return;
        }

        // Trap focus within modal
        if (event.key === 'Tab') {
            trapFocus(event);
        }
    }

    /**
     * Trap focus within modal
     */
    function trapFocus(event) {
        const focusableElements = state.modal.querySelectorAll(
            'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
        );

        if (focusableElements.length === 0) return;

        const firstElement = focusableElements[0];
        const lastElement = focusableElements[focusableElements.length - 1];

        if (event.shiftKey) {
            if (document.activeElement === firstElement) {
                event.preventDefault();
                lastElement.focus();
            }
        } else {
            if (document.activeElement === lastElement) {
                event.preventDefault();
                firstElement.focus();
            }
        }
    }

    /**
     * Open modal with mobile-optimized accessibility
     */
    function openModal() {
        if (!state.modal || !state.modalImage || !state.triggerElement || state.isOpen) {
            return;
        }

        try {
            // Store current focus
            state.previousFocus = document.activeElement;

            // Mobile-specific preparations
            if (state.isMobile) {
                prepareMobileModal();
            }

            // Set modal image source and alt text
            state.modalImage.src = state.triggerElement.src;
            state.modalImage.alt = state.triggerElement.alt;

            // Show modal with mobile-optimized animation
            state.modal.classList.add(CONFIG.activeClass);
            state.modal.setAttribute('aria-hidden', 'false');
            state.isOpen = true;

            // Prevent body scroll with mobile considerations
            preventBodyScroll();

            // Focus management with mobile delay
            setTimeout(() => {
                const closeButton = state.modal.querySelector(CONFIG.closeSelector);
                if (closeButton && !state.isMobile) {
                    // On mobile, don't auto-focus to prevent keyboard popup
                    closeButton.focus();
                }
            }, CONFIG.focusDelay);

            // Add keyboard listener
            document.addEventListener('keydown', handleModalKeydown);

            // Announce to screen readers
            announceToScreenReader('Изображение открыто в полном размере');

            // Dispatch custom event
            dispatchEvent('edsModalOpen');

        } catch (error) {
            console.error('EDS Modal: Error opening modal', error);
        }
    }

    /**
     * Prepare modal for mobile display
     */
    function prepareMobileModal() {
        // Disable viewport zooming
        const viewport = document.querySelector('meta[name=viewport]');
        if (viewport) {
            viewport.setAttribute('content', 'width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no');
        }

        // Set initial modal position for mobile
        const container = state.modal.querySelector(CONFIG.containerSelector);
        if (container) {
            container.style.transform = 'translateY(100%)';
            // Animate in from bottom
            setTimeout(() => {
                container.style.transition = `transform ${CONFIG.animationDuration}ms ease-out`;
                container.style.transform = 'translateY(0)';
            }, 10);
        }
    }

    /**
     * Close modal with mobile optimizations
     */
    function closeModal() {
        if (!state.modal || !state.isOpen) {
            return;
        }

        try {
            // Mobile-specific closing animation
            if (state.isMobile) {
                const container = state.modal.querySelector(CONFIG.containerSelector);
                if (container) {
                    container.style.transform = 'translateY(100%)';

                    // Delay actual close to show animation
                    setTimeout(() => {
                        completeModalClose();
                    }, CONFIG.animationDuration);
                    return;
                }
            }

            completeModalClose();

        } catch (error) {
            console.error('EDS Modal: Error closing modal', error);
        }
    }

    /**
     * Complete the modal closing process
     */
    function completeModalClose() {
        // Hide modal
        state.modal.classList.remove(CONFIG.activeClass);
        state.modal.setAttribute('aria-hidden', 'true');
        state.isOpen = false;

        // Restore body scroll
        restoreBodyScroll();

        // Restore viewport on mobile
        if (state.isMobile && state.originalViewport) {
            const viewport = document.querySelector('meta[name=viewport]');
            if (viewport) {
                viewport.setAttribute('content', state.originalViewport);
            }
        }

        // Reset container transform
        const container = state.modal.querySelector(CONFIG.containerSelector);
        if (container) {
            container.style.transform = '';
            container.style.transition = '';
        }

        // Restore focus
        if (state.previousFocus) {
            state.previousFocus.focus();
            state.previousFocus = null;
        }

        // Remove keyboard listener
        document.removeEventListener('keydown', handleModalKeydown);

        // Announce to screen readers
        announceToScreenReader('Изображение закрыто');

        // Dispatch custom event
        dispatchEvent('edsModalClose');
    }

    /**
     * Prevent body scroll with mobile considerations
     */
    function preventBodyScroll() {
        state.scrollY = window.scrollY;

        if (state.isMobile) {
            // More aggressive scroll prevention for mobile
            document.body.style.position = 'fixed';
            document.body.style.top = `-${state.scrollY}px`;
            document.body.style.width = '100%';
        } else {
            document.body.style.overflow = 'hidden';
        }
    }

    /**
     * Restore body scroll
     */
    function restoreBodyScroll() {
        if (state.isMobile) {
            document.body.style.position = '';
            document.body.style.top = '';
            document.body.style.width = '';
            window.scrollTo(0, state.scrollY);
        } else {
            document.body.style.overflow = '';
        }
    }

    /**
     * Announce content to screen readers
     */
    function announceToScreenReader(message) {
        const announcement = document.createElement('div');
        announcement.setAttribute('aria-live', 'polite');
        announcement.setAttribute('aria-atomic', 'true');
        announcement.className = 'sr-only';
        announcement.textContent = message;

        document.body.appendChild(announcement);

        setTimeout(() => {
            document.body.removeChild(announcement);
        }, 1000);
    }

    /**
     * Debounce function for performance optimization
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
     * Dispatch custom events
     */
    function dispatchEvent(eventName) {
        const event = new CustomEvent(eventName, {
            detail: {
                modal: state.modal,
                triggerElement: state.triggerElement,
                isMobile: state.isMobile
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Cleanup function
     */
    function cleanup() {
        document.removeEventListener('keydown', handleModalKeydown);
        if (state.isOpen) {
            restoreBodyScroll();
        }

        // Clean up mobile modifications
        if (state.isMobile && state.originalViewport) {
            const viewport = document.querySelector('meta[name=viewport]');
            if (viewport) {
                viewport.setAttribute('content', state.originalViewport);
            }
        }
    }

    /**
     * Enhanced public API with mobile features
     */
    const EdsModal = {
        open: openModal,
        close: closeModal,
        isOpen: () => state.isOpen,
        isMobile: () => state.isMobile,
        getState: () => ({ ...state }),

        // Mobile-specific methods
        enableTouchGestures: () => {
            if (state.isMobile) {
                setupMobileOptimizations();
            }
        },

        // Configuration methods
        setConfig: (newConfig) => {
            Object.assign(CONFIG, newConfig);
        }
    };

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Expose to global scope
    if (typeof window !== 'undefined') {
        window.EdsModal = EdsModal;
    }

})();