/**
 * Калькулятор расчета тока в цепи
 * Версия: 1.3
 * Автор: EDS Development Team
 *
 * Исправления:
 * - Унификация стиля поля результата с калькулятором "Ватт в Амперы"
 * - Изменен ID элемента результата на currentValue
 * - Добавлена поддержка CSS класса для активного состояния
 * - Улучшено форматирование результата
 */

'use strict';

/**
 * Основная функция расчета тока
 * Формула: I = P / (U × K × cos φ)
 */
function calculateCurrent() {
    console.log('=== Начало расчета тока ===');

    try {
        // Получаем форму
        const form = document.forms['currentCalculatorForm'];
        if (!form) {
            console.error('Форма currentCalculatorForm не найдена');
            return;
        }

        // Получаем значения из формы
        const powerInput = form.num;
        const voltageInput = form.num1;
        const networkSelect = form.num3;
        const loadSelect = form.num4;

        // Проверяем существование элементов
        if (!powerInput || !voltageInput || !networkSelect || !loadSelect) {
            console.error('Не все поля формы найдены');
            return;
        }

        // Парсим значения
        const power = parseFloat(powerInput.value) || 0;
        const voltage = parseFloat(voltageInput.value) || 0;
        const networkCoeff = parseFloat(networkSelect.value) || 1;
        const loadCoeff = parseFloat(loadSelect.value) || 1;

        console.log('Входные данные:', {
            power: power + ' Вт',
            voltage: voltage + ' В',
            networkCoeff: networkCoeff + ' (коэффициент сети)',
            loadCoeff: loadCoeff + ' (коэффициент нагрузки)'
        });

        // Проверяем корректность данных
        if (power <= 0) {
            console.log('Мощность не введена или некорректна');
            updateResult(0);
            showValidationMessage('Введите корректное значение мощности');
            return;
        }

        if (voltage <= 0) {
            console.log('Напряжение не введено или некорректно');
            updateResult(0);
            showValidationMessage('Введите корректное значение напряжения');
            return;
        }

        // Выполняем расчет по формуле: I = P / (U × K × cos φ)
        const denominator = voltage * networkCoeff * loadCoeff;
        const current = power / denominator;

        console.log('Расчет:', {
            formula: 'I = P / (U × K × cos φ)',
            calculation: power + ' / (' + voltage + ' × ' + networkCoeff + ' × ' + loadCoeff + ')',
            denominator: denominator,
            result: current + ' А'
        });

        // Обновляем результат
        updateResult(current);
        hideValidationMessage();

    } catch (error) {
        console.error('Ошибка при расчете:', error);
        updateResult(0);
        showValidationMessage('Произошла ошибка при расчете');
    }
}

/**
 * Обновляет отображение результата в едином стиле с калькулятором "Ватт в Амперы"
 */
function updateResult(current) {
    // Используем currentValue для единообразия с калькулятором "Ватт в Амперы"
    const resultElement = document.getElementById('currentValue');
    const hiddenResult = document.getElementById('hiddenResult');

    if (resultElement) {
        const formattedResult = formatNumber(current, 2);
        resultElement.textContent = formattedResult;

        // Обновляем скрытое поле для обратной совместимости
        if (hiddenResult) {
            hiddenResult.value = formattedResult;
        }

        console.log('Результат обновлен:', formattedResult + ' А');

        // Добавляем/удаляем класс для стилизации (единообразие с калькулятором "Ватт в Амперы")
        const resultContainer = resultElement.closest('.edsys-calculator-form__result');
        if (resultContainer) {
            if (current > 0) {
                resultContainer.classList.add('edsys-calculator-form__result--has-value');
            } else {
                resultContainer.classList.remove('edsys-calculator-form__result--has-value');
            }
        }
    } else {
        console.error('Элемент результата currentValue не найден');
    }
}

/**
 * Форматирование числа для отображения (единообразие с калькулятором "Ватт в Амперы")
 */
function formatNumber(num, decimals = 2) {
    if (typeof num !== 'number' || isNaN(num)) {
        return '0';
    }
    return num.toFixed(decimals).replace(/\.?0+$/, '');
}

/**
 * Показывает сообщение валидации
 */
