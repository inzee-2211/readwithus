<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$levels = Course::getCourseLevels();
?>
<title><?php echo $course['course_title']; ?></title>
<!-- [ MAIN BODY ========= -->
<section class="section-view">
    <div class="container container--narrow">
        <div class="breadcrumbs">
            <ul>
                <li><a href="<?php echo MyUtility::makeUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                <li><a href="<?php echo MyUtility::makeUrl('Courses'); ?>"><?php echo Label::getLabel('LBL_Courses'); ?></a></li>
                <li><?php echo $course['course_title']; ?></li>
            </ul>
        </div>
        <div class="page-view">
            <div class="page-view__head">
                <hgroup>
                    <span class="course-card__label margin-top-4">
                        <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_cate_id'] ?>"><?php echo $course['cate_name']; ?></a>
                        <?php
                        if (!empty($course['subcate_name'])) {
                            echo ' / ';
                            ?>
                            <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_subcate_id'] ?>"><?php echo $course['subcate_name']; ?></a>
                        <?php } ?>
                    </span>
                    <h1 class="page-heading"><?php echo $course['course_title']; ?></h1>
                    <h4 class="page-subheading"><?php echo $course['course_subtitle']; ?></h4>
                </hgroup>
                <div class="course-counts margin-bottom-6">
                    <div class="course-counts__item">
                        <a class="rating">
                            <svg class="rating__media"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#rating"></use></svg>
                            <span class="rating__value"><?php echo $course['course_ratings']; ?></span>
                            <span class="rating__count"><?php echo $course['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEW(S)'); ?></span>
                        </a>
                    </div>
                    <div class="course-counts__item">
                        <div class="course-info">
                            <div class="course-info__media">
                                <svg class="icon icon--level">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-students"></use>
                                </svg>
                            </div>
                            <div class="course-info__title">
                                <strong><?php echo $course['course_students']; ?></strong>
                                <?php echo Label::getLabel('LBL_STUDENTS_ENROLLED'); ?>
                            </div>
                        </div>
                    </div>
                    <div class="course-counts__item">
                        <div class="course-info">
                            <div class="course-info__media">
                                <svg class="icon icon--level">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-level"></use>
                                </svg>
                            </div>
                            <div class="course-info__title">
                                <strong><?php echo Course::getCourseLevels($course['course_level']); ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="course-counts__item" title="<?php echo Label::getLabel('LBL_COURSE_LANGUAGE') ?>">
                        <div class="course-info">
                            <div class="course-info__media">
                                <svg class="icon icon--level icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-globe"></use>
                                </svg>
                            </div>
                            <div class="course-info__title"><strong><?php echo $course['course_clang_name']; ?></strong> </div>
                        </div>
                    </div>
                    <div class="course-counts__item">
                        <a href="#" class="profile-meta d-flex align-items-center">
                            <div class="profile-meta__media margin-right-4">
                                <!-- <span class="avtar avtar--xsmall avtar--round" data-title="<?php echo CommonHelper::getFirstChar($course['teacher_first_name']); ?>">
                                    <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $course['teacher_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo ucfirst($course['teacher_first_name']) . ' ' . ucfirst($course['teacher_last_name']) ?>">
                                  -->
                                    <img src="<?php echo 'http://readwithus.org.uk/image/show/6/0/LARGE'; ?>" style="width:70%;margin-bottom:20px;" alt="Readwithus">  
                               
                                <!-- </span> -->
                            </div>
                            <!-- <div class="profile-meta__details">
                                <span class="color-black bold-600">
                                    <?php //echo ucfirst($course['teacher_first_name'])
                                    echo 'Read With Us';
                                    ?>
                                </span>
                            </div> -->
                        </a>
                    </div>
                </div>
            </div>
            <div class="page-view__body">
                <div class="page-flex">
                    <!-- [ PANEL LARGE 1 ========= -->
                    <div class="page-flex__large">
                        <div class="course-preview">
                            <div class="course-preview__media ratio ratio--16by9">
                                <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'LARGE', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="<?php echo $course['course_title']; ?>">
                            </div>
                            <a href="javascript:void(0);" onclick="showPreviewVideo('<?php echo $course['course_id']; ?>');" class="course-preview__action">
                                <span></span>
                            </a>
                        </div>
                    </div>
                    <!-- ] -->
                    <!-- [ PANEL SMALL ========= -->
                    <div class="page-flex__small">
                        <div class="page-flex__sticky">
                            <div class="page-box">
                                <div class="page-box__head">
                                    <h5 class="bold-700"><?php echo Label::getLabel('LBL_THIS_COURSE_INCLUDES:'); ?></h5>
                                </div>
                                <div class="page-box__body">
                                    <div class="course-options">
                                        <ul>
                                            <?php 
                                            
                                            //echo '<pre>';print_r($course);die;
                                            if ($course['course_duration'] > 0) { ?>
                                                <li class="course-options__item">
                                                    <span class="course-options__item-media">
                                                        <svg class="icon icon--level">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-video">
                                                        </use>
                                                        </svg>
                                                    </span>
                                                    <span class="course-options__item-label">
                                                        <strong><?php echo YouTube::convertDuration($course['course_duration']); ?></strong>
                                                    </span>
                                                </li>
                                            <?php } ?>
                                            <li class="course-options__item">
                                                <span class="course-options__item-media">
                                                    <svg class="icon icon--level">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-lecture">
                                                    </use>
                                                    </svg>
                                                </span>
                                                <span class="course-options__item-label">
                                                    <strong><?php echo $course['course_lectures']; ?></strong>
                                                    <?php echo Label::getLabel("LBL_LECTURES") ?>
                                                </span>
                                            </li>

                                            <li class="course-options__item">
                                                <span class="course-options__item-media">
                                                    <svg class="icon icon--level">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-lecture">
                                                    </use>
                                                    </svg>
                                                </span>
                                                <span class="course-options__item-label">
                                                    <strong><?php echo $course['section_count']; ?></strong>
                                                    <?php echo Label::getLabel("LBL_QUIZ") ?>
                                                </span>
                                            </li>
                                            <?php if ($totalResources > 0) { ?>
                                                <li class="course-options__item">
                                                    <span class="course-options__item-media">
                                                        <svg class="icon icon--level">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-assets">
                                                        </use>
                                                        </svg>
                                                    </span>
                                                    <span class="course-options__item-label">
                                                        <strong><?php echo $totalResources; ?></strong> <?php echo Label::getLabel("LBL_DOWNLOADABLE_ASSETS") ?>
                                                    </span>
                                                </li>
                                            <?php } ?>
                                            <li class="course-options__item">
                                                <span class="course-options__item-media">
                                                    <svg class="icon icon--level">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-access">
                                                    </use>
                                                    </svg>
                                                </span>
                                                <span class="course-options__item-label">
                                                    <?php echo Label::getLabel('LBL_FULL_LIFETIME_ACCESS'); ?>
                                                </span>
                                            </li>
                                            <li class="course-options__item">
                                                <span class="course-options__item-media">
                                                    <svg class="icon icon--level">
                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-tv">
                                                    </use>
                                                    </svg>
                                                </span>
                                                <span class="course-options__item-label">
                                                    <?php echo Label::getLabel('LBL__ACCESS_ON_MOBILE_AND_TV'); ?>
                                                </span>
                                            </li>
                                            <?php if ($course['course_certificate'] == AppConstant::YES) { ?>
                                                <li class="course-options__item">
                                                    <span class="course-options__item-media">
                                                        <svg class="icon icon--level">
                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-certificate">
                                                        </use>
                                                        </svg>
                                                    </span>
                                                    <span class="course-options__item-label">
                                                        <?php echo Label::getLabel('LBL_CERTIFICATE_ON_COMPLETION'); ?>
                                                    </span>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="page-box__footer">
                                    <div class="course-pricing margin-bottom-4">
                                        <?php if (!$course['is_purchased']) { ?>
                                            <div class="course-pricing__head">
                                                <?php if ($course['course_type'] != Course::TYPE_FREE) { ?>
                                                    <span class="style-italic bold-600">
                                                        <?php echo Label::getLabel('LBL_AT_JUST_PRICE_OF'); ?>
                                                    </span>
                                                <?php } else { ?>
                                                    <h3 class="free-text color-red">
                                                        <?php echo Label::getLabel('LBL_FREE'); ?>
                                                    </h3>
                                                <?php } ?>
                                                <span class="course-pricing__price">
                                                    <?php
                                                    if ($course['course_type'] != Course::TYPE_FREE) {
                                                        echo CourseUtility::formatMoney($course['course_price']);
                                                    }
                                                    ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                        <div class="course-pricing__body">
                                            <?php if (!$course['is_purchased']) { ?>
                                                <?php if ($course['course_type'] != Course::TYPE_FREE) { ?>
                                                    <a href="javascript:void(0);" onclick="cart.addCourse(<?php echo $course['course_id']; ?>)" class="btn btn--block btn--primary btn--large">
                                                        <?php echo Label::getLabel("LBL_ENROLL_NOW"); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0);" onclick="cart.addFreeCourse(<?php echo $course['course_id']; ?>)" class="btn btn--block btn--primary btn--large">
                                                        <?php echo Label::getLabel("LBL_ENROLL_NOW"); ?>
                                                    </a>
                                                    <?php
                                                    $checkoutForm->setFormTagAttribute('class', 'd-none');
                                                    $checkoutForm->setFormTagAttribute('name', 'frmCheckout');
                                                    $checkoutForm->setFormTagAttribute('id', 'frmCheckout');
                                                    echo $checkoutForm->getFormHtml();
                                                    ?>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="<?php echo MyUtility::makeUrl('Tutorials', 'start', [$course['ordcrs_id']], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--block btn--primary btn--large">
                                                    <?php echo Label::getLabel("LBL_GO_TO_COURSE"); ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                    <a href="javascript:void(0)" onclick="toggleCourseFavorite('<?php echo $course['course_id'] ?>', this)" class="btn btn--bordered btn--favorite btn--block <?php echo ($course['is_favorite'] == AppConstant::YES) ? 'is-active' : ''; ?>" data-status="<?php echo $course['is_favorite']; ?>" tabindex="0">

                                        <svg class="icon icon--heart margin-right-2 fav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.32 25.32">
                                        <g>
                                        <path class="cls-1" d="M17.16,3.41c3.04,0,5.5,2.5,5.5,6,0,7-7.5,11-10,12.5-2.5-1.5-10-5.5-10-12.5,0-3.5,2.5-6,5.5-6,1.86,0,3.5,1,4.5,2,1-1,2.64-2,4.5-2Z"></path>
                                        </g>
                                        </svg>

                                        <?php echo Label::getLabel("LBL_FAVORITE"); ?>
                                    </a>
                                    <div class="sharing-view align-center margin-top-12">
                                        <h6><?php echo Label::getLabel('LBL_SHARE_THIS_COURSE'); ?></h6>
                                        <ul class="social--share clearfix">
                                            <li class="social--fb">
                                                <a class="st-custom-button" data-network="facebook" displayText='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' st_processed="yes">
                                                    <img alt="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg">
                                                </a>
                                            </li>
                                            <li class="social--tw">
                                                <a class="st-custom-button" data-network="twitter" displayText='<?php echo Label::getLabel('LBL_TWITTER'); ?>' title='<?php echo Label::getLabel('LBL_TWITTER'); ?>' st_processed="yes">
                                                    <img alt="<?php echo Label::getLabel('LBL_TWITTER'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg">
                                                </a>
                                            </li>
                                            <li class="social--pt">
                                                <a class="st-custom-button" data-network="pinterest" displayText='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' st_processed="yes">
                                                    <img alt="<?php echo Label::getLabel('LBL_PINTEREST'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg">
                                                </a>
                                            </li>
                                            <li class="social--mail">
                                                <a class="st-custom-button" data-network="email" displayText='<?php echo Label::getLabel('LBL_EMAIL'); ?>' title='<?php echo Label::getLabel('LBL_EMAIL'); ?>' st_processed="yes">
                                                    <img alt="<?php echo Label::getLabel('LBL_EMAIL'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg">
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ] -->
                    <!-- [ PANEL LARGE 2 ========= -->
                    <div class="page-flex__large">
                        <nav class="page-nav tabs tabs--line page-nav-js">
                            <ul>
                                <li class="is-active" data-id="panel-content-1">
                                    <a href="#panel-content-1"><?php echo Label::getLabel('LBL_OVERVIEW'); ?></a>
                                </li>
                                <li data-id="panel-content-2">
                                    <a href="#panel-content-2"><?php echo Label::getLabel('LBL_COURSE_CONTENT'); ?></a>
                                </li>
                                <!-- <li data-id="panel-content-3">
                                    <a href="#panel-content-3"><?php echo Label::getLabel('LBL_ABOUT_TUTOR'); ?></a>
                                </li> -->
                                <li data-id="panel-content-4">
                                    <a href="#panel-content-4"><?php echo Label::getLabel('LBL_REVIEWS'); ?> (<?php echo $course['course_reviews'] ?>)</a>
                                </li>
                            </ul>
                        </nav>
                        <div class="panels-container panels-container-js">
                            <!-- [ COURSE OVERVIEW ========= -->
                            <div data-id="panel-content-1" class="panel-content panel-content-js">
                                <div class="panel-content__head d-sm-none d-block panel-trigger-js">
                                    <h3><?php echo Label::getLabel('LBL_OVERVIEW'); ?></h3>
                                </div>
                                <div class="panel-content__body panel-target-js">
                                    <?php $types = IntendedLearner::getTypes(); ?>
                                    <?php if (isset($intendedLearners[IntendedLEarner::TYPE_LEARNING])) { ?>
                                        <div class="content-group">
                                            <h5 class="margin-bottom-6">
                                                <?php echo $types[IntendedLEarner::TYPE_LEARNING]; ?>
                                            </h5>
                                            <div class="check-list check-list--half">
                                                <ul>
                                                    <?php foreach ($intendedLearners[IntendedLEarner::TYPE_LEARNING] as $learner) { ?>
                                                        <li>
                                                            <?php echo $learner['coinle_response'] ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($intendedLearners[IntendedLEarner::TYPE_REQUIREMENTS])) { ?>
                                        <div class="content-group">
                                            <h5 class="margin-bottom-6">
                                                <?php echo $types[IntendedLEarner::TYPE_REQUIREMENTS]; ?>
                                            </h5>
                                            <div class="check-list check-list--half">
                                                <ul>
                                                    <?php foreach ($intendedLearners[IntendedLEarner::TYPE_REQUIREMENTS] as $learner) { ?>
                                                        <li>
                                                            <?php echo $learner['coinle_response'] ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php if (isset($intendedLearners[IntendedLEarner::TYPE_LEARNERS])) { ?>
                                        <div class="content-group">
                                            <h5 class="margin-bottom-6">
                                                <?php echo $types[IntendedLEarner::TYPE_LEARNERS]; ?>
                                            </h5>
                                            <div class="check-list check-list--half">
                                                <ul>
                                                    <?php foreach ($intendedLearners[IntendedLEarner::TYPE_LEARNERS] as $learner) { ?>
                                                        <li>
                                                            <?php echo $learner['coinle_response'] ?>
                                                        </li>
                                                    <?php } ?>
                                                </ul>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="content-group">
                                        <h5 class="margin-bottom-6">
                                            <?php echo Label::getLabel('LBL_DESCRIPTION'); ?>
                                        </h5>
                                        <div class="check-list check-list--half editor-content">
                                            <iframe srcdoc="<?php echo $course['course_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe>
                                        </div>
                                    </div>
                                    <?php if (count($course['course_tags']) > 0) { ?>
                                        <div class="content-group">
                                            <h5 class="margin-bottom-6">
                                                <?php echo Label::getLabel('LBL_COURSE_TAGS'); ?>
                                            </h5>
                                            <div class="tags">
                                                <?php foreach ($course['course_tags'] as $tag) { ?>
                                                    <a href="javascript:void(0);" class="tags__item badge badge--curve"><?php echo $tag; ?></a>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <!-- ] -->
                            <!-- [ COURSE CONTENT ========= -->
                            <div data-id="panel-content-2" class="panel-content panel-content-js">
                                <div class="panel-content__head  panel-trigger-js">
                                    <h3><?php echo Label::getLabel('LBL_COURSE_CONTENT'); ?></h3>
                                </div>
                                <div class="panel-content__body  panel-target-js">
                                    <div class="dots-list margin-top-8">
                                        <ul>
                                            <li>
                                                <?php echo $course['course_sections'] . ' ' . Label::getLabel('LBL_SECTIONS'); ?>
                                            </li>
                                            <li>
                                                <?php echo $course['course_lectures']; ?>
                                                <?php echo Label::getLabel("LBL_LECTURES") ?>
                                            </li>
                                            <?php if ($course['course_duration'] > 0) { ?>
                                                <li>
                                                    <?php
                                                    echo YouTube::convertDuration($course['course_duration']) . ' ' . Label::getLabel("LBL_TOTAL_LENGTH");
                                                    ?>
                                                </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                    <?php
                                    if (count($sections) > 0) {
                                        $i = 1;
                                        foreach ($sections as $section) {
                                            $lectures = ($section['lectures']) ?? [];
                                            if (count($lectures) > 0) {
                                                ?>
                                                <div class="course-layout">
                                                    <div class="course-layout__head">
                                                        <div class="d-sm-flex justify-content-sm-between">
                                                            <div class="course-layout__left">
                                                                <span class="step-caption">
                                                                    <?php echo Label::getLabel('LBL_SECTION'); ?>
                                                                </span>
                                                                <span class="step-counter">
                                                                    <?php echo $section['section_order'] ?>
                                                                </span>
                                                            </div>
                                                            <div class="course-layout__right">
                                                                <div class="course-layout__right">
                                                                    <div class="course-content">
                                                                        <h5 class="margin-bottom-6">
                                                                            <?php echo $section['section_title'] ?>
                                                                        </h5>
                                                                        <p><?php echo $section['section_details']; ?></p>
                                                                        <div class="course-counts">
                                                                            <div class="course-counts__item">
                                                                                <div class="course-info">
                                                                                    <div class="course-info__media">
                                                                                        <svg class="icon icon--level">
                                                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-time">
                                                                                        </use>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div class="course-info__title">
                                                                                        <?php echo Label::getLabel('LBL_TIME'); ?>
                                                                                        <strong>
                                                                                            <?php echo YouTube::convertDuration($section['section_duration']); ?>
                                                                                        </strong>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="course-counts__item">
                                                                                <div class="course-info">
                                                                                    <div class="course-info__media">
                                                                                        <svg class="icon icon--level">
                                                                                        <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-lecture">
                                                                                        </use>
                                                                                        </svg>
                                                                                    </div>
                                                                                    <div class="course-info__title">
                                                                                        <?php echo Label::getLabel("LBL_LECTURES") ?>
                                                                                        <strong>
                                                                                            <?php echo $section['section_lectures']; ?>
                                                                                        </strong>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                            <div class="course-counts__item">
                                                                                <a href="javascript:void(0);" onclick="showLectures(this, '<?php echo $i; ?>');" class="course-trigger course-trigger-js <?php echo ($i == 1) ? 'is-active' : ''; ?>">
                                                                                    <?php echo Label::getLabel('LBL_SEE_ALL') ?>
                                                                                </a>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="course-layout__body lecturesListJs<?php echo $i; ?> " style="display:<?php echo ($i == 1) ? 'block' : 'none'; ?>">
                                                        <div class="d-flex justify-content-between">
                                                            <div class="course-layout__left"></div>
                                                            <div class="course-layout__right">
                                                                <div class="course-topic-list">
                                                                    <?php
                                                                    foreach ($section['lectures'] as $lesson) {
                                                                        $showPreview = false;
                                                                        $rsrcId = array_search($lesson['lecture_id'], $videos);
                                                                        if ($rsrcId && $lesson['lecture_is_trial']) {
                                                                            $showPreview = true;
                                                                        }
                                                                        ?>
                                                                        <div class="course-topic">
                                                                            <?php if ($showPreview) { ?>
                                                                                <a href="javascript:void(0);" onclick="openMedia('<?php echo $rsrcId ?>');" class="course-topic__action">
                                                                                <?php } ?>
                                                                                <div class="course-topic__title">
                                                                                    <svg class="icon icon--play icon--xsmall margin-right-3 color-gray-800">
                                                                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-play">
                                                                                    </use>
                                                                                    </svg>
                                                                                    <span class="course-topic__name">
                                                                                        <?php echo $lesson['lecture_title'] ?>
                                                                                    </span>
                                                                                </div>
                                                                                <div class="course-topic__content">
                                                                                    <?php if ($showPreview) { ?>
                                                                                        <span class="course-topic__preview">
                                                                                            <?php echo Label::getLabel('LBL_PREVIEW'); ?>
                                                                                        </span>
                                                                                    <?php } ?>
                                                                                    <span class="course-topic__time">
                                                                                        <?php
                                                                                        echo $duration = YouTube::convertDuration($lesson['lecture_duration'], true, true, true, false);
                                                                                        ?>
                                                                                    </span>
                                                                                </div>
                                                                                <?php if ($showPreview) { ?>
                                                                                </a>
                                                                            <?php } ?>
                                                                        </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php
                                            }
                                            $i++;
                                        }
                                    }
                                    ?>
                                </div>
                            </div>
                            
                            <div data-id="panel-content-4" class="panel-content panel-content-js">
                                <div class="panel-content__head  panel-trigger-js">
                                    <h3><?php echo Label::getLabel('LBL_RATINGS_&_REVIEWS'); ?></h3>
                                </div>
                                <div class="panel-content__body  panel-target-js">
                                    <div class="reviews-section margin-top-14">
                                        <div class="reviews-section__head">
                                            <div class="reviews-stats">
                                                <div class="row justify-content-between">
                                                    <div class="col-4 col-sm-2">
                                                        <div class="reviews-total">
                                                            <div class="reviews-media">
                                                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 110 110">
                                                                <g transform="translate(-28.999 -29)">
                                                                <path d="M892.348,2341l17.582,31.851,35.759,6.861L920.8,2406.26l4.518,36.091-32.967-15.445-32.968,15.445,4.518-36.091-24.892-26.546,35.759-6.861L892.348,2341" transform="translate(-808.008 -2308.001)" />
                                                                </g>
                                                                </svg>
                                                                <span class="reviews-count">
                                                                    <?php echo $course['course_ratings'] ?>
                                                                </span>
                                                            </div>
                                                            <div class="reviews-value">
                                                                <?php echo $course['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS'); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-8 col-sm-6">
                                                        <div class="reviews-counter">
                                                            <?php foreach ($reviews as $review) { ?>
                                                                <div class="reviews-counter__item">
                                                                    <div class="reviews-progress">
                                                                        <div class="reviews-progress__value">
                                                                            <?php echo $review['rating'] ?>
                                                                        </div>
                                                                        <div class="reviews-progress__content">
                                                                            <div class="progress progress--small progress--round">
                                                                                <?php if ($review['percent'] > 0) { ?>
                                                                                    <div class="progress__bar bg-yellow" role="progressbar" style="width:<?php echo $review['percent'] ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                                                                <?php } ?>
                                                                            </div>
                                                                        </div>
                                                                        <div class="reviews-progress__value">
                                                                            <?php
                                                                            if ($review['count'] > 0) {
                                                                                echo '(' . $review['count'] . ')';
                                                                            }
                                                                            ?>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-3 col-md-4 col-xl-3">
                                                        <?php if ($canRate) { ?>
                                                            <div class="reviews-submission">
                                                                <p class="margin-bottom-3 margin-top-4 align-center">
                                                                    <?php echo Label::getLabel('LBL_HAVE_YOU_USED_THIS_COURSE?') ?>
                                                                </p>
                                                                <a href="javascript:void(0);" onclick="feedbackForm('<?php echo $course['ordcrs_id']; ?>')" class="btn color-primary btn--bordered btn--block">
                                                                    <?php echo Label::getLabel('LBL_RATE_IT_NOW') ?>
                                                                </a>
                                                            </div>
                                                        <?php } ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php echo $frm->getFormHtml(); ?>
                                        </div>
                                        <div class="reviews-section__body" id="reviewsListingJs">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- ] -->
                </div>
            </div>
        </div>
    </div>
</section>
<?php if ($moreCourses) { ?>
    <section class="section section--gray padding-bottom-20">
        <div class="container container--narrow">
            <div class="section__head d-flex justify-content-between align-items-center">
                <h3>
                    <?php
                    $label = Label::getLabel('LBL_MORE_COURSES_FROM') . ' Read With Us';
                    echo str_replace('{teacher-name}', '<strong class="bold-700">' . ucfirst($course['teacher_first_name']) . '</strong>', $label);
                    ?>
                </h3>
            </div>
            <div class="section__body">
                <?php
                echo $this->includeTemplate('courses/more-courses.php', [
                    'moreCourses' => $moreCourses,
                    'siteLangId' => $siteLangId,
                    'siteUserId' => $siteUserId,
                ]);
                ?>
            </div>
        </div>
    </section>
<?php } ?>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>
