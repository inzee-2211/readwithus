<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
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
                        <li><a class="active" href="javascript:void(0);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <?php
                        $inactive = ($categoryId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                        ?>
                            <li class=" lang-li-js <?php echo $inactive; ?>">
                                <a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php if ($categoryId > 0) { ?> onclick="langForm(<?php echo $categoryId; ?>, <?php echo $langId; ?>);" <?php } ?>>
                                    <?php echo $langName; ?>
                                </a>
                            </li>
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