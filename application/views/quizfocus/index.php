<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

if (isset($_GET['subtopic'])) {
    $subtopicId = intval($_GET['subtopic']);
}
?>

<style>
    .hidden {
        display: none;
    }

    /* PAGE BACKGROUND + LAYOUT */
    .quiz-mode-page {
        position: relative;
        min-height: 800px;
        padding-top: 40px;
        padding-bottom: 60px;
        background: radial-gradient(circle at top left, #e4f4ff 0, #f8fbff 45%, #f3f5fa 100%);
        overflow: hidden;
    }

    .quiz-mode-page::before,
    .quiz-mode-page::after {
        content: '';
        position: absolute;
        border-radius: 999px;
        filter: blur(40px);
        opacity: 0.35;
        pointer-events: none;
    }

    .quiz-mode-page::before {
        width: 280px;
        height: 280px;
        background: #2dadff;
        top: -80px;
        right: 10%;
    }

    .quiz-mode-page::after {
        width: 260px;
        height: 260px;
        background: #14a3ff;
        bottom: -100px;
        left: 5%;
    }

    /* MAIN CARD */
    .quiz-selection-box {
        position: relative;
        border-radius: 24px;
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(45, 173, 255, 0.15);
        box-shadow: 0 18px 50px rgba(15, 23, 42, 0.08);
    }

    .quiz-selection-box h2 {
        font-weight: 700;
        font-size: 26px;
        color: #0f172a;
    }

    .quiz-selection-subtitle {
        font-size: 14px;
        color: #64748b;
        margin-bottom: 18px;
    }

    /* TOP BADGE */
    .quiz-mode-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 6px 14px;
        border-radius: 999px;
        background: rgba(45, 173, 255, 0.08);
        color: #0369a1;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        margin-bottom: 4px;
    }

    .quiz-mode-badge svg {
        width: 14px;
        height: 14px;
    }

    .quiz-mode-header {
        margin-bottom: 18px;
    }

    /* STEP INDICATOR */
    .quiz-mode-steps {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 18px;
        margin-bottom: 26px;
        flex-wrap: wrap;
    }

    .quiz-mode-step {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 13px;
        color: #475569;
    }

    .quiz-mode-step-bubble {
        width: 26px;
        height: 26px;
        border-radius: 999px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 13px;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
    }

    .quiz-mode-step span {
        font-weight: 500;
    }

    /* BUTTON CARDS */
    .twin-btn-wrap {
        gap: 20px;
    }

    .custom-quiz-btn {
        position: relative;
        border: none;
        border-radius: 20px;
        background: #f8fafc;
        padding: 20px 18px;
        min-height: 190px;
        display: flex;
        align-items: stretch;
        justify-content: center;
        text-align: left;
        transition: all 0.28s cubic-bezier(0.22, 0.61, 0.36, 1);
        box-shadow: 0 10px 30px rgba(15, 23, 42, 0.03);
        overflow: hidden;
    }

    .custom-quiz-btn span {
        color: #0f172a;
    }

    .custom-quiz-btn .mode-title {
        font-size: 22px;
        font-weight: 700;
        color: #0f172a;
    }

    .custom-quiz-btn .mode-subtitle {
        font-size: 13px;
        color: #6b7280;
        margin-top: 3px;
    }

    .custom-quiz-btn .mode-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 999px;
        background: #e0f2fe;
        color: #0369a1;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        margin-bottom: 6px;
    }

    .custom-quiz-btn .mode-tag.mode-focus {
        background: #fef3c7;
        color: #92400e;
    }

    .custom-quiz-btn .mode-tag svg {
        width: 12px;
        height: 12px;
    }

    .custom-quiz-btn .mode-points {
        font-size: 12px;
        color: #6b7280;
        margin-top: 10px;
        list-style: none;
        padding-left: 0;
    }

    .custom-quiz-btn .mode-points li {
        display: flex;
        align-items: center;
        gap: 6px;
        margin-bottom: 5px;
    }

    .custom-quiz-btn .mode-points li::before {
        content: '•';
        font-size: 16px;
        line-height: 1;
        color: #22c55e;
    }

    .custom-quiz-btn .img-box,
    .custom-quiz-btn .img-quiz {
        max-width: 80px;
        max-height: 80px;
        object-fit: contain;
        border-radius: 16px;
        background: #fff;
        padding: 8px;
        box-shadow: 0 6px 16px rgba(15, 23, 42, 0.08);
    }

    .custom-quiz-btn .mode-content-wrap {
        flex: 1;
    }

    .custom-quiz-btn .mode-image-wrap {
        flex-shrink: 0;
        margin-left: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .custom-quiz-btn:hover,
    .custom-quiz-btn:focus {
        transform: translateY(-4px);
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.16);
        background: #ffffff;
    }

    .custom-quiz-btn:hover::after,
    .custom-quiz-btn:focus::after {
        opacity: 0.12;
    }

    .custom-quiz-btn::after {
        content: '';
        position: absolute;
        inset: 0;
        background: radial-gradient(circle at top right, rgba(45, 173, 255, 0.6), transparent 60%);
        opacity: 0;
        transition: opacity 0.28s ease;
        pointer-events: none;
    }

    .custom-quiz-btn.active-mode {
        outline: 2px solid #2dadff;
        box-shadow: 0 16px 40px rgba(37, 99, 235, 0.2);
        background: linear-gradient(135deg, #ffffff 0%, #eff6ff 100%);
    }

    /* DYNAMIC DESCRIPTION */
    .quiz-selection-box .quiz-selection-box-inner {
        border-radius: 20px;
        background: #f9fafb;
        border: 1px dashed rgba(148, 163, 184, 0.7);
    }

    .quiz-mode-description {
        font-size: 14px;
        color: #4b5563;
        line-height: 1.6;
        max-width: 680px;
        margin: 0 auto;
    }

    .quiz-mode-description strong {
        color: #111827;
    }

    .quiz-mode-description span.mode-label {
        color: #2563eb;
        font-weight: 600;
    }

    /* RESPONSIVE */
    @media (max-width: 991.98px) {
        .quiz-selection-box {
            padding: 24px 18px !important;
        }

        .twin-btn-wrap {
            flex-direction: column;
        }

        .custom-quiz-btn {
            width: 100% !important;
        }

        .custom-quiz-btn .mode-image-wrap {
            margin-left: 10px;
        }
    }

    @media (max-width: 575.98px) {
        .quiz-selection-box h2 {
            font-size: 22px;
        }

        .quiz-mode-step {
            font-size: 12px;
        }

        .custom-quiz-btn {
            padding: 18px 14px;
            min-height: 170px;
        }

        .custom-quiz-btn .mode-title {
            font-size: 20px;
        }

        .custom-quiz-btn .img-box,
        .custom-quiz-btn .img-quiz {
            max-width: 64px;
            max-height: 64px;
        }
    }

    /* Existing generic styles you had (kept) */
    .quiz-container {
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        width: 900px;
        margin: auto;
        padding: 20px;
        border-radius: 32px;
        text-align: center;
    }

    .quiz-question h3 {
        font-size: 20px;
        color: #333;
        margin-bottom: 15px;
    }

    .quiz-hint {
        color: #007bff;
        font-style: italic;
        margin-top: 10px;
    }

    .quiz-explanation {
        color: #28a745;
        font-weight: bold;
        margin-top: 10px;
    }

    .quiz-options {
        text-align: left;
    }

    .quiz-option {
        display: block;
        background: #f5f5f5;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        cursor: pointer;
    }

    .quiz-option input {
        margin-right: 8px;
    }

    .quiz-navigation {
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        font-size: 14px;
        cursor: pointer;
        border-radius: 6px;
        transition: 0.2s;
    }

    .btn--primary {
        background: #4CAF50;
        color: white;
    }

    .btn--primary:hover {
        background: #45a049;
    }

    .btn--secondary {
        background: #ccc;
        color: black;
    }

    .btn--secondary:hover {
        background: #bbb;
    }

    .btn--info {
        background: #007bff;
        color: white;
        margin-bottom: 10px;
    }

    .btn--info:hover {
        background: #0056b3;
    }

    .quiz-header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: red;
    }
