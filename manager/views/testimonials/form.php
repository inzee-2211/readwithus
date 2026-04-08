<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setupTestimonial(this); return(false);');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
?>
<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_Testimonial_Setup'); ?></h4>
    </div>
    <div class="sectionbody space">
        <div class="row">	
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0)" onclick="editTestimonialForm(<?php echo $testimonial_id ?>);"><?php echo Label::getLabel('LBL_General'); ?></a></li>
                        <?php
                        $inactive = ($testimonial_id == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                            ?>
                            <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($testimonial_id > 0) { ?> onclick="editTestimonialLangForm(<?php echo $testimonial_id ?>, <?php echo $langId; ?>);" <?php } ?>><?php echo $langName; ?></a></li>
                        <?php } ?>
                        <li class="<?php echo $inactive; ?>"><a href="javascript:void(0);" <?php if ($testimonial_id > 0) { ?> onclick="testimonialMediaForm(<?php echo $testimonial_id ?>);" <?php } ?>><?php echo Label::getLabel('LBL_Media'); ?></a></li>
                    </ul>
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
