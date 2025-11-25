<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();

$primaryColor = '#2DADFF';

/** Safely extract attempt row */
$attemptRow      = $attemptresult[0] ?? [];
$resultText      = '';
$subtopic_id     = null;
$totalQuestions  = 0;
$correct         = 0;
$incorrect       = 0;
$accuracy        = 0;

if (!empty($attemptRow)) {
    $resultText  = strtolower(trim($attemptRow['result'] ?? ''));
    $subtopic_id = $attemptRow['subtopic_id'] ?? null;
    $totalQuestions = (int)($attemptRow['total_questions'] ?? 0);
}

/** Compute correct/incorrect/accuracy from $attemptquestions */
if (!empty($attemptquestions) && is_array($attemptquestions)) {
    $totalQuestions = count($attemptquestions);
    foreach ($attemptquestions as $q) {
        if (!empty($q['is_correct']) && (int)$q['is_correct'] === 1) {
            $correct++;
        }
    }
}
if ($totalQuestions > 0) {
    $incorrect = $totalQuestions - $correct;
    $accuracy  = round(($correct / $totalQuestions) * 100);
}

/** Fallback for current subtopic id (for Next / Requiz buttons) */
$currentSubtopicId = $currentSubtopicId ?? $subtopic_id ?? ($_SESSION['subtopicId'] ?? null);
?>

