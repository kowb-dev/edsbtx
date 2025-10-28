<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Подтверждение регистрации");

$APPLICATION->IncludeComponent(
    "bitrix:system.auth.confirmation",
    "",
    Array(
        "CONFIRM_CODE" => "confirm_code",
        "LOGIN" => "login",
        "USER_ID" => "user_id"
    )
);

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>