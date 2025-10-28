<?php
/**
 * Bitrix Cart Component Epilog - edsys_cart
 * 
 * @version 1.0.3
 * @date 2025-10-18
 * @description Registers CSS and JS files after component execution
 * @author KW
 * @uri https://kowb.ru
 * 
 * @var array $arParams Component parameters
 * @var array $arResult Component result data
 * @var string $templateFolder Template folder path (set by template engine)
 * @global CMain $APPLICATION Global application object
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

// Get template folder from $arResult (set in template.php)
$templateFolder = $arResult['TEMPLATE_FOLDER'] ?? '';

// Fallback: construct path if not available
if (empty($templateFolder)) {
    $templateFolder = '/local/templates/' . SITE_TEMPLATE_ID . '/components/bitrix/sale.basket.basket/edsys_cart';
}

// Register CSS
$APPLICATION->SetAdditionalCSS($templateFolder . '/style.css');

// Register JavaScript
Asset::getInstance()->addJs($templateFolder . '/script.js');

// Add Bitrix AJAX script for proper API functionality
CJSCore::Init(['ajax']);

// Pass data to JavaScript
?>
<script>
// Pass basket data to JavaScript
window.edsysCart = {
    itemsCount: <?= intval($arResult['ITEMS_COUNT'] ?? 0) ?>,
    total: <?= floatval($arResult['TOTAL_PRICE'] ?? 0) ?>,
    currency: '<?= CUtil::JSEscape($arResult['CURRENCY'] ?? 'RUB') ?>',
    templateFolder: '<?= CUtil::JSEscape($templateFolder) ?>',
    siteId: '<?= CUtil::JSEscape(SITE_ID) ?>'
};
</script>