<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Каталог");
?>

<section class="edsys-category-page">
    <div class="edsys-category__container">
        <!-- Хлебные крошки -->
        <nav class="edsys-breadcrumbs" aria-label="Навигация">
            <?$APPLICATION->IncludeComponent(
                "bitrix:breadcrumb",
                "edsys_breadcrumbs",
                array(
                    "START_FROM" => "0",
                    "PATH" => "",
                    "SITE_ID" => "-"
                ),
                false
            );?>
        </nav>

        <!-- Заголовок и описание категории -->
        <header class="edsys-category__header">
            <h1 class="edsys-category__title"><?$APPLICATION->ShowTitle(false)?></h1>
            <div class="edsys-category__description">
                <?$APPLICATION->ShowProperty("description")?>
            </div>
        </header>

        <div class="edsys-category__layout">
            <!-- Фильтр -->
            <aside class="edsys-category__filters">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:catalog.smart.filter",
                    "edsys_filter",
                    array(
                        "IBLOCK_TYPE" => "catalog",
                        "IBLOCK_ID" => "7",
                        "SECTION_ID" => $_REQUEST["SECTION_ID"],
                        "FILTER_NAME" => "arrFilter",
                        "PRICE_CODE" => array("BASE", "RETAIL"),
                        "CACHE_TYPE" => "A",
                        "CACHE_TIME" => "36000000",
                        "CACHE_GROUPS" => "Y",
                        "SAVE_IN_SESSION" => "N",
                        "FILTER_VIEW_MODE" => "vertical",
                        "XML_EXPORT" => "N",
                        "SECTION_TITLE" => "NAME",
                        "SECTION_DESCRIPTION" => "DESCRIPTION",
                        "HIDE_NOT_AVAILABLE" => "N",
                        "TEMPLATE_THEME" => "blue",
                        "CONVERT_CURRENCY" => "N",
                        "CURRENCY_ID" => "RUB",
                        "SEF_MODE" => "N",
                        "SEF_RULE" => "",
                        "SMART_FILTER_PATH" => "",
                        "PAGER_PARAMS_NAME" => "arrPager",
                        "INSTANT_RELOAD" => "N",
                    ),
                    false
                );?>
            </aside>

            <!-- Основной контент -->
            <main class="edsys-category__content">
                <!-- Сортировка и настройки отображения -->
                <div class="edsys-category__toolbar">
                    <div class="edsys-category__sort">
                        <label for="sort-select" class="edsys-category__sort-label">Сортировать:</label>
                        <select id="sort-select" class="edsys-category__sort-select">
                            <option value="name-asc">По названию ↑</option>
                            <option value="name-desc">По названию ↓</option>
                            <option value="price-asc">По цене ↑</option>
                            <option value="price-desc">По цене ↓</option>
                            <option value="popularity">По популярности</option>
                            <option value="date">По новизне</option>
                        </select>
                    </div>

                    <div class="edsys-category__view-mode">
                        <button class="edsys-category__view-btn edsys-category__view-btn--active" data-view="grid">
                            <i class="ph ph-thin ph-squares-four"></i>
                        </button>
                        <button class="edsys-category__view-btn" data-view="list">
                            <i class="ph ph-thin ph-list"></i>
                        </button>
                    </div>
                </div>

                <!-- Список товаров -->
                <div class="edsys-category__products" id="products-container">
                    <?$APPLICATION->IncludeComponent(
                        "bitrix:catalog.section",
                        "edsys_products",
                        array(
                            "IBLOCK_TYPE" => "catalog",
                            "IBLOCK_ID" => "7",
                            "SECTION_ID" => $_REQUEST["SECTION_ID"],
                            "SECTION_CODE" => $_REQUEST["SECTION_CODE"],
                            "ELEMENT_SORT_FIELD" => "name",
                            "ELEMENT_SORT_ORDER" => "asc",
                            "ELEMENT_SORT_FIELD2" => "id",
                            "ELEMENT_SORT_ORDER2" => "desc",
                            "PROPERTY_CODE" => array("CML2_ARTICLE", "BRAND", "SPECIFICATIONS"),
                            "META_KEYWORDS" => "-",
                            "META_DESCRIPTION" => "-",
                            "BROWSER_TITLE" => "-",
                            "SET_LAST_MODIFIED" => "N",
                            "INCLUDE_SUBSECTIONS" => "Y",
                            "BASKET_URL" => "/personal/basket.php",
                            "ACTION_VARIABLE" => "action",
                            "PRODUCT_ID_VARIABLE" => "id",
                            "SECTION_ID_VARIABLE" => "SECTION_ID",
                            "PRODUCT_QUANTITY_VARIABLE" => "quantity",
                            "PRODUCT_PROPS_VARIABLE" => "prop",
                            "FILTER_NAME" => "arrFilter",
                            "CACHE_TYPE" => "A",
                            "CACHE_TIME" => "36000000",
                            "CACHE_FILTER" => "N",
                            "CACHE_GROUPS" => "Y",
                            "SET_TITLE" => "Y",
                            "MESSAGE_404" => "",
                            "SET_STATUS_404" => "N",
                            "SHOW_404" => "N",
                            "FILE_404" => "",
                            "DISPLAY_COMPARE" => "Y",
                            "PAGE_ELEMENT_COUNT" => "20",
                            "LINE_ELEMENT_COUNT" => "3",
                            "PRICE_CODE" => array("BASE", "RETAIL"),
                            "USE_PRICE_COUNT" => "N",
                            "SHOW_PRICE_COUNT" => "1",
                            "PRICE_VAT_INCLUDE" => "Y",
                            "USE_PRODUCT_QUANTITY" => "N",
                            "ADD_PROPERTIES_TO_BASKET" => "Y",
                            "PARTIAL_PRODUCT_PROPERTIES" => "N",
                            "PRODUCT_PROPERTIES" => array(),
                            "DISPLAY_TOP_PAGER" => "N",
                            "DISPLAY_BOTTOM_PAGER" => "N",
                            "PAGER_TITLE" => "Товары",
                            "PAGER_SHOW_ALWAYS" => "N",
                            "PAGER_TEMPLATE" => "",
                            "PAGER_DESC_NUMBERING" => "N",
                            "PAGER_DESC_NUMBERING_CACHE_TIME" => "36000",
                            "PAGER_SHOW_ALL" => "N",
                            "PAGER_BASE_LINK_ENABLE" => "N",
                            "PAGER_BASE_LINK" => "",
                            "PAGER_PARAMS_NAME" => "arrPager",
                            "OFFERS_CART_PROPERTIES" => array(),
                            "OFFERS_FIELD_CODE" => array("NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE"),
                            "OFFERS_PROPERTY_CODE" => array(""),
                            "OFFERS_SORT_FIELD" => "name",
                            "OFFERS_SORT_ORDER" => "asc",
                            "OFFERS_SORT_FIELD2" => "id",
                            "OFFERS_SORT_ORDER2" => "desc",
                            "OFFERS_LIMIT" => "5",
                            "SECTION_URL" => "",
                            "DETAIL_URL" => "",
                            "USE_MAIN_ELEMENT_SECTION" => "N",
                            "CONVERT_CURRENCY" => "N",
                            "CURRENCY_ID" => "RUB",
                            "HIDE_NOT_AVAILABLE" => "N",
                            "LAZY_LOAD" => "Y",
                            "LOAD_ON_SCROLL" => "Y",
                            "USE_ENHANCED_ECOMMERCE" => "N",
                            "DATA_LAYER_NAME" => "dataLayer",
                            "BRAND_PROPERTY" => "BRAND",
                            "USE_ELEMENT_COUNTER" => "Y"
                        ),
                        false
                    );?>
                </div>

                <!-- Индикатор загрузки -->
                <div class="edsys-category__loader" id="products-loader" style="display: none;">
                    <div class="edsys-category__loader-spinner"></div>
                    <span>Загружаем товары...</span>
                </div>
            </main>
        </div>
    </div>
</section>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
