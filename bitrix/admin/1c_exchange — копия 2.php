<?php
/**
 * Обработчик обмена с 1С:ERP
 * Версия: 1.3 (исправлено)
 * Дата: 2025-01-21
 * Путь: /bitrix/admin/1c_exchange.php
 *
 * ИСПРАВЛЕНИЯ:
 * - Встроены все необходимые функции
 * - Исправлена структура XML для остатков
 * - Добавлена корректная обработка единиц измерения
 * - Улучшена обработка ошибок
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

// Отключаем вывод ошибок в браузер для корректной работы обмена
ini_set('display_errors', 0);
error_reporting(0);

// Увеличиваем лимиты для больших файлов
ini_set('max_execution_time', 300);
ini_set('memory_limit', '512M');

// Логирование для отладки
$logFile = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange.log";

function writeLog($message) {
	global $logFile;
	$timestamp = date('Y-m-d H:i:s');
	file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND | LOCK_EX);
}

// Подключение модулей
$requiredModules = ['catalog', 'iblock', 'sale'];
foreach ($requiredModules as $module) {
	if (!CModule::IncludeModule($module)) {
		writeLog("ERROR: Модуль $module не подключен");
		echo "failure\nМодуль $module не подключен";
		exit;
	}
}

// Получение параметров запроса
$type = $_GET["type"] ?? "";
$mode = $_GET["mode"] ?? "";
$filename = $_GET["filename"] ?? "";

writeLog("REQUEST: type=$type, mode=$mode, filename=$filename, method=".$_SERVER['REQUEST_METHOD']);

// Обработка запросов для каталога
if ($type == "catalog") {

	// Проверка авторизации
	if ($mode == "checkauth") {
		writeLog("Запрос авторизации");

		// Проверяем права администратора
		global $USER;
		if (!$USER->IsAdmin()) {
			writeLog("ERROR: Недостаточно прав доступа");
			echo "failure\nНедостаточно прав доступа";
			exit;
		}

		echo "success\n";
		echo "sessid\n";
		echo bitrix_sessid();
		writeLog("Авторизация успешна");
		exit;
	}

	// Инициализация обмена
	if ($mode == "init") {
		writeLog("Инициализация обмена");
		echo "zip=no\n";
		echo "file_limit=10485760\n"; // Увеличили до 10MB
		echo "version=2.0\n";
		writeLog("Инициализация завершена");
		exit;
	}

	// Загрузка файла
	if ($mode == "file") {
		writeLog("Начало загрузки файла: $filename");

		// Создаем директорию для загрузки
		$uploadDir = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_catalog/";
		if (!file_exists($uploadDir)) {
			if (!mkdir($uploadDir, 0755, true)) {
				writeLog("ERROR: Не удалось создать директорию $uploadDir");
				echo "failure\nНе удалось создать директорию для загрузки";
				exit;
			}
		}

		$uploadFile = $uploadDir . $filename;

		// Обработка разных способов передачи файла
		$fileUploaded = false;

		// Способ 1: Стандартный POST upload
		if (!empty($_FILES)) {
			foreach ($_FILES as $key => $file) {
				if (isset($file['tmp_name']) && $file['tmp_name'] && is_uploaded_file($file['tmp_name'])) {
					if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
						$fileUploaded = true;
						writeLog("Файл успешно загружен через \$_FILES[$key], размер: " . filesize($uploadFile) . " байт");
						break;
					}
				}
			}
		}

		// Способ 2: Raw POST data
		if (!$fileUploaded) {
			$rawData = file_get_contents('php://input');
			if (!empty($rawData)) {
				if (file_put_contents($uploadFile, $rawData)) {
					$fileUploaded = true;
					writeLog("Файл успешно загружен через raw data, размер: " . strlen($rawData) . " байт");
				} else {
					writeLog("ERROR: Не удалось записать файл через raw data");
				}
			}
		}

		if ($fileUploaded && file_exists($uploadFile)) {
			echo "success\n";
			writeLog("Загрузка файла $filename завершена успешно");
		} else {
			echo "failure\nОшибка загрузки файла";
			writeLog("ERROR: Загрузка файла $filename завершилась неудачей");
		}
		exit;
	}

	// Импорт файла
	if ($mode == "import") {
		writeLog("Начало импорта файла: $filename");

		$importFile = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_catalog/" . $filename;

		if (!file_exists($importFile)) {
			writeLog("ERROR: Файл для импорта не найден: $importFile");
			echo "failure\nФайл для импорта не найден: " . $filename;
			exit;
		}

		writeLog("Размер файла для импорта: " . filesize($importFile) . " байт");

		try {
			$result = false;

			// Определяем тип импортируемых данных по имени файла
			if (strpos($filename, 'priceLists_') === 0) {
				$result = importPriceLists($importFile);
			} elseif (strpos($filename, 'storages_') === 0) {
				$result = importStorages($importFile);
			} elseif (strpos($filename, 'propertiesGoods_') === 0) {
				$result = importPropertiesGoods($importFile);
			} elseif (strpos($filename, 'units_') === 0) {
				$result = importUnits($importFile);
			} elseif (strpos($filename, 'groups_') === 0) {
				$result = importGroups($importFile);
			} elseif (strpos($filename, 'goods_') === 0) {
				$result = importGoods($importFile);
			} elseif (strpos($filename, 'offers_') === 0) {
				$result = importOffers($importFile);
			} elseif (strpos($filename, 'rests_') === 0) {
				$result = importRests($importFile);
			} else {
				writeLog("WARNING: Неизвестный тип файла для импорта: $filename");
				// Попробуем определить по содержимому XML
				$result = importByContent($importFile);
			}

			if ($result) {
				echo "success\n";
				writeLog("Импорт файла $filename завершен успешно");
			} else {
				echo "failure\nОшибка импорта файла";
				writeLog("ERROR: Импорт файла $filename завершился ошибкой");
			}

		} catch (Exception $e) {
			writeLog("ERROR: Исключение при импорте: " . $e->getMessage());
			echo "failure\n" . $e->getMessage();
		}

		exit;
	}
}

/**
 * Импорт прайс-листов
 */
