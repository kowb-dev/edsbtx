<?php
/**
 * AJAX handler for catalog filtering
 * Processes filter requests and returns filtered products
 * 
 * @version 1.0.0
 * @author KW
 * @link https://kowb.ru
 */

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;
use Bitrix\Main\Loader;

// Check required modules
if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    sendJsonResponse(false, [], 'Required modules not loaded');
}

// Get request
$request = Application::getInstance()->getContext()->getRequest();

// Check session
if (!check_bitrix_sessid()) {
    sendJsonResponse(false, [], 'Invalid session');
}

// Get POST data
$action = $request->getPost('action');
$iblockId = (int)$request->getPost('iblock_id');
$categoriesJson = $request->getPost('categories');

// Validate action
if ($action !== 'filter') {
    sendJsonResponse(false, [], 'Invalid action');
}

// Validate iblock ID
if ($iblockId <= 0) {
    sendJsonResponse(false, [], 'Invalid iblock ID');
}

// Parse categories
$categories = [];
if (!empty($categoriesJson)) {
    try {
        $categories = Json::decode($categoriesJson);
        if (!is_array($categories)) {
            $categories = [];
        }
    } catch (Exception $e) {
        sendJsonResponse(false, [], 'Invalid categories data');
    }
}

// Build filter
$arFilter = [
    'IBLOCK_ID' => $iblockId,
    'ACTIVE' => 'Y',
    'ACTIVE_DATE' => 'Y'
];

// Add category filter if not "all"
if (!empty($categories) && !in_array('all', $categories)) {
    $arFilter['SECTION_ID'] = $categories;
    $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
}

// Get products
$arSelect = [
    'ID',
    'IBLOCK_ID',
    'IBLOCK_SECTION_ID',
    'NAME',
    'PREVIEW_PICTURE',
    'PREVIEW_TEXT',
    'DETAIL_PAGE_URL',
    'PROPERTY_CML2_ARTICLE',
    'PROPERTY_NEWPRODUCT',
    'PROPERTY_SPECIALOFFER'
];

$rsElements = CIBlockElement::GetList(
    ['SORT' => 'ASC', 'NAME' => 'ASC'],
    $arFilter,
    false,
    ['nPageSize' => 50], // Pagination limit
    $arSelect
);

// Build HTML for products
$productsHtml = '';
$productCount = 0;

// Get prices
if (Loader::includeModule('catalog')) {
    CIBlockPriceTools::SetCatalogDiscountCache();
}

while ($arElement = $rsElements->GetNext()) {
    $productCount++;
    
    // Get picture
    $picture = null;
    if (!empty($arElement['PREVIEW_PICTURE'])) {
        $picture = CFile::ResizeImageGet(
            $arElement['PREVIEW_PICTURE'],
            ['width' => 400, 'height' => 400],
            BX_RESIZE_IMAGE_PROPORTIONAL
        );
    }
    
    // Default no-photo image
    if (empty($picture['src'])) {
        $picture = ['src' => SITE_TEMPLATE_PATH . '/images/no-photo.svg'];
    }
    
    // Get price
    $price = null;
    $hasPrice = false;
    
    if (Loader::includeModule('catalog')) {
        $arPrice = CPrice::GetBasePrice($arElement['ID']);
        if (!empty($arPrice) && $arPrice['PRICE'] > 0) {
            $price = [
                'VALUE' => $arPrice['PRICE'],
                'CURRENCY' => $arPrice['CURRENCY'],
                'PRINT_VALUE' => CurrencyFormat($arPrice['PRICE'], $arPrice['CURRENCY'])
            ];
            $hasPrice = true;
        }
    }
    
    // Build product card HTML
    ob_start();
    ?>
    
    <article 
        class="edsys-catalog__card" 
        data-section-id="<?= htmlspecialchars($arElement['IBLOCK_SECTION_ID']) ?>"
        itemscope 
        itemtype="https://schema.org/Product"
    >
        <a 
            href="<?= htmlspecialchars($arElement['DETAIL_PAGE_URL']) ?>" 
            class="edsys-catalog__card-link"
            itemprop="url"
            aria-label="<?= htmlspecialchars($arElement['NAME']) ?>"
        >
            <div class="edsys-catalog__card-image">
                <img 
                    src="<?= htmlspecialchars($picture['src']) ?>" 
                    alt="<?= htmlspecialchars($arElement['NAME']) ?>"
                    width="400"
                    height="400"
                    loading="lazy"
                    itemprop="image"
                    class="edsys-catalog__card-img"
                >
                
                <?php if (!empty($arElement['PROPERTY_NEWPRODUCT_VALUE'])): ?>
                    <span class="edsys-catalog__card-badge edsys-catalog__card-badge--new">
                        Новинка
                    </span>
                <?php endif; ?>
                
                <?php if (!empty($arElement['PROPERTY_SPECIALOFFER_VALUE'])): ?>
                    <span class="edsys-catalog__card-badge edsys-catalog__card-badge--special">
                        Акция
                    </span>
                <?php endif; ?>
            </div>

            <div class="edsys-catalog__card-content">
                <h3 class="edsys-catalog__card-title" itemprop="name">
                    <?= htmlspecialchars($arElement['NAME']) ?>
                </h3>

                <?php if (!empty($arElement['PROPERTY_CML2_ARTICLE_VALUE'])): ?>
                    <div class="edsys-catalog__card-article">
                        <span class="edsys-catalog__card-article-label">
                            Артикул:
                        </span>
                        <span class="edsys-catalog__card-article-value" itemprop="sku">
                            <?= htmlspecialchars($arElement['PROPERTY_CML2_ARTICLE_VALUE']) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($arElement['PREVIEW_TEXT'])): ?>
                    <div class="edsys-catalog__card-description" itemprop="description">
                        <?= htmlspecialchars($arElement['PREVIEW_TEXT']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </a>

        <div class="edsys-catalog__card-footer">
            <?php if ($hasPrice): ?>
                <div class="edsys-catalog__card-price" itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                    <meta itemprop="priceCurrency" content="<?= htmlspecialchars($price['CURRENCY']) ?>">
                    <link itemprop="availability" href="https://schema.org/InStock">
                    <span class="edsys-catalog__card-price-value" itemprop="price" content="<?= $price['VALUE'] ?>">
                        <?= htmlspecialchars($price['PRINT_VALUE']) ?>
                    </span>
                </div>
            <?php else: ?>
                <div class="edsys-catalog__card-price-request">
                    Цена по запросу
                </div>
            <?php endif; ?>

            <div class="edsys-catalog__card-actions">
                <button 
                    type="button"
                    class="edsys-catalog__card-btn edsys-catalog__card-btn--detail"
                    onclick="window.location.href='<?= htmlspecialchars($arElement['DETAIL_PAGE_URL']) ?>'"
                    aria-label="Подробная информация о товаре <?= htmlspecialchars($arElement['NAME']) ?>"
                >
                    <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                    <span>Подробнее</span>
                </button>
            </div>
        </div>
    </article>
    
    <?php
    $productsHtml .= ob_get_clean();
}

// Send response
sendJsonResponse(true, [
    'html' => $productsHtml,
    'count' => $productCount
]);

/**
 * Send JSON response helper
 * 
 * @param bool $success Success status
 * @param array $data Response data
 * @param string $error Error message
 */
function sendJsonResponse($success, $data = [], $error = '')
{
    header('Content-Type: application/json');
    
    $response = [
        'success' => $success,
        'data' => $data
    ];
    
    if (!$success && !empty($error)) {
        $response['error'] = $error;
    }
    
    echo Json::encode($response);
    
    // Stop execution
    require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
    die();
}