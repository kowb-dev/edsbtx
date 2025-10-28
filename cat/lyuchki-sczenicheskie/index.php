<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Лючки сценические");

$APPLICATION->IncludeComponent(
    "bitrix:catalog.section",
    "catalog_list",
    [
        "SECTION_CODE" => "lyuchki-sczenicheskie",
        "IBLOCK_TYPE" => "catalog",
        "IBLOCK_ID" => 7,
        "ELEMENT_SORT_FIELD" => "sort",
        "ELEMENT_SORT_ORDER" => "asc",
        "ELEMENT_SORT_FIELD2" => "name",
        "ELEMENT_SORT_ORDER2" => "asc",
        "FILTER_NAME" => "arrFilter",
        "INCLUDE_SUBSECTIONS" => "N",
        "SHOW_ALL_WO_SECTION" => "N",
        "PAGE_ELEMENT_COUNT" => 1, 
        "LINE_ELEMENT_COUNT" => 1,
        "PROPERTY_CODE" => [],
        "OFFERS_LIMIT" => 0,
        "SECTION_URL" => "/catalog/#SECTION_CODE_PATH#/",
        "DETAIL_URL" => "/product/#ELEMENT_CODE#/",
        "BASKET_URL" => "/personal/cart/",
        "ACTION_VARIABLE" => "action",
        "PRODUCT_ID_VARIABLE" => "id",
        "SECTION_ID_VARIABLE" => "SECTION_ID",
        "CACHE_TYPE" => "A",
        "CACHE_TIME" => 36000000,
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
        "DISPLAY_BOTTOM_PAGER" => "N",
        "COMPATIBLE_MODE" => "Y",
    ],
    false,
    ["HIDE_ICONS" => "Y"]
);

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>