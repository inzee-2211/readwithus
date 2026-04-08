<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupMetaTag(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
if ($metaType == MetaTag::META_GROUP_OTHER) {
    $slugField = $frm->getField('meta_slug');
    $slugField->htmlAfterField = "<small>" . sprintf(Label::getLabel("LBL_Ex_slug_%s_%s_%s"), MyUtility::makeFullUrl('Contact'), 'contact', MyUtility::makeFullUrl('Contact')) . "</small>";
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Meta_Tag_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="editMetaTagForm(<?php echo "$metaId,'$metaType',$recordId" ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        $inactive = ($metaId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($metaId > 0) { ?> onclick="editMetaTagLangForm(<?php echo "$metaId,$langId,'$metaType'" ?>);" <?php } ?>><?php echo $langName; ?></a></li>
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