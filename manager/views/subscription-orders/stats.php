<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$activeByPlan   = $activeByPlan ?? [];
$mrr            = $mrr ?? 0;
$newThisMonth   = $newThisMonth ?? 0;
$newLastMonth   = $newLastMonth ?? 0;
$canceled30     = $canceled30 ?? 0;
$churnRate      = $churnRate ?? 0;
?>

<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">

                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-stats-bars"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Subscription_Analytics'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col--last">
                            <div class="buttons-group">
                                <a href="<?php echo MyUtility::makeUrl('SubscriptionOrders'); ?>"
                                   class="btn btn--secondary btn--sm">
                                    <?php echo Label::getLabel('LBL_Back_to_List'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- KPI Cards -->
                <section class="section">
                    <div class="sectionbody space">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card card--stat">
                                    <h6><?php echo Label::getLabel('LBL_MRR_Estimate'); ?></h6>
                                    <p class="value">
                                        <?php echo number_format($mrr, 2); ?>
                                        <small>/month</small>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card--stat">
                                    <h6><?php echo Label::getLabel('LBL_New_subscriptions_this_month'); ?></h6>
                                    <p class="value"><?php echo (int)$newThisMonth; ?></p>
                                    <p class="note">
                                        <?php echo Label::getLabel('LBL_Last_month'); ?>:
                                        <?php echo (int)$newLastMonth; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card--stat">
                                    <h6><?php echo Label::getLabel('LBL_Cancellations_(30_days)'); ?></h6>
                                    <p class="value"><?php echo (int)$canceled30; ?></p>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card card--stat">
                                    <h6><?php echo Label::getLabel('LBL_Estimated_churn_rate'); ?></h6>
                                    <p class="value"><?php echo $churnRate; ?>%</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Active subs by plan -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Active_subscriptions_by_plan'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="tablewrap">
                            <table class="table table--hovered">
                                <thead>
                                    <tr>
                                        <th><?php echo Label::getLabel('LBL_Plan'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Active_subscriptions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if (!empty($activeByPlan)) { ?>
                                    <?php foreach ($activeByPlan as $row) { ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($row['spackage_name']); ?></td>
                                            <td><?php echo (int)$row['total']; ?></td>
                                        </tr>
                                    <?php } ?>
                                <?php } else { ?>
                                    <tr>
                                        <td colspan="2">
                                            <?php echo Label::getLabel('LBL_No_active_subscriptions_found'); ?>
                                        </td>
                                    </tr>
                                <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>
