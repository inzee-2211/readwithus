<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSearch->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$frmSearch->setFormTagAttribute('id', 'search');
$frmSearch->setFormTagAttribute('class', 'web_form');
$frmSearch->getField('keyword')->addFieldtagAttribute('placeholder', Label::getLabel('LBL_SEARCH_BY_COURSE_TITLE_OR_SUBTITLE'));
$btn = $frmSearch->getField('btn_clear');
$btn->setFieldTagAttribute('onClick', 'clearSearch()');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_MANAGE_COURSE_REFUND_REQUESTS'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_SEARCH'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $frmSearch->getFormHtml(); ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"> <?php echo Label::getLabel('LBL_PROCESSING'); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
    var REFUND_DECLINED = "<?php echo Course::REFUND_DECLINED ?>";
    var refundApproved = "<?php echo $frmSearch->getField('corere_status')->value ?? 0; ?>";
    $(document).ready(function() {
        if (refundApproved > 0) {
            $('.section.searchform_filter .sectionhead').click();
        }
    });
</script>