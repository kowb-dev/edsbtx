 /**
 * EDS Main JavaScript
 * Модульная структура для лучшей организации и производительности
 */

// Импорт модулей
import { HeaderModule } from './modules/header.js';
import { CarouselModule } from './modules/carousel.js';
import { MobileMenuModule } from './modules/mobile-menu.js';
import { FooterModule } from './modules/footer.js';
import { debounce, throttle, isElementInViewport } from './utils.js';

// Главный класс приложения
class EdsysApp {
    constructor() {
        this.modules = {};
        this.init();
    }

    init() {
        // Ожидаем загрузки DOM
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => this.onDOMReady());
        } else {
            this.onDOMReady();
        }

        // Обработка загрузки всех ресурсов
        window.addEventListener('load', () => this.onPageLoad());
    }

    onDOMReady() {
        // Инициализация модулей
        this.initModules();

        // Глобальные обработчики событий
        this.initGlobalHandlers();

        // Инициализация анимаций
        this.initAnimations();

        // Оптимизация производительности
        this.initPerformanceOptimizations();
    }

    onPageLoad() {
        // Удаляем прелоадер если есть
        const preloader = document.getElementById('preloader');
        if (preloader) {
            preloader.classList.add('fade-out');
            setTimeout(() => preloader.remove(), 300);
        }
    }

    initModules() {
        // Инициализация модуля шапки
        this.modules.header = new HeaderModule();

        // Инициализация каруселей
        this.modules.carousel = new CarouselModule();

        // Инициализация мобильного меню
        this.modules.mobileMenu = new MobileMenuModule();

        // Инициализация футера
        this.modules.footer = new FooterModule();
    }

    initGlobalHandlers() {
        // Плавный скролл для якорных ссылок
        this.initSmoothScroll();

        // Обработка изменения размера окна
        this.handleResize();

        // Обработка клавиатурной навигации
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
        let resizeTimer;
        const handleResizeDebounced = debounce(() => {
            // Обновляем информацию об экране
            this.updateScreenInfo();

            // Сообщаем модулям об изменении размера
            Object.values(this.modules).forEach(module => {
                if (module.onResize) module.onResize();
            });
        }, 250);

        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(handleResizeDebounced, 250);
        });

        // Первоначальный вызов
        this.updateScreenInfo();
    }

    updateScreenInfo() {
        const width = window.innerWidth;
        const height = window.innerHeight;

        // Сохраняем информацию о размере экрана
        document.documentElement.style.setProperty('--viewport-width', `${width}px`);
        document.documentElement.style.setProperty('--viewport-height', `${height}px`);

        // Определяем тип устройства
        const deviceType = width < 768 ? 'mobile' : width < 1024 ? 'tablet' : 'desktop';
        document.documentElement.setAttribute('data-device', deviceType);
    }

    initKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // Escape закрывает все модальные окна
            if (e.key === 'Escape') {
                this.closeAllModals();
            }

            // Tab navigation improvements
            if (e.key === 'Tab') {
                document.body.classList.add('keyboard-nav');
            }
        });

        // Убираем индикатор клавиатурной навигации при клике мышью
        document.addEventListener('mousedown', () => {
            document.body.classList.remove('keyboard-nav');
        });
    }

    closeAllModals() {
        // Закрываем мобильные меню
        if (this.modules.mobileMenu) {
            this.modules.mobileMenu.closeAll();
        }

        // Закрываем мега-меню
        if (this.modules.header) {
            this.modules.header.closeMegaMenu();
        }
    }

    initAnimations() {
        // Intersection Observer для анимаций при скролле
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');

                    // Для элементов с одноразовой анимацией
                    if (entry.target.hasAttribute('data-animate-once')) {
                        observer.unobserve(entry.target);
                    }
                }
            });
        }, observerOptions);

        // Наблюдаем за элементами с анимацией
        document.querySelectorAll('[data-animate]').forEach(el => {
            observer.observe(el);
        });

        // Анимация футера
        document.querySelectorAll('.edsys-footer__section, .edsys-footer__brand, .edsys-footer__newsletter').forEach(el => {
            observer.observe(el);
        });
    }

    initPerformanceOptimizations() {
        // Lazy loading для изображений (если не поддерживается нативно)
        if ('loading' in HTMLImageElement.prototype) {
            // Браузер поддерживает lazy loading
        } else {
            // Подключаем полифилл
            this.lazyLoadImages();
        }

        // Оптимизация видео
        this.optimizeVideos();

        // Предзагрузка критических ресурсов
        this.preloadCriticalAssets();
    }

    lazyLoadImages() {
        const images = document.querySelectorAll('img[loading="lazy"]');
        const imageObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    img.src = img.dataset.src || img.src;
                    img.classList.add('loaded');
                    imageObserver.unobserve(img);
                }
            });
        });

        images.forEach(img => imageObserver.observe(img));
    }

    optimizeVideos() {
        // Приостанавливаем видео вне области видимости
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
        // Предзагрузка важных изображений
        const criticalImages = [
            './img/logo.svg',
            './img/hero-vid.jpg'
        ];

        criticalImages.forEach(src => {
            const link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = src;
            document.head.appendChild(link);
        });
    }
}

// Инициализация приложения
const app = new EdsysApp();

// Экспорт для использования в других модулях
window.EdsysApp = app;