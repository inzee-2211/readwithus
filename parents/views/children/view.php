<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo $child['name']; ?> - <?php echo Label::getLabel('LBL_PROGRESS'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <a href="<?php echo MyUtility::makeUrl('ParentChildren'); ?>"
                    class="btn btn--bordered color-black"><?php echo Label::getLabel('LBL_BACK_TO_CHILDREN'); ?></a>
            </div>
        </div>
    </div>
    <div class="page__body">

        <!-- Courses Section -->
        <div class="section-head">
            <div class="section__heading">
                <h4><?php echo Label::getLabel('LBL_ENROLLED_COURSES'); ?></h4>
            </div>
        </div>
        <div class="box box--white mb-5">
            <div class="table-scroll">
                <table class="table table--styled table--responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_COURSE_NAME'); ?></th>
                            <th><?php echo Label::getLabel('LBL_PROGRESS'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($child['courses'] as $course) { ?>
                            <tr>
                                <td><?php echo $course['name']; ?></td>
                                <td><span class="badge badge--success"><?php echo $course['progress']; ?></span></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quizzes Section -->
        <div class="section-head">
            <div class="section__heading">
                <h4><?php echo Label::getLabel('LBL_QUIZZES_TAKEN'); ?></h4>
            </div>
        </div>
        <div class="box box--white mb-5">
            <div class="table-scroll">
                <table class="table table--styled table--responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_QUIZ_NAME'); ?></th>
                            <th><?php echo Label::getLabel('LBL_DATE'); ?></th>
                            <th><?php echo Label::getLabel('LBL_SCORE'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($child['quizzes'] as $quiz) { ?>
                            <tr>
                                <td><?php echo $quiz['name']; ?></td>
                                <td><?php echo $quiz['date']; ?></td>
                                <td><?php echo $quiz['score']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Upcoming Lessons -->
        <div class="section-head">
            <div class="section__heading">
                <h4><?php echo Label::getLabel('LBL_UPCOMING_LESSONS'); ?></h4>
            </div>
        </div>
        <div class="box box--white">
            <div class="table-scroll">
                <table class="table table--styled table--responsive">
                    <thead>
                        <tr>
                            <th><?php echo Label::getLabel('LBL_SUBJECT'); ?></th>
                            <th><?php echo Label::getLabel('LBL_TEACHER'); ?></th>
                            <th><?php echo Label::getLabel('LBL_DATE_TIME'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($child['upcoming_lessons'] as $lesson) { ?>
                            <tr>
                                <td><?php echo $lesson['subject']; ?></td>
                                <td><?php echo $lesson['teacher']; ?></td>
                                <td><?php echo $lesson['date']; ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>