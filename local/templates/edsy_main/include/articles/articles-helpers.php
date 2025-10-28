<?php
/**
 * EDS Articles Helper Functions v1.0
 * Вспомогательные функции для работы со статьями
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/**
 * Generate SEO meta tags for article
 * Генерация SEO мета-тегов для статьи
 */
function edsysGenerateArticleSEO($arParams) {
	global $APPLICATION;

	// Load configuration
	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");
	$siteUrl = $config['base']['site_url'];
	$currentUrl = $siteUrl . $_SERVER['REQUEST_URI'];

	// Set title
	if (!empty($arParams['TITLE'])) {
		$APPLICATION->SetTitle($arParams['TITLE']);
	}

	// Set meta description
	if (!empty($arParams['DESCRIPTION'])) {
		$APPLICATION->SetPageProperty("description", $arParams['DESCRIPTION']);
	}

	// Set keywords
	if (!empty($arParams['KEYWORDS'])) {
		$APPLICATION->SetPageProperty("keywords", $arParams['KEYWORDS']);
	}

	// Canonical URL
	$APPLICATION->AddHeadString('<link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">');

	// Open Graph
	$APPLICATION->AddHeadString('<meta property="og:title" content="' . htmlspecialchars($arParams['TITLE'] ?? '') . '">');
	$APPLICATION->AddHeadString('<meta property="og:description" content="' . htmlspecialchars($arParams['DESCRIPTION'] ?? '') . '">');
	$APPLICATION->AddHeadString('<meta property="og:url" content="' . htmlspecialchars($currentUrl) . '">');
	$APPLICATION->AddHeadString('<meta property="og:type" content="article">');

	if (!empty($arParams['IMAGE'])) {
		$imageUrl = (strpos($arParams['IMAGE'], 'http') === 0) ? $arParams['IMAGE'] : $siteUrl . $arParams['IMAGE'];
		$APPLICATION->AddHeadString('<meta property="og:image" content="' . htmlspecialchars($imageUrl) . '">');
	}

	// Twitter Card
	$APPLICATION->AddHeadString('<meta name="twitter:card" content="summary_large_image">');
	$APPLICATION->AddHeadString('<meta name="twitter:title" content="' . htmlspecialchars($arParams['TITLE'] ?? '') . '">');
	$APPLICATION->AddHeadString('<meta name="twitter:description" content="' . htmlspecialchars($arParams['DESCRIPTION'] ?? '') . '">');

	// Additional meta tags
	$metaTags = $config['seo']['meta_tags'];
	foreach ($metaTags as $name => $content) {
		$APPLICATION->AddHeadString('<meta name="' . htmlspecialchars($name) . '" content="' . htmlspecialchars($content) . '">');
	}
}

/**
 * Generate structured data for article
 * Генерация структурированных данных для статьи
 */
function edsysGenerateArticleStructuredData($arParams) {
	global $APPLICATION;

	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");
	$siteUrl = $config['base']['site_url'];
	$currentUrl = $siteUrl . $_SERVER['REQUEST_URI'];

	$structuredData = [
		'@context' => 'https://schema.org',
		'@type' => 'Article',
		'headline' => $arParams['TITLE'] ?? '',
		'description' => $arParams['DESCRIPTION'] ?? '',
		'author' => [
			'@type' => 'Organization',
			'name' => $config['base']['organization_name']
		],
		'publisher' => [
			'@type' => 'Organization',
			'name' => $config['meta_defaults']['publisher'],
			'logo' => [
				'@type' => 'ImageObject',
				'url' => $siteUrl . $config['meta_defaults']['logo_url']
			]
		],
		'mainEntityOfPage' => [
			'@type' => 'WebPage',
			'@id' => $currentUrl
		]
	];

	// Add image if provided
	if (!empty($arParams['IMAGE'])) {
		$imageUrl = (strpos($arParams['IMAGE'], 'http') === 0) ? $arParams['IMAGE'] : $siteUrl . $arParams['IMAGE'];
		$structuredData['image'] = $imageUrl;
	}

	// Add dates if provided
	if (!empty($arParams['DATE_PUBLISHED'])) {
		$structuredData['datePublished'] = $arParams['DATE_PUBLISHED'];
	}

	if (!empty($arParams['DATE_MODIFIED'])) {
		$structuredData['dateModified'] = $arParams['DATE_MODIFIED'];
	}

	$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');
}

