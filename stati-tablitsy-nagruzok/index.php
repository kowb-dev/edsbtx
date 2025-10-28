<?php
/**
 * Cable Load Tables Page - Professional Version with Integrated Navigation
 * Страница таблиц токовых нагрузок медных кабелей с интегрированной навигацией
 */

// Bitrix prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Include component and navigation config
require_once(__DIR__ . '/component.php');
require_once(__DIR__ . '/navigation-config.php');

// Initialize component
$cableTables = new CableTables();

// Page meta data
$APPLICATION->SetTitle("Таблицы токовых нагрузок медных кабелей");
$APPLICATION->SetPageProperty("title", "Таблицы токовых нагрузок медных кабелей - EDS");
$APPLICATION->SetPageProperty("description", "Подробные таблицы токовых нагрузок для кабелей XTREM H07RN-F, КГтп-ХЛ и проводов ПуГВ. Технические характеристики: сечение, диаметр, масса, допустимая токовая нагрузка.");
$APPLICATION->SetPageProperty("keywords", "таблицы токовых нагрузок, кабель XTREM H07RN-F, КГтп-ХЛ, ПуГВ, сечение кабеля, токовая нагрузка, технические характеристики кабелей");

// Structured data for SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "TechArticle",
	"headline" => "Таблицы токовых нагрузок медных кабелей",
	"description" => "Технические таблицы с характеристиками кабелей для профессионального применения",
	"author" => [
		"@type" => "Organization",
		"name" => "EDS - Electric Distribution Systems"
	],
	"publisher" => [
		"@type" => "Organization",
		"name" => "EDS",
		"logo" => [
			"@type" => "ImageObject",
			"url" => "https://edsy.ru/logo.png"
		]
	],
	"datePublished" => "2024-01-15",
	"dateModified" => date('Y-m-d'),
	"mainEntityOfPage" => [
		"@type" => "WebPage",
		"@id" => "https://edsy.ru/stati-tablitsy-nagruzok/"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');

// Add page styles and scripts - include useful info navigation
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/local/templates/' . SITE_TEMPLATE_ID . '/css/useful-info-navigation.css" />');
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/stati-tablitsy-nagruzok/styles.css" />');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/local/templates/' . SITE_TEMPLATE_ID . '/js/useful-info-navigation.js" defer></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/stati-tablitsy-nagruzok/scripts.js" defer></script>');

// Get cable data and navigation data
$cableTypes = $cableTables->getCableTypes();
$localNavigationData = $cableTables->getLocalNavigationData();

// Prepare navigation data for integration
$navigationData = prepareUsefulInfoNavigationData($arUsefulInfoNavigation, $localNavigationData);

?>

    <div class="edsys-cable-page">
        <div class="edsys-cable-container">

            <!-- Breadcrumbs -->
			<?= $cableTables->renderBreadcrumbs() ?>

            <!-- Hero Section -->
            <section class="edsys-cable-hero">
                <h1 class="edsys-cable-hero__title">Таблицы токовых нагрузок медных кабелей</h1>
                <p class="edsys-cable-hero__subtitle">
                    Подробные технические характеристики профессиональных кабелей для сценического и технического оборудования
                </p>
            </section>

            <!-- Cable Navigation Cards -->
			<?= $cableTables->renderNavigationCards() ?>

            <!-- Main Content with Integrated Sidebar -->
            <div class="edsys-cable-main">

                <!-- Integrated Sidebar Navigation (Desktop) -->
				<?= $cableTables->renderIntegratedNavigation($arUsefulInfoNavigation, $localNavigationData) ?>

                <!-- Cable Tables Content -->
                <main class="edsys-cable-content">

                    <!-- XTREM H07RN-F Cable Table -->
					<?= $cableTables->renderTable('xtrem') ?>

                    <!-- КГтп-ХЛ Cable Table -->
					<?= $cableTables->renderTable('kgtp') ?>

                    <!-- ПуГВ Cable Table -->
					<?= $cableTables->renderTable('pugv') ?>

                </main>

            </div>

            <!-- Additional Information -->
            <section class="edsys-cable-info">
                <h3>Важная информация по применению</h3>
                <div class="edsys-cable-info__grid">
                    <div class="edsys-cable-info__item">
                        <i class="ph ph-warning-thin"></i>
                        <div>
                            <h4>Условия эксплуатации</h4>
                            <p>Указанные токовые нагрузки действительны при температуре окружающей среды +25°C и определенных условиях прокладки.</p>
                        </div>
                    </div>
                    <div class="edsys-cable-info__item">
                        <i class="ph ph-thermometer-thin"></i>
                        <div>
                            <h4>Температурные поправки</h4>
                            <p>При повышенных температурах необходимо применять понижающие коэффициенты согласно ГОСТ и ПУЭ.</p>
                        </div>
                    </div>
                    <div class="edsys-cable-info__item">
                        <i class="ph ph-calculator-thin"></i>
                        <div>
                            <h4>Расчеты и калькуляторы</h4>
                            <p>Воспользуйтесь нашими <a href="/kalkulyatory/" class="edsys-link">онлайн калькуляторами</a> для точного расчета параметров.</p>
                        </div>
                    </div>
                    <div class="edsys-cable-info__item">
                        <i class="ph ph-certificate-thin"></i>
                        <div>
                            <h4>Сертификация</h4>
                            <p>Все кабели соответствуют российским и международным стандартам качества и безопасности.</p>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Related Sections -->
			<?= $cableTables->renderRelatedSections() ?>

        </div>

        <!-- Mobile Navigation is now handled by useful-info-navigation component -->

    </div>

    <!-- Initialize integrated navigation -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize useful info navigation
            if (window.EDSUsefulNavigation) {
                window.EDSUsefulNavigation.init();
            }

            // Initialize cable tables functionality
            if (window.EdsysCableTables) {
                // Cable tables navigation will work with useful info navigation
                console.log('Cable Tables with integrated navigation initialized');
            }
        });
    </script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>