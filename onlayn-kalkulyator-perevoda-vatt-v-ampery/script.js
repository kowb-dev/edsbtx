/**
 * Watts to Amperes Calculator - Production Version
 * Калькулятор перевода Ватт в Амперы - Продакшн версия
 *
 * @author EDS Development Team
 * @version 2.0
 * @since 2024
 */

(function() {
    'use strict';

    // Конфигурация калькулятора
    const CONFIG = {
        debounceDelay: 300,
        minPower: 0.1,
        maxPower: 1000000,
        minVoltage: 1,
        maxVoltage: 1000,
        decimals: 2
    };

    /**
     * Класс калькулятора перевода Ватт в Амперы
     */
    class WattsToAmperesCalculator {
        constructor() {
            this.elements = {};
            this.debounceTimer = null;
            this.init();
        }

        /**
         * Инициализация калькулятора
         */
        init() {
            this.setupElements();
            this.setupEventListeners();
            this.resetCalculator();
        }

        /**
         * Настройка DOM элементов
         */
        setupElements() {
            this.elements = {
                powerInput: document.getElementById('power'),
                voltageInput: document.getElementById('voltage'),
                phasesSelect: document.getElementById('phases'),
                currentValue: document.getElementById('currentValue'),
                resetButton: document.getElementById('resetButton'),
                form: document.getElementById('calculatorForm')
            };

            // Проверка наличия обязательных элементов
            const required = ['powerInput', 'voltageInput', 'phasesSelect', 'currentValue', 'resetButton'];
            for (const key of required) {
                if (!this.elements[key]) {
                    throw new Error(`Элемент ${key} не найден`);
                }
            }
        }

        /**
         * Настройка обработчиков событий
         */
        setupEventListeners() {
            // Обработка ввода мощности
            this.elements.powerInput.addEventListener('input', (e) => {
                this.handleInputChange();
            });

            // Обработка ввода напряжения
            this.elements.voltageInput.addEventListener('input', (e) => {
                this.handleInputChange();
            });

            // Обработка выбора типа сети
            this.elements.phasesSelect.addEventListener('change', (e) => {
                this.updateCalculation();
            });

            // Кнопка сброса
            this.elements.resetButton.addEventListener('click', () => {
                this.resetCalculator();
            });

            // Предотвращение отправки формы
            if (this.elements.form) {
                this.elements.form.addEventListener('submit', (e) => {
                    e.preventDefault();
                });
            }
        }

        /**
         * Обработка изменений ввода с задержкой
         */
        handleInputChange() {
            // Очистка предыдущего таймера
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            // Валидация с визуальной обратной связью
            this.validateInputs();

            // Задержка перед расчетом
            this.debounceTimer = setTimeout(() => {
                this.updateCalculation();
            }, CONFIG.debounceDelay);
        }

        /**
         * Валидация входных данных
         */
        validateInputs() {
            const power = this.validateInput(this.elements.powerInput, 'power');
            const voltage = this.validateInput(this.elements.voltageInput, 'voltage');

            this.showValidationState(this.elements.powerInput, power.valid);
            this.showValidationState(this.elements.voltageInput, voltage.valid);

            return power.valid && voltage.valid;
        }

        /**
         * Валидация отдельного поля
         */
        validateInput(input, type) {
            const value = parseFloat(input.value);

            if (input.value === '') {
                return { valid: true, value: 0 };
            }

            if (isNaN(value) || !isFinite(value)) {
                return { valid: false, value: 0 };
            }

            if (type === 'power') {
                return {
                    valid: value >= CONFIG.minPower && value <= CONFIG.maxPower,
                    value: value
                };
            }

            if (type === 'voltage') {
                return {
                    valid: value >= CONFIG.minVoltage && value <= CONFIG.maxVoltage,
                    value: value
                };
            }

            return { valid: true, value: value };
        }

        /**
         * Отображение состояния валидации
         */
        showValidationState(input, isValid) {
            if (isValid) {
                input.classList.remove('edsys-calculator-form__input--error');
            } else {
                input.classList.add('edsys-calculator-form__input--error');
            }
        }

        /**
         * Обновление расчета
         */
        updateCalculation() {
            const powerValidation = this.validateInput(this.elements.powerInput, 'power');
            const voltageValidation = this.validateInput(this.elements.voltageInput, 'voltage');

            if (!powerValidation.valid || !voltageValidation.valid) {
                this.updateResult(0);
                return;
            }

            const power = powerValidation.value;
            const voltage = voltageValidation.value;
            const phases = parseInt(this.elements.phasesSelect.value);

            if (power > 0 && voltage > 0) {
                const current = this.calculateCurrent(power, voltage, phases);
                this.updateResult(current);
            } else {
                this.updateResult(0);
            }
        }

        /**
         * Расчет тока
         * @param {number} power - Мощность в ваттах
         * @param {number} voltage - Напряжение в вольтах
         * @param {number} phases - Количество фаз (1 или 3)
         * @returns {number} Ток в амперах
         */
        calculateCurrent(power, voltage, phases) {
            if (phases === 1) {
                // Однофазная сеть: I = P / U
                return power / voltage;
            } else {
                // Трехфазная сеть: I = P / (√3 × U)
                // Предполагаем cos φ = 1 для упрощения
                return power / (Math.sqrt(3) * voltage);
            }
        }

        /**
         * Обновление отображения результата
         */
        updateResult(current) {
            const formattedCurrent = this.formatNumber(current, CONFIG.decimals);
            this.elements.currentValue.textContent = formattedCurrent;

            // Добавление/удаление класса для стилизации
            const resultContainer = this.elements.currentValue.closest('.edsys-calculator-form__result');
            if (resultContainer) {
                if (current > 0) {
                    resultContainer.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    resultContainer.classList.remove('edsys-calculator-form__result--has-value');
                }
            }
        }

        /**
         * Форматирование числа для отображения
         */
        formatNumber(num, decimals = 2) {
            if (typeof num !== 'number' || isNaN(num)) {
                return '0';
            }
            return num.toFixed(decimals).replace(/\.?0+$/, '');
        }

        /**
         * Сброс калькулятора
         */
        resetCalculator() {
            // Очистка полей
            this.elements.powerInput.value = '';
            this.elements.voltageInput.value = '';
            this.elements.phasesSelect.value = '1';

            // Очистка классов валидации
            this.elements.powerInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.voltageInput.classList.remove('edsys-calculator-form__input--error');

            // Обновление результата
            this.updateResult(0);

            // Фокус на первом поле
            this.elements.powerInput.focus();
        }
    }

    // Инициализация калькулятора
    function initCalculator() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new WattsToAmperesCalculator();
            });
        } else {
            new WattsToAmperesCalculator();
        }
    }

    // Запуск инициализации
    initCalculator();

})();