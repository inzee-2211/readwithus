<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
    .parent-dashboard {
        padding: 20px 0;
    }

    .page-title-wrap {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .page-title {
        font-size: 2rem;
        font-weight: 800;
        color: #1a202c;
        margin: 0;
        background: linear-gradient(135deg, #2dadff 0%, #153e7d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .status-section {
        margin-bottom: 40px;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
    }

    .section-title i {
        margin-right: 10px;
        color: #2dadff;
    }

    .children-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
    }

    .child-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        padding: 24px;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }

    .child-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
        border-color: #2dadff;
    }

    .child-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: #2dadff;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .child-card:hover::before {
        opacity: 1;
    }

    .child-avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        background: #edf2f7;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        color: #2dadff;
        margin-bottom: 16px;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #edf2f7;
    }

    .child-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin-bottom: 4px;
    }

    .child-email {
        font-size: 0.875rem;
        color: #718096;
        margin-bottom: 12px;
    }

    .child-relation {
        display: inline-block;
        padding: 4px 12px;
        border-radius: 20px;
        background: #ebf8ff;
        color: #2b6cb0;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 20px;
    }

    .card-actions {
        display: flex;
        gap: 12px;
        margin-top: auto;
    }

    .btn-action {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 10px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-view {
        background: #f7fafc;
        color: #4a5568;
        border: 1px solid #e2e8f0;
    }

    .btn-view:hover {
        background: #edf2f7;
        color: #2d3748;
    }

    .btn-login {
        background: #2dadff;
        color: #fff;
        border: none;
        box-shadow: 0 4px 14px 0 rgba(45, 173, 255, 0.39);
    }

    .btn-login:hover {
        background: #1a9fff;
        transform: scale(1.02);
        box-shadow: 0 6px 20px rgba(45, 173, 255, 0.23);
    }

    .btn-remove {
        position: absolute;
        top: 16px;
        right: 16px;
        color: #a0aec0;
        background: transparent;
        border: none;
        padding: 4px;
        cursor: pointer;
        border-radius: 50%;
        transition: all 0.2s;
    }

    .btn-remove:hover {
        color: #f56565;
        background: #fff5f5;
    }

    .pending-card {
        border-style: dashed;
        background: #fffaf0;
    }

    .pending-badge {
        background: #feebc8;
        color: #9c4221;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #fff;
        border-radius: 20px;
        border: 2px dashed #e2e8f0;
    }

    .empty-icon {
        font-size: 4rem;
        color: #cbd5e0;
        margin-bottom: 20px;
    }

    .animate-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }
</style>

