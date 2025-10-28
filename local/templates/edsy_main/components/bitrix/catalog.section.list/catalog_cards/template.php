<?if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
/**
 * @author KW
 * @version 1.0
 * @URI https://kowb.ru
 *
 * Template for displaying subcategories as cards.
 */
$this->setFrameMode(true);
?>

<?php if (!empty($arResult['SECTIONS'])):
    // The main CSS file of the edsy_main template already contains all the necessary styles for these classes.
    // We don't need to include a separate style.css file.
    ?>
    <div class="edsys-catalog-list__grid">
        <?php foreach ($arResult['SECTIONS'] as $arSection):
            $this->AddEditAction($arSection['ID'], $arSection['EDIT_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_EDIT"));
            $this->AddDeleteAction($arSection['ID'], $arSection['DELETE_LINK'], CIBlock::GetArrayByID($arSection["IBLOCK_ID"], "SECTION_DELETE"), array("CONFIRM" => GetMessage('CT_BCSL_ELEMENT_DELETE_CONFIRM')));
            ?>
            <div class="edsys-catalog-list__item" id="<?=$this->GetEditAreaId($arSection['ID']);?>" data-category-id="<?=$arSection['ID']?>">
                <a href="<?=$arSection['SECTION_PAGE_URL']?>" class="edsys-catalog-list__item-link">
                    <div class="edsys-catalog-list__image-wrapper">
                        <?php
                        // Using a placeholder if the image is missing
                        $pictureSrc = !empty($arSection['PICTURE']['SRC'])
                            ? $arSection['PICTURE']['SRC']
                            : '/local/templates/edsy_main/images/placeholder.jpg'; // A default placeholder image
                        ?>
                        <img src="<?=$pictureSrc?>"
                             alt="<?=htmlspecialchars($arSection['NAME'])?>"
                             class="edsys-catalog-list__image"
                             loading="lazy">
                    </div>
                    <h3 class="edsys-catalog-list__item-title"><?=htmlspecialchars($arSection['NAME'])?></h3>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>
