<?php
/**
 * Страница 404 - Страница не найдена
 * 
 * @version 1.0.0
 * @author KW
 * @uri https://kowb.ru
 */

require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");

$APPLICATION->SetTitle("404 - Страница не найдена");
$APPLICATION->SetPageProperty("description", "Запрашиваемая страница не найдена. Воспользуйтесь поиском или перейдите в каталог оборудования EDSYS.");
?>

<main class="edsys-404-page">
    <!-- Hero секция с ошибкой -->
    <section class="edsys-404-hero">
        <div class="edsys-container">
            <div class="edsys-404-hero__content">
                <!-- Визуальная иконка 404 с электрическим эффектом -->
                <div class="edsys-404-hero__icon">
                    <svg class="edsys-404-hero__spark" viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path class="edsys-404-hero__spark-bolt" d="M100,20 L85,90 L115,90 L100,180 L130,100 L105,100 Z" />
                        <circle class="edsys-404-hero__spark-glow" cx="100" cy="100" r="80" />
                    </svg>
                    <div class="edsys-404-hero__code" aria-label="Ошибка 404">
                        <span class="edsys-404-hero__digit">4</span>
                        <span class="edsys-404-hero__digit edsys-404-hero__digit--accent">0</span>
                        <span class="edsys-404-hero__digit">4</span>
                    </div>
                </div>

                <!-- Текстовый контент -->
                <h1 class="edsys-404-hero__title">Сигнал потерян</h1>
                <p class="edsys-404-hero__description">
                    К сожалению, запрашиваемая страница не найдена. Возможно, она была перемещена или удалена. 
                    Воспользуйтесь поиском или вернитесь на главную страницу.
                </p>

                <!-- Кнопка на главную -->
                <a href="/" class="edsys-404-hero__button" aria-label="Вернуться на главную страницу">
                    <i class="ph ph-thin ph-house" aria-hidden="true"></i>
                    <span>На главную</span>
                </a>
            </div>
        </div>
    </section>

    <!-- Популярные категории -->
    <section class="edsys-404-categories edsys-container">
        <h2 class="edsys-404-categories__title">Популярные категории</h2>
        <p class="edsys-404-categories__subtitle">Возможно, вы искали что-то из этого</p>

        <div class="edsys-404-categories__grid">
            <a href="/cat/turovye/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/turovye-distribyutory_150x150.jpeg" 
                     alt="Туровые дистрибьюторы" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">Туровые дистрибьюторы</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Переносные блоки распределения питания для гастролей</p>
                </div>
            </a>

            <a href="/cat/rjekovye/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/rekovye-distribyutory_150x150.jpeg" 
                     alt="Рэковые дистрибьюторы" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">Рэковые дистрибьюторы</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Распределители питания для монтажа в стойку</p>
                </div>
            </a>

            <a href="/cat/dimmery/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/dimmery_150x150.jpeg" 
                     alt="Диммеры" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">Диммеры</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Устройства плавного регулирования яркости света</p>
                </div>
            </a>

            <a href="/cat/dmx-splitters/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/dmx-splittery_150x150.jpeg" 
                     alt="DMX сплиттеры" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">DMX сплиттеры</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Разветвители DMX сигнала для световых приборов</p>
                </div>
            </a>

            <a href="/cat/pulty-lebedochnye-digital/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/pulty-lebedochnye-cifrovye_150x150.jpeg" 
                     alt="Пульты лебедочные цифровые" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">Пульты лебедочные цифровые</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Современные пульты для автоматизированного управления лебёдками</p>
                </div>
            </a>

            <a href="/cat/kabelnaya-produktsiya/" class="edsys-404-category-card">
                <img src="/local/templates/.default/images/kabelnaya-produkciya_150x150.jpeg" 
                     alt="Кабельная продукция" 
                     width="150" 
                     height="150" 
                     loading="lazy">
                <div class="edsys-404-category-card__title">Кабельная продукция</div>
                <div class="edsys-404-category-card__overlay">
                    <p>Силовые кабели с различными промышленными разъемами</p>
                </div>
            </a>
        </div>
    </section>
</main>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>