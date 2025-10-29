/**
 * Favorites Initialization Script
 * Location: /local/templates/edsy_main/js/favorites-init.js
 * Version: 2.2.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Initialize favorites state and counter on page load
 * Поддержка избранного для всех пользователей (авторизованных и гостей)
 */

(function() {
    'use strict';

    /**
     * Initialize favorites on page load
     */
    function initFavorites() {
        // Check if user is authorized
        const isAuthorized = typeof BX !== 'undefined' && 
                           typeof BX.message === 'function' && 
                           BX.message('USER_IS_AUTHORIZED');

        if (isAuthorized) {
            // Для авторизованных загружаем с сервера
            fetchFavoritesFromServer();
        } else {
            // Для гостей загружаем из localStorage
            loadFavoritesFromLocalStorage();
        }
    }

    /**
     * Load favorites from localStorage for guests
     */
    function loadFavoritesFromLocalStorage() {
        try {
            const favorites = JSON.parse(localStorage.getItem('userFavorites')) || [];
            window.wishlistProductIds = favorites;
            updateFavoritesUI(favorites);
            updateFavoritesCounter(favorites.length);
        } catch (e) {
            console.error('Error loading favorites from localStorage:', e);
            window.wishlistProductIds = [];
            updateFavoritesCounter(0);
        }
    }

    /**
     * Fetch favorites list from server
     */
    function fetchFavoritesFromServer() {
        fetch('/local/ajax/get-favorites.php', {
            method: 'GET',
            credentials: 'same-origin',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.favorites) {
                // Store favorites globally
                window.wishlistProductIds = data.favorites;
                
                // Update UI
                updateFavoritesUI(data.favorites);
                updateFavoritesCounter(data.favorites.length);
                
            }
        })
        .catch(error => {
            console.error('Error fetching favorites:', error);
            // Initialize with empty array
            window.wishlistProductIds = [];
            updateFavoritesCounter(0);
        });
    }

    /**
     * Update favorites buttons UI based on favorites list
     * @param {Array} favorites - Array of favorite product IDs
     */
    function updateFavoritesUI(favorites) {
        if (!Array.isArray(favorites)) {
            favorites = [];
        }

        // Find all favorite buttons
        const favoriteButtons = document.querySelectorAll('[data-action="add-to-favorites"]');
        
        favoriteButtons.forEach(button => {
            const productId = parseInt(button.dataset.productId, 10);
            
            if (favorites.includes(productId)) {
                button.classList.add('active');
                button.setAttribute('title', 'Удалить из избранного');
                button.setAttribute('aria-label', 'Удалить из избранного');
            } else {
                button.classList.remove('active');
                button.setAttribute('title', 'Добавить в избранное');
                button.setAttribute('aria-label', 'Добавить в избранное');
            }
        });
    }

    /**
     * Update favorites counter in header
     * @param {number} count - Number of favorites
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
    }

    /**
     * Listen for favorite toggle events
     */
    function setupFavoriteEventListeners() {
        document.addEventListener('edsys:favoriteToggled', function(e) {
            const detail = e.detail;
            
            if (detail && typeof detail.count !== 'undefined') {
                updateFavoritesCounter(detail.count);
            }
            
            // Update wishlistProductIds
            if (detail && typeof detail.productId !== 'undefined' && typeof detail.inFavorites !== 'undefined') {
                if (!window.wishlistProductIds) {
                    window.wishlistProductIds = [];
                }
                
                const productId = detail.productId;
                const index = window.wishlistProductIds.indexOf(productId);
                
                if (detail.inFavorites && index === -1) {
                    window.wishlistProductIds.push(productId);
                } else if (!detail.inFavorites && index !== -1) {
                    window.wishlistProductIds.splice(index, 1);
                }
                
                // Для неавторизованных пользователей сохраняем в localStorage
                const isAuthorized = typeof BX !== 'undefined' && 
                                   typeof BX.message === 'function' && 
                                   BX.message('USER_IS_AUTHORIZED');
                
                if (!isAuthorized) {
                    try {
                        localStorage.setItem('userFavorites', JSON.stringify(window.wishlistProductIds));
                    } catch (e) {
                        console.error('Error saving to localStorage:', e);
                    }
                }
            }
        });
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            initFavorites();
            setupFavoriteEventListeners();
        });
    } else {
        initFavorites();
        setupFavoriteEventListeners();
    }

    // Re-initialize when new content is loaded via AJAX
    if (typeof BX !== 'undefined') {
        BX.addCustomEvent('onAjaxSuccess', function() {
            if (window.wishlistProductIds) {
                updateFavoritesUI(window.wishlistProductIds);
            }
        });
    }

    // Expose functions globally for catalog to use
    window.FavoritesManager = window.FavoritesManager || {};
    window.FavoritesManager.updateUI = updateFavoritesUI;
    window.FavoritesManager.updateCounter = updateFavoritesCounter;
})();