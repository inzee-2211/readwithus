<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $siteLangId, FatUtility::VAR_STRING, '');
?>
<!-- [ HEADER ========= -->
<header class="header">
    <div class="header-primary d-sm-flex justify-content-sm-between align-items-sm-center">
        <div class="header-primary__right order-sm-2">
            <div class="d-flex justify-content-between align-items-center">
                <!-- [ COURSE PROGRESS - NOT COMPLETED ========= -->
                <div class="course-progress <?php echo ($progress['crspro_progress'] < 100) ? 'in-progress' : 'is-completed' ?>">
                    <a href="#course-progress" class="course-progress__trigger d-flex align-items-center trigger-js">
                        <div class="course-progress__count margin-right-1">
                            <div class="percent">
                                <svg class="percent__progress" viewBox="0 0 300 300">
                                    <circle cx="150" cy="150" r="100"></circle>
                                    <circle cx="150" cy="150" r="100" style="--percent: <?php echo $progress['crspro_progress'] ?>" id="progressBarJs"></circle>
                                </svg>
                                <svg class="icon icon--trophy percent__media">
                                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#trophy">
                                    </use>
                                </svg>
                            </div>
                        </div>
                        <div class="course-progress__content">
                            <h6><?php echo $label = Label::getLabel('LBL_COURSE_PROGRESS'); ?></h6>
                            <small class="progressPercent">
                                <?php
                                $progressLbl = Label::getLabel('LBL_{percent}%_COMPLETED');
                                $progressLbl = str_replace('{percent}', $progress['crspro_progress'], $progressLbl);
                                echo $progressLbl;
                                ?>
                            </small>
                        </div>
                    </a>
                    <div id="course-progress" class="course-progress__target">
                        <div class="course-progress__content align-center d-block">
                            <?php
                            if ($progress['crspro_completed']) { ?>
                                <p class="margin-bottom-2">
                                    <?php
                                    $label = Label::getLabel('LBL_{completed-lectures}_OF_{total-lectures}_COMPLETE.');
                                    echo str_replace(
                                        ['{completed-lectures}', '{total-lectures}'], $course['course_lectures'], $label
                                    );
                                    ?>
                                </p>
                                <p class="margin-bottom-5 bold-600">
                                    <?php echo Label::getLabel('LBL_CONGRATULATIONS!_YOUR_COURSE_HAS_BEEN_SUCCESSFULLY_COMPLETED'); ?>
                                </p>
                                <?php if ($course['course_certificate'] == AppConstant::YES) { ?>
                                    <a href="<?php echo MyUtility::makeUrl('Certificates', 'index', [$progressId], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--secondary margin-left-4">
                                        <svg class="icon icon--small margin-right-2">
                                            <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD; ?>images/sprite.svg#download-icon"></use>
                                        </svg>
                                        <?php echo Label::getLabel('LBL_DOWNLOAD_CERTIFICATE'); ?>
                                    </a>
                                <?php } ?>
                            <?php } else { ?>
                                <h6 class="margin-0"><?php echo $label; ?></h6>
                                <small class="progressPercent"><?php echo $progressLbl; ?></small>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <!-- ] -->
                <!-- [ USER ACCOUNT ========= -->
                <div class="account">
                    <a href="#accout-target" class="avtar avtar--small account__trigger trigger-js" data-title="S">
                        <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $siteUserId, Afile::SIZE_SMALL], CONF_WEBROOT_FRONTEND) . '?' . time() ?>" alt="">
                    </a>
                    <div id="accout-target" class="account__target">
                        <nav class="menu-vertical">
                            <ul>
                                <?php
                                if ($siteUserType == User::LEARNER) { ?>
                                    <li class="menu__item <?php echo ("Learner" == $controllerName) ? 'is-active' : ''; ?>">
                                        <a href="<?php echo MyUtility::makeUrl('Learner', '', [], CONF_WEBROOT_DASHBOARD); ?>">
                                            <?php echo Label::getLabel('LBL_Dashboard'); ?>
                                        </a>
                                    </li>
                                    <li class="menu__item <?php echo ("Teachers" == $controllerName) ? 'is-active' : ''; ?>">
                                        <a href="<?php echo MyUtility::makeUrl('Teachers', '', [], CONF_WEBROOT_DASHBOARD); ?>">
                                            <?php echo Label::getLabel('LBL_My_Teachers'); ?>
                                        </a>
                                    </li>
                                    <li class="menu__item <?php echo ("Lessons" == $controllerName) ? 'is-active' : ''; ?>">
                                        <a href="<?php echo MyUtility::makeUrl('Lessons', '', [], CONF_WEBROOT_DASHBOARD); ?>">
                                            <?php echo Label::getLabel('LBL_Lessons'); ?>
                                        </a>
                                    </li>
                                    <li class="menu__item <?php echo ("Classes" == $controllerName) ? 'is-active' : ''; ?>">
                                        <a href="<?php echo MyUtility::makeUrl('Classes', '', [], CONF_WEBROOT_DASHBOARD); ?>">
                                            <?php echo Label::getLabel('LBL_Classes'); ?>
                                        </a>
                                    </li>
                                    <li class="menu__item <?php echo ("Courses" == $controllerName) ? 'is-active' : ''; ?>">
                                        <a href="<?php echo MyUtility::makeUrl('Courses', '', [], CONF_WEBROOT_DASHBOARD); ?>">
                                            <?php echo Label::getLabel('LBL_Courses'); ?>
                                        </a>
                                    </li>
                                    <?php
                                        }
                                            ?>
                                <li class="menu__item <?php echo ("Account" == $controllerName && "profileInfo" == $action) ? 'is-active' : ''; ?>">
                                    <a href="<?php echo MyUtility::makeUrl('Account', 'ProfileInfo', [], CONF_WEBROOT_DASHBOARD); ?>">
                                        <?php echo Label::getLabel('LBL_Settings'); ?>
                                    </a>
                                </li>
                                <li class="menu__item border-top margin-top-3">
                                    <a href="<?php echo MyUtility::makeUrl('Account', 'logout', [], CONF_WEBROOT_DASHBOARD); ?>">
                                        <?php echo Label::getLabel('LBL_Logout'); ?>
                                    </a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
                <!-- ] -->
            </div>
        </div>
        <div class="header-primary__left order-sm-1">
            <div class="d-sm-flex justify-content-sm-between align-items-sm-center">
                <figure class="header-logo">
                    <a href="<?php echo MyUtility::makeUrl('', '', [], CONF_WEBROOT_FRONT_URL); ?>">
                        <?php if (MyUtility::isDemoUrl()) { ?>
                            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'images/yocoach-logo.svg'; ?>" alt="" />
                        <?php } else { ?>
                            <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', array(Afile::TYPE_FRONT_LOGO, 0, Afile::SIZE_LARGE), CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $websiteName; ?>">
                        <?php } ?>
                    </a>
                </figure>
                <h1 class="page-title"><a href="javascript:void(0);"><?php echo CommonHelper::renderHtml($course['course_title']); ?></a>
                </h1>
            </div>
        </div>
    </div>
</header>
<!-- ] -->