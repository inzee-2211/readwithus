<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
$fullName = htmlspecialchars(($child['user_first_name'] ?? '') . ' ' . ($child['user_last_name'] ?? ''));
$initial = CommonHelper::getFirstChar($child['user_first_name'] ?? 'C');
?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%);
        --surface-glass: rgba(255, 255, 255, 0.7);
        --border-glass: rgba(255, 255, 255, 0.3);
        --shadow-premium: 0 20px 40px rgba(0, 0, 0, 0.05);
        --text-main: #1e293b;
        --text-muted: #64748b;
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

    @keyframes pulseSoft {
        0% {
            transform: scale(1);
            box-shadow: 0 10px 15px -3px rgba(45, 173, 255, 0.4);
        }

        50% {
            transform: scale(1.02);
            box-shadow: 0 20px 30px -5px rgba(45, 173, 255, 0.2);
        }

        100% {
            transform: scale(1);
            box-shadow: 0 10px 15px -3px rgba(45, 173, 255, 0.4);
        }
    }

    .child-dashboard {
        padding: 40px 0;
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .premium-header {
        background: #fff;
        border-radius: 32px;
        padding: 32px;
        margin-bottom: 40px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border: 1px solid #f1f5f9;
        box-shadow: var(--shadow-premium);
        flex-wrap: wrap;
        gap: 24px;
    }

    .child-info-brief {
        display: flex;
        align-items: center;
        gap: 24px;
    }

    .child-avatar-glow {
        position: relative;
    }

    .child-avatar-glow::before {
        content: '';
        position: absolute;
        inset: -4px;
        background: var(--primary-gradient);
        border-radius: 24px;
        z-index: 0;
        opacity: 0.3;
        filter: blur(8px);
    }

    .child-avatar-large {
        width: 88px;
        height: 88px;
        border-radius: 22px;
        background: var(--primary-gradient);
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.25rem;
        font-weight: 800;
        color: #fff;
        box-shadow: 0 10px 20px rgba(45, 173, 255, 0.3);
    }

    .child-title h1 {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        letter-spacing: -0.02em;
    }

    .child-title p {
        color: var(--text-muted);
        margin: 4px 0 0;
        font-size: 1.1rem;
        font-weight: 500;
    }

    .header-actions {
        display: flex;
        gap: 16px;
    }

    .btn-premium-outline {
        padding: 14px 28px;
        border-radius: 16px;
        border: 2px solid #f1f5f9;
        color: var(--text-main);
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        background: #fff;
    }

    .btn-premium-outline:hover {
        border-color: #2dadff;
        color: #2dadff;
        transform: translateY(-2px);
    }

    .btn-premium-solid {
        padding: 14px 28px;
        border-radius: 16px;
        background: var(--primary-gradient);
        color: #fff !important;
        font-weight: 700;
        text-decoration: none !important;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        border: none;
        box-shadow: 0 10px 20px rgba(45, 173, 255, 0.2);
    }

    .btn-premium-solid:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(45, 173, 255, 0.3);
    }

    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 24px;
        margin-bottom: 60px;
    }

    .stat-card-glass {
        background: #fff;
        padding: 28px;
        border-radius: 28px;
        border: 1px solid #f1f5f9;
        display: flex;
        flex-direction: column;
        gap: 16px;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        cursor: pointer;
        box-shadow: var(--shadow-premium);
        position: relative;
        overflow: hidden;
    }

    .stat-card-glass:hover {
        transform: translateY(-8px);
        border-color: #2dadff;
    }

    .stat-icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
    }

    .stat-content h3 {
        font-size: 2rem;
        font-weight: 800;
        color: var(--text-main);
        margin: 0;
        line-height: 1;
    }

    .stat-content p {
        font-size: 0.813rem;
        color: var(--text-muted);
        margin: 8px 0 0;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .cta-container {
        margin-bottom: 60px;
    }

    .cta-card-premium {
        background: #fff;
        border-radius: 40px;
        padding: 80px 40px;
        text-align: center;
        border: 1px solid #f1f5f9;
        box-shadow: var(--shadow-premium);
        position: relative;
        overflow: hidden;
    }

    .cta-icon-wrap {
        width: 120px;
        height: 120px;
        background: #f8fafc;
        border-radius: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 32px;
        color: #2dadff;
        font-size: 4rem;
        box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.02);
    }

    .cta-card-premium h2 {
        font-size: 2.25rem;
        font-weight: 900;
        color: var(--text-main);
        margin-bottom: 20px;
        letter-spacing: -0.03em;
    }

    .cta-card-premium p {
        font-size: 1.125rem;
        color: var(--text-muted);
        max-width: 650px;
        margin: 0 auto 40px;
        line-height: 1.7;
    }

    .btn-cta-pulse {
        animation: pulseSoft 3s infinite ease-in-out;
        padding: 20px 48px;
        font-size: 1.125rem;
    }
