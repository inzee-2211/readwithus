<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

// $frm->setFormTagAttribute('class', 'form');
// $frm->setFormTagAttribute('id', 'frmCourses');
//$frm->setFormTagAttribute('onsubmit', 'setupPrice(this); return false;');
// $typeFld = $frm->getField('course_type');
// $currencyFld = $frm->getField('course_currency_id');
$priceFld = $frm->getField('course_price');
?>
<?php echo $frm->getFormTag(); ?>
<div class="page-layout">
   
        <div class="box-panel">
            <div class="box-panel__head border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4><?php echo Label::getLabel('LBL_MANAGE_QUESTIONS'); ?></h4>
                    </div>

                    <div>
                            <div class="buttons-group d-flex align-items-center">
                                <a href="javascript:void(0);" onclick="selectAllCheckboxes(<?php echo $courseId; ?>)" class="btn btn--primary btn--block-mobile">
                                    <svg class="icon">
                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#plus-more"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_ATTACH'); ?>
                                </a>
                            </div>
                     </div>
                </div>
            </div>
            <div class="box-panel__body">
                <div class="box-panel__container">
                    <div class="box-min-height">

                        <div class="">
                            <div class="form form--horizontal">
                                



                                    <div class="table-scroll">
                                        <table class="table table--styled table--responsive table--aligned-middle">
                                            <tr class="title-row">
                                             
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
                                                
                                               
                                                <td style="  max-width: 300px;overflow:hidden;">
                                                    <div class="flex-cell">
                                                        <div class="flex-cell__label"><?php echo $titleLabel; ?></div>
                                                        <div class="flex-cell__content">
                                                <div style="margin-right:4px;">       
                                             </div>
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

                                                            <a href="javascript:void(0);"
                                                                
                                                                class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                <input type="checkbox" id="select_all" name="questionIds[]" value="<?php echo $question['question_id']; ?>" >
                                                               
                                                                <div class="tooltip tooltip--top bg-black">
                                                                    <?php echo Label::getLabel('LBL_EDIT'); ?></div>
                                                            </a>

                                                                                                                        <!-- <a href="<?php echo MyUtility::makeUrl('Lessons') . '?ordles_status=-1&order_id=' . $question['question_added_on']; ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                                            <svg class="icon icon--cancel icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . '/images/sprite.svg#view'; ?>"></use></svg>
                                                                                            <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_VIEW_LESSONS'); ?></div>
                                                                                        </a> -->
                                                            <?php   ?>
                                                            <!-- <a href="javascript:void(0);"
                                                                onclick="cancelForm('<?php echo $question['question_id']; ?>');"
                                                                class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                                                <svg class="icon icon--cancel icon--small">
                                                                    <use
                                                                        xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#delete-icon'; ?>">
                                                                    </use>
                                                                </svg>
                                                                <div class="tooltip tooltip--top bg-black">
                                                                    <?php echo Label::getLabel('LBL_CANCEL'); ?></div>
                                                            </a> -->
                                                            <?php   ?>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php }  ?>
                                        </table>
                                    
                                        
                                </div>


                            </div>
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

<?php echo $frm->getFieldHtml('course_id'); ?>
</form>
<?php echo $frm->getExternalJS(); ?>
<script>
var TYPE_FREE = "<?php echo Course::TYPE_FREE; ?>";
var TYPE_PAID = "<?php echo Course::TYPE_PAID; ?>";
</script>