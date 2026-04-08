<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();
 

// function getBaseUrl() {
//     $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off'
//                  || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
//     $host = $_SERVER['HTTP_HOST'];
//     $script = $_SERVER['SCRIPT_NAME'];
//     $path = str_replace(basename($script), '', $script);
//     return $protocol . $host . $path;
// }

 
?>
 

   <section>
        <div class="container">
            <div class="row mt-5 justify-content-center mb-5">
                <div class="col-md-6">
                    <div class="logo-w-head d-flex align-items-baseline">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/math.jpg">
                        
                        <?php echo $_SESSION['subtopicName']; ?>
                    </div>
                    <div class="">
                        <h6 class="fw-bold brd-crumb">GCSE Maths ~ AQA Higher</h6>
                    </div>
                    <p class="mb-3"> It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. 
                    </p>
                    <p>The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content hereThe point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using 'Content here, content here</p>
                </div>
                <div class="col-md-6">
                    <div class="vide-wrap embed-responsive embed-responsive-16by9">
                        <iframe width="1128" height="634" src="https://www.youtube.com/embed/aqz-KE-bpKQ" title="Big Buck Bunny 60fps 4K - Official Blender Foundation Short Film" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                    </div>
                </div>
            </div>
        </div>

        <div class="pt-4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-9">
                        <!-- Custom Tab Bar -->
                        <div class="tab-wrapper">
                            <div class="tabs w-75 p-1">
                              <div class="tab pt-3" onclick="openTab(event,'video')">Video Lessons</div>
                              <div class="tab active pt-3" onclick="openTab(event,'quiz')">Quiz</div>
                              <div class="tab  pt-3" onclick="openTab(event,'paper')">Past Paper</div>
                            </div>
                          </div>
                        
                    </div>
                </div> 

                <!-- Tab Contents -->
                <div class="pt-4">
                    <div id="video" class="tab-content ">
                        <h4>Video Lessons</h4>
                        <p>This section contains video lessons for your course.</p>
                    </div>
                    
                    <div id="quiz" class="tab-content">
                      <div class="row">
                            <div class="col-lg-6">






                            <?php


                            $subjectId = $_SESSION['subtopicId'];
                            $db = FatApp::getDb();

                            // Fetch topics
                            $topicQuery = "SELECT id, topic FROM course_topics WHERE subject_id = " . (int)$subjectId;
                            $topicResult = $db->query($topicQuery);
                            $topics = $db->fetchAll($topicResult);

                            if (empty($topics)) {
                            echo '<p>No topics found.</p>';
                            } else {
                            foreach ($topics as $topicIndex => $topic) {
                            $topicId = $topic['id'];
                            $topicName = htmlspecialchars($topic['topic']);

                            // Fetch subtopics
                            $subtopicQuery = "SELECT id, topic as subtopic FROM course_topics WHERE parent_id = " . (int)$topicId;
                            $subtopicResult = $db->query($subtopicQuery);
                            $subtopics = $db->fetchAll($subtopicResult);
                            ?>

                            <div class="dropdown dropdown-vid-55">
                            <button class="btn-prnt-55" type="button" id="video-less-dropdown-<?php echo $topicId; ?>" data-bs-toggle="dropdown" aria-expanded="false">
                            <img src="<?php echo getBaseUrl(); ?>assets/img/arrow.svg">
                            Topic <?php echo $topicIndex + 1; ?> - <?php echo $topicName; ?>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="video-less-dropdown-<?php echo $topicId; ?>">
                            <?php
                            if (empty($subtopics)) {
                            echo '<li><span class="dropdown-item text-muted">No subtopics found.</span></li>';
                            } else {
                            foreach ($subtopics as $subIndex => $sub) {
                            ?>
                            <li>
                            <button class="btn-prnt-inner" data-bs-toggle="modal" data-bs-target="#quizSignupModal">
                            <span>
                            <span class="numb-index"><?php echo ($topicIndex + 1) . '.' . ($subIndex + 1); ?></span>
                            <span><?php echo htmlspecialchars($sub['subtopic']); ?></span>
                            </span>
                            <img src="<?php echo getBaseUrl(); ?>assets/img/right-arrow.svg">
                            </button>
                            </li>
                            <?php
                            }
                            }
                            ?>
                            </ul>
                            </div>
                            <?php
                            }
                            }
                            ?>

                 
                         
                            </div>
                        </div>
                    </div>


      
                    
                    <div id="paper" class="tab-content active">
                        <!-- <div class="d-flex justify-content-end mb-3">
                            <button class="btn-expand">Expand All <img src="<?php echo getBaseUrl(); ?>assets/img/expand.svg"></button>
                        </div> -->
 

              
                        <div class="accordion accordian-past-paper" id="accordionExample">
                            <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                Paper 2025
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="paper-list-download mb-2">
                                        <span>June 25 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                    <div class="paper-list-download mb-2">
                                        <span>June 25 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                Paper 2024
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="paper-list-download mb-2">
                                        <span>June 24 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                    <div class="paper-list-download mb-2">
                                        <span>June 24 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <div class="accordion-item mb-3">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    Paper 2023
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                <div class="accordion-body">
                                    <div class="paper-list-download mb-2">
                                        <span>June 23 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                    <div class="paper-list-download mb-2">
                                        <span>June 23 Paper 1</span>
                                        <button>Download Question Paper</button>
                                    </div>
                                </div>
                            </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- <div class="py-4">
                    <button class="btn btn-primary"  data-bs-toggle="modal" data-bs-target="#quizSignupModal">Start Quiz</button>
                </div> -->

                <!-- modal-start-quiz -->
                <div class="modal fade" id="quizSignupModal" tabindex="-1" aria-labelledby="quizSignupModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                      <div class="modal-content p-4" style="margin:auto;">
                        <div class="modal-body text-center">
                          <h2 class="fw-bold mb-2" id="quizSignupModalLabel">Ready to test your<br>Knowledge</h2>
                          <p class="mb-4" style="font-size: 1rem;">Create an account to access the quiz.<br>We'll send your quiz result directly to your gmail</p>
                          <form class="form-knowledge">
                            <input type="text" class="form-control mb-3" placeholder="Enter full name" required>
                            <input type="email" class="form-control mb-3" placeholder="Enter e-mail" required>
                             <input type="email" class="form-control mb-3" placeholder="Enter Parents e-mail" required>
                            <input type="tel" class="form-control mb-4" placeholder="Enter phone number" required>
                            <button type="submit" class=" start-quiz-btn w-100 py-2">Start Quiz</button>
                          </form>
                        </div>
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
    $.ajax({
        url: fcom.makeUrl('Quizizz', 'getQuestions'),
        type: "POST",
        data: {
            pageno: 1,
            subtopicid: userSessionId
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                questions = response.data;
                loadQuestion(0);
                startTimer();
            } else {
                alert("No questions found.");
            }
        },
        error: function(xhr, status, error) {
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
            submitQuiz();
        }

        let minutes = Math.floor(timerDuration / 60);
        let seconds = timerDuration % 60;
        document.getElementById("timer").innerText =
            `Time Left: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

        timerDuration--;
    }, 1000);
}




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
            if (questionData.type === "Multiple-Choice" && Array.isArray(savedAnswer) && savedAnswer.includes(
                    input.value)) {
                input.checked = true;
            }

            input.addEventListener("change", () => {
                if (questionData.type === "Single-Choice") {
                    userAnswers[index] = input.value;
                } else {
                    const selected = Array.from(optionsContainer.querySelectorAll(
                            "input[type='checkbox']:checked"))
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

    // === STORY-BASED / TEXT ===
    if (questionData.type === "Story-Based") {
        const input = document.createElement("input");
        input.type = "text";
        input.id = "text-answer";
        input.placeholder = "Type your answer here...";
        input.value = userAnswers[index] || "";

        input.addEventListener("input", function() {
            userAnswers[index] = this.value;
            console.log("✅ Updated userAnswers:", JSON.parse(JSON.stringify(userAnswers)));
        });

        optionsContainer.appendChild(input);
    }

    // Hint & Explanation UI
    document.getElementById("hint-text").innerText = questionData.hint || "";
    document.getElementById("explanation-text").innerText = questionData.explanation || "";
    document.getElementById("hint-btn").classList.toggle("hidden", !questionData.hint);

    // Buttons
    document.getElementById("prev-btn").disabled = index === 0;
    document.getElementById("next-btn").innerText = index === questions.length - 1 ? "Submit" : "Next";
}


document.addEventListener("DOMContentLoaded", function() {
    const quizOptions = document.getElementById("quiz-options");
    if (quizOptions) {
        quizOptions.addEventListener("change", function(event) {
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



// document.getElementById(`text-answer-${index}`).addEventListener("input", function () {
//     userAnswers[index] = this.value.trim();
// });



// Show Hint
document.getElementById("hint-btn").addEventListener("click", function() {
    document.getElementById("hint-text").classList.toggle("hidden");
});

// Show Explanation when an answer is selected
document.getElementById("quiz-options").addEventListener("change", function() {
    document.getElementById("explanation-text").classList.remove("hidden");
});

// Submit Quiz Function
// function submitQuiz() {
//     clearInterval(timerInterval); // Stop timer on submit
//     alert("Quiz Submitted!");
//     window.location.href = "quizresults"; // Redirect to results page
// }


function submitQuiz() {

    clearInterval(timerInterval);
    // document.getElementById("submit-btn").disabled = true;
    saveAnswer(currentQuestion);
    console.log("User Answers:", userAnswers);

    $.ajax({
        url: fcom.makeUrl('Quizizz', 'submitAnswers'),
        type: "POST",
        data: {
            answers: JSON.stringify(userAnswers),
            subtopicid: userSessionId
        },
        dataType: "json",
        success: function(response) {
            console.log("✅ Server Response:", response);

            if (response.success) {

                const resultStatus = response.status;
                const marks = response.marksObtained; // assuming `marks` is returned
                const userName = response.userName || 'User';

                if (resultStatus === 'pass') {

                    Swal.fire({
                        icon: 'success',
                        title: '🎉 Yay! You Passed!',
                        html: `
                <p style="font-size: 16px;">You scored <strong style="color: green;">${marks}</strong> points!</p>
                <img src="https://media.giphy.com/media/111ebonMs90YLu/giphy.gif" alt="Congrats" style="margin:auto;max-width:200px;" />
                <p style="font-size: 14px;">Ready for a <strong>Certification Exam (£10)</strong> or want to <strong>explore more fun topics</strong>? 🤓</p>
            `,
                        showCancelButton: true,
                        confirmButtonText: '🎖️ Take Exam',
                        cancelButtonText: '🧠 Explore More',
                        background: '#e6fffa',
                        confirmButtonColor: '#4caf50',
                        cancelButtonColor: '#03a9f4',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href =
                            '/certification-exam'; // ✅ Update to your actual exam route
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href =
                            '/courses'; // ✅ Update to your advanced courses page
                        }
                    });


                } else {

                    Swal.fire({
                        icon: 'error',
                        title: '😢 Oops! You didn\'t pass!',
                        html: `
                <p style="font-size: 16px;">You scored <strong style="color: red;">${marks}</strong> points.</p>
                 <img src="https://media.giphy.com/media/3og0IPxMM0erATueVW/giphy.gif" alt="Try Again" style="margin:auto;max-width:200px; " />
                <p style="font-size: 14px;">Would you like help from a tutor or try some videos? 📚</p>
            `,
                        showCancelButton: true,
                        confirmButtonText: '👨‍🏫 Get Tutor Help',
                        cancelButtonText: '🎬 Watch Videos',
                        background: '#fff3f3',
                        confirmButtonColor: '#f44336',
                        cancelButtonColor: '#ff9800',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '/teachers'; // ✅ Update to your tutor list page
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            window.location.href =
                            '/struggle-topics-videos'; // ✅ Update to your video suggestions page
                        }
                    });


                }
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Submission Failed',
                    text: '❌ Failed to submit quiz. Try again.',
                });
            }

        },
        error: function(xhr, status, error) {
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





document.getElementById("next-btn").addEventListener("click", function() {
    const isValid = validateAnswer(currentQuestion);
    if (!isValid) {
        alert("Please answer the question before proceeding.");
        return;
    }

    saveAnswer(currentQuestion);

    if (currentQuestion < questions.length - 1) {
        currentQuestion++; // 👈 increment it here!
        loadQuestion(currentQuestion);
    } else {
        submitQuiz();
    }
});



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




document.getElementById("prev-btn").addEventListener("click", function() {
    saveAnswer(currentQuestion);
    if (currentQuestion > 0) {
        currentQuestion--;
        loadQuestion(currentQuestion);
    }
});





// Load the first question and start the timer (only once)
window.onload = function() {
    loadQuestion(0);
};
startTimer();
fetchQuestions();
</script>