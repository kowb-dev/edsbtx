<?
// This is a simplified ajax handler. For a real project, consider using a D7 controller.
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Context;

if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    return;
}

$request = Context::getCurrent()->getRequest();

if ($request->isPost() && check_bitrix_sessid()) {
    $ids = $request->getPost('ids');

    if (!empty($ids) && is_array($ids)) {
        global $arrFavoritesFilter;
        $arrFavoritesFilter = ['ID' => $ids];

        $APPLICATION->IncludeComponent(
            "bitrix:catalog.section",
            "edsys_products",
            array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "7",
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
                "FILTER_NAME" => "arrFavoritesFilter",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_FILTER" => "N",
                "CACHE_GROUPS" => "Y",
                "SET_TITLE" => "N", // No need to set title in AJAX response
                "MESSAGE_404" => "",
                "SET_STATUS_404" => "N",
                "SHOW_404" => "N",
                "FILE_404" => "",
                "DISPLAY_COMPARE" => "Y",
                "PAGE_ELEMENT_COUNT" => "30", // Show all favorites
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
            ),
            false
        );
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
