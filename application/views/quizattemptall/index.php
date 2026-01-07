<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>
<style>
/* ====== Shared helpers ====== */
.hidden { display:none; }

/* ====== Layout (sidebar LEFT + widened question panel) ====== */
.qz-shell {
  display: grid;
  grid-template-columns: 320px minmax(0, 1fr);
  gap: 22px;
  max-width: 1400px;
  margin: 10px auto 28px;
  padding: 0 8px;
}
/* ===== Math field styling (MathLive) ===== */
.rwu-math-wrapper{
  margin-top: 10px;
  border: 1px solid #e2e8f0;
  border-radius: 12px;
  padding: 10px;
  background: #f9fafb;
}
.rwu-math-wrapper math-field{
  width: 100%;
  min-height: 56px;
  padding: 10px 12px;
  border-radius: 10px;
  border: 1px solid #e2e8f0;
  background: #fff;
  font-size: 16px;
  display:block;
}
.rwu-math-clear{
  margin-top: 8px;
  padding: 6px 12px;
  border-radius: 10px;
  border: 1px solid #e5e7eb;
  background:#f3f4f6;
  cursor:pointer;
  font-weight:700;
  font-size:12px;
}
.rwu-math-raw{
  margin-top: 8px;
  padding: 6px;
  background: #f8f9fa;
  border-radius: 8px;
  font-size: 11px;
  color: #6b7280;
  max-height: 60px;
  overflow-y: auto;
}

/* Mobile / tablet: stack, sidebar full-width, no sticky */
@media (max-width: 1100px){
  .qz-shell{
    grid-template-columns: 1fr;
    align-items: flex-start;
  }

  .qz-side{
    position: static;        /* 🔴 turn off sticky */
    top: auto;
    height: auto;            /* let content define height */
    width: 100%;             /* full width on mobile */
    margin-bottom: 16px;     /* some spacing before questions */
  }

  .quiz-container{
    max-width: 100%;
  }
}