function importPriceLists($filePath) {
	writeLog("Импорт прайс-листов из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл прайс-листов");
		return false;
	}

	// Обработка прайс-листов
	if (isset($xml->Классификатор->ТипыЦен->ТипЦены)) {
		foreach ($xml->Классификатор->ТипыЦен->ТипЦены as $priceType) {
			$priceId = (string)$priceType->Ид;
			$priceName = (string)$priceType->Наименование;

			// Создание или обновление типа цены в Битрикс
			createOrUpdatePriceType($priceId, $priceName);
		}
	}

	writeLog("Прайс-листы импортированы успешно");
	return true;
}

/**
 * Импорт складов
 */
function importStorages($filePath) {
	writeLog("Импорт складов из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл складов");
		return false;
	}

	// Обработка складов
	if (isset($xml->Классификатор->Склады->Склад)) {
		foreach ($xml->Классификатор->Склады->Склад as $storage) {
			$storageId = (string)$storage->Ид;
			$storageName = (string)$storage->Наименование;

			// Создание или обновление склада в Битрикс
			createOrUpdateStorage($storageId, $storageName);
		}
	}

	writeLog("Склады импортированы успешно");
	return true;
}

/**
 * Импорт свойств товаров
 */
function importPropertiesGoods($filePath) {
	writeLog("Импорт свойств товаров из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл свойств товаров");
		return false;
	}

	$iblockId = getOrCreateCatalogIBlock();
	if (!$iblockId) {
		writeLog("ERROR: Не удалось получить ID инфоблока каталога");
		return false;
	}

	// Обработка свойств
	if (isset($xml->Классификатор->Свойства->Свойство)) {
		foreach ($xml->Классификатор->Свойства->Свойство as $property) {
			$propertyId = (string)$property->Ид;
			$propertyName = (string)$property->Наименование;
			$propertyType = (string)$property->ТипЗначений ?? 'Строка';

			// Создание или обновление свойства в ИБ
			createOrUpdateIBlockProperty($iblockId, $propertyId, $propertyName, $propertyType);
		}
	}

	writeLog("Свойства товаров импортированы успешно");
	return true;
}

/**
 * Импорт единиц измерения
 */
function importUnits($filePath) {
	writeLog("Импорт единиц измерения из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл единиц измерения");
		return false;
	}

	// Обработка единиц измерения
	if (isset($xml->Классификатор->ЕдиницыИзмерения->ЕдиницаИзмерения)) {
		foreach ($xml->Классификатор->ЕдиницыИзмерения->ЕдиницаИзмерения as $unit) {
			$unitId = (string)$unit->Ид;
			$unitName = (string)$unit->НаименованиеПолное;
			$unitCode = (string)$unit->Код;
			$unitSymbol = (string)$unit->НаименованиеКраткое;

			// Создание или обновление единицы измерения в каталоге
			createOrUpdateMeasure($unitId, $unitName, $unitCode, $unitSymbol);
		}
	}

	writeLog("Единицы измерения импортированы успешно");
	return true;
}

