<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$keyword = $srchFrm->getField('keyword');
$keyword->setFieldTagAttribute('title', Label::getLabel('LBL_BY_COURSE_NAME,_TEACHER_NAME,_TAGS'));
$sorting = $srchFrm->getField('sorting');
$priceSorting = $srchFrm->getField('price_sorting');
$category = $srchFrm->getField('course_cate_id');
$level = $srchFrm->getField('course_level');
$ratings = $srchFrm->getField('course_ratings');
$language = $srchFrm->getField('course_clang_id');
$price = $srchFrm->getField('price');
$priceFrom = $srchFrm->getField('price_from');
$priceFrom->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PRICE_FROM'));
$priceFrom->setFieldTagAttribute('class', 'price-from-js');
$priceTill = $srchFrm->getField('price_till');
$priceTill->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PRICE_TILL'));
$priceTill->setFieldTagAttribute('class', 'price-till-js');
?>
<section class="section section--gray section--listing">
    <div class="section-filters">
        <div class="container container--narrow">
            <h1 class="page-title margin-bottom-8">
                <?php echo Label::getLabel('LBL_UPGRADE_YOUR_GRADES_WITH_STRUCTURED_COURSES_AND_REAL_PRACTICE'); ?>
            </h1>
            <?php echo $srchFrm->getFormTag(); ?>
            <div id="filter-panel" class="filter-panel">
                <div class="filter-panel__head">
                    <h4><?php echo Label::getLabel('LBL_FILTERS'); ?></h4>
                    <a href="javascript:closeFilter();" class="close"></a>
                </div>
                <div class="filter-panel__body">
                    <div class="filters-layout">
                        <!-- [ SEARCH FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-first basicFiltersJs">
                            <div class="filter-item">
                                <div class="filter-item__trigger">
                                    <div class="filter-item__label d-none d-sm-block">
                                        <?php echo Label::getLabel('LBL_SEARCH') ?>
                                    </div>
                                    <div class="filter-item__field">
                                        <div class="filter-item__search">
                                            <?php echo $keyword->getHtml(); ?>
                                            <div class="filter-item__search-action">
                                                <div class="filter-item__search-submit submit-keyword-js" onclick="applyFilters('keyword');">
                                                    <svg class="icon icon--search icon--small">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND ?>images/sprite.svg#search"></use>
                                                    </svg>
                                                </div>
                                                <div class="filter-item__search-reset reset-keyword-js" onclick="clearFieldFilter('keyword');" style="display: none;">
                                                    <span class="close"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ LANGUAGE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-second basicFiltersJs">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_CATEGORIES'); ?>
                                        <span class="filters-count d-sm-none catgCountJs" style="display:none;">
                                    </div>
                                    <div class="filter-item__field d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow catgPlaceholderJs">
                                            <?php echo Label::getLabel('LBL_SELECT_CATEGORY'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h5><?php echo Label::getLabel('LBL_SELECT_CATEGORY'); ?></h5>
                                                </div>
                                                <div>
                                                    <a href="javascript:void(0);" onclick="clearCategorySearch(1)" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="search-form-cover">
                                                <div class="search-form">
                                                    <div class="search-form__field">
                                                        <input type="text" name="category" onkeyup="onkeyupCategory();" placeholder="<?php echo Label::getLabel('LBL_SEARCH_CATEGORIES'); ?>">
                                                    </div>
                                                    <div class="search-form__action">
                                                        <span class="btn btn--equal btn--transparent color-black">
                                                            <svg class="icon icon--search icon--small">
                                                                <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#search"></use>
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul class="categorySelectJs">
                                                    <?php
                                                    $options = $category->options;
                                                    if (count($options) > 0) {
                                                        foreach ($options as $id => $option) { ?>
                                                            <li class="categOptParentJS">
                                                                <label class="select-option">
                                                                    <input class="select-option__input" type="checkbox" name="course_cate_id[]" <?php echo (in_array($id, $category->value)) ? "checked='checked'" : ''; ?> value="<?php echo $id; ?>">
                                                                    <span class="select-option__item categorySelectOptJs">
                                                                        <?php echo strtolower($option['name']) ?>
                                                                    </span>
                                                                </label>
                                                                <?php if (count($option['sub_categories']) > 0) { ?>
                                                                    <ul class="categOptParentJS">
                                                                        <?php foreach ($option['sub_categories'] as $sid => $name) { ?>
                                                                            <li class="categOptParentJS">
                                                                                <label class="select-option">
                                                                                    <input class="select-option__input" type="checkbox" <?php echo (in_array($sid, $category->value)) ? "checked='checked'" : ''; ?> name="course_cate_id[]" value="<?php echo $sid; ?>">
                                                                                    <span class="select-option__item categorySelectOptJs">
                                                                                        <?php echo strtolower($name) ?>
                                                                                    </span>
                                                                                </label>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                <?php } ?>
                                                            </li>
                                                    <?php }
                                                    } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:void(0);" onclick="clearCategorySearch()" class=" btn btn--gray">
                                                    <?php echo Label::getLabel('LBL_CLEAR') ?>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn--secondary margin-left-4" onclick="searchByCategory();">
                                                    <?php echo Label::getLabel('LBL_APPLY') ?>
                                                </a>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ PRICE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-third basicFiltersJs">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_PRICE'); ?>
                                        <span class="filters-count d-sm-none priceCountJs" style="display:none;">
                                    </div>
                                    <div class="filter-item__field  d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow pricePlaceholderJs">
                                            <?php echo Label::getLabel('LBL_ALL_PRICE'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h5><?php echo Label::getLabel('LBL_SELECT_PRICE'); ?></h5>
                                                </div>
                                                <div>
                                                    <a href="javascript:clearPrice(1);" class="clear-link bold-600 color-primary underline">
                                                        <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="padding-top-4 padding-bottom-4 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-6">
                                                        <?php echo $priceFrom->getHtml(); ?>
                                                    </div>
                                                    <div class="col-6">
                                                        <?php echo $priceTill->getHtml(); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul class="priceSelectJs">
                                                    <?php foreach ($price->options as $id => $name) { ?>
                                                        <li>
                                                            <label class="select-option">
                                                                <input class="select-option__input price-filter-js" type="checkbox" name="price[]" value="<?php echo $id; ?>" <?php echo in_array($id, $price->value) ? 'checked' : ''; ?> />
                                                                <span class="select-option__item"><?php echo $name; ?></span>
                                                            </label>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearPrice();" class="btn btn--gray">
                                                    <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                </a>
                                                <a href="javascript:searchByPrice();" class="btn btn--secondary margin-left-4">
                                                    <?php echo Label::getLabel('LBL_APPLY'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ RATING FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-third basicFiltersJs">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_RATING') ?>
                                        <span class="filters-count d-sm-none ratingCountJs" style="display:none;">
                                    </div>
                                    <div class="filter-item__field  d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow ratingPlaceholderJs">
                                            <?php echo Label::getLabel('LBL_ALL_RATINGS'); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div>
                                                    <h5><?php echo Label::getLabel('LBL_RATING'); ?></h5>
                                                </div>
                                                <div>
                                                    <a href="javascript:clearRating(1);" class="clear-link bold-600 color-primary underline">
                                                        <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul class="ratingSelectJs">
                                                    <?php
                                                    $options = $ratings->options;
                                                    if (count($options) > 0) {
                                                        foreach ($options as $id => $option) { ?>
                                                            <li>
                                                                <label class="select-option">
                                                                    <input class="select-option__input" type="radio" name="course_ratings" value="<?php echo $id; ?>" <?php echo $id == $ratings->value ? 'checked' : ''; ?>>
                                                                    <span class="select-option__item SelectOptJs">
                                                                        <span class="d-flex align-items-center">
                                                                            <svg class="rating__media">
                                                                                <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#rating"></use>
                                                                            </svg>
                                                                            <span><?php echo strtolower($option) ?></span>
                                                                        </span>
                                                                    </span>
                                                                </label>
                                                            </li>
                                                    <?php }
                                                    } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearRating();" class="btn btn--gray">
                                                    <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                </a>
                                                <a href="javascript:searchByRating();" class="btn btn--secondary margin-left-4">
                                                    <?php echo Label::getLabel('LBL_APPLY'); ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ MORE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-fifth moreFiltersJs">
                            <div class="filter-item">
                                <div class="filter-item__trigger cursor-pointer more-filters filter-item__trigger-js filter-more-js">
                                    <span class="filters-count filters-count--positioned moreCountJs" style="display: none;"></span>
                                    <a href="javascript:void(0)" class="btn more-filters-btn color-primary">
                                        <svg class="icon icon--more icon--small margin-right-2" viewBox="0 0 14 14.003">
                                            <path d="M2.919 11.202a2.1 2.1 0 013.962 0H14v1.4H6.881a2.1 2.1 0 01-3.962 0H0v-1.4zm4.2-4.9a2.1 2.1 0 013.962 0H14v1.4h-2.919a2.1 2.1 0 01-3.962 0H0v-1.4zm-4.2-4.9a2.1 2.1 0 013.962 0H14v1.4H6.881a2.1 2.1 0 01-3.962 0H0v-1.4z"></path>
                                        </svg>
                                        <?php echo Label::getLabel('LBL_MORE_FILTERS'); ?>
                                    </a>
                                </div>
                                <div class="filter-item__target filter-item__target-js more-filters-target moreFiltersPanelJs" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__body">
                                            <div class="filters-more maga-body-js">
                                                <!-- [ COURSE LEVEL FILTER ========= -->
                                                <div class="filter-item">
                                                    <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                        <div class="filter-item__label">
                                                            <?php echo Label::getLabel('LBL_COURSE_LEVELS'); ?>
                                                            <span class="filters-count levelCountJs" style="display:none;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                        <div class="filter-dropdown">
                                                            <div class="filter-dropdown__head">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <h5>
                                                                            <?php echo Label::getLabel('LBL_COURSE_LEVEL'); ?>
                                                                        </h5>
                                                                    </div>
                                                                    <div>
                                                                        <a href="javascript:clearLevelFilters()" class="clear-link bold-600 color-primary underline">
                                                                            <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="filter-dropdown__body">
                                                                <div class="search-form-cover d-block d-sm-none">
                                                                    <div class="search-form">
                                                                        <div class="search-form__field">
                                                                            <input type="text" name="course_levels" placeholder="Search Location" onkeyup="onkeyupLevels();">
                                                                        </div>
                                                                        <div class="search-form__action">
                                                                            <span class="btn btn--equal btn--transparent color-black">
                                                                                <svg class="icon icon--search icon--small">
                                                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#search"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="select-list select-list--inline">
                                                                    <ul class="levelFiltersJs">
                                                                        <?php
                                                                        $options = $level->options;
                                                                        if (count($options) > 0) {
                                                                            foreach ($options as $id => $option) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input SelectOptJs" type="checkbox" name="course_level[]" value="<?php echo $id; ?>" <?php echo in_array($id, $level->value) ? 'checked' : ''; ?>>
                                                                                        <span class="select-option__item levelSelectOptJs">
                                                                                            <?php echo strtolower($option) ?>
                                                                                        </span>
                                                                                    </label>
                                                                                </li>
                                                                        <?php }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ] -->
                                                <!-- [ LANGUAGE FILTER ========= -->
                                                <div class="filter-item">
                                                    <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                        <div class="filter-item__label">
                                                            <?php echo Label::getLabel('LBL_LANGUAGES'); ?>
                                                            <span class="filters-count langCountJs" style="display:none;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                        <div class="filter-dropdown">
                                                            <div class="filter-dropdown__head">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div>
                                                                        <h5>
                                                                            <?php echo Label::getLabel('LBL_LANGUAGE'); ?>
                                                                        </h5>
                                                                    </div>
                                                                    <div>
                                                                        <a href="javascript:clearLangSearch()" class="clear-link bold-600 color-primary underline">
                                                                            <?php echo Label::getLabel('LBL_CLEAR'); ?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="filter-dropdown__body">
                                                                <div class="search-form-cover d-block d-sm-none">
                                                                    <div class="search-form">
                                                                        <div class="search-form__field">
                                                                            <input type="text" name="course_languages" placeholder="Search Location" onkeyup="onkeyupLangs();">
                                                                        </div>
                                                                        <div class="search-form__action">
                                                                            <span class="btn btn--equal btn--transparent color-black">
                                                                                <svg class="icon icon--search icon--small">
                                                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#search"></use>
                                                                                </svg>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div class="select-list select-list--inline">
                                                                    <ul class="langFiltersJs">
                                                                        <?php
                                                                        $options = $language->options;
                                                                        if (count($options) > 0) {
                                                                            foreach ($options as $id => $option) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input" type="checkbox" name="course_clang_id[]" value="<?php echo $id; ?>" <?php echo in_array($id, $language->value) ? 'checked' : ''; ?>>
                                                                                        <span class="select-option__item langSelectOptJs">
                                                                                            <?php echo strtolower($option) ?>
                                                                                        </span>
                                                                                    </label>
                                                                                </li>
                                                                        <?php }
                                                                        } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ] -->
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearAllFiltersWeb()" class="btn btn--gray">
                                                    <?php echo Label::getLabel('LBL_CLEAR_ALL') ?>
                                                </a>
                                                <a href="javascript:void(0);" onclick="applyMoreFilters()" class="btn btn--secondary margin-left-4">
                                                    <?php echo Label::getLabel('LBL_APPLY') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                    </div>
                </div>
                <div class="filter-panel__footer d-block d-sm-none">
                    <div class="filter-actions">
                        <a href="javascript:clearAllFiltersMobile();" class="btn btn--gray">
                            <?php echo Label::getLabel('LBL_CLEAR_ALL'); ?>
                        </a>
                        <a href="javascript:void(0);" onclick="applyMoreFilters()" class="btn btn--secondary margin-left-4">
                            <?php echo Label::getLabel('LBL_APPLY'); ?>
                        </a>
                    </div>
                </div>
            </div>
            <input type="hidden" name="price_sorting" value="<?php echo $priceSorting->value; ?>" />
            <?php echo $srchFrm->getFieldHtml('record_id'); ?>
            <?php echo $srchFrm->getFieldHtml('type'); ?>
            <?php echo $srchFrm->getFieldHtml('search_keyword'); ?>
            <?php echo $srchFrm->getFieldHtml('pageno'); ?>
            </form>
        </div>
    </div>
    <div class="container container--narrow">
        <div class="page-listing" id="listing">
        </div>
    </div>
</section>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" />
<script src="//cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<script>
    var categoryLbl = "<?php echo Label::getLabel('LBL_SELECT_CATEGORY'); ?>";
    var priceLbl = "<?php echo Label::getLabel('LBL_ALL_PRICE'); ?>";
    var ratingLbl = "<?php echo Label::getLabel('LBL_ALL_RATINGS'); ?>";
    $(document).ready(function() {
        var catid = "<?php echo count($category->value) ?>";
        if (catid > 0) {
            searchByCategory(true);
        }
    });
</script>