<?php
/**
 * Bitrix Cart Template - edsys_cart
 * 
 * @version 1.0.4
 * @date 2025-10-25
 * @description B2B E-commerce cart template with proper Bitrix integration
 * @author KW
 * @uri https://kowb.ru
 * 
 * @var array $arParams Component parameters
 * @var array $arResult Component result data
 * @var CBitrixComponentTemplate $this Template instance
 * @global CMain $APPLICATION Global application object
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);

$templateFolder = $this->GetFolder();
$arResult['TEMPLATE_FOLDER'] = $templateFolder;

$isCartEmpty = empty($arResult['ITEMS']) || $arResult['ITEMS_COUNT'] === 0;
?>

<section class="edsys-cart" id="edsys-cart-section">
    <div class="edsys-cart__container">
        
        <?php if ($isCartEmpty): ?>
            <div class="edsys-cart__empty">
                <div class="edsys-cart__empty-icon">
                    <i class="ph ph-thin ph-shopping-cart" aria-hidden="true"></i>
                </div>
                <h1 class="edsys-cart__empty-title">
                    <?= Loc::getMessage('EDSYS_CART_EMPTY_TITLE') ?>
                </h1>
                <p class="edsys-cart__empty-text">
                    <?= Loc::getMessage('EDSYS_CART_EMPTY_TEXT') ?>
                </p>
                <a href="<?= isset($arParams['PATH_TO_CATALOG']) ? htmlspecialchars($arParams['PATH_TO_CATALOG']) : '/catalog/' ?>" 
                   class="edsys-cart__empty-btn">
                    <?= Loc::getMessage('EDSYS_CART_GO_CATALOG') ?>
                </a>
            </div>
        <?php else: ?>
            
            <header class="edsys-cart__header">
                <h1 class="edsys-cart__title">
                    <?= Loc::getMessage('EDSYS_CART_TITLE') ?>
                </h1>
                <div class="edsys-cart__count">
                    <span class="edsys-cart__count-value"><?= $arResult['ITEMS_COUNT'] ?></span>
                    <?= Loc::getMessage('EDSYS_CART_ITEMS') ?>
                </div>
            </header>

            <div class="edsys-cart__layout">
                
                <div class="edsys-cart__main">
                    
                    <div class="edsys-cart__select-all">
                        <label class="edsys-cart__checkbox-label">
                            <input type="checkbox" 
                                   class="edsys-cart__checkbox" 
                                   id="edsys-cart-select-all"
                                   checked
                                   aria-label="<?= Loc::getMessage('EDSYS_CART_SELECT_ALL') ?>">
                            <span class="edsys-cart__checkbox-text">
                                <?= Loc::getMessage('EDSYS_CART_SELECT_ALL') ?>
                            </span>
                        </label>
                        <button type="button" 
                                class="edsys-cart__clear-selected"
                                id="edsys-cart-clear-selected"
                                aria-label="<?= Loc::getMessage('EDSYS_CART_DELETE_SELECTED') ?>">
                            <i class="ph ph-thin ph-trash" aria-hidden="true"></i>
                            <span><?= Loc::getMessage('EDSYS_CART_DELETE_SELECTED') ?></span>
                        </button>
                    </div>

                    <div class="edsys-cart__items">
                    <?php foreach ($arResult['ITEMS'] as $arItem): 
                        $itemId = intval($arItem['ID']);
                        $productId = intval($arItem['PRODUCT_ID']);
                        $quantity = floatval($arItem['QUANTITY']);
                        $price = floatval($arItem['PRICE']);
                        $sum = floatval($arItem['SUM']);
                        $currency = htmlspecialchars($arItem['CURRENCY']);
                        
                        $picture = '/local/templates/edsys_template/images/no-photo.svg';
                        if (!empty($arItem['PREVIEW_PICTURE']['SRC'])) {
                            $picture = htmlspecialchars($arItem['PREVIEW_PICTURE']['SRC']);
                        } elseif (!empty($arItem['DETAIL_PICTURE']['SRC'])) {
                            $picture = htmlspecialchars($arItem['DETAIL_PICTURE']['SRC']);
                        }
                        
                        $name = htmlspecialchars($arItem['NAME']);
                        $detailPageUrl = !empty($arItem['DETAIL_PAGE_URL']) ? htmlspecialchars($arItem['DETAIL_PAGE_URL']) : '#';
                        
                        $priceFormatted = number_format($price, 2, '.', ' ');
                        $sumFormatted = number_format($sum, 2, '.', ' ');
                    ?>
                        
                        <article class="edsys-cart__item" 
                                 data-item-id="<?= $itemId ?>"
                                 data-product-id="<?= $productId ?>">
                            
                            <div class="edsys-cart__item-select">
                                <label class="edsys-cart__checkbox-label">
                                    <input type="checkbox" 
                                           class="edsys-cart__checkbox edsys-cart__item-checkbox" 
                                           data-item-id="<?= $itemId ?>"
                                           checked
                                           aria-label="<?= Loc::getMessage('EDSYS_CART_SELECT_ITEM') ?> <?= $name ?>">
                                </label>
                            </div>

                            <div class="edsys-cart__item-image">
                                <a href="<?= $detailPageUrl ?>" 
                                   class="edsys-cart__item-link"
                                   aria-label="<?= $name ?>">
                                    <img src="<?= $picture ?>" 
                                         alt="<?= $name ?>"
                                         width="120"
                                         height="120"
                                         loading="lazy"
                                         class="edsys-cart__item-img">
                                </a>
                            </div>

                            <div class="edsys-cart__item-info">
                                <h2 class="edsys-cart__item-name">
                                    <a href="<?= $detailPageUrl ?>" class="edsys-cart__item-link">
                                        <?= $name ?>
                                    </a>
                                </h2>
                                
                                <?php if (!empty($arItem['PROPS'])): ?>
                                    <dl class="edsys-cart__item-props">
                                        <?php foreach ($arItem['PROPS'] as $prop): 
                                            if (!empty($prop['NAME']) && !empty($prop['VALUE'])): ?>
                                                <div class="edsys-cart__item-prop">
                                                    <dt class="edsys-cart__item-prop-name">
                                                        <?= htmlspecialchars($prop['NAME']) ?>:
                                                    </dt>
                                                    <dd class="edsys-cart__item-prop-value">
                                                        <?= htmlspecialchars($prop['VALUE']) ?>
                                                    </dd>
                                                </div>
                                            <?php endif;
                                        endforeach; ?>
                                    </dl>
                                <?php endif; ?>

                                <div class="edsys-cart__item-mobile-controls">
                                    <div class="edsys-cart__item-mobile-price">
                                        <span class="edsys-cart__item-price-value">
                                            <?= $priceFormatted ?>
                                        </span>
                                        <span class="edsys-cart__item-currency"><?= $currency ?></span>
                                    </div>
                                    
                                    <div class="edsys-cart__item-quantity">
                                        <button type="button" 
                                                class="edsys-cart__qty-btn edsys-cart__qty-minus"
                                                data-item-id="<?= $itemId ?>"
                                                aria-label="<?= Loc::getMessage('EDSYS_CART_DECREASE_QTY') ?>">
                                            <i class="ph ph-thin ph-minus" aria-hidden="true"></i>
                                        </button>
                                        <input type="number" 
                                               class="edsys-cart__qty-input"
                                               data-item-id="<?= $itemId ?>"
                                               value="<?= $quantity ?>"
                                               min="1"
                                               step="1"
                                               aria-label="<?= Loc::getMessage('EDSYS_CART_QUANTITY') ?>">
                                        <button type="button" 
                                                class="edsys-cart__qty-btn edsys-cart__qty-plus"
                                                data-item-id="<?= $itemId ?>"
                                                aria-label="<?= Loc::getMessage('EDSYS_CART_INCREASE_QTY') ?>">
                                            <i class="ph ph-thin ph-plus" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="edsys-cart__item-price">
                                <span class="edsys-cart__item-price-value">
                                    <?= $priceFormatted ?>
                                </span>
                                <span class="edsys-cart__item-currency"><?= $currency ?></span>
                            </div>

                            <div class="edsys-cart__item-quantity edsys-cart__item-quantity--desktop">
                                <button type="button" 
                                        class="edsys-cart__qty-btn edsys-cart__qty-minus"
                                        data-item-id="<?= $itemId ?>"
                                        aria-label="<?= Loc::getMessage('EDSYS_CART_DECREASE_QTY') ?>">
                                    <i class="ph ph-thin ph-minus" aria-hidden="true"></i>
                                </button>
                                <input type="number" 
                                       class="edsys-cart__qty-input"
                                       data-item-id="<?= $itemId ?>"
                                       value="<?= $quantity ?>"
                                       min="1"
                                       step="1"
                                       aria-label="<?= Loc::getMessage('EDSYS_CART_QUANTITY') ?>">
                                <button type="button" 
                                        class="edsys-cart__qty-btn edsys-cart__qty-plus"
                                        data-item-id="<?= $itemId ?>"
                                        aria-label="<?= Loc::getMessage('EDSYS_CART_INCREASE_QTY') ?>">
                                    <i class="ph ph-thin ph-plus" aria-hidden="true"></i>
                                </button>
                            </div>

                            <div class="edsys-cart__item-total">
                                <span class="edsys-cart__item-total-value" data-item-id="<?= $itemId ?>">
                                    <?= $sumFormatted ?> ₽
                                </span>
                            </div>

                            <div class="edsys-cart__item-actions">
                                <button type="button" 
                                        class="edsys-cart__item-delete"
                                        data-item-id="<?= $itemId ?>"
                                        aria-label="<?= Loc::getMessage('EDSYS_CART_DELETE_ITEM') ?>">
                                    <i class="ph ph-thin ph-trash" aria-hidden="true"></i>
                                </button>
                            </div>
                        </article>

                    <?php endforeach; ?>
                    </div>
                </div>

                <aside class="edsys-cart__summary" aria-label="<?= Loc::getMessage('EDSYS_CART_ORDER_SUMMARY') ?>">
                    <div class="edsys-cart__summary-content">
                        
                        <h2 class="edsys-cart__summary-title">
                            <?= Loc::getMessage('EDSYS_CART_ORDER_SUMMARY') ?>
                        </h2>

                        <dl class="edsys-cart__summary-list">
                            <div class="edsys-cart__summary-row">
                                <dt class="edsys-cart__summary-label">
                                    <?= Loc::getMessage('EDSYS_CART_ITEMS_TOTAL') ?>:
                                </dt>
                                <dd class="edsys-cart__summary-value" id="edsys-cart-items-count">
                                    <?= $arResult['ITEMS_COUNT'] ?>
                                </dd>
                            </div>

                            <?php if (!empty($arResult['BASE_TOTAL']) && $arResult['BASE_TOTAL'] > $arResult['TOTAL_PRICE']): ?>
                            <div class="edsys-cart__summary-row">
                                <dt class="edsys-cart__summary-label">
                                    <?= Loc::getMessage('EDSYS_CART_SUBTOTAL') ?>:
                                </dt>
                                <dd class="edsys-cart__summary-value" id="edsys-cart-subtotal">
                                    <?= number_format($arResult['BASE_TOTAL'], 2, '.', ' ') ?> ₽
                                </dd>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($arResult['DISCOUNT_PRICE']) && $arResult['DISCOUNT_PRICE'] > 0): ?>
                                <div class="edsys-cart__summary-row edsys-cart__summary-row--discount">
                                    <dt class="edsys-cart__summary-label">
                                        <?= Loc::getMessage('EDSYS_CART_DISCOUNT') ?>:
                                    </dt>
                                    <dd class="edsys-cart__summary-value edsys-cart__summary-value--discount">
                                        -<?= number_format($arResult['DISCOUNT_PRICE'], 2, '.', ' ') ?> ₽
                                    </dd>
                                </div>
                            <?php endif; ?>

                            <div class="edsys-cart__summary-row edsys-cart__summary-row--total">
                                <dt class="edsys-cart__summary-label">
                                    <?= Loc::getMessage('EDSYS_CART_TOTAL') ?>:
                                </dt>
                                <dd class="edsys-cart__summary-value edsys-cart__summary-value--total" id="edsys-cart-total">
                                    <?= number_format($arResult['TOTAL_PRICE'], 2, '.', ' ') ?> ₽
                                </dd>
                            </div>
                        </dl>

                        <div class="edsys-cart__promo">
                            <button type="button" 
                                    class="edsys-cart__promo-toggle"
                                    id="edsys-cart-promo-toggle"
                                    aria-expanded="false"
                                    aria-controls="edsys-cart-promo-form">
                                <i class="ph ph-thin ph-ticket" aria-hidden="true"></i>
                                <span><?= Loc::getMessage('EDSYS_CART_PROMO_CODE') ?></span>
                                <i class="ph ph-thin ph-caret-down edsys-cart__promo-arrow" aria-hidden="true"></i>
                            </button>
                            <div class="edsys-cart__promo-form" id="edsys-cart-promo-form" aria-hidden="true">
                                <input type="text" 
                                       class="edsys-cart__promo-input"
                                       id="edsys-cart-promo-input"
                                       placeholder="<?= Loc::getMessage('EDSYS_CART_PROMO_PLACEHOLDER') ?>"
                                       aria-label="<?= Loc::getMessage('EDSYS_CART_PROMO_CODE') ?>">
                                <button type="button" 
                                        class="edsys-cart__promo-apply"
                                        id="edsys-cart-promo-apply">
                                    <?= Loc::getMessage('EDSYS_CART_PROMO_APPLY') ?>
                                </button>
                            </div>
                        </div>

                        <a href="<?= isset($arParams['PATH_TO_ORDER']) ? htmlspecialchars($arParams['PATH_TO_ORDER']) : '/personal/order/' ?>" 
                           class="edsys-cart__checkout-btn"
                           id="edsys-cart-checkout">
                            <?= Loc::getMessage('EDSYS_CART_CHECKOUT') ?>
                            <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                        </a>

                        <a href="<?= isset($arParams['PATH_TO_CATALOG']) ? htmlspecialchars($arParams['PATH_TO_CATALOG']) : '/catalog/' ?>" 
                           class="edsys-cart__continue-shopping">
                            <i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
                            <?= Loc::getMessage('EDSYS_CART_CONTINUE_SHOPPING') ?>
                        </a>
                    </div>
                </aside>

            </div>

        <?php endif; ?>
    </div>

    <div class="edsys-cart__loading" id="edsys-cart-loading" aria-hidden="true">
        <div class="edsys-cart__loader">
            <div class="edsys-cart__loader-spinner"></div>
        </div>
    </div>
</section>