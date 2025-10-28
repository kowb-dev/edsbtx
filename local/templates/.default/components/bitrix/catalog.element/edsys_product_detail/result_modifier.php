<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Loader;
use Bitrix\Main\Application;
use Bitrix\Catalog\Product\Price;

/**
 * Модификатор результата для шаблона карточки товара EDS
 * Обрабатывает данные товара перед выводом в шаблоне
 */

// Проверка подключения модулей
if (!Loader::includeModule("catalog") || !Loader::includeModule("sale")) {
	ShowError("Необходимы модули Каталог и Интернет-магазин");
	return;
}

global $USER;

// === Проверка, в избранном ли товар ===
$arResult['IS_FAVORITE'] = false;
if ($USER->IsAuthorized()) {
    $rsUser = CUser::GetByID($USER->GetID())->Fetch();
    $favoriteIds = $rsUser['UF_FAVORITES'] ?? [];
    if (!is_array($favoriteIds)) {
        $favoriteIds = [];
    }
    if (in_array($arResult['ID'], $favoriteIds)) {
        $arResult['IS_FAVORITE'] = true;
    }
}

// Получение данных о товаре
$elementId = $arResult['ID'];
$arItem = &$arResult['ITEM'];

// === Обработка изображений товара ===
$arResult['IMAGES'] = [];
if (!empty($arResult['DETAIL_PICTURE'])) {
	$arResult['IMAGES'][] = [
		'ID' => $arResult['DETAIL_PICTURE']['ID'],
		'SRC' => $arResult['DETAIL_PICTURE']['SRC'],
		'ALT' => $arResult['DETAIL_PICTURE']['ALT'] ?: $arResult['NAME'],
		'TITLE' => $arResult['DETAIL_PICTURE']['TITLE'] ?: $arResult['NAME']
	];
}

// Добавление дополнительных изображений из свойств
if (!empty($arResult['MORE_PHOTO'])) {
	foreach ($arResult['MORE_PHOTO'] as $photo) {
		$arResult['IMAGES'][] = [
			'ID' => $photo['ID'],
			'SRC' => $photo['SRC'],
			'ALT' => $photo['ALT'] ?: $arResult['NAME'],
			'TITLE' => $photo['TITLE'] ?: $arResult['NAME']
		];
	}
}

// === Обработка цен ===
$arResult['PRICES_PROCESSED'] = [];

if ($USER->IsAuthorized()) {
    $userGroups = $USER->GetUserGroupArray();
    
    // Используем GetOptimalPrice для получения цены
    $arPrice = CCatalogProduct::GetOptimalPrice($elementId, 1, $userGroups, "N", [], SITE_ID);

    if ($arPrice && isset($arPrice['RESULT_PRICE'])) {
        $retailPrice = $arPrice['RESULT_PRICE']['BASE_PRICE'];
        $userPrice = $arPrice['RESULT_PRICE']['DISCOUNT_PRICE'];
        $userDiscount = $arPrice['RESULT_PRICE']['PERCENT'];

        $arResult['PRICES_PROCESSED'] = [
            'RETAIL' => [
                'VALUE' => $retailPrice,
                'FORMATTED' => number_format($retailPrice, 0, ',', ' ') . ' ₽'
            ],
            'USER' => [
                'VALUE' => $userPrice,
                'FORMATTED' => number_format($userPrice, 0, ',', ' ') . ' ₽',
                'DISCOUNT_PERCENT' => $userDiscount
            ]
        ];
    }
}

// === Проверка наличия товара ===
$arResult['STOCK_STATUS'] = [
	'AVAILABLE' => true,
	'TEXT' => 'В наличии'
];

// Проверка количества на складах
if (Loader::includeModule("catalog")) {
	$rsStore = CCatalogStoreProduct::GetList(
		[],
		['PRODUCT_ID' => $elementId],
		false,
		false,
		['AMOUNT']
	);

	$totalAmount = 0;
	while ($arStore = $rsStore->Fetch()) {
		$totalAmount += floatval($arStore['AMOUNT']);
	}

	if ($totalAmount <= 0) {
		$arResult['STOCK_STATUS'] = [
			'AVAILABLE' => false,
			'TEXT' => 'Доступен предзаказ'
		];
	}
}

// === Обработка характеристик ===
$arResult['SPECIFICATIONS'] = [];

if (!empty($arResult['DISPLAY_PROPERTIES'])) {
	foreach ($arResult['DISPLAY_PROPERTIES'] as $code => $prop) {
		// Исключаем артикул из характеристик
		if ($code === 'ARTICLE' || $code === 'CML2_ARTICLE') {
			continue;
		}

		if (!empty($prop['DISPLAY_VALUE'])) {
			$arResult['SPECIFICATIONS'][] = [
				'NAME' => $prop['NAME'],
				'VALUE' => is_array($prop['DISPLAY_VALUE'])
					? implode(', ', $prop['DISPLAY_VALUE'])
					: $prop['DISPLAY_VALUE'],
				'CODE' => $code
			];
		}
	}
}

