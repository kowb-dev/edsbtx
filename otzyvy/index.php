<?php
/**
 * Reviews Page - Отзывы о нашей работе
 * Version: 1.0.0
 * Date: 2025-07-16
 * Description: Страница с отзывами клиентов EDS
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Отзывы о нашей работе - Electric Distribution Systems");
$APPLICATION->SetPageProperty("description", "Отзывы клиентов о работе компании EDS. Профессиональное электрооборудование для сцен, театров и концертных площадок. Качество проверено временем.");
$APPLICATION->SetPageProperty("keywords", "отзывы EDS, отзывы о дистрибьюторах питания, отзывы электрооборудование, профессиональное оборудование отзывы");

// Structured data for reviews
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "Organization",
	"name" => "Electric Distribution Systems",
	"url" => "https://edsy.ru",
	"review" => [
		[
			"@type" => "Review",
			"author" => [
				"@type" => "Person",
				"name" => "Дмитрий Сазонов"
			],
			"reviewBody" => "Произошёл казус, по моему ранее заказанному дистрибьютору проехала 2 тонная машина... И О ЧУДО — ему ничего! Он жив здоров и отработал мероприятие отменно!",
			"reviewRating" => [
				"@type" => "Rating",
				"ratingValue" => "5",
				"bestRating" => "5"
			]
		]
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">'.json_encode($structuredData).'</script>');
?>

    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/otzyvy/style.css">
    </head>
    <body>
    <div class="edsys-reviews">
        <div class="edsys-reviews__container">
            <!-- Sidebar -->
            <aside class="edsys-reviews__sidebar">
                <div class="edsys-sidebar">
                    <div class="edsys-sidebar__header">
                        <button class="edsys-sidebar__toggle" aria-label="Показать категории">
                            <i class="ph ph-thin ph-list"></i>
                        </button>
                        <h3 class="edsys-sidebar__title">Категории товаров</h3>
                    </div>

                    <div class="edsys-sidebar__content">
                        <div class="edsys-sidebar__section">
                            <h4 class="edsys-sidebar__section-title">
                                <i class="ph ph-thin ph-lightning"></i>
                                Дистрибьюторы питания
                            </h4>
                            <ul class="edsys-sidebar__list">
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/turovye/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-cube"></i>
                                        Туровые дистрибьюторы
                                    </a>
                                </li>
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/rjekovye/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-stack"></i>
                                        Рэковые дистрибьюторы
                                    </a>
                                </li>
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/vvod-ot-63a/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-plug"></i>
                                        Ввод от 63A
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="edsys-sidebar__section">
                            <h4 class="edsys-sidebar__section-title">
                                <i class="ph ph-thin ph-shield-check"></i>
                                Защитное оборудование
                            </h4>
                            <ul class="edsys-sidebar__list">
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-shield"></i>
                                        Устройства с защитными реле
                                    </a>
                                </li>
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/seriya-black-edition/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-star"></i>
                                        Серия Black Edition
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="edsys-sidebar__section">
                            <h4 class="edsys-sidebar__section-title">
                                <i class="ph ph-thin ph-gear"></i>
                                Управление
                            </h4>
                            <ul class="edsys-sidebar__list">
                                <li class="edsys-sidebar__item">
                                    <a href="https://btx.edsy.ru/cat/distribyutori-so-vstroennym-splitterom/" class="edsys-sidebar__link">
                                        <i class="ph ph-thin ph-tree-structure"></i>
                                        Дистрибьюторы со сплиттером
                                    </a>
                                </li>
                            </ul>
                        </div>

                        <div class="edsys-sidebar__cta">
                            <a href="/catalog/" class="edsys-sidebar__cta-link">
                                <i class="ph ph-thin ph-arrow-right"></i>
                                Весь каталог
                            </a>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Main Content -->
            <main class="edsys-reviews__main">
                <header class="edsys-reviews__header">
                    <h1 class="edsys-reviews__title">Отзывы о нашей работе</h1>
                    <p class="edsys-reviews__description">
                        Мы гордимся доверием наших клиентов и благодарны за каждый отзыв.
                        Ваше мнение помогает нам становиться лучше и предлагать еще более качественные решения
                        для профессионального электрооборудования.
                    </p>
                </header>

                <div class="edsys-reviews__content">
                    <!-- Review 1 -->
                    <article class="edsys-testimonial" itemscope itemtype="https://schema.org/Review">
                        <div class="edsys-testimonial__icon">
                            <i class="ph ph-thin ph-quotes"></i>
                        </div>
                        <div class="edsys-testimonial__content">
                            <blockquote class="edsys-testimonial__text" itemprop="reviewBody">
                                Всем привет! Произошёл казус, по моему ранее заказанному дистрибьютору проехала 2 тонная машина,
                                больше того после крика моего «стой» тачка проехала обратно ещё раз И О ЧУДО — ему ничего!
                                Он жив здоров и отработал мероприятие отменно! Немного откололись ручки, но это же не влияет
                                на работоспособность!!! Вообщем заказывайте все в #EDS- не дорого, качественно, быстро,
                                надежно — проверено опытом!!!
                            </blockquote>
                        </div>
                        <footer class="edsys-testimonial__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                            <div class="edsys-testimonial__author-info">
                                <span class="edsys-testimonial__author-name" itemprop="name">Дмитрий Сазонов</span>
                                <span class="edsys-testimonial__author-location">Санкт-Петербург</span>
                                <span class="edsys-testimonial__author-company">Кухня Звука</span>
                            </div>
                            <a href="https://vk.com/topic-106046682_36309757?post=19"
                               class="edsys-testimonial__link"
                               target="_blank"
                               rel="nofollow noopener"
                               aria-label="Перейти к источнику отзыва">
                                <i class="ph ph-thin ph-arrow-square-out"></i>
                            </a>
                        </footer>
                    </article>

                    <!-- EDS Response -->
                    <article class="edsys-testimonial edsys-testimonial--response">
                        <div class="edsys-testimonial__icon">
                            <i class="ph ph-thin ph-lightning"></i>
                        </div>
                        <div class="edsys-testimonial__content">
                            <blockquote class="edsys-testimonial__text">
                                Дмитрий, спасибо за отзыв! Мы вышлем вам комплект ручек за наш счет!
                            </blockquote>
                        </div>
                        <footer class="edsys-testimonial__author">
                            <div class="edsys-testimonial__author-info">
                                <span class="edsys-testimonial__author-name">Electric Distribution Systems</span>
                                <span class="edsys-testimonial__author-company">Ответ компании</span>
                            </div>
                        </footer>
                    </article>

                    <!-- Review 2 -->
                    <article class="edsys-testimonial" itemscope itemtype="https://schema.org/Review">
                        <div class="edsys-testimonial__icon">
                            <i class="ph ph-thin ph-quotes"></i>
                        </div>
                        <div class="edsys-testimonial__content">
                            <blockquote class="edsys-testimonial__text" itemprop="reviewBody">
                                Узнали о ребятах просто из рекламы Вконтакте. Сделали заказ — очень рады!!!
                                Легкое, человечное общение…быстрые сроки… приятные цены!….прекрасное качество монтажа внутри!…
                                подкупающее внимание к мелочам (покраска, ножки, серийники, гравировка)………сертификация!
                                Всем рекомендую!
                            </blockquote>
                        </div>
                        <footer class="edsys-testimonial__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                            <div class="edsys-testimonial__author-info">
                                <span class="edsys-testimonial__author-name" itemprop="name">Андрей Козлов</span>
                                <span class="edsys-testimonial__author-location">Казань</span>
                                <span class="edsys-testimonial__author-company">KOZAUDIO_RU</span>
                            </div>
                            <a href="https://vk.com/topic-106046682_36309757?post=21"
                               class="edsys-testimonial__link"
                               target="_blank"
                               rel="nofollow noopener"
                               aria-label="Перейти к источнику отзыва">
                                <i class="ph ph-thin ph-arrow-square-out"></i>
                            </a>
                        </footer>
                    </article>

                    <!-- Review 3 -->
                    <article class="edsys-testimonial" itemscope itemtype="https://schema.org/Review">
                        <div class="edsys-testimonial__icon">
                            <i class="ph ph-thin ph-quotes"></i>
                        </div>
                        <div class="edsys-testimonial__content">
                            <blockquote class="edsys-testimonial__text" itemprop="reviewBody">
                                Спасибо от бойцов, командиров и политработников омской компании «Октава» за надежную конструкцию,
                                отличную сборку и лютую антивандальную упаковку при отгрузке! До сих пор пупырки целлофановые щелкаем.
                                Антону Чернявскому отдельное человеческое спасибо за профессионализм, отзывчивость, оперативность
                                и симпатичную лазерную графику нашего лого на корпусах блоков! А еще за сертификаты соответствия
                                и фирменные паспорта на каждую единицу. Я вообще впервые увидел живой паспорт на блок розеток.
                            </blockquote>
                        </div>
                        <footer class="edsys-testimonial__author" itemprop="author" itemscope itemtype="https://schema.org/Person">
                            <div class="edsys-testimonial__author-info">
                                <span class="edsys-testimonial__author-name" itemprop="name">Марат Мамонов</span>
                                <span class="edsys-testimonial__author-location">Омск</span>
                                <span class="edsys-testimonial__author-company">Октава</span>
                            </div>
                            <a href="https://vk.com/wall-106046682_717"
                               class="edsys-testimonial__link"
                               target="_blank"
                               rel="nofollow noopener"
                               aria-label="Перейти к источнику отзыва">
                                <i class="ph ph-thin ph-arrow-square-out"></i>
                            </a>
                        </footer>
                    </article>
                </div>

                <div class="edsys-reviews__cta">
                    <p class="edsys-reviews__cta-text">
                        Хотите поделиться своим опытом работы с нами?
                    </p>
                    <a href="/contacty/" class="edsys-reviews__cta-button">
                        <i class="ph ph-thin ph-chat-circle"></i>
                        Оставить отзыв
                    </a>
                </div>
            </main>
        </div>
    </div>

    <script src="/otzyvy/script.js"></script>
    </body>
    </html>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>