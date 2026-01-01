<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_PARENT_REQUESTS'); ?></h1>
            </div>

            <div class="page__body">
                <?php if (empty($pendingRequests)) { ?>
                    <div class="alert alert--info">
                        <?php echo Label::getLabel('LBL_NO_PENDING_REQUESTS'); ?>
                    </div>
                <?php } else { ?>
                    <div class="table-scroll">
                        <table class="table table--hover">
                            <thead>
                                <tr>
                                    <th><?php echo Label::getLabel('LBL_PARENT'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_EMAIL'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_RELATION'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_REQUEST_DATE'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pendingRequests as $request) {
                                    $fullName = htmlspecialchars($request['user_first_name'] . ' ' . $request['user_last_name']);
                                    $requestDate = MyDate::formatDate($request['parstd_added_on'], true, null, $siteUser['user_timezone']);
                                ?>
                                <tr>
                                    <td><?php echo $fullName; ?></td>
                                    <td><?php echo htmlspecialchars($request['user_email']); ?></td>
                                    <td><?php echo htmlspecialchars($request['parstd_relation']); ?></td>
                                    <td><?php echo $requestDate; ?></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn--success btn--small approve-request-btn" data-request-id="<?php echo $request['parstd_id']; ?>">
                                                <?php echo Label::getLabel('LBL_APPROVE'); ?>
                                            </button>
                                            <button type="button" class="btn btn--danger btn--small reject-request-btn" data-request-id="<?php echo $request['parstd_id']; ?>">
                                                <?php echo Label::getLabel('LBL_REJECT'); ?>
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

<script>
$(document).ready(function() {
    // Approve request
    $('.approve-request-btn').click(function() {
        var requestId = $(this).data('request-id');
        
        if (confirm('<?php echo Label::getLabel('LBL_CONFIRM_APPROVE_REQUEST'); ?>')) {
            fcom.updateWithAjax(fcom.makeUrl('Learner', 'approveParentRequest', [requestId]), '', function(response) {
                if (response.status) {
                    window.location.reload();
                }
            });
        }
    });
    
    // Reject request
    $('.reject-request-btn').click(function() {
        var requestId = $(this).data('request-id');
        
        if (confirm('<?php echo Label::getLabel('LBL_CONFIRM_REJECT_REQUEST'); ?>')) {
            fcom.updateWithAjax(fcom.makeUrl('Learner', 'rejectParentRequest', [requestId]), '', function(response) {
                if (response.status) {
                    window.location.reload();
                }
            });
        }
    });
});
</script>