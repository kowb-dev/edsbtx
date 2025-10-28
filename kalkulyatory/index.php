<?php
/**
 * Calculators Main Page
 * Version: 1.0.0
 * Author: EdSys Development Team
 * Description: Main page displaying calculator cards with electrical equipment tools
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Set page title and meta
$APPLICATION->SetTitle("Калькуляторы расчета электрических величин");
$APPLICATION->SetPageProperty("description", "Профессиональные калькуляторы для расчета электрооборудования сцены, сечения проводов, тока и напряжения. Точные расчеты для сценического освещения.");
$APPLICATION->SetPageProperty("keywords", "калькулятор электрооборудования, расчет сечения провода, калькулятор тока, падение напряжения, сценическое освещение");

// Include CSS and JS files
$APPLICATION->AddHeadString('<link rel="stylesheet" href="/kalkulyatory/style.css">');
$APPLICATION->AddHeadString('<script defer src="/kalkulyatory/script.js"></script>');

// Calculator data array
$calculators = [
	[
		'id' => 'stage-equipment',
		'title' => 'Калькулятор электрооборудования сцены',
		'description' => 'Расчет нагрузки и распределения электрооборудования для сценического освещения',
		'icon' => 'ph-lightning',
		'url' => '/kalkulator-elektrooborudovaniya-sceny/',
		'category' => 'equipment'
	],
	[
		'id' => 'watts-to-amperes',
		'title' => 'Онлайн калькулятор перевода Ватт в Амперы',
		'description' => 'Быстрый перевод мощности в силу тока для однофазных и трехфазных цепей',
		'icon' => 'ph-arrows-clockwise',
		'url' => '/onlayn-kalkulyator-perevoda-vatt-v-ampery/',
		'category' => 'conversion'
	],
	[
		'id' => 'wire-section-diameter',
		'title' => 'Расчет сечения провода по диаметру или количеству витков',
		'description' => 'Определение сечения провода по диаметру жилы или количеству витков',
		'icon' => 'ph-ruler',
		'url' => '/raschet-secheniya-provoda-po-diametru-ili-kolichestvu-vitkov/',
		'category' => 'wire'
	],
	[
		'id' => 'circuit-current',
		'title' => 'Расчет тока в цепи',
		'description' => 'Вычисление силы тока по закону Ома для различных типов цепей',
		'icon' => 'ph-circuitry',
		'url' => '/raschet-toka-v-tsepi/',
		'category' => 'current'
	],
	[
		'id' => 'wire-section-voltage-drop',
		'title' => 'Калькулятор расчета сечения провода по заданной потере напряжения',
		'description' => 'Подбор оптимального сечения провода с учетом допустимых потерь напряжения',
		'icon' => 'ph-chart-line-down',
		'url' => '/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/',
		'category' => 'wire'
	],
	[
		'id' => 'voltage-drop-line',
		'title' => 'Расчет падения напряжения в линии',
		'description' => 'Определение потерь напряжения в кабельных линиях различной длины',
		'icon' => 'ph-trend-down',
		'url' => '/raschet-padeniya-napryazheniya-v-linii/',
		'category' => 'voltage'
	]
];

// Add structured data for SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebPage",
	"name" => "Калькуляторы расчета электрических величин",
	"description" => "Профессиональные калькуляторы для расчета электрооборудования сцены, сечения проводов, тока и напряжения",
	"url" => "https://" . $_SERVER['SERVER_NAME'] . "/kalkulyatory/",
	"mainEntity" => [
		"@type" => "ItemList",
		"name" => "Калькуляторы",
		"description" => "Список профессиональных калькуляторов для электрооборудования",
		"itemListElement" => array_map(function($calc, $index) {
			return [
				"@type" => "ListItem",
				"position" => $index + 1,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => $calc['title'],
					"description" => $calc['description'],
					"applicationCategory" => "UtilityApplication",
					"url" => "https://" . $_SERVER['SERVER_NAME'] . $calc['url']
				]
			];
		}, $calculators, array_keys($calculators))
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');
?>

    <main class="edsys-calculators-main">
        <!-- Hero Section -->
        <section class="edsys-calculators-hero">
            <div class="edsys-calculators-container">
                <h1 class="edsys-calculators-hero__title">
                    Калькуляторы расчета электрических величин
                </h1>
                <p class="edsys-calculators-hero__description">
                    Профессиональные инструменты для точных расчетов электрических параметров сценического оборудования.
                    Все необходимые калькуляторы для проектирования и эксплуатации систем освещения.
                </p>
            </div>
        </section>

        <!-- Calculators Grid -->
        <section class="edsys-calculators-section">
            <div class="edsys-calculators-container">
                <div class="edsys-calculators-grid">
					<?php foreach($calculators as $calculator): ?>
                        <article class="edsys-calculator-card"
                                 data-calculator-id="<?= htmlspecialchars($calculator['id']) ?>"
                                 data-calculator-url="<?= htmlspecialchars($calculator['url']) ?>"
                                 data-calculator-category="<?= htmlspecialchars($calculator['category']) ?>">

                            <div class="edsys-calculator-card__icon">
                                <i class="ph-thin <?= htmlspecialchars($calculator['icon']) ?>" aria-hidden="true"></i>
                            </div>

                            <div class="edsys-calculator-card__content">
                                <h2 class="edsys-calculator-card__title">
									<?= htmlspecialchars($calculator['title']) ?>
                                </h2>
                                <p class="edsys-calculator-card__description">
									<?= htmlspecialchars($calculator['description']) ?>
                                </p>
                            </div>

                            <div class="edsys-calculator-card__action">
                            <span class="edsys-calculator-card__button">
                                Перейти к калькулятору
                                <i class="ph-thin ph-arrow-right" aria-hidden="true"></i>
                            </span>
                            </div>
                        </article>
					<?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Additional Info Section -->
        <section class="edsys-calculators-info">
            <div class="edsys-calculators-container">
                <div class="edsys-calculators-info__content">
                    <h2 class="edsys-calculators-info__title">
                        Точные расчеты для профессионалов
                    </h2>
                    <p class="edsys-calculators-info__text">
                        Наши калькуляторы созданы специально для специалистов в области сценического освещения и электрооборудования.
                        Все расчеты выполняются с учетом действующих ГОСТов и нормативов безопасности.
                    </p>
                </div>
            </div>
        </section>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>