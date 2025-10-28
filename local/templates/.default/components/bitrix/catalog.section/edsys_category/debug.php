<?php
// Добавьте этот код в конец template.php для отладки (после закрывающего </div>)
// Только для режима отладки

if ($_GET['debug'] == 'Y' || $_SERVER['REMOTE_ADDR'] == '127.0.0.1'): ?>
	<div style="margin-top: 2rem; padding: 1rem; background: #f8f9fa; border: 1px solid #ddd; border-radius: 0.5rem; font-family: monospace; font-size: 0.875rem;">
		<h3 style="margin: 0 0 1rem 0; color: #333;">🐛 Debug Information</h3>

		<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 1rem;">
			<div>
				<h4 style="margin: 0 0 0.5rem 0; color: #666;">Параметры компонента ($arParams):</h4>
				<pre style="background: #fff; padding: 0.5rem; border: 1px solid #eee; border-radius: 0.25rem; margin: 0; overflow-x: auto;"><?= htmlspecialchars(print_r([
						'IBLOCK_ID' => $arParams['IBLOCK_ID'] ?? 'not set',
						'SECTION_ID' => $arParams['SECTION_ID'] ?? 'not set',
						'SECTION_CODE' => $arParams['SECTION_CODE'] ?? 'not set',
						'PAGE_ELEMENT_COUNT' => $arParams['PAGE_ELEMENT_COUNT'] ?? 'not set',
					], true)) ?></pre>
			</div>

			<div>
				<h4 style="margin: 0 0 0.5rem 0; color: #666;">Результат компонента ($arResult):</h4>
				<pre style="background: #fff; padding: 0.5rem; border: 1px solid #eee; border-radius: 0.25rem; margin: 0; overflow-x: auto;"><?= htmlspecialchars(print_r([
						'SECTION_ID' => $arResult['SECTION']['ID'] ?? 'not set',
						'SECTION_NAME' => $arResult['SECTION']['NAME'] ?? 'not set',
						'CURRENT_SECTION_ID' => $arResult['CURRENT_SECTION_ID'] ?? 'not set',
						'ITEMS_COUNT' => count($arResult['ITEMS'] ?? []),
					], true)) ?></pre>
			</div>

			<div>
				<h4 style="margin: 0 0 0.5rem 0; color: #666;">Переменные окружения:</h4>
				<pre style="background: #fff; padding: 0.5rem; border: 1px solid #eee; border-radius: 0.25rem; margin: 0; overflow-x: auto;"><?= htmlspecialchars(print_r([
						'REQUEST_URI' => $_SERVER['REQUEST_URI'] ?? 'not set',
						'SCRIPT_NAME' => $_SERVER['SCRIPT_NAME'] ?? 'not set',
						'GET_SECTION_ID' => $_GET['SECTION_ID'] ?? 'not set',
						'POST_SECTION_ID' => $_POST['SECTION_ID'] ?? 'not set',
						'CURRENT_SECTION_ID_FINAL' => $currentSectionId,
					], true)) ?></pre>
			</div>

			<div>
				<h4 style="margin: 0 0 0.5rem 0; color: #666;">JavaScript конфиг:</h4>
				<pre style="background: #fff; padding: 0.5rem; border: 1px solid #eee; border-radius: 0.25rem; margin: 0; overflow-x: auto;"><?= htmlspecialchars(print_r([
						'sectionId' => $currentSectionId,
						'iblockId' => $arParams['IBLOCK_ID'],
						'initialCount' => $initialItemsCount,
						'ajaxUrl' => $templateFolder . '/ajax.php',
						'isAuthorized' => $isAuthorized
					], true)) ?></pre>
			</div>
		</div>

		<div style="margin-top: 1rem; padding: 0.5rem; background: #e3f2fd; border-left: 4px solid #2196f3; color: #1565c0;">
			<strong>💡 Совет:</strong> Добавьте <code>?debug=Y</code> к URL для включения отладки.<br>
			Для скрытия этой информации уберите параметр debug или измените IP-адрес проверки.
		</div>
	</div>
<?php endif; ?>