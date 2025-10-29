/**
 * Header Module - версия с фиксами overlay и кнопки Каталог
 * @file js/modules/header.js
 * @author KW
 * @version 1.1.0
 * @URI https://kowb.ru
 */

export class HeaderModule {
    constructor() {
        this.header = document.querySelector('.edsys-header');
        this.catalogBtn = document.querySelector('.edsys-header__catalog-btn');
        this.megaMenu = document.querySelector('#megaMenu');
        this.overlay = document.querySelector('#overlay');
        this.searchForm = document.querySelector('.edsys-header__search-form');

        // States
        this.isMenuOpen = false;
        this.isMobileMenuOpen = false;
        this.isMobileCatalogOpen = false;
        this.hideTimeout = null; // Один таймаут для всего

        // Variables for header sticky functionality
        this.lastScrollTop = 0;
        this.headerHeight = 0;
        this.isScrolling = false;
        this.scrollThreshold = 100;
        this.isHeaderSticky = false;

        this.init();
    }

    init() {
        if (!this.header) return;

        this.initMegaMenu();
        this.initSearch();
        this.initStickyHeader();
        this.initDropdownMenus();
        this.initMobileMenus();
        this.initOverlay();

        this.updateHeaderHeight();
        window.addEventListener('resize', () => this.onResize());

        console.log('HeaderModule initialized');
    }