// Если нет свойств, добавляем базовые характеристики из полей элемента
if (empty($arResult['SPECIFICATIONS'])) {
	// Попробуем получить характеристики из других источников
	if (!empty($arResult['PROPERTIES'])) {
		foreach ($arResult['PROPERTIES'] as $code => $prop) {
			if ($code === 'ARTICLE' || $code === 'CML2_ARTICLE') {
				continue;
			}

			if (!empty($prop['VALUE']) && $prop['PROPERTY_TYPE'] !== 'F') {
				$value = is_array($prop['VALUE']) ? implode(', ', $prop['VALUE']) : $prop['VALUE'];
				if (!empty($value) && $value !== '-') {
					$arResult['SPECIFICATIONS'][] = [
						'NAME' => $prop['NAME'],
						'VALUE' => $value,
						'CODE' => $code
					];
				}
			}
		}
	}
}

// === Видео товара ===
$arResult['VIDEO'] = null;
if (!empty($arResult['PROPERTIES']['VIDEO']['VALUE'])) {
	$videoUrl = $arResult['PROPERTIES']['VIDEO']['VALUE'];

	// Обработка VK видео
	if (strpos($videoUrl, 'vk.com') !== false || strpos($videoUrl, 'vkvideo.ru') !== false) {
		// Извлечение ID видео и создание embed кода
		preg_match('/video(-?\d+)_(\d+)/', $videoUrl, $matches);
		if (!empty($matches[1]) && !empty($matches[2])) {
			$ownerId = $matches[1];
			$videoId = $matches[2];
			$arResult['VIDEO'] = [
				'TYPE' => 'VK',
				'EMBED_CODE' => '<iframe src="https://vk.com/video_ext.php?oid=' . $ownerId . '&id=' . $videoId . '&hd=2" width="853" height="480" frameborder="0" allowfullscreen></iframe>'
			];
		}
	}
}

// === Похожие товары ===
$arResult['RELATED_PRODUCTS'] = [];

// Сначала проверяем есть ли привязанные товары
if (!empty($arResult['PROPERTIES']['RELATED']['VALUE'])) {
	$relatedIds = is_array($arResult['PROPERTIES']['RELATED']['VALUE'])
		? $arResult['PROPERTIES']['RELATED']['VALUE']
		: [$arResult['PROPERTIES']['RELATED']['VALUE']];
} else {
	// Проверяем настройки категории через UF поля или свойства раздела
	$relatedIds = [];
	$sectionRelatedIds = [];

	if (!empty($arResult['IBLOCK_SECTION_ID'])) {
		// Получаем настройки раздела
		$rsSection = CIBlockSection::GetByID($arResult['IBLOCK_SECTION_ID']);
		if ($arSection = $rsSection->GetNext()) {
			// Проверяем UF поле для рекомендуемых товаров раздела
			if (!empty($arSection['UF_RELATED_PRODUCTS'])) {
				$sectionRelatedIds = is_array($arSection['UF_RELATED_PRODUCTS'])
					? $arSection['UF_RELATED_PRODUCTS']
					: [$arSection['UF_RELATED_PRODUCTS']];
			}
		}
	}

	if (!empty($sectionRelatedIds)) {
		// Используем товары, указанные для раздела
		$relatedIds = $sectionRelatedIds;
	} else {
		// Товары из той же категории (по умолчанию)
		if (!empty($arResult['IBLOCK_SECTION_ID'])) {
			$rsElements = CIBlockElement::GetList(
				['RAND' => 'ASC'],
				[
					'IBLOCK_ID' => $arResult['IBLOCK_ID'],
					'SECTION_ID' => $arResult['IBLOCK_SECTION_ID'],
					'!ID' => $elementId,
					'ACTIVE' => 'Y'
				],
				false,
				['nTopCount' => 10], // Загружаем 10 товаров
				['ID']
			);

			while ($arElement = $rsElements->GetNext()) {
				$relatedIds[] = $arElement['ID'];
			}
		}
	}
}

// Получение данных похожих товаров
if (!empty($relatedIds)) {
	$rsRelated = CIBlockElement::GetList(
		['SORT' => 'ASC'],
		[
			'IBLOCK_ID' => $arResult['IBLOCK_ID'],
			'ID' => $relatedIds,
			'ACTIVE' => 'Y'
		],
		false,
		false,
		[
			'ID', 'NAME', 'CODE', 'DETAIL_PAGE_URL',
			'PREVIEW_PICTURE', 'DETAIL_PICTURE',
			'PROPERTY_CML2_ARTICLE', 'PREVIEW_TEXT'
		]
	);

	while ($arRelated = $rsRelated->GetNext()) {
		$image = '';
		if ($arRelated['PREVIEW_PICTURE']) {
			$arImage = CFile::GetByID($arRelated['PREVIEW_PICTURE'])->GetNext();
			$image = $arImage['SRC'];
		} elseif ($arRelated['DETAIL_PICTURE']) {
			$arImage = CFile::GetByID($arRelated['DETAIL_PICTURE'])->GetNext();
			$image = $arImage['SRC'];
		}

		// Обработка описания товара
		$description = '';
		if (!empty($arRelated['PREVIEW_TEXT'])) {
			// Убираем лишние HTML теги и оставляем только важные
			$description = strip_tags($arRelated['PREVIEW_TEXT'], '<br><strong><em>');
			// Обрезаем длинные описания
			if (strlen($description) > 200) {
				$description = substr($description, 0, 200) . '...';
			}
		}

		$arResult['RELATED_PRODUCTS'][] = [
			'ID' => $arRelated['ID'],
			'NAME' => $arRelated['NAME'],
			'URL' => $arRelated['DETAIL_PAGE_URL'],
			'IMAGE' => $image,
			'ARTICLE' => $arRelated['PROPERTY_CML2_ARTICLE_VALUE'] ?: $arRelated['ID'],
			'DESCRIPTION' => $description
		];
	}
}

