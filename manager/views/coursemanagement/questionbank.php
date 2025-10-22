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

/* .buttons-group .btn--secondary { */
  /* background-color: #2563eb; blue tone */
  /* color: #fff;
  border-radius: 6px;
  padding: 6px 12px;
  font-size: 14px;
  transition: 0.2s;
} */

/* .buttons-group .btn--secondary:hover {
  background-color: #1d4ed8;
} */

/* ---------- Facebox fix ---------- */
#facebox .close { z-index: 1001; }
</style>


<div class="rwu-qb-wrap">
  <div class="sectionhead">
    <h4>
      <?php echo Label::getLabel('LBL_QUESTION_BANK'); ?>:
      <small><?php echo htmlspecialchars($subtopic['subject']); ?> → <?php echo htmlspecialchars($subtopic['topic_name']); ?> → <?php echo htmlspecialchars($subtopic['subtopic_name']); ?></small>
    </h4>
  </div>

  <div class="buttons-group" style="padding:4px; text-align:right;">
    <!-- <a href="javascript:void(0);" onclick="categoryForm(<?php echo (int)$subtopic['id'];?>)" class="btn btn--primary btn--sm"><?php echo Label::getLabel('LBL_EDIT_SUBTOPIC'); ?></a> -->
    <a href="javascript:void(0);" onclick="questionForm(<?php echo (int)$subtopic['id'];?>, 0)" class="btn btn--secondary btn--sm" style="margin-left:2px"><?php echo Label::getLabel('LBL_ADD_QUESTION'); ?></a>
  </div>

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
        <th><?php echo Label::getLabel('LBL_ACTION'); ?></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($questions as $i => $q) {
  // normalize to be safe against “MCQ”, “Multiple-Choice”, “multiple choice”, etc.
  $type = trim((string)$q['question_type']);
  $isMcq = (stripos($type, 'mcq') !== false) || (stripos($type, 'multiple') !== false);
?>
  <tr>
    <td><?= $i+1; ?></td>
    <td>
      <?= $q['question_title']; ?>
      <?php if(!empty($q['hint'])){ ?>
        <small class="rwu-hint">
          <?= Label::getLabel('LBL_HINT'); ?>:
          <?= html_entity_decode($q['hint'], ENT_QUOTES, 'UTF-8'); ?>
        </small>
      <?php } ?>
    </td>
    <td>
      <?php if ($isMcq) { ?>
        A. <?= $q['answer_a']; ?><br>
        B. <?= $q['answer_b']; ?><br>
        C. <?= $q['answer_c']; ?><br>
        D. <?= $q['answer_d']; ?>
      <?php } else { ?>
        <em><?= htmlspecialchars($q['answer_a']); ?></em>
      <?php } ?>
    </td>
    <td><?= $isMcq ? $q['correct_answer'] : '-'; ?></td>
    <td><?= $q['difficult_level']; ?></td>
    <td><?= $q['question_type']; ?></td>
    <td>
      <?php if (!empty($q['image'])) { ?>
        <img src="/<?= $q['image']; ?>" style="max-width:100px;height:auto;border:1px solid #ddd;padding:2px;border-radius:4px">
      <?php } ?>
    </td>
    <td>
      <a href="javascript:void(0);" onclick="questionForm(<?= (int)$subtopic['id']; ?>, <?= (int)$q['id']; ?>)">Edit</a>
      &nbsp;|&nbsp;
      <a href="javascript:void(0);" onclick="deleteQuestion(<?= (int)$q['id']; ?>)">Delete</a>
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
/* =========================================================================
   Facebox hard-guard: remove any previous containers/handlers before open
   This fixes the “multiple × buttons” issue even if the plugin was bound twice.
   ========================================================================== */
(function(){
  // One-time namespaced global init guard
  if (!window.__rwuFaceboxPatched) {
    // When Facebox closes, ensure DOM is clean
    $(document).on('afterClose.facebox.rwu', function(){
      $('#facebox .close:not(:first)').remove();
      // (optional) If your theme injects extra wrappers, you can strip them here
    });
    window.__rwuFaceboxPatched = true;
  }
})();

function openFacebox(html){
  // Close if already open to reset state
  if ($('#facebox').is(':visible')) { $.facebox.close(); }
  // Open fresh
  $.facebox(html);
  // After reveal, make sure there's only one close button
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
