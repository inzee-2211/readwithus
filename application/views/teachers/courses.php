<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="slider slider--onethird slider-onethird-js">
    <?php foreach ($moreCourses as $crs) { ?>
        <div class="card-class-cover">
            <div class="card-class">
                <div class="card-class__head">
                    <div class="card-class__media ratio ratio--16by9">
                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                            <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME); ?>" alt="<?php echo $crs['course_title']; ?>">
                        </a>
                    </div>
                    <div class="card-class__elements d-flex justify-content-between align-items-center">
                        <a href="javascript:void(0)" onclick="toggleCourseFavorite('<?php echo $crs['course_id'] ?>', this)" class="mark-option <?php echo ($crs['is_favorite'] == AppConstant::YES) ? 'is-active' : ''; ?>" data-status="<?php echo $crs['is_favorite']; ?>" tabindex="0">
                            <svg class="icon icon--heart icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/sprite.svg#icon-heart"></use>
                            </svg>
                        </a>
                        <?php if ($crs['course_certificate'] == AppConstant::YES) { ?>
                            <span>
                                <div class="offers-ui">
                                    <span class="offers-ui__trigger cursor-default">
                                        <svg class="icon icon--offers icon--small margin-right-2">
                                            <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/sprite.svg#icon-course-certificate">
                                            </use>
                                        </svg>
                                        <span>
                                            <?php echo Label::getLabel('LBL_CERTIFICATE_OF_COMPLETION'); ?>
                                        </span>
                                    </span>
                                </div>
                            </span>
                        <?php } ?>
                    </div>
                </div>
                <div class="card-class__body">
                    <span class="card-class__subtitle">
                        <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_cate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['cate_name']); ?></a>
                        <?php
                        if (!empty($crs['subcate_name'])) {
                            echo ' / ' ?>
                            <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_subcate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['subcate_name']); ?></a>
                        <?php } ?>
                    </span>
                    <div class="card-class__title">
                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>" class="snakeline-hover">
                            <?php
                            echo (strlen($crs['course_title']) > 70) ? CommonHelper::renderHtml(substr($crs['course_title'], 0, 70)) . '...' : CommonHelper::renderHtml($crs['course_title']); ?>
                        </a>
                    </div>
                    <div class="card-element">
                        <div class="card-element__item">
                            <span><?php echo YouTube::convertDuration($crs['course_duration']); ?></span>
                        </div>
                        <div class="card-element__item">
                            <span><?php echo $crs['course_lectures'] . ' ' . Label::getLabel('LBL_LECTURES'); ?></span>
                        </div>

                        <div class="card-element__item">
                            <span><?php echo $crs['course_students'] . ' ' . Label::getLabel('LBL_STUDENTS'); ?></span>
                        </div>
                    </div>
                    <?php if ($crs['course_type'] != Course::TYPE_FREE) { ?>
                        <h4 class="card-price color-primary margin-top-5 bold-700">
                            <?php echo CourseUtility::formatMoney($crs['course_price']); ?>
                        </h4>
                    <?php } else { ?>
                        <h4 class="free-text color-red card-price margin-top-5 bold-700">
                            <?php echo Label::getLabel('LBL_FREE'); ?>
                        </h4>
                    <?php } ?>
                </div>
                <div class="card-class__footer">
                    <div class="row justify-content-between align-items-center">
                        <div class="col-sm-7">
                            <div class="profile-meta__details">
                                <div class="ratings">
                                    <svg class="icon icon--rating">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating'; ?>"></use>
                                    </svg>&nbsp;
                                    <span class="value"><?php echo $crs['course_ratings']; ?></span>
                                    <span class="count">(<?php echo $crs['course_reviews']; ?>)</span>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-5">
                            <div class="btn-group d-flex d-sm-block">
                                <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>" class="btn btn--primary-bordered btn--block d-block d-sm-none margin-right-1">
                                    <?php echo Label::getLabel('LBL_VIEW_DETAILS') ?>
                                </a>

                                <?php if (!$crs['is_purchased']) { ?>
                                    <?php if ($crs['course_type'] != Course::TYPE_FREE) { ?>
                                        <a href="javascript:void(0);" onclick="cart.addCourse(<?php echo $crs['course_id']; ?>)" class="btn btn--primary btn--block margin-left-1">
                                            <?php echo Label::getLabel("LBL_ENROLL_NOW"); ?>
                                        </a>
                                    <?php } else { ?>
                                        <a href="javascript:void(0);" onclick="cart.addFreeCourse(<?php echo $crs['course_id']; ?>)" class="btn btn--primary btn--block margin-left-1">
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
                                    <a href="<?php echo MyUtility::makeUrl('Tutorials', 'start', [$crs['ordcrs_id']], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--primary btn--block margin-left-1">
                                        <?php echo Label::getLabel("LBL_GO_TO_COURSE"); ?>
                                    </a>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php } ?>
</div>