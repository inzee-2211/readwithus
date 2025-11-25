<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
/* ---------- Layout & background fix ---------- */
.rwu-qb-wrap {
  padding: 20px;
  margin-left: 220px;          /* match sidebar width */
  background: #fff;            /* ✅ white background */
  border-radius: 10px;         /* smooth corners like other cards */
  box-shadow: 0 1px 4px rgba(0,0,0,0.08); /* subtle shadow for card feel */
}

@media (max-width: 1024px){
  .rwu-qb-wrap{ margin-left:0; }
}

/* ---------- Table styling ---------- */
.rwu-qb-wrap .table {
  width: 100%;
  border-collapse: collapse;
  background: #fff;
}

.rwu-qb-wrap .table th {
  background: #f9fafb; /* light grey header */
  font-weight: 600;
  color: #333;
  border-bottom: 2px solid #e5e7eb;
  padding: 10px;
  text-align: left;
}

.rwu-qb-wrap .table td {
  padding: 10px;
  border-bottom: 1px solid #f0f0f0;
  vertical-align: top;
  color: #444;
}

.rwu-qb-wrap .table tr:hover td {
  background: #f6f8fa;
}

/* ---------- Hint & image polish ---------- */
.rwu-hint {
  display: block;
  margin-top: 4px;
  color: #666;
  font-size: 12px;
  font-style: italic;
}

.rwu-qb-wrap img {
  background: #fff;
  border-radius: 6px;
  box-shadow: 0 0 2px rgba(0,0,0,0.1);
}

/* ---------- Button area ---------- */
.buttons-group {
  text-align: right;
  margin-bottom: 10px;
}

/* ---------- Search bar ---------- */
.rwu-search-bar {
  margin: 10px 0 20px;
  display: flex;
  gap: 10px;
  align-items: center;
}

.rwu-search-bar input.search-input {
  width: 100%;
  padding: 8px 12px;
  height: 38px;
  border-radius: 6px;
  border: 1px solid #cbd5e1;
  box-sizing: border-box;
}

.rwu-search-bar .btn-primary,
.rwu-search-bar .btn-secondary {
  height: 38px;
  padding: 0 18px;
  border-radius: 6px;
  border: none;
  cursor: pointer;
}

.rwu-search-bar .btn-primary {
  background: #ff6a00;          /* YoCoach orange primary */
  color: #fff;
}

.rwu-search-bar .btn-secondary {
  background: #e5e7eb;
  color: #111827;
}

/* ---------- Facebox fix ---------- */
#facebox .close { z-index: 1001; }
</style>


<div class="rwu-qb-wrap">
  <div class="sectionhead">
    <h4>
      <?php echo Label::getLabel('LBL_QUESTION_BANK'); ?>:
      <small>
        <?php
          echo htmlspecialchars($subtopic['subject']);
          echo ' → ' . htmlspecialchars($subtopic['topic_name']);
          echo ' → ' . htmlspecialchars($subtopic['subtopic_name']);
        ?>
      </small>
    </h4>
  </div>

  <div class="buttons-group" style="padding:4px; text-align:right;">
    <a href="javascript:void(0);"
       onclick="questionForm(<?php echo (int)$subtopic['id'];?>, 0)"
       class="btn btn--secondary btn--sm"
       style="margin-left:2px">
       <?php echo Label::getLabel('LBL_ADD_QUESTION'); ?>
    </a>
  </div>

<?php
/* ======================
   SEARCH FORM (keyword)
   ====================== */

/** @var Form $srchFrm */
$srchFrm->setFormTagAttribute('method', 'GET');
$srchFrm->setFormTagAttribute(
    'action',
    MyUtility::makeUrl('Coursemanagement', 'questionBank', [(int)$subtopic['id']])
);
$srchFrm->setFormTagAttribute('id', 'frmQbSearch');
$srchFrm->setFormTagAttribute('onsubmit', 'return qbSearch(this);');

$srchFrm->developerTags['colClassPrefix'] = 'col-md-';
$srchFrm->developerTags['fld_default_col'] = 12;

// keyword input style + placeholder
$kwFld = $srchFrm->getField('keyword');
$kwFld->addFieldtagAttribute('class', 'search-input');
$kwFld->addFieldtagAttribute(
    'placeholder',
    Label::getLabel('LBL_SEARCH_BY_QUESTION_TITLE')
);

// buttons styling
$btnSearch = $srchFrm->getField('btn_submit');
$btnSearch->addFieldTagAttribute('class', 'btn-primary');

$btnReset = $srchFrm->getField('btn_reset');
$btnReset->addFieldTagAttribute('class', 'btn-secondary');
$btnReset->addFieldTagAttribute('type', 'button');
$btnReset->addFieldTagAttribute('onclick', 'qbClear();');

// open form tag
echo $srchFrm->getFormTag();
?>
  <div class="rwu-search-bar">
    <div style="flex:1;">
      <?php echo $srchFrm->getFieldHtml('keyword'); ?>
    </div>
    <div>
      <?php echo $srchFrm->getFieldHtml('btn_submit'); ?>
    </div>
    <div>
      <?php echo $srchFrm->getFieldHtml('btn_reset'); ?>
    </div>
  </div>
