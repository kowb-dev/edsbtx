<?php
/**
 * Шаблон профиля пользователя
 * Файл: /local/templates/edsy_main/components/bitrix/main.profile/edsy_profile/template.php
 * @author: KW, https://kowb.ru
 * @version: 2.2
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

/** @var array $arParams */
/** @var array $arResult */
/** @global CMain $APPLICATION */
/** @global CUser $USER */

CJSCore::Init(array("jquery"));
?>

<style>
    .edsy-profile-form .edsy-form-group {
        position: relative;
        margin-top: 20px;
    }

    .edsy-profile-form .edsy-form-input,
    .edsy-profile-form .edsy-form-textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        background-color: #f9f9f9;
        color: #333;
        transition: all 0.3s ease;
    }

    .edsy-profile-form .edsy-form-label {
        position: absolute;
        left: 10px;
        top: 12px;
        color: #999;
        pointer-events: none;
        transition: all 0.2s ease;
        background-color: transparent;
    }

    .edsy-profile-form .edsy-form-input:focus,
    .edsy-profile-form .edsy-form-textarea:focus {
        outline: none;
        border-color: #E30613;
        background-color: #fff;
    }

    .edsy-profile-form .edsy-form-input:focus + .edsy-form-label,
    .edsy-profile-form .edsy-form-input:not(:placeholder-shown) + .edsy-form-label,
    .edsy-profile-form .edsy-form-textarea:focus + .edsy-form-label,
    .edsy-profile-form .edsy-form-textarea:not(:placeholder-shown) + .edsy-form-label {
        top: -10px;
        left: 5px;
        font-size: 12px;
        color: #E30613;
        padding: 0 5px;
        background-color: #fff;
    }
    .edsy-profile-grid .edsy-form-group--full {
        grid-column: 1 / -1;
    }

</style>

<div class="edsy-profile-wrapper">
    <?php if ($arResult["SHOW_RESULT_ERROR"] == "Y"): ?>
        <div class="alert alert-danger edsy-alert">
            <ul>
                <?php foreach ($arResult["SHOW_RESULT_ERROR_ARRAY"] as $error): ?>
                    <li><?= $error ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($arResult["SHOW_RESULT_SUCCESS"] == "Y"): ?>
        <div class="alert alert-success edsy-alert">
            <?= GetMessage("PROFILE_DATA_SAVED") ?>
        </div>
    <?php endif; ?>

    <form method="post" name="form1" action="<?= POST_FORM_ACTION_URI ?>" enctype="multipart/form-data" class="edsy-profile-form">
        <?= bitrix_sessid_post(); ?>
        <input type="hidden" name="lang" value="<?= LANG ?>" />
        <input type="hidden" name="ID" value="<?= $arResult["ID"] ?>" />

        <!-- Личные данные -->
        <div class="edsy-profile-section">
            <h2 class="edsy-profile-section__title edsy-accordion-toggle">
                Личные данные
                <svg class="edsy-accordion-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                    <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </h2>

            <div class="edsy-profile-grid edsy-accordion-content">
                <!-- Имя -->
                <div class="edsy-form-group">
                    <input type="text" id="NAME" name="NAME" class="edsy-form-input" value="<?=$arResult["arUser"]["NAME"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="NAME"><?=GetMessage('NAME')?>Имя</label>
                </div>
                <!-- Фамилия -->
                <div class="edsy-form-group">
                    <input type="text" id="LAST_NAME" name="LAST_NAME" class="edsy-form-input" value="<?=$arResult["arUser"]["LAST_NAME"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="LAST_NAME"><?=GetMessage('LAST_NAME')?>Фамилия</label>
                </div>
                <!-- Отчество -->
                <div class="edsy-form-group">
                    <input type="text" id="SECOND_NAME" name="SECOND_NAME" class="edsy-form-input" value="<?=$arResult["arUser"]["SECOND_NAME"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="SECOND_NAME"><?=GetMessage('SECOND_NAME')?>Отчество</label>
                </div>
                <!-- Email -->
                <div class="edsy-form-group">
                    <input type="email" id="EMAIL" name="EMAIL" class="edsy-form-input" value="<?=$arResult["arUser"]["EMAIL"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="EMAIL"><?=GetMessage('EMAIL')?>Email</label>
                </div>
                <!-- Логин -->
                <div class="edsy-form-group">
                    <input type="text" id="LOGIN" name="LOGIN" class="edsy-form-input" value="<?=$arResult["arUser"]["LOGIN"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="LOGIN"><?=GetMessage('LOGIN')?>Логин</label>
                </div>
                <!-- Телефон -->
                <div class="edsy-form-group">
                    <input type="tel" id="PERSONAL_PHONE" name="PERSONAL_PHONE" class="edsy-form-input" value="<?=$arResult["arUser"]["PERSONAL_PHONE"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="PERSONAL_PHONE"><?=GetMessage('USER_PHONE')?>Телефон</label>
                </div>
                <!-- Мобильный телефон -->
                <div class="edsy-form-group">
                    <input type="tel" id="PERSONAL_MOBILE" name="PERSONAL_MOBILE" class="edsy-form-input" value="<?=$arResult["arUser"]["PERSONAL_MOBILE"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="PERSONAL_MOBILE"><?=GetMessage('USER_MOBILE')?>Мобильный телефон</label>
                </div>
                <!-- WWW-страница -->
                <div class="edsy-form-group">
                    <input type="url" id="PERSONAL_WWW" name="PERSONAL_WWW" class="edsy-form-input" value="<?=$arResult["arUser"]["PERSONAL_WWW"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="PERSONAL_WWW"><?=GetMessage('USER_WWW')?>Личный сайт</label>
                </div>
                <!-- Страна -->
                <div class="edsy-form-group">
                    <select id="PERSONAL_COUNTRY" name="PERSONAL_COUNTRY" class="edsy-form-input">
                        <option value="Россия"<?=($arResult["arUser"]["PERSONAL_COUNTRY"] == "Россия" || !$arResult["arUser"]["PERSONAL_COUNTRY"]) ? " selected" : ""?>>Россия</option>
                        <option value="Беларусь"<?=$arResult["arUser"]["PERSONAL_COUNTRY"] == "Беларусь" ? " selected" : ""?>>Беларусь</option>
                        <option value="Казахстан"<?=$arResult["arUser"]["PERSONAL_COUNTRY"] == "Казахстан" ? " selected" : ""?>>Казахстан</option>
                    </select>
                    <label class="edsy-form-label" for="PERSONAL_COUNTRY"><?=GetMessage('USER_COUNTRY')?>Страна</label>
                </div>
                <!-- Город -->
                <div class="edsy-form-group">
                    <input type="text" id="PERSONAL_CITY" name="PERSONAL_CITY" class="edsy-form-input" value="<?=$arResult["arUser"]["PERSONAL_CITY"]?>" placeholder=" " />
                    <label class="edsy-form-label" for="PERSONAL_CITY"><?=GetMessage('USER_CITY')?>Город</label>
                </div>
                <!-- Адрес -->
                <div class="edsy-form-group edsy-form-group--full">
                    <textarea id="PERSONAL_STREET" name="PERSONAL_STREET" class="edsy-form-input" placeholder=" "><?=$arResult["arUser"]["PERSONAL_STREET"]?></textarea>
                    <label class="edsy-form-label" for="PERSONAL_STREET"><?=GetMessage('USER_STREET')?>Адрес</label>
                </div>
            </div>
        </div>

       <!-- Информация о работе -->
