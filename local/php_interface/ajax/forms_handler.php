<?php
/**
 * Enhanced Forms Handler with Database Integration
 * Version: 3.1.0
 * Date: 2025-07-18
 * Description: Обработчик форм с полной интеграцией базы данных
 * File: /local/php_interface/ajax/forms_handler.php
 */

// Security check
if (!defined("B_PROLOG_INCLUDED")) {
	define("B_PROLOG_INCLUDED", true);
}

// Error handling
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

// Set JSON headers
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Load Bitrix core if not loaded
if (!function_exists('CModule')) {
	require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");
}

// Include required modules
CModule::IncludeModule("main");

// Include email template generator
$emailTemplateFile = $_SERVER['DOCUMENT_ROOT'] . '/local/php_interface/classes/EmailTemplateGenerator.php';
if (file_exists($emailTemplateFile)) {
	require_once $emailTemplateFile;
}

/**
 * EDS Forms Handler with Database Integration
 */
class EDSFormsHandler {

	private $logFile;
	private $version = "3.1.0";
	private $templateGenerator;
	private $tableName = "eds_newsletter_subscribers";

	public function __construct() {
		$this->logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/forms_handler.log';
		$this->ensureLogDirectory();
		$this->initDatabase();

		if (class_exists('EmailTemplateGenerator')) {
			$this->templateGenerator = new EmailTemplateGenerator();
		}
	}

	/**
	 * Ensure log directory exists
	 */
	private function ensureLogDirectory() {
		$logDir = dirname($this->logFile);
		if (!is_dir($logDir)) {
			mkdir($logDir, 0755, true);
		}
	}

