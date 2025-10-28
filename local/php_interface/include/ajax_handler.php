<?php
/**
 * Ajax Handler Wrapper for Bitrix
 * Version: 1.0.0
 * Date: 2025-01-17
 * Description: Wrapper для подключения универсального обработчика форм к Bitrix
 * File: /local/php_interface/include/ajax_handler.php
 */

// This file is included in Bitrix init.php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

// Add event handler for AJAX requests
AddEventHandler("main", "OnEndBufferContent", "HandleAjaxForms");

function HandleAjaxForms(&$content) {
	// Check if this is an AJAX request to our forms handler
	if (isset($_POST['form_type']) &&
	    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
	    $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {

		// Load our forms handler
		require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/forms_handler.php';

		// Stop further processing
		$content = '';
		return;
	}
}

// Alternative: Add direct endpoint
if (isset($_GET['ajax_action']) && $_GET['ajax_action'] === 'forms_handler') {
	require_once $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/ajax/forms_handler.php';
	die();
}
?>