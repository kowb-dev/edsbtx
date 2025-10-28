<?php
/**
 * AJAX handler for adding products to cart
 * @author KW
 * @version 1.0.0
 * @see https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Context;
use Bitrix\Currency\CurrencyManager;
use Bitrix\Sale\Basket;
use Bitrix\Sale\Fuser;
use Bitrix\Main\Loader;
use Bitrix\Sale\BasketItem;

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

// Check if user is authorized
if (!$USER->IsAuthorized()) {
    echo json_encode([
        'success' => false,
        'message' => 'Для добавления товара в корзину необходимо авторизоваться',
        'needAuth' => true
    ]);
    die();
}

// Get request data
$request = Context::getCurrent()->getRequest();
$productId = (int)$request->getPost('productId');
$quantity = (int)$request->getPost('quantity');

// Validate input
if ($productId <= 0 || $quantity <= 0) {
    echo json_encode([
        'success' => false,
        'message' => 'Некорректные параметры товара'
    ]);
    die();
}

try {
    Loader::includeModule('sale');
    Loader::includeModule('catalog');

    $basket = Basket::loadItemsForFUser(Fuser::getId(), Context::getCurrent()->getSite());

    // Check if product already exists in basket
    $basketItem = null;
    foreach ($basket as $item) {
        if ((int)$item->getProductId() === $productId) {
            $basketItem = $item;
            break;
        }
    }

    if ($basketItem) {
        // Update quantity if product exists
        $basketItem->setField('QUANTITY', $basketItem->getQuantity() + $quantity);
    } else {
        // Add new product to basket
        $basketItem = $basket->createItem('catalog', $productId);
        $basketItem->setFields([
            'QUANTITY' => $quantity,
            'CURRENCY' => CurrencyManager::getBaseCurrency(),
            'LID' => Context::getCurrent()->getSite(),
            'PRODUCT_PROVIDER_CLASS' => 'CCatalogProductProvider'
        ]);
    }

    // Save basket
    $basket->save();

    // Get updated basket count
    $basketCount = $basket->count();
    
    // Get item price for response
    $price = $basketItem->getPrice();
    $formattedPrice = CCurrencyLang::CurrencyFormat($price, CurrencyManager::getBaseCurrency());

    echo json_encode([
        'success' => true,
        'message' => 'Товар успешно добавлен в корзину',
        'basketCount' => $basketCount,
        'price' => $price,
        'formattedPrice' => $formattedPrice
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Произошла ошибка при добавлении товара в корзину'
    ]);
}