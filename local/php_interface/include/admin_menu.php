<?php
/**
 * Admin Menu Integration
 * Version: 1.0.0
 * Date: 2025-01-17
 * Description: Интеграция модуля управления подписчиками в админ-меню Bitrix
 * File: /local/php_interface/include/admin_menu.php
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
	die();
}

// Check if user has admin rights
if (!$USER->IsAdmin()) {
	return;
}

// Add event handler for admin menu
AddEventHandler("main", "OnBuildGlobalMenu", "OnBuildGlobalMenuHandler");

function OnBuildGlobalMenuHandler(&$aGlobalMenu, &$aModuleMenu) {
	// Add EDS Forms section to admin menu
	$aModuleMenu[] = [
		"parent_menu" => "global_menu_content",
		"section" => "eds_forms",
		"sort" => 200,
		"text" => "EDS Forms",
		"title" => "EDS Forms Management",
		"icon" => "mail_menu_icon",
		"page_icon" => "mail_page_icon",
		"items_id" => "menu_eds_forms",
		"items" => [
			[
				"text" => "Newsletter Subscribers",
				"title" => "Управление подписчиками рассылки",
				"url" => "/local/php_interface/admin/newsletter_admin.php",
				"icon" => "mail_menu_icon",
				"page_icon" => "mail_page_icon"
			],
			[
				"text" => "Contact Forms",
				"title" => "Контактные формы",
				"url" => "/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=" . getFormIblockId('contact'),
				"icon" => "form_menu_icon",
				"page_icon" => "form_page_icon"
			],
			[
				"text" => "Technical Specifications",
				"title" => "Технические задания",
				"url" => "/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=" . getFormIblockId('technical_spec'),
				"icon" => "form_menu_icon",
				"page_icon" => "form_page_icon"
			],
			[
				"text" => "Consultations",
				"title" => "Консультации",
				"url" => "/bitrix/admin/iblock_list_admin.php?IBLOCK_ID=" . getFormIblockId('consultation'),
				"icon" => "form_menu_icon",
				"page_icon" => "form_page_icon"
			],
			[
				"text" => "Forms Statistics",
				"title" => "Статистика форм",
				"url" => "/local/php_interface/admin/forms_stats.php",
				"icon" => "statistic_menu_icon",
				"page_icon" => "statistic_page_icon"
			]
		]
	];
}

/**
 * Get form iblock ID
 */
function getFormIblockId($formType) {
	static $cache = [];

	if (isset($cache[$formType])) {
		return $cache[$formType];
	}

	CModule::IncludeModule("iblock");

	$iblockCode = 'eds_forms_' . $formType;

	$arFilter = [
		'TYPE' => 'content',
		'SITE_ID' => SITE_ID,
		'CODE' => $iblockCode
	];

	$rsIBlock = CIBlock::GetList([], $arFilter);
	if ($arIBlock = $rsIBlock->Fetch()) {
		$cache[$formType] = $arIBlock['ID'];
		return $arIBlock['ID'];
	}

	return 0;
}

// Add CSS for admin icons
AddEventHandler("main", "OnAdminListDisplay", "OnAdminListDisplayHandler");

