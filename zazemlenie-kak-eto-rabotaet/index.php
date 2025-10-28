<?php
/**
 * Статья "Заземление. Как это работает?"
 * Раздел "Полезно знать" - EDS
 *
 * @version 2.0 - Адаптирована под новую структуру
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'Заземление. Как это работает?',
	'DESCRIPTION' => 'Заземление – устройство, предохраняющее человека от поражения электрическим током. Узнайте как работает заземление, чем отличается от зануления и как правильно обеспечить безопасность на площадке.',
	'KEYWORDS' => 'заземление, зануление, электробезопасность, защита от поражения током, заземляющие устройства, EDS',
	'SLUG' => 'zazemlenie-kak-eto-rabotaet',
	'IMAGE' => '/upload/useful/zazemlenie-kak-eto-rabotaet.webp',
	'DATE_PUBLISHED' => '2021-05-15T10:30:00+03:00',
	'DATE_MODIFIED' => '2021-10-12T16:45:00+03:00'
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
	'CATEGORIES_TYPE' => 'grounding_safety',
	'TITLE' => 'Решения EDS для безопасного заземления',
	'SUBTITLE' => 'Профессиональное оборудование с надежными заземляющими контактами'
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

				<?= edsysRenderSocialSharing($arArticleData['TITLE'], $currentUrl, ['whatsapp', 'telegram']) ?>
            </header>

            <!-- Главное изображение -->
            <div class="edsys-article-hero">
                <img src="<?= htmlspecialchars($arArticleData['IMAGE']) ?>"
                     alt="<?= htmlspecialchars($arArticleData['TITLE']) ?>"
                     class="edsys-article-hero__image"
                     width="784"
                     height="784"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p><strong>Заземление</strong> – устройство, предохраняющее человека от поражения электрическим током. Благодаря использованию различных заземляющих приспособлений удается избежать жертв на производстве и в быту. Собственно в этом его основное предназначение.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Что такое заземление?</h2>
                    <p>Конструктивно это чаще всего обычный кусок провода, который одним концом соединён с корпусом электрического прибора, а другим концом с землей, откуда и название.</p>

                    <div class="edsys-definition-box">
                        <h3>Принцип работы</h3>
                        <p>Суть заземления проста – служить проводником с минимальным сопротивлением для отвода опасного тока в землю.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Как работает заземление?</h2>
                    <p>Допустим, случилась аварийная ситуация – пробило фазу на корпус и он оказался под напряжением, а соответственно и вся конструкция (ферма, сцена) на которой располагается данный прибор.</p>

                    <div class="edsys-danger-warning">
                        <h3>⚠️ Опасная ситуация</h3>
                        <p>Человек ничего не подозревая может дотронуться до корпуса или конструкции, после чего его ударит током. Этого конечно может и не произойти, если ноги сухие, и обувь у вас с хорошей резиновой подошвой т.е. если вы полностью изолированы от земли.</p>
                    </div>

                    <div class="edsys-technical-note">
                        <h3>Как заземление защищает</h3>
                        <p>Для того чтобы этого не произошло, приборы должны быть заземлены. Тогда если человек коснётся корпуса, то ток пройдет не через него, а через заземление.</p>

                        <div class="edsys-resistance-comparison">
                            <div class="edsys-resistance-item">
                                <span class="edsys-resistance-value">Несколько кОм</span>
                                <span class="edsys-resistance-label">Сопротивление кожи человека</span>
                            </div>
                            <div class="edsys-vs-divider">VS</div>
                            <div class="edsys-resistance-item">
                                <span class="edsys-resistance-value">5-10 Ом</span>
                                <span class="edsys-resistance-label">Сопротивление заземляющего проводника</span>
                            </div>
                        </div>

                        <div class="edsys-conclusion-highlight">
                            <p>Выходит, что току в <strong>тысячу раз проще</strong> пройти по проводу и уйти в землю, чем пройти через человека.</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>В чем разница между заземлением и занулением?</h2>

                    <div class="edsys-comparison-grid">
                        <div class="edsys-comparison-card">
                            <h3>Заземление</h3>
                            <div class="edsys-comparison-icon">
                                <i class="ph ph-thin ph-shield-check"></i>
                            </div>
                            <p>Соединение корпуса прибора с землей через заземляющий проводник.</p>
                            <ul>
                                <li>Отводит ток утечки в землю</li>
                                <li>Снижает потенциал корпуса</li>
                                <li>Работает независимо от сети</li>
                            </ul>
                        </div>

                        <div class="edsys-comparison-card">
                            <h3>Зануление</h3>
                            <div class="edsys-comparison-icon">
                                <i class="ph ph-thin ph-lightning"></i>
                            </div>
                            <p>Соединение корпуса приемника электроэнергии с нулевым проводом.</p>
                            <ul>
                                <li>Создает короткое замыкание</li>
                                <li>Срабатывает автоматическая защита</li>
                                <li>Отключает питание при аварии</li>
                            </ul>
                        </div>
                    </div>

                    <div class="edsys-technical-note">
                        <h4>Принцип работы зануления</h4>
                        <p>Если говорить простым языком, то зануление – это соединение корпуса приемника электроэнергии с нулем. <strong>Ноль – это провод, имеющий нулевой потенциал и идущий из трансформатора.</strong></p>

                        <p>Зануление работает так: если на корпус приемника попадает провод под напряжением, то он через корпус замыкается на ноль, что вызывает короткое замыкание. Защита автоматически срабатывает и отключает питание.</p>
                    </div>
                </section>

                <section class="edsys-content-section edsys-highlight-section">
                    <h2 class="edsys-before-title">🔧 Практические рекомендации EDS</h2>

                    <div class="edsys-practical-advice">
                        <p class="edsys-before-section">К сожалению, на многих площадках нет заземления. Для этого мы рекомендуем сделать хотя бы минимальное самое простое заземление.</p>

                        <div class="edsys-instruction-steps">
                            <h3 class="edsys-before-subtitle">Как сделать простое заземление на площадке:</h3>

                            <div class="edsys-step-by-step">
                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">1</div>
                                    <div class="edsys-step-content">
                                        <h4>Подготовка заземлителя</h4>
                                        <p>Необходимо взять какой-нибудь металлический кол и забить его в землю, хотя бы на <strong>пол метра – метр</strong>.</p>
                                    </div>
                                </div>

                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">2</div>
                                    <div class="edsys-step-content">
                                        <h4>Подключение провода</h4>
                                        <p>От заземлителя бросить провод, сечением минимум <strong>6-10 квадратных миллиметров</strong> и подмотать его под специальный болт с основной раздачи.</p>
                                    </div>
                                </div>

                                <div class="edsys-step-item">
                                    <div class="edsys-step-number">3</div>
                                    <div class="edsys-step-content">
                                        <h4>Проверка системы</h4>
                                        <p>Все розетки изделий компании EDS заземлены на корпус. После данных действий при использовании правильной коммутации, все ваши приборы будут заземлены.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-eds-products-note">
                            <h4>Наши устройства</h4>
                            <p>На таких наших устройствах как <strong>R531, R552</strong>, а так же на всех рэковых изделиях присутствует специальный болтик для подмотки заземляющего провода.</p>
                        </div>

                        <div class="edsys-safety-result">
                            <h4>Результат</h4>
                            <p>И соответственно после данных действий Вы, организаторы и артисты, будете в <strong>полной безопасности</strong> 😊</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Дополнительные опасности</h2>
                    <div class="edsys-related-info">
                        <h3>Разность потенциалов</h3>
                        <p>Так же не стоит забывать об опасности возникновения разности потенциалов между корпусами приборов, от этого тоже защищает заземление, но об этом уже в следующей нашей статье.</p>

                        <a href="/raznost-potentsialov/" class="edsys-related-article-link">
                            <div class="edsys-related-article-icon">
                                <i class="ph ph-thin ph-pulse"></i>
                            </div>
                            <div class="edsys-related-article-content">
                                <h4>Разность потенциалов</h4>
                                <p>Проблемы в блоках питания оборудования и способы их решения</p>
                            </div>
                            <i class="ph ph-thin ph-arrow-right"></i>
                        </a>
                    </div>
                </section>

                <section class="edsys-content-section edsys-technical-requirements">
                    <h2>Технические требования к заземлению</h2>

                    <div class="edsys-requirements-grid">
                        <div class="edsys-requirement-item">
                            <div class="edsys-requirement-icon">
                                <i class="ph ph-thin ph-ruler"></i>
                            </div>
                            <h4>Сечение проводника</h4>
                            <p>Минимум <strong>6-10 мм²</strong> для медного провода</p>
                        </div>

                        <div class="edsys-requirement-item">
                            <div class="edsys-requirement-icon">
                                <i class="ph ph-thin ph-gauge"></i>
                            </div>
                            <h4>Сопротивление заземления</h4>
                            <p>Не более <strong>4 Ом</strong> для электроустановок до 1000В</p>
                        </div>

                        <div class="edsys-requirement-item">
                            <div class="edsys-requirement-icon">
                                <i class="ph ph-thin ph-shovel"></i>
                            </div>
                            <h4>Глубина заземлителя</h4>
                            <p>Минимум <strong>0,5-1 метр</strong> в грунт</p>
                        </div>

                        <div class="edsys-requirement-item">
                            <div class="edsys-requirement-icon">
                                <i class="ph ph-thin ph-wrench"></i>
                            </div>
                            <h4>Соединения</h4>
                            <p>Надежные <strong>болтовые соединения</strong> с антикоррозийной обработкой</p>
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