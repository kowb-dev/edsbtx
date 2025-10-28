<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

/**
 * Component epilog for EDS product detail
 * Additional scripts and meta tags connection
 * 
 * Version: 2.0.0
 * Author: KW
 * URI: https://kowb.ru
 */

$asset = Asset::getInstance();

// === CSS and JS connection ===
$templateFolder = $this->__template->GetFolder();
$asset->addCss($templateFolder . "/style.css");
$asset->addJs($templateFolder . "/script.js");

// === SEO meta tags ===
if (!empty($arResult['NAME'])) {
	$APPLICATION->SetTitle($arResult['NAME']);

	// Meta description
	$metaDescription = $arResult['NAME'];
	if (!empty($arResult['SCHEMA']['CATEGORY'])) {
		$metaDescription .= ' в категории ' . $arResult['SCHEMA']['CATEGORY'];
	}
	$metaDescription .= '. Характеристики, цены, наличие на сайте EDS.';

	$APPLICATION->SetPageProperty("description", $metaDescription);

	// Social media meta tags
	$asset->addString('<meta property="og:title" content="' . htmlspecialchars($arResult['NAME']) . '">', false);
	$asset->addString('<meta property="og:description" content="' . htmlspecialchars($metaDescription) . '">', false);
	$asset->addString('<meta property="og:type" content="product">', false);

	if (!empty($arResult['IMAGES'][0]['SRC'])) {
		$asset->addString('<meta property="og:image" content="' . htmlspecialchars($arResult['IMAGES'][0]['SRC']) . '">', false);
	}
}

// === Structured data JSON-LD ===
$jsonLD = [
	"@context" => "https://schema.org/",
	"@type" => "Product",
	"name" => $arResult['NAME'],
	"sku" => $arResult['SCHEMA']['SKU'],
	"category" => $arResult['SCHEMA']['CATEGORY'],
	"brand" => [
		"@type" => "Brand",
		"name" => "EDS"
	]
];

// Add images
if (!empty($arResult['IMAGES'])) {
	$images = [];
	foreach ($arResult['IMAGES'] as $image) {
		$images[] = $image['SRC'];
	}
	$jsonLD["image"] = $images;
}

// Add description from specifications
if (!empty($arResult['SPECIFICATIONS'])) {
	$description = [];
	foreach ($arResult['SPECIFICATIONS'] as $spec) {
		$description[] = $spec['NAME'] . ': ' . $spec['VALUE'];
	}
	$jsonLD["description"] = implode('. ', array_slice($description, 0, 3)) . '.';
}

// Add price for authorized users
if (!empty($arResult['PRICES_PROCESSED']['USER']['VALUE'])) {
	$jsonLD["offers"] = [
		"@type" => "Offer",
		"priceCurrency" => "RUB",
		"price" => $arResult['PRICES_PROCESSED']['USER']['VALUE'],
		"availability" => "https://schema.org/" . $arResult['SCHEMA']['AVAILABILITY'],
		"seller" => [
			"@type" => "Organization",
			"name" => "EDS"
		]
	];
}

