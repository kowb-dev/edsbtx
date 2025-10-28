<?php
/**
 * Файл инициализации сайта (Финальная безопасная версия)
 * Version: 2.2.0
 * Date: 2025-10-19
 * File: /local/php_interface/init.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/local/php_interface/classes/BitrixMailHelper.php");

\Bitrix\Main\Loader::registerAutoLoadClasses(null, [
    '\Local\Controllers\FavoritesController' => '/local/php_interface/lib/FavoritesController.php',
]);

//AddEventHandler("main", "OnBeforeUserRegister", "DisablePhoneValidation");
function DisablePhoneValidation(&$arFields)
{
    unset($arFields["PERSONAL_PHONE"]);
}

AddEventHandler("main", "OnAfterUserRegister", "OnAfterUserRegisterHandler");
function OnAfterUserRegisterHandler(&$arFields)
{
    if (intval($arFields["USER_ID"]) > 0)
    {
        // Add user to group 2
        CUser::SetUserGroup($arFields["USER_ID"], array_merge($arFields["GROUP_ID"], [2]));
    }
}

define("DEFAULT_TEMPLATE_PATH", "/local/templates/.default");

function debug($data){
	echo '<pre>' . print_r($data, 1) . '</pre>';
}

// Артикул вместо внешний код
AddEventHandler("main", "OnBeforeProlog", "ChangeIblockFieldNames");

function ChangeIblockFieldNames() {
	if (strpos($_SERVER['REQUEST_URI'], '/bitrix/admin/iblock_list_admin.php') !== false) {
		$GLOBALS['MESS']['IBLOCK_FIELD_CODE'] = 'Артикул';
	}
}

// Настройки для нового шаблона каталога
AddEventHandler( 'main', 'OnEpilog', 'ModifyCatalogComponent' );

function ModifyCatalogComponent() {
	global $APPLICATION;

	// Проверяем, что мы находимся в разделе каталога
	$curPage = $APPLICATION->GetCurPage();
	if ( strpos( $curPage, '/catalog/' ) !== FALSE ) {
		// Добавляем мета-теги для каталога
		$APPLICATION->SetPageProperty( 'viewport',
			'width=device-width, initial-scale=1' );

		// Подключаем дополнительные стили для старых браузеров
		$APPLICATION->SetAdditionalCSS( '/local/templates/.default/css/catalog-polyfills.css' );

		// Добавляем structured data для SEO
		addCatalogStructuredData();
	}
}

function addCatalogStructuredData() {
	global $APPLICATION;

	$structuredData = [
		'@context' => 'https://schema.org',
		'@type' => 'CollectionPage',
		'name' => $APPLICATION->GetTitle(),
		'description' => $APPLICATION->GetProperty( 'description' ),
		'url' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'],
		'mainEntity' => [
			'@type' => 'ItemList',
			'name'  => 'Товары каталога',
		]
	];

	$APPLICATION->AddHeadString(
		'<script type="application/ld+json">' .
		json_encode( $structuredData,
			JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) .
		'</script>'
	);
}



// Дополнительные настройки производительности
AddEventHandler( 'main', 'OnBeforeProlog', 'OptimizeCatalogPerformance' );

function OptimizeCatalogPerformance() {
	// Включаем сжатие для каталога
	if ( strpos( $_SERVER['REQUEST_URI'], '/catalog/' ) !== FALSE ) {
		if ( function_exists( 'gzencode' )
		     && strpos( $_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip' ) !== FALSE
		     && ! headers_sent()
		) {
			ob_start( 'ob_gzhandler' );
		}
	}
}

AddEventHandler("search", "OnBeforeSearch", "SearchByArticleHandler");

function SearchByArticleHandler(&$arFields)
{
    if ($arFields["MODULE_ID"] == "iblock" && CModule::IncludeModule("iblock"))
    {
        // Check if the query is not empty
        $query = $arFields["QUERY"];
        if (empty($query)) {
            return;
        }

        // A simple check to see if the query could be an article number.
        // This can be adjusted to be more specific if needed.
        // For example, check for a specific length or pattern.
        if (strlen($query) > 2) {
            $arFilter = array(
                "IBLOCK_ID" => 7, // Catalog iblock ID
                "ACTIVE" => "Y",
                "PROPERTY_CML2_ARTICLE" => "%" . $query . "%"
            );

            $arSelect = array("ID", "IBLOCK_ID");

            $rsElements = CIBlockElement::GetList(array(), $arFilter, false, false, $arSelect);
            $foundIds = array();
            while ($arElement = $rsElements->Fetch()) {
                $foundIds[] = $arElement["ID"];
            }

            if (!empty($foundIds)) {
                // If we found items by article, we modify the search query
                // to search for these specific item IDs.
                // This ensures they are included in the search results.
                $arFields["QUERY"] = implode(" | ", $foundIds);
                $arFields["PARAMS"]["STEMMING"] = "N"; // Disable stemming for ID search
            }
        }
    }
}

/**
 * Битрикс - Безопасный PHP блокировщик лицензионных уведомлений
 * Version: 2.1.0 (Safe Edition)
 * Date: 2025-07-19
 * Минимальное и безопасное блокирование рекламных уведомлений о лицензии
 */
