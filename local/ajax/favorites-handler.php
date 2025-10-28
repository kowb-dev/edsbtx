<?php
/**
 * Favorites AJAX Handler (Fallback)
 * Location: /local/ajax/favorites-handler.php
 * Version: 1.0.0
 * 
 * This is a fallback handler for older Bitrix installations
 * or when BX.ajax.runComponentAction is not available
 */

define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_PUBLIC_MODE', 1);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Application;
use Bitrix\Main\Context;
use Bitrix\Main\Loader;

// Set JSON header
header('Content-Type: application/json');

// Check if user is authorized
global $USER;
if (!$USER->IsAuthorized()) {
    echo json_encode([
        'success' => false,
        'message' => 'Пользователь не авторизован',
        'error' => 'USER_NOT_AUTHORIZED'
    ]);
    die();
}

try {
    // Get request data
    $request = Context::getCurrent()->getRequest();
    
    // Get JSON data from request body
    $jsonData = file_get_contents('php://input');
    $data = json_decode($jsonData, true);
    
    // Validate action
    $action = $data['action'] ?? '';
    if (empty($action)) {
        throw new \Exception('Action not specified');
    }
    
    // Handle toggle action
    if ($action === 'toggle') {
        $productId = isset($data['productId']) ? (int)$data['productId'] : 0;
        
        if ($productId <= 0) {
            throw new \Exception('Invalid product ID');
        }
        
        // Get user ID
        $userId = $USER->GetID();
        
        // Get current favorites from user profile
        $rsUser = \CUser::GetByID($userId);
        $arUser = $rsUser->Fetch();
        $favorites = $arUser['UF_FAVORITES'] ?? [];
        
        if (!is_array($favorites)) {
            $favorites = [];
        }
        
        // Check if product is in favorites
        $key = array_search($productId, $favorites);
        $shouldAdd = ($key === false);
        
        // Toggle favorite
        if ($shouldAdd) {
            $favorites[] = $productId;
            $message = 'Товар добавлен в избранное';
        } else {
            unset($favorites[$key]);
            $message = 'Товар удален из избранного';
        }
        
        // Update user profile
        $user = new \CUser;
        $updateResult = $user->Update($userId, [
            'UF_FAVORITES' => array_values($favorites)
        ]);
        
        if ($updateResult) {
            echo json_encode([
                'success' => true,
                'message' => $message,
                'data' => [
                    'inFavorites' => $shouldAdd,
                    'count' => count($favorites),
                    'productId' => $productId
                ]
            ]);
        } else {
            throw new \Exception($user->LAST_ERROR);
        }
        
    } else {
        throw new \Exception('Unknown action: ' . $action);
    }
    
} catch (\Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ошибка: ' . $e->getMessage(),
        'error' => 'EXCEPTION',
        'trace' => $e->getTraceAsString()
    ]);
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');