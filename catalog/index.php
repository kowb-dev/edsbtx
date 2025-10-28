<?php
/**
 * Catalog Page
 * 
 * @version 1.1.0
 * @author KW
 * @link https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Каталог товаров");
$APPLICATION->SetPageProperty("description", "Профессиональное оборудование для распределения электроэнергии и управления сигналами");
$APPLICATION->SetPageProperty("keywords", "каталог оборудования, распределение электроэнергии, stage equipment");

$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "catalog_list",
    [
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => 7,
        "SECTION_ID" => $_REQUEST["SECTION_ID"] ?? "",
        "SECTION_CODE" => $_REQUEST["SECTION_CODE"] ?? "",
        
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => "name",
        "ELEMENT_SORT_ORDER2" => "asc",
        
        "FILTER_NAME" => "arrFilter",
        "INCLUDE_SUBSECTIONS" => "Y",
        "SHOW_ALL_WO_SECTION" => "Y",
        
        "PAGE_ELEMENT_COUNT" => 30,
        "LINE_ELEMENT_COUNT" => 4,
        
        "PROPERTY_CODE" => [
            "CML2_ARTICLE",
            "NEWPRODUCT",
            "SPECIALOFFER",
            "CML2_MANUFACTURER",
        ],
        
        "OFFERS_LIMIT" => 0,
        "OFFERS_FIELD_CODE" => [],
        "OFFERS_PROPERTY_CODE" => [],
        "OFFERS_SORT_FIELD" => "sort",
        "OFFERS_SORT_ORDER" => "asc",
        "OFFERS_SORT_FIELD2" => "id",
        "OFFERS_SORT_ORDER2" => "desc",
        
        "SECTION_URL" => "/catalog/#SECTION_CODE_PATH#/",
        "DETAIL_URL" => "/product/#ELEMENT_CODE#/",
        "BASKET_URL" => "/personal/cart/",
        
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "PRODUCT_QUANTITY_VARIABLE" => "quantity",
        "PRODUCT_PROPS_VARIABLE" => "prop",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        
        "USE_PRODUCT_QUANTITY" => "N",
        "ADD_PROPERTIES_TO_BASKET" => "Y",
        "PRODUCT_PROPERTIES" => [],
        "PARTIAL_PRODUCT_PROPERTIES" => "N",
        
        "USE_COMPARE" => "N",
        
        "USE_PRICE_COUNT" => "N",
        "SHOW_PRICE_COUNT" => 1,
        "PRICE_CODE" => ["BASE"],
        "USE_ENHANCED_ECOMMERCE" => "N",
        
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => 36000000,
        "CACHE_FILTER" => "Y",
        "CACHE_GROUPS" => "Y",
        
        "SET_TITLE" => "Y",
        "SET_BROWSER_TITLE" => "Y",
        "BROWSER_TITLE" => "-",
        "SET_META_KEYWORDS" => "Y",
        "META_KEYWORDS" => "-",
        "SET_META_DESCRIPTION" => "Y",
        "META_DESCRIPTION" => "-",
        "ADD_SECTIONS_CHAIN" => "Y",
        "SET_STATUS_404" => "Y",
        "SHOW_404" => "Y",
        "FILE_404" => "",
        
        "PAGER_TEMPLATE" => ".default",
        "DISPLAY_TOP_PAGER" => "N",
        "DISPLAY_BOTTOM_PAGER" => "Y",
        "PAGER_TITLE" => "Товары",
        "PAGER_SHOW_ALWAYS" => "N",
        "PAGER_DESC_NUMBERING" => "N",
        "PAGER_DESC_NUMBERING_CACHE_TIME" => 36000,
        "PAGER_SHOW_ALL" => "N",
        "PAGER_BASE_LINK_ENABLE" => "N",
        
        "SET_LAST_MODIFIED" => "N",
        "USE_MAIN_ELEMENT_SECTION" => "N",
        "LAZY_LOAD" => "N",
        "LOAD_ON_SCROLL" => "N",
        "MESS_BTN_LAZY_LOAD" => "Показать ещё",
        "MESS_BTN_ADD_TO_BASKET" => "В корзину",
        "MESS_BTN_BUY" => "Купить",
        "MESS_BTN_DETAIL" => "Подробнее",
        "MESS_BTN_SUBSCRIBE" => "Подписаться",
        "MESS_NOT_AVAILABLE" => "Нет в наличии",
        
        "COMPATIBLE_MODE" => "Y",
        "DISABLE_INIT_JS_IN_COMPONENT" => "N",
    ],
    false,
    ["HIDE_ICONS" => "Y"]
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");