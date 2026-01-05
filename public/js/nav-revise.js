// public/js/nav-revise.js
// Disable on mobile/tablet
if (window.matchMedia("(max-width: 991.98px)").matches) {
  const dd = document.getElementById("dropDownOptionNav");
  const btn = document.getElementById("openSelectorNav");
  if (dd) { dd.style.display = "none"; dd.style.pointerEvents = "none"; }
  if (btn) { btn.style.display = "none"; btn.style.pointerEvents = "none"; }
  // ✅ Do nothing else on mobile
} else {
  


(function () {
  // harden against globals
  const RWU = window.RWU_CONFIG || {};
  const baseUrlRaw = RWU.baseUrl || '/';
  const baseUrl = baseUrlRaw.endsWith('/') ? baseUrlRaw : baseUrlRaw + '/';

  // --- initial steps (level → subject). More are injected dynamically based on level
  let steps = [
    { title: "Select Level",   options: [], url: "api.php?url=getCourses",   paramKey: null },
    { title: "Select Subject", options: [], url: "api.php?url=getSubjects",  paramKey: "levelId" }
  ];

  // state
  const selectedValues = { levelId:null, subjectId:null, examboardId:null, tierId:null, yearId:null };
  const selectedSteps  = [];
  let currentStep = 0;
  let loadedOnce  = false;

  // ---- tiny helpers
  const qs = (sel, root=document) => root.querySelector(sel);
  function onReady(fn){ document.readyState !== 'loading' ? fn() : document.addEventListener('DOMContentLoaded', fn); }

  function buildQueryString(params) {
    const parts = Object.keys(params)
      .filter(k => params[k] && params[k] !== "error")
      .map(k => `${encodeURIComponent(k)}=${encodeURIComponent(params[k])}`);
    return parts.length ? "&" + parts.join("&") : "";
  }

  function makeAbs(relative) {
    const rel = (relative || '').replace(/^\//,'');
    return new URL(rel, baseUrl).toString();
  }

  // ---- FETCH OPTIONS (aligned with home.js)
  function fetchOptionsForStepWithParam(stepIndex, params) {
    return new Promise((resolve) => {
      const step = steps[stepIndex];
      if (!step || !step.url) { resolve(); return; }

      const rel = step.url.replace(/^\//,'');
      const url = new URL(rel, baseUrl);

      if (step.paramKey && params[step.paramKey]) {
        url.searchParams.append(step.paramKey, params[step.paramKey]);
      }

      // extra params for deeper steps (same logic as hero)
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

  // ---- rendering the inline dropdown content (no modal)
  function renderStep(container, stepIndex) {
    const step = steps[stepIndex];
    container.innerHTML = '';

    // breadcrumb row
    const headerRow = document.createElement('div');
    headerRow.style.display = "flex";
    headerRow.style.alignItems = "center";
    headerRow.style.justifyContent = "space-between";
    headerRow.style.gap = '10px';
    headerRow.style.margin = "6px 0 8px";

    const breadcrumb = document.createElement('div');
    breadcrumb.style.display = 'flex';
    breadcrumb.style.gap = '8px';
    breadcrumb.style.fontSize = '13px';
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
        if (idx < 1) {
          selectedValues.subjectId   =
          selectedValues.examboardId =
          selectedValues.tierId      =
          selectedValues.yearId      = null;
        } else if (idx < 2) {
          selectedValues.examboardId =
          selectedValues.tierId      =
          selectedValues.yearId      = null;
        } else if (idx < 3) {
          selectedValues.tierId = selectedValues.yearId = null;
        }
        renderStep(container, currentStep);
      };
      breadcrumb.appendChild(crumb);
      if (idx < stepIndex) {
        const arrow = document.createElement('span');
        arrow.textContent = '›';
        breadcrumb.appendChild(arrow);
      }
    });

    const title = document.createElement('div');
    title.textContent = step.title;
    title.style.fontWeight = '700';
    title.style.fontSize = '14px';

    headerRow.appendChild(breadcrumb);
    headerRow.appendChild(title);
    container.appendChild(headerRow);

    // options
    const list = document.createElement('div');
    list.style.display = 'grid';
    list.style.gridTemplateColumns = '1fr';
    list.style.gap = '6px';

    step.options.forEach(opt => {
      const btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'rtm-item';
      btn.textContent = opt.name;

      btn.onclick = () => {
        selectedSteps[stepIndex] = { id: opt.id, name: opt.name };

        // 🔵 GCSE vs non-GCSE branching
        if (step.title === "Select Level") {
          selectedValues.levelId = opt.id;
          selectedValues.yearId  = null; // clear any stale year

          if (opt.name === "GCSE") {
            // GCSE: NO Year step
            steps = [
              { title: "Select Level",     options: [], url: "api.php?url=getCourses",    paramKey: null },
              { title: "Select Subject",   options: [], url: "api.php?url=getSubjects",   paramKey: "levelId" },
              { title: "Select Examboard", options: [], url: "api.php?url=getExamboards", paramKey: "subjectId" },
              { title: "Select Tier",      options: [], url: "api.php?url=getTiers",      paramKey: "examboardId" }
              // ✅ no "Select Year" here
            ];
          } else {
            // Non-GCSE (KS1, KS2, etc.): includes Year
            steps = [
              { title: "Select Level",   options: [], url: "api.php?url=getCourses",  paramKey: null },
              { title: "Select Subject", options: [], url: "api.php?url=getSubjects", paramKey: "levelId" },
              { title: "Select Year",    options: [], url: "api.php?url=getYears",    paramKey: "subjectId" }
            ];
          }

        } else if (step.title === "Select Subject")   {
          selectedValues.subjectId   = opt.id;
        } else if (step.title === "Select Examboard") {
          selectedValues.examboardId = opt.id;
        } else if (step.title === "Select Tier")      {
          selectedValues.tierId      = opt.id;
        } else if (step.title === "Select Year")      {
          selectedValues.yearId      = opt.id;
        }

        currentStep++;

        if (currentStep < steps.length) {
          fetchOptionsForStepWithParam(currentStep, selectedValues)
            .then(() => renderStep(container, currentStep));
        } else {
          // final step → resolve & go (aligned with hero: uses setup_ids)
          fetch(makeAbs("api.php?url=resolveSetup" + buildQueryString(selectedValues)))
            .then(r => r.json())
            .then(j => {
              if (j.status === 1 && j.data?.setup_ids && Array.isArray(j.data.setup_ids)) {
                const ids = j.data.setup_ids.join(',');
                const nextUrl = (window.fcom && typeof fcom.makeUrl === 'function')
                  ? fcom.makeUrl('quizizz') + '?setup_ids=' + ids
                  : (baseUrl + 'quizizz?setup_ids=' + ids);
                window.location.href = nextUrl;
              } else {
                alert("Unable to load quiz. Please try again.");
              }
            })
            .catch(() => alert("Network error. Please try again."));
        }
      };

      list.appendChild(btn);
    });

    container.appendChild(list);
  }

  function hideMenu(menu) { menu.style.display = 'none'; menu.setAttribute('aria-expanded', 'false'); }
  function showMenu(menu) { menu.style.display = 'block'; menu.setAttribute('aria-expanded', 'true'); }

  onReady(() => {
    const trigger = qs('#openSelectorNav');
    const menu    = qs('#dropDownOptionNav');
    if (!trigger || !menu) return;

    menu.classList.add('rtm-menu');

    // click-outside to close
    document.addEventListener('click', (e) => {
      if (menu.contains(e.target) || trigger.contains(e.target)) return;
      hideMenu(menu);
    });

    trigger.addEventListener('click', (e) => {
      e.preventDefault();
      if (menu.style.display === 'block') { hideMenu(menu); return; }

      showMenu(menu);

      if (!loadedOnce) {
        fetchOptionsForStepWithParam(0, selectedValues).then(() => {
          renderStep(menu, 0);
          loadedOnce = true;
        });
      } else {
        renderStep(menu, currentStep || 0);
      }
    });
  });
})();
}