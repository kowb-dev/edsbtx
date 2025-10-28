/**
 * Wire Section Calculator - Production Version
 * Калькулятор расчета сечения провода - Продакшн версия
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
        minDiameter: 0.1,
        maxDiameter: 100,
        minTurns: 1,
        maxTurns: 10000,
        minLength: 0.1,
        maxLength: 1000,
        decimals: 3
    };

    /**
     * Wire Section Calculator Class
     * Класс калькулятора сечения провода
     */
    class WireSectionCalculator {
        constructor() {
            this.elements = {};
            this.debounceTimers = {};
            this.init();
        }

        /**
         * Initialize calculator
         * Инициализация калькулятора
         */
        init() {
            this.setupElements();
            this.setupEventListeners();
            this.resetCalculators();
        }

        /**
         * Setup DOM elements
         * Настройка DOM элементов
         */
        setupElements() {
            this.elements = {
                // Diameter calculator
                diameterInput: document.getElementById('diameter'),
                diameterValue: document.getElementById('diameterValue'),
                diameterResult: document.getElementById('diameterResult'),
                resetDiameter: document.getElementById('resetDiameter'),

                // Turns calculator
                turnsInput: document.getElementById('turns'),
                lengthInput: document.getElementById('length'),
                turnsValue: document.getElementById('turnsValue'),
                turnsResult: document.getElementById('turnsResult'),
                resetTurns: document.getElementById('resetTurns')
            };

            // Check required elements
            const required = ['diameterInput', 'diameterValue', 'resetDiameter', 'turnsInput', 'lengthInput', 'turnsValue', 'resetTurns'];
            for (const key of required) {
                if (!this.elements[key]) {
                    throw new Error(`Element ${key} not found`);
                }
            }
        }

        /**
         * Setup event listeners
         * Настройка обработчиков событий
         */
        setupEventListeners() {
            // Diameter calculator
            this.elements.diameterInput.addEventListener('input', () => {
                this.handleDiameterInput();
            });

            // Turns calculator
            this.elements.turnsInput.addEventListener('input', () => {
                this.handleTurnsInput();
            });

            this.elements.lengthInput.addEventListener('input', () => {
                this.handleTurnsInput();
            });

            // Reset buttons
            this.elements.resetDiameter.addEventListener('click', () => {
                this.resetDiameterCalculator();
            });

            this.elements.resetTurns.addEventListener('click', () => {
                this.resetTurnsCalculator();
            });
        }

        /**
         * Handle diameter input with debounce
         * Обработка ввода диаметра с задержкой
         */
        handleDiameterInput() {
            if (this.debounceTimers.diameter) {
                clearTimeout(this.debounceTimers.diameter);
            }

            this.validateDiameterInput();

            this.debounceTimers.diameter = setTimeout(() => {
                this.calculateDiameterSection();
            }, CONFIG.debounceDelay);
        }

        /**
         * Handle turns input with debounce
         * Обработка ввода витков с задержкой
         */
        handleTurnsInput() {
            if (this.debounceTimers.turns) {
                clearTimeout(this.debounceTimers.turns);
            }

            this.validateTurnsInput();

            this.debounceTimers.turns = setTimeout(() => {
                this.calculateTurnsSection();
            }, CONFIG.debounceDelay);
        }

        /**
         * Validate diameter input
         * Валидация ввода диаметра
         */
        validateDiameterInput() {
            const validation = this.validateInput(this.elements.diameterInput, 'diameter');
            this.showValidationState(this.elements.diameterInput, validation.valid);
            return validation;
        }

        /**
         * Validate turns input
         * Валидация ввода витков
         */
        validateTurnsInput() {
            const turnsValidation = this.validateInput(this.elements.turnsInput, 'turns');
            const lengthValidation = this.validateInput(this.elements.lengthInput, 'length');

            this.showValidationState(this.elements.turnsInput, turnsValidation.valid);
            this.showValidationState(this.elements.lengthInput, lengthValidation.valid);

            return turnsValidation.valid && lengthValidation.valid;
        }

        /**
         * Validate individual input
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

            switch (type) {
                case 'diameter':
                    return {
                        valid: value >= CONFIG.minDiameter && value <= CONFIG.maxDiameter,
                        value: value
                    };
                case 'turns':
                    return {
                        valid: value >= CONFIG.minTurns && value <= CONFIG.maxTurns && Number.isInteger(value),
                        value: value
                    };
                case 'length':
                    return {
                        valid: value >= CONFIG.minLength && value <= CONFIG.maxLength,
                        value: value
                    };
                default:
                    return { valid: true, value: value };
            }
        }

        /**
         * Show validation state
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
         * Calculate section by diameter
         * Расчет сечения по диаметру
         */
        calculateDiameterSection() {
            const validation = this.validateDiameterInput();

            if (!validation.valid) {
                this.updateDiameterResult(0);
                return;
            }

            const diameter = validation.value;

            if (diameter > 0) {
                // Formula: S = π × (d/2)² = π × d² / 4
                const section = Math.PI * Math.pow(diameter, 2) / 4;
                this.updateDiameterResult(section);
            } else {
                this.updateDiameterResult(0);
            }
        }

        /**
         * Calculate section by turns
         * Расчет сечения по виткам
         */
        calculateTurnsSection() {
            const turnsValidation = this.validateInput(this.elements.turnsInput, 'turns');
            const lengthValidation = this.validateInput(this.elements.lengthInput, 'length');

            if (!turnsValidation.valid || !lengthValidation.valid) {
                this.updateTurnsResult(0);
                return;
            }

            const turns = turnsValidation.value;
            const length = lengthValidation.value;

            if (turns > 0 && length > 0) {
                // Formula: d = L / (n × π), then S = π × d² / 4
                const diameter = length / (turns * Math.PI);
                const section = Math.PI * Math.pow(diameter, 2) / 4;
                this.updateTurnsResult(section);
            } else {
                this.updateTurnsResult(0);
            }
        }

        /**
         * Update diameter result display
         * Обновление отображения результата диаметра
         */
        updateDiameterResult(section) {
            const formattedSection = this.formatNumber(section, CONFIG.decimals);
            this.elements.diameterValue.textContent = formattedSection;

            if (this.elements.diameterResult) {
                if (section > 0) {
                    this.elements.diameterResult.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    this.elements.diameterResult.classList.remove('edsys-calculator-form__result--has-value');
                }
            }
        }

        /**
         * Update turns result display
         * Обновление отображения результата витков
         */
        updateTurnsResult(section) {
            const formattedSection = this.formatNumber(section, CONFIG.decimals);
            this.elements.turnsValue.textContent = formattedSection;

            if (this.elements.turnsResult) {
                if (section > 0) {
                    this.elements.turnsResult.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    this.elements.turnsResult.classList.remove('edsys-calculator-form__result--has-value');
                }
            }
        }

        /**
         * Format number for display
         * Форматирование числа для отображения
         */
        formatNumber(num, decimals = 3) {
            if (typeof num !== 'number' || isNaN(num)) {
                return '0';
            }
            return num.toFixed(decimals).replace(/\.?0+$/, '');
        }

        /**
         * Reset diameter calculator
         * Сброс калькулятора диаметра
         */
        resetDiameterCalculator() {
            this.elements.diameterInput.value = '';
            this.elements.diameterInput.classList.remove('edsys-calculator-form__input--error');
            this.updateDiameterResult(0);
            this.elements.diameterInput.focus();
        }

        /**
         * Reset turns calculator
         * Сброс калькулятора витков
         */
        resetTurnsCalculator() {
            this.elements.turnsInput.value = '';
            this.elements.lengthInput.value = '';
            this.elements.turnsInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.lengthInput.classList.remove('edsys-calculator-form__input--error');
            this.updateTurnsResult(0);
            this.elements.turnsInput.focus();
        }

        /**
         * Reset all calculators
         * Сброс всех калькуляторов
         */
        resetCalculators() {
            this.resetDiameterCalculator();
            this.resetTurnsCalculator();
        }
    }

    // Initialize calculator
    function initCalculator() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new WireSectionCalculator();
            });
        } else {
            new WireSectionCalculator();
        }
    }

    // Start initialization
    initCalculator();

})();