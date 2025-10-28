<?php
/**
 * Newsletter Administration Module - Russian Version
 * Version: 1.1.0
 * Date: 2025-07-18
 * Description: Модуль управления подписчиками на русском языке
 * File: /local/php_interface/admin/newsletter_admin.php
 */

require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_before.php");
require_once($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_admin_after.php");

// Check permissions
if (!$USER->IsAdmin()) {
	$APPLICATION->AuthForm("Доступ запрещен");
}

// Load modules
CModule::IncludeModule("main");
CModule::IncludeModule("iblock");

/**
 * Newsletter Admin Class - Russian Version
 */
class EDSNewsletterAdmin {

	private $iblockId = null;
	private $tableName = "eds_newsletter_subscribers";

	public function __construct() {
		$this->initDatabase();
	}

	/**
	 * Initialize database table
	 */
	private function initDatabase() {
		global $DB;

		$createTableSQL = "
            CREATE TABLE IF NOT EXISTS {$this->tableName} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                email VARCHAR(255) NOT NULL UNIQUE,
                status ENUM('active', 'inactive', 'unsubscribed') DEFAULT 'active',
                subscribe_date DATETIME DEFAULT CURRENT_TIMESTAMP,
                unsubscribe_date DATETIME NULL,
                source VARCHAR(100) DEFAULT 'website',
                user_agent TEXT,
                ip_address VARCHAR(45),
                confirmed TINYINT(1) DEFAULT 1,
                INDEX idx_email (email),
                INDEX idx_status (status),
                INDEX idx_subscribe_date (subscribe_date)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";

		$DB->Query($createTableSQL);
	}

	/**
	 * Get subscribers with pagination
	 */
	public function getSubscribers($page = 1, $limit = 50, $filter = []) {
		global $DB;

		$offset = ($page - 1) * $limit;

		// Build WHERE clause
		$whereClause = "WHERE 1=1";

		if (!empty($filter['email'])) {
			$email = $DB->ForSQL($filter['email']);
			$whereClause .= " AND email LIKE '%{$email}%'";
		}

		if (!empty($filter['status'])) {
			$status = $DB->ForSQL($filter['status']);
			$whereClause .= " AND status = '{$status}'";
		}

		if (!empty($filter['date_from'])) {
			$dateFrom = $DB->ForSQL($filter['date_from']);
			$whereClause .= " AND subscribe_date >= '{$dateFrom}'";
		}

		if (!empty($filter['date_to'])) {
			$dateTo = $DB->ForSQL($filter['date_to']);
			$whereClause .= " AND subscribe_date <= '{$dateTo} 23:59:59'";
		}

		// Get total count
		$countSQL = "SELECT COUNT(*) as total FROM {$this->tableName} {$whereClause}";
		$countResult = $DB->Query($countSQL);
		$total = $countResult->Fetch()['total'];

		// Get subscribers
		$sql = "
            SELECT id, email, status, subscribe_date, unsubscribe_date, source, confirmed 
            FROM {$this->tableName} 
            {$whereClause} 
            ORDER BY subscribe_date DESC 
            LIMIT {$limit} OFFSET {$offset}
        ";

		$result = $DB->Query($sql);
		$subscribers = [];

		while ($row = $result->Fetch()) {
			$subscribers[] = $row;
		}

		return [
			'subscribers' => $subscribers,
			'total' => $total,
			'page' => $page,
			'limit' => $limit,
			'total_pages' => ceil($total / $limit)
		];
	}

	/**
	 * Add subscriber
	 */
	public function addSubscriber($email, $source = 'manual', $ipAddress = '', $userAgent = '') {
		global $DB;

		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			return ['success' => false, 'message' => 'Некорректный email адрес'];
		}

		$email = $DB->ForSQL($email);
		$source = $DB->ForSQL($source);
		$ipAddress = $DB->ForSQL($ipAddress);
		$userAgent = $DB->ForSQL($userAgent);

		$sql = "
            INSERT INTO {$this->tableName} (email, source, ip_address, user_agent) 
            VALUES ('{$email}', '{$source}', '{$ipAddress}', '{$userAgent}')
            ON DUPLICATE KEY UPDATE 
                status = 'active',
                subscribe_date = CURRENT_TIMESTAMP,
                unsubscribe_date = NULL
        ";

		$result = $DB->Query($sql);

		if ($result) {
			return ['success' => true, 'message' => 'Подписчик успешно добавлен'];
		} else {
			return ['success' => false, 'message' => 'Ошибка базы данных'];
		}
	}

	/**
	 * Update subscriber status
	 */
	public function updateSubscriberStatus($id, $status) {
		global $DB;

		$id = intval($id);
		$status = $DB->ForSQL($status);

		$unsubscribeDate = ($status === 'unsubscribed') ? ", unsubscribe_date = CURRENT_TIMESTAMP" : ", unsubscribe_date = NULL";

		$sql = "UPDATE {$this->tableName} SET status = '{$status}' {$unsubscribeDate} WHERE id = {$id}";

		$result = $DB->Query($sql);

		return $result !== false;
	}

	/**
	 * Delete subscriber
	 */
	public function deleteSubscriber($id) {
		global $DB;

		$id = intval($id);
		$sql = "DELETE FROM {$this->tableName} WHERE id = {$id}";

		$result = $DB->Query($sql);

		return $result !== false;
	}

	/**
	 * Export subscribers to CSV
	 */
	public function exportToCsv($filter = []) {
		global $DB;

		// Build WHERE clause
		$whereClause = "WHERE 1=1";

		if (!empty($filter['status'])) {
			$status = $DB->ForSQL($filter['status']);
			$whereClause .= " AND status = '{$status}'";
		}

		if (!empty($filter['date_from'])) {
			$dateFrom = $DB->ForSQL($filter['date_from']);
			$whereClause .= " AND subscribe_date >= '{$dateFrom}'";
		}

		if (!empty($filter['date_to'])) {
			$dateTo = $DB->ForSQL($filter['date_to']);
			$whereClause .= " AND subscribe_date <= '{$dateTo} 23:59:59'";
		}

		$sql = "
            SELECT email, status, subscribe_date, unsubscribe_date, source, confirmed 
            FROM {$this->tableName} 
            {$whereClause} 
            ORDER BY subscribe_date DESC
        ";

		$result = $DB->Query($sql);

		// Generate CSV filename
		$filename = 'eds_newsletter_subscribers_' . date('Y-m-d_H-i-s') . '.csv';

		// Set headers for CSV download
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="' . $filename . '"');
		header('Cache-Control: max-age=0');

		// Open output stream
		$output = fopen('php://output', 'w');

		// Add BOM for Excel compatibility
		fwrite($output, "\xEF\xBB\xBF");

		// Add CSV headers
		fputcsv($output, [
			'Email',
			'Статус',
			'Дата подписки',
			'Дата отписки',
			'Источник',
			'Подтвержден'
		], ';');

		// Add data rows
		while ($row = $result->Fetch()) {
			fputcsv($output, [
				$row['email'],
				$row['status'],
				$row['subscribe_date'],
				$row['unsubscribe_date'] ?: '',
				$row['source'],
				$row['confirmed'] ? 'Да' : 'Нет'
			], ';');
		}

		fclose($output);
		exit;
	}

	/**
	 * Get statistics
	 */
	public function getStatistics() {
		global $DB;

		$stats = [];

		// Total subscribers
		$result = $DB->Query("SELECT COUNT(*) as total FROM {$this->tableName}");
		$stats['total'] = $result->Fetch()['total'];

		// Active subscribers
		$result = $DB->Query("SELECT COUNT(*) as active FROM {$this->tableName} WHERE status = 'active'");
		$stats['active'] = $result->Fetch()['active'];

		// Unsubscribed
		$result = $DB->Query("SELECT COUNT(*) as unsubscribed FROM {$this->tableName} WHERE status = 'unsubscribed'");
		$stats['unsubscribed'] = $result->Fetch()['unsubscribed'];

		// Today's subscriptions
		$result = $DB->Query("SELECT COUNT(*) as today FROM {$this->tableName} WHERE DATE(subscribe_date) = CURDATE()");
		$stats['today'] = $result->Fetch()['today'];

		// This week's subscriptions
		$result = $DB->Query("SELECT COUNT(*) as week FROM {$this->tableName} WHERE subscribe_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)");
		$stats['week'] = $result->Fetch()['week'];

		// This month's subscriptions
		$result = $DB->Query("SELECT COUNT(*) as month FROM {$this->tableName} WHERE MONTH(subscribe_date) = MONTH(CURDATE()) AND YEAR(subscribe_date) = YEAR(CURDATE())");
		$stats['month'] = $result->Fetch()['month'];

		return $stats;
	}

	/**
	 * Import subscribers from CSV
	 */
	public function importFromCsv($csvFile) {
		global $DB;

		if (!file_exists($csvFile)) {
			return ['success' => false, 'message' => 'Файл не найден'];
		}

		$imported = 0;
		$errors = [];

		if (($handle = fopen($csvFile, "r")) !== false) {
			// Skip header row
			fgetcsv($handle, 1000, ",");

			while (($data = fgetcsv($handle, 1000, ",")) !== false) {
				$email = trim($data[0]);

				if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
					$result = $this->addSubscriber($email, 'import');
					if ($result['success']) {
						$imported++;
					} else {
						$errors[] = "Не удалось импортировать: $email";
					}
				} else {
					$errors[] = "Некорректный email: $email";
				}
			}

			fclose($handle);
		}

		return [
			'success' => true,
			'imported' => $imported,
			'errors' => $errors
		];
	}
}

