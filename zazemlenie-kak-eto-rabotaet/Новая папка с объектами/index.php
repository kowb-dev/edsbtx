<?php
/**
 * Статья "Заземление. Как это работает?"
 * Раздел "Полезно знать" - EDS
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// SEO настройки
$APPLICATION->SetTitle("Заземление. Как это работает?");
$APPLICATION->SetPageProperty("description", "Заземление – устройство, предохраняющее человека от поражения электрическим током. Узнайте как работает заземление, чем отличается от зануления и как правильно обеспечить безопасность на площадке.");
$APPLICATION->SetPageProperty("keywords", "заземление, зануление, электробезопасность, защита от поражения током, заземляющие устройства, EDS");

// Canonical URL
$currentUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$APPLICATION->AddHeadString('<link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">');

// Open Graph
$APPLICATION->AddHeadString('<meta property="og:title" content="Заземление. Как это работает?">');
$APPLICATION->AddHeadString('<meta property="og:description" content="Заземление – устройство, предохраняющее человека от поражения электрическим током. Узнайте как работает заземление, чем отличается от зануления и как правильно обеспечить безопасность на площадке.">');
$APPLICATION->AddHeadString('<meta property="og:image" content="https://' . $_SERVER['HTTP_HOST'] . '/upload/useful/zazemlenie-kak-eto-rabotaet.webp">');
$APPLICATION->AddHeadString('<meta property="og:url" content="' . htmlspecialchars($currentUrl) . '">');
$APPLICATION->AddHeadString('<meta property="og:type" content="article">');

// Structured Data для статьи
$structuredData = [
	'@context' => 'https://schema.org',
	'@type' => 'Article',
	'headline' => 'Заземление. Как это работает?',
	'description' => 'Заземление – устройство, предохраняющее человека от поражения электрическим током. Узнайте как работает заземление, чем отличается от зануления и как правильно обеспечить безопасность на площадке.',
	'image' => 'https://' . $_SERVER['HTTP_HOST'] . '/upload/useful/zazemlenie-kak-eto-rabotaet.webp',
	'author' => [
		'@type' => 'Organization',
		'name' => 'EDS - Electric Distribution Systems'
	],
	'publisher' => [
		'@type' => 'Organization',
		'name' => 'EDS',
		'logo' => [
			'@type' => 'ImageObject',
			'url' => 'https://' . $_SERVER['HTTP_HOST'] . '/local/templates/edsy_main/images/logo.svg'
		]
	],
	'datePublished' => '2021-05-15T10:30:00+03:00',
	'dateModified' => '2021-10-12T16:45:00+03:00',
	'mainEntityOfPage' => [
		'@type' => 'WebPage',
		'@id' => $currentUrl
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');

// Breadcrumbs Schema
$breadcrumbsSchema = [
	'@context' => 'https://schema.org',
	'@type' => 'BreadcrumbList',
	'itemListElement' => [
		[
			'@type' => 'ListItem',
			'position' => 1,
			'name' => 'Главная',
			'item' => 'https://' . $_SERVER['HTTP_HOST'] . '/'
		],
		[
			'@type' => 'ListItem',
			'position' => 2,
			'name' => 'Полезно знать',
			'item' => 'https://' . $_SERVER['HTTP_HOST'] . '/polezno-znat/'
		],
		[
			'@type' => 'ListItem',
			'position' => 3,
			'name' => 'Заземление. Как это работает?'
		]
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($breadcrumbsSchema, JSON_UNESCAPED_UNICODE) . '</script>');

// Подключение стилей и скриптов
$APPLICATION->AddHeadString('<link rel="stylesheet" type="text/css" href="/zazemlenie-kak-eto-rabotaet/styles.css" />');
$APPLICATION->AddHeadString('<script type="text/javascript" src="/zazemlenie-kak-eto-rabotaet/scripts.js" defer></script>');

// Навигация по статьям
$articlesNavigation = [
	[
		'NAME' => 'Пусковые токи и как с ними бороться',
		'URL' => '/puskovye-toki-i-kak-s-nimi-borotsya/',
		'ACTIVE' => false,
		'ICON' => 'ph-lightning',
		'DESCRIPTION' => 'Проблемы импульсных источников питания'
	],
	[
		'NAME' => 'Заземление. Как это работает?',
		'URL' => '/zazemlenie-kak-eto-rabotaet/',
		'ACTIVE' => true,
		'ICON' => 'ph-shield-check',
		'DESCRIPTION' => 'Защита от поражения электрическим током'
	],
	[
		'NAME' => 'Разность потенциалов',
		'URL' => '/raznost-potentsialov/',
		'ACTIVE' => false,
		'ICON' => 'ph-pulse',
		'DESCRIPTION' => 'Проблемы в блоках питания оборудования'
	],
	[
		'NAME' => 'Сравнение КГтп-ХЛ и H07RN-F',
		'URL' => '/sravnenie-kgtp-hl-i-h07rn-f/',
		'ACTIVE' => false,
		'ICON' => 'ph-git-diff',
		'DESCRIPTION' => 'Выбор кабеля для профессионального оборудования'
	],
	[
		'NAME' => 'АВР: что это такое',
		'URL' => '/avr-chto-eto-takoe/',
		'ACTIVE' => false,
		'ICON' => 'ph-arrows-counter-clockwise',
		'DESCRIPTION' => 'Автоматический ввод резерва'
	],
	[
		'NAME' => 'Витая пара. Категории и применение',
		'URL' => '/vitaya-para-kategorii-i-primenenie/',
		'ACTIVE' => false,
		'ICON' => 'ph-network',
		'DESCRIPTION' => 'Кабели для передачи цифрового сигнала'
	],
	[
		'NAME' => 'Расчет потери напряжения',
		'URL' => '/raschet-poteri-napryazheniya/',
		'ACTIVE' => false,
		'ICON' => 'ph-calculator',
		'DESCRIPTION' => 'Оптимизация электропроводки'
	],
	[
		'NAME' => 'Как правильно пользоваться мультиметром',
		'URL' => '/kak-pravilno-polzovatsya-multimetrom/',
		'ACTIVE' => false,
		'ICON' => 'ph-device-mobile',
		'DESCRIPTION' => 'Измерительные приборы и их применение'
	],
	[
		'NAME' => 'Немного о реле контроля напряжения',
		'URL' => '/nemnogo-o-rele-kontrolya-napryazheniya/',
		'ACTIVE' => false,
		'ICON' => 'ph-gear',
		'DESCRIPTION' => 'Защита от перепадов напряжения'
	],
	[
		'NAME' => 'Неизвестное об известном. Unit',
		'URL' => '/unit/',
		'ACTIVE' => false,
		'ICON' => 'ph-ruler',
		'DESCRIPTION' => 'Единицы измерения стойкового оборудования'
	]
];

// Категории товаров
$productCategories = [
	[
		'name' => 'Устройства с защитными реле',
		'description' => 'Профессиональные дистрибьюторы с встроенной защитой от перегрузок и контролем заземления',
		'url' => 'https://btx.edsy.ru/cat/ustrojstva-s-zashhitnymi-rele/',
		'icon' => 'ph-shield-check',
		'color' => 'circuit',
		'features' => ['Контроль заземления', 'Защитные реле', 'Мониторинг параметров']
	],
	[
		'name' => 'Кабельная продукция',
		'description' => 'Заземляющие провода и кабели для обеспечения безопасности электрических установок',
		'url' => 'https://btx.edsy.ru/cat/kabelnaya-produktsiya/',
		'icon' => 'ph-plugs',
		'color' => 'wire',
		'features' => ['Заземляющие провода', 'Медные жилы', 'Профессиональное качество']
	],
	[
		'name' => 'Коммутационные коробки',
		'description' => 'Распределительные устройства с заземляющими контактами для безопасной коммутации',
		'url' => 'https://btx.edsy.ru/cat/korobki-kommutatsionnye/',
		'icon' => 'ph-square-half',
		'color' => 'voltage',
		'features' => ['Заземляющие контакты', 'Безопасная коммутация', 'Защита от КЗ']
	],
	[
		'name' => 'Туровые дистрибьюторы',
		'description' => 'Мобильные решения для распределения питания с надежным заземлением для концертного оборудования',
		'url' => 'https://btx.edsy.ru/cat/turovye/',
		'icon' => 'ph-suitcase',
		'color' => 'spark',
		'features' => ['Встроенное заземление', 'Мобильность', 'Концертное применение']
	]
];
?>

    <main class="article-page">
        <!-- Навигация по статьям для Desktop -->
        <aside class="articles-navigation" id="articlesNav">
            <div class="articles-navigation__header">
                <h3 class="articles-navigation__title">
                    <i class="ph ph-thin ph-book-open"></i>
                    Полезно знать
                </h3>
            </div>
            <nav class="articles-navigation__list">
				<?php foreach ($articlesNavigation as $article): ?>
                    <a href="<?= htmlspecialchars($article['URL']) ?>"
                       class="articles-navigation__item <?= $article['ACTIVE'] ? 'active' : '' ?>"
						<?= $article['ACTIVE'] ? 'aria-current="page"' : '' ?>>
                        <i class="ph ph-thin <?= htmlspecialchars($article['ICON']) ?>"></i>
                        <div class="articles-navigation__content">
                            <span class="articles-navigation__name"><?= htmlspecialchars($article['NAME']) ?></span>
                            <span class="articles-navigation__desc"><?= htmlspecialchars($article['DESCRIPTION']) ?></span>
                        </div>
                        <i class="ph ph-thin ph-arrow-right articles-navigation__arrow"></i>
                    </a>
				<?php endforeach; ?>
            </nav>
        </aside>

        <!-- Мобильная кнопка навигации -->
        <button class="mobile-nav-btn" id="mobileNavBtn" aria-label="Открыть навигацию по статьям">
            <i class="ph ph-thin ph-list"></i>
        </button>

        <!-- Мобильное меню -->
        <div class="mobile-nav-menu" id="mobileNavMenu">
            <div class="mobile-nav-header">
                <h3 class="mobile-nav-title">
                    <i class="ph ph-thin ph-book-open"></i>
                    Полезно знать
                </h3>
                <button class="mobile-nav-close" id="mobileNavClose" aria-label="Закрыть меню">
                    <i class="ph ph-thin ph-x"></i>
                </button>
            </div>
            <nav class="mobile-nav-list">
				<?php foreach ($articlesNavigation as $article): ?>
                    <a href="<?= htmlspecialchars($article['URL']) ?>"
                       class="mobile-nav-item <?= $article['ACTIVE'] ? 'active' : '' ?>"
						<?= $article['ACTIVE'] ? 'aria-current="page"' : '' ?>>
                        <i class="ph ph-thin <?= htmlspecialchars($article['ICON']) ?>"></i>
                        <div class="mobile-nav-content">
                            <span class="mobile-nav-name"><?= htmlspecialchars($article['NAME']) ?></span>
                            <span class="mobile-nav-desc"><?= htmlspecialchars($article['DESCRIPTION']) ?></span>
                        </div>
                    </a>
				<?php endforeach; ?>
            </nav>
        </div>

        <!-- Оверлей для мобильного меню -->
        <div class="mobile-nav-overlay" id="mobileNavOverlay"></div>

        <!-- Основной контент -->
        <div class="article-content">
            <!-- Breadcrumbs -->
            <nav class="breadcrumbs" aria-label="Навигация">
                <ol class="breadcrumbs__list">
                    <li class="breadcrumbs__item">
                        <a href="/" class="breadcrumbs__link">Главная</a>
                        <i class="ph ph-thin ph-caret-right"></i>
                    </li>
                    <li class="breadcrumbs__item">
                        <a href="/polezno-znat/" class="breadcrumbs__link">Полезно знать</a>
                        <i class="ph ph-thin ph-caret-right"></i>
                    </li>
                    <li class="breadcrumbs__item">
                        <span class="breadcrumbs__current">Заземление. Как это работает?</span>
                    </li>
                </ol>
            </nav>

            <!-- Заголовок статьи -->
            <header class="article-header">
                <h1 class="article-title">Заземление. Как это работает?</h1>

                <!-- Кнопки действий -->
                <div class="article-actions">
                    <span class="action-btn-title">Поделиться:</span>
                    <a href="https://wa.me/?text=Заземление. Как это работает? - EDS%0A<?= urlencode($currentUrl) ?>"
                       class="action-btn action-btn--whatsapp"
                       target="_blank"
                       rel="noopener"
                       title="Поделиться в WhatsApp">
                        <i class="ph ph-thin ph-whatsapp-logo"></i>
                        <span>WhatsApp</span>
                    </a>
                    <a href="https://t.me/share/url?url=<?= urlencode($currentUrl) ?>&text=<?= urlencode('Заземление. Как это работает? - EDS') ?>"
                       class="action-btn action-btn--telegram"
                       target="_blank"
                       rel="noopener"
                       title="Поделиться в Telegram">
                        <i class="ph ph-thin ph-telegram-logo"></i>
                        <span>Telegram</span>
                    </a>
                </div>
            </header>

            <!-- Главное изображение -->
            <div class="article-hero">
                <img src="/upload/useful/zazemlenie-kak-eto-rabotaet.webp"
                     alt="Заземление. Как это работает?"
                     class="article-hero__image"
                     width="784"
                     height="784"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="article-body">
                <div class="article-intro">
                    <p><strong>Заземление</strong> – устройство, предохраняющее человека от поражения электрическим током. Благодаря использованию различных заземляющих приспособлений удается избежать жертв на производстве и в быту. Собственно в этом его основное предназначение.</p>
                </div>

                <section class="content-section">
                    <h2>Что такое заземление?</h2>
                    <p>Конструктивно это чаще всего обычный кусок провода, который одним концом соединён с корпусом электрического прибора, а другим концом с землей, откуда и название.</p>

                    <div class="definition-box">
                        <h3>Принцип работы</h3>
                        <p>Суть заземления проста – служить проводником с минимальным сопротивлением для отвода опасного тока в землю.</p>
                    </div>
                </section>

                <section class="content-section">
                    <h2>Как работает заземление?</h2>
                    <p>Допустим, случилась аварийная ситуация – пробило фазу на корпус и он оказался под напряжением, а соответственно и вся конструкция (ферма, сцена) на которой располагается данный прибор.</p>

                    <div class="danger-scenario">
                        <h3>⚠️ Опасная ситуация</h3>
                        <p>Человек ничего не подозревая может дотронуться до корпуса или конструкции, после чего его ударит током. Этого конечно может и не произойти, если ноги сухие, и обувь у вас с хорошей резиновой подошвой т.е. если вы полностью изолированы от земли.</p>
                    </div>

                    <div class="protection-principle">
                        <h3>Как заземление защищает</h3>
                        <p>Для того чтобы этого не произошло, приборы должны быть заземлены. Тогда если человек коснётся корпуса, то ток пройдет не через него, а через заземление.</p>

                        <div class="resistance-comparison">
                            <div class="resistance-item">
                                <span class="resistance-value">Несколько кОм</span>
                                <span class="resistance-label">Сопротивление кожи человека</span>
                            </div>
                            <div class="vs-divider">VS</div>
                            <div class="resistance-item">
                                <span class="resistance-value">5-10 Ом</span>
                                <span class="resistance-label">Сопротивление заземляющего проводника</span>
                            </div>
                        </div>

                        <div class="conclusion-highlight">
                            <p>Выходит, что току в <strong>тысячу раз проще</strong> пройти по проводу и уйти в землю, чем пройти через человека.</p>
                        </div>
                    </div>
                </section>

                <section class="content-section">
                    <h2>В чем разница между заземлением и занулением?</h2>

                    <div class="comparison-grid">
                        <div class="comparison-card">
                            <h3>Заземление</h3>
                            <div class="comparison-icon">
                                <i class="ph ph-thin ph-shield-check"></i>
                            </div>
                            <p>Соединение корпуса прибора с землей через заземляющий проводник.</p>
                            <ul>
                                <li>Отводит ток утечки в землю</li>
                                <li>Снижает потенциал корпуса</li>
                                <li>Работает независимо от сети</li>
                            </ul>
                        </div>

                        <div class="comparison-card">
                            <h3>Зануление</h3>
                            <div class="comparison-icon">
                                <i class="ph ph-thin ph-lightning"></i>
                            </div>
                            <p>Соединение корпуса приемника электроэнергии с нулевым проводом.</p>
                            <ul>
                                <li>Создает короткое замыкание</li>
                                <li>Срабатывает автоматическая защита</li>
                                <li>Отключает питание при аварии</li>
                            </ul>
                        </div>
                    </div>

                    <div class="technical-note">
                        <h4>Принцип работы зануления</h4>
                        <p>Если говорить простым языком, то зануление – это соединение корпуса приемника электроэнергии с нулем. <strong>Ноль – это провод, имеющий нулевой потенциал и идущий из трансформатора.</strong></p>

                        <p>Зануление работает так: если на корпус приемника попадает провод под напряжением, то он через корпус замыкается на ноль, что вызывает короткое замыкание. Защита автоматически срабатывает и отключает питание.</p>
                    </div>
                </section>

                <section class="content-section highlight-section">
                    <h2 class="edsys-before-title">🔧 Практические рекомендации EDS</h2>

                    <div class="practical-advice">
                        <p class="before-section">К сожалению, на многих площадках нет заземления. Для этого мы рекомендуем сделать хотя бы минимальное самое простое заземление.</p>

                        <div class="instruction-steps">
                            <h3 class="edsys-before-subtitle">Как сделать простое заземление на площадке:</h3>

                            <div class="step-by-step">
                                <div class="step-item">
                                    <div class="step-number">1</div>
                                    <div class="step-content">
                                        <h4>Подготовка заземлителя</h4>
                                        <p>Необходимо взять какой-нибудь металлический кол и забить его в землю, хотя бы на <strong>пол метра – метр</strong>.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">2</div>
                                    <div class="step-content">
                                        <h4>Подключение провода</h4>
                                        <p>От заземлителя бросить провод, сечением минимум <strong>6-10 квадратных миллиметров</strong> и подмотать его под специальный болт с основной раздачи.</p>
                                    </div>
                                </div>

                                <div class="step-item">
                                    <div class="step-number">3</div>
                                    <div class="step-content">
                                        <h4>Проверка системы</h4>
                                        <p>Все розетки изделий компании EDS заземлены на корпус. После данных действий при использовании правильной коммутации, все ваши приборы будут заземлены.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="eds-products-note">
                            <h4>Наши устройства</h4>
                            <p>На таких наших устройствах как <strong>R531, R552</strong>, а так же на всех рэковых изделиях присутствует специальный болтик для подмотки заземляющего провода.</p>
                        </div>

                        <div class="safety-result">
                            <h4>Результат</h4>
                            <p>И соответственно после данных действий Вы, организаторы и артисты, будете в <strong>полной безопасности</strong> 😊</p>
                        </div>
                    </div>
                </section>

                <section class="content-section">
                    <h2>Дополнительные опасности</h2>
                    <div class="additional-danger">
                        <h3>Разность потенциалов</h3>
                        <p>Так же не стоит забывать об опасности возникновения разности потенциалов между корпусами приборов, от этого тоже защищает заземление, но об этом уже в следующей нашей статье.</p>

                        <a href="/raznost-potentsialov/" class="next-article-link">
                            <i class="ph ph-thin ph-arrow-right"></i>
                            Читать про разность потенциалов
                        </a>
                    </div>
                </section>

                <section class="content-section technical-requirements">
                    <h2>Технические требования к заземлению</h2>

                    <div class="requirements-grid">
                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="ph ph-thin ph-ruler"></i>
                            </div>
                            <h4>Сечение проводника</h4>
                            <p>Минимум <strong>6-10 мм²</strong> для медного провода</p>
                        </div>

                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="ph ph-thin ph-gauge"></i>
                            </div>
                            <h4>Сопротивление заземления</h4>
                            <p>Не более <strong>4 Ом</strong> для электроустановок до 1000В</p>
                        </div>

                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="ph ph-thin ph-shovel"></i>
                            </div>
                            <h4>Глубина заземлителя</h4>
                            <p>Минимум <strong>0,5-1 метр</strong> в грунт</p>
                        </div>

                        <div class="requirement-item">
                            <div class="requirement-icon">
                                <i class="ph ph-thin ph-wrench"></i>
                            </div>
                            <h4>Соединения</h4>
                            <p>Надежные <strong>болтовые соединения</strong> с антикоррозийной обработкой</p>
                        </div>
                    </div>
                </section>
            </article>

            <!-- Блок категорий товаров -->
            <section class="product-categories">
                <div class="categories-header">
                    <h2 class="categories-title">Решения EDS для безопасного заземления</h2>
                    <p class="categories-subtitle">Профессиональное оборудование с надежными заземляющими контактами</p>
                </div>

                <div class="categories-grid">
					<?php foreach ($productCategories as $category): ?>
                        <a href="<?= htmlspecialchars($category['url']) ?>"
                           class="category-card"
                           data-color="<?= htmlspecialchars($category['color']) ?>"
                           target="_blank"
                           rel="noopener">
                            <div class="category-icon">
                                <i class="ph ph-thin <?= htmlspecialchars($category['icon']) ?>"></i>
                            </div>
                            <div class="category-content">
                                <h3 class="category-name"><?= htmlspecialchars($category['name']) ?></h3>
                                <p class="category-description"><?= htmlspecialchars($category['description']) ?></p>
                                <ul class="category-features">
									<?php foreach ($category['features'] as $feature): ?>
                                        <li><?= htmlspecialchars($feature) ?></li>
									<?php endforeach; ?>
                                </ul>
                            </div>
                            <div class="category-arrow">
                                <i class="ph ph-thin ph-arrow-right"></i>
                            </div>
                        </a>
					<?php endforeach; ?>
                </div>
            </section>

            <!-- Навигация по статьям -->
            <nav class="article-navigation">
                <a href="/puskovye-toki-i-kak-s-nimi-borotsya/" class="article-nav-link article-nav-prev">
                    <div class="article-nav-direction">
                        <i class="ph ph-thin ph-arrow-left"></i>
                        <span>Предыдущая статья</span>
                    </div>
                    <div class="article-nav-title">Пусковые токи и как с ними бороться</div>
                </a>

                <a href="/polezno-znat/" class="article-nav-all">
                    <i class="ph ph-thin ph-list"></i>
                    <span>Все статьи</span>
                </a>

                <a href="/raznost-potentsialov/" class="article-nav-link article-nav-next">
                    <div class="article-nav-direction">
                        <span>Следующая статья</span>
                        <i class="ph ph-thin ph-arrow-right"></i>
                    </div>
                    <div class="article-nav-title">Разность потенциалов</div>
                </a>
            </nav>
        </div>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>