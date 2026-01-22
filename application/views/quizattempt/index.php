<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
?>

<style>
/* ====== Shared helpers ====== */
.hidden { display:none; }

/* ====== Layout (sidebar LEFT + question panel) ====== */
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

.qz-btn-primary{
    background: #2DADFF!important; color:#fff;
  box-shadow:0 6px 16px rgba(20,160,163,.22);
}
/* Mobile / tablet: stack, sidebar full-width, no sticky */
@media (max-width: 1100px){
  .qz-shell{
    grid-template-columns: 1fr;
    align-items: flex-start;
  }
  .qz-side{
    position: static;
    top: auto;
    height: auto;
    width: 100%;
    margin-bottom: 16px;
  }
  .quiz-container{
    max-width: 100%;
  }
}

@media (max-width: 600px){
  .qz-shell{
    padding: 0 10px;
  }
  .qz-side{
    padding: 12px;
  }
  .qz-nav{
    grid-template-columns: repeat(auto-fill, minmax(28px,1fr));
  }
}

/* ====== Sidebar ====== */
.qz-side {
  position: sticky;
  top: 84px;
  height: calc(60vh - 84px);
  overflow: auto;
  width: 320px;
  background: #ffffff;
  border: 1px solid #e8edf3;
  border-radius: 16px;
  padding: 18px;
  box-shadow: 0 10px 22px rgba(17,24,39,.05);
  z-index: 2; /* ensure it never goes behind content when sticky */
}
.qz-side h5{
  margin:0 0 6px;
  font-size:15px;
  color:#0f172a;
}

.qz-timer {
  display:flex;
  align-items:center;
  justify-content:space-between;
  background:#fff7ed;
  border:1px solid #fde7c7;
  color:#8a4b10;
  border-radius:10px;
  padding:10px 12px;
  font-weight:700;
}

