<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php $containerClass = (count($resources) > 0) ? 'col-xl-7' : 'col-xl-12'; ?>
<?php if ($lecture) { ?>
    <div class="row justify-content-between">
        <div class="<?php echo $containerClass; ?>">
            <div class="cms-container">
                <div class="editor-content">
                    <iframe srcdoc="<?php echo $lecture['lecture_details']; ?>" style="border:none;width: 100%;height: 100%;" ></iframe>
                </div>
            </div>
        </div>
        <?php if (count($resources) > 0) { ?>
            <div class="col-xl-5 d-flex justify-content-xl-end align-items-xl-start">
                <div class="box-outlined">
                    <div class="box-outlined__head margin-bottom-6">
                        <h6><?php echo Label::getLabel('LBL_LECTURE_RESOURCES') . ' (' . count($resources) . ')' ?></h6>
                    </div>
                    <div class="box-outlined__body">
                        <div class="lecture-attachment">
                            <?php foreach ($resources as $resource) { ?>
                                <a href="<?php echo MyUtility::makeUrl('CoursePreview', 'downloadResource', [$resource['lecsrc_id']]); ?>" target="_blank" class="lecture-attachment__item">
                                    <figure class="lecture-attachment__media">
                                        <svg class="attached-media"><use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#pdf-attachment"></use></svg>
                                    </figure>
                                    <span class="lecture-attachment__content">
                                        <p class="margin-bottom-0 color-black"><?php echo $resource['resrc_name']; ?></p>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="page-directions border-top">
        <div class="row justify-content-between">
            <div class="col-sm-6">
                <?php if (empty($video['lecsrc_link'])) { ?>
                    <a href="javascript:void(0);" id="btnComplete<?php echo $lecture['lecture_id'] ?>" onclick="markComplete('<?php echo $lecture['lecture_section_id'] ?>', '<?php echo $lecture['lecture_id'] ?>')" class="btn btn--primary btn--sm-block">
                        <?php echo Label::getLabel('LBL_MARK_LECTURE_COMPLETE'); ?>
                    </a>
                <?php } ?>
            </div>
            <div class="col-sm-auto">
                <div class="btn-actions">
                    <?php $display = ($previousLecture) ? '' : 'btn--disabled'; ?>
                    <a href="javascript:void(0);" class="btn btn--primary-bordered margin-right-1 getPrevJs <?php echo $display; ?>">
                        <svg class="icon icon--arrow icon--xsmall margin-right-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#prev"></use>
                        </svg>
                        <?php echo Label::getLabel('LBL_PREV') ?>
                    </a>
                    <?php $display = ($nextLecture) ? '' : 'btn--disabled'; ?>
                    <a href="javascript:void(0);" class="btn btn--primary-bordered margin-left-1 getNextJs <?php echo $display; ?>">
                        <?php echo Label::getLabel('LBL_NEXT') ?>
                        <svg class="icon icon--arrow icon--xsmall margin-left-2">
                        <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#next"></use>
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="message-display no-skin">
                <div class="message-display__media">
                    <svg>
                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#stuck"></use>
                    </svg>
                </div>
                <h4 class="margin-bottom-4">
                    <?php echo stripslashes(Label::getLabel("LBL_YOU'VE_COMPLETED_THE_LAST_LECTURE_IN_THIS_COURSE.")); ?>
                </h4>
                <p><?php echo Label::getLabel('LBL_LAST_LECTURE_COMPLETED_SHORT_DESCRIPTION'); ?></p>
                <a href="javascript:void(0);" onclick="goToPendingLecture();" class="btn btn--secondary">
                    <?php echo Label::getLabel('LBL_GO_TO_PENDING_LECTURES'); ?>
                </a>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    $(document).ready(function () {
        resetEditorHeight();
    });
</script>