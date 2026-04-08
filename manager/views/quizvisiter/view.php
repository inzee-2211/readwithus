<div class="sectionhead">
    <h4><?php echo Label::getLabel('LBL_USER_ATTEMPT_HISTORY'); ?></h4>
</div>

<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><?php echo Label::getLabel('LBL_USER_DETAILS'); ?></h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong><?php echo Label::getLabel('LBL_NAME'); ?>:</strong>
                    <?php echo htmlspecialchars($userData['name']); ?></p>
                <p><strong><?php echo Label::getLabel('LBL_EMAIL'); ?>:</strong>
                    <?php echo htmlspecialchars($userData['email']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong><?php echo Label::getLabel('LBL_PHONE'); ?>:</strong>
                    <?php echo htmlspecialchars($userData['phone']); ?></p>
                <p><strong><?php echo Label::getLabel('LBL_PARENT_EMAIL'); ?>:</strong>
                    <?php echo htmlspecialchars($userData['parent_email']); ?></p>
            </div>
            <div class="col-md-12">
                <p><strong><?php echo Label::getLabel('LBL_TOTAL_ATTEMPTS'); ?>:</strong>
                    <?php echo $userData['quiz_attempts_count']; ?></p>
            </div>
        </div>
    </div>
</div>

<div class="tabs_panel">
    <?php if (!empty($attempts)) {
        foreach ($attempts as $index => $attempt) {
            ?>
            <div class="card mb-4 border">
                <div class="card-header bg-light border-bottom rounded-top clickable" data-toggle="collapse"
                    data-target="#attempt-<?php echo $attempt['attempt_id']; ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?php echo Label::getLabel('LBL_ATTEMPT'); ?>
                                #<?php echo $attempt['attempt_id']; ?></strong>
                            - <?php echo htmlspecialchars($attempt['subtopic_name']); ?>
                            <small
                                class="text-muted">(<?php echo date('d M Y H:i', strtotime($attempt['created_at'])); ?>)</small>
                        </div>
                        <div>
                            <span class="badge badge-<?php echo ($attempt['result'] == 'pass') ? 'success' : 'danger'; ?>">
                                <?php echo ucfirst($attempt['result']); ?>
                            </span>
                            <span><?php echo $attempt['marks_obtained'] . ' / ' . $attempt['total_marks']; ?></span>
                        </div>
                    </div>
                </div>

                <div id="attempt-<?php echo $attempt['attempt_id']; ?>" class="collapse show">
                    <div class="card-body">
                        <?php if (!empty($attempt['answers'])) { ?>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm">
                                    <thead>
                                        <tr>
                                            <th width="5%">#</th>
                                            <th width="40%"><?php echo Label::getLabel('LBL_QUESTION'); ?></th>
                                            <th width="20%"><?php echo Label::getLabel('LBL_USER_ANSWER'); ?></th>
                                            <th width="20%"><?php echo Label::getLabel('LBL_CORRECT_ANSWER'); ?></th>
                                            <th width="5%"><?php echo Label::getLabel('LBL_MARKS'); ?></th>
                                            <th width="10%"><?php echo Label::getLabel('LBL_RESULT'); ?></th>
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
                                                <td>
                                                    <?php echo $ans['is_correct'] ? '<span class="text-success">Correct</span>' : '<span class="text-danger">Incorrect</span>'; ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } else { ?>
                            <p class="text-center text-muted"><?php echo Label::getLabel('LBL_NO_ANSWERS_FOUND'); ?></p>
                        <?php } ?>
                    </div>
                </div>
            </div>
        <?php
        }
    } else { ?>
        <p class="alert alert-warning"><?php echo Label::getLabel('LBL_NO_ATTEMPT_DATA_FOUND'); ?></p>
    <?php } ?>
</div>