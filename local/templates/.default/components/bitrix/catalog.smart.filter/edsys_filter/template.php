<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

$this->setFrameMode(true);

if (empty($arResult["ITEMS"])) {
    return;
}

// Добавляем специфичные фильтры для EDS
$customFilters = [
    'INPUT_TYPE' => [
        'NAME' => 'Тип ввода',
        'VALUES' => [
            'CEE_16A_3PIN' => 'CEE 16A 3-pin',
            'CEE_16A_5PIN' => 'CEE 16A 5-pin',
            'CEE_32A_3PIN' => 'CEE 32A 3-pin',
            'CEE_32A_5PIN' => 'CEE 32A 5-pin',
            'POWERCON_20A' => 'PowerCon 20A',
            'REVOS_16PIN' => 'Revos 16-pin',
            'SCHUKO_16A' => 'Schuko 16A',
            'SOCAPEX_19PIN' => 'Socapex 19-pin'
        ]
    ],
    'ADDITIONAL' => [
        'NAME' => 'Дополнительно',
        'VALUES' => [
            'AUTO_SWITCH' => 'Автомат. выключатели',
            'AMMETER' => 'Амперметр',
            'VOLTMETER' => 'Вольтметр',
            'CABLE_INPUT' => 'Кабельный ввод',
            'PASS_SOCKET' => 'Проходная розетка'
        ]
    ]
];
?>