/**
 * Generate breadcrumbs structured data
 * Генерация структурированных данных для хлебных крошек
 */
function edsysGenerateBreadcrumbsStructuredData($arParams) {
	global $APPLICATION;

	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");
	$siteUrl = $config['base']['site_url'];

	$breadcrumbsData = [
		'@context' => 'https://schema.org',
		'@type' => 'BreadcrumbList',
		'itemListElement' => []
	];

	// Add base breadcrumbs
	foreach ($config['breadcrumbs_base'] as $item) {
		$breadcrumbsData['itemListElement'][] = [
			'@type' => 'ListItem',
			'position' => $item['position'],
			'name' => $item['name'],
			'item' => $siteUrl . $item['url']
		];
	}

	// Add current page
	if (!empty($arParams['TITLE'])) {
		$breadcrumbsData['itemListElement'][] = [
			'@type' => 'ListItem',
			'position' => count($breadcrumbsData['itemListElement']) + 1,
			'name' => $arParams['TITLE']
		];
	}

	$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($breadcrumbsData, JSON_UNESCAPED_UNICODE) . '</script>');
}

/**
 * Load article assets (CSS, JS)
 * Загрузка ресурсов статьи
 */
function edsysLoadArticleAssets($options = []) {
	global $APPLICATION;

	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");
	$assetsPath = $config['base']['assets_path'];

	$defaults = [
		'load_common_css' => true,
		'load_navigation_css' => true,
		'load_core_js' => true,
		'load_navigation_js' => true,
		'load_utils_js' => true,
		'version' => time() // For cache busting in development
	];

	$options = array_merge($defaults, $options);

	// Load CSS
	if ($options['load_common_css']) {
		$APPLICATION->AddHeadString('<link rel="stylesheet" href="' . $assetsPath . '/css/edsys-articles-common.css?v=' . $options['version'] . '">');
	}

	if ($options['load_navigation_css']) {
		$APPLICATION->AddHeadString('<link rel="stylesheet" href="' . $assetsPath . '/css/edsys-articles-navigation.css?v=' . $options['version'] . '">');
	}

	// Load JavaScript
	if ($options['load_core_js']) {
		$APPLICATION->AddHeadString('<script src="' . $assetsPath . '/js/edsys-articles-core.js?v=' . $options['version'] . '" defer></script>');
	}

	if ($options['load_navigation_js']) {
		$APPLICATION->AddHeadString('<script src="' . $assetsPath . '/js/edsys-articles-navigation.js?v=' . $options['version'] . '" defer></script>');
	}

	if ($options['load_utils_js']) {
		$APPLICATION->AddHeadString('<script src="' . $assetsPath . '/js/edsys-articles-utils.js?v=' . $options['version'] . '" defer></script>');
	}
}

/**
 * Generate social sharing URLs
 * Генерация URL для социального шаринга
 */
function edsysGenerateSocialSharingUrls($title, $url = null) {
	if ($url === null) {
		$url = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
	}

	return [
		'whatsapp' => 'https://wa.me/?text=' . urlencode($title . ' - EDS') . '%0A' . urlencode($url),
		'telegram' => 'https://t.me/share/url?url=' . urlencode($url) . '&text=' . urlencode($title . ' - EDS'),
		'vk' => 'https://vk.com/share.php?url=' . urlencode($url) . '&title=' . urlencode($title),
		'facebook' => 'https://www.facebook.com/sharer/sharer.php?u=' . urlencode($url),
		'twitter' => 'https://twitter.com/intent/tweet?text=' . urlencode($title) . '&url=' . urlencode($url)
	];
}

/**
 * Render social sharing buttons
 * Отрисовка кнопок социального шаринга
 */
