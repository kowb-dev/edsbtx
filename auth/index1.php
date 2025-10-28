<?php
/**
 * Login and Registration Page
 * Version: 1.2.1
 * Date: 2025-10-22
 * Changes: Added password recovery logic using Bitrix component.
 * Author: KW
 * URI: https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

use Bitrix\Main\PhoneNumber;
use Bitrix\Main\Application;

global $USER;
$arResult = [];

// Handle password recovery
if (isset($_GET['forgot_password']) && $_GET['forgot_password'] === 'yes') {
    $APPLICATION->SetTitle("Восстановление пароля");
    ?>
<?php
/**
 * Login and Registration Page
 * Version: 1.2.1
 * Date: 2025-10-22
 * Changes: Added password recovery logic using Bitrix component.
 * Author: KW
 * URI: https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

use Bitrix\Main\PhoneNumber;
use Bitrix\Main\Application;

global $USER;
$arResult = [];

// Handle password recovery
if (isset($_GET['forgot_password']) && $_GET['forgot_password'] === 'yes') {
    $APPLICATION->SetTitle("Восстановление пароля");
    ?>
<?php
/**
 * Login and Registration Page
 * Version: 1.2.2
 * Date: 2025-10-22
 * Changes: Refined CSS for password recovery form to ensure text visibility and consistent styling, corrected rgba color format.
 * Author: KW
 * URI: https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

use Bitrix\Main\PhoneNumber;
use Bitrix\Main\Application;

global $USER;
$arResult = [];

// Handle password recovery
if (isset($_GET['forgot_password']) && $_GET['forgot_password'] === 'yes') {
    $APPLICATION->SetTitle("Восстановление пароля");
    ?>
    <style>
        /* Base styles for password recovery form to match existing auth styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        .edsys-auth-container {
            background-color: var(--edsys-carbon); /* Using CSS variable */
            padding: var(--space-md) var(--space-lg); /* Using CSS variables */
            border-radius: var(--radius-md); /* Using CSS variable */
            box-shadow: var(--edsys-shadow); /* Using CSS variable */
            width: 100%;
            max-width: 25rem;
            color: var(--edsys-white); /* Using CSS variable */
        }
        .edsys-form-group {
            margin-bottom: var(--space-md); /* Using CSS variable */
        }
        .edsys-form-group label {
            display: block;
            margin-bottom: var(--space-xs); /* Using CSS variable */
            color: var(--edsys-text-light); /* Using CSS variable */
            font-size: var(--fs-xs); /* Using CSS variable */
        }
        .edsys-form-group input[type="text"],
        .edsys-form-group input[type="email"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #333; /* Keeping specific color for input background */
            border: 1px solid #555; /* Keeping specific color for input border */
            border-radius: var(--radius-sm); /* Using CSS variable */
            color: var(--edsys-white); /* Using CSS variable */
            box-sizing: border-box;
            font-size: var(--fs-base); /* Using CSS variable */
            transition: var(--edsys-transition); /* Using CSS variable */
        }
        .edsys-form-group input[type="text"]:focus,
        .edsys-form-group input[type="email"]:focus {
            outline: none;
            border-color: var(--edsys-accent); /* Using CSS variable */
        }
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: var(--edsys-accent); /* Using CSS variable */
            border: none;
            border-radius: var(--radius-sm); /* Using CSS variable */
            color: var(--edsys-white); /* Using CSS variable */
            font-size: var(--fs-base); /* Using CSS variable */
            cursor: pointer;
            transition: var(--edsys-transition); /* Using CSS variable */
            font-weight: var(--edsys-font-bold); /* Using CSS variable */
        }
        .edsys-auth-submit:hover {
            background-color: var(--edsys-accent-hover); /* Using CSS variable */
        }
        .edsys-auth-submit:active {
            transform: scale(0.98);
        }
        .edsys-error-message {
            color: var(--edsys-accent); /* Using CSS variable */
            background-color: rgba(255, 71, 87, 0.1); /* Corrected rgba for --edsys-accent */
            border: 1px solid var(--edsys-accent); /* Using CSS variable */
            padding: 0.75rem;
            border-radius: var(--radius-sm); /* Using CSS variable */
            margin-bottom: var(--space-md); /* Using CSS variable */
            text-align: center;
            font-size: var(--fs-xs); /* Using CSS variable */
            line-height: var(--edsys-lh-snug); /* Using CSS variable */
        }
        .edsys-success-message {
            color: var(--edsys-neon); /* Using CSS variable */
            background-color: rgba(57, 255, 20, 0.1); /* Corrected rgba for --edsys-neon */
            border: 1px solid var(--edsys-neon); /* Using CSS variable */
            padding: 0.75rem;
            border-radius: var(--radius-sm); /* Using CSS variable */
            margin-bottom: var(--space-md); /* Using CSS variable */
            text-align: center;
            font-size: var(--fs-xs); /* Using CSS variable */
            line-height: var(--edsys-lh-snug); /* Using CSS variable */
        }
        .edsys-auth-footer {
            text-align: center;
            margin-top: var(--space-md); /* Using CSS variable */
        }
        .edsys-auth-footer a {
            color: var(--edsys-accent); /* Using CSS variable */
            text-decoration: none;
            font-size: var(--fs-xs); /* Using CSS variable */
            transition: var(--edsys-transition); /* Using CSS variable */
        }
        .edsys-auth-footer a:hover {
            color: var(--edsys-accent-hover); /* Using CSS variable */
        }
        /* Bitrix component specific overrides */
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
        /* Styling for the input field */
        form[name="bform"] input[type="text"][name="USER_LOGIN"],
        form[name="bform"] input[type="text"][name="USER_PHONE_NUMBER"],
        form[name="bform"] input[type="text"][name="captcha_word"] {
            width: 100%;
            padding: 0.75rem;
            background-color: #333;
            border: 1px solid #555;
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
        /* Styling for the submit button */
        form[name="bform"] input[type="submit"][name="send_account_info"] {
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
        form[name="bform"] input[type="submit"][name="send_account_info"]:hover {
            background-color: var(--edsys-accent-hover);
        }
        form[name="bform"] input[type="submit"][name="send_account_info"]:active {
            transform: scale(0.98);
        }
        .bx-auth-note {
            color: var(--edsys-text-light); /* Using CSS variable */
            font-size: var(--fs-xs); /* Using CSS variable */
            margin-top: var(--space-md); /* Using CSS variable */
            text-align: center;
        }
        .bx-auth-note a {
            color: var(--edsys-accent); /* Using CSS variable */
            text-decoration: none;
            transition: var(--edsys-transition); /* Using CSS variable */
        }
        .bx-auth-note a:hover {
            color: var(--edsys-accent-hover); /* Using CSS variable */
        }
        .bx-auth-message {
            color: var(--edsys-neon); /* Using CSS variable */
            background-color: rgba(57, 255, 20, 0.1); /* Corrected rgba for --edsys-neon */
            border: 1px solid var(--edsys-neon); /* Using CSS variable */
            padding: 0.75rem;
            border-radius: var(--radius-sm); /* Using CSS variable */
            margin-bottom: var(--space-md); /* Using CSS variable */
            text-align: center;
            font-size: var(--fs-xs); /* Using CSS variable */
            line-height: var(--edsys-lh-snug); /* Using CSS variable */
        }
        .bx-auth-error {
            color: var(--edsys-accent); /* Using CSS variable */
            background-color: rgba(255, 71, 87, 0.1); /* Corrected rgba for --edsys-accent */
            border: 1px solid var(--edsys-accent); /* Using CSS variable */
            padding: 0.75rem;
            border-radius: var(--radius-sm); /* Using CSS variable */
            margin-bottom: var(--space-md); /* Using CSS variable */
            text-align: center;
            font-size: var(--fs-xs); /* Using CSS variable */
            line-height: var(--edsys-lh-snug); /* Using CSS variable */
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
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.forgotpasswd",
                "",
                [
                    "AUTH_URL" => "/auth/",
                    "REGISTER_URL" => "/auth/?register=yes",
                ],
                false
            );
            ?>
        </div>
    </div>
    <?php
} else {
    // Existing login/registration logic
    // Handle confirmation message
    if (isset($_GET['confirm_registration']) && $_GET['confirm_registration'] === 'yes') {
        $arResult['SUCCESS_MESSAGE'] = "Благодарим за регистрацию на нашем сайте. В ближайшее время мы активируем ваш аккаунт.";
    }

    // Handle Login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login']) && check_bitrix_sessid()) {
        $arAuthResult = $USER->Login($_POST['USER_LOGIN'], $_POST['USER_PASSWORD'], "Y");
        if ($arAuthResult === true || (is_array($arAuthResult) && $arAuthResult['TYPE'] == 'ERROR' && strpos($arAuthResult['MESSAGE'], 'успешно авторизован') !== false)) {
            LocalRedirect("/personal/profile/");
        } else {
            $arResult['LOGIN_ERROR_MESSAGE'] = is_array($arAuthResult) ? $arAuthResult['MESSAGE'] : 'Неверный логин или пароль.';
        }
    }

    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Register']) && check_bitrix_sessid()) {
        $user = new CUser;
        
        // Clean phone number from mask characters
        $phoneRaw = preg_replace('/[^0-9+]/', '', $_POST['PERSONAL_PHONE']);
        
        // If phone starts with 8, replace with +7
        if (strpos($phoneRaw, '8') === 0) {
            $phoneRaw = '+7' . substr($phoneRaw, 1);
        }
        
        // If phone doesn't start with +, add +7
        if (strpos($phoneRaw, '+') !== 0) {
            $phoneRaw = '+7' . $phoneRaw;
        }
        
        // Validate phone format (should be +7XXXXXXXXXX)
        if (!preg_match('/^\+7\d{10}$/', $phoneRaw)) {
            $arResult['REGISTER_ERROR_MESSAGE'] = 'Указан некорректный номер телефона. Формат: +7(XXX)XXX-XX-XX';
            $arResult['SHOW_REGISTER'] = true;
        } else {
            // Parse phone number to E.164 format
            try {
                $phoneParser = PhoneNumber\Parser::getInstance();
                $parsedPhone = $phoneParser->parse($phoneRaw);
                
                if ($parsedPhone->isValid()) {
                    $phoneFormatted = $parsedPhone->format(PhoneNumber\Format::E164);
                }
            } catch (Exception $e) {
                // Fallback if parser fails - use cleaned phone
                $phoneFormatted = $phoneRaw;
            }
            
            // Prepare user fields
            $arFields = [
                "NAME" => trim($_POST['USER_NAME']),
                "EMAIL" => trim($_POST['USER_EMAIL']),
                "LOGIN" => trim($_POST['USER_EMAIL']),
                "LID" => SITE_ID,
                "ACTIVE" => "N", // User will be activated after email confirmation
                "GROUP_ID" => [2], // Default user group
                "PASSWORD" => $_POST['USER_PASSWORD'],
                "CONFIRM_PASSWORD" => $_POST['USER_CONFIRM_PASSWORD'],
                "PERSONAL_PHONE" => $phoneFormatted,
            ];
            
            // Add user
            $ID = $user->Add($arFields);
            
            if (intval($ID) > 0) {
                // Send confirmation email
                $user->SendUserInfo($ID, SITE_ID, "Регистрация на сайте", true, "NEW_USER_CONFIRM");
                
                // ВАЖНО: Редирект с параметром success для очистки POST-данных
                $request = Application::getInstance()->getContext()->getRequest();
                $currentUrl = $request->getRequestedPage();
                LocalRedirect($currentUrl . "?registration=success");
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
        /* Base styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        
        .edsys-auth-container {
            background-color: #1E1E1E;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: 0.625rem;
            box-shadow: 0 0.625rem 1.875rem rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 25rem;
            color: #fff;
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
            color: #888;
            font-size: clamp(1rem, 2vw, 1.125rem);
            padding: 0.625rem 1.25rem;
            cursor: pointer;
            transition: color 0.3s;
            border-bottom: 2px solid transparent;
        }
        
        .edsys-auth-toggle button.active {
            color: #E30613;
            border-bottom-color: #E30613;
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
            color: #aaa;
            font-size: 0.875rem;
        }
        
        .edsys-form-group input {
            width: 100%;
            padding: 0.75rem;
            background-color: #333;
            border: 1px solid #555;
            border-radius: 0.3125rem;
            color: #fff;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .edsys-form-group input:focus {
            outline: none;
            border-color: #E30613;
        }
        
        .edsys-form-group input.error {
            border-color: #ff4d4d;
        }
        
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: #E30613;
            border: none;
            border-radius: 0.3125rem;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            font-weight: 500;
        }
        
        .edsys-auth-submit:hover {
            background-color: #ff1a1a;
        }
        
        .edsys-auth-submit:active {
            transform: scale(0.98);
        }
        
        .edsys-auth-footer {
            text-align: center;
            margin-top: 1.25rem;
        }
        
        .edsys-auth-footer a {
            color: #E30613;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }
        
        .edsys-auth-footer a:hover {
            color: #ff1a1a;
        }
        
        .edsys-error-message {
            color: #ff4d4d;
            background-color: rgba(255, 77, 77, 0.1);
            border: 1px solid #ff4d4d;
            padding: 0.75rem;
            border-radius: 0.3125rem;
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .edsys-success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            padding: 0.75rem;
            border-radius: 0.3125rem;
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: 0.875rem;
            line-height: 1.4;
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

            <div class="edsys-auth-toggle">
                <button id="edsys-login-btn" class="active" type="button">Вход</button>
                <button id="edsys-register-btn" type="button">Регистрация</button>
            </div>

            <!-- Login Form -->
            <div id="edsys-login-form" class="edsys-auth-form active">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
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
                        <a href="?forgot_password=yes">Забыли пароль?</a>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div id="edsys-register-form" class="edsys-auth-form">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>" id="registration-form">
                    <?= bitrix_sessid_post() ?>
                    <?php if (!empty($arResult['SUCCESS_MESSAGE'])):
                        ?>
                        <div class="edsys-success-message"><?= $arResult['SUCCESS_MESSAGE'] ?></div>
                    <?php endif; ?>
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
     * Version: 1.2.0
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
            
            // Set initial value on focus
            phoneInput.addEventListener('focus', function() {
                if (this.value === '') {
                    this.value = prefix;
                }
            });
            
            // Clear if only prefix remains on blur
            phoneInput.addEventListener('blur', function() {
                if (this.value === prefix || this.value === '+7') {
                    this.value = '';
                }
            });
            
            // Format input
            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                
                // Always start with 7
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
            
            // Handle backspace
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
                
                // Check password match
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Пароли не совпадают');
                    return false;
                }
                
                // Validate phone format
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
            // Setup form switchers
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
            
            // Check URL parameters and errors
            const urlParams = new URLSearchParams(window.location.search);
            const hasRegisterError = document.getElementById('register-error');
            const hasSuccess = document.querySelector('.edsys-success-message');
            const showRegister = <?= !empty($arResult['SHOW_REGISTER']) ? 'true' : 'false' ?>;
            
            // Show registration form if needed
            if (urlParams.has('register') || urlParams.has('registration') || showRegister || hasRegisterError || hasSuccess) {
                showForm('register');
            } else {
                showForm('login');
            }
            
            // Initialize phone mask and validation
            initPhoneMask();
            initPasswordValidation();
            
            // Clear success parameter from URL after display
            if (urlParams.has('registration') && urlParams.get('registration') === 'success') {
                // Use replaceState to update URL without reload
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl + '?register');
            }
        });
    })();
    </script>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>
    <div class="edsys-auth-wrapper">
        <div class="edsys-auth-container">
            <?php
            $APPLICATION->IncludeComponent(
                "bitrix:system.auth.forgotpasswd",
                "",
                [
                    "AUTH_URL" => "/auth/",
                    "REGISTER_URL" => "/auth/?register=yes",
                ],
                false
            );
            ?>
        </div>
    </div>
    <?php
} else {
    // Existing login/registration logic
    // Handle confirmation message
    if (isset($_GET['confirm_registration']) && $_GET['confirm_registration'] === 'yes') {
        $arResult['SUCCESS_MESSAGE'] = "Благодарим за регистрацию на нашем сайте. В ближайшее время мы активируем ваш аккаунт.";
    }

    // Handle Login
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login']) && check_bitrix_sessid()) {
        $arAuthResult = $USER->Login($_POST['USER_LOGIN'], $_POST['USER_PASSWORD'], "Y");
        if ($arAuthResult === true || (is_array($arAuthResult) && $arAuthResult['TYPE'] == 'ERROR' && strpos($arAuthResult['MESSAGE'], 'успешно авторизован') !== false)) {
            LocalRedirect("/personal/profile/");
        } else {
            $arResult['LOGIN_ERROR_MESSAGE'] = is_array($arAuthResult) ? $arAuthResult['MESSAGE'] : 'Неверный логин или пароль.';
        }
    }

    // Handle Registration
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Register']) && check_bitrix_sessid()) {
        $user = new CUser;
        
        // Clean phone number from mask characters
        $phoneRaw = preg_replace('/[^0-9+]/', '', $_POST['PERSONAL_PHONE']);
        
        // If phone starts with 8, replace with +7
        if (strpos($phoneRaw, '8') === 0) {
            $phoneRaw = '+7' . substr($phoneRaw, 1);
        }
        
        // If phone doesn't start with +, add +7
        if (strpos($phoneRaw, '+') !== 0) {
            $phoneRaw = '+7' . $phoneRaw;
        }
        
        // Validate phone format (should be +7XXXXXXXXXX)
        if (!preg_match('/^\+7\d{10}$/', $phoneRaw)) {
            $arResult['REGISTER_ERROR_MESSAGE'] = 'Указан некорректный номер телефона. Формат: +7(XXX)XXX-XX-XX';
            $arResult['SHOW_REGISTER'] = true;
        } else {
            // Parse phone number to E.164 format
            try {
                $phoneParser = PhoneNumber\Parser::getInstance();
                $parsedPhone = $phoneParser->parse($phoneRaw);
                
                if ($parsedPhone->isValid()) {
                    $phoneFormatted = $parsedPhone->format(PhoneNumber\Format::E164);
                }
            } catch (Exception $e) {
                // Fallback if parser fails - use cleaned phone
                $phoneFormatted = $phoneRaw;
            }
            
            // Prepare user fields
            $arFields = [
                "NAME" => trim($_POST['USER_NAME']),
                "EMAIL" => trim($_POST['USER_EMAIL']),
                "LOGIN" => trim($_POST['USER_EMAIL']),
                "LID" => SITE_ID,
                "ACTIVE" => "N", // User will be activated after email confirmation
                "GROUP_ID" => [2], // Default user group
                "PASSWORD" => $_POST['USER_PASSWORD'],
                "CONFIRM_PASSWORD" => $_POST['USER_CONFIRM_PASSWORD'],
                "PERSONAL_PHONE" => $phoneFormatted,
            ];
            
            // Add user
            $ID = $user->Add($arFields);
            
            if (intval($ID) > 0) {
                // Send confirmation email
                $user->SendUserInfo($ID, SITE_ID, "Регистрация на сайте", true, "NEW_USER_CONFIRM");
                
                // ВАЖНО: Редирект с параметром success для очистки POST-данных
                $request = Application::getInstance()->getContext()->getRequest();
                $currentUrl = $request->getRequestedPage();
                LocalRedirect($currentUrl . "?registration=success");
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
        /* Base styles */
        .edsys-auth-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            padding: clamp(2rem, 5vw, 3.125rem) 1rem;
            min-height: 60vh;
        }
        
        .edsys-auth-container {
            background-color: #1E1E1E;
            padding: clamp(1.5rem, 4vw, 2.5rem);
            border-radius: 0.625rem;
            box-shadow: 0 0.625rem 1.875rem rgba(0, 0, 0, 0.5);
            width: 100%;
            max-width: 25rem;
            color: #fff;
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
            color: #888;
            font-size: clamp(1rem, 2vw, 1.125rem);
            padding: 0.625rem 1.25rem;
            cursor: pointer;
            transition: color 0.3s;
            border-bottom: 2px solid transparent;
        }
        
        .edsys-auth-toggle button.active {
            color: #E30613;
            border-bottom-color: #E30613;
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
            color: #aaa;
            font-size: 0.875rem;
        }
        
        .edsys-form-group input {
            width: 100%;
            padding: 0.75rem;
            background-color: #333;
            border: 1px solid #555;
            border-radius: 0.3125rem;
            color: #fff;
            box-sizing: border-box;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .edsys-form-group input:focus {
            outline: none;
            border-color: #E30613;
        }
        
        .edsys-form-group input.error {
            border-color: #ff4d4d;
        }
        
        .edsys-auth-submit {
            width: 100%;
            padding: 0.875rem;
            background-color: #E30613;
            border: none;
            border-radius: 0.3125rem;
            color: #fff;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.1s;
            font-weight: 500;
        }
        
        .edsys-auth-submit:hover {
            background-color: #ff1a1a;
        }
        
        .edsys-auth-submit:active {
            transform: scale(0.98);
        }
        
        .edsys-auth-footer {
            text-align: center;
            margin-top: 1.25rem;
        }
        
        .edsys-auth-footer a {
            color: #E30613;
            text-decoration: none;
            font-size: 0.875rem;
            transition: color 0.3s;
        }
        
        .edsys-auth-footer a:hover {
            color: #ff1a1a;
        }
        
        .edsys-error-message {
            color: #ff4d4d;
            background-color: rgba(255, 77, 77, 0.1);
            border: 1px solid #ff4d4d;
            padding: 0.75rem;
            border-radius: 0.3125rem;
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: 0.875rem;
            line-height: 1.4;
        }
        
        .edsys-success-message {
            color: #28a745;
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid #28a745;
            padding: 0.75rem;
            border-radius: 0.3125rem;
            margin-bottom: 1.25rem;
            text-align: center;
            font-size: 0.875rem;
            line-height: 1.4;
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

            <div class="edsys-auth-toggle">
                <button id="edsys-login-btn" class="active" type="button">Вход</button>
                <button id="edsys-register-btn" type="button">Регистрация</button>
            </div>

            <!-- Login Form -->
            <div id="edsys-login-form" class="edsys-auth-form active">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                    <?= bitrix_sessid_post() ?>
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
                        <a href="?forgot_password=yes">Забыли пароль?</a>
                    </div>
                </form>
            </div>

            <!-- Registration Form -->
            <div id="edsys-register-form" class="edsys-auth-form">
                <form method="post" action="<?= $APPLICATION->GetCurPage() ?>" id="registration-form">
                    <?= bitrix_sessid_post() ?>
                    <?php if (!empty($arResult['SUCCESS_MESSAGE'])):
                        ?>
                        <div class="edsys-success-message"><?= $arResult['SUCCESS_MESSAGE'] ?></div>
                    <?php endif; ?>
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
     * Version: 1.2.0
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
            
            // Set initial value on focus
            phoneInput.addEventListener('focus', function() {
                if (this.value === '') {
                    this.value = prefix;
                }
            });
            
            // Clear if only prefix remains on blur
            phoneInput.addEventListener('blur', function() {
                if (this.value === prefix || this.value === '+7') {
                    this.value = '';
                }
            });
            
            // Format input
            phoneInput.addEventListener('input', function(e) {
                let value = this.value.replace(/\D/g, '');
                
                // Always start with 7
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
            
            // Handle backspace
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
                
                // Check password match
                if (password !== confirmPassword) {
                    e.preventDefault();
                    alert('Пароли не совпадают');
                    return false;
                }
                
                // Validate phone format
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
            // Setup form switchers
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
            
            // Check URL parameters and errors
            const urlParams = new URLSearchParams(window.location.search);
            const hasRegisterError = document.getElementById('register-error');
            const hasSuccess = document.querySelector('.edsys-success-message');
            const showRegister = <?= !empty($arResult['SHOW_REGISTER']) ? 'true' : 'false' ?>;
            
            // Show registration form if needed
            if (urlParams.has('register') || urlParams.has('registration') || showRegister || hasRegisterError || hasSuccess) {
                showForm('register');
            } else {
                showForm('login');
            }
            
            // Initialize phone mask and validation
            initPhoneMask();
            initPasswordValidation();
            
            // Clear success parameter from URL after display
            if (urlParams.has('registration') && urlParams.get('registration') === 'success') {
                // Use replaceState to update URL without reload
                const cleanUrl = window.location.pathname;
                window.history.replaceState({}, document.title, cleanUrl + '?register');
            }
        });
    })();
    </script>
    <?php
}
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>