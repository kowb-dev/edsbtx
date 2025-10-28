<?php
/**
 * Enhanced Bitrix handler for EDS Individual Solutions - Fixed Email Version
 * File: /individualnye-resheniya/handler.php
 * Version: 9.5 - Fixed email subjects and admin notifications
 */

// Start output buffering to prevent header issues
ob_start();

// Set execution time and memory limits for large file uploads
ini_set('max_execution_time', 300);
ini_set('memory_limit', '256M');
ini_set('upload_max_filesize', '10M');
ini_set('post_max_size', '50M');

// Disable error display but enable logging
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Create logs directory first
$logsDir = $_SERVER['DOCUMENT_ROOT'] . '/individualnye-resheniya/logs/';
if (!is_dir($logsDir)) {
	@mkdir($logsDir, 0755, true);
}

$errorLogPath = $logsDir . 'handler_debug.log';
ini_set('log_errors', 1);
ini_set('error_log', $errorLogPath);

// Custom error handler for debugging
function edsErrorHandler($errno, $errstr, $errfile, $errline) {
	$errorMessage = "[" . date('Y-m-d H:i:s') . "] PHP Error [$errno]: $errstr in " . basename($errfile) . " on line $errline";

	global $errorLogPath;
	@error_log($errorMessage, 3, $errorLogPath);

	return false;
}

set_error_handler('edsErrorHandler');

// Utility function for logging
function logMessage($message) {
	$timestamp = date('Y-m-d H:i:s');
	$logEntry = "[$timestamp] $message\n";

	global $errorLogPath;
	@error_log($logEntry, 3, $errorLogPath);
}

// Function to send JSON response safely
function sendJsonResponse($data, $httpCode = 200) {
	while (ob_get_level()) {
		ob_end_clean();
	}

	ob_start();

	if (!headers_sent()) {
		http_response_code($httpCode);
		header('Content-Type: application/json; charset=utf-8');
		header('Cache-Control: no-cache, no-store, must-revalidate');
		header('Pragma: no-cache');
		header('Expires: 0');
		header('Access-Control-Allow-Origin: *');
		header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
		header('Access-Control-Allow-Headers: Content-Type, X-Requested-With');
	}

	echo json_encode($data, JSON_UNESCAPED_UNICODE);

	ob_end_flush();

	if (function_exists('fastcgi_finish_request')) {
		fastcgi_finish_request();
	}

	exit;
}

// Function to send error response
function sendErrorResponse($message, $httpCode = 500, $debug = []) {
	logMessage("ERROR RESPONSE: " . $message);

	$response = [
		'success' => false,
		'message' => $message,
		'debug' => array_merge([
			'timestamp' => date('Y-m-d H:i:s'),
			'error_id' => substr(md5(uniqid()), 0, 8)
		], $debug)
	];

	sendJsonResponse($response, $httpCode);
}

// Email configuration
define('EDS_ADMIN_EMAIL', 'a0123e@ya.ru');
define('EDS_SITE_EMAIL', 'noreply@edsy.ru');
define('EDS_MANAGER_EMAIL', 'orders@edsy.ru');

