<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (count($courses) == 0) {

    // ✅ If learner has no subscription → show custom message
    if (!empty($noActiveSubscription) && $siteUserType == User::LEARNER) { ?>
        <div class="no-data no-data--empty">
            <div class="no-data__img">
                <svg class="icon icon--empty" width="90" height="90" viewBox="0 0 90 90" aria-hidden="true">
                    <circle cx="45" cy="45" r="44" fill="none" stroke="currentColor" stroke-width="2" opacity="0.15"></circle>
                    <path d="M27 54c0-10 8-18 18-18s18 8 18 18" fill="none" stroke="currentColor" stroke-width="2" opacity="0.35"></path>
                    <path d="M33 38h24" stroke="currentColor" stroke-width="2" opacity="0.35"></path>
                </svg>
            </div>

            <h4><?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?></h4>
            <p><?php echo Label::getLabel('LBL_PLEASE_SUBSCRIBE_TO_ACCESS_COURSES'); ?></p>

            <a class="btn btn--primary" href="<?php echo MyUtility::makeUrl('Subscriptions'); ?>">
                <?php echo Label::getLabel('LBL_VIEW_PLANS'); ?>
            </a>
        </div>
    <?php
        return;
    }

    // otherwise keep default empty state (filters / no courses)
    $this->includeTemplate('_partial/no-record-found.php');
    return;
}

