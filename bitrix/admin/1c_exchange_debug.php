<?php
require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

// Логирование всех входящих запросов
$logFile = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange_debug.log";

$requestData = [
	'timestamp' => date('Y-m-d H:i:s'),
	'method' => $_SERVER['REQUEST_METHOD'],
	'uri' => $_SERVER['REQUEST_URI'],
	'headers' => getallheaders(),
	'get' => $_GET,
	'post' => $_POST,
	'raw_input' => file_get_contents('php://input'),
	'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
];

file_put_contents($logFile, json_encode($requestData, JSON_UNESCAPED_UNICODE) . "\n", FILE_APPEND);

// Проверяем доступность модуля
if (!CModule::IncludeModule("catalog")) {
	echo "failure\nМодуль catalog не подключен";
	exit;
}

if (!CModule::IncludeModule("iblock")) {
	echo "failure\nМодуль iblock не подключен";
	exit;
}

// Проверяем права доступа
global $USER;
if (!$USER->IsAdmin()) {
	echo "failure\nНедостаточно прав доступа";
	exit;
}

echo "success\nСистема готова к обмену";
?>