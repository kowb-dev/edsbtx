<?php
/**
 * Configuration file for Cable Schemes page
 * Конфигурация страницы схем распайки кабелей
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Page configuration
$pageConfig = [
	'title' => 'Схемы распайки кабелей',
	'description' => 'Профессиональные схемы распайки балансных, небалансных, инсертных кабелей и DMX. Подробные диаграммы подключения для сценического и студийного оборудования.',
	'keywords' => 'схемы распайки, кабели, XLR, DMX, балансные кабели, небалансные кабели, инсертные провода',
	'breadcrumbs' => [
		['name' => 'Главная', 'url' => '/'],
		['name' => 'Полезно знать', 'url' => '/stati-tablitsy-nagruzok/'],
		['name' => 'Схемы распайки кабелей', 'url' => '']
	]
];

// Cable scheme categories
$schemeCategories = [
	'balanced' => [
		'id' => 'balanced',
		'title' => 'Схемы распайки балансных кабелей',
		'short_title' => 'Балансные кабели',
		'anchor' => 'bal',
		'icon' => 'ph-plugs-connected',
		'image' => '/images/schemy-raspaiki/scheme-balanced.jpg',
		'description' => 'Схемы подключения балансных аудиокабелей XLR3 для профессионального звукового оборудования. Правильная распайка обеспечивает максимальное качество сигнала и защиту от помех.',
		'alt' => 'Схемы распайки балансных кабелей XLR3'
	],
	'unbalanced' => [
		'id' => 'unbalanced',
		'title' => 'Схемы распайки небалансных кабелей',
		'short_title' => 'Небалансные кабели',
		'anchor' => 'nbal',
		'icon' => 'ph-plugs-connected',
		'image' => '/images/schemy-raspaiki/scheme-unbalanced.jpg',
		'description' => 'Диаграммы подключения небалансных аудиокабелей для различных типов разъемов. Схемы для работы с бытовым и полупрофессиональным оборудованием.',
		'alt' => 'Схемы распайки небалансных кабелей'
	],
	'insert' => [
		'id' => 'insert',
		'title' => 'Схемы распайки инсертных проводов',
		'short_title' => 'Инсертные провода',
		'anchor' => 'insrt',
		'icon' => 'ph-plugs-connected',
		'image' => '/images/schemy-raspaiki/scheme-insert.jpg',
		'description' => 'Специальные схемы для инсертных кабелей, используемых для подключения внешних процессоров эффектов к микшерным пультам.',
		'alt' => 'Схемы распайки инсертных проводов'
	],
	'dmx' => [
		'id' => 'dmx',
		'title' => 'Схемы распайки кабеля DMX',
		'short_title' => 'Кабель DMX',
		'anchor' => 'dmx',
		'icon' => 'ph-plugs-connected',
		'image' => '/images/schemy-raspaiki/scheme-dmx.webp',
		'description' => 'Схемы подключения DMX512 кабелей для управления световым оборудованием. Правильная распайка 5-пинового XLR для надежной передачи цифрового сигнала.',
		'alt' => 'Схемы распайки кабеля DMX512',
		'has_text_content' => true, // Флаг для DMX секции с текстовым контентом
		'schemes_data' => [
			[
				'title' => '1. DMX512-КАБЕЛЬ 5-КОНТ. ПОЛНАЯ РАСПАЙКА:',
				'subtitle' => '(В СООТВЕТСТВИИ С USITT1990)',
				'connections' => [
					['from' => 'XLR 5-КОНТ.', 'to' => 'XLR 5-КОНТ.'],
					['from' => 'ПИН 1', 'to' => 'ПИН 1: ЗЕМЛЯ/ЭКРАН'],
					['from' => 'ПИН 2', 'to' => 'ПИН 2: Данные - 1'],
					['from' => 'ПИН 3', 'to' => 'ПИН 3: Данные + 1'],
					['from' => 'ПИН 4', 'to' => 'ПИН 4: ДАННЫЕ -2/ КАНАЛ ОБРАТНОГО СИГНАЛА'],
					['from' => 'ПИН 5', 'to' => 'ПИН 5: ДАННЫЕ +2/ КАНАЛ ОБРАТНОГО СИГНАЛА']
				]
			],
			[
				'title' => '2. DMX-КАБЕЛЬ 5-КОНТ. НЕПОЛНАЯ РАСПАЙКА:',
				'subtitle' => '',
				'connections' => [
					['from' => 'XLR 5-КОНТ.', 'to' => 'XLR 5-КОНТ.'],
					['from' => 'ПИН 1', 'to' => 'ПИН 1: ЗЕМЛЯ/ЭКРАН'],
					['from' => 'ПИН 2', 'to' => 'ПИН 2: ДАННЫЕ -'],
					['from' => 'ПИН 3', 'to' => 'ПИН 3: ДАННЫЕ +'],
					['from' => 'ПИН 4', 'to' => 'N.C.'],
					['from' => 'ПИН 5', 'to' => 'N.C.']
				]
			],
			[
				'title' => '3. DMX-АДАПТЕРНЫЙ КАБЕЛЬ, НЕПОЛНАЯ РАСПАЙКА:',
				'subtitle' => '(В СООТВЕТСТВИИ С USITT1990)',
				'connections' => [
					['from' => 'XLR 5-КОНТ.', 'to' => 'XLR 3-КОНТ.'],
					['from' => 'ПИН 1', 'to' => 'ПИН 1: ЗЕМЛЯ/ЭКРАН'],
					['from' => 'ПИН 2', 'to' => 'ПИН 2: ДАННЫЕ -'],
					['from' => 'ПИН 3', 'to' => 'ПИН 3: ДАННЫЕ +'],
					['from' => 'ПИН 4', 'to' => 'N.C.'],
					['from' => 'ПИН 5', 'to' => 'N.C.']
				]
			],
			[
				'title' => '4. DMX-КАБЕЛЬ 3-КОНТ. НЕПОЛНАЯ РАСПАЙКА:',
				'subtitle' => '(В СООТВЕТСТВИИ С USITT1990)',
				'connections' => [
					['from' => 'XLR 3-КОНТ.', 'to' => 'XLR 3-КОНТ.'],
					['from' => 'ПИН 1', 'to' => 'ПИН 1: ЗЕМЛЯ/ЭКРАН'],
					['from' => 'ПИН 2', 'to' => 'ПИН 2: ДАННЫЕ -'],
					['from' => 'ПИН 3', 'to' => 'ПИН 3: ДАННЫЕ +']
				]
			]
		]
	]
];

// Navigation configuration for useful info sections
$usefulInfoNavigation = [
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
		'ACTIVE' => false,
		'ICON' => 'ph-table',
		'DESCRIPTION' => 'Справочные таблицы нагрузок'
	],
	[
		'NAME' => 'Схемы распайки кабелей',
		'URL' => '/shemy-raspajki-kabelej/',
		'ACTIVE' => true, // Активна текущая страница
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

// Quick navigation for schemes (for sidebar)
$quickNavigation = [];
foreach ($schemeCategories as $scheme) {
	$quickNavigation[] = [
		'id' => $scheme['id'],
		'title' => $scheme['short_title'],
		'anchor' => $scheme['anchor'],
		'icon' => $scheme['icon']
	];
}

// Related links for sidebar
$relatedLinks = [
	'calculators' => [
		'title' => 'Калькуляторы',
		'url' => '/calculators/',
		'description' => 'Расчет сечения проводов, падения напряжения'
	],
	'products' => [
		'title' => 'О продукции',
		'url' => '/about-products/',
		'description' => 'Подробная информация о нашем оборудовании'
	],
	'useful' => [
		'title' => 'Полезно знать',
		'url' => '/stati-tablitsy-nagruzok/',
		'description' => 'Техническая документация и статьи'
	],
	'contacts' => [
		'title' => 'Контакты',
		'url' => '/contacts/',
		'description' => 'Связаться с нашими специалистами'
	],
	'delivery' => [
		'title' => 'Оплата и доставка',
		'url' => '/payment-delivery/',
		'description' => 'Условия заказа и доставки'
	]
];

// Template paths
$templatePath = '/local/templates/' . SITE_TEMPLATE_ID;
if (!defined('TEMPLATE_PATH')) {
	define('TEMPLATE_PATH', $templatePath);
}

// Current page URL for canonical
$currentUrl = $APPLICATION->GetCurUri();

/**
 * Generate breadcrumbs HTML
 * @param array $breadcrumbs
 * @return string
 */
