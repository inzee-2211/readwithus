<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->developerTags['colClassPrefix'] = 'col-sm-';
$frm->developerTags['fld_default_col'] = 12;
$fld = $frm->getField('remember_me');
$fld->setWrapperAttribute('class', 'set-remember');
$fldPassword = $frm->getField('password');
$fldPassword->changeCaption('');
$fldPassword->captionWrapper = [
    Label::getLabel('LBL_Password'),
    '<a onClick="toggleLoginPassword(this)" href="javascript:void(0)" class="-link-underline -float-right link-color" data-show-caption="' .
    Label::getLabel('LBL_Show_Password') . '" data-hide-caption="' . Label::getLabel('LBL_Hide_Password') . '">' . Label::getLabel('LBL_Show_Password') . '</a>'
];
$frm->setFormTagAttribute('onsubmit', 'signinSetup(this); return(false);');
?>
<section class="section section--gray section--page">
    <div class="container container--fixed">
        <div class="row justify-content-center">
            <div class="col-sm-10 col-lg-7 col-xl-4">
                <div class="box -skin">
                    <div class="box__head -align-center">
                        <h4 class="-border-title"><?php echo Label::getLabel('LBL_LOGIN'); ?></h4>
                    </div>
                    <div class="box__body -padding-40 div-login-form">
                        <?php
                        $this->includeTemplate('guest-user/_partial/learner-social-media-signup.php');
                        echo $frm->getFormHtml();
                        ?>
                          <div class="-align-center">
        <p><?php echo Label::getLabel('LBL_DO_NOT_HAVE_AN_ACCOUNT?'); ?> <a href="javascript:void(0);" onClick="signupForm()" class="-link-underline link-color"><?php echo Label::getLabel('LBL_REGISTER'); ?></a></p>
    </div>
                        <div class="-align-center">
                            <a href="<?php echo MyUtility::makeUrl('GuestUser', 'forgotPassword'); ?>" class="-link-underline"><?php echo Label::getLabel('LBL_Forgot_Password?'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>