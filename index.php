<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("EDS | Силовые распределители и устройства коммутации (свет, звук, видео)");
?>


    <!-- Overlay -->
    <div class="edsys-overlay" id="overlay"></div>

    <!-- Hero Section -->
    <section class="edsys-hero edsys-container">
        <!-- Main Banners Grid -->
        <div class="edsys-banners-grid">
            <!-- Large Banner Carousel 1045x280 -->
            <div class="edsys-carousel edsys-banner--large" id="mainCarousel">
                <!-- Видео фон -->
                <div class="edsys-carousel__video-container">
                    <video class="edsys-carousel__video" autoplay muted loop playsinline poster="<?=DEFAULT_TEMPLATE_PATH?>/images/hero-vid.jpg">
                        <source src="<?=DEFAULT_TEMPLATE_PATH?>/vid/hero-vid.mp4" type="video/mp4">
                        <source src="<?=DEFAULT_TEMPLATE_PATH?>/vid/hero-vid.webm" type="video/webm">
                        <!-- Fallback изображение внутри <video> -->
                        <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/hero-vid.jpg" alt="Профессиональное оборудование EDS" loading="lazy">
                    </video>

                    <!-- Оверлей с затемнением и сеткой -->
                    <div class="edsys-carousel__video-overlay"></div>
                </div>

                <div class="edsys-carousel__wrapper">
                    <!-- Slide 1: Main Hero - Professional Power Distribution Systems -->
                    <div class="edsys-carousel__slide edsys-slide--power edsys-carousel__slide--active">
                        <div class="edsys-banner__badge">Российский производитель</div>
                        <div class="edsys-banner__content">
                            <h1 class="edsys-banner__title">Профессиональные системы распределения питания для сцен и
                                мероприятий</h1>
                            <p class="edsys-banner__subtitle">Производство и поставка сертифицированного оборудования с
                                2016 года</p>
                            <p class="edsys-banner__description edsys-banner__description-mob">Отправка в день оплаты • Гарантия 2 года • Собственное
                                производство в Калуге</p>
                        </div>
                        <div class="edsys-banner__cta">
                            <a href="/catalog/" class="edsys-banner__btn edsys-banner__btn--primary">Смотреть каталог</a>
                            <a href="/contacty/" class="edsys-banner__btn edsys-banner__btn--secondary">Получить консультацию</a>
                        </div>
                    </div>

                    <!-- Slide 2: Distributors -->
                    <div class="edsys-carousel__slide edsys-slide--energy">
                        <div class="edsys-banner__badge">Хит продаж</div>
                        <div class="edsys-banner__content">
                            <h2 class="edsys-banner__title">Дистрибьюторы питания EDS</h2>
                            <p class="edsys-banner__subtitle">Туровые и рэковые системы от 32А до 400А</p>
                            <p class="edsys-banner__description">Силовые распределители с защитными реле для концертов,
                                театров и телестудий. Соответствие ГОСТ и международным стандартам.</p>
                        </div>
                        <div class="edsys-banner__cta">
                            <a href="/cat/turovye/" class="edsys-banner__btn edsys-banner__btn--accent">Подобрать дистрибьютор</a>
                        </div>
                    </div>

                    <!-- Slide 3: DMX Equipment -->
                    <div class="edsys-carousel__slide edsys-slide--dmx">
                        <div class="edsys-banner__badge">Новинка 2025</div>
                        <div class="edsys-banner__content">
                            <h2 class="edsys-banner__title">DMX-оборудование для управления светом</h2>
                            <p class="edsys-banner__subtitle">Сплиттеры, конвертеры Art-Net, диммеры</p>
                            <p class="edsys-banner__description">Профессиональные DMX-сплиттеры с гальванической развязкой и
                                поддержкой RDM. Идеально для event-индустрии.</p>
                        </div>
                        <div class="edsys-banner__cta">
                            <a href="/cat/dmx-splitters/" class="edsys-banner__btn edsys-banner__btn--accent">Смотреть DMX-устройства</a>
                        </div>
                    </div>

                    <!-- Slide 4: Custom Solutions -->
                    <div class="edsys-carousel__slide edsys-slide--equipment">
                        <div class="edsys-banner__badge">14 дней* на разработку</div>
                        <div class="edsys-banner__content">
                            <h2 class="edsys-banner__title">Индивидуальные решения по вашему ТЗ</h2>
                            <p class="edsys-banner__subtitle">Разработка устройств под ваши задачи</p>
                            <p class="edsys-banner__description">Полный цикл от проектирования до внедрения. Бесплатные
                                расчёты и консультации инженеров.</p>
                        </div>
                        <div class="edsys-banner__cta">
                            <a href="/individualnye-resheniya/" class="edsys-banner__btn edsys-banner__btn--primary">Отправить техзадание</a>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <button class="edsys-carousel__nav edsys-carousel__nav--prev" data-carousel="mainCarousel"
                        data-direction="prev">
                    <i class="ph-thin ph-caret-left"></i>
                </button>
                <button class="edsys-carousel__nav edsys-carousel__nav--next" data-carousel="mainCarousel"
                        data-direction="next">
                    <i class="ph-thin ph-caret-right"></i>
                </button>
            </div>

            <!-- Small Banner Carousel 423x280 - Calculators -->
            <div class="edsys-carousel edsys-banner--small" id="sideCarousel">
                <div class="edsys-carousel__wrapper">
                    <!-- Calculator 1: Stage Equipment -->
                    <div class="edsys-carousel__slide edsys-slide--calc1 edsys-carousel__slide--active">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Калькулятор электрооборудования сцены</h3>
                            <p class="edsys-banner__subtitle">Вычисление общей мощности и тока техники</p>
                            <a href="/kalkulator-elektrooborudovaniya-sceny/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Рассчитать</a>
                        </div>
                    </div>

                    <!-- Calculator 2: Watts to Amps -->
                    <div class="edsys-carousel__slide edsys-slide--calc2">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Калькулятор перевода Ватт в Амперы</h3>
                            <p class="edsys-banner__subtitle">Вычисление ампер для заданной мощности.</p>
                            <a href="/onlayn-kalkulyator-perevoda-vatt-v-ampery/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Перевести</a>
                        </div>
                    </div>

                    <!-- Calculator 3: Current Calculation -->
                    <div class="edsys-carousel__slide edsys-slide--calc3">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Расчёт тока в цепи</h3>
                            <p class="edsys-banner__subtitle">Калькулятор тока для однофазной и трехфазной сети</p>
                            <a href="/raschet-toka-v-tsepi/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Рассчитать</a>
                        </div>
                    </div>

                    <!-- Calculator 4: Voltage Drop -->
                    <div class="edsys-carousel__slide edsys-slide--calc4">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Расчёт падения напряжения</h3>
                            <p class="edsys-banner__subtitle">Вычислить потери напряжения в линии по длине и нагрузке</p>
                            <a href="/raschet-padeniya-napryazheniya-v-linii/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Рассчитать</a>
                        </div>
                    </div>

                    <!-- Calculator 5: Wire Section -->
                    <div class="edsys-carousel__slide edsys-slide--calc5">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Расчёт сечения провода</h3>
                            <p class="edsys-banner__subtitle">Вычислить сечение кабеля по диаметру или числу витков</p>
                            <a href="/raschet-secheniya-provoda-po-diametru-ili-kolichestvu-vitkov/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Рассчитать</a>
                        </div>
                    </div>

                    <!-- Calculator 6: Wire Section by Voltage Loss -->
                    <div class="edsys-carousel__slide edsys-slide--calc6">
                        <div class="edsys-banner__content">
                            <h3 class="edsys-banner__title">Сечение по потере напряжения</h3>
                            <p class="edsys-banner__subtitle">Подбор сечения провода по потерям напряжения и нагрузке</p>
                            <a href="/kalkulyator-rascheta-secheniya-provoda-po-zadannoy-potere-napryazheniya/"
                               class="edsys-banner__btn edsys-banner__btn--primary">Рассчитать</a>
                        </div>
                    </div>
                </div>

                <!-- Navigation -->
                <button class="edsys-carousel__nav edsys-carousel__nav--prev" data-carousel="sideCarousel"
                        data-direction="prev">
                    <i class="ph-thin ph-caret-left"></i>
                </button>
                <button class="edsys-carousel__nav edsys-carousel__nav--next" data-carousel="sideCarousel"
                        data-direction="next">
                    <i class="ph-thin ph-caret-right"></i>
                </button>
            </div>
        </div>

        <!-- Service Blocks 272x72 с улучшенной бесшовной прокруткой -->
        <div class="edsys-services-container">
            <!-- Стрелки навигации (только для десктопа) -->
            <button class="edsys-services-nav edsys-services-nav--prev"
                    onclick="window.carouselModule?.scrollServices(-1)"
                    aria-label="Предыдущие услуги">
                <i class="ph-thin ph-caret-left"></i>
            </button>
            <button class="edsys-services-nav edsys-services-nav--next"
                    onclick="window.carouselModule?.scrollServices(1)"
                    aria-label="Следующие услуги">
                <i class="ph-thin ph-caret-right"></i>
            </button>

            <!-- Контейнер прокрутки с улучшенными атрибутами -->
            <div class="edsys-services"
                 id="servicesContainer"
                 role="list"
                 aria-label="Услуги и преимущества компании EDS">

                <!-- Оригинальные сервисные блоки -->
                <div class="edsys-service" role="listitem" data-service="delivery">
                    <div class="edsys-service__icon edsys-service__icon--delivery">
                        <i class="ph-thin ph-rocket-launch"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title"><a href="/oplata-i-dostavka/">Отгрузка в день оплаты</a></div>
                        <div class="edsys-service__subtitle">По России и СНГ</div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="custom">
                    <div class="edsys-service__icon edsys-service__icon--support">
                        <i class="ph-thin ph-gear"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title">Разработка по ТЗ</div>
                        <div class="edsys-service__subtitle"><a href="/individualnye-resheniya/">За 14 дней*</a></div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="warranty">
                    <div class="edsys-service__icon edsys-service__icon--warranty">
                        <i class="ph-thin ph-shield-check"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title"><a href="/o-kompanii/">Гарантия 2 года</a></div>
                        <div class="edsys-service__subtitle">На всё оборудование</div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="production">
                    <div class="edsys-service__icon edsys-service__icon--production">
                        <i class="ph-thin ph-factory"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title">Российский производитель</div>
                        <div class="edsys-service__subtitle"><a href="/o-kompanii/">Производство в Калуге</a></div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="certification">
                    <div class="edsys-service__icon edsys-service__icon--cert">
                        <i class="ph-thin ph-certificate"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title">Сертификация ГОСТ</div>
                        <div class="edsys-service__subtitle"><a href="/sertifikaty-eds/">Соответствие стандартам</a></div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="calculation">
                    <div class="edsys-service__icon edsys-service__icon--calc">
                        <i class="ph-thin ph-calculator"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title">Бесплатный расчёт</div>
                        <div class="edsys-service__subtitle"><a href="/contacty/">Подбор оборудования</a></div>
                    </div>
                </div>

                <div class="edsys-service" role="listitem" data-service="support">
                    <div class="edsys-service__icon edsys-service__icon--tech">
                        <i class="ph-thin ph-headset"></i>
                    </div>
                    <div class="edsys-service__divider"></div>
                    <div class="edsys-service__content">
                        <div class="edsys-service__title">Техподдержка</div>
                        <div class="edsys-service__subtitle"><a href="/contacty/">Консультации инженеров</a></div>
                    </div>
                </div>

            </div>
        </div>
    </section>



    <!-- Catalog -->

