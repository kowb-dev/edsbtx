<?php
/**
 * Compare products page - FIXED VERSION WITH ARTICLE
 * Version: 1.2.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Location: /compare/index.php
 * 
 * Changelog:
 * - 1.2.0: Added article display in comparison table
 * - 1.1.0: Fixed session handling and data retrieval
 * - 1.0.0: Initial version
 */

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

$APPLICATION->SetTitle('Сравнение товаров');
$APPLICATION->SetPageProperty('description', 'Сравните характеристики выбранных товаров');

// Load required modules
if (!CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
    ShowError('Ошибка загрузки модулей');
    require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
    die();
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Initialize compare list
if (!isset($_SESSION['COMPARE_LIST'])) {
    $_SESSION['COMPARE_LIST'] = [];
}

$compareList = $_SESSION['COMPARE_LIST'];

// Debug info (remove in production)
if ($_GET['debug'] == 'Y') {
    echo '<pre style="background: #f0f0f0; padding: 1rem; margin: 1rem; border-radius: 0.5rem;">';
    echo 'Session ID: ' . session_id() . "\n";
    echo 'Compare List: ';
    print_r($compareList);
    echo '</pre>';
}

// Get IBLOCK_ID from settings
$IBLOCK_ID = 7; // Change to your catalog IBLOCK_ID

// Prepare result array
$arResult = [
    'ITEMS' => [],
    'PROPERTIES' => [],
    'COMPARE_COUNT' => count($compareList)
];

// Fetch products if compare list is not empty
if (!empty($compareList) && is_array($compareList)) {
    $arSelect = [
        'ID',
        'IBLOCK_ID',
        'NAME',
        'DETAIL_PAGE_URL',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'PREVIEW_TEXT',
        'PREVIEW_TEXT_TYPE',
        'DETAIL_TEXT',
        'DETAIL_TEXT_TYPE',
        'PROPERTY_*'
    ];
    
    $arFilter = [
        'IBLOCK_ID' => $IBLOCK_ID,
        'ID' => $compareList,
        'ACTIVE' => 'Y'
    ];
    
    $rsElements = CIBlockElement::GetList(
        ['SORT' => 'ASC'],
        $arFilter,
        false,
        false,
        $arSelect
    );
    
    // Collect all properties for comparison
    $allProperties = [];
    
    while ($arItem = $rsElements->GetNextElement()) {
        $arFields = $arItem->GetFields();
        $arProps = $arItem->GetProperties();
        
        // Get main image
        $imageUrl = '';
        if ($arFields['PREVIEW_PICTURE']) {
            $image = CFile::GetFileArray($arFields['PREVIEW_PICTURE']);
            $imageUrl = $image['SRC'];
        } elseif ($arFields['DETAIL_PICTURE']) {
            $image = CFile::GetFileArray($arFields['DETAIL_PICTURE']);
            $imageUrl = $image['SRC'];
        }
        
        // Get article number from properties
        $article = '';
        if (!empty($arProps['CML2_ARTICLE']['VALUE'])) {
            $article = $arProps['CML2_ARTICLE']['VALUE'];
        } elseif (!empty($arProps['ARTICLE']['VALUE'])) {
            $article = $arProps['ARTICLE']['VALUE'];
        } elseif (!empty($arProps['ARTNUMBER']['VALUE'])) {
            $article = $arProps['ARTNUMBER']['VALUE'];
        }
        
        // Get description (for additional info instead of gallery images)
        $description = '';
        if (!empty($arFields['PREVIEW_TEXT'])) {
            $description = $arFields['PREVIEW_TEXT'];
            // Remove HTML tags for clean display
            $description = strip_tags($description);
        } elseif (!empty($arFields['DETAIL_TEXT'])) {
            $description = $arFields['DETAIL_TEXT'];
            // Remove HTML tags for clean display
            $description = strip_tags($description);
            // Limit length to reasonable amount for comparison
            if (mb_strlen($description) > 300) {
                $description = mb_substr($description, 0, 300) . '...';
            }
        }
        
        // Get price
        $price = 0;
        $priceFormatted = '';
        if (CModule::IncludeModule('catalog')) {
            $arPrice = CPrice::GetBasePrice($arFields['ID']);
            if ($arPrice) {
                $price = $arPrice['PRICE'];
                $priceFormatted = CurrencyFormat($price, $arPrice['CURRENCY']);
            }
        }
        
        // Prepare item
        $item = [
            'ID' => $arFields['ID'],
            'NAME' => $arFields['NAME'],
            'ARTICLE' => $article,
            'DETAIL_PAGE_URL' => $arFields['DETAIL_PAGE_URL'],
            'IMAGE' => $imageUrl,
            'DESCRIPTION' => $description,
            'PRICE' => $price,
            'PRICE_FORMATTED' => $priceFormatted,
            'PROPERTIES' => []
        ];
        
        // Process properties (exclude article properties as we already got them)
        foreach ($arProps as $propCode => $arProp) {
            // Skip empty properties, HTML type, and article properties
            if (empty($arProp['VALUE']) || 
                $arProp['USER_TYPE'] === 'HTML' ||
                in_array($propCode, ['CML2_ARTICLE', 'ARTICLE', 'ARTNUMBER'])) {
                continue;
            }
            
            // Format value
            $value = is_array($arProp['VALUE']) ? implode(', ', $arProp['VALUE']) : $arProp['VALUE'];
            
            // Add to item
            $item['PROPERTIES'][$propCode] = [
                'NAME' => $arProp['NAME'],
                'VALUE' => $value
            ];
            
            // Collect all unique properties
            if (!isset($allProperties[$propCode])) {
                $allProperties[$propCode] = $arProp['NAME'];
            }
        }
        
        $arResult['ITEMS'][] = $item;
    }
    
    $arResult['PROPERTIES'] = $allProperties;
}

?>

<div class="edsys-compare-page">
    <div class="edsys-container">
        
        <!-- Breadcrumbs -->
        <nav class="edsys-breadcrumb" aria-label="Хлебные крошки">
            <ol class="edsys-breadcrumb__list">
                <li class="edsys-breadcrumb__item">
                    <a href="/" class="edsys-breadcrumb__link">Главная</a>
                </li>
                <li class="edsys-breadcrumb__item edsys-breadcrumb__item--current">
                    Сравнение товаров
                </li>
            </ol>
        </nav>

        <!-- Page Header -->
        <div class="edsys-compare-header">
            <h1 class="edsys-compare-title">Сравнение товаров</h1>
            <?php if ($arResult['COMPARE_COUNT'] > 0): ?>
                <p class="edsys-compare-count">
                    Товаров в сравнении: <strong><?= $arResult['COMPARE_COUNT'] ?></strong>
                </p>
            <?php endif; ?>
        </div>

        <?php if (empty($arResult['ITEMS'])): ?>
            
            <!-- Empty State -->
            <div class="edsys-compare-empty">
                <i class="ph ph-thin ph-arrows-left-right"></i>
                <h2>Список сравнения пуст</h2>
                <p>Вы ещё не добавили товары для сравнения</p>
                <a href="/catalog/" class="edsys-btn edsys-btn--primary">
                    <i class="ph ph-thin ph-shopping-bag"></i>
                    <span>Перейти в каталог</span>
                </a>
            </div>

        <?php else: ?>
            
            <!-- Clear All Button -->
            <div class="edsys-compare-controls">
                <button type="button" class="edsys-btn edsys-btn--outline" id="clearAllCompare">
                    <i class="ph ph-thin ph-trash"></i>
                    <span>Очистить все</span>
                </button>
            </div>

            <!-- Compare Table -->
            <div class="edsys-compare-table-wrapper">
                <table class="edsys-compare-table">
                    <thead>
                        <tr>
                            <th class="edsys-compare-table__prop-name">Характеристики</th>
                            <?php foreach ($arResult['ITEMS'] as $item): ?>
                                <th class="edsys-compare-table__product">
                                    <div class="edsys-compare-product">
                                        
                                        <!-- Remove Button -->
                                        <button 
                                            type="button" 
                                            class="edsys-compare-product__remove"
                                            data-compare-remove="<?= $item['ID'] ?>"
                                            title="Удалить из сравнения"
                                            aria-label="Удалить <?= htmlspecialchars($item['NAME']) ?> из сравнения"
                                        >
                                            <i class="ph ph-thin ph-x"></i>
                                        </button>

                                        <!-- Product Image (main/primary) -->
                                        <?php if ($item['IMAGE']): ?>
                                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>" class="edsys-compare-product__image">
                                                <img 
                                                    src="<?= $item['IMAGE'] ?>" 
                                                    alt="<?= htmlspecialchars($item['NAME']) ?>"
                                                    loading="lazy"
                                                    width="150"
                                                    height="150"
                                                >
                                            </a>
                                        <?php else: ?>
                                            <div class="edsys-compare-product__image edsys-compare-product__image--no-image">
                                                <i class="ph ph-thin ph-image"></i>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Product Name -->
                                        <h3 class="edsys-compare-product__name">
                                            <a href="<?= $item['DETAIL_PAGE_URL'] ?>">
                                                <?= htmlspecialchars($item['NAME']) ?>
                                            </a>
                                        </h3>

                                        <!-- Article Number -->
                                        <?php if (!empty($item['ARTICLE'])): ?>
                                            <div class="edsys-compare-product__article">
                                                Арт. <?= htmlspecialchars($item['ARTICLE']) ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Price -->
                                        <?php if ($item['PRICE_FORMATTED']): ?>
                                            <div class="edsys-compare-product__price">
                                                <?= $item['PRICE_FORMATTED'] ?>
                                            </div>
                                        <?php endif; ?>

                                    </div>
                                </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Description Row -->
                        <tr class="edsys-compare-table__description-row">
                            <td class="edsys-compare-table__prop-name">
                                Описание
                            </td>
                            <?php foreach ($arResult['ITEMS'] as $item): ?>
                                <td class="edsys-compare-table__prop-value edsys-compare-table__description">
                                    <?php if (!empty($item['DESCRIPTION'])): ?>
                                        <?= nl2br(htmlspecialchars($item['DESCRIPTION'])) ?>
                                    <?php else: ?>
                                        <span class="edsys-compare-table__empty-value">—</span>
                                    <?php endif; ?>
                                </td>
                            <?php endforeach; ?>
                        </tr>
                        
                        <!-- Other Properties -->
                        <?php if (!empty($arResult['PROPERTIES'])): ?>
                            <?php foreach ($arResult['PROPERTIES'] as $propCode => $propName): ?>
                                <?php
                                // Skip properties that contain image IDs or gallery data
                                if (stripos($propName, 'картинк') !== false || 
                                    stripos($propName, 'фото') !== false ||
                                    stripos($propName, 'изображ') !== false ||
                                    stripos($propName, 'image') !== false ||
                                    stripos($propName, 'photo') !== false ||
                                    stripos($propName, 'gallery') !== false ||
                                    $propCode === 'MORE_PHOTO' ||
                                    $propCode === 'PHOTOS' ||
                                    $propCode === 'IMAGES') {
                                    continue;
                                }
                                ?>
                                <tr>
                                    <td class="edsys-compare-table__prop-name">
                                        <?= htmlspecialchars($propName) ?>
                                    </td>
                                    <?php foreach ($arResult['ITEMS'] as $item): ?>
                                        <td class="edsys-compare-table__prop-value">
                                            <?php 
                                            echo isset($item['PROPERTIES'][$propCode]) 
                                                ? htmlspecialchars($item['PROPERTIES'][$propCode]['VALUE']) 
                                                : '—';
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="<?= count($arResult['ITEMS']) + 1 ?>" class="edsys-compare-table__empty">
                                    <p>У выбранных товаров нет дополнительных свойств для сравнения</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Actions -->
            <div class="edsys-compare-actions">
                <a href="/catalog/" class="edsys-btn edsys-btn--secondary">
                    <i class="ph ph-thin ph-arrow-left"></i>
                    <span>Вернуться в каталог</span>
                </a>
            </div>

        <?php endif; ?>

    </div>
</div>

<script>
// Get sessid for AJAX requests
function getSessid() {
    // Try BX object
    if (typeof BX !== 'undefined' && BX.bitrix_sessid) {
        return BX.bitrix_sessid();
    }
    
    // Try meta tag
    const metaSessid = document.querySelector('meta[name="bitrix-sessid"]');
    if (metaSessid) {
        return metaSessid.getAttribute('content');
    }
    
    // Try input field
    const inputSessid = document.querySelector('input[name="sessid"]');
    if (inputSessid) {
        return inputSessid.value;
    }
    
    return '';
}

// Clear all compare items
const clearAllBtn = document.getElementById('clearAllCompare');
if (clearAllBtn) {
    clearAllBtn.addEventListener('click', async function() {
        if (!confirm('Вы уверены, что хотите очистить весь список сравнения?')) {
            return;
        }
        
        const sessid = getSessid();
        
        if (!sessid) {
            alert('Ошибка: не удалось получить токен безопасности');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('sessid', sessid);
            
            const response = await fetch('/local/ajax/compare/clear.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.message || 'Не удалось очистить список'));
            }
        } catch (error) {
            console.error('Error clearing compare:', error);
            alert('Произошла ошибка. Попробуйте позже.');
        }
    });
}

// Remove individual items
document.querySelectorAll('[data-compare-remove]').forEach(button => {
    button.addEventListener('click', async function() {
        const productId = this.dataset.compareRemove;
        const sessid = getSessid();
        
        if (!sessid) {
            alert('Ошибка: не удалось получить токен безопасности');
            return;
        }
        
        try {
            const formData = new FormData();
            formData.append('productId', productId);
            formData.append('sessid', sessid);
            
            const response = await fetch('/local/ajax/compare/add.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                location.reload();
            } else {
                alert('Ошибка: ' + (data.message || 'Не удалось удалить товар'));
            }
        } catch (error) {
            console.error('Error removing from compare:', error);
            alert('Произошла ошибка. Попробуйте позже.');
        }
    });
});
</script>

<style>
/* Compare Page Styles */
.edsys-compare-page {
    padding: var(--space-2xl, 2rem) 0;
    min-height: 60vh;
}

.edsys-compare-header {
    margin-bottom: var(--space-2xl, 2rem);
}

.edsys-compare-title {
    font-size: var(--edsys-fs-h1, 2rem);
    font-weight: var(--edsys-font-bold, 700);
    color: var(--edsys-text, #21242D);
    margin-bottom: var(--space-sm, 0.5rem);
}

.edsys-compare-count {
    font-size: var(--fs-lg, 1.125rem);
    color: var(--edsys-text-muted, #555);
}

/* Empty State */
.edsys-compare-empty {
    text-align: center;
    padding: var(--space-3xl, 3rem) var(--space-lg, 1rem);
    background: var(--edsys-bg-light, #f8f9fa);
    border-radius: var(--radius-lg, 1rem);
}

.edsys-compare-empty i {
    font-size: 4rem;
    color: var(--edsys-text-light, #777);
    margin-bottom: var(--space-lg, 1rem);
}

.edsys-compare-empty h2 {
    font-size: var(--edsys-fs-h2, 1.75rem);
    font-weight: var(--edsys-font-bold, 700);
    color: var(--edsys-text, #21242D);
    margin-bottom: var(--space-md, 0.75rem);
}

.edsys-compare-empty p {
    font-size: var(--fs-lg, 1.125rem);
    color: var(--edsys-text-muted, #555);
    margin-bottom: var(--space-xl, 1.5rem);
}

/* Controls */
.edsys-compare-controls {
    display: flex;
    justify-content: flex-end;
    margin-bottom: var(--space-xl, 1.5rem);
}

/* Table */
.edsys-compare-table-wrapper {
    overflow-x: auto;
    margin-bottom: var(--space-2xl, 2rem);
    border-radius: var(--radius-lg, 1rem);
    box-shadow: var(--edsys-shadow, 0 2px 8px rgba(0, 0, 0, 0.1));
}

.edsys-compare-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
    background: var(--edsys-white, #fff);
    min-width: 800px;
}

.edsys-compare-table thead {
    background: var(--edsys-white, #fff);
}

.edsys-compare-table th,
.edsys-compare-table td {
    padding: var(--space-lg, 1rem);
    border-bottom: 1px solid var(--edsys-border, #e0e0e0);
    vertical-align: top;
    background: var(--edsys-white, #fff);
}

.edsys-compare-table__prop-name {
    font-weight: var(--edsys-font-bold, 700);
    color: var(--edsys-text, #21242D);
    min-width: 200px;
    position: sticky;
    left: 0;
    background: var(--edsys-white, #fff);
    z-index: 1;
}

.edsys-compare-table tbody .edsys-compare-table__prop-name {
    background: var(--edsys-white, #fff);
}

.edsys-compare-table__product {
    min-width: 250px;
}

.edsys-compare-product {
    display: flex;
    flex-direction: column;
    gap: var(--space-md, 0.75rem);
    position: relative;
}

.edsys-compare-product__remove {
    position: absolute;
    top: -0.5rem;
    right: -0.5rem;
    width: 2rem;
    height: 2rem;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--edsys-white, #fff);
    border: 1px solid var(--edsys-border, #e0e0e0);
    border-radius: 50%;
    cursor: pointer;
    transition: all 0.2s ease;
    z-index: 2;
}

.edsys-compare-product__remove:hover {
    background: var(--edsys-accent, #ff2545);
    color: var(--edsys-white, #fff);
    border-color: var(--edsys-accent, #ff2545);
}

.edsys-compare-product__image {
    display: block;
    width: 150px;
    height: 150px;
    margin: 0 auto;
    border-radius: var(--radius-md, 0.75rem);
    overflow: hidden;
    background: var(--edsys-bg-light, #f8f9fa);
}

.edsys-compare-product__image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.edsys-compare-product__image--no-image {
    display: flex;
    align-items: center;
    justify-content: center;
}

.edsys-compare-product__image--no-image i {
    font-size: 3rem;
    color: var(--edsys-text-light, #777);
}

.edsys-compare-product__name {
    font-size: var(--fs-base, 1rem);
    font-weight: var(--edsys-font-bold, 700);
    text-align: center;
}

.edsys-compare-product__name a {
    color: var(--edsys-text, #21242D);
    text-decoration: none;
    transition: color 0.2s ease;
}

.edsys-compare-product__name a:hover {
    color: var(--edsys-accent, #ff2545);
}

/* Article Number */
.edsys-compare-product__article {
    font-size: var(--fs-sm, 0.875rem);
    color: var(--edsys-text-muted, #555);
    text-align: center;
    padding: var(--space-xs, 0.25rem) var(--space-sm, 0.5rem);
    background: var(--edsys-bg-light, #f8f9fa);
    border-radius: var(--radius-sm, 0.25rem);
    font-weight: 500;
}

.edsys-compare-product__price {
    font-size: var(--fs-lg, 1.125rem);
    font-weight: var(--edsys-font-bold, 700);
    color: var(--edsys-accent, #ff2545);
    text-align: center;
}

.edsys-compare-table__prop-value {
    font-size: var(--fs-base, 1rem);
    color: var(--edsys-text, #21242D);
}

/* Description Row Styles */
.edsys-compare-table__description-row {
    background: var(--edsys-white, #fff);
}

.edsys-compare-table__description-row .edsys-compare-table__prop-name {
    background: var(--edsys-white, #fff);
}

.edsys-compare-table__description {
    max-height: 200px;
    overflow-y: auto;
    line-height: var(--edsys-lh-normal, 1.6);
    font-size: var(--fs-sm, 0.875rem);
    color: var(--edsys-text-muted, #555);
    background: var(--edsys-white, #fff);
}

.edsys-compare-table__empty-value {
    color: var(--edsys-text-light, #777);
    font-style: italic;
}

.edsys-compare-table__empty {
    text-align: center;
    padding: var(--space-2xl, 2rem) !important;
    color: var(--edsys-text-muted, #555);
}

/* Actions */
.edsys-compare-actions {
    display: flex;
    justify-content: center;
    gap: var(--space-lg, 1rem);
}

/* Mobile */
@media (max-width: 768px) {
    .edsys-compare-table-wrapper {
        margin: 0 calc(var(--container-padding) * -1);
        border-radius: 0;
    }
    
    .edsys-compare-table {
        min-width: 600px;
    }
    
    .edsys-compare-controls {
        justify-content: center;
    }
    
    .edsys-compare-product__article {
        font-size: var(--fs-xxs, 0.625rem);
    }
}
</style>

<?php require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php'); ?>