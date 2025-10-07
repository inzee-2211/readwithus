<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_VIEW_USER_DETAIL'); ?></h4>
    </div>
    <div class="sectionbody">
        <table class="table table--details">
            <tbody>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_NAME'); ?></strong> <?php echo $data['user_full_name']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_EMAIL'); ?></strong> <?php echo $data['user_email']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_TIMEZONE'); ?></strong> <?php echo MyDate::formatTimeZoneLabel($data['user_timezone']); ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_REG_DATE'); ?></strong> <?php echo MyDate::formatDate($data['user_created']); ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_PHONE_NO'); ?></strong> <?php echo $data['user_phone_code'] . ' ' . $data['user_phone_number']; ?></td>
                    <td><strong><?php echo Label::getLabel('LBL_COUNTRY'); ?></strong> <?php echo $data['country_name']; ?></td>
                </tr>
                <tr>
                    <td><strong><?php echo Label::getLabel('LBL_BIOGRAPHY'); ?></strong> <?php echo nl2br($data['user_biography']); ?></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>
</section>