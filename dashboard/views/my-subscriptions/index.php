<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

/** @var array $subscription */
/** @var array $subjects */
/** @var string $pricingUrl */

$hasSubscription = !empty($subscription);

$packageName = '';
if ($hasSubscription) {
    $packageName = $subscription['spackage_title']
        ?? $subscription['spackage_name']
        ?? ('Package #' . ($subscription['package_id'] ?? ''));
}

$subjectLimit = $hasSubscription
    ? ($subscription['spackage_subject_limit'] ?? null)
    : null;

$startDate = $hasSubscription && !empty($subscription['start_date'])
    ? date('d M Y', strtotime($subscription['start_date']))
    : '-';

$endDate = $hasSubscription && !empty($subscription['end_date'])
    ? date('d M Y', strtotime($subscription['end_date']))
    : '-';

// Time remaining
$timeRemainingLabel = '';
if ($hasSubscription && !empty($subscription['end_date'])) {
    $now = new DateTime();
    $end = new DateTime($subscription['end_date']);

    if ($end > $now) {
        $diff      = $now->diff($end);
        $daysTotal = (int)$diff->format('%a');

        if ($daysTotal >= 1) {
            $timeRemainingLabel = $daysTotal . ' ' . Label::getLabel('LBL_DAYS_REMAINING');
        } else {
            $hours = $diff->h + ($diff->days * 24);
            $timeRemainingLabel = $hours . ' ' . Label::getLabel('LBL_HOURS_REMAINING');
        }
    } else {
        $timeRemainingLabel = Label::getLabel('LBL_PLAN_EXPIRED');
    }
}
?>

