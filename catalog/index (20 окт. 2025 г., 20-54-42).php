<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Каталог товаров EDS");
$APPLICATION->SetPageProperty("description", "Профессиональное оборудование для распределения электроэнергии и управления сигналами");
?>

<section class="edsys-catalog-page">
    <div class="edsys-catalog__container">
        <?$APPLICATION->IncludeComponent(
            "bitrix:catalog.section.list",
            "edsys_categories",
            array(
                "IBLOCK_TYPE" => "catalog",
                "IBLOCK_ID" => "2", // ID инфоблока каталога
                "SECTION_ID" => "",
                "SECTION_CODE" => "",
                "COUNT_ELEMENTS" => "Y",
                "TOP_DEPTH" => "2",
                "SECTION_FIELDS" => array("", ""),
                "SECTION_USER_FIELDS" => array("", ""),
                "VIEW_MODE" => "LIST",
                "SHOW_PARENT_NAME" => "Y",
                "SECTION_URL" => "",
                "CACHE_TYPE" => "A",
                "CACHE_TIME" => "36000000",
                "CACHE_GROUPS" => "Y",
            ),
            false
        );?>
    </div>
</section>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>