<style>
    :root {
        --rwu-primary: <?= $primaryColor ?>;
        --rwu-secondary: #0b84d9;
        --rwu-success: #28c76f;
        --rwu-danger: #ff4d4f;
        --rwu-warning: #fbbf24;
        --rwu-info: #38bdf8;
        --rwu-light: #f5f7fb;
        --rwu-dark: #111827;
        --rwu-radius: 14px;
        --rwu-shadow: 0 12px 32px rgba(15, 23, 42, 0.08);
        --rwu-transition: all 0.3s ease;
    }

    .quiz-results-page {
        background: #f3f6fb;
        padding: 40px 0 60px;
        font-family: "Inter", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
    }

    .quiz-results-card {
        border-radius: var(--rwu-radius);
        box-shadow: var(--rwu-shadow);
        border: none;
        overflow: hidden;
        background: #fff;
        margin-bottom: 30px;
    }

    .quiz-header-modern {
        background: radial-gradient(circle at top left, rgba(45, 173, 255, 0.45), transparent 55%),
                    radial-gradient(circle at bottom right, rgba(15, 23, 42, 0.8), transparent 60%),
                    linear-gradient(135deg, #2DADFF, #020617);
        color: #fff;
        padding: 24px 28px;
    }

    .quiz-header-modern h1 {
        font-size: 26px;
        font-weight: 700;
        margin-bottom: 6px;
    }

    .quiz-header-modern p {
        margin-bottom: 0;
        opacity: 0.85;
    }

    .quiz-result-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 18px;
        border-radius: 999px;
        font-weight: 600;
        font-size: 14px;
        background: rgba(15, 23, 42, 0.85);
        color: #fff;
        border: 1px solid rgba(148, 163, 184, 0.45);
    }

    .quiz-result-badge.pass {
        background: rgba(22, 163, 74, 0.14);
        color: #bbf7d0;
        border-color: rgba(22, 163, 74, 0.55);
    }

    .quiz-result-badge.fail {
        background: rgba(239, 68, 68, 0.16);
        color: #fecaca;
        border-color: rgba(239, 68, 68, 0.6);
    }

    .quiz-header-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 14px;
        font-size: 13px;
        color: #e5e7eb;
        margin-top: 10px;
    }

    .quiz-header-meta span {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.65);
        border: 1px solid rgba(148, 163, 184, 0.4);
    }

    .quiz-header-meta svg {
        width: 14px;
        height: 14px;
    }

    .quiz-results-card .card-body {
        padding: 22px 24px 24px;
    }

    .section-title-modern {
        font-size: 17px;
        font-weight: 600;
        margin-bottom: 14px;
        position: relative;
        padding-bottom: 6px;
        color: #0f172a;
    }

    .section-title-modern::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: 0;
        height: 3px;
        width: 64px;
        border-radius: 999px;
        background: linear-gradient(90deg, var(--rwu-primary), var(--rwu-secondary));
    }

    /* Question indicators */
    .question-indicators-wrap {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
    }

    .question-indicator-chip {
        width: 38px;
        height: 38px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
        font-weight: 600;
        cursor: default;
        transition: var(--rwu-transition);
        border: 1px solid transparent;
    }

    .question-indicator-chip.correct {
        background: rgba(34, 197, 94, 0.12);
        color: #16a34a;
        border-color: rgba(22, 163, 74, 0.3);
    }

    .question-indicator-chip.incorrect {
        background: rgba(239, 68, 68, 0.12);
        color: #dc2626;
        border-color: rgba(239, 68, 68, 0.3);
    }

    .question-indicator-chip:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(15, 23, 42, 0.08);
    }

    /* Stat cards */
    .stat-card-modern {
        background: #fff;
        border-radius: 16px;
        padding: 18px 16px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.06);
        border: 1px solid rgba(148, 163, 184, 0.3);
        text-align: center;
        height: 100%;
    }

    .stat-label {
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        color: #6b7280;
        margin-bottom: 4px;
    }

    .stat-value {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 3px;
        color: #111827;
    }

    .stat-meta {
        font-size: 12px;
        color: #9ca3af;
    }

    /* Circular score */
    .score-circle-wrap {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .score-circle {
        position: relative;
        width: 132px;
        height: 132px;
    }

    .score-circle svg {
        transform: rotate(-90deg);
    }

    .score-circle-bg {
        fill: none;
        stroke: #e5e7eb;
        stroke-width: 8;
    }

    .score-circle-progress {
        fill: none;
        stroke: var(--rwu-primary);
        stroke-width: 8;
        stroke-linecap: round;
        transition: stroke-dashoffset 0.6s ease;
    }

    .score-circle-text {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        font-weight: 700;
        color: #0f172a;
    }

    .score-circle-text span {
        font-size: 22px;
        line-height: 1;
    }

    .score-circle-text small {
        font-size: 12px;
        color: #6b7280;
        font-weight: 500;
    }

    /* Video card */
    .video-card-modern {
        border-radius: var(--rwu-radius);
        box-shadow: var(--rwu-shadow);
        border: none;
        overflow: hidden;
        background: #fff;
        height: 100%;
    }

    .video-card-modern .card-header {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 18px;
    }

    .video-card-modern .card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

.video-frame-wrap {
    position: relative;
    width: 100%;
    padding-top: 20%; /* 🔹 Makes height equal to width */
    border-radius: 12px;
    overflow: hidden;
    background: #000;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.4);
}

    .video-frame-wrap iframe {
        width: 100%;
        padding-bottom:20% ;
        height: 100%;
        /* min-height: 220px; */
        border: none;
        display: block;
         object-fit: cover;
    }

    .video-topic-title {
        text-align: center;
        margin-top: 12px;
        font-size: 14px;
        color: #4b5563;
    }

    /* Next steps card */
    .next-steps-card {
        border-radius: var(--rwu-radius);
        box-shadow: var(--rwu-shadow);
        border: none;
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .next-steps-card .card-header {
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
        padding: 14px 18px;
    }

    .next-steps-card .card-header h5 {
        margin: 0;
        font-size: 16px;
        font-weight: 600;
    }

    .next-steps-card .card-body {
        padding: 18px 18px 20px;
        display: flex;
        flex-direction: column;
    }

    .next-steps-card p.desc {
        font-size: 14px;
        color: #6b7280;
        margin-bottom: 16px;
    }

    .btn-pill {
        border-radius: 999px;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 18px;
        border: none;
        transition: var(--rwu-transition);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-pill-primary {
        background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
        color: #fff;
        box-shadow: 0 10px 24px rgba(45, 173, 255, 0.35);
    }

    .btn-pill-primary:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 32px rgba(37, 99, 235, 0.45);
    }

    .btn-pill-outline {
        background: transparent;
        border: 1px solid rgba(148, 163, 184, 0.7);
        color: #111827;
    }

    .btn-pill-outline:hover {
        background: rgba(15, 23, 42, 0.03);
    }

    .btn-pill-danger {
        background: linear-gradient(135deg, #f97373, #ef4444);
        color: #fff;
        box-shadow: 0 10px 24px rgba(239, 68, 68, 0.35);
    }

    .btn-pill-danger:hover {
        transform: translateY(-1px);
        box-shadow: 0 14px 32px rgba(185, 28, 28, 0.45);
    }

    /* Recommended courses */
    .recommended-section {
        margin-top: 34px;
        margin-bottom: 40px;
    }

    .featured-course-card {
        border-radius: var(--rwu-radius);
        box-shadow: var(--rwu-shadow);
        border: none;
        background: #fff;
        height: 100%;
        overflow: hidden;
    }

    .featured-course-card img {
        border-radius: 12px;
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .featured-course-body {
        padding: 18px 18px 20px;
        display: flex;
        flex-direction: column;
        height: 100%;
    }

    .featured-course-title {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin-bottom: 8px;
    }

    .course-rating-stars {
        font-size: 13px;
        color: #fbbf24;
        margin-bottom: 4px;
    }

    .course-rating-stars i {
        margin-right: 1px;
    }

    .course-rating-meta {
        font-size: 12px;
        color: #6b7280;
    }

    .course-price-pill {
        font-weight: 700;
        font-size: 18px;
        color: var(--rwu-primary);
    }

    .course-mini-card {
        border-radius: 12px;
        box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
        border: 1px solid rgba(148, 163, 184, 0.3);
        background: #fff;
        height: 100%;
        overflow: hidden;
    }

    .course-mini-card img {
        width: 100%;
        height: 120px;
        object-fit: cover;
    }

    .course-mini-body {
        padding: 10px 12px 12px;
    }

    .course-mini-body h6 {
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 4px;
    }

    .course-mini-body p {
        font-size: 12px;
        color: #6b7280;
        margin-bottom: 6px;
    }

    .course-mini-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 12px;
    }

    .course-mini-footer a {
        font-weight: 600;
        color: var(--rwu-primary);
        text-decoration: none;
    }

    .course-mini-footer a:hover {
        text-decoration: underline;
    }

    /* Tutor section */
    .tutor-section-modern {
        background: linear-gradient(135deg, #2DADFF, #020617);
        border-radius: 24px;
        padding: 28px 24px;
        color: white;
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.7);
    }

    .tutor-section-modern img {
        border-radius: 18px;
        width: 100%;
        object-fit: cover;
    }

    .tutor-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 3px 10px;
        border-radius: 999px;
        background: rgba(15, 23, 42, 0.8);
        border: 1px solid rgba(148, 163, 184, 0.5);
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        color: #9ca3af;
        margin-bottom: 10px;
    }

    .tutor-section-modern h3 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #f9fafb;
    }

    .tutor-section-modern p {
        font-size: 14px;
        color: #9ca3af;
        margin-bottom: 18px;
    }

    .btn-tutor-primary {
        border-radius: 999px;
        background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
        color: #fff;
        border: none;
        padding: 10px 22px;
        font-size: 14px;
        font-weight: 600;
        box-shadow: 0 14px 36px rgba(45, 173, 255, 0.45);
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-tutor-primary:hover {
        transform: translateY(-1px);
        color: #fff;
    }

    /* Modal styles tweaks */
     #quizSignupModal{
        padding: auto;
        margin: 10px;
     }
    #quizSignupModal .modal-content {
        border-radius: 18px;
        border: none;
        box-shadow: 0 22px 60px rgba(15, 23, 42, 0.4);
    }

    #quizSignupModal .modal-header {
        border-bottom: none;
        padding-bottom: 0;
    }

    #quizSignupModal .modal-title {
        font-weight: 700;
        font-size: 20px;
    }

    #quizSignupModal .modal-body {
        padding-top: 10px;
          margin: 10px;
    }

    #quizSignupModal .form-label {
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
    }

    #quizSignupModal .form-control {
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        padding: 9px 11px;
        font-size: 14px;
        transition: var(--rwu-transition);
    }

    #quizSignupModal .form-control:focus {
        border-color: var(--rwu-primary);
        box-shadow: 0 0 0 1px rgba(45, 173, 255, 0.25);
    }

    #quizSignupModal .btn-submit-tutor {
        border-radius: 999px;
        background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
        border: none;
        color: #fff;
        font-size: 14px;
        font-weight: 600;
        padding: 10px 18px;
        width: 100%;
        box-shadow: 0 16px 40px rgba(45, 173, 255, 0.45);
        margin-top: 4px;
    }

    /* Responsive tweaks */
    @media (max-width: 991.98px) {
        .quiz-header-modern {
            padding: 18px 18px;
        }

        .quiz-header-modern h1 {
            font-size: 22px;
        }

        .quiz-results-card .card-body {
            padding: 18px;
        }

        .tutor-section-modern {
            padding: 22px 18px;
        }

        .featured-course-card img {
            height: 180px;
        }
    }

    @media (max-width: 575.98px) {
        .quiz-results-page {
            padding-top: 24px;
            padding-bottom: 36px;
        }

        .quiz-header-meta span {
            width: 100%;
        }
    }
    .question-indicator-chip.active {
    box-shadow: 0 6px 18px rgba(15, 23, 42, 0.18);
    border-color: var(--rwu-primary);
    background: #e0f2fe;
}

