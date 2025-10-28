<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Расчет потери напряжения");
$APPLICATION->SetPageProperty("description", "Расчет потери напряжения - страница EDS");
$APPLICATION->SetPageProperty("keywords", "EDS, Расчет потери напряжения");
?>

<!-- Хлебные крошки -->
<?php
$APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "custom",
    Array(
        "PATH" => "",
        "SITE_ID" => "s1",
        "START_FROM" => "0"
    )
);
?>

<div class="edsys-container">
    <div class="edsys-section">
        <div class="edsys-section__header">
            <h1 class="edsys-section__title">Расчет потери напряжения</h1>
            <p class="edsys-section__subtitle">Эта страница содержит информацию о разделе "Расчет потери напряжения"</p>
        </div>

        <div class="edsys-section__content">
            <p>Здесь будет подробная информация по разделу "Расчет потери напряжения".</p>
        </div>
    </div>
</div>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>