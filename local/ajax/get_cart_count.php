<?php
/**
 * Get Cart Items Count
 * Returns the number of items in the current user's cart.
 * Version: 1.0.0
 * Date: 2025-10-25
 * Author: KW
 * URI: https://kowb.ru
 */

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Sale\Fuser;
use Bitrix\Sale\Basket;

if (!Loader::includeModule('sale')) {
    echo json_encode(['error' => 'Sale module not found']);
    exit;
}

$basket = Basket::loadItemsForFUser(Fuser::getId(), SITE_ID);

// We count the number of basket items, not the total quantity of products
$count = count($basket->getBasketItems());

header('Content-Type: application/json');
echo json_encode(['count' => $count]);