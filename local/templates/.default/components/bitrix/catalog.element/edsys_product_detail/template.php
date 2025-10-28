<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

use Bitrix\Main\Localization\Loc;

$this->setFrameMode(true);

// --- KW: Start ---
// Read the list of available passports
$passportsFile = $_SERVER['DOCUMENT_ROOT'] . '/passports_available.txt';
$availablePassports = [];
if (file_exists($passportsFile)) {
    $lines = file($passportsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (preg_match("/upload\\/passport\\/(.+?)\\.pdf/", $line, $matches)) {
            $availablePassports[] = $matches[1];
        }
    }
}
$availablePassports = array_unique($availablePassports);
$productSlug = $arResult['CODE'];
$hasPassport = in_array($productSlug, $availablePassports);
// --- KW: End ---

// Подключение языковых констант
Loc::loadMessages(__FILE__);

// Получение данных товара из result_modifier
$arItem = $arResult;

// Проверка авторизации
$isAuthorized = $USER->IsAuthorized();

// Получение обработанных данных
$productImages = $arResult['IMAGES'] ?? [];
$hasVideo = !empty($arResult['VIDEO']);
$videoEmbedCode = $arResult['VIDEO']['EMBED_CODE'] ?? '';
$relatedProducts = $arResult['RELATED_PRODUCTS'] ?? [];
$pricesData = $arResult['PRICES_PROCESSED'] ?? [];
$stockStatus = $arResult['STOCK_STATUS'] ?? ['AVAILABLE' => true, 'TEXT' => 'В наличии'];
$breadcrumbs = $arResult['BREADCRUMBS'] ?? [];
$specifications = $arResult['SPECIFICATIONS'] ?? [];
$documents = $arResult['DOCUMENTS'] ?? [];
?>

