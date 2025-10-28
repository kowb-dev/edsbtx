<?php
/**
 * Страница "О компании EDS"
 * Использует глобальную дизайн-систему и следует принципам Bitrix
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// SEO настройки
$APPLICATION->SetTitle("О компании EDS - Electric Distribution Systems");
$APPLICATION->SetPageProperty("description", "Российская торгово-производственная компания EDS - производитель устройств распределения электроэнергии для сценического и концертного оборудования с 2016 года.");
$APPLICATION->SetPageProperty("keywords", "EDS, Electric Distribution Systems, дистрибьюторы питания, сценическое оборудование, производитель электрооборудования, распределение электроэнергии");
?>

    <link rel="stylesheet" href="style.css">

    <main class="edsys-about" role="main">
        <!-- Hero Section -->
        <section class="edsys-about__hero">
            <div class="edsys-about__container">
                <div class="edsys-about__hero-content">
                    <div class="edsys-about__hero-text">
                        <h1 class="edsys-about__title">О компании EDS</h1>
                        <p class="edsys-about__subtitle">Российская торгово-производственная компания Electric Distribution Systems, основанная в 2016 году</p>
                    </div>
                    <div class="edsys-about__hero-image">
                        <picture>
                            <img
                                    src="/images/headquarters.jpg"
                                    alt="Современный офис компании EDS Electric Distribution Systems с фирменной красной вывеской на фасаде здания"
                                    width="600"
                                    height="400"
                                    loading="eager"
                                    class="edsys-about__image"
                            >
                        </picture>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <section class="edsys-about__content">
            <div class="edsys-about__container">
                <div class="edsys-about__text-blocks">
                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-buildings" aria-hidden="true"></i>
                        </div>
                        <h2 class="edsys-about__block-title">О нашей компании</h2>
                        <p>Российская торгово-производственная компания <strong>Electric Distribution Systems (EDS)</strong>, основанная в 2016 году, является производителем устройств распределения электроэнергии. Компания осуществляет работу на всей территории Российской Федерации, а также в странах ближнего зарубежья.</p>
                    </article>

                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-certificate" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__block-title">Качество и надежность</h3>
                        <p>Колоссальный опыт в сфере проката и инсталляций, использование только лучших европейских компонентов, таких как <strong>PCE (Австрия)</strong>, <strong>Legrand (Франция)</strong>, <strong>Top Cable (Испания)</strong>, позволило нам создать продукцию, отвечающую международным стандартам качества. Между тем, наши цены имеют заметное преимущество по сравнению с другими производителями.</p>
                    </article>

                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__block-title">Специализация</h3>
                        <p>Наша компания занимается производством мобильных систем распределения электроэнергии для подключения потребителей питания шоу-техники: светового и видеопроекционного оборудования, звукового оборудования, LED – экранов, а также оборудования механики сцены и музыкальных инструментов – для оснащения объектов и проведения мероприятий различной степени сложности.</p>
                    </article>

                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-user-focus" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__block-title">Индивидуальный подход</h3>
                        <p>На протяжении всего периода существования мы сталкивались со множеством сложнейших технических задач, быстрое и качественное решение которых позволило завоевать любовь и доверие клиентов к нашему продукту, разработать ассортимент, удовлетворяющий потребностям заказчика. Если клиент не может найти необходимое ему устройство, инженеры компании EDS готовы, в максимально короткие сроки, разработать и предложить новые, нестандартные решения по индивидуальным техническим требованиям клиента.</p>
                    </article>

                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-package" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__block-title">Складские запасы</h3>
                        <p>Также на нашем складе всегда имеется постоянный запас дистрибьюторов, коммутации, силовых и сигнальных разъемов, готовых к срочной отправке заказчику.</p>
                    </article>

                    <article class="edsys-about__block">
                        <div class="edsys-about__block-icon">
                            <i class="ph ph-thin ph-headset" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__block-title">Поддержка клиентов</h3>
                        <p>Мы ценим наших клиентов и партнеров, стараемся найти индивидуальный подход к каждому, поэтому всегда готовы оказать бесплатную консультацию по любым техническим вопросам, касающимся работы, приобретения, монтажа и эксплуатации устройств, помочь подобрать максимально эффективный комплект оборудования для решения поставленных задач.</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Advantages -->
        <section class="edsys-about__advantages">
            <div class="edsys-about__container">
                <h2 class="edsys-about__section-title">Компания Electric Distribution Systems – это:</h2>
                <div class="edsys-about__advantages-grid">
                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-certificate" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Качественные комплектующие</h3>
                        <p class="edsys-about__advantage-text">Только лучшие европейские компоненты от PCE, Legrand, Top Cable</p>
                    </article>

                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-currency-rub" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Ценовое преимущество</h3>
                        <p class="edsys-about__advantage-text">Выгодные цены по сравнению с аналогичной продукцией</p>
                    </article>

                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-package" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Широкий ассортимент</h3>
                        <p class="edsys-about__advantage-text">Полная линейка оборудования для любых задач</p>
                    </article>

                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-wrench" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Высококачественная сборка</h3>
                        <p class="edsys-about__advantage-text">Соответствие международным стандартам качества</p>
                    </article>

                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-user-focus" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Индивидуальный подход</h3>
                        <p class="edsys-about__advantage-text">Персональное решение для каждого клиента</p>
                    </article>

                    <article class="edsys-about__advantage">
                        <div class="edsys-about__advantage-icon">
                            <i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
                        </div>
                        <h3 class="edsys-about__advantage-title">Оперативная разработка</h3>
                        <p class="edsys-about__advantage-text">Быстрое создание новых устройств по техническим заданиям</p>
                    </article>
                </div>
            </div>
        </section>

        <!-- Stats -->
        <section class="edsys-about__stats" role="region" aria-label="Статистика компании">
            <div class="edsys-about__container">
                <div class="edsys-about__stats-grid">
                    <div class="edsys-about__stat">
                        <div class="edsys-about__stat-number" aria-label="Год основания">2016</div>
                        <div class="edsys-about__stat-label">Год основания</div>
                    </div>
                    <div class="edsys-about__stat">
                        <div class="edsys-about__stat-number" aria-label="Более 9 лет опыта">9+</div>
                        <div class="edsys-about__stat-label">Лет опыта</div>
                    </div>
                    <div class="edsys-about__stat">
                        <div class="edsys-about__stat-number" aria-label="География работы: Россия и СНГ">РФ + СНГ</div>
                        <div class="edsys-about__stat-label">География работы</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <section class="edsys-about__cta">
            <div class="edsys-about__container">
                <div class="edsys-about__cta-content">
                    <h2 class="edsys-about__cta-title">EDS «Надежные распределители по выгодным ценам!»</h2>
                    <p class="edsys-about__cta-text">Готовы оказать бесплатную консультацию по любым техническим вопросам</p>
                    <div class="edsys-about__cta-buttons">
                        <a href="/catalog/" class="edsys-about__cta-button edsys-about__cta-button--primary" aria-label="Перейти к каталогу продукции EDS">
                            <i class="ph ph-thin ph-package" aria-hidden="true"></i>
                            <span>Каталог продукции</span>
                        </a>
                        <a href="/contacty/" class="edsys-about__cta-button edsys-about__cta-button--secondary" aria-label="Связаться с компанией EDS">
                            <i class="ph ph-thin ph-phone" aria-hidden="true"></i>
                            <span>Связаться с нами</span>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <!-- Structured Data для SEO -->
    <script type="application/ld+json">
        {
			"@context": "https://schema.org",
			"@type": "Organization",
			"name": "Electric Distribution Systems",
			"alternateName": "EDS",
			"url": "<?=SITE_SERVER_NAME?>",
            "foundingDate": "2016",
            "description": "Российская торгово-производственная компания, производитель устройств распределения электроэнергии для сценического и концертного оборудования",
            "address": {
                "@type": "PostalAddress",
                "addressCountry": "RU"
            },
            "contactPoint": {
                "@type": "ContactPoint",
                "contactType": "customer service",
                "availableLanguage": "Russian"
            },
            "founder": {
                "@type": "Organization",
                "name": "EDS"
            },
            "knowsAbout": [
                "Дистрибьюторы питания",
                "Сценическое оборудование",
                "Системы распределения электроэнергии",
                "Концертное оборудование"
            ]
        }
    </script>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>