<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="slider slider-onethird slider-onethird-js">
    <?php foreach ($moreCourses as $crs) { ?>
        <!-- [ SLIDER ITEM ========= -->
        <div>
            <!-- [ COURSE CARD ========= -->
            <div class="card-tile-cover">
                <div class="card-tile">
                    <div class="card-tile__head">
                        <div class="course-media ratio ratio--16by9 22">
                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="<?php echo $crs['course_title']; ?>">
                            </a>
                        </div>
                        <a href="javascript:void(0)" onclick="toggleCourseFavorite('<?php echo $crs['course_id'] ?>', this)" class="mark-option <?php echo ($crs['is_favorite'] == AppConstant::YES) ? 'is-active' : ''; ?>" data-status="<?php echo $crs['is_favorite']; ?>" tabindex="0">
                            <svg class="icon icon--heart icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/sprite.svg#icon-heart"></use>
                            </svg>
                        </a>
                        <?php if ($crs['course_certificate'] == AppConstant::YES) { ?>
                            <span class="course-tag">
                                <svg class="icon icon--award margin-right-1">
                                    <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND; ?>images/sprite.svg#icon-course-certificate">
                                    </use>
                                </svg>
                                <span>
                                    <?php echo Label::getLabel('LBL_CERTIFICATE_OF_COMPLETION'); ?>
                                </span>
                            </span>
                        <?php } ?>
                    </div>
                    <div class="card-tile__body">
                        <span class="card-tile__label">
                            <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_cate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['cate_name']); ?></a>
                                <?php
                                if (!empty($crs['subcate_name'])) {
                                    echo ' / ' ?>
                                    <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_subcate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['subcate_name']);?></a>
                                <?php } ?>
                        </span>
                        <h5 class="card-tile__title">
                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>" class="snakeline-hover">
                                <?php
                                echo (strlen($crs['course_title']) > 70) ? CommonHelper::renderHtml(substr($crs['course_title'], 0, 70)) . '...' : CommonHelper::renderHtml($crs['course_title']); ?>
                            </a>
                        </h5>
                        <div class="card-element">
                            <div class="card-element__item">
                                <span class="icon-element__label">
                                    <?php echo YouTube::convertDuration($crs['course_duration']); ?>
                                </span>
                            </div>
                            <div class="card-element__item">
                                <span class="icon-element__label">
                                    <?php echo $crs['course_lectures'] . ' ' . Label::getLabel('LBL_LECTURES'); ?>
                                </span>
                            </div>
                            <div class="card-element__item">
                                <span class="icon-element__label">
                                    <?php echo $crs['course_students'] . ' ' . Label::getLabel('LBL_STUDENTS'); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="card-tile__footer">
                        <div class="teacher-details">
                            <a href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$crs['teacher_username']]) ?>" class="profile-meta d-flex align-items-center">
                                <div class="profile-meta__media margin-right-4">
                                    <span class="avtar" data-title="M"><img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $crs['teacher_id'], Afile::SIZE_SMALL]); ?>" alt=" <?php echo ucwords($crs['teacher_first_name'] . ' ' . $crs['teacher_last_name']); ?>" /></span>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black margin-bottom-1 style-ellipsis"> <?php echo ucwords($crs['teacher_first_name'] . ' ' . $crs['teacher_last_name']); ?></p>
                                </div>
                            </a>
                        </div>
                        <div class="course-price-reviews">
                            <div class="d-flex align-items-center">
                                <div class="padding-right-4">
                                    <?php if ($crs['course_type'] != Course::TYPE_FREE) { ?>
                                        <h4 class="color-primary bold-700">
                                            <?php echo CourseUtility::formatMoney($crs['course_price']); ?>
                                        </h4>
                                    <?php } else { ?>
                                        <h4 class="free-text color-red bold-700">
                                            <?php echo Label::getLabel('LBL_FREE'); ?>
                                        </h4>
                                    <?php } ?>
                                </div>
                                <div>
                                    <div class="rating">
                                        <svg class="rating__media">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#rating"></use>
                                        </svg>
                                        <span class="rating__value">
                                            <?php echo $crs['course_ratings']; ?>
                                        </span>
                                        <span class="rating__count">
                                            <?php echo '(' . $crs['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')'; ?>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ] -->
        </div>
        <!-- ] -->
    <?php } ?>
</div>