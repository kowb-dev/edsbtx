<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$arTemplateParameters = array(
    "SHOW_QUICK_VIEW" => array(
        "PARENT" => "VISUAL",
        "NAME" => "Показывать быстрый просмотр",
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "SHOW_ARTICLE" => array(
        "PARENT" => "VISUAL", 
        "NAME" => "Показывать артикул",
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "SHOW_RATING" => array(
        "PARENT" => "VISUAL",
        "NAME" => "Показывать рейтинг",
        "TYPE" => "CHECKBOX", 
        "DEFAULT" => "N",
    ),
    "AJAX_LOAD_MORE" => array(
        "PARENT" => "AJAX_SETTINGS",
        "NAME" => "Включить дозагрузку товаров",
        "TYPE" => "CHECKBOX",
        "DEFAULT" => "Y",
    ),
    "LOAD_MORE_COUNT" => array(
        "PARENT" => "AJAX_SETTINGS",
        "NAME" => "Количество товаров для дозагрузки",
        "TYPE" => "STRING",
        "DEFAULT" => "8",
    ),
);
?>
