<?php
/**
 * Модификатор результатов компонента личного кабинета
 *
 * @version 1.0.1
 * @author KW https://kowb.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Sale\Order;

global $USER;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/');
}

$arResult['PATH_TO_PROFILE'] = '/personal/profile/';
$arResult['PATH_TO_ORDERS'] = '/personal/orders/';
$arResult['PATH_TO_BASKET'] = '/personal/favorites/';
$arResult['PATH_TO_CONTACT'] = '/personal/address/';
$arResult['PATH_TO_PRIVATE'] = '/personal/password/';
$arResult['PATH_TO_ORDER_DETAIL'] = '/personal/orders/?ID=#ID#';
$arResult['PATH_TO_ORDER_CANCEL'] = '/personal/orders/?ID=#ID#&cancel=Y';
$arResult['PATH_TO_PAYMENT'] = '/personal/orders/payment/';
$arResult['PATH_TO_PROFILE_DETAIL'] = '/personal/address/?ID=#ID#';

$curPage = $APPLICATION->GetCurPage();

if (strpos($curPage, '/personal/profile/') !== false) {
    $arResult['CURRENT_PAGE'] = 'profile';
} elseif (strpos($curPage, '/personal/orders/') !== false) {
    $arResult['CURRENT_PAGE'] = 'orders';
} elseif (strpos($curPage, '/personal/favorites/') !== false) {
    $arResult['CURRENT_PAGE'] = 'basket';
} elseif (strpos($curPage, '/personal/address/') !== false) {
    $arResult['CURRENT_PAGE'] = 'address';
} elseif (strpos($curPage, '/personal/password/') !== false) {
    $arResult['CURRENT_PAGE'] = 'password';
} else {
    $arResult['CURRENT_PAGE'] = 'dashboard';
}

$arResult['USER_COMPANY'] = '';
$rsUser = CUser::GetByID($USER->GetID());
if ($arUser = $rsUser->Fetch()) {
    $arResult['USER_COMPANY'] = $arUser['WORK_COMPANY'] ?? '';
}

$arResult['ORDERS_COUNT'] = 0;
$arResult['FAVORITES_COUNT'] = 0;

if (Loader::includeModule('sale')) {
    try {
        $arFilter = array(
            'filter' => array(
                'USER_ID' => $USER->GetID(),
                '!STATUS_ID' => array('F', 'C')
            ),
            'select' => array('ID'),
            'count_total' => true
        );

        $dbOrders = Order::getList($arFilter);

        if (is_object($dbOrders)) {
            $arResult['ORDERS_COUNT'] = $dbOrders->getSelectedRowsCount();
        }
    } catch (Exception $e) {
        $arResult['ORDERS_COUNT'] = 0;
    }
}

if (Loader::includeModule('catalog')) {
    try {
        // Получаем количество товаров в избранном через отложенные товары
        $dbBasketItems = CSaleBasket::GetList(
            array(),
            array(
                "FUSER_ID" => CSaleBasket::GetBasketUserID(),
                "LID" => SITE_ID,
                "ORDER_ID" => "NULL",
                "DELAY" => "Y"
            ),
            false,
            false,
            array("ID")
        );

        $arResult['FAVORITES_COUNT'] = 0;
        while ($arItem = $dbBasketItems->Fetch()) {
            $arResult['FAVORITES_COUNT']++;
        }
    } catch (Exception $e) {
        $arResult['FAVORITES_COUNT'] = 0;
    }
}