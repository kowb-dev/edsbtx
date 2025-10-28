<?php
/**
 * Navigation Configuration for Cable Tables Page
 * Конфигурация навигации для страницы таблиц кабелей
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Useful Information Navigation Configuration
$arUsefulInfoNavigation = [
	[
		'NAME' => 'Статьи',
		'URL' => '/polezno-znat/',
		'ACTIVE' => false,
		'ICON' => 'ph-article',
		'DESCRIPTION' => 'Профессиональные статьи и руководства'
	],
	[
		'NAME' => 'Таблицы токовых нагрузок медных кабелей',
		'URL' => '/stati-tablitsy-nagruzok/',
		'ACTIVE' => true, // Current page
		'ICON' => 'ph-table',
		'DESCRIPTION' => 'Справочные таблицы нагрузок'
	],
	[
		'NAME' => 'Схемы распайки кабелей',
		'URL' => '/shemy-raspajki-kabelej/',
		'ACTIVE' => false,
		'ICON' => 'ph-circuitry',
		'DESCRIPTION' => 'Схемы подключения и распайки'
	],
	[
		'NAME' => 'Классификация типов нагрузки контактов',
		'URL' => '/klassifikatsiya-tipov-nagruzki-kontaktov/',
		'ACTIVE' => false,
		'ICON' => 'ph-tree-structure',
		'DESCRIPTION' => 'Типы и характеристики нагрузок'
	]
];

// Local Navigation Configuration (for cable tables)
$arLocalNavigation = [
	[
		'id' => 'xtrem',
		'title' => 'Кабель XTREM H07RN-F',
		'anchor' => 'xtrem',
		'icon' => 'ph-lightning',
		'description' => 'Европейский стандарт качества'
	],
	[
		'id' => 'kgtp',
		'title' => 'Кабель КГтп-ХЛ',
		'anchor' => 'kgtp',
		'icon' => 'ph-snowflake',
		'description' => 'Морозостойкий кабель'
	],
	[
		'id' => 'pugv',
		'title' => 'Провод ПуГВ',
		'anchor' => 'pugv',
		'icon' => 'ph-plug',
		'description' => 'Универсальный провод'
	]
];

/**
 * Prepare navigation data for useful info component
 */
function prepareUsefulInfoNavigationData($usefulInfoNav, $localNav = []) {
	return [
		'NAVIGATION' => $usefulInfoNav,
		'QUICK_NAV' => $localNav
	];
}

/**
 * Get current page URL for active state detection
 */
function getCurrentPageUrl() {
	global $APPLICATION;
	return $APPLICATION->GetCurUri();
}

/**
 * Auto-detect active page in navigation
 */
function updateActiveNavigation(&$navigation) {
	$currentUrl = getCurrentPageUrl();

	foreach ($navigation as &$item) {
		if (strpos($currentUrl, $item['URL']) !== false) {
			$item['ACTIVE'] = true;
		} else {
			$item['ACTIVE'] = false;
		}
	}
	unset($item);
}

// Auto-update active states
updateActiveNavigation($arUsefulInfoNavigation);

?>