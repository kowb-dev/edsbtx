<?php
/**
 * Login and Registration Page
 * Version: 1.2.8
 * Date: 2025-10-24
 * Changes:
 * - Fixed duplicate PHP tags in change password block
 * - Safer access to $_POST keys
 * - Stable phone formatting with default fallback
 * - Correct SendUserInfo signature (4 args) and added die() after redirects
 * - Simplified/robust login success check
 * - Added CSS to stack "change password" labels above inputs (no template override)
 * Author: KW
 * URI: https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

use Bitrix\Main\PhoneNumber;
use Bitrix\Main\Application;

global $USER;
$arResult = [];

/** Helper: safely pretty-print Bitrix messages with <br> */
function edsys_render_message($message) {
    if (!is_string($message)) {
        return '';
    }
    // normalize Bitrix <br> to newlines, then escape, then turn back to <br>
    $msg = str_replace(['<br>', '<br />', '&lt;br&gt;', '&lt;br /&gt;'], "\n", $message);
    return nl2br(htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
}

// Handle password recovery
if (isset($_GET['forgot_password']) && $_GET['forgot_password'] === 'yes') {
    $APPLICATION->SetTitle("Восстановление пароля");
    ?>
    <style>
        /* Password recovery form styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        .edsys-auth-container {
            background-color: var(--edsys-carbon);
            padding: var(--space-md) var(--space-lg);
            border-radius: var(--radius-md);
            box-shadow: var(--edsys-shadow);
            width: 100%;
            max-width: 25rem;
            color: var(--edsys-white);
        }
        .edsys-form-group {
            margin-bottom: var(--space-md);
        }
        .edsys-form-group label {
            display: block;
            margin-bottom: var(--space-xs);
            color: var(--edsys-chrome);
            font-size: var(--fs-xs);
        }
        .edsys-form-group input[type="text"],
        .edsys-form-group input[type="email"] {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--edsys-carbon);
            border: 1px solid var(--edsys-steel);
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            box-sizing: border-box;
            font-size: var(--fs-base);
            transition: var(--edsys-transition);
        }
        .edsys-form-group input[type="text"]:focus,
        .edsys-form-group input[type="email"]:focus {
            outline: none;
            border-color: var(--edsys-accent);
        }
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent);
            border: none;
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            font-size: var(--fs-base);
            cursor: pointer;
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
        }
        .edsys-auth-submit:hover {
            background-color: var(--edsys-accent-hover);
        }
        .edsys-auth-submit:active {
            transform: scale(0.98);
        }
        .edsys-error-message {
            color: var(--edsys-accent);
            background-color: rgba(255, 37, 69, 0.1);
            border: 1px solid var(--edsys-accent);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .edsys-success-message {
            color: var(--edsys-neon);
            background-color: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--edsys-neon);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .edsys-auth-footer {
            text-align: center;
            margin-top: var(--space-md);
        }
        .edsys-auth-footer a {
            color: var(--edsys-accent);
            text-decoration: none;
            font-size: var(--fs-sm);
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
            text-shadow: 1px 0 black;
        }
        .edsys-auth-footer a:hover {
            color: var(--edsys-accent-hover);
        }
        /* Bitrix component overrides */
        form[name="bform"] p,
        form[name="bform"] div,
        form[name="bform"] b {
            color: var(--edsys-white);
            font-size: var(--fs-base);
            line-height: var(--edsys-lh-normal);
            margin-bottom: var(--space-xs);
        }
        form[name="bform"] div[style="margin-top: 16px"] {
            margin-top: var(--space-md) !important;
        }
        form[name="bform"] div[style="margin-top: 20px"] {
            margin-top: var(--space-lg) !important;
        }
        form[name="bform"] p:first-of-type {
            margin-bottom: var(--space-md);
        }
        form[name="bform"] div:has(input[name="USER_LOGIN"]) + div {
            margin-top: var(--space-xs);
        }
        form[name="bform"] input[type="text"][name="USER_LOGIN"],
        form[name="bform"] input[type="text"][name="USER_PHONE_NUMBER"],
        form[name="bform"] input[type="text"][name="captcha_word"] {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--edsys-carbon);
            border: 1px solid var(--edsys-steel);
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            box-sizing: border-box;
            font-size: var(--fs-base);
            transition: var(--edsys-transition);
        }
        form[name="bform"] input[type="text"][name="USER_LOGIN"]:focus,
        form[name="bform"] input[type="text"][name="USER_PHONE_NUMBER"]:focus,
        form[name="bform"] input[type="text"][name="captcha_word"]:focus {
            outline: none;
            border-color: var(--edsys-accent);
        }
        form[name="bform"] input[type="submit"][name="send_account_info"] {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent);
            border: none;
            border-radius: var(--radius-sm);
            color: var(--edsys-white) !important;
            cursor: pointer;
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
            font-size: var(--fs-base) !important;
            position: relative;
            overflow: visible;
            text-indent: 0 !important;
            line-height: normal !important;
            height: auto !important;
        }
        form[name="bform"] input[type="submit"][name="send_account_info"]:hover {
            background-color: var(--edsys-accent-hover);
        }
        form[name="bform"] input[type="submit"][name="send_account_info"]:active {
            transform: scale(0.98);
        }
        .bx-auth-note {
            color: var(--edsys-text-light);
            font-size: var(--fs-xs);
            margin-top: var(--space-md);
            text-align: center;
        }
        .bx-auth-note a {
            color: var(--edsys-accent);
            text-decoration: none;
            transition: var(--edsys-transition);
        }
        .bx-auth-note a:hover {
            color: var(--edsys-accent-hover);
        }
        .bx-auth-message {
            color: var(--edsys-neon);
            background-color: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--edsys-neon);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .bx-auth-error {
            color: var(--edsys-accent);
            background-color: rgba(255, 37, 69, 0.1);
            border: 1px solid var(--edsys-accent);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        /* Mobile optimization */
        @media (max-width: 480px) {
            .edsys-auth-container {
                padding: 1.5rem 1rem;
            }
        }
    </style>
    <div class="edsys-auth-wrapper">
        <div class="edsys-auth-container">
            <h2 style="margin: 0 0 var(--space-md) 0; font-size: var(--fs-xl); color: var(--edsys-white); text-align: center; font-weight: var(--edsys-font-bold);">Восстановление пароля</h2>
            <p style="margin: 0 0 var(--space-lg) 0; font-size: var(--fs-sm); color: var(--edsys-chrome); text-align: center; line-height: var(--edsys-lh-normal);">Введите ваш email, и мы отправим вам инструкции по восстановлению пароля</p>
            <?php
            $authResult = null;
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_account_info'])) {
                $loginOrEmail = isset($_POST['USER_LOGIN']) ? $_POST['USER_LOGIN'] : '';
                // Формы иногда кладут USER_EMAIL = USER_LOGIN — подстрахуемся
                $email = !empty($_POST['USER_EMAIL']) ? $_POST['USER_EMAIL'] : $loginOrEmail;
                if ($loginOrEmail !== '') {
                    $authResult = CUser::SendPassword($loginOrEmail, $email);
                }
            }

            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.forgotpasswd",
                "",
                [
                    "AUTH_URL" => "/auth/",
                    "REGISTER_URL" => "/auth/?register=yes",
                    "~AUTH_RESULT" => $authResult,
                ],
                false
            );
            ?>
            <div class="edsys-auth-footer">
                <a href="/auth/">Вернуться ко входу</a>
            </div>
        </div>
    </div>
    <?php
} elseif (isset($_GET['change_password']) && $_GET['change_password'] === 'yes') {
    $APPLICATION->SetTitle("Смена пароля");
    ?>
    <style>
        /* Change password form styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        .edsys-auth-container {
            background-color: var(--edsys-carbon);
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: var(--radius-lg);
            box-shadow: var(--edsys-shadow);
            width: 100%;
            max-width: 25rem;
            color: var(--edsys-white);
        }
        .edsys-form-group {
            margin-bottom: var(--space-md);
        }
        .edsys-form-group label {
            display: block;
            margin-bottom: var(--space-xs);
            color: var(--edsys-chrome);
            font-size: var(--fs-sm);
        }
        .edsys-form-group input[type="text"],
        .edsys-form-group input[type="email"],
        .edsys-form-group input[type="password"] {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--edsys-carbon);
            border: 1px solid var(--edsys-steel);
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            box-sizing: border-box;
            font-size: var(--fs-base);
            transition: var(--edsys-transition);
        }
        .edsys-form-group input[type="text"]:focus,
        .edsys-form-group input[type="email"]:focus,
        .edsys-form-group input[type="password"]:focus {
            outline: none;
            border-color: var(--edsys-accent);
        }
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent);
            border: none;
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            font-size: var(--fs-base);
            cursor: pointer;
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
        }
        .edsys-auth-submit:hover {
            background-color: var(--edsys-accent-hover);
        }
        .edsys-auth-submit:active {
            transform: scale(0.98);
        }
        .edsys-error-message {
            color: var(--edsys-accent);
            background-color: rgba(255, 37, 69, 0.1);
            border: 1px solid var(--edsys-accent);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .edsys-success-message {
            color: var(--edsys-neon);
            background-color: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--edsys-neon);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .edsys-auth-footer {
            text-align: center;
            margin-top: var(--space-md);
        }
        .edsys-auth-footer a {
            color: var(--edsys-accent);
            text-decoration: none;
            font-size: var(--fs-sm);
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
            text-shadow: 1px 0 black;
        }
        .edsys-auth-footer a:hover {
            color: var(--edsys-accent-hover);
        }

        /* Bitrix component overrides for changepasswd */
        form[name="bform"] p,
        form[name="bform"] div,
        form[name="bform"] b {
            color: var(--edsys-white);
            font-size: var(--fs-base);
            line-height: var(--edsys-lh-normal);
            margin-bottom: var(--space-xs);
        }
        form[name="bform"] div[style="margin-top: 16px"] {
            margin-top: var(--space-md) !important;
        }
        form[name="bform"] div[style="margin-top: 20px"] {
            margin-top: var(--space-lg) !important;
        }
        form[name="bform"] p:first-of-type {
            margin-bottom: var(--space-md);
        }
        form[name="bform"] div:has(input[name="USER_LOGIN"]) + div {
            margin-top: var(--space-xs);
        }
        form[name="bform"] input[type="text"],
        form[name="bform"] input[type="email"],
        form[name="bform"] input[type="password"],
        form[name="bform"] input[type="text"][name="USER_LOGIN"],
        form[name="bform"] input[type="text"][name="USER_PHONE_NUMBER"],
        form[name="bform"] input[type="text"][name="captcha_word"] {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--edsys-carbon);
            border: 1px solid var(--edsys-steel);
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            box-sizing: border-box;
            font-size: var(--fs-base);
            transition: var(--edsys-transition);
        }
        form[name="bform"] input[type="text"]:focus,
        form[name="bform"] input[type="email"]:focus,
        form[name="bform"] input[type="password"]:focus,
        form[name="bform"] input[type="text"][name="USER_LOGIN"]:focus,
        form[name="bform"] input[type="text"][name="USER_PHONE_NUMBER"]:focus,
        form[name="bform"] input[type="text"][name="captcha_word"]:focus {
            outline: none;
            border-color: var(--edsys-accent);
        }
        form[name="bform"] input[type="submit"][name="change_pwd"],
        form[name="bform"] button[type="submit"] {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent);
            border: none;
            border-radius: var(--radius-sm);
            color: var(--edsys-white) !important;
            font-size: var(--fs-base) !important;
            cursor: pointer;
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
            position: relative;
            overflow: visible;
            text-indent: 0 !important;
            line-height: normal !important;
            height: auto !important;
        }
        form[name="bform"] input[type="submit"][name="change_pwd"]:hover,
        form[name="bform"] button[type="submit"]:hover {
            background-color: var(--edsys-accent-hover);
        }
        form[name="bform"] input[type="submit"][name="change_pwd"]:active,
        form[name="bform"] button[type="submit"]:active {
            transform: scale(0.98);
        }
        form[name="bform"] label {
            display: block;
            margin-bottom: var(--space-xs);
            color: var(--edsys-chrome);
            font-size: var(--fs-sm);
        }
        .bx-auth-note {
            color: var(--edsys-text-light);
            font-size: var(--fs-xs);
            margin-top: var(--space-md);
            text-align: center;
        }
        .bx-auth-note a {
            color: var(--edsys-accent);
            text-decoration: none;
            transition: var(--edsys-transition);
        }
        .bx-auth-note a:hover {
            color: var(--edsys-accent-hover);
        }
        .bx-auth-message {
            color: var(--edsys-neon);
            background-color: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--edsys-neon);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }
        .bx-auth-error {
            color: var(--edsys-accent);
            background-color: rgba(255, 37, 69, 0.1);
            border: 1px solid var(--edsys-accent);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: var(--space-md);
            text-align: center;
            font-size: var(--fs-xs);
            line-height: var(--edsys-lh-snug);
        }

        /* --- Stack table labels above inputs (no template override) --- */
        .edsys-auth-container form[name="bform"] table,
        .edsys-auth-container form[name="bform"] tbody,
        .edsys-auth-container form[name="bform"] tr {
            display: block;
            width: 100%;
        }
        .edsys-auth-container form[name="bform"] tr + tr {
            margin-top: var(--space-sm);
        }
        .edsys-auth-container form[name="bform"] td {
            display: block;
            width: 100%;
            padding: 0;
            border: 0;
        }
        .edsys-auth-container form[name="bform"] td:first-child {
            margin-bottom: var(--space-2xs, 0.375rem);
            color: var(--edsys-chrome);
            font-size: var(--fs-sm);
            line-height: var(--edsys-lh-normal);
        }
        .edsys-auth-container form[name="bform"] td:nth-child(2) input[type="text"],
        .edsys-auth-container form[name="bform"] td:nth-child(2) input[type="password"],
        .edsys-auth-container form[name="bform"] td:nth-child(2) input[type="email"] {
            width: 100%;
            box-sizing: border-box;
        }
        .edsys-auth-container form[name="bform"] input[type="submit"][name="change_pwd"],
        .edsys-auth-container form[name="bform"] button[type="submit"] {
            width: 100%;
            margin-top: var(--space-md);
        }
        .edsys-auth-container form[name="bform"] .bx-auth-title,
        .edsys-auth-container form[name="bform"] .bx-auth-subtitle {
            display: none;
        }
        .edsys-auth-container form[name="bform"] p,
        .edsys-auth-container form[name="bform"] .bx-auth-note {
            margin-top: var(--space-sm);
            color: var(--edsys-chrome);
            font-size: var(--fs-xs);
        }

        /* Mobile optimization */
        @media (max-width: 480px) {
            .edsys-auth-container {
                padding: 1.5rem 1rem;
            }
        }
    </style>
    <div class="edsys-auth-wrapper">
        <div class="edsys-auth-container">
            <h2 style="margin: 0 0 var(--space-md) 0; font-size: var(--fs-xl); color: var(--edsys-white); text-align: center; font-weight: var(--edsys-font-bold);">Смена пароля</h2>
            <?php
            $lastLogin = isset($_GET["USER_LOGIN"]) ? $_GET["USER_LOGIN"] : "";
            $checkword = isset($_GET["USER_CHECKWORD"]) ? $_GET["USER_CHECKWORD"] : "";

            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.changepasswd",
                "",
                [
                    "AUTH_URL" => "/auth/",
                    "REGISTER_URL" => "/auth/?register=yes",
                    "LAST_LOGIN" => $lastLogin,
                    "CHECKWORD" => $checkword,
                ],
                false
            );
            ?>
            <div class="edsys-auth-footer">
                <a href="/auth/">Вернуться ко входу</a>
            </div>
        </div>
    </div>
    <?php

} else {
    // Handle confirmation message
    if (isset($_GET['confirm_registration']) && $_GET['confirm_registration'] === 'yes') {
        $arResult['SUCCESS_MESSAGE'] = "Благодарим за регистрацию на нашем сайте. В ближайшее время мы активируем ваш аккаунт.";
        $arResult['SHOW_REGISTER'] = true;
    }

    // Handle Login
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Login']) && check_bitrix_sessid()) {
        $userLogin = isset($_POST['USER_LOGIN']) ? $_POST['USER_LOGIN'] : '';
        $userPassword = isset($_POST['USER_PASSWORD']) ? $_POST['USER_PASSWORD'] : '';
        $arAuthResult = $USER->Login($userLogin, $userPassword, "Y");
        if ($arAuthResult === true) {
            LocalRedirect("/personal/profile/");
            die();
        } else {
            $arResult['LOGIN_ERROR_MESSAGE'] = is_array($arAuthResult) && isset($arAuthResult['MESSAGE'])
                ? $arAuthResult['MESSAGE']
                : 'Неверный логин или пароль.';
        }
    }

    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['Register']) && check_bitrix_sessid()) {
        $user = new CUser;

        // Clean phone number from mask characters
        $rawPhoneInput = isset($_POST['PERSONAL_PHONE']) ? $_POST['PERSONAL_PHONE'] : '';
        $phoneRaw = preg_replace('/[^0-9+]/', '', $rawPhoneInput);

        // If phone starts with 8, replace with +7
        if (strpos($phoneRaw, '8') === 0) {
            $phoneRaw = '+7' . substr($phoneRaw, 1);
        }

        // If phone doesn't start with +, add +7
        if ($phoneRaw !== '' && strpos($phoneRaw, '+') !== 0) {
            $phoneRaw = '+7' . $phoneRaw;
        }

        // Validate phone format (should be +7XXXXXXXXXX)
        if (!preg_match('/^\+7\d{10}$/', $phoneRaw)) {
            $arResult['REGISTER_ERROR_MESSAGE'] = 'Указан некорректный номер телефона. Формат: +7(XXX)XXX-XX-XX';
            $arResult['SHOW_REGISTER'] = true;
        } else {
            // Default to raw, try to normalize to E.164 using Bitrix phone lib if available
            $phoneFormatted = $phoneRaw;
            try {
                if (class_exists(\Bitrix\Main\PhoneNumber\Parser::class)) {
                    $phoneParser = PhoneNumber\Parser::getInstance();
                    $parsedPhone = $phoneParser->parse($phoneRaw);
                    if ($parsedPhone && method_exists($parsedPhone, 'isValid') && $parsedPhone->isValid()) {
                        $phoneFormatted = $parsedPhone->format(PhoneNumber\Format::E164);
                    }
                }
            } catch (\Throwable $e) {
                // keep $phoneFormatted as $phoneRaw
            }

            // Prepare user fields
            $name  = isset($_POST['USER_NAME']) ? trim($_POST['USER_NAME']) : '';
            $email = isset($_POST['USER_EMAIL']) ? trim($_POST['USER_EMAIL']) : '';
            $pass  = isset($_POST['USER_PASSWORD']) ? $_POST['USER_PASSWORD'] : '';
            $pass2 = isset($_POST['USER_CONFIRM_PASSWORD']) ? $_POST['USER_CONFIRM_PASSWORD'] : '';

            $arFields = [
                "NAME" => $name,
                "EMAIL" => $email,
                "LOGIN" => $email,
                "LID" => SITE_ID,
                "ACTIVE" => "N",
                "GROUP_ID" => [2],
                "PASSWORD" => $pass,
                "CONFIRM_PASSWORD" => $pass2,
                "PERSONAL_PHONE" => $phoneFormatted,
            ];

            // Add user
            $ID = $user->Add($arFields);

            if ((int)$ID > 0) {
                // Send info (4-арг сигнатура совместима с большинством версий)
                $user->SendUserInfo($ID, SITE_ID, "Регистрация на сайте", true);

                // Redirect to same page with success flag
                $request = Application::getInstance()->getContext()->getRequest();
                $currentUrl = $request->getRequestedPage();
                LocalRedirect($currentUrl . "?registration=success");
                die();
            } else {
                if (!empty($user->LAST_ERROR)) {
                    $arResult['REGISTER_ERROR_MESSAGE'] = $user->LAST_ERROR;
                } else {
                    $arResult['REGISTER_ERROR_MESSAGE'] = "Ошибка при регистрации. Пожалуйста, попробуйте еще раз.";
                }
                $arResult['SHOW_REGISTER'] = true;
            }
        }
    }

    // Check for success message from redirect
    if (isset($_GET['registration']) && $_GET['registration'] === 'success') {
        $arResult['SUCCESS_MESSAGE'] = "Вы успешно зарегистрированы. Пожалуйста, проверьте свою почту для подтверждения регистрации.";
        $arResult['SHOW_REGISTER'] = true;
    }
    ?>

    <style>
        /* Auth forms styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        .edsys-auth-container {
            background-color: var(--edsys-carbon);
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: var(--radius-lg);
            box-shadow: var(--edsys-shadow);
            width: 100%;
            max-width: 25rem;
            color: var(--edsys-white);
        }
        .edsys-auth-toggle {
            display: flex;
            justify-content: center;
            margin-bottom: 1.875rem;
            gap: 0.5rem;
        }
        .edsys-auth-toggle button {
            background: none;
            border: none;
            color: var(--edsys-steel);
            font-size: clamp(1rem, 2vw, 1.125rem);
            padding: 0.625rem 1.25rem;
            cursor: pointer;
            transition: var(--edsys-transition);
            border-bottom: 2px solid transparent;
        }
        .edsys-auth-toggle button.active {
            color: var(--edsys-accent);
            border-bottom-color: var(--edsys-accent);
            font-weight: var(--edsys-font-bold);
            text-shadow: 1px 0 black;
        }
        .edsys-auth-form { display: none; }
        .edsys-auth-form.active { display: block; }
        .edsys-form-group { margin-bottom: 1.25rem; }
        .edsys-form-group label {
            display: block;
            margin-bottom: 0.375rem;
            color: var(--edsys-chrome);
            font-size: var(--fs-sm);
        }
        .edsys-form-group input {
            width: 100%;
            padding: 0.75rem;
            background-color: var(--edsys-carbon);
            border: 1px solid var(--edsys-steel);
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            box-sizing: border-box;
            font-size: var(--fs-base);
            transition: var(--edsys-transition);
        }
        .edsys-form-group input:focus { outline: none; border-color: var(--edsys-accent); }
        .edsys-form-group input.error { border-color: var(--edsys-accent); }
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent);
            border: none;
            border-radius: var(--radius-sm);
            color: var(--edsys-white);
            font-size: var(--fs-base);
            cursor: pointer;
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
        }
        .edsys-auth-submit:hover { background-color: var(--edsys-accent-hover); }
        .edsys-auth-submit:active { transform: scale(0.98); }
        .edsys-auth-footer { text-align: center; margin-top: 1.25rem; }
        .edsys-auth-footer a {
            color: var(--edsys-accent);
            text-decoration: none;
            font-size: var(--fs-sm);
            transition: var(--edsys-transition);
            font-weight: var(--edsys-font-bold);
            text-shadow: 1px 0 black;
        }
        .edsys-auth-footer a:hover { color: var(--edsys-accent-hover); }
        .edsys-error-message {
            color: var(--edsys-accent);
            background-color: rgba(255, 37, 69, 0.1);
            border: 1px solid var(--edsys-accent);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: var(--fs-sm);
            line-height: var(--edsys-lh-snug);
        }
        .edsys-success-message {
            color: var(--edsys-neon);
            background-color: rgba(57, 255, 20, 0.1);
            border: 1px solid var(--edsys-neon);
            padding: 0.75rem;
            border-radius: var(--radius-sm);
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: var(--fs-sm);
            line-height: var(--edsys-lh-snug);
        }
        /* Mobile optimization */
        @media (max-width: 480px) {
            .edsys-auth-container { padding: 1.5rem 1rem; }
            .edsys-auth-toggle button { padding: 0.5rem 1rem; font-size: 1rem; }
        }
    </style>

    <div class="edsys-auth-wrapper">
        <div class="edsys-auth-container">
            <!-- Toggle buttons -->
            <div class="edsys-auth-toggle">
                <button id="edsys-login-btn" class="active" type="button">Вход</button>
                <button id="edsys-register-btn" type="button">Регистрация</button>
            </div>

            <!-- Login Form -->
            <div id="edsys-login-form" class="edsys-auth-form active">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
                    <?php if (!empty($arResult['LOGIN_ERROR_MESSAGE'])): ?>
                        <div class="edsys-error-message" id="login-error"><?= edsys_render_message($arResult['LOGIN_ERROR_MESSAGE']) ?></div>
                    <?php endif; ?>
                    <div class="edsys-form-group">
                        <label for="login-email">Email или телефон</label>
                        <input type="text" id="login-email" name="USER_LOGIN" required autocomplete="username">
                    </div>
                    <div class="edsys-form-group">
                        <label for="login-password">Пароль</label>
                        <input type="password" id="login-password" name="USER_PASSWORD" required autocomplete="current-password">
                    </div>
                    <button type="submit" name="Login" class="edsys-auth-submit">Войти</button>
                    <div class="edsys-auth-footer">
                        <a href="?forgot_password=yes">Забыли пароль?</a>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div id="edsys-register-form" class="edsys-auth-form">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>" id="registration-form">
                    <?= bitrix_sessid_post() ?>
                    <?php if (!empty($arResult['SUCCESS_MESSAGE'])): ?>
                        <div class="edsys-success-message"><?= edsys_render_message($arResult['SUCCESS_MESSAGE']) ?></div>
                    <?php endif; ?>
                    <?php if (!empty($arResult['REGISTER_ERROR_MESSAGE'])): ?>
                        <div class="edsys-error-message" id="register-error"><?= edsys_render_message($arResult['REGISTER_ERROR_MESSAGE']) ?></div>
                    <?php endif; ?>
                    <div class="edsys-form-group">
                        <label for="register-name">Имя</label>
                        <input
                            type="text"
                            id="register-name"
                            name="USER_NAME"
                            required
                            autocomplete="name"
                            value="<?= (isset($_POST['USER_NAME']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['USER_NAME'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?>"
                        >
                    </div>
                    <div class="edsys-form-group">
                        <label for="register-email">Email</label>
                        <input
                            type="email"
                            id="register-email"
                            name="USER_EMAIL"
                            required
                            autocomplete="email"
                            value="<?= (isset($_POST['USER_EMAIL']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['USER_EMAIL'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?>"
                        >
                    </div>
                    <div class="edsys-form-group">
                        <label for="register-phone">Номер телефона</label>
                        <input
                            type="tel"
                            id="register-phone"
                            name="PERSONAL_PHONE"
                            required
                            autocomplete="tel"
                            placeholder="+7(XXX)XXX-XX-XX"
                            value="<?= (isset($_POST['PERSONAL_PHONE']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['PERSONAL_PHONE'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') : '' ?>"
                        >
                    </div>
                    <div class="edsys-form-group">
                        <label for="register-password">Пароль</label>
                        <input
                            type="password"
                            id="register-password"
                            name="USER_PASSWORD"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                    </div>
                    <div class="edsys-form-group">
                        <label for="register-confirm-password">Подтвердите пароль</label>
                        <input
                            type="password"
                            id="register-confirm-password"
                            name="USER_CONFIRM_PASSWORD"
                            required
                            autocomplete="new-password"
                            minlength="6"
                        >
                    </div>
                    <button type="submit" name="Register" class="edsys-auth-submit">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>

    <script>
    /**
     * Auth Form Handler
     * Version: 1.2.4
     */
    (function() {
        'use strict';

        // Form switcher
        function showForm(formName) {
            const forms = ['login', 'register'];
            forms.forEach(name => {
                const form = document.getElementById(`edsys-${name}-form`);
                const btn = document.getElementById(`edsys-${name}-btn`);
                if (form && btn) {
                    if (name === formName) {
                        form.classList.add('active');
                        btn.classList.add('active');
                    } else {
                        form.classList.remove('active');
                        btn.classList.remove('active');
                    }
                }
            });
        }

        // Phone mask implementation
        function initPhoneMask() {
            const phoneInput = document.getElementById('register-phone');
            if (!phoneInput) return;

            const prefix = '+7(';

            phoneInput.addEventListener('focus', function() {
                if (this.value === '') {
                    this.value = prefix;
                }
            });

            phoneInput.addEventListener('blur', function() {
                if (this.value === prefix || this.value === '+7') {
                    this.value = '';
                }
            });

            phoneInput.addEventListener('input', function() {
                let value = this.value.replace(/\D/g, '');

                if (value.length > 0 && value[0] !== '7') {
                    value = '7' + value;
                }

                let formattedValue = '+7';

                if (value.length > 1) {
                    formattedValue += '(' + value.substring(1, 4);
                }
                if (value.length >= 4) {
                    formattedValue += ')';
                }
                if (value.length > 4) {
                    formattedValue += value.substring(4, 7);
                }
                if (value.length > 7) {
                    formattedValue += '-' + value.substring(7, 9);
                }
                if (value.length > 9) {
                    formattedValue += '-' + value.substring(9, 11);
                }

                this.value = formattedValue;
            });

            phoneInput.addEventListener('keydown', function(e) {
                if (e.key === 'Backspace') {
                    const cursorPos = this.selectionStart;
                    if (cursorPos <= 3) {
                        e.preventDefault();
                        this.value = '';
                    }
                }
            });
        }

        // Password validation
        function initPasswordValidation() {
            const form = document.getElementById('registration-form');
            if (!form) return;

            form.addEventListener('submit', function(e) {
                const password = document.getElementById('register-password').value;
                const confirmPassword = document.getElementById('register-confirm-password').value;
                const phoneInput = document.getElementById('register-phone');

                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Пароли не совпадают');
                    return false;
                }

                const phoneValue = phoneInput.value.replace(/\D/g, '');
                if (phoneValue.length !== 11 || phoneValue[0] !== '7') {
                    e.preventDefault();
                    phoneInput.classList.add('error');
                    alert('Пожалуйста, введите корректный номер телефона в формате +7(XXX)XXX-XX-XX');
                    return false;
                }

                phoneInput.classList.remove('error');
                return true;
            });
        }

        // Initialize on DOM ready
        document.addEventListener('DOMContentLoaded', function() {
            const loginBtn = document.getElementById('edsys-login-btn');
            const registerBtn = document.getElementById('edsys-register-btn');

            if (loginBtn) {
                loginBtn.addEventListener('click', function() {
                    showForm('login');
                });
            }

            if (registerBtn) {
                registerBtn.addEventListener('click', function() {
                    showForm('register');
                });
            }

            const urlParams = new URLSearchParams(window.location.search);
            const hasRegisterError = document.getElementById('register-error');
            const hasSuccess = document.querySelector('.edsys-success-message');
            const showRegister = <?= !empty($arResult['SHOW_REGISTER']) ? 'true' : 'false' ?>;

            if (urlParams.has('register') || urlParams.has('registration') || showRegister || hasRegisterError || hasSuccess) {
                showForm('register');
            } else {
                showForm('login');
            }

            initPhoneMask();
            initPasswordValidation();

            if (urlParams.has('registration') && urlParams.get('registration') === 'success') {
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl + '?register');
            }
        });
    })();
    </script>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>

