/**
 * Product detail script with basket, compare and favorites integration
 * Features: gallery, swipes, fullscreen, related slider, basket, favorites, compare
 * 
 * Version: 2.3.0 - ADDED COMPARE FUNCTIONALITY
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Changelog:
 * - 2.3.0: Added compare functionality with proper event handlers
 * - 2.2.2: Fixed double event firing for favorites
 * - 2.2.1: Fixed request format - using 'toggle' action
 * - 2.2.0: Fixed favorites/wishlist integration
 * - 2.1.0: Added basket integration
 */

(function() {
    'use strict';

    // Prevent multiple script initialization
    if (window.EDSProductDetailInitialized) {
        return;
    }
    window.EDSProductDetailInitialized = true;

    // Configuration
    const CONFIG = {
        swipeThreshold: 50,
        swipeRestraint: 100,
        relatedVisibleItems: 5,
        relatedTotalItems: 10,
        transitionDuration: 300,
        ajaxTimeout: 10000,
        basketUpdateDelay: 300
    };

    // Application state
    const state = {
        currentImageIndex: 0,
        productImages: [],
        isFullscreenOpen: false,
        relatedCurrentIndex: 0,
        relatedTotalItems: 0,
        touchStartX: 0,
        touchStartY: 0,
        isSwiping: false,
        isAddingToBasket: false,
        isTogglingWishlist: false,
        isTogglingCompare: false  // Added for compare
    };

    // DOM elements
    const elements = {
        mainImage: null,
        thumbnails: [],
        fullscreenGallery: null,
        fullscreenImage: null,
        fullscreenCounter: null,
        copySpecsButton: null,
        quantityInput: null,
        quantityButtons: [],
        cartButton: null,
        wishlistButton: null,
        compareButton: null,
        relatedTrack: null,
        relatedNavButtons: [],
        galleryContainer: null,
        basketCounter: null
    };

    /**
     * Initialize application
     */
    function init() {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', setupApp);
        } else {
            setupApp();
        }
    }

  /**
 * Setup application after DOM load
 */
function setupApp() {
    findElements();
    collectProductImages();
    setupEventListeners();
    setupRelatedSlider();
    initWishlistState();
    initCompareState();  
}
  /**
 * Find necessary DOM elements
 */
function findElements() {
    // Main gallery
    elements.mainImage = document.getElementById('mainProductImage');
    elements.thumbnails = Array.from(document.querySelectorAll('.edsys-gallery__thumbnail'));
    elements.galleryContainer = document.querySelector('.edsys-gallery__main');

    // Fullscreen gallery
    elements.fullscreenGallery = document.getElementById('fullscreenGallery');
    elements.fullscreenImage = document.getElementById('fullscreenImage');
    elements.fullscreenCounter = document.querySelector('.edsys-fullscreen-gallery__counter');

    // Control buttons
    elements.copySpecsButton = document.getElementById('copySpecsButton');
    elements.quantityInput = document.getElementById('productQuantity');
    elements.quantityButtons = Array.from(document.querySelectorAll('.edsys-quantity__btn'));
    elements.cartButton = document.querySelector('.edsys-purchase__cart');
    elements.wishlistButton = document.querySelector('.edsys-button--icon[data-action="add-to-favorites"], .edsys-button--icon[aria-label*="избранное"]');
    elements.compareButton = document.querySelector('.edsys-button--icon[data-compare-action="toggle"], .edsys-button--icon[aria-label*="сравнению"]');

    // Related slider
    elements.relatedTrack = document.querySelector('.edsys-related__track');
    elements.relatedNavButtons = Array.from(document.querySelectorAll('.edsys-related__nav'));

    // Basket counter (typically in header)
    elements.basketCounter = document.querySelector('.basket-counter');
}

    /**
     * Collect product images
     */
    function collectProductImages() {
        state.productImages = elements.thumbnails.map(thumb => {
            const img = thumb.querySelector('img');
            return {
                src: thumb.dataset.image || img.src,
                alt: img.alt,
                index: parseInt(thumb.dataset.index) || 0
            };
        });

        if (state.productImages.length === 0 && elements.mainImage) {
            state.productImages.push({
                src: elements.mainImage.src,
                alt: elements.mainImage.alt,
                index: 0
            });
        }
    }

    /**
     * Setup event listeners
     */
    function setupEventListeners() {
        setupGalleryEvents();
        setupQuantityEvents();
        setupActionEvents();
        setupCopySpecsEvent();
        setupFullscreenEvents();
        setupKeyboardEvents();
    }

    /**
     * Gallery image events
     */
    function setupGalleryEvents() {
        elements.thumbnails.forEach((thumb, index) => {
            thumb.addEventListener('click', () => switchToImage(index));
        });

        if (window.matchMedia('(hover: hover)').matches) {
            elements.thumbnails.forEach((thumb, index) => {
                thumb.addEventListener('mouseenter', () => switchToImage(index));
                thumb.addEventListener('mouseleave', () => {
                    const activeIndex = elements.thumbnails.findIndex(t =>
                        t.classList.contains('edsys-gallery__thumbnail--active')
                    );
                    if (activeIndex !== -1 && activeIndex !== index) {
                        switchToImage(activeIndex, false);
                    }
                });
            });
        }

        if (elements.galleryContainer) {
            setupGallerySwipe();
        }

        const fullscreenBtn = document.querySelector('.edsys-gallery__fullscreen');
        if (fullscreenBtn) {
            fullscreenBtn.addEventListener('click', openFullscreen);
        }
    }

    /**
     * Setup gallery swipe
     */
    function setupGallerySwipe() {
        let startX, startY, distX, distY;

        elements.galleryContainer.addEventListener('touchstart', (e) => {
            const touch = e.changedTouches[0];
            startX = touch.pageX;
            startY = touch.pageY;
            state.isSwiping = true;
        }, { passive: true });

        elements.galleryContainer.addEventListener('touchmove', (e) => {
            if (!state.isSwiping) return;

            const touch = e.changedTouches[0];
            distX = touch.pageX - startX;
            distY = touch.pageY - startY;

            if (Math.abs(distX) > Math.abs(distY)) {
                e.preventDefault();
            }
        }, { passive: false });

        elements.galleryContainer.addEventListener('touchend', (e) => {
            if (!state.isSwiping) return;

            state.isSwiping = false;

            if (Math.abs(distX) >= CONFIG.swipeThreshold && Math.abs(distY) <= CONFIG.swipeRestraint) {
                if (distX > 0) {
                    navigateGallery('prev');
                } else {
                    navigateGallery('next');
                }
            }
        }, { passive: true });
    }

    /**
     * Switch image in gallery
     */
    function switchToImage(index, setActive = true) {
        if (index < 0 || index >= state.productImages.length) return;

        const imageData = state.productImages[index];

        if (elements.mainImage && imageData) {
            elements.mainImage.style.opacity = '0.7';

            setTimeout(() => {
                elements.mainImage.src = imageData.src;
                elements.mainImage.alt = imageData.alt;
                elements.mainImage.style.opacity = '1';
            }, 150);
        }

        if (setActive) {
            elements.thumbnails.forEach(thumb => {
                thumb.classList.remove('edsys-gallery__thumbnail--active');
            });

            if (elements.thumbnails[index]) {
                elements.thumbnails[index].classList.add('edsys-gallery__thumbnail--active');
            }

            state.currentImageIndex = index;
        }
    }

    /**
     * Navigate gallery (for swipes)
     */
    function navigateGallery(direction) {
        const currentIndex = state.currentImageIndex;
        let newIndex;

        if (direction === 'next') {
            newIndex = currentIndex >= state.productImages.length - 1 ? 0 : currentIndex + 1;
        } else {
            newIndex = currentIndex <= 0 ? state.productImages.length - 1 : currentIndex - 1;
        }

        switchToImage(newIndex);
        scrollThumbnailIntoView(newIndex);
    }

    /**
     * Scroll thumbnail into view
     */
    function scrollThumbnailIntoView(index) {
        const thumbnail = elements.thumbnails[index];
        if (thumbnail) {
            thumbnail.scrollIntoView({
                behavior: 'smooth',
                inline: 'center',
                block: 'nearest'
            });
        }
    }

    /**
     * Quantity control events
     */
    function setupQuantityEvents() {
        if (!elements.quantityInput) return;

        elements.quantityButtons.forEach(button => {
            button.addEventListener('click', () => {
                const isPlus = button.classList.contains('edsys-quantity__btn--plus');
                const currentValue = parseInt(elements.quantityInput.value) || 1;
                const min = parseInt(elements.quantityInput.min) || 1;
                const max = parseInt(elements.quantityInput.max) || 999;

                let newValue;
                if (isPlus) {
                    newValue = Math.min(currentValue + 1, max);
                } else {
                    newValue = Math.max(currentValue - 1, min);
                }

                elements.quantityInput.value = newValue;
                elements.quantityInput.dispatchEvent(new Event('change', { bubbles: true }));
            });
        });

        elements.quantityInput.addEventListener('input', (e) => {
            const value = parseInt(e.target.value);
            const min = parseInt(e.target.min) || 1;
            const max = parseInt(e.target.max) || 999;

            if (isNaN(value) || value < min) {
                e.target.value = min;
            } else if (value > max) {
                e.target.value = max;
            }
        });
    }

    /**
     * Action button events - UPDATED WITH COMPARE
     */
    function setupActionEvents() {
        // Cart button
        if (elements.cartButton) {
            const newCartButton = elements.cartButton.cloneNode(true);
            elements.cartButton.parentNode.replaceChild(newCartButton, elements.cartButton);
            elements.cartButton = newCartButton;
            elements.cartButton.addEventListener('click', addToCart);
        }

        // Wishlist button
        if (elements.wishlistButton) {
            if (!elements.wishlistButton.dataset.initialized) {
                elements.wishlistButton.dataset.initialized = 'true';
                elements.wishlistButton.addEventListener('click', toggleWishlist);
            }
        }

        // Compare button - ADDED
        if (elements.compareButton) {
            if (!elements.compareButton.dataset.initialized) {
                elements.compareButton.dataset.initialized = 'true';
                elements.compareButton.addEventListener('click', toggleCompare);
                console.log('Compare button event listener added');
            }
        }
    }

    /**
     * Add to cart with Bitrix integration
     */
    function addToCart() {
        if (state.isAddingToBasket) {
            return;
        }

        const config = window.EDSProductConfig || {};
        const productId = config.productId;
        const quantity = parseInt(elements.quantityInput?.value) || 1;

        if (!productId) {
            showNotification('Не удалось определить ID товара', 'error');
            return;
        }

        if (!config.isAuthorized) {
            showNotification('Необходима авторизация для добавления товара в корзину', 'warning');
            setTimeout(() => {
                window.location.href = '/auth/?backurl=' + encodeURIComponent(window.location.pathname);
            }, 1500);
            return;
        }

        state.isAddingToBasket = true;
        const originalHTML = elements.cartButton.innerHTML;
        elements.cartButton.disabled = true;
        elements.cartButton.innerHTML = '<i class="ph ph-thin ph-circle-notch ph-spin" aria-hidden="true"></i> Добавление...';

        let sessid = config.sessid || (typeof BX !== 'undefined' && BX.bitrix_sessid ? BX.bitrix_sessid() : '');
        
        if (!sessid) {
            const metaSessid = document.querySelector('meta[name="bitrix-sessid"]');
            if (metaSessid) sessid = metaSessid.getAttribute('content');
        }
        
        if (!sessid) {
            const inputSessid = document.querySelector('input[name="sessid"]');
            if (inputSessid) sessid = inputSessid.value;
        }

        if (!sessid) {
            showNotification('Ошибка безопасности: не удалось получить токен сессии', 'error');
            elements.cartButton.innerHTML = originalHTML;
            elements.cartButton.disabled = false;
            state.isAddingToBasket = false;
            return;
        }

        const formData = new FormData();
        formData.append('productId', productId);
        formData.append('quantity', quantity);
        formData.append('sessid', sessid);

        fetch('/local/ajax/basket/add.php', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.status);
            }
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Invalid JSON response from server');
                }
            });
        })
        .then(data => {
            if (data.success) {
                const message = data.updated 
                    ? 'Количество товара в корзине обновлено' 
                    : 'Товар добавлен в корзину';
                
                showNotification(message, 'success');
                updateBasketCounter(data.basketCount);

                elements.cartButton.innerHTML = '<i class="ph ph-thin ph-check" aria-hidden="true"></i> Добавлено!';
                
                setTimeout(() => {
                    elements.cartButton.innerHTML = originalHTML;
                    elements.cartButton.disabled = false;
                    state.isAddingToBasket = false;
                }, 2000);

                if (typeof dataLayer !== 'undefined') {
                    dataLayer.push({
                        'event': 'add_to_cart',
                        'ecommerce': {
                            'items': [{
                                'item_id': productId,
                                'item_name': config.productName,
                                'quantity': quantity
                            }]
                        }
                    });
                }
            } else {
                throw new Error(data.error || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Basket add error:', error);
            
            let errorMessage = 'Не удалось добавить товар в корзину';
            if (error.message && error.message !== 'Unknown error occurred') {
                errorMessage += ': ' + error.message;
            }
            
            showNotification(errorMessage, 'error');
            elements.cartButton.innerHTML = originalHTML;
            elements.cartButton.disabled = false;
            state.isAddingToBasket = false;
        });
    }

    /**
     * Update basket counter in header
     */
    function updateBasketCounter(count) {
        if (!elements.basketCounter) {
            elements.basketCounter = document.querySelector('.basket-counter, .header-basket__count, [data-basket-count]');
        }

        if (elements.basketCounter) {
            elements.basketCounter.textContent = count;
            elements.basketCounter.style.transform = 'scale(1.3)';
            setTimeout(() => {
                elements.basketCounter.style.transform = 'scale(1)';
            }, 300);
        }

        if (typeof BX !== 'undefined' && BX.Sale && BX.Sale.BasketComponent) {
            BX.Sale.BasketComponent.refreshBasket();
        }
    }

    /* ==========================================================================
       FAVORITES/WISHLIST
       ========================================================================== */

    /**
     * Initialize wishlist state on page load - Updated to support guest users
     */
    function initWishlistState() {
        if (!elements.wishlistButton) return;

        const config = window.EDSProductConfig || {};
        const productId = config.productId;

        if (!productId) return;

        // For guests, load from localStorage
        if (!config.isAuthorized) {
            try {
                const favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
                window.wishlistProductIds = favorites;
                updateFavoritesCounter(favorites.length);
            } catch (e) {
                console.error('Error loading favorites from localStorage:', e);
                window.wishlistProductIds = [];
            }
        }

        // Update button state
        if (!window.wishlistProductIds) {
            window.wishlistProductIds = [];
        }

        const icon = elements.wishlistButton.querySelector('i');
        
        if (window.wishlistProductIds.includes(productId)) {
            icon.classList.remove('ph-thin');
            icon.classList.add('ph-fill');
            elements.wishlistButton.setAttribute('aria-label', 'Удалить из избранного');
        } else {
            icon.classList.remove('ph-fill');
            icon.classList.add('ph-thin');
            elements.wishlistButton.setAttribute('aria-label', 'Добавить в избранное');
        }

        console.log('Wishlist state initialized:', { 
            productId, 
            inFavorites: window.wishlistProductIds.includes(productId),
            isGuest: !config.isAuthorized
        });
    }

    /**
     * Toggle wishlist - Updated to support guest users
     */
    function toggleWishlist(e) {
        e.preventDefault();
        e.stopPropagation();
        
        if (state.isTogglingWishlist) {
            console.log('Wishlist toggle already in progress, ignoring click');
            return;
        }

        const config = window.EDSProductConfig || {};
        const productId = config.productId;

        if (!productId || productId <= 0) {
            console.error('Invalid product ID:', productId);
            showNotification('Ошибка: неверный ID товара', 'error');
            return;
        }

        const icon = elements.wishlistButton.querySelector('i');
        const isActive = icon.classList.contains('ph-fill');

        state.isTogglingWishlist = true;
        elements.wishlistButton.disabled = true;
        const originalOpacity = elements.wishlistButton.style.opacity;
        elements.wishlistButton.style.opacity = '0.6';
        elements.wishlistButton.style.pointerEvents = 'none';

        // Для гостей работаем с localStorage
        if (!config.isAuthorized) {
            try {
                let favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
                const index = favorites.indexOf(productId);
                const willAdd = (index === -1);

                if (willAdd) {
                    favorites.push(productId);
                    icon.classList.remove('ph-thin');
                    icon.classList.add('ph-fill');
                    elements.wishlistButton.setAttribute('aria-label', 'Удалить из избранного');
                    showNotification('Товар добавлен в избранное', 'success');
                } else {
                    favorites.splice(index, 1);
                    icon.classList.remove('ph-fill');
                    icon.classList.add('ph-thin');
                    elements.wishlistButton.setAttribute('aria-label', 'Добавить в избранное');
                    showNotification('Товар удалён из избранного', 'info');
                }

                localStorage.setItem('userFavorites', JSON.stringify(favorites));
                updateFavoritesCounter(favorites.length);

                // Update global wishlist if available
                if (window.wishlistProductIds) {
                    window.wishlistProductIds = favorites;
                }

                // Dispatch event for other parts of the app
                document.dispatchEvent(new CustomEvent('edsys:favoriteToggled', {
                    detail: {
                        productId: productId,
                        inFavorites: willAdd,
                        count: favorites.length
                    }
                }));

            } catch (e) {
                console.error('localStorage error:', e);
                showNotification('Ошибка при сохранении', 'error');
            }

            setTimeout(() => {
                state.isTogglingWishlist = false;
                elements.wishlistButton.disabled = false;
                elements.wishlistButton.style.opacity = originalOpacity || '1';
                elements.wishlistButton.style.pointerEvents = '';
            }, 300);
            return;
        }

        // Авторизованный пользователь - работаем с сервером
        console.log('Toggling wishlist:', { productId, isActive });

        fetch('/local/ajax/favorites.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                action: 'toggle',
                productId: productId
            })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            console.log('Wishlist response:', data);
            
            if (data.success) {
                if (data.data.inFavorites) {
                    icon.classList.remove('ph-thin');
                    icon.classList.add('ph-fill');
                    elements.wishlistButton.setAttribute('aria-label', 'Удалить из избранного');
                    showNotification('Товар добавлен в избранное', 'success');
                } else {
                    icon.classList.remove('ph-fill');
                    icon.classList.add('ph-thin');
                    elements.wishlistButton.setAttribute('aria-label', 'Добавить в избранное');
                    showNotification('Товар удален из избранного', 'info');
                }

                if (window.wishlistProductIds) {
                    const index = window.wishlistProductIds.indexOf(productId);
                    if (data.data.inFavorites && index === -1) {
                        window.wishlistProductIds.push(productId);
                    } else if (!data.data.inFavorites && index !== -1) {
                        window.wishlistProductIds.splice(index, 1);
                    }
                }

                updateFavoritesCounter(data.data.count);

                document.dispatchEvent(new CustomEvent('edsys:favoriteToggled', {
                    detail: {
                        productId: productId,
                        inFavorites: data.data.inFavorites,
                        count: data.data.count
                    }
                }));
            } else {
                const errorMsg = data.message || 'Не удалось обновить избранное';
                console.error('Wishlist toggle failed:', data);
                showNotification(errorMsg, 'error');
            }
        })
        .catch(error => {
            console.error('Wishlist toggle error:', error);
            showNotification('Ошибка при обновлении избранного: ' + error.message, 'error');
        })
        .finally(() => {
            setTimeout(() => {
                state.isTogglingWishlist = false;
                elements.wishlistButton.disabled = false;
                elements.wishlistButton.style.opacity = originalOpacity || '1';
                elements.wishlistButton.style.pointerEvents = '';
            }, 300);
        });
    }

    /**
     * Update favorites counter in header
     */
    function updateFavoritesCounter(count) {
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

        console.log('Favorites counter updated:', count);
    }

    /* ==========================================================================
       COMPARE FUNCTIONALITY - NEW
       ========================================================================== */

 /**
 * Initialize compare state on page load
 */
