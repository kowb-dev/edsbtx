<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Вход и регистрация");

global $USER;
$arResult = [];

// Handle Login
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Login'])) {
    $arAuthResult = $USER->Login($_POST['USER_LOGIN'], $_POST['USER_PASSWORD'], "Y");
    if ($arAuthResult === true || (is_array($arAuthResult) && $arAuthResult['TYPE'] == 'ERROR' && strpos($arAuthResult['MESSAGE'], 'успешно авторизован') !== false)) {
        LocalRedirect("/personal/profile/");
    } else {
        $arResult['ERROR_MESSAGE'] = is_array($arAuthResult) ? $arAuthResult['MESSAGE'] : 'Неверный логин или пароль.';
    }
}

// Handle Registration
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['Register'])) {
    $user = new CUser;

    // Нормализуем номер телефона к формату E.164 (+79998887766)
    $phone = \Bitrix\Main\PhoneNumber\Parser::getInstance()->parse($_POST['PERSONAL_PHONE'])->format(\Bitrix\Main\PhoneNumber\Format::E164);

    $arFields = [
        "NAME" => $_POST['USER_NAME'],
        "EMAIL" => $_POST['USER_EMAIL'],
        "LOGIN" => $_POST['USER_EMAIL'],
        "LID" => SITE_ID,
        "ACTIVE" => "N",
        "GROUP_ID" => [2],
        "PASSWORD" => $_POST['USER_PASSWORD'],
        "CONFIRM_PASSWORD" => $_POST['USER_CONFIRM_PASSWORD'],
        "PERSONAL_PHONE" => $phone, // Используем нормализованный номер
    ];
    // В метод Register последним параметром передается телефон
    $arAuthResult = $user->Register($arFields["LOGIN"], $arFields["NAME"], "", $arFields["PASSWORD"], $arFields["CONFIRM_PASSWORD"], $arFields["EMAIL"], SITE_ID, "", 0, false, "Y", $phone);

    if ($arAuthResult["TYPE"] == "OK") {
        $arResult['SUCCESS_MESSAGE'] = "Вы успешно зарегистрированы. Пожалуйста, проверьте свою почту для подтверждения регистрации.";
    } else {
        $arResult['ERROR_MESSAGE'] = $arAuthResult["MESSAGE"];
    }
}
?>

