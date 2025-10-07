<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$search->setFormTagAttribute('onsubmit', 'searchMeetingTool(this); return(false);');
$search->setFormTagAttribute('class', 'web_form');
$search->setFormTagAttribute('id', 'frmSearch');
$search->getField('btn_clear')->addFieldtagAttribute('onclick', 'clearSearch();');
$submitBtn = $search->getField('btn_submit');
$submitBtn->developerTags['col'] = 6;
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Meeting_Tools'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div> 
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $search->getFormHtml(); ?>    
                    </div>
                </section> 		
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="listing"> <?php echo Label::getLabel('LBL_Processing...'); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div>
</div>