// === Метаданные для Schema.org ===
$arResult['SCHEMA'] = [
	'NAME' => $arResult['NAME'],
	'SKU' => $arResult['PROPERTIES']['CML2_ARTICLE']['VALUE'] ?: '—',
	'CATEGORY' => '',
	'AVAILABILITY' => $arResult['STOCK_STATUS']['AVAILABLE'] ? 'InStock' : 'PreOrder'
];

// Получение названия категории
if (!empty($arResult['IBLOCK_SECTION_ID'])) {
	$rsSection = CIBlockSection::GetByID($arResult['IBLOCK_SECTION_ID']);
	if ($arSection = $rsSection->GetNext()) {
		$arResult['SCHEMA']['CATEGORY'] = $arSection['NAME'];
	}
}

// === Хлебные крошки ===
$arResult['BREADCRUMBS'] = [];

// Добавление главной страницы
$arResult['BREADCRUMBS'][] = [
	'TITLE' => 'Главная',
	'LINK' => '/'
];

// Добавление разделов каталога
$arResult['SECTION_URL'] = '';
if (!empty($arResult['IBLOCK_SECTION_ID'])) {
	$nav = CIBlockSection::GetNavChain(
		$arResult['IBLOCK_ID'],
		$arResult['IBLOCK_SECTION_ID']
	);

	while ($arNav = $nav->GetNext()) {
		$arResult['BREADCRUMBS'][] = [
			'TITLE' => $arNav['NAME'],
			'LINK' => $arNav['SECTION_PAGE_URL']
		];

		// Сохраняем URL текущего раздела
		$arResult['SECTION_URL'] = $arNav['SECTION_PAGE_URL'];
	}
}

// Текущая страница
$arResult['BREADCRUMBS'][] = [
	'TITLE' => $arResult['NAME'],
	'LINK' => '',
	'CURRENT' => true
];

// === Документы товара ===
$arResult['DOCUMENTS'] = [];
if (!empty($arResult['PROPERTIES']['DOCUMENTS']['VALUE'])) {
	$documentIds = is_array($arResult['PROPERTIES']['DOCUMENTS']['VALUE'])
		? $arResult['PROPERTIES']['DOCUMENTS']['VALUE']
		: [$arResult['PROPERTIES']['DOCUMENTS']['VALUE']];

	foreach ($documentIds as $fileId) {
		$arFile = CFile::GetByID($fileId)->GetNext();
		if ($arFile) {
			$arResult['DOCUMENTS'][] = [
				'NAME' => $arFile['ORIGINAL_NAME'],
			'SIZE' => CFile::FormatSize($arFile['FILE_SIZE']),
			'URL' => CFile::GetPath($fileId),
			'TYPE' => strtoupper(pathinfo($arFile['ORIGINAL_NAME'], PATHINFO_EXTENSION))
			];
		}
	}
}

// === Проверка прав доступа ===
$arResult['USER_ACCESS'] = [
	'CAN_VIEW_PRICES' => $USER->IsAuthorized(),
	'CAN_BUY' => $USER->IsAuthorized() && $arResult['STOCK_STATUS']['AVAILABLE'],
	'IS_AUTHORIZED' => $USER->IsAuthorized()
];

// === Настройки компонента ===
$arResult['COMPONENT_PARAMS'] = [
	'SHOW_PRICE' => $arParams['SHOW_PRICE'] !== 'N',
	'SHOW_QUANTITY' => $arParams['SHOW_QUANTITY'] !== 'N',
	'SHOW_SKU_PROPS' => $arParams['SHOW_SKU_PROPS'] !== 'N',
	'RELATED_COUNT' => intval($arParams['RELATED_COUNT']) ?: 8
];

// === Добавление в историю просмотров ===
if ($USER->IsAuthorized()) {
	// Здесь можно добавить логику сохранения в историю просмотров
}

// === Счетчики и аналитика ===
$arResult['ANALYTICS'] = [
	'PRODUCT_ID' => $elementId,
	'PRODUCT_NAME' => $arResult['NAME'],
	'CATEGORY' => $arResult['SCHEMA']['CATEGORY'],
	'PRICE' => $arResult['PRICES_PROCESSED']['USER']['VALUE'] ?? 0
];
?>