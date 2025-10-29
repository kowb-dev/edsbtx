<?php
/**
 * Search Page Template
 * Enhanced with clickable cards and empty state
 * 
 * @version 2.0.0
 * @author KW
 * @link https://kowb.ru
 * Path: /local/templates/edsy_main/components/bitrix/search.page/edsys_search/template.php
 */

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
                <a href="<?= $arItem["URL"] ?>" class="edsys-search-list-item">
                    <? if ($product): ?>
                        <div class="edsys-search-list-item__image-link">
                            <img src="<?= $product["PREVIEW_PICTURE"] ?>" alt="<?= $product["NAME"] ?>" class="edsys-search-list-item__image">
                        </div>
                    <? endif; ?>
                    <div class="edsys-search-list-item__info">
                        <h3 class="edsys-search-list-item__title">
                            <?= $arItem["TITLE_FORMATED"] ?>
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
                        </div>
                    </div>
                </a>
            <? endforeach; ?>
        </div>

        <? if($arParams["DISPLAY_BOTTOM_PAGER"] != "N") echo $arResult["NAV_STRING"]; ?>

    <? else: ?>
        <div class="edsys-search-empty">
            <div class="edsys-search-empty__icon">
                <i class="ph ph-thin ph-magnifying-glass"></i>
            </div>
            <h2 class="edsys-search-empty__title">По вашему запросу ничего не найдено</h2>
            <p class="edsys-search-empty__text">
                К сожалению, по вашему запросу "<?= htmlspecialchars($arResult["REQUEST"]["QUERY"]) ?>" ничего не найдено. 
                Попробуйте изменить запрос или воспользуйтесь одним из предложений ниже.
            </p>
            
            <div class="edsys-search-suggestions">
                <h3 class="edsys-search-suggestions__title">Попробуйте:</h3>
                <ul class="edsys-search-suggestions__list">
                    <li>Проверьте правильность написания слов</li>
                    <li>Используйте более общие ключевые слова</li>
                    <li>Попробуйте использовать синонимы</li>
                    <li>Поищите товар по артикулу</li>
                </ul>
            </div>
            
            <div class="edsys-search-popular">
                <h3 class="edsys-search-popular__title">Популярные категории</h3>
                <div class="edsys-search-popular__grid">
                    <a href="/catalog/raspredelitelnye-ustroystva/" class="edsys-search-popular__item">
                        <i class="ph ph-thin ph-plugs"></i>
                        <span>Распределительные устройства</span>
                    </a>
                    <a href="/catalog/kabel-silovoy/" class="edsys-search-popular__item">
                        <i class="ph ph-thin ph-plugs-connected"></i>
                        <span>Силовой кабель</span>
                    </a>
                    <a href="/catalog/lyuchki-sczenicheskie/" class="edsys-search-popular__item">
                        <i class="ph ph-thin ph-door"></i>
                        <span>Лючки сценические</span>
                    </a>
                    <a href="/catalog/korobki-kommutatsionnye/" class="edsys-search-popular__item">
                        <i class="ph ph-thin ph-package"></i>
                        <span>Коробки коммутационные</span>
                    </a>
                </div>
            </div>
            
            <div class="edsys-search-actions">
                <a href="/catalog/" class="edsys-btn edsys-btn--primary">
                    Перейти в каталог
                </a>
                <a href="/kontakty/" class="edsys-btn edsys-btn--outline">
                    Связаться с нами
                </a>
            </div>
        </div>
        
        <style>
        .edsys-search-empty {
            max-width: 1000px;
            margin: clamp(2rem, 5vw, 4rem) auto;
            text-align: center;
            padding: clamp(1rem, 3vw, 2rem);
        }
        
        .edsys-search-empty__icon {
            font-size: clamp(3rem, 8vw, 5rem);
            color: #ddd;
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
        }
        
        .edsys-search-empty__title {
            font-size: clamp(1.5rem, 3vw, 2rem);
            margin-bottom: clamp(0.75rem, 2vw, 1rem);
            color: var(--edsys-text, #21242D);
        }
        
        .edsys-search-empty__text {
            font-size: clamp(1rem, 2vw, 1.125rem);
            color: #666;
            line-height: 1.6;
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .edsys-search-suggestions {
            background: #f8f9fa;
            border-radius: clamp(0.5rem, 1vw, 1rem);
            padding: clamp(1rem, 2vw, 1.5rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
            text-align: left;
        }
        
        .edsys-search-suggestions__title {
            font-size: clamp(1.125rem, 2vw, 1.25rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1rem);
            color: var(--edsys-text, #21242D);
        }
        
        .edsys-search-suggestions__list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .edsys-search-suggestions__list li {
            padding: 0.5rem 0;
            padding-left: 1.5rem;
            position: relative;
            color: #555;
        }
        
        .edsys-search-suggestions__list li::before {
            content: '→';
            position: absolute;
            left: 0;
            color: var(--edsys-accent, #007bff);
        }
        
        .edsys-search-popular {
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .edsys-search-popular__title {
            font-size: clamp(1.125rem, 2vw, 1.25rem);
            margin-bottom: clamp(1rem, 2vw, 1.5rem);
            color: var(--edsys-text, #21242D);
        }
        
        .edsys-search-popular__grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(min(200px, 100%), 1fr));
            gap: clamp(0.75rem, 2vw, 1rem);
            margin-bottom: clamp(1.5rem, 3vw, 2rem);
        }
        
        .edsys-search-popular__item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 0.75rem;
            padding: clamp(1rem, 2vw, 1.5rem);
            background: #fff;
            border: 1px solid #e5e5e5;
            border-radius: clamp(0.5rem, 1vw, 0.75rem);
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }
        
        @media (hover: hover) {
            .edsys-search-popular__item:hover {
                border-color: var(--edsys-accent, #007bff);
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                transform: translateY(-2px);
            }
        }
        
        .edsys-search-popular__item i {
            font-size: clamp(1.5rem, 3vw, 2rem);
            color: var(--edsys-accent, #007bff);
        }
        
        .edsys-search-popular__item span {
            font-size: clamp(0.875rem, 1.5vw, 1rem);
            text-align: center;
            font-weight: 500;
        }
        
        .edsys-search-actions {
            display: flex;
            flex-wrap: wrap;
            gap: clamp(0.75rem, 2vw, 1rem);
            justify-content: center;
        }
        
        .edsys-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: clamp(0.75rem, 1.5vw, 1rem) clamp(1.5rem, 3vw, 2rem);
            border-radius: clamp(0.5rem, 1vw, 0.75rem);
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .edsys-btn--primary {
            background: var(--edsys-accent, #007bff);
            color: #fff;
            border: 2px solid var(--edsys-accent, #007bff);
        }
        
        @media (hover: hover) {
            .edsys-btn--primary:hover {
                background: var(--edsys-accent-dark, #0056b3);
                border-color: var(--edsys-accent-dark, #0056b3);
            }
        }
        
        .edsys-btn--outline {
            background: transparent;
            color: var(--edsys-accent, #007bff);
            border: 2px solid var(--edsys-accent, #007bff);
        }
        
        @media (hover: hover) {
            .edsys-btn--outline:hover {
                background: var(--edsys-accent, #007bff);
                color: #fff;
            }
        }
        </style>
    <? endif; ?>
</div>
