<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Manage_Content_Blocks'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div> 
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <div id="blockListing"> <?php echo Label::getLabel('LBL_Processing...'); ?></div>
                        </div> 
                    </div>
                </section>
            </div>		
        </div>
    </div>
</div>
<?php if (isset($epage_id) && $epage_id > 0) { ?>
    <script type="text/javascript">
        $(document).ready(function () {
            addBlockForm(<?php echo $epage_id; ?>);
        });
    </script>
    <?php
}
?>