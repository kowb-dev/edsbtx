<?php
/**
 * Cable load tables data
 * Данные таблиц токовых нагрузок медных кабелей
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Cable types configuration
$arCableTypes = [
	'xtrem' => [
		'name' => 'Кабель XTREM H07RN-F',
		'full_name' => 'Кабель гибкий резиновый XTREM H07RN-F',
		'description' => 'Европейский стандарт качества для профессионального применения',
		'color' => 'voltage', // Uses --edsys-voltage color
		'icon' => 'ph-lightning'
	],
	'kgtp' => [
		'name' => 'Кабель КГтп-ХЛ',
		'full_name' => 'Кабель гибкий резиновый КГтп-ХЛ',
		'description' => 'Морозостойкий кабель для экстремальных условий',
		'color' => 'circuit', // Uses --edsys-circuit color
		'icon' => 'ph-snowflake'
	],
	'pugv' => [
		'name' => 'Провод ПуГВ',
		'full_name' => 'Провод установочный гибкий ПуГВ',
		'description' => 'Универсальный провод для внутренних установок',
		'color' => 'spark', // Uses --edsys-spark color
		'icon' => 'ph-plug'
	]
];

// Related sections from site menu
$arRelatedSections = [
	[
		'name' => 'Калькуляторы',
		'url' => '/kalkulyatory/',
		'description' => 'Расчет сечения проводов и падения напряжения',
		'icon' => 'ph-calculator'
	],
	[
		'name' => 'Кабельная продукция',
		'url' => '/cat/kabelnaya-produktsiya/',
		'description' => 'Профессиональные кабели и провода',
		'icon' => 'ph-plug-charging'
	],
	[
		'name' => 'Разъемы и компоненты',
		'url' => '/cat/razemy-i-komponenty/',
		'description' => 'Соединители и аксессуары',
		'icon' => 'ph-usb'
	],
	[
		'name' => 'Сигнальная коммутация',
		'url' => '/cat/signalnaya-kommutatsiya/',
		'description' => 'Коммутационное оборудование',
		'icon' => 'ph-flow-arrow'
	]
];

// XTREM H07RN-F cable data
$arXtremData = [
	['section' => '1 G 50', 'diameter' => '17', 'weight' => '630', 'current' => '207'],
	['section' => '1 G 70', 'diameter' => '18.9', 'weight' => '840', 'current' => '268'],
	['section' => '1 G 95', 'diameter' => '21.4', 'weight' => '1100', 'current' => '328'],
	['section' => '1 G 120', 'diameter' => '23.3', 'weight' => '1370', 'current' => '383'],
	['section' => '1 G 150', 'diameter' => '25.8', 'weight' => '1685', 'current' => '444'],
	['section' => '2 G 1,5', 'diameter' => '8.3', 'weight' => '100', 'current' => '26'],
	['section' => '2 G 2,5', 'diameter' => '9.8', 'weight' => '145', 'current' => '36'],
	['section' => '2 G 4', 'diameter' => '10.9', 'weight' => '195', 'current' => '49'],
	['section' => '2 G 6', 'diameter' => '12.4', 'weight' => '265', 'current' => '63'],
	['section' => '3 G 1,5', 'diameter' => '9.2', 'weight' => '125', 'current' => '26'],
	['section' => '3 G 2,5', 'diameter' => '10.9', 'weight' => '185', 'current' => '36'],
	['section' => '3 G 4', 'diameter' => '12.4', 'weight' => '260', 'current' => '49'],
	['section' => '3 G 6', 'diameter' => '14.4', 'weight' => '350', 'current' => '63'],
	['section' => '3 G 10', 'diameter' => '19.4', 'weight' => '625', 'current' => '86'],
	['section' => '3 G 16', 'diameter' => '21.8', 'weight' => '855', 'current' => '115'],
	['section' => '4 G 1,5', 'diameter' => '10.3', 'weight' => '160', 'current' => '23'],
	['section' => '4 G 2,5', 'diameter' => '11.9', 'weight' => '230', 'current' => '32'],
	['section' => '5 G 1,5', 'diameter' => '11.1', 'weight' => '190', 'current' => '23'],
	['section' => '5 G 2,5', 'diameter' => '13.2', 'weight' => '280', 'current' => '32'],
	['section' => '5 G 4', 'diameter' => '15.3', 'weight' => '400', 'current' => '42'],
	['section' => '5 G 6', 'diameter' => '17.7', 'weight' => '545', 'current' => '54'],
	['section' => '5 G 10', 'diameter' => '23.7', 'weight' => '945', 'current' => '75'],
	['section' => '5 G 16', 'diameter' => '26.9', 'weight' => '1320', 'current' => '100'],
	['section' => '5 G 25', 'diameter' => '32.9', 'weight' => '1995', 'current' => '127'],
	['section' => '5 G 35', 'diameter' => '35.8', 'weight' => '2560', 'current' => '158'],
	['section' => '5 G 50', 'diameter' => '42.2', 'weight' => '3575', 'current' => '192'],
	['section' => '5 G 70', 'diameter' => '46.7', 'weight' => '4715', 'current' => '226'],
	['section' => '5 G 95', 'diameter' => '52.5', 'weight' => '6105', 'current' => '298'],
	['section' => '5 G 120', 'diameter' => '57.2', 'weight' => '7500', 'current' => '346'],
	['section' => '16 G 1,5', 'diameter' => '19.6', 'weight' => '580', 'current' => '26'],
	['section' => '16 G 2,5', 'diameter' => '22.5', 'weight' => '840', 'current' => '36'],
	['section' => '18 G 1,5', 'diameter' => '20.2', 'weight' => '635', 'current' => '26'],
	['section' => '18 G 2,5', 'diameter' => '23.3', 'weight' => '910', 'current' => '36'],
	['section' => '24 G 1,5', 'diameter' => '23.1', 'weight' => '810', 'current' => '26'],
	['section' => '24 G 2,5', 'diameter' => '27', 'weight' => '1180', 'current' => '36']
];

// КГтп-ХЛ cable data
$arKgtpData = [
	['section' => '1х50', 'diameter' => '18,04', 'weight' => '533,8', 'current' => '215'],
	['section' => '1х70', 'diameter' => '19,47', 'weight' => '716,7', 'current' => '270'],
	['section' => '1х95', 'diameter' => '22,33', 'weight' => '955,5', 'current' => '325'],
	['section' => '1х120', 'diameter' => '24,31', 'weight' => '1191,3', 'current' => '385'],
	['section' => '1х150', 'diameter' => '31,35', 'weight' => '1632,1', 'current' => '440'],
	['section' => '2х1,5', 'diameter' => '9,79', 'weight' => '87', 'current' => '19'],
	['section' => '2х2,5', 'diameter' => '11', 'weight' => '118,5', 'current' => '27'],
	['section' => '2х4', 'diameter' => '12,87', 'weight' => '173,6', 'current' => '38'],
	['section' => '2х6', 'diameter' => '13,97', 'weight' => '223,8', 'current' => '50'],
	['section' => '3х1,5', 'diameter' => '10,34', 'weight' => '102,8', 'current' => '19'],
	['section' => '3х2,5', 'diameter' => '11,66', 'weight' => '145,4', 'current' => '27'],
	['section' => '3х4', 'diameter' => '13,64', 'weight' => '214,4', 'current' => '38'],
	['section' => '3х6', 'diameter' => '15,07', 'weight' => '286,2', 'current' => '50'],
	['section' => '3х10', 'diameter' => '19,25', 'weight' => '471,8', 'current' => '70'],
	['section' => '3х16', 'diameter' => '23,21', 'weight' => '687,7', 'current' => '90'],
	['section' => '4х1,5', 'diameter' => '11,66', 'weight' => '131,7', 'current' => '19'],
	['section' => '4х2,5', 'diameter' => '12,67', 'weight' => '178,9', 'current' => '25'],
	['section' => '5х1,5', 'diameter' => '12,76', 'weight' => '162,7', 'current' => '19'],
	['section' => '5х2,5', 'diameter' => '13,97', 'weight' => '222,8', 'current' => '25'],
	['section' => '5х4', 'diameter' => '16,72', 'weight' => '335,2', 'current' => '35'],
	['section' => '5х6', 'diameter' => '18,15', 'weight' => '442,8', 'current' => '42'],
	['section' => '5х10', 'diameter' => '23,32', 'weight' => '733,4', 'current' => '55'],
	['section' => '5х16', 'diameter' => '28,82', 'weight' => '1103,1', 'current' => '75'],
	['section' => '5х25', 'diameter' => '34,98', 'weight' => '1647,8', 'current' => '95'],
	['section' => '5х35', 'diameter' => '40,7', 'weight' => '2262,3', 'current' => '120'],
	['section' => '5х50', 'diameter' => '48,18', 'weight' => '3179,1', 'current' => '145'],
	['section' => '5х70', 'diameter' => '51,37', 'weight' => '4127,4', 'current' => '180'],
	['section' => '5х95', 'diameter' => '59,62', 'weight' => '5639,6', 'current' => '220'],
	['section' => '5х120', 'diameter' => '66,66', 'weight' => '7145,9', 'current' => '260']
];

// ПуГВ cable data
$arPugvData = [
	['section' => '1х1,5', 'diameter' => '3,3', 'weight' => '24,2', 'current' => '23'],
	['section' => '1х2,5', 'diameter' => '3,6', 'weight' => '34,3', 'current' => '30'],
	['section' => '1х4', 'diameter' => '4,5', 'weight' => '54,8', 'current' => '41'],
	['section' => '1х6', 'diameter' => '5', 'weight' => '74,4', 'current' => '50'],
	['section' => '1х10', 'diameter' => '6,5', 'weight' => '122,6', 'current' => '80'],
	['section' => '1х16', 'diameter' => '7,8', 'weight' => '178,3', 'current' => '100'],
	['section' => '1х25', 'diameter' => '10', 'weight' => '274,3', 'current' => '140'],
	['section' => '1х35', 'diameter' => '11,4', 'weight' => '366,8', 'current' => '170'],
	['section' => '1х50', 'diameter' => '13,8', 'weight' => '523', 'current' => '215'],
	['section' => '1х70', 'diameter' => '15', 'weight' => '701,1', 'current' => '270'],
	['section' => '1х95', 'diameter' => '17', 'weight' => '917,4', 'current' => '325']
];

// Combine all data
$arCableData = [
	'xtrem' => $arXtremData,
	'kgtp' => $arKgtpData,
	'pugv' => $arPugvData
];

// Table headers
$arTableHeaders = [
	'section' => 'Сечение, мм²',
	'diameter' => 'Диаметр, мм',
	'weight' => 'Масса, кг/км',
	'current' => 'Токовая нагрузка, А'
];

?>