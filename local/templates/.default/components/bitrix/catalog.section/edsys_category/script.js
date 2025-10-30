/**
 * EDS Catalog Section JavaScript - Fixed Compare Integration
 * Version: 2.3.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Purpose: Catalog with fixed compare and favorites functionality
 * 
 * Changelog:
 * - 2.3.0: Fixed compare functionality - added proper event handlers and state management
 * - 2.2.0: Fixed favorites functionality
 */

// Check if class was already initialized
if (typeof window.EdsCatalogSection === 'undefined') {

    class EdsCatalogSection {
        constructor() {
            this.config = this.getConfig();
            this.activeFiltersCount = 0;
            this.lastScrollTop = 0;
            this.scrollDirection = 'down';
            this.isTogglingFavorite = false;
            this.isTogglingCompare = false; // Prevent multiple simultaneous compare requests

            this.elements = {
                sidebarFilters: document.getElementById('sidebar-filters'),
                mobileFilterBtns: document.querySelectorAll('.edsys-mobile-filter-btn'),
                filtersOverlay: document.getElementById('filters-overlay'),
                filtersClose: document.querySelector('.edsys-sidebar-filters__close'),
                filtersApply: document.querySelector('.edsys-filters__apply'),
                filtersReset: document.querySelector('.edsys-filters__reset'),
                filtersCounters: document.querySelectorAll('.edsys-filters-counter'),
                sortSelects: document.querySelectorAll('.edsys-sort__select'),
                productsGrid: document.getElementById('products-grid'),
                mobileControlsStatic: document.getElementById('mobile-controls-static'),
                mobileControlsFloating: document.getElementById('mobile-controls-floating'),
                compareFloating: document.getElementById('compare-floating-button')
            };

            this.init();
        }

        init() {
            this.initSidebarFilters();
            this.initSort();
            this.initQuickActions();
            this.initKeyboardNavigation();
            this.initImageGallery();
            this.initSmartStickyControls();
            this.updateFiltersCounter();
            this.initWishlistState();
            this.initCompareState();

            console.log('EDS Catalog initialized with config:', this.config);
        }

        getConfig() {
            const configElement = document.getElementById('catalog-config');
            if (configElement) {
                try {
                    return JSON.parse(configElement.textContent);
                } catch (e) {
                    console.error('Error parsing catalog config:', e);
                }
            }

            return {
                sectionId: null,
                iblockId: null,
                itemsPerPage: 40,
                currentPage: 1,
                totalPages: 1,
                totalItems: 0,
                isAuthorized: false
            };
        }

        /* ==========================================================================
           Smart sticky mobile controls
           ========================================================================== */

        initSmartStickyControls() {
            if (!this.elements.mobileControlsFloating) return;

            let ticking = false;

            const handleScroll = () => {
                if (!ticking) {
                    requestAnimationFrame(() => {
                        this.updateStickyControls();
                        ticking = false;
                    });
                    ticking = true;
                }
            };

            window.addEventListener('scroll', handleScroll, { passive: true });
            this.elements.mobileControlsFloating.classList.remove('show');
        }

        updateStickyControls() {
            const currentScrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const scrollThreshold = 200;

            if (currentScrollTop > this.lastScrollTop) {
                this.scrollDirection = 'down';
            } else if (currentScrollTop < this.lastScrollTop) {
                this.scrollDirection = 'up';
            }

            if (this.scrollDirection === 'up' && currentScrollTop > scrollThreshold) {
                this.elements.mobileControlsFloating?.classList.add('show');
            } else if (this.scrollDirection === 'down' || currentScrollTop <= scrollThreshold) {
                this.elements.mobileControlsFloating?.classList.remove('show');
            }

            this.lastScrollTop = currentScrollTop <= 0 ? 0 : currentScrollTop;
        }

        /* ==========================================================================
           Image gallery
           ========================================================================== */

        initImageGallery() {
            const productCards = document.querySelectorAll('.edsys-product-card');

            productCards.forEach(card => {
                const hasAdditional = card.dataset.hasAdditional === 'true';
                const imagesCount = parseInt(card.dataset.imagesCount) || 1;

                if (!hasAdditional || imagesCount <= 1) {
                    return;
                }

                if (card.dataset.galleryInitialized === 'true') {
                    return;
                }

                this.setupImageInteraction(card, imagesCount);
                card.dataset.galleryInitialized = 'true';
            });
        }

        setupImageInteraction(card, imagesCount) {
            const allImages = card.querySelectorAll('.edsys-product-card__image');
            const indicators = card.querySelectorAll('.edsys-image-indicator');
            const imageWrapper = card.querySelector('.edsys-product-card__image-wrapper');

            let currentImageIndex = 0;
            const imageElements = Array.from(allImages);

            this.showImageInstant(imageElements, indicators, 0);

            indicators.forEach(indicator => {
                indicator.addEventListener('click', (e) => {
                    e.preventDefault();
                    e.stopPropagation();

                    const targetImageIndex = parseInt(indicator.dataset.imageIndex);
                    currentImageIndex = targetImageIndex;
                    this.showImageInstant(imageElements, indicators, targetImageIndex);
                });
            });

            if (window.innerWidth > 768) {
                this.setupDesktopMouseControl(imageWrapper, imageElements, indicators, imagesCount, () => currentImageIndex, (index) => {
                    currentImageIndex = index;
                });
            }

            this.setupMobileSwipeControl(imageWrapper, imageElements, indicators, imagesCount, () => currentImageIndex, (index) => {
                currentImageIndex = index;
            });
        }

        setupDesktopMouseControl(imageWrapper, imageElements, indicators, imagesCount, getCurrentIndex, setCurrentIndex) {
            imageWrapper.addEventListener('mousemove', (e) => {
                const rect = imageWrapper.getBoundingClientRect();
                const mouseX = e.clientX - rect.left;
                const sectionWidth = rect.width / imagesCount;
                const targetIndex = Math.max(0, Math.min(imagesCount - 1, Math.floor(mouseX / sectionWidth)));

                if (targetIndex !== getCurrentIndex()) {
                    setCurrentIndex(targetIndex);
                    this.showImageInstant(imageElements, indicators, targetIndex);
                }
            });

            imageWrapper.addEventListener('mouseleave', () => {
                setCurrentIndex(0);
                this.showImageInstant(imageElements, indicators, 0);
            });
        }

        setupMobileSwipeControl(imageWrapper, imageElements, indicators, imagesCount, getCurrentIndex, setCurrentIndex) {
            if (!('ontouchstart' in window)) return;

            let touchStartX = 0;
            let touchEndX = 0;
            let isSwiping = false;

            imageWrapper.addEventListener('touchstart', (e) => {
                e.stopPropagation();
                touchStartX = e.touches[0].clientX;
                isSwiping = true;
            }, { passive: true });

            imageWrapper.addEventListener('touchmove', (e) => {
                if (!isSwiping) return;
                e.stopPropagation();
            }, { passive: true });

            imageWrapper.addEventListener('touchend', (e) => {
                if (!isSwiping) return;

                e.stopPropagation();
                touchEndX = e.changedTouches[0].clientX;

                const swipeDistance = touchStartX - touchEndX;
                const minSwipeDistance = 50;

                if (Math.abs(swipeDistance) > minSwipeDistance) {
                    const currentIndex = getCurrentIndex();
                    let newIndex;

                    if (swipeDistance > 0) {
                        newIndex = (currentIndex + 1) % imagesCount;
                    } else {
                        newIndex = currentIndex === 0 ? imagesCount - 1 : currentIndex - 1;
                    }

                    setCurrentIndex(newIndex);
                    this.showImageInstant(imageElements, indicators, newIndex);
                }

                isSwiping = false;
            }, { passive: true });

            imageWrapper.addEventListener('touchcancel', () => {
                isSwiping = false;
            }, { passive: true });
        }

        showImageInstant(imageElements, indicators, imageIndex) {
            imageElements.forEach(img => {
                img.style.opacity = '0';
                img.classList.remove('active');
            });

            indicators.forEach(indicator => indicator.classList.remove('edsys-image-indicator--active'));

            const targetImage = imageElements.find(img =>
                parseInt(img.dataset.imageIndex) === imageIndex
            );

            if (targetImage) {
                targetImage.style.opacity = '1';
                targetImage.classList.add('active');

                const activeIndicator = Array.from(indicators).find(indicator =>
                    parseInt(indicator.dataset.imageIndex) === imageIndex
                );
                if (activeIndicator) {
                    activeIndicator.classList.add('edsys-image-indicator--active');
                }
            }
        }

        /* ==========================================================================
           Sidebar filters
           ========================================================================== */

        initSidebarFilters() {
            if (!this.elements.sidebarFilters) return;

            this.elements.mobileFilterBtns.forEach(btn => {
                btn.addEventListener('click', () => {
                    this.openMobileFilters();
                });
            });

            if (this.elements.filtersClose) {
                this.elements.filtersClose.addEventListener('click', () => {
                    this.closeMobileFilters();
                });
            }

            if (this.elements.filtersOverlay) {
                this.elements.filtersOverlay.addEventListener('click', () => {
                    this.closeMobileFilters();
                });
            }

            if (this.elements.filtersApply) {
                this.elements.filtersApply.addEventListener('click', () => {
                    this.applyFilters();
                });
            }

            if (this.elements.filtersReset) {
                this.elements.filtersReset.addEventListener('click', () => {
                    this.resetFilters();
                });
            }

            const checkboxes = this.elements.sidebarFilters.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', () => {
                    this.updateFiltersCounter();
                });
            });

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    this.closeMobileFilters();
                }
            });
        }

        openMobileFilters() {
            if (this.elements.sidebarFilters) {
                this.elements.sidebarFilters.classList.add('active');
            }
            if (this.elements.filtersOverlay) {
                this.elements.filtersOverlay.classList.add('active');
            }
            document.body.style.overflow = 'hidden';
        }

        closeMobileFilters() {
            if (this.elements.sidebarFilters) {
                this.elements.sidebarFilters.classList.remove('active');
            }
            if (this.elements.filtersOverlay) {
                this.elements.filtersOverlay.classList.remove('active');
            }
            document.body.style.overflow = '';
        }

        updateFiltersCounter() {
            const checkboxes = this.elements.sidebarFilters.querySelectorAll('input[type="checkbox"]:checked');
            this.activeFiltersCount = checkboxes.length;

            this.elements.filtersCounters.forEach(counter => {
                if (this.activeFiltersCount > 0) {
                    counter.textContent = this.activeFiltersCount;
                    counter.style.display = 'block';
                } else {
                    counter.style.display = 'none';
                }
            });
        }

        applyFilters() {
            const formData = this.getFilterData();
            const url = new URL(window.location);
            url.searchParams.set('page', '1');

            const inputFilters = formData.getAll('filter_input[]');
            const additionalFilters = formData.getAll('filter_additional[]');

            if (inputFilters.length > 0) {
                url.searchParams.set('filter_input', inputFilters.join(','));
            } else {
                url.searchParams.delete('filter_input');
            }

            if (additionalFilters.length > 0) {
                url.searchParams.set('filter_additional', additionalFilters.join(','));
            } else {
                url.searchParams.delete('filter_additional');
            }

            window.location.href = url.toString();
            this.closeMobileFilters();
        }

        resetFilters() {
            const checkboxes = this.elements.sidebarFilters.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
            });

            this.updateFiltersCounter();

            const url = new URL(window.location);
            url.searchParams.delete('filter_input');
            url.searchParams.delete('filter_additional');
            url.searchParams.set('page', '1');

            window.location.href = url.toString();
        }

        getFilterData() {
            const formData = new FormData();
            const inputFilters = [];
            const additionalFilters = [];

            const checkboxes = this.elements.sidebarFilters.querySelectorAll('input[type="checkbox"]:checked');
            checkboxes.forEach(checkbox => {
                if (checkbox.name === 'filter_input[]') {
                    inputFilters.push(checkbox.value);
                } else if (checkbox.name === 'filter_additional[]') {
                    additionalFilters.push(checkbox.value);
                }
            });

            if (inputFilters.length > 0) {
                inputFilters.forEach(value => {
                    formData.append('filter_input[]', value);
                });
            }

            if (additionalFilters.length > 0) {
                additionalFilters.forEach(value => {
                    formData.append('filter_additional[]', value);
                });
            }

            return formData;
        }

        /* ==========================================================================
           Sort
           ========================================================================== */

        initSort() {
            this.elements.sortSelects.forEach(select => {
                select.addEventListener('change', () => {
                    this.elements.sortSelects.forEach(otherSelect => {
                        if (otherSelect !== select) {
                            otherSelect.value = select.value;
                        }
                    });

                    const url = new URL(window.location);
                    url.searchParams.set('sort', select.value);
                    url.searchParams.set('page', '1');

                    window.location.href = url.toString();
                });
            });
        }

        /* ==========================================================================
           Quick actions - FIXED VERSION WITH COMPARE
           ========================================================================== */

        initQuickActions() {
            if (this.elements.productsGrid) {
                // Use event delegation for both favorites and compare buttons
                this.elements.productsGrid.addEventListener('click', (e) => {
                    // Handle compare button
                    const compareButton = e.target.closest('[data-compare-action="toggle"]');
                    if (compareButton) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleCompare(compareButton);
                        return;
                    }

                    // Handle favorite button
                    const favoriteButton = e.target.closest('[data-action="add-to-favorites"]');
                    if (favoriteButton) {
                        e.preventDefault();
                        e.stopPropagation();
                        this.toggleFavorite(favoriteButton);
                        return;
                    }
                });

                console.log('Quick actions initialized (favorites + compare)');
            }
        }

        /**
         * Toggle product in favorites
         * @param {HTMLElement} button - Favorite button element
         */
        toggleFavorite(button) {
            if (button.disabled) return;
            
            const productId = parseInt(button.dataset.productId);
            const icon = button.querySelector('i') || button; // Fallback if no icon
            const isActive = button.classList.contains('active');

            button.disabled = true;
            button.style.opacity = '0.6';

            if (!this.config.isAuthorized) {
                // Guest user - use localStorage
                try {
                    let favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
                    const index = favorites.indexOf(productId);
                    const willAdd = (index === -1);

                    if (willAdd) {
                        favorites.push(productId);
                        button.classList.add('active');
                        this.showNotification('Товар добавлен в избранное', 'success');
                    } else {
                        favorites.splice(index, 1);
                        button.classList.remove('active');
                        this.showNotification('Товар удалён из избранного', 'info');
                    }

                    localStorage.setItem('userFavorites', JSON.stringify(favorites));
                    this.updateFavoritesCounter(favorites.length);

                    document.dispatchEvent(new CustomEvent('edsys:favoriteToggled', {
                        detail: {
                            productId: productId,
                            inFavorites: willAdd,
                            count: favorites.length
                        }
                    }));

                } catch (e) {
                    console.error('localStorage error:', e);
                    this.showNotification('Ошибка при сохранении', 'error');
                }

                button.disabled = false;
                button.style.opacity = '1';
                return;
            }

            // Authorized user - use server
            const shouldAdd = !isActive;
            fetch('/local/ajax/favorites.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    action: 'toggle',
                    productId: productId,
                    add: shouldAdd
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    this.handleFavoriteSuccess(button, data, productId, shouldAdd);
                } else {
                    this.showNotification(data.message || 'Ошибка при обновлении избранного', 'error');
                }
            })
            .catch(error => {
                console.error('Favorites error:', error);
                this.handleFavoriteError(button, error);
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }

        handleFavoriteSuccess(button, response, productId, shouldAdd) {
            if (response && response.success) {
                button.classList.toggle('active');
                
                const newTitle = button.classList.contains('active') 
                    ? 'Удалить из избранного' 
                    : 'Добавить в избранное';
                button.setAttribute('title', newTitle);
                button.setAttribute('aria-label', newTitle);

                const message = response.message || (shouldAdd ? 'Товар добавлен в избранное' : 'Товар удален из избранного');
                this.showNotification(message, 'success');

                if (response.data && typeof response.data.count !== 'undefined') {
                    this.updateFavoritesCounter(response.data.count);
                }

                if (typeof window.wishlistProductIds !== 'undefined') {
                    if (button.classList.contains('active')) {
                        if (!window.wishlistProductIds.includes(productId)) {
                            window.wishlistProductIds.push(productId);
                        }
                    } else {
                        const index = window.wishlistProductIds.indexOf(productId);
                        if (index > -1) {
                            window.wishlistProductIds.splice(index, 1);
                        }
                    }
                }

                document.dispatchEvent(new CustomEvent('edsys:favoriteToggled', {
                    detail: {
                        productId: productId,
                        inFavorites: button.classList.contains('active'),
                        count: response.data?.count
                    }
                }));
            } else {
                console.error('Favorite toggle failed:', response);
                const errorMsg = response?.message || 'Не удалось обновить избранное';
                this.showNotification(errorMsg, 'error');
            }
        }

        handleFavoriteError(button, error) {
            console.error('Favorite toggle error details:', error);
            this.showNotification('Ошибка при обновлении избранного', 'error');
        }

        updateFavoritesCounter(count) {
            const counters = document.querySelectorAll('#favorites-counter, .favorites-counter, [data-favorites-count]');
            counters.forEach(counter => {
                counter.textContent = count || 0;
                if (count > 0) {
                    counter.style.display = 'inline-block';
                    counter.classList.add('active');
                } else {
                    counter.style.display = 'none';
                    counter.classList.remove('active');
                }
            });
        }

        /* ==========================================================================
           COMPARE FUNCTIONALITY - FIXED VERSION
           ========================================================================== */

        /**
         * Toggle product in comparison - FIXED
         * @param {HTMLElement} button - Compare button element
         */
        toggleCompare(button) {
            // Prevent multiple simultaneous requests
            if (this.isTogglingCompare) {
                console.log('Compare toggle already in progress, skipping...');
                return;
            }

            const productId = parseInt(button.dataset.productId);
            
            if (!productId || productId <= 0) {
                console.error('Invalid product ID for compare:', productId);
                this.showNotification('Ошибка: неверный ID товара', 'error');
                return;
            }

            // Check authorization
            if (!this.config.isAuthorized) {
                this.showNotification('Войдите в аккаунт для добавления к сравнению', 'warning');
                setTimeout(() => {
                    window.location.href = '/auth/?backurl=' + encodeURIComponent(window.location.pathname);
                }, 1500);
                return;
            }

            // Get current button state
            const isActive = button.classList.contains('active');

            // Set flag
            this.isTogglingCompare = true;

            // Disable button during request
            button.disabled = true;
            const originalOpacity = button.style.opacity;
            button.style.opacity = '0.6';
            button.style.pointerEvents = 'none';

            console.log('Toggling compare:', { productId, isActive, willAdd: !isActive });

            // Get session ID
            let sessid = '';
            
            if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
                sessid = BX.bitrix_sessid();
            }
            
            if (!sessid) {
                const metaSessid = document.querySelector('meta[name="bitrix-sessid"]');
                if (metaSessid) {
                    sessid = metaSessid.getAttribute('content');
                }
            }
            
            if (!sessid) {
                const inputSessid = document.querySelector('input[name="sessid"]');
                if (inputSessid) {
                    sessid = inputSessid.value;
                }
            }

            if (!sessid) {
                console.error('No sessid found');
                this.showNotification('Ошибка безопасности: не удалось получить токен сессии', 'error');
                button.disabled = false;
                button.style.opacity = originalOpacity || '1';
                button.style.pointerEvents = '';
                this.isTogglingCompare = false;
                return;
            }

            // Prepare form data
            const formData = new FormData();
            formData.append('productId', productId);
            formData.append('sessid', sessid);

            // Send AJAX request to toggle compare
            fetch('/local/ajax/compare/add.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Compare response:', data);
                
                if (data.success) {
                    // Update button state based on server response
                    this.updateCompareButtons(productId, data.data.inCompare);
                    
                    // Update counter
                    this.updateCompareCounter(data.data.compareCount);
                    
                    // Show notification
                    this.showNotification(data.message || (data.data.inCompare ? 'Товар добавлен к сравнению' : 'Товар удален из сравнения'), 'success');
                    
                    // Dispatch custom event
                    document.dispatchEvent(new CustomEvent('edsys:compareToggled', {
                        detail: {
                            productId: productId,
                            inCompare: data.data.inCompare,
                            count: data.data.compareCount
                        }
                    }));
                } else {
                    throw new Error(data.message || 'Неизвестная ошибка');
                }
            })
            .catch(error => {
                console.error('Error toggling compare:', error);
                this.showNotification('Произошла ошибка при добавлении в сравнение: ' + error.message, 'error');
            })
            .finally(() => {
                // Re-enable button with delay
                setTimeout(() => {
                    button.disabled = false;
                    button.style.opacity = originalOpacity || '1';
                    button.style.pointerEvents = '';
                    this.isTogglingCompare = false;
                }, 300);
            });
        }

        /**
         * Update all compare buttons for a specific product
         * @param {number} productId - Product ID
         * @param {boolean} inCompare - Whether product is in compare
         */
        updateCompareButtons(productId, inCompare) {
            const buttons = document.querySelectorAll(`[data-compare-action="toggle"][data-product-id="${productId}"]`);
            
            buttons.forEach(button => {
                if (inCompare) {
                    button.classList.add('active');
                    button.setAttribute('aria-pressed', 'true');
                } else {
                    button.classList.remove('active');
                    button.setAttribute('aria-pressed', 'false');
                }
                
                const newTitle = inCompare ? 'Удалить из сравнения' : 'Добавить к сравнению';
                button.setAttribute('title', newTitle);
                button.setAttribute('aria-label', newTitle);
            });

            console.log(`Updated compare buttons for product ${productId}: ${inCompare ? 'in compare' : 'not in compare'}`);
        }

        /**
         * Update compare counter in UI
         * @param {number} count - Number of items in compare
         */
        updateCompareCounter(count) {
            const counters = document.querySelectorAll('[data-compare-count]');
            const floatingButton = this.elements.compareFloating;

            counters.forEach(counter => {
                counter.textContent = count || 0;
            });

            if (floatingButton) {
                if (count > 0) {
                    floatingButton.classList.add('visible');
                } else {
                    floatingButton.classList.remove('visible');
                }
            }

            console.log('Compare counter updated:', count);
        }

        /**
         * Initialize compare state on page load
         */
        initCompareState() {
            fetch('/local/ajax/compare/get_status.php', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update counter
                    this.updateCompareCounter(data.compareCount);
                    
                    // Update buttons for products already in compare
                    if (data.compareList && data.compareList.length > 0) {
                        data.compareList.forEach(productId => {
                            this.updateCompareButtons(productId, true);
                        });
                    }
                    
                    console.log('Compare state initialized:', {
                        count: data.compareCount,
                        products: data.compareList
                    });
                }
            })
            .catch(error => {
                console.error('Error getting compare status:', error);
            });
        }

        /* ==========================================================================
           Notification system
           ========================================================================== */

        showNotification(message, type = 'info') {
            if (typeof BX !== 'undefined' && BX.UI && BX.UI.Notification) {
                BX.UI.Notification.Center.notify({
                    content: message,
                    position: 'top-right',
                    autoHideDelay: 3000,
                });
            } else {
                this.showCustomNotification(message, type);
            }
        }

        showCustomNotification(message, type) {
            let container = document.getElementById('edsys-notifications');
            if (!container) {
                container = document.createElement('div');
                container.id = 'edsys-notifications';
                container.style.cssText = `
                    position: fixed;
                    top: 1rem;
                    right: 1rem;
                    z-index: 10000;
                    display: flex;
                    flex-direction: column;
                    gap: 0.5rem;
                    max-width: 400px;
                `;
                document.body.appendChild(container);
            }

            const notification = document.createElement('div');
            notification.className = `edsys-notification edsys-notification--${type}`;
            
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
            `;

            notification.innerHTML = `
                <i class="ph ph-thin ${this.getNotificationIcon(type)}" style="font-size: 1.5rem; color: ${colors[type]}; flex-shrink: 0;"></i>
                <span style="flex: 1;">${message}</span>
                <button type="button" class="edsys-notification-close" style="background: none; border: none; cursor: pointer; color: #999; font-size: 1.25rem; padding: 0; line-height: 1; flex-shrink: 0;" aria-label="Закрыть уведомление">
                    <i class="ph ph-thin ph-x"></i>
                </button>
            `;

            const closeBtn = notification.querySelector('.edsys-notification-close');
            closeBtn.addEventListener('click', () => {
                notification.style.animation = 'slideOutRight 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            });

            container.appendChild(notification);

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

            if (!document.getElementById('edsys-notification-styles')) {
                const style = document.createElement('style');
                style.id = 'edsys-notification-styles';
                style.textContent = `
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
                    @media (max-width: 640px) {
                        #edsys-notifications {
                            left: 1rem !important;
                            right: 1rem !important;
                            max-width: none !important;
                        }
                        .edsys-notification {
                            min-width: 0 !important;
                        }
                    }
                `;
                document.head.appendChild(style);
            }
        }

        getNotificationIcon(type) {
            const icons = {
                success: 'ph-check-circle',
                error: 'ph-x-circle',
                warning: 'ph-warning-circle',
                info: 'ph-info'
            };
            return icons[type] || icons.info;
        }

        /* ==========================================================================
           Wishlist state initialization
           ========================================================================== */

        initWishlistState() {
            if (window.wishlistProductIds && Array.isArray(window.wishlistProductIds)) {
                const buttons = document.querySelectorAll('[data-action="add-to-favorites"]');
                buttons.forEach(button => {
                    const productId = parseInt(button.dataset.productId, 10);
                    if (window.wishlistProductIds.includes(productId)) {
                        button.classList.add('active');
                        const newTitle = 'Удалить из избранного';
                        button.setAttribute('title', newTitle);
                        button.setAttribute('aria-label', newTitle);
                    }
                });
                console.log('Wishlist state initialized for', buttons.length, 'buttons');
            }
        }

        /* ==========================================================================
           Keyboard navigation
           ========================================================================== */

        initKeyboardNavigation() {
            if (this.elements.productsGrid) {
                this.elements.productsGrid.removeEventListener('keydown', this.keyboardHandler);

                this.keyboardHandler = (e) => {
                    const card = e.target.closest('.edsys-product-card');
                    if (!card) return;

                    const currentCards = document.querySelectorAll('.edsys-product-card');
                    const currentIndex = Array.from(currentCards).indexOf(card);

                    switch (e.key) {
                        case 'Enter':
                            const link = card.querySelector('.edsys-product-card__title-link');
                            if (link) {
                                link.click();
                            }
                            break;

                        case 'ArrowRight':
                            e.preventDefault();
                            this.focusNextCard(currentIndex, currentCards);
                            break;

                        case 'ArrowLeft':
                            e.preventDefault();
                            this.focusPrevCard(currentIndex, currentCards);
                            break;

                        case 'ArrowDown':
                            e.preventDefault();
                            this.focusCardBelow(currentIndex, currentCards);
                            break;

                        case 'ArrowUp':
                            e.preventDefault();
                            this.focusCardAbove(currentIndex, currentCards);
                            break;
                    }
                };

                this.elements.productsGrid.addEventListener('keydown', this.keyboardHandler);

                const productCards = document.querySelectorAll('.edsys-product-card');
                productCards.forEach(card => {
                    if (!card.hasAttribute('tabindex')) {
                        card.setAttribute('tabindex', '0');
                    }
                });
            }
        }

        focusNextCard(currentIndex, cards) {
            const nextIndex = (currentIndex + 1) % cards.length;
            cards[nextIndex].focus();
        }

        focusPrevCard(currentIndex, cards) {
            const prevIndex = currentIndex === 0 ? cards.length - 1 : currentIndex - 1;
            cards[prevIndex].focus();
        }

        focusCardBelow(currentIndex, cards) {
            const columnsCount = this.getColumnsCount();
            const nextIndex = currentIndex + columnsCount;

            if (nextIndex < cards.length) {
                cards[nextIndex].focus();
            }
        }

        focusCardAbove(currentIndex, cards) {
            const columnsCount = this.getColumnsCount();
            const prevIndex = currentIndex - columnsCount;

            if (prevIndex >= 0) {
                cards[prevIndex].focus();
            }
        }

        getColumnsCount() {
            if (!this.elements.productsGrid) return 3;

            const gridStyle = window.getComputedStyle(this.elements.productsGrid);
            const columns = gridStyle.gridTemplateColumns.split(' ');
            return columns.length;
        }

        /* ==========================================================================
           Window resize handling
           ========================================================================== */

        handleResize() {
            if (window.innerWidth > 1024) {
                this.closeMobileFilters();
            }

            this.initImageGallery();
        }
    }

    window.EdsCatalogSection = EdsCatalogSection;

}

// Initialize on DOM ready
document.addEventListener('DOMContentLoaded', () => {
    if (typeof window.catalogInstance === 'undefined') {
        window.catalogInstance = new window.EdsCatalogSection();

        window.addEventListener('resize', () => {
            window.catalogInstance.handleResize();
        });
    }
});