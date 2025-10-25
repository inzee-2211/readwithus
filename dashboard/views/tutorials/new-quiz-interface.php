<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<!-- Copy the CSS and JavaScript from your reference code (quizattemptall/index.php) -->
<style>
    /* Copy all the CSS styles from your reference code */
    .hidden {
        display: none;
    }

    .quiz-container {
        box-shadow: 0 0 10px rgba(0,0,0,0.1);
        width: 900px;
        margin: auto;
        padding: 20px;
        border-radius: 32px;
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
    
    .quiz-media img {
        max-width: 60%;
        height: 60%;
        border-radius: 12px;
        display: block;
        margin: 12px auto 16px;
    }
</style>

<div class="quiz-container">
    <div class="quiz-header" style="display: flex; justify-content: space-between; align-items: center;">
        <p id="subtopic" style="font-weight: bold;"><?php echo htmlspecialchars($quizDetails['quiz_title']); ?></p>
        <p id="timer" style="color:red;">Time Left: 10:00</p>
    </div>
   
    <div class="quiz-question">
        <h3 id="question-text"></h3>
    </div>
    
    <div id="question-media" class="quiz-media"></div>
 
    <div id="hint-btn" class=""></div>
    
    <div id="quiz-options" class="quiz-options"></div>
 
    <div class="quiz-navigation" style="text-align:center; margin-top: 30px;">
        <button id="submit-btn" class="btn btn--primary">Submit Quiz</button>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/jquery.validation/1.19.3/jquery.validate.min.js"></script>

<script>
// Initialize with questions from PHP
var questions = <?php echo json_encode($quizDetails['questions'] ?? []); ?>;
let currentQuestion = 0;
const CONF_WEBROOT_FRONT_URL = <?= json_encode(CONF_WEBROOT_FRONT_URL); ?>;
const ENFORCE_SINGLE_CHOICE = true;
var userSessionId = "<?php echo $subtopicId; ?>";

/* ------------------ STATE ------------------ */
let timerDuration = 10 * 60;   // seconds
let timerInterval = null;
let userAnswers = {};          // { [qIndex]: { questionId, answer } }

// ... (Copy all the JavaScript functions from your reference code)

// Add your existing JavaScript logic here from the reference code
// This should include:
// - toArray function
// - normalizeQuestions function  
// - deriveType function
// - resolveUrl function
// - startTimer function
// - loadAllQuestions function
// - renderTextarea function
// - validateAllAnswers function
// - buildUserAnswers function
// - Event listeners
// - Submit button handler

// Initialize the quiz
(function init() {
    if (Array.isArray(questions) && questions.length) {
        // Normalize and load questions
        loadAllQuestions();
    }
    startTimer();
})();

// Make sure to update the submit URL to point to your Tutorials controller
function submitQuizToTutorials() {
    // Update the AJAX call to submit to Tutorials controller
    $.ajax({
        url: '<?php echo MyUtility::makeUrl('Tutorials', 'submitNewQuiz'); ?>',
        type: "POST",
        data: {
            answers: JSON.stringify(userAnswers),
            subtopicid: userSessionId,
            lectureId: <?php echo $lectureId; ?>,
            courseId: <?php echo $courseId; ?>,
            progressId: <?php echo $progressId; ?>
        },
        dataType: "json",
        success: function (response) {
            // Handle success response
            if (response && response.success) {
                // Redirect or show results
                Swal.fire("Success", "Quiz submitted successfully!", "success");
                // Optionally close the window or redirect
                setTimeout(function() {
                    window.close(); // Close the tab/window
                }, 2000);
            } else {
                Swal.fire("Error", "Submission failed. Please try again.", "error");
            }
        },
        error: function (xhr, status, error) {
            console.error("Error:", error);
            Swal.fire("Error", "Something went wrong.", "error");
        }
    });
}
</script>