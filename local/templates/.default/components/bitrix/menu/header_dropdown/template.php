<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

$this->setFrameMode(true);

if (empty($arResult)) return;

// Отладочная информация (удалите после настройки)
/*
echo '<pre style="background: #f0f0f0; padding: 10px; margin: 10px 0; font-size: 12px;">';
echo "DEBUG: arResult содержит " . count($arResult) . " элементов:\n";
foreach($arResult as $i => $item) {
    echo "[$i] " . $item['TEXT'] . " => " . $item['LINK'] . " (depth: " . $item['DEPTH_LEVEL'] . ")\n";
}
echo '</pre>';
*/
?>

<nav class="edsys-header__nav" role="navigation" aria-label="Основная навигация">
	<?
	$currentParent = null;
	$hasChildren = false;

	for($i = 0; $i < count($arResult); $i++):
		$arItem = $arResult[$i];

		// Проверяем есть ли дочерние элементы
		$hasChildren = false;
		if(isset($arResult[$i + 1]) && $arResult[$i + 1]['DEPTH_LEVEL'] > $arItem['DEPTH_LEVEL']) {
			$hasChildren = true;
		}

		if($arItem['DEPTH_LEVEL'] == 1): // Основные пункты меню
			?>
			<?if($hasChildren):?>
            <!-- Пункт с выпадающим меню -->
            <div class="edsys-header__nav-dropdown">
                <a href="<?=$arItem['LINK']?>"
                   class="edsys-header__nav-link edsys-header__nav-link--dropdown<?=($arItem['SELECTED'] ? ' active' : '')?>"
                   aria-haspopup="true"
                   aria-expanded="false">
					<?=$arItem['TEXT']?>
                    <div class="icon-wrapper"></div>
                    <i class="ph ph-thin ph-caret-down"></i>
                </a>
                <div class="edsys-header__dropdown-menu"
                     role="menu"
                     aria-hidden="true">
					<?
					// Выводим дочерние элементы
					$j = $i + 1;
					while($j < count($arResult) && $arResult[$j]['DEPTH_LEVEL'] > $arItem['DEPTH_LEVEL']):
						if($arResult[$j]['DEPTH_LEVEL'] == 2): // Только прямые дочерние
							?>
                            <a href="<?=$arResult[$j]['LINK']?>"
                               class="edsys-header__dropdown-link<?=($arResult[$j]['SELECTED'] ? ' active' : '')?>"
                               role="menuitem">
								<?=$arResult[$j]['TEXT']?>
                                <div class="icon-wrapper"></div>
                            </a>
						<?
						endif;
						$j++;
					endwhile;
					?>
                </div>
            </div>
		<?else:?>
            <!-- Простая ссылка -->
            <a href="<?=$arItem['LINK']?>"
               class="edsys-header__nav-link<?=($arItem['SELECTED'] ? ' active' : '')?>">
				<?=$arItem['TEXT']?>
                <div class="icon-wrapper"></div>
            </a>
		<?endif?>
		<?
		endif; // DEPTH_LEVEL == 1
	endfor;
	?>
</nav>

<script>
    // Инициализация выпадающих меню с улучшенной логикой hover
    document.addEventListener('DOMContentLoaded', function() {
        const dropdowns = document.querySelectorAll('.edsys-header__nav-dropdown');

        dropdowns.forEach(dropdown => {
            const link = dropdown.querySelector('.edsys-header__nav-link--dropdown');
            const menu = dropdown.querySelector('.edsys-header__dropdown-menu');

            if (!link || !menu) return;

            let hideTimeout = null;

            // Функция показа меню
            function showMenu() {
                clearTimeout(hideTimeout);
                link.setAttribute('aria-expanded', 'true');
                menu.setAttribute('aria-hidden', 'false');
                menu.style.display = 'block';
            }

            // Функция скрытия меню с задержкой
            function hideMenu() {
                hideTimeout = setTimeout(() => {
                    link.setAttribute('aria-expanded', 'false');
                    menu.setAttribute('aria-hidden', 'true');
                    menu.style.display = 'none';
                }, 150); // Задержка 150ms
            }

            // События мыши для dropdown контейнера
            dropdown.addEventListener('mouseenter', showMenu);
            dropdown.addEventListener('mouseleave', hideMenu);

            // События мыши для самого меню (на случай если курсор "перепрыгнет")
            menu.addEventListener('mouseenter', () => {
                clearTimeout(hideTimeout);
            });

            menu.addEventListener('mouseleave', hideMenu);

            // События клавиатуры
            link.addEventListener('keydown', (e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    const isExpanded = link.getAttribute('aria-expanded') === 'true';

                    if (isExpanded) {
                        clearTimeout(hideTimeout);
                        link.setAttribute('aria-expanded', 'false');
                        menu.setAttribute('aria-hidden', 'true');
                        menu.style.display = 'none';
                    } else {
                        showMenu();
                        const firstMenuItem = menu.querySelector('.edsys-header__dropdown-link');
                        if (firstMenuItem) {
                            setTimeout(() => firstMenuItem.focus(), 50);
                        }
                    }
                }
            });

            // Закрытие по Escape
            menu.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') {
                    clearTimeout(hideTimeout);
                    link.setAttribute('aria-expanded', 'false');
                    menu.setAttribute('aria-hidden', 'true');
                    menu.style.display = 'none';
                    link.focus();
                }
            });

            // Закрытие при клике вне меню
            document.addEventListener('click', (e) => {
                if (!dropdown.contains(e.target)) {
                    clearTimeout(hideTimeout);
                    link.setAttribute('aria-expanded', 'false');
                    menu.setAttribute('aria-hidden', 'true');
                    menu.style.display = 'none';
                }
            });
        });
    });
</script>