<?php
/**
 * Cable Tables Component - Updated with Useful Info Navigation Integration
 * Компонент для обработки и отображения таблиц кабелей с интеграцией навигации
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class CableTables
{
	private $cableTypes;
	private $cableData;
	private $tableHeaders;
	private $relatedSections;

	public function __construct()
	{
		// Include data file
		require_once($_SERVER['DOCUMENT_ROOT'] . '/stati-tablitsy-nagruzok/cable-data.php');

		$this->cableTypes = $arCableTypes;
		$this->cableData = $arCableData;
		$this->tableHeaders = $arTableHeaders;
		$this->relatedSections = $arRelatedSections;
	}

	/**
	 * Get cable types for navigation
	 */
	public function getCableTypes()
	{
		return $this->cableTypes;
	}

	/**
	 * Get specific cable data
	 */
	public function getCableData($type = null)
	{
		if ($type && isset($this->cableData[$type])) {
			return $this->cableData[$type];
		}
		return $this->cableData;
	}

	/**
	 * Get table headers
	 */
	public function getTableHeaders()
	{
		return $this->tableHeaders;
	}

	/**
	 * Get related sections
	 */
	public function getRelatedSections()
	{
		return $this->relatedSections;
	}

	/**
	 * Generate navigation cards HTML
	 */
	public function renderNavigationCards()
	{
		$html = '<div class="edsys-cable-nav">';

		foreach ($this->cableTypes as $key => $cable) {
			$html .= sprintf(
				'<div class="edsys-cable-card" data-cable="%s" data-color="%s">
                    <div class="edsys-cable-card__icon">
                        <i class="ph %s"></i>
                    </div>
                    <div class="edsys-cable-card__content">
                        <h3 class="edsys-cable-card__title">%s</h3>
                        <p class="edsys-cable-card__desc">%s</p>
                    </div>
                </div>',
				htmlspecialchars($key),
				htmlspecialchars($cable['color']),
				htmlspecialchars($cable['icon']),
				htmlspecialchars($cable['name']),
				htmlspecialchars($cable['description'])
			);
		}

		$html .= '</div>';
		return $html;
	}

	/**
	 * Generate table HTML for specific cable type
	 */
	public function renderTable($cableType)
	{
		if (!isset($this->cableTypes[$cableType]) || !isset($this->cableData[$cableType])) {
			return '';
		}

		$cable = $this->cableTypes[$cableType];
		$data = $this->cableData[$cableType];

		$html = sprintf(
			'<section class="edsys-cable-section" id="%s" data-cable="%s">
            <div class="edsys-cable-section__header">
                <h2 class="edsys-cable-section__title">%s</h2>
            </div>
            <div class="edsys-cable-table-wrapper">
                <table class="edsys-cable-table" data-color="%s">
                    <thead>
                        <tr>
                            <th><span class="edsys-table-header-desktop">Сечение, мм²</span><span class="edsys-table-header-mobile">Сечение,<br>мм²</span></th>
                            <th><span class="edsys-table-header-desktop">Диаметр, мм</span><span class="edsys-table-header-mobile">Диаметр,<br>мм</span></th>
                            <th><span class="edsys-table-header-desktop">Масса, кг/км</span><span class="edsys-table-header-mobile">Масса,<br>кг/км</span></th>
                            <th><span class="edsys-table-header-desktop">Токовая нагрузка, А</span><span class="edsys-table-header-mobile">Токовая<br>нагрузка, А</span></th>
                        </tr>',
			htmlspecialchars($cableType),
			htmlspecialchars($cableType),
			htmlspecialchars($cable['full_name']),
			htmlspecialchars($cable['color'])
		);

		$html .= '</thead><tbody>';

		// Table rows
		foreach ($data as $index => $row) {
			$html .= '<tr>';
			foreach (['section', 'diameter', 'weight', 'current'] as $column) {
				$value = isset($row[$column]) ? $row[$column] : '';
				$html .= sprintf('<td>%s</td>', htmlspecialchars($value));
			}
			$html .= '</tr>';
		}

		$html .= '</tbody></table></div></section>';

		return $html;
	}

	/**
	 * Render integrated navigation with useful info navigation
	 * Новый метод для интеграции с useful-info navigation
	 */
	public function renderIntegratedNavigation($usefulInfoNav, $localNav)
	{
		// Prepare data for useful info navigation component
		$arResult = [
			'NAVIGATION' => $usefulInfoNav,
			'QUICK_NAV' => $localNav
		];

		$arParams = [];

		// Include component template
		ob_start();
		include($_SERVER["DOCUMENT_ROOT"] . '/local/templates/' . SITE_TEMPLATE_ID . '/components/bitrix/menu/useful_info_navigation/template.php');
		return ob_get_clean();
	}

	/**
	 * Generate sidebar navigation (legacy method - now uses integrated navigation)
	 * @deprecated Use renderIntegratedNavigation() instead
	 */
	public function renderSidebarNavigation()
	{
		// Include navigation config
		require_once($_SERVER['DOCUMENT_ROOT'] . '/stati-tablitsy-nagruzok/navigation-config.php');

		// Prepare local navigation for current page
		$localNav = [];
		foreach ($this->cableTypes as $key => $cable) {
			$localNav[] = [
				'id' => $key,
				'title' => $cable['name'],
				'anchor' => $key,
				'icon' => $cable['icon']
			];
		}

		// Use integrated navigation
		return $this->renderIntegratedNavigation($arUsefulInfoNavigation, $localNav);
	}

	/**
	 * Generate mobile floating button (removed - now handled by useful info navigation)
	 * @deprecated Mobile navigation now handled by useful-info-navigation component
	 */
	public function renderMobileFloatingButton()
	{
		// Mobile navigation is now handled by useful-info-navigation component
		return '';
	}

	/**
	 * Generate floating navigation menu (removed - now handled by useful info navigation)
	 * @deprecated Use useful-info-navigation component instead
	 */
	public function renderFloatingNav()
	{
		// Mobile navigation is now handled by useful-info-navigation component
		return '';
	}

	/**
	 * Generate breadcrumbs
	 */
	public function renderBreadcrumbs()
	{
		return '<nav class="edsys-breadcrumbs">
                    <a href="/" class="edsys-breadcrumbs__link">Главная</a>
                    <span class="edsys-breadcrumbs__separator">/</span>
                    <a href="/polezno-znat/" class="edsys-breadcrumbs__link">Полезная информация</a>
                    <span class="edsys-breadcrumbs__separator">/</span>
                    <span class="edsys-breadcrumbs__current">Таблицы токовых нагрузок</span>
                </nav>';
	}

	/**
	 * Generate related sections HTML
	 */
	public function renderRelatedSections()
	{
		$html = '<section class="edsys-cable-related">
                    <h3>Смежные разделы</h3>
                    <div class="edsys-cable-related__grid">';

		foreach ($this->relatedSections as $section) {
			$html .= sprintf(
				'<a href="%s" class="edsys-cable-related__link">
                    <div class="edsys-cable-related__icon">
                        <i class="ph %s"></i>
                    </div>
                    <div class="edsys-cable-related__content">
                        <span class="edsys-cable-related__title">%s</span>
                        <small class="edsys-cable-related__desc">%s</small>
                    </div>
                </a>',
				htmlspecialchars($section['url']),
				htmlspecialchars($section['icon']),
				htmlspecialchars($section['name']),
				htmlspecialchars($section['description'])
			);
		}

		$html .= '</div></section>';
		return $html;
	}

	/**
	 * Get local navigation data for current page
	 */
	public function getLocalNavigationData()
	{
		$localNav = [];
		foreach ($this->cableTypes as $key => $cable) {
			$localNav[] = [
				'id' => $key,
				'title' => $cable['name'],
				'anchor' => $key,
				'icon' => $cable['icon']
			];
		}
		return $localNav;
	}
}

?>