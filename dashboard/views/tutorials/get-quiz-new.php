<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php if (!empty($quizDetails['is_math'])): ?>
    <!-- Math editor only for math quizzes on attempt screen -->
    <link rel="stylesheet"
           href="https://cdn.jsdelivr.net/npm/mathlive/dist/mathlive-static.css" />
<script src="https://cdn.jsdelivr.net/npm/mathlive/dist/mathlive.min.js"></script>
    <script src="<?php echo CONF_WEBROOT_DASHBOARD; ?>../js/math-editor.js"></script>
<?php endif; ?>

<?php if ($quizDetails) { ?>
<style>
/* ====== Layout (sidebar LEFT + widened question panel) ====== */
.qz-shell {
  display: grid;
  grid-template-columns: 320px minmax(0, 1fr);
  gap: 22px;
  max-width: 1400px;
  margin: 10px auto 28px;
  padding: 0 12px;
}

@media (max-width: 1100px){
  .qz-shell{ grid-template-columns: 1fr; gap: 14px; }
}

@media (max-width: 520px){
  .qz-shell{ padding: 0 10px; margin: 8px auto 18px; }
}


/* ====== Sidebar (sticky, fixed width, full height viewport minus header) ====== */
.qz-side {
  position: sticky;
  top: 84px; 
  /* left: 10px;                         keeps it visible below header */
  height: calc(100vh - 84px);         /* fixed height relative to viewport */
  overflow: auto;                     /* allow scrolling inside if needed */
  width: 320px;                       /* fixed width */
  background: #ffffff;
  border: 1px solid #e8edf3;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 10px 22px rgba(17,24,39,.05);
}
.qz-side h5{ margin:0 0 6px; font-size:15px; color:#0f172a; }
.qz-timer {
  display:flex; align-items:center; justify-content:space-between;
  background:#fff7ed; border:1px solid #fde7c7; color:#8a4b10;
  border-radius:10px; padding:10px 12px; font-weight:700;
}
@media (max-width: 1100px){
  .qz-side{
    position: relative;     /* ✅ stop sticky on small screens */
    top: auto;
    height: auto;           /* ✅ let it grow */
    overflow: visible;      /* ✅ no inner scrolling */
    width: 100%;            /* ✅ full width */
  }
}
@media (max-width: 520px){
  .quiz-container{ padding: 12px 12px 6px; border-radius: 14px; }
  .quiz-question{ padding: 12px; }
  .qz-qtop{ gap: 10px; }
  .qz-qnum{ min-width: 30px; height: 30px; border-radius: 10px; }
}

/* Add to the style section in get-quiz-new.php */
.quiz-math-wrapper {
    position: relative;
    margin: 8px 0;
    border-radius: 8px;
    border: 1px solid #e2e8f0;
    background: #f9fafb;
    padding: 12px;
}

.quiz-math-wrapper math-field {
    min-height: 40px;
    width: 100%;
    padding: 8px 10px;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    background: white;
    font-family: "Cambria Math", "Latin Modern Math", STIXGeneral, serif;
}

.quiz-math-wrapper math-field:focus {
    outline: 2px solid #2DADFF;
    outline-offset: 2px;
}

.quiz-math-wrapper .rwu-math-clear {
    position: absolute;
    right: 12px;
    top: 12px;
    border: none;
    background: #f3f4f6;
    border-radius: 6px;
    padding: 4px 10px;
    font-size: 12px;
    cursor: pointer;
    color: #6b7280;
}

.quiz-math-wrapper .rwu-math-clear:hover {
    background: #e5e7eb;
    color: #374151;
}
.qz-progress-wrap{ margin:12px 0 8px; }
.qz-progress-bar{ height:10px; background:#f1f5f9; border-radius:999px; overflow:hidden; }
.qz-progress-bar > span{ display:block; height:100%; width:0%; background:linear-gradient(90deg,#14A0A3,#0ea5e9); transition:width .25s ease; }
.qz-progress-meta{ display:flex; justify-content:space-between; color:#64748b; font-size:12px; margin-top:6px; }

/* Navigator dots */
.qz-nav{
  display:grid; grid-template-columns:repeat(auto-fill,minmax(34px,1fr)); gap:8px; margin-top:12px;
}
.qz-dot{
  display:flex; align-items:center; justify-content:center;
  height:34px; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc; color:#0f172a;
  font-weight:700; cursor:pointer; transition:all .15s ease;
}
.qz-dot:hover{ border-color:#cbd5e1; background:#eef2f7; }
.qz-dot.answered{ background:#e6fffb; color:#065f5b; border-color:#99f6e4; }
.qz-dot.active{ background:#14A0A3; color:#fff; border-color:#14A0A3; }

/* Actions (submit only in sidebar) */
.qz-actions-side{ display:flex; gap:8px; margin-top:12px; }
.qz-btn-primary, .qz-btn-secondary{
  padding:11px 14px; border-radius:10px; border:1px solid transparent; font-weight:700; cursor:pointer;
}
.qz-btn-primary{ background:#14A0A3; color:#fff; box-shadow:0 6px 16px rgba(20,160,163,.22); }
.qz-btn-primary:hover{ background:#118e90; }
.qz-btn-secondary{ background:#f3f4f6; color:#111827; border-color:#e5e7eb; }

/* ====== Questions panel ====== */
.quiz-container{
  background:#ffffff; border:1px solid #e8edf3; border-radius:16px; padding:16px 16px 6px;
  width: 800px;
  box-shadow:0 10px 24px rgba(17,24,39,.04);
}
.quiz-header{ padding:2px 6px 10px; border-radius:8px 8px 0 0; }
.quiz-body{ padding:6px 4px 2px; }

.quiz-question{
  background:#ffffff; border:1px solid #e7edf4; border-radius:14px;
  padding:16px; margin:0 0 14px; position:relative;
  transition:box-shadow .15s ease, border-color .15s ease;
}
.quiz-question:hover{ box-shadow:0 8px 18px rgba(17,24,39,.05); border-color:#d8e3ef; }
.qz-qtop{ display:flex; align-items:flex-start; gap:12px; }
.qz-qnum{ background:#eef7f7; color:#0f6c6e; font-weight:800; min-width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; }
.qz-qtitle{ flex:1; }
.qz-marks{ color:#475569; }

/* Option tiles */
.quiz-options{ display:grid; gap:8px; margin-top:10px; }
.quiz-options input[type="radio"], .quiz-options input[type="checkbox"]{ display:none; }
.qz-opt{
  background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:10px 12px; cursor:pointer;
  transition:all .15s ease; display:flex; gap:10px; align-items:flex-start;
}
.qz-opt:hover{ background:#f1f5f9; border-color:#d7dee9; }
.qz-opt .tick{ width:20px; height:20px; min-width:20px; border-radius:6px; background:#e2f6f7; display:flex; align-items:center; justify-content:center; color:#0f6c6e; font-weight:900; font-size:13px; }
.quiz-options input[type="radio"]:checked + label.qz-opt,
.quiz-options input[type="checkbox"]:checked + label.qz-opt{
  background:#14A0A3; color:#fff; border-color:#14A0A3; box-shadow:0 8px 18px rgba(20,160,163,.25);
}
.quiz-options input[type="radio"]:focus + label.qz-opt,
.quiz-options input[type="checkbox"]:focus + label.qz-opt{ outline:3px solid rgba(14,165,233,.35); outline-offset:2px; }

/* Submit bar in panel -> hidden (submit from sidebar only) */
.qz-submitbar{ display:none !important; }

/* ====== Modal + Confetti ====== */
/* ====== Modal + Confetti (QUIZ) - Production grade ====== */
#qzConfettiCanvas { position:fixed; inset:0; pointer-events:none; display:none; z-index:10020; }

.result-modal-backdrop{
  position:fixed; inset:0;
  background:rgba(15,23,42,.55);
  backdrop-filter: blur(6px);
  display:none; z-index:10010;
}
.result-modal{
  position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
  width:min(1100px,96vw);
  max-height:90vh;
  background:#fff;
  border-radius:18px;
  border:1px solid rgba(226,232,240,.9);
  box-shadow:0 30px 80px rgba(0,0,0,.35);
  display:none;               /* ✅ MUST be none */
  z-index:10011;
  flex-direction:column;      /* ✅ keep */
  overflow:hidden;
}

.result-head{
  padding:18px 20px;
  background:linear-gradient(90deg, rgba(20,160,163,.10), rgba(14,165,233,.10));
  border-bottom:1px solid #eef2f7;
  display:flex; align-items:center; justify-content:space-between; gap:12px;
}
.result-head .eyebrow{ font-size:12px; color:#64748b; font-weight:800; text-transform:uppercase; letter-spacing:.02em; }
.result-head h3{ margin:2px 0 0; font-size:20px; color:#0f172a; font-weight:900; }
.result-x{
  border:1px solid #e2e8f0; background:#fff; border-radius:12px;
  width:40px; height:40px; cursor:pointer;
  display:flex; align-items:center; justify-content:center;
}
.result-x:hover{ background:#f8fafc; }

.result-body{
  padding:0;
  display:flex; flex-direction:column;
    flex: 1;          /* ✅ takes remaining modal space */
  min-height: 0;  
}

.result-summary-wrap{
  padding:16px 20px;
  display:grid;
  grid-template-columns: 220px 1fr;
  gap:14px;
  border-bottom:1px solid #eef2f7;
}
@media (max-width: 820px){ .result-summary-wrap{ grid-template-columns:1fr; } }

.qz-scoreCard{
  border:1px solid #e8edf3; background:#fff;
  border-radius:16px; padding:14px;
  box-shadow:0 10px 24px rgba(17,24,39,.04);
  display:flex; align-items:center; gap:14px;
}
.qz-ring{
  --p: 0;
  width:74px; height:74px; border-radius:999px;
  background:conic-gradient(#14A0A3 calc(var(--p) * 1%), #e2e8f0 0);
  display:grid; place-items:center;
}
.qz-ring__inner{
  width:60px; height:60px; border-radius:999px;
  background:#fff; border:1px solid #eef2f7;
  display:flex; flex-direction:column; align-items:center; justify-content:center;
  font-weight:900; color:#0f172a;
}
.qz-ring__inner small{ font-weight:800; color:#64748b; font-size:10px; margin-top:-2px; }

.qz-summaryGrid{
  display:grid;
  grid-template-columns: repeat(4, minmax(0, 1fr));
  gap:10px;
}
@media (max-width: 820px){ .qz-summaryGrid{ grid-template-columns: repeat(2, minmax(0, 1fr)); } }

.qz-kpi{
  border:1px solid #e8edf3; background:#fafafa;
  border-radius:14px; padding:12px;
}
.qz-kpi .t{ font-size:12px; color:#64748b; font-weight:800; }
.qz-kpi .v{ font-size:16px; color:#0f172a; font-weight:900; margin-top:4px; }

.qz-status{
  display:inline-flex; align-items:center; gap:8px;
  padding:8px 10px; border-radius:999px;
  border:1px solid #e2e8f0;
  font-weight:900; font-size:12px;
  width:max-content;
}
.qz-status.pass{ background:#dcfce7; border-color:#bbf7d0; color:#166534; }
.qz-status.fail{ background:#fee2e2; border-color:#fecaca; color:#991b1b; }

.result-tools{
  padding:12px 20px;
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  border-bottom:1px solid #eef2f7;
  flex-wrap:wrap;
}
.qz-chip{
  padding:8px 10px; border-radius:999px;
  border:1px solid #e2e8f0; background:#fff;
  font-weight:900; font-size:12px; cursor:pointer; color:#0f172a;
}
.qz-chip:hover{ background:#f8fafc; }
.qz-chip.is-active{
  background:#14A0A3; border-color:#14A0A3; color:#fff;
  box-shadow:0 10px 20px rgba(20,160,163,.18);
}
.result-search{
  display:flex; align-items:center; gap:8px;
  border:1px solid #e2e8f0; background:#fff;
  border-radius:12px; padding:8px 10px;
}
.result-search input{ border:none; outline:none; min-width:240px; }
@media (max-width: 520px){ .result-search input{ min-width:160px; } }

.result-list{
  padding:10px 20px 90px; /* ✅ extra bottom room so explanation never hides behind actions */
  overflow:auto;
  flex: 1;                /* ✅ takes remaining space */
  min-height: 0;          /* ✅ critical */
  max-height: none;       /* ✅ remove the 55vh cap */
  background:linear-gradient(180deg, #ffffff, #fbfdff);
}


.qz-qa{
  border:1px solid #e8edf3;
  border-radius:16px;
  background:#fff;
  overflow:hidden;
  margin:10px 0;
  box-shadow:0 10px 24px rgba(17,24,39,.03);
}
.qz-qa__head{
  width:100%;
  display:flex; align-items:center; justify-content:space-between; gap:12px;
  padding:14px 14px;
  background:#fff;
  cursor:pointer;
  border:none;
}
.qz-qa__left{ display:flex; gap:12px; align-items:flex-start; }
.qz-qa__num{
  width:34px; height:34px; border-radius:12px;
  display:flex; align-items:center; justify-content:center;
  font-weight:900;
  background:rgba(20,160,163,.10);
  color:#0f6c6e;
  flex-shrink:0;
}
.qz-qa__title{ font-weight:900; color:#0f172a; }
.qz-qa__meta{ font-size:12px; color:#64748b; margin-top:3px; }
.qz-badge{
  font-size:12px; font-weight:900;
  padding:6px 10px; border-radius:999px;
  color:#fff; flex-shrink:0;
}
.qz-badge.correct{ background:#16a34a; }
.qz-badge.incorrect{ background:#F5411F; }
.qz-qa__body{
  display:none;
  border-top:1px solid #eef2f7;
  padding:14px 14px;
  background:#fcfdff;
}
.qz-cols{ display:grid; grid-template-columns:1fr 1fr; gap:10px; }
@media (max-width: 820px){ .qz-cols{ grid-template-columns:1fr; } }
.qz-box{
  border:1px solid #e8edf3; border-radius:14px;
  background:#fff; padding:12px;
}
.qz-box h6{ margin:0 0 8px; font-size:12px; color:#64748b; font-weight:900; text-transform:uppercase; letter-spacing:.02em; }
.qz-mono{
  font-family:ui-monospace, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
  font-size:12.5px;
  white-space:pre-wrap;
  background:#f8fafc;
  border:1px solid #e2e8f0;
  border-radius:12px;
  padding:10px;
}

.result-actions{
  padding:14px 20px;
  border-top:1px solid #eef2f7;
  background:#fff;
  display:flex; justify-content:flex-end; gap:10px;
  position: sticky; bottom: 0;  /* ✅ */
}
@media (max-width: 520px){
  .result-summary-wrap{ padding: 12px 12px; }
  .result-tools{ padding: 10px 12px; }
  .result-list{ padding: 10px 12px 90px; }
  .result-actions{ padding: 12px 12px; }
  .result-search input{ min-width: 120px; }  /* prevent overflow */
}

.btn-find-tutor{
  background:#F5411F; color:#fff; border:none;
  padding:10px 18px; border-radius:12px; font-weight:900;
  box-shadow:0 10px 22px rgba(245,65,31,.22);
}
.btn-find-tutor:hover{ background:#d73d1c; }
.btn-close{
  background:#fff; color:#0f172a;
  border:1px solid #e2e8f0;
  padding:10px 18px; border-radius:12px; font-weight:900;
}
.btn-close:hover{ background:#f8fafc; }

</style>

<?php if ($previousAttempt) { ?>
  <div class="quiz-message" style="padding:16px; text-align:center; background:#fff3cd; border:1px solid #ffeaa7; border-radius:10px; margin:0 auto 14px; max-width:1400px;">
    <h4 style="margin:0 0 6px;"><?php echo Label::getLabel('LBL_QUIZ_ALREADY_ATTEMPTED'); ?></h4>
    <p style="margin:0;"><?php echo Label::getLabel('LBL_YOUR_PREVIOUS_SCORE') . ': ' . (float)$previousAttempt['percentage'] . '%'; ?></p>
    <p style="margin:4px 0 8px;"><?php echo Label::getLabel('LBL_STATUS') . ': ' . ($previousAttempt['status'] == 2 ? 'Passed' : 'Failed'); ?></p>
    <!-- <button class="qz-btn-primary" onclick="retakeQuiz()"><?php echo Label::getLabel('LBL_RETAKE_QUIZ'); ?></button> -->
  </div>
<?php } ?>

<div class="qz-shell">
  <!-- ===== Sidebar (LEFT) ===== -->
  <aside class="qz-side">
    <h5><?php echo Label::getLabel('LBL_TIME_REMAINING'); ?></h5>
    <div class="qz-timer"><span>⏱️</span><span id="quizTimeLeft">08:00</span></div>

    <div class="qz-progress-wrap">
      <div class="qz-progress-bar"><span id="qzProgFill"></span></div>
      <div class="qz-progress-meta">
        <div><span id="qzAnsCount">0</span> / <?php echo count($quizDetails['questions']); ?> answered</div>
        <div id="qzPercent">0%</div>
      </div>
    </div>

    <h5 style="margin-top:10px;"><?php echo Label::getLabel('LBL_QUESTIONS'); ?></h5>
    <div id="qzNav" class="qz-nav">
      <?php foreach ($quizDetails['questions'] as $i => $q) { ?>
        <div class="qz-dot" data-target="qcard_<?php echo $q['question_id']; ?>"><?php echo $i+1; ?></div>
      <?php } ?>
    </div>

    <div class="qz-actions-side">
      <button class="qz-btn-secondary" onclick="window.scrollTo({top:0,behavior:'smooth'})">Top</button>
      <button class="qz-btn-primary" onclick="submitLectureQuiz()"><?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?></button>
    </div>
  </aside>

  <!-- ===== Questions Panel ===== -->
  <div class="quiz-container">
    <div class="quiz-header">
      <input type="hidden" id="quiz_id" value="<?php echo $quizDetails['quiz_id']; ?>">
      <input type="hidden" id="quiz_pass_percentage" value="<?php echo $quizDetails['quiz_pass_percentage']; ?>">
      <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">
      <input type="hidden" id="lectureId" value="<?php echo $lectureId; ?>">
      <input type="hidden" id="progressId" value="<?php echo $progressId; ?>">
    </div>

    <div class="quiz-body">
      <form id="quizForm" method="post" action="javascript:void(0);">
        <?php foreach ($quizDetails['questions'] as $index => $question) { ?>
          <div class="quiz-question" id="qcard_<?php echo $question['question_id']; ?>"
               data-qid="<?php echo $question['question_id']; ?>"
               data-type="<?php echo $question['question_type']; ?>"
               data-correct="<?php echo htmlspecialchars($question['question_answers']); ?>">

            <div class="qz-qtop">
              <div class="qz-qnum"><?php echo $index + 1; ?></div>
              <div class="qz-qtitle">
                <div style="font-weight:800; color:#0f172a;"><?php echo html_entity_decode($question['question_title'], ENT_QUOTES); ?></div>
                <?php if (!empty($question['question_hint'])) { ?>
                  <div style="color:#64748b; font-size:12px; margin-top:4px;">💡 <?php echo htmlspecialchars($question['question_hint']); ?></div>
                <?php } ?>
              </div>
              <div class="qz-marks"><strong><?php echo Label::getLabel('LBL_MARKS'); ?>:</strong> <?php echo (int)$question['question_marks']; ?></div>
            </div>

            <?php if ($question['question_type'] === '2') { ?>
              <div class="quiz-options">
                <?php foreach ($question['randomized_options'] as $option) { ?>
                  <input type="checkbox" id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>"
                         name="answers[<?php echo $question['question_id']; ?>][]" value="<?php echo $option['id']; ?>">
                  <label class="qz-opt" for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>">
                    <span class="tick">✓</span>
                    <span><?php echo $option['text']; ?></span>
                  </label>
                <?php } ?>
              </div>

            <?php } elseif ($question['question_type'] === '1') { ?>
              <div class="quiz-options">
                <?php foreach ($question['randomized_options'] as $option) { ?>
                  <input type="radio" id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>"
                         name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $option['id']; ?>">
                  <label class="qz-opt" for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>">
                    <span class="tick">✓</span>
                    <span><?php echo $option['text']; ?></span>
                  </label>
                <?php } ?>
              </div>

            <?php } else { ?>
    <?php if ($quizDetails['is_math'] && $question['question_type'] === '3'): ?>
        <!-- Math subject: use math field -->
        <div class="quiz-math-wrapper" style="position: relative; margin: 8px 0;">
            <math-field 
                id="math_<?php echo $question['question_id']; ?>"
                style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc; min-height: 60px;"
                placeholder="<?php echo Label::getLabel('LBL_ENTER_YOUR_ANSWER'); ?>"
                virtual-keyboard-mode="onfocus"
                virtual-keyboard-layout="basic"
                virtual-keyboard-policy="auto"
            >
            </math-field>
            <input type="hidden" 
                   id="ans_<?php echo $question['question_id']; ?>"
                   name="answers[<?php echo $question['question_id']; ?>]"
                   value="">
            <button type="button" class="rwu-math-clear" 
                    style="position: absolute; right: 10px; top: 10px; border: none; background: #f3f4f6; border-radius: 6px; padding: 4px 10px; font-size: 12px; cursor: pointer; color: #6b7280;"
                    onclick="clearMathField('<?php echo $question['question_id']; ?>')">
                Clear
            </button>
        </div>
    <?php else: ?>
        <!-- Non-math subject: use textarea -->
        <textarea id="q_<?php echo $question['question_id']; ?>"
                  name="answers[<?php echo $question['question_id']; ?>]"
                  class="form-control" rows="3"
                  style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc;"
                  placeholder="<?php echo Label::getLabel('LBL_ENTER_YOUR_ANSWER'); ?>"></textarea>
    <?php endif; ?>
<?php } ?>
            
          </div>
        <?php } ?>

        <!-- bottom submit bar intentionally removed; submit via sidebar only -->
      </form>
    </div>
  </div>
</div>

<!-- ==== Modal + Confetti ==== -->
<canvas id="qzConfettiCanvas"></canvas>
<div id="resultBackdrop" class="result-modal-backdrop"></div>
<!-- <div id="resultModal" class="result-modal" role="dialog" aria-modal="true" aria-labelledby="resultTitle" aria-describedby="resultBody">
  <h3 id="resultTitle"><?php echo Label::getLabel('LBL_QUIZ_EVALUATION_SUMMARY'); ?></h3>
  <div id="resultBody">
    <div class="result-summary" id="resultSummary" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;"></div>
    <div class="result-list" id="resultList"></div>
    <div class="result-actions">
      <a id="findTutorBtn" class="btn-find-tutor" href="<?php echo CONF_WEBROOT_FRONT_URL; ?>teachers"><?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?></a>
      <button type="button" class="btn-close" id="closeResultBtn"><?php echo Label::getLabel('LBL_CLOSE'); ?></button>
    </div>
  </div>
</div> -->
<div id="resultModal" class="result-modal" role="dialog" aria-modal="true" aria-labelledby="resultTitle">
  <div class="result-head">
    <div>
      <div class="eyebrow">Quiz Results</div>
      <h3 id="resultTitle"><?php echo Label::getLabel('LBL_QUIZ_EVALUATION_SUMMARY'); ?></h3>
    </div>
    <button type="button" class="result-x" id="xCloseBtn" aria-label="Close">✕</button>
  </div>

  <div class="result-body">
    <div class="result-summary-wrap" id="resultSummaryWrap"></div>

    <div class="result-tools">
      <div style="display:flex; gap:8px; align-items:center;">
        <button type="button" class="qz-chip is-active" data-filter="all">All</button>
        <button type="button" class="qz-chip" data-filter="correct">Correct</button>
        <button type="button" class="qz-chip" data-filter="incorrect">Incorrect</button>
      </div>
      <div class="result-search">
        <span>🔎</span>
        <input type="text" id="resultSearch" placeholder="Search questions…">
      </div>
    </div>

    <div class="result-list" id="resultList"></div>

    <div class="result-actions">
      <a id="findTutorBtn" class="btn-find-tutor" href="<?php echo CONF_WEBROOT_FRONT_URL; ?>teachers"><?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?></a>
      <button type="button" class="btn-close" id="closeResultBtn"><?php echo Label::getLabel('LBL_CLOSE'); ?></button>
    </div>
  </div>
</div>


<script>
  
// ====== Timer ======
let qzTimerSecs = 8 * 60; // 8 minutes
let qzTick = null;
function formatMMSS(s){ const m=Math.floor(s/60),sec=s%60; return String(m).padStart(2,'0')+':'+String(sec).padStart(2,'0'); }
function startQuizTimer(){
  document.getElementById('quizTimeLeft').textContent = formatMMSS(qzTimerSecs);
  qzTick = setInterval(function(){
    qzTimerSecs--; document.getElementById('quizTimeLeft').textContent = formatMMSS(qzTimerSecs);
    if (qzTimerSecs <= 0){ clearInterval(qzTick); autoFillWrongAndSubmit(); }
  }, 1000);
}
const IS_MATH_QUIZ = <?php echo !empty($quizDetails['is_math']) ? 'true' : 'false'; ?>;

function initMathInputs() {
    if (!IS_MATH_QUIZ) return;
    if (typeof MathfieldElement === 'undefined') {
        console.warn('MathLive not loaded');
        return;
    }

    // Configure MathLive
    MathfieldElement.soundsDirectory = null;
    MathfieldElement.fontsDirectory = null;
    
    // Initialize all math fields
    document.querySelectorAll('.quiz-math-wrapper math-field').forEach((mathField) => {
        const wrapper = mathField.closest('.quiz-math-wrapper');
        const hiddenInput = wrapper.querySelector('input[type="hidden"]');
        const clearButton = wrapper.querySelector('.rwu-math-clear');
        
        if (!hiddenInput) return;
        
        // Configure the math field
        mathField.setOptions({
            virtualKeyboardMode: 'onfocus',
            smartMode: true,
            virtualKeyboardLayout: 'basic'
        });
        
        // Sync value to hidden input
        mathField.addEventListener('input', () => {
            hiddenInput.value = mathField.value || '';
            updateProgress();
        });
        
        // Initialize with any existing value
        if (hiddenInput.value) {
            mathField.value = hiddenInput.value;
        }
        
        // Clear button functionality
        if (clearButton) {
            clearButton.addEventListener('click', () => {
                mathField.value = '';
                hiddenInput.value = '';
                mathField.focus();
                updateProgress();
            });
        }
    });
}

// Helper function to clear math field
function clearMathField(questionId) {
    const mathField = document.getElementById('math_' + questionId);
    const hiddenInput = document.getElementById('ans_' + questionId);
    
    if (mathField) {
        mathField.value = '';
    }
    if (hiddenInput) {
        hiddenInput.value = '';
    }
    updateProgress();
}

// Update the isQuestionAnswered function to check math fields
function isQuestionAnswered($q) {
    const type = ($q.data('type') + '');

    // MCQ / Checkbox
    if (type === '1' || type === '2') {
        return $q.find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0;
    }

    // Short answer: textarea OR math hidden field
    const ta = $q.find('textarea');
    const hidden = $q.find('input[type="hidden"][id^="ans_"]');
    const mathField = $q.find('math-field');

    const taOk = ta.length && (ta.val() || '').trim() !== '';
    const hiddenOk = hidden.length && (hidden.val() || '').trim() !== '';
    const mathFieldOk = mathField.length && (mathField[0].value || '').trim() !== '';

    return taOk || hiddenOk || mathFieldOk;
}
// ====== Progress + Navigator ======
// function isQuestionAnswered($q){
//   const type = ($q.data('type') + '');

//   // MCQ / Checkbox
//   if (type === '1' || type === '2') {
//     return $q.find('input[type="radio"]:checked, input[type="checkbox"]:checked').length > 0;
//   }

//   // Short answer: textarea OR math hidden field
//   const ta = $q.find('textarea');
//   const hidden = $q.find('input[type="hidden"][id^="ans_"]');

//   const taOk = ta.length && (ta.val() || '').trim() !== '';
//   const hiddenOk = hidden.length && (hidden.val() || '').trim() !== '';

//   return taOk || hiddenOk;
// }

function updateProgress(){
  const $questions = $('.quiz-question');
  const total = $questions.length;

  let answered = 0;

  $questions.each(function(i){
    const $q = $(this);
    const ok = isQuestionAnswered($q);
    if (ok) answered++;

    // mark dot by question index
    $('.qz-dot').eq(i).toggleClass('answered', ok);
  });

  const pct = Math.round((answered / Math.max(1,total)) * 100);
  $('#qzAnsCount').text(answered);
  $('#qzPercent').text(pct + '%');
  $('#qzProgFill').css('width', pct + '%');
}

// scroll to card
$('#qzNav').on('click', '.qz-dot', function(){
  const id = $(this).data('target');
  const el = document.getElementById(id);
  if (el){ el.scrollIntoView({behavior:'smooth', block:'start'}); $('.qz-dot').removeClass('active'); $(this).addClass('active'); }
});
// track answers
$(document).on('change keyup', '.quiz-question input, .quiz-question textarea', function(){ updateProgress(); });

// ====== Auto-fill on timeout ======
function autoFillWrongAndSubmit(){
  $('.quiz-question').each(function(){
    const type = $(this).data('type') + '';
    const correct = (($(this).data('correct') || '') + '').split(',').map(s => s.trim());
    if (type === '1' || type === '2'){
      const opts = $(this).find('input[type="radio"], input[type="checkbox"]');
      let picked=false;
      opts.each(function(){
        const val=$(this).val();
        if (correct.indexOf(val)===-1 && !picked){ $(this).prop('checked', true); picked=true; }
        else { if (type==='2'){} else { $(this).prop('checked', false); } }
      });
      if (!picked && opts.length){ $(opts[0]).prop('checked', true); }
    } else {
      const ta=$(this).find('textarea'); if (ta.length){ ta.val(''); }
    }
  });
  submitLectureQuiz(true);
}

// ====== Submit ======
function submitLectureQuiz(silent=false) {
  if (!silent){
    let allAnswered = true;
   $('#quizForm .quiz-question').each(function() {
  if (!isQuestionAnswered($(this))) {
    allAnswered = false;
  }
});

    if (!allAnswered) { alert('<?php echo Label::getLabel('LBL_PLEASE_ANSWER_ALL_QUESTIONS'); ?>'); return; }
  }
  $('.qz-btn-primary').prop('disabled', true).text('Submitting...');
  clearInterval(qzTick);

  const quizId=$('#quiz_id').val(), quizPass=$('#quiz_pass_percentage').val(), courseId=$('#courseId').val(), lectureId=$('#lectureId').val(), progressId=$('#progressId').val();
  const form=$('#quizForm');
  form.append('<input type="hidden" name="quiz_id" value="'+quizId+'">');
  form.append('<input type="hidden" name="quiz_pass_percentage" value="'+quizPass+'">');
  form.append('<input type="hidden" name="course_id" value="'+courseId+'">');
  form.append('<input type="hidden" name="lecture_id" value="'+lectureId+'">');
  form.append('<input type="hidden" name="progress_id" value="'+progressId+'">');

  $.ajax({
    url: '<?php echo MyUtility::makeUrl('Tutorials', 'submitLectureQuiz'); ?>',
    method:'POST', data: form.serialize(), dataType:'json',
   success:function(response){
  if (response.status === "success" && response.data){
    const r = response.data;
    if (r.passed){
      showConfettiWin();
      buildAndShowResultModal(r, true);

      // >>> new: reflect pass in the left sidebar immediately
      markLectureCoveredInSidebar($('#lectureId').val());
    } else {
      buildAndShowResultModal(r, false);
    }
  } else {
    alert("Error: " + (response.message || 'Unknown error'));
    $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
  }
},

    error:function(){
      alert('<?php echo Label::getLabel('LBL_ERROR_SUBMITTING_QUIZ'); ?>');
      $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
    }
  });
}

// ====== Result modal & confetti ======
function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

/**
 * buildAndShowResultModal
 * @param {*} r backend response data (score, totalMarks, percentage, autoCheckedQuestions, etc.)
 * @param {boolean} isPass whether passed
 */
// function buildAndShowResultModal(r, isPass){
//   const s=$('#resultSummary').empty();

//   // Chips
//   s.append($('<span class="qz-pill">').text('Score: '+(r.score||0)+' / '+(r.totalMarks||0)));
//   s.append($('<span class="qz-pill">').text('Percentage: '+(Number(r.percentage||0).toFixed(2))+'%'));

//   const statusChip = $('<span class="qz-pill">')
//     .addClass(isPass ? 'qz-status-pass' : 'qz-status-fail')
//     .text(isPass ? 'Status: Passed' : 'Status: Failed');
//   s.append(statusChip);

//   // Detailed list
//   const list=$('#resultList').empty(), items=r.autoCheckedQuestions||{}; let idx=1;
//   Object.keys(items).forEach(function(qid){
//     const it=items[qid];
//     const badge=(it.status==='correct')?'<span class="badge-correct">correct</span>':'<span class="badge-incorrect">incorrect</span>';
//     list.append($(`
//       <div class="result-item">
//         <div class="q">${idx}. ${esc(it.question_title)||'Question'} ${badge}</div>
//         <div class="meta">Marks Awarded: ${it.marks||0}</div>
//         <div><strong>Submitted Answer:</strong></div>
//         <div class="smallmono">${Array.isArray(it.submitted_answer)?esc(it.submitted_answer.join(', ')):esc(it.submitted_answer)}</div>
//         <div style="margin-top:8px;"><strong>Correct Option(s):</strong></div>
//         <div class="smallmono">${esc(it.correctanswer)}</div>
//         ${it.explanation?`<div style="margin-top:8px;"><strong>Explanation:</strong></div><div class="smallmono">${esc(it.explanation)}</div>`:''}
//       </div>`));
//     idx++;
//   });

//   $('#resultBackdrop').show(); $('#resultModal').show();

//   // Close -> go back to lecture (keeps your previous flow under user control)
//   $('#closeResultBtn').off('click').on('click', function(){
//     $('#resultBackdrop').hide(); $('#resultModal').hide();
//     loadLecture($('#lectureId').val());
//   });

//   // Enable submit again (in case they want to see UI active after modal)
//   $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
// }
function esc(s){ return String(s ?? '').replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[m])); }

function buildAndShowResultModal(r, isPass){
  const score = Number(r.score || 0);
  const total = Number(r.totalMarks || 0) || 0;
  const pct = Math.round(Number(r.percentage || (total ? (score/total*100) : 0)));

  const items = r.autoCheckedQuestions || {};
  const keys = Object.keys(items);

  let correctCount = 0, incorrectCount = 0;

  // Summary header
  const wrap = $('#resultSummaryWrap').empty();

  const passPct = Number($('#quiz_pass_percentage').val() || 0) || 50;

  wrap.append(`
    <div class="qz-scoreCard">
      <div class="qz-ring" style="--p:${pct}">
        <div class="qz-ring__inner">${pct}%<small>score</small></div>
      </div>
      <div>
        <div style="font-weight:900;color:#0f172a;font-size:16px;">${score} / ${total}</div>
       
      </div>
    </div>

    <div class="qz-summaryGrid">
      <div class="qz-kpi"><div class="t">Score</div><div class="v">${score} / ${total}</div></div>
      <div class="qz-kpi"><div class="t">Percentage</div><div class="v">${pct}%</div></div>
      <div class="qz-kpi"><div class="t">Correct</div><div class="v" id="kpiCorrect">–</div></div>
      <div class="qz-kpi"><div class="t">Incorrect</div><div class="v" id="kpiIncorrect">–</div></div>
    </div>
  `);

  // Build accordion list
  const list = $('#resultList').empty();

  keys.forEach(function(qid, idx){
    const it = items[qid] || {};
    const status = (it.status || '').toLowerCase();
    const isCorrect = status === 'correct';
    if (isCorrect) correctCount++; else incorrectCount++;

    const submitted = Array.isArray(it.submitted_answer) ? it.submitted_answer.join(', ') : (it.submitted_answer ?? '');
    const correctAns = it.correctanswer ?? '';
    const marks = Number(it.marks || 0);

    list.append(`
      <div class="qz-qa" data-status="${isCorrect ? 'correct' : 'incorrect'}" data-text="${esc(it.question_title || '')}">
        <button type="button" class="qz-qa__head" aria-expanded="false">
          <div class="qz-qa__left">
            <div class="qz-qa__num">${idx + 1}</div>
            <div>
              <div class="qz-qa__title">${esc(it.question_title || 'Question')}</div>
              <div class="qz-qa__meta">Marks awarded: <strong>${marks}</strong></div>
            </div>
          </div>
          <div class="qz-badge ${isCorrect ? 'correct' : 'incorrect'}">${isCorrect ? 'Correct' : 'Incorrect'}</div>
        </button>

        <div class="qz-qa__body">
          <div class="qz-cols">
            <div class="qz-box">
              <h6>Submitted Answer</h6>
              <div class="qz-mono">${esc(submitted)}</div>
            </div>
            <div class="qz-box">
              <h6>Correct Answer</h6>
              <div class="qz-mono">${esc(correctAns)}</div>
            </div>
          </div>
          ${it.explanation ? `
            <div class="qz-box" style="margin-top:10px;">
              <h6>Explanation</h6>
              <div class="qz-mono">${esc(it.explanation)}</div>
            </div>` : ``}
        </div>
      </div>
    `);
  });

  $('#kpiCorrect').text(String(correctCount));
  $('#kpiIncorrect').text(String(incorrectCount));

  // Show modal
  $('#resultBackdrop').show();
  // $('#resultModal').show();
$('#resultModal').css('display','flex');

  // Accordion toggle
  $('#resultList').off('click').on('click', '.qz-qa__head', function(){
    const $wrap = $(this).closest('.qz-qa');
    const $body = $wrap.find('.qz-qa__body');
    const expanded = $(this).attr('aria-expanded') === 'true';
    $(this).attr('aria-expanded', expanded ? 'false' : 'true');
    $body.slideToggle(160);
  });

  // Filters
  $('.qz-chip').off('click').on('click', function(){
    $('.qz-chip').removeClass('is-active'); $(this).addClass('is-active');
    const f = $(this).data('filter');
    $('#resultList .qz-qa').each(function(){
      const s = $(this).data('status');
      $(this).toggle(f === 'all' || s === f);
    });
  });

  // Search
  $('#resultSearch').off('input').on('input', function(){
    const q = (this.value || '').toLowerCase().trim();
    $('#resultList .qz-qa').each(function(){
      const txt = ($(this).data('text') || '').toLowerCase();
      $(this).toggle(!q || txt.includes(q));
    });
  });

  // Close handlers
  function closeIt(){
    $('#resultBackdrop').hide();
    $('#resultModal').hide();
    loadLecture($('#lectureId').val());
  }

  $('#closeResultBtn').off('click').on('click', closeIt);
  $('#xCloseBtn').off('click').on('click', closeIt);
  $('#resultBackdrop').off('click').on('click', closeIt);

  // ESC closes
  document.addEventListener('keydown', function onEsc(e){
    if (e.key === 'Escape'){
      document.removeEventListener('keydown', onEsc);
      closeIt();
    }
  });

  // re-enable submit button label
  $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
}


function showConfettiWin(){
  const canvas=document.getElementById('qzConfettiCanvas'), ctx=canvas.getContext('2d');
  canvas.style.display='block'; canvas.width=window.innerWidth; canvas.height=window.innerHeight;
  const N=150, parts=[];
  for(let i=0;i<N;i++){
    parts.push({x:Math.random()*canvas.width,y:-20-Math.random()*200,r:2+Math.random()*4,v:2+Math.random()*3,a:Math.random()*Math.PI*2});
  }
  const start=performance.now();
  (function tick(t){
    ctx.clearRect(0,0,canvas.width,canvas.height);
    parts.forEach(p=>{
      p.y+=p.v; p.x+=Math.sin((t/200)+p.a)*1.5;
      ctx.beginPath(); ctx.arc(p.x,p.y,p.r,0,Math.PI*2);
      ctx.fillStyle='hsl('+((p.y/5)%360)+',90%,60%)'; ctx.fill();
    });
    if (t-start<2000) requestAnimationFrame(tick); else canvas.style.display='none';
  })(start);
}

$(function(){ 
    startQuizTimer(); 
    updateProgress(); 
    
    // Initialize math fields if needed
    if (IS_MATH_QUIZ) {
        // Wait a moment for MathLive to load if needed
        if (typeof MathfieldElement !== 'undefined') {
            initMathInputs();
        } else {
            // Try again after a short delay
            setTimeout(function() {
                if (typeof MathfieldElement !== 'undefined') {
                    initMathInputs();
                } else {
                    console.warn('MathLive failed to load');
                }
            }, 500);
        }
    }
});
</script>

<?php } else { ?>
  <div class="message-display no-skin">
    <div class="message-display__media">
      <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
    </div>
    <h4><?php echo stripslashes(Label::getLabel("LBL_NO_QUIZ_AVAILABLE")); ?></h4>
  </div>
<?php } ?>
