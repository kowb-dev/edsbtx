/**
 * Mobile Menu Module
 * Управление мобильным меню и каталогом
 */

export class MobileMenuModule {
    constructor() {
        this.mobileMenu = document.getElementById('mobileMenu');
        this.mobileCatalog = document.getElementById('mobileCatalog');
        this.overlay = document.getElementById('overlay');
        this.body = document.body;
        this.isMenuOpen = false;
        this.isCatalogOpen = false;

        this.init();
    }

    init() {
        this.initMenuToggle();
        this.initCatalogToggle();
        this.initOverlay();
        this.initSwipeGestures();
        this.observeMenuState();
    }

    initMenuToggle() {
        // Кнопка открытия/закрытия меню
        const menuToggleButtons = document.querySelectorAll('[data-action="toggle-menu"]');

        menuToggleButtons.forEach(button => {
            button.addEventListener('click', () => this.toggleMenu());
        });

        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isMenuOpen) {
                this.closeMenu();
            }
        });
    }

    initCatalogToggle() {
        // Кнопки открытия/закрытия каталога
        const catalogToggleButtons = document.querySelectorAll('[data-action="toggle-catalog"]');

        catalogToggleButtons.forEach(button => {
            button.addEventListener('click', () => this.toggleCatalog());
        });

        // Закрытие по Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.isCatalogOpen) {
                this.closeCatalog();
            }
        });
    }

    initOverlay() {
        if (!this.overlay) return;

        this.overlay.addEventListener('click', () => {
            this.closeAll();
        });
    }

    initSwipeGestures() {
        // Свайп для закрытия меню
        this.addSwipeGesture(this.mobileMenu, 'right', () => this.closeMenu());

        // Свайп для закрытия каталога
        this.addSwipeGesture(this.mobileCatalog, 'left', () => this.closeCatalog());
    }

    addSwipeGesture(element, direction, callback) {
        if (!element) return;

        let startX = 0;
        let startY = 0;
        let distX = 0;
        let distY = 0;
        let startTime = 0;
        const threshold = 100; // Минимальное расстояние для свайпа
        const restraint = 100; // Максимальное отклонение по перпендикулярной оси
        const allowedTime = 300; // Максимальное время для свайпа

        element.addEventListener('touchstart', (e) => {
            const touchobj = e.changedTouches[0];
            startX = touchobj.pageX;
            startY = touchobj.pageY;
            startTime = new Date().getTime();
        }, { passive: true });

        element.addEventListener('touchend', (e) => {
            const touchobj = e.changedTouches[0];
            distX = touchobj.pageX - startX;
            distY = touchobj.pageY - startY;
            const elapsedTime = new Date().getTime() - startTime;

            if (elapsedTime <= allowedTime) {
                if (Math.abs(distX) >= threshold && Math.abs(distY) <= restraint) {
                    if ((direction === 'right' && distX > 0) || (direction === 'left' && distX < 0)) {
                        callback();
                    }
                }
            }
        }, { passive: true });
    }

    toggleMenu() {
        if (this.isMenuOpen) {
            this.closeMenu();
        } else {
            this.openMenu();
        }
    }

    openMenu() {
        if (!this.mobileMenu) return;

        this.isMenuOpen = true;
        this.mobileMenu.classList.add('active');
        this.showOverlay();
        this.lockBody();

        // Устанавливаем фокус на первый элемент меню
        setTimeout(() => {
            const firstLink = this.mobileMenu.querySelector('a, button');
            if (firstLink) firstLink.focus();
        }, 300);

        // Запускаем событие
        this.mobileMenu.dispatchEvent(new CustomEvent('menuOpen'));
    }

    closeMenu() {
        if (!this.mobileMenu) return;

        this.isMenuOpen = false;
        this.mobileMenu.classList.remove('active');

        if (!this.isCatalogOpen) {
            this.hideOverlay();
            this.unlockBody();
        }

        // Запускаем событие
        this.mobileMenu.dispatchEvent(new CustomEvent('menuClose'));
    }

    toggleCatalog() {
        if (this.isCatalogOpen) {
            this.closeCatalog();
        } else {
            this.openCatalog();
        }
    }

    openCatalog() {
        if (!this.mobileCatalog) return;

        this.isCatalogOpen = true;
        this.mobileCatalog.classList.add('active');
        this.showOverlay();
        this.lockBody();

        // Устанавливаем фокус на заголовок
        setTimeout(() => {
            const header = this.mobileCatalog.querySelector('h3');
            if (header) header.focus();
        }, 300);

        // Запускаем событие
        this.mobileCatalog.dispatchEvent(new CustomEvent('catalogOpen'));
    }

    closeCatalog() {
        if (!this.mobileCatalog) return;

        this.isCatalogOpen = false;
        this.mobileCatalog.classList.remove('active');

        if (!this.isMenuOpen) {
            this.hideOverlay();
            this.unlockBody();
        }

        // Запускаем событие
        this.mobileCatalog.dispatchEvent(new CustomEvent('catalogClose'));
    }

    closeAll() {
        this.closeMenu();
        this.closeCatalog();
    }

    showOverlay() {
        if (!this.overlay) return;

        this.overlay.classList.add('active');
    }

    hideOverlay() {
        if (!this.overlay) return;

        this.overlay.classList.remove('active');
    }

    lockBody() {
        // Сохраняем текущую позицию скролла
        const scrollY = window.scrollY;

        this.body.style.position = 'fixed';
        this.body.style.top = `-${scrollY}px`;
        this.body.style.width = '100%';
        this.body.setAttribute('data-scroll-lock', scrollY);
    }

    unlockBody() {
        const scrollY = this.body.getAttribute('data-scroll-lock');

        this.body.style.position = '';
        this.body.style.top = '';
        this.body.style.width = '';

        if (scrollY) {
            window.scrollTo(0, parseInt(scrollY, 10));
            this.body.removeAttribute('data-scroll-lock');
        }
    }

    observeMenuState() {
        // Наблюдаем за изменением состояния меню для управления скроллом
        const observer = new MutationObserver(() => {
            const isAnyMenuOpen = this.isMenuOpen || this.isCatalogOpen;

            if (isAnyMenuOpen) {
                this.lockBody();
            } else {
                this.unlockBody();
            }
        });

        if (this.mobileMenu) {
            observer.observe(this.mobileMenu, {
                attributes: true,
                attributeFilter: ['class']
            });
        }

        if (this.mobileCatalog) {
            observer.observe(this.mobileCatalog, {
                attributes: true,
                attributeFilter: ['class']
            });
        }
    }

    // Публичные методы для внешнего управления
    isOpen() {
        return this.isMenuOpen || this.isCatalogOpen;
    }

    getOpenMenu() {
        if (this.isMenuOpen) return 'menu';
        if (this.isCatalogOpen) return 'catalog';
        return null;
    }

    // Анимация пунктов меню
    animateMenuItems() {
        const menuItems = this.mobileMenu.querySelectorAll('li');

        menuItems.forEach((item, index) => {
            item.style.opacity = '0';
            item.style.transform = 'translateX(-20px)';

            setTimeout(() => {
                item.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                item.style.opacity = '1';
                item.style.transform = 'translateX(0)';
            }, index * 50);
        });
    }

    // Обработка изменения ориентации экрана
    handleOrientationChange() {
        // Закрываем меню при изменении ориентации для лучшего UX
        if (window.matchMedia('(max-width: 768px)').matches) {
            setTimeout(() => {
                this.closeAll();
            }, 300);
        }
    }

    onResize() {
        // Закрываем мобильное меню на десктопе
        if (window.innerWidth > 768 && this.isOpen()) {
            this.closeAll();
        }
    }
}

// Добавляем обработчик изменения ориентации
window.addEventListener('orientationchange', () => {
    const mobileMenu = window.EdsysApp?.modules?.mobileMenu;
    if (mobileMenu) {
        mobileMenu.handleOrientationChange();
    }
});