<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="tabs_panel meta-tag-tbl">
    <div class="row">
        <div class="col-sm-12">
            <?php if (!empty($frmSearch)) { ?>
                <?php if ($showFilters) { ?>
                    <section class="section searchform_filter">
                        <div class="sectionhead">
                            <h4> <?php echo Label::getLabel('LBL_SEARCH...'); ?></h4>
                        </div>
                        <div class="sectionbody space togglewrap" style="display:none;">
                            <?php
                            $frmSearch->addFormTagAttribute('class', 'web_form');
                            $frmSearch->addFormTagAttribute('onsubmit', 'searchMetaTag(this);return false;');
                            $frmSearch->setFormTagAttribute('id', 'frmSearch');
                            ($frmSearch->getField('keyword')) ? $frmSearch->getField('keyword')->addFieldtagAttribute('class', 'search-input') : NUll;
                            ($frmSearch->getField('hasTagsAssociated')) ? $frmSearch->getField('hasTagsAssociated')->addFieldtagAttribute('class', 'search-input') : NUll;
                            $submitBtn = $frmSearch->getField('btn_submit');
                            $clearbtn = $frmSearch->getField('btn_clear');
                            $submitBtn->attachField($clearbtn);
                            $clearbtn->addFieldtagAttribute('onclick', 'clearSearch();');
                            $submitBtn->developerTags['col'] = 6;
                            echo $frmSearch->getFormHtml();
                            ?>
                        </div>
                    </section>
                    <?php
                } else {
                    echo $frmSearch->getFormHtml();
                }
                ?>
            <?php } ?>
        </div>
        <div class="col-sm-12">
            <section class="section">
                <?php if ($canEdit && $canAdd) { ?>
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_OTHER_META_TAGS_LISTING'); ?></h4>
                        <a href="javascript:void(0);" onclick="addMetaTagForm(0, '<?php echo $metaType; ?>', 0)" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                    </div>
                <?php } ?>
                <div class="sectionbody">
                    <div class="tablewrap">
                        <div id="listing"> <?php echo Label::getLabel('LBL_Processing...'); ?></div>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>