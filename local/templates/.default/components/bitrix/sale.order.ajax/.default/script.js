/**
 * Order Checkout Script
 * Component: sale.order.ajax
 * 
 * @version 2.0.0
 * @author KW https://kowb.ru
 * 
 * Path: /local/templates/.default/components/bitrix/sale.order.ajax/.default/script.js
 */

(function() {
    'use strict';

    class EdsysOrderController {
        constructor() {
            this.order = document.getElementById('edsys-order-form');
            if (!this.order) {
                console.warn('Order form not found');
                return;
            }

            this.loading = document.getElementById('edsys-order-loading');
            this.promoToggle = document.getElementById('edsys-order-promo-toggle');
            this.promoForm = document.getElementById('edsys-order-promo-form');

            console.log('Order controller initialized', {
                order: !!this.order,
                promoToggle: !!this.promoToggle,
                promoForm: !!this.promoForm
            });

            this.init();
        }

        init() {
            this.setupPromoToggle();
            this.setupFormValidation();
            this.setupDeliverySelection();
            this.setupPaymentSelection();
        }

        setupPromoToggle() {
            if (!this.promoToggle || !this.promoForm) return;

            this.promoToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isExpanded = this.promoToggle.getAttribute('aria-expanded') === 'true';
                const newState = !isExpanded;
                
                this.promoToggle.setAttribute('aria-expanded', newState);
                this.promoForm.setAttribute('aria-hidden', !newState);
                
                // Update button styles
                if (newState) {
                    this.promoToggle.classList.add('active');
                } else {
                    this.promoToggle.classList.remove('active');
                }
                
                console.log('Promo toggle clicked, expanded:', newState);
            });
        }

        setupFormValidation() {
            const inputs = this.order.querySelectorAll('.edsys-order__input, .edsys-order__textarea');
            
            inputs.forEach(input => {
                input.addEventListener('blur', () => {
                    this.validateField(input);
                });

                input.addEventListener('input', () => {
                    if (input.classList.contains('error')) {
                        this.validateField(input);
                    }
                });
            });

            const form = this.order.querySelector('form');
            if (form) {
                form.addEventListener('submit', (e) => {
                    let isValid = true;
                    
                    inputs.forEach(input => {
                        if (!this.validateField(input)) {
                            isValid = false;
                        }
                    });

                    if (!isValid) {
                        e.preventDefault();
                        this.showError('Пожалуйста, заполните все обязательные поля');
                    }
                });
            }
        }

        validateField(field) {
            const isRequired = field.required;
            
            if (isRequired && !field.value.trim()) {
                this.markFieldInvalid(field);
                return false;
            }

            if (field.type === 'email' && field.value) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(field.value)) {
                    this.markFieldInvalid(field);
                    return false;
                }
            }

            if (field.type === 'tel' && field.value) {
                const phoneRegex = /^[\d\s\-\+\(\)]+$/;
                if (!phoneRegex.test(field.value) || field.value.length < 10) {
                    this.markFieldInvalid(field);
                    return false;
                }
            }

            this.markFieldValid(field);
            return true;
        }

        markFieldInvalid(field) {
            field.classList.add('error');
            field.style.borderColor = 'var(--edsys-accent)';
        }

        markFieldValid(field) {
            field.classList.remove('error');
            field.style.borderColor = '';
        }

        setupDeliverySelection() {
            const deliveryItems = this.order.querySelectorAll('.edsys-order__delivery-item');
            
            deliveryItems.forEach(item => {
                item.addEventListener('click', () => {
                    deliveryItems.forEach(i => {
                        i.classList.remove('edsys-order__delivery-item--selected');
                    });
                    item.classList.add('edsys-order__delivery-item--selected');
                });
            });
        }

        setupPaymentSelection() {
            const paymentItems = this.order.querySelectorAll('.edsys-order__payment-item');
            
            paymentItems.forEach(item => {
                item.addEventListener('click', () => {
                    paymentItems.forEach(i => {
                        i.classList.remove('edsys-order__payment-item--selected');
                    });
                    item.classList.add('edsys-order__payment-item--selected');
                });
            });
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
            if (typeof BX !== 'undefined' && BX.UI && BX.UI.Notification) {
                BX.UI.Notification.Center.notify({
                    content: message,
                    position: 'top-right',
                    autoHideDelay: 3000
                });
            } else {
                alert(message);
            }
        }

        showSuccess(message) {
            if (typeof BX !== 'undefined' && BX.UI && BX.UI.Notification) {
                BX.UI.Notification.Center.notify({
                    content: message,
                    position: 'top-right',
                    autoHideDelay: 3000,
                    category: 'success'
                });
            } else {
                alert(message);
            }
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => {
            new EdsysOrderController();
        });
    } else {
        new EdsysOrderController();
    }

    if (typeof BX !== 'undefined') {
        BX.addCustomEvent('onAjaxSuccess', function() {
            new EdsysOrderController();
        });
    }

})();