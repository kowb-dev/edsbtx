<?php
/**
 * Login and Registration Page
 * File: /auth/index.php
 * Version: 1.3.0
 * Date: 2025-10-25
 * Changes: Enhanced backurl handling with automatic referrer capture and preservation across all auth flows
 * Author: KW
 * URI: https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

use Bitrix\Main\PhoneNumber;
use Bitrix\Main\Application;

global $USER;
$arResult = [];

// Enhanced backurl preparation with automatic referrer capture
$backUrl = '';
$currentAuthPage = '/auth/';

if (isset($_REQUEST['backurl'])) {
    // Sanitize backurl to prevent open redirects
    $backUrl = htmlspecialchars($_REQUEST['backurl']);
    if (strpos($backUrl, '/') !== 0 || strpos($backUrl, '//') === 0 || preg_match('/[\\\/\.]\./', $backUrl)) {
        $backUrl = '';
    }
}

// Auto-capture referrer if no backurl provided and user came from another page on the site
if (empty($backUrl) && !empty($_SERVER['HTTP_REFERER'])) {
    $referrer = parse_url($_SERVER['HTTP_REFERER']);
    $currentHost = $_SERVER['HTTP_HOST'];
    
    // Only use referrer if it's from the same domain and not the auth page itself
    if (isset($referrer['host']) && $referrer['host'] === $currentHost) {
        $referrerPath = $referrer['path'] ?? '/';
        if (strpos($referrerPath, $currentAuthPage) !== 0 && $referrerPath !== '/') {
            $backUrl = $referrerPath;
            if (!empty($referrer['query'])) {
                $backUrl .= '?' . $referrer['query'];
            }
        }
    }
}

// Check for password change success
if (isset($_GET['password_changed']) && $_GET['password_changed'] === 'yes') {
    $arResult['SUCCESS_MESSAGE'] = "Пароль успешно изменен. Войдите, используя новый пароль.";
}

// Handle password recovery OR password change via email link
if ((isset($_GET['forgot_password']) && $_GET['forgot_password'] === 'yes') || 
    (isset($_GET['change_password']) && $_GET['change_password'] === 'yes')) {
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
            <p style="margin: 0 0 var(--space-lg) 0; font-size: var(--fs-sm); color: var(--edsys-chrome); text-align: center; line-height: var(--edsys-lh-normal);">
                <?php if (isset($_GET['USER_CHECKWORD']) && isset($_GET['USER_LOGIN'])): ?>
                    Установите новый пароль для вашего аккаунта
                <?php else: ?>
                    Введите ваш email, и мы отправим вам инструкции по восстановлению пароля
                <?php endif; ?>
            </p>
            <?php
            $authResult = null;
            
            // Build success URL with backurl preservation
            $successUrl = '/auth/?password_changed=yes';
            if (!empty($backUrl)) {
                $successUrl .= '&backurl=' . urlencode($backUrl);
            }
            
            // If we have checkword and login - show password change form
            if (isset($_GET['USER_CHECKWORD']) && isset($_GET['USER_LOGIN'])) {
                $APPLICATION->IncludeComponent(
                    "bitrix:system.auth.changepasswd",
                    ".default",
                    [
                        "AUTH_URL" => "/auth/",
                        "SUCCESS_URL" => $successUrl,
                        "LAST_LOGIN" => urldecode($_GET['USER_LOGIN']),
                        "CHECKWORD" => $_GET['USER_CHECKWORD'],
                    ],
                    false
                );
            } elseif (isset($_POST['USER_CHECKWORD']) && isset($_POST['USER_LOGIN'])) {
                $APPLICATION->IncludeComponent(
                    "bitrix:system.auth.changepasswd",
                    ".default",
                    [
                        "AUTH_URL" => "/auth/",
                        "SUCCESS_URL" => $successUrl,
                        "LAST_LOGIN" => $_POST['USER_LOGIN'],
                        "CHECKWORD" => $_POST['USER_CHECKWORD'],
                    ],
                    false
                );
            } else {
                // Show forgot password form to request reset email
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_account_info'])) {
                    $loginOrEmail = $_POST['USER_LOGIN'];
                    $email = $_POST['USER_EMAIL'] ?: $loginOrEmail;
                    if (!empty($loginOrEmail)) {
                        $authResult = CUser::SendPassword($loginOrEmail, $email);
                    }
                }

                // Preserve backurl in forgot password flow
                $forgotAuthUrl = '/auth/';
                if (!empty($backUrl)) {
                    $forgotAuthUrl .= '?backurl=' . urlencode($backUrl);
                }

                $APPLICATION->IncludeComponent(
                    "bitrix:system.auth.forgotpasswd",
                    "",
                    [
                        "AUTH_URL" => $forgotAuthUrl,
                        "REGISTER_URL" => "/auth/?register=yes" . (!empty($backUrl) ? '&backurl=' . urlencode($backUrl) : ''),
                        "~AUTH_RESULT" => $authResult,
                    ],
                    false
                );
            }
            ?>
            <div class="edsys-auth-footer">
                <a href="/auth/<?= !empty($backUrl) ? '?backurl=' . urlencode($backUrl) : '' ?>">Вернуться ко входу</a>
            </div>
        </div>
    </div>
    <?php
} else {
    // Handle confirmation message
    if (isset($_GET['confirm_registration']) && $_GET['confirm_registration'] === 'yes') {
        $arResult['SUCCESS_MESSAGE'] = "Благодарим за регистрацию на нашем сайте. В ближайшее время мы активируем ваш аккаунт.";
    }

    // Handle Login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login']) && check_bitrix_sessid()) {
        $arAuthResult = $USER->Login($_POST['USER_LOGIN'], $_POST['USER_PASSWORD'], "Y");
        if ($arAuthResult === true || (is_array($arAuthResult) && $arAuthResult['TYPE'] == 'ERROR' && strpos($arAuthResult['MESSAGE'], 'успешно авторизован') !== false)) {
            // Determine redirect URL with priority: POST backurl > GET backurl > default
            $redirectUrl = '/personal/profile/';
            
            if (!empty($_POST['backurl'])) {
                $redirectUrl = $_POST['backurl'];
            } elseif (!empty($backUrl)) {
                $redirectUrl = $backUrl;
            }
            
            LocalRedirect($redirectUrl);
        } else {
            $arResult['LOGIN_ERROR_MESSAGE'] = is_array($arAuthResult) ? $arAuthResult['MESSAGE'] : 'Неверный логин или пароль.';
        }
    }

    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Register']) && check_bitrix_sessid()) {
        $user = new CUser;
        
        // Clean phone number from mask characters
        $phoneRaw = preg_replace('/[^0-9+]/', '', $_POST['PERSONAL_PHONE']);
        
        if (strpos($phoneRaw, '8') === 0) {
            $phoneRaw = '+7' . substr($phoneRaw, 1);
        }
        
        if (strpos($phoneRaw, '+') !== 0) {
            $phoneRaw = '+7' . $phoneRaw;
        }
        
        if (!preg_match('/^\+7\d{10}$/', $phoneRaw)) {
            $arResult['REGISTER_ERROR_MESSAGE'] = 'Указан некорректный номер телефона. Формат: +7(XXX)XXX-XX-XX';
            $arResult['SHOW_REGISTER'] = true;
        } else {
            try {
                $phoneParser = PhoneNumber\Parser::getInstance();
                $parsedPhone = $phoneParser->parse($phoneRaw);
                
                if ($parsedPhone->isValid()) {
                    $phoneFormatted = $parsedPhone->format(PhoneNumber\Format::E164);
                }
            } catch (Exception $e) {
                $phoneFormatted = $phoneRaw;
            }
            
            $arFields = [
                "NAME" => trim($_POST['USER_NAME']),
                "EMAIL" => trim($_POST['USER_EMAIL']),
                "LOGIN" => trim($_POST['USER_EMAIL']),
                "LID" => SITE_ID,
                "ACTIVE" => "N",
                "GROUP_ID" => [2],
                "PASSWORD" => $_POST['USER_PASSWORD'],
                "CONFIRM_PASSWORD" => $_POST['USER_CONFIRM_PASSWORD'],
                "PERSONAL_PHONE" => $phoneFormatted,
            ];
            
            $ID = $user->Add($arFields);
            
            if (intval($ID) > 0) {
                $user->SendUserInfo($ID, SITE_ID, "Регистрация на сайте", true, "NEW_USER_CONFIRM");
                
                $request = Application::getInstance()->getContext()->getRequest();
                $currentUrl = $request->getRequestedPage();
                
                // Preserve backurl after successful registration
                $redirectParams = '?registration=success';
                if (!empty($backUrl)) {
                    $redirectParams .= '&backurl=' . urlencode($backUrl);
                }
                
                LocalRedirect($currentUrl . $redirectParams);
            } else {
                if ($user->LAST_ERROR) {
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
        
        .edsys-auth-form {
            display: none;
        }
        
        .edsys-auth-form.active {
            display: block;
        }
        
        .edsys-form-group {
            margin-bottom: 1.25rem;
        }
        
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
        
        .edsys-form-group input:focus {
            outline: none;
            border-color: var(--edsys-accent);
        }
        
        .edsys-form-group input.error {
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
        
        .edsys-auth-footer {
            text-align: center;
            margin-top: 1.25rem;
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
            .edsys-auth-container {
                padding: 1.5rem 1rem;
            }
            
            .edsys-auth-toggle button {
                padding: 0.5rem 1rem;
                font-size: 1rem;
            }
        }
    </style>

    <div class="edsys-auth-wrapper">
        <div class="edsys-auth-container">
            <!-- Toggle buttons -->
            <div class="edsys-auth-toggle">
                <button id="edsys-login-btn" class="active" type="button">Вход</button>
                <button id="edsys-register-btn" type="button">Регистрация</button>
            </div>

            <!-- Success message (after password change or registration) -->
            <?php if (!empty($arResult['SUCCESS_MESSAGE'])): ?>
                <div class="edsys-success-message"><?= htmlspecialchars($arResult['SUCCESS_MESSAGE']) ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <div id="edsys-login-form" class="edsys-auth-form active">
                <form method="post" action="<?= $APPLICATION->GetCurPageParam("", ["backurl"]) ?>">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="backurl" value="<?= htmlspecialchars($backUrl) ?>">
                    <?php if (!empty($arResult['LOGIN_ERROR_MESSAGE'])):
                        $errorMessage = str_replace(['&lt;br&gt;', '&lt;br /&gt;'], '<br>', htmlspecialchars($arResult['LOGIN_ERROR_MESSAGE']));
                        ?>
                        <div class="edsys-error-message" id="login-error"><?= $errorMessage ?></div>
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
                        <a href="?forgot_password=yes<?= !empty($backUrl) ? '&backurl=' . urlencode($backUrl) : '' ?>">Забыли пароль?</a>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div id="edsys-register-form" class="edsys-auth-form">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>" id="registration-form">
                    <?= bitrix_sessid_post() ?>
                    <input type="hidden" name="backurl" value="<?= htmlspecialchars($backUrl) ?>">
                    <?php if (!empty($arResult['REGISTER_ERROR_MESSAGE'])):
                        $errorMessage = str_replace(['&lt;br&gt;', '&lt;br /&gt;'], '<br>', htmlspecialchars($arResult['REGISTER_ERROR_MESSAGE']));
                        ?>
                        <div class="edsys-error-message" id="register-error"><?= $errorMessage ?></div>
                    <?php endif; ?>
                    <div class="edsys-form-group">
                        <label for="register-name">Имя</label>
                        <input 
                            type="text" 
                            id="register-name" 
                            name="USER_NAME" 
                            required 
                            autocomplete="name"
                            value="<?= (isset($_POST['USER_NAME']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['USER_NAME']) : '' ?>"
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
                            value="<?= (isset($_POST['USER_EMAIL']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['USER_EMAIL']) : '' ?>"
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
                            value="<?= (isset($_POST['PERSONAL_PHONE']) && isset($arResult['REGISTER_ERROR_MESSAGE'])) ? htmlspecialchars($_POST['PERSONAL_PHONE']) : '' ?>"
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
     * Version: 1.3.0
     */
    (function() {
        'use strict';
        
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
            
            phoneInput.addEventListener('input', function(e) {
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
            
            // If password was changed, show login form
            if (urlParams.has('password_changed')) {
                showForm('login');
            } else if (urlParams.has('register') || urlParams.has('registration') || showRegister || hasRegisterError || hasSuccess) {
                showForm('register');
            } else {
                showForm('login');
            }
            
            initPhoneMask();
            initPasswordValidation();
            
            if (urlParams.has('registration') && urlParams.get('registration') === 'success') {
                const backurl = urlParams.get('backurl');
                const cleanUrl = window.location.pathname + '?register' + (backurl ? '&backurl=' + encodeURIComponent(backurl) : '');
                window.history.replaceState({}, document.title, cleanUrl);
            }
            
            // Clean URL after showing password change message
            if (urlParams.has('password_changed')) {
                setTimeout(function() {
                    const backurl = urlParams.get('backurl');
                    const cleanUrl = window.location.pathname + (backurl ? '?backurl=' + encodeURIComponent(backurl) : '');
                    window.history.replaceState({}, document.title, cleanUrl);
                }, 100);
            }
        });
    })();
    </script>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>