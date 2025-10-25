<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php if ($quizDetails) { ?>
<style>
/* ====== Layout (sidebar LEFT + widened question panel) ====== */
.qz-shell {
  display: grid;
  grid-template-columns: 320px minmax(0, 1fr); /* sidebar left, fixed width */
  gap: 22px;
  max-width: 1400px;            /* ~25% wider than 1120px */
  margin: 10px auto 28px;
  padding: 0 8px;
}
@media (max-width: 1100px){
  .qz-shell{ grid-template-columns: 1fr; }
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
#qzConfettiCanvas { position:fixed; inset:0; pointer-events:none; display:none; z-index:10020; }
.result-modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.5); display:none; z-index:10010; }
.result-modal {
  position:fixed; top:50%; left:50%; transform:translate(-50%,-50%);
  width:min(1100px,96%); max-height:90vh; background:#fff; border-radius:14px; padding:24px;
  box-shadow:0 20px 50px rgba(0,0,0,.25); display:none; z-index:10011;
}
#resultTitle { font-size:26px; margin:0 0 14px; }
#resultList { max-height:60vh; overflow:auto; border:1px solid #eef2f7; border-radius:10px; padding:14px; background:#fafafa; }
.result-summary .qz-pill{ background:#eef2ff; color:#1e3a8a; }
.btn-find-tutor,.btn-close{ border-radius:10px; font-weight:600; }
.result-actions{ display:flex; gap:10px; justify-content:flex-end; margin-top:16px; }
.result-item{ border-bottom:1px dashed #e5e7eb; padding:12px 0; } .result-item:last-child{ border-bottom:0; }
.badge-correct{ background:#16a34a; color:#fff; padding:2px 8px; border-radius:999px; font-size:12px; }
.badge-incorrect{ background:#F5411F; color:#fff; padding:2px 8px; border-radius:999px; font-size:12px; }
.btn-find-tutor{ background:#F5411F; color:#fff; border:none; padding:10px 18px; }
.btn-close{ background:#0EA5E9; color:#fff; border:none; padding:10px 18px; border-radius:6px; }
.smallmono{ white-space:pre-wrap; background:#fafafa; border:1px solid #eee; padding:8px; border-radius:6px; font-family:ui-monospace,Menlo,monospace; }

/* Tiny helpers */
.qz-pill{ background:#f1f5f9; color:#0f172a; border:1px solid #e2e8f0; border-radius:10px; padding:8px 12px; font-weight:600; font-size:13px; }

/* Status chips */
.qz-status-pass{ background:#dcfce7; color:#166534; border-color:#bbf7d0; }
.qz-status-fail{ background:#fee2e2; color:#991b1b; border-color:#fecaca; }
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
              <textarea id="q_<?php echo $question['question_id']; ?>"
                        name="answers[<?php echo $question['question_id']; ?>]"
                        class="form-control" rows="3"
                        style="width:100%; padding:10px 12px; border-radius:10px; border:1px solid #e2e8f0; background:#f8fafc;"
                        placeholder="<?php echo Label::getLabel('LBL_ENTER_YOUR_ANSWER'); ?>"></textarea>
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
<div id="resultModal" class="result-modal" role="dialog" aria-modal="true" aria-labelledby="resultTitle" aria-describedby="resultBody">
  <h3 id="resultTitle"><?php echo Label::getLabel('LBL_QUIZ_EVALUATION_SUMMARY'); ?></h3>
  <div id="resultBody">
    <div class="result-summary" id="resultSummary" style="display:flex; gap:12px; flex-wrap:wrap; margin-bottom:12px;"></div>
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

// ====== Progress + Navigator ======
function updateProgress(){
  const total = $('.quiz-question').length;
  let answered = 0;
  $('.quiz-question').each(function(){
    const has = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function(){ return $(this).val().trim() !== ''; }).length;
    if (has) answered++;
  });
  const pct = Math.round((answered/Math.max(1,total))*100);
  $('#qzAnsCount').text(answered); $('#qzPercent').text(pct+'%'); $('#qzProgFill').css('width', pct+'%');

  // mark dots (simple heuristic)
  $('.qz-dot').each(function(i){
    $(this).toggleClass('answered', i < answered);
  });
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
      const selected = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function(){ return $(this).val().trim() !== ''; });
      if (!selected.length) { allAnswered = false; }
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
function buildAndShowResultModal(r, isPass){
  const s=$('#resultSummary').empty();

  // Chips
  s.append($('<span class="qz-pill">').text('Score: '+(r.score||0)+' / '+(r.totalMarks||0)));
  s.append($('<span class="qz-pill">').text('Percentage: '+(Number(r.percentage||0).toFixed(2))+'%'));

  const statusChip = $('<span class="qz-pill">')
    .addClass(isPass ? 'qz-status-pass' : 'qz-status-fail')
    .text(isPass ? 'Status: Passed' : 'Status: Failed');
  s.append(statusChip);

  // Detailed list
  const list=$('#resultList').empty(), items=r.autoCheckedQuestions||{}; let idx=1;
  Object.keys(items).forEach(function(qid){
    const it=items[qid];
    const badge=(it.status==='correct')?'<span class="badge-correct">correct</span>':'<span class="badge-incorrect">incorrect</span>';
    list.append($(`
      <div class="result-item">
        <div class="q">${idx}. ${esc(it.question_title)||'Question'} ${badge}</div>
        <div class="meta">Marks Awarded: ${it.marks||0}</div>
        <div><strong>Submitted Answer:</strong></div>
        <div class="smallmono">${Array.isArray(it.submitted_answer)?esc(it.submitted_answer.join(', ')):esc(it.submitted_answer)}</div>
        <div style="margin-top:8px;"><strong>Correct Option(s):</strong></div>
        <div class="smallmono">${esc(it.correctanswer)}</div>
        ${it.explanation?`<div style="margin-top:8px;"><strong>Explanation:</strong></div><div class="smallmono">${esc(it.explanation)}</div>`:''}
      </div>`));
    idx++;
  });

  $('#resultBackdrop').show(); $('#resultModal').show();

  // Close -> go back to lecture (keeps your previous flow under user control)
  $('#closeResultBtn').off('click').on('click', function(){
    $('#resultBackdrop').hide(); $('#resultModal').hide();
    loadLecture($('#lectureId').val());
  });

  // Enable submit again (in case they want to see UI active after modal)
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

$(function(){ startQuizTimer(); updateProgress(); });
</script>

<?php } else { ?>
  <div class="message-display no-skin">
    <div class="message-display__media">
      <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
    </div>
    <h4><?php echo stripslashes(Label::getLabel("LBL_NO_QUIZ_AVAILABLE")); ?></h4>
  </div>
<?php } ?>
