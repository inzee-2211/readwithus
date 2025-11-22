<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

$row        = $row ?? [];
$subjects   = $subjects ?? [];
$courses    = $courses ?? [];
$stripeData = $stripeData ?? [];
$allPackages = $allPackages ?? [];
?>

<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">

                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-person"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Subscription_Detail'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col--last">
                            <div class="buttons-group">
                                <a href="<?php echo MyUtility::makeUrl('SubscriptionOrders'); ?>"
                                   class="btn btn--secondary btn--sm">
                                    <?php echo Label::getLabel('LBL_BACK'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Subscription_Summary'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="stat">
                                    <strong><?php echo Label::getLabel('LBL_User'); ?>:</strong>
                                    <br>
                                    <?php
                                    echo htmlspecialchars($row['user_first_name'] . ' ' . $row['user_last_name']);
                                    ?>
                                    <br>
                                    <small><?php echo htmlspecialchars($row['user_email']); ?></small>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="stat">
                                    <strong><?php echo Label::getLabel('LBL_Plan'); ?>:</strong>
                                    <br>
                                    <?php echo htmlspecialchars($row['spackage_name']); ?>
                                    <?php if (!empty($row['level_name'])) { ?>
                                        <span class="badge badge--round badge--secondary">
                                            <?php echo htmlspecialchars($row['level_name']); ?>
                                        </span>
                                    <?php } ?>
                                    <br>
                                    <small>
                                        <?php echo Label::getLabel('LBL_Status'); ?>:
                                        <b><?php echo htmlspecialchars($row['usubs_status']); ?></b>
                                        &nbsp;&middot;&nbsp;
                                        <?php echo Label::getLabel('LBL_Start'); ?>:
                                        <?php echo htmlspecialchars($row['usubs_start_date']); ?>
                                        &nbsp;&middot;&nbsp;
                                        <?php echo Label::getLabel('LBL_End'); ?>:
                                        <?php echo htmlspecialchars($row['usubs_end_date']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Subjects + Courses -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Subjects_&_Courses'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="row">
                            <!-- Subjects -->
                            <div class="col-md-4">
                                <h6><?php echo Label::getLabel('LBL_Selected_Subjects'); ?></h6>
                                <?php if (!empty($subjects)) { ?>
                                    <ul class="list list--dash">
                                        <?php foreach ($subjects as $s) { ?>
                                            <li>
                                                <?php echo htmlspecialchars($s['subject']); ?>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                <?php } else { ?>
                                    <p><?php echo Label::getLabel('LBL_No_subjects_selected'); ?></p>
                                <?php } ?>
                            </div>

                            <!-- Courses -->
                            <div class="col-md-8">
                                <h6><?php echo Label::getLabel('LBL_Unlocked_Courses'); ?></h6>
                                <div class="tablewrap">
                                    <table class="table table--hovered">
                                        <thead>
                                            <tr>
                                                <th><?php echo Label::getLabel('LBL_ID'); ?></th>
                                                <th><?php echo Label::getLabel('LBL_Title'); ?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php if (!empty($courses)) { ?>
                                            <?php foreach ($courses as $c) { ?>
                                                <tr>
                                                    <td><?php echo (int)$c['course_id']; ?></td>
                                                    <td><?php echo htmlspecialchars($c['course_title']); ?></td>
                                                </tr>
                                            <?php } ?>
                                        <?php } else { ?>
                                            <tr>
                                                <td colspan="2">
                                                    <?php echo Label::getLabel('LBL_No_courses_found_for_selected_subjects'); ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Stripe Info + Change Plan -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Billing_&_Stripe'); ?></h4>
                    </div>
                    <div class="sectionbody space">
                        <div class="row">
                            <!-- Stripe info -->
                            <div class="col-md-6">
                                <h6><?php echo Label::getLabel('LBL_Stripe_Subscription'); ?></h6>
                                <?php if (!empty($stripeData['error'])) { ?>
                                    <p class="text-danger">
                                        <?php echo htmlspecialchars($stripeData['error']); ?>
                                    </p>
                                <?php } elseif (!empty($stripeData)) { ?>
                                    <p>
                                        <strong><?php echo Label::getLabel('LBL_Status'); ?>:</strong>
                                        <?php echo htmlspecialchars($stripeData['status']); ?><br>
                                        <strong><?php echo Label::getLabel('LBL_Current_Period'); ?>:</strong>
                                        <?php echo htmlspecialchars($stripeData['current_period_start']); ?>
                                        &rarr;
                                        <?php echo htmlspecialchars($stripeData['current_period_end']); ?><br>
                                        <strong><?php echo Label::getLabel('LBL_Last_Invoice'); ?>:</strong>
                                        <?php
                                        if (!empty($stripeData['latest_amount'])) {
                                            echo number_format($stripeData['latest_amount'], 2) . ' ' .
                                                 htmlspecialchars($stripeData['latest_currency']);
                                        } else {
                                            echo Label::getLabel('LBL_Not_available');
                                        }
                                        ?><br>
                                        <?php if (!empty($stripeData['stripe_subscription_url'])) { ?>
                                            <strong><?php echo Label::getLabel('LBL_Stripe_Subscription'); ?>:</strong>
                                            <a href="<?php echo $stripeData['stripe_subscription_url']; ?>"
                                               target="_blank" rel="noopener">
                                                <?php echo Label::getLabel('LBL_Open_in_Stripe'); ?>
                                            </a><br>
                                        <?php } ?>
                                        <?php if (!empty($stripeData['stripe_customer_url'])) { ?>
                                            <strong><?php echo Label::getLabel('LBL_Stripe_Customer'); ?>:</strong>
                                            <a href="<?php echo $stripeData['stripe_customer_url']; ?>"
                                               target="_blank" rel="noopener">
                                                <?php echo Label::getLabel('LBL_Open_in_Stripe'); ?>
                                            </a>
                                        <?php } ?>
                                    </p>
                                <?php } else { ?>
                                    <p><?php echo Label::getLabel('LBL_No_Stripe_information_found'); ?></p>
                                <?php } ?>
                            </div>

                            <!-- Upgrade / Downgrade -->
                            <div class="col-md-6">
                                <h6><?php echo Label::getLabel('LBL_Change_Plan'); ?></h6>
                                <p class="note">
                                    <?php echo Label::getLabel('LBL_Change_plan_will_update_Stripe_subscription_(prorated)_and_keep_webhooks_in_sync'); ?>
                                </p>

                                <form id="frmChangePlan">
                                    <input type="hidden" name="usubs_id"
                                           value="<?php echo (int)$row['usubs_id']; ?>" />
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo Label::getLabel('LBL_Select_new_package'); ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <select name="new_spackage_id" class="field">
    <option value="">
        <?php echo Label::getLabel('LBL_Select'); ?>
    </option>
    <?php foreach ($allPackages as $pkg) { ?>
        <option
            value="<?php echo (int)$pkg['spackage_id']; ?>"
            <?php echo ((int)$pkg['spackage_id'] === (int)$row['usubs_spackage_id']) ? 'selected' : ''; ?>
        >
            <?php
            $label = $pkg['spackage_name'];

            // Append level if available
            if (!empty($pkg['level_name'])) {
                $label .= ' — ' . $pkg['level_name'];
            }

            // Append price
            $label .= ' — ' . number_format((float)$pkg['spackage_price_monthly'], 2) . '/month';

            echo htmlspecialchars($label);
            ?>
        </option>
    <?php } ?>
</select>

                                            </div>
                                        </div>
                                    </div>
                                    <div class="field-set">
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <button type="button"
                                                        onclick="changePlan();"
                                                        class="btn btn--primary">
                                                    <?php echo Label::getLabel('LBL_Update_Plan'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </section>

            </div>
        </div>
    </div>
</div>

<script>
    function changePlan() {
        var frm = document.getElementById('frmChangePlan');
        if (!frm) return;

        var pkgSelect = frm.querySelector('select[name="new_spackage_id"]');
        if (!pkgSelect.value) {
            alert('<?php echo Label::getLabel('LBL_Please_select_a_package'); ?>');
            return;
        }

        if (!confirm('<?php echo Label::getLabel('LBL_Confirm_change_subscription_plan'); ?>')) {
            return;
        }

        fcom.updateWithAjax(
            fcom.makeUrl('SubscriptionOrders', 'changePlan'),
            fcom.frmData(frm),
            function (res) {
                // On success we simply reload this page
                window.location.reload();
            }
        );
    }
</script>
