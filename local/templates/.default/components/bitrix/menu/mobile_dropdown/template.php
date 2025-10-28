<?php
/**
 * Mobile Accordion Menu Component Template
 * @version 1.5
 * @author EDS Development Team
 * @description Mobile dropdown accordion menu with smooth animations for B2B ecommerce
 */

if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if (empty($arResult)) return;
?>

<div class="edsys-mobile-accordion" id="mobileAccordionMenu">
    <div class="edsys-mobile-accordion__header">
        <h3 class="edsys-mobile-accordion__title">Навигация</h3>
        <button class="edsys-mobile-accordion__close" data-action="toggle-accordion-menu" aria-label="Закрыть меню">
            <i class="ph ph-thin ph-x"></i>
        </button>
    </div>

    <nav class="edsys-mobile-accordion__nav" role="navigation" aria-label="Мобильная навигация">
        <ul class="edsys-mobile-accordion__list">
			<?php
			$currentParent = null;
			$hasChildren = false;
			$menuItemId = 0;

			for($i = 0; $i < count($arResult); $i++):
				$arItem = $arResult[$i];
				$menuItemId++;

				// Check if item has children
				$hasChildren = false;
				if(isset($arResult[$i + 1]) && $arResult[$i + 1]['DEPTH_LEVEL'] > $arItem['DEPTH_LEVEL']) {
					$hasChildren = true;
				}

				if($arItem['DEPTH_LEVEL'] == 1): // Main menu items
					?>
                    <li class="edsys-mobile-accordion__item<?=($arItem['SELECTED'] ? ' edsys-mobile-accordion__item--active' : '')?>">
						<?php if($hasChildren): ?>
                            <div class="edsys-mobile-accordion__dropdown">
                                <button class="edsys-mobile-accordion__trigger<?=($arItem['SELECTED'] ? ' edsys-mobile-accordion__trigger--active' : '')?>"
                                        data-accordion-target="mobile-accordion-submenu-<?=$menuItemId?>"
                                        aria-expanded="<?=($arItem['SELECTED'] ? 'true' : 'false')?>"
                                        aria-controls="mobile-accordion-submenu-<?=$menuItemId?>">
                                    <span class="edsys-mobile-accordion__link-text"><?=$arItem['TEXT']?></span>
                                    <i class="ph ph-thin ph-caret-down edsys-mobile-accordion__icon"></i>
                                </button>

                                <div class="edsys-mobile-accordion__submenu"
                                     id="mobile-accordion-submenu-<?=$menuItemId?>"
                                     aria-hidden="<?=($arItem['SELECTED'] ? 'false' : 'true')?>">
                                    <div class="edsys-mobile-accordion__submenu-content">
                                        <a href="<?=$arItem['LINK']?>"
                                           class="edsys-mobile-accordion__submenu-link edsys-mobile-accordion__submenu-link--parent">
                                            <i class="ph ph-thin ph-house"></i>
                                            <span>Все в разделе "<?=$arItem['TEXT']?>"</span>
                                        </a>

										<?php
										// Output child items
										$j = $i + 1;
										while($j < count($arResult) && $arResult[$j]['DEPTH_LEVEL'] > $arItem['DEPTH_LEVEL']):
											if($arResult[$j]['DEPTH_LEVEL'] == 2):
												?>
                                                <a href="<?=$arResult[$j]['LINK']?>"
                                                   class="edsys-mobile-accordion__submenu-link<?=($arResult[$j]['SELECTED'] ? ' edsys-mobile-accordion__submenu-link--active' : '')?>">
                                                    <i class="ph ph-thin ph-circle"></i>
                                                    <span><?=$arResult[$j]['TEXT']?></span>
                                                </a>
											<?php
											endif;
											$j++;
										endwhile;
										?>
                                    </div>
                                </div>
                            </div>
						<?php else: ?>
                            <a href="<?=$arItem['LINK']?>"
                               class="edsys-mobile-accordion__link<?=($arItem['SELECTED'] ? ' edsys-mobile-accordion__link--active' : '')?>">
                                <span class="edsys-mobile-accordion__link-text"><?=$arItem['TEXT']?></span>
                                <i class="ph ph-thin ph-arrow-right"></i>
                            </a>
						<?php endif; ?>
                    </li>
				<?php
				endif;
			endfor;
			?>
        </ul>
    </nav>

    <div class="edsys-mobile-accordion__footer">
        <div class="edsys-mobile-accordion__contacts">
            <a href="tel:+79105273538" class="edsys-mobile-accordion__contact-link">
                <i class="ph ph-thin ph-phone"></i>
                <span>+7 (910) 527-35-38</span>
            </a>
            <a href="https://wa.me/79999999999" class="edsys-mobile-accordion__contact-link">
                <i class="ph ph-thin ph-whatsapp-logo"></i>
                <span>WhatsApp</span>
            </a>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const mobileAccordion = document.getElementById('mobileAccordionMenu');
        const accordionTriggers = mobileAccordion.querySelectorAll('.edsys-mobile-accordion__trigger');

        accordionTriggers.forEach(trigger => {
            const targetId = trigger.getAttribute('data-accordion-target');
            const submenu = document.getElementById(targetId);
            const icon = trigger.querySelector('.edsys-mobile-accordion__icon');

            if (!submenu || !icon) return;

            const isInitiallyActive = trigger.classList.contains('edsys-mobile-accordion__trigger--active');

            if (isInitiallyActive) {
                submenu.style.maxHeight = 'none';
                submenu.style.overflow = 'visible';
                icon.style.transform = 'rotate(180deg)';
            } else {
                submenu.style.maxHeight = '0px';
                submenu.style.overflow = 'hidden';
            }

            submenu.style.transition = 'max-height 0.3s cubic-bezier(0.4, 0, 0.2, 1)';

            trigger.addEventListener('click', function(e) {
                e.preventDefault();

                const isExpanded = trigger.getAttribute('aria-expanded') === 'true';

                if (isExpanded) {
                    closeSubmenu(trigger, submenu, icon);
                } else {
                    accordionTriggers.forEach(otherTrigger => {
                        if (otherTrigger !== trigger) {
                            const otherTargetId = otherTrigger.getAttribute('data-accordion-target');
                            const otherSubmenu = document.getElementById(otherTargetId);
                            const otherIcon = otherTrigger.querySelector('.edsys-mobile-accordion__icon');

                            if (otherSubmenu && otherIcon) {
                                closeSubmenu(otherTrigger, otherSubmenu, otherIcon);
                            }
                        }
                    });

                    openSubmenu(trigger, submenu, icon);
                }
            });

            trigger.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    trigger.click();
                }
            });
        });

        function openSubmenu(trigger, submenu, icon) {
            const content = submenu.querySelector('.edsys-mobile-accordion__submenu-content');
            const contentHeight = content.scrollHeight;

            trigger.setAttribute('aria-expanded', 'true');
            submenu.setAttribute('aria-hidden', 'false');
            trigger.classList.add('edsys-mobile-accordion__trigger--active');
            icon.style.transform = 'rotate(180deg)';

            submenu.style.maxHeight = contentHeight + 'px';

            setTimeout(() => {
                if (trigger.getAttribute('aria-expanded') === 'true') {
                    submenu.style.maxHeight = 'none';
                }
            }, 300);
        }

        function closeSubmenu(trigger, submenu, icon) {
            const content = submenu.querySelector('.edsys-mobile-accordion__submenu-content');
            const contentHeight = content.scrollHeight;

            submenu.style.maxHeight = contentHeight + 'px';
            submenu.offsetHeight;

            trigger.setAttribute('aria-expanded', 'false');
            submenu.setAttribute('aria-hidden', 'true');
            trigger.classList.remove('edsys-mobile-accordion__trigger--active');
            icon.style.transform = 'rotate(0deg)';
            submenu.style.maxHeight = '0px';
        }

        // Multiple selectors for menu toggle buttons
        const mobileAccordionToggles = document.querySelectorAll([
            '[data-action="toggle-accordion-menu"]',
            '.edsys-header__mobile-hamburger',
            '[data-action="toggle-menu"]'
        ].join(', '));

        const overlay = document.querySelector('.edsys-overlay');

        // Add click handlers to all toggle buttons
        mobileAccordionToggles.forEach(toggle => {
            if (toggle) {
                toggle.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();

                    const isActive = mobileAccordion.classList.contains('active');

                    if (isActive) {
                        mobileAccordion.classList.remove('active');
                        if (overlay) overlay.classList.remove('active');
                        document.body.style.overflow = '';
                    } else {
                        mobileAccordion.classList.add('active');
                        if (overlay) overlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                    }
                });
            }
        });

        if (overlay) {
            overlay.addEventListener('click', function() {
                mobileAccordion.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && mobileAccordion.classList.contains('active')) {
                mobileAccordion.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            }
        });
    });
</script>