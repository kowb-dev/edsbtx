<?php
/**
 * File: /edsys_sku_search_diagnostics.php
 * Version: 1.0.0
 * Author: KW
 * URI: https://kowb.ru
 *
 * Назначение:
 * Диагностика причин, почему не находится товар по артикулу (SKU) в стандартном поиске Битрикс.
 * Запускать из браузера: /edsys_sku_search_diagnostics.php?sku=ED-123  (sku необязателен)
 *
 * Требования: запуск только администратором.
 */

/* ===== Bootstrap ===== */
define('NO_KEEP_STATISTIC', true);
define('NOT_CHECK_PERMISSIONS', true);
define('BX_WITH_ON_AFTER_EPILOG', false);
require $_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_before.php';

global $USER, $DB, $APPLICATION;

if (!$USER->IsAuthorized() || !$USER->IsAdmin()) {
    http_response_code(403);
    echo 'Access denied. Admins only.';
    die();
}

\Bitrix\Main\Loader::includeModule('main');
\Bitrix\Main\Loader::includeModule('iblock');
\Bitrix\Main\Loader::includeModule('catalog');
\Bitrix\Main\Loader::includeModule('search');

use Bitrix\Main\Application;
use Bitrix\Main\Config\Option;
use Bitrix\Catalog\CatalogIblockTable;

/* ===== Helpers ===== */
function edsys_out($label, $value, $ok = null) {
    $status = is_null($ok) ? '' : ($ok ? ' [OK]' : ' [FAIL]');
    echo '<div style="margin:6px 0;"><b>'.$label.':</b> '.htmlspecialcharsbx((string)$value).$status.'</div>';
}
function edsys_hr() { echo '<hr style="margin:16px 0;">'; }
function edsys_bool($v){ return ($v === 'Y' || $v === true) ? 'Y' : 'N'; }
function edsys_ok($cond){ return $cond ? 'OK' : 'FAIL'; }

/* ===== Input ===== */
$request = \Bitrix\Main\Context::getCurrent()->getRequest();
$inputSku = trim((string)$request->get('sku'));
$inputSkuNorm = mb_strtoupper(preg_replace('~\s+~u','',$inputSku));

$skuCandidates = ['CML2_ARTICLE','ARTICLE','ARTNUMBER','ARTICUL','SKU'];

/* ===== Page Head ===== */
echo '<meta charset="utf-8">';
echo '<div style="font:14px/1.45 -apple-system,BlinkMacSystemFont,Segoe UI,Roboto,Arial,sans-serif;max-width:980px;margin:24px auto;padding:0 16px">';
echo '<h2 style="margin:0 0 12px">edsys · SKU Search Diagnostics</h2>';
echo '<div>File: /edsys_sku_search_diagnostics.php • Version 1.0.0 • Author: KW • <a href="https://kowb.ru" target="_blank" rel="nofollow">kowb.ru</a></div>';
edsys_hr();

/* ===== Core environment ===== */
$engine = Option::get('search','full_text_engine','bitrix');
$useStemming = Option::get('search','use_stemming','Y');                // Морфология
$agentStemming = Option::get('search','agent_stemming','N');            // Запуск на агенте
$agentDuration = Option::get('search','agent_duration','3');            // Макс. длительность, сек
$minWordLen   = Option::get('search','min_word_length','3');            // Мин. длина слов
$indexNumbers = Option::get('search','include_numbers','N');            // Индексировать числа (имя опции зависит от версии; проверим обе)
if ($indexNumbers === null || $indexNumbers === '') {
    $indexNumbers = Option::get('search','index_numbers','N');
}
$lettersSym   = Option::get('search','letters','');                     // Символы (разделители/буквы), зависит от версии

echo '<h3>1) Search Engine & Morphology</h3>';
edsys_out('Full-text engine', $engine, in_array($engine, ['bitrix','sphinx','opensearch','mysql'], true));
edsys_out('Use morphology (use_stemming)', $useStemming, $useStemming==='Y');
edsys_out('Agent stemming (agent_stemming)', $agentStemming);
edsys_out('Agent duration, sec (agent_duration)', $agentDuration);
edsys_out('Min word length (min_word_length)', $minWordLen);
edsys_out('Index numbers (include_numbers/index_numbers)', $indexNumbers);
edsys_out('Letters/Symbols (letters)', $lettersSym !== '' ? $lettersSym : '[empty]');
edsys_hr();

