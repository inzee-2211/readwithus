<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
    .parent-dashboard-home {
        padding: 30px 0;
    }

    .page-title {
        font-size: 2.25rem;
        font-weight: 800;
        color: #1a202c;
        margin-bottom: 30px;
        background: linear-gradient(135deg, #2dadff 0%, #153e7d 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 24px;
        margin-bottom: 40px;
    }

    .stat-card-modern {
        background: #fff;
        padding: 24px;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 20px;
        transition: all 0.3s ease;
        text-decoration: none !important;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .stat-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        border-color: #2dadff;
    }

    .stat-icon-wrap {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-info h3 {
        font-size: 1.75rem;
        font-weight: 800;
        color: #1a202c;
        margin: 0;
        line-height: 1;
    }

    .stat-info p {
        font-size: 0.875rem;
        color: #718096;
        margin: 6px 0 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .dashboard-section {
        margin-bottom: 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 24px;
    }

    .section-header h2 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .section-header h2 i {
        color: #2dadff;
    }

    .content-card {
        background: #fff;
        border-radius: 20px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
    }

    .table-premium th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.1em;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-premium td {
        padding: 18px 24px;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-premium tr:last-child td {
        border-bottom: none;
    }

    .table-premium tr:hover td {
        background: #fcfdfe;
    }

    .child-mini-pill {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .child-mini-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e0f2fe;
        color: #0ea5e9;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 800;
        border: 2px solid #fff;
        box-shadow: 0 0 0 1px #e0f2fe;
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 15px;
        padding: 16px 20px;
        border-radius: 16px;
        background: #f8fafc;
        color: #4a5568;
        font-weight: 700;
        transition: all 0.2s;
        border: 1px solid #e2e8f0;
        margin-bottom: 12px;
        text-decoration: none !important;
    }

    .quick-action-btn:last-child {
        margin-bottom: 0;
    }

    .quick-action-btn:hover {
        background: #fff;
        border-color: #2dadff;
        color: #2dadff;
        transform: translateX(5px);
    }

    .quick-action-btn i {
        font-size: 1.25rem;
        color: #cbd5e0;
        transition: color 0.2s;
    }

    .quick-action-btn:hover i {
        color: #2dadff;
    }

    .action-primary {
        background: #ebf8ff;
        border-color: #bee3f8;
        color: #2b6cb0;
    }

    .action-primary i {
        color: #63b3ed;
    }

    .recent-child-item {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: background 0.2s;
    }

    .recent-child-item:last-child {
        border-bottom: none;
    }

    .recent-child-item:hover {
        background: #fcfdfe;
    }
</style>

<div class="container container--fixed parent-dashboard-home">
    <div class="page-title-wrap">
        <h1 class="page-title"><?php echo Label::getLabel('LBL_PARENT_DASHBOARD'); ?></h1>
    </div>

    <!-- Summary Stats Grid -->
    <div class="stat-grid">
        <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
            class="stat-card-modern">
            <div class="stat-icon-wrap" style="background: #e0f2fe; color: #0ea5e9;"><i class="ion-ios-people"></i>
            </div>
            <div class="stat-info">
                <h3><?php echo (int) $childrenCount; ?></h3>
                <p><?php echo Label::getLabel('LBL_LINKED_CHILDREN'); ?></p>
            </div>
        </a>

        <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
            class="stat-card-modern">
            <div class="stat-icon-wrap" style="background: #fef3c7; color: #d97706;"><i
                    class="ion-android-notifications"></i></div>
            <div class="stat-info">
                <h3><?php echo (int) $pendingRequests; ?></h3>
                <p><?php echo Label::getLabel('LBL_PENDING_REQUESTS'); ?></p>
            </div>
        </a>

    </div>



    <!-- Quick Actions & Recent Children -->
    <div class="row">
        <div class="col-lg-6">
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="ion-flash"></i> <?php echo Label::getLabel('LBL_QUICK_ACTIONS'); ?></h2>
                </div>
                <div class="padding-0">
                    <a href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>"
                        class="quick-action-btn action-primary">
                        <i class="ion-person-add"></i>
                        <span><?php echo Label::getLabel('LBL_ADD_NEW_CHILD'); ?></span>
                        <i class="ion-chevron-right margin-left-auto" style="font-size: 0.875rem;"></i>
                    </a>
                    <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
                        class="quick-action-btn">
                        <i class="ion-settings"></i>
                        <span><?php echo Label::getLabel('LBL_MANAGE_ALL_CHILDREN'); ?></span>
                        <i class="ion-chevron-right margin-left-auto" style="font-size: 0.875rem;"></i>
                    </a>
                    </a>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="dashboard-section">
                <div class="section-header">
                    <h2><i class="ion-ios-people"></i> <?php echo Label::getLabel('LBL_ALL_CHILDREN'); ?></h2>
                    <?php if (count($children) > 10) { ?>
                        <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
                            class="btn btn--link btn--small"><?php echo Label::getLabel('LBL_VIEW_ALL'); ?></a>
                    <?php } ?>
                </div>
                <div class="content-card">
                    <?php if (empty($children)) { ?>
                        <div class="padding-20 text-center color-gray">
                            <p><?php echo Label::getLabel('LBL_NO_CHILDREN_LINKED'); ?></p>
                        </div>
                    <?php } else { ?>
                        <div class="recent-children-list">
                            <?php foreach (array_slice($children, 0, 10) as $child) {
                                $fullName = htmlspecialchars($child['user_first_name'] . ' ' . $child['user_last_name']);
                                $initial = CommonHelper::getFirstChar($child['user_first_name']);
                                $viewUrl = MyUtility::makeUrl('Parent', 'child', [$child['student_id']], CONF_WEBROOT_DASHBOARD);
                                ?>
                                <div class="recent-child-item">
                                    <div class="child-mini-pill">
                                        <div class="child-mini-avatar" style="width: 40px; height: 40px; font-size: 1rem;">
                                            <?php echo $initial; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700;"><?php echo $fullName; ?></div>
                                            <div style="font-size: 0.75rem; color: #718096;">
                                                <?php echo htmlspecialchars($child['user_email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?php echo $viewUrl; ?>" class="btn btn--secondary btn--small"
                                        style="border-radius: 8px;">
                                        <?php echo Label::getLabel('LBL_DASHBOARD'); ?>
                                    </a>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>