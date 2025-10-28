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

    $compare = $_SESSION['CATALOG_COMPARE_LIST'][2]['ITEMS'] ?? [];

    if ($action === 'add') {
        $compare[$productId] = $productId;
        $message = 'Товар добавлен в сравнение';
    } else {
        unset($compare[$productId]);
        $message = 'Товар удален из сравнения';
    }

    $_SESSION['CATALOG_COMPARE_LIST'][2]['ITEMS'] = $compare;

    echo json_encode([
        'status' => 'success',
        'message' => $message,
        'count' => count($compare)
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>
