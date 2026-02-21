<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="container container--fixed">
  <div class="page__head">
    <h1><?php echo Label::getLabel('LBL_ADD_NEW_EXAM'); ?> (CSV)</h1>
    <p class="margin-0">
      Upload a CSV to create an exam automatically.
      <br><b>Note:</b> CSV filename will be used as exam title. Description will be saved as NULL.
    </p>
  </div>

  <div class="page__body">
    <div class="box box--white" style="padding:18px;">
      <form class="form" id="quizBulkUploadFrm" enctype="multipart/form-data">

        <div class="row">
          <div class="col-md-8">

            <div class="field-set">
              <label class="field_label">CSV File <span class="spn_must_field">*</span></label>
              <div class="field-wraper"><div class="field_cover">
                <input type="file" name="bulk_csv" accept=".csv" required>
                <small>CSV UTF-8. Delimiter supported: comma (,) or semicolon (;).</small>
              </div></div>
            </div>

            <div class="field-set">
              <label class="field_label">Images ZIP (optional)</label>
              <div class="field-wraper"><div class="field_cover">
                <input type="file" name="bulk_images" accept=".zip">
                <small>Reference images by filename in CSV. HTTP/HTTPS image URLs also supported.</small>
              </div></div>
            </div>

            <div class="field-set">
              <label class="field_label">CSV Columns (must match question bulk importer)</label>
              <div class="field-wraper"><div class="field_cover">
<pre style="white-space:pre-wrap;margin:0;">
Option A (friendly):
title,type(1 single,2 multiple,3 text),marks,category_id,subcategory_id
description,math_equation,hint,option_1..option_4,correct_answers,image

Option B (DB-like):
question_title,question_type,question_marks,question_cat,question_subcat
question_desc,question_math_equation,question_hint,question_option_1..4,question_answers,image_filename
</pre>
              </div></div>
            </div>

           <div class="field-set" style="margin-top:10px;">
  <div class="field-wraper">
    <div class="field_cover">
      <button type="button" class="btn btn--primary" onclick="downloadSampleCSV();" style="display:inline-flex;align-items:center;gap:8px;">
        <i class="fa fa-download"></i> Download Sample CSV
      </button>
    </div>
  </div>
</div>

          </div>

          <div class="col-md-4">
            <div class="box box--white" style="padding:12px;">
              <h6 style="margin-top:0;">Defaults that will be applied</h6>
              <ul style="margin:0; padding-left:16px;">
                <li>Pass % = 70</li>
                <li>Offer certificate = No</li>
                <li>Duration/Validity = defaults (backend)</li>
                <li>Pass/Fail messages = defaults (backend)</li>
              </ul>
            </div>
          </div>
        </div>

   <hr style="margin:16px 0; opacity:.25;">

<div class="row">
  <div class="col-sm-12">
    <div class="field-set margin-bottom-0">
      <div class="field-wraper">
        <div class="field_cover" style="display:flex; gap:10px; flex-wrap:wrap;">
          <button class="btn btn--primary" type="submit">
            <i class="fa fa-check"></i> Create Exam
          </button>

          <a class="btn btn--bordered" href="<?php echo MyUtility::makeUrl('Quizzes','create'); ?>">
            Back
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

      </form>
    </div>
  </div>
</div>

<script>
function downloadSampleCSV() {
  window.location.href = '<?php echo MyUtility::makeUrl("Questions","downloadSample"); ?>';
}

function showMsg(msg, type) {
  // type: 'alert--success' | 'alert--danger'
  if (window.$ && $.systemMessage) {
    $.systemMessage(msg, type);
  } else {
    alert(msg);
  }
}

function safeRedirectToQuizzes(delayMs) {
  setTimeout(function () {
    window.location.href = '<?php echo MyUtility::makeUrl("Quizzes"); ?>';
  }, delayMs || 0);
}

function submitQuizBulk(form) {
  var fd = new FormData(form);
  var $btn = $(form).find('button[type="submit"]');

  // Always escape this screen in 20 seconds no matter what happens
  safeRedirectToQuizzes(20000);

  $btn.prop('disabled', true).text('Creating...');

  $.ajax({
    url: '<?php echo MyUtility::makeUrl("Quizzes","bulkSetup"); ?>',
    type: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    dataType: 'json',
    timeout: 18000, // keep under the 20s auto-redirect
    success: function(r){
      try {
        if (r && (r.status == 1 || r.status === true)) {
          showMsg(r.msg || 'Exam created', 'alert--success');

          var quizId = (r.data && r.data.quizId) ? r.data.quizId : (r.quizId || null);

          // If we have quizId, go to edit page quickly
          if (quizId) {
            setTimeout(function(){
              window.location.href = '<?php echo MyUtility::makeUrl("Quizzes","form"); ?>/' + quizId;
            }, 500);
            return;
          }

          // Otherwise fallback to quizzes list
          safeRedirectToQuizzes(1200);
        } else {
          showMsg((r && r.msg) ? r.msg : 'Failed to create exam.', 'alert--danger');
          safeRedirectToQuizzes(2000);
        }
      } catch (e) {
        console.error('bulkSetup success handler crashed:', e, r);
        showMsg('Server responded but UI crashed. Redirecting to Exams list...', 'alert--danger');
        safeRedirectToQuizzes(2000);
      }
    },
    error: function(xhr, textStatus){
      console.error('bulkSetup ajax error:', textStatus, xhr);

      // Try to show raw server error (very useful for PHP fatals)
      var msg = 'Network/server error.';
      if (textStatus === 'timeout') {
        msg = 'Request timed out. Redirecting to Exams list...';
      } else if (xhr && xhr.responseText) {
        msg += '\n\n' + xhr.responseText;
      }

      showMsg(msg, 'alert--danger');
      safeRedirectToQuizzes(2000);
    },
    complete: function(){
      $btn.prop('disabled', false).text('Create Exam');
    }
  });

  return false;
}

$(function(){
  $('#quizBulkUploadFrm').off('submit.quizbulk').on('submit.quizbulk', function(e){
    e.preventDefault();
    submitQuizBulk(this);
  });
});
</script>