/**
 * Stage Equipment Calculator - Production Version
 * Калькулятор электрооборудования сцены - Продакшн версия
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        debounceDelay: 300,
        voltage1Phase: 230,
        voltage3Phase: 400,
        searchMinLength: 2
    };

    // Equipment database
    const EQUIPMENT_DATABASE = [
        { name: 'CLAY PAKY MYTHOS', power: 700 },
        { name: 'CLAY PAKY MYTHOS 2', power: 700 },
        { name: 'CLAY PAKY SHARPY WASH 330 PC', power: 520 },
        { name: 'CLAY PAKY Sharpy wash 330', power: 520 },
        { name: 'CLAY PAKY SHARPY', power: 350 },
        { name: 'Прожектор PAR-64 CR', power: 1000 },
        { name: 'Блиндер (blinder) 4 лампы', power: 2600 },
        { name: 'Блиндер (blinder) 2 лампы', power: 1300 },
        { name: 'Блиндер (blinder) 2x100W LED', power: 200 },
        { name: 'Блиндер (blinder) 4x100W LED', power: 400 },
        { name: 'Стробоскоп Martin Atomic 3000 DMX', power: 1840 },
        { name: 'Генератор тумана TOUR HAZER II-S', power: 1600 },
        { name: 'Генератор тумана INVOLIGHT HZ610', power: 600 },
        { name: 'Генератор тумана INVOLIGHT HZ2500', power: 1500 },
        { name: 'Генератор тумана INVOLIGHT Stratus1500DMX', power: 1500 },
        { name: 'Генератор тумана INVOLIGHT HZ2500', power: 2500 },
        { name: 'LED PAR 64 RGBW 200W', power: 200 },
        { name: 'LED PAR 56 RGB 150W', power: 150 },
        { name: 'Moving Head Beam 230W', power: 280 },
        { name: 'Moving Head Wash 300W', power: 350 },
        { name: 'Лазерная установка 1W RGB', power: 25 },
        { name: 'Лазерная установка 3W RGB', power: 50 },
        { name: 'Дым-машина 1500W', power: 1500 },
        { name: 'Дым-машина 3000W', power: 3000 },
        { name: 'Ультрафиолетовая лампа 400W', power: 400 }
    ];

    /**
     * Stage Equipment Calculator Class
     */
    class StageEquipmentCalculator {
        constructor() {
            this.elements = {};
            this.equipmentData = [];
            this.currentRow = 1;
            this.debounceTimers = {};
            this.isAddingRow = false;
            this.init();
        }

        /**
         * Initialize calculator
         */
        init() {
            this.setupElements();
            this.setupEventListeners();
            this.populateEquipmentList(1);
            this.updateTotals();
        }

        /**
         * Setup DOM elements
         */
        setupElements() {
            this.elements = {
                container: document.getElementById('equipmentContainer'),
                addButton: document.getElementById('addCustomDevice'),
                clearButton: document.getElementById('clearAllDevices'),
                totalPower: document.getElementById('totalPower'),
                totalCurrent1: document.getElementById('totalCurrent1'),
                totalCurrent3: document.getElementById('totalCurrent3'),
                modal: document.getElementById('edsysDeviceRequestModal'),
                modalClose: document.querySelector('.edsys-stage-modal__close'),
                userName: document.getElementById('userName'),
                userEmail: document.getElementById('userEmail'),
                userComments: document.getElementById('userComments'),
                submitRequest: document.getElementById('submitDeviceRequest')
            };

            // Initialize equipment data array
            this.equipmentData[1] = { power: 0, current: 0, quantity: 1 };
        }

        /**
         * Setup event listeners
         */
        setupEventListeners() {
            // Убираем все существующие обработчики перед добавлением новых
            if (this.elements.addButton) {
                // Клонируем кнопку для удаления всех обработчиков
                const newAddButton = this.elements.addButton.cloneNode(true);
                this.elements.addButton.parentNode.replaceChild(newAddButton, this.elements.addButton);
                this.elements.addButton = newAddButton;

                // Добавляем один обработчик
                this.elements.addButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.addCustomDeviceRow();
                }, { once: false });
            }

            // Clear button
            if (this.elements.clearButton) {
                this.elements.clearButton.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    this.clearAllDevices();
                });
            }

            // Modal events
            if (this.elements.modalClose) {
                this.elements.modalClose.addEventListener('click', () => {
                    this.closeModal();
                });
            }

            if (this.elements.submitRequest) {
                this.elements.submitRequest.addEventListener('click', () => {
                    this.submitDeviceRequest();
                });
            }

            // Close modal on outside click
            window.addEventListener('click', (event) => {
                if (event.target === this.elements.modal) {
                    this.closeModal();
                }
            });

            // Email validation
            if (this.elements.userEmail) {
                this.elements.userEmail.addEventListener('input', () => {
                    this.validateEmail();
                });
            }

            // Initial row setup
            this.setupRowEvents(1);
        }

        /**
         * Setup events for a specific row
         */
        setupRowEvents(rowNumber) {
            const searchInput = document.getElementById(`deviceSearch${rowNumber}`);
            const quantityInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-quantity-input`);
            const dropdown = document.getElementById(`deviceDropdown${rowNumber}`);

            if (searchInput) {
                // Search input events
                searchInput.addEventListener('input', () => {
                    this.handleSearchInput(rowNumber);
                });

                searchInput.addEventListener('focus', () => {
                    this.showDropdown(rowNumber);
                });

                searchInput.addEventListener('blur', () => {
                    setTimeout(() => this.hideDropdown(rowNumber), 200);
                });
            }

            if (quantityInput) {
                // Quantity input events
                quantityInput.addEventListener('input', () => {
                    this.handleQuantityChange(rowNumber);
                });

                quantityInput.addEventListener('change', () => {
                    this.handleQuantityChange(rowNumber);
                });
            }

            // Delete button events
            const deleteButton = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-delete`);
            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    this.deleteRow(rowNumber);
                });
            }
        }

        /**
         * Populate equipment list for dropdown
         */
        populateEquipmentList(rowNumber) {
            const listElement = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-list`);
            if (!listElement) return;

            listElement.innerHTML = '';
            EQUIPMENT_DATABASE.forEach(device => {
                const item = document.createElement('div');
                item.className = 'edsys-stage-equipment-item';
                item.textContent = device.name;
                item.dataset.power = device.power;
                item.addEventListener('click', () => {
                    this.selectDevice(rowNumber, device);
                });
                listElement.appendChild(item);
            });
        }

        /**
         * Handle search input
         */
        handleSearchInput(rowNumber) {
            const searchInput = document.getElementById(`deviceSearch${rowNumber}`);
            const searchTerm = searchInput.value.toLowerCase();

            if (this.debounceTimers[rowNumber]) {
                clearTimeout(this.debounceTimers[rowNumber]);
            }

            // Clear current selection if input is changed
            if (this.equipmentData[rowNumber] && this.equipmentData[rowNumber].name) {
                this.clearRowData(rowNumber);
            }

            this.debounceTimers[rowNumber] = setTimeout(() => {
                this.filterEquipmentList(rowNumber, searchTerm);
            }, CONFIG.debounceDelay);
        }

        /**
         * Filter equipment list based on search term
         */
        filterEquipmentList(rowNumber, searchTerm) {
            const listElement = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-list`);
            if (!listElement) return;

            const items = listElement.querySelectorAll('.edsys-stage-equipment-item');
            items.forEach(item => {
                const deviceName = item.textContent.toLowerCase();
                if (deviceName.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }

        /**
         * Select device from dropdown
         */
        selectDevice(rowNumber, device) {
            const searchInput = document.getElementById(`deviceSearch${rowNumber}`);
            const powerInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-power-input`);
            const quantityInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-quantity-input`);

            searchInput.value = device.name;
            powerInput.value = device.power;

            // Update equipment data
            this.equipmentData[rowNumber] = {
                name: device.name,
                basePower: device.power,
                quantity: parseInt(quantityInput.value) || 1
            };

            this.hideDropdown(rowNumber);
            this.calculateRowPower(rowNumber);
            this.updateTotals();

            // Auto-add new row for next device selection
            this.addNewDeviceRow();
        }

        /**
         * Handle quantity change
         */
        handleQuantityChange(rowNumber) {
            const quantityInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-quantity-input`);
            const quantity = parseInt(quantityInput.value) || 1;

            if (quantity < 1) {
                quantityInput.value = 1;
                return;
            }

            if (this.equipmentData[rowNumber]) {
                this.equipmentData[rowNumber].quantity = quantity;

                // Recalculate power based on row type
                const row = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-row`);
                if (row && row.classList.contains('edsys-stage-equipment-row--custom')) {
                    this.handleCustomPowerInput(rowNumber);
                } else {
                    this.calculateRowPower(rowNumber);
                }
            }
        }

        /**
         * Calculate power for a specific row
         */
        calculateRowPower(rowNumber) {
            const data = this.equipmentData[rowNumber];
            if (!data || !data.basePower) return;

            const totalPower = data.basePower * data.quantity;
            const current = totalPower / CONFIG.voltage1Phase;

            const powerInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-power-input`);
            const currentInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-current-input`);

            if (powerInput) powerInput.value = Math.round(totalPower);
            if (currentInput) currentInput.value = this.formatNumber(current, 2);

            // Update stored data
            this.equipmentData[rowNumber].power = totalPower;
            this.equipmentData[rowNumber].current = current;
        }

        /**
         * Clear row data
         */
        clearRowData(rowNumber) {
            const powerInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-power-input`);
            const currentInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-current-input`);

            if (powerInput) powerInput.value = '0';
            if (currentInput) currentInput.value = '0';

            if (this.equipmentData[rowNumber]) {
                this.equipmentData[rowNumber] = { power: 0, current: 0, quantity: this.equipmentData[rowNumber].quantity || 1 };
            }

            this.updateTotals();
        }

        /**
         * Add new device selection row (from database)
         */
        addNewDeviceRow() {
            // Check if we already have an empty device row
            const emptyRow = this.findEmptyDeviceRow();
            if (emptyRow) {
                return; // Don't add if empty row exists
            }

            this.currentRow++;
            const rowNumber = this.currentRow;

            const rowHtml = `
                <div class="edsys-stage-equipment-row" data-row="${rowNumber}">
                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--device">
                        <label class="edsys-stage-equipment-label">Выберите прибор</label>
                        <div class="edsys-stage-equipment-search">
                            <input type="text" 
                                   class="edsys-stage-equipment-input edsys-stage-search-input" 
                                   id="deviceSearch${rowNumber}"
                                   placeholder="Введите название устройства"
                                   autocomplete="off">
                            <div class="edsys-stage-equipment-dropdown" id="deviceDropdown${rowNumber}">
                                <div class="edsys-stage-equipment-list" data-row="${rowNumber}"></div>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--quantity">
                        <label class="edsys-stage-equipment-label">Количество</label>
                        <input type="number" 
                               class="edsys-stage-equipment-input edsys-stage-quantity-input" 
                               min="1" 
                               value="1"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--power">
                        <label class="edsys-stage-equipment-label">Мощность (Вт)</label>
                        <input type="text" 
                               class="edsys-stage-equipment-input edsys-stage-power-input" 
                               readonly 
                               value="0"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--current">
                        <label class="edsys-stage-equipment-label">Ток (А)</label>
                        <input type="text" 
                               class="edsys-stage-equipment-input edsys-stage-current-input" 
                               readonly 
                               value="0"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-actions">
                        <button type="button" class="edsys-stage-equipment-delete" data-row="${rowNumber}" style="display: none;">
                            <i class="ph ph-thin ph-x"></i>
                        </button>
                    </div>
                </div>
            `;

            this.elements.container.insertAdjacentHTML('beforeend', rowHtml);

            // Initialize equipment data for new row
            this.equipmentData[rowNumber] = { power: 0, current: 0, quantity: 1 };

            // Setup events and populate dropdown
            this.setupRowEvents(rowNumber);
            this.populateEquipmentList(rowNumber);
            this.updateDeleteButtons();
        }

        /**
         * Find empty device row (row without selected device)
         */
        findEmptyDeviceRow() {
            const rows = document.querySelectorAll('.edsys-stage-equipment-row:not(.edsys-stage-equipment-row--custom)');
            for (let row of rows) {
                const searchInput = row.querySelector('.edsys-stage-search-input');
                const powerInput = row.querySelector('.edsys-stage-power-input');

                if (searchInput && powerInput &&
                    searchInput.value.trim() === '' &&
                    powerInput.value === '0') {
                    return row;
                }
            }
            return null;
        }
        /**
         * Add custom device row
         */
        addCustomDeviceRow() {
            // Проверяем, что не добавляем дублирующий ряд
            if (this.isAddingRow) {
                return;
            }

            this.isAddingRow = true;

            this.currentRow++;
            const rowNumber = this.currentRow;

            const rowHtml = `
                <div class="edsys-stage-equipment-row edsys-stage-equipment-row--custom" data-row="${rowNumber}">
                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--device">
                        <label class="edsys-stage-equipment-label">Название устройства</label>
                        <input type="text" 
                               class="edsys-stage-equipment-input edsys-stage-custom-name" 
                               placeholder="Введите название устройства"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--quantity">
                        <label class="edsys-stage-equipment-label">Количество</label>
                        <input type="number" 
                               class="edsys-stage-equipment-input edsys-stage-quantity-input" 
                               min="1" 
                               value="1"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--power">
                        <label class="edsys-stage-equipment-label">Мощность (Вт)</label>
                        <input type="number" 
                               class="edsys-stage-equipment-input edsys-stage-custom-power" 
                               min="0" 
                               value="0"
                               placeholder="Введите мощность"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-field edsys-stage-equipment-field--current">
                        <label class="edsys-stage-equipment-label">Ток (А)</label>
                        <input type="text" 
                               class="edsys-stage-equipment-input edsys-stage-current-input" 
                               readonly 
                               value="0"
                               data-row="${rowNumber}">
                    </div>

                    <div class="edsys-stage-equipment-actions">
                        <button type="button" class="edsys-stage-equipment-request" data-row="${rowNumber}">
                            <i class="ph ph-thin ph-plus"></i>
                            Запросить добавление
                        </button>
                        <button type="button" class="edsys-stage-equipment-delete" data-row="${rowNumber}">
                            <i class="ph ph-thin ph-x"></i>
                        </button>
                    </div>
                </div>
            `;

            this.elements.container.insertAdjacentHTML('beforeend', rowHtml);

            // Initialize equipment data for new row
            this.equipmentData[rowNumber] = { power: 0, current: 0, quantity: 1 };

            // Setup events for new row
            this.setupCustomRowEvents(rowNumber);
            this.updateDeleteButtons();

            // Сбрасываем флаг после завершения добавления
            this.isAddingRow = false;
        }

        /**
         * Clear all devices
         */
        clearAllDevices() {
            // Удаляем все ряды кроме первого
            const rows = this.elements.container.querySelectorAll('.edsys-stage-equipment-row');
            rows.forEach((row, index) => {
                if (index > 0) { // Оставляем первый ряд
                    row.remove();
                }
            });

            // Сбрасываем первый ряд
            const firstRow = this.elements.container.querySelector('.edsys-stage-equipment-row[data-row="1"]');
            if (firstRow) {
                const searchInput = firstRow.querySelector('.edsys-stage-search-input');
                const powerInput = firstRow.querySelector('.edsys-stage-power-input');
                const currentInput = firstRow.querySelector('.edsys-stage-current-input');
                const quantityInput = firstRow.querySelector('.edsys-stage-quantity-input');

                if (searchInput) searchInput.value = '';
                if (powerInput) powerInput.value = '0';
                if (currentInput) currentInput.value = '0';
                if (quantityInput) quantityInput.value = '1';
            }

            // Сбрасываем данные
            this.equipmentData = [];
            this.equipmentData[1] = { power: 0, current: 0, quantity: 1 };
            this.currentRow = 1;

            // Обновляем итоги
            this.updateTotals();
            this.updateDeleteButtons();

            // Центрируем калькулятор вертикально
            this.centerCalculator();
        }

        /**
         * Center calculator vertically on screen
         */
        centerCalculator() {
            const calculator = document.getElementById('stageEquipmentCalculator');
            if (calculator) {
                calculator.scrollIntoView({
                    behavior: 'smooth',
                    block: 'center'
                });
            }
        }

        /**
         * Setup events for custom device row
         */
        setupCustomRowEvents(rowNumber) {
            const nameInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-custom-name`);
            const powerInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-custom-power`);
            const quantityInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-quantity-input`);
            const requestButton = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-request`);
            const deleteButton = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-delete`);

            if (powerInput) {
                powerInput.addEventListener('input', () => {
                    this.handleCustomPowerInput(rowNumber);
                });
            }

            if (quantityInput) {
                quantityInput.addEventListener('input', () => {
                    this.handleQuantityChange(rowNumber);
                });
            }

            if (requestButton) {
                requestButton.addEventListener('click', () => {
                    this.openDeviceRequestModal(rowNumber);
                });
            }

            if (deleteButton) {
                deleteButton.addEventListener('click', () => {
                    this.deleteRow(rowNumber);
                });
            }
        }

        /**
         * Handle custom power input
         */
        handleCustomPowerInput(rowNumber) {
            const powerInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-custom-power`);
            const currentInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-current-input`);
            const quantityInput = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-quantity-input`);

            const power = parseFloat(powerInput.value) || 0;
            const quantity = parseInt(quantityInput.value) || 1;
            const totalPower = power * quantity;
            const current = totalPower / CONFIG.voltage1Phase;

            if (currentInput) {
                currentInput.value = this.formatNumber(current, 2);
            }

            // Update stored data
            this.equipmentData[rowNumber] = {
                basePower: power,
                quantity: quantity,
                power: totalPower,
                current: current
            };

            this.updateTotals();
        }

        /**
         * Delete equipment row
         */
        deleteRow(rowNumber) {
            const row = document.querySelector(`[data-row="${rowNumber}"].edsys-stage-equipment-row`);
            if (row) {
                row.remove();
                delete this.equipmentData[rowNumber];
                this.updateTotals();
                this.updateDeleteButtons();
            }
        }

        /**
         * Update delete buttons visibility
         */
        updateDeleteButtons() {
            const deleteButtons = document.querySelectorAll('.edsys-stage-equipment-delete');
            if (deleteButtons.length > 1) {
                deleteButtons.forEach(button => {
                    button.style.display = 'flex';
                });
            } else {
                deleteButtons.forEach(button => {
                    button.style.display = 'none';
                });
            }
        }

        /**
         * Update total calculations
         */
        updateTotals() {
            let totalPower = 0;
            let totalCurrent = 0;

            Object.values(this.equipmentData).forEach(data => {
                if (data.power) {
                    totalPower += data.power;
                    totalCurrent += data.current;
                }
            });

            const current3Phase = totalCurrent / 3;

            this.elements.totalPower.textContent = `${Math.round(totalPower)} Вт`;
            this.elements.totalCurrent1.textContent = `${this.formatNumber(totalCurrent, 2)} А`;
            this.elements.totalCurrent3.textContent = `${this.formatNumber(current3Phase, 2)} А`;
        }

        /**
         * Show dropdown
         */
        showDropdown(rowNumber) {
            const dropdown = document.getElementById(`deviceDropdown${rowNumber}`);
            if (dropdown) {
                dropdown.style.display = 'block';
            }
        }

        /**
         * Hide dropdown
         */
        hideDropdown(rowNumber) {
            const dropdown = document.getElementById(`deviceDropdown${rowNumber}`);
            if (dropdown) {
                dropdown.style.display = 'none';
            }
        }

        /**
         * Open device request modal
         */
        openDeviceRequestModal(rowNumber) {
            this.currentRequestRow = rowNumber;
            this.elements.modal.style.display = 'block';
        }

        /**
         * Close modal
         */
        closeModal() {
            this.elements.modal.style.display = 'none';
            this.resetModalForm();
            this.currentRequestRow = null;
        }

        /**
         * Validate email
         */
        validateEmail() {
            const email = this.elements.userEmail.value;
            const emailRegex = /^[a-z0-9_-]+@[a-z0-9-]+\.([a-z]{1,6}\.)?[a-z]{2,6}$/i;

            if (email && emailRegex.test(email)) {
                this.elements.userEmail.classList.remove('error');
                this.elements.userEmail.classList.add('valid');
                return true;
            } else {
                this.elements.userEmail.classList.remove('valid');
                this.elements.userEmail.classList.add('error');
                return false;
            }
        }

        /**
         * Submit device request
         */
        async submitDeviceRequest() {
            const name = this.elements.userName.value.trim();
            const email = this.elements.userEmail.value.trim();
            const comments = this.elements.userComments.value.trim();

            // Validation
            let isValid = true;

            if (!name) {
                this.elements.userName.classList.add('error');
                isValid = false;
            } else {
                this.elements.userName.classList.remove('error');
            }

            if (!this.validateEmail()) {
                isValid = false;
            }

            if (!isValid) {
                return;
            }

            // Get device data
            const deviceName = document.querySelector(`[data-row="${this.currentRequestRow}"].edsys-stage-custom-name`)?.value;
            const devicePower = document.querySelector(`[data-row="${this.currentRequestRow}"].edsys-stage-custom-power`)?.value;

            if (!deviceName || !devicePower) {
                return;
            }

            const requestData = {
                deviceName: deviceName,
                devicePower: devicePower,
                userName: name,
                userEmail: email,
                comments: comments
            };

            // Show loading state
            this.elements.submitRequest.disabled = true;
            this.elements.submitRequest.innerHTML = '<i class="ph ph-thin ph-spinner"></i> Отправка...';

            try {
                // Send request to server
                const response = await fetch('/kalkulator-elektrooborudovaniya-sceny/telegram.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(requestData)
                });

                const result = await response.json();

                if (result.success) {
                    // Success - show success message and close modal
                    this.showSuccessMessage();
                    this.closeModal();
                    this.resetModalForm();
                } else {
                    // Show error (you can implement error display if needed)
                    throw new Error(result.message || 'Ошибка отправки запроса');
                }

            } catch (error) {
                // Handle error silently or show user-friendly message
                // For production, you might want to show a subtle error indication
            } finally {
                // Reset button state
                this.elements.submitRequest.disabled = false;
                this.elements.submitRequest.innerHTML = 'Отправить запрос';
            }
        }

        /**
         * Show success message
         */
        showSuccessMessage() {
            // Create success message overlay
            const successOverlay = document.createElement('div');
            successOverlay.className = 'edsys-stage-success-overlay';
            successOverlay.innerHTML = `
                <div class="edsys-stage-success-message">
                    <div class="edsys-stage-success-icon">
                        <i class="ph ph-thin ph-check-circle"></i>
                    </div>
                    <h3 class="edsys-stage-success-title">Спасибо!</h3>
                    <p class="edsys-stage-success-text">Ваше сообщение отправлено! В ближайшее время мы добавим ваше устройство в этот калькулятор.</p>
                </div>
            `;

            // Add styles
            successOverlay.style.cssText = `
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.8);
                backdrop-filter: blur(4px);
                display: flex;
                align-items: center;
                justify-content: center;
                z-index: 10000;
                opacity: 0;
                transition: opacity 0.3s ease;
            `;

            const successMessage = successOverlay.querySelector('.edsys-stage-success-message');
            successMessage.style.cssText = `
                background: white;
                padding: 40px;
                border-radius: 12px;
                text-align: center;
                max-width: 400px;
                margin: 20px;
                transform: scale(0.8);
                transition: transform 0.3s ease;
            `;

            const successIcon = successOverlay.querySelector('.edsys-stage-success-icon');
            successIcon.style.cssText = `
                width: 80px;
                height: 80px;
                margin: 0 auto 20px;
                background: var(--edsys-circuit);
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                color: white;
                font-size: 40px;
            `;

            const successTitle = successOverlay.querySelector('.edsys-stage-success-title');
            successTitle.style.cssText = `
                font-size: 24px;
                font-weight: bold;
                color: var(--edsys-text);
                margin-bottom: 16px;
            `;

            const successText = successOverlay.querySelector('.edsys-stage-success-text');
            successText.style.cssText = `
                font-size: 16px;
                color: var(--edsys-text-muted);
                line-height: 1.5;
                margin: 0;
            `;

            // Add to page
            document.body.appendChild(successOverlay);

            // Show with animation
            setTimeout(() => {
                successOverlay.style.opacity = '1';
                successMessage.style.transform = 'scale(1)';
            }, 100);

            // Auto-hide after 4 seconds
            setTimeout(() => {
                successOverlay.style.opacity = '0';
                successMessage.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    if (successOverlay.parentNode) {
                        successOverlay.parentNode.removeChild(successOverlay);
                    }
                }, 300);
            }, 4000);

            // Close on click
            successOverlay.addEventListener('click', () => {
                successOverlay.style.opacity = '0';
                successMessage.style.transform = 'scale(0.8)';
                setTimeout(() => {
                    if (successOverlay.parentNode) {
                        successOverlay.parentNode.removeChild(successOverlay);
                    }
                }, 300);
            });
        }

        /**
         * Reset modal form
         */
        resetModalForm() {
            this.elements.userName.value = '';
            this.elements.userEmail.value = '';
            this.elements.userComments.value = '';

            // Remove validation classes
            this.elements.userName.classList.remove('error', 'valid');
            this.elements.userEmail.classList.remove('error', 'valid');
        }

        /**
         * Format number for display
         */
        formatNumber(num, decimals = 2) {
            if (typeof num !== 'number' || isNaN(num)) {
                return '0';
            }
            return num.toFixed(decimals).replace(/\.?0+$/, '');
        }
    }

    // Initialize calculator
    function initCalculator() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new StageEquipmentCalculator();
            });
        } else {
            new StageEquipmentCalculator();
        }
    }

    // Start initialization
    initCalculator();

})();