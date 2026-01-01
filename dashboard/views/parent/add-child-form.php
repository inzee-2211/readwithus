<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
    <div class="dashboard">
        <div class="dashboard__primary">
            <div class="page__head">
                <h1><?php echo Label::getLabel('LBL_ADD_CHILD'); ?></h1>
                <div>
                    <a class="btn btn--secondary" href="<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>">
                        <?php echo Label::getLabel('LBL_BACK_TO_CHILDREN'); ?>
                    </a>
                </div>
            </div>

            <div class="page__body">
                <div class="card">
                    <div class="card__head">
                        <h4><?php echo Label::getLabel('LBL_ADD_CHILD_BY_EMAIL'); ?></h4>
                    </div>
                    <div class="card__body">
                        <div class="form-message"></div>
                        
                        <?php
                        $frm->setFormTagAttribute('id', 'addChildForm');
                        $frm->setFormTagAttribute('class', 'form');
                        $frm->setFormTagAttribute('onsubmit', 'setupAddChild(this); return false;');
                        $frm->developerTags['colClassPrefix'] = 'col-md-';
                        $frm->developerTags['fld_default_col'] = 12;
                        echo $frm->getFormHtml();
                        ?>
                        
                        <div class="alert alert--info margin-top-4">
                            <strong><?php echo Label::getLabel('LBL_NOTE'); ?>:</strong>
                            <?php echo Label::getLabel('LBL_CHILD_WILL_RECEIVE_REQUEST'); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setupAddChild(frm) {
    var data = fcom.frmData(frm);
    fcom.updateWithAjax(fcom.makeUrl('Parent', 'setupAddChild'), data, function(response) {
        if (response.status) {
            window.location.href = '<?php echo MyUtility::makeUrl('Parent', 'children', [], CONF_WEBROOT_DASHBOARD); ?>';
        }
    });
}
</script>