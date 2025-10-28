/**
 * Infinite Scroll for EDS Catalog
 * Бесконечная прокрутка для каталога товаров
 */

class EdsysInfiniteScroll {
    constructor(options = {}) {
        this.container = options.container || '#products-grid';
        this.loader = options.loader || '#products-loader';
        this.threshold = options.threshold || 200;
        this.pageSize = options.pageSize || 20;
        this.currentPage = 1;
        this.isLoading = false;
        this.hasMore = true;
        this.sectionId = options.sectionId || null;
        this.filter = options.filter || {};
        this.sort = options.sort || 'name-asc';

        this.init();
    }

    init() {
        this.containerEl = document.querySelector(this.container);
        this.loaderEl = document.querySelector(this.loader);

        if (!this.containerEl) {
            console.error('Container not found:', this.container);
            return;
        }

        this.bindEvents();
        this.initSortHandler();
        this.initViewModeHandler();
    }

    bindEvents() {
        // Обработчик скролла с throttling
        let scrollTimeout;
        window.addEventListener('scroll', () => {
            if (scrollTimeout) {
                clearTimeout(scrollTimeout);
            }

            scrollTimeout = setTimeout(() => {
                this.handleScroll();
            }, 100);
        }, { passive: true });

        // Обработчик изменения фильтров
        document.addEventListener('filterChange', (e) => {
            this.handleFilterChange(e.detail);
        });
    }

    handleScroll() {
        if (this.isLoading || !this.hasMore) {
            return;
        }

        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const documentHeight = document.documentElement.scrollHeight;

        // Проверяем, достиг ли пользователь конца страницы
        if (scrollTop + windowHeight >= documentHeight - this.threshold) {
            this.loadMore();
        }
    }

    async loadMore() {
        if (this.isLoading || !this.hasMore) {
            return;
        }

        this.isLoading = true;
        this.showLoader();

        try {
            const nextPage = this.currentPage + 1;
            const products = await this.fetchProducts(nextPage);

            if (products && products.length > 0) {
                this.appendProducts(products);
                this.currentPage = nextPage;

                // Проверяем, есть ли еще товары
                if (products.length < this.pageSize) {
                    this.hasMore = false;
                }
            } else {
                this.hasMore = false;
            }
        } catch (error) {
            console.error('Error loading products:', error);
            this.showError('Ошибка загрузки товаров');
        } finally {
            this.isLoading = false;
            this.hideLoader();
        }
    }

