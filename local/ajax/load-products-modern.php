<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;

// Проверяем AJAX запрос
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    die();
}

// Загружаем модули
if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Required modules not found']);
    die();
}

try {
    // Получаем параметры
    $page = max(1, (int)($_GET['page'] ?? 1));
    $perPage = min(100, max(1, (int)($_GET['per_page'] ?? 20)));
    $sectionId = (int)($_GET['section_id'] ?? 0);
    $sort = $_GET['sort'] ?? 'name_asc';

    // Парсим сортировку
    list($sortField, $sortOrder) = explode('_', $sort);
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

    // Базовый фильтр
    $arFilter = [
        'IBLOCK_ID' => 2, // ID инфоблока каталога
        'ACTIVE' => 'Y'
    ];

    if ($sectionId > 0) {
        $arFilter['SECTION_ID'] = $sectionId;
        $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
    }

    // Применяем фильтры
    $filterInput = $_GET['filter_input'] ?? [];
    $filterAdditional = $_GET['filter_additional'] ?? [];
    $priceFrom = (float)($_GET['price_from'] ?? 0);
    $priceTo = (float)($_GET['price_to'] ?? 0);

    if (!empty($filterInput)) {
        $arFilter['PROPERTY_INPUT_TYPE'] = $filterInput;
    }

    if (!empty($filterAdditional)) {
        $arFilter['PROPERTY_ADDITIONAL'] = $filterAdditional;
    }

    if ($priceFrom > 0) {
        $arFilter['>=CATALOG_PRICE_1'] = $priceFrom;
    }

    if ($priceTo > 0) {
        $arFilter['<=CATALOG_PRICE_1'] = $priceTo;
    }

    // Параметры выборки
    $arSelect = [
        'ID',
        'NAME',
        'PREVIEW_TEXT',
        'PREVIEW_PICTURE',
        'DETAIL_PICTURE',
        'DETAIL_PAGE_URL',
        'PROPERTY_ARTICLE',
        'PROPERTY_INPUT_TYPE',
        'PROPERTY_ADDITIONAL'
    ];

    // Навигация
    $navParams = [
        'nPageSize' => $perPage,
        'iNumPage' => $page,
        'bShowAll' => false
    ];

    // Получаем общее количество для подсчета
    $totalCount = CIBlockElement::GetList(
        [],
        $arFilter,
        [],
        false,
        ['ID']
    )->SelectedRowsCount();

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

        // Формируем спецификации
        $specs = [];
        if ($arElement['PROPERTY_INPUT_TYPE_VALUE']) {
            $specs[] = 'Ввод: ' . $arElement['PROPERTY_INPUT_TYPE_VALUE'];
        }
        if ($arElement['PROPERTY_ADDITIONAL_VALUE']) {
            if (is_array($arElement['PROPERTY_ADDITIONAL_VALUE'])) {
                $specs = array_merge($specs, $arElement['PROPERTY_ADDITIONAL_VALUE']);
            } else {
                $specs[] = $arElement['PROPERTY_ADDITIONAL_VALUE'];
            }
        }

        $products[] = [
            'id' => $arElement['ID'],
            'name' => $arElement['NAME'],
            'description' => $arElement['PREVIEW_TEXT'],
            'image' => $imageUrl,
            'detail_url' => $arElement['DETAIL_PAGE_URL'],
            'article' => $arElement['PROPERTY_ARTICLE_VALUE'],
            'user_price' => $prices['user'] ?? '',
            'retail_price' => $prices['retail'] ?? '',
            'available' => $canBuy,
            'specs' => implode('; ', $specs)
        ];
    }

    echo json_encode([
        'success' => true,
        'products' => $products,
        'total_count' => $totalCount,
        'current_page' => $page,
        'per_page' => $perPage,
        'has_more' => count($products) === $perPage
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка сервера',
        'debug' => SITE_ID === 's1' ? $e->getMessage() : null
    ]);
}
?>
