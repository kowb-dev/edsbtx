<?php
/**
 * Страница списка заказов
 * Файл: /personal/orders/index.php
 * 
 * @version 1.2.0
 * @author KW https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/orders/');
    die();
}

$APPLICATION->SetTitle("Мои заказы");

$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css?v=1.2.0");
$APPLICATION->AddHeadScript("/local/templates/.default/components/bitrix/sale.personal.section/.default/script.js?v=1.2.0");

$arUser = $USER->GetByID($USER->GetID())->Fetch();
$userCompany = $arUser['WORK_COMPANY'] ?? '';

include($_SERVER["DOCUMENT_ROOT"] . "/personal/includes/menu.php");
?>

<main class="edsys-account__content">
    <header class="edsys-content-header">
        <h1 class="edsys-dashboard__title">Мои заказы</h1>
        <p class="edsys-dashboard__subtitle">История и статус ваших заказов</p>
    </header>

    <?$APPLICATION->IncludeComponent(
        "bitrix:sale.personal.order.list",
        "",
        array(
            "SEF_MODE" => "N",
            "ORDERS_PER_PAGE" => "20",
            "PATH_TO_DETAIL" => "/personal/orders/#ID#/",
            "PATH_TO_CANCEL" => "/personal/orders/#ID#/?cancel=Y",
            "PATH_TO_CATALOG" => "/catalog/",
            "PATH_TO_PAYMENT" => "/personal/orders/payment/",
            "SAVE_IN_SESSION" => "Y",
            "NAV_TEMPLATE" => "",
            "ACTIVE_DATE_FORMAT" => "d.m.Y",
            "HISTORIC_STATUSES" => array("F"),
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600",
            "CACHE_GROUPS" => "Y",
            "SET_TITLE" => "N"
        ),
        false
    );?>
</main>

        </div>
    </div>
</section>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>