try {
	logMessage("=== NEW REQUEST " . date('Y-m-d H:i:s') . " ===");
	logMessage("REQUEST_METHOD: " . $_SERVER['REQUEST_METHOD']);
	logMessage("REQUEST_URI: " . $_SERVER['REQUEST_URI']);
	logMessage("CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'unknown'));

	// Handle preflight requests
	if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
		sendJsonResponse(['status' => 'ok'], 200);
	}

	// Security checks
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		sendErrorResponse('Only POST method allowed', 405);
	}

	// Log input size
	$rawInput = file_get_contents('php://input');
	logMessage("RAW INPUT LENGTH: " . strlen($rawInput));
	logMessage("POST data count: " . count($_POST));
	logMessage("FILES data count: " . count($_FILES));

	// Include Bitrix prolog BEFORE any output
	$bitrixPath = $_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php";
	if (!file_exists($bitrixPath)) {
		sendErrorResponse("Bitrix core file not found: $bitrixPath");
	}

	// Clean output buffer before including Bitrix
	while (ob_get_level()) {
		ob_end_clean();
	}

	// Include Bitrix
	require_once($bitrixPath);

	// Start our output buffering again after Bitrix
	ob_start();

	if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
		sendErrorResponse("Bitrix core not loaded properly");
	}

	logMessage("Bitrix loaded successfully");

	// Load required Bitrix modules
	if (!CModule::IncludeModule("iblock")) {
		sendErrorResponse("Could not load iblock module");
	}

	if (!CModule::IncludeModule("main")) {
		sendErrorResponse("Could not load main module");
	}

	// Set proper locale for numeric formatting
	setlocale(LC_NUMERIC, 'C');

	// Get input data
	$data = null;
	$attachments = [];
	$action = '';

	// Check content type and process accordingly
	$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
	logMessage("Processing content type: " . $contentType);

	if (strpos($contentType, 'multipart/form-data') !== false) {
		logMessage("Processing multipart form data");

		if (isset($_FILES['attachments']) && !empty($_FILES['attachments']['name'][0])) {
			$attachments = $_FILES['attachments'];
			logMessage("Files received: " . count($attachments['name']));
		}

		if (isset($_POST['data'])) {
			$jsonData = $_POST['data'];
			$data = json_decode($jsonData, true);

			if (json_last_error() !== JSON_ERROR_NONE) {
				sendErrorResponse('Invalid JSON data: ' . json_last_error_msg());
			}
		} else {
			$data = $_POST;
			unset($data['attachments']);
		}

		$action = $_POST['action'] ?? '';

	} else {
		logMessage("Processing JSON data");

		if (empty($rawInput)) {
			sendErrorResponse('No input data received');
		}

		$requestData = json_decode($rawInput, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			sendErrorResponse('Invalid JSON input: ' . json_last_error_msg());
		}

		if (!$requestData || !isset($requestData['action'])) {
			sendErrorResponse('Missing action parameter');
		}

		$data = $requestData['data'] ?? [];
		$action = $requestData['action'];
	}

	logMessage("Action: " . $action);
	logMessage("Form data keys: " . implode(', ', array_keys($data)));

	// Include EmailTemplateManager if available
	$emailTemplateFile = __DIR__ . '/classes/EmailTemplateManager.php';
	$emailTemplateManager = null;
	if (file_exists($emailTemplateFile)) {
		require_once($emailTemplateFile);
		if (class_exists('EmailTemplateManager')) {
			$emailTemplateManager = new EmailTemplateManager();
		}
	}

	// Action router with immediate response
	$result = null;
	switch ($action) {
		case 'submit_wizard':
			$result = handleWizardSubmission($data, $attachments, $emailTemplateManager);
			break;

		case 'generate_pdf':
			$result = generatePDFDownload($data, $emailTemplateManager);
			break;

		case 'consultation_request':
			$result = handleConsultationRequest($data, $emailTemplateManager);
			break;

		default:
			sendErrorResponse("Unknown action: $action");
	}

	if ($result) {
		sendJsonResponse($result, 200);
	} else {
		sendErrorResponse("No result returned from action handler");
	}

} catch (Exception $e) {
	logMessage("EXCEPTION: " . $e->getMessage());
	logMessage("EXCEPTION FILE: " . $e->getFile());
	logMessage("EXCEPTION LINE: " . $e->getLine());
	logMessage("EXCEPTION TRACE: " . $e->getTraceAsString());

	sendErrorResponse(
		$e->getMessage(),
		500,
		[
			'file' => basename($e->getFile()),
			'line' => $e->getLine()
		]
	);
} catch (Error $e) {
	logMessage("FATAL ERROR: " . $e->getMessage());
	logMessage("FATAL ERROR FILE: " . $e->getFile());
	logMessage("FATAL ERROR LINE: " . $e->getLine());

	sendErrorResponse(
		"Fatal error occurred: " . $e->getMessage(),
		500,
		[
			'file' => basename($e->getFile()),
			'line' => $e->getLine()
		]
	);
}

// ===== UTILITY FUNCTIONS =====

function transliterate($text) {
	$transliterationTable = [
		'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd',
		'е' => 'e', 'ё' => 'yo', 'ж' => 'zh', 'з' => 'z', 'и' => 'i',
		'й' => 'y', 'к' => 'k', 'л' => 'l', 'м' => 'm', 		'н' => 'n',
		'о' => 'o', 'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't',
		'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'ts', 'ч' => 'ch',
		'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => '',
		'э' => 'e', 'ю' => 'yu', 'я' => 'ya',
		'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D',
		'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh', 'З' => 'Z', 'И' => 'I',
		'Й' => 'Y', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N',
		'О' => 'O', 'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T',
		'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'Ts', 'Ч' => 'Ch',
		'Ш' => 'Sh', 'Щ' => 'Sch', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '',
		'Э' => 'E', 'Ю' => 'Yu', 'Я' => 'Ya'
	];

	$transliterated = strtr($text, $transliterationTable);
	$transliterated = preg_replace('/[^a-zA-Z0-9._-]/', '_', $transliterated);
	$transliterated = preg_replace('/_{2,}/', '_', $transliterated);
	$transliterated = trim($transliterated, '_');

	return $transliterated ?: 'file';
}

function generateSafeFilename($originalName, $prefix = '') {
	$pathInfo = pathinfo($originalName);
	$baseName = $pathInfo['filename'] ?? 'file';
	$extension = $pathInfo['extension'] ?? '';

	$safeBaseName = transliterate($baseName);

	if (empty($safeBaseName)) {
		$safeBaseName = 'file';
	}

	$timestamp = date('Ymd_His');
	$uniqueId = substr(uniqid(), -6);

	if (!empty($prefix)) {
		$fileName = $prefix . '_' . $timestamp . '_' . $uniqueId . '_' . $safeBaseName;
	} else {
		$fileName = $timestamp . '_' . $uniqueId . '_' . $safeBaseName;
	}

	if (!empty($extension)) {
		$fileName .= '.' . strtolower($extension);
	}

	return $fileName;
}

// ===== EMAIL FUNCTIONS =====

