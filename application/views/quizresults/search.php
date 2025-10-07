<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();
?>
<style>
   .hidden {
    display: none;
}

.quiz-container {
    width: 900px;
    margin: auto;
    /* background: #fff; */
    padding: 20px;
    border-radius: 10px;
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
.quiz-result {
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    padding: 20px;
    border-radius: 10px;
    margin: 20px auto;
    background: #f0f0f0;
    border: 2px solid #ddd;
    width: 90%;
    max-width: 400px;
    min-height: 200px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    position: relative;
    transition: all 0.3s ease;
}

.quiz-result h2 {
    font-size: 26px;
    margin-bottom: 15px;
    color: #333;
}

.quiz-result p {
    font-size: 18px;
    margin-bottom: 20px;
    color: #555;
}

.quiz-result.success {
    border-color: #28a745;
    background: #d4edda;
    color: #155724;
}

.quiz-result.failed {
    border-color: #dc3545;
    background: #f8d7da;
    color: #721c24;
}

.quiz-result button {
    background: #007bff;
    color: white;
    border: none;
    padding: 12px 25px;
    border-radius: 6px;
    cursor: pointer;
    transition: 0.3s ease;
    font-size: 16px;
}

.quiz-result button:hover {
    background: #0056b3;
}


    </style>
 
<div class="page-listing__body">
    <div class="course-results">
        <?php
        if (count($courses)) { 
?>               
                <div class="course-card">
                    <div class="course-grid">
                        
                    <div class="quiz-container">
    <!-- Quiz content will be here -->
</div>

<!-- Result Message Section -->
<div class="quiz-result hidden" id="quizResult">
    <h2 id="resultMessage"></h2>
    <p id="resultDetails"></p>
    <button class="btn btn--primary" onclick="restartQuiz()">Try Again</button>
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
                        <h5><?php echo Label::getLabel('LBL_NO_COURSE_FOUND!'); ?></h5>
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


 
    function showResult(passed, score, total) {
    const resultContainer = $('#quizResult');
    const message = $('#resultMessage');
    const details = $('#resultDetails');

    if (passed) {
        resultContainer.addClass('success').removeClass('failed');
        message.text('🎉 Congratulations! You Passed the Quiz!');
        details.text(`Your Score: ${score} out of ${total}`);
    } else {
        resultContainer.addClass('failed').removeClass('success');
        message.text('❌ Sorry, You Failed the Quiz.');
        details.text(`Your Score: ${score} out of ${total}`);
    }

    resultContainer.removeClass('hidden');
    $('.quiz-container').hide();
}

function restartQuiz() {
    $('#quizResult').addClass('hidden');
    $('.quiz-container').show();
    // Reset quiz state here if needed
}
// Example: Call this after the quiz is submitted
var userScore = 8; // User's score
var totalQuestions = 10;
var passingScore = 6; // Minimum passing score

if (userScore >= passingScore) {
    showResult(true, userScore, totalQuestions);
} else {
    showResult(false, userScore, totalQuestions);
}

 
</script>