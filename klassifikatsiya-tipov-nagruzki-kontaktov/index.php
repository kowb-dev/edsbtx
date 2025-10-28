<?php
/**
 * Contact Load Classification Page - Main Page
 * Страница классификации типов нагрузки контактов
 */

// Bitrix prolog
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Include configuration
require_once(__DIR__ . '/config.php');

// Page meta data
$APPLICATION->SetTitle($pageConfig['title']);
$APPLICATION->SetPageProperty("title", $pageConfig['title'] . " - EDS");
$APPLICATION->SetPageProperty("description", $pageConfig['description']);
$APPLICATION->SetPageProperty("keywords", $pageConfig['keywords']);

// SEO structured data
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "TechArticle",
	"headline" => $pageConfig['title'],
	"description" => $pageConfig['description'],
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
	"datePublished" => "2024-01-20",
	"dateModified" => date('Y-m-d'),
	"mainEntityOfPage" => [
		"@type" => "WebPage",
		"@id" => "https://edsy.ru/klassifikatsiya-tipov-nagruzki-kontaktov/"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');

// Add breadcrumbs structured data
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
            "name": "Классификация типов нагрузки контактов"
        }
    ]
}
</script>
');

// Add page styles and scripts
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/local/templates/' . SITE_TEMPLATE_ID . '/css/useful-info-navigation.css" />');
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/klassifikatsiya-tipov-nagruzki-kontaktov/styles.css" />');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/local/templates/' . SITE_TEMPLATE_ID . '/js/useful-info-navigation.js" defer></script>');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/klassifikatsiya-tipov-nagruzki-kontaktov/scripts.js" defer></script>');

// Add preload hints for critical resources
$APPLICATION->AddHeadString('<link rel="preload" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css" as="style">');
$APPLICATION->AddHeadString('<link rel="dns-prefetch" href="//btx.edsy.ru">');
?>

    <main class="edsys-classification">
        <!-- Breadcrumbs -->
		<?= generateBreadcrumbs($pageConfig['breadcrumbs']) ?>

        <!-- Header Section -->
        <header class="edsys-classification__header">
            <h1 class="edsys-classification__title"><?= htmlspecialchars($pageConfig['title']) ?></h1>
            <p class="edsys-classification__subtitle">
                Подробное техническое руководство по классификации электрических нагрузок и выбору релейного оборудования для профессионального применения
            </p>
        </header>

        <!-- Main Layout -->
        <div class="edsys-classification__layout">
            <!-- Sidebar Navigation -->
			<?= renderNavigationComponent($usefulInfoNavigation) ?>

            <!-- Main Content -->
            <div class="edsys-classification__content">
                <!-- Classification Table Section -->
                <section class="edsys-classification__table-section" aria-label="Таблица классификации нагрузок">
					<?= generateClassificationTable($classificationData) ?>
                </section>

                <!-- Technical Notes Section -->
				<?= generateTechnicalNotes($technicalNotes) ?>

                <!-- Related Products Section -->
				<?= generateRelatedProducts($relatedProducts) ?>
            </div>
        </div>
    </main>

    <!-- Initialize page functionality -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize useful info navigation
            if (window.EDSUsefulNavigation) {
                window.EDSUsefulNavigation.init();
            }

            // Initialize classification page functionality
            if (window.EDSClassification) {
                window.EDSClassification.init();
            }

            // Performance monitoring
            if ('performance' in window) {
                window.addEventListener('load', () => {
                    const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
                    console.log('Classification page load time:', loadTime + 'ms');

                    // Analytics
                    if (typeof gtag !== 'undefined') {
                        gtag('event', 'page_load_time', {
                            event_category: 'performance',
                            event_label: 'classification_page',
                            value: Math.round(loadTime)
                        });
                    }
                });
            }

            console.log('Classification page initialized successfully');
        });

        // Handle print functionality
        window.addEventListener('beforeprint', function() {
            document.body.classList.add('printing');
        });

        window.addEventListener('afterprint', function() {
            document.body.classList.remove('printing');
        });

        // SEO and accessibility enhancements
        document.addEventListener('DOMContentLoaded', function() {
            // Add skip link for accessibility
            const skipLink = document.createElement('a');
            skipLink.href = '#main-content';
            skipLink.textContent = 'Перейти к основному содержанию';
            skipLink.className = 'skip-link';
            skipLink.style.cssText = `
        position: absolute;
        top: -40px;
        left: 6px;
        background: var(--edsys-accent);
        color: white;
        padding: 8px;
        text-decoration: none;
        z-index: 9999;
        border-radius: 4px;
        transition: top 0.3s;
    `;

            skipLink.addEventListener('focus', function() {
                this.style.top = '6px';
            });

            skipLink.addEventListener('blur', function() {
                this.style.top = '-40px';
            });

            document.body.insertBefore(skipLink, document.body.firstChild);

            // Add main content ID for skip link
            const mainContent = document.querySelector('.edsys-classification__content');
            if (mainContent) {
                mainContent.id = 'main-content';
                mainContent.setAttribute('tabindex', '-1');
            }
        });

        // Error handling
        window.addEventListener('error', function(e) {
            console.error('Classification page error:', e.error);

            // Analytics for error tracking
            if (typeof gtag !== 'undefined') {
                gtag('event', 'exception', {
                    description: e.error?.message || 'Unknown error',
                    fatal: false
                });
            }
        });

        // Service Worker registration for future PWA features
        if ('serviceWorker' in navigator && location.protocol === 'https:') {
            window.addEventListener('load', function() {
                navigator.serviceWorker.register('/sw.js').then(function(registration) {
                    console.log('SW registered: ', registration);
                }).catch(function(registrationError) {
                    console.log('SW registration failed: ', registrationError);
                });
            });
        }
    </script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>