function sendEmailWithTemplate($to, $subject, $htmlContent, $textContent = null, $from = null) {
	try {
		logMessage("Sending email to: $to");
		logMessage("Subject: $subject");

		$from = $from ?: EDS_SITE_EMAIL;
		$fromName = 'EDS - Electric Distribution Systems';

		// Encode subject for proper UTF-8 handling
		$encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';

		// Create multipart message
		$boundary = "EDS-EMAIL-" . md5(time() . rand());

		$headers = [
			'From: =?UTF-8?B?' . base64_encode($fromName) . '?= <' . $from . '>',
			'Reply-To: ' . EDS_MANAGER_EMAIL,
			'MIME-Version: 1.0',
			'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
			'Content-Transfer-Encoding: 8bit',
			'X-Mailer: EDS System v9.5',
			'X-Priority: 3',
			'Date: ' . date('r')
		];

		$headerString = implode("\r\n", $headers);

		// Build multipart message
		$message = "--$boundary\r\n";

		// Plain text version
		if ($textContent) {
			$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
			$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
			$message .= $textContent . "\r\n\r\n";
			$message .= "--$boundary\r\n";
		}

		// HTML version
		$message .= "Content-Type: text/html; charset=UTF-8\r\n";
		$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message .= $htmlContent . "\r\n\r\n";
		$message .= "--$boundary--\r\n";

		// Send email
		$result = mail($to, $encodedSubject, $message, $headerString);

		if ($result) {
			logMessage("Email sent successfully to: $to");
			return true;
		} else {
			logMessage("Failed to send email to: $to");
			return false;
		}

	} catch (Exception $e) {
		logMessage("Email sending error: " . $e->getMessage());
		return false;
	}
}

// ===== FILE HANDLING FUNCTIONS =====

function processFileUploads($attachments) {
	$uploadedFiles = [];
	$uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/individualnye-resheniya/uploads/';

	if (!is_dir($uploadDir)) {
		if (!mkdir($uploadDir, 0755, true)) {
			throw new Exception("Could not create upload directory");
		}
	}

	$maxFileSize = 10 * 1024 * 1024; // 10MB
	$allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'jpg', 'jpeg', 'png', 'dwg', 'dxf'];

	// Handle both single and multiple file uploads
	if (isset($attachments['name'])) {
		$fileCount = is_array($attachments['name']) ? count($attachments['name']) : 1;
		logMessage("Processing $fileCount files");

		for ($i = 0; $i < $fileCount; $i++) {
			$error = is_array($attachments['error']) ? $attachments['error'][$i] : $attachments['error'];

			if ($error !== UPLOAD_ERR_OK) {
				logMessage("File upload error for file $i: " . $error);
				continue;
			}

			$originalFileName = is_array($attachments['name']) ? $attachments['name'][$i] : $attachments['name'];
			$fileTmpName = is_array($attachments['tmp_name']) ? $attachments['tmp_name'][$i] : $attachments['tmp_name'];
			$fileSize = is_array($attachments['size']) ? $attachments['size'][$i] : $attachments['size'];

			logMessage("Processing file: $originalFileName");

			if ($fileSize > $maxFileSize) {
				throw new Exception("File '$originalFileName' is too large");
			}

			$fileExtension = strtolower(pathinfo($originalFileName, PATHINFO_EXTENSION));
			if (!in_array($fileExtension, $allowedExtensions)) {
				throw new Exception("Unsupported file type: '$originalFileName'");
			}

			$safeFileName = generateSafeFilename($originalFileName, 'tz');
			$filePath = $uploadDir . $safeFileName;

			if (move_uploaded_file($fileTmpName, $filePath)) {
				$uploadedFiles[] = [
					'original_name' => $originalFileName,
					'file_name' => $safeFileName,
					'file_path' => $filePath,
					'file_size' => $fileSize,
					'file_type' => $fileExtension
				];
				logMessage("File uploaded successfully: $originalFileName -> $safeFileName");
			} else {
				throw new Exception("Failed to upload file: '$originalFileName'");
			}
		}
	}

	logMessage("Successfully processed " . count($uploadedFiles) . " files");
	return $uploadedFiles;
}

// ===== DATABASE FUNCTIONS =====

function getLeadsIBlockId() {
	$arFilter = [
		'TYPE' => 'content',
		'SITE_ID' => SITE_ID,
		'CODE' => 'eds_leads'
	];

	$rsIBlock = CIBlock::GetList([], $arFilter);
	if ($arIBlock = $rsIBlock->Fetch()) {
		logMessage("Found existing IBlock with ID: " . $arIBlock['ID']);
		return $arIBlock['ID'];
	}

	return createLeadsIBlock();
}

function createLeadsIBlock() {
	$ib = new CIBlock;

	$arFields = [
		"ACTIVE" => "Y",
		"NAME" => "EDS Leads",
		"CODE" => "eds_leads",
		"IBLOCK_TYPE_ID" => "content",
		"SITE_ID" => SITE_ID,
		"SORT" => 500,
		"GROUP_ID" => ["2" => "R"]
	];

	$iblockId = $ib->Add($arFields);

	if (!$iblockId) {
		logMessage("Could not create IBlock: " . $ib->LAST_ERROR);
		throw new Exception("Could not create IBlock: " . $ib->LAST_ERROR);
	}

	createIBlockProperties($iblockId);
	logMessage("Created new IBlock with ID: $iblockId");
	return $iblockId;
}

