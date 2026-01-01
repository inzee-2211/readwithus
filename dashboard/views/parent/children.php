<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></h1>
                <div>
                    <a class="btn btn--primary" href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>">
                        <?php echo Label::getLabel('LBL_ADD_CHILD'); ?>
                    </a>
                </div>
            </div>

            <div class="page__body">
                <!-- Pending Requests -->
                <?php if (!empty($pendingRequests)) { ?>
                <div class="page-panel margin-bottom-6">
                    <div class="page-panel__head">
                        <h4><?php echo Label::getLabel('LBL_PENDING_REQUESTS'); ?></h4>
                    </div>
                    <div class="page-panel__body">
                        <div class="table-scroll">
                            <table class="table table--hover">
                                <thead>
                                    <tr>
                                        <th><?php echo Label::getLabel('LBL_CHILD'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_EMAIL'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_RELATION'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_REQUEST_DATE'); ?></th>
                                        <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($pendingRequests as $request) {
                                        $fullName = htmlspecialchars(($request['user_first_name'] ?? '') . ' ' . ($request['user_last_name'] ?? ''));
                                        $requestDate = MyDate::formatDate($request['parstd_added_on'], true, null, $siteUser['user_timezone']);
                                    ?>
                                    <tr>
                                        <td><?php echo $fullName; ?></td>
                                        <td><?php echo htmlspecialchars($request['user_email'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars($request['parstd_relation'] ?? '-'); ?></td>
                                        <td><?php echo $requestDate; ?></td>
                                        <td>
                                            <span class="badge badge--warning">
                                                <?php echo Label::getLabel('LBL_PENDING'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php } ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Approved Children -->
                <div class="page-panel">
                    <div class="page-panel__head">
                        <h4><?php echo Label::getLabel('LBL_APPROVED_CHILDREN'); ?></h4>
                    </div>
                    <div class="page-panel__body">
                        <?php if (empty($children)) { ?>
                            <div class="alert alert--info">
                                <?php echo Label::getLabel('LBL_NO_CHILDREN_LINKED_YET'); ?>
                                <br>
                                <a href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>">
                                    <?php echo Label::getLabel('LBL_CLICK_HERE_TO_ADD_CHILD'); ?>
                                </a>
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
                                            $fullName = htmlspecialchars(($row['user_first_name'] ?? '') . ' ' . ($row['user_last_name'] ?? ''));
                                            $relation = $row['parstd_relation'] ?? '-';
                                            $viewUrl = MyUtility::makeUrl('Parent', 'child', [$row['student_id']], CONF_WEBROOT_DASHBOARD);
                                        ?>
                                        <tr>
                                            <td><?php echo $fullName; ?></td>
                                            <td><?php echo htmlspecialchars($row['user_email'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($relation); ?></td>
                                            <td>
                                                <div class="btn-group">
                                                    <a class="btn btn--primary btn--small" href="<?php echo $viewUrl; ?>">
                                                        <?php echo Label::getLabel('LBL_VIEW'); ?>
                                                    </a>
                                                    <button type="button" class="btn btn--secondary btn--small remove-child-btn" data-child-id="<?php echo $row['student_id']; ?>" data-child-name="<?php echo htmlspecialchars($fullName); ?>">
                                                        <?php echo Label::getLabel('LBL_REMOVE'); ?>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Remove child confirmation
    $('.remove-child-btn').click(function() {
        var childId = $(this).data('child-id');
        var childName = $(this).data('child-name');
        
        if (confirm('<?php echo Label::getLabel('LBL_CONFIRM_REMOVE_CHILD'); ?>: ' + childName + '?')) {
            fcom.updateWithAjax(fcom.makeUrl('Parent', 'removeChild', [childId]), '', function(response) {
                if (response.status) {
                    window.location.reload();
                }
            });
        }
    });
});
</script>