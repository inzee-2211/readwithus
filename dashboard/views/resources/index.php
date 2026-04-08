<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$keywordFld = $frm->getField('keyword');
$keywordFld->setFieldTagAttribute('id', 'planKeyword');
$keywordFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_KEYWORD'));
$levelFld = $frm->getField('plan_level');
$btnReset = $frm->getField('btn_reset');
$btnReset->addFieldTagAttribute('onclick', 'clearSearch()');
?>
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-7">
                <h1><?php echo Label::getLabel('LBL_MANAGE_COURSE_RESOURCES'); ?></h1>
                <p class="margin-0">
                    <?php echo Label::getLabel('LBL_MANAGE_COURSE_RESOURCES_PAGE_SUB_HEADING'); ?>
                </p>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn bg-secondary slide-toggle-js">
                        <svg class="icon icon--clock icon--small margin-right-2">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#search"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_SEARCH'); ?>
                    </a>
                    <a href="javascript:void(0)" onclick="form();" class="btn color-secondary btn--bordered margin-left-3">
                        <svg class="icon icon--uploader margin-right-2">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#uploader"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_BULK_UPLOADER'); ?>
                    </a>
                </div>
            </div>
        </div>
        <!-- [ FILTERS ========= -->
        <div class="search-filter slide-target-js" style="display: none;">
            <?php echo $frm->getFormTag(); ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label">
                                <?php echo $keywordFld->getCaption(); ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php echo $keywordFld->getHtml(); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="field-set">
                        <div class="caption-wraper">
                            <label class="field_label"></label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <?php
                                echo $frm->getFieldHtml('btn_submit');
                                echo $frm->getFieldHtml('btn_reset');
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo $frm->getFieldHtml('page'); ?>
            <?php echo $frm->getFieldHtml('pagesize'); ?>
            </form>
            <?php echo $frm->getExternalJS(); ?>
        </div>
        <!-- ] ========= -->
    </div>
    <div class="page__body">
        <div class="page-content">
            <div class="table-scroll" id="listing">
            </div>
        </div>
    </div>
