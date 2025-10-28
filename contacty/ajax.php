<?php
/**
 * Contact Form Handler - Fixed Version
 * Version: 1.1.0
 * Date: 2025-07-17
 * Description: Исправленный Ajax обработчик для формы обратной связи
 */

// Подключаем ядро Bitrix
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

// Проверяем, что это Ajax запрос
if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest') {
	die('Access denied');
}

// Проверяем метод запроса
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	die('Method not allowed');
}

// Загружаем модули
CModule::IncludeModule("main");

// Функция для отправки JSON ответа
function sendJsonResponse($success, $message = '', $data = []) {
	header('Content-Type: application/json; charset=utf-8');
	echo json_encode([
		'success' => $success,
		'message' => $message,
		'data' => $data
	], JSON_UNESCAPED_UNICODE);
	exit;
}

// Функция для логирования ошибок
function logError($message) {
	$logFile = $_SERVER["DOCUMENT_ROOT"] . "/upload/logs/contact_form_errors.log";
	$logDir = dirname($logFile);
	if (!is_dir($logDir)) {
		mkdir($logDir, 0755, true);
	}

	$timestamp = date('Y-m-d H:i:s');
	$logMessage = "[{$timestamp}] {$message}" . PHP_EOL;
	file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

try {
	// Валидация токена и временной метки
	$formToken = $_POST['form_token'] ?? '';
	$timestamp = $_POST['timestamp'] ?? '';

	if (empty($formToken) || empty($timestamp)) {
		sendJsonResponse(false, 'Ошибка безопасности. Обновите страницу и попробуйте снова.');
	}

	// Проверка времени отправки (защита от спама)
	$currentTime = time();
	if ($currentTime - (int)$timestamp < 3) {
		sendJsonResponse(false, 'Слишком быстрая отправка. Подождите немного.');
	}

	if ($currentTime - (int)$timestamp > 1800) { // 30 минут
		sendJsonResponse(false, 'Форма устарела. Обновите страницу.');
	}

	// Получение и валидация данных формы
	$firstName = trim($_POST['first_name'] ?? '');
	$lastName = trim($_POST['last_name'] ?? '');
	$email = trim($_POST['email'] ?? '');
	$phone = trim($_POST['phone'] ?? '');
	$message = trim($_POST['message'] ?? '');

	// Валидация обязательных полей
	$errors = [];

	if (empty($firstName)) {
		$errors[] = 'Имя обязательно для заполнения';
	} elseif (!preg_match('/^[a-zA-Zа-яёА-ЯЁ\s\-\']{2,}$/u', $firstName)) {
		$errors[] = 'Некорректное имя';
	}

	if (empty($lastName)) {
		$errors[] = 'Фамилия обязательна для заполнения';
	} elseif (!preg_match('/^[a-zA-Zа-яёА-ЯЁ\s\-\']{2,}$/u', $lastName)) {
		$errors[] = 'Некорректная фамилия';
	}

	if (empty($email)) {
		$errors[] = 'Email обязателен для заполнения';
	} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		$errors[] = 'Некорректный email адрес';
	}

	if (empty($message)) {
		$errors[] = 'Сообщение обязательно для заполнения';
	} elseif (strlen($message) < 10) {
		$errors[] = 'Сообщение слишком короткое (минимум 10 символов)';
	}

	// Валидация телефона (если указан)
	if (!empty($phone) && !preg_match('/^\+[\d\s\(\)\-]{10,}$/', $phone)) {
		$errors[] = 'Некорректный номер телефона';
	}

	if (!empty($errors)) {
		sendJsonResponse(false, 'Ошибки валидации: ' . implode(', ', $errors));
	}

	// Защита от спама
	$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
	$referrer = $_SERVER['HTTP_REFERER'] ?? '';

	// Проверка рефферера
	$allowedDomains = ['edsy.ru', 'www.edsy.ru', 'btx.edsy.ru'];
	$referrerHost = parse_url($referrer, PHP_URL_HOST);
	if (!in_array($referrerHost, $allowedDomains)) {
		logError("Suspicious referrer: {$referrer} from IP: " . $_SERVER['REMOTE_ADDR']);
		sendJsonResponse(false, 'Ошибка безопасности');
	}

	// Сохранение заявки в базу данных
	$contactData = [
		'FIRST_NAME' => $firstName,
		'LAST_NAME' => $lastName,
		'EMAIL' => $email,
		'PHONE' => $phone,
		'MESSAGE' => $message,
		'USER_AGENT' => $userAgent,
		'IP_ADDRESS' => $_SERVER['REMOTE_ADDR'],
		'REFERRER' => $referrer,
		'FORM_TOKEN' => $formToken,
		'CREATED_AT' => date('Y-m-d H:i:s')
	];

	// Сохранение в кастомную таблицу (создать при необходимости)
	$contactId = saveContactToDatabase($contactData);

	// Отправка уведомлений
	$emailSent = sendNotificationEmails($contactData, $contactId);

	if ($emailSent) {
		// Логирование успешной отправки
		logError("Contact form submitted successfully. ID: {$contactId}, Email: {$email}");

		sendJsonResponse(true, 'Сообщение отправлено успешно! Мы свяжемся с вами в ближайшее время.');
	} else {
		logError("Failed to send email notification. Contact ID: {$contactId}");
		sendJsonResponse(false, 'Ошибка отправки уведомления. Попробуйте позвонить нам.');
	}

} catch (Exception $e) {
	logError("Exception in contact form: " . $e->getMessage());
	sendJsonResponse(false, 'Произошла техническая ошибка. Попробуйте позже.');
}