function createIBlockProperties($iblockId) {
	$ibp = new CIBlockProperty;

	$properties = [
		"REQUEST_ID" => ["NAME" => "Request ID", "PROPERTY_TYPE" => "S"],
		"STATUS" => ["NAME" => "Status", "PROPERTY_TYPE" => "S"],
		"PRIORITY" => ["NAME" => "Priority", "PROPERTY_TYPE" => "S"],
		"SOURCE" => ["NAME" => "Source", "PROPERTY_TYPE" => "S"],
		"CONTACT_NAME" => ["NAME" => "Contact Name", "PROPERTY_TYPE" => "S"],
		"CONTACT_PHONE" => ["NAME" => "Contact Phone", "PROPERTY_TYPE" => "S"],
		"CONTACT_EMAIL" => ["NAME" => "Contact Email", "PROPERTY_TYPE" => "S"],
		"CONTACT_COMPANY" => ["NAME" => "Contact Company", "PROPERTY_TYPE" => "S"],
		"CONTACT_POSITION" => ["NAME" => "Contact Position", "PROPERTY_TYPE" => "S"],
		"OBJECT_TYPE" => ["NAME" => "Object Type", "PROPERTY_TYPE" => "S"],
		"INSTALLATION_TYPE" => ["NAME" => "Installation Type", "PROPERTY_TYPE" => "S"],
		"AUDIENCE_SIZE" => ["NAME" => "Audience Size", "PROPERTY_TYPE" => "S"],
		"PERFORMERS_COUNT" => ["NAME" => "Performers Count", "PROPERTY_TYPE" => "S"],
		"POWER_REQUIREMENT" => ["NAME" => "Power Requirement", "PROPERTY_TYPE" => "S"],
		"POWER_CONNECTION" => ["NAME" => "Power Connection", "PROPERTY_TYPE" => "S"],
		"TIMELINE" => ["NAME" => "Timeline", "PROPERTY_TYPE" => "S"],
		"BUDGET" => ["NAME" => "Budget", "PROPERTY_TYPE" => "S"],
		"EQUIPMENT_CATEGORIES" => ["NAME" => "Equipment Categories", "PROPERTY_TYPE" => "S", "MULTIPLE" => "Y"],
		"REQUIREMENTS" => ["NAME" => "Requirements", "PROPERTY_TYPE" => "S", "MULTIPLE" => "Y"],
		"INSTALLATION_LIMITATIONS" => ["NAME" => "Installation Limitations", "PROPERTY_TYPE" => "S", "MULTIPLE" => "Y"],
		"ADDITIONAL_INFO" => ["NAME" => "Additional Info", "PROPERTY_TYPE" => "S"],
		"ATTACHED_FILES" => ["NAME" => "Attached Files Info", "PROPERTY_TYPE" => "S"],
		"FILES_COUNT" => ["NAME" => "Files Count", "PROPERTY_TYPE" => "N"],
		"DELIVERY_METHOD" => ["NAME" => "Delivery Method", "PROPERTY_TYPE" => "S", "MULTIPLE" => "Y"]
	];

	foreach ($properties as $code => $prop) {
		$arFields = [
			"NAME" => $prop["NAME"],
			"ACTIVE" => "Y",
			"SORT" => 500,
			"CODE" => $code,
			"PROPERTY_TYPE" => $prop["PROPERTY_TYPE"],
			"IBLOCK_ID" => $iblockId,
			"MULTIPLE" => $prop["MULTIPLE"] ?? "N"
		];

		$PropID = $ibp->Add($arFields);
		if (!$PropID) {
			logMessage("Could not create property $code: " . $ibp->LAST_ERROR);
		} else {
			logMessage("Created property $code with ID: $PropID");
		}
	}
}

// ===== MAIN HANDLERS =====

function handleWizardSubmission($formData, $attachments, $emailTemplateManager) {
	try {
		logMessage("Starting wizard submission");
		logMessage("Form data received keys: " . implode(', ', array_keys($formData)));

		// Validate required fields
		$requiredFields = ['objectType', 'installationType', 'audienceSize', 'powerRequirement', 'timeline', 'contactName', 'contactPhone', 'contactEmail'];

		foreach ($requiredFields as $field) {
			if (empty($formData[$field])) {
				throw new Exception("Required field missing: $field");
			}
		}

		if (!filter_var($formData['contactEmail'], FILTER_VALIDATE_EMAIL)) {
			throw new Exception("Invalid email address");
		}

		if (empty($formData['equipment']) || !is_array($formData['equipment'])) {
			throw new Exception("At least one equipment type must be selected");
		}

		// Process file attachments
		$uploadedFiles = [];
		if (!empty($attachments) && isset($attachments['name']) && !empty($attachments['name'][0])) {
			$uploadedFiles = processFileUploads($attachments);
			logMessage("Processed " . count($uploadedFiles) . " uploaded files");
		}

		// Generate unique request ID
		$requestId = 'TZ-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
		logMessage("Generated request ID: $requestId");

		// Send emails
		$emailResult = false;
		try {
			$emailResult = sendWizardEmails($formData, $requestId, $uploadedFiles, $emailTemplateManager);
			logMessage("Email notifications processed");
		} catch (Exception $e) {
			logMessage("Email sending failed: " . $e->getMessage());
		}

		// Save to database
		$leadId = 0;
		try {
			$leadId = saveWizardLead($formData, $requestId, $uploadedFiles);
			logMessage("Saved lead with ID: $leadId");
		} catch (Exception $e) {
			logMessage("Database save failed: " . $e->getMessage());
		}

		return [
			'success' => true,
			'message' => 'Technical specification created successfully',
			'requestId' => $requestId,
			'leadId' => $leadId,
			'attachments' => count($uploadedFiles),
			'emailSent' => $emailResult,
			'timestamp' => date('Y-m-d H:i:s')
		];

	} catch (Exception $e) {
		logMessage("Wizard submission error: " . $e->getMessage());
		throw $e;
	}
}

