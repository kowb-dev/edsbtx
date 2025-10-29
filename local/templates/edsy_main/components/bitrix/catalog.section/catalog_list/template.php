<?php
/**
 * Catalog Section Template - Table View
 * 
 * @version 1.5.0
 * @author KW
 * @link https://kowb.ru
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

if ($arResult['CODE'] == 'korobki-kommutatsionnye') {
    ?>
    <style>
        .subcategories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 20px;
        }
        .subcategory-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            transition: box-shadow 0.3s;
        }
        .subcategory-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .subcategory-card img {
            max-width: 100%;
            height: auto;
            margin-bottom: 16px;
        }
        .subcategory-card span {
            font-weight: 500;
            text-align: center;
        }
    </style>
    <div class="subcategories">
        <a href="/cat/korobki-kommutatsionnye-bez-profilya/" class="subcategory-card">
            <img src="/upload/iblock/e6c/kkxzakekerj9jw2z794xh1kujdgh84co.jpg" alt="Без профиля">
            <span>Без профиля</span>
        </a>
        <a href="/cat/korobki-s-profilem/" class="subcategory-card">
            <img src="/upload/iblock/310/nr3mzcvnhm2jmts4tp3bsnhbeocicg1s.jpg" alt="С профилем">
            <span>С профилем</span>
        </a>
        <a href="/cat/korobki-kommutatsionnye-skoshennye/" class="subcategory-card">
            <img src="/upload/iblock/052/fxc45564ov2bl3unca8endq68g8t719i.jpg" alt="Скошенная">
            <span>Скошенные</span>
        </a>
        <a href="/cat/korobki-kommutatsionnye-vstraivaemye" class="subcategory-card">
            <img src="/upload/iblock/3a1/hmd6l05vsmyejx8r7pt6jcxe26u4cdtm.jpg" alt="Встраиваемая">
            <span>Встраиваемые</span>
        </a>
    </div>
    <?php
} elseif ($arResult['CODE'] == 'lyuchki-sczenicheskie') {
    ?>
    <?$APPLICATION->IncludeComponent(
        "bitrix:breadcrumb",
        "",
        Array(
            "START_FROM" => "0", 
            "PATH" => "", 
            "SITE_ID" => "s1" 
        )
    );?>
    <style>
        .subcategories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 5rem auto;
            max-width: 1600px;
        }
        .subcategory-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            transition: box-shadow 0.3s;
        }
        .subcategory-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.12);
        }
        .subcategory-card__image-wrapper {
            width: 100%;
            aspect-ratio: 4/3;
            margin-bottom: 16px;
            overflow: hidden;
            border-radius: 4px;
        }
        .subcategory-card img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .subcategory-card:hover img {
            transform: scale(1.1);
        }
        .subcategory-card span {
            font-weight: 500;
            text-align: center;
        }
    </style>
    <div class="subcategories">
        <a href="/cat/lyuchki-sczenicheskie/seriya-sh/" class="subcategory-card">
            <div class="subcategory-card__image-wrapper">
                <img src="/upload/iblock/a5c/ah9z3bvqdyp6tfgtagut7aocg5bk1zzm.jpg" alt="Лючки сценические обрезные серии SH">
            </div>
            <span>Лючки сценические обрезные серии SH</span>
        </a>
        <a href="/cat/lyuchki-sczenicheskie/seriya-esh/" class="subcategory-card">
            <div class="subcategory-card__image-wrapper">
                <img src="/upload/iblock/56b/wym5scez4d872wm801w21pabm5bn9llx.jpg" alt="Лючки сценические встраиваемые под покрытие серии ESH">
            </div>
            <span>Лючки сценические встраиваемые под покрытие серии ESH</span>
        </a>
        <a href="/cat/lyuchki-sczenicheskie/seriya-bsh/" class="subcategory-card">
            <div class="subcategory-card__image-wrapper">
                <img src="/upload/iblock/1f2/91hbfr1szyds937mano0l3abacdmdkm6.jpg" alt="Лючки сценические с минимальной монтажной глубиной серии BSH">
            </div>
            <span>Лючки сценические с минимальной монтажной глубиной серии BSH</span>
        </a>
    </div>
    <?php
} else {
    $this->addExternalCss($templateFolder . '/style.css');
    $this->addExternalJs($templateFolder . '/script.js');

    CModule::IncludeModule('iblock');

    $iblockId = isset($arParams['IBLOCK_ID']) ? (int)$arParams['IBLOCK_ID'] : 7;

    $rsSections = CIBlockSection::GetList(
        ['LEFT_MARGIN' => 'ASC'],
        [
            'IBLOCK_ID' => $iblockId,
            'ACTIVE' => 'Y',
            'GLOBAL_ACTIVE' => 'Y',
        ],
        true,
        ['ID', 'NAME', 'IBLOCK_SECTION_ID', 'LEFT_MARGIN', 'RIGHT_MARGIN', 'ELEMENT_CNT', 'SECTION_PAGE_URL']
    );

    $arSections = [];
    $arHierarchy = [];
    $totalProductCount = 0;

    while ($arSection = $rsSections->GetNext()) {
        $arHierarchy[$arSection['ID']] = [
            'parent' => $arSection['IBLOCK_SECTION_ID'],
            'left' => (int)$arSection['LEFT_MARGIN'],
            'right' => (int)$arSection['RIGHT_MARGIN']
        ];
        
        if ($arSection['ELEMENT_CNT'] > 0) {
            $arSections[] = $arSection;
            $totalProductCount += (int)$arSection['ELEMENT_CNT'];
        }
    }
    ?>

    <div class="edsys-catalog" itemscope itemtype="https://schema.org/CollectionPage">
        <div class="edsys-catalog__container">
            
            <nav class="edsys-breadcrumb" aria-label="Навигация">
                <ol class="edsys-breadcrumb__list">
                    <li class="edsys-breadcrumb__item">
                        <a href="/" class="edsys-breadcrumb__link">Главная</a>
                    </li>
                    <li class="edsys-breadcrumb__item">
                        <a href="/catalog/" class="edsys-breadcrumb__link">Каталог</a>
                    </li>
                    <?php if (!empty($arResult['SECTION']['PATH'])): ?>
                        <?php foreach ($arResult['SECTION']['PATH'] as $pathItem): ?>
                            <li class="edsys-breadcrumb__item">
                                <a href="<?= htmlspecialchars($pathItem['SECTION_PAGE_URL']) ?>" class="edsys-breadcrumb__link">
                                    <?= htmlspecialchars($pathItem['NAME']) ?>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (!empty($arResult['NAME'])): ?>
                        <li class="edsys-breadcrumb__item">
                            <?= htmlspecialchars($arResult['NAME']) ?>
                        </li>
                    <?php endif; ?>
                </ol>
            </nav>

            <button 
                class="edsys-catalog__filter-toggle" 
                type="button"
                aria-label="Открыть фильтры"
                aria-expanded="false"
                aria-controls="catalog-filter"
            >
                <i class="ph ph-thin ph-funnel" aria-hidden="true"></i>
                <span>Фильтры</span>
            </button>

            <div class="edsys-catalog__layout">
                
                <!-- Sidebar Filter -->
                <aside 
                    class="edsys-catalog__sidebar" 
                    id="catalog-filter"
                    role="complementary"
                    aria-label="Фильтрация товаров"
                >
                    <div class="edsys-catalog__sidebar-inner">
                        
                        <div class="edsys-catalog__filter-header">
                            <h2 class="edsys-catalog__filter-title">Категории</h2>
                            <button 
                                class="edsys-catalog__filter-close"
                                type="button"
                                aria-label="Закрыть фильтры"
                            >
                                <i class="ph ph-thin ph-x" aria-hidden="true"></i>
                            </button>
                        </div>

                        <form 
                            class="edsys-catalog__filter-form" 
                            id="catalog-filter-form"
                            action="<?= $APPLICATION->GetCurPage() ?>"
                            method="get"
                        >
                            
                            <fieldset class="edsys-catalog__filter-group">
                                <legend class="edsys-catalog__filter-legend">
                                    Фильтр по категориям
                                </legend>
                                
                                <div class="edsys-catalog__filter-items" role="group">
                                    <?php if (!empty($arSections)): ?>
                                        
                                        <label class="edsys-catalog__filter-item edsys-catalog__filter-item--all">
                                            <input 
                                                type="checkbox" 
                                                class="edsys-catalog__filter-checkbox"
                                                id="category-all"
                                                name="category[]"
                                                value="all"
                                                checked
                                            >
                                            <span class="edsys-catalog__filter-checkmark" aria-hidden="true">
                                                <i class="ph ph-thin ph-check"></i>
                                            </span>
                                            <span class="edsys-catalog__filter-label">
                                                Все категории
                                            </span>
                                        </label>

                                        <?php foreach ($arSections as $arSection): ?>
                                            <label class="edsys-catalog__filter-item">
                                                <input 
                                                    type="checkbox" 
                                                    class="edsys-catalog__filter-checkbox edsys-catalog__category-checkbox"
                                                    id="category-<?= $arSection['ID'] ?>"
                                                    name="category[]"
                                                    value="<?= $arSection['ID'] ?>"
                                                    data-section-id="<?= $arSection['ID'] ?>"
                                                >
                                                <span class="edsys-catalog__filter-checkmark" aria-hidden="true">
                                                    <i class="ph ph-thin ph-check"></i>
                                                </span>
                                                <span class="edsys-catalog__filter-label">
                                                    <?= htmlspecialchars($arSection['NAME']) ?>
                                                </span>
                                            </label>
                                        <?php endforeach; ?>
                                        
                                    <?php endif; ?>
                                </div>
                            </fieldset>

                            <div class="edsys-catalog__filter-actions">
                                <button 
                                    type="button" 
                                    class="edsys-catalog__filter-reset"
                                    id="filter-reset"
                                >
                                    <i class="ph ph-thin ph-arrow-counter-clockwise" aria-hidden="true"></i>
                                    Сбросить фильтры
                                </button>
                            </div>

                        </form>

                    </div>
                </aside>

                <!-- Main Content -->
                <main class="edsys-catalog__main" role="main">
                    
                    <header class="edsys-catalog__header">
                        <h1 class="edsys-catalog__title" itemprop="name">
                            <?= $arResult['NAME'] ?? 'Каталог товаров' ?>
                        </h1>
                        
                        <div class="edsys-catalog__counter" role="status" aria-live="polite">
                            <span>Товаров на странице:</span>
                            <strong id="products-count"><?= $totalProductCount ?></strong>
                        </div>
                    </header>

                    <div class="edsys-catalog__products" id="catalog-products">
                        <?php if (!empty($arResult['ITEMS'])): ?>
                            
                            <!-- Table Header -->
                            <div class="edsys-catalog__table-header">
                                <div class="edsys-catalog__table-col edsys-catalog__table-col--photo">Фото</div>
                                <div class="edsys-catalog__table-col edsys-catalog__table-col--name">Наименование</div>
                                <div class="edsys-catalog__table-col edsys-catalog__table-col--article">Артикул</div>
                                <div class="edsys-catalog__table-col edsys-catalog__table-col--price">Цена</div>
                                <div class="edsys-catalog__table-col edsys-catalog__table-col--actions"></div>
                            </div>

                            <div class="edsys-catalog__table-body">
                                <?php foreach ($arResult['ITEMS'] as $arItem): 
                                    $picture = !empty($arItem['PREVIEW_PICTURE']) 
                                        ? CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], ['width' => 200, 'height' => 200], BX_RESIZE_IMAGE_PROPORTIONAL)
                                        : null;
                                    
                                    // Article extraction - check all possible sources
                                    $article = '';
                                    // Get article
                                    $article = '';
                                    $articleProps = ['CML2_ARTICLE', 'ARTICLE', 'ART', 'ARTICUL'];
                                    foreach ($articleProps as $propCode) {
                                        if (!empty($arItem['PROPERTIES'][$propCode]['VALUE'])) {
                                            $article = is_array($arItem['PROPERTIES'][$propCode]['VALUE']) ? $arItem['PROPERTIES'][$propCode]['VALUE'][0] : $arItem['PROPERTIES'][$propCode]['VALUE'];
                                            break;
                                        }
                                    }
                                    
                                    $price = $arItem['PRICES']['BASE'] ?? null;
                                    $hasPrice = !empty($price) && $price['VALUE'] > 0;
                                ?>
                                    
                                    <!-- Product Row -->
                                    <article 
                                        class="edsys-catalog__table-row" 
                                        data-section-id="<?= $arItem['IBLOCK_SECTION_ID'] ?>"
                                        data-product-url="<?= htmlspecialchars($arItem['DETAIL_PAGE_URL']) ?>"
                                        itemscope 
                                        itemtype="https://schema.org/Product"
                                    >
                                        <!-- Photo Column -->
                                        <div class="edsys-catalog__table-col edsys-catalog__table-col--photo">
                                            <div class="edsys-catalog__table-photo">
                                                <?php if ($picture): ?>
                                                    <img 
                                                        src="<?= $picture['src'] ?>" 
                                                        alt="<?= htmlspecialchars($arItem['NAME']) ?>"
                                                        width="80"
                                                        height="80"
                                                        loading="lazy"
                                                        itemprop="image"
                                                    >
                                                <?php else: ?>
                                                    <div class="edsys-catalog__table-no-photo">
                                                        <i class="ph ph-thin ph-image"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <!-- Name Column -->
                                        <div class="edsys-catalog__table-col edsys-catalog__table-col--name">
                                            <span class="edsys-catalog__table-name" itemprop="name">
                                                <?= htmlspecialchars($arItem['NAME']) ?>
                                            </span>
                                            
                                            <?php if (!empty($arItem['PREVIEW_TEXT'])): ?>
                                                <div class="edsys-catalog__table-desc">
                                                    <?= strip_tags($arItem['PREVIEW_TEXT']) ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Article Column -->
                                        <div class="edsys-catalog__table-col edsys-catalog__table-col--article">
                                            <?php if (!empty($article)): ?>
                                                <span itemprop="sku"><?= htmlspecialchars($article) ?></span>
                                            <?php else: ?>
                                                <span class="edsys-catalog__table-empty">—</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Price Column -->
                                        <div class="edsys-catalog__table-col edsys-catalog__table-col--price">
                                            <?php if ($hasPrice): ?>
                                                <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
                                                    <meta itemprop="priceCurrency" content="<?= $price['CURRENCY'] ?>">
                                                    <link itemprop="availability" href="https://schema.org/InStock">
                                                    <span class="edsys-catalog__table-price" itemprop="price" content="<?= $price['VALUE'] ?>">
                                                        <?= $price['PRINT_VALUE'] ?>
                                                    </span>
                                                </div>
                                            <?php else: ?>
                                                <span class="edsys-catalog__table-price-request">Цена по запросу</span>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Actions Column -->
                                        <div class="edsys-catalog__table-col edsys-catalog__table-col--actions">
                                            <div class="edsys-catalog__table-actions">
                                                <button 
                                                    type="button"
                                                    class="edsys-catalog__action-btn edsys-catalog__action-btn--favorite"
                                                    data-product-id="<?= $arItem['ID'] ?>"
                                                    title="Добавить в избранное"
                                                    aria-label="Добавить в избранное"
                                                >
                                                    <i class="ph ph-thin ph-heart"></i>
                                                </button>
                                                
                                                <button 
                                                    type="button"
                                                    class="edsys-catalog__action-btn edsys-catalog__action-btn--compare"
                                                    data-product-id="<?= $arItem['ID'] ?>"
                                                    title="Добавить к сравнению"
                                                    aria-label="Добавить к сравнению"
                                                    aria-pressed="false"
                                                >
                                                    <i class="ph ph-thin ph-chart-bar"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </article>

                                <?php endforeach; ?>
                            </div>

                            <?php if ($arParams["DISPLAY_BOTTOM_PAGER"]): ?>
                                <div class="edsys-catalog__pagination">
                                    <?= $arResult["NAV_STRING"] ?>
                                </div>
                            <?php endif; ?>

                        <?php else: ?>
                            
                            <!-- Empty State -->
                            <div class="edsys-catalog__empty">
                                <div class="edsys-catalog__empty-icon">
                                    <i class="ph ph-thin ph-package"></i>
                                </div>
                                <h2 class="edsys-catalog__empty-title">Товары не найдены</h2>
                                <p class="edsys-catalog__empty-text">
                                    По выбранным фильтрам товары не найдены. Попробуйте изменить параметры фильтрации.
                                </p>
                                <button 
                                    type="button"
                                    class="edsys-catalog__empty-btn"
                                    id="reset-all-filters"
                                >
                                    Сбросить все фильтры
                                </button>
                            </div>

                        <?php endif; ?>

                    </div>

                    <!-- Loading Overlay -->
                    <div class="edsys-catalog__loading" id="catalog-loading" hidden>
                        <div class="edsys-catalog__spinner">
                            <svg viewBox="0 0 50 50" class="edsys-catalog__spinner-svg">
                                <circle cx="25" cy="25" r="20" fill="none" stroke-width="4"></circle>
                            </svg>
                        </div>
                        <span class="edsys-catalog__loading-text">Загрузка...</span>
                    </div>

                </main>

            </div>

        </div>
    </div>

    <script>
        window.edysCatalogConfig = {
            iblockId: <?= $iblockId ?>,
            ajaxUrl: '<?= $componentPath ?>/ajax.php',
            sessid: '<?= bitrix_sessid() ?>',
            hierarchy: <?= json_encode($arHierarchy, JSON_NUMERIC_CHECK) ?>,
            totalProducts: <?= $totalProductCount ?>,
            messages: {
                found: 'Найдено товаров',
                loading: 'Загрузка...',
                error: 'Произошла ошибка'
            }
        };
    </script>
<?php } ?>