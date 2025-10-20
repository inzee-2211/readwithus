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
document.addEventListener('DOMContentLoaded', function () {
  const subj  = document.getElementById('subject_id');
  const topic = document.getElementById('quiz_setup_id');
  if (!subj || !topic) return;

  function resetTopics(label) {
    topic.innerHTML = '<option value="">' + (label || 'Select Topic') + '</option>';
  }

  async function loadTopics(subjectId) {
    resetTopics('Loading...');
    const fd = new FormData();
    fd.append('subject_id', subjectId);

    try {
      const res = await fetch('<?= MyUtility::makeUrl('Coursemanagement', 'topicsBySubject'); ?>', {
        method: 'POST',
        body: fd,
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      });

      let json;
      try { json = await res.json(); } catch (e) {
        console.error('topicsBySubject returned non-JSON', await res.text());
        resetTopics('No topics found');
        return;
      }

      console.log('topicsBySubject response:', json);

      const data = (json && (json.data || json)) || {};
      const ids = Object.keys(data);

      resetTopics(ids.length ? 'Select Topic' : 'No topics for this subject');

      ids.forEach(id => {
        const opt = document.createElement('option');
        opt.value = id;
        opt.textContent = data[id];
        topic.appendChild(opt);
      });
    } catch (err) {
      console.error('topicsBySubject fetch failed:', err);
      resetTopics('No topics found');
    }
  }

  subj.addEventListener('change', () => {
    const sid = subj.value ? String(subj.value).trim() : '';
    console.log('Subject changed:', sid);
    if (!sid) { resetTopics('Select Topic'); return; }
    loadTopics(sid);
  });

  // If subject is preselected (edit flow), auto-load topics
  if (subj.value) {
    loadTopics(subj.value);
  }
});
</script>
