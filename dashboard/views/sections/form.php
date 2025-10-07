<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('id', 'frmSection' . $order);
$frm->setFormTagAttribute('onsubmit', 'setupSection(this); return false;');
$titleFld = $frm->getField('section_title');
$titleFld->setFieldTagAttribute('class', 'field-count__wrap');
$titleFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_ADD_SECTION_TITLE'));
$descFld = $frm->getField('section_details');
$descFld->setFieldTagAttribute('class', 'field-count__wrap  textarea-small');
$descFld->setFieldTagAttribute('placeholder', Label::getLabel('LBL_ADD_SECTION_DESCRIPTION'));
$cancelFld = $frm->getField('btn_cancel');
$cancelFld->setFieldTagAttribute('class', 'hide-js');


// Get the "Add Quiz" button and set attributes
$addQuizFld = $frm->getField('btn_add_quiz');
$addQuizFld->setFieldTagAttribute('class', 'btn btn-primary');
//$addQuizFld->setFieldTagAttribute('onclick', 'addQuizFunction();');
$addQuizFld->setFieldTagAttribute('onclick', 'addQuizFunction(this);');


if ($sectionId > 0) {
    $cancelFld->setFieldTagAttribute('onclick', '$("#sectionId' . $sectionId . ' .sectionEditCardJs").html("");$("#sectionId' . $sectionId . ' .sectionCardJs").show();');
} else {
    $cancelFld->setFieldTagAttribute('onclick', 'cancelSection("' . $order . '");');
}
$submitFld = $frm->getField('btn_submit');
$submitFld->setFieldTagAttribute('class', 'hide-js');
$titleLength = 80;
$descLength = 300;

if ($sectionId > 0) { ?>
    <?php echo $frm->getFormTag(); ?>
    <div class="step-small-form">
        <div class="row">
            <div class="col-md-12 33">
                <!-- Add Quiz Button -->
                <!-- <div class="step-actions">
                    <?php echo $addQuizFld->getHtml(); ?>
                </div> -->
                <div class="field-wraper" style="justify-content: flex-end;display: flex;margin-bottom:8px;">
                                            <?php
                                            // if(isset($_SESSION['APP_SESSION']['user_id']) && $_SESSION['APP_SESSION']['user_id']==449)
                                            // {
                                             echo $addQuizFld->getHtml(); 
                                          //  }
                                            ?>
                                            </div>
                <div class="field-set">
                    <div class="field-wraper">
                        
                        <?php
                        $strLen = $titleLength - strlen($titleFld->value); ?>
                        <div class="field_cover field-count" data-length="<?php echo $titleLength ?>" field-count="<?php echo $strLen; ?>">
                            <?php echo $titleFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="field-set">
                    <div class="field-wraper">
                        <?php
                        $strLen = $descLength - strlen($descFld->value); ?>
                        <div class="field_cover field-count" data-length="<?php echo $descLength ?>" field-count="<?php echo $strLen; ?>">
                            <?php echo $descFld->getHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="step-actions">
                    <?php echo $cancelFld->getHtml(); ?>
                    <?php echo $submitFld->getHtml(); ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    echo $frm->getFieldHtml('section_id');
    echo $frm->getFieldHtml('section_course_id');
    ?>
    </form>
<?php echo $frm->getExternalJs();
} else { ?>
    <div class="card-panel" id="sectionForm<?php echo $order; ?>">
        <div class="card-panel__head sectionCardJs">
            <a href="javascript:void(0)" class="btn btn--equal btn--sort btn--transparent color-gray-1000 cursor-move">
                <svg class="icon icon--sorting">
                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#sorting-icon"></use>
                </svg>
            </a>
            <div class="card-title">
                <span class="card-title__label"><?php echo Label::getLabel('LBL_SECTION'); ?> </span>
                <div class="card-title__meta">
                    <!-- [ SECTION FORM ========= 1122-->
                    <div class="card-title__form edit-form-js sectionEditCardJs">
                        <?php echo $frm->getFormTag(); ?>
                        <div class="step-small-form">
                            <div class="row">
                                <!-- Add Quiz Button -->
   
                                <div class="col-md-12">
                                            <div class="field-wraper 11" style="justify-content: flex-end;display: flex;margin-bottom:8px;">
                                            <?php 
//   if(isset($_SESSION['APP_SESSION']['user_id']) && $_SESSION['APP_SESSION']['user_id']==449)
//   {
                                            echo $addQuizFld->getHtml();
  //}
                                            ?>
                                            </div>
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover field-count" data-length="<?php echo $titleLength ?>" field-count="<?php echo $titleLength; ?>">
                                                <?php echo $titleFld->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover field-count" data-length="<?php echo $descLength ?>" field-count="<?php echo $descLength; ?>">
                                                <?php echo $descFld->getHtml(); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="step-actions">
                                        <?php echo $cancelFld->getHtml(); ?>
                                        <?php echo $submitFld->getHtml(); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php
                        echo $frm->getFieldHtml('section_id');
                        echo $frm->getFieldHtml('section_course_id');
                        ?>
                        </form>
                        <?php echo $frm->getExternalJs(); ?>
                    </div>
                    <!-- ] -->
                </div>
            </div>
        </div>
        <div class="card-panel__body lecturesListJs"></div>
    </div>
    
    
    
    <?php
        }?>


<script>

function addQuizFunction(buttonElement) {

    const form = buttonElement.closest("form");

    classId=0;
    fcom.ajax(fcom.makeUrl('Questions', 'quizForm'), {classId: classId,formid:form.id}, function (response) {
          //  alert(response);
            $.facebox(response, 'facebox-medium');
            bindDatetimePicker("#grpcls_start_datetime");
        });
}

</script>
