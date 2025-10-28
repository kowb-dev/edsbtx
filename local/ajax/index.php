<?php
/**
 * Single AJAX Endpoint
 * Version: 1.0.0
 * Date: 2025-07-18
 * Description: Единая точка входа для всех AJAX запросов
 * File: /ajax/index.php
 */

define("B_PROLOG_INCLUDED", true);
define("NO_KEEP_STATISTIC", true);
define("NOT_CHECK_PERMISSIONS", true);

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Security headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// CORS headers if needed
if (isset($_SERVER['HTTP_ORIGIN'])) {
    $allowedOrigins = [
        'https://edsy.ru',
        'https://www.edsy.ru',
        'https://btx.edsy.ru'
    ];

    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
        header('Access-Control-Allow-Credentials: true');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
    }
}

// Handle OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Route to appropriate handler
$handler = $_GET['handler'] ?? $_POST['handler'] ?? 'forms';

switch ($handler) {
    case 'forms':
        $handlerPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/forms_handler.php';
        break;

    case 'catalog':
        $handlerPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/catalog_handler.php';
        break;

    case 'user':
        $handlerPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/user_handler.php';
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Unknown handler: ' . $handler,
            'timestamp' => date('Y-m-d H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
}

// Include handler if exists
if (file_exists($handlerPath)) {
    require_once $handlerPath;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Handler not found: ' . $handler,
        'path' => $handlerPath,
        'timestamp' => date('Y-m-d H:i:s')
    ], JSON_UNESCAPED_UNICODE);
}

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_after.php");
?>