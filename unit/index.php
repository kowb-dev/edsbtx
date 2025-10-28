<?php
/**
 * Статья "Неизвестное об известном. Unit"
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
	'TITLE' => 'Неизвестное об известном. Unit',
	'DESCRIPTION' => 'Что такое Unit (юнит) в IT-индустрии? История возникновения единицы измерения высоты рэкового оборудования. Интересные факты о стандартизации размеров аппаратных стоек.',
	'KEYWORDS' => 'unit, юнит, рэковое оборудование, стойки, 19 дюймов, IT-индустрия, стандартизация, размеры оборудования, аппаратные стойки, вершок',
	'SLUG' => 'unit',
	'IMAGE' => '/upload/useful/rack-unit-730x410.jpg',
	'DATE_PUBLISHED' => '2021-05-08T07:00:00+03:00',
	'DATE_MODIFIED' => '2024-12-20T18:00:00+03:00'
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
	'CATEGORIES_TYPE' => 'unit_measurement',
	'CATEGORIES_DATA' => [
		[
			'name' => 'Рэковые дистрибьюторы',
			'description' => 'Стандартные 19-дюймовые решения с размерами в единицах Unit',
			'url' => 'https://btx.edsy.ru/cat/rjekovye/',
			'icon' => 'ph-cube',
			'color' => 'voltage',
			'features' => ['Стандарт 19 дюймов', 'Размеры в Unit', 'Рэковое оборудование']
		],
		[
			'name' => 'Секвенсоры',
			'description' => 'Рэковые секвенсоры стандартных размеров для профессиональных инсталляций',
			'url' => 'https://btx.edsy.ru/cat/sekvensory/',
			'icon' => 'ph-list-numbers',
			'color' => 'wire',
			'features' => ['Рэковые размеры', 'Стандартные Unit', 'Профессиональные инсталляции']
		],
		[
			'name' => 'Сопутствующие товары',
			'description' => 'Аксессуары и крепежные элементы для рэкового оборудования',
			'url' => 'https://btx.edsy.ru/cat/soputstvuyushchie-tovary/',
			'icon' => 'ph-wrench',
			'color' => 'spark',
			'features' => ['Рэковые аксессуары', 'Крепежные элементы', 'Монтажные решения']
		]
	],
	'TITLE' => 'Рэковые решения EDS',
	'SUBTITLE' => 'Стандартное оборудование для 19-дюймовых стоек'
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
                     width="730"
                     height="410"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p>Все привыкли измерять высоту оборудования, устанавливаемого в стойку в юнитах. А что такое <strong>Юнит (Unit)</strong>? Это неофициальная единица измерения, используемая в IT-индустрии для стандартизации размеров оборудования.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Определение и размеры</h2>
                    <div class="edsys-formula-block">
                        <h4>Стандартный размер Unit</h4>
                        <p style="font-size: 1.5rem; font-weight: bold; margin: 1rem 0;">1 Unit = 1,75 дюйма = 4,45 см</p>
                        <p class="edsys-formula-explanation">Обозначается латинской буквой U и вычисляется в отношении высоты приборного блока</p>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Что измеряется в Unit</h4>
                        <p>Unit используется для измерения высоты оборудования, устанавливаемого в стандартные 19-дюймовые стойки:</p>
                        <ul>
                            <li><strong>1U</strong> – тонкие устройства (патч-панели, коммутаторы)</li>
                            <li><strong>2U</strong> – серверы начального уровня</li>
                            <li><strong>3U</strong> – мощные серверы и сетевое оборудование</li>
                            <li><strong>4U</strong> – высокопроизводительные системы</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>История стандартизации</h2>
                    <p>Стандартизация прошла в конце XX века, когда IT-технологии перестали быть уделом узкого круга профессионалов и стали достоянием миллионов. Оказалось, что единственно возможным способом расположить на единице площади максимально большое количество блоков аппаратуры является их размещение «в стопочку» и формирование аппаратных стоек.</p>

                    <div class="edsys-highlight-section">
                        <h3>Преимущества стандартизации</h3>
                        <p>Унификация размера позволила использовать оборудование в любой точке мира и любого производителя – бесспорное преимущество!</p>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Причины появления стандарта</h4>
                        <p>Кто изобрел единицу измерения высоты аппаратного блока, и почему он избрал именно такой размер – достоверно неизвестно. Но стандартизация решила несколько важных задач:</p>
                        <ul>
                            <li><strong>Совместимость</strong> – оборудование разных производителей помещается в одну стойку</li>
                            <li><strong>Оптимизация пространства</strong> – максимальное использование объема стойки</li>
                            <li><strong>Унификация крепежа</strong> – стандартные монтажные рельсы и винты</li>
                            <li><strong>Планирование инфраструктуры</strong> – предсказуемые размеры для проектирования</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Связь с историей</h2>
                    <p>Но мало кто знает, что эту величину использовали наши далекие предки еще в XVI веке и назвалась она <strong>вершок</strong>.</p>

                    <div class="edsys-formula-block">
                        <h4>Старинная русская мера</h4>
                        <p style="font-size: 1.2rem; font-weight: bold; margin: 1rem 0;">1 вершок = 1/48 сажени = 7⁄48 фута = 1⁄16 аршина = 1,75 дюйма</p>
                        <p class="edsys-formula-explanation">Что, как мы выяснили ранее = 4,45 см или 1 Unit!</p>
                    </div>

                    <div class="edsys-highlight-section">
                        <h3>Интересный факт</h3>
                        <p>Так что в следующий раз можно просить «у ремесленников машину заморскую в 3 вершка»! 😄</p>
                    </div>

                    <div class="edsys-technical-explanation">
                        <h4>Старинные русские меры длины</h4>
                        <p>Для понимания масштаба, вот как соотносились старинные русские меры:</p>
                        <ul>
                            <li><strong>1 сажень</strong> = 7 футов = 48 вершков ≈ 2,13 метра</li>
                            <li><strong>1 аршин</strong> = 16 вершков ≈ 71,12 см</li>
                            <li><strong>1 фут</strong> = 12 дюймов = 48/7 вершка ≈ 30,48 см</li>
                            <li><strong>1 вершок</strong> = 1,75 дюйма ≈ 4,45 см</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Практическое применение Unit</h2>
                    <p>Понимание размеров в Unit критически важно для планирования и монтажа рэкового оборудования:</p>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">1</div>
                        <div class="edsys-step-content">
                            <h4>Планирование стойки</h4>
                            <p>Стандартная стойка имеет высоту 42U (около 2 метров). Необходимо заранее рассчитать, сколько оборудования поместится</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">2</div>
                        <div class="edsys-step-content">
                            <h4>Вентиляция и охлаждение</h4>
                            <p>Между устройствами оставляют свободные Unit для циркуляции воздуха и охлаждения</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">3</div>
                        <div class="edsys-step-content">
                            <h4>Кабель-менеджмент</h4>
                            <p>Панели кабель-менеджмента также имеют стандартные размеры 1U или 2U</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">4</div>
                        <div class="edsys-step-content">
                            <h4>Распределение питания</h4>
                            <p>PDU (Power Distribution Unit) и дистрибьюторы питания изготавливаются в стандартных размерах</p>
                        </div>
                    </div>

                    <div class="edsys-expert-advice">
                        <h4>💡 Совет для практического использования</h4>
                        <p>При планировании стойки всегда резервируйте 10-15% свободного места для будущего расширения и обеспечения вентиляции.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Современные стандарты</h2>
                    <p>Сегодня Unit является общепринятым стандартом не только в IT, но и в других отраслях:</p>

                    <div class="edsys-technical-explanation">
                        <h4>Области применения стандарта Unit</h4>
                        <ul>
                            <li><strong>Серверные</strong> – размещение серверов и сетевого оборудования</li>
                            <li><strong>Телекоммуникации</strong> – коммутационное оборудование</li>
                            <li><strong>Аудио/видео</strong> – профессиональное студийное оборудование</li>
                            <li><strong>Промышленность</strong> – системы управления и контроля</li>
                            <li><strong>Медицина</strong> – диагностическое оборудование</li>
                            <li><strong>Энергетика</strong> – системы распределения питания</li>
                        </ul>
                    </div>

                    <div class="edsys-highlight-section">
                        <h3>Международный стандарт</h3>
                        <p>Размер Unit регламентируется международными стандартами:</p>
                        <ul>
                            <li><strong>IEC 60297</strong> – Европейский стандарт</li>
                            <li><strong>EIA-310</strong> – Американский стандарт</li>
                            <li><strong>DIN 41494</strong> – Немецкий стандарт</li>
                        </ul>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Практические советы</h2>
                    <p>При работе с рэковым оборудованием важно учитывать несколько нюансов:</p>

                    <div class="edsys-technical-explanation">
                        <h4>Важные моменты при монтаже</h4>
                        <ul>
                            <li><strong>Нумерация</strong> – Unit считаются снизу вверх, начиная с 1U</li>
                            <li><strong>Глубина</strong> – стандартная глубина стойки 600мм или 800мм</li>
                            <li><strong>Нагрузка</strong> – учитывайте весовые ограничения стойки</li>
                            <li><strong>Доступ</strong> – предусматривайте место для обслуживания</li>
                            <li><strong>Кабели</strong> – планируйте кабельные трассы заранее</li>
                        </ul>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">✓</div>
                        <div class="edsys-step-content">
                            <h4>Правило хорошего тона</h4>
                            <p>Самое тяжелое оборудование размещают в нижней части стойки для стабильности</p>
                        </div>
                    </div>

                    <div class="edsys-step-item">
                        <div class="edsys-step-number">✓</div>
                        <div class="edsys-step-content">
                            <h4>Охлаждение</h4>
                            <p>Горячий воздух поднимается вверх, поэтому самое теплонагруженное оборудование лучше размещать снизу</p>
                        </div>
                    </div>

                    <div class="edsys-expert-advice">
                        <h4>🔧 Рекомендация от EDS</h4>
                        <p>Для профессиональных инсталляций используйте качественные рэковые дистрибьюторы питания стандартных размеров. Это обеспечит надежность и удобство обслуживания.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Заключение</h2>
                    <p>Unit – это не просто единица измерения, а основа современной IT-инфраструктуры. В чем польза от понимания принципов стандартизации?</p>

                    <div class="edsys-highlight-section">
                        <h3>Преимущества знания стандарта</h3>
                        <ul>
                            <li><strong>Правильное планирование</strong> – точный расчет места в стойке</li>
                            <li><strong>Совместимость</strong> – уверенность в сочетании оборудования</li>
                            <li><strong>Экономия</strong> – оптимальное использование пространства</li>
                            <li><strong>Профессионализм</strong> – понимание отраслевых стандартов</li>
                        </ul>
                    </div>

                    <p>Интересно, что современные IT-специалисты используют ту же единицу измерения, что и русские мастера XVI века. История имеет обыкновение повторяться, даже в технических стандартах!</p>
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