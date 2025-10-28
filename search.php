<?php
/**
 * Search Page
 *
 * @author      KW https://kowb.ru
 * @version     1.0
 */
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Search");
$APPLICATION->SetPageProperty("description", "Search for products, articles, and information on the EDS website.");
?>

<main class="edsys-search-page">

    <!-- Search Input Section -->
    <section class="edsys-search-bar edsys-container">
        <div class="edsys-search-bar__wrapper">
            <h1 class="edsys-search-bar__title">What are you looking for?</h1>
            <form action="/search/" method="get" class="edsys-search-bar__form">
                <div class="edsys-search-bar__input-group">
                    <input type="text" name="q" class="edsys-search-bar__input" placeholder="e.g., DMX splitter, power distributor..." value="<?=htmlspecialchars($_GET['q'] ?? '')?>">
                    <button type="submit" class="edsys-search-bar__button">
                        <i class="ph-thin ph-magnifying-glass"></i>
                        <span>Search</span>
                    </button>
                </div>
            </form>
        </div>
    </section>

    <!-- Search Categories Section -->
    <section class="edsys-search-categories edsys-container">
        <h2 class="edsys-search-categories__title">Or browse by category:</h2>
        <div class="edsys-search-categories__grid">
            <a href="/catalog/" class="edsys-search-category">
                <div class="edsys-search-category__icon">
                    <i class="ph-thin ph-plugs"></i>
                </div>
                <h3 class="edsys-search-category__title">Catalog</h3>
                <p class="edsys-search-category__description">Explore our professional power and signal distribution solutions.</p>
            </a>
            <a href="/kalkulyatory/" class="edsys-search-category">
                <div class="edsys-search-category__icon">
                    <i class="ph-thin ph-calculator"></i>
                </div>
                <h3 class="edsys-search-category__title">Calculators</h3>
                <p class="edsys-search-category__description">Calculate power, voltage, and other electrical parameters.</p>
            </a>
            <a href="/stati-tablitsy-nagruzok/" class="edsys-search-category">
                <div class="edsys-search-category__icon">
                    <i class="ph-thin ph-book-open"></i>
                </div>
                <h3 class="edsys-search-category__title">Articles & Reviews</h3>
                <p class="edsys-search-category__description">Read our expert articles, reviews, and technical guides.</p>
            </a>
            <a href="/o-kompanii/" class="edsys-search-category">
                <div class="edsys-search-category__icon">
                    <i class="ph-thin ph-info"></i>
                </div>
                <h3 class="edsys-search-category__title">About Us</h3>
                <p class="edsys-search-category__description">Learn more about our company, certificates, and contact information.</p>
            </a>
        </div>
    </section>

    <!-- Search Results Section -->
    <section class="edsys-search-results edsys-container">
        <?
        if (!empty($_GET['q'])) {
            $APPLICATION->IncludeComponent(
                "bitrix:search.page",
                "edsys_search",
                array(
                    "RESTART" => "Y",
                    "NO_WORD_LOGIC" => "Y",
                    "USE_LANGUAGE_GUESS" => "N",
                    "CHECK_DATES" => "Y",
                    "arrFILTER" => array("no_filter"),
                    "SHOW_WHERE" => "N",
                    "SHOW_WHEN" => "N",
                    "PAGE_RESULT_COUNT" => "50",
                    "CACHE_TYPE" => "A",
                    "CACHE_TIME" => "3600",
                    "DISPLAY_TOP_PAGER" => "Y",
                    "DISPLAY_BOTTOM_PAGER" => "Y",
                    "PAGER_TITLE" => "Search results",
                    "PAGER_SHOW_ALWAYS" => "Y",
                    "PAGER_TEMPLATE" => "modern",
                ),
                false
            );
        }
        ?>
    </section>

</main>

<?php
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");
?>
