<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

// Set page title and meta
$APPLICATION->SetTitle("Сертификаты - EDS");
$APPLICATION->SetPageProperty("title", "Сертификаты - EDS");
$APPLICATION->SetPageProperty("description", "Сертификаты соответствия. Дистрибьюторы питания, лючки, сплиттеры, мультикоры и многое другое от компании EDS");
$APPLICATION->SetPageProperty("keywords", "сертификаты EDS, сертификаты соответствия, дистрибьюторы питания, электрооборудование, сцена, театр");

// Certificates data array
$certificates = [
	[
		'id' => 1,
		'title' => 'Сертификат соответствия блоков и пультов управления',
		'category' => 'bloki-pulty',
		'category_name' => 'Блоки и пульты',
		'image' => '/images/certs/bloki-i-pulty.jpg',
		'description' => 'Сертификат соответствия на блоки управления мощностью и пульты управления серий PB, пульты управления серии PBC',
		'type' => 'Сертификат соответствия',
		'valid_until' => '17.11.2025'
	],
	[
		'id' => 2,
		'title' => 'Декларация соответствия низковольтного оборудования',
		'category' => 'nizkovoltnoe',
		'category_name' => 'Низковольтное оборудование',
		'image' => '/images/certs/deklaratsiya-sootvetstviya.jpg',
		'description' => 'Декларация соответствия на низковольтные комплектные устройства: блоки управления мощностью, серии PB, пульты управления',
		'type' => 'Декларация соответствия',
		'valid_until' => '03.09.2025'
	],
	[
		'id' => 3,
		'title' => 'Информационное письмо об исключении из обязательной сертификации',
		'category' => 'otkaznoe',
		'category_name' => 'Отказные письма',
		'image' => '/images/certs/otkaznoe-pismo-eds.jpg',
		'description' => 'Информационное письмо о том, что продукция не подлежит обязательной сертификации согласно единому перечню',
		'type' => 'Информационное письмо',
		'valid_until' => ''
	],
	[
		'id' => 4,
		'title' => 'Сертификат соответствия устройств распределения электроэнергии',
		'category' => 'distribyutory',
		'category_name' => 'Дистрибьюторы питания',
		'image' => '/images/certs/sertifikat-distribyutorov-pitaniya.jpg',
		'description' => 'Сертификат соответствия на устройства распределения электроэнергии (смотри приложение - бланк № 0450197)',
		'type' => 'Сертификат соответствия',
		'valid_until' => '30.11.2022'
	],
	[
		'id' => 5,
		'title' => 'Сертификат соответствия кабельной продукции',
		'category' => 'kabeli',
		'category_name' => 'Кабельная продукция',
		'image' => '/images/certs/sertifikat-kabelnoy-produktsii.jpg',
		'description' => 'Сертификат соответствия на удлинители сигнальные серии CS, удлинители силовые серии CP',
		'type' => 'Сертификат соответствия',
		'valid_until' => '12.04.2023'
	]
];
?>

    <div class="edsys-certificates-page">
        <!-- Breadcrumbs -->
        <div class="edsys-breadcrumbs">
            <div class="edsys-container">
                <nav aria-label="Хлебные крошки">
                    <a href="/" class="edsys-breadcrumb-link">Главная</a>
                    <span class="edsys-breadcrumb-separator">›</span>
                    <span class="edsys-breadcrumb-current">Сертификаты</span>
                </nav>
            </div>
        </div>

        <!-- Hero Section -->
        <section class="edsys-hero-section">
            <div class="edsys-container">
                <div class="edsys-hero-content">
                    <h1 class="edsys-hero-title">Сертификаты соответствия EDS</h1>
                    <p class="edsys-hero-description">
                        Вся наша продукция сертифицирована и соответствует российским и международным стандартам качества и безопасности.
                        Документы подтверждают соответствие техническим регламентам Таможенного союза.
                    </p>
                    <div class="edsys-hero-stats">
                        <div class="edsys-stat-item">
                            <span class="edsys-stat-number"><?= count($certificates) ?></span>
                            <span class="edsys-stat-label">документов</span>
                        </div>
                        <div class="edsys-stat-item">
                            <span class="edsys-stat-number">100%</span>
                            <span class="edsys-stat-label">соответствие</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Certificates Grid -->
        <section class="edsys-certificates-section">
            <div class="edsys-container">
                <div class="edsys-certificates-grid" id="certificatesGrid">
					<?php foreach ($certificates as $cert): ?>
                        <article class="edsys-certificate-card">
                            <div class="edsys-certificate-image">
                                <img
                                        src="<?= htmlspecialchars($cert['image']) ?>"
                                        alt="<?= htmlspecialchars($cert['title']) ?>"
                                        width="300"
                                        height="400"
                                        loading="lazy"
                                        class="edsys-cert-thumbnail"
                                />
                                <div class="edsys-certificate-overlay">
                                    <button
                                            class="edsys-view-button"
                                            data-cert-id="<?= $cert['id'] ?>"
                                            data-cert-title="<?= htmlspecialchars($cert['title']) ?>"
                                            data-cert-image="<?= htmlspecialchars($cert['image']) ?>"
                                            data-cert-description="<?= htmlspecialchars($cert['description']) ?>"
                                            data-cert-type="<?= htmlspecialchars($cert['type']) ?>"
                                            data-cert-valid="<?= htmlspecialchars($cert['valid_until']) ?>"
                                            aria-label="Просмотреть сертификат"
                                    >
                                        <i class="ph ph-thin ph-magnifying-glass-plus"></i>
                                        <span>Просмотреть</span>
                                    </button>
                                </div>
                            </div>

                            <div class="edsys-certificate-content">
                                <div class="edsys-certificate-meta">
                                    <span class="edsys-certificate-type"><?= htmlspecialchars($cert['type']) ?></span>
									<?php if (!empty($cert['valid_until'])): ?>
                                        <span class="edsys-certificate-valid">до <?= htmlspecialchars($cert['valid_until']) ?></span>
									<?php endif; ?>
                                </div>

                                <h3 class="edsys-certificate-title"><?= htmlspecialchars($cert['title']) ?></h3>
                                <p class="edsys-certificate-description"><?= htmlspecialchars($cert['description']) ?></p>

                                <div class="edsys-certificate-category">
                                    <i class="ph ph-thin ph-tag"></i>
                                    <span><?= htmlspecialchars($cert['category_name']) ?></span>
                                </div>
                            </div>
                        </article>
					<?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- Additional Info Section -->
        <section class="edsys-info-section">
            <div class="edsys-container">
                <div class="edsys-info-grid">
                    <div class="edsys-info-card">
                        <i class="ph ph-thin ph-shield-check"></i>
                        <h3>Соответствие стандартам</h3>
                        <p>Вся продукция соответствует техническим регламентам Таможенного союза и международным стандартам качества</p>
                    </div>

                    <div class="edsys-info-card">
                        <i class="ph ph-thin ph-certificate"></i>
                        <h3>Официальная сертификация</h3>
                        <p>Документы выданы аккредитованными органами по сертификации и имеют юридическую силу</p>
                    </div>

                    <div class="edsys-info-card">
                        <i class="ph ph-thin ph-clock-clockwise"></i>
                        <h3>Актуальность документов</h3>
                        <p>Все сертификаты действующие, регулярно обновляются и продлеваются в установленные сроки</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Modal Window -->
		<?php include 'modal.php'; ?>

        <!-- JavaScript -->
        <link rel="stylesheet" href="/sertifikaty-eds/css/certificates.css">
        <script src="/sertifikaty-eds/js/certificates.js"></script>
    </div>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>