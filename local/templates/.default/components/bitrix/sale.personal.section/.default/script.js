/**
 * Скрипты личного кабинета
 * Компонент: sale.personal.section
 * 
 * @version 1.2.0
 * @author KW https://kowb.ru
 */

(function() {
    'use strict';

    const accountModule = {
        selectors: {
            mobileToggle: '.edsys-account__mobile-toggle',
            sidebar: '.edsys-account__sidebar',
            menuLink: '.edsys-account__menu-link',
            accountSection: '.edsys-account'
        },

        init() {
            this.setupMobileMenu();
            this.setupAccessibility();
            this.handleMenuState();
            this.hideSocialButtons();
        },

        setupMobileMenu() {
            const toggle = document.querySelector(this.selectors.mobileToggle);
            const sidebar = document.querySelector(this.selectors.sidebar);
            const accountSection = document.querySelector(this.selectors.accountSection);

            if (!toggle || !sidebar || !accountSection) return;

            const openMenu = () => {
                sidebar.classList.add('is-open');
                accountSection.classList.add('has-overlay');
                document.body.classList.add('edsys-menu-open');
                toggle.setAttribute('aria-expanded', 'true');
                toggle.setAttribute('aria-label', 'Закрыть меню личного кабинета');
                sidebar.querySelector(this.selectors.menuLink)?.focus();
            };

            const closeMenu = () => {
                sidebar.classList.remove('is-open');
                accountSection.classList.remove('has-overlay');
                document.body.classList.remove('edsys-menu-open');
                toggle.setAttribute('aria-expanded', 'false');
                toggle.setAttribute('aria-label', 'Открыть меню личного кабинета');
            };

            toggle.addEventListener('click', () => {
                const isOpen = sidebar.classList.contains('is-open');
                isOpen ? closeMenu() : openMenu();
            });

            accountSection.addEventListener('click', (e) => {
                if (accountSection.classList.contains('has-overlay') && 
                    !sidebar.contains(e.target) && 
                    !toggle.contains(e.target)) {
                    closeMenu();
                }
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
                    closeMenu();
                }
            });

            const mediaQuery = window.matchMedia('(min-width: 48rem)');
            const handleMediaChange = (e) => {
                if (e.matches) {
                    closeMenu();
                }
            };

            mediaQuery.addEventListener('change', handleMediaChange);
        },

        setupAccessibility() {
            const menuLinks = document.querySelectorAll(this.selectors.menuLink);

            menuLinks.forEach(link => {
                link.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' || e.key === ' ') {
                        e.preventDefault();
                        link.click();
                    }
                });
            });
        },

        handleMenuState() {
            const currentPath = window.location.pathname;
            const menuLinks = document.querySelectorAll(this.selectors.menuLink);

            menuLinks.forEach(link => {
                const linkPath = new URL(link.href).pathname;
                
                if (currentPath.startsWith(linkPath) && linkPath !== '/personal/') {
                    link.classList.add('is-active');
                    link.setAttribute('aria-current', 'page');
                } else {
                    link.classList.remove('is-active');
                    link.setAttribute('aria-current', 'false');
                }
            });
        },

        hideSocialButtons() {
            const socialSelectors = [
                '.bx-auth-services',
                '.bx-ss-buttons',
                '.bx-soc-auth-title',
                '[class*="socserv"]',
                '[class*="social-services"]',
                '.social-auth'
            ];

            const hideElements = () => {
                socialSelectors.forEach(selector => {
                    document.querySelectorAll(selector).forEach(el => {
                        el.style.display = 'none';
                        el.remove();
                    });
                });
            };

            hideElements();

            const observer = new MutationObserver(hideElements);
            observer.observe(document.body, {
                childList: true,
                subtree: true
            });
        }
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', () => accountModule.init());
    } else {
        accountModule.init();
    }
})();