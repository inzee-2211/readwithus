<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($quizDetails) {
  //  echo '<pre>';print_r($quizDetails);die;
    ?>

<div id="quizPopup" style="display: none;">
    <div class="quiz-popup-overlay" onclick="closePopup()"></div>
    <div class="quiz-popup-content">
        <h3>Quiz Evaluation Summary</h3>
        <div id="quizSummary" class="quiz-summary-content">
            <!-- Dynamic quiz summary content will go here -->
        </div>
        <button class="btn-close-popup" onclick="closePopup()">Close</button>
    </div>
</div>
    <style>
 
.quizcontent p {
  margin-bottom: 6px;
  color: inherit;
  line-height: 1.8;
  color: var(--color-black);
}
.quiz-popup-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.7);
        z-index: 999;
    }

    /* Popup container */
    .quiz-popup-content {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 80%;
        max-width: 800px;
        max-height: 80%;
        background: #fff;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.25);
        z-index: 1000;
        overflow-y: auto; /* Add scrollbar for long content */
    }

    /* Scrollbar styles */
    .quiz-popup-content::-webkit-scrollbar {
        width: 8px;
    }

    .quiz-popup-content::-webkit-scrollbar-thumb {
        background: #ccc;
        border-radius: 4px;
    }

    .quiz-popup-content::-webkit-scrollbar-thumb:hover {
        background: #aaa;
    }

    /* Popup header */
    .quiz-popup-content h3 {
        margin: 0 0 15px;
        font-size: 1.5em;
        text-align: center;
    }

    /* Quiz summary content */
    .quiz-summary-content {
        text-align: left;
        font-size: 1em;
        line-height: 1.6;
        color: #333;
    }

    .quiz-summary-content pre {
        background: #f7f7f7;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 0.9em;
    }

    /* Close button */
    .btn-close-popup {
        margin-top: 20px;
        padding: 10px 20px;
        background: #007bff;
        color: #fff;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        text-align: center;
        width: 100%;
        font-size: 1em;
    }

    .btn-close-popup:hover {
        background: #0056b3;
    }
        .quiz-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 20px;
            position: relative;
            width:100%;
        }
        .quiz-header {
            background-color: #14A0A3;
            color: #fff;
            padding: 15px;
            border-radius: 8px 8px 0 0;
            text-align: center;
        }
        .quiz-question {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 15px;
            margin-bottom: 15px;
        }
        .quiz-options label {
            display: block;
            padding: 8px 12px;
            margin-bottom: 5px;
            background-color: #f1f1f1;
            border: 1px solid #ddd;
            border-radius: 4px;
            cursor: pointer;
        }
        .quiz-options input[type="radio"],
        .quiz-options input[type="checkbox"] {
            display: none;
        }
        .quiz-options input[type="radio"]:checked + label,
        .quiz-options input[type="checkbox"]:checked + label {
            background-color: #14A0A3;
            color: #fff;
            border-color: #14A0A3;
        }
        #quizTimer {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            background-color: #F5411F;
            color: #fff;
            text-align: center;
            padding: 10px;
            font-size: 18px;
            font-weight: bold;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.2);
        }
        button.btn-submit {
            background-color: #F5411F;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button.btn-submit:hover {
            background-color: #d73d1c;
        }
        .quiz-body {
            padding-top: 50px; /* Avoid overlap with the sticky timer */
        }
        .avtar1 {
    display: flex;
    justify-content: center;   /* Centers content horizontally */
    align-items: center;       /* Centers content vertically */
    height: 200px;             /* Set height of the container */
    width: 200px;              /* Set width of the container */
    margin: 0 auto;            /* Centers the container itself horizontally */
    margin-bottom: 22px;             /* Adds padding inside the container */
    border-radius: 12px;       /* Rounds the corners of the container */
    overflow: hidden;          /* Ensures content doesn't overflow the rounded corners */
}

 
.avtar1 img {
    width: 100%;  /* Make image responsive to container size */
    /* height: 100%;   */
    max-height:200px;
    border-radius:6px;
    object-fit: cover; /* Ensure it covers the area without distortion */
}
 
    </style>

    <div id="quizTimer">
        <strong><?php echo Label::getLabel('LBL_TIME_REMAINING'); ?>:</strong>
        <span id="timeRemaining">00:00</span>
    </div>

    <div class="quiz-container">
        <div class="quiz-header">
            <h4><?php echo htmlspecialchars($quizDetails['quiz_title']); ?></h4>
            <h5><?php echo  html_entity_decode($quizDetails['quiz_description']); ?> </h5>
            <?php
            if(isset($quizDetails['quiz_duration']) && $quizDetails['quiz_duration']>0)
            {
                $quiz_duration=$quizDetails['quiz_duration']*60;
            }
            else
            {
                $quiz_duration=0;
            }
           
            ?>
            <input type="hidden" id="quiz_id" value="<?php echo $quizDetails['quiz_id']; ?>">
            <input type="hidden" id="quiz_pass_percentage" value="<?php echo $quizDetails['quiz_pass_percentage']; ?>">
            <input type="hidden" id="quiz_teacher_id" value="<?php echo $quizDetails['quiz_user_id']; ?>">
            <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">
            <input type="hidden" id="lectureId" value="<?php echo $lectureId; ?>">
        </div>

        <div class="quiz-body">
            <form id="quizForm" method="post" action="javascript:void(0);">
                <?php 
                
               
                foreach ($quizDetails['questions'] as $index => $question) {
                    // echo '<pre>';print_r($question);die;
                     ?>
        <div class="quiz-question">

        <div style="display: flex; justify-content: space-between; align-items: center;">
        <p style="flex: 1;"><strong><?php echo ($index + 1) . '. ' . html_entity_decode($question['question_title'], ENT_QUOTES); ?></strong></p>
       
        <p style="flex-shrink: 0; text-align: right;"><strong><?php echo Label::getLabel('LBL_MARKS'); ?>:</strong> <?php echo htmlspecialchars($question['question_marks']); ?></p>
        </div>
        <?php 
if (!empty($question['question_math_equation']) && trim($question['question_math_equation']) !== '') { ?>
        <div style="display: flex; justify-content: space-between; align-items: center;">
          <p style="flex: 1;"> <?php echo   '$$'.$question['question_math_equation'].'$$'; ?> </p>
        </div> <?php } ?>
 
   <?php
$imageUrl = MyUtility::makeUrl('Image', 'show', [Afile::TYPE_LESSON_QUESTIONS_FILE, $question['question_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL);
?>

<?php if (isset($question['question_image']) && $question['question_image'] == 1): ?>
    <div  class="avtar1 avtar--centered">
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

                <div class="text-right">
                    <button type="submit" class="btn-submit" onclick="submitQuiz();"><?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?></button>
                </div>
            </form>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        //  var quiz_duration = "<?php echo $quiz_duration; ?>";
        //  let timeRemaining = quiz_duration; //  minutes in seconds
        // const timerDisplay = document.getElementById('timeRemaining');

        // function startTimer() {
        //     const interval = setInterval(() => {
        //         if (timeRemaining <= 0) {
        //             clearInterval(interval);
        //             alert('<?php echo Label::getLabel('LBL_TIME_UP_PLEASE_CONDUCT_THE_QUIZ_AGAIN'); ?>');
        //             location.reload();
        //         } else {
        //             const minutes = Math.floor(timeRemaining / 60);
        //             const seconds = timeRemaining % 60;
        //             timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
        //             timeRemaining--;
        //         }
        //     }, 1000);
        // }

        var quiz_duration = "<?php echo $quiz_duration; ?>";
    let timeRemaining = quiz_duration; // Minutes in seconds
    const timerDisplay = document.getElementById('timeRemaining');
    let timerInterval;

    function startTimer() {
        timerInterval = setInterval(() => {
            if (timeRemaining <= 0) {
                clearInterval(timerInterval);
                alert('<?php echo Label::getLabel('LBL_TIME_UP_PLEASE_CONDUCT_THE_QUIZ_AGAIN'); ?>');
                location.reload();
            } else {
                const minutes = Math.floor(timeRemaining / 60);
                const seconds = timeRemaining % 60;
                timerDisplay.textContent = `${String(minutes).padStart(2, '0')}:${String(seconds).padStart(2, '0')}`;
                timeRemaining--;
            }
        }, 1000);
    }

    function pauseTimer() {
        clearInterval(timerInterval); // Stops the timer
    }



 
  function submitQuiz() {
    
    let allAnswered = true; 
    const unansweredQuestions = [];

    // Loop through each question and check if it's answered
    $('#quizForm .quiz-question').each(function () {
        const questionId = $(this).find('input[type="radio"], input[type="checkbox"], textarea').attr('name');
        const selectedAnswer = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function () {
            return $(this).val().trim() !== '';
        });

        if (!selectedAnswer.length) {
            allAnswered = false;
            unansweredQuestions.push(questionId);
        }
    });

    if (!allAnswered) {
        alert('Please answer all the questions before submitting the quiz.');
        // Optionally, highlight unanswered questions
        unansweredQuestions.forEach((questionId) => {
            const questionElement = $(`[name="${questionId}"]`).closest('.quiz-question');
            questionElement.css('border', '2px solid red'); // Highlight unanswered question
            setTimeout(() => questionElement.css('border', ''), 5000); // Remove highlight after 3 seconds
        });
        return; // Stop submission
    }




    $('.btn-submit').prop('disabled', true).text('Submitting...');
    // Proceed if all questions are answered
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
    pauseTimer();
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
                }
  
        },
        error: function () {
            alert('<?php echo Label::getLabel('LBL_ERROR_SUBMITTING_QUIZ'); ?>');
        }
    });
}


        $(document).ready(() => {
        if(timeRemaining>0)
        {
        startTimer();
        }
        else
        {
            var element = document.getElementById("quizTimer");
        element.style.display = "none";
        }
       
        });


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

            // Loop through each question
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

            // Update the popup content and display it
            document.getElementById('quizSummary').innerHTML = popupContent;
            document.getElementById('quizPopup').style.display = 'flex';
        }
    }
    </script>
<?php } else { ?>
    <div class="message-display no-skin">
        <div class="message-display__media">
            <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
        </div>
        <h4><?php echo stripslashes(Label::getLabel("LBL_NO_QUIZ_AVAILABLE.")); ?></h4>
    </div>
<?php } ?>
