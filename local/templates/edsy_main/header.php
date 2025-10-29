<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();?>
<?
/**
 * @author KW
 * @version 1.8
 * @URI https://kowb.ru
 */
?>

<?php

if (!defined('DEFAULT_TEMPLATE_PATH')) {
    define('DEFAULT_TEMPLATE_PATH', '/local/templates/' . SITE_TEMPLATE_ID);
}

// Альтернативный вариант если SITE_TEMPLATE_ID не работает
if (!defined('DEFAULT_TEMPLATE_PATH') || DEFAULT_TEMPLATE_PATH === '/local/templates/') {
    define('DEFAULT_TEMPLATE_PATH', '/local/templates/edsy_main');
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <?php
    use Bitrix\Main\Page\Asset;
    $APPLICATION->ShowHead();
    ?>
    <title><?php $APPLICATION->ShowTitle(); ?></title>

    <?php
    Asset::getInstance()->addString('<meta name="viewport" content="width=device-width, initial-scale=1">');
    // Подключение jQuery из ядра Битрикса
    Asset::getInstance()->addJs("/bitrix/js/main/jquery/jquery-3.7.1.min.js");
    Asset::getInstance()->addString('<script src="/local/templates/edsy_main/js/favorites.js?v=2.0.0"></script>');
Asset::getInstance()->addString('<script src="/local/templates/edsy_main/js/favorites-init.js?v=2.0.0"></script>');

    // Preconnect для оптимизации загрузки шрифтов
    Asset::getInstance()->addString('<link rel="preconnect" href="https://fonts.googleapis.com">');
    Asset::getInstance()->addString('<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>');

    // Async загрузка шрифтов
    Asset::getInstance()->addString('<link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;700&display=swap" rel="stylesheet">');

    // Preload основных стилей и скриптов
    Asset::getInstance()->addString('<link rel="preload" href="' . DEFAULT_TEMPLATE_PATH . '/css/variables.css" as="style">');
    Asset::getInstance()->addString('<link rel="preload" href="' . DEFAULT_TEMPLATE_PATH . '/js/main.js" as="script" crossorigin>');

    // Подключение основных стилей
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . "/css/variables.css");
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . "/css/reset.css");
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . "/css/utilities.css");
    Asset::getInstance()->addCss(DEFAULT_TEMPLATE_PATH . "/css/main.css");

    // Подключение основных скриптов
    Asset::getInstance()->addString('<script type="module" src="' . DEFAULT_TEMPLATE_PATH . '/js/main.js"></script>');


    // Phosphor Icons
    Asset::getInstance()->addString('<link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/regular/style.css">');
    Asset::getInstance()->addString('<link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.1.1/src/thin/style.css">');

    // Inline critical CSS (для предотвращения FOUC и ускорения загрузки)
    Asset::getInstance()->addString('
        <style>
            :root {
                --edsys-font-primary: \'Open Sans\', -apple-system, BlinkMacSystemFont, \'Segoe UI\', sans-serif;
                --edsys-font-regular: 400;
                --edsys-font-bold: 700;
                --edsys-primary: #ff4757;
                --edsys-white: #ffffff;
                --edsys-text: #21242D;
                --edsys-bg: #f5f5f7;
                --edsys-border: #e0e0e0;
                --container-max: min(103.2rem, 100vw - 2rem);
                --container-padding: clamp(1rem, 3vw, 2rem);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: var(--edsys-font-primary);
                font-weight: var(--edsys-font-regular);
                line-height: 1.5;
                color: var(--edsys-text);
                background: var(--edsys-bg);
                -webkit-font-smoothing: antialiased;
                -moz-osx-font-smoothing: grayscale;
            }

            .edsys-header {
                position: relative;
                z-index: 1000;
                background: var(--edsys-white);
                border-bottom: 1px solid var(--edsys-border);
            }

            .edsys-header__container {
                max-width: var(--container-max);
                margin: 0 auto;
            }

            .edsys-megamenu,
            .edsys-mobile-menu,
            .edsys-mobile-catalog,
            .edsys-overlay {
                visibility: visible;
            }
        </style>
    ');
    Asset::getInstance()->addString('
        <style>
        .edsys-header__action {
            position: relative;
        }

        #favorites-counter {
            position: absolute;
            top: 0;
            right: 0;
            background-color: var(--edsys-primary);
            color: var(--edsys-white);
            border-radius: 50%;
            width: 18px;
            height: 18px;
            font-size: 12px;
            line-height: 18px;
            text-align: center;
            transform: translate(-80%, 0%);
            display: none; /* Hidden by default */
        }
        </style>
    ');
    ?>
    <?php $APPLICATION->IncludeFile('/local/templates/.default/include/catalog_styles.php'); ?>
    <script>
        window.SITE_CONFIG = {
            ASSETS: {
                logo: '/img/logo.svg',
                heroVideo: '/img/hero-vid.jpg',
                favicon: '/img/favicon.ico'
            }
        };
    </script>
<?php
// Скрыть панель для всех, кроме администраторов
global $USER;
if (!$USER->IsAdmin()) {
    $APPLICATION->ShowPanel = false;
}
?>
<script>BX.message({ 'USER_IS_AUTHORIZED': <?= $GLOBALS['USER']->IsAuthorized() ? 'true' : 'false' ?> });</script>
</head>

<body>
<div id="panel"><?php $APPLICATION->ShowPanel(); ?></div>
<!-- Header -->
<header class="edsys-header">
    <div class="edsys-header__container">
        <!-- Mobile Top Bar -->
        <div class="edsys-header__mobile-top">
            <a href="https://wa.me/79999999999" class="edsys-header__mobile-contact" aria-label="WhatsApp">
                <i class="ph ph-thin ph-whatsapp-logo"></i>
            </a>

            <div class="edsys-header__logo">
                <?php if($APPLICATION->GetCurPage() !== '/'): ?>
                <a href="/" class="edsys-header__logo-link">
                    <?php endif; ?>
                    <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/logo.svg" alt="EDS">
                    <div class="edsys-header__title">
                        <span>ELECTRIC</span>
                        <span>DISTRIBUTION</span>
                        <span>SYSTEMS</span>
                    </div>
                    <?php if($APPLICATION->GetCurPage() !== '/'): ?>
                </a>
            <?php endif; ?>
            </div>

            <a href="tel:+79105273538" class="edsys-header__mobile-contact" aria-label="Позвонить">
                <i class="ph ph-thin ph-phone"></i>
            </a>
        </div>

        <!-- Mobile Search Bar -->
        <div class="edsys-header__mobile-search">
            <button class="edsys-header__mobile-search-btn" data-action="toggle-catalog" aria-label="Каталог">
                <i class="ph ph-list-magnifying-glass"></i>
            </button>

            <div class="edsys-header__search">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:search.title", 
                    "visual_custom", 
                    array(
                        "NUM_CATEGORIES" => "1",
                        "TOP_COUNT" => "5",
                        "ORDER" => "date",
                        "USE_LANGUAGE_GUESS" => "N",
                        "CHECK_DATES" => "N",
                        "SHOW_OTHERS" => "N",
                        "PAGE" => "/search/",
                        "CATEGORY_0_TITLE" => "Товары",
                        "CATEGORY_0" => array(
                            0 => "iblock_catalog",
                        ),
                        "CATEGORY_0_iblock_catalog" => array(
                            0 => "7",
                        ),
                        "SHOW_INPUT" => "Y",
                        "INPUT_ID" => "title-search-input-mobile",
                        "CONTAINER_ID" => "title-search-mobile",
                    ),
                    false
                );?>
            </div>

            <button class="edsys-header__mobile-hamburger" data-action="toggle-menu" aria-label="Меню">
                <i class="ph ph-thin ph-list"></i>
            </button>
        </div>

        <!-- Desktop Header -->
        <div class="edsys-header__top">
            <!-- Logo -->
            <div class="edsys-header__logo">
                <?php if($APPLICATION->GetCurPage() !== '/'): ?>
                <a href="/" class="edsys-header__logo-link">
                    <?php endif; ?>
                    <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/logo.svg" alt="EDS">
                    <div class="edsys-header__title">
                        <span>ELECTRIC</span>
                        <span>DISTRIBUTION</span>
                        <span>SYSTEMS</span>
                    </div>
                    <?php if($APPLICATION->GetCurPage() !== '/'): ?>
                </a>
            <?php endif; ?>
            </div>

            <!-- Catalog Button -->
            <div class="edsys-header__catalog">
                <a href="/catalog/" class="edsys-header__catalog-btn" id="catalogBtn">
                    <i class="ph ph-list-magnifying-glass"></i>
                    <span class="edsys-header__catalog-text">КАТАЛОГ</span>
                </a>
            </div>

            <!-- Search -->
            <div class="edsys-header__search">
                <?$APPLICATION->IncludeComponent(
                    "bitrix:search.title", 
                    "visual_custom", 
                    array(
                        "NUM_CATEGORIES" => "1",
                        "TOP_COUNT" => "5",
                        "ORDER" => "date",
                        "USE_LANGUAGE_GUESS" => "N",
                        "CHECK_DATES" => "N",
                        "SHOW_OTHERS" => "N",
                        "PAGE" => "/search/",
                        "CATEGORY_0_TITLE" => "Товары",
                        "CATEGORY_0" => array(
                            0 => "iblock_catalog",
                        ),
                        "CATEGORY_0_iblock_catalog" => array(
                            0 => "7",
                        ),
                        "SHOW_INPUT" => "Y",
                        "INPUT_ID" => "title-search-input-desktop",
                        "CONTAINER_ID" => "title-search-desktop",
                    ),
                    false
                );?>
            </div>

            <!-- Actions -->
            <div class="edsys-header__actions">
                <a href="/personal/orders/" class="edsys-header__action">
                    <i class="ph ph-thin ph-package"></i>
                    <span class="edsys-header__action-text">Заказы</span>
                </a>
                <?if ($USER->IsAuthorized()):?>
                    <a href="/personal/favorites/" class="edsys-header__action">
                <?else:?>
                    <a href="/favorites/" class="edsys-header__action">
                <?endif;?>
                    <i class="ph ph-thin ph-heart"></i>
                    <span class="edsys-header__action-text">Избранное</span>
                    <span id="favorites-counter"></span>
                </a>
                <a href="/compare/" class="edsys-header__action">
                    <i class="ph ph-thin ph-chart-bar"></i>
                    <span class="edsys-header__action-text">Сравнение</span>
                </a>
                <?if ($USER->IsAuthorized()):?>
                    <a href="/personal/" class="edsys-header__action">
                        <i class="ph ph-thin ph-user"></i>
                        <span class="edsys-header__action-text">Аккаунт</span>
                    </a>
                <?else:?>
                    <a href="/auth/" class="edsys-header__action">
                        <i class="ph ph-thin ph-user"></i>
                        <span class="edsys-header__action-text">Войти</span>
                    </a>
                <?endif;?>
                <a href="/personal/cart/" class="edsys-header__action">
                    <i class="ph ph-thin ph-shopping-cart"></i>
                    <span class="edsys-header__action-text">Корзина</span>
                </a>
            </div>
        </div>

        <!-- Навигационное меню -->
        <?$APPLICATION->IncludeComponent(
            "bitrix:menu",
            "header_dropdown",
            array(
                "ROOT_MENU_TYPE" => "main",
                "CHILD_MENU_TYPE" => "left",
                "MAX_LEVEL" => "2",
                "USE_EXT" => "Y",
                "MENU_CACHE_TYPE" => "N" // Отключаем кэш для тестирования
            )
        );?>

    </div>