<style>
    /* Full white background just for this page area */
    .dashboard,
    .dashboard__primary,
    .page,
    .page__body {
        background: #ffffff;
        
    }

    /* Main subscription card */
    .subs-wrapper {
        background: #ffffff;
        border-radius: 16px;
        padding: 24px 24px 28px;
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
        border: 1px solid #e5e7eb;
        min-height: 100vh;
        
    }

    .subs-plan-title {
        font-size: 2.25rem;
        font-weight: 600;
        color: #111827;
        margin-bottom: 6px;
    }

    .subs-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 12px 24px;
        margin-top: 6px;
        font-size: 1.0rem;
    }

    .subs-meta__item {
        color: #4b5563;
    }

    .subs-meta__label {
        font-weight: 500;
        margin-right: 4px;
    }

    .subs-time-remaining {
        margin-top: 20px;
        font-size: 0.9rem;
        color: #111827;
    }

    .subs-divider {
        margin-top: 27px;
        margin-bottom: 22px;
        border-top: 1px solid #e5e7eb;
    }

    .subs-section-title {
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 14px;
        color: #111827;
    }

    .subs-empty-text {
        color: #6b7280;
        font-size: 0.92rem;
    }

    .subs-tags {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .subs-tag {
        display: inline-flex;
        align-items: center;
        padding: 4px 10px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        font-size: 0.85rem;
        font-weight: 500;
    }

    /* Solid danger button for cancel */
    .btn--danger-solid {
        background: #ef4444;
        border-color: #ef4444;
        color: #ffffff !important;
        margin-bottom: 26px;
        margin-top: 26px;
        margin-left: 20%;

    }

    .btn--danger-solid:hover,
    .btn--danger-solid:focus {
        background: #dc2626;
        border-color: #dc2626;
        color: #ffffff !important;
    }

    /* Page head spacing tweaks */
    .page__head {
        margin-bottom: 18px;
    }

    @media (max-width: 767px) {
        .subs-wrapper {
            padding: 16px 14px 20px;
            border-radius: 12px;
        }

        .page__head h1 {
            font-size: 1.2rem;
        }

        .subs-meta {
            flex-direction: column;
            gap: 6px;
        }

        .text-md-right {
            text-align: left !important;
        }
    }
</style>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">

            <div class="page__head d-flex justify-content-between align-items-center">
                <h1><?php echo Label::getLabel('LBL_MY_SUBSCRIPTIONS'); ?></h1>

                <?php if ($hasSubscription) { ?>
                    <a href="<?php echo $pricingUrl; ?>" class="btn btn--primary">
                        <?php echo Label::getLabel('LBL_UPGRADE_PLAN'); ?>
                    </a>
                <?php } else { ?>
                    <a href="<?php echo $pricingUrl; ?>" class="btn btn--primary">
                        <?php echo Label::getLabel('LBL_BROWSE_PLANS'); ?>
                    </a>
                <?php } ?>
            </div>

            <div class="page__body">
                <div class="subs-wrapper">
                    <?php if (!$hasSubscription) { ?>
                        <!-- No active subscription -->
                        <h4 class="subs-plan-title margin-bottom-2">
                            <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?>
                        </h4>
                        <p class="subs-empty-text margin-bottom-0">
                            <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION_DESC'); ?>
                        </p>
                        <!-- No extra button here; only top-right Browse Plans -->

                    <?php } else { ?>

                        <!-- Current subscription main row -->
                        <div class="row align-items-start">
                            <div class="col-lg-8 col-md-7 col-sm-12">
                                <h4 class="subs-plan-title">
                                    <?php echo htmlspecialchars($packageName); ?>
                                </h4>

                                <div class="subs-meta">
                                    <?php if ($subjectLimit !== null) { ?>
                                        <div class="subs-meta__item">
                                            <span class="subs-meta__label">
                                                <?php echo Label::getLabel('LBL_SUBJECT_LIMIT'); ?>:
                                            </span>
                                            <span><strong><?php echo (int)$subjectLimit; ?></strong></span>
                                        </div>
                                    <?php } ?>

                                    <div class="subs-meta__item">
                                        <span class="subs-meta__label">
                                            <?php echo Label::getLabel('LBL_STATUS'); ?>:
                                        </span>
                                        <?php if ($subscription['status'] === 'active') { ?>
                                            <span class="badge badge--success">
                                                <?php echo ucwords($subscription['status']); ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge badge--secondary">
                                                <?php echo ucwords($subscription['status']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>

                                    <div class="subs-meta__item">
                                        <span class="subs-meta__label">
                                            <?php echo Label::getLabel('LBL_START_DATE'); ?>:
                                        </span>
                                        <span><strong><?php echo $startDate; ?></strong></span>
                                    </div>

                                    <div class="subs-meta__item">
                                        <span class="subs-meta__label">
                                            <?php echo Label::getLabel('LBL_END_DATE'); ?>:
                                        </span>
                                        <span><strong><?php echo $endDate; ?></strong></span>
                                    </div>
                                </div>

                                <?php if ($timeRemainingLabel) { ?>
                                    <p class="subs-time-remaining">
                                        <?php echo Label::getLabel('LBL_TIME_REMAINING'); ?>:
                                        <strong><?php echo $timeRemainingLabel; ?></strong>
                                    </p>
                                <?php } ?>
                            </div>

                            <div class="col-lg-4 col-md-5 col-sm-12 text-md-right margin-top-4 margin-top-md-0">
                                <button
                                    type="button"
                                    class="btn btn--danger-solid"
                                    onclick="MySubscriptions.cancelSubscription(<?php echo (int)$subscription['user_sub_id']; ?>);">
                                    <?php echo Label::getLabel('LBL_CANCEL_SUBSCRIPTION'); ?>
                                </button>
                            </div>
                        </div>

                        <!-- Divider + subjects section -->
                        <div class="subs-divider"></div>

                        <div>
                            <h4 class="subs-section-title">
                                <?php echo Label::getLabel('LBL_UNLOCKED_SUBJECTS'); ?>
                            </h4>

                            <?php if (empty($subjects)) { ?>
                                <p class="subs-empty-text">
                                    <?php echo Label::getLabel('LBL_NO_SUBJECTS_SELECTED_YET'); ?>
                                </p>
                            <?php } else { ?>
                                <div class="subs-tags">
                                    <?php foreach ($subjects as $subject) { ?>
                                        <span class="subs-tag">
                                            <?php echo htmlspecialchars($subject['subject_title']); ?>
                                        </span>
                                    <?php } ?>
                                </div>
                            <?php } ?>
                        </div>

                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var MySubscriptions = (function () {
    function cancelSubscription(userSubId) {
        if (!confirm('<?php echo Label::getLabel('MSG_CONFIRM_CANCEL_SUBSCRIPTION'); ?>')) {
            return;
        }

        fcom.ajax(
            fcom.makeUrl('MySubscriptions', 'cancel'),
            {user_sub_id: userSubId},
            function (res) {
                try {
                    var ans = JSON.parse(res);
                    if (ans.status == 1) {
                        $.mbsmessage(ans.msg, true, 'alert--success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        $.mbsmessage(ans.msg, true, 'alert--danger');
                    }
                } catch (e) {
                    $.mbsmessage('<?php echo Label::getLabel('MSG_SOMETHING_WENT_WRONG'); ?>', true, 'alert--danger');
                }
            }
        );
    }

    return {
        cancelSubscription: cancelSubscription
    };
})();
</script>
