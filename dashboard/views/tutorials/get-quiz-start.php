<?php defined('SYSTEM_INIT') or die('Invalid Usage.');
$webroot = defined('CONF_WEBROOT_FRONT_URL') ? CONF_WEBROOT_FRONT_URL : '/';
 ?>
<style>
/* ====== Layout ====== */
.qz-wrap {
  display: grid;
  grid-template-columns: 1.3fr .9fr;           /* wider main card than sidebar */
  gap: 22px;
  max-width: 1080px;                           /* ~30% wider than before */
  margin: 16px auto 28px;
  padding: 0 8px;
}
@media (max-width: 1024px){ .qz-wrap{ grid-template-columns: 1fr; } }

/* ====== Main Start Card ====== */
.qz-card {
  position: relative;
  min-height: 360px;                           /* taller than before */
  background: radial-gradient(1200px 480px at -10% -30%, #eaf6ff 0%, transparent 55%),
              radial-gradient(800px 360px at 120% 120%, #f4fffd 0%, transparent 50%),
              #ffffff;
  border: 1px solid #e8edf3;
  border-radius: 16px;
  padding: 28px;
  overflow: hidden;
  box-shadow: 0 10px 24px rgba(17,24,39,.05), 0 2px 6px rgba(17,24,39,.06);
}

/* Decorative ribbons */
.qz-card::before, .qz-card::after{
  content:"";
  position:absolute; inset:auto -40px -40px auto;
  width: 220px; height: 220px;
  border-radius: 24px;
  transform: rotate(25deg);
  background: linear-gradient(135deg, rgba(20,160,163,.12), rgba(65,154,213,.10));
  filter: blur(10px);
}
.qz-card::after{
  inset: -40px auto auto -40px; transform: rotate(-18deg);
}

/* Head */
.qz-header { display:flex; align-items:center; gap:14px; margin-bottom:8px; }
.qz-badge {
  display:inline-flex; align-items:center; gap:6px;
  padding:6px 10px; border-radius:999px;
  background:#eef7f7; color:#0f6c6e; font-weight:700; font-size:12px;
  letter-spacing:.02em; text-transform:uppercase;
}
.qz-title {
  font-size:26px; line-height:1.2; margin:0; font-weight:800; color:#0f172a;
}
.qz-sub {
  color:#64748b; margin:6px 0 16px; font-size:14px;
}

/* Meta pills */
.qz-meta { display:flex; flex-wrap:wrap; gap:10px; margin:10px 0 18px; }
.qz-pill {
  background:#f1f5f9; color:#0f172a; border:1px solid #e2e8f0;
  border-radius:10px; padding:8px 12px; font-weight:600; font-size:13px;
}

/* Rule list */
.qz-rules {
  background:#f9fafb; border:1px dashed #dbe3ee; border-radius:12px;
  padding:16px 18px; margin-top:12px;
}
.qz-rules strong{ color:#0f172a; }
.qz-rules ul{ margin:10px 0 0 22px; }
.qz-rules li{ margin:6px 0; color:#374151; }

/* Callouts */
.qz-callout {
  display:flex; gap:10px; align-items:flex-start; margin-top:16px;
  background:#fff7ed; border:1px solid #fde7c7; color:#8a4b10;
  border-radius:10px; padding:10px 12px; font-size:13px;
}

/* Actions */
.qz-actions { display:flex; flex-wrap:wrap; gap:12px; margin-top:22px; }
.qz-btn-primary {
  background:#14A0A3; color:#fff; border:none; padding:12px 18px;
  border-radius:10px; cursor:pointer; font-weight:700; letter-spacing:.2px;
  box-shadow: 0 6px 16px rgba(20,160,163,.25);
  transition: transform .06s ease, box-shadow .2s ease, background .2s ease;
}
.qz-btn-primary:hover { background:#118e90; box-shadow:0 8px 18px rgba(20,160,163,.32); transform: translateY(-1px); }
.qz-btn-secondary {
  background:#f3f4f6; color:#111827; border:1px solid #e5e7eb;
  padding:12px 16px; border-radius:10px; cursor:pointer; font-weight:700;
}

/* ====== Sidebar: Readiness Checklist ====== */
.qz-aside {
  background:#ffffff; border:1px solid #e8edf3; border-radius:16px;
  padding:22px; min-height:360px;
  box-shadow: 0 10px 24px rgba(17,24,39,.04), 0 2px 6px rgba(17,24,39,.05);
}
.qz-aside h4{ margin:0 0 8px; font-size:16px; color:#0f172a; }
.qz-aside p { margin:0 0 14px; color:#64748b; font-size:13px; }
.qz-checks { display:grid; gap:10px; margin:12px 0 6px; }
.qz-check {
  display:flex; gap:10px; align-items:flex-start; padding:10px 12px;
  background:#f8fafc; border:1px solid #e2e8f0; border-radius:10px;
}
.qz-check .mark {
  width:18px; height:18px; min-width:18px; border-radius:4px;
  background:#e2f6f7; display:flex; align-items:center; justify-content:center;
  color:#0f6c6e; font-weight:900; font-size:12px;
}
.qz-check strong{ display:block; color:#0f172a; }
.qz-check span{ display:block; color:#475569; font-size:12px; }

/* Small footer tip */
.qz-aside .mini {
  margin-top:12px; font-size:12px; color:#64748b;
  background:#f6f7fb; border:1px solid #e6e9f4; padding:10px 12px; border-radius:10px;
}

/* Accessibility helper (screen-reader only) */
.sr-only{ position:absolute; width:1px; height:1px; padding:0; margin:-1px; overflow:hidden; clip:rect(0,0,0,0); border:0; }
</style>

<div class="qz-wrap">
  <!-- ===== Main Card ===== -->
  <section class="qz-card" aria-labelledby="qz-title">
    <div class="qz-header">
      <span class="qz-badge">Quiz</span>
      <h3 class="qz-title" id="qz-title"><?php echo htmlspecialchars($title); ?></h3>
    </div>

    <p class="qz-sub"><?php echo Label::getLabel('LBL_READY_TO_START_YOUR_QUIZ'); ?></p>

    <div class="qz-meta" role="list">
      <div class="qz-pill" role="listitem">⏱️ <?php echo Label::getLabel('LBL_DURATION'); ?>: <?php echo floor($duration/60); ?>m</div>
      <div class="qz-pill" role="listitem">🧪 <?php echo Label::getLabel('LBL_ATTEMPT'); ?>: <?php echo Label::getLabel('LBL_SINGLE_SESSION'); ?></div>
      <div class="qz-pill" role="listitem">🎯 <?php echo Label::getLabel('LBL_PASSING_SCORE'); ?>: 60%</div>
    </div>

    <div class="qz-rules">
      <strong><?php echo Label::getLabel('LBL_RULES'); ?>:</strong>
      <ul>
        <li>📽️ Please watch the full lecture before you begin.</li>
        <li>📝 Revise your notes and examples from the lecture.</li>
        <li>🔒 Do not refresh, use the back button, or leave the page during the quiz.</li>
        <li>📶 Ensure a stable internet connection and enough battery power.</li>
        <li>⏳ The quiz auto-submits when the time ends.</li>
        <li>✅ Choose the best answers; some questions may have multiple correct choices.</li>
      </ul>
    </div>

    <div class="qz-callout">
      <span aria-hidden="true">⚠️</span>
      <div>
        <strong>Heads-up:</strong> Passing this quiz may be required to mark this lecture complete and unlock the next section.
      </div>
    </div>

    <div class="qz-actions" role="group" aria-label="Quiz actions">
      <button class="qz-btn-primary" onclick="beginQuizNow()"><?php echo Label::getLabel('LBL_START_QUIZ'); ?></button>
      <button class="qz-btn-secondary" onclick="window.location.href='<?php echo $webroot; ?>teachers';">
        <?php echo Label::getLabel('LBL_FIND_A_TUTOR'); ?>
      </button>
      <button class="qz-btn-secondary" onclick="loadLecture(<?php echo (int)$lectureId; ?>)"><?php echo Label::getLabel('LBL_GO_BACK'); ?></button>
    
    </div>


    <input type="hidden" id="qzStart_courseId" value="<?php echo (int)$courseId; ?>">
    <input type="hidden" id="qzStart_lectureId" value="<?php echo (int)$lectureId; ?>">
    <input type="hidden" id="qzStart_progressId" value="<?php echo (int)$progressId; ?>">
  </section>

  <!-- ===== Sidebar: Readiness Checklist ===== -->
  <aside class="qz-aside" aria-labelledby="qz-ready">
    <h4 id="qz-ready">Be Quiz-Ready ✅</h4>
    <p>Do a quick self-check before you dive in. It improves your score and confidence.</p>

    <div class="qz-checks">
      <div class="qz-check">
        <div class="mark">✓</div>
        <div>
          <strong>Watched the full lecture</strong>
          <span>Understand the key idea, formulae, and method covered.</span>
        </div>
      </div>

      <div class="qz-check">
        <div class="mark">✓</div>
        <div>
          <strong>Revised your notes</strong>
          <span>Skim the examples and any tricky steps you highlighted.</span>
        </div>
      </div>

      <div class="qz-check">
        <div class="mark">✓</div>
        <div>
          <strong>Ready environment</strong>
          <span>Stable internet, notifications muted, calculator .</span>
        </div>
      </div>

      <div class="qz-check">
        <div class="mark">✓</div>
        <div>
          <strong>Timing mindset</strong>
          <span>Don’t spend too long on one question — flag and move on.</span>
        </div>
      </div>
    </div>

    <div class="mini">
      Tip: if you get stuck, note the question number and revisit after answering the rest.
    </div>
  </aside>
</div>

<script>
function beginQuizNow(){
  fcom.ajax(
    fcom.makeUrl('Tutorials', 'beginLectureQuiz'),
    {
      lecture_id: document.getElementById('qzStart_lectureId').value,
      course_id:  document.getElementById('qzStart_courseId').value,
      progress_id:document.getElementById('qzStart_progressId').value
    },
    function(res){
      $('.quizJs').html(res).show();
      // Smooth scroll to quiz container if needed
      try { document.querySelector('.quizJs').scrollIntoView({behavior:'smooth'}); } catch(e){}
    }
  );
}
</script>