</header>

<?php
// Определяем базовый URL для категорий
$CATALOG_BASE_URL = "https://btx.edsy.ru";
?>

<div class="edsys-megamenu" id="megaMenu">
    <div class="edsys-megamenu__container">
        <div class="edsys-megamenu__grid">
            <div class="edsys-megamenu__column">
                <ul>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/turovye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/turovye-distribyutory.jpeg"
                                 alt="Туровые дистрибьюторы" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Туровые дистрибьюторы
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/rjekovye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-distribyutory.jpeg"
                                 alt="Рэковые дистрибьюторы" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Рэковые дистрибьюторы
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/vvod-ot-63a/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/vvod-ot-63a.jpeg" alt="Ввод от 63A"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            Ввод от 63A
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/ustrojstva-s-zashhitnymi-rele/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-s-zaschitnymi-rele.jpeg"
                                 alt="Устройства с защитными реле" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Устройства с защитными реле
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/seriya-black-edition/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/seriya-black-edition.jpeg"
                                 alt="Серия Black Edition" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Серия Black Edition
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/distribyutori-so-vstroennym-splitterom/"
                           class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/distribyutory-so-vstroennym-splitterom.jpeg"
                                 alt="Дистрибьюторы со встроенным сплиттером" class="edsys-megamenu__image" width="48"
                                 height="48" loading="lazy">
                            Дистрибьюторы со встроенным сплиттером
                        </a>
                    </li>
                </ul>
            </div>

            <div class="edsys-megamenu__column">
                <ul>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye-digital/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-cifrovye.jpeg"
                                 alt="Пульты лебедочные цифровые" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Пульты лебедочные цифровые
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-analogovye.jpeg"
                                 alt="Пульты лебедочные аналоговые" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Пульты лебедочные аналоговые
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/art-net-dmx-konvertery/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/art-net-dmx-konvertery.jpeg"
                                 alt="Art-Net - DMX конвертеры" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Art-Net - DMX конвертеры
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/dmx-splitters/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dmx-splittery.jpeg" alt="DMX-сплиттеры"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            DMX-сплиттеры
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/dimmery/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dimmery.jpeg" alt="Диммеры"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            Диммеры
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/sekvensory/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/sekvensory.jpeg" alt="Секвенсоры"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            Секвенсоры
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/svitchery/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/svitchery.jpeg" alt="Свитчеры"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            Свитчеры
                        </a>
                    </li>
                </ul>
            </div>

            <div class="edsys-megamenu__column">
                <ul>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/korobki-kommutatsionnye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kommutacionnye-korobki.jpeg"
                                 alt="Коммутационные коробки" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Коммутационные коробки
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-knopochnye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-upravleniya.jpeg"
                                 alt="Пульты управления" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Пульты управления
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/easylink/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-peredachi-signala.jpeg"
                                 alt="Устройства передачи сигнала" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Устройства передачи сигнала
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/audio-devices/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/audio-izolyatory.jpeg"
                                 alt="Аудио изоляторы" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Аудиоустройства
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/paneli-rekovye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-paneli.jpeg"
                                 alt="Рэковые панели" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Рэковые панели
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/rekovye-aksessuary/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/reki-i-aksessuary.jpeg"
                                 alt="Рэки и аксессуары" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Рэки и аксессуары
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/sistemy-podvesa/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/strubciny-i-trosik.jpeg"
                                 alt="Струбцины и тросики" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Струбцины и тросики
                        </a>
                    </li>
                </ul>
            </div>

            <div class="edsys-megamenu__column">
                <ul>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-sczenicheskie/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/scenicheskie-lyuchki.jpeg"
                                 alt="Сценические лючки" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Сценические лючки
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/nastolnye-lyuchki/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nastolnye-lyuchki.jpeg"
                                 alt="Настольные лючки" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Настольные лючки
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-nastennye/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nast.-lyuchok-50piks.jpg"
                                 alt="Настенные лючки" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Настенные лючки
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/kabelnaya-produktsiya/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kabelnaya-produkciya.jpeg"
                                 alt="Кабельная продукция" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Кабельная продукция
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/signalnaya-kommutatsiya/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/signalnaya-kommutaciya.jpeg"
                                 alt="Сигнальная коммутация" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Сигнальная коммутация
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/razemy-i-komponenty/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/razemy.jpeg" alt="Разъемы"
                                 class="edsys-megamenu__image" width="48" height="48" loading="lazy">
                            Разъемы
                        </a>
                    </li>
                    <li class="edsys-megamenu__item">
                        <a href="<?=$CATALOG_BASE_URL?>/cat/soputstvuyushchie-tovary/" class="edsys-megamenu__link">
                            <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/soputstvuyuschie-tovary.jpeg"
                                 alt="Сопутствующие товары" class="edsys-megamenu__image" width="48" height="48"
                                 loading="lazy">
                            Сопутствующие товары
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Overlay -->
<div class="edsys-overlay" id="overlay"></div>

