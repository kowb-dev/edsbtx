<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

// Проверяем, что это AJAX запрос
if (!Application::getInstance()->getContext()->getRequest()->isPost()) {
	die();
}

$request = Application::getInstance()->getContext()->getRequest();
$action = $request->getPost('action');

$response = ['success' => false, 'error' => 'Unknown action'];

switch ($action) {
	case 'load_more_products':
		$response = loadMoreProducts($request);
		break;

	case 'favorites':
		$response = toggleFavorites($request);
		break;

	case 'compare':
		$response = toggleCompare($request);
		break;
}

header('Content-Type: application/json');
echo Json::encode($response);

/**
 * Загрузка дополнительных товаров для кнопки "Показать ещё"
 */
function loadMoreProducts($request) {
	if (!CModule::IncludeModule("iblock")) {
		return ['success' => false, 'error' => 'IBlock module not found'];
	}

	$sectionId = intval($request->getPost('section_id'));
	$iblockId = intval($request->getPost('iblock_id'));
	$offset = intval($request->getPost('offset')) ?: 0;
	$count = intval($request->getPost('count')) ?: 20;
	$sort = $request->getPost('sort') ?: 'name_asc';

	// Проверяем обязательные параметры
	if (!$sectionId || !$iblockId) {
		return ['success' => false, 'error' => 'Section ID or IBlock ID is missing'];
	}

	// Параметры сортировки
	$arSort = getSortParameters($sort);

	// Базовый фильтр
	$arFilter = [
		'IBLOCK_ID' => $iblockId,
		'SECTION_ID' => $sectionId,
		'ACTIVE' => 'Y',
		'INCLUDE_SUBSECTIONS' => 'Y'
	];

	// Применяем фильтры из формы
	$filterInput = $request->getPost('filter_input');
	$filterAdditional = $request->getPost('filter_additional');

	if (!empty($filterInput) && is_array($filterInput)) {
		$arFilter['PROPERTY_INPUT'] = $filterInput;
	}

	if (!empty($filterAdditional) && is_array($filterAdditional)) {
		$arFilter['PROPERTY_ADDITIONAL'] = $filterAdditional;
	}

	// Получаем товары с offset
	$arNavParams = [
		'nPageSize' => $count,
		'iNumPage' => 1,
		'bShowAll' => false
	];

	$arSelect = [
		'ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE', 'DETAIL_PICTURE',
		'DETAIL_PAGE_URL', 'CATALOG_QUANTITY', 'CODE'
	];

	// Сначала получаем общее количество товаров
	$rsElementsCount = CIBlockElement::GetList($arSort, $arFilter, false, false, ['ID']);
	$totalCount = $rsElementsCount->SelectedRowsCount();

	// Затем получаем товары с учетом offset
	$rsElements = CIBlockElement::GetList(
		$arSort,
		$arFilter,
		false,
		['nPageSize' => $count, 'nOffset' => $offset],
		$arSelect
	);

	$items = [];
	$loadedCount = 0;

	while ($arItem = $rsElements->GetNext()) {
		// Получаем свойства
		$rsProps = CIBlockElement::GetProperty($iblockId, $arItem['ID']);
		$arItem['PROPERTIES'] = [];
		while ($arProp = $rsProps->Fetch()) {
			$arItem['PROPERTIES'][$arProp['CODE']] = $arProp;
		}

		// Обрабатываем товар как в модификаторе
		$arItem = processItemForAjax($arItem);

		// Получаем цены
		if (CModule::IncludeModule("catalog")) {
			$arPrices = CPrice::GetList([], ['PRODUCT_ID' => $arItem['ID']]);
			if ($price = $arPrices->Fetch()) {
				$arItem['PRICE'] = $price;
			}
		}

		$items[] = $arItem;
		$loadedCount++;
	}

	// Генерируем HTML
	$html = '';
	foreach ($items as $arItem) {
		$html .= generateProductCard($arItem);
	}

	$hasMore = ($offset + $loadedCount) < $totalCount;

	return [
		'success' => true,
		'html' => $html,
		'loadedCount' => $loadedCount,
		'hasMore' => $hasMore,
		'totalCount' => $totalCount,
		'offset' => $offset,
		'newOffset' => $offset + $loadedCount
	];
}

