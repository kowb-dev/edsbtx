<?php
/**
 * Статья "Пусковые токи и как с ними бороться"
 * Раздел "Полезно знать" - EDS
 *
 * @version 2.0 - Адаптирована под новую структуру
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'Пусковые токи и как с ними бороться',
	'DESCRIPTION' => 'Современные тенденции снижения массы и габаритов приборов привели к тому, что практически в каждом устройстве применяют импульсные источники питания. Разбираем проблемы пусковых токов и способы их решения.',
	'KEYWORDS' => 'пусковые токи, импульсные источники питания, электрооборудование, защита от пусковых токов, секвенсоры, Safe Start, EDS',
	'SLUG' => 'puskovye-toki-i-kak-s-nimi-borotsya',
	'IMAGE' => '/upload/useful/inrush-current.jpg',
	'DATE_PUBLISHED' => '2021-08-24T09:57:59+03:00',
	'DATE_MODIFIED' => '2021-10-11T14:38:59+03:00'
]);

// Generate SEO
edsysGenerateArticleSEO($arArticleData);
edsysGenerateArticleStructuredData($arArticleData);
edsysGenerateBreadcrumbsStructuredData($arArticleData);

// Load assets
edsysLoadArticleAssets([
	'version' => '1.0' // Production version
]);

// Get current URL for sharing
$currentUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Navigation parameters
$arNavParams = [
	'CURRENT_ARTICLE' => $arArticleData['SLUG']
];

// Product categories for this article
$arCategoriesParams = [
	'CATEGORIES_TYPE' => 'power_distribution',
	'TITLE' => 'Решения EDS для защиты от пусковых токов',
	'SUBTITLE' => 'Профессиональное оборудование с технологией Safe Start'
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
                     width="500"
                     height="300"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p>Современные тенденции снижения массы и габаритов приборов привели к тому, что практически в каждом устройстве применяют <strong>импульсные источники питания</strong>, ведь они превосходят трансформаторные не только по вышеперечисленным характеристикам, но также и в качестве получаемого постоянного напряжения, имеют широкие возможности регулировки выходного напряжения и тока, а также традиционно оснащены защитой от перегрузки по выходному току, но во всем есть и обратная сторона.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Проблема пусковых токов</h2>
                    <p>Давайте постараемся разобраться, где тут подводные камни.</p>

                    <p>Наверное, многие уже сталкивались с такой ситуацией: купили новый светодиодный экран хорошей площади, с маленьким шагом пикселей, посчитали максимальную нагрузку, вроде влезаете в действующие дистрибьюторы и коммутацию. И вот приехали на площадку, повесили, включаете основной автомат, с характеристикой «С» (чуть позже разберем данный параметр), а его сразу выбивает.</p>

                    <div class="edsys-highlight-box">
                        <p><strong>В чем же проблема?</strong> Вроде и автомат с запасом, и КЗ на линии нет… Ответ на данный вопрос очень прост: дело в том, что у каждого блока питания в момент включения есть так называемый <strong>пусковой ток (Inrush Current)</strong>, его величина может в десятки раз превышать ток, потребляемый при максимальной нагрузке.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Распространенные «решения» и их недостатки</h2>
                    <p>Что же делать в такой ситуации? Многие приходят к следующим вариантам решения данной проблемы:</p>

                    <ol class="edsys-solutions-list">
                        <li>Повысить номинал автоматических выключателей.</li>
                        <li>Использовать автоматические выключатели категории «D».</li>
                        <li>Включать не все приборы сразу, а постепенно.</li>
                    </ol>

                    <h3>Недостатки этих методов:</h3>

                    <div class="edsys-solutions-grid">
                        <div class="edsys-problem-card">
                            <h4><span class="edsys-problem-number">1</span> Повышение номинала автомата</h4>
                            <p>Выключатель на 16А ставим на 32А. Да, работать будет, но:</p>
                            <ul>
                                <li><strong>Риск перегрузки кабеля:</strong> 16А автомат спасет кабель 3х2,5, а вот 32А автомат перегреет его с легкостью</li>
                                <li><strong>Проблема остается:</strong> возможно подгорание контактов автомата в момент включения и выключения</li>
                            </ul>
                        </div>

                        <div class="edsys-problem-card">
                            <h4><span class="edsys-problem-number">2</span> Автоматы категории «D»</h4>
                            <div class="edsys-comparison-grid">
                                <div class="edsys-comparison-card">
                                    <strong>Категория «С»</strong> — самые распространенные. Срабатывание электромагнитного расцепления при превышении номинала в <strong>5 раз</strong>. Тепловой расцепитель срабатывает через <strong>1,5 сек</strong>.
                                </div>
                                <div class="edsys-comparison-card">
                                    <strong>Категория «D»</strong> — высокая перегрузочная способность. Срабатывание при превышении в <strong>10 раз</strong>. Тепловой расцепитель срабатывает через <strong>0,4 сек</strong>.
                                </div>
                            </div>
                            <p><strong>Проблемы:</strong> кабель с перегрузкой нагреется быстрее, чем биметаллические пластины внутри автомата. Плюс такие выключатели труднее приобрести и их стоимость достаточно высокая.</p>
                        </div>

                        <div class="edsys-problem-card">
                            <h4><span class="edsys-problem-number">3</span> Поочередное включение</h4>
                            <p>Как это осуществить? Первый способ: щелкать автоматами по одному, второй способ: включать по одному усилителю, модулю экрана, кабинету активного массива, световому прибору.</p>
                            <p><strong>Проблема:</strong> что, если свет отключат в момент концерта? Повторный запуск займет достаточно много времени.</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Техническое объяснение проблемы</h2>
                    <p>Попробуем разобраться как более профессионально бороться с данной проблемой. Для начала разберемся откуда берутся эти пусковые токи. Рассмотрим схему простейшего импульсного блока питания.</p>

                    <div class="edsys-scheme-block">
                        <img src="/upload/useful/shema.webp"
                             alt="Схема импульсного блока питания"
                             class="edsys-technical-image"
                             width="616"
                             height="200"
                             loading="lazy">
                        <p class="edsys-image-caption">Схема простейшего импульсного блока питания</p>
                    </div>

                    <div class="edsys-technical-note">
                        <p>До включения блока питания конденсатор C1 полностью разряжен и напряжение на нем равно нулю, в то время как в рабочем режиме оно достигает амплитудного значения напряжения сети, равного, при входном напряжении 220 В, около <strong>310 В</strong>.</p>

                        <p>Поскольку напряжение на конденсаторе измениться мгновенно не может, то в момент включения схемы обязательно должен произойти <strong>бросок тока</strong> из-за необходимости заряда конденсатора фильтра.</p>
                    </div>

                    <div class="edsys-danger-warning">
                        <h4>⚠️ Критические значения</h4>
                        <p>Максимальное значение пускового тока зависит не только от электрических характеристик элементов схемы, но и от момента включения ее в сеть. Наихудшим случаем считается подключение к сети в моменты, когда ее напряжение равно амплитудным значениям (пик синуса).</p>

                        <div class="edsys-critical-values">
                            <span class="edsys-critical-number">600x</span>
                            <span class="edsys-critical-text">превышение номинального тока</span>
                        </div>

                        <p>Соответственно если блок питания всего 6 ампер, то в неудачное время включения Вы можете получить ток в проводниках в <strong>3600А</strong>, хотя и время будет совсем небольшим (1мс), но такой импульсный ток способен хорошо нагреть контакты в вилках, розетках, автоматических выключателях, реле и т.д.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Защита от пусковых токов</h2>
                    <p>Для предотвращения появления большого пускового тока чаще всего устанавливают <strong>термистор с отрицательным температурным коэффициентом сопротивления</strong>. В момент включения, когда сопротивление термистора велико, пусковой ток мал. После запуска источника питания ток, протекающий через термистор, разогревает его, что приводит к снижению его сопротивления и, как следствие, к уменьшению влияния на работу схемы.</p>

                    <h3>Проблема параллельного подключения</h3>
                    <p>Еще одна проблема — это параллельное подключение большого количества блоков питания.</p>

                    <div class="edsys-formula-block">
                        <img src="/upload/useful/formula.webp"
                             alt="Формула расчета общего сопротивления"
                             class="edsys-formula-image"
                             width="300"
                             height="100"
                             loading="lazy">
                        <p class="edsys-formula-explanation">Из формулы видно, что, подключая большое количество блоков питания, мы понижаем защитное сопротивление цепи.</p>
                    </div>

                    <div class="edsys-technical-note">
                        <h4>Проблема выключения</h4>
                        <p>Ко всему прочему, есть еще одна проблема, мало кто о ней пишет и мало, кто о ней знает. Это <strong>момент выключения оборудования</strong>. Дело в том, что импульсники работают в широком диапазоне напряжений обычно от 90 до 270 вольт и в момент расцепления, заряд на конденсаторе еще накоплен и получается воздушное сопротивление, блок питания начинает потреблять еще больший ток, чтобы стабилизировать выходное напряжение и происходит дугообразование, которое способно «склеить» контакты расцепителя.</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Профессиональные решения</h2>
                    <p>Из всего вышесказанного очевидно, что проблему пусковых токов необходимо как-то решать. Давайте рассмотрим некоторые способы:</p>

                    <div class="edsys-solutions-grid">
                        <div class="edsys-solution-card">
                            <h4>Выключатели нагрузки</h4>
                            <p>Вместо автоматических выключателей. В них конструктивно отсутствует расцепители максимального тока и «перегрузки», но они могут коммутировать цепи под нагрузкой.</p>
                            <div class="edsys-solution-status">Частично решает проблему</div>
                        </div>

                        <div class="edsys-solution-card">
                            <h4>Реле с дугогасящей камерой</h4>
                            <p>Специальные контакты, понижающие риск образования дуги. Камера содержит дугогасительное устройство, которое рассеивает дугу от контактов.</p>
                            <div class="edsys-solution-note">Используется в наших секвенсорах и DMX свитчерах</div>
                            <p><strong>Ограничение:</strong> активной нагрузки — 8000ВА, индуктивной — всего 1450ВА.</p>
                        </div>

                        <div class="edsys-solution-card">
                            <h4>Контакторы и магнитные пускатели</h4>
                            <p>Имеют хорошо подпружиненные контакты и применяются как раз в схемах с большими пусковыми токами.</p>
                            <div class="edsys-solution-drawback">Минус: большие габариты и масса</div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-highlight-section">
                    <h2 class="edsys-before-title">🚀 Рекомендуемое решение EDS</h2>
                    <p class="edsys-before-section">Использование секвенсоров и свитчеров нашего производства с функцией <strong>«Safe Start»</strong>.</p>

                    <div class="edsys-safe-start-explanation">
                        <h3 class="edsys-before-subtitle">Принцип действия технологии Safe Start:</h3>

                        <div class="edsys-step-by-step">
                            <div class="edsys-step-item">
                                <div class="edsys-step-number">1</div>
                                <div class="edsys-step-content">
                                    <h4>Мягкий старт</h4>
                                    <p>Производится открытие полупроводникового ключа с последовательно подключенным резистором при переходе тока через ноль, тем самым уменьшая электромагнитные помехи и предотвращая высокие броски тока.</p>
                                </div>
                            </div>

                            <div class="edsys-step-item">
                                <div class="edsys-step-number">2</div>
                                <div class="edsys-step-content">
                                    <h4>Подключение силового реле</h4>
                                    <p>Включается силовое реле минимум через 3 периода при частоте сети 50Гц, и отключается полупроводниковый ключ. Поскольку изначально ток течет через полупроводниковый ключ и токоограничивающий резистор, при подключении силового реле отсутствует искрообразование на силовых контактах.</p>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-safe-start-benefits">
                            <h4>Преимущества технологии Safe Start:</h4>
                            <ul>
                                <li>Значительно повышает срок службы импульсных блоков питания</li>
                                <li>Продлевает срок службы всего оборудования</li>
                                <li>Позволяет нагрузить 16А автоматический выключатель полностью, согласно его номиналу</li>
                                <li>Исключает подгорание контактов</li>
                                <li>Безопасное выключение в обратной последовательности</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-conclusion">
                    <h2>Заключение</h2>
                    <p>Проблема пусковых токов выпрямительных устройств не нова. В свою очередь мы, компания EDS, потратили значительное количество времени, чтобы постараться устранить данную проблему для сферы проката и инсталляции профессионального оборудования, проанализировав и создав устройства, не имеющие аналогов на отечественном и зарубежном рынке.</p>

                    <p>Надеемся, что наши устройства будут полезны для Вас и будут надежно оберегать Ваше оборудование.</p>

                    <p class="edsys-conclusion-thanks"><strong><em>Спасибо за внимание!</em></strong></p>
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