function edsysRenderSocialSharing($title, $url = null, $platforms = ['whatsapp', 'telegram']) {
	$urls = edsysGenerateSocialSharingUrls($title, $url);

	$platformNames = [
		'whatsapp' => 'WhatsApp',
		'telegram' => 'Telegram',
		'vk' => 'ВКонтакте',
		'facebook' => 'Facebook',
		'twitter' => 'Twitter'
	];

	$platformIcons = [
		'whatsapp' => 'ph-whatsapp-logo',
		'telegram' => 'ph-telegram-logo',
		'vk' => 'ph-share-network',
		'facebook' => 'ph-facebook-logo',
		'twitter' => 'ph-twitter-logo'
	];

	$output = '<div class="edsys-article-actions">';
	$output .= '<span class="edsys-action-btn-title">Поделиться:</span>';

	foreach ($platforms as $platform) {
		if (!isset($urls[$platform])) continue;

		$platformName = $platformNames[$platform] ?? ucfirst($platform);
		$platformIcon = $platformIcons[$platform] ?? 'ph-share';
		$platformClass = 'edsys-action-btn--' . $platform;

		$output .= '<a href="' . htmlspecialchars($urls[$platform]) . '" ';
		$output .= 'class="edsys-action-btn ' . $platformClass . '" ';
		$output .= 'target="_blank" rel="noopener" ';
		$output .= 'title="Поделиться в ' . htmlspecialchars($platformName) . '">';
		$output .= '<i class="ph ' . $platformIcon . '" aria-hidden="true"></i>';
		$output .= '<span>' . htmlspecialchars($platformName) . '</span>';
		$output .= '</a>';
	}

	$output .= '</div>';

	return $output;
}

/**
 * Render breadcrumbs
 * Отрисовка хлебных крошек
 */
function edsysRenderBreadcrumbs($currentTitle) {
	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");

	$output = '<nav class="edsys-breadcrumbs" aria-label="Навигация">';
	$output .= '<ol class="edsys-breadcrumbs__list">';

	// Base breadcrumbs
	foreach ($config['breadcrumbs_base'] as $item) {
		$output .= '<li class="edsys-breadcrumbs__item">';
		$output .= '<a href="' . htmlspecialchars($item['url']) . '" class="edsys-breadcrumbs__link">' . htmlspecialchars($item['name']) . '</a>';
		$output .= '<i class="ph ph-thin ph-caret-right" aria-hidden="true"></i>';
		$output .= '</li>';
	}

	// Current page
	$output .= '<li class="edsys-breadcrumbs__item">';
	$output .= '<span class="edsys-breadcrumbs__current">' . htmlspecialchars($currentTitle) . '</span>';
	$output .= '</li>';

	$output .= '</ol>';
	$output .= '</nav>';

	return $output;
}

/**
 * Get article navigation data
 * Получение данных навигации статьи
 */
function edsysGetArticleNavigation($currentSlug = null) {
	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");
	$navigation = $config['navigation'];

	// Sort by SORT field
	usort($navigation, function($a, $b) {
		return ($a['SORT'] ?? 0) - ($b['SORT'] ?? 0);
	});

	// Find current article index
	$currentIndex = -1;
	if ($currentSlug) {
		foreach ($navigation as $index => $item) {
			if ($item['SLUG'] === $currentSlug) {
				$currentIndex = $index;
				break;
			}
		}
	}

	$result = [
		'current' => $currentIndex >= 0 ? $navigation[$currentIndex] : null,
		'prev' => $currentIndex > 0 ? $navigation[$currentIndex - 1] : null,
		'next' => $currentIndex >= 0 && $currentIndex < count($navigation) - 1 ? $navigation[$currentIndex + 1] : null,
		'all' => $navigation
	];

	return $result;
}

/**
 * Render article navigation
 * Отрисовка навигации по статьям
 */
function edsysRenderArticleNavigation($currentSlug = null) {
	$nav = edsysGetArticleNavigation($currentSlug);

	$output = '<nav class="edsys-article-navigation">';

	// Previous article
	if ($nav['prev']) {
		$output .= '<a href="' . htmlspecialchars($nav['prev']['URL']) . '" class="edsys-article-nav-link edsys-article-nav-prev">';
		$output .= '<div class="edsys-article-nav-direction">';
		$output .= '<i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>';
		$output .= '<span>Предыдущая статья</span>';
		$output .= '</div>';
		$output .= '<div class="edsys-article-nav-title">' . htmlspecialchars($nav['prev']['NAME']) . '</div>';
		$output .= '</a>';
	}

	// All articles link
	$output .= '<a href="/polezno-znat/" class="edsys-article-nav-all">';
	$output .= '<i class="ph ph-list" aria-hidden="true"></i>';
	$output .= '<span>Все статьи</span>';
	$output .= '</a>';

	// Next article
	if ($nav['next']) {
		$output .= '<a href="' . htmlspecialchars($nav['next']['URL']) . '" class="edsys-article-nav-link edsys-article-nav-next">';
		$output .= '<div class="edsys-article-nav-direction">';
		$output .= '<span>Следующая статья</span>';
		$output .= '<i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>';
		$output .= '</div>';
		$output .= '<div class="edsys-article-nav-title">' . htmlspecialchars($nav['next']['NAME']) . '</div>';
		$output .= '</a>';
	}

	$output .= '</nav>';

	return $output;
}

