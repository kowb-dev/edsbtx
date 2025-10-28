<?php
/**
 * AJAX handler for adding products to basket
 * File: /local/ajax/basket/add.php
 * 
 * Version: 1.2.0
 * Author: KW
 * URI: https://kowb.ru
 */

// Prevent any output before JSON
define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('NO_KEEP_STATISTIC', true);
define('BX_NO_ACCELERATOR_RESET', true);

// Clear any output buffers
while (ob_get_level()) {
    ob_end_clean();
}

// Start clean output buffer
ob_start();

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

// Set JSON header immediately
header('Content-Type: application/json; charset=utf-8');

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Catalog\ProductTable;
use Bitrix\Main\Web\Json;

// Check if modules are loaded
if (!Loader::includeModule('catalog') || !Loader::includeModule('sale')) {
    ob_clean();
    echo Json::encode([
        'success' => false,
        'error' => 'Required modules are not installed'
    ]);
    exit();
}

// Get request data
$request = Context::getCurrent()->getRequest();
$productId = (int)$request->getPost('productId');
$quantity = (int)$request->getPost('quantity');
$sessid = $request->getPost('sessid');

// Response array
$response = [
    'success' => false,
    'error' => '',
    'basketId' => 0,
    'basketCount' => 0,
    'basketTotal' => 0,
    'productName' => '',
    'debug' => []
];

// Debug info
if (defined('DEBUG') && DEBUG === true) {
    $response['debug']['received_sessid'] = $sessid ? 'yes' : 'no';
    $response['debug']['expected_sessid'] = bitrix_sessid();
    $response['debug']['productId'] = $productId;
    $response['debug']['quantity'] = $quantity;
}

// Validate request - use simplified check for AJAX
if (!$sessid || $sessid !== bitrix_sessid()) {
    // Try alternative validation
    if (!check_bitrix_sessid()) {
        $response['error'] = 'Security token validation failed';
        ob_clean();
        echo Json::encode($response);
        exit();
    }
}

if ($productId <= 0) {
    $response['error'] = 'Invalid product ID';
    ob_clean();
    echo Json::encode($response);
    exit();
}

if ($quantity <= 0) {
    $quantity = 1;
}

// Check if user is authorized
global $USER;
if (!$USER->IsAuthorized()) {
    $response['error'] = 'Authorization required to add items to basket';
    $response['needAuth'] = true;
    ob_clean();
    echo Json::encode($response);
    exit();
}

