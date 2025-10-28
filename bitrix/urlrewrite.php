<?
include_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/urlrewrite.php');
if(file_exists($_SERVER['DOCUMENT_ROOT'].'/404.php'))
	include_once($_SERVER['DOCUMENT_ROOT'].'/404.php');
?>

<?php
$arUrlRewrite = array(
	// WordPress-совместимые URL для каталога
	array(
		"CONDITION" => "#^/cat/([^/]+)/?#",
		"RULE" => "category=\$1",
		"ID" => "",
		"PATH" => "/cat/index.php",
		"SORT" => 100,
	),
	array(
		"CONDITION" => "#^/cat/?#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/cat/index.php",
		"SORT" => 100,
	),
	// WordPress-совместимые URL для товаров
	array(
		"CONDITION" => "#^/product/([^/]+)/?#",
		"RULE" => "product=\$1",
		"ID" => "",
		"PATH" => "/product/index.php",
		"SORT" => 100,
	),
	// Стандартные правила Битрикса для остальных страниц
	array(
		"CONDITION" => "#^/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/index.php",
		"SORT" => 100,
	),
);
?>
