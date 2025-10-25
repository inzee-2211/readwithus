<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($quizDetails) {
  //  echo '<pre>';print_r($quizDetails);die;
?>
<!-- ====== EXAM LAYOUT: Sidebar LEFT + Wide Question Panel (matches quiz-new) ====== -->
<style>
/* ====== Layout (sidebar LEFT + widened question panel) ====== */
.qz-shell {
  display: grid;
  grid-template-columns: 320px minmax(0, 1fr); /* sidebar left, fixed width */
  gap: 22px;
  max-width: 1400px;            /* ~25% wider than older 1120px */
  margin: 10px auto 28px;
  padding: 0 8px;
}
@media (max-width: 1100px){
  .qz-shell{ grid-template-columns: 1fr; }
}

/* ====== Sidebar (sticky, fixed width, full height viewport minus header) ====== */
.qz-side {
  position: sticky;
  top: 84px;                          /* align under your header */
  height: calc(100vh - 84px);         /* fixed height relative to viewport */
  overflow: auto;                     /* allow internal scroll if content overflows */
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
.qz-btn-primary{ background:#F5411F; color:#fff; box-shadow:0 6px 16px rgba(245,65,31,.22); }
.qz-btn-primary:hover{ background:#d73d1c; }
.qz-btn-secondary{ background:#f3f4f6; color:#111827; border-color:#e5e7eb; }

/* ====== Questions panel ====== */
.quiz-container{
  background:#ffffff; border:1px solid #e8edf3; border-radius:16px; padding:16px 16px 6px;
  width: 800px;
  box-shadow:0 10px 24px rgba(17,24,39,.04);
}
/* .quiz-header{
  background:#14A0A3; color:#fff; padding:15px; border-radius:8px; margin-bottom:10px;
} */
.quiz-header h4{ margin:0 0 6px; }
.quiz-header h5{ margin:0; font-weight:500; opacity:.95; }

.quiz-body{ padding:6px 4px 2px; }

/* Question card */
.quiz-question{
  background:#ffffff; border:1px solid #e7edf4; border-radius:14px;
  padding:16px; margin:0 0 14px; position:relative;
  transition:box-shadow .15s ease, border-color .15s ease;
}
.quiz-question:hover{ box-shadow:0 8px 18px rgba(17,24,39,.05); border-color:#d8e3ef; }

/* Option tiles */
.quiz-options{ display:grid; gap:8px; margin-top:10px; }
.quiz-options input[type="radio"], .quiz-options input[type="checkbox"]{ display:none; }
.quiz-options label{
  display:block; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:10px 12px; cursor:pointer;
  transition:all .15s ease;
}
.quiz-options label:hover{ background:#f1f5f9; border-color:#d7dee9; }
.quiz-options input[type="radio"]:checked + label,
.quiz-options input[type="checkbox"]:checked + label{
  background:#14A0A3; color:#fff; border-color:#14A0A3; box-shadow:0 8px 18px rgba(20,160,163,.25);
}

/* Remove any bottom submit bars if existed */
.qz-submitbar{ display:none !important; }

/* Exam popup (existing) */
#quizPopup { display:none; position:fixed; inset:0; align-items:center; justify-content:center; z-index:10000; }
.quiz-popup-overlay { position:fixed; inset:0; background:rgba(0,0,0,.7); z-index:999; }
.quiz-popup-content {
  position:relative; z-index:1000;
  width:80%; max-width:800px; max-height:80%;
  background:#fff; border-radius:10px; padding:20px; overflow-y:auto;
  box-shadow:0 4px 10px rgba(0,0,0,.25);
}
.quiz-popup-content h3 { margin:0 0 15px; font-size:1.5em; text-align:center; }
.quiz-summary-content { text-align:left; font-size:1em; line-height:1.6; color:#333; }
.quiz-summary-content pre{
  background:#f7f7f7; padding:10px; border-radius:5px; overflow-x:auto; white-space:pre-wrap; word-wrap:break-word; font-size:.9em;
}
.btn-close-popup{ margin-top:12px; padding:10px 20px; background:#0EA5E9; color:#fff; border:none; border-radius:5px; cursor:pointer; width:100%; font-size:1em; }
.btn-close-popup:hover{ background:#0c95d5; }
.btn-tutor{
  display:block; width:100%; margin-top:12px; padding:10px 20px;
  background:#F5411F; color:#fff; border:none; border-radius:5px; text-align:center; font-size:1em; cursor:pointer; text-decoration:none;
}
.btn-tutor:hover{ background:#d73d1c; }

/* Question image wrapper */
.avtar1 {
  display:flex; justify-content:center; align-items:center;
  height:200px; width:200px; margin:0 auto 22px; border-radius:12px; overflow:hidden;
}
.avtar1 img{ width:100%; max-height:200px; border-radius:6px; object-fit:cover; }
</style>

<!-- ====== Popup Summary (existing evaluation view preserved) ====== -->
<div id="quizPopup" style="display: none;">
  <div class="quiz-popup-overlay" onclick="closePopup()"></div>
  <div class="quiz-popup-content">
    <h3>Exam Evaluation Summary</h3>
    <div id="quizSummary" class="quiz-summary-content"></div>
    <a class="btn-tutor" href="<?php echo MyUtility::makeUrl('teachers', '', [], CONF_WEBROOT_FRONT_URL); ?>">
      Find a Tutor
    </a>
    <button class="btn-close-popup" onclick="closePopup()">Close</button>
  </div>
</div>

<?php
  if(isset($quizDetails['quiz_duration']) && $quizDetails['quiz_duration']>0){
      $quiz_duration = $quizDetails['quiz_duration']*60;  // seconds
  } else {
      $quiz_duration = 0;
  }
?>

<div class="qz-shell">
  <!-- ===== Sidebar (LEFT) ===== -->
  <aside class="qz-side">
    <h5><?php echo Label::getLabel('LBL_TIME_REMAINING'); ?></h5>
    <div class="qz-timer"><span>⏱️</span><span id="timeRemaining">00:00</span></div>

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
      <button class="qz-btn-primary" onclick="submitQuiz()"><?php echo Label::getLabel('LBL_SUBMIT_EXAM'); ?></button>
    </div>
  </aside>

  <!-- ===== Questions Panel ===== -->
  <div class="quiz-container">
    <div class="quiz-header">
      <!-- <h4><?php echo htmlspecialchars($quizDetails['quiz_title']); ?></h4>
      <h5><?php echo html_entity_decode($quizDetails['quiz_description']); ?></h5> -->

      <input type="hidden" id="quiz_id" value="<?php echo $quizDetails['quiz_id']; ?>">
      <input type="hidden" id="quiz_pass_percentage" value="<?php echo $quizDetails['quiz_pass_percentage']; ?>">
      <input type="hidden" id="quiz_teacher_id" value="<?php echo $quizDetails['quiz_user_id']; ?>">
      <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">
      <input type="hidden" id="lectureId" value="<?php echo $lectureId; ?>">
    </div>

    <div class="quiz-body">
      <form id="quizForm" method="post" action="javascript:void(0);">
        <?php foreach ($quizDetails['questions'] as $index => $question) { ?>
          <div class="quiz-question" id="qcard_<?php echo $question['question_id']; ?>">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px;">
              <p style="flex:1; margin:0;"><strong><?php echo ($index + 1) . '. ' . html_entity_decode($question['question_title'], ENT_QUOTES); ?></strong></p>
              <p style="flex-shrink:0; text-align:right; margin:0;">
                <strong><?php echo Label::getLabel('LBL_MARKS'); ?>:</strong> <?php echo htmlspecialchars($question['question_marks']); ?>
              </p>
            </div>

            <?php if (!empty($question['question_math_equation']) && trim($question['question_math_equation']) !== '') { ?>
              <div style="margin-top:6px;">
                <?php echo '$$'.$question['question_math_equation'].'$$'; ?>
              </div>
            <?php } ?>

            <?php
              $imageUrl = MyUtility::makeUrl('Image', 'show', [Afile::TYPE_LESSON_QUESTIONS_FILE, $question['question_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL);
            ?>
            <?php if (isset($question['question_image']) && $question['question_image'] == 1): ?>
              <div class="avtar1 avtar--centered">
                <img src="<?php echo $imageUrl; ?>" alt="">
              </div>
            <?php endif; ?>

            <?php if ($question['question_type'] === '2') { ?>
              <div class="quiz-options">
                <?php for ($i = 1; $i <= 4; $i++) {
                  $optionKey = "question_option_$i";
                  if (!empty($question[$optionKey])) { ?>
                    <div>
                      <input type="checkbox" id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $i; ?>" name="answers[<?php echo $question['question_id']; ?>][]" value="<?php echo $i; ?>">
                      <label for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $i; ?>"><?php echo $question[$optionKey]; ?></label>
                    </div>
                  <?php }
                } ?>
              </div>

            <?php } elseif ($question['question_type'] === '1') { ?>
              <div class="quiz-options">
                <?php for ($i = 1; $i <= 4; $i++) {
                  $optionKey = "question_option_$i";
                  if (!empty($question[$optionKey])) { ?>
                    <div>
                      <input type="radio" id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $i; ?>" name="answers[<?php echo $question['question_id']; ?>]" value="<?php echo $i; ?>">
                      <label for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $i; ?>"><?php echo $question[$optionKey]; ?></label>
                    </div>
                  <?php }
                } ?>
              </div>

            <?php } elseif ($question['question_type'] === '3') { ?>
              <textarea id="q_<?php echo $question['question_id']; ?>" name="answers[<?php echo $question['question_id']; ?>]" class="form-control" rows="3" placeholder="<?php echo Label::getLabel('LBL_ENTER_YOUR_ANSWER'); ?>"></textarea>
            <?php } ?>
          </div>
        <?php } ?>
        <!-- No bottom submit bar; submission via sidebar button -->
      </form>
    </div>
  </div>
</div>

<!-- ====== MathJax for equations ====== -->
<script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>

<script>
// ====== Timer (keeps your existing logic) ======
var quiz_duration = "<?php echo $quiz_duration; ?>";
let timeRemaining = Number(quiz_duration) || 0;  // seconds
const timerDisplay = document.getElementById('timeRemaining');
let timerInterval;

function startTimer() {
  if (!timerDisplay) return;
  timerInterval = setInterval(() => {
    if (timeRemaining <= 0) {
      clearInterval(timerInterval);
      alert('<?php echo Label::getLabel('LBL_TIME_UP_PLEASE_CONDUCT_THE_EXAM_AGAIN'); ?>');
      location.reload();
    } else {
      const minutes = Math.floor(timeRemaining / 60);
      const seconds = timeRemaining % 60;
      timerDisplay.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
      timeRemaining--;
    }
  }, 1000);
}
function pauseTimer(){ clearInterval(timerInterval); }

// ====== Progress + Navigator (non-invasive UI helpers) ======
function updateProgress(){
  const total = $('.quiz-question').length;
  let answered = 0;
  $('.quiz-question').each(function(){
    const has = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea')
      .filter(function(){ return $(this).val().trim() !== ''; }).length;
    if (has) answered++;
  });
  const pct = Math.round((answered/Math.max(1,total))*100);
  $('#qzAnsCount').text(answered);
  $('#qzPercent').text(pct+'%');
  $('#qzProgFill').css('width', pct+'%');

  // Heuristic marking on dots
  $('.qz-dot').each(function(i){
    $(this).toggleClass('answered', i < answered);
  });
}

// Scroll to card
$('#qzNav').on('click', '.qz-dot', function(){
  const id = $(this).data('target');
  const el = document.getElementById(id);
  if (el){
    el.scrollIntoView({behavior:'smooth', block:'start'});
    $('.qz-dot').removeClass('active'); $(this).addClass('active');
  }
});
// Track answers to refresh progress
$(document).on('change keyup', '.quiz-question input, .quiz-question textarea', function(){ updateProgress(); });

// ====== Submit (unchanged backend flow) ======
function submitQuiz() {
  let allAnswered = true;
  const unansweredQuestions = [];
  $('#quizForm .quiz-question').each(function () {
    const selectedAnswer = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function () {
      return $(this).val().trim() !== '';
    });
    if (!selectedAnswer.length) {
      allAnswered = false;
      // store any name to highlight (just for UX, does not affect backend)
      const anyName = $(this).find('input[type="radio"], input[type="checkbox"], textarea').attr('name');
      unansweredQuestions.push(anyName);
    }
  });

  if (!allAnswered) {
    alert('Please answer all the questions before submitting the Exam.');
    unansweredQuestions.forEach((questionName) => {
      if (!questionName) return;
      const questionElement = $(`[name="${questionName}"]`).closest('.quiz-question');
      questionElement.css('border', '2px solid red');
      setTimeout(() => questionElement.css('border', ''), 5000);
    });
    return;
  }

  $('.qz-btn-primary').prop('disabled', true).text('Submitting...');
  pauseTimer();

  const quizId = document.getElementById('quiz_id').value;
  const quizPass = document.getElementById('quiz_pass_percentage').value;
  const quiz_teacher_id = document.getElementById('quiz_teacher_id').value;
  const courseId = document.getElementById('courseId').value;
  const lectureId = document.getElementById('lectureId').value;

  const form = $('#quizForm');
  form.append('<input type="hidden" name="quiz_id" value="' + quizId + '">');
  form.append('<input type="hidden" name="quiz_pass_percentage" value="' + quizPass + '">');
  form.append('<input type="hidden" name="quiz_teacher_id" value="' + quiz_teacher_id + '">');
  form.append('<input type="hidden" name="courseId" value="' + courseId + '">');
  form.append('<input type="hidden" name="lectureId" value="' + lectureId + '">');

  const data = form.serialize();

  $.ajax({
    url: '<?php echo MyUtility::makeUrl('Tutorials', 'submitQuiz'); ?>',
    method: 'POST',
    data: data,
    dataType: 'json',
    success: function (response) {
      if (response.status === "success") {
        showQuizPopup(response);
      } else {
        alert("Error: " + response.message);
        $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_EXAM'); ?>');
      }
    },
    error: function () {
      alert('<?php echo Label::getLabel('LBL_ERROR_SUBMITTING_EXAM'); ?>');
      $('.qz-btn-primary').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_EXAM'); ?>');
    }
  });
}

// ====== Popup (unchanged) ======
function closePopup() {
  location.reload();
  document.getElementById('quizPopup').style.display = 'none';
}
function showQuizPopup(response) {
  if (response.Quizdata && response.Quizdata.autoCheckedQuestions) {
    const autoCheckedQuestions = response.Quizdata.autoCheckedQuestions;

    let popupContent = `
      <p><strong>Score:</strong> ${response.Quizdata.score} / ${response.Quizdata.totalMarks}</p>
      <hr>
    `;
    for (const questionId in autoCheckedQuestions) {
      const question = autoCheckedQuestions[questionId];
      popupContent += `
        <div class="quizcontent">
          <p><strong>Question:</strong> ${question.question_title || "N/A"}</p>
          <p><strong>Status:</strong> ${question.status}</p>
          <p><strong>Marks Awarded:</strong> ${question.marks}</p>
          <p><strong>Submitted Answer:</strong> ${question.submitted_answer}</p>
          <p><strong>Explanations / Correct Option:</strong></p>
          <pre>${question.correctanswer}</pre>
          <hr>
        </div>
      `;
    }
    document.getElementById('quizSummary').innerHTML = popupContent;
    document.getElementById('quizPopup').style.display = 'flex';
  }
}

// ====== Init ======
$(document).ready(function(){
  if (timeRemaining > 0) {
    // Initialize display immediately so it doesn't flash 00:00 for a second
    const minutes = Math.floor(timeRemaining / 60);
    const seconds = timeRemaining % 60;
    timerDisplay.textContent = `${String(minutes).padStart(2,'0')}:${String(seconds).padStart(2,'0')}`;
    startTimer();
  }
  updateProgress();
});
</script>

<?php } else { ?>
  <div class="message-display no-skin">
    <div class="message-display__media">
      <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
    </div>
    <h4><?php echo stripslashes(Label::getLabel("LBL_NO_EXAM_AVAILABLE.")); ?></h4>
  </div>
<?php } ?>
