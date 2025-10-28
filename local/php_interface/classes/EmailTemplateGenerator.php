<?php
/**
 * Fixed HTML Email Template Generator
 * Version: 1.2.0
 * Date: 2025-07-18
 * Description: Исправленный генератор HTML шаблонов для всех почтовых клиентов
 * File: /local/php_interface/classes/EmailTemplateGenerator.php
 */

class EmailTemplateGenerator {

	private $logoSvg;

	public function __construct() {
		$this->logoSvg = '<svg viewBox="0 0 734 734" xmlns="http://www.w3.org/2000/svg" xml:space="preserve">
            <path d="M151.662 194.22c-30.267 2.8-62 9.866-79.866 17.733-33.6 14.933-51.734 44.933-45.734 76.133 5.067 26.667 19.334 50.934 45.6 77.467 8.934 9.067 16.8 16.533 17.467 16.533s1.333-17.466 1.6-41.066l.4-40.934 162.933-.4c114.533-.266 165.6.134 171.867 1.2 45.2 7.467 70.933 59.334 49.6 99.867-2.134 4-4 7.733-4.267 8.267-.267.666 38 1.066 85.067 1.066 93.466 0 91.333.134 94.533-7.733 2-4.667.8-9.867-2.8-13.2-2.533-2.267-7.2-2.4-66.933-3.067l-64.134-.666-8.933-4.4c-9.333-4.534-14.267-9.6-19.867-20.267-2.666-5.067-3.2-8-3.2-18 0-10.933.4-12.667 4.134-19.333 5.333-9.2 14-16.667 23.6-20 6.533-2.4 11.066-2.667 35.866-2.667h28.4l-9.333-5.467c-12-7.066-47.333-24.8-63.467-32-76.8-33.866-160.8-56.666-244.533-66.533-27.467-3.2-86-4.667-108-2.533"/>
            <path d="M120.329 330.086v24h157.733l-.8 3.6c-.4 2.134-2.667 8.934-4.933 15.334l-4.267 11.733H120.329v11.2c0 7.733.533 11.333 1.733 11.733.8.4 37.867.534 82.267.534l80.666-.134v-77.333l-82.266-.4zm196 .667v78.133l53.066-.4 52.934-.4 7.6-3.733c13.733-6.667 21.2-18.267 22.133-34.267.533-8.4.133-11.333-2.4-17.066-3.467-8-10.933-15.334-19.867-19.467-6-2.667-7.733-2.8-59.733-2.8zm202 4c-5.2 5.067-4.934 11.733.8 16.933l3.866 3.734h62.667c38.133 0 64 .533 66.267 1.333 3.466 1.2 3.333.933-1.6-3.467-2.934-2.666-10.134-8.666-16-13.333l-10.534-8.533H521.529zm162.666 63.067c0 9.6-.666 12.666-3.2 17.6-5.333 10.133-10.533 15.733-18.533 19.6l-7.6 3.733H412.729c-131.467 0-239.067.267-239.067.667 0 1.066 25.2 14.8 44 24.133 59.733 29.466 121.067 49.466 197.333 64.533 110.934 21.867 212.934 13.467 262.134-21.467 32.533-22.933 40-56.666 20.933-94.266-4.667-9.333-14-24.133-16-25.333-.667-.4-1.067 4.533-1.067 10.8"/>
        </svg>';
	}

