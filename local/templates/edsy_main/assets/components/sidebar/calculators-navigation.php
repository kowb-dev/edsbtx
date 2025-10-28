<?php
/**
 * EDS Calculators Navigation Component v1.0
 * Универсальный компонент навигации по калькуляторам
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

try {
	// Load calculators configuration with error handling
	$calculatorsConfigPath = $_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/calculators/calculators-config.php";

	if (file_exists($calculatorsConfigPath)) {
		$calculatorsConfig = include($calculatorsConfigPath);
	} else {
		$calculatorsConfig = [];
	}

	// Default parameters
	$arParams = array_merge([
		'CURRENT_CALCULATOR' => '',
		'SHOW_MOBILE' => true,
		'SHOW_ALL_LINK' => true,
		'CUSTOM_NAVIGATION' => null
	], $arParams ?? []);

	// Get navigation data with fallback
	$arNavigation = [];

	if (!empty($arParams['CUSTOM_NAVIGATION']) && is_array($arParams['CUSTOM_NAVIGATION'])) {
		$arNavigation = $arParams['CUSTOM_NAVIGATION'];
	} elseif (isset($calculatorsConfig['navigation']) && is_array($calculatorsConfig['navigation'])) {
		$arNavigation = $calculatorsConfig['navigation'];
	} else {
		// Ultimate fallback - create minimal navigation
		$arNavigation = [
			[
				'NAME' => 'Калькуляторы',
				'URL' => '/kalkulyatory/',
				'SLUG' => 'all-calculators',
				'DESCRIPTION' => 'Вернуться к разделу',
				'ICON' => 'ph-calculator',
				'SORT' => 1
			]
		];
	}

	// Ensure $arNavigation is an array before sorting
	if (is_array($arNavigation) && count($arNavigation) > 0) {
		// Sort navigation by SORT field
		usort($arNavigation, function($a, $b) {
			return ($a['SORT'] ?? 0) - ($b['SORT'] ?? 0);
		});
	}

	// Detect current calculator from URL if not provided
	if (empty($arParams['CURRENT_CALCULATOR'])) {
		$currentPath = $_SERVER['REQUEST_URI'];
		$currentPath = trim($currentPath, '/');
		$pathParts = explode('/', $currentPath);

		// Try to match URL with calculator slugs
		foreach ($arNavigation as $item) {
			if (strpos($currentPath, trim($item['URL'], '/')) !== false) {
				$arParams['CURRENT_CALCULATOR'] = $item['SLUG'];
				break;
			}
		}
	}

	// Mark active calculator
	foreach ($arNavigation as &$arItem) {
		$arItem['ACTIVE'] = ($arItem['SLUG'] === $arParams['CURRENT_CALCULATOR']);
	}
	unset($arItem);

	?>

	<!-- Desktop Sidebar Navigation -->
	<aside class="edsys-calculators-navigation" id="edsysCalculatorsNav">
		<div class="edsys-calculators-navigation__main-section">
			<div class="edsys-calculators-navigation__header">
				<h3 class="edsys-calculators-navigation__title">
					<i class="ph ph-thin ph-calculator"></i>
					Калькуляторы
				</h3>
			</div>

			<nav class="edsys-calculators-navigation__list" role="navigation" aria-label="Навигация по калькуляторам">
				<?php if (!empty($arNavigation)): ?>
					<?php foreach ($arNavigation as $arItem): ?>
						<a href="<?= htmlspecialchars($arItem['URL']) ?>"
						   class="edsys-calculators-navigation__item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
							<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>
                           title="<?= htmlspecialchars($arItem['DESCRIPTION']) ?>"
                           data-calculator="<?= htmlspecialchars($arItem['SLUG']) ?>">
							<i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>" aria-hidden="true"></i>
							<div class="edsys-calculators-navigation__content">
								<span class="edsys-calculators-navigation__name"><?= htmlspecialchars($arItem['NAME']) ?></span>
								<span class="edsys-calculators-navigation__desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></span>
							</div>
							<i class="ph ph-thin ph-arrow-right edsys-calculators-navigation__arrow" aria-hidden="true"></i>
						</a>
					<?php endforeach; ?>
				<?php else: ?>
					<!-- Fallback navigation -->
					<a href="/kalkulyatory/" class="edsys-calculators-navigation__item">
						<i class="ph ph-thin ph-calculator" aria-hidden="true"></i>
						<div class="edsys-calculators-navigation__content">
							<span class="edsys-calculators-navigation__name">Калькуляторы</span>
							<span class="edsys-calculators-navigation__desc">Вернуться к разделу</span>
						</div>
						<i class="ph ph-thin ph-arrow-right edsys-calculators-navigation__arrow" aria-hidden="true"></i>
					</a>
				<?php endif; ?>
			</nav>
		</div>
	</aside>

	<?php if ($arParams['SHOW_MOBILE']): ?>
		<!-- Mobile Side Navigation Button -->
		<button class="edsys-calculators-mobile-nav-btn"
		        id="edsysCalculatorsMobileNavBtn"
		        aria-label="Открыть навигацию по калькуляторам"
		        type="button">
			<i class="ph ph-thin ph-calculator" aria-hidden="true"></i>
		</button>

		<!-- Mobile Side Menu -->
		<div class="edsys-calculators-mobile-nav-menu" id="edsysCalculatorsMobileNavMenu" role="dialog" aria-modal="true" aria-labelledby="edsysCalculatorsMobileNavTitle">
			<div class="edsys-calculators-mobile-nav-header">
				<h3 class="edsys-calculators-mobile-nav-title" id="edsysCalculatorsMobileNavTitle">
					<i class="ph ph-thin ph-calculator" aria-hidden="true"></i>
					Калькуляторы
				</h3>
				<button class="edsys-calculators-mobile-nav-close"
				        id="edsysCalculatorsMobileNavClose"
				        aria-label="Закрыть меню"
				        type="button">
					<i class="ph ph-thin ph-x" aria-hidden="true"></i>
				</button>
			</div>

			<div class="edsys-calculators-mobile-nav-content">
				<!-- Mobile Main Navigation -->
				<div class="edsys-calculators-mobile-nav-main">
					<nav class="edsys-calculators-mobile-nav-main-list" role="navigation" aria-label="Основная навигация по калькуляторам">
						<?php if (!empty($arNavigation)): ?>
							<?php foreach ($arNavigation as $arItem): ?>
								<a href="<?= htmlspecialchars($arItem['URL']) ?>"
								   class="edsys-calculators-mobile-nav-item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
									<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>
                                   title="<?= htmlspecialchars($arItem['DESCRIPTION']) ?>"
                                   data-calculator="<?= htmlspecialchars($arItem['SLUG']) ?>">
									<i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>" aria-hidden="true"></i>
									<div class="edsys-calculators-mobile-nav-content">
										<div class="edsys-calculators-mobile-nav-name"><?= htmlspecialchars($arItem['NAME']) ?></div>
										<div class="edsys-calculators-mobile-nav-desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></div>
									</div>
									<i class="ph ph-thin ph-arrow-right edsys-calculators-mobile-nav-arrow" aria-hidden="true"></i>
								</a>
							<?php endforeach; ?>
						<?php else: ?>
							<!-- Fallback mobile navigation -->
							<a href="/kalkulyatory/" class="edsys-calculators-mobile-nav-item">
								<i class="ph ph-thin ph-calculator" aria-hidden="true"></i>
								<div class="edsys-calculators-mobile-nav-content">
									<div class="edsys-calculators-mobile-nav-name">Калькуляторы</div>
									<div class="edsys-calculators-mobile-nav-desc">Вернуться к разделу</div>
								</div>
								<i class="ph ph-thin ph-arrow-right edsys-calculators-mobile-nav-arrow" aria-hidden="true"></i>
							</a>
						<?php endif; ?>
					</nav>
				</div>
			</div>
		</div>

		<!-- Mobile Overlay -->
		<div class="edsys-calculators-mobile-nav-overlay" id="edsysCalculatorsMobileNavOverlay" aria-hidden="true"></div>
	<?php endif; ?>

	<!-- Structured Data -->
	<script type="application/ld+json">
		{
			"@context": "https://schema.org",
			"@type": "SiteNavigationElement",
			"name": "Калькуляторы - Навигация по калькуляторам",
			"description": "Навигация по калькуляторам раздела 'Калькуляторы' на сайте EDS",
			"url": "<?= 'https://' . $_SERVER['HTTP_HOST'] . '/kalkulyatory/' ?>",
            "hasPart": [
		<?php
		$navigationLD = [];
		foreach ($arNavigation as $index => $arItem) {
			$navigationLD[] = json_encode([
				"@type" => "SiteNavigationElement",
				"name" => $arItem['NAME'],
				"description" => $arItem['DESCRIPTION'],
				"url" => 'https://' . $_SERVER['HTTP_HOST'] . $arItem['URL'],
				"position" => $index + 1
			], JSON_UNESCAPED_UNICODE);
		}
		echo implode(',', $navigationLD);
		?>
		]
	}
	</script>

	<?php
} catch (Exception $e) {
	// Log error for debugging
	if (function_exists('AddMessage2Log')) {
		AddMessage2Log("EDS Calculators Navigation Error: " . $e->getMessage(), "calculators_navigation");
	}

	// Show minimal fallback navigation
	?>
	<aside class="edsys-calculators-navigation edsys-calculators-navigation--fallback">
		<div class="edsys-calculators-navigation__header">
			<h3 class="edsys-calculators-navigation__title">
				<i class="ph ph-thin ph-calculator"></i>
				Калькуляторы
			</h3>
		</div>
		<nav class="edsys-calculators-navigation__list">
			<a href="/kalkulyatory/" class="edsys-calculators-navigation__item">
				<i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
				<div class="edsys-calculators-navigation__content">
					<span class="edsys-calculators-navigation__name">Вернуться к разделу</span>
					<span class="edsys-calculators-navigation__desc">Все калькуляторы</span>
				</div>
			</a>
		</nav>
	</aside>
	<?php
}
?>