.qz-progress-wrap{ margin:12px 0 8px; }
.qz-progress-bar{
  height:10px;
  background:#f1f5f9;
  border-radius:999px;
  overflow:hidden;
}
.qz-progress-bar > span{
  display:block;
  height:100%;
  width:0%;
  background:linear-gradient(90deg,#14A0A3,#0ea5e9);
  transition:width .25s ease;
}
.qz-progress-meta{
  display:flex;
  justify-content:space-between;
  color:#64748b;
  font-size:12px;
  margin-top:6px;
}

/* Navigator dots */
.qz-nav{
  display:grid;
  grid-template-columns:repeat(auto-fill,minmax(34px,1fr));
  gap:8px;
  margin-top:12px;
}
.qz-dot{
  display:flex;
  align-items:center;
  justify-content:center;
  height:34px;
  border-radius:10px;
  border:1px solid #e2e8f0;
  background:#f8fafc;
  color:#0f172a;
  font-weight:700;
  cursor:pointer;
  transition:all .15s ease;
}
.qz-dot:hover{
  border-color:#cbd5e1;
  background:#eef2f7;
}
.qz-dot.answered{
  background:#e6fffb;
  color:#065f5b;
  border-color:#99f6e4;
}
.qz-dot.active{
 background: #2DADFF!important;
  color:#fff;
  border-color:#14A0A3;
}

/* Sidebar actions */
.qz-actions-side{
  display:flex;
  gap:8px;
  margin-top:12px;
}
.qz-btn-primary,
.qz-btn-secondary{
  padding:11px 14px;
  border-radius:10px;
  border:1px solid transparent;
  font-weight:700;
  cursor:pointer;
  font-size:14px;
}
.qz-btn-primary{
  background:#14A0A3;
  color:#fff;
  box-shadow:0 6px 16px rgba(20,160,163,.22);
}
.qz-btn-primary:hover{ background:#118e90; }
.qz-btn-secondary{
  background:#f3f4f6;
  color:#111827;
  border-color:#e5e7eb;
}

/* ====== Questions panel ====== */
.quiz-container{
  background:#ffffff;
  border:1px solid #e8edf3;
  border-radius:16px;
  padding:16px 16px 12px;
  width:100%;
  max-width:800px;
  box-shadow:0 10px 24px rgba(17,24,39,.04);
  position: relative;
  z-index: 1;
}
.quiz-header{
  padding:2px 6px 10px;
  border-radius:8px 8px 0 0;
  display:flex;
  justify-content:space-between;
  align-items:center;
}
.quiz-header-title{
  font-size:17px;
  font-weight:700;
  color:#0f172a;
}
.quiz-header-sub{
  font-size:13px;
  color:#64748b;
}
.quiz-body{ padding:6px 4px 0; }

/* Single question card */
.quiz-question{
  background:#ffffff;
  border:1px solid #e7edf4;
  border-radius:14px;
  padding:16px;
  /* ✅ 10% wider than previous 600px, but responsive */
  width: 660px;
  max-width: 100%;
  margin:0 auto 14px;
  position:relative;
  transition:box-shadow .15s ease, border-color .15s ease;
}
.quiz-question:hover{
  box-shadow:0 8px 18px rgba(17,24,39,.05);
  border-color:#d8e3ef;
}

/* Top row (number + title + marks) */
.qz-qtop{
  display:flex;
  align-items:flex-start;
  gap:12px;
}
.qz-qnum{
  background:#eef7f7;
  color:#0f6c6e;
  font-weight:800;
  min-width:34px;
  height:34px;
  border-radius:10px;
  display:flex;
  align-items:center;
  justify-content:center;
}
.qz-qtitle{ flex:1; }
.qz-qtitle-main{
  font-weight:800;
  color:#0f172a;
}
.qz-qtitle-hint{
  color:#64748b;
  font-size:12px;
  margin-top:4px;
}
.qz-marks{
  color:#475569;
  font-size:12px;
}

/* Question media */
.quiz-media img{
  max-width:60%;
  height:auto;
  border-radius:12px;
  display:block;
  margin:12px auto 4px;
}

/* Option tiles */
.quiz-options{
  display:grid;
  gap:8px;
  margin-top:10px;
}
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
  display:flex;
  gap:10px;
  align-items:flex-start;
}
.qz-opt:hover{
  background:#f1f5f9;
  border-color:#d7dee9;
}
.qz-opt .tick{
  width:20px;
  height:20px;
  min-width:20px;
  border-radius:6px;
  background:#e2f6f7;
  display:flex;
  align-items:center;
  justify-content:center;
  color:#0f6c6e;
  font-weight:900;
  font-size:13px;
}
.qz-opt span:last-child{ flex:1; }

.quiz-options input[type="radio"]:checked + label.qz-opt,
.quiz-options input[type="checkbox"]:checked + label.qz-opt{
background: #2DADFF!important;  color:#fff;
  border-color:#14A0A3;
  box-shadow:0 8px 18px rgba(20,160,163,.25);
}

/* Textarea */
.quiz-question textarea{
  width:100%;
  padding:10px 12px;
  border-radius:10px;
  border:1px solid #e2e8f0;
  background:#f8fafc;
  font-size:14px;
  box-sizing:border-box;
}

/* Navigation buttons under card */
.quiz-navigation{
  margin-top:14px;
  display:flex;
  justify-content:space-between;
  gap:8px;
}

/* ✅ Mobile refinements: question full-width, no overlap, no weird spacing */
@media (max-width: 768px){
  .quiz-container{
    max-width: 100%;
    padding: 14px 10px 10px;
  }

  .quiz-header{
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .quiz-question{
    width: 100%;
    max-width: 100%;
    margin: 0 0 14px;
    padding: 14px;
  }

  .quiz-media img{
    max-width: 100%;
  }

  .quiz-navigation{
    flex-direction: row;
  }
}
/* ===== Mobile: sidebar fixed on top (30%), questions scroll below (70%) ===== */
@media (max-width: 768px){
  /* Shell just stacks, no extra grid layout needed */
  .qz-shell{
    display: block;
    max-width: 100%;
    margin: 0;
    padding: 0;
  }

  /* Sidebar: fixed at top, 30% of viewport height */
  .qz-side{
    position: fixed;
    top: 0;               /* if your main site header overlaps, change to e.g. 60px */
    left: 0;
    right: 0;
    width: 100%;
    height: 30vh;
    border-radius: 0 0 16px 16px;
    z-index: 1000;
    overflow-y: auto;
    margin-bottom: 0;
  }

  /* Question container sits below the fixed sidebar */
  .quiz-container{
    margin-top: 30vh;     /* same as qz-side height */
    height: 70vh;
    overflow-y: auto;
    max-width: 100%;
    border-radius: 16px 16px 0 0;
    padding: 14px 10px 10px;
  }

  /* Make question card fully responsive inside scroll area */
  .quiz-question{
    width: 100%;
    max-width: 90%;
    margin: 0 0 14px;
    padding: 14px;
  }

  .quiz-header{
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
  }

  .quiz-media img{
    max-width: 100%;
  }
}

</style>

<section class="section section--gray section--listing">
  <div class="page-listing__body">
    <div class="course-results">
      <?php if (count($courses)) { ?>
        <div class="course-card">
          <div class="course-grid">

            <div class="qz-shell">
              <!-- ===== Sidebar ===== -->
              <aside class="qz-side">
                <h5><?php echo Label::getLabel('LBL_TIME_REMAINING'); ?></h5>
                <div class="qz-timer">
                  <span>⏱️</span>
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
                <div id="qzNav" class="qz-nav"><!-- dots from JS --></div>

                <div class="qz-actions-side">
                  <button
                    type="button"
                    class="qz-btn-secondary"
                    onclick="window.scrollTo({top:0,behavior:'smooth'});">
                    Top
                  </button>
                  <!-- Submit is triggered via Next on last question, but keep for emergency if you want -->
                  <button
                    type="button"
                    class="qz-btn-primary"
                    onclick="submitQuiz();">
                    <?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>
                  </button>
                </div>
              </aside>

              <!-- ===== Question panel (single question at a time) ===== -->
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
                  <!-- Host card for current question -->
                  <div id="qcard_wrapper" class="quiz-question">
                    <div class="qz-qtop">
                      <div class="qz-qnum" id="qzQnum">1</div>
                      <div class="qz-qtitle">
                        <div class="qz-qtitle-main" id="question-text"></div>
                        <div class="qz-qtitle-hint" id="qzHintTop" style="display:none;"></div>
                      </div>
                      <div class="qz-marks" id="qzMarks"></div>
                    </div>

                    <div id="question-media" class="quiz-media"></div>

                    <!-- Options / textarea rendered here -->
                    <div id="quiz-options" class="quiz-options"></div>
                  </div>

                  <div class="quiz-navigation">
                    <button
                      id="prev-btn"
                      class="qz-btn-secondary"
                      disabled>
                      Previous
                    </button>
                    <button
                      id="next-btn"
                      class="qz-btn-primary">
                      Next
                    </button>
                  </div>
                </div>
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
/* ------------------ CONFIG / STATE ------------------ */
var questions = <?php echo json_encode($questionData ?? []); ?>;
let currentQuestion = 0;
const CONF_WEBROOT_FRONT_URL = <?= json_encode(CONF_WEBROOT_FRONT_URL); ?>;
let timerDuration = 10 * 60;
let timerInterval = null;
const ENFORCE_SINGLE_CHOICE = true;

let userAnswers = {};   // { [index]: { questionId, answer } }
var userSessionId = "<?php echo $_SESSION['subtopicId']; ?>";
let isMathSubject = false;
window.RWU_IS_MATH_SUBJECT = false;

function canUseMathLive() {
  return !!(
    isMathSubject &&
    window.customElements &&
    customElements.get('math-field') &&
    window.RWUMath
  );
}

function renderTextAnswer(container, index, savedValue) {
  container.innerHTML = "";

  if (canUseMathLive()) {
    const wrapper = document.createElement("div");
    wrapper.className = "rwu-math-wrapper";
    wrapper.setAttribute("data-math-field", "true");
    wrapper.setAttribute("data-keyboard", "basic");
    wrapper.setAttribute("data-keyboard-mode", "onfocus");

    // hidden latex store
    const hidden = document.createElement("input");
    hidden.type = "hidden";
    hidden.id = `math_hidden_${index}`;
    hidden.value = (typeof savedValue === "string") ? savedValue : "";
    hidden.addEventListener("input", updateProgress);

    const clearBtn = document.createElement("button");
    clearBtn.type = "button";
    clearBtn.className = "rwu-math-clear";
    clearBtn.textContent = "Clear";
    clearBtn.addEventListener("click", () => {
      const mf = wrapper.querySelector("math-field");
      if (mf) mf.value = "";
      hidden.value = "";
      hidden.dispatchEvent(new Event("input", { bubbles: true }));
    });

    const raw = document.createElement("div");
    raw.className = "rwu-math-raw";
    raw.textContent = "";

    wrapper.appendChild(hidden);
    // wrapper.appendChild(clearBtn);
    wrapper.appendChild(raw);
    container.appendChild(wrapper);

    // upgrade wrapper -> math-field
    setTimeout(() => window.RWUMath?.initFields?.(), 0);
    return;
  }

  // fallback normal textarea
  const textarea = document.createElement("textarea");
  textarea.id = "text-answer";
  textarea.placeholder = "Type your answer here...";
  textarea.value = (typeof savedValue === "string") ? savedValue : "";
  textarea.addEventListener("input", function () {
    userAnswers[index] = { questionId: questions[index].id, answer: this.value };
    updateProgress();
  });
  container.appendChild(textarea);
}

/* ------------------ UTILITIES ------------------ */
function normalizeQuestions(rows) {
  return (rows || []).map(function (q) {
    var opts = Array.isArray(q.options) ? q.options.filter(Boolean) : [];
    var ans  = Array.isArray(q.answer)
      ? q.answer
      : (typeof q.answer === 'string' && q.answer.trim().length
          ? q.answer.split(',').map(function (s){ return s.trim().toUpperCase(); })
          : []);
    return {
      id     : q.id,
      text   : q.text,
      hint   : q.hint || '',
      options: opts,
      correct: ans,
      image  : q.image || '',
      type   : (q.type || '').toLowerCase()
    };
  });
}

function hasOptions(q) {
  return Array.isArray(q.options) && q.options.length > 0;
}

function isMultiAnswer(q) {
  if (ENFORCE_SINGLE_CHOICE && hasOptions(q)) return false;
  if (q.type === 'multiple-choice') return true;
  if (Array.isArray(q.correct) && q.correct.length > 1) return true;
  return false;
}

function resolveUrl(u) {
  if (!u) return '';
  if (/^https?:\/\//i.test(u)) return u;
  const base = CONF_WEBROOT_FRONT_URL.replace(/\/+$/, '');
  if (u.charAt(0) === '/') return base + u;
  return base + '/' + u;
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

/* ------------------ PROGRESS + NAV ------------------ */
function buildNavDots() {
  const nav = document.getElementById('qzNav');
  if (!nav) return;
  nav.innerHTML = '';
  questions.forEach(function(q, i){
    const dot = document.createElement('div');
    dot.className = 'qz-dot';
    dot.dataset.index = String(i);
    dot.textContent = i + 1;
    nav.appendChild(dot);
  });
  highlightActiveDot();
}

function highlightActiveDot() {
  const dots = document.querySelectorAll('.qz-dot');
  dots.forEach(function(dot){
    dot.classList.remove('active');
  });
  const active = document.querySelector('.qz-dot[data-index="' + currentQuestion + '"]');
  if (active) active.classList.add('active');
}

function updateProgress() {
  const total = questions.length;
  let answered = 0;

  for (let i = 0; i < total; i++) {
    const entry = userAnswers[i];
    if (!entry) continue;
    const val = entry.answer;
    if (Array.isArray(val) && val.length) answered++;
    else if (typeof val === 'string' && val.trim() !== '') answered++;
    else if (val !== null && val !== undefined && val !== '') answered++;
  }

  const pct = total ? Math.round((answered / total) * 100) : 0;
  const countEl = document.getElementById('qzAnsCount');
  const pctEl   = document.getElementById('qzPercent');
  const fillEl  = document.getElementById('qzProgFill');

  if (countEl) countEl.textContent = answered;
  if (pctEl)   pctEl.textContent   = pct + '%';
  if (fillEl)  fillEl.style.width  = pct + '%';

  // mark dots as answered
  const dots = document.querySelectorAll('.qz-dot');
  dots.forEach(function(dot){
    const idx = parseInt(dot.dataset.index, 10);
    const entry = userAnswers[idx];
    let has = false;
    if (entry) {
      const v = entry.answer;
      if (Array.isArray(v) && v.length) has = true;
      else if (typeof v === 'string' && v.trim() !== '') has = true;
      else if (v !== null && v !== undefined && v !== '') has = true;
    }
    if (has) dot.classList.add('answered'); else dot.classList.remove('answered');
  });
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

    currentQuestion = 0;
    userAnswers = {};
    buildNavDots();
    loadQuestion(0);
    updateProgress();
    startTimer();

    if (isMathSubject) {
      setTimeout(() => window.RWUMath?.initFields?.(), 0);
    }
  } else {
    alert("No questions found. Please try another quiz.");
    window.history.back();
  }
},

    error: function (xhr, status, error) {
      console.error("Error fetching questions:", error);
      alert("Failed to load questions. Please try again.");
      window.history.back();
    }
  });
}

/* ------------------ RENDER ONE QUESTION ------------------ */
function loadQuestion(index) {
  if (!questions || !questions[index]) return;

  const q = questions[index];

  const qNumEl = document.getElementById('qzQnum');
  if (qNumEl) qNumEl.textContent = index + 1;

  const qText = document.getElementById("question-text");
  if (qText) qText.textContent = q.text || '';

  const hintTop = document.getElementById('qzHintTop');
  if (hintTop) {
    if (q.hint) {
      hintTop.style.display = '';
      hintTop.textContent = "💡 " + q.hint;
    } else {
      hintTop.style.display = 'none';
      hintTop.textContent = '';
    }
  }

  const media = document.getElementById("question-media");
  if (media) {
    media.innerHTML = '';
    if (q.image && q.image.trim().length) {
      const img = document.createElement('img');
      img.src = resolveUrl(q.image.trim());
      img.alt = q.text ? ("Image: " + q.text.substring(0, 80)) : "Question image";
      img.loading = 'lazy';
      media.appendChild(img);
    }
  }

  const optionsContainer = document.getElementById("quiz-options");
  if (!optionsContainer) return;
  optionsContainer.innerHTML = "";

  const saved = (userAnswers[index] && userAnswers[index].answer) ? userAnswers[index].answer : null;

  if (hasOptions(q)) {
    const multi = isMultiAnswer(q);

    q.options.forEach(function (opt, i) {
      const letter = String.fromCharCode(65 + i);
      const id = "q" + index + "_" + letter;

      const input = document.createElement("input");
      input.type = multi ? "checkbox" : "radio";
      input.name = "question-" + index;
      input.id = id;
      input.value = letter;
      input.setAttribute("data-index", String(index));

      if (multi && Array.isArray(saved) && saved.indexOf(letter) > -1) {
        input.checked = true;
      }
      if (!multi && saved === letter) {
        input.checked = true;
      }

      const label = document.createElement("label");
      label.className = "qz-opt";
      label.setAttribute("for", id);

      const tick = document.createElement("span");
      tick.className = "tick";
      tick.textContent = "✓";

      // const textSpan = document.createElement("span");
      // textSpan.textContent = opt;

      // label.appendChild(tick);
      // label.appendChild(textSpan);
      // ----- render option content (text OR image) -----
const content = document.createElement("span");
content.className = "qz-opt-content";

// Backward compatible: if opt is a string, show as text
if (typeof opt === "string") {
  content.textContent = opt;
} else if (opt && typeof opt === "object") {
  const type = (opt.type || "text").toLowerCase();
  const val  = (opt.value || "").toString().trim();

  if (type === "image" && val) {
    const img = document.createElement("img");
    img.src = resolveUrl(val);
    img.alt = `Option ${letter}`;
    img.loading = "lazy";
    img.style.maxWidth = "180px";
    img.style.maxHeight = "120px";
    img.style.borderRadius = "10px";
    img.style.display = "block";
    content.appendChild(img);
  } else {
    content.textContent = val;
  }
} else {
  content.textContent = "";
}

label.appendChild(tick);
label.appendChild(content);


      optionsContainer.appendChild(input);
      optionsContainer.appendChild(label);

      input.addEventListener("change", function () {
        if (multi) {
          const selected = Array.prototype.slice.call(
            optionsContainer.querySelectorAll("input[name='question-" + index + "']:checked")
          ).map(function (el) { return el.value; });
          userAnswers[index] = { questionId: q.id, answer: selected };
        } else {
          userAnswers[index] = { questionId: q.id, answer: input.value };
        }
        updateProgress();
      });
    });
   } else {
  // saved may be latex string (math) or plain string (non-math)
  renderTextAnswer(optionsContainer, index, saved);

  // if math, also reflect saved into progress state
  if (canUseMathLive()) {
    const hidden = document.getElementById(`math_hidden_${index}`);
    if (hidden) {
      hidden.addEventListener("input", function () {
        userAnswers[index] = { questionId: q.id, answer: hidden.value };
        updateProgress();
      });

      // ensure already-saved value counts
      if (hidden.value && hidden.value.trim() !== "") {
        userAnswers[index] = { questionId: q.id, answer: hidden.value };
      }
    }
  }
}


  // Prev / Next buttons
  const prevBtn = document.getElementById("prev-btn");
  if (prevBtn) prevBtn.disabled = (index === 0);

  const nextBtn = document.getElementById("next-btn");
  if (nextBtn) nextBtn.textContent = (index === questions.length - 1) ? "Submit" : "Next";

  highlightActiveDot();
}

/* ------------------ SAVE & VALIDATE ------------------ */
function saveAnswer(index) {
  const q = questions[index];
  if (hasOptions(q)) {
    const multi = isMultiAnswer(q);
    if (multi) {
      const selected = Array.prototype.slice.call(
        document.querySelectorAll("input[name='question-" + index + "']:checked")
      ).map(function (el) { return el.value; });
      userAnswers[index] = { questionId: q.id, answer: selected };
    } else {
      const selected = document.querySelector("input[name='question-" + index + "']:checked");
      userAnswers[index] = { questionId: q.id, answer: selected ? selected.value : null };
    }
  }  else {
  if (canUseMathLive()) {
    const hidden = document.getElementById(`math_hidden_${index}`);
    userAnswers[index] = { questionId: q.id, answer: hidden ? (hidden.value || '').trim() : "" };
  } else {
    const txt = document.getElementById("text-answer");
    userAnswers[index] = { questionId: q.id, answer: txt ? txt.value.trim() : "" };
  }
}

  updateProgress();
}

function validateAnswer(index) {
  const q = questions[index];
  if (hasOptions(q)) {
    const multi = isMultiAnswer(q);
    if (multi) {
      return document.querySelectorAll("input[name='question-" + index + "']:checked").length > 0;
    }
    return !!document.querySelector("input[name='question-" + index + "']:checked");
  } else {
  if (canUseMathLive()) {
    const hidden = document.getElementById(`math_hidden_${index}`);
    return !!(hidden && hidden.value.trim().length > 0);
  }
  const txt = document.getElementById("text-answer");
  return !!(txt && txt.value.trim().length > 0);
}

}

/* ------------------ SUBMISSION ------------------ */
function submitQuiz() {
  clearInterval(timerInterval);
  saveAnswer(currentQuestion);

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
        var attemptid = response.attemptid;
        var url = fcom.makeUrl('quizr') + '?attempt=' + attemptid;
        window.location.href = url;
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Submission Failed',
          text: '❌ Failed to submit quiz. Try again.'
        });
      }
    },
    error: function (xhr, status, error) {
      console.error("Error submitting quiz:", error, xhr.responseText);
      alert("An error occurred while submitting the quiz.");
    }
  });
}

