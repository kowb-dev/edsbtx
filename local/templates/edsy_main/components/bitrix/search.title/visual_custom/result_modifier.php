<?php
/**
 * Search Title Result Modifier
 * Enhanced with article search support
 * 
 * @version 2.0.0
 * @author KW
 * @link https://kowb.ru
 * Path: /local/templates/edsy_main/components/bitrix/search.title/visual_custom/result_modifier.php
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

$PREVIEW_WIDTH = intval($arParams['PREVIEW_WIDTH']);
if ($PREVIEW_WIDTH <= 0) {
    $PREVIEW_WIDTH = 75;
}

$PREVIEW_HEIGHT = intval($arParams['PREVIEW_HEIGHT']);
if ($PREVIEW_HEIGHT <= 0) {
    $PREVIEW_HEIGHT = 75;
}

$arParams['PRICE_VAT_INCLUDE'] = (!isset($arParams['PRICE_VAT_INCLUDE']) || $arParams['PRICE_VAT_INCLUDE'] !== 'N');

$arCatalogs = false;

$arResult['ELEMENTS'] = [];
$arResult['SEARCH'] = [];

if (CModule::IncludeModule('iblock') && !empty($arResult['QUERY'])) {
    $searchQuery = trim($arResult['QUERY']);
    
    $arArticleFilter = [
        'IBLOCK_ID' => 7,
        'ACTIVE' => 'Y',
        [
            'LOGIC' => 'OR',
            ['PROPERTY_CML2_ARTICLE' => '%' . $searchQuery . '%'],
            ['PROPERTY_ARTICLE' => '%' . $searchQuery . '%'],
            ['PROPERTY_ARTNUMBER' => '%' . $searchQuery . '%'],
        ]
    ];
    
    $rsArticleSearch = CIBlockElement::GetList(
        ['SORT' => 'ASC'],
        $arArticleFilter,
        false,
        ['nTopCount' => 10],
        ['ID', 'IBLOCK_ID', 'NAME', 'DETAIL_PAGE_URL']
    );
    
    while ($arArticleItem = $rsArticleSearch->Fetch()) {
        $foundInCategories = false;
        foreach ($arResult['CATEGORIES'] as $cat) {
            foreach ($cat['ITEMS'] as $item) {
                if ($item['ITEM_ID'] == $arArticleItem['ID']) {
                    $foundInCategories = true;
                    break 2;
                }
            }
        }
        
        if (!$foundInCategories) {
            if (!isset($arResult['CATEGORIES']['iblock'])) {
                $arResult['CATEGORIES']['iblock'] = [
                    'TITLE' => 'Товары',
                    'ITEMS' => []
                ];
            }
            
            $arResult['CATEGORIES']['iblock']['ITEMS'][] = [
                'MODULE_ID' => 'iblock',
                'ITEM_ID' => $arArticleItem['ID'],
                'PARAM1' => '',
                'PARAM2' => $arArticleItem['IBLOCK_ID'],
                'NAME' => $arArticleItem['NAME'],
                'URL' => $arArticleItem['DETAIL_PAGE_URL']
            ];
            
            $arResult['ELEMENTS'][$arArticleItem['ID']] = $arArticleItem['ID'];
        }
    }
}

foreach ($arResult['CATEGORIES'] as $category_id => $arCategory) {
    foreach ($arCategory['ITEMS'] as $i => $arItem) {
        if (isset($arItem['ITEM_ID'])) {
            $arResult['SEARCH'][] = &$arResult['CATEGORIES'][$category_id]['ITEMS'][$i];
            if (
                $arItem['MODULE_ID'] == 'iblock'
                && mb_substr($arItem['ITEM_ID'], 0, 1) !== 'S'
            ) {
                if ($arCatalogs === false) {
                    $arCatalogs = [];
                    if (CModule::IncludeModule('catalog')) {
                        $rsCatalog = CCatalog::GetList([
                            'sort' => 'asc',
                        ]);
                        while ($ar = $rsCatalog->Fetch()) {
                            if ($ar['PRODUCT_IBLOCK_ID']) {
                                $arCatalogs[$ar['PRODUCT_IBLOCK_ID']] = 1;
                            } else {
                                $arCatalogs[$ar['IBLOCK_ID']] = 1;
                            }
                        }
                    }
                }

                if (array_key_exists($arItem['PARAM2'], $arCatalogs)) {
                    $arResult['ELEMENTS'][$arItem['ITEM_ID']] = $arItem['ITEM_ID'];
                }
            }
        }
    }
}

if (!empty($arResult['ELEMENTS']) && CModule::IncludeModule('iblock')) {
    $arConvertParams = [];
    if ('Y' == $arParams['CONVERT_CURRENCY']) {
        if (!CModule::IncludeModule('currency')) {
            $arParams['CONVERT_CURRENCY'] = 'N';
            $arParams['CURRENCY_ID'] = '';
        } else {
            $arCurrencyInfo = CCurrency::GetByID($arParams['CURRENCY_ID']);
            if (!(is_array($arCurrencyInfo) && !empty($arCurrencyInfo))) {
                $arParams['CONVERT_CURRENCY'] = 'N';
                $arParams['CURRENCY_ID'] = '';
            } else {
                $arParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
                $arConvertParams['CURRENCY_ID'] = $arCurrencyInfo['CURRENCY'];
            }
        }
    }

    $obParser = new CTextParser;

    if (is_array($arParams['PRICE_CODE'])) {
        $arResult['PRICES'] = CIBlockPriceTools::GetCatalogPrices(0, $arParams['PRICE_CODE']);
    } else {
        $arResult['PRICES'] = [];
    }

    $arSelect = [
        'ID',
        'IBLOCK_ID',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
    ];
    $arFilter = [
        'IBLOCK_LID' => SITE_ID,
        'IBLOCK_ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
        'ACTIVE' => 'Y',
        'CHECK_PERMISSIONS' => 'Y',
        'MIN_PERMISSION' => 'R',
    ];
    foreach ($arResult['PRICES'] as $value) {
        $arSelect[] = $value['SELECT'];
        $arFilter['CATALOG_SHOP_QUANTITY_' . $value['ID']] = 1;
    }
    $arFilter['=ID'] = $arResult['ELEMENTS'];
    $arResult['ELEMENTS'] = [];
    $rsElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($arElement = $rsElements->Fetch()) {
        $arElement['PRICES'] = CIBlockPriceTools::GetItemPrices($arElement['IBLOCK_ID'], $arResult['PRICES'], $arElement, $arParams['PRICE_VAT_INCLUDE'], $arConvertParams);
        if ($arParams['PREVIEW_TRUNCATE_LEN'] > 0) {
            $arElement['PREVIEW_TEXT'] = $obParser->html_cut($arElement['PREVIEW_TEXT'], $arParams['PREVIEW_TRUNCATE_LEN']);
        }

        $arResult['ELEMENTS'][$arElement['ID']] = $arElement;
    }
}

foreach ($arResult['SEARCH'] as $i => $arItem) {
    switch ($arItem['MODULE_ID']) {
        case 'iblock':
            if (array_key_exists($arItem['ITEM_ID'], $arResult['ELEMENTS'])) {
                $arElement = &$arResult['ELEMENTS'][$arItem['ITEM_ID']];

                if ($arParams['SHOW_PREVIEW'] == 'Y') {
                    if ($arElement['PREVIEW_PICTURE'] > 0) {
                        $arElement['PICTURE'] = CFile::ResizeImageGet($arElement['PREVIEW_PICTURE'], ['width' => $PREVIEW_WIDTH, 'height' => $PREVIEW_HEIGHT], BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    } elseif ($arElement['DETAIL_PICTURE'] > 0) {
                        $arElement['PICTURE'] = CFile::ResizeImageGet($arElement['DETAIL_PICTURE'], ['width' => $PREVIEW_WIDTH, 'height' => $PREVIEW_HEIGHT], BX_RESIZE_IMAGE_PROPORTIONAL, true);
                    }
                }
            }
            break;
    }

    $arResult['SEARCH'][$i]['ICON'] = true;
}
