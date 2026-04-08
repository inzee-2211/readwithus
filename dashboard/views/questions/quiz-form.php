<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('id', 'groupClassesFrm');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'setupClass(this, false); return(false);');
$titleFld = $frm->getField('grpcls_title');
$hintFld = $frm->getField('grpcls_hint');
$descFld = $frm->getField('grpcls_description');
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
$bannerExt = implode(", ", Afile::getAllowedExts(Afile::TYPE_GROUP_CLASS_BANNER));
$bannerSize = MyUtility::convertBitesToMb(Afile::getAllowedUploadSize(Afile::TYPE_GROUP_CLASS_BANNER)) . ' MB';
?>
<div class="facebox-panel">
    <div class="facebox-panel__head">
        <h4>Exams</h4>
    </div>
    <div class="facebox-panel__body">

    
<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <!-- <th><?php echo $nameLabel = ($siteUserType == User::LEARNER) ? Label::getLabel('LBL_TEACHER') : Label::getLabel('LBL_LEARNER'); ?></th> -->
            <th><?php echo $titleLabel = Label::getLabel('LBL_TITLE'); ?></th>
            <th><?php echo $typeLabel = Label::getLabel('LBL_DESCRIPTION'); ?></th>
            <th><?php echo $languageLabel = Label::getLabel('LBL_DURATION'); ?></th>
            <th><?php echo $lessonLabel = Label::getLabel('LBL_PASS_PERCENTAGE'); ?></th>
            <th><?php echo $statusLabel = Label::getLabel('LBL_VALIDITY'); ?></th>
            <!-- <th><?php echo $added_onLabel = Label::getLabel('LBL_ADDED_ON'); ?></th> -->
            <th><?php echo $actionLabel = Label::getLabel('LBL_ACTIONS'); ?></th>
        </tr>
        <?php
        $naLabel = Label::getLabel('LBL_N/A');
        $statuses = Subscription::getStatuses();
 
    // echo '<pre>';print_r($allClasses);die;
        foreach ($allClasses as $question) {
            //echo '<pre>';print_r($question);die;
            ?>
            <tr>
               
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $titleLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php echo $question['quiz_title']; ?>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $typeLabel; ?></div>
                        <div class="flex-cell__content">
                        <?php echo $question['quiz_description']; ?>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Duration'; ?></div>
                        <div class="flex-cell__content"> <?php echo $question['quiz_duration'].' Mins'; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Pass Percentage'; ?></div>
                        <div class="flex-cell__content"><?php echo $question['quiz_pass_percentage']. ' %'; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Validity'; ?></div>
                        <div class="flex-cell__content">
                        <?php echo $question['quiz_validity'].' Hours'; ?>
                           
                        </div>
                    </div>
                </td>
                
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Action'; ?></div>
                        <div class="flex-cell__content">

                      
                            <?php   ?>
                                <a href="javascript:void(0);" onclick="attachquiztoQuestion('<?php echo $question['quiz_id']; ?>', '<?php echo $formid; ?>','<?php echo $question['quiz_title']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--cancel icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#plus-more'; ?>"></use></svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_ADD'); ?></div>
                                </a>
                            <?php   ?>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
    
    </div>
</div>
<?php

$catId = ($catgFld->value) ? $catgFld->value : 0;
$subCatId = ($subCatFld->value) ? $subCatFld->value : 0;
?>
<script>

function attachquiztoQuestion(id,formSectionId,title) {

    const form = document.getElementById(formSectionId);
    let existingInput = form.querySelector(`input[name="quiz_id"]`);
   const targetField = form.querySelector('input[name="target_field_name"]'); // Replace with the actual name of the text field

if (existingInput) {
      existingInput.value = id;
      $.facebox.close();
} else {
   
    const hiddenInput = document.createElement("input");
        hiddenInput.type = "hidden";
        hiddenInput.name = "section_quiz_id";
        hiddenInput.value = id;
        hiddenInput.classList.add("highlight"); // Add highlight class for visual effect
        form.appendChild(hiddenInput);
 
        //Optionally, add a message or indicator
        // const message = document.createElement("span");
        // message.textContent =  "Quiz attached: "+title;
        // message.classList.add("notification");
        // form.appendChild(message);

        const message = document.createElement("span");

// Use innerHTML to style "Quiz attached:" separately
message.innerHTML = `<span style="color: #f5411f; font-weight: bold;">Exam attached:</span> ${title}`;

message.classList.add("notification");
form.appendChild(message);

        
 
        $.facebox.close();
}
 
}
$(document).ready(function() {
    getSubCategories("<?php echo $catId; ?>", "<?php echo $subCatId; ?>");

    // $('#grpcls_tlang_id').change(function() {

    //     const selectedType = $(this).val();
    //     // console.log(selectedType);
    //     $('#optionsContainer').hide();
    //     $('#addOptionBtn').hide();

    //     if (selectedType === '1' || selectedType === '2') {
    //         $('#optionsContainer').show();
    //         $('#addOptionBtn').show();
    //         $('#option-1').show();
    //         optionCount = 1;
    //         $('#addOptionBtn').off('click').on('click', function() {
    //             if (optionCount < 4) {
    //                 optionCount++;
    //                 const newOption = `
    //                     <div class="option-field" id="option-${optionCount}">
    //                         <div class="caption-wraper">
    //                             <label class="field_label"> <label for="option-${optionCount}">Option ${optionCount}</label></div></div>
    //                         <input type="text" name="options[]" class="form-control" placeholder="Option ${optionCount}">
    //                         <input type="checkbox" class="checkbox-spacing" style="margin-top: 10px;margin-bottom: 10px;" name="correct_answer[]" value="${optionCount}"> Correct Answer
    //                     </div>
    //                 `;
    //                 $('#optionsContainer').append(newOption);
    //             }
    //         });
    //     }
    // });
});
</script>