<style>
    .auth-wrapper {
        display: flex;
        justify-content: center;
        align-items: center;
        padding: 50px 0;
    }
    .auth-container {
        background-color: #1E1E1E;
        padding: 40px;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
        width: 400px;
        color: #fff;
    }
    .auth-toggle {
        display: flex;
        justify-content: center;
        margin-bottom: 30px;
    }
    .auth-toggle button {
        background: none; border: none; color: #888; font-size: 18px;
        padding: 10px 20px; cursor: pointer; transition: color 0.3s;
    }
    .auth-toggle button.active { color: #E30613; border-bottom: 2px solid #E30613; }
    .auth-form { display: none; }
    .auth-form.active { display: block; }
    .form-group { margin-bottom: 20px; }
    .form-group label { display: block; margin-bottom: 5px; color: #aaa; }
    .form-group input {
        width: 100%; padding: 10px; background-color: #333;
        border: 1px solid #555; border-radius: 5px; color: #fff; box-sizing: border-box;
    }
    .auth-submit {
        width: 100%; padding: 12px; background-color: #E30613; border: none;
        border-radius: 5px; color: #fff; font-size: 16px; cursor: pointer; transition: background-color 0.3s;
    }
    .auth-submit:hover { background-color: #ff4d4d; }
    .auth-footer {
        text-align: center; margin-top: 20px;
    }
    .auth-footer a { color: #E30613; text-decoration: none; }
    .error-message { color: #ff4d4d; background-color: rgba(255, 77, 77, 0.1); border: 1px solid #ff4d4d; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
    .success-message { color: #28a745; background-color: rgba(40, 167, 69, 0.1); border: 1px solid #28a745; padding: 10px; border-radius: 5px; margin-bottom: 20px; text-align: center; }
</style>

<div class="auth-wrapper">
    <div class="auth-container">

        <?php if (!empty($arResult['ERROR_MESSAGE'])): ?>
            <div class="error-message"><?= $arResult['ERROR_MESSAGE'] ?></div>
        <?php endif; ?>
        <?php if (!empty($arResult['SUCCESS_MESSAGE'])): ?>
            <div class="success-message"><?= $arResult['SUCCESS_MESSAGE'] ?></div>
        <?php endif; ?>

        <div class="auth-toggle">
            <button id="login-btn" class="active">Вход</button>
            <button id="register-btn">Регистрация</button>
        </div>

        <div id="login-form" class="auth-form active">
            <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                <div class="form-group">
                    <label for="login-email">Email или телефон</label>
                    <input type="text" id="login-email" name="USER_LOGIN" required>
                </div>
                <div class="form-group">
                    <label for="login-password">Пароль</label>
                    <input type="password" id="login-password" name="USER_PASSWORD" required>
                </div>
                <button type="submit" name="Login" class="auth-submit">Войти</button>
                <div class="auth-footer">
                    <a href="?forgot_password=yes">Забыли пароль?</a>
                </div>
            </form>
        </div>

        <div id="register-form" class="auth-form">
            <form method="post" action="<?= $APPLICATION->GetCurPage() ?>">
                <div class="form-group">
                    <label for="register-name">Имя</label>
                    <input type="text" id="register-name" name="USER_NAME" required>
                </div>
                <div class="form-group">
                    <label for="register-email">Email</label>
                    <input type="email" id="register-email" name="USER_EMAIL" required>
                </div>
                <div class="form-group">
                    <label for="register-phone">Номер телефона</label>
                    <input type="text" id="register-phone" name="PERSONAL_PHONE" required>
                </div>
                <div class="form-group">
                    <label for="register-password">Пароль</label>
                    <input type="password" id="register-password" name="USER_PASSWORD" required>
                </div>
                <div class="form-group">
                    <label for="register-confirm-password">Подтвердите пароль</label>
                    <input type="password" id="register-confirm-password" name="USER_CONFIRM_PASSWORD" required>
                </div>
                <button type="submit" name="Register" class="auth-submit">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</div>

<script>
    function showForm(formName) {
        document.getElementById('login-form').classList.remove('active');
        document.getElementById('register-form').classList.remove('active');
        document.getElementById('login-btn').classList.remove('active');
        document.getElementById('register-btn').classList.remove('active');

        document.getElementById(formName + '-form').classList.add('active');
        document.getElementById(formName + '-btn').classList.add('active');
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.getElementById('login-btn').addEventListener('click', function() {
            showForm('login');
        });
        document.getElementById('register-btn').addEventListener('click', function() {
            showForm('register');
        });

        const urlParams = new URLSearchParams(window.location.search);
        const hasError = document.querySelector('.error-message');
        const hasSuccess = document.querySelector('.success-message');

        if (urlParams.has('register') || (hasError && '<?=isset($_POST['Register'])?>') || hasSuccess) {
            showForm('register');
        } else {
            showForm('login');
        }

        // Phone mask
        const phoneInput = document.getElementById('register-phone');
        const prefix = '+7(';

        phoneInput.addEventListener('focus', () => {
            if (phoneInput.value === '') {
                phoneInput.value = prefix;
            }
        });

        phoneInput.addEventListener('blur', () => {
            if (phoneInput.value === prefix) {
                phoneInput.value = '';
            }
        });

        phoneInput.addEventListener('input', () => {
            const value = phoneInput.value.replace(/\D/g, '');
            let formattedValue = prefix;

            if (value.length > 1) {
                formattedValue += value.substring(1, 4);
            }
            if (value.length > 4) {
                formattedValue += ') ' + value.substring(4, 7);
            }
            if (value.length > 7) {
                formattedValue += '-' + value.substring(7, 9);
            }
            if (value.length > 9) {
                formattedValue += '-' + value.substring(9, 11);
            }

            phoneInput.value = formattedValue;
        });

        phoneInput.addEventListener('keydown', (e) => {
            if (e.key === 'Backspace' && phoneInput.value.length <= prefix.length) {
                e.preventDefault();
                phoneInput.value = '';
            }
        });
    });
</script>
<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>