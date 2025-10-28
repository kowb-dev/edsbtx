<?php
/**
 * Bitrix System Auth Change Password Result Modifier
 * Template: .default (edsy_main)
 * Version: 1.3.1
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

// Если успех и это не страница с результатом - редиректим
if ($success && !isset($_GET['change'])) {
    $successUrl = !empty($arParams['SUCCESS_URL']) ? $arParams['SUCCESS_URL'] : '/personal/password/?change=success';
    LocalRedirect($successUrl);
    die();
}

// Сохраняем флаг успеха в результате
$arResult['IS_SUCCESS'] = $success;