	/**
	 * Generate light theme welcome email - fixed for all mail clients
	 */
	public function generateWelcomeEmail($userEmail = '') {
		$html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
    <title>Добро пожаловать в рассылку EDS!</title>
    <style>
        /* Reset styles for email clients */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        /* Force light theme for all clients */
        html, body {
            background-color: #ffffff !important;
            color: #333333 !important;
            color-scheme: light !important;
        }
        
        /* Prevent dark mode override */
        @media (prefers-color-scheme: dark) {
            html, body {
                background-color: #ffffff !important;
                color: #333333 !important;
            }
            .email-container {
                background-color: #ffffff !important;
                color: #333333 !important;
            }
            .email-header {
                background-color: #ffffff !important;
                color: #333333 !important;
            }
            .email-content {
                background-color: #ffffff !important;
                color: #333333 !important;
            }
            .benefits, .contact-info {
                background-color: #f8f9fa !important;
                color: #333333 !important;
            }
            .footer {
                background-color: #2d3748 !important;
                color: #ffffff !important;
            }
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, "Helvetica Neue", sans-serif !important;
            line-height: 1.6 !important;
            color: #333333 !important;
            background-color: #ffffff !important;
            padding: 20px !important;
            margin: 0 !important;
            width: 100% !important;
            -webkit-text-size-adjust: 100% !important;
            -ms-text-size-adjust: 100% !important;
        }
        
        .email-container {
            background-color: #ffffff !important;
            border-radius: 16px !important;
            border: 2px solid #e9ecef !important;
            overflow: hidden !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            width: 100% !important;
            display: block !important;
        }
        
        .email-header {
            background-color: #ffffff !important;
            border-bottom: 3px solid #ff2545 !important;
            padding: 30px 20px !important;
            text-align: center !important;
            color: #333333 !important;
        }
        
        .logo {
            width: 90px !important;
            height: 90px !important;
            margin: 0 auto 20px !important;
            display: block !important;
        }
        
        .logo svg {
            width: 100% !important;
            height: 100% !important;
            fill: #ff2545 !important;
        }
        
        .welcome-title {
            font-size: 28px !important;
            font-weight: 700 !important;
            margin-bottom: 10px !important;
            color: #333333 !important;
            line-height: 1.2 !important;
        }
        
        .welcome-subtitle {
            font-size: 16px !important;
            color: #666666 !important;
            margin-bottom: 0 !important;
        }
        
        .email-content {
            padding: 30px 20px !important;
            background-color: #ffffff !important;
            color: #333333 !important;
        }
        
        .greeting {
            font-size: 18px !important;
            color: #333333 !important;
            margin-bottom: 25px !important;
            text-align: center !important;
            line-height: 1.5 !important;
        }
        
        .benefits {
            background-color: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 25px !important;
            margin: 20px 0 !important;
            position: relative !important;
        }
        
        .benefits h3 {
            color: #ff2545 !important;
            font-size: 20px !important;
            margin-bottom: 15px !important;
            text-align: center !important;
            font-weight: 600 !important;
        }
        
        .benefits-list {
            list-style: none !important;
            padding: 0 !important;
            margin: 0 !important;
        }
        
        .benefits-list li {
            padding: 8px 0 !important;
            border-bottom: 1px solid #e9ecef !important;
            font-size: 16px !important;
            color: #333333 !important;
            line-height: 1.4 !important;
            position: relative !important;
            padding-left: 25px !important;
        }
        
        .benefits-list li:last-child {
            border-bottom: none !important;
        }
        
        .benefits-list li:before {
            content: "✓" !important;
            color: #ff2545 !important;
            font-weight: bold !important;
            position: absolute !important;
            left: 0 !important;
            top: 8px !important;
            font-size: 18px !important;
        }
        
        .contact-info {
            background-color: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-radius: 12px !important;
            padding: 25px !important;
            margin: 20px 0 !important;
            color: #333333 !important;
            text-align: center !important;
        }
        
        .contact-info h3 {
            font-size: 20px !important;
            margin-bottom: 15px !important;
            color: #333333 !important;
            font-weight: 600 !important;
        }
        
        .contact-item {
            margin: 12px 0 !important;
            font-size: 16px !important;
            color: #333333 !important;
        }
        
        .contact-item a {
            color: #ff2545 !important;
            text-decoration: none !important;
        }
        
        .contact-combined {
            display: block !important;
            margin: 15px 0 !important;
            font-size: 16px !important;
            color: #333333 !important;
        }
        
        .contact-combined a {
            color: #ff2545 !important;
            text-decoration: none !important;
            margin: 0 15px !important;
        }
        
        .footer {
            background-color: #2d3748 !important;
            color: #ffffff !important;
            padding: 25px 20px !important;
            text-align: center !important;
        }
        
        .footer h4 {
            font-size: 18px !important;
            margin-bottom: 10px !important;
            color: #ff2545 !important;
            font-weight: 600 !important;
        }
        
        .footer p {
            font-size: 14px !important;
            color: #ffffff !important;
            line-height: 1.4 !important;
            margin: 0 0 15px 0 !important;
        }
        
        .social-links {
            margin-top: 15px !important;
        }
        
        .social-links a {
            display: inline-block !important;
            margin: 0 10px !important;
            color: #ffffff !important;
            text-decoration: none !important;
            font-size: 14px !important;
        }
        
        /* Mobile responsiveness */
        @media screen and (max-width: 600px) {
            .email-container {
                border-radius: 8px !important;
                margin: 0 !important;
                width: 100% !important;
            }
            
            .email-header {
                padding: 20px 15px !important;
            }
            
            .welcome-title {
                font-size: 24px !important;
            }
            
            .welcome-subtitle {
                font-size: 14px !important;
            }
            
            .email-content {
                padding: 20px 15px !important;
            }
            
            .benefits, .contact-info {
                padding: 20px !important;
                margin: 15px 0 !important;
            }
            
            .footer {
                padding: 20px 15px !important;
            }
        }
        
        /* Outlook specific fixes */
        .outlook-fix {
            mso-table-lspace: 0pt !important;
            mso-table-rspace: 0pt !important;
        }
        
        /* Gmail specific fixes */
        .gmail-fix {
            color: #333333 !important;
            background-color: #ffffff !important;
        }
    </style>
    <!--[if mso]>
    <style>
        .email-container {
            width: 600px !important;
        }
    </style>
    <![endif]-->
</head>
<body class="gmail-fix">
    <div class="email-container outlook-fix">
        <div class="email-header">
            <div class="logo">' . $this->logoSvg . '</div>
            <h1 class="welcome-title">Добро пожаловать!</h1>
            <p class="welcome-subtitle">Спасибо за подписку на рассылку EDS</p>
        </div>
        
        <div class="email-content">
            <div class="greeting">
                <strong>Здравствуйте!</strong><br><br>
                Спасибо за подписку на рассылку EDS - Electric Distribution Systems!
            </div>
            
            <div class="benefits">
                <h3>Теперь вы будете получать:</h3>
                <ul class="benefits-list">
                    <li>Новости о продуктах и решениях</li>
                    <li>Специальные предложения и скидки</li>
                    <li>Технические обновления и инструкции</li>
                    <li>Отраслевые новости и тренды</li>
                </ul>
            </div>
            
            <div class="contact-info">
                <h3>Наши контакты</h3>
                <div class="contact-item">
                    <strong>Телефон:</strong> <a href="tel:+79105273538">+7 (910) 527-35-38</a>
                </div>
                <div class="contact-combined">
                    <a href="mailto:sales@edsy.ru">sales@edsy.ru</a>
                    <a href="https://edsy.ru">edsy.ru</a>
                </div>
            </div>
        </div>
        
        <div class="footer">
            <h4>EDS - Electric Distribution Systems</h4>
            <p>
                Профессиональные решения для электроснабжения<br>
                Качество, надежность, инновации
            </p>
            <div class="social-links">
                <a href="https://edsy.ru" target="_blank">🌐 Сайт</a>
                <a href="mailto:sales@edsy.ru">📧 Email</a>
                <a href="tel:+79105273538">📞 Телефон</a>
            </div>
        </div>
    </div>
</body>
</html>';

		return $html;
	}

