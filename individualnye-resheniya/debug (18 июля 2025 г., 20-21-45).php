<?php
/**
 * Debug handler to check what's causing the 500 error
 */

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Set content type for JSON response
header('Content-Type: application/json');

try {
    // Basic check
    echo json_encode([
        'success' => true,
        'message' => 'Debug handler working',
        'php_version' => PHP_VERSION,
        'server_time' => date('Y-m-d H:i:s')
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
    ]);
}
?>
