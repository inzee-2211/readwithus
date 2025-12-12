<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_WALLET'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <h4><?php echo Label::getLabel('LBL_BALANCE'); ?>: <?php echo MyUtility::formatMoney($balance); ?></h4>
            </div>
        </div>
    </div>
    <div class="page__body">
        <div class="box box--white">
            <div class="section-head p-4 pb-0">
                <div class="section__heading">
                    <h4><?php echo Label::getLabel('LBL_RECENT_TRANSACTIONS'); ?></h4>
                </div>
            </div>
            <div class="table-scroll">
                <table class="table table--styled table--responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_DATE'); ?></th>
                            <th><?php echo Label::getLabel('LBL_TXN_ID'); ?></th>
                            <th><?php echo Label::getLabel('LBL_DESCRIPTION'); ?></th>
                            <th><?php echo Label::getLabel('LBL_AMOUNT'); ?></th>
                            <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($transactions)) {
                            foreach ($transactions as $txn) { ?>
                                <tr>
                                    <td><?php echo $txn['date']; ?></td>
                                    <td><?php echo $txn['id']; ?></td>
                                    <td><?php echo $txn['description']; ?></td>
                                    <td><?php echo $txn['amount']; ?></td>
                                    <td><?php echo $txn['status']; ?></td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <?php echo Label::getLabel('LBL_NO_TRANSACTIONS_FOUND'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>