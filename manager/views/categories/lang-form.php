<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
$frm->setFormTagAttribute('onsubmit', 'langSetup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$langFld = $frm->getField('catelang_lang_id');
$langFld->addFieldTagAttribute('class', 'hide');
$langFld->setWrapperAttribute('class', 'hide');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_CATEGORY_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a href="javascript:void(0);" onclick="categoryForm(<?php echo $categoryId ?>);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <?php foreach ($languages as $id => $langName) { ?>
                            <li><a class="<?php echo ($langFld->value == $id) ? 'active' : '' ?>" href="javascript:void(0);" data-id="<?php echo $id; ?>" onclick="langForm(<?php echo $categoryId ?>, <?php echo $id; ?>);"><?php echo $langName; ?></a></li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>