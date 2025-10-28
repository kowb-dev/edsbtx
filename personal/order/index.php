<?php
/**
 * Order Checkout Page
 * File: /personal/order/index.php
 * 
 * @version 1.0.0
 * @author KW https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/order/');
    die();
}

$APPLICATION->SetTitle("Оформление заказа");

$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.order.ajax/.default/style.css?v=1.0.0");
$APPLICATION->AddHeadScript("/local/templates/.default/components/bitrix/sale.order.ajax/.default/script.js?v=1.0.0");

?>

<section class="edsys-order-page">
    <div class="edsys-order-page__container">
    <?php
    $APPLICATION->IncludeComponent(
        "bitrix:sale.order.ajax",
        ".default",
        array(
            "PAY_FROM_ACCOUNT" => "Y",
            "COUNT_DELIVERY_TAX" => "N",
            "COUNT_DISCOUNT_4_ALL_QUANTITY" => "N",
            "ONLY_FULL_PAY_FROM_ACCOUNT" => "N",
            "ALLOW_AUTO_REGISTER" => "Y",
            "SEND_NEW_USER_NOTIFY" => "Y",
            "DELIVERY_NO_AJAX" => "N",
            "TEMPLATE_LOCATION" => "popup",
            "PROP_1" => array(),
            "PATH_TO_BASKET" => "/personal/cart/",
            "PATH_TO_PERSONAL" => "/personal/orders/",
            "PATH_TO_PAYMENT" => "/personal/order/payment/",
            "PATH_TO_ORDER" => "/personal/order/",
            "SET_TITLE" => "N",
            "SHOW_ACCOUNT_NUMBER" => "Y",
            "DELIVERY_NO_SESSION" => "Y",
            "COMPATIBLE_MODE" => "Y",
            "BASKET_POSITION" => "before",
            "BASKET_IMAGES_SCALING" => "adaptive",
            "SERVICES_IMAGES_SCALING" => "adaptive",
            "USER_CONSENT" => "Y",
            "USER_CONSENT_ID" => "1",
            "USER_CONSENT_IS_CHECKED" => "Y",
            "USER_CONSENT_IS_LOADED" => "Y",
            "USE_PRELOAD" => "Y",
            "ALLOW_APPEND_ORDER" => "Y",
            "SHOW_NOT_CALCULATED_DELIVERIES" => "L",
            "SHOW_VAT_PRICE" => "Y",
            "USE_YM_GOALS" => "N",
            "USE_ENHANCED_ECOMMERCE" => "Y",
            "DATA_LAYER_NAME" => "dataLayer",
            "BRAND_PROPERTY" => "",
            "ADDITIONAL_PICT_PROP_2" => "-",
            "ADDITIONAL_PICT_PROP_1" => "-",
            "SHOW_COUPONS" => "Y",
            "SHOW_COUPONS_BASKET" => "Y",
            "SHOW_COUPONS_DELIVERY" => "Y",
            "SHOW_COUPONS_PAY_SYSTEM" => "Y",
            "DELIVERY_FADE_EXTRA_SERVICES" => "Y",
            "SHOW_ORDER_BUTTON" => "final_step",
            "SHOW_TOTAL_ORDER_BUTTON" => "N",
            "SHOW_PAY_SYSTEM_LIST_NAMES" => "Y",
            "SHOW_PAY_SYSTEM_INFO_NAME" => "Y",
            "SHOW_DELIVERY_LIST_NAMES" => "Y",
            "SHOW_DELIVERY_INFO_NAME" => "Y",
            "SHOW_DELIVERY_PARENT_NAMES" => "Y",
            "SKIP_USELESS_BLOCK" => "Y",
            "SHOW_BASKET_HEADERS" => "N",
            "DELIVERY_CALC_AJAX_TTL" => "3600",
            "USE_CUSTOM_MAIN_MESSAGES" => "N",
            "USE_CUSTOM_ADDITIONAL_MESSAGES" => "N",
            "USE_CUSTOM_ERROR_MESSAGES" => "N",
            "SPOT_LOCATION_BY_GEOIP" => "Y",
            "SHOW_STORES_IMAGES" => "Y",
            "CHECKOUT_STEP_ORDER" => "basket,info,delivery,payment,finalize",
            "HIDE_ORDER_DESCRIPTION" => "N",
            "PRODUCT_COLUMNS_VISIBLE" => array(
                "PREVIEW_PICTURE",
                "PROPS",
                "PRICE",
                "QUANTITY",
                "SUM",
            ),
            "PRODUCT_COLUMNS_HIDDEN" => array(),
            "ALLOW_NEW_PROFILE" => "Y",
            "SHOW_PAYMENT_SERVICES_NAMES" => "Y",
            "SHOW_STORES_IMAGES" => "Y",
            "DISABLE_BASKET_REDIRECT" => "N",
            "SHOW_MAP_IN_PROPS" => "N",
            "USE_PHONE_NORMALIZATION" => "Y",
            "PHONE_NORMALIZATION_COUNTRY" => "KZ",
            "ALLOW_USER_PROFILES" => "Y",
            "TEMPLATE_THEME" => "site",
            "SHOW_ORDER_PRODUCT_PICTURE" => "Y",
        ),
        false
    );
    ?>
    </div>
</section>

<script src="/local/templates/.default/components/bitrix/sale.order.ajax/.default/script.js?v=2.0.0"></script>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>