function OnAdminListDisplayHandler(&$list) {
	global $APPLICATION;

	$APPLICATION->AddHeadString('
    <style>
        .mail_menu_icon {
            background: url("/bitrix/themes/.default/icons/mail/mail.gif") no-repeat 0 0;
        }
        .form_menu_icon {
            background: url("/bitrix/themes/.default/icons/form/form.gif") no-repeat 0 0;
        }
        .statistic_menu_icon {
            background: url("/bitrix/themes/.default/icons/statistic/statistic.gif") no-repeat 0 0;
        }
        .mail_page_icon {
            background: url("/bitrix/themes/.default/icons/mail/mail_page.gif") no-repeat 0 0;
        }
        .form_page_icon {
            background: url("/bitrix/themes/.default/icons/form/form_page.gif") no-repeat 0 0;
        }
        .statistic_page_icon {
            background: url("/bitrix/themes/.default/icons/statistic/statistic_page.gif") no-repeat 0 0;
        }
    </style>
    ');
}

// Add newsletter widget to admin dashboard
AddEventHandler("main", "OnAdminInformerInsertItems", "OnAdminInformerInsertItemsHandler");

function OnAdminInformerInsertItemsHandler() {
	global $USER;

	if (!$USER->IsAdmin()) {
		return;
	}

	CAdminInformer::AddItem([
		"HTML" => getNewsletterWidget(),
		"TITLE" => "Newsletter Statistics",
		"COLOR" => "red",
		"SORT" => 100
	]);
}

/**
 * Get newsletter widget HTML
 */
function getNewsletterWidget() {
	global $DB;

	$tableName = "eds_newsletter_subscribers";

	// Get statistics
	$stats = [];

	try {
		// Total subscribers
		$result = $DB->Query("SELECT COUNT(*) as total FROM {$tableName}");
		$stats['total'] = $result ? $result->Fetch()['total'] : 0;

		// Active subscribers
		$result = $DB->Query("SELECT COUNT(*) as active FROM {$tableName} WHERE status = 'active'");
		$stats['active'] = $result ? $result->Fetch()['active'] : 0;

		// Today's subscriptions
		$result = $DB->Query("SELECT COUNT(*) as today FROM {$tableName} WHERE DATE(subscribe_date) = CURDATE()");
		$stats['today'] = $result ? $result->Fetch()['today'] : 0;

		// This week's subscriptions
		$result = $DB->Query("SELECT COUNT(*) as week FROM {$tableName} WHERE subscribe_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
		$stats['week'] = $result ? $result->Fetch()['week'] : 0;

	} catch (Exception $e) {
		$stats = ['total' => 0, 'active' => 0, 'today' => 0, 'week' => 0];
	}

	return '
    <div style="padding: 10px; background: #f8f9fa; border-radius: 5px;">
        <h4 style="margin: 0 0 10px 0; color: #333;">Newsletter Statistics</h4>
        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 10px; font-size: 12px;">
            <div style="background: white; padding: 8px; border-radius: 3px; text-align: center;">
                <div style="font-weight: bold; color: #ff2545; font-size: 16px;">' . $stats['total'] . '</div>
                <div style="color: #666;">Total</div>
            </div>
            <div style="background: white; padding: 8px; border-radius: 3px; text-align: center;">
                <div style="font-weight: bold; color: #28a745; font-size: 16px;">' . $stats['active'] . '</div>
                <div style="color: #666;">Active</div>
            </div>
            <div style="background: white; padding: 8px; border-radius: 3px; text-align: center;">
                <div style="font-weight: bold; color: #007bff; font-size: 16px;">' . $stats['today'] . '</div>
                <div style="color: #666;">Today</div>
            </div>
            <div style="background: white; padding: 8px; border-radius: 3px; text-align: center;">
                <div style="font-weight: bold; color: #6c757d; font-size: 16px;">' . $stats['week'] . '</div>
                <div style="color: #666;">This Week</div>
            </div>
        </div>
        <div style="margin-top: 10px; text-align: center;">
            <a href="/local/php_interface/admin/newsletter_admin.php" style="color: #ff2545; text-decoration: none; font-size: 12px;">
                Manage Subscribers →
            </a>
        </div>
    </div>
    ';
}

// Auto-create admin user menu item
AddEventHandler("main", "OnAfterUserLogin", "OnAfterUserLoginHandler");

function OnAfterUserLoginHandler(&$arFields) {
	global $USER;

	if ($USER->IsAdmin()) {
		// Create quick access in user menu
		$_SESSION['EDS_FORMS_QUICK_ACCESS'] = [
			'newsletter' => '/local/php_interface/admin/newsletter_admin.php',
			'stats' => '/local/php_interface/admin/forms_stats.php'
		];
	}
}
?>