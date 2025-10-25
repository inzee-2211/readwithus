<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
// $keyword = $srchFrm->getField('keyword');
// $keyword->setFieldTagAttribute('title', Label::getLabel('LBL_BY_COURSE_NAME,_TEACHER_NAME,_TAGS'));
// $sorting = $srchFrm->getField('sorting');
// $priceSorting = $srchFrm->getField('price_sorting');
// $category = $srchFrm->getField('course_cate_id');
// $level = $srchFrm->getField('course_level');
// $ratings = $srchFrm->getField('course_ratings');
// $language = $srchFrm->getField('course_clang_id');
// $price = $srchFrm->getField('price');
// $priceFrom = $srchFrm->getField('price_from');
// $priceFrom->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PRICE_FROM'));
// $priceFrom->setFieldTagAttribute('class', 'price-from-js');
// $priceTill = $srchFrm->getField('price_till');
//$priceTill->setFieldTagAttribute('placeholder', Label::getLabel('LBL_PRICE_TILL'));
//$priceTill->setFieldTagAttribute('class', 'price-till-js');
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
    <div id="hint-btn"></div>

    <!-- Answer Options -->
    <div id="quiz-options" class="quiz-options">
        <!-- <label class="quiz-option">
            <input type="radio" name="answer" value="A"> A) London
        </label>
        <label class="quiz-option">
            <input type="radio" name="answer" value="B"> B) Berlin
        </label>
        <label class="quiz-option">
            <input type="radio" name="answer" value="C"> C) Paris
        </label>
        <label class="quiz-option">
            <input type="radio" name="answer" value="D"> D) Madrid
        </label> -->
    </div>

    <!-- Explanation -->
    <!-- <p id="explanation-text" class="quiz-explanation hidden">Explanation: Paris is the capital of France.</p> -->

    <!-- Navigation Buttons -->
    <div class="quiz-navigation">
        <button id="prev-btn" style="display:none;" class="btn btn--secondary" disabled>Previous</button>
        <button id="next-btn" class="btn btn--primary">Next</button>
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
    <!-- <div class="pagination pagination--centered margin-top-10">
        <?php
        
        echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
        $pagingArr = ['page' => $post['pageno'], 'pageCount' => $pageCount, 'recordCount' => $recordCount, 'callBackJsFunc' => 'gotoPage'];
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
        ?>
    </div> -->
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
/* ------------------ CONFIG/STATE ------------------ */
var questions = <?php echo json_encode($questionData ?? []); ?>; // initial (will be replaced by AJAX)
let currentQuestion = 0;
const CONF_WEBROOT_FRONT_URL = <?= json_encode(CONF_WEBROOT_FRONT_URL); ?>;
let timerDuration = 10 * 60;  // seconds
let timerInterval = null;
const ENFORCE_SINGLE_CHOICE = true; // 🔒 force single-choice for any question that has options

let userAnswers = {};         // always store as: { [index]: { questionId, answer } }
var userSessionId = "<?php echo $_SESSION['subtopicId']; ?>";

/* ------------------ UTILITIES ------------------ */
// Normalize questions coming from the API to predictable fields
function normalizeQuestions(rows) {
  return (rows || []).map(function (q) {
    var opts = Array.isArray(q.options) ? q.options.filter(Boolean) : [];
    var ans  = Array.isArray(q.answer) ? q.answer
               : (typeof q.answer === 'string' && q.answer.trim().length
                  ? q.answer.split(',').map(function (s) { return s.trim().toUpperCase(); })
                  : []);
    return {
      id: q.id,
      text: q.text,
      hint: q.hint || '',
      options: opts,                                 // [] if none
      correct: ans, 
      image: q.image || '' ,                                 // [] if unknown
      type: (q.type || '').toLowerCase()             // 'single-choice','multiple-choice','story-based','short', etc.
    };
  });
}

function hasOptions(q) {
  return Array.isArray(q.options) && q.options.length > 0;
}

