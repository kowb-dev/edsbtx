/**
 * Footer Module
 * Управление футером, аккордеоном и формой подписки
 */

export class FooterModule {
    constructor() {
        this.footer = document.querySelector('.edsys-footer');
        this.sections = document.querySelectorAll('.edsys-footer__section');
        this.newsletterForm = document.querySelector('.edsys-footer__newsletter-form');

        this.init();
    }

    init() {
        if (!this.footer) return;

        this.initAccordion();
        this.initNewsletterForm();
        this.handleResize();
        this.observeFooterAnimation();
    }

    initAccordion() {
        this.sections.forEach(section => {
            const header = section.querySelector('.edsys-footer__section-header');
            const content = section.querySelector('.edsys-footer__section-content');

            if (!header || !content) return;

            header.addEventListener('click', () => {
                // Проверяем, что мы на мобильном устройстве
                if (window.innerWidth < 768) {
                    this.toggleSection(section);
                }
            });

            // Клавиатурная навигация
            header.addEventListener('keydown', (e) => {
                if ((e.key === 'Enter' || e.key === ' ') && window.innerWidth < 768) {
                    e.preventDefault();
                    this.toggleSection(section);
                }
            });
        });
    }

    toggleSection(section) {
        const isActive = section.classList.contains('active');

        // Закрываем все остальные секции
        this.sections.forEach(otherSection => {
            if (otherSection !== section) {
                this.closeSection(otherSection);
            }
        });

        // Переключаем текущую секцию
        if (isActive) {
            this.closeSection(section);
        } else {
            this.openSection(section);
        }
    }

    openSection(section) {
        const content = section.querySelector('.edsys-footer__section-content');
        if (!content) return;

        section.classList.add('active');

        // Анимация открытия
        const height = content.scrollHeight;
        content.style.maxHeight = height + 'px';

        // Устанавливаем фокус на первую ссылку
        setTimeout(() => {
            const firstLink = content.querySelector('a');
            if (firstLink) firstLink.focus();
        }, 300);
    }

    closeSection(section) {
        const content = section.querySelector('.edsys-footer__section-content');
        if (!content) return;

        section.classList.remove('active');
        content.style.maxHeight = '0';
    }

    initNewsletterForm() {
        if (!this.newsletterForm) return;

        this.newsletterForm.addEventListener('submit', (e) => {
            e.preventDefault();
            this.handleNewsletterSubmit();
        });

        // Валидация email в реальном времени
        const emailInput = this.newsletterForm.querySelector('input[type="email"]');
        if (emailInput) {
            emailInput.addEventListener('input', (e) => {
                this.validateEmail(e.target);
            });

            emailInput.addEventListener('blur', (e) => {
                this.validateEmail(e.target);
            });
        }
    }

    handleNewsletterSubmit() {
        const emailInput = this.newsletterForm.querySelector('input[type="email"]');
        const submitButton = this.newsletterForm.querySelector('button[type="submit"]');

        if (!emailInput || !submitButton) return;

        const email = emailInput.value.trim();

        // Валидация
        if (!this.isValidEmail(email)) {
            this.showError(emailInput, 'Пожалуйста, введите корректный email');
            return;
        }

        // Показываем состояние загрузки
        submitButton.disabled = true;
        submitButton.textContent = 'Отправка...';

        // Имитация отправки (замените на реальный API вызов)
        this.subscribeToNewsletter(email)
            .then(() => {
                this.showSuccess();
                this.newsletterForm.reset();
            })
            .catch((error) => {
                this.showError(emailInput, 'Произошла ошибка. Попробуйте позже.');
            })
            .finally(() => {
                submitButton.disabled = false;
                submitButton.textContent = 'Подписаться';
            });
    }

    async subscribeToNewsletter(email) {
        // Здесь должен быть реальный API вызов
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                // Имитация успешной подписки
                resolve();

                // Отправка события в аналитику
                if (typeof gtag !== 'undefined') {
                    gtag('event', 'newsletter_subscribe', {
                        email: email
                    });
                }
            }, 1000);
        });
    }

    validateEmail(input) {
        const email = input.value.trim();

        if (email === '') {
            this.clearError(input);
            return true;
        }

        if (this.isValidEmail(email)) {
            this.clearError(input);
            this.showValidState(input);
            return true;
        } else {
            this.showError(input, 'Некорректный формат email');
            return false;
        }
    }

    isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    showError(input, message) {
        input.classList.add('error');
        input.classList.remove('valid');

        // Удаляем предыдущее сообщение об ошибке
        this.clearError(input);

        // Создаем новое сообщение об ошибке
        const errorMsg = document.createElement('div');
        errorMsg.className = 'edsys-footer__error-message';
        errorMsg.textContent = message;
        input.parentElement.appendChild(errorMsg);
    }

    clearError(input) {
        input.classList.remove('error');

        const errorMsg = input.parentElement.querySelector('.edsys-footer__error-message');
        if (errorMsg) {
            errorMsg.remove();
        }
    }

    showValidState(input) {
        input.classList.add('valid');
    }

    showSuccess() {
        // Создаем сообщение об успехе
        const successMsg = document.createElement('div');
        successMsg.className = 'edsys-footer__success-message';
        successMsg.innerHTML = `
            <i class="ph ph-thin ph-check-circle"></i>
            <span>Спасибо за подписку! Мы будем держать вас в курсе наших новостей.</span>
        `;

        this.newsletterForm.appendChild(successMsg);

        // Удаляем сообщение через 5 секунд
        setTimeout(() => {
            successMsg.style.opacity = '0';
            setTimeout(() => successMsg.remove(), 300);
        }, 5000);
    }

    handleResize() {
        const handleResizeCallback = () => {
            if (window.innerWidth >= 768) {
                // На десктопе все секции открыты
                this.sections.forEach(section => {
                    section.classList.add('active');
                    const content = section.querySelector('.edsys-footer__section-content');
                    if (content) {
                        content.style.maxHeight = 'none';
                    }
                });
            } else {
                // На мобильном все секции закрыты
                this.sections.forEach(section => {
                    this.closeSection(section);
                });
            }
        };

        // Первоначальный вызов
        handleResizeCallback();

        // Обработчик изменения размера
        window.addEventListener('resize', handleResizeCallback);
    }

    observeFooterAnimation() {
        // Анимация элементов футера при появлении
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        // Наблюдаем за элементами
        const animatedElements = this.footer.querySelectorAll(
            '.edsys-footer__section, .edsys-footer__brand, .edsys-footer__newsletter'
        );

        animatedElements.forEach(el => {
            observer.observe(el);
        });
    }

    // Публичные методы
    onResize() {
        this.handleResize();
    }

    // Методы для работы с куками (для сохранения предпочтений)
    setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = `${name}=${value};expires=${expires.toUTCString()};path=/`;
    }

    getCookie(name) {
        const nameEQ = name + "=";
        const ca = document.cookie.split(';');

        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }

        return null;
    }

    // Сохранение предпочтений пользователя
    saveNewsletterPreference(subscribed) {
        this.setCookie('newsletter_subscribed', subscribed, 365);
    }

    checkNewsletterPreference() {
        return this.getCookie('newsletter_subscribed') === 'true';
    }
}