/**
 * Получение параметров сортировки
 */
function getSortParameters($sort) {
	switch ($sort) {
		case 'name_desc':
			return ['NAME' => 'DESC'];
		case 'price_asc':
			return ['CATALOG_PRICE_1' => 'ASC'];
		case 'price_desc':
			return ['CATALOG_PRICE_1' => 'DESC'];
		case 'popularity':
			return ['SHOW_COUNTER' => 'DESC'];
		case 'date_desc':
			return ['DATE_CREATE' => 'DESC'];
		default:
			return ['NAME' => 'ASC'];
	}
}

/**
 * Обработка товара как в модификаторе
 */
function processItemForAjax($arItem) {
	// Получаем артикул
	$arItem['ARTICLE'] = '';
	$articleProperties = ['CML2_ARTICLE', 'ARTICLE', 'ART', 'ARTICUL', 'CODE_ARTICLE'];

	foreach ($articleProperties as $propCode) {
		if (!empty($arItem['PROPERTIES'][$propCode]['VALUE'])) {
			$value = $arItem['PROPERTIES'][$propCode]['VALUE'];
			$arItem['ARTICLE'] = is_array($value) ? $value[0] : $value;
			break;
		}
	}

	if (empty($arItem['ARTICLE']) && !empty($arItem['CODE'])) {
		$arItem['ARTICLE'] = $arItem['CODE'];
	}

	// Получаем дополнительные изображения
	$arItem['MORE_PHOTOS'] = [];
	$arItem['HAS_ADDITIONAL_IMAGES'] = false;

	$photoProperties = ['MORE_PHOTO', 'PHOTOS', 'IMAGES', 'GALLERY', 'ADDITIONAL_PHOTOS'];

	foreach ($photoProperties as $propCode) {
		if (!empty($arItem['PROPERTIES'][$propCode]['VALUE'])) {
			$photos = $arItem['PROPERTIES'][$propCode]['VALUE'];

			if (is_array($photos)) {
				foreach ($photos as $photoId) {
					if (!empty($photoId) && intval($photoId) > 0) {
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

	// Проверяем DETAIL_PICTURE
	if (!empty($arItem['DETAIL_PICTURE']) &&
	    $arItem['DETAIL_PICTURE'] !== $arItem['PREVIEW_PICTURE'] &&
	    !in_array($arItem['DETAIL_PICTURE'], $arItem['MORE_PHOTOS'])) {

		$fileInfo = CFile::GetFileArray($arItem['DETAIL_PICTURE']);
		if ($fileInfo && file_exists($_SERVER['DOCUMENT_ROOT'] . $fileInfo['SRC'])) {
			array_unshift($arItem['MORE_PHOTOS'], intval($arItem['DETAIL_PICTURE']));
		}
	}

	$arItem['HAS_ADDITIONAL_IMAGES'] = count($arItem['MORE_PHOTOS']) > 0;

	return $arItem;
}

/**
 * Генерация HTML карточки товара
 */
function generateProductCard($arItem) {
	global $USER;

	$isAuthorized = $USER->IsAuthorized();

	// Получаем дополнительные изображения
	$moreImages = [];
	if ($arItem['HAS_ADDITIONAL_IMAGES'] && !empty($arItem['MORE_PHOTOS'])) {
		foreach ($arItem['MORE_PHOTOS'] as $photoId) {
			if ($photoId && intval($photoId) > 0) {
				$resizedImage = CFile::ResizeImageGet($photoId, ['width' => 400, 'height' => 400], BX_RESIZE_IMAGE_PROPORTIONAL, true);
				if ($resizedImage && $resizedImage['src']) {
					$moreImages[] = $resizedImage;
				}
			}
		}
	}

	// Финальная проверка наличия дополнительных изображений
	$hasAdditionalImages = count($moreImages) > 0;
	$totalImagesCount = $hasAdditionalImages ? (count($moreImages) + 1) : 1;

	// Формируем основное изображение
	$imageHtml = '';
	if (!empty($arItem['PREVIEW_PICTURE'])) {
		$arImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], ['width' => 400, 'height' => 400], BX_RESIZE_IMAGE_PROPORTIONAL, true);
		if ($arImage) {
			$imageHtml = '<img src="' . $arImage['src'] . '" alt="' . htmlspecialchars($arItem['NAME']) . '" class="edsys-product-card__image edsys-product-card__image--main" width="400" height="400" loading="lazy" data-image-index="0">';
		}
	} elseif (!empty($arItem['DETAIL_PICTURE'])) {
		$arImage = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], ['width' => 400, 'height' => 400], BX_RESIZE_IMAGE_PROPORTIONAL, true);
		if ($arImage) {
			$imageHtml = '<img src="' . $arImage['src'] . '" alt="' . htmlspecialchars($arItem['NAME']) . '" class="edsys-product-card__image edsys-product-card__image--main" width="400" height="400" loading="lazy" data-image-index="0">';
		}
	}

	if (empty($imageHtml)) {
		$imageHtml = '<div class="edsys-product-card__no-image"><i class="ph ph-thin ph-image"></i><span>Нет изображения</span></div>';
	}

	// Формируем дополнительные изображения
	$additionalImagesHtml = '';
	$navigationHtml = '';

	if ($hasAdditionalImages) {
		$navigationIndicators = '<button class="edsys-image-indicator edsys-image-indicator--active" data-image-index="0" title="Основное изображение"></button>';

		foreach ($moreImages as $index => $moreImage) {
			$imageIndex = $index + 1;
			$additionalImagesHtml .= '<img src="' . $moreImage['src'] . '" alt="' . htmlspecialchars($arItem['NAME']) . ' - изображение ' . ($imageIndex + 1) . '" class="edsys-product-card__image edsys-product-card__image--additional" width="400" height="400" loading="lazy" data-image-index="' . $imageIndex . '">';
			$navigationIndicators .= '<button class="edsys-image-indicator" data-image-index="' . $imageIndex . '" title="Изображение ' . ($imageIndex + 1) . '"></button>';
		}

		$navigationHtml = '
            <div class="edsys-product-card__image-nav">
                <div class="edsys-image-indicators">
                    ' . $navigationIndicators . '
                </div>
            </div>';
	}

	// Формируем цены для авторизованных
	$priceHtml = '';
	if ($isAuthorized && !empty($arItem['PRICE'])) {
		$retailPrice = CurrencyFormat($arItem['PRICE']['PRICE'], $arItem['PRICE']['CURRENCY']);
		$personalPrice = CurrencyFormat($arItem['PRICE']['PRICE'] * 0.9, $arItem['PRICE']['CURRENCY']); // Скидка 10%

		$priceHtml = '
            <div class="edsys-product-card__pricing">
                <div class="edsys-product-card__prices">
                    <div class="edsys-product-card__price edsys-product-card__price--retail">
                        <span class="edsys-product-card__price-label">розн.</span>
                        <span class="edsys-product-card__price-value">' . $retailPrice . '</span>
                    </div>
                    <div class="edsys-product-card__price edsys-product-card__price--personal">
                        <span class="edsys-product-card__price-label">ваша цена</span>
                        <span class="edsys-product-card__price-value">' . $personalPrice . '</span>
                    </div>
                </div>
                <div class="edsys-product-card__availability">
                    ' . ($arItem['CATALOG_QUANTITY'] > 0 ?
				'<span class="edsys-availability edsys-availability--in-stock"><i class="ph ph-thin ph-check-circle"></i>В наличии</span>' :
				'<span class="edsys-availability edsys-availability--preorder"><i class="ph ph-thin ph-clock"></i>Под заказ</span>') . '
                </div>
            </div>';
	} else {
		$priceHtml = '
            <div class="edsys-product-card__auth-notice">
                <a href="/login/" class="edsys-auth-link">
                    Войдите в аккаунт для просмотра цен
                </a>
            </div>';
	}

	$articleHtml = '';
	if (!empty($arItem['ARTICLE'])) {
		$articleHtml = '<div class="edsys-product-card__article">Арт. ' . htmlspecialchars($arItem['ARTICLE']) . '</div>';
	}

	$descriptionHtml = '';
	if (!empty($arItem['PREVIEW_TEXT'])) {
		$description = strip_tags($arItem['PREVIEW_TEXT']);
		if (strlen($description) > 100) {
			$description = substr($description, 0, 100) . '...';
		}
		$descriptionHtml = '<div class="edsys-product-card__description">' . $description . '</div>';
	}

	return '
        <article class="edsys-product-card" 
                 data-product-id="' . $arItem['ID'] . '" 
                 data-has-additional="' . ($hasAdditionalImages ? 'true' : 'false') . '"
                 data-images-count="' . $totalImagesCount . '">
            <div class="edsys-product-card__image-wrapper">
                <a href="' . $arItem['DETAIL_PAGE_URL'] . '" class="edsys-product-card__image-link">
                    ' . $imageHtml . '
                    ' . $additionalImagesHtml . '
                </a>
                ' . $navigationHtml . '
                <div class="edsys-product-card__quick-actions">
                    <button type="button" class="edsys-quick-action edsys-quick-action--favorite" title="Добавить в избранное" data-action="add-to-favorites" data-product-id="' . $arItem['ID'] . '">
                        <i class="ph ph-thin ph-heart"></i>
                    </button>
                    <button type="button" class="edsys-quick-action edsys-quick-action--compare" title="Добавить к сравнению" data-action="add-to-compare" data-product-id="' . $arItem['ID'] . '">
                        <i class="ph ph-thin ph-chart-bar"></i>
                    </button>
                </div>
            </div>
            <div class="edsys-product-card__content">
                ' . $articleHtml . '
                <h3 class="edsys-product-card__title">
                    <a href="' . $arItem['DETAIL_PAGE_URL'] . '" class="edsys-product-card__title-link">
                        ' . htmlspecialchars($arItem['NAME']) . '
                    </a>
                </h3>
                ' . $descriptionHtml . '
                ' . $priceHtml . '
            </div>
        </article>';
}

/**
 * Переключение избранного
 */
function toggleFavorites($request) {
	global $USER;

	if (!$USER->IsAuthorized()) {
		return ['success' => false, 'error' => 'User not authorized'];
	}

	$productId = intval($request->getPost('product_id'));
	$add = $request->getPost('add') === '1';

	if (!$productId) {
		return ['success' => false, 'error' => 'Invalid product ID'];
	}

	// Здесь должна быть логика работы с избранным
	// Например, запись в таблицу пользовательских избранных товаров

	return ['success' => true, 'message' => $add ? 'Added to favorites' : 'Removed from favorites'];
}

/**
 * Переключение сравнения
 */
function toggleCompare($request) {
	global $USER;

	if (!$USER->IsAuthorized()) {
		return ['success' => false, 'error' => 'User not authorized'];
	}

	$productId = intval($request->getPost('product_id'));
	$add = $request->getPost('add') === '1';

	if (!$productId) {
		return ['success' => false, 'error' => 'Invalid product ID'];
	}

	// Здесь должна быть логика работы со сравнением
	// Например, запись в сессию или таблицу сравнения товаров

	return ['success' => true, 'message' => $add ? 'Added to compare' : 'Removed from compare'];
}
?>