<div class="container container--fixed parent-dashboard">
    <div class="page-title-wrap">
        <h1 class="page-title"><?php echo Label::getLabel('LBL_MY_FAMILY'); ?></h1>
        <a class="btn btn--primary btn--large"
            href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>"
            style="border-radius: 12px; font-weight: 700; padding: 12px 24px;">
            <i class="ion-plus-round margin-right-2"></i> <?php echo Label::getLabel('LBL_ADD_CHILD'); ?>
        </a>
    </div>

    <div class="page__body">
        <!-- Pending Requests -->
        <?php if (!empty($pendingRequests)) { ?>
            <div class="status-section">
                <h4 class="section-title"><i class="ion-clock"></i> <?php echo Label::getLabel('LBL_PENDING_REQUESTS'); ?>
                </h4>
                <div class="children-grid">
                    <?php foreach ($pendingRequests as $request) {
                        $fullName = htmlspecialchars(($request['user_first_name'] ?? '') . ' ' . ($request['user_last_name'] ?? ''));
                        $initial = CommonHelper::getFirstChar($request['user_first_name'] ?? 'C');
                        ?>
                        <div class="child-card pending-card">
                            <div class="child-avatar"><?php echo $initial; ?></div>
                            <div class="child-name"><?php echo $fullName; ?></div>
                            <div class="child-email"><?php echo htmlspecialchars($request['user_email'] ?? ''); ?></div>
                            <span
                                class="child-relation pending-badge"><?php echo Label::getLabel('LBL_PENDING_APPROVAL'); ?></span>
                            <div style="font-size: 0.75rem; color: #a0aec0;">
                                <?php echo Label::getLabel('LBL_REQUESTED_ON'); ?>:
                                <?php echo MyDate::formatDate($request['parstd_added_on'], true, null, $siteUser['user_timezone']); ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <!-- Approved Children -->
        <div class="status-section">
            <h4 class="section-title"><i class="ion-checkmark-circled"></i>
                <?php echo Label::getLabel('LBL_APPROVED_CHILDREN'); ?></h4>
            <?php if (empty($children)) { ?>
                <div class="empty-state">
                    <div class="empty-icon"><i class="ion-ios-people-outline"></i></div>
                    <h3><?php echo Label::getLabel('LBL_NO_CHILDREN_LINKED_YET'); ?></h3>
                    <p class="margin-bottom-6"><?php echo Label::getLabel('LBL_START_BY_ADDING_YOUR_CHILD_PROFILE'); ?></p>
                    <a href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>"
                        class="btn btn--primary">
                        <?php echo Label::getLabel('LBL_CLICK_HERE_TO_ADD_CHILD'); ?>
                    </a>
                </div>
            <?php } else { ?>
                <div class="children-grid">
                    <?php foreach ($children as $row) {
                        $fullName = htmlspecialchars(($row['user_first_name'] ?? '') . ' ' . ($row['user_last_name'] ?? ''));
                        $initial = CommonHelper::getFirstChar($row['user_first_name'] ?? 'C');
                        $viewUrl = MyUtility::makeUrl('Parent', 'child', [$row['student_id']], CONF_WEBROOT_DASHBOARD);
                        $loginUrl = MyUtility::makeUrl('Parent', 'loginAsChild', [$row['student_id']], CONF_WEBROOT_DASHBOARD);
                        ?>
                        <div class="child-card">
                            <button type="button" class="btn-remove remove-child-btn"
                                data-child-id="<?php echo $row['student_id']; ?>"
                                data-child-name="<?php echo htmlspecialchars($fullName); ?>"
                                title="<?php echo Label::getLabel('LBL_REMOVE'); ?>">
                                <i class="ion-android-close"></i>
                            </button>
                            <div class="child-avatar" style="background: linear-gradient(135deg, #ebf8ff 0%, #bee3f8 100%);">
                                <?php echo $initial; ?></div>
                            <div class="child-name"><?php echo $fullName; ?></div>
                            <div class="child-email"><?php echo htmlspecialchars($row['user_email'] ?? ''); ?></div>
                            <span
                                class="child-relation"><?php echo htmlspecialchars($row['parstd_relation'] ?? Label::getLabel('LBL_CHILD')); ?></span>

                            <div class="card-actions">
                                <a class="btn-action btn-view" href="<?php echo $viewUrl; ?>">
                                    <i class="ion-eye margin-right-2"></i> <?php echo Label::getLabel('LBL_VIEW'); ?>
                                </a>
                                <a class="btn-action btn-login" href="<?php echo $loginUrl; ?>">
                                    <i class="ion-log-in margin-right-2"></i> <?php echo Label::getLabel('LBL_LOGIN'); ?>
                                </a>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>
    </div>
</div>

<script>
    $(document).ready(function () {
        // Remove child confirmation
        $('.remove-child-btn').click(function () {
            var childId = $(this).data('child-id');
            var childName = $(this).data('child-name');

            if (confirm('<?php echo Label::getLabel('LBL_CONFIRM_REMOVE_CHILD'); ?>: ' + childName + '?')) {
                fcom.updateWithAjax(fcom.makeUrl('Parent', 'removeChild', [childId]), '', function (response) {
                    if (response.status) {
                        window.location.reload();
                    }
                });
            }
        });
    });
</script>