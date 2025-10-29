<?php
/**
 * Страница серии BSH - Лючки сценические с минимальной монтажной глубиной
 * 
 * @version 1.0.0
 * @author KW
 * @link https://kowb.ru
 * @file /cat/lyuchki-sczenicheskie/seriya-bsh/index.php
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");
$APPLICATION->SetTitle("Лючки сценические с минимальной монтажной глубиной серии BSH");
$APPLICATION->SetPageProperty("description", "Лючки сценические с минимальной монтажной глубиной серии BSH - компактное профессиональное решение для сцены.");
?>

<?$APPLICATION->IncludeComponent(
    "bitrix:breadcrumb",
    "",
    Array(
        "START_FROM" => "0", 
        "PATH" => "", 
        "SITE_ID" => "s1" 
    )
);?>

<style>
.edsys-series-page {
    max-width: min(1600px, 95vw);
    margin: clamp(2rem, 5vw, 5rem) auto;
    padding: 0 clamp(1rem, 3vw, 2rem);
}

.edsys-series-page__title {
    margin-block-end: clamp(2rem, 4vw, 3rem);
    text-align: center;
}

.edsys-product-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(min(280px, 100%), 1fr));
    gap: clamp(1rem, 3vw, 2rem);
    margin-block-end: clamp(2rem, 5vw, 4rem);
}

@media (min-width: 768px) {
    .edsys-product-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

.edsys-product-card {
    display: flex;
    flex-direction: column;
    text-decoration: none;
    color: inherit;
    border: 1px solid #e5e5e5;
    border-radius: clamp(0.5rem, 1vw, 0.75rem);
    padding: clamp(1rem, 2vw, 1.5rem);
    background: #fff;
    transition: box-shadow 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (hover: hover) {
    .edsys-product-card:hover {
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
        border-color: #d0d0d0;
    }
    
    .edsys-product-card:hover .edsys-product-card__image {
        transform: scale(1.08);
    }
}

.edsys-product-card__figure {
    width: 100%;
    aspect-ratio: 4 / 3;
    margin: 0 0 clamp(0.75rem, 2vw, 1.25rem) 0;
    overflow: hidden;
    border-radius: clamp(0.25rem, 0.5vw, 0.5rem);
    background: #f8f8f8;
}

.edsys-product-card__image {
    width: 100%;
    height: 100%;
    object-fit: contain;
    transition: transform 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.edsys-product-card__caption {
    font-weight: 500;
    text-align: center;
    line-height: 1.4;
}

.edsys-back-link {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: clamp(0.5rem, 1.5vw, 0.75rem) clamp(1rem, 2vw, 1.5rem);
    text-decoration: none;
    color: inherit;
    border: 1px solid #e5e5e5;
    border-radius: 0.5rem;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

@media (hover: hover) {
    .edsys-back-link:hover {
        background: #f8f8f8;
        border-color: #d0d0d0;
    }
}

.edsys-back-link i {
    font-size: 1.25em;
}
</style>

<main class="edsys-series-page">
    <h1 class="edsys-series-page__title">Лючки сценические с минимальной монтажной глубиной серии BSH</h1>
    
    <div class="edsys-product-grid">
        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-01/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/1f2/91hbfr1szyds937mano0l3abacdmdkm6.jpg"
                        alt="Лючок BSH 01 - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 01</figcaption>
            </a>
        </article>

        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-02/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/b87/zxamvur3dwsez4ap3hwcoz6usd2xste4.jpg"
                        alt="Лючок BSH 02 - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 02</figcaption>
            </a>
        </article>

        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-03/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/450/xxgnalna8b9j1tfkibqled3s20cff9tt.jpg"
                        alt="Лючок BSH 03 - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 03</figcaption>
            </a>
        </article>

        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-01l/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/967/4ztp4i1nu88wf3o3l0ol91sd9s41cvqx.jpg"
                        alt="Лючок BSH 01L - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 01L</figcaption>
            </a>
        </article>

        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-02l/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/435/7ioblwbuedkj14lcahulr5676aoz2yyc.jpg"
                        alt="Лючок BSH 02L - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 02L</figcaption>
            </a>
        </article>

        <article class="edsys-product-card">
            <a href="/cat/lyuchok-bsh-03l/" class="edsys-product-card__link">
                <figure class="edsys-product-card__figure">
                    <img 
                        src="/upload/iblock/b51/xesko6yy1ahgr747mkv2z7we3rwqzu07.jpg"
                        alt="Лючок BSH 03L - сценический лючок с минимальной монтажной глубиной"
                        class="edsys-product-card__image"
                        width="400"
                        height="300"
                        loading="lazy"
                    >
                </figure>
                <figcaption class="edsys-product-card__caption">Лючок BSH 03L</figcaption>
            </a>
        </article>
    </div>

    <a href="/cat/lyuchki-sczenicheskie/" class="edsys-back-link">
        <i class="ph ph-thin ph-arrow-left" aria-hidden="true"></i>
        <span>Вернуться к выбору серии</span>
    </a>
</main>

<?php
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php");
?>
