<?php
/**
 * Bitrix Cart Result Modifier - edsys_cart
 * 
 * @version 1.0.7
 * @date 2025-10-25
 * @description Fixed property filtering and encoding issues
 * @author KW
 * @uri https://kowb.ru
 * 
 * @var array $arParams Component parameters
 * @var array $arResult Component result data
 * @var CBitrixComponentTemplate $this Template instance
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Sale;
use Bitrix\Currency;

if (!Loader::includeModule('sale')) {
    return;
}

Loader::includeModule('currency');

$basket = Sale\Basket::loadItemsForFUser(
    Sale\Fuser::getId(),
    \Bitrix\Main\Context::getCurrent()->getSite()
);

$arResult['ITEMS'] = [];
$arResult['TOTAL_PRICE'] = 0;
$arResult['TOTAL_DISCOUNT'] = 0;
$arResult['CURRENCY'] = 'RUB';

if ($basket && !$basket->isEmpty()) {
    $basketItems = $basket->getBasketItems();
    
    foreach ($basketItems as $basketItem) {
        if ($basketItem->getField('DELAY') === 'Y' || $basketItem->getField('CAN_BUY') !== 'Y') {
            continue;
        }
        
        $productId = $basketItem->getProductId();
        $quantity = $basketItem->getQuantity();
        $price = $basketItem->getPrice();
        $basePrice = $basketItem->getBasePrice();
        $currency = $basketItem->getCurrency();
        
        $discount = ($basePrice - $price) * $quantity;
        
        $arProduct = [];
        $articleValue = '';
        
        if ($productId > 0) {
            $rsElement = CIBlockElement::GetByID($productId);
            if ($arElement = $rsElement->GetNext()) {
                $arProduct = $arElement;
                
                if ($arElement['PREVIEW_PICTURE']) {
                    $arProduct['PREVIEW_PICTURE'] = CFile::GetFileArray($arElement['PREVIEW_PICTURE']);
                }
                
                if (!$arProduct['PREVIEW_PICTURE'] && $arElement['DETAIL_PICTURE']) {
                    $arProduct['DETAIL_PICTURE'] = CFile::GetFileArray($arElement['DETAIL_PICTURE']);
                }
                
                $rsProps = CIBlockElement::GetProperty(
                    $arElement['IBLOCK_ID'],
                    $productId,
                    [],
                    ['CODE' => 'CML2_ARTICLE']
                );
                
                if ($arProp = $rsProps->Fetch()) {
                    $articleValue = $arProp['VALUE'];
                }
            }
        }
        
        $arProps = [];
        $basketPropertyCollection = $basketItem->getPropertyCollection();
        if ($basketPropertyCollection) {
            $excludedCodes = ['CATALOG_XML_ID', 'PRODUCT_XML_ID', 'CATALOG.XML_ID', 'PRODUCT.XML_ID'];
            
            foreach ($basketPropertyCollection as $property) {
                $propCode = $property->getField('CODE');
                $propValue = $property->getField('VALUE');
                
                if (!$propCode || !$propValue) {
                    continue;
                }
                
                if (in_array($propCode, $excludedCodes)) {
                    continue;
                }
                
                $propName = $property->getField('NAME');
                
                if (stripos($propName, 'XML') !== false || stripos($propName, 'xml_id') !== false) {
                    continue;
                }
                
                $arProps[] = [
                    'NAME' => $propName,
                    'VALUE' => $propValue,
                    'CODE' => $propCode
                ];
            }
        }
        
        if (!empty($articleValue)) {
            array_unshift($arProps, [
                'NAME' => 'Артикул',
                'VALUE' => $articleValue,
                'CODE' => 'ARTICLE'
            ]);
        }
        
        $arItem = [
            'ID' => $basketItem->getId(),
            'PRODUCT_ID' => $productId,
            'NAME' => $basketItem->getField('NAME'),
            'QUANTITY' => $quantity,
            'PRICE' => $price,
            'BASE_PRICE' => $basePrice,
            'SUM' => $price * $quantity,
            'BASE_SUM' => $basePrice * $quantity,
            'DISCOUNT_PRICE' => $basePrice - $price,
            'CURRENCY' => $currency,
            'DETAIL_PAGE_URL' => $arProduct['DETAIL_PAGE_URL'] ?? '',
            'PREVIEW_PICTURE' => $arProduct['PREVIEW_PICTURE'] ?? null,
            'DETAIL_PICTURE' => $arProduct['DETAIL_PICTURE'] ?? null,
            'PROPS' => $arProps,
            'CAN_BUY' => $basketItem->getField('CAN_BUY') === 'Y',
            'AVAILABLE' => $basketItem->getField('AVAILABLE') === 'Y',
        ];
        
        $arResult['ITEMS'][] = $arItem;
        $arResult['TOTAL_PRICE'] += $arItem['SUM'];
        $arResult['TOTAL_DISCOUNT'] += $discount;
        $arResult['CURRENCY'] = $currency;
    }
}

$arResult['ITEMS_COUNT'] = count($arResult['ITEMS']);
$arResult['BASE_TOTAL'] = $arResult['TOTAL_PRICE'] + $arResult['TOTAL_DISCOUNT'];
$arResult['DISCOUNT_PRICE'] = $arResult['TOTAL_DISCOUNT'];

if ($arResult['TOTAL_PRICE'] > 0) {
    $arResult['TOTAL_PRICE_FORMATED'] = number_format($arResult['TOTAL_PRICE'], 2, '.', ' ') . ' ₽';
    
    $arResult['BASE_TOTAL_FORMATED'] = number_format($arResult['BASE_TOTAL'], 2, '.', ' ') . ' ₽';
    
    if ($arResult['DISCOUNT_PRICE'] > 0) {
        $arResult['DISCOUNT_PRICE_FORMATED'] = number_format($arResult['DISCOUNT_PRICE'], 2, '.', ' ') . ' ₽';
    }
}

$arResult['BASKET_OBJECT'] = $basket;
$this->__component->arResult = $arResult;