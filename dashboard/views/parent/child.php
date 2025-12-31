<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
$fullName = trim(($child['user_first_name'] ?? '') . ' ' . ($child['user_last_name'] ?? ''));
?>

<div class="page">
    <div class="page__head">
        <h4><?php echo Label::getLabel('LBL_CHILD_DASHBOARD'); ?>: <?php echo htmlspecialchars($fullName); ?></h4>
        <div>
            <a class="btn btn--secondary" href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
                <?php echo Label::getLabel('LBL_BACK'); ?>
            </a>
        </div>
    </div>

    <div class="page__body">
        <div class="card">
            <div class="card__body">
                <p><strong><?php echo Label::getLabel('LBL_EMAIL'); ?>:</strong> <?php echo htmlspecialchars($child['user_email'] ?? ''); ?></p>
                <p><strong><?php echo Label::getLabel('LBL_RELATION'); ?>:</strong> <?php echo htmlspecialchars($child['parstd_relation'] ?? '-'); ?></p>

                <hr>

                <p>
                    <?php echo Label::getLabel('LBL_THIS_IS_A_BASIC_PARENT_PORTAL_PAGE'); ?>
                </p>

                <ul>
                    <li><?php echo Label::getLabel('LBL_NEXT_SHOW_PROGRESS'); ?></li>
                    <li><?php echo Label::getLabel('LBL_NEXT_SHOW_QUIZ_HISTORY'); ?></li>
                    <li><?php echo Label::getLabel('LBL_NEXT_SHOW_SUBSCRIPTION_STATUS'); ?></li>
                </ul>
            </div>
        </div>
    </div>
</div>
