<?php
require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/prolog_before.php');

use Bitrix\Main\Loader;
use Bitrix\Main\Context;

if (!Loader::includeModule('iblock') || !Loader::includeModule('catalog')) {
    http_response_code(500);
    echo 'Required modules not installed';
    return;
}

$request = Context::getCurrent()->getRequest();
$postData = json_decode($request->getInput(), true);
$ids = $postData['ids'] ?? [];

if ($request->isPost() && !empty($ids) && is_array($ids)) {
    $arSelect = [
        "ID", "IBLOCK_ID", "NAME", "PREVIEW_PICTURE", "DETAIL_PICTURE",
        "DETAIL_PAGE_URL", "PREVIEW_TEXT"
    ];
    $arFilter = ["IBLOCK_ID" => 7, "ID" => $ids, "ACTIVE" => "Y"];
    $res = CIBlockElement::GetList(["NAME" => "ASC"], $arFilter, false, false, $arSelect);

    while ($ob = $res->GetNextElement()) {
        $arFields = $ob->GetFields();
        $arProps = $ob->GetProperties();

        $article = '';
        $articleProps = ['CML2_ARTICLE', 'ARTICLE', 'ART', 'ARTICUL'];
        foreach ($articleProps as $propCode) {
            if (!empty($arProps[$propCode]['VALUE'])) {
                $article = is_array($arProps[$propCode]['VALUE']) ? $arProps[$propCode]['VALUE'][0] : $arProps[$propCode]['VALUE'];
                break;
            }
        }

        $arImage = CFile::ResizeImageGet(
            $arFields['PREVIEW_PICTURE'] ?: $arFields['DETAIL_PICTURE'],
            ['width' => 100, 'height' => 100],
            BX_RESIZE_IMAGE_PROPORTIONAL,
            true
        );
        ?>
        <a href="<?= $arFields['DETAIL_PAGE_URL'] ?>" class="edsys-favorite-item">
            <div class="edsys-favorite-item__image">
                <?php if ($arImage && $arImage['src']): ?>
                    <img src="<?= $arImage['src'] ?>" alt="<?= htmlspecialchars($arFields['NAME']) ?>" width="100" height="100" loading="lazy">
                <?php else: ?>
                    <div class="edsys-favorite-item__no-image"><i class="ph ph-thin ph-image"></i></div>
                <?php endif; ?>
            </div>
            <div class="edsys-favorite-item__info">
                <h3 class="edsys-favorite-item__title"><?= $arFields['NAME'] ?></h3>
                <?php if (!empty($arFields['PREVIEW_TEXT'])): ?>
                    <div class="edsys-favorite-item__description">
                        <?= strip_tags($arFields['PREVIEW_TEXT']) ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="edsys-favorite-item__article">
                <span>Артикул</span>
                <span><?= !empty($article) ? htmlspecialchars($article) : '—' ?></span>
            </div>
        </a>
        <?php
    }
}

require_once($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/main/include/epilog_after.php');
