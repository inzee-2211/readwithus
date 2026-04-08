<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
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
$fldPassword = $frm->getField('user_password');
$fldPassword->changeCaption('');
$fldPassword->captionWrapper = (array(Label::getLabel('LBL_Password') . '<span class="spn_must_field">*</span><a onClick="togglePassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' . Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password'), '</a>'));
$termLink = ' <a target="_blank" class = "-link-underline link-color" href="' . $termsConditionsLink . '">' . Label::getLabel('LBL_TERMS_AND_CONDITION') . '</a> and <a href="' . $privacyPolicyLink . '" target="_blank" class = "-link-underline link-color" >' . Label::getLabel('LBL_Privacy_Policy') . '</a>';
$terms_caption = '<span>' . $termLink . '</span>';
$frm->getField('agree')->addWrapperAttribute('class', 'terms_wrap set-remember');
$frm->getField('agree')->htmlAfterField = $terms_caption;
$fldUserPhoneCode = $frm->getField('user_phone_code');
$fldUserPhoneCode->developerTags['col'] = 3;
$fldUserPhoneNo = $frm->getField('user_phone_number');
$fldUserPhoneNo->developerTags['col'] = 9;
$frm->getField('user_is_parent')->setWrapperAttribute('class', 'set-remember');
?>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-lg-7 col-xl-6">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_REGISTER'); ?></h4>
                    </div>
                    <div class="box__body -padding-40 div-login-form">
                        <?php
                        $this->includeTemplate('guest-user/_partial/learner-social-media-signup.php');
                        echo $frm->getFormHtml();
                        ?>
                        <div class="-align-center">
                            <p><?php echo Label::getLabel('LBL_ALREADY_HAVE_AN_ACCOUNT?'); ?> <a
                                    href="javascript:void(0);" onClick="signinForm()"
                                    class="-link-underline link-color"><?php echo Label::getLabel('LBL_Sign_In'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $(document).ready(function () {
        $('#termLabelWrapper label').addClass('field_resp_block');
        $('#termLabelWrapper label').append('<?php echo $termLink; ?>');
    })
</script>