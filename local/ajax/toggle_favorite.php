<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $productId = (int)($input['PRODUCT_ID'] ?? 0);
    $action = $input['ACTION'] ?? 'add';

    if ($productId <= 0) {
        throw new Exception('Некорректный ID товара');
    }

    $favorites = $_SESSION['CATALOG_FAVORITES'] ?? [];

    if ($action === 'add') {
        $favorites[$productId] = $productId;
        $message = 'Товар добавлен в избранное';
    } else {
        unset($favorites[$productId]);
        $message = 'Товар удален из избранного';
    }

    $_SESSION['CATALOG_FAVORITES'] = $favorites;

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'count' => count($favorites)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
