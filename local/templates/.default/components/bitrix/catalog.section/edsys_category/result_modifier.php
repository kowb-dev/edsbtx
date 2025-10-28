<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Модификатор результатов для шаблона каталога
 */

// Правильное определение ID секции
$currentSectionId = 0;

// Способ 1: Из параметров компонента
if (!empty($arParams['SECTION_ID'])) {
	$currentSectionId = intval($arParams['SECTION_ID']);
}

// Способ 2: Из результата (если есть текущая секция)
if (!$currentSectionId && !empty($arResult['SECTION']['ID'])) {
	$currentSectionId = intval($arResult['SECTION']['ID']);
}

// Способ 3: Из глобальных переменных Bitrix
if (!$currentSectionId && defined('SITE_ID')) {
	global $APPLICATION;
	$curPage = $APPLICATION->GetCurPage();

	// Пытаемся получить ID секции из URL
	if (preg_match('/\/(\d+)\/$/', $curPage, $matches)) {
		$currentSectionId = intval($matches[1]);
	}
}

// Способ 4: Из $_REQUEST (последний вариант)
if (!$currentSectionId && !empty($_REQUEST['SECTION_ID'])) {
	$currentSectionId = intval($_REQUEST['SECTION_ID']);
}

// Способ 5: Найти секцию по коду, если есть
if (!$currentSectionId && !empty($arParams['SECTION_CODE'])) {
	if (CModule::IncludeModule("iblock")) {
		$arFilter = array(
			'IBLOCK_ID' => $arParams['IBLOCK_ID'],
			'CODE' => $arParams['SECTION_CODE'],
			'ACTIVE' => 'Y'
		);
		$rsSections = CIBlockSection::GetList(array(), $arFilter, false, array('ID'));
		if ($arSection = $rsSections->Fetch()) {
			$currentSectionId = intval($arSection['ID']);
		}
	}
}

// Сохраняем найденный ID секции в результат
$arResult['CURRENT_SECTION_ID'] = $currentSectionId;

