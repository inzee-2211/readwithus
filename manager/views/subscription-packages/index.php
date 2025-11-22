<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">
                <!-- Page Title + Breadcrumb + Add button -->
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-list"></i></span>
                            <h5><?php echo Label::getLabel('LBL_SUBSCRIPTION_PACKAGES'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col--last">
                            <div class="buttons-group">
                                <a href="<?php echo MyUtility::makeUrl('SubscriptionPackages', 'form'); ?>"
                                   class="btn btn--primary btn--sm">
                                    <?php echo Label::getLabel('LBL_ADD_NEW_PACKAGE'); ?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Listing Section -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Listing'); ?></h4>
                    </div>
                    <div class="sectionbody">
                        <div class="tablewrap">
                            <table class="table table--hovered table-responsive">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th><?php echo Label::getLabel('LBL_NAME'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_LEVEL'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_SUBJECT_LIMIT'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_MONTHLY_PRICE'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_YEARLY_PRICE'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_ACTIONS'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($rows)) { ?>
                                        <?php foreach ($rows as $r) { ?>
                                            <tr>
                                                <td><?php echo (int)$r['spackage_id']; ?></td>
                                                <td><?php echo htmlspecialchars($r['spackage_name']); ?></td>
                                                <td><?php echo !empty($r['level_name']) ? htmlspecialchars($r['level_name']) : '-'; ?></td>
                                                <td><?php echo (int)$r['spackage_subject_limit']; ?></td>
                                                <td><?php echo number_format((float)$r['spackage_price_monthly'], 2); ?></td>
                                                <td><?php echo number_format((float)$r['spackage_price_yearly'], 2); ?></td>
                                                <td><?php echo ((int)$r['spackage_status'] === 1) ? 'Active' : 'Inactive'; ?></td>
                                                <td>
                                                    <a class="btn btn--secondary btn--sm"
                                                       href="<?php echo MyUtility::makeUrl('SubscriptionPackages', 'form', [$r['spackage_id']]); ?>">
                                                        <?php echo Label::getLabel('LBL_EDIT'); ?>
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    <?php } else { ?>
                                        <tr>
                                            <td colspan="8">
                                                <?php echo Label::getLabel('LBL_NO_RECORD_FOUND'); ?>
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
