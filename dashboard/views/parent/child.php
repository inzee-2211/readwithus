<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
$fullName = htmlspecialchars(($child['user_first_name'] ?? '') . ' ' . ($child['user_last_name'] ?? ''));
$initial = CommonHelper::getFirstChar($child['user_first_name'] ?? 'C');
?>

<style>
    .child-dashboard {
        padding: 30px 0;
    }

    .page-header {
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        border-bottom: 1px solid #e2e8f0;
        padding-bottom: 20px;
    }

    .child-info-brief {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .child-avatar-large {
        width: 80px;
        height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, #2dadff 0%, #153e7d 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 800;
        color: #fff;
        box-shadow: 0 10px 15px -3px rgba(45, 173, 255, 0.4);
    }

    .child-title h1 {
        font-size: 1.875rem;
        font-weight: 800;
        color: #1a202c;
        margin: 0;
    }

    .child-title p {
        color: #718096;
        margin: 0;
        font-size: 1rem;
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 20px;
        margin-bottom: 40px;
    }

    .stat-card {
        background: #fff;
        padding: 20px;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-content h3 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0;
        line-height: 1;
    }

    .stat-content p {
        font-size: 0.875rem;
        color: #718096;
        margin: 4px 0 0;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.025em;
    }

    .dashboard-section {
        margin-bottom: 40px;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }

    .section-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        color: #2d3748;
        margin: 0;
    }

    .data-card {
        background: #fff;
        border-radius: 16px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .table-modern th {
        background: #f8fafc;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.05em;
        padding: 16px 24px;
        border-bottom: 1px solid #e2e8f0;
    }

    .table-modern td {
        padding: 16px 24px;
        vertical-align: middle;
        color: #1e293b;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-modern tr:last-child td {
        border-bottom: none;
    }

    .progress-bar-wrap {
        width: 100%;
        height: 8px;
        background: #f1f5f9;
        border-radius: 4px;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-bar-fill {
        height: 100%;
        transition: width 0.6s cubic-bezier(0.1, 0, 0, 1);
    }

    .badge-modern {
        padding: 6px 12px;
        border-radius: 6px;
        font-size: 0.75rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .badge-primary {
        background: #e0f2fe;
        color: #0369a1;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .teacher-pill {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .teacher-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .clickable-card {
        cursor: pointer;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .clickable-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #2dadff;
    }
</style>

<script>
    function showChildLoginMsg() {
        alert("<?php echo Label::getLabel('LBL_LOGIN_TO_YOUR_CHILD_PROFILE_DIRECTLY_TO_SEE_DETAILED_STATISTICS'); ?>");
    }
</script>

<div class="container container--fixed child-dashboard">
    <div class="page-header">
        <div class="child-info-brief">
            <div class="child-avatar-large"><?php echo $initial; ?></div>
            <div class="child-title">
                <h1><?php echo $fullName; ?></h1>
                <p><?php echo htmlspecialchars($child['user_email'] ?? ''); ?> •
                    <?php echo htmlspecialchars($child['parstd_relation'] ?? Label::getLabel('LBL_CHILD')); ?>
                </p>
            </div>
        </div>
        <div>
            <a class="btn btn--secondary btn--large"
                href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>"
                style="border-radius: 12px; font-weight: 700;">
                <i class="ion-arrow-left-c margin-right-2"></i> <?php echo Label::getLabel('LBL_BACK_TO_FAMILY'); ?>
            </a>
            <a class="btn btn--primary btn--large margin-left-2"
                href="<?php echo MyUtility::makeUrl('Parent', 'loginAsChild', [$child['student_id']], CONF_WEBROOT_DASHBOARD); ?>"
                style="border-radius: 12px; font-weight: 700; background: #2dadff; border: none;">
                <i class="ion-log-in margin-right-2"></i> <?php echo Label::getLabel('LBL_LOGIN_AS_CHILD'); ?>
            </a>
        </div>
    </div>

    <!-- Stats Overview -->
    <div class="stat-grid">
        <div class="stat-card clickable-card" onclick="showChildLoginMsg()">
            <div class="stat-icon" style="background: #e0f2fe; color: #0ea5e9;"><i class="ion-university"></i></div>
            <div class="stat-content">
                <h3><?php echo (int) $unlockedCoursesCount; ?></h3>
                <p><?php echo Label::getLabel('LBL_COURSES'); ?></p>
            </div>
        </div>
        <div class="stat-card clickable-card" onclick="showChildLoginMsg()">
            <div class="stat-icon" style="background: #fef3c7; color: #d97706;"><i class="ion-calendar"></i></div>
            <div class="stat-content">
                <h3><?php echo count($upcomingLessons); ?></h3>
                <p><?php echo Label::getLabel('LBL_UPCOMING'); ?></p>
            </div>
        </div>
        <div class="stat-card clickable-card" onclick="showChildLoginMsg()">
            <div class="stat-icon" style="background: #dcfce7; color: #16a34a;"><i class="ion-ribbon-b"></i></div>
            <div class="stat-content">
                <h3><?php echo (int) $attemptedQuizzesCount; ?></h3>
                <p><?php echo Label::getLabel('LBL_QUIZZES'); ?></p>
            </div>
        </div>
        <div class="stat-card clickable-card" onclick="showChildLoginMsg()">
            <div class="stat-icon" style="background: #f3e8ff; color: #9333ea;"><i class="ion-person-stalker"></i></div>
            <div class="stat-content">
                <h3><?php echo count($tutors); ?></h3>
                <p><?php echo Label::getLabel('LBL_TUTORS'); ?></p>
            </div>
        </div>
    </div>

    <!-- Profile Access Notice -->
    <div class="dashboard-section">
        <div class="content-card text-center"
            style="padding: 60px 40px; background: #f8fafc; border: 2px dashed #cbd5e1; border-radius: 24px;">
            <div style="margin-bottom: 24px;">
                <i class="ion-ios-information-outline" style="font-size: 5rem; color: #2dadff;"></i>
            </div>
            <h2 style="font-size: 1.75rem; font-weight: 800; color: #1e293b; margin-bottom: 16px;">
                <?php echo Label::getLabel('LBL_VIEW_DETAILED_STATISTICS'); ?>
            </h2>
            <p style="font-size: 1.125rem; color: #64748b; max-width: 600px; margin: 0 auto 32px; line-height: 1.6;">
                <?php echo Label::getLabel('LBL_PLEASE_OPEN_YOUR_CHILDS_PROFILE_TO_SEE_THE_DETAILS_OF_EACH_ASPECT_INCLUDING_COURSES_LESSONS_QUIZZES_AND_TUTORS'); ?>
            </p>
            <a class="btn btn--primary btn--large"
                href="<?php echo MyUtility::makeUrl('Parent', 'loginAsChild', [$child['student_id']], CONF_WEBROOT_DASHBOARD); ?>"
                style="padding: 16px 40px; border-radius: 14px; font-weight: 800; font-size: 1.1rem; background: #2dadff; border: none; box-shadow: 0 10px 15px -3px rgba(45, 173, 255, 0.4);">
                <?php echo Label::getLabel('LBL_OPEN_CHILDS_PROFILE'); ?>
            </a>
        </div>
    </div>
</div>