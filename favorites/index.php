<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Избранное");
?>

<div class="edsys-container">
    <main class="edsys-account__content">
        <header class="edsys-content-header">
            <h1 class="edsys-dashboard__title">Избранное</h1>
            <p class="edsys-dashboard__subtitle">Сохранённые товары (<span id="guest-favorites-count">0</span>)</p>
        </header>

        <div class="edsys-favorites-list edsys-favorites-list--guest" id="guest-favorites-container">
            <!-- Guest favorites will be loaded here -->
        </div>
        <div class="edsys-favorites-empty" style="display: none;">
            <i class="ph-thin ph-heart" aria-hidden="true"></i>
            <h2>Нет избранных товаров</h2>
            <p>Добавьте товары в избранное, чтобы быстро находить их позже</p>
            <a href="/catalog/" class="edsys-btn edsys-btn--primary">
                Перейти в каталог
            </a>
        </div>
    </main>
</div>

<style>
    .edsys-container {
        max-width: min(103.2rem, 100vw - 2rem);
        margin: 0 auto;
        padding: clamp(1rem, 3vw, 2rem);
    }

    .edsys-favorites-list--guest {
        display: flex;
        flex-direction: column;
        gap: 1rem;
    }

    .edsys-favorite-item {
        display: grid;
        grid-template-columns: 100px 1fr auto;
        gap: 1rem;
        padding: 1rem;
        background-color: var(--edsys-surface);
        border-radius: var(--radius-md);
        transition: background-color var(--edsys-transition-fast) var(--edsys-ease);
        text-decoration: none;
        color: var(--edsys-text);
    }

    .edsys-favorite-item:hover {
        background-color: #e9e9e9;
    }

    .edsys-favorite-item__image {
        width: 100px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: var(--edsys-white);
        border-radius: var(--radius-sm);
        overflow: hidden;
    }

    .edsys-favorite-item__image img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }
    
    .edsys-favorite-item__no-image {
        font-size: 2rem;
        color: var(--edsys-text-light);
    }

    .edsys-favorite-item__info {
        display: flex;
        flex-direction: column;
    }

    .edsys-favorite-item__title {
        font-size: var(--fs-base);
        font-weight: var(--edsys-font-bold);
        margin: 0 0 0.5rem;
    }

    .edsys-favorite-item__description {
        font-size: var(--fs-sm);
        color: var(--edsys-text-muted);
        line-height: var(--edsys-lh-normal);
    }

    .edsys-favorite-item__article {
        display: flex;
        flex-direction: column;
        align-items: flex-end;
        font-size: var(--fs-sm);
    }

    .edsys-favorite-item__article span:first-child {
        color: var(--edsys-text-muted);
    }

    .edsys-favorite-item__article span:last-child {
        font-weight: var(--edsys-font-bold);
        font-size: var(--fs-base);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const guestFavoritesContainer = document.getElementById('guest-favorites-container');
    const emptyFavoritesContainer = document.querySelector('.edsys-favorites-empty');
    const guestFavoritesCount = document.getElementById('guest-favorites-count');

    const favoriteIds = JSON.parse(localStorage.getItem('userFavorites')) || [];
    
    if (guestFavoritesCount) {
        guestFavoritesCount.textContent = favoriteIds.length;
    }

    if (favoriteIds.length > 0) {
        fetch('/ajax/get_favorites_html.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: favoriteIds })
        })
        .then(response => response.text())
        .then(html => {
            guestFavoritesContainer.innerHTML = html;
        })
        .catch(error => console.error('Error fetching guest favorites:', error));
    } else {
        if (emptyFavoritesContainer) {
            emptyFavoritesContainer.style.display = 'block';
        }
    }
});
</script>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
