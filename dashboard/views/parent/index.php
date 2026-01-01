<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_PARENT_DASHBOARD'); ?></h1>
            </div>

            <div class="page__body">
                <!-- Summary Stats -->
                <div class="stats-row margin-bottom-6">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_LINKED_CHILDREN'); ?></span>
                                    <h5><?php echo (int)$childrenCount; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--students icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#students'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>" class="stat__action"></a>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_PENDING_REQUESTS'); ?></span>
                                    <h5><?php echo (int)$pendingRequests; ?></h5>
                                </div>
                                <div class="stat__media bg-warning">
                                    <svg class="icon icon--notification icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#notification'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>" class="stat__action"></a>
                            </div>
                        </div>
                        
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_UPCOMING_LESSONS'); ?></span>
                                    <h5><?php echo count($upcomingLessons); ?></h5>
                                </div>
                                <div class="stat__media bg-primary">
                                    <svg class="icon icon--calendar icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#calendar'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="#" class="stat__action" onclick="alert('<?php echo Label::getLabel('LBL_FEATURE_COMING_SOON'); ?>');"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Upcoming Lessons -->
                <div class="page-panel margin-bottom-6">
                    <div class="page-panel__head">
                        <h4><?php echo Label::getLabel('LBL_UPCOMING_LESSONS'); ?></h4>
                    </div>
                    <div class="page-panel__body">
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
                                            <th><?php echo Label::getLabel('LBL_CHILD'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($upcomingLessons as $lesson) { 
                                            $child = array_filter($children, function($c) use ($lesson) {
                                                return $c['student_id'] == $lesson['child_id'];
                                            });
                                            $child = reset($child);
                                            $childName = $child ? htmlspecialchars($child['user_first_name'] . ' ' . $child['user_last_name']) : '-';
                                            $lessonTime = MyDate::formatDate($lesson['ordles_lesson_starttime'], true, null, $siteUser['user_timezone']);
                                        ?>
                                        <tr>
                                            <td><?php echo $lessonTime; ?></td>
                                            <td><?php echo htmlspecialchars($lesson['teacher_first_name'] . ' ' . $lesson['teacher_last_name']); ?></td>
                                            <td><?php echo $childName; ?></td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card__head">
                                <h5><?php echo Label::getLabel('LBL_QUICK_ACTIONS'); ?></h5>
                            </div>
                            <div class="card__body">
                                <div class="btn-group-vertical btn-group--full">
                                    <a href="<?php echo MyUtility::makeUrl('Parent', 'addChildForm', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--primary margin-bottom-2">
                                        <?php echo Label::getLabel('LBL_ADD_CHILD'); ?>
                                    </a>
                                    <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary margin-bottom-2">
                                        <?php echo Label::getLabel('LBL_MANAGE_CHILDREN'); ?>
                                    </a>
                                    <a href="<?php echo MyUtility::makeUrl('Messages', '', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary">
                                        <?php echo Label::getLabel('LBL_VIEW_MESSAGES'); ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card__head">
                                <h5><?php echo Label::getLabel('LBL_RECENT_CHILDREN'); ?></h5>
                            </div>
                            <div class="card__body">
                                <?php if (empty($children)) { ?>
                                    <div class="alert alert--info">
                                        <?php echo Label::getLabel('LBL_NO_CHILDREN_LINKED_YET'); ?>
                                    </div>
                                <?php } else { ?>
                                    <ul class="list">
                                        <?php foreach (array_slice($children, 0, 3) as $child) { 
                                            $fullName = htmlspecialchars($child['user_first_name'] . ' ' . $child['user_last_name']);
                                            $viewUrl = MyUtility::makeUrl('Parent', 'child', [$child['student_id']], CONF_WEBROOT_DASHBOARD);
                                        ?>
                                        <li class="list__item">
                                            <div class="list__title">
                                                <strong><?php echo $fullName; ?></strong>
                                            </div>
                                            <div class="list__details">
                                                <?php echo htmlspecialchars($child['user_email']); ?>
                                            </div>
                                            <div class="list__action">
                                                <a href="<?php echo $viewUrl; ?>" class="btn btn--primary btn--small">
                                                    <?php echo Label::getLabel('LBL_VIEW'); ?>
                                                </a>
                                            </div>
                                        </li>
                                        <?php } ?>
                                    </ul>
                                    <?php if (count($children) > 3) { ?>
                                    <div class="text-center margin-top-2">
                                        <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--link">
                                            <?php echo Label::getLabel('LBL_VIEW_ALL_CHILDREN'); ?>
                                        </a>
                                    </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>