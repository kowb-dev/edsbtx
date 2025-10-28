<?php
/**
 * EDS Calculators Configuration v1.3
 * Конфигурация калькуляторов для системы EDS
 * Файл должен быть размещен в: /local/templates/edsy_main/include/calculators/
 *
 * @author EDS Development Team
 * @version 1.3
 * @since 2024
 *
 * Изменения v1.3:
 * - Обновлена сортировка для корректного отображения
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Основные настройки калькуляторов
$calculatorsConfig = [
	'calculators' => [
		'stage-equipment' => [
			'NAME' => 'Калькулятор электрооборудования сцены',
			'URL' => '/kalkulator-elektrooborudovaniya-sceny/',
			'SLUG' => 'stage-equipment',
			'DESCRIPTION' => 'Расчет нагрузки и распределения электрооборудования',
			'ICON' => 'ph-lightning',
			'CATEGORY' => 'equipment',
			'SORT' => 10
		],
		'watts-to-amperes' => [
			'NAME' => 'Онлайн калькулятор перевода Ватт в Амперы',
			'URL' => '/onlayn-kalkulyator-perevoda-vatt-v-ampery/',
			'SLUG' => 'watts-to-amperes',
			'DESCRIPTION' => 'Быстрый перевод мощности в силу тока',
			'ICON' => 'ph-arrows-clockwise',
			'CATEGORY' => 'conversion',
			'SORT' => 20
		],
		'wire-section-diameter' => [
			'NAME' => 'Расчет сечения провода по диаметру',
			'URL' => '/raschet-secheniya-provoda-po-diametru-ili-kolichestvu-vitkov/',
			'SLUG' => 'wire-section-diameter',
			'DESCRIPTION' => 'Определение сечения провода по диаметру жилы',
			'ICON' => 'ph-ruler',
			'CATEGORY' => 'wire',
			'SORT' => 30
		],
		'circuit-current' => [
			'NAME' => 'Расчет тока в цепи',
			'URL' => '/raschet-toka-v-tsepi/',
			'SLUG' => 'circuit-current',
			'DESCRIPTION' => 'Вычисление силы тока по закону Ома',
			'ICON' => 'ph-circuitry',
			'CATEGORY' => 'current',
			'SORT' => 40
		],
		'wire-section-voltage-drop' => [
			'NAME' => 'Расчет сечения провода по потере напряжения',
			'URL' => '/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/',
			'SLUG' => 'wire-section-voltage-drop',
			'DESCRIPTION' => 'Подбор сечения с учетом допустимых потерь',
			'ICON' => 'ph-chart-line-down',
			'CATEGORY' => 'wire',
			'SORT' => 50
		],
		'voltage-drop-line' => [
			'NAME' => 'Расчет падения напряжения в линии',
			'URL' => '/raschet-padeniya-napryazheniya-v-linii/',
			'SLUG' => 'voltage-drop-line',
			'DESCRIPTION' => 'Определение потерь напряжения в кабельных линиях',
			'ICON' => 'ph-trend-down',
			'CATEGORY' => 'voltage',
			'SORT' => 60
		]
	],

	// Базовые категории товаров из каталога (без изменений)
	'base_categories' => [
		'turovye' => [
			'title' => 'Туровые дистрибьюторы',
			'description' => 'Профессиональные дистрибьюторы питания для турового оборудования',
			'icon' => 'ph-align-center-vertical-simple',
			'url' => '/cat/turovye/',
			'tags' => ['power', 'distribution', 'tour', 'current', 'equipment']
		],
		'rekovye' => [
			'title' => 'Рэковые дистрибьюторы',
			'description' => 'Дистрибьюторы для монтажа в рэковые стойки',
			'icon' => 'ph-stack',
			'url' => '/cat/rjekovye/',
			'tags' => ['power', 'distribution', 'rack', 'current', 'equipment']
		],
		'vvod-63a' => [
			'title' => 'Ввод от 63A',
			'description' => 'Дистрибьюторы с вводом от 63 ампер',
			'icon' => 'ph-plug',
			'url' => '/cat/vvod-ot-63a/',
			'tags' => ['power', 'high-current', 'distribution', 'current']
		],
		'protective-relays' => [
			'title' => 'Устройства с защитными реле',
			'description' => 'Оборудование с встроенными защитными устройствами',
			'icon' => 'ph-shield-check',
			'url' => '/cat/ustrojstva-s-zashhitnymi-rele/',
			'tags' => ['protection', 'safety', 'current', 'voltage', 'equipment']
		],
		'black-edition' => [
			'title' => 'Серия Black Edition',
			'description' => 'Премиальная серия дистрибьюторов питания',
			'icon' => 'ph-crown',
			'url' => '/cat/seriya-black-edition/',
			'tags' => ['premium', 'power', 'distribution', 'current', 'equipment']
		],
		'splitter-distributors' => [
			'title' => 'Дистрибьюторы со встроенным сплиттером',
			'description' => 'Комбинированные устройства с функцией сплиттера',
			'icon' => 'ph-split-horizontal',
			'url' => '/cat/distribyutori-so-vstroennym-splitterom/',
			'tags' => ['power', 'distribution', 'signal', 'current', 'equipment']
		],
		'cables' => [
			'title' => 'Кабельная продукция',
			'description' => 'Силовые и сигнальные кабели, удлинители для сценического оборудования',
			'icon' => 'ph-plugs',
			'url' => '/cat/kabelnaya-produktsiya/',
			'tags' => ['wire', 'cable', 'transmission', 'voltage', 'current']
		],
		'connection-boxes' => [
			'title' => 'Коммутационные коробки',
			'description' => 'Коробки для коммутации сигналов и питания',
			'icon' => 'ph-squares-four',
			'url' => '/cat/kommutatsionnye-korobki/',
			'tags' => ['connection', 'switching', 'wire', 'signal']
		],
		'stage-hatches' => [
			'title' => 'Сценические лючки',
			'description' => 'Напольные и настенные лючки для сценических площадок',
			'icon' => 'ph-square-half',
			'url' => '/cat/stsenicheskie-lyuchki/',
			'tags' => ['stage', 'connection', 'wire', 'equipment']
		],
		'desktop-hatches' => [
			'title' => 'Настольные лючки',
			'description' => 'Настольные коммутационные лючки',
			'icon' => 'ph-square',
			'url' => '/cat/nastolnye-lyuchki/',
			'tags' => ['desktop', 'connection', 'wire', 'equipment']
		],
		'dmx-splitters' => [
			'title' => 'DMX-сплиттеры',
			'description' => 'Сплиттеры для разветвления DMX сигналов',
			'icon' => 'ph-split-horizontal',
			'url' => '/cat/dmx-splittery/',
			'tags' => ['dmx', 'signal', 'splitting', 'lighting']
		],
		'signal-devices' => [
			'title' => 'Устройства передачи сигнала',
			'description' => 'Устройства для передачи аудио и видео сигналов',
			'icon' => 'ph-broadcast',
			'url' => '/cat/ustroystva-peredachi-signala/',
			'tags' => ['signal', 'transmission', 'audio', 'video']
		],
		'winch-controls-digital' => [
			'title' => 'Пульты лебедочные цифровые',
			'description' => 'Цифровые пульты управления лебедочными системами',
			'icon' => 'ph-device-tablet',
			'url' => '/cat/pulty-lebedochnye-tsifrovye/',
			'tags' => ['control', 'digital', 'winch', 'equipment']
		],
		'winch-controls-analog' => [
			'title' => 'Пульты лебедочные аналоговые',
			'description' => 'Аналоговые пульты управления лебедочными системами',
			'icon' => 'ph-sliders',
			'url' => '/cat/pulty-lebedochnye-analogovye/',
			'tags' => ['control', 'analog', 'winch', 'equipment']
		],
		'dimmers' => [
			'title' => 'Диммеры',
			'description' => 'Диммерные устройства для управления освещением',
			'icon' => 'ph-lightbulb',
			'url' => '/cat/dimmery/',
			'tags' => ['dimmer', 'lighting', 'control', 'current', 'voltage']
		],
		'sequencers' => [
			'title' => 'Секвенсоры',
			'description' => 'Устройства последовательного управления нагрузками',
			'icon' => 'ph-list-checks',
			'url' => '/cat/sekvensory/',
			'tags' => ['sequencer', 'control', 'power', 'current', 'equipment']
		]
	],

	// Маппинг категорий товаров для каждого калькулятора
	'calculator_categories' => [
		'stage-equipment' => [
			'turovye', 'rekovye', 'vvod-63a', 'protective-relays',
			'black-edition', 'splitter-distributors'
		],
		'watts-to-amperes' => [
			'turovye', 'rekovye', 'protective-relays',
			'black-edition', 'vvod-63a', 'splitter-distributors'
		],
		'wire-section-diameter' => [
			'cables', 'connection-boxes', 'stage-hatches',
			'desktop-hatches', 'protective-relays', 'turovye'
		],
		'circuit-current' => [
			'turovye', 'rekovye', 'protective-relays',
			'black-edition', 'vvod-63a', 'splitter-distributors'
		],
		'wire-section-voltage-drop' => [
			'cables', 'connection-boxes', 'dimmers',
			'protective-relays', 'turovye', 'rekovye'
		],
		'voltage-drop-line' => [
			'cables', 'dmx-splitters', 'signal-devices',
			'protective-relays', 'connection-boxes', 'dimmers'
		]
	],

	// Навигация для калькуляторов (обновлена)
	'navigation' => [
		[
			'NAME' => 'Калькулятор электрооборудования сцены',
			'URL' => '/kalkulator-elektrooborudovaniya-sceny/',
			'SLUG' => 'stage-equipment',
			'DESCRIPTION' => 'Расчет нагрузки и распределения электрооборудования',
			'ICON' => 'ph-lightning',
			'SORT' => 10
		],
		[
			'NAME' => 'Перевод Ватт в Амперы',
			'URL' => '/onlayn-kalkulyator-perevoda-vatt-v-ampery/',
			'SLUG' => 'watts-to-amperes',
			'DESCRIPTION' => 'Быстрый перевод мощности в силу тока',
			'ICON' => 'ph-arrows-clockwise',
			'SORT' => 20
		],
		[
			'NAME' => 'Сечение провода по диаметру',
			'URL' => '/raschet-secheniya-provoda-po-diametru-ili-kolichestvu-vitkov/',
			'SLUG' => 'wire-section-diameter',
			'DESCRIPTION' => 'Определение сечения провода по диаметру жилы',
			'ICON' => 'ph-ruler',
			'SORT' => 30
		],
		[
			'NAME' => 'Расчет тока в цепи',
			'URL' => '/raschet-toka-v-tsepi/',
			'SLUG' => 'circuit-current',
			'DESCRIPTION' => 'Вычисление силы тока по закону Ома',
			'ICON' => 'ph-circuitry',
			'SORT' => 40
		],
		[
			'NAME' => 'Сечение провода по потере напряжения',
			'URL' => '/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/',
			'SLUG' => 'wire-section-voltage-drop',
			'DESCRIPTION' => 'Подбор сечения с учетом допустимых потерь',
			'ICON' => 'ph-chart-line-down',
			'SORT' => 50
		],
		[
			'NAME' => 'Падение напряжения в линии',
			'URL' => '/raschet-padeniya-napryazheniya-v-linii/',
			'SLUG' => 'voltage-drop-line',
			'DESCRIPTION' => 'Определение потерь напряжения в кабельных линиях',
			'ICON' => 'ph-trend-down',
			'SORT' => 60
		]
		// Удален элемент "Потери напряжения" (voltage-loss)
	]
];

// Функции остаются без изменений
if (!function_exists('getCalculatorCategories')) {
	function getCalculatorCategories($calculatorSlug, $limit = 6) {
		global $calculatorsConfig;

		if (!isset($calculatorsConfig['calculator_categories'][$calculatorSlug])) {
			$defaultCategories = ['turovye', 'rekovye', 'protective-relays', 'black-edition', 'vvod-63a', 'cables'];
			return array_slice(array_map(function($categoryKey) use ($calculatorsConfig) {
				return $calculatorsConfig['base_categories'][$categoryKey];
			}, $defaultCategories), 0, $limit);
		}

		$categoryKeys = $calculatorsConfig['calculator_categories'][$calculatorSlug];

		$result = [];
		foreach ($categoryKeys as $categoryKey) {
			if (isset($calculatorsConfig['base_categories'][$categoryKey])) {
				$result[] = $calculatorsConfig['base_categories'][$categoryKey];
			}
		}

		return array_slice($result, 0, $limit);
	}
}

if (!function_exists('getAllBaseCategories')) {
	function getAllBaseCategories() {
		global $calculatorsConfig;
		return $calculatorsConfig['base_categories'];
	}
}

if (!function_exists('getCategoriesByTags')) {
	function getCategoriesByTags($tags, $limit = 6) {
		global $calculatorsConfig;

		$result = [];
		foreach ($calculatorsConfig['base_categories'] as $categoryKey => $category) {
			$intersection = array_intersect($tags, $category['tags']);
			if (!empty($intersection)) {
				$result[] = $category;
			}
		}

		return array_slice($result, 0, $limit);
	}
}

if (!function_exists('getCalculatorCategoriesWithFallback')) {
	function getCalculatorCategoriesWithFallback($calculatorSlug, $fallbackTags = [], $limit = 6) {
		global $calculatorsConfig;

		$categories = getCalculatorCategories($calculatorSlug, $limit);

		if (empty($categories) && !empty($fallbackTags)) {
			$categories = getCategoriesByTags($fallbackTags, $limit);
		}

		if (empty($categories)) {
			$defaultCategories = ['turovye', 'rekovye', 'protective-relays', 'black-edition', 'vvod-63a', 'cables'];
			$categories = array_slice(array_map(function($categoryKey) use ($calculatorsConfig) {
				return $calculatorsConfig['base_categories'][$categoryKey];
			}, $defaultCategories), 0, $limit);
		}

		return $categories;
	}
}

// Возвращаем конфигурацию
return $calculatorsConfig;
?>