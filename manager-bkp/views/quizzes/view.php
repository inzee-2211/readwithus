<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$yesNoArr = AppConstant::getYesNoArr();
?>

<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_QUIZ_DETAIL'); ?></h4>
    </div>

    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">

                <!-- Quiz Settings Section -->
                <div class="sectionhead">
                    <h4><?php echo Label::getLabel('LBL_QUIZ_SETTINGS'); ?></h4>
                </div>
                <div class="tabs_panel">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_TITLE'); ?>:</label>
                                <strong><?php echo $quizData['quiz_title']; ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_DESCRIPTION'); ?>:</label>
                                <strong><?php echo html_entity_decode($quizData['quiz_description'], ENT_QUOTES, 'UTF-8'); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_DURATION'); ?>:</label>
                                <strong><?php echo $quizData['quiz_duration'] . ' mins'; ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_PASS_PERCENTAGE'); ?>:</label>
                                <strong><?php echo $quizData['quiz_pass_percentage']; ?>%</strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_VALIDITY'); ?>:</label>
                                <strong><?php echo $quizData['quiz_validity'] . ' days'; ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_FAIL_MESSAGE'); ?>:</label>
                                <strong><?php echo $quizData['quiz_fail_message']; ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_PASS_MESSAGE'); ?>:</label>
                                <strong><?php echo $quizData['quiz_pass_message']; ?></strong>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_OFFER_CERTIFICATE'); ?>:</label>
                                <strong><?php echo AppConstant::getYesNoArr()[$quizData['quiz_offer_certificate']]; ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUIZ_STATUS'); ?>:</label>
                                <strong><?php echo $quizData['quiz_status'] == 1 ? 'Active' : 'Inactive'; ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Questions Section -->
                <div class="sectionhead">
                    <h4><?php echo Label::getLabel('LBL_QUESTIONS'); ?></h4>
                </div>
                <div class="tabs_panel">
                    <?php foreach ($questionsData as $key => $question): ?>
                        <div class="question-item">
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_QUESTION'); ?> <?php echo ($key + 1); ?>:</label>
                                <strong><?php echo $question['question_title'].' (' .$question['question_added_on'].')'; ?></strong>
                            </div>
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_CATEGORY'); ?>:</label>
                                <strong><?php echo $question['catname']; ?></strong>
                            </div>
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_TYPE'); ?>:</label>
                                <strong>
                                    <?php 
                                        switch($question['question_type']) {
                                            case 1:
                                                echo 'Single Choice';
                                                break;
                                            case 2:
                                                echo 'Multiple Choice';
                                                break;
                                            case 3:
                                                echo 'Text-Based Question';
                                                break;
                                            default:
                                                echo 'Unknown Type';
                                        }
                                    ?>
                                </strong>
                            </div>
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_OPTIONS'); ?>:</label>
                                <?php if ($question['question_type'] == 1 || $question['question_type'] == 2): ?>
                                    <ul>
                                        <?php for ($i = 1; $i <= 4; $i++): ?>
                                            <?php if ($question["question_option_$i"]): ?>
                                                <li><strong><?php echo $question["question_option_$i"]; ?></strong></li>
                                            <?php endif; ?>
                                        <?php endfor; ?>
                                    </ul>
                                <?php else: ?>
                                    <p><strong>Open-ended question</strong></p>
                                <?php endif; ?>
                            </div>
                            <div class="field-set">
                                <label class="field_label"><?php echo Label::getLabel('LBL_CORRECT_ANSWER'); ?>:</label>
                                <strong>
                                    <?php
                                        if ($question['question_type'] == 1 || $question['question_type'] == 2) {
                                            $correctAnswers = explode(',', $question['question_answers']);
                                            $correctAnswerTexts = [];

                                            foreach ($correctAnswers as $answer) {
                                                $correctOption = 'question_option_' . $answer;
                                                if (isset($question[$correctOption])) {
                                                    $correctAnswerTexts[] = $question[$correctOption];
                                                }
                                            }

                                            echo implode(', ', $correctAnswerTexts);
                                        } else {
                                            echo 'N/A';  
                                        }
                                    ?>
                                </strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .field-set {
        margin-bottom: 20px;
        display: flex;
        flex-wrap: wrap;
        align-items: center;
    }

    .field_label {
        font-weight: bold;
        margin-right: 10px;
        font-size: 14px;
        color: #333;
        flex-basis: 30%;
    }

    .tabs_panel {
        padding: 20px;
        border-radius: 5px;
        background-color: #f9f9f9;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        margin-top: 15px;
    }

    .tabs_panel h4 {
        font-size: 18px;
        margin-bottom: 15px;
    }

    .tabs_panel strong {
        font-weight: normal;
        color: #333;
    }

    .question-item {
        margin-bottom: 25px;
    }

    .tabs_panel ul {
        list-style-type: disc;
        margin-left: 20px;
    }

    .tabs_panel .row {
        display: flex;
        flex-wrap: wrap;
    }

    .col-md-6 {
        flex-basis: 48%;
        margin-bottom: 20px;
    }

    .col-md-6:last-child {
        margin-bottom: 0;
    }

    .sectionhead {
        margin-bottom: 10px;
    }

    .sectionhead h4 {
        font-size: 18px;
        margin-bottom: 10px;
        color: #333;
    }
</style>