function showValidationMessage(message) {
    let messageElement = document.getElementById('validationMessage');

    if (!messageElement) {
        messageElement = document.createElement('div');
        messageElement.id = 'validationMessage';
        messageElement.className = 'edsys-calculator__validation-message';
        messageElement.style.cssText = `
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 8px;
            text-align: center;
            padding: 8px;
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
        `;

        const resultElement = document.querySelector('.edsys-calculator-form__result');
        if (resultElement) {
            resultElement.appendChild(messageElement);
        }
    }

    messageElement.textContent = message;
    messageElement.style.display = 'block';
}

/**
 * Скрывает сообщение валидации
 */
function hideValidationMessage() {
    const messageElement = document.getElementById('validationMessage');
    if (messageElement) {
        messageElement.style.display = 'none';
    }
}

/**
 * Сбрасывает калькулятор
 */
function resetCalculator() {
    console.log('=== Сброс калькулятора ===');

    const form = document.forms['currentCalculatorForm'];
    if (form) {
        form.reset();

        // Сбрасываем селекты на значения по умолчанию
        const networkSelect = form.num3;
        const loadSelect = form.num4;

        if (networkSelect) networkSelect.selectedIndex = 0;
        if (loadSelect) loadSelect.selectedIndex = 0;

        updateResult(0);
        hideValidationMessage();
        console.log('Калькулятор сброшен');
    }
}

/**
 * Старая функция для обратной совместимости (исправленная)
 */
function u(obj) {
    console.log('=== Вызов старой функции u() (обратная совместимость) ===');
    console.log('Объект формы:', obj);

    try {
        // Проверяем существование полей
        if (!obj.num || !obj.num1 || !obj.num3 || !obj.num4 || !obj.res) {
            console.error('Не все поля формы найдены в объекте');
            return;
        }

        const power = parseFloat(obj.num.value) || 0;
        const voltage = parseFloat(obj.num1.value) || 0;
        const networkCoeff = parseFloat(obj.num3.value) || 1;
        const loadCoeff = parseFloat(obj.num4.value) || 1;

        console.log('Старая функция - входные данные:', {
            power: power,
            voltage: voltage,
            networkCoeff: networkCoeff,
            loadCoeff: loadCoeff
        });

        if (power > 0 && voltage > 0) {
            const result = power / (voltage * networkCoeff * loadCoeff);
            obj.res.value = result.toFixed(2);

            console.log('Старая функция - результат:', result.toFixed(2));
        } else {
            obj.res.value = '0.00';
            console.log('Старая функция - некорректные данные, результат: 0.00');
        }
    } catch (error) {
        console.error('Ошибка в старой функции u():', error);
        if (obj.res) {
            obj.res.value = '0.00';
        }
    }
}

/**
 * Инициализация калькулятора
 */
function initCalculator() {
    console.log('=== Инициализация калькулятора тока ===');

    // Устанавливаем начальное значение
    updateResult(0);

    // Добавляем обработчики событий для автоматического расчета
    const inputs = document.querySelectorAll('.edsys-calculator__input');
    const selects = document.querySelectorAll('.edsys-calculator__select');

    [...inputs, ...selects].forEach(element => {
        element.addEventListener('input', calculateCurrent);
        element.addEventListener('change', calculateCurrent);
        console.log('Добавлен обработчик для элемента:', element.name || element.id);
    });

    // Дополнительные обработчики для обратной совместимости
    const form = document.forms['currentCalculatorForm'];
    if (form) {
        const formInputs = form.querySelectorAll('input, select');
        formInputs.forEach(element => {
            if (!element.classList.contains('edsys-calculator__input') && !element.classList.contains('edsys-calculator__select')) {
                element.addEventListener('input', calculateCurrent);
                element.addEventListener('change', calculateCurrent);
                console.log('Добавлен дополнительный обработчик для:', element.name || element.id);
            }
        });
    }

    // Добавляем обработчик для кнопки сброса
    const resetBtn = document.querySelector('#resetButton');
    if (resetBtn) {
        resetBtn.addEventListener('click', resetCalculator);
        console.log('Добавлен обработчик для кнопки сброса');
    }

    // Дополнительные обработчики для других селекторов
    const additionalResetBtns = document.querySelectorAll('.edsys-calculator-form__button--secondary');
    additionalResetBtns.forEach(btn => {
        if (btn !== resetBtn) {
            btn.addEventListener('click', resetCalculator);
            console.log('Добавлен дополнительный обработчик сброса');
        }
    });

    console.log('Калькулятор инициализирован успешно');
}

// Инициализация при загрузке страницы
document.addEventListener('DOMContentLoaded', initCalculator);

// Дополнительная инициализация для случаев динамической загрузки
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initCalculator);
} else {
    initCalculator();
}