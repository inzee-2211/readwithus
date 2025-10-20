<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="message-display no-skin">
    <div class="message-display__media">
        <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
    </div>
    <h4><?php echo $msg ?? Label::getLabel("LBL_NO_QUIZ_AVAILABLE_FOR_THIS_LECTURE"); ?></h4>
</div>