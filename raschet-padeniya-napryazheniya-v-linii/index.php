<?php
/**
 * Калькулятор расчета падения напряжения в линии
 * Версия: 1.0
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Профессиональный калькулятор для расчета падения напряжения в кабельных и воздушных линиях
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Калькулятор расчета падения напряжения в линии");
$APPLICATION->SetPageProperty("description", "Профессиональный калькулятор для расчета падения напряжения в кабельных и воздушных линиях. Учет материала проводника, сечения, мощности нагрузки и температуры.");
$APPLICATION->SetPageProperty("keywords", "калькулятор падения напряжения, расчет напряжения, кабельная линия, воздушная линия, медный провод, алюминиевый провод, сечение провода");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/raschet-padeniya-napryazheniya-v-linii/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор расчета падения напряжения в линии",
	"description" => "Профессиональный калькулятор для расчета падения напряжения в кабельных и воздушных линиях",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/raschet-padeniya-napryazheniya-v-linii/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"Расчет для кабельных и воздушных линий",
		"Учет материала проводника и сечения",
		"Коэффициент мощности и температура",
		"Результат в вольтах и процентах",
		"Автоматический расчет"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Расчет падения напряжения в линии");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'voltage-drop-line'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('voltage-drop-line', 6);
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
			'title' => 'DMX-сплиттеры',
			'description' => 'Сплиттеры для разветвления DMX сигналов',
			'icon' => 'ph-split-horizontal',
			'url' => '/cat/dmx-splittery/'
		],
		[
			'title' => 'Устройства передачи сигнала',
			'description' => 'Устройства для передачи аудио и видео сигналов',
			'icon' => 'ph-broadcast',
			'url' => '/cat/ustroystva-peredachi-signala/'
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
                        <span class="edsys-calculator-breadcrumbs__current">Расчет падения напряжения в линии</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Калькулятор расчета падения напряжения в линии</h1>
                <p class="edsys-calculator-subtitle">Профессиональный расчет потерь напряжения в кабельных и воздушных линиях</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор расчета падения напряжения в линии</strong> позволяет определить потери напряжения в кабельных и воздушных линиях с учетом материала проводника, сечения, мощности нагрузки, коэффициента мощности и температуры окружающей среды.</p>
            </div>

            <!-- Форма калькулятора -->
            <section class="edsys-calculator-form" id="voltageDropCalculator">
                <div class="edsys-calculator-form__header">
                    <h2 class="edsys-calculator-form__title">Расчет падения напряжения</h2>
                    <p class="edsys-calculator-form__description">Введите параметры для автоматического расчета падения напряжения в линии</p>
                </div>

                <form class="edsys-calculator-form__fields edsys-calculator-form__fields--voltage-drop" id="calculatorForm" name="form1">
                    <div class="edsys-calculator-form__field">
                        <label class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-waveform"></i>
                            Тип измерения
                        </label>
                        <div class="edsys-calculator-toggle">
                            <input type="radio" id="phaseNeutral" name="num" value="2" checked>
                            <label for="phaseNeutral" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Фаза-ноль</span>
                            </label>
                            <input type="radio" id="phasePhase" name="num" value="0.34">
                            <label for="phasePhase" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Фаза-фаза</span>
                            </label>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-path"></i>
                            Тип линии
                        </label>
                        <div class="edsys-calculator-toggle">
                            <input type="radio" id="cableLine" name="num6" value="0.07" checked>
                            <label for="cableLine" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Кабельная</span>
                            </label>
                            <input type="radio" id="airLine" name="num6" value="10">
                            <label for="airLine" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Воздушная</span>
                            </label>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-diamond"></i>
                            Материал провода
                        </label>
                        <div class="edsys-calculator-toggle">
                            <input type="radio" id="copperMaterial" name="num1" value="0.0175" checked>
                            <label for="copperMaterial" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Медь</span>
                            </label>
                            <input type="radio" id="aluminumMaterial" name="num1" value="0.0271">
                            <label for="aluminumMaterial" class="edsys-calculator-toggle__option">
                                <span class="edsys-calculator-toggle__text">Алюминий</span>
                            </label>
                        </div>
                    </div>

                    <div class="edsys-calculator-form__field">
                        <label for="wireSection" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-circle"></i>
                            Сечение жил проводников
                        </label>
                        <select id="wireSection" name="num2" class="edsys-calculator-form__input">
                            <option value="0.5">0.5 мм²</option>
                            <option value="1">1.0 мм²</option>
                            <option value="1.5">1.5 мм²</option>
                            <option value="2.5">2.5 мм²</option>
                            <option value="4">4 мм²</option>
                            <option value="6">6 мм²</option>
                            <option value="10">10 мм²</option>
                            <option value="16">16 мм²</option>
                            <option value="25">25 мм²</option>
                            <option value="35">35 мм²</option>
                            <option value="50">50 мм²</option>
                            <option value="70">70 мм²</option>
                            <option value="95">95 мм²</option>
                            <option value="120">120 мм²</option>
                        </select>
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
                        <label for="powerFactor" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-function"></i>
                            Коэффициент мощности
                        </label>
                        <input type="number"
                               id="powerFactor"
                               name="num5"
                               class="edsys-calculator-form__input"
                               placeholder="cos φ"
                               min="0.1"
                               max="1"
                               step="0.01"
                               value="0.95"
                               required>
                        <span class="edsys-calculator-form__unit">cos φ</span>
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

                    <div class="edsys-calculator-form__field edsys-calculator-form__field--with-unit">
                        <label for="temperature" class="edsys-calculator-form__label">
                            <i class="ph ph-thin ph-thermometer"></i>
                            Температура кабеля
                        </label>
                        <input type="number"
                               id="temperature"
                               name="num7"
                               class="edsys-calculator-form__input"
                               placeholder="Температура"
                               min="-50"
                               max="100"
                               step="1"
                               value="20"
                               required>
                        <span class="edsys-calculator-form__unit">°C</span>
                    </div>

                    <input type="hidden" name="res" id="hiddenResultVolts" value="0">
                    <input type="hidden" name="resu" id="hiddenResultPercent" value="0">
                </form>

                <div class="edsys-calculator-form__results-grid">
                    <div class="edsys-calculator-form__result" id="calculatorResultVolts">
                        <div class="edsys-calculator-form__result-label">Падение напряжения</div>
                        <div class="edsys-calculator-form__result-value" id="voltageDropVolts">0</div>
                        <div class="edsys-calculator-form__result-unit">Вольт</div>
                    </div>

                    <div class="edsys-calculator-form__result" id="calculatorResultPercent">
                        <div class="edsys-calculator-form__result-label">Падение напряжения</div>
                        <div class="edsys-calculator-form__result-value" id="voltageDropPercent">0</div>
                        <div class="edsys-calculator-form__result-unit">%</div>
                    </div>
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
                            <div class="edsys-calculator-formula__expression">ΔU = (P × L × K × (ρ + X × tan φ)) / (U × S)</div>
                            <div class="edsys-calculator-formula__description">Расчет падения напряжения в линии</div>
                        </div>

                        <p>Где:</p>
                        <ul>
                            <li><strong>ΔU</strong> – падение напряжения в вольтах (В)</li>
                            <li><strong>P</strong> – мощность нагрузки в ваттах (Вт)</li>
                            <li><strong>L</strong> – длина линии в метрах (м)</li>
                            <li><strong>K</strong> – коэффициент для типа измерения (2 для фазы-ноль, 0.34 для фазы-фаза)</li>
                            <li><strong>ρ</strong> – удельное сопротивление материала при температуре (Ом·мм²/м)</li>
                            <li><strong>X</strong> – реактивное сопротивление линии (Ом·мм²/м)</li>
                            <li><strong>tan φ</strong> – тангенс угла сдвига фаз</li>
                            <li><strong>U</strong> – напряжение сети в вольтах (220В или 380В)</li>
                            <li><strong>S</strong> – сечение проводника в мм²</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Удельные сопротивления материалов</h3>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Медь при 20°C:</h4>
                                <ul>
                                    <li>Удельное сопротивление: 0.0175 Ом·мм²/м</li>
                                    <li>Температурный коэффициент: 0.00393 1/°C</li>
                                    <li>Высокая проводимость</li>
                                    <li>Стабильные характеристики</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Алюминий при 20°C:</h4>
                                <ul>
                                    <li>Удельное сопротивление: 0.0271 Ом·мм²/м</li>
                                    <li>Температурный коэффициент: 0.00403 1/°C</li>
                                    <li>Большее сопротивление</li>
                                    <li>Сильнее зависит от температуры</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Типы линий и реактивное сопротивление</h3>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Кабельные линии:</h4>
                                <ul>
                                    <li>Реактивное сопротивление: 0.07 Ом·мм²/м</li>
                                    <li>Малое индуктивное сопротивление</li>
                                    <li>Защищены от внешних воздействий</li>
                                    <li>Стабильные параметры</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Воздушные линии:</h4>
                                <ul>
                                    <li>Реактивное сопротивление: 10 Ом·мм²/м</li>
                                    <li>Большое индуктивное сопротивление</li>
                                    <li>Влияние погодных условий</li>
                                    <li>Большие расстояния между проводами</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Влияние температуры</h3>
                        <p>Сопротивление проводника зависит от температуры по формуле:</p>

                        <div class="edsys-calculator-formula">
                            <div class="edsys-calculator-formula__expression">ρ(T) = ρ₂₀ × [1 + α × (T - 20)]</div>
                            <div class="edsys-calculator-formula__description">Температурная зависимость сопротивления</div>
                        </div>

                        <p>При повышении температуры сопротивление увеличивается, что приводит к большим потерям напряжения.</p>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Практические рекомендации</h3>
                        <ul>
                            <li>Для <strong>освещения</strong> допустимые потери не более 2.5%</li>
                            <li>Для <strong>силовых нагрузок</strong> допустимые потери не более 5%</li>
                            <li>Учитывайте <strong>температуру окружающей среды</strong> при расчетах</li>
                            <li>Для <strong>длинных линий</strong> рассматривайте повышение напряжения</li>
                            <li>При <strong>низком коэффициенте мощности</strong> потери значительно увеличиваются</li>
                            <li>Используйте <strong>кабельные линии</strong> для снижения реактивных потерь</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Стандартные значения коэффициента мощности</h3>
                        <ul>
                            <li><strong>Лампы накаливания</strong>: cos φ = 1.0</li>
                            <li><strong>LED освещение</strong>: cos φ = 0.9-0.95</li>
                            <li><strong>Асинхронные двигатели</strong>: cos φ = 0.8-0.9</li>
                            <li><strong>Люминесцентные лампы</strong>: cos φ = 0.5-0.6 (без компенсации)</li>
                            <li><strong>Трансформаторы</strong>: cos φ = 0.8-0.9</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Сопутствующие товары</h2>
                    <p class="edsys-calculator-categories__subtitle">Кабельная продукция и оборудование для передачи сигналов</p>
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