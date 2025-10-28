<?php
/**
 * Статья "Разность потенциалов"
 * Раздел "Полезно знать" - EDS
 *
 * @version 2.0 - Адаптирована под новую структуру
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'Разность потенциалов',
	'SUBTITLE' => 'Как избежать потребности в починке блоков питания в экранах, головах, активных колонках',
	'DESCRIPTION' => 'Разность потенциалов в блоках питания оборудования. Как избежать потребности в починке блоков питания в экранах, головах, активных колонках. Профессиональные советы от EDS.',
	'KEYWORDS' => 'разность потенциалов, блоки питания, импульсные источники питания, заземление оборудования, помехи, фильтры, EDS',
	'SLUG' => 'raznost-potentsialov',
	'IMAGE' => '/upload/useful/raznost-potentsialov.jpg',
	'DATE_PUBLISHED' => '2021-05-10T14:20:00+03:00',
	'DATE_MODIFIED' => '2021-10-15T11:30:00+03:00'
]);

// Generate SEO
edsysGenerateArticleSEO($arArticleData);
edsysGenerateArticleStructuredData($arArticleData);
edsysGenerateBreadcrumbsStructuredData($arArticleData);

// Load assets
edsysLoadArticleAssets([
	'version' => '1.0'
]);

// Get current URL for sharing
$currentUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Navigation parameters
$arNavParams = [
	'CURRENT_ARTICLE' => $arArticleData['SLUG']
];

// Product categories for this article
$arCategoriesParams = [
	'CATEGORIES_TYPE' => 'potential_difference',
	'TITLE' => 'Решения EDS для устранения разности потенциалов',
	'SUBTITLE' => 'Профессиональное оборудование для безопасной работы без помех'
];
?>

    <main class="edsys-article-page">

		<?php
		// Include navigation component
		$arParams = $arNavParams;
		include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/assets/components/sidebar/articles-navigation.php");
		?>

        <!-- Основной контент -->
        <div class="edsys-article-content">

			<?= edsysRenderBreadcrumbs($arArticleData['TITLE']) ?>

            <!-- Заголовок статьи -->
            <header class="edsys-article-header">
                <h1 class="edsys-article-title"><?= htmlspecialchars($arArticleData['TITLE']) ?></h1>
				<?php if (!empty($arArticleData['SUBTITLE'])): ?>
                    <p class="edsys-article-subtitle"><?= htmlspecialchars($arArticleData['SUBTITLE']) ?></p>
				<?php endif; ?>

				<?= edsysRenderSocialSharing($arArticleData['TITLE'], $currentUrl, ['whatsapp', 'telegram']) ?>
            </header>

            <!-- Главное изображение -->
            <div class="edsys-article-hero">
                <img src="<?= htmlspecialchars($arArticleData['IMAGE']) ?>"
                     alt="<?= htmlspecialchars($arArticleData['TITLE']) ?>"
                     class="edsys-article-hero__image"
                     width="584"
                     height="584"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p>Повесили вы L-Acoustics, запустили DiGiCo, включили LA-8, достаете свой старенький ACER и думаете: «сейчас как дуну, вот запоет-то!!!», а там в порталах фон, гудеж, пердеж... <strong>Знакомо???</strong></p>

                    <p>А знаете почему это происходит? Давайте инженеры компании EDS сейчас Вам все разложат по полочкам.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Причина проблемы</h2>
                    <div class="edsys-highlight-box">
                        <p>Дело в том, что <strong>корпус пульта и земля блока питания объединены между собой</strong> (вставлены в одну розетницу) и не заземлены и между ними возникает разность потенциалов.</p>

                        <div class="edsys-question-answer">
                            <h3>💡 Логичный вопрос</h3>
                            <p>Вроде бы тогда спрашивается: «зачем нужны эти злощавые колодки с заземлением?»</p>
                            <p class="edsys-answer"><strong>Ответ прост</strong> – прежде всего для <strong>Вашей безопасности!</strong></p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Откуда берется разность потенциалов?</h2>
                    <p>Все это происходит из-за <strong>импульсных блоков питания</strong>, в них установлен фильтр, гасящий высокочастотные помехи и сбрасывающий их в землю, а земля и корпус прибора всегда объединены.</p>

                    <div class="edsys-technical-note">
                        <h3>⚡ Важная деталь</h3>
                        <p>Хорошо, что ток там незначительный, так как это всего-лишь <strong>ток помех</strong>.</p>
                    </div>

                    <div class="edsys-scheme-block">
                        <h3>Схема стандартного фильтра</h3>
                        <div class="edsys-scheme-image">
                            <img src="/upload/useful/scheme.jpg"
                                 alt="Схема стандартного фильтра импульсного блока питания"
                                 class="edsys-technical-image"
                                 width="616"
                                 height="162"
                                 loading="lazy">
                        </div>
                        <p class="edsys-scheme-caption">Схема стандартного фильтра импульсного блока питания</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Что страшного в разности потенциалов?</h2>
                    <p>Дело в том, что как писали мы ранее ток незначительный, но вся беда, что он <strong>складывается при подключении множества блоков питания</strong>.</p>

                    <div class="edsys-danger-progression">
                        <h3>⚠️ Нарастание проблемы</h3>
                        <div class="edsys-danger-levels">
                            <div class="edsys-danger-level">
                                <div class="edsys-danger-icon">
                                    <i class="ph ph-thin ph-speaker-simple-high"></i>
                                </div>
                                <div class="edsys-danger-content">
                                    <h4>2 активные колонки</h4>
                                    <p>Может это не так страшно...</p>
                                </div>
                            </div>

                            <div class="edsys-danger-level">
                                <div class="edsys-danger-icon">
                                    <i class="ph ph-thin ph-monitor"></i>
                                </div>
                                <div class="edsys-danger-content">
                                    <h4>LED экран + 60 бимов</h4>
                                    <p>Уже серьезная проблема!</p>
                                </div>
                            </div>

                            <div class="edsys-danger-level edsys-danger-level--critical">
                                <div class="edsys-danger-icon">
                                    <i class="ph ph-thin ph-warning"></i>
                                </div>
                                <div class="edsys-danger-content">
                                    <h4>Еще и фаза на корпус попадет</h4>
                                    <p>Критическая ситуация!</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-consequences">
                        <h3>Последствия</h3>
                        <p>Дотронетесь вы до корпуса прибора и <strong>бабах</strong>, все накопленное уйдет через Вас и Ваши промокшие ботинки в землю.</p>

                        <div class="edsys-consequences-variants">
                            <div class="edsys-consequence-item edsys-consequence-item--mild">
                                <h4>Легко отделались</h4>
                                <p>Просто в паре блоков сгорят фильтры или выбьет ШИМ</p>
                            </div>

                            <div class="edsys-consequence-item edsys-consequence-item--severe">
                                <h4>Серьезные последствия</h4>
                                <p>Может и <strong>Вас отстранить от прокатов... на долго...</strong></p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-technical-insight">
                    <h2>Техническое объяснение</h2>
                    <div class="edsys-technical-content">
                        <p>Всегда не стоит забывать, что <strong>устройства, рассчитанные на заземления проектируются так, как будто у вас заземление есть</strong>.</p>

                        <div class="edsys-device-logic">
                            <h3>Логика устройства</h3>
                            <p>Устройство правомерно считает, что в случае внештатной ситуации оно может сбросить излишки тока в землю.</p>
                        </div>

                        <div class="edsys-reality-check">
                            <h3>Реальность</h3>
                            <p>Но все мы работали и в старых ДК и с ленивыми генераторщиками и понимаем, что <strong>в половине случаев заземления нет</strong>.</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-highlight-section">
                    <h2 class="edsys-before-title">🔧 Практические рекомендации EDS</h2>

                    <div class="edsys-practical-solution">
                        <h3 class="edsys-before-subtitle">Простое решение</h3>
                        <p class="edsys-before-section">Не ленитесь, забивайте хотя бы кол в землю... и кидайте провод от кола к нашим дистрибьюторам.</p>

                        <div class="edsys-recommended-product">
                            <h4>Рекомендуемое решение</h4>
                            <div class="edsys-product-highlight">
                                <div class="edsys-product-icon">
                                    <i class="ph ph-thin ph-plugs-connected"></i>
                                </div>
                                <a href="/product/r-551av" class="edsys-product-info">
                                    <h5>R 551AV </h5>
                                    <p>Самая ходовая модель дистрибьютора с надежным заземлением</p>
                                </a>
                            </div>
                        </div>

                        <div class="edsys-step-by-step">
                            <h4>Пошаговая инструкция</h4>
                            <div class="edsys-steps">
                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">1</div>
                                    <div class="edsys-step-content">
                                        <h5>Забить заземлитель</h5>
                                        <p>Забить металлический кол в землю на глубину минимум 0,5-1 метр</p>
                                    </div>
                                </div>

                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">2</div>
                                    <div class="edsys-step-content">
                                        <h5>Подключить провод</h5>
                                        <p>Кинуть провод от кола к заземляющему контакту дистрибьютора</p>
                                    </div>
                                </div>

                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">3</div>
                                    <div class="edsys-step-content">
                                        <h5>Получить результат</h5>
                                        <p>Устранить разность потенциалов и обеспечить безопасность</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Дополнительная информация</h2>
                    <div class="edsys-related-info">
                        <h3>Связанные статьи</h3>
                        <p>Более подробно о заземлении вы можете почитать в нашей статье:</p>

                        <a href="/zazemlenie-kak-eto-rabotaet/" class="edsys-related-article-link">
                            <div class="edsys-related-article-icon">
                                <i class="ph ph-thin ph-shield-check"></i>
                            </div>
                            <div class="edsys-related-article-content">
                                <h4>Заземление. Как это работает?</h4>
                                <p>Подробное объяснение принципов заземления и его важности для безопасности</p>
                            </div>
                            <i class="ph ph-thin ph-arrow-right"></i>
                        </a>
                    </div>
                </section>

                <section class="edsys-content-section edsys-summary">
                    <h2>Резюме</h2>
                    <div class="edsys-summary-content">
                        <h3>Основные принципы</h3>
                        <ul class="edsys-summary-list">
                            <li>Разность потенциалов возникает из-за импульсных блоков питания с фильтрами</li>
                            <li>Проблема усугубляется при подключении множества устройств</li>
                            <li>Отсутствие заземления создает опасность для оборудования и людей</li>
                            <li>Простое заземление решает большинство проблем</li>
                        </ul>

                        <div class="edsys-final-advice">
                            <h4>💡 Главный совет</h4>
                            <p>Всегда используйте качественные дистрибьюторы с надежным заземлением и не пренебрегайте элементарными мерами безопасности!</p>
                        </div>
                    </div>
                </section>
            </article>

			<?php
			// Include product categories component
			$arParams = $arCategoriesParams;
			include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/assets/components/blocks/product-categories.php");
			?>

			<?= edsysRenderArticleNavigation($arArticleData['SLUG']) ?>
        </div>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>