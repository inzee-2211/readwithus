<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;

$fld = $frm->getField('certpl_image');
$fld->setFieldTagAttribute('onchange', 'setupMedia();');

$fld = $frm->getField('certpl_image');
$fld->htmlAfterField = '<div id="image-listing">';
$fld->htmlAfterField .= '<ul class="grids--onethird"><li><div class="logothumb"> <img src="' . MyUtility::makeUrl('image', 'show', [Afile::TYPE_CERTIFICATE_BACKGROUND_IMAGE, $certId, Afile::SIZE_SMALL, $langId], CONF_WEBROOT_BACKEND) . '?' . time() . '"> </div></li></ul>';

$fld->htmlAfterField .= '</div>';
$fld->htmlAfterField .= '<div style="margin-top:15px;" >' . str_replace('{dimensions}', implode('x', $dimensions), Label::getLabel('LBL_PREFERRED_DIMENSIONS_{dimensions}')) . '</div>';
$fld->htmlAfterField .= '<div style="margin-top:15px;">' . str_replace('{ext}', $imageExts, Label::getLabel('LBL_ALLOWED_FILE_EXTS_{ext}')) . '</div>';


$fld = $frm->getField('certpl_lang_id');
$fld->setFieldTagAttribute('onchange', 'mediaForm("'.$certTplCpde.'", this.value); return false;');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_CERTIFICATE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li>
                            <a class="" href="javascript:void(0)" onclick="edit('<?php echo $certTplCpde ?>', '<?php echo $langId ?>');"><?php echo Label::getLabel('LBL_General'); ?></a>
                        </li>
                        <li>
                            <a class="active" href="javascript:void(0)" onclick="mediaForm('<?php echo $certTplCpde ?>', '<?php echo $langId ?>');"><?php echo Label::getLabel('LBL_Media'); ?></a>
                        </li>
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