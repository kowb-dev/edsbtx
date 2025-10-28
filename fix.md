Ок, теперь у нас **код 200, но тело пустое** — значит PHP-скрипт либо «умирает» до вывода (фатал/early exit), либо контракт запроса/ответа не совпадает и в итоге `echo` не выполняется. Делаем 3 вещи:

---

# 1) Синхронизируем контракт запроса (JS ⇄ PHP)

Судя по вашему `ajax.php`, он ждёт:

* `action=filter`
* `iblock_id` (snake_case)
* `categories` как **JSON-строку** (а не `categories[]`)
* `sessid`

А ваш фронт шлёт:

* `iblockId`
* `'categories[]'`
* возможно без `action`

Исправляем фронт, чтобы он отправлял **оба варианта** (для совместимости), а `categories` дополнительно как JSON.

```js
/* File: /local/templates/edsy_main/components/bitrix/catalog.section/catalog_list/script.js
 * @version 1.3.3 (prev 1.3.2) | author: KW | uri: https://kowb.ru
 */

// внутри класса CatalogFilter, метод applyFilters()

async applyFilters() {
  const selected = this.getSelectedCategories();

  try {
    this.showLoading && this.showLoading();

    const body = new URLSearchParams({
      // ожидаемое PHP
      action: 'filter',
      iblock_id: String(window.edysCatalogConfig?.iblockId || 0),
      categories: JSON.stringify(selected),
      sessid: window.edysCatalogConfig?.sessid || '',

      // обратная совместимость с прошлой версией
      iblockId: String(window.edysCatalogConfig?.iblockId || 0),
    });

    // если хотите — оставим и массивом, но JSON обязателен:
    selected.forEach(v => body.append('categories[]', v));

    const res = await fetch(window.edysCatalogConfig.ajaxUrl, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-Requested-With': 'XMLHttpRequest'
      },
      body
    });

    const raw = await res.text();
    console.log('[filter][raw-status]', res.status);
    console.log('[filter][raw-body]', raw);

    if (!raw.trim()) {
      throw new Error('Empty JSON body');
    }

    const data = JSON.parse(raw);

    if (!data.success) {
      console.error('[filter][server-error]', data.error);
      return;
    }

    // html + count
    if (typeof data.html === 'string') {
      const container = document.querySelector('#catalog-products .edsys-catalog__table-body');
      if (container) container.innerHTML = data.html;
    }
    if (typeof data.count === 'number') {
      this.updateProductsCount && this.updateProductsCount(data.count);
      this.toggleEmptyState && this.toggleEmptyState(data.count === 0);
    }

  } catch (err) {
    console.error('Filter fetch error:', err);
  } finally {
    this.hideLoading && this.hideLoading();
    if (window.innerWidth < 1024) this.closeFilter && this.closeFilter();
  }
}
```

---

# 2) Чиним `ajax.php`: гарантируем JSON-ответ при любом исходе

Добавим:

* `PUBLIC_AJAX_MODE`, `$APPLICATION->RestartBuffer()`, заголовок JSON.
* Сброс **всех** активных буферов перед `echo`.
* Толерантный парсинг входных параметров (и `iblockId`, и `iblock_id`; и `categories[]`, и `categories` JSON).
* `register_shutdown_function` для отлова фаталов и отдачи JSON даже при падении.

```php
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
        $count++;

        $picture = null;
        if (!empty($arItem['PREVIEW_PICTURE'])) {
            $picture = \CFile::ResizeImageGet(
                $arItem['PREVIEW_PICTURE'],
                ['width'=>200,'height'=>200],
                BX_RESIZE_IMAGE_PROPORTIONAL
            );
        }
        $article = (string)$arItem['PROPERTY_CML2_ARTICLE_VALUE'];

        ?>
        <div class="edsys-catalog__table-row" data-section-id="<?= (int)$arItem['IBLOCK_SECTION_ID'] ?>">
            <div class="edsys-catalog__table-col edsys-catalog__table-col--photo">
                <a class="edsys-catalog__table-photo" href="<?= htmlspecialcharsbx($arItem['DETAIL_PAGE_URL']) ?>">
                    <?php if ($picture && isset($picture['src'])): ?>
                        <img src="<?= htmlspecialcharsbx($picture['src']) ?>"
                             alt="<?= htmlspecialcharsbx($arItem['NAME']) ?>"
                             width="80" height="80" loading="lazy">
                    <?php else: ?>
                        <div class="edsys-catalog__table-no-photo"><i class="ph ph-thin ph-image" aria-hidden="true"></i></div>
                    <?php endif; ?>
                </a>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--name">
                <a class="edsys-catalog__table-name" href="<?= htmlspecialcharsbx($arItem['DETAIL_PAGE_URL']) ?>">
                    <?= htmlspecialcharsbx($arItem['NAME']) ?>
                </a>
                <?php if (!empty($arItem['PREVIEW_TEXT'])): ?>
                    <div class="edsys-catalog__table-desc"><?= htmlspecialcharsbx(strip_tags($arItem['PREVIEW_TEXT'])) ?></div>
                <?php endif; ?>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--article">
                <?= $article ? '<span itemprop="sku">'.htmlspecialcharsbx($article).'</span>' : '<span class="edsys-catalog__table-empty">—</span>' ?>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--price">
                <span class="edsys-catalog__table-price-request">Цена по запросу</span>
            </div>
            <div class="edsys-catalog__table-col edsys-catalog__table-col--actions"></div>
        </div>
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
```

> Это гарантирует, что **даже при фатале** вы получите JSON с текстом ошибки, а не пустое тело. И снимает вероятность «мусора» благодаря `RestartBuffer()` + полному `ob_end_clean()`.

---

# 3) Быстрые проверки «почему пусто»

Если после правок всё ещё пусто — быстро проверяем:

1. **URL**
   `window.edysCatalogConfig.ajaxUrl` реально указывает на ваш `ajax.php` (а не на каталог/папку без индекса).

2. **Сессия**
   В `template.php` есть конфиг до подключения `script.js`:

   ```php
   <script>
     window.edysCatalogConfig = {
       iblockId: <?= (int)$arParams['IBLOCK_ID'] ?>,
       ajaxUrl: '<?= CUtil::JSEscape($componentPath) ?>/ajax.php',
       sessid: '<?= bitrix_sessid() ?>'
     };
   </script>
   ```

   Если `sessid` не совпадает — сервер вернёт `Bad session` (теперь вы это увидите в JSON).

3. **PHP Notices/Warnings**
   На проде `display_errors=Off`. Если было `On`, HTML предупреждение ломало JSON. Сейчас это не страшно — мы сбрасываем буферы перед JSON; но рекомендую отключить вывод в браузер и вести лог.

4. **Несовпадение имён полей**
   После синхронизации из п.1 это исключили: сервер принимает **оба** формата.

---

## Что вы увидите в консоли после исправлений

* `[filter][raw-status] 200`
* `[filter][raw-body] {"success":true,"html":"<div ...>...","count":23}` — не пусто
* При ошибке: `{"success":false,"error":"...подробно..."}` — тоже не пусто

И фронт больше не упадёт с `Empty JSON body`.

