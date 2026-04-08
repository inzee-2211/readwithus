<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();
$primaryColor = '#2DADFF';

$attemptRow      = $attemptresult[0] ?? [];
$resultText      = '';
$subtopic_id     = null;
$totalQuestions  = 0;
$correct         = 0;
$incorrect       = 0;
$accuracy        = 0;

// Default values jo card mein nazar aayengi
$topName = 'ARITHMETIC'; 
$subName = 'BODMAS Rule';
$desc    = 'Master the order of operations.';

if (!empty($attemptRow)) {
    $resultText  = strtolower(trim($attemptRow['result'] ?? ''));
    $subtopic_id = $attemptRow['subtopic_id'] ?? null;
    $totalQuestions = (int)($attemptRow['total_questions'] ?? 0);

    if (!empty($subtopic_id)) {
        $db = FatApp::getDb();
        // Database se data fetch karna
        $sql = "SELECT * FROM course_subtopics WHERE id = " . (int)$subtopic_id;
        $res = $db->query($sql);
        $row = $db->fetch($res);

        if ($row) {
            $subName = $row['st_title'] ?? $row['subtopic_title'] ?? $row['title'] ?? $subName;
            $desc    = $row['st_description'] ?? $row['description'] ?? $desc;
            
            $t_id = $row['st_topic_id'] ?? $row['topic_id'] ?? null;
            if ($t_id) {
                $tSql = "SELECT * FROM course_topics WHERE id = " . (int)$t_id;
                $tRes = $db->query($tSql);
                $tRow = $db->fetch($tRes);
                if ($tRow) {
                    $topName = $tRow['topic_title'] ?? $tRow['topic_name'] ?? $tRow['title'] ?? $topName;
                }
            }
        }
    }
}

// Aapka original logic (Inhein mat cherna)
if (!empty($attemptquestions) && is_array($attemptquestions)) {
    $totalQuestions = count($attemptquestions);
    foreach ($attemptquestions as $q) {
        if (!empty($q['is_correct']) && (int)$q['is_correct'] === 1) { $correct++; }
    }
}
if ($totalQuestions > 0) {
    $incorrect = $totalQuestions - $correct;
    $accuracy  = round(($correct / $totalQuestions) * 100);
}
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
.video-frame-wrap {
  position: relative;
  width: 100%;

  padding-top: 56.25%; /* 16:9 */
  border-radius: 12px;
  overflow: hidden;
  background: #000;
  box-shadow: 0 10px 26px rgba(15, 23, 42, 0.4);
}
.video-frame-wrap iframe {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 130%!important;
  border: 0;
}
/* === Recommended Courses (Hero slider + side list) === */
.recommended-section {
    margin-top: 40px;
    margin-bottom: 48px;
}

/* Top heading + intro */
.recommended-intro {
    max-width: 780px;
    margin-bottom: 18px;
}

.recommended-intro h3 {
    font-size: 20px;
    font-weight: 700;
    margin-bottom: 8px;
    color: #0f172a;
}

.recommended-intro p {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 14px;
}

/* === Benefit CARDS (3 cards like screenshot 2) === */
.recommended-benefits {
    margin: 0;
    padding: 0;
    list-style: none;
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
}

.recommended-benefits li {
    background: #ffffff;
    border-radius: 18px;
    padding: 16px 18px;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.08);
    border: 1px solid rgba(148, 163, 184, 0.35);
    font-size: 13px;
    color: #4b5563;
    display: flex;
    align-items: flex-start;
    gap: 10px;
}
.recommended-benefits li:hover {
    transform: translateY(-1px);
    box-shadow: 0 18px 10px rgba(45, 173, 255, 0.25),
                0 8px 10px rgba(15, 23, 42, 0.08);
    border-color: var(--rwu-primary);
    
}

.recommended-benefits li::before {
    content: "";
    flex-shrink: 0;
    width: 8px;
    height: 8px;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
    margin-top: 5px;
}

