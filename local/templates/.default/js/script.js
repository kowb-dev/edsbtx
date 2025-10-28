/**
 * EDS Compare Module
 * Handles product comparison functionality
 * 
 * @version 1.0.0
 * @author KW
 * @uri https://kowb.ru
 * 
 * Add this to existing script.js file
 */

class EDSCompare {
    constructor() {
        this.compareButtons = [];
        this.notificationContainer = null;
        this.init();
    }

    /**
     * Initialize compare functionality
     */
    init() {
        this.createNotificationContainer();
        this.initCompareButtons();
        this.loadCompareStates();
    }

    /**
     * Create notification container if it doesn't exist
     */
    createNotificationContainer() {
        let container = document.getElementById('edsys-notifications');
        if (!container) {
            container = document.createElement('div');
            container.id = 'edsys-notifications';
            container.className = 'edsys-notifications';
            document.body.appendChild(container);
        }
        this.notificationContainer = container;
    }

    /**
     * Initialize all compare buttons on the page
     */
    initCompareButtons() {
        const buttons = document.querySelectorAll('[data-compare-action="toggle"]');
        
        buttons.forEach(button => {
            const productId = parseInt(button.dataset.productId);
            
            if (!productId) {
                console.error('Compare button missing product ID:', button);
                return;
            }

            // Store button reference
            this.compareButtons.push({
                element: button,
                productId: productId
            });

            // Add click handler
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                this.toggleCompare(productId, button);
            });
        });
    }

    /**
     * Load compare states for all buttons from session
     */
    async loadCompareStates() {
        try {
            const response = await fetch('/local/ajax/compare/get_status.php', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success && data.compareList) {
                // Update all buttons based on compare list
                this.compareButtons.forEach(({ element, productId }) => {
                    const inCompare = data.compareList.includes(productId);
                    this.updateButtonState(element, inCompare);
                });
            }
        } catch (error) {
            console.error('Error loading compare states:', error);
        }
    }

    /**
     * Toggle product in compare
     * @param {number} productId - Product ID
     * @param {HTMLElement} button - Button element
     */
    async toggleCompare(productId, button) {
        // Prevent double clicks
        if (button.disabled) return;
        
        button.disabled = true;

        try {
            const formData = new FormData();
            formData.append('productId', productId);
            formData.append('sessid', this.getSessid());

            const response = await fetch('/local/ajax/compare/add.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });

            if (!response.ok) {
                throw new Error('Network response was not ok');
            }

            const data = await response.json();

            if (data.success) {
                // Update button state
                this.updateButtonState(button, data.data.inCompare);
                
                // Update all buttons for this product
                this.updateAllButtonsForProduct(productId, data.data.inCompare);

                // Show notification
                this.showNotification(
                    data.message,
                    data.data.action === 'added' ? 'success' : 'info'
                );

                // Dispatch custom event for other scripts
                window.dispatchEvent(new CustomEvent('edsys:compareUpdated', {
                    detail: {
                        productId: productId,
                        inCompare: data.data.inCompare,
                        compareCount: data.data.compareCount
                    }
                }));
            } else {
                throw new Error(data.message || 'Ошибка добавления в сравнение');
            }
        } catch (error) {
            console.error('Error toggling compare:', error);
            this.showNotification(
                error.message || 'Произошла ошибка. Попробуйте позже.',
                'error'
            );
        } finally {
            button.disabled = false;
        }
    }

    /**
     * Update button visual state
     * @param {HTMLElement} button - Button element
     * @param {boolean} inCompare - Is product in compare
     */
    updateButtonState(button, inCompare) {
        if (inCompare) {
            button.classList.add('active');
            button.setAttribute('aria-pressed', 'true');
            button.setAttribute('title', 'Удалить из сравнения');
        } else {
            button.classList.remove('active');
            button.setAttribute('aria-pressed', 'false');
            button.setAttribute('title', 'Добавить к сравнению');
        }
    }

    /**
     * Update all buttons for a specific product
     * @param {number} productId - Product ID
     * @param {boolean} inCompare - Is product in compare
     */
    updateAllButtonsForProduct(productId, inCompare) {
        this.compareButtons.forEach(({ element, productId: btnProductId }) => {
            if (btnProductId === productId) {
                this.updateButtonState(element, inCompare);
            }
        });
    }

    /**
     * Show notification
     * @param {string} message - Notification message
     * @param {string} type - Notification type (success, error, info, warning)
     */
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = 'edsys-notification';
        notification.setAttribute('role', 'alert');

        const colors = {
            success: '#28a745',
            error: '#dc3545',
            warning: '#ffc107',
            info: '#17a2b8'
        };

        const bgColors = {
            success: '#d4edda',
            error: '#f8d7da',
            warning: '#fff3cd',
            info: '#d1ecf1'
        };

        const icons = {
            success: 'ph-check-circle',
            error: 'ph-x-circle',
            warning: 'ph-warning-circle',
            info: 'ph-info'
        };

        notification.style.cssText = `
            background: ${bgColors[type] || bgColors.info};
            border-left: 4px solid ${colors[type] || colors.info};
            padding: 1rem;
            border-radius: 0.375rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            display: flex;
            align-items: center;
            gap: 0.75rem;
            animation: slideInRight 0.3s ease;
            font-size: 0.875rem;
            color: #333;
            min-width: 300px;
            max-width: 500px;
        `;

        notification.innerHTML = `
            <i class="ph ph-thin ${icons[type] || icons.info}" style="font-size: 1.5rem; color: ${colors[type]}; flex-shrink: 0;"></i>
            <span style="flex: 1;">${message}</span>
            <button type="button" class="edsys-notification-close" style="background: none; border: none; cursor: pointer; color: #999; font-size: 1.25rem; padding: 0; line-height: 1; flex-shrink: 0;" aria-label="Закрыть уведомление">
                <i class="ph ph-thin ph-x"></i>
            </button>
        `;

        // Add close handler
        const closeBtn = notification.querySelector('.edsys-notification-close');
        closeBtn.addEventListener('click', () => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        });

        this.notificationContainer.appendChild(notification);

        // Auto remove after 3 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => {
                    if (notification.parentElement) {
                        notification.remove();
                    }
                }, 300);
            }
        }, 3000);

        // Add CSS animation if not exists
        this.ensureNotificationStyles();
    }

    /**
     * Ensure notification styles are added
     */
    ensureNotificationStyles() {
        if (!document.getElementById('edsys-notification-styles')) {
            const style = document.createElement('style');
            style.id = 'edsys-notification-styles';
            style.textContent = `
                .edsys-notifications {
                    position: fixed;
                    top: 1rem;
                    right: 1rem;
                    z-index: 10000;
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                    pointer-events: none;
                }
                
                .edsys-notification {
                    pointer-events: auto;
                }
                
                @keyframes slideInRight {
                    from {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                    to {
                        transform: translateX(0);
                        opacity: 1;
                    }
                }
                
                @keyframes slideOutRight {
                    from {
                        transform: translateX(0);
                        opacity: 1;
                    }
                    to {
                        transform: translateX(100%);
                        opacity: 0;
                    }
                }
                
                @media (max-width: 768px) {
                    .edsys-notifications {
                        left: 1rem;
                        right: 1rem;
                        top: auto;
                        bottom: 1rem;
                    }
                    
                    .edsys-notification {
                        min-width: auto;
                        max-width: none;
                    }
                    
                    @keyframes slideInRight {
                        from {
                            transform: translateY(100%);
                            opacity: 0;
                        }
                        to {
                            transform: translateY(0);
                            opacity: 1;
                        }
                    }
                    
                    @keyframes slideOutRight {
                        from {
                            transform: translateY(0);
                            opacity: 1;
                        }
                        to {
                            transform: translateY(100%);
                            opacity: 0;
                        }
                    }
                }
            `;
            document.head.appendChild(style);
        }
    }

    /**
     * Get Bitrix session ID
     * @returns {string} Session ID
     */
    getSessid() {
        // Try to get from meta tag
        const meta = document.querySelector('meta[name="bitrix-sessid"]');
        if (meta) {
            return meta.getAttribute('content');
        }

        // Try to get from global variable
        if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
            return BX.bitrix_sessid();
        }

        // Fallback - try to get from any form
        const input = document.querySelector('input[name="sessid"]');
        if (input) {
            return input.value;
        }

        console.error('Could not find Bitrix session ID');
        return '';
    }
}

// Initialize compare functionality when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
        window.edsysCompare = new EDSCompare();
    });
} else {
    window.edsysCompare = new EDSCompare();
}

// Re-initialize when new content is loaded dynamically
window.addEventListener('edsys:contentLoaded', () => {
    if (window.edsysCompare) {
        window.edsysCompare.init();
    }
});