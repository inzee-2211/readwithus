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
                                    <span><?php echo Label::getLabel('LBL_LINKED_CHILDREN'); ?></span>
                                    <h5><?php echo (int)$childrenCount; ?></h5>
                                </div>
                                <div class="stat__media bg-secondary">
                                    <svg class="icon icon--money icon--40 color-white">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#students'; ?>"></use>
                                    </svg>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>" class="stat__action"></a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-panel">
                    <div class="page-panel__head">
                        <h4><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></h4>
                    </div>
                    <div class="page-panel__body">
                        <?php $this->includeTemplate('parent/children.php', ['children' => $children], false); ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
