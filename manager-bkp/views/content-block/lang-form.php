<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$blockLangFrm->setFormTagAttribute('class', 'web_form layout--' . $formLayout);
$blockLangFrm->setFormTagAttribute('onsubmit', 'setupBlockLang(this); return(false);');
$blockLangFrm->developerTags['colClassPrefix'] = 'col-md-';
$blockLangFrm->developerTags['fld_default_col'] = 12;
$edFld = $blockLangFrm->getField('epage_content');
$edFld->htmlBeforeField = '<br/><a class="themebtn btn-primary" onClick="resetToDefaultContent();" href="javascript:void(0)">Reset Editor Content to default</a>';
?>
<!-- editor's default content[ -->
<div id="editor_default_content" style="display:none;">
    <?php echo (isset($epageData)) ? html_entity_decode($epageData['epage_default_content']) : ''; ?>
</div>
<!-- ] -->
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Content_Block_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a  href="javascript:void(0)" onclick="addBlockForm(<?php echo $epage_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php foreach ($languages as $langId => $langName) { ?>
                            <li >
                                <a class="<?php echo ( $epage_lang_id == $langId) ? 'active' : '' ?>" href="javascript:void(0);"  onclick="addBlockLangForm(<?php echo $epage_id ?>, <?php echo $langId; ?>);" >
                                    <?php echo $langName; ?>
                                </a>
                            </li>
                        <?php } ?>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php
                            echo $blockLangFrm->getFormTag();
                            echo $blockLangFrm->getFormHtml(false);
                            echo '</form>';
                            ?>	
                        </div>
                    </div>
                </div>	
            </div>
        </div>
    </div>
</section>
