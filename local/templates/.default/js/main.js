/**
 * EDS Main JavaScript
 * Version: 1.2.4
 * Date: 2025-10-22
 * File: /local/templates/.default/js/main.js
 */

// Импорт существующих модулей
import { HeaderModule } from './modules/header.js';
import { CarouselModule } from './modules/carousel.js';
import { MobileMenuModule } from './modules/mobile-menu.js';

// Utility functions
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
    }
}

function isElementInViewport(el) {
    const rect = el.getBoundingClientRect();
    return (
        rect.top >= 0 &&
        rect.left >= 0 &&
        rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
        rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    );
}

// Main Application Class
class EdsysApp {
    constructor() {
        this.modules = {};
        this.isInitialized = false;
        this.init();
    }

    init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }

        window.addEventListener('load', () => this.onPageLoad());
    }

    onDOMReady() {
        if (this.isInitialized) return;

        try {
            this.initModules();
            this.initGlobalHandlers();
            this.initAnimations();
            this.initPerformanceOptimizations();

            this.isInitialized = true;

        } catch (error) {
            if (window.BX && window.BX.debug) {
                window.BX.debug('Error initializing EDS Application:', error);
            }
        }
    }

    onPageLoad() {
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.add('fade-out');
            setTimeout(() => preloader.remove(), 300);
        }

        Object.values(this.modules).forEach(module => {
            if (module.onPageLoad) module.onPageLoad();
        });
    }

    initModules() {
        try {
            // Header Module
            if (document.querySelector('.edsys-header')) {
                this.modules.header = new HeaderModule();
            }

            // Footer Module
            if (document.querySelector('.edsys-footer')) {
                if (window.edsFooter) {
                    this.modules.footer = window.edsFooter;
                } else if (window.FooterModule) {
                    this.modules.footer = new window.FooterModule();
                }
            }

            // Carousel Module
            if (document.querySelector('.edsys-carousel') || document.getElementById('servicesContainer')) {
                this.modules.carousel = new CarouselModule();
            }

            // Mobile Menu Module
            if (document.getElementById('mobileMenu') || document.getElementById('mobileCatalog')) {
                this.modules.mobileMenu = new MobileMenuModule();
            }

        } catch (error) {
            if (window.BX && window.BX.debug) {
                window.BX.debug('Error initializing modules:', error);
            }
        }
    }

    initGlobalHandlers() {
        this.initSmoothScroll();
        this.handleResize();
        this.initKeyboardNavigation();
    }

    initSmoothScroll() {
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a[href^="#"]');
            if (!link) return;

            const targetId = link.getAttribute('href');
            if (targetId === '#') return;

            const target = document.querySelector(targetId);
            if (!target) return;

            e.preventDefault();

            const headerHeight = document.querySelector('.edsys-header')?.offsetHeight || 0;
            const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

            window.scrollTo({
                top: targetPosition,
                behavior: 'smooth'
            });
        });
    }

    handleResize() {
        const handleResizeDebounced = debounce(() => {
            this.updateScreenInfo();

            Object.values(this.modules).forEach(module => {
                if (module.onResize) module.onResize();
            });
        }, 250);

        window.addEventListener('resize', handleResizeDebounced);
        this.updateScreenInfo();
    }

    updateScreenInfo() {
        const width = window.innerWidth;
        const height = window.innerHeight;

        document.documentElement.style.setProperty('--viewport-width', `${width}px`);
        document.documentElement.style.setProperty('--viewport-height', `${height}px`);

        const deviceType = width < 768 ? 'mobile' : width < 1024 ? 'tablet' : 'desktop';
        document.documentElement.setAttribute('data-device', deviceType);
    }

    initKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                this.closeAllModals();
            }

            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-nav');
            }
        });

        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-nav');
        });
    }

    closeAllModals() {
        if (this.modules.header && this.modules.header.closeMegaMenu) {
            this.modules.header.closeMegaMenu();
        }

        if (this.modules.mobileMenu && this.modules.mobileMenu.closeAll) {
            this.modules.mobileMenu.closeAll();
        }

        const mobileMenu = document.getElementById('mobileMenu');
        const mobileCatalog = document.getElementById('mobileCatalog');
        const overlay = document.getElementById('overlay');

        if (mobileMenu) mobileMenu.classList.remove('active');
        if (mobileCatalog) mobileCatalog.classList.remove('active');
        if (overlay) overlay.classList.remove('active');
        document.body.style.overflow = '';
    }

    initAnimations() {
        if (!('IntersectionObserver' in window)) {
            return;
        }

        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible', 'animate-in');

                    if (entry.target.hasAttribute('data-animate-once')) {
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, observerOptions);

        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });

        document.querySelectorAll('.edsys-advantage-card, .edsys-wizard__header').forEach(el => {
            observer.observe(el);
        });
    }

    initPerformanceOptimizations() {
        if (!('loading' in HTMLImageElement.prototype)) {
            this.lazyLoadImages();
        }

        this.optimizeVideos();
        this.preloadCriticalAssets();
    }

    lazyLoadImages() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        if (!images.length) return;

        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                    }
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    optimizeVideos() {
        const videos = document.querySelectorAll('video');
        if (!videos.length) return;

        const videoObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const video = entry.target;
                if (entry.isIntersecting) {
                    video.play().catch(() => {});
                } else {
                    video.pause();
                }
            });
        });

        videos.forEach(video => videoObserver.observe(video));
    }

    preloadCriticalAssets() {
        const criticalImages = [
            '/local/templates/.default/images/logo.svg'
        ];

        criticalImages.forEach(imagePath => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = imagePath;
            document.head.appendChild(link);
        });
    }

    // Public API
    getModule(name) {
        return this.modules[name];
    }

    isModuleLoaded(name) {
        return !!this.modules[name];
    }

    getCarouselModule() {
        return this.modules.carousel;
    }

    getHeaderModule() {
        return this.modules.header;
    }

    getFooterModule() {
        return this.modules.footer;
    }

    getMobileMenuModule() {
        return this.modules.mobileMenu;
    }
}

// Initialize application
const app = new EdsysApp();

// Global access
window.EdsysApp = app;
window.EDSMainLoaded = true;

// Compatibility with existing scripts
window.carouselModule = app.getCarouselModule();

// Export utilities
export { debounce, throttle, isElementInViewport };