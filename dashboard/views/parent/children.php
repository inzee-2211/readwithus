<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="page">
    <div class="page__head">
        <h4><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></h4>
    </div>

    <div class="page__body">
        <?php if (empty($children)) { ?>
            <div class="alert alert--info">
                <?php echo Label::getLabel('LBL_NO_CHILDREN_LINKED_YET'); ?>
            </div>
        <?php } else { ?>
            <div class="table-scroll">
                <table class="table table--hover">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_CHILD'); ?></th>
                            <th><?php echo Label::getLabel('LBL_EMAIL'); ?></th>
                            <th><?php echo Label::getLabel('LBL_RELATION'); ?></th>
                            <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($children as $row) {
                            $fullName = trim(($row['user_first_name'] ?? '') . ' ' . ($row['user_last_name'] ?? ''));
                            $relation = $row['parstd_relation'] ?? '-';
                            $viewUrl = MyUtility::makeUrl('Parent', 'child', [$row['student_id']], CONF_WEBROOT_DASHBOARD);
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($fullName); ?></td>
                            <td><?php echo htmlspecialchars($row['user_email'] ?? ''); ?></td>
                            <td><?php echo htmlspecialchars($relation); ?></td>
                            <td>
                                <a class="btn btn--primary btn--small" href="<?php echo $viewUrl; ?>">
                                    <?php echo Label::getLabel('LBL_VIEW'); ?>
                                </a>
                            </td>
                        </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        <?php } ?>
    </div>
</div>
