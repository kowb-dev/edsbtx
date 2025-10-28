<?php
/**
 * Favorites AJAX Handler
 * Location: /local/ajax/favorites.php
 * Version: 2.0.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Main AJAX endpoint for managing favorites for both authorized and guest users
 */

// Disable statistics tracking for AJAX
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Context;
use Bitrix\Main\Application;

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

// Log function for debugging
function logFavorites($message, $data = null) {
    $logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/favorites.log';
    $logDir = dirname($logFile);
    
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true);
    }
    
    $logMessage = date('[Y-m-d H:i:s] ') . $message;
    if ($data !== null) {
        $logMessage .= ': ' . print_r($data, true);
    }
    $logMessage .= PHP_EOL;
    
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

try {
    global $USER;
    
    // Get request data
    $request = Context::getCurrent()->getRequest();
    
    // Parse JSON from request body
    $rawInput = file_get_contents('php://input');
    logFavorites('Raw input', $rawInput);
    
    // Try to parse as JSON first
    $jsonData = json_decode($rawInput, true);
    
    // If JSON parsing failed, try to get from POST
    if (json_last_error() !== JSON_ERROR_NONE) {
        $data = [
            'action' => $request->getPost('action'),
            'productId' => $request->getPost('productId'),
            'add' => $request->getPost('add')
        ];
    } else {
        $data = $jsonData;
    }
    
    logFavorites('Parsed data', $data);
    
    // Validate action
    $action = $data['action'] ?? '';
    if (empty($action)) {
        throw new Exception('Action not specified');
    }
    
    // Check if action is 'toggle'
    if ($action !== 'toggle') {
        throw new Exception('Unknown action: ' . $action);
    }
    
    // Get and validate product ID
    $productId = isset($data['productId']) ? intval($data['productId']) : 0;
    if ($productId <= 0) {
        throw new Exception('Invalid product ID');
    }
    
    // Check authorization
    if (!$USER->IsAuthorized()) {
        logFavorites('User not authorized', ['productId' => $productId]);
        
        echo json_encode([
            'success' => false,
            'message' => 'Пользователь не авторизован',
            'error' => 'USER_NOT_AUTHORIZED',
            'needAuth' => true
        ], JSON_UNESCAPED_UNICODE);
        die();
    }
    
    // Get user ID
    $userId = $USER->GetID();
    logFavorites('User ID', $userId);
    
    // Get current user favorites from database
    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();
    
    if (!$arUser) {
        throw new Exception('User not found');
    }
    
    // Get current favorites array
    $favorites = $arUser['UF_FAVORITES'] ?? [];
    
    // Ensure favorites is an array
    if (!is_array($favorites)) {
        $favorites = [];
    }
    
    logFavorites('Current favorites', $favorites);
    
    // Check if product is already in favorites
    $key = array_search($productId, $favorites);
    $isInFavorites = ($key !== false);
    
    // Determine action
    $shouldAdd = !$isInFavorites;
    
    // Toggle favorite status
    if ($shouldAdd) {
        // Add to favorites
        $favorites[] = $productId;
        $message = 'Товар добавлен в избранное';
        logFavorites('Adding product to favorites', $productId);
    } else {
        // Remove from favorites
        unset($favorites[$key]);
        $message = 'Товар удален из избранного';
        logFavorites('Removing product from favorites', $productId);
    }
    
    // Reset array keys
    $favorites = array_values($favorites);
    
    logFavorites('New favorites array', $favorites);
    
    // Update user field
    $user = new CUser;
    $updateResult = $user->Update($userId, [
        'UF_FAVORITES' => $favorites
    ]);
    
    if (!$updateResult) {
        $error = $user->LAST_ERROR;
        logFavorites('Update failed', $error);
        throw new Exception('Не удалось обновить избранное: ' . $error);
    }
    
    logFavorites('Update successful', [
        'inFavorites' => $shouldAdd,
        'count' => count($favorites)
    ]);
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => $message,
        'data' => [
            'inFavorites' => $shouldAdd,
            'count' => count($favorites),
            'productId' => $productId,
            'favorites' => $favorites
        ]
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    logFavorites('Exception caught', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'error' => 'EXCEPTION'
    ], JSON_UNESCAPED_UNICODE);
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');