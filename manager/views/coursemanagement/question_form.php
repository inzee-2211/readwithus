<?php defined('SYSTEM_INIT') or die('Invalid Usage.');

$frm->setFormTagAttribute('class','rwu-form');
$frm->setFormTagAttribute('enctype','multipart/form-data');
$frm->setFormTagAttribute('id','frmQuestion');
$frm->setFormTagAttribute('onsubmit','saveQuestion(this); return false;');

if ($btn = $frm->getField('btn_submit')) {
    $btn->setFieldTagAttribute('type', 'submit');
    $btn->setFieldTagAttribute('id', 'btnSaveQuestion');
    $btn->setFieldTagAttribute('class', trim((string)$btn->getFieldTagAttribute('class') . ' js-btn-save-question'));
}

function rwuField(Form $frm, string $name): string {
    $f = $frm->getField($name);
    if (!$f) return '';
    $cap = $f->getCaption();
    return '<div class="rwu-field">'
        . '<label for="'.htmlspecialchars($name).'">'.htmlspecialchars($cap).'</label>'
        . $frm->getFieldHtml($name)
        . '</div>';
}
?>
<style>
#facebox .content { max-width: 980px; }
.rwu-form { padding: 8px 16px 16px; }
.rwu-grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px 20px; }
@media (max-width: 640px){ .rwu-grid{ grid-template-columns: 1fr; } }
.rwu-full { grid-column: 1 / -1; }
.rwu-field label { font-weight:600; display:block; margin-bottom:6px; }
.rwu-field input[type="text"], .rwu-field select, .rwu-field textarea, .rwu-field input[type="file"] {
  width:100%; box-sizing:border-box; padding:8px 10px; border-radius:6px;
}
.mcq-fields, .text-answer-field { margin-top:4px; }
.text-answer-field { display:block; }
</style>

<section class="section">
  <div class="sectionhead"><h4><?php echo ($q ? 'Edit' : 'Add'); ?> Question</h4></div>
  <div class="sectionbody space">

    <?= $frm->getFormTag(); ?>

      <div class="rwu-grid">
        <?= rwuField($frm,'question_title'); ?>
        <?= rwuField($frm,'question_type'); ?>

        <!-- MCQ -->
        <!-- <div class="mcq-fields rwu-full">
          <div class="rwu-grid">
            <?= rwuField($frm,'answer_a'); ?>
            <?= rwuField($frm,'answer_b'); ?>
            <?= rwuField($frm,'answer_c'); ?>
            <?= rwuField($frm,'answer_d'); ?>
            <?= rwuField($frm,'correct_answer'); ?>
          </div>
        </div> -->
        <div class="mcq-fields rwu-full">
  <div class="rwu-grid">
    <?= rwuField($frm,'option_mode'); ?>
  </div>

  <!-- Text options -->
  <div class="mcq-text-options">
    <div class="rwu-grid">
      <?= rwuField($frm,'answer_a'); ?>
      <?= rwuField($frm,'answer_b'); ?>
      <?= rwuField($frm,'answer_c'); ?>
      <?= rwuField($frm,'answer_d'); ?>
    </div>
  </div>

  <!-- Image options -->
  <div class="mcq-image-options">
    <div class="rwu-grid">
      <?= rwuField($frm,'answer_a_image_file'); ?>
      <?= rwuField($frm,'answer_b_image_file'); ?>
      <?= rwuField($frm,'answer_c_image_file'); ?>
      <?= rwuField($frm,'answer_d_image_file'); ?>
    </div>

    <!-- existing paths hidden -->
    <?= $frm->getFieldHtml('existing_answer_a_image'); ?>
    <?= $frm->getFieldHtml('existing_answer_b_image'); ?>
    <?= $frm->getFieldHtml('existing_answer_c_image'); ?>
    <?= $frm->getFieldHtml('existing_answer_d_image'); ?>
  </div>

  <div class="rwu-grid">
    <?= rwuField($frm,'correct_answer'); ?>
  </div>
</div>


        <!-- Story/Short -->
        <div class="text-answer-field rwu-full">
          <?= rwuField($frm,'correct_answer_text'); ?>
        </div>

        <?= rwuField($frm,'difficult_level'); ?>
        <?= rwuField($frm,'hint'); ?>
        <div class="rwu-full"><?= rwuField($frm,'explanation'); ?></div>

        <?= rwuField($frm,'image'); ?>
        <?= $frm->getFieldHtml('existing_image'); ?>
        <?= $frm->getFieldHtml('id'); ?>
        <?= $frm->getFieldHtml('subtopic_id'); ?>

        <div class="rwu-full">
          <?= $frm->getFieldHtml('btn_submit'); ?>
        </div>
      </div>

    </form>

  </div>
</section>

<script>
(function(){
  function toggleTypeUI(){
  var sel = document.getElementById('question_type') || document.querySelector('[name="question_type"]');
  var t   = sel ? (sel.value || '') : '';

  var mcqWrap = document.querySelector('.mcq-fields');
  var txtWrap = document.querySelector('.text-answer-field');

  var isMcq = (/multiple/i.test(t) || /mcq/i.test(t));

  if (mcqWrap) mcqWrap.style.display = isMcq ? '' : 'none';
  if (txtWrap) txtWrap.style.display = isMcq ? 'none' : '';

  // Option mode toggles (only in MCQ)
  var modeSel = document.getElementById('option_mode') || document.querySelector('[name="option_mode"]');
  var mode = modeSel ? (modeSel.value || 'text') : 'text';

  var textOps = document.querySelector('.mcq-text-options');
  var imgOps  = document.querySelector('.mcq-image-options');

  if (isMcq) {
    if (textOps) textOps.style.display = (mode === 'image') ? 'none' : '';
    if (imgOps)  imgOps.style.display  = (mode === 'image') ? '' : 'none';
  } else {
    if (textOps) textOps.style.display = 'none';
    if (imgOps)  imgOps.style.display  = 'none';
  }
}

toggleTypeUI();
document.addEventListener('change', function(e){
  if (!e.target) return;
  if (e.target.id === 'question_type' || e.target.name === 'question_type' ||
      e.target.id === 'option_mode'  || e.target.name === 'option_mode') {
    toggleTypeUI();
  }
});
})();

function saveQuestion(form){
  var submitBtn = form.querySelector('.js-btn-save-question') || form.querySelector('[type="submit"]');
  if (submitBtn) { submitBtn.disabled = true; submitBtn.innerHTML = 'Saving...'; }

  var fd = new FormData(form);

  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','saveQuestion'); ?>',
    type: 'POST',
    data: fd,
    processData: false,
    contentType: false,
    success: function(res){
      try{ res = (typeof res==='object') ? res : JSON.parse(res); }catch(e){}
      if(res && (res.status == 1 || res.status === '1')){
        if ($.facebox && $.facebox.close) $.facebox.close();
        location.reload();
      } else {
        alert((res && (res.msg || res.message)) ? (res.msg || res.message) : 'Save failed');
      }
    },
    error: function(xhr){
      alert('Network/Server error while saving.\n' + (xhr && xhr.responseText ? xhr.responseText : ''));
    },
    complete: function(){
      if (submitBtn) { submitBtn.disabled = false; submitBtn.innerHTML = 'Save'; }
    }
  });

  return false;
}
</script>