	/**
	 * Generate admin notification email HTML - fixed version
	 */
	public function generateAdminNotificationEmail($subscriberEmail, $subscriptionData = []) {
		$html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="color-scheme" content="light only">
    <meta name="supported-color-schemes" content="light">
    <title>Новая подписка на рассылку EDS</title>
    <style>
        /* Reset and force light theme */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        html, body {
            background-color: #ffffff !important;
            color: #333333 !important;
            color-scheme: light !important;
        }
        
        @media (prefers-color-scheme: dark) {
            html, body, .email-container, .email-header, .email-content, .info-table {
                background-color: #ffffff !important;
                color: #333333 !important;
            }
        }
        
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif !important;
            line-height: 1.6 !important;
            color: #333333 !important;
            background-color: #ffffff !important;
            padding: 20px !important;
            margin: 0 !important;
            width: 100% !important;
        }
        
        .email-container {
            background-color: #ffffff !important;
            border-radius: 10px !important;
            border: 2px solid #e9ecef !important;
            overflow: hidden !important;
            max-width: 600px !important;
            margin: 0 auto !important;
            width: 100% !important;
        }
        
        .email-header {
            background-color: #ffffff !important;
            border-bottom: 3px solid #ff2545 !important;
            color: #333333 !important;
            padding: 25px 20px !important;
            text-align: center !important;
        }
        