/* ===== Catalog iblocks / SKU properties ===== */
echo '<h3>2) Catalogs & Article properties</h3>';

$catalogs = CatalogIblockTable::getList([
    'select' => ['IBLOCK_ID','PRODUCT_IBLOCK_ID','SKU_PROPERTY_ID','IBLOCK.TYPE_ID','IBLOCK.NAME'],
])->fetchAll();

if (!$catalogs) {
    edsys_out('Catalogs', 'Not found', false);
    edsys_hr();
} else {
    foreach ($catalogs as $row) {
        $pid = (int)$row['PRODUCT_IBLOCK_ID'] ?: (int)$row['IBLOCK_ID'];
        $isOffers = ((int)$row['PRODUCT_IBLOCK_ID'] > 0);
        $title = ($isOffers ? 'Offers IBLOCK' : 'Product IBLOCK');

        echo '<div style="background:#fafafa;border:1px solid #eee;border-radius:8px;padding:10px;margin:8px 0">';
        edsys_out($title, 'ID='.$pid.' • '.$row['iblock_catalog_iblock_name'].' (type='.$row['iblock_catalog_iblock_type_id'].')');

        // Найти свойства-кандидаты
        $propsRs = \CIBlockProperty::GetList(['SORT'=>'ASC'], ['IBLOCK_ID'=>$pid,'ACTIVE'=>'Y']);
        $found = [];
        while ($p = $propsRs->Fetch()) {
            $codeUp = mb_strtoupper($p['CODE']);
            if (in_array($codeUp, $skuCandidates, true) || preg_match('~(ART|SKU|ARTICLE)~i', $p['CODE'].$p['NAME'])) {
                $found[] = $p;
            }
        }

        if (!$found) {
            edsys_out('SKU property', 'Not found by common patterns', false);
        } else {
            foreach ($found as $p) {
                $flag = ($p['SEARCHABLE'] === 'Y' && $p['PROPERTY_TYPE'] === 'S');
                edsys_out(
                    'Prop #'.$p['ID'],
                    'CODE='.$p['CODE'].' • NAME='.$p['NAME'].' • TYPE='.$p['PROPERTY_TYPE'].' • SEARCHABLE='.$p['SEARCHABLE'],
                    $flag
                );
            }
        }

        // Пример элемента с не пустым артикулом
        if ($found) {
            $codes = array_map(fn($x) => $x['CODE'], $found);
            $filter = ['IBLOCK_ID'=>$pid,'ACTIVE'=>'Y','ACTIVE_DATE'=>'Y'];
            $select = ['ID','IBLOCK_ID','NAME','DETAIL_PAGE_URL'];
            foreach ($codes as $c) { $filter['!PROPERTY_'.$c] = false; }
            $el = \CIBlockElement::GetList(['ID'=>'ASC'],$filter,false,['nTopCount'=>1],$select)->GetNext();
            if ($el) {
                edsys_out('Sample element with SKU', $el['ID'].' • '.$el['NAME'].' • URL='.$el['DETAIL_PAGE_URL'], true);
            } else {
                edsys_out('Sample element with SKU', 'No active elements with non-empty SKU', false);
            }
        }

        echo '</div>';
    }
    edsys_hr();
}

/* ===== Search index state ===== */
echo '<h3>3) Search index state</h3>';

$conn = Application::getConnection();

$cntAll = (int)$conn->queryScalar("SELECT COUNT(*) FROM b_search_content");
$cntIblock = (int)$conn->queryScalar("SELECT COUNT(*) FROM b_search_content WHERE MODULE_ID='iblock'");
$cntText = (int)$conn->queryScalar("SELECT COUNT(*) FROM b_search_content_text");

