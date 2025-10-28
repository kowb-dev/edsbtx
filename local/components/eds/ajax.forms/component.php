<?php
/**
 * EDS AJAX Forms Component
 * Version: 2.1.0
 * Date: 2025-07-18
 * Description: Компонент для работы с обработчиком форм
 * File: /local/components/eds/ajax.forms/component.php
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

// Set JSON response headers
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-cache, no-store, must-revalidate');
header('Pragma: no-cache');
header('Expires: 0');

// Security checks
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {

	echo json_encode([
		'success' => false,
		'message' => 'Only AJAX requests allowed',
		'timestamp' => date('Y-m-d H:i:s')
	], JSON_UNESCAPED_UNICODE);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	echo json_encode([
		'success' => false,
		'message' => 'Only POST method allowed',
		'timestamp' => date('Y-m-d H:i:s')
	], JSON_UNESCAPED_UNICODE);
	exit;
}

// Path to forms handler
$handlerPath = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/forms_handler.php';

if (file_exists($handlerPath)) {
	// Include the forms handler
	require_once $handlerPath;
} else {
	// Handler not found
	echo json_encode([
		'success' => false,
		'message' => 'Forms handler not found',
		'timestamp' => date('Y-m-d H:i:s'),
		'expected_path' => $handlerPath
	], JSON_UNESCAPED_UNICODE);
	exit;
}
?>