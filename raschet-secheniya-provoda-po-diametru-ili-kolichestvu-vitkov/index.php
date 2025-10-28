<?php
/**
 * Калькулятор расчета сечения провода по диаметру или количеству витков
 * Версия: 1.0
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Профессиональный калькулятор для определения сечения проводов
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Калькулятор расчета сечения провода по диаметру или количеству витков");
$APPLICATION->SetPageProperty("description", "Профессиональный калькулятор для определения сечения провода. Расчет по диаметру жилы или количеству витков. Точные формулы для кабельной продукции.");
$APPLICATION->SetPageProperty("keywords", "калькулятор сечения провода, расчет сечения кабеля, диаметр провода, количество витков, сечение жилы, кабельная продукция");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/raschet-secheniya-provoda-po-diametru-ili-kolichestvu-vitkov/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор расчета сечения провода",
	"description" => "Профессиональный калькулятор для определения сечения провода по диаметру или количеству витков",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"Расчет по диаметру провода",
		"Расчет по количеству витков",
		"Автоматический пересчет",
		"Валидация входных данных",
		"Профессиональные рекомендации"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Расчет сечения провода");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'wire-section-diameter'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('wire-section-diameter', 6);
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
			'title' => 'Сценические лючки',
			'description' => 'Напольные и настенные лючки для сценических площадок',
			'icon' => 'ph-square-half',
			'url' => '/cat/stsenicheskie-lyuchki/'
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
                        <span class="edsys-calculator-breadcrumbs__current">Расчет сечения провода</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Калькулятор расчета сечения провода</h1>
                <p class="edsys-calculator-subtitle">Определение сечения провода по диаметру или количеству витков</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор расчета сечения провода</strong> предназначен для быстрого и точного определения сечения проводника по его диаметру или по количеству витков на единицу длины. Незаменимый инструмент для специалистов, работающих с кабельной продукцией.</p>
            </div>

            <!-- Калькуляторы -->
            <div class="edsys-wire-calculators">
                <!-- Калькулятор по диаметру -->
                <section class="edsys-calculator-form edsys-wire-calculator" id="diameterCalculator">
                    <div class="edsys-calculator-form__header">
                        <h2 class="edsys-calculator-form__title">Расчет по диаметру</h2>
                        <p class="edsys-calculator-form__description">Введите диаметр провода для расчета сечения</p>
                    </div>

                    <div class="edsys-calculator-form__fields">
                        <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                            <label for="diameter" class="edsys-calculator-form__label">
                                <i class="ph ph-thin ph-ruler"></i>
                                Диаметр провода
                            </label>
                            <input type="number"
                                   id="diameter"
                                   class="edsys-calculator-form__input"
                                   placeholder="Введите диаметр"
                                   min="0.1"
                                   max="100"
                                   step="0.1"
                                   required>
                            <span class="edsys-calculator-form__unit">мм</span>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__result" id="diameterResult">
                        <div class="edsys-calculator-form__result-label">Сечение провода</div>
                        <div class="edsys-calculator-form__result-value" id="diameterValue">0</div>
                        <div class="edsys-calculator-form__result-unit">мм²</div>
                    </div>

                    <div class="edsys-calculator-form__actions">
                        <button type="button"
                                class="edsys-calculator-form__button edsys-calculator-form__button--secondary"
                                id="resetDiameter">
                            <i class="ph ph-thin ph-arrows-counter-clockwise"></i>
                            Сбросить
                        </button>
                    </div>
                </section>

                <!-- Калькулятор по количеству витков -->
                <section class="edsys-calculator-form edsys-wire-calculator" id="turnsCalculator">
                    <div class="edsys-calculator-form__header">
                        <h2 class="edsys-calculator-form__title">Расчет по количеству витков</h2>
                        <p class="edsys-calculator-form__description">Введите количество витков и длину для расчета сечения</p>
                    </div>

                    <div class="edsys-calculator-form__fields">
                        <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                            <label for="turns" class="edsys-calculator-form__label">
                                <i class="ph ph-thin ph-spiral"></i>
                                Количество витков
                            </label>
                            <input type="number"
                                   id="turns"
                                   class="edsys-calculator-form__input"
                                   placeholder="Введите количество витков"
                                   min="1"
                                   max="10000"
                                   step="1"
                                   required>
                            <span class="edsys-calculator-form__unit">шт</span>
                        </div>

                        <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                            <label for="length" class="edsys-calculator-form__label">
                                <i class="ph ph-thin ph-ruler"></i>
                                Длина намотки
                            </label>
                            <input type="number"
                                   id="length"
                                   class="edsys-calculator-form__input"
                                   placeholder="Введите длину"
                                   min="0.1"
                                   max="1000"
                                   step="0.1"
                                   required>
                            <span class="edsys-calculator-form__unit">мм</span>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__result" id="turnsResult">
                        <div class="edsys-calculator-form__result-label">Сечение провода</div>
                        <div class="edsys-calculator-form__result-value" id="turnsValue">0</div>
                        <div class="edsys-calculator-form__result-unit">мм²</div>
                    </div>

                    <div class="edsys-calculator-form__actions">
                        <button type="button"
                                class="edsys-calculator-form__button edsys-calculator-form__button--secondary"
                                id="resetTurns">
                            <i class="ph ph-thin ph-arrows-counter-clockwise"></i>
                            Сбросить
                        </button>
                    </div>
                </section>
            </div>

            <!-- Техническая информация -->
            <section class="edsys-calculator-technical">
                <h2 class="edsys-calculator-technical__title">Техническая информация</h2>

                <div class="edsys-calculator-technical__content">
                    <div class="edsys-calculator-technical__section">
                        <h3>Формулы расчета</h3>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">S = π × (d/2)² = π × d² / 4</div>
                            <div class="edsys-calculator-formula__description">Расчет сечения по диаметру</div>
                        </div>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">d = L / (n × π), затем S = π × d² / 4</div>
                            <div class="edsys-calculator-formula__description">Расчет сечения по количеству витков</div>
                        </div>

                        <p>Где:</p>
                        <ul>
                            <li><strong>S</strong> – сечение провода в мм²</li>
                            <li><strong>d</strong> – диаметр провода в мм</li>
                            <li><strong>L</strong> – длина намотки в мм</li>
                            <li><strong>n</strong> – количество витков</li>
                            <li><strong>π</strong> – математическая константа (≈ 3.14159)</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Стандартные сечения проводов</h3>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Малые сечения:</h4>
                                <ul>
                                    <li>0.5 мм² (d ≈ 0.8 мм)</li>
                                    <li>0.75 мм² (d ≈ 0.98 мм)</li>
                                    <li>1.0 мм² (d ≈ 1.13 мм)</li>
                                    <li>1.5 мм² (d ≈ 1.38 мм)</li>
                                    <li>2.5 мм² (d ≈ 1.78 мм)</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Большие сечения:</h4>
                                <ul>
                                    <li>4.0 мм² (d ≈ 2.26 мм)</li>
                                    <li>6.0 мм² (d ≈ 2.76 мм)</li>
                                    <li>10.0 мм² (d ≈ 3.57 мм)</li>
                                    <li>16.0 мм² (d ≈ 4.51 мм)</li>
                                    <li>25.0 мм² (d ≈ 5.64 мм)</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Методы измерения диаметра</h3>
                        <ul>
                            <li><strong>Штангенциркуль</strong> – наиболее точный способ измерения</li>
                            <li><strong>Микрометр</strong> – для высокоточных измерений тонких проводов</li>
                            <li><strong>Метод намотки</strong> – определение диаметра по количеству витков</li>
                            <li><strong>Калибр проводов</strong> – специальный инструмент для стандартных размеров</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Особенности расчета</h3>
                        <ul>
                            <li>Расчет ведется для круглых одножильных проводов</li>
                            <li>Для многожильных проводов учитывается сечение одной жилы</li>
                            <li>При измерении методом намотки используется плотная укладка витков</li>
                            <li>Точность расчета зависит от точности измерения диаметра</li>
                            <li>Для изолированных проводов измеряется диаметр жилы без изоляции</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Практические рекомендации</h3>
                        <ul>
                            <li>При измерении штангенциркулем делайте несколько измерений в разных местах</li>
                            <li>Для метода намотки используйте не менее 10 витков для точности</li>
                            <li>Учитывайте, что реальное сечение может отличаться от номинального на ±10%</li>
                            <li>При выборе провода всегда используйте ближайшее большее стандартное сечение</li>
                            <li>Для ответственных соединений проверяйте сечение несколькими методами</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Сопутствующие товары</h2>
                    <p class="edsys-calculator-categories__subtitle">Кабельная продукция и аксессуары для профессиональной работы</p>
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