.question-explanation-panel {
    border-radius: 16px;
    background: #ffffff;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
    border: 1px solid rgba(148, 163, 184, 0.35);
    padding: 16px 18px;
    font-size: 14px;
    color: #4b5563;
}
.question-explanation-panel h6 {
    font-size: 15px;
    font-weight: 600;
    margin-bottom: 6px;
}
.question-explanation-panel .q-text {
    margin-bottom: 6px;
}
.question-explanation-panel .exp-text {
    margin-bottom: 0;
}

</style>

<div class="page-listing__body quiz-results-page">
    <div class="container">
        <!-- MAIN RESULT CARD -->
        <div class="card quiz-results-card">
            <div class="quiz-header-modern">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div>
                        <h1>Quiz Results</h1>
                        <?php if (!empty($subtopicName)) { ?>
                            <p>Result for <strong><?php echo htmlspecialchars($subtopicName); ?></strong></p>
                        <?php } else { ?>
                            <p>Your quiz performance summary</p>
                        <?php } ?>

                        <div class="quiz-header-meta">
                            <?php if (!empty($attemptRow['created_at'])) {
                                $datetime      = new DateTime($attemptRow['created_at']);
                                $dateFormatted = $datetime->format('M d, Y');
                                $timeFormatted = $datetime->format('h:i A');
                            ?>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M8 7V3M16 7V3M7 11H17M7 21H17C18.1046 21 19 20.1046 19 19V7C19 5.89543 18.1046 5 17 5H7C5.89543 5 5 5.89543 5 7V19C5 20.1046 5.89543 21 7 21Z" stroke="#e5e7eb" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    Finished <?php echo $dateFormatted; ?> at <?php echo $timeFormatted; ?>
                                </span>
                            <?php } ?>

                            <?php if ($totalQuestions > 0) { ?>
                                <span>
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M12 8V12M12 16H12.01M21 12C21 16.9706 16.9706 21 12 21C7.02944 21 3 16.9706 3 12C3 7.02944 7.02944 3 12 3C16.9706 3 21 7.02944 21 12Z" stroke="#e5e7eb" stroke-width="1.7" stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                    <?php echo $totalQuestions; ?> Questions
                                </span>
                            <?php } ?>
                        </div>
                    </div>

                    <div class="d-flex flex-column align-items-end gap-2">
                        <?php if (!empty($resultText)) {
                            $isPass = ($resultText === 'pass');
                            $badgeClass = $isPass ? 'pass' : 'fail';
                        ?>
                            <span class="quiz-result-badge <?php echo $badgeClass; ?>">
                                <span class="dot" style="width:8px;height:8px;border-radius:999px;background:<?php echo $isPass ? '#4ade80' : '#f97373'; ?>;"></span>
                                <?php echo ucfirst($resultText); ?>
                            </span>
                        <?php } ?>

                        <div class="text-end" style="font-size:12px; color:#9ca3af;">
                            <div>Accuracy: <strong><?php echo $accuracy; ?>%</strong></div>
                            <div>Score: <strong><?php echo $correct; ?>/<?php echo $totalQuestions; ?></strong></div>
                        </div>
                    </div>
                </div>
            </div>

          <div class="card-body">
    <!-- QUESTIONS OVERVIEW + STATS -->
    <div class="row g-4 align-items-start">
        <!-- LEFT: chips + explanation -->
        <div class="col-lg-7">
            <h5 class="section-title-modern">Questions Overview</h5>

            <!-- chips -->
            <div class="question-indicators-wrap mb-3">
                <?php if (!empty($attemptquestions)) {
                    foreach ($attemptquestions as $index => $question) {
                        $questionNumber = $index + 1;
                        $correctFlag    = !empty($question['is_correct']) && (int)$question['is_correct'] === 1;
                        $chipClass      = $correctFlag ? 'correct' : 'incorrect';
                        $tooltipText    = "Question {$questionNumber}: " . ($correctFlag ? 'Correct' : 'Incorrect');

                        // these come from tbl_question_bank via controller join
                        $questionText = trim((string)($question['question_title'] ?? ''));
                        $explanation  = trim((string)($question['explanation'] ?? ''));
                ?>
                    <div class="question-indicator-chip <?php echo $chipClass; ?>"
                         title="<?php echo $tooltipText; ?>"
                         data-number="<?php echo $questionNumber; ?>"
                         data-question="<?php echo htmlspecialchars($questionText, ENT_QUOTES, 'UTF-8'); ?>"
                         data-explanation="<?php echo htmlspecialchars($explanation, ENT_QUOTES, 'UTF-8'); ?>">
                        <?php echo $questionNumber; ?>
                    </div>
                <?php
                    }
                } else { ?>
                    <p class="text-muted mb-0">No question breakdown available.</p>
                <?php } ?>
            </div>

            <!-- explanation panel under chips -->
            <h5 class="section-title-modern" style="margin-top: 10px;">Question Explanation</h5>
            <div id="question-explanation-panel" class="question-explanation-panel">
                <p class="text-muted mb-0">
                    Click on any question number above to see the explanation here.
                </p>
            </div>
        </div>

        <!-- RIGHT: stats -->
        <div class="col-lg-5">
            <div class="row g-3">
                <div class="col-6 col-md-4">
                    <div class="stat-card-modern">
                        <div class="stat-label">Correct</div>
                        <div class="stat-value" style="color:#16a34a;"><?php echo $correct; ?></div>
                        <div class="stat-meta"><?php echo $accuracy; ?>% of total</div>
                    </div>
                </div>
                <div class="col-6 col-md-4">
                    <div class="stat-card-modern">
                        <div class="stat-label">Incorrect</div>
                        <div class="stat-value" style="color:#dc2626;"><?php echo $incorrect; ?></div>
                        <div class="stat-meta"><?php echo max(0, 100 - $accuracy); ?>% of total</div>
                    </div>
                </div>
                <div class="col-12 col-md-4">
                    <div class="stat-card-modern">
                        <div class="score-circle-wrap">
                            <?php
                            $radius        = 58;
                            $circumference = 2 * M_PI * $radius;
                            $offset        = $circumference - ($circumference * $accuracy / 100);
                            ?>
                            <div class="score-circle">
                                <svg width="132" height="132">
                                    <circle class="score-circle-bg" cx="66" cy="66" r="<?php echo $radius; ?>" />
                                    <circle class="score-circle-progress"
                                            cx="66" cy="66" r="<?php echo $radius; ?>"
                                            stroke-dasharray="<?php echo $circumference; ?>"
                                            stroke-dashoffset="<?php echo $offset; ?>" />
                                </svg>
                                <div class="score-circle-text">
                                    <span><?php echo $accuracy; ?>%</span>
                                    <small>Accuracy</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>

        </div>

        <!-- VIDEO + NEXT STEPS -->
        <div class="row g-4">
            <div class="col-lg-8">
                <?php
                $videoUrl = '';
                if (!empty($subtopic_id)) {
                    $db    = FatApp::getDb();
                    $query = "SELECT video_url FROM course_subtopics WHERE subtopic = " . (int)$subtopic_id . " LIMIT 1";
                    $res   = $db->query($query);
                    if ($res) {
                        $row      = $db->fetch($res);
                        $videoUrl = $row['video_url'] ?? '';
                    }
                }
                ?>
                <div class="card video-card-modern">
                    <div class="card-header">
                        <h5 class="mb-0">Recommended Video</h5>
                    </div>
                    <div class="card-body">
                        <div class="video-frame-wrap">
                            <?php if (!empty($videoUrl)) {
                                $videoId = '';
                                if (preg_match('%(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})%', $videoUrl, $matches)) {
                                    $videoId = $matches[1];
                                }
                                $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                            ?>
                                <iframe src="<?php echo htmlspecialchars($embedUrl); ?>"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowfullscreen></iframe>
                            <?php } else { ?>
                                <iframe src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw"
                                        title="Default Recommended Video"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                        allowfullscreen></iframe>
                            <?php } ?>
                        </div>

                        <p class="video-topic-title">
                            For <strong><?php echo htmlspecialchars($_SESSION['subtopicName'] ?? 'Topic Preparation'); ?></strong>
                        </p>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="next-steps-card card">
                    <div class="card-header">
                        <h5 class="mb-0">Next Steps</h5>
                    </div>
                    <div class="card-body">
                        <p class="desc">
                            Thank you for completing the quiz! Review your answers and keep practising to strengthen your understanding of this topic.
                        </p>

                     <div class="d-grid gap-2 mb-3">
    <?php if (!empty($resultText) && !empty($currentSubtopicId)) { ?>

        <?php if ($resultText === 'pass') { ?>
            <a href="<?php echo MyUtility::makeUrl('quizizz', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$currentSubtopicId; ?>"
               class="btn-pill btn-pill-primary text-center">
                Next Suggested Quiz
            </a>

        <?php } else { ?>
            <a href="<?php echo MyUtility::makeUrl('quizattemptall', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$currentSubtopicId; ?>"
               class="btn-pill btn-pill-danger text-center">
                Retake Quiz
            </a>
        <?php } ?>

    <?php } ?>
