/**
 * Cable Schemes Page JavaScript
 * Функциональность страницы схем распайки кабелей
 */

class CableSchemesPage {
    constructor() {
        this.init();
    }

    init() {
        this.setupSmoothScrolling();
        this.setupSidebarNavigation();
        this.setupMobileNavigation();
        this.setupImageZoom();
        this.setupKeyboardNavigation();
        this.observeSchemes();
    }

    /**
     * Setup smooth scrolling for anchor links
     */
    setupSmoothScrolling() {
        const links = document.querySelectorAll('a[href^="#"]');

        links.forEach(link => {
            link.addEventListener('click', (e) => {
                e.preventDefault();

                const targetId = link.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);

                if (targetElement) {
                    // Calculate offset for sticky header
                    const headerHeight = 100;
                    const elementPosition = targetElement.getBoundingClientRect().top;
                    const offsetPosition = elementPosition + window.pageYOffset - headerHeight;

                    window.scrollTo({
                        top: offsetPosition,
                        behavior: 'smooth'
                    });

                    // Update URL without jumping
                    history.pushState(null, null, '#' + targetId);

                    // Update active states
                    this.updateActiveNavigation(targetId);
                }
            });
        });
    }

    /**
     * Setup sidebar navigation highlighting
     */
    setupSidebarNavigation() {
        const sidebarLinks = document.querySelectorAll('.edsys-schemes__sidebar-link');

        sidebarLinks.forEach(link => {
            link.addEventListener('click', () => {
                // Remove active class from all links
                sidebarLinks.forEach(l => l.classList.remove('active'));
                // Add active class to clicked link
                link.classList.add('active');
            });
        });
    }

    /**
     * Setup mobile navigation
     */
    setupMobileNavigation() {
        const mobileMenuBtn = document.querySelector('[data-action="toggle-mobile-menu"]');
        const quickMenu = document.querySelector('.edsys-schemes__quick-menu');
        const backToTopBtn = document.querySelector('[data-action="back-to-top"]');
        const shareBtn = document.querySelector('[data-action="share-page"]');

        // Mobile menu toggle
        if (mobileMenuBtn && quickMenu) {
            mobileMenuBtn.addEventListener('click', () => {
                quickMenu.classList.toggle('active');
            });

            // Close menu when clicking outside
            quickMenu.addEventListener('click', (e) => {
                if (e.target === quickMenu) {
                    quickMenu.classList.remove('active');
                }
            });
        }

        // Back to top functionality
        if (backToTopBtn) {
            backToTopBtn.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            // Show/hide back to top button based on scroll position
            window.addEventListener('scroll', this.throttle(() => {
                if (window.pageYOffset > 300) {
                    backToTopBtn.style.opacity = '1';
                    backToTopBtn.style.pointerEvents = 'auto';
                } else {
                    backToTopBtn.style.opacity = '0';
                    backToTopBtn.style.pointerEvents = 'none';
                }
            }, 100));
        }

        // Share functionality
        if (shareBtn) {
            shareBtn.addEventListener('click', this.handleShare.bind(this));
        }
    }

    /**
     * Setup image zoom functionality
     */
    setupImageZoom() {
        const images = document.querySelectorAll('.edsys-schemes__image');

        images.forEach(image => {
            image.addEventListener('click', () => {
                this.openImageModal(image);
            });

            // Add cursor pointer to indicate clickability
            image.style.cursor = 'pointer';
        });
    }

    /**
     * Setup keyboard navigation
     */
    setupKeyboardNavigation() {
        document.addEventListener('keydown', (e) => {
            // ESC key closes modals
            if (e.key === 'Escape') {
                this.closeImageModal();
                document.querySelector('.edsys-schemes__quick-menu')?.classList.remove('active');
            }

            // Arrow keys for navigation between sections
            if (e.key === 'ArrowUp' || e.key === 'ArrowDown') {
                if (e.ctrlKey || e.metaKey) {
                    e.preventDefault();
                    this.navigateSections(e.key === 'ArrowUp' ? 'prev' : 'next');
                }
            }
        });
    }

    /**
     * Observe scheme sections for active navigation updates
     */
    observeSchemes() {
        const sections = document.querySelectorAll('.edsys-schemes__section');

        if (sections.length === 0) return;

        const observerOptions = {
            root: null,
            rootMargin: '-20% 0px -60% 0px',
            threshold: 0
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const sectionId = entry.target.id;
                    this.updateActiveNavigation(sectionId);
                }
            });
        }, observerOptions);

        sections.forEach(section => {
            observer.observe(section);
        });
    }

    /**
     * Update active navigation highlighting
     */
    updateActiveNavigation(activeId) {
        // Update sidebar navigation
        const sidebarLinks = document.querySelectorAll('.edsys-schemes__sidebar-link');
        sidebarLinks.forEach(link => {
            const href = link.getAttribute('href');
            if (href === '#' + activeId) {
                link.classList.add('active');
            } else {
                link.classList.remove('active');
            }
        });

        // Update navigation cards
        const navCards = document.querySelectorAll('.edsys-schemes__nav-card');
        navCards.forEach(card => {
            const href = card.getAttribute('href');
            if (href === '#' + activeId) {
                card.classList.add('active');
            } else {
                card.classList.remove('active');
            }
        });
    }

    /**
     * Open image in modal
     */
    openImageModal(image) {
        // Create modal if it doesn't exist
        let modal = document.querySelector('.edsys-schemes__image-modal');

        if (!modal) {
            modal = document.createElement('div');
            modal.className = 'edsys-schemes__image-modal';
            modal.innerHTML = `
                <div class="edsys-schemes__image-modal-overlay">
                    <div class="edsys-schemes__image-modal-content">
                        <button class="edsys-schemes__image-modal-close" aria-label="Закрыть">
                            <i class="ph ph-thin ph-x"></i>
                        </button>
                        <img class="edsys-schemes__image-modal-img" alt="">
                    </div>
                </div>
            `;
            document.body.appendChild(modal);

            // Add modal styles
            const modalStyles = `
                .edsys-schemes__image-modal {
                    position: fixed;
                    top: 0;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    background: rgba(0, 0, 0, 0.9);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    z-index: 2000;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                }
                
                .edsys-schemes__image-modal.active {
                    opacity: 1;
                    visibility: visible;
                }
                
                .edsys-schemes__image-modal-content {
                    position: relative;
                    max-width: 95%;
                    max-height: 95%;
                }
                
                .edsys-schemes__image-modal-img {
                    max-width: 100%;
                    max-height: 100%;
                    object-fit: contain;
                    border-radius: 8px;
                }
                
                .edsys-schemes__image-modal-close {
                    position: absolute;
                    top: -40px;
                    right: 0;
                    background: rgba(255, 255, 255, 0.2);
                    border: none;
                    color: white;
                    width: 32px;
                    height: 32px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    cursor: pointer;
                    transition: background 0.2s ease;
                }
                
                .edsys-schemes__image-modal-close:hover {
                    background: rgba(255, 255, 255, 0.3);
                }
            `;

            if (!document.querySelector('#image-modal-styles')) {
                const styleSheet = document.createElement('style');
                styleSheet.id = 'image-modal-styles';
                styleSheet.innerHTML = modalStyles;
                document.head.appendChild(styleSheet);
            }

            // Setup modal event listeners
            modal.querySelector('.edsys-schemes__image-modal-close').addEventListener('click', () => {
                this.closeImageModal();
            });

            modal.querySelector('.edsys-schemes__image-modal-overlay').addEventListener('click', (e) => {
                if (e.target === e.currentTarget) {
                    this.closeImageModal();
                }
            });
        }

        // Set image source and alt text
        const modalImg = modal.querySelector('.edsys-schemes__image-modal-img');
        modalImg.src = image.src;
        modalImg.alt = image.alt;

        // Show modal
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    /**
     * Close image modal
     */
    closeImageModal() {
        const modal = document.querySelector('.edsys-schemes__image-modal');
        if (modal) {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    /**
     * Navigate between sections
     */
    navigateSections(direction) {
        const sections = Array.from(document.querySelectorAll('.edsys-schemes__section'));
        const currentIndex = sections.findIndex(section => {
            const rect = section.getBoundingClientRect();
            return rect.top >= 0 && rect.top <= window.innerHeight / 2;
        });

        let targetIndex;
        if (direction === 'next') {
            targetIndex = Math.min(currentIndex + 1, sections.length - 1);
        } else {
            targetIndex = Math.max(currentIndex - 1, 0);
        }

        if (sections[targetIndex]) {
            sections[targetIndex].scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    }

    /**
     * Handle page sharing
     */
    handleShare() {
        const shareData = {
            title: document.title,
            text: 'Схемы распайки кабелей - EDS',
            url: window.location.href
        };

        if (navigator.share) {
            navigator.share(shareData);
        } else {
            // Fallback: copy URL to clipboard
            navigator.clipboard.writeText(window.location.href).then(() => {
                this.showNotification('Ссылка скопирована в буфер обмена');
            }).catch(() => {
                // Final fallback: show URL in prompt
                prompt('Скопируйте ссылку:', window.location.href);
            });
        }
    }

    /**
     * Show notification message
     */
    showNotification(message) {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = 'edsys-schemes__notification';
        notification.textContent = message;
        notification.style.cssText = `
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--edsys-accent);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s ease;
        `;

        document.body.appendChild(notification);

        // Show notification
        setTimeout(() => {
            notification.style.opacity = '1';
        }, 10);

        // Hide and remove notification
        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    /**
     * Throttle function for performance optimization
     */
    throttle(func, limit) {
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
}

// Initialize page functionality when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
    new CableSchemesPage();
});

// Handle page load from history (back/forward buttons)
window.addEventListener('popstate', () => {
    const hash = window.location.hash.substring(1);
    if (hash) {
        const element = document.getElementById(hash);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }
});