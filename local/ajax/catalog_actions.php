<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * AJAX обработчик для действий с товарами в каталоге
 */

// Проверяем AJAX запрос
if (!$_SERVER['HTTP_X_REQUESTED_WITH'] || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    die('Access denied');
}

// Проверяем сессию
if (!check_bitrix_sessid()) {
    die(Json::encode(['success' => false, 'message' => 'Неверная сессия']));
}

$request = Application::getInstance()->getContext()->getRequest();
$action = $request->getPost('action');
$itemId = intval($request->getPost('id'));

if (!$action || !$itemId) {
    die(Json::encode(['success' => false, 'message' => 'Неверные параметры']));
}

$response = ['success' => false, 'message' => 'Неизвестное действие'];

switch ($action) {
    case 'add_to_favorites':
        $response = addToFavorites($itemId);
        break;
        
    case 'remove_from_favorites':
        $response = removeFromFavorites($itemId);
        break;
        
    case 'add_to_compare':
        $response = addToCompare($itemId);
        break;
        
    case 'remove_from_compare':
        $response = removeFromCompare($itemId);
        break;
        
    case 'add_to_cart':
        $response = addToCart($itemId);
        break;
}

header('Content-Type: application/json');
echo Json::encode($response);

/**
 * Добавление в избранное
 */
function addToFavorites($itemId) {
    global $USER;
    
    if (!$USER->IsAuthorized()) {
        return ['success' => false, 'message' => 'Необходимо авторизоваться'];
    }
    
    // Здесь должна быть логика добавления в избранное
    // Например, через API пользовательских полей или отдельную таблицу
    
    return ['success' => true, 'message' => 'Товар добавлен в избранное'];
}

/**
 * Удаление из избранного
 */
function removeFromFavorites($itemId) {
    global $USER;
    
    if (!$USER->IsAuthorized()) {
        return ['success' => false, 'message' => 'Необходимо авторизоваться'];
    }
    
    // Здесь должна быть логика удаления из избранного
    
    return ['success' => true, 'message' => 'Товар удален из избранного'];
}

/**
 * Добавление к сравнению
 */
function addToCompare($itemId) {
    if (\Bitrix\Main\Loader::includeModule('catalog')) {
        $compareList = new \Bitrix\Catalog\Product\Compare\CompareList();
        $result = $compareList->add($itemId);
        
        if ($result) {
            $count = $compareList->getCount();
            return [
                'success' => true, 
                'message' => 'Товар добавлен к сравнению',
                'compareCount' => $count
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Ошибка добавления к сравнению'];
}

/**
 * Удаление из сравнения
 */
function removeFromCompare($itemId) {
    if (\Bitrix\Main\Loader::includeModule('catalog')) {
        $compareList = new \Bitrix\Catalog\Product\Compare\CompareList();
        $result = $compareList->delete($itemId);
        
        if ($result) {
            $count = $compareList->getCount();
            return [
                'success' => true, 
                'message' => 'Товар удален из сравнения',
                'compareCount' => $count
            ];
        }
    }
    
    return ['success' => false, 'message' => 'Ошибка удаления из сравнения'];
}

/**
 * Добавление в корзину
 */
function addToCart($itemId) {
    if (!\Bitrix\Main\Loader::includeModule('sale') || !\Bitrix\Main\Loader::includeModule('catalog')) {
        return ['success' => false, 'message' => 'Модули не подключены'];
    }
    
    // Проверяем существование товара
    $arProduct = \CCatalogProduct::GetByID($itemId);
    if (!$arProduct) {
        return ['success' => false, 'message' => 'Товар не найден'];
    }
    
    // Проверяем возможность покупки
    if ($arProduct['CAN_BUY'] !== 'Y') {
        return ['success' => false, 'message' => 'Товар недоступен для покупки'];
    }
    
    // Добавляем в корзину
    $quantity = 1;
    $arFields = array(
        'PRODUCT_ID' => $itemId,
        'QUANTITY' => $quantity,
    );
    
    if (\CSaleBasket::Add($arFields)) {
        // Получаем количество товаров в корзине
        $cartCount = \CSaleBasket::GetList(
            array(),
            array('FUSER_ID' => \CSaleBasket::GetBasketUserID(), 'ORDER_ID' => false),
            false,
            false,
            array('ID', 'QUANTITY')
        )->SelectedRowsCount();
        
        return [
            'success' => true, 
            'message' => 'Товар добавлен в корзину',
            'cartCount' => $cartCount
        ];
    }
    
    return ['success' => false, 'message' => 'Ошибка добавления в корзину'];
}

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_after.php");
?>