	/**
	 * Initialize database table
	 */
	private function initDatabase() {
		global $DB;

		$createTableSQL = "
            CREATE TABLE IF NOT EXISTS {$this->tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL,
                status ENUM('active', 'inactive', 'unsubscribed') DEFAULT 'active',
                subscribe_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                unsubscribe_date DATETIME NULL,
                source VARCHAR(100) DEFAULT 'website',
                user_agent TEXT,
                ip_address VARCHAR(45),
                confirmed TINYINT(1) DEFAULT 1,
                UNIQUE KEY unique_email (email),
                INDEX idx_status (status),
                INDEX idx_subscribe_date (subscribe_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

		try {
			$result = $DB->Query($createTableSQL);
			if ($result) {
				$this->logMessage("Database table initialized successfully");
			}
		} catch (Exception $e) {
			$this->logMessage("Database initialization error: " . $e->getMessage());
		}
	}

	/**
	 * Enhanced logging function
	 */
	private function logMessage($message, $data = null) {
		$timestamp = date("Y-m-d H:i:s");
		$logEntry = "[$timestamp] [v{$this->version}] $message";

		if ($data !== null) {
			$logEntry .= " | " . json_encode($data, JSON_UNESCAPED_UNICODE);
		}

		$logEntry .= "\n";
		file_put_contents($this->logFile, $logEntry, FILE_APPEND | LOCK_EX);
	}

	/**
	 * Save subscription to database with proper error handling
	 */
	private function saveSubscriptionToDatabase($email) {
		global $DB;

		$email = $DB->ForSQL($email);
		$ip = $DB->ForSQL($_SERVER['REMOTE_ADDR'] ?? 'unknown');
		$userAgent = $DB->ForSQL($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');
		$source = $DB->ForSQL('website');

		$sql = "
            INSERT INTO {$this->tableName} (email, ip_address, user_agent, source, subscribe_date) 
            VALUES ('{$email}', '{$ip}', '{$userAgent}', '{$source}', NOW())
            ON DUPLICATE KEY UPDATE 
                status = 'active',
                subscribe_date = NOW(),
                unsubscribe_date = NULL,
                ip_address = '{$ip}',
                user_agent = '{$userAgent}'
        ";

		try {
			$result = $DB->Query($sql);
			$this->logMessage("Subscription saved to database", [
				"email" => $email,
				"ip" => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
				"result" => $result ? "success" : "failed"
			]);
			return $result;
		} catch (Exception $e) {
			$this->logMessage("Database save error: " . $e->getMessage(), ["email" => $email]);
			return false;
		}
	}

	/**
	 * Save subscription to text file as backup
	 */
	private function saveSubscriptionToFile($email) {
		$logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/newsletter_subscriptions.txt';
		$logEntry = date('Y-m-d H:i:s') . " - " . $email . " - " . ($_SERVER["REMOTE_ADDR"] ?? "unknown") . "\n";

		try {
			file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
			$this->logMessage("Subscription saved to file", ["email" => $email]);
			return true;
		} catch (Exception $e) {
			$this->logMessage("File save error: " . $e->getMessage(), ["email" => $email]);
			return false;
		}
	}

	/**
	 * Send HTML email
	 */
	private function sendHtmlEmail($to, $subject, $htmlContent, $textContent = '', $type = "default") {
		$this->logMessage("Sending HTML email", [
			"to" => $to,
			"subject" => $subject,
			"type" => $type,
			"html_length" => strlen($htmlContent)
		]);

		// Generate boundary
		$boundary = "EDS-EMAIL-" . md5(time() . rand());

		// Encode subject for proper display
		$encodedSubject = $this->encodeEmailHeader($subject);

		// Enhanced headers for HTML email
		$headers = [
			"From: " . $this->encodeEmailHeader("EDS - Electric Distribution Systems") . " <noreply@edsy.ru>",
			"Reply-To: sales@edsy.ru",
			"Return-Path: noreply@edsy.ru",
			"MIME-Version: 1.0",
			"Content-Type: multipart/alternative; boundary=\"{$boundary}\"",
			"X-Mailer: EDS Forms Handler v{$this->version}",
			"X-Priority: 3",
			"Message-ID: <" . time() . rand(1000, 9999) . "@edsy.ru>",
			"Date: " . date("r"),
			"X-EDS-Type: " . $type
		];

		// Add List-Unsubscribe for welcome emails
		if ($type === "welcome") {
			$headers[] = "List-Unsubscribe: <mailto:sales@edsy.ru?subject=Unsubscribe>";
			$headers[] = "List-Unsubscribe-Post: List-Unsubscribe=One-Click";
		}

		// Create text version if not provided
		if (empty($textContent)) {
			$textContent = $this->htmlToText($htmlContent);
		}

		// Build multipart message
		$message = "--{$boundary}\r\n";
		$message .= "Content-Type: text/plain; charset=UTF-8\r\n";
		$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message .= $textContent . "\r\n\r\n";

		$message .= "--{$boundary}\r\n";
		$message .= "Content-Type: text/html; charset=UTF-8\r\n";
		$message .= "Content-Transfer-Encoding: 8bit\r\n\r\n";
		$message .= $htmlContent . "\r\n\r\n";

		$message .= "--{$boundary}--\r\n";

		// Send email
		$result = mail($to, $encodedSubject, $message, implode("\r\n", $headers));

		$this->logMessage("HTML email send result", [
			"to" => $to,
			"result" => $result,
			"message_length" => strlen($message),
			"last_error" => $result ? null : error_get_last()
		]);

		return $result;
	}

	/**
	 * Convert HTML to text
	 */
	private function htmlToText($html) {
		// Remove HTML tags and decode entities
		$text = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');

		// Clean up whitespace
		$text = preg_replace('/\s+/', ' ', $text);
		$text = trim($text);

		return $text;
	}

	/**
	 * Enhanced email encoding function
	 */
	private function encodeEmailHeader($text) {
		if (!mb_check_encoding($text, "ASCII")) {
			return "=?UTF-8?B?" . base64_encode($text) . "?=";
		}
		return $text;
	}

	/**
	 * Handle newsletter form with proper database integration
	 */
	private function handleNewsletterForm($data) {
		if (empty($data["email"]) || !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
			$this->logMessage("Invalid email address", $data["email"] ?? "empty");
			return [
				"success" => false,
				"message" => "Некорректный email адрес"
			];
		}

		$email = trim($data["email"]);
		$timestamp = date("Y-m-d H:i:s");

		$this->logMessage("Processing newsletter subscription", [
			"email" => $email,
			"timestamp" => $timestamp
		]);

		// Save to database (primary storage)
		$dbSaved = $this->saveSubscriptionToDatabase($email);

		// Save to file (backup storage)
		$fileSaved = $this->saveSubscriptionToFile($email);

		// Check if at least one storage method succeeded
		if (!$dbSaved && !$fileSaved) {
			$this->logMessage("Failed to save subscription", ["email" => $email]);
			return [
				"success" => false,
				"message" => "Ошибка при сохранении подписки. Попробуйте еще раз."
			];
		}

		// Send notification emails
		$emailResults = $this->sendNewsletterEmails($email);

		// Prepare response
		$response = [
			"success" => true,
			"data" => [
				"message" => "Подписка оформлена успешно!",
				"email" => $email,
				"timestamp" => $timestamp,
				"emails_sent" => $emailResults['success_count'],
				"emails_failed" => $emailResults['failure_count'],
				"total_emails" => $emailResults['total_count'],
				"database_saved" => $dbSaved,
				"file_saved" => $fileSaved,
				"version" => $this->version
			]
		];

		// Add warnings if some emails failed
		if ($emailResults['failure_count'] > 0) {
			$response["data"]["warnings"] = "Некоторые уведомления могли не дойти (" . $emailResults['failure_count'] . " из " . $emailResults['total_count'] . ")";
		}

		return $response;
	}

	/**
	 * Send newsletter emails
	 */
	private function sendNewsletterEmails($email) {
		$adminEmails = [
			"a0123e@ya.ru",
			"ae001t@gmail.com"
		];

		$successCount = 0;
		$failureCount = 0;
		$totalCount = 0;

		// Send admin notifications
		foreach ($adminEmails as $adminEmail) {
			$totalCount++;
			$adminSubject = "🔔 Новая подписка на рассылку EDS";

			if ($this->templateGenerator) {
				// Use HTML template if available
				$adminHtmlContent = $this->templateGenerator->generateAdminNotificationEmail($email);
				$adminTextContent = $this->generateAdminTextEmail($email);
				$result = $this->sendHtmlEmail($adminEmail, $adminSubject, $adminHtmlContent, $adminTextContent, "admin");
			} else {
				// Fallback to simple email
				$result = $this->sendSimpleEmail($adminEmail, $adminSubject, $this->generateAdminTextEmail($email));
			}

			if ($result) {
				$successCount++;
			} else {
				$failureCount++;
			}
		}

		// Send welcome email to subscriber
		$totalCount++;
		$welcomeSubject = "🎉 Добро пожаловать в рассылку EDS!";

		if ($this->templateGenerator) {
			// Use HTML template if available
			$welcomeHtmlContent = $this->templateGenerator->generateWelcomeEmail($email);
			$welcomeTextContent = $this->generateWelcomeTextEmail($email);
			$result = $this->sendHtmlEmail($email, $welcomeSubject, $welcomeHtmlContent, $welcomeTextContent, "welcome");
		} else {
			// Fallback to simple email
			$result = $this->sendSimpleEmail($email, $welcomeSubject, $this->generateWelcomeTextEmail($email));
		}

		if ($result) {
			$successCount++;
		} else {
			$failureCount++;
		}

		return [
			'success_count' => $successCount,
			'failure_count' => $failureCount,
			'total_count' => $totalCount
		];
	}

	/**
	 * Send simple email (fallback)
	 */
	private function sendSimpleEmail($to, $subject, $message) {
		$headers = [
			"From: EDS - Electric Distribution Systems <noreply@edsy.ru>",
			"Reply-To: sales@edsy.ru",
			"Content-Type: text/plain; charset=UTF-8",
			"X-Mailer: EDS Forms Handler v{$this->version}"
		];

		return mail($to, $subject, $message, implode("\r\n", $headers));
	}

	/**
	 * Generate admin text email
	 */
	private function generateAdminTextEmail($email) {
		$content = "Новая подписка на рассылку!\n\n";
		$content .= "Email подписчика: " . $email . "\n";
		$content .= "Дата подписки: " . date("d.m.Y в H:i:s") . "\n";
		$content .= "IP адрес: " . ($_SERVER["REMOTE_ADDR"] ?? "неизвестен") . "\n";
		$content .= "Браузер: " . ($_SERVER["HTTP_USER_AGENT"] ?? "неизвестен") . "\n";
		$content .= "Источник: " . ($_SERVER["HTTP_REFERER"] ?? "прямой переход") . "\n\n";
		$content .= "Для управления подписчиками используйте админ-панель:\n";
		$content .= "https://btx.edsy.ru/local/php_interface/admin/newsletter_admin.php\n\n";
		$content .= "Это автоматическое уведомление системы EDS.\n";
		$content .= "Время отправки: " . date('d.m.Y H:i:s');

		return $content;
	}

	/**
	 * Generate welcome text email
	 */
	private function generateWelcomeTextEmail($email) {
		$content = "Здравствуйте!\n\n";
		$content .= "Спасибо за подписку на рассылку EDS - Electric Distribution Systems!\n\n";
		$content .= "Теперь вы будете получать:\n";
		$content .= "• Новости о продуктах и решениях\n";
		$content .= "• Специальные предложения и скидки\n";
		$content .= "• Технические обновления и инструкции\n";
		$content .= "• Отраслевые новости и тренды\n\n";
		$content .= "Наши контакты:\n";
		$content .= "Email: sales@edsy.ru\n";
		$content .= "Телефон: +7 (910) 527-35-38\n";
		$content .= "Сайт: https://edsy.ru\n\n";
		$content .= "Если у вас есть вопросы или вы хотите отписаться от рассылки,\n";
		$content .= "просто ответьте на это письмо или напишите нам на sales@edsy.ru\n\n";
		$content .= "С уважением,\n";
		$content .= "Команда EDS - Electric Distribution Systems";

		return $content;
	}

	/**
	 * Handle contact form
	 */
	private function handleContactForm($data) {
		// Validate required fields
		$requiredFields = ['name', 'email', 'message'];
		foreach ($requiredFields as $field) {
			if (empty($data[$field])) {
				return [
					"success" => false,
					"message" => "Поле '{$field}' обязательно для заполнения"
				];
			}
		}

		// Validate email
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			return [
				"success" => false,
				"message" => "Некорректный email адрес"
			];
		}

		$this->logMessage("Processing contact form", [
			"name" => $data['name'],
			"email" => $data['email']
		]);

		// Here you can add contact form processing logic
		// For example, save to database, send email, etc.

		return [
			"success" => true,
			"data" => [
				"message" => "Сообщение отправлено успешно!"
			]
		];
	}

	/**
	 * Main processing method
	 */
	public function process() {
		$this->logMessage("=== NEW REQUEST ===", [
			"method" => $_SERVER["REQUEST_METHOD"],
			"ip" => $_SERVER["REMOTE_ADDR"] ?? "unknown",
			"user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "unknown"
		]);

		// Only allow POST requests
		if ($_SERVER["REQUEST_METHOD"] !== "POST") {
			http_response_code(405);
			return [
				"success" => false,
				"message" => "Only POST method allowed"
			];
		}

		// Get input data
		$input = file_get_contents("php://input");
		$data = json_decode($input, true);

		$this->logMessage("Input data received", ["raw" => $input, "parsed" => $data]);

		// Basic validation
		if (empty($data) || !isset($data["form_type"])) {
			$this->logMessage("Invalid form data - missing form_type");
			http_response_code(400);
			return [
				"success" => false,
				"message" => "Invalid form data"
			];
		}

		// Route to appropriate handler
		switch ($data["form_type"]) {
			case "newsletter":
				return $this->handleNewsletterForm($data);

			case "contact":
				return $this->handleContactForm($data);

			default:
				$this->logMessage("Unknown form type", $data["form_type"]);
				http_response_code(400);
				return [
					"success" => false,
					"message" => "Unknown form type: " . $data["form_type"]
				];
		}
	}

	/**
	 * Get subscription statistics for admin
	 */
	public function getStatistics() {
		global $DB;

		$stats = ['total' => 0, 'active' => 0, 'today' => 0, 'week' => 0];

		try {
			// Total subscribers
			$result = $DB->Query("SELECT COUNT(*) as total FROM {$this->tableName}");
			if ($result && $row = $result->Fetch()) {
				$stats['total'] = $row['total'];
			}

			// Active subscribers
			$result = $DB->Query("SELECT COUNT(*) as active FROM {$this->tableName} WHERE status = 'active'");
			if ($result && $row = $result->Fetch()) {
				$stats['active'] = $row['active'];
			}

			// Today's subscriptions
			$result = $DB->Query("SELECT COUNT(*) as today FROM {$this->tableName} WHERE DATE(subscribe_date) = CURDATE()");
			if ($result && $row = $result->Fetch()) {
				$stats['today'] = $row['today'];
			}

			// This week's subscriptions
			$result = $DB->Query("SELECT COUNT(*) as week FROM {$this->tableName} WHERE subscribe_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
			if ($result && $row = $result->Fetch()) {
				$stats['week'] = $row['week'];
			}

		} catch (Exception $e) {
			$this->logMessage("Statistics error: " . $e->getMessage());
		}

		return $stats;
	}
}

// Initialize and process
$handler = new EDSFormsHandler();
$result = $handler->process();

// Send response
echo json_encode($result, JSON_UNESCAPED_UNICODE);
exit;
?>