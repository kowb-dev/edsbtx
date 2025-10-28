<?php
/**
 * Useful Information Navigation Component Template
 * Шаблон компонента навигации по полезной информации
 *
 * @var array $arResult - Navigation data
 * @var array $arParams - Component parameters
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

// Default navigation items
$defaultNavigation = [
	[
		'NAME' => 'Статьи',
		'URL' => '/polezno-znat/',
		'ACTIVE' => false,
		'ICON' => 'ph-article',
		'DESCRIPTION' => 'Профессиональные статьи и руководства'
	],
	[
		'NAME' => 'Таблицы токовых нагрузок медных кабелей',
		'URL' => '/stati-tablitsy-nagruzok/',
		'ACTIVE' => false,
		'ICON' => 'ph-table',
		'DESCRIPTION' => 'Справочные таблицы нагрузок'
	],
	[
		'NAME' => 'Схемы распайки кабелей',
		'URL' => '/shemy-raspajki-kabelej/',
		'ACTIVE' => false,
		'ICON' => 'ph-circuitry',
		'DESCRIPTION' => 'Схемы подключения и распайки'
	],
	[
		'NAME' => 'Классификация типов нагрузки контактов',
		'URL' => '/klassifikatsiya-tipov-nagruzki-kontaktov/',
		'ACTIVE' => false,
		'ICON' => 'ph-tree-structure',
		'DESCRIPTION' => 'Типы и характеристики нагрузок'
	]
];

// Get navigation items from arResult or use default
$arNavigation = isset($arResult['NAVIGATION']) ? $arResult['NAVIGATION'] : $defaultNavigation;

// Get current page URL for active state detection
$currentUrl = $GLOBALS['APPLICATION']->GetCurUri();

// Auto-detect active page
foreach ($arNavigation as &$item) {
	if (strpos($currentUrl, $item['URL']) !== false) {
		$item['ACTIVE'] = true;
	}
}
unset($item);

// Get quick navigation items (for schemes page)
$arQuickNav = isset($arResult['QUICK_NAV']) ? $arResult['QUICK_NAV'] : [];
?>

<!-- Desktop Sidebar Navigation -->
<aside class="edsys-useful-nav" id="usefulInfoNav">

	<?php if (!empty($arQuickNav)): ?>
        <!-- Quick Navigation (for schemes page) -->
        <div class="edsys-useful-nav__quick-section">
            <h3 class="edsys-useful-nav__quick-title">
                <i class="ph ph-thin ph-lightning"></i>
                Навигация по схемам
            </h3>
            <nav class="edsys-useful-nav__quick-nav">
				<?php foreach ($arQuickNav as $quickItem): ?>
                    <a href="#<?= htmlspecialchars($quickItem['anchor']) ?>"
                       class="edsys-useful-nav__quick-item"
                       data-scheme="<?= htmlspecialchars($quickItem['id']) ?>">
                        <i class="ph ph-thin <?= htmlspecialchars($quickItem['icon']) ?>"></i>
						<?= htmlspecialchars($quickItem['title']) ?>
                    </a>
				<?php endforeach; ?>
            </nav>
        </div>
	<?php endif; ?>

    <!-- Main Navigation -->
    <div class="edsys-useful-nav__main-section">
        <h3 class="edsys-useful-nav__title">
            <i class="ph ph-thin ph-book-open"></i>
            Полезная информация
        </h3>
        <nav class="edsys-useful-nav__list">
			<?php foreach ($arNavigation as $arItem): ?>
                <a href="<?= htmlspecialchars($arItem['URL']) ?>"
                   class="edsys-useful-nav__item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
					<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>>
                    <i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>"></i>
                    <div class="edsys-useful-nav__item-content">
                        <span class="edsys-useful-nav__item-title"><?= htmlspecialchars($arItem['NAME']) ?></span>
                        <span class="edsys-useful-nav__item-desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></span>
                    </div>
                    <i class="ph ph-thin ph-arrow-right edsys-useful-nav__arrow"></i>
                </a>
			<?php endforeach; ?>
        </nav>
    </div>
</aside>

<!-- Mobile Side Navigation Button -->
<button class="edsys-useful-nav__mobile-btn" id="usefulNavMobileBtn" aria-label="Открыть навигацию по разделам">
    <i class="ph ph-thin ph-compass"></i>
</button>

<!-- Mobile Side Menu -->
<div class="edsys-useful-nav__mobile-menu" id="usefulNavMobileMenu">
    <div class="edsys-useful-nav__mobile-header">
        <h3 class="edsys-useful-nav__mobile-title">
            <i class="ph ph-thin ph-book-open"></i>
            Навигация
        </h3>
        <button class="edsys-useful-nav__mobile-close" id="usefulNavMobileClose" aria-label="Закрыть меню">
            <i class="ph ph-thin ph-x"></i>
        </button>
    </div>

    <div class="edsys-useful-nav__mobile-content">

		<?php if (!empty($arQuickNav)): ?>
            <!-- Mobile Quick Navigation -->
            <div class="edsys-useful-nav__mobile-quick">
                <h4 class="edsys-useful-nav__mobile-section-title">
                    <i class="ph ph-thin ph-lightning"></i>
                    Быстрая навигация
                </h4>
                <nav class="edsys-useful-nav__mobile-quick-list">
					<?php foreach ($arQuickNav as $quickItem): ?>
                        <a href="#<?= htmlspecialchars($quickItem['anchor']) ?>"
                           class="edsys-useful-nav__mobile-quick-item"
                           data-scheme="<?= htmlspecialchars($quickItem['id']) ?>">
                            <i class="ph ph-thin <?= htmlspecialchars($quickItem['icon']) ?>"></i>
							<?= htmlspecialchars($quickItem['title']) ?>
                        </a>
					<?php endforeach; ?>
                </nav>
            </div>
		<?php endif; ?>

        <!-- Mobile Main Navigation -->
        <div class="edsys-useful-nav__mobile-main">
            <h4 class="edsys-useful-nav__mobile-section-title">
                <i class="ph ph-thin ph-book-open"></i>
                Полезная информация
            </h4>
            <nav class="edsys-useful-nav__mobile-main-list">
				<?php foreach ($arNavigation as $arItem): ?>
                    <a href="<?= htmlspecialchars($arItem['URL']) ?>"
                       class="edsys-useful-nav__mobile-main-item <?= $arItem['ACTIVE'] ? 'active' : '' ?>"
						<?= $arItem['ACTIVE'] ? 'aria-current="page"' : '' ?>>
                        <i class="ph ph-thin <?= htmlspecialchars($arItem['ICON']) ?>"></i>
                        <div class="edsys-useful-nav__mobile-item-content">
                            <div class="edsys-useful-nav__mobile-item-title"><?= htmlspecialchars($arItem['NAME']) ?></div>
                            <div class="edsys-useful-nav__mobile-item-desc"><?= htmlspecialchars($arItem['DESCRIPTION']) ?></div>
                        </div>
                        <i class="ph ph-thin ph-arrow-right edsys-useful-nav__mobile-arrow"></i>
                    </a>
				<?php endforeach; ?>
            </nav>
        </div>
    </div>
</div>

<!-- Mobile Overlay -->
<div class="edsys-useful-nav__mobile-overlay" id="usefulNavMobileOverlay"></div>