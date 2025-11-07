<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
// small helper to load the same CSS file you used on home
function asset_css($file){
    $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($file,'/');
    return CONF_WEBROOT_URL . $file . '?v=' . (@filemtime($abs) ?: time());
}
?>

<link rel="stylesheet" href="<?= asset_css('css/home.testimonials.css') ?>">

<section class="rwu-testimonials" aria-label="What they say about us">
  <div class="rwu-t-container">
    
    <!-- LEFT PANEL (same look as homepage) -->
    <div class="rwu-t-left">
      <div class="rwu-pill" aria-hidden="true">
        <span>Trusted by Parents</span>
      </div>

      <h2 class="rwu-t-title">
        What they say <br />
        <span class="accent">About us</span>
      </h2>

      <p class="rwu-t-copy">
        Explore real stories from parents and learners who have used ReadWithUs.
        These testimonials highlight how personalised tutoring, structured lessons,
        and interactive tools helped them learn with confidence.
      </p>

      <div class="rwu-t-cta">
        <a class="rwu-btn" href="<?= MyUtility::makeUrl('Home'); ?>">
          Back to Home →
        </a>
      </div>
    </div>

    <!-- RIGHT GRID: ALL TESTIMONIALS -->
    <div class="rwu-t-right">
      <?php if (!empty($testimonials)) { ?>
        <?php foreach ($testimonials as $t) {

            // Build image URL for testimonial avatar
            $imgUrl = FatCache::getCachedUrl(
                MyUtility::makeFullUrl('Image', 'show', [
                    Afile::TYPE_TESTIMONIAL_IMAGE,
                    $t['testimonial_id'],
                    Afile::SIZE_SMALL
                ]),
                CONF_DEF_CACHE_TIME,
                '.jpg'
            );

            if (empty($imgUrl)) {
                // fallback avatar
                $imgUrl = CONF_WEBROOT_URL . 'images/defaults/user.jpg';
            }
        ?>
          <article class="rwu-quote-card">
            <div class="rwu-quote-badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" class="rwu-quote-icon" aria-hidden="true">
                <path d="M8.9 6C6.7 6 5 7.8 5 10v8h7v-8H9.9c.1-1.1.6-2 1.8-2V6H8.9zm9 0c-2.2 0-3.9 1.8-3.9 4v8H21v-8h-2.1c.1-1.1.6-2 1.8-2V6h-1.8z"/>
              </svg>
            </div>

            <div class="rwu-quote-body">
              “<?= nl2br(FatUtility::decodeHtmlEntities($t['testimonial_text'])) ?>”
            </div>

            <div class="rwu-person">
              <div class="rwu-avatar">
                <img src="<?= $imgUrl; ?>"
                     alt="<?= htmlspecialchars($t['testimonial_user_name']); ?>" />
              </div>
              <div class="rwu-person-meta">
                <div class="rwu-name">
                  <?= htmlspecialchars($t['testimonial_user_name']); ?>
                </div>
                <div class="rwu-role">
                  <?= htmlspecialchars($t['testimonial_identifier']); ?>
                </div>
              </div>
            </div>
          </article>
        <?php } ?>
      <?php } else { ?>
        <article class="rwu-quote-card">
          <div class="rwu-quote-body">
            <?= Label::getLabel('LBL_NO_TESTIMONIALS_TO_SHOW_YET'); ?>
          </div>
        </article>
      <?php } ?>
    </div>

  </div>
</section>
