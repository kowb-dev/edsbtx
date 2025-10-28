<?php
/**
 * EDS Articles Configuration v1.2 - UPDATED
 * Центральная конфигурация для всех статей раздела "Полезно знать"
 * Добавлена статья "Расчет потери напряжения"
 *
 * @author EDS Development Team
 * @version 1.2
 * @since 2024
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

return [
	// Base configuration
	'base' => [
		'template_path' => '/local/templates/edsy_main',
		'assets_path' => '/local/templates/edsy_main/assets',
		'site_name' => 'EDS - Electric Distribution Systems',
		'site_url' => 'https://' . $_SERVER['HTTP_HOST'],
		'organization_name' => 'EDS - Electric Distribution Systems',
		'version' => '1.2'
	],

	// Common meta defaults
	'meta_defaults' => [
		'author' => 'EDS - Electric Distribution Systems',
		'publisher' => 'EDS',
		'logo_url' => '/local/templates/edsy_main/images/logo.svg',
		'default_image' => '/upload/useful/default-article.jpg',
		'twitter_card' => 'summary_large_image',
		'og_type' => 'article'
	],

	// SEO configuration
	'seo' => [
		'site_name' => 'EDS - Electric Distribution Systems',
		'default_title_suffix' => ' - EDS',
		'structured_data' => [
			'organization' => [
				'@type' => 'Organization',
				'name' => 'EDS - Electric Distribution Systems',
				'url' => 'https://' . $_SERVER['HTTP_HOST'],
				'logo' => 'https://' . $_SERVER['HTTP_HOST'] . '/local/templates/edsy_main/images/logo.svg'
			]
		]
	],

	// Assets configuration
	'assets' => [
		'css' => [
			'common' => '/local/templates/edsy_main/assets/css/articles/edsys-articles-common.css',
			'navigation' => '/local/templates/edsy_main/assets/css/articles/edsys-articles-navigation.css',
			'blocks' => '/local/templates/edsy_main/assets/css/components/blocks.css'
		],
		'js' => [
			'core' => '/local/templates/edsy_main/assets/js/articles/edsys-articles-core.js',
			'navigation' => '/local/templates/edsy_main/assets/js/articles/edsys-articles-navigation.js',
			'utils' => '/local/templates/edsy_main/assets/js/articles/edsys-articles-utils.js'
		]
	],

	// Social sharing configuration
	'social_sharing' => [
		'whatsapp' => [
			'enabled' => true,
			'base_url' => 'https://wa.me/?text=',
			'template' => '{title} - {url}'
		],
		'telegram' => [
			'enabled' => true,
			'base_url' => 'https://t.me/share/url?url={url}&text={title}',
			'template' => '{title}'
		],
		'vk' => [
			'enabled' => false,
			'base_url' => 'https://vk.com/share.php?url={url}&title={title}',
			'template' => '{title}'
		],
		'facebook' => [
			'enabled' => false,
			'base_url' => 'https://www.facebook.com/sharer/sharer.php?u={url}',
			'template' => '{title}'
		]
	],

	// Breadcrumbs base structure
	'breadcrumbs_base' => [
		[
			'name' => 'Главная',
			'url' => '/',
			'position' => 1
		],
		[
			'name' => 'Полезно знать',
			'url' => '/polezno-znat/',
			'position' => 2
		]
	],

	// Articles navigation configuration
	'navigation' => [
		[
			'NAME' => 'Пусковые токи и как с ними бороться',
			'URL' => '/puskovye-toki-i-kak-s-nimi-borotsya/',
			'SLUG' => 'puskovye-toki-i-kak-s-nimi-borotsya',
			'ICON' => 'ph-lightning',
			'DESCRIPTION' => 'Проблемы импульсных источников питания',
			'KEYWORDS' => 'пусковые токи, импульсные источники питания, Safe Start',
			'SORT' => 10
		],
		[
			'NAME' => 'Заземление. Как это работает?',
			'URL' => '/zazemlenie-kak-eto-rabotaet/',
			'SLUG' => 'zazemlenie-kak-eto-rabotaet',
			'ICON' => 'ph-shield-check',
			'DESCRIPTION' => 'Защита от поражения электрическим током',
			'KEYWORDS' => 'заземление, электробезопасность, защита от тока',
			'SORT' => 20
		],
		[
			'NAME' => 'Разность потенциалов',
			'URL' => '/raznost-potentsialov/',
			'SLUG' => 'raznost-potentsialov',
			'ICON' => 'ph-pulse',
			'DESCRIPTION' => 'Проблемы в блоках питания оборудования',
			'KEYWORDS' => 'разность потенциалов, блоки питания, импульсные источники',
			'SORT' => 30
		],
		[
			'NAME' => 'Сравнение КГтп-ХЛ и H07RN-F',
			'URL' => '/sravnenie-kgtp-hl-i-h07rn-f/',
			'SLUG' => 'sravnenie-kgtp-hl-i-h07rn-f',
			'ICON' => 'ph-git-diff',
			'DESCRIPTION' => 'Выбор кабеля для профессионального оборудования',
			'KEYWORDS' => 'кабели, КГтп-ХЛ, H07RN-F, сравнение кабелей',
			'SORT' => 40
		],
		[
			'NAME' => 'АВР: что это такое',
			'URL' => '/avr-chto-eto-takoe/',
			'SLUG' => 'avr-chto-eto-takoe',
			'ICON' => 'ph-arrows-counter-clockwise',
			'DESCRIPTION' => 'Автоматический ввод резерва',
			'KEYWORDS' => 'АВР, автоматический ввод резерва, резервирование питания',
			'SORT' => 50
		],
		[
			'NAME' => 'Витая пара. Категории и применение',
			'URL' => '/vitaya-para-kategorii-i-primenenie/',
			'SLUG' => 'vitaya-para-kategorii-i-primenenie',
			'ICON' => 'ph-network',
			'DESCRIPTION' => 'Кабели для передачи цифрового сигнала',
			'KEYWORDS' => 'витая пара, категории кабелей, цифровой сигнал, ethernet, патч-корды',
			'SORT' => 60
		],
		// NEW ARTICLE - Расчет потери напряжения
		[
			'NAME' => 'Расчет потери напряжения',
			'URL' => '/raschet-poteri-napryazheniya/',
			'SLUG' => 'raschet-poteri-napryazheniya',
			'ICON' => 'ph-calculator',
			'DESCRIPTION' => 'Формулы и практические примеры расчета потерь в кабелях',
			'KEYWORDS' => 'расчет потерь напряжения, формула потерь, сечение кабеля, электропроводка',
			'SORT' => 70
		],
		[
			'NAME' => 'Как правильно пользоваться мультиметром',
			'URL' => '/kak-pravilno-polzovatsya-multimetrom/',
			'SLUG' => 'kak-pravilno-polzovatsya-multimetrom',
			'ICON' => 'ph-device-mobile',
			'DESCRIPTION' => 'Измерительные приборы и их применение',
			'KEYWORDS' => 'мультиметр, измерения, электрические параметры',
			'SORT' => 80
		],
		[
			'NAME' => 'Немного о реле контроля напряжения',
			'URL' => '/nemnogo-o-rele-kontrolya-napryazheniya/',
			'SLUG' => 'nemnogo-o-rele-kontrolya-napryazheniya',
			'ICON' => 'ph-gear',
			'DESCRIPTION' => 'Защита от перепадов напряжения',
			'KEYWORDS' => 'реле контроля, защита напряжения, стабилизация',
			'SORT' => 90
		],
		[
			'NAME' => 'Неизвестное об известном. Unit',
			'URL' => '/unit/',
			'SLUG' => 'unit',
			'ICON' => 'ph-ruler',
			'DESCRIPTION' => 'Единицы измерения оборудования в рэковых стойках',
			'KEYWORDS' => 'unit, стойки, рэковое оборудование, размеры',
			'SORT' => 100
		]
	],

	// Common product categories templates
	'product_categories_templates' => [
		'power_distribution' => [
			[
				'name' => 'Туровые дистрибьюторы',
				'description' => 'Мобильные решения для распределения питания с надежным заземлением для концертного оборудования',
				'url' => 'https://btx.edsy.ru/cat/turovye/',
				'icon' => 'ph-suitcase',
				'color' => 'spark',
				'features' => ['Мобильное применение', 'Концертное оборудование', 'Защита от токов']
			],
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Профессиональные дистрибьюторы с встроенной защитой от перегрузок и контролем параметров',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'voltage',
				'features' => ['Контроль перегрузок', 'Защитные реле', 'Мониторинг параметров']
			],
			[
				'name' => 'Секвенсоры',
				'description' => 'Устройства последовательного включения нагрузок с технологией Safe Start для защиты от пусковых токов',
				'url' => 'https://btx.edsy.ru/cat/sekvensory/',
				'icon' => 'ph-list-numbers',
				'color' => 'wire',
				'features' => ['Safe Start технология', 'Защита от пусковых токов', 'Последовательное включение']
			]
		],

		'grounding_safety' => [
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Профессиональные дистрибьюторы с встроенной защитой от перегрузок и заземлением',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'voltage',
				'features' => ['Контроль заземления', 'Защитные реле', 'Безопасность']
			],
			[
				'name' => 'Туровые дистрибьюторы',
				'description' => 'Мобильные решения с надежными заземляющими контактами для безопасной работы',
				'url' => 'https://btx.edsy.ru/cat/turovye/',
				'icon' => 'ph-suitcase',
				'color' => 'spark',
				'features' => ['Заземляющие контакты', 'Мобильность', 'Безопасность']
			],
			[
				'name' => 'Рэковые дистрибьюторы',
				'description' => 'Стационарные решения с профессиональным заземлением для студий и залов',
				'url' => 'https://btx.edsy.ru/cat/rjekovye/',
				'icon' => 'ph-cube',
				'color' => 'wire',
				'features' => ['Стационарное заземление', 'Профессиональное качество', 'Надежность']
			]
		],

		'potential_difference' => [
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Контроль разности потенциалов и защита от перепадов напряжения',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'voltage',
				'features' => ['Контроль потенциалов', 'Защита от перепадов', 'Мониторинг']
			],
			[
				'name' => 'Секвенсоры',
				'description' => 'Управление последовательностью включения для минимизации разности потенциалов',
				'url' => 'https://btx.edsy.ru/cat/sekvensory/',
				'icon' => 'ph-list-numbers',
				'color' => 'wire',
				'features' => ['Последовательное включение', 'Контроль потенциалов', 'Безопасность']
			],
			[
				'name' => 'Дистрибьюторы со встроенным сплиттером',
				'description' => 'Распределение питания с выравниванием потенциалов между приборами',
				'url' => 'https://btx.edsy.ru/cat/distribyutori-so-vstroennym-splitterom/',
				'icon' => 'ph-flow-arrow',
				'color' => 'spark',
				'features' => ['Выравнивание потенциалов', 'Встроенный сплиттер', 'Профессиональное качество']
			]
		],

		'cables_comparison' => [
			[
				'name' => 'Кабельная продукция',
				'description' => 'Широкий выбор кабелей КГтп-ХЛ и H07RN-F для различных применений',
				'url' => 'https://btx.edsy.ru/cat/kabelnaya-produktsiya/',
				'icon' => 'ph-plug',
				'color' => 'wire',
				'features' => ['КГтп-ХЛ и H07RN-F', 'Различные сечения', 'Профессиональное качество']
			],
			[
				'name' => 'Туровые дистрибьюторы',
				'description' => 'Мобильные решения с качественными кабелями для гастрольной деятельности',
				'url' => 'https://btx.edsy.ru/cat/turovye/',
				'icon' => 'ph-suitcase',
				'color' => 'spark',
				'features' => ['Мобильные решения', 'Качественные кабели', 'Концертное оборудование']
			],
			[
				'name' => 'Сопутствующие товары',
				'description' => 'Разъемы, переходники и аксессуары для кабельных соединений',
				'url' => 'https://btx.edsy.ru/cat/soputstvuyushchie-tovary/',
				'icon' => 'ph-plug',
				'color' => 'voltage',
				'features' => ['Разъемы', 'Переходники', 'Аксессуары']
			]
		],

		'avr_systems' => [
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Автоматические системы контроля и переключения с защитными реле',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'voltage',
				'features' => ['Автоматическое переключение', 'Защитные реле', 'Контроль питания']
			],
			[
				'name' => 'Секвенсоры',
				'description' => 'Управление последовательностью включения резервных источников',
				'url' => 'https://btx.edsy.ru/cat/sekvensory/',
				'icon' => 'ph-list-numbers',
				'color' => 'wire',
				'features' => ['Последовательное включение', 'Управление резервом', 'Автоматизация']
			],
			[
				'name' => 'Рэковые дистрибьюторы',
				'description' => 'Стационарные решения с возможностью резервирования питания',
				'url' => 'https://btx.edsy.ru/cat/rjekovye/',
				'icon' => 'ph-cube',
				'color' => 'spark',
				'features' => ['Резервирование питания', 'Стационарные решения', 'Профессиональное качество']
			]
		],

		'twisted_pair_signal' => [
			[
				'name' => 'Сигнальная коммутация',
				'description' => 'Профессиональные решения для передачи цифрового сигнала по витой паре',
				'url' => 'https://btx.edsy.ru/cat/signalnaya-kommutatsiya/',
				'icon' => 'ph-network',
				'color' => 'circuit',
				'features' => ['Цифровой сигнал', 'Витая пара', 'Профессиональное качество']
			],
			[
				'name' => 'Аудиоустройства',
				'description' => 'Аудиооборудование с поддержкой цифровых интерфейсов',
				'url' => 'https://btx.edsy.ru/cat/audio-devices/',
				'icon' => 'ph-speaker-high',
				'color' => 'spark',
				'features' => ['Цифровые интерфейсы', 'Аудио оборудование', 'Высокое качество']
			],
			[
				'name' => 'Разъемы и компоненты',
				'description' => 'Разъемы и соединители для витой пары и цифрового сигнала',
				'url' => 'https://btx.edsy.ru/cat/razemy-i-komponenty/',
				'icon' => 'ph-plug',
				'color' => 'voltage',
				'features' => ['Разъемы для витой пары', 'Цифровые соединители', 'Надежность']
			]
		],

		// NEW TEMPLATE - Voltage Loss Solutions
		'voltage_loss_solutions' => [
			[
				'name' => 'Кабельная продукция',
				'description' => 'Медные и алюминиевые кабели различных сечений для минимизации потерь напряжения',
				'url' => 'https://btx.edsy.ru/cat/kabelnaya-produktsiya/',
				'icon' => 'ph-plug',
				'color' => 'wire',
				'features' => ['Различные сечения', 'Медь и алюминий', 'Низкие потери']
			],
			[
				'name' => 'Туровые дистрибьюторы',
				'description' => 'Мобильные решения для больших расстояний с учетом потерь напряжения',
				'url' => 'https://btx.edsy.ru/cat/turovye/',
				'icon' => 'ph-suitcase',
				'color' => 'spark',
				'features' => ['Для больших расстояний', 'Мобильность', 'Расчет потерь']
			],
			[
				'name' => 'Рэковые дистрибьюторы',
				'description' => 'Стационарные решения с оптимизированными кабельными трассами',
				'url' => 'https://btx.edsy.ru/cat/rjekovye/',
				'icon' => 'ph-cube',
				'color' => 'voltage',
				'features' => ['Стационарные решения', 'Оптимизация трасс', 'Точные расчеты']
			]
		],

		'multimeter_usage' => [
			[
				'name' => 'Сопутствующие товары',
				'description' => 'Измерительные приборы и тестеры для контроля электрических параметров',
				'url' => 'https://btx.edsy.ru/cat/soputstvuyushchie-tovary/',
				'icon' => 'ph-device-mobile',
				'color' => 'voltage',
				'features' => ['Измерительные приборы', 'Тестеры', 'Контроль параметров']
			],
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Оборудование с возможностью мониторинга и отображения электрических параметров',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'spark',
				'features' => ['Мониторинг параметров', 'Защитные реле', 'Индикация']
			],
			[
				'name' => 'Кабельная продукция',
				'description' => 'Кабели и проводники для проведения измерений и тестирования',
				'url' => 'https://btx.edsy.ru/cat/kabelnaya-produktsiya/',
				'icon' => 'ph-plug',
				'color' => 'wire',
				'features' => ['Измерительные кабели', 'Тестовые провода', 'Качественные соединения']
			]
		],

		'voltage_relay_control' => [
			[
				'name' => 'Устройства с защитными реле',
				'description' => 'Профессиональные дистрибьюторы с встроенными реле контроля напряжения',
				'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
				'icon' => 'ph-shield-check',
				'color' => 'voltage',
				'features' => ['Реле контроля', 'Защита от перепадов', 'Мониторинг напряжения']
			],
			[
				'name' => 'Секвенсоры',
				'description' => 'Устройства последовательного включения с контролем напряжения',
				'url' => 'https://btx.edsy.ru/cat/sekvensory/',
				'icon' => 'ph-list-numbers',
				'color' => 'wire',
				'features' => ['Контроль напряжения', 'Последовательное включение', 'Защита оборудования']
			],
			[
				'name' => 'Рэковые дистрибьюторы',
				'description' => 'Стационарные решения с системами контроля и стабилизации напряжения',
				'url' => 'https://btx.edsy.ru/cat/rjekovye/',
				'icon' => 'ph-cube',
				'color' => 'spark',
				'features' => ['Стабилизация напряжения', 'Системы контроля', 'Профессиональное качество']
			]
		],

		'unit_measurement' => [
			[
				'name' => 'Рэковые дистрибьюторы',
				'description' => 'Стандартные 19-дюймовые решения с размерами в единицах Unit',
				'url' => 'https://btx.edsy.ru/cat/rjekovye/',
				'icon' => 'ph-cube',
				'color' => 'voltage',
				'features' => ['Стандарт 19 дюймов', 'Размеры в Unit', 'Рэковое оборудование']
			],
			[
				'name' => 'Секвенсоры',
				'description' => 'Рэковые секвенсоры стандартных размеров для профессиональных инсталляций',
				'url' => 'https://btx.edsy.ru/cat/sekvensory/',
				'icon' => 'ph-list-numbers',
				'color' => 'wire',
				'features' => ['Рэковые размеры', 'Стандартные Unit', 'Профессиональные инсталляции']
			],
			[
				'name' => 'Сопутствующие товары',
				'description' => 'Аксессуары и крепежные элементы для рэкового оборудования',
				'url' => 'https://btx.edsy.ru/cat/soputstvuyushchie-tovary/',
				'icon' => 'ph-wrench',
				'color' => 'spark',
				'features' => ['Рэковые аксессуары', 'Крепежные элементы', 'Монтажные решения']
			]
		]
	]
];
?>