<?php
/**
 * Telegram Handler for Stage Equipment Calculator
 * Обработчик запросов на добавление устройств в Telegram
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 */

// Prevent direct access
if (!defined("B_PROLOG_INCLUDED")) {
	define("B_PROLOG_INCLUDED", true);
}

// Set content type to JSON
header('Content-Type: application/json; charset=utf-8');

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 0);

/**
 * Telegram Bot Class
 */
class TelegramBot
{
	private $botApiKey;
	private $userID;

	public function __construct($botApiKey, $userID)
	{
		$this->botApiKey = $botApiKey;
		$this->userID = $userID;
	}

	/**
	 * Send message to Telegram
	 */
	public function sendMessage($message, $reply_markup = null)
	{
		$chat_id = $this->userID;
		$data = array(
			'chat_id' => urlencode($chat_id),
			'text' => $message,
			'disable_web_page_preview' => false,
			'reply_markup' => $reply_markup,
			'parse_mode' => 'HTML'
		);

		$url = "https://api.telegram.org/bot{$this->botApiKey}/sendMessage";

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 30);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		$result = curl_exec($ch);
		$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);

		return [
			'success' => ($httpCode == 200),
			'result' => $result,
			'http_code' => $httpCode
		];
	}
}

/**
 * Validate input data
 */
function validateDeviceRequest($data)
{
	$errors = [];

	if (empty($data['deviceName'])) {
		$errors[] = 'Device name is required';
	}

	if (empty($data['devicePower']) || !is_numeric($data['devicePower']) || $data['devicePower'] <= 0) {
		$errors[] = 'Valid device power is required';
	}

	if (empty($data['userName'])) {
		$errors[] = 'User name is required';
	}

	if (empty($data['userEmail']) || !filter_var($data['userEmail'], FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Valid email is required';
	}

	return $errors;
}

/**
 * Format message for Telegram
 */
function formatTelegramMessage($data)
{
	$message = "🔧 <b>Запрос на добавление устройства</b>\n\n";
	$message .= "📱 <b>Название прибора:</b> " . htmlspecialchars($data['deviceName']) . "\n";
	$message .= "⚡ <b>Мощность прибора:</b> " . htmlspecialchars($data['devicePower']) . " Вт\n\n";
	$message .= "👤 <b>Контакты пользователя:</b>\n";
	$message .= "• <b>Имя:</b> " . htmlspecialchars($data['userName']) . "\n";
	$message .= "• <b>Email:</b> " . htmlspecialchars($data['userEmail']) . "\n";

	if (!empty($data['comments'])) {
		$message .= "\n💬 <b>Ссылка на сайт производителя:</b>\n" . htmlspecialchars($data['comments']);
	}

	$message .= "\n\n🌐 <b>Источник:</b> Калькулятор электрооборудования сцены";
	$message .= "\n📅 <b>Дата:</b> " . date('d.m.Y H:i:s');

	return $message;
}

/**
 * Log error for debugging
 */
function logError($message, $data = null)
{
	$logMessage = "[" . date('Y-m-d H:i:s') . "] " . $message;
	if ($data) {
		$logMessage .= " Data: " . json_encode($data);
	}
	$logMessage .= "\n";

	// Log to file (adjust path as needed)
	$logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/telegram_errors.log';
	if (is_writable(dirname($logFile))) {
		file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
	}
}

// Main processing
try {
	// Check if request is POST
	if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
		throw new Exception('Only POST requests are allowed');
	}

	// Get JSON data
	$input = file_get_contents('php://input');
	$data = json_decode($input, true);

	if (json_last_error() !== JSON_ERROR_NONE) {
		throw new Exception('Invalid JSON data');
	}

	// Validate required data
	$validationErrors = validateDeviceRequest($data);
	if (!empty($validationErrors)) {
		throw new Exception('Validation failed: ' . implode(', ', $validationErrors));
	}

	// Telegram Bot Configuration
	// Replace with your actual bot token and chat IDs
	$botToken = '1964945712:AAEp8LYysqc1qgut0XJ4Scy1gMXDO2L_zIE'; // Replace with actual token
	$chatIds = [
//		418449109,  // Replace with actual chat ID 1
		169279758   // Replace with actual chat ID 2
	];

	// Format message
	$message = formatTelegramMessage($data);

	// Send to all configured chats
	$sendResults = [];
	foreach ($chatIds as $chatId) {
		$telegram = new TelegramBot($botToken, $chatId);
		$result = $telegram->sendMessage($message);
		$sendResults[] = $result;

		if (!$result['success']) {
			logError("Failed to send message to chat {$chatId}", $result);
		}
	}

	// Check if at least one message was sent successfully
	$atLeastOneSuccess = false;
	foreach ($sendResults as $result) {
		if ($result['success']) {
			$atLeastOneSuccess = true;
			break;
		}
	}

	if ($atLeastOneSuccess) {
		echo json_encode([
			'success' => true,
			'message' => 'Запрос успешно отправлен'
		]);
	} else {
		throw new Exception('Failed to send message to any chat');
	}

} catch (Exception $e) {
	// Log error
	logError('Error processing device request: ' . $e->getMessage(), $data ?? null);

	// Return error response
	http_response_code(500);
	echo json_encode([
		'success' => false,
		'message' => 'Произошла ошибка при отправке запроса. Попробуйте позже.',
		'error' => $e->getMessage() // Remove in production
	]);
}
?>