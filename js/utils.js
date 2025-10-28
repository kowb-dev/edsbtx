/**
 * Utility Functions
 * Вспомогательные функции для всего приложения
 */

/**
 * Debounce функция для оптимизации частых вызовов
 * @param {Function} func - Функция для вызова
 * @param {number} wait - Задержка в миллисекундах
 * @param {boolean} immediate - Вызвать немедленно
 * @returns {Function}
 */
export function debounce(func, wait, immediate) {
    let timeout;

    return function executedFunction() {
        const context = this;
        const args = arguments;

        const later = function() {
            timeout = null;
            if (!immediate) func.apply(context, args);
        };

        const callNow = immediate && !timeout;

        clearTimeout(timeout);
        timeout = setTimeout(later, wait);

        if (callNow) func.apply(context, args);
    };
}

/**
 * Throttle функция для ограничения частоты вызовов
 * @param {Function} func - Функция для вызова
 * @param {number} limit - Минимальный интервал между вызовами
 * @returns {Function}
 */
export function throttle(func, limit) {
    let inThrottle;

    return function() {
        const args = arguments;
        const context = this;

        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;

            setTimeout(() => inThrottle = false, limit);
        }
    };
}

/**
 * Проверка видимости элемента во viewport
 * @param {HTMLElement} element - DOM элемент
 * @param {number} threshold - Порог видимости (0-1)
 * @returns {boolean}
 */
export function isElementInViewport(element, threshold = 0) {
    const rect = element.getBoundingClientRect();
    const windowHeight = window.innerHeight || document.documentElement.clientHeight;
    const windowWidth = window.innerWidth || document.documentElement.clientWidth;

    const vertInView = (rect.top <= windowHeight) && ((rect.top + rect.height) >= 0);
    const horInView = (rect.left <= windowWidth) && ((rect.left + rect.width) >= 0);

    return vertInView && horInView;
}

/**
 * Плавная прокрутка к элементу
 * @param {HTMLElement|string} target - Элемент или селектор
 * @param {number} offset - Отступ сверху
 * @param {number} duration - Длительность анимации
 */
export function smoothScrollTo(target, offset = 0, duration = 600) {
    const element = typeof target === 'string' ? document.querySelector(target) : target;

    if (!element) return;

    const targetPosition = element.getBoundingClientRect().top + window.pageYOffset - offset;
    const startPosition = window.pageYOffset;
    const distance = targetPosition - startPosition;
    let startTime = null;

    function animation(currentTime) {
        if (startTime === null) startTime = currentTime;

        const timeElapsed = currentTime - startTime;
        const run = ease(timeElapsed, startPosition, distance, duration);

        window.scrollTo(0, run);

        if (timeElapsed < duration) requestAnimationFrame(animation);
    }

    function ease(t, b, c, d) {
        t /= d / 2;
        if (t < 1) return c / 2 * t * t + b;
        t--;
        return -c / 2 * (t * (t - 2) - 1) + b;
    }

    requestAnimationFrame(animation);
}

/**
 * Получение параметров из URL
 * @param {string} name - Имя параметра
 * @returns {string|null}
 */
export function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    const regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    const results = regex.exec(location.search);
    return results === null ? null : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

/**
 * Форматирование числа с разделителями
 * @param {number} num - Число
 * @param {string} separator - Разделитель
 * @returns {string}
 */
export function formatNumber(num, separator = ' ') {
    return num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, separator);
}

/**
 * Определение типа устройства
 * @returns {Object}
 */
export function getDeviceInfo() {
    const userAgent = navigator.userAgent.toLowerCase();
    const isMobile = /mobile|android|iphone|ipad|phone/i.test(userAgent);
    const isTablet = /tablet|ipad/i.test(userAgent);
    const isDesktop = !isMobile && !isTablet;
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints > 0;

    return {
        isMobile,
        isTablet,
        isDesktop,
        isTouchDevice,
        isIOS: /iphone|ipad|ipod/.test(userAgent),
        isAndroid: /android/.test(userAgent),
        screenWidth: window.innerWidth,
        screenHeight: window.innerHeight
    };
}

/**
 * Копирование текста в буфер обмена
 * @param {string} text - Текст для копирования
 * @returns {Promise<boolean>}
 */
export async function copyToClipboard(text) {
    try {
        if (navigator.clipboard && window.isSecureContext) {
            await navigator.clipboard.writeText(text);
            return true;
        } else {
            // Fallback для старых браузеров
            const textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            textArea.style.top = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();

            const successful = document.execCommand('copy');
            textArea.remove();
            return successful;
        }
    } catch (error) {
        console.error('Failed to copy text: ', error);
        return false;
    }
}

/**
 * Загрузка скрипта динамически
 * @param {string} src - URL скрипта
 * @param {Object} attributes - Дополнительные атрибуты
 * @returns {Promise}
 */
export function loadScript(src, attributes = {}) {
    return new Promise((resolve, reject) => {
        const script = document.createElement('script');
        script.src = src;

        Object.keys(attributes).forEach(key => {
            script.setAttribute(key, attributes[key]);
        });

        script.onload = resolve;
        script.onerror = reject;

        document.head.appendChild(script);
    });
}

