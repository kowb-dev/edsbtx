<?php
/**
 * Enhanced AJAX Handler for Newsletter with Fixed Email Subjects
 * Version: 2.1.0
 * Date: 2025-07-18
 * Description: Исправленный обработчик с правильными темами писем
 * File: /ajax/forms_handler.php
 */

// Error handling
error_reporting(E_ALL);
ini_set("display_errors", 0);
ini_set("log_errors", 1);

// Set JSON headers immediately
header("Content-Type: application/json; charset=utf-8");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

// Enhanced logging function
function logMessage($message, $data = null) {
    $logFile = __DIR__ . "/newsletter_enhanced.log";
    $timestamp = date("Y-m-d H:i:s");
    $logEntry = "[$timestamp] $message";
    
    if ($data !== null) {
        $logEntry .= " | " . json_encode($data, JSON_UNESCAPED_UNICODE);
    }
    
    $logEntry .= "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}

// Enhanced email encoding function
function encodeEmailHeader($text) {
    // Check if text contains non-ASCII characters
    if (!mb_check_encoding($text, "ASCII")) {
        return "=?UTF-8?B?" . base64_encode($text) . "?=";
    }
    return $text;
}

// Enhanced email sending function
function sendEnhancedEmail($to, $subject, $message, $type = "default") {
    logMessage("Sending enhanced email", [
        "to" => $to,
        "subject" => $subject,
        "type" => $type
    ]);
    
    // Encode subject for proper display
    $encodedSubject = encodeEmailHeader($subject);
    
    // Enhanced headers with proper encoding
    $headers = [
        "From: " . encodeEmailHeader("EDS - Electric Distribution Systems") . " <noreply@edsy.ru>",
        "Reply-To: sales@edsy.ru",
        "Return-Path: noreply@edsy.ru",
        "Content-Type: text/plain; charset=UTF-8",
        "Content-Transfer-Encoding: 8bit",
        "MIME-Version: 1.0",
        "X-Mailer: EDS Newsletter Handler v2.1",
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
    
    // Enhanced message with proper formatting
    $enhancedMessage = $message . "\n\n";
    $enhancedMessage .= "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    $enhancedMessage .= "EDS - Electric Distribution Systems\n";
    $enhancedMessage .= "Отправлено: " . date("d.m.Y H:i:s") . "\n";
    $enhancedMessage .= "Сайт: https://edsy.ru\n";
    if ($type === "admin") {
        $enhancedMessage .= "Панель управления: https://btx.edsy.ru/local/php_interface/admin/newsletter_admin.php\n";
    }
    
    // Send email
    $result = mail($to, $encodedSubject, $enhancedMessage, implode("\r\n", $headers));
    
    logMessage("Email send result", [
        "to" => $to,
        "encoded_subject" => $encodedSubject,
        "result" => $result,
        "last_error" => $result ? null : error_get_last()
    ]);
    
    return $result;
}

// Log request start
logMessage("=== NEW ENHANCED REQUEST ===", [
    "method" => $_SERVER["REQUEST_METHOD"],
    "ip" => $_SERVER["REMOTE_ADDR"] ?? "unknown",
    "user_agent" => $_SERVER["HTTP_USER_AGENT"] ?? "unknown",
    "timestamp" => date("Y-m-d H:i:s")
]);

// Only allow POST requests
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode([
        "success" => false,
        "message" => "Only POST method allowed"
    ]);
    exit;
}

// Get input data
$input = file_get_contents("php://input");
$data = json_decode($input, true);

logMessage("Input data received", ["raw" => $input, "parsed" => $data]);

// Basic validation
if (empty($data) || !isset($data["form_type"])) {
    logMessage("Invalid form data - missing form_type");
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Invalid form data"
    ]);
    exit;
}

