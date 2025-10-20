<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$frm->developerTags['colClassPrefix'] = 'col-md-';
$frm->developerTags['fld_default_col'] = 12;
$frm->setFormTagAttribute('class', 'web_form form_horizontal');
$frm->setFormTagAttribute('onsubmit', 'setup(this); return(false);');
?>
<style>
    .rightalign {
    display: flex;
    justify-content: flex-end; /* Aligns the button to the right */
    margin-right: 100px;
}
/* CSS to hide the new sub-topic field by default */
.hidden {
    display: none;
}


    </style>
<section class="section">


 

    <div class="sectionhead d-flex justify-content-between align-items-center">
   
    <h4 class="mb-0"><?php echo Label::getLabel('LBL_ADD_QUIZ'); ?></h4>
 
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
                    <!-- <ul class="tabs_nav">
                        <li><a class="active" href="javascript:void(0);"><?php echo Label::getLabel('LBL_GENERAL'); ?></a></li>
                         <?php
                        $inactive = ($categoryId == 0) ? 'fat-inactive' : '';
                        foreach ($languages as $langId => $langName) {
                        ?>
                            <li class=" lang-li-js <?php echo $inactive; ?>">
                                <a href="javascript:void(0);" data-id="<?php echo $langId; ?>" <?php if ($categoryId > 0) { ?> onclick="langForm(<?php echo $categoryId; ?>, <?php echo $langId; ?>);" <?php } ?>>
                                    <?php echo $langName; ?>
                                </a>
                            </li>
                        <?php } ?>  
                    </ul> -->
                    <div class="tabs_panel_wrap">
                        <div class="tabs_panel">
                          <?php echo $frm->getFormHtml(); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
(function () {
  // Event delegation works even if the form is injected later
  document.addEventListener('change', function (e) {
    const t = e.target;
    if (!t) return;

    // Match the Subject select by id or name
    const isSubject =
      t.id === 'subject_id' || t.name === 'subject_id';

    if (!isSubject) return;

    const sid = (t.value || '').trim();
    // Find the Topic select reliably
    const topic = document.querySelector('#quiz_setup_id,[name="quiz_setup_id"]');
    if (!topic) { console.error('Topic <select> not found'); return; }

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
      const text = await res.text();          // log raw for debugging
      console.log('topicsBySubject raw:', text);
      try { return JSON.parse(text); } catch { return null; }
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

      // If using Select2 / Bootstrap-Select, refresh it
      if (window.jQuery && jQuery(topic).data('select2')) jQuery(topic).trigger('change.select2');
      if (window.jQuery && jQuery.fn.selectpicker) jQuery(topic).selectpicker('refresh');
    })
    .catch(err => {
      console.error('topicsBySubject failed:', err);
      topic.innerHTML = '<option value="">No topics found</option>';
    });
  });

  // If subject is preselected, load topics once
  const subj = document.querySelector('#subject_id,[name="subject_id"]');
  if (subj && subj.value) {
    const evt = new Event('change', { bubbles: true });
    subj.dispatchEvent(evt);
  }
})();
</script>