        .logo {
            width: 50px !important;
            height: 50px !important;
            margin: 0 auto 15px !important;
            display: block !important;
        }
        
        .logo svg {
            width: 100% !important;
            height: 100% !important;
            fill: #ff2545 !important;
        }
        
        .email-content {
            padding: 30px 20px !important;
            background-color: #ffffff !important;
            color: #333333 !important;
        }
        
        .info-table {
            background-color: #f8f9fa !important;
            border: 2px solid #e9ecef !important;
            border-radius: 8px !important;
            padding: 25px !important;
            margin: 20px 0 !important;
        }
        
        .info-row {
            display: block !important;
            padding: 10px 0 !important;
            border-bottom: 1px solid #e9ecef !important;
            color: #333333 !important;
        }
        
        .info-row:last-child {
            border-bottom: none !important;
        }
        
        .info-label {
            font-weight: bold !important;
            color: #333333 !important;
            display: block !important;
            margin-bottom: 5px !important;
        }
        
        .info-value {
            color: #666666 !important;
            word-break: break-all !important;
        }
        
        .action-button {
            background-color: #ff2545 !important;
            color: #ffffff !important;
            padding: 15px 30px !important;
            border-radius: 6px !important;
            text-decoration: none !important;
            display: inline-block !important;
            margin: 20px 0 !important;
            font-weight: bold !important;
        }
        
        .footer-text {
            margin-top: 30px !important;
            font-size: 14px !important;
            color: #666666 !important;
            border-top: 1px solid #e9ecef !important;
            padding-top: 20px !important;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <div class="logo">' . $this->logoSvg . '</div>
            <h1>🔔 Новая подписка</h1>
            <p>Поступила новая подписка на рассылку EDS</p>
        </div>
        
        <div class="email-content">
            <h2>Информация о подписчике</h2>
            
            <div class="info-table">
                <div class="info-row">
                    <div class="info-label">📧 Email:</div>
                    <div class="info-value">' . htmlspecialchars($subscriberEmail) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🕐 Дата подписки:</div>
                    <div class="info-value">' . date('d.m.Y в H:i:s') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🌐 IP адрес:</div>
                    <div class="info-value">' . ($_SERVER['REMOTE_ADDR'] ?? 'неизвестен') . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">💻 Браузер:</div>
                    <div class="info-value">' . htmlspecialchars(substr($_SERVER['HTTP_USER_AGENT'] ?? 'неизвестен', 0, 100)) . '</div>
                </div>
                <div class="info-row">
                    <div class="info-label">🔗 Источник:</div>
                    <div class="info-value">' . htmlspecialchars($_SERVER['HTTP_REFERER'] ?? 'прямой переход') . '</div>
                </div>
            </div>
            
            <div style="text-align: center;">
                <a href="https://btx.edsy.ru/local/php_interface/admin/newsletter_admin.php" class="action-button">
                    Управление подписчиками
                </a>
            </div>
            
            <div class="footer-text">
                Это автоматическое уведомление системы EDS.<br>
                Время отправки: ' . date('d.m.Y H:i:s') . '
            </div>
        </div>
    </div>
</body>
</html>';

		return $html;
	}
}
?>