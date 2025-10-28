<?php
/**
 * Калькулятор расчета сечения провода по заданной потере напряжения
 * Версия: 1.0
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Профессиональный калькулятор для подбора сечения провода с учетом допустимых потерь напряжения
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Калькулятор расчета сечения провода по заданной потере напряжения");
$APPLICATION->SetPageProperty("description", "Профессиональный калькулятор для расчета сечения провода с учетом допустимых потерь напряжения. Подбор сечения для медных и алюминиевых проводов в однофазных и трехфазных сетях.");
$APPLICATION->SetPageProperty("keywords", "калькулятор сечения провода, потери напряжения, расчет сечения, медный провод, алюминиевый провод, однофазная сеть, трехфазная сеть");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор расчета сечения провода по заданной потере напряжения",
	"description" => "Профессиональный калькулятор для расчета сечения провода с учетом допустимых потерь напряжения",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"Расчет для однофазных и трехфазных сетей",
		"Подбор сечения для медных и алюминиевых проводов",
		"Учет допустимых потерь напряжения",
		"Автоматический расчет",
		"Мобильная версия"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Расчет сечения провода по заданной потере напряжения");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'wire-section-voltage-drop'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('wire-section-voltage-drop', 6);
} else {
	// Fallback на случай отсутствия файла конфигурации
	$productCategories = [
		[
			'title' => 'Кабельная продукция',
			'description' => 'Силовые и сигнальные кабели для сценического оборудования',
			'icon' => 'ph-cable',
			'url' => '/cat/kabelnaya-produktsiya/'
		],
		[
			'title' => 'Коммутационные коробки',
			'description' => 'Коробки для коммутации сигналов и питания',
			'icon' => 'ph-squares-four',
			'url' => '/cat/kommutatsionnye-korobki/'
		],
		[
			'title' => 'Дистрибьюторы с защитными реле',
			'description' => 'Оборудование с встроенными защитными устройствами',
			'icon' => 'ph-shield-check',
			'url' => '/cat/ustrojstva-s-zashhitnymi-rele/'
		]
	];
}
?>

    <main class="edsys-calculator-page">

		<?php
		// Подключаем навигацию калькуляторов
		$arParams = $arNavParams;
		include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/assets/components/sidebar/calculators-navigation.php");
		?>

        <div class="edsys-calculator-content">
            <!-- Хлебные крошки -->
            <nav class="edsys-calculator-breadcrumbs" aria-label="Хлебные крошки">
                <ol class="edsys-calculator-breadcrumbs__list">
                    <li class="edsys-calculator-breadcrumbs__item">
                        <a href="/" class="edsys-calculator-breadcrumbs__link">Главная</a>
                        <i class="ph ph-thin ph-caret-right"></i>
                    </li>
                    <li class="edsys-calculator-breadcrumbs__item">
                        <a href="/kalkulyatory/" class="edsys-calculator-breadcrumbs__link">Калькуляторы</a>
                        <i class="ph ph-thin ph-caret-right"></i>
                    </li>
                    <li class="edsys-calculator-breadcrumbs__item">
                        <span class="edsys-calculator-breadcrumbs__current">Расчет сечения провода по заданной потере напряжения</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Калькулятор расчета сечения провода по заданной потере напряжения</h1>
                <p class="edsys-calculator-subtitle">Профессиональный подбор сечения с учетом допустимых потерь</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор расчета сечения провода по заданной потере напряжения</strong> поможет определить оптимальное сечение проводника для обеспечения качественного электроснабжения. Учитывает тип сети, материал провода и допустимые потери напряжения согласно нормативным требованиям.</p>
            </div>

            <!-- Форма калькулятора -->
            <section class="edsys-calculator-form" id="wireSectionCalculator">
                <div class="edsys-calculator-form__header">
                    <h2 class="edsys-calculator-form__title">Расчет сечения провода</h2>
                    <p class="edsys-calculator-form__description">Введите параметры для автоматического расчета необходимого сечения провода</p>
                </div>

                <form class="edsys-calculator-form__fields edsys-calculator-form__fields--two-rows" id="calculatorForm" name="form1">
                    <div class="edsys-calculator-form__field">
                        <label class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-waveform"></i>
                            Тип потребителя
                        </label>
                        <div class="edsys-calculator-toggle">
                            <input type="radio" id="singlePhase" name="num" value="2" checked>
                            <label for="singlePhase" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Однофазный</span>
                            </label>
                            <input type="radio" id="threePhase" name="num" value="0.34">
                            <label for="threePhase" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Трехфазный</span>
                            </label>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-diamond"></i>
                            Материал провода
                        </label>
                        <div class="edsys-calculator-toggle">
                            <input type="radio" id="copper" name="num1" value="53" checked>
                            <label for="copper" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Медь</span>
                            </label>
                            <input type="radio" id="aluminum" name="num1" value="32">
                            <label for="aluminum" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Алюминий</span>
                            </label>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="power" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-lightning"></i>
                            Мощность нагрузки
                        </label>
                        <input type="number"
                               id="power"
                               name="num3"
                               class="edsys-calculator-form__input"
                               placeholder="Введите мощность"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">Вт</span>
                    </div>

                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="length" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-ruler"></i>
                            Длина линии
                        </label>
                        <input type="number"
                               id="length"
                               name="num4"
                               class="edsys-calculator-form__input"
                               placeholder="Введите длину"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">м</span>
                    </div>

                    <div class="edsys-calculator-form__field" style="grid-column: 1 / -1;">
                        <label for="voltageDrop" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-chart-line-down"></i>
                            Допустимые потери напряжения
                        </label>
                        <select id="voltageDrop" name="num2" class="edsys-calculator-form__input">
                            <option value="1">1 %</option>
                            <option value="2">2 %</option>
                            <option value="3">3 %</option>
                            <option value="4">4 %</option>
                            <option value="5">5 %</option>
                            <option value="6">6 %</option>
                            <option value="7">7 %</option>
                            <option value="8">8 %</option>
                            <option value="9">9 %</option>
                            <option value="10">10 %</option>
                        </select>
                    </div>

                    <input type="hidden" name="res" id="hiddenResult" value="0">
                </form>

                <div class="edsys-calculator-form__result" id="calculatorResult">
                    <div class="edsys-calculator-form__result-label">Необходимое сечение провода</div>
                    <div class="edsys-calculator-form__result-value" id="sectionValue">0</div>
                    <div class="edsys-calculator-form__result-unit">мм²</div>
                </div>

                <div class="edsys-calculator-form__actions">
                    <button type="button"
                            class="edsys-calculator-form__button edsys-calculator-form__button--secondary"
                            id="resetButton">
                        <i class="ph ph-thin ph-arrows-counter-clockwise"></i>
                        Сбросить
                    </button>
                </div>
            </section>

            <!-- Техническая информация -->
            <section class="edsys-calculator-technical">
                <h2 class="edsys-calculator-technical__title">Техническая информация</h2>

                <div class="edsys-calculator-technical__content">
                    <div class="edsys-calculator-technical__section">
                        <h3>Формула расчета</h3>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">S = (P × L × K) / (U² × σ × ΔU%)</div>
                            <div class="edsys-calculator-formula__description">Расчет сечения провода по потерям напряжения</div>
                        </div>

                        <p>Где:</p>
                        <ul>
                            <li><strong>S</strong> – сечение провода в мм²</li>
                            <li><strong>P</strong> – мощность нагрузки в ваттах (Вт)</li>
                            <li><strong>L</strong> – длина линии в метрах (м)</li>
                            <li><strong>K</strong> – коэффициент для типа сети (2 для однофазной, 0.34 для трехфазной)</li>
                            <li><strong>U</strong> – напряжение сети в вольтах (220В для однофазной, 380В для трехфазной)</li>
                            <li><strong>σ</strong> – удельная проводимость материала (медь: 53, алюминий: 32)</li>
                            <li><strong>ΔU%</strong> – допустимые потери напряжения в процентах</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Нормативные требования</h3>
                        <p>Согласно ПУЭ (Правила устройства электроустановок), <strong>допустимые потери напряжения</strong> в различных сетях регламентируются следующими значениями:</p>

                        <ul>
                            <li><strong>Осветительные сети</strong> – не более 2.5%</li>
                            <li><strong>Силовые сети</strong> – не более 5%</li>
                            <li><strong>Сети до 1000 В</strong> – от источника питания до наиболее удаленного потребителя не более 5%</li>
                            <li><strong>Сценические сети</strong> – рекомендуется не более 3% для качественного освещения</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Материалы проводников</h3>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Медные провода (σ = 53):</h4>
                                <ul>
                                    <li>Высокая проводимость</li>
                                    <li>Коррозионная стойкость</li>
                                    <li>Долговечность</li>
                                    <li>Меньшее сечение при той же нагрузке</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Алюминиевые провода (σ = 32):</h4>
                                <ul>
                                    <li>Экономичность</li>
                                    <li>Меньший вес</li>
                                    <li>Большее сечение при той же нагрузке</li>
                                    <li>Требуют специальных соединений</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Практические рекомендации</h3>
                        <ul>
                            <li>Выбирайте ближайшее <strong>стандартное сечение</strong> в большую сторону</li>
                            <li>Учитывайте <strong>механическую прочность</strong> – минимальное сечение медного провода 1.5 мм²</li>
                            <li>Для <strong>длинных линий</strong> потери напряжения могут стать критическими</li>
                            <li>При <strong>реактивных нагрузках</strong> учитывайте коэффициент мощности</li>
                            <li>Для <strong>групповых линий</strong> суммируйте мощности всех потребителей</li>
                            <li>Рассматривайте возможность <strong>повышения напряжения</strong> для снижения потерь</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Стандартные сечения проводов</h3>
                        <p><strong>Ряд стандартных сечений</strong> (мм²): 0.5, 0.75, 1, 1.5, 2.5, 4, 6, 10, 16, 25, 35, 50, 70, 95, 120, 150, 185, 240, 300, 400, 500, 630, 800, 1000</p>

                        <p>При получении расчетного значения выбирайте ближайшее стандартное сечение в большую сторону.</p>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Сопутствующие товары</h2>
                    <p class="edsys-calculator-categories__subtitle">Кабельная продукция и оборудование для качественного электроснабжения</p>
                </div>

                <div class="edsys-calculator-categories__grid">
					<?php foreach ($productCategories as $category): ?>
                        <a href="<?= htmlspecialchars($category['url']) ?>" class="edsys-calculator-category-card">
                            <div class="edsys-calculator-category-card__icon">
                                <i class="ph ph-thin <?= htmlspecialchars($category['icon']) ?>"></i>
                            </div>
                            <h3 class="edsys-calculator-category-card__title"><?= htmlspecialchars($category['title']) ?></h3>
                            <p class="edsys-calculator-category-card__description"><?= htmlspecialchars($category['description']) ?></p>
                            <span class="edsys-calculator-category-card__link">
                            Перейти в каталог
                            <i class="ph ph-thin ph-arrow-right"></i>
                        </span>
                        </a>
					<?php endforeach; ?>
                </div>
            </section>
        </div>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>