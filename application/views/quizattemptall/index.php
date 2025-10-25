<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
 
?>


<style>
   .hidden {
    display: none;
}

.quiz-container {
    /* border: 2px solid #ccc; border-radius: 12px; padding: 20px;
     */
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
    width: 900px;
    margin: auto;
    /* background: #fff; */
    padding: 20px;
    border-radius: 32px;
    /* box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); */
    text-align: center;
}

.quiz-question h3 {
    font-size: 20px;
    color: #333;
    margin-bottom: 15px;
}

.quiz-hint {
    font-size: 16px; 
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
 .quiz-media img{
  max-width: 60%;
  height: 60%;
  border-radius: 12px;
  display: block;
  margin: 12px auto 16px;
}


    </style>
<section class="section section--gray section--listing">
    



<div class="page-listing__body">
    <div class="course-results">
        <?php
        
        if (count($courses)) { 
?>               
                <div class="course-card">
                    <div class="course-grid">
                        
                    <div class="quiz-container">

                    <div class="quiz-header" style="display: flex; justify-content: space-between; align-items: center;">
                    <p id="subtopic" style="font-weight: bold;"> <?php echo ucfirst($_SESSION['subtopicName']); ?></p>
    <p id="timer" style="color:red;">Time Left: 10:00</p>
</div>
   
    <div class="quiz-question">
        <h3 id="question-text"></h3>
    </div>
     <div id="question-media" class="quiz-media"></div>
 
    <div id="hint-btn" class=""></div>
    
    <div id="quiz-options" class="quiz-options">
      
    </div>
 
    <!-- <div class="quiz-navigation">
        <button id="prev-btn" style="display:none;" class="btn btn--secondary" disabled>Previous</button>
        <button id="next-btn" class="btn btn--primary">Next</button>
    </div> -->

    <div class="quiz-navigation" style="text-align:center; margin-top: 30px;">

     <!-- <button id="next-btn" class="btn btn--primary">Next</button> -->
    <button id="submit-btn" class="btn btn--primary">Submit Quiz</button>
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
<!-- Load jQuery first -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Then load the jQuery Validation plugin -->
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>

<script>
/* ------------------ BOOTSTRAP DATA FROM PHP ------------------ */
var questions = <?php echo json_encode($questionData ?? []); ?>; // may be replaced by AJAX
let currentQuestion = 0; // not used for paging here, but kept for compatibility
const CONF_WEBROOT_FRONT_URL = <?= json_encode(CONF_WEBROOT_FRONT_URL); ?>;
const ENFORCE_SINGLE_CHOICE = true;
var userSessionId = "<?php echo $_SESSION['subtopicId']; ?>";

/* ------------------ STATE ------------------ */
let timerDuration = 10 * 60;   // seconds
let timerInterval = null;
let userAnswers = {};          // { [qIndex]: { questionId, answer } }

/* ------------------ UTILITIES ------------------ */
function toArray(val) { return Array.isArray(val) ? val : (val ? [val] : []); }

// Normalize backend rows into a predictable shape
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

// Decide final render type from both declared type and data shape
// Returns: 'single', 'multiple', or 'text'
function deriveType(q) {
  const t = q._rawType;
  const hasOpts = Array.isArray(q.options) && q.options.length > 0;

  if (ENFORCE_SINGLE_CHOICE && hasOpts) return 'single';       // 🔒 force radios

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
  if (/^https?:\/\//i.test(u)) return u;  // already absolute
  const base = CONF_WEBROOT_FRONT_URL.replace(/\/+$/, '');
  return (u.charAt(0) === '/') ? (base + u) : (base + '/' + u);
}

/* ------------------ TIMER ------------------ */
function startTimer() {
  if (timerInterval !== null) return; // already running

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
        loadAllQuestions();
        startTimer();
      } else {
        alert("No questions found. Please try some other");
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

/* ------------------ RENDERING ------------------ */
function renderTextarea(parent, index) {
  const textarea = document.createElement("textarea");
  textarea.name = `question-${index}`;
  textarea.placeholder = "Type your answer here...";
  textarea.style.width = "100%";
  textarea.style.height = "120px";
  textarea.style.padding = "10px";
  textarea.style.fontSize = "16px";
  textarea.style.borderRadius = "8px";
  parent.appendChild(textarea);
}

function loadAllQuestions() {
  const container = document.getElementById("quiz-options");
  if (!container) return;
  container.innerHTML = "";

  questions.forEach((q, index) => {
    const wrap = document.createElement("div");
    wrap.className = "quiz-question";
    wrap.style.marginBottom = "30px";

    // Title
    const title = document.createElement("h3");
    title.innerText = `Q${index + 1}: ${q.text}`;
    wrap.appendChild(title);

    // Image (optional)
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

    // Hint (optional)
    if (q.hint) {
      const hint = document.createElement("div");
      hint.className = "quiz-hint";
      hint.innerHTML = "💡 Hint: " + q.hint;
      wrap.appendChild(hint);
    }

    // Decide final type & render input
    const finalType = deriveType(q);

    if (finalType === 'single' || finalType === 'multiple') {
      if (!q.options || q.options.length === 0) {
        // In case backend forgot options, fallback to text answer so the UI never breaks
        renderTextarea(wrap, index);
      } else {
        q.options.forEach((opt, i) => {
          const letter = String.fromCharCode(65 + i); // A,B,C...
          const id = `q${index}_${letter}`;

          const label = document.createElement("label");
          label.className = "quiz-option";
          label.setAttribute("for", id);

          const input = document.createElement("input");
          input.type = (finalType === 'multiple') ? "checkbox" : "radio";
          input.name = `question-${index}`;
          input.id = id;
          input.value = letter;
          input.setAttribute("data-index", String(index));

          label.appendChild(input);
          label.appendChild(document.createTextNode(` ${letter}) ${opt}`));
          wrap.appendChild(label);
        });
      }
    } else {
      // text / short / story
      renderTextarea(wrap, index);
    }

    container.appendChild(wrap);
  });
}

/* ------------------ VALIDATION + ANSWER BUILDING ------------------ */
function validateAllAnswers() {
  for (let i = 0; i < questions.length; i++) {
    const q = questions[i];
    const finalType = deriveType(q);
    if (finalType === 'single') {
      if (!document.querySelector(`input[name="question-${i}"]:checked`)) return false;
    } else if (finalType === 'multiple') {
      if (document.querySelectorAll(`input[name="question-${i}"]:checked`).length === 0) return false;
    } else {
      const input = document.querySelector(`textarea[name="question-${i}"]`);
      if (!input || input.value.trim() === "") return false;
    }
  }
  return true;
}

function buildUserAnswers() {
  userAnswers = {};
  questions.forEach((q, index) => {
    const finalType = deriveType(q);
    let answer = null;

    if (finalType === 'single') {
      const selected = document.querySelector(`input[name="question-${index}"]:checked`);
      if (selected) answer = selected.value;
    } else if (finalType === 'multiple') {
      const selected = document.querySelectorAll(`input[name="question-${index}"]:checked`);
      answer = Array.from(selected).map(el => el.value);
    } else {
      const textarea = document.querySelector(`textarea[name="question-${index}"]`);
      answer = (textarea && textarea.value) ? textarea.value.trim() : "";
    }

    userAnswers[index] = { questionId: q.id, answer: answer };
  });
}

/* ------------------ EVENTS ------------------ */
// Delegated change handler (keeps userAnswers live as user clicks)
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
  });
});

// Submit button
document.getElementById("submit-btn").addEventListener("click", function () {
  if (!validateAllAnswers()) {
    alert("❗ Please answer all questions before submitting.");
    return;
  }

  buildUserAnswers();             // final, defensive rebuild
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
        window.location.href = url;
      } else {
        Swal.fire("Error", "Submission failed. Please try again.", "error");
        btn.disabled = false;
        btn.innerText = "Submit Quiz";
      }
    },
    error: function (xhr, status, error) {
      console.error("Error:", error, xhr?.responseText);
      Swal.fire("Error", "Something went wrong.", "error");
      btn.disabled = false;
      btn.innerText = "Submit Quiz";
    }
  });
});

/* ------------------ EXIT + INIT ------------------ */
function exitQuiz() {
  clearInterval(timerInterval);
  window.history.back();
}

// Initial run
(function init() {
  if (Array.isArray(questions) && questions.length) {
    questions = normalizeQuestions(questions);
    loadAllQuestions();
  }
  startTimer();
  fetchQuestions(); // will replace questions with the latest set
})();

/* ------------------ Optional UI toggles already present ------------------ */
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
