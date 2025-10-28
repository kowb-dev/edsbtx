<?php
/**
 * AJAX handler for adding products to the cart.
 * Version: 2.1.0 (production)
 * Author: KW (w/ Gemini assistance)
 * URI: https://kowb.ru
 */

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', 'Y');
define('NO_AGENT_CHECK', true);
define('NOT_CHECK_PERMISSIONS', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Loader;
use Bitrix\Main\Web\Json;

header('Content-Type: application/json');

function sendCartResponse($success, $message, $data = []) {
    echo Json::encode([
        'success' => $success,
        'message' => $message,
        'data' => $data
    ]);
    exit();
}

try {
    if (!Loader::includeModule('sale') || !Loader::includeModule('catalog')) {
        sendCartResponse(false, 'Required modules could not be loaded.');
    }

    $request = Application::getInstance()->getContext()->getRequest();
    if (!$request->isPost()) {
        sendCartResponse(false, 'Invalid request method.');
    }

    $input = Json::decode(file_get_contents('php://input'));

    $productId = isset($input['productId']) ? (int)$input['productId'] : 0;
    $quantity = isset($input['quantity']) ? (int)$input['quantity'] : 1;

    if ($productId <= 0) {
        sendCartResponse(false, 'Invalid Product ID.');
    }

    $result = \Bitrix\Catalog\Product\Basket::addProduct([
        'PRODUCT_ID' => $productId,
        'QUANTITY' => $quantity,
    ]);

    if ($result->isSuccess()) {
        sendCartResponse(true, 'Product added to cart.');
    } else {
        $errors = $result->getErrorMessages();
        sendCartResponse(false, 'Failed to add product to cart.', ['errors' => $errors]);
    }

} catch (\Exception $e) {
    // In production, it's good practice to log this error to a file.
    // error_log('Add to cart exception: ' . $e->getMessage());
    sendCartResponse(false, 'An unexpected error occurred.');
}