/* === Slider shell: hero card + arrows === */
.course-slider-shell {
    margin-top: 16px;
    border-radius: 22px;
    padding: 0;
    display: grid;
    grid-template-columns: auto minmax(0, 1fr) auto;
    align-items: stretch;
    gap: 12px;
}

/* Arrows */
.course-slider-arrow {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    border: 1px solid rgba(148, 163, 184, 0.7);
    background: #ffffff;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: var(--rwu-transition);
    align-self: center;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.08);
}

.course-slider-arrow svg {
    width: 18px;
    height: 18px;
}

.course-slider-arrow:hover {
    background: #e5f2ff;
    border-color: var(--rwu-primary);
    box-shadow: 0 14px 32px rgba(37, 99, 235, 0.20);
}

/* Window that shows 1 slide */
.course-slider-window {
    position: relative;
    overflow: hidden;
    min-height: 260px;
}

/* Individual slide */
.course-slide {
    display: none;
    height: 100%;
}

.course-slide.active {
    display: block;
    animation: fadeSlide 0.4s ease-out;
}

/* Hero card inside slide */
.course-slide-card {
    background: #ffffff;
    /* border-radius: 22px; */
    padding: 20px 22px;
    box-shadow: 0 18px 46px rgba(15, 23, 42, 0.10);
    border: 1px solid rgba(148, 163, 184, 0.35);
    height: 100%;
    display: flex;
    flex-direction: column;
}
.course-slide-card:hover {
    transform: translateY(-6px) scale(1.015);
    box-shadow: 0 18px 45px rgba(45, 173, 255, 0.25),
                0 8px 20px rgba(15, 23, 42, 0.08);
    border-color: var(--rwu-primary);
}


/* Little pill “Matched to your quiz” */
.course-slide-pill {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 12px;
    border-radius: 999px;
    font-size: 11px;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
    background: #eff6ff;
    margin-bottom: 10px;
}

.course-slide-pill .dot {
    width: 6px;
    height: 6px;
    border-radius: 999px;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
}

/* Main layout: text + image */
.course-slide-inner {
    display: grid;
    grid-template-columns: minmax(0, 1.4fr) minmax(0, 1fr);
    gap: 22px;
    align-items: center;
}

/* Left / text side */
.course-slide-body {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.course-slide-title {
    font-size: 18px;
    font-weight: 700;
    color: #111827;
}

.course-slide-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    font-size: 12px;
    color: #6b7280;
}

.course-slide-meta span {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 4px 9px;
    border-radius: 999px;
    background: #f3f4f6;
}

.course-slide-rating {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    color: #6b7280;
}

.course-slide-rating .stars {
    color: #fbbf24;
    font-size: 13px;
}

.course-slide-desc {
    font-size: 13px;
    color: #4b5563;
    line-height: 1.5;
}

/* Footer with CTA + price */
.course-slide-footer {
    margin-top: 10px;
    display: flex;
    align-items: flex-end;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}

.btn-course-primary {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border-radius: 999px;
    padding: 9px 18px;
    font-size: 14px;
    font-weight: 600;
    border: none;
    text-decoration: none;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
    color: #ffffff;
    box-shadow: 0 14px 36px rgba(45, 173, 255, 0.45);
}

.btn-course-primary:hover {
    color: #ffffff;
    transform: translateY(-1px);
    box-shadow: 0 18px 46px rgba(37, 99, 235, 0.55);
}

.course-slide-price-wrap {
    text-align: right;
}

.course-slide-price {
    font-size: 18px;
    font-weight: 700;
    color: var(--rwu-primary);
}

.course-slide-price-note {
    font-size: 11px;
    color: #6b7280;
}

/* Right / image side */
.course-slide-media {
    display: flex;
    align-items: center;
    justify-content: center;
}