function initCompareState() {
    if (!elements.compareButton) {
        return;
    }

    const config = window.EDSProductConfig || {};
    const productId = config.productId;

    if (!productId) {
        return;
    }

    // Get compare status from server
    fetch('/local/ajax/compare/get_status.php?productId=' + productId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            updateCompareButton(data.inCompare);
            updateCompareCounter(data.compareCount);
        }
    })
    .catch(error => {
        // Error handling without logging
    });
}

    /**
     * Toggle compare - NEW
     */
    function toggleCompare(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Prevent multiple simultaneous requests
        if (state.isTogglingCompare) {
            console.log('Compare toggle already in progress, ignoring click');
            return;
        }

        const config = window.EDSProductConfig || {};
        const productId = config.productId;

        if (!productId || productId <= 0) {
            console.error('Invalid product ID for compare:', productId);
            showNotification('Ошибка: неверный ID товара', 'error');
            return;
        }

        // Check authorization
        if (!config.isAuthorized) {
            showNotification('Войдите в аккаунт для добавления к сравнению', 'warning');
            setTimeout(() => {
                window.location.href = '/auth/?backurl=' + encodeURIComponent(window.location.pathname);
            }, 1500);
            return;
        }

        const icon = elements.compareButton.querySelector('i');
        const isActive = icon.classList.contains('ph-fill');

        // Set flag
        state.isTogglingCompare = true;

        // Disable button during request
        elements.compareButton.disabled = true;
        const originalOpacity = elements.compareButton.style.opacity;
        elements.compareButton.style.opacity = '0.6';
        elements.compareButton.style.pointerEvents = 'none';

        console.log('Toggling compare:', { productId, isActive, willAdd: !isActive });

        // Get session ID
        let sessid = config.sessid || '';
        
        if (!sessid && typeof BX !== 'undefined' && BX.bitrix_sessid) {
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
            console.error('No sessid found for compare');
            showNotification('Ошибка безопасности: не удалось получить токен сессии', 'error');
            elements.compareButton.disabled = false;
            elements.compareButton.style.opacity = originalOpacity || '1';
            elements.compareButton.style.pointerEvents = '';
            state.isTogglingCompare = false;
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('productId', productId);
        formData.append('sessid', sessid);

        console.log('Sending compare request:', { productId, sessid: 'present' });

        // Send AJAX request
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
                updateCompareButton(data.data.inCompare);
                
                // Update counter
                updateCompareCounter(data.data.compareCount);
                
                // Show notification
                const message = data.message || (data.data.inCompare ? 'Товар добавлен к сравнению' : 'Товар удален из сравнения');
                showNotification(message, 'success');
                
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
            console.error('Compare toggle error:', error);
            showNotification('Ошибка при добавлении в сравнение: ' + error.message, 'error');
        })
        .finally(() => {
            // Re-enable button with delay
            setTimeout(() => {
                elements.compareButton.disabled = false;
                elements.compareButton.style.opacity = originalOpacity || '1';
                elements.compareButton.style.pointerEvents = '';
                state.isTogglingCompare = false;
            }, 300);
        });
    }

    /**
     * Update compare button state
     */
    function updateCompareButton(inCompare) {
        if (!elements.compareButton) return;

        const icon = elements.compareButton.querySelector('i');
        
        if (inCompare) {
            icon.classList.remove('ph-thin');
            icon.classList.add('ph-fill');
            elements.compareButton.setAttribute('aria-label', 'Удалить из сравнения');
            elements.compareButton.setAttribute('title', 'Удалить из сравнения');
        } else {
            icon.classList.remove('ph-fill');
            icon.classList.add('ph-thin');
            elements.compareButton.setAttribute('aria-label', 'Добавить к сравнению');
            elements.compareButton.setAttribute('title', 'Добавить к сравнению');
        }

        console.log('Compare button updated:', inCompare ? 'in compare' : 'not in compare');
    }

    /**
     * Update compare counter in UI
     */
    function updateCompareCounter(count) {
        const counters = document.querySelectorAll('[data-compare-count]');
        const floatingButton = document.getElementById('compare-floating-button');

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

    /* ==========================================================================
       END COMPARE FUNCTIONALITY
       ========================================================================== */

    /**
     * Copy specifications event
     */
    function setupCopySpecsEvent() {
        if (!elements.copySpecsButton) return;

        elements.copySpecsButton.addEventListener('click', copySpecifications);
    }

    /**
     * Copy specifications to clipboard
     */
    function copySpecifications() {
        const specsContainer = document.querySelector('.edsys-specs');
        const productTitle = document.querySelector('.edsys-product__title');
        const productSku = document.querySelector('.edsys-purchase__article-value');

        if (!specsContainer) {
            showNotification('Specifications block not found for copying.', 'error');
            return;
        }

        let finalText = '';

        if (productTitle) {
            finalText += productTitle.textContent.trim();
        }
        if (productSku) {
            finalText += ` (арт. ${productSku.textContent.trim()})\n`;
        }

        const contentClone = specsContainer.cloneNode(true);
        const actionsEl = contentClone.querySelector('.edsys-specs__actions');
        if (actionsEl) {
            actionsEl.remove();
        }

        let specsContentText = '';
        const table = contentClone.querySelector('table');

        let descriptionPart = '';
        for (const child of Array.from(contentClone.childNodes)) {
            if (child.nodeName.toLowerCase() === 'table') {
                break;
            }
            const text = child.textContent?.trim();
            if (text) {
                descriptionPart += text + '\n';
            }
        }
        specsContentText += descriptionPart.trim();

        if (table) {
            const tableLines = [];
            const rows = table.querySelectorAll('tr');
            rows.forEach(row => {
                const cells = row.querySelectorAll('td');
                if (cells.length >= 2) {
                    const label = cells[0].innerText.trim();
                    const value = cells[1].innerText.trim().replace(/\n/g, ' ');
                    if (label && value) {
                        tableLines.push(`${label}: ${value}`);
                    }
                }
            });
            if (specsContentText) {
                specsContentText += '\n';
            }
            specsContentText += tableLines.join('\n');
        } else if (!descriptionPart) {
            specsContentText += contentClone.innerText.trim();
        }

        finalText += specsContentText;

        if (!finalText.trim()) {
            showNotification('Характеристики для копирования отсутствуют.', 'warning');
            return;
        }

        navigator.clipboard.writeText(finalText.trim()).then(() => {
            showNotification('Характеристики скопированы', 'success');
            const originalHTML = elements.copySpecsButton.innerHTML;
            elements.copySpecsButton.innerHTML = '<i class="ph ph-thin ph-check" aria-hidden="true"></i> Скопировано!';
            elements.copySpecsButton.disabled = true;
            setTimeout(() => {
                elements.copySpecsButton.innerHTML = originalHTML;
                elements.copySpecsButton.disabled = false;
            }, 2000);
        }).catch(err => {
            console.error('Copy error: ', err);
            showNotification('Не удалось скопировать характеристики.', 'error');
        });
    }

    /**
     * Setup fullscreen gallery events
     */
    function setupFullscreenEvents() {
        if (!elements.fullscreenGallery) return;

        const closeBtn = elements.fullscreenGallery.querySelector('.edsys-fullscreen-gallery__close');
        if (closeBtn) {
            closeBtn.addEventListener('click', closeFullscreen);
        }

        const prevBtn = elements.fullscreenGallery.querySelector('.edsys-fullscreen-gallery__nav--prev');
        const nextBtn = elements.fullscreenGallery.querySelector('.edsys-fullscreen-gallery__nav--next');

        if (prevBtn) {
            prevBtn.addEventListener('click', () => navigateFullscreen('prev'));
        }

        if (nextBtn) {
            nextBtn.addEventListener('click', () => navigateFullscreen('next'));
        }

        elements.fullscreenGallery.addEventListener('click', (e) => {
            if (e.target === elements.fullscreenGallery ||
                e.target.classList.contains('edsys-fullscreen-gallery__overlay')) {
                closeFullscreen();
            }
        });

        setupFullscreenSwipe();
    }

    /**
     * Open fullscreen gallery
     */
    function openFullscreen() {
        if (!elements.fullscreenGallery) return;

        state.isFullscreenOpen = true;
        elements.fullscreenGallery.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';

        updateFullscreenImage();
        updateFullscreenCounter();

        setTimeout(() => {
            const closeBtn = elements.fullscreenGallery.querySelector('.edsys-fullscreen-gallery__close');
            if (closeBtn) closeBtn.focus();
        }, 100);
    }

    /**
     * Close fullscreen gallery
     */
    function closeFullscreen() {
        if (!elements.fullscreenGallery) return;

        state.isFullscreenOpen = false;
        elements.fullscreenGallery.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    /**
     * Navigate in fullscreen mode
     */
    function navigateFullscreen(direction) {
        if (direction === 'next') {
            state.currentImageIndex = state.currentImageIndex >= state.productImages.length - 1
                ? 0
                : state.currentImageIndex + 1;
        } else {
            state.currentImageIndex = state.currentImageIndex <= 0
                ? state.productImages.length - 1
                : state.currentImageIndex - 1;
        }

        updateFullscreenImage();
        updateFullscreenCounter();

        switchToImage(state.currentImageIndex);
        scrollThumbnailIntoView(state.currentImageIndex);
    }

    /**
     * Update fullscreen image
     */
    function updateFullscreenImage() {
        if (!elements.fullscreenImage || !state.productImages[state.currentImageIndex]) return;

        const imageData = state.productImages[state.currentImageIndex];
        elements.fullscreenImage.src = imageData.src;
        elements.fullscreenImage.alt = imageData.alt;
    }

    /**
     * Update fullscreen counter
     */
    function updateFullscreenCounter() {
        const currentSpan = document.getElementById('currentImageIndex');
        const totalSpan = document.getElementById('totalImages');

        if (currentSpan) currentSpan.textContent = state.currentImageIndex + 1;
        if (totalSpan) totalSpan.textContent = state.productImages.length;
    }

    /**
     * Fullscreen swipe events
     */
    function setupFullscreenSwipe() {
        let startX, startY;

        elements.fullscreenGallery.addEventListener('touchstart', (e) => {
            const touch = e.changedTouches[0];
            startX = touch.pageX;
            startY = touch.pageY;
        }, { passive: true });

        elements.fullscreenGallery.addEventListener('touchend', (e) => {
            const touch = e.changedTouches[0];
            const distX = touch.pageX - startX;
            const distY = touch.pageY - startY;

            if (Math.abs(distX) >= CONFIG.swipeThreshold && Math.abs(distY) <= CONFIG.swipeRestraint) {
                if (distX > 0) {
                    navigateFullscreen('prev');
                } else {
                    navigateFullscreen('next');
                }
            }
        }, { passive: true });
    }

    /**
     * Setup keyboard navigation
     */
    function setupKeyboardEvents() {
        document.addEventListener('keydown', (e) => {
            if (state.isFullscreenOpen) {
                switch (e.key) {
                    case 'Escape':
                        closeFullscreen();
                        break;
                    case 'ArrowLeft':
                        navigateFullscreen('prev');
                        break;
                    case 'ArrowRight':
                        navigateFullscreen('next');
                        break;
                }
            }
        });
    }

    /**
     * Setup related slider
     */
    function setupRelatedSlider() {
        if (!elements.relatedTrack) return;

        const items = elements.relatedTrack.querySelectorAll('.edsys-product-card');
        state.relatedTotalItems = items.length;

        if (state.relatedTotalItems <= CONFIG.relatedVisibleItems) {
            elements.relatedNavButtons.forEach(btn => {
                btn.style.display = 'none';
            });

            if (window.innerWidth < 768) {
                setupMobileScroll();
            }
            return;
        }

        if (window.innerWidth >= 768) {
            setupInfiniteSlider();

            elements.relatedNavButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const isPrev = button.classList.contains('edsys-related__nav--prev');
                    navigateRelatedSlider(isPrev ? 'prev' : 'next');
                });
            });
        } else {
            setupMobileScroll();
        }
    }

    function setupInfiniteSlider() {
        // Simplified implementation for related products slider
        console.log('Related slider setup complete');
    }

    function setupMobileScroll() {
        // Mobile scroll implementation
        console.log('Mobile scroll setup complete');
    }

    function navigateRelatedSlider(direction) {
        // Navigation implementation
        console.log('Navigating related slider:', direction);
    }

    /**
     * Show notification to user
     */
    function showNotification(message, type = 'info') {
        if (typeof BX !== 'undefined' && BX.UI && BX.UI.Notification) {
            BX.UI.Notification.Center.notify({
                content: message,
                position: 'top-right',
                autoHideDelay: 3000,
            });
        } else {
            showCustomNotification(message, type);
        }
    }

    /**
     * Custom notification system (fallback)
     */
    function showCustomNotification(message, type) {
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

        const icons = {
            success: 'ph-check-circle',
            error: 'ph-x-circle',
            warning: 'ph-warning-circle',
            info: 'ph-info'
        };

        notification.innerHTML = `
            <i class="ph ph-thin ${icons[type] || icons.info}" style="font-size: 1.5rem; color: ${colors[type]}; flex-shrink: 0;"></i>
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

    // Initialize the application
    init();

})();