// Initialize admin
$newsletterAdmin = new EDSNewsletterAdmin();

// Handle actions
$action = $_REQUEST['action'] ?? '';
$message = '';
$messageType = '';

switch ($action) {
	case 'add':
		if (!empty($_POST['email'])) {
			$result = $newsletterAdmin->addSubscriber($_POST['email'], 'manual', $_SERVER['REMOTE_ADDR'] ?? '', $_SERVER['HTTP_USER_AGENT'] ?? '');
			$message = $result['message'];
			$messageType = $result['success'] ? 'success' : 'error';
		}
		break;

	case 'update_status':
		if (!empty($_POST['id']) && !empty($_POST['status'])) {
			$result = $newsletterAdmin->updateSubscriberStatus($_POST['id'], $_POST['status']);
			$message = $result ? 'Статус успешно обновлен' : 'Не удалось обновить статус';
			$messageType = $result ? 'success' : 'error';
		}
		break;

	case 'delete':
		if (!empty($_POST['id'])) {
			$result = $newsletterAdmin->deleteSubscriber($_POST['id']);
			$message = $result ? 'Подписчик успешно удален' : 'Не удалось удалить подписчика';
			$messageType = $result ? 'success' : 'error';
		}
		break;

	case 'export':
		$filter = [
			'status' => $_GET['filter_status'] ?? '',
			'date_from' => $_GET['filter_date_from'] ?? '',
			'date_to' => $_GET['filter_date_to'] ?? ''
		];
		$newsletterAdmin->exportToCsv($filter);
		break;

	case 'import':
		if (!empty($_FILES['csv_file']['tmp_name'])) {
			$result = $newsletterAdmin->importFromCsv($_FILES['csv_file']['tmp_name']);
			$message = "Импортировано: {$result['imported']} подписчиков";
			if (!empty($result['errors'])) {
				$message .= ". Ошибки: " . implode(', ', array_slice($result['errors'], 0, 5));
			}
			$messageType = 'success';
		}
		break;
}

