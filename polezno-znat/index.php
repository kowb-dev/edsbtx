<?php
/**
 * Страница статей и полезной информации EDS
 * /stati-tablitsy-nagruzok/index.php
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Статьи и полезная информация");
$APPLICATION->SetPageProperty("description", "Профессиональные статьи и полезная информация от экспертов EDS по электрооборудованию, распределению электроэнергии и сценическому оборудованию");
$APPLICATION->SetPageProperty("keywords", "статьи EDS, электрооборудование, сценическое оборудование, полезная информация, профессиональные решения");

// Add CSS - include navigation styles
$APPLICATION->SetAdditionalCSS("/local/templates/" . SITE_TEMPLATE_ID . "/css/useful-info-navigation.css");
$APPLICATION->SetAdditionalCSS("/polezno-znat/assets/css/articles.css");

// Add JS - include navigation script
$APPLICATION->AddHeadString('<script defer src="/local/templates/' . SITE_TEMPLATE_ID . '/js/useful-info-navigation.js"></script>');

// SEO and Schema markup
$APPLICATION->AddHeadString('<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "CollectionPage",
  "name": "Статьи и полезная информация EDS",
  "description": "Профессиональные статьи по электрооборудованию и сценическому оборудованию",
  "url": "https://edsy.ru/stati-tablitsy-nagruzok/",
  "breadcrumb": {
    "@type": "BreadcrumbList",
    "itemListElement": [
      {
        "@type": "ListItem",
        "position": 1,
        "name": "Главная",
        "item": "https://edsy.ru/"
      },
      {
        "@type": "ListItem",
        "position": 2,
        "name": "Статьи",
        "item": "https://edsy.ru/stati-tablitsy-nagruzok/"
      }
    ]
  }
}
</script>');

// Navigation array for sidebar
$arNavigation = [
	[
		'NAME' => 'Статьи',
		'URL' => '/polezno-znat/',
		'ACTIVE' => true,
		'ICON' => 'ph-article',
		'DESCRIPTION' => 'Профессиональные статьи и руководства'
	],
	[
		'NAME' => 'Таблицы токовых нагрузок медных кабелей',
		'URL' => '/stati-tablitsy-nagruzok/',
		'ACTIVE' => false,
		'ICON' => 'ph-table',
		'DESCRIPTION' => 'Справочные таблицы нагрузок'
	],
	[
		'NAME' => 'Схемы распайки кабелей',
		'URL' => '/shemy-raspajki-kabelej/',
		'ACTIVE' => false,
		'ICON' => 'ph-circuitry',
		'DESCRIPTION' => 'Схемы подключения и распайки'
	],
	[
		'NAME' => 'Классификация типов нагрузки контактов',
		'URL' => '/klassifikatsiya-tipov-nagruzki-kontaktov/',
		'ACTIVE' => false,
		'ICON' => 'ph-tree-structure',
		'DESCRIPTION' => 'Типы и характеристики нагрузок'
	]
];

// Articles data with local images
$arArticles = [
	[
		'ID' => 27362,
		'NAME' => 'Пусковые токи и как с ними бороться',
		'PREVIEW_TEXT' => 'Пусковые токи и как с ними бороться. Современные тенденции снижения массы и габаритов приборов привели к тому, что практически в каждом устройстве применяют импульсные источники',
		'DETAIL_PAGE_URL' => '/puskovye-toki-i-kak-s-nimi-borotsya/',
		'PREVIEW_PICTURE' => '/images/stati/inrush-current-300x201.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-08-15'
	],
	[
		'ID' => 17166,
		'NAME' => 'Заземление. Как это работает?',
		'PREVIEW_TEXT' => 'Заземление. Как это работает? Заземление – устройство, предохраняющее человека от поражения электрическим током. Благодаря использованию различных заземляющих приспособлений удается избежать жертв на производстве и в быту.',
		'DETAIL_PAGE_URL' => '/zazemlenie-kak-eto-rabotaet/',
		'PREVIEW_PICTURE' => '/images/stati/zazemlenie-kak-eto-rabotaet-300x300.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-20'
	],
	[
		'ID' => 17160,
		'NAME' => 'Разность потенциалов',
		'PREVIEW_TEXT' => 'Разность потенциалов или как избежать потребности в починке блоков питания в экранах, головах, актив. Повесили вы L-Acoustics, запустили DiGiCo, включили LA-8, достаете свой старенький ACER',
		'DETAIL_PAGE_URL' => '/raznost-potentsialov/',
		'PREVIEW_PICTURE' => '/images/stati/raznost-potentsialov-300x300.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-18'
	],
	[
		'ID' => 17151,
		'NAME' => 'Сравнение КГтп-ХЛ и H07RN-F',
		'PREVIEW_TEXT' => 'Какой кабель выбрать для сферы проката и инсталляций профессионального оборудования? Сравнение КГтп-ХЛ и H07RN-F Здравствуйте, дорогие друзья и коллеги! Сегодня компания Electric Distribution Systems (EDS)',
		'DETAIL_PAGE_URL' => '/sravnenie-kgtp-hl-i-h07rn-f/',
		'PREVIEW_PICTURE' => '/images/stati/razveska-dc-870x460-1-300x159.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-15'
	],
	[
		'ID' => 17142,
		'NAME' => 'АВР: что это такое',
		'PREVIEW_TEXT' => 'АВР: что это такое АВР – это вводно-коммутационное распределительное устройство, минимум, на два питающих ввода. Один ввод основной (от которого постоянно работает нагрузка) и другой',
		'DETAIL_PAGE_URL' => '/avr-chto-eto-takoe/',
		'PREVIEW_PICTURE' => '/images/stati/khd1miy-eei-300x139.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-12'
	],
	[
		'ID' => 17130,
		'NAME' => 'Витая пара. Категории и применение',
		'PREVIEW_TEXT' => 'Витая пара. Категории и применение Витая пара – это кабель связи для передачи цифрового сигнала. Он стал самым популярным для создания локальных и структурированных кабельных систем,',
		'DETAIL_PAGE_URL' => '/vitaya-para-kategorii-i-primenenie/',
		'PREVIEW_PICTURE' => '/images/stati/ethernet-604x460-1-300x228.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-10'
	],
	[
		'ID' => 17109,
		'NAME' => 'Расчет потери напряжения',
		'PREVIEW_TEXT' => 'Расчет потери напряжения Во время передачи электроэнергии по проводам к электроприемникам ее небольшая часть расходуется на сопротивление самих проводов. Чем выше протекаемый ток и больше',
		'DETAIL_PAGE_URL' => '/raschet-poteri-napryazheniya/',
		'PREVIEW_PICTURE' => '/images/stati/power-lose-300x169.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-08'
	],
	[
		'ID' => 17087,
		'NAME' => 'Как правильно пользоваться мультиметром',
		'PREVIEW_TEXT' => 'Как правильно пользоваться мультиметром Мультиметр – это прибор, с помощью которого можно без всякого труда определить величину напряжения и силу тока, сопротивление проводников, узнать параметры',
		'DETAIL_PAGE_URL' => '/kak-pravilno-polzovatsya-multimetrom/',
		'PREVIEW_PICTURE' => '/images/stati/multimtr-mas838-300x146.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-05'
	],
	[
		'ID' => 17072,
		'NAME' => 'Немного о реле контроля напряжения',
		'PREVIEW_TEXT' => 'Немного о реле контроля напряжения Что такое РКН? Перепады напряжения – проблема частая и повсеместная. Увеличилась нагрузка на фазу, перебои в работе линии электросети, перекос фаз',
		'DETAIL_PAGE_URL' => '/nemnogo-o-rele-kontrolya-napryazheniya/',
		'PREVIEW_PICTURE' => '/images/stati/rkn-300x156.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-03'
	],
	[
		'ID' => 17063,
		'NAME' => 'Неизвестное об известном. Unit',
		'PREVIEW_TEXT' => 'Неизвестное об известном. Unit Все привыкли измерять высоту оборудования, устанавливаемого в стойку в юнитах. А что такое Юнит (Unit)? Это неофициальная единица измерения, используемая в',
		'DETAIL_PAGE_URL' => '/unit/',
		'PREVIEW_PICTURE' => '/images/stati/cofr-300x168.jpg',
		'CATEGORY' => 'eds-polezno-znat',
		'DATE_CREATE' => '2021-05-01'
	]
];

/**
 * Render navigation component
 * @param array $navigation
 * @return string
 */
