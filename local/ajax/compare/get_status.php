<?php
/**
 * AJAX handler for getting compare status
 * 
 * @version 1.0.0
 * @author KW
 * @uri https://kowb.ru
 * 
 * Location: /local/ajax/compare/get_status.php
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

// Initialize session compare array
if (!isset($_SESSION['COMPARE_LIST'])) {
    $_SESSION['COMPARE_LIST'] = [];
}

// Get product ID if provided
$productId = isset($_GET['productId']) ? intval($_GET['productId']) : 0;

$response = [
    'success' => true,
    'compareCount' => count($_SESSION['COMPARE_LIST']),
    'compareList' => $_SESSION['COMPARE_LIST']
];

// Check if specific product is in compare
if ($productId > 0) {
    $response['inCompare'] = in_array($productId, $_SESSION['COMPARE_LIST']);
    $response['productId'] = $productId;
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);