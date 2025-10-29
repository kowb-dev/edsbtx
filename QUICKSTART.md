# 🚀 Quick Start Guide
Быстрый старт для разработки на проекте EDS

---

## 📁 Структура CSS

```
css/
├── variables.css   ← Все переменные здесь
├── reset.css       ← CSS reset
├── utilities.css   ← Готовые классы
├── main.css        ← Основные стили
└── style.css       ← Специфичные стили
```

---

## 🎨 Быстрое использование

### 1. Цвета
```css
background-color: var(--edsys-accent);       /* Красный акцент */
background-color: var(--edsys-voltage);      /* Синий */
color: var(--edsys-text);                    /* Основной текст */
color: var(--edsys-text-muted);              /* Приглушённый текст */
```

### 2. Типографика
```css
font-size: var(--edsys-fs-h1);              /* Заголовок H1 */
font-size: var(--fs-base);                  /* Базовый размер */
font-weight: var(--edsys-font-bold);        /* Жирный */
line-height: var(--edsys-lh-normal);        /* Межстрочный интервал */
```

### 3. Отступы
```css
padding: var(--space-md);                   /* Средний отступ */
gap: var(--space-lg);                       /* Промежуток */
margin-block: var(--space-2xl);             /* Вертикальные отступы */
```

### 4. Скругления
```css
border-radius: var(--radius-md);            /* Средний радиус */
border-radius: var(--radius-lg);            /* Большой радиус */
```

### 5. Transitions
```css
transition: all var(--edsys-transition-fast) var(--edsys-ease);
```

---

## 🛠️ Готовые классы

### Container
```html
<div class="edsys-container">
    <!-- Контейнер с автоматическими отступами -->
</div>
```

### Flexbox
```html
<div class="edsys-flex edsys-flex--center edsys-flex--gap-md">
    <div>Item 1</div>
    <div>Item 2</div>
</div>
```

### Grid (адаптивная сетка)
```html
<div class="edsys-grid edsys-grid--3">
    <!-- Автоматически 3 колонки, адаптируется под экран -->
</div>
```

### Отступы
```html
<div class="edsys-mt-xl edsys-py-md">
    <!-- margin-top: xl, padding vertical: md -->
</div>
```

### Текст
```html
<p class="edsys-text-center edsys-text-bold edsys-text-accent">
    Центрированный жирный красный текст
</p>
```

---

## 🎯 Именование (BEM)

```html
<section class="edsys-products">
    <div class="edsys-products__header">
        <h2 class="edsys-products__title">Заголовок</h2>
    </div>
    <div class="edsys-products__grid">
        <article class="edsys-product">
            <img class="edsys-product__image" src="..." alt="...">
            <h3 class="edsys-product__name">Название</h3>
            <p class="edsys-product__price edsys-product__price--discount">Цена</p>
        </article>
    </div>
</section>
```

**Правило:** `edsys-блок__элемент--модификатор`

---

## 🚫 НЕ ДЕЛАТЬ

```css
/* ❌ НЕПРАВИЛЬНО */
.card {
    font-size: 16px;                        /* Фиксированный размер */
    font-family: Arial;                     /* Указание шрифта */
    padding: 20px;                          /* Фиксированный отступ */
}

.card:hover {
    transform: translateY(-5px);            /* Запрещённый эффект */
}

/* ✅ ПРАВИЛЬНО */
.edsys-card {
    font-size: var(--fs-base);              /* Переменная */
    font-family: var(--edsys-font-primary); /* Переменная */
    padding: var(--space-lg);               /* Переменная */
}

@media (hover: hover) {
    .edsys-card:hover {
        box-shadow: var(--edsys-shadow);    /* Только тень */
    }
}
```

---

## 📱 Mobile-First

```css
/* ✅ Всегда пишем от мобильной версии */
.edsys-section {
    padding: var(--space-md);
    /* Мобильная версия по умолчанию */
}

/* Расширяем на больших экранах только если нужно */
@media (min-width: 768px) {
    .edsys-section {
        padding: var(--space-2xl);
    }
}
```

---

## 🎨 Иконки Phosphor

```html
<!-- Тонкие иконки (по умолчанию) -->
<i class="ph-thin ph-shopping-cart"></i>
<i class="ph-thin ph-magnifying-glass"></i>
<i class="ph-thin ph-heart"></i>
<i class="ph-thin ph-user"></i>

<!-- В кнопке -->
<button class="edsys-btn">
    <i class="ph-thin ph-plus"></i>
    Добавить
</button>
```

Каталог иконок: https://phosphoricons.com

---

## ✨ Accessibility

```html
<!-- Скрытый текст для скринридеров -->
<span class="edsys-sr-only">Описание для незрячих</span>

<!-- Ссылка для пропуска контента -->
<a href="#main" class="edsys-skip-link">Перейти к содержанию</a>

<!-- Всегда указывай alt для изображений -->
<img 
    src="product.jpg" 
    alt="Кабель КГТП-ХЛ 4x2.5" 
    width="300" 
    height="200" 
    loading="lazy"
>
```

---

## 🔥 Полезные комбинации

### Карточка товара
```html
<article class="edsys-card edsys-bg-white edsys-rounded-md edsys-shadow">
    <img class="edsys-aspect-square" src="..." alt="...">
    <div class="edsys-py-md edsys-px-lg">
        <h3 class="edsys-mb-sm">Название</h3>
        <p class="edsys-text-accent edsys-text-bold">9 999 ₽</p>
    </div>
</article>
```

### Секция с заголовком
```html
<section class="edsys-container edsys-py-3xl">
    <h2 class="edsys-text-center edsys-mb-xl">Заголовок секции</h2>
    <div class="edsys-grid edsys-grid--3">
        <!-- Контент -->
    </div>
</section>
```

### Flexbox для кнопок
```html
<div class="edsys-flex edsys-flex--gap-md edsys-flex--wrap">
    <button class="edsys-btn">Кнопка 1</button>
    <button class="edsys-btn">Кнопка 2</button>
</div>
```

---

## 📖 Документация

- `README.md` - Полная документация
- `CHANGELOG.md` - История изменений
- `GEMINI.md` - Инструкции для AI разработки
- `variables.css` - Все переменные с комментариями

---

**Автор:** KW  
**URI:** https://kowb.ru