/**
 * Сохранение контакта в базу данных
 */
function saveContactToDatabase($data) {
	global $DB;

	// Создаем таблицу если не существует
	$createTableSQL = "
        CREATE TABLE IF NOT EXISTS b_edsys_contacts (
            ID int(11) NOT NULL AUTO_INCREMENT,
            FIRST_NAME varchar(255) NOT NULL,
            LAST_NAME varchar(255) NOT NULL,
            EMAIL varchar(255) NOT NULL,
            PHONE varchar(50) DEFAULT NULL,
            MESSAGE text NOT NULL,
            USER_AGENT text,
            IP_ADDRESS varchar(45),
            REFERRER varchar(500),
            FORM_TOKEN varchar(255),
            CREATED_AT datetime NOT NULL,
            STATUS enum('NEW','PROCESSING','COMPLETED') DEFAULT 'NEW',
            PRIMARY KEY (ID),
            KEY IX_EMAIL (EMAIL),
            KEY IX_CREATED_AT (CREATED_AT),
            KEY IX_STATUS (STATUS)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";

	$DB->Query($createTableSQL);

	// Вставляем данные
	$insertSQL = "
        INSERT INTO b_edsys_contacts 
        (FIRST_NAME, LAST_NAME, EMAIL, PHONE, MESSAGE, USER_AGENT, IP_ADDRESS, REFERRER, FORM_TOKEN, CREATED_AT) 
        VALUES 
        ('" . $DB->ForSQL($data['FIRST_NAME']) . "', 
         '" . $DB->ForSQL($data['LAST_NAME']) . "', 
         '" . $DB->ForSQL($data['EMAIL']) . "', 
         '" . $DB->ForSQL($data['PHONE']) . "', 
         '" . $DB->ForSQL($data['MESSAGE']) . "', 
         '" . $DB->ForSQL($data['USER_AGENT']) . "', 
         '" . $DB->ForSQL($data['IP_ADDRESS']) . "', 
         '" . $DB->ForSQL($data['REFERRER']) . "', 
         '" . $DB->ForSQL($data['FORM_TOKEN']) . "', 
         '" . $DB->ForSQL($data['CREATED_AT']) . "')
    ";

	$result = $DB->Query($insertSQL);

	if ($result) {
		return $DB->LastID();
	}

	return false;
}

/**
 * Отправка уведомлений по email
 */
function sendNotificationEmails($data, $contactId) {
	// Email для администратора и дублирования
	$adminEmails = [
		'sales@edsy.ru',
		'a0123e@ya.ru',
		'ae001t@gmail.com'
	];

	$adminSubject = 'Сообщение со страницы Контакты - ' . $data['FIRST_NAME'] . ' ' . $data['LAST_NAME'];

	// Шаблон письма для администратора
	$adminMessage = buildAdminEmailTemplate($data, $contactId);

	// Отправка администраторам
	$adminSent = true;
	foreach ($adminEmails as $adminEmail) {
		$headers = buildEmailHeaders('EDS <noreply@edsy.ru>', $data['EMAIL']);

		// Кодируем тему для корректного отображения
		$encodedAdminSubject = '=?UTF-8?B?' . base64_encode($adminSubject) . '?=';

		$sent = mail($adminEmail, $encodedAdminSubject, $adminMessage, $headers);

		if (!$sent) {
			$adminSent = false;
			logError("Failed to send email to admin: {$adminEmail}");
		} else {
			logError("Email sent successfully to admin: {$adminEmail} with subject: {$adminSubject}");
		}
	}

	// Автоответ клиенту
	$clientSubject = 'Спасибо за обращение в EDS - Electric Distribution Systems';
	$clientMessage = buildClientEmailTemplate($data);
	$clientHeaders = buildEmailHeaders('EDS Support <noreply@edsy.ru>', 'sales@edsy.ru');

	// Кодируем тему для клиента
	$encodedClientSubject = '=?UTF-8?B?' . base64_encode($clientSubject) . '?=';

	$clientSent = mail($data['EMAIL'], $encodedClientSubject, $clientMessage, $clientHeaders);

	if (!$clientSent) {
		logError("Failed to send email to client: {$data['EMAIL']}");
	} else {
		logError("Email sent successfully to client: {$data['EMAIL']} with subject: {$clientSubject}");
	}

	return $adminSent && $clientSent;
}

/**
 * Построение заголовков для email
 */
function buildEmailHeaders($from, $replyTo) {
	return "From: {$from}\r\n" .
	       "Reply-To: {$replyTo}\r\n" .
	       "Content-Type: text/html; charset=UTF-8\r\n" .
	       "MIME-Version: 1.0\r\n" .
	       "X-Mailer: PHP/" . phpversion() . "\r\n" .
	       "X-Originating-Domain: btx.edsy.ru\r\n" .
	       "X-Priority: 3\r\n";
}

/**
 * Шаблон письма для администратора
 */
function buildAdminEmailTemplate($data, $contactId) {
	$phoneBlock = !empty($data['PHONE']) ?
		"<p><strong>Телефон:</strong> <a href='tel:" . htmlspecialchars($data['PHONE']) . "'>" . htmlspecialchars($data['PHONE']) . "</a></p>" :
		"<p><strong>Телефон:</strong> не указан</p>";

	return "
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Новая заявка с сайта EDS</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;'>
                    <tr>
                        <td style='padding: 30px;'>
                            <h1 style='color: #ff2545; border-bottom: 2px solid #ff2545; padding-bottom: 10px; margin-top: 0;'>
                                Новая заявка с сайта EDS
                            </h1>
                            
                            <table width='100%' cellpadding='15' cellspacing='0' style='background: #f9f9f9; border-radius: 5px; margin: 20px 0;'>
                                <tr>
                                    <td>
                                        <h3 style='margin-top: 0; color: #333;'>Контактная информация:</h3>
                                        <p><strong>Имя:</strong> " . htmlspecialchars($data['FIRST_NAME']) . "</p>
                                        <p><strong>Фамилия:</strong> " . htmlspecialchars($data['LAST_NAME']) . "</p>
                                        <p><strong>Email:</strong> <a href='mailto:" . htmlspecialchars($data['EMAIL']) . "'>" . htmlspecialchars($data['EMAIL']) . "</a></p>
                                        {$phoneBlock}
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='15' cellspacing='0' style='background: #f0f8ff; border-radius: 5px; border-left: 4px solid #0066cc; margin: 20px 0;'>
                                <tr>
                                    <td>
                                        <h3 style='margin-top: 0; color: #333;'>Сообщение:</h3>
                                        <p style='white-space: pre-wrap;'>" . htmlspecialchars($data['MESSAGE']) . "</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='10' cellspacing='0' style='background: #f5f5f5; border-radius: 5px; margin: 20px 0;'>
                                <tr>
                                    <td style='font-size: 12px; color: #666;'>
                                        <p><strong>Дополнительная информация:</strong></p>
                                        <p>ID заявки: {$contactId}</p>
                                        <p>Дата: " . date('d.m.Y H:i:s') . "</p>
                                        <p>Сайт: <a href='https://btx.edsy.ru/'>https://btx.edsy.ru/</a></p>
                                        <p>IP адрес: " . htmlspecialchars($data['IP_ADDRESS']) . "</p>
                                        <p>Реферер: " . htmlspecialchars($data['REFERRER']) . "</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='0' cellspacing='0' style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                                <tr>
                                    <td align='center'>
                                        <a href='mailto:" . htmlspecialchars($data['EMAIL']) . "' 
                                           style='display: inline-block; padding: 12px 24px; background: #ff2545; color: white; text-decoration: none; border-radius: 5px; font-weight: bold;'>
                                            Ответить клиенту
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
}

/**
 * Шаблон письма для клиента
 */
function buildClientEmailTemplate($data) {
	$phoneBlock = !empty($data['PHONE']) ?
		"<p><strong>Телефон:</strong> " . htmlspecialchars($data['PHONE']) . "</p>" : "";

	return "
<!DOCTYPE html>
<html lang='ru'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Спасибо за обращение в EDS</title>
</head>
<body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0;'>
    <table width='100%' cellpadding='0' cellspacing='0' style='background-color: #f4f4f4; padding: 20px;'>
        <tr>
            <td align='center'>
                <table width='600' cellpadding='0' cellspacing='0' style='background-color: #ffffff; border: 1px solid #ddd; border-radius: 8px;'>
                    <tr>
                        <td style='padding: 30px;'>
                            <table width='100%' cellpadding='0' cellspacing='0' style='margin-bottom: 30px;'>
                                <tr>
                                    <td align='center'>
                                        <h1 style='color: #ff2545; margin-bottom: 10px; font-size: 28px;'>EDS</h1>
                                        <p style='color: #666; margin: 0; font-size: 16px;'>Electric Distribution Systems</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <h2 style='color: #333; margin-bottom: 20px;'>Здравствуйте, " . htmlspecialchars($data['FIRST_NAME']) . "!</h2>
                            
                            <p style='margin-bottom: 20px; font-size: 16px;'>
                                Спасибо за ваше обращение. Мы получили ваше сообщение и свяжемся с вами в ближайшее время.
                            </p>
                            
                            <table width='100%' cellpadding='15' cellspacing='0' style='background: #f9f9f9; border-radius: 5px; margin: 20px 0;'>
                                <tr>
                                    <td>
                                        <h3 style='margin-top: 0; color: #333;'>Ваши данные:</h3>
                                        <p><strong>Имя:</strong> " . htmlspecialchars($data['FIRST_NAME']) . " " . htmlspecialchars($data['LAST_NAME']) . "</p>
                                        <p><strong>Email:</strong> " . htmlspecialchars($data['EMAIL']) . "</p>
                                        {$phoneBlock}
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='15' cellspacing='0' style='background: #f0f8ff; border-radius: 5px; border-left: 4px solid #0066cc; margin: 20px 0;'>
                                <tr>
                                    <td>
                                        <h3 style='margin-top: 0; color: #333;'>Ваше сообщение:</h3>
                                        <p style='white-space: pre-wrap;'>" . htmlspecialchars($data['MESSAGE']) . "</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='15' cellspacing='0' style='background: #fff8dc; border-radius: 5px; border-left: 4px solid #ffcc00; margin: 20px 0;'>
                                <tr>
                                    <td>
                                        <h3 style='margin-top: 0; color: #333;'>Наши контакты:</h3>
                                        <p><strong>Телефон:</strong> +7 (4842) 20-74-16, +7 (910) 527-35-38</p>
                                        <p><strong>Email:</strong> sales@edsy.ru</p>
                                        <p><strong>Адрес:</strong> 248009, г. Калуга, Грабцевский пр., 17А</p>
                                        <p><strong>Время работы:</strong> Пн-Пт: 09:00-19:00</p>
                                    </td>
                                </tr>
                            </table>
                            
                            <table width='100%' cellpadding='0' cellspacing='0' style='margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd;'>
                                <tr>
                                    <td align='center'>
                                        <p style='color: #666; font-size: 14px; margin: 0;'>
                                            С уважением,<br>
                                            Команда EDS - Electric Distribution Systems
                                        </p>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>";
}

?>