<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="container">
  <h2><?php echo FatUtility::decodeHtmlEntities($course['course_title']); ?></h2>
  <p>Progress: <?php echo (float)$progress['progress_percent']; ?>%</p>
  <ul>
  <?php foreach ($sections as $s): ?>
    <li>
      <strong><?php echo $s['section_title']; ?></strong>
      <ul>
        <?php foreach ($s['lectures'] as $lec): ?>
          <li data-lecture="<?php echo $lec['lecture_id']; ?>">
            <?php echo $lec['lecture_title']; ?>
            <button class="btn btn-primary js-start-lecture"
              data-lecture-id="<?php echo $lec['lecture_id']; ?>"
              data-progress-id="<?php echo (int)$progressId; ?>">
              Open
            </button>
          </li>
        <?php endforeach; ?>
      </ul>
    </li>
  <?php endforeach; ?>
  </ul>
</div>
<script>
document.addEventListener('click', async (e) => {
  const b = e.target.closest('.js-start-lecture');
  if (!b) return;
  const lectureId = b.dataset.lectureId;
  const progressId = b.dataset.progressId;
  // Just call get-lecture view; you can replace with your player logic
  window.location = '<?php echo MyUtility::makeUrl("SubscriptionTutorials","getLectureData"); ?>'
    + '?lecture_id=' + lectureId + '&progress_id=' + progressId;
});
</script>
