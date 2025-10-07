<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$srchFrm->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$srchFrm->setFormTagAttribute('id', 'frmSearch');
$srchFrm->setFormTagAttribute('class', 'web_form');
$srchFrm->developerTags['colClassPrefix'] = 'col-md-';
$srchFrm->developerTags['fld_default_col'] = 3;
$srchFrm->getField('keyword')->addFieldtagAttribute('class', 'search-input');
$srchFrm->getField('btn_reset')->addFieldtagAttribute('onclick', 'clearSearch();');
// $catefld = $srchFrm->getField('course_cateid');
// $subcatefld = $srchFrm->getField('course_subcateid');
// $subcatefld->addFieldtagAttribute('id', 'subCategories');
// $catefld->addFieldtagAttribute('onchange', 'getSubcategories(this.value);');
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-star"></i></span>
                            <h5> <?php echo Label::getLabel('LBL_MANAGE_COURSE'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4> <?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:none;">
                        <?php echo $srchFrm->getFormHtml(); ?>
                    </div>
                </section>
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <div id="listing"><?php echo Label::getLabel('LBL_Processing...'); ?></div>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function() {
        var catId = "<?php echo !empty($catefld->value) ? $catefld->value : 0; ?>";
        if (catId > 0) {
            $('.section.searchform_filter .sectionhead').click();
        }
    });




</script>