</form>

  <?php if (!empty($questions)) { ?>
  <table class="table table--hovered table-responsive">
    <thead>
      <tr>
        <th>#</th>
        <th><?php echo Label::getLabel('LBL_QUESTION'); ?></th>
        <th><?php echo Label::getLabel('LBL_OPTIONS'); ?></th>
        <th><?php echo Label::getLabel('LBL_CORRECT_ANSWER'); ?></th>
        <th><?php echo Label::getLabel('LBL_DIFFICULTY'); ?></th>
        <th><?php echo Label::getLabel('LBL_TYPE'); ?></th>
        <th><?php echo Label::getLabel('LBL_IMAGE'); ?></th>
        <th><?php echo Label::getLabel('LBL_EXPLANATION'); ?></th>
        <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($questions as $i => $q) {
        $explanation = $q['explanation'] ?? '';
        $shortExplanation = strlen($explanation) > 100
          ? substr($explanation, 0, 100) . '...'
          : $explanation;
      ?>
      <tr>
        <td><?= $i + 1; ?></td>
        <td>
          <?= $q['question_title']; ?>
          <?php if (!empty($q['hint'])) { ?>
            <small class="rwu-hint">
              <?= Label::getLabel('LBL_HINT'); ?>:
              <?= html_entity_decode($q['hint'], ENT_QUOTES, 'UTF-8'); ?>
            </small>
          <?php } ?>
        </td>
        <td>
          A. <?= $q['answer_a']; ?><br>
          B. <?= $q['answer_b']; ?><br>
          C. <?= $q['answer_c']; ?><br>
          D. <?= $q['answer_d']; ?>
        </td>
        <td><?= $q['correct_answer']; ?></td>
        <td><?= $q['difficult_level']; ?></td>
        <td><?= $q['question_type']; ?></td>
        <td>
          <?php if (!empty($q['image'])) { ?>
            <img src="/<?= $q['image']; ?>"
                 style="max-width:100px;height:auto;border:1px solid #ddd;padding:2px;border-radius:4px"><br>
            <small class="rwu-hint"><?= htmlspecialchars($q['image']); ?></small>
          <?php } else { ?>
            <em>-</em>
          <?php } ?>
        </td>
        <td class="explanation-cell">
          <?php if (!empty($q['explanation'])) {
            $explanation = (string)$q['explanation'];
            $short = mb_strlen($explanation) > 120
              ? mb_substr($explanation, 0, 120) . '…'
              : $explanation;
          ?>
            <div class="short-explanation">
              <?= nl2br(htmlspecialchars($short)); ?>
              <?php if (mb_strlen($explanation) > 120) { ?>
                <br>
                <a href="javascript:void(0)"
                   onclick="document.getElementById('full-expl-<?= $i; ?>').style.display='block'; this.parentNode.style.display='none';">
                  <?= Label::getLabel('LBL_VIEW_FULL'); ?>
                </a>
              <?php } ?>
            </div>
            <div id="full-expl-<?= $i; ?>" style="display:none;">
              <?= nl2br(htmlspecialchars($explanation)); ?>
            </div>
          <?php } else { ?>
            <em>-</em>
          <?php } ?>
        </td>
        <td>
          <a href="javascript:void(0);"
             onclick="questionForm(<?= (int)$subtopic['id']; ?>, <?= (int)$q['id']; ?>)">Edit</a>
          &nbsp;|&nbsp;
          <a href="javascript:void(0);"
             onclick="deleteQuestion(<?= (int)$q['id']; ?>)">Delete</a>
        </td>
      </tr>
      <?php } ?>
    </tbody>
  </table>
  <?php } else { ?>
    <p><?php echo Label::getLabel('LBL_NO_QUESTIONS_FOUND'); ?></p>
  <?php } ?>
</div>

<script>
/* ======= keyword search (runtime refresh via URL) ======= */
function qbSearch(frm){
  var kw = (frm.keyword && frm.keyword.value || '').trim();
  var base = "<?= MyUtility::makeUrl('Coursemanagement','questionBank',[(int)$subtopic['id']]); ?>";
  if (kw) {
    base += '?keyword=' + encodeURIComponent(kw);
  }
  window.location.href = base;
  return false;
}

function qbClear(){
  window.location.href = "<?= MyUtility::makeUrl('Coursemanagement','questionBank',[(int)$subtopic['id']]); ?>";
}

/* =========================================================================
   Facebox hard-guard
   ========================================================================= */
(function(){
  if (!window.__rwuFaceboxPatched) {
    $(document).on('afterClose.facebox.rwu', function(){
      $('#facebox .close:not(:first)').remove();
    });
    window.__rwuFaceboxPatched = true;
  }
})();

function openFacebox(html){
  if ($('#facebox').is(':visible')) { $.facebox.close(); }
  $.facebox(html);
  $(document).one('reveal.facebox.rwu', function(){
    var $closes = $('#facebox .close');
    if ($closes.length > 1) $closes.not(':first').remove();
  });
}

function categoryForm(id){
  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','subtopicForm'); ?>/' + (id || 0),
    type: 'GET',
    success: function (html) { openFacebox(html); }
  });
}

function questionForm(subtopicId, id){
  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','questionForm'); ?>/' + subtopicId + '/' + (id || 0),
    type: 'GET',
    success: function (html) { openFacebox(html); }
  });
}

function deleteQuestion(id){
  if(!confirm('Delete this question?')) return;
  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','deleteQuestion'); ?>/' + id,
    type: 'POST',
    success: function(res){
      try{res=JSON.parse(res);}catch(e){}
      if(res && res.status==1){ location.reload(); }
      else { alert(res && res.msg ? res.msg : 'Delete failed'); }
    },
    error: function(){ alert('Network error'); }
  });
}
</script>
