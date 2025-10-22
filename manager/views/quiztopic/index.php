<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
  <div class="page__head d-flex justify-content-between align-items-center">
    <h1><?= Label::getLabel('LBL_QUIZ_TOPICS'); ?></h1>
    <a class="btn btn-primary" href="<?= MyUtility::makeUrl('Quiztopic','form'); ?>">
      <?= Label::getLabel('LBL_ADD_TOPIC'); ?>
    </a>
  </div>

  <div class="page__body">
    <table class="table">
      <thead>
        <tr>
          <th>ID</th>
          <th><?= Label::getLabel('LBL_TOPIC'); ?></th>
          <th><?= Label::getLabel('LBL_LEVEL'); ?></th>
          <th><?= Label::getLabel('LBL_SUBJECT'); ?></th>
          <th><?= Label::getLabel('LBL_TYPE'); ?></th>
          <th><?= Label::getLabel('LBL_TIER'); ?></th>
          <th><?= Label::getLabel('LBL_EXAM_BOARD'); ?></th>
          <th><?= Label::getLabel('LBL_YEAR'); ?></th>
          <th><?= Label::getLabel('LBL_ACTIONS'); ?></th>
        </tr>
      </thead>
      <tbody>
        <?php if (!empty($rows)) : foreach ($rows as $r): ?>
          <tr>
            <td><?= $r['id']; ?></td>
            <td><?= $r['topic_name']; ?></td>
            <td><?= $r['level_name']; ?></td>
            <td><?= $r['subject']; ?></td>
            <td><?= $r['type_name']; ?></td>
            <td><?= $r['tier_name']; ?></td>
            <td><?= $r['examboard_name']; ?></td>
            <td><?= $r['year_name']; ?></td>
            <td>
              <a href="<?= MyUtility::makeUrl('Quiztopic','form',[$r['id']]); ?>"><?= Label::getLabel('LBL_EDIT'); ?></a>
              |
              <a class="js-delete"
                 data-id="<?= $r['id']; ?>"
                 data-url="<?= MyUtility::makeUrl('Quiztopic','delete'); ?>"
                 href="javascript:void(0);"><?= Label::getLabel('LBL_DELETE'); ?></a>
            </td>
          </tr>
        <?php endforeach; else: ?>
          <tr><td colspan="9"><?= Label::getLabel('LBL_NO_RECORD_FOUND'); ?></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<script>
document.querySelectorAll('.js-delete').forEach(a => {
  a.addEventListener('click', () => {
    if (!confirm('Delete this topic?')) return;
    const fd = new FormData();
    fd.append('id', a.dataset.id);
    fetch(a.dataset.url, { method: 'POST', body: fd, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(async r => { const t = await r.text(); try { return JSON.parse(t); } catch { throw new Error(t); } })
      .then(j => { if (j.status == 1) location.reload(); else alert(j.msg || 'Error'); })
      .catch(err => alert((err && err.message) || 'Network error'));
  });
});
</script>
<style>
  /* ===== White background for Quiz Topics table ===== */
.page__body {
  background: #fff;
  /* border-radius: 12px; */
  padding: 18px;
  padding-bottom: 5%;
  margin-bottom: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}
</style>