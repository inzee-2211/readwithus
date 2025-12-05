<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

/** @var Form $frm */
$frm->setFormTagAttribute('action', MyUtility::makeUrl('SubscriptionPackages', 'save'));
$frm->setFormTagAttribute('method', 'post');
$frm->setFormTagAttribute('id', 'frmPkg');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 6;

echo $frm->getFormTag();
?>

<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <!-- Page Title + Breadcrumb + Back -->
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-options"></i></span>
                            <h5><?php echo Label::getLabel('LBL_SUBSCRIPTION_PACKAGE'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col--last">
                            <div class="buttons-group">
                                <a href="<?php echo MyUtility::makeUrl('SubscriptionPackages'); ?>"
                                   class="btn btn--secondary btn--sm">
                                    <?php echo Label::getLabel('LBL_BACK'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Form Section -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Package_Details'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="row">
                            <?php echo $frm->getFieldHtml('spackage_id'); // hidden ?>

                            <!-- Name -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_name')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_name'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_description')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_description'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Monthly Price -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_price_monthly')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_price_monthly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Yearly Price -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_price_yearly')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_price_yearly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Level -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_level_id')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_level_id'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subject Limit -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_subject_limit')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_subject_limit'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Status -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('spackage_status')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('spackage_status'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stripe Price (Monthly) -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('stripe_price_id_monthly')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('stripe_price_id_monthly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Stripe Price (Yearly) -->
                            <div class="col-md-6">
                                <div class="field-set">
                                    <div class="caption-wraper">
                                        <label class="field_label">
                                            <?php echo $frm->getField('stripe_price_id_yearly')->getCaption(); ?>
                                        </label>
                                    </div>
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('stripe_price_id_yearly'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- Trial Days -->
<div class="col-md-6">
    <div class="field-set">
        <div class="caption-wraper">
            <label class="field_label">
                <?php echo $frm->getField('spackage_trial_days')->getCaption(); ?>
            </label>
        </div>
        <div class="field-wraper">
            <div class="field_cover">
                <?php echo $frm->getFieldHtml('spackage_trial_days'); ?>
            </div>
        </div>
    </div>
</div>


                            <!-- Submit -->
                            <div class="col-md-12">
                                <div class="field-set">
                                    <div class="field-wraper">
                                        <div class="field_cover">
                                            <?php echo $frm->getFieldHtml('btn_submit'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div><!-- /.row -->
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

</form>

<script>
    // AJAX save using existing fcom helper (functionality unchanged)
    (function(){
        var frm = document.getElementById('frmPkg');
        if (!frm) return;
        frm.addEventListener('submit', function(e){
            e.preventDefault();
            fcom.ajax(
                fcom.makeUrl('SubscriptionPackages', 'save'),
                fcom.frmData(frm),
                function(ans){
                    var res = JSON.parse(ans);
                    if (res.status == 1) {
                        fcom.success(res.msg || 'Saved');
                        setTimeout(function(){
                            window.location.href = '<?php echo MyUtility::makeUrl('SubscriptionPackages'); ?>';
                        }, 600);
                    } else {
                        fcom.error(res.msg || 'Error');
                    }
                }
            );
        });
    })();
</script>
