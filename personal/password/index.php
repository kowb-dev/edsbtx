<?php
/**
 * Страница смены пароля
 * Файл: /personal/password/index.php
 * 
 * @version 1.4.0
 * @author KW https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=/personal/password/');
    die();
}

$APPLICATION->SetTitle("Смена пароля");

$APPLICATION->SetAdditionalCSS("/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css?v=1.3.0");
$APPLICATION->AddHeadScript("/local/templates/.default/components/bitrix/sale.personal.section/.default/script.js?v=1.2.0");

$arUser = $USER->GetByID($USER->GetID())->Fetch();
$userCompany = $arUser['WORK_COMPANY'] ?? '';

include($_SERVER["DOCUMENT_ROOT"] . "/personal/includes/menu.php");

$successMessage = '';
$showForm = true;

// Check for success parameter from redirect
if (isset($_GET['change']) && $_GET['change'] === 'success') {
    $successMessage = 'Пароль успешно изменен';
    $showForm = false;
}
?>

<main class="edsys-account__content">
    <header class="edsys-content-header">
        <h1 class="edsys-dashboard__title">Смена пароля</h1>
        <p class="edsys-dashboard__subtitle">Обновление пароля для входа</p>
    </header>

    <?php if ($successMessage): ?>
        <div class="edsys-success-message">
            <?= htmlspecialcharsbx($successMessage) ?>
        </div>
        <div class="edsys-auth-footer">
            <a href="/personal/profile/">Вернуться в профиль</a>
        </div>
    <?php endif; ?>

    <?php if ($showForm): ?>
        <?php
        $APPLICATION->IncludeComponent(
            "bitrix:system.auth.changepasswd",
            ".default",
            [
                "AUTH_URL" => "/auth/",
                "SUCCESS_URL" => "/personal/password/?change=success",
            ],
            false
        );
        ?>
    <?php endif; ?>
</main>

        </div>
    </div>
</section>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>