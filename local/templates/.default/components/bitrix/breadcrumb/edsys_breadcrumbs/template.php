<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (empty($arResult)) {
    return;
}
?>

<nav class="edsys-breadcrumbs" aria-label="Навигация по сайту" itemscope itemtype="http://schema.org/BreadcrumbList">
    <ul class="edsys-breadcrumbs__list">
        <?php foreach ($arResult as $index => $arItem): ?>
        <li class="edsys-breadcrumbs__item"
            itemprop="itemListElement"
            itemscope
            itemtype="http://schema.org/ListItem">

            <?php if (!empty($arItem["LINK"])): ?>
            <a href="<?=htmlspecialchars($arItem["LINK"])?>"
               class="edsys-breadcrumbs__link"
               itemprop="item">
                <span itemprop="name"><?=htmlspecialchars($arItem["TITLE"])?></span>
            </a>
            <?php else: ?>
            <span class="edsys-breadcrumbs__current" itemprop="name">
                <?=htmlspecialchars($arItem["TITLE"])?>
            </span>
            <?php endif; ?>

            <meta itemprop="position" content="<?=($index + 1)?>">

            <?php if ($index < count($arResult) - 1): ?>
            <i class="ph ph-thin ph-caret-right edsys-breadcrumbs__separator"></i>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<style>
.edsys-breadcrumbs {
    margin-bottom: clamp(1rem, 3vw, 1.5rem);
}

.edsys-breadcrumbs__list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
}

.edsys-breadcrumbs__item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: clamp(0.75rem, 2vw, 0.875rem);
}

.edsys-breadcrumbs__link {
    color: var(--edsys-text-muted, #666);
    text-decoration: none;
    transition: color 0.2s ease;
}

.edsys-breadcrumbs__link:hover {
    color: var(--edsys-primary, #ff4757);
}

.edsys-breadcrumbs__current {
    color: var(--edsys-text, #21242D);
    font-weight: 500;
}

.edsys-breadcrumbs__separator {
    color: var(--edsys-text-muted, #666);
    font-size: 0.75em;
}

@media (max-width: 640px) {
    .edsys-breadcrumbs__item:not(:last-child):not(:first-child) {
        display: none;
    }

    .edsys-breadcrumbs__item:nth-last-child(2)::before {
        content: '...';
        margin-right: 0.5rem;
        color: var(--edsys-text-muted, #666);
    }
}
</style>
