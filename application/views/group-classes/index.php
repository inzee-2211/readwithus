<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$keyword = $srchFrm->getField('keyword');
$language = $srchFrm->getField('language');
$classtype = $srchFrm->getField('classtype');
$duration = $srchFrm->getField('duration');
$pageno = $srchFrm->getField('pageno');
$jslabels = json_encode([
    'allLanguages' => Label::getLabel('LBL_ALL_LANGUAGES'),
    'allClassTypes' => Label::getLabel('LBL_All_CLASS_TYPES'),
    'allDurations' => Label::getLabel('LBL_All_DURATIONS')
        ]);
?>
<script> LABELS = <?php echo $jslabels; ?>;</script> 
<section class="section section--gray section--listing">
    <div class="section-filters">
        <?php echo $srchFrm->getFormTag(); ?>
        <div class="container container--narrow">
            <h1 class="page-title margin-bottom-8"><?php echo Label::getLabel('LBL_CLASS_SEARCH_HEADLINE'); ?></h1>
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
                                                <div><a href="javascript:clearMore('language[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
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
                                                    <?php foreach ($language->options as $id => $name) { ?>
                                                        <li>
                                                            <label class="select-option">
                                                                <input class="select-option__input language-filter-js" type="checkbox" name="language[]" value="<?php echo $id; ?>" <?php echo in_array($id, $language->value) ? 'checked' : ''; ?> />
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
                        <!-- [ TYPE FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-third">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_CLASS_TYPE'); ?>
                                        <span class="filters-count d-sm-none classtype-count-js" style="display: none;"></span>
                                    </div>
                                    <div class="filter-item__field  d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow classtype-placeholder-js"><?php echo Label::getLabel('LBL_ALL_TYPES'); ?></div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div><h5><?php echo Label::getLabel('LBL_SELECT_TYPE'); ?></h5></div>
                                                <div><a href="javascript:clearMore('classtype[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul>
                                                    <?php foreach ($classtype->options as $id => $name) { ?>
                                                        <li>
                                                            <label class="select-option">
                                                                <input class="select-option__input classtype-filter-js" type="checkbox" name="classtype[]" value="<?php echo $id; ?>" <?php echo in_array($id, $classtype->value) ? 'checked' : ''; ?> />
                                                                <span class="select-option__item"><?php echo $name; ?></span>
                                                            </label>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearClasstype();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                <a href="javascript:searchClasstype();" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                        <!-- [ DURATION FILTER ========= -->
                        <div class="filters-layout__item filters-layout__item-third">
                            <div class="filter-item">
                                <div class="filter-item__trigger filter-item__trigger-js  cursor-pointer">
                                    <div class="filter-item__label">
                                        <?php echo Label::getLabel('LBL_CLASS_DURATION'); ?>
                                        <span class="filters-count d-sm-none duration-count-js" style="display: none;"></span>
                                    </div>
                                    <div class="filter-item__field  d-none d-sm-block">
                                        <div class="filter-item__select filter-item__select--arrow duration-placeholder-js"><?php echo Label::getLabel('LBL_ALL_DURATIONS'); ?></div>
                                    </div>
                                </div>
                                <div class="filter-item__target filter-item__target-js" style="display: none;">
                                    <div class="filter-dropdown">
                                        <div class="filter-dropdown__head d-block d-sm-none">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div><h5><?php echo Label::getLabel('LBL_SELECT_DURATION'); ?></h5></div>
                                                <div><a href="javascript:clearMore('duration[]');" class="clear-link bold-600 color-primary underline"><?php echo Label::getLabel('LBL_CLEAR'); ?></a></div>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__body">
                                            <div class="select-list select-list--vertical select-list--scroll">
                                                <ul>
                                                    <?php foreach ($duration->options as $id => $name) { ?>
                                                        <li>
                                                            <label class="select-option">
                                                                <input class="select-option__input duration-filter-js" type="checkbox" name="duration[]" value="<?php echo $id; ?>" <?php echo in_array($id, $duration->value) ? 'checked' : ''; ?> />
                                                                <span class="select-option__item"><?php echo $name; ?></span>
                                                            </label>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                        <div class="filter-dropdown__footer d-none d-sm-block">
                                            <div class="filter-actions">
                                                <a href="javascript:clearDuration();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR'); ?></a>
                                                <a href="javascript:searchDuration();" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                    </div>
                </div>
                <div class="filter-panel__footer d-sm-none d-block">
                    <div class="filter-actions">
                        <a href="javascript:clearAllMobile();" class="btn btn--gray"><?php echo Label::getLabel('LBL_CLEAR_ALL'); ?></a>
                        <a href="javascript:search(document.frmSearch);" class="btn btn--secondary margin-left-4"><?php echo Label::getLabel('LBL_APPLY'); ?></a>
                    </div>
                </div>
            </div>
        </div>
        <input type="text" name="pageno" value="<?php echo $pageno->value; ?>" style="display: none;" />
        </form>
    </div>
    <div class="container container--narrow">
        <div class="page-listing" id="listing"></div>
    </div>
</section>
<!-- ] -->
<script>
    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script>