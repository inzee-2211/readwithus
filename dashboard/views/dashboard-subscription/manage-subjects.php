<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm = $frm ?? null;
$subscription = $subscription ?? [];
$package = $package ?? [];
$limit = $limit ?? 0;
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title"><?php echo Label::getLabel('LBL_MANAGE_SUBJECTS'); ?></h5>
    </div>
    <div class="card-body">
        <?php if (!empty($subscription)): ?>
            <div class="alert alert-info">
                <strong><?php echo Label::getLabel('LBL_PLAN'); ?>:</strong> 
                <?php echo $package['spackage_name'] ?? 'N/A'; ?><br>
                <strong><?php echo Label::getLabel('LBL_SUBJECT_LIMIT'); ?>:</strong> 
                <?php echo $limit; ?> <?php echo Label::getLabel('LBL_SUBJECTS'); ?>
            </div>

            <?php echo $frm->getFormTag(); ?>
                <div class="form-group">
                    <?php echo $frm->getFieldHtml('subject_ids'); ?>
                </div>
                <div class="form-actions">
                    <?php echo $frm->getFieldHtml('btn_submit'); ?>
                    <?php echo $frm->getFieldHtml('btn_cancel'); ?>
                </div>
            </form>
            <?php echo $frm->getExternalJs(); ?>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="empty-state">
                    <p><?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?></p>
                    <a href="<?php echo MyUtility::makeUrl('Subscription', 'pricing', [], CONF_WEBROOT_FRONT_URL); ?>" 
                       class="btn btn-primary">
                        <?php echo Label::getLabel('LBL_BROWSE_PLANS'); ?>
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
$(document).ready(function() {
    $('#frmSubjects').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        $.ajax({
            url: '<?php echo MyUtility::makeUrl("DashboardSubscription", "setupSubjects"); ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            beforeSend: function() {
                showLoader();
            }
        }).done(function(response) {
            if (response.status) {
                $.systemMessage(response.msg, 'alert--success');
                setTimeout(function() {
                    window.location.href = '<?php echo MyUtility::makeUrl("DashboardSubscription"); ?>';
                }, 1000);
            } else {
                $.systemMessage(response.msg, 'alert--danger');
            }
        }).always(function() {
            hideLoader();
        });
    });
});
</script>