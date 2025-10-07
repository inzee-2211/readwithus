<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Contribution_Detail'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="border-box border-box--space">
            <table class="table table-responsive table--hovered">
                <tr>
                    <td style="width: 35%;"><?php echo Label::getLabel('LBL_FULL_NAME'); ?></td>
                    <td><?php echo ucfirst($data['bcontributions_author_first_name'] . ' ' . $data['bcontributions_author_last_name']); ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_EMAIL'); ?></td>
                    <td><?php echo $data['bcontributions_author_email']; ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_PHONE'); ?></td>
                    <td><?php echo $data['bcontributions_author_phone']; ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_POSTED_ON'); ?></td>
                    <td><?php echo MyDate::formatDate($data['bcontributions_added_on']); ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_STATUS'); ?></td>
                    <td><?php echo $statusArr[$data['bcontributions_status']]; ?></td>
                </tr>
                <?php if (!empty($fileData)) { ?>
                    <tr>
                        <td><?php echo Label::getLabel('LBL_ATTACHED_FILE'); ?></td>
                        <td><a target="_new" href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_BLOG_CONTRIBUTION, $fileData['file_record_id']]); ?>"><?php echo $fileData['file_name']; ?></a></td>
                    </tr>
                <?php } ?>
            </table>
            <div class="repeatedrow">
                <div class="form_horizontal">
                    <h3><i class="ion-person icon"></i><?php echo Label::getLabel('LBL_Update_Status'); ?></h3>
                </div>
                <div class="rowbody">
                    <div class="listview">
                        <?php
                        $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                        $frm->developerTags['colClassPrefix'] = 'col-sm-';
                        $frm->developerTags['fld_default_col'] = '12';
                        $frm->setFormTagAttribute('onsubmit', 'updateStatus(this); return(false);');
                        echo $frm->getFormHtml();
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>