<?php
/**
 * Contacts Page - Контакты
 * Version: 1.4.1
 * Date: 2025-07-17
 * Description: Страница контактов EDS с формой обратной связи и актуализированной картой Yandex (убрано поле "Тема")
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("Контакты - Electric Distribution Systems");
$APPLICATION->SetPageProperty("description", "Контакты компании EDS - адрес, телефоны, email, форма обратной связи. Калуга, Грабцевский пр., 17А. Профессиональное электрооборудование.");
$APPLICATION->SetPageProperty("keywords", "контакты EDS, адрес EDS Калуга, телефон EDS, email EDS, обратная связь, электрооборудование контакты");

// Structured data for contact info
$structuredData = [
	"@context" => "https://schema.org",
	"@type" => "Organization",
	"name" => "Electric Distribution Systems",
	"url" => "https://edsy.ru",
	"logo" => "https://edsy.ru/logo.png",
	"address" => [
		"@type" => "PostalAddress",
		"streetAddress" => "Грабцевский пр., 17А",
		"addressLocality" => "Калуга",
		"postalCode" => "248009",
		"addressCountry" => "RU"
	],
	"telephone" => ["+7-4842-20-74-16", "+7-910-527-35-38"],
	"email" => "sales@edsy.ru",
	"openingHours" => "Mo-Fr 09:00-19:00",
	"areaServed" => "Россия, СНГ",
	"sameAs" => [
		"https://vk.com/edsystems",
		"https://www.youtube.com/channel/UCKNUJ_Z_jNGaTm-GRqgED6g",
		"https://wa.me/79105273538"
	]
];

$APPLICATION->AddHeadString('<script type="application/ld+json">'.json_encode($structuredData).'</script>');
?>

    <!DOCTYPE html>
    <html lang="ru">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="/contacty/style.css">
    </head>
    <body>
    <div class="edsys-contacts">
        <div class="edsys-contacts__container">
            <!-- Header Section -->
            <header class="edsys-contacts__header">
                <h1 class="edsys-contacts__title">Контакты</h1>
                <p class="edsys-contacts__description">
                    Свяжитесь с нами любым удобным способом. Мы всегда готовы ответить на ваши вопросы
                    и помочь с выбором профессионального электрооборудования.
                </p>
            </header>

            <!-- Main Content Grid -->
            <div class="edsys-contacts__grid">
                <!-- Contact Information -->
                <section class="edsys-contacts__info">
                    <!-- Address -->
                    <article class="edsys-contact-card">
                        <div class="edsys-contact-card__icon">
                            <i class="ph ph-map-pin"></i>
                        </div>
                        <div class="edsys-contact-card__content">
                            <h3 class="edsys-contact-card__title">Адрес:</h3>
                            <div class="edsys-contact-card__text">
                                <address class="edsys-contact-address" itemprop="address" itemscope itemtype="https://schema.org/PostalAddress">
                                    <span itemprop="postalCode">248009</span>, <span itemprop="addressLocality">г. Калуга</span>, <span itemprop="streetAddress">Грабцевский пр., 17А</span>
                                </address>
                                <a href="https://yandex.ru/maps/org/eds/99890858437"
                                   class="edsys-map-link"
                                   target="_blank"
                                   rel="noopener noreferrer">
                                    <i class="ph ph-thin ph-map-pin"></i>
                                    Показать на карте
                                </a>
                            </div>
                        </div>
                    </article>

                    <!-- Phones -->
                    <article class="edsys-contact-card">
                        <div class="edsys-contact-card__icon">
                            <i class="ph ph-phone"></i>
                        </div>
                        <div class="edsys-contact-card__content">
                            <h3 class="edsys-contact-card__title">Телефоны:</h3>
                            <div class="edsys-contact-card__text">
                                <div class="edsys-contact-phones">
                                    <a href="tel:+74842207416" class="edsys-phone-inline" itemprop="telephone">
                                        <i class="ph ph-thin ph-phone"></i>
                                        +7 (4842) 20-74-16
                                    </a>
                                    <a href="tel:+79105273538" class="edsys-phone-inline" itemprop="telephone">
                                        <i class="ph ph-thin ph-phone"></i>
                                        +7 (910) 527-35-38
                                    </a>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Email - FIXED: Email in one line with department name -->
                    <article class="edsys-contact-card">
                        <div class="edsys-contact-card__icon">
                            <i class="ph ph-envelope"></i>
                        </div>
                        <div class="edsys-contact-card__content">
                            <h3 class="edsys-contact-card__title">Email:</h3>
                            <div class="edsys-contact-card__text">
                                <div class="edsys-email-department">
                                    <i class="ph ph-thin ph-briefcase"></i>
                                    <div class="edsys-email-department-content">
                                        <strong>Отдел продаж:</strong>
                                        <a href="mailto:sales@edsy.ru" class="edsys-email-link" itemprop="email">
                                            sales@edsy.ru
                                        </a>
                                    </div>
                                </div>
                                <div class="edsys-email-department">
                                    <i class="ph ph-thin ph-gear"></i>
                                    <div class="edsys-email-department-content">
                                        <strong>Производственный отдел:</strong>
                                        <a href="mailto:production@edsy.ru" class="edsys-email-link">
                                            production@edsy.ru
                                        </a>
                                    </div>
                                </div>
                                <div class="edsys-email-department">
                                    <i class="ph ph-thin ph-calculator"></i>
                                    <div class="edsys-email-department-content">
                                        <strong>Финансовый отдел:</strong>
                                        <a href="mailto:buhgalter@edsy.ru" class="edsys-email-link">
                                            buhgalter@edsy.ru
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Working Hours -->
                    <article class="edsys-contact-card">
                        <div class="edsys-contact-card__icon">
                            <i class="ph ph-clock"></i>
                        </div>
                        <div class="edsys-contact-card__content">
                            <h3 class="edsys-contact-card__title">Время работы:</h3>
                            <div class="edsys-contact-card__text">
                                <div class="edsys-working-hours">
                                    <div class="edsys-working-day">
                                        <span class="edsys-working-day__days">Понедельник - Пятница:</span>
                                        <span class="edsys-working-day__hours">09:00 - 19:00</span>
                                    </div>
                                    <div class="edsys-working-day">
                                        <span class="edsys-working-day__days">Суббота - Воскресенье:</span>
                                        <span class="edsys-working-day__hours">Выходной</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </article>

                    <!-- Social Networks -->
                    <article class="edsys-contact-card">
                        <div class="edsys-contact-card__icon">
                            <i class="ph ph-share-network"></i>
                        </div>
                        <div class="edsys-contact-card__content">
                            <h3 class="edsys-contact-card__title">Социальные сети:</h3>
                            <div class="edsys-social-icons">
                                <a href="https://wa.me/79105273538"
                                   class="edsys-social-icon edsys-social-icon--whatsapp"
                                   target="_blank"
                                   rel="nofollow noopener"
                                   title="WhatsApp"
                                   aria-label="WhatsApp">
                                    <img src="/images/svg/whatsapp.svg" alt="WhatsApp" width="24" height="24" loading="lazy">
                                </a>
                                <a href="https://vk.com/edsystems"
                                   class="edsys-social-icon edsys-social-icon--vk"
                                   target="_blank"
                                   rel="noopener"
                                   title="ВКонтакте"
                                   aria-label="ВКонтакте">
                                    <img src="/images/svg/vk.svg" alt="ВКонтакте" width="24" height="24" loading="lazy">
                                </a>
                                <a href="https://www.youtube.com/channel/UCKNUJ_Z_jNGaTm-GRqgED6g"
                                   class="edsys-social-icon edsys-social-icon--youtube"
                                   target="_blank"
                                   rel="noopener"
                                   title="YouTube"
                                   aria-label="YouTube">
                                    <img src="/images/svg/youtube.svg" alt="YouTube" width="24" height="24" loading="lazy">
                                </a>
                            </div>
                        </div>
                    </article>
                </section>

                <!-- Contact Form -->
                <section class="edsys-contacts__form">
                    <div class="edsys-form-wrapper">
                        <h2 class="edsys-form__title">Связаться с нами</h2>
                        <p class="edsys-form__description">
                            Отправьте нам сообщение, и мы свяжемся с вами в ближайшее время
                        </p>

                        <form class="edsys-contact-form" action="/contacty/ajax.php" method="post" novalidate>
                            <div class="edsys-form__fields">
                                <!-- Name Fields -->
                                <div class="edsys-form__row">
                                    <div class="edsys-form__field edsys-form__field--floating">
                                        <input type="text"
                                               id="first_name"
                                               name="first_name"
                                               class="edsys-form__input"
                                               placeholder=" "
                                               required
                                               aria-describedby="first_name_error">
                                        <label for="first_name" class="edsys-form__label">
                                            Имя <span class="edsys-form__required">*</span>
                                        </label>
                                        <div id="first_name_error" class="edsys-form__error" role="alert"></div>
                                    </div>
                                    <div class="edsys-form__field edsys-form__field--floating">
                                        <input type="text"
                                               id="last_name"
                                               name="last_name"
                                               class="edsys-form__input"
                                               placeholder=" "
                                               required
                                               aria-describedby="last_name_error">
                                        <label for="last_name" class="edsys-form__label">
                                            Фамилия <span class="edsys-form__required">*</span>
                                        </label>
                                        <div id="last_name_error" class="edsys-form__error" role="alert"></div>
                                    </div>
                                </div>

                                <!-- Phone -->
                                <div class="edsys-form__field edsys-form__field--floating">
                                    <div class="edsys-phone-wrapper">
                                        <div class="edsys-phone__country" id="country_selector">
                                            <span class="edsys-phone__flag">🇷🇺</span>
                                            <span class="edsys-phone__code">+7</span>
                                            <i class="ph ph-thin ph-caret-down"></i>
                                        </div>
                                        <div class="edsys-phone__input-wrapper">
                                            <input type="tel"
                                                   id="phone"
                                                   name="phone"
                                                   class="edsys-phone__input"
                                                   placeholder=" "
                                                   aria-describedby="phone_error">
                                            <label for="phone" class="edsys-form__label">Номер телефона</label>
                                        </div>
                                    </div>
                                    <div class="edsys-country-dropdown" id="country_dropdown">
                                        <div class="edsys-country-option" data-country="ru" data-code="+7" data-flag="🇷🇺">
                                            <span class="edsys-country-flag">🇷🇺</span>
                                            <span class="edsys-country-name">Россия</span>
                                            <span class="edsys-country-code">+7</span>
                                        </div>
                                        <div class="edsys-country-option" data-country="by" data-code="+375" data-flag="🇧🇾">
                                            <span class="edsys-country-flag">🇧🇾</span>
                                            <span class="edsys-country-name">Беларусь</span>
                                            <span class="edsys-country-code">+375</span>
                                        </div>
                                        <div class="edsys-country-option" data-country="kz" data-code="+7" data-flag="🇰🇿">
                                            <span class="edsys-country-flag">🇰🇿</span>
                                            <span class="edsys-country-name">Казахстан</span>
                                            <span class="edsys-country-code">+7</span>
                                        </div>
                                    </div>
                                    <div id="phone_error" class="edsys-form__error" role="alert"></div>
                                </div>

                                <!-- Email -->
                                <div class="edsys-form__field edsys-form__field--floating">
                                    <input type="email"
                                           id="email"
                                           name="email"
                                           class="edsys-form__input"
                                           placeholder=" "
                                           required
                                           aria-describedby="email_error">
                                    <label for="email" class="edsys-form__label">
                                        Email <span class="edsys-form__required">*</span>
                                    </label>
                                    <div id="email_error" class="edsys-form__error" role="alert"></div>
                                </div>

                                <!-- Message -->
                                <div class="edsys-form__field edsys-form__field--floating">
                                <textarea id="message"
                                          name="message"
                                          class="edsys-form__textarea"
                                          rows="4"
                                          placeholder=" "
                                          required
                                          aria-describedby="message_error"></textarea>
                                    <label for="message" class="edsys-form__label">
                                        Ваше сообщение <span class="edsys-form__required">*</span>
                                    </label>
                                    <div id="message_error" class="edsys-form__error" role="alert"></div>
                                </div>
                            </div>

                            <!-- Submit -->
                            <div class="edsys-form__submit">
                                <button type="submit" class="edsys-form__button" id="submit_button">
                                <span class="edsys-button__text">
                                    <i class="ph ph-paper-plane-tilt"></i>
                                    Отправить
                                </span>
                                    <div class="edsys-button__loading" style="display: none;">
                                        <div class="edsys-spinner"></div>
                                    </div>
                                </button>
                                <p class="edsys-form__privacy">
                                    Нажимая кнопку "Отправить", вы соглашаетесь с
                                    <a href="/privacy/" target="_blank">политикой конфиденциальности</a>
                                </p>
                            </div>

                            <!-- Success/Error Messages -->
                            <div id="form_success" class="edsys-form__success" role="alert" style="display: none;">
                                <i class="ph ph-thin ph-check-circle"></i>
                                Спасибо! Ваше сообщение отправлено. Мы свяжемся с вами в ближайшее время.
                            </div>
                            <div id="form_error" class="edsys-form__general-error" role="alert" style="display: none;">
                                <i class="ph ph-thin ph-warning-circle"></i>
                                Произошла ошибка при отправке сообщения. Попробуйте еще раз или свяжитесь с нами по телефону.
                            </div>

                            <!-- Hidden Fields -->
                            <input type="hidden" name="form_token" id="form_token" value="">
                            <input type="hidden" name="timestamp" value="<?= time(); ?>">
                        </form>
                    </div>
                </section>
            </div>

            <!-- Map Section with Updated Yandex Maps -->
            <section class="edsys-contacts__map">
                <div class="edsys-map__container">
                    <div class="edsys-map__header">
                        <h2 class="edsys-map__title">Как добраться</h2>
                        <p class="edsys-map__description">
                            Наш офис находится в Калуге по адресу: Грабцевский пр., 17А
                        </p>
                    </div>
                    <div class="edsys-map__content">
                        <div class="edsys-map__widget">
                            <script type="text/javascript" charset="utf-8" async src="https://api-maps.yandex.ru/services/constructor/1.0/js/?um=constructor%3A7bf0c27cc8adc9a70c2ea7204118b545b65bede80a73afc9f3be55a673b42a98&amp;width=100%25&amp;height=500&amp;lang=ru_RU&amp;scroll=true"></script>
                            <!-- Map Info Overlay - FIXED: Added with white semi-transparent background -->
                            <div class="edsys-map__info-overlay" id="map-info-overlay">
                                <div class="edsys-map__info-content">
                                    <h4>EDS - Electric Distribution Systems</h4>
                                    <div class="edsys-map__info-details">
                                        <div class="edsys-map__info-item">
                                            <i class="ph ph-thin ph-map-pin"></i>
                                            <span>Грабцевский пр., 17А<br>Калуга, 248009</span>
                                        </div>
                                        <div class="edsys-map__info-item">
                                            <i class="ph ph-thin ph-car"></i>
                                            <span>Удобная парковка рядом с офисом</span>
                                        </div>
                                        <div class="edsys-map__info-item">
                                            <i class="ph ph-thin ph-phone"></i>
                                            <span>+7 (4842) 20-74-16</span>
                                        </div>
                                    </div>
                                    <div class="edsys-map__info-actions">
                                        <a href="tel:+74842207416" class="edsys-info-action">
                                            <i class="ph ph-phone"></i>
                                            Позвонить
                                        </a>
                                        <a href="https://yandex.ru/maps/org/eds/99890858437" target="_blank" class="edsys-info-action">
                                            <i class="ph ph-navigation-arrow"></i>
                                            Маршрут
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </div>

    <script src="/contacty/script.js"></script>
    </body>
    </html>

<?php require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php"); ?>