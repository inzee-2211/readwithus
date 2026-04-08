<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form');
$frm->setFormTagAttribute('onsubmit', 'searchResources(this); return(false);');
$keywordFld = $frm->getField('keyword');
$keywordFld->addFieldTagAttribute('placeholder', Label::getLabel('LBL_KEYWORD'));
?>
<div class="facebox-panel">
    <div class="facebox-panel__head padding-bottom-6">
        <div class="d-flex align-items-center justify-content-between">
            <div>
                <h6><?php echo Label::getLabel('LBL_ATTACH_COURSE_RESOURCES'); ?></h6>
            </div>
            <div>
                <a href="javascript:void(0);" onclick="uploadResource('frmLectureForm');" class="btn btn--bordered color-secondary">
                    <svg class="icon">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#plus-more"></use>
                    </svg>
                    <?php echo Label::getLabel('LBL_ATTACH'); ?>
                </a>
            </div>
        </div>
        <div class="form-search margin-top-6">
            <?php echo $frm->getFormTag(); ?>
            <div class="form-search__field">
                <?php echo $keywordFld->getHtml(); ?>
            </div>
            <div class="form-search__action form-search__action--submit">
                <?php echo $frm->getFieldHtml('btn_submit'); ?>
                <span class="btn btn--equal btn--transparent color-black">
                    <svg class="icon icon--search icon--small">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#search"></use>
                    </svg>
                </span>
            </div>
            <?php
            echo $frm->getFieldHtml('pagesize');
            echo $frm->getFieldHtml('page');
            ?>
            </form>
            <?php echo $frm->getExternalJs(); ?>
        </div>
    </div>
    <div class="facebox-panel__body padding-0">
        <?php
        $resrcFrm->setFormTagAttribute('id', 'frmLectureForm');
        echo $resrcFrm->getFormTag();
        echo $resrcFrm->getFieldHtml('lecsrc_type');
        echo $resrcFrm->getFieldHtml('lecsrc_lecture_id');
        echo $resrcFrm->getFieldHtml('lecsrc_course_id');
        ?>
        <div class="table-scroll">
            <table class="table table--styled table--responsive" id="listingJs">
                <tr class="title-row">
                    <th></th>
                    <th><?php echo $titleLabel = Label::getLabel('LBL_FILENAME'); ?></th>
                    <th><?php echo $typeLabel = Label::getLabel('LBL_TYPE'); ?></th>
                    <th><?php echo $dateLabel = Label::getLabel('LBL_DATE'); ?></th>
                </tr>
            </table>
            <div class="show-more-container rvwLoadMoreJs padding-6" style="display:none;">
                <div class="show-more d-flex justify-content-center">
                    <a href="javascript:void(0);" class="btn btn--primary-bordered" data-page="1" onclick="resourcePaging(this)"><?php echo Label::getLabel('LBL_SHOW_MORE'); ?></a>
                </div>
            </div>
        </div>
        </form>
        <?php echo $resrcFrm->getExternalJs(); ?>
    </div>
</div>