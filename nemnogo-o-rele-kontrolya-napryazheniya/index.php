<?php
/**
 * Статья "Немного о реле контроля напряжения"
 * Раздел "Полезно знать" - EDS
 *
 * @version 1.0 - Создана по образцу существующих статей
 * @author EDS Development Team
 * @since 2024-12-20
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'Немного о реле контроля напряжения',
	'DESCRIPTION' => 'Что такое РКН и зачем оно нужно. Защита от перепадов напряжения, обрыва нуля и перекоса фаз. Принцип работы реле контроля напряжения и его практическое применение.',
	'KEYWORDS' => 'реле контроля напряжения, РКН, защита от перепадов, обрыв нуля, перекос фаз, стабилизация напряжения, автоматическая защита, электробезопасность',
	'SLUG' => 'nemnogo-o-rele-kontrolya-napryazheniya',
	'IMAGE' => '/upload/useful/voltage-relay-604x315.jpg',
	'DATE_PUBLISHED' => '2021-05-10T08:00:00+03:00',
	'DATE_MODIFIED' => '2024-12-20T17:00:00+03:00'
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
	'CATEGORIES_TYPE' => 'voltage_relay_control',
	'CATEGORIES_DATA' => [
		[
			'name' => 'Устройства с защитными реле',
			'description' => 'Профессиональные дистрибьюторы с встроенными реле контроля напряжения',
			'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
			'icon' => 'ph-shield-check',
			'color' => 'voltage',
			'features' => ['Реле контроля', 'Защита от перепадов', 'Мониторинг напряжения']
		],
		[
			'name' => 'Секвенсоры',
			'description' => 'Устройства последовательного включения с контролем напряжения',
			'url' => 'https://btx.edsy.ru/cat/sekvensory/',
			'icon' => 'ph-list-numbers',
			'color' => 'wire',
			'features' => ['Контроль напряжения', 'Последовательное включение', 'Защита оборудования']
		],
		[
			'name' => 'Рэковые дистрибьюторы',
			'description' => 'Стационарные решения с системами контроля и стабилизации напряжения',
			'url' => 'https://btx.edsy.ru/cat/rjekovye/',
			'icon' => 'ph-cube',
			'color' => 'spark',
			'features' => ['Стабилизация напряжения', 'Системы контроля', 'Профессиональное качество']
		]
	],
	'TITLE' => 'Решения EDS с контролем напряжения',
	'SUBTITLE' => 'Профессиональное оборудование с защитными реле'
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
                     width="604"
                     height="315"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <h2>Что такое РКН?</h2>
                    <p><strong>Перепады напряжения</strong> – проблема частая и повсеместная. Увеличилась нагрузка на фазу, перебои в работе линии электросети, перекос фаз или обрыв нуля – все это ведет к нестабильной работе.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Проблемы нестабильного напряжения</h2>
                    <p>Поэтому необходимо знать в любой момент времени величину напряжения. Для этого обычно ставят на вход вольтметры, но они только снимают показания. Если напряжение резко возрастет, то самому отключить оборудование быстро не получится и может случиться непоправимое.</p>

                    <div class="edsys-highlight-section">
                        <h3>Решение проблемы</h3>
                        <p>А вот <strong>реле беспрерывно контролирует</strong> величину напряжения и при изменении параметров выше или ниже заданных значений моментально обесточивает всю сеть.</p>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Самая распространенная причина – обрыв нуля</h4>
                        <p>Конечно, если нагрузка на фазах одинаковая, то опасности в этом нет – напряжение равномерно распределяется по всем фазам. Но такое бывает редко и обрыв нуля провоцирует перекос фаз:</p>
                        <ul>
                            <li>На одной фазе будет <strong>140В</strong></li>
                            <li>На второй <strong>280В</strong></li>
                            <li>На третьей что осталось</li>
                        </ul>
                        <p><strong>Важно:</strong> На фазе с наименьшей нагрузкой будет самое высокое напряжение – что может привести к безвозвратной потере оборудования!</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Назначение реле контроля напряжения</h2>
                    <p><em><strong>Для того, чтобы защитить себя и все, что мы подключаем и существует реле напряжения.</strong></em></p>

                    <div class="edsys-tech-image-block">
                        <img src="/upload/useful/voltage-relay-principle-300x180.jpg"
                             alt="Принцип работы реле контроля напряжения"
                             class="edsys-technical-image"
                             width="300"
                             height="180"
                             loading="lazy">
                        <p class="edsys-image-caption">Принцип работы реле контроля напряжения</p>
                    </div>

                    <h3>Принцип действия довольно прост:</h3>
                    <div class="edsys-step-item">
                        <div class="edsys-step-number">1</div>
                        <div class="edsys-step-content">
                            <h4>Контроль параметров</h4>
                            <p>Измерительный электронный блок контролирует значения напряжения</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">2</div>
                        <div class="edsys-step-content">
                            <h4>Обнаружение отклонений</h4>
                            <p>В случае выхода параметров за границы уставки – передает сигнал на электромагнитный элемент</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">3</div>
                        <div class="edsys-step-content">
                            <h4>Защитное отключение</h4>
                            <p>Электромагнитный элемент отключает питание, защищая оборудование</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">4</div>
                        <div class="edsys-step-content">
                            <h4>Автоматическое восстановление</h4>
                            <p>При восстановлении параметров сети, через установленную выдержку времени (от 5 секунд до 15 минут), блок подает сигнал на включение</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Технические характеристики</h2>
                    <div class="edsys-technical-explanation">
                        <h4>Важные ограничения</h4>
                        <p><strong>ВАЖНО:</strong> реле может выдержать нагрузку до 3,5 кВт. Если нагрузка больше – то вместе с реле должен быть установлен контактор!</p>
                    </div>

                    <h3>Дополнительные возможности</h3>
                    <p>Для удобства контроля реле могут быть с цифровым табло и показывать текущие значения напряжения и тока сети.</p>

                    <div class="edsys-technical-explanation">
                        <h4>Преимущества современных реле</h4>
                        <ul>
                            <li><strong>Цифровое табло</strong> – отображение текущих параметров</li>
                            <li><strong>Настраиваемые уставки</strong> – верхний и нижний пороги срабатывания</li>
                            <li><strong>Задержка времени</strong> – от 5 секунд до 15 минут</li>
                            <li><strong>Память событий</strong> – сохранение информации о срабатываниях</li>
                            <li><strong>Световая индикация</strong> – визуальное отображение состояния</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Практическое применение</h2>
                    <p>Как вы видите – это не просто лишняя трата ваших денег, а инструмент, с помощью которого вы сможете защитить от поломки ваше дорогостоящее оборудование. Но ставить или нет реле напряжения – дело каждого.</p>

                    <div class="edsys-highlight-section">
                        <h3>Решения EDS</h3>
                        <p>Наша компания позаботилась о вашей безопасности и безопасности вашего оборудования и разработала <strong>серию дистрибьюторов питания со встроенными реле напряжения.</strong></p>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Области применения реле контроля напряжения</h4>
                        <ul>
                            <li><strong>Бытовая техника</strong> – холодильники, кондиционеры, стиральные машины</li>
                            <li><strong>Промышленное оборудование</strong> – станки, насосы, компрессоры</li>
                            <li><strong>Концертное оборудование</strong> – усилители, световые приборы, микшерные пульты</li>
                            <li><strong>Серверное оборудование</strong> – критически важные системы</li>
                            <li><strong>Медицинское оборудование</strong> – аппараты, требующие стабильного питания</li>
                        </ul>
                    </div>

                    <div class="edsys-expert-advice">
                        <h4>⚡ Совет от экспертов EDS</h4>
                        <p>Установка реле контроля напряжения – это инвестиция в безопасность вашего оборудования. Стоимость реле многократно окупается защитой от одной аварийной ситуации.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Настройка и эксплуатация</h2>
                    <p>Правильная настройка реле контроля напряжения – залог эффективной защиты:</p>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">1</div>
                        <div class="edsys-step-content">
                            <h4>Установка верхнего порога</h4>
                            <p>Обычно устанавливается на уровне 240-250В для сети 220В</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">2</div>
                        <div class="edsys-step-content">
                            <h4>Установка нижнего порога</h4>
                            <p>Рекомендуется устанавливать на уровне 180-190В для сети 220В</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">3</div>
                        <div class="edsys-step-content">
                            <h4>Настройка задержки времени</h4>
                            <p>Для бытовых приборов – 5-10 секунд, для промышленного оборудования – 30-60 секунд</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">4</div>
                        <div class="edsys-step-content">
                            <h4>Тестирование работы</h4>
                            <p>Регулярная проверка срабатывания реле в тестовом режиме</p>
                        </div>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Рекомендации по эксплуатации</h4>
                        <ul>
                            <li><strong>Регулярный контроль</strong> – проверка показаний дисплея</li>
                            <li><strong>Ведение журнала</strong> – фиксация случаев срабатывания</li>
                            <li><strong>Периодическое обслуживание</strong> – очистка контактов</li>
                            <li><strong>Проверка калибровки</strong> – сравнение с эталонными приборами</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Заключение</h2>
                    <p>Реле контроля напряжения – это простое, но эффективное устройство защиты электрооборудования.</p>

                    <div class="edsys-highlight-section">
                        <h3>Основные преимущества</h3>
                        <ul>
                            <li><strong>Автоматическая защита</strong> – без участия человека</li>
                            <li><strong>Быстрая реакция</strong> – срабатывание в доли секунды</li>
                            <li><strong>Настраиваемость</strong> – адаптация под конкретные условия</li>
                            <li><strong>Экономия средств</strong> – предотвращение дорогостоящих поломок</li>
                            <li><strong>Надежность</strong> – многолетняя безотказная работа</li>
                        </ul>
                    </div>

                    <div class="edsys-expert-advice">
                        <h4>🔧 Рекомендация EDS</h4>
                        <p>Для профессиональных инсталляций рекомендуем использовать дистрибьюторы питания EDS со встроенными реле контроля напряжения. Это обеспечит комплексную защиту всего оборудования.</p>
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