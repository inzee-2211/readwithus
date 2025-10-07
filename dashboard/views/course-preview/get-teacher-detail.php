<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="col-xl-6 col-lg-10">
    <div class="author-bio">
        <div class="author-bio__head">
            <h5 class="bold-700"><?php echo Label::getLabel('LBL_ABOUT_TUTOR'); ?></h5>
            <div class="author-box">
                <div class="author-box__media">
                    <div class="avtar avtar--large" data-title="<?php echo strtoupper($teacher['user_first_name'][0]); ?>">
                        <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM], CONF_WEBROOT_FRONTEND); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] ?>">
                    </div>
                </div>
                <div class="author-box__content">
                    <div class="author-box__head">
                        <h4 class="author-name margin-0">
                            <?php echo ucfirst($teacher['user_first_name'] . ' ' . $teacher['user_last_name']) ?>
                        </h4>
                        <a href="<?php echo MyUtility::makeUrl('teachers', 'view', [$teacher['user_username']], CONF_WEBROOT_FRONTEND) ?>" class="underline color-primary padding-bottom-5">
                            <?php echo Label::getLabel('LBL_VIEW_PROFILE'); ?>
                        </a>
                    </div>
                    <div class="rating color-yellow">
                        <svg class="rating__media">
                            <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#rating"></use>
                        </svg>
                        <span class="rating__value"><?php echo $teacher['testat_ratings']; ?></span>
                        <span class="rating__count"><?php echo '(' . $teacher['testat_reviewes'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')'; ?></span>
                    </div>
                    <div class="teaches margin-top-4">
                        <span class="teaches__media">
                            <svg class="icon icon--teaches icon--18">
                                <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND ?>images/sprite.svg#icon-lecture">
                                </use>
                            </svg>
                        </span>
                        <span class="teaches__content">
                            <strong><?php echo Label::getLabel('LBL_COURSES'); ?></strong>
                            <?php echo $teacher['courses'] ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="author-bio__body margin-top-10">
            <?php if (!empty($teacher['user_biography'])) { ?>
                <h5 class="bold-700"><?php echo Label::getLabel('LBL_BIOGRAPHY'); ?></h5>
                <div class="author-box__desc">
                    <p><?php echo nl2br($teacher['user_biography']); ?></p>
                </div>
            <?php } ?>
        </div>
    </div>
</div>