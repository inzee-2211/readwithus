<div class="sectionhead">
    <h4><?php echo Label::getLabel('LBL_USER_ATTEMPT_SUMMARY'); ?></h4>
</div>

<div class="tabs_panel">
    <?php if (!empty($attemptData)) {
        $attempt = $attemptData[0]; // assuming single attempt for now
    ?>
        <div class="card mb-4">
            <div class="card-header bg-light border-bottom rounded-top">
                <h5 class="mb-2"><?php echo Label::getLabel('LBL_ATTEMPT_DETAILS'); ?></h5>

                <p class="mb-1"><strong><?php echo Label::getLabel('LBL_TOTAL_QUESTIONS'); ?>:</strong> <?php echo $attempt['total_questions']; ?></p>
                <p class="mb-1"><strong><?php echo Label::getLabel('LBL_CORRECT_ANSWERS'); ?>:</strong> <?php echo $attempt['total_correct']; ?></p>
                <p class="mb-1"><strong><?php echo Label::getLabel('LBL_MARKS_OBTAINED'); ?>:</strong> <?php echo $attempt['marks_obtained'] . '/' . $attempt['total_marks']; ?></p>
                <p class="mb-0"><strong><?php echo Label::getLabel('LBL_RESULT'); ?>:</strong> 
                    <span class="<?php echo ($attempt['result'] == 'pass') ? 'text-success' : 'text-danger'; ?>">
                        <?php echo ucfirst($attempt['result']); ?>
                    </span>
                </p>
            </div>

            <div class="card-body">
                <?php if (!empty($attempt['answers'])) { ?>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th><?php echo Label::getLabel('LBL_QUESTION'); ?></th>
                                 <th><?php echo Label::getLabel('LBL_USER_ANSWER'); ?></th>
                                <th><?php echo Label::getLabel('LBL_CORRECT_ANSWER'); ?></th>
                                <th><?php echo Label::getLabel('LBL_MARKS'); ?></th>
                                <th><?php echo Label::getLabel('LBL_DIFFICULTY'); ?></th>
                                <th><?php echo Label::getLabel('LBL_RESULT'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($attempt['answers'] as $idx => $ans) { ?>
                                <tr>
                                    <td><?php echo $idx + 1; ?></td>
                                    <td><?php echo $ans['question_title']; ?></td>
                                 
                                    <td>
                                        <?php echo $ans['user_answer'] ?: '<span class="text-muted">N/A</span>'; ?>
                                    </td>
                                    <td><?php echo ($ans['question_type'] != "Story-Based") ? $ans['actual_correct_answer'] : ''; ?>
</td>
                                    <td><?php echo $ans['marks_obtained']; ?></td>
                                    <td><?php echo $ans['difficult_level']; ?></td>
                                    <td>
                                        <?php echo $ans['is_correct'] ? '<span class="text-success">Correct</span>' : '<span class="text-danger">Incorrect</span>'; ?>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } else { ?>
                    <p><?php echo Label::getLabel('LBL_NO_QUESTIONS_FOUND'); ?></p>
                <?php } ?>
            </div>
        </div>
    <?php } else { ?>
        <p><?php echo Label::getLabel('LBL_NO_ATTEMPT_DATA_FOUND'); ?></p>
    <?php } ?>
</div>
