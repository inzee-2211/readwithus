<?php defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->setFormTagAttribute('class','rwu-form');
$frm->setFormTagAttribute('enctype','multipart/form-data');
$frm->setFormTagAttribute('onsubmit','saveQuestion(this); return false;');
?>
<style>
/* Facebox content cleanup */
#facebox .content { max-width: 980px; }

/* Form layout (scoped) */
.rwu-form { padding: 8px 16px 16px; }
.rwu-grid { display:grid; grid-template-columns: 1fr 1fr; gap:16px 20px; }
@media (max-width: 640px){ .rwu-grid{ grid-template-columns: 1fr; } }
.rwu-full { grid-column: 1 / -1; }

.rwu-form label { font-weight:600; display:block; margin-bottom:6px; }
.rwu-form input[type="text"],
.rwu-form select,
.rwu-form textarea {
  width:100%; box-sizing:border-box; padding:8px 10px; border-radius:6px;
}

.mcq-fields, .text-answer-field { margin-top:4px; }
.text-answer-field { display:none; }

/* tidy image preview */
.rwu-imgprev { margin-top:8px; }
.rwu-imgprev img{ max-width:240px; height:auto; border:1px solid #ddd; padding:4px; border-radius:6px; }
</style>

<section class="section">
  <div class="sectionhead"><h4><?php echo ($q ? 'Edit' : 'Add'); ?> Question</h4></div>
  <div class="sectionbody space">
    <div class="rwu-grid">
      <?php
        // We’ll let the form helper output fields, but we hint layout by ordering.
        echo $frm->getFieldHtml('question_title');          // full row by CSS below
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
        <?php echo $frm->getFieldHtml('answer_text'); ?>
      </div>

      <?php
        echo $frm->getFieldHtml('difficult_level');
        echo $frm->getFieldHtml('hint');
        echo $frm->getFieldHtml('image');
        echo $frm->getFieldHtml('existing_image');
        echo $frm->getFieldHtml('id');
        echo $frm->getFieldHtml('subtopic_id');
      ?>

      <div class="rwu-full">
        <?php echo $frm->getFieldHtml('btn_submit'); ?>
      </div>
    </div>

    <?php if (!empty($q['image'])) { ?>
      <div class="rwu-imgprev">
        <strong>Current Image:</strong><br>
        <img src="/<?php echo $q['image']; ?>">
      </div>
    <?php } ?>
  </div>
</section>

<script>
function saveQuestion(f){
  var fd = new FormData(f);
  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','saveQuestion'); ?>',
    type: 'POST',
    data: fd, processData:false, contentType:false,
    success: function(res){
      try{ res = JSON.parse(res); }catch(e){}
      if(res && res.status==1){ $.facebox.close(); location.reload(); }
      else{ alert(res && res.msg ? res.msg : 'Save failed'); }
    },
    error: function(){ alert('Network error'); }
  });
}

function toggleTypeUI(){
  var sel = document.getElementById('question_type');
  var t   = sel ? sel.value : 'Multiple-Choice';
  var mcq = document.querySelector('.mcq-fields');
  var txt = document.querySelector('.text-answer-field');
  if(!mcq || !txt) return;
  if (/multiple/i.test(t) || /mcq/i.test(t)){
    mcq.style.display='';  txt.style.display='none';
  }else{
    mcq.style.display='none'; txt.style.display='';
  }
}
(function(){ toggleTypeUI(); $(document).on('change','#question_type',toggleTypeUI); })();
</script>
