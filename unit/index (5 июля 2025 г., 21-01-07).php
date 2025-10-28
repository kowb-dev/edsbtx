<?php
/**
 * Статья: Неизвестное об известном. Unit
 * Автоматически создано скриптом создания структуры статей
 */

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/header.php");

$APPLICATION->SetTitle("Неизвестное об известном. Unit");
$APPLICATION->SetPageProperty("description", "Все привыкли измерять высоту оборудования, устанавливаемого в стойку в юнитах. А что такое Юнит (Unit)? Это неофициальная единица измерения");
$APPLICATION->SetPageProperty("keywords", "unit, юнит, стойковое оборудование, серверные стойки, рэковое оборудование");

// Canonical URL
$currentUrl = "https://" . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$APPLICATION->AddHeadString('<link rel="canonical" href="' . htmlspecialchars($currentUrl) . '">');

// Структурированные данные
$structuredData = [
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => "Неизвестное об известном. Unit",
    "description" => "Все привыкли измерять высоту оборудования, устанавливаемого в стойку в юнитах. А что такое Юнит (Unit)? Это неофициальная единица измерения",
    "author" => [
        "@type" => "Organization",
        "name" => "EDS - Electric Distribution Systems"
    ],
    "publisher" => [
        "@type" => "Organization", 
        "name" => "EDS",
        "url" => "https://" . $_SERVER["HTTP_HOST"]
    ],
    "url" => $currentUrl,
    "datePublished" => "2025-07-03T01:56:09+03:00",
    "dateModified" => "2025-07-03T01:56:09+03:00"
];

$APPLICATION->AddHeadString('<script type="application/ld+json">' . json_encode($structuredData, JSON_UNESCAPED_UNICODE) . '</script>');
?>

<div class="useful-article-container">
    <!-- Хлебные крошки -->
    <nav class="breadcrumbs">
        <a href="/">Главная</a> → 
        <a href="/polezno-znat/">Полезно знать</a> → 
        <span>Неизвестное об известном. Unit</span>
    </nav>

    <!-- Заголовок статьи -->
    <header class="article-header">
        <h1>Неизвестное об известном. Unit</h1>
        <div class="article-meta">
            <span class="article-date">03.07.2025</span>
            <span class="article-category">Полезно знать</span>
        </div>
    </header>

    <!-- Описание статьи -->
    <div class="article-intro">
        <p>Все привыкли измерять высоту оборудования, устанавливаемого в стойку в юнитах. А что такое Юнит (Unit)? Это неофициальная единица измерения</p>
    </div>

    <!-- Основное содержимое статьи -->
    <article class="article-content">
        <div class="content-placeholder">
            <h2>📝 Содержимое статьи требует добавления</h2>
            <p><strong>Эта страница была автоматически создана.</strong></p>
            <p>Необходимо добавить:</p>
            <ul>
                <li>Основной текст статьи</li>
                <li>Изображения и схемы</li>
                <li>Практические примеры</li>
                <li>Техническую документацию</li>
            </ul>
            <div class="article-image-placeholder">
                <img src="/upload/useful/cofr.jpg" alt="Неизвестное об известном. Unit" 
                     onerror="this.style.display='none'; this.nextElementSibling.style.display='block';">
                <div style="display:none; padding: 20px; background: #f0f0f0; text-align: center; color: #666;">
                    Изображение будет добавлено позже<br>
                    <small>/upload/useful/cofr.jpg</small>
                </div>
            </div>
        </div>
    </article>

    <!-- Навигация -->
    <div class="article-navigation">
        <a href="/polezno-znat/" class="btn-back">← Вернуться к списку статей</a>
        <a href="/contacts/" class="btn-contact">Задать вопрос специалисту</a>
    </div>
</div>

<style>
.useful-article-container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
    font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif;
    line-height: 1.6;
    color: #333;
}

.breadcrumbs {
    margin-bottom: 20px;
    font-size: 14px;
    color: #666;
}

.breadcrumbs a {
    color: #0066cc;
    text-decoration: none;
}

.breadcrumbs a:hover {
    text-decoration: underline;
}

.article-header {
    margin-bottom: 30px;
    text-align: center;
    padding-bottom: 20px;
    border-bottom: 1px solid #eee;
}

.article-header h1 {
    font-size: 2.2em;
    margin-bottom: 15px;
    color: #2c3e50;
    font-weight: 700;
}

.article-meta {
    display: flex;
    justify-content: center;
    gap: 15px;
    font-size: 14px;
    color: #666;
}

.article-category {
    background: #ff4757;
    color: white;
    padding: 4px 12px;
    border-radius: 15px;
    font-size: 12px;
    text-transform: uppercase;
    font-weight: 600;
}

.article-intro {
    background: #f8f9fa;
    padding: 20px;
    border-left: 4px solid #ff4757;
    margin-bottom: 30px;
    font-size: 1.1em;
    color: #555;
}

.content-placeholder {
    background: #fff3cd;
    border: 1px solid #ffeaa7;
    border-radius: 8px;
    padding: 30px;
    text-align: center;
    margin: 30px 0;
}

.content-placeholder h2 {
    color: #856404;
    margin-bottom: 15px;
}

.content-placeholder ul {
    text-align: left;
    max-width: 300px;
    margin: 20px auto;
}

.article-image-placeholder img {
    max-width: 100%;
    height: auto;
    border-radius: 8px;
    margin: 20px 0;
}

.article-navigation {
    display: flex;
    justify-content: space-between;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid #eee;
    gap: 15px;
}

.btn-back, .btn-contact {
    padding: 12px 24px;
    text-decoration: none;
    border-radius: 6px;
    font-weight: 600;
    display: inline-block;
    transition: all 0.2s ease;
}

.btn-back {
    background: #e9ecef;
    color: #495057;
}

.btn-back:hover {
    background: #dee2e6;
}

.btn-contact {
    background: #ff4757;
    color: white;
}

.btn-contact:hover {
    background: #ff3838;
}

@media (max-width: 768px) {
    .useful-article-container {
        padding: 15px;
    }
    
    .article-header h1 {
        font-size: 1.8em;
    }
    
    .article-meta {
        flex-direction: column;
        gap: 8px;
    }
    
    .article-navigation {
        flex-direction: column;
    }
    
    .btn-back, .btn-contact {
        text-align: center;
    }
}
</style>

<?php require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/footer.php"); ?>
