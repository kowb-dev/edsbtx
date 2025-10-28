<?php
/**
 * Configuration file for Contact Load Classification page
 * Конфигурация страницы классификации типов нагрузки контактов
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Page configuration
$pageConfig = [
	'title' => 'Классификация типов нагрузки контактов',
	'description' => 'Подробная классификация типов нагрузки контактов AC1, AC3, AC4, AC14, AC15, DC1, DC3. Технические характеристики и рекомендации по выбору релейного оборудования для различных применений.',
	'keywords' => 'классификация нагрузки контактов, AC1, AC3, AC4, AC14, AC15, DC1, DC3, реле, коммутация, электрооборудование, защитные реле',
	'breadcrumbs' => [
		['name' => 'Главная', 'url' => '/'],
		['name' => 'Полезно знать', 'url' => '/polezno-znat/'],
		['name' => 'Классификация типов нагрузки контактов', 'url' => '']
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
		'ACTIVE' => false,
		'ICON' => 'ph-circuitry',
		'DESCRIPTION' => 'Схемы подключения и распайки'
	],
	[
		'NAME' => 'Классификация типов нагрузки контактов',
		'URL' => '/klassifikatsiya-tipov-nagruzki-kontaktov/',
		'ACTIVE' => true, // Current page
		'ICON' => 'ph-tree-structure',
		'DESCRIPTION' => 'Типы и характеристики нагрузок'
	]
];

// Classification data for the main table
$classificationData = [
	[
		'category' => 'AC1',
		'power_type' => 'Однофазный ток AC<br>Трехфазный ток AC',
		'applications' => 'Резистивные или слабоиндуктивные нагрузки',
		'relay_switching' => 'Соблюдайте<br>параметры реле',
		'color' => 'voltage'
	],
	[
		'category' => 'AC3',
		'power_type' => 'Однофазный ток AC<br>Трехфазный ток AC',
		'applications' => 'Запуск и остановка электромоторов с обмоткой «беличье колесо». Смена направления вращения только после полной остановки электромотора. Трехфазные: Реверс электромотора допускается при гарантированной остановке на 50 мс между подачей напряжения для одного направления вращения и для другого направления. Однофазные: Обеспечить «мертвую паузу» 300 мс когда контакты реле разомкнуты – в течение которой конденсатор разрядится безопасно для обмоток электромотора.',
		'relay_switching' => 'Для однофазных:<br>Соблюдайте параметры реле<br>Для трехфазных:<br>См. трехфазные<br>электромоторы',
		'color' => 'circuit'
	],
	[
		'category' => 'AC4',
		'power_type' => 'Трехфазный ток AC',
		'applications' => 'Запуск, остановка, смена вращения электромоторов с обмоткой «беличье колесо», толчки (медленное вращение), рекуперативное торможение (за счет смены фаз).',
		'relay_switching' => 'Реле не применяются, т.к. происходит перекоммутация фаз для смены направления вращения, на контактах возникает сильная электрическая дуга.',
		'color' => 'spark'
	],
	[
		'category' => 'AC14',
		'power_type' => 'Однофазный ток AC',
		'applications' => 'Управление небольшими электромагнитными нагрузками (72ВА), силовыми контакторами, магнитными соленоидными клапанами, электромагнитами.',
		'relay_switching' => 'При выборе реле принимайте во внимание, что скачки тока для этого типа нагрузки могут превышать номинальный ток в 6 раз.',
		'color' => 'wire'
	],
	[
		'category' => 'AC15',
		'power_type' => 'Однофазный ток AC',
		'applications' => 'Управление небольшими электромагнитными нагрузками (72ВА), силовыми контакторами, магнитными соленоидными клапанами, электромагнитами.',
		'relay_switching' => 'При выборе реле принимайте во внимание, что скачки тока для этого типа нагрузки могут превышать номинальный ток в 10 раз.',
		'color' => 'neon'
	],
	[
		'category' => 'DC1',
		'power_type' => 'DC',
		'applications' => 'Резистивные или слабоиндуктивные нагрузки DC. (Коммутируемое напряжение при той же величине тока можно удвоить за счет подключения двух контактов последовательно).',
		'relay_switching' => 'Соблюдайте параметры реле (см.график «Макс. отключающая способность DC1»).',
		'color' => 'steel'
	],
	[
		'category' => 'DC3',
		'power_type' => 'DC',
		'applications' => 'Управление электромагнитными нагрузками, силовыми контакторами, магнитными соленоидными клапанами, электромагнитами.',
		'relay_switching' => 'Принимайте во внимание, что при отсутствии скачков тока, величина повышенного напряжения может превышать номинальное значение напряжения в 15 раз. Приблизительное значение мощности реле при индуктивной нагрузке DC (при 40 мс L/R) можно принять за 50% от мощности DC1. (см.график «Макс. отключающая способность DC1»)',
		'color' => 'carbon'
	]
];

// Technical notes data for recommendations section
$technicalNotes = [
	[
		'icon' => 'ph-warning',
		'title' => 'Особенности коммутации',
		'content' => '• AC1: Резистивные нагрузки - соблюдайте номинальные параметры<br>• AC3: Электродвигатели - обязательна пауза 50-300мс между переключениями<br>• AC4: Не используйте реле для реверса под нагрузкой<br>• AC14/15: Учитывайте пусковые токи в 6-10 раз выше номинала<br>• DC3: Индуктивные нагрузки постоянного тока требуют снижения мощности на 50%',
		'color' => 'accent'
	],
	[
		'icon' => 'ph-wrench',
		'title' => 'Подбор реле',
		'content' => '• Для AC1: Выбирайте по номинальному току нагрузки<br>• Для моторов AC3: Добавляйте запас 20-30% к пусковому току<br>• DC нагрузки: Два контакта последовательно удваивают напряжение<br>• Индуктивные цепи: Учитывайте выбросы напряжения до 15х номинала<br>• Всегда проверяйте коммутационную способность при вашем напряжении',
		'color' => 'voltage'
	],
	[
		'icon' => 'ph-shield',
		'title' => 'Защитные меры',
		'content' => '• Используйте искрогасящие цепи для индуктивных нагрузок<br>• При DC коммутации применяйте диоды или варисторы<br>• Для моторов - обязательные защитные паузы<br>• Контролируйте температуру контактов при больших токах<br>• Регулярно проверяйте износ контактной группы реле',
		'color' => 'circuit'
	]
];

// Related products data for product cards
$relatedProducts = [
	[
		'title' => 'Устройства с защитными реле',
		'description' => 'Устройства с встроенными реле контроля напряжения и защитными функциями для безопасной коммутации нагрузок.',
		'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
		'icon' => 'ph-shield-check',
		'color' => 'voltage'
	],
	[
		'title' => 'Секвенсоры',
		'description' => 'Устройства последовательного включения нагрузок с контролем токовых характеристик и защитой от перегрузок.',
		'url' => 'https://btx.edsy.ru/cat/sekvensory/',
		'icon' => 'ph-list-numbers',
		'color' => 'circuit'
	],
	[
		'title' => 'Свитчеры',
		'description' => 'Коммутационные устройства для переключения различных типов нагрузок с учетом их электрических характеристик.',
		'url' => 'https://btx.edsy.ru/cat/svitchery/',
		'icon' => 'ph-arrows-counter-clockwise',
		'color' => 'spark'
	],
	[
		'title' => 'Пульты управления',
		'description' => 'Системы управления с программируемой логикой для безопасной коммутации различных типов электрических нагрузок.',
		'url' => 'https://btx.edsy.ru/cat/pulty-knopochnye/',
		'icon' => 'ph-control',
		'color' => 'wire'
	]
];

// Related links for sidebar or additional navigation
$relatedLinks = [
	'cable_tables' => [
		'title' => 'Таблицы токовых нагрузок',
		'url' => '/stati-tablitsy-nagruzok/',
		'description' => 'Справочные таблицы нагрузок медных кабелей'
	],
	'cable_schemes' => [
		'title' => 'Схемы распайки кабелей',
		'url' => '/shemy-raspajki-kabelej/',
		'description' => 'Профессиональные схемы подключения'
	],
	'calculators' => [
		'title' => 'Калькуляторы',
		'url' => '/kalkulyatory/',
		'description' => 'Расчет сечения проводов, падения напряжения'
	],
	'articles' => [
		'title' => 'Полезные статьи',
		'url' => '/polezno-znat/',
		'description' => 'Техническая документация и экспертные материалы'
	],
	'contacts' => [
		'title' => 'Техническая поддержка',
		'url' => '/kontakty/',
		'description' => 'Связаться с нашими специалистами'
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
	$html = '<nav class="edsys-classification__breadcrumbs" aria-label="Навигация">';
	$html .= '<ol class="edsys-classification__breadcrumb-list">';

	foreach ($breadcrumbs as $index => $item) {
		$isLast = $index === count($breadcrumbs) - 1;
		$html .= '<li class="edsys-classification__breadcrumb-item">';

		if ($isLast || empty($item['url'])) {
			$html .= '<span class="edsys-classification__breadcrumb-current">' . htmlspecialchars($item['name']) . '</span>';
		} else {
			$html .= '<a href="' . htmlspecialchars($item['url']) . '" class="edsys-classification__breadcrumb-link">' . htmlspecialchars($item['name']) . '</a>';
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
 * Render navigation component
 * @param array $navigation
 * @return string
 */
