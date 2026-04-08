<div id="wrapper">
    <!--header start here-->
    <header id="header" class="no-print">
        <div class="headerwrap">
            <div class="one_third_grid"><a href="javascript:void(0);" class="menutrigger"><span></span></a></div>
            <div class="one_third_grid logo"><a href="<?php echo MyUtility::makeUrl('home'); ?>"><img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_ADMIN_LOGO, 0, Afile::SIZE_ORIGINAL, $siteLangId]); ?>" alt=""></a></div>
            <div class="one_third_grid">
                <ul class="iconmenus">
                    <?php if ($controllerName == 'Home' && $actionName == 'index' && $objPrivilege->canViewSalesReport(true)) { ?>
                        <li class="viewstore">
                            <a title="<?php echo Label::getLabel('LBL_REGENERATE_STATS') . ' (' . $regendatedtime . ')'; ?>" onclick="regenerateStat();" href="javascript:void(0);">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M11 .543c.33-.029.663-.043 1-.043C18.351.5 23.5 5.649 23.5 12c0 .337-.014.67-.043 1h-1.506c-.502 5.053-4.766 9-9.951 9-5.523 0-10-4.477-10-10 0-5.185 3.947-9.449 9-9.95V.542zM11 13V4.062A8.001 8.001 0 0 0 12 20a8.001 8.001 0 0 0 7.938-7H11zm10.448-2A9.503 9.503 0 0 0 13 2.552V11h8.448z"/></svg>
                            </a>
                        </li>
                    <?php } ?>

                    <li class="viewstore">
                        <a title="<?php echo Label::getLabel('LBL_View_Portal'); ?>" href="<?php echo CONF_WEBROOT_FRONT_URL; ?>" target="_blank"><img src="<?php echo CONF_WEBROOT_URL; ?>images/store.svg" width="20" alt=""></a>
                    </li>
                    <li class="erase">
                        <a title="<?php echo Label::getLabel('LBL_Clear_Cache'); ?>" href="javascript:void(0)" onclick="clearCache()"><img class="iconerase" alt="" src="<?php echo CONF_WEBROOT_URL; ?>images/header_icon_2.svg"></a>
                    </li>
                    <li class="droplink">
                        <a href="javascript:void(0)" title="Language"><img src="<?php echo CONF_WEBROOT_URL; ?>images/icon_langs.svg" width="20" alt=""></a>
                        <div class="dropwrap">
                            <div class="head"><?php echo Label::getLabel('LBL_Select_Language'); ?></div>
                            <div class="body">
                                <ul class="linksvertical">
                                    <?php foreach ($siteLanguages as $langId => $language) { ?>
                                        <li <?php echo ($siteLangId == $language['language_id']) ? 'class="is--active"' : ''; ?>><a href="javascript:void(0);" onClick="setSiteDefaultLang(<?php echo $language['language_id']; ?>)"><?php echo $language['language_name']; ?></a></li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                    </li>
                    <li class="droplink user-account">
                        <figure class="user-account__media"><img id="leftmenuimgtag" alt="" src="<?php echo MyUtility::makeUrl('image', 'show', [Afile::TYPE_ADMIN_PROFILE_IMAGE, $adminLoggedId, Afile::SIZE_SMALL]); ?>" alt=""></figure>
                        <div class="dropwrap">
                            <div class="head"><?php echo Label::getLabel('LBL_Welcome'); ?> <?php echo $adminName; ?></div>
                            <div class="body">
                                <ul class="linksvertical">
                                    <li class=""><a href="<?php echo MyUtility::makeUrl('profile'); ?>"><?php echo Label::getLabel('LBL_View_Profile'); ?></a></li>
                                    <li class=""><a href="<?php echo MyUtility::makeUrl('profile', 'changePassword'); ?>"><?php echo Label::getLabel('LBL_Change_Password'); ?></a></li>
                                    <li class=""><a href="javascript:void(0);" onclick="logout();"><?php echo Label::getLabel('LBL_Logout'); ?></a></li>
                                </ul>
                            </div>
                        </div>

                    </li>
                </ul>
            </div>
        </div>
        <div class="searchwrap">
            <div class="searchform"><input type="text" /></div><a href="javascript:void(0)" class="searchclose searchtoggle"></a>
        </div>
    </header>
    <!--header end here-->
    <!--body start here-->
    <div id="body">
        <?php $this->includeTemplate('_partial/header/left-navigation.php') ?>