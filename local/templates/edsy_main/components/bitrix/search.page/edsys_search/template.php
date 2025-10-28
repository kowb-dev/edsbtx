<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */

// 1. Collect item IDs from search results
$elementIds = [];
if (!empty($arResult["SEARCH"]) && is_array($arResult["SEARCH"])) {
    foreach ($arResult["SEARCH"] as $arItem) {
        if ($arItem["MODULE_ID"] == "iblock" && (int)$arItem["ITEM_ID"] > 0) {
            $elementIds[] = $arItem["ITEM_ID"];
        }
    }
}

$products = [];
if (!empty($elementIds) && CModule::IncludeModule('iblock') && CModule::IncludeModule('catalog')) {
    // 2. Fetch product data in a single query
    $arSelect = ["ID", "IBLOCK_ID", "NAME", "DETAIL_PAGE_URL", "PREVIEW_PICTURE", "PROPERTY_CML2_ARTICLE"];
    $arFilter = ["ID" => $elementIds, "IBLOCK_ID" => 7]; // IBLOCK_ID = 7

    $rsElements = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);
    while ($ob = $rsElements->GetNextElement()) {
        $arFields = $ob->GetFields();
        
        // Get Price
        $price = CCatalogProduct::GetOptimalPrice($arFields['ID'], 1, $USER->GetUserGroupArray(), 'N');
        $arFields['PRICE_INFO'] = $price;
        
        // Get Image
        if ($arFields['PREVIEW_PICTURE']) {
            $arFields['PREVIEW_PICTURE'] = CFile::GetPath($arFields['PREVIEW_PICTURE']);
        } else {
            $arFields['PREVIEW_PICTURE'] = '/local/templates/edsy_main/images/no_photo.svg'; // Path to a placeholder
        }
        
        $products[$arFields['ID']] = $arFields;
    }
}
?>

<div class="edsys-search-page">
    <?if (!empty($arResult["SEARCH"])) :?>
        <h1 class="edsys-search-page__title">Результаты поиска по запросу "<?= htmlspecialchars($arResult["REQUEST"]["QUERY"]) ?>"</h1>
        
        <? if($arParams["DISPLAY_TOP_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>

        <div class="edsys-search-results-list">
            <? foreach ($arResult["SEARCH"] as $arItem): ?>
                <? 
                $product = null;
                if (isset($products[$arItem['ITEM_ID']])) {
                    $product = $products[$arItem['ITEM_ID']];
                }
                ?>
                <div class="edsys-search-list-item">
                    <? if ($product): ?>
                        <a href="<?= $arItem["URL"] ?>" class="edsys-search-list-item__image-link">
                            <img src="<?= $product["PREVIEW_PICTURE"] ?>" alt="<?= $product["NAME"] ?>" class="edsys-search-list-item__image">
                        </a>
                    <? endif; ?>
                    <div class="edsys-search-list-item__info">
                        <h3 class="edsys-search-list-item__title">
                            <a href="<?= $arItem["URL"] ?>"><?= $arItem["TITLE_FORMATED"] ?></a>
                        </h3>
                        <? if ($product && !empty($product["PROPERTY_CML2_ARTICLE_VALUE"])): ?>
                            <div class="edsys-search-list-item__article">
                                Арт: <?= $product["PROPERTY_CML2_ARTICLE_VALUE"] ?>
                            </div>
                        <? endif; ?>
                        <div class="edsys-search-list-item__body">
                            <?= $arItem["BODY_FORMATED"] ?>
                        </div>
                        <div class="edsys-search-list-item__footer">
                            <? if ($product && isset($product['PRICE_INFO']['RESULT_PRICE'])): ?>
                                <div class="edsys-search-list-item__price">
                                    <?= CurrencyFormat($product['PRICE_INFO']['RESULT_PRICE']['DISCOUNT_PRICE'], $product['PRICE_INFO']['RESULT_PRICE']['CURRENCY']); ?>
                                </div>
                            <? else: ?>
                                <div class="edsys-search-list-item__price">
                                    Цена по запросу
                                </div>
                            <? endif; ?>
                             <a href="<?= $arItem["URL"] ?>" class="edsys-btn">Подробнее</a>
                        </div>
                    </div>
                </div>
            <? endforeach; ?>
        </div>

        <? if($arParams["DISPLAY_BOTTOM_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>

    <? else: ?>
        <? ShowNote(GetMessage("SEARCH_NOTHING_TO_FOUND")); ?>
    <? endif; ?>
</div>
