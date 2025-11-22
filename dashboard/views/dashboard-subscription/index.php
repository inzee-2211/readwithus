<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$subscription = $subscription ?? [];
$package = $package ?? [];
$subjects = $subjects ?? [];
$courseCount = $courseCount ?? 0;
?>

<div class="card">
    <div class="card-header">
        <h5 class="card-title"><?php echo Label::getLabel('LBL_MY_SUBSCRIPTION'); ?></h5>
    </div>
    <div class="card-body">
        <?php if (!empty($subscription)): ?>
            <div class="subscription-details">
                <div class="row">
                    <div class="col-md-6">
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_PLAN'); ?>:</label>
                            <span><?php echo $package['spackage_name'] ?? 'N/A'; ?></span>
                        </div>
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_STATUS'); ?>:</label>
                            <span class="badge badge-success"><?php echo ucfirst($subscription['usubs_status']); ?></span>
                        </div>
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_START_DATE'); ?>:</label>
                            <span><?php echo FatDate::format($subscription['usubs_start_date']); ?></span>
                        </div>
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_END_DATE'); ?>:</label>
                            <span><?php echo FatDate::format($subscription['usubs_end_date']); ?></span>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_SELECTED_SUBJECTS'); ?>:</label>
                            <span><?php echo count($subjects); ?> / <?php echo $package['spackage_subject_limit'] ?? 0; ?></span>
                        </div>
                        <div class="info-box">
                            <label><?php echo Label::getLabel('LBL_ACCESSIBLE_COURSES'); ?>:</label>
                            <span><?php echo $courseCount; ?></span>
                        </div>
                    </div>
                </div>

                <?php if (!empty($subjects)): ?>
                    <div class="selected-subjects margin-top-4">
                        <h6><?php echo Label::getLabel('LBL_YOUR_SUBJECTS'); ?></h6>
                        <div class="subject-tags">
                            <?php foreach ($subjects as $subject): ?>
                                <span class="badge badge-primary"><?php echo $subject['subject']; ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="action-buttons margin-top-4">
                    <a href="<?php echo MyUtility::makeUrl('DashboardSubscription', 'manageSubjects'); ?>" 
                       class="btn btn-primary">
                        <?php echo Label::getLabel('LBL_MANAGE_SUBJECTS'); ?>
                    </a>
                    
                    <a href="<?php echo MyUtility::makeUrl('DashboardSubscription', 'upgrade'); ?>" 
                       class="btn btn-outline-primary">
                        <?php echo Label::getLabel('LBL_UPGRADE_PLAN'); ?>
                    </a>
                    
                    <button type="button" onclick="cancelSubscription()" 
                            class="btn btn-outline-danger">
                        <?php echo Label::getLabel('LBL_CANCEL_SUBSCRIPTION'); ?>
                    </button>
                </div>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <div class="empty-state">
                    <svg class="icon icon--subscription icon--64 color-gray-500">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#subscription"></use>
                    </svg>
                    <h4><?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?></h4>
                    <p><?php echo Label::getLabel('LBL_YOU_DONT_HAVE_ACTIVE_SUBSCRIPTION'); ?></p>
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
function cancelSubscription() {
    if (!confirm('<?php echo Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_CANCEL_SUBSCRIPTION'); ?>')) {
        return;
    }
    
    $.ajax({
        url: '<?php echo MyUtility::makeUrl("DashboardSubscription", "cancel"); ?>',
        type: 'POST',
        dataType: 'json',
        beforeSend: function() {
            showLoader();
        }
    }).done(function(response) {
        if (response.status) {
            $.systemMessage(response.msg, 'alert--success');
            if (response.redirectUrl) {
                setTimeout(function() {
                    window.location.href = response.redirectUrl;
                }, 1000);
            }
        } else {
            $.systemMessage(response.msg, 'alert--danger');
        }
    }).always(function() {
        hideLoader();
    });
}

function goToSubscription() {
    window.location.href = '<?php echo MyUtility::makeUrl("DashboardSubscription"); ?>';
}
</script>