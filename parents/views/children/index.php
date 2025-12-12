<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="page__head">
        <div class="row align-items-center justify-content-between">
            <div class="col-sm-6">
                <h1><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></h1>
            </div>
            <div class="col-sm-auto">
                <!-- Placeholder for Add Child button -->
                <a href="javascript:void(0)"
                    class="btn btn--primary"><?php echo Label::getLabel('LBL_ADD_CHILD'); ?></a>
            </div>
        </div>
    </div>
    <div class="page__body">
        <?php if (!empty($children)) { ?>
            <div class="row">
                <?php foreach ($children as $child) { ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="box box--white box--height-full">
                            <div class="box__body p-4 text-center">
                                <div class="avtar avtar--large avtar--centered mb-3"
                                    data-title="<?php echo $child['avatar_letter']; ?>">
                                    <!-- Use dummy image or placeholder -->
                                    <img src="<?php echo CONF_WEBROOT_URL . 'images/defaults/default_user.jpg'; ?>"
                                        alt="<?php echo $child['name']; ?>" onerror="this.style.display='none'">
                                </div>
                                <h5 class="margin-bottom-2"><?php echo $child['name']; ?></h5>
                                <p class="margin-bottom-4 color-secondary"><?php echo $child['grade']; ?></p>

                                <div class="stats-list text-left mb-4">
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="color-secondary"><?php echo Label::getLabel('LBL_Teachers'); ?></span>
                                        <span class="font-weight-bold"><?php echo count($child['teachers']); ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="color-secondary"><?php echo Label::getLabel('LBL_Quizzes'); ?></span>
                                        <span class="font-weight-bold"><?php echo $child['totalQuizzes']; ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <span class="color-secondary"><?php echo Label::getLabel('LBL_Avg_Score'); ?></span>
                                        <span class="font-weight-bold"><?php echo $child['averageScore']; ?>%</span>
                                    </div>
                                </div>

                                <a href="<?php echo MyUtility::makeUrl('ParentChildren', 'view', [$child['id']]); ?>"
                                    class="btn btn--secondary btn--block"><?php echo Label::getLabel('LBL_VIEW_PROGRESS'); ?></a>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } else { ?>
            <div class="box box--white p-5 text-center">
                <h5><?php echo Label::getLabel('LBL_NO_CHILDREN_FOUND'); ?></h5>
            </div>
        <?php } ?>
    </div>
</div>