function sendWizardEmails($formData, $requestId, $uploadedFiles, $emailTemplateManager) {
	try {
		$clientEmail = $formData['contactEmail'];
		$clientName = $formData['contactName'];

		// Generate emails using template manager if available
		if ($emailTemplateManager) {
			$adminHtmlContent = $emailTemplateManager->generateAdminEmail($formData, $requestId, $uploadedFiles);
			$clientHtmlContent = $emailTemplateManager->generateClientEmail($formData, $requestId, $uploadedFiles);
		} else {
			$adminHtmlContent = generateAdminTextEmail($formData, $requestId, $uploadedFiles);
			$clientHtmlContent = generateClientTextEmail($formData, $requestId);
		}

		$emailsSent = 0;

		// Send admin notification to manager
		$adminSubject = "Новое техническое задание #$requestId - EDS";
		if (sendEmailWithTemplate(EDS_MANAGER_EMAIL, $adminSubject, $adminHtmlContent)) {
			$emailsSent++;
		}

		// Send copy to main admin
		if (sendEmailWithTemplate(EDS_ADMIN_EMAIL, $adminSubject, $adminHtmlContent)) {
			$emailsSent++;
		}

		// Send client confirmation
		$clientSubject = "Ваше техническое задание #$requestId получено - EDS";
		if (sendEmailWithTemplate($clientEmail, $clientSubject, $clientHtmlContent)) {
			$emailsSent++;
		}

		logMessage("Sent $emailsSent emails successfully");
		return $emailsSent > 0;

	} catch (Exception $e) {
		logMessage("Email sending error: " . $e->getMessage());
		return false;
	}
}

function generateAdminTextEmail($formData, $requestId, $uploadedFiles) {
	$content = "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>Новое техническое задание</title></head><body>";
	$content .= "<h2>Новое техническое задание #$requestId</h2>";
	$content .= "<p><strong>Дата:</strong> " . date('d.m.Y H:i:s') . "</p>";
	$content .= "<h3>Контактная информация:</h3>";
	$content .= "<p><strong>Имя:</strong> " . htmlspecialchars($formData['contactName']) . "</p>";
	$content .= "<p><strong>Телефон:</strong> " . htmlspecialchars($formData['contactPhone']) . "</p>";
	$content .= "<p><strong>Email:</strong> " . htmlspecialchars($formData['contactEmail']) . "</p>";

	if (!empty($formData['contactCompany'])) {
		$content .= "<p><strong>Компания:</strong> " . htmlspecialchars($formData['contactCompany']) . "</p>";
	}

	$content .= "<h3>Параметры проекта:</h3>";
	$content .= "<p><strong>Тип объекта:</strong> " . translateValue($formData['objectType']) . "</p>";
	$content .= "<p><strong>Тип инсталляции:</strong> " . translateValue($formData['installationType']) . "</p>";
	$content .= "<p><strong>Размер аудитории:</strong> " . translateValue($formData['audienceSize']) . "</p>";
	$content .= "<p><strong>Требуемая мощность:</strong> " . translateValue($formData['powerRequirement']) . "</p>";
	$content .= "<p><strong>Сроки:</strong> " . translateValue($formData['timeline']) . "</p>";

	if (!empty($formData['equipment'])) {
		$content .= "<h3>Оборудование:</h3><ul>";
		foreach ($formData['equipment'] as $equipment) {
			$content .= "<li>" . translateValue($equipment) . "</li>";
		}
		$content .= "</ul>";
	}

	if (!empty($uploadedFiles)) {
		$content .= "<h3>Прикрепленные файлы:</h3><ul>";
		foreach ($uploadedFiles as $file) {
			$content .= "<li>" . htmlspecialchars($file['original_name']) . " (" . formatFileSize($file['file_size']) . ")</li>";
		}
		$content .= "</ul>";
	}

	$content .= "</body></html>";
	return $content;
}

function generateClientTextEmail($formData, $requestId) {
	$content = "<!DOCTYPE html><html><head><meta charset=\"UTF-8\"><title>Ваше техническое задание получено</title></head><body>";
	$content .= "<h2>Спасибо за ваше обращение!</h2>";
	$content .= "<p>Ваше техническое задание #$requestId принято в обработку.</p>";
	$content .= "<p>Наши специалисты свяжутся с вами в ближайшее время для уточнения деталей.</p>";
	$content .= "<p>С уважением,<br>Команда EDS - Electric Distribution Systems</p>";
	$content .= "</body></html>";

	return $content;
}

