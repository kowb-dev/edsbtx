<?php
/**
 * Калькулятор перевода Ватт в Амперы
 * Версия: 1.2
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Профессиональный калькулятор для расчета силы тока по мощности и напряжению
 * для однофазных и трехфазных электрических цепей
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Онлайн калькулятор перевода Ватт в Амперы");
$APPLICATION->SetPageProperty("description", "Профессиональный калькулятор для перевода мощности в силу тока. Расчет ампер по ваттам для однофазных и трехфазных цепей. Точные расчеты для сценического оборудования.");
$APPLICATION->SetPageProperty("keywords", "калькулятор ватт в амперы, расчет тока, мощность в амперы, сила тока, электрические расчеты, однофазная сеть, трехфазная сеть");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/onlayn-kalkulyator-perevoda-vatt-v-ampery/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор перевода Ватт в Амперы",
	"description" => "Профессиональный онлайн калькулятор для перевода мощности в силу тока",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/onlayn-kalkulyator-perevoda-vatt-v-ampery/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"Расчет для однофазных цепей",
		"Расчет для трехфазных цепей",
		"Автоматический пересчет",
		"Валидация входных данных",
		"Мобильная версия"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Перевод Ватт в Амперы");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'watts-to-amperes'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('watts-to-amperes', 6);
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
                        <span class="edsys-calculator-breadcrumbs__current">Перевод Ватт в Амперы</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Онлайн калькулятор перевода Ватт в Амперы</h1>
                <p class="edsys-calculator-subtitle">Профессиональный расчет силы тока для сценического оборудования</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор перевода Ватт в Амперы</strong> предназначен для быстрого и точного расчета силы тока в электрической цепи по известной мощности и напряжению. Поддерживает расчеты для однофазных и трехфазных сетей с учетом специфики сценического оборудования.</p>
            </div>

            <!-- Форма калькулятора -->
            <section class="edsys-calculator-form" id="wattsToAmperesCalculator">
                <div class="edsys-calculator-form__header">
                    <h2 class="edsys-calculator-form__title">Расчет силы тока</h2>
                    <p class="edsys-calculator-form__description">Введите мощность и напряжение для автоматического расчета силы тока</p>
                </div>

                <form class="edsys-calculator-form__fields" id="calculatorForm">
                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="power" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-lightning"></i>
                            Мощность (P)
                        </label>
                        <input type="number"
                               id="power"
                               class="edsys-calculator-form__input"
                               placeholder="Введите мощность"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">Вт</span>
                    </div>

                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="voltage" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-plug"></i>
                            Напряжение (U)
                        </label>
                        <input type="number"
                               id="voltage"
                               class="edsys-calculator-form__input"
                               placeholder="Введите напряжение"
                               min="0"
                               step="0.1"
                               required>
                        <span class="edsys-calculator-form__unit">В</span>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label for="phases" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-waveform"></i>
                            Тип сети
                        </label>
                        <select id="phases" class="edsys-calculator-form__input">
                            <option value="1">Однофазная сеть</option>
                            <option value="3">Трехфазная сеть</option>
                        </select>
                    </div>
                </form>
                <div class="edsys-calculator-form__result" id="calculatorResult">
                    <div class="edsys-calculator-form__result-label">Сила тока</div>
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
                        <h3>Формулы расчета</h3>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">I = P / U</div>
                            <div class="edsys-calculator-formula__description">Для однофазной сети</div>
                        </div>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">I = P / (√3 × U × cos φ)</div>
                            <div class="edsys-calculator-formula__description">Для трехфазной сети</div>
                        </div>

                        <p>Где:</p>
                        <ul>
                            <li><strong>I</strong> – сила тока в амперах (А)</li>
                            <li><strong>P</strong> – мощность в ваттах (Вт)</li>
                            <li><strong>U</strong> – напряжение в вольтах (В)</li>
                            <li><strong>cos φ</strong> – коэффициент мощности (принимается равным 1 для упрощения)</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Принцип работы</h3>
                        <p>Калькулятор основан на <strong>законе Ома</strong> и принципах расчета мощности в электрических цепях. Для однофазных сетей используется классическая формула P = U × I, откуда получаем I = P / U.</p>

                        <p>Для трехфазных сетей учитывается <strong>√3</strong> (примерно 1,732) – коэффициент, связанный с соотношением между линейными и фазными величинами в симметричной трехфазной системе.</p>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Практические примеры</h3>
                        <p><strong>Пример 1:</strong> Прожектор мощностью 1000 Вт подключен к однофазной сети 220 В:</p>
                        <p>I = 1000 / 220 = 4,55 А</p>

                        <p><strong>Пример 2:</strong> Комплект светового оборудования мощностью 6000 Вт подключен к трехфазной сети 380 В:</p>
                        <p>I = 6000 / (1,732 × 380) = 9,12 А</p>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Особенности для сценического оборудования</h3>
                        <ul>
                            <li>Учитывайте <strong>пусковые токи</strong> для ламп накаливания (могут быть в 10-15 раз больше номинальных)</li>
                            <li>Для LED-оборудования используйте <strong>коэффициент мощности</strong> 0,9-0,95</li>
                            <li>При расчете нагрузки дистрибьюторов закладывайте <strong>запас 20-30%</strong></li>
                            <li>Учитывайте <strong>длину кабельных линий</strong> и потери напряжения</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Сопутствующие товары</h2>
                    <p class="edsys-calculator-categories__subtitle">Оборудование для безопасной работы с электричеством</p>
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