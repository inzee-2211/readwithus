<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

if (isset($_GET['subtopic'])) {
    $subtopicId = intval($_GET['subtopic']);  
  
}  
 
?>


<style>
    .hidden {
        display: none;
    }

    .quiz-container {
        /* border: 2px solid #ccc; border-radius: 12px; padding: 20px;
     */
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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
<section class="section section--gray section--listing" style="min-height: 800px;">
    <div class="container-fluid px-lg-5 pb-5">
        <div class="row g-3 justify-content-center quiz-wrapper">
            <div class="col-md-9 col-12 mb-3">

                <div class="quiz-selection-box p-4 bg-white rounded-4 shadow-sm">
                    <h2 class="mb-4 text-center">How would you like to learn?</h2>

                    <div class="d-flex align-items-center justify-content-between twin-btn-wrap">
                        <button class="custom-quiz-btn w-50 text-center me-3" data-bs-toggle="modal" data-bs-target="#quizInfoModal" onclick="window.location.href='quizattemptall?subtopic=<?php echo $subtopicId; ?>';">
                            <span class="d-flex flex-column align-items-center justify-content-center text-center">
                                <img src="<?php echo getBaseUrl(); ?>assets/img/boxes.svg" alt="" class="mb-3 img-box">
                                <span>
                                    <span class="fw-bold" style="font-size: 24px;">All at once</span><br>
                                    <span style="font-size: 0.9rem; color: #222;">View all questions at once</span>
                                </span>
                            </span>
                        </button>   

                        <button class="custom-quiz-btn w-50 text-center ms-3" onclick="window.location.href='quizattempt?subtopic=<?php echo $subtopicId; ?>';">
                            <span class="d-flex flex-column align-items-center justify-content-center text-center">
                                <img src="<?php echo getBaseUrl(); ?>assets/img/Mission statement.png" alt="" class="mb-3 img-quiz">
                                <span>
                                    <span class="fw-bold" style="font-size: 24px;">Focus</span><br>
                                    <span style="font-size: 0.9rem; color: #222;">View one question at a time</span>
                                </span>
                            </span>
                        </button>    
                    </div>

                    <br>

                    <div class="quiz-selection-box p-4 text-center">
                        <p>
    Choose the learning style that suits you best: with <strong>All at once</strong>, explore all questions together and build your confidence by tackling the entire quiz in one go. Or select <strong>Focus</strong> mode to concentrate on one question at a time, perfect for deep understanding and step-by-step learning. No matter which you pick, you'll strengthen your knowledge and be ready to ace your topics!
</p>

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
 

 
</script>