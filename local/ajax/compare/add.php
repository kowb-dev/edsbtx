<?php
/**
 * AJAX handler for adding/removing products to compare
 * 
 * @version 1.0.0
 * @author KW
 * @uri https://kowb.ru
 * 
 * Location: /local/ajax/compare/add.php
 */

define('STOP_STATISTICS', true);
define('NO_AGENT_CHECK', true);
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

// Security check
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die('Access denied');
}

// Set JSON header
header('Content-Type: application/json; charset=utf-8');

// Function to send JSON response
function sendJsonResponse($success, $message = '', $data = []) {
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ], JSON_UNESCAPED_UNICODE);
    die();
}

// Check request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJsonResponse(false, 'Неверный метод запроса');
}

// Check AJAX request
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || 
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') {
    sendJsonResponse(false, 'Доступ запрещён');
}

// Check session ID
if (!check_bitrix_sessid()) {
    sendJsonResponse(false, 'Ошибка проверки сессии');
}

// Get product ID
$productId = isset($_POST['productId']) ? intval($_POST['productId']) : 0;

if ($productId <= 0) {
    sendJsonResponse(false, 'Некорректный ID товара');
}

// Load required modules
if (!CModule::IncludeModule('iblock') || !CModule::IncludeModule('catalog')) {
    sendJsonResponse(false, 'Ошибка загрузки модулей');
}

// Initialize session compare array
if (!isset($_SESSION['COMPARE_LIST'])) {
    $_SESSION['COMPARE_LIST'] = [];
}

// Check if product exists
$arProduct = CIBlockElement::GetByID($productId)->GetNext();

if (!$arProduct) {
    sendJsonResponse(false, 'Товар не найден');
}

// Get product details for response
$productName = $arProduct['NAME'];
$iblockId = $arProduct['IBLOCK_ID'];

// Check if product is already in compare
$index = array_search($productId, $_SESSION['COMPARE_LIST']);

if ($index !== false) {
    // Remove from compare
    unset($_SESSION['COMPARE_LIST'][$index]);
    $_SESSION['COMPARE_LIST'] = array_values($_SESSION['COMPARE_LIST']); // Re-index array
    
    sendJsonResponse(
        true,
        'Товар удалён из сравнения',
        [
            'action' => 'removed',
            'productId' => $productId,
            'productName' => $productName,
            'compareCount' => count($_SESSION['COMPARE_LIST']),
            'inCompare' => false
        ]
    );
} else {
    // Add to compare
    $_SESSION['COMPARE_LIST'][] = $productId;
    
    sendJsonResponse(
        true,
        'Товар добавлен в сравнение',
        [
            'action' => 'added',
            'productId' => $productId,
            'productName' => $productName,
            'compareCount' => count($_SESSION['COMPARE_LIST']),
            'inCompare' => true
        ]
    );
}