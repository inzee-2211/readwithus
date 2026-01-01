<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
$fullName = htmlspecialchars(($child['user_first_name'] ?? '') . ' ' . ($child['user_last_name'] ?? ''));
?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_CHILD_DASHBOARD'); ?>: <?php echo $fullName; ?></h1>
                <div>
                    <a class="btn btn--secondary" href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
                        <?php echo Label::getLabel('LBL_BACK'); ?>
                    </a>
                </div>
            </div>

            <div class="page__body">
                <!-- Basic Info -->
                <div class="card margin-bottom-6">
                    <div class="card__head">
                        <h4><?php echo Label::getLabel('LBL_BASIC_INFORMATION'); ?></h4>
                    </div>
                    <div class="card__body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><?php echo Label::getLabel('LBL_EMAIL'); ?>:</strong> <?php echo htmlspecialchars($child['user_email'] ?? ''); ?></p>
                                <p><strong><?php echo Label::getLabel('LBL_RELATION'); ?>:</strong> <?php echo htmlspecialchars($child['parstd_relation'] ?? '-'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Courses -->
                    <div class="col-lg-6 margin-bottom-6">
                        <div class="card">
                            <div class="card__head">
                                <h4><?php echo Label::getLabel('LBL_COURSES'); ?></h4>
                            </div>
                            <div class="card__body">
                                <?php if (empty($courses)) { ?>
                                    <div class="alert alert--info">
                                        <?php echo Label::getLabel('LBL_NO_COURSES_ENROLLED'); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="table-scroll">
                                        <table class="table table--hover">
                                            <thead>
                                                <tr>
                                                    <th><?php echo Label::getLabel('LBL_COURSE'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_PROGRESS'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($courses as $course) { 
                                                    $progressClass = $course['ordcrs_progress'] >= 80 ? 'text-success' : ($course['ordcrs_progress'] >= 50 ? 'text-warning' : 'text-danger');
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($course['course_title'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <div class="progress">
                                                            <div class="progress-bar <?php echo $progressClass; ?>" style="width: <?php echo $course['ordcrs_progress']; ?>%">
                                                                <?php echo $course['ordcrs_progress']; ?>%
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <?php if ($course['ordcrs_status'] == OrderCourse::STATUS_COMPLETED) { ?>
                                                            <span class="badge badge--success"><?php echo Label::getLabel('LBL_COMPLETED'); ?></span>
                                                        <?php } else { ?>
                                                            <span class="badge badge--primary"><?php echo Label::getLabel('LBL_IN_PROGRESS'); ?></span>
                                                        <?php } ?>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Upcoming Lessons -->
                    <div class="col-lg-6 margin-bottom-6">
                        <div class="card">
                            <div class="card__head">
                                <h4><?php echo Label::getLabel('LBL_UPCOMING_LESSONS'); ?></h4>
                            </div>
                            <div class="card__body">
                                <?php if (empty($upcomingLessons)) { ?>
                                    <div class="alert alert--info">
                                        <?php echo Label::getLabel('LBL_NO_UPCOMING_LESSONS'); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="table-scroll">
                                        <table class="table table--hover">
                                            <thead>
                                                <tr>
                                                    <th><?php echo Label::getLabel('LBL_DATE_TIME'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_TEACHER'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_DURATION'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($upcomingLessons as $lesson) { 
                                                    $startTime = MyDate::formatDate($lesson['ordles_lesson_starttime'], true, null, $siteUser['user_timezone']);
                                                    $endTime = MyDate::formatDate($lesson['ordles_lesson_endtime'], true, null, $siteUser['user_timezone']);
                                                    $duration = round((strtotime($lesson['ordles_lesson_endtime']) - strtotime($lesson['ordles_lesson_starttime'])) / 60);
                                                ?>
                                                <tr>
                                                    <td><?php echo $startTime; ?></td>
                                                    <td><?php echo htmlspecialchars($lesson['teacher_first_name'] . ' ' . $lesson['teacher_last_name']); ?></td>
                                                    <td><?php echo $duration; ?> <?php echo Label::getLabel('LBL_MINUTES'); ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Quiz Attempts -->
                    <div class="col-lg-6 margin-bottom-6">
                        <div class="card">
                            <div class="card__head">
                                <h4><?php echo Label::getLabel('LBL_RECENT_QUIZZES'); ?></h4>
                            </div>
                            <div class="card__body">
                                <?php if (empty($quizAttempts)) { ?>
                                    <div class="alert alert--info">
                                        <?php echo Label::getLabel('LBL_NO_QUIZ_ATTEMPTS'); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="table-scroll">
                                        <table class="table table--hover">
                                            <thead>
                                                <tr>
                                                    <th><?php echo Label::getLabel('LBL_QUIZ'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_SCORE'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_DATE'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($quizAttempts as $quiz) { 
                                                    $attemptDate = MyDate::formatDate($quiz['quatt_attempted_on'], true, null, $siteUser['user_timezone']);
                                                    $scoreClass = $quiz['quatt_score'] >= 80 ? 'text-success' : ($quiz['quatt_score'] >= 50 ? 'text-warning' : 'text-danger');
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($quiz['quiz_title'] ?? 'N/A'); ?></td>
                                                    <td class="<?php echo $scoreClass; ?>">
                                                        <strong><?php echo $quiz['quatt_score']; ?>%</strong>
                                                    </td>
                                                    <td><?php echo $attemptDate; ?></td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>

                    <!-- Tutors -->
                    <div class="col-lg-6 margin-bottom-6">
                        <div class="card">
                            <div class="card__head">
                                <h4><?php echo Label::getLabel('LBL_TUTORS'); ?></h4>
                            </div>
                            <div class="card__body">
                                <?php if (empty($tutors)) { ?>
                                    <div class="alert alert--info">
                                        <?php echo Label::getLabel('LBL_NO_TUTORS'); ?>
                                    </div>
                                <?php } else { ?>
                                    <div class="table-scroll">
                                        <table class="table table--hover">
                                            <thead>
                                                <tr>
                                                    <th><?php echo Label::getLabel('LBL_TUTOR'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_TOTAL_LESSONS'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tutors as $tutor) { 
                                                    $tutorName = htmlspecialchars($tutor['user_first_name'] . ' ' . $tutor['user_last_name']);
                                                    $messageUrl = MyUtility::makeUrl('Messages', 'compose', ['teacher' => $tutor['user_id']], CONF_WEBROOT_DASHBOARD);
                                                ?>
                                                <tr>
                                                    <td><?php echo $tutorName; ?></td>
                                                    <td><?php echo $tutor['total_lessons']; ?></td>
                                                    <td>
                                                        <a href="<?php echo $messageUrl; ?>" class="btn btn--primary btn--small">
                                                            <?php echo Label::getLabel('LBL_MESSAGE'); ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>