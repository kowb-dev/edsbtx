<?php
/**
 * AJAX handler for clearing all compare items - FIXED VERSION
 * 
 * @version 1.1.0
 * @author KW
 * @uri https://kowb.ru
 * 
 * Location: /local/ajax/compare/clear.php
 * 
 * Changelog:
 * - 1.1.0: Fixed session handling and validation
 * - 1.0.0: Initial version
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

// Get sessid from POST or REQUEST
$sessid = isset($_POST['sessid']) ? $_POST['sessid'] : (isset($_REQUEST['sessid']) ? $_REQUEST['sessid'] : '');

// Check session ID
if (!check_bitrix_sessid($sessid)) {
    sendJsonResponse(false, 'Ошибка проверки сессии');
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear compare list
$_SESSION['COMPARE_LIST'] = [];

sendJsonResponse(
    true,
    'Список сравнения очищен',
    [
        'compareCount' => 0,
        'compareList' => []
    ]
);