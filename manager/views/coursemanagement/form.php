<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
$frm->setFormTagAttribute('enctype', 'multipart/form-data'); // ✅ add this

?>
<style>
    .rightalign {
        display: flex;
        justify-content: flex-end;
        margin-right: 100px;
    }
    .hidden {
        display: none;
    }
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
                            $existingPdf = $frm->getField('pdf_path')->value;
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
    // Store the initial topic ID for edit mode
    const initialTopicId = <?php echo json_encode($data['quiz_setup_id'] ?? 0); ?>;
    
    // Event delegation for subject change
    document.addEventListener('change', function (e) {
        const t = e.target;
        if (!t) return;

        // Match the Subject select by id or name
        const isSubject = t.id === 'subject_id' || t.name === 'subject_id';

        if (!isSubject) return;

        const sid = (t.value || '').trim();
        // Find the Topic select reliably
        const topic = document.querySelector('#quiz_setup_id,[name="quiz_setup_id"]');
        if (!topic) { 
            console.error('Topic <select> not found'); 
            return; 
        }

        if (!sid) {
            topic.innerHTML = '<option value="">Select Topic</option>';
            return;
        }

        // Show loading and call endpoint
        topic.innerHTML = '<option value="">Loading...</option>';

        const fd = new FormData();
        fd.append('subject_id', sid);

        fetch('<?= MyUtility::makeUrl('Coursemanagement','topicsBySubject'); ?>', {
            method: 'POST',
            body: fd,
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(async res => {
            const text = await res.text();
            console.log('topicsBySubject raw:', text);
            try { 
                return JSON.parse(text); 
            } catch(e) { 
                console.error('JSON parse error:', e);
                return null; 
            }
        })
        .then(json => {
            const map = (json && json.data) || {};
            const ids = Object.keys(map);

            topic.innerHTML = '<option value="">' + (ids.length ? 'Select Topic' : 'No topics for this subject') + '</option>';

            const frag = document.createDocumentFragment();
            ids.forEach(id => {
                const opt = document.createElement('option');
                opt.value = id;
                opt.textContent = map[id];
                frag.appendChild(opt);
            });
            topic.appendChild(frag);

            // In edit mode, try to select the initial topic
            if (initialTopicId > 0) {
                setTimeout(() => {
                    const optionToSelect = topic.querySelector('option[value="' + initialTopicId + '"]');
                    if (optionToSelect) {
                        topic.value = initialTopicId;
                        console.log('Auto-selected topic:', initialTopicId);
                    } else {
                        console.warn('Initial topic not found in options:', initialTopicId);
                    }
                    
                    // Refresh any select plugins
                    if (window.jQuery && jQuery(topic).data('select2')) {
                        jQuery(topic).trigger('change.select2');
                    }
                    if (window.jQuery && jQuery.fn.selectpicker) {
                        jQuery(topic).selectpicker('refresh');
                    }
                }, 100);
            }

            // Refresh any select plugins
            if (window.jQuery && jQuery(topic).data('select2')) {
                jQuery(topic).trigger('change.select2');
            }
            if (window.jQuery && jQuery.fn.selectpicker) {
                jQuery(topic).selectpicker('refresh');
            }
        })
        .catch(err => {
            console.error('topicsBySubject failed:', err);
            topic.innerHTML = '<option value="">Error loading topics</option>';
        });
    });

    // Auto-trigger subject change if subject is pre-selected (for edit mode)
    const subj = document.querySelector('#subject_id,[name="subject_id"]');
    if (subj && subj.value) {
        console.log('Auto-loading topics for subject:', subj.value);
        const evt = new Event('change', { bubbles: true });
        subj.dispatchEvent(evt);
    } else {
        // If no subject selected, ensure topic dropdown is reset
        const topic = document.querySelector('#quiz_setup_id,[name="quiz_setup_id"]');
        if (topic) {
            topic.innerHTML = '<option value="">Select Topic</option>';
        }
    }
})();

// Form submission handler
function setup(frm) {
  const submitBtn = frm.querySelector('button[type="submit"]');
  const originalText = submitBtn ? submitBtn.innerHTML : '';
  if (submitBtn) { submitBtn.innerHTML = 'Saving...'; submitBtn.disabled = true; }

  var fd = new FormData(frm);

  $.ajax({
    url: '<?= MyUtility::makeUrl('Coursemanagement','setup'); ?>',   // ⬅️ no fcom
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

  return false; // extra safety if this is ever called directly
}


</script>