edsys_out('Rows in b_search_content (all)', $cntAll, $cntAll>0);
edsys_out('Rows in b_search_content (iblock)', $cntIblock, $cntIblock>0);
edsys_out('Rows in b_search_content_text', $cntText, $cntText>0);

/* Попытка найти по SKU напрямую в индексе (как цель — точный токен) */
if ($inputSkuNorm !== '') {
    $like = $conn->getSqlHelper()->forSql($inputSkuNorm);
    $sqlHit = "
        SELECT COUNT(*) 
        FROM b_search_content sc
        INNER JOIN b_search_content_text sct ON sct.SEARCH_CONTENT_ID = sc.ID
        WHERE sc.MODULE_ID='iblock' AND UPPER(sct.SEARCHABLE_CONTENT) LIKE '%".$like."%'
    ";
    $hits = (int)$conn->queryScalar($sqlHit);
    edsys_out('Index contains token like "'.$inputSkuNorm.'"', $hits.' hit(s)', $hits>0);
}
edsys_hr();

/* ===== Live test via CSearch (optional) ===== */
echo '<h3>4) Live search test (CSearch)</h3>';
if ($inputSku === '') {
    echo '<div>Передайте параметр <code>?sku=ВАШ_АРТИКУЛ</code> для живой проверки поиска.</div>';
} else {
    $s = new \CSearch;
    $query = $inputSku;
    $lang = LANGUAGE_ID;
    $params = [
        'QUERY' => $query,
        'SITE_ID' => SITE_ID,
        'MODULE_ID' => 'iblock',
    ];
    $s->Search($params, [], ['STEMMING' => $useStemming === 'Y']);
    $found = [];
    while ($r = $s->GetNext()) {
        if ($r['MODULE_ID'] === 'iblock') {
            $found[] = [
                'TITLE' => $r['TITLE'],
                'URL'   => $r['URL'],
                'ITEM_ID' => $r['ITEM_ID'],
            ];
        }
    }

    edsys_out('Query', $query);
    edsys_out('Found results', count($found), count($found) > 0);

    if ($found) {
        echo '<ol style="padding-left:20px">';
        foreach ($found as $f) {
            echo '<li><a href="'.htmlspecialcharsbx($f['URL']).'" target="_blank">'.htmlspecialcharsbx($f['TITLE']).'</a> <span style="opacity:.7">#'.$f['ITEM_ID'].'</span></li>';
        }
        echo '</ol>';
    } else {
        echo '<div style="color:#b00020">Совпадений не найдено стандартным поиском.</div>';
        echo '<div style="opacity:.8">Если артикул содержит символы (-_/.\# и т.п.), проверьте «Морфология → Символы…», индексацию чисел, «SEARCHABLE=Y» у свойства и заново пересоздайте индекс.</div>';
    }
}
edsys_hr();

/* ===== Summary with flags ===== */
echo '<h3>5) Summary</h3>';
$summary = [];

/* Критичные условия */
$summary[] = ['Morphology enabled', edsys_ok($useStemming==='Y')];
$summary[] = ['Index (iblock) has rows', edsys_ok($cntIblock>0)];
$summary[] = ['Numbers indexed', edsys_ok($indexNumbers==='Y')];
$summary[] = ['Min word length <= 3', edsys_ok((int)$minWordLen <= 3)];

echo '<ul>';
foreach ($summary as $s) {
    $ok = (strpos($s[1],'OK')!==false);
    echo '<li>'.htmlspecialcharsbx($s[0]).': <b style="color:'.($ok?'#2e7d32':'#b00020').'">'.$s[1].'</b></li>';
}
echo '</ul>';

echo '<div style="margin-top:12px;opacity:.8">
Рекомендации при проблеме: 
1) У свойства артикула поставить <b>Индексируется (SEARCHABLE=Y)</b>. 
2) В настройках поиска включить <b>индексацию чисел</b> и задать <b>минимальную длину слова ≤ 2–3</b>. 
3) В «Морфология → Символы…» добавить специальные символы из артикула. 
4) Запустить <b>Пересоздание индекса</b> по инфоблокам товаров и ТП. 
5) Ограничить компонент поиска областями iblock_catalog/iblock_offers.
</div>';

echo '</div>';