.course-slide-media-inner {
    width: 100%;
    max-width: 320px;
    border-radius: 16px;
    overflow: hidden;
    background: #e5e7eb;
    box-shadow: 0 16px 40px rgba(15, 23, 42, 0.18);
}

.course-slide-media-inner img {
    width: 100%;
    height: 210px;
    object-fit: cover;
}

/* Dots under slider */
.course-slider-dots {
    display: flex;
    justify-content: flex-start;
    align-items: center;
    gap: 6px;
    margin-top: 12px;
}

.course-slider-dot {
    width: 8px;
    height: 8px;
    border-radius: 999px;
    border: none;
    background: #d1d5db;
    padding: 0;
    cursor: pointer;
    transition: var(--rwu-transition);
}

.course-slider-dot.active {
    width: 18px;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
}

/* === Side vertical mini list === */
.recommended-side-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.recommended-side-title {
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: #6b7280;
    margin-bottom: 2px;
}

/* Mini cards (reuse but tighten) */
.course-mini-card {
    border-radius: 12px;
    box-shadow: 0 10px 26px rgba(15, 23, 42, 0.05);
    border: 1px solid rgba(148, 163, 184, 0.3);
    background: #fff;
    overflow: hidden;
}
.course-mini-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 16px 32px rgba(45, 173, 255, 0.20);
    border-color: var(--rwu-primary);
}
.course-mini-card-inner {
    display: grid;
    grid-template-columns: 80px minmax(0, 1fr);
    gap: 8px;
}

.course-mini-thumb {
    position: relative;
    width: 100%;
    height: 100%;
    min-height: 70px;
    overflow: hidden;
}

.course-mini-thumb img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.course-mini-body {
    padding: 8px 10px 8px 4px;
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.course-mini-title {
    font-size: 13px;
    font-weight: 600;
    color: #111827;
    margin: 0;
}

.course-mini-meta {
    font-size: 11px;
    color: #6b7280;
}

.course-mini-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 11px;
    margin-top: 4px;
}

.course-mini-footer a {
    font-weight: 600;
    color: var(--rwu-primary);
    text-decoration: none;
}

.course-mini-footer a:hover {
    text-decoration: underline;
}

/* Slide animation */
@keyframes fadeSlide {
    from { opacity: 0; transform: translateY(4px); }
    to   { opacity: 1; transform: translateY(0); }
}

/* === Responsive === */
@media (max-width: 991.98px) {
    .recommended-benefits {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .course-slide-inner {
        grid-template-columns: minmax(0, 1fr);
    }

    .course-slide-media-inner img {
        height: 200px;
    }
}

@media (max-width: 767.98px) {
    .recommended-benefits {
        grid-template-columns: 1fr;
    }

    .course-slider-shell {
        grid-template-columns: minmax(0, 1fr);
        gap: 8px;
    }

    .course-slider-arrow {
        display: none;
    }

    .course-slide-card {
        padding: 16px 14px;
        border-radius: 18px;
    }
}
.view-all-courses-wrap {
    margin-top: 14px;
    text-align: center;
}

.view-all-courses-btn {
    display: inline-block;
    padding: 10px 22px;
    border-radius: 999px;
    font-size: 14px;
    font-weight: 600;
    color: #fff;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-secondary));
    box-shadow: 0 12px 28px rgba(45, 173, 255, 0.35);
    text-decoration: none;
    transition: all 0.35s ease;
}

.view-all-courses-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 18px 50px rgba(45, 173, 255, 0.45);
    color: #fff;
}
.course-slide-card,
.course-mini-card {
    opacity: 0;
    animation: fadeInUp 0.6s ease forwards;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(10px); }
    to   { opacity: 1; transform: translateY(0); }
}


.subscribe-fav-btn-wrap {
    margin: 18px 0 8px;
    text-align: left;      /* keep aligned with the chips */
}

