<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$overallRating = round($data["ratrev_overall"]);
?>
<div class="repeatedrow">
    <h3>
        <?php 
            if ($data['ratrev_type'] == AppConstant::COURSE) {
                echo Label::getLabel('LBL_COURSE_RATING_INFORMATION');
            } else {
                echo Label::getLabel('LBL_TEACHER_RATING_INFORMATION');
            }
        ?>
    </h3>
    <div class="rowbody">
        <div class="listview">
            <?php if ($data['ratrev_type'] == AppConstant::COURSE && !empty($data['course_name'])) { ?>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_COURSE_NAME'); ?></dt>
                <dd><?php echo $data['course_name']; ?></dd>
            </dl>
            <?php } ?>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_REVIEWED_BY'); ?></dt>
                <dd><?php echo $data['learner_first_name'] . ' ' . $data['learner_last_name']; ?></dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_RATING'); ?></dt>
                <dd>
                    <ul class="rating list-inline">
                        <?php for ($j = 1; $j <= 5; $j++) { ?>
                            <li class="<?php echo $j <= $overallRating ? "active" : "in-active" ?>" style="padding: 0px;">
                                <svg xml:space="preserve" enable-background="new 0 0 70 70" viewBox="0 0 70 70" height="18px" width="18px" y="0px" x="0px" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns="http://www.w3.org/2000/svg" id="Layer_1" version="1.1">
                                    <g>
                                        <path d="M51,42l5.6,24.6L35,53.6l-21.6,13L19,42L0,25.4l25.1-2.2L35,0l9.9,23.2L70,25.4L51,42z M51,42" fill="<?php echo $j <= $overallRating ? "#ff3a59" : "#474747" ?>" />
                                    </g>
                                </svg>
                            </li>
                        <?php } ?>
                    </ul>
                </dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_REVIEW_TITLE'); ?></dt>
                <?php $findKeywordStr = ''; ?>
                <dd><?php echo nl2br($data['ratrev_title']); ?></dd>
            </dl>
            <dl class="list">
                <dt><?php echo Label::getLabel('LBL_REVIEW_COMMENTS'); ?></dt>
                <?php $findKeywordStr = ''; ?>
                <dd><?php echo nl2br($data['ratrev_detail']); ?></dd>
            </dl>
        </div>
    </div>
    <br>
        <?php if (empty($data['teacher_deleted'])) { ?>
            <h3><?php echo Label::getLabel('LBL_CHANGE_STATUS'); ?></h3>
            <div class="rowbody space">
                <div class="listview">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
        <?php } ?>
</div>