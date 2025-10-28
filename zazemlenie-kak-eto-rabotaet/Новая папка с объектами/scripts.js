/**
 * JavaScript для функциональности страницы статьи
 * "Заземление. Как это работает?"
 */

document.addEventListener('DOMContentLoaded', function() {
    console.log('Инициализация страницы статьи о заземлении');

    // Initialize all components
    initMobileNavigation();
    initImageZoom();
    initSmoothScroll();
    initScrollAnimations();
    initPerformanceTracking();

    console.log('Страница статьи инициализирована успешно');
});

/**
 * Mobile Navigation
 */
function initMobileNavigation() {
    const mobileNavBtn = document.getElementById('mobileNavBtn');
    const mobileNavMenu = document.getElementById('mobileNavMenu');
    const mobileNavClose = document.getElementById('mobileNavClose');
    const mobileNavOverlay = document.getElementById('mobileNavOverlay');

    if (!mobileNavBtn || !mobileNavMenu || !mobileNavClose || !mobileNavOverlay) {
        console.warn('Mobile navigation elements not found');
        return;
    }

    // Open mobile menu
    function openMobileNav() {
        mobileNavMenu.classList.add('open');
        mobileNavOverlay.classList.add('show');
        document.body.style.overflow = 'hidden';

        // Focus management for accessibility
        const firstLink = mobileNavMenu.querySelector('.mobile-nav-item');
        if (firstLink) {
            setTimeout(() => firstLink.focus(), 300);
        }
    }

    // Close mobile menu
    function closeMobileNav() {
        mobileNavMenu.classList.remove('open');
        mobileNavOverlay.classList.remove('show');
        document.body.style.overflow = '';

        // Return focus to button
        mobileNavBtn.focus();
    }

    // Event handlers
    mobileNavBtn.addEventListener('click', openMobileNav);
    mobileNavClose.addEventListener('click', closeMobileNav);
    mobileNavOverlay.addEventListener('click', closeMobileNav);

    // Close mobile menu when clicking on link
    const mobileNavLinks = mobileNavMenu.querySelectorAll('.mobile-nav-item');
    mobileNavLinks.forEach(link => {
        link.addEventListener('click', () => {
            // Small delay for smooth transition
            setTimeout(closeMobileNav, 150);
        });
    });

    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && mobileNavMenu.classList.contains('open')) {
            closeMobileNav();
        }
    });

    // Handle window resize
    window.addEventListener('resize', debounce(() => {
        if (window.innerWidth > 1024 && mobileNavMenu.classList.contains('open')) {
            closeMobileNav();
        }
    }, 250));

    console.log('Mobile navigation initialized');
}

/**
 * Image Zoom for technical images
 */
function initImageZoom() {
    const zoomableImages = document.querySelectorAll('.article-hero__image, .technical-image');

    zoomableImages.forEach(img => {
        img.style.cursor = 'zoom-in';
        img.addEventListener('click', function() {
            createImageZoom(this);
        });
    });

    console.log(`Image zoom initialized for ${zoomableImages.length} images`);
}

/**
 * Create zoom overlay for image
 */