.subscribe-fav-btn {
    display: inline-block;
    padding: 10px 22px;
    border-radius: 999px;
    border: 1.5px solid #111;   /* thin black border */
    background: #fff;
    color: #111;
    font-size: 14px;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
}

.subscribe-fav-btn:hover {
    background: #111;
    color: #fff;
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}
.rwu-glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(20px);
        border-radius: 35px;
        position: relative;
        overflow: hidden;
        border: 1px solid rgba(255, 255, 255, 0.5);
        box-shadow: 0 20px 50px rgba(45, 173, 255, 0.1);
        transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        animation: rwuAppear 0.8s ease-out;
    }

    /* 2. Background Animated Blobs (Moving Colors) */
    .rwu-blob {
        position: absolute;
        width: 150px;
        height: 150px;
        background: linear-gradient(135deg, #2DADFF 0%, #B4E4FF 100%);
        filter: blur(50px);
        border-radius: 50%;
        z-index: 0;
        opacity: 0.2;
        animation: rwuFloat 8s infinite alternate;
    }

    .rwu-blob-1 { top: -50px; right: -50px; }
    .rwu-blob-2 { bottom: -50px; left: -50px; animation-delay: -4s; }

    /* 3. Typography & Styling */
    .rwu-pill-topic {
        background: #2DADFF;
        color: #fff;
        padding: 5px 15px;
        border-radius: 100px;
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 2px;
        display: inline-block;
        margin-bottom: 1.2rem;
        box-shadow: 0 4px 15px rgba(45, 173, 255, 0.3);
    }

    .rwu-title-text {
        color: #0F172A;
        font-weight: 850;
        font-size: 1.6rem;
        letter-spacing: -0.5px;
        line-height: 1.1;
        margin-bottom: 1rem;
    }

    .rwu-desc-text {
        color: #64748B;
        font-size: 0.95rem;
        max-width: 280px;
        margin: 0 auto;
        line-height: 1.7;
        font-weight: 500;
    }

    /* Animations */
    @keyframes rwuAppear {
        from { opacity: 0; transform: translateY(30px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }

    @keyframes rwuFloat {
        0% { transform: translate(0, 0) rotate(0deg); }
        100% { transform: translate(30px, 30px) rotate(15deg); }
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
<div class="card rwu-glass-card mb-4 text-center p-5">
    <div class="rwu-blob rwu-blob-1"></div>
    <div class="rwu-blob rwu-blob-2"></div>

    <div class="card-body p-0 position-relative" style="z-index: 2;">
        <div class="rwu-pill-topic text-uppercase">
            <i class="fas fa-sparkles me-1"></i>
            <?php echo htmlspecialchars($topName); ?>
        </div>

        <h2 class="rwu-title-text">
            <?php echo htmlspecialchars($subName); ?>
        </h2>

        <div class="mx-auto my-3" style="width: 40px; height: 5px; background: linear-gradient(90deg, #2DADFF, #B4E4FF); border-radius: 50px;"></div>

        <p class="rwu-desc-text">
            <?php echo htmlspecialchars($desc); ?>
        </p>
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
            <!-- VIDEO + NEXT STEPS -->
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card video-card-modern">
                <div class="card-header">
                    <h5 class="mb-0">Recommended Video</h5>
                </div>
                <div class="card-body">
                    <div class="video-frame-wrap">
                        <?php if (!empty($recommendedVideoUrl)) {
                            $videoId = '';
                            if (preg_match('%(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})%', $recommendedVideoUrl, $matches)) {
                                $videoId = $matches[1];
                            }
                            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                        ?>
                            <iframe src="<?php echo htmlspecialchars($embedUrl); ?>"
                                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                    allowfullscreen></iframe>
                        <?php } else { ?>
                            <div style="display: flex; align-items: center; justify-content: center; height: 100%; background: #f8f9fa; color: #6c757d;">
                                <div class="text-center">
                                    <i class="fa fa-video-camera fa-3x mb-3"></i>
                                    <p>No video available for this topic</p>
                                </div>
                            </div>
                        <?php } ?>
                    </div>

                    <?php if (!empty($subtopicName)) { ?>
                        <p class="video-topic-title">
                            For <strong><?php echo htmlspecialchars($subtopicName); ?></strong>
                        </p>
                    <?php } ?>
                </div>
            </div>
        </div>
        <!-- ... rest of next steps code remains the same ... -->

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

    <!-- Existing logic (KEEP AS-IS) -->
    <?php if ($resultText === 'pass') { ?>
        <?php
        $targetSubtopic = !empty($nextSubtopicId) ? $nextSubtopicId : $currentSubtopicId;
        ?>
        <a href="<?php echo MyUtility::makeUrl('quizattemptall', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$targetSubtopic; ?>"
           class="btn-pill btn-pill-primary text-center">
            Next Quiz
        </a>
    <?php } else { ?>
        <a href="<?php echo MyUtility::makeUrl('quizattemptall', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$currentSubtopicId; ?>"
           class="btn-pill btn-pill-danger text-center">
            Retake Quiz
        </a>
    <?php } ?>

    <!-- NEW: Opposite button (below the existing one) -->
    <?php if ($resultText === 'pass') { ?>
        <!-- Passed => show opposite: Retake -->
        <a href="<?php echo MyUtility::makeUrl('quizattemptall', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$currentSubtopicId; ?>"
           class="btn-pill btn-pill-danger text-center">
            Retake Quiz
        </a>
    <?php } else { ?>
        <!-- Failed => show opposite: Next Suggested -->
        <?php
        $targetSubtopicOpp = !empty($nextSubtopicId) ? $nextSubtopicId : $currentSubtopicId;
        ?>
        <a href="<?php echo MyUtility::makeUrl('quizattemptall', '', [], CONF_WEBROOT_FRONTEND) . '?subtopic=' . (int)$targetSubtopicOpp; ?>"
           class="btn-pill btn-pill-primary text-center">
            Next Quiz
        </a>
    <?php } ?>

<?php } ?>
</div>



  <a href="<?php echo MyUtility::makeUrl('Teachers'); ?>"
   class="btn-pill btn-pill-outline text-center">
   Find a Tutor for this Topic
</a>

                        </div>

                        <small class="text-muted" style="font-size:12px;">
                            Need more help? Connect with a verified tutor and get personalised support.
                        </small>
                    </div>
                </div>
            </div>
            <div class="recommended-section">
                <div class="row mb-2">
                    <div class="col-12">
                        <div class="recommended-intro">
                            <h3 class="section-title-modern" style="margin-bottom: 10px;">
                                Recommended Courses
                            </h3>
                            <p>
                                Based on this quiz, we’ve picked courses that match your subject and level so you can
                                strengthen the exact topics you’ve just practised.
                            </p>
                            <ul class="recommended-benefits">
                                <li>Structured video lessons that move from basics to exam-style questions.</li>
                                <li>Topic-based practice matched to your quiz performance and exam board.</li>
                                <li>Study at your own pace with on-demand lessons and repeatable quizzes.</li>
                            </ul>
                            
                        </div>
                        
                    </div>
                </div>
            
                <?php
                // Resolve pricing URL (fallback if not passed from controller)
                $pricingUrl = isset($pricingUrl)
                    ? $pricingUrl
                    : MyUtility::makeUrl('pricing', 'index');
            
                // Filter courses: prefer those with decent details, but accept title+slug as fallback
                $sliderCourses = [];
                if (!empty($coursesslider) && is_array($coursesslider)) {
                    $sliderCourses = array_filter($coursesslider, function ($course) {
                        if (!empty($course['course_details']) && strlen(trim($course['course_details'])) >= 30) {
                            return true;
                        }
                        return !empty($course['course_title']) && !empty($course['course_slug']);
                    });
                }
            
                if (!empty($sliderCourses)) {
                    $sliderCourses = array_values($sliderCourses);
                    $totalSlides   = count($sliderCourses);
            
                    // Side list: just take first 4 for display on the right
                    $sideListCourses = array_slice($sliderCourses, 0, 4);
                ?>
                    <div class="row g-4 align-items-start">
                        <!-- LEFT: HERO SLIDER -->
                        <div class="col-lg-8">
                            <div class="course-slider-shell" data-slider="recommended-courses">
                                <!-- Prev arrow -->
                                <button type="button" class="course-slider-arrow prev" aria-label="Previous course">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M15 5L9 12L15 19"
                                              stroke="#4b5563" stroke-width="1.8"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
            
                                <!-- Slides window -->
                                <div class="course-slider-window">
                                    <?php foreach ($sliderCourses as $index => $course) {
                                        $isActive   = ($index === 0);
                                        $title      = $course['course_title'] ?? '';
                                        $slug       = $course['course_slug'] ?? '';
                                        // $price      = $course['course_price'] ?? 0;
                                        $rating     = (float)($course['course_ratings'] ?? 0);
                                        $reviews    = (int)($course['course_reviews'] ?? 0);
                                        $subcat     = $course['subcate_name'] ?? '';
                                       $detailsRaw = (string)($course['course_details'] ?? '');

// 1) convert &lt;p&gt; back into <p>
$detailsDecoded = html_entity_decode($detailsRaw, ENT_QUOTES | ENT_HTML5, 'UTF-8');

// 2) strip any real HTML tags
$detailsStr = trim(strip_tags($detailsDecoded));

// 3) normalize whitespace (optional but makes it look nice)
$detailsStr = preg_replace('/\s+/', ' ', $detailsStr);

if (mb_strlen($detailsStr) > 180) {
    $detailsStr = mb_substr($detailsStr, 0, 180) . '…';
}

            
                                        $fullStars   = floor($rating);
                                        $hasHalfStar = ($rating - $fullStars) >= 0.5;
                                    ?>
                                        <div class="course-slide <?php echo $isActive ? 'active' : ''; ?>"
                                             data-index="<?php echo $index; ?>">
                                            <div class="course-slide-card">
                                                <div class="course-slide-pill">
                                                    <span class="dot"></span>
                                                    Matched to your quiz<?php
                                                    if (!empty($subcat)) {
                                                        echo ' · ' . CommonHelper::renderHtml($subcat);
                                                    } ?>
                                                </div>
            
                                                <div class="course-slide-inner">
                                                    <!-- Text side -->
                                                    <div class="course-slide-body">
                                                        <div class="course-slide-title">
                                                            <?php
                                                            echo (strlen($title) > 80)
                                                                ? CommonHelper::renderHtml(substr($title, 0, 80)) . '…'
                                                                : CommonHelper::renderHtml($title);
                                                            ?>
                                                        </div>
            
                                                        <div class="course-slide-meta">
                                                            <?php if (!empty($course['course_level'])) { ?>
                                                                <span>
                                                                    <i class="fa fa-signal"></i>
                                                                    Level <?php echo (int)$course['course_level']; ?>
                                                                </span>
                                                            <?php } ?>
                                                            <?php if (!empty($course['course_duration'])) { ?>
                                                                <span>
                                                                    <i class="fa fa-clock-o"></i>
                                                                    <?php echo htmlspecialchars($course['course_duration']); ?>
                                                                </span>
                                                            <?php } ?>
                                                        </div>
            
                                                        <div class="course-slide-rating">
                                                            <span class="stars">
                                                                <?php
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
                                                            </span>
                                                            <span class="rating-text">
                                                                <?php echo number_format($rating, 1); ?>
                                                                <?php if ($reviews > 0) { ?>
                                                                    · <?php echo $reviews; ?> reviews
                                                                <?php } else { ?>
                                                                    · New course
                                                                <?php } ?>
                                                            </span>
                                                        </div>
            
                                                        <?php if (!empty($detailsStr)) { ?>
                                                            <div class="course-slide-desc">
                                                                <?php echo htmlspecialchars($detailsStr, ENT_QUOTES, 'UTF-8'); ?>
                                                            </div>
                                                        <?php } ?>
            
                                                        <div class="course-slide-footer">
                                                            <!-- CTA to pricing page -->
                                                            <a href="<?php echo $pricingUrl; ?>"
                                                               class="btn-course-primary">
                                                                <i class="fa fa-bolt"></i>
                                                                <span>Start learning</span>
                                                            </a>
            
                                                            <div class="course-slide-price-wrap">
                                                              
                                                                <div class="course-slide-price-note">
                                                                    Access this and similar courses with your plan.
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
            
                                                    <!-- Image side -->
                                                    <div class="course-slide-media">
                                                        <div class="course-slide-media-inner">
                                                            <img src="<?php echo MyUtility::makeUrl(
                                                                    'Image',
                                                                    'show',
                                                                    [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'MEDIUM', $siteLangId],
                                                                    CONF_WEBROOT_FRONT_URL
                                                                ) . '?=' . time(); ?>"
                                                                 alt="<?php echo htmlspecialchars($title); ?>">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
            
                                <!-- Next arrow -->
                                <button type="button" class="course-slider-arrow next" aria-label="Next course">
                                    <svg viewBox="0 0 24 24" fill="none">
                                        <path d="M9 5L15 12L9 19"
                                              stroke="#4b5563" stroke-width="1.8"
                                              stroke-linecap="round" stroke-linejoin="round"/>
                                    </svg>
                                </button>
                            </div>
            
                            <!-- Dots under slider -->
                            <!-- <?php if ($totalSlides > 1) { ?>
                                <div class="course-slider-dots" data-slider-dots="recommended-courses">
                                    <?php for ($i = 0; $i < $totalSlides; $i++) { ?>
                                        <button type="button"
                                                class="course-slider-dot <?php echo $i === 0 ? 'active' : ''; ?>"
                                                data-index="<?php echo $i; ?>"></button>
                                    <?php } ?>
                                </div>
                            <?php } ?> -->
                        </div>
            
                        <!-- RIGHT: VERTICAL MINI LIST -->
                        <div class="col-lg-4">
                            <div class="recommended-side-list">
                                <div class="recommended-side-title">
                                    More courses you might like
                                </div>
            
                                <?php if (!empty($sideListCourses)) { ?>
                                    <?php foreach ($sideListCourses as $crs) { ?>
                                        <div class="course-mini-card">
                                            <div class="course-mini-card-inner">
                                                <div class="course-mini-thumb">
                                                    <img src="<?php echo MyUtility::makeUrl(
                                                            'Image',
                                                            'show',
                                                            [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId],
                                                            CONF_WEBROOT_FRONT_URL
                                                        ) . '?=' . time(); ?>"
                                                         alt="<?php echo htmlspecialchars($crs['course_title']); ?>">
                                                </div>
                                                <div class="course-mini-body">
                                                    <p class="course-mini-title">
                                                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>"
                                                           style="color:#111827;text-decoration:none;">
                                                            <?php
                                                            $miniTitle = $crs['course_title'] ?? '';
                                                            echo (strlen($miniTitle) > 52)
                                                                ? CommonHelper::renderHtml(substr($miniTitle, 0, 52)) . '…'
                                                                : CommonHelper::renderHtml($miniTitle);
                                                            ?>
                                                        </a>
                                                    </p>
                                                    <div class="course-mini-meta">
                                                        <?php echo CommonHelper::renderHtml($crs['subcate_name'] ?? ''); ?>
                                                    </div>
                                                    <div class="course-mini-footer">
                                                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                                            View course
                                                        </a>
                                                     
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>
                                <?php } else { ?>
                                    <p class="text-muted mb-0" style="font-size:12px;">
                                        More courses will appear here as we add them.
                                    </p>
                                <?php } ?>
                                <div class="view-all-courses-wrap">
                <a href="<?php echo MyUtility::makeUrl('Courses'); ?>" class="view-all-courses-btn">
                    Browse all courses
                </a>
            </div>
            
                            </div>
                        </div>
                    </div>
                <?php } else { ?>
                    <div class="row">
                        <div class="col-12">
                            <p class="text-muted mb-0">No recommended courses to show right now.</p>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>

        <!-- RECOMMENDED COURSES -->
      <!-- RECOMMENDED COURSES (HERO SLIDER + SIDE LIST) -->


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

 <a href="<?php echo MyUtility::makeUrl('Teachers'); ?>"
   class="btn-tutor-primary">
   <span>Find a Tutor</span>
</a>


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
    var sliderRoot   = document.querySelector('.course-slider-shell[data-slider="recommended-courses"]');
    if (!sliderRoot) return;

    var slides       = sliderRoot.querySelectorAll('.course-slide');
    if (!slides.length) return;

    var prevBtn      = sliderRoot.querySelector('.course-slider-arrow.prev');
    var nextBtn      = sliderRoot.querySelector('.course-slider-arrow.next');
    var dotsRoot     = document.querySelector('.course-slider-dots[data-slider-dots="recommended-courses"]');
    var dots         = dotsRoot ? dotsRoot.querySelectorAll('.course-slider-dot') : [];
    var currentIndex = 0;
    var intervalMs   = 5000;
    var timer        = null;

    function showSlide(index) {
        var total = slides.length;
        if (index < 0) index = total - 1;
        if (index >= total) index = 0;

        slides.forEach(function (slide, i) {
            slide.classList.toggle('active', i === index);
        });
        if (dots.length) {
            dots.forEach(function (dot, i) {
                dot.classList.toggle('active', i === index);
            });
        }
        currentIndex = index;
    }

    function next(step) {
        showSlide(currentIndex + step);
    }

    function resetTimer() {
        if (timer) clearInterval(timer);
        timer = setInterval(function () {
            next(1);
        }, intervalMs);
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            next(-1);
            resetTimer();
        });
    }
    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            next(1);
            resetTimer();
        });
    }

    if (dots.length) {
        dots.forEach(function (dot) {
            dot.addEventListener('click', function () {
                var target = parseInt(this.getAttribute('data-index'), 10);
                if (!isNaN(target)) {
                    showSlide(target);
                    resetTimer();
                }
            });
        });
    }

    // Init
    showSlide(0);
    resetTimer();
});

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const chips = document.querySelectorAll('.question-indicator-chip');
    const panel = document.getElementById('question-explanation-panel');

    if (!chips.length || !panel) {
        return;
    }

    chips.forEach(function (chip) {
        chip.style.cursor = 'pointer'; // optional: nicer UX

        chip.addEventListener('click', function () {
            // Remove 'active' from all chips
            chips.forEach(function (c) {
                c.classList.remove('active');
            });
            // Add to clicked chip
            this.classList.add('active');

            const num   = this.getAttribute('data-number') || '';
            const qText = this.getAttribute('data-question') || '';
            const exp   = this.getAttribute('data-explanation') || '';

            if (!qText && !exp) {
                panel.innerHTML =
                    '<p class="text-muted mb-0">No explanation provided for this question yet.</p>';
                return;
            }

            panel.innerHTML =
                '<h6>Question ' + num + '</h6>' +
                (qText
                    ? '<p class="q-text"><strong>Question:</strong> ' + qText + '</p>'
                    : ''
                ) +
                (exp
                    ? '<p class="exp-text"><strong>Explanation:</strong> ' + exp + '</p>'
                    : ''
                );
        });
    });
});
</script>
