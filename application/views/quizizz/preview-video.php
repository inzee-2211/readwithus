<?php
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';

// iOS detection (covers Safari + all iOS browsers)
$isiOS = (stripos($userAgent, 'iPhone') !== false)
    || (stripos($userAgent, 'iPad') !== false)
    || (stripos($userAgent, 'iPod') !== false);

// Your existing URL (keep cache busting)
$videoUrl = MyUtility::makeUrl(
    'Image',
    'showVideo',
    [Afile::TYPE_COURSE_PREVIEW_VIDEO, $courseId],
    CONF_WEBROOT_FRONT_URL
) . '?t=' . time();
?>

<?php if ($isiOS) { ?>
    <div class="preview-video ratio ratio--16by9">
        <video
            controls
            playsinline
            webkit-playsinline
            preload="metadata"
            style="width:100%;height:100%;display:block;"
        >
            <source src="<?php echo $videoUrl; ?>" type="video/mp4">
            <?php echo Label::getLabel('LBL_BROWSER_VIDEO_NOT_SUPPORTED_INFO'); ?>
        </video>

        <div class="align-center padding-10">
            <a class="btn btn--primary btn--wide" target="_blank"
               href="<?php echo MyUtility::makeUrl('Image', 'download', [Afile::TYPE_COURSE_PREVIEW_VIDEO, $courseId], CONF_WEBROOT_FRONT_URL); ?>">
                <?php echo Label::getLabel('LBL_DOWNLOAD'); ?>
            </a>
        </div>
    </div>
<?php } else { ?>
    <div class="preview-video ratio ratio--16by9">
        <iframe
            src="<?php echo $videoUrl; ?>"
            allowfullscreen
            width="100%"
            height="100%"
            frameborder="0"
            allow="autoplay; fullscreen; picture-in-picture"
        ></iframe>
    </div>
<?php } ?>
