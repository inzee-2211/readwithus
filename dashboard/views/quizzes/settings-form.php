<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('id', 'frmCourses');
$frm->setFormTagAttribute('onsubmit', 'setupSettings(this); return false;');
$certFld = $frm->getField('quiz_offer_certificate');
$welcomeFld = $frm->getField('quiz_fail_message');
$welcomeFld->setFieldTagAttribute('class', 'field-count__wrap');
$welcomeFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_INSERT_FAIL_MESSAGE'));
$congrFld = $frm->getField('quiz_pass_message');
$congrFld->setFieldTagAttribute('class', 'field-count__wrap');
$congrFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_INSERT_PASS_MESSAGE'));  

$durationFld = $frm->getField('quiz_duration');
$passpercentageFld = $frm->getField('quiz_pass_percentage');
$validityFld = $frm->getField('quiz_validity');

//$tagFld = $frm->getField('course_tags');
///$tagFld->addFieldTagAttribute('id', "tagsinput");
//$tagFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_INSERT_YOUR_COURSE_TAGS'));
// ($frm->getField('btn_approval'))->setFieldTagAttribute('onclick', 'submitForReview();');
?>
<?php echo $frm->getFormTag(); ?>
<div class="page-layout">
    <div class="page-layout__small">
        <?php echo $this->includeTemplate('quizzes/sidebar.php', ['frm' => $frm, 'active' => 5, 'courseId' => $courseId]) ?>
    </div>
    <div class="page-layout__large">
        <div class="box-panel">
            <div class="box-panel__head border-bottom">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <h4><?php echo Label::getLabel('LBL_MANAGE_EXAM_SETTINGS'); ?></h4>
                    </div>
                </div>
            </div>
            <div class="box-panel__body">
                <div class="box-panel__container">
                    <!-- <p><?php echo Label::getLabel('LBL_SETTINGS_FORM_INFO'); ?></p> -->
                    <div class=" ">
                        <div class="form">
                            <?php //if ($offerCetificate == true) { ?>

                            <?php echo $frm->getFieldHtml('course_id'); ?>
                            
                                <div class="row">
                                <div class="col-md-4">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $durationFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                        <div class="field_cover">
                                                    <?php echo $durationFld->getHtml('quiz_duration'); ?>
                                                </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $passpercentageFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                        <div class="field_cover">
                                                    <?php echo $passpercentageFld->getHtml('course_pass'); ?>
                                                </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $validityFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                        <div class="field_cover">
                                                    <?php echo $validityFld->getHtml('quiz_validity'); ?>
                                                </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                                <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $welcomeFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <?php
                                            $maxLength = 300;
                                            $strLen = $maxLength - strlen($welcomeFld->value); ?>
                                            <div class="field_cover field-count" data-length="<?php echo $maxLength ?>" field-count="<?php echo $strLen; ?>">
                                                <?php echo $welcomeFld->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo $congrFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <?php
                                            $maxLength = 300;
                                            $strLen = $maxLength - strlen($congrFld->value); ?>
                                            <div class="field_cover field-count" data-length="<?php echo $maxLength ?>" field-count="<?php echo $strLen; ?>">
                                                <?php echo $congrFld->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> 

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="field-set">
                                            <div class="caption-wraper">
                                                <label class="field_label">
                                                    <?php echo $certFld->getCaption(); ?>
                                                    <span class="spn_must_field">*</span>
                                                </label>
                                            </div>
                                            <div class="field-wraper">
                                                <div class="field_cover">
                                                    <ul class="list-inline">
                                                        <?php
                                                        $selected = ($certFld->value > 0) ? $certFld->value : AppConstant::NO;
                                                        foreach ($certFld->options as $val => $option) { ?>
                                                            <li>
                                                                <label>
                                                                    <span class="radio">
                                                                        <input type="radio" <?php echo ($selected == $val) ? 'checked="checked"' : '' ?> data-fatreq='{"required":true}' onchange="updatePriceForm(this.value);" name="quiz_offer_certificate" value="<?php echo $val; ?>">
                                                                        <i class="input-helper"></i>
                                                                    </span>
                                                                    <?php echo $option; ?>
                                                                </label>
                                                            </li>
                                                        <?php } ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <!-- <?php //} else {
                               // echo $certFld->getHtml();
                         //   }
                            ?> -->
                       
                            <!-- <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php// echo $tagFld->getCaption(); ?>
                                                <span class="spn_must_field">*</span>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <?php //echo $tagFld->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> -->
                        </div>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

</form>
<?php echo $frm->getExternalJS(); ?>
<script>
    var TYPE_FREE = "<?php echo Course::TYPE_FREE; ?>";
    var TYPE_PAID = "<?php echo Course::TYPE_PAID; ?>";
    $(document).ready(function() {
        $('input[name="course_tags"]').tagit({
            caseSensitive : false, 
            allowDuplicates: false,
            allowSpaces: true,
        });
        $('.ui-autocomplete-input').attr('name', 'tags');
        $('form input[name="course_tags"]').on('keypress', function(e) {
            var keyCode = e.keyCode || e.which;
            if (keyCode === 13) {
                e.preventDefault();
                return false;
            }
        });
    });
</script>