<div class="edsys-filter" data-filter-ajax-url="<?=htmlspecialchars($arResult["FILTER_URL"])?>">
    <div class="edsys-filter__header">
        <h3 class="edsys-filter__title">
            <i class="ph ph-thin ph-funnel"></i>
            Фильтры
        </h3>

        <?php if (!empty($arResult["ITEMS"])): ?>
        <button type="button"
                class="edsys-filter__reset"
                id="edsys-filter-reset"
                style="display: none;">
            <i class="ph ph-thin ph-x"></i>
            Сбросить
        </button>
        <?php endif; ?>
    </div>

    <form name="<?=htmlspecialchars($arResult["FILTER_NAME"])?>"
          action="<?=htmlspecialchars($arResult["FORM_ACTION"])?>"
          method="get"
          class="edsys-filter__form">

        <?php foreach ($arResult["HIDDEN"] as $arItem): ?>
        <input type="hidden"
               name="<?=htmlspecialchars($arItem["CONTROL_NAME"])?>"
               id="<?=htmlspecialchars($arItem["CONTROL_ID"])?>"
               value="<?=htmlspecialchars($arItem["HTML_VALUE"])?>" />
        <?php endforeach; ?>

        <!-- Кастомные фильтры EDS -->
        <?php foreach ($customFilters as $filterId => $filterData): ?>
        <div class="edsys-filter__group">
            <div class="edsys-filter__group-header" data-toggle="collapse" data-target="#filter-<?=$filterId?>">
                <h4 class="edsys-filter__group-title"><?=$filterData['NAME']?></h4>
                <i class="ph ph-thin ph-caret-down edsys-filter__group-icon"></i>
            </div>

            <div class="edsys-filter__group-content collapse show" id="filter-<?=$filterId?>">
                <?php foreach ($filterData['VALUES'] as $valueId => $valueName): ?>
                <label class="edsys-filter__checkbox">
                    <input type="checkbox"
                           name="arrFilter_PROPERTY_<?=$filterId?>[]"
                           value="<?=$valueId?>"
                           class="edsys-filter__checkbox-input">
                    <span class="edsys-filter__checkbox-mark"></span>
                    <span class="edsys-filter__checkbox-text"><?=$valueName?></span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <!-- Стандартные фильтры Битрикс -->
        <?php foreach ($arResult["ITEMS"] as $key => $arItem): ?>

        <?php if ($arItem["DISPLAY_TYPE"] === "A"): // Checkbox list ?>
        <div class="edsys-filter__group">
            <div class="edsys-filter__group-header" data-toggle="collapse" data-target="#filter-<?=$key?>">
                <h4 class="edsys-filter__group-title"><?=$arItem["NAME"]?></h4>
                <i class="ph ph-thin ph-caret-down edsys-filter__group-icon"></i>
            </div>

            <div class="edsys-filter__group-content collapse show" id="filter-<?=$key?>">
                <?php foreach ($arItem["VALUES"] as $val => $arVal): ?>
                <label class="edsys-filter__checkbox">
                    <input type="checkbox"
                           name="<?=htmlspecialchars($arVal["CONTROL_NAME"])?>"
                           value="<?=htmlspecialchars($arVal["HTML_VALUE"])?>"
                           <?=$arVal["CHECKED"] ? 'checked="checked"' : ''?>
                           class="edsys-filter__checkbox-input">
                    <span class="edsys-filter__checkbox-mark"></span>
                    <span class="edsys-filter__checkbox-text">
                        <?=$arVal["VALUE"]?>
                        <?php if ($arVal["ELEMENT_COUNT"] > 0): ?>
                        <span class="edsys-filter__count">(<?=$arVal["ELEMENT_COUNT"]?>)</span>
                        <?php endif; ?>
                    </span>
                </label>
                <?php endforeach; ?>
            </div>
        </div>

        <?php elseif ($arItem["DISPLAY_TYPE"] === "B"): // Range slider ?>
        <div class="edsys-filter__group">
            <div class="edsys-filter__group-header" data-toggle="collapse" data-target="#filter-<?=$key?>">
                <h4 class="edsys-filter__group-title"><?=$arItem["NAME"]?></h4>
                <i class="ph ph-thin ph-caret-down edsys-filter__group-icon"></i>
            </div>

            <div class="edsys-filter__group-content collapse show" id="filter-<?=$key?>">
                <div class="edsys-filter__range">
                    <div class="edsys-filter__range-inputs">
                        <input type="number"
                               name="<?=htmlspecialchars($arItem["VALUES"]["MIN"]["CONTROL_NAME"])?>"
                               value="<?=htmlspecialchars($arItem["VALUES"]["MIN"]["HTML_VALUE"])?>"
                               placeholder="от <?=$arItem["VALUES"]["MIN"]["VALUE"]?>"
                               class="edsys-filter__range-input">

                        <span class="edsys-filter__range-separator">—</span>

                        <input type="number"
                               name="<?=htmlspecialchars($arItem["VALUES"]["MAX"]["CONTROL_NAME"])?>"
                               value="<?=htmlspecialchars($arItem["VALUES"]["MAX"]["HTML_VALUE"])?>"
                               placeholder="до <?=$arItem["VALUES"]["MAX"]["VALUE"]?>"
                               class="edsys-filter__range-input">
                    </div>

                    <div class="edsys-filter__range-slider"
                         data-min="<?=$arItem["VALUES"]["MIN"]["VALUE"]?>"
                         data-max="<?=$arItem["VALUES"]["MAX"]["VALUE"]?>"
                         data-value-min="<?=$arItem["VALUES"]["MIN"]["HTML_VALUE"] ?: $arItem["VALUES"]["MIN"]["VALUE"]?>"
                         data-value-max="<?=$arItem["VALUES"]["MAX"]["HTML_VALUE"] ?: $arItem["VALUES"]["MAX"]["VALUE"]?>">
                    </div>
                </div>
            </div>
        </div>

        <?php endif; ?>
        <?php endforeach; ?>

        <div class="edsys-filter__actions">
            <button type="submit" class="edsys-filter__apply-btn">
                <i class="ph ph-thin ph-check"></i>
                Применить
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filter = document.querySelector('.edsys-filter');
    const form = filter.querySelector('.edsys-filter__form');
    const resetBtn = document.getElementById('edsys-filter-reset');

    // Обработка сворачивания/разворачивания групп
    filter.addEventListener('click', function(e) {
        const header = e.target.closest('.edsys-filter__group-header');
        if (!header) return;

        const content = document.querySelector(header.dataset.target);
        const icon = header.querySelector('.edsys-filter__group-icon');

        if (content.classList.contains('show')) {
            content.classList.remove('show');
            icon.style.transform = 'rotate(-90deg)';
        } else {
            content.classList.add('show');
            icon.style.transform = 'rotate(0deg)';
        }
    });

    // Обработка применения фильтра
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        applyFilter();
    });

    // Обработка изменения чекбоксов (автоприменение)
    form.addEventListener('change', function(e) {
        if (e.target.type === 'checkbox') {
            setTimeout(applyFilter, 300); // Небольшая задержка для UX
        }
    });

    // Обработка сброса фильтра
    if (resetBtn) {
        resetBtn.addEventListener('click', function() {
            // Снимаем все чекбоксы
            form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

            // Очищаем поля диапазонов
            form.querySelectorAll('.edsys-filter__range-input').forEach(input => input.value = '');

            // Применяем пустой фильтр
            applyFilter();
        });
    }

    function applyFilter() {
        const formData = new FormData(form);
        const filterData = {};

        // Собираем данные фильтра
        for (let [key, value] of formData.entries()) {
            if (key.startsWith('arrFilter_')) {
                if (!filterData[key]) {
                    filterData[key] = [];
                }
                if (Array.isArray(filterData[key])) {
                    filterData[key].push(value);
                } else {
                    filterData[key] = [filterData[key], value];
                }
            }
        }

        // Отправляем событие для обновления списка товаров
        document.dispatchEvent(new CustomEvent('filterChange', {
            detail: filterData
        }));

        // Показываем/скрываем кнопку сброса
        const hasActiveFilters = Object.keys(filterData).length > 0;
        if (resetBtn) {
            resetBtn.style.display = hasActiveFilters ? 'block' : 'none';
        }

        // Обновляем URL без перезагрузки страницы
        updateURL(filterData);
    }

    function updateURL(filterData) {
        const url = new URL(window.location);

        // Очищаем старые параметры фильтра
        for (let key of url.searchParams.keys()) {
            if (key.startsWith('arrFilter_')) {
                url.searchParams.delete(key);
            }
        }

        // Добавляем новые параметры
        for (let [key, values] of Object.entries(filterData)) {
            if (Array.isArray(values)) {
                values.forEach(value => url.searchParams.append(key, value));
            } else {
                url.searchParams.set(key, values);
            }
        }

        // Обновляем URL
        window.history.replaceState({}, '', url.toString());
    }

    // Инициализация слайдеров диапазонов
    initRangeSliders();

    function initRangeSliders() {
        const sliders = filter.querySelectorAll('.edsys-filter__range-slider');

        sliders.forEach(slider => {
            // Здесь можно подключить библиотеку для слайдеров (например, noUiSlider)
            // Или реализовать простой слайдер на CSS и JS
            console.log('Range slider initialization for:', slider);
        });
    }
});
</script>
