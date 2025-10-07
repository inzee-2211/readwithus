<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$applyTeachFrm->developerTags['colClassPrefix'] = 'col-md-';
$applyTeachFrm->developerTags['fld_default_col'] = 12;
$applyTeachFrm->setFormTagAttribute('class', 'form');
$applyTeachFrm->setFormTagAttribute('onsubmit', 'teacherSetup(this); return(false);');
$userEmail = $applyTeachFrm->getField('user_email');
$userEmail->setFieldTagAttribute('placeholder', Label::getLabel('LBL_EMAIL'));
$userPassword = $applyTeachFrm->getField('user_password');
$userPassword->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PASSWORD'));
$submitBtn = $applyTeachFrm->getField('btn_submit');
$submitBtn->setFieldTagAttribute('class', 'btn btn--secondary btn--large btn--block ');
?>
<section class="section section-full">
    <div class="slideshow full-view-banner">
        <picture class="hero-img">
            <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('image', 'show', [Afile::TYPE_APPLY_TO_TEACH_BANNER, 0, Afile::SIZE_LARGE], CONF_WEBROOT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="">
        </picture>
    </div>
    <div class="slideshow-content">
        <h1><?php echo Label::getLabel('LBL_APPLY_TO_TEACH'); ?></h1>
        <p><?php echo Label::getLabel('LBL_APPLY_TO_TEACH_DESCRITPION'); ?></p>
        <?php if (!empty($siteUserId)) { ?>
            <?php if (empty($siteUser['user_is_teacher'])) { ?>
                <a href="<?php echo MyUtility::makeUrl('TeacherRequest', 'form'); ?>" class="btn btn--secondary btn--large btn--block "><?php echo Label::getLabel('LBL_BECOME_A_TUTOR'); ?></a>
            <?php } ?>
            <div class="row justify-content-center margin-top-4">
                <p><?php echo Label::getLabel('LBL_FAQS_DESCRIPTION'); ?></p>
            </div>
            <div class="row">
                <div class="col-6">
                    <a href="#faq-area" class="btn btn--block btn--white scroll">
                        <?php echo Label::getLabel('LBL_FAQS'); ?>
                    </a>
                </div>
                <div class="col-6">
                    <a href="#how-it-works" class="btn btn--block btn--white scroll ">
                        <?php echo Label::getLabel('LBL_HOW_IT_WORKS'); ?>
                    </a>
                </div>
            </div>
        <?php } else { ?>
            <div class="form-register">
                <?php echo $applyTeachFrm->getFormTag(); ?>
                <?php echo $applyTeachFrm->getFieldHTML('agree'); ?>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $userEmail->getHTML(); ?>
                                    <?php echo $applyTeachFrm->getFieldHTML('user_dashboard'); ?>
                                    <?php echo $applyTeachFrm->getFieldHTML('user_id'); ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-12">
                        <div class="field-set">
                            <div class="field-wraper">
                                <div class="field_cover">
                                    <?php echo $userPassword->getHTML(); ?>
                                    <a href="javascript:void(0);" class="password-toggle">
                                        <span class="icon" id="hide-password">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.2" height="17.134" viewBox="0 0 16.2 17.134">
                                                <path id="Path_6420" data-name="Path 6420" d="M13.685,15.853a7.764,7.764,0,0,1-4.4,1.375,8.437,8.437,0,0,1-8.1-7.269,9.083,9.083,0,0,1,2.5-4.9L1.339,2.536,2.4,1.393,17.222,17.384l-1.059,1.142-2.478-2.673ZM4.74,6.2A7.383,7.383,0,0,0,2.71,9.96a7.171,7.171,0,0,0,3.846,5.031,6.307,6.307,0,0,0,6.038-.316l-1.518-1.638A3.187,3.187,0,0,1,6.9,12.532a3.852,3.852,0,0,1-.468-4.507ZM9.965,11.84,7.538,9.222a2.136,2.136,0,0,0,.419,2.166,1.774,1.774,0,0,0,2.008.452Zm5.909,1.829L14.8,12.514A7.509,7.509,0,0,0,15.852,9.96,7.262,7.262,0,0,0,12.72,5.324a6.315,6.315,0,0,0-5.272-.745L6.267,3.3a7.7,7.7,0,0,1,3.014-.614,8.437,8.437,0,0,1,8.1,7.269,9.2,9.2,0,0,1-1.506,3.709Zm-6.8-7.337a3.236,3.236,0,0,1,2.59,1.058,3.8,3.8,0,0,1,.98,2.794L9.073,6.332Z" transform="translate(-1.181 -1.393)" fill="#a2a2a2" />
                                            </svg>
                                        </span>
                                        <span class="icon" id="show-password" style="display: none;">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="16.2" height="14.538" viewBox="0 0 16.2 14.538">
                                                <path id="Path_6422" data-name="Path 6422" d="M9.281,3a8.437,8.437,0,0,1,8.1,7.269,8.436,8.436,0,0,1-8.1,7.269,8.437,8.437,0,0,1-8.1-7.269A8.436,8.436,0,0,1,9.281,3Zm0,12.922a6.873,6.873,0,0,0,6.571-5.652,6.873,6.873,0,0,0-6.57-5.647A6.873,6.873,0,0,0,2.71,10.27a6.874,6.874,0,0,0,6.571,5.653Zm0-2.019a3.509,3.509,0,0,1-3.369-3.634A3.509,3.509,0,0,1,9.281,6.634a3.509,3.509,0,0,1,3.369,3.634A3.509,3.509,0,0,1,9.281,13.9Zm0-1.615a1.95,1.95,0,0,0,1.872-2.019A1.95,1.95,0,0,0,9.281,8.25a1.95,1.95,0,0,0-1.872,2.019A1.95,1.95,0,0,0,9.281,12.288Z" transform="translate(-1.181 -3)" fill="#a2a2a2" />
                                            </svg>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <button class="btn btn--secondary btn--large btn--block" name="btn_submit" value="<?php echo $submitBtn->value; ?>"><?php echo $submitBtn->value; ?></button>
                </form>
                <?php echo $applyTeachFrm->getExternalJs(); ?>
                <div class="row justify-content-center">
                    <p>
                        <?php
                        $termsPage = FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0);
                        $privacyPage = FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0);
                        $termsLink = '<a href="' . MyUtility::makeUrl('Cms', 'view', [$termsPage]) . '" class="color-primary">' . Label::getLabel('LBL_Terms_&_Conditions') . '</a>';
                        $privacyLink = '<a href="' . MyUtility::makeUrl('cms', 'view', [$privacyPage]) . '" class="color-primary">' . Label::getLabel('LBL_Privacy_Policy') . '</a>';
                        echo sprintf(Label::getLabel('LBL_BY_SIGNING_UP_YOU_AGREE_TO_TERMS'), $termsLink, $privacyLink);
                        ?>
                    </p>
                </div>
            </div>
        <?php } ?>
    </div>