function exitQuiz() {
  clearInterval(timerInterval);
  window.history.back();
}

/* ------------------ EVENTS ------------------ */
document.getElementById("next-btn").addEventListener("click", function () {
  if (!validateAnswer(currentQuestion)) {
    alert("Please answer the question before proceeding.");
    return;
  }
  saveAnswer(currentQuestion);

  if (currentQuestion < questions.length - 1) {
    currentQuestion++;
    loadQuestion(currentQuestion);
  } else {
    const btn = document.getElementById("next-btn");
    btn.disabled = true;
    btn.textContent = "Processing...";
    submitQuiz();
  }
});

document.getElementById("prev-btn").addEventListener("click", function () {
  saveAnswer(currentQuestion);
  if (currentQuestion > 0) {
    currentQuestion--;
    loadQuestion(currentQuestion);
  }
});

document.addEventListener('click', function(e){
  const dot = e.target.closest('.qz-dot');
  if (!dot) return;
  const idx = parseInt(dot.dataset.index, 10);
  if (Number.isNaN(idx)) return;

  if (!validateAnswer(currentQuestion)) {
    // optional: warn before jumping
    // alert("Please answer the question before moving.");
    // return;
  }
  saveAnswer(currentQuestion);
  currentQuestion = idx;
  loadQuestion(currentQuestion);
});

/* ------------------ INIT ------------------ */
window.onload = function () {
  if (Array.isArray(questions) && questions.length) {
    questions = normalizeQuestions(questions);
    buildNavDots();
    loadQuestion(0);
    updateProgress();
  }
  startTimer();
  fetchQuestions(); // will replace questions with fresh data
};

/* Existing filter toggle behaviour (unchanged) */
var _body = $('body');
var _toggle = $('.js-filter-toggle');
_toggle.each(function () {
  var _this = $(this), _target = $(_this.attr('href'));
  _this.on('click', function (e) {
    e.preventDefault();
    _target.toggleClass('is-filter-visible');
    _this.toggleClass('is-active');
    _body.toggleClass('is-filter-show');
  });
});
</script>