    async fetchProducts(page) {
        const params = new URLSearchParams({
            action: 'get_products',
            page: page,
            pageSize: this.pageSize,
            sectionId: this.sectionId || '',
            sort: this.sort,
            ...this.filter
        });

        const response = await fetch('/local/ajax/get_products.php?' + params.toString(), {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const data = await response.json();

        if (data.status !== 'success') {
            throw new Error(data.message || 'Unknown error');
        }

        return data.products;
    }

    appendProducts(products) {
        const fragment = document.createDocumentFragment();

        products.forEach(product => {
            const productElement = this.createProductElement(product);
            fragment.appendChild(productElement);
        });

        this.containerEl.appendChild(fragment);

        // Анимация появления новых товаров
        this.animateNewProducts();
    }

    createProductElement(product) {
        const article = document.createElement('article');
        article.className = 'edsys-product-card';
        article.setAttribute('itemscope', '');
        article.setAttribute('itemtype', 'http://schema.org/Product');
        article.setAttribute('data-product-id', product.ID);

        // Здесь создаем HTML для товара
        article.innerHTML = this.getProductHTML(product);

        return article;
    }

    getProductHTML(product) {
        const isUserAuthorized = window.USER_AUTHORIZED || false;
        const imageUrl = product.PREVIEW_PICTURE || product.DETAIL_PICTURE || '/local/templates/.default/images/no-photo.jpg';
        const isAvailable = product.CAN_BUY;
        const availabilityText = isAvailable ? 'В наличии' : 'Предзаказ';
        const availabilityClass = isAvailable ? 'available' : 'preorder';

        return `
            <div class="edsys-product-card__image-wrapper">
                <img src="${imageUrl}"
                     alt="${this.escapeHtml(product.NAME)}"
                     class="edsys-product-card__image"
                     itemprop="image"
                     width="280"
                     height="280"
                     loading="lazy">

                <div class="edsys-product-card__actions">
                    <button class="edsys-product-card__action edsys-product-card__compare"
                            data-product-id="${product.ID}"
                            title="Добавить в сравнение"
                            aria-label="Добавить в сравнение">
                        <i class="ph ph-thin ph-chart-bar"></i>
                    </button>

                    <button class="edsys-product-card__action edsys-product-card__favorite"
                            data-product-id="${product.ID}"
                            title="Добавить в избранное"
                            aria-label="Добавить в избранное">
                        <i class="ph ph-thin ph-heart"></i>
                    </button>
                </div>
            </div>

            <div class="edsys-product-card__content">
                <h3 class="edsys-product-card__title" itemprop="name">
                    <a href="${product.DETAIL_PAGE_URL}"
                       class="edsys-product-card__link">
                        ${this.escapeHtml(product.NAME)}
                    </a>
                </h3>

                ${product.ARTICLE ? `
                <div class="edsys-product-card__article">
                    Артикул: <span itemprop="sku">${product.ARTICLE}</span>
                </div>
                ` : ''}

                ${product.PREVIEW_TEXT ? `
                <div class="edsys-product-card__description" itemprop="description">
                    ${product.PREVIEW_TEXT}
                </div>
                ` : ''}

                ${isUserAuthorized ? `
                <div class="edsys-product-card__availability edsys-product-card__availability--${availabilityClass}">
                    <i class="ph ph-thin ph-${isAvailable ? 'check-circle' : 'clock'}"></i>
                    ${availabilityText}
                </div>

                <div class="edsys-product-card__prices" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                    ${product.RETAIL_PRICE ? `
                    <div class="edsys-product-card__price edsys-product-card__price--retail">
                        <span class="edsys-product-card__price-label">Розничная цена</span>
                        <span class="edsys-product-card__price-value">${product.RETAIL_PRICE}</span>
                    </div>
                    ` : ''}

                    ${product.USER_PRICE ? `
                    <div class="edsys-product-card__price edsys-product-card__price--user" itemprop="price" content="${product.PRICE_VALUE}">
                        <span class="edsys-product-card__price-label">Ваша цена</span>
                        <span class="edsys-product-card__price-value">${product.USER_PRICE}</span>
                    </div>
                    <meta itemprop="priceCurrency" content="${product.CURRENCY}">
                    ` : ''}
                </div>
                ` : ''}

                <div class="edsys-product-card__footer">
                    ${isUserAuthorized && product.CAN_BUY ? `
                    <button class="edsys-product-card__buy-btn"
                            data-product-id="${product.ID}"
                            type="button">
                        <i class="ph ph-thin ph-shopping-cart"></i>
                        В корзину
                    </button>
                    ` : `
                    <a href="${product.DETAIL_PAGE_URL}"
                       class="edsys-product-card__details-btn">
                        Подробнее
                    </a>
                    `}
                </div>
            </div>
        `;
    }

    animateNewProducts() {
        const newProducts = this.containerEl.querySelectorAll('.edsys-product-card:not(.animated)');

        newProducts.forEach((product, index) => {
            product.classList.add('animated');
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';

            setTimeout(() => {
                product.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, index * 100);
        });
    }

    showLoader() {
        if (this.loaderEl) {
            this.loaderEl.style.display = 'flex';
        }
    }

    hideLoader() {
        if (this.loaderEl) {
            this.loaderEl.style.display = 'none';
        }
    }

    showError(message) {
        // Показываем ошибку пользователю
        console.error(message);
    }

    handleFilterChange(filterData) {
        // Сброс состояния и перезагрузка с новыми фильтрами
        this.filter = filterData;
        this.currentPage = 1;
        this.hasMore = true;
        this.isLoading = false;

        // Очищаем контейнер и загружаем заново
        this.containerEl.innerHTML = '';
        this.loadMore();
    }

    initSortHandler() {
        const sortSelect = document.getElementById('sort-select');
        if (sortSelect) {
            sortSelect.addEventListener('change', (e) => {
                this.sort = e.target.value;
                this.handleFilterChange(this.filter);
            });
        }
    }

    initViewModeHandler() {
        const viewButtons = document.querySelectorAll('.edsys-category__view-btn');
        const productsContainer = document.querySelector('.edsys-products');

        viewButtons.forEach(button => {
            button.addEventListener('click', () => {
                const viewMode = button.dataset.view;

                // Обновляем активную кнопку
                viewButtons.forEach(btn => btn.classList.remove('edsys-category__view-btn--active'));
                button.classList.add('edsys-category__view-btn--active');

                // Обновляем режим отображения
                if (productsContainer) {
                    productsContainer.setAttribute('data-view', viewMode);
                }

                // Сохраняем предпочтения пользователя
                localStorage.setItem('edsys-view-mode', viewMode);
            });
        });

        // Восстанавливаем сохраненный режим отображения
        const savedViewMode = localStorage.getItem('edsys-view-mode');
        if (savedViewMode && productsContainer) {
            productsContainer.setAttribute('data-view', savedViewMode);

            const activeButton = document.querySelector(`[data-view="${savedViewMode}"]`);
            if (activeButton) {
                viewButtons.forEach(btn => btn.classList.remove('edsys-category__view-btn--active'));
                activeButton.classList.add('edsys-category__view-btn--active');
            }
        }
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Публичные методы
    reset() {
        this.currentPage = 1;
        this.hasMore = true;
        this.isLoading = false;
        this.containerEl.innerHTML = '';
    }

    updateFilter(newFilter) {
        this.handleFilterChange(newFilter);
    }

    updateSort(newSort) {
        this.sort = newSort;
        this.handleFilterChange(this.filter);
    }
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', function() {
    // Получаем ID секции из URL или другого источника
    const sectionId = new URLSearchParams(window.location.search).get('SECTION_ID') || null;

    // Инициализируем бесконечную прокрутку
    window.edsysInfiniteScroll = new EdsysInfiniteScroll({
        container: '#products-grid',
        loader: '#products-loader',
        threshold: 200,
        pageSize: 20,
        sectionId: sectionId
    });
});
