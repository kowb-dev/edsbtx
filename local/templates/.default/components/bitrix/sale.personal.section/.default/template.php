<?php
/**
 * Шаблон личного кабинета пользователя
 * 
 * @version 1.1.0
 * @author KW https://kowb.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

$curPage = $APPLICATION->GetCurPage();
$userCompany = $arResult['USER_COMPANY'] ?? '';
?>
<style>
    .edsys-dashboard__card-link-wrapper {
        text-decoration: none;
        color: inherit;
        display: block;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .edsys-dashboard__card-link-wrapper:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }
</style>
<section class="edsys-account">
    <div class="edsys-account__container">
        
        <!-- Мобильное меню -->
        <button class="edsys-account__mobile-toggle" type="button" aria-label="Открыть меню личного кабинета" aria-expanded="false">
            <i class="ph-thin ph-list" aria-hidden="true"></i>
            <span>Меню кабинета</span>
        </button>

        <div class="edsys-account__wrapper">
            
            <!-- Боковое меню навигации -->
            <aside class="edsys-account__sidebar" aria-label="Навигация личного кабинета">
                <nav class="edsys-account__nav">
                    <h2 class="edsys-account__nav-title">Личный кабинет</h2>
                    
                    <?php if ($userCompany): ?>
                        <div class="edsys-account__company-badge">
                            <i class="ph-thin ph-buildings" aria-hidden="true"></i>
                            <span><?= htmlspecialcharsbx($userCompany) ?></span>
                        </div>
                    <?php endif; ?>

                    <ul class="edsys-account__menu">
                        <li class="edsys-account__menu-item">
                            <a href="<?= $arResult['PATH_TO_PROFILE'] ?>" 
                               class="edsys-account__menu-link <?= ($arResult['CURRENT_PAGE'] === 'profile') ? 'is-active' : '' ?>"
                               aria-current="<?= ($arResult['CURRENT_PAGE'] === 'profile') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-user-circle" aria-hidden="true"></i>
                                <span>Личная информация</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="<?= $arResult['PATH_TO_ORDERS'] ?>" 
                               class="edsys-account__menu-link <?= ($arResult['CURRENT_PAGE'] === 'orders') ? 'is-active' : '' ?>"
                               aria-current="<?= ($arResult['CURRENT_PAGE'] === 'orders') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-package" aria-hidden="true"></i>
                                <span>Мои заказы</span>
                                <?php if ($arResult['ORDERS_COUNT'] > 0): ?>
                                    <span class="edsys-account__badge" aria-label="Количество заказов"><?= $arResult['ORDERS_COUNT'] ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="<?= $arResult['PATH_TO_BASKET'] ?>" 
                               class="edsys-account__menu-link <?= ($arResult['CURRENT_PAGE'] === 'basket') ? 'is-active' : '' ?>"
                               aria-current="<?= ($arResult['CURRENT_PAGE'] === 'basket') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-heart" aria-hidden="true"></i>
                                <span>Избранное</span>
                                <?php if ($arResult['FAVORITES_COUNT'] > 0): ?>
                                    <span class="edsys-account__badge" aria-label="Количество товаров в избранном"><?= $arResult['FAVORITES_COUNT'] ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="<?= $arResult['PATH_TO_CONTACT'] ?>" 
                               class="edsys-account__menu-link <?= ($arResult['CURRENT_PAGE'] === 'address') ? 'is-active' : '' ?>"
                               aria-current="<?= ($arResult['CURRENT_PAGE'] === 'address') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-map-pin" aria-hidden="true"></i>
                                <span>Адреса доставки</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="<?= $arResult['PATH_TO_PRIVATE'] ?>" 
                               class="edsys-account__menu-link <?= ($arResult['CURRENT_PAGE'] === 'password') ? 'is-active' : '' ?>"
                               aria-current="<?= ($arResult['CURRENT_PAGE'] === 'password') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-lock-key" aria-hidden="true"></i>
                                <span>Смена пароля</span>
                            </a>
                        </li>
                    </ul>

                    <a href="?logout=yes&<?= bitrix_sessid_get() ?>" class="edsys-account__logout">
                        <i class="ph-thin ph-sign-out" aria-hidden="true"></i>
                        <span>Выход</span>
                    </a>
                </nav>
            </aside>

            <!-- Основной контент -->
            <main class="edsys-account__content">
                <?php
                // Подключение активного раздела
                switch ($arResult['CURRENT_PAGE']) {
                    case 'profile':
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.profile",
                            "edsys",
                            array(
                                "SET_TITLE" => "Y",
                                "AJAX_MODE" => "N",
                                "SEND_INFO" => "N",
                                "CHECK_RIGHTS" => "N",
                                "USER_PROPERTY_NAME" => ""
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );
                        break;

                    case 'orders':
                        $APPLICATION->IncludeComponent(
                            "bitrix:sale.personal.order.list",
                            "edsys",
                            array(
                                "SEF_MODE" => "N",
                                "ORDERS_PER_PAGE" => "20",
                                "PATH_TO_DETAIL" => $arResult['PATH_TO_ORDER_DETAIL'],
                                "PATH_TO_CANCEL" => $arResult['PATH_TO_ORDER_CANCEL'],
                                "PATH_TO_CATALOG" => "/catalog/",
                                "PATH_TO_PAYMENT" => $arResult['PATH_TO_PAYMENT'],
                                "SAVE_IN_SESSION" => "Y",
                                "NAV_TEMPLATE" => "edsys",
                                "ACTIVE_DATE_FORMAT" => "d.m.Y",
                                "HISTORIC_STATUSES" => array("F"),
                                "ALLOW_INNER" => "N",
                                "ONLY_INNER_FULL" => "N",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600",
                                "CACHE_GROUPS" => "Y",
                                "SET_TITLE" => "Y"
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );
                        break;

                    case 'basket':
                        $APPLICATION->IncludeComponent(
                            "bitrix:catalog.favorites",
                            "edsys",
                            array(
                                "IBLOCK_TYPE" => "catalog",
                                "IBLOCK_ID" => "2",
                                "ELEMENT_SORT_FIELD" => "name",
                                "ELEMENT_SORT_ORDER" => "asc",
                                "ELEMENT_COUNT" => "20",
                                "LINE_ELEMENT_COUNT" => "4",
                                "DETAIL_URL" => "/catalog/#SECTION_CODE#/#ELEMENT_CODE#/",
                                "BASKET_URL" => "/personal/cart/",
                                "ACTION_VARIABLE" => "action",
                                "PRODUCT_ID_VARIABLE" => "id",
                                "SET_TITLE" => "Y",
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600",
                                "CACHE_GROUPS" => "Y"
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );
                        break;

                    case 'address':
                        $APPLICATION->IncludeComponent(
                            "bitrix:sale.personal.profile",
                            "edsys",
                            array(
                                "SET_TITLE" => "Y",
                                "PER_PAGE" => "20",
                                "USE_AJAX_LOCATIONS" => "Y",
                                "AJAX_OPTION_ADDITIONAL" => "",
                                "PATH_TO_DETAIL" => $arResult['PATH_TO_PROFILE_DETAIL'],
                                "CACHE_TYPE" => "A",
                                "CACHE_TIME" => "3600"
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );
                        break;

                    case 'password':
                        $APPLICATION->IncludeComponent(
                            "bitrix:main.profile",
                            "edsys_password",
                            array(
                                "SET_TITLE" => "Y",
                                "AJAX_MODE" => "N",
                                "SEND_INFO" => "N",
                                "CHECK_RIGHTS" => "N"
                            ),
                            $component,
                            array("HIDE_ICONS" => "Y")
                        );
                        break;

                    default:
                        // Дашборд по умолчанию
                        include(__DIR__ . '/dashboard.php');
                        break;
                }
                ?>
            </main>

        </div>
    </div>
</section>