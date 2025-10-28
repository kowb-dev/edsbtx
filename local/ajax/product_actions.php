<?php
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Application;
use Bitrix\Main\Web\Json;

// Проверяем, что запрос AJAX
if (!isset($_SERVER["HTTP_X_REQUESTED_WITH"]) || $_SERVER["HTTP_X_REQUESTED_WITH"] !== "XMLHttpRequest") {
    http_response_code(400);
    die("Bad Request");
}

// Проверяем авторизацию
global $USER;
if (!$USER->IsAuthorized()) {
    http_response_code(401);
    echo Json::encode(array("success" => false, "error" => "Необходима авторизация"));
    die();
}

// Получаем данные из POST запроса
$request = Application::getInstance()->getContext()->getRequest();
$postData = Json::decode($request->getInput());

if (empty($postData["action"]) || empty($postData["productId"])) {
    http_response_code(400);
    echo Json::encode(array("success" => false, "error" => "Некорректные данные"));
    die();
}

$action = $postData["action"];
$productId = (int)$postData["productId"];
$userId = $USER->GetID();

try {
    switch ($action) {
        case "add_favorite":
        case "remove_favorite":
            // Работа с избранным (можно использовать свою таблицу или пользовательские свойства)
            $isAdd = ($action === "add_favorite");
            
            // Пример с использованием пользовательских полей
            $userFavorites = $USER->GetParam("FAVORITE_PRODUCTS");
            if (!is_array($userFavorites)) {
                $userFavorites = array();
            }
            
            if ($isAdd) {
                if (!in_array($productId, $userFavorites)) {
                    $userFavorites[] = $productId;
                }
                $message = "Товар добавлен в избранное";
            } else {
                $userFavorites = array_diff($userFavorites, array($productId));
                $message = "Товар удален из избранного";
            }
            
            $USER->SetParam("FAVORITE_PRODUCTS", $userFavorites);
            echo Json::encode(array("success" => true, "message" => $message));
            break;

        case "add_compare":
        case "remove_compare":
            // Работа со сравнением
            $isAdd = ($action === "add_compare");
            
            if (!CModule::IncludeModule("catalog")) {
                echo Json::encode(array("success" => false, "error" => "Модуль каталога недоступен"));
                die();
            }
            
            $userCompare = $USER->GetParam("COMPARE_PRODUCTS");
            if (!is_array($userCompare)) {
                $userCompare = array();
            }
            
            if ($isAdd) {
                if (!in_array($productId, $userCompare)) {
                    $userCompare[] = $productId;
                }
                $message = "Товар добавлен к сравнению";
            } else {
                $userCompare = array_diff($userCompare, array($productId));
                $message = "Товар удален из сравнения";
            }
            
            $USER->SetParam("COMPARE_PRODUCTS", $userCompare);
            echo Json::encode(array("success" => true, "message" => $message));
            break;

        default:
            echo Json::encode(array("success" => false, "error" => "Неизвестное действие"));
            break;
    }

} catch (Exception $e) {
    echo Json::encode(array("success" => false, "error" => "Системная ошибка: " . $e->getMessage()));
}
?>