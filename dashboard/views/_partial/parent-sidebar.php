<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('LBL_PARENT_PORTAL'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ($controllerName == "Parent" && $action == "index") ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('Parent', 'index', [], CONF_WEBROOT_DASHBOARD); ?>">
                    <svg class="icon icon--dashboard margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#dashboard'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_DASHBOARD'); ?></span>
                </a>
            </li>

            <li class="menu__item <?php echo ($controllerName == "Parent" && $action == "children") ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
                    <svg class="icon icon--students margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#students'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></span>
                </a>
            </li>

            <li class="menu__item">
                <a href="<?php echo MyUtility::makeUrl('Account', 'ProfileInfo'); ?>">
                    <svg class="icon icon--settings margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#settings'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_ACCOUNT_SETTINGS'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>