/* Optional tighter tweaks for very small screens */
@media (max-width: 600px){
  .qz-side{
    padding: 12px;
  }
  .qz-nav{
    grid-template-columns: repeat(auto-fill, minmax(28px, 1fr));
  }
}
.qz-side h5{ margin:0 0 6px; font-size:15px; color:#0f172a; }

.qz-timer {
  display:flex; align-items:center; justify-content:space-between;
  background:#fff7ed; border:1px solid #fde7c7; color:#8a4b10;
  border-radius:10px; padding:10px 12px; font-weight:700;
}

.qz-progress-wrap{ margin:12px 0 8px; }
.qz-progress-bar{
  height:10px; background:#f1f5f9; border-radius:999px; overflow:hidden;
}
.qz-progress-bar > span{
  display:block; height:100%; width:0%;
  background:linear-gradient(90deg,#14A0A3,#0ea5e9);
  transition:width .25s ease;
}
.qz-progress-meta{
  display:flex; justify-content:space-between;
  color:#64748b; font-size:12px; margin-top:6px;
}

/* Navigator dots */
.qz-nav{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(34px,1fr));
  gap:8px; margin-top:12px;
}
.qz-dot{
  display:flex; align-items:center; justify-content:center;
  height:34px; border-radius:10px;
  border:1px solid #e2e8f0; background:#f8fafc; color:#0f172a;
  font-weight:700; cursor:pointer; transition:all .15s ease;
}
.qz-dot:hover{ border-color:#cbd5e1; background:#eef2f7; }
.qz-dot.answered{ background:#e6fffb; color:#065f5b; border-color:#99f6e4; }
.qz-dot.active{ background:#14A0A3; color:#fff; border-color:#14A0A3; }

/* Sidebar actions */
.qz-actions-side{ display:flex; gap:8px; margin-top:12px; }
.qz-btn-primary, .qz-btn-secondary{
  padding:11px 14px; border-radius:10px; border:1px solid transparent;
  font-weight:700; cursor:pointer; font-size:14px;
}
.qz-btn-primary{
    background: #2DADFF; color:#fff;
  box-shadow:0 6px 16px rgba(20,160,163,.22);
}
.qz-btn-primary:hover{ background:#118e90; }
.qz-btn-secondary{
  background:#f3f4f6; color:#111827; border-color:#e5e7eb;
}

/* ====== Questions panel ====== */
.quiz-container{
  background:#ffffff;
  border:1px solid #e8edf3;
  border-radius:16px;
  padding:16px 16px 6px;
  width: 100%;
  max-width: 800px;
  box-shadow:0 10px 24px rgba(17,24,39,.04);
}
.quiz-header{
  padding:2px 6px 10px;
  border-radius:8px 8px 0 0;
  display:flex; justify-content:space-between; align-items:center;
}
.quiz-header-title{
  font-size:17px; font-weight:700; color:#0f172a;
}
.quiz-header-sub{
  font-size:13px; color:#64748b;
}
.quiz-body{ padding:6px 4px 12px; }

/* Individual question card */
.quiz-question{
  background:#ffffff;
  border:1px solid #e7edf4;
  border-radius:14px;
  padding:16px;
  margin:0 0 14px;
  position:relative;
  transition:box-shadow .15s ease, border-color .15s ease;
}
.quiz-question:hover{
  box-shadow:0 8px 18px rgba(17,24,39,.05);
  border-color:#d8e3ef;
}
.qz-qtop{
  display:flex; align-items:flex-start; gap:12px;
}
.qz-qnum{
  background:#eef7f7; color:#0f6c6e;
  font-weight:800; min-width:34px; height:34px;
  border-radius:10px;
  display:flex; align-items:center; justify-content:center;
}
.qz-qtitle{ flex:1; }
.qz-qtitle-main{ font-weight:800; color:#0f172a; }
.qz-qtitle-hint{
  color:#64748b; font-size:12px; margin-top:4px;
}
.qz-marks{ color:#475569; font-size:12px; }

/* Option tiles (MCQ) */
.quiz-options{ display:grid; gap:8px; margin-top:10px; }
.quiz-options input[type="radio"],
.quiz-options input[type="checkbox"]{
  display:none;
}
.qz-opt{
  background:#f8fafc;
  border:1px solid #e2e8f0;
  border-radius:12px;
  padding:10px 12px;
  cursor:pointer;
  transition:all .15s ease;
  display:flex; gap:10px; align-items:flex-start;
}
.qz-opt:hover{
  background:#f1f5f9; border-color:#d7dee9;
}
.qz-opt .tick{
  width:20px; height:20px; min-width:20px;
  border-radius:6px; background:#e2f6f7;
  display:flex; align-items:center; justify-content:center;
  color:#0f6c6e; font-weight:900; font-size:13px;
}
.qz-opt span:last-child{ flex:1; }

/* checked state */
.quiz-options input[type="radio"]:checked + label.qz-opt,
.quiz-options input[type="checkbox"]:checked + label.qz-opt{
      background: #2DADFF; color:#fff; border-color:#14A0A3;
  box-shadow:0 8px 18px rgba(20,160,163,.25);
}

/* Textarea style */
.quiz-question textarea{
  width:100%; padding:10px 12px;
  border-radius:10px; border:1px solid #e2e8f0;
  background:#f8fafc; font-size:14px;
}

/* Question image */
.quiz-media img{
  max-width: 60%;
  height: auto;
  border-radius: 12px;
  display: block;
  margin: 12px auto 4px;
}
</style>

<section class="section section--gray section--listing">
  <div class="page-listing__body">
    <div class="course-results">
      <?php if (count($courses)) { ?>
        <div class="course-card">
          <div class="course-grid">

            <div class="qz-shell">
              <!-- ========== LEFT SIDEBAR ========== -->
              <aside class="qz-side">
                <h5><?php echo Label::getLabel('LBL_TIME_REMAINING'); ?></h5>
                <div class="qz-timer">
                  <span>⏱️</span>
                  <!-- keep id="timer" so existing JS keeps working -->
                  <span id="timer">10:00</span>
                </div>

                <div class="qz-progress-wrap">
                  <div class="qz-progress-bar">
                    <span id="qzProgFill"></span>
                  </div>
                  <div class="qz-progress-meta">
                    <div>
                      <span id="qzAnsCount">0</span>
                      / <?php echo count($questionData ?? []); ?> answered
                    </div>
                    <div id="qzPercent">0%</div>
                  </div>
                </div>

                <h5 style="margin-top:10px;"><?php echo Label::getLabel('LBL_QUESTIONS'); ?></h5>
                <!-- Filled dynamically from JS after questions load -->
                <div id="qzNav" class="qz-nav"></div>

                <div class="qz-actions-side">
                  <button
                    type="button"
                    class="qz-btn-secondary"
                    onclick="window.scrollTo({top:0, behavior:'smooth'});">
                    Top
                  </button>
                  <button
                    type="button"
                    id="submit-btn"
                    class="qz-btn-primary">
                    <?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>
                  </button>
                </div>
              </aside>

              <!-- ========== QUESTIONS PANEL ========== -->
              <div class="quiz-container">
                <div class="quiz-header">
                  <div class="quiz-header-title">
                    <?php echo ucfirst($_SESSION['subtopicName']); ?>
                  </div>
                  <div class="quiz-header-sub">
                    <?php echo Label::getLabel('LBL_VISITOR_QUIZ'); ?>
                  </div>
                </div>

                <div class="quiz-body">
                  <!-- All questions will be rendered here by JS -->
                  <div id="quiz-options"></div>
                </div>
                  <button
                    type="button"
                    id="submit-btn"
                     class="qz-btn-primary js-submit-quiz">
                    <?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>
                  </button>
              </div>
            </div>

          </div>
        </div>
      <?php } else { ?>
        <div class="page-listing__body">
          <div class="box -padding-30" style="margin-bottom: 30px;">
            <div class="message-display">
              <div class="message-display__icon">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                  <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
                </svg>
              </div>
              <h5><?php echo Label::getLabel('LBL_NO_QUIZ_FOUND!'); ?></h5>
            </div>
          </div>
        </div>
      <?php } ?>
    </div>

    <?php
    $checkoutForm->setFormTagAttribute('class', 'd-none');
    $checkoutForm->setFormTagAttribute('name', 'frmCheckout');
    $checkoutForm->setFormTagAttribute('id', 'frmCheckout');
    echo $checkoutForm->getFormHtml();
    ?>
  </div>
</section>

<script>
  var questions = <?php echo json_encode($questionData ?? []); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>

<script>
/* ------------------ BOOTSTRAP DATA FROM PHP ------------------ */
var questions = <?php echo json_encode($questionData ?? []); ?>;
let currentQuestion = 0;
const CONF_WEBROOT_FRONT_URL = <?= json_encode(CONF_WEBROOT_FRONT_URL); ?>;
const ENFORCE_SINGLE_CHOICE = true;
var userSessionId = parseInt("<?php echo (int)($_SESSION['subtopicId'] ?? 0); ?>", 10) || 0; // line changed jan 2 2026

/* ------------------ STATE ------------------ */
let timerDuration = 10 * 60;
let timerInterval = null;
let userAnswers = {};
let isMathSubject = false;
window.RWU_IS_MATH_SUBJECT = false; // RWUMath reads this in other pages too


/* ------------------ UTILITIES ------------------ */
function toArray(val) { return Array.isArray(val) ? val : (val ? [val] : []); }

function normalizeQuestions(rows) {
  return (rows || []).map(function (q) {
    var opts = Array.isArray(q.options) ? q.options.filter(Boolean) : [];

    var correct = [];
    if (Array.isArray(q.answer)) {
      correct = q.answer.map(function (s){ return String(s).trim().toUpperCase(); }).filter(Boolean);
    } else if (typeof q.answer === 'string') {
      correct = q.answer.split(',').map(function (s){ return s.trim().toUpperCase(); }).filter(Boolean);
    }

    return {
      id      : q.id,
      text    : q.text || '',
      hint    : q.hint || '',
      image   : q.image || '',
      options : opts,
      correct : correct,
      _rawType: String(q.type || '').trim().toLowerCase()
    };
  });
}

function deriveType(q) {
  const t = q._rawType;
  const hasOpts = Array.isArray(q.options) && q.options.length > 0;

  if (ENFORCE_SINGLE_CHOICE && hasOpts) return 'single';

  if (t.includes('multiple')) return 'multiple';
  if (t.includes('single') || t.includes('mcq')) return 'single';
  if (t.includes('short') || t.includes('story') || t.includes('text')) return 'text';

  if (hasOpts) {
    return (Array.isArray(q.correct) && q.correct.length > 1) ? 'multiple' : 'single';
  }
  return 'text';
}

function resolveUrl(u) {
  if (!u) return '';
  if (/^https?:\/\//i.test(u)) return u;
  const base = CONF_WEBROOT_FRONT_URL.replace(/\/+$/, '');
  return (u.charAt(0) === '/') ? (base + u) : (base + '/' + u);
}

/* ------------------ TIMER ------------------ */
function startTimer() {
  if (timerInterval !== null) return;

  timerInterval = setInterval(function () {
    if (timerDuration <= 0) {
      clearInterval(timerInterval);
      alert("Time is up! Submitting quiz...");
      exitQuiz();
      return;
    }
    var minutes = Math.floor(timerDuration / 60);
    var seconds = timerDuration % 60;
    var el = document.getElementById("timer");
    if (el) el.innerText = "Time Left: " + minutes + ":" + (seconds < 10 ? "0" : "") + seconds;
    timerDuration--;
  }, 1000);
}

/* ------------------ FETCH QUESTIONS ------------------ */
function fetchQuestions() {
  $.ajax({
    url: fcom.makeUrl('Quizattempt', 'getQuestions'),
    type: "POST",
    data: { pageno: 1, subtopicid: userSessionId },
    dataType: "json",
 success: function (response) {
  if (response && response.success) {
    questions = normalizeQuestions(response.data);

    isMathSubject = !!(response.meta && response.meta.isMathSubject);
    window.RWU_IS_MATH_SUBJECT = isMathSubject;

    loadAllQuestions();
    startTimer();

    // ✅ only init math when subject is math
    if (isMathSubject) {
      setTimeout(() => window.RWUMath?.initFields?.(), 0);
    }
  } else {
    alert(response?.message || response?.msg || "No questions found.");
    window.history.back();
  }
}

    ,
    error: function (xhr, status, error) {
      console.error("Error fetching questions:", error);
      alert("Failed to load questions. Please try again.");
      window.history.back();
    }
  });
}

/* ------------------ PROGRESS + NAV ------------------ */
function updateProgress() {
  const cards = document.querySelectorAll('.quiz-question');
  const total = cards.length;
  let answered = 0;

  cards.forEach(function(card) {
    let has = false;
    const inputs = card.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    inputs.forEach(function(inp){ if (inp.checked) has = true; });
//     const ta = card.querySelector('textarea');
// if (ta && ta.value.trim() !== '') has = true;

// // ✅ NEW: math hidden input
// const mh = card.querySelector('#math_hidden_' + (card.dataset.qindex || ''));
// if (mh && mh.value.trim() !== '') has = true;

// // safer (works even if dataset missing)
// const anyHidden = card.querySelector('input[type="hidden"][id^="math_hidden_"]');
// if (anyHidden && anyHidden.value.trim() !== '') has = true;
const idx = parseInt(card.dataset.qindex || '-1', 10);
if (idx >= 0) {
  if (isTextAnswered(idx)) has = true;
}


    if (has) answered++;
  });

  const pct = total ? Math.round((answered / total) * 100) : 0;
  const countEl = document.getElementById('qzAnsCount');
  const pctEl   = document.getElementById('qzPercent');
  const fillEl  = document.getElementById('qzProgFill');

  if (countEl) countEl.textContent = answered;
  if (pctEl)   pctEl.textContent   = pct + '%';
  if (fillEl)  fillEl.style.width  = pct + '%';

  const dots = document.querySelectorAll('.qz-dot');
  dots.forEach(function(dot, i){
    if (i < answered) dot.classList.add('answered');
    else dot.classList.remove('answered');
  });
}

/* ------------------ RENDERING ------------------ */
function canUseMathLive() {
  return !!(isMathSubject && window.customElements && customElements.get('math-field') && window.RWUMath);
}

function renderTextarea(parent, index) {
  if (canUseMathLive()) {
    const wrapper = document.createElement("div");
    wrapper.className = "rwu-math-wrapper";
    wrapper.setAttribute("data-math-field", "true");
    wrapper.setAttribute("data-keyboard", "basic");
    wrapper.setAttribute("data-keyboard-mode", "onfocus");

    // hidden input used by RWUMath to store latex (we'll read it for answers)
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.id = `math_hidden_${index}`;
    hidden.addEventListener('input', updateProgress);

    // clear button (RWUMath also creates one sometimes; this keeps UX consistent)
    const clearBtn = document.createElement("button");
    clearBtn.type = "button";
    clearBtn.className = "rwu-math-clear";
    clearBtn.textContent = "Clear";
    clearBtn.addEventListener("click", () => {
      // RWUMath attaches math-field inside wrapper; clear if present
      const mf = wrapper.querySelector('math-field');
      if (mf) mf.value = '';
      hidden.value = '';
      hidden.dispatchEvent(new Event('input', { bubbles: true }));
    });

    const raw = document.createElement("div");
    raw.className = "rwu-math-raw";
    raw.textContent = "";

    wrapper.appendChild(hidden);
    // wrapper.appendChild(clearBtn);
    wrapper.appendChild(raw);

    parent.appendChild(wrapper);

    // ✅ Ensure RWUMath upgrades this wrapper into <math-field>
    setTimeout(() => window.RWUMath?.initFields?.(), 0);

    return;
  }

  // fallback: normal textarea for non-math subjects
  const textarea = document.createElement("textarea");
  textarea.name = `question-${index}`;
  textarea.placeholder = "Type your answer here...";
  textarea.style.height = "120px";
  textarea.addEventListener('input', updateProgress);
  parent.appendChild(textarea);
}
function getTextAnswerValue(index) {
  // Find the question card
  const card = document.querySelector(`.quiz-question[data-qindex="${index}"]`);

  // 1) If MathLive field exists, read latex reliably
  if (card) {
    const mf = card.querySelector('math-field');
    if (mf) {
      const v = (typeof mf.getValue === 'function')
        ? (mf.getValue('latex') || '')
        : (mf.value || '');
      if (String(v).trim() !== '') return String(v).trim();
    }
  }

  // 2) Hidden latex fallback (if RWUMath syncs it)
  const hidden = document.getElementById(`math_hidden_${index}`);
  if (hidden && String(hidden.value || '').trim() !== '') {
    return String(hidden.value || '').trim();
  }

  // 3) Textarea inside THIS card (most reliable for non-math text)
  if (card) {
    const taLocal = card.querySelector('textarea');
    if (taLocal && String(taLocal.value || '').trim() !== '') {
      return String(taLocal.value || '').trim();
    }
  }

  // 4) Last resort: global textarea by name (your original approach)
  const ta = document.querySelector(`textarea[name="question-${index}"]`);
  return ta ? String(ta.value || '').trim() : '';
}

function isTextAnswered(index) {
  return getTextAnswerValue(index) !== '';
}


function loadAllQuestions() {
  const container = document.getElementById("quiz-options");
  if (!container) return;
  container.innerHTML = "";

  questions.forEach((q, index) => {
    const wrap = document.createElement("div");
    wrap.className = "quiz-question";
    wrap.id = "qcard_" + q.id;
    wrap.dataset.qindex = String(index);


    // top row
    const top = document.createElement("div");
    top.className = "qz-qtop";

    const num = document.createElement("div");
    num.className = "qz-qnum";
    num.textContent = index + 1;

    const titleWrap = document.createElement("div");
    titleWrap.className = "qz-qtitle";

    const titleMain = document.createElement("div");
    titleMain.className = "qz-qtitle-main";
    titleMain.textContent = q.text || '';

    titleWrap.appendChild(titleMain);

    if (q.hint) {
      const hint = document.createElement("div");
      hint.className = "qz-qtitle-hint";
      hint.textContent = "💡 " + q.hint;
      titleWrap.appendChild(hint);
    }

    const marks = document.createElement("div");
    marks.className = "qz-marks";
    marks.textContent = ""; // no marks for visitors (or plug in if you have)

    top.appendChild(num);
    top.appendChild(titleWrap);
    top.appendChild(marks);
    wrap.appendChild(top);

    // image (optional)
    const mediaDiv = document.createElement("div");
    mediaDiv.className = "quiz-media";
    if (q.image && String(q.image).trim().length) {
      const img = document.createElement("img");
      img.src = resolveUrl(String(q.image).trim());
      img.alt = q.text ? ("Image: " + q.text.substring(0, 80)) : "Question image";
      img.loading = "lazy";
      mediaDiv.appendChild(img);
    }
    wrap.appendChild(mediaDiv);

    // decide type
    const finalType = deriveType(q);

    if (finalType === 'single' || finalType === 'multiple') {
      const optsDiv = document.createElement("div");
      optsDiv.className = "quiz-options";

      if (!q.options || q.options.length === 0) {
        renderTextarea(optsDiv, index);
      } else {
        q.options.forEach((opt, i) => {
          const letter = String.fromCharCode(65 + i);
          const id = `q${index}_${letter}`;

          const input = document.createElement("input");
          input.type = (finalType === 'multiple') ? "checkbox" : "radio";
          input.name = `question-${index}`;
          input.id = id;
          input.value = letter;
          input.setAttribute("data-index", String(index));

          const label = document.createElement("label");
          label.className = "qz-opt";
          label.setAttribute("for", id);

          const tick = document.createElement("span");
          tick.className = "tick";
          tick.textContent = "✓";

          const textSpan = document.createElement("span");
          textSpan.textContent = `${opt}`;

          label.appendChild(tick);
          label.appendChild(textSpan);

          optsDiv.appendChild(input);
          optsDiv.appendChild(label);
        });
      }

      wrap.appendChild(optsDiv);
    } else {
      renderTextarea(wrap, index);
    }

    container.appendChild(wrap);
  });

  /* build nav dots */
  const nav = document.getElementById('qzNav');
  if (nav) {
    nav.innerHTML = '';
    questions.forEach(function(q, i){
      const dot = document.createElement('div');
      dot.className = 'qz-dot';
      dot.dataset.target = 'qcard_' + q.id;
      dot.textContent = i + 1;
      nav.appendChild(dot);
    });
  }

  updateProgress();
}

/* ------------------ VALIDATION + ANSWER BUILDING ------------------ */
function getCardTextValue(card, index) {
  // math-field (best)
  const mf = card.querySelector('math-field');
  if (mf) {
    const v = (typeof mf.getValue === 'function') ? mf.getValue('latex') : mf.value;
    if (String(v || '').trim() !== '') return String(v || '').trim();
  }

  // hidden latex fallback
  const hidden = card.querySelector(`#math_hidden_${index}`) || document.getElementById(`math_hidden_${index}`);
  if (hidden && String(hidden.value || '').trim() !== '') return String(hidden.value || '').trim();

  // textarea fallback
  const ta = card.querySelector('textarea');
  if (ta && String(ta.value || '').trim() !== '') return String(ta.value || '').trim();

  return '';
}

function validateAllAnswers() {
  const cards = document.querySelectorAll('.quiz-question');
  for (const card of cards) {
    const idx = parseInt(card.dataset.qindex || '-1', 10);
    if (idx < 0) return false;

    // MCQ?
    const inputs = card.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    if (inputs.length > 0) {
      const anyChecked = Array.from(inputs).some(i => i.checked);
      if (!anyChecked) return false;
      continue;
    }

    // Text/Math
    const v = getCardTextValue(card, idx);
    if (v === '') return false;
  }
  return true;
}


// function validateAllAnswers() {
//   for (let i = 0; i < questions.length; i++) {
//     const q = questions[i];
//     const finalType = deriveType(q);
//     if (finalType === 'single') {
//       if (!document.querySelector(`input[name="question-${i}"]:checked`)) return false;
//     } else if (finalType === 'multiple') {
//       if (document.querySelectorAll(`input[name="question-${i}"]:checked`).length === 0) return false;
//     } else {
//    if (canUseMathLive()) {
//   const hidden = document.getElementById(`math_hidden_${i}`);
//   if (!hidden || hidden.value.trim() === "") return false;
// } else {
//   const input = document.querySelector(`textarea[name="question-${i}"]`);
//   if (!input || input.value.trim() === "") return false;
// }

//     }
//   }
//   return true;
// }

function buildUserAnswers() {
  userAnswers = {};

  const cards = document.querySelectorAll('.quiz-question');
  cards.forEach(card => {
    const idx = parseInt(card.dataset.qindex || '-1', 10);
    if (idx < 0) return;

    const q = questions[idx];
    let answer = null;

    // MCQ
    const inputs = card.querySelectorAll('input[type="radio"], input[type="checkbox"]');
    if (inputs.length > 0) {
      const checked = Array.from(inputs).filter(i => i.checked).map(i => i.value);

      // if radio -> single value
      const isRadio = Array.from(inputs).some(i => i.type === 'radio');
      answer = isRadio ? (checked[0] || null) : checked;

    } else {
      // Text/Math
      answer = getCardTextValue(card, idx);
    }

    userAnswers[idx] = { questionId: q.id, answer };
  });
}


/* ------------------ EVENTS ------------------ */
/* live tracking for MCQs */
document.addEventListener("DOMContentLoaded", function () {
  const quizOptions = document.getElementById("quiz-options");
  if (!quizOptions) return;

  quizOptions.addEventListener("change", function (event) {
    const target = event.target;
    if (!target || target.tagName !== 'INPUT') return;

    const qIndex = parseInt(target.getAttribute("data-index"), 10);
    if (Number.isNaN(qIndex)) return;

    const q = questions[qIndex];
    const finalType = deriveType(q);
    const name = `question-${qIndex}`;

    if (finalType === 'multiple') {
      const selected = Array.from(document.querySelectorAll(`input[name="${name}"]:checked`))
        .map(el => el.value);
      userAnswers[qIndex] = { questionId: q.id, answer: selected };
    } else {
      userAnswers[qIndex] = { questionId: q.id, answer: target.value };
    }

    updateProgress();
  });
});

/* nav dot click scroll */
document.addEventListener('click', function(e){
  const dot = e.target.closest('.qz-dot');
  if (!dot) return;
  const id = dot.getAttribute('data-target');
  const el = document.getElementById(id);
  if (el) {
    el.scrollIntoView({behavior:'smooth', block:'start'});
    document.querySelectorAll('.qz-dot').forEach(d => d.classList.remove('active'));
    dot.classList.add('active');
  }
});

/* submit */
document.getElementById("submit-btn").addEventListener("click", function () {
  if (!validateAllAnswers()) {
    alert("❗ Please answer all questions before submitting.");
    return;
  }

  buildUserAnswers();
  clearInterval(timerInterval);

  const btn = document.getElementById("submit-btn");
  btn.disabled = true;
  btn.innerText = "Processing...";

  $.ajax({
    url: fcom.makeUrl('Quizattempt', 'submitAnswers'),
    type: "POST",
    data: {
      answers: JSON.stringify(userAnswers),
      subtopicid: userSessionId
    },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        const url = fcom.makeUrl('quizr') + '?attempt=' + response.attemptid;
        window.location.href = url;   // ✅ dedicated result page
      } else {
        Swal.fire("Error", "Submission failed. Please try again.", "error");
        btn.disabled = false;
        btn.innerText = "<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>";
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error, xhr?.responseText);
      Swal.fire("Error", "Something went wrong.", "error");
      btn.disabled = false;
      btn.innerText = "<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>";
    }
  });
});

/* ------------------ EXIT + INIT ------------------ */
function exitQuiz() {
  clearInterval(timerInterval);
  window.history.back();
}

(function init() {
  if (Array.isArray(questions) && questions.length) {
    questions = normalizeQuestions(questions);
    loadAllQuestions();
  }
  startTimer();
  fetchQuestions(); // refresh from backend
})();
function handleSubmitClick () {
  if (!validateAllAnswers()) {
    alert("❗ Please answer all questions before submitting.");
    return;
  }

  buildUserAnswers();
  clearInterval(timerInterval);

  const allBtns = document.querySelectorAll('.js-submit-quiz');
  allBtns.forEach(btn => {
    btn.disabled = true;
    btn.innerText = "Processing...";
  });

  $.ajax({
    url: fcom.makeUrl('Quizattempt', 'submitAnswers'),
    type: "POST",
    data: {
      answers: JSON.stringify(userAnswers),
      subtopicid: userSessionId
    },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        const url = fcom.makeUrl('quizr') + '?attempt=' + response.attemptid;
        window.location.href = url;
      } else {
        Swal.fire("Error", "Submission failed. Please try again.", "error");
        allBtns.forEach(btn => {
          btn.disabled = false;
          btn.innerText = "<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>";
        });
      }
    },
    error: function () {
      Swal.fire("Error", "Something went wrong.", "error");
      allBtns.forEach(btn => {
        btn.disabled = false;
        btn.innerText = "<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>";
      });
    }
  });
}

document.querySelectorAll('.js-submit-quiz')
  .forEach(btn => btn.addEventListener('click', handleSubmitClick));

</script>
