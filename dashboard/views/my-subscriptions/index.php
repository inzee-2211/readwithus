<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

/** @var array $subscription */
/** @var array $subjects */
/** @var string $pricingUrl */

$hasSubscription = !empty($subscription);

$packageName = '';
if ($hasSubscription) {
    $packageName = $subscription['spackage_title']
        ?? $subscription['spackage_name']
        ?? ('Package #' . ($subscription['package_id'] ?? ''));
}

$subjectLimit = $hasSubscription
    ? ($subscription['spackage_subject_limit'] ?? null)
    : null;

$startDate = $hasSubscription && !empty($subscription['start_date'])
    ? date('d M Y', strtotime($subscription['start_date']))
    : '-';

$endDate = $hasSubscription && !empty($subscription['end_date'])
    ? date('d M Y', strtotime($subscription['end_date']))
    : '-';

/** Helpers for usage display */
$usedSubjects = $hasSubscription ? count($subjects ?? []) : 0;
$remainingSubjects = ($hasSubscription && $subjectLimit !== null)
    ? max(0, (int)$subjectLimit - $usedSubjects)
    : null;

// Time remaining
$timeRemainingLabel = '';
if ($hasSubscription && !empty($subscription['end_date'])) {
    $now = new DateTime();
    $end = new DateTime($subscription['end_date']);

    if ($end > $now) {
        $diff      = $now->diff($end);
        $daysTotal = (int)$diff->format('%a');

        if ($daysTotal >= 1) {
            $timeRemainingLabel = $daysTotal . ' ' . Label::getLabel('LBL_DAYS_REMAINING');
        } else {
            $hours = $diff->h + ($diff->days * 24);
            $timeRemainingLabel = $hours . ' ' . Label::getLabel('LBL_HOURS_REMAINING');
        }
    } else {
        $timeRemainingLabel = Label::getLabel('LBL_PLAN_EXPIRED');
    }
}
?>

<style>
/* Full white background for dashboard area */
.dashboard,
.dashboard__primary,
.page,
.page__body {
    background: #ffffff;
}

/* Main wrapper card */
.subs-wrapper {
    background: #ffffff;
    border-radius: 16px;
    padding: 24px 24px 28px;
    box-shadow: 0 10px 30px rgba(15, 23, 42, 0.08);
    border: 1px solid #e5e7eb;
    min-height: 60vh;
}

/* Generic text helpers */
.subs-plan-title {
    font-size: 2.25rem;
    font-weight: 600;
    color: #111827;
    margin-bottom: 6px;
}

.subs-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px 24px;
    margin-top: 6px;
    font-size: 1rem;
}

.subs-meta__item {
    color: #4b5563;
}

.subs-meta__label {
    font-weight: 500;
    margin-right: 4px;
}

.subs-time-remaining {
    margin-top: 12px;
    font-size: 0.9rem;
    color: #111827;
}

.subs-divider {
    margin-top: 24px;
    margin-bottom: 20px;
    border-top: 1px solid #e5e7eb;
}

.subs-section-title {
    font-size: 1.05rem;
    font-weight: 600;
    margin-bottom: 12px;
    color: #111827;
}

.subs-empty-text {
    color: #6b7280;
    font-size: 0.92rem;
}

