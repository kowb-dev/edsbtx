<?php
/**
 * Bitrix AJAX Component with Mail Events
 * Version: 1.2.0
 * Date: 2025-01-17
 * Description: Компонент с использованием Bitrix mail events
 * File: /local/components/eds/ajax.forms/component_with_mail.php
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Load required modules and classes
CModule::IncludeModule("main");
CModule::IncludeModule("iblock");
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/classes/BitrixMailHelper.php");

/**
 * EDS AJAX Forms Component with Mail Events
 */
class EDSAjaxFormsComponentWithMail extends CBitrixComponent {

    private $logFile;
    private $tableName = "eds_newsletter_subscribers";

    public function __construct($component = null) {
        parent::__construct($component);
        $this->logFile = $_SERVER['DOCUMENT_ROOT'] . '/local/logs/forms_handler.log';
        $this->ensureLogDirectory();
        $this->initNewsletterTable();
    }

    /**
     * Send newsletter emails via Bitrix Events
     */
    private function sendNewsletterEmails($data) {
        try {
            // Send admin notification
            BitrixMailHelper::sendNewsletterEmail($data['email'], 'admin');
            
            // Send welcome email
            BitrixMailHelper::sendNewsletterEmail($data['email'], 'welcome');
            
            $this->logMessage("Newsletter emails sent via Bitrix Events");
            
        } catch (Exception $e) {
            $this->logMessage("Email sending error: " . $e->getMessage());
            throw $e;
        }
    }

    // ... остальные методы остаются такими же
}
