<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->setFormTagAttribute('enctype', 'multipart/form-data'); // ✅ keep upload support
?>
<style>
    .rightalign {
        display: flex;
        justify-content: flex-end;
        margin-right: 100px;
    }
    .hidden { display: none; }
    .existing-file {
        margin-top: 10px;
        padding: 10px;
        background: #f9f9f9;
        border-radius: 4px;
        border-left: 4px solid #007bff;
    }
</style>

<section class="section">
    <div class="sectionhead d-flex justify-content-between align-items-center">
        <h4 class="mb-0">
            <?php echo ($categoryId > 0) ? Label::getLabel('LBL_EDIT_QUIZ') : Label::getLabel('LBL_ADD_QUIZ'); ?>
        </h4>
        <a href="<?php echo CONF_WEBROOT_FRONT_URL . 'public/uploads/sample_csv/questions.csv'; ?>"
           class="btn btn--primary btn--sm"
           download
           title="Download sample course import file">
            <i class="ion-android-download"></i> Download Sample CSV
        </a>
    </div>

    <div class="sectionbody space">
        <div class="row">
            <div class="col-sm-12">
                <div class="tabs_nav_container responsive flat">
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                            <?php echo $frm->getFormHtml(); ?>

                            <!-- Show existing PDF if available -->
                            <?php
                            $existingPdfField = $frm->getField('pdf_path');
                            $existingPdf = $existingPdfField ? $existingPdfField->value : '';
                            if (!empty($existingPdf)):
                            ?>
                            <div class="existing-file">
                                <strong>Existing PDF:</strong>
                                <a href="<?php echo $existingPdf; ?>" target="_blank" style="margin-left: 10px; color: #007bff;">
                                    <i class="ion-document-text"></i> View Current PDF
                                </a>
                                <div style="font-size: 12px; color: #666; margin-top: 5px;">
                                    Upload a new PDF to replace this file.
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
  // ======= Preselected values from PHP (edit mode friendly) =======
  const initialData = {
    subjectId:   <?php echo json_encode($data['subject_id']    ?? 0); ?>,
    examboardId: <?php echo json_encode($data['examboard_id']  ?? 0); ?>,
    tierId:      <?php echo json_encode($data['tier_id']       ?? 0); ?>,
    topicId:     <?php echo json_encode($data['quiz_setup_id'] ?? 0); ?>
  };

  // ======= Elements =======
  const $ = (sel)=>document.querySelector(sel);
  const subj  = $('#subject_id,[name="subject_id"]');
  const board = $('#examboard_id,[name="examboard_id"]');
  const tier  = $('#tier_id,[name="tier_id"]');
  const topic = $('#quiz_setup_id,[name="quiz_setup_id"]');

  // ======= Endpoints =======
  const URL_TOPICS    = <?= json_encode(MyUtility::makeUrl('Coursemanagement','topicsBySubject')); ?>;
  const URL_EXAMBOARD = <?= json_encode(MyUtility::makeUrl('Coursemanagement','getexamboardforsubject')); ?>;
  const URL_TIER      = <?= json_encode(MyUtility::makeUrl('Coursemanagement','getTierforExamboard')); ?>;

  function refreshPlugins(el) {
    if (!el) return;
    if (window.jQuery && jQuery(el).data('select2')) {
      jQuery(el).trigger('change.select2');
    }
    if (window.jQuery && jQuery.fn.selectpicker) {
      jQuery(el).selectpicker('refresh');
    }
  }

  function setOptions(selectEl, data, placeholder) {
    if (!selectEl) return;
    selectEl.innerHTML = '';
    const opt0 = document.createElement('option');
    opt0.value = '';
    opt0.textContent = placeholder;
    selectEl.appendChild(opt0);

    if (!data) {
      refreshPlugins(selectEl);
      return;
    }

    // Supports {id: name} OR [{id,name}, ...]
    if (Array.isArray(data)) {
      data.forEach(o => {
        if (!o) return;
        const opt = document.createElement('option');
        opt.value = o.id;
        opt.textContent = o.name;
        selectEl.appendChild(opt);
      });
    } else {
      Object.keys(data).forEach(id => {
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = data[id];
        selectEl.appendChild(opt);
      });
    }
    refreshPlugins(selectEl);
  }

  async function postJSON(url, payload) {
    const fd = new FormData();
    Object.keys(payload).forEach(k => {
      const v = payload[k];
      if (v !== undefined && v !== null) fd.append(k, v);
    });
    const res = await fetch(url, { method: 'POST', body: fd, headers: {'X-Requested-With':'XMLHttpRequest'} });
    const txt = await res.text();
    try { return JSON.parse(txt); } catch (e) { console.warn('JSON parse error:', e, txt); return null; }
  }

  // ========== Loaders ==========
  async function loadExamboards(subjectId, preselect = 0) {
    setOptions(board, null, <?= json_encode(Label::getLabel('LBL_LOADING')); ?> + '…');
    setOptions(tier,  null, <?= json_encode(Label::getLabel('LBL_SELECT_TIER')); ?>);
    setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);

    if (!subjectId) {
      setOptions(board, null, <?= json_encode(Label::getLabel('LBL_SELECT_EXAMBOARD')); ?>);
      return;
    }
    const json = await postJSON(URL_EXAMBOARD, { subjectId: subjectId });
    if (!json || json.status !== 1) {
      setOptions(board, null, <?= json_encode(Label::getLabel('LBL_NO_RECORD_FOUND')); ?>);
      return;
    }
    const map = json.data || {};
    delete map.add_new; // remove the “add new” synthetic entry if your endpoint adds it
    const hasAny = Object.keys(map).length > 0;
    setOptions(board, map, hasAny ? <?= json_encode(Label::getLabel('LBL_SELECT_EXAMBOARD')); ?> : <?= json_encode(Label::getLabel('LBL_NO_RECORD_FOUND')); ?>);

    if (preselect && map[preselect]) {
      board.value = String(preselect);
      refreshPlugins(board);
    }
  }

  async function loadTiers(examboardId, preselect = 0) {
    setOptions(tier,  null, <?= json_encode(Label::getLabel('LBL_LOADING')); ?> + '…');
    setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);

    if (!examboardId) {
      setOptions(tier, null, <?= json_encode(Label::getLabel('LBL_SELECT_TIER')); ?>);
      return;
    }
    const json = await postJSON(URL_TIER, { examboardId: examboardId });
    if (!json || json.status !== 1) {
      setOptions(tier, null, <?= json_encode(Label::getLabel('LBL_NO_RECORD_FOUND')); ?>);
      return;
    }
    const map = json.data || {};
    delete map.add_new;
    const hasAny = Object.keys(map).length > 0;
    setOptions(tier, map, hasAny ? <?= json_encode(Label::getLabel('LBL_SELECT_TIER')); ?> : <?= json_encode(Label::getLabel('LBL_NO_RECORD_FOUND')); ?>);

    if (preselect && map[preselect]) {
      tier.value = String(preselect);
      refreshPlugins(tier);
    }
  }

  async function loadTopics(subjectId, examboardId, tierId, preselectTopicId = 0) {
    setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_LOADING')); ?> + '…');
    if (!subjectId) {
      setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);
      return;
    }
    const json = await postJSON(URL_TOPICS, {
      subject_id: subjectId,
      examboard_id: examboardId || 0,
      tier_id: tierId || 0
    });
    const map = (json && json.data) || {};
    const hasAny = Object.keys(map).length > 0;
    setOptions(topic, map, hasAny ? <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?> : <?= json_encode(Label::getLabel('LBL_NO_RECORD_FOUND')); ?>);

    if (preselectTopicId && map[preselectTopicId]) {
      topic.value = String(preselectTopicId);
      refreshPlugins(topic);
    }
  }

  // ========== Event wiring ==========
  if (subj) {
    subj.addEventListener('change', async () => {
      await loadExamboards(subj.value, 0);
      setOptions(tier,  null, <?= json_encode(Label::getLabel('LBL_SELECT_TIER')); ?>);
      setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);
    });
  }
  if (board) {
    board.addEventListener('change', async () => {
      await loadTiers(board.value, 0);
      setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);
    });
  }
  if (tier) {
    tier.addEventListener('change', async () => {
      await loadTopics(subj.value, board.value, tier.value, 0);
    });
  }

  // ========== Boot (edit/new) ==========
  (async function boot() {
    // Edit mode with preselected IDs
    if (subj && (initialData.subjectId || subj.value)) {
      const s = String(initialData.subjectId || subj.value);
      subj.value = s;
      refreshPlugins(subj);
      await loadExamboards(s, initialData.examboardId || 0);
    } else {
      // New form with no subject: ensure clean placeholders
      setOptions(board, null, <?= json_encode(Label::getLabel('LBL_SELECT_EXAMBOARD')); ?>);
      setOptions(tier,  null, <?= json_encode(Label::getLabel('LBL_SELECT_TIER')); ?>);
      setOptions(topic, null, <?= json_encode(Label::getLabel('LBL_SELECT_TOPIC')); ?>);
      return;
    }

    if (initialData.examboardId) {
      await loadTiers(String(initialData.examboardId), initialData.tierId || 0);
    }

    if (initialData.tierId) {
      await loadTopics(subj.value, board.value, tier.value, initialData.topicId || 0);
    } else {
      // Backward compatibility: allow topics by subject alone if board/tier not set
      await loadTopics(subj.value, 0, 0, initialData.topicId || 0);
    }
  })();
})();
</script>

<script>
// Unchanged: AJAX submit handler
function setup(frm) {
  const submitBtn = frm.querySelector('button[type="submit"]');
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) { submitBtn.innerHTML = 'Saving...'; submitBtn.disabled = true; }

  var fd = new FormData(frm);

  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','setup'); ?>',
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    success: function (res) {
      try { res = JSON.parse(res); } catch (e) {}
      if (submitBtn) { submitBtn.innerHTML = originalText; submitBtn.disabled = false; }

      if (res && (res.status == 1 || res.msg)) {
        $.facebox.close();
        if (window.search) { search(document.frmSearch); }
      } else {
        alert((res && res.msg) ? res.msg : 'Save failed.');
      }
    },
    error: function () {
      if (submitBtn) { submitBtn.innerHTML = originalText; submitBtn.disabled = false; }
      alert('Network error while saving.');
    }
  });

  return false;
}
</script>