// Process newsletter form
if ($data["form_type"] === "newsletter") {
    if (empty($data["email"]) || !filter_var($data["email"], FILTER_VALIDATE_EMAIL)) {
        logMessage("Invalid email address", $data["email"] ?? "empty");
        http_response_code(400);
        echo json_encode([
            "success" => false,
            "message" => "Invalid email address"
        ]);
        exit;
    }

    $email = $data["email"];
    $timestamp = date("Y-m-d H:i:s");
    
    logMessage("Processing newsletter subscription", [
        "email" => $email,
        "timestamp" => $timestamp
    ]);

    // Log the subscription to file
    $logFile = __DIR__ . "/newsletter_logs.txt";
    $logEntry = $timestamp . " - " . $email . " - " . ($_SERVER["REMOTE_ADDR"] ?? "unknown") . "\n";
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);

    // Admin emails
    $adminEmails = [
        "a0123e@ya.ru",
        "ae001t@gmail.com"
    ];

    $emailResults = [];
    $successCount = 0;
    $failureCount = 0;

    // Send admin notifications with enhanced subjects
    foreach ($adminEmails as $adminEmail) {
        logMessage("Sending admin notification", ["admin_email" => $adminEmail]);
        
        $adminSubject = "🔔 Новая подписка на рассылку EDS";
        $adminMessage = "Поступила новая подписка на рассылку!\n\n";
        $adminMessage .= "📧 Email подписчика: " . $email . "\n";
        $adminMessage .= "🕐 Дата подписки: " . date("d.m.Y в H:i:s") . "\n";
        $adminMessage .= "🌐 IP адрес: " . ($_SERVER["REMOTE_ADDR"] ?? "неизвестен") . "\n";
        $adminMessage .= "💻 Браузер: " . ($_SERVER["HTTP_USER_AGENT"] ?? "неизвестен") . "\n";
        $adminMessage .= "🔗 Источник: " . ($_SERVER["HTTP_REFERER"] ?? "прямой переход") . "\n\n";
        $adminMessage .= "Для управления подписчиками используйте админ-панель:\n";
        $adminMessage .= "https://btx.edsy.ru/local/php_interface/admin/newsletter_admin.php\n\n";
        $adminMessage .= "Это автоматическое уведомление системы EDS.";

        $result = sendEnhancedEmail($adminEmail, $adminSubject, $adminMessage, "admin");
        $emailResults["admin_" . $adminEmail] = $result;
        
        if ($result) {
            $successCount++;
        } else {
            $failureCount++;
        }
    }

    // Send welcome email to subscriber with enhanced subject
    logMessage("Sending welcome email", ["subscriber_email" => $email]);
    
    $welcomeSubject = "🎉 Добро пожаловать в рассылку EDS!";
    $welcomeMessage = "Здравствуйте!\n\n";
    $welcomeMessage .= "Спасибо за подписку на рассылку EDS - Electric Distribution Systems!\n\n";
    $welcomeMessage .= "🎯 Теперь вы будете получать:\n";
    $welcomeMessage .= "• 📢 Новости о продуктах и решениях\n";
    $welcomeMessage .= "• 🎁 Специальные предложения и скидки\n";
    $welcomeMessage .= "• 🔧 Технические обновления и инструкции\n";
    $welcomeMessage .= "• 📊 Отраслевые новости и тренды\n\n";
    $welcomeMessage .= "📞 Наши контакты:\n";
    $welcomeMessage .= "📧 Email: sales@edsy.ru\n";
    $welcomeMessage .= "☎️ Телефон: +7 (910) 527-35-38\n";
    $welcomeMessage .= "🌐 Сайт: https://edsy.ru\n\n";
    $welcomeMessage .= "❓ Если у вас есть вопросы или вы хотите отписаться от рассылки,\n";
    $welcomeMessage .= "просто ответьте на это письмо или напишите нам на sales@edsy.ru\n\n";
    $welcomeMessage .= "С уважением,\n";
    $welcomeMessage .= "Команда EDS - Electric Distribution Systems";

    $welcomeResult = sendEnhancedEmail($email, $welcomeSubject, $welcomeMessage, "welcome");
    $emailResults["welcome"] = $welcomeResult;
    
    if ($welcomeResult) {
        $successCount++;
    } else {
        $failureCount++;
    }

    // Prepare response
    $response = [
        "success" => true,
        "data" => [
            "message" => "Подписка оформлена успешно!",
            "email" => $email,
            "timestamp" => $timestamp,
            "emails_sent" => $successCount,
            "emails_failed" => $failureCount,
            "total_emails" => count($emailResults)
        ]
    ];

    // Add warnings if some emails failed
    if ($failureCount > 0) {
        $response["data"]["warnings"] = "Некоторые уведомления могли не дойти (" . $failureCount . " из " . count($emailResults) . ")";
        
        $failedTypes = [];
        foreach ($emailResults as $type => $result) {
            if (!$result) {
                $failedTypes[] = $type;
            }
        }
        $response["data"]["failed_types"] = $failedTypes;
    }

    // Log final response
    logMessage("Sending success response", $response);
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// Handle other form types
logMessage("Unknown form type received", $data["form_type"] ?? "null");
http_response_code(400);
echo json_encode([
    "success" => false,
    "message" => "Unknown form type: " . ($data["form_type"] ?? "null")
]);
exit;
?>