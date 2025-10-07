<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$idFld = $frm->getField('tlang_id');
$idFld->addFieldTagAttribute('id', 'tlang_id');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_TEACHING_LANGUAGE_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0);" onclick="form(<?php echo $tLangId ?>);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                        <?php
                        $inactive = ($tLangId == 0) ? 'fat-inactive' : '';
                        $mediaForm = ($tLangId > 0) ? 'onclick="mediaForm(' . $tLangId . ');"' : '';
                        foreach ($languages as $langId => $langName) {
                            $langForm = (intval($tLangId) > 0) ? 'onclick="langForm(' . $tLangId . ',' . $langId . ');"' : '';
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php echo $langForm; ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php echo $mediaForm; ?> ><?php echo Label::getLabel('LBL_MEDIA'); ?></a></li>
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
<script>
    $('input[name="tlang_slug"]').on('change', function (e) {
        var slug = $(this).val();
        slug = slug.trim(slug.toLowerCase());
        slug = slug.replace(/[\s,<>\/\"&#%+?$@=]/g, "-");
        slug = slug.replace(/[\s\s]+/g, '-');
        slug = slug.replace(/[\-]+/g, '-');
        $(this).val(slug);
        if (slug != '') {
            checkUnique($(this), 'tbl_teach_languages', 'tlang_slug', 'tlang_id', $('#tlang_id'), []);
        }
    });
</script>