function generateBreadcrumbs($breadcrumbs) {
	$html = '<nav class="edsys-schemes__breadcrumbs" aria-label="Навигация">';
	$html .= '<ol class="edsys-schemes__breadcrumb-list">';

	foreach ($breadcrumbs as $index => $item) {
		$isLast = $index === count($breadcrumbs) - 1;
		$html .= '<li class="edsys-schemes__breadcrumb-item">';

		if ($isLast || empty($item['url'])) {
			$html .= '<span class="edsys-schemes__breadcrumb-current">' . htmlspecialchars($item['name']) . '</span>';
		} else {
			$html .= '<a href="' . htmlspecialchars($item['url']) . '" class="edsys-schemes__breadcrumb-link">' . htmlspecialchars($item['name']) . '</a>';
		}

		if (!$isLast) {
			$html .= '<i class="ph ph-thin ph-caret-right"></i>';
		}

		$html .= '</li>';
	}

	$html .= '</ol>';
	$html .= '</nav>';

	return $html;
}

/**
 * Generate DMX scheme with text content
 * @param array $scheme
 * @return string
 */
function generateDmxSchemeSection($scheme) {
	$html = '<section class="edsys-schemes__section" id="' . htmlspecialchars($scheme['anchor']) . '">';
	$html .= '<div class="edsys-schemes__section-header">';
	$html .= '<h2 class="edsys-schemes__section-title">' . htmlspecialchars($scheme['title']) . '</h2>';
	$html .= '<p class="edsys-schemes__section-description">' . htmlspecialchars($scheme['description']) . '</p>';
	$html .= '</div>';

	$html .= '<div class="edsys-schemes__dmx-container">';

	// Левая часть с текстовым содержимым
	$html .= '<div class="edsys-schemes__dmx-text">';

	foreach ($scheme['schemes_data'] as $schemeData) {
		$html .= '<div class="edsys-schemes__dmx-scheme">';
		$html .= '<h3 class="edsys-schemes__dmx-scheme-title">' . htmlspecialchars($schemeData['title']) . '</h3>';

		if (!empty($schemeData['subtitle'])) {
			$html .= '<div class="edsys-schemes__dmx-scheme-subtitle">' . htmlspecialchars($schemeData['subtitle']) . '</div>';
		}

		$html .= '<ul class="edsys-schemes__dmx-connections">';
		foreach ($schemeData['connections'] as $connection) {
			$html .= '<li class="edsys-schemes__dmx-connection">';
			$html .= '<span class="edsys-schemes__dmx-pin-from">' . htmlspecialchars($connection['from']) . '</span>';
			$html .= '<span class="edsys-schemes__dmx-arrow">→</span>';
			$html .= '<span class="edsys-schemes__dmx-pin-to">' . htmlspecialchars($connection['to']) . '</span>';
			$html .= '</li>';
		}
		$html .= '</ul>';
		$html .= '</div>';
	}

	$html .= '</div>';

	// Правая часть с изображением
	$html .= '<div class="edsys-schemes__dmx-visual">';
	$html .= '<div class="edsys-schemes__image-container">';
	$html .= '<img src="' . htmlspecialchars($scheme['image']) . '" ';
	$html .= 'alt="' . htmlspecialchars($scheme['alt']) . '" ';
	$html .= 'class="edsys-schemes__image" ';
	$html .= 'loading="lazy" ';
	$html .= 'width="600" height="800">';
	$html .= '</div>';
	$html .= '</div>';

	$html .= '</div>';
	$html .= '</section>';

	return $html;
}

