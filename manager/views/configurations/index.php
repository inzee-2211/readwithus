<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class='page'>
    <div class='fixed_container'>
        <div class="row">
            <div class="space">
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon">
                                <i class="ion-android-star"></i></span>
                            <h5><?php echo Label::getLabel('LBL_General_Settings'); ?> </h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                    </div>
                </div>
                <div class="tabs_nav_container vertical">
                    <ul class="tabs_nav outerul">
                        <?php
                        $count = 1;
                        foreach ($tabs as $formType => $tabName) {
                            $tabsId = 'tabs_' . $count;
                            ?>		
                            <?php if ($formType == Configurations::FORM_MEDIA) { ?>
                                <li><a class="<?php echo ( $activeTab == $formType ) ? 'active' : '' ?>" rel = <?php echo $tabsId; ?> href="javascript:void(0)" onClick="getLangForm(<?php echo $formType; ?>, <?php echo $siteLangId; ?>, '<?php echo $tabsId; ?>')"><?php echo $tabName; ?></a></li>
                            <?php } else { ?>
                                <li><a class="<?php echo ($activeTab == $formType) ? 'active' : '' ?>" rel = <?php echo $tabsId; ?> href="javascript:void(0)" onClick="getForm(<?php echo $formType; ?>, '<?php echo $tabsId; ?>')"><?php echo $tabName; ?></a></li>
                                <?php
                            } $count++;
                        }
                        ?>			
                    </ul>
                    <div id="frmBlock" class="tabs_panel_wrap">
                        <div class="tabs_panel"></div>
                    </div>										
                </div>
            </div>
        </div>
    </div>
</div>
<script >
    var activeTab = <?php echo $activeTab; ?>;
    var YES = <?php echo AppConstant::YES; ?>;
    var NO = <?php echo AppConstant::NO; ?>;
</script>