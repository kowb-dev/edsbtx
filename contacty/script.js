/**
 * Contacts Page Scripts - Fixed Version
 * Version: 1.7.0
 * Date: 2025-07-17
 * Description: Исправленная версия с корректной обработкой полей и Ajax запросов
 */

(function() {
    'use strict';

    // DOM Elements
    let contactForm = null;
    let formFields = null;
    let submitButton = null;
    let phoneInput = null;
    let countrySelector = null;
    let countryDropdown = null;
    let selectedCountry = {
        code: '+7',
        flag: '🇷🇺',
        country: 'ru'
    };

    // Form validation patterns
    const patterns = {
        email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/,
        phone: /^[\d\s\(\)\-\+]{10,}$/,
        name: /^[a-zA-Zа-яёА-ЯЁ\s\-\']{2,}$/
    };

    // Form state
    let isSubmitting = false;
    let formToken = '';

    /**
     * Initialize the contacts page functionality
     */
    function init() {
        // Wait for DOM to be ready
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupEventListeners);
        } else {
            setupEventListeners();
        }
    }

    /**
     * Setup all event listeners
     */
    function setupEventListeners() {
        // Get DOM elements
        contactForm = document.querySelector('.edsys-contact-form');
        formFields = document.querySelectorAll('.edsys-form__input, .edsys-form__textarea, .edsys-phone__input');
        submitButton = document.getElementById('submit_button');
        phoneInput = document.getElementById('phone');
        countrySelector = document.getElementById('country_selector');
        countryDropdown = document.getElementById('country_dropdown');

        if (!contactForm || !submitButton) {
            console.warn('EDS Contacts: Required form elements not found');
            return;
        }

        // Generate form token for security
        generateFormToken();

        // Setup form validation
        setupFormValidation();

        // Setup phone input
        setupPhoneInput();

        // Setup country selector
        setupCountrySelector();

        // Setup floating labels
        setupFloatingLabels();

        // Setup form submission
        setupFormSubmission();

        // Setup scroll animations
        setupScrollAnimations();

        // Setup contact links tracking
        setupContactTracking();

        // Setup social icons interactions
        setupSocialIcons();

        // Setup map interactions
        setupMapInteractions();

        // Setup keyboard navigation
        setupKeyboardNavigation();

        console.info('EDS Contacts: Page initialized successfully');
    }

    /**
     * Generate unique form token for security
     */
    function generateFormToken() {
        const timestamp = Date.now();
        const random = Math.random().toString(36).substring(2);
        formToken = btoa(timestamp + '_' + random);

        const tokenField = document.getElementById('form_token');
        if (tokenField) {
            tokenField.value = formToken;
        }
    }

    /**
     * Setup form validation
     */
    function setupFormValidation() {
        if (!formFields) return;

        formFields.forEach(function(field) {
            // Real-time validation on input
            field.addEventListener('input', debounce(function() {
                validateField(this);
            }, 300));

            // Validation on blur
            field.addEventListener('blur', function() {
                validateField(this);
            });

            // Clear errors on focus
            field.addEventListener('focus', function() {
                clearFieldError(this);
            });
        });
    }

    /**
     * Validate individual field
     */
    function validateField(field) {
        if (!field || !field.name) {
            console.warn('Field validation error: field or field.name is undefined');
            return false;
        }

        const fieldName = field.name;
        const value = field.value ? field.value.trim() : '';
        const errorElement = document.getElementById(fieldName + '_error');
        let isValid = true;
        let errorMessage = '';

        // Required field check
        if (field.hasAttribute('required') && !value) {
            isValid = false;
            errorMessage = 'Это поле обязательно для заполнения';
        }
        // Pattern validation
        else if (value) {
            switch (fieldName) {
                case 'first_name':
                case 'last_name':
                    if (!patterns.name.test(value)) {
                        isValid = false;
                        errorMessage = 'Введите корректное имя (только буквы, дефисы и апострофы)';
                    }
                    break;
                case 'email':
                    if (!patterns.email.test(value)) {
                        isValid = false;
                        errorMessage = 'Введите корректный email адрес';
                    }
                    break;
                case 'phone':
                    if (value && !patterns.phone.test(value)) {
                        isValid = false;
                        errorMessage = 'Введите корректный номер телефона';
                    }
                    break;
                case 'message':
                    if (value.length < 10) {
                        isValid = false;
                        errorMessage = 'Сообщение должно содержать минимум 10 символов';
                    }
                    break;
            }
        }

        // Update field state
        if (isValid) {
            field.classList.remove('error');
            hideFieldError(errorElement);
        } else {
            field.classList.add('error');
            showFieldError(errorElement, errorMessage);
        }

        return isValid;
    }

    /**
     * Show field error
     */
    function showFieldError(errorElement, message) {
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.classList.add('show');
        }
    }

    /**
     * Hide field error
     */
    function hideFieldError(errorElement) {
        if (errorElement) {
            errorElement.classList.remove('show');
            errorElement.textContent = '';
        }
    }

    /**
     * Clear field error
     */
    function clearFieldError(field) {
        if (!field || !field.name) return;

        const errorElement = document.getElementById(field.name + '_error');
        field.classList.remove('error');
        hideFieldError(errorElement);
    }

    /**
     * Setup phone input functionality
     */
    function setupPhoneInput() {
        if (!phoneInput) return;

        // Format phone number on input based on selected country
        phoneInput.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, '');

            // Format based on selected country
            if (selectedCountry.country === 'ru' || selectedCountry.country === 'kz') {
                // Russian/Kazakh format
                if (value.length > 0) {
                    if (value.length <= 3) {
                        value = `(${value}`;
                    } else if (value.length <= 6) {
                        value = `(${value.slice(0, 3)}) ${value.slice(3)}`;
                    } else if (value.length <= 8) {
                        value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6)}`;
                    } else {
                        value = `(${value.slice(0, 3)}) ${value.slice(3, 6)}-${value.slice(6, 8)}-${value.slice(8, 10)}`;
                    }
                }
            } else if (selectedCountry.country === 'by') {
                // Belarusian format
                if (value.length > 0) {
                    if (value.length <= 2) {
                        value = `(${value}`;
                    } else if (value.length <= 5) {
                        value = `(${value.slice(0, 2)}) ${value.slice(2)}`;
                    } else if (value.length <= 7) {
                        value = `(${value.slice(0, 2)}) ${value.slice(2, 5)}-${value.slice(5)}`;
                    } else {
                        value = `(${value.slice(0, 2)}) ${value.slice(2, 5)}-${value.slice(5, 7)}-${value.slice(7, 9)}`;
                    }
                }
            }

            e.target.value = value;
        });

        // Prevent non-numeric input
        phoneInput.addEventListener('keypress', function(e) {
            const allowedKeys = ['Backspace', 'Delete', 'Tab', 'Escape', 'Enter', 'ArrowLeft', 'ArrowRight', 'ArrowUp', 'ArrowDown'];
            if (allowedKeys.includes(e.key)) {
                return;
            }

            // Allow numbers and some formatting chars
            if (!/[\d\(\)\-\s]/.test(e.key)) {
                e.preventDefault();
            }
        });
    }

    /**
     * Setup country selector functionality
     */
    function setupCountrySelector() {
        if (!countrySelector || !countryDropdown) return;

        // Toggle dropdown on click
        countrySelector.addEventListener('click', function(e) {
            e.stopPropagation();
            countryDropdown.classList.toggle('show');
        });

        // Close dropdown on outside click
        document.addEventListener('click', function(e) {
            if (!countryDropdown.contains(e.target) && !countrySelector.contains(e.target)) {
                countryDropdown.classList.remove('show');
            }
        });

        // Handle keyboard navigation in dropdown
        countrySelector.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                countryDropdown.classList.toggle('show');
            }
        });

        // Handle country selection
        const countryOptions = countryDropdown.querySelectorAll('.edsys-country-option');
        countryOptions.forEach(function(option) {
            option.addEventListener('click', function() {
                selectCountry(this);
            });

            option.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    selectCountry(this);
                }
            });
        });

        // Set initial selection
        const defaultOption = countryDropdown.querySelector('[data-country="ru"]');
        if (defaultOption) {
            defaultOption.classList.add('selected');
        }
    }

    /**
     * Select country from dropdown
     */
    function selectCountry(option) {
        const country = option.getAttribute('data-country');
        const code = option.getAttribute('data-code');
        const flag = option.getAttribute('data-flag');

        // Update selected country
        selectedCountry = { country, code, flag };

        // Update UI
        const flagElement = countrySelector.querySelector('.edsys-phone__flag');
        const codeElement = countrySelector.querySelector('.edsys-phone__code');

        if (flagElement) flagElement.textContent = flag;
        if (codeElement) codeElement.textContent = code;

        // Update visual selection
        const countryOptions = countryDropdown.querySelectorAll('.edsys-country-option');
        countryOptions.forEach(opt => opt.classList.remove('selected'));
        option.classList.add('selected');

        // Close dropdown
        countryDropdown.classList.remove('show');

        // Clear phone input for new format
        if (phoneInput) {
            phoneInput.value = '';
            phoneInput.focus();
        }
    }

    /**
     * Setup floating labels functionality
     */
    function setupFloatingLabels() {
        const floatingFields = document.querySelectorAll('.edsys-form__field--floating');

        floatingFields.forEach(function(field) {
            const input = field.querySelector('.edsys-form__input, .edsys-form__textarea, .edsys-phone__input');
            const label = field.querySelector('.edsys-form__label');

            if (!input || !label) return;

            // Check if field has value on load
            function checkFieldValue() {
                if (input.value && input.value.trim() !== '') {
                    label.classList.add('active');
                } else {
                    label.classList.remove('active');
                }
            }

            // Initial check
            checkFieldValue();

            // Handle input events
            input.addEventListener('input', checkFieldValue);
            input.addEventListener('blur', checkFieldValue);
            input.addEventListener('focus', checkFieldValue);
        });
    }

    /**
     * Setup form submission
     */
    function setupFormSubmission() {
        if (!contactForm) return;

        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();

            if (isSubmitting) return;

            handleFormSubmission();
        });
    }

    /**
     * Handle form submission
     */
    function handleFormSubmission() {
        // Validate all fields
        let isFormValid = true;
        const requiredFields = [];

        if (!formFields) {
            console.error('Form fields not found');
            return;
        }

        formFields.forEach(function(field) {
            const fieldValid = validateField(field);
            if (!fieldValid) {
                isFormValid = false;
                if (field.hasAttribute('required')) {
                    requiredFields.push(field.name);
                }
            }
        });

        if (!isFormValid) {
            showFormError('Пожалуйста, исправьте ошибки в форме');

            // Focus on first invalid field
            const firstInvalidField = contactForm.querySelector('.edsys-form__input.error, .edsys-form__textarea.error, .edsys-phone__input.error');
            if (firstInvalidField) {
                firstInvalidField.focus();
            }

            return;
        }

        // Show loading state
        setSubmitButtonLoading(true);
        isSubmitting = true;

        // Collect form data
        const formData = new FormData(contactForm);

        // Add phone with country code
        if (phoneInput && phoneInput.value) {
            formData.set('phone', selectedCountry.code + phoneInput.value.replace(/\D/g, ''));
        }

        // Add additional security data
        formData.append('action', 'send_contact_form');
        formData.append('form_token', formToken);
        formData.append('user_agent', navigator.userAgent);
        formData.append('referrer', document.referrer);

        // Send form data
        fetch('/contacty/ajax.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(function(response) {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.text();
            })
            .then(function(text) {
                try {
                    const data = JSON.parse(text);
                    if (data.success) {
                        handleFormSuccess();
                        trackFormSubmission(true);
                    } else {
                        throw new Error(data.message || 'Ошибка отправки формы');
                    }
                } catch (parseError) {
                    console.error('JSON parse error:', parseError);
                    console.error('Response text:', text);
                    throw new Error('Ошибка обработки ответа сервера');
                }
            })
            .catch(function(error) {
                console.error('Form submission error:', error);
                handleFormError(error.message);
                trackFormSubmission(false, error.message);
            })
            .finally(function() {
                setSubmitButtonLoading(false);
                isSubmitting = false;
            });
    }

    /**
     * Track form submission
     */
    function trackFormSubmission(success, errorMessage) {
        const event = new CustomEvent('edsFormSubmit', {
            detail: {
                type: 'contact',
                success: success,
                error: errorMessage || null,
                timestamp: new Date().toISOString()
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Handle successful form submission
     */
    function handleFormSuccess() {
        // Show success message
        const successElement = document.getElementById('form_success');
        if (successElement) {
            successElement.style.display = 'flex';
        }

        // Hide error messages
        hideFormError();

        // Reset form
        contactForm.reset();

        // Clear field errors
        if (formFields) {
            formFields.forEach(function(field) {
                clearFieldError(field);
            });
        }

        // Reset floating labels
        const labels = document.querySelectorAll('.edsys-form__label');
        labels.forEach(function(label) {
            label.classList.remove('active');
        });

        // Reset country selector
        selectedCountry = { code: '+7', flag: '🇷🇺', country: 'ru' };
        if (countrySelector) {
            const flagElement = countrySelector.querySelector('.edsys-phone__flag');
            const codeElement = countrySelector.querySelector('.edsys-phone__code');
            if (flagElement) flagElement.textContent = '🇷🇺';
            if (codeElement) codeElement.textContent = '+7';
        }

        // Generate new token
        generateFormToken();

        // Scroll to success message
        if (successElement) {
            successElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }

        // Hide success message after delay
        setTimeout(function() {
            if (successElement) {
                successElement.style.display = 'none';
            }
        }, 10000);
    }

    /**
     * Handle form submission error
     */
    function handleFormError(message) {
        showFormError(message || 'Произошла ошибка при отправке сообщения. Попробуйте еще раз.');
    }

    /**
     * Show general form error
     */
    function showFormError(message) {
        const errorElement = document.getElementById('form_error');
        if (errorElement) {
            errorElement.textContent = message;
            errorElement.style.display = 'flex';

            // Scroll to error message
            errorElement.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    }

    /**
     * Hide general form error
     */
    function hideFormError() {
        const errorElement = document.getElementById('form_error');
        if (errorElement) {
            errorElement.style.display = 'none';
        }
    }

    /**
     * Set submit button loading state
     */
    function setSubmitButtonLoading(loading) {
        if (!submitButton) return;

        const buttonText = submitButton.querySelector('.edsys-button__text');
        const buttonLoading = submitButton.querySelector('.edsys-button__loading');

        if (loading) {
            submitButton.disabled = true;
            submitButton.classList.add('loading');
            if (buttonText) buttonText.style.opacity = '0';
            if (buttonLoading) buttonLoading.style.display = 'flex';
        } else {
            submitButton.disabled = false;
            submitButton.classList.remove('loading');
            if (buttonText) buttonText.style.opacity = '1';
            if (buttonLoading) buttonLoading.style.display = 'none';
        }
    }

    /**
     * Setup scroll animations
     */
    function setupScrollAnimations() {
        // Check if browser supports Intersection Observer
        if (!window.IntersectionObserver) {
            return;
        }

        const observerOptions = {
            root: null,
            rootMargin: '0px 0px -50px 0px',
            threshold: 0.1
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Animate contact cards and form
        const animatedElements = [
            ...document.querySelectorAll('.edsys-contact-card'),
            document.querySelector('.edsys-form-wrapper'),
            document.querySelector('.edsys-contacts__map')
        ].filter(Boolean);

        animatedElements.forEach(function(element, index) {
            element.style.opacity = '0';
            element.style.transform = 'translateY(30px)';
            element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            element.style.transitionDelay = (index * 0.1) + 's';

            observer.observe(element);
        });
    }

    /**
     * Setup contact links tracking
     */
    function setupContactTracking() {
        // Track phone calls
        const phoneLinks = document.querySelectorAll('.edsys-phone-inline');
        phoneLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const phone = this.getAttribute('href').replace('tel:', '');
                trackContactAction('phone', phone);
            });
        });

        // Track email clicks
        const emailLinks = document.querySelectorAll('.edsys-email-link');
        emailLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const email = this.getAttribute('href').replace('mailto:', '');
                trackContactAction('email', email);
            });
        });

        // Track map interactions
        const mapLinks = document.querySelectorAll('.edsys-map-link, .edsys-info-action');
        mapLinks.forEach(function(link) {
            link.addEventListener('click', function() {
                const action = this.classList.contains('edsys-info-action') ? 'map_action' : 'map_external';
                trackContactAction('map', action);
            });
        });
    }

    /**
     * Track contact action
     */
    function trackContactAction(type, value) {
        const event = new CustomEvent('edsContactAction', {
            detail: {
                type: type,
                value: value,
                timestamp: new Date().toISOString()
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Setup social icons interactions
     */
    function setupSocialIcons() {
        const socialIcons = document.querySelectorAll('.edsys-social-icon');

        socialIcons.forEach(function(icon) {
            // Add ripple effect on click
            icon.addEventListener('click', function(e) {
                const href = this.getAttribute('href');
                const platform = this.classList.contains('edsys-social-icon--whatsapp') ? 'whatsapp' :
                    this.classList.contains('edsys-social-icon--vk') ? 'vk' :
                        this.classList.contains('edsys-social-icon--youtube') ? 'youtube' : 'unknown';

                // Create ripple effect
                createRippleEffect(this, e);

                // Track social media click
                trackContactAction('social', platform);
            });

            // Add hover effect for better UX
            icon.addEventListener('mouseenter', function() {
                this.style.transform = 'scale(1.1)';
            });

            icon.addEventListener('mouseleave', function() {
                this.style.transform = 'scale(1)';
            });
        });
    }

    /**
     * Setup map interactions
     */
    function setupMapInteractions() {
        const mapInfoOverlay = document.getElementById('map-info-overlay');
        const mapWidget = document.querySelector('.edsys-map__widget');

        if (!mapInfoOverlay || !mapWidget) return;

        // Track map interactions
        mapWidget.addEventListener('click', function() {
            trackContactAction('map', 'map_interaction');
        });

        // Track info overlay actions
        const infoActions = mapInfoOverlay.querySelectorAll('.edsys-info-action');
        infoActions.forEach(function(action) {
            action.addEventListener('click', function(e) {
                const actionType = this.getAttribute('href').startsWith('tel:') ? 'phone' : 'route';
                trackContactAction('map_overlay', actionType);
            });
        });
    }

    /**
     * Setup keyboard navigation
     */
    function setupKeyboardNavigation() {
        // Add keyboard navigation for country dropdown
        if (countryDropdown) {
            const countryOptions = countryDropdown.querySelectorAll('.edsys-country-option');

            countryOptions.forEach(function(option, index) {
                option.setAttribute('tabindex', '0');

                option.addEventListener('keydown', function(e) {
                    let nextIndex;

                    switch(e.key) {
                        case 'ArrowDown':
                            e.preventDefault();
                            nextIndex = (index + 1) % countryOptions.length;
                            countryOptions[nextIndex].focus();
                            break;
                        case 'ArrowUp':
                            e.preventDefault();
                            nextIndex = (index - 1 + countryOptions.length) % countryOptions.length;
                            countryOptions[nextIndex].focus();
                            break;
                        case 'Escape':
                            countryDropdown.classList.remove('show');
                            if (countrySelector) countrySelector.focus();
                            break;
                    }
                });
            });
        }

        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Escape key closes dropdowns
            if (e.key === 'Escape') {
                if (countryDropdown) {
                    countryDropdown.classList.remove('show');
                }
            }
        });
    }

    /**
     * Create ripple effect on social icon click
     */
    function createRippleEffect(element, event) {
        const rect = element.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = event.clientX - rect.left - size / 2;
        const y = event.clientY - rect.top - size / 2;

        const ripple = document.createElement('span');
        ripple.style.cssText = `
            position: absolute;
            left: ${x}px;
            top: ${y}px;
            width: ${size}px;
            height: ${size}px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.3);
            transform: scale(0);
            animation: ripple 0.6s ease-out;
            pointer-events: none;
        `;

        // Add keyframe animation if not exists
        if (!document.getElementById('ripple-animation')) {
            const style = document.createElement('style');
            style.id = 'ripple-animation';
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(2);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
        }

        element.appendChild(ripple);

        // Remove ripple after animation
        setTimeout(function() {
            if (ripple.parentNode) {
                ripple.parentNode.removeChild(ripple);
            }
        }, 600);
    }

    /**
     * Utility function to debounce function calls
     */
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = function() {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    /**
     * Error handling wrapper
     */
    function handleError(error, context) {
        console.error('EDS Contacts Error (' + context + '):', error);

        // Dispatch error event for monitoring
        const event = new CustomEvent('edsError', {
            detail: {
                context: context,
                message: error.message,
                stack: error.stack,
                timestamp: new Date().toISOString()
            }
        });
        document.dispatchEvent(event);
    }

    /**
     * Public API for external access
     */
    window.EDSContacts = {
        init: init,
        validateField: validateField,
        submitForm: handleFormSubmission,
        trackAction: trackContactAction,
        version: '1.7.0'
    };

    // Auto-initialize
    init();

})();