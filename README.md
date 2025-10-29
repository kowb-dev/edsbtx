# EDS - Electric Distribution Systems
## Документация проекта

Проект на **1С-Битрикс: Управление сайтом 25.550.100**  
Тематика: B2B E-commerce в области энергетики, сценического оборудования и распределения электропитания.

---

## 🎨 CSS Архитектура

Проект использует современную модульную систему CSS с полной поддержкой CSS переменных и mobile-first подхода.

### Структура стилей

```
local/templates/edsy_main/css/
├── variables.css    - Централизованная система CSS переменных
├── reset.css        - Современный CSS reset и базовые стили
├── utilities.css    - Утилитарные классы для быстрой разработки
├── main.css         - Основные стили компонентов
├── style.css        - Стили страницы поиска
└── search.css       - Дополнительные стили поиска
```

### Порядок подключения

1. **variables.css** - CSS переменные (цвета, типографика, отступы)
2. **reset.css** - Сброс стилей браузера
3. **utilities.css** - Утилитарные классы
4. **main.css** - Основные стили
5. **style.css** - Специфичные стили

---

## 📐 CSS Variables

Все CSS переменные определены в `variables.css` и используют префикс `--edsys-*`.

### Основные категории

#### Типографика
```css
--edsys-font-primary: 'Open Sans', sans-serif;
--edsys-font-regular: 400;
--edsys-font-bold: 700;

--edsys-fs-hero: clamp(2rem, 5vw + 1rem, 3.5rem);
--edsys-fs-h1: clamp(1.75rem, 4vw + 0.5rem, 2.5rem);
--edsys-fs-h2: clamp(1.5rem, 3vw + 0.5rem, 2rem);
```

#### Отступы (Fluid Spacing)
```css
--space-xs: clamp(0.25rem, 0.5vw, 0.5rem);
--space-sm: clamp(0.5rem, 1vw, 0.75rem);
--space-md: clamp(0.75rem, 1.5vw, 1rem);
--space-lg: clamp(1rem, 2vw, 1.5rem);
--space-xl: clamp(1.5rem, 3vw, 2rem);
```

#### Цвета (Electrical Theme)
```css
--edsys-accent: #ff2545;
--edsys-voltage: #0066cc;
--edsys-spark: #ffcc00;
--edsys-circuit: #00cc99;
```

Полный список см. в `variables.css`.

---

## 🛠️ Utilities Classes

Утилитарные классы для быстрой разработки без написания CSS.

### Примеры

```html
<!-- Container -->
<div class="edsys-container">...</div>

<!-- Flexbox -->
<div class="edsys-flex edsys-flex--center edsys-flex--gap-md">...</div>

<!-- Grid -->
<div class="edsys-grid edsys-grid--3">...</div>

<!-- Spacing -->
<div class="edsys-mt-xl edsys-py-md">...</div>

<!-- Text -->
<p class="edsys-text-center edsys-text-bold">...</p>
```

---

## 🔑 Naming Conventions

### BEM методология с префиксом `edsys`

```css
.edsys-block {}
.edsys-block__element {}
.edsys-block--modifier {}
```

### Примеры

```html
<section class="edsys-products">
    <div class="edsys-products__header">
        <h2 class="edsys-products__title edsys-products__title--large">...</h2>
    </div>
</section>
```

---

## 📱 Mobile-First Approach

Все стили пишутся от 375px и расширяются на большие экраны.

### Адаптивные техники

- Fluid typography с `clamp()`
- CSS Grid с `auto-fit/auto-fill`
- Flexbox с `gap`
- Относительные единицы (rem, em, %, vw)
- Media queries только для исключений

```css
/* ✅ Правильно - fluid без media queries */
.edsys-card {
    padding: clamp(1rem, 3vw, 2rem);
    font-size: clamp(0.875rem, 2vw, 1rem);
}

/* ❌ Неправильно - фиксированные значения */
.card {
    padding: 20px;
    font-size: 14px;
}
```

---

## 🚫 Запрещено в проекте

1. ❌ `transform: translateY()` в hover эффектах
2. ❌ Указание размеров шрифтов напрямую (`font-size: 16px`)
3. ❌ Указание семейства шрифтов (используйте переменную)
4. ❌ Устаревшие методы Битрикс
5. ❌ Необработанные ошибки

---

## ✅ Обязательно

1. ✔ Использовать CSS переменные
2. ✔ Префикс `edsys` для всех классов
3. ✔ Семантический HTML5
4. ✔ Атрибуты изображений (width, height, loading, alt)
5. ✔ Phosphor icons (ph-thin)
6. ✔ Mobile-first подход
7. ✔ Accessibility (WCAG 2.1)
8. ✔ @media (hover: hover) для hover эффектов

---

## 🎯 Иконки

Проект использует **Phosphor Icons** с весом `ph-thin`:

```html
<i class="ph-thin ph-shopping-cart"></i>
<i class="ph-thin ph-magnifying-glass"></i>
<i class="ph-thin ph-heart"></i>
```

Библиотека: https://unpkg.com/@phosphor-icons/web@2.1.1/src/thin/style.css

---

## 🏗️ Структура шаблона

```
local/templates/edsy_main/
├── header.php          - Шапка сайта (v1.7)
├── footer.php          - Подвал сайта
├── css/
│   ├── variables.css   - CSS переменные (v1.0.0)
│   ├── reset.css       - CSS reset (v1.0.0)
│   ├── utilities.css   - Utilities (v1.0.0)
│   ├── main.css        - Основные стили (v1.1.0)
│   └── style.css       - Страница поиска (v1.1.0)
├── js/
│   └── main.js         - Основной JS
└── components/         - Компоненты Битрикс
```

---

## 📚 Дополнительная информация

Подробные инструкции по разработке см. в `GEMINI.md`.

**Автор:** KW  
**URI:** https://kowb.ru  
**Последнее обновление:** 2025
