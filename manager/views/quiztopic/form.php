<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="page">
  <div class="page__head">
    <h1><?= Label::getLabel('LBL_ADD_EDIT_QUIZ_TOPIC'); ?></h1>
  </div>

  <div class="page__body">
    <section class="card">
      <div class="card__head">
        <h3><?= Label::getLabel('LBL_QUIZ_TOPIC_DETAILS'); ?></h3>
      </div>

      <div class="card__body">
        <!-- feedback area -->
        <div id="qt-alert" class="alert alert--hidden"></div>

        <!-- form -->
        <?= $frm->getFormHtml([
          'action' => MyUtility::makeUrl('Quiztopic','save'),
          'id'     => 'frmQuizSetup',
          'class'  => 'form form--styled'
        ]); ?>
      </div>

      <div class="card__foot d-flex justify-content-end gap-8">
        <button type="submit" form="frmQuizSetup" class="btn btn-primary" id="qt-submit">
          <?= Label::getLabel('LBL_SAVE'); ?>
        </button>
      </div>
    </section>
  </div>
</div>

<style>
/* --- light, project-friendly cosmetics (no global overrides) --- */
#qt-alert{margin:0 0 12px 0;padding:10px 12px;border-radius:8px;font-size:.95rem;line-height:1.4}
#qt-alert.alert--hidden{display:none}
#qt-alert.alert--ok{display:block;background:#e8f7ef;border:1px solid #b8e6cd;color:#11643a}
#qt-alert.alert--err{display:block;background:#fdecec;border:1px solid #f3b1b1;color:#8d1a1a}
.form--styled .field-set{margin-bottom:12px}
.form--styled .caption-wrap label,
.form--styled label{font-weight:600;margin-bottom:6px;display:inline-block}
.form--styled .field-control,
.form--styled select,
.form--styled input[type="text"],
.form--styled input[type="number"],
.form--styled textarea{width:100%;border:1px solid #e2e5ea;border-radius:10px;padding:10px 12px;outline:0}
.form--styled .caption-wrap .note{font-size:.85rem;color:#6b7280}
.card__foot .btn{min-width:120px;border-radius:12px}
.btn[disabled]{opacity:.65;cursor:not-allowed}
</style>

<script>
const LBL_LOADING = <?= json_encode(Label::getLabel('LBL_LOADING')); ?>;
const LBL_SELECT  = <?= json_encode(Label::getLabel('LBL_SELECT')); ?>;
const URL_SUBJECTS_BY_LEVEL = <?= json_encode(MyUtility::makeUrl('Quiztopic','subjectsByLevel')); ?>;
const PRESELECT_SUBJECT_ID  = <?= (int)($data['subject_id'] ?? 0); ?>;

(function () {
  // subjects-by-level
  const levelSel   = document.querySelector('#level_id');
  const subjectSel = document.querySelector('#subject_id');
  function showAlert(ok, msg){
    const el = document.getElementById('qt-alert');
    el.className = 'alert ' + (ok ? 'alert--ok' : 'alert--err');
    el.textContent = msg || (ok ? '<?= Label::getLabel('LBL_SAVED_SUCCESSFULLY'); ?>' : '<?= Label::getLabel('LBL_INVALID_REQUEST'); ?>');
  }
  function hideAlert(){
    const el = document.getElementById('qt-alert');
    el.className = 'alert alert--hidden';
    el.textContent = '';
  }
  function fillSubjects(map, selectedId) {
    subjectSel.innerHTML = '<option value="">' + LBL_SELECT + '</option>';
    Object.keys(map || {}).forEach(id => {
      const opt = document.createElement('option');
      opt.value = id;
      opt.textContent = map[id];
      if (selectedId && String(selectedId) === String(id)) opt.selected = true;
      subjectSel.appendChild(opt);
    });
  }
  function loadSubjects(levelId, selectedId) {
    if (!levelSel || !subjectSel) return;
    if (!levelId) { fillSubjects({}, 0); return; }
    subjectSel.innerHTML = '<option value="">' + LBL_LOADING + '</option>';
    const fd = new FormData();
    fd.append('level_id', levelId);
    fetch(URL_SUBJECTS_BY_LEVEL, {
      method: 'POST',
      body: fd,
      headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(async r => { const t = await r.text(); try { return JSON.parse(t); } catch { throw new Error(t); }})
    .then(j => fillSubjects((j && j.data) || {}, selectedId))
    .catch(() => fillSubjects({}, 0));
  }
  if (levelSel && subjectSel) {
    levelSel.addEventListener('change', () => loadSubjects(levelSel.value, 0));
    if (levelSel.value) loadSubjects(levelSel.value, PRESELECT_SUBJECT_ID);
  }

  // submit -> save (AJAX)
  const form = document.getElementById('frmQuizSetup');
  const btn  = document.getElementById('qt-submit');

  if (form) {
    form.addEventListener('submit', function(e){
      e.preventDefault();
      hideAlert();
      btn && (btn.disabled = true);
      const fd = new FormData(form);

      fetch(<?= json_encode(MyUtility::makeUrl('Quiztopic','save')); ?>, {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
      .then(r => r.json())
      .then(j => {
        if (j && j.status == 1) {
          showAlert(true, j.msg || '<?= Label::getLabel('LBL_SAVED_SUCCESSFULLY'); ?>');
          // If you want to return to list, uncomment the next line
          // setTimeout(() => location.href = <?= json_encode(MyUtility::makeUrl('Quiztopic','index')); ?>, 600);
        } else {
          showAlert(false, (j && j.msg) || '<?= Label::getLabel('LBL_INVALID_REQUEST'); ?>');
        }
      })
      .catch(() => showAlert(false, 'Network error occurred'))
      .finally(() => { btn && (btn.disabled = false); });
    });
  }
})();
</script>
