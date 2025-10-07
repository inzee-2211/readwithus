<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'groupClassesFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupClass(this, false); return(false);');
$titleFld = $frm->getField('grpcls_title');
$hintFld = $frm->getField('grpcls_hint');
$descFld = $frm->getField('grpcls_description');
$descmathFld = $frm->getField('grpcls_description_math');
$totalSeatFld = $frm->getField('grpcls_total_seats');
$totalMARKSFld = $frm->getField('grpcls_total_marks');
$tlangFld = $frm->getField('grpcls_tlang_id');
$entryFeeFld = $frm->getField('grpcls_entry_fee');
$starttimeFld = $frm->getField('grpcls_start_datetime');
$durationFld = $frm->getField('grpcls_duration');
 
 
$fld = $frm->getField('question_id');
$fld->setFieldTagAttribute('id', 'question_id');

$catgFld = $frm->getField('course_cate_id');
$catgFld->setFieldTagAttribute('onchange', 'getSubCategories(this.value)');
$subCatFld = $frm->getField('course_subcate_ida');
$subCatFld->setFieldTagAttribute('id', 'subCategories');

$bannerFld = $frm->getField('grpcls_banner');
 
$nextButton = $frm->getField('btn_next');
$nextButton->addFieldTagAttribute('onClick', 'setupClass(this.form, true); return(false);');
// if ($isClassBooked) {
//     $frm->getField('grpcls_start_datetime')->addFieldTagAttribute('readonly', 'readonly');
//     $frm->getField('grpcls_duration')->addFieldTagAttribute('readonly', 'readonly');
//    // $frm->getField('grpcls_tlang_id')->addFieldTagAttribute('readonly', 'readonly');
//     $frm->getField('grpcls_entry_fee')->addFieldTagAttribute('readonly', 'readonly');
// }

 
$bannerInfo = Label::getLabel('LBL_MAX_SIZE_{size}_&_EXT_ARE_{ext}');
$bannerExt = implode(", ", Afile::getAllowedExts(Afile::TYPE_LESSON_QUESTIONS_FILE));
$bannerSize = MyUtility::convertBitesToMb(Afile::getAllowedUploadSize(Afile::TYPE_LESSON_QUESTIONS_FILE)) . ' MB';
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4>Add Question</h4>
    </div>
    <div class="facebox-panel__body">
        <?php echo $frm->getFormTag();
       
        ?>
        <?php echo $frm->getFieldHTML('question_id'); ?>
        <?php



 
        ?>

        <div class="row">
            <div class="col-md-8">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $titleFld->getCaption(); ?>
                            <?php if ($titleFld->requirement->isRequired()) { ?>
                            <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $titleFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $tlangFld->getCaption(); ?>
                            <?php if ($tlangFld->requirement->isRequired()) { ?>
                            <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $tlangFld->getHtml(); ?>
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
                            <?php echo $descFld->getCaption(); ?>
                            <?php if ($descFld->requirement->isRequired()) { ?>
                            <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $descFld->getHtml(); ?>
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
                            <?php echo $descmathFld->getCaption(); ?>
                           
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $descmathFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

         <div class="col-md-12">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php
                            
                            
                            echo $bannerFld->getCaption(); ?>
                            <?php if ($bannerFld->requirement->isRequired()) { ?><span class="spn_must_field">*</span><?php } ?>
                            <?php if (!empty($banner)) { ?><a href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_LESSON_QUESTIONS_FILE, $classId], CONF_WEBROOT_FRONT_URL) . '?t=' . time(); ?>" class="color-primary"><?php echo Label::getLabel('LBL_DOWNLOAD'); ?></a><?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $bannerFld->getHtml(); ?>
                        
                            <small class="margin-0"><?php echo str_replace(['{size}', '{ext}'], [$bannerSize, $bannerExt], $bannerInfo); ?></small>
                        </div>
                    </div>
                </div>
          </div> 

            <?php if (isset($tlangFld->value)) {  
            if ($tlangFld->value == 1 || $tlangFld->value == 2) {   ?>



<?php
 if(isset($data) && !empty($data))
 {
     
// Extract options and correct answers
    $options = [
        1 => $data['question_option_1'],
        2 => $data['question_option_2'],
        3 => $data['question_option_3'],
        4 => $data['question_option_4'],
    ];
 }
$correctAnswers = explode(',', $data['question_answers']); // Split into an array of correct answers
?>

<div class="row">
    <div class="col-md-12">
        <div class="field-set">
            <div id="optionsContainer">
                <?php foreach ($options as $optionNumber => $optionValue): ?>
                    <div class="option-field" id="option-<?php echo $optionNumber; ?>">
                        <div class="caption-wraper">
                            <label class="field_label">
                                Option <?php echo $optionNumber; ?>
                            </label>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">
                                <input type="text" name="options[]" class="form-control" 
                                    value="<?php echo htmlspecialchars($optionValue); ?>" 
                                    placeholder="Option <?php echo $optionNumber; ?>">

                                <input type="checkbox" name="correct_answer[]" class="ms-3"
                                    style="margin-top: 10px;margin-bottom: 10px;" 
                                    value="<?php echo $optionNumber; ?>"
                                    <?php echo in_array($optionNumber, $correctAnswers) ? 'checked' : ''; ?>> Correct Answer
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

                <!-- <div class="row">

                <div class="col-md-12">
                    <div class="field-set">
                        <div id="optionsContainer"  >
                            
                            <div class="option-field" id="option-1">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        Option 1  
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <input type="text" name="options[]" class="form-control" placeholder="Option 1">
    
                                        <input type="checkbox" name="correct_answer[]" class="ms-3"
                                            style="margin-top: 10px;margin-bottom: 10px;" value="1"> Correct Answer
                                    </div>
                                </div>
                            </div>

                            <div class="option-field" id="option-1">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        Option 2 
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <input type="text" name="options[]" class="form-control" placeholder="Option 2">
    
                                        <input type="checkbox" name="correct_answer[]" class="ms-3"
                                            style="margin-top: 10px;margin-bottom: 10px;" value="2"> Correct Answer
                                    </div>
                                </div>
                            </div>

                            <div class="option-field" id="option-1">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        Option 3  
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <input type="text" name="options[]" class="form-control" placeholder="Option 3">
    
                                        <input type="checkbox" name="correct_answer[]" class="ms-3"
                                            style="margin-top: 10px;margin-bottom: 10px;" value="3"> Correct Answer
                                    </div>
                                </div>
                            </div>

                            <div class="option-field" id="option-1">
                                <div class="caption-wraper">
                                    <label class="field_label">
                                        Option 4  
                                    </label>
                                </div>
                                <div class="field-wraper">
                                    <div class="field_cover">
                                        <input type="text" name="options[]" class="form-control" placeholder="Option 4">
    
                                        <input type="checkbox" name="correct_answer[]" class="ms-3"
                                            style="margin-top: 10px;margin-bottom: 10px;" value="4"> Correct Answer
                                    </div>
                                </div>
                            </div>
                           
                        </div>
                    </div>
                </div>
            </div> -->

               <?php          }
                      } ?>
        <div class="row">

            <div class="col-md-12">
                <div class="field-set">
                    <div id="optionsContainer" style="display: none;">
                        <!-- <h5>Options:</h5> -->
                        <div class="option-field" id="option-1">
                            <div class="caption-wraper">
                                <label class="field_label">
                                    Option 1  
                                </label>
                            </div>
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <input type="text" name="options[]" class="form-control" placeholder="Option 1">

                                    <input type="checkbox" name="correct_answer[]" class="ms-3"
                                        style="margin-top: 10px;margin-bottom: 10px;" value="1"> Correct Answer
                                </div>
                            </div>
                        </div>
                        <div class="field-wraper">
                            <div class="field_cover">

                                <button type="button" id="addOptionBtn" style="float:right; margin: 5px 10px 10px 10px;"
                                    class="btn btn--secondary">Add Option</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $catgFld->getCaption(); ?>
                            <span class="spn_must_field">*</span>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $catgFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $subCatFld->getCaption(); ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $subCatFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $totalMARKSFld->getCaption(); ?>
                            <?php if ($totalMARKSFld->requirement->isRequired()) { ?>
                            <span class="spn_must_field">*</span>
                            <?php } ?>
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $totalMARKSFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="field-set">
                    <div class="caption-wraper">
                        <label class="field_label">
                            <?php echo $hintFld->getCaption(); ?>
                            <!-- <span class="spn_must_field">*</span> -->
                        </label>
                    </div>
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $hintFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row form-action-sticky">
            <div class="col-sm-12">
                <div class="field-set margin-bottom-0">
                    <div class="field-wraper">
                        <div class="field_cover">
                            <?php echo $nextButton->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </form>
        <?php echo $frm->getExternalJS(); ?>
    </div>
</div>
<?php

$catId = ($catgFld->value) ? $catgFld->value : 0;
$subCatId = ($subCatFld->value) ? $subCatFld->value : 0;

// $catId = ($data['question_cat']) ? $data['question_cat'] : 0;
// $subCatId = ($data['question_subcat']) ? $data['question_subcat'] : 0;
// $selectedSubCategoryId = 4;

?>
<script>
$(document).ready(function() {
 
    getSubCategories("<?php echo $catId; ?>", "<?php echo $subCatId; ?>");

    $('#grpcls_tlang_id').change(function() {

        const selectedType = $(this).val();
        // console.log(selectedType);
        $('#optionsContainer').hide();
        $('#addOptionBtn').hide();

        if (selectedType === '1' || selectedType === '2') {
            $('#optionsContainer').show();
            $('#addOptionBtn').show();
            $('#option-1').show();
            optionCount = 1;
            $('#addOptionBtn').off('click').on('click', function() {
                if (optionCount < 4) {
                    optionCount++;
                    const newOption = `
                        <div class="option-field" id="option-${optionCount}">
                            <div class="caption-wraper">
                                <label class="field_label"> <label for="option-${optionCount}">Option ${optionCount}</label></div></div>
                            <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}">
                            <input type="checkbox" class="checkbox-spacing" style="margin-top: 10px;margin-bottom: 10px;" name="correct_answer[]" value="${optionCount}"> Correct Answer
                        </div>
                    `;
                    $('#optionsContainer').append(newOption);
                }
            });
        }
    });
});
</script>