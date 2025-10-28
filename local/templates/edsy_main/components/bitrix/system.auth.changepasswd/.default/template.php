<?php
/**
 * Bitrix System Auth Change Password Component Template
 * Template: .default (edsy_main)
 * Version: 1.6.3
 * Author: KW https://kowb.ru
 * Changes:
 * - Removed checkword field (not needed for authorized users)
 * - Proper success handling with redirect
 * - Clean error message display
 * - Simplified form structure for authorized users
 * - Added password visibility toggle for all password fields
 * - Smart success detection via POST validation
 * - Fixed positioning in page layout
 * - Fixed checkword preservation in POST requests
 * - Production ready
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) { die(); }

/** Helper functions */
$esc = fn($s) => htmlspecialchars((string)$s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$renderMsg = function($msg) {
    if (!is_string($msg)) return '';
    $msg = str_replace(['<br>', '<br />', '&lt;br&gt;', '&lt;br /&gt;'], "\n", $msg);
    return nl2br(htmlspecialchars($msg, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8'));
};

// Success is handled in result_modifier.php
?>
<div class="edsys-changepwd">
    <?php
    // If success was flagged, show message (shouldn't normally reach here due to redirect)
    if (!empty($arResult['IS_SUCCESS'])) {
        echo '<div class="edsys-success-message">Пароль успешно изменен</div>';
        echo '<div class="edsys-auth-footer"><a href="/personal/profile/">Вернуться в профиль</a></div>';
        return;
    }
    
    // Display errors if any
    if (!empty($arResult['ERROR_MESSAGE'])) {
        $errors = is_array($arResult['ERROR_MESSAGE']) ? $arResult['ERROR_MESSAGE'] : [$arResult['ERROR_MESSAGE']];
        foreach ($errors as $v) {
            echo '<div class="edsys-error-message">' . $renderMsg($v) . '</div>';
        }
    }
    ?>

    <form method="post" action="<?= $esc(POST_FORM_ACTION_URI) ?>" name="bform" class="edsys-changepwd-form">
        <?php if (!empty($arResult['BACKURL'])): ?>
            <input type="hidden" name="backurl" value="<?= $esc($arResult['BACKURL']) ?>">
        <?php endif; ?>
        <input type="hidden" name="AUTH_FORM" value="Y">
        <input type="hidden" name="TYPE" value="CHANGE_PWD">
        
        <?php if (!empty($arResult['USER_CHECKWORD'])): ?>
            <input type="hidden" name="USER_CHECKWORD" value="<?= $esc($arResult['USER_CHECKWORD']) ?>">
        <?php endif; ?>

        <div class="edsys-form-group">
            <label for="edsys-change-password-login"><span class="starrequired">*</span>Логин</label>
            <input type="text" id="edsys-change-password-login" name="USER_LOGIN"
                   value="<?= $esc($arResult['LAST_LOGIN'] ?? '') ?>" maxlength="50" autocomplete="username" required>
        </div>

        <?php if (!empty($arResult['USE_PASSWORD'])): ?>
            <div class="edsys-form-group">
                <label for="edsys-change-password-current"><span class="starrequired">*</span>Текущий пароль</label>
                <div class="edsys-password-wrapper">
                    <input type="password" id="edsys-change-password-current" name="USER_CURRENT_PASSWORD"
                           maxlength="255" autocomplete="current-password" required>
                    <button type="button" class="edsys-password-toggle" data-target="edsys-change-password-current" aria-label="Показать пароль">
                        <i class="ph-thin ph-eye"></i>
                    </button>
                </div>
            </div>
        <?php endif; ?>

        <div class="edsys-form-group">
            <label for="edsys-change-password-new"><span class="starrequired">*</span>Новый пароль</label>
            <div class="edsys-password-wrapper">
                <input type="password" id="edsys-change-password-new" name="USER_PASSWORD"
                       maxlength="255" autocomplete="new-password" required>
                <button type="button" class="edsys-password-toggle" data-target="edsys-change-password-new" aria-label="Показать пароль">
                    <i class="ph-thin ph-eye"></i>
                </button>
            </div>
        </div>

        <div class="edsys-form-group">
            <label for="edsys-change-password-confirm"><span class="starrequired">*</span>Подтверждение нового пароля</label>
            <div class="edsys-password-wrapper">
                <input type="password" id="edsys-change-password-confirm" name="USER_CONFIRM_PASSWORD"
                       maxlength="255" autocomplete="new-password" required>
                <button type="button" class="edsys-password-toggle" data-target="edsys-change-password-confirm" aria-label="Показать пароль">
                    <i class="ph-thin ph-eye"></i>
                </button>
            </div>
        </div>

        <?php if (!empty($arResult['USE_CAPTCHA'])): ?>
            <div class="edsys-form-group">
                <label><span class="starrequired">*</span>Введите символы с картинки</label>
                <input type="hidden" name="captcha_sid" value="<?= $esc($arResult['CAPTCHA_CODE'] ?? '') ?>">
                <img src="/bitrix/tools/captcha.php?captcha_sid=<?= $esc($arResult['CAPTCHA_CODE'] ?? '') ?>" width="180" height="40" alt="CAPTCHA">
                <input type="text" name="captcha_word" maxlength="50" value="" autocomplete="off" required>
            </div>
        <?php endif; ?>

        <button type="submit" name="change_pwd" class="edsys-auth-submit">Изменить пароль</button>
    </form>

    <?php if (!empty($arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS'])): ?>
        <p class="edsys-note"><?= $renderMsg($arResult['GROUP_POLICY']['PASSWORD_REQUIREMENTS']) ?></p>
    <?php endif; ?>
    <p class="edsys-note"><span class="starrequired">*</span>Обязательные поля</p>

    <!-- <div class="edsys-auth-footer">
        <a href="<?= $esc($arResult['AUTH_AUTH_URL'] ?? '/auth/') ?>">Авторизация</a>
    </div> -->
</div>

<script>
(function() {
    'use strict';
    
    // Проверяем, что скрипт выполняется только один раз
    if (window.edsysPasswordToggleInitialized) {
        return;
    }
    window.edsysPasswordToggleInitialized = true;
    
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButtons = document.querySelectorAll('.edsys-password-toggle');
        
        toggleButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                const input = document.getElementById(targetId);
                const icon = this.querySelector('i');
                
                if (!input || !icon) return;
                
                if (input.type === 'password') {
                    input.type = 'text';
                    icon.classList.remove('ph-eye');
                    icon.classList.add('ph-eye-slash');
                    this.setAttribute('aria-label', 'Скрыть пароль');
                } else {
                    input.type = 'password';
                    icon.classList.remove('ph-eye-slash');
                    icon.classList.add('ph-eye');
                    this.setAttribute('aria-label', 'Показать пароль');
                }
            });
        });
    });
})();
</script>