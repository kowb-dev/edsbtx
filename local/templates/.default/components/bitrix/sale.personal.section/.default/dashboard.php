<?php
/**
 * Дашборд личного кабинета
 * 
 * @version 1.1.0
 * @author KW https://kowb.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

global $USER;

$userName = $USER->GetFullName();
if (empty($userName)) {
    $userName = $USER->GetLogin();
}
?>

<div class="edsys-dashboard">
    <header class="edsys-dashboard__header">
        <h1 class="edsys-dashboard__title">
            Добро пожаловать, <span class="edsys-dashboard__name"><?= htmlspecialcharsbx($userName) ?></span>
        </h1>
        <p class="edsys-dashboard__subtitle">Управляйте своими заказами, профилем и настройками</p>
    </header>

    <div class="edsys-dashboard__grid">
        
        <a href="<?= $arResult['PATH_TO_ORDERS'] ?>" class="edsys-dashboard__card-link-wrapper">
            <article class="edsys-dashboard__card">
                <div class="edsys-dashboard__card-icon edsys-dashboard__card-icon--orders">
                    <i class="ph-thin ph-package" aria-hidden="true"></i>
                </div>
                <div class="edsys-dashboard__card-content">
                    <h2 class="edsys-dashboard__card-title">Мои заказы</h2>
                    <p class="edsys-dashboard__card-desc">Просматривайте статус текущих и историю выполненных заказов</p>
                    <?php if ($arResult['ORDERS_COUNT'] > 0): ?>
                        <div class="edsys-dashboard__card-stat">
                            <span class="edsys-dashboard__card-number"><?= $arResult['ORDERS_COUNT'] ?></span>
                            <span class="edsys-dashboard__card-label">активных заказов</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="edsys-dashboard__card-arrow" aria-label="Перейти к заказам">
                    <i class="ph-thin ph-arrow-right" aria-hidden="true"></i>
                </div>
            </article>
        </a>

        <a href="<?= $arResult['PATH_TO_PROFILE'] ?>" class="edsys-dashboard__card-link-wrapper">
            <article class="edsys-dashboard__card">
                <div class="edsys-dashboard__card-icon edsys-dashboard__card-icon--profile">
                    <i class="ph-thin ph-user-circle" aria-hidden="true"></i>
                </div>
                <div class="edsys-dashboard__card-content">
                    <h2 class="edsys-dashboard__card-title">Личная информация</h2>
                    <p class="edsys-dashboard__card-desc">Обновите данные компании, контактную информацию и настройки</p>
                    <?php if ($arResult['USER_COMPANY']): ?>
                        <div class="edsys-dashboard__card-company">
                            <i class="ph-thin ph-buildings" aria-hidden="true"></i>
                            <span><?= htmlspecialcharsbx($arResult['USER_COMPANY']) ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="edsys-dashboard__card-arrow" aria-label="Перейти к профилю">
                    <i class="ph-thin ph-arrow-right" aria-hidden="true"></i>
                </div>
            </article>
        </a>

        <a href="<?= $arResult['PATH_TO_BASKET'] ?>" class="edsys-dashboard__card-link-wrapper">
            <article class="edsys-dashboard__card">
                <div class="edsys-dashboard__card-icon edsys-dashboard__card-icon--favorites">
                    <i class="ph-thin ph-heart" aria-hidden="true"></i>
                </div>
                <div class="edsys-dashboard__card-content">
                    <h2 class="edsys-dashboard__card-title">Избранное</h2>
                    <p class="edsys-dashboard__card-desc">Сохраненные товары для быстрого доступа и повторного заказа</p>
                    <?php if ($arResult['FAVORITES_COUNT'] > 0): ?>
                        <div class="edsys-dashboard__card-stat">
                            <span class="edsys-dashboard__card-number"><?= $arResult['FAVORITES_COUNT'] ?></span>
                            <span class="edsys-dashboard__card-label">товаров в избранном</span>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="edsys-dashboard__card-arrow" aria-label="Перейти к избранному">
                    <i class="ph-thin ph-arrow-right" aria-hidden="true"></i>
                </div>
            </article>
        </a>

        <a href="<?= $arResult['PATH_TO_CONTACT'] ?>" class="edsys-dashboard__card-link-wrapper">
            <article class="edsys-dashboard__card">
                <div class="edsys-dashboard__card-icon edsys-dashboard__card-icon--address">
                    <i class="ph-thin ph-map-pin" aria-hidden="true"></i>
                </div>
                <div class="edsys-dashboard__card-content">
                    <h2 class="edsys-dashboard__card-title">Адреса доставки</h2>
                    <p class="edsys-dashboard__card-desc">Управляйте адресами для быстрого оформления заказов</p>
                </div>
                <div class="edsys-dashboard__card-arrow" aria-label="Перейти к адресам">
                    <i class="ph-thin ph-arrow-right" aria-hidden="true"></i>
                </div>
            </article>
        </a>

    </div>
</div>