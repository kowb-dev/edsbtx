<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @var CBitrixComponentTemplate $this */

$this->setFrameMode(true);

if (!$arResult["NavShowAlways"]) {
    if ($arResult["NavRecordCount"] == 0 || ($arResult["NavPageCount"] == 1 && $arResult["NavShowAll"] == false)) {
        return;
    }
}

$strNavQueryString = ($arResult["NavQueryString"] != "" ? $arResult["NavQueryString"] . "&" : "");
$strNavQueryStringFull = ($arResult["NavQueryString"] != "" ? "?" . $arResult["NavQueryString"] : "");
?>

<div class="edsys-pagination">
    <ul class="edsys-pagination__list">
        <? if ($arResult["NavPageNomer"] > 1): ?>
            <li class="edsys-pagination__item edsys-pagination__item--arrow">
                <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] - 1) ?>" class="edsys-pagination__link">
                    <i class="ph ph-thin ph-caret-left"></i>
                </a>
            </li>
        <? else: ?>
            <li class="edsys-pagination__item edsys-pagination__item--arrow edsys-pagination__item--disabled">
                <span class="edsys-pagination__link"><i class="ph ph-thin ph-caret-left"></i></span>
            </li>
        <? endif; ?>

        <? while ($arResult["nStartPage"] <= $arResult["nEndPage"]): ?>
            <? if ($arResult["nStartPage"] == $arResult["NavPageNomer"]): ?>
                <li class="edsys-pagination__item edsys-pagination__item--active">
                    <span class="edsys-pagination__link"><?= $arResult["nStartPage"] ?></span>
                </li>
            <? elseif ($arResult["nStartPage"] == 1 && $arResult["bSavePage"] == false): ?>
                <li class="edsys-pagination__item">
                    <a href="<?= $arResult["sUrlPath"] ?><?= $strNavQueryStringFull ?>" class="edsys-pagination__link"><?= $arResult["nStartPage"] ?></a>
                </li>
            <? else: ?>
                <li class="edsys-pagination__item">
                    <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["nStartPage"] ?>" class="edsys-pagination__link"><?= $arResult["nStartPage"] ?></a>
                </li>
            <? endif; ?>
            <? $arResult["nStartPage"]++; ?>
        <? endwhile; ?>

        <? if ($arResult["NavPageNomer"] < $arResult["NavPageCount"]): ?>
            <? if ($arResult["nEndPage"] < $arResult["NavPageCount"]): ?>
                <li class="edsys-pagination__item edsys-pagination__item--dots">
                    <span class="edsys-pagination__link">...</span>
                </li>
                <li class="edsys-pagination__item">
                    <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= $arResult["NavPageCount"] ?>" class="edsys-pagination__link"><?= $arResult["NavPageCount"] ?></a>
                </li>
            <? endif; ?>
            <li class="edsys-pagination__item edsys-pagination__item--arrow">
                <a href="<?= $arResult["sUrlPath"] ?>?<?= $strNavQueryString ?>PAGEN_<?= $arResult["NavNum"] ?>=<?= ($arResult["NavPageNomer"] + 1) ?>" class="edsys-pagination__link">
                    <i class="ph ph-thin ph-caret-right"></i>
                </a>
            </li>
        <? else: ?>
            <li class="edsys-pagination__item edsys-pagination__item--arrow edsys-pagination__item--disabled">
                <span class="edsys-pagination__link"><i class="ph ph-thin ph-caret-right"></i></span>
            </li>
        <? endif; ?>
    </ul>
</div>
