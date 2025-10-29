<?php
/**
 * Страница профиля пользователя
 * Файл: /personal/profile/index.php
 *
 * @version 1.3.0
 * @author KW https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/profile/');
    die();
}

$APPLICATION->SetTitle("Личная информация");

$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css?v=1.2.0");
$APPLICATION->AddHeadScript("/local/templates/.default/components/bitrix/sale.personal.section/.default/script.js?v=1.2.0");

$arUser = $USER->GetByID($USER->GetID())->Fetch();
$userCompany = $arUser['WORK_COMPANY'] ?? '';

include($_SERVER["DOCUMENT_ROOT"] . "/personal/includes/menu.php");
?>

    <main class="edsys-account__content">
        <header class="edsys-content-header">
            <h1 class="edsys-dashboard__title">Личная информация</h1>
            <p class="edsys-dashboard__subtitle">Управление данными профиля и компании</p>
        </header>

        <?$APPLICATION->IncludeComponent(
                "bitrix:main.profile",
                ".default",
                array(
                        "SET_TITLE" => "N",
                        "AJAX_MODE" => "N",
                        "SEND_INFO" => "N",
                        "CHECK_RIGHTS" => "N",
                        "USER_PROPERTY_NAME" => ""
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