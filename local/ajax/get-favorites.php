<?php
/**
 * Get Favorites List AJAX Handler
 * Location: /local/ajax/get-favorites.php
 * Version: 1.0.0
 * Author: KW
 * URI: https://kowb.ru
 * 
 * Returns list of user's favorite product IDs
 */

// Disable statistics tracking
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

// Set JSON response header
header('Content-Type: application/json; charset=UTF-8');

global $USER;

try {
    // Check authorization
    if (!$USER->IsAuthorized()) {
        echo json_encode([
            'success' => false,
            'message' => 'User not authorized',
            'favorites' => [],
            'count' => 0
        ], JSON_UNESCAPED_UNICODE);
        die();
    }
    
    // Get user ID
    $userId = $USER->GetID();
    
    // Get user data
    $rsUser = CUser::GetByID($userId);
    $arUser = $rsUser->Fetch();
    
    if (!$arUser) {
        throw new Exception('User not found');
    }
    
    // Get favorites array
    $favorites = $arUser['UF_FAVORITES'] ?? [];
    
    // Ensure it's an array
    if (!is_array($favorites)) {
        $favorites = [];
    }
    
    // Filter out empty values and convert to integers
    $favorites = array_filter(array_map('intval', $favorites), function($id) {
        return $id > 0;
    });
    
    // Reset array keys
    $favorites = array_values($favorites);
    
    // Return response
    echo json_encode([
        'success' => true,
        'favorites' => $favorites,
        'count' => count($favorites)
    ], JSON_UNESCAPED_UNICODE);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error: ' . $e->getMessage(),
        'favorites' => [],
        'count' => 0
    ], JSON_UNESCAPED_UNICODE);
}

require($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');