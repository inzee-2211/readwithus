<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('id', 'frmCourses');
$frm->setFormTagAttribute('onsubmit', 'setupIntendedLearners(this); return false;');
$typeLearning = IntendedLearner::TYPE_LEARNING;
$typeRequirements = IntendedLearner::TYPE_REQUIREMENTS;
$typeLearners = IntendedLearner::TYPE_LEARNERS;
$intendedLearnertypes = IntendedLearner::getTypes();
$typesSubTitles = IntendedLearner::getTypesSubTitles();
// ($frm->getField('type_learnings[]'))->setFieldTagAttribute('placeholder', $intendedLearnertypes[$typeLearning]);
// ($frm->getField('type_requirements[]'))->setFieldTagAttribute('placeholder', $intendedLearnertypes[$typeRequirements]);
// ($frm->getField('type_learners[]'))->setFieldTagAttribute('placeholder', $intendedLearnertypes[$typeLearners]);
$textLength = 155;
?>
<?php echo $frm->getFormTag(); ?>
<div class="page-layout">
    <div class="page-layout__small">
        <?php echo $this->includeTemplate('quizzes/sidebar.php', ['frm' => $frm, 'active' => 2, 'courseId' => $courseId]) ?>
    </div>
    <div class="page-layout__large">
        <div class="box-panel">
            <div class="box-panel__head border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4><?php echo 'Add Quiz';//echo Label::getLabel('LBL_INTENDED_LEARNERS'); ?></h4>
                    </div>
                    <div>
                   
                            <div class="buttons-group d-flex align-items-center">
                                <a href="javascript:void(0);" onclick="priceForm()" class="btn btn--secondary btn--block-mobile">
                                    <svg class="icon">
                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#plus-more"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_ADD_QUESTIONS'); ?>
                                </a>
                            </div>
                        </div>
                </div>
            </div>
            <div class="box-panel__body">
            <?php echo $frm->getFieldHtml('course_id'); ?>
                <div class="box-panel__container">
                    <!-- <p><?php echo Label::getLabel('LBL_INTENDED_LEARNERS_SHORT_INFO'); ?></p> -->
                    <div class="fields-collection  typesAreaJs<?php echo $typeLearning ?>">
                        <div class="fields-collection__head">
                            <!-- <h6 class="margin-bottom-2"><?php echo $intendedLearnertypes[$typeLearning]; ?></h6>
                            <p class="style-italic">
                                <?php echo $typesSubTitles[$typeLearning]; ?>
                            </p> -->
                        </div>
                        <div class="fields-collection__body  typesListJs sortableLearningJs">


                        <div class="table-scroll">
                                        <table class="table table--styled table--responsive table--aligned-middle">
                                            <tr class="title-row">
                                                <!-- <th><?php //echo $nameLabel = ($siteUserType == User::LEARNER) ? Label::getLabel('LBL_TEACHER') : Label::getLabel('LBL_LEARNER'); ?></th>
