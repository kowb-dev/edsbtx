/**
 * EDS Individual Solutions JavaScript - Fixed Validation Logic
 * Version: 8.3.0 - Fixed step validation synchronization
 * Date: 2025-07-19
 * File: /individualnye-resheniya/script.js
 */

(function() {
    'use strict';

    // Force light theme immediately
    if (document.documentElement) {
        document.documentElement.setAttribute('data-theme', 'light');
    }
    if (document.body) {
        document.body.classList.remove('dark-theme');
        document.body.classList.add('light-theme');
    }

    // Check if script is already loaded
    if (window.EDSIndividualSolutionsLoaded) {
        console.log('EDS Individual Solutions script already loaded');
        return;
    }

    window.EDSIndividualSolutionsLoaded = true;

    // Wait for template modules to load
    function waitForTemplateModules(callback) {
        var checkInterval = setInterval(function() {
            if (window.EDSMainLoaded || window.FooterModule || window.HeaderModule) {
                clearInterval(checkInterval);
                callback();
            }
        }, 100);

        setTimeout(function() {
            clearInterval(checkInterval);
            callback();
        }, 3000);
    }

    // Technical Specification Wizard Class
    function EDSTechnicalWizard() {
        this.currentStep = 1;
        this.totalSteps = 8;
        this.formData = {};
        this.uploadedFiles = [];
        this.maxFiles = 5;
        this.maxFileSize = 10 * 1024 * 1024;
        this.isSubmitting = false;
        this.autoScrollEnabled = false;
        this.isNavigating = false;

        this.init();
    }

    EDSTechnicalWizard.prototype.init = function() {
        this.bindEvents();
        this.updateUI();
        this.initRangeSliders();
        this.initToggleControls();
        this.initRadioCards();
        this.initFormValidation();
        this.initFileUpload();
        this.loadProgress();
        this.forceLightTheme();

        console.log('EDS Wizard initialized - fixed validation approach');
    };

    // Force light theme across all elements
    EDSTechnicalWizard.prototype.forceLightTheme = function() {
        if (document.documentElement) {
            document.documentElement.classList.remove('dark-theme', 'dark');
            document.documentElement.classList.add('light-theme', 'light');
            document.documentElement.setAttribute('data-theme', 'light');
        }

        if (document.body) {
            document.body.classList.remove('dark-theme', 'dark');
            document.body.classList.add('light-theme', 'light');
            document.body.setAttribute('data-theme', 'light');
        }

        // Override CSS variables for light theme
        if (document.head && !document.querySelector('#force-light-theme')) {
            var style = document.createElement('style');
            style.id = 'force-light-theme';
            style.textContent = `
                :root, [data-theme="light"], .light-theme {
                    --edsys-bg: #ffffff !important;
                    --edsys-white: #ffffff !important;
                    --edsys-text: #1a1a1a !important;
                    --edsys-text-muted: #666666 !important;
                    --edsys-border: #e0e0e0 !important;
                    --edsys-accent: #ff2545 !important;
                    --edsys-circuit: #00cc99 !important;
                    --edsys-voltage: #0066cc !important;
                    --edsys-power: #1a1a1a !important;
                    background-color: #ffffff !important;
                    color: #1a1a1a !important;
                }
                
                body, .edsys-wizard, .edsys-wizard__content, .edsys-wizard__step-content {
                    background-color: #ffffff !important;
                    color: #1a1a1a !important;
                }
                
                .edsys-radio-card, .edsys-toggle, .edsys-wizard__input {
                    background-color: #ffffff !important;
                    color: #1a1a1a !important;
                    border-color: #e0e0e0 !important;
                }
                
                .edsys-wizard__results {
                    background-color: #ffffff !important;
                    color: #1a1a1a !important;
                }
            `;
            document.head.appendChild(style);
        }
    };

    EDSTechnicalWizard.prototype.bindEvents = function() {
        var self = this;

        var prevBtn = document.getElementById('wizardPrevBtn');
        var nextBtn = document.getElementById('wizardNextBtn');

        if (prevBtn) {
            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                self.previousStep();
            });
        }

        if (nextBtn) {
            // Use debounced version to prevent multiple calls
            var debouncedNext = self.debounce(function() {
                self.nextStepWithValidation();
            }, 300); // 300ms debounce

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Prevent multiple calls during navigation
                if (self.isNavigating) {
                    console.log('Navigation in progress - ignoring click');
                    return;
                }

                debouncedNext();
            });
        }

        var downloadBtn = document.getElementById('wizardDownloadPDF');
        var createNewBtn = document.getElementById('wizardCreateNew');

        if (downloadBtn) {
            downloadBtn.addEventListener('click', function() {
                self.downloadPDF();
            });
        }

        if (createNewBtn) {
            createNewBtn.addEventListener('click', function() {
                self.resetWizard();
            });
        }

        // Remove keyboard navigation to prevent issues
        document.addEventListener('keydown', function(e) {
            if (e.ctrlKey || e.metaKey) {
                if (e.key === 'ArrowRight') {
                    e.preventDefault();
                    if (!self.isNavigating) {
                        self.nextStepWithValidation();
                    }
                } else if (e.key === 'ArrowLeft') {
                    e.preventDefault();
                    if (!self.isNavigating) {
                        self.previousStep();
                    }
                }
            }
        });

        window.addEventListener('beforeunload', function() {
            self.saveProgress();
        });
    };

    // Debounce utility function
    EDSTechnicalWizard.prototype.debounce = function(func, wait) {
        var timeout;
        return function() {
            var context = this;
            var args = arguments;
            clearTimeout(timeout);
            timeout = setTimeout(function() {
                func.apply(context, args);
            }, wait);
        };
    };

    // Navigation with validation - FIXED to validate correct step
    EDSTechnicalWizard.prototype.nextStepWithValidation = function() {
        if (this.isSubmitting || this.isNavigating) {
            console.log('Already processing - skipping navigation');
            return;
        }

        this.isNavigating = true;
        console.log('User navigation started - step', this.currentStep);

        var self = this;
        var stepToValidate = this.currentStep; // Store current step before any changes

        setTimeout(function() {
            try {
                // Collect current step data first
                self.collectCurrentStepData();

                // Validate the step we're trying to leave (NOT the active UI step)
                if (self.validateStep(stepToValidate)) {
                    console.log('Validation passed - proceeding to next step');
                    self.saveProgress();
                    self.proceedToNextStep();
                } else {
                    console.log('Validation failed - staying on current step');
                    self.showValidationErrors(stepToValidate);
                }
            } catch (error) {
                console.error('Navigation error:', error);
            } finally {
                // Reset navigation flag after a delay
                setTimeout(function() {
                    self.isNavigating = false;
                }, 100);
            }
        }, 10);
    };

    EDSTechnicalWizard.prototype.proceedToNextStep = function() {
        if (this.currentStep < this.totalSteps) {
            this.currentStep++;
            this.updateUI();
            console.log('Moved to step', this.currentStep);
        } else {
            // Final step - submit form
            this.submitForm();
        }
    };

    EDSTechnicalWizard.prototype.previousStep = function() {
        if (this.isNavigating || this.currentStep <= 1) {
            return;
        }

        this.isNavigating = true;
        console.log('Moving to previous step (no validation)');

        var self = this;
        setTimeout(function() {
            try {
                self.collectCurrentStepData();
                self.currentStep--;
                self.updateUI();
            } finally {
                setTimeout(function() {
                    self.isNavigating = false;
                }, 100);
            }
        }, 10);
    };

    EDSTechnicalWizard.prototype.updateUI = function() {
        console.log('Updating UI for step', this.currentStep);

        var progressBar = document.querySelector('.edsys-wizard__progress-bar');
        if (progressBar) {
            var progressPercent = (this.currentStep / this.totalSteps) * 100;
            progressBar.style.width = progressPercent + '%';
        }

        var steps = document.querySelectorAll('.edsys-wizard__step');
        var self = this;
        steps.forEach(function(step, index) {
            var stepNumber = index + 1;
            step.classList.remove('active', 'completed');

            if (stepNumber === self.currentStep) {
                step.classList.add('active');
            } else if (stepNumber < self.currentStep) {
                step.classList.add('completed');
            }
        });

        var contents = document.querySelectorAll('.edsys-wizard__step-content');
        contents.forEach(function(content, index) {
            var stepNumber = index + 1;
            content.classList.remove('active');

            if (stepNumber === self.currentStep) {
                content.classList.add('active');
            }
        });

        var prevBtn = document.getElementById('wizardPrevBtn');
        var nextBtn = document.getElementById('wizardNextBtn');

        if (prevBtn) {
            prevBtn.disabled = this.currentStep === 1;
        }

        if (nextBtn) {
            if (this.currentStep === this.totalSteps) {
                nextBtn.innerHTML = '<i class="ph ph-thin ph-check"></i> Создать ТЗ';
                nextBtn.classList.add('edsys-wizard__btn--create');
            } else {
                nextBtn.innerHTML = 'Далее <i class="ph ph-thin ph-arrow-right"></i>';
                nextBtn.classList.remove('edsys-wizard__btn--create');
            }
        }
    };

    // FIXED: Validate specific step instead of active UI step
    EDSTechnicalWizard.prototype.validateStep = function(stepNumber) {
        console.log('Validating step', stepNumber);

        var stepContent = document.querySelector('.edsys-wizard__step-content[data-step="' + stepNumber + '"]');
        if (!stepContent) {
            console.warn('Step content not found for step', stepNumber);
            return true;
        }

        var requiredFields = stepContent.querySelectorAll('[required]');
        var isValid = true;
        var self = this;

        // Validate required fields
        requiredFields.forEach(function(field) {
            if (!self.validateField(field)) {
                isValid = false;
                console.log('Field validation failed:', field.name, field.type);
            }
        });

        // Special validation for equipment selection on step 5
        if (stepNumber === 5) {
            var equipmentCheckboxes = stepContent.querySelectorAll('input[name="equipment[]"]');
            var hasSelected = Array.from(equipmentCheckboxes).some(function(cb) {
                return cb.checked;
            });

            if (!hasSelected) {
                isValid = false;
                console.log('No equipment selected on step 5');
            } else {
                console.log('Equipment validation passed:', this.formData.equipment);
            }
        }

        console.log('Step', stepNumber, 'validation result:', isValid);
        return isValid;
    };

    // Keep the old method for backward compatibility but redirect to new one
    EDSTechnicalWizard.prototype.validateCurrentStep = function() {
        return this.validateStep(this.currentStep);
    };

    EDSTechnicalWizard.prototype.validateField = function(field) {
        if (field.type === 'radio') {
            var radioGroup = document.querySelectorAll('[name="' + field.name + '"]');
            return Array.from(radioGroup).some(function(radio) {
                return radio.checked;
            });
        } else if (field.type === 'checkbox' && field.name.includes('[]')) {
            var checkboxGroup = document.querySelectorAll('[name="' + field.name + '"]');
            return Array.from(checkboxGroup).some(function(checkbox) {
                return checkbox.checked;
            });
        } else if (field.type === 'checkbox') {
            return field.checked;
        } else if (field.type === 'email') {
            var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return field.value.trim() !== '' && emailRegex.test(field.value);
        } else if (field.type === 'tel') {
            var phoneClean = field.value.replace(/[^\d]/g, '');
            return phoneClean.length >= 10;
        } else {
            return field.value.trim() !== '';
        }
    };

    // FIXED: Show validation errors for specific step
    EDSTechnicalWizard.prototype.showValidationErrors = function(stepNumber) {
        stepNumber = stepNumber || this.currentStep;

        var stepContent = document.querySelector('.edsys-wizard__step-content[data-step="' + stepNumber + '"]');
        var self = this;

        console.log('Showing validation errors for step', stepNumber);

        if (!stepContent) {
            console.warn('Step content not found for error display');
            return;
        }

        if (!document.querySelector('#shakeAnimation')) {
            var style = document.createElement('style');
            style.id = 'shakeAnimation';
            style.textContent = '@keyframes shake { 0%, 100% { transform: translateX(0); } 25% { transform: translateX(-8px); } 75% { transform: translateX(8px); } } .shake-error { animation: shake 0.5s ease-in-out; border-color: var(--edsys-accent) !important; } .edsys-wizard__toggle-group.shake-error { background-color: rgba(255, 37, 69, 0.05); border-radius: 8px; padding: 10px; }';
            document.head.appendChild(style);
        }

        var message = 'Пожалуйста, заполните все обязательные поля';

        if (stepNumber === 5) {
            var equipmentCheckboxes = stepContent.querySelectorAll('input[name="equipment[]"]');
            var hasSelected = Array.from(equipmentCheckboxes).some(function(cb) {
                return cb.checked;
            });
            if (!hasSelected) {
                message = 'Пожалуйста, выберите хотя бы один тип оборудования';

                var toggleGroup = stepContent.querySelector('.edsys-wizard__toggle-group');
                if (toggleGroup) {
                    toggleGroup.classList.add('shake-error');
                    setTimeout(function() {
                        toggleGroup.classList.remove('shake-error');
                    }, 500);
                }
            }
        }

        var requiredFields = stepContent.querySelectorAll('[required]');
        requiredFields.forEach(function(field) {
            if (!self.validateField(field)) {
                if (field.type === 'radio') {
                    var radioCards = stepContent.querySelectorAll('input[name="' + field.name + '"]');
                    radioCards.forEach(function(radio) {
                        var card = radio.closest('.edsys-radio-card');
                        if (card) {
                            card.classList.add('shake-error');
                            setTimeout(function() {
                                card.classList.remove('shake-error');
                            }, 500);
                        }
                    });
                } else {
                    field.classList.add('shake-error');
                    setTimeout(function() {
                        field.classList.remove('shake-error');
                    }, 500);
                }
            }
        });

        this.showNotification(message, 'error');
    };

    EDSTechnicalWizard.prototype.showNotification = function(message, type) {
        type = type || 'info';

        var currentScrollY = window.pageYOffset || document.documentElement.scrollTop;

        var existingNotifications = document.querySelectorAll('.edsys-notification');
        existingNotifications.forEach(function(n) {
            n.remove();
        });

        var notification = document.createElement('div');
        notification.className = 'edsys-notification edsys-notification--' + type;

        var icon = type === 'error' ? 'warning' : type === 'success' ? 'check' : 'info';
        notification.innerHTML = '<i class="ph ph-thin ph-' + icon + '"></i><span>' + message + '</span>';

        if (!document.querySelector('#notificationStyles')) {
            var style = document.createElement('style');
            style.id = 'notificationStyles';
            style.textContent = '.edsys-notification { position: fixed; top: 2rem; left: 50%; transform: translateX(-50%); background: #ffffff !important; border: 2px solid; border-radius: 8px; padding: 16px 24px; box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1); z-index: 10001; display: flex; align-items: center; gap: 8px; animation: slideInDown 0.3s ease; max-width: 90vw; font-weight: 500; } .edsys-notification--error { border-color: #ff2545 !important; color: #ff2545 !important; } .edsys-notification--success { border-color: #00cc99 !important; color: #00cc99 !important; } .edsys-notification--info { border-color: #0066cc !important; color: #0066cc !important; } @keyframes slideInDown { from { opacity: 0; transform: translateX(-50%) translateY(-2rem); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }';
            document.head.appendChild(style);
        }

        document.body.appendChild(notification);

        setTimeout(function() {
            window.scrollTo(0, currentScrollY);
        }, 10);

        setTimeout(function() {
            if (notification.parentNode) {
                notification.style.animation = 'slideInDown 0.3s ease reverse';
                setTimeout(function() {
                    if (notification.parentNode) {
                        notification.parentNode.removeChild(notification);
                    }
                }, 300);
            }
        }, 4000);
    };

    // Improved data collection - collect all form data at once
    EDSTechnicalWizard.prototype.collectCurrentStepData = function() {
        var form = document.getElementById('wizardForm');
        if (!form) return;

        console.log('Collecting form data for current state');

        // Process all form fields
        var allInputs = form.querySelectorAll('input, select, textarea');
        allInputs.forEach(function(input) {
            var fieldName = input.name;
            var fieldValue = input.value;

            if (!fieldName) return;

            if (input.type === 'radio') {
                if (input.checked) {
                    this.formData[fieldName] = fieldValue;
                }
            } else if (input.type === 'checkbox') {
                if (fieldName.includes('[]')) {
                    var cleanFieldName = fieldName.replace('[]', '');
                    if (!this.formData[cleanFieldName]) {
                        this.formData[cleanFieldName] = [];
                    }

                    if (input.checked) {
                        if (this.formData[cleanFieldName].indexOf(fieldValue) === -1) {
                            this.formData[cleanFieldName].push(fieldValue);
                        }
                    } else {
                        // Remove unchecked items
                        var index = this.formData[cleanFieldName].indexOf(fieldValue);
                        if (index > -1) {
                            this.formData[cleanFieldName].splice(index, 1);
                        }
                    }
                } else {
                    this.formData[fieldName] = input.checked;
                }
            } else if (input.type === 'range') {
                if (fieldValue) {
                    this.formData[fieldName] = fieldValue;
                }
            } else if (input.type === 'file') {
                // Skip file inputs
            } else if (input.type !== 'radio' && input.type !== 'checkbox') {
                if (fieldValue.trim() !== '') {
                    this.formData[fieldName] = fieldValue;
                }
            }
        }.bind(this));

        // Clean up empty arrays
        Object.keys(this.formData).forEach(function(key) {
            if (Array.isArray(this.formData[key]) && this.formData[key].length === 0) {
                delete this.formData[key];
            }
        }.bind(this));

        console.log('Collected form data:', this.formData);
    };

    // Toggle controls with better event handling
    EDSTechnicalWizard.prototype.initToggleControls = function() {
        var self = this;

        setTimeout(function() {
            var toggles = document.querySelectorAll('.edsys-toggle');
            console.log('Initializing', toggles.length, 'toggle controls');

            toggles.forEach(function(toggle, index) {
                var checkbox = toggle.querySelector('input[type="checkbox"]');

                if (!checkbox) {
                    console.warn('Toggle', index, 'has no checkbox');
                    return;
                }

                console.log('Setting up toggle', index, 'for checkbox:', checkbox.name, checkbox.value);

                // Remove any existing event listeners
                var newToggle = toggle.cloneNode(true);
                toggle.parentNode.replaceChild(newToggle, toggle);

                // Get references to new elements
                toggle = newToggle;
                checkbox = toggle.querySelector('input[type="checkbox"]');

                // Set initial state
                if (checkbox.checked) {
                    toggle.classList.add('active');
                    console.log('Toggle', index, 'set to active (checked)');
                }

                // Main click handler for the toggle container
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    console.log('Toggle clicked:', checkbox.name, checkbox.value);

                    // Toggle the checkbox state
                    checkbox.checked = !checkbox.checked;

                    // Update visual state
                    toggle.classList.toggle('active', checkbox.checked);

                    console.log('Toggle state changed to:', checkbox.checked);

                    // Trigger change event
                    var changeEvent = new Event('change', { bubbles: true });
                    checkbox.dispatchEvent(changeEvent);

                    // Update form data immediately for step 5
                    if (self.currentStep === 5 && checkbox.name === 'equipment[]') {
                        self.collectCurrentStepData();
                        console.log('Equipment data updated:', self.formData.equipment);
                    }
                });

                // Keyboard support
                toggle.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        toggle.click();
                    }
                });

                // Direct checkbox change handler
                checkbox.addEventListener('change', function(e) {
                    e.stopPropagation();
                    toggle.classList.toggle('active', checkbox.checked);
                    console.log('Checkbox change event:', checkbox.name, checkbox.value, checkbox.checked);
                });

                // Prevent checkbox clicks from bubbling
                checkbox.addEventListener('click', function(e) {
                    e.stopPropagation();
                });

                console.log('Toggle', index, 'initialized successfully');
            });

            console.log('All toggles initialized');
        }, 100);
    };

    EDSTechnicalWizard.prototype.initRadioCards = function() {
        var radioCards = document.querySelectorAll('.edsys-radio-card');
        console.log('Initializing', radioCards.length, 'radio cards');

        radioCards.forEach(function(card) {
            var radio = card.querySelector('input[type="radio"]');

            if (radio) {
                var handleSelection = function(e) {
                    if (e.target === radio) return;

                    radio.checked = true;

                    var groupName = radio.name;
                    var groupRadios = document.querySelectorAll('input[name="' + groupName + '"]');
                    groupRadios.forEach(function(r) {
                        var parentCard = r.closest('.edsys-radio-card');
                        if (parentCard) {
                            parentCard.classList.remove('active');
                        }
                    });

                    card.classList.add('active');
                    radio.dispatchEvent(new Event('change', { bubbles: true }));
                };

                card.addEventListener('click', handleSelection);

                card.addEventListener('keydown', function(e) {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        handleSelection(e);
                    }
                });

                radio.addEventListener('change', function() {
                    if (radio.checked) {
                        var groupName = radio.name;
                        var groupRadios = document.querySelectorAll('input[name="' + groupName + '"]');
                        groupRadios.forEach(function(r) {
                            var parentCard = r.closest('.edsys-radio-card');
                            if (parentCard && parentCard !== card) {
                                parentCard.classList.remove('active');
                            }
                        });
                        card.classList.add('active');
                    }
                });
            }
        });
    };

    // Rest of the methods remain the same (submitForm, file handling, etc.)
    EDSTechnicalWizard.prototype.submitForm = function() {
        if (this.isSubmitting) return;

        this.isSubmitting = true;
        this.showLoading();

        var self = this;

        setTimeout(function() {
            try {
                self.collectCurrentStepData();

                console.log('Final form data before submission:', self.formData);

                if (!self.formData.equipment || self.formData.equipment.length === 0) {
                    throw new Error('Необходимо выбрать хотя бы один тип оборудования');
                }

                var formData = new FormData();

                formData.append('action', 'submit_wizard');
                formData.append('data', JSON.stringify(self.formData));

                if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
                    formData.append('sessid', BX.bitrix_sessid());
                } else {
                    formData.append('sessid', 'dummy_sessid');
                }

                self.uploadedFiles.forEach(function(file, index) {
                    formData.append('attachments[]', file);
                });

                console.log('Submitting form data:', self.formData);

                var controller = new AbortController();
                var timeoutId = setTimeout(function() {
                    controller.abort();
                }, 30000);

                fetch('/individualnye-resheniya/handler.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData,
                    signal: controller.signal
                }).then(function(response) {
                    clearTimeout(timeoutId);

                    if (!response.ok) {
                        throw new Error('Ошибка сервера: ' + response.status + ' ' + response.statusText);
                    }

                    var contentType = response.headers.get('content-type');
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('Сервер вернул некорректный ответ');
                    }

                    return response.json();
                }).then(function(result) {
                    console.log('Server response:', result);

                    if (result && result.success) {
                        self.showResults(result);
                        self.clearProgress();
                    } else {
                        throw new Error(result ? (result.message || 'Неизвестная ошибка сервера') : 'Пустой ответ от сервера');
                    }
                }).catch(function(error) {
                    clearTimeout(timeoutId);
                    console.error('Submission error:', error);

                    var errorMessage = 'Произошла ошибка при создании ТЗ. Пожалуйста, попробуйте еще раз.';

                    if (error.name === 'AbortError') {
                        errorMessage = 'Превышено время ожидания. Проверьте подключение к интернету и попробуйте еще раз.';
                    } else if (error.message.includes('Failed to fetch')) {
                        errorMessage = 'Ошибка подключения. Проверьте интернет-соединение и попробуйте еще раз.';
                    } else if (error.message) {
                        errorMessage = error.message;
                    }

                    self.showNotification(errorMessage, 'error');
                }).finally(function() {
                    self.isSubmitting = false;
                    self.hideLoading();
                });

            } catch (error) {
                console.error('Submission error:', error);
                self.showNotification(
                    error.message || 'Произошла ошибка при создании ТЗ. Пожалуйста, попробуйте еще раз.',
                    'error'
                );
                self.isSubmitting = false;
                self.hideLoading();
            }
        }, 100);
    };

    EDSTechnicalWizard.prototype.showLoading = function() {
        var form = document.querySelector('.edsys-wizard__content');
        var actions = document.querySelector('.edsys-wizard__actions');
        var loading = document.querySelector('.edsys-wizard__loading');

        if (form) form.style.display = 'none';
        if (actions) actions.style.display = 'none';
        if (loading) loading.classList.add('active');
    };

    EDSTechnicalWizard.prototype.hideLoading = function() {
        var loading = document.querySelector('.edsys-wizard__loading');
        if (loading) loading.classList.remove('active');
    };

    EDSTechnicalWizard.prototype.showResults = function(result) {
        var results = document.querySelector('.edsys-wizard__results');
        var downloadBtn = document.getElementById('wizardDownloadPDF');
        var form = document.querySelector('.edsys-wizard__content');
        var actions = document.querySelector('.edsys-wizard__actions');

        if (form) form.style.display = 'none';
        if (actions) actions.style.display = 'none';

        if (results) {
            results.classList.add('active');

            if (result.pdfUrl && downloadBtn) {
                downloadBtn.dataset.pdfUrl = result.pdfUrl;
            }
        }

        // Scroll wizard section to center after showing results
        var self = this;
        setTimeout(function() {
            var wizardSection = document.querySelector('.edsys-wizard');
            if (wizardSection) {
                wizardSection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center',
                    inline: 'center'
                });
            }
        }, 100);

        this.showNotification('Техническое задание успешно создано!', 'success');
    };

    EDSTechnicalWizard.prototype.downloadPDF = function() {
        var self = this;

        var controller = new AbortController();
        var timeoutId = setTimeout(function() {
            controller.abort();
        }, 15000);

        fetch('/individualnye-resheniya/handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'generate_pdf',
                data: this.formData
            }),
            signal: controller.signal
        }).then(function(response) {
            clearTimeout(timeoutId);
            if (response.ok) {
                return response.blob();
            } else {
                throw new Error('Ошибка генерации PDF');
            }
        }).then(function(blob) {
            var url = URL.createObjectURL(blob);
            var a = document.createElement('a');
            a.href = url;
            a.download = 'ТЗ_EDS_' + new Date().toISOString().split('T')[0] + '.pdf';
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);

            self.showNotification('PDF готов к сохранению!', 'success');
        }).catch(function(error) {
            clearTimeout(timeoutId);
            console.error('PDF generation error:', error);

            var errorMessage = 'Ошибка при генерации PDF';
            if (error.name === 'AbortError') {
                errorMessage = 'Превышено время генерации PDF. Попробуйте еще раз.';
            }

            self.showNotification(errorMessage, 'error');
        });
    };

    EDSTechnicalWizard.prototype.resetWizard = function() {
        this.currentStep = 1;
        this.formData = {};
        this.uploadedFiles = [];
        this.isSubmitting = false;
        this.isNavigating = false;

        var form = document.getElementById('wizardForm');
        if (form) form.reset();

        var results = document.querySelector('.edsys-wizard__results');
        var formEl = document.querySelector('.edsys-wizard__content');
        var actions = document.querySelector('.edsys-wizard__actions');

        if (results) results.classList.remove('active');
        if (formEl) formEl.style.display = 'block';
        if (actions) actions.style.display = 'flex';

        var toggles = document.querySelectorAll('.edsys-toggle');
        toggles.forEach(function(toggle) {
            toggle.classList.remove('active');
            var checkbox = toggle.querySelector('input[type="checkbox"]');
            if (checkbox) checkbox.checked = false;
        });

        var radioCards = document.querySelectorAll('.edsys-radio-card');
        radioCards.forEach(function(card) {
            card.classList.remove('active');
        });

        var fileList = document.getElementById('fileList');
        if (fileList) fileList.innerHTML = '';

        this.clearProgress();
        this.updateUI();
        this.forceLightTheme();

        // Re-initialize toggles
        this.initToggleControls();
    };

    // Simplified file handling methods
    EDSTechnicalWizard.prototype.initFileUpload = function() {
        var fileInput = document.getElementById('fileInput');
        var fileDropZone = document.getElementById('fileDropZone');
        var fileUploadButton = document.querySelector('.edsys-file-upload__button');
        var self = this;

        if (!fileInput || !fileDropZone || !fileUploadButton) return;

        fileInput.addEventListener('change', function(e) {
            self.handleFiles(Array.from(e.target.files));
        });

        fileUploadButton.addEventListener('click', function(e) {
            e.preventDefault();
            fileInput.click();
        });

        fileDropZone.addEventListener('dragover', function(e) {
            e.preventDefault();
            fileDropZone.classList.add('edsys-file-upload__zone--dragover');
        });

        fileDropZone.addEventListener('dragleave', function(e) {
            e.preventDefault();
            if (!fileDropZone.contains(e.relatedTarget)) {
                fileDropZone.classList.remove('edsys-file-upload__zone--dragover');
            }
        });

        fileDropZone.addEventListener('drop', function(e) {
            e.preventDefault();
            fileDropZone.classList.remove('edsys-file-upload__zone--dragover');
            var files = Array.from(e.dataTransfer.files);
            self.handleFiles(files);
        });
    };

    EDSTechnicalWizard.prototype.handleFiles = function(files) {
        var self = this;
        files.forEach(function(file) {
            if (self.uploadedFiles.length >= self.maxFiles) {
                self.showNotification('Можно загрузить максимум ' + self.maxFiles + ' файлов', 'error');
                return;
            }
            if (file.size > self.maxFileSize) {
                self.showNotification('Файл "' + file.name + '" слишком большой. Максимальный размер: 10 МБ', 'error');
                return;
            }
            if (!self.isValidFileType(file)) {
                self.showNotification('Неподдерживаемый тип файла: "' + file.name + '"', 'error');
                return;
            }
            var isDuplicate = self.uploadedFiles.some(function(uploadedFile) {
                return uploadedFile.name === file.name && uploadedFile.size === file.size;
            });
            if (isDuplicate) {
                self.showNotification('Файл "' + file.name + '" уже добавлен', 'error');
                return;
            }
            self.uploadedFiles.push(file);
            self.addFileToList(file);
        });
        this.updateFileUploadUI();
    };

    // Other utility methods
    EDSTechnicalWizard.prototype.isValidFileType = function(file) {
        var allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.ms-excel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'image/jpeg', 'image/jpg', 'image/png', 'image/vnd.dwg', 'image/vnd.dxf'];
        var allowedExtensions = ['.pdf', '.doc', '.docx', '.xls', '.xlsx', '.jpg', '.jpeg', '.png', '.dwg', '.dxf'];
        return allowedTypes.includes(file.type) || allowedExtensions.some(function(ext) {
            return file.name.toLowerCase().endsWith(ext);
        });
    };

    EDSTechnicalWizard.prototype.addFileToList = function(file) {
        var fileList = document.getElementById('fileList');
        if (!fileList) return;
        var fileItem = document.createElement('div');
        fileItem.className = 'edsys-file-item';
        fileItem.dataset.fileName = file.name;
        var fileExtension = file.name.split('.').pop().toLowerCase();
        fileItem.dataset.fileType = fileExtension;
        var fileSize = this.formatFileSize(file.size);
        var fileIcon = this.getFileIcon(file);
        var self = this;
        fileItem.innerHTML = '<div class="edsys-file-item__icon"><i class="ph ph-thin ph-' + fileIcon + '"></i></div><div class="edsys-file-item__info"><div class="edsys-file-item__name" title="' + file.name + '">' + file.name + '</div><div class="edsys-file-item__size">' + fileSize + '</div></div><button type="button" class="edsys-file-item__remove" title="Удалить файл"><i class="ph ph-thin ph-x"></i></button>';
        var removeBtn = fileItem.querySelector('.edsys-file-item__remove');
        removeBtn.addEventListener('click', function() {
            self.removeFile(file.name);
        });
        fileList.appendChild(fileItem);
    };

    EDSTechnicalWizard.prototype.removeFile = function(fileName) {
        this.uploadedFiles = this.uploadedFiles.filter(function(file) {
            return file.name !== fileName;
        });
        var fileItem = document.querySelector('[data-file-name="' + fileName + '"]');
        if (fileItem) {
            fileItem.remove();
        }
        this.updateFileUploadUI();
    };

    EDSTechnicalWizard.prototype.updateFileUploadUI = function() {
        var fileDropZone = document.getElementById('fileDropZone');
        var fileList = document.getElementById('fileList');
        if (this.uploadedFiles.length > 0) {
            fileDropZone.classList.add('edsys-file-upload__zone--has-files');
            fileList.style.display = 'block';
        } else {
            fileDropZone.classList.remove('edsys-file-upload__zone--has-files');
            fileList.style.display = 'none';
        }
        var uploadButton = document.querySelector('.edsys-file-upload__button');
        if (uploadButton) {
            var filesLeft = this.maxFiles - this.uploadedFiles.length;
            if (filesLeft > 0) {
                uploadButton.textContent = 'выберите файлы (осталось: ' + filesLeft + ')';
                uploadButton.disabled = false;
            } else {
                uploadButton.textContent = 'максимум файлов загружено';
                uploadButton.disabled = true;
            }
        }
    };

    EDSTechnicalWizard.prototype.formatFileSize = function(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    };

    EDSTechnicalWizard.prototype.getFileIcon = function(file) {
        var ext = file.name.split('.').pop().toLowerCase();
        var iconMap = {
            'pdf': 'file-pdf', 'doc': 'file-doc', 'docx': 'file-doc',
            'xls': 'file-xls', 'xlsx': 'file-xls', 'jpg': 'file-image',
            'jpeg': 'file-image', 'png': 'file-image', 'dwg': 'file', 'dxf': 'file'
        };
        return iconMap[ext] || 'file';
    };

    EDSTechnicalWizard.prototype.initRangeSliders = function() {
        var audienceRange = document.getElementById('audienceRange');
        var audienceValue = document.getElementById('audienceValue');
        var audienceLabels = ['До 50', '50-200', '200-500', '500-1000', '1000-5000', '5000+'];

        if (audienceRange && audienceValue) {
            var updateAudienceValue = function() {
                var value = parseInt(audienceRange.value);
                audienceValue.textContent = audienceLabels[value - 1] + ' человек';
                audienceRange.dataset.actualValue = value;
            };
            audienceRange.addEventListener('input', updateAudienceValue);
            updateAudienceValue();
        }

        var performersRange = document.getElementById('performersRange');
        var performersValue = document.getElementById('performersValue');
        var performersLabels = ['1-5', '5-15', '15-30', '30-50', '50+'];

        if (performersRange && performersValue) {
            var updatePerformersValue = function() {
                var value = parseInt(performersRange.value);
                performersValue.textContent = performersLabels[value - 1] + ' человек';
                performersRange.dataset.actualValue = value;
            };
            performersRange.addEventListener('input', updatePerformersValue);
            updatePerformersValue();
        }
    };

    EDSTechnicalWizard.prototype.initFormValidation = function() {
        var phoneInput = document.getElementById('contactPhone');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                var value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.startsWith('8')) {
                        value = '7' + value.slice(1);
                    }
                    if (value.startsWith('7')) {
                        value = value.slice(0, 11);
                        if (value.length >= 1) {
                            var formatted = '+7';
                            if (value.length > 1) {
                                formatted += ' (' + value.slice(1, 4);
                                if (value.length > 4) {
                                    formatted += ') ' + value.slice(4, 7);
                                    if (value.length > 7) {
                                        formatted += '-' + value.slice(7, 9);
                                        if (value.length > 9) {
                                            formatted += '-' + value.slice(9, 11);
                                        }
                                    }
                                }
                            }
                            e.target.value = formatted;
                        }
                    }
                }
            });
        }

        var emailInput = document.getElementById('contactEmail');
        if (emailInput) {
            emailInput.addEventListener('blur', function(e) {
                var email = e.target.value.trim();
                var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (email && !emailRegex.test(email)) {
                    e.target.style.borderColor = '#ff2545';
                } else {
                    e.target.style.borderColor = '';
                }
            });
        }
    };

    EDSTechnicalWizard.prototype.saveProgress = function() {
        try {
            var progressData = {
                currentStep: this.currentStep,
                formData: this.formData,
                timestamp: Date.now()
            };
            localStorage.setItem('edsWizardProgress', JSON.stringify(progressData));
        } catch (error) {
            console.warn('Could not save progress:', error);
        }
    };

    EDSTechnicalWizard.prototype.loadProgress = function() {
        try {
            var item = localStorage.getItem('edsWizardProgress');
            var saved = item ? JSON.parse(item) : null;
            if (saved) {
                var hoursSinceLastSave = (Date.now() - saved.timestamp) / (1000 * 60 * 60);
                if (hoursSinceLastSave < 24) {
                    this.currentStep = saved.currentStep || 1;
                    this.formData = saved.formData || {};
                    this.restoreFormValues();
                    this.updateUI();
                }
            }
        } catch (error) {
            console.warn('Could not load progress:', error);
        }
    };

    EDSTechnicalWizard.prototype.restoreFormValues = function() {
        var self = this;
        Object.keys(this.formData).forEach(function(key) {
            var value = self.formData[key];
            if (Array.isArray(value)) {
                value.forEach(function(val) {
                    var checkbox = document.querySelector('input[name="' + key + '[]"][value="' + val + '"]');
                    if (checkbox) {
                        checkbox.checked = true;
                        var toggle = checkbox.closest('.edsys-toggle');
                        if (toggle) toggle.classList.add('active');
                    }
                });
            } else {
                var field = document.querySelector('input[name="' + key + '"], select[name="' + key + '"], textarea[name="' + key + '"]');
                if (field) {
                    if (field.type === 'radio') {
                        var radio = document.querySelector('input[name="' + key + '"][value="' + value + '"]');
                        if (radio) {
                            radio.checked = true;
                            var card = radio.closest('.edsys-radio-card');
                            if (card) card.classList.add('active');
                        }
                    } else if (field.type === 'checkbox') {
                        field.checked = value;
                        var toggle = field.closest('.edsys-toggle');
                        if (toggle) toggle.classList.toggle('active', value);
                    } else if (field.type === 'range') {
                        field.value = value;
                    } else if (field.type !== 'file') {
                        field.value = value;
                    }
                }
            }
        });
        this.initRangeSliders();
    };

    EDSTechnicalWizard.prototype.clearProgress = function() {
        try {
            localStorage.removeItem('edsWizardProgress');
        } catch (error) {
            console.warn('Could not clear progress:', error);
        }
    };

    // Consultation Form Handler
    function EDSConsultationForm() {
        this.form = document.querySelector('.edsys-consultation__form');
        this.isSubmitting = false;
        this.init();
    }

    EDSConsultationForm.prototype.init = function() {
        if (this.form) {
            var self = this;
            this.form.addEventListener('submit', function(e) {
                self.handleSubmit(e);
            });
            this.initPhoneFormatting();
        }
    };

    EDSConsultationForm.prototype.initPhoneFormatting = function() {
        var phoneInput = this.form.querySelector('input[type="tel"]');
        if (phoneInput) {
            phoneInput.addEventListener('input', function(e) {
                var value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    if (value.startsWith('8')) {
                        value = '7' + value.slice(1);
                    }
                    if (value.startsWith('7')) {
                        value = value.slice(0, 11);
                        var formatted = '+7';
                        if (value.length > 1) {
                            formatted += ' (' + value.slice(1, 4);
                            if (value.length > 4) {
                                formatted += ') ' + value.slice(4, 7);
                                if (value.length > 7) {
                                    formatted += '-' + value.slice(7, 9);
                                    if (value.length > 9) {
                                        formatted += '-' + value.slice(9, 11);
                                    }
                                }
                            }
                        }
                        e.target.value = formatted;
                    }
                }
            });
        }
    };

    EDSConsultationForm.prototype.handleSubmit = function(e) {
        e.preventDefault();
        if (this.isSubmitting) return;
        var formData = new FormData(this.form);
        var data = {};
        formData.forEach(function(value, key) {
            data[key] = value;
        });
        if (!data.consultationName || !data.consultationPhone) {
            this.showNotification('Пожалуйста, заполните все поля', 'error');
            return;
        }
        var phoneClean = data.consultationPhone.replace(/[^\d]/g, '');
        if (phoneClean.length < 10) {
            this.showNotification('Пожалуйста, введите корректный номер телефона', 'error');
            return;
        }
        this.isSubmitting = true;
        var btn = this.form.querySelector('.edsys-consultation__btn');
        var originalText = btn.innerHTML;
        var self = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="ph ph-thin ph-spinner"></i> Отправляем...';
        var controller = new AbortController();
        var timeoutId = setTimeout(function() {
            controller.abort();
        }, 15000);
        fetch('/individualnye-resheniya/handler.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'consultation_request',
                data: data
            }),
            signal: controller.signal
        }).then(function(response) {
            clearTimeout(timeoutId);
            return response.json();
        }).then(function(result) {
            if (result.success) {
                self.showNotification('Заявка отправлена! Мы свяжемся с вами в ближайшее время.', 'success');
                self.form.reset();
            } else {
                throw new Error(result.message || 'Ошибка при отправке заявки');
            }
        }).catch(function(error) {
            clearTimeout(timeoutId);
            console.error('Consultation form error:', error);
            var errorMessage = 'Ошибка при отправке заявки';
            if (error.name === 'AbortError') {
                errorMessage = 'Превышено время ожидания. Попробуйте еще раз.';
            } else if (error.message) {
                errorMessage = error.message;
            }
            self.showNotification(errorMessage, 'error');
        }).finally(function() {
            self.isSubmitting = false;
            btn.disabled = false;
            btn.innerHTML = originalText;
        });
    };

    EDSConsultationForm.prototype.showNotification = function(message, type) {
        if (window.edsWizard && window.edsWizard.showNotification) {
            window.edsWizard.showNotification(message, type);
        } else {
            alert(message);
        }
    };

    // Global scroll functions
    window.scrollToWizard = function() {
        var wizard = document.querySelector('.edsys-wizard');
        if (wizard) {
            wizard.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }
    };

    window.scrollToAdvantages = function() {
        var advantages = document.querySelector('.edsys-advantages');
        if (advantages) {
            advantages.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };

    window.scrollToConsultation = function() {
        var consultation = document.querySelector('.edsys-consultation');
        if (consultation) {
            consultation.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };

    // Initialize everything when DOM is loaded
    function initializeEDS() {
        try {
            if (!document.body || !document.documentElement) {
                setTimeout(initializeEDS, 50);
                return;
            }

            console.log('Initializing EDS Individual Solutions...');

            window.edsWizard = new EDSTechnicalWizard();
            window.edsConsultation = new EDSConsultationForm();

            // Force light theme on all dynamic elements
            setTimeout(function() {
                if (document.documentElement) {
                    document.documentElement.setAttribute('data-theme', 'light');
                }
                if (document.body) {
                    document.body.classList.add('light-theme');
                }
            }, 100);

            console.log('EDS Individual Solutions initialized successfully');
        } catch (error) {
            console.error('Error initializing EDS page:', error);
        }
    }

    // Wait for template modules first, then initialize
    waitForTemplateModules(function() {
        function checkDOMReady() {
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initializeEDS);
            } else if (document.body && document.documentElement) {
                initializeEDS();
            } else {
                setTimeout(checkDOMReady, 50);
            }
        }
        checkDOMReady();
    });

})();