function saveWizardLead($data, $requestId, $uploadedFiles = []) {
	try {
		$iblockId = getLeadsIBlockId();
		if (!$iblockId) {
			throw new Exception("Could not get or create IBlock for leads");
		}

		logMessage("Using IBlock ID: $iblockId");

		$el = new CIBlockElement;

		$equipmentCategories = [];
		if (!empty($data['equipment']) && is_array($data['equipment'])) {
			$equipmentCategories = $data['equipment'];
		}

		$requirements = [];
		if (!empty($data['requirements']) && is_array($data['requirements'])) {
			$requirements = $data['requirements'];
		}

		$installationLimitations = [];
		if (!empty($data['installationLimitations']) && is_array($data['installationLimitations'])) {
			$installationLimitations = $data['installationLimitations'];
		}

		$deliveryMethods = [];
		if (!empty($data['deliveryMethod']) && is_array($data['deliveryMethod'])) {
			$deliveryMethods = $data['deliveryMethod'];
		}

		$attachedFilesInfo = '';
		if (!empty($uploadedFiles)) {
			$filesInfo = [];
			foreach ($uploadedFiles as $file) {
				$filesInfo[] = [
					'original_name' => $file['original_name'],
					'saved_name' => $file['file_name'],
					'size' => $file['file_size'],
					'type' => $file['file_type']
				];
			}
			$attachedFilesInfo = json_encode($filesInfo, JSON_UNESCAPED_UNICODE);
		}

		$arLoadProductArray = [
			"MODIFIED_BY" => 1,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $iblockId,
			"NAME" => "Technical Specification #$requestId - " . htmlspecialcharsbx($data['contactName']),
			"ACTIVE" => "Y",
			"DETAIL_TEXT" => generateLeadDescription($data, $uploadedFiles),
			"DETAIL_TEXT_TYPE" => "text",
			"PROPERTY_VALUES" => [
				"REQUEST_ID" => $requestId,
				"STATUS" => "NEW",
				"PRIORITY" => in_array($data['timeline'] ?? '', ['urgent', 'fast']) ? 'HIGH' : 'NORMAL',
				"SOURCE" => "WEBSITE_WIZARD",
				"CONTACT_NAME" => htmlspecialcharsbx($data['contactName']),
				"CONTACT_PHONE" => htmlspecialcharsbx($data['contactPhone']),
				"CONTACT_EMAIL" => htmlspecialcharsbx($data['contactEmail']),
				"CONTACT_COMPANY" => htmlspecialcharsbx($data['contactCompany'] ?? ''),
				"CONTACT_POSITION" => htmlspecialcharsbx($data['contactPosition'] ?? ''),
				"OBJECT_TYPE" => $data['objectType'],
				"INSTALLATION_TYPE" => $data['installationType'],
				"AUDIENCE_SIZE" => $data['audienceSize'],
				"PERFORMERS_COUNT" => $data['performersCount'] ?? '',
				"POWER_REQUIREMENT" => $data['powerRequirement'],
				"POWER_CONNECTION" => $data['powerConnection'] ?? '',
				"TIMELINE" => $data['timeline'],
				"BUDGET" => $data['budget'] ?? '',
				"EQUIPMENT_CATEGORIES" => $equipmentCategories,
				"REQUIREMENTS" => $requirements,
				"INSTALLATION_LIMITATIONS" => $installationLimitations,
				"DELIVERY_METHOD" => $deliveryMethods,
				"ADDITIONAL_INFO" => htmlspecialcharsbx($data['additionalInfo'] ?? ''),
				"ATTACHED_FILES" => $attachedFilesInfo,
				"FILES_COUNT" => count($uploadedFiles),
			]
		];

		logMessage("Attempting to save element");

		$leadId = $el->Add($arLoadProductArray);

		if (!$leadId) {
			$error = $el->LAST_ERROR;
			logMessage("Database save error: " . $error);
			throw new Exception("Database save error: " . $error);
		}

		logMessage("Lead saved successfully with ID: $leadId");
		return $leadId;

	} catch (Exception $e) {
		logMessage("Save lead error: " . $e->getMessage());
		throw $e;
	}
}

function handleConsultationRequest($data, $emailTemplateManager) {
	try {
		if (empty($data['consultationName']) || empty($data['consultationPhone'])) {
			throw new Exception("Name and phone are required");
		}

		$consultationId = 'CONS-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));
		logMessage("Consultation request: $consultationId");

		// Send emails
		$emailSent = false;
		try {
			$subject = "Запрос на консультацию #$consultationId - EDS";
			$htmlMessage = generateConsultationNotificationHTML($data, $consultationId);

			// Send to manager
			if (sendEmailWithTemplate(EDS_MANAGER_EMAIL, $subject, $htmlMessage)) {
				$emailSent = true;
			}

			// Send to main admin
			if (sendEmailWithTemplate(EDS_ADMIN_EMAIL, $subject, $htmlMessage)) {
				$emailSent = true;
			}

			logMessage("Consultation notification emails sent");
		} catch (Exception $e) {
			logMessage("Consultation email failed: " . $e->getMessage());
		}

		// Save to database
		try {
			saveConsultationRequest($data, $consultationId);
			logMessage("Consultation saved to database");
		} catch (Exception $e) {
			logMessage("Consultation save failed: " . $e->getMessage());
		}

		return [
			'success' => true,
			'message' => 'Consultation request received',
			'consultationId' => $consultationId,
			'emailSent' => $emailSent,
			'timestamp' => date('Y-m-d H:i:s')
		];

	} catch (Exception $e) {
		logMessage("Consultation request error: " . $e->getMessage());
		throw $e;
	}
}

