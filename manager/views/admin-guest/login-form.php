<?php

 
defined('SYSTEM_INIT') or die('Invalid Usage.');
$userNameFld = $frm->getField('username');
$userNameFld->addFieldTagAttribute('placeholder', Label::getlabel('LBL_Username'));
$passwordFld = $frm->getField('password');
$passwordFld->addFieldTagAttribute('placeholder', Label::getlabel('LBL_Password'));
$rememberMeFld = $frm->getField('rememberme');
$rememberMeFld->addFieldTagAttribute('class', 'switch-labels');
 
?>
<div class="page__cell">
    <div class="container container--narrow">
        <div class="box box--white">
            <figure class="logo"><img title="<?php echo FatApp::getConfig("CONF_WEBSITE_NAME_" . $siteLangId); ?>" src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_ADMIN_LOGO, 0, Afile::SIZE_SMALL, $siteLangId]); ?>" alt="<?php echo FatApp::getConfig("CONF_WEBSITE_NAME_" . $siteLangId); ?>"></figure>
            <div class="box__centered box__centered--form">
                <?php echo $frm->getFormTag(); ?>
                <div class="row">
                    <div class="col-md-12">
                        <div class="field-set">									
                            <div class="field_cover field_cover--user"><?php echo $frm->getFieldHTML('username'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="field-set">
                            <div class="field_cover field_cover--lock"><?php echo $frm->getFieldHTML('password'); ?></div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="field-set">
                            <label class="statustab -txt-uppercase">
                                <?php
                                $remeberfld = $frm->getFieldHTML('rememberme');
                                $remeberfld = str_replace("<label>", "", $remeberfld);
                                $remeberfld = str_replace("</label>", "", $remeberfld);
                                echo $remeberfld;
                                ?>
                                <i class="switch-handles"></i>
                                <?php echo Label::getlabel('LBL_Remember_me'); ?>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="field-set">
                            <a href="<?php echo MyUtility::makeUrl('adminGuest', 'forgotPasswordForm'); ?>" class="-link-underline -txt-uppercase -float-right"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></a>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="field-set">
                            <?php echo $frm->getFieldHTML('btn_submit'); ?>
                        </div>
                    </div>
                </div>
                <?php echo $frm->getExternalJS(); ?>
                </form>
            </div>
        </div>
    </div>