function createImageZoom(img) {
    // Check if zoom is already open
    if (document.querySelector('.image-zoom-overlay')) {
        return;
    }

    const overlay = document.createElement('div');
    overlay.className = 'image-zoom-overlay';
    overlay.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        background: rgba(0, 0, 0, 0.9);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 10000;
        cursor: zoom-out;
        opacity: 0;
        transition: opacity 0.3s ease;
    `;

    const zoomedImg = img.cloneNode();
    zoomedImg.style.cssText = `
        max-width: 90vw;
        max-height: 90vh;
        object-fit: contain;
        border-radius: 8px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.5);
        transform: scale(0.8);
        transition: transform 0.3s ease;
    `;

    // Close button
    const closeBtn = document.createElement('button');
    closeBtn.innerHTML = '<i class="ph ph-thin ph-x"></i>';
    closeBtn.style.cssText = `
        position: absolute;
        top: 20px;
        right: 20px;
        width: 48px;
        height: 48px;
        background: rgba(255, 255, 255, 0.2);
        border: none;
        border-radius: 50%;
        color: white;
        font-size: 24px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: background 0.2s ease;
        backdrop-filter: blur(10px);
    `;

    closeBtn.addEventListener('mouseenter', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.3)';
    });

    closeBtn.addEventListener('mouseleave', () => {
        closeBtn.style.background = 'rgba(255, 255, 255, 0.2)';
    });

    overlay.appendChild(zoomedImg);
    overlay.appendChild(closeBtn);
    document.body.appendChild(overlay);

    // Prevent background scrolling
    document.body.style.overflow = 'hidden';

    // Animation
    requestAnimationFrame(() => {
        overlay.style.opacity = '1';
        zoomedImg.style.transform = 'scale(1)';
    });

    // Close function
    function closeZoom() {
        overlay.style.opacity = '0';
        zoomedImg.style.transform = 'scale(0.8)';

        setTimeout(() => {
            if (overlay.parentNode) {
                document.body.removeChild(overlay);
            }
            document.body.style.overflow = '';
        }, 300);
    }

    // Close handlers
    overlay.addEventListener('click', (e) => {
        if (e.target === overlay) {
            closeZoom();
        }
    });

    closeBtn.addEventListener('click', closeZoom);

    // Close on ESC
    const handleEscape = (e) => {
        if (e.key === 'Escape') {
            closeZoom();
            document.removeEventListener('keydown', handleEscape);
        }
    };
    document.addEventListener('keydown', handleEscape);
}

/**
 * Smooth scroll for anchor links
 */
function initSmoothScroll() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const targetId = this.getAttribute('href').substring(1);
            if (!targetId) return;

            const targetElement = document.getElementById(targetId);

            if (targetElement) {
                e.preventDefault();

                const offsetTop = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const offset = window.innerWidth <= 768 ? 80 : 100;

                window.scrollTo({
                    top: offsetTop - offset,
                    behavior: 'smooth'
                });
            }
        });
    });

    console.log('Smooth scroll initialized');
}

/**
 * Scroll animations
 */
function initScrollAnimations() {
    if (!window.IntersectionObserver) {
        console.warn('IntersectionObserver not supported');
        return;
    }

    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');

                // Add delay for child elements
                const children = entry.target.querySelectorAll('.step-item, .comparison-card, '.category-card, .requirement-item');
                children.forEach((child, index) => {
                    setTimeout(() => {
                        child.classList.add('animate-in');
                    }, index * 100);
                });
            }
        });
    }, observerOptions);

    // Observe content sections
    const sections = document.querySelectorAll('.content-section, .product-categories, .comparison-grid, .requirements-grid');
    sections.forEach(section => {
        observer.observe(section);
    });

    // CSS for animations
    const animationStyles = document.createElement('style');
    animationStyles.textContent = `
        .content-section,
        .product-categories,
        .step-item,
        .comparison-card,
        .category-card,
        .requirement-item {
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.6s ease, transform 0.6s ease;
        }

        .animate-in {
            opacity: 1 !important;
            transform: translateY(0) !important;
        }

        @media (prefers-reduced-motion: reduce) {
            .content-section,
            .product-categories,
            .step-item,
            .comparison-card,
            .category-card,
            .requirement-item {
                opacity: 1 !important;
                transform: none !important;
                transition: none !important;
            }
        }
    `;

    document.head.appendChild(animationStyles);
    console.log('Scroll animations initialized');
}

/**
 * Performance tracking
 */
function initPerformanceTracking() {
    if (!('performance' in window)) {
        console.warn('Performance API not supported');
        return;
    }

    window.addEventListener('load', () => {
        // Measure load time
        const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;

        // Measure First Contentful Paint
        const paintEntries = performance.getEntriesByType('paint');
        const fcp = paintEntries.find(entry => entry.name === 'first-contentful-paint');

        console.log('Performance metrics:', {
            loadTime: loadTime + 'ms',
            fcp: fcp ? Math.round(fcp.startTime) + 'ms' : 'unavailable'
        });

        // Track resource errors
        const resourceErrors = performance.getEntriesByType('resource')
            .filter(entry => entry.transferSize === 0 && entry.decodedBodySize === 0);

        if (resourceErrors.length > 0) {
            console.warn('Resource loading errors:', resourceErrors);
        }
    });

    console.log('Performance tracking initialized');
}

/**
 * Debounce utility
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
 * Throttle utility
 */
function throttle(func, limit) {
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
 * JavaScript error handling
 */
window.addEventListener('error', function(e) {
    console.error('JavaScript error on grounding article page:', {
        message: e.message,
        filename: e.filename,
        lineno: e.lineno,
        colno: e.colno,
        error: e.error
    });
});

/**
 * Unhandled promise rejections
 */
window.addEventListener('unhandledrejection', function(e) {
    console.error('Unhandled promise rejection:', e.reason);
});

/**
 * Accessibility improvements
 */
document.addEventListener('DOMContentLoaded', function() {
    // Improve focus management
    const focusableElements = document.querySelectorAll(
        'a, button, input, textarea, select, details, [tabindex]:not([tabindex="-1"])'
    );

    // Add visible focus for keyboard navigation
    focusableElements.forEach(element => {
        element.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                this.setAttribute('data-keyboard-focus', 'true');
            }
        });

        element.addEventListener('mousedown', function() {
            this.removeAttribute('data-keyboard-focus');
        });
    });

    // CSS for keyboard focus
    const focusStyles = document.createElement('style');
    focusStyles.textContent = `
        [data-keyboard-focus="true"]:focus {
            outline: 2px solid var(--edsys-accent) !important;
            outline-offset: 2px !important;
        }
    `;
    document.head.appendChild(focusStyles);

    console.log('Accessibility improvements applied');
});

/**
 * Lazy loading for images (if browser doesn't support natively)
 */
function initLazyLoading() {
    if ('loading' in HTMLImageElement.prototype) {
        return; // Browser supports native lazy loading
    }

    const images = document.querySelectorAll('img[loading="lazy"]');

    if (!images.length) return;

    const imageObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src || img.src;
                img.classList.remove('lazy');
                imageObserver.unobserve(img);
            }
        });
    });

    images.forEach(img => {
        img.classList.add('lazy');
        imageObserver.observe(img);
    });

    console.log('Lazy loading initialized for older browsers');
}

// Initialize lazy loading
document.addEventListener('DOMContentLoaded', initLazyLoading);

/**
 * Export functions for external use
 */
window.GroundingArticlePage = {
    debounce,
    throttle
};