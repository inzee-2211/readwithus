<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
if ($commissionId > 0) {
    $frm->getField('user_name')->addFieldTagAttribute('disabled', true);
}
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_COMMISSION_SETUP'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="tabs_nav_container responsive flat">
            <div class="tabs_panel_wrap">
                <div class="tabs_panel">
                    <?php echo $frm->getFormHtml(); ?>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    $("document").ready(function() {
        $("input[name='user_name']").autocomplete({
            'source': function(request, response) {
                fcom.updateWithAjax(fcom.makeUrl('Commission', 'AutoCompleteJson'), {keyword: request}, function (result) {
                    response($.map(result.data, function(item) {
                        return {
                            label: escapeHtml(item['full_name'] + ' (' + item['user_email'] + ')'),
                            value: item['user_id'],
                            name: item['full_name']
                        };
                    }));
                });
            },
            'select': function(item) {
                $("input[name='comm_user_id']").val(item.value);
                $("input[name='user_name']").val(item.name);
            }
        });
        $("input[name='user_name']").keyup(function() {
            $("input[name='comm_user_id']").val('');
        });
    });
</script>