/**
 * Импорт групп товаров
 */
function importGroups($filePath) {
	writeLog("Импорт групп товаров из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл групп товаров");
		return false;
	}

	// Проверяем существование типа ИБ для 1С
	$arIBlockType = CIBlockType::GetList(array(), array("ID" => "1c_catalog"))->Fetch();
	if (!$arIBlockType) {
		createIBlockType();
	}

	// Находим или создаем ИБ каталога
	$iblockId = getOrCreateCatalogIBlock();
	if ($iblockId > 0) {
		setIBlockPermissions($iblockId);

		// Обработка групп товаров (разделов)
		if (isset($xml->Классификатор->Группы->Группа)) {
			processGroupsRecursively($xml->Классификатор->Группы->Группа, $iblockId, 0);
		}

		writeLog("ИБ каталога настроен с ID: $iblockId");
	}

	writeLog("Группы товаров импортированы успешно");
	return true;
}

/**
 * Импорт товаров (ОСНОВНАЯ ФУНКЦИЯ)
 */
function importGoods($filePath) {
	writeLog("Импорт товаров из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл товаров");
		return false;
	}

	$iblockId = getOrCreateCatalogIBlock();
	if (!$iblockId) {
		writeLog("ERROR: Не удалось получить ID инфоблока каталога");
		return false;
	}

	// Подключение к каталогу
	$arCatalog = CCatalog::GetByID($iblockId);
	if (!$arCatalog) {
		$arFields = array('IBLOCK_ID' => $iblockId);
		if (CCatalog::Add($arFields)) {
			writeLog("Инфоблок $iblockId подключен к модулю Каталог");
		} else {
			writeLog("ERROR: Не удалось подключить инфоблок к каталогу");
			return false;
		}
	}

	$importedCount = 0;
	$errorCount = 0;

	// Обработка товаров
	if (isset($xml->Каталог->Товары->Товар)) {
		foreach ($xml->Каталог->Товары->Товар as $product) {
			try {
				if (importSingleProduct($product, $iblockId)) {
					$importedCount++;
				} else {
					$errorCount++;
				}
			} catch (Exception $e) {
				writeLog("ERROR: Ошибка импорта товара: " . $e->getMessage());
				$errorCount++;
			}
		}
	}

	writeLog("Товары импортированы: успешно - $importedCount, с ошибками - $errorCount");
	return ($importedCount > 0 || $errorCount == 0);
}

/**
 * Импорт предложений (торговых предложений)
 */
function importOffers($filePath) {
	writeLog("Импорт предложений из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл предложений");
		return false;
	}

	// Обработка предложений с ценами
	if (isset($xml->ПакетПредложений->Предложения->Предложение)) {
		foreach ($xml->ПакетПредложений->Предложения->Предложение as $offer) {
			$offerId = (string)$offer->Ид;
			$productId = (string)$offer->Артикул ?? $offerId;

			// Поиск элемента в каталоге
			$element = getElementByXmlId($productId);
			if ($element) {
				// Обновление цен
				updateProductPrices($element['ID'], $offer);

				// Обновление остатков (если есть)
				if (isset($offer->Количество)) {
					updateProductQuantity($element['ID'], (float)$offer->Количество);
				}
			} else {
				writeLog("WARNING: Товар не найден для предложения: $offerId");
			}
		}
	}

	writeLog("Предложения импортированы успешно");
	return true;
}

/**
 * Импорт остатков
 */
function importRests($filePath) {
	writeLog("Импорт остатков из файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл остатков");
		return false;
	}

	// Обработка остатков
	if (isset($xml->ПакетПредложений->Предложения->Предложение)) {
		foreach ($xml->ПакетПредложений->Предложения->Предложение as $offer) {
			$offerId = (string)$offer->Ид;

			if (isset($offer->Остатки->Остаток)) {
				foreach ($offer->Остатки->Остаток as $rest) {
					$storageId = (string)$rest->Склад->Ид;
					$quantity = (float)$rest->Склад->Количество;

					// Обновление остатка товара на складе
					updateProductQuantityByStorage($offerId, $storageId, $quantity);
				}
			}
		}
	}

	writeLog("Остатки импортированы успешно");
	return true;
}

/**
 * Определение типа импорта по содержимому файла
 */
