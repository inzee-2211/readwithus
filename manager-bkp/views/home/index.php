<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<script src="https://www.google.com/jsapi"></script>
<!--main panel start here-->
<?php
$durationType = MyDate::getDurationTypesArr();
?>
<div class="page">
    <div class="container container-fluid">
        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/reve-1.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_LESSONS_REVENUE'); ?></h6>
                        <h3 class="counter" data-currency="1"><?php echo MyUtility::formatMoney($stats['ALL_LESSONS_REVENUE'] ?? 0); ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo MyUtility::formatMoney($stats['TM_LESSONS_REVENUE'] ?? 0); ?></strong></p>
                        <?php if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Lessons'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/reve-2.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_CLASSES_REVENUE'); ?></h6>
                        <h3 class="counter" data-currency="1"><?php echo MyUtility::formatMoney($stats['ALL_CLASSES_REVENUE'] ?? 0); ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo MyUtility::formatMoney($stats['TM_CLASSES_REVENUE'] ?? 0); ?></strong></p>
                        <?php if ($objPrivilege->canViewClassesOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Classes'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/reve-2.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_COURSES_REVENUE'); ?></h6>
                        <h3 class="counter" data-currency="1"><?php echo MyUtility::formatMoney($stats['ALL_COURSES_REVENUE'] ?? 0); ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo MyUtility::formatMoney($stats['TM_COURSES_REVENUE'] ?? 0); ?></strong></p>
                        <?php if ($objPrivilege->canViewCourses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('CourseOrders'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/reve-3.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_ADMIN_EARNINGS'); ?></h6>
                        <h3 class="counter" data-currency="1"><?php echo MyUtility::formatMoney($stats['ALL_ADMIN_EARNINGS'] ?? 0); ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo MyUtility::formatMoney($stats['TM_ADMIN_EARNINGS'] ?? 0); ?></strong></p>
                        <?php if ($objPrivilege->canViewSettlementsReport(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Settlements'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/total-lessons.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_TOTAL_LESSONS'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_LESSONS_TOTAL'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_LESSONS_TOTAL'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Lessons') . '?order_payment_status=' . Order::ISPAID; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/total-classes.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_TOTAL_CLASSES'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_CLASSES_TOTAL'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_CLASSES_TOTAL'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewGroupClasses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('GroupClasses'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/total-classes.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_TOTAL_COURSES'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_COURSES_TOTAL'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_COURSES_TOTAL'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewCourses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Courses'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/completed-lessons.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_COMPLETED_LESSONS'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_COMPLETED_LESSONS'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_COMPLETED_LESSONS'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Lessons') . '?order_payment_status=' . Order::ISPAID . '&ordles_status=' . Lesson::COMPLETED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/cancelled.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_CANCELLED_LESSONS'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_CANCELLED_LESSONS'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_CANCELLED_LESSONS'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Lessons') . '?order_payment_status=' . Order::ISPAID . '&ordles_status=' . Lesson::CANCELLED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/cancelled.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_CANCELLED_CLASSES'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_CANCELLED_CLASSES'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_CANCELLED_CLASSES'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewGroupClasses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('GroupClasses') . '?grpcls_status=' . GroupClass::CANCELLED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/cancelled.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_CANCELLED_COURSES'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_CANCELLED_COURSES'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_CANCELLED_COURSES'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewCourses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('CourseRefundRequests') . '?corere_status=' . Course::REFUND_APPROVED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-3 col-md-3 col-sm-3">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/completed-classes.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_COMPLETED_CLASSES'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_COMPLETED_CLASSES'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_COMPLETED_CLASSES'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewGroupClasses(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('GroupClasses') . '?grpcls_status=' . GroupClass::COMPLETED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/un-scheduled.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_UNSCHEDULED_LESSONS'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_UNSCHEDULE_LESSONS'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_UNSCHEDULE_LESSONS'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewLessonsOrders(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Lessons') . '?order_payment_status=' . Order::ISPAID . '&ordles_status=' . Lesson::UNSCHEDULED; ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="box box--white box--stats">
                    <div class="box__body">
                        <img src="<?php echo CONF_WEBROOT_URL ?>images/users.svg" alt="" class="stats__icon">
                        <h6 class="-txt-uppercase"><?php echo Label::getLabel('LBL_TOTAL_USERS'); ?></h6>
                        <h3 class="counter" data-currency="0"><?php echo $stats['ALL_USERS_TOTAL'] ?? 0; ?></h3>
                        <p><?php echo Label::getLabel('LBL_THIS_MONTH'); ?> <strong><?php echo $stats['TM_USERS_TOTAL'] ?? 0; ?></strong></p>
                        <?php if ($objPrivilege->canViewUsers(true)) { ?>
                            <a href="<?php echo MyUtility::makeUrl('Users'); ?>" class="stats__link"></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="gap"></div>

        <div class="box box--white">
            <div class="box__head padding-bottom-0">
                <h4><?php echo Label::getLabel('LBL_STATISTICS'); ?></h4>
            </div>
            <div class="box__body space">
                <div>
                    <ul class="nav nav--floated -clearfix theme--hovercolor statistics-nav-js">
                        <li><a class="active" rel="tabs_1" data-chart="true" href="javascript:void(0)"><?php echo Label::getLabel('LBL_COMMISSION_FROM_LESSONS'); ?></a></li>
                        <li><a rel="tabs_2" data-chart="true" href="javascript:void(0)"><?php echo Label::getLabel('LBL_COMMISSION_FROM_CLASSES'); ?></a></li>
                        <li><a rel="tabs_3" data-chart="true" href="javascript:void(0)"><?php echo Label::getLabel('LBL_COMMISSION_FROM_COURSES'); ?></a></li>
                        <li><a rel="tabs_4" data-chart="true" href="javascript:void(0)"><?php echo Label::getLabel('LBL_TOTAL_SIGN_UPS'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap statistics-tab-js">
                        <div id="tabs_1" class="tabs_panel" style="width:100%;height:100%">
                            <div id="lessonEarning--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                        </div>
                        <div id="tabs_2" class="tabs_panel" style="width:100%;height:100%">
                            <div id="classEarning--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                        </div>
                        <div id="tabs_3" class="tabs_panel" style="width:100%;height:100%">
                            <div id="courseEarning--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                        </div>
                        <div id="tabs_4" class="tabs_panel" style="width:100%;height:100%">
                            <div id="userSignups--js" class="ct-chart ct-perfect-fourth graph--sales"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="box--scroll box box--white box--height">
                    <div class="box__head">
                        <h4><?php echo Label::getLabel('LBL_TOP_CLASS_LANGUAGES') . ' (<span class="languageDurationType-js">' . $durationType[MyDate::TYPE_ALL] . '</span>)'; ?></h4>
                        <ul class="actions right">
                            <li class="droplink">
                                <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                <div class="dropwrap">
                                    <ul class="linksvertical">
                                        <?php
                                        foreach ($durationType as $key => $value) {
                                            $datetime = MyDate::getStartEndDate($key);
                                            $days = ($key == MyDate::TYPE_ALL) ? 0 : 1;
                                            $datetime['startDate'] = date('Y-m-d', strtotime($datetime['startDate']));
                                            $datetime['endDate'] = date('Y-m-d', strtotime($datetime['endDate'] . ' -' . $days . ' day'));
                                        ?>
                                            <li><a href="javascript:void(0);" onClick="getTopClassLanguage('<?php echo $key; ?>', '<?php echo $value; ?>')"><?php echo $value; ?> (<span> <?php echo $datetime['startDate'] . ' - ' . $datetime['endDate'] ?>)</span></a> </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="box__body">
                        <div class="scrollbar scrollbar-js">
                            <ul class="list list--vertical theme--txtcolor theme--hovercolor topClassLanguage"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="box--scroll box box--white box--height">
                    <div class="box__head">
                        <h4><?php echo Label::getLabel('LBL_TOP_LESSON_LANGUAGES') . ' (<span class="languageDurationType-js2">' . $durationType[MyDate::TYPE_ALL] . '</span>)'; ?></h4>
                        <ul class="actions right">
                            <li class="droplink">
                                <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                <div class="dropwrap">
                                    <ul class="linksvertical">
                                        <?php
                                        foreach ($durationType as $key => $value) {
                                            $datetime = MyDate::getStartEndDate($key);
                                            $days = ($key == MyDate::TYPE_ALL) ? 0 : 1;
                                            $datetime['startDate'] = date('Y-m-d', strtotime($datetime['startDate']));
                                            $datetime['endDate'] = date('Y-m-d', strtotime($datetime['endDate'] . ' -' . $days . ' day'));
                                        ?>
                                            <li><a href="javascript:void(0);" onClick="getTopLessonLanguage('<?php echo $key; ?>', '<?php echo $value; ?>')"><?php echo $value; ?> (<span> <?php echo $datetime['startDate'] . ' - ' . $datetime['endDate'] ?>)</span></a> </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="box__body">
                        <div class="scrollbar scrollbar-js">
                            <ul class="list list--vertical theme--txtcolor theme--hovercolor topLessonLanguage"></ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4">
                <div class="box--scroll box box--white box--height">
                    <div class="box__head">
                        <h4><?php echo Label::getLabel('LBL_TOP_COURSE_CATEGORIES') . ' (<span class="languageDurationType-js2">' . $durationType[MyDate::TYPE_ALL] . '</span>)'; ?></h4>
                        <ul class="actions right">
                            <li class="droplink">
                                <a href="javascript:void(0)"><i class="ion-android-more-vertical icon"></i></a>
                                <div class="dropwrap">
                                    <ul class="linksvertical">
                                        <?php
                                        foreach ($durationType as $key => $value) {
                                            $datetime = MyDate::getStartEndDate($key);
                                            $days = ($key == MyDate::TYPE_ALL) ? 0 : 1;
                                            $datetime['startDate'] = date('Y-m-d', strtotime($datetime['startDate']));
                                            $datetime['endDate'] = date('Y-m-d', strtotime($datetime['endDate'] . ' -' . $days . ' day'));
                                        ?>
                                            <li><a href="javascript:void(0);" onClick="getTopCourseCategories('<?php echo $key; ?>', '<?php echo $value; ?>')"><?php echo $value; ?> (<span> <?php echo $datetime['startDate'] . ' - ' . $datetime['endDate'] ?>)</span></a> </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="box__body">
                        <div class="scrollbar scrollbar-js">
                            <ul class="list list--vertical theme--txtcolor theme--hovercolor topCourseCategories"></ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="gap"></div>
        <div class="row">
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="box box--white box--height box--scroll">
                    <div class="box__head">
                        <h4><?php echo Label::getLabel('LBL_VISITORS_STATISTICS'); ?></h4>
                    </div>
                    <div class="box__body space">
                        <div class="graph-container">
                            <div id="visitsGraph" class="ct-chart ct-perfect-fourth graph--visitor"></div>
                        </div>

                    </div>
                </div>
            </div>
            <div class="col-lg-6 col-md-6 col-sm-6">
                <div class="box box--white box--height box--scroll">
                    <div class="box__head">
                        <h4>
                            <?php echo Label::getLabel('LBL_TRAFFIC') . '<span class="trafficDurationType-js">(' . $durationType[MyDate::TYPE_ALL] . ')</span>'; ?>
                        </h4>
                    </div>
                    <div class="box__body space">
                        <div class="graph-container">
                            <div id="piechart" class="ct-chart ct-perfect-fourth graph--traffic"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    var dataCurrencyLeft = '<?php echo MyUtility::getCurrencyLeftSymbol(); ?>';
    var dataCurrencyRight = '<?php echo MyUtility::getCurrencyRightSymbol(); ?>';
    var w = $('.tabs_panel_wrap').width();
    google.load('visualization', '1', {
        'packages': ['corechart', 'bar']
    });
</script>