<?php
/**
 * AJAX Component for Forms
 * Version: 2.0.0
 * Date: 2025-07-18
 * Description: Правильная организация AJAX через компоненты Bitrix
 * File: /local/components/eds/ajax.forms/class.php
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Engine\ActionFilter;
use Bitrix\Main\Engine\Controller;
use Bitrix\Main\Engine\Response\AjaxJson;
use Bitrix\Main\Mail\Event;

/**
 * EDS AJAX Forms Controller
 */
class EDSAjaxFormsController extends Controller {

	/**
	 * Configure action filters
	 */
	public function configureActions() {
		return [
			'processForm' => [
				'prefilters' => [
					new ActionFilter\HttpMethod([ActionFilter\HttpMethod::METHOD_POST]),
					new ActionFilter\Csrf(),
				],
				'postfilters' => []
			]
		];
	}

	/**
	 * Process form submission
	 */
	public function processFormAction($formType, $formData) {
		// Validate form type
		$allowedTypes = ['newsletter', 'contact', 'technical_spec', 'consultation'];
		if (!in_array($formType, $allowedTypes)) {
			return new AjaxJson([
				'success' => false,
				'message' => 'Invalid form type'
			]);
		}

		// Route to appropriate handler
		switch ($formType) {
			case 'newsletter':
				return $this->processNewsletterForm($formData);

			case 'contact':
				return $this->processContactForm($formData);

			case 'technical_spec':
				return $this->processTechnicalSpecForm($formData);

			case 'consultation':
				return $this->processConsultationForm($formData);

			default:
				return new AjaxJson([
					'success' => false,
					'message' => 'Handler not implemented'
				]);
		}
	}

	/**
	 * Process newsletter form
	 */
	private function processNewsletterForm($data) {
		// Validate email
		if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			return new AjaxJson([
				'success' => false,
				'message' => 'Некорректный email адрес'
			]);
		}

		$email = $data['email'];

		// Save to database
		$this->saveNewsletterSubscription($email);

		// Send emails
		$emailResults = $this->sendNewsletterEmails($email);

		return new AjaxJson([
			'success' => true,
			'data' => [
				'message' => 'Подписка оформлена успешно!',
				'email' => $email,
				'timestamp' => date('Y-m-d H:i:s'),
				'emails_sent' => $emailResults['success_count'],
				'emails_failed' => $emailResults['failure_count']
			]
		]);
	}

	/**
	 * Process contact form
	 */
	private function processContactForm($data) {
		// Validate required fields
		$requiredFields = ['name', 'email', 'message'];
		foreach ($requiredFields as $field) {
			if (empty($data[$field])) {
				return new AjaxJson([
					'success' => false,
					'message' => "Поле '{$field}' обязательно для заполнения"
				]);
			}
		}

		// Validate email
		if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
			return new AjaxJson([
				'success' => false,
				'message' => 'Некорректный email адрес'
			]);
		}

		// Save to iblock
		$this->saveContactFormData($data);

		// Send notification
		$this->sendContactNotification($data);

		return new AjaxJson([
			'success' => true,
			'data' => [
				'message' => 'Сообщение отправлено успешно!'
			]
		]);
	}

	/**
	 * Save newsletter subscription
	 */
	private function saveNewsletterSubscription($email) {
		global $DB;

		$tableName = "eds_newsletter_subscribers";
		$email = $DB->ForSQL($email);
		$ip = $DB->ForSQL($_SERVER['REMOTE_ADDR'] ?? 'unknown');
		$userAgent = $DB->ForSQL($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

		$sql = "
            INSERT INTO {$tableName} (email, ip_address, user_agent, source) 
            VALUES ('{$email}', '{$ip}', '{$userAgent}', 'website')
            ON DUPLICATE KEY UPDATE 
                status = 'active',
                subscribe_date = CURRENT_TIMESTAMP
        ";

		$DB->Query($sql);
	}

	/**
	 * Send newsletter emails
	 */
	private function sendNewsletterEmails($email) {
		$adminEmails = ['a0123e@ya.ru', 'ae001t@gmail.com'];
		$successCount = 0;
		$failureCount = 0;

		// Send admin notifications
		foreach ($adminEmails as $adminEmail) {
			$result = Event::send([
				'EVENT_NAME' => 'EDS_NEWSLETTER_ADMIN',
				'LID' => SITE_ID,
				'C_FIELDS' => [
					'EMAIL' => $email,
					'ADMIN_EMAIL' => $adminEmail,
					'DATE' => date('d.m.Y H:i:s'),
					'IP' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
					'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
				]
			]);

			if ($result) {
				$successCount++;
			} else {
				$failureCount++;
			}
		}

		// Send welcome email
		$result = Event::send([
			'EVENT_NAME' => 'EDS_NEWSLETTER_WELCOME',
			'LID' => SITE_ID,
			'C_FIELDS' => [
				'EMAIL' => $email,
				'DATE' => date('d.m.Y H:i:s')
			]
		]);

		if ($result) {
			$successCount++;
		} else {
			$failureCount++;
		}

		return [
			'success_count' => $successCount,
			'failure_count' => $failureCount
		];
	}

	/**
	 * Save contact form data to iblock
	 */
	private function saveContactFormData($data) {
		if (!\Bitrix\Main\Loader::includeModule('iblock')) {
			return false;
		}

		$iblockId = $this->getFormIblockId('contact');
		if (!$iblockId) {
			return false;
		}

		$el = new \CIBlockElement;
		$arFields = [
			'IBLOCK_ID' => $iblockId,
			'NAME' => 'Обращение от ' . $data['name'],
			'ACTIVE' => 'Y',
			'PROPERTY_VALUES' => [
				'NAME' => $data['name'],
				'EMAIL' => $data['email'],
				'PHONE' => $data['phone'] ?? '',
				'MESSAGE' => $data['message'],
				'IP' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
				'USER_AGENT' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
			]
		];

		return $el->Add($arFields);
	}

	/**
	 * Get form iblock ID
	 */
	private function getFormIblockId($formType) {
		static $cache = [];

		if (isset($cache[$formType])) {
			return $cache[$formType];
		}

		if (!\Bitrix\Main\Loader::includeModule('iblock')) {
			return false;
		}

		$iblockCode = 'eds_forms_' . $formType;

		$rsIBlock = \CIBlock::GetList([], [
			'TYPE' => 'content',
			'SITE_ID' => SITE_ID,
			'CODE' => $iblockCode
		]);

		if ($arIBlock = $rsIBlock->Fetch()) {
			$cache[$formType] = $arIBlock['ID'];
			return $arIBlock['ID'];
		}

		return false;
	}

	/**
	 * Send contact notification
	 */
	private function sendContactNotification($data) {
		Event::send([
			'EVENT_NAME' => 'EDS_CONTACT_FORM',
			'LID' => SITE_ID,
			'C_FIELDS' => [
				'NAME' => $data['name'],
				'EMAIL' => $data['email'],
				'PHONE' => $data['phone'] ?? '',
				'MESSAGE' => $data['message'],
				'DATE' => date('d.m.Y H:i:s'),
				'IP' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
			]
		]);
	}
}
?>