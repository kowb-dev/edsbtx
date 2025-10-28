<?php
/**
 * Cart Page
 * 
 * @version 1.0.2
 * @date 2025-10-17
 * @description Personal cart page with basket component integration
 * 
 * Path: /personal/cart/index.php
 */

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php');

// Set page title
$APPLICATION->SetTitle('Корзина');

// Include basket component
$APPLICATION->IncludeComponent(
    'bitrix:sale.basket.basket',
    'edsys_cart',
    [
        // Main paths
        'PATH_TO_ORDER' => '/personal/order/',
        'PATH_TO_CATALOG' => '/catalog/',
        
        // Display columns
        'COLUMNS_LIST' => [
            'PREVIEW_PICTURE',
            'NAME',
            'PROPS',
            'PRICE',
            'QUANTITY',
            'SUM',
            'DELETE'
        ],
        
        // Settings
        'HIDE_COUPON' => 'N',
        'QUANTITY_FLOAT' => 'N',
        'PRICE_VAT_SHOW_VALUE' => 'Y',
        'SET_TITLE' => 'Y',
        
        // AJAX settings
        'AJAX_MODE' => 'Y',
        'AJAX_OPTION_JUMP' => 'N',
        'AJAX_OPTION_STYLE' => 'Y',
        'AJAX_OPTION_HISTORY' => 'N',
        
        // Additional
        'USE_ENHANCED_ECOMMERCE' => 'Y',
        'DATA_LAYER_NAME' => 'dataLayer',
        'AUTO_CALCULATION' => 'Y',
        'BASKET_WITH_ORDER_INTEGRATION' => 'Y',
        
        // Cache (disabled for basket)
        'CACHE_TYPE' => 'N',
        'CACHE_TIME' => '0',
    ],
    false
);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php');
?>