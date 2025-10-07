<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'updateStatus(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;

$fld = $frm->getField('corere_status');
$fld->setFieldTagAttribute('onChange', 'showHideCommentBox(this.value);');

$fld = $frm->getField('corere_comment');
$fld->setWrapperAttribute('id', 'remarkField');

?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_UPDATE_STATUS'); ?></h4>
    </div>
    <div class="sectionbody space">      
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frm->getFormHtml(); ?>
                    <small>
                    <?php
                    $label = Label::getLabel('LBL_NOTE:_LEARNER_HAS_ALREADY_COMPLETED_{percent}%_OF_THE_COURSE');
                    if ($data['crspro_status'] == CourseProgress::COMPLETED) {
                        echo str_replace('{percent}', 100, $label);
                    } else {
                        echo str_replace('{percent}', $data['crspro_progress'], $label);
                    }
                    ?>
                    </small>
                </div>
            </div>						
        </div>
    </div>						
</section>