<div class="edsy-profile-section">
    <h2 class="edsy-profile-section__title edsy-accordion-toggle">
        Информация о компании
        <svg class="edsy-accordion-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
            <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
        </svg>
    </h2>

    <div class="edsy-profile-grid edsy-accordion-content">
        <!-- Компания -->
        <div class="edsy-form-group">
            <input type="text" id="WORK_COMPANY" name="WORK_COMPANY" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_COMPANY"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_COMPANY"><?= GetMessage("WORK_COMPANY") ?>Компания</label>
        </div>

        <!-- Должность -->
        <div class="edsy-form-group">
            <input type="text" id="WORK_POSITION" name="WORK_POSITION" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_POSITION"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_POSITION"><?= GetMessage("WORK_POSITION") ?>Должность</label>
        </div>

        <!-- Отдел / Департамент -->
        <div class="edsy-form-group">
            <input type="text" id="WORK_DEPARTMENT" name="WORK_DEPARTMENT" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_DEPARTMENT"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_DEPARTMENT"><?= GetMessage("WORK_DEPARTMENT") ?>Отдел / Департамент</label>
        </div>

        <!-- WWW-страница -->
        <div class="edsy-form-group">
            <input type="url" id="WORK_WWW" name="WORK_WWW" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_WWW"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_WWW"><?= GetMessage("WORK_WWW") ?>Сайт компании</label>
        </div>

        <!-- Телефон -->
        <div class="edsy-form-group">
            <input type="tel" id="WORK_PHONE" name="WORK_PHONE" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_PHONE"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_PHONE"><?= GetMessage("WORK_PHONE") ?>Телефон</label>
        </div>

        <!-- Страна -->
        <div class="edsy-form-group">
            <select id="WORK_COUNTRY" name="WORK_COUNTRY" class="edsy-form-input">
                <option value="Россия"<?if($arResult["arUser"]["WORK_COUNTRY"] == "Россия" || empty($arResult["arUser"]["WORK_COUNTRY"])) echo " selected";?>>Россия</option>
                <option value="Беларусь"<?if($arResult["arUser"]["WORK_COUNTRY"] == "Беларусь") echo " selected";?>>Беларусь</option>
                <option value="Казахстан"<?if($arResult["arUser"]["WORK_COUNTRY"] == "Казахстан") echo " selected";?>>Казахстан</option>
            </select>
            <label class="edsy-form-label" for="WORK_COUNTRY"><?= GetMessage("WORK_COUNTRY") ?>Страна</label>
        </div>

        <!-- Город -->
        <div class="edsy-form-group">
            <input type="text" id="WORK_CITY" name="WORK_CITY" value="<?= htmlspecialcharsEx($arResult["arUser"]["WORK_CITY"]) ?>" maxlength="255" class="edsy-form-input" placeholder=" " />
            <label class="edsy-form-label" for="WORK_CITY"><?= GetMessage("WORK_CITY") ?>Город</label>
        </div>

        <!-- Адрес -->
        <div class="edsy-form-group">
            <textarea id="WORK_STREET" name="WORK_STREET" rows="3" class="edsy-form-textarea" placeholder=" "><?= htmlspecialcharsEx($arResult["arUser"]["WORK_STREET"]) ?></textarea>
            <label class="edsy-form-label" for="WORK_STREET"><?= GetMessage("WORK_STREET") ?>Адрес</label>
        </div>
    </div>
