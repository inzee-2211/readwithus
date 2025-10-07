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
function fetchQuestions() {
//   document.getElementById('next-btn').style.display = 'none';
// $('#next-btn').hide();
    
    $.ajax({
        url: fcom.makeUrl('Quizattempt', 'getQuestions'),  
        type: "POST",
        data: { pageno: 1 , subtopicid:userSessionId},  
        dataType: "json",
        success: function (response) {
            
            if (response.success) {
                 console.log(response.data);
                  questions = response.data;  
                 loadAllQuestions();
                  startTimer();  
            } else {
               
                alert("No questions found.Please try some other");
                 window.history.back();
            }
        },
        error: function (xhr, status, error) {
            console.error("Error fetching questions:", error);
        }
    });
}

// Function to start the timer (ensuring only one instance runs)
function startTimer() {
    if (timerInterval !== null) return;  

    timerInterval = setInterval(() => {
        if (timerDuration <= 0) {
            clearInterval(timerInterval);
            alert("Time is up! Submitting quiz...");
            exitQuiz();
        }

        let minutes = Math.floor(timerDuration / 60);
        let seconds = timerDuration % 60;
        document.getElementById("timer").innerText = `Time Left: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

        timerDuration--;
    }, 1000);
}


 function loadAllQuestions() {
    
    const container = document.getElementById("quiz-options");
    container.innerHTML = "";

    questions.forEach((questionData, index) => {
        const questionBlock = document.createElement("div");
        questionBlock.classList.add("quiz-question");
        questionBlock.style.marginBottom = "30px";

        const questionTitle = document.createElement("h3");
        questionTitle.innerText = `Q${index + 1}: ${questionData.text}`;
        questionBlock.appendChild(questionTitle);

        if (questionData.type === "Single-Choice" || questionData.type === "Multiple-Choice") {
            questionData.options.forEach((option, i) => {
                const optionId = `q${index}_opt${i}`;
                const wrapper = document.createElement("div");
                wrapper.className = "quiz-option";

                const input = document.createElement("input");
                input.type = questionData.type === "Single-Choice" ? "radio" : "checkbox";
                input.name = `question-${index}`;
                input.id = optionId;
                input.value = String.fromCharCode(65 + i); // A, B, C...

                const label = document.createElement("label");
                label.setAttribute("for", optionId);
                label.textContent = `${input.value}) ${option}`;

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                questionBlock.appendChild(wrapper);
            });
        }

        if (questionData.type === "Story-Based") {
            const textarea = document.createElement("textarea");
            textarea.name = `question-${index}`;
            textarea.placeholder = "Type your answer here...";
            textarea.style.width = "100%";
            textarea.style.height = "120px";
            textarea.style.padding = "10px";
            textarea.style.fontSize = "16px";
            textarea.style.borderRadius = "8px";

            questionBlock.appendChild(textarea);
        }

        container.appendChild(questionBlock);
    });
}



function validateAllAnswers() {
    for (let i = 0; i < questions.length; i++) {
        const question = questions[i];
        if (question.type === "Single-Choice") {
            if (!document.querySelector(`input[name="question-${i}"]:checked`)) return false;
        } else if (question.type === "Multiple-Choice") {
            const selected = document.querySelectorAll(`input[name="question-${i}"]:checked`);
            if (selected.length === 0) return false;
        } else if (question.type === "Story-Based") {
            const input = document.querySelector(`textarea[name="question-${i}"]`);
            if (!input || input.value.trim() === "") return false;
        }
    }
    return true;
}


document.getElementById("submit-btn").addEventListener("click", function () {
    if (!validateAllAnswers()) {
        alert("❗ Please answer all questions before submitting.");
        return;
    }

    // Build userAnswers object
    userAnswers = {};

    questions.forEach((question, index) => {
        let answer = null;

        if (question.type === "Single-Choice") {
            const selected = document.querySelector(`input[name="question-${index}"]:checked`);
            if (selected) answer = selected.value;
        }

        if (question.type === "Multiple-Choice") {
            const selected = document.querySelectorAll(`input[name="question-${index}"]:checked`);
            answer = Array.from(selected).map(el => el.value);
        }

        if (question.type === "Story-Based") {
            const textarea = document.querySelector(`textarea[name="question-${index}"]`);
            answer = textarea?.value.trim() || "";
        }

        userAnswers[index] = {
            questionId: question.id,
            answer: answer
        };
    });

    console.log("Submitting userAnswers:", userAnswers);

    clearInterval(timerInterval);

    const nextBtn = document.getElementById("submit-btn");
    nextBtn.disabled = true;                     
    nextBtn.innerText = "Processing...";

    $.ajax({
        url: fcom.makeUrl('Quizattempt', 'submitAnswers'),
        type: "POST",
        data: {
            answers: JSON.stringify(userAnswers),
            subtopicid: userSessionId
        },
        dataType: "json",
        success: function (response) {
            if (response.success) {
                const url = fcom.makeUrl('quizr') + '?attempt=' + response.attemptid;
                window.location.href = url;
            } else {
                Swal.fire("Error", "Submission failed. Please try again.", "error");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
            Swal.fire("Error", "Something went wrong.", "error");
        }
    });
});



/*
function loadQuestion(index) {
    if (!questions || !questions[index]) {
        console.error("❌ Invalid question index:", index);
        return;
    }
 
    const questionData = questions[index];
    const optionsContainer = document.getElementById("quiz-options");
    optionsContainer.innerHTML = "";

    document.getElementById("question-text").innerText = `Question ${index + 1}: ${questionData.text}`;
    
    // === SINGLE & MULTI CHOICE ===
    if (questionData.type === "Single-Choice" || questionData.type === "Multiple-Choice") {
        questionData.options.forEach((option, i) => {
            const optionId = `q${index}_opt${i}`;
            const wrapper = document.createElement("div");
            wrapper.className = "quiz-option";

            const input = document.createElement("input");
            input.type = questionData.type === "Single-Choice" ? "radio" : "checkbox";
            input.name = `question-${index}`;
            input.id = optionId;
            input.value = String.fromCharCode(65 + i); // A, B, C...

            const savedAnswer = userAnswers[index];

            // restore answer
            if (questionData.type === "Single-Choice" && savedAnswer === input.value) {
                input.checked = true;
            }
            if (questionData.type === "Multiple-Choice" && Array.isArray(savedAnswer) && savedAnswer.includes(input.value)) {
                input.checked = true;
            }

            input.addEventListener("change", () => {
                if (questionData.type === "Single-Choice") {
                    userAnswers[index] = input.value;
                } else {
                    const selected = Array.from(optionsContainer.querySelectorAll("input[type='checkbox']:checked"))
                        .map(el => el.value);
                    userAnswers[index] = selected;
                }
                console.log("✅ Updated userAnswers:", JSON.parse(JSON.stringify(userAnswers)));
            });

            const label = document.createElement("label");
            label.setAttribute("for", optionId);
            label.textContent = `${input.value}) ${option}`;

            wrapper.appendChild(input);
            wrapper.appendChild(label);
            optionsContainer.appendChild(wrapper);
        });
    }

 

    if (questionData.type === "Story-Based") {
    const textarea = document.createElement("textarea");
    textarea.id = "text-answer";
    textarea.placeholder = "Type your answer here...";
    textarea.value = userAnswers[index] || "";

    textarea.addEventListener("input", function () {
        userAnswers[index] = this.value;
        console.log("✅ Updated userAnswers:", JSON.parse(JSON.stringify(userAnswers)));
    });

    // Optional: Add styling directly or via CSS class
    textarea.style.width = "100%";
    textarea.style.height = "180px";
    textarea.style.padding = "12px";
    textarea.style.fontSize = "16px";
    textarea.style.borderRadius = "8px";
    textarea.style.boxSizing = "border-box";

    optionsContainer.appendChild(textarea);
}

 
    document.getElementById("hint-text").innerText = questionData.hint || "";
    document.getElementById("explanation-text").innerText = questionData.explanation || "";
    document.getElementById("hint-btn").classList.toggle("hidden", !questionData.hint);

   
    document.getElementById("prev-btn").disabled = index === 0;
    document.getElementById("next-btn").innerText = index === questions.length - 1 ? "Submit" : "Next";
}*/
 

document.addEventListener("DOMContentLoaded", function () {
    const quizOptions = document.getElementById("quiz-options");
    if (quizOptions) {
        quizOptions.addEventListener("change", function (event) {
            const answer = event.target.value;
            const questionIndex = parseInt(event.target.getAttribute("data-index"));

            if (questionData.type === "Multiple-Choice") {
                if (!userAnswers[questionIndex]) {
                    userAnswers[questionIndex] = [];
                }
                if (!userAnswers[questionIndex].includes(answer)) {
                    userAnswers[questionIndex].push(answer);
                }
            } else {
                userAnswers[questionIndex] = answer;
            }
        });
    } else {
        console.warn("Element with ID 'quiz-options' not found.");
    }
});
 
function exitQuiz() {

    clearInterval(timerInterval);
     window.history.back();
}

 

function submitQuiz() {

    clearInterval(timerInterval);
    saveAnswer(currentQuestion);  
    console.log("User Answers:", userAnswers); 
   //  document.getElementById("next-btn").innerText="Processing...":
    $.ajax({
        url: fcom.makeUrl('Quizattempt', 'submitAnswers'),
        type: "POST",
        data: {
            answers: JSON.stringify(userAnswers),
            subtopicid: userSessionId
        },
        dataType: "json",
        success: function (response) {
            console.log("✅ Server Response:", response);

            if (response.success) {
                
    const resultStatus = response.status;  
     const attemptid = response.attemptid;  
    const marks = response.marksObtained; // assuming `marks` is returned
    const userName = response.userName || 'User';
    
     var url = fcom.makeUrl('quizr') + '?attempt=' + attemptid;
        window.location.href = url;

    if (resultStatus === 'pass') {

       var url = fcom.makeUrl('quizr') + '?attempt=' + attemptid;
        window.location.href = url;
 
        

    } else {
 
    }
} else {
    Swal.fire({
        icon: 'error',
        title: 'Submission Failed',
        text: '❌ Failed to submit quiz. Try again.',
    });
}

        },
        error: function (xhr, status, error) {
            console.error("🚨 Error submitting quiz:", error);
            console.warn("📦 Raw Response:", xhr.responseText);
            alert("An error occurred while submitting the quiz.");
        }
    });
}


function calculateGrade(score) {
    const percentage = (score / questions.length) * 100;
    if (percentage >= 90) return "A";
    if (percentage >= 80) return "B";
    if (percentage >= 70) return "C";
    if (percentage >= 60) return "D";
    return "F";
}

 

function saveAnswer(index) {
    const question = questions[index];
    let answer = null;

    if (question.type === "Single-Choice") {
        const selectedOption = document.querySelector(`input[name="question-${index}"]:checked`);
        if (selectedOption) {
            answer = selectedOption.value;
        }
    }

    if (question.type === "Multiple-Choice") {
        const selectedOptions = document.querySelectorAll(`input[name="question-${index}"]:checked`);
        answer = Array.from(selectedOptions).map(opt => opt.value);
    }

    if (question.type === "Story-Based") {
        const textInput = document.getElementById("text-answer");
        if (textInput) {
            answer = textInput.value.trim();
        }
    }

    // Save as object: questionId + answer
    userAnswers[index] = {
        questionId: question.id,
        answer: answer
    };

    console.log("✅ Updated userAnswers:", userAnswers);
}



 
/*
document.getElementById("next-btn").addEventListener("click", function () {
    const isValid = validateAnswer(currentQuestion);
    if (!isValid) {
        alert("Please answer the question before proceeding.");
        return;
    }

    saveAnswer(currentQuestion);

    if (currentQuestion < questions.length - 1) {
        currentQuestion++;  // 👈 increment it here!
        loadQuestion(currentQuestion);
    } else {
        const nextBtn = document.getElementById("next-btn");
    nextBtn.disabled = true;                     
    nextBtn.innerText = "Processing...";
        submitQuiz();
    }
});
*/


function validateAnswer(index) {
    const question = questions[index];
    console.log("🔍 Validating Q#", index, "Type:", question.type);

    if (question.type === "Single-Choice") {
        const selected = document.querySelector(`input[name="question-${index}"]:checked`);
        console.log("Selected single option:", selected);
        return !!selected;
    }

    if (question.type === "Multiple-Choice") {
        const selected = document.querySelectorAll(`input[name="question-${index}"]:checked`);
        console.log("Selected multiple options:", selected.length);
        return selected.length > 0;
    }

    if (question.type === "Story-Based") {
        const input = document.getElementById("text-answer");
        console.log("Story input value:", input?.value);
        return input && input.value.trim().length > 0;
    }

    return false;
}




// document.getElementById("prev-btn").addEventListener("click", function () {
//     saveAnswer(currentQuestion);
//     if (currentQuestion > 0) {
//         currentQuestion--;
//         loadQuestion(currentQuestion);
//     }
// });
 
window.onload = function () {
     
    loadQuestion(0);
};
startTimer();
fetchQuestions();


</script>