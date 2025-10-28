<?php
/**
 * Калькулятор электрооборудования сцены
 * Версия: 1.0
 * Автор: EDS Development Team
 *
 * Использует централизованную систему категорий товаров из calculators-config.php
 * Интерактивный калькулятор для подбора и расчета нагрузки сценического оборудования
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Настройка SEO
$APPLICATION->SetTitle("Калькулятор электрооборудования сцены");
$APPLICATION->SetPageProperty("description", "Профессиональный калькулятор для расчета нагрузки сценического оборудования. Подбор приборов, расчет мощности и тока для однофазных и трехфазных сетей.");
$APPLICATION->SetPageProperty("keywords", "калькулятор сценического оборудования, расчет нагрузки сцены, мощность световых приборов, ток потребления, сценическое освещение, расчет электрики сцены");

// Подключение стилей и скриптов
$APPLICATION->SetAdditionalCSS("/local/templates/edsy_main/assets/css/edsys-calculators-common.css");
$APPLICATION->AddHeadScript("/local/templates/edsy_main/assets/js/edsys-calculators-core.js");
$APPLICATION->AddHeadScript("/kalkulator-elektrooborudovaniya-sceny/script.js");

// Структурированные данные для SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebApplication",
	"name" => "Калькулятор электрооборудования сцены",
	"description" => "Профессиональный калькулятор для расчета нагрузки и подбора сценического оборудования",
	"url" => "https://" . $_SERVER['HTTP_HOST'] . "/kalkulator-elektrooborudovaniya-sceny/",
	"applicationCategory" => "UtilityApplication",
	"operatingSystem" => "Web Browser",
	"offers" => [
		"@type" => "Offer",
		"price" => "0",
		"priceCurrency" => "RUB"
	],
	"featureList" => [
		"База данных сценических приборов",
		"Автоматический расчет мощности",
		"Расчет тока для 1-фазных и 3-фазных сетей",
		"Добавление собственных устройств",
		"Запрос на добавление нового оборудования"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . '</script>');

// Хлебные крошки
$APPLICATION->AddChainItem("Главная", "/");
$APPLICATION->AddChainItem("Калькуляторы", "/kalkulyatory/");
$APPLICATION->AddChainItem("Калькулятор электрооборудования сцены");

// Параметры навигации
$arNavParams = [
	'CURRENT_CALCULATOR' => 'stage-equipment'
];

// Получаем категории товаров из централизованной системы
$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";
if (file_exists($calculatorsConfigPath)) {
	require_once($calculatorsConfigPath);
	$productCategories = getCalculatorCategories('stage-equipment', 6);
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
                        <span class="edsys-calculator-breadcrumbs__current">Калькулятор электрооборудования сцены</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок страницы -->
            <header class="edsys-calculator-header">
                <h1 class="edsys-calculator-title">Калькулятор электрооборудования сцены</h1>
                <p class="edsys-calculator-subtitle">Профессиональный расчет нагрузки и подбор оборудования для сценических площадок</p>
            </header>

            <!-- Введение -->
            <div class="edsys-calculator-intro">
                <p><strong>Калькулятор электрооборудования сцены</strong> поможет точно рассчитать электрическую нагрузку для вашего мероприятия. Выберите необходимое оборудование из базы данных или добавьте собственные устройства. Калькулятор автоматически вычислит общую мощность и ток для однофазных и трехфазных сетей.</p>
            </div>

            <!-- Калькулятор -->
            <section class="edsys-calculator-form edsys-stage-equipment-calculator" id="stageEquipmentCalculator">
                <div class="edsys-calculator-form__header">
                    <h2 class="edsys-calculator-form__title">Расчет нагрузки оборудования</h2>
                    <p class="edsys-calculator-form__description">Выберите оборудование и укажите количество для автоматического расчета</p>
                </div>

                <!-- Модальное окно для запроса добавления устройства -->
                <div id="edsysDeviceRequestModal" class="edsys-stage-modal">
                    <div class="edsys-stage-modal__content">
                        <span class="edsys-stage-modal__close">&times;</span>
                        <h3 class="edsys-stage-modal__title">Запрос на добавление устройства</h3>
                        <p class="edsys-stage-modal__subtitle">Укажите контактные данные для уведомления о добавлении устройства в базу</p>

                        <div class="edsys-stage-modal__field">
                            <label class="edsys-stage-modal__label">Ваше имя</label>
                            <input type="text" class="edsys-stage-modal__input" id="userName" placeholder="Введите ваше имя">
                        </div>

                        <div class="edsys-stage-modal__field">
                            <label class="edsys-stage-modal__label">Ваш email</label>
                            <input type="email" class="edsys-stage-modal__input" id="userEmail" placeholder="Введите ваш email">
                        </div>

                        <div class="edsys-stage-modal__field">
                            <label class="edsys-stage-modal__label">Комментарии</label>
                            <textarea class="edsys-stage-modal__textarea" id="userComments" placeholder="Пожалуйста, укажите ссылку на сайт производителя (желательно)"></textarea>
                        </div>

                        <button type="button" class="edsys-stage-modal__button" id="submitDeviceRequest">Отправить запрос</button>
                    </div>
                </div>

                <!-- Контейнер для строк оборудования -->
                <div class="edsys-stage-equipment-container" id="equipmentContainer">
                    <!-- Первая строка оборудования -->
                    <div class="edsys-stage-equipment-row" data-row="1">
                        <div class="edsys-stage-equipment-field edsys-stage-equipment-field--device">
                            <label class="edsys-stage-equipment-label">Выберите прибор</label>
                            <div class="edsys-stage-equipment-search">
                                <input type="text"
                                       class="edsys-stage-equipment-input edsys-stage-search-input"
                                       id="deviceSearch1"
                                       placeholder="Введите название устройства"
                                       autocomplete="off">
                                <div class="edsys-stage-equipment-dropdown" id="deviceDropdown1">
                                    <div class="edsys-stage-equipment-list" data-row="1"></div>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-stage-equipment-field edsys-stage-equipment-field--quantity">
                            <label class="edsys-stage-equipment-label">Количество</label>
                            <input type="number"
                                   class="edsys-stage-equipment-input edsys-stage-quantity-input"
                                   min="1"
                                   value="1"
                                   data-row="1">
                        </div>

                        <div class="edsys-stage-equipment-field edsys-stage-equipment-field--power">
                            <label class="edsys-stage-equipment-label">Мощность (Вт)</label>
                            <input type="text"
                                   class="edsys-stage-equipment-input edsys-stage-power-input"
                                   readonly
                                   value="0"
                                   data-row="1">
                        </div>

                        <div class="edsys-stage-equipment-field edsys-stage-equipment-field--current">
                            <label class="edsys-stage-equipment-label">Ток (А)</label>
                            <input type="text"
                                   class="edsys-stage-equipment-input edsys-stage-current-input"
                                   readonly
                                   value="0"
                                   data-row="1">
                        </div>

                        <div class="edsys-stage-equipment-actions">
                            <button type="button" class="edsys-stage-equipment-delete" data-row="1" style="display: none;">
                                <i class="ph ph-thin ph-x"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Кнопка добавления устройства -->
                <div class="edsys-stage-equipment-add-section">
                    <div class="edsys-stage-equipment-buttons">
                        <button type="button" class="edsys-stage-equipment-add-button" id="addCustomDevice">
                            <i class="ph ph-thin ph-plus-circle"></i>
                            Добавить свое устройство
                        </button>
                        <button type="button" class="edsys-stage-equipment-clear-button" id="clearAllDevices">
                            <i class="ph ph-thin ph-trash"></i>
                            Очистить все
                        </button>
                    </div>
                </div>

                <!-- Итоговые результаты -->
                <div class="edsys-stage-equipment-totals">
                    <h3 class="edsys-stage-equipment-totals__title">Общие результаты</h3>

                    <div class="edsys-stage-equipment-totals__grid">
                        <div class="edsys-stage-equipment-totals__item">
                            <label class="edsys-stage-equipment-totals__label">Общая мощность</label>
                            <div class="edsys-stage-equipment-totals__value" id="totalPower">0 Вт</div>
                        </div>

                        <div class="edsys-stage-equipment-totals__item">
                            <label class="edsys-stage-equipment-totals__label">Ток (1 фаза)</label>
                            <div class="edsys-stage-equipment-totals__value" id="totalCurrent1">0 А</div>
                        </div>

                        <div class="edsys-stage-equipment-totals__item">
                            <label class="edsys-stage-equipment-totals__label">Ток (3 фазы)</label>
                            <div class="edsys-stage-equipment-totals__value" id="totalCurrent3">0 А</div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- Техническая информация -->
            <section class="edsys-calculator-technical">
                <h2 class="edsys-calculator-technical__title">Техническая информация</h2>

                <div class="edsys-calculator-technical__content">
                    <div class="edsys-calculator-technical__section">
                        <h3>Принцип работы калькулятора</h3>
                        <p>Калькулятор содержит базу данных популярного сценического оборудования с указанием потребляемой мощности. При выборе устройства автоматически рассчитывается:</p>
                        <ul>
                            <li><strong>Общая мощность</strong> - суммарная мощность всех выбранных устройств</li>
                            <li><strong>Ток для однофазной сети</strong> - I = P / U (где U = 230В)</li>
                            <li><strong>Ток для трехфазной сети</strong> - I = P / (√3 × U) (где U = 400В)</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>База данных оборудования</h3>
                        <p>В калькуляторе представлены следующие категории оборудования:</p>

                        <div class="edsys-calculator-technical__two-columns">
                            <div class="edsys-calculator-technical__column">
                                <h4>Световые приборы:</h4>
                                <ul>
                                    <li>Интеллектуальные прожекторы (moving head)</li>
                                    <li>PAR-прожекторы</li>
                                    <li>Блиндеры (LED и галогенные)</li>
                                    <li>Стробоскопы</li>
                                </ul>
                            </div>

                            <div class="edsys-calculator-technical__column">
                                <h4>Спецэффекты:</h4>
                                <ul>
                                    <li>Генераторы тумана (хейзеры)</li>
                                    <li>Дым-машины</li>
                                    <li>Эффект-машины</li>
                                    <li>Лазерные установки</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Особенности расчета для сценического оборудования</h3>
                        <ul>
                            <li><strong>Пусковые токи</strong> - для ламп накаливания могут превышать номинальные в 10-15 раз</li>
                            <li><strong>Коэффициент одновременности</strong> - не все оборудование работает одновременно</li>
                            <li><strong>Запас мощности</strong> - рекомендуется закладывать 20-30% запаса</li>
                            <li><strong>Тип нагрузки</strong> - учитывайте реактивную составляющую для двигательных нагрузок</li>
                        </ul>
                    </div>

                    <div class="edsys-calculator-technical__section">
                        <h3>Рекомендации по электроснабжению</h3>
                        <ul>
                            <li>Используйте качественные дистрибьюторы питания с защитными устройствами</li>
                            <li>Обеспечьте равномерное распределение нагрузки по фазам</li>
                            <li>Предусмотрите отдельные линии для мощных потребителей</li>
                            <li>Используйте кабели с запасом по сечению</li>
                            <li>Обязательно заземляйте все металлические части оборудования</li>
                        </ul>
                    </div>
                </div>
            </section>

            <!-- Карточки товаров -->
            <section class="edsys-calculator-categories">
                <div class="edsys-calculator-categories__header">
                    <h2 class="edsys-calculator-categories__title">Оборудование для сценических площадок</h2>
                    <p class="edsys-calculator-categories__subtitle">Профессиональные решения для распределения питания</p>
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