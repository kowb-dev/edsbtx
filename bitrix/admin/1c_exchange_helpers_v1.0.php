<?php
/**
 * Вспомогательные функции для обмена с 1С
 * Версия: 1.0
 * Дата: 2025-01-21
 *
 * Подключается к основному файлу обмена
 * require_once("1c_exchange_helpers_v1.0.php");
 */

/**
 * Создание или обновление типа цены
 */
function createOrUpdatePriceType($xmlId, $name) {
	// Поиск существующего типа цены
	$rsPrice = CPrice::GetList(
		array(),
		array("XML_ID" => $xmlId),
		false,
		false,
		array("ID", "NAME")
	);

	if ($arPrice = $rsPrice->Fetch()) {
		// Обновление существующего
		$arFields = array("NAME" => $name);
		if (CPrice::Update($arPrice["ID"], $arFields)) {
			writeLog("Тип цены обновлен: $name");
			return $arPrice["ID"];
		}
	} else {
		// Создание нового
		$arFields = array(
			"NAME" => $name,
			"CODE" => generateCode($name),
			"SORT" => 100,
			"BASE" => "N",
			"XML_ID" => $xmlId
		);

		$priceId = CPrice::Add($arFields);
		if ($priceId) {
			writeLog("Тип цены создан: $name (ID: $priceId)");
			return $priceId;
		}
	}

	return false;
}

/**
 * Создание или обновление склада
 */
function createOrUpdateStorage($xmlId, $name) {
	global $DB;

	// Поиск существующего склада в таблице каталога
	$query = "SELECT ID FROM b_catalog_store WHERE XML_ID = '" . $DB->ForSql($xmlId) . "'";
	$rsStore = $DB->Query($query);

	if ($arStore = $rsStore->Fetch()) {
		// Обновление существующего
		$arFields = array("TITLE" => $name);
		if (CCatalogStore::Update($arStore["ID"], $arFields)) {
			writeLog("Склад обновлен: $name");
			return $arStore["ID"];
		}
	} else {
		// Создание нового
		$arFields = array(
			"TITLE" => $name,
			"CODE" => generateCode($name),
			"SORT" => 100,
			"ACTIVE" => "Y",
			"XML_ID" => $xmlId
		);

		$storeId = CCatalogStore::Add($arFields);
		if ($storeId) {
			writeLog("Склад создан: $name (ID: $storeId)");
			return $storeId;
		}
	}

	return false;
}

/**
 * Создание или обновление свойства инфоблока
 */
function createOrUpdateIBlockProperty($iblockId, $xmlId, $name, $type = 'Строка') {
	// Определяем тип свойства в Битрикс
	$propertyType = 'S'; // Строка по умолчанию
	switch ($type) {
		case 'Число':
			$propertyType = 'N';
			break;
		case 'Логический':
			$propertyType = 'L';
			break;
		case 'Дата':
			$propertyType = 'S';
			break;
		case 'Справочник':
			$propertyType = 'L';
			break;
	}

	// Поиск существующего свойства
	$rsProperty = CIBlockProperty::GetList(
		array(),
		array("IBLOCK_ID" => $iblockId, "XML_ID" => $xmlId)
	);

	if ($arProperty = $rsProperty->Fetch()) {
		// Обновление существующего
		$arFields = array("NAME" => $name);
		$obProperty = new CIBlockProperty;

		if ($obProperty->Update($arProperty["ID"], $arFields)) {
			writeLog("Свойство обновлено: $name");
			return $arProperty["ID"];
		}
	} else {
		// Создание нового
		$arFields = array(
			"NAME" => $name,
			"CODE" => generateCode($name),
			"IBLOCK_ID" => $iblockId,
			"PROPERTY_TYPE" => $propertyType,
			"SORT" => 500,
			"XML_ID" => $xmlId,
			"ACTIVE" => "Y"
		);

		$obProperty = new CIBlockProperty;
		$propId = $obProperty->Add($arFields);

		if ($propId) {
			writeLog("Свойство создано: $name (ID: $propId)");
			return $propId;
		}
	}

	return false;
}

/**
 * Создание или обновление единицы измерения
 */