</style>

<section class="section section--gray section--listing quiz-mode-page">
    <div class="container-fluid px-lg-5 pb-5">
        <div class="row g-3 justify-content-center quiz-wrapper">
            <div class="col-md-9 col-12 mb-3">

                <div class="quiz-selection-box p-4 p-md-5 bg-white rounded-4 shadow-sm">
                    <div class="quiz-mode-header text-center">
                        <div class="quiz-mode-badge">
                            <!-- simple lightning icon -->
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M11.3 1.046a1 1 0 0 1 .683 1.243L10.615 8H15a1 1 0 0 1 .8 1.6l-7 9.5A1 1 0 0 1 7 18.95L9.385 12H5a1 1 0 0 1-.949-1.316l3-9A1 1 0 0 1 7.99 1h3.31z"/>
                            </svg>
                            Smart Practice
                        </div>
                        <h2 class="mb-1 text-center">How would you like to learn?</h2>
                        <p class="quiz-selection-subtitle mb-0">
                            Choose a mode to match your focus level today — you can always switch next time.
                        </p>
                    </div>

                    <div class="quiz-mode-steps">
                        <div class="quiz-mode-step">
                            <div class="quiz-mode-step-bubble">1</div>
                            <span>Pick your learning style</span>
                        </div>
                        <div class="quiz-mode-step">
                            <div class="quiz-mode-step-bubble">2</div>
                            <span>Answer topic-based questions</span>
                        </div>
                        <div class="quiz-mode-step">
                            <div class="quiz-mode-step-bubble">3</div>
                            <span>Review and strengthen weak areas</span>
                        </div>
                    </div>

                    <div class="d-flex align-items-stretch justify-content-between twin-btn-wrap">
                        <!-- ALL AT ONCE -->
                        <button
                            class="custom-quiz-btn w-50 text-center me-3 quiz-mode-btn"
                            data-mode="all"
                            onclick="window.location.href='quizattemptall?subtopic=<?php echo $subtopicId; ?>';"
                        >
                            <span class="d-flex flex-row align-items-stretch w-100">
                                <div class="mode-content-wrap d-flex flex-column justify-content-center text-start">
                                    <div class="mode-tag">
                                        <!-- grid icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M3 3h5v5H3V3zm9 0h5v5h-5V3zM3 12h5v5H3v-5zm9 0h5v5h-5v-5z"/>
                                        </svg>
                                        All Questions View
                                    </div>
                                    <span class="mode-title">All at once</span>
                                    <span class="mode-subtitle">See the full quiz in one go & build exam stamina.</span>

                                    <ul class="mode-points">
                                        <li>Great for timed exam-style practice.</li>
                                        <li>Quickly spot which questions look easy or hard.</li>
                                        <li>Perfect when you feel confident with this topic.</li>
                                    </ul>
                                </div>

                                <div class="mode-image-wrap">
                                    <img src="<?php echo getBaseUrl(); ?>assets/img/boxes.svg" alt="" class="mb-0 img-box">
                                </div>
                            </span>
                        </button>

                        <!-- FOCUS MODE -->
                        <button
                            class="custom-quiz-btn w-50 text-center ms-3 quiz-mode-btn"
                            data-mode="focus"
                            onclick="window.location.href='quizattempt?subtopic=<?php echo $subtopicId; ?>';"
                        >
                            <span class="d-flex flex-row align-items-stretch w-100">
                                <div class="mode-content-wrap d-flex flex-column justify-content-center text-start">
                                    <div class="mode-tag mode-focus">
                                        <!-- focus target icon -->
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M10 3a1 1 0 0 1 1 1v1.055A5.002 5.002 0 0 1 15.945 9H17a1 1 0 1 1 0 2h-1.055A5.002 5.002 0 0 1 11 14.945V16a1 1 0 1 1-2 0v-1.055A5.002 5.002 0 0 1 4.055 11H3a1 1 0 1 1 0-2h1.055A5.002 5.002 0 0 1 9 5.055V4a1 1 0 0 1 1-1zm0 4a3 3 0 1 0 .001 6.001A3 3 0 0 0 10 7z"/>
                                        </svg>
                                        Recommended
                                    </div>
                                    <span class="mode-title">Focus</span>
                                    <span class="mode-subtitle">One question at a time for deep understanding.</span>

                                    <ul class="mode-points">
                                        <li>Reduce overwhelm and stay fully focused.</li>
                                        <li>Ideal when learning a new or tricky topic.</li>
                                        <li>Perfect for step-by-step concept building.</li>
                                    </ul>
                                </div>

                                <div class="mode-image-wrap">
                                    <img src="<?php echo getBaseUrl(); ?>assets/img/Mission statement.png" alt="" class="mb-0 img-quiz">
                                </div>
                            </span>
                        </button>
                    </div>

                    <br>

                    <div class="quiz-selection-box quiz-selection-box-inner p-4 text-center mt-1">
                        <p class="quiz-mode-description quiz-mode-dynamic-text">
                            Choose the learning style that suits you best: with
                            <strong>All at once</strong>, explore all questions together and build your confidence
                            by tackling the entire quiz in one go. Or select <strong>Focus</strong> mode to concentrate
                            on one question at a time — perfect for deep understanding and step-by-step learning.
                            No matter which you pick, you’ll strengthen your knowledge and be ready to ace your topics!
                        </p>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<script>
    var questions = <?php echo json_encode($questionData ?? []); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Then load the jQuery Validation plugin -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>