//
//use Bitrix\Main\Page\Asset;
//
//class BitrixLicenseBlockerSafe {
//	private static $instance = NULL;
//	private $isActive = FALSE;
//
//	public static function getInstance() {
//		if ( self::$instance === NULL ) {
//			self::$instance = new self();
//		}
//		return self::$instance;
//	}
//
//	private function __construct() {
//		$this->init();
//	}
//
//	private function init() {
//		if ( $this->isActive ) {
//			return;
//		}
//
//		// Основной блокирующий JavaScript - работает на всех страницах
//		$this->addUniversalBlockingScript();
//
//		// Дополнительная CSS блокировка для админки
//		if ( defined( 'ADMIN_SECTION' ) && ADMIN_SECTION === TRUE ) {
//			$this->addAdminCSS();
//		}
//
//		// Простая буферная очистка без событий
//		$this->startSimpleBuffering();
//
//		$this->isActive = TRUE;
//	}
//
//	private function addUniversalBlockingScript() {
//		// Универсальный JavaScript блокировщик
//		Asset::getInstance()->addString( "
//		<script>
//		(function() {
//			'use strict';
//
//			// Немедленное блокирование функций
//			const blockFunctions = () => {
//				const blocked = ['showProlongMenu', 'prolongRemind'];
//				blocked.forEach(func => {
//					try {
//						if (window[func]) window[func] = () => false;
//						Object.defineProperty(window, func, {
//							value: () => false,
//							writable: false,
//							configurable: false
//						});
//					} catch(e) {}
//				});
//			};
//
//			// Блокировка BX методов
//			const blockBXMethods = () => {
//				try {
//					if (typeof BX !== 'undefined') {
//						if (BX.PopupMenu && BX.PopupMenu.show) {
//							const originalShow = BX.PopupMenu.show;
//							BX.PopupMenu.show = function(id) {
//								if (id === 'prolong-popup') return false;
//								return originalShow.apply(this, arguments);
//							};
//						}
//
//						if (BX.userOptions && BX.userOptions.save) {
//							const originalSave = BX.userOptions.save;
//							BX.userOptions.save = function(module, option, key) {
//								if (module === 'main' && option === 'admSupInf' && key === 'showInformerDate') {
//									return false;
//								}
//								return originalSave.apply(this, arguments);
//							};
//						}
//					}
//				} catch(e) {}
//			};
//
//			// Очистка DOM элементов
//			const cleanupDOM = () => {
//				try {
//					// Селекторы для удаления
//					const selectors = [
//						'[href*=\"key_update.php\"]',
//						'[href*=\"util.1c-bitrix.ru\"]',
//						'[onclick*=\"showProlongMenu\"]',
//						'[onclick*=\"prolongRemind\"]',
//						'#prolongmenu',
//						'#supdescr',
//						'.adm-info-message-wrap',
//						'.adm-info-message'
//					];
//
//					selectors.forEach(selector => {
//						try {
//							document.querySelectorAll(selector).forEach(el => {
//								el.style.display = 'none !important';
//								el.style.visibility = 'hidden !important';
//								el.style.opacity = '0 !important';
//								el.remove();
//							});
//						} catch(e) {}
//					});
//
//					// Поиск по тексту
//					if (document.body) {
//						const walker = document.createTreeWalker(
//							document.body,
//							NodeFilter.SHOW_ELEMENT,
//							null,
//							false
//						);
//
//						const toHide = [];
//						let node;
//
//						while (node = walker.nextNode()) {
//							try {
//								const text = node.textContent || '';
//								if (text.includes('Срок действия текущей лицензии') ||
//									text.includes('Продлить лицензию') ||
//									text.includes('закончился') ||
//									text.includes('util.1c-bitrix.ru')) {
//									toHide.push(node);
//								}
//							} catch(e) {}
//						}
//
//						toHide.forEach(el => {
//							try {
//								// Ищем родительский контейнер
//								let parent = el;
//								for (let i = 0; i < 5 && parent && parent !== document.body; i++) {
//									parent = parent.parentElement;
//									if (parent && (
//										parent.style.backgroundColor ||
//										parent.className.includes('message') ||
//										parent.className.includes('adm-')
//									)) {
//										parent.style.display = 'none !important';
//										parent.remove();
//										break;
//									}
//								}
//								// Если не нашли контейнер, скрываем сам элемент
//								el.style.display = 'none !important';
//								el.remove();
//							} catch(e) {}
//						});
//					}
//				} catch(e) {}
//			};
//
//			// Инициализация
//			blockFunctions();
//
//			// Ожидание загрузки BX
//			let bxCheckAttempts = 0;
//			const checkBX = () => {
//				if (typeof BX !== 'undefined' || bxCheckAttempts > 50) {
//					blockBXMethods();
//					return;
//				}
//				bxCheckAttempts++;
//				setTimeout(checkBX, 100);
//			};
//			checkBX();
//
//			// Очистка после загрузки DOM
//			if (document.readyState === 'loading') {
//				document.addEventListener('DOMContentLoaded', cleanupDOM);
//			} else {
//				cleanupDOM();
//			}
//
//			// Периодическая очистка (ограниченная по времени)
//			let cleanupCount = 0;
//			const periodicCleanup = setInterval(() => {
//				if (cleanupCount > 15) { // Максимум 30 секунд
//					clearInterval(periodicCleanup);
//					return;
//				}
//				cleanupDOM();
//				cleanupCount++;
//			}, 2000);
//
//			// Мониторинг изменений DOM (если поддерживается)
//			if (window.MutationObserver) {
//				try {
//					const observer = new MutationObserver(() => {
//						setTimeout(cleanupDOM, 100);
//					});
//
//					if (document.body) {
//						observer.observe(document.body, {
//							childList: true,
//						subtree: true
//						});
//
//						// Отключаем наблюдатель через минуту
//						setTimeout(() => observer.disconnect(), 60000);
//					}
//				} catch(e) {}
//			}
//
//		})();
//		</script>
//		" );
//	}
//
//	private function addAdminCSS() {
//		// CSS для скрытия в админке
//		Asset::getInstance()->addString( '
//		<style>
//		/* Блокировка лицензионных элементов */
//		[href*="key_update.php"],
//		[href*="util.1c-bitrix.ru"],
//		[onclick*="showProlongMenu"],
//		[onclick*="prolongRemind"],
//		#prolongmenu,
//		#supdescr,
//		.adm-info-message-wrap,
//		.adm-info-message,
//		#menu-popup-prolong-popup {
//			display: none !important;
//			visibility: hidden !important;
//			opacity: 0 !important;
//			height: 0 !important;
//			width: 0 !important;
//			position: absolute !important;
//			left: -9999px !important;
//			z-index: -1 !important;
//			pointer-events: none !important;
//		}
//
//		/* Скрытие контейнеров с лицензионным контентом */
//		table[bgcolor="#fff2cc"],
//		table[bgcolor="#ffebcd"],
//		div[style*="background-color: #fff2cc"],
//		div[style*="background-color: #ffebcd"],
//		td:has([href*="key_update.php"]),
//		tr:has([href*="key_update.php"]),
//		div[style*="float: right"]:has([href*="key_update.php"]) {
//			display: none !important;
//		}
//
//		/* Дополнительные правила для разных типов уведомлений */
//		.popup-window[id*="prolong"],
//		.menu-popup[id*="prolong"] {
//			display: none !important;
//		}
//		</style>
//		' );
//	}
//
//	private function startSimpleBuffering() {
//		// Простая буферная очистка без сложных событий
//		if ( ! ob_get_level() ) {
//			ob_start( function( $buffer ) {
//				return $this->cleanContent( $buffer );
//			} );
//		}
//	}
//
//	private function cleanContent( $content ) {
//		if ( ! is_string( $content ) || empty( $content ) ) {
//			return $content;
//		}
//
//		try {
//			// Простые замены без сложных regex
//			$patterns = [
//				'showProlongMenu',
//				'prolongRemind',
//				'key_update.php',
//				'util.1c-bitrix.ru',
//				'prolong-popup',
//				'menu-popup-prolong-popup'
//			];
//
//			foreach ( $patterns as $pattern ) {
//				// Удаляем script теги содержащие эти паттерны
//				$content = preg_replace(
//					'/<script[^>]*>.*?' . preg_quote( $pattern, '/' ) . '.*?</script>/uis',
//					'',
//					$content
//				);
//
//				// Удаляем onclick атрибуты
//				$content = preg_replace(
//					'/onclick\s*=\s*["\'](?:[^"\']*(?:' . preg_quote( $pattern, '/' ) . ')[^"\']*["\']/ui',
//					'',
//					$content
//				);
//
//				// Удаляем href атрибуты с этими паттернами
//				$content = preg_replace(
//					'/<a[^>]*href\s*=\s*["\'](?:[^"\']*(?:' . preg_quote( $pattern, '/' ) . ')[^"\']*["\'][^>]*>.*?</a>/uis',
//					'',
//					$content
//				);
//			}
//
//			// Удаляем специфические div блоки
//			$content = preg_replace(
//				'/<div[^>]*adm-info-message[^>]*>.*?</div>/uis',
//				'',
//				$content
//			);
//
//			$content = preg_replace(
//				'/<div[^>]*float\s*:\s*right[^>]*>.*?</div>/uis',
//				'',
//				$content
//			);
//
//		} catch ( Exception $e ) {
//			// Если regex не сработал, возвращаем оригинал
//			return $content;
//		}
//
//		return $content;
//	}
//
//	// Публичные методы для управления
//	public function isActive() {
//		return $this->isActive;
//	}
//
//	public function disable() {
//		$this->isActive = FALSE;
//	}
//}
//
//// Безопасная инициализация
//if ( ! defined( 'BITRIX_LICENSE_BLOCKER_SAFE_LOADED' ) ) {
//	define( 'BITRIX_LICENSE_BLOCKER_SAFE_LOADED', TRUE );
//
//	try {
//		BitrixLicenseBlockerSafe::getInstance();
//	} catch ( Exception $e ) {
//		// Тихо игнорируем ошибки инициализации
//		error_log( 'License blocker safe initialization error: ' . $e->getMessage() );
//	}
//}
//
//?>