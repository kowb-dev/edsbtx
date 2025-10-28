<?php
/**
 * Настройки раздела "Статьи и полезная информация"
 * /stati-tablitsy-nagruzok/.section.php
 */

$sSectionName = "Статьи и полезная информация";
$arDirProperties = array(
	"TITLE" => "Статьи и полезная информация | EDS - Electric Distribution Systems",
	"DESCRIPTION" => "Профессиональные статьи, руководства и полезная информация от экспертов EDS по электрооборудованию, распределению электроэнергии, сценическому оборудованию и безопасности работы с электричеством.",
	"KEYWORDS" => "статьи EDS, электрооборудование, сценическое оборудование, распределение электроэнергии, профессиональные решения, безопасность, руководства, полезная информация",
	"ROBOTS" => "index, follow",
	"CANONICAL" => "https://edsy.ru/stati-tablitsy-nagruzok/",

	// Open Graph для социальных сетей
	"OG_TYPE" => "website",
	"OG_TITLE" => "Статьи и полезная информация | EDS",
	"OG_DESCRIPTION" => "Профессиональные статьи по электрооборудованию и сценическому оборудованию от экспертов EDS",
	"OG_IMAGE" => "https://edsy.ru/stati-tablitsy-nagruzok/assets/images/og-articles.jpg",
	"OG_URL" => "https://edsy.ru/stati-tablitsy-nagruzok/",
	"OG_SITE_NAME" => "EDS - Electric Distribution Systems",

	// Twitter Card
	"TWITTER_CARD" => "summary_large_image",
	"TWITTER_TITLE" => "Статьи и полезная информация | EDS",
	"TWITTER_DESCRIPTION" => "Профессиональные статьи по электрооборудованию и сценическому оборудованию",
	"TWITTER_IMAGE" => "https://edsy.ru/stati-tablitsy-nagruzok/assets/images/twitter-articles.jpg",

	// Дополнительные мета-теги
	"AUTHOR" => "EDS - Electric Distribution Systems",
	"COPYRIGHT" => "© 2025 EDS - Electric Distribution Systems",
	"GENERATOR" => "1С-Битрикс: Управление сайтом",
	"RATING" => "general",
	"REVISIT_AFTER" => "7 days",

	// Настройки для поисковых систем
	"GOOGLE_SITE_VERIFICATION" => "", // Добавить при необходимости
	"YANDEX_VERIFICATION" => "", // Добавить при необходимости

	// Локализация
	"LANGUAGE" => "ru",
	"CONTENT_LANGUAGE" => "ru",
	"HREFLANG" => "ru-RU",

	// Настройки кэширования
	"CACHE_TIME" => "3600", // 1 час
	"CACHE_TYPE" => "A", // Автокэширование

	// Дополнительные настройки для SEO
	"SECTION_CODE" => "stati-tablitsy-nagruzok",
	"SECTION_NAME" => $sSectionName,
	"SECTION_ACTIVE" => "Y",
	"SECTION_GLOBAL_ACTIVE" => "Y",
	"SECTION_SORT" => "100",

	// Настройки для навигации
	"MENU_TEXT" => "Статьи",
	"MENU_TITLE" => "Статьи и полезная информация",
	"MENU_DESCRIPTION" => "Профессиональные статьи и руководства",
	"MENU_ICON" => "ph-article",

	// Настройки для хлебных крошек
	"BREADCRUMB_TITLE" => "Статьи",
	"BREADCRUMB_DESCRIPTION" => "Полезная информация",

	// Дополнительные CSS и JS файлы
	"ADDITIONAL_CSS" => array(
		"/stati-tablitsy-nagruzok/assets/css/articles.css",
		"/stati-tablitsy-nagruzok/assets/css/articles-responsive.css"
	),
	"ADDITIONAL_JS" => array(
		"/stati-tablitsy-nagruzok/assets/js/articles.js",
		"/stati-tablitsy-nagruzok/assets/js/search.js"
	),

	// Настройки для мобильных устройств
	"MOBILE_OPTIMIZED" => "Y",
	"VIEWPORT" => "width=device-width, initial-scale=1.0",
	"MOBILE_WEB_APP_CAPABLE" => "yes",
	"MOBILE_WEB_APP_STATUS_BAR_STYLE" => "black-translucent",

	// Настройки для PWA (если планируется)
	"THEME_COLOR" => "#ff2545",
	"MANIFEST" => "/manifest.json",

	// Настройки аналитики
	"GOOGLE_ANALYTICS" => "", // Добавить при необходимости
	"YANDEX_METRIKA" => "", // Добавить при необходимости

	// Настройки для комментариев и социальных сетей
	"FACEBOOK_APP_ID" => "", // Добавить при необходимости
	"VK_APP_ID" => "", // Добавить при необходимости
);

// Настройки для компонентов Битрикс
$arComponentSettings = array(
	// Настройки для news.list (если будет использоваться)
	"NEWS_LIST_TEMPLATE" => "edsys_articles",
	"NEWS_LIST_CACHE_TIME" => "3600",
	"NEWS_LIST_CACHE_GROUPS" => "Y",

	// Настройки для поиска
	"SEARCH_TEMPLATE" => "edsys_search",
	"SEARCH_CACHE_TIME" => "3600",

	// Настройки для комментариев
	"COMMENTS_TEMPLATE" => "edsys_comments",
	"COMMENTS_CACHE_TIME" => "3600",

	// Настройки для тегов
	"TAGS_TEMPLATE" => "edsys_tags",
	"TAGS_CACHE_TIME" => "3600",
);

// Настройки для индексации
$arIndexSettings = array(
	"INDEX_SECTION" => "Y",
	"INDEX_ELEMENT" => "Y",
	"SITEMAP_SECTION" => "Y",
	"SITEMAP_ELEMENT" => "Y",
	"SITEMAP_PRIORITY" => "0.8",
	"SITEMAP_CHANGEFREQ" => "weekly",
);

// Безопасность и права доступа
$arSecuritySettings = array(
	"ACCESS_CODE" => array(), // Пустой массив = доступ для всех
	"WORKFLOW" => "N",
	"BIZPROC" => "N",
	"SECTION_PERMISSION" => array(
		"1" => "R", // Все группы - чтение
		"2" => "R", // Авторизованные - чтение
	),
);

// Экспорт всех настроек
$arDirProperties = array_merge($arDirProperties, $arComponentSettings, $arIndexSettings, $arSecuritySettings);
?>