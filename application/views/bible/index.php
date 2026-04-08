<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--gray">
    <div class="container container--fixed">
        <div class="row d-block -clearfix">
            <div class="col-xl-12 col-lg-12">
                <p class="pagCount"><?php echo Label::getLabel('LBL_Showing'); ?> <span id="start_record">{xx}</span>-<span id="end_record">{xx}</span> <?php echo Label::getLabel('LBL_of'); ?> <span id="total_records">{xx}</span> <?php echo Label::getLabel('LBL_Bible'); ?></p>
            </div>
            <div class="col-xl-12 col-lg-12 order-2 -float-right" id="bibleListingContainer"></div>
        </div>
    </div>
</section>