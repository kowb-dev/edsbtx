<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

/**
 * @var array $arParams
 * @var array $arResult
 * @var CMain $APPLICATION
 * @var CUser $USER
 * @var CDatabase $DB
 * @var CBitrixComponentTemplate $this
 * @var string $templateName
 * @var string $templateFile
 * @var string $templateFolder
 * @var string $componentPath
 * @var CBitrixComponent $component
 */

$this->setFrameMode(true);

if ($arResult['CODE'] == 'korobki-kommutatsionnye') {
    ?>
    <style>
        .subcategories-container {
            max-width: 1600px;
            margin: 5rem auto; /* 5rem top/bottom, auto left/right for centering */
            padding: 0 20px; /* Add some padding to the sides */
        }
        .breadcrumbs {
            margin-bottom: 20px;
            text-align: left;
            font-size: 0.9em;
        }
        .breadcrumbs a {
            text-decoration: none;
            color: #007bff; /* Example color for links */
        }
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        .breadcrumbs span {
            color: #777;
        }
        .subcategories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .subcategory-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            transition: box-shadow 0.3s;
        }
        .subcategory-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .subcategory-card img {
            max-width: 90%; /* Initially smaller */
            height: auto;
            margin-bottom: 16px;
            transition: transform 0.3s ease; /* Smooth animation */
        }
        .subcategory-card:hover img {
            transform: scale(1.05);
        }
        .subcategory-card span {
            font-weight: 500;
            text-align: center;
        }
    </style>
    <?php
    ?>
    <style>
        .subcategories-container {
            max-width: 1600px;
            margin: 5rem auto; /* 5rem top/bottom, auto left/right for centering */
            padding: 0 20px; /* Add some padding to the sides */
        }
        .breadcrumbs {
            margin-bottom: 20px;
            text-align: left;
            font-size: 0.9em;
        }
        .breadcrumbs a {
            text-decoration: none;
            color: #007bff; /* Example color for links */
        }
        .breadcrumbs a:hover {
            text-decoration: underline;
        }
        .breadcrumbs span {
            color: #777;
        }
        .subcategories {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .subcategory-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: inherit;
            border: 1px solid #eee;
            border-radius: 8px;
            padding: 16px;
            transition: box-shadow 0.3s;
        }
        .subcategory-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .subcategory-card img {
            max-width: 90%; /* Initially smaller */
            height: auto;
            margin-bottom: 16px;
            transition: transform 0.3s ease; /* Smooth animation */
        }
        .subcategory-card:hover img {
            transform: scale(1.05);
        }
        .subcategory-card span {
            font-weight: 500;
            text-align: center;
        }
    </style>
    <div class="subcategories-container">
        <div class="breadcrumbs">
            <a href="/">Главная</a> / <span>коробки коммутационные</span>
        </div>
        <div class="subcategories">
            <a href="/cat/korobki-kommutatsionnye-bez-profilya/" class="subcategory-card">
                <img src="/upload/iblock/e6c/kkxzakekerj9jw2z794xh1kujdgh84co.jpg" alt="Без профиля">
                <span>Без профиля</span>
            </a>
            <a href="/cat/korobki-s-profilem/" class="subcategory-card">
                <img src="/upload/iblock/310/nr3mzcvnhm2jmts4tp3bsnhbeocicg1s.jpg" alt="С профилем">
                <span>С профилем</span>
            </a>
            <a href="/cat/korobki-kommutatsionnye-skoshennye/" class="subcategory-card">
                <img src="/upload/iblock/052/fxc45564ov2bl3unca8endq68g8t719i.jpg" alt="Скошенная">
                <span>Скошенные</span>
            </a>
            <a href="/cat/korobki-kommutatsionnye-vstraivaemye" class="subcategory-card">
                <img src="/upload/iblock/3a1/hmd6l05vsmyejx8r7pt6jcxe26u4cdtm.jpg" alt="Встраиваемая">
                <span>Встраиваемые</span>
            </a>
        </div>
    </div>

    <?php
} else {
// Подключаем CSS и JS
$this->addExternalCSS($templateFolder . "/style.css");
$this->addExternalCSS($templateFolder . "/css/compare.css");
$this->addExternalJS($templateFolder . "/script.js");

// Проверяем авторизацию пользователя
$isAuthorized = $USER->IsAuthorized();

// Получаем правильный ID секции из модификатора
$currentSectionId = !empty($arResult['CURRENT_SECTION_ID']) ? $arResult['CURRENT_SECTION_ID'] : 0;

// Параметры пагинации - 40 товаров на странице
$itemsPerPage = 40;
$totalItems = count($arResult['ITEMS']);
$currentPage = intval($_GET['page']) ?: 1;
$totalPages = ceil($totalItems / $itemsPerPage);

// Получаем товары для текущей страницы
$startIndex = ($currentPage - 1) * $itemsPerPage;
$currentPageItems = array_slice($arResult['ITEMS'], $startIndex, $itemsPerPage);
?>

    <div class="edsys-catalog-section" data-component-id="<?= $currentSectionId ?>">

        <!-- Хлебные крошки -->
		<?php if (!empty($arResult['SECTION']['PATH'])): ?>
            <nav class="edsys-breadcrumbs" aria-label="Навигация по разделам">
                <ol class="edsys-breadcrumbs__list">
                    <li class="edsys-breadcrumbs__item">
                        <a href="/" class="edsys-breadcrumbs__link">Главная</a>
                    </li>
					<?php foreach ($arResult['SECTION']['PATH'] as $path): ?>
                        <li class="edsys-breadcrumbs__item">
							<?php if ($path['IBLOCK_SECTION_ID']): ?>
                                <a href="<?= $path['SECTION_PAGE_URL'] ?>" class="edsys-breadcrumbs__link">
									<?= $path['NAME'] ?>
                                </a>
							<?php else: ?>
                                <span class="edsys-breadcrumbs__current"><?= $path['NAME'] ?></span>
							<?php endif; ?>
                        </li>
					<?php endforeach; ?>
                </ol>
            </nav>
		<?php endif; ?>

        <!-- Основной контент с боковыми фильтрами -->
        <div class="edsys-catalog-layout">

            <!-- Боковая панель фильтров -->
            <aside class="edsys-sidebar-filters" id="sidebar-filters">
                <div class="edsys-sidebar-filters__header">
                    <h2 class="edsys-sidebar-filters__title">
                        <i class="ph ph-thin ph-funnel"></i>
                        Фильтры
                    </h2>
                    <button type="button" class="edsys-sidebar-filters__close" aria-label="Закрыть фильтры">
                        <i class="ph ph-thin ph-x"></i>
                    </button>
                </div>

                <div class="edsys-sidebar-filters__content">
                    <!-- Фильтр по вводу -->
                    <div class="edsys-filter-group">
                        <h3 class="edsys-filter-group__title">Ввод</h3>
                        <div class="edsys-filter-group__content">
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="CEE_16A_3pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">CEE 16A 3-pin</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="CEE_16A_5pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">CEE 16A 5-pin</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="CEE_32A_3pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">CEE 32A 3-pin</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="CEE_32A_5pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">CEE 32A 5-pin</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="PowerCon_20A">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">PowerCon 20A</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="Revos_16pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Revos 16-pin</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="Schuko_16A">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Schuko 16A</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_input[]" value="Socapex_19pin">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Socapex 19-pin</span>
                            </label>
                        </div>
                    </div>

                    <!-- Фильтр дополнительно -->
                    <div class="edsys-filter-group">
                        <h3 class="edsys-filter-group__title">Дополнительно</h3>
                        <div class="edsys-filter-group__content">
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_additional[]" value="circuit_breaker">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Автомат. выключатели</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_additional[]" value="ammeter">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Амперметр</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_additional[]" value="voltmeter">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Вольтметр</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_additional[]" value="cable_input">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Кабельный ввод</span>
                            </label>
                            <label class="edsys-checkbox">
                                <input type="checkbox" name="filter_additional[]" value="pass_through">
                                <span class="edsys-checkbox__mark"></span>
                                <span class="edsys-checkbox__label">Проходная розетка</span>
                            </label>
                        </div>
                    </div>

                    <!-- Кнопки управления фильтрами -->
                    <div class="edsys-sidebar-filters__actions">
                        <button type="button" class="edsys-btn edsys-btn--primary edsys-filters__apply">
                            Применить
                        </button>
                        <button type="button" class="edsys-btn edsys-btn--secondary edsys-filters__reset">
                            Сбросить
                        </button>
                    </div>
                </div>
            </aside>

            <!-- Основной контент -->
            <main class="edsys-catalog-main">

                <!-- Заголовок категории с сортировкой -->
                <header class="edsys-category-header">
                    <h1 class="edsys-category-title"><?= $arResult['SECTION']['NAME'] ?></h1>

                    <!-- Сортировка для десктопа -->
                    <div class="edsys-sort edsys-sort--desktop">
                        <select id="catalog-sort" class="edsys-sort__select">
                            <option value="popularity">Сначала популярные</option>
                            <option value="price_asc">Сначала дешевле</option>
                            <option value="price_desc">Сначала дороже</option>
                            <option value="name_asc">По названию (А-Я)</option>
                            <option value="name_desc">По названию (Я-А)</option>
                            <option value="date_desc">Сначала новые</option>
                        </select>
                    </div>
                </header>

                <!-- Статичные мобильные контролы (только фильтры и сортировка) -->
                <div class="edsys-mobile-controls edsys-mobile-controls--static" id="mobile-controls-static">
                    <div class="edsys-mobile-controls__filters">
                        <button class="edsys-mobile-filter-btn" type="button" aria-expanded="false">
                            <i class="ph ph-thin ph-funnel"></i>
                            <span>Фильтры</span>
                            <span class="edsys-filters-counter" id="filters-counter-static" style="display: none;">0</span>
                        </button>

                        <div class="edsys-sort edsys-sort--mobile">
                            <select class="edsys-sort__select">
                                <option value="popularity">Сначала популярные</option>
                                <option value="price_asc">Сначала дешевле</option>
                                <option value="price_desc">Сначала дороже</option>
                                <option value="name_asc">По названию (А-Я)</option>
                                <option value="name_desc">По названию (Я-А)</option>
                                <option value="date_desc">Сначала новые</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Плавающие мобильные контролы (при скролле вверх) -->
                <div class="edsys-mobile-controls edsys-mobile-controls--floating" id="mobile-controls-floating">
                    <div class="edsys-mobile-controls__search">
                        <button class="edsys-mobile-search__btn" data-action="toggle-catalog" aria-label="Каталог">
                            <i class="ph ph-thin ph-list-magnifying-glass"></i>
                        </button>

                        <div class="edsys-mobile-search__form">
                            <form class="edsys-search-form" action="#" method="get">
                                <input type="search" class="edsys-search-input" placeholder="Поиск товара..." name="q" aria-label="Поиск товара">
                                <button type="submit" class="edsys-search-btn" aria-label="Найти">
                                    <i class="ph ph-thin ph-magnifying-glass"></i>
                                </button>
                            </form>
                        </div>

                        <button class="edsys-mobile-search__btn" data-action="toggle-menu" aria-label="Меню">
                            <i class="ph ph-thin ph-list"></i>
                        </button>
                    </div>

                    <div class="edsys-mobile-controls__filters">
                        <button class="edsys-mobile-filter-btn" type="button" aria-expanded="false">
                            <i class="ph ph-thin ph-funnel"></i>
                            <span>Фильтры</span>
                            <span class="edsys-filters-counter" id="filters-counter-floating" style="display: none;">0</span>
                        </button>

                        <div class="edsys-sort edsys-sort--mobile">
                            <select class="edsys-sort__select">
                                <option value="popularity">Сначала популярные</option>
                                <option value="price_asc">Сначала дешевле</option>
                                <option value="price_desc">Сначала дороже</option>
                                <option value="name_asc">По названию (А-Я)</option>
                                <option value="name_desc">По названию (Я-А)</option>
                                <option value="date_desc">Сначала новые</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Информация о результатах -->
                <div class="edsys-catalog-info">
                <span class="edsys-catalog-info__count">
                    Показано <?= count($currentPageItems) ?> товаров (<?= $startIndex + 1 ?>-<?= $startIndex + count($currentPageItems) ?> из <?= $totalItems ?>)
                </span>
                </div>

                <!-- Сетка товаров -->
                <div class="edsys-products-grid" id="products-grid">
					<?php foreach ($currentPageItems as $key => $arItem): ?>
						<?php
						$this->AddEditAction($arItem['ID'], $arItem['EDIT_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_EDIT"));
						$this->AddDeleteAction($arItem['ID'], $arItem['DELETE_LINK'], CIBlock::GetArrayByID($arItem["IBLOCK_ID"], "ELEMENT_DELETE"), array("CONFIRM" => Loc::getMessage('CT_BCS_ELEMENT_DELETE_CONFIRM')));

						// Получаем цены
						$arPrice = array();
						if (!empty($arItem['PRICES'])) {
							foreach ($arItem['PRICES'] as $code => $price) {
								if ($price['CAN_ACCESS']) {
									$arPrice = $price;
									break;
								}
							}
						}

						// Формируем основное изображение
						$arImage = false;
						if (!empty($arItem['PREVIEW_PICTURE'])) {
							$arImage = CFile::ResizeImageGet($arItem['PREVIEW_PICTURE'], array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						} elseif (!empty($arItem['DETAIL_PICTURE'])) {
							$arImage = CFile::ResizeImageGet($arItem['DETAIL_PICTURE'], array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL, true);
						}

						// Получаем дополнительные изображения
						$moreImages = [];
						if ($arItem['HAS_ADDITIONAL_IMAGES'] && !empty($arItem['MORE_PHOTOS'])) {
							foreach ($arItem['MORE_PHOTOS'] as $photoId) {
								if ($photoId && intval($photoId) > 0) {
									$resizedImage = CFile::ResizeImageGet($photoId, array('width' => 400, 'height' => 400), BX_RESIZE_IMAGE_PROPORTIONAL, true);
									if ($resizedImage && $resizedImage['src']) {
										$moreImages[] = $resizedImage;
									}
								}
							}
						}

						$hasAdditionalImages = count($moreImages) > 0;
						?>

                        <article class="edsys-product-card" id="<?= $this->GetEditAreaId($arItem['ID']) ?>"
                                 data-product-id="<?= $arItem['ID'] ?>"
                                 data-has-additional="<?= $hasAdditionalImages ? 'true' : 'false' ?>"
                                 data-images-count="<?= $hasAdditionalImages ? (count($moreImages) + 1) : 1 ?>">

                            <!-- Изображение товара с галереей -->
                            <div class="edsys-product-card__image-wrapper">
                                <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="edsys-product-card__image-link">
									<?php if ($arImage): ?>
                                        <img
                                                src="<?= $arImage['src'] ?>"
                                                alt="<?= htmlspecialchars($arItem['NAME']) ?>"
                                                class="edsys-product-card__image edsys-product-card__image--main"
                                                width="400"
                                                height="400"
                                                loading="lazy"
                                                data-image-index="0"
                                        >

										<?php if ($hasAdditionalImages): ?>
											<?php foreach ($moreImages as $index => $moreImage): ?>
                                                <img
                                                        src="<?= $moreImage['src'] ?>"
                                                        alt="<?= htmlspecialchars($arItem['NAME']) ?> - изображение <?= $index + 2 ?>"
                                                        class="edsys-product-card__image edsys-product-card__image--additional"
                                                        width="400"
                                                        height="400"
                                                        loading="lazy"
                                                        data-image-index="<?= $index + 1 ?>"
                                                >
											<?php endforeach; ?>
										<?php endif; ?>
									<?php else: ?>
                                        <div class="edsys-product-card__no-image">
                                            <i class="ph ph-thin ph-image"></i>
                                            <span>Нет изображения</span>
                                        </div>
									<?php endif; ?>
                                </a>

                                <!-- Навигация по изображениям -->
								<?php if ($hasAdditionalImages): ?>
                                    <div class="edsys-product-card__image-nav">
                                        <div class="edsys-image-indicators">
                                            <button class="edsys-image-indicator edsys-image-indicator--active"
                                                    data-image-index="0"
                                                    title="Основное изображение"></button>
											<?php foreach ($moreImages as $index => $moreImage): ?>
                                                <button class="edsys-image-indicator"
                                                        data-image-index="<?= $index + 1 ?>"
                                                        title="Изображение <?= $index + 2 ?>"></button>
											<?php endforeach; ?>
                                        </div>
                                    </div>
								<?php endif; ?>

                                <!-- Быстрые действия -->
                                <div class="edsys-product-card__quick-actions">
                                    <button
                                            type="button"
                                            class="edsys-quick-action edsys-quick-action--favorite favorite-toggle-btn <?= $arItem['IS_FAVORITE'] ? 'active' : '' ?>"
                                            title="Добавить в избранное"
                                            data-product-id="<?= $arItem['ID'] ?>"
                                            data-action="add-to-favorites"
                                    >
                                        <i class="ph ph-thin ph-heart"></i>
                                    </button>
                                   <button 
    type="button" 
    class="edsys-quick-action edsys-quick-action--compare" 
    data-compare-action="toggle"
    data-product-id="<?= $arItem['ID'] ?>"
    title="Добавить к сравнению"
    aria-label="Добавить <?= htmlspecialchars($arItem['NAME']) ?> к сравнению"
    aria-pressed="false"
>
    <i class="ph ph-thin ph-chart-bar" aria-hidden="true"></i>
</button>
                                </div>
                            </div>

                            <!-- Информация о товаре -->
                            <div class="edsys-product-card__content">
                                <!-- Артикул -->
								<?php if (!empty($arItem['ARTICLE'])): ?>
                                    <div class="edsys-product-card__article">
                                        Арт. <?= htmlspecialchars($arItem['ARTICLE']) ?>
                                    </div>
								<?php endif; ?>

                                <!-- Название -->
                                <h3 class="edsys-product-card__title">
                                    <a href="<?= $arItem['DETAIL_PAGE_URL'] ?>" class="edsys-product-card__title-link">
										<?= $arItem['NAME'] ?>
                                    </a>
                                </h3>

                                <!-- Краткое описание -->
								<?php if (!empty($arItem['PREVIEW_TEXT'])): ?>
                                    <div class="edsys-product-card__description">
										<?= strip_tags($arItem['PREVIEW_TEXT']) ?>
                                    </div>
								<?php endif; ?>

                                <!-- Цены и наличие для авторизованных -->
								<?php if ($isAuthorized): ?>
                                    <div class="edsys-product-card__pricing">
										<?php if (!empty($arPrice) && $arPrice['VALUE'] > 0): ?>
                                            <div class="edsys-product-card__prices">
                                                <div class="edsys-product-card__price edsys-product-card__price--retail">
                                                    <span class="edsys-product-card__price-label">розн.</span>
                                                    <span class="edsys-product-card__price-value"><?= $arPrice['PRINT_VALUE'] ?></span>
                                                </div>
                                                <div class="edsys-product-card__price edsys-product-card__price--personal">
                                                    <span class="edsys-product-card__price-label">ваша цена</span>
                                                    <span class="edsys-product-card__price-value"><?= $arPrice['PRINT_DISCOUNT_VALUE'] ?: $arPrice['PRINT_VALUE'] ?></span>
                                                </div>
                                            </div>
										<?php endif; ?>

                                        <!-- Наличие -->
                                        <div class="edsys-product-card__availability">
											<?php if (!empty($arItem['CATALOG_QUANTITY']) && $arItem['CATALOG_QUANTITY'] > 0): ?>
                                                <span class="edsys-availability edsys-availability--in-stock">
                                                <i class="ph ph-thin ph-check-circle"></i>
                                                В наличии
                                            </span>
											<?php else: ?>
                                                <span class="edsys-availability edsys-availability--preorder">
                                                <i class="ph ph-thin ph-clock"></i>
                                                Под заказ
                                            </span>
											<?php endif; ?>
                                        </div>
                                    </div>
								<?php else: ?>
                                    <!-- Сообщение для неавторизованных -->
                                    <div class="edsys-product-card__auth-notice">
                                        <a href="/login/" class="edsys-auth-link">
                                            Войдите в аккаунт для просмотра цен
                                        </a>
                                    </div>
								<?php endif; ?>
                            </div>
                        </article>
					<?php endforeach; ?>
                </div>

                <!-- Пагинация -->
				<?php if ($totalPages > 1): ?>
                    <nav class="edsys-pagination" aria-label="Навигация по страницам">
                        <ul class="edsys-pagination__list">
							<?php if ($currentPage > 1): ?>
                                <li class="edsys-pagination__item">
                                    <a href="?page=<?= $currentPage - 1 ?>" class="edsys-pagination__link edsys-pagination__link--prev">
                                        <i class="ph ph-thin ph-caret-left"></i>
                                    </a>
                                </li>
							<?php endif; ?>

							<?php
							$startPage = max(1, $currentPage - 2);
							$endPage = min($totalPages, $currentPage + 2);
							?>

							<?php if ($startPage > 1): ?>
                                <li class="edsys-pagination__item">
                                    <a href="?page=1" class="edsys-pagination__link">1</a>
                                </li>
								<?php if ($startPage > 2): ?>
                                    <li class="edsys-pagination__item edsys-pagination__ellipsis">...</li>
								<?php endif; ?>
							<?php endif; ?>

							<?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                                <li class="edsys-pagination__item">
									<?php if ($i == $currentPage): ?>
                                        <span class="edsys-pagination__link edsys-pagination__link--current"><?= $i ?></span>
									<?php else: ?>
                                        <a href="?page=<?= $i ?>" class="edsys-pagination__link"><?= $i ?></a>
									<?php endif; ?>
                                </li>
							<?php endfor; ?>

							<?php if ($endPage < $totalPages): ?>
								<?php if ($endPage < $totalPages - 1): ?>
                                    <li class="edsys-pagination__item edsys-pagination__ellipsis">...</li>
								<?php endif; ?>
                                <li class="edsys-pagination__item">
                                    <a href="?page=<?= $totalPages ?>" class="edsys-pagination__link"><?= $totalPages ?></a>
                                </li>
							<?php endif; ?>

							<?php if ($currentPage < $totalPages): ?>
                                <li class="edsys-pagination__item">
                                    <a href="?page=<?= $currentPage + 1 ?>" class="edsys-pagination__link edsys-pagination__link--next">
                                        <i class="ph ph-thin ph-caret-right"></i>
                                    </a>
                                </li>
							<?php endif; ?>
                        </ul>
                    </nav>
				<?php endif; ?>
            </main>
        </div>
    </div>

    <!-- Overlay для мобильных фильтров -->
    <div class="edsys-filters-overlay" id="filters-overlay"></div>

    <!-- Скрытые данные для JS -->
    <script type="application/json" id="catalog-config">
        {
			"sectionId": "<?= $currentSectionId ?>",
    "iblockId": "<?= $arParams['IBLOCK_ID'] ?>",
    "itemsPerPage": <?= $itemsPerPage ?>,
    "currentPage": <?= $currentPage ?>,
    "totalPages": <?= $totalPages ?>,
    "totalItems": <?= $totalItems ?>,
    "isAuthorized": <?= $isAuthorized ? 'true' : 'false' ?>
        }
    </script>

    <!-- Floating Compare Button -->
    <div class="edsys-compare-floating" id="compare-floating-button">
        <a href="/compare/" class="edsys-compare-floating__link">
            <i class="ph ph-thin ph-chart-bar edsys-compare-floating__icon"></i>
            <span>Сравнить</span>
            <span class="edsys-compare-floating__count" data-compare-count>0</span>
        </a>
    </div>

<?php
// Подключаем отладочную информацию если нужно
if ($_GET['debug'] == 'Y') {
	include($templateFolder . '/debug.php');
}
}
?>