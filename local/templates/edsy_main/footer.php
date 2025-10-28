<?php
/**
 * Footer Template - Tablet Layout Version
 * Version: 1.4.3
 * Date: 2025-07-19
 * Description: Версия футера с автоматическим версионированием JS
 * File: /local/templates/edsy_main/footer.php
 */

if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

$sessid = bitrix_sessid();
?>

<footer class="edsys-footer" role="contentinfo">
    <div class="edsys-footer__container">
        <!-- Newsletter Section for Tablet Layout -->
        <div class="edsys-footer__newsletter-tablet">
            <div class="edsys-footer__newsletter-tablet-container">
                <div class="edsys-footer__newsletter-content">
                    <h3 class="edsys-footer__newsletter-title">Подписка на новости</h3>
                    <p class="edsys-footer__newsletter-text">
                        Получайте информацию о новинках, специальных предложениях и технических решениях
                    </p>

                    <div class="edsys-footer__newsletter-wrapper">
                        <form class="edsys-footer__newsletter-form"
                              id="newsletter-form"
                              data-form-type="newsletter"
                              method="post"
                              novalidate>

                            <input type="email"
                                   name="email"
                                   id="newsletter-email"
                                   class="edsys-footer__newsletter-input"
                                   placeholder="Ваш email"
                                   required
                                   aria-label="Email для подписки"
                                   autocomplete="email">

                            <button type="submit" class="edsys-footer__newsletter-btn" id="newsletter-submit">
                                <span class="edsys-newsletter-btn__text">Подписаться</span>
                                <span class="edsys-newsletter-btn__loading">
                                    <i class="ph ph-thin ph-circle-notch"></i>
                                </span>
                            </button>

                            <input type="hidden" name="sessid" value="<?= $sessid ?>">
                            <input type="hidden" name="form_token" value="<?= md5(time() . ($_SERVER['REMOTE_ADDR'] ?? 'unknown')) ?>">
                            <input type="hidden" name="timestamp" value="<?= time() ?>">
                            <input type="hidden" name="form_type" value="newsletter">

                            <input type="text" name="website" style="display: none !important; position: absolute !important; left: -9999px !important;" tabindex="-1" autocomplete="off">
                        </form>

                        <div class="edsys-newsletter__messages" id="newsletter-messages">
                            <div class="edsys-newsletter__success" id="newsletter-success" role="status" aria-live="polite">
                                <i class="ph ph-thin ph-check-circle"></i>
                                <span id="newsletter-success-text">Подписка оформлена успешно!</span>
                            </div>
                            <div class="edsys-newsletter__error" id="newsletter-error" role="alert" aria-live="assertive">
                                <i class="ph ph-thin ph-warning-circle"></i>
                                <span id="newsletter-error-text">Ошибка оформления подписки</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="edsys-footer__main">
            <div class="edsys-footer__grid">

                <!-- Brand and Contacts -->
                <div class="edsys-footer__brand">
                    <div class="edsys-footer__logo">
			            <?php if($APPLICATION->GetCurPage() !== '/'): ?>
                            <a href="/" class="edsys-footer__logo-link">
                                <img
                                        src="<?=DEFAULT_TEMPLATE_PATH?>/images/logo.svg"
                                        alt="Логотип компании EDS - Electric Distribution Systems"
                                        width="180"
                                        height="135"
                                        loading="lazy"
                                >
                                <div class="edsys-footer__title">
                                    <span>ELECTRIC</span>
                                    <span>DISTRIBUTION</span>
                                    <span>SYSTEMS</span>
                                </div>
                            </a>
			            <?php else: ?>
                            <div class="edsys-footer__logo-content">
                                <img
                                        src="<?=DEFAULT_TEMPLATE_PATH?>/images/logo.svg"
                                        alt="Логотип компании EDS - Electric Distribution Systems"
                                        width="180"
                                        height="135"
                                        loading="lazy"
                                >
                                <div class="edsys-footer__title">
                                    <span>ELECTRIC</span>
                                    <span>DISTRIBUTION</span>
                                    <span>SYSTEMS</span>
                                </div>
                            </div>
			            <?php endif; ?>
                    </div>

                    <p class="edsys-footer__tagline">
                        Профессиональные решения для распределения электроэнергии и управления сигналами
                    </p>

                    <div class="edsys-footer__contacts">
                        <a href="tel:+79105273538" class="edsys-footer__contact-item">
                            <i class="ph ph-thin ph-phone edsys-footer__contact-icon"></i>
                            <span>+7 (910) 527-35-38</span>
                        </a>

                        <a href="mailto:info@edsy.ru" class="edsys-footer__contact-item">
                            <i class="ph ph-thin ph-envelope edsys-footer__contact-icon"></i>
                            <span>info@edsy.ru</span>
                        </a>

                        <div class="edsys-footer__contact-item">
                            <i class="ph ph-thin ph-clock edsys-footer__contact-icon"></i>
                            <span>Пн-Пт: 9:00-18:00 МСК</span>
                        </div>
                    </div>

                    <div class="edsys-footer__social">
                        <a href="https://wa.me/79105273538?text=Здравствуйте!"
                           class="edsys-footer__social-link"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="WhatsApp">
                            <i class="ph ph-thin ph-whatsapp-logo edsys-footer__social-icon"></i>
                        </a>

                        <a href="https://vk.com/edsystems"
                           class="edsys-footer__social-link"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="ВКонтакте">
                            <i class="ph ph-thin ph-globe edsys-footer__social-icon"></i>
                        </a>

                        <a href="https://www.youtube.com/channel/UCKNUJ_Z_jNGaTm-GRqgED6g"
                           class="edsys-footer__social-link"
                           target="_blank"
                           rel="noopener noreferrer"
                           aria-label="YouTube">
                            <i class="ph ph-thin ph-youtube-logo edsys-footer__social-icon"></i>
                        </a>
                    </div>
                </div>

                <!-- About Company -->
                <div class="edsys-footer__section">
                    <div class="edsys-footer__section-header">
                        <h3 class="edsys-footer__section-title">О компании</h3>
                        <i class="ph ph-thin ph-caret-down edsys-footer__accordion-icon"></i>
                    </div>
                    <div class="edsys-footer__section-content">
                        <ul class="edsys-footer__links">
                            <li><a href="/o-kompanii/" class="edsys-footer__link">Про EDS</a></li>
                            <li><a href="/o-produktsii/" class="edsys-footer__link">Продукция</a></li>
                            <li><a href="/sertifikaty-eds/" class="edsys-footer__link">Сертификаты</a></li>
                            <li><a href="/individualnye-resheniya/" class="edsys-footer__link">Индивидуальные решения</a></li>
                        </ul>
                    </div>
                </div>

                <!-- For Customers -->
                <div class="edsys-footer__section">
                    <div class="edsys-footer__section-header">
                        <h3 class="edsys-footer__section-title">Покупателям</h3>
                        <i class="ph ph-thin ph-caret-down edsys-footer__accordion-icon"></i>
                    </div>
                    <div class="edsys-footer__section-content">
                        <ul class="edsys-footer__links">
                            <li><a href="/polezno-znat/" class="edsys-footer__link">Полезно знать</a></li>
                            <li><a href="/otzyvy/" class="edsys-footer__link">Отзывы</a></li>
                            <li><a href="/oplata-i-dostavka/" class="edsys-footer__link">Оплата и доставка</a></li>
                            <li><a href="/contacty/" class="edsys-footer__link">Контакты</a></li>
                            <li><a href="/garantiya/" class="edsys-footer__link">Гарантия</a></li>
                        </ul>
                    </div>
                </div>

                <!-- My Account -->
                <div class="edsys-footer__section">
                    <div class="edsys-footer__section-header">
                        <h3 class="edsys-footer__section-title">Мой аккаунт</h3>
                        <i class="ph ph-thin ph-caret-down edsys-footer__accordion-icon"></i>
                    </div>
                    <div class="edsys-footer__section-content">
                        <ul class="edsys-footer__links">
                            <li><a href="/my-account/" class="edsys-footer__link">Личный кабинет</a></li>
                            <li><a href="/cart/" class="edsys-footer__link">Корзина</a></li>
                            <li><a href="/izbrannoe/" class="edsys-footer__link">Избранное</a></li>
                            <li><a href="/sravnenie/" class="edsys-footer__link">Сравнение</a></li>
                        </ul>
                    </div>
                </div>

                <!-- Newsletter Subscription (Desktop/Mobile) -->
                <div class="edsys-footer__newsletter">
                    <h3 class="edsys-footer__newsletter-title">Подписка на новости</h3>
                    <p class="edsys-footer__newsletter-text">
                        Получайте информацию о новинках, специальных предложениях и технических решениях
                    </p>

                    <div class="edsys-footer__newsletter-wrapper">
                        <form class="edsys-footer__newsletter-form"
                              id="newsletter-form-desktop"
                              data-form-type="newsletter"
                              method="post"
                              novalidate>

                            <input type="email"
                                   name="email"
                                   id="newsletter-email-desktop"
                                   class="edsys-footer__newsletter-input"
                                   placeholder="Ваш email"
                                   required
                                   aria-label="Email для подписки"
                                   autocomplete="email">

                            <button type="submit" class="edsys-footer__newsletter-btn" id="newsletter-submit-desktop">
                                <span class="edsys-newsletter-btn__text">Подписаться</span>
                                <span class="edsys-newsletter-btn__loading">
                                    <i class="ph ph-thin ph-circle-notch"></i>
                                </span>
                            </button>

                            <input type="hidden" name="sessid" value="<?= $sessid ?>">
                            <input type="hidden" name="form_token" value="<?= md5(time() . ($_SERVER['REMOTE_ADDR'] ?? 'unknown')) ?>">
                            <input type="hidden" name="timestamp" value="<?= time() ?>">
                            <input type="hidden" name="form_type" value="newsletter">

                            <input type="text" name="website" style="display: none !important; position: absolute !important; left: -9999px !important;" tabindex="-1" autocomplete="off">
                        </form>

                        <div class="edsys-newsletter__messages" id="newsletter-messages-desktop">
                            <div class="edsys-newsletter__success" id="newsletter-success-desktop" role="status" aria-live="polite">
                                <i class="ph ph-thin ph-check-circle"></i>
                                <span id="newsletter-success-text-desktop">Подписка оформлена успешно!</span>
                            </div>
                            <div class="edsys-newsletter__error" id="newsletter-error-desktop" role="alert" aria-live="assertive">
                                <i class="ph ph-thin ph-warning-circle"></i>
                                <span id="newsletter-error-text-desktop">Ошибка оформления подписки</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Features Section -->
        <div class="edsys-footer__features">
            <div class="edsys-footer__features-grid">
                <div class="edsys-footer__feature">
                    <i class="ph ph-thin ph-truck edsys-footer__feature-icon"></i>
                    <span>Доставка по России и СНГ</span>
                </div>

                <div class="edsys-footer__feature">
                    <i class="ph ph-thin ph-shield-check edsys-footer__feature-icon"></i>
                    <span>Сертифицированная продукция</span>
                </div>

                <div class="edsys-footer__feature">
                    <i class="ph ph-thin ph-wrench edsys-footer__feature-icon"></i>
                    <span>Техническая поддержка</span>
                </div>

                <div class="edsys-footer__feature">
                    <i class="ph ph-thin ph-medal edsys-footer__feature-icon"></i>
                    <span>Более 9 лет на рынке</span>
                </div>
            </div>
        </div>

        <!-- Bottom Section -->
        <div class="edsys-footer__bottom">
            <div class="edsys-footer__bottom-content">
                <p class="edsys-footer__copyright">
                    © <?= date('Y') ?> EDS - Electric Distribution Systems. Все права защищены.
                </p>

                <ul class="edsys-footer__legal-links">
                    <li><a href="/privacy-policy/" class="edsys-footer__legal-link">Политика конфиденциальности</a></li>
                    <li><a href="/personal-data-policy/" class="edsys-footer__legal-link">Политика в отношении обработки и защиты персональных данных</a></li>
                    <li><a href="/data-protection/" class="edsys-footer__legal-link">Положение об обработке и защите персональных данных</a></li>
                </ul>
            </div>
        </div>
    </div>
</footer>

</div>

<?php
// Автоматическое версионирование JS файла
$footerJsPath = $_SERVER['DOCUMENT_ROOT'] . '/local/templates/.default/js/modules/footer.js';
$footerVersion = file_exists($footerJsPath) ? filemtime($footerJsPath) : time();
?>
<script src="/local/templates/.default/js/modules/footer.js?v=<?= $footerVersion ?>"></script>

</body>
</html>