<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
  .fq-shell{
    max-width: 1200px;
    margin: 22px auto 44px;
    padding: 0 14px;
  }

  .fq-hero{
    border-radius: 22px;
    overflow: hidden;
    border: 1px solid #e5e7eb;
    background: radial-gradient(1200px 500px at 15% 0%, rgba(45,173,255,.22), transparent 55%),
                radial-gradient(900px 450px at 85% 15%, rgba(10,3,60,.14), transparent 55%),
                linear-gradient(180deg, #ffffff, #f8fbff);
    box-shadow: 0 18px 50px rgba(15,23,42,.08);
  }

  .fq-hero__inner{
    display: grid;
    grid-template-columns: 1.25fr .95fr;
    gap: 18px;
    padding: 28px;
    align-items: center;
  }

  .fq-badge{
    display: inline-flex;
    gap: 8px;
    align-items: center;
    padding: 6px 12px;
    border-radius: 999px;
    border: 1px solid #bfdbfe;
    background: rgba(219,234,254,.7);
    color: #075985;
    font-weight: 700;
    font-size: 12px;
    letter-spacing: .08em;
    text-transform: uppercase;
  }

  .fq-title{
    margin: 10px 0 8px;
    font-size: 34px;
    line-height: 1.1;
    font-weight: 800;
    color: #0f172a;
  }

  .fq-sub{
    margin: 0 0 16px;
    color: #475569;
    font-size: 15.5px;
    max-width: 620px;
  }

  .fq-ctaRow{
    display:flex;
    flex-wrap: wrap;
    gap: 12px;
    align-items:center;
    margin-top: 12px;
  }

  .fq-primaryBtn{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 12px 18px;
    border-radius: 999px;
    background: linear-gradient(135deg, #2DADFF 0%, #14A3FF 100%);
    border: none;
    color: #fff;
    font-weight: 800;
    box-shadow: 0 14px 34px rgba(29,156,253,.28);
    transition: transform .18s ease, box-shadow .18s ease, filter .18s ease;
  }
  .fq-primaryBtn:hover{ transform: translateY(-1px); filter: brightness(1.03); box-shadow: 0 18px 40px rgba(29,156,253,.34); }

  .fq-ghostBtn{
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 11px 16px;
    border-radius: 999px;
    background: #ffffff;
    border: 1px solid #e5e7eb;
    color: #0f172a;
    font-weight: 700;
  }

  .fq-note{
    margin-top: 10px;
    color: #64748b;
    font-size: 12.5px;
  }

  .fq-card{
    border-radius: 18px;
    border: 1px solid #e5e7eb;
    background: #fff;
    box-shadow: 0 12px 32px rgba(15,23,42,.06);
    padding: 16px;
  }

  .fq-card h4{
    margin: 0 0 8px;
    color: #0f172a;
    font-size: 14px;
    font-weight: 800;
    letter-spacing: .02em;
  }

  .fq-statgrid{
    display:grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-top: 10px;
  }

  .fq-stat{
    border-radius: 14px;
    border: 1px solid #eef2f7;
    background: #f9fbff;
    padding: 12px;
  }

  .fq-stat .k{ font-size: 12px; color:#64748b; font-weight:700; }
  .fq-stat .v{ font-size: 16px; color:#0f172a; font-weight:900; margin-top:2px; }

  .fq-grid{
    margin-top: 18px;
    display:grid;
    grid-template-columns: 1.2fr .8fr;
    gap: 16px;
  }

  .fq-sectionTitle{
    margin: 0 0 10px;
    font-size: 18px;
    font-weight: 900;
    color:#0f172a;
  }

  .fq-steps{
    display:grid;
    gap: 10px;
  }

  .fq-step{
    display:flex;
    gap: 12px;
    align-items:flex-start;
    border: 1px solid #e5e7eb;
    border-radius: 16px;
    padding: 12px 14px;
    background: #ffffff;
  }
  .fq-step__num{
    width: 30px;
    height: 30px;
    border-radius: 999px;
    display:flex;
    align-items:center;
    justify-content:center;
    font-weight: 900;
    color:#0b4a6b;
    background: rgba(45,173,255,.18);
    border: 1px solid rgba(45,173,255,.28);
    flex: 0 0 30px;
  }
  .fq-step__txt b{ color:#0f172a; }
  .fq-step__txt{ color:#475569; font-size: 14px; line-height: 1.45; }

  .fq-pillRow{
    display:flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-top: 12px;
  }
  .fq-pill{
    font-size: 12px;
    font-weight: 800;
    color: #075985;
    background: rgba(219,234,254,.7);
    border: 1px solid #bfdbfe;
    padding: 6px 10px;
    border-radius: 999px;
  }

  .fq-side{
    display:grid;
    gap: 12px;
  }

  .fq-side ul{
    margin: 8px 0 0;
    padding-left: 16px;
    color:#475569;
    font-size: 13.5px;
  }

  .fq-side li{ margin-bottom: 6px; }

  .fq-actions{
    display:flex;
    flex-direction: column;
    gap: 10px;
    margin-top: 12px;
  }

  .fq-linkBtn{
    display:flex;
    justify-content: space-between;
    align-items:center;
    gap: 10px;
    padding: 12px 14px;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    background: #ffffff;
    font-weight: 800;
    color: #0f172a;
    text-decoration: none;
    transition: transform .16s ease, box-shadow .16s ease;
  }
  .fq-linkBtn:hover{ transform: translateY(-1px); box-shadow: 0 10px 26px rgba(15,23,42,.08); }

  .fq-mini{
    color:#64748b;
    font-weight: 700;
    font-size: 12.5px;
    margin-top: 4px;
  }

  @media (max-width: 980px){
    .fq-hero__inner{ grid-template-columns: 1fr; }
    .fq-grid{ grid-template-columns: 1fr; }
  }
</style>

<div class="fq-shell">
  <div class="fq-hero">
    <div class="fq-hero__inner">
      <div>
        <div class="fq-badge">🎯 Free Quiz Mode • Quick Topic Revision</div>

        <h1 class="fq-title">Free Quizzes</h1>

        <p class="fq-sub">
          Choose your level and subject, then practice with topic-based quizzes designed to build accuracy,
          speed, and confidence. Perfect for daily revision and exam prep.
        </p>

        <div class="fq-ctaRow">
          <button id="openSelector" class="fq-primaryBtn" type="button">
            Revise Your Topic
            <span aria-hidden="true">→</span>
          </button>

          <a class="fq-ghostBtn" href="<?php echo MyUtility::makeUrl('Courses'); ?>">
            Explore Courses
            
          </a>
        </div>

        <div class="fq-note">
          Tip: Do a short quiz first → review mistakes → watch a targeted video → attempt again.
        </div>

        <div class="fq-pillRow">
          <span class="fq-pill">Instant practice</span>
          <span class="fq-pill">Topic selection</span>
          <span class="fq-pill">Smarter revision</span>
          <span class="fq-pill">Exam confidence</span>
        </div>
      </div>

      <div class="fq-card">
        <h4>Your best revision routine</h4>
        <div class="fq-statgrid">
          <div class="fq-stat">
            <div class="k">Step 1</div>
            <div class="v">Attempt quiz</div>
          </div>
          <div class="fq-stat">
            <div class="k">Step 2</div>
            <div class="v">Review mistakes</div>
          </div>
          <div class="fq-stat">
            <div class="k">Step 3</div>
            <div class="v">Watch lesson</div>
          </div>
          <div class="fq-stat">
            <div class="k">Step 4</div>
            <div class="v">Try again</div>
          </div>
        </div>

        <div class="fq-actions">
          <a class="fq-linkBtn" href="<?php echo MyUtility::makeUrl('Pricing'); ?>">
            Upgrade for full access
            <span aria-hidden="true">↗</span>
          </a>
          <div class="fq-mini">
            Upgrading unlocks more practice modes,full courses,AI tutor, deeper progress insights, and wider subject access.
          </div>
        </div>
      </div>
    </div>
  </div>

  <div class="fq-grid">
    <div class="fq-card">
      <h3 class="fq-sectionTitle">How to attempt quizzes (do it like a topper)</h3>

      <div class="fq-steps">
        <div class="fq-step">
          <div class="fq-step__num">1</div>
          <div class="fq-step__txt"><b>Start small:</b> Attempt a quick quiz to identify weak areas. Don’t aim for perfection on your first run.</div>
        </div>
        <div class="fq-step">
          <div class="fq-step__num">2</div>
          <div class="fq-step__txt"><b>Focus on accuracy:</b> Read the question carefully, eliminate wrong options, and answer confidently.</div>
        </div>
        <div class="fq-step">
          <div class="fq-step__num">3</div>
          <div class="fq-step__txt"><b>Review your mistakes:</b> Note down what went wrong (concept gap, silly mistake, or time pressure).</div>
        </div>
        <div class="fq-step">
          <div class="fq-step__num">4</div>
          <div class="fq-step__txt"><b>Watch video lessons:</b> Use short topic lessons to fix the exact gap you discovered in the quiz.</div>
        </div>
        <div class="fq-step">
          <div class="fq-step__num">5</div>
          <div class="fq-step__txt"><b>Re-attempt:</b> Do the same topic again after learning. Your score will improve fast when revision is targeted.</div>
        </div>
      </div>
    </div>

    <div class="fq-side">
      <div class="fq-card">
        <h3 class="fq-sectionTitle">Advantages of quizzes</h3>
        <ul>
          <li>Builds speed + accuracy with repeated topic practice</li>
          <li>Reveals exactly what to study next (no guesswork)</li>
          <li>Improves recall and exam technique under time pressure</li>
          <li>Creates a “feedback loop” → learn → practice → improve</li>
        </ul>
      </div>

      <div class="fq-card">
        <h3 class="fq-sectionTitle">What to do after quizzes</h3>
        <ul>
          <li>Revisit the topic using a short video lesson</li>
          <li>Practice similar questions (same topic, different phrasing)</li>
          <li>Move to <b>past papers</b> once your topic confidence increases</li>
          <li>Repeat weekly to keep the knowledge fresh</li>
        </ul>

        <div class="fq-actions">
          <a class="fq-linkBtn" href="<?php echo MyUtility::makeUrl('Courses'); ?>">
            Our top Courses
          </a>
          <a class="fq-linkBtn" href="<?php echo MyUtility::makeUrl('Teachers'); ?>">
            Need help? Find a tutor
            <span aria-hidden="true">👨‍🏫</span>
          </a>
        </div>
      </div>
    </div>
  </div>
</div>

<!-- Modal (same IDs as visitor home) -->
<div class="modal fade rw-modal" id="reviseTopicModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content shadow-lg">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold">Revise your Topic</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body pt-2">
        <div id="dropDownOptions" class="rtm-body"></div>
      </div>
    </div>
  </div>
</div>

<script>
  // ✅ Use the same base as visitor home (root public URL)
  window.RWU_CONFIG = {
    baseUrl: <?= json_encode(getBaseUrl()); ?>,
    quizizzUrl: <?= json_encode($quizizzUrl); ?>
  };
</script>

<script src="<?= CONF_WEBROOT_URL ?>js/home.js"></script>
