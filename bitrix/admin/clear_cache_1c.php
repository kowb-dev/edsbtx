<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!$USER->IsAdmin()) {
	echo "Нет прав доступа";
	exit;
}

echo "<h2>Очистка кэша после импорта 1С</h2>";

// Очищаем кэш
$GLOBALS["CACHE_MANAGER"]->CleanAll();
BXClearCache(true);

// Очищаем управляемый кэш
if (method_exists($GLOBALS["CACHE_MANAGER"], "CleanDir")) {
	$GLOBALS["CACHE_MANAGER"]->CleanDir("iblock");
	$GLOBALS["CACHE_MANAGER"]->CleanDir("menu");
	$GLOBALS["CACHE_MANAGER"]->CleanDir("catalog");
}

// Перестраиваем автокомплит
if (CModule::IncludeModule("search")) {
	CSearch::ReIndexAll(true);
}

echo "✓ Кэш очищен<br>";
echo "✓ Автокомплит перестроен<br>";

echo "<br><strong>Рекомендации:</strong><br>";
echo "1. Выйдите из админки и войдите заново<br>";
echo "2. <a href='/bitrix/admin/iblock_admin.php'>Перейти к списку ИБ</a><br>";
echo "3. <a href='/bitrix/admin/user_admin.php'>Проверить права пользователей</a><br>";

// Проверяем права текущего пользователя
echo "<br><h3>Информация о текущем пользователе:</h3>";
echo "ID: " . $USER->GetID() . "<br>";
echo "Логин: " . $USER->GetLogin() . "<br>";
echo "Админ: " . ($USER->IsAdmin() ? "Да" : "Нет") . "<br>";

// Проверяем группы пользователя
$arGroups = CUser::GetUserGroup($USER->GetID());
echo "Группы: " . implode(", ", $arGroups) . "<br>";
?>