<!-- Mobile Navigation -->
<nav class="edsys-mobile-nav">
    <?php
    $isHomePage = $APPLICATION->GetCurPage(false) === '/';
    $gridCols = $isHomePage ? 4 : 5;
    ?>
    <div class="edsys-mobile-nav__grid" style="grid-template-columns: repeat(<?= $gridCols ?>, 1fr);">
        <?php if(!$isHomePage): ?>
        <a href="/" class="edsys-mobile-nav__item">
            <i class="ph ph-thin ph-house"></i>
            <span>Главная</span>
        </a>
        <?php endif; ?>
        <a href="/catalog/" class="edsys-mobile-nav__item">
            <i class="ph ph-thin ph-list"></i>
            <span>Каталог</span>
        </a>
        <a href="/personal/cart/" class="edsys-mobile-nav__item">
            <i class="ph ph-thin ph-shopping-cart"></i>
            <span>Корзина</span>
        </a>
        <?if ($USER->IsAuthorized()):?>
            <a href="/personal/favorites/" class="edsys-mobile-nav__item">
        <?else:?>
            <a href="/favorites/" class="edsys-mobile-nav__item">
        <?endif;?>
            <i class="ph ph-thin ph-heart"></i>
            <span>Избранное</span>
        </a>
        <?if ($USER->IsAuthorized()):?>
            <a href="/personal/" class="edsys-mobile-nav__item">
                <i class="ph ph-thin ph-user"></i>
                <span>Аккаунт</span>
            </a>
        <?else:?>
            <a href="/auth/" class="edsys-mobile-nav__item">
                <i class="ph ph-thin ph-user"></i>
                <span>Войти</span>
            </a>
        <?endif;?>
    </div>
