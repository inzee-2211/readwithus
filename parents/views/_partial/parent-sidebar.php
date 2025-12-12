<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('LBL_PROFILE'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ('ParentDashboard' == $controllerName) ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('ParentDashboard'); ?>">
                    <svg class="icon icon--dashboard margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#dashboard'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_DASHBOARD'); ?></span>
                </a>
            </li>
            <!-- Placeholder for Account Settings -->
            <li class="menu__item">
                <a href="javascript:void(0)">
                    <svg class="icon icon--settings margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#settings'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_ACCOUNT_SETTINGS'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('LBL_MY_CHILDREN'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ('ParentChildren' == $controllerName) ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('ParentChildren'); ?>">
                    <svg class="icon icon--students margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#students'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_CHILDREN_OVERVIEW'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>

<div class="menu-group">
    <h6 class="heading-6"><?php echo Label::getLabel('LBL_SUBSCRIPTIONS_&_PAYMENTS'); ?></h6>
    <nav class="menu menu--primary">
        <ul>
            <li class="menu__item <?php echo ('ParentSubscription' == $controllerName) ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('ParentSubscription'); ?>">
                    <svg class="icon icon--lesson margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#subscription'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_SUBSCRIPTIONS'); ?></span>
                </a>
            </li>
            <li class="menu__item <?php echo ('ParentWallet' == $controllerName) ? 'is-active' : ''; ?>">
                <a href="<?php echo MyUtility::makeUrl('ParentWallet'); ?>">
                    <svg class="icon icon--wallet margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#wallet'; ?>"></use>
                    </svg>
                    <span><?php echo Label::getLabel('LBL_WALLET'); ?></span>
                </a>
            </li>
        </ul>
    </nav>
</div>