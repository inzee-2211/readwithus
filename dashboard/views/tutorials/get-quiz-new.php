<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php if ($quizDetails) { ?>
    <style>
        .quiz-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 8px;
            margin-bottom: 20px;
            position: relative;
            width: 100%;
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
        .quiz-message {
            padding: 20px;
            text-align: center;
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 6px;
            margin-bottom: 20px;
        }
    </style>

    <?php if ($previousAttempt) { ?>
        <div class="quiz-message">
            <h4><?php echo Label::getLabel('LBL_QUIZ_ALREADY_ATTEMPTED'); ?></h4>
            <p><?php echo Label::getLabel('LBL_YOUR_PREVIOUS_SCORE') . ': ' . $previousAttempt['score'] . '%'; ?></p>
            <p><?php echo Label::getLabel('LBL_STATUS') . ': ' . ($previousAttempt['status'] == 2 ? 'Passed' : 'Failed'); ?></p>
            <button class="btn-submit" onclick="retakeQuiz()"><?php echo Label::getLabel('LBL_RETAKE_QUIZ'); ?></button>
        </div>
    <?php } ?>

    <div class="quiz-container">
        <div class="quiz-header">
            <h4><?php echo htmlspecialchars($quizDetails['quiz_title']); ?></h4>
            <h5><?php echo html_entity_decode($quizDetails['quiz_description']); ?></h5>
            <input type="hidden" id="quiz_id" value="<?php echo $quizDetails['quiz_id']; ?>">
            <input type="hidden" id="quiz_pass_percentage" value="<?php echo $quizDetails['quiz_pass_percentage']; ?>">
            <input type="hidden" id="courseId" value="<?php echo $courseId; ?>">
            <input type="hidden" id="lectureId" value="<?php echo $lectureId; ?>">
            <input type="hidden" id="progressId" value="<?php echo $progressId; ?>">
        </div>

        <div class="quiz-body">
            <form id="quizForm" method="post" action="javascript:void(0);">
                <?php foreach ($quizDetails['questions'] as $index => $question) { ?>
                    <div class="quiz-question">
                        <div style="display: flex; justify-content: space-between; align-items: center;">
                            <p style="flex: 1;">
                                <strong><?php echo ($index + 1) . '. ' . html_entity_decode($question['question_title'], ENT_QUOTES); ?></strong>
                            </p>
                            <p style="flex-shrink: 0; text-align: right;">
                                <strong><?php echo Label::getLabel('LBL_MARKS'); ?>:</strong> 
                                <?php echo htmlspecialchars($question['question_marks']); ?>
                            </p>
                        </div>

                        <?php if ($question['question_type'] === '2') { // Checkbox ?>
                            <div class="quiz-options">
                                <?php foreach ($question['randomized_options'] as $option) { ?>
                                    <div>
                                        <input type="checkbox" 
                                               id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>" 
                                               name="answers[<?php echo $question['question_id']; ?>][]" 
                                               value="<?php echo $option['id']; ?>">
                                        <label for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>">
                                            <?php echo $option['text']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } elseif ($question['question_type'] === '1') { // Radio ?>
                            <div class="quiz-options">
                                <?php foreach ($question['randomized_options'] as $option) { ?>
                                    <div>
                                        <input type="radio" 
                                               id="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>" 
                                               name="answers[<?php echo $question['question_id']; ?>]" 
                                               value="<?php echo $option['id']; ?>">
                                        <label for="q_<?php echo $question['question_id']; ?>_opt_<?php echo $option['id']; ?>">
                                            <?php echo $option['text']; ?>
                                        </label>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } elseif ($question['question_type'] === '3') { // Textarea ?>
                            <textarea id="q_<?php echo $question['question_id']; ?>" 
                                      name="answers[<?php echo $question['question_id']; ?>]" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="<?php echo Label::getLabel('LBL_ENTER_YOUR_ANSWER'); ?>"></textarea>
                        <?php } ?>
                    </div>
                <?php } ?>

                <div class="text-right">
                    <button type="submit" class="btn-submit" onclick="submitLectureQuiz();">
                        <?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
    function submitLectureQuiz() {
        let allAnswered = true;
        const unansweredQuestions = [];

        // Validate all questions are answered
        $('#quizForm .quiz-question').each(function() {
            const questionId = $(this).find('input[type="radio"], input[type="checkbox"], textarea').attr('name');
            const selectedAnswer = $(this).find('input[type="radio"]:checked, input[type="checkbox"]:checked, textarea').filter(function() {
                return $(this).val().trim() !== '';
            });

            if (!selectedAnswer.length) {
                allAnswered = false;
                unansweredQuestions.push(questionId);
            }
        });

        if (!allAnswered) {
            alert('<?php echo Label::getLabel('LBL_PLEASE_ANSWER_ALL_QUESTIONS'); ?>');
            return;
        }

        $('.btn-submit').prop('disabled', true).text('Submitting...');
        
        const quizId = $('#quiz_id').val();
        const quizPass = $('#quiz_pass_percentage').val();
        const courseId = $('#courseId').val();
        const lectureId = $('#lectureId').val();
        const progressId = $('#progressId').val();

        const form = $('#quizForm');
        form.append('<input type="hidden" name="quiz_id" value="' + quizId + '">');
        form.append('<input type="hidden" name="quiz_pass_percentage" value="' + quizPass + '">');
        form.append('<input type="hidden" name="course_id" value="' + courseId + '">');
        form.append('<input type="hidden" name="lecture_id" value="' + lectureId + '">');
        form.append('<input type="hidden" name="progress_id" value="' + progressId + '">');

        const data = form.serialize();
        
        $.ajax({
            url: '<?php echo MyUtility::makeUrl('Tutorials', 'submitLectureQuiz'); ?>',
            method: 'POST',
            data: data,
            dataType: 'json',
            success: function(response) {
                if (response.status === "success") {
                    alert('<?php echo Label::getLabel('LBL_QUIZ_SUBMITTED_SUCCESSFULLY'); ?>');
                    // Reload the lecture to update progress
                    loadLecture(lectureId);
                } else {
                    alert("Error: " + response.message);
                    $('.btn-submit').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
                }
            },
            error: function() {
                alert('<?php echo Label::getLabel('LBL_ERROR_SUBMITTING_QUIZ'); ?>');
                $('.btn-submit').prop('disabled', false).text('<?php echo Label::getLabel('LBL_SUBMIT_QUIZ'); ?>');
            }
        });
    }

    function retakeQuiz() {
        if (confirm('<?php echo Label::getLabel('LBL_CONFIRM_RETAKE_QUIZ'); ?>')) {
            // Reload the quiz page to start fresh
            getQuiz();
        }
    }
    </script>
<?php } else { ?>
    <div class="message-display no-skin">
        <div class="message-display__media">
            <svg><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use></svg>
        </div>
        <h4><?php echo stripslashes(Label::getLabel("LBL_NO_QUIZ_AVAILABLE")); ?></h4>
    </div>
<?php } ?>