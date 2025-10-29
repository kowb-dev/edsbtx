<?php
/**
 * Live Search AJAX Results Template
 * 
 * @version 2.0.0
 * @author KW
 * @link https://kowb.ru
 * Path: /local/templates/edsy_main/components/bitrix/search.title/visual_custom/ajax.php
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) {
    die();
}

if (empty($arResult['CATEGORIES']) || !$arResult['CATEGORIES_ITEMS_EXISTS']) {
    return;
}

$elementIDs = [];
foreach ($arResult['CATEGORIES'] as $category_id => $arCategory) {
    foreach ($arCategory['ITEMS'] as $i => $arItem) {
        if (isset($arResult['ELEMENTS'][$arItem['ITEM_ID']])) {
            $elementIDs[] = $arItem['ITEM_ID'];
        }
    }
}

$arProducts = [];
if (!empty($elementIDs) && CModule::IncludeModule('iblock')) {
    $db_props = CIBlockElement::GetList(
        [],
        ['IBLOCK_ID' => 7, 'ID' => $elementIDs],
        false,
        false,
        ['ID', 'PREVIEW_TEXT', 'PROPERTY_CML2_ARTICLE', 'PROPERTY_ARTICLE', 'PROPERTY_ARTNUMBER']
    );
    while ($ar_props = $db_props->Fetch()) {
        $article = $ar_props['PROPERTY_CML2_ARTICLE_VALUE'] 
            ?? $ar_props['PROPERTY_ARTICLE_VALUE'] 
            ?? $ar_props['PROPERTY_ARTNUMBER_VALUE'] 
            ?? '';
        
        $preview = strip_tags($ar_props['PREVIEW_TEXT'] ?? '');
        if (mb_strlen($preview) > 150) {
            $preview = mb_substr($preview, 0, 150) . '...';
        }
        
        $arProducts[$ar_props['ID']] = [
            'ARTICLE' => $article,
            'PREVIEW' => $preview
        ];
    }
}
?>

<div class="edsys-live-search-results">
    <?php foreach ($arResult['CATEGORIES'] as $category_id => $arCategory): ?>
        <?php foreach ($arCategory['ITEMS'] as $i => $arItem): ?>
            
            <?php if ($category_id === 'all'): ?>
                <div class="edsys-live-search-divider"></div>
                <a href="<?= htmlspecialchars($arItem['URL']) ?>" class="edsys-live-search-all">
                    <span class="edsys-live-search-all__text"><?= htmlspecialchars($arItem['NAME']) ?></span>
                    <i class="ph ph-thin ph-arrow-right"></i>
                </a>
                
            <?php elseif (isset($arResult['ELEMENTS'][$arItem['ITEM_ID']])): 
                $arElement = $arResult['ELEMENTS'][$arItem['ITEM_ID']];
                $productData = $arProducts[$arElement['ID']] ?? null;
                ?>
                
                <a href="<?= htmlspecialchars($arItem['URL']) ?>" class="edsys-live-search-item">
                    <?php if (is_array($arElement['PICTURE'])): ?>
                        <div class="edsys-live-search-item__image">
                            <img 
                                src="<?= htmlspecialchars($arElement['PICTURE']['src']) ?>" 
                                alt="<?= htmlspecialchars($arItem['NAME']) ?>"
                                width="64"
                                height="64"
                                loading="lazy"
                            >
                        </div>
                    <?php else: ?>
                        <div class="edsys-live-search-item__image edsys-live-search-item__image--placeholder">
                            <i class="ph ph-thin ph-image"></i>
                        </div>
                    <?php endif; ?>
                    
                    <div class="edsys-live-search-item__content">
                        <div class="edsys-live-search-item__title">
                            <?= $arItem['NAME'] ?>
                        </div>
                        
                        <?php if ($productData && !empty($productData['ARTICLE'])): ?>
                            <div class="edsys-live-search-item__article">
                                Артикул: <strong><?= htmlspecialchars($productData['ARTICLE']) ?></strong>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($productData && !empty($productData['PREVIEW'])): ?>
                            <div class="edsys-live-search-item__preview">
                                <?= htmlspecialchars($productData['PREVIEW']) ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </a>
                
            <?php else: ?>
                
                <a href="<?= htmlspecialchars($arItem['URL']) ?>" class="edsys-live-search-item edsys-live-search-item--other">
                    <div class="edsys-live-search-item__content">
                        <div class="edsys-live-search-item__title">
                            <?= $arItem['NAME'] ?>
                        </div>
                    </div>
                </a>
                
            <?php endif; ?>
            
        <?php endforeach; ?>
    <?php endforeach; ?>
</div>

<style>
.edsys-live-search-results {
    display: flex;
    flex-direction: column;
    max-height: min(70vh, 500px);
    overflow-y: auto;
    background: #fff;
    border-radius: clamp(0.5rem, 1vw, 0.75rem);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.edsys-live-search-item {
    display: flex;
    align-items: flex-start;
    gap: clamp(0.75rem, 2vw, 1rem);
    padding: clamp(0.75rem, 2vw, 1rem);
    text-decoration: none;
    color: inherit;
    border-bottom: 1px solid #f0f0f0;
    transition: background-color 0.2s ease;
    cursor: pointer;
}

@media (hover: hover) {
    .edsys-live-search-item:hover {
        background-color: #f8f9fa;
    }
}

.edsys-live-search-item:last-child {
    border-bottom: none;
}

.edsys-live-search-item__image {
    flex-shrink: 0;
    width: 64px;
    height: 64px;
    border-radius: 0.5rem;
    overflow: hidden;
    background: #f5f5f5;
    display: flex;
    align-items: center;
    justify-content: center;
}

.edsys-live-search-item__image--placeholder {
    font-size: 1.5rem;
    color: #ccc;
}

.edsys-live-search-item__image img {
    width: 100%;
    height: 100%;
    object-fit: contain;
}

.edsys-live-search-item__content {
    flex: 1;
    min-width: 0;
}

.edsys-live-search-item__title {
    font-weight: 500;
    margin-bottom: 0.25rem;
    line-height: 1.4;
}

.edsys-live-search-item__article {
    font-size: 0.875rem;
    color: #666;
    margin-bottom: 0.25rem;
}

.edsys-live-search-item__preview {
    font-size: 0.75rem;
    color: #999;
    line-height: 1.4;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
}

.edsys-live-search-divider {
    height: 1px;
    background: #e0e0e0;
    margin: 0.5rem 0;
}

.edsys-live-search-all {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: clamp(0.75rem, 2vw, 1rem);
    text-decoration: none;
    color: var(--edsys-accent, #007bff);
    font-weight: 500;
    transition: background-color 0.2s ease;
}

@media (hover: hover) {
    .edsys-live-search-all:hover {
        background-color: #f8f9fa;
    }
}

.edsys-live-search-item--other {
    padding-left: 80px;
}
</style>
