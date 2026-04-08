<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'form form--horizontal web_form');
$frm->setFormTagAttribute('onsubmit', 'feedbackSetup(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>
<div class="box -padding-20">
    <h4><?php echo Label::getLabel('LBL_LESSON_FEEDBACK'); ?></h4>
    <?php echo $frm->getFormHtml(); ?>
</div>
<div class="gap"></div>
<script>
    $(document).ready(function () {
        $('.star-rating').barrating({
            showSelectedRating: false
        });
        $('select.star-rating').each(function () {
            $(this).before($(this).next());
        });
    });
</script>