<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="video-preview">
    <div class="video-preview__head">
        <h5><?php echo $resource['lecture_title'] ?></h5>
    </div>
    <div class="video-preview__body">
        <div class="video-preview__large">
            <div class="preview-video ratio ratio--16by9">
                <iframe width="100%" height="100%" src="<?php echo $resource['lecsrc_link'] ?>" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen=""></iframe>
            </div>
        </div>
        <div class="video-preview__small">
            <h6 class="padding-6 padding-bottom-3 padding-top-4 bold-700">
                <?php echo Label::getLabel('LBL_FREE_SAMPLE_VIDEOS') ?>
            </h6>
            <div class="more-videos">
                <?php if (count($lectures) > 0) { ?>
                    <?php foreach ($lectures as $lecture) { ?>
                        <!-- video thumb -->
                        <div class="more-videos__item" onclick="openMedia('<?php echo $lecture['lecsrc_id']; ?>');">
                            <div class="video-item <?php echo ($resource['lecsrc_lecture_id'] == $lecture['lecsrc_lecture_id']) ? 'is-active' : ''; ?>">
                                <div class="video-item__content">
                                    <div class="video-item__title">
                                        <?php echo $lecture['lecture_title'] ?>
                                    </div>
                                    <div class="video-item__time">
                                        <?php
                                        echo YouTube::convertDuration($lecture['lecture_duration'], true, true, true, false);
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- ] -->
                    <?php } ?>
                <?php } else { ?>
                    <!-- video thumb -->
                    <div class="more-videos__item">
                        <div class="video-item">
                            <div class="video-item__content">
                                <div class="video-item__title">
                                    <?php echo Label::getLabel('LBL_NO_FREE_VIDEOS_AVAILABLE') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>