.subs-tags {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.subs-tag {
    display: inline-flex;
    align-items: center;
    padding: 4px 10px;
    border-radius: 999px;
    background: #e0f2fe;
    color: #0369a1;
    font-size: 0.85rem;
    font-weight: 500;
}

/* Solid danger button for cancel */
.btn--danger-solid {
    background: #ef4444;
    border-color: #ef4444;
    color: #ffffff !important;
}

.btn--danger-solid:hover,
.btn--danger-solid:focus {
    background: #dc2626;
    border-color: #dc2626;
    color: #ffffff !important;
}

/* Page head spacing tweaks */
.page__head {
    margin-bottom: 18px;
}

/* =========================
   NO-SUBSCRIPTION PANEL
========================= */
.subs-empty-panel {
    display: grid;
    grid-template-columns: minmax(0, 1.7fr) minmax(0, 1.1fr);
    gap: 28px;
    padding: 24px 26px 26px;
    border-radius: 18px;
    background: linear-gradient(135deg, #f9fafb 0%, #eff6ff 100%);
    border: 1px solid #dbe3f0;
    box-shadow: 0 10px 28px rgba(15, 23, 42, 0.06);
}

.subs-empty-left {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.subs-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 6px 14px;
    border-radius: 999px;
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: .08em;
    text-transform: uppercase;
    color: #0369a1;
    background: rgba(219, 234, 254, 0.9);
    border: 1px solid #bfdbfe;
}

.subs-empty-title {
    font-size: 1.45rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0;
}

.subs-empty-text {
    font-size: 0.98rem;
    color: #4b5563;
    max-width: 520px;
}

.subs-benefits-list {
    margin: 4px 0 0;
    padding-left: 0;
    list-style: none;
}

.subs-benefits-list li {
    position: relative;
    padding-left: 24px;
    margin-bottom: 6px;
    font-size: 0.95rem;
    color: #111827;
}

.subs-benefits-list li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 1px;
    width: 18px;
    height: 18px;
    border-radius: 999px;
    background: #dcfce7;
    color: #15803d;
    font-size: 11px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* CTA button */
.subs-empty-cta {
    margin-top: 12px;
}

.subs-cta-btn {
    display: inline-flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: center;
    gap: 2px;
    padding: 10px 18px 8px;
    border-radius: 999px;
    background: linear-gradient(135deg, #2DADFF 0%, #14A3FF 100%);
    border: none;
    color: #ffffff !important;
    font-weight: 600;
    font-size: 0.95rem;
    box-shadow: 0 10px 24px rgba(37, 99, 235, 0.3);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
    height: auto !important;      /* override base .btn height */
    line-height: 1.25;
    white-space: normal;          /* allow subtext to wrap if needed */
}

.subs-cta-btn span {
    display: block;
}

.subs-cta-subtext {
    font-size: 0.78rem;
    font-weight: 500;
    opacity: 0.95;
}

.subs-cta-btn:hover,
.subs-cta-btn:focus {
    transform: translateY(-1px);
    box-shadow: 0 16px 32px rgba(37, 99, 235, 0.35);
    filter: brightness(1.03);
}

.subs-cta-helper {
    margin-top: 6px;
    font-size: 0.8rem;
    color: #6b7280;
}

/* Right-side highlight card */
.subs-empty-right {
    display: flex;
    align-items: stretch;
    justify-content: flex-end;
}

.subs-highlight-card {
    background: #ffffff;
    border-radius: 16px;
    padding: 16px 18px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
    font-size: 0.9rem;
    color: #111827;
    max-width: 320px;
    width: 100%;
}

.subs-highlight-card h5 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 8px;
    color: #0f172a;
}

.subs-highlight-card ul {
    margin: 0;
    padding-left: 18px;
}

.subs-highlight-card li {
    margin-bottom: 4px;
    color: #4b5563;
}

/* =========================
   CURRENT SUBSCRIPTION CARD
========================= */
.subs-plan {
    border-radius: 20px;
    padding: 20px 22px 24px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    box-shadow: 0 18px 40px rgba(15, 23, 42, 0.06);
}

/* header strip */
.subs-plan-header {
    padding: 14px 16px 16px;
    border-radius: 16px;
    background: radial-gradient(circle at top left, #e0f2fe 0, #eff6ff 38%, #ffffff 85%);
    border: 1px solid #dbeafe;
    margin-bottom: 18px;
}

.subs-plan-pill {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 0.76rem;
    font-weight: 600;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    color: #1d4ed8;
    background: rgba(219, 234, 254, 0.9);
    border: 1px solid #bfdbfe;
    margin-bottom: 6px;
}

.subs-plan-header-main {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    gap: 16px;
    margin-bottom: 8px;
}

.subs-plan-title-main {
    font-size: 1.6rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 4px;
}

.subs-plan-usage {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 10px;
    font-size: 0.9rem;
}

.subs-plan-usage-current {
    color: #111827;
    font-weight: 500;
}

.subs-plan-usage-remaining {
    padding: 2px 8px;
    border-radius: 999px;
    background: #ecfdf5;
    color: #15803d;
    font-size: 0.8rem;
    font-weight: 600;
}

.subs-plan-status-wrap {
    display: flex;
    flex-direction: column;
    align-items: flex-end;
    gap: 4px;
}

.subs-plan-status-label {
    font-size: 0.78rem;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
}

/* meta row under header */
.subs-plan-header-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 6px 18px;
    font-size: 0.88rem;
}

/* body layout */
.subs-plan-body {
    display: grid;
    grid-template-columns: minmax(0, 2fr) minmax(0, 1.3fr);
    gap: 24px;
    margin-top: 6px;
}

.subs-plan-body-left {
    padding-right: 8px;
    border-right: 1px solid #e5e7eb;
}

.subs-plan-body-right {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

/* small summary card on the right */
.subs-stat-card {
    background: #f9fafb;
    border-radius: 14px;
    padding: 12px 14px;
    border: 1px solid #e5e7eb;
    font-size: 0.9rem;
    color: #111827;
}

.subs-stat-card h5 {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 6px;
    color: #0f172a;
}

.subs-stat-card ul {
    margin: 0;
    padding-left: 18px;
}

.subs-stat-card li {
    margin-bottom: 4px;
    color: #4b5563;
}

/* cancel button + helper */
.subs-cancel-btn {
    width: 100%;
    margin-top: 8px;
}

.subs-cancel-helper {
    font-size: 0.8rem;
    color: #6b7280;
}

/* =========================
   RESPONSIVE
========================= */
@media (max-width: 1024px) {
    .subs-wrapper {
        padding: 18px 16px 22px;
    }
}

@media (max-width: 768px) {
    .subs-wrapper {
        padding: 16px 14px 20px;
        border-radius: 12px;
    }

    .page__head h1 {
        font-size: 1.2rem;
    }

    .subs-meta {
        flex-direction: column;
        gap: 6px;
    }

    .text-md-right {
        text-align: left !important;
    }

    .subs-empty-panel {
        grid-template-columns: 1fr;
        padding: 20px 16px 20px;
        border-radius: 14px;
        gap: 18px;
    }

    .subs-empty-right {
        justify-content: flex-start;
    }

    .subs-highlight-card {
        max-width: 100%;
    }

    .subs-plan {
        padding: 16px 14px 18px;
    }

    .subs-plan-header {
        padding: 12px 12px 14px;
    }

    .subs-plan-header-main {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }

    .subs-plan-body {
        grid-template-columns: 1fr;
        gap: 18px;
    }

    .subs-plan-body-left {
        border-right: none;
        border-bottom: 1px solid #e5e7eb;
        padding-right: 0;
        padding-bottom: 14px;
    }

    .subs-plan-body-right {
        align-items: stretch;
    }
}
/* Benefits block under "Unlocked Subjects" */
.subs-plan-benefits {
    margin-top: 18px;
    padding-top: 12px;
    border-top: 1px dashed #e5e7eb;
}

.subs-plan-benefits h5 {
    font-size: 0.95rem;
    font-weight: 600;
    color: #0f172a;
    margin: 0 0 6px;
}

.subs-plan-benefits ul {
    margin: 0;
    padding-left: 0;
    list-style: none;
}

.subs-plan-benefits li {
    position: relative;
    padding-left: 22px;
    margin-bottom: 4px;
    font-size: 0.9rem;
    color: #4b5563;
}

.subs-plan-benefits li::before {
    content: "✓";
    position: absolute;
    left: 0;
    top: 1px;
    width: 16px;
    height: 16px;
    border-radius: 999px;
    background: #dcfce7;
    color: #15803d;
    font-size: 10px;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* keep spacing sane on mobile */
@media (max-width: 768px) {
    .subs-plan-benefits {
        margin-top: 14px;
        padding-top: 10px;
    }
}

</style>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">

            <div class="page__head d-flex justify-content-between align-items-center">
                <h1><?php echo Label::getLabel('LBL_MY_SUBSCRIPTIONS'); ?></h1>

                <?php if ($hasSubscription) { ?>
                    <a href="<?php echo $pricingUrl; ?>" class="btn btn--primary">
                        <?php echo Label::getLabel('LBL_UPGRADE_PLAN'); ?>
                    </a>
                <?php } else { ?>
                    <a href="<?php echo $pricingUrl; ?>" class="btn btn--primary">
                        <?php echo Label::getLabel('LBL_BROWSE_PLANS'); ?>
                    </a>
                <?php } ?>
            </div>

            <div class="page__body">
                <div class="subs-wrapper">
                    <?php if (!$hasSubscription) { ?>
                        <!-- ===== NO ACTIVE SUBSCRIPTION ===== -->
                        <div class="subs-empty-panel">
                            <div class="subs-empty-left">
                                <span class="subs-pill">
                                    <?php echo Label::getLabel('LBL_START_YOUR_LEARNING_JOURNEY'); ?>
                                </span>

                                <h4 class="subs-empty-title">
                                    <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?>
                                </h4>

                                <p class="subs-empty-text">
                                    <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION_DESC'); ?>
                                </p>

                                <ul class="subs-benefits-list">
                                    <li>Unlimited access to all subjects in your chosen plan.</li>
                                    <li>Topic-wise videos, practice quizzes and exam-style questions.</li>
                                    <li>Smart progress tracking and performance insights.</li>
                                    <li>Study anytime, cancel anytime – no long-term contracts.</li>
                                </ul>

                                <div class="subs-empty-cta">
                                    <a href="<?php echo $pricingUrl; ?>" class="btn subs-cta-btn">
                                        <span><?php echo Label::getLabel('LBL_SEE_PLANS'); ?></span>
                                        <span class="subs-cta-subtext">
                                            <?php echo Label::getLabel('LBL_CANCEL_ANYTIME'); ?>
                                        </span>
                                    </a>
                                    <p class="subs-cta-helper">
                                        <?php echo Label::getLabel('LBL_SUBS_NO_CARD_CHARGE_UNTIL_CHECKOUT'); ?>
                                    </p>
                                </div>
                            </div>

                            <div class="subs-empty-right">
                                <div class="subs-highlight-card">
                                    <h5><?php echo Label::getLabel('LBL_PERFECT_FOR_KS2_GCSE'); ?></h5>
                                    <ul>
                                        <li>Structured paths for KS2 – GCSE, aligned with UK curriculum.</li>
                                        <li>Mix of human tutors + AI-assisted practice for faster progress.</li>
                                        <li>Ideal for homework help, exam prep and closing learning gaps.</li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                    <?php } else { ?>
                        <!-- ===== CURRENT SUBSCRIPTION CARD ===== -->
                        <div class="subs-plan">
                            <div class="subs-plan-header">
                                <div class="subs-plan-pill">
                                    <?php echo Label::getLabel('LBL_YOUR_CURRENT_PLAN'); ?>
                                </div>

                                <div class="subs-plan-header-main">
                                    <div>
                                        <h4 class="subs-plan-title-main">
                                            <?php echo htmlspecialchars($packageName); ?>
                                        </h4>

                                        <?php if ($subjectLimit !== null) { ?>
                                            <div class="subs-plan-usage">
                                                <span class="subs-plan-usage-current">
                                                    <?php echo $usedSubjects; ?> / <?php echo (int)$subjectLimit; ?>
                                                    <?php echo Label::getLabel('LBL_SUBJECTS_SELECTED'); ?>
                                                </span>
                                                <?php if ($remainingSubjects !== null && $remainingSubjects > 0) { ?>
                                                    <span class="subs-plan-usage-remaining">
                                                        <?php echo $remainingSubjects; ?>
                                                        <?php echo Label::getLabel('LBL_SUBJECTS_LEFT'); ?>
                                                    </span>
                                                <?php } ?>
                                            </div>
                                        <?php } ?>
                                    </div>

                                    <div class="subs-plan-status-wrap">
                                        <span class="subs-meta__label subs-plan-status-label">
                                            <?php echo Label::getLabel('LBL_STATUS'); ?>
                                        </span>
                                        <?php if ($subscription['status'] === 'active') { ?>
                                            <span class="badge badge--success">
                                                <?php echo ucwords($subscription['status']); ?>
                                            </span>
                                        <?php } else { ?>
                                            <span class="badge badge--secondary">
                                                <?php echo ucwords($subscription['status']); ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>

                                <div class="subs-plan-header-meta">
                                    <div class="subs-meta__item">
                                        <span class="subs-meta__label">
                                            <?php echo Label::getLabel('LBL_START_DATE'); ?>:
                                        </span>
                                        <span><?php echo $startDate; ?></span>
                                    </div>
                                    <div class="subs-meta__item">
                                        <span class="subs-meta__label">
                                            <?php echo Label::getLabel('LBL_END_DATE'); ?>:
                                        </span>
                                        <span><?php echo $endDate; ?></span>
                                    </div>
                                    <?php if ($timeRemainingLabel) { ?>
                                        <div class="subs-meta__item">
                                            <span class="subs-meta__label">
                                                <?php echo Label::getLabel('LBL_TIME_REMAINING'); ?>:
                                            </span>
                                            <span><?php echo $timeRemainingLabel; ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>

                            <div class="subs-plan-body">
                                <div class="subs-plan-body-left">
    <h4 class="subs-section-title">
        <?php echo Label::getLabel('LBL_UNLOCKED_SUBJECTS'); ?>
    </h4>

    <?php if (empty($subjects)) { ?>
        <p class="subs-empty-text">
            <?php echo Label::getLabel('LBL_NO_SUBJECTS_SELECTED_YET'); ?>
        </p>
    <?php } else { ?>
        <div class="subs-tags">
            <?php foreach ($subjects as $subject) { ?>
                <span class="subs-tag">
                    <?php echo htmlspecialchars($subject['subject_title']); ?>
                </span>
            <?php } ?>
        </div>
    <?php } ?>

    <!-- NEW: benefits of this plan under unlocked subjects -->
    <div class="subs-plan-benefits">
        <h5><?php echo Label::getLabel('LBL_WHATS_INCLUDED_IN_YOUR_PLAN'); ?></h5>
        <ul>
            <li>AI Tutor support for step-by-step help on topics.</li>
            <li>Unlimited practice quizzes & exam-style questions.</li>
            <li>Topic-wise video lessons .</li>
        </ul>
    </div>
</div>


                                <div class="subs-plan-body-right">
                                    <div class="subs-stat-card">
                                        <h5><?php echo Label::getLabel('LBL_PLAN_OVERVIEW'); ?></h5>
                                        <ul>
                                            <?php if ($subjectLimit !== null) { ?>
                                                <li>
                                                    <strong><?php echo $usedSubjects; ?></strong>
                                                    / <?php echo (int)$subjectLimit; ?>
                                                    <?php echo Label::getLabel('LBL_SUBJECTS_IN_YOUR_PLAN'); ?>
                                                </li>
                                            <?php } ?>
                                            <li>
                                                <?php echo Label::getLabel('LBL_ACCESS_ALL_QUIZZES_AND_VIDEOS'); ?>
                                            </li>
                                            <li>
                                                <?php echo Label::getLabel('LBL_PROGRESS_TRACKING_AND_REPORTS'); ?>
                                            </li>
                                        </ul>
                                    </div>

                                    <button
                                        type="button"
                                        class="btn btn--danger-solid subs-cancel-btn"
                                        onclick="MySubscriptions.cancelSubscription(<?php echo (int)$subscription['user_sub_id']; ?>);">
                                        <?php echo Label::getLabel('LBL_CANCEL_SUBSCRIPTION'); ?>
                                    </button>

                                    <p class="subs-cancel-helper">
                                        <?php echo Label::getLabel('LBL_YOU_KEEP_ACCESS_UNTIL_END_DATE'); ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var MySubscriptions = (function () {
    function cancelSubscription(userSubId) {
        if (!confirm('<?php echo Label::getLabel('MSG_CONFIRM_CANCEL_SUBSCRIPTION'); ?>')) {
            return;
        }

        fcom.ajax(
            fcom.makeUrl('MySubscriptions', 'cancel'),
            {user_sub_id: userSubId},
            function (res) {
                try {
                    var ans = JSON.parse(res);
                    if (ans.status == 1) {
                        $.mbsmessage(ans.msg, true, 'alert--success');
                        setTimeout(function () {
                            window.location.reload();
                        }, 700);
                    } else {
                        $.mbsmessage(ans.msg, true, 'alert--danger');
                    }
                } catch (e) {
                    $.mbsmessage('<?php echo Label::getLabel('MSG_SOMETHING_WENT_WRONG'); ?>', true, 'alert--danger');
                }
            }
        );
    }

    return {
        cancelSubscription: cancelSubscription
    };
})();
</script>
