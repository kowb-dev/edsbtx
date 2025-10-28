<?php
/**
 * Bitrix System Auth Change Password Result Modifier
 * Template: .default (edsy_main)
 * Version: 1.6.3
 * Author: KW https://kowb.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

// Проверяем успешную смену пароля
$success = false;

// Различные варианты индикации успеха в разных версиях Bitrix
if (isset($arResult['SHOW_FORM']) && $arResult['SHOW_FORM'] !== true) {
    $success = true;
}

if (!empty($arResult['OK_MESSAGE'])) {
    $success = true;
}

if (isset($arResult['AUTH_RESULT']['TYPE']) && $arResult['AUTH_RESULT']['TYPE'] === 'OK') {
    $success = true;
}

if (isset($arResult['MESSAGE_CODE']) && $arResult['MESSAGE_CODE'] === 'CHANGE_SUCCESS') {
    $success = true;
}

// КРИТИЧЕСКИ ВАЖНО: Проверяем был ли POST запрос на смену пароля
// Если были отправлены пароли и нет ошибок - значит успех
if ($_SERVER['REQUEST_METHOD'] === 'POST' 
    && !empty($_POST['TYPE']) 
    && $_POST['TYPE'] === 'CHANGE_PWD'
    && !empty($_POST['USER_PASSWORD'])
    && !empty($_POST['USER_CONFIRM_PASSWORD'])
    && empty($arResult['ERROR_MESSAGE'])
    && empty($arResult['strError'])
) {
    // Проверяем что пароли совпадают
    if ($_POST['USER_PASSWORD'] === $_POST['USER_CONFIRM_PASSWORD']) {
        $success = true;
    }
}

// Если успех и это не страница с результатом - редиректим
if ($success && !isset($_GET['change'])) {
    // Для авторизованных пользователей - редирект на страницу успеха
    // Для неавторизованных (смена через checkword) - редирект на вход с сообщением
    global $USER;
    
    if ($USER->IsAuthorized()) {
        $successUrl = !empty($arParams['SUCCESS_URL']) ? $arParams['SUCCESS_URL'] : '/personal/password/?change=success';
    } else {
        // Пользователь сменил пароль через ссылку из письма - редирект на вход
        $successUrl = '/auth/?password_changed=yes&backurl=' . urlencode('/personal/profile/');
    }
    
    LocalRedirect($successUrl);
    die();
}

// Сохраняем флаг успеха в результате
$arResult['IS_SUCCESS'] = $success;


// Сохраняем флаг успеха в результате
$arResult['IS_SUCCESS'] = $success;