/**
 * Sanitize and validate article data
 * Санитизация и валидация данных статьи
 */
function edsysSanitizeArticleData($data) {
	$sanitized = [];

	// Title
	$sanitized['TITLE'] = !empty($data['TITLE']) ? strip_tags(trim($data['TITLE'])) : '';

	// Description
	$sanitized['DESCRIPTION'] = !empty($data['DESCRIPTION']) ? strip_tags(trim($data['DESCRIPTION'])) : '';

	// Keywords
	$sanitized['KEYWORDS'] = !empty($data['KEYWORDS']) ? strip_tags(trim($data['KEYWORDS'])) : '';

	// Image URL
	if (!empty($data['IMAGE'])) {
		$sanitized['IMAGE'] = filter_var($data['IMAGE'], FILTER_SANITIZE_URL);
	}

	// Dates
	if (!empty($data['DATE_PUBLISHED'])) {
		$sanitized['DATE_PUBLISHED'] = date('c', strtotime($data['DATE_PUBLISHED']));
	}

	if (!empty($data['DATE_MODIFIED'])) {
		$sanitized['DATE_MODIFIED'] = date('c', strtotime($data['DATE_MODIFIED']));
	}

	// Slug
	if (!empty($data['SLUG'])) {
		$sanitized['SLUG'] = preg_replace('/[^a-z0-9\-]/', '', strtolower($data['SLUG']));
	}

	return $sanitized;
}

/**
 * Get cache key for article
 * Получение ключа кэша для статьи
 */
function edsysGetArticleCacheKey($slug, $additional = '') {
	return 'edsys_article_' . md5($slug . $additional . SITE_ID);
}

/**
 * Log article error
 * Логирование ошибки статьи
 */
function edsysLogArticleError($message, $context = []) {
	if (function_exists('AddMessage2Log')) {
		$logMessage = $message;
		if (!empty($context)) {
			$logMessage .= ' Context: ' . json_encode($context, JSON_UNESCAPED_UNICODE);
		}
		AddMessage2Log($logMessage, 'edsys_articles');
	}
}

/**
 * Format file size
 * Форматирование размера файла
 */
function edsysFormatFileSize($size) {
	$units = ['B', 'KB', 'MB', 'GB'];
	$unitIndex = 0;

	while ($size >= 1024 && $unitIndex < count($units) - 1) {
		$size /= 1024;
		$unitIndex++;
	}

	return round($size, 2) . ' ' . $units[$unitIndex];
}

/**
 * Get article reading time estimate
 * Оценка времени чтения статьи
 */
function edsysGetReadingTime($content, $wordsPerMinute = 200) {
	$wordCount = str_word_count(strip_tags($content));
	$minutes = ceil($wordCount / $wordsPerMinute);

	if ($minutes < 1) {
		return 'менее минуты';
	} elseif ($minutes === 1) {
		return '1 минута';
	} elseif ($minutes < 5) {
		return $minutes . ' минуты';
	} else {
		return $minutes . ' минут';
	}
}

/**
 * Generate article excerpt
 * Генерация выдержки из статьи
 */
function edsysGenerateExcerpt($content, $length = 150, $suffix = '...') {
	$text = strip_tags($content);
	$text = preg_replace('/\s+/', ' ', $text);
	$text = trim($text);

	if (mb_strlen($text) <= $length) {
		return $text;
	}

	$text = mb_substr($text, 0, $length);
	$lastSpace = mb_strrpos($text, ' ');

	if ($lastSpace !== false) {
		$text = mb_substr($text, 0, $lastSpace);
	}

	return $text . $suffix;
}

/**
 * Check if article exists
 * Проверка существования статьи
 */
function edsysArticleExists($slug) {
	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");

	foreach ($config['navigation'] as $article) {
		if ($article['SLUG'] === $slug) {
			return true;
		}
	}

	return false;
}

/**
 * Get article by slug
 * Получение статьи по слагу
 */
function edsysGetArticleBySlug($slug) {
	$config = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");

	foreach ($config['navigation'] as $article) {
		if ($article['SLUG'] === $slug) {
			return $article;
		}
	}

	return null;
}