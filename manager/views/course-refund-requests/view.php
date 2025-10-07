<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COURSE_REFUND_REQUEST_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="add border-box border-box--space">
            <div class="repeatedrow">
                <form class="web_form form_horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <h3><i class="ion-pull-request icon"></i> <?php echo Label::getLabel('LBL_REQUEST_INFORMATION'); ?></h3>
                        </div>
                    </div>
                    <div class="rowbody">
                        <div class="listview">
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_REQUESTED_ON'); ?></dt>
                                <dd><?php echo MyDate::formatDate($requestData['corere_created']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_STATUS'); ?></dt>
                                <dd><?php echo Course::getRefundStatuses($requestData['corere_status']); ?></dd>
                            </dl>
                            <?php if ($requestData['corere_remark'] != '') { ?>
                                <dl class="list">
                                    <dt><?php echo Label::getLabel('LBL_COMMENTS'); ?></dt>
                                    <dd><?php echo nl2br($requestData['corere_remark']); ?></dd>
                                </dl>
                            <?php } ?>
                            <?php if ($requestData['corere_comment'] != '') { ?>
                                <dl class="list">
                                    <dt><?php echo Label::getLabel('LBL_DECLINE_REASON/COMMENTS'); ?></dt>
                                    <dd><?php echo nl2br($requestData['corere_comment']); ?></dd>
                                </dl>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
            <div class="repeatedrow">
                <form class="web_form form_horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <h3><i class="ion-ios-paper icon"></i> <?php echo Label::getLabel('LBL_COURSE_INFORMATION'); ?></h3>
                        </div>
                    </div>
                    <div class="rowbody">
                        <div class="listview">
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_TITLE'); ?></dt>
                                <dd><?php echo $requestData['course_title']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_SUB_TITLE'); ?></dt>
                                <dd><?php echo $requestData['course_subtitle']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_DETAIL'); ?></dt>
                                <dd>
                                    <div class="editor-content">
                                        <iframe srcdoc="<?php echo $requestData['course_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_PRICE'); ?></dt>
                                <dd><?php echo CourseUtility::formatMoney($requestData['course_price']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_DURATION'); ?></dt>
                                <dd><?php echo YouTube::convertDuration($requestData['course_duration']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_STATUS'); ?></dt>
                                <dd><?php echo Course::getStatuses($requestData['course_status']); ?></dd>
                            </dl>
                        </div>
                    </div>
                </form>
            </div>
            <div class="repeatedrow">
                <form class="web_form form_horizontal">
                    <div class="row">
                        <div class="col-md-12">
                            <h3><i class="ion-person icon"></i> <?php echo Label::getLabel('LBL_PROFILE_INFORMATION'); ?></h3>
                        </div>
                    </div>
                    <div class="rowbody">
                        <div class="listview">
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_FIRST_NAME'); ?></dt>
                                <dd><?php echo $requestData['user_first_name']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_LAST_NAME'); ?></dt>
                                <dd><?php echo empty($requestData['user_last_name']) ? '-' : $requestData['user_last_name']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_GENDER'); ?></dt>
                                <dd><?php echo!empty($requestData['user_gender']) ? User::getGenderTypes()[$requestData['user_gender']] : '-'; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_EMAIL'); ?></dt>
                                <dd><?php echo $requestData['user_email']; ?></dd>
                            </dl>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        resetEditorHeight();
    });
</script>