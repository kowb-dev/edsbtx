<?php
/**
 * Cable Schemes Page - Схемы распайки кабелей
 * Professional cable wiring diagrams for EDS equipment
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
require_once('config.php');

// Set page title and meta
$APPLICATION->SetTitle($pageConfig['title']);
$APPLICATION->SetPageProperty('description', $pageConfig['description']);
$APPLICATION->SetPageProperty('keywords', $pageConfig['keywords']);
$APPLICATION->AddHeadString('<link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">');

// Add structured data
$structuredData = [
	'@context' => 'https://schema.org',
	'@type' => 'WebPage',
	'name' => $pageConfig['title'],
	'description' => $pageConfig['description'],
	'url' => $currentUrl,
	'mainEntity' => [
		'@type' => 'HowTo',
		'name' => 'Схемы распайки кабелей',
		'description' => 'Профессиональные схемы подключения аудио и DMX кабелей',
		'step' => []
	]
];

foreach ($schemeCategories as $scheme) {
	$structuredData['mainEntity']['step'][] = [
		'@type' => 'HowToStep',
		'name' => $scheme['title'],
		'text' => $scheme['description'],
		'image' => 'https://' . $_SERVER['HTTP_HOST'] . $scheme['image']
	];
}

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');

// Add page styles and scripts - Fixed: Only one navigation script
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/local/templates/' . SITE_TEMPLATE_ID . '/css/useful-info-navigation.css">');
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/shemy-raspajki-kabelej/styles.css">');
$APPLICATION->AddHeadString('<script defer src="/local/templates/' . SITE_TEMPLATE_ID . '/js/useful-info-navigation.js"></script>');

// Add preload hints for critical images
foreach ($schemeCategories as $scheme) {
	$APPLICATION->AddHeadString('<link rel="preload" href="' . $scheme['image'] . '" as="image" type="image/jpeg">');
}
?>

    <main class="edsys-schemes">
        <!-- Breadcrumbs -->
		<?= generateBreadcrumbs($pageConfig['breadcrumbs']) ?>

        <!-- Page Header -->
        <header class="edsys-schemes__header">
            <h1 class="edsys-schemes__title"><?= htmlspecialchars($pageConfig['title']) ?></h1>
            <p class="edsys-schemes__subtitle"><?= htmlspecialchars($pageConfig['description']) ?></p>
        </header>

        <!-- Main Layout -->
        <div class="edsys-schemes__layout">

            <!-- Sidebar with Navigation - Fixed: Remove wrapper that blocks sticky -->
			<?= renderNavigationComponent($usefulInfoNavigation, $quickNavigation) ?>

            <!-- Main Content -->
            <div class="edsys-schemes__content">
				<?php foreach ($schemeCategories as $scheme): ?>
					<?= generateSchemeSection($scheme) ?>
				<?php endforeach; ?>
            </div>
        </div>
    </main>

<?php
// Add SEO benefits - breadcrumbs structured data
$APPLICATION->AddHeadString('
<script type="application/ld+json">
{
    "@context": "https://schema.org",
    "@type": "BreadcrumbList",
    "itemListElement": [
        {
            "@type": "ListItem",
            "position": 1,
            "name": "Главная",
            "item": "https://' . $_SERVER['HTTP_HOST'] . '/"
        },
        {
            "@type": "ListItem", 
            "position": 2,
            "name": "Полезно знать",
            "item": "https://' . $_SERVER['HTTP_HOST'] . '/polezno-znat/"
        },
        {
            "@type": "ListItem",
            "position": 3,
            "name": "Схемы распайки кабелей"
        }
    ]
}
</script>
');

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>