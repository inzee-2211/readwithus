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
        <div class="mcq-fields rwu-full">
          <div class="rwu-grid">
            <?= rwuField($frm,'answer_a'); ?>
            <?= rwuField($frm,'answer_b'); ?>
            <?= rwuField($frm,'answer_c'); ?>
            <?= rwuField($frm,'answer_d'); ?>
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
    var mcq = document.querySelector('.mcq-fields');
    var txt = document.querySelector('.text-answer-field');
    if(!mcq || !txt) return;

    var isMcq = (/multiple/i.test(t) || /mcq/i.test(t));

    mcq.style.display = isMcq ? '' : 'none';
    txt.style.display = isMcq ? 'none' : '';

    // OPTIONAL: clear irrelevant fields when switching type
    if (!isMcq) {
      ['answer_a','answer_b','answer_c','answer_d'].forEach(function(n){
        var el = document.querySelector('[name="'+n+'"]'); if (el) el.value = '';
      });
      var ca = document.querySelector('[name="correct_answer"]'); if (ca) ca.value = '';
    } else {
      var cat = document.querySelector('[name="correct_answer_text"]'); if (cat) cat.value = '';
    }
  }

  toggleTypeUI();
  document.addEventListener('change', function(e){
    if (e.target && (e.target.id === 'question_type' || e.target.name === 'question_type')) toggleTypeUI();
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
