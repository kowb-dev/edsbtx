<?php
/**
 * EDS Product Categories Component v1.2 - DEBUG VERSION
 * ИСПРАВЛЕНА ПРОБЛЕМА С ОТОБРАЖЕНИЕМ БЛОКА
 *
 * @author EDS Development Team
 * @version 1.2
 * @since 2024
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Debug: Start logging
$debug = true; // Set to false in production
if ($debug) {
	error_log("EDS Product Categories: Component started");
}

// Load articles configuration
$articlesConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php";
$articlesConfig = [];

if (file_exists($articlesConfigPath)) {
	$articlesConfig = include($articlesConfigPath);
	if ($debug) {
		error_log("EDS Product Categories: Config loaded successfully");
	}
} else {
	if ($debug) {
		error_log("EDS Product Categories: Config file not found at " . $articlesConfigPath);
	}
}

// Default parameters with validation
$arParams = array_merge([
	'CATEGORIES_TYPE' => 'power_distribution',
	'CATEGORIES_DATA' => null,
	'TITLE' => 'Решения EDS',
	'SUBTITLE' => 'Профессиональное оборудование',
	'SHOW_FEATURES' => true,
	'COLUMNS' => 'auto',
	'TARGET_BLANK' => true,
	'BLOCK_ID' => 'edsysProductCategories' . '_' . uniqid() // Generate unique ID
], $arParams ?? []);

if ($debug) {
	error_log("EDS Product Categories: Parameters set - Type: " . $arParams['CATEGORIES_TYPE']);
}

// Get categories data with fallback
$arCategories = [];

if (!empty($arParams['CATEGORIES_DATA'])) {
	// Use custom categories data
	$arCategories = $arParams['CATEGORIES_DATA'];
	if ($debug) {
		error_log("EDS Product Categories: Using custom categories data");
	}
} elseif (!empty($arParams['CATEGORIES_TYPE']) &&
          isset($articlesConfig['product_categories_templates'][$arParams['CATEGORIES_TYPE']])) {
	// Use predefined template
	$arCategories = $articlesConfig['product_categories_templates'][$arParams['CATEGORIES_TYPE']];
	if ($debug) {
		error_log("EDS Product Categories: Using template: " . $arParams['CATEGORIES_TYPE'] . " with " . count($arCategories) . " categories");
	}
} else {
	// Enhanced fallback
	if (isset($articlesConfig['product_categories_templates']['power_distribution'])) {
		$arCategories = $articlesConfig['product_categories_templates']['power_distribution'];
		if ($debug) {
			error_log("EDS Product Categories: Using fallback power_distribution template");
		}
	} else {
		// Ultimate fallback
		$arCategories = [
			[
				'name' => 'Каталог продукции EDS',
				'description' => 'Профессиональные системы распределения электропитания',
				'url' => 'https://btx.edsy.ru/',
				'icon' => 'ph-cube',
				'color' => 'voltage',
				'features' => ['Профессиональное оборудование', 'Надежность', 'Качество']
			]
		];
		if ($debug) {
			error_log("EDS Product Categories: Using ultimate fallback");
		}
	}
}

// Validate categories data
if (empty($arCategories)) {
	if ($debug) {
		error_log("EDS Product Categories: No categories found, component will not display");
	}
	return;
}

if ($debug) {
	error_log("EDS Product Categories: Final categories count: " . count($arCategories));
}

// Generate column class
$columnClass = '';
switch ($arParams['COLUMNS']) {
	case '1':
		$columnClass = 'edsys-categories-grid--single';
		break;
	case '2':
		$columnClass = 'edsys-categories-grid--double';
		break;
	case '3':
		$columnClass = 'edsys-categories-grid--triple';
		break;
	case '4':
		$columnClass = 'edsys-categories-grid--quad';
		break;
	default:
		$columnClass = 'edsys-categories-grid--auto';
		break;
}

// Link target
$linkTarget = $arParams['TARGET_BLANK'] ? ' target="_blank" rel="noopener"' : '';

try {
	if ($debug) {
		error_log("EDS Product Categories: Starting render");
	}
	?>

    <!-- Product Categories Block -->
    <section class="edsys-product-categories" id="<?= htmlspecialchars($arParams['BLOCK_ID']) ?>">
        <div class="edsys-categories-header">
            <h2 class="edsys-categories-title"><?= htmlspecialchars($arParams['TITLE']) ?></h2>
			<?php if (!empty($arParams['SUBTITLE'])): ?>
                <p class="edsys-categories-subtitle"><?= htmlspecialchars($arParams['SUBTITLE']) ?></p>
			<?php endif; ?>
        </div>

        <div class="edsys-categories-grid <?= $columnClass ?>">
			<?php foreach ($arCategories as $index => $category): ?>
				<?php
				// Validate required fields
				if (empty($category['name']) || empty($category['url'])) {
					if ($debug) {
						error_log("EDS Product Categories: Skipping category " . $index . " - missing name or url");
					}
					continue;
				}

				// Set defaults
				$category = array_merge([
					'description' => '',
					'icon' => 'ph-cube',
					'color' => 'voltage',
					'features' => []
				], $category);

				// Generate unique category ID
				$categoryId = 'category_' . $index . '_' . uniqid();

				if ($debug && $index === 0) {
					error_log("EDS Product Categories: Rendering category: " . $category['name']);
				}
				?>

                <a href="<?= htmlspecialchars($category['url']) ?>"
                   class="edsys-category-card"
                   id="<?= $categoryId ?>"
                   data-color="<?= htmlspecialchars($category['color']) ?>"
                   data-category="<?= htmlspecialchars(strtolower(str_replace(' ', '_', $category['name']))) ?>"
                   title="<?= htmlspecialchars($category['description'] ?: $category['name']) ?>"
					<?= $linkTarget ?>>

                    <div class="edsys-category-icon">
                        <i class="ph ph-thin <?= htmlspecialchars($category['icon']) ?>" aria-hidden="true"></i>
                    </div>

                    <div class="edsys-category-content">
                        <h3 class="edsys-category-name"><?= htmlspecialchars($category['name']) ?></h3>

						<?php if (!empty($category['description'])): ?>
                            <p class="edsys-category-description"><?= htmlspecialchars($category['description']) ?></p>
						<?php endif; ?>

						<?php if ($arParams['SHOW_FEATURES'] && !empty($category['features'])): ?>
                            <ul class="edsys-category-features" role="list">
								<?php foreach ($category['features'] as $feature): ?>
                                    <li><?= htmlspecialchars($feature) ?></li>
								<?php endforeach; ?>
                            </ul>
						<?php endif; ?>
                    </div>

                    <div class="edsys-category-arrow">
                        <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                    </div>
                </a>
			<?php endforeach; ?>
        </div>
    </section>

	<?php
	if ($debug) {
		error_log("EDS Product Categories: Render completed successfully");
	}

} catch (Exception $e) {
	// Log error for debugging
	if (function_exists('AddMessage2Log')) {
		AddMessage2Log("EDS Product Categories Error: " . $e->getMessage(), "product_categories");
	}

	if ($debug) {
		error_log("EDS Product Categories: Exception - " . $e->getMessage());
	}

	// Show enhanced fallback
	?>
    <section class="edsys-product-categories edsys-product-categories--fallback" id="<?= htmlspecialchars($arParams['BLOCK_ID']) ?>_fallback">
        <div class="edsys-categories-header">
            <h2 class="edsys-categories-title">Наша продукция</h2>
            <p class="edsys-categories-subtitle">Профессиональные решения для электроснабжения</p>
        </div>
        <div class="edsys-categories-grid edsys-categories-grid--single">
            <a href="https://btx.edsy.ru/"
               class="edsys-category-card"
               data-color="voltage"
               target="_blank"
               rel="noopener">
                <div class="edsys-category-icon">
                    <i class="ph ph-thin ph-cube" aria-hidden="true"></i>
                </div>
                <div class="edsys-category-content">
                    <h3 class="edsys-category-name">Каталог продукции EDS</h3>
                    <p class="edsys-category-description">Перейти к полному каталогу продукции</p>
                </div>
                <div class="edsys-category-arrow">
                    <i class="ph ph-thin ph-arrow-right" aria-hidden="true"></i>
                </div>
            </a>
        </div>
    </section>

    <!-- Debug Information -->
	<?php if ($debug): ?>
        <div style="background: #f0f0f0; padding: 10px; margin: 10px 0; font-family: monospace; font-size: 12px;">
            <strong>Debug Info:</strong><br>
            Categories Type: <?= htmlspecialchars($arParams['CATEGORIES_TYPE']) ?><br>
            Categories Found: <?= count($arCategories) ?><br>
            Config File Exists: <?= file_exists($articlesConfigPath) ? 'Yes' : 'No' ?><br>
            Error: <?= htmlspecialchars($e->getMessage()) ?>
        </div>
	<?php endif; ?>

	<?php
}

// Structured Data
if (!empty($arCategories)) {
	?>
    <script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "ItemList",
            "name": "<?= htmlspecialchars($arParams['TITLE']) ?>",
            "description": "<?= htmlspecialchars($arParams['SUBTITLE']) ?>",
            "numberOfItems": <?= count($arCategories) ?>,
            "itemListElement": [
		<?php
		$categoriesLD = [];
		foreach ($arCategories as $index => $category) {
			if (empty($category['name']) || empty($category['url'])) continue;

			$categoriesLD[] = json_encode([
				"@type" => "ListItem",
				"position" => $index + 1,
				"item" => [
					"@type" => "Product",
					"name" => $category['name'],
					"description" => $category['description'] ?? '',
					"url" => $category['url'],
					"manufacturer" => [
						"@type" => "Organization",
						"name" => "EDS - Electric Distribution Systems"
					]
				]
			], JSON_UNESCAPED_UNICODE);
		}
		echo implode(',', $categoriesLD);
		?>
        ]
	}
    </script>
	<?php
}

if ($debug) {
	error_log("EDS Product Categories: Component completed");
}
?>