function isMultiAnswer(q) {
  // If the page must be single-choice, never treat as multiple when options exist
  if (ENFORCE_SINGLE_CHOICE && hasOptions(q)) return false;

  // Original logic (kept for completeness if you ever turn the flag off)
  if (q.type === 'multiple-choice') return true;
  if (Array.isArray(q.correct) && q.correct.length > 1) return true;
  return false;
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
  // Hide NEXT until data arrives
  var nextBtn = document.getElementById('next-btn');
  if (nextBtn) nextBtn.style.display = 'none';

  $.ajax({
    url: fcom.makeUrl('Quizattempt', 'getQuestions'),
    type: "POST",
    data: { pageno: 1, subtopicid: userSessionId },
    dataType: "json",
    success: function (response) {
      if (response && response.success) {
        questions = normalizeQuestions(response.data);
        if (nextBtn) nextBtn.style.display = ''; // show again
        currentQuestion = 0;
        loadQuestion(0);
        startTimer();
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

/* ------------------ RENDER ------------------ */
function renderHint(container, q) {
  if (!q.hint) return;
  var hintDiv = document.createElement("div");
  hintDiv.className = "quiz-hint";
  hintDiv.innerHTML = "💡 Hint: " + q.hint;
  container.appendChild(hintDiv);
}
function resolveUrl(u) {
  if (!u) return '';
  if (/^https?:\/\//i.test(u)) return u;  // already absolute
  const base = CONF_WEBROOT_FRONT_URL.replace(/\/+$/, '');  // remove trailing slash
  if (u.charAt(0) === '/') return base + u;
  return base + '/' + u;
}

function loadQuestion(index) {
  if (!questions || !questions[index]) return;

  var q = questions[index];
  var optionsContainer = document.getElementById("quiz-options");
  var media = document.getElementById("question-media");
  if (!optionsContainer) return;
  optionsContainer.innerHTML = "";
    media.innerHTML = "";      //rehan

  var qText = document.getElementById("question-text");
  if (qText) qText.innerText = "Question " + (index + 1) + ": " + q.text;

  // 👇 render image if provided
   if (q.image && q.image.trim().length) {
    var img = document.createElement('img');
    img.src = resolveUrl(q.image.trim());
    img.alt = q.text ? ("Image: " + q.text.substring(0, 80)) : "Question image";
    img.loading = 'lazy';
    media.appendChild(img);
  }

  // Previously saved value (we *always* store {questionId, answer})
  var saved = (userAnswers[index] && userAnswers[index].answer) ? userAnswers[index].answer : null;

  if (hasOptions(q)) {
    // Options present → render radio/checkbox depending on single/multi
    renderHint(optionsContainer, q);
    var multi = isMultiAnswer(q);

    q.options.forEach(function (opt, i) {
      var letter = String.fromCharCode(65 + i); // A,B,C,D...
      var id = "q" + index + "_" + letter;

      var label = document.createElement("label");
      label.className = "quiz-option";
      label.setAttribute("for", id);

      var input = document.createElement("input");
      input.type = multi ? "checkbox" : "radio";
      input.name = "question-" + index;
      input.id = id;
      input.value = letter;

      // Restore selection
      if (multi && Array.isArray(saved) && saved.indexOf(letter) > -1) {
        input.checked = true;
      }
      if (!multi && saved === letter) {
        input.checked = true;
      }

      input.addEventListener("change", function () {
        if (multi) {
          var selected = Array.prototype.slice.call(
            optionsContainer.querySelectorAll("input[name='question-" + index + "']:checked")
          ).map(function (el) { return el.value; });
          userAnswers[index] = { questionId: q.id, answer: selected };
        } else {
          userAnswers[index] = { questionId: q.id, answer: input.value };
        }
      });

      label.appendChild(input);
      label.appendChild(document.createTextNode(" " + letter + ") " + opt));
      optionsContainer.appendChild(label);
    });

  } else {
    // No options → textarea (short/story)
    renderHint(optionsContainer, q);
    var textarea = document.createElement("textarea");
    textarea.id = "text-answer";
    textarea.placeholder = "Type your answer here...";
    textarea.value = (typeof saved === "string") ? saved : "";
    textarea.style.width = "100%";
    textarea.style.height = "180px";
    textarea.style.padding = "12px";
    textarea.style.fontSize = "16px";
    textarea.style.borderRadius = "8px";
    textarea.style.boxSizing = "border-box";
    textarea.addEventListener("input", function () {
      userAnswers[index] = { questionId: q.id, answer: this.value };
    });
    optionsContainer.appendChild(textarea);
  }

  // Prev/Next controls
  var prevBtn = document.getElementById("prev-btn");
  if (prevBtn) prevBtn.disabled = (index === 0);

  var nextBtn2 = document.getElementById("next-btn");
  if (nextBtn2) nextBtn2.innerText = (index === questions.length - 1) ? "Submit" : "Next";
}

/* ------------------ SAVE & VALIDATE ------------------ */
function saveAnswer(index) {
  var q = questions[index];

  if (hasOptions(q)) {
    var multi = isMultiAnswer(q);
    if (multi) {
      var selected = Array.prototype.slice.call(
        document.querySelectorAll("input[name='question-" + index + "']:checked")
      ).map(function (el) { return el.value; });
      userAnswers[index] = { questionId: q.id, answer: selected };
    } else {
      var selected = document.querySelector("input[name='question-" + index + "']:checked");
      userAnswers[index] = { questionId: q.id, answer: selected ? selected.value : null };
    }
  } else {
    var txt = document.getElementById("text-answer");
    userAnswers[index] = { questionId: q.id, answer: txt ? txt.value.trim() : "" };
  }
}

function validateAnswer(index) {
  var q = questions[index];
  if (hasOptions(q)) {
    var multi = isMultiAnswer(q);
    if (multi) {
      return document.querySelectorAll("input[name='question-" + index + "']:checked").length > 0;
    }
    return !!document.querySelector("input[name='question-" + index + "']:checked");
  } else {
    var txt = document.getElementById("text-answer");
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
    var btn = document.getElementById("next-btn");
    btn.disabled = true;
    btn.innerText = "Processing...";
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

/* ------------------ INIT ------------------ */
window.onload = function () {
  // Render placeholder (if any) then fetch real questions
  if (Array.isArray(questions) && questions.length) {
    questions = normalizeQuestions(questions);
    loadQuestion(0);
  }
  startTimer();
  fetchQuestions();
};

/* ------------------ (Optional) UI toggles you had ------------------ */
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

