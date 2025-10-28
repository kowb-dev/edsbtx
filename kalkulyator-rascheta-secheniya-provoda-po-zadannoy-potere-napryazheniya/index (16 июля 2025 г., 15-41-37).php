<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Калькулятор расчета сечения провода по заданной потере напряжения");
$APPLICATION->SetPageProperty("description", "Калькулятор расчета сечения провода по заданной потере напряжения - страница EDS");
$APPLICATION->SetPageProperty("keywords", "EDS, Калькулятор расчета сечения провода по заданной потере напряжения");
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
            <h1 class="edsys-section__title">Калькулятор расчета сечения провода по заданной потере напряжения</h1>
            <p class="edsys-section__subtitle">Эта страница содержит информацию о разделе "Калькулятор расчета сечения провода по заданной потере напряжения"</p>
        </div>

        <div class="edsys-section__content">
            <p>Здесь будет подробная информация по разделу "Калькулятор расчета сечения провода по заданной потере напряжения".</p>
        </div>
    </div>
</div>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>