<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

/**
 * We rely on POST values directly so we don't have to touch your controller.
 */
$status = FatApp::getPostedData('status', FatUtility::VAR_STRING, '');
$email  = FatApp::getPostedData('email', FatUtility::VAR_STRING, '');
?>
<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <!-- Page Title + Breadcrumb -->
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-list"></i></span>
                            <h5><?php echo Label::getLabel('LBL_Subscription_Orders'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>

                <!-- Search Box -->
                <section class="section searchform_filter">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Search...'); ?></h4>
                    </div>
                    <div class="sectionbody space togglewrap" style="display:block;">
                        <form name="frmSubSearch" id="frmSubSearch" class="web_form" method="post">
                            <div class="row">
                                <!-- Status -->
                                <div class="col-md-3">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo Label::getLabel('LBL_Status'); ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <select name="status" class="field">
                                                    <option value="">
                                                        <?php echo Label::getLabel('LBL_All'); ?>
                                                    </option>
                                                    <option value="active"   <?php echo ($status === 'active')   ? 'selected' : ''; ?>>active</option>
                                                    <option value="canceled" <?php echo ($status === 'canceled') ? 'selected' : ''; ?>>canceled</option>
                                                    <option value="expired"  <?php echo ($status === 'expired')  ? 'selected' : ''; ?>>expired</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- User Email -->
                                <div class="col-md-4">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">
                                                <?php echo Label::getLabel('LBL_User_Email'); ?>
                                            </label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <input
                                                    type="text"
                                                    name="email"
                                                    class="field search-input"
                                                    value="<?php echo htmlspecialchars($email); ?>"
                                                    placeholder="<?php echo Label::getLabel('LBL_Search_by_email'); ?>"
                                                />
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Buttons -->
                                <div class="col-md-3">
                                    <div class="field-set">
                                        <div class="caption-wraper">
                                            <label class="field_label">&nbsp;</label>
                                        </div>
                                        <div class="field-wraper">
                                            <div class="field_cover">
                                                <input
                                                    type="submit"
                                                    class="btn btn--primary"
                                                    value="<?php echo Label::getLabel('LBL_Search'); ?>"
                                                />
                                                &nbsp;
                                                <button type="button"
                                                        onclick="clearSubSearch();"
                                                        class="btn btn--secondary">
                                                    <?php echo Label::getLabel('LBL_Clear'); ?>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </section>

                <!-- Listing -->
                <section class="section">
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <table class="table table--hovered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo Label::getLabel('LBL_Email'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Plan'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Status'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Start'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_End'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Stripe_Sub_ID'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_Actions'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($rows)) { ?>
                                        <?php $i = 1; foreach ($rows as $row) { ?>
                                            <tr>
                                                <td><?php echo $i++; ?></td>
                                                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['spackage_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['usubs_status']); ?></td>
                                                <td><?php echo htmlspecialchars($row['usubs_start_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['usubs_end_date']); ?></td>
                                                <td><?php echo htmlspecialchars($row['stripe_subscription_id']); ?></td>
                                                <td>
                                                    <a href="<?php echo MyUtility::makeUrl('SubscriptionOrders', 'view', [(int)$row['usubs_id']]); ?>">
                                                        <?php echo Label::getLabel('LBL_View'); ?>
                                                    </a>
                                                    <?php if ($row['usubs_status'] === 'active') { ?>
                                                        &nbsp;|&nbsp;
                                                        <a href="javascript:void(0);"
                                                           onclick="cancelSubscription(<?php echo (int)$row['usubs_id']; ?>);"
                                                           class="text-danger">
                                                            <?php echo Label::getLabel('LBL_Cancel'); ?>
                                                        </a>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="8">
                                                <?php echo Label::getLabel('LBL_No_records_found'); ?>
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

<script>
    // Optional: mimic other search sections – click header to toggle
    $(document).ready(function () {
        $('.searchform_filter .sectionhead').on('click', function () {
            $(this).next('.sectionbody').slideToggle();
        });
    });

    function clearSubSearch() {
        $('#frmSubSearch').trigger('reset');
        $('#frmSubSearch').submit();
    }

    function cancelSubscription(id) {
        if (!confirm('<?php echo Label::getLabel('LBL_Confirm_cancel_subscription'); ?>')) {
            return;
        }
        fcom.updateWithAjax(
            fcom.makeUrl('SubscriptionOrders', 'cancel', [id]),
            '',
            function (res) {
                window.location.reload();
            }
        );
    }
</script>
