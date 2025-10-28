<?php
/**
 * Bitrix Mail Helper
 * Version: 1.0.0
 * Date: 2025-01-17
 * Description: Помощник для отправки писем через Bitrix
 * File: /local/php_interface/classes/BitrixMailHelper.php
 */

class BitrixMailHelper {
    
    /**
     * Send email via Bitrix CEvent
     */
    public static function sendNewsletterEmail($email, $type = 'admin') {
        if (!CModule::IncludeModule("main")) {
            return false;
        }

        $eventType = ($type === 'admin') ? 'EDS_NEWSLETTER_ADMIN' : 'EDS_NEWSLETTER_WELCOME';
        
        $arFields = [
            'EMAIL' => $email,
            'DATE' => date('d.m.Y H:i:s'),
            'IP' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'SITE_NAME' => 'EDS - Electric Distribution Systems',
            'SERVER_NAME' => $_SERVER['HTTP_HOST'] ?? 'edsy.ru'
        ];

        return CEvent::Send($eventType, SITE_ID, $arFields);
    }

    /**
     * Create mail event types
     */
    public static function createMailEventTypes() {
        if (!CModule::IncludeModule("main")) {
            return false;
        }

        $et = new CEventType;
        
        // Admin notification event type
        $arFields = [
            'LID' => 'ru',
            'EVENT_NAME' => 'EDS_NEWSLETTER_ADMIN',
            'NAME' => 'Уведомление админа о новой подписке',
            'DESCRIPTION' => 'Отправляется администратору при новой подписке на рассылку',
            'SORT' => 150
        ];
        
        $et->Add($arFields);

        // Welcome email event type
        $arFields = [
            'LID' => 'ru',
            'EVENT_NAME' => 'EDS_NEWSLETTER_WELCOME',
            'NAME' => 'Приветственное письмо подписчику',
            'DESCRIPTION' => 'Отправляется подписчику после оформления подписки',
            'SORT' => 150
        ];
        
        $et->Add($arFields);

        return true;
    }

    /**
     * Create mail templates
     */
    public static function createMailTemplates() {
        if (!CModule::IncludeModule("main")) {
            return false;
        }

        $em = new CEventMessage;

        // Admin notification template
        $arFields = [
            'ACTIVE' => 'Y',
            'EVENT_NAME' => 'EDS_NEWSLETTER_ADMIN',
            'LID' => SITE_ID,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => 'sales@edsy.ru',
            'SUBJECT' => 'Новая подписка на рассылку - EDS',
            'BODY_TYPE' => 'text',
            'MESSAGE' => 'Новая подписка на рассылку:

Email: #EMAIL#
Дата: #DATE#
IP: #IP#
User Agent: #USER_AGENT#

--
Система уведомлений EDS'
        ];
        
        $em->Add($arFields);

        // Welcome email template
        $arFields = [
            'ACTIVE' => 'Y',
            'EVENT_NAME' => 'EDS_NEWSLETTER_WELCOME',
            'LID' => SITE_ID,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL#',
            'SUBJECT' => 'Добро пожаловать в рассылку EDS!',
            'BODY_TYPE' => 'text',
            'MESSAGE' => 'Здравствуйте!

Вы успешно подписались на новости и специальные предложения EDS - Electric Distribution Systems.

Мы будем присылать вам:
• Информацию о новых продуктах
• Специальные предложения
• Технические новости
• Обновления каталога

Если вы хотите отписаться от рассылки, перейдите по ссылке: http://#SERVER_NAME#/unsubscribe/?email=#EMAIL#

С уважением,
Команда EDS - Electric Distribution Systems
Сайт: http://#SERVER_NAME#
Email: sales@edsy.ru
Телефон: +7 (910) 527-35-38'
        ];
        
        $em->Add($arFields);

        return true;
    }

    /**
     * Initialize mail system
     */
    public static function init() {
        self::createMailEventTypes();
        self::createMailTemplates();
        self::createRegistrationEvent();
    }

    /**
     * Send registration confirmation email
     */
    public static function sendRegistrationEmail($userId, $email, $confirmCode) {
        if (!CModule::IncludeModule("main")) {
            return false;
        }

        $arFields = [
            'USER_ID' => $userId,
            'EMAIL' => $email,
            'CONFIRM_CODE' => $confirmCode,
            'SITE_NAME' => 'EDS - Electric Distribution Systems',
            'SERVER_NAME' => $_SERVER['HTTP_HOST'] ?? 'edsy.ru',
        ];

        return CEvent::Send('NEW_USER_CONFIRM', SITE_ID, $arFields);
    }

    /**
     * Create mail event and template for user registration
     */
    public static function createRegistrationEvent() {
        if (!CModule::IncludeModule("main")) {
            return false;
        }

        $et = new CEventType;
        $em = new CEventMessage;

        // User registration confirmation event type
        $et->Add([
            'LID' => 'ru',
            'EVENT_NAME' => 'NEW_USER_CONFIRM',
            'NAME' => 'Подтверждение регистрации нового пользователя',
            'DESCRIPTION' => 'Отправляется после регистрации пользователя для подтверждения email',
            'SORT' => 1,
        ]);

        // User registration confirmation template
        $em->Add([
            'ACTIVE' => 'Y',
            'EVENT_NAME' => 'NEW_USER_CONFIRM',
            'LID' => SITE_ID,
            'EMAIL_FROM' => '#DEFAULT_EMAIL_FROM#',
            'EMAIL_TO' => '#EMAIL#',
            'SUBJECT' => 'Подтверждение регистрации на #SITE_NAME#',
            'BODY_TYPE' => 'html',
            'MESSAGE' => '<h1>Здравствуйте!</h1>
<p>Вы успешно зарегистрировались на сайте #SITE_NAME#.</p>
<p>Для подтверждения вашей регистрации, пожалуйста, перейдите по ссылке:</p>
<p><a href="http://#SERVER_NAME#/auth/?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#">http://#SERVER_NAME#/auth/?confirm_registration=yes&confirm_user_id=#USER_ID#&confirm_code=#CONFIRM_CODE#</a></p>
<hr>
<p>С уважением,<br>Администрация сайта</p>',
        ]);

        return true;
    }
}

// Auto-initialize if called directly
if (defined('BITRIX_MAIL_HELPER_INIT')) {
    BitrixMailHelper::init();
}
?>