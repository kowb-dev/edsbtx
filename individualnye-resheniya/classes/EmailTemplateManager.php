<?php
/**
 * Email Template Manager for EDS Individual Solutions
 * File: /individualnye-resheniya/classes/EmailTemplateManager.php
 * Version: 2.1 - Fixed email encoding and admin notifications
 */

class EmailTemplateManager {

	private $translations;

	public function __construct() {
		$this->translations = [
			'theater' => 'Театр',
			'concert' => 'Концертная площадка',
			'studio' => 'Студия',
			'event' => 'Выездное мероприятие',
			'conference' => 'Конференц-зал',
			'other' => 'Другое',
			'permanent' => 'Стационарная установка',
			'temporary' => 'Временная установка',
			'mobile' => 'Мобильная система',
			'1' => 'До 50 человек',
			'2' => '50-200 человек',
			'3' => '200-500 человек',
			'4' => '500-1000 человек',
			'5' => '1000-5000 человек',
			'6' => '5000+ человек',
			'low' => 'До 10 кВт',
			'medium' => '10-50 кВт',
			'high' => '50-200 кВт',
			'very-high' => 'Свыше 200 кВт',
			'220v' => '220В (однофазная сеть)',
			'380v' => '380В (трёхфазная сеть)',
			'unknown' => 'Не знаю',
			'urgent' => 'Срочно (1-2 недели)',
			'fast' => 'Быстро (до месяца)',
			'standard' => 'Стандартно (2-3 месяца)',
			'flexible' => 'Гибко (без срочности)',
			'power-distributors' => 'Дистрибьюторы питания',
			'dmx-equipment' => 'DMX-оборудование',
			'winch-controls' => 'Пульты управления лебёдками',
			'stage-boxes' => 'Лючки и коммутационные коробки',
			'sequencers' => 'Секвенсоры',
			'cables-connectors' => 'Кабели и разъёмы',
			'low_budget' => 'До 500 000 ₽',
			'medium_budget' => '500 000 - 2 000 000 ₽',
			'high_budget' => '2 000 000 - 5 000 000 ₽',
			'premium_budget' => 'Свыше 5 000 000 ₽',
			'flexible_budget' => 'Гибкий бюджет',
			'mobility' => 'Повышенная мобильность',
			'safety' => 'Повышенные требования безопасности',
			'weather-resistance' => 'Защита от внешних воздействий',
			'remote-control' => 'Дистанционное управление',
			'monitoring' => 'Мониторинг параметров',
			'backup-power' => 'Резервное питание',
			'time-limited' => 'Ограниченное время монтажа',
			'noise-restrictions' => 'Ограничения по шуму',
			'access-restrictions' => 'Ограниченный доступ',
			'operating-restrictions' => 'Работа в действующем объекте',
			'email' => 'Email',
			'phone-call' => 'Звонок менеджера'
		];
	}

	private function translateValue($value) {
		return $this->translations[$value] ?? $value;
	}

	private function formatFileSize($bytes) {
		if ($bytes === 0) return '0 Bytes';
		$k = 1024;
		$sizes = ['Bytes', 'KB', 'MB', 'GB'];
		$i = floor(log($bytes) / log($k));
		return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
	}

