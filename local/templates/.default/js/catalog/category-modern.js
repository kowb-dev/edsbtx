/**
 * Modern Category Page JavaScript
 * Управление фильтрами, сортировкой и загрузкой товаров
 */

class EdsysCategoryModern {
    constructor() {
        this.currentPage = 1;
        this.isLoading = false;
        this.hasMoreProducts = true;
        this.filters = {};
        this.sort = 'name_asc';
        this.viewMode = 'grid';
        this.productsContainer = document.getElementById('productsGrid');
        this.loadMoreBtn = document.getElementById('loadMoreBtn');
        this.productsCount = document.getElementById('productsCount');

        this.init();
    }

    init() {
        this.bindEvents();
        this.initializeFilters();
        this.loadProducts(true); // Первоначальная загрузка
    }

    bindEvents() {
        // Фильтры
        document.addEventListener('change', (e) => {
            if (e.target.matches('input[type="checkbox"]') ||
                e.target.matches('input[type="number"]')) {
                this.handleFilterChange();
            }
        });

        // Сортировка
        const sortSelect = document.getElementById('sortSelect');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.sort = e.target.value;
                this.resetAndReload();
            });
        }

        // Переключение вида
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.addEventListener('click', (e) => {
                this.switchView(e.target.closest('.view-btn').dataset.view);
            });
        });

        // Загрузить еще
        if (this.loadMoreBtn) {
            this.loadMoreBtn.addEventListener('click', () => {
                this.loadProducts(false);
            });
        }

        // Сброс фильтров
        const resetBtn = document.getElementById('resetFilters');
        if (resetBtn) {
            resetBtn.addEventListener('click', () => {
                this.resetFilters();
            });
        }

        // Сворачивание групп фильтров
        document.querySelectorAll('.edsys-filter-modern__group-header').forEach(header => {
            header.addEventListener('click', () => {
                const group = header.closest('.edsys-filter-modern__group');
                group.classList.toggle('collapsed');
            });
        });

        // Бесконечная прокрутка (альтернатива кнопке)
        this.initInfiniteScroll();
    }

    initializeFilters() {
        // Восстанавливаем фильтры из URL
        const urlParams = new URLSearchParams(window.location.search);

        // Восстанавливаем чекбоксы
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            const paramName = checkbox.name.replace('[]', '');
            const values = urlParams.getAll(paramName);
            if (values.includes(checkbox.value)) {
                checkbox.checked = true;
            }
        });

        // Восстанавливаем цены
        const priceFrom = urlParams.get('price_from');
        const priceTo = urlParams.get('price_to');
        if (priceFrom) document.querySelector('input[name="price_from"]').value = priceFrom;
        if (priceTo) document.querySelector('input[name="price_to"]').value = priceTo;

        this.collectFilters();
    }

    collectFilters() {
        this.filters = {};

        // Собираем чекбоксы
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(checkbox => {
            const name = checkbox.name.replace('[]', '');
            if (!this.filters[name]) this.filters[name] = [];
            this.filters[name].push(checkbox.value);
        });

        // Собираем цены
        const priceFrom = document.querySelector('input[name="price_from"]').value;
        const priceTo = document.querySelector('input[name="price_to"]').value;

        if (priceFrom) this.filters.price_from = priceFrom;
        if (priceTo) this.filters.price_to = priceTo;
    }

    handleFilterChange() {
        this.collectFilters();
        this.updateURL();
        this.resetAndReload();
        this.toggleResetButton();
    }

    resetFilters() {
        // Снимаем все чекбоксы
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

        // Очищаем поля цен
        document.querySelectorAll('input[type="number"]').forEach(input => input.value = '');

        this.filters = {};
        this.resetAndReload();
        this.toggleResetButton();

        // Очищаем URL
        window.history.replaceState({}, '', window.location.pathname);
    }

    toggleResetButton() {
        const resetBtn = document.getElementById('resetFilters');
        const hasFilters = Object.keys(this.filters).length > 0;

        if (resetBtn) {
            resetBtn.style.display = hasFilters ? 'flex' : 'none';
        }
    }

    switchView(view) {
        this.viewMode = view;

        // Обновляем кнопки
        document.querySelectorAll('.view-btn').forEach(btn => {
            btn.classList.toggle('active', btn.dataset.view === view);
        });

        // Обновляем контейнер
        this.productsContainer.setAttribute('data-view', view);

        // Сохраняем предпочтение
        localStorage.setItem('edsys-view-mode', view);
    }

    resetAndReload() {
        this.currentPage = 1;
        this.hasMoreProducts = true;
        this.productsContainer.innerHTML = '';
        this.loadProducts(true);
    }

    async loadProducts(isFirstLoad = false) {
        if (this.isLoading || (!this.hasMoreProducts && !isFirstLoad)) {
            return;
        }

        this.isLoading = true;

        if (isFirstLoad) {
            this.showInitialLoader();
        } else {
            this.showLoadMoreLoader();
        }

        try {
            const params = new URLSearchParams({
                page: this.currentPage,
                per_page: window.PRODUCTS_PER_PAGE || 20,
                sort: this.sort,
                section_id: window.SECTION_ID || '',
                ...this.filters
            });

            const response = await fetch('/local/ajax/load-products-modern.php?' + params);
            const data = await response.json();

            if (data.success) {
                this.renderProducts(data.products, isFirstLoad);
                this.updateProductsCount(data.total_count);

                // Проверяем, есть ли еще товары
                this.hasMoreProducts = data.products.length === (window.PRODUCTS_PER_PAGE || 20);
                this.currentPage++;

                // Показываем/скрываем кнопку "Загрузить еще"
                if (this.loadMoreBtn) {
                    this.loadMoreBtn.style.display = this.hasMoreProducts ? 'flex' : 'none';
                }
            } else {
                this.showError(data.message || 'Ошибка загрузки товаров');
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError('Ошибка соединения с сервером');
        } finally {
            this.isLoading = false;
            this.hideLoaders();
        }
    }

    renderProducts(products, replace = false) {
        if (replace) {
            this.productsContainer.innerHTML = '';
        }

        if (products.length === 0 && replace) {
            this.showEmptyState();
            return;
        }

        const fragment = document.createDocumentFragment();

        products.forEach((product, index) => {
            const productElement = this.createProductElement(product);
            fragment.appendChild(productElement);

            // Анимация появления
            setTimeout(() => {
                productElement.style.opacity = '1';
                productElement.style.transform = 'translateY(0)';
            }, index * 50);
        });

        this.productsContainer.appendChild(fragment);
    }

    createProductElement(product) {
        const article = document.createElement('article');
        article.className = 'edsys-product-modern';
        article.style.opacity = '0';
        article.style.transform = 'translateY(20px)';
        article.style.transition = 'opacity 0.3s ease, transform 0.3s ease';

        const isAuthorized = window.USER_AUTHORIZED;
        const imageUrl = product.image || '/local/templates/.default/images/no-photo.jpg';

        article.innerHTML = `
            <div class="edsys-product-modern__image-wrapper">
                <img src="${imageUrl}"
                     alt="${this.escapeHtml(product.name)}"
                     class="edsys-product-modern__image"
                     width="280" height="280" loading="lazy">

                <div class="edsys-product-modern__actions">
                    <button class="edsys-product-modern__action"
                            data-action="compare"
                            data-product-id="${product.id}"
                            title="Добавить в сравнение">
                        <i class="ph ph-thin ph-chart-bar"></i>
                    </button>

                    <button class="edsys-product-modern__action"
                            data-action="favorite"
                            data-product-id="${product.id}"
                            title="Добавить в избранное">
                        <i class="ph ph-thin ph-heart"></i>
                    </button>
                </div>
            </div>

            <div class="edsys-product-modern__content">
                <h3 class="edsys-product-modern__title">
                    <a href="${product.detail_url}">${this.escapeHtml(product.name)}</a>
                </h3>

                ${product.article ? `
                <div class="edsys-product-modern__article">
                    Артикул: ${product.article}
                </div>
                ` : ''}

                ${product.description ? `
                <div class="edsys-product-modern__description">
                    ${product.description}
                </div>
                ` : ''}

                <div class="edsys-product-modern__specs">
                    ${product.specs ? product.specs.split(';').map(spec =>
                        `<div>${spec}</div>`
                    ).join('') : ''}
                </div>

                ${isAuthorized ? `
                <div class="edsys-product-modern__availability edsys-product-modern__availability--${product.available ? 'available' : 'preorder'}">
                    <i class="ph ph-thin ph-${product.available ? 'check-circle' : 'clock'}"></i>
                    ${product.available ? 'В наличии' : 'Предзаказ'}
                </div>

                <div class="edsys-product-modern__prices">
                    ${product.retail_price ? `
                    <div class="edsys-product-modern__price edsys-product-modern__price--retail">
                        <span class="edsys-product-modern__price-label">Розничная цена</span>
                        <span class="edsys-product-modern__price-value">${product.retail_price}</span>
                    </div>
                    ` : ''}

                    ${product.user_price ? `
                    <div class="edsys-product-modern__price edsys-product-modern__price--user">
                        <span class="edsys-product-modern__price-label">Ваша цена</span>
                        <span class="edsys-product-modern__price-value">${product.user_price}</span>
                    </div>
                    ` : ''}
                </div>
                ` : ''}

                <div class="edsys-product-modern__footer">
                    ${isAuthorized && product.available ? `
                    <button class="edsys-product-modern__btn edsys-product-modern__btn--primary"
                            data-action="buy"
                            data-product-id="${product.id}">
                        <i class="ph ph-thin ph-shopping-cart"></i>
                        В корзину
                    </button>
                    ` : `
                    <a href="${product.detail_url}"
                       class="edsys-product-modern__btn edsys-product-modern__btn--secondary">
                        Подробнее
                    </a>
                    `}
                </div>
            </div>
        `;

        // Привязываем обработчики
        this.bindProductEvents(article);

        return article;
    }

    bindProductEvents(productElement) {
        productElement.addEventListener('click', (e) => {
            const action = e.target.closest('[data-action]');
            if (!action) return;

            e.preventDefault();

            const actionType = action.dataset.action;
            const productId = action.dataset.productId;

            switch (actionType) {
                case 'buy':
                    this.addToCart(productId);
                    break;
                case 'compare':
                    this.toggleCompare(productId, action);
                    break;
                case 'favorite':
                    this.toggleFavorite(productId, action);
                    break;
            }
        });
    }

    async addToCart(productId) {
        try {
            const response = await fetch('/local/ajax/add_to_basket.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    PRODUCT_ID: productId,
                    QUANTITY: 1
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                this.showNotification('Товар добавлен в корзину', 'success');
            } else {
                this.showNotification(data.message || 'Ошибка при добавлении товара', 'error');
            }
        } catch (error) {
            this.showNotification('Ошибка соединения', 'error');
        }
    }

    async toggleCompare(productId, button) {
        const isActive = button.classList.contains('active');

        try {
            const response = await fetch('/local/ajax/toggle_compare.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    PRODUCT_ID: productId,
                    ACTION: isActive ? 'remove' : 'add'
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                button.classList.toggle('active');
                this.showNotification(data.message, 'success');
            }
        } catch (error) {
            this.showNotification('Ошибка при работе со сравнением', 'error');
        }
    }

    async toggleFavorite(productId, button) {
        const isActive = button.classList.contains('active');

        try {
            const response = await fetch('/local/ajax/toggle_favorite.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    PRODUCT_ID: productId,
                    ACTION: isActive ? 'remove' : 'add'
                })
            });

            const data = await response.json();

            if (data.status === 'success') {
                button.classList.toggle('active');
                this.showNotification(data.message, 'success');
            }
        } catch (error) {
            this.showNotification('Ошибка при работе с избранным', 'error');
        }
    }

    updateProductsCount(count) {
        if (this.productsCount) {
            this.productsCount.textContent = count;
        }
    }

    showInitialLoader() {
        this.productsContainer.innerHTML = `
            <div class="edsys-category-modern__loading" id="initialLoader">
                <div class="loader-spinner"></div>
                <span>Загружаем товары...</span>
            </div>
        `;
    }

    showLoadMoreLoader() {
        if (this.loadMoreBtn) {
            this.loadMoreBtn.innerHTML = `
                <div class="loader-spinner" style="width: 1rem; height: 1rem; border-width: 1px;"></div>
                Загружаем...
            `;
            this.loadMoreBtn.disabled = true;
        }
    }

    hideLoaders() {
        const initialLoader = document.getElementById('initialLoader');
        if (initialLoader) {
            initialLoader.remove();
        }

        if (this.loadMoreBtn) {
            this.loadMoreBtn.innerHTML = `
                <i class="ph ph-thin ph-plus-circle"></i>
                Показать еще товары
            `;
            this.loadMoreBtn.disabled = false;
        }
    }

    showEmptyState() {
        this.productsContainer.innerHTML = `
            <div class="edsys-category-modern__loading">
                <i class="ph ph-thin ph-package" style="font-size: 3rem; color: var(--edsys-text-muted);"></i>
                <span>Товары не найдены</span>
                <small>Попробуйте изменить фильтры поиска</small>
            </div>
        `;
    }

    showError(message) {
        this.productsContainer.innerHTML = `
            <div class="edsys-category-modern__loading">
                <i class="ph ph-thin ph-warning-circle" style="font-size: 3rem; color: var(--edsys-primary);"></i>
                <span>Ошибка загрузки</span>
                <small>${message}</small>
                <button onclick="window.location.reload()" style="margin-top: 1rem; padding: 0.5rem 1rem; border: 1px solid var(--edsys-primary); background: transparent; color: var(--edsys-primary); border-radius: 0.25rem; cursor: pointer;">
                    Обновить страницу
                </button>
            </div>
        `;
    }

    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `edsys-notification edsys-notification--${type}`;
        notification.innerHTML = `
            <div class="edsys-notification__content">
                <i class="ph ph-thin ph-${type === 'success' ? 'check-circle' : 'warning-circle'}"></i>
                <span>${message}</span>
            </div>
        `;

        notification.style.cssText = `
            position: fixed;
            top: 1rem;
            right: 1rem;
            background: var(--edsys-white);
            padding: 1rem;
            border-radius: 0.5rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            transform: translateX(100%);
            transition: transform 0.3s ease;
            border-left: 4px solid ${type === 'success' ? '#10b981' : '#ef4444'};
        `;

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        setTimeout(() => {
            notification.style.transform = 'translateX(100%)';
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }

    updateURL() {
        const url = new URL(window.location);

        // Очищаем старые параметры фильтра
        for (let key of [...url.searchParams.keys()]) {
            if (key.startsWith('filter_') || key === 'price_from' || key === 'price_to') {
                url.searchParams.delete(key);
            }
        }

        // Добавляем новые параметры
        for (let [key, values] of Object.entries(this.filters)) {
            if (Array.isArray(values)) {
                values.forEach(value => url.searchParams.append(key, value));
            } else {
                url.searchParams.set(key, values);
            }
        }

        window.history.replaceState({}, '', url.toString());
    }

    initInfiniteScroll() {
        let scrollTimeout;

        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }

            scrollTimeout = setTimeout(() => {
                if (this.isLoading || !this.hasMoreProducts) return;

                const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
                const windowHeight = window.innerHeight;
                const documentHeight = document.documentElement.scrollHeight;

                if (scrollTop + windowHeight >= documentHeight - 300) {
                    this.loadProducts(false);
                }
            }, 100);
        }, { passive: true });
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    window.edsysCategoryModern = new EdsysCategoryModern();

    // Восстанавливаем сохраненный режим просмотра
    const savedViewMode = localStorage.getItem('edsys-view-mode');
    if (savedViewMode) {
        window.edsysCategoryModern.switchView(savedViewMode);
    }
});
