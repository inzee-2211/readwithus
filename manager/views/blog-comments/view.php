<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Comment_Details'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="border-box border-box--space">
            <table class="table table-responsive table--hovered">
                <tr>
                    <td style="width: 35%;"><?php echo Label::getLabel('LBL_Full_Name'); ?></td>
                    <td><?php echo ucfirst($data['bpcomment_author_name']); ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_Email'); ?></td>
                    <td><?php echo $data['bpcomment_author_email']; ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_Posted_On'); ?></td>
                    <td><?php echo MyDate::formatDate($data['bpcomment_added_on']); ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_Blog_Post_Title'); ?></td>
                    <td><?php echo $data['post_title']; ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_Comment'); ?></td>
                    <td><?php echo nl2br($data['bpcomment_content']); ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_User_IP'); ?></td>
                    <td><?php echo $data['bpcomment_user_ip']; ?></td>
                </tr>
                <tr>
                    <td><?php echo Label::getLabel('LBL_User_Agent'); ?></td>
                    <td><?php echo $data['bpcomment_user_agent']; ?></td>
                </tr>
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