-->
                                                 <th><?php echo $titleLabel = Label::getLabel('LBL_TITLE'); ?></th> 
                                                <th><?php echo $typeLabel = Label::getLabel('LBL_TYPE'); ?></th>
                                                <th><?php echo $languageLabel = Label::getLabel('LBL_CATEGORY'); ?></th>
                                                <th><?php echo $lessonLabel = Label::getLabel('LBL_SUBCATGORY'); ?></th>
                                                <th><?php echo $actionLabel = Label::getLabel('LBL_ACTIONS'); ?></th>
                                            </tr>
                                            <?php
                                                $naLabel = Label::getLabel('LBL_N/A');
                                                $statuses = Subscription::getStatuses();

                                            // echo '<pre>';print_r($allClasses);die;
                                            foreach ($allClasses as $question) {
                                                    ?>
                                            <tr>
                                                
                                               
                                                <td>
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo $titleLabel; ?></div>
                                                        <div class="flex-cell__content">
                                                <!-- <div style="margin-right:4px;">        <input type="checkbox" id="select_all" name="questionIds[]" value="<?php echo $question['question_id']; ?>" >
                                             </div> -->
                                                            <?php echo $question['question_title']; ?>
                                                        </div>
                                                </td>
                                                <td>
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo $typeLabel; ?></div>
                                                        <div class="flex-cell__content">
                                                            <?php $question_type= $question['question_type'];
                                                    if ($question_type == 1) {
                                                        // Single-choice question logic
                                                        echo 'Single Choice Question.';
                                                    } elseif ($question_type == 2) {
                                                        // Multiple-choice question logic
                                                        echo 'Multiple Choice Question.';
                                                    } elseif ($question_type == 3) {
                                                        // Text-based question logic
                                                        echo 'Text-based Question.';
                                                    } else {
                                                        // Handle other cases if needed
                                                        echo 'Invalid question type.';
                                                    }
                                                    
                                                    ?>
                                                        </div>
                                                </td>
                                                <td>
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo 'Category'; ?></div>
                                                        <div class="flex-cell__content">
                                                            <?php echo $question['catname']; ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo 'SubCategory'; ?></div>
                                                        <div class="flex-cell__content">
                                                            <?php echo $question['subcatname']; ?></div>
                                                    </div>
                                                </td>
                         
                                                 
                                                <td>
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo 'Action'; ?></div>
                                                        <div class="flex-cell__content">

                                                            <!-- <a href="javascript:void(0);"
                                                                onclick="addForm('<?php echo $question['question_id']; ?>');"
                                                                class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                <svg class="icon icon--edit icon--small">
                                                                    <use
                                                                        xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#edit'; ?>">
                                                                    </use>
                                                                </svg>
                                                                <div class="tooltip tooltip--top bg-black">
                                                                    <?php echo Label::getLabel('LBL_EDIT'); ?></div>
                                                            </a> -->

                                                                                                                        <!-- <a href="<?php echo MyUtility::makeUrl('Lessons') . '?ordles_status=-1&order_id=' . $question['question_added_on']; ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                                            <svg class="icon icon--cancel icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . '/images/sprite.svg#view'; ?>"></use></svg>
                                                                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_VIEW_LESSONS'); ?></div>
                                                                                        </a> -->
                                                            <?php   ?>
                                                            <a href="javascript:void(0);"
                                                                onclick="cancelForm('<?php echo $question['question_id']; ?>');"
                                                                class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                <svg class="icon icon--cancel icon--small">
                                                                    <use
                                                                        xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#delete-icon'; ?>">
                                                                    </use>
                                                                </svg>
                                                                <div class="tooltip tooltip--top bg-black">
                                                                    <?php echo Label::getLabel('LBL_CANCEL'); ?></div>
                                                            </a>
                                                            <?php   ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php }   ?>
                                        </table>
                                    
                                        
                                </div>
                            <!-- <?php
                            $i = 1;
                            if (isset($responses[$typeLearning]) && count($responses[$typeLearning]) > 0) {
                                $learningField = $frm->getField('type_learnings[]');
                                $idsFld = $frm->getField('type_learnings_ids[]');
                                $idsFld->setFieldTagAttribute('class', 'sortable_ids');
                                foreach ($responses[$typeLearning] as $response) {
                                    $learningField->value = CommonHelper::renderHtml($response['coinle_response']);
                                    $idsFld->value = $response['coinle_id']; ?>
                                    <div class="sort-row typeFieldsJs">
                                        <div class="sort-row__item">
                                            <div class="sort-row__field">
                                                <?php $strLen = $textLength - strlen($learningField->value); ?>
                                                <div class="field-count" data-length="<?php echo $textLength ?>" field-count="<?php echo $strLen; ?>">
                                                    <?php echo $frm->getFieldHtml('type_learnings[]') ?>
                                                </div>
                                            </div>
                                            <div class="sort-row__actions">
                                                <a href="javascript:void(0)" class="btn btn--equal btn--sort btn--transparent color-gray-1000 cursor-move sortHandlerJs">
                                                    <svg class="icon icon--sorting">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#sorting-icon"></use>
                                                    </svg>
                                                </a>
                                                <a href="javascript:void(0);" class="btn btn--equal btn--transparent color-gray-1000 removeRespJs" <?php echo ($i > 1) ? 'onclick="removeIntendedLearner(this, \'' . $response['coinle_id'] . '\');"' : 'style="display:none;"'; ?>>
                                                    <svg class="icon icon--sorting">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#bin-icon"></use>
                                                    </svg>
                                                </a>
                                            </div>
                                        </div>
                                        <?php ///echo $frm->getFieldHtml('type_learnings_ids[]'); ?>
                                    </div>
                                <?php
                                    $i++;
                                }
                            }?> -->
                         
                         
                        </div>
                        <!-- <div class="fields-collection__footer margin-top-4">
                            <a href="javascript:void(0)" onclick="addFld('<?php echo $typeLearning ?>')" class="icon-link">
                                <svg class="icon icon--more margin-right-2">
                                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#more-icon"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_ADD_MORE_TO_YOUR_RESPONSE'); ?>
                            </a>
                        </div> -->
                    </div>
                    
                
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$pagingArr = [
    'pageSize' => $post['pagesize'],
    'page' => $post['pageno'],
    'recordCount' => $recordCount,
    'pageCount' => ceil($recordCount / $post['pagesize'])
];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
?>


</form>
<?php echo $frm->getExternalJS(); ?>
<script type="text/javascript">
    $(function() {
        $(".sortableLearningJs, .sortableRequirementJs, .sortableLearnerJs").sortable({
            handle: ".sortHandlerJs",
            update: function(event, ui) {
                updateIntendedOrder();
            }
        });
    });
</script>