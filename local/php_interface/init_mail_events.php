<?php
/**
 * Initialize Mail Events for EDS Forms
 * File: /local/php_interface/init_mail_events.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

define('BITRIX_MAIL_HELPER_INIT', true);
require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/classes/BitrixMailHelper.php");

echo "Initializing EDS Mail Events...\n";

if (BitrixMailHelper::init()) {
    echo "✅ Mail events initialized successfully!\n";
} else {
    echo "❌ Failed to initialize mail events!\n";
}

echo "Done.\n";
?>