function renderNavigationComponent($navigation) {
	// Prepare data for component
	$arResult = [
		'NAVIGATION' => $navigation,
		'QUICK_NAV' => [] // No quick nav for this page
	];

	$arParams = [];

	// Include component template
	ob_start();
	include($_SERVER["DOCUMENT_ROOT"] . '/local/templates/' . SITE_TEMPLATE_ID . '/components/bitrix/menu/useful_info_navigation/template.php');
	return ob_get_clean();
}

/**
 * Generate classification table HTML
 * @param array $data
 * @return string
 */
function generateClassificationTable($data) {
	$html = '<div class="edsys-classification__table-wrapper">';
	$html .= '<table class="edsys-classification__table">';
	$html .= '<thead>';
	$html .= '<tr>';
	$html .= '<th>Категория нагрузки</th>';
	$html .= '<th>Тип электропитания</th>';
	$html .= '<th>Приложения</th>';
	$html .= '<th>Переключение с помощью реле</th>';
	$html .= '</tr>';
	$html .= '</thead>';
	$html .= '<tbody>';

	foreach($data as $row) {
		$html .= '<tr>';
		$html .= '<td class="edsys-classification__category" data-color="' . htmlspecialchars($row['color']) . '">';
		$html .= '<strong>' . htmlspecialchars($row['category']) . '</strong>';
		$html .= '</td>';
		$html .= '<td>' . $row['power_type'] . '</td>';
		$html .= '<td>' . htmlspecialchars($row['applications']) . '</td>';
		$html .= '<td>' . $row['relay_switching'] . '</td>';
		$html .= '</tr>';
	}

	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';

	return $html;
}

