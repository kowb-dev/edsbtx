<?php
/**
 * Конфигурационный файл для обмена с 1С
 * Версия: 1.0
 * Дата: 2025-01-21
 *
 * Включить в основной файл:
 * require_once("1c_exchange_config_v1.0.php");
 */

// Основные настройки обмена
define('EXCHANGE_LOG_ENABLED', true);
define('EXCHANGE_LOG_FILE', $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange.log");
define('EXCHANGE_UPLOAD_DIR', $_SERVER["DOCUMENT_ROOT"]."/upload/1c_catalog/");

// Настройки инфоблока
define('IBLOCK_TYPE_ID', '1c_catalog');
define('IBLOCK_CODE', '1c_catalog');
define('IBLOCK_NAME', '1С Каталог товаров');

// Лимиты и таймауты
define('EXCHANGE_MAX_EXECUTION_TIME', 600); // 10 минут
define('EXCHANGE_MEMORY_LIMIT', '1024M');
define('EXCHANGE_FILE_LIMIT', 50 * 1024 * 1024); // 50MB

// Настройки каталога
define('CATALOG_USE_OFFERS', true); // Использовать торговые предложения
define('CATALOG_PRICE_CODE', 'BASE'); // Код базовой цены
define('CATALOG_QUANTITY_TRACE', 'Y'); // Включить количественный учет
define('CATALOG_CAN_BUY_ZERO', 'Y'); // Разрешить покупку при отсутствии товара

// Настройки импорта
$EXCHANGE_CONFIG = array(
	// Общие настройки
	'ENCODING' => 'UTF-8',
	'FULL_IMPORT_MODE' => false, // true - полный импорт, false - обновление
	'CREATE_BACKUP' => true, // Создавать резервные копии
	'CLEAR_CACHE' => true, // Очищать кэш после импорта
	'UPDATE_SEARCH_INDEX' => true, // Обновлять поисковый индекс

	// Настройки элементов
	'ELEMENTS' => array(
		'TRANSLIT_CODE' => true, // Транслитерировать символьные коды
		'CHECK_DUPLICATES' => true, // Проверять дубликаты по XML_ID
		'ACTIVATE_NEW' => true, // Активировать новые элементы
		'DEACTIVATE_MISSING' => false, // Деактивировать отсутствующие элементы
		'MAX_BATCH_SIZE' => 100, // Максимальный размер пакета для импорта
	),

	// Настройки разделов
	'SECTIONS' => array(
		'TRANSLIT_CODE' => true,
		'CHECK_DUPLICATES' => true,
		'ACTIVATE_NEW' => true,
		'DELETE_MISSING' => false,
	),

	// Настройки свойств
	'PROPERTIES' => array(
		'CREATE_NEW' => true, // Создавать новые свойства
		'UPDATE_EXISTING' => true, // Обновлять существующие
		'PROPERTY_PREFIX' => 'PROP_', // Префикс для кодов свойств
	),

	// Настройки цен
	'PRICES' => array(
		'DEFAULT_CURRENCY' => 'RUB',
		'CREATE_PRICE_TYPES' => true,
		'UPDATE_EXISTING_PRICES' => true,
		'ROUND_PRECISION' => 2, // Количество знаков после запятой
	),

	// Настройки складов
	'STORES' => array(
		'CREATE_NEW_STORES' => true,
		'DEFAULT_STORE_ACTIVE' => true,
		'SYNC_QUANTITIES' => true,
	),

	// Настройки обработки ошибок
	'ERROR_HANDLING' => array(
		'STOP_ON_ERROR' => false, // Остановка при первой ошибке
		'LOG_LEVEL' => 'ALL', // ALL, ERRORS, WARNINGS
		'EMAIL_NOTIFICATIONS' => false, // Отправлять уведомления на email
		'EMAIL_ADDRESS' => 'admin@example.com',
	),

	// Фильтры импорта
	'FILTERS' => array(
		'IMPORT_ONLY_ACTIVE' => false, // Импортировать только активные товары из 1С
		'SKIP_EMPTY_NAMES' => true, // Пропускать элементы без названий
		'MIN_PRICE' => 0, // Минимальная цена для импорта
		'ALLOWED_GROUPS' => array(), // Разрешенные группы товаров (пусто = все)
		'FORBIDDEN_GROUPS' => array(), // Запрещенные группы товаров
	),

	// Соответствие типов данных
	'DATA_MAPPING' => array(
		'1C_PROPERTY_TYPES' => array(
			'Строка' => 'S',
			'Число' => 'N',
			'Логический' => 'L',
			'Дата' => 'S',
			'Справочник' => 'L',
		),
		'UNIT_MAPPING' => array(
			'шт' => 'PCE',
			'кг' => 'KGM',
			'м' => 'MTR',
			'л' => 'LTR',
		),
	),

	// Настройки производительности
	'PERFORMANCE' => array(
		'USE_DELAYED_INDEXING' => true, // Отложенная индексация
		'BATCH_SIZE' => 50, // Размер пакета для обработки
		'USE_MYSQL_OPTIMIZATION' => true, // Оптимизация MySQL запросов
		'DISABLE_EVENTS' => true, // Отключить события Битрикс при импорте
	),

	// Пути к файлам (шаблоны)
	'FILE_PATTERNS' => array(
		'GOODS' => '/^goods_.*\.xml$/',
		'OFFERS' => '/^offers_.*\.xml$/',
		'RESTS' => '/^rests_.*\.xml$/',
		'GROUPS' => '/^groups_.*\.xml$/',
		'PROPERTIES' => '/^propertiesGoods_.*\.xml$/',
		'UNITS' => '/^units_.*\.xml$/',
		'STORAGES' => '/^storages_.*\.xml$/',
		'PRICELISTS' => '/^priceLists_.*\.xml$/',
	),
);

// Функция получения конфигурации
function getExchangeConfig($key = null) {
	global $EXCHANGE_CONFIG;

	if ($key === null) {
		return $EXCHANGE_CONFIG;
	}

	$keys = explode('.', $key);
	$value = $EXCHANGE_CONFIG;

	foreach ($keys as $k) {
		if (isset($value[$k])) {
			$value = $value[$k];
		} else {
			return null;
		}
	}

	return $value;
}

// Функция установки конфигурации
function setExchangeConfig($key, $value) {
	global $EXCHANGE_CONFIG;

	$keys = explode('.', $key);
	$config = &$EXCHANGE_CONFIG;

	foreach ($keys as $k) {
		if (!isset($config[$k])) {
			$config[$k] = array();
		}
		$config = &$config[$k];
	}

	$config = $value;
}

// Настройка лимитов PHP
function setExchangeLimits() {
	ini_set('max_execution_time', EXCHANGE_MAX_EXECUTION_TIME);
	ini_set('memory_limit', EXCHANGE_MEMORY_LIMIT);
	ini_set('post_max_size', EXCHANGE_FILE_LIMIT);
	ini_set('upload_max_filesize', EXCHANGE_FILE_LIMIT);
}

// Проверка настроек системы
function checkSystemRequirements() {
	$requirements = array(
		'PHP Version >= 7.4' => version_compare(PHP_VERSION, '7.4.0', '>='),
		'SimpleXML Extension' => extension_loaded('simplexml'),
		'MBString Extension' => extension_loaded('mbstring'),
		'Upload Directory Writable' => is_writable(dirname(EXCHANGE_UPLOAD_DIR)),
		'Log Directory Writable' => is_writable(dirname(EXCHANGE_LOG_FILE)),
	);

	$allOk = true;
	foreach ($requirements as $requirement => $status) {
		if (!$status) {
			writeLog("SYSTEM ERROR: $requirement - NOT OK");
			$allOk = false;
		}
	}

	return $allOk;
}

// Настройки отладки
$DEBUG_CONFIG = array(
	'ENABLE_DEBUG' => false, // Включить режим отладки
	'LOG_SQL_QUERIES' => false, // Логировать SQL запросы
	'LOG_MEMORY_USAGE' => false, // Логировать использование памяти
	'BENCHMARK_OPERATIONS' => false, // Замерять время операций
	'SAVE_IMPORTED_FILES' => true, // Сохранять импортированные файлы
	'DEBUG_EMAIL' => 'developer@example.com',
);

// Функции отладки
function debugLog($message, $type = 'DEBUG') {
	global $DEBUG_CONFIG;

	if ($DEBUG_CONFIG['ENABLE_DEBUG']) {
		$memory = '';
		if ($DEBUG_CONFIG['LOG_MEMORY_USAGE']) {
			$memory = ' [MEM: ' . formatBytes(memory_get_usage()) . ']';
		}

		writeLog("[$type] $message$memory");
	}
}

function formatBytes($bytes, $precision = 2) {
	$units = array('B', 'KB', 'MB', 'GB', 'TB');

	for ($i = 0; $bytes > 1024; $i++) {
		$bytes /= 1024;
	}

	return round($bytes, $precision) . ' ' . $units[$i];
}

function benchmarkStart($operation) {
	global $DEBUG_CONFIG, $BENCHMARK_DATA;

	if ($DEBUG_CONFIG['BENCHMARK_OPERATIONS']) {
		$BENCHMARK_DATA[$operation] = microtime(true);
	}
}

function benchmarkEnd($operation) {
	global $DEBUG_CONFIG, $BENCHMARK_DATA;

	if ($DEBUG_CONFIG['BENCHMARK_OPERATIONS'] && isset($BENCHMARK_DATA[$operation])) {
		$time = microtime(true) - $BENCHMARK_DATA[$operation];
		debugLog("Операция '$operation' выполнена за " . round($time, 4) . " сек", 'BENCHMARK');
		unset($BENCHMARK_DATA[$operation]);
	}
}
?>