function importByContent($filePath) {
	writeLog("Попытка определения типа импорта по содержимому файла: $filePath");

	$xml = simplexml_load_file($filePath);
	if ($xml === false) {
		writeLog("ERROR: Невалидный XML файл");
		return false;
	}

	// Анализируем структуру XML
	if (isset($xml->Каталог->Товары)) {
		return importGoods($filePath);
	} elseif (isset($xml->ПакетПредложений->Предложения)) {
		return importOffers($filePath);
	} elseif (isset($xml->Классификатор->Группы)) {
		return importGroups($filePath);
	} elseif (isset($xml->Классификатор->Свойства)) {
		return importPropertiesGoods($filePath);
	}

	writeLog("WARNING: Не удалось определить тип импорта по содержимому файла");
	return true; // Считаем успешным для неизвестных типов
}

/**
 * Импорт одного товара
 */
function importSingleProduct($product, $iblockId) {
	$productId = (string)$product->Ид;
	$productName = (string)$product->Наименование;
	$productCode = generateCode($productName);
	$groupId = (string)$product->Группы->Ид ?? '';

	writeLog("Импорт товара: $productName (ID: $productId)");

	// Поиск существующего элемента
	$existingElement = getElementByXmlId($productId, $iblockId);

	$arFields = array(
		'NAME' => $productName,
		'CODE' => $productCode,
		'IBLOCK_ID' => $iblockId,
		'ACTIVE' => 'Y',
		'XML_ID' => $productId,
	);

	// Поиск раздела по группе
	if ($groupId) {
		$sectionId = getSectionByXmlId($groupId, $iblockId);
		if ($sectionId) {
			$arFields['IBLOCK_SECTION_ID'] = $sectionId;
		}
	}

	// Обработка свойств
	$arProps = array();
	if (isset($product->ЗначенияСвойств->ЗначенияСвойства)) {
		foreach ($product->ЗначенияСвойств->ЗначенияСвойства as $propValue) {
			$propId = (string)$propValue->Ид;
			$propVal = (string)$propValue->Значение;

			$propCode = getPropertyCodeByXmlId($propId, $iblockId);
			if ($propCode) {
				$arProps[$propCode] = $propVal;
			}
		}
	}

	$arFields['PROPERTY_VALUES'] = $arProps;

	$el = new CIBlockElement();

	if ($existingElement) {
		// Обновление существующего элемента
		if ($el->Update($existingElement['ID'], $arFields)) {
			writeLog("Товар обновлен: $productName");
			return true;
		} else {
			writeLog("ERROR: Ошибка обновления товара: " . $el->LAST_ERROR);
			return false;
		}
	} else {
		// Создание нового элемента
		$elementId = $el->Add($arFields);
		if ($elementId) {
			writeLog("Товар создан: $productName (ID: $elementId)");
			return true;
		} else {
			writeLog("ERROR: Ошибка создания товара: " . $el->LAST_ERROR);
			return false;
		}
	}
}

// === ВСПОМОГАТЕЛЬНЫЕ ФУНКЦИИ ===

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
			"CODE" => !empty($code) ? intval(trim($code)) : 0,
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
 * Создание или обновление типа цены
 */
function createOrUpdatePriceType($xmlId, $name) {
	// Поиск существующего типа цены
	$rsPrice = CCatalogGroup::GetList(
		array(),
		array("XML_ID" => $xmlId),
		false,
		false,
		array("ID", "NAME")
	);

	if ($arPrice = $rsPrice->Fetch()) {
		// Обновление существующего
		$arFields = array("NAME" => $name);
		if (CCatalogGroup::Update($arPrice["ID"], $arFields)) {
			writeLog("Тип цены обновлен: $name");
			return $arPrice["ID"];
		}
	} else {
		// Создание нового
		$arFields = array(
			"NAME" => $name,
			"BASE" => "N",
			"SORT" => 100,
			"XML_ID" => $xmlId
		);

		$priceId = CCatalogGroup::Add($arFields);
		if ($priceId) {
			writeLog("Тип цены создан: $name (ID: $priceId)");
			return $priceId;
		}
	}

	return false;
}

/**
 * Поиск элемента по XML_ID
 */
function getElementByXmlId($xmlId, $iblockId = null) {
	$filter = array('XML_ID' => $xmlId);
	if ($iblockId) {
		$filter['IBLOCK_ID'] = $iblockId;
	}

	$rsElement = CIBlockElement::GetList(array(), $filter, false, false, array('ID', 'NAME', 'XML_ID'));
	return $rsElement->Fetch();
}