</section>
<?php echo FatUtility::decodeHtmlEntities($sectionAfterBanner); ?>
<?php echo FatUtility::decodeHtmlEntities($featuresSection); ?>
<?php echo FatUtility::decodeHtmlEntities($becometutorSection); ?>
<?php echo FatUtility::decodeHtmlEntities($staticBannerSection); ?>
<?php if (!empty($faqs)) { ?>
    <section class="section section--faq" id="faq-area">
        <div class="container container--narrow">
            <div class="section__head">
                <h2><?php echo Label::getLabel('LBL_faq_title_second'); ?></h2>
            </div>
            <div class="faq-cover">
                <div class="faq-container">
                    <?php foreach ($faqs as $ques) { ?>
                        <div class="faq-row faq-group-js">
                            <a href="javascript:void(0)" class="faq-title faq__trigger faq__trigger-js">
                                <h5><?php echo $ques['faq_title']; ?></h5>
                            </a>
                            <div class="faq-answer faq__target faq__target-js">
                                <iframe srcdoc="<?php echo $ques['faq_description']; ?>" style="border:none;width: 100%;height: 35px;" ></iframe>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
    <?php $this->includeTemplate('_partial/contact-us-section.php', ['siteLangId' => $siteLangId]); ?>
<?php } ?>
<script>
    $(".faq__trigger-js").click(function (e) {
        e.preventDefault();
        if ($(this).parents('.faq-group-js').hasClass('is-active')) {
            $(this).siblings('.faq__target-js').slideUp();
            $('.faq-group-js').removeClass('is-active');
        } else {
            $('.faq-group-js').removeClass('is-active');
            $(this).parents('.faq-group-js').addClass('is-active');
            $('.faq__target-js').slideUp();
            $(this).siblings('.faq__target-js').slideDown(0);
        }
        $iframe = $(this).siblings('.faq__target-js').children('iframe');
        height = $iframe.contents().height();
        $iframe.css({height: height + 'px'});
        $(this).siblings('.faq__target-js').css('height', height + 35 + 'px');
    });
</script>