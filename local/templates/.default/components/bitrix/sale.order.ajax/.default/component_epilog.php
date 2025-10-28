<?php
/**
 * Order Component Epilog
 * Component: sale.order.ajax
 * 
 * @version 2.0.2
 * @author KW https://kowb.ru
 * 
 * Path: /local/templates/.default/components/bitrix/sale.order.ajax/.default/component_epilog.php
 */

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Page\Asset;

Asset::getInstance()->addJs('/local/templates/.default/components/bitrix/sale.order.ajax/.default/script.js?v=2.0.0');