	public function generateAdminEmail($formData, $requestId, $uploadedFiles = []) {
		$priority = in_array($formData['timeline'] ?? '', ['urgent', 'fast']) ? 'ВЫСОКИЙ' : 'ОБЫЧНЫЙ';
		$priorityColor = $priority === 'ВЫСОКИЙ' ? '#ff2545' : '#00cc99';

		$html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новое техническое задание #' . htmlspecialchars($requestId) . '</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 800px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #1a1a1a 0%, #ff2545 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .header .subtitle { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #ff2545; border-bottom: 2px solid #ff2545; padding-bottom: 8px; margin-bottom: 20px; font-size: 18px; }
        .info-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .info-item { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #ff2545; }
        .info-item strong { color: #333; display: block; margin-bottom: 4px; }
        .info-item span { color: #666; }
        .priority { display: inline-block; padding: 6px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; color: white; background: ' . $priorityColor . '; }
        .equipment-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 10px; }
        .equipment-item { background: #e8f5e8; padding: 12px; border-radius: 6px; border-left: 3px solid #00cc99; }
        .files-list { background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; }
        .file-item { display: flex; justify-content: space-between; align-items: center; padding: 8px 0; border-bottom: 1px solid #f0f0f0; }
        .file-item:last-child { border-bottom: none; }
        .footer { background: #1a1a1a; color: white; padding: 20px; text-align: center; font-size: 14px; }
        .action-btn { display: inline-block; background: #ff2545; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 10px; }
        .urgent-notice { background: #ff2545; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 20px; }
        @media (max-width: 600px) {
            .info-grid { grid-template-columns: 1fr; }
            .equipment-list { grid-template-columns: 1fr; }
            .content { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>⚡ Новое техническое задание</h1>
            <div class="subtitle">ID: ' . htmlspecialchars($requestId) . ' | Дата: ' . date('d.m.Y H:i:s') . '</div>
            <div style="margin-top: 10px;">
                <span class="priority">Приоритет: ' . $priority . '</span>
            </div>
        </div>';

		// Urgent notice for high priority requests
		if ($priority === 'ВЫСОКИЙ') {
			$html .= '<div class="urgent-notice">
                <h3 style="margin: 0 0 10px 0;">🚨 СРОЧНАЯ ЗАЯВКА</h3>
                <p style="margin: 0;">Клиент указал срочные сроки выполнения. Требуется немедленное внимание!</p>
            </div>';
		}

		$html .= '<div class="content">
            <div class="section">
                <h2>👤 Контактная информация</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Контактное лицо:</strong>
                        <span>' . htmlspecialchars($formData['contactName']) . '</span>
                    </div>
                    <div class="info-item">
                        <strong>Телефон:</strong>
                        <span><a href="tel:' . preg_replace('/[^+\d]/', '', $formData['contactPhone']) . '" style="color: #ff2545; text-decoration: none;">' . htmlspecialchars($formData['contactPhone']) . '</a></span>
                    </div>
                    <div class="info-item">
                        <strong>Email:</strong>
                        <span><a href="mailto:' . htmlspecialchars($formData['contactEmail']) . '" style="color: #ff2545; text-decoration: none;">' . htmlspecialchars($formData['contactEmail']) . '</a></span>
                    </div>';

		if (!empty($formData['contactCompany'])) {
			$html .= '<div class="info-item">
                        <strong>Компания:</strong>
                        <span>' . htmlspecialchars($formData['contactCompany']) . '</span>
                    </div>';
		}

		if (!empty($formData['contactPosition'])) {
			$html .= '<div class="info-item">
                        <strong>Должность:</strong>
                        <span>' . htmlspecialchars($formData['contactPosition']) . '</span>
                    </div>';
		}

		$html .= '</div>
            </div>
            
            <div class="section">
                <h2>🏢 Параметры проекта</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <strong>Тип объекта:</strong>
                        <span>' . $this->translateValue($formData['objectType']) . '</span>
                    </div>
                    <div class="info-item">
                        <strong>Тип инсталляции:</strong>
                        <span>' . $this->translateValue($formData['installationType']) . '</span>
                    </div>
                    <div class="info-item">
                        <strong>Размер аудитории:</strong>
                        <span>' . $this->translateValue($formData['audienceSize']) . '</span>
                    </div>';

		if (!empty($formData['performersCount'])) {
			$html .= '<div class="info-item">
                        <strong>Исполнители:</strong>
                        <span>' . $this->translateValue($formData['performersCount']) . '</span>
                    </div>';
		}

		$html .= '<div class="info-item">
                        <strong>Требуемая мощность:</strong>
                        <span>' . $this->translateValue($formData['powerRequirement']) . '</span>
                    </div>';

		if (!empty($formData['powerConnection'])) {
			$html .= '<div class="info-item">
                        <strong>Тип питания:</strong>
                        <span>' . $this->translateValue($formData['powerConnection']) . '</span>
                    </div>';
		}

		$html .= '<div class="info-item">
                        <strong>Сроки:</strong>
                        <span>' . $this->translateValue($formData['timeline']) . '</span>
                    </div>';

		if (!empty($formData['budget'])) {
			$html .= '<div class="info-item">
                        <strong>Бюджет:</strong>
                        <span>' . $this->translateValue($formData['budget']) . '</span>
                    </div>';
		}

		$html .= '</div>
            </div>';

		// Equipment section
		if (!empty($formData['equipment']) && is_array($formData['equipment'])) {
			$html .= '<div class="section">
                <h2>⚙️ Требуемое оборудование</h2>
                <div class="equipment-list">';
			foreach ($formData['equipment'] as $equipment) {
				$html .= '<div class="equipment-item">
                    <strong>' . $this->translateValue($equipment) . '</strong>
                </div>';
			}
			$html .= '</div>
            </div>';
		}

		// Requirements section
		if (!empty($formData['requirements']) && is_array($formData['requirements'])) {
			$html .= '<div class="section">
                <h2>📋 Особые требования</h2>
                <div class="equipment-list">';
			foreach ($formData['requirements'] as $requirement) {
				$html .= '<div class="equipment-item">
                    <strong>' . $this->translateValue($requirement) . '</strong>
                </div>';
			}
			$html .= '</div>
            </div>';
		}

		// Installation limitations section
		if (!empty($formData['installationLimitations']) && is_array($formData['installationLimitations'])) {
			$html .= '<div class="section">
                <h2>⚠️ Ограничения по установке</h2>
                <div class="equipment-list">';
			foreach ($formData['installationLimitations'] as $limitation) {
				$html .= '<div class="equipment-item">
                    <strong>' . $this->translateValue($limitation) . '</strong>
                </div>';
			}
			$html .= '</div>
            </div>';
		}

		// Additional info section
		if (!empty($formData['additionalInfo'])) {
			$html .= '<div class="section">
                <h2>💬 Дополнительная информация</h2>
                <div class="info-item">
                    <p style="margin: 0; white-space: pre-wrap;">' . htmlspecialchars($formData['additionalInfo']) . '</p>
                </div>
            </div>';
		}

		// Files section
		if (!empty($uploadedFiles)) {
			$html .= '<div class="section">
                <h2>📎 Прикрепленные файлы</h2>
                <div class="files-list">
                    <p style="margin: 0 0 15px 0;"><strong>Загружено файлов: ' . count($uploadedFiles) . '</strong></p>';
			foreach ($uploadedFiles as $file) {
				$html .= '<div class="file-item">
                    <div>
                        <strong>' . htmlspecialchars($file['original_name']) . '</strong><br>
                        <small style="color: #666;">Сохранен как: ' . htmlspecialchars($file['file_name']) . '</small>
                    </div>
                    <div style="text-align: right;">
                        <span style="color: #666;">' . $this->formatFileSize($file['file_size']) . '</span><br>
                        <small style="color: #999; text-transform: uppercase;">' . strtoupper($file['file_type']) . '</small>
                    </div>
                </div>';
			}
			$html .= '</div>
            </div>';
		}

		// Delivery methods section
		if (!empty($formData['deliveryMethod']) && is_array($formData['deliveryMethod'])) {
			$html .= '<div class="section">
                <h2>📞 Способы связи</h2>
                <div class="info-item">
                    <strong>Предпочтительные способы получения ТЗ:</strong><br>';
			foreach ($formData['deliveryMethod'] as $method) {
				$html .= '<span style="display: inline-block; background: #e8f5e8; padding: 4px 8px; border-radius: 4px; margin: 2px; font-size: 12px;">' . $this->translateValue($method) . '</span> ';
			}
			$html .= '</div>
            </div>';
		}

		// Action buttons
		$html .= '<div class="section" style="text-align: center; background: #f8f9fa; padding: 30px; border-radius: 10px;">
            <h2 style="margin-bottom: 20px; color: #333;">🚀 Действия</h2>
            <a href="tel:' . preg_replace('/[^+\d]/', '', $formData['contactPhone']) . '" class="action-btn">
                📞 Позвонить клиенту
            </a>
            <a href="mailto:' . htmlspecialchars($formData['contactEmail']) . '?subject=Техническое задание ' . htmlspecialchars($requestId) . '" class="action-btn">
                ✉️ Написать email
            </a>
        </div>
        
        </div>
        
        <div class="footer">
            <p style="margin: 0;"><strong>EDS - Electric Distribution Systems</strong></p>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">
                Система уведомлений v2.1 | Сгенерировано: ' . date('d.m.Y H:i:s') . '
            </p>
        </div>
    </div>
</body>
</html>';

		return $html;
	}

	public function generateClientEmail($formData, $requestId, $uploadedFiles = []) {
		$html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ваше техническое задание получено - EDS</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #1a1a1a 0%, #ff2545 100%); color: white; padding: 40px 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 28px; font-weight: 700; }
        .header .subtitle { opacity: 0.9; margin-top: 10px; font-size: 16px; }
        .content { padding: 40px 30px; }
        .success-icon { width: 80px; height: 80px; background: #00cc99; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 30px; font-size: 40px; }
        .section { margin-bottom: 30px; }
        .section h2 { color: #ff2545; margin-bottom: 15px; font-size: 20px; }
        .info-box { background: #f8f9fa; padding: 20px; border-radius: 8px; border-left: 4px solid #ff2545; margin-bottom: 20px; }
        .timeline { background: #e8f5e8; padding: 20px; border-radius: 8px; border-left: 4px solid #00cc99; }
        .timeline h3 { margin: 0 0 10px 0; color: #00cc99; }
        .timeline-item { display: flex; align-items: center; margin-bottom: 10px; }
        .timeline-item:last-child { margin-bottom: 0; }
        .timeline-number { background: #00cc99; color: white; width: 24px; height: 24px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 12px; font-weight: bold; margin-right: 15px; }
        .contact-info { background: #fff3cd; padding: 20px; border-radius: 8px; border-left: 4px solid #ffc107; }
        .footer { background: #1a1a1a; color: white; padding: 30px; text-align: center; }
        .footer a { color: #ff2545; text-decoration: none; }
        @media (max-width: 600px) {
            .content { padding: 30px 20px; }
            .header { padding: 30px 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ Заявка получена!</h1>
            <div class="subtitle">Спасибо за ваше обращение в EDS</div>
        </div>
        
        <div class="content">
            <div class="success-icon">✓</div>
            
            <div class="section">
                <p style="font-size: 18px; text-align: center; margin-bottom: 30px;">
                    Здравствуйте, <strong>' . htmlspecialchars($formData['contactName']) . '</strong>!
                </p>
                
                <div class="info-box">
                    <h2 style="margin: 0 0 15px 0;">📋 Ваше техническое задание</h2>
                    <p style="margin: 0;"><strong>Номер заявки:</strong> ' . htmlspecialchars($requestId) . '</p>
                    <p style="margin: 5px 0 0 0;"><strong>Дата создания:</strong> ' . date('d.m.Y H:i:s') . '</p>
                    ' . (!empty($uploadedFiles) ? '<p style="margin: 5px 0 0 0;"><strong>Прикреплено файлов:</strong> ' . count($uploadedFiles) . '</p>' : '') . '
                </div>
                
                <p>Ваша заявка на разработку технического задания успешно получена и принята в работу. Наши специалисты уже приступили к её рассмотрению.</p>
            </div>
            
            <div class="section">
                <div class="timeline">
                    <h3>🕐 Что происходит дальше?</h3>
                    <div class="timeline-item">
                        <div class="timeline-number">1</div>
                        <div>
                            <strong>Анализ заявки</strong><br>
                            <small>Наши инженеры изучают ваши требования и техническую документацию</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-number">2</div>
                        <div>
                            <strong>Консультация</strong><br>
                            <small>Менеджер свяжется с вами для уточнения деталей проекта</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-number">3</div>
                        <div>
                            <strong>Подготовка предложения</strong><br>
                            <small>Формируем детальное техническое задание и коммерческое предложение</small>
                        </div>
                    </div>
                    <div class="timeline-item">
                        <div class="timeline-number">4</div>
                        <div>
                            <strong>Согласование</strong><br>
                            <small>Обсуждаем с вами все детали и вносим необходимые корректировки</small>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="section">
                <h2>⏰ Ожидаемые сроки</h2>
                <p>
                    ' . ($this->translateValue($formData['timeline']) === 'Срочно (1-2 недели)' ?
				'Учитывая срочность вашей заявки, наш менеджер свяжется с вами в течение <strong>2-3 часов</strong>.' :
				'Наш менеджер свяжется с вами в течение <strong>1 рабочего дня</strong> для уточнения деталей проекта.') . '
                </p>
                <p>Готовое техническое задание с коммерческим предложением будет направлено в указанные вами сроки: <strong>' . $this->translateValue($formData['timeline']) . '</strong></p>
            </div>
            
            <div class="section">
                <div class="contact-info">
                    <h2 style="margin: 0 0 15px 0;">📞 Контактная информация</h2>
                    <p style="margin: 0;"><strong>Менеджер проекта:</strong> EDS Technical Team</p>
                    <p style="margin: 5px 0;"><strong>Email:</strong> <a href="mailto:orders@edsy.ru" style="color: #ff2545;">orders@edsy.ru</a></p>
                    <p style="margin: 5px 0 0 0;"><strong>Телефон:</strong> +7 (495) 123-45-67</p>
                </div>
            </div>
            
            <div class="section">
                <h2>💡 Дополнительная информация</h2>
                <p>Пока мы готовим ваше предложение, рекомендуем:</p>
                <ul style="padding-left: 20px;">
                    <li>Подготовить дополнительные чертежи или схемы, если они появятся</li>
                    <li>Уточнить технические требования к оборудованию</li>
                    <li>Определить бюджетные рамки для более точного расчета</li>
                </ul>
            </div>
        </div>
        
        <div class="footer">
            <p style="margin: 0 0 15px 0;"><strong>EDS - Electric Distribution Systems</strong></p>
            <p style="margin: 0 0 15px 0; opacity: 0.8;">
                Профессиональное электрооборудование для сцены и мероприятий
            </p>
            <p style="margin: 0; font-size: 14px; opacity: 0.7;">
                Сайт: <a href="https://edsy.ru">edsy.ru</a> | 
                Email: <a href="mailto:info@edsy.ru">info@edsy.ru</a>
            </p>
        </div>
    </div>
</body>
</html>';

		return $html;
	}

	public function generateConsultationEmail($formData, $consultationId) {
		$html = '<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Запрос на консультацию #' . htmlspecialchars($consultationId) . '</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif; line-height: 1.6; color: #333; margin: 0; padding: 0; background-color: #f8f9fa; }
        .container { max-width: 600px; margin: 0 auto; background: white; }
        .header { background: linear-gradient(135deg, #ff2545 0%, #ff6b35 100%); color: white; padding: 30px; text-align: center; }
        .header h1 { margin: 0; font-size: 24px; font-weight: 700; }
        .header .subtitle { opacity: 0.9; margin-top: 8px; font-size: 14px; }
        .content { padding: 30px; }
        .urgent-notice { background: #ff2545; color: white; padding: 20px; border-radius: 8px; text-align: center; margin-bottom: 30px; }
        .urgent-notice h2 { margin: 0 0 10px 0; font-size: 20px; }
        .info-item { background: #f8f9fa; padding: 15px; border-radius: 8px; border-left: 4px solid #ff2545; margin-bottom: 15px; }
        .info-item strong { color: #333; display: block; margin-bottom: 4px; }
        .info-item span { color: #666; }
        .action-btn { display: inline-block; background: #ff2545; color: white; padding: 15px 25px; text-decoration: none; border-radius: 6px; font-weight: bold; margin: 10px 5px; }
        .footer { background: #1a1a1a; color: white; padding: 20px; text-align: center; font-size: 14px; }
        @media (max-width: 600px) {
            .content { padding: 20px; }
            .header { padding: 20px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>📞 Запрос на консультацию</h1>
            <div class="subtitle">ID: ' . htmlspecialchars($consultationId) . ' | ' . date('d.m.Y H:i:s') . '</div>
        </div>
        
        <div class="content">
            <div class="urgent-notice">
                <h2>🚨 Срочно требуется действие!</h2>
                <p style="margin: 0;">Клиент ожидает звонка в ближайшее время!</p>
            </div>
            
            <div class="info-item">
                <strong>Имя клиента:</strong>
                <span style="font-size: 18px; font-weight: 600; color: #ff2545;">' . htmlspecialchars($formData['consultationName']) . '</span>
            </div>
            
            <div class="info-item">
                <strong>Телефон:</strong>
                <span style="font-size: 18px; font-weight: 600;">
                    <a href="tel:' . preg_replace('/[^+\d]/', '', $formData['consultationPhone']) . '" style="color: #ff2545; text-decoration: none;">
                        ' . htmlspecialchars($formData['consultationPhone']) . '
                    </a>
                </span>
            </div>
            
            <div class="info-item">
                <strong>Дата запроса:</strong>
                <span>' . date('d.m.Y H:i:s') . '</span>
            </div>
            
            <div class="info-item">
                <strong>IP адрес:</strong>
                <span>' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown') . '</span>
            </div>
            
            <div style="text-align: center; margin-top: 30px;">
                <a href="tel:' . preg_replace('/[^+\d]/', '', $formData['consultationPhone']) . '" class="action-btn">
                    📞 Позвонить сейчас
                </a>
                <a href="mailto:' . (isset($formData['consultationEmail']) ? htmlspecialchars($formData['consultationEmail']) : 'client@example.com') . '?subject=Консультация EDS" class="action-btn">
                    ✉️ Написать email
                </a>
            </div>
        </div>
        
        <div class="footer">
            <p style="margin: 0;"><strong>EDS - Electric Distribution Systems</strong></p>
            <p style="margin: 5px 0 0 0; opacity: 0.8;">
                Система уведомлений v2.1 | Автоматическое уведомление
            </p>
        </div>
    </div>
</body>
</html>';

		return $html;
	}
}