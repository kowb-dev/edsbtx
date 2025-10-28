<?php
/**
 * Filter component for certificates page - Fixed Version
 * This file is included in certificates.php and uses $categories and $certificates variables
 */

// Ensure variables are available
if (!isset($categories) || !isset($certificates)) {
	error_log('Filter component: Required variables not found');
	return;
}
?>

<div class="edsys-filter-container">
    <div class="edsys-filter-header">
        <h2 class="edsys-filter-title">
            <i class="ph ph-thin ph-funnel"></i>
            Категории сертификатов
        </h2>
        <div class="edsys-filter-count">
            <span id="certificatesCount"><?= count($certificates) ?></span>
            <span class="edsys-count-label">документов</span>
        </div>
    </div>

    <!-- Desktop Filter Tabs - FIXED: 2 rows, 3 columns -->
    <div class="edsys-filter-tabs" role="tablist" aria-label="Фильтр по категориям сертификатов">
		<?php
		$isFirst = true;
		$tabIndex = 0;
		?>
		<?php foreach ($categories as $categoryKey => $categoryName): ?>
            <button
                    class="edsys-filter-tab<?= $isFirst ? ' edsys-active' : '' ?>"
                    data-category="<?= htmlspecialchars($categoryKey) ?>"
                    role="tab"
                    aria-selected="<?= $isFirst ? 'true' : 'false' ?>"
                    aria-controls="certificatesGrid"
                    tabindex="<?= $isFirst ? '0' : '-1' ?>"
                    id="tab-<?= htmlspecialchars($categoryKey) ?>"
            >
                <span class="edsys-tab-text"><?= htmlspecialchars($categoryName) ?></span>
                <span class="edsys-tab-count" data-category-count="<?= htmlspecialchars($categoryKey) ?>">
					<?php
					if ($categoryKey === 'all') {
						echo count($certificates);
					} else {
						$count = 0;
						foreach ($certificates as $cert) {
							if (isset($cert['category']) && $cert['category'] === $categoryKey) {
								$count++;
							}
						}
						echo $count;
					}
					?>
				</span>
            </button>
			<?php
			$isFirst = false;
			$tabIndex++;
			?>
		<?php endforeach; ?>
    </div>

    <!-- Mobile Filter Dropdown - FIXED -->
    <div class="edsys-mobile-filter" id="mobileFilter">
        <div class="edsys-mobile-filter-header" onclick="EdsysCertificates.toggleMobileFilter()" aria-expanded="false" aria-controls="mobileFilterDropdown">
            <div>
                <span class="edsys-mobile-filter-label">Категория:</span>
                <span class="edsys-mobile-filter-current" id="mobileFilterCurrent">Все сертификаты</span>
            </div>
            <i class="ph ph-thin ph-caret-down edsys-mobile-filter-icon"></i>
        </div>

        <div class="edsys-mobile-filter-dropdown" id="mobileFilterDropdown">
			<?php foreach ($categories as $categoryKey => $categoryName): ?>
                <button
                        class="edsys-mobile-filter-option<?= $categoryKey === 'all' ? ' edsys-active' : '' ?>"
                        data-category="<?= htmlspecialchars($categoryKey) ?>"
                        onclick="EdsysCertificates.selectMobileFilter('<?= htmlspecialchars($categoryKey) ?>', '<?= htmlspecialchars($categoryName) ?>')"
                        type="button"
                >
                    <span><?= htmlspecialchars($categoryName) ?></span>
                    <span class="edsys-mobile-option-count">
						<?php
						if ($categoryKey === 'all') {
							echo count($certificates);
						} else {
							$count = 0;
							foreach ($certificates as $cert) {
								if (isset($cert['category']) && $cert['category'] === $categoryKey) {
									$count++;
								}
							}
							echo $count;
						}
						?>
					</span>
                </button>
			<?php endforeach; ?>
        </div>
    </div>

    <!-- Search Input - Enhanced -->
    <div class="edsys-search-container">
        <div class="edsys-search-input-wrapper">
            <i class="ph ph-thin ph-magnifying-glass edsys-search-icon"></i>
            <input
                    type="text"
                    class="edsys-search-input"
                    placeholder="Поиск по названию сертификата..."
                    id="certificatesSearch"
                    aria-label="Поиск сертификатов"
                    autocomplete="off"
                    autocorrect="off"
                    autocapitalize="off"
                    spellcheck="false"
            />
            <button
                    class="edsys-search-clear"
                    id="searchClear"
                    style="display: none;"
                    onclick="EdsysCertificates.clearSearch()"
                    aria-label="Очистить поиск"
                    type="button"
            >
                <i class="ph ph-thin ph-x"></i>
            </button>
        </div>
    </div>
</div>

<!-- Filter Status Bar - FIXED -->
<div class="edsys-filter-status" id="filterStatus" style="display: none;" aria-live="polite">
    <div class="edsys-filter-status-content">
        <span class="edsys-status-text">Активные фильтры:</span>
        <div class="edsys-active-filters" id="activeFilters">
            <!-- Filter tags will be inserted here by JavaScript -->
        </div>
        <button
                class="edsys-clear-all-filters"
                onclick="EdsysCertificates.clearAllFilters()"
                aria-label="Очистить все фильтры"
                type="button"
        >
            <i class="ph ph-thin ph-x"></i>
            Сбросить все
        </button>
    </div>
</div>

<script type="application/ld+json">
    {
		"@context": "https://schema.org",
		"@type": "WebPage",
		"name": "Сертификаты EDS",
		"description": "Сертификаты соответствия продукции EDS",
		"url": "<?= 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] ?>",
	"mainEntity": {
		"@type": "ItemList",
		"numberOfItems": <?= count($certificates) ?>,
		"itemListElement": [
	<?php
	$jsonItems = [];
	foreach ($certificates as $index => $cert) {
		$jsonItems[] = json_encode([
			"@type" => "CreativeWork",
			"name" => $cert["title"],
			"description" => $cert["description"],
			"category" => $cert["category_name"],
			"position" => $index + 1
		], JSON_UNESCAPED_UNICODE);
	}
	echo implode(',', $jsonItems);
	?>
    ]
}
}
</script>