<?php
if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

/**
 * @var CBitrixComponentTemplate $this
 * @var array $arParams
 * @var array $arResult
 */

if (!empty($arResult['ITEMS'])) {
    $productIds = [];
    foreach ($arResult['ITEMS'] as $item) {
        $productIds[] = $item['ID'];
    }

    if (!empty($productIds)) {
        $arSelect = ['ID', 'IBLOCK_ID'];
        $arFilter = ['ID' => $productIds, 'IBLOCK_ID' => $arParams['IBLOCK_ID']];
        
        $dbElement = CIBlockElement::GetList([], $arFilter, false, false, $arSelect);

        $itemProperties = [];
        while ($obElement = $dbElement->GetNextElement()) {
            $arFields = $obElement->GetFields();
            $arProps = $obElement->GetProperties();
            $itemProperties[$arFields['ID']] = $arProps;
        }

        foreach ($arResult['ITEMS'] as &$item) {
            if (isset($itemProperties[$item['ID']])) {
                $item['PROPERTIES'] = $itemProperties[$item['ID']];
            }
        }
        unset($item);
    }
}
