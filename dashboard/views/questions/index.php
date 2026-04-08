<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'search(this);return false;');

if ($siteUserType == User::TEACHER) {
    $statusFld = $frm->getField('grpcls_status');
} elseif ($siteUserType == User::LEARNER) {
    $statusFld = $frm->getField('ordcls_status');
}

$viewFld = $frm->getField('view');
$keywordFld = $frm->getField('title');
$keywordFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_TITLE'));
$durationFld = $frm->getField('grpcls_duration');
$langFld = $frm->getField('grpcls_tlang_id');

$startdateFld = $frm->getField('grpcls_q_cat');
$startdateFld->setFieldTagAttribute('onchange', 'getSubCategoriessearch(this.value)');

$enddateFld = $frm->getField('grpcls_subcate_id');
$enddateFld->setFieldTagAttribute('id', 'subCategoriesSearch');

$frm->getField('btn_clear')->addFieldTagAttribute('onClick', 'clearSearch();');
?>
<script>
    const VIEW_CALENDAR = <?php echo AppConstant::VIEW_CALENDAR ?>;
    const VIEW_LISTING = <?php echo AppConstant::VIEW_LISTING ?>;
</script>
<!-- [ PAGE ========= -->
<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1>Questions</h1>
            </div>
            <div class="col-sm-auto">
                <div class="buttons-group d-flex align-items-center">
                    <a href="javascript:void(0)" class="btn btn--secondary slide-toggle-js">
                        <svg class="icon icon--search icon--small margin-right-2">
                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#search'; ?>"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_SEARCH'); ?>
                    </a>
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <a href="javascript:void(0);" onclick="openQuestionAddChooser();" class="btn color-secondary btn--bordered margin-left-4">
                            <svg class="icon icon--add icon--small margin-right-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24">
                                <path d="M11 11V7h2v4h4v2h-4v4h-2v-4H7v-2h4zm1 11C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"></path>
                            </svg>
                            Add Question
                        </a>

                        <!-- Bulk Delete button (new) -->
                        <button type="button"
                                class="btn btn--bordered color-third margin-left-4"
                                onclick="bulkDeleteSelected();">
                            <?php echo Label::getLabel('LBL_BULK_DELETE'); ?>
                        </button>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
    <div class="page__body">
        <?php
        if (!empty($upcomingClass)) {
            $classId = ($siteUserType == User::LEARNER) ? $upcomingClass['ordcls_id'] : $upcomingClass['grpcls_id'];
            ?>
            <!-- [ INFO BAR ========= -->
            <div class="infobar infobar--primary">
                <div class="row justify-content-between align-items-center">
                    <div class="col-lg-8 col-sm-6">
                        <div class="d-flex align-items-lg-center">
                            <div class="infobar__media margin-right-5">
                                <div class="infobar__media-icon infobar__media-icon--vcamera ">
                                    <svg class="icon icon--vcamera">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#video-camera'; ?>"></use>
                                    </svg>
                                </div>
                            </div>
                            <div class="infobar__content">
                                <div class="upcoming-lesson display-inline">
                                    <?php echo Label::getLabel('LBL_NEXT_CLASS:'); ?>
                                    <date class=" bold-600">
                                        <?php echo date('Y-m-d', $upcomingClass['grpcls_starttime_unix']); ?>
                                    </date>
                                    <?php echo Label::getLabel('LBL_AT'); ?>
                                    <time class=". bold-600">
                                        <?php echo date('H:i', $upcomingClass['grpcls_starttime_unix']); ?>
                                    </time>
                                    <?php if ($siteUserType == User::LEARNER) { ?>
                                        <?php echo Label::getLabel('LBL_WITH'); ?>
                                        <div class="avtar-meta display-inline">
                                            <span class="avtar avtar--xsmall display-inline margin-right-2" data-title="<?php echo CommonHelper::getFirstChar($upcomingClass['teacher_first_name']); ?>">
                                                <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $upcomingClass['grpcls_teacher_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>" />
                                            </span>
                                            <?php echo $upcomingClass['teacher_first_name'] . ' ' . $upcomingClass['teacher_last_name']; ?>
                                        </div>
                                        <?php
                                    } else {
                                        echo "(" . $upcomingClass['grpcls_title'] . ")";
                                    }
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-4 col-sm-6">
                        <div class="upcoming-lesson-action d-flex align-items-center justify-content-between justify-content-sm-end">
                            <div class="timer margin-right-4">
                                <div class="timer__media">
                                    <span>
                                        <svg class="icon icon--clock icon--small">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#clock'; ?>"></use>
                                        </svg>
                                    </span>
                                </div>
                                <div class="timer__content">
                                    <div class="timer__controls timer-js style colorDefinition size_sm" id="classStartTimer" timestamp="<?php echo $upcomingClass['grpcls_start_datetime_utc']; ?>">00:00:00:00</div>
                                </div>
                            </div>
                            <a href="<?php echo MyUtility::makeUrl('Classes', 'view', [$classId]); ?>" class="btn bg-secondary">
                                <?php echo Label::getLabel('LBL_ENTER_CLASSROOM') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ] -->
        <?php } ?>
        <!-- [ FILTERS ========= -->
        <div class="page-filter">
            <?php echo $frm->getFormTag(); ?>

            <div class="search-filter slide-target-js">
                <div class="row">
                    <div class="col-lg-3 col-sm-8">
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
                    <div class="col-lg-2 col-sm-4">
                        <div class="field-set">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    <?php echo $langFld->getCaption(); ?>
                                    <?php if ($langFld->requirement->isRequired()) { ?>
                                        <span class="spn_must_field">*</span>
                                    <?php } ?>
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $langFld->getHtml(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-4">
                        <div class="field-set">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    <?php echo $startdateFld->getCaption(); ?>
                                    <?php if ($startdateFld->requirement->isRequired()) { ?>
                                        <span class="spn_must_field">*</span>
                                    <?php } ?>
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $startdateFld->getHtml(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-2 col-sm-4">
                        <div class="field-set">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    <?php echo $enddateFld->getCaption(); ?>
                                    <?php if ($enddateFld->requirement->isRequired()) { ?>
                                        <span class="spn_must_field">*</span>
                                    <?php } ?>
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $enddateFld->getHtml(); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-sm-4  form-buttons-group">
                        <div class="field-set">
                            <div class="caption-wraper"><label class="field_label"></label></div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $frm->getFieldHtml('pageno'); ?>
                                    <?php echo $frm->getFieldHtml('pagesize'); ?>
                                    <?php echo $frm->getFieldHtml('package_id'); ?>
                                    <?php echo $frm->getFieldHtml('ordcls_id'); ?>
                                    <?php echo $frm->getFieldHtml('grpcls_id'); ?>
                                    <?php echo $frm->getFieldHtml('order_id'); ?>
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
        $(document).ready(function () {
            search(document.frmClassSearch);
            <?php if (!empty($upcomingClass)) { ?>
            $("#classStartTimer").yocoachTimer({recordId: '<?php echo $classId; ?>', recordType: 'CLASS'});
            <?php } ?>
        });
    </script>
        <script>
        function openQuestionAddChooser() {
            var html = `
  <div class="facebox-panel" style="max-width:300px; padding:12px 12px 8px; margin:0 auto;">

    <div class="facebox-panel__head"><h4>Add Questions</h4></div>
    <div class="facebox-panel__body">
      <div class="row">
        <div class="col-md-6">
          <div class="box box--white text-center" style="padding:10px;">
            <h5>Single</h5>
            <p>Add one question at a time.</p>
            <a href="javascript:void(0)" class="btn btn--secondary" onclick="chooseSingle();">Use Single Form</a>

          </div>
        </div>
        <div class="col-md-6">
          <div class="box box--white text-center" style="padding:10px;">
            <h5>Bulk (CSV)</h5>
            <p>Import multiple questions.</p>
            <a href="javascript:void(0)" class="btn btn--secondary" onclick="openBulkForm();">Open Bulk Form</a>
          </div>
        </div>
      </div>
    </div>
  </div>`;
            $.facebox(html);
        }

        function chooseSingle() {
            $(document).one('afterClose.facebox', function () {
                addForm(0);
            });
            $.facebox.close();
        }

        function openBulkForm() {
            $.facebox(function () {
                $.ajax({
                    url: '<?php echo MyUtility::makeUrl("Questions", "bulkForm"); ?>',
                    success: function (res) {
                        $.facebox(res);
                    },
                    error: function () {
                        $.facebox('<div class="pad-8">Failed to load bulk form.</div>');
                    }
                });
            });
        }

        // === Bulk select helpers ===
        function toggleSelectAllQuestions(master) {
            var isChecked = $(master).prop('checked');
            $('#listing').find('input.question-select').prop('checked', isChecked);
        }

        function bulkDeleteSelected() {
            var ids = [];
            $('#listing').find('input.question-select:checked').each(function () {
                ids.push($(this).val());
            });

            if (ids.length === 0) {
                alert('Please select at least one question.');
                return;
            }

            if (!confirm('Are you sure you want to delete the selected questions?')) {
                return;
            }

            // Use PHP-generated URL instead of fcom.makeUrl
            var url = '<?php echo MyUtility::makeUrl("Questions", "bulkDelete"); ?>';

            fcom.updateWithAjax(
                url,
                { question_ids: ids },
                function (res) {
                    // Refresh listing after successful delete
                    search(document.frmClassSearch);
                    // Reset master checkbox
                    $('#selectAllQuestions').prop('checked', false);
                }
            );
        }
    </script>

</div>