/**
 * Generate technical notes section HTML
 * @param array $notes
 * @return string
 */
function generateTechnicalNotes($notes) {
	$html = '<section class="edsys-classification__notes">';
	$html .= '<h2 class="edsys-classification__notes-title">Технические рекомендации</h2>';
	$html .= '<div class="edsys-classification__notes-grid">';

	foreach($notes as $note) {
		$html .= '<div class="edsys-classification__note-card" data-color="' . htmlspecialchars($note['color']) . '">';
		$html .= '<div class="edsys-classification__note-header">';
		$html .= '<div class="edsys-classification__note-icon">';
		$html .= '<i class="ph ph-thin ' . htmlspecialchars($note['icon']) . '"></i>';
		$html .= '</div>';
		$html .= '<h3 class="edsys-classification__note-title">' . htmlspecialchars($note['title']) . '</h3>';
		$html .= '</div>';
		$html .= '<div class="edsys-classification__note-content">' . $note['content'] . '</div>';
		$html .= '</div>';
	}

	$html .= '</div>';
	$html .= '</section>';

	return $html;
}

/**
 * Generate related products section HTML
 * @param array $products
 * @return string
 */
function generateRelatedProducts($products) {
	$html = '<section class="edsys-classification__products">';
	$html .= '<h2 class="edsys-classification__products-title">Рекомендуемое оборудование</h2>';
	$html .= '<div class="edsys-classification__products-grid">';

	foreach($products as $product) {
		$html .= '<a href="' . htmlspecialchars($product['url']) . '" ';
		$html .= 'class="edsys-classification__product-card" ';
		$html .= 'data-color="' . htmlspecialchars($product['color']) . '" ';
		$html .= 'target="_blank" rel="noopener">';

		$html .= '<div class="edsys-classification__product-icon">';
		$html .= '<i class="ph ph-thin ' . htmlspecialchars($product['icon']) . '"></i>';
		$html .= '</div>';

		$html .= '<div class="edsys-classification__product-content">';
		$html .= '<h3 class="edsys-classification__product-title">' . htmlspecialchars($product['title']) . '</h3>';
		$html .= '<p class="edsys-classification__product-description">' . htmlspecialchars($product['description']) . '</p>';
		$html .= '</div>';
		$html .= '</a>';
	}

	$html .= '</div>';
	$html .= '</section>';

	return $html;
}

/**
 * Auto-detect active page in navigation
 */
function updateActiveNavigation(&$navigation) {
	global $APPLICATION;
	$currentUrl = $APPLICATION->GetCurUri();

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
updateActiveNavigation($usefulInfoNavigation);

?>