<?php defined('SYSTEM_INIT') or die('Invalid Usage.');

// ---------- Form setup ----------
$frm->setFormTagAttribute('class','rwu-form');
$frm->setFormTagAttribute('enctype','multipart/form-data');
$frm->setFormTagAttribute('id','frmQuestion');
$frm->setFormTagAttribute('onsubmit','saveQuestion(this); return false;');

/* Ensure submit control is a real submit button */
if ($btn = $frm->getField('btn_submit')) {
    $btn->setFieldTagAttribute('type', 'submit');
    $btn->setFieldTagAttribute('id', 'btnSaveQuestion');
    $btn->setFieldTagAttribute('class', trim((string)$btn->getFieldTagAttribute('class') . ' js-btn-save-question'));
}
?>
<style>
#facebox .content { max-width: 980px; }
.rwu-form { padding: 8px 16px 16px; }
.rwu-grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px 20px; }
@media (max-width: 640px){ .rwu-grid{ grid-template-columns: 1fr; } }
.rwu-full { grid-column: 1 / -1; }
.rwu-form label { font-weight:600; display:block; margin-bottom:6px; }
.rwu-form input[type="text"], .rwu-form select, .rwu-form textarea { width:100%; box-sizing:border-box; padding:8px 10px; border-radius:6px; }
.mcq-fields, .text-answer-field { margin-top:4px; }
.text-answer-field { display:none; }
.rwu-imgprev { margin-top:8px; }
.rwu-imgprev img{ max-width:240px; height:auto; border:1px solid #ddd; padding:4px; border-radius:6px; }
</style>

<section class="section">
  <div class="sectionhead"><h4><?php echo ($q ? 'Edit' : 'Add'); ?> Question</h4></div>
  <div class="sectionbody space">

    <?= $frm->getFormTag(); // ✅ OPEN <form ...> ?>

      <div class="rwu-grid">
        <?php
          echo $frm->getFieldHtml('question_title');
          echo $frm->getFieldHtml('question_type');
        ?>

        <div class="mcq-fields rwu-full">
          <div class="rwu-grid">
            <?php
              echo $frm->getFieldHtml('answer_a');
              echo $frm->getFieldHtml('answer_b');
              echo $frm->getFieldHtml('answer_c');
              echo $frm->getFieldHtml('answer_d');
              echo $frm->getFieldHtml('correct_answer');
            ?>
          </div>
        </div>

        <div class="text-answer-field rwu-full">
          <?= $frm->getFieldHtml('answer_text'); ?>
        </div>

        <?php
          echo $frm->getFieldHtml('difficult_level');
          echo $frm->getFieldHtml('hint');
          echo $frm->getFieldHtml('explanation');
          echo $frm->getFieldHtml('image');
          echo $frm->getFieldHtml('existing_image');
          echo $frm->getFieldHtml('id');
          echo $frm->getFieldHtml('subtopic_id');
        ?>

        <div class="rwu-full">
          <?= $frm->getFieldHtml('btn_submit'); ?>
        </div>
      </div>

    </form> <!-- ✅ CLOSE form -->

    <?php if (!empty($q['image'])) { ?>
      <div class="rwu-imgprev">
        <strong>Current Image:</strong><br>
        <img src="/<?php echo $q['image']; ?>" alt="Current image">
      </div>
    <?php } ?>
  </div>
</section>

<script>
(function(){
  // If the builder ever outputs a non-submit button, trigger a submit programmatically.
  document.addEventListener('click', function(e){
    var el = e.target.closest('.js-btn-save-question');
    if(!el) return;
    var form = document.getElementById('frmQuestion');
    if(!form) return;
    if (!el.type || el.type.toLowerCase() !== 'submit') {
      if (form.requestSubmit) form.requestSubmit();
      else form.submit();
    }
  });

  function toggleTypeUI(){
    var sel = document.getElementById('question_type') || document.querySelector('[name="question_type"]');
    var t   = sel ? (sel.value || '') : '';
    var mcq = document.querySelector('.mcq-fields');
    var txt = document.querySelector('.text-answer-field');
    if(!mcq || !txt) return;
    if (/multiple/i.test(t) || /mcq/i.test(t)){
      mcq.style.display='';  txt.style.display='none';
    } else {
      mcq.style.display='none'; txt.style.display='';
    }
  }
  toggleTypeUI();
  document.addEventListener('change', function(e){
    if (e.target && (e.target.id === 'question_type' || e.target.name === 'question_type')) toggleTypeUI();
  });
})();

/* AJAX submit */
function saveQuestion(form){
  var submitBtn = form.querySelector('.js-btn-save-question') || form.querySelector('[type="submit"]');
  if (submitBtn) {
    submitBtn.disabled = true;
    submitBtn.dataset.originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = 'Saving...';
  }

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
        if ($.facebox && $.facebox.close) { $.facebox.close(); }
        location.reload();
      } else {
        alert((res && (res.msg || res.message)) ? (res.msg || res.message) : 'Save failed');
      }
    },
    error: function(xhr){
      alert('Network/Server error while saving.\n' + (xhr && xhr.responseText ? xhr.responseText : ''));
    },
    complete: function(){
      if (submitBtn) {
        submitBtn.disabled = false;
        submitBtn.innerHTML = submitBtn.dataset.originalText || 'Save';
      }
    }
  });

  return false;
}
</script>
