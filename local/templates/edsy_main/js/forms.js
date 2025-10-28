/**
 * Universal Forms Handler JavaScript
 * Version: 1.0.0
 * Date: 2025-01-17
 * Description: Единый обработчик для всех форм сайта EDS
 * File: /local/templates/edsy_main/js/forms.js
 */

(function() {
    'use strict';

    // Universal Forms Handler Class
    class EDSFormsHandler {
        constructor() {
            this.endpoint = '/local/php_interface/ajax/forms_handler.php';
            this.forms = new Map();
            this.init();
        }

        init() {
            this.registerFormHandlers();
            this.setupGlobalErrorHandling();
        }

        /**
         * Register all form handlers
         */
        registerFormHandlers() {
            // Contact form
            this.registerForm('.edsys-contact-form', {
                type: 'contact',
                requiredFields: ['first_name', 'last_name', 'email', 'message'],
                successMessage: 'Сообщение отправлено успешно!',
                successCallback: this.handleContactSuccess.bind(this)
            });

            // Technical specification form (individual solutions)
            this.registerForm('#wizardForm', {
                type: 'technical_spec',
                requiredFields: ['contactName', 'contactEmail', 'contactPhone', 'objectType'],
                successMessage: 'Техническое задание создано успешно!',
                successCallback: this.handleTechnicalSpecSuccess.bind(this),
                customSubmitHandler: this.handleWizardSubmit.bind(this)
            });

            // Consultation form
            this.registerForm('.edsys-consultation__form', {
                type: 'consultation',
                requiredFields: ['consultationName', 'consultationPhone'],
                successMessage: 'Заявка на консультацию принята!',
                successCallback: this.handleConsultationSuccess.bind(this)
            });

            // Newsletter form
            this.registerForm('.edsys-footer__newsletter-form', {
                type: 'newsletter',
                requiredFields: ['email'],
                successMessage: 'Подписка оформлена успешно!',
                successCallback: this.handleNewsletterSuccess.bind(this)
            });
        }

        /**
         * Register form handler
         */
        registerForm(selector, config) {
            const form = document.querySelector(selector);
            if (!form) return;

            this.forms.set(form, config);

            // Use custom submit handler if provided
            if (config.customSubmitHandler) {
                config.customSubmitHandler(form, config);
            } else {
                form.addEventListener('submit', (e) => {
                    e.preventDefault();
                    this.handleFormSubmit(form, config);
                });
            }
        }

        /**
         * Handle standard form submit
         */
        async handleFormSubmit(form, config) {
            try {
                // Validate form
                if (!this.validateForm(form, config)) {
                    return;
                }

                // Show loading
                this.showLoading(form);

                // Collect form data
                const formData = this.collectFormData(form, config.type);

                // Submit form
                const response = await this.submitForm(formData);

                if (response.success) {
                    this.showSuccess(form, config.successMessage);
                    if (config.successCallback) {
                        config.successCallback(response.data, form);
                    }
                } else {
                    throw new Error(response.message || 'Ошибка отправки формы');
                }

            } catch (error) {
                this.showError(form, error.message);
            } finally {
                this.hideLoading(form);
            }
        }

        /**
         * Handle wizard form submit (special case)
         */
        handleWizardSubmit(form, config) {
            // This integrates with existing wizard JavaScript
            if (window.edsWizard) {
                const originalSubmit = window.edsWizard.submitForm;
                window.edsWizard.submitForm = async () => {
                    try {
                        // Collect wizard data
                        const wizardData = window.edsWizard.formData;

                        // Add form type
                        wizardData.form_type = 'technical_spec';

                        // Submit using our universal handler
                        const response = await this.submitForm(wizardData, window.edsWizard.uploadedFiles);

                        if (response.success) {
                            // Use wizard's success handler
                            window.edsWizard.showResults(response.data);
                        } else {
                            throw new Error(response.message);
                        }

                    } catch (error) {
                        window.edsWizard.showNotification(error.message, 'error');
                    }
                };
            }
        }

        /**
         * Validate form
         */
        validateForm(form, config) {
            let isValid = true;

            // Clear previous errors
            form.querySelectorAll('.error').forEach(el => el.classList.remove('error'));
            form.querySelectorAll('.error-message').forEach(el => el.remove());

            // Validate required fields
            config.requiredFields.forEach(fieldName => {
                const field = form.querySelector(`[name="${fieldName}"]`);
                if (!field || !field.value.trim()) {
                    this.showFieldError(field, 'Это поле обязательно для заполнения');
                    isValid = false;
                }
            });

            // Validate email fields
            form.querySelectorAll('[type="email"]').forEach(field => {
                if (field.value && !this.isValidEmail(field.value)) {
                    this.showFieldError(field, 'Некорректный email адрес');
                    isValid = false;
                }
            });

            // Validate phone fields
            form.querySelectorAll('[type="tel"]').forEach(field => {
                if (field.value && !this.isValidPhone(field.value)) {
                    this.showFieldError(field, 'Некорректный номер телефона');
                    isValid = false;
                }
            });

            return isValid;
        }

        /**
         * Show field error
         */
        showFieldError(field, message) {
            if (!field) return;

            field.classList.add('error');

            const errorEl = document.createElement('div');
            errorEl.className = 'error-message';
            errorEl.textContent = message;
            errorEl.style.color = 'var(--edsys-accent)';
            errorEl.style.fontSize = '0.875rem';
            errorEl.style.marginTop = '0.25rem';

            field.parentNode.appendChild(errorEl);
        }

        /**
         * Collect form data
         */
        collectFormData(form, formType) {
            const formData = new FormData(form);
            const data = {};

            // Add form type
            data.form_type = formType;

            // Add Bitrix session ID
            if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
                data.sessid = BX.bitrix_sessid();
            }

            // Convert FormData to object
            for (let [key, value] of formData.entries()) {
                if (data[key]) {
                    // Handle multiple values (arrays)
                    if (Array.isArray(data[key])) {
                        data[key].push(value);
                    } else {
                        data[key] = [data[key], value];
                    }
                } else {
                    data[key] = value;
                }
            }

            return data;
        }

        /**
         * Submit form to server
         */
        async submitForm(data, files = null) {
            const formData = new FormData();

            // Add form data
            if (files) {
                // Handle file uploads
                files.forEach((file, index) => {
                    formData.append(`attachments[]`, file);
                });
                formData.append('data', JSON.stringify(data));
            } else {
                // Regular form submission
                Object.keys(data).forEach(key => {
                    if (Array.isArray(data[key])) {
                        data[key].forEach(value => {
                            formData.append(key + '[]', value);
                        });
                    } else {
                        formData.append(key, data[key]);
                    }
                });
            }

            const response = await fetch(this.endpoint, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: files ? formData : JSON.stringify(data)
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            return await response.json();
        }

        /**
         * Show loading state
         */
        showLoading(form) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.dataset.originalText = submitBtn.textContent;
                submitBtn.innerHTML = '<i class="ph ph-thin ph-spinner"></i> Отправляем...';
            }
        }

        /**
         * Hide loading state
         */
        hideLoading(form) {
            const submitBtn = form.querySelector('[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = false;
                if (submitBtn.dataset.originalText) {
                    submitBtn.textContent = submitBtn.dataset.originalText;
                }
            }
        }

        /**
         * Show success message
         */
        showSuccess(form, message) {
            this.showNotification(message, 'success');
            form.reset();
        }

        /**
         * Show error message
         */
        showError(form, message) {
            this.showNotification(message, 'error');
        }

        /**
         * Show notification
         */
        showNotification(message, type = 'info') {
            // Remove existing notifications
            document.querySelectorAll('.edsys-notification').forEach(n => n.remove());

            const notification = document.createElement('div');
            notification.className = `edsys-notification edsys-notification--${type}`;

            const icon = type === 'error' ? 'warning' : type === 'success' ? 'check' : 'info';
            notification.innerHTML = `
                <i class="ph ph-thin ph-${icon}"></i>
                <span>${message}</span>
            `;

            // Add styles if not exist
            if (!document.querySelector('#edsys-notification-styles')) {
                const style = document.createElement('style');
                style.id = 'edsys-notification-styles';
                style.textContent = `
                    .edsys-notification {
                        position: fixed;
                        top: 2rem;
                        left: 50%;
                        transform: translateX(-50%);
                        background: var(--edsys-white);
                        border: 2px solid;
                        border-radius: var(--radius-lg);
                        padding: var(--space-lg) var(--space-xl);
                        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
                        z-index: 10001;
                        display: flex;
                        align-items: center;
                        gap: var(--space-sm);
                        animation: slideInDown 0.3s ease;
                        max-width: 90vw;
                        font-weight: 500;
                    }
                    .edsys-notification--error {
                        border-color: var(--edsys-accent);
                        color: var(--edsys-accent);
                    }
                    .edsys-notification--success {
                        border-color: var(--edsys-circuit);
                        color: var(--edsys-circuit);
                    }
                    .edsys-notification--info {
                        border-color: var(--edsys-voltage);
                        color: var(--edsys-voltage);
                    }
                    @keyframes slideInDown {
                        from {
                            opacity: 0;
                            transform: translateX(-50%) translateY(-2rem);
                        }
                        to {
                            opacity: 1;
                            transform: translateX(-50%) translateY(0);
                        }
                    }
                `;
                document.head.appendChild(style);
            }

            document.body.appendChild(notification);

            // Auto remove after 4 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.style.animation = 'slideInDown 0.3s ease reverse';
                    setTimeout(() => {
                        if (notification.parentNode) {
                            notification.parentNode.removeChild(notification);
                        }
                    }, 300);
                }
            }, 4000);
        }

        /**
         * Success callbacks
         */
        handleContactSuccess(data, form) {
            console.log('Contact form submitted successfully:', data);
        }

        handleTechnicalSpecSuccess(data, form) {
            console.log('Technical spec submitted successfully:', data);
        }

        handleConsultationSuccess(data, form) {
            console.log('Consultation request submitted successfully:', data);
        }

        handleNewsletterSuccess(data, form) {
            console.log('Newsletter subscription successful:', data);

            // Update newsletter form UI
            const form = document.querySelector('.edsys-footer__newsletter-form');
            if (form) {
                const input = form.querySelector('input[type="email"]');
                const button = form.querySelector('button[type="submit"]');

                if (input && button) {
                    input.style.display = 'none';
                    button.textContent = 'Подписка оформлена!';
                    button.disabled = true;
                    button.style.background = 'var(--edsys-circuit)';

                    // Reset after 5 seconds
                    setTimeout(() => {
                        input.style.display = 'block';
                        button.textContent = 'Подписаться';
                        button.disabled = false;
                        button.style.background = '';
                    }, 5000);
                }
            }
        }

        /**
         * Utility methods
         */
        isValidEmail(email) {
            return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
        }

        isValidPhone(phone) {
            const cleaned = phone.replace(/\D/g, '');
            return cleaned.length >= 10;
        }

        /**
         * Global error handling
         */
        setupGlobalErrorHandling() {
            window.addEventListener('error', (e) => {
                console.error('Global error:', e.error);
            });

            window.addEventListener('unhandledrejection', (e) => {
                console.error('Unhandled promise rejection:', e.reason);
            });
        }
    }

    // Initialize when DOM is ready
    function initializeForms() {
        window.edsFormsHandler = new EDSFormsHandler();
        console.log('EDS Forms Handler initialized');
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeForms);
    } else {
        initializeForms();
    }

})();