<?php
/**
 * Bitrix Cart AJAX Handler - edsys_cart
 * 
 * @version 1.0.4
 * @date 2025-10-25
 * @description Fixed delete handling and item existence checks
 * @author KW
 * @uri https://kowb.ru
 */

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('DisableEventsCheck', true);

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Sale;

header('Content-Type: application/json; charset=utf-8');

if (!Loader::includeModule('sale')) {
    echo json_encode(['success' => false, 'error' => 'Sale module not loaded'], JSON_UNESCAPED_UNICODE);
    die();
}

$request = Context::getCurrent()->getRequest();
$action = $request->get('action');

if (!check_bitrix_sessid()) {
    echo json_encode(['success' => false, 'error' => 'Session expired'], JSON_UNESCAPED_UNICODE);
    die();
}

$basket = Sale\Basket::loadItemsForFUser(
    Sale\Fuser::getId(),
    Context::getCurrent()->getSite()
);

$response = ['success' => false];

try {
    switch ($action) {
        case 'updateQuantity':
            $itemId = intval($request->get('itemId'));
            $quantity = floatval($request->get('quantity'));
            
            if ($itemId > 0 && $quantity > 0) {
                $basketItem = $basket->getItemById($itemId);
                
                if ($basketItem) {
                    $basketItem->setField('QUANTITY', $quantity);
                    $basket->save();
                    
                    $price = $basketItem->getPrice();
                    $sum = $price * $quantity;
                    $currency = $basketItem->getCurrency();
                    
                    $response = [
                        'success' => true,
                        'itemId' => $itemId,
                        'quantity' => $quantity,
                        'price' => $price,
                        'sum' => $sum,
                        'sumFormatted' => number_format($sum, 2, '.', ' ') . ' ₽',
                        'total' => $basket->getPrice(),
                        'totalFormatted' => number_format($basket->getPrice(), 2, '.', ' ') . ' ₽',
                        'itemsCount' => count($basket->getBasketItems())
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'error' => 'Товар не найден в корзине'
                    ];
                }
            }
            break;
            
        case 'deleteItem':
            $itemId = intval($request->get('itemId'));
            
            if ($itemId > 0) {
                $basketItem = $basket->getItemById($itemId);
                
                if ($basketItem) {
                    $basketItem->delete();
                    $result = $basket->save();
                    
                    if ($result->isSuccess()) {
                        $response = [
                            'success' => true,
                            'itemId' => $itemId,
                            'total' => $basket->getPrice(),
                            'totalFormatted' => number_format($basket->getPrice(), 2, '.', ' ') . ' ₽',
                            'itemsCount' => count($basket->getBasketItems())
                        ];
                    } else {
                        $errors = $result->getErrorMessages();
                        $response = [
                            'success' => false,
                            'error' => !empty($errors) ? implode(', ', $errors) : 'Ошибка при сохранении корзины'
                        ];
                    }
                } else {
                    // Товар уже удален или не существует - это не ошибка
                    $response = [
                        'success' => true,
                        'itemId' => $itemId,
                        'total' => $basket->getPrice(),
                        'totalFormatted' => number_format($basket->getPrice(), 2, '.', ' ') . ' ₽',
                        'itemsCount' => count($basket->getBasketItems()),
                        'already_deleted' => true
                    ];
                }
            }
            break;
            
        case 'deleteMultiple':
            $itemIds = json_decode($request->get('itemIds'), true);
            
            if (is_array($itemIds) && !empty($itemIds)) {
                $deletedCount = 0;
                
                foreach ($itemIds as $itemId) {
                    $itemId = intval($itemId);
                    if ($itemId > 0) {
                        $basketItem = $basket->getItemById($itemId);
                        if ($basketItem) {
                            $basketItem->delete();
                            $deletedCount++;
                        }
                    }
                }
                
                if ($deletedCount > 0) {
                    $result = $basket->save();
                    
                    if ($result->isSuccess()) {
                        $response = [
                            'success' => true,
                            'deletedIds' => $itemIds,
                            'deletedCount' => $deletedCount,
                            'total' => $basket->getPrice(),
                            'totalFormatted' => number_format($basket->getPrice(), 2, '.', ' ') . ' ₽',
                            'itemsCount' => count($basket->getBasketItems())
                        ];
                    } else {
                        $errors = $result->getErrorMessages();
                        $response = [
                            'success' => false,
                            'error' => !empty($errors) ? implode(', ', $errors) : 'Ошибка при сохранении корзины'
                        ];
                    }
                } else {
                    // Все товары уже были удалены
                    $response = [
                        'success' => true,
                        'deletedIds' => $itemIds,
                        'deletedCount' => 0,
                        'total' => $basket->getPrice(),
                        'totalFormatted' => number_format($basket->getPrice(), 2, '.', ' ') . ' ₽',
                        'itemsCount' => count($basket->getBasketItems()),
                        'already_deleted' => true
                    ];
                }
            }
            break;
            
        case 'applyCoupon':
            $coupon = trim($request->get('coupon'));
            
            if (!empty($coupon)) {
                $order = Sale\Order::create(Context::getCurrent()->getSite(), Sale\Fuser::getId());
                $order->setBasket($basket);
                
                $couponResult = Sale\DiscountCouponsManager::add($coupon);
                
                if ($couponResult) {
                    $order->doFinalAction(true);
                    
                    $response = [
                        'success' => true,
                        'coupon' => $coupon,
                        'discount' => $order->getDiscountPrice(),
                        'total' => $order->getPrice(),
                        'totalFormatted' => number_format($order->getPrice(), 2, '.', ' ') . ' ₽'
                    ];
                } else {
                    $response = [
                        'success' => false,
                        'error' => 'Неверный промокод'
                    ];
                }
            }
            break;
            
        default:
            $response = ['success' => false, 'error' => 'Unknown action'];
    }
    
} catch (Exception $e) {
    $response = [
        'success' => false,
        'error' => $e->getMessage()
    ];
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);