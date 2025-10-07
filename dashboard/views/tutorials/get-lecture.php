<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($lecture) {
    $containerClass = (count($resources) > 0) ? 'col-xl-7' : 'col-xl-12';
    $lectures = json_decode($progData['crspro_covered'], true);
    $lectures = ($lectures) ? $lectures : [];




     
    ?>
    <div class="row justify-content-between">
        <div class="<?php echo $containerClass; ?>">
            <div class="cms-container">
                <div class="editor-content">
                    <iframe srcdoc="<?php echo $lecture['lecture_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe>
                </div>
            </div>
        </div>
        <?php if (count($resources) > 0) { ?>
            <div class="col-xl-5 d-flex justify-content-xl-end align-items-xl-start">
                <div class="box-outlined">
                    <div class="box-outlined__head margin-bottom-6">
                        <h6><?php echo Label::getLabel('LBL_LECTURE_RESOURCES') . ' (' . count($resources) . ')' ?></h6>
                    </div>
                    <div class="box-outlined__body">
                        <div class="lecture-attachment">
                            <?php foreach ($resources as $resource) { ?>
                                <a href="<?php echo MyUtility::makeUrl('Tutorials', 'downloadResource', [$progressId, $resource['lecsrc_id']]); ?>" target="_blank" class="lecture-attachment__item">
                                    <figure class="lecture-attachment__media">
                                        <svg class="attached-media">
                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#<?php echo Resource::getFileIcon($resource['resrc_type']); ?>">
                                        </use>
                                        </svg>
                                    </figure>
                                    <span class="lecture-attachment__content">
                                        <p class="margin-bottom-0 color-black"><?php echo $resource['resrc_name']; ?></p>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="page-directions border-top">
        <div class="row justify-content-between">
            <div class="col-sm-4">
                <?php if (empty($video['lecsrc_link'])) {
                    
                 //  echo '<pre>';print_r($lecture);die;
                    ?>
                    <a href="javascript:void(0);" id="btnComplete<?php echo $lecture['lecture_id']; ?>" onclick="markComplete('<?php echo $lecture['lecture_id']; ?>', '<?php echo AppConstant::YES; ?>')" class="btn btn--primary btn--sm-block  <?php echo (!in_array($lecture['lecture_id'], $lectures)) ? '' : 'btn--disabled' ?>">
                        <?php echo Label::getLabel('LBL_MARK_LECTURE_COMPLETE'); ?>
                    </a>
                <?php } ?>
            </div>
            <!-- <div class="col-sm-3">
                <?php if (isset($lecture['section_quiz_id']) && $lecture['section_quiz_id'] != 0) {
                if(isset($QuizAttemptData[0]) && !empty($QuizAttemptData[0]))
                {
                 if($QuizAttemptData[0]['status']==0)
                 {
                  echo 'Quiz submitted for evaluation';
                 }
                 if($QuizAttemptData[0]['status']==1)
                 {
                    echo $QuizAttemptData[0]['quiz_fail_message'];

                    ?>

<a href="javascript:void(0);" id="btnComplete<?php echo $lecture['section_quiz_id']; ?>" onclick="AttampQuiz('<?php echo $lecture['section_quiz_id']; ?>', '<?php echo $lecture['lecture_course_id']; ?>', '<?php echo $lecture['lecture_id']; ?>')" class="btn btn--primary btn--sm-block  ">
                        <?php echo Label::getLabel('LBL_ATTEMPT_QUIZ'); ?>
                    </a>
<?php
                 }
                 if($QuizAttemptData[0]['status']==2)
                 {
                    echo $QuizAttemptData[0]['quiz_pass_message'];
                 }

                }
                else
                {
                 ?>
                    <a href="javascript:void(0);" id="btnComplete<?php echo $lecture['section_quiz_id']; ?>" onclick="AttampQuiz('<?php echo $lecture['section_quiz_id']; ?>', '<?php echo $lecture['lecture_course_id']; ?>', '<?php echo $lecture['lecture_id']; ?>')" class="btn btn--primary btn--sm-block  ">
                        <?php echo Label::getLabel('LBL_ATTEMPT_QUIZ'); ?>
                    </a>
                <?php } 
                } ?>
            </div>
             -->





             <div class="col-sm-4">
    <?php 
    
    
    if (!empty($lecture['section_quiz_id'])): ?>
        <?php   if (!empty($QuizAttemptData[0])): ?>
            <?php 
            $status = $QuizAttemptData[0]['status'];
            switch ($status) {
                case 0:
                    echo '<span class="alert1 alert-info" style="padding: 10px; background-color: #e0f7fa; border: 1px solid #00acc1; color: #00796b; border-radius: 5px;">Quiz Submitted for Evaluation</span>';
                    break;
                case 1:
                    echo '<span class="alert1 alert-danger" style="color:red;">' . $QuizAttemptData[0]['quiz_fail_message'] . '</span>';
                    ?>
                    <a href="javascript:void(0);" 
                       id="btnComplete<?php echo $lecture['section_quiz_id']; ?>" 
                       onclick="AttampQuiz('<?php echo $lecture['section_quiz_id']; ?>', '<?php echo $lecture['lecture_course_id']; ?>', '<?php echo $lecture['lecture_id']; ?>')" 
                       class="btn btn--primary btn--sm-block"
                       style="margin-top:10px; padding: 8px 15px; background-color: #00796b; color: white; border-radius: 5px; text-decoration: none;">
                        <?php echo Label::getLabel('LBL_ATTEMPT_QUIZ_AGAIN'); ?>
                    </a>
                    <?php break;
                case 2:
                    echo '<span class="alert1 alert-success" style="color:green;">' . $QuizAttemptData[0]['quiz_pass_message'] . '</span>';
                 //   echo '<span class="alert1 alert-success" style="padding: 10px; background-color: #c8e6c9; border: 1px solid #66bb6a; color: #388e3c; border-radius: 5px;">' . $QuizAttemptData[0]['quiz_pass_message'] . '</span>';
                    break;
            }
            ?>
        <?php else:  ?>
            <a href="javascript:void(0);" 
               id="btnComplete<?php echo $lecture['section_quiz_id']; ?>" 
               onclick="AttampQuiz('<?php echo $lecture['section_quiz_id']; ?>', '<?php echo $lecture['lecture_course_id']; ?>', '<?php echo $lecture['lecture_id']; ?>')" 
               class="btn btn--primary btn--sm-block"
               style="padding: 8px 15px; background-color: #00796b; color: white; border-radius: 5px; text-decoration: none;">
                <?php echo Label::getLabel('LBL_ATTEMPT_QUIZ'); ?>
            </a>
        <?php  endif; ?>
    <?php endif; ?>
</div>

            <div class="col-sm-auto">
                <div class="btn-actions">
                    <?php $display = ($previousLecture) ? '' : 'btn--disabled'; ?>
                    <a href="javascript:void(0);" class="btn btn--primary-bordered margin-right-1 getPrevJs <?php echo $display; ?>">
                        <svg class="icon icon--arrow icon--xsmall margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#prev"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_PREV') ?>
                    </a>
                    <?php $display = ($nextLecture) ? '' : 'btn--disabled'; ?>
               
                    <?php if($lecture['section_quiz_id']==0): ?>
                    <a href="javascript:void(0);" class="btn btn--primary-bordered margin-left-1 getNextJs <?php echo $display; ?>">
                        <?php echo Label::getLabel('LBL_NEXT') ?>
                        <svg class="icon icon--arrow icon--xsmall margin-left-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#next"></use>
                        </svg>
                    </a>

                    <?php else: 
                        if(!empty($QuizAttemptData) && $QuizAttemptData[0]['status']==2):
                        ?>
                    <a href="javascript:void(0);" class="btn btn--primary-bordered margin-left-1 getNextJs <?php echo $display; ?>">
                        <?php echo Label::getLabel('LBL_NEXT') ?>
                        <svg class="icon icon--arrow icon--xsmall margin-left-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#next"></use>
                        </svg>
                    </a>
                     <?php endif;
                     endif;
                     ?>
                    
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="message-display no-skin">
                <div class="message-display__media">
                    <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
                </div>
                <h4 class="margin-bottom-4"><?php echo stripslashes(Label::getLabel("LBL_YOU'VE_COMPLETED_THE_LAST_LECTURE_IN_THIS_COURSE.")); ?></h4>
                <?php if ((int) $progData['crspro_progress'] < 100) { ?>
                    <p><?php echo Label::getLabel('LBL_LAST_LECTURE_COMPLETED_SHORT_DESCRIPTION'); ?></p>
                    <a href="javascript:void(0);" onclick="goToPendingLecture();" class="btn btn--secondary">
                        <?php echo Label::getLabel('LBL_GO_TO_PENDING_LECTURES'); ?>
                    </a>
                <?php } ?>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    $(document).ready(function () {
        resetEditorHeight();
    });
</script>