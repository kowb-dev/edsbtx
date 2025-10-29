# Changelog
Все изменения в проекте EDS документируются в этом файле.

---

## [1.7.0] - 2025-10-29

### ✅ Добавлено
- **Централизованная система CSS переменных** (`variables.css`)
  - Полная типографическая система с fluid размерами
  - Система отступов с clamp()
  - Electrical theme цветовая палитра
  - Токены анимаций
  - Grid и container системы

- **Утилитарные классы** (`utilities.css`)
  - Container utilities
  - Flexbox utilities с gap
  - Grid utilities с auto-fit
  - Spacing utilities (margin/padding)
  - Text utilities
  - Border utilities
  - Background utilities
  - Shadow utilities
  - Display utilities
  - Responsive visibility utilities
  - Transition utilities
  - Aspect ratio utilities
  - Overflow utilities

- **Современный CSS Reset** (`reset.css`)
  - Modern CSS reset с box-sizing
  - Базовые стили для элементов
  - Accessibility классы (.edsys-sr-only, .edsys-skip-link)
  - Focus styles с :focus-visible
  - Reduced motion support
  - Print styles

### 🔧 Исправлено
- **header.php** (v1.5 → v1.7)
  - Убран дублирующий вызов `$APPLICATION->ShowHead()`
  - Добавлено подключение новых CSS файлов в правильном порядке
  - Добавлен preload для variables.css

- **main.css** (v1.0 → v1.1.0)
  - Заменены фиксированные размеры шрифтов на CSS переменные
  - Заменены фиксированные отступы на fluid spacing переменные
  - Добавлены метаданные файла (author, version, URI)

- **style.css** (v1.0 → v1.1.0)
  - Удалён запрещённый эффект `transform: translateY(-5px)` в hover
  - Добавлен `@media (hover: hover)` для hover эффектов
  - Добавлены метаданные файла

- **search.css** (v1.0 → v1.1.0)
  - Удалён запрещённый эффект `transform: translateY(-5px)` в hover
  - Добавлен `@media (hover: hover)` для hover эффектов
  - Обновлены метаданные файла

### 📚 Документация
- **README.md** - Полностью переписан с подробной документацией:
  - CSS архитектура и структура
  - CSS Variables с примерами
  - Utilities classes
  - Naming conventions (BEM)
  - Mobile-first подход
  - Список запретов и обязательств
  - Примеры использования

---

## Структура файлов

### CSS (порядок подключения)
1. `variables.css` - CSS переменные
2. `reset.css` - CSS reset
3. `utilities.css` - Утилитарные классы
4. `main.css` - Основные стили
5. `style.css` - Специфичные стили

### Версии файлов
- header.php: v1.7
- variables.css: v1.0.0
- reset.css: v1.0.0
- utilities.css: v1.0.0
- main.css: v1.1.0
- style.css: v1.1.0
- search.css: v1.1.0

---

## Соответствие стандартам

### ✅ Выполнено
- [x] Использование CSS переменных
- [x] Префикс `edsys` для всех классов
- [x] Удаление `transform: translateY()` в hover
- [x] Замена фиксированных font-size на переменные
- [x] Mobile-first подход
- [x] Semantic HTML5
- [x] Accessibility (WCAG 2.1)
- [x] `@media (hover: hover)` для hover эффектов
- [x] Fluid typography с clamp()
- [x] Production ready код

### 🎯 Технические улучшения
- Оптимизация загрузки стилей с preload
- Модульная архитектура CSS
- Centralized design system
- Reusable utility classes
- Cross-browser compatibility
- Print styles
- Reduced motion support

---

**Автор:** KW  
**URI:** https://kowb.ru
