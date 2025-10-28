<?php
/**
 * EDS Product Overview Page - Mobile-First Professional Design v4.0
 * Fully responsive PHP/HTML structure with comprehensive mobile optimization
 *
 * @author EDS Development Team
 * @version 4.0
 * @created 2025-06-27
 * @updated 2025-06-28
 * @file index.php
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Enhanced mobile-first page configuration
$APPLICATION->SetTitle("О выпускаемой продукции — Профессиональные решения EDS");
$APPLICATION->SetPageProperty("description", "Инновационные силовые дистрибьюторы премиум-класса с европейскими компонентами. Профессиональные решения для сценического и технического оборудования от Electric Distribution Systems.");
$APPLICATION->SetPageProperty("keywords", "EDS силовые дистрибьюторы, профессиональное электрооборудование, европейские компоненты, PCE Австрия, Legrand Франция, Black Edition, сценическое оборудование");

// Mobile-optimized viewport meta tag
$APPLICATION->AddHeadString('<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">');

// Mobile-specific meta tags
$APPLICATION->AddHeadString('<meta name="format-detection" content="telephone=no">');
$APPLICATION->AddHeadString('<meta name="mobile-web-app-capable" content="yes">');
$APPLICATION->AddHeadString('<meta name="apple-mobile-web-app-capable" content="yes">');
$APPLICATION->AddHeadString('<meta name="apple-mobile-web-app-status-bar-style" content="default">');
$APPLICATION->AddHeadString('<meta name="theme-color" content="#21242D">');

// Performance optimizations
$APPLICATION->AddHeadString('<link rel="preload" href="/o-produktsii/style.css" as="style">');
$APPLICATION->AddHeadString('<link rel="dns-prefetch" href="//fonts.googleapis.com">');

// Add local assets from same directory
$APPLICATION->SetAdditionalCSS("/o-produktsii/style.css");
$APPLICATION->AddHeadScript("/o-produktsii/modal.js");

// Enhanced Schema.org data with mobile considerations
$schemaData = [
	"@context" => "https://schema.org",
	"@type" => "Organization",
	"name" => "Electric Distribution Systems (EDS)",
	"description" => "Лидер в производстве профессиональных силовых дистрибьюторов и электрооборудования премиум-класса",
	"url" => "https://edsy.ru",
	"foundingDate" => "2010",
	"specialty" => "Силовые дистрибьюторы для профессионального применения",
	"address" => [
		"@type" => "PostalAddress",
		"addressCountry" => "RU"
	],
	"sameAs" => [
		"https://edsy.ru/o-produktsii/"
	]
];

// Detect mobile user agent for server-side optimizations
$isMobile = preg_match('/(android|iphone|ipad|mobile)/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
?>

    <!-- Enhanced Schema.org structured data -->
    <script type="application/ld+json">
    <?= json_encode($schemaData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) ?>
</script>

    <!-- Mobile-optimized main content wrapper -->
    <main class="edsys-product-page" role="main" itemscope itemtype="https://schema.org/WebPage">
        <div class="edsys-product-container">

            <!-- Mobile-first professional header -->
            <header class="edsys-product-header">
                <h1 class="edsys-product-title" itemprop="name">О выпускаемой продукции</h1>
            </header>

            <!-- Mobile-optimized asymmetric content layout -->
            <article class="edsys-product-content" itemscope itemtype="https://schema.org/Article" itemprop="mainEntity">

                <section class="edsys-content-grid">

                    <!-- Introduction text block with mobile optimization -->
                    <div class="edsys-text-intro">
                        <div class="edsys-text-content" itemprop="text">
                            <p>
                                Создавая силовые дистрибьюторы, компания
                                <strong class="edsys-highlight" itemprop="author" itemscope itemtype="https://schema.org/Organization">
                                    <span itemprop="name">Electric Distribution Systems (EDS)</span>
                                </strong>
                                опирается на личный многолетний опыт работы своих сотрудников в сфере
                                прокатной деятельности, технического обеспечения мероприятий и инсталляций.
                            </p>

                            <p>
                                Зная и понимая все проблемы, которые есть в работе данных сфер, мы предлагаем
                                рынку качественные устройства распределения электроэнергии, испытанные «на себе».
                            </p>
                        </div>
                    </div>

                    <!-- Mobile-optimized product image with enhanced accessibility -->
                    <aside class="edsys-image-container"
                           role="img"
                           aria-labelledby="product-diagram"
                           itemscope
                           itemtype="https://schema.org/ImageObject">

                        <img src="/images/product-overview.jpg"
                             alt="Профессиональная техническая схема силового дистрибьютора EDS с детальной маркировкой компонентов: прочный корпус из стали 1,5мм, автоматические выключатели Legrand, высококачественные компоненты PCE, система индикации, эргономичные ручки для транспортировки"
                             class="edsys-product-image"
                             width="600"
                             height="400"
                             loading="lazy"
                             decoding="async"
                             itemprop="contentUrl"
                             itemProp="image"
                             tabindex="0"
                             data-modal-trigger
                             role="button"
                             aria-label="Увеличить изображение технической схемы"
							<?= $isMobile ? 'data-mobile="true"' : '' ?>>

                        <!-- Enhanced screen reader description -->
                        <div id="product-diagram" class="sr-only">
                            Инженерная схема конструкции профессионального силового дистрибьютора с выделением технических особенностей.
							<?= $isMobile ? 'Коснитесь' : 'Нажмите' ?> для увеличения изображения.
                        </div>

                        <!-- Mobile hint overlay -->
						<?php if ($isMobile): ?>
                            <div class="edsys-mobile-hint sr-only" aria-live="polite">
                                Коснитесь изображения для просмотра в полном размере
                            </div>
						<?php endif; ?>
                    </aside>

                    <!-- Details section with mobile optimization -->
                    <div class="edsys-text-details">
                        <div class="edsys-text-content" itemprop="text">
                            <p>
                                Именно поэтому все корпуса наших приборов выполнены из
                                <strong class="edsys-highlight" itemprop="material">1,5 мм стали</strong> и специального
                                алюминиевого профиля с продольным пазом для крепления устройств.
                            </p>

                            <p>
                                Сам корпус покрашен полиэфирной краской черного матового цвета,
                                разводка внутри устройств осуществляется проводом, имеющим запас
                                по максимальной нагрузке.
                            </p>
                        </div>
                    </div>

                    <!-- Continuation section with mobile-friendly text -->
                    <div class="edsys-text-continuation">
                        <div class="edsys-text-content" itemprop="text">
                            <p>
                                Мы устанавливаем компоненты только ведущих европейских производителей:
                                <strong class="edsys-highlight" itemprop="manufacturer">PCE (Австрия)</strong>,
                                <strong class="edsys-highlight" itemprop="manufacturer">Legrand (Франция)</strong>,
                                <strong class="edsys-highlight" itemprop="manufacturer">Top Cable (Испания)</strong>.
                            </p>

                            <p>
                                Функционал выпускаемых нами мобильных электрораспределителей очень широк:
                                от обычных «проходных дистрибьюторов» до систем с защитой и измерительными приборами.
                            </p>
                        </div>
                    </div>

                    <!-- Mobile-optimized technical specifications sidebar -->
                    <aside class="edsys-specs-sidebar"
                           role="complementary"
                           aria-labelledby="tech-specs"
                           itemscope
                           itemtype="https://schema.org/TechArticle">

                        <h2 id="tech-specs" class="edsys-specs-title" itemprop="name">
                            Технические особенности
                        </h2>

                        <ul class="edsys-specs-list" itemprop="articleBody">
                            <li class="edsys-specs-item" itemprop="keywords">Корпус из стали 1,5 мм</li>
                            <li class="edsys-specs-item" itemprop="keywords">Алюминиевый профиль с пазом</li>
                            <li class="edsys-specs-item" itemprop="keywords">Европейские компоненты</li>
                            <li class="edsys-specs-item" itemprop="keywords">Полиэфирная покраска</li>
                            <li class="edsys-specs-item" itemprop="keywords">Запас по нагрузке</li>
                            <li class="edsys-specs-item" itemprop="keywords">Серия Black Edition</li>
                        </ul>
                    </aside>

                </section>

            </article>

            <!-- Mobile-optimized call-to-action section -->
            <section class="edsys-cta-section"
                     role="banner"
                     aria-labelledby="cta-message"
                     itemscope
                     itemtype="https://schema.org/PromotionalOffer">

                <div class="edsys-cta-content">
                    <h2 id="cta-message" class="sr-only">Заключительное сообщение</h2>
                    <p class="edsys-cta-text" itemprop="description">
                        Особое внимание мы уделяем не только функционалу, но и внешнему виду
                        электросиловых дистрибьюторов. Хорошим примером служит специальная серия
                        <strong itemprop="category">«Black Edition»</strong>, собранная только из черных компонентов.
                        Выбирая продукцию от компании EDS, Вы делаете правильный выбор
                        и приобретаете высококачественные устройства.
                    </p>
                </div>
            </section>

        </div>
    </main>

    <!-- Mobile-first Professional Modal Image Viewer -->
    <dialog id="imageModal"
            class="edsys-modal-overlay"
            role="dialog"
            aria-labelledby="modal-title"
            aria-describedby="modal-description"
            aria-hidden="true"
            aria-modal="true">

        <div class="edsys-modal-container" role="document">

            <!-- Mobile-optimized modal header -->
            <header class="edsys-modal-header">
                <h2 id="modal-title" class="edsys-modal-title">
                    Техническая схема дистрибьютора EDS
                </h2>
                <button class="edsys-modal-close"
                        type="button"
                        aria-label="Закрыть модальное окно"
                        title="Закрыть (Esc)"
                        data-dismiss="modal">
                    <span class="sr-only">Закрыть</span>
                </button>
            </header>

            <!-- Mobile-friendly modal content -->
            <div class="edsys-modal-content">
                <img id="modalImage"
                     class="edsys-modal-image"
                     src=""
                     alt=""
                     loading="lazy"
                     decoding="async">

                <div id="modal-description" class="edsys-modal-caption">
                    Детальная техническая схема профессионального силового дистрибьютора с маркировкой всех ключевых компонентов:
                    корпус из стали 1,5мм, европейские автоматические выключатели, система индикации и эргономичные элементы конструкции.
					<?= $isMobile ? 'Смахните вниз для закрытия изображения.' : '' ?>
                </div>
            </div>
        </div>
    </dialog>

    <!-- Mobile-specific enhancements -->
<?php if ($isMobile): ?>
    <script>
        // Mobile-specific optimizations
        document.addEventListener('DOMContentLoaded', function() {
            // Disable double-tap zoom on images
            const images = document.querySelectorAll('.edsys-product-image');
            images.forEach(img => {
                img.addEventListener('touchend', function(e) {
                    e.preventDefault();
                });
            });

            // Add touch indicators
            document.documentElement.classList.add('touch-device');

            // Optimize viewport for mobile
            const viewport = document.querySelector('meta[name=viewport]');
            if (viewport && window.innerWidth <= 768) {
                viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=5.0, user-scalable=yes, viewport-fit=cover');
            }
        });
    </script>
<?php endif; ?>

    <!-- Accessibility announcements -->
    <div aria-live="polite" aria-atomic="true" class="sr-only" id="announcements"></div>

    <!-- Performance optimization: Intersection Observer for lazy loading -->
    <script>
        // Enhanced lazy loading for mobile performance
        if ('IntersectionObserver' in window) {
            const imageObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const img = entry.target;
                        if (img.dataset.src) {
                            img.src = img.dataset.src;
                            img.removeAttribute('data-src');
                            observer.unobserve(img);
                        }
                    }
                });
            }, {
                rootMargin: '50px 0px',
                threshold: 0.01
            });

            // Observe images that haven't loaded yet
            document.addEventListener('DOMContentLoaded', function() {
                const lazyImages = document.querySelectorAll('img[data-src]');
                lazyImages.forEach(img => imageObserver.observe(img));
            });
        }

        // Mobile performance monitoring
        if (typeof window !== 'undefined' && 'performance' in window) {
            window.addEventListener('load', function() {
                // Log performance metrics for mobile optimization
                const perfData = performance.getEntriesByType('navigation')[0];
                if (perfData && perfData.loadEventEnd > 0) {
                    console.log('EDS Page Load Time:', Math.round(perfData.loadEventEnd - perfData.fetchStart) + 'ms');
                }
            });
        }
    </script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>