</nav>

<!-- Mobile Menu -->
<?php
$APPLICATION->IncludeComponent(
    "bitrix:menu",
    "mobile_dropdown",
    array(
        "ROOT_MENU_TYPE" => "main",
        "CHILD_MENU_TYPE" => "left",
        "MAX_LEVEL" => "2",
        "USE_EXT" => "Y",
        "MENU_CACHE_TYPE" => "N",
        "MENU_CACHE_TIME" => "3600",
        "MENU_CACHE_USE_GROUPS" => "Y",
        "MENU_CACHE_GET_VARS" => array(),
        "COMPONENT_TEMPLATE" => "mobile_dropdown"
    ),
    false
);
?>

<!-- Mobile Catalog -->
<?php
// Определяем базовый URL для категорий
$CATALOG_BASE_URL = "https://btx.edsy.ru";
?>

<div class="edsys-mobile-catalog" id="mobileCatalog">
    <div class="edsys-mobile-catalog__header">
        <!--        <h3>Все товары каталога</h3>-->
        <a href="/catalog/" class="edsys-good__all edsys-btn">все товары в таблице</a>
        <button class="edsys-mobile-catalog__close" data-action="toggle-catalog">
            <i class="ph ph-thin ph-x"></i>
        </button>
    </div>
    <ul class="edsys-mobile-catalog__list">
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/turovye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/turovye-distribyutory.jpeg"
                     alt="Туровые дистрибьюторы" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Туровые дистрибьюторы
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/rjekovye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-distribyutory.jpeg"
                     alt="Рэковые дистрибьюторы" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Рэковые дистрибьюторы
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/vvod-ot-63a/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/vvod-ot-63a.jpeg" alt="Ввод от 63A"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Ввод от 63A
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/ustrojstva-s-zashhitnymi-rele/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-s-zaschitnymi-rele.jpeg"
                     alt="Устройства с защитными реле" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Устройства с защитными реле
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/seriya-black-edition/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/seriya-black-edition.jpeg"
                     alt="Серия Black Edition" class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Серия Black Edition
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/distribyutori-so-vstroennym-splitterom/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/distribyutory-so-vstroennym-splitterom.jpeg"
                     alt="Дистрибьюторы со встроенным сплиттером" class="edsys-mobile-catalog__icon" width="24"
                     height="24" loading="lazy">
                Дистрибьюторы со встроенным сплиттером
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye-digital/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-cifrovye.jpeg"
                     alt="Пульты лебедочные цифровые" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Пульты лебедочные цифровые
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-lebedochnye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-lebedochnye-analogovye.jpeg"
                     alt="Пульты лебедочные аналоговые" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Пульты лебедочные аналоговые
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/art-net-dmx-konvertery/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/art-net-dmx-konvertery.jpeg"
                     alt="Art-Net - DMX конвертеры" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Art-Net - DMX конвертеры
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/dmx-splitters/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dmx-splittery.jpeg" alt="DMX-сплиттеры"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                DMX-сплиттеры
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/dimmery/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/dimmery.jpeg" alt="Диммеры"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Диммеры
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/sekvensory/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/sekvensory.jpeg" alt="Секвенсоры"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Секвенсоры
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/svitchery/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/svitchery.jpeg" alt="Свитчеры"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Свитчеры
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/korobki-kommutatsionnye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kommutacionnye-korobki.jpeg"
                     alt="Коммутационные коробки" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Коммутационные коробки
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/pulty-knopochnye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/pulty-upravleniya.jpeg" alt="Пульты управления"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Пульты управления
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/easylink/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/ustroistva-peredachi-signala.jpeg"
                     alt="Устройства передачи сигнала" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Устройства передачи сигнала
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/audio-devices/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/audio-izolyatory.jpeg" alt="Аудио изоляторы"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Аудиоустройства
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/paneli-rekovye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/rekovye-paneli.jpeg" alt="Рэковые панели"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Рэковые панели
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/rekovye-aksessuary/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/reki-i-aksessuary.jpeg" alt="Рэки и аксессуары"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Рэки и аксессуары
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/sistemy-podvesa/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/strubciny-i-trosik.jpeg" alt="Струбцины и тросики"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Струбцины и тросики
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-sczenicheskie/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/scenicheskie-lyuchki.jpeg" alt="Сценические лючки"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Сценические лючки
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/nastolnye-lyuchki/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nastolnye-lyuchki.jpeg" alt="Настольные лючки"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Настольные лючки
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/lyuchki-nastennye/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/nast.-lyuchok-50piks.jpg" alt="Настенные лючки"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Настенные лючки
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/kabelnaya-produktsiya/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/kabelnaya-produkciya.jpeg"
                     alt="Кабельная продукция" class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Кабельная продукция
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/signalnaya-kommutatsiya/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/signalnaya-kommutaciya.jpeg"
                     alt="Сигнальная коммутация" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Сигнальная коммутация
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/razemy-i-komponenty/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/razemy.jpeg" alt="Разъемы"
                     class="edsys-mobile-catalog__icon" width="24" height="24" loading="lazy">
                Разъемы
            </a>
        </li>
        <li class="edsys-mobile-catalog__item">
            <a href="<?=$CATALOG_BASE_URL?>/cat/soputstvuyushchie-tovary/" class="edsys-mobile-catalog__link">
                <img src="<?=DEFAULT_TEMPLATE_PATH?>/images/soputstvuyuschie-tovary.jpeg"
                     alt="Сопутствующие товары" class="edsys-mobile-catalog__icon" width="24" height="24"
                     loading="lazy">
                Сопутствующие товары
            </a>
        </li>
    </ul>
</div>