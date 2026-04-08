<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = '12';
if ($lang_id > 0) {
    $frm->setFormTagAttribute('onsubmit', 'setupLang(this); return(false);');
} else {
    $frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
}
if (!$canEdit || $frmType == Configurations::FORM_MEDIA) {
    $submitBtn = $frm->getField('btn_submit');
    $frm->removeField($submitBtn);
}
$tbid = isset($tabId) ? $tabId : 'tabs_' . $frmType;
switch ($frmType) {
    case Configurations::FORM_OPTIONS:
        $fld = $frm->getField('CONF_GROUP_CLASS_DURATION');
        $fld->setWrapperAttribute('class', 'form__list--check');
        $registrationApproval = $frm->getField('CONF_ADMIN_APPROVAL_REGISTRATION');
        $registrationApproval->setFieldTagAttribute('id', 'registrationApproval');
        $registrationApproval->setFieldTagAttribute('class', 'registration-js');
        $registrationVerification = $frm->getField('CONF_EMAIL_VERIFICATION_REGISTRATION');
        $registrationVerification->setFieldTagAttribute('id', 'registrationVerification');
        $registrationVerification->setFieldTagAttribute('class', 'registration-js');
        $autoRegistration = $frm->getField('CONF_AUTO_LOGIN_REGISTRATION');
        $autoRegistration->setFieldTagAttribute('id', 'autoRegistration');
        $autoRegistration->setFieldTagAttribute('class', 'registration-js');
        break;
    case Configurations::FORM_MEDIA:
        $frm->developerTags['fld_default_col'] = '6';
        $adminLogoFld = $frm->getField('admin_logo');
        $desktopLogoFld = $frm->getField('front_logo');
        $certLogoFld = $frm->getField('certificate_logo');
        $emailLogoFld = $frm->getField('email_logo');
        $faviconFld = $frm->getField('favicon');
        $paymentPageLogo = $frm->getField('payment_page_logo');
        $appleTouchIcon = $frm->getField('apple_touch_icon');
        $blogImg = $frm->getField('blog_img');
        $lessonImg = $frm->getField('lesson_img');
        $applyToTeachImage = $frm->getField('apply_to_teach_banner');
        if ($canEdit) {
            $adminLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '200*100');
            $desktopLogoFld->htmlAfterField = str_replace(['{width}', '{height}'], ['200', '67'], Label::getLabel('LBL_For_best_view_width_{width}px_and_height_{height}px'));
            $emailLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '200*100');
            $certLogoFld->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '140*47');
            $faviconFld->htmlAfterField = str_replace(['{dimensions}', '{ext}'], ['16*16', '.ico'], Label::getLabel('LBL_FAV_DIMENSIONS_{dimensions}_AND_EXTENSION_{ext}'));
            $blogImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '1600*480');
            $lessonImg->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '2000*900');
            $appleTouchIcon->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '16*16');
            $paymentPageLogo->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '168*37');
            $applyToTeachImage->htmlAfterField = sprintf(Label::getLabel('LBL_Dimensions_%s'), '2000*900');
        } else {
            $adminLogoFld->setFieldTagAttribute('class', 'hide');
            $desktopLogoFld->setFieldTagAttribute('class', 'hide');
            $emailLogoFld->setFieldTagAttribute('class', 'hide');
            $faviconFld->setFieldTagAttribute('class', 'hide');
            $appleTouchIcon->setFieldTagAttribute('class', 'hide');
            $paymentPageLogo->setFieldTagAttribute('class', 'hide');
            $lessonImg->setFieldTagAttribute('class', 'hide');
            $blogImg->setFieldTagAttribute('class', 'hide');
            $applyToTeachImage->setFieldTagAttribute('class', 'hide');
        }
        if (!empty($mediaData[Afile::TYPE_ADMIN_LOGO])) {
            $adminLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_ADMIN_LOGO, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $adminLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_ADMIN_LOGO . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $adminLogoFld->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_FRONT_LOGO])) {
            $desktopLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_FRONT_LOGO, 0, Afile::SIZE_MEDIUM, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $desktopLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_FRONT_LOGO . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $desktopLogoFld->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_CERTIFICATE_LOGO])) {
            $certLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_CERTIFICATE_LOGO, 0, Afile::SIZE_MEDIUM, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $certLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_CERTIFICATE_LOGO . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $certLogoFld->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_PAYMENT_PAGE_LOGO])) {
            $paymentPageLogo->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_PAYMENT_PAGE_LOGO, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $paymentPageLogo->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_PAYMENT_PAGE_LOGO . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $paymentPageLogo->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_EMAIL_LOGO])) {
            $emailLogoFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_EMAIL_LOGO, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $emailLogoFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_EMAIL_LOGO . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $emailLogoFld->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_FAVICON])) {
            $faviconFld->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_FAVICON, 0, Afile::SIZE_ORIGINAL, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $faviconFld->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_FAVICON . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $faviconFld->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_APPLE_TOUCH_ICON])) {
            $appleTouchIcon->htmlAfterField .= '<div class="uploaded--image"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_APPLE_TOUCH_ICON, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '"> ';
            if ($canEdit) {
                $appleTouchIcon->htmlAfterField .= '<a  class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_APPLE_TOUCH_ICON . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $appleTouchIcon->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_BLOG_PAGE_IMAGE])) {
            $blogImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_BLOG_PAGE_IMAGE, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '">';
            if ($canEdit) {
                $blogImg->htmlAfterField .= '<a class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_BLOG_PAGE_IMAGE . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $blogImg->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_APPLY_TO_TEACH_BANNER])) {
            $applyToTeachImage->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_APPLY_TO_TEACH_BANNER, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '">';
            if ($canEdit) {
                $applyToTeachImage->htmlAfterField .= '<a class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_APPLY_TO_TEACH_BANNER . ', ' . $lang_id . ', this);" ><i class="ion-close-round"></i></a>';
            }
            $applyToTeachImage->htmlAfterField .= '</div><br>';
        }
        if (!empty($mediaData[Afile::TYPE_LESSON_PAGE_IMAGE])) {
            $lessonImg->htmlAfterField .= '<div class="uploaded--image" style="width:100%"><img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_LESSON_PAGE_IMAGE, 0, Afile::SIZE_SMALL, $lang_id]) . '?' . time() . '">';
            if ($canEdit) {
                $lessonImg->htmlAfterField .= '<a class="remove--img" href="javascript:void(0);" onclick="removeMedia(' . Afile::TYPE_LESSON_PAGE_IMAGE . ', ' . $lang_id . ', this);"><i class="ion-close-round"></i></a>';
            }
            $lessonImg->htmlAfterField .= '</div><br>';
        }
        break;
    case Configurations::FORM_THIRD_PARTY:
        $googleClientJson = $frm->getField('CONF_GOOGLE_CLIENT_JSON');
        if ($isGoogleAuthSet) {
            $googleClientJson->htmlAfterFiel = '<p class="margin-bottom-0 color-secondary">';
            if (empty($accessToken)) {
                $googleClientJson->htmlAfterField .= Label::getLabel("LBL_GOOGLE_CREDENTIALS_NOT_AUTHORIZED") . '<a  href="javascript:void(0);" onclick="googleAuthorize();">' . Label::getLabel("LBL_CLICK_HERE_TO_AUTHORIZED") . '</a>';
            } else {
                $googleClientJson->htmlAfterField .= Label::getLabel('LBL_GOOGLE_CREDENTIALS_ALREADY_AUTHORIZED');
            }
            $googleClientJson->htmlAfterField .= '</p>';
        }
        break;
}
?>
<?php if (in_array($frmType, Configurations::getLangTypeForms())) { ?>
    <ul class="tabs_nav innerul">
        <?php if ($frmType != Configurations::FORM_MEDIA) { ?>
            <li><a href="javascript:void(0)" class="<?php echo ($lang_id == 0) ? 'active' : ''; ?>" onClick="getForm(<?php echo $frmType; ?>, '<?php echo $tbid; ?>')">Basic</a></li>
        <?php } ?>
        <?php foreach ($languages as $langId => $langName) { ?>
            <li><a href="javascript:void(0);" class="<?php echo ($lang_id == $langId) ? 'active' : ''; ?>" onClick="getLangForm(<?php echo $frmType; ?>,<?php echo $langId; ?>, '<?php echo $tbid; ?>')"><?php echo $langName; ?></a></li>
        <?php } ?>
    </ul>
<?php } ?>
<div class="tabs_panel_wrap">
    <?php echo $frm->getFormHtml(); ?>

</div>