<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 3;
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'search(this);return false;');

$statusFld  = $frm->getField('ordles_status');
$viewFld    = $frm->getField('view');
$keywordFld = $frm->getField('keyword');
$keywordFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_COURSE_OR_QUIZ_KEYWORD'));
$langFld    = $frm->getField('ordles_tlang_id');

/* Your custom required Quiz Start Date */


/* End date */
$enddateFld = $frm->getField('ordles_lesson_endtime');
$enddateFld->addFieldtagAttribute('id', 'ordles_lesson_endtime');
$enddateFld->addFieldtagAttribute('placeholder', Label::getLabel('LBL_END_TIME'));

/* NEW: Quiz Status filter (All / Pass / Fail) */
$frm->addSelectBox(
    Label::getLabel('LBL_EXAM_STATUS'),
    'quiz_status',
    [
        ''      => Label::getLabel('LBL_ALL'),
        'pass'  => Label::getLabel('LBL_PASS'),
        'fail'  => Label::getLabel('LBL_FAIL'),
    ]
);
$quizStatusFld = $frm->getField('quiz_status');

$frm->getField('btn_clear')->addFieldTagAttribute('onClick', 'clearSearch();');
?>
<!-- [ PAGE ========= -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_MANAGE_QUIZ'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn btn--secondary slide-toggle-js  d-flex d-sm-none">
                        <svg class="icon icon--search icon--small margin-right-2">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_SEARCH'); ?>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="page__body">
        <!-- [ INFO BAR ========= -->
        <div id="upcomingLesson"></div>
        <!-- ] -->
        <!-- [ FILTERS ========= -->
        <div class="page-filter">
            <?php echo $frm->getFormTag(); ?>
            <div class="switch-controls">
                <div class="switch-controls__colum-left">
                    <!-- Lesson status switches intentionally hidden for quiz page -->
                </div>
                <div class="switch-controls__colum-right">
                    <div class="switch-ui switch-ui--icons">
                        <ul>
                            <li>
                                <label class="switch-ui__item">
                                    <input type="radio"
                                           class="switch-ui__input"
                                           onchange="search(this.form);"
                                           name="<?php echo $viewFld->getName(); ?>"
                                           value="<?php echo AppConstant::VIEW_LISTING; ?>"
                                           <?php echo ($viewFld->value == AppConstant::VIEW_LISTING) ? 'checked' : ''; ?> />
                                </label>
                            </li>
                            <!-- Calendar view disabled on quiz page -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="search-filter slide-target-js">
                <div class="row">
                    <!-- Keyword: course / quiz -->
                    <div class="col-lg-3 col-sm-12">
                        <div class="field-set">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    <?php echo $keywordFld->getCaption(); ?>
                                    <?php if ($keywordFld->requirement->isRequired()) { ?>
                                        <span class="spn_must_field">*</span>
                                    <?php } ?>
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $keywordFld->getHtml(); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                   <!-- Start date (STATIC ONLY, NO BACKEND) -->
<div class="col-lg-3 col-sm-6">
    <div class="field-set">
        <div class="caption-wraper">
            <label class="field_label">
                <?php echo Label::getLabel('LBL_EXAM_STARTDATE'); ?>
            </label>
        </div>
        <div class="field-wraper">
            <div class="field_cover">
                <input
                    type="text"
                    class="field-control"
                    placeholder="<?php echo Label::getLabel('LBL_START_DATE'); ?>"
                />
            </div>
        </div>
    </div>
</div>

<!-- End date (STATIC ONLY, NO BACKEND) -->
<div class="col-lg-3 col-sm-6">
    <div class="field-set">
        <div class="caption-wraper">
            <label class="field_label">
                <?php echo Label::getLabel('LBL_EXAM_ENDDATE'); ?>
            </label>
        </div>
        <div class="field-wraper">
            <div class="field_cover">
                <input
                    type="text"
                    class="field-control"
                    placeholder="<?php echo Label::getLabel('LBL_END_TIME'); ?>"
                />
            </div>
        </div>
    </div>
</div>


                    <!-- Quiz status + buttons -->
                    <div class="col-lg-3 col-sm-6 form-buttons-group">
                        <div class="field-set">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    <?php echo $quizStatusFld->getCaption(); ?>
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $quizStatusFld->getHtml(); ?>
                                </div>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover margin-top-3">
                                    <?php echo $frm->getFieldHtml('order_id'); ?>
                                    <?php echo $frm->getFieldHtml('pageno'); ?>
                                    <?php echo $frm->getFieldHtml('pagesize'); ?>
                                    <?php echo $frm->getFieldHtml('btn_submit'); ?>
                                    <?php echo $frm->getFieldHtml('btn_clear'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            </form>
            <?php echo $frm->getExternalJS(); ?>
        </div>
        <!-- ] ========= -->
        <!-- [ PAGE PANEL ========= -->
        <div class="page-content" id="listing"></div>
        <!-- ] -->
    </div>
    <script>
        const VIEW_CALENDAR = <?php echo AppConstant::VIEW_CALENDAR ?>;
        const VIEW_LISTING = <?php echo AppConstant::VIEW_LISTING ?>;
        $(document).ready(function () {
            // same JS hook as before – keeps your existing behaviour
            search(document.frmLessonSearch);
            upcoming();
        });
    </script>
</div>
