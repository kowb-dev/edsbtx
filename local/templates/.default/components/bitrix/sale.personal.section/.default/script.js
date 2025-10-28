/**
 * Скрипты личного кабинета
 * Компонент: sale.personal.section
 * 
 * @version 1.1.0
 * @author KW https://kowb.ru
 */

(function() {
    'use strict';

    const accountModule = {
        selectors: {
            mobileToggle: '.edsys-account__mobile-toggle',
            sidebar: '.edsys-account__sidebar',
            menuLink: '.edsys-account__menu-link'
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

            if (!toggle || !sidebar) return;

            toggle.addEventListener('click', () => {
                const isOpen = sidebar.classList.toggle('is-open');
                toggle.setAttribute('aria-expanded', isOpen);
                
                if (isOpen) {
                    sidebar.querySelector(this.selectors.menuLink)?.focus();
                }
            });

            const mediaQuery = window.matchMedia('(min-width: 48rem)');
            const handleMediaChange = (e) => {
                if (e.matches) {
                    sidebar.classList.remove('is-open');
                    toggle.setAttribute('aria-expanded', 'false');
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