$asset->addString('<script type="application/ld+json">' . json_encode($jsonLD, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>', false);

// === Pass data to JavaScript ===
$jsConfig = [
	'productId' => $arResult['ID'],
	'productName' => $arResult['NAME'],
	'hasMultipleImages' => !empty($arResult['IMAGES']) ? count($arResult['IMAGES']) > 1 : false,
	'isAuthorized' => $USER->IsAuthorized(),
	'canBuy' => $arResult['USER_ACCESS']['CAN_BUY'] ?? false,
	'relatedCount' => !empty($arResult['RELATED_PRODUCTS']) ? count($arResult['RELATED_PRODUCTS']) : 0,
	'sessid' => bitrix_sessid(), // Add sessid to config
	'ajaxUrls' => [
		'addToCart' => '/local/ajax/basket/add.php',
		'addToWishlist' => '/local/ajax/wishlist/add.php',
		'addToCompare' => '/local/ajax/compare/add.php'
	]
];

$asset->addString('<script>window.EDSProductConfig = ' . json_encode($jsConfig) . ';</script>', false);

// Add sessid to meta tag for easy access
$asset->addString('<meta name="bitrix-sessid" content="' . bitrix_sessid() . '">', false);

// === Breadcrumbs for search engines ===
if (!empty($arResult['BREADCRUMBS'])) {
	$breadcrumbsLD = [
		"@context" => "https://schema.org",
		"@type" => "BreadcrumbList",
		"itemListElement" => []
	];

	foreach ($arResult['BREADCRUMBS'] as $index => $breadcrumb) {
		$item = [
			"@type" => "ListItem",
			"position" => $index + 1,
			"name" => $breadcrumb['TITLE']
		];

		if (!empty($breadcrumb['LINK'])) {
			$item["item"] = $breadcrumb['LINK'];
		}

		$breadcrumbsLD["itemListElement"][] = $item;
	}

	$asset->addString('<script type="application/ld+json">' . json_encode($breadcrumbsLD, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>', false);
}

// === Canonical URL ===
$canonicalUrl = $APPLICATION->GetCurDir();
$asset->addString('<link rel="canonical" href="' . htmlspecialchars($canonicalUrl) . '">', false);

// === Preload for critical resources ===
if (!empty($arResult['IMAGES'][0]['SRC'])) {
	$asset->addString('<link rel="preload" href="' . htmlspecialchars($arResult['IMAGES'][0]['SRC']) . '" as="image">', false);
}

// === CSS variables for dynamic styles ===
$cssVars = [];

// Gallery images count
if (!empty($arResult['IMAGES']) && is_array($arResult['IMAGES'])) {
	$cssVars[] = '--edsys-gallery-count: ' . count($arResult['IMAGES']);
}

// Related products count
if (!empty($arResult['RELATED_PRODUCTS']) && is_array($arResult['RELATED_PRODUCTS'])) {
	$cssVars[] = '--edsys-related-count: ' . count($arResult['RELATED_PRODUCTS']);
}

if (!empty($cssVars)) {
	$asset->addString('<style>:root { ' . implode('; ', $cssVars) . '; }</style>', false);
}

// === Add to viewing history ===
if ($USER->IsAuthorized() && !empty($arResult['ID'])) {
	$_SESSION['LAST_VIEWED_PRODUCTS'] = $_SESSION['LAST_VIEWED_PRODUCTS'] ?: [];

	array_unshift($_SESSION['LAST_VIEWED_PRODUCTS'], $arResult['ID']);

	$_SESSION['LAST_VIEWED_PRODUCTS'] = array_unique($_SESSION['LAST_VIEWED_PRODUCTS']);
	$_SESSION['LAST_VIEWED_PRODUCTS'] = array_slice($_SESSION['LAST_VIEWED_PRODUCTS'], 0, 20);
}

// === Send event to Google Analytics / Yandex Metrika ===
if (!empty($arResult['ANALYTICS'])) {
	$analyticsData = [
		'event' => 'view_item',
		'ecommerce' => [
			'currency' => 'RUB',
			'value' => $arResult['ANALYTICS']['PRICE'],
			'items' => [
				[
					'item_id' => $arResult['ANALYTICS']['PRODUCT_ID'],
					'item_name' => $arResult['ANALYTICS']['PRODUCT_NAME'],
					'item_category' => $arResult['ANALYTICS']['CATEGORY'],
					'price' => $arResult['ANALYTICS']['PRICE'],
					'quantity' => 1
				]
			]
		]
	];

	$asset->addString('<script>
        window.dataLayer = window.dataLayer || [];
        window.dataLayer.push(' . json_encode($analyticsData) . ');
    </script>', false);
}

// === Template caching ===
if ($arParams["CACHE_TYPE"] == "Y" && $arParams["CACHE_TIME"] > 0) {
	$cache_id = md5(serialize($arResult) . $USER->GetGroups());
	$cache_dir = "/edsys_product_detail/" . $arResult['ID'];

	$CACHE_MANAGER->RegisterTag("iblock_id_" . $arResult['IBLOCK_ID']);
	$CACHE_MANAGER->RegisterTag("iblock_element_" . $arResult['ID']);
}

// === Check indexing by robots ===
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
$isBot = preg_match('/bot|crawl|spider|googlebot|yandexbot/i', $userAgent);

if ($isBot) {
	$asset->addString('<meta name="robots" content="index,follow">', false);
	$asset->addString('<meta name="priority" content="0.8">', false);
}

// === Security ===
// CSP for external resources (VK Video)
$asset->addString('<meta http-equiv="Content-Security-Policy" content="frame-src \'self\' vk.com *.vk.com vkvideo.ru *.vkvideo.ru">', false);

// === Load optimization ===
// DNS prefetch for external resources
$asset->addString('<link rel="dns-prefetch" href="//vk.com">', false);
$asset->addString('<link rel="dns-prefetch" href="//unpkg.com">', false);

// === Debug information ===
if (defined('DEBUG') && DEBUG === true) {
	$asset->addString('<script>console.log("EDS Product Detail Template Loaded", ' . json_encode([
			'productId' => $arResult['ID'],
			'imagesCount' => !empty($arResult['IMAGES']) ? count($arResult['IMAGES']) : 0,
			'relatedCount' => !empty($arResult['RELATED_PRODUCTS']) ? count($arResult['RELATED_PRODUCTS']) : 0,
			'hasVideo' => !empty($arResult['VIDEO']),
			'userAuthorized' => $USER->IsAuthorized(),
			'basketIntegration' => true
		]) . ');</script>', false);
}
?>