<?php
/**
 * Favorites Page - Fixed Version
 * Location: /personal/favorites/index.php
 * Version: 3.1.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Displays user's favorite products in list view
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

use Bitrix\Main\Loader;

global $USER, $APPLICATION;

// Check authorization
if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/favorites/');
    die();
}

$APPLICATION->SetTitle("Избранное");

// Add CSS
$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css?v=1.1.0");

// Load required modules
Loader::includeModule('iblock');
Loader::includeModule('catalog');

// Get user data
$userId = $USER->GetID();
$rsUser = CUser::GetByID($userId);
$arUser = $rsUser->Fetch();

// Get user's favorite product IDs
$favoriteIds = $arUser['UF_FAVORITES'] ?? [];

// Ensure it's an array
if (!is_array($favoriteIds)) {
    $favoriteIds = [];
}

// Remove empty values
$favoriteIds = array_filter($favoriteIds, function($id) {
    return !empty($id) && intval($id) > 0;
});

// Get user company
$userCompany = $arUser['WORK_COMPANY'] ?? '';

// Include personal menu
include($_SERVER["DOCUMENT_ROOT"] . "/personal/includes/menu.php");
?>

<main class="edsys-account__content">
    <header class="edsys-content-header">
        <h1 class="edsys-dashboard__title">Избранное</h1>
        <p class="edsys-dashboard__subtitle">Сохранённые товары (<?= count($favoriteIds) ?>)</p>
    </header>

    <?php if (empty($favoriteIds)): ?>
        <div class="edsys-favorites-empty">
            <i class="ph-thin ph-heart" aria-hidden="true"></i>
            <h2>Нет избранных товаров</h2>
            <p>Добавьте товары в избранное, чтобы быстро находить их позже</p>
            <a href="/catalog/" class="edsys-btn edsys-btn--primary">
                Перейти в каталог
            </a>
        </div>
    <?php else: ?>
        <div class="edsys-favorites-list">
            <div class="edsys-favorites-list-header">
                <span>Фото</span>
                <span>Наименование</span>
                <span>Артикул</span>
                <span>Цена</span>
                <span>Действия</span>
            </div>
            <?php
            // Get products by IDs
            $arSelect = array(
                "ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE",
                "DETAIL_PAGE_URL", "CATALOG_QUANTITY", "PREVIEW_TEXT"
            );
            $arFilter = array("IBLOCK_ID" => 7, "ID" => $favoriteIds, "ACTIVE" => "Y");
            $res = CIBlockElement::GetList(array("NAME" => "ASC"), $arFilter, false, false, $arSelect);

            while ($ob = $res->GetNextElement()) {
                $arFields = $ob->GetFields();
                $arProps = $ob->GetProperties();

                // Get article
                $article = '';
                $articleProps = ['CML2_ARTICLE', 'ARTICLE', 'ART', 'ARTICUL'];
                foreach ($articleProps as $propCode) {
                    if (!empty($arProps[$propCode]['VALUE'])) {
                        $article = is_array($arProps[$propCode]['VALUE']) ? $arProps[$propCode]['VALUE'][0] : $arProps[$propCode]['VALUE'];
                        break;
                    }
                }

                // Get prices
                $arPrice = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray());

                // Get main image
                $arImage = CFile::ResizeImageGet(
                    $arFields['PREVIEW_PICTURE'] ?: $arFields['DETAIL_PICTURE'],
                    ['width' => 100, 'height' => 100],
                    BX_RESIZE_IMAGE_PROPORTIONAL,
                    true
                );
                ?>
                <article class="edsys-favorite-item">
                    <a href="<?= $arFields['DETAIL_PAGE_URL'] ?>" class="edsys-favorite-item__link">
                        <div class="edsys-favorite-item__image">
                            <?php if ($arImage && $arImage['src']): ?>
                                <img src="<?= $arImage['src'] ?>" alt="<?= htmlspecialchars($arFields['NAME']) ?>" width="100" height="100" loading="lazy">
                            <?php else: ?>
                                <div class="edsys-favorite-item__no-image"><i class="ph ph-thin ph-image"></i></div>
                            <?php endif; ?>
                        </div>
                        <div class="edsys-favorite-item__info">
                            <h3 class="edsys-favorite-item__title"><?= $arFields['NAME'] ?></h3>
                            <?php if (!empty($arFields['PREVIEW_TEXT'])): ?>
                                <div class="edsys-favorite-item__description">
                                    <?= strip_tags($arFields['PREVIEW_TEXT']) ?>
                                </div>
                            <?php endif; ?>
                            <div class="edsys-favorite-item__availability">
                                <?php if ($arFields['CATALOG_QUANTITY'] > 0): ?>
                                    <span class="edsys-availability edsys-availability--in-stock"><i class="ph ph-thin ph-check-circle"></i> В наличии</span>
                                <?php else: ?>
                                    <span class="edsys-availability edsys-availability--preorder"><i class="ph ph-thin ph-clock"></i> Под заказ</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="edsys-favorite-item__article">
                            <?= !empty($article) ? htmlspecialchars($article) : '—' ?>
                        </div>
                        <div class="edsys-price-block">
                            <?php if (!empty($arPrice['RESULT_PRICE'])): ?>
                                <span class="edsys-price__actual"><?= CurrencyFormat($arPrice['RESULT_PRICE']['DISCOUNT_PRICE'], 'RUB') ?></span>
                                <?php if ($arPrice['RESULT_PRICE']['DISCOUNT'] > 0): ?>
                                    <span class="edsys-price__old"><?= CurrencyFormat($arPrice['RESULT_PRICE']['BASE_PRICE'], 'RUB') ?></span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="edsys-price__on-request">Цена по запросу</span>
                            <?php endif; ?>
                        </div>
                    </a>
                    <div class="edsys-favorite-item__actions">
                        <button type="button" class="edsys-btn edsys-btn--primary add-to-cart-btn" data-product-id="<?= $arFields['ID'] ?>">
                            <i class="ph-thin ph-shopping-cart"></i>
                            <span>В корзину</span>
                        </button>
                        <button type="button" class="favorite-toggle-btn active" title="Удалить из избранного" data-product-id="<?= $arFields['ID'] ?>">
                            <i class="ph-thin ph-trash"></i>
                            <span>Удалить</span>
                        </button>
                    </div>
                </article>
            <?php } ?>
        </div>

        <div class="edsys-favorites-actions">
            <button 
                type="button" 
                class="edsys-btn edsys-btn--secondary" 
                id="clear-favorites-btn"
            >
                <i class="ph-thin ph-trash"></i>
                Очистить избранное
            </button>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const favoritesList = document.querySelector('.edsys-favorites-list');
            const clearFavoritesBtn = document.getElementById('clear-favorites-btn');
            const favoritesCounter = document.querySelector('.edsys-dashboard__subtitle');
            const emptyFavoritesContainer = document.querySelector('.edsys-favorites-empty');

            // Event delegation for Add to Cart and Remove from Favorites
            if (favoritesList) {
                favoritesList.addEventListener('click', function(e) {
                    const addToCartBtn = e.target.closest('.add-to-cart-btn');
                    const removeFavoriteBtn = e.target.closest('.favorite-toggle-btn');

                    if (addToCartBtn) {
                        e.preventDefault();
                        const productId = addToCartBtn.dataset.productId;
                        handleAddToCart(productId, addToCartBtn);
                    }

                    if (removeFavoriteBtn) {
                        e.preventDefault();
                        const productId = removeFavoriteBtn.dataset.productId;
                        handleRemoveFavorite(productId, removeFavoriteBtn);
                    }
                });
            }

            // Clear all favorites button
            if (clearFavoritesBtn) {
                clearFavoritesBtn.addEventListener('click', function() {
                    if (confirm('Вы уверены, что хотите очистить все избранное?')) {
                        handleClearAllFavorites();
                    }
                });
            }

            function handleAddToCart(productId, button) {
                if (button.disabled) return;

                const originalText = button.innerHTML;
                button.innerHTML = '<span>Добавление...</span>';
                button.disabled = true;

                fetch('/ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ 
                        productId: parseInt(productId),
                        quantity: 1
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        button.innerHTML = '<span><i class="ph-thin ph-check-circle"></i> В корзине</span>';
                        // Optionally, update mini-cart if one exists
                        // document.dispatchEvent(new CustomEvent('update-mini-cart'));
                    } else {
                        button.innerHTML = '<span>Ошибка</span>';
                        setTimeout(() => {
                            button.innerHTML = originalText;
                            button.disabled = false;
                        }, 2000);
                    }
                })
                .catch(error => {
                    console.error('Add to cart error:', error);
                    button.innerHTML = '<span>Ошибка</span>';
                    setTimeout(() => {
                        button.innerHTML = originalText;
                        button.disabled = false;
                    }, 2000);
                });
            }

            function handleRemoveFavorite(productId, button) {
                const itemElement = button.closest('.edsys-favorite-item');

                fetch('/local/ajax/favorites.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ action: 'toggle', productId: productId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        itemElement.style.transition = 'opacity 0.3s ease';
                        itemElement.style.opacity = '0';
                        setTimeout(() => {
                            itemElement.remove();
                            const currentCount = favoritesList.querySelectorAll('.edsys-favorite-item').length;
                            if (favoritesCounter) {
                                favoritesCounter.textContent = `Сохранённые товары (${currentCount})`;
                            }
                            if (currentCount === 0) {
                                favoritesList.style.display = 'none';
                                if(clearFavoritesBtn) clearFavoritesBtn.style.display = 'none';
                                if(emptyFavoritesContainer) emptyFavoritesContainer.style.display = 'block';
                            }
                        }, 300);
                    } else {
                        // Handle error - maybe show a notification
                        console.error('Failed to remove favorite');
                    }
                })
                .catch(error => console.error('Remove favorite error:', error));
            }

            function handleClearAllFavorites() {
                const favoriteItems = document.querySelectorAll('.edsys-favorite-item');
                const productIds = Array.from(favoriteItems).map(item => item.querySelector('.favorite-toggle-btn').dataset.productId);

                let clearedCount = 0;

                productIds.forEach(productId => {
                    fetch('/local/ajax/favorites.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ action: 'toggle', productId: productId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        clearedCount++;
                        if (data.success) {
                            const itemElement = document.querySelector(`.favorite-toggle-btn[data-product-id='${productId}']`).closest('.edsys-favorite-item');
                            if(itemElement) itemElement.remove();
                        }
                        if (clearedCount === productIds.length) {
                            window.location.reload(); // Reload to show the empty message correctly
                        }
                    })
                    .catch(error => {
                        clearedCount++;
                        console.error('Clear favorite error:', error);
                        if (clearedCount === productIds.length) {
                            window.location.reload();
                        }
                    });
                });
            }
        });
        </script>
    <?php endif; ?>
</main>

</div>
</div>
</section>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>