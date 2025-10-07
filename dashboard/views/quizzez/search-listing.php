<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

if (count($allLessons) == 0) {
    $link = MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND);
    $variables = ['msgHeading' => Label::getLabel('LBL_NO_QUIZ_ATTEMPTS_FOUND!')];
    if ($siteUserType == User::LEARNER) {
        $variables['btn'] = '<a href="' . $link . '" class="btn btn--primary">' . Label::getLabel('LBL_FIND_TEACHER') . '</a>';
    }
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    return;
}
?>

<div class="results">
    <?php foreach ($allLessons as $lesson) {
        $isPassed = strtolower($lesson['result']) == 'pass';
        $scorePercent = $lesson['total_marks'] > 0
            ? round(($lesson['marks_obtained'] / $lesson['total_marks']) * 100)
            : 0;
        ?>
        <div class="lessons-group margin-top-10">
            <div class="card-landscape shadow bg-light padding-6 border-radius-6">
                <!-- LEFT Column -->
                <div class="card-landscape__colum card-landscape__colum--second">
                    <div class="card-landscape__head margin-bottom-3">
                        <span class="card-landscape__title bold-700 font-20 color-dark">
                            <?php echo Label::getLabel('LBL_Subtopic'); ?>: <?php echo $lesson['subtopic_name']; ?>
                        </span>
                    </div>
                    <div class="card-landscape__body">
                        <p class="margin-bottom-2">
                            <strong><?php echo Label::getLabel('LBL_Attempt_Date'); ?>:</strong>
                            <?php echo $lesson['attempt_date']; ?>
                        </p>
                        <div class="status-badge margin-top-2">
                            <span class="badge badge--rounded <?php echo $isPassed ? 'bg-success' : 'bg-danger'; ?>">
                                <?php echo $lesson['result']; ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- RIGHT Column -->
                <div class="card-landscape__colum card-landscape__colum--third">
                    <div class="card-landscape__actions">
                        <div class="profile-meta">
                            <div class="profile-meta__details">
                                <p><strong><?php echo Label::getLabel('LBL_Questions'); ?>:</strong>
                                    <?php echo $lesson['total_questions']; ?>
                                </p>
                                <p><strong><?php echo Label::getLabel('LBL_Correct'); ?>:</strong>
                                    <?php echo $lesson['total_correct']; ?>
                                </p>
                                <p><strong><?php echo Label::getLabel('LBL_Marks'); ?>:</strong>
                                    <?php echo $lesson['marks_obtained']; ?> / <?php echo $lesson['total_marks']; ?>
                                </p>
                                <div class="progress-bar margin-top-2" style="height: 8px; background-color: #e9ecef; border-radius: 4px;">
                                    <div style="width: <?php echo $scorePercent; ?>%; background-color: <?php echo $isPassed ? '#28a745' : '#dc3545'; ?>; height: 100%; border-radius: 4px;"></div>
                                </div>
                                <small><?php echo $scorePercent; ?>% Score</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div> <!-- .card-landscape -->
        </div> <!-- .lessons-group -->
    <?php } ?>
</div>

<?php
if ($post['view'] != AppConstant::VIEW_DASHBOARD_LISTING) {
    $pagingArr = [
        'pageSize' => $post['pagesize'],
        'page' => $post['pageno'],
        'recordCount' => $recordCount,
        'pageCount' => ceil($recordCount / $post['pagesize']),
    ];
    echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
}
?>
