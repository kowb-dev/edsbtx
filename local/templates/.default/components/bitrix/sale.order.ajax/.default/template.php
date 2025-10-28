<?php
/**
 * Order Checkout Template
 * Component: sale.order.ajax
 *
 * @version 1.1.1
 * @author  KW
 * @uri     https://kowb.ru
 *
 * Path: /local/templates/.default/components/bitrix/sale.order.ajax/.default/template.php
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) { die(); }

$this->setFrameMode(true);

$templateFolder = $this->GetFolder();

/** @var array $arResult */
/** @var array $arParams */
?>
<form id="edsys-order-form"
      class="edsys-order"
      method="post"
      action="<?= $APPLICATION->GetCurPage() ?>"
      enctype="multipart/form-data">
    <?= bitrix_sessid_post() ?>

    <?php
    // person type resolve
    $personTypes = isset($arResult['PERSON_TYPE']) && is_array($arResult['PERSON_TYPE']) ? $arResult['PERSON_TYPE'] : [];
    $selectedPersonTypeId = (int)($arResult['USER_VALS']['PERSON_TYPE_ID'] ?? $arResult['PERSON_TYPE_ID'] ?? 0);
    if ($selectedPersonTypeId <= 0 && !empty($personTypes)) {
        foreach ($personTypes as $pt) {
            if (!empty($pt['CHECKED'])) { $selectedPersonTypeId = (int)$pt['ID']; break; }
        }
        if ($selectedPersonTypeId <= 0) {
            $first = reset($personTypes);
            $selectedPersonTypeId = (int)($first['ID'] ?? 0);
        }
    }

    // default pay system (hidden, выбор отключён на странице)
    $defaultPaySystemId = 0;
    if (!empty($arResult['PAY_SYSTEM']) && is_array($arResult['PAY_SYSTEM'])) {
        foreach ($arResult['PAY_SYSTEM'] as $ps) {
            if (!empty($ps['CHECKED'])) { $defaultPaySystemId = (int)$ps['ID']; break; }
        }
        if ($defaultPaySystemId <= 0) {
            $firstPs = reset($arResult['PAY_SYSTEM']);
            $defaultPaySystemId = (int)($firstPs['ID'] ?? 0);
        }
    }

    // normalize errors
    $errors = [];
    if (!empty($arResult['ERROR'])) {
        $errors = is_array($arResult['ERROR']) ? $arResult['ERROR'] : [$arResult['ERROR']];
    }

    // user profiles
    $userProfiles = $arResult['ORDER_PROP']['USER_PROFILES'] ?? [];

    // properties
    $propsY = $arResult['ORDER_PROP']['USER_PROPS_Y'] ?? [];
    $propsN = $arResult['ORDER_PROP']['USER_PROPS_N'] ?? [];
    $allProps = array_merge((array)$propsY, (array)$propsN);

    // section numbering
    $step = 1;
    ?>

    <input type="hidden" name="PERSON_TYPE" value="<?= (int)$selectedPersonTypeId ?>">
    <?php if ($defaultPaySystemId > 0): ?>
        <input type="hidden" name="PAY_SYSTEM_ID" value="<?= (int)$defaultPaySystemId ?>">
    <?php endif; ?>

    <div class="edsys-order__header">
        <h1 class="edsys-order__title">Оформление заказа</h1>
        <p class="edsys-order__subtitle">Заполните данные для оформления заказа</p>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="edsys-order__error" role="alert" aria-live="assertive">
            <i class="ph ph-thin ph-warning-circle" aria-hidden="true"></i>
            <div class="edsys-order__error-content">
                <?php foreach ($errors as $error): ?>
                    <p class="edsys-order__error-message"><?= htmlspecialchars($error, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="edsys-order__layout">
        <div class="edsys-order__main">

            <?php if (count($personTypes) > 1): ?>
                <section class="edsys-order__section edsys-order__section--person-type">
                    <div class="edsys-order__section-header">
                        <div class="edsys-order__section-number"><?= $step ?></div>
                        <h2 class="edsys-order__section-title">Тип плательщика</h2>
                    </div>
                    <?php $step++; ?>
                    <div class="edsys-order__section-content">
                        <div class="edsys-order__radios" role="radiogroup" aria-label="Тип плательщика">
                            <?php foreach ($personTypes as $pt): ?>
                                <?php
                                $ptId = (int)$pt['ID'];
                                $ptInputId = 'edsys-person-type-' . $ptId;
                                ?>
                                <label class="edsys-order__radio">
                                    <input type="radio"
                                           id="<?= $ptInputId ?>"
                                           name="PERSON_TYPE"
                                           value="<?= $ptId ?>"
                                           class="edsys-order__radio-input"
                                           <?= ($ptId === $selectedPersonTypeId) ? 'checked' : '' ?>>
                                    <span class="edsys-order__radio-text"><?= htmlspecialchars($pt['NAME'] ?? ('ID '.$ptId), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <?php if (!empty($userProfiles)): ?>
                <section class="edsys-order__section edsys-order__section--profiles">
                    <div class="edsys-order__section-header">
                        <div class="edsys-order__section-number"><?= $step ?></div>
                        <h2 class="edsys-order__section-title">Профили покупателя</h2>
                    </div>
                    <?php $step++; ?>
                    <div class="edsys-order__section-content">
                        <div class="edsys-order__profiles">
                            <?php foreach ($userProfiles as $profile): ?>
                                <?php
                                $profId = (int)$profile['ID'];
                                $profInputId = 'edsys-profile-' . $profId;
                                ?>
                                <label class="edsys-order__profile" for="<?= $profInputId ?>">
                                    <input type="radio"
                                           id="<?= $profInputId ?>"
                                           name="PROFILE_ID"
                                           value="<?= $profId ?>"
                                           class="edsys-order__profile-radio"
                                           <?= !empty($profile['CHECKED']) ? 'checked' : '' ?>>
                                    <div class="edsys-order__profile-content">
                                        <div class="edsys-order__profile-name">
                                            <?= htmlspecialchars($profile['NAME'] ?? ('Профиль #'.$profId), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>
                                        </div>
                                        <?php if (!empty($profile['VALUES']) && is_array($profile['VALUES'])): ?>
                                            <div class="edsys-order__profile-info">
                                                <?php foreach ($profile['VALUES'] as $value): ?>
                                                    <span><?= htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="edsys-order__section edsys-order__section--customer">
                <div class="edsys-order__section-header">
                    <div class="edsys-order__section-number"><?= $step ?></div>
                    <h2 class="edsys-order__section-title">Данные для доставки</h2>
                </div>
                <?php $step++; ?>
                <div class="edsys-order__section-content">
                    <div class="edsys-order__fields">
                        <?php foreach ($allProps as $arProperty): ?>
                            <?php
                            // skip technical
                            if (!empty($arProperty['UTIL']) && $arProperty['UTIL'] === 'Y') { continue; }

                            $propId     = (int)($arProperty['ID'] ?? 0);
                            $propCode   = (string)($arProperty['CODE'] ?? ('PROP_'.$propId));
                            $propName   = (string)($arProperty['NAME'] ?? $propCode);
                            $propType   = (string)($arProperty['TYPE'] ?? 'TEXT');
                            $propDesc   = (string)($arProperty['DESCRIPTION'] ?? '');
                            $propReq    = (string)($arProperty['REQUIRED'] ?? 'N') === 'Y';
                            $fieldName  = (string)($arProperty['FIELD_NAME'] ?? '');
                            $value      = $arProperty['VALUE'] ?? '';
                            $variants   = is_array($arProperty['VARIANTS'] ?? null) ? $arProperty['VARIANTS'] : [];
                            $inputId    = 'edsys-prop-'.$propId.'-'.substr(md5($propCode.$propId), 0, 6);
                            ?>
                            <div class="edsys-order__field">
                                <?php if ($propType !== 'CHECKBOX'): ?>
                                    <label class="edsys-order__label" for="<?= $inputId ?>">
                                        <?= htmlspecialchars($propName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>
                                        <?php if ($propReq): ?><span class="edsys-order__required">*</span><?php endif; ?>
                                    </label>
                                <?php endif; ?>

                                <?php if ($propType === 'TEXT'): ?>
                                    <input type="text"
                                           id="<?= $inputId ?>"
                                           name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           value="<?= htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           class="edsys-order__input"
                                           <?= $propReq ? 'required' : '' ?>
                                           placeholder="<?= htmlspecialchars($propDesc, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>">

                                <?php elseif ($propType === 'TEXTAREA'): ?>
                                    <textarea id="<?= $inputId ?>"
                                              name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                              class="edsys-order__textarea"
                                              <?= $propReq ? 'required' : '' ?>
                                              placeholder="<?= htmlspecialchars($propDesc, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                    ><?= htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></textarea>

                                <?php elseif ($propType === 'SELECT'): ?>
                                    <select id="<?= $inputId ?>"
                                            name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                            class="edsys-order__select"
                                            <?= $propReq ? 'required' : '' ?>>
                                        <?php foreach ($variants as $variant): ?>
                                            <option value="<?= htmlspecialchars((string)($variant['VALUE'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                                <?= !empty($variant['SELECTED']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string)($variant['NAME'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($propType === 'MULTISELECT'): ?>
                                    <select id="<?= $inputId ?>"
                                            name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>[]"
                                            class="edsys-order__select"
                                            multiple
                                            <?= $propReq ? 'required' : '' ?>>
                                        <?php foreach ($variants as $variant): ?>
                                            <option value="<?= htmlspecialchars((string)($variant['VALUE'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                                <?= !empty($variant['SELECTED']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars((string)($variant['NAME'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                <?php elseif ($propType === 'RADIO'): ?>
                                    <div class="edsys-order__radios" role="radiogroup" aria-label="<?= htmlspecialchars($propName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>">
                                        <?php foreach ($variants as $variant): ?>
                                            <?php
                                            $radioId = $inputId . '-' . md5((string)($variant['VALUE'] ?? ''));
                                            ?>
                                            <label class="edsys-order__radio" for="<?= $radioId ?>">
                                                <input type="radio"
                                                       id="<?= $radioId ?>"
                                                       class="edsys-order__radio-input"
                                                       name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                                       value="<?= htmlspecialchars((string)($variant['VALUE'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                                    <?= !empty($variant['SELECTED']) ? 'checked' : '' ?>
                                                    <?= $propReq ? 'required' : '' ?>>
                                                <span class="edsys-order__radio-text"><?= htmlspecialchars((string)($variant['NAME'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></span>
                                            </label>
                                        <?php endforeach; ?>
                                    </div>

                                <?php elseif ($propType === 'CHECKBOX'): ?>
                                    <?php
                                    $checked = (!empty($arProperty['CHECKED']) || $value === 'Y') ? 'checked' : '';
                                    ?>
                                    <label class="edsys-order__checkbox-label" for="<?= $inputId ?>">
                                        <input type="checkbox"
                                               id="<?= $inputId ?>"
                                               name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                               value="Y"
                                               class="edsys-order__checkbox"
                                               <?= $checked ?>
                                               <?= $propReq ? 'required' : '' ?>>
                                        <span><?= htmlspecialchars($propDesc !== '' ? $propDesc : $propName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?><?php if ($propReq): ?> <span class="edsys-order__required">*</span><?php endif; ?></span>
                                    </label>

                                <?php elseif ($propType === 'FILE'): ?>
                                    <input type="file"
                                           id="<?= $inputId ?>"
                                           name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           class="edsys-order__input"
                                           <?= $propReq ? 'required' : '' ?>>

                                <?php elseif ($propType === 'DATE'): ?>
                                    <input type="date"
                                           id="<?= $inputId ?>"
                                           name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           value="<?= htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           class="edsys-order__input"
                                           <?= $propReq ? 'required' : '' ?>>

                                <?php elseif ($propType === 'LOCATION'): ?>
                                    <div class="edsys-order__location" id="<?= $inputId ?>">
                                        <?php
                                        $APPLICATION->IncludeComponent(
                                            'bitrix:sale.location.selector.search',
                                            '',
                                            [
                                                'ID' => (int)$value,
                                                'INPUT_NAME' => $fieldName,
                                                'PROVIDE_LINK_BY' => 'id',
                                                'FILTER_BY_SITE' => 'Y',
                                                'SHOW_DEFAULT_LOCATIONS' => 'Y',
                                                'CACHE_TYPE' => 'A',
                                                'CACHE_TIME' => 3600000,
                                                'INITIALIZE_BY_GLOBAL_EVENT' => '',
                                                'SUPPRESS_ERRORS' => 'Y',
                                            ],
                                            false
                                        );
                                        ?>
                                    </div>

                                <?php else: ?>
                                    <input type="text"
                                           id="<?= $inputId ?>"
                                           name="<?= htmlspecialchars($fieldName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           value="<?= htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                           class="edsys-order__input"
                                           <?= $propReq ? 'required' : '' ?>>
                                <?php endif; ?>

                                <?php if ($propDesc && $propType !== 'CHECKBOX'): ?>
                                    <div class="edsys-order__field-note"><?= htmlspecialchars($propDesc, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <?php if (!empty($arResult['DELIVERY'])): ?>
                <section class="edsys-order__section edsys-order__section--delivery">
                    <div class="edsys-order__section-header">
                        <div class="edsys-order__section-number"><?= $step ?></div>
                        <h2 class="edsys-order__section-title">Доставка</h2>
                    </div>
                    <?php $step++; ?>
                    <div class="edsys-order__section-content">
                        <div class="edsys-order__delivery-list">
                            <?php foreach ($arResult['DELIVERY'] as $delivery): ?>
                                <?php
                                $delId = (int)$delivery['ID'];
                                $delInputId = 'edsys-delivery-' . $delId;
                                // TK item detection (only for "Доставка транспортной компанией")
                                $deliveryName = (string)($delivery['NAME'] ?? '');
                                $isTkDelivery = (mb_stripos($deliveryName, 'доставка транспортной компан') !== false);
                                ?>
                                <label class="edsys-order__delivery-item <?= !empty($delivery['CHECKED']) ? 'edsys-order__delivery-item--selected' : '' ?>" for="<?= $delInputId ?>">
                                    <input type="radio"
                                           id="<?= $delInputId ?>"
                                           name="DELIVERY_ID"
                                           value="<?= $delId ?>"
                                           class="edsys-order__delivery-radio"
                                           <?= !empty($delivery['CHECKED']) ? 'checked' : '' ?>>
                                    <div class="edsys-order__delivery-content">
                                        <div class="edsys-order__delivery-name">
                                            <?= htmlspecialchars($deliveryName, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>
                                        </div>
                                        <?php if (!empty($delivery['DESCRIPTION'])): ?>
                                            <div class="edsys-order__delivery-desc">
                                                <?= $delivery['DESCRIPTION'] ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="edsys-order__delivery-price">
                                        <?php if (!empty($delivery['PRICE']) && (float)$delivery['PRICE'] > 0): ?>
                                            <?= $delivery['PRICE_FORMATED'] ?>
                                        <?php else: ?>
                                            <?= $isTkDelivery ? 'По тарифу ТК' : 'Бесплатно' ?>
                                        <?php endif; ?>
                                    </div>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </section>
            <?php endif; ?>

            <section class="edsys-order__section edsys-order__section--payment">
                <div class="edsys-order__section-header">
                    <div class="edsys-order__section-number"><?= $step ?></div>
                    <h2 class="edsys-order__section-title">Оплата</h2>
                </div>
                <?php $step++; ?>
                <div class="edsys-order__section-content">
                    <div class="edsys-order__payment-info">
                        <p class="edsys-order__payment-notice">
                            На этом этапе выбор способа оплаты отключён. После оформления заказа менеджер направит Вам счёт.
                        </p>
                        <p class="edsys-order__payment-notice">
                            Оплата производится безналичным расчётом согласно выставленному счёту.
                        </p>
                    </div>
                </div>
            </section>

            <?php if (!empty($arParams['USER_CONSENT'])): ?>
                <div class="edsys-order__consent">
                    <?php
                    $consentId = 'edsys-order-consent';
                    ?>
                    <label class="edsys-order__consent-label" for="<?= $consentId ?>">
                        <input type="checkbox"
                               id="<?= $consentId ?>"
                               name="USER_CONSENT"
                               value="Y"
                               class="edsys-order__consent-checkbox"
                               <?= !empty($arParams['USER_CONSENT_IS_CHECKED']) ? 'checked' : '' ?>
                               required>
                        <span>
                            Нажимая кнопку «Оформить заказ», я даю согласие на обработку моих персональных данных
                            в соответствии с Федеральным законом №152-ФЗ «О персональных данных».
                        </span>
                    </label>
                </div>
            <?php endif; ?>

            <div class="edsys-order__actions">
                <button type="submit"
                        class="edsys-order__submit"
                        name="confirmorder"
                        value="Y">
                    Оформить заказ
                    <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                </button>

                <a href="<?= htmlspecialchars((string)($arParams['PATH_TO_BASKET'] ?? '/personal/cart/'), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                   class="edsys-order__back">
                    <i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
                    Вернуться в корзину
                </a>
            </div>
        </div>

        <aside class="edsys-order__sidebar">
            <div class="edsys-order__summary">
                <h3 class="edsys-order__summary-title">Товары в заказе</h3>

                <div class="edsys-order__basket-items">
                    <?php if (!empty($arResult['BASKET_ITEMS']) && is_array($arResult['BASKET_ITEMS'])): ?>
                        <?php foreach ($arResult['BASKET_ITEMS'] as $item): ?>
                            <?php
                            $itemName = htmlspecialchars((string)($item['NAME'] ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET);
                            $qty = (float)($item['QUANTITY'] ?? 0);
                            $img = $item['PREVIEW_PICTURE']['SRC'] ?? '';
                            // show sum for line
                            $linePrice = $item['SUM_FULL_PRICE_FORMATED'] ?? $item['FULL_PRICE_FORMATED'] ?? $item['SUM_PRICE_FORMATED'] ?? $item['PRICE_FORMATED'] ?? '';
                            ?>
                            <div class="edsys-order__basket-item">
                                <?php if ($img): ?>
                                    <div class="edsys-order__basket-image">
                                        <img src="<?= htmlspecialchars((string)$img, ENT_QUOTES | ENT_SUBSTITUTE, SITE_CHARSET) ?>"
                                             alt="<?= $itemName ?>"
                                             width="60"
                                             height="60"
                                             loading="lazy">
                                    </div>
                                <?php endif; ?>
                                <div class="edsys-order__basket-info">
                                    <div class="edsys-order__basket-name"><?= $itemName ?></div>
                                    <div class="edsys-order__basket-quantity"><?= $qty ?> шт.</div>
                                </div>
                                <div class="edsys-order__basket-price"><?= $linePrice ?></div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="edsys-order__promo">
                    <button type="button"
                            class="edsys-order__promo-toggle"
                            id="edsys-order-promo-toggle"
                            aria-expanded="false"
                            aria-controls="edsys-order-promo-form">
                        <i class="ph ph-thin ph-ticket" aria-hidden="true"></i>
                        <span>Применить купон</span>
                        <i class="ph ph-thin ph-caret-down edsys-order__promo-arrow" aria-hidden="true"></i>
                    </button>

                    <div class="edsys-order__promo-form"
                         id="edsys-order-promo-form"
                         aria-hidden="true">
                        <input type="text"
                               name="COUPON"
                               class="edsys-order__promo-input"
                               placeholder="Введите промокод"
                               id="edsys-order-promo-input"
                               autocomplete="off" inputmode="text">
                        <button type="submit"
                                class="edsys-order__promo-apply"
                                name="apply_coupon"
                                value="Y">
                            Применить
                        </button>
                    </div>
                </div>

                <div class="edsys-order__totals">
                    <div class="edsys-order__total-row">
                        <span class="edsys-order__total-label">Товаров на:</span>
                        <span class="edsys-order__total-value"><?= $arResult['ORDER_PRICE_FORMATED'] ?? '' ?></span>
                    </div>

                    <?php if (isset($arResult['DELIVERY_PRICE'])): ?>
                        <div class="edsys-order__total-row">
                            <span class="edsys-order__total-label">Доставка:</span>
                            <span class="edsys-order__total-value">
                                <?php
                                if (!empty($arResult['DELIVERY_PRICE']) && (float)$arResult['DELIVERY_PRICE'] > 0) {
                                    echo $arResult['DELIVERY_PRICE_FORMATED'];
                                } else {
                                    echo 'Бесплатно';
                                }
                                ?>
                            </span>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($arResult['DISCOUNT_PRICE']) && (float)$arResult['DISCOUNT_PRICE'] > 0): ?>
                        <div class="edsys-order__total-row edsys-order__total-row--discount">
                            <span class="edsys-order__total-label">Скидка:</span>
                            <span class="edsys-order__total-value">-<?= $arResult['DISCOUNT_PRICE_FORMATED'] ?></span>
                        </div>
                    <?php endif; ?>

                    <div class="edsys-order__total-row edsys-order__total-row--final">
                        <span class="edsys-order__total-label">Итого:</span>
                        <span class="edsys-order__total-value"><?= $arResult['ORDER_TOTAL_PRICE_FORMATED'] ?? '' ?></span>
                    </div>
                </div>
            </div>
        </aside>
    </div>

    <input type="hidden" name="ajax_post" value="Y">
</form>

<div class="edsys-order__loading" id="edsys-order-loading" aria-hidden="true">
    <div class="edsys-order__loader">
        <div class="edsys-order__loader-spinner"></div>
    </div>
</div>
