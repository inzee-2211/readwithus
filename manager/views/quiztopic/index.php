<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="page">
  <div class="page__head d-flex justify-content-between align-items-center">
    <h1><?= Label::getLabel('LBL_QUIZ_TOPICS'); ?></h1>
    <a class="btn btn-primary" href="<?= MyUtility::makeUrl('Quiztopic','form'); ?>">
      <?= Label::getLabel('LBL_ADD_TOPIC'); ?>
    </a>
  </div>

  <!-- ===================== FILTER / SEARCH BAR ===================== -->
  <?php
    $qs        = FatApp::getQueryStringData();
    $keyword   = $qs['keyword']   ?? '';
    $level     = $qs['level']     ?? '';
    $subject   = $qs['subject']   ?? '';
    $year      = $qs['year']      ?? '';
    $examboard = $qs['examboard'] ?? '';
    $type      = $qs['type']      ?? '';
    $tier      = $qs['tier']      ?? '';
  ?>

  <div class="qt-search card">
    <form id="frmQtSearch" onsubmit="return qtSearch(this);">
      <!-- main bar -->
      <div class="qt-search-main" onclick="qtToggleFilters();">
        <i class="ion-android-search qt-search-icon"></i>
        <input
          type="text"
          name="keyword"
          id="qtKeyword"
          value="<?= htmlspecialchars($keyword); ?>"
          placeholder="<?= Label::getLabel('LBL_SEARCH_BY_TOPIC_OR_SUBJECT'); ?>"
          onfocus="qtOpenFilters();"
        />
        <button type="submit" class="qt-btn qt-btn-primary">
          <?= Label::getLabel('LBL_SEARCH'); ?>
        </button>
        <button type="button" class="qt-btn qt-btn-secondary" onclick="qtClear();">
          <?= Label::getLabel('LBL_CLEAR'); ?>
        </button>
      </div>

      <!-- advanced filters (collapsed by default) -->
      <div class="qt-search-filters" id="qtFilters">
        <div class="qt-filters-row">
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_LEVEL'); ?></label>
            <input type="text" name="level" value="<?= htmlspecialchars($level); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_LEVEL'); ?>">
          </div>
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_SUBJECT'); ?></label>
            <input type="text" name="subject" value="<?= htmlspecialchars($subject); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_SUBJECT'); ?>">
          </div>
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_YEAR'); ?></label>
            <input type="text" name="year" value="<?= htmlspecialchars($year); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_YEAR'); ?>">
          </div>
        </div>

        <div class="qt-filters-row">
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_EXAM_BOARD'); ?></label>
            <input type="text" name="examboard" value="<?= htmlspecialchars($examboard); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_EXAM_BOARD'); ?>">
          </div>
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_TYPE'); ?></label>
            <input type="text" name="type" value="<?= htmlspecialchars($type); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_TYPE'); ?>">
          </div>
          <div class="qt-filter">
            <label><?= Label::getLabel('LBL_TIER'); ?></label>
            <input type="text" name="tier" value="<?= htmlspecialchars($tier); ?>"
                   placeholder="<?= Label::getLabel('LBL_SEARCH_BY_TIER'); ?>">
          </div>
        </div>
      </div>
    </form>
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
/* ======== Delete existing behaviour ======== */
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

/* ======== Search behaviour ======== */
function qtSearch(frm){
  const params = new URLSearchParams(new FormData(frm));
  const base = "<?= MyUtility::makeUrl('Quiztopic','index'); ?>";
  // remove empty values from query
  for (const [k, v] of params.entries()) {
    if (!v.trim()) params.delete(k);
  }
  const url = params.toString() ? base + '?' + params.toString() : base;
  window.location.href = url;
  return false;
}

function qtClear(){
  const base = "<?= MyUtility::makeUrl('Quiztopic','index'); ?>";
  window.location.href = base;
}

function qtToggleFilters(){
  const box = document.getElementById('qtFilters');
  if (!box) return;
  box.classList.toggle('is-open');
}

function qtOpenFilters(){
  const box = document.getElementById('qtFilters');
  if (!box) return;
  box.classList.add('is-open');
}
</script>

<style>
/* ===== White background for Quiz Topics table ===== */
.page__body {
  background: #fff;
  padding: 18px;
  padding-bottom: 5%;
  margin-bottom: 10px;
  box-shadow: 0 2px 10px rgba(0,0,0,0.04);
}

/* ===== Search bar styling (YoCoach-ish) ===== */
.qt-search {
  margin: 15px 0 10px;
  padding: 16px 18px;
  background: #fff;
  border-radius: 10px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

.qt-search-main {
  display: flex;
  align-items: center;
  gap: 10px;
  border: 1px solid #e5e7eb;
  border-radius: 8px;
  padding: 6px 10px;
  cursor: text;
}

.qt-search-main input {
  flex: 1;
  border: none;
  outline: none;
  height: 34px;
  font-size: 14px;
}

.qt-search-icon {
  font-size: 18px;
  color: #9ca3af;
}

.qt-btn {
  border-radius: 6px;
  padding: 0 16px;
  height: 32px;
  border: none;
  font-size: 13px;
  cursor: pointer;
}

.qt-btn-primary {
  background: orangered;
  color: #fff;
}

.qt-btn-secondary {
  background: #e5e7eb;
  color: #111827;
}

/* advanced filters */
.qt-search-filters {
  max-height: 0;
  overflow: hidden;
  transition: max-height .25s ease;
  margin-top: 8px;
}

.qt-search-filters.is-open {
  max-height: 200px;
}

.qt-filters-row {
  display: flex;
  gap: 12px;
  margin-top: 8px;
}

.qt-filter {
  flex: 1;
}

.qt-filter label {
  display: block;
  font-size: 12px;
  color: #6b7280;
  margin-bottom: 2px;
}

.qt-filter input {
  width: 100%;
  height: 32px;
  padding: 4px 8px;
  border-radius: 6px;
  border: 1px solid #d1d5db;
  font-size: 13px;
}

@media (max-width: 900px){
  .qt-filters-row { flex-direction: column; }
}
</style>
