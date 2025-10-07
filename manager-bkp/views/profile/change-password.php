<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first col-lg-6">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_CHANGE_PASSWORD'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <div class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Change_Password'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <?php
                        $pwdFrm->addFormTagAttribute('class', 'web_form form_horizontal');
                        $pwdFrm->setFormTagAttribute('autocomplete', 'off');
                        echo $pwdFrm->getFormtag();
                        ?> 
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_CURRENT_PASSWORD'); ?><span class="spn_must_field">*</span></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php
                                            $curPwd = $pwdFrm->getField('current_password');
                                            $curPwd->setFieldTagAttribute('autocomplete', 'off');
                                            echo $pwdFrm->getFieldHTML('current_password');
                                            ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_NEW_PASSWORD'); ?><span class="spn_must_field">*</span></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $pwdFrm->getFieldHTML('new_password'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>							
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"><?php echo Label::getLabel('LBL_CONFIRM_NEW_PASSWORD'); ?><span class="spn_must_field">*</span></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $pwdFrm->getFieldHTML('conf_new_password'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>	
                        <div class="row">
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label"></label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $pwdFrm->getFieldHTML('btn_submit'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>		
                        </form>
                        <?php echo $pwdFrm->getExternalJS(); ?>
                    </div>
                </div>               
            </div>     
        </div>
    </div>
</div>  