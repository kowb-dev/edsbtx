/**
 * Catalog Section JavaScript
 * Handles filtering, favorites, compare, and navigation
 * 
 * @version 1.5.0
 * @author KW
 * @link https://kowb.ru
 */

(function() {
    'use strict';

    class CatalogFilter {
        constructor() {
            this.sidebar = document.getElementById('catalog-filter');
            this.filterToggle = document.querySelector('.edsys-catalog__filter-toggle');
            this.filterClose = document.querySelector('.edsys-catalog__filter-close');
            this.filterForm = document.getElementById('catalog-filter-form');
            this.productsContainer = document.getElementById('catalog-products');
            this.loadingOverlay = document.getElementById('catalog-loading');
            this.productsCount = document.getElementById('products-count');
            
            this.categoryAll = document.getElementById('category-all');
            this.categoryCheckboxes = document.querySelectorAll('.edsys-catalog__category-checkbox');
            this.filterReset = document.getElementById('filter-reset');
            this.resetAll = document.getElementById('reset-all-filters');

            this.createOverlay();
            this.config = window.edysCatalogConfig || {};
            this.filterTimer = null;

            this.init();
            this.initProductActions();
            this.initCompareStatus();
        }

        init() {
            if (!this.sidebar || !this.productsContainer) {
                console.warn('Catalog: Required DOM elements not found');
                return;
            }

            if (this.filterToggle) {
                this.filterToggle.addEventListener('click', () => this.openFilter());
            }

            if (this.filterClose) {
                this.filterClose.addEventListener('click', () => this.closeFilter());
            }

            if (this.overlay) {
                this.overlay.addEventListener('click', () => this.closeFilter());
            }

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && this.sidebar.classList.contains('is-open')) {
                    this.closeFilter();
                }
            });

            if (this.categoryAll) {
                this.categoryAll.addEventListener('change', (e) => this.handleSelectAll(e));
            }

            this.categoryCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.handleCategoryChange();
                });
            });

            if (this.filterReset) {
                this.filterReset.addEventListener('click', () => this.resetFilters());
            }

            if (this.resetAll) {
                this.resetAll.addEventListener('click', () => this.resetFilters());
            }

            if (this.filterForm) {
                this.filterForm.addEventListener('submit', (e) => e.preventDefault());
            }

            this.updateSelectAllState();
        }

        initProductActions() {
            const productRows = document.querySelectorAll('.edsys-catalog__table-row');
            
            productRows.forEach(row => {
                row.addEventListener('click', (e) => {
                    if (!e.target.closest('.edsys-catalog__table-actions')) {
                        const url = row.dataset.productUrl;
                        if (url) {
                            window.location.href = url;
                        }
                    }
                });

                const favoriteBtn = row.querySelector('.edsys-catalog__action-btn--favorite');
                const compareBtn = row.querySelector('.edsys-catalog__action-btn--compare');

                if (favoriteBtn) {
                    favoriteBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.toggleFavorite(favoriteBtn);
                    });
                }

                if (compareBtn) {
                    compareBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.toggleCompare(compareBtn);
                    });
                }
            });
        }

        initCompareStatus() {
            fetch('/local/ajax/compare/get_status.php', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.compareList) {
                    this.updateCompareButtons(data.compareList);
                    this.updateCompareCounter(data.compareCount);
                }
            })
            .catch(error => {
                console.error('Compare status error:', error);
            });
        }

        updateCompareButtons(compareList) {
            const compareButtons = document.querySelectorAll('.edsys-catalog__action-btn--compare');
            
            compareButtons.forEach(button => {
                const productId = parseInt(button.dataset.productId);
                const icon = button.querySelector('i');
                
                if (compareList.includes(productId)) {
                    icon.classList.remove('ph-thin');
                    icon.classList.add('ph-fill');
                    button.setAttribute('aria-pressed', 'true');
                    button.classList.add('active');
                }
            });
        }

        toggleFavorite(button) {
            if (button.disabled) return;
            
            const productId = button.dataset.productId;
            const icon = button.querySelector('i');
            const isActive = icon.classList.contains('ph-fill');

            button.disabled = true;
            button.style.opacity = '0.6';

            const formData = new FormData();
            formData.append('action', 'toggle');
            formData.append('productId', parseInt(productId));
            formData.append('sessid', this.config.sessid);

            fetch('/local/ajax/favorites.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.inFavorites) {
                        icon.classList.remove('ph-thin');
                        icon.classList.add('ph-fill');
                        button.classList.add('active');
                        this.showNotification('Товар добавлен в избранное', 'success');
                    } else {
                        icon.classList.remove('ph-fill');
                        icon.classList.add('ph-thin');
                        button.classList.remove('active');
                        this.showNotification('Товар удалён из избранного', 'info');
                    }

                    this.updateFavoritesCounter(data.data.count);
                } else {
                    this.showNotification(data.message || 'Ошибка при обновлении избранного', 'error');
                }
            })
            .catch(error => {
                console.error('Favorites error:', error);
                this.showNotification('Ошибка при обновлении избранного', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        toggleCompare(button) {
            if (button.disabled) return;
            
            const productId = button.dataset.productId;
            const icon = button.querySelector('i');
            const isActive = button.getAttribute('aria-pressed') === 'true';

            button.disabled = true;
            button.style.opacity = '0.6';

            const formData = new FormData();
            formData.append('productId', parseInt(productId));
            formData.append('sessid', this.config.sessid);

            fetch('/local/ajax/compare/add.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (data.data.inCompare) {
                        icon.classList.remove('ph-thin');
                        icon.classList.add('ph-fill');
                        button.setAttribute('aria-pressed', 'true');
                        button.classList.add('active');
                        this.showNotification('Товар добавлен к сравнению', 'success');
                    } else {
                        icon.classList.remove('ph-fill');
                        icon.classList.add('ph-thin');
                        button.setAttribute('aria-pressed', 'false');
                        button.classList.remove('active');
                        this.showNotification('Товар удалён из сравнения', 'info');
                    }

                    this.updateCompareCounter(data.data.compareCount);
                } else {
                    console.error('Compare toggle error:', data.message);
                    this.showNotification(data.message || 'Ошибка при обновлении сравнения', 'error');
                }
            })
            .catch(error => {
                console.error('Compare error:', error);
                this.showNotification('Ошибка при обновлении сравнения', 'error');
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        updateCompareCounter(count) {
            const counters = document.querySelectorAll('#compare-counter, .compare-counter, [data-compare-count]');
            
            counters.forEach(counter => {
                counter.textContent = count || 0;
                
                if (count > 0) {
                    counter.style.display = 'inline-block';
                    counter.classList.add('active');
                } else {
                    counter.style.display = 'none';
                    counter.classList.remove('active');
                }
            });
        }

        updateFavoritesCounter(count) {
            const counters = document.querySelectorAll('#favorites-counter, .favorites-counter, [data-favorites-count]');
            
            counters.forEach(counter => {
                counter.textContent = count || 0;
                
                if (count > 0) {
                    counter.style.display = 'inline-block';
                    counter.classList.add('active');
                } else {
                    counter.style.display = 'none';
                    counter.classList.remove('active');
                }
            });
        }

        showNotification(message, type = 'info') {
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        createOverlay() {
            this.overlay = document.createElement('div');
            this.overlay.className = 'edsys-catalog__overlay';
            document.body.appendChild(this.overlay);
        }

        openFilter() {
            this.sidebar.classList.add('is-open');
            this.overlay.classList.add('is-visible');
            this.filterToggle.setAttribute('aria-expanded', 'true');
            document.body.style.overflow = 'hidden';
            this.trapFocus();
        }

        closeFilter() {
            this.sidebar.classList.remove('is-open');
            this.overlay.classList.remove('is-visible');
            this.filterToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }

        trapFocus() {
            const focusableElements = this.sidebar.querySelectorAll(
                'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
            );
            
            if (focusableElements.length === 0) return;

            const firstElement = focusableElements[0];
            const lastElement = focusableElements[focusableElements.length - 1];

            firstElement.focus();

            const handleTabKey = (e) => {
                if (e.key !== 'Tab') return;

                if (e.shiftKey) {
                    if (document.activeElement === firstElement) {
                        e.preventDefault();
                        lastElement.focus();
                    }
                } else {
                    if (document.activeElement === lastElement) {
                        e.preventDefault();
                        firstElement.focus();
                    }
                }
            };

            this.sidebar.addEventListener('keydown', handleTabKey);
        }

        handleSelectAll(e) {
            const isChecked = e.target.checked;
            
            this.categoryCheckboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            this.applyFilters();
        }

        handleCategoryChange() {
            const anyChecked = Array.from(this.categoryCheckboxes).some(cb => cb.checked);
            
            if (anyChecked && this.categoryAll) {
                this.categoryAll.checked = false;
            }

            if (!anyChecked && this.categoryAll) {
                this.categoryAll.checked = true;
            }
            
            clearTimeout(this.filterTimer);
            this.filterTimer = setTimeout(() => {
                this.applyFilters();
            }, 300);
        }

        updateSelectAllState() {
            if (!this.categoryAll) return;

            const anyChecked = Array.from(this.categoryCheckboxes).some(cb => cb.checked);
            
            if (!anyChecked && !this.categoryAll.checked) {
                this.categoryAll.checked = true;
            }
        }

        async applyFilters() {
            const selected = this.getSelectedCategories();

            try {
                this.showLoading && this.showLoading();

                const body = new URLSearchParams({
                    // ожидаемое PHP
                    action: 'filter',
                    iblock_id: String(window.edysCatalogConfig?.iblockId || 0),
                    categories: JSON.stringify(selected),
                    sessid: window.edysCatalogConfig?.sessid || '',

                    // обратная совместимость с прошлой версией
                    iblockId: String(window.edysCatalogConfig?.iblockId || 0),
                });

                // если хотите — оставим и массивом, но JSON обязателен:
                selected.forEach(v => body.append('categories[]', v));

                const res = await fetch(window.edysCatalogConfig.ajaxUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body
                });

                const raw = await res.text();
                console.log('[filter][raw-status]', res.status);
                console.log('[filter][raw-body]', raw);

                if (!raw.trim()) {
                    throw new Error('Empty JSON body');
                }

                const data = JSON.parse(raw);

                if (!data.success) {
                    console.error('[filter][server-error]', data.error);
                    return;
                }

                // html + count
                if (typeof data.html === 'string') {
                    const container = document.querySelector('#catalog-products .edsys-catalog__table-body');
                    if (container) {
                        container.innerHTML = data.html;
                        this.initProductActions();
                    }
                }
                if (typeof data.count === 'number') {
                    this.updateProductsCount && this.updateProductsCount(data.count);
                    this.toggleEmptyState && this.toggleEmptyState(data.count === 0);
                }

            } catch (err) {
                console.error('Filter fetch error:', err);
            } finally {
                this.hideLoading && this.hideLoading();
                if (window.innerWidth < 1024) this.closeFilter && this.closeFilter();
            }
        }

        getSelectedCategories() {
            if (this.categoryAll && this.categoryAll.checked) {
                const anyIndividualChecked = Array.from(this.categoryCheckboxes).some(cb => cb.checked);
                if (!anyIndividualChecked) {
                    return ['all'];
                }
            }

            const selected = Array.from(this.categoryCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            
            if (selected.length === 0) {
                return ['all'];
            }
            
            return selected;
        }

        updateProductsCount(count) {
            if (this.productsCount) {
                this.productsCount.textContent = count;
            }
        }

        toggleEmptyState(show) {
            const emptyState = this.productsContainer.querySelector('.edsys-catalog__empty');
            const tableBody = this.productsContainer.querySelector('.edsys-catalog__table-body');

            if (show) {
                if (tableBody) tableBody.style.display = 'none';
                if (emptyState) emptyState.style.display = 'flex';
            } else {
                if (tableBody) tableBody.style.display = '';
                if (emptyState) emptyState.style.display = 'none';
            }
        }

        resetFilters() {
            this.categoryCheckboxes.forEach(cb => {
                cb.checked = false;
            });

            if (this.categoryAll) {
                this.categoryAll.checked = true;
            }

            this.applyFilters();
        }

        showLoading() {
            if (this.loadingOverlay) {
                this.loadingOverlay.hidden = false;
            }
        }

        hideLoading() {
            if (this.loadingOverlay) {
                this.loadingOverlay.hidden = true;
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new CatalogFilter();
        });
    } else {
        new CatalogFilter();
    }

})();