</div>

        <!-- Смена пароля -->
        <?php if ($arResult["CAN_EDIT_PASSWORD"]): ?>
            <div class="edsy-profile-section">
                <h2 class="edsy-profile-section__title edsy-accordion-toggle">
                    Смена пароля
                    <svg class="edsy-accordion-icon" width="20" height="20" viewBox="0 0 20 20" fill="none">
                        <path d="M5 7.5L10 12.5L15 7.5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </h2>

                <div class="edsy-profile-grid edsy-accordion-content">
                    <!-- Новый пароль -->
                    <div class="edsy-form-group">
                        <input type="password" id="NEW_PASSWORD" name="NEW_PASSWORD" value="" maxlength="50" class="edsy-form-input" autocomplete="new-password" placeholder=" " />
                        <label class="edsy-form-label" for="NEW_PASSWORD"><?= GetMessage("NEW_PASSWORD_REQ") ?></label>
                    </div>

                    <!-- Подтверждение пароля -->
                    <div class="edsy-form-group">
                        <input type="password" id="NEW_PASSWORD_CONFIRM" name="NEW_PASSWORD_CONFIRM" value="" maxlength="50" class="edsy-form-input" autocomplete="new-password" placeholder=" " />
                        <label class="edsy-form-label" for="NEW_PASSWORD_CONFIRM"><?= GetMessage("NEW_PASSWORD_CONFIRM") ?></label>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Кнопки -->
        <div class="edsy-profile-actions">
            <button type="submit" name="save" value="Y" class="edsy-btn edsy-btn--primary">
                <?= GetMessage("MAIN_SAVE") ?>
            </button>
            <button type="reset" class="edsy-btn edsy-btn--secondary">
                <?= GetMessage("MAIN_RESET") ?>
            </button>
        </div>
    </form>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
    const accordionToggles = document.querySelectorAll('.edsy-accordion-toggle');

    accordionToggles.forEach(function(toggle) {
        toggle.addEventListener('click', function() {
            const section = this.closest('.edsy-profile-section');
            const content = section.querySelector('.edsy-accordion-content');

            section.classList.toggle('active');

            if (section.classList.contains('active')) {
                // Устанавливаем maxHeight только если есть содержимое
                content.style.maxHeight = content.scrollHeight ? content.scrollHeight + 'px' : 'auto';
            } else {
                content.style.maxHeight = '0';
            }
        });
    });

    // Открываем первые два раздела по умолчанию
    accordionToggles.forEach(function(toggle, index) {
        if (index === 0 || index === 1) {
            const section = toggle.closest('.edsy-profile-section');
            const content = section.querySelector('.edsy-accordion-content');
            if (content) {
                section.classList.add('active');
                // Проверяем, есть ли scrollHeight, иначе используем auto
                content.style.maxHeight = content.scrollHeight ? content.scrollHeight + 'px' : 'auto';
                // Для отладки: выводим scrollHeight
                console.log(`Section ${index} scrollHeight: ${content.scrollHeight}`);
            }
        }
    });

    // Floating labels
    const formGroups = document.querySelectorAll('.edsy-form-group');
    formGroups.forEach(function(group) {
        const input = group.querySelector('.edsy-form-input, .edsy-form-textarea');
        if (input && input.value) {
            input.classList.add('has-value');
        }
    });
});
</script>