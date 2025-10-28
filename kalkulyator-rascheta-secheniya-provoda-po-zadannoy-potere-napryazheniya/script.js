/**
 * Wire Section Calculator by Voltage Drop - Production Version
 * Калькулятор расчета сечения провода по заданной потере напряжения - Продакшн версия
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
        minPower: 0.1,
        maxPower: 1000000,
        minLength: 0.1,
        maxLength: 10000,
        decimals: 2,
        // Standard wire sections for recommendations
        standardSections: [0.5, 0.75, 1, 1.5, 2.5, 4, 6, 10, 16, 25, 35, 50, 70, 95, 120, 150, 185, 240, 300, 400, 500, 630, 800, 1000],
        // Voltage values for calculation
        voltages: {
            singlePhase: 220,
            threePhase: 380
        }
    };

    /**
     * Wire Section Calculator Class
     */
    class WireSectionCalculator {
        constructor() {
            this.elements = {};
            this.debounceTimer = null;
            this.init();
        }

        /**
         * Initialize calculator
         */
        init() {
            this.setupElements();
            this.setupEventListeners();
            this.resetCalculator();
        }

        /**
         * Setup DOM elements
         */
        setupElements() {
            this.elements = {
                form: document.forms['form1'],
                phaseInputs: document.querySelectorAll('input[name="num"]'),
                materialInputs: document.querySelectorAll('input[name="num1"]'),
                powerInput: document.getElementById('power'),
                lengthInput: document.getElementById('length'),
                voltageDropSelect: document.getElementById('voltageDrop'),
                sectionValue: document.getElementById('sectionValue'),
                hiddenResult: document.getElementById('hiddenResult'),
                resetButton: document.getElementById('resetButton'),
                calculatorForm: document.getElementById('calculatorForm')
            };

            // Check required elements
            const required = ['form', 'powerInput', 'lengthInput', 'voltageDropSelect', 'sectionValue', 'resetButton'];
            for (const key of required) {
                if (!this.elements[key]) {
                    throw new Error(`Element ${key} not found`);
                }
            }
        }

        /**
         * Setup event listeners
         */
        setupEventListeners() {
            // Power input
            this.elements.powerInput.addEventListener('input', () => {
                this.handleInputChange();
            });

            // Length input
            this.elements.lengthInput.addEventListener('input', () => {
                this.handleInputChange();
            });

            // Voltage drop select
            this.elements.voltageDropSelect.addEventListener('change', () => {
                this.updateCalculation();
            });

            // Phase type radio buttons
            this.elements.phaseInputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.updateCalculation();
                });
            });

            // Material radio buttons
            this.elements.materialInputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.updateCalculation();
                });
            });

            // Reset button
            this.elements.resetButton.addEventListener('click', () => {
                this.resetCalculator();
            });

            // Prevent form submission
            if (this.elements.calculatorForm) {
                this.elements.calculatorForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                });
            }
        }

        /**
         * Handle input changes with debounce
         */
        handleInputChange() {
            // Clear previous timer
            if (this.debounceTimer) {
                clearTimeout(this.debounceTimer);
            }

            // Validate inputs with visual feedback
            this.validateInputs();

            // Debounced calculation
            this.debounceTimer = setTimeout(() => {
                this.updateCalculation();
            }, CONFIG.debounceDelay);
        }

        /**
         * Validate inputs
         */
        validateInputs() {
            const power = this.validateInput(this.elements.powerInput, 'power');
            const length = this.validateInput(this.elements.lengthInput, 'length');

            this.showValidationState(this.elements.powerInput, power.valid);
            this.showValidationState(this.elements.lengthInput, length.valid);

            return power.valid && length.valid;
        }

        /**
         * Validate individual input
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

            if (type === 'length') {
                return {
                    valid: value >= CONFIG.minLength && value <= CONFIG.maxLength,
                    value: value
                };
            }

            return { valid: true, value: value };
        }

        /**
         * Show validation state
         */
        showValidationState(input, isValid) {
            if (isValid) {
                input.classList.remove('edsys-calculator-form__input--error');
            } else {
                input.classList.add('edsys-calculator-form__input--error');
            }
        }

        /**
         * Update calculation
         */
        updateCalculation() {
            const powerValidation = this.validateInput(this.elements.powerInput, 'power');
            const lengthValidation = this.validateInput(this.elements.lengthInput, 'length');

            if (!powerValidation.valid || !lengthValidation.valid) {
                this.updateResult(0);
                return;
            }

            const power = powerValidation.value;
            const length = lengthValidation.value;

            // Get selected values
            const phaseCoeff = this.getSelectedValue('num');
            const materialConductivity = this.getSelectedValue('num1');
            const voltageDropPercent = parseFloat(this.elements.voltageDropSelect.value);

            if (power > 0 && length > 0) {
                const section = this.calculateSection(power, length, phaseCoeff, materialConductivity, voltageDropPercent);
                this.updateResult(section);

                // Update hidden field for backward compatibility
                if (this.elements.hiddenResult) {
                    this.elements.hiddenResult.value = this.formatNumber(section, CONFIG.decimals);
                }
            } else {
                this.updateResult(0);
            }
        }

        /**
         * Get selected radio button value
         */
        getSelectedValue(name) {
            const selectedInput = document.querySelector(`input[name="${name}"]:checked`);
            return selectedInput ? parseFloat(selectedInput.value) : 0;
        }

        /**
         * Calculate wire section
         * Formula: S = (P × L × K) / (U² × σ × ΔU%)
         */
        calculateSection(power, length, phaseCoeff, materialConductivity, voltageDropPercent) {
            // Determine voltage based on phase type
            const voltage = phaseCoeff === 2 ? CONFIG.voltages.singlePhase : CONFIG.voltages.threePhase;
            
            // Calculate section using the formula
            const numerator = power * length * phaseCoeff;
            const denominator = (voltage * voltage) * materialConductivity * (voltageDropPercent / 100);
            
            const section = numerator / denominator;
            
            return Math.max(0, section);
        }

        /**
         * Update result display
         */
        updateResult(section) {
            const formattedSection = this.formatNumber(section, CONFIG.decimals);
            this.elements.sectionValue.textContent = formattedSection;

            // Add/remove class for styling
            const resultContainer = this.elements.sectionValue.closest('.edsys-calculator-form__result');
            if (resultContainer) {
                if (section > 0) {
                    resultContainer.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    resultContainer.classList.remove('edsys-calculator-form__result--has-value');
                }
            }

            // Show standard section recommendation
            if (section > 0) {
                const standardSection = this.getStandardSection(section);
                this.showStandardSectionRecommendation(section, standardSection);
            } else {
                this.hideStandardSectionRecommendation();
            }
        }

        /**
         * Get nearest standard section (higher)
         */
        getStandardSection(calculatedSection) {
            for (const standardSection of CONFIG.standardSections) {
                if (standardSection >= calculatedSection) {
                    return standardSection;
                }
            }
            return CONFIG.standardSections[CONFIG.standardSections.length - 1];
        }

        /**
         * Show standard section recommendation
         */
        showStandardSectionRecommendation(calculated, standard) {
            let recommendationElement = document.getElementById('standardSectionRecommendation');
            
            if (!recommendationElement) {
                recommendationElement = document.createElement('div');
                recommendationElement.id = 'standardSectionRecommendation';
                recommendationElement.className = 'edsys-calculator-form__recommendation';
                recommendationElement.style.cssText = `
                    margin-top: 12px;
                    padding: 12px;
                    background: #e8f5e8;
                    border: 1px solid #4caf50;
                    border-radius: 6px;
                    font-size: 0.9rem;
                    color: #2e7d32;
                    text-align: center;
                `;
                
                const resultContainer = this.elements.sectionValue.closest('.edsys-calculator-form__result');
                if (resultContainer) {
                    resultContainer.appendChild(recommendationElement);
                }
            }

            if (Math.abs(calculated - standard) < 0.01) {
                recommendationElement.innerHTML = `
                    <strong>✓ Расчетное сечение совпадает со стандартным</strong><br>
                    Рекомендуется: <strong>${standard} мм²</strong>
                `;
            } else {
                recommendationElement.innerHTML = `
                    <strong>Рекомендуется стандартное сечение: ${standard} мм²</strong><br>
                    <small>Ближайшее стандартное сечение в большую сторону</small>
                `;
            }

            recommendationElement.style.display = 'block';
        }

        /**
         * Hide standard section recommendation
         */
        hideStandardSectionRecommendation() {
            const recommendationElement = document.getElementById('standardSectionRecommendation');
            if (recommendationElement) {
                recommendationElement.style.display = 'none';
            }
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

        /**
         * Reset calculator
         */
        resetCalculator() {
            // Clear inputs
            this.elements.powerInput.value = '';
            this.elements.lengthInput.value = '';
            this.elements.voltageDropSelect.value = '1';

            // Reset radio buttons to default
            const singlePhaseRadio = document.getElementById('singlePhase');
            const copperRadio = document.getElementById('copper');
            
            if (singlePhaseRadio) singlePhaseRadio.checked = true;
            if (copperRadio) copperRadio.checked = true;

            // Clear validation classes
            this.elements.powerInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.lengthInput.classList.remove('edsys-calculator-form__input--error');

            // Update result
            this.updateResult(0);
            this.hideStandardSectionRecommendation();

            // Focus on first input
            this.elements.powerInput.focus();
        }
    }

    /**
     * Legacy function for backward compatibility
     */
    function u(obj) {
        console.log('Legacy function u() called for backward compatibility');
        
        try {
            if (!obj || !obj.num3 || !obj.num4 || !obj.num || !obj.num1 || !obj.num2 || !obj.res) {
                console.error('Required form elements not found');
                return;
            }

            const power = parseFloat(obj.num3.value) || 0;
            const length = parseFloat(obj.num4.value) || 0;
            const phaseCoeff = parseFloat(obj.num.value) || 2;
            const materialConductivity = parseFloat(obj.num1.value) || 53;
            const voltageDropPercent = parseFloat(obj.num2.value) || 1;

            if (power > 0 && length > 0) {
                const voltage = phaseCoeff === 2 ? 220 : 380;
                const numerator = power * length * phaseCoeff;
                const denominator = (voltage * voltage) * materialConductivity * (voltageDropPercent / 100);
                const result = numerator / denominator;
                
                obj.res.value = result.toFixed(2);
            } else {
                obj.res.value = '0.00';
            }
        } catch (error) {
            console.error('Error in legacy function u():', error);
            if (obj.res) {
                obj.res.value = '0.00';
            }
        }
    }

    // Make legacy function globally available
    window.u = u;

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
