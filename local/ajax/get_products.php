<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Iblock\ElementTable;

// Проверяем AJAX запрос
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    die('Access denied');
}

// Загружаем модули
if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Required modules not found']);
    die();
}

try {
    // Получаем параметры
    $page = (int)($_GET['page'] ?? 1);
    $pageSize = (int)($_GET['pageSize'] ?? 20);
    $sectionId = (int)($_GET['sectionId'] ?? 0);
    $sort = $_GET['sort'] ?? 'name-asc';

    // Максимальные ограничения безопасности
    $page = max(1, min($page, 1000));
    $pageSize = max(1, min($pageSize, 100));

    // Парсим сортировку
    list($sortField, $sortOrder) = explode('-', $sort);
    $sortField = in_array($sortField, ['name', 'price', 'date', 'popularity']) ? $sortField : 'name';
    $sortOrder = $sortOrder === 'desc' ? 'desc' : 'asc';

    // Маппинг полей сортировки
    $sortFieldMap = [
        'name' => 'NAME',
        'price' => 'CATALOG_PRICE_1',
        'date' => 'DATE_CREATE',
        'popularity' => 'SHOW_COUNTER'
    ];

    $arSort = [$sortFieldMap[$sortField] => strtoupper($sortOrder)];

    // Фильтр
    $arFilter = [
        'IBLOCK_ID' => 2, // ID инфоблока каталога
        'ACTIVE' => 'Y'
    ];

    if ($sectionId > 0) {
        $arFilter['SECTION_ID'] = $sectionId;
        $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
    }

    // Добавляем фильтры из smart filter (если есть)
    foreach ($_GET as $key => $value) {
        if (strpos($key, 'arrFilter_') === 0 && !empty($value)) {
            $filterKey = str_replace('arrFilter_', '', $key);
            $arFilter[$filterKey] = $value;
        }
    }

    // Параметры выборки
    $arSelect = [
        'ID',
        'NAME',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PAGE_URL',
        'PROPERTY_ARTICLE'
    ];

    // Навигация
    $navParams = [
        'nPageSize' => $pageSize,
        'iNumPage' => $page,
        'bShowAll' => false
    ];

    // Получаем элементы
    $rsElements = CIBlockElement::GetList(
        $arSort,
        $arFilter,
        false,
        $navParams,
        $arSelect
    );

    $products = [];
    $isUserAuthorized = $USER->IsAuthorized();

    while ($arElement = $rsElements->GetNext()) {
        // Получаем изображение
        $imageUrl = '';
        if ($arElement['PREVIEW_PICTURE']) {
            $imageUrl = CFile::GetPath($arElement['PREVIEW_PICTURE']);
        } elseif ($arElement['DETAIL_PICTURE']) {
            $imageUrl = CFile::GetPath($arElement['DETAIL_PICTURE']);
        }

        // Получаем цены (только для авторизованных)
        $prices = [];
        if ($isUserAuthorized) {
            $priceResult = CPrice::GetList(
                [],
                [
                    'PRODUCT_ID' => $arElement['ID'],
                    'CATALOG_GROUP_ID' => [1, 2] // BASE и RETAIL
                ]
            );

            while ($arPrice = $priceResult->Fetch()) {
                if ($arPrice['CATALOG_GROUP_ID'] == 1) {
                    $prices['user'] = CurrencyFormat($arPrice['PRICE'], $arPrice['CURRENCY']);
                    $prices['user_value'] = $arPrice['PRICE'];
                } elseif ($arPrice['CATALOG_GROUP_ID'] == 2) {
                    $prices['retail'] = CurrencyFormat($arPrice['PRICE'], $arPrice['CURRENCY']);
                }
            }
        }

        // Проверяем доступность для покупки
        $canBuy = false;
        if (Loader::includeModule('catalog')) {
            $productInfo = CCatalogProduct::GetByID($arElement['ID']);
            $canBuy = $productInfo && $productInfo['QUANTITY'] > 0;
        }

        $products[] = [
            'ID' => $arElement['ID'],
            'NAME' => $arElement['NAME'],
            'PREVIEW_TEXT' => $arElement['PREVIEW_TEXT'],
            'PREVIEW_PICTURE' => $imageUrl,
            'DETAIL_PAGE_URL' => $arElement['DETAIL_PAGE_URL'],
            'ARTICLE' => $arElement['PROPERTY_ARTICLE_VALUE'],
            'USER_PRICE' => $prices['user'] ?? '',
            'RETAIL_PRICE' => $prices['retail'] ?? '',
            'PRICE_VALUE' => $prices['user_value'] ?? 0,
            'CURRENCY' => 'RUB',
            'CAN_BUY' => $canBuy
        ];
    }

    echo json_encode([
        'status' => 'success',
        'products' => $products,
        'pagination' => [
            'current_page' => $page,
            'total_count' => $rsElements->NavRecordCount,
            'page_size' => $pageSize
        ]
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'status' => 'error',
        'message' => 'Internal server error',
        'debug' => SITE_ID === 's1' ? $e->getMessage() : null // Показываем детали только на dev
    ]);
}
?>
