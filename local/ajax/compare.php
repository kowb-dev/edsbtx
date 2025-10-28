<?php
/**
 * Compare AJAX Handler
 * Handles adding/removing products from comparison
 * 
 * @version 1.0.0
 * @author KW
 * @link https://kowb.ru
 */

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;

header('Content-Type: application/json; charset=UTF-8');

global $USER;

function sendJsonResponse($success, $data = [], $message = '')
{
    $response = [
        'success' => $success,
        'data' => $data,
        'message' => $message
    ];
    
    echo Json::encode($response);
    die();
}

if (!$USER->IsAuthorized()) {
    sendJsonResponse(false, [], 'Необходима авторизация');
}

$request = Context::getCurrent()->getRequest();
$rawInput = file_get_contents('php://input');
$input = Json::decode($rawInput);

if (empty($input)) {
    sendJsonResponse(false, [], 'Некорректные данные');
}

$action = $input['action'] ?? '';
$productId = (int)($input['productId'] ?? 0);

if ($action !== 'toggle' || $productId <= 0) {
    sendJsonResponse(false, [], 'Некорректный запрос');
}

$userId = $USER->GetID();

$compareProducts = [];
if (isset($_SESSION['COMPARE_PRODUCTS'])) {
    $compareProducts = $_SESSION['COMPARE_PRODUCTS'];
}

$inCompare = in_array($productId, $compareProducts);

if ($inCompare) {
    $compareProducts = array_diff($compareProducts, [$productId]);
    $compareProducts = array_values($compareProducts);
    $inCompare = false;
} else {
    $compareProducts[] = $productId;
    $inCompare = true;
}

$_SESSION['COMPARE_PRODUCTS'] = $compareProducts;

sendJsonResponse(true, [
    'inCompare' => $inCompare,
    'count' => count($compareProducts),
    'products' => $compareProducts
], $inCompare ? 'Товар добавлен к сравнению' : 'Товар удален из сравнения');