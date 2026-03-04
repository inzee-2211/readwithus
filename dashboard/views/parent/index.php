<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%);
        --mesh-gradient: radial-gradient(at 0% 0%, hsla(202, 100%, 84%, 1) 0, transparent 50%),
            radial-gradient(at 50% 0%, hsla(202, 100%, 94%, 1) 0, transparent 50%),
            radial-gradient(at 100% 0%, hsla(202, 100%, 89%, 1) 0, transparent 50%);
        --surface-glass: rgba(255, 255, 255, 0.85);
        --border-glass: rgba(255, 255, 255, 0.5);
        --shadow-premium: 0 10px 30px rgba(0, 0, 0, 0.04);
        --text-main: #0f172a;
        --text-muted: #475569;
        --accent-blue: #2dadff;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .parent-dashboard-home {
        padding: 60px 0;
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1);
        max-width: 1200px;
        margin: 0 auto;
    }

    .hero-welcome {
        background: #fff;
        background-image: var(--mesh-gradient);
        border-radius: 40px;
        padding: 80px 60px;
        margin-bottom: 60px;
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.03);
    }

    .hero-welcome::after {
        content: '';
        position: absolute;
        bottom: -20%;
        right: -5%;
        width: 300px;
        height: 300px;
        background: rgba(45, 173, 255, 0.05);
        border-radius: 50%;
        filter: blur(80px);
    }

    .page-title {
        font-size: 3.25rem;
        font-weight: 700;
        margin-bottom: 16px;
        color: var(--text-main);
        letter-spacing: -0.04em;
        line-height: 1.1;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--text-muted);
        font-weight: 500;
        margin: 0;
        letter-spacing: -0.01em;
        max-width: 600px;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 32px;
        margin-bottom: 80px;
    }

    .stat-card-modern {
        background: #fff;
        padding: 40px;
        border-radius: 32px;
        border: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 28px;
        transition: all 0.4s cubic-bezier(0.2, 1, 0.3, 1);
        text-decoration: none !important;
        box-shadow: var(--shadow-premium);
    }

    .stat-card-modern:hover {
        transform: translateY(-8px);
        background: #fff;
        box-shadow: 0 25px 50px rgba(0, 0, 0, 0.06);
        border-color: var(--accent-blue);
    }

    .stat-icon-wrap {
        width: 80px;
        height: 80px;
        border-radius: 24px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.25rem;
        box-shadow: inset 0 0 12px rgba(255, 255, 255, 0.5);
    }

    .stat-info h3 {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        line-height: 1;
        letter-spacing: -0.05em;
    }

    .stat-info p {
        font-size: 0.875rem;
        color: var(--text-muted);
        margin: 10px 0 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.12em;
    }

    .dashboard-section {
        margin-bottom: 80px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 40px;
        padding: 0 10px;
    }

    .section-header h2 {
        font-size: 2rem;
        font-weight: 700;
        color: var(--text-main);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 20px;
        letter-spacing: -0.03em;
    }

    .section-header h2 i {
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        color: var(--accent-blue);
        font-size: 1.5rem;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.02);
    }

    .content-card {
        background: #fff;
        border-radius: 32px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        box-shadow: var(--shadow-premium);
    }

    .quick-action-btn {
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 28px 32px;
        border-radius: 28px;
        background: #fdfdfd;
        color: var(--text-main);
        font-weight: 600;
        transition: all 0.3s cubic-bezier(0.2, 1, 0.3, 1);
        border: 1px solid #f1f5f9;
        margin-bottom: 20px;
        text-decoration: none !important;
        position: relative;
    }

    .quick-action-btn:hover {
        background: #fff;
        border-color: var(--accent-blue);
        color: var(--accent-blue);
        transform: scale(1.02);
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.05);
    }

    .quick-action-btn i:first-child {
        width: 56px;
        height: 56px;
        border-radius: 18px;
        background: #f8fafc;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        color: #94a3b8;
        transition: all 0.3s;
    }

    .quick-action-btn:hover i:first-child {
        background: var(--primary-gradient);
        color: #fff;
        box-shadow: 0 10px 20px rgba(45, 173, 255, 0.2);
    }

    .recent-child-item {
        padding: 28px 40px;
        border-bottom: 1px solid #f8fafc;
        display: flex;
        align-items: center;
        justify-content: space-between;
        transition: all 0.3s;
    }

    .recent-child-item:hover {
        background: #fcfdfe;
        padding-left: 50px;
    }

    .child-mini-avatar {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        background: var(--primary-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(45, 173, 255, 0.15);
    }

    .btn-dashboard {
        background: #f1f5f9;
        color: var(--text-muted);
        padding: 14px 28px;
        border-radius: 14px;
        font-weight: 700;
        font-size: 0.938rem;
        transition: all 0.3s;
        text-decoration: none !important;
    }

    .btn-dashboard:hover {
        background: var(--text-main);
        color: #fff;
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
    }
</style>

<div class="container container--fixed parent-dashboard-home">
    <div class="hero-welcome">
        <h1 class="page-title"><?php echo Label::getLabel('LBL_PARENT_DASHBOARD'); ?></h1>
        <p class="hero-subtitle"><?php echo Label::getLabel('LBL_MANAGE_YOUR_FAMILY_LEARNING_JOURNEY_IN_ONE_PLACE'); ?>
        </p>
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
                        class="quick-action-btn">
                        <i class="ion-person-add"></i>
                        <span><?php echo Label::getLabel('LBL_ADD_NEW_CHILD'); ?></span>
                        <i class="ion-chevron-right margin-left-auto" style="font-size: 1rem; opacity: 0.3;"></i>
                    </a>
                    <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
                        class="quick-action-btn">
                        <i class="ion-settings"></i>
                        <span><?php echo Label::getLabel('LBL_MANAGE_ALL_CHILDREN'); ?></span>
                        <i class="ion-chevron-right margin-left-auto" style="font-size: 1rem; opacity: 0.3;"></i>
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
                            class="btn--link fw-bold"
                            style="color: var(--accent-blue); text-decoration: none;"><?php echo Label::getLabel('LBL_VIEW_ALL'); ?></a>
                    <?php } ?>
                </div>
                <div class="content-card">
                    <?php if (empty($children)) { ?>
                        <div class="padding-40 text-center color-gray">
                            <p style="font-weight: 500;">
                                <?php echo Label::getLabel('LBL_ONLY_ADMIN_APPROVED_CHILDREN_LISTED_HERE'); ?></p>
                        </div>
                    <?php } else { ?>
                        <div class="recent-children-list">
                            <?php foreach (array_slice($children, 0, 10) as $child) {
                                $fullName = htmlspecialchars($child['user_first_name'] . ' ' . $child['user_last_name']);
                                $initial = CommonHelper::getFirstChar($child['user_first_name']);
                                $viewUrl = MyUtility::makeUrl('Parent', 'child', [$child['student_id']], CONF_WEBROOT_DASHBOARD);
                                ?>
                                <div class="recent-child-item">
                                    <div class="d-flex align-items-center gap-4">
                                        <div class="child-mini-avatar">
                                            <?php echo $initial; ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 700; color: var(--text-main); font-size: 1.125rem;">
                                                <?php echo $fullName; ?></div>
                                            <div style="font-size: 0.875rem; color: var(--text-muted); font-weight: 500;">
                                                <?php echo htmlspecialchars($child['user_email']); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <a href="<?php echo $viewUrl; ?>" class="btn-dashboard">
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