/**
 * Generate scheme section HTML
 * @param array $scheme
 * @return string
 */
function generateSchemeSection($scheme) {
	// Для DMX используем специальную функцию с текстовым контентом
	if (!empty($scheme['has_text_content'])) {
		return generateDmxSchemeSection($scheme);
	}

	// Для остальных схем используем стандартную функцию
	$html = '<section class="edsys-schemes__section" id="' . htmlspecialchars($scheme['anchor']) . '">';
	$html .= '<div class="edsys-schemes__section-header">';
	$html .= '<h2 class="edsys-schemes__section-title">' . htmlspecialchars($scheme['title']) . '</h2>';
	$html .= '<p class="edsys-schemes__section-description">' . htmlspecialchars($scheme['description']) . '</p>';
	$html .= '</div>';

	$html .= '<div class="edsys-schemes__image-container">';
	$html .= '<img src="' . htmlspecialchars($scheme['image']) . '" ';
	$html .= 'alt="' . htmlspecialchars($scheme['alt']) . '" ';
	$html .= 'class="edsys-schemes__image" ';
	$html .= 'loading="lazy" ';
	$html .= 'width="1200" height="800">';
	$html .= '</div>';
	$html .= '</section>';

	return $html;
}

/**
 * Render navigation component
 * @param array $navigation
 * @param array $quickNav
 * @return string
 */
function renderNavigationComponent($navigation, $quickNav = []) {
	// Prepare data for component
	$arResult = [
		'NAVIGATION' => $navigation,
		'QUICK_NAV' => $quickNav
	];

	$arParams = [];

	// Include component template
	ob_start();
	include($_SERVER["DOCUMENT_ROOT"] . '/local/templates/' . SITE_TEMPLATE_ID . '/components/bitrix/menu/useful_info_navigation/template.php');
	return ob_get_clean();
}
?>