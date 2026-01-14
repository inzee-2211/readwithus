<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="facebox-panel">
  <div class="facebox-panel__head padding-bottom-6">
    <h5><?php echo $course['course_title'] ?></h5>
  </div>

  <div class="facebox-panel__body padding-0">
    <div class="preview-video ratio ratio--16by9">
      <video
        controls
        playsinline
        preload="metadata"
        style="width:100%;height:100%;display:block;"
        src="<?php echo MyUtility::makeFullUrl('Image', 'showVideo', [Afile::TYPE_COURSE_PREVIEW_VIDEO, $courseId], CONF_WEBROOT_FRONT_URL) . '?t=' . time(); ?>"
      >
        Your browser does not support the video tag.
      </video>
    </div>

    <div class="align-center padding-6">
      <a class="btn btn--bordered btn--wide" target="_blank"
         href="<?php echo MyUtility::makeFullUrl('Image', 'download', [Afile::TYPE_COURSE_PREVIEW_VIDEO, $courseId], CONF_WEBROOT_FRONT_URL); ?>">
        <?php echo Label::getLabel('LBL_DOWNLOAD'); ?>
      </a>
    </div>
  </div>
</div>