function createOrUpdateMeasure($xmlId, $name, $code = '', $symbol = '') {
	// Поиск существующей единицы измерения
	$rsMeasure = CCatalogMeasure::getList(
		array(),
		array("XML_ID" => $xmlId),
		false,
		false,
		array("ID", "MEASURE_TITLE")
	);

	if ($arMeasure = $rsMeasure->Fetch()) {
		// Обновление существующей
		$arFields = array("MEASURE_TITLE" => $name);
		if (!empty($symbol)) {
			$arFields["SYMBOL"] = $symbol;
		}

		if (CCatalogMeasure::update($arMeasure["ID"], $arFields)) {
			writeLog("Единица измерения обновлена: $name");
			return $arMeasure["ID"];
		}
	} else {
		// Создание новой
		$arFields = array(
			"CODE" => !empty($code) ? $code : 0,
			"MEASURE_TITLE" => $name,
			"SYMBOL" => !empty($symbol) ? $symbol : $name,
			"XML_ID" => $xmlId
		);

		$measureId = CCatalogMeasure::add($arFields);
		if ($measureId) {
			writeLog("Единица измерения создана: $name (ID: $measureId)");
			return $measureId;
		}
	}

	return false;
}

/**
 * Рекурсивная обработка групп товаров
 */
function processGroupsRecursively($groups, $iblockId, $parentSectionId = 0) {
	foreach ($groups as $group) {
		$groupId = (string)$group->Ид;
		$groupName = (string)$group->Наименование;

		// Создание или обновление раздела
		$sectionId = createOrUpdateSection($iblockId, $groupId, $groupName, $parentSectionId);

		// Рекурсивная обработка подгрупп
		if (isset($group->Группы->Группа) && $sectionId) {
			processGroupsRecursively($group->Группы->Группа, $iblockId, $sectionId);
		}
	}
}

/**
 * Создание или обновление раздела
 */
function createOrUpdateSection($iblockId, $xmlId, $name, $parentId = 0) {
	// Поиск существующего раздела
	$rsSection = CIBlockSection::GetList(
		array(),
		array("IBLOCK_ID" => $iblockId, "XML_ID" => $xmlId),
		false,
		array("ID", "NAME")
	);

	$arFields = array(
		"NAME" => $name,
		"CODE" => generateCode($name),
		"IBLOCK_ID" => $iblockId,
		"ACTIVE" => "Y",
		"XML_ID" => $xmlId
	);

	if ($parentId > 0) {
		$arFields["IBLOCK_SECTION_ID"] = $parentId;
	}

	$bs = new CIBlockSection;

	if ($arSection = $rsSection->Fetch()) {
		// Обновление существующего
		if ($bs->Update($arSection["ID"], $arFields)) {
			writeLog("Раздел обновлен: $name");
			return $arSection["ID"];
		}
	} else {
		// Создание нового
		$sectionId = $bs->Add($arFields);
		if ($sectionId) {
			writeLog("Раздел создан: $name (ID: $sectionId)");
			return $sectionId;
		} else {
			writeLog("ERROR: Ошибка создания раздела: " . $bs->LAST_ERROR);
		}
	}

	return false;
}

/**
 * Обновление цен товара
 */
function updateProductPrices($elementId, $offer) {
	if (!isset($offer->Цены->Цена)) {
		return false;
	}

	foreach ($offer->Цены->Цена as $price) {
		$priceTypeId = (string)$price->ИдТипаЦены;
		$priceValue = (float)$price->ЦенаЗаЕдиницу;
		$currency = (string)$price->Валюта ?? 'RUB';

		// Поиск типа цены по XML_ID
		$rsPriceType = CPrice::GetList(
			array(),
			array("XML_ID" => $priceTypeId),
			false,
			false,
			array("ID")
		);

		if ($arPriceType = $rsPriceType->Fetch()) {
			// Обновление или создание цены
			$arFields = array(
				"PRODUCT_ID" => $elementId,
				"CATALOG_GROUP_ID" => $arPriceType["ID"],
				"PRICE" => $priceValue,
				"CURRENCY" => $currency
			);

			// Поиск существующей цены
			$rsPrice = CPrice::GetList(
				array(),
				array(
					"PRODUCT_ID" => $elementId,
					"CATALOG_GROUP_ID" => $arPriceType["ID"]
				)
			);

			if ($arPrice = $rsPrice->Fetch()) {
				CPrice::Update($arPrice["ID"], $arFields);
			} else {
				CPrice::Add($arFields);
			}

			writeLog("Цена обновлена для товара $elementId: $priceValue $currency");
		}
	}

	return true;
}

/**
 * Обновление количества товара
 */
