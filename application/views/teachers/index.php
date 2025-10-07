<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$keyword = $srchFrm->getField('keyword');
$teachs = $srchFrm->getField('teachs');
$price = $srchFrm->getField('price');
$days = $srchFrm->getField('days');
$slots = $srchFrm->getField('slots');
$gender = $srchFrm->getField('gender');
$locations = $srchFrm->getField('locations');
$speaks = $srchFrm->getField('speaks');
$accents = $srchFrm->getField('accents');
$levels = $srchFrm->getField('levels');
$subjects = $srchFrm->getField('subjects');
$lessonType = $srchFrm->getField('lesson_type');
$tests = $srchFrm->getField('tests');
$ageGroup = $srchFrm->getField('age_group');
$sorting = $srchFrm->getField('sorting');
$pageno = $srchFrm->getField('pageno');
$priceFrom = $srchFrm->getField('price_from');
$priceTill = $srchFrm->getField('price_till');
$jslabels = json_encode([
    'allLanguages' => Label::getLabel('LBL_ALL_LANGUAGES'),
    'selectTiming' => Label::getLabel('LBL_SELECT_TIMING'),
    'allPrices' => Label::getLabel('LBL_ALL_PRICES'),
        ]);
?>
<script> LABELS = <?php echo $jslabels; ?>;</script> 
<section class="section section--gray section--listing">
    <div class="section-filters">
        <?php echo $srchFrm->getFormTag(); ?>
        <div class="container container--narrow">
            <h1 class="page-title margin-bottom-8"><?php echo Label::getLabel('LBL_TEACHER_SEARCH_HEADLINE'); ?></h1>
            <div id="filter-panel" class="filter-panel">
                <div class="filter-panel__head">
                    <h4><?php echo Label::getLabel('LBL_FILTERS'); ?></h4>
                    <a href="javascript:closeFilter();" class="close"></a>
                </div>
                <div class="filter-panel__body">
                    <div class="filters-layout">
                        <!-- [ SEARCH FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-first">
                            <div class="filter-item">
                                <div class="filter-item__trigger">
                                    <div class="filter-item__label d-none d-sm-block"><?php echo Label::getLabel('LBL_SEARCH'); ?></div>
                                    <div class="filter-item__field">
                                        <div class="filter-item__search">
                                            <?php echo $keyword->getHtml(); ?>
                                            <div class="filter-item__search-action" >
                                                <a class="filter-item__search-submit" onclick="searchKeyword();" title="<?php echo Label::getLabel('LBL_SEARCH'); ?>">
                                                    <svg class="icon icon--search icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#search'; ?>"></use></svg>
                                                </a>
                                                <div class="filter-item__search-reset" onclick="clearKeyword();" style="display: none;" title="<?php echo Label::getLabel('LBL_RESET'); ?>">
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
                        <div class="filters-layout__item filters-layout__item-second">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_LANGUAGE'); ?>
                                        <span class="filters-count d-sm-none language-count-js" style="display: none;"></span>
                                    </div>
                                    <div class="filter-item__field d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow teachlang-placeholder-js"><?php echo Label::getLabel('LBL_ALL_LANGUAGE'); ?></div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div><h5><?php echo Label::getLabel('LBL_SELECT_LANGUAGE'); ?></h5></div>
                                                <div><a href="javascript:clearMore('teachs[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="search-form-cover">
                                                <div class="search-form">
                                                    <div class="search-form__field">
                                                        <input type="text" name="teach_language" onkeyup="onkeyupLanguage()" placeholder="<?php echo Label::getLabel('LBL_SEARCH_LANGUAGE'); ?>" />
                                                    </div>
                                                    <div class="search-form__action">
                                                        <span class="btn btn--equal btn--transparent color-black">
                                                            <svg class="icon icon--search icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#search'; ?>"></use></svg>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul>
                                                    <?php foreach ($teachs->options as $id => $name) { ?>
                                                        <li>
                                                            <label class="select-option">
                                                                <input class="select-option__input language-filter-js" type="checkbox" name="teachs[]" value="<?php echo $id; ?>" <?php echo in_array($id, $teachs->value) ? 'checked' : ''; ?> />
                                                                <span class="select-option__item select-teachlang-js"><?php echo strtolower($name); ?></span>
                                                            </label>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearLanguage();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                <a href="javascript:searchLanguage();" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ PRICE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-third">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_PRICE'); ?>
                                        <span class="filters-count d-sm-none price-count-js" style="display: none;"></span>
                                    </div>
                                    <div class="filter-item__field  d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow price-placeholder-js"><?php echo Label::getLabel('LBL_ALL_PRICES'); ?></div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div><h5><?php echo Label::getLabel('LBL_SELECT_PRICE'); ?></h5></div>
                                                <div><a href="javascript:clearMore('price[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="padding-top-4 padding-bottom-4 border-bottom">
                                                <div class="d-flex justify-content-between">
                                                    <div class="col-6"><?php echo $priceFrom->getHtml(); ?></div>
                                                    <div class="col-6"><?php echo $priceTill->getHtml(); ?></div>
                                                </div>    
                                            </div>
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul>

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
                                                <a href="javascript:clearPrice();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                <a href="javascript:searchPrice();" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ AVAILABILITY FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-forth">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_AVAILABILITY'); ?>
                                        <span class="filters-count d-sm-none availbility-count-js" style="display: none;"></span>
                                    </div>
                                    <div class="filter-item__field d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow availbility-placeholder-js"><?php echo Label::getLabel('LBL_SELECT_TIMING') ?></div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div><h5><?php echo Label::getLabel('LBL_SELECT_AVAILBILITY'); ?></h5></div>
                                                <div><a href="javascript:clearMore('slots[]');clearMore('days[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="selection-group">
                                                <h6 class="margin-bottom-3"><?php echo Label::getLabel('LBL_DAYS_OF_THE_WEEK'); ?></h6>
                                                <div class="select-list select-list--flex">
                                                    <ul>
                                                        <?php foreach ($days->options as $id => $name) { ?>
                                                            <li>
                                                                <label class="select-option">
                                                                    <input class="select-option__input availbility-filter-js" type="checkbox" name="days[]" value="<?php echo $id; ?>" <?php echo in_array($id, $days->value) ? 'checked' : ''; ?> />
                                                                    <span class="select-option__item"><?php echo $name; ?></span>
                                                                </label>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                            <div class="selection-group">
                                                <h6 class="margin-bottom-3"><?php echo Label::getLabel('LBL_TIMES_OF_DAY_24_HOURS'); ?></h6>
                                                <div class="select-list select-list--onethird">
                                                    <ul>
                                                        <?php foreach ($slots->options as $id => $name) { ?>
                                                            <li>
                                                                <label class="select-option">
                                                                    <input class="select-option__input availbility-filter-js" type="checkbox" name="slots[]" value="<?php echo $id; ?>" <?php echo in_array($id, $slots->value) ? 'checked' : ''; ?> />
                                                                    <span class="select-option__item"><?php echo $name; ?></span>
                                                                </label>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearAvailbility();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                <a href="javascript:searchAvailbility();" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ MORE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-fifth">
                            <div class="filter-item">
                                <div class="filter-item__trigger cursor-pointer more-filters filter-item__trigger-js filter-more-js">
                                    <span class="filters-count filters-count--positioned more-count-js" style="display: none;"></span>
                                    <a href="javascript:void(0)" class="btn more-filters-btn color-primary">
                                        <svg class="icon icon--more icon--small margin-right-2" viewBox="0 0 14 14.003">
                                        <path d="M2.919 11.202a2.1 2.1 0 013.962 0H14v1.4H6.881a2.1 2.1 0 01-3.962 0H0v-1.4zm4.2-4.9a2.1 2.1 0 013.962 0H14v1.4h-2.919a2.1 2.1 0 01-3.962 0H0v-1.4zm-4.2-4.9a2.1 2.1 0 013.962 0H14v1.4H6.881a2.1 2.1 0 01-3.962 0H0v-1.4z"></path>
                                        </svg>
                                        <?php echo Label::getLabel('LBL_MORE_FILTERS'); ?>                                        
                                    </a>
                                </div>
                                <div class="filter-item__target filter-item__target-js more-filters-target" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__body">
                                            <div class="filters-more maga-body-js">
                                                <!-- [ COUNTRIES FILTER ========= -->
                                                <div class="filter-item">
                                                    <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                        <div class="filter-item__label">
                                                            <?php echo Label::getLabel('LBL_LOCATION'); ?>
                                                            <span class="filters-count country-count-js" style="display: none;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                        <div class="filter-dropdown">
                                                            <div class="filter-dropdown__head">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div><h5><?php echo Label::getLabel('LBL_SELECT_LOCATION'); ?></h5></div>
                                                                    <div><a href="javascript:clearMore('locations[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                </div>

                                                                <div class="search-form-cover d-block padding-0 border-bottom-0 margin-top-6">
                                                                    <div class="search-form">
                                                                        <div class="search-form__field">
                                                                            <input type="text" name="location_search" onkeyup="onkeyupLocation()" placeholder="<?php echo Label::getLabel('LBL_SEARCH_LOCATION'); ?>" />
                                                                        </div>
                                                                        <div class="search-form__action">
                                                                            <span class="btn btn--equal btn--transparent color-black">
                                                                                <svg class="icon icon--search icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#search'; ?>"></use></svg>
                                                                            </span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                            </div>
                                                            <div class="filter-dropdown__body">

                                                                <div class="select-list select-list--inline">
                                                                    <ul>
                                                                        <?php foreach ($locations->options as $id => $name) { ?>
                                                                            <li>
                                                                                <label class="select-option">
                                                                                    <input class="select-option__input country-filter-js" type="checkbox" name="locations[]" value="<?php echo $id; ?>" <?php echo in_array($id, $locations->value) ? 'checked' : ''; ?> />
                                                                                    <span class="select-option__item select-location-js"><?php echo strtolower($name); ?></span>
                                                                                </label>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ] -->
                                                <!-- [ GENDER FILTER ========= -->
                                                <div class="filter-item">
                                                    <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                        <div class="filter-item__label">
                                                            <?php echo Label::getLabel('LBL_TEACHER_GENDER'); ?>
                                                            <span class="filters-count gender-count-js" style="display: none;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                        <div class="filter-dropdown">
                                                            <div class="filter-dropdown__head">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div><h5><?php echo Label::getLabel('LBL_SELECT_GENDER'); ?></h5></div>
                                                                    <div><a href="javascript:clearMore('gender[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                </div>
                                                            </div>
                                                            <div class="filter-dropdown__body">
                                                                <div class="select-list select-list--inline">
                                                                    <ul>
                                                                        <?php foreach ($gender->options as $id => $name) { ?>
                                                                            <li>
                                                                                <label class="select-option">
                                                                                    <input class="select-option__input gender-filter-js" type="checkbox" name="gender[]" value="<?php echo $id; ?>" <?php echo in_array($id, $gender->value) ? 'checked' : ''; ?> />
                                                                                    <span class="select-option__item"><?php echo $name; ?></span>
                                                                                </label>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ] -->
                                                <!-- [ SPEAKS FILTER ========= -->
                                                <div class="filter-item">
                                                    <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                        <div class="filter-item__label">
                                                            <?php echo Label::getLabel('LBL_TEACHER_SPEAKS'); ?>
                                                            <span class="filters-count speak-count-js" style="display: none;"></span>
                                                        </div>
                                                    </div>
                                                    <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                        <div class="filter-dropdown">
                                                            <div class="filter-dropdown__head">
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <div><h5><?php echo Label::getLabel('LBL_SELECT_SPEAKS'); ?></h5></div>
                                                                    <div><a href="javascript:clearMore('speaks[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                </div>
                                                            </div>
                                                            <div class="filter-dropdown__body">
                                                                <div class="select-list select-list--inline">
                                                                    <ul>
                                                                        <?php foreach ($speaks->options as $id => $name) { ?>
                                                                            <li>
                                                                                <label class="select-option">
                                                                                    <input class="select-option__input speak-filter-js" type="checkbox" name="speaks[]" value="<?php echo $id; ?>" <?php echo in_array($id, $speaks->value) ? 'checked' : ''; ?> />
                                                                                    <span class="select-option__item"><?php echo $name; ?></span>
                                                                                </label>
                                                                            </li>
                                                                        <?php } ?>
                                                                    </ul>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- ] -->
                                                <!-- [ ACCENTS FILTER ========= -->
                                                <?php if (!empty($accents->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_TEACHER_ACCENTS'); ?>
                                                                <span class="filters-count accent-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_ACCENTS'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('accents[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($accents->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input accent-filter-js" type="checkbox" name="accents[]" value="<?php echo $id; ?>" <?php echo in_array($id, $accents->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                                <!-- [ LEVEL FILTER ========= -->
                                                <?php if (!empty($levels->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_TEACHES_LEVEL'); ?>
                                                                <span class="filters-count level-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_LEVELS'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('levels[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($levels->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input level-filter-js" type="checkbox" name="levels[]" value="<?php echo $id; ?>" <?php echo in_array($id, $levels->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                                <!-- [ SUBJECTS FILTER ========= -->
                                                <?php if (!empty($subjects->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_SUBJECTS'); ?>
                                                                <span class="filters-count subject-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js"  style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_SUBJECTS'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('subjects[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($subjects->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input subject-filter-js" type="checkbox" name="subjects[]" value="<?php echo $id; ?>" <?php echo in_array($id, $subjects->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                                <!-- [ LESSONS INCLUDES FILTER ========= -->
                                                <?php if (!empty($lessonType->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_LESSON_INCLUDES'); ?>
                                                                <span class="filters-count include-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_LESSON_INCLUDES'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('lesson_type[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($lessonType->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input include-filter-js" type="checkbox" name="lesson_type[]" value="<?php echo $id; ?>" <?php echo in_array($id, $lessonType->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                                <!-- [ PREPARATIONS FILTER ========= -->
                                                <?php if (!empty($tests->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_TEST_PREPARATIONS'); ?>
                                                                <span class="filters-count test-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_TEST_PREPARATIONS'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('tests[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($tests->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input test-filter-js" type="checkbox" name="tests[]" value="<?php echo $id; ?>" <?php echo in_array($id, $tests->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                                <!-- [ AGE GROUP FILTER ========= -->
                                                <?php if (!empty($ageGroup->options)) { ?>
                                                    <div class="filter-item">
                                                        <div class="filter-item__trigger cursor-pointer filter-item__trigger-js">
                                                            <div class="filter-item__label">
                                                                <?php echo Label::getLabel('LBL_LEARNER_AGE_GROUP'); ?>
                                                                <span class="filters-count age-group-count-js" style="display: none;"></span>
                                                            </div>
                                                        </div>
                                                        <div class="filter-item__target filter-item__target-js" style="display: none;">
                                                            <div class="filter-dropdown">
                                                                <div class="filter-dropdown__head">
                                                                    <div class="d-flex align-items-center justify-content-between">
                                                                        <div><h5><?php echo Label::getLabel('LBL_SELECT_AGE_GROUP'); ?></h5></div>
                                                                        <div><a href="javascript:clearMore('age_group[]');"  class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                                                    </div>
                                                                </div>
                                                                <div class="filter-dropdown__body">
                                                                    <div class="select-list select-list--inline">
                                                                        <ul>
                                                                            <?php foreach ($ageGroup->options as $id => $name) { ?>
                                                                                <li>
                                                                                    <label class="select-option">
                                                                                        <input class="select-option__input age-group-filter-js" type="checkbox" name="age_group[]" value="<?php echo $id; ?>" <?php echo in_array($id, $ageGroup->value) ? 'checked' : ''; ?> />
                                                                                        <span class="select-option__item"><?php echo $name; ?></span>
                                                                                    </label>
                                                                                </li>
                                                                            <?php } ?>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <?php } ?>
                                                <!-- ] -->
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearAllDesktop();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR_ALL'); ?></a>
                                                <a href="javascript:searchMore(document.frmSearch);" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
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
                        <a href="javascript:clearAllMobile();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR_ALL'); ?></a>
                        <a href="javascript:searchMore(document.frmSearch);" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                    </div>
                </div>

            </div>
        </div>
        <input type="text" name="sorting" value="<?php echo $sorting->value; ?>" style="display: none;" />
        <input type="text" name="pageno" value="<?php echo $pageno->value; ?>" style="display: none;" />
        </form>
    </div>
    <div class="container container--narrow">
        <div class="page-listing" id="listing"></div>
    </div>
</section>