<script>
    var _body = $('body');
    var _toggle = $('.js-filter-toggle');
    _toggle.each(function() {
        var _this = $(this),
            _target = $(_this.attr('href'));

        _this.on('click', function(e) {
            e.preventDefault();
            _target.toggleClass('is-filter-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-filter-show');
        });
    });

    let currentQuestion = 0;
    let timerDuration = 10 * 60;
    let timerInterval = null;
    let userAnswers = {};

    var userSessionId = "<?php echo $_SESSION['subtopicId']; ?>";

    // INTERACTIVITY: highlight active card + dynamic description
    document.addEventListener('DOMContentLoaded', function() {
        const buttons = document.querySelectorAll('.quiz-mode-btn');
        const descEl = document.querySelector('.quiz-mode-dynamic-text');

        const defaultText = `Choose the learning style that suits you best: with 
            All at once, explore all questions together and build your confidence 
            by tackling the entire quiz in one go. Or select Focus mode to concentrate 
            on one question at a time — perfect for deep understanding and step-by-step learning. 
            No matter which you pick, you’ll strengthen your knowledge and be ready to ace your topics!`;

        const modeTexts = {
            all: `You’re choosing <span class="mode-label">All at once</span> — great when you’re ready 
                  to simulate a real exam. View all questions together, move freely between them, 
                  and build your speed and confidence by tackling the entire quiz in one go.`,
            focus: `You’re choosing <span class="mode-label">Focus mode</span> — perfect for careful, 
                    concept-by-concept learning. See one question at a time, stay fully present, 
                    and give each idea the attention it deserves. Great when a topic feels new or challenging.`
        };

        function setActiveMode(mode) {
            buttons.forEach(btn => {
                if (btn.dataset.mode === mode) {
                    btn.classList.add('active-mode');
                } else {
                    btn.classList.remove('active-mode');
                }
            });

            if (descEl && modeTexts[mode]) {
                descEl.innerHTML = modeTexts[mode];
            }
        }

        buttons.forEach(btn => {
            const mode = btn.dataset.mode;

            btn.addEventListener('mouseenter', function() {
                setActiveMode(mode);
            });

            btn.addEventListener('focus', function() {
                setActiveMode(mode);
            });
        });

        // Set default active mode = focus (recommended)
        setActiveMode('focus');
    });
</script>
