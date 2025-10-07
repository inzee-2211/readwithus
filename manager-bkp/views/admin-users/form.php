<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupAdminUser(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$userNameFld = $frm->getField('admin_username');
$userNameFld->addFieldTagAttribute('id', 'admin_username');
if ($admin_id > 0) {
    $userNameFld->addFieldTagAttribute('disabled', 'disabled');
}
$emailFld = $frm->getField('admin_email');
$emailFld->addFieldTagAttribute('id', 'admin_email');
if ($admin_id > 0) {
    $emailFld->addFieldTagAttribute('disabled', 'disabled');
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Admin_User_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="border-box border-box--space">
            <?php echo $frm->getFormHtml(); ?>
        </div>
    </div>						
</section>
<script>
    $("[name='admin_timezone']").select2();
</script>
