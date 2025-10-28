<?php
/**
 * Административная панель для управления обменом с 1С
 * Путь: /bitrix/admin/1c_exchange_admin.php
 * Версия: 1.0
 * Дата: 2025-01-21
 */

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_before.php");

if (!$USER->IsAdmin()) {
	$APPLICATION->AuthForm("Доступ запрещен");
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");

$APPLICATION->SetTitle("Обмен с 1С - Управление");

// Подключение модулей
CModule::IncludeModule("iblock");
CModule::IncludeModule("catalog");

$logFile = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_exchange.log";
$uploadDir = $_SERVER["DOCUMENT_ROOT"]."/upload/1c_catalog/";

// Обработка действий
if ($_POST['action']) {
	switch ($_POST['action']) {
		case 'clear_log':
			if (file_exists($logFile)) {
				file_put_contents($logFile, '');
				ShowMessage("Лог-файл очищен");
			}
			break;

		case 'clear_files':
			if (is_dir($uploadDir)) {
				$files = glob($uploadDir . "*.xml");
				foreach ($files as $file) {
					unlink($file);
				}
				ShowMessage("Загруженные файлы удалены (" . count($files) . " файлов)");
			}
			break;

		case 'recreate_iblock':
			$result = recreateCatalogIBlock();
			if ($result) {
				ShowMessage("Инфоблок каталога пересоздан с ID: " . $result);
			} else {
				ShowError("Ошибка при пересоздании инфоблока");
			}
			break;

		case 'reindex_catalog':
			$iblockId = getCatalogIBlockId();
			if ($iblockId) {
				CSearch::ReIndexAll(false, $iblockId, "iblock");
				ShowMessage("Переиндексация каталога запущена");
			}
			break;
	}
}

// Получение статистики
$stats = getExchangeStatistics();
?>

	<style>
        .exchange-admin-panel { margin: 20px 0; }
        .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin: 20px 0; }
        .stat-card { background: #f8f9fa; padding: 15px; border-radius: 5px; border-left: 4px solid #007bff; }
        .stat-card h4 { margin: 0 0 5px 0; color: #333; }
        .stat-card .value { font-size: 24px; font-weight: bold; color: #007bff; }
        .log-container { background: #1e1e1e; color: #f8f8f2; padding: 15px; border-radius: 5px; max-height: 400px; overflow-y: auto; font-family: monospace; }
        .actions-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; }
        .action-card { background: #fff; border: 1px solid #ddd; border-radius: 5px; padding: 15px; }
        .btn-action { margin: 5px 5px 5px 0; }
        .error-log { color: #ff6b6b; }
        .warning-log { color: #ffd93d; }
        .success-log { color: #51cf66; }
	</style>

	<div class="exchange-admin-panel">

		<!-- Статистика -->
		<h2>Статистика обмена</h2>
		<div class="stats-grid">
			<div class="stat-card">
				<h4>Товаров в каталоге</h4>
				<div class="value"><?= number_format($stats['products']) ?></div>
			</div>
			<div class="stat-card">
				<h4>Разделов каталога</h4>
				<div class="value"><?= number_format($stats['sections']) ?></div>
			</div>
			<div class="stat-card">
				<h4>Типов цен</h4>
				<div class="value"><?= number_format($stats['price_types']) ?></div>
			</div>
			<div class="stat-card">
				<h4>Складов</h4>
				<div class="value"><?= number_format($stats['stores']) ?></div>
			</div>
			<div class="stat-card">
				<h4>Последний обмен</h4>
				<div class="value" style="font-size: 14px;"><?= $stats['last_exchange'] ?></div>
			</div>
			<div class="stat-card">
				<h4>Размер лога</h4>
				<div class="value" style="font-size: 16px;"><?= $stats['log_size'] ?></div>
			</div>
		</div>

		<!-- Действия -->
		<h2>Управление</h2>
		<div class="actions-grid">

			<div class="action-card">
				<h3>🗂️ Файлы обмена</h3>
				<p>Управление загруженными XML файлами</p>
				<form method="post" style="display: inline;">
					<input type="hidden" name="action" value="clear_files">
					<input type="submit" value="Очистить файлы" class="btn btn-danger btn-action"
					       onclick="return confirm('Удалить все XML файлы?')">
				</form>
				<a href="#files-list" class="btn btn-info btn-action" onclick="toggleFilesList()">Показать файлы</a>
			</div>

			<div class="action-card">
				<h3>📋 Логи обмена</h3>
				<p>Просмотр и очистка логов</p>
				<form method="post" style="display: inline;">
					<input type="hidden" name="action" value="clear_log">
					<input type="submit" value="Очистить лог" class="btn btn-danger btn-action"
					       onclick="return confirm('Очистить лог-файл?')">
				</form>
				<a href="#log-viewer" class="btn btn-info btn-action" onclick="toggleLog()">Показать лог</a>
			</div>

			<div class="action-card">
				<h3>🏗️ Структура каталога</h3>
				<p>Управление инфоблоком каталога</p>
				<form method="post" style="display: inline;">
					<input type="hidden" name="action" value="recreate_iblock">
					<input type="submit" value="Пересоздать ИБ" class="btn btn-warning btn-action"
					       onclick="return confirm('Пересоздать инфоблок каталога? Все данные будут удалены!')">
				</form>
				<a href="/bitrix/admin/iblock_admin.php?type=1c_catalog" class="btn btn-info btn-action" target="_blank">Открыть ИБ</a>
			</div>

			<div class="action-card">
				<h3>🔍 Поиск и индексы</h3>
				<p>Переиндексация каталога</p>
				<form method="post" style="display: inline;">
					<input type="hidden" name="action" value="reindex_catalog">
					<input type="submit" value="Переиндексировать" class="btn btn-success btn-action">
				</form>
				<a href="/bitrix/admin/search_reindex.php" class="btn btn-info btn-action" target="_blank">Настройки поиска</a>
			</div>

			<div class="action-card">
				<h3>⚙️ Диагностика</h3>
				<p>Проверка системы обмена</p>
				<button onclick="runDiagnostics()" class="btn btn-primary btn-action">Запустить диагностику</button>
				<a href="1c_exchange_diagnostics_v1.0.php" class="btn btn-info btn-action" target="_blank">Полная диагностика</a>
			</div>

			<div class="action-card">
				<h3>📊 Статистика обмена</h3>
				<p>Детальная аналитика</p>
				<button onclick="showDetailedStats()" class="btn btn-primary btn-action">Подробная статистика</button>
				<button onclick="exportStats()" class="btn btn-success btn-action">Экспорт в Excel</button>
			</div>

		</div>

		<!-- Список файлов -->
		<div id="files-list" style="display: none; margin-top: 20px;">
			<h3>Загруженные файлы</h3>
			<div id="files-content">
				<?= getFilesList() ?>
			</div>
		</div>

		<!-- Просмотр лога -->
		<div id="log-viewer" style="display: none; margin-top: 20px;">
			<h3>Последние записи лога <button onclick="refreshLog()" class="btn btn-sm btn-info">Обновить</button></h3>
			<div id="log-content" class="log-container">
				<?= getLogContent() ?>
			</div>
		</div>

		<!-- Диагностика -->
		<div id="diagnostics-result" style="display: none; margin-top: 20px;">
			<h3>Результаты диагностики</h3>
			<div id="diagnostics-content" class="log-container">
			</div>
		</div>

		<!-- Детальная статистика -->
		<div id="detailed-stats" style="display: none; margin-top: 20px;">
			<h3>Детальная статистика</h3>
			<div id="detailed-stats-content">
			</div>
		</div>

	</div>

	<script>
        function toggleFilesList() {
            const div = document.getElementById('files-list');
            div.style.display = div.style.display === 'none' ? 'block' : 'none';
        }

        function toggleLog() {
            const div = document.getElementById('log-viewer');
            div.style.display = div.style.display === 'none' ? 'block' : 'none';
        }

        function refreshLog() {
            fetch('1c_exchange_admin_v1.0.php?ajax=get_log')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('log-content').innerHTML = data;
                });
        }

        function runDiagnostics() {
            const div = document.getElementById('diagnostics-result');
            const content = document.getElementById('diagnostics-content');

            div.style.display = 'block';
            content.innerHTML = 'Выполняется диагностика...';

            fetch('1c_exchange_diagnostics_v1.0.php?ajax=1')
                .then(response => response.json())
                .then(data => {
                    content.innerHTML = data.output.replace(/\n/g, '<br>');
                })
                .catch(error => {
                    content.innerHTML = 'Ошибка выполнения диагностики: ' + error;
                });
        }

        function showDetailedStats() {
            const div = document.getElementById('detailed-stats');
            const content = document.getElementById('detailed-stats-content');

            div.style.display = 'block';
            content.innerHTML = 'Загрузка статистики...';

            fetch('1c_exchange_admin_v1.0.php?ajax=detailed_stats')
                .then(response => response.text())
                .then(data => {
                    content.innerHTML = data;
                });
        }

        function exportStats() {
            window.open('1c_exchange_admin_v1.0.php?ajax=export_stats', '_blank');
        }

        // Автообновление статистики каждые 30 секунд
        setInterval(() => {
            location.reload();
        }, 30000);
	</script>

<?php

// AJAX обработчики
if (isset($_GET['ajax'])) {
	switch ($_GET['ajax']) {
		case 'get_log':
			echo getLogContent();
			break;
		case 'detailed_stats':
			echo getDetailedStatistics();
			break;
		case 'export_stats':
			exportStatisticsToExcel();
			break;
	}
	exit;
}

/**
 * Получение статистики обмена
 */
function getExchangeStatistics() {
	global $logFile;

	$stats = array(
		'products' => 0,
		'sections' => 0,
		'price_types' => 0,
		'stores' => 0,
		'last_exchange' => 'Никогда',
		'log_size' => '0 B'
	);

	// Подсчет товаров
	$iblockId = getCatalogIBlockId();
	if ($iblockId) {
		$stats['products'] = CIBlockElement::GetList(
			array(),
			array("IBLOCK_ID" => $iblockId),
			array()
		);

		$stats['sections'] = CIBlockSection::GetList(
			array(),
			array("IBLOCK_ID" => $iblockId),
			false,
			array()
		);
	}

	// Типы цен
	$stats['price_types'] = CPrice::GetList()->SelectedRowsCount();

	// Склады
	$stats['stores'] = CCatalogStore::GetList()->SelectedRowsCount();

	// Последний обмен из лога
	if (file_exists($logFile)) {
		$stats['log_size'] = formatBytes(filesize($logFile));

		$lines = file($logFile);
		if (!empty($lines)) {
			$lastLine = end($lines);
			if (preg_match('/\[([\d\-: ]+)\]/', $lastLine, $matches)) {
				$stats['last_exchange'] = $matches[1];
			}
		}
	}

	return $stats;
}

/**
 * Получение ID инфоблока каталога
 */
function getCatalogIBlockId() {
	$rsIBlock = CIBlock::GetList(array(), array('TYPE' => '1c_catalog', 'CODE' => '1c_catalog'));
	if ($arIBlock = $rsIBlock->Fetch()) {
		return $arIBlock['ID'];
	}
	return false;
}

/**
 * Получение содержимого лога
 */
function getLogContent($lines = 50) {
	global $logFile;

	if (!file_exists($logFile)) {
		return "Лог-файл не найден";
	}

	$content = file($logFile);
	if (empty($content)) {
		return "Лог-файл пустой";
	}

	$recentLines = array_slice($content, -$lines);
	$output = '';

	foreach ($recentLines as $line) {
		$line = htmlspecialchars(trim($line));
		$class = '';

		if (strpos($line, 'ERROR') !== false) {
			$class = 'error-log';
		} elseif (strpos($line, 'WARNING') !== false) {
			$class = 'warning-log';
		} elseif (strpos($line, 'успешно') !== false) {
			$class = 'success-log';
		}

		$output .= "<div class='$class'>$line</div>";
	}

	return $output;
}

/**
 * Получение списка файлов
 */
function getFilesList() {
	global $uploadDir;

	if (!is_dir($uploadDir)) {
		return "Директория не найдена";
	}

	$files = glob($uploadDir . "*.xml");
	if (empty($files)) {
		return "Файлы не найдены";
	}

	// Сортировка по времени
	usort($files, function($a, $b) {
		return filemtime($b) - filemtime($a);
	});

	$output = "<table class='table table-striped'>";
	$output .= "<tr><th>Файл</th><th>Размер</th><th>Дата</th><th>Действия</th></tr>";

	foreach ($files as $file) {
		$basename = basename($file);
		$size = formatBytes(filesize($file));
		$date = date('Y-m-d H:i:s', filemtime($file));

		$output .= "<tr>";
		$output .= "<td>$basename</td>";
		$output .= "<td>$size</td>";
		$output .= "<td>$date</td>";
		$output .= "<td><a href='/upload/1c_catalog/$basename' target='_blank'>Скачать</a></td>";
		$output .= "</tr>";
	}

	$output .= "</table>";
	return $output;
}

/**
 * Получение детальной статистики
 */
function getDetailedStatistics() {
	global $DB;

	$iblockId = getCatalogIBlockId();
	if (!$iblockId) {
		return "Инфоблок каталога не найден";
	}

	$output = "<div class='stats-grid'>";

	// Статистика по разделам
	$query = "
        SELECT s.NAME, COUNT(e.ID) as cnt 
        FROM b_iblock_section s 
        LEFT JOIN b_iblock_element e ON s.ID = e.IBLOCK_SECTION_ID 
        WHERE s.IBLOCK_ID = $iblockId 
        GROUP BY s.ID, s.NAME 
        ORDER BY cnt DESC 
        LIMIT 10
    ";

	$result = $DB->Query($query);
	$sectionsData = array();

	while ($row = $result->Fetch()) {
		$sectionsData[] = $row;
	}

	if (!empty($sectionsData)) {
		$output .= "<div class='action-card'><h4>Товаров по разделам</h4><table class='table'>";
		foreach ($sectionsData as $section) {
			$output .= "<tr><td>{$section['NAME']}</td><td>{$section['cnt']}</td></tr>";
		}
		$output .= "</table></div>";
	}

	// Статистика цен
	$query = "
        SELECT cg.NAME, COUNT(p.ID) as cnt, AVG(p.PRICE) as avg_price
        FROM b_catalog_price p 
        JOIN b_catalog_group cg ON p.CATALOG_GROUP_ID = cg.ID
        GROUP BY cg.ID, cg.NAME
    ";

	$result = $DB->Query($query);
	$pricesData = array();

	while ($row = $result->Fetch()) {
		$pricesData[] = $row;
	}

	if (!empty($pricesData)) {
		$output .= "<div class='action-card'><h4>Статистика цен</h4><table class='table'>";
		$output .= "<tr><th>Тип цены</th><th>Количество</th><th>Средняя цена</th></tr>";
		foreach ($pricesData as $price) {
			$avgPrice = number_format($price['avg_price'], 2);
			$output .= "<tr><td>{$price['NAME']}</td><td>{$price['cnt']}</td><td>$avgPrice ₽</td></tr>";
		}
		$output .= "</table></div>";
	}

	$output .= "</div>";
	return $output;
}

/**
 * Пересоздание инфоблока каталога
 */
function recreateCatalogIBlock() {
	// Удаление старого ИБ
	$rsIBlock = CIBlock::GetList(array(), array('TYPE' => '1c_catalog'));
	while ($arIBlock = $rsIBlock->Fetch()) {
		CIBlock::Delete($arIBlock['ID']);
	}

	// Создание нового
	require_once("1c_exchange_helpers_v1.0.php");
	return getOrCreateCatalogIBlock();
}

/**
 * Экспорт статистики в Excel
 */
function exportStatisticsToExcel() {
	header('Content-Type: application/vnd.ms-excel');
	header('Content-Disposition: attachment; filename="1c_exchange_stats_' . date('Y-m-d') . '.xls"');

	echo "<table border='1'>";
	echo "<tr><th>Параметр</th><th>Значение</th></tr>";

	$stats = getExchangeStatistics();
	foreach ($stats as $key => $value) {
		echo "<tr><td>$key</td><td>$value</td></tr>";
	}

	echo "</table>";
	exit;
}

/**
 * Форматирование размера файла
 */
function formatBytes($size, $precision = 2) {
	$base = log($size, 1024);
	$suffixes = array('B', 'KB', 'MB', 'GB', 'TB');

	return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[floor($base)];
}

require_once($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
?>