try {
    // Get product info
    $product = CCatalogProduct::GetByID($productId);
    if (!$product) {
        $response['error'] = 'Product not found';
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    // Get element info
    $arElement = CIBlockElement::GetByID($productId)->GetNext();
    if (!$arElement) {
        $response['error'] = 'Product element not found';
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    $response['productName'] = $arElement['NAME'];

    // Check if product is available
    if ($product['QUANTITY'] <= 0 && $product['CAN_BUY_ZERO'] !== 'Y') {
        $response['error'] = 'Product is out of stock';
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    // Check quantity limits
    if ($quantity > $product['QUANTITY'] && $product['CAN_BUY_ZERO'] !== 'Y') {
        $response['error'] = 'Requested quantity exceeds available stock';
        $response['availableQuantity'] = (int)$product['QUANTITY'];
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    // Get user groups for price calculation
    $userGroups = $USER->GetUserGroupArray();
    
    // Get optimal price
    $arPrice = CCatalogProduct::GetOptimalPrice(
        $productId,
        $quantity,
        $userGroups,
        'N',
        [],
        SITE_ID
    );

    if (!$arPrice || !isset($arPrice['RESULT_PRICE'])) {
        $response['error'] = 'Unable to calculate product price';
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    $price = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
    $currency = $arPrice['RESULT_PRICE']['CURRENCY'];

    // Prepare basket item data
    $arFields = [
        'PRODUCT_ID' => $productId,
        'QUANTITY' => $quantity,
        'PRICE' => $price,
        'CURRENCY' => $currency,
        'LID' => SITE_ID,
        'DELAY' => 'N',
        'CAN_BUY' => 'Y',
        'NAME' => $arElement['NAME'],
        'DETAIL_PAGE_URL' => $arElement['DETAIL_PAGE_URL'],
    ];

    // Add product image if available
    if ($arElement['PREVIEW_PICTURE']) {
        $arFields['PREVIEW_PICTURE'] = $arElement['PREVIEW_PICTURE'];
    } elseif ($arElement['DETAIL_PICTURE']) {
        $arFields['DETAIL_PICTURE'] = $arElement['DETAIL_PICTURE'];
    }

    // Get Fuser ID
    $fUserId = CSaleBasket::GetBasketUserID();
    if (!$fUserId) {
        $response['error'] = 'Unable to initialize basket';
        ob_clean();
        echo Json::encode($response);
        exit();
    }

    // Check if product already exists in basket
    $arBasketItems = CSaleBasket::GetList(
        [],
        [
            'FUSER_ID' => $fUserId,
            'ORDER_ID' => 'NULL',
            'LID' => SITE_ID,
            'PRODUCT_ID' => $productId
        ],
        false,
        false,
        ['ID', 'QUANTITY']
    );

    $existingItem = $arBasketItems->Fetch();

    if ($existingItem) {
        // Update quantity of existing item
        $newQuantity = $existingItem['QUANTITY'] + $quantity;
        
        $updateResult = CSaleBasket::Update($existingItem['ID'], [
            'QUANTITY' => $newQuantity
        ]);

        if ($updateResult) {
            $response['success'] = true;
            $response['basketId'] = $existingItem['ID'];
            $response['updated'] = true;
        } else {
            $response['error'] = 'Failed to update basket item';
            ob_clean();
            echo Json::encode($response);
            exit();
        }
    } else {
        // Add new item to basket
        $basketId = Add2BasketByProductID(
            $productId,
            $quantity,
            [],
            []
        );

        if ($basketId) {
            $response['success'] = true;
            $response['basketId'] = $basketId;
            $response['updated'] = false;
        } else {
            // Fallback to manual addition
            $basketId = CSaleBasket::Add($arFields);
            
            if ($basketId) {
                $response['success'] = true;
                $response['basketId'] = $basketId;
                $response['updated'] = false;
            } else {
                $response['error'] = 'Failed to add product to basket: ' . (CSaleBasket::GetErrors() ?: 'Unknown error');
                ob_clean();
                echo Json::encode($response);
                exit();
            }
        }
    }

    // Get updated basket info
    $basketTotal = 0;
    $basketCount = 0;

    $arBasket = CSaleBasket::GetList(
        [],
        [
            'FUSER_ID' => $fUserId,
            'ORDER_ID' => 'NULL',
            'LID' => SITE_ID
        ],
        false,
        false,
        ['ID', 'QUANTITY', 'PRICE', 'CURRENCY']
    );

    while ($item = $arBasket->Fetch()) {
        $basketCount += (int)$item['QUANTITY'];
        $basketTotal += (float)$item['PRICE'] * (int)$item['QUANTITY'];
    }

    $response['basketCount'] = $basketCount;
    $response['basketTotal'] = number_format($basketTotal, 0, ',', ' ') . ' ₽';

    // Log successful addition
    if (defined('DEBUG') && DEBUG === true) {
        AddMessage2Log(sprintf(
            'Product added to basket: ID=%d, Quantity=%d, Price=%s, BasketID=%d',
            $productId,
            $quantity,
            $price,
            $response['basketId']
        ));
    }

} catch (Exception $e) {
    $response['error'] = 'Server error: ' . $e->getMessage();
    
    if (defined('DEBUG') && DEBUG === true) {
        AddMessage2Log('Basket add error: ' . $e->getMessage());
    }
}

// Send response
ob_clean(); // Clear any previous output
echo Json::encode($response);
exit();