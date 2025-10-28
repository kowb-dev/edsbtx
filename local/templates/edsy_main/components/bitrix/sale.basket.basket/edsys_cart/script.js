/**
 * Cart JavaScript - edsys_cart
 * 
 * @version 1.1.1
 * @date 2025-10-25
 * @description Production ready - no console logs, optimized performance
 * @author KW
 * @uri https://kowb.ru
 * 
 * Path: /local/templates/edsy_main/components/bitrix/sale.basket.basket/edsys_cart/script.js
 */

(function() {
    'use strict';

    class CartController {
        constructor() {
            this.cart = document.getElementById('edsys-cart-section');
            if (!this.cart) return;

            this.loading = document.getElementById('edsys-cart-loading');
            this.selectAllCheckbox = document.getElementById('edsys-cart-select-all');
            this.clearSelectedBtn = document.getElementById('edsys-cart-clear-selected');
            this.promoToggle = document.getElementById('edsys-cart-promo-toggle');
            this.promoForm = document.getElementById('edsys-cart-promo-form');
            this.promoInput = document.getElementById('edsys-cart-promo-input');
            this.promoApplyBtn = document.getElementById('edsys-cart-promo-apply');

            this.ajaxPath = window.edsysCart && window.edsysCart.templateFolder 
                ? window.edsysCart.templateFolder + '/ajax.php'
                : this.getTemplatePath() + '/ajax.php';
            
            this.processingItems = new Set();
            this.updateTimeout = null;
            this.eventsBound = false;
            
            this.init();
        }

        getTemplatePath() {
            const scripts = document.querySelectorAll('script[src*="edsys_cart"]');
            if (scripts.length > 0) {
                const src = scripts[scripts.length - 1].src;
                return src.substring(0, src.lastIndexOf('/'));
            }
            return '/local/templates/.default/components/bitrix/sale.basket.basket/edsys_cart';
        }

        init() {
            if (this.eventsBound) return;
            
            this.bindSelectAllEvents();
            this.bindItemCheckboxEvents();
            this.bindQuantityEvents();
            this.bindDeleteEvents();
            this.bindPromoEvents();
            
            this.eventsBound = true;
        }

        bindSelectAllEvents() {
            if (!this.selectAllCheckbox) return;

            this.selectAllCheckbox.addEventListener('change', (e) => {
                const isChecked = e.target.checked;
                const itemCheckboxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox');
                
                itemCheckboxes.forEach(checkbox => {
                    checkbox.checked = isChecked;
                });

                this.updateClearButtonState();
            });
        }

        bindItemCheckboxEvents() {
            const itemCheckboxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox');
            
            itemCheckboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateSelectAllState();
                    this.updateClearButtonState();
                });
            });

            if (this.clearSelectedBtn) {
                this.clearSelectedBtn.addEventListener('click', () => {
                    this.deleteSelectedItems();
                });
            }
        }

        updateSelectAllState() {
            if (!this.selectAllCheckbox) return;

            const itemCheckboxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox');
            const checkedBoxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox:checked');

            if (itemCheckboxes.length === 0) {
                this.selectAllCheckbox.checked = false;
                this.selectAllCheckbox.indeterminate = false;
                return;
            }

            if (checkedBoxes.length === 0) {
                this.selectAllCheckbox.checked = false;
                this.selectAllCheckbox.indeterminate = false;
            } else if (checkedBoxes.length === itemCheckboxes.length) {
                this.selectAllCheckbox.checked = true;
                this.selectAllCheckbox.indeterminate = false;
            } else {
                this.selectAllCheckbox.checked = false;
                this.selectAllCheckbox.indeterminate = true;
            }
        }

        updateClearButtonState() {
            if (!this.clearSelectedBtn) return;

            const checkedBoxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox:checked');
            this.clearSelectedBtn.disabled = checkedBoxes.length === 0;
        }

        bindQuantityEvents() {
            this.cart.addEventListener('click', (e) => {
                const button = e.target.closest('.edsys-cart__qty-plus, .edsys-cart__qty-minus');
                if (!button) return;

                e.preventDefault();
                e.stopPropagation();

                const itemId = button.dataset.itemId;
                if (this.processingItems.has(itemId)) return;

                const input = this.getQuantityInput(itemId);
                if (!input) return;

                const currentValue = parseInt(input.value) || 1;
                let newValue = currentValue;

                if (button.classList.contains('edsys-cart__qty-plus')) {
                    newValue = currentValue + 1;
                } else if (button.classList.contains('edsys-cart__qty-minus') && currentValue > 1) {
                    newValue = currentValue - 1;
                }

                if (newValue !== currentValue) {
                    this.syncAllInputs(itemId, newValue);
                    this.updateQuantity(itemId, newValue);
                }
            });

            this.cart.addEventListener('change', (e) => {
                const input = e.target.closest('.edsys-cart__qty-input');
                if (!input) return;

                const itemId = input.dataset.itemId;
                if (this.processingItems.has(itemId)) return;

                let newValue = parseInt(input.value);
                if (isNaN(newValue) || newValue < 1) {
                    newValue = 1;
                }

                this.syncAllInputs(itemId, newValue);
                this.debouncedUpdate(itemId, newValue);
            });

            this.cart.addEventListener('keypress', (e) => {
                const input = e.target.closest('.edsys-cart__qty-input');
                if (!input) return;

                if (e.key < '0' || e.key > '9') {
                    e.preventDefault();
                }
            });
        }

        getQuantityInput(itemId) {
            const inputs = this.cart.querySelectorAll(`.edsys-cart__qty-input[data-item-id="${itemId}"]`);
            return inputs.length > 0 ? inputs[0] : null;
        }

        syncAllInputs(itemId, value) {
            const allInputs = this.cart.querySelectorAll(`.edsys-cart__qty-input[data-item-id="${itemId}"]`);
            allInputs.forEach(input => {
                input.value = value;
            });
        }

        debouncedUpdate(itemId, quantity) {
            if (this.updateTimeout) {
                clearTimeout(this.updateTimeout);
            }
            
            this.updateTimeout = setTimeout(() => {
                this.updateQuantity(itemId, quantity);
            }, 300);
        }

        bindDeleteEvents() {
            this.cart.querySelectorAll('.edsys-cart__item-delete').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    e.preventDefault();
                    const itemId = e.currentTarget.dataset.itemId;
                    this.deleteItem(itemId);
                });
            });
        }

        bindPromoEvents() {
            if (!this.promoToggle || !this.promoForm) return;

            this.promoToggle.addEventListener('click', () => {
                const isExpanded = this.promoToggle.getAttribute('aria-expanded') === 'true';
                this.promoToggle.setAttribute('aria-expanded', !isExpanded);
                this.promoForm.setAttribute('aria-hidden', isExpanded);
            });

            if (this.promoApplyBtn) {
                this.promoApplyBtn.addEventListener('click', () => {
                    this.applyPromoCode();
                });
            }

            if (this.promoInput) {
                this.promoInput.addEventListener('keypress', (e) => {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        this.applyPromoCode();
                    }
                });
            }
        }

        updateQuantity(itemId, quantity) {
            if (this.processingItems.has(itemId)) return;
            
            this.processingItems.add(itemId);
            this.showLoading();

            const formData = new FormData();
            formData.append('action', 'updateQuantity');
            formData.append('itemId', itemId);
            formData.append('quantity', quantity);
            formData.append('sessid', BX.bitrix_sessid());

            fetch(this.ajaxPath, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateItemTotal(itemId, data);
                    this.updateSummary(data);
                    this.showSuccess('Количество обновлено');
                } else {
                    this.showError(data.error || 'Ошибка при обновлении количества');
                    const input = this.getQuantityInput(itemId);
                    if (input && data.currentQuantity) {
                        this.syncAllInputs(itemId, data.currentQuantity);
                    }
                }
            })
            .catch(() => {
                this.showError('Ошибка при обновлении количества');
            })
            .finally(() => {
                this.processingItems.delete(itemId);
                this.hideLoading();
            });
        }

        deleteItem(itemId) {
            if (this.processingItems.has(itemId)) return;
            
            this.processingItems.add(itemId);
            this.showLoading();

            const formData = new FormData();
            formData.append('action', 'deleteItem');
            formData.append('itemId', itemId);
            formData.append('sessid', BX.bitrix_sessid());

            fetch(this.ajaxPath, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.removeItemFromDOM(itemId);
                    this.updateSummary(data);
                    this.showSuccess('Товар удален из корзины');
                    this.checkEmptyCart();
                } else {
                    this.showError(data.error || 'Ошибка при удалении товара');
                }
            })
            .catch(() => {
                this.showError('Ошибка при удалении товара');
            })
            .finally(() => {
                this.processingItems.delete(itemId);
                this.hideLoading();
            });
        }

        deleteSelectedItems() {
            const checkedBoxes = this.cart.querySelectorAll('.edsys-cart__item-checkbox:checked');
            
            if (checkedBoxes.length === 0) return;

            const itemIds = Array.from(checkedBoxes).map(cb => cb.dataset.itemId);
            
            const alreadyProcessing = itemIds.some(id => this.processingItems.has(id));
            if (alreadyProcessing) return;
            
            this.showLoading();

            itemIds.forEach(id => this.processingItems.add(id));

            const formData = new FormData();
            formData.append('action', 'deleteMultiple');
            formData.append('itemIds', JSON.stringify(itemIds));
            formData.append('sessid', BX.bitrix_sessid());

            fetch(this.ajaxPath, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    itemIds.forEach(id => this.removeItemFromDOM(id));
                    this.updateSummary(data);
                    this.showSuccess(`Удалено товаров: ${itemIds.length}`);
                    this.checkEmptyCart();
                } else {
                    this.showError(data.error || 'Ошибка при удалении товаров');
                }
            })
            .catch(() => {
                this.showError('Ошибка при удалении товаров');
            })
            .finally(() => {
                itemIds.forEach(id => this.processingItems.delete(id));
                this.hideLoading();
            });
        }

        applyPromoCode() {
            if (!this.promoInput) return;

            const promoCode = this.promoInput.value.trim();
            
            if (!promoCode) {
                this.showError('Введите промокод');
                return;
            }

            this.showLoading();

            const formData = new FormData();
            formData.append('action', 'applyCoupon');
            formData.append('coupon', promoCode);
            formData.append('sessid', BX.bitrix_sessid());

            fetch(this.ajaxPath, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.updateSummary(data);
                    this.showSuccess('Промокод применён');
                    this.promoInput.value = '';
                } else {
                    this.showError(data.error || 'Неверный промокод');
                }
            })
            .catch(() => {
                this.showError('Ошибка при применении промокода');
            })
            .finally(() => {
                this.hideLoading();
            });
        }

        updateItemTotal(itemId, data) {
            const totalElements = this.cart.querySelectorAll(`.edsys-cart__item-total-value[data-item-id="${itemId}"]`);
            
            if (totalElements.length > 0 && data.sumFormatted) {
                totalElements.forEach(el => {
                    el.innerHTML = data.sumFormatted;
                });
            }
        }

        updateSummary(data) {
            const countElement = document.getElementById('edsys-cart-items-count');
            if (countElement && data.itemsCount !== undefined) {
                countElement.textContent = data.itemsCount;
            }

            const totalElement = document.getElementById('edsys-cart-total');
            if (totalElement && data.totalFormatted !== undefined) {
                totalElement.innerHTML = data.totalFormatted;
            }

            if (data.discount && data.discount > 0) {
                const discountRow = this.cart.querySelector('.edsys-cart__summary-row--discount');
                if (discountRow) {
                    discountRow.style.display = 'flex';
                }
            }
        }

        removeItemFromDOM(itemId) {
            const item = this.cart.querySelector(`.edsys-cart__item[data-item-id="${itemId}"]`);
            
            if (item) {
                item.style.transition = 'opacity 0.3s ease';
                item.style.opacity = '0';
                setTimeout(() => {
                    item.remove();
                    this.updateSelectAllState();
                    this.updateClearButtonState();
                }, 300);
            }
        }

        checkEmptyCart() {
            const items = this.cart.querySelectorAll('.edsys-cart__item');
            
            if (items.length === 0) {
                setTimeout(() => {
                    window.location.reload();
                }, 500);
            }
        }

        showLoading() {
            if (this.loading) {
                this.loading.setAttribute('aria-hidden', 'false');
            }
        }

        hideLoading() {
            if (this.loading) {
                this.loading.setAttribute('aria-hidden', 'true');
            }
        }

        showError(message) {
            this.showNotification(message, 'error');
        }

        showSuccess(message) {
            this.showNotification(message, 'success');
        }

        showNotification(message, type = 'info') {
            if (typeof BX !== 'undefined' && BX.UI && BX.UI.Notification) {
                BX.UI.Notification.Center.notify({
                    content: message,
                    position: 'top-right',
                    autoHideDelay: 3000,
                    category: type === 'error' ? 'danger' : type
                });
                return;
            }

            this.showCustomToast(message, type);
        }

        showCustomToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `edsys-toast edsys-toast--${type}`;
            toast.innerHTML = `
                <div class="edsys-toast__content">
                    <i class="ph ph-thin ${type === 'error' ? 'ph-x-circle' : 'ph-check-circle'}" aria-hidden="true"></i>
                    <span class="edsys-toast__message">${message}</span>
                </div>
            `;

            document.body.appendChild(toast);

            setTimeout(() => toast.classList.add('edsys-toast--show'), 10);

            setTimeout(() => {
                toast.classList.remove('edsys-toast--show');
                setTimeout(() => document.body.removeChild(toast), 300);
            }, 3000);
        }
    }

    if (window.edsysCartInitialized) return;

    window.edsysCartInitialized = true;

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new CartController();
        });
    } else {
        new CartController();
    }

})();