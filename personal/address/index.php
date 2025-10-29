<?php
/**
 * Страница адресов доставки
 * Файл: /personal/address/index.php
 * 
 * @version 1.2.0
 * @author KW https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/address/');
    die();
}

$APPLICATION->SetTitle("Адреса доставки");

$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css?v=1.2.0");
$APPLICATION->AddHeadScript("/local/templates/.default/components/bitrix/sale.personal.section/.default/script.js?v=1.2.0");

$arUser = $USER->GetByID($USER->GetID())->Fetch();
$userCompany = $arUser['WORK_COMPANY'] ?? '';

include($_SERVER["DOCUMENT_ROOT"] . "/personal/includes/menu.php");
?>

<main class="edsys-account__content">
    <header class="edsys-content-header">
        <h1 class="edsys-dashboard__title">Адреса доставки</h1>
        <p class="edsys-dashboard__subtitle">Управление адресами для заказов</p>
    </header>

    <?$APPLICATION->IncludeComponent(
        "bitrix:sale.personal.profile",
        "",
        array(
            "SET_TITLE" => "N",
            "PER_PAGE" => "20",
            "USE_AJAX_LOCATIONS" => "Y",
            "AJAX_OPTION_ADDITIONAL" => "",
            "PATH_TO_DETAIL" => "/personal/address/#ID#/",
            "CACHE_TYPE" => "A",
            "CACHE_TIME" => "3600"
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