</style>

<script>
    function showChildLoginMsg() {
        alert("<?php echo Label::getLabel('LBL_LOGIN_TO_YOUR_CHILD_PROFILE_DIRECTLY_TO_SEE_DETAILED_STATISTICS'); ?>");
    }
</script>

<div class="container container--fixed child-dashboard">
    <!-- Premium Profile Header -->
    <div class="premium-header">
        <div class="child-info-brief">
            <div class="child-avatar-glow">
                <div class="child-avatar-large">
                    <?php echo $initial; ?>
                </div>
            </div>
            <div class="child-title">
                <h1>
                    <?php echo $fullName; ?>
                </h1>
                <p>
                    <?php echo htmlspecialchars($child['user_email'] ?? ''); ?> •
                    <span style="color: #2dadff;">
                        <?php echo htmlspecialchars($child['parstd_relation'] ?? Label::getLabel('LBL_CHILD')); ?>
                    </span>
                </p>
            </div>
        </div>
        <div class="header-actions">
            <a class="btn-premium-outline"
                href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
                <i class="ion-arrow-left-c"></i>
                <?php echo Label::getLabel('LBL_BACK'); ?>
            </a>
            <a class="btn-premium-solid"
                href="<?php echo MyUtility::makeUrl('Parent', 'loginAsChild', [$child['student_id']], CONF_WEBROOT_DASHBOARD); ?>">
                <i class="ion-log-in"></i>
                <?php echo Label::getLabel('LBL_LOGIN_AS_CHILD'); ?>
            </a>
        </div>
    </div>

    <!-- Stats Overview Grid -->
    <div class="stat-grid">
        <div class="stat-card-glass" onclick="showChildLoginMsg()">
            <div class="stat-icon-circle" style="background: #e0f2fe; color: #0ea5e9;"><i class="ion-university"></i>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo (int) $unlockedCoursesCount; ?>
                </h3>
                <p>
                    <?php echo Label::getLabel('LBL_COURSES'); ?>
                </p>
            </div>
        </div>
        <div class="stat-card-glass" onclick="showChildLoginMsg()">
            <div class="stat-icon-circle" style="background: #fef3c7; color: #d97706;"><i class="ion-calendar"></i>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo count($upcomingLessons); ?>
                </h3>
                <p>
                    <?php echo Label::getLabel('LBL_UPCOMING'); ?>
                </p>
            </div>
        </div>
        <div class="stat-card-glass" onclick="showChildLoginMsg()">
            <div class="stat-icon-circle" style="background: #dcfce7; color: #16a34a;"><i class="ion-ribbon-b"></i>
            </div>
            <div class="stat-content">
                <h3>
                    <?php echo (int) $attemptedQuizzesCount; ?>
                </h3>
                <p>
                    <?php echo Label::getLabel('LBL_QUIZZES'); ?>
                </p>
            </div>
        </div>
        <div class="stat-card-glass" onclick="showChildLoginMsg()">
            <div class="stat-icon-circle" style="background: #f3e8ff; color: #9333ea;"><i
                    class="ion-person-stalker"></i></div>
            <div class="stat-content">
                <h3>
                    <?php echo count($tutors); ?>
                </h3>
                <p>
                    <?php echo Label::getLabel('LBL_TUTORS'); ?>
                </p>
            </div>
        </div>
    </div>

    <!-- High-Impact Access Notice -->
    <div class="cta-container">
        <div class="cta-card-premium">
            <div class="cta-icon-wrap">
                <i class="ion-ios-search-strong"></i>
            </div>
            <h2>
                <?php echo Label::getLabel('LBL_DIVE_DEEPER_INTO_THEIR_PROGRESS'); ?>
            </h2>
            <p>
                <?php echo Label::getLabel('LBL_TO_VIEW_FULL_TRANSCRIPTS_GRADES_AND_LESSON_DETAILS_PLEASE_STEP_INTO_YOUR_CHILDS_PERSONAL_DASHBOARD'); ?>
            </p>
            <a class="btn-premium-solid btn-cta-pulse"
                href="<?php echo MyUtility::makeUrl('Parent', 'loginAsChild', [$child['student_id']], CONF_WEBROOT_DASHBOARD); ?>">
                <i class="ion-happy-outline"></i>
                <?php echo Label::getLabel('LBL_OPEN_CHILDS_PROFILE'); ?>
            </a>
        </div>
    </div>
</div>