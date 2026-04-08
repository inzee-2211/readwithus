<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="box box--narrow">
    <h2 class="-align-center"><?php echo Label::getLabel('LBL_REGISTER'); ?></h2>
    <?php $this->includeTemplate('guest-user/_partial/learner-social-media-signup.php'); ?>
    <?php
    $privacyPolicyLink = empty($privacyPolicyLink) ? 'javascript:void();' : $privacyPolicyLink;
    $termsConditionsLink = empty($termsConditionsLink) ? 'javascript:void();' : $termsConditionsLink;
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-sm-';
    $frm->developerTags['fld_default_col'] = 12;
    $frm->setFormTagAttribute('onsubmit', 'signupSetup(this); return(false);');
    $fldFirstName = $frm->getField('user_first_name');
    $fldFirstName->developerTags['col'] = 6;
    $fldLastName = $frm->getField('user_last_name');
    $fldLastName->developerTags['col'] = 6;
    $fld = $frm->getField('agree');
    $fld->setWrapperAttribute('class', 'set-remember');
    $fldPassword = $frm->getField('user_password');
    $fldPassword->changeCaption('');
    $fldPassword->captionWrapper = (array(Label::getLabel('LBL_Password') . '<span class="spn_must_field">*</span><a onClick="togglePassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password'), '</a>'));
    $termLink = ' <a target="_blank" class = "-link-underline link-color" href="' . $termsConditionsLink . '">' . Label::getLabel('LBL_TERMS_AND_CONDITION') . '</a> and <a href="' . $privacyPolicyLink . '" target="_blank" class = "-link-underline link-color" >' . Label::getLabel('LBL_Privacy_Policy') . '</a>';
    $terms_caption = '<span>' . $termLink . '</span>';
    $frm->getField('agree')->addWrapperAttribute('class', 'terms_wrap');
    $frm->getField('agree')->htmlAfterField = $terms_caption;
    $fldUserPhoneCode = $frm->getField('user_phone_code');
    $fldUserPhoneCode->developerTags['col'] = 4;
    $fldUserPhoneNo = $frm->getField('user_phone_number');
    $fldUserPhoneNo->developerTags['col'] = 8;
    echo $frm->getFormHtml();
    ?>
    <div class="-align-center">
        <p><?php echo Label::getLabel('LBL_ALREADY_HAVE_AN_ACCOUNT?'); ?> <a href="javascript:void(0);" onClick="signinForm()" class="-link-underline link-color"><?php echo Label::getLabel('LBL_Sign_In'); ?></a></p>
    </div>
</div>
<script>
    $(document).ready(function () {
        $('#termLabelWrapper label').addClass('field_resp_block');
        $('#termLabelWrapper label').append('<?php echo $termLink; ?>');
    })
</script>