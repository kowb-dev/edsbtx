<?php
/**
 * Статья "Сравнение КГтп-ХЛ и H07RN-F"
 * Раздел "Полезно знать" - EDS
 *
 * @version 2.0 - Адаптирована под новую структуру
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'Сравнение КГтп-ХЛ и H07RN-F',
	'SUBTITLE' => 'Какой кабель выбрать для сферы проката и инсталляций профессионального оборудования?',
	'DESCRIPTION' => 'Подробное сравнение российского кабеля КГтп-ХЛ и европейского H07RN-F. Практические тестирования, технические характеристики, цены и профессиональные рекомендации от EDS для выбора оптимального кабеля.',
	'KEYWORDS' => 'КГтп-ХЛ, H07RN-F, сравнение кабелей, кабели для проката, профессиональные кабели, силовые кабели, удлинители, гибкие кабели, EDS',
	'SLUG' => 'sravnenie-kgtp-hl-i-h07rn-f',
	'IMAGE' => '/upload/useful/cables-comparison-hero.jpg',
	'DATE_PUBLISHED' => '2019-07-15T12:00:00+03:00',
	'DATE_MODIFIED' => '2021-10-20T16:30:00+03:00'
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
                     width="870"
                     height="460"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p>Здравствуйте, дорогие друзья и коллеги! Сегодня компания <strong>Electric Distribution Systems (EDS)</strong> расскажет вам, чем же отличается хваленый <strong>H07RN-F</strong> от отечественного давно знакомого <strong>КГ</strong>.</p>

                    <p>Как добросовестный изготовитель кабельных удлинителей, мы проверили большое количество производителей кабельных проводников. Кабели, изготовленные не по ГОСТ, были отметены сразу.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Критерии сравнительных испытаний</h2>
                    <p>Критерии для сравнения были совершенно разнообразными:</p>

                    <div class="edsys-test-criteria">
                        <div class="edsys-criteria-grid">
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-ruler"></i>
                                <h4>Соответствие сечения</h4>
                                <p>Заявленному производителем</p>
                            </div>
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-arrow-bend-down-right"></i>
                                <h4>Гибкость</h4>
                                <p>Способность к изгибам</p>
                            </div>
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-shield"></i>
                                <h4>Толщина изоляции</h4>
                                <p>Защита от повреждений</p>
                            </div>
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-snowflake"></i>
                                <h4>Поведение на морозе</h4>
                                <p>При отрицательных температурах</p>
                            </div>
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-thermometer"></i>
                                <h4>Нагрев под нагрузкой</h4>
                                <p>Теплоотводящие свойства</p>
                            </div>
                            <div class="edsys-criteria-item">
                                <i class="ph ph-thin ph-sparkle"></i>
                                <h4>Качество изоляции</h4>
                                <p>Сбор пыли и грязи</p>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-highlight-box">
                        <p><strong>Забавный факт:</strong> одним из критериев был даже <em>источаемый запах</em>! Не очень хочется, чтобы на мероприятии с высокопоставленными чинами и вкусным кейтерингом пахло технической резиной.</p>
                    </div>

                    <p>В ходе долгих испытаний мы остановились на одном отечественном и одном импортном заводе. Марки в статье указаны не будут — если потребуется, напишем в личку.</p>
                </section>

                <section class="edsys-content-section">
                    <h2>Что такое КГ и H07RN-F?</h2>
                    <p>Это кабель с медными жилами, от одной до пяти, которые скручены из тоненьких медных проволочек, что обеспечивает <strong>высокий (5) класс гибкости</strong> и общим сечением от 1,5 до 240 кв.мм.</p>

                    <div class="edsys-cable-description">
                        <div class="edsys-cable-structure">
                            <h3>Конструкция кабеля</h3>
                            <ul>
                                <li><strong>Жилы:</strong> медные, скрученные из тонких проволочек</li>
                                <li><strong>Изоляция жил:</strong> резиновая</li>
                                <li><strong>Оболочка:</strong> резина или ТЭП (термоэластопласт)</li>
                                <li><strong>Класс гибкости:</strong> 5 (высокий)</li>
                            </ul>
                        </div>

                        <div class="edsys-temperature-advantage">
                            <h3>Преимущество перед ПВС</h3>
                            <p>В отличие от ПВС, резиновая/ТЭП оболочка позволяет кабелю гораздо лучше чувствовать себя при <strong>отрицательных температурах</strong>.</p>
                            <p class="edsys-example">Все помнят, что стоит смотать IEC-320 (компьютерный кабель) на новогодней елке — у него ПВХ изоляция, как и у ПВС.</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Важность качества кабеля</h2>
                    <div class="edsys-danger-warning">
                        <h3>⚠️ Не покупайтесь на низкие цены!</h3>
                        <p>Не стоит радоваться, если вы нашли «супердешевую КГшку производства ТоржокКабель». Радость может быть недолгой, как и рабочее состояние кабеля.</p>

                        <div class="edsys-safety-importance">
                            <h4>Качественный кабель = безопасность</h4>
                            <p>Это прежде всего безопасность как людей, так и вашего оборудования.</p>

                            <div class="edsys-consequences">
                                <h5>Последствия использования некачественного кабеля:</h5>
                                <ul>
                                    <li><strong>Переломанные жилы</strong> → потеря сечения</li>
                                    <li><strong>Потеря сечения</strong> → потеря напряжения на расстояниях</li>
                                    <li><strong>Неправильная нагрузка</strong> → нагрев кабеля</li>
                                    <li><strong>Критический нагрев</strong> → возгорание</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-buying-guide">
                        <h3>На что обратить внимание при выборе</h3>
                        <div class="edsys-guide-grid">
                            <div class="edsys-guide-item">
                                <i class="ph ph-thin ph-factory"></i>
                                <h4>Производитель</h4>
                                <p>Покупайте только известные бренды</p>
                            </div>
                            <div class="edsys-guide-item">
                                <i class="ph ph-thin ph-certificate"></i>
                                <h4>Условия изготовления</h4>
                                <p>ТУ или ГОСТ</p>
                            </div>
                            <div class="edsys-guide-item">
                                <i class="ph ph-thin ph-seal-check"></i>
                                <h4>Сертификация</h4>
                                <p>Обязательно должна быть</p>
                            </div>
                            <div class="edsys-guide-item">
                                <i class="ph ph-thin ph-handshake"></i>
                                <h4>Проверенный поставщик</h4>
                                <p>Официальные представители</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Технические характеристики</h2>
                    <p>Для обширного описания сравним кабель сечением <strong>5×6</strong>. Мы используем для производства удлинителей <strong>КГтп-ХЛ</strong> (хладостойкий, позволяющий сохранять гибкость при температуре до -60 градусов).</p>

                    <div class="edsys-specs-table">
                        <img src="/upload/useful/cables-table-comparison.jpg"
                             alt="Сравнительная таблица характеристик КГтп-ХЛ и H07RN-F"
                             class="edsys-technical-image"
                             width="687"
                             height="296"
                             loading="lazy">
                        <p class="edsys-image-caption">Сравнительная таблица технических характеристик</p>
                    </div>

                    <p>Розничную цену на метр кабеля мы раскроем чуть позже.<br>Теперь давайте проведем практические испытания.</p>
                </section>

                <section class="edsys-content-section">
                    <h2>Практические испытания: 7 тестов</h2>

                    <div class="edsys-test-intro">
                        <p>Проведем серию эмпирических испытаний и посмотрим, какой кабель лучше справляется с практическими задачами.</p>
                    </div>

                    <!-- Тест 1: Запах -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">1</span>
                            <h3>Запах кабеля</h3>
                        </div>

                        <div class="edsys-test-content">
                            <p>Используемый нами <strong>КГтп-ХЛ</strong> практически не имеет запаха, в то время как <strong>H07RN-F</strong> пахнет альпийскими горными лугами! Шутка)) Он тоже ничем не пахнет.</p>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 1</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score">H07RN-F: 1</span>
                                </div>
                                <p class="edsys-score-text">Пока счет равный — 1:1</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 2: Пыль и грязь -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">2</span>
                            <h3>Умение цеплять на себя пыль и грязь</h3>
                        </div>

                        <div class="edsys-test-content">
                            <div class="edsys-test-image-block">
                                <img src="/upload/useful/cable-dust-test.jpg"
                                     alt="Тест на сбор пыли и грязи кабелями"
                                     class="edsys-technical-image"
                                     width="300"
                                     height="225"
                                     loading="lazy">
                                <p class="edsys-image-caption">Кабели после перемотки по пыльному полу</p>
                            </div>

                            <p>Мы, конечно, протираем и тот и другой перед отправкой клиенту, и в грязь его намеренно не бросаем, но давайте посмотрим, как он выглядит после перемотки по пыльному полу на развеске.</p>

                            <p>Из фото отчетливо видно, что <strong>H07RN-F цепляет на себя гораздо меньше</strong> пыли и грязи.</p>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 1</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 2</span>
                                </div>
                                <p class="edsys-score-text">Импортный кабель вырывается вперед — 1:2</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 3: Толщина жилы -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">3</span>
                            <h3>Толщина токопроводящей жилы</h3>
                        </div>

                        <div class="edsys-test-content">
                            <p>Реальные измерения показали:</p>

                            <div class="edsys-measurement-comparison">
                                <div class="edsys-measurement-item">
                                    <h4>КГтп-ХЛ 5×6</h4>
                                    <div class="edsys-measurement-value">2,7 мм</div>
                                    <div class="edsys-area-value">5,6 мм²</div>
                                </div>
                                <div class="edsys-vs-divider">VS</div>
                                <div class="edsys-measurement-item">
                                    <h4>H07RN-F 5×6</h4>
                                    <div class="edsys-measurement-value">2,95 мм</div>
                                    <div class="edsys-area-value">6,8 мм²</div>
                                </div>
                            </div>

                            <div class="edsys-technical-note">
                                <h4>Почему у КГ занижено сечение?</h4>
                                <p>ГОСТ на производство кабельной продукции подразумевает отклонение в <strong>± 10%</strong>, но кто будет делать в «плюс», повышая себестоимость и становясь неконкурентоспособными?</p>
                            </div>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 1</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 3</span>
                                </div>
                                <p class="edsys-score-text">Иностранец получает еще одно очко — 1:3</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 4: Внешняя изоляция -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">4</span>
                            <h3>Внешняя изоляция</h3>
                        </div>

                        <div class="edsys-test-content">
                            <div class="edsys-test-image-block">
                                <img src="/upload/useful/cable-isolation-test.jpg"
                                     alt="Сравнение толщины внешней изоляции кабелей"
                                     class="edsys-technical-image"
                                     width="300"
                                     height="260"
                                     loading="lazy">
                                <p class="edsys-image-caption">Сравнение толщины внешней изоляции</p>
                            </div>

                            <p>У обоих кабелей изоляция выполнена из <strong>термоэластопласта</strong>. Плюс импортного в том, что он не распространяет горение, без галогенов и с низким дымовыделением — его можно применять для стационарной прокладки в общественных местах.</p>

                            <p>Из фото видно, что толщина изоляции у <strong>КГтп-ХЛ гораздо меньше</strong>.</p>

                            <div class="edsys-pros-cons">
                                <div class="edsys-pros">
                                    <h4>Плюсы тонкой изоляции:</h4>
                                    <ul>
                                        <li>Меньше масса</li>
                                        <li>Занимает меньше места</li>
                                    </ul>
                                </div>
                                <div class="edsys-cons">
                                    <h4>Минусы тонкой изоляции:</h4>
                                    <ul>
                                        <li>Меньше защищен от истирания</li>
                                        <li>Меньше защищен от перегибов и переломов</li>
                                        <li>Больше риск образования «грыж» и превращения в «спиральку»</li>
                                    </ul>
                                </div>
                            </div>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 1</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 4</span>
                                </div>
                                <p class="edsys-score-text">Очко опять в пользу H07RN-F — 1:4</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 5: Армировка и маркировка -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">5</span>
                            <h3>Армировка и маркировка жил</h3>
                        </div>

                        <div class="edsys-test-content">
                            <div class="edsys-test-image-block">
                                <img src="/upload/useful/cable-cores-test.jpg"
                                     alt="Сравнение маркировки и армировки жил кабелей"
                                     class="edsys-technical-image"
                                     width="300"
                                     height="225"
                                     loading="lazy">
                                <p class="edsys-image-caption">Маркировка и армировка жил</p>
                            </div>

                            <div class="edsys-cable-cores-comparison">
                                <div class="edsys-cable-cores-item">
                                    <h4>H07RN-F</h4>
                                    <ul>
                                        <li><strong>5 жил разных цветов</strong> — упрощает монтаж</li>
                                        <li><strong>Армировка жгутом</strong> из множества тонких нитей</li>
                                    </ul>
                                </div>
                                <div class="edsys-cable-cores-item">
                                    <h4>КГтп-ХЛ</h4>
                                    <ul>
                                        <li><strong>2 черные + 2 коричневые + 1 синяя</strong> — нужно прозванивать тестером</li>
                                        <li><strong>ПВХ стержень</strong> — понижает гибкость</li>
                                    </ul>
                                </div>
                            </div>

                            <p>Если нужно изготовить 1-2 удлинителя, то прозвонить концы не сложно. Но если предстоит изготовить не один десяток удлинителей, разноцветная маркировка <strong>H07RN-F</strong> доставляет определенные удобства.</p>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 2</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 5</span>
                                </div>
                                <p class="edsys-score-text">КГтп-ХЛ получает очко, но H07RN-F все равно впереди — 2:5</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 6: Гибкость -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">6</span>
                            <h3>Гибкость кабеля и поведение на морозе</h3>
                        </div>

                        <div class="edsys-test-content">
                            <div class="edsys-test-image-block">
                                <img src="/upload/useful/cable-flexibility-test.jpg"
                                     alt="Тест гибкости кабелей в тисках"
                                     class="edsys-technical-image"
                                     width="300"
                                     height="224"
                                     loading="lazy">
                                <p class="edsys-image-caption">Тест гибкости: кабели закреплены в тисках</p>
                            </div>

                            <p>Мы отрезали равные куски кабеля и закрепили их в тиски. Результат говорит сам за себя!</p>

                            <p>Тут все очевидно! Но КГшка и не претендовала на победителя (если обратиться к минимальному радиусу изгиба в табличных данных).</p>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score">КГтп-ХЛ: 2</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 6</span>
                                </div>
                                <p class="edsys-score-text">Очко в пользу приезжего кабеля — Россия 2, Европа 6!</p>
                            </div>
                        </div>
                    </div>

                    <!-- Тест 7: Цена -->
                    <div class="edsys-test-block">
                        <div class="edsys-test-header">
                            <span class="edsys-test-number">7</span>
                            <h3>Розничная цена</h3>
                        </div>

                        <div class="edsys-test-content">
                            <p>И все бы было так здорово, и можно было бы переходить на иностранный кабель, если бы не один подводный камень… <strong>Его цена!</strong></p>

                            <div class="edsys-price-comparison">
                                <div class="edsys-price-item">
                                    <h4>КГтп-ХЛ</h4>
                                    <div class="edsys-price-value">≈ 258 руб.</div>
                                    <p class="edsys-price-note">за метр кабеля 5×6</p>
                                </div>
                                <div class="edsys-vs-divider">VS</div>
                                <div class="edsys-price-item">
                                    <h4>H07RN-F</h4>
                                    <div class="edsys-price-value">≈ 500 руб.</div>
                                    <p class="edsys-price-note">за метр кабеля 5×6</p>
                                </div>
                            </div>

                            <p>И без слов понятно, кто тут чемпион! За такую разницу, думаю, можно присудить <strong>3 очка</strong> в пользу отечественного кабеля.</p>

                            <div class="edsys-test-result">
                                <div class="edsys-score-card">
                                    <span class="edsys-score edsys-score--winning">КГтп-ХЛ: 5</span>
                                    <span class="edsys-vs">VS</span>
                                    <span class="edsys-score edsys-score--winning">H07RN-F: 6</span>
                                </div>
                                <p class="edsys-score-text"><strong>Итоговый счет:</strong> КГтп-ХЛ — 5 очков, H07RN-F — 6 очков</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-highlight-section">
                    <h2 class="edsys-before-title">🏆 Выводы и рекомендации EDS</h2>

                    <div class="edsys-conclusion-content">
                        <p class="edsys-before-section">На наш взгляд, и один и другой кабель имеют право на существование в сфере проката и инсталляций профессионального оборудования.</p>

                        <div class="edsys-recommendation-grid">
                            <div class="edsys-recommendation-card">
                                <div class="edsys-recommendation-icon">
                                    <i class="ph ph-thin ph-globe"></i>
                                </div>
                                <h3>Выбирайте H07RN-F если:</h3>
                                <ul>
                                    <li>Времена хорошие и с бюджетом все «ОК»</li>
                                    <li>Нужна максимальная гибкость</li>
                                    <li>Важна простота монтажа (цветная маркировка)</li>
                                    <li>Требуется устойчивость к загрязнениям</li>
                                </ul>
                                <p class="edsys-recommendation-note">С ним работать гораздо приятнее!</p>
                            </div>

                            <div class="edsys-recommendation-card">
                                <div class="edsys-recommendation-icon">
                                    <i class="ph ph-thin ph-snowflake"></i>
                                </div>
                                <h3>Выбирайте КГтп-ХЛ если:</h3>
                                <ul>
                                    <li>Нужно оптимизировать бюджет</li>
                                    <li>Работаете в суровых климатических условиях</li>
                                    <li>Важна морозостойкость до -60°C</li>
                                    <li>Поддерживаете отечественного производителя</li>
                                </ul>
                                <p class="edsys-recommendation-note">Нужно правильно выбрать и делать расчет нагрузки с запасом</p>
                            </div>
                        </div>

                        <div class="edsys-eds-policy">
                            <h3>Политика EDS</h3>
                            <p>Мы решили, что при изготовлении <strong>дистрибьюторов питания</strong> будем использовать только <strong>европейский кабель</strong>, а <strong>удлинители</strong> с удовольствием изготовим для вас из любого выбранного вами!</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Дополнительные рекомендации</h2>

                    <div class="edsys-additional-tips">
                        <div class="edsys-tip-card">
                            <h3>При самостоятельном изготовлении удлинителей</h3>
                            <p>Если вы решили сделать удлинитель самостоятельно, а не покупать готовый у EDS (собранный на качественных компонентах, с обжатыми наконечниками, правильным моментом затяжки винтов и сертификатом соответствия EAC), то подумайте еще раз!</p>
                        </div>

                        <div class="edsys-related-info">
                            <h3>Готовые удлинители EDS</h3>
                            <p>С ценами на готовые удлинители от компании EDS вы можете ознакомиться в нашем каталоге.</p>

                            <div class="edsys-catalog-link">
                                <a href="https://btx.edsy.ru/cat/kabelnaya-produktsiya/" class="edsys-related-article-link">
                                    <div class="edsys-related-article-icon">
                                        <i class="ph ph-thin ph-plugs"></i>
                                    </div>
                                    <div class="edsys-related-article-content">
                                        <h4>Кабельная продукция EDS</h4>
                                        <p>Профессиональные удлинители и кабели для проката</p>
                                    </div>
                                    <i class="ph ph-thin ph-arrow-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </section>
            </article>


			<?= edsysRenderArticleNavigation($arArticleData['SLUG']) ?>
        </div>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>