function generateConsultationNotificationHTML($data, $consultationId) {
	$html = '<!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Запрос на консультацию #' . htmlspecialchars($consultationId) . '</title>
    </head>
    <body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333;">
        <div style="max-width: 600px; margin: 0 auto; padding: 20px;">
            <h2 style="color: #ff2545; border-bottom: 2px solid #ff2545; padding-bottom: 10px;">
                📞 Запрос на консультацию
            </h2>
            <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin: 20px 0;">
                <p><strong>ID заявки:</strong> ' . htmlspecialchars($consultationId) . '</p>
                <p><strong>Дата запроса:</strong> ' . date('d.m.Y H:i:s') . '</p>
                <p><strong>Имя клиента:</strong> ' . htmlspecialchars($data['consultationName']) . '</p>
                <p><strong>Телефон:</strong> <a href="tel:' . preg_replace('/[^+\d]/', '', $data['consultationPhone']) . '" style="color: #ff2545;">' . htmlspecialchars($data['consultationPhone']) . '</a></p>
                <p><strong>IP адрес:</strong> ' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '</p>
            </div>
            <div style="background: #ff2545; color: white; padding: 20px; border-radius: 8px; text-align: center;">
                <h3 style="margin: 0 0 10px 0;">⚡ Срочно требуется действие!</h3>
                <p style="margin: 0 0 15px 0;">Клиент ожидает звонка в ближайшее время!</p>
                <a href="tel:' . preg_replace('/[^+\d]/', '', $data['consultationPhone']) . '" 
                   style="background: white; color: #ff2545; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                    📞 Позвонить клиенту
                </a>
            </div>
            <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; text-align: center; color: #666;">
                <p>EDS - Electric Distribution Systems</p>
                <p>Система уведомлений v9.5</p>
            </div>
        </div>
    </body>
    </html>';

	return $html;
}

function saveConsultationRequest($data, $consultationId) {
	try {
		$iblockId = getLeadsIBlockId();
		if (!$iblockId) {
			throw new Exception("Could not get IBlock for consultation");
		}

		$el = new CIBlockElement;

		$arLoadProductArray = [
			"MODIFIED_BY" => 1,
			"IBLOCK_SECTION_ID" => false,
			"IBLOCK_ID" => $iblockId,
			"NAME" => "Consultation #$consultationId - " . htmlspecialcharsbx($data['consultationName']),
			"ACTIVE" => "Y",
			"DETAIL_TEXT" => "Consultation request from " . htmlspecialcharsbx($data['consultationName']),
			"PROPERTY_VALUES" => [
				"REQUEST_ID" => $consultationId,
				"STATUS" => "NEW",
				"PRIORITY" => "HIGH",
				"SOURCE" => "WEBSITE_CONSULTATION",
				"CONTACT_NAME" => htmlspecialcharsbx($data['consultationName']),
				"CONTACT_PHONE" => htmlspecialcharsbx($data['consultationPhone']),
			]
		];

		$id = $el->Add($arLoadProductArray);

		if (!$id) {
			throw new Exception("Database save error: " . $el->LAST_ERROR);
		}

		return $consultationId;

	} catch (Exception $e) {
		logMessage("Save consultation error: " . $e->getMessage());
		throw $e;
	}
}

function generatePDFDownload($data, $emailTemplateManager) {
	try {
		logMessage("PDF download requested");
		$requestId = 'TZ-' . date('Ymd') . '-' . sprintf('%04d', rand(1000, 9999));

		$htmlContent = generateAdminTextEmail($data, $requestId, []);

		while (ob_get_level()) {
			ob_end_clean();
		}

		header('Content-Type: text/html; charset=utf-8');
		header('Content-Disposition: attachment; filename="TZ_' . $requestId . '.html"');
		header('Content-Length: ' . strlen($htmlContent));

		echo $htmlContent;
		exit;

	} catch (Exception $e) {
		logMessage("PDF download error: " . $e->getMessage());
		throw $e;
	}
}

// ===== UTILITY FUNCTIONS =====

function translateValue($value) {
	$translations = [
		'theater' => 'Театр',
		'concert' => 'Концертная площадка',
		'studio' => 'Студия',
		'event' => 'Выездное мероприятие',
		'conference' => 'Конференц-зал',
		'other' => 'Другое',
		'permanent' => 'Стационарная установка',
		'temporary' => 'Временная установка',
		'mobile' => 'Мобильная система',
		'1' => 'До 50 человек',
		'2' => '50-200 человек',
		'3' => '200-500 человек',
		'4' => '500-1000 человек',
		'5' => '1000-5000 человек',
		'6' => '5000+ человек',
		'low' => 'До 10 кВт',
		'medium' => '10-50 кВт',
		'high' => '50-200 кВт',
		'very-high' => 'Свыше 200 кВт',
		'220v' => '220В (однофазная сеть)',
		'380v' => '380В (трёхфазная сеть)',
		'unknown' => 'Не знаю',
		'urgent' => 'Срочно (1-2 недели)',
		'fast' => 'Быстро (до месяца)',
		'standard' => 'Стандартно (2-3 месяца)',
		'flexible' => 'Гибко (без срочности)',
		'power-distributors' => 'Дистрибьюторы питания',
		'dmx-equipment' => 'DMX-оборудование',
		'winch-controls' => 'Пульты управления лебёдками',
		'stage-boxes' => 'Лючки и коммутационные коробки',
		'sequencers' => 'Секвенсоры',
		'cables-connectors' => 'Кабели и разъёмы',
		'mobility' => 'Повышенная мобильность',
		'safety' => 'Повышенные требования безопасности',
		'weather-resistance' => 'Защита от внешних воздействий',
		'remote-control' => 'Дистанционное управление',
		'monitoring' => 'Мониторинг параметров',
		'backup-power' => 'Резервное питание',
		'time-limited' => 'Ограниченное время монтажа',
		'noise-restrictions' => 'Ограничения по шуму',
		'access-restrictions' => 'Ограниченный доступ',
		'operating-restrictions' => 'Работа в действующем объекте',
		'email' => 'Email',
		'phone-call' => 'Звонок менеджера'
	];

	return $translations[$value] ?? $value;
}

