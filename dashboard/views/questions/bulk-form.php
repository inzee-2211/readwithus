<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="facebox-panel">
  <div class="facebox-panel__head"><h4>Bulk Upload Questions (CSV)</h4></div>
  <div class="facebox-panel__body">
    <form class="form" id="bulkUploadFrm" ;>
      <div class="row">
        <div class="col-md-8">
          <div class="field-set">
            <label class="field_label">CSV File <span class="spn_must_field">*</span></label>
            <div class="field-wraper"><div class="field_cover">
              <input type="file" name="bulk_csv" accept=".csv" required>
              <small>CSV UTF-8. We accept either header style below.</small>
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
            <label class="field_label">CSV Columns</label>
            <div class="field-wraper"><div class="field_cover">
<pre style="white-space:pre-wrap;margin:0;">
Option A (friendly):
- title, type(1 single,2 multiple,3 text), marks, category_id, subcategory_id
- description, math_equation, hint, option_1..option_4, correct_answers, image

Option B (DB-like):
- question_title, question_type, question_marks, question_cat, question_subcat
- question_desc, question_math_equation, question_hint, question_option_1..4, question_answers, image_filename
</pre>
            </div></div>
          </div>

          <div class="field-set">
            <a href="javascript:void(0);" class="btn btn--primary" onclick="downloadSampleCSV();">Download Sample CSV</a>
          </div>
        </div>

        <div class="col-md-4">
          <div class="box box--white" style="padding:12px;">
            <h6>Example Row</h6>
<pre style="white-space:pre-wrap;margin:0;">
title,type,marks,category_id,subcategory_id,description,math_equation,hint,option_1,option_2,option_3,option_4,correct_answers,image
"Area of circle?",1,2,3,7,"Find area","\pi r^2","Use π≈3.14","πr^2","2πr","r^2","π/2","1","circle.png"
</pre>
          </div>
        </div>
      </div>

      <div class="row form-action-sticky">
        <div class="col-sm-12">
          <div class="field-set margin-bottom-0">
            <div class="field-wraper"><div class="field_cover">
              <button class="btn bg-primary" type="submit">Upload</button>
              <a class="btn btn--bordered" href="javascript:$.facebox.close();">Cancel</a>
            </div></div>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
function submitBulkUpload(form) {
  var fd = new FormData(form);
  var $btn = $(form).find('button[type="submit"]');
  $btn.prop('disabled', true).text('Uploading...');

  $.ajax({
    url: '<?php echo MyUtility::makeUrl("Questions","bulkSetup"); ?>',
    type: 'POST',
    data: fd,
    contentType: false,
    processData: false,
    success: function(resp, status, xhr) {
      var ct = (xhr.getResponseHeader('Content-Type') || '').toLowerCase();
      var r = null;
      if (ct.indexOf('application/json') !== -1) {
        try { r = (typeof resp === 'string') ? JSON.parse(resp) : resp; } catch(e) {}
      }
      if (!r) { try { r = JSON.parse(resp); } catch(e) { r = {status:0, msg:String(resp)}; } }

      if (r.status == 1 || r.status === true) {
        // Close FIRST (some sites error on $.systemMessage and never reach close)
        if ($.facebox && $.facebox.close) { $.facebox.close(); }
        else { $(document).trigger('close.facebox'); }

        // Small delay to allow DOM cleanup, then message + refresh
        setTimeout(function() {
          if (window.$ && $.systemMessage) {
            $.systemMessage(r.msg || 'Uploaded', 'alert--success');
          }
          if (typeof search === 'function' && document.frmClassSearch) {
            search(document.frmClassSearch);
          } else {
            location.reload();
          }
        }, 50);
      } else {
        if (window.$ && $.systemMessage) {
          $.systemMessage(r.msg || 'Import failed.', 'alert--danger');
        } else {
          alert(r.msg || 'Import failed.');
        }
      }
    },
    error: function(xhr) {
      console.error('bulkSetup error:', xhr);
      if (window.$ && $.systemMessage) {
        $.systemMessage('Network/server error.', 'alert--danger');
      } else {
        alert('Network/server error.');
      }
    },
    complete: function() {
      $btn.prop('disabled', false).text('Upload');
    }
  });
  return false; // block default submit
}

// hard-bind submit to avoid global handlers interfering
$(function(){
  $('#bulkUploadFrm').off('submit.bulk').on('submit.bulk', function(e){
    e.preventDefault();
    submitBulkUpload(this);
  });
});
function downloadSampleCSV() {
  window.location.href = '<?php echo MyUtility::makeUrl("Questions","downloadSample"); ?>';
}
</script>
