<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<section class="section section--certificate">
    <div class="container container--narrow">
        <div class="layout-flex">
            <div class="layout-flex__large">
                <div class="certificate-media margin-bottom-4">
                    <object data="<?php echo MyUtility::generateUrl('Image', 'showPdf', [Afile::TYPE_CERTIFICATE_PDF, $ordcrsId], CONF_WEBROOT_FRONTEND) . '?t=' . time() ?>#toolbar=0&navpanes=0&scrollbar=0" type="application/pdf" width="100%" height="603">
                        <p>Your web browser doesn't have a PDF plugin.
                            Instead you can <a class="underline color-primary" target="_blank" href="<?php echo MyUtility::generateUrl('Image', 'showPdf', [Afile::TYPE_CERTIFICATE_PDF, $ordcrsId], CONF_WEBROOT_FRONTEND) ?>#toolbar=0&navpanes=0&scrollbar=0">click here</a> to access the file directly.</p>
                    </object>
                </div>
                <div class="certificate-desc padding-top-2">
                    <p class="font-small"><?php echo Label::getLabel('LBL_CERTIFICATE_BOTTOM_TEXT'); ?></p>
                </div>
            </div>
            <div class="layout-flex__small">
                <div class="sidebox margin-bottom-10">
                    <div class="sidebox__head">
                        <h5><?php echo Label::getLabel('LBL_CERTIFICATE_RECIPIENT'); ?></h5>
                    </div>
                    <div class="sidebox__body">
                        <div class="profile-meta d-flex align-items-center">
                            <div class="profile-meta__media margin-right-4">
                                <span class="avtar" data-title="<?php echo ucwords($order['learner_first_name'][0]); ?>">
                                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $order['order_user_id'], 'SMALL'], CONF_WEBROOT_FRONTEND); ?>" alt="<?php echo ucwords($order['learner_first_name'] . ' ' . $order['learner_last_name']) ?>">
                                </span>
                            </div>
                            <div class="profile-meta__details">
                                <p class="bold-600 color-black margin-bottom-1">
                                    <?php echo ucwords($order['learner_first_name'] . ' ' . $order['learner_last_name']) ?>
                                </p>
                                <span class="font-small"><?php echo $order['country_name'] ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sidebox border-bottom padding-bottom-10">
                    <div class="sidebox__head">
                        <h5><?php echo Label::getLabel('LBL_COURSE_DETAILS'); ?> </h5>
                    </div>
                    <div class="sidebox__body">
                        <div class="course-tile">
                            <div class="course-tile__head">
                                <div class="course-media ratio ratio--16by9">
                                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $order['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL); ?>" alt="<?php echo $order['course_title'] ?>">
                                </div>
                            </div>
                            <div class="course-tile__body">
                                <p class="course-title bold-600 margin-bottom-0">
                                    <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$order['course_slug']], CONF_WEBROOT_FRONTEND); ?>">
                                        <?php echo $order['course_title'] ?>
                                    </a>
                                </p>
                                <div class="course-tile__meta d-flex align-items-center  margin-bottom-5">
                                    <div class="course-tile__item margin-2 margin-left-0">
                                        <?php echo YouTube::convertDuration($order['course_duration']) . ', ';
                                        echo $order['course_lectures'] . ' ' . Label::getLabel('LBL_LECTURES') ?>
                                    </div>
                                    <div class="course-tile__item margin-2 margin-left-4">
                                        <a href="javascript:void(0);" class="rating -no-hover">
                                            <svg class="rating__media">
                                                <use xlink:href="<?php echo CONF_WEBROOT_FRONTEND ?>images/sprite.svg#rating"></use>
                                            </svg>
                                            <span class="rating__value">
                                                <?php echo $order['course_ratings']; ?>
                                            </span>
                                            <span class="rating__count">
                                                <?php echo ' (' . $order['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')' ?>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                                <div class="course-tile__price">
                                    <span class="price-standard">
                                        <?php
                                        $amount = $order['ordcrs_amount'] - $order['ordcrs_discount'];
                                        echo MyUtility::formatMoney($amount);
                                        ?>
                                    </span>
                                    <?php if ($order['ordcrs_discount'] > 0) { ?>
                                        <span class="price-old">
                                            <?php echo MyUtility::formatMoney($order['ordcrs_amount']); ?>
                                        </span>
                                    <?php } ?>
                                </div>

                                <div class="course-tite__tutor border-top margin-top-5 padding-top-5">
                                    <h5><?php echo Label::getLabel('LBL_TUTOR_DETAILS'); ?></h5>
                                </div>
                                <div class="profile-meta d-flex align-items-center padding-top-5">
                                    <div class="profile-meta__media margin-right-4">
                                        <span class="avtar avtar--small" data-title="<?php echo strtoupper($order['teacher_first_name'][0]); ?>">
                                            <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $order['teacher_id'], 'SMALL'], CONF_WEBROOT_FRONTEND); ?>" alt="<?php echo ucwords($order['teacher_first_name'] . ' ' . $order['teacher_last_name']) ?>">
                                        </span>
                                    </div>
                                    <div class="profile-meta__details">
                                        <p class="bold-600 color-black margin-bottom-1">
                                            <a href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$order['user_username']], CONF_WEBROOT_FRONTEND); ?>">
                                                <?php echo ucwords($order['teacher_first_name'] . ' ' . $order['teacher_last_name']) ?>
                                            </a>
                                        </p>
                                        <a href="javascript:void(0);" class="rating -no-hover">
                                            <svg class="rating__media">
                                                <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#rating"></use>
                                            </svg>
                                            <span class="rating__value">
                                                <?php echo $order['teacher_rating']; ?>
                                            </span>
                                            <span class="rating__count">
                                                <?php echo '(' . $order['teacher_reviewes'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')'; ?>
                                            </span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-actions margin-top-8">
                    <div class="share margin-right-1">
                        <a target="_blank" href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_CERTIFICATE_PDF, $ordcrsId]); ?>" class="btn btn--primary-bordered btn--block">
                            <svg class="icon icon--download margin-right-2 icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#download-icon"></use>
                            </svg>
                            <span class="margin-left-1">
                                <?php echo Label::getLabel('LBL_DOWNLOAD'); ?>
                            </span>
                        </a>
                        <!-- <div id="download-target" class="share__target">
                            <div class="d-flex justify-content-center">
                                <a target="_blank" href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_CERTIFICATE_IMAGE, $ordcrsId]); ?>" class="btn btn--primary-bordered margin-1">
                                    <svg class="icon icon--png icon--small margin-right-2">
                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#png-attachment"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_PNG'); ?>
                                </a>
                                <a target="_blank" href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_CERTIFICATE_PDF, $ordcrsId]); ?>" class="btn btn--secondary-bordered margin-1">
                                    <svg class="icon icon--png icon--small margin-right-2">
                                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#pdf-attachment"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_PDF'); ?>
                                </a>
                            </div>
                        </div> -->
                    </div>
                    <div class="share margin-left-1">
                        <a href="#share-target" class="share__trigger trigger-js btn btn--primary-bordered btn--block">
                            <svg class="icon icon--share icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#share-media"></use>
                            </svg>
                            <span class="margin-left-1"><?php echo Label::getLabel('LBL_SHARE'); ?></span>
                        </a>
                        <div id="share-target" class="share__target">
                            <ul class="social--share d-flex justify-content-center align-items-center">
                                <li class="social--fb">
                                    <span class="st_facebook_large st-custom-button" data-network="facebook" displaytext="Facebook" displayText='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' st_processed="yes">
                                        <img alt="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg">
                                        <span style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;" class="stButton">
                                            <span class="stLarge" style="background-image: url(&quot;https://ws.sharethis.com/images/2017/facebook_32.png&quot;);">
                                            </span>
                                        </span>
                                    </span>
                                </li>
                                <li class="social--tw">
                                    <span class="st_twitter_large st-custom-button" data-network="twitter" displaytext="Tweet" displayText='<?php echo Label::getLabel('LBL_TWITTER'); ?>' title='<?php echo Label::getLabel('LBL_TWITTER'); ?>' st_processed="yes">
                                        <img alt="<?php echo Label::getLabel('LBL_TWITTER'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg">
                                        <span style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;" class="stButton">
                                            <span class="stLarge" style="background-image: url(&quot;https://ws.sharethis.com/images/2017/twitter_32.png&quot;);">
                                            </span>
                                        </span>
                                    </span>
                                </li>
                                <li class="social--pt">
                                    <span class="st_pinterest_large st-custom-button" data-network="pinterest" displaytext="Pinterest" displayText='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' st_processed="yes">
                                        <img alt="<?php echo Label::getLabel('LBL_PINTEREST'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg">
                                        <span style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;" class="stButton">
                                            <span class="stLarge" style="background-image: url(&quot;https://ws.sharethis.com/images/2017/pinterest_32.png&quot;);">
                                            </span>
                                        </span>
                                    </span>
                                </li>
                                <li class="social--mail">
                                    <span class="st_email_large st-custom-button" data-network="email" displaytext="Email" displayText='<?php echo Label::getLabel('LBL_EMAIL'); ?>' title='<?php echo Label::getLabel('LBL_EMAIL'); ?>' st_processed="yes">
                                        <img alt="<?php echo Label::getLabel('LBL_EMAIL'); ?>" src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg">
                                        <span style="text-decoration:none;color:#000000;display:inline-block;cursor:pointer;" class="stButton">
                                            <span class="stLarge" style="background-image: url(&quot;https://ws.sharethis.com/images/2017/email_32.png&quot;);">
                                            </span>
                                        </span>
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>