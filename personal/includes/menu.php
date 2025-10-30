<?php
/**
 * Общее меню для всех страниц личного кабинета
 * Файл: /personal/includes/menu.php
 * 
 * @version 1.4.0
 * @author KW https://kowb.ru
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) {
    die("Access denied");
}

global $USER, $APPLICATION;

if (!$USER->IsAuthorized()) {
    LocalRedirect('/auth/?backurl=' . urlencode($APPLICATION->GetCurPage()));
    die();
}

$arUser = $USER->GetByID($USER->GetID())->Fetch();
$userCompany = $arUser['WORK_COMPANY'] ?? '';

$curPage = $APPLICATION->GetCurPage();
$currentSection = 'dashboard';

if (strpos($curPage, '/personal/profile') !== false) {
    $currentSection = 'profile';
} elseif (strpos($curPage, '/personal/orders') !== false) {
    $currentSection = 'orders';
} elseif (strpos($curPage, '/personal/favorites') !== false) {
    $currentSection = 'favorites';
} elseif (strpos($curPage, '/personal/address') !== false) {
    $currentSection = 'address';
} elseif (strpos($curPage, '/personal/password') !== false) {
    $currentSection = 'password';
} elseif ($curPage === '/personal/' || $curPage === '/personal/index.php') {
    $currentSection = 'dashboard';
}
?>

<section class="edsys-account">
    <div class="edsys-account__container">
        
        <button class="edsys-account__mobile-toggle" type="button" aria-label="Открыть меню личного кабинета" aria-expanded="false">
            <i class="ph-thin ph-list" aria-hidden="true"></i>
            <span>Меню кабинета</span>
        </button>

        <div class="edsys-account__wrapper">
            
            <aside class="edsys-account__sidebar" aria-label="Навигация личного кабинета">
                <nav class="edsys-account__nav">
                    <h2 class="edsys-account__nav-title">Личный кабинет</h2>
                    
                    <?php if (!empty($userCompany)): ?>
                        <div class="edsys-account__company-badge">
                            <i class="ph-thin ph-buildings" aria-hidden="true"></i>
                            <span><?= htmlspecialcharsbx($userCompany) ?></span>
                        </div>
                    <?php endif; ?>

                    <ul class="edsys-account__menu">
                        <li class="edsys-account__menu-item">
                            <a href="/personal/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'dashboard') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'dashboard') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-house" aria-hidden="true"></i>
                                <span>Главная</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="/personal/profile/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'profile') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'profile') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-user-circle" aria-hidden="true"></i>
                                <span>Личная информация</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="/personal/orders/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'orders') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'orders') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-package" aria-hidden="true"></i>
                                <span>Мои заказы</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="/personal/favorites/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'favorites') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'favorites') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-heart" aria-hidden="true"></i>
                                <span>Избранное</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="/personal/address/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'address') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'address') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-map-pin" aria-hidden="true"></i>
                                <span>Адреса доставки</span>
                            </a>
                        </li>
                        
                        <li class="edsys-account__menu-item">
                            <a href="/personal/password/" 
                               class="edsys-account__menu-link <?= ($currentSection === 'password') ? 'is-active' : '' ?>"
                               aria-current="<?= ($currentSection === 'password') ? 'page' : 'false' ?>">
                                <i class="ph-thin ph-lock-key" aria-hidden="true"></i>
                                <span>Смена пароля</span>
                            </a>
                        </li>
                    </ul>

                    <a href="?logout=yes&<?= bitrix_sessid_get() ?>" class="edsys-account__logout">
                        <i class="ph-thin ph-sign-out" aria-hidden="true"></i>
                        <span>Выход</span>
                    </a>
                </nav>
            </aside>

<script>
(function() {
    'use strict';
    
    const toggleBtn = document.querySelector('.edsys-account__mobile-toggle');
    const sidebar = document.querySelector('.edsys-account__sidebar');
    const accountSection = document.querySelector('.edsys-account');
    
    if (!toggleBtn || !sidebar || !accountSection) return;
    
    function openMenu() {
        sidebar.classList.add('is-open');
        accountSection.classList.add('has-overlay');
        document.body.classList.add('edsys-menu-open');
        toggleBtn.setAttribute('aria-expanded', 'true');
        toggleBtn.setAttribute('aria-label', 'Закрыть меню личного кабинета');
    }
    
    function closeMenu() {
        sidebar.classList.remove('is-open');
        accountSection.classList.remove('has-overlay');
        document.body.classList.remove('edsys-menu-open');
        toggleBtn.setAttribute('aria-expanded', 'false');
        toggleBtn.setAttribute('aria-label', 'Открыть меню личного кабинета');
    }
    
    toggleBtn.addEventListener('click', function() {
        const isOpen = sidebar.classList.contains('is-open');
        isOpen ? closeMenu() : openMenu();
    });
    
    accountSection.addEventListener('click', function(e) {
        if (accountSection.classList.contains('has-overlay') && 
            !sidebar.contains(e.target) && 
            !toggleBtn.contains(e.target)) {
            closeMenu();
        }
    });
    
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar.classList.contains('is-open')) {
            closeMenu();
        }
    });
    
    const mediaQuery = window.matchMedia('(min-width: 48rem)');
    const handleMediaChange = function(e) {
        if (e.matches) {
            closeMenu();
        }
    };
    
    mediaQuery.addEventListener('change', handleMediaChange);
})();
</script>