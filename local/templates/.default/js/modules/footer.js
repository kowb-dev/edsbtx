/**
 * Footer Module - Tablet Layout Version (Compatible)
 * Version: 1.6.2
 * Date: 2025-07-19
 * Description: Исправленная версия Footer JavaScript без двойной инициализации
 * File: /local/templates/.default/js/modules/footer.js
 */

(function() {
    'use strict';

    // Prevent double initialization
    if (window.FooterModule) {
        return;
    }

    /**
     * Footer Module Class
     */
    function FooterModule() {
        this.footer = document.querySelector('.edsys-footer');
        this.sections = document.querySelectorAll('.edsys-footer__section');
        this.newsletterForm = document.querySelector('#newsletter-form');
        this.isFormSubmitting = false;
        this.maxRetries = 3;
        this.retryDelay = 1000;
        this.eventListeners = [];
        this.resizeObserver = null;
        this.resizeTimeout = null;
        this.animationObserver = null;
        this.currentWidth = window.innerWidth;
        this.isInitialized = false;

        this.init();
    }

    FooterModule.prototype.init = function() {
        if (!this.footer) {
            console.warn('Footer element not found');
            return;
        }

        if (this.isInitialized) {
            console.warn('Footer module already initialized');
            return;
        }

        try {
            this.initAccordion();
            this.initNewsletterForm();
            this.initSocialLinks();
            this.handleResize();
            this.observeFooterAnimation();
            this.initAccessibility();

            this.isInitialized = true;
        } catch (error) {
            console.error('Error initializing footer module:', error);
        }
    };

    /**
     * Initialize accordion functionality with improved mobile detection
     */
    FooterModule.prototype.initAccordion = function() {
        var self = this;

        this.sections.forEach(function(section, index) {
            var header = section.querySelector('.edsys-footer__section-header');
            var content = section.querySelector('.edsys-footer__section-content');

            if (!header || !content) {
                console.warn('Missing header or content for section', index);
                return;
            }

            // Add ARIA attributes
            var headerId = 'footer-section-' + index;
            var contentId = 'footer-content-' + index;

            header.id = headerId;
            content.id = contentId;
            header.setAttribute('aria-controls', contentId);
            header.setAttribute('role', 'button');
            header.setAttribute('tabindex', '0');
            content.setAttribute('aria-labelledby', headerId);

            // Set initial state based on screen size
            self.updateSectionState(section);

            var clickHandler = function(e) {
                e.preventDefault();
                if (self.isMobile()) {
                    self.toggleSection(section);
                }
            };

            var keydownHandler = function(e) {
                if ((e.key === 'Enter' || e.key === ' ') && self.isMobile()) {
                    e.preventDefault();
                    self.toggleSection(section);
                }
            };

            header.addEventListener('click', clickHandler);
            header.addEventListener('keydown', keydownHandler);

            self.eventListeners.push({ element: header, event: 'click', handler: clickHandler });
            self.eventListeners.push({ element: header, event: 'keydown', handler: keydownHandler });
        });

    };

    /**
     * Check if current viewport is mobile
     */
    FooterModule.prototype.isMobile = function() {
        return window.innerWidth < 768;
    };

    /**
     * Update section state based on current screen size
     */
    FooterModule.prototype.updateSectionState = function(section) {
        var header = section.querySelector('.edsys-footer__section-header');
        var content = section.querySelector('.edsys-footer__section-content');

        if (!header || !content) return;

        if (this.isMobile()) {
            // Mobile: enable accordion functionality, close sections
            header.style.cursor = 'pointer';
            header.setAttribute('aria-expanded', 'false');
            section.classList.remove('active');
            content.style.maxHeight = '0';
        } else {
            // Desktop/Tablet: disable accordion, open sections
            header.style.cursor = 'default';
            header.setAttribute('aria-expanded', 'true');
            section.classList.add('active');
            content.style.maxHeight = 'none';
        }
    };

    /**
     * Toggle section with improved animation
     */
    FooterModule.prototype.toggleSection = function(section) {
        if (!this.isMobile()) {
            return;
        }

        var isActive = section.classList.contains('active');
        var header = section.querySelector('.edsys-footer__section-header');
        var content = section.querySelector('.edsys-footer__section-content');

        if (!header || !content) return;

        // Close all other sections first
        var self = this;
        this.sections.forEach(function(otherSection) {
            if (otherSection !== section && otherSection.classList.contains('active')) {
                self.closeSection(otherSection);
            }
        });

        // Toggle current section
        if (isActive) {
            this.closeSection(section);
        } else {
            this.openSection(section);
        }
    };

    /**
     * Open section with smooth animation
     */
    FooterModule.prototype.openSection = function(section) {
        var content = section.querySelector('.edsys-footer__section-content');
        var header = section.querySelector('.edsys-footer__section-header');

        if (!content || !header) return;

        section.classList.add('active');

        // Calculate actual content height
        var scrollHeight = content.scrollHeight;
        content.style.maxHeight = scrollHeight + 'px';

        // Update ARIA
        header.setAttribute('aria-expanded', 'true');

        // Focus management
        setTimeout(function() {
            var firstLink = content.querySelector('a, button');
            if (firstLink && document.activeElement === header) {
                firstLink.focus();
            }
        }, 300);
    };

    /**
     * Close section with smooth animation
     */
    FooterModule.prototype.closeSection = function(section) {
        var content = section.querySelector('.edsys-footer__section-content');
        var header = section.querySelector('.edsys-footer__section-header');

        if (!content || !header) return;

        section.classList.remove('active');
        content.style.maxHeight = '0';

        // Update ARIA
        header.setAttribute('aria-expanded', 'false');
    };

    /**
     * Initialize newsletter form with enhanced validation
     */
    FooterModule.prototype.initNewsletterForm = function() {
        // Initialize both forms (tablet and desktop/mobile)
        var tabletForm = document.querySelector('#newsletter-form');
        var desktopForm = document.querySelector('#newsletter-form-desktop');

        // Initialize tablet form
        if (tabletForm) {
            this.initSingleNewsletterForm(tabletForm, 'tablet');
        }

        // Initialize desktop form
        if (desktopForm) {
            this.initSingleNewsletterForm(desktopForm, 'desktop');
        }

    };

    /**
     * Initialize single newsletter form
     */
    FooterModule.prototype.initSingleNewsletterForm = function(form, type) {
        if (!form) return;

        var self = this;
        var formId = type === 'tablet' ? '' : '-desktop';

        // Remove any existing event listeners by cloning
        var newForm = form.cloneNode(true);
        form.parentNode.replaceChild(newForm, form);
        form = newForm;

        var submitHandler = function(e) {
            e.preventDefault();
            self.handleNewsletterSubmit(form, type);
        };

        form.addEventListener('submit', submitHandler);
        this.eventListeners.push({ element: form, event: 'submit', handler: submitHandler });

        // Real-time email validation
        var emailInput = form.querySelector('#newsletter-email' + formId);
        if (emailInput) {
            var inputHandler = function(e) {
                self.validateEmail(e.target);
            };

            var blurHandler = function(e) {
                self.validateEmail(e.target);
            };

            emailInput.addEventListener('input', inputHandler);
            emailInput.addEventListener('blur', blurHandler);

            this.eventListeners.push({ element: emailInput, event: 'input', handler: inputHandler });
            this.eventListeners.push({ element: emailInput, event: 'blur', handler: blurHandler });

            // Add ARIA attributes
            emailInput.setAttribute('aria-describedby', 'newsletter-description');
            emailInput.setAttribute('aria-invalid', 'false');
        }
    };

    /**
     * Handle newsletter form submission with proper endpoint
     */
    FooterModule.prototype.handleNewsletterSubmit = function(form, type) {
        if (this.isFormSubmitting) {
            return;
        }

        var formId = type === 'tablet' ? '' : '-desktop';
        var emailInput = form.querySelector('#newsletter-email' + formId);
        var submitButton = form.querySelector('#newsletter-submit' + formId);
        var self = this;

        if (!emailInput || !submitButton) {
            console.error('Required form elements not found');
            this.showError('Ошибка: элементы формы не найдены', type);
            return;
        }

        var email = emailInput.value.trim();

        // Enhanced email validation
        if (!this.isValidEmail(email)) {
            this.showFieldError(emailInput, 'Пожалуйста, введите корректный email адрес');
            emailInput.focus();
            return;
        }

        // Check for common typos
        var emailSuggestion = this.suggestEmailCorrection(email);
        if (emailSuggestion && emailSuggestion !== email) {
            if (confirm('Вы имели в виду: ' + emailSuggestion + '?')) {
                emailInput.value = emailSuggestion;
                email = emailSuggestion;
            }
        }

        this.isFormSubmitting = true;
        this.showLoading(true, type);

        try {
            // Prepare form data with all necessary fields
            var formData = {
                form_type: 'newsletter',
                email: email,
                timestamp: Date.now(),
                source: 'footer_' + type,
                user_agent: navigator.userAgent,
                referrer: document.referrer || 'direct'
            };

            // Add Bitrix session ID if available
            var sessidInput = form.querySelector('input[name="sessid"]');
            if (sessidInput && sessidInput.value) {
                formData.sessid = sessidInput.value;
            }

            // Add form token
            var tokenInput = form.querySelector('input[name="form_token"]');
            if (tokenInput && tokenInput.value) {
                formData.form_token = tokenInput.value;
            }

            // Submit with retry logic
            this.submitWithRetry(formData).then(function(response) {

                if (response && response.success) {
                    var successMessage = 'Подписка оформлена успешно! Проверьте почту.';
                    if (response.data && response.data.message) {
                        successMessage = response.data.message;
                    }

                    self.showSuccess(successMessage, type);
                    form.reset();
                    self.updateFormAfterSuccess(type);
                    self.clearFieldError(emailInput);
                    self.trackNewsletterEvent('subscribe_success', email);

                    if (response.data) {
                    }
                } else {
                    var errorMessage = 'Неизвестная ошибка сервера';
                    if (response && response.message) {
                        errorMessage = response.message;
                    }
                    throw new Error(errorMessage);
                }
            }).catch(function(error) {
                console.error('Newsletter submission error:', error);
                var errorMessage = error.message || 'Произошла ошибка. Попробуйте позже или обратитесь в поддержку.';
                self.showError(errorMessage, type);
                self.trackNewsletterEvent('subscribe_error', email, error.message);
            }).finally(function() {
                self.isFormSubmitting = false;
                self.showLoading(false, type);
            });

        } catch (error) {
            console.error('Newsletter submission error:', error);
            this.showError('Произошла ошибка при отправке. Проверьте подключение к интернету.', type);
            this.isFormSubmitting = false;
            this.showLoading(false, type);
        }
    };

    /**
     * Submit with exponential backoff retry logic
     */
    FooterModule.prototype.submitWithRetry = function(data, attempt) {
        var self = this;
        attempt = attempt || 1;

        return this.submitToHandler(data).catch(function(error) {
            console.error('Attempt', attempt, 'failed:', error.message || error);

            if (attempt < self.maxRetries) {
                var delay = self.retryDelay * Math.pow(2, attempt - 1);

                return new Promise(function(resolve) {
                    setTimeout(function() {
                        resolve(self.submitWithRetry(data, attempt + 1));
                    }, delay);
                });
            } else {
                console.error('All retry attempts failed');
                throw new Error('Не удалось отправить форму после ' + attempt + ' попыток. Проверьте подключение к интернету.');
            }
        });
    };

    /**
     * Submit to correct form handler endpoints
     */
    FooterModule.prototype.submitToHandler = function(data) {
        var self = this;

        var endpoints = [
            '/local/php_interface/ajax/forms_handler.php',
            '/local/components/eds/ajax.forms/component.php'
        ];

        function tryEndpoint(index) {
            if (index >= endpoints.length) {
                throw new Error('Все серверы недоступны. Попробуйте позже или обратитесь в поддержку.');
            }

            var url = endpoints[index];

            return self.makeRequest(url, data).then(function(response) {
                return response;
            }).catch(function(error) {
                console.error('Failed with endpoint:', url, error.message || error);

                if (error.message && (
                    error.message.includes('500') ||
                    error.message.includes('404') ||
                    error.message.includes('Network') ||
                    error.message.includes('не найден') ||
                    error.message.includes('недоступен')
                )) {
                    return tryEndpoint(index + 1);
                } else {
                    throw error;
                }
            });
        }

        return tryEndpoint(0);
    };

    /**
     * Make HTTP request with timeout and proper error handling
     */
    FooterModule.prototype.makeRequest = function(url, data) {

        var controller = new AbortController();
        var timeoutId = setTimeout(function() {
            controller.abort();
        }, 15000);

        return fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data),
            signal: controller.signal
        }).then(function(response) {
            clearTimeout(timeoutId);

            if (!response.ok) {
                throw new Error('Ошибка сервера: ' + response.status + ' ' + response.statusText);
            }

            return response.text();
        }).then(function(text) {

            if (!text.trim()) {
                throw new Error('Пустой ответ от сервера');
            }

            var cleanText = text.trim();

            if (cleanText.charCodeAt(0) === 0xFEFF) {
                cleanText = cleanText.slice(1);
            }

            var jsonStartIndex = cleanText.indexOf('{');
            if (jsonStartIndex > 0) {
                console.warn('Removing non-JSON content before response:', cleanText.substring(0, jsonStartIndex));
                cleanText = cleanText.substring(jsonStartIndex);
            }

            try {
                var jsonResponse = JSON.parse(cleanText);
                return jsonResponse;
            } catch (parseError) {
                console.error('JSON parse error:', parseError);
                console.error('Raw response (first 1000 chars):', cleanText.substring(0, 1000));

                if (cleanText.includes('<html>') || cleanText.includes('<!DOCTYPE')) {
                    throw new Error('Сервер вернул HTML вместо JSON. Возможно, произошла ошибка на сервере.');
                } else if (cleanText.includes('Fatal error') || cleanText.includes('Parse error')) {
                    throw new Error('Ошибка сервера. Обратитесь к администратору.');
                } else if (cleanText.includes('404') || cleanText.includes('Not Found')) {
                    throw new Error('Обработчик форм не найден на сервере.');
                } else {
                    throw new Error('Некорректный ответ сервера. Попробуйте позже.');
                }
            }
        }).catch(function(error) {
            clearTimeout(timeoutId);

            if (error.name === 'AbortError') {
                throw new Error('Время ожидания истекло. Проверьте подключение к интернету.');
            } else if (error instanceof TypeError && error.message.includes('Failed to fetch')) {
                throw new Error('Проблема с подключением к серверу. Проверьте интернет-соединение.');
            } else {
                throw error;
            }
        });
    };

    /**
     * Show loading state with accessibility
     */
    FooterModule.prototype.showLoading = function(show, type) {
        var formId = type === 'tablet' ? '' : '-desktop';
        var button = document.querySelector('#newsletter-submit' + formId);

        if (!button) return;

        if (show) {
            button.classList.add('loading');
            button.disabled = true;
            button.setAttribute('aria-busy', 'true');

            var textSpan = button.querySelector('.edsys-newsletter-btn__text');
            var loadingSpan = button.querySelector('.edsys-newsletter-btn__loading');

            if (textSpan) textSpan.style.opacity = '0';
            if (loadingSpan) {
                loadingSpan.style.opacity = '1';
            }
        } else {
            button.classList.remove('loading');
            button.disabled = false;
            button.setAttribute('aria-busy', 'false');

            var textSpan = button.querySelector('.edsys-newsletter-btn__text');
            var loadingSpan = button.querySelector('.edsys-newsletter-btn__loading');

            if (textSpan) textSpan.style.opacity = '1';
            if (loadingSpan) loadingSpan.style.opacity = '0';
        }
    };

    /**
     * Show success message with auto-hide
     */
    FooterModule.prototype.showSuccess = function(message, type) {
        this.hideAllMessages(type);

        var formId = type === 'tablet' ? '' : '-desktop';
        var successDiv = document.getElementById('newsletter-success' + formId);

        if (successDiv) {
            var messageSpan = successDiv.querySelector('#newsletter-success-text' + formId);
            if (messageSpan) {
                messageSpan.textContent = message;
            }

            successDiv.classList.add('show');
            successDiv.setAttribute('role', 'status');
            successDiv.setAttribute('aria-live', 'polite');

            setTimeout(function() {
                successDiv.classList.remove('show');
            }, 8000);
        }
    };

    /**
     * Show error message with auto-hide
     */
    FooterModule.prototype.showError = function(message, type) {
        this.hideAllMessages(type);

        var formId = type === 'tablet' ? '' : '-desktop';
        var errorDiv = document.getElementById('newsletter-error' + formId);

        if (errorDiv) {
            var errorText = errorDiv.querySelector('#newsletter-error-text' + formId);
            if (errorText) {
                errorText.textContent = message;
            }

            errorDiv.classList.add('show');
            errorDiv.setAttribute('role', 'alert');
            errorDiv.setAttribute('aria-live', 'assertive');

            setTimeout(function() {
                errorDiv.classList.remove('show');
            }, 10000);
        }
    };

    /**
     * Hide all notification messages
     */
    FooterModule.prototype.hideAllMessages = function(type) {
        if (type) {
            var formId = type === 'tablet' ? '' : '-desktop';
            var messages = document.querySelectorAll('#newsletter-success' + formId + ', #newsletter-error' + formId);
            messages.forEach(function(msg) {
                msg.classList.remove('show');
            });
        } else {
            var messages = document.querySelectorAll('.edsys-newsletter__success, .edsys-newsletter__error');
            messages.forEach(function(msg) {
                msg.classList.remove('show');
            });
        }
    };

    /**
     * Update form after successful submission
     */
    FooterModule.prototype.updateFormAfterSuccess = function(type) {
        var formId = type === 'tablet' ? '' : '-desktop';
        var button = document.querySelector('#newsletter-submit' + formId);

        if (!button) return;

        var textSpan = button.querySelector('.edsys-newsletter-btn__text');
        var originalText = textSpan ? textSpan.textContent : 'Подписаться';

        var successHTML = '<i class="ph ph-thin ph-check"></i> Отлично 🤝';

        if (textSpan) {
            textSpan.innerHTML = successHTML;
        } else {
            button.innerHTML = successHTML;
        }

        button.style.background = 'var(--edsys-circuit, #00cc99)';
        button.disabled = true;

        setTimeout(function() {
            if (textSpan) {
                textSpan.textContent = originalText;
            } else {
                button.textContent = originalText;
            }
            button.style.background = '';
            button.disabled = false;
        }, 5000);
    };

    /**
     * Enhanced email validation
     */
    FooterModule.prototype.validateEmail = function(input) {
        var email = input.value.trim();

        this.clearFieldError(input);

        if (email === '') {
            input.setAttribute('aria-invalid', 'false');
            return true;
        }

        if (!this.isValidEmail(email)) {
            this.showFieldError(input, 'Некорректный формат email');
            input.setAttribute('aria-invalid', 'true');
            return false;
        }

        if (email.length > 254) {
            this.showFieldError(input, 'Email слишком длинный');
            input.setAttribute('aria-invalid', 'true');
            return false;
        }

        if (this.isDisposableEmail(email)) {
            this.showFieldError(input, 'Используйте постоянный email адрес');
            input.setAttribute('aria-invalid', 'true');
            return false;
        }

        this.showValidState(input);
        input.setAttribute('aria-invalid', 'false');
        return true;
    };

    /**
     * Check if email is valid
     */
    FooterModule.prototype.isValidEmail = function(email) {
        var emailRegex = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)*$/;
        return emailRegex.test(email) && email.indexOf('@') > 0 && email.indexOf('@') < email.length - 1;
    };

    /**
     * Check for disposable email services
     */
    FooterModule.prototype.isDisposableEmail = function(email) {
        var disposableDomains = [
            '10minutemail.com', 'guerrillamail.com', 'mailinator.com',
            'tempmail.org', 'throwaway.email', 'temp-mail.org',
            '1secmail.com', 'mohmal.com', 'sharklasers.com'
        ];

        var domain = email.split('@')[1];
        if (!domain) return false;

        return disposableDomains.includes(domain.toLowerCase());
    };

    /**
     * Suggest email correction for common typos
     */
    FooterModule.prototype.suggestEmailCorrection = function(email) {
        var commonDomains = {
            'gmial.com': 'gmail.com',
            'gmai.com': 'gmail.com',
            'gmail.co': 'gmail.com',
            'yahooo.com': 'yahoo.com',
            'yaho.com': 'yahoo.com',
            'yahoo.co': 'yahoo.com',
            'hotmial.com': 'hotmail.com',
            'hotmai.com': 'hotmail.com',
            'outlok.com': 'outlook.com',
            'outlook.co': 'outlook.com',
            'yandx.ru': 'yandex.ru',
            'yanex.ru': 'yandex.ru',
            'yandex.r': 'yandex.ru',
            'mal.ru': 'mail.ru',
            'mai.ru': 'mail.ru',
            'mail.r': 'mail.ru'
        };

        var parts = email.split('@');
        if (parts.length !== 2) return email;

        var domain = parts[1].toLowerCase();
        if (commonDomains[domain]) {
            return parts[0] + '@' + commonDomains[domain];
        }

        return email;
    };

    /**
     * Show field error with accessibility
     */
    FooterModule.prototype.showFieldError = function(input, message) {
        input.classList.add('error');
        input.classList.remove('valid');

        this.clearFieldError(input);

        var errorMsg = document.createElement('div');
        errorMsg.className = 'edsys-footer__error-message';
        errorMsg.textContent = message;
        errorMsg.id = input.id + '-error';
        errorMsg.setAttribute('role', 'alert');

        var newsletterWrapper = input.closest('.edsys-footer__newsletter-wrapper');
        if (newsletterWrapper) {
            newsletterWrapper.appendChild(errorMsg);
        } else {
            input.parentElement.appendChild(errorMsg);
        }

        input.setAttribute('aria-describedby', errorMsg.id);
    };

    /**
     * Clear field error
     */
    FooterModule.prototype.clearFieldError = function(input) {
        input.classList.remove('error');

        var newsletterWrapper = input.closest('.edsys-footer__newsletter-wrapper');
        var errorMsg = null;

        if (newsletterWrapper) {
            errorMsg = newsletterWrapper.querySelector('.edsys-footer__error-message');
        } else {
            errorMsg = input.parentElement.querySelector('.edsys-footer__error-message');
        }

        if (errorMsg) {
            errorMsg.remove();
            input.removeAttribute('aria-describedby');
        }
    };

    /**
     * Show valid state
     */
    FooterModule.prototype.showValidState = function(input) {
        input.classList.add('valid');
        input.classList.remove('error');
    };

    /**
     * Initialize social links with tracking
     */
    FooterModule.prototype.initSocialLinks = function() {
        var socialLinks = this.footer.querySelectorAll('.edsys-footer__social-link');
        var self = this;

        socialLinks.forEach(function(link) {
            var clickHandler = function(e) {
                var platform = self.getSocialPlatform(link.href);
                self.trackSocialClick(platform, link.href);
            };

            link.addEventListener('click', clickHandler);
            self.eventListeners.push({ element: link, event: 'click', handler: clickHandler });
        });
    };

    /**
     * Get social platform from URL
     */
    FooterModule.prototype.getSocialPlatform = function(url) {
        if (url.includes('facebook.com')) return 'facebook';
        if (url.includes('twitter.com') || url.includes('t.co')) return 'twitter';
        if (url.includes('instagram.com')) return 'instagram';
        if (url.includes('linkedin.com')) return 'linkedin';
        if (url.includes('youtube.com')) return 'youtube';
        if (url.includes('vk.com')) return 'vkontakte';
        if (url.includes('telegram.org') || url.includes('t.me')) return 'telegram';
        if (url.includes('whatsapp.com') || url.includes('wa.me')) return 'whatsapp';
        return 'unknown';
    };

    /**
     * Track social media clicks
     */
    FooterModule.prototype.trackSocialClick = function(platform, url) {
        try {
            if (typeof gtag !== 'undefined') {
                gtag('event', 'social_click', {
                    social_platform: platform,
                    social_url: url,
                    event_category: 'social',
                    event_label: platform
                });
            }

            if (typeof ym !== 'undefined' && window.yaMetricaId) {
                ym(window.yaMetricaId, 'reachGoal', 'social_click', {
                    platform: platform,
                    url: url
                });
            }

        } catch (error) {
            console.warn('Error tracking social click:', error);
        }
    };

    /**
     * Track newsletter events
     */
    FooterModule.prototype.trackNewsletterEvent = function(event, email, error) {
        try {
            var eventData = {
                event_category: 'newsletter',
                event_label: 'footer_form'
            };

            if (error) {
                eventData.error_message = error;
            }

            if (typeof gtag !== 'undefined') {
                gtag('event', event, eventData);
            }

            if (typeof ym !== 'undefined' && window.yaMetricaId) {
                ym(window.yaMetricaId, 'reachGoal', event, eventData);
            }

        } catch (error) {
            console.warn('Error tracking newsletter event:', error);
        }
    };

    /**
     * Handle window resize with improved mobile detection
     */
    FooterModule.prototype.handleResize = function() {
        var self = this;

        var handleResizeCallback = function() {
            var width = window.innerWidth;
            var widthChanged = Math.abs(width - self.currentWidth) > 10;

            if (widthChanged) {
                var wasMobile = self.currentWidth < 768;
                var isMobile = width < 768;

                self.currentWidth = width;

                // Update all sections based on current screen size
                self.sections.forEach(function(section) {
                    self.updateSectionState(section);
                });

            }
        };

        if ('ResizeObserver' in window) {
            this.resizeObserver = new ResizeObserver(function(entries) {
                handleResizeCallback();
            });
            this.resizeObserver.observe(document.body);
        } else {
            var resizeHandler = function() {
                clearTimeout(self.resizeTimeout);
                self.resizeTimeout = setTimeout(handleResizeCallback, 100);
            };

            window.addEventListener('resize', resizeHandler);
            this.eventListeners.push({ element: window, event: 'resize', handler: resizeHandler });
        }

        // Initial call
        handleResizeCallback();
    };

    /**
     * Observe footer animation with improved performance
     */
    FooterModule.prototype.observeFooterAnimation = function() {
        if (!('IntersectionObserver' in window)) {
            console.warn('IntersectionObserver not supported, footer animations disabled');
            return;
        }

        var observerOptions = {
            threshold: [0.1, 0.5],
            rootMargin: '0px 0px -50px 0px'
        };

        var observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible', 'animate-in');

                    var delay = Array.from(entry.target.parentElement.children).indexOf(entry.target) * 100;
                    entry.target.style.animationDelay = delay + 'ms';

                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        var animatedElements = this.footer.querySelectorAll(
            '.edsys-footer__section, .edsys-footer__brand, .edsys-footer__newsletter, .edsys-footer__newsletter-tablet, .edsys-footer__bottom'
        );

        animatedElements.forEach(function(el) {
            observer.observe(el);
        });

        this.animationObserver = observer;
    };

    /**
     * Initialize accessibility features
     */
    FooterModule.prototype.initAccessibility = function() {
        var sections = this.footer.querySelectorAll('.edsys-footer__section');
        sections.forEach(function(section, index) {
            var heading = section.querySelector('h3, h4, .edsys-footer__section-header');
            if (heading && !heading.id) {
                heading.id = 'footer-section-' + index;
            }
        });

        var links = this.footer.querySelectorAll('a');
        links.forEach(function(link) {
            if (!link.getAttribute('aria-label') && !link.textContent.trim()) {
                var img = link.querySelector('img');
                if (img && img.alt) {
                    link.setAttribute('aria-label', img.alt);
                }
            }
        });

        if (!this.footer.getAttribute('role')) {
            this.footer.setAttribute('role', 'contentinfo');
        }
    };

    /**
     * Public API methods
     */
    FooterModule.prototype.onResize = function() {
    };

    FooterModule.prototype.onPageLoad = function() {
    };

    FooterModule.prototype.destroy = function() {

        // Remove event listeners
        this.eventListeners.forEach(function(listener) {
            listener.element.removeEventListener(listener.event, listener.handler);
        });
        this.eventListeners = [];

        // Clear timeouts
        if (this.resizeTimeout) {
            clearTimeout(this.resizeTimeout);
        }

        // Disconnect observers
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }
        this.animationObserver = null;

        this.isInitialized = false;
    };

    // Export to global scope
    window.FooterModule = FooterModule;

})();