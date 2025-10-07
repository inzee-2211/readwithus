<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'return false;');
$fld = $frm->getField('biblecontent_url');
$fld->htmlAfterField = "<small>" . Label::getLabel("HTMLAFTERFIELD_VIDEO_CONTENT_URL_TEXT") . ".</small>";

?>
<div class="col-sm-12">
    <h1><?php echo Label::getLabel('LBL_Video_Content'); ?></h1>
    <div class="tabs_nav_container responsive flat">
        <ul class="tabs_nav">
            <li><a class="active" href="javascript:void(0)" onclick="addForm(<?php echo $contentId ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
            <?php
            $inactive = ($contentId == 0) ? 'fat-inactive' : '';
            foreach ($languages as $langId => $langName) {
                ?>
                <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($contentId > 0) { ?> onclick="addLangForm(<?php echo $contentId ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
            <?php } ?>
        </ul>
        <div class="tabs_panel_wrap">
            <div class="tabs_panel">
                <?php echo $frm->getFormHtml(); ?>
            </div>
        </div>
    </div>
</div>
