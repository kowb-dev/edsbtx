/**
 * EDS Certificates Page JavaScript
 * Handles modal functionality and user interactions
 */

const EdsysCertificates = {
    // State management
    state: {
        currentModalIndex: 0,
        certificates: [],
        zoomLevel: 100,
        isDragging: false,
        dragStart: { x: 0, y: 0 },
        imagePosition: { x: 0, y: 0 }
    },

    // Initialize the application
    init() {
        this.bindEvents();
        this.collectCertificates();
        this.setupKeyboardShortcuts();
        this.setupLazyLoading();
    },

    // Collect all certificates from DOM
    collectCertificates() {
        const cards = document.querySelectorAll('.edsys-certificate-card');
        this.state.certificates = Array.from(cards).map((card, index) => ({
            element: card,
            id: card.querySelector('.edsys-view-button').dataset.certId,
            title: card.querySelector('.edsys-view-button').dataset.certTitle,
            image: card.querySelector('.edsys-view-button').dataset.certImage,
            description: card.querySelector('.edsys-view-button').dataset.certDescription,
            type: card.querySelector('.edsys-view-button').dataset.certType,
            valid: card.querySelector('.edsys-view-button').dataset.certValid,
            index: index
        }));
    },

    // Bind all event listeners
    bindEvents() {
        // View buttons
        document.querySelectorAll('.edsys-view-button').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const certId = button.dataset.certId;
                this.openModal(certId);
            });
        });

        // Modal backdrop click
        const modalBackdrop = document.querySelector('.edsys-modal-backdrop');
        if (modalBackdrop) {
            modalBackdrop.addEventListener('click', () => {
                this.closeModal();
            });
        }

        // Image dragging
        this.setupImageDragging();

        // Window resize
        window.addEventListener('resize', () => {
            this.handleResize();
        });
    },

    // Open modal with specific certificate
    openModal(certId) {
        const cert = this.state.certificates.find(c => c.id === certId);
        if (!cert) return;

        this.state.currentModalIndex = this.state.certificates.findIndex(c => c.id === certId);

        const modal = document.getElementById('certificateModal');
        if (!modal) return;

        // Set modal content
        this.updateModalContent(cert);

        // Show modal
        modal.classList.add('edsys-active');
        modal.setAttribute('aria-hidden', 'false');

        // Focus management
        const closeButton = modal.querySelector('.edsys-modal-close-btn');
        if (closeButton) {
            closeButton.focus();
        }

        // Prevent body scroll
        document.body.style.overflow = 'hidden';

        // Reset zoom and position
        this.resetImageTransform();

        // Preload adjacent images
        this.preloadImages();
    },

    // Update modal content
    updateModalContent(cert) {
        // Title and meta
        const titleEl = document.getElementById('modalTitle');
        const typeEl = document.getElementById('modalType');
        const validEl = document.getElementById('modalValid');

        if (titleEl) titleEl.textContent = cert.title;
        if (typeEl) typeEl.textContent = cert.type;
        if (validEl) {
            validEl.textContent = cert.valid ? `до ${cert.valid}` : '';
            validEl.style.display = cert.valid ? 'inline' : 'none';
        }

        // Image
        const imageEl = document.getElementById('modalImage');
        const loadingEl = document.getElementById('modalLoading');

        if (imageEl && loadingEl) {
            loadingEl.style.display = 'flex';
            imageEl.style.display = 'none';

            imageEl.src = cert.image;
            imageEl.alt = cert.title;

            imageEl.onload = () => {
                loadingEl.style.display = 'none';
                imageEl.style.display = 'block';
            };
        }

        // Description and details
        const descEl = document.getElementById('modalDescription');
        const typeDetailEl = document.getElementById('modalTypeDetail');
        const validValueEl = document.getElementById('modalValidValue');
        const validDetailEl = document.getElementById('modalValidDetail');

        if (descEl) descEl.textContent = cert.description;
        if (typeDetailEl) typeDetailEl.textContent = cert.type;
        if (validValueEl) validValueEl.textContent = cert.valid || '—';
        if (validDetailEl) {
            validDetailEl.style.display = cert.valid ? 'flex' : 'none';
        }

        // Counter
        const counterEl = document.getElementById('modalCounter');
        if (counterEl) {
            counterEl.textContent = `${this.state.currentModalIndex + 1} из ${this.state.certificates.length}`;
        }

        // Navigation buttons state
        this.updateNavigationButtons();
    },

    // Update navigation buttons state
    updateNavigationButtons() {
        const prevBtns = [
            document.getElementById('modalPrevBtn'),
            document.getElementById('modalPrevBtnFooter')
        ];
        const nextBtns = [
            document.getElementById('modalNextBtn'),
            document.getElementById('modalNextBtnFooter')
        ];

        const hasPrev = this.state.currentModalIndex > 0;
        const hasNext = this.state.currentModalIndex < this.state.certificates.length - 1;

        prevBtns.forEach(btn => {
            if (btn) {
                btn.disabled = !hasPrev;
                btn.style.opacity = hasPrev ? '1' : '0.5';
            }
        });

        nextBtns.forEach(btn => {
            if (btn) {
                btn.disabled = !hasNext;
                btn.style.opacity = hasNext ? '1' : '0.5';
            }
        });
    },

    // Navigate modal (prev/next)
    navigateModal(direction) {
        const newIndex = direction === 'next'
            ? this.state.currentModalIndex + 1
            : this.state.currentModalIndex - 1;

        if (newIndex >= 0 && newIndex < this.state.certificates.length) {
            this.state.currentModalIndex = newIndex;
            const cert = this.state.certificates[newIndex];
            this.updateModalContent(cert);
            this.resetImageTransform();
        }
    },

    // Close modal
    closeModal() {
        const modal = document.getElementById('certificateModal');
        if (!modal) return;

        modal.classList.remove('edsys-active');
        modal.setAttribute('aria-hidden', 'true');

        // Restore body scroll
        document.body.style.overflow = '';

        // Return focus to trigger button
        const currentCert = this.state.certificates[this.state.currentModalIndex];
        if (currentCert) {
            const triggerButton = currentCert.element.querySelector('.edsys-view-button');
            if (triggerButton) {
                triggerButton.focus();
            }
        }
    },

    // Zoom modal image
    zoomModal(action) {
        const imageWrapper = document.getElementById('modalImageWrapper');
        const zoomLevelEl = document.getElementById('zoomLevel');

        if (!imageWrapper || !zoomLevelEl) return;

        switch (action) {
            case 'in':
                this.state.zoomLevel = Math.min(this.state.zoomLevel + 25, 300);
                break;
            case 'out':
                this.state.zoomLevel = Math.max(this.state.zoomLevel - 25, 50);
                break;
            case 'reset':
                this.state.zoomLevel = 100;
                this.state.imagePosition = { x: 0, y: 0 };
                break;
        }

        this.applyImageTransform();
        zoomLevelEl.textContent = `${this.state.zoomLevel}%`;
    },

    // Apply image transform
    applyImageTransform() {
        const imageWrapper = document.getElementById('modalImageWrapper');
        if (!imageWrapper) return;

        const scale = this.state.zoomLevel / 100;
        const { x, y } = this.state.imagePosition;

        imageWrapper.style.transform = `scale(${scale}) translate(${x}px, ${y}px)`;
    },

    // Reset image transform
    resetImageTransform() {
        this.state.zoomLevel = 100;
        this.state.imagePosition = { x: 0, y: 0 };
        this.applyImageTransform();

        const zoomLevelEl = document.getElementById('zoomLevel');
        if (zoomLevelEl) {
            zoomLevelEl.textContent = '100%';
        }
    },

    // Setup image dragging
    setupImageDragging() {
        const imageContainer = document.getElementById('modalImageContainer');
        if (!imageContainer) return;

        let isPointerDown = false;
        let startPoint = { x: 0, y: 0 };

        // Mouse events
        imageContainer.addEventListener('mousedown', (e) => {
            if (this.state.zoomLevel <= 100) return;
            isPointerDown = true;
            startPoint = { x: e.clientX, y: e.clientY };
            imageContainer.style.cursor = 'grabbing';
        });

        document.addEventListener('mousemove', (e) => {
            if (!isPointerDown || this.state.zoomLevel <= 100) return;

            const deltaX = e.clientX - startPoint.x;
            const deltaY = e.clientY - startPoint.y;

            this.state.imagePosition.x += deltaX;
            this.state.imagePosition.y += deltaY;

            this.applyImageTransform();

            startPoint = { x: e.clientX, y: e.clientY };
        });

        document.addEventListener('mouseup', () => {
            isPointerDown = false;
            imageContainer.style.cursor = this.state.zoomLevel > 100 ? 'grab' : 'default';
        });

        // Touch events for better mobile performance
        imageContainer.addEventListener('touchstart', (e) => {
            if (this.state.zoomLevel <= 100 || e.touches.length !== 1) return;
            isPointerDown = true;
            startPoint = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }, { passive: false });

        imageContainer.addEventListener('touchmove', (e) => {
            if (!isPointerDown || this.state.zoomLevel <= 100 || e.touches.length !== 1) return;
            e.preventDefault();

            const deltaX = e.touches[0].clientX - startPoint.x;
            const deltaY = e.touches[0].clientY - startPoint.y;

            this.state.imagePosition.x += deltaX;
            this.state.imagePosition.y += deltaY;

            this.applyImageTransform();

            startPoint = { x: e.touches[0].clientX, y: e.touches[0].clientY };
        }, { passive: false });

        imageContainer.addEventListener('touchend', () => {
            isPointerDown = false;
        }, { passive: true });

        // Double-click to zoom
        imageContainer.addEventListener('dblclick', () => {
            if (this.state.zoomLevel === 100) {
                this.zoomModal('in');
                this.zoomModal('in');
            } else {
                this.zoomModal('reset');
            }
        });
    },

    // Setup keyboard shortcuts (minimal - only modal controls)
    setupKeyboardShortcuts() {
        document.addEventListener('keydown', (e) => {
            const modal = document.getElementById('certificateModal');
            const isModalOpen = modal && modal.classList.contains('edsys-active');

            if (isModalOpen) {
                switch (e.key) {
                    case 'Escape':
                        e.preventDefault();
                        this.closeModal();
                        break;
                }
            }
        });
    },

    // Print certificate
    printCertificate() {
        window.print();
    },

    // Share certificate
    shareCertificate() {
        const currentCert = this.state.certificates[this.state.currentModalIndex];
        if (!currentCert) return;

        const url = `${window.location.origin}${window.location.pathname}?cert=${currentCert.id}`;

        if (navigator.share) {
            navigator.share({
                title: currentCert.title,
                text: currentCert.description,
                url: url
            });
        } else {
            // Fallback - copy to clipboard
            navigator.clipboard.writeText(url).then(() => {
                // Show tooltip or notification
                this.showNotification('Ссылка скопирована в буфер обмена');
            });
        }
    },

    // Show notification
    showNotification(message) {
        const notification = document.createElement('div');
        notification.className = 'edsys-notification';
        notification.textContent = message;

        Object.assign(notification.style, {
            position: 'fixed',
            bottom: '2rem',
            left: '50%',
            transform: 'translateX(-50%)',
            backgroundColor: 'var(--edsys-accent)',
            color: 'white',
            padding: '1rem 2rem',
            borderRadius: 'var(--radius-lg)',
            zIndex: '1002',
            opacity: '0',
            transition: 'opacity 0.3s ease'
        });

        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.opacity = '1';
        }, 100);

        setTimeout(() => {
            notification.style.opacity = '0';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    },

    // Setup lazy loading for images
    setupLazyLoading() {
        const images = document.querySelectorAll('.edsys-cert-thumbnail[loading="lazy"]');

        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        img.src = img.src || img.dataset.src;
                        img.classList.remove('lazy');
                        imageObserver.unobserve(img);
                    }
                });
            });

            images.forEach(img => imageObserver.observe(img));
        }
    },

    // Preload adjacent images in modal
    preloadImages() {
        const prevIndex = this.state.currentModalIndex - 1;
        const nextIndex = this.state.currentModalIndex + 1;

        [prevIndex, nextIndex].forEach(index => {
            if (index >= 0 && index < this.state.certificates.length) {
                const cert = this.state.certificates[index];
                const img = new Image();
                img.src = cert.image;
            }
        });
    },

    // Handle window resize
    handleResize() {
        // Reset image position on resize
        const modal = document.getElementById('certificateModal');
        if (modal && modal.classList.contains('edsys-active')) {
            this.resetImageTransform();
        }
    }
};

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    // Check if we're on certificates page
    if (document.querySelector('.edsys-certificates-page')) {
        EdsysCertificates.init();
    }
});

// Handle page visibility change
document.addEventListener('visibilitychange', () => {
    if (document.visibilityState === 'visible' && document.querySelector('.edsys-certificates-page')) {
        // Refresh data if needed
        EdsysCertificates.collectCertificates();
    }
});