<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$srchFrm->setFormTagAttribute('onsubmit', 'search(this); return(false);');
$srchFrm->setFormTagAttribute('id', 'frmSearch');
$srchFrm->setFormTagAttribute('class', 'web_form');
$srchFrm->developerTags['colClassPrefix'] = 'col-md-';
$srchFrm->developerTags['fld_default_col'] = 3;
$srchFrm->getField('keyword')->addFieldtagAttribute('class', 'search-input');
$srchFrm->getField('btn_reset')->addFieldtagAttribute('onclick', 'clearSearch();');
 
?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                  
            <div class="row align-items-center justify-content-between">
            <!-- Left side: Icon + Title -->
            <div class="col-md-6 col--first d-flex align-items-center">
            <span class="page__icon me-2"><i class="ion-android-star"></i></span>
            <h5 class="mb-0"><?php echo Label::getLabel('LBL_REQUEST_TUTOR'); ?></h5>
            </div>


            </div>

                </div>
                <section class="section  ">
                     
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