<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
  <div class="page__head"><h1><?= Label::getLabel('LBL_EDIT_LEVEL'); ?></h1></div>
  <div class="page__body">
    <?php echo $frm->getFormHtml(); ?>
  </div>
</div>
