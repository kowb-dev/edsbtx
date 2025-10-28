/**
 * Voltage Drop Calculator - Production Version
 * Калькулятор расчета падения напряжения в линии - Продакшн версия
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
        minPowerFactor: 0.1,
        maxPowerFactor: 1.0,
        minTemperature: -50,
        maxTemperature: 100,
        decimals: 2,
        // Voltage values for calculation
        voltages: {
            phaseNeutral: 220,
            phasePhase: 380
        },
        // Temperature coefficients
        temperatureCoefficients: {
            copper: 0.00393,    // 1/°C
            aluminum: 0.00403   // 1/°C
        }
    };

    /**
     * Voltage Drop Calculator Class
     */
    class VoltageDropCalculator {
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
                measurementInputs: document.querySelectorAll('input[name="num"]'),
                lineTypeInputs: document.querySelectorAll('input[name="num6"]'),
                materialInputs: document.querySelectorAll('input[name="num1"]'),
                wireSectionSelect: document.getElementById('wireSection'),
                powerInput: document.getElementById('power'),
                powerFactorInput: document.getElementById('powerFactor'),
                lengthInput: document.getElementById('length'),
                temperatureInput: document.getElementById('temperature'),
                voltageDropVolts: document.getElementById('voltageDropVolts'),
                voltageDropPercent: document.getElementById('voltageDropPercent'),
                hiddenResultVolts: document.getElementById('hiddenResultVolts'),
                hiddenResultPercent: document.getElementById('hiddenResultPercent'),
                resetButton: document.getElementById('resetButton'),
                calculatorForm: document.getElementById('calculatorForm')
            };

            // Check required elements
            const required = ['form', 'powerInput', 'lengthInput', 'voltageDropVolts', 'voltageDropPercent', 'resetButton'];
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

            // Power factor input
            this.elements.powerFactorInput.addEventListener('input', () => {
                this.handleInputChange();
            });

            // Length input
            this.elements.lengthInput.addEventListener('input', () => {
                this.handleInputChange();
            });

            // Temperature input
            this.elements.temperatureInput.addEventListener('input', () => {
                this.handleInputChange();
            });

            // Wire section select
            this.elements.wireSectionSelect.addEventListener('change', () => {
                this.updateCalculation();
            });

            // Measurement type radio buttons
            this.elements.measurementInputs.forEach(input => {
                input.addEventListener('change', () => {
                    this.updateCalculation();
                });
            });

            // Line type radio buttons
            this.elements.lineTypeInputs.forEach(input => {
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
            const powerFactor = this.validateInput(this.elements.powerFactorInput, 'powerFactor');
            const length = this.validateInput(this.elements.lengthInput, 'length');
            const temperature = this.validateInput(this.elements.temperatureInput, 'temperature');

            this.showValidationState(this.elements.powerInput, power.valid);
            this.showValidationState(this.elements.powerFactorInput, powerFactor.valid);
            this.showValidationState(this.elements.lengthInput, length.valid);
            this.showValidationState(this.elements.temperatureInput, temperature.valid);

            return power.valid && powerFactor.valid && length.valid && temperature.valid;
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

            switch (type) {
                case 'power':
                    return {
                        valid: value >= CONFIG.minPower && value <= CONFIG.maxPower,
                        value: value
                    };
                case 'powerFactor':
                    return {
                        valid: value >= CONFIG.minPowerFactor && value <= CONFIG.maxPowerFactor,
                        value: value
                    };
                case 'length':
                    return {
                        valid: value >= CONFIG.minLength && value <= CONFIG.maxLength,
                        value: value
                    };
                case 'temperature':
                    return {
                        valid: value >= CONFIG.minTemperature && value <= CONFIG.maxTemperature,
                        value: value
                    };
                default:
                    return { valid: true, value: value };
            }
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
            const powerFactorValidation = this.validateInput(this.elements.powerFactorInput, 'powerFactor');
            const lengthValidation = this.validateInput(this.elements.lengthInput, 'length');
            const temperatureValidation = this.validateInput(this.elements.temperatureInput, 'temperature');

            if (!powerValidation.valid || !powerFactorValidation.valid || 
                !lengthValidation.valid || !temperatureValidation.valid) {
                this.updateResults(0, 0);
                return;
            }

            const power = powerValidation.value;
            const powerFactor = powerFactorValidation.value;
            const length = lengthValidation.value;
            const temperature = temperatureValidation.value;

            // Get selected values
            const measurementCoeff = this.getSelectedValue('num');
            const lineReactance = this.getSelectedValue('num6');
            const materialResistivity = this.getSelectedValue('num1');
            const wireSection = parseFloat(this.elements.wireSectionSelect.value);

            if (power > 0 && length > 0 && powerFactor > 0 && wireSection > 0) {
                const results = this.calculateVoltageDrop(
                    power, powerFactor, length, temperature, measurementCoeff, 
                    lineReactance, materialResistivity, wireSection
                );
                this.updateResults(results.voltageDrop, results.voltageDropPercent);

                // Update hidden fields for backward compatibility
                if (this.elements.hiddenResultVolts) {
                    this.elements.hiddenResultVolts.value = this.formatNumber(results.voltageDrop, CONFIG.decimals);
                }
                if (this.elements.hiddenResultPercent) {
                    this.elements.hiddenResultPercent.value = this.formatNumber(results.voltageDropPercent, CONFIG.decimals);
                }
            } else {
                this.updateResults(0, 0);
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
         * Calculate voltage drop
         * Formula: ΔU = (P × L × K × (ρ + X × tan φ)) / (U × S)
         */
        calculateVoltageDrop(power, powerFactor, length, temperature, measurementCoeff, lineReactance, materialResistivity, wireSection) {
            // Determine voltage based on measurement type
            const voltage = measurementCoeff === 2 ? CONFIG.voltages.phaseNeutral : CONFIG.voltages.phasePhase;
            
            // Calculate temperature correction for resistivity
            const materialType = materialResistivity === 0.0175 ? 'copper' : 'aluminum';
            const tempCoeff = CONFIG.temperatureCoefficients[materialType];
            const resistivityAtTemp = materialResistivity * (1 + tempCoeff * (temperature - 20));
            
            // Calculate tan φ from cos φ
            const tanPhi = Math.sqrt(1 - powerFactor * powerFactor) / powerFactor;
            
            // Calculate voltage drop
            const numerator = power * length * measurementCoeff * (resistivityAtTemp + lineReactance * tanPhi);
            const denominator = voltage * wireSection;
            
            const voltageDrop = numerator / denominator;
            const voltageDropPercent = (voltageDrop / voltage) * 100;
            
            return {
                voltageDrop: Math.max(0, voltageDrop),
                voltageDropPercent: Math.max(0, voltageDropPercent)
            };
        }

        /**
         * Update results display
         */
        updateResults(voltageDrop, voltageDropPercent) {
            const formattedVoltageDrop = this.formatNumber(voltageDrop, CONFIG.decimals);
            const formattedVoltageDropPercent = this.formatNumber(voltageDropPercent, CONFIG.decimals);
            
            this.elements.voltageDropVolts.textContent = formattedVoltageDrop;
            this.elements.voltageDropPercent.textContent = formattedVoltageDropPercent;

            // Add/remove class for styling
            const resultContainerVolts = this.elements.voltageDropVolts.closest('.edsys-calculator-form__result');
            const resultContainerPercent = this.elements.voltageDropPercent.closest('.edsys-calculator-form__result');
            
            if (resultContainerVolts) {
                if (voltageDrop > 0) {
                    resultContainerVolts.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    resultContainerVolts.classList.remove('edsys-calculator-form__result--has-value');
                }
            }
            
            if (resultContainerPercent) {
                if (voltageDropPercent > 0) {
                    resultContainerPercent.classList.add('edsys-calculator-form__result--has-value');
                } else {
                    resultContainerPercent.classList.remove('edsys-calculator-form__result--has-value');
                }
            }

            // Show voltage drop assessment
            if (voltageDropPercent > 0) {
                this.showVoltageDropAssessment(voltageDropPercent);
            } else {
                this.hideVoltageDropAssessment();
            }
        }

        /**
         * Show voltage drop assessment
         */
        showVoltageDropAssessment(voltageDropPercent) {
            let assessmentElement = document.getElementById('voltageDropAssessment');
            
            if (!assessmentElement) {
                assessmentElement = document.createElement('div');
                assessmentElement.id = 'voltageDropAssessment';
                assessmentElement.className = 'edsys-calculator-form__assessment';
                assessmentElement.style.cssText = `
                    margin-top: 12px;
                    padding: 12px;
                    border-radius: 6px;
                    font-size: 0.9rem;
                    text-align: center;
                    font-weight: 600;
                `;
                
                const resultsGrid = document.querySelector('.edsys-calculator-form__results-grid');
                if (resultsGrid) {
                    resultsGrid.appendChild(assessmentElement);
                }
            }

            let bgColor, borderColor, textColor, message;
            
            if (voltageDropPercent <= 2.5) {
                bgColor = '#e8f5e8';
                borderColor = '#4caf50';
                textColor = '#2e7d32';
                message = '✓ Отличное качество электроснабжения';
            } else if (voltageDropPercent <= 5) {
                bgColor = '#fff3cd';
                borderColor = '#ffc107';
                textColor = '#856404';
                message = '⚠ Приемлемые потери напряжения';
            } else {
                bgColor = '#f8d7da';
                borderColor = '#dc3545';
                textColor = '#721c24';
                message = '⚠ Высокие потери напряжения - рекомендуется пересмотреть сечение';
            }

            assessmentElement.style.background = bgColor;
            assessmentElement.style.border = `1px solid ${borderColor}`;
            assessmentElement.style.color = textColor;
            assessmentElement.innerHTML = `<strong>${message}</strong>`;
            assessmentElement.style.display = 'block';
        }

        /**
         * Hide voltage drop assessment
         */
        hideVoltageDropAssessment() {
            const assessmentElement = document.getElementById('voltageDropAssessment');
            if (assessmentElement) {
                assessmentElement.style.display = 'none';
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
            this.elements.powerFactorInput.value = '0.95';
            this.elements.lengthInput.value = '';
            this.elements.temperatureInput.value = '20';
            this.elements.wireSectionSelect.value = '0.5';

            // Reset radio buttons to default
            const phaseNeutralRadio = document.getElementById('phaseNeutral');
            const cableLineRadio = document.getElementById('cableLine');
            const copperMaterialRadio = document.getElementById('copperMaterial');
            
            if (phaseNeutralRadio) phaseNeutralRadio.checked = true;
            if (cableLineRadio) cableLineRadio.checked = true;
            if (copperMaterialRadio) copperMaterialRadio.checked = true;

            // Clear validation classes
            this.elements.powerInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.powerFactorInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.lengthInput.classList.remove('edsys-calculator-form__input--error');
            this.elements.temperatureInput.classList.remove('edsys-calculator-form__input--error');

            // Update results
            this.updateResults(0, 0);
            this.hideVoltageDropAssessment();

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
            if (!obj || !obj.num3 || !obj.num4 || !obj.num5 || !obj.num7 || 
                !obj.num || !obj.num1 || !obj.num2 || !obj.num6 || !obj.res || !obj.resu) {
                console.error('Required form elements not found');
                return;
            }

            const power = parseFloat(obj.num3.value) || 0;
            const length = parseFloat(obj.num4.value) || 0;
            const powerFactor = parseFloat(obj.num5.value) || 0.95;
            const temperature = parseFloat(obj.num7.value) || 20;
            const measurementCoeff = parseFloat(obj.num.value) || 2;
            const materialResistivity = parseFloat(obj.num1.value) || 0.0175;
            const wireSection = parseFloat(obj.num2.value) || 0.5;
            const lineReactance = parseFloat(obj.num6.value) || 0.07;

            if (power > 0 && length > 0 && powerFactor > 0 && wireSection > 0) {
                const voltage = measurementCoeff === 2 ? 220 : 380;
                const materialType = materialResistivity === 0.0175 ? 'copper' : 'aluminum';
                const tempCoeff = materialType === 'copper' ? 0.00393 : 0.00403;
                const resistivityAtTemp = materialResistivity * (1 + tempCoeff * (temperature - 20));
                const tanPhi = Math.sqrt(1 - powerFactor * powerFactor) / powerFactor;
                
                const numerator = power * length * measurementCoeff * (resistivityAtTemp + lineReactance * tanPhi);
                const denominator = voltage * wireSection;
                
                const voltageDrop = numerator / denominator;
                const voltageDropPercent = (voltageDrop / voltage) * 100;
                
                obj.res.value = voltageDrop.toFixed(2);
                obj.resu.value = voltageDropPercent.toFixed(2);
            } else {
                obj.res.value = '0.00';
                obj.resu.value = '0.00';
            }
        } catch (error) {
            console.error('Error in legacy function u():', error);
            if (obj.res) obj.res.value = '0.00';
            if (obj.resu) obj.resu.value = '0.00';
        }
    }

    // Make legacy function globally available
    window.u = u;

    // Initialize calculator
    function initCalculator() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', () => {
                new VoltageDropCalculator();
            });
        } else {
            new VoltageDropCalculator();
        }
    }

    // Start initialization
    initCalculator();

})();
