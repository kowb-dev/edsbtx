/**
 * Cart Counter Updater
 * Fetches cart item count and updates the header counters.
 * Version: 1.0.0
 * Date: 2025-10-25
 * Author: KW
 * URI: https://kowb.ru
 */
document.addEventListener('DOMContentLoaded', function() {
    function updateCartCounters() {
        fetch('/local/ajax/get_cart_count.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                const count = data.count || 0;
                const desktopCounter = document.getElementById('cart-counter');
                const mobileCounter = document.getElementById('cart-counter-mobile');

                if (desktopCounter) {
                    desktopCounter.textContent = count;
                    desktopCounter.style.display = count > 0 ? 'inline-block' : 'none';
                }
                if (mobileCounter) {
                    mobileCounter.textContent = count;
                    mobileCounter.style.display = count > 0 ? 'inline-block' : 'none';
                }
            })
            .catch(error => console.error('Error updating cart counters:', error));
    }

    // Initial update on page load
    updateCartCounters();

    // Listen for custom event to update counter after cart modifications
    document.addEventListener('edsys:cartUpdated', function() {
        updateCartCounters();
    });

    // Expose the update function globally so it can be called from other scripts if needed
    window.updateCartCounters = updateCartCounters;
});