<article class="edsys-product" itemscope itemtype="http://schema.org/Product">
    <!-- Хлебные крошки -->
    <nav class="edsys-breadcrumb" aria-label="<?= Loc::getMessage('EDSYS_BREADCRUMB_LABEL') ?>">
        <ol class="edsys-breadcrumb__list">
			<?php foreach ($breadcrumbs as $breadcrumb): ?>
                <li class="edsys-breadcrumb__item <?= !empty($breadcrumb['CURRENT']) ? 'edsys-breadcrumb__item--current' : '' ?>" <?= !empty($breadcrumb['CURRENT']) ? 'aria-current="page"' : '' ?>>
					<?php if (!empty($breadcrumb['LINK']) && empty($breadcrumb['CURRENT'])): ?>
                        <a href="<?= htmlspecialchars($breadcrumb['LINK']) ?>" class="edsys-breadcrumb__link"><?= htmlspecialchars($breadcrumb['TITLE']) ?></a>
					<?php else: ?>
						<?= htmlspecialchars($breadcrumb['TITLE']) ?>
					<?php endif; ?>
                </li>
			<?php endforeach; ?>
        </ol>
    </nav>

    <!-- Заголовок товара -->
    <header class="edsys-product__header">
        <h1 class="edsys-product__title" itemprop="name"><?= htmlspecialchars($arResult['NAME']) ?></h1>
    </header>

    <!-- Основной контент товара -->
    <div class="edsys-product__main">
        <!-- Галерея изображений -->
        <section class="edsys-product__gallery">
            <div class="edsys-gallery">
				<?php if (!empty($productImages)): ?>
                    <div class="edsys-gallery__main">
                        <img
                                src="<?= htmlspecialchars($productImages[0]['SRC']) ?>"
                                alt="<?= htmlspecialchars($productImages[0]['ALT']) ?>"
                                class="edsys-gallery__image edsys-gallery__image--active"
                                width="400"
                                height="300"
                                loading="eager"
                                itemprop="image"
                                id="mainProductImage"
                        >
                        <button
                                class="edsys-gallery__fullscreen"
                                aria-label="Открыть в полноэкранном режиме"
                                type="button"
                        >
                            <i class="ph ph-thin ph-arrows-out" aria-hidden="true"></i>
                        </button>
                    </div>

					<?php if (count($productImages) > 1): ?>
                        <div class="edsys-gallery__thumbnails">
							<?php foreach ($productImages as $index => $image): ?>
                                <button
                                        class="edsys-gallery__thumbnail <?= $index === 0 ? 'edsys-gallery__thumbnail--active' : '' ?>"
                                        data-image="<?= htmlspecialchars($image['SRC']) ?>"
                                        data-index="<?= $index ?>"
                                        type="button"
                                        aria-label="Просмотреть изображение <?= $index + 1 ?>"
                                >
                                    <img
                                            src="<?= htmlspecialchars($image['SRC']) ?>"
                                            alt="<?= htmlspecialchars($image['ALT']) ?>"
                                            width="80"
                                            height="60"
                                            loading="lazy"
                                    >
                                </button>
							<?php endforeach; ?>
                        </div>
					<?php endif; ?>
				<?php else: ?>
                    <div class="edsys-gallery__main">
                        <img
                                src="/local/templates/main/images/no-image.jpg"
                                alt="<?= htmlspecialchars($arResult['NAME']) ?>"
                                class="edsys-gallery__image"
                                width="400"
                                height="300"
                                loading="eager"
                                id="mainProductImage"
                        >
                    </div>
				<?php endif; ?>
            </div>
        </section>

        <!-- Характеристики товара -->
        <section class="edsys-product__specs">

            <div class="edsys-specs" itemprop="description">
				                <?php
								// Check if DETAIL_TEXT is available and not empty
								if (!empty($arResult['DETAIL_TEXT'])) {
				                    // --- KW: Start ---
				                    // Remove the old button from the description
				                    $detailText = $arResult['DETAIL_TEXT'];
				                    $detailText = preg_replace('/<div class="baton">.*?<\/div>/s', '', $detailText);
				                    echo $detailText;
				                    // --- KW: End ---
								} else {
									// Optional: Provide a fallback message if the description is missing.
									echo '<p>Подробное описание товара готовится к публикации.</p>';
								}
								?>
                <div class="edsys-specs__actions">
                    <button
                            class="edsys-button edsys-button--secondary edsys-specs__copy"
                            type="button"
                            id="copySpecsButton"
                    >
                        <i class="ph ph-thin ph-copy" aria-hidden="true"></i>
                        Копировать характеристики
                    </button>

					                    <?php if ($hasPassport): ?>
                        <div class="edsys-specs__download">
                            <a
                                    href="/upload/passport/<?= htmlspecialchars($productSlug) ?>.pdf"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    class="edsys-button edsys-button--outline"
                            >
                                <i class="ph ph-thin ph-file-pdf" aria-hidden="true"></i>
                                Паспорт устройства
                            </a>
                        </div>
                    <?php endif; ?>

                    <!-- Поле для добавления видео -->
					<?php if (!empty($arResult['PROPERTIES']['VIDEO_URL']['VALUE'])): ?>
                        <div class="edsys-specs__video-link">
                            <strong>Видеообзор:</strong>
                            <a href="<?= htmlspecialchars($arResult['PROPERTIES']['VIDEO_URL']['VALUE']) ?>" target="_blank" rel="noopener noreferrer">
                                Смотреть видео
                            </a>
                            <small style="display: block; margin-top: 5px; color: #777;">
                                Добавьте ссылку на видео в свойство VIDEO_URL товара
                            </small>
                        </div>
					<?php endif; ?>
                </div>
            </div>
        </section>

        <!-- Блок покупки -->
        <aside class="edsys-product__purchase">
            <div class="edsys-purchase" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
                <!-- Артикул и категория в блоке покупки -->
                <div class="edsys-purchase__article">
                    <span class="edsys-purchase__article-label">Артикул:</span>
                    <span class="edsys-purchase__article-value" itemprop="sku"><?= htmlspecialchars($arResult['SCHEMA']['SKU']) ?></span>
                </div>

				<?php if (!empty($arResult['SCHEMA']['CATEGORY'])): ?>
                    <div class="edsys-purchase__category">
                        <span class="edsys-purchase__category-label">Категория:</span>
                        <a href="<?= $arResult['SECTION_URL'] ?: '/catalog/' ?>" class="edsys-purchase__category-link" itemprop="category">
							<?= htmlspecialchars($arResult['SCHEMA']['CATEGORY']) ?>
                        </a>
                    </div>
				<?php endif; ?>

                <!-- Наличие товара - показываем только авторизованным пользователям -->
				<?php if ($isAuthorized): ?>
                    <div class="edsys-purchase__availability">
                        <span class="edsys-purchase__stock <?= $stockStatus['AVAILABLE'] ? 'edsys-purchase__stock--available' : 'edsys-purchase__stock--preorder' ?>" itemprop="availability" content="http://schema.org/<?= $stockStatus['AVAILABLE'] ? 'InStock' : 'PreOrder' ?>">
                            <i class="ph ph-thin ph-<?= $stockStatus['AVAILABLE'] ? 'check-circle' : 'clock' ?>" aria-hidden="true"></i>
                            <?= htmlspecialchars($stockStatus['TEXT']) ?>
                        </span>
                    </div>
				<?php endif; ?>

				<?php if ($isAuthorized): ?>
                    <!-- Блок для авторизованных пользователей -->
					<?php if (!empty($pricesData)): ?>
                        <div class="edsys-purchase__prices">
							<?php if (!empty($pricesData['RETAIL']['VALUE'])): ?>
                                <div class="edsys-purchase__price edsys-purchase__price--retail">
                                    <span class="edsys-purchase__price-label">Розничная цена:</span>
                                    <span class="edsys-purchase__price-value"><?= $pricesData['RETAIL']['FORMATTED'] ?></span>
                                </div>
							<?php endif; ?>

							<?php if (!empty($pricesData['USER']['VALUE'])): ?>
                                <div class="edsys-purchase__price edsys-purchase__price--user">
                                    <span class="edsys-purchase__price-label">Ваша цена:</span>
                                    <span class="edsys-purchase__price-value" itemprop="price"><?= $pricesData['USER']['FORMATTED'] ?></span>
                                    <meta itemprop="priceCurrency" content="RUB">
                                </div>
							<?php endif; ?>
                        </div>
					<?php endif; ?>

                    <div class="edsys-purchase__controls">
                        <div class="edsys-quantity">
                            <div class="edsys-quantity__control">
                                <button type="button" class="edsys-quantity__btn edsys-quantity__btn--minus" aria-label="Уменьшить количество">
                                    <i class="ph ph-thin ph-minus" aria-hidden="true"></i>
                                </button>
                                <input
                                        type="number"
                                        id="productQuantity"
                                        class="edsys-quantity__input"
                                        value="1"
                                        min="1"
                                        max="999"
                                >
                                <button type="button" class="edsys-quantity__btn edsys-quantity__btn--plus" aria-label="Увеличить количество">
                                    <i class="ph ph-thin ph-plus" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>

                        <div class="edsys-purchase__actions">
                            <button type="button" class="edsys-button edsys-button--primary edsys-purchase__cart">
                                <i class="ph ph-thin ph-shopping-cart" aria-hidden="true"></i>
                                Добавить в корзину
                            </button>

                            <div class="edsys-purchase__secondary">
                                <button type="button" class="edsys-button edsys-button--icon" aria-label="Добавить в избранное" title="Добавить в избранное">
                                    <i class="ph ph-thin ph-heart" aria-hidden="true"></i>
                                </button>
                                
                                <button 
                                    type="button" 
                                    class="edsys-button edsys-button--icon" 
                                    data-compare-action="toggle"
                                    data-product-id="<?= $arResult['ID'] ?>"
                                    title="Добавить к сравнению"
                                    aria-label="Добавить <?= htmlspecialchars($arResult['NAME']) ?> к сравнению"
                                >
                                    <i class="ph ph-thin ph-chart-bar" aria-hidden="true"></i>
                                </button>
                            </div>
                        </div>
                    </div>
				<?php else: ?>
                    <!-- Блок для неавторизованных пользователей -->
                    <div class="edsys-purchase__auth-required">
                        <div class="edsys-purchase__auth-message">
                            <i class="ph ph-thin ph-lock" aria-hidden="true"></i>
                            <p>
                                <a href="/auth/">Войдите</a> или <a href="/register/">зарегистрируйтесь</a>,
                                чтобы увидеть цены с Вашей персональной скидкой
                            </p>
                        </div>
                    </div>
				<?php endif; ?>
            </div>
        </aside>
    </div>

    <!-- Видео обзор -->
	<?php if ($hasVideo): ?>
        <section class="edsys-product__video">
            <div class="edsys-video">
                <div class="edsys-video__container">
                    <h2 class="edsys-video__title">Видеообзор товара</h2>
                    <div class="edsys-video__embed">
						<?= $videoEmbedCode ?>
                    </div>
                </div>
            </div>
        </section>
	<?php endif; ?>

    <!-- Похожие товары -->
    <section class="edsys-product__related">
        <div class="edsys-related">
            <div class="edsys-related__header">
                <h2 class="edsys-related__title">Вам также понравится</h2>
                <div class="edsys-related__navigation">
                    <button type="button" class="edsys-related__nav edsys-related__nav--prev" aria-label="Предыдущие товары">
                        <i class="ph ph-thin ph-caret-left" aria-hidden="true"></i>
                    </button>
                    <button type="button" class="edsys-related__nav edsys-related__nav--next" aria-label="Следующие товары">
                        <i class="ph ph-thin ph-caret-right" aria-hidden="true"></i>
                    </button>
                </div>
            </div>

            <div class="edsys-related__slider">
                <div class="edsys-related__track">
					<?php if (!empty($relatedProducts) && count($relatedProducts) > 0): ?>
						<?php foreach ($relatedProducts as $product): ?>
                            <article class="edsys-product-card">
                                <a href="<?= htmlspecialchars($product['URL']) ?>" class="edsys-product-card__link">
                                    <div class="edsys-product-card__image">
                                        <img
                                                src="<?= $product['IMAGE'] ?: '/local/templates/main/images/no-image.jpg' ?>"
                                                alt="<?= htmlspecialchars($product['NAME']) ?>"
                                                width="200"
                                                height="150"
                                                loading="lazy"
                                        >
                                    </div>
                                    <div class="edsys-product-card__content">
                                        <div class="edsys-product-card__article">АРТ. <?= htmlspecialchars($product['ARTICLE']) ?></div>
                                        <h3 class="edsys-product-card__title"><?= htmlspecialchars($product['NAME']) ?></h3>
										<?php if (!empty($product['DESCRIPTION'])): ?>
                                            <div class="edsys-product-card__description">
												<?php
												// Дополнительная обработка HTML если он попал в описание
												$description = $product['DESCRIPTION'];

												// Если в описании есть HTML теги класса, убираем их
												$description = preg_replace('/<(\w+)[^>]*class="[^"]*"[^>]*>/', '<$1>', $description);

												// Убираем span теги полностью, но оставляем содержимое
												$description = preg_replace('/<\/?span[^>]*>/', '', $description);

												// Разрешаем только базовые теги
												$description = strip_tags($description, '<br><strong><em><b><i>');

												echo $description;
												?>
                                            </div>
										<?php endif; ?>
                                    </div>
                                </a>
                                <div class="edsys-product-card__footer">
                                    <button class="edsys-product-card__order">Под заказ</button>
                                </div>
                            </article>
						<?php endforeach; ?>
					<?php else: ?>
                        <!-- Заглушка если нет товаров - всегда показываем для демонстрации -->
                        <article class="edsys-product-card">
                            <a href="/product/etc-402-9-1-pct/" class="edsys-product-card__link">
                                <div class="edsys-product-card__image">
                                    <img
                                            src="/upload/iblock/etc-402-9-1-pct.jpg"
                                            alt="ETC 402-9.1 РСТ"
                                            width="200"
                                            height="150"
                                            loading="lazy"
                                    >
                                </div>
                                <div class="edsys-product-card__content">
                                    <div class="edsys-product-card__article">АРТ. 1600.1</div>
                                    <h3 class="edsys-product-card__title">ETC 402-9.1 РСТ</h3>
                                    <div class="edsys-product-card__description">
                                        <strong>IN:</strong> 1 метровый кабель H07RN-F 5G6 и вилка PCE CEE 32A 5-pol <strong>OUT:</strong> панельная розетка PCE CEE 32A 5-pol IP44, 9 x PowerCon True1 Truecon (female)
                                    </div>
                                </div>
                            </a>
                            <div class="edsys-product-card__footer">
                                <button class="edsys-product-card__order">Под заказ</button>
                            </div>
                        </article>

                        <article class="edsys-product-card">
                            <a href="/product/t-1-3pct/" class="edsys-product-card__link">
                                <div class="edsys-product-card__image">
                                    <img src="/upload/iblock/t-1-3pct.jpg" alt="T 1-3РСТ" width="200" height="150" loading="lazy">
                                </div>
                                <div class="edsys-product-card__content">
                                    <div class="edsys-product-card__article">АРТ. 431</div>
                                    <h3 class="edsys-product-card__title">T 1-3РСТ</h3>
                                    <div class="edsys-product-card__description">
                                        <strong>IN:</strong> панельный разъем powerCON 16 A, тип-А Seetronic SAC3MPX(05) <strong>OUT:</strong> панельный разъем 3хPowerCON 16 A, тип-В Seetronic SAC3FPX(05)
                                    </div>
                                </div>
                            </a>
                            <div class="edsys-product-card__footer">
                                <button class="edsys-product-card__order">Под заказ</button>
                            </div>
                        </article>

                        <article class="edsys-product-card">
                            <a href="/product/etc-402-9-pct/" class="edsys-product-card__link">
                                <div class="edsys-product-card__image">
                                    <img src="/upload/iblock/etc-402-9-pct.jpg" alt="ETC 402-9 РСТ" width="200" height="150" loading="lazy">
                                </div>
                                <div class="edsys-product-card__content">
                                    <div class="edsys-product-card__article">АРТ. 1600</div>
                                    <h3 class="edsys-product-card__title">ETC 402-9 РСТ</h3>
                                    <div class="edsys-product-card__description">
                                        <strong>IN:</strong> 1 метровый кабель H07RN-F 5G6 и вилка PCE CEE 32A 5-pol <strong>OUT:</strong> панельная розетка PCE CEE 32A 5-pol IP44, 9 x PowerCon True1 Truecon (female)
                                    </div>
                                </div>
                            </a>
                            <div class="edsys-product-card__footer">
                                <button class="edsys-product-card__order">Под заказ</button>
                            </div>
                        </article>

                        <article class="edsys-product-card">
                            <a href="/product/et-2-5-pct/" class="edsys-product-card__link">
                                <div class="edsys-product-card__image">
                                    <img src="/upload/iblock/et-2-5-pct.jpg" alt="ET 2-5 РСТ" width="200" height="150" loading="lazy">
                                </div>
                                <div class="edsys-product-card__content">
                                    <div class="edsys-product-card__article">АРТ. 195.1</div>
                                    <h3 class="edsys-product-card__title">ET 2-5 РСТ</h3>
                                    <div class="edsys-product-card__description">
                                        <strong>IN:</strong> панельный разъем Seetronic PowerCon True1 16A (male) <strong>OUT:</strong> панельный разъем Seetronic PowerCon True1 16A (female); 5xPCE Schuko 16A
                                    </div>
                                </div>
                            </a>
                            <div class="edsys-product-card__footer">
                                <button class="edsys-product-card__order">Под заказ</button>
                            </div>
                        </article>

                        <article class="edsys-product-card">
                            <a href="/product/t-1-3pc/" class="edsys-product-card__link">
                                <div class="edsys-product-card__image">
                                    <img src="/upload/iblock/t-1-3pc.jpg" alt="T 1-3PC" width="200" height="150" loading="lazy">
                                </div>
                                <div class="edsys-product-card__content">
                                    <div class="edsys-product-card__article">АРТ. 430</div>
                                    <h3 class="edsys-product-card__title">T 1-3PC</h3>
                                    <div class="edsys-product-card__description">
                                        <strong>IN:</strong> панельный разъем 1xPowerCon 20A/250B (male) <strong>OUT:</strong> панельный разъем SelfPowerCon 20A/250B (female)
                                    </div>
                                </div>
                            </a>
                            <div class="edsys-product-card__footer">
                                <button class="edsys-product-card__order">Под заказ</button>
                            </div>
                        </article>
					<?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Полноэкранная галерея -->
    <div class="edsys-fullscreen-gallery" id="fullscreenGallery" aria-hidden="true">
        <div class="edsys-fullscreen-gallery__overlay">
            <div class="edsys-fullscreen-gallery__container">
                <button
                        type="button"
                        class="edsys-fullscreen-gallery__close"
                        aria-label="Закрыть галерею"
                >
                    <i class="ph ph-thin ph-x" aria-hidden="true"></i>
                </button>

                <button
                        type="button"
                        class="edsys-fullscreen-gallery__nav edsys-fullscreen-gallery__nav--prev"
                        aria-label="Предыдущее изображение"
                >
                    <i class="ph ph-thin ph-caret-left" aria-hidden="true"></i>
                </button>

                <div class="edsys-fullscreen-gallery__image-container">
                    <img
                            src=""
                            alt=""
                            class="edsys-fullscreen-gallery__image"
                            id="fullscreenImage"
                    >
                </div>

                <button
                        type="button"
                        class="edsys-fullscreen-gallery__nav edsys-fullscreen-gallery__nav--next"
                        aria-label="Следующее изображение"
                >
                    <i class="ph ph-thin ph-caret-right" aria-hidden="true"></i>
                </button>

                <div class="edsys-fullscreen-gallery__counter">
                    <span id="currentImageIndex">1</span> / <span id="totalImages"><?= count($productImages) ?></span>
                </div>
            </div>
        </div>
    </div>
</article>
<script src="<?= $templateFolder ?>/script.js?v=1.1.0"></script>