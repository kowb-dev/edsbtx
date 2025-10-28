<?php
/**
 * Статья "АВР: что это такое"
 * Раздел "Полезно знать" - EDS
 *
 * @version 2.0 - Адаптирована под новую структуру
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

// Load helper functions
include_once($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-helpers.php");

// Article data
$arArticleData = edsysSanitizeArticleData([
	'TITLE' => 'АВР: что это такое',
	'SUBTITLE' => 'Автоматический ввод резерва для бесперебойного электроснабжения',
	'DESCRIPTION' => 'АВР (Автоматический ввод резерва) - вводно-коммутационное распределительное устройство для автоматического переключения между основным и резервным источниками питания. Принцип работы, устройство, применение.',
	'KEYWORDS' => 'АВР, автоматический ввод резерва, резервирование питания, переключение источников, контакторы, бесперебойное питание, ДГУ, генератор, EDS',
	'SLUG' => 'avr-chto-eto-takoe',
	'IMAGE' => '/upload/useful/avr-sistema.jpg',
	'DATE_PUBLISHED' => '2021-05-20T14:30:00+03:00',
	'DATE_MODIFIED' => '2021-10-18T12:15:00+03:00'
]);

// Generate SEO
edsysGenerateArticleSEO($arArticleData);
edsysGenerateArticleStructuredData($arArticleData);
edsysGenerateBreadcrumbsStructuredData($arArticleData);

// Load assets
edsysLoadArticleAssets([
	'version' => '1.0'
]);

// Get current URL for sharing
$currentUrl = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

// Navigation parameters
$arNavParams = [
	'CURRENT_ARTICLE' => $arArticleData['SLUG']
];

// Product categories for this article
$arCategoriesParams = [
	'CATEGORIES_TYPE' => 'avr_systems',
	'TITLE' => 'Решения EDS для систем АВР',
	'SUBTITLE' => 'Профессиональное оборудование автоматического ввода резерва'
];
?>

    <main class="edsys-article-page">

		<?php
		// Include navigation component
		$arParams = $arNavParams;
		include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/assets/components/sidebar/articles-navigation.php");
		?>

        <!-- Основной контент -->
        <div class="edsys-article-content">

			<?= edsysRenderBreadcrumbs($arArticleData['TITLE']) ?>

            <!-- Заголовок статьи -->
            <header class="edsys-article-header">
                <h1 class="edsys-article-title"><?= htmlspecialchars($arArticleData['TITLE']) ?></h1>
				<?php if (!empty($arArticleData['SUBTITLE'])): ?>
                    <p class="edsys-article-subtitle"><?= htmlspecialchars($arArticleData['SUBTITLE']) ?></p>
				<?php endif; ?>

				<?= edsysRenderSocialSharing($arArticleData['TITLE'], $currentUrl, ['whatsapp', 'telegram']) ?>
            </header>

            <!-- Главное изображение -->
            <div class="edsys-article-hero">
                <img src="<?= htmlspecialchars($arArticleData['IMAGE']) ?>"
                     alt="<?= htmlspecialchars($arArticleData['TITLE']) ?>"
                     class="edsys-article-hero__image"
                     width="807"
                     height="374"
                     loading="eager">
            </div>

            <!-- Контент статьи -->
            <article class="edsys-article-body">
                <div class="edsys-article-intro">
                    <p><strong>АВР</strong> – это вводно-коммутационное распределительное устройство, минимум, на два питающих ввода. Один ввод основной (от которого постоянно работает нагрузка) и другой ввод – резервный. От резервного ввода происходит питание нагрузки в случае «пропадания» напряжения на основном вводе.</p>

                    <p>По своему назначению ввод резерва схож с обеспечением бесперебойного электроснабжения и вся работа системы осуществляется полностью в <strong>автоматическом режиме</strong> без участия человека.</p>
                </div>

                <section class="edsys-content-section">
                    <h2>Что такое АВР и зачем он нужен?</h2>

                    <div class="edsys-definition-box">
                        <h3>Определение</h3>
                        <p><strong>Автоматический ввод резерва (АВР)</strong> — это система автоматического переключения электрической нагрузки с основного источника питания на резервный при исчезновении или недопустимом отклонении параметров напряжения основного источника.</p>
                    </div>

                    <div class="edsys-avr-applications">
                        <h3>Основные области применения АВР</h3>
                        <div class="edsys-applications-grid">
                            <div class="edsys-application-item">
                                <div class="edsys-application-icon">
                                    <i class="ph ph-thin ph-microphone-stage"></i>
                                </div>
                                <h4>Концертные площадки</h4>
                                <p>Непрерывное питание звукового и светового оборудования</p>
                            </div>
                            <div class="edsys-application-item">
                                <div class="edsys-application-icon">
                                    <i class="ph ph-thin ph-hospital"></i>
                                </div>
                                <h4>Медицинские учреждения</h4>
                                <p>Критически важное оборудование жизнеобеспечения</p>
                            </div>
                            <div class="edsys-application-item">
                                <div class="edsys-application-icon">
                                    <i class="ph ph-thin ph-buildings"></i>
                                </div>
                                <h4>Офисные центры</h4>
                                <p>Серверные, системы безопасности, лифты</p>
                            </div>
                            <div class="edsys-application-item">
                                <div class="edsys-application-icon">
                                    <i class="ph ph-thin ph-factory"></i>
                                </div>
                                <h4>Промышленные объекты</h4>
                                <p>Непрерывные технологические процессы</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Устройство и принцип работы АВР</h2>

                    <div class="edsys-technical-explanation">

                        <p>Самым распространенным типом устройства АВР являются <strong>контакторы</strong>.</p>

                        <p>Устройство на контакторах состоит из двух частей – один контактор подключает питание от основного ввода на нагрузку, другой контактор – от резервного ввода.</p>
                    </div>

                    <div class="edsys-danger-warning">
                        <h3>⚠️ Важная особенность</h3>
                        <p>Контакторы <strong>взаимосблокированы</strong> друг с другом. Это означает, что когда один контактор замкнут, то другой разомкнут и наоборот. Причем, включить оба контактора нельзя, т.к. между ними есть механическая и электрическая взаимоблокировки.</p>
                    </div>

                    <div class="edsys-blocking-explanation">
                        <h3>Зачем нужна взаимоблокировка?</h3>
                        <p>Если два питающих ввода включить встречно, то произойдет встречное включение (обычно, как вариант, это может привести к полному короткому замыканию). Этого необходимо избегать. Особенно когда это АВР для <strong>ДГУ (дизель-генераторной установки)</strong>.</p>

                        <div class="edsys-blocking-types">
                            <div class="edsys-blocking-type">
                                <h4>Механическая взаимоблокировка</h4>
                                <p>Специальное устройство, которое при монтаже контакторов устанавливается между ними и объединяет их таким образом, чтобы они не смогли включиться одновременно. Блокирует движущиеся части с силовыми контактами, позволяя включиться только одному.</p>
                            </div>
                            <div class="edsys-blocking-type">
                                <h4>Электрическая взаимоблокировка</h4>
                                <p>Схемотехническое решение, исключающее одновременную подачу управляющего сигнала на оба контактора через систему управления и блокировочные контакты.</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Технические характеристики АВР</h2>

                    <div class="edsys-tech-specs">
                        <h3>Время переключения</h3>
                        <p>Время переключения АВР на контакторах минимально короткое и может составлять до <strong>200-250 мс</strong>. Но, на самом деле, оно может отличаться в зависимости от номинального тока.</p>

                        <div class="edsys-timing-comparison">
                            <div class="edsys-timing-item">
                                <div class="edsys-timing-icon">
                                    <i class="ph ph-thin ph-lightning"></i>
                                </div>
                                <h4>Малые токи</h4>
                                <p>Меньший физический габарит → <strong>быстрее замыкание/размыкание</strong> контактов</p>
                                <div class="edsys-timing-value">≈ 150-200 мс</div>
                            </div>
                            <div class="edsys-timing-item">
                                <div class="edsys-timing-icon">
                                    <i class="ph ph-thin ph-lightning-slash"></i>
                                </div>
                                <h4>Большие токи</h4>
                                <p>Больший габарит → <strong>больше расстояния</strong> между контактами</p>
                                <div class="edsys-timing-value">≈ 250-400 мс</div>
                            </div>
                        </div>

                        <div class="edsys-technical-note">
                            <h4>Факторы, влияющие на время переключения:</h4>
                            <ul>
                                <li><strong>Номинальный ток</strong> контакторов</li>
                                <li><strong>Тип контакторов</strong> (электромагнитные, вакуумные)</li>
                                <li><strong>Настройки реле времени</strong> в системе управления</li>
                                <li><strong>Время срабатывания</strong> реле контроля напряжения</li>
                                <li><strong>Состояние контактов</strong> и качество обслуживания</li>
                            </ul>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Типы систем АВР</h2>

                    <div class="edsys-avr-types-grid">
                        <div class="edsys-avr-type-card">
                            <div class="edsys-avr-type-icon">
                                <i class="ph ph-thin ph-plugs-connected"></i>
                            </div>
                            <h3>АВР на контакторах</h3>
                            <div class="edsys-pros-cons">
                                <div class="edsys-pros">
                                    <h4>Преимущества:</h4>
                                    <ul>
                                        <li>Простота конструкции</li>
                                        <li>Надежность</li>
                                        <li>Низкая стоимость</li>
                                        <li>Простое обслуживание</li>
                                    </ul>
                                </div>
                                <div class="edsys-cons">
                                    <h4>Недостатки:</h4>
                                    <ul>
                                        <li>Механический износ контактов</li>
                                        <li>Время переключения 200-400 мс</li>
                                        <li>Необходимость обслуживания</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-avr-type-card">
                            <div class="edsys-avr-type-icon">
                                <i class="ph ph-thin ph-cpu"></i>
                            </div>
                            <h3>Статические АВР</h3>
                            <div class="edsys-pros-cons">
                                <div class="edsys-pros">
                                    <h4>Преимущества:</h4>
                                    <ul>
                                        <li>Быстрое переключение ≤ 10 мс</li>
                                        <li>Отсутствие механического износа</li>
                                        <li>Высокая точность</li>
                                        <li>Бесшумная работа</li>
                                    </ul>
                                </div>
                                <div class="edsys-cons">
                                    <h4>Недостатки:</h4>
                                    <ul>
                                        <li>Высокая стоимость</li>
                                        <li>Сложность схемы</li>
                                        <li>Потери мощности в рабочем режиме</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="edsys-avr-type-card">
                            <div class="edsys-avr-type-icon">
                                <i class="ph ph-thin ph-gear-six"></i>
                            </div>
                            <h3>Гибридные АВР</h3>
                            <div class="edsys-pros-cons">
                                <div class="edsys-pros">
                                    <h4>Преимущества:</h4>
                                    <ul>
                                        <li>Оптимальное время переключения</li>
                                        <li>Надежность контакторов</li>
                                        <li>Быстрота тиристоров</li>
                                        <li>Компромиссная стоимость</li>
                                    </ul>
                                </div>
                                <div class="edsys-cons">
                                    <h4>Недостатки:</h4>
                                    <ul>
                                        <li>Сложность управления</li>
                                        <li>Требует квалифицированного обслуживания</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Схема работы АВР</h2>

                    <div class="edsys-avr-workflow">
                        <h3>Пошаговый алгоритм работы системы АВР</h3>

                        <div class="edsys-step-by-step">
                            <div class="edsys-step-item">
                                <div class="edsys-step-number">1</div>
                                <div class="edsys-step-content">
                                    <h4>Нормальный режим</h4>
                                    <p>Контактор основного ввода замкнут, нагрузка питается от основной сети. Контактор резервного ввода разомкнут. Система управления контролирует параметры основной сети.</p>
                                </div>
                            </div>

                            <div class="edsys-step-item">
                                <div class="edsys-step-number">2</div>
                                <div class="edsys-step-content">
                                    <h4>Обнаружение неисправности</h4>
                                    <p>Реле контроля напряжения обнаруживает исчезновение или недопустимое отклонение параметров основной сети (напряжение, частота, фазность).</p>
                                </div>
                            </div>

                            <div class="edsys-step-item">
                                <div class="edsys-step-number">3</div>
                                <div class="edsys-step-content">
                                    <h4>Выдержка времени</h4>
                                    <p>Система выдерживает заданную временную задержку (обычно 0,5-3 секунды) для исключения ложных срабатываний при кратковременных провалах.</p>
                                </div>
                            </div>

                            <div class="edsys-step-item">
                                <div class="edsys-step-number">4</div>
                                <div class="edsys-step-content">
                                    <h4>Переключение на резерв</h4>
                                    <p>Контактор основного ввода размыкается, после чего замыкается контактор резервного ввода. Нагрузка переключается на резервный источник питания.</p>
                                </div>
                            </div>

                            <div class="edsys-step-item">
                                <div class="edsys-step-number">5</div>
                                <div class="edsys-step-content">
                                    <h4>Контроль восстановления</h4>
                                    <p>Система постоянно контролирует состояние основной сети. При восстановлении нормальных параметров происходит обратное переключение с выдержкой времени.</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="edsys-avr-diagram">
                        <img src="/upload/useful/avr-algorithm-diagram.jpg"
                             alt="Алгоритм работы системы АВР"
                             class="edsys-technical-image"
                             width="600"
                             height="400"
                             loading="lazy">
                        <p class="edsys-image-caption">Блок-схема алгоритма работы системы АВР</p>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Критерии выбора системы АВР</h2>

                    <div class="edsys-selection-criteria">
                        <div class="edsys-criteria-grid">
                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-lightning"></i>
                                </div>
                                <h4>Номинальная мощность</h4>
                                <p>Должна быть не менее суммарной мощности всех подключаемых потребителей с учетом коэффициента запаса 1,2-1,5</p>
                            </div>

                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-timer"></i>
                                </div>
                                <h4>Время переключения</h4>
                                <p>Критично для чувствительного оборудования. Для ИБП достаточно 200-400 мс, для критичных систем требуется ≤ 100 мс</p>
                            </div>

                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-checks"></i>
                                </div>
                                <h4>Контролируемые параметры</h4>
                                <p>Напряжение, частота, порядок чередования фаз, симметрия нагрузки по фазам, качество электроэнергии</p>
                            </div>

                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-shield-check"></i>
                                </div>
                                <h4>Защитные функции</h4>
                                <p>Защита от КЗ, перегрузки, обрыва фаз, неправильного чередования фаз, минимального и максимального напряжения</p>
                            </div>

                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-thermometer"></i>
                                </div>
                                <h4>Условия эксплуатации</h4>
                                <p>Температурный диапазон, влажность, степень защиты IP, механические воздействия, электромагнитная совместимость</p>
                            </div>

                            <div class="edsys-criteria-item">
                                <div class="edsys-criteria-icon">
                                    <i class="ph ph-thin ph-monitor"></i>
                                </div>
                                <h4>Система управления</h4>
                                <p>Ручное/автоматическое управление, индикация состояния, возможность дистанционного мониторинга и управления</p>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section edsys-highlight-section">
                    <h2 class="edsys-before-title">🔧 Рекомендации EDS по выбору АВР</h2>

                    <div class="edsys-eds-recommendations">
                        <p class="edsys-before-section">Компания EDS готова предложить профессиональные решения с системами автоматического ввода резерва для различных применений.</p>

                        <div class="edsys-recommended-products">
                          <a href="/product/r-5-4120-1-de/">
                              <div class="edsys-product-highlight">
                                <div class="edsys-product-icon">
                                    <i class="ph ph-thin ph-plugs-connected"></i>
                                </div>
                                <div class="edsys-product-info">
                                    <h4>R 5-4120.1DE</h4>
                                    <p>Дистрибьютор с 2-мя входами, в котором контроль сети осуществляется при помощи реле напряжения, а переключение между источниками питания производится при помощи двух контакторов с механической блокировкой.</p>
                                    <div class="edsys-product-features">
                                        <span class="edsys-feature-tag">Автоматическое переключение</span>
                                        <span class="edsys-feature-tag">Механическая блокировка</span>
                                        <span class="edsys-feature-tag">Реле контроля</span>
                                    </div>
                                </div>
                            </div>
                          </a>
                        </div>

                        <div class="edsys-application-tips">
                            <h3 class="edsys-before-subtitle">Практические советы по применению:</h3>

                            <div class="edsys-tips-grid">
                                <div class="edsys-tip-item">
                                    <h4>Для концертных площадок</h4>
                                    <p>Используйте АВР с минимальным временем переключения. Обязательно предусмотрите ручной режим управления для критических моментов выступления.</p>
                                </div>

                                <div class="edsys-tip-item">
                                    <h4>Для работы с генераторами</h4>
                                    <p>Обеспечьте синхронизацию частот перед переключением. Используйте системы с задержкой для стабилизации параметров генератора.</p>
                                </div>

                                <div class="edsys-tip-item">
                                    <h4>Для стационарных инсталляций</h4>
                                    <p>Предусмотрите системы мониторинга и удаленного управления. Регулярно проводите профилактическое обслуживание контакторов.</p>
                                </div>

                                <div class="edsys-tip-item">
                                    <h4>Для мобильных применений</h4>
                                    <p>Выбирайте компактные решения в защищенном исполнении. Обеспечьте простоту подключения и настройки на объекте.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Преимущества использования АВР</h2>

                    <div class="edsys-advantages-grid">
                        <div class="edsys-advantage-card">
                            <div class="edsys-advantage-icon">
                                <i class="ph ph-thin ph-clock"></i>
                            </div>
                            <h3>Непрерывность работы</h3>
                            <p>Обеспечивает бесперебойное электроснабжение критически важного оборудования без участия персонала</p>
                        </div>

                        <div class="edsys-advantage-card">
                            <div class="edsys-advantage-icon">
                                <i class="ph ph-thin ph-shield-check"></i>
                            </div>
                            <h3>Защита оборудования</h3>
                            <p>Предотвращает повреждение дорогостоящего оборудования от скачков напряжения и аварийных ситуаций</p>
                        </div>

                        <div class="edsys-advantage-card">
                            <div class="edsys-advantage-icon">
                                <i class="ph ph-thin ph-currency-circle-dollar"></i>
                            </div>
                            <h3>Экономическая эффективность</h3>
                            <p>Предотвращает финансовые потери от простоев, срыва мероприятий и потери репутации</p>
                        </div>

                        <div class="edsys-advantage-card">
                            <div class="edsys-advantage-icon">
                                <i class="ph ph-thin ph-robot"></i>
                            </div>
                            <h3>Автоматизация</h3>
                            <p>Полностью автоматическая работа без необходимости постоянного контроля со стороны персонала</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Заключение</h2>

                    <div class="edsys-conclusion">
                        <p>Таким образом, <strong>АВР просто незаменим</strong> в тех случаях, где необходима постоянная подача электроснабжения, но где нельзя положиться на основной источник питания.</p>

                        <p>Система с АВР будет отличным вариантом страховки от обесточивания и даст время найти неисправность и восстановить основной источник питания <strong>без простоя!</strong></p>

                        <div class="edsys-final-recommendation">
                            <h3>Выбор системы АВР</h3>
                            <p>При выборе системы АВР важно учитывать специфику применения, требования к времени переключения, номинальную мощность и условия эксплуатации. Компания EDS поможет подобрать оптимальное решение для ваших задач.</p>
                        </div>
                    </div>
                </section>

                <section class="edsys-content-section">
                    <h2>Связанные статьи</h2>
                    <div class="edsys-related-info">
                        <h3>Дополнительная информация</h3>
                        <p>Для более глубокого понимания систем электроснабжения рекомендуем изучить:</p>

                        <div class="edsys-related-articles">
                            <a href="/nemnogo-o-rele-kontrolya-napryazheniya/" class="edsys-related-article-link">
                                <div class="edsys-related-article-icon">
                                    <i class="ph ph-thin ph-gear"></i>
                                </div>
                                <div class="edsys-related-article-content">
                                    <h4>Немного о реле контроля напряжения</h4>
                                    <p>Защита от перепадов напряжения и контроль параметров сети</p>
                                </div>
                                <i class="ph ph-thin ph-arrow-right"></i>
                            </a>

                            <a href="/puskovye-toki-i-kak-s-nimi-borotsya/" class="edsys-related-article-link">
                                <div class="edsys-related-article-icon">
                                    <i class="ph ph-thin ph-lightning"></i>
                                </div>
                                <div class="edsys-related-article-content">
                                    <h4>Пусковые токи и как с ними бороться</h4>
                                    <p>Проблемы импульсных источников питания при работе с генераторами</p>
                                </div>
                                <i class="ph ph-thin ph-arrow-right"></i>
                            </a>

                            <a href="/zazemlenie-kak-eto-rabotaet/" class="edsys-related-article-link">
                                <div class="edsys-related-article-icon">
                                    <i class="ph ph-thin ph-shield-check"></i>
                                </div>
                                <div class="edsys-related-article-content">
                                    <h4>Заземление. Как это работает?</h4>
                                    <p>Обеспечение безопасности при работе с системами АВР</p>
                                </div>
                                <i class="ph ph-thin ph-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </section>
            </article>

			<?php
			// Include product categories component
			$arParams = $arCategoriesParams;
			include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/assets/components/blocks/product-categories.php");
			?>

			<?= edsysRenderArticleNavigation($arArticleData['SLUG']) ?>
        </div>
    </main>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>