function updateProductQuantity($elementId, $quantity) {
	$arFields = array(
		"ID" => $elementId,
		"QUANTITY" => $quantity
	);

	if (CCatalogProduct::Add($arFields, false)) {
		writeLog("Количество товара обновлено: ID=$elementId, количество=$quantity");
		return true;
	}

	return false;
}

/**
 * Обновление остатка товара на складе
 */
function updateProductQuantityByStorage($productXmlId, $storageXmlId, $quantity) {
	// Поиск товара
	$element = getElementByXmlId($productXmlId);
	if (!$element) {
		writeLog("WARNING: Товар не найден для остатка: $productXmlId");
		return false;
	}

	// Поиск склада
	global $DB;
	$query = "SELECT ID FROM b_catalog_store WHERE XML_ID = '" . $DB->ForSql($storageXmlId) . "'";
	$rsStore = $DB->Query($query);

	if ($arStore = $rsStore->Fetch()) {
		// Обновление остатка на складе
		$arFields = array(
			"PRODUCT_ID" => $element["ID"],
			"STORE_ID" => $arStore["ID"],
			"AMOUNT" => $quantity
		);

		// Поиск существующего остатка
		$rsAmount = CCatalogStoreProduct::GetList(
			array(),
			array(
				"PRODUCT_ID" => $element["ID"],
				"STORE_ID" => $arStore["ID"]
			)
		);

		if ($arAmount = $rsAmount->Fetch()) {
			CCatalogStoreProduct::Update($arAmount["ID"], array("AMOUNT" => $quantity));
		} else {
			CCatalogStoreProduct::Add($arFields);
		}

		writeLog("Остаток обновлен: товар=" . $element["ID"] . ", склад=" . $arStore["ID"] . ", количество=$quantity");
		return true;
	}

	return false;
}

/**
 * Получение кода свойства по XML_ID
 */
function getPropertyCodeByXmlId($xmlId, $iblockId) {
	$rsProperty = CIBlockProperty::GetList(
		array(),
		array("IBLOCK_ID" => $iblockId, "XML_ID" => $xmlId),
		false,
		false,
		array("ID", "CODE")
	);

	if ($arProperty = $rsProperty->Fetch()) {
		return $arProperty["CODE"];
	}

	return false;
}

/**
 * Очистка кэша инфоблоков
 */
function clearIBlockCache($iblockId) {
	// Очистка кэша элементов
	CIBlock::clearIblockTagCache($iblockId);

	// Очистка кэша разделов
	$GLOBALS["CACHE_MANAGER"]->ClearByTag("iblock_id_$iblockId");

	writeLog("Кэш инфоблока $iblockId очищен");
}

/**
 * Проверка и создание индексов для поиска
 */
function createSearchIndexes($iblockId) {
	// Переиндексация поиска
	CSearch::ReIndexAll(false, $iblockId, "iblock");

	writeLog("Поисковые индексы обновлены для ИБ $iblockId");
}

/**
 * Получение статистики импорта
 */
function getImportStatistics($iblockId) {
	// Подсчет элементов
	$rsElements = CIBlockElement::GetList(
		array(),
		array("IBLOCK_ID" => $iblockId),
		array(),
		false,
		array("ID")
	);
	$elementsCount = $rsElements->SelectedRowsCount();

	// Подсчет разделов
	$rsSections = CIBlockSection::GetList(
		array(),
		array("IBLOCK_ID" => $iblockId),
		false,
		array("ID")
	);
	$sectionsCount = $rsSections->SelectedRowsCount();

	writeLog("Статистика импорта: элементов=$elementsCount, разделов=$sectionsCount");

	return array(
		"elements" => $elementsCount,
		"sections" => $sectionsCount
	);
}

/**
 * Валидация XML файла
 */
function validateXMLFile($filePath) {
	libxml_use_internal_errors(true);

	$xml = simplexml_load_file($filePath);

	if ($xml === false) {
		$errors = libxml_get_errors();
		foreach ($errors as $error) {
			writeLog("XML ERROR: " . trim($error->message));
		}
		libxml_clear_errors();
		return false;
	}

	return true;
}

/**
 * Резервное копирование базы перед импортом
 */
function createBackup($tableName) {
	global $DB;

	$backupTable = $tableName . "_backup_" . date('Y_m_d_H_i_s');
	$query = "CREATE TABLE $backupTable AS SELECT * FROM $tableName";

	if ($DB->Query($query)) {
		writeLog("Создана резервная копия таблицы: $backupTable");
		return $backupTable;
	}

	return false;
}
?>