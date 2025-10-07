<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COURSE_APPROVAL_REQUEST_DETAIL'); ?></h4>
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
                                <dd><?php echo MyDate::formatDate($requestData['coapre_created']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_STATUS'); ?></dt>
                                <dd><?php echo Course::getRequestStatuses($requestData['coapre_status']); ?></dd>
                            </dl>
                            <?php if ($requestData['coapre_remark'] != '') { ?>
                                <dl class="list">
                                    <dt><?php echo Label::getLabel('LBL_COMMENTS'); ?></dt>
                                    <dd><?php echo nl2br($requestData['coapre_remark']); ?></dd>
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
                                <dd><?php echo $requestData['coapre_title']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_SUB_TITLE'); ?></dt>
                                <dd><?php echo $requestData['coapre_subtitle']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_CATEGORY'); ?></dt>
                                <dd><?php echo empty($requestData['coapre_cate_name']) ? Label::getLabel('LBL_NA') : $requestData['coapre_cate_name']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_SUBCATEGORY'); ?></dt>
                                <dd><?php echo empty($requestData['coapre_subcate_name']) ? Label::getLabel('LBL_NA') : $requestData['coapre_subcate_name']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_DETAIL'); ?></dt>
                                <dd>
                                    <div class="editor-content">
                                        <iframe srcdoc="<?php echo $requestData['coapre_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe>
                                    </div>
                                </dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_PRICE'); ?></dt>
                                <dd><?php echo MyUtility::formatMoney($requestData['coapre_price']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_DURATION'); ?></dt>
                                <dd><?php echo YouTube::convertDuration($requestData['coapre_duration']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_LEVEL'); ?></dt>
                                <dd><?php echo Course::getCourseLevels($requestData['coapre_level']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_LANGUAGE'); ?></dt>
                                <dd><?php echo $requestData['coapre_clang_name']; ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_CERTIFICATE'); ?></dt>
                                <dd><?php echo AppConstant::getYesNoArr($requestData['coapre_certificate']); ?></dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_TAGS'); ?></dt>
                                <dd><?php echo empty($requestData['coapre_srchtags']) ? Label::getLabel('LBL_NA') : implode(', ', $requestData['coapre_srchtags']); ?></dd>
                            </dl>

                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_CONTENT'); ?></dt>
                                <dd>     
                                    <?php if (!empty($requestData['coapre_learnings'])) { ?>      
                                        <ul>
                                            <?php foreach ($requestData['coapre_learnings'] as $content) { ?>
                                                <li><?php echo $content['coinle_response']; ?></li>   
                                            <?php } ?>
                                        </ul>
                                        <?php
                                    } else {
                                        echo Label::getLabel('LBL_NA');
                                    }
                                    ?>
                                </dd>
                            </dl>

                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_LEARNERS'); ?></dt>
                                <dd>                                
                                    <?php if (!empty($requestData['coapre_learners'])) { ?>
                                        <ul>
                                            <?php foreach ($requestData['coapre_learners'] as $content) { ?>
                                                <li><?php echo $content['coinle_response']; ?></li>   
                                            <?php } ?>
                                        </ul>
                                        <?php
                                    } else {
                                        echo Label::getLabel('LBL_NA');
                                    }
                                    ?>
                                </dd>
                            </dl>
                            <dl class="list">
                                <dt><?php echo Label::getLabel('LBL_COURSE_REQUIREMENTS'); ?></dt>
                                <dd>                                
                                    <?php if (!empty($requestData['coapre_requirements'])) { ?>
                                        <ul>
                                            <?php foreach ($requestData['coapre_requirements'] as $content) { ?>
                                                <li><?php echo $content['coinle_response']; ?></li>   
                                            <?php } ?>
                                        </ul>
                                        <?php
                                    } else {
                                        echo Label::getLabel('LBL_NA');
                                    }
                                    ?>
                                </dd>
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
                                <dd><?php echo empty($requestData['user_gender']) ? '-' : User::getGenderTypes()[$requestData['user_gender']]; ?></dd>
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