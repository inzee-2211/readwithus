<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='container container-fluid'>
        <div class="row">
            <div class="col-lg-12 col-md-12 space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-auto">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Sales_Report'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col-lg-auto">
                            <div class="buttons-group">
                                <span class="-color-secondary span-right" id="regendatedtime"><?php echo $regendatedtime; ?></span>
                                <a href="javascript:void(0);" onclick="regenerate();" class="btn-primary"><?php echo Label::getLabel('LBL_REGENERATE'); ?></a>
                            </div>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php
                        $frm->setFormTagAttribute('onsubmit', 'search(this); return(false);');
                        $frm->setFormTagAttribute('class', 'web_form');
                        $fld = $frm->getField('btn_clear');
                        $fld->setFieldTagAttribute('onclick', 'clearSearch()');
                        echo $frm->getFormHtml();
                        ?>    
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