<?php
// /bitrix/admin/php_command_line.php (или временный файл)
// Version: 1.0.0 • Author: KW • https://kowb.ru
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php');
\Bitrix\Main\Config\Option::set('search','include_numbers','Y');
\Bitrix\Main\Config\Option::set('search','min_word_length','2'); // по желанию
echo "OK";
