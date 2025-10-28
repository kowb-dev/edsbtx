<?php
/**
 * Индивидуальные решения EDS - Главная страница с мастером ТЗ
 * Файл: /individualnye-resheniya/index.php
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Установка заголовков и мета-тегов
$APPLICATION->SetTitle("Индивидуальные решения EDS - Профессиональное электрооборудование");
$APPLICATION->SetPageProperty("description", "Разработка и изготовление профессионального электрооборудования точно под ваши требования. Создайте техническое задание за 5 минут и получите персональное коммерческое предложение.");
$APPLICATION->SetPageProperty("keywords", "индивидуальные решения, профессиональное электрооборудование, дистрибьюторы питания, DMX оборудование, техническое задание, EDS");


// Подключение локальных файлов
$APPLICATION->SetAdditionalCSS('/individualnye-resheniya/style.css');
$APPLICATION->AddHeadScript('/individualnye-resheniya/script.js');
?>

    <!-- Hero Section -->
    <section class="edsys-individual-hero">
        <div class="edsys-container">
            <div class="edsys-individual-hero__content">
                <div class="edsys-individual-hero__text">
                    <h1 class="edsys-individual-hero__title">
                        <span class="edsys-highlight-text">Индивидуальные решения</span>
                        для вашего проекта
                    </h1>
                    <p class="edsys-individual-hero__subtitle">
                        Разрабатываем и изготавливаем профессиональное оборудование точно под
                        ваши требования. От концепции до готового устройства.
                    </p>
                    <div class="edsys-individual-hero__features">
                        <div class="edsys-feature-badge">
                            <i class="ph ph-thin ph-lightning"></i>
                            <span>Быстрая разработка</span>
                        </div>
                        <div class="edsys-feature-badge">
                            <i class="ph ph-thin ph-shield-check"></i>
                            <span>Гарантия качества</span>
                        </div>
                        <div class="edsys-feature-badge">
                            <i class="ph ph-thin ph-users"></i>
                            <span>Экспертная поддержка</span>
                        </div>
                    </div>
                    <div class="edsys-individual-hero__actions">
                        <button class="edsys-btn edsys-btn--primary edsys-btn--large" onclick="scrollToWizard()">
                            <i class="ph ph-thin ph-magic-wand"></i>
                            Создать ТЗ за 5 минут
                        </button>
                        <button class="edsys-btn edsys-btn--secondary edsys-btn--large" onclick="scrollToAdvantages()">
                            <i class="ph ph-thin ph-eye"></i>
                            Подробнее
                        </button>
                    </div>
                </div>
                <div class="edsys-individual-hero__visual">
                    <div class="edsys-hero-device">
                        <div class="edsys-hero-device__screen">
                            <div class="edsys-circuit-animation">
                                <!-- Animated circuit pattern -->
                                <svg viewBox="0 0 400 300" class="edsys-circuit-svg">
                                    <defs>
                                        <linearGradient id="circuitGlow" x1="0%" y1="0%" x2="100%" y2="100%">
                                            <stop offset="0%" style="stop-color:#ff2545;stop-opacity:0.8" />
                                            <stop offset="50%" style="stop-color:#0066cc;stop-opacity:0.6" />
                                            <stop offset="100%" style="stop-color:#00cc99;stop-opacity:0.8" />
                                        </linearGradient>
                                    </defs>
                                    <path class="edsys-circuit-path" d="M50,50 L150,50 L150,100 L250,100 L250,150 L350,150" stroke="url(#circuitGlow)" stroke-width="2" fill="none" />
                                    <circle class="edsys-circuit-node" cx="150" cy="50" r="4" fill="#ff2545" />
                                    <circle class="edsys-circuit-node" cx="250" cy="100" r="4" fill="#0066cc" />
                                    <circle class="edsys-circuit-node" cx="350" cy="150" r="4" fill="#00cc99" />
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Technical Specification Wizard -->
    <section class="edsys-wizard" id="wizard">
        <div class="edsys-container">
            <div class="edsys-wizard__header">
                <h2 class="edsys-wizard__title">
                    <i class="ph ph-thin ph-gear" aria-hidden="true"></i>
                    Мастер создания технического задания
                </h2>
                <p class="edsys-wizard__subtitle">
                    Ответьте на несколько вопросов и получите готовое ТЗ с персональным коммерческим предложением от EDS
                </p>
            </div>

            <!-- Progress Bar -->
            <div class="edsys-wizard__progress">
                <div class="edsys-wizard__progress-bar" style="width: 12.5%"></div>
            </div>

            <!-- Step Navigation -->
            <div class="edsys-wizard__steps">
                <div class="edsys-wizard__step active" data-step="1">
                    <div class="edsys-wizard__step-number">1</div>
                    <span>Объект</span>
                </div>
                <div class="edsys-wizard__step" data-step="2">
                    <div class="edsys-wizard__step-number">2</div>
                    <span>Тип</span>
                </div>
                <div class="edsys-wizard__step" data-step="3">
                    <div class="edsys-wizard__step-number">3</div>
                    <span>Масштаб</span>
                </div>
                <div class="edsys-wizard__step" data-step="4">
                    <div class="edsys-wizard__step-number">4</div>
                    <span>Мощность</span>
                </div>
                <div class="edsys-wizard__step" data-step="5">
                    <div class="edsys-wizard__step-number">5</div>
                    <span>Оборудование</span>
                </div>
                <div class="edsys-wizard__step" data-step="6">
                    <div class="edsys-wizard__step-number">6</div>
                    <span>Требования</span>
                </div>
                <div class="edsys-wizard__step" data-step="7">
                    <div class="edsys-wizard__step-number">7</div>
                    <span>Сроки</span>
                </div>
                <div class="edsys-wizard__step" data-step="8">
                    <div class="edsys-wizard__step-number">8</div>
                    <span>Контакты</span>
                </div>
            </div>

            <!-- Form Content -->
            <div class="edsys-wizard__content">
                <form id="wizardForm">
                    <!-- Step 1: Object Type -->
                    <div class="edsys-wizard__step-content active" data-step="1">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-buildings" aria-hidden="true"></i>
                                Какой тип объекта вы оснащаете? <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Выберите основной тип объекта для корректного подбора оборудования
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="theater" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-mask-happy" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Театр</div>
                                    <div class="edsys-radio-card__subtitle">Стационарные театральные площадки с профессиональным оборудованием</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="concert" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-microphone-stage" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Концертная площадка</div>
                                    <div class="edsys-radio-card__subtitle">Концертные залы, клубы, арены для живых выступлений</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="studio" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-television" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Студия</div>
                                    <div class="edsys-radio-card__subtitle">Телестудии, радиостудии, студии звукозаписи</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="event" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-tent" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Выездное мероприятие</div>
                                    <div class="edsys-radio-card__subtitle">Фестивали, корпоративы, свадьбы, выставки</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="conference" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-presentation" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Конференц-зал</div>
                                    <div class="edsys-radio-card__subtitle">Деловые мероприятия, презентации, семинары</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="objectType" value="other" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-question" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Другое</div>
                                    <div class="edsys-radio-card__subtitle">Нестандартные объекты и специальные проекты</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Installation Type -->
                    <div class="edsys-wizard__step-content" data-step="2">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-wrench" aria-hidden="true"></i>
                                Тип инсталляции <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Определите характер установки оборудования для выбора оптимального решения
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="installationType" value="permanent" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-anchor" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Стационарная установка</div>
                                    <div class="edsys-radio-card__subtitle">Постоянное размещение в одном месте на длительный срок</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="installationType" value="temporary" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-clock" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Временная установка</div>
                                    <div class="edsys-radio-card__subtitle">Оборудование на время конкретного мероприятия</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="installationType" value="mobile" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-truck" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Мобильная система</div>
                                    <div class="edsys-radio-card__subtitle">Частые перемещения, туры, выездные концерты</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Scale -->
                    <div class="edsys-wizard__step-content" data-step="3">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-users" aria-hidden="true"></i>
                                Масштаб мероприятий <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Укажите примерное количество зрителей/участников для правильного расчета мощности
                            </p>
                            <div class="edsys-wizard__range-group">
                                <input type="range" class="edsys-wizard__range" id="audienceRange" name="audienceSize"
                                       min="1" max="6" step="1" value="3" required>
                                <div class="edsys-wizard__range-labels">
                                    <span>До 50</span>
                                    <span>50-200</span>
                                    <span>200-500</span>
                                    <span>500-1000</span>
                                    <span>1000-5000</span>
                                    <span>5000+</span>
                                </div>
                                <div class="edsys-wizard__range-value" id="audienceValue">200-500 человек</div>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-user-sound" aria-hidden="true"></i>
                                Количество исполнителей на сцене
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Сколько человек будет на сцене одновременно? Это влияет на количество необходимых подключений
                            </p>
                            <div class="edsys-wizard__range-group">
                                <input type="range" class="edsys-wizard__range" id="performersRange" name="performersCount"
                                       min="1" max="5" step="1" value="2">
                                <div class="edsys-wizard__range-labels">
                                    <span>1-5</span>
                                    <span>5-15</span>
                                    <span>15-30</span>
                                    <span>30-50</span>
                                    <span>50+</span>
                                </div>
                                <div class="edsys-wizard__range-value" id="performersValue">5-15 человек</div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Power Requirements -->
                    <div class="edsys-wizard__step-content" data-step="4">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                                Требуемая мощность <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Выберите диапазон общей мощности оборудования для правильного выбора дистрибьюторов
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerRequirement" value="low" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-battery-low" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">До 10 кВт</div>
                                    <div class="edsys-radio-card__subtitle">Небольшие мероприятия, конференции, малые залы</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerRequirement" value="medium" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-battery-medium" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">10-50 кВт</div>
                                    <div class="edsys-radio-card__subtitle">Средние площадки, клубы, театры</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerRequirement" value="high" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-battery-high" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">50-200 кВт</div>
                                    <div class="edsys-radio-card__subtitle">Крупные мероприятия, концертные залы</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerRequirement" value="very-high" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-battery-full" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Свыше 200 кВт</div>
                                    <div class="edsys-radio-card__subtitle">Фестивали, арены, крупные комплексы</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-plug" aria-hidden="true"></i>
                                Тип электропитания
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Какой тип электропитания доступен на объекте?
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerConnection" value="220v" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-plug" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">220В</div>
                                    <div class="edsys-radio-card__subtitle">Стандартная однофазная сеть</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerConnection" value="380v" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">380В</div>
                                    <div class="edsys-radio-card__subtitle">Трёхфазная промышленная сеть</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="powerConnection" value="unknown" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-question" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Не знаю</div>
                                    <div class="edsys-radio-card__subtitle">Требуется консультация специалиста</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Equipment -->
                    <div class="edsys-wizard__step-content" data-step="5">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-gear-six" aria-hidden="true"></i>
                                Необходимое оборудование <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Отметьте типы оборудования, которые потребуются для вашего проекта. Можно выбрать несколько вариантов.
                            </p>
                            <div class="edsys-wizard__toggle-group">
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="power-distributors" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Дистрибьюторы питания</div>
                                        <div class="edsys-toggle__subtitle">Силовые распределители для подключения оборудования с защитными функциями</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="dmx-equipment" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">DMX-оборудование</div>
                                        <div class="edsys-toggle__subtitle">Сплиттеры, конвертеры Art-Net, диммеры для управления световым оборудованием</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="winch-controls" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Пульты управления лебёдками</div>
                                        <div class="edsys-toggle__subtitle">Цифровые и аналоговые системы управления подъёмными механизмами</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="stage-boxes" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Лючки и коммутационные коробки</div>
                                        <div class="edsys-toggle__subtitle">Сценические, настольные и настенные решения для подключения</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="sequencers" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Секвенсоры</div>
                                        <div class="edsys-toggle__subtitle">Устройства последовательного включения оборудования с программируемыми задержками</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="equipment[]" value="cables-connectors" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Кабели и разъёмы</div>
                                        <div class="edsys-toggle__subtitle">Силовые и сигнальные кабели, профессиональные коннекторы</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 6: Requirements -->
                    <div class="edsys-wizard__step-content" data-step="6">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-shield-check" aria-hidden="true"></i>
                                Особые требования
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Отметьте дополнительные требования к проекту, которые нужно учесть при проектировании
                            </p>
                            <div class="edsys-wizard__toggle-group">
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="mobility" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Повышенная мобильность</div>
                                        <div class="edsys-toggle__subtitle">Лёгкий и быстрый монтаж/демонтаж, транспортировочные кейсы</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="safety" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Повышенные требования безопасности</div>
                                        <div class="edsys-toggle__subtitle">Дополнительные защитные системы, УЗО, контроль изоляции</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="weather-resistance" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Защита от внешних воздействий</div>
                                        <div class="edsys-toggle__subtitle">Влагозащита IP44/IP54, пылезащита для уличных мероприятий</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="remote-control" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Дистанционное управление</div>
                                        <div class="edsys-toggle__subtitle">Возможность управления оборудованием на расстоянии</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="monitoring" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Мониторинг параметров</div>
                                        <div class="edsys-toggle__subtitle">Контроль напряжения, тока, температуры в реальном времени</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="requirements[]" value="backup-power" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Резервное питание</div>
                                        <div class="edsys-toggle__subtitle">Автоматическое переключение на резервные источники питания</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-currency-rub" aria-hidden="true"></i>
                                Бюджетные рамки
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Укажите примерный бюджет на оборудование для подготовки оптимального предложения
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="budget" value="low" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-coins" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">До 500 000 ₽</div>
                                    <div class="edsys-radio-card__subtitle">Базовое оснащение малых объектов</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="budget" value="medium" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-bank" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">500 000 - 2 000 000 ₽</div>
                                    <div class="edsys-radio-card__subtitle">Стандартное профессиональное решение</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="budget" value="high" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-diamond" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">2 000 000 - 5 000 000 ₽</div>
                                    <div class="edsys-radio-card__subtitle">Профессиональное решение высокого класса</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="budget" value="premium" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-crown" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Свыше 5 000 000 ₽</div>
                                    <div class="edsys-radio-card__subtitle">Премиальное решение с индивидуальными разработками</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="budget" value="flexible" class="sr-only">
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-scales" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Гибкий бюджет</div>
                                    <div class="edsys-radio-card__subtitle">Рассмотрим различные варианты решений</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Step 7: Timeline -->
                    <div class="edsys-wizard__step-content" data-step="7">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-calendar" aria-hidden="true"></i>
                                Сроки реализации <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Когда планируется запуск проекта? Это поможет нам правильно спланировать работы
                            </p>
                            <div class="edsys-wizard__radio-group">
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="timeline" value="urgent" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-fire" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Срочно</div>
                                    <div class="edsys-radio-card__subtitle">В течение 1-2 недель, экспресс-поставка</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="timeline" value="fast" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-clock" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Быстро</div>
                                    <div class="edsys-radio-card__subtitle">В течение месяца, приоритетное выполнение</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="timeline" value="standard" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-calendar-check" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Стандартно</div>
                                    <div class="edsys-radio-card__subtitle">2-3 месяца, обычный срок выполнения</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                                <label class="edsys-radio-card" tabindex="0">
                                    <input type="radio" name="timeline" value="flexible" class="sr-only" required>
                                    <div class="edsys-radio-card__icon">
                                        <i class="ph ph-thin ph-calendar-dots" aria-hidden="true"></i>
                                    </div>
                                    <div class="edsys-radio-card__title">Гибко</div>
                                    <div class="edsys-radio-card__subtitle">Когда будет готово, нет срочности</div>
                                    <div class="edsys-radio-card__check">
                                        <i class="ph ph-thin ph-check" aria-hidden="true"></i>
                                    </div>
                                </label>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-wrench" aria-hidden="true"></i>
                                Ограничения по установке
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Есть ли особые ограничения по времени или условиям монтажа?
                            </p>
                            <div class="edsys-wizard__toggle-group">
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="installationLimitations[]" value="time-limited" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Ограниченное время монтажа</div>
                                        <div class="edsys-toggle__subtitle">Быстрая установка и демонтаж за короткий срок</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="installationLimitations[]" value="noise-restrictions" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Ограничения по шуму</div>
                                        <div class="edsys-toggle__subtitle">Тихий монтаж в определённые часы работы</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="installationLimitations[]" value="access-restrictions" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Ограниченный доступ</div>
                                        <div class="edsys-toggle__subtitle">Сложная доставка оборудования на объект</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="installationLimitations[]" value="operating-restrictions" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Работа в действующем объекте</div>
                                        <div class="edsys-toggle__subtitle">Без остановки основной деятельности объекта</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 8: Contacts -->
                    <div class="edsys-wizard__step-content" data-step="8">
                        <div class="edsys-wizard__question">
                            <h3 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-user" aria-hidden="true"></i>
                                Контактная информация <span class="edsys-wizard__required">*</span>
                            </h3>
                            <p class="edsys-wizard__question-subtitle">
                                Укажите ваши данные для связи и отправки готового ТЗ с коммерческим предложением
                            </p>
                            <div class="edsys-wizard__input-group">
                                <div class="edsys-wizard__input-item">
                                    <label for="contactName" class="edsys-wizard__input-label">
                                        Имя <span class="edsys-wizard__required">*</span>
                                    </label>
                                    <input type="text" id="contactName" name="contactName" class="edsys-wizard__input"
                                           placeholder="Ваше имя" required>
                                </div>
                                <div class="edsys-wizard__input-item">
                                    <label for="contactPhone" class="edsys-wizard__input-label">
                                        Телефон <span class="edsys-wizard__required">*</span>
                                    </label>
                                    <input type="tel" id="contactPhone" name="contactPhone" class="edsys-wizard__input"
                                           placeholder="+7 (999) 123-45-67" required>
                                </div>
                                <div class="edsys-wizard__input-item">
                                    <label for="contactEmail" class="edsys-wizard__input-label">
                                        Email <span class="edsys-wizard__required">*</span>
                                    </label>
                                    <input type="email" id="contactEmail" name="contactEmail" class="edsys-wizard__input"
                                           placeholder="your@email.com" required>
                                </div>
                                <div class="edsys-wizard__input-item">
                                    <label for="contactCompany" class="edsys-wizard__input-label">
                                        Компания/Организация
                                    </label>
                                    <input type="text" id="contactCompany" name="contactCompany" class="edsys-wizard__input"
                                           placeholder="Название организации">
                                </div>
                                <div class="edsys-wizard__input-item">
                                    <label for="contactPosition" class="edsys-wizard__input-label">
                                        Должность
                                    </label>
                                    <input type="text" id="contactPosition" name="contactPosition" class="edsys-wizard__input"
                                           placeholder="Ваша должность">
                                </div>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-chat-text" aria-hidden="true"></i>
                                Дополнительная информация
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Опишите особенности проекта, которые не были упомянуты в предыдущих вопросах
                            </p>
                            <textarea name="additionalInfo" class="edsys-wizard__input edsys-wizard__textarea"
                                      placeholder="Любая дополнительная информация о проекте, особые требования, пожелания к оборудованию..."></textarea>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-paperclip" aria-hidden="true"></i>
                                Приложить файлы
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Загрузите схемы, планы, чертежи или другие документы по проекту (до 5 файлов, максимум 10 МБ каждый)
                            </p>
                            <div class="edsys-file-upload" id="fileUploadArea">
                                <div class="edsys-file-upload__zone" id="fileDropZone">
                                    <div class="edsys-file-upload__content">
                                        <div class="edsys-file-upload__icon">
                                            <i class="ph ph-thin ph-cloud-arrow-up"></i>
                                        </div>
                                        <div class="edsys-file-upload__text">
                                            <p class="edsys-file-upload__primary">Перетащите файлы сюда или</p>
                                            <p class="edsys-file-upload__secondary">
                                                <button type="button" class="edsys-file-upload__button">выберите файлы</button>
                                            </p>
                                            <p class="edsys-file-upload__hint">
                                                Поддерживаются: PDF, DOC, DOCX, XLS, XLSX, JPG, PNG, DWG, DXF<br>
                                                Максимум 10 МБ на файл, до 5 файлов
                                            </p>
                                        </div>
                                    </div>
                                    <input type="file" id="fileInput" name="attachments[]" multiple
                                           accept=".pdf,.doc,.docx,.xls,.xlsx,.jpg,.jpeg,.png,.dwg,.dxf"
                                           style="display: none;">
                                </div>
                                <div class="edsys-file-upload__list" id="fileList"></div>
                            </div>
                        </div>

                        <div class="edsys-wizard__question">
                            <h4 class="edsys-wizard__question-title">
                                <i class="ph ph-thin ph-bell" aria-hidden="true"></i>
                                Способ получения результата
                            </h4>
                            <p class="edsys-wizard__question-subtitle">
                                Как вы хотите получить готовое ТЗ и коммерческое предложение?
                            </p>
                            <div class="edsys-wizard__toggle-group">
                                <div class="edsys-toggle active" tabindex="0">
                                    <input type="checkbox" name="deliveryMethod[]" value="email" class="sr-only" checked>
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Email</div>
                                        <div class="edsys-toggle__subtitle">Отправка PDF-файла на указанную почту</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                                <div class="edsys-toggle" tabindex="0">
                                    <input type="checkbox" name="deliveryMethod[]" value="phone-call" class="sr-only">
                                    <div class="edsys-toggle__content">
                                        <div class="edsys-toggle__title">Звонок менеджера</div>
                                        <div class="edsys-toggle__subtitle">Обсуждение ТЗ и предложения по телефону</div>
                                    </div>
                                    <div class="edsys-toggle__switch"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Navigation -->
            <div class="edsys-wizard__actions">
                <button type="button" class="edsys-wizard__btn edsys-wizard__btn--secondary" id="wizardPrevBtn" disabled>
                    <i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
                    Назад
                </button>
                <button type="button" class="edsys-wizard__btn edsys-wizard__btn--primary" id="wizardNextBtn">
                    Далее
                    <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                </button>
            </div>

            <!-- Loading State -->
            <div class="edsys-wizard__loading">
                <div class="edsys-wizard__loading-spinner"></div>
                <div class="edsys-wizard__loading-text">Генерируем ваше техническое задание...</div>
            </div>

            <!-- Results -->
            <div class="edsys-wizard__results">
                <div class="edsys-wizard__results-icon">
                    <i class="ph ph-thin ph-check-circle" aria-hidden="true"></i>
                </div>
                <h3 class="edsys-wizard__results-title">Техническое задание готово!</h3>
                <p class="edsys-wizard__results-subtitle">
                    Ваше персональное ТЗ сгенерировано и отправлено указанными способами.
                    Наши специалисты свяжутся с вами в ближайшее время для обсуждения деталей проекта.
                </p>
                <div class="edsys-wizard__results-actions">
                    <button type="button" class="edsys-wizard__btn edsys-wizard__btn--primary" id="wizardDownloadPDF">
                        <i class="ph ph-thin ph-download" aria-hidden="true"></i>
                        Скачать ТЗ (PDF)
                    </button>
                    <button type="button" class="edsys-wizard__btn edsys-wizard__btn--secondary" id="wizardCreateNew">
                        <i class="ph ph-thin ph-plus" aria-hidden="true"></i>
                        Создать новое ТЗ
                    </button>
                </div>
            </div>
        </div>
    </section>

    <!-- Advantages Section -->
    <section class="edsys-advantages" id="advantages">
        <div class="edsys-container">
            <div class="edsys-advantages__header">
                <h2 class="edsys-advantages__title">Почему выбирают EDS для индивидуальных проектов</h2>
            </div>

            <div class="edsys-advantages__grid">
                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Быстрая разработка</h3>
                    <p class="edsys-advantage-card__text">
                        От технического задания до готового прототипа всего 2-4 недели.
                        Собственное производство и склад комплектующих.
                    </p>
                </div>

                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-medal" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Более 9 лет опыта</h3>
                    <p class="edsys-advantage-card__text">
                        Более 2000 успешных индивидуальных проектов для ведущих театров,
                        концертных площадок и телекомпаний России.
                    </p>
                </div>

                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-certificate" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Сертификация и гарантии</h3>
                    <p class="edsys-advantage-card__text">
                        Все изделия соответствуют ГОСТ и международным стандартам.
                        Полная гарантия 24 месяца.
                    </p>
                </div>

                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-headset" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Полное сопровождение</h3>
                    <p class="edsys-advantage-card__text">
                        От консультации до ввода в эксплуатацию. Техническая поддержка
                        и обучение персонала включены.
                    </p>
                </div>

                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-gear-six" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Интеграция систем</h3>
                    <p class="edsys-advantage-card__text">
                        Совместимость с оборудованием ведущих производителей.
                        Открытые протоколы и стандарты.
                    </p>
                </div>

                <div class="edsys-advantage-card">
                    <div class="edsys-advantage-card__icon">
                        <i class="ph ph-thin ph-currency-rub" aria-hidden="true"></i>
                    </div>
                    <h3 class="edsys-advantage-card__title">Оптимальная стоимость</h3>
                    <p class="edsys-advantage-card__text">
                        Конкурентные цены за счёт собственного производства.
                        Гибкие условия оплаты для постоянных клиентов.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Consultation Section -->
    <section class="edsys-consultation" id="consultation">
        <div class="edsys-container">
            <div class="edsys-consultation__content">
                <h2 class="edsys-consultation__title">Нужна консультация специалиста?</h2>
                <p class="edsys-consultation__subtitle">
                    Обсудим ваш проект, ответим на вопросы и поможем с выбором оптимального решения
                </p>

                <form class="edsys-consultation__form" method="post">
                    <input type="text" name="consultationName" class="edsys-consultation__input"
                           placeholder="Ваше имя" required>
                    <input type="tel" name="consultationPhone" class="edsys-consultation__input"
                           placeholder="Телефон" required>
                    <button type="submit" class="edsys-consultation__btn">
                        <i class="ph ph-thin ph-phone" aria-hidden="true"></i>
                        Заказать звонок
                    </button>
                </form>

                <p class="edsys-consultation__privacy">
                    Нажимая кнопку, вы соглашаетесь с
                    <a href="/privacy-policy/" target="_blank">политикой конфиденциальности</a>
                </p>
            </div>
        </div>
    </section>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>