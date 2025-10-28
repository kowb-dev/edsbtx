<?php
/**
 * Калькулятор расчета тока в цепи
 * Версия: 1.3
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Исправлена ошибка с заголовками - убраны пробелы перед <?php
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Расчет тока в цепи");
$APPLICATION->SetPageProperty("description", "Калькулятор для расчета силы тока в электрической цепи по закону Ома. Простой и точный расчет тока для однофазных и трехфазных сетей.");
$APPLICATION->SetPageProperty("keywords", "калькулятор тока, расчет силы тока, закон Ома, электрическая цепь, однофазная сеть, трехфазная сеть");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/raschet-toka-v-tsepi/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор расчета тока в цепи",
	"description" => "Профессиональный калькулятор для расчета силы тока в электрической цепи по закону Ома",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/raschet-toka-v-tsepi/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"Расчет для однофазных и трехфазных сетей",
		"Учет коэффициента мощности",
		"Автоматический пересчет",
		"Профессиональные рекомендации"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Расчет тока в цепи");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'circuit-current'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('circuit-current', 6);
} else {
	// Fallback на случай отсутствия файла конфигурации
	$productCategories = [
		[
			'title' => 'Туровые дистрибьюторы',
			'description' => 'Профессиональные дистрибьюторы питания для турового оборудования',
			'icon' => 'ph-plugs-connected',
			'url' => '/cat/turovye/'
		],
		[
			'title' => 'Рэковые дистрибьюторы',
			'description' => 'Дистрибьюторы для монтажа в рэковые стойки',
			'icon' => 'ph-stack',
			'url' => '/cat/rjekovye/'
		],
		[
			'title' => 'Устройства с защитными реле',
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
                        <span class="edsys-calculator-breadcrumbs__current">Расчет тока в цепи</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Расчет тока в цепи</h1>
                <p class="edsys-calculator-subtitle">Калькулятор для расчета силы тока по закону Ома</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор расчета тока в цепи</strong> позволяет быстро и точно определить силу тока в электрической цепи по закону Ома. Подходит для расчетов в однофазных и трехфазных сетях с учетом коэффициента мощности.</p>
            </div>

            <!-- Форма калькулятора -->
            <section class="edsys-calculator-form" id="currentCalculator">
                <div class="edsys-calculator-form__header">
                    <h2 class="edsys-calculator-form__title">Расчет силы тока</h2>
                    <p class="edsys-calculator-form__description">Введите параметры для автоматического расчета силы тока в цепи</p>
                </div>

                <form name="currentCalculatorForm" class="edsys-calculator-form__fields edsys-calculator-form__fields--two-rows" id="calculatorForm">
                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="power" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-lightning"></i>
                            Мощность нагрузки
                        </label>
                        <input type="number"
                               id="power"
                               name="num"
                               class="edsys-calculator-form__input edsys-calculator__input"
                               placeholder="Введите мощность"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">Вт</span>
                    </div>

                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="voltage" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-plug"></i>
                            Напряжение в сети
                        </label>
                        <input type="number"
                               id="voltage"
                               name="num1"
                               class="edsys-calculator-form__input edsys-calculator__input"
                               placeholder="Введите напряжение"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">В</span>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label for="networkType" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-waveform"></i>
                            Тип питающей сети
                        </label>
                        <select id="networkType" name="num3" class="edsys-calculator-form__input edsys-calculator__input edsys-calculator__select">
                            <option value="1">Однофазная сеть</option>
                            <option value="1.73">Трехфазная сеть</option>
                        </select>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label for="powerFactor" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-function"></i>
                            Коэффициент мощности (cos φ)
                        </label>
                        <select id="powerFactor" name="num4" class="edsys-calculator-form__input edsys-calculator__input edsys-calculator__select">
                            <option value="1">Активная нагрузка (cos φ = 1)</option>
                            <option value="0.95">Смешанная нагрузка (cos φ = 0.95)</option>
                            <option value="0.85">Реактивная нагрузка (cos φ = 0.85)</option>
                            <option value="0.8">Индуктивная нагрузка (cos φ = 0.8)</option>
                        </select>
                    </div>

                    <input type="hidden" id="hiddenResult" name="res" value="0">
                </form>

                <div class="edsys-calculator-form__result" id="calculatorResult">
                    <div class="edsys-calculator-form__result-label">Расчетный ток</div>
                    <div class="edsys-calculator-form__result-value" id="currentValue">0</div>
                    <div class="edsys-calculator-form__result-unit">Ампер</div>
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
                        <h3>Закон Ома и расчет тока</h3>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">I = P / (U × K × cos φ)</div>
                            <div class="edsys-calculator-formula__description">Формула расчета тока с учетом коэффициента мощности</div>
                        </div>

                        <p>Где:</p>
                        <ul>
                            <li><strong>I</strong> – сила тока в амперах (А)</li>
                            <li><strong>P</strong> – мощность нагрузки в ваттах (Вт)</li>
                            <li><strong>U</strong> – напряжение сети в вольтах (В)</li>
                            <li><strong>K</strong> – коэффициент для типа сети (1 для однофазной, √3 для трехфазной)</li>
                            <li><strong>cos φ</strong> – коэффициент мощности</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Коэффициент мощности</h3>
                        <p><strong>Коэффициент мощности (cos φ)</strong> характеризует эффективность использования электрической энергии в цепи переменного тока.</p>

                        <ul>
                            <li><strong>cos φ = 1</strong> – чисто активная нагрузка (резистивная)</li>
                            <li><strong>cos φ = 0.95</strong> – смешанная нагрузка (активно-реактивная)</li>
                            <li><strong>cos φ = 0.85</strong> – реактивная нагрузка</li>
                            <li><strong>cos φ = 0.8</strong> – индуктивная нагрузка (двигатели, трансформаторы)</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Типы нагрузок</h3>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Активная нагрузка (cos φ = 1):</h4>
                                <ul>
                                    <li>Лампы накаливания</li>
                                    <li>Нагревательные элементы</li>
                                    <li>Резисторы</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Реактивная нагрузка (cos φ < 1):</h4>
                                <ul>
                                    <li>Электродвигатели</li>
                                    <li>Трансформаторы</li>
                                    <li>Дроссели</li>
                                    <li>Конденсаторы</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Практические рекомендации</h3>
                        <ul>
                            <li>Для точных расчетов используйте реальные значения cos φ из паспорта оборудования</li>
                            <li>При проектировании закладывайте запас по току 10-20%</li>
                            <li>Учитывайте пусковые токи для двигательных нагрузок</li>
                            <li>Для трехфазных систем используйте линейное напряжение</li>
                            <li>Контролируйте коэффициент мощности для оптимизации энергопотребления</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Сопутствующие товары</h2>
                    <p class="edsys-calculator-categories__subtitle">Оборудование для распределения и управления электропитанием</p>
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