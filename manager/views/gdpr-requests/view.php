<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_REQUEST_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_Username'); ?>:</strong> <?php echo $data['user_first_name'] . ' ' . $data['user_last_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_Request_Added'); ?>:</strong> <?php echo MyDate::formatDate($data['gdpreq_added_on']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_REQUEST_MODIFIED'); ?>:</strong> <?php echo ($data['gdpreq_status'] == GdprRequest::STATUS_PENDING) ? Label::getLabel('LBL_N/A') : MyDate::formatDate($data['gdpreq_updated_on']); ?></td>
                </tr>
                <tr>
                    <td colspan="3"><strong><?php echo Label::getLabel('LBL_Erasure_Request_Reason'); ?>:</strong>
                        <?php echo $data['gdpreq_reason']; ?>
                    </td>
                </tr>
            </tbody>
        </table>
        <?php if ($data['gdpreq_status'] == GdprRequest::STATUS_PENDING) { ?>
            <div class="repeatedrow">
                <br>
                <h3><?php echo Label::getLabel('LBL_Change_Status'); ?></h3>
                <div class="rowbody space">
                    <div class="listview">
                        <?php
                        $frm->setFormTagAttribute('class', 'web_form form_horizontal');
                        $frm->setFormTagAttribute('onsubmit', 'updateStatus(this); return false;');
                        $frm->developerTags['colClassPrefix'] = 'col-sm-';
                        $frm->developerTags['fld_default_col'] = '10';
                        echo $frm->getFormHtml();
                        ?>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
</section>