function generateLeadDescription($data, $uploadedFiles = []) {
	$description = "ТЕХНИЧЕСКОЕ ЗАДАНИЕ НА ПРОФЕССИОНАЛЬНОЕ ОБОРУДОВАНИЕ\n";
	$description .= "Создано: " . date('d.m.Y H:i:s') . "\n\n";

	$description .= "ХАРАКТЕРИСТИКИ ПРОЕКТА:\n";
	$description .= "Тип объекта: " . translateValue($data['objectType'] ?? 'Не указано') . "\n";
	$description .= "Тип инсталляции: " . translateValue($data['installationType'] ?? 'Не указано') . "\n";
	$description .= "Размер аудитории: " . translateValue($data['audienceSize'] ?? 'Не указано') . "\n";

	if (!empty($data['performersCount'])) {
		$description .= "Количество исполнителей: " . translateValue($data['performersCount']) . "\n";
	}

	$description .= "Требуемая мощность: " . translateValue($data['powerRequirement'] ?? 'Не указано') . "\n";

	if (!empty($data['powerConnection'])) {
		$description .= "Тип электропитания: " . translateValue($data['powerConnection']) . "\n";
	}

	$description .= "Сроки: " . translateValue($data['timeline'] ?? 'Не указано') . "\n";

	if (!empty($data['budget'])) {
		$description .= "Бюджет: " . translateValue($data['budget']) . "\n";
	}

	$description .= "\n";

	if (!empty($data['equipment']) && is_array($data['equipment'])) {
		$description .= "КАТЕГОРИИ ОБОРУДОВАНИЯ:\n";
		foreach ($data['equipment'] as $equipment) {
			$description .= "• " . translateValue($equipment) . "\n";
		}
		$description .= "\n";
	}

	if (!empty($data['requirements']) && is_array($data['requirements'])) {
		$description .= "ОСОБЫЕ ТРЕБОВАНИЯ:\n";
		foreach ($data['requirements'] as $requirement) {
			$description .= "• " . translateValue($requirement) . "\n";
		}
		$description .= "\n";
	}

	if (!empty($data['installationLimitations']) && is_array($data['installationLimitations'])) {
		$description .= "ОГРАНИЧЕНИЯ ПО УСТАНОВКЕ:\n";
		foreach ($data['installationLimitations'] as $limitation) {
			$description .= "• " . translateValue($limitation) . "\n";
		}
		$description .= "\n";
	}

	$description .= "КОНТАКТНАЯ ИНФОРМАЦИЯ:\n";
	$description .= "Имя: " . ($data['contactName'] ?? 'Не указано') . "\n";
	$description .= "Телефон: " . ($data['contactPhone'] ?? 'Не указано') . "\n";
	$description .= "Email: " . ($data['contactEmail'] ?? 'Не указано') . "\n";

	if (!empty($data['contactCompany'])) {
		$description .= "Компания: " . $data['contactCompany'] . "\n";
	}

	if (!empty($data['contactPosition'])) {
		$description .= "Должность: " . $data['contactPosition'] . "\n";
	}

	if (!empty($data['additionalInfo'])) {
		$description .= "\nДОПОЛНИТЕЛЬНАЯ ИНФОРМАЦИЯ:\n";
		$description .= $data['additionalInfo'] . "\n\n";
	}

	if (!empty($uploadedFiles)) {
		$description .= "ПРИКРЕПЛЕННЫЕ ФАЙЛЫ:\n";
		foreach ($uploadedFiles as $file) {
			$description .= "• " . $file['original_name'] . " (" . formatFileSize($file['file_size']) . ") - " . $file['file_type'] . "\n";
			$description .= "  Сохранен как: " . $file['file_name'] . "\n";
		}
		$description .= "\n";
	}

	if (!empty($data['deliveryMethod']) && is_array($data['deliveryMethod'])) {
		$description .= "СПОСОБЫ ПОЛУЧЕНИЯ ТЗ:\n";
		foreach ($data['deliveryMethod'] as $method) {
			$description .= "• " . translateValue($method) . "\n";
		}
		$description .= "\n";
	}

	return $description;
}

function formatFileSize($bytes) {
	if ($bytes === 0) return '0 Bytes';
	$k = 1024;
	$sizes = ['Bytes', 'KB', 'MB', 'GB'];
	$i = floor(log($bytes) / log($k));
	return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

logMessage("Handler completed successfully");
?>