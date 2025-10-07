<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frmSrch->setFormTagAttribute('onsubmit', 'searchLessons(this); return(false);');
$frmSrch->setFormTagAttribute('class', 'd-none');
?>
<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_DASHBOARD'); ?></h1>
            </div>
            <div class="page__body">
                <div class="stats-row margin-bottom-6">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_LESSONS_SCHEDULED'); ?></span>
                                    <h5><?php echo $schLessonCount; ?></h5>
                                </div>
                                <div class="stat__media bg-yellow">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#planning'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Lessons') . '?ordles_status=' . Lesson::SCHEDULED; ?>" class="stat__action"></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_TOTAL_LESSONS'); ?></span>
                                    <h5><?php echo $totalLesson; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#stats_1'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Lessons') . '?ordles_status=-1'; ?>" class="stat__action"></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_TOTAL_CLASSES'); ?></span>
                                    <h5><?php echo $totalClasses; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#stats_1'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Classes') . '?ordcls_status=-1'; ?>" class="stat__action"></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="stats-row margin-bottom-6">
                    <div class="row align-items-center">
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_TOTAL_COURSES'); ?></span>
                                    <h5><?php echo $totalCourses; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#stats_1'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Courses'); ?>" class="stat__action"></a>
                            </div>
                        </div>
                        <div class="col-lg-6 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_WALLET_BALANCE'); ?></span>
                                    <h5><?php echo MyUtility::formatMoney($walletBalance); ?></h5>
                                </div>
                                <div class="stat__media bg-primary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#stats_2'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Wallet'); ?>" class="stat__action"></a>
                            </div>
                        </div>

                    </div>
                </div>
                <?php echo $frmSrch->getFormHtml(); ?>
                <div class="page-content">
                    <div class="results" id="listItemsLessons">
                    </div>
                </div>
            </div>