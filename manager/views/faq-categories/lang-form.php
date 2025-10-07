<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$faqCatLangFrm->setFormTagAttribute('id', 'faqCat');
$faqCatLangFrm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$faqCatLangFrm->setFormTagAttribute('onsubmit', 'setupLang(this); return(false);');
$faqCatLangFrm->developerTags['colClassPrefix'] = 'col-md-';
$faqCatLangFrm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Faq_Category_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">		
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="faqCatForm(<?php echo $faqcat_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        if ($faqcat_id > 0) {
                            foreach ($languages as $langId => $langName) {
                                ?>
                                <li><a class="<?php echo ($faqcat_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" onclick="faqCatLangForm(<?php echo $faqcat_id ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                                <?php
                            }
                        }
                        ?>			
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $faqCatLangFrm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>	
            </div>
        </div>
    </div>
</section>