/**
 * Проверка поддержки WebP
 * @returns {Promise<boolean>}
 */
export function supportsWebP() {
    return new Promise((resolve) => {
        const webP = new Image();
        webP.onload = webP.onerror = function() {
            resolve(webP.height === 2);
        };
        webP.src = 'data:image/webp;base64,UklGRjoAAABXRUJQVlA4IC4AAACyAgCdASoCAAIALmk0mk0iIiIiIgBoSygABc6WWgAA/veff/0PP8bA//LwYAAA';
    });
}

/**
 * Задержка выполнения
 * @param {number} ms - Миллисекунды
 * @returns {Promise}
 */
export function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Генерация уникального ID
 * @param {string} prefix - Префикс
 * @returns {string}
 */
export function generateId(prefix = 'id') {
    return `${prefix}-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
}

/**
 * Проверка валидности email
 * @param {string} email - Email адрес
 * @returns {boolean}
 */
export function isValidEmail(email) {
    const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
    return re.test(String(email).toLowerCase());
}

/**
 * Проверка валидности телефона (российский формат)
 * @param {string} phone - Номер телефона
 * @returns {boolean}
 */
export function isValidPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    const re = /^[78]?[0-9]{10}$/;
    return re.test(cleaned);
}

/**
 * Форматирование телефона
 * @param {string} phone - Номер телефона
 * @returns {string}
 */
export function formatPhone(phone) {
    const cleaned = phone.replace(/\D/g, '');
    const match = cleaned.match(/^([78])?(\d{3})(\d{3})(\d{2})(\d{2})$/);

    if (match) {
        const intlCode = match[1] ? '+7' : '';
        return `${intlCode} (${match[2]}) ${match[3]}-${match[4]}-${match[5]}`;
    }

    return phone;
}

/**
 * Определение направления скролла
 * @param {Function} callback - Функция обратного вызова
 * @returns {Function} - Функция для удаления слушателя
 */
export function detectScrollDirection(callback) {
    let lastScrollTop = 0;

    const handleScroll = throttle(() => {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const direction = scrollTop > lastScrollTop ? 'down' : 'up';

        callback(direction, scrollTop);
        lastScrollTop = scrollTop <= 0 ? 0 : scrollTop;
    }, 100);

    window.addEventListener('scroll', handleScroll, { passive: true });

    // Возвращаем функцию для удаления слушателя
    return () => window.removeEventListener('scroll', handleScroll);
}

/**
 * Получение размеров элемента с учетом трансформаций
 * @param {HTMLElement} element - DOM элемент
 * @returns {Object}
 */
export function getTransformedDimensions(element) {
    const rect = element.getBoundingClientRect();
    const style = window.getComputedStyle(element);
    const matrix = new DOMMatrix(style.transform);

    return {
        width: rect.width,
        height: rect.height,
        scaleX: matrix.a,
        scaleY: matrix.d,
        actualWidth: rect.width / matrix.a,
        actualHeight: rect.height / matrix.d
    };
}

/**
 * Предзагрузка изображений
 * @param {Array<string>} urls - Массив URL изображений
 * @returns {Promise}
 */
export function preloadImages(urls) {
    const promises = urls.map(url => {
        return new Promise((resolve, reject) => {
            const img = new Image();
            img.onload = resolve;
            img.onerror = reject;
            img.src = url;
        });
    });

    return Promise.all(promises);
}

/**
 * Безопасное выполнение функции с логированием ошибок
 * @param {Function} fn - Функция для выполнения
 * @param {*} fallback - Значение по умолчанию при ошибке
 * @returns {*}
 */
export function safeExecute(fn, fallback = null) {
    try {
        return fn();
    } catch (error) {
        console.error('Safe execute error:', error);
        return fallback;
    }
}

/**
 * Проверка поддержки localStorage
 * @returns {boolean}
 */
export function isLocalStorageAvailable() {
    try {
        const test = '__localStorage_test__';
        localStorage.setItem(test, test);
        localStorage.removeItem(test);
        return true;
    } catch (e) {
        return false;
    }
}

/**
 * Безопасная работа с localStorage
 */
export const storage = {
    get(key, defaultValue = null) {
        if (!isLocalStorageAvailable()) return defaultValue;

        try {
            const item = localStorage.getItem(key);
            return item ? JSON.parse(item) : defaultValue;
        } catch (error) {
            console.error('Storage get error:', error);
            return defaultValue;
        }
    },

    set(key, value) {
        if (!isLocalStorageAvailable()) return false;

        try {
            localStorage.setItem(key, JSON.stringify(value));
            return true;
        } catch (error) {
            console.error('Storage set error:', error);
            return false;
        }
    },

    remove(key) {
        if (!isLocalStorageAvailable()) return false;

        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.error('Storage remove error:', error);
            return false;
        }
    },

    clear() {
        if (!isLocalStorageAvailable()) return false;

        try {
            localStorage.clear();
            return true;
        } catch (error) {
            console.error('Storage clear error:', error);
            return false;
        }
    }
};