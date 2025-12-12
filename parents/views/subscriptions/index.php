<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="page__head">
        <h1><?php echo Label::getLabel('LBL_MY_SUBSCRIPTIONS'); ?></h1>
    </div>
    <div class="page__body">
        <div class="box box--white">
            <div class="table-scroll">
                <table class="table table--styled table--responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_PACKAGE_NAME'); ?></th>
                            <th><?php echo Label::getLabel('LBL_CHILD_NAME'); ?></th>
                            <th><?php echo Label::getLabel('LBL_START_DATE'); ?></th>
                            <th><?php echo Label::getLabel('LBL_END_DATE'); ?></th>
                            <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($subscriptions)) {
                            foreach ($subscriptions as $sub) { ?>
                                <tr>
                                    <td><?php echo $sub['package_name']; ?></td>
                                    <td><?php echo $sub['child_name']; ?></td>
                                    <td><?php echo $sub['start_date']; ?></td>
                                    <td><?php echo $sub['end_date']; ?></td>
                                    <td><span
                                            class="badge <?php echo $sub['status_class']; ?>"><?php echo $sub['status']; ?></span>
                                    </td>
                                </tr>
                            <?php }
                        } else { ?>
                            <tr>
                                <td colspan="5" class="text-center">
                                    <?php echo Label::getLabel('LBL_NO_SUBSCRIPTIONS_FOUND'); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>