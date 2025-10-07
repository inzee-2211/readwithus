<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($topRatedTeachers) { ?>
    <section class="section padding-bottom-5">
        <div class="container container--narrow">
            <div class="section__head">
                <h2><?php echo Label::getLabel('Lbl_Top_Rated_Teachers'); ?></h2>
            </div>
            <div class="section__body">
                <div class="teacher-wrapper">
                    <div class="row">
                        <?php foreach ($topRatedTeachers as $topRatedTeacher) { ?>
                            <div class="col-auto col-sm-6 col-md-6 col-lg-4 col-xl-3">
                                <div class="tile">
                                    <div class="tile__head">
                                        <div class="tile__media ratio ratio--1by1">
                                            <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $topRatedTeacher['user_id'], Afile::SIZE_MEDIUM]), CONF_IMG_CACHE_TIME, '.jpg') ?>" alt="">
                                        </div>
                                    </div>
                                    <div class="tile__body">
                                        <a class="tile__title" href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$topRatedTeacher['user_username']]); ?>">
                                            <h4><?php echo $topRatedTeacher['user_first_name'] . ' ' . $topRatedTeacher['user_last_name']; ?></h4>
                                        </a>
                                        <div class="info-wrapper">
                                            <div class="info-tag location">
                                                <svg class="icon icon--location">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#location'; ?>"></use>
                                                </svg>
                                                <span class="lacation__name"><?php echo $topRatedTeacher['country_name']; ?></span>
                                            </div>
                                            <div class="info-tag ratings">
                                                <svg class="icon icon--rating">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating' ?>"></use>
                                                </svg>
                                                <span class="value"><?php echo $topRatedTeacher['teacher_rating'] ?? 0; ?></span>
                                                <span class="count"><?php echo '(' . $topRatedTeacher['totReviews'] . ')'; ?></span>
                                            </div>
                                        </div>
                                        <div class="card__row--action ">
                                            <a href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$topRatedTeacher['user_username']]); ?>" class="btn btn--primary btn--block"><?php echo Label::getLabel('LBL_View_Details'); ?></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php
}