</div>


                           <button type="button"
        class="btn-pill btn-pill-outline text-center"
        data-bs-toggle="modal" data-bs-target="#quizSignupModal">
    Find a Tutor for this Topic
</button>

                        </div>

                        <small class="text-muted" style="font-size:12px;">
                            Need more help? Connect with a verified tutor and get personalised support.
                        </small>
                    </div>
                </div>
            </div>
        </div>

        <!-- RECOMMENDED COURSES -->
        <div class="recommended-section">
            <div class="row mb-3">
                <div class="col-12">
                    <h3 class="section-title-modern">Recommended Courses</h3>
                </div>
            </div>

            <div class="row g-4">
                <?php
                $filteredCourses = [];
                if (!empty($coursesslider) && is_array($coursesslider)) {
                    $filteredCourses = array_filter($coursesslider, function ($course) {
                        return isset($course['course_details']) && strlen(trim($course['course_details'])) >= 50;
                    });
                }

                if (!empty($filteredCourses)) {
                    $filteredCourses = array_values($filteredCourses);
                    $randomKey       = array_rand($filteredCourses);
                    $randomCourse    = $filteredCourses[$randomKey];
                ?>
                    <!-- FEATURED COURSE -->
                    <div class="col-lg-6">
                        <div class="featured-course-card card">
                            <div class="row g-0">
                                <div class="col-md-5">
                                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $randomCourse['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>"
                                         alt="<?php echo htmlspecialchars($randomCourse['course_title']); ?>">
                                </div>
                                <div class="col-md-7">
                                    <div class="featured-course-body">
                                        <div class="featured-course-title">
                                            <?php
                                            echo (strlen($randomCourse['course_title']) > 70)
                                                ? CommonHelper::renderHtml(substr($randomCourse['course_title'], 0, 70)) . '...'
                                                : CommonHelper::renderHtml($randomCourse['course_title']);
                                            ?>
                                        </div>

                                        <div class="course-rating-stars">
                                            <?php
                                            $rating      = (float)$randomCourse['course_ratings'];
                                            $fullStars   = floor($rating);
                                            $hasHalfStar = ($rating - $fullStars) >= 0.5;

                                            for ($i = 1; $i <= 5; $i++) {
                                                if ($i <= $fullStars) {
                                                    echo '<i class="fa fa-star"></i>';
                                                } elseif ($i === $fullStars + 1 && $hasHalfStar) {
                                                    echo '<i class="fa fa-star-half-o"></i>';
                                                } else {
                                                    echo '<i class="fa fa-star-o"></i>';
                                                }
                                            }
                                            ?>
                                            <span class="course-rating-meta">
                                                (<?php echo $randomCourse['course_reviews']; ?> reviews)
                                            </span>
                                        </div>

                                        <div style="flex:1 1 auto; margin-bottom:8px; max-height:90px; overflow:hidden; border-radius:8px; background:#f9fafb;">
                                            <iframe srcdoc="<?php echo $randomCourse['course_details']; ?>"
                                                    style="border:none;width:100%;height:100%;"></iframe>
                                        </div>

                                        <div class="d-flex justify-content-between align-items-center">
                                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$randomCourse['course_slug']]); ?>"
                                               class="btn-pill btn-pill-primary">
                                                Start Learning
                                            </a>
                                            <span class="course-price-pill">
                                                <?php echo CourseUtility::formatMoney($randomCourse['course_price']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- 3 MINI COURSES -->
                    <div class="col-lg-6">
                        <div class="row g-3">
                            <?php
                            $count = 0;
                            foreach ($coursesslider as $crs) {
                                if ($count >= 3) {
                                    break;
                                }
                            ?>
                                <div class="col-12">
                                    <div class="course-mini-card">
                                        <div class="row g-0">
                                            <div class="col-4">
                                                <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>"
                                                     alt="<?php echo htmlspecialchars($crs['course_title']); ?>">
                                            </div>
                                            <div class="col-8">
                                                <div class="course-mini-body">
                                                    <h6>
                                                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>"
                                                           style="color:#111827;text-decoration:none;">
                                                            <?php
                                                            echo (strlen($crs['course_title']) > 50)
                                                                ? CommonHelper::renderHtml(substr($crs['course_title'], 0, 50)) . '...'
                                                                : CommonHelper::renderHtml($crs['course_title']);
                                                            ?>
                                                        </a>
                                                    </h6>
                                                    <p><?php echo CommonHelper::renderHtml($crs['subcate_name']); ?></p>
                                                    <div class="course-mini-footer">
                                                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                                            View course
                                                        </a>
                                                        <span class="text-muted">
                                                            <?php echo CourseUtility::formatMoney($crs['course_price']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php
                                $count++;
                            }
                            ?>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="col-12">
                        <p class="text-muted mb-0">No recommended courses to show right now.</p>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- TUTOR SECTION -->
        <div class="row">
            <div class="col-12">
                <div class="tutor-section-modern">
                    <div class="row align-items-center g-4">
                        <div class="col-lg-5">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user.png" alt="Tutor">
                        </div>
                        <div class="col-lg-7">
                            <div class="tutor-tag">
                                <span style="width:6px;height:6px;border-radius:999px;background:#22c55e;"></span>
                                1-to-1 Learning Support
                            </div>
                            <h3>Find Your Perfect Tutor</h3>
                            <p>
    Get matched with an experienced tutor who understands your exam board, level,
    and learning style. Schedule flexible sessions and focus on the topics you find most challenging.  
    Our tutors provide personalized feedback after every session to help you identify your strengths and areas for improvement.  
    Learn at your own pace, build confidence, and achieve the grades you deserve with expert guidance every step of the way.
</p>

                           <button type="button"
        class="btn-tutor-primary"
        data-bs-toggle="modal" data-bs-target="#quizSignupModal">
    <span>Find a Tutor</span>
</button>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FIND A TUTOR MODAL -->
    <div class="modal fade" id="quizSignupModal" tabindex="-1" aria-labelledby="quizSignupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="quizSignupModalLabel">Find a Tutor</h5>
                   <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                       
                    </button>
                </div>
                <div class="modal-body">
                    <p class="mb-3" style="font-size: 14px;">
                        Tell us what you're looking for and we'll connect you with a qualified tutor tailored to your learning needs.
                    </p>
               <form id="quizSignupForm" class="form-knowledge" novalidate>
    <!-- Hidden fields required by TutorRequestController::create -->
    <input type="hidden" name="tutreq_phone_code" value="44"> <!-- UK default, change if needed -->
    <input type="hidden" name="source" value="quiz">

    <!-- NEW: hidden IDs from controller -->
    <input type="hidden" name="tutreq_level_id"
           value="<?= (int)($tutreqLevelId ?? 0); ?>">
    <input type="hidden" name="tutreq_subject_id"
           value="<?= (int)($tutreqSubjectId ?? 0); ?>">
    <input type="hidden" name="tutreq_examboard_id"
           value="<?= (int)($tutreqExamboardId ?? 0); ?>">
    <input type="hidden" name="tutreq_tier_id"
           value="<?= (int)($tutreqTierId ?? 0); ?>">

    <div class="form-group mb-2">
        <label for="fullName" class="form-label">Full Name</label>
        <!-- mapped to tutreq_first_name -->
        <input type="text" name="tutreq_first_name" class="form-control" id="fullName"
               placeholder="Enter Name" required>
    </div>

    <div class="form-group mb-2">
        <label for="email" class="form-label">Email Address</label>
        <input type="email" name="tutreq_email" class="form-control" id="email"
               placeholder="Enter E-mail" required>
    </div>

    <div class="form-group mb-2">
        <label for="parentEmail" class="form-label">Parent's Email</label>
        <!-- extra, we push into preferred_time string -->
        <input type="email" name="parent_email" class="form-control" id="parentEmail"
               placeholder="Enter Parent's E-mail">
    </div>

    <div class="form-group mb-2">
        <label for="phone" class="form-label">Phone Number</label>
        <input type="text" name="tutreq_phone_number" class="form-control" id="phone"
               placeholder="Enter Phone Number" required>
    </div>

    <div class="form-group mb-2">
        <label for="subject" class="form-label">Subject / Topic</label>
        <!-- free-text; we’ll stuff into preferred_time for extra context -->
        <input type="text" name="subject_text" class="form-control" id="subject"
               placeholder="e.g. GCSE Maths – Algebra">
    </div>

    <div class="form-group mb-3">
        <label for="preferredTime" class="form-label">Preferred Time</label>
        <!-- this is the one the controller reads -->
        <input type="text" name="tutreq_preferred_time" class="form-control" id="preferredTime"
               placeholder="e.g. Weekdays 5–7 PM">
    </div>

    <button type="button" class="btn-submit-tutor" id="submitTutorForm">
        Submit Request
    </button>
</form>

            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    // Enable Bootstrap tooltips if available
$('#submitTutorForm').on('click', function () {
    const form = $('#quizSignupForm');

    // Build a nicer "preferred time" string with extra context
    const preferredTime = $('#preferredTime').val().trim();
    const subject       = $('#subject').val().trim();
    const parentEmail   = $('#parentEmail').val().trim();

    let combinedPref = preferredTime;
    if (subject) {
        combinedPref += (combinedPref ? ' | ' : '') + 'Subject: ' + subject;
    }
    if (parentEmail) {
        combinedPref += (combinedPref ? ' | ' : '') + 'Parent Email: ' + parentEmail;
    }
    $('input[name="tutreq_preferred_time"]').val(combinedPref);

    const formData = form.serialize();

    // Call TutorRequest/create
    fcom.ajax(fcom.makeUrl('TutorRequest', 'create'), formData, function (response) {
        try {
            if (typeof response === 'string') {
                response = JSON.parse(response);
            }

            if (response.status == 1) {
                form[0].reset();
                $('#quizSignupModal').modal('hide');

                Swal.fire({
                    icon: 'success',
                    title: 'Request submitted!',
                    text: response.msg || 'Your request has been sent successfully. Our team will contact you soon with tutor options.',
                    confirmButtonColor: '<?= $primaryColor ?>'
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission failed',
                    text: response.msg || 'Something went wrong. Please try again.',
                    confirmButtonColor: '#ef4444'
                });
            }
        } catch (e) {
            console.error('Invalid JSON:', e, response);
            Swal.fire({
                icon: 'error',
                title: 'Unexpected error',
                text: 'We could not process the server response. Please try again later.',
                confirmButtonColor: '#ef4444'
            });
        }
    });
});


</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chips  = document.querySelectorAll('.question-indicator-chip');
    const panel  = document.getElementById('question-explanation-panel');
    if (!chips.length || !panel) return;

    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            // remove active from all, then set on clicked
            chips.forEach(c => c.classList.remove('active'));
            this.classList.add('active');

            const num   = this.getAttribute('data-number') || '';
            const qText = this.getAttribute('data-question') || '';
            const exp   = this.getAttribute('data-explanation') || '';

            if (!qText && !exp) {
                panel.innerHTML = '<p class="text-muted mb-0">No explanation provided for this question yet.</p>';
                return;
            }

            panel.innerHTML =
                '<h6>Question ' + num + '</h6>' +
                (qText ? '<p class="q-text"><strong>Question:</strong> ' + qText + '</p>' : '') +
                (exp   ? '<p class="exp-text"><strong>Explanation:</strong> ' + exp + '</p>' : '');
        });
    });
});
</script>
