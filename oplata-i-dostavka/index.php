<?php
/**
 * Payment and Delivery Page - Оплата и доставка
 * Version: 1.0.0
 * Date: 2025-07-16
 * Description: Страница с информацией об оплате и доставке EDS
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Оплата и доставка - Electric Distribution Systems");
$APPLICATION->SetPageProperty("description", "Информация об оплате и доставке оборудования EDS. Безналичный расчет, доставка по России и СНГ, самовывоз из Калуги. Быстро и надежно.");
$APPLICATION->SetPageProperty("keywords", "оплата EDS, доставка электрооборудования, безналичный расчет, транспортные компании, самовывоз Калуга");

// Structured data for payment and delivery info
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "Organization",
	"name" => "Electric Distribution Systems",
	"url" => "https://edsy.ru",
	"address" => [
		"@type" => "PostalAddress",
		"streetAddress" => "Грабцевский пр., 17А",
		"addressLocality" => "Калуга",
		"addressCountry" => "RU"
	],
	"telephone" => "+7-4842-21-96-97",
	"email" => "sales@edsy.ru",
	"openingHours" => "Mo-Fr 09:00-19:00",
	"paymentAccepted" => "Безналичный расчет",
	"areaServed" => "Россия, СНГ"
];

$APPLICATION->AddHeadString('<script type="application/ld+json">'.json_encode($structuredData).'</script>');
?>

    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/oplata-i-dostavka/style.css">
    </head>
    <body>
    <div class="edsys-payment-delivery">
        <div class="edsys-payment-delivery__container">
            <!-- Header Section -->
            <header class="edsys-payment-delivery__header">
                <h1 class="edsys-payment-delivery__title">Оплата и доставка</h1>
                <p class="edsys-payment-delivery__description">
                    Мы предлагаем удобные способы оплаты и быструю доставку по всей России и СНГ.
                    Работаем с юридическими лицами по безналичному расчету.
                </p>
            </header>

            <!-- Process Steps -->
            <section class="edsys-process-steps">
                <div class="edsys-process-steps__container">
                    <!-- Step 1: Order -->
                    <div class="edsys-process-step">
                        <div class="edsys-process-step__icon">
                            <i class="ph ph-thin ph-shopping-cart"></i>
                        </div>
                        <h2 class="edsys-process-step__title">Оформление заказа</h2>
                    </div>

                    <!-- Step 2: Payment -->
                    <div class="edsys-process-step">
                        <div class="edsys-process-step__icon">
                            <i class="ph ph-thin ph-check-circle"></i>
                        </div>
                        <h2 class="edsys-process-step__title">Выставление счета и оплата</h2>
                    </div>

                    <!-- Step 3: Delivery -->
                    <div class="edsys-process-step">
                        <div class="edsys-process-step__icon">
                            <i class="ph ph-thin ph-airplane-takeoff"></i>
                        </div>
                        <h2 class="edsys-process-step__title">Доставка</h2>
                    </div>

                    <!-- Step 4: Pickup -->
                    <div class="edsys-process-step">
                        <div class="edsys-process-step__icon">
                            <i class="ph ph-thin ph-warehouse"></i>
                        </div>
                        <h2 class="edsys-process-step__title">Самовывоз</h2>
                    </div>
                </div>
            </section>

            <!-- Detailed Information -->
            <section class="edsys-info-cards">
                <div class="edsys-info-cards__container">
                    <!-- Order Card -->
                    <article class="edsys-info-card">
                        <div class="edsys-info-card__icon">
                            <i class="ph ph-thin ph-shopping-bag"></i>
                        </div>
                        <div class="edsys-info-card__content">
                            <h3 class="edsys-info-card__title">Как оформить заказ</h3>
                            <div class="edsys-info-card__text">
                                <p>Оформить заказ вы можете на сайте, после прохождения регистрации или отправив заявку менеджеру по телефонам:</p>
                                <div class="edsys-contact-info">
                                    <a href="tel:+7-4842-21-96-97" class="edsys-contact-link">
                                        <i class="ph ph-thin ph-phone"></i>
                                        +7 (4842) 21-96-97
                                    </a>
                                    <a href="tel:+7-910-527-73-35" class="edsys-contact-link">
                                        <i class="ph ph-thin ph-phone"></i>
                                        +7 (910) 527-73-35
                                    </a>
                                    <a href="mailto:sales@edsy.ru" class="edsys-contact-link">
                                        <i class="ph ph-thin ph-envelope"></i>
                                        sales@edsy.ru
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Payment Card -->
                    <article class="edsys-info-card">
                        <div class="edsys-info-card__icon">
                            <i class="ph ph-thin ph-wallet"></i>
                        </div>
                        <div class="edsys-info-card__content">
                            <h3 class="edsys-info-card__title">Оплата</h3>
                            <div class="edsys-info-card__text">
                                <p>После оформления заказа менеджер направляет Вам счет.</p>
                                <p><strong>Оплата производится безналичным расчетом согласно выставленному счету</strong></p>
                                <div class="edsys-payment-methods">
                                    <div class="edsys-payment-method">
                                        <i class="ph ph-thin ph-bank"></i>
                                        Банковский перевод
                                    </div>
                                    <div class="edsys-payment-method">
                                        <i class="ph ph-thin ph-receipt"></i>
                                        По счету для ЮЛ
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Delivery Card -->
                    <article class="edsys-info-card">
                        <div class="edsys-info-card__icon">
                            <i class="ph ph-thin ph-truck"></i>
                        </div>
                        <div class="edsys-info-card__content">
                            <h3 class="edsys-info-card__title">Доставка</h3>
                            <div class="edsys-info-card__text">
                                <div class="edsys-delivery-option">
                                    <h4 class="edsys-delivery-option__title">
                                        <i class="ph ph-thin ph-package"></i>
                                        Доставка ТК
                                    </h4>
                                    <p>Доставка осуществляется с помощью ТК:</p>
                                    <ul class="edsys-delivery-list">
                                        <li>Деловые линии</li>
                                        <li>СДЕК</li>
                                    </ul>
                                    <p>в любую точку России или СНГ</p>
                                </div>

                                <div class="edsys-delivery-option">
                                    <h4 class="edsys-delivery-option__title">
                                        <i class="ph ph-thin ph-lightning"></i>
                                        Срочная доставка
                                    </h4>
                                    <p>Доставка курьером в радиусе 500км от Калуги</p>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Pickup Card -->
                    <article class="edsys-info-card">
                        <div class="edsys-info-card__icon">
                            <i class="ph ph-thin ph-buildings"></i>
                        </div>
                        <div class="edsys-info-card__content">
                            <h3 class="edsys-info-card__title">Самовывоз</h3>
                            <div class="edsys-info-card__text">
                                <p>Вы можете забрать Ваш заказ самостоятельно со склада по адресу:</p>
                                <div class="edsys-pickup-info">
                                    <a href="https://yandex.ru/maps/org/eds/99890858437"
                                       class="edsys-pickup-address"
                                       target="_blank"
                                       rel="noopener noreferrer"
                                       itemprop="address">
                                        <i class="ph ph-thin ph-map-pin"></i>
                                        г. Калуга, Грабцевский пр., 17А
                                    </a>
                                    <div class="edsys-pickup-hours">
                                        <i class="ph ph-thin ph-clock"></i>
                                        <div>
                                            <strong>Время работы склада:</strong><br>
                                            Пн-Пт. с 9:00 до 19:00
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>
                </div>
            </section>

            <!-- Additional Information -->
            <section class="edsys-additional-info">
                <div class="edsys-additional-info__container">
                    <div class="edsys-additional-info__content">
                        <h3 class="edsys-additional-info__title">Дополнительная информация</h3>
                        <div class="edsys-additional-info__grid">
                            <div class="edsys-additional-info__item">
                                <i class="ph ph-thin ph-shield-check"></i>
                                <div>
                                    <strong>Гарантия качества</strong>
                                    <p>Все оборудование сертифицировано и имеет гарантию</p>
                                </div>
                            </div>
                            <div class="edsys-additional-info__item">
                                <i class="ph ph-thin ph-clock-countdown"></i>
                                <div>
                                    <strong>Быстрая обработка</strong>
                                    <p>Заказы обрабатываются в день поступления</p>
                                </div>
                            </div>
                            <div class="edsys-additional-info__item">
                                <i class="ph ph-thin ph-headset"></i>
                                <div>
                                    <strong>Техническая поддержка</strong>
                                    <p>Консультации по выбору и использованию оборудования</p>
                                </div>
                            </div>
                            <div class="edsys-additional-info__item">
                                <i class="ph ph-thin ph-package"></i>
                                <div>
                                    <strong>Надежная упаковка</strong>
                                    <p>Антивандальная упаковка для безопасной доставки</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- CTA Section -->
            <section class="edsys-cta-section">
                <div class="edsys-cta-section__content">
                    <h3 class="edsys-cta-section__title">Готовы сделать заказ?</h3>
                    <p class="edsys-cta-section__text">
                        Свяжитесь с нами любым удобным способом или оформите заказ на сайте
                    </p>
                    <div class="edsys-cta-section__buttons">
                        <a href="/catalog/" class="edsys-cta-button edsys-cta-button--primary">
                            <i class="ph ph-thin ph-shopping-cart"></i>
                            Перейти в каталог
                        </a>
                        <a href="/contacty/" class="edsys-cta-button edsys-cta-button--secondary">
                            <i class="ph ph-thin ph-phone"></i>
                            Связаться с нами
                        </a>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="/oplata-i-dostavka/script.js"></script>
    </body>
    </html>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>