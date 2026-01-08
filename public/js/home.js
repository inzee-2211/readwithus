console.log('[home.js] loaded', {
  time: new Date().toISOString(),
  hasBootstrap: !!(window.bootstrap && window.bootstrap.Modal),
  RWU_CONFIG: window.RWU_CONFIG
});

(function () {
  // --- read PHP-provided config ---
  const baseUrlRaw = (window.RWU_CONFIG && window.RWU_CONFIG.baseUrl) || '/';
  const baseUrl = baseUrlRaw.endsWith('/') ? baseUrlRaw : baseUrlRaw + '/';

  let steps = [
    { title: "Select Level",   options: [], url: "api.php?url=getCourses",   paramKey: null },
    { title: "Select Subject", options: [], url: "api.php?url=getSubjects",  paramKey: "levelId" }
  ];

  const selectedValues = { levelId:null, subjectId:null, examboardId:null, tierId:null, yearId:null };
  const selectedSteps = [];
  let currentStep = 0;

  function onReady(fn){ document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }

  onReady(() => {
    // hook DOM only after it exists
    const modalEl   = document.getElementById('reviseTopicModal');
  const openBtn   = document.getElementById('openSelector');
   // pick the one INSIDE the modal body:
   const container = modalEl ? modalEl.querySelector('#dropDownOptions') : null;

    if (!modalEl || !openBtn || !container) return; // safe guard if section not on this page

    const reviseModal = new bootstrap.Modal(modalEl);

    function fetchOptionsForStepWithParam(stepIndex, params) {
      return new Promise((resolve) => {
        const step = steps[stepIndex];
        if (!step || !step.url) { resolve(); return; }

        const rel = step.url.replace(/^\//,'');
        const url = new URL(rel, baseUrl);

        if (step.paramKey && params[step.paramKey]) {
          url.searchParams.append(step.paramKey, params[step.paramKey]);
        }
        if (rel.includes('getExamboards')) {
          if (params.levelId) url.searchParams.append('levelId', params.levelId);
        }
        if (rel.includes('getYears')) {
          if (params.levelId)     url.searchParams.append('levelId', params.levelId);
          if (params.subjectId)   url.searchParams.append('subjectId', params.subjectId);
          if (params.examboardId) url.searchParams.append('examboardId', params.examboardId);
          if (params.tierId)      url.searchParams.append('tierId', params.tierId);
        }

        fetch(url.toString())
          .then(res => res.json())
          .then(json => {
            step.options = (json.status === 1 && Array.isArray(json.data))
              ? json.data.map(item => ({ id: item.id, name: item.name }))
              : [{ id: "error", name: "Error loading options" }];
            resolve();
          })
          .catch(() => { step.options = [{ id: "error", name: "Error loading options" }]; resolve(); });
      });
    }

    function buildQueryString(params) {
      const keys = Object.keys(params)
        .filter(k => params[k] && params[k] !== "error")
        .map(k => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`);
      return keys.length ? "&" + keys.join("&") : "";
    }

    function renderStep(stepIndex) {
      const step = steps[stepIndex];
      container.innerHTML = '';

      const headerRow = document.createElement('div');
      headerRow.style.display = "flex";
      headerRow.style.alignItems = "center";
      headerRow.style.justifyContent = "center";
      headerRow.style.position = "relative";
      headerRow.style.margin = "6px 0 4px";

      const breadcrumb = document.createElement('div');
      breadcrumb.className = 'breadcrumb-nav';
      breadcrumb.style.display = 'flex';
      breadcrumb.style.gap = '8px';
      breadcrumb.style.fontSize = '14px';
      breadcrumb.style.cursor = 'pointer';

      selectedSteps.forEach((sel, idx) => {
        if (!sel) return;
        const crumb = document.createElement('span');
        crumb.textContent = sel.name;
        crumb.style.color = idx === stepIndex ? '#0A033C' : '#1D9CFD';
        crumb.style.fontWeight = idx === stepIndex ? '700' : '600';
        crumb.onclick = () => {
          currentStep = idx;
          selectedSteps.splice(idx + 1);
          if (idx < 1) { selectedValues.subjectId = selectedValues.examboardId = selectedValues.tierId = selectedValues.yearId = null; }
          else if (idx < 2) { selectedValues.examboardId = selectedValues.tierId = selectedValues.yearId = null; }
          else if (idx < 3) { selectedValues.tierId = selectedValues.yearId = null; }
          renderStep(currentStep);
        };
        breadcrumb.appendChild(crumb);
        if (idx < stepIndex) {
          const arrow = document.createElement('span'); arrow.textContent = '›'; breadcrumb.appendChild(arrow);
        }
      });

      headerRow.appendChild(breadcrumb);
      container.appendChild(headerRow);

      const heading = document.createElement('h5');
      heading.className = 'ft-gothic text-center';
      heading.style.margin = '8px 0 14px';
      heading.textContent = step.title;
      container.appendChild(heading);

      step.options.forEach(opt => {
        const div = document.createElement('div');
        div.className = 'loop-wrap-btn';
        const btn = document.createElement('button');
        btn.className = 'ft-gothic';
        const displayName = opt.name;
        const optId = opt.id;
      btn.innerHTML = `<span>${displayName}</span>
 <img src="${baseUrl}assets/img/right-arrow.svg" alt="Arrow" class="arrow">`;

        // btn.onclick = () => {
        //   selectedSteps[stepIndex] = { id: optId, name: displayName };

        //   if (step.title === "Select Level") {
        //     selectedValues.levelId = optId;
        //     if (displayName === "GCSE") {
        //       steps = [
        //         { title: "Select Level",   options: [], url: "api.php?url=getCourses",    paramKey: null },
        //         { title: "Select Subject", options: [], url: "api.php?url=getSubjects",   paramKey: "levelId" },
        //         { title: "Select Examboard", options: [], url: "api.php?url=getExamboards", paramKey: "subjectId" },
        //         { title: "Select Tier",    options: [], url: "api.php?url=getTiers",      paramKey: "examboardId" },
        //         { title: "Select Year",    options: [], url: "api.php?url=getYears",      paramKey: "subjectId" },
        //       ];
        //     } else {
        //       steps = [
        //         { title: "Select Level",   options: [], url: "api.php?url=getCourses",   paramKey: null },
        //         { title: "Select Subject", options: [], url: "api.php?url=getSubjects",  paramKey: "levelId" },
        //         { title: "Select Year",    options: [], url: "api.php?url=getYears",     paramKey: "subjectId" },
        //       ];
        //     }
        //   } else if (step.title === "Select Subject")   { selectedValues.subjectId   = optId; }
        //   else if (step.title === "Select Examboard")   { selectedValues.examboardId = optId; }
        //   else if (step.title === "Select Tier")        { selectedValues.tierId      = optId; }
        //   else if (step.title === "Select Year")        { selectedValues.yearId      = optId; }

        //   currentStep++;
        //   if (currentStep < steps.length) {
        //     fetchOptionsForStepWithParam(currentStep, selectedValues).then(() => renderStep(currentStep));
        //   } else {
        //     fetch("api.php?url=resolveSetup" + buildQueryString(selectedValues))
        //       .then(r => r.json())
        //       .then(j => {
        //         if (j.status === 1 && j.data?.setup_ids) {
        btn.onclick = () => {
  selectedSteps[stepIndex] = { id: optId, name: displayName };

  // --- STEP: Level selection ---
  if (step.title === "Select Level") {
    // Reset all downstream selections when level changes
    selectedValues.levelId      = optId;
    selectedValues.subjectId    = null;
    selectedValues.examboardId  = null;
    selectedValues.tierId       = null;
    selectedValues.yearId       = null;

    // GCSE: NO YEARS
    if (displayName.trim().toUpperCase() === "GCSE") {
      steps = [
        { title: "Select Level",     options: [], url: "api.php?url=getCourses",     paramKey: null },
        { title: "Select Subject",   options: [], url: "api.php?url=getSubjects",    paramKey: "levelId" },
        { title: "Select Examboard", options: [], url: "api.php?url=getExamboards", paramKey: "subjectId" },
        { title: "Select Tier",      options: [], url: "api.php?url=getTiers",      paramKey: "examboardId" }
        // ✅ no Year step here
      ];
    } else {
      // Non-GCSE: has Year
      steps = [
        { title: "Select Level",   options: [], url: "api.php?url=getCourses",   paramKey: null },
        { title: "Select Subject", options: [], url: "api.php?url=getSubjects",  paramKey: "levelId" },
        { title: "Select Year",    options: [], url: "api.php?url=getYears",     paramKey: "subjectId" }
      ];
    }

  // --- STEP: Subject selection ---
  } else if (step.title === "Select Subject") {
    selectedValues.subjectId = optId;

  // --- STEP: Examboard selection (GCSE only) ---
  } else if (step.title === "Select Examboard") {
    selectedValues.examboardId = optId;

  // --- STEP: Tier selection (GCSE only) ---
  } else if (step.title === "Select Tier") {
    selectedValues.tierId = optId;

  // --- STEP: Year selection (non-GCSE only) ---
  } else if (step.title === "Select Year") {
    selectedValues.yearId = optId;
  }

  // Move to next step or resolve setup
  currentStep++;
  if (currentStep < steps.length) {
    fetchOptionsForStepWithParam(currentStep, selectedValues)
      .then(() => renderStep(currentStep));
  } else {
    // Final step → resolve quiz setup and redirect
  const quizizzBase = (window.RWU_CONFIG && window.RWU_CONFIG.quizizzUrl) || null;

// ✅ make resolveSetup absolute (so it works from /free-quizzes/* too)
const resolveUrl = new URL("api.php?url=resolveSetup", baseUrl).toString();

fetch(resolveUrl + buildQueryString(selectedValues))
  .then(r => r.json())
  .then(j => {
    if (j.status === 1 && j.data?.setup_ids) {
      const ids = j.data.setup_ids.join(',');

      // ✅ prefer front URL if provided, else fallback to old behavior (home page)
      const nextUrl = quizizzBase
        ? (quizizzBase + '?setup_ids=' + ids)
        : (fcom.makeUrl('quizizz') + '?setup_ids=' + ids);

      reviseModal.hide();
      window.location.href = nextUrl;
    } else {
      alert("Unable to load quiz. Please try again.");
    }
  })
  .catch(() => alert("Network error. Please try again."));

  }
};

//     const ids = j.data.setup_ids.join(',');
//     const nextUrl = fcom.makeUrl('quizizz') + '?setup_ids=' + ids;
//     reviseModal.hide();
//     window.location.href = nextUrl;
// }
//  else {
//                   alert("Unable to load quiz. Please try again.");
//                 }
//               })
//               .catch(() => alert("Network error. Please try again."));
//           }
//         };

        div.appendChild(btn);
        container.appendChild(div);
      });
    }

    // open button → show modal + load first step
    openBtn.addEventListener('click', () => {
      if (steps[0].url && steps[0].options.length === 0) {
        fetchOptionsForStepWithParam(0, selectedValues).then(() => {
          renderStep(0);
          reviseModal.show();
        });
      } else {
        renderStep(currentStep || 0);
        reviseModal.show();
      }
    });

    // reset state when modal closes
    modalEl.addEventListener('hidden.bs.modal', () => {
      container.innerHTML = '';
      currentStep = 0;
      selectedSteps.length = 0;
      selectedValues.levelId = selectedValues.subjectId = selectedValues.examboardId = selectedValues.tierId = selectedValues.yearId = null;
    });
  });
})();