// Получаем путь к разделу для хлебных крошек
if (!empty($arResult['SECTION']) || $currentSectionId) {
	$arResult['SECTION']['PATH'] = [];

	$sectionIdForPath = !empty($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : $currentSectionId;

	if ($sectionIdForPath && CModule::IncludeModule("iblock")) {
		$nav = CIBlockSection::GetNavChain($arParams['IBLOCK_ID'], $sectionIdForPath);
		while ($arNav = $nav->GetNext()) {
			$arResult['SECTION']['PATH'][] = $arNav;
		}

		// Если нет информации о текущей секции, получаем её
		if (empty($arResult['SECTION']['NAME']) && $currentSectionId) {
			$rsSection = CIBlockSection::GetByID($currentSectionId);
			if ($arSection = $rsSection->GetNext()) {
				$arResult['SECTION'] = array_merge($arResult['SECTION'] ?: [], $arSection);
			}
		}
	}
}

// Обрабатываем товары
if (!empty($arResult['ITEMS'])) {
    global $USER;
    $favoriteIds = [];
    if ($USER->IsAuthorized()) {
        $rsUser = CUser::GetByID($USER->GetID())->Fetch();
        $favoriteIds = $rsUser['UF_FAVORITES'] ?? [];
        if (!is_array($favoriteIds)) {
            $favoriteIds = [];
        }
    }

	foreach ($arResult['ITEMS'] as $key => &$arItem) {
        $arItem['IS_FAVORITE'] = in_array($arItem['ID'], $favoriteIds);
		// Формируем массив цен
		if (!empty($arItem['ITEM_PRICES'])) {
			$arItem['PRICES'] = $arItem['ITEM_PRICES'];
		}

		// УЛУЧШЕННАЯ логика получения артикула
		$arItem['ARTICLE'] = '';

		// Проверяем разные варианты свойств артикула
		$articleProperties = ['CML2_ARTICLE', 'ARTICLE', 'ART', 'ARTICUL', 'CODE_ARTICLE'];

		foreach ($articleProperties as $propCode) {
			if (!empty($arItem['PROPERTIES'][$propCode]['VALUE'])) {
				$value = $arItem['PROPERTIES'][$propCode]['VALUE'];
				// Если значение массив, берем первый элемент
				$arItem['ARTICLE'] = is_array($value) ? $value[0] : $value;
				break;
			}
		}

        // --- НАЧАЛО ИСПРАВЛЕНИЯ ---
        // Принудительно получаем свойство MORE_PHOTO, так как оно не передается в параметрах компонента
        if (empty($arItem['PROPERTIES']['MORE_PHOTO']['VALUE'])) {
            $db_props = CIBlockElement::GetProperty(
                $arItem['IBLOCK_ID'],
                $arItem['ID'],
                array("sort" => "asc"),
                array("CODE" => "MORE_PHOTO")
            );
            $arItem['PROPERTIES']['MORE_PHOTO']['VALUE'] = [];
            while ($ar_props = $db_props->Fetch()) {
                if ($ar_props['VALUE']) {
                    $arItem['PROPERTIES']['MORE_PHOTO']['VALUE'][] = $ar_props['VALUE'];
                }
            }
        }
        // --- КОНЕЦ ИСПРАВЛЕНИЯ ---

		// УЛУЧШЕННАЯ логика получения дополнительных изображений
		$arItem['MORE_PHOTOS'] = [];
		$arItem['HAS_ADDITIONAL_IMAGES'] = false;

		// Проверяем разные варианты свойств с изображениями
		$photoProperties = ['MORE_PHOTO', 'PHOTOS', 'IMAGES', 'GALLERY', 'ADDITIONAL_PHOTOS'];

		foreach ($photoProperties as $propCode) {
			if (!empty($arItem['PROPERTIES'][$propCode]['VALUE'])) {
				$photos = $arItem['PROPERTIES'][$propCode]['VALUE'];

				if (is_array($photos)) {
					foreach ($photos as $photoId) {
						if (!empty($photoId) && intval($photoId) > 0) {
							// Проверяем, что файл действительно существует
							$fileInfo = CFile::GetFileArray($photoId);
							if ($fileInfo && file_exists($_SERVER['DOCUMENT_ROOT'] . $fileInfo['SRC'])) {
								$arItem['MORE_PHOTOS'][] = intval($photoId);
							}
						}
					}
				}

				if (!empty($arItem['MORE_PHOTOS'])) {
					break;
				}
			}
		}

		// Дополнительная проверка: если есть DETAIL_PICTURE и оно отличается от PREVIEW_PICTURE
		if (!empty($arItem['DETAIL_PICTURE']) &&
		    $arItem['DETAIL_PICTURE'] !== $arItem['PREVIEW_PICTURE'] &&
		    !in_array($arItem['DETAIL_PICTURE'], $arItem['MORE_PHOTOS'])) {

			// Проверяем, что файл существует
			$fileInfo = CFile::GetFileArray($arItem['DETAIL_PICTURE']);
			if ($fileInfo && file_exists($_SERVER['DOCUMENT_ROOT'] . $fileInfo['SRC'])) {
				array_unshift($arItem['MORE_PHOTOS'], intval($arItem['DETAIL_PICTURE']));
			}
		}

		// Устанавливаем флаг наличия дополнительных изображений
		$arItem['HAS_ADDITIONAL_IMAGES'] = count($arItem['MORE_PHOTOS']) > 0;

		// Обрабатываем изображения для ленивой загрузки
		if (!empty($arItem['PREVIEW_PICTURE'])) {
			$arItem['PREVIEW_PICTURE_LAZY'] = CFile::ResizeImageGet(
				$arItem['PREVIEW_PICTURE'],
				['width' => 400, 'height' => 400],
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
		}

		// Обрабатываем описание
		if (!empty($arItem['PREVIEW_TEXT'])) {
			$arItem['PREVIEW_TEXT_FORMATTED'] = strip_tags($arItem['PREVIEW_TEXT']);
		}

		// Отладочная информация для конкретного товара (можно убрать)
		if ($_GET['debug_item'] == $arItem['ID']) {
			echo '<pre>DEBUG ITEM ' . $arItem['ID'] . ':';
			echo "\nArticle: " . $arItem['ARTICLE'];
			echo "\nHas additional images: " . ($arItem['HAS_ADDITIONAL_IMAGES'] ? 'YES' : 'NO');
			echo "\nMore photos count: " . count($arItem['MORE_PHOTOS']);
			echo "\nMore photos IDs: " . implode(', ', $arItem['MORE_PHOTOS']);
			echo "\nProperties keys: " . implode(', ', array_keys($arItem['PROPERTIES']));
			echo '</pre>';
		}
	}
	unset($arItem);
}

// Устанавливаем параметры для AJAX
$arResult['AJAX_PARAMS'] = [
	'SECTION_ID' => $currentSectionId,
	'IBLOCK_ID' => $arParams['IBLOCK_ID'],
	'INITIAL_COUNT' => $arParams['PAGE_ELEMENT_COUNT'] ?: 12,
	'LOAD_MORE_COUNT' => 8
];

// Общая отладочная информация
if ($_GET['debug'] == 'Y') {
	echo '<pre>DEBUG INFO:';
	echo "\nCurrent Section ID: " . $currentSectionId;
	echo "\nParams SECTION_ID: " . ($arParams['SECTION_ID'] ?: 'empty');
	echo "\nParams SECTION_CODE: " . ($arParams['SECTION_CODE'] ?: 'empty');
	echo "\nResult SECTION ID: " . ($arResult['SECTION']['ID'] ?: 'empty');
	echo "\nRequest SECTION_ID: " . ($_REQUEST['SECTION_ID'] ?: 'empty');
	echo "\nCurrent Page: " . $APPLICATION->GetCurPage();
	echo "\nTotal items: " . count($arResult['ITEMS']);

	// Статистика по изображениям
	$withAdditional = 0;
	$withoutAdditional = 0;
	foreach ($arResult['ITEMS'] as $item) {
		if ($item['HAS_ADDITIONAL_IMAGES']) {
			$withAdditional++;
		} else {
			$withoutAdditional++;
		}
	}
	echo "\nItems with additional images: " . $withAdditional;
	echo "\nItems without additional images: " . $withoutAdditional;
	echo '</pre>';
}
?>