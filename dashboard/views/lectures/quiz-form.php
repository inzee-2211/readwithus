<?php defined('SYSTEM_INIT') or die('Invalid Usage.');
$LEC_ID = (int)$lecture['lecture_id'];
?>
<div class="card-controls">
  <?php
  $this->includeTemplate('lectures/navigation.php', [
      'active'    => 'quizzes',
      'lectureId' => $lecture['lecture_id'],
      'sectionId' => $lecture['lecture_section_id'],
  ]);
  ?>
</div>

<div class="card-controls-content">
  <div class="card-controls-view controls-tabs-view-js">
    <div class="step-small-form">
      <div class="row">
        <div class="col-md-12">
          <div class="box box--white padding-4">
            <h5 class="margin-bottom-3"><?php echo Label::getLabel('LBL_ATTACH_QUIZ_TO_THIS_LECTURE'); ?></h5>

            <div class="d-flex gap-2 margin-bottom-3">
              <button id="openSelector<?= $LEC_ID ?>" type="button" class="btn btn--secondary">
                <?php echo Label::getLabel('LBL_CHOOSE_QUIZ'); ?>
              </button>

              <?php if (!empty($quizRes)) { ?>
                <div class="badge badge--success">
                  <?php
                    $summary = [];
                    if (!empty($quizRes['lecsrc_link'])) {
                        $summary[] = 'URL: ' . htmlspecialchars($quizRes['lecsrc_link']);
                    }
                    if (!empty($quizRes['lecsrc_meta'])) {
                        $meta = json_decode($quizRes['lecsrc_meta'], true);
                        if (is_array($meta)) {
                            $map = [
                                'levelId'     => 'Level',
                                'subjectId'   => 'Subject',
                                'examboardId' => 'Examboard',
                                'tierId'      => 'Tier',
                                'yearId'      => 'Year',
                                'topicId'     => 'Topic',
                                'subtopic'    => 'Subtopic',
                                'quizId'      => 'Quiz'
                            ];
                            foreach ($map as $k => $label) {
                                if (!empty($meta[$k])) { $summary[] = $label . ': ' . $meta[$k]; }
                            }
                        }
                    }
                    echo $summary ? implode(' • ', $summary) : Label::getLabel('LBL_QUIZ_SELECTED');
                  ?>
                </div>
              <?php } ?>
            </div>

            <div id="dropDownOptions<?= $LEC_ID ?>" style="display:none; position:relative; background:#fff; border:1px solid #ddd; border-radius:8px; padding:15px; margin-top:10px; z-index:1000; max-height:420px; overflow-y:auto;"></div>

            <form id="frmAttachQuiz<?= $LEC_ID ?>" onsubmit="return false;" class="form margin-top-4">
              <input type="hidden" name="lecture_id" value="<?php echo (int)$lecture['lecture_id']; ?>">
              <input type="hidden" name="course_id"  value="<?php echo (int)$lecture['lecture_course_id']; ?>">

              <input type="hidden" name="levelId"     value="">
              <input type="hidden" name="subjectId"   value="">
              <input type="hidden" name="examboardId" value="">
              <input type="hidden" name="tierId"      value="">
              <input type="hidden" name="yearId"      value="">
              <input type="hidden" name="topicId"     value="">
              <input type="hidden" name="quizId"      value="">
              <input type="hidden" name="subtopic"    value="">

              <div class="step-actions">
                <button type="button" class="btn btn--transparent " onclick="lectureMediaForm(<?php echo (int)$lecture['lecture_id'];?>)">
                  <?php echo Label::getLabel('LBL_CANCEL'); ?>
                </button>
                <button type="button" class="btn btn--primary" onclick="submitAttachQuiz<?= $LEC_ID ?>(document.getElementById('frmAttachQuiz<?= $LEC_ID ?>'))">
                  <?php echo Label::getLabel('LBL_SAVE'); ?>
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
(function () {
  const LEC_ID  = <?php echo $LEC_ID; ?>;
  const apiBase = "<?php echo rtrim(CONF_WEBROOT_FRONT_URL, '/'); ?>/";

  const container = document.getElementById('dropDownOptions' + LEC_ID);
  const openBtn   = document.getElementById('openSelector' + LEC_ID);
  const form      = document.getElementById('frmAttachQuiz' + LEC_ID);

  // Initial minimal steps; we branch after Level
  let steps = [
    { title: "Select Level",   options: [], url: "api.php?url=getCourses",   paramKey: null },
    { title: "Select Subject", options: [], url: "api.php?url=getSubjects",  paramKey: "levelId" }
  ];

  const selectedValues = {
    levelId: null, subjectId: null,
    examboardId: null, tierId: null, yearId: null,
    topicId: null, subtopicId: null
  };
  const selectedSteps  = [];
  let currentStep = 0;

  function fetchOptionsForStepWithParam(stepIndex, params) {
  return new Promise((resolve) => {
    const step = steps[stepIndex];
    if (!step || !step.url) { resolve(); return; }

    const rel = step.url.replace(/^\//, '');
    const url = new URL(rel, apiBase);

    // Support multiple param keys
    const keys = Array.isArray(step.paramKeys) ? step.paramKeys : (step.paramKey ? [step.paramKey] : []);
    keys.forEach(k => {
      if (!k) return;
      // special mapping: getSubtopics expects setupId (we store it in selectedValues.topicId)
      if (k === 'setupId') {
        if (params.topicId) url.searchParams.append('setupId', params.topicId);
      } else if (params[k] !== null && params[k] !== undefined && params[k] !== '') {
        url.searchParams.append(k, params[k]);
      }
    });

    fetch(url.toString())
      .then(res => { if (!res.ok) throw new Error('HTTP ' + res.status); return res.json(); })
      .then(json => {
        step.options = (json.status === 1 && Array.isArray(json.data))
                     ? json.data.map(i => ({ id: i.id, name: i.name }))
                     : [];
        resolve();
      })
      .catch((err) => { console.error('Fetch step error:', step.title, err); step.options = []; resolve(); });
  });
}


  function renderButtons(title, options, onSelect, emptyText = 'No options') {
    container.innerHTML = '';
    const h = document.createElement('h5');
    h.textContent = title;
    h.style.textAlign = 'center';
    h.style.marginBottom = '12px';
    container.appendChild(h);

    if (!options || options.length === 0) {
      const no = document.createElement('div');
      no.textContent = emptyText;
      no.style.cssText = 'text-align:center;color:#666;padding:20px;';
      container.appendChild(no);
    } else {
      options.forEach(opt => {
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'btn btn--secondary btn--block margin-bottom-2';
        btn.style.cssText = 'width:100%;text-align:left;padding:10px 14px;margin-bottom:8px;';
        btn.textContent = opt.name;
        btn.onclick = () => onSelect(opt);
        container.appendChild(btn);
      });
    }
    container.style.display = 'block';
  }

  function renderStep(stepIndex) {
    const step = steps[stepIndex];

    // Special handling: if we're at "Select Quiz" and no results, fall back to using subtopic as quiz
    if (step.title === 'Select Quiz' && (!step.options || step.options.length === 0)) {
      renderFinalSelection(); // fallback to subtopic-as-quiz
      return;
    }
    renderButtons(step.title, step.options, (opt) => onPick(stepIndex, opt));
  }

  function onPick(stepIndex, opt) {
    selectedSteps[stepIndex] = { id: opt.id, name: opt.name };
    const title = steps[stepIndex].title;

    if (title === 'Select Level') {
      selectedValues.levelId = opt.id;

      // Build complete flow depending on level name
      if (opt.name === 'GCSE') {
  steps = [
    { title:"Select Level",     options:[], url:"api.php?url=getCourses" },
    { title:"Select Subject",   options:[], url:"api.php?url=getSubjects",   paramKeys:["levelId"] },
    { title:"Select Examboard", options:[], url:"api.php?url=getExamboards", paramKeys:["subjectId","levelId"] },
    { title:"Select Tier",      options:[], url:"api.php?url=getTiers",      paramKeys:["examboardId"] },
    { title:"Select Year",      options:[], url:"api.php?url=getYears",      paramKeys:["subjectId","levelId","examboardId","tierId"] },

    // IMPORTANT: topics come from tbl_quiz_setup with the full path
    { title:"Select Topic",     options:[], url:"api.php?url=getTopics",     paramKeys:["levelId","subjectId","examboardId","tierId","yearId"] },

    // subtopics now come from tbl_quiz_management via setupId (= topicId)
    { title:"Select Subtopic",  options:[], url:"api.php?url=getSubtopics",  paramKeys:["setupId"] },
  ];
} else {
  steps = [
    { title:"Select Level",     options:[], url:"api.php?url=getCourses" },
    { title:"Select Subject",   options:[], url:"api.php?url=getSubjects",   paramKeys:["levelId"] },
    { title:"Select Year",      options:[], url:"api.php?url=getYears",      paramKeys:["subjectId","levelId"] },

    { title:"Select Topic",     options:[], url:"api.php?url=getTopics",     paramKeys:["levelId","subjectId","yearId"] },
    { title:"Select Subtopic",  options:[], url:"api.php?url=getSubtopics",  paramKeys:["setupId"] },
  ];
}


    } else if (title === 'Select Subject')   { selectedValues.subjectId   = opt.id; }
      else if (title === 'Select Examboard'){ selectedValues.examboardId = opt.id; }
      else if (title === 'Select Tier')     { selectedValues.tierId      = opt.id; }
      else if (title === 'Select Year')     { selectedValues.yearId      = opt.id; }
      else if (title === 'Select Topic')    { selectedValues.topicId     = opt.id; }
      else if (title === 'Select Subtopic') { selectedValues.subtopicId  = opt.id; }
      else if (title === 'Select Quiz')     {
        // User picked a specific quiz from the question bank
        finalizeWithQuiz(opt.id, opt.name);
        return;
      }

    currentStep++;
    if (currentStep < steps.length) {
      const params = {
        levelId:     selectedValues.levelId,
        subjectId:   selectedValues.subjectId,
        examboardId: selectedValues.examboardId,
        tierId:      selectedValues.tierId,
        yearId:      selectedValues.yearId,
        topicId:     selectedValues.topicId,
        subtopicId:  selectedValues.subtopicId
      };
      fetchOptionsForStepWithParam(currentStep, params).then(() => renderStep(currentStep));
    } else {
      // If no explicit quiz step, or quiz list empty, fall back to subtopic-as-quiz
      renderFinalSelection();
    }
  }

  // Used when API returns concrete quizzes (tbl_question_bank) for a subtopic
  function finalizeWithQuiz(realQuizId, quizName) {
    const pairs = {
      levelId:     selectedValues.levelId || '',
      subjectId:   selectedValues.subjectId || '',
      examboardId: selectedValues.examboardId || '',
      tierId:      selectedValues.tierId || '',
      yearId:      selectedValues.yearId || '',
      topicId:     selectedValues.topicId || '',
      // Save the REAL question bank id
      quizId:      realQuizId,
      // Still persist subtopic for context/URL compatibility
      subtopic:    selectedValues.subtopicId || ''
    };
    Object.entries(pairs).forEach(([k,v]) => {
      const el = form.querySelector(`input[name="${k}"]`);
      if (el) el.value = v;
    });

    container.innerHTML = `
      <div class="alert alert-success" style="padding:10px;border:1px solid #cde7cd;border-radius:6px;">
        Selected quiz: <strong>${quizName}</strong><br/>
        Click <em>Save</em> to attach it to this lecture.
      </div>`;
    container.style.display = 'block';
  }

  // Fallback: treat Subtopic as the quiz (legacy behaviour)
  function renderFinalSelection() {
    const picked = selectedSteps[selectedSteps.length - 1]; // subtopic
    const quizId = selectedValues.subtopicId;
    const quizNm = (picked && picked.name) ? picked.name : 'Selected subtopic';

    const pairs = {
      levelId:     selectedValues.levelId || '',
      subjectId:   selectedValues.subjectId || '',
      examboardId: selectedValues.examboardId || '',
      tierId:      selectedValues.tierId || '',
      yearId:      selectedValues.yearId || '',
      topicId:     selectedValues.topicId || '',
      quizId:      quizId,   // subtopic id used as quiz id
      subtopic:    quizId
    };
    Object.entries(pairs).forEach(([k,v]) => {
      const el = form.querySelector(`input[name="${k}"]`);
      if (el) el.value = v;
    });

    container.innerHTML = `
      <div class="alert alert-success" style="padding:10px;border:1px solid #cde7cd;border-radius:6px;">
        Selected quiz (subtopic): <strong>${quizNm}</strong><br/>
        Click <em>Save</em> to attach it to this lecture.
      </div>`;
    container.style.display = 'block';
  }

  // open/close
  openBtn.addEventListener('click', (e) => {
    e.stopPropagation();
    if (container.style.display === 'block') {
      container.style.display = 'none';
      currentStep = 0;
    } else {
      currentStep = 0;
      Object.keys(selectedValues).forEach(k => selectedValues[k] = null);
      fetchOptionsForStepWithParam(0, selectedValues).then(() => renderStep(0));
    }
  });

  // click outside to close
  document.addEventListener('click', (e) => {
    const inside = container.contains(e.target) || openBtn.contains(e.target);
    if (!inside) container.style.display = 'none';
  });

  // expose submit for this lecture
  window['submitAttachQuiz' + LEC_ID] = function (frm) {
    if (!frm) return;
    const subtopic = frm.querySelector('input[name="subtopic"]').value;
    const quizId   = frm.querySelector('input[name="quizId"]').value;

    if (!quizId) {
      alert('Please select a quiz first.');
      return;
    }
    fcom.updateWithAjax(
      fcom.makeUrl('Lectures', 'setupQuiz'),
      fcom.frmData($('#' + frm.id)),
      function (res) {
        if (res && res.status) {
          lectureQuizForm(res.lectureId);
        }
      }
    );
  };
})();
</script>
<style>
/* Panel wrapper (the dropdown with options) */
[id^="dropDownOptions"] {
  display: none;                 /* your JS toggles this */
  position: relative;
  background: #fff;
  border: 1px solid #e6eaef;
  border-radius: 10px;
  padding: 16px;
  margin-top: 12px;
  max-height: 460px;
  overflow: auto;
  box-shadow: 0 6px 18px rgba(16, 24, 40, 0.08);
  animation: panelIn .16s ease-out;
}

/* Title inside the panel */
[id^="dropDownOptions"] > h5 {
  text-align: center;
  margin: 0 0 14px;
  font-size: 16px;
  font-weight: 700;
  color: #344051;
}

/* Option buttons (keep original classes; just polish) */
[id^="dropDownOptions"] .btn.btn--secondary.btn--block {
  width: 100%;
  text-align: left;
  padding: 12px 14px;
  margin-bottom: 8px;
  border-radius: 8px;
  border: 1px solid #dfe4ea;
  background: #1aa3a9;           /* theme teal */
  color: #fff;
  box-shadow: 0 1px 0 rgba(16,24,40,0.02);
  transition: transform .06s ease, background-color .12s ease, box-shadow .12s ease;
}

/* Hover / focus visuals */
[id^="dropDownOptions"] .btn.btn--secondary.btn--block:hover {
  background: #178e93;
  box-shadow: 0 4px 10px rgba(26,163,169,0.18);
  transform: translateY(-1px);
}
[id^="dropDownOptions"] .btn.btn--secondary.btn--block:focus-visible {
  outline: 2px solid #1aa3a9;
  outline-offset: 2px;
}

/* “Selected” look for the red item you show (keeps red, but polishes) */
[id^="dropDownOptions"] .btn.btn--secondary.btn--block[style*="background: red"],
[id^="dropDownOptions"] .btn.btn--secondary.btn--block.active,
[id^="dropDownOptions"] .btn.btn--secondary.btn--block.is-active {
  background: #e44b3a !important;   /* refined red */
  border-color: #e44b3a !important;
  box-shadow: 0 4px 10px rgba(228,75,58,0.22);
}

/* Empty-state text */
[id^="dropDownOptions"] .no-options,
[id^="dropDownOptions"] .empty {
  text-align: center;
  color: #6b7a90;
  padding: 22px 8px;
}

/* Nice thin scrollbar for long lists */
[id^="dropDownOptions"]::-webkit-scrollbar { width: 10px; }
[id^="dropDownOptions"]::-webkit-scrollbar-track { background: #f3f5f7; border-radius: 8px; }
[id^="dropDownOptions"]::-webkit-scrollbar-thumb { background: #d4d9e1; border-radius: 8px; }
[id^="dropDownOptions"]::-webkit-scrollbar-thumb:hover { background: #bfc6d1; }

/* Success badge summary (right of "Choose Quiz") */
.badge.badge--success {
  background: #e8f6ee;
  color: #156845;
  border: 1px solid #cfead9;
  border-radius: 20px;
  padding: 6px 10px;
  font-weight: 600;
  line-height: 1.2;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  max-width: 100%;
}

/* Align the action buttons at the bottom (Cancel/Save) neatly */
.step-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 16px;
}
.step-actions .btn--transparent {
  background: #fff;
  color: black;
 
  
}
.step-actions .btn--transparent:hover {
  background: #f7f8fa;
  border-color: #cfd6dc;
  color: #344051;
}

/* Subtle panel entrance */
@keyframes panelIn {
  from { opacity: 0; transform: translateY(4px); }
  to   { opacity: 1; transform: translateY(0); }
}

</style>