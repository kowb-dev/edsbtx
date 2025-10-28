<?php
/**
 * AJAX handler for catalog filtering
 * @version 1.1.2 (prev 1.1.0) | author: KW | uri: https://kowb.ru
 * Path: /local/templates/edsy_main/components/bitrix/catalog.section/catalog_list/ajax.php
 */

use Bitrix\Main\Loader;
use Bitrix\Main\Context;
use Bitrix\Main\Web\Json;

define('STOP_STATISTICS', true);
define('NO_KEEP_STATISTIC', true);
define('NO_AGENT_STATISTIC', true);
define('DisableEventsCheck', true);
define('PUBLIC_AJAX_MODE', true);

require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

global $APPLICATION;
$APPLICATION->RestartBuffer();
header('Content-Type: application/json; charset=UTF-8');

// Глобальный аварийный JSON, если случится фатал до sendJson()
$__fatalSent = false;
register_shutdown_function(function() use (&$__fatalSent) {
    if ($__fatalSent) return;
    $e = error_get_last();
    if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
        while (ob_get_level()) ob_end_clean();
        echo Json::encode(['success'=>false,'error'=>'Fatal: '.$e['message']], JSON_UNESCAPED_UNICODE);
    }
});

function sendJson($success, array $data = [], string $error = ''): void {
    global $__fatalSent;
    $__fatalSent = true;
    $resp = ['success' => (bool)$success];
    if ($success) { $resp += $data; } else { $resp['error'] = $error ?: 'Unknown error'; }
    while (ob_get_level()) ob_end_clean();
    echo Json::encode($resp, JSON_UNESCAPED_UNICODE);
    \CMain::FinalActions();
    die();
}

try {
    if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
        sendJson(false, [], 'Modules not loaded');
    }

    $request = Context::getCurrent()->getRequest();
    if (!$request->isPost() || $request->getHeader('X-Requested-With') !== 'XMLHttpRequest') {
        sendJson(false, [], 'Invalid request');
    }

    if (!check_bitrix_sessid($request->getPost('sessid'))) {
        sendJson(false, [], 'Bad session');
    }

    // Контракт: поддержим обе схемы именования
    $action = (string)$request->getPost('action') ?: 'filter';

    $iblockId = (int)(
        $request->getPost('iblock_id') ??
        $request->getPost('iblockId') ??
        0
    );
    if ($iblockId <= 0) {
        sendJson(false, [], 'Bad iblock');
    }

    // Категории: JSON-строка, массив categories[], fallback: 'all'
    $categories = [];
    $json = $request->getPost('categories');
    if (is_string($json) && $json !== '') {
        try {
            $tmp = Json::decode($json);
            if (is_array($tmp)) $categories = $tmp;
        } catch (\Throwable $e) {
            // игнорируем — попробуем массивом
        }
    }
    if (!$categories) {
        $categories = (array)$request->getPost('categories');
    }
    if (!$categories) {
        $categories = (array)$request->getPost('categories'); // дублируем на случай особенностей окружения
    }
    if (!$categories || !is_array($categories)) {
        $categories = ['all'];
    }

    // Фильтр элементов
    $arFilter = [
        'IBLOCK_ID' => $iblockId,
        'ACTIVE' => 'Y',
        'ACTIVE_DATE' => 'Y',
    ];
    if (!in_array('all', $categories, true)) {
        $arFilter['SECTION_ID'] = array_map('intval', $categories);
        $arFilter['INCLUDE_SUBSECTIONS'] = 'Y';
    }

    $arSelect = [
        'ID','IBLOCK_ID','IBLOCK_SECTION_ID','NAME',
        'PREVIEW_PICTURE','PREVIEW_TEXT','DETAIL_PAGE_URL',
        'PROPERTY_CML2_ARTICLE'
    ];

    $rs = \CIBlockElement::GetList(
        ['SORT'=>'ASC','NAME'=>'ASC'],
        $arFilter,
        false,
        ['nTopCount'=>200],
        $arSelect
    );

    ob_start();
    $count = 0;
    while ($ob = $rs->GetNextElement()) {
        $arItem = $ob->GetFields();
        $arItem['PROPERTIES'] = $ob->GetProperties();
        $count++;

        $picture = null;
        if (!empty($arItem['PREVIEW_PICTURE'])) {
            $picture = \CFile::ResizeImageGet(
                $arItem['PREVIEW_PICTURE'],
                ['width'=>80,'height'=>80],
                BX_RESIZE_IMAGE_PROPORTIONAL
            );
        }
        $article = (string)$arItem['PROPERTIES']['CML2_ARTICLE']['VALUE'];

        ?>
        <article 
            class="edsys-catalog__table-row" 
            data-section-id="<?= (int)$arItem['IBLOCK_SECTION_ID'] ?>"
            data-product-url="<?= htmlspecialcharsbx($arItem['DETAIL_PAGE_URL']) ?>"
            itemscope 
            itemtype="https://schema.org/Product"
        >
            <div class="edsys-catalog__table-col edsys-catalog__table-col--photo">
                <div class="edsys-catalog__table-photo">
                    <?php if ($picture && isset($picture['src'])): ?>
                        <img src="<?= htmlspecialcharsbx($picture['src']) ?>"
                             alt="<?= htmlspecialcharsbx($arItem['NAME']) ?>"
                             width="80" height="80" loading="lazy" itemprop="image">
                    <?php else: ?>
                        <div class="edsys-catalog__table-no-photo"><i class="ph ph-thin ph-image" aria-hidden="true"></i></div>
                    <?php endif; ?>
                </div>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--name">
                <span class="edsys-catalog__table-name" itemprop="name">
                    <?= htmlspecialcharsbx($arItem['NAME']) ?>
                </span>
                <?php if ($arItem['PREVIEW_TEXT']): ?>
                    <div class="edsys-catalog__table-desc"><?= htmlspecialcharsbx(strip_tags($arItem['PREVIEW_TEXT'])) ?></div>
                <?php endif; ?>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--article">
                <?= $article ? '<span itemprop="sku">'.htmlspecialcharsbx($article).'</span>' : '<span class="edsys-catalog__table-empty">—</span>' ?>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--price">
                <span class="edsys-catalog__table-price-request">Цена по запросу</span>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--actions">
                <div class="edsys-catalog__table-actions">
                    <button type="button" class="edsys-catalog__action-btn edsys-catalog__action-btn--favorite" data-product-id="<?= $arItem['ID'] ?>" title="Добавить в избранное" aria-label="Добавить в избранное">
                        <i class="ph ph-thin ph-heart"></i>
                    </button>
                    <button type="button" class="edsys-catalog__action-btn edsys-catalog__action-btn--compare" data-product-id="<?= $arItem['ID'] ?>" title="Добавить к сравнению" aria-label="Добавить к сравнению" aria-pressed="false">
                        <i class="ph ph-thin ph-chart-bar"></i>
                    </button>
                </div>
            </div>
        </article>
        <?php
    }
    $html = ob_get_clean();

    sendJson(true, [
        'html'  => $html,
        'count' => $count,
    ]);

} catch (\Throwable $e) {
    sendJson(false, [], 'Server error: '.$e->getMessage());
}