<?php
// Определяем базовый URL для категорий
$CATALOG_BASE_URL = "https://btx.edsy.ru";
?>

    <section class="edsys-nav-catalog-section edsys-container">
        <div class="edsys-nav-catalog__grid">

            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye-digital/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-cifrovye_150x150.jpeg"
                     alt="Пульты лебедочные цифровые">
                <div class="edsys-nav-catalog__card-title">Пульты лебедочные цифровые</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Современные пульты для автоматизированного управления лебёдками</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-analogovye-_150x150.jpeg"
                     alt="Пульты лебедочные аналоговые">
                <div class="edsys-nav-catalog__card-title">Пульты лебедочные аналоговые</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Простые и надёжные пульты для управления сценическим оборудованием</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/turovye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/turovye-distribyutory_150x150.jpeg"
                     alt="Туровые дистрибьюторы">
                <div class="edsys-nav-catalog__card-title">Туровые дистрибьюторы</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Переносные блоки распределения питания для гастролей</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/rjekovye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-distribyutory_150x150.jpeg"
                     alt="Рэковые дистрибьюторы">
                <div class="edsys-nav-catalog__card-title">Рэковые дистрибьюторы</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Распределители питания для монтажа в стойку</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/vvod-ot-63a/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/vvod-ot-63a-1.jpeg" alt="Ввод от 63А и выше">
                <div class="edsys-nav-catalog__card-title">Ввод от 63А</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Мощные распределители для больших мероприятий</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/ustrojstva-s-zashhitnymi-rele/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-s-zaschitnymi-rele_150x150.jpeg"
                     alt="Устройства с защитными реле">
                <div class="edsys-nav-catalog__card-title">Устройства с защитными реле</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Распределители с автоматической защитой при аварийных случаях</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/seriya-black-edition/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/seriya-black-edition_150x150.jpg"
                     alt="Серия Black Edition">
                <div class="edsys-nav-catalog__card-title">Серия Black Edition</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Эксклюзивная серия оборудования в премиальном чёрном исполнении</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/distribyutori-so-vstroennym-splitterom/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/distribyutory-so-vstroennym-splitterom_150x150.jpeg"
                     alt="Дистрибьюторы со встроенным сплиттером">
                <div class="edsys-nav-catalog__card-title">Дистрибьюторы со встроенным сплиттером</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Комбинированные устройства питания и управления светом</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/sekvensory/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/sekvensory_150x150.jpeg" alt="Секвенсоры">
                <div class="edsys-nav-catalog__card-title">Секвенсоры</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Устройства для управления порядком включения оборудования</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/dimmery/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dimmery_150x150.jpeg" alt="Диммеры">
                <div class="edsys-nav-catalog__card-title">Диммеры</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Устройства плавного регулирования яркости света</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/svitchery/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/svitchery_150x150.jpeg" alt="Свитчеры">
                <div class="edsys-nav-catalog__card-title">Свитчеры</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Управление коммутацией, распределением и переключением аудио- и видеосигналов</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-knopochnye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-upravleniya_150x150.jpeg"
                     alt="Пульты управления">
                <div class="edsys-nav-catalog__card-title">Пульты управления</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Центральные пульты для дистанционного управления системами</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/audio-devices/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/audio-izolyatory_150x150.jpeg" alt="Аудио изоляторы">
                <div class="edsys-nav-catalog__card-title">Аудио изоляторы</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Аудиоизоляторы, сумматоры, сплиттеры и тестеры для качественной обработки и тестирования аудиосигналов.</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/art-net-dmx-konvertery/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/art-net-dmx-konvertery_150x150.jpeg"
                     alt="Art-Net/DMX конвертеры">
                <div class="edsys-nav-catalog__card-title">Art-Net/DMX конвертеры</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Преобразователи сетевых протоколов управления светом</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/dmx-splitters/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dmx-splittery_150x150.jpeg" alt="DMX сплиттеры">
                <div class="edsys-nav-catalog__card-title">DMX сплиттеры</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Разветвители DMX сигнала для световых приборов</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/easylink/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-peredachi-signala_150x150.jpeg"
                     alt="Устройства передачи сигнала">
                <div class="edsys-nav-catalog__card-title">Устройства передачи сигнала</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Усилители и передатчики сигналов на большие расстояния</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/korobki-kommutatsionnye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kommutacionnye-korobki_150x150.jpeg"
                     alt="Коммутационные коробки">
                <div class="edsys-nav-catalog__card-title">Коммутационные коробки</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Сцена, свет, аудио — распределение и подключение различных интерфейсов</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-sczenicheskie/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/scenicheskie-lyuchki_150x150.jpeg"
                     alt="Сценические лючки">
                <div class="edsys-nav-catalog__card-title">Сценические лючки</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Потайные напольные решения для подключения сценического оборудования</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/nastolnye-lyuchki/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nastolnye-lyuchki_150x150.jpeg" alt="Настольные лючки">
                <div class="edsys-nav-catalog__card-title">Настольные лючки</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Удобный доступ к розеткам и различным разъемам на рабочих местах</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-nastennye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nasten.-lyuchok-—-200-px.png" alt="Настенные лючки">
                <div class="edsys-nav-catalog__card-title">Настенные лючки</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Компактные решения для монтажа в стену и скрытых подключений блоков розеток</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/razemy-i-komponenty/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/razemy_150x150.jpeg" alt="Разъёмы">
                <div class="edsys-nav-catalog__card-title">Разъёмы</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Профессиональные разъёмы всех популярных промышленных стандартов</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/paneli-rekovye/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-paneli_150x150.jpeg" alt="Рэковые панели">
                <div class="edsys-nav-catalog__card-title">Рэковые панели</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Монтажные панели для организации подключения в рэке</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/rekovye-aksessuary/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/reki-i-aksessuary_150x150.jpeg"
                     alt="Рэки и аксессуары">
                <div class="edsys-nav-catalog__card-title">Рэки и аксессуары</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Рэковые стойки и комплектующие для установки оборудования</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/kabelnaya-produktsiya/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kabelnaya-produkciya_150x150.jpeg"
                     alt="Кабельная продукция">
                <div class="edsys-nav-catalog__card-title">Кабельная продукция</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Силовые кабели с различными промышленными разъемами</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/signalnaya-kommutatsiya/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/signalnaya-kommutaciya_150x150.jpeg"
                     alt="Сигнальная коммутация">
                <div class="edsys-nav-catalog__card-title">Сигнальная коммутация</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Микрофонные и DMX удлинители с кабелями DRAKA и разъемами Neutrik для надежной передачи сигнала</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/sistemy-podvesa/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/strubciny-i-trosiki_150x150.jpeg"
                     alt="Струбцины и тросики">
                <div class="edsys-nav-catalog__card-title">Струбцины и тросики</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Крепёжные элементы и страховочные тросы</p>
                </div>
            </a>

            <a href="<?=$CATALOG_BASE_URL?>/cat/soputstvuyushchie-tovary/" class="edsys-nav-catalog__card">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/soputstvuyuschie-tovary_150x150.jpeg"
                     alt="Сопутствующие товары">
                <div class="edsys-nav-catalog__card-title">Сопутствующие товары</div>
                <div class="edsys-nav-catalog__card-overlay">
                    <p>Дополнительные материалы и расходники для монтажа</p>
                </div>
            </a>
        </div>
    </section>

    <!--New products-->
    <section class="edsys-new-goods-section">
        <header class="edsys-partners__header">
            <h2 class="edsys-partners__title edsys-section__title">Эксклюзивные новинки</h2>
            <p class="edsys-partners__subtitle edsys-section__subtitle">
                Современные технологии для концертов, театров и телестудий
            </p>
        </header>
        <div class="edsys-new-goods">
            <!-- DMX Splitter new-goods -->
            <article class="edsys-new-goods edsys-new-goods--dmx">
                <div class="edsys-new-goods__content">
                    <div class="edsys-new-goods__category">
                        <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                        Для профессионалов
                    </div>
                    <h2 class="edsys-new-goods__title">
                        DMX Splitter DS E1-4.3
                    </h2>
                    <p class="edsys-new-goods__subtitle">
                        Правильное разветвление DMX сигнала
                    </p>
                    <ul class="edsys-new-goods__features">
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-shield-check" aria-hidden="true"></i>
                            </div>
                            <span>Гальваническая развязка</span>
                        </li>
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-broadcast" aria-hidden="true"></i>
                            </div>
                            <span>Усилитель</span>
                        </li>
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-plug" aria-hidden="true"></i>
                            </div>
                            <span>4 выхода XLR 3-pin</span>
                        </li>
                    </ul>
                    <a href="/product/ds-e1-4-3/" class="edsys-new-goods__cta">
                        <span>Подробнее</span>
                        <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="edsys-new-goods__image-wrapper">
                    <img
                    <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dmx-splitter-ds-e1-4-3.png"
                            alt="MINI DMX Splitter DS E1-4.3"
                            class="edsys-new-goods__image"
                            width="300"
                            height="259"
                            loading="lazy"
                    />
                </div>
            </article>

            <!-- Floor Stand new-goods -->
            <article class="edsys-new-goods edsys-new-goods--stand">
                <div class="edsys-new-goods__content">
                    <div class="edsys-new-goods__category">
                        <i class="ph ph-thin ph-television" aria-hidden="true"></i>
                        Для телестудий
                    </div>
                    <h2 class="edsys-new-goods__title">
                        Стойка-суфлёр FST 1-4-1
                    </h2>
                    <p class="edsys-new-goods__subtitle">
                        Мобильная стойка для экранов 32-80″
                    </p>
                    <ul class="edsys-new-goods__features">
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-monitor" aria-hidden="true"></i>
                            </div>
                            <span>32-80″</span>
                        </li>
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-arrows-counter-clockwise" aria-hidden="true"></i>
                            </div>
                            <span>Наклон 20 – 80°</span>
                        </li>
                        <li class="edsys-new-goods__feature">
                            <div class="edsys-new-goods__feature-icon">
                                <i class="ph ph-thin ph-package" aria-hidden="true"></i>
                            </div>
                            <span>Складная</span>
                        </li>
                    </ul>
                    <a href="/product/fst-1-4-1/" class="edsys-new-goods__cta">
                        <span>Заказать</span>
                        <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                    </a>
                </div>

                <div class="edsys-new-goods__image-wrapper">
                    <img
                    <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/stoika-sufler.png"
                            alt="Напольная стойка-суфлёр FST 1-4-1"
                            class="edsys-new-goods__image"
                            width="200"
                            height="300"
                            loading="lazy"
                    />
                </div>
            </article>
        </div>
    </section>


    <!--Events bestsellers-->
    <section class="edsys-bestsellers edsys-container" id="bestsellers">
        <div class="edsys-bestsellers__container">
            <!-- Заголовок секции -->
            <div class="edsys-bestsellers__header">
                <div class="edsys-bestsellers__title-wrapper">
                    <h2 class="edsys-bestsellers__title">Хиты продаж для event-индустрии</h2>
                    <p class="edsys-bestsellers__subtitle">Легендарные дистрибьторы питания инсталла и проката</p>
                </div>
            </div>

            <!-- Карточки товаров -->
            <div class="edsys-bestsellers__wrapper">
                <div class="edsys-bestsellers__grid" id="bestsellersGrid">

                    <!-- Товар 1: ETC 462 ICB -->
                    <a href="/product/etc-462-icb/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Тренд</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/etc-462-icb-.jpg"
                                 alt="Туровый дистрибьютор питания ETC 462 ICB"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Туровый дистрибьютор питания ETC 462 ICB
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-cube"></i>
                                    <span>7 автоматов для надежной защиты</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-lightning"></i>
                                    <span>Мощный вход 32А для требовательного оборудования</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-rocket-launch"></i>
                                    <span>Быстрая установка на площадке</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">

                            </div>
                        </div>
                    </a>

                    <!-- Товар 2: ET 2-5 TR -->
                    <a href="/product/et-2-5-tr/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Компактный</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/et-2-5-tr-1.jpg"
                                 alt="Туровый дистрибьютор питания ET 2-5 TR"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Туровый дистрибьютор питания ET 2-5 TR
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-rocket-launch"></i>
                                    <span>Быстрое подключение с Neutrik PowerCon</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-lightning"></i>
                                    <span>Надёжное питание для концертного оборудования</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-shield-check"></i>
                                    <span>Компактный размер для гастрольных туров</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">

                            </div>
                        </div>
                    </a>

                    <!-- Товар 3: RC 7016 -->
                    <a href="/product/rc-7016/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Функциональный</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rc-7016-3.jpg"
                                 alt="Рэковый дистрибьютор питания RC 7016"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Рэковый дистрибьютор питания RC 7016
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-plug"></i>
                                    <span>16 розеток в компактном 3U корпусе</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-stack"></i>
                                    <span>Идеально для рэковых инсталляций</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-eye"></i>
                                    <span>Индикация включения для контроля</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">


                            </div>
                        </div>
                    </a>

                    <!-- Товар 4: ET 406.1 -->
                    <a href="/product/et-406-1/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Популярный</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/turovyj-distribyutor-et-406.1.jpg"
                                 alt="Туровый дистрибьютор питания ET 406.1"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Туровый дистрибьютор питания ET 406.1
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-lightning"></i>
                                    <span>Мощное питание 32А для больших нагрузок</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-arrows-out"></i>
                                    <span>Универсальные выходы CEE + Schuko</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-package"></i>
                                    <span>Оптимальный размер для транспортировки</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">


                            </div>
                        </div>
                    </a>

                    <!-- Товар 5: ER 408 -->
                    <a href="/product/er-408/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Рекомендуем</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/er-408-1.jpg"
                                 alt="Рэковый дистрибьютор питания ER 408"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Рэковый дистрибьютор питания ER 408
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-shield-check"></i>
                                    <span>Встроенные автоматы Legrand для защиты</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-activity"></i>
                                    <span>Тройная индикация фаз и включения</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-stack"></i>
                                    <span>Компактный 2U формат для рэка</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">

                            </div>
                        </div>
                    </a>

                    <!-- Товар 6: RC 708 -->
                    <a href="/product/rc-708/" class="edsys-bestsellers__card">
                        <div class="edsys-bestsellers__badge">Легкий</div>
                        <div class="edsys-bestsellers__image-wrapper">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/distribyutor-pitaniya-rekovyj-rc-708.jpg"
                                 alt="Рэковый дистрибьютор питания RC 708"
                                 width="280" height="280" loading="lazy"
                                 class="edsys-bestsellers__image">
                        </div>
                        <div class="edsys-bestsellers__content">
                            <h3 class="edsys-bestsellers__product-title">
                                Рэковый дистрибьютор питания RC 708
                            </h3>
                            <div class="edsys-bestsellers__specs">
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-feather"></i>
                                    <span>Сверхлёгкий корпус всего 2.5 кг</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-plug"></i>
                                    <span>8 розеток в компактном 2U формате</span>
                                </div>
                                <div class="edsys-bestsellers__benefit">
                                    <i class="ph ph-thin ph-eye"></i>
                                    <span>Контроль фазы для безопасной работы</span>
                                </div>
                            </div>
                            <div class="edsys-bestsellers__actions">


                            </div>
                        </div>
                    </a>

                </div>
            </div>

        </div>
    </section>

    <!--Brands-->
    <section class="edsys-partners" edsys-container aria-label="Наши партнеры">
        <div class="edsys-partners__container">
            <header class="edsys-partners__header">
                <h2 class="edsys-partners__title">Наши партнеры</h2>
                <p class="edsys-partners__subtitle">
                    Работаем только с ведущими мировыми производителями электротехнических компонентов, проверенными опытом и годами
                </p>
            </header>

            <div class="edsys-partners__carousel" role="img" aria-label="Логотипы компаний-партнеров">
                <div class="edsys-partners__track">
                    <!-- Первая группа логотипов -->
                    <div class="edsys-partners__group">
                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/PCE_Logo.svg"

                                 alt="PCE логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/legrand-logo.svg"
                                 alt="Legrand логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/schneider-logo.svg"
                                 alt="Schneider Electric логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/abb-logo.svg"
                                 alt="ABB логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/top-cable-logo.svg"
                                 alt="Top Cable логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/neutrik-logo.svg"
                                 alt="Neutrik логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>


                    </div>

                    <!-- Дублирующая группа логотипов для бесшовной прокрутки -->
                    <div class="edsys-partners__group">
                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/PCE_Logo.svg"
                                 alt="PCE логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/legrand-logo.svg"
                                 alt="Legrand логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/schneider-logo.svg"
                                 alt="Schneider Electric логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/abb-logo.svg"
                                 alt="ABB логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/top-cable-logo.svg"
                                 alt="Top Cable логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                        <div class="edsys-partners__logo">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/neutrik-logo.svg"
                                 alt="Neutrik логотип"
                                 width="120"
                                 height="40"
                                 loading="lazy" />
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </section>

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>