// Get filter parameters
$filter = [
	'email' => $_GET['filter_email'] ?? '',
	'status' => $_GET['filter_status'] ?? '',
	'date_from' => $_GET['filter_date_from'] ?? '',
	'date_to' => $_GET['filter_date_to'] ?? ''
];

// Get page and limit
$page = intval($_GET['page'] ?? 1);
$limit = intval($_GET['limit'] ?? 50);

// Get subscribers
$data = $newsletterAdmin->getSubscribers($page, $limit, $filter);
$stats = $newsletterAdmin->getStatistics();

// Build filter query string
$filterQuery = '';
foreach ($filter as $key => $value) {
	if (!empty($value)) {
		$filterQuery .= "&filter_{$key}=" . urlencode($value);
	}
}

$APPLICATION->SetTitle("Управление подписчиками рассылки EDS");
?>

    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Управление подписчиками EDS</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                margin: 0;
                padding: 20px;
                background: #f5f5f5;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                background: white;
                padding: 30px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            }

            h1 {
                color: #333;
                margin-bottom: 30px;
                border-bottom: 2px solid #ff2545;
                padding-bottom: 10px;
            }

            .stats {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                margin-bottom: 30px;
            }

            .stat-card {
                background: linear-gradient(135deg, #ff2545, #ff4567);
                color: white;
                padding: 20px;
                border-radius: 8px;
                text-align: center;
            }

            .stat-number {
                font-size: 2em;
                font-weight: bold;
                margin-bottom: 5px;
            }

            .stat-label {
                font-size: 0.9em;
                opacity: 0.9;
            }

            .actions {
                display: flex;
                gap: 15px;
                margin-bottom: 30px;
                flex-wrap: wrap;
            }

            .btn {
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
                text-decoration: none;
                display: inline-block;
                font-size: 14px;
                transition: background-color 0.3s;
            }

            .btn-primary {
                background: #ff2545;
                color: white;
            }

            .btn-primary:hover {
                background: #e61e3d;
            }

            .btn-secondary {
                background: #6c757d;
                color: white;
            }

            .btn-secondary:hover {
                background: #5a6268;
            }

            .btn-success {
                background: #28a745;
                color: white;
            }

            .btn-success:hover {
                background: #218838;
            }

            .btn-danger {
                background: #dc3545;
                color: white;
            }

            .btn-danger:hover {
                background: #c82333;
            }

            .btn-small {
                padding: 5px 10px;
                font-size: 12px;
            }

            .filters {
                background: #f8f9fa;
                padding: 20px;
                border-radius: 8px;
                margin-bottom: 30px;
            }

            .filter-row {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 15px;
                margin-bottom: 15px;
            }

            .form-group {
                display: flex;
                flex-direction: column;
            }

            .form-group label {
                margin-bottom: 5px;
                font-weight: bold;
                color: #333;
            }

            .form-group input, .form-group select {
                padding: 8px 12px;
                border: 1px solid #ddd;
                border-radius: 4px;
                font-size: 14px;
            }

            .form-group input:focus, .form-group select:focus {
                outline: none;
                border-color: #ff2545;
            }

            .table-container {
                overflow-x: auto;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 20px;
            }

            th, td {
                padding: 12px;
                text-align: left;
                border-bottom: 1px solid #ddd;
            }

            th {
                background: #f8f9fa;
                font-weight: bold;
                color: #333;
            }

            tr:hover {
                background: #f8f9fa;
            }

            .status-active {
                color: #28a745;
                font-weight: bold;
            }

            .status-inactive {
                color: #6c757d;
                font-weight: bold;
            }

            .status-unsubscribed {
                color: #dc3545;
                font-weight: bold;
            }

            .pagination {
                display: flex;
                justify-content: center;
                gap: 10px;
                margin-top: 20px;
            }

            .pagination a {
                padding: 8px 12px;
                text-decoration: none;
                border: 1px solid #ddd;
                border-radius: 4px;
                color: #333;
            }

            .pagination a:hover {
                background: #f8f9fa;
            }

            .pagination .active {
                background: #ff2545;
                color: white;
                border-color: #ff2545;
            }

            .message {
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 5px;
                border: 1px solid;
            }

            .message.success {
                background: #d4edda;
                color: #155724;
                border-color: #c3e6cb;
            }

            .message.error {
                background: #f8d7da;
                color: #721c24;
                border-color: #f5c6cb;
            }

            .modal {
                display: none;
                position: fixed;
                z-index: 1000;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,0,0,0.5);
            }

            .modal-content {
                background: white;
                margin: 15% auto;
                padding: 20px;
                border-radius: 8px;
                width: 90%;
                max-width: 500px;
            }

            .close {
                float: right;
                font-size: 28px;
                font-weight: bold;
                cursor: pointer;
            }

            .close:hover {
                color: #ff2545;
            }

            @media (max-width: 768px) {
                .container {
                    padding: 15px;
                }

                .actions {
                    flex-direction: column;
                }

                .filter-row {
                    grid-template-columns: 1fr;
                }

                table {
                    font-size: 14px;
                }

                th, td {
                    padding: 8px;
                }
            }
        </style>
    </head>
    <body>
    <div class="container">
        <h1>Управление подписчиками рассылки EDS</h1>

		<?php if ($message): ?>
            <div class="message <?= $messageType ?>">
				<?= htmlspecialchars($message) ?>
            </div>
		<?php endif; ?>

        <!-- Statistics -->
        <div class="stats">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total'] ?></div>
                <div class="stat-label">Всего подписчиков</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['active'] ?></div>
                <div class="stat-label">Активных подписчиков</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['unsubscribed'] ?></div>
                <div class="stat-label">Отписавшихся</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['today'] ?></div>
                <div class="stat-label">Сегодня</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['week'] ?></div>
                <div class="stat-label">За неделю</div>
            </div>
            <div class="stat-card">
                <div class="stat-number"><?= $stats['month'] ?></div>
                <div class="stat-label">За месяц</div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <button class="btn btn-primary" onclick="openModal('addModal')">Добавить подписчика</button>
            <a href="?action=export<?= $filterQuery ?>" class="btn btn-success">Экспорт в CSV</a>
            <button class="btn btn-secondary" onclick="openModal('importModal')">Импорт из CSV</button>
        </div>

        <!-- Filters -->
        <div class="filters">
            <form method="GET">
                <div class="filter-row">
                    <div class="form-group">
                        <label for="filter_email">Email:</label>
                        <input type="text" id="filter_email" name="filter_email" value="<?= htmlspecialchars($filter['email']) ?>" placeholder="Поиск по email">
                    </div>
                    <div class="form-group">
                        <label for="filter_status">Статус:</label>
                        <select id="filter_status" name="filter_status">
                            <option value="">Все</option>
                            <option value="active" <?= $filter['status'] === 'active' ? 'selected' : '' ?>>Активные</option>
                            <option value="inactive" <?= $filter['status'] === 'inactive' ? 'selected' : '' ?>>Неактивные</option>
                            <option value="unsubscribed" <?= $filter['status'] === 'unsubscribed' ? 'selected' : '' ?>>Отписавшиеся</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="filter_date_from">С даты:</label>
                        <input type="date" id="filter_date_from" name="filter_date_from" value="<?= htmlspecialchars($filter['date_from']) ?>">
                    </div>
                    <div class="form-group">
                        <label for="filter_date_to">По дату:</label>
                        <input type="date" id="filter_date_to" name="filter_date_to" value="<?= htmlspecialchars($filter['date_to']) ?>">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">Применить фильтр</button>
                <a href="?" class="btn btn-secondary">Очистить</a>
            </form>
        </div>

        <!-- Subscribers Table -->
        <div class="table-container">
            <table>
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Email</th>
                    <th>Статус</th>
                    <th>Дата подписки</th>
                    <th>Дата отписки</th>
                    <th>Источник</th>
                    <th>Подтвержден</th>
                    <th>Действия</th>
                </tr>
                </thead>
                <tbody>
				<?php foreach ($data['subscribers'] as $subscriber): ?>
                    <tr>
                        <td><?= $subscriber['id'] ?></td>
                        <td><?= htmlspecialchars($subscriber['email']) ?></td>
                        <td class="status-<?= $subscriber['status'] ?>">
							<?php
							$statusLabels = [
								'active' => 'Активный',
								'inactive' => 'Неактивный',
								'unsubscribed' => 'Отписался'
							];
							echo $statusLabels[$subscriber['status']] ?? ucfirst($subscriber['status']);
							?>
                        </td>
                        <td><?= date('d.m.Y H:i', strtotime($subscriber['subscribe_date'])) ?></td>
                        <td><?= $subscriber['unsubscribe_date'] ? date('d.m.Y H:i', strtotime($subscriber['unsubscribe_date'])) : '-' ?></td>
                        <td><?= htmlspecialchars($subscriber['source']) ?></td>
                        <td><?= $subscriber['confirmed'] ? 'Да' : 'Нет' ?></td>
                        <td>
                            <select onchange="updateStatus(<?= $subscriber['id'] ?>, this.value)" class="btn-small">
                                <option value="active" <?= $subscriber['status'] === 'active' ? 'selected' : '' ?>>Активный</option>
                                <option value="inactive" <?= $subscriber['status'] === 'inactive' ? 'selected' : '' ?>>Неактивный</option>
                                <option value="unsubscribed" <?= $subscriber['status'] === 'unsubscribed' ? 'selected' : '' ?>>Отписался</option>
                            </select>
                            <button class="btn btn-danger btn-small" onclick="deleteSubscriber(<?= $subscriber['id'] ?>)">Удалить</button>
                        </td>
                    </tr>
				<?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
		<?php if ($data['total_pages'] > 1): ?>
            <div class="pagination">
				<?php for ($i = 1; $i <= $data['total_pages']; $i++): ?>
                    <a href="?page=<?= $i ?><?= $filterQuery ?>" class="<?= $i === $page ? 'active' : '' ?>">
						<?= $i ?>
                    </a>
				<?php endfor; ?>
            </div>
		<?php endif; ?>

        <p>Показано <?= count($data['subscribers']) ?> из <?= $data['total'] ?> подписчиков</p>
    </div>

    <!-- Add Subscriber Modal -->
    <div id="addModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('addModal')">&times;</span>
            <h2>Добавить нового подписчика</h2>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" required placeholder="Введите email адрес">
                </div>
                <button type="submit" class="btn btn-primary">Добавить подписчика</button>
            </form>
        </div>
    </div>

    <!-- Import CSV Modal -->
    <div id="importModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal('importModal')">&times;</span>
            <h2>Импорт подписчиков из CSV</h2>
            <p>Формат CSV: email в каждой строке или через запятую</p>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="import">
                <div class="form-group">
                    <label for="csv_file">CSV файл:</label>
                    <input type="file" id="csv_file" name="csv_file" accept=".csv" required>
                </div>
                <button type="submit" class="btn btn-primary">Импортировать</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = 'block';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = 'none';
        }

        function updateStatus(id, status) {
            if (confirm('Вы уверены, что хотите изменить статус?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="update_status">
                    <input type="hidden" name="id" value="${id}">
                    <input type="hidden" name="status" value="${status}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        function deleteSubscriber(id) {
            if (confirm('Вы уверены, что хотите удалить этого подписчика?')) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="id" value="${id}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
    </script>
    </body>
    </html>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/epilog_admin.php");
?>