$requestStatuses = Course::getRefundStatuses();
?>

 
<div class="course-group">
    <!-- [ COURSE CARD ========= -->
    <?php 
    
   
    foreach ($courses as $course) {
            $course['can_view_course'] = $course['can_view_course'] ?? true;
        $course['can_edit_course'] = $course['can_edit_course'] ?? false;
        $course['can_delete_course'] = $course['can_delete_course'] ?? false;
        $course['can_cancel_course'] = $course['can_cancel_course'] ?? false;
        $course['can_rate_course'] = $course['can_rate_course'] ?? false;
        $course['can_retake_course'] = $course['can_retake_course'] ?? false;
        $course['can_download_certificate'] = $course['can_download_certificate'] ?? false;
        $course['crspro_progress'] = $course['crspro_progress'] ?? 0;
        $course['crspro_status'] = $course['crspro_status'] ?? CourseProgress::PENDING;
        $course['ordcrs_id'] = $course['ordcrs_id'] ?? 0;
        $course['crspro_id'] = $course['crspro_id'] ?? 0; ?>
        <div class="card-course">
            <div class="card-course__colum card-course__colum--first">
                <div class="ratio ratio--16by9">
                    <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL); ?>" alt="">
                </div>
            </div>
            <div class="card-course__colum card-course__colum--second">
                <div class="card-course__head">
                    <small class="card-course__subtitle uppercase color-gray-900">
                        <?php echo $course['cate_name'] ?>
                        <?php echo !empty($course['subcate_name']) ? ' / ' . $course['subcate_name'] : ''; ?>
                    </small>
                    <span class="card-course__title">
                        <?php echo $course['course_title'] ?>
                    </span>
                </div>
                <div class="card-course__body">
                    <div class="course-stats">
                        
                        <span class="course-stats__item">
                            <?php echo Label::getLabel('LBL_LECTURES') ?>
                            <strong> <?php echo $course['course_lectures'] ?></strong>
                        </span>
                  
                       
                         
                        
                        
                        </div>

                   
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php
                        $color = 'color-warning';
                        if ($course['course_status'] == Course::PUBLISHED) {
                            $color = 'color-success';
                        } elseif ($course['course_status'] == Course::SUBMITTED) {
                            $color = 'color-info';
                        }
                        ?>
                        <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                            <?php echo $courseStatuses[$course['course_status']]; ?>
                        </span>
                    <?php } else { ?>
                        <?php
                        $color = 'color-success';
                        if ($course['crspro_status'] == CourseProgress::CANCELLED) {
                            $color = 'color-danger';
                        } elseif ($course['crspro_status'] == CourseProgress::PENDING) {
                            $color = 'color-warning';
                        } elseif ($course['crspro_status'] == CourseProgress::IN_PROGRESS) {
                            $color = 'color-info';
                        }
                        ?>
                        <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                            <?php echo $orderStatuses[$course['crspro_status']]; ?>
                        </span>                        
                    <?php } ?>
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php if ($course['course_active'] == AppConstant::INACTIVE) { ?>
                            <span class="card-landscape__status badge color-danger badge--curve badge--small margin-left-0">
                                <?php echo AppConstant::getActiveArr($course['course_active']); ?>
                            </span>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($siteUserType == User::LEARNER) { ?>
                        <?php if (isset($course['corere_status'])) { ?>
                            <?php
                            $color = 'color-success';
                            if ($course['corere_status'] == Course::REFUND_DECLINED) {
                                $color = 'color-danger';
                            } elseif ($course['corere_status'] == Course::REFUND_PENDING) {
                                $color = 'color-warning';
                            }
                            ?>
                            <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                                <?php echo $requestStatuses[$course['corere_status']]; ?>
                            </span>
                        <?php } ?>
                    <?php } ?>

                   

                    <?php if ($siteUserType == User::LEARNER && (!isset($course['corere_status']) || $course['corere_status'] != Course::REFUND_APPROVED)) { ?>
                    <div class="course-progress margin-top-2">
                        <div class="course-progress__value"><?php echo Label::getLabel('LBL_COURSE_PROGRESS'); ?></div>
                        <div class="course-progress__content">
                            <div class="progress progress--xsmall progress--round">
                                <?php if ( $course['crspro_progress'] > 0) { ?>
                                <div class="progress__bar bg-green" role="progressbar" style="width:<?php echo $course['crspro_progress']; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="course-progress__value"><?php echo $course['crspro_progress']; ?>%</div>
                    </div>
                    <?php } ?> 
                </div>
               
            </div>
            <div class="card-course__colum card-course__colum--third">
                <div class="actions-group">
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php if ($course['course_sections'] > 0 && $course['course_lectures'] > 0) { ?>
                            <a href="<?php echo MyUtility::makeUrl('CoursePreview', 'index', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_PREVIEW'); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                <svg class="icon icon--enter icon--18">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#view-icon"></use>
                                </svg>
                                <div class="tooltip tooltip--top bg-black">
                                    <?php echo Label::getLabel('LBL_PREVIEW'); ?>
                                </div>
                            </a>
                        <?php } ?>
                    <?php } elseif ($course['can_view_course']) { ?>
                       <a href="<?php echo MyUtility::makeUrl('Tutorials', 'startByCourse', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_VIEW'); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                            <svg class="icon icon--enter icon--18">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#view-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_VIEW'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_edit_course']) { ?>
                        <a href="<?php echo MyUtility::makeUrl('Courses', 'form', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_EDIT'); ?>" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#edit"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_EDIT'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_delete_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_DELETE'); ?>" onclick="remove('<?php echo $course['course_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#delete-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_DELETE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_cancel_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CANCEL'); ?>" onclick="cancelForm('<?php echo $course['ordcrs_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#cancel"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_CANCEL'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_rate_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_RATE'); ?>" onclick="feedbackForm('<?php echo $course['ordcrs_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#review-star"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_RATE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_retake_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_RETAKE'); ?>" onclick="retake('<?php echo $course['crspro_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#retake"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_RETAKE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_download_certificate']) { ?>
                        <a href="<?php echo MyUtility::makeUrl('Certificates', 'index', [$course['crspro_id']], CONF_WEBROOT_DASHBOARD); ?>" target="_blank" title="<?php echo Label::getLabel('LBL_DOWNLOAD_CERTIFICATE'); ?>" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#download-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_DOWNLOAD_CERTIFICATE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- ] ========= -->
</div>
<?php
$pagingArr = [
    'page' => $post['page'],
    'pageSize' => $post['pagesize'],
    'pageCount' => ceil($recordCount / $post['pagesize']),
    'recordCount' => $recordCount,
    'callBackJsFunc' => 'goToSearchPage'
];

$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmPaging']);
