<?php
/**
 * Section Settings for Calculators
 * Version: 1.0.0
 * Author: Kostya Webin
 * Description: Section configuration for calculators directory
 */

// Include global settings
$sSectionName = "Калькуляторы";
$arDirProperties = array(
	// SEO Properties
	"title" => "Калькуляторы электрооборудования сцены - Профессиональные расчеты",
	"keywords" => "калькулятор электрооборудования, расчет сечения провода, калькулятор тока, падение напряжения, сценическое освещение, электрические расчеты",
	"description" => "Профессиональные калькуляторы для расчета электрооборудования сцены, сечения проводов, тока и напряжения. Точные расчеты для сценического освещения и электрических систем.",

	// Additional meta tags
	"robots" => "index, follow",
	"author" => "EdSys - Электрооборудование для сцены",
	"viewport" => "width=device-width, initial-scale=1.0",

	// Open Graph properties
	"og:title" => "Калькуляторы электрооборудования сцены",
	"og:description" => "Профессиональные калькуляторы для расчета электрооборудования сцены, сечения проводов, тока и напряжения",
	"og:type" => "website",
	"og:locale" => "ru_RU",

	// Technical settings
	"charset" => "UTF-8",
	"content-type" => "text/html; charset=UTF-8"
);

// Include structured data for SEO
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "WebPage",
	"name" => "Калькуляторы электрооборудования сцены",
	"description" => "Профессиональные калькуляторы для расчета электрооборудования сцены, сечения проводов, тока и напряжения",
	"url" => "https://" . $_SERVER['SERVER_NAME'] . "/kalkulyatory/",
	"mainEntity" => [
		"@type" => "ItemList",
		"name" => "Калькуляторы",
		"description" => "Список профессиональных калькуляторов для электрооборудования",
		"itemListElement" => [
			[
				"@type" => "ListItem",
				"position" => 1,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Калькулятор электрооборудования сцены",
					"description" => "Расчет нагрузки и распределения электрооборудования для сценического освещения",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 2,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Калькулятор перевода Ватт в Амперы",
					"description" => "Быстрый перевод мощности в силу тока для однофазных и трехфазных цепей",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 3,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Расчет сечения провода по диаметру",
					"description" => "Определение сечения провода по диаметру жилы или количеству витков",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 4,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Расчет тока в цепи",
					"description" => "Вычисление силы тока по закону Ома для различных типов цепей",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 5,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Расчет сечения провода по потере напряжения",
					"description" => "Подбор оптимального сечения провода с учетом допустимых потерь напряжения",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 6,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Расчет падения напряжения в линии",
					"description" => "Определение потерь напряжения в кабельных линиях различной длины",
					"applicationCategory" => "UtilityApplication"
				]
			],
			[
				"@type" => "ListItem",
				"position" => 7,
				"item" => [
					"@type" => "SoftwareApplication",
					"name" => "Расчет потери напряжения",
					"description" => "Комплексный расчет потерь напряжения в электрических сетях",
					"applicationCategory" => "UtilityApplication"
				]
			]
		]
	]
];

?>