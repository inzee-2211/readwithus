<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_PARENT_DASHBOARD'); ?></h1>
            </div>
            <div class="page__body">
                <div class="stats-row margin-bottom-6">
                    <div class="row align-items-center">
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_TOTAL_CHILDREN'); ?></span>
                                    <h5><?php echo $stats['childrenCount']; ?></h5>
                                </div>
                                <div class="stat__media bg-yellow">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use
                                            xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#students'; ?>">
                                        </use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('ParentChildren'); ?>" class="stat__action"></a>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_UPCOMING_LESSONS'); ?></span>
                                    <h5><?php echo $stats['upcomingLessons']; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use
                                            xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#planning'; ?>">
                                        </use>
                                    </svg>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 col-md-6 col-sm-6">
                            <div class="stat">
                                <div class="stat__amount">
                                    <span><?php echo Label::getLabel('LBL_WALLET_BALANCE'); ?></span>
                                    <h5><?php echo MyUtility::formatMoney($stats['walletBalance']); ?></h5>
                                </div>
                                <div class="stat__media bg-primary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#stats_2'; ?>">
                                        </use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('ParentWallet'); ?>" class="stat__action"></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>