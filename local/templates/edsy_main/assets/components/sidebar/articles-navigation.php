<?php
/**
 * EDS Articles Navigation Component v1.0
 * Универсальный компонент навигации по статьям
 *
 * @author EDS Development Team
 * @version 1.0
 * @since 2024
 *
 * Параметры:
 * $arParams['CURRENT_ARTICLE'] - слаг текущей статьи
 * $arParams['SHOW_MOBILE'] - показывать мобильную версию (по умолчанию true)
 * $arParams['SHOW_QUICK_NAV'] - показывать быструю навигацию (по умолчанию false)
 * $arParams['QUICK_NAV_DATA'] - данные для быстрой навигации
 * $arParams['CUSTOM_NAVIGATION'] - кастомная навигация (переопределяет стандартную)
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Load articles configuration
$articlesConfig = include($_SERVER["DOCUMENT_ROOT"] . "/local/templates/edsy_main/include/articles/articles-config.php");

// Default parameters
$arParams = array_merge([
	'CURRENT_ARTICLE' => '',
	'SHOW_MOBILE' => true,
	'SHOW_QUICK_NAV' => false,
	'QUICK_NAV_DATA' => [],
	'CUSTOM_NAVIGATION' => null
], $arParams ?? []);

// Get navigation data
$arNavigation = $arParams['CUSTOM_NAVIGATION'] ?? $articlesConfig['navigation'];

// Sort navigation by SORT field
usort($arNavigation, function($a, $b) {
	return ($a['SORT'] ?? 0) - ($b['SORT'] ?? 0);
});

// Detect current article from URL if not provided
if (empty($arParams['CURRENT_ARTICLE'])) {
	$currentPath = $_SERVER['REQUEST_URI'];
	$currentPath = trim($currentPath, '/');
	$currentPath = explode('/', $currentPath)[0];
	$arParams['CURRENT_ARTICLE'] = $currentPath;
}

// Mark active article
foreach ($arNavigation as &$arItem) {
	$arItem['ACTIVE'] = ($arItem['SLUG'] === $arParams['CURRENT_ARTICLE']);
}
unset($arItem);

// Get quick navigation data
$arQuickNav = $arParams['QUICK_NAV_DATA'];

try {
	?>

	<!-- Desktop Sidebar Navigation -->
	<aside class="edsys-articles-navigation" id="edsysArticlesNav">

		<?php if (!empty($arQuickNav) && $arParams['SHOW_QUICK_NAV']): ?>
			<!-- Quick Navigation (for schemes page) -->
			<div class="edsys-articles-navigation__quick-section">
				<h3 class="edsys-articles-navigation__quick-title">
					<i class="ph ph-thin ph-lightning"></i>
					Навигация по схемам
				</h3>
				<nav class="edsys-articles-navigation__quick-nav">
					<?php foreach ($arQuickNav as $quickItem): ?>
						<a href="#<?= htmlspecialchars($quickItem['anchor']) ?>"
						   class="edsys-articles-navigation__quick-item"
						   data-scheme="<?= htmlspecialchars($quickItem['id']) ?>"
						   title="<?= htmlspecialchars($quickItem['description'] ?? $quickItem['title']) ?>">
							<i class="ph ph-thin <?= htmlspecialchars($quickItem['icon']) ?>"></i>
							<?= htmlspecialchars($quickItem['title']) ?>
						</a>
					<?php endforeach; ?>
				</nav>
			</div>
		<?php endif; ?>

		<!-- Main Navigation -->
		<div class="edsys-articles-navigation__main-section">
			<div class="edsys-articles-navigation__header">
				<h3 class="edsys-articles-navigation__title">
					<i class="ph ph-thin ph-book-open"></i>
					Полезно знать
				</h3>
			</div>

			<nav class="edsys-articles-navigation__list" role="navigation" aria-label="Навигация по статьям">
				<?php foreach ($arNavigation as $arItem): ?>
					<a href="<?= htmlspecialchars($arItem['URL']) ?>"
					   class="edsys-articles-navigation__item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
						<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>
                       title="<?= htmlspecialchars($arItem['DESCRIPTION']) ?>"
                       data-article="<?= htmlspecialchars($arItem['SLUG']) ?>">
						<i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>" aria-hidden="true"></i>
						<div class="edsys-articles-navigation__content">
							<span class="edsys-articles-navigation__name"><?= htmlspecialchars($arItem['NAME']) ?></span>
							<span class="edsys-articles-navigation__desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></span>
						</div>
						<i class="ph ph-thin ph-arrow-right edsys-articles-navigation__arrow" aria-hidden="true"></i>
					</a>
				<?php endforeach; ?>
			</nav>
		</div>
	</aside>

	<?php if ($arParams['SHOW_MOBILE']): ?>
		<!-- Mobile Side Navigation Button -->
		<button class="edsys-mobile-nav-btn"
		        id="edsysMobileNavBtn"
		        aria-label="Открыть навигацию по разделам"
		        type="button">
			<i class="ph ph-thin ph-list" aria-hidden="true"></i>
		</button>

		<!-- Mobile Side Menu -->
		<div class="edsys-mobile-nav-menu" id="edsysMobileNavMenu" role="dialog" aria-modal="true" aria-labelledby="edsysMobileNavTitle">
			<div class="edsys-mobile-nav-header">
				<h3 class="edsys-mobile-nav-title" id="edsysMobileNavTitle">
					<i class="ph ph-thin ph-book-open" aria-hidden="true"></i>
					Навигация
				</h3>
				<button class="edsys-mobile-nav-close"
				        id="edsysMobileNavClose"
				        aria-label="Закрыть меню"
				        type="button">
					<i class="ph ph-thin ph-x" aria-hidden="true"></i>
				</button>
			</div>

			<div class="edsys-mobile-nav-content">

				<?php if (!empty($arQuickNav) && $arParams['SHOW_QUICK_NAV']): ?>
					<!-- Mobile Quick Navigation -->
					<div class="edsys-mobile-nav-quick">
						<h4 class="edsys-mobile-nav-section-title">
							<i class="ph ph-thin ph-lightning" aria-hidden="true"></i>
							Быстрая навигация
						</h4>
						<nav class="edsys-mobile-nav-quick-list" role="navigation" aria-label="Быстрая навигация по схемам">
							<?php foreach ($arQuickNav as $quickItem): ?>
								<a href="#<?= htmlspecialchars($quickItem['anchor']) ?>"
								   class="edsys-mobile-nav-quick-item"
								   data-scheme="<?= htmlspecialchars($quickItem['id']) ?>"
								   title="<?= htmlspecialchars($quickItem['description'] ?? $quickItem['title']) ?>">
									<i class="ph ph-thin <?= htmlspecialchars($quickItem['icon']) ?>" aria-hidden="true"></i>
									<?= htmlspecialchars($quickItem['title']) ?>
								</a>
							<?php endforeach; ?>
						</nav>
					</div>
				<?php endif; ?>

				<!-- Mobile Main Navigation -->
				<div class="edsys-mobile-nav-main">
					<h4 class="edsys-mobile-nav-section-title">
						<i class="ph ph-thin ph-book-open" aria-hidden="true"></i>
						Полезная информация
					</h4>
					<nav class="edsys-mobile-nav-main-list" role="navigation" aria-label="Основная навигация по статьям">
						<?php foreach ($arNavigation as $arItem): ?>
							<a href="<?= htmlspecialchars($arItem['URL']) ?>"
							   class="edsys-mobile-nav-item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
								<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>
                               title="<?= htmlspecialchars($arItem['DESCRIPTION']) ?>"
                               data-article="<?= htmlspecialchars($arItem['SLUG']) ?>">
								<i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>" aria-hidden="true"></i>
								<div class="edsys-mobile-nav-content">
									<div class="edsys-mobile-nav-name"><?= htmlspecialchars($arItem['NAME']) ?></div>
									<div class="edsys-mobile-nav-desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></div>
								</div>
								<i class="ph ph-thin ph-arrow-right edsys-mobile-nav-arrow" aria-hidden="true"></i>
							</a>
						<?php endforeach; ?>
					</nav>
				</div>
			</div>
		</div>

		<!-- Mobile Overlay -->
		<div class="edsys-mobile-nav-overlay" id="edsysMobileNavOverlay" aria-hidden="true"></div>
	<?php endif; ?>

	<?php
} catch (Exception $e) {
	// Log error for debugging
	if (function_exists('AddMessage2Log')) {
		AddMessage2Log("EDS Articles Navigation Error: " . $e->getMessage(), "articles_navigation");
	}

	// Show minimal fallback navigation
	?>
	<aside class="edsys-articles-navigation edsys-articles-navigation--fallback">
		<div class="edsys-articles-navigation__header">
			<h3 class="edsys-articles-navigation__title">
				<i class="ph ph-thin ph-book-open"></i>
				Полезно знать
			</h3>
		</div>
		<nav class="edsys-articles-navigation__list">
			<a href="/polezno-znat/" class="edsys-articles-navigation__item">
				<i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
				<div class="edsys-articles-navigation__content">
					<span class="edsys-articles-navigation__name">Вернуться к разделу</span>
					<span class="edsys-articles-navigation__desc">Все статьи раздела</span>
				</div>
			</a>
		</nav>
	</aside>
	<?php
}
?>

<script type="application/ld+json">
	{
		"@context": "https://schema.org",
		"@type": "SiteNavigationElement",
		"name": "Полезно знать - Навигация по статьям",
		"description": "Навигация по статьям раздела 'Полезно знать' на сайте EDS",
		"url": "<?= 'https://' . $_SERVER['HTTP_HOST'] . '/polezno-znat/' ?>",
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