    /**
     * Initialize mega menu - УПРОЩЕННАЯ версия без hover bridge
     */
    initMegaMenu() {
        if (!this.catalogBtn || !this.megaMenu) return;

        // Создаем общий контейнер для hover-области
        const hoverZone = document.createElement('div');
        hoverZone.id = 'megaMenuHoverZone';
        hoverZone.style.cssText = `
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 200px;
            z-index: 1000;
            pointer-events: none;
            display: none;
        `;
        document.body.appendChild(hoverZone);

        // Объединяем кнопку и меню в одну hover-зону
        const elements = [this.catalogBtn, this.megaMenu, hoverZone];

        elements.forEach(el => {
            if (!el) return;

            el.addEventListener('mouseenter', () => {
                this.clearHideTimeout();
                if (!this.isMenuOpen) {
                    this.showMegaMenu();
                }
            });

            el.addEventListener('mouseleave', () => {
                this.scheduleHide();
            });
        });

        // Click handler - для десктопа только при открытом меню, иначе переход по ссылке
        this.catalogBtn.addEventListener('click', (e) => {
            // На мобильных всегда открываем меню
            if (window.innerWidth <= 768) {
                e.preventDefault();
                if (this.isMenuOpen) {
                    this.hideMegaMenu();
                } else {
                    this.showMegaMenu();
                }
                return;
            }
            
            // На десктопе: если меню открыто, закрываем и блокируем переход
            if (this.isMenuOpen) {
                e.preventDefault();
                this.hideMegaMenu();
            }
            // Если меню закрыто, разрешаем переход по ссылке (не preventDefault)
        });

        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.hideMegaMenu();
            }
        });

        // Закрытие при клике вне меню
        document.addEventListener('click', (e) => {
            if (!this.catalogBtn.contains(e.target) &&
                !this.megaMenu.contains(e.target) &&
                this.isMenuOpen) {
                this.hideMegaMenu();
            }
        });
    }

    /**
     * Показать мегаменю - УПРОЩЕННАЯ версия
     */
    showMegaMenu() {
        if (this.isMenuOpen) return;

        this.clearHideTimeout();
        this.updateMegaMenuPosition();

        // Принудительный reflow
        this.megaMenu.offsetHeight;

        this.isMenuOpen = true;
        this.megaMenu.classList.add('active');

        // Показываем оверлей только если нужно
        if (this.overlay && window.innerWidth > 768) {
            this.overlay.classList.add('active');
        }

        // Активируем hover-зону
        const hoverZone = document.getElementById('megaMenuHoverZone');
        if (hoverZone) {
            const catalogRect = this.catalogBtn.getBoundingClientRect();
            hoverZone.style.cssText = `
                position: fixed;
                top: ${catalogRect.top}px;
                left: 0;
                right: 0;
                height: ${window.innerHeight - catalogRect.top}px;
                z-index: 1003;
                pointer-events: auto;
                display: block;
            `;
        }
    }

    /**
     * Обновить позицию мегаменю
     */
    updateMegaMenuPosition() {
        const catalogRect = this.catalogBtn.getBoundingClientRect();
        const topPosition = catalogRect.bottom + window.pageYOffset + 5;

        this.megaMenu.style.cssText = `
            position: absolute !important;
            top: ${topPosition}px !important;
            left: 50% !important;
            z-index: 1004 !important;
        `;
    }

    /**
     * Запланировать скрытие меню
     */
    scheduleHide() {
        this.clearHideTimeout();
        this.hideTimeout = setTimeout(() => {
            this.hideMegaMenu();
        }, 300);
    }

    /**
     * Отменить скрытие меню
     */
    clearHideTimeout() {
        if (this.hideTimeout) {
            clearTimeout(this.hideTimeout);
            this.hideTimeout = null;
        }
    }

    /**
     * Скрыть мегаменю - УПРОЩЕННАЯ версия
     */
    hideMegaMenu() {
        if (!this.isMenuOpen) return;

        this.clearHideTimeout();
        this.isMenuOpen = false;
        this.megaMenu.classList.remove('active');

        // Скрываем оверлей только если больше ничего не открыто
        if (this.overlay && !this.isMobileMenuOpen && !this.isMobileCatalogOpen) {
            this.overlay.classList.remove('active');
        }

        // Деактивируем hover-зону
        const hoverZone = document.getElementById('megaMenuHoverZone');
        if (hoverZone) {
            hoverZone.style.display = 'none';
            hoverZone.style.pointerEvents = 'none';
        }
    }

    /**
     * Initialize sticky header
     */
    initStickyHeader() {
        if (window.innerWidth <= 768) return;

        const handleScroll = () => {
            if (!this.isScrolling) {
                window.requestAnimationFrame(() => {
                    this.updateStickyHeader();
                    this.isScrolling = false;
                });
                this.isScrolling = true;
            }
        };

        window.addEventListener('scroll', handleScroll, { passive: true });
    }

    /**
     * Update sticky header state
     */
    updateStickyHeader() {
        if (window.innerWidth <= 768) {
            this.removeStickyHeader();
            return;
        }

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const scrollDirection = scrollTop > this.lastScrollTop ? 'down' : 'up';

        if (scrollTop > this.scrollThreshold) {
            if (scrollDirection === 'up' && !this.isHeaderSticky) {
                this.showStickyHeader();
            } else if (scrollDirection === 'down' && scrollTop > this.headerHeight * 2) {
                this.hideStickyHeader();
            } else if (scrollDirection === 'up') {
                this.showStickyHeader();
            }
        } else if (scrollTop <= this.scrollThreshold) {
            this.removeStickyHeader();
        }

        this.lastScrollTop = scrollTop;
    }

    /**
     * Show sticky header
     */
    showStickyHeader() {
        if (!this.isHeaderSticky) {
            this.header.classList.add('edsys-header--sticky');
            document.body.classList.add('edsys-header-sticky-compensate');
            document.documentElement.style.setProperty('--header-height', `${this.headerHeight}px`);
            this.isHeaderSticky = true;

            if (this.isMenuOpen) {
                this.updateMegaMenuPosition();
            }
        }
        this.header.classList.remove('edsys-header--hidden');
    }

    /**
     * Hide sticky header
     */
    hideStickyHeader() {
        if (this.isHeaderSticky) {
            this.header.classList.add('edsys-header--hidden');
            if (this.isMenuOpen) {
                this.hideMegaMenu();
            }
        }
    }

    /**
     * Remove sticky header
     */
    removeStickyHeader() {
        this.header.classList.remove('edsys-header--sticky', 'edsys-header--hidden');
        document.body.classList.remove('edsys-header-sticky-compensate');
        this.isHeaderSticky = false;

        if (this.isMenuOpen) {
            this.updateMegaMenuPosition();
        }
    }

    /**
     * Update header height
     */
    updateHeaderHeight() {
        this.headerHeight = this.header.offsetHeight;
    }

    /**
     * Initialize search
     */
    initSearch() {
        if (!this.searchForm) return;

        this.searchForm.addEventListener('submit', (e) => {
            const searchInput = e.target.querySelector('input[type="search"]');
            const query = searchInput.value.trim();

            if (!query) {
                e.preventDefault();
                searchInput.focus();
                this.showSearchError('Введите поисковый запрос');
                return;
            }

            this.trackSearch(query);
        });

        this.initSearchAutocomplete();
    }

    /**
     * Show search error
     */
    showSearchError(message) {
        const searchInput = this.searchForm.querySelector('input[type="search"]');
        const existingError = this.searchForm.querySelector('.edsys-search-error');

        if (existingError) existingError.remove();

        searchInput.classList.add('error');

        const errorMsg = document.createElement('div');
        errorMsg.className = 'edsys-search-error';
        errorMsg.textContent = message;
        searchInput.parentElement.appendChild(errorMsg);

        setTimeout(() => {
            searchInput.classList.remove('error');
            if (errorMsg.parentElement) errorMsg.remove();
        }, 3000);
    }

    /**
     * Initialize search autocomplete
     */
    initSearchAutocomplete() {
        const searchInput = this.searchForm.querySelector('input[type="search"]');
        if (!searchInput) return;

        const suggestions = [
            'Дистрибьюторы питания', 'DMX сплиттеры', 'Пульты управления',
            'Рэковые панели', 'Сценические лючки', 'Туровые дистрибьюторы',
            'Рэковые дистрибьюторы', 'Пульты лебедочные', 'Диммеры', 'Секвенсоры'
        ];

        let autocompleteList = null;

        searchInput.addEventListener('input', (e) => {
            const value = e.target.value.toLowerCase();

            if (autocompleteList) {
                autocompleteList.remove();
                autocompleteList = null;
            }

            if (value.length < 2) return;

            const matches = suggestions.filter(s =>
                s.toLowerCase().includes(value)
            ).slice(0, 5);

            if (matches.length === 0) return;

            autocompleteList = document.createElement('ul');
            autocompleteList.className = 'edsys-search-autocomplete';

            matches.forEach(match => {
                const li = document.createElement('li');
                li.textContent = match;
                li.addEventListener('click', () => {
                    searchInput.value = match;
                    autocompleteList.remove();
                    autocompleteList = null;
                    this.searchForm.submit();
                });
                autocompleteList.appendChild(li);
            });

            searchInput.parentElement.appendChild(autocompleteList);
        });

        document.addEventListener('click', (e) => {
            if (!this.searchForm.contains(e.target) && autocompleteList) {
                autocompleteList.remove();
                autocompleteList = null;
            }
        });

        searchInput.addEventListener('blur', () => {
            setTimeout(() => {
                if (autocompleteList) {
                    autocompleteList.remove();
                    autocompleteList = null;
                }
            }, 200);
        });
    }

    /**
     * Initialize dropdown menus
     */
    initDropdownMenus() {
        const dropdowns = document.querySelectorAll('.edsys-header__nav-dropdown');

        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.edsys-header__nav-link--dropdown');
            const menu = dropdown.querySelector('.edsys-header__dropdown-menu');
            let timeout;

            if (!link || !menu) return;

            dropdown.addEventListener('mouseenter', () => {
                clearTimeout(timeout);
                menu.style.display = 'block';
                setTimeout(() => menu.classList.add('active'), 10);
            });

            dropdown.addEventListener('mouseleave', () => {
                menu.classList.remove('active');
                timeout = setTimeout(() => menu.style.display = 'none', 300);
            });

            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const isOpen = menu.classList.contains('active');

                    if (isOpen) {
                        menu.classList.remove('active');
                        setTimeout(() => menu.style.display = 'none', 300);
                    } else {
                        menu.style.display = 'block';
                        setTimeout(() => menu.classList.add('active'), 10);
                    }
                }
            });
        });
    }

    /**
     * Initialize mobile menus
     */
    initMobileMenus() {
        this.initMobileMenu();
        this.initMobileCatalog();
    }

    /**
     * Initialize mobile menu
     */
    initMobileMenu() {
        const mobileMenuBtn = document.querySelector('[data-action="toggle-menu"]');
        const mobileMenu = document.querySelector('#mobileMenu');
        const mobileMenuClose = document.querySelector('.edsys-mobile-menu__close');

        if (!mobileMenuBtn || !mobileMenu) return;

        mobileMenuBtn.addEventListener('click', () => this.toggleMobileMenu());

        if (mobileMenuClose) {
            mobileMenuClose.addEventListener('click', () => this.closeMobileMenu());
        }
    }

    /**
     * Toggle mobile menu
     */
    toggleMobileMenu() {
        if (this.isMobileMenuOpen) {
            this.closeMobileMenu();
        } else {
            this.openMobileMenu();
        }
    }

    /**
     * Open mobile menu
     */
    openMobileMenu() {
        const mobileMenu = document.querySelector('#mobileMenu');
        if (!mobileMenu) return;

        this.isMobileMenuOpen = true;
        mobileMenu.classList.add('active');

        if (this.overlay) {
            this.overlay.classList.add('active');
        }

        document.body.style.overflow = 'hidden';
    }

    /**
     * Close mobile menu
     */
    closeMobileMenu() {
        const mobileMenu = document.querySelector('#mobileMenu');
        if (!mobileMenu) return;

        this.isMobileMenuOpen = false;
        mobileMenu.classList.remove('active');

        if (!this.isMobileCatalogOpen && !this.isMenuOpen && this.overlay) {
            this.overlay.classList.remove('active');
        }

        if (!this.isMobileCatalogOpen) {
            document.body.style.overflow = '';
        }
    }

    /**
     * Initialize mobile catalog
     */
    initMobileCatalog() {
        const mobileCatalogBtns = document.querySelectorAll('[data-action="toggle-catalog"]');
        const mobileCatalog = document.querySelector('#mobileCatalog');
        const mobileCatalogClose = document.querySelector('.edsys-mobile-catalog__close');

        if (!mobileCatalogBtns.length || !mobileCatalog) return;

        mobileCatalogBtns.forEach(btn => {
            btn.addEventListener('click', () => this.toggleMobileCatalog());
        });

        if (mobileCatalogClose) {
            mobileCatalogClose.addEventListener('click', () => this.closeMobileCatalog());
        }
    }

    /**
     * Toggle mobile catalog
     */
    toggleMobileCatalog() {
        if (this.isMobileCatalogOpen) {
            this.closeMobileCatalog();
        } else {
            this.openMobileCatalog();
        }
    }

    /**
     * Open mobile catalog
     */
    openMobileCatalog() {
        const mobileCatalog = document.querySelector('#mobileCatalog');
        if (!mobileCatalog) return;

        this.isMobileCatalogOpen = true;
        mobileCatalog.classList.add('active');

        if (this.overlay) {
            this.overlay.classList.add('active');
        }

        document.body.style.overflow = 'hidden';
    }

    /**
     * Close mobile catalog
     */
    closeMobileCatalog() {
        const mobileCatalog = document.querySelector('#mobileCatalog');
        if (!mobileCatalog) return;

        this.isMobileCatalogOpen = false;
        mobileCatalog.classList.remove('active');

        if (!this.isMobileMenuOpen && !this.isMenuOpen && this.overlay) {
            this.overlay.classList.remove('active');
        }

        if (!this.isMobileMenuOpen) {
            document.body.style.overflow = '';
        }
    }

    /**
     * Initialize overlay - УПРОЩЕННАЯ версия
     */
    initOverlay() {
        if (!this.overlay) return;

        this.overlay.addEventListener('click', () => {
            this.hideMegaMenu();
            this.closeMobileMenu();
            this.closeMobileCatalog();
        });
    }

    /**
     * Track search analytics
     */
    trackSearch(query) {
        if (typeof gtag !== 'undefined') {
            gtag('event', 'search', {
                search_term: query,
                event_category: 'ecommerce',
                event_label: 'header_search'
            });
        }

        if (typeof ym !== 'undefined' && window.yaMetricaId) {
            ym(window.yaMetricaId, 'reachGoal', 'search', {
                search_query: query
            });
        }
    }

    /**
     * Handle window resize
     */
    onResize() {
        this.updateHeaderHeight();

        if (window.innerWidth <= 768) {
            this.removeStickyHeader();
            if (this.isMenuOpen) {
                this.hideMegaMenu();
            }
        } else {
            this.closeMobileMenu();
            this.closeMobileCatalog();

            if (!this.isHeaderSticky && window.pageYOffset > this.scrollThreshold) {
                this.updateStickyHeader();
            }
        }

        if (this.isMenuOpen) {
            this.updateMegaMenuPosition();
        }
    }

    /**
     * Public methods
     */
    closeMegaMenu() {
        this.hideMegaMenu();
    }

    isMegaMenuOpen() {
        return this.isMenuOpen;
    }

    isStickyHeaderActive() {
        return this.isHeaderSticky;
    }

    /**
     * Destroy module
     */
    destroy() {
        this.clearHideTimeout();

        // Удаляем hover-зону
        const hoverZone = document.getElementById('megaMenuHoverZone');
        if (hoverZone) hoverZone.remove();

        window.removeEventListener('scroll', this.updateStickyHeader);
        window.removeEventListener('resize', this.onResize);
        this.removeStickyHeader();

        console.log('HeaderModule destroyed');
    }
}