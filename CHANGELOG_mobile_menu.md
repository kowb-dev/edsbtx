# Исправления мобильного меню личного кабинета

**Дата:** 2024
**Автор:** KW https://kowb.ru

## Проблема

На разных страницах личного кабинета (`/personal/`, `/personal/orders/`, `/personal/favorites/`, `/personal/address/`, `/personal/password/`) мобильное меню открывалось по-разному из-за конфликта подходов:
- В `style.css` использовался подход с `display: none/block`
- В `menu.php` использовался подход с `position: fixed` и `transform`

Страница адресов доставки отображалась без стилей.

## Решение

### 1. Унифицированное мобильное меню (Mobile-First)

Реализован единый mobile-first подход во всех файлах:

**Mobile (375px+):**
- Меню скрыто за пределами экрана (`transform: translateX(-100%)`)
- При открытии плавно выезжает слева (`transition: 0.35s cubic-bezier`)
- Темный overlay с затемнением 60%
- Блокировка скролла body при открытом меню
- Ширина меню: `min(85vw, 22rem)`

**Desktop (768px+):**
- Меню статичное (`position: static`)
- Всегда видимое
- Без overlay и блокировки скролла

### 2. Обновленные файлы

#### `/local/templates/.default/components/bitrix/sale.personal.section/.default/style.css`
- **Версия:** 1.2.0 → 1.3.0
- Удалены конфликтующие стили `display: none/block`
- Добавлены mobile-first стили для sidebar с transform и visibility
- Добавлены стили для overlay
- Добавлены стили для блокировки скролла body
- Улучшены медиа-запросы для desktop

#### `/local/templates/.default/components/bitrix/sale.personal.section/.default/script.js`
- **Версия:** 1.1.0 → 1.2.0
- Добавлена логика управления overlay
- Добавлена блокировка скролла body при открытом меню
- Добавлено закрытие меню по клику на overlay
- Добавлено закрытие меню по Escape
- Добавлено автозакрытие при переходе на desktop

#### `/personal/includes/menu.php`
- **Версия:** 1.3.0 → 1.4.0
- Удалены дублирующие стили (перенесены в style.css)
- Улучшен встроенный скрипт с поддержкой медиа-запросов
- Упрощена структура

#### Страницы личного кабинета
Обновлены версии CSS во всех страницах:
- `/personal/address/index.php` - v1.2.0 → v1.3.0
- `/personal/orders/index.php` - v1.2.0 → v1.3.0
- `/personal/favorites/index.php` - v1.2.0 → v1.3.0
- `/personal/profile/index.php` - v1.3.0 (обновлено)
- `/personal/password/index.php` - v1.4.0 (обновлено)

### 3. Стили страницы адресов доставки

Уже были реализованы mobile-first стили для страницы адресов (секция начинается со строки 886 в style.css):
- Карточки адресов с адаптивной версткой
- Формы добавления/редактирования адресов
- Кнопки действий с touch-friendly размерами (min 44px)
- Пустые состояния
- Hover эффекты для desktop

## Ключевые особенности реализации

### Плавная анимация
```css
transition: transform 0.35s cubic-bezier(0.4, 0, 0.2, 1), 
            visibility 0.35s cubic-bezier(0.4, 0, 0.2, 1);
```

### Overlay с анимацией
```css
.edsys-account.has-overlay::before {
    content: '';
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.6);
    z-index: 998;
    animation: edsysOverlayFadeIn 0.35s forwards;
}
```

### Блокировка скролла
```css
body.edsys-menu-open {
    overflow: hidden;
    position: fixed;
    width: 100%;
}
```

### Accessibility
- ARIA атрибуты: `aria-expanded`, `aria-label`, `aria-current`
- Фокус на первом элементе меню при открытии
- Закрытие по Escape
- Touch-friendly размеры кнопок (min 44px)

## Результат

✅ Меню плавно выезжает на всех страницах личного кабинета
✅ Единообразное поведение на mobile и desktop
✅ Улучшенная accessibility
✅ Оптимизированная производительность (will-change, transform)
✅ Страница адресов полностью стилизована
✅ Соответствие mobile-first подходу
