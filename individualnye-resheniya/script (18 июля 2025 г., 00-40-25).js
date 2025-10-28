/**
 * Fixed EDS Individual Solutions Page JavaScript
 * Technical Specification Wizard and Page Interactions
 * Version: 7.0 - Fixed data collection and validation
 */

(function() {
    'use strict';

    // Check if script is already loaded
    if (window.EDSPageLoaded) {
        console.log('EDS script already loaded');
        return;
    }

    window.EDSPageLoaded = true;

    // Technical Specification Wizard Class
    class EDSTechnicalWizard {
        constructor() {
            this.currentStep = 1;
            this.totalSteps = 8;
            this.formData = {};
            this.uploadedFiles = [];
            this.maxFiles = 5;
            this.maxFileSize = 10 * 1024 * 1024; // 10MB
            this.isSubmitting = false;

            this.init();
        }

        init() {
            this.bindEvents();
            this.updateUI();
            this.initRangeSliders();
            this.initToggleControls();
            this.initRadioCards();
            this.initFormValidation();
            this.initFileUpload();
        }

        bindEvents() {
            // Navigation buttons
            const prevBtn = document.getElementById('wizardPrevBtn');
            const nextBtn = document.getElementById('wizardNextBtn');

            if (prevBtn) {
                prevBtn.addEventListener('click', () => this.previousStep());
            }

            if (nextBtn) {
                nextBtn.addEventListener('click', () => this.nextStep());
            }

            // Results actions
            const downloadBtn = document.getElementById('wizardDownloadPDF');
            const createNewBtn = document.getElementById('wizardCreateNew');

            if (downloadBtn) {
                downloadBtn.addEventListener('click', () => this.downloadPDF());
            }

            if (createNewBtn) {
                createNewBtn.addEventListener('click', () => this.resetWizard());
            }

            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.ctrlKey || e.metaKey) {
                    if (e.key === 'ArrowRight') {
                        e.preventDefault();
                        this.nextStep();
                    } else if (e.key === 'ArrowLeft') {
                        e.preventDefault();
                        this.previousStep();
                    }
                }
            });

            // Auto-save progress
            window.addEventListener('beforeunload', () => {
                this.saveProgress();
            });

            // Load saved progress
            this.loadProgress();
        }

        nextStep() {
            if (this.isSubmitting) return;

            if (this.validateCurrentStep()) {
                this.collectCurrentStepData();
                this.saveProgress();

                if (this.currentStep < this.totalSteps) {
                    this.currentStep++;
                    this.updateUI();
                    this.scrollToWizard();
                } else {
                    this.submitForm();
                }
            } else {
                this.showValidationErrors();
            }
        }

        previousStep() {
            if (this.currentStep > 1) {
                this.currentStep--;
                this.updateUI();
                this.scrollToWizard();
            }
        }

        scrollToWizard() {
            const wizard = document.querySelector('.edsys-wizard');
            if (wizard) {
                wizard.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        }

        updateUI() {
            // Update progress bar
            const progressBar = document.querySelector('.edsys-wizard__progress-bar');
            if (progressBar) {
                const progressPercent = (this.currentStep / this.totalSteps) * 100;
                progressBar.style.width = `${progressPercent}%`;
            }

            // Update step indicators
            document.querySelectorAll('.edsys-wizard__step').forEach((step, index) => {
                const stepNumber = index + 1;
                step.classList.remove('active', 'completed');

                if (stepNumber === this.currentStep) {
                    step.classList.add('active');
                } else if (stepNumber < this.currentStep) {
                    step.classList.add('completed');
                }
            });

            // Update step content
            document.querySelectorAll('.edsys-wizard__step-content').forEach((content, index) => {
                const stepNumber = index + 1;
                content.classList.remove('active');

                if (stepNumber === this.currentStep) {
                    content.classList.add('active');
                }
            });

            // Update navigation buttons
            const prevBtn = document.getElementById('wizardPrevBtn');
            const nextBtn = document.getElementById('wizardNextBtn');

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

            // Focus management for accessibility
            setTimeout(() => {
                const activeContent = document.querySelector('.edsys-wizard__step-content.active');
                if (activeContent) {
                    const firstFocusable = activeContent.querySelector('input, [tabindex="0"]');
                    if (firstFocusable && !this.isSubmitting) {
                        firstFocusable.focus();
                    }
                }
            }, 100);
        }

        validateCurrentStep() {
            const activeContent = document.querySelector('.edsys-wizard__step-content.active');
            if (!activeContent) return false;

            const requiredFields = activeContent.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    isValid = false;
                }
            });

            // Special validation for equipment step (step 5)
            if (this.currentStep === 5) {
                const equipmentCheckboxes = activeContent.querySelectorAll('input[name="equipment[]"]');
                const hasSelected = Array.from(equipmentCheckboxes).some(cb => cb.checked);
                if (!hasSelected) {
                    isValid = false;
                }
            }

            return isValid;
        }

        validateField(field) {
            if (field.type === 'radio') {
                const radioGroup = document.querySelectorAll(`[name="${field.name}"]`);
                return Array.from(radioGroup).some(radio => radio.checked);
            } else if (field.type === 'checkbox' && field.name.includes('[]')) {
                const checkboxGroup = document.querySelectorAll(`[name="${field.name}"]`);
                return Array.from(checkboxGroup).some(checkbox => checkbox.checked);
            } else if (field.type === 'checkbox') {
                return field.checked;
            } else if (field.type === 'email') {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return field.value.trim() !== '' && emailRegex.test(field.value);
            } else if (field.type === 'tel') {
                const phoneClean = field.value.replace(/[^\d]/g, '');
                return phoneClean.length >= 10;
            } else {
                return field.value.trim() !== '';
            }
        }

        showValidationErrors() {
            const activeContent = document.querySelector('.edsys-wizard__step-content.active');

            // Add shake animation if not exists
            if (!document.querySelector('#shakeAnimation')) {
                const style = document.createElement('style');
                style.id = 'shakeAnimation';
                style.textContent = `
                    @keyframes shake {
                        0%, 100% { transform: translateX(0); }
                        25% { transform: translateX(-8px); }
                        75% { transform: translateX(8px); }
                    }
                    .shake-error {
                        animation: shake 0.5s ease-in-out;
                        border-color: var(--edsys-accent) !important;
                    }
                `;
                document.head.appendChild(style);
            }

            // Apply shake animation to invalid fields
            const requiredFields = activeContent.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!this.validateField(field)) {
                    if (field.type === 'radio') {
                        const radioCards = activeContent.querySelectorAll(`input[name="${field.name}"]`);
                        radioCards.forEach(radio => {
                            const card = radio.closest('.edsys-radio-card');
                            if (card) {
                                card.classList.add('shake-error');
                                setTimeout(() => card.classList.remove('shake-error'), 500);
                            }
                        });
                    } else if (field.type === 'checkbox' && field.name.includes('[]')) {
                        const toggles = activeContent.querySelectorAll(`input[name="${field.name}"]`);
                        toggles.forEach(checkbox => {
                            const toggle = checkbox.closest('.edsys-toggle');
                            if (toggle) {
                                toggle.classList.add('shake-error');
                                setTimeout(() => toggle.classList.remove('shake-error'), 500);
                            }
                        });
                    } else {
                        field.classList.add('shake-error');
                        setTimeout(() => field.classList.remove('shake-error'), 500);
                    }
                }
            });

            // Special validation message for equipment step
            let message = 'Пожалуйста, заполните все обязательные поля';
            if (this.currentStep === 5) {
                const equipmentCheckboxes = activeContent.querySelectorAll('input[name="equipment[]"]');
                const hasSelected = Array.from(equipmentCheckboxes).some(cb => cb.checked);
                if (!hasSelected) {
                    message = 'Пожалуйста, выберите хотя бы один тип оборудования';
                }
            }

            this.showNotification(message, 'error');
        }

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

            // Add notification styles if not exists
            if (!document.querySelector('#notificationStyles')) {
                const style = document.createElement('style');
                style.id = 'notificationStyles';
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

        // FIXED: Collect current step data with proper handling of all form elements
        collectCurrentStepData() {
            const activeContent = document.querySelector('.edsys-wizard__step-content.active');
            const inputs = activeContent.querySelectorAll('input, select, textarea');

            console.log('Collecting data from step', this.currentStep);

            inputs.forEach(input => {
                const fieldName = input.name;
                const fieldValue = input.value;

                console.log('Processing field:', fieldName, 'value:', fieldValue, 'type:', input.type);

                if (input.type === 'radio' && input.checked) {
                    this.formData[fieldName] = fieldValue;
                    console.log('Set radio value:', fieldName, '=', fieldValue);
                } else if (input.type === 'checkbox') {
                    if (fieldName.includes('[]')) {
                        // Multiple checkbox field
                        const cleanFieldName = fieldName.replace('[]', '');
                        if (!this.formData[cleanFieldName]) {
                            this.formData[cleanFieldName] = [];
                        }

                        if (input.checked) {
                            if (!this.formData[cleanFieldName].includes(fieldValue)) {
                                this.formData[cleanFieldName].push(fieldValue);
                            }
                        } else {
                            const index = this.formData[cleanFieldName].indexOf(fieldValue);
                            if (index > -1) {
                                this.formData[cleanFieldName].splice(index, 1);
                            }
                        }
                        console.log('Updated checkbox array:', cleanFieldName, '=', this.formData[cleanFieldName]);
                    } else {
                        // Single checkbox field
                        this.formData[fieldName] = input.checked;
                        console.log('Set checkbox value:', fieldName, '=', input.checked);
                    }
                } else if (input.type === 'range') {
                    // FIXED: Properly handle range slider values
                    this.formData[fieldName] = fieldValue;
                    console.log('Set range value:', fieldName, '=', fieldValue);
                } else if (input.type !== 'radio' && input.type !== 'checkbox') {
                    // Text inputs, selects, textareas
                    if (fieldValue.trim() !== '') {
                        this.formData[fieldName] = fieldValue;
                        console.log('Set text value:', fieldName, '=', fieldValue);
                    }
                }
            });

            console.log('Current form data:', this.formData);
        }

        async submitForm() {
            if (this.isSubmitting) return;

            this.isSubmitting = true;
            this.showLoading();

            try {
                // Collect final step data
                this.collectCurrentStepData();

                console.log('Final form data before submission:', this.formData);

                // Validate final data
                if (!this.formData.equipment || this.formData.equipment.length === 0) {
                    throw new Error('Необходимо выбрать хотя бы один тип оборудования');
                }

                // Create FormData for file upload
                const formData = new FormData();

                // Add form data
                formData.append('action', 'submit_wizard');
                formData.append('data', JSON.stringify(this.formData));
                formData.append('sessid', BX?.bitrix_sessid() || 'dummy_sessid');

                // Add files
                this.uploadedFiles.forEach((file, index) => {
                    formData.append(`attachments[]`, file);
                });

                console.log('Submitting form data:', this.formData);

                // Submit to server
                const response = await fetch('/individualnye-resheniya/handler.php', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                        // Don't set Content-Type - let browser set it for FormData
                    },
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                console.log('Server response:', result);

                if (result.success) {
                    this.showResults(result);
                    this.clearProgress();
                } else {
                    throw new Error(result.message || 'Ошибка при обработке запроса');
                }

            } catch (error) {
                console.error('Submission error:', error);
                this.showNotification(
                    error.message || 'Произошла ошибка при создании ТЗ. Пожалуйста, попробуйте еще раз.',
                    'error'
                );
            } finally {
                this.isSubmitting = false;
                this.hideLoading();
            }
        }

        showLoading() {
            const form = document.querySelector('.edsys-wizard__content');
            const actions = document.querySelector('.edsys-wizard__actions');
            const loading = document.querySelector('.edsys-wizard__loading');

            if (form) form.style.display = 'none';
            if (actions) actions.style.display = 'none';
            if (loading) loading.classList.add('active');
        }

        hideLoading() {
            const loading = document.querySelector('.edsys-wizard__loading');
            if (loading) loading.classList.remove('active');
        }

        showResults(result) {
            const results = document.querySelector('.edsys-wizard__results');
            const downloadBtn = document.getElementById('wizardDownloadPDF');

            if (results) {
                results.classList.add('active');

                if (result.pdfUrl && downloadBtn) {
                    downloadBtn.dataset.pdfUrl = result.pdfUrl;
                }
            }

            this.scrollToWizard();
            this.showNotification('Техническое задание успешно создано!', 'success');
        }

        async downloadPDF() {
            const downloadBtn = document.getElementById('wizardDownloadPDF');
            const pdfUrl = downloadBtn?.dataset.pdfUrl;

            if (pdfUrl) {
                try {
                    // Direct download from server URL
                    const link = document.createElement('a');
                    link.href = pdfUrl;
                    link.download = `ТЗ_EDS_${new Date().toISOString().split('T')[0]}.pdf`;
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);

                    this.showNotification('PDF готов к сохранению!', 'success');
                } catch (error) {
                    console.error('Download error:', error);
                    this.showNotification('Ошибка при скачивании PDF', 'error');
                }
            } else {
                try {
                    // Generate PDF via API
                    const response = await fetch('/individualnye-resheniya/handler.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            action: 'generate_pdf',
                            data: this.formData
                        })
                    });

                    if (response.ok) {
                        const blob = await response.blob();
                        const url = URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.href = url;
                        a.download = `ТЗ_EDS_${new Date().toISOString().split('T')[0]}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        document.body.removeChild(a);
                        URL.revokeObjectURL(url);

                        this.showNotification('PDF готов к сохранению!', 'success');
                    } else {
                        throw new Error('Ошибка генерации PDF');
                    }
                } catch (error) {
                    console.error('PDF generation error:', error);
                    this.showNotification('Ошибка при генерации PDF', 'error');
                }
            }
        }

        resetWizard() {
            this.currentStep = 1;
            this.formData = {};
            this.uploadedFiles = [];
            this.isSubmitting = false;

            const form = document.getElementById('wizardForm');
            if (form) form.reset();

            const results = document.querySelector('.edsys-wizard__results');
            const formEl = document.querySelector('.edsys-wizard__content');
            const actions = document.querySelector('.edsys-wizard__actions');

            if (results) results.classList.remove('active');
            if (formEl) formEl.style.display = 'block';
            if (actions) actions.style.display = 'flex';

            // Reset all interactive elements
            document.querySelectorAll('.edsys-toggle').forEach(toggle => {
                toggle.classList.remove('active');
                const checkbox = toggle.querySelector('input[type="checkbox"]');
                if (checkbox) checkbox.checked = false;
            });

            document.querySelectorAll('.edsys-radio-card').forEach(card => {
                card.classList.remove('active');
            });

            // Clear file list
            const fileList = document.getElementById('fileList');
            if (fileList) fileList.innerHTML = '';

            this.clearProgress();
            this.updateUI();
            this.scrollToWizard();
        }

        initFileUpload() {
            const fileInput = document.getElementById('fileInput');
            const fileDropZone = document.getElementById('fileDropZone');
            const fileUploadButton = document.querySelector('.edsys-file-upload__button');

            if (!fileInput || !fileDropZone || !fileUploadButton) return;

            // File input change handler
            fileInput.addEventListener('change', (e) => {
                this.handleFiles(Array.from(e.target.files));
            });

            // Upload button click handler
            fileUploadButton.addEventListener('click', (e) => {
                e.preventDefault();
                fileInput.click();
            });

            // Drag and drop handlers
            fileDropZone.addEventListener('dragover', (e) => {
                e.preventDefault();
                fileDropZone.classList.add('edsys-file-upload__zone--dragover');
            });

            fileDropZone.addEventListener('dragleave', (e) => {
                e.preventDefault();
                if (!fileDropZone.contains(e.relatedTarget)) {
                    fileDropZone.classList.remove('edsys-file-upload__zone--dragover');
                }
            });

            fileDropZone.addEventListener('drop', (e) => {
                e.preventDefault();
                fileDropZone.classList.remove('edsys-file-upload__zone--dragover');

                const files = Array.from(e.dataTransfer.files);
                this.handleFiles(files);
            });
        }

        handleFiles(files) {
            files.forEach(file => {
                if (this.uploadedFiles.length >= this.maxFiles) {
                    this.showNotification(`Можно загрузить максимум ${this.maxFiles} файлов`, 'error');
                    return;
                }

                if (file.size > this.maxFileSize) {
                    this.showNotification(`Файл "${file.name}" слишком большой. Максимальный размер: 10 МБ`, 'error');
                    return;
                }

                if (!this.isValidFileType(file)) {
                    this.showNotification(`Неподдерживаемый тип файла: "${file.name}"`, 'error');
                    return;
                }

                // Check for duplicates
                const isDuplicate = this.uploadedFiles.some(uploadedFile =>
                    uploadedFile.name === file.name && uploadedFile.size === file.size
                );

                if (isDuplicate) {
                    this.showNotification(`Файл "${file.name}" уже добавлен`, 'error');
                    return;
                }

                this.uploadedFiles.push(file);
                this.addFileToList(file);
            });

            this.updateFileUploadUI();
        }

        isValidFileType(file) {
            const allowedTypes = [
                'application/pdf',
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'image/jpeg',
                'image/jpg',
                'image/png',
                'image/vnd.dwg',
                'image/vnd.dxf'
            ];

            const allowedExtensions = [
                '.pdf', '.doc', '.docx', '.xls', '.xlsx',
                '.jpg', '.jpeg', '.png', '.dwg', '.dxf'
            ];

            return allowedTypes.includes(file.type) ||
                allowedExtensions.some(ext => file.name.toLowerCase().endsWith(ext));
        }

        addFileToList(file) {
            const fileList = document.getElementById('fileList');
            if (!fileList) return;

            const fileItem = document.createElement('div');
            fileItem.className = 'edsys-file-item';
            fileItem.dataset.fileName = file.name;

            // Add file type for styling
            const fileExtension = file.name.split('.').pop().toLowerCase();
            fileItem.dataset.fileType = fileExtension;

            const fileSize = this.formatFileSize(file.size);
            const fileIcon = this.getFileIcon(file);

            fileItem.innerHTML = `
                <div class="edsys-file-item__icon">
                    <i class="ph ph-thin ph-${fileIcon}"></i>
                </div>
                <div class="edsys-file-item__info">
                    <div class="edsys-file-item__name" title="${file.name}">${file.name}</div>
                    <div class="edsys-file-item__size">${fileSize}</div>
                </div>
                <button type="button" class="edsys-file-item__remove" title="Удалить файл">
                    <i class="ph ph-thin ph-x"></i>
                </button>
            `;

            // Add remove handler
            const removeBtn = fileItem.querySelector('.edsys-file-item__remove');
            removeBtn.addEventListener('click', () => {
                this.removeFile(file.name);
            });

            fileList.appendChild(fileItem);
        }

        removeFile(fileName) {
            this.uploadedFiles = this.uploadedFiles.filter(file => file.name !== fileName);

            const fileItem = document.querySelector(`[data-file-name="${fileName}"]`);
            if (fileItem) {
                fileItem.remove();
            }

            this.updateFileUploadUI();
        }

        updateFileUploadUI() {
            const fileDropZone = document.getElementById('fileDropZone');
            const fileList = document.getElementById('fileList');

            if (this.uploadedFiles.length > 0) {
                fileDropZone.classList.add('edsys-file-upload__zone--has-files');
                fileList.style.display = 'block';
            } else {
                fileDropZone.classList.remove('edsys-file-upload__zone--has-files');
                fileList.style.display = 'none';
            }

            // Update upload button text
            const uploadButton = document.querySelector('.edsys-file-upload__button');
            if (uploadButton) {
                const filesLeft = this.maxFiles - this.uploadedFiles.length;
                if (filesLeft > 0) {
                    uploadButton.textContent = `выберите файлы (осталось: ${filesLeft})`;
                } else {
                    uploadButton.textContent = 'максимум файлов загружено';
                    uploadButton.disabled = true;
                }
            }
        }

        formatFileSize(bytes) {
            if (bytes === 0) return '0 Bytes';
            const k = 1024;
            const sizes = ['Bytes', 'KB', 'MB', 'GB'];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
        }

        getFileIcon(file) {
            const ext = file.name.split('.').pop().toLowerCase();

            const iconMap = {
                'pdf': 'file-pdf',
                'doc': 'file-doc',
                'docx': 'file-doc',
                'xls': 'file-xls',
                'xlsx': 'file-xls',
                'jpg': 'file-image',
                'jpeg': 'file-image',
                'png': 'file-image',
                'dwg': 'file',
                'dxf': 'file'
            };

            return iconMap[ext] || 'file';
        }

        // FIXED: Range sliders with proper value mapping
        initRangeSliders() {
            const audienceRange = document.getElementById('audienceRange');
            const audienceValue = document.getElementById('audienceValue');
            const audienceLabels = ['До 50', '50-200', '200-500', '500-1000', '1000-5000', '5000+'];

            if (audienceRange && audienceValue) {
                const updateAudienceValue = () => {
                    const value = parseInt(audienceRange.value);
                    audienceValue.textContent = audienceLabels[value - 1] + ' человек';

                    // Store the actual index value for form submission
                    audienceRange.dataset.actualValue = value;
                };

                audienceRange.addEventListener('input', updateAudienceValue);
                updateAudienceValue();
            }

            const performersRange = document.getElementById('performersRange');
            const performersValue = document.getElementById('performersValue');
            const performersLabels = ['1-5', '5-15', '15-30', '30-50', '50+'];

            if (performersRange && performersValue) {
                const updatePerformersValue = () => {
                    const value = parseInt(performersRange.value);
                    performersValue.textContent = performersLabels[value - 1] + ' человек';

                    // Store the actual index value for form submission
                    performersRange.dataset.actualValue = value;
                };

                performersRange.addEventListener('input', updatePerformersValue);
                updatePerformersValue();
            }
        }

        initToggleControls() {
            document.querySelectorAll('.edsys-toggle').forEach(toggle => {
                const checkbox = toggle.querySelector('input[type="checkbox"]');

                if (checkbox) {
                    // Set initial state
                    if (checkbox.checked) {
                        toggle.classList.add('active');
                    }

                    const handleToggle = (e) => {
                        if (e.target === checkbox) return;

                        checkbox.checked = !checkbox.checked;
                        toggle.classList.toggle('active', checkbox.checked);
                        checkbox.dispatchEvent(new Event('change', { bubbles: true }));
                    };

                    toggle.addEventListener('click', handleToggle);

                    toggle.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            handleToggle(e);
                        }
                    });

                    checkbox.addEventListener('change', () => {
                        toggle.classList.toggle('active', checkbox.checked);
                    });
                }
            });
        }

        initRadioCards() {
            document.querySelectorAll('.edsys-radio-card').forEach(card => {
                const radio = card.querySelector('input[type="radio"]');

                if (radio) {
                    const handleSelection = (e) => {
                        if (e.target === radio) return;

                        radio.checked = true;

                        const groupName = radio.name;
                        document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                            const parentCard = r.closest('.edsys-radio-card');
                            if (parentCard) {
                                parentCard.classList.remove('active');
                            }
                        });

                        card.classList.add('active');
                        radio.dispatchEvent(new Event('change', { bubbles: true }));
                    };

                    card.addEventListener('click', handleSelection);

                    card.addEventListener('keydown', (e) => {
                        if (e.key === 'Enter' || e.key === ' ') {
                            e.preventDefault();
                            handleSelection(e);
                        }
                    });

                    radio.addEventListener('change', () => {
                        if (radio.checked) {
                            const groupName = radio.name;
                            document.querySelectorAll(`input[name="${groupName}"]`).forEach(r => {
                                const parentCard = r.closest('.edsys-radio-card');
                                if (parentCard && parentCard !== card) {
                                    parentCard.classList.remove('active');
                                }
                            });
                            card.classList.add('active');
                        }
                    });
                }
            });
        }

        initFormValidation() {
            const phoneInput = document.getElementById('contactPhone');
            if (phoneInput) {
                phoneInput.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value.length > 0) {
                        if (value.startsWith('8')) {
                            value = '7' + value.slice(1);
                        }

                        if (value.startsWith('7')) {
                            value = value.slice(0, 11);
                            if (value.length >= 1) {
                                let formatted = '+7';
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

                phoneInput.addEventListener('blur', (e) => {
                    const phoneClean = e.target.value.replace(/[^\d]/g, '');
                    const isValid = phoneClean.length >= 10;

                    if (e.target.value && !isValid) {
                        e.target.style.borderColor = 'var(--edsys-accent)';
                    } else {
                        e.target.style.borderColor = '';
                    }
                });
            }

            const emailInput = document.getElementById('contactEmail');
            if (emailInput) {
                emailInput.addEventListener('blur', (e) => {
                    const email = e.target.value.trim();
                    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

                    if (email && !emailRegex.test(email)) {
                        e.target.style.borderColor = 'var(--edsys-accent)';
                    } else {
                        e.target.style.borderColor = '';
                    }
                });
            }

            // Real-time validation for all required fields
            document.querySelectorAll('input[required], textarea[required]').forEach(field => {
                field.addEventListener('input', () => {
                    if (this.validateField(field)) {
                        field.style.borderColor = '';
                    }
                });
            });
        }

        saveProgress() {
            try {
                const progressData = {
                    currentStep: this.currentStep,
                    formData: this.formData,
                    timestamp: Date.now()
                    // Note: We don't save files as they can't be serialized
                };
                localStorage.setItem('edsWizardProgress', JSON.stringify(progressData));
            } catch (error) {
                console.warn('Could not save progress to localStorage:', error);
            }
        }

        loadProgress() {
            try {
                const saved = localStorage.getItem('edsWizardProgress');
                if (saved) {
                    const progressData = JSON.parse(saved);

                    // Only restore if saved within last 24 hours
                    const hoursSinceLastSave = (Date.now() - progressData.timestamp) / (1000 * 60 * 60);
                    if (hoursSinceLastSave < 24) {
                        this.currentStep = progressData.currentStep || 1;
                        this.formData = progressData.formData || {};

                        this.restoreFormValues();
                        this.updateUI();

                        if (this.currentStep > 1) {
                            this.showNotification('Восстановлен сохранённый прогресс', 'info');
                        }
                    }
                }
            } catch (error) {
                console.warn('Could not load progress from localStorage:', error);
            }
        }

        restoreFormValues() {
            Object.keys(this.formData).forEach(key => {
                const value = this.formData[key];

                if (Array.isArray(value)) {
                    // Handle array values (checkboxes)
                    value.forEach(val => {
                        const checkbox = document.querySelector(`input[name="${key}[]"][value="${val}"]`);
                        if (checkbox) {
                            checkbox.checked = true;
                            const toggle = checkbox.closest('.edsys-toggle');
                            if (toggle) toggle.classList.add('active');
                        }
                    });
                } else {
                    // Handle single values
                    const field = document.querySelector(`input[name="${key}"], select[name="${key}"], textarea[name="${key}"]`);
                    if (field) {
                        if (field.type === 'radio') {
                            const radio = document.querySelector(`input[name="${key}"][value="${value}"]`);
                            if (radio) {
                                radio.checked = true;
                                const card = radio.closest('.edsys-radio-card');
                                if (card) card.classList.add('active');
                            }
                        } else if (field.type === 'checkbox') {
                            field.checked = value;
                            const toggle = field.closest('.edsys-toggle');
                            if (toggle) toggle.classList.toggle('active', value);
                        } else if (field.type === 'range') {
                            field.value = value;
                        } else {
                            field.value = value;
                        }
                    }
                }
            });

            // Re-initialize range sliders with restored values
            this.initRangeSliders();
        }

        clearProgress() {
            try {
                localStorage.removeItem('edsWizardProgress');
            } catch (error) {
                console.warn('Could not clear progress from localStorage:', error);
            }
        }
    }

    // Consultation Form Handler
    class EDSConsultationForm {
        constructor() {
            this.form = document.querySelector('.edsys-consultation__form');
            this.init();
        }

        init() {
            if (this.form) {
                this.form.addEventListener('submit', (e) => this.handleSubmit(e));
                this.initPhoneFormatting();
            }
        }

        initPhoneFormatting() {
            const phoneInput = this.form.querySelector('input[type="tel"]');
            if (phoneInput) {
                phoneInput.addEventListener('input', (e) => {
                    let value = e.target.value.replace(/\D/g, '');

                    if (value.length > 0) {
                        if (value.startsWith('8')) {
                            value = '7' + value.slice(1);
                        }

                        if (value.startsWith('7')) {
                            value = value.slice(0, 11);
                            let formatted = '+7';
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
        }

        async handleSubmit(e) {
            e.preventDefault();

            const formData = new FormData(this.form);
            const data = Object.fromEntries(formData);

            const btn = this.form.querySelector('.edsys-consultation__btn');
            const originalText = btn.innerHTML;

            try {
                btn.disabled = true;
                btn.innerHTML = '<i class="ph ph-thin ph-spinner"></i> Отправляем...';

                const response = await fetch('/individualnye-resheniya/handler.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        action: 'consultation_request',
                        data: data
                    })
                });

                const result = await response.json();

                if (result.success) {
                    this.showNotification('Заявка отправлена! Мы свяжемся с вами в ближайшее время.', 'success');
                    this.form.reset();
                } else {
                    throw new Error(result.message || 'Ошибка при отправке заявки');
                }

            } catch (error) {
                console.error('Consultation form error:', error);
                this.showNotification(error.message || 'Ошибка при отправке заявки', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = originalText;
            }
        }

        showNotification(message, type) {
            if (window.edsWizard) {
                window.edsWizard.showNotification(message, type);
            } else {
                // Fallback notification
                alert(message);
            }
        }
    }

    // Performance Optimizer
    class EDSPerformanceOptimizer {
        constructor() {
            this.init();
        }

        init() {
            this.optimizeImages();
            this.initIntersectionObserver();
        }

        optimizeImages() {
            document.querySelectorAll('img:not([loading])').forEach(img => {
                if (!this.isInViewport(img)) {
                    img.loading = 'lazy';
                }
            });
        }

        initIntersectionObserver() {
            if (!('IntersectionObserver' in window)) {
                return; // Skip if not supported
            }

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target);
                    }
                });
            }, observerOptions);

            document.querySelectorAll('.edsys-advantage-card, .edsys-wizard__header').forEach(el => {
                observer.observe(el);
            });
        }

        isInViewport(element) {
            const rect = element.getBoundingClientRect();
            return (
                rect.top >= 0 &&
                rect.left >= 0 &&
                rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
                rect.right <= (window.innerWidth || document.documentElement.clientWidth)
            );
        }
    }

    // Global scroll functions
    window.scrollToWizard = function() {
        const wizard = document.querySelector('.edsys-wizard');
        if (wizard) {
            wizard.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };

    window.scrollToAdvantages = function() {
        const advantages = document.querySelector('.edsys-advantages');
        if (advantages) {
            advantages.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    };

    // Initialize everything when DOM is loaded
    function initializeEDS() {
        try {
            // Initialize main components
            window.edsWizard = new EDSTechnicalWizard();
            window.edsConsultation = new EDSConsultationForm();
            window.edsPerformanceOptimizer = new EDSPerformanceOptimizer();

            console.log('EDS Individual Solutions page initialized successfully');
        } catch (error) {
            console.error('Error initializing EDS page:', error);
        }
    }

    // Wait for DOM to be ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeEDS);
    } else {
        initializeEDS();
    }

})();