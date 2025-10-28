<?php
// Создайте файл /local/ajax/catalog_handler.php

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

use Bitrix\Main\Loader;
use Bitrix\Main\Application;

// Устанавливаем заголовки для JSON
header('Content-Type: application/json; charset=utf-8');

// Проверяем AJAX запрос
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['ajax'])) {
	echo json_encode(['success' => false, 'error' => 'Invalid request']);
	exit;
}

try {
	$action = $_POST['action'] ?? '';

	switch ($action) {
		case 'load_more':
			handleLoadMore();
			break;

		case 'add_favorite':
		case 'remove_favorite':
			handleFavoriteAction();
			break;

		case 'add_compare':
		case 'remove_compare':
			handleCompareAction();
			break;

		default:
			echo json_encode(['success' => false, 'error' => 'Unknown action']);
	}

} catch (Exception $e) {
	echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

function handleLoadMore() {
	if (!Loader::includeModule('iblock')) {
		throw new Exception('IBlock module not loaded');
	}

	$page = intval($_POST['page'] ?? 0);
	$sectionId = intval($_POST['section_id'] ?? 0);
	$iblockId = intval($_POST['iblock_id'] ?? 0);
	$itemsPerPage = intval($_POST['items_per_page'] ?? 20);

	if ($page <= 1 || !$sectionId || !$iblockId) {
		throw new Exception('Invalid parameters');
	}

	$arFilter = [
		'IBLOCK_ID' => $iblockId,
		'SECTION_ID' => $sectionId,
		'ACTIVE' => 'Y',
		'INCLUDE_SUBSECTIONS' => 'Y',
	];

	$arSelect = [
		'ID', 'NAME', 'PREVIEW_TEXT', 'PREVIEW_PICTURE',
		'DETAIL_PAGE_URL', 'PROPERTY_*'
	];

	$arNavParams = [
		'nPageSize' => $itemsPerPage,
		'iNumPage' => $page,
		'bShowAll' => false,
	];

	$rsElements = CIBlockElement::GetList(
		['SORT' => 'ASC', 'NAME' => 'ASC'],
		$arFilter,
		false,
		$arNavParams,
		$arSelect
	);

	$items = [];
	while ($element = $rsElements->GetNextElement()) {
		$fields = $element->GetFields();
		$properties = $element->GetProperties();

		$item = array_merge($fields, ['PROPERTIES' => $properties]);

		// Обработка изображения
		if (!empty($item['PREVIEW_PICTURE'])) {
			$item['PREVIEW_PICTURE'] = CFile::ResizeImageGet(
				$item['PREVIEW_PICTURE'],
				['width' => 400, 'height' => 400],
				BX_RESIZE_IMAGE_PROPORTIONAL,
				true
			);
		}

		// Получаем цены если пользователь авторизован
		if (global $USER && $USER->IsAuthorized() && Loader::includeModule('catalog')) {
			$priceTypes = ['BASE'];
			if (defined('PRICE_CODE')) {
				$priceTypes = PRICE_CODE;
			}
			$arPrices = GetCatalogProductPrice($item['ID'], $priceTypes);
			$item['PRICES'] = $arPrices ?: [];
		} else {
			$item['PRICES'] = [];
		}

        $items[] = $item;
    }

	if (!empty($items)) {
		$html = renderProductCards($items);

		echo json_encode([
			'success' => true,
			'items' => $html,
			'hasMore' => $rsElements->NavPageNomer < $rsElements->NavPageCount,
			'currentPage' => $page,
			'totalPages' => $rsElements->NavPageCount
		]);
	} else {
		echo json_encode([
			'success' => false,
			'hasMore' => false,
			'message' => 'No more items'
		]);
	}
}

function renderProductCards($items) {
	global $USER;

	ob_start();
	foreach ($items as $item) {
		?>
		<article class="product-card" data-product-id="<?=$item['ID']?>">
			<div class="product-card-inner">

				<!-- Изображение товара -->
				<div class="product-image-wrapper">
					<a href="<?=$item['DETAIL_PAGE_URL']?>" class="product-image-link">
						<?php if (!empty($item['PREVIEW_PICTURE'])): ?>
							<img
								class="product-image"
								src="<?=$item['PREVIEW_PICTURE']['SRC']?>"
								alt="<?=htmlspecialcharsEx($item['NAME'])?>"
								loading="lazy"
								width="<?=$item['PREVIEW_PICTURE']['WIDTH']?>"
								height="<?=$item['PREVIEW_PICTURE']['HEIGHT']?>"
							/>
						<?php else: ?>
							<div class="product-image-placeholder">
								<svg width="60" height="60" viewBox="0 0 60 60">
									<rect width="60" height="60" fill="#f0f0f0"/>
									<text x="30" y="35" text-anchor="middle" fill="#999">Фото</text>
								</svg>
							</div>
						<?php endif; ?>
					</a>

					<!-- Быстрые действия -->
					<div class="product-quick-actions">
						<button class="quick-action-btn favorites-btn"
						        data-product-id="<?=$item['ID']?>"
						        aria-label="В избранное">
							<svg width="18" height="18" viewBox="0 0 18 18">
								<path d="M9 15.5L7.7 14.3C3.4 10.4 0.5 7.9 0.5 4.8C0.5 2.3 2.4 0.5 4.9 0.5C6.2 0.5 7.5 1.1 8.3 2C9.1 1.1 10.4 0.5 11.7 0.5C14.2 0.5 16.1 2.3 16.1 4.8C16.1 7.9 13.2 10.4 8.9 14.3L9 15.5Z" stroke="currentColor" fill="none"/>
							</svg>
						</button>

						<button class="quick-action-btn compare-btn"
						        data-product-id="<?=$item['ID']?>"
						        aria-label="К сравнению">
							<svg width="18" height="18" viewBox="0 0 18 18">
								<path d="M3 14V8M9 14V4M15 14V10" stroke="currentColor" stroke-width="2"/>
							</svg>
						</button>
					</div>
				</div>

				<!-- Информация о товаре -->
				<div class="product-info">
					<?php if (!empty($item['PROPERTIES']['ARTICLE']['VALUE'])): ?>
						<div class="product-article">
							Артикул: <?=$item['PROPERTIES']['ARTICLE']['VALUE']?>
						</div>
					<?php endif; ?>

					<h3 class="product-name">
						<a href="<?=$item['DETAIL_PAGE_URL']?>"><?=htmlspecialcharsEx($item['NAME'])?></a>
					</h3>

					<?php if (!empty($item['PREVIEW_TEXT'])): ?>
						<div class="product-description">
							<?=htmlspecialcharsEx($item['PREVIEW_TEXT'])?>
						</div>
					<?php endif; ?>

					<!-- Цены (показываем только авторизованным пользователям) -->
					<?php if ($USER->IsAuthorized() && !empty($item['PRICES'])): ?>
						<div class="product-prices">
							<?php foreach ($item['PRICES'] as $priceCode => $price): ?>
								<?php if (!empty($price['PRINT_VALUE'])): ?>
									<div class="price-item <?=strtolower($priceCode)?>">
										<?php if ($priceCode == 'BASE'): ?>
											<span class="price-label">розн.</span>
										<?php elseif ($priceCode == 'PERSONAL'): ?>
											<span class="price-label personal">ваша цена</span>
										<?php endif; ?>
										<span class="price-value"><?=$price['PRINT_VALUE']?></span>
									</div>
								<?php endif; ?>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<!-- Статус товара -->
					<div class="product-status">
						<?php if ($item['CAN_BUY']): ?>
							<span class="status-available">В наличии</span>
						<?php else: ?>
							<span class="status-preorder">Под заказ</span>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</article>
		<?php
	}
	return ob_get_clean();
}

function handleFavoriteAction() {
	global $USER;

	$productId = intval($_POST['product_id'] ?? 0);
	$action = $_POST['action'] ?? '';

	if (!$productId) {
		throw new Exception('Invalid product ID');
	}

	// Для авторизованных пользователей можно сохранять в БД
	if ($USER->IsAuthorized()) {
		// Добавить логику сохранения в базу данных
		// Например, в пользовательские свойства или отдельную таблицу
	}

	echo json_encode(['success' => true]);
}

function handleCompareAction() {
	global $USER;

	$productId = intval($_POST['product_id'] ?? 0);
	$action = $_POST['action'] ?? '';

	if (!$productId) {
		throw new Exception('Invalid product ID');
	}

	// Аналогично для сравнения
	echo json_encode(['success' => true]);
}
?>