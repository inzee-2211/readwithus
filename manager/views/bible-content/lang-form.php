<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$bibleLangFrm->setFormTagAttribute('onsubmit', 'setupLang(this); return(false);');
$bibleLangFrm->developerTags['colClassPrefix'] = 'col-md-';
$bibleLangFrm->developerTags['fld_default_col'] = 12;
$bibleLangFrm->setFormTagAttribute('class', 'web_form form_horizontal layout--' . $formLayout);
?>
<div class="col-sm-12">
    <h1><?php echo Label::getLabel('LBL_VIDEO_CONTENT') ?></h1>
    <div class="tabs_nav_container responsive flat">
        <ul class="tabs_nav">
            <li><a href="javascript:void(0);" onclick="addForm(<?php echo $biblecontent_id ?>);"><?php echo Label::getLabel('LBL_GENERAL') ?></a></li>
            <?php
            if ($biblecontent_id > 0) {
                foreach ($languages as $langId => $langName) {
                    ?>
                    <li><a class="<?php echo ($bible_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);" 
                           onclick="addLangForm(<?php echo $biblecontent_id ?>, <?php echo $langId; ?>);"><?php echo $langName; ?></a></li>
                        <?php
                    }
                }
                ?>
        </ul>
        <div class="tabs_panel_wrap">
            <div class="tabs_panel">
                <?php
                echo $bibleLangFrm->getFormTag();
                echo $bibleLangFrm->getFormHtml(false);
                echo '</form>';
                ?>				
            </div>
        </div>
    </div>	
</div>