/**
 * Поиск раздела по XML_ID
 */
function getSectionByXmlId($xmlId, $iblockId) {
	$rsSection = CIBlockSection::GetList(
		array(),
		array('XML_ID' => $xmlId, 'IBLOCK_ID' => $iblockId),
		false,
		array('ID', 'NAME')
	);

	$section = $rsSection->Fetch();
	return $section ? $section['ID'] : false;
}

/**
 * Генерация символьного кода
 */
function generateCode($name) {
	return CUtil::translit($name, "ru", array(
		"max_len" => 100,
		"change_case" => "L",
		"replace_space" => "_",
		"replace_other" => "_"
	));
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
 * Создание типа инфоблока
 */
function createIBlockType() {
	$obBlocktype = new CIBlockType;
	$arFields = array(
		"ID" => "1c_catalog",
		"SECTIONS" => "Y",
		"IN_RSS" => "N",
		"SORT" => 100,
		"LANG" => array(
			"ru" => array(
				"NAME" => "1С Каталог",
				"SECTION_NAME" => "Разделы",
				"ELEMENT_NAME" => "Товары"
			),
			"en" => array(
				"NAME" => "1C Catalog",
				"SECTION_NAME" => "Sections",
				"ELEMENT_NAME" => "Products"
			)
		)
	);

	if ($obBlocktype->Add($arFields)) {
		writeLog("Тип ИБ 1c_catalog создан");
		return true;
	} else {
		writeLog("ERROR: Ошибка создания типа ИБ: " . $obBlocktype->LAST_ERROR);
		return false;
	}
}

/**
 * Получение или создание ИБ каталога
 */
function getOrCreateCatalogIBlock() {
	// Ищем существующий ИБ
	$rsIBlock = CIBlock::GetList(
		array(),
		array(
			'TYPE' => '1c_catalog',
			'CODE' => '1c_catalog'
		)
	);

	if ($arIBlock = $rsIBlock->Fetch()) {
		return $arIBlock['ID'];
	}

	// Создаем новый ИБ
	$ib = new CIBlock;
	$arFields = Array(
		"ACTIVE" => "Y",
		"NAME" => "1С Каталог товаров",
		"CODE" => "1c_catalog",
		"IBLOCK_TYPE_ID" => "1c_catalog",
		"SITE_ID" => SITE_ID,
		"SORT" => 500,
		"GROUP_ID" => array("1" => "X", "2" => "R"),
		"FIELDS" => array(
			"CODE" => array(
				"IS_REQUIRED" => "Y",
				"DEFAULT_VALUE" => array(
					"UNIQUE" => "Y",
					"TRANSLITERATION" => "Y",
					"TRANS_LEN" => "30",
					"TRANS_CASE" => "L",
					"TRANS_SPACE" => "_"
				)
			)
		),
		"LIST_PAGE_URL" => "/catalog/",
		"DETAIL_PAGE_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
		"SECTION_PAGE_URL" => "/catalog/#SECTION_CODE#/",
		"INDEX_ELEMENT" => "Y",
		"INDEX_SECTION" => "Y",
		"WORKFLOW" => "N",
		"BIZPROC" => "N",
		"SECTION_CHOOSER" => "L",
		"LIST_MODE" => "",
		"RIGHTS_MODE" => "S",
		"VERSION" => 1
	);

	$iblockId = $ib->Add($arFields);
	if ($iblockId) {
		writeLog("ИБ каталога создан с ID: $iblockId");
		return $iblockId;
	} else {
		writeLog("ERROR: Ошибка создания ИБ: " . $ib->LAST_ERROR);
		return false;
	}
}

/**
 * Установка прав доступа к ИБ
 */
function setIBlockPermissions($iblockId) {
	$ib = new CIBlock;
	$arFields = array(
		"GROUP_ID" => array(
			"1" => "X", // Администраторы
			"2" => "R"  // Все пользователи
		),
		"RIGHTS_MODE" => "S"
	);

	if ($ib->Update($iblockId, $arFields)) {
		writeLog("Права доступа к ИБ $iblockId обновлены");
		return true;
	} else {
		writeLog("ERROR: Ошибка обновления прав ИБ: " . $ib->LAST_ERROR);
		return false;
	}
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
		$rsPriceType = CCatalogGroup::GetList(
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

// Если не обработано ни одно условие
writeLog("ERROR: Неизвестный тип запроса: type=$type, mode=$mode");
echo "failure\nНеизвестный тип запроса: type=$type, mode=$mode";
?>