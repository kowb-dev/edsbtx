<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

if (empty($arResult)) {
    return;
}
?>

<nav class="edsys-breadcrumbs-modern" aria-label="Навигация">
    <ul class="edsys-breadcrumbs-modern__list">
        <?php foreach ($arResult as $index => $arItem): ?>
        <li class="edsys-breadcrumbs-modern__item">
            <?php if (!empty($arItem["LINK"])): ?>
            <a href="<?=htmlspecialchars($arItem["LINK"])?>"
               class="edsys-breadcrumbs-modern__link">
                <?=htmlspecialchars($arItem["TITLE"])?>
            </a>
            <?php else: ?>
            <span class="edsys-breadcrumbs-modern__current">
                <?=htmlspecialchars($arItem["TITLE"])?>
            </span>
            <?php endif; ?>

            <?php if ($index < count($arResult) - 1): ?>
            <i class="ph ph-thin ph-caret-right edsys-breadcrumbs-modern__separator"></i>
            <?php endif; ?>
        </li>
        <?php endforeach; ?>
    </ul>
</nav>

<style>
.edsys-breadcrumbs-modern {
    margin-bottom: clamp(1rem, 3vw, 1.5rem);
}

.edsys-breadcrumbs-modern__list {
    display: flex;
    align-items: center;
    flex-wrap: wrap;
    gap: 0.5rem;
    list-style: none;
    margin: 0;
    padding: 0;
    font-size: clamp(0.75rem, 2vw, 0.875rem);
}

.edsys-breadcrumbs-modern__item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.edsys-breadcrumbs-modern__link {
    color: var(--edsys-text-muted, #666);
    text-decoration: none;
    transition: color 0.2s ease;
    padding: 0.25rem 0;
}

.edsys-breadcrumbs-modern__link:hover {
    color: var(--edsys-primary, #ff4757);
}

.edsys-breadcrumbs-modern__current {
    color: var(--edsys-text, #21242D);
    font-weight: 500;
}

.edsys-breadcrumbs-modern__separator {
    color: var(--edsys-text-muted, #666);
    font-size: 0.75em;
}

/* Mobile optimization */
@media (max-width: 640px) {
    .edsys-breadcrumbs-modern__item:not(:last-child):not(:first-child) {
        display: none;
    }

    .edsys-breadcrumbs-modern__item:nth-last-child(2)::before {
        content: '...';
        margin-right: 0.5rem;
        color: var(--edsys-text-muted, #666);
    }
}
</style>