function renderNavigationComponent($navigation) {
	// Prepare data for component
	$arResult = [
		'NAVIGATION' => $navigation,
		'QUICK_NAV' => [] // No quick nav for articles page
	];

	$arParams = [];

	// Include component template
	ob_start();
	include($_SERVER["DOCUMENT_ROOT"] . '/local/templates/' . SITE_TEMPLATE_ID . '/components/bitrix/menu/useful_info_navigation/template.php');
	return ob_get_clean();
}
?>

    <div class="edsys-articles-page">
        <!-- Breadcrumbs -->
        <div class="edsys-breadcrumbs">
            <div class="container">
				<?$APPLICATION->IncludeComponent(
					"bitrix:breadcrumb",
					"edsys",
					Array(
						"PATH" => "",
						"SITE_ID" => "s1",
						"START_FROM" => "0"
					)
				);?>
            </div>
        </div>

        <!-- Hero Section -->
        <section class="edsys-articles-hero">
            <div class="container">
                <div class="edsys-articles-hero__content">
                    <h1 class="edsys-articles-hero__title">
                        <i class="ph ph-thin ph-lightning"></i>
                        Полезно знать
                    </h1>
                    <p class="edsys-articles-hero__subtitle">
                        Профессиональные статьи и экспертные материалы по электрооборудованию,<br>
                        распределению электроэнергии и сценическому оборудованию
                    </p>
                </div>
            </div>
        </section>

        <!-- Main Content -->
        <main class="edsys-articles-main">
            <div class="container">
                <div class="edsys-articles-layout">

                    <!-- Sidebar Navigation -->
                    <div class="edsys-articles-sidebar">
						<?= renderNavigationComponent($arNavigation) ?>
                    </div>

                    <!-- Articles Grid -->
                    <div class="edsys-articles-content">
                        <!-- Articles Grid -->
                        <div class="edsys-articles-grid" id="articlesGrid">
							<?php foreach($arArticles as $arArticle): ?>
                                <article class="edsys-article-card">
                                    <a href="<?=$arArticle['DETAIL_PAGE_URL']?>" class="edsys-article-card__link">
                                        <div class="edsys-article-card__image">
                                            <img src="<?=$arArticle['PREVIEW_PICTURE']?>"
                                                 alt="<?=htmlspecialchars($arArticle['NAME'])?>"
                                                 width="300"
                                                 height="200"
                                                 loading="lazy">
                                        </div>
                                        <div class="edsys-article-card__content">
                                            <h3 class="edsys-article-card__title"><?=$arArticle['NAME']?></h3>
                                            <p class="edsys-article-card__excerpt"><?=$arArticle['PREVIEW_TEXT']?></p>
                                            <div class="edsys-article-card__meta">
                                                <time class="edsys-article-card__date" datetime="<?=$arArticle['DATE_CREATE']?>">
                                                    <i class="ph ph-thin ph-calendar"></i>
													<?=FormatDate("d F Y", MakeTimeStamp($arArticle['DATE_CREATE']))?>
                                                </time>
                                                <span class="edsys-article-card__read-more">
                                            Читать дальше
                                            <i class="ph ph-thin ph-arrow-right"></i>
                                        </span>
                                            </div>
                                        </div>
                                    </a>
                                </article>
							<?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Scripts -->
    <script src="/stati-tablitsy-nagruzok/assets/js/articles.js"></script>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>