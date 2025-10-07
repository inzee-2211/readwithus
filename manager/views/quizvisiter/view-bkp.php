<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COURSE_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="sectionhead">
                    <h4><?php echo Label::getLabel('LBL_BASIC_DETAILS') ?></h4>
                </div>
                <div class="tabs_panel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TITLE'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_title']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_SUB_TITLE'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_subtitle']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_TEACHER_NAME'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['teacher_first_name'] . ' ' . $courseData['teacher_last_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_DURATION'); ?>
                                    </label>
                                    : <strong><?php echo YouTube::convertDuration($courseData['course_duration']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_CATEGORY'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['cate_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_SUB_CATEGORY'); ?>
                                    </label>
                                    : <strong><?php echo empty($courseData['subcate_name']) ? Label::getLabel('LBL_NA') : $courseData['subcate_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LEVEL'); ?>
                                    </label>
                                    : <strong><?php echo Course::getCourseLevels($courseData['course_level']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LANGUAGE'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_clang_name']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_STATUS'); ?>
                                    </label>
                                    : <strong><?php echo Course::getStatuses($courseData['course_status']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_PRICE'); ?>
                                    </label>
                                    : <strong><?php echo CourseUtility::formatMoney($courseData['course_price']); ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_ADDED_ON'); ?>
                                    </label>
                                    : <strong><?php echo MyDate::formatDate($courseData['course_created']); ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_SECTIONS'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_sections']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_LECTURES'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_lectures']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_REVIEWS'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_reviews']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_STUDENTS'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_students']; ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_CERTIFICATE'); ?>
                                    </label>
                                    : <strong><?php echo AppConstant::getYesNoArr()[$courseData['course_certificate']]; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_RATINGS'); ?>
                                    </label>
                                    : <strong><?php echo $courseData['course_ratings']; ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sectionhead">
                    <h4><?php echo Label::getLabel('LBL_OTHER_DETAILS') ?></h4>
                </div>
                <div class="tabs_panel">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="field-set">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        <?php echo Label::getLabel('LBL_DESCRIPTION'); ?>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="editor-content"><iframe srcdoc="<?php echo $courseData['course_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        resetEditorHeight();
    });
</script>