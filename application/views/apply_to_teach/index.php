<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php

function asset_css($f){
  $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($f,'/');
  return CONF_WEBROOT_URL . $f . '?v=' . (@filemtime($abs) ?: time());
}
?>
<link rel="stylesheet" href="<?= asset_css('css/apply-tutor.page.css') ?>"/>

<section class="tutor-hero">
  <div class="tutor-hero__inner">
    <div class="tutor-hero__copy">
      <h1 class="tutor-hero__title">
        Become a Tutor<br>
        <span class="accent">Empower Students Globally</span>
      </h1>
      <p class="tutor-hero__sub">
        Empower learners from over 180 countries by teaching what you love.
        Set your own hours, earn confidently, and grow your teaching career online.
      </p>
      <a href="<?= MyUtility::makeUrl('ApplyToTeach','start'); ?>" class="tutor-hero__cta">
        Apply to Teach Now
      </a>
    </div>

    <div class="tutor-hero__art">
      <img src="<?= CONF_WEBROOT_URL ?>images/hero/teacher.png?v=1" alt="Tutor hero">
    </div>
  </div>
</section>

<<section class="tutor-stats">
  <div class="wrap">
    <?php foreach ($stats as $s): ?>
      <div class="stat" data-variant="<?= $s['variant'] ?? 'primary' ?>">
        <div class="stat__icon">
          <span class="stat__svg"><?= $s['icon'] ?></span>
        </div>
        <div class="stat__content">
          <div class="value"><?= $s['value'] ?></div>
          <div class="label"><?= htmlspecialchars($s['label']) ?></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>
<?php /* Steps Section */ 
// $stats = [
//   ['icon' => $usersSvg,     'value' => '67.1k', 'label' => 'Students',           'variant' => 'primary'],
//   ['icon' => $notebookSvg,  'value' => '26k',   'label' => 'Certified Instructor','variant' => 'secondary'],
//   ['icon' => $globeSvg,     'value' => '72',    'label' => 'Country Language',    'variant' => 'error'],
//   ['icon' => $checkSvg,     'value' => '99.9%', 'label' => 'Success Rate',        'variant' => 'success'],
//   ['icon' => $stackSvg,     'value' => '57',    'label' => 'Trusted Companies',   'variant' => 'warn'],
// ];?>
<!-- <section class="tutor-steps">
  <div class="wrap">
    <?php foreach ($steps as $i=>$st): ?>
      <article class="step">
        <div class="step__num"><?= $i+1 ?></div>
        <h3><?= htmlspecialchars($st['title']) ?></h3>
        <p><?= htmlspecialchars($st['desc']) ?></p>
      </article>
    <?php endforeach; ?>
  </div>
</section> -->
<section class="tutor-benefits">
  <div class="wrap">
    <div class="pill">Benefits</div>

    <h2 class="title">
      Why Teach with <span class="brand">ReadWithUs?</span>
    </h2>

    <p class="sub">
      Discover the freedom, flexibility, and support that make ReadWithUs the trusted choice for tutors worldwide.
    </p>

    <div class="grid">
      <!-- Card 1 -->
      <article class="benefit-card">
        <div class="icon" data-variant="indigo"><?php /* 40px SVG */ ?>
          <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
            <path d="M4 7h16v10H4zM4 7l8-4 8 4" fill="none" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Earn with Freedom</h3>
        <p>Decide your own hourly rates and cash out anytime, no hidden fees.</p>
      </article>

      <!-- Card 2 -->
      <article class="benefit-card">
        <div class="icon" data-variant="red">
          <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
            <path d="M3 5h18M7 5v14m5-10h7m-7 5h5" fill="none" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Teach Anytime, Anywhere</h3>
        <p>Go online from your laptop, tablet, or phone—wherever you are.</p>
      </article>

      <!-- Card 3 -->
      <article class="benefit-card">
        <div class="icon" data-variant="orange">
          <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
            <path d="M12 2v6m0 8v6M4 12h6m4 0h6" fill="none" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Grow Your Skills</h3>
        <p>Access free training, webinars, and resources to stay ahead.</p>
      </article>

      <!-- Card 4 -->
      <article class="benefit-card">
        <div class="icon" data-variant="green">
          <svg viewBox="0 0 24 24" width="24" height="24" aria-hidden="true">
            <path d="M12 17l-4-4 1.4-1.4L12 14.2l6.6-6.6L20 9" fill="none" stroke="currentColor" stroke-width="2"/>
          </svg>
        </div>
        <h3>Secure & Reliable</h3>
        <p>Safe payments, verified students, and 24/7 support for every tutor.</p>
      </article>
    </div>
  </div>
</section>

<section class="tutor-why">
  <div class="wrap">
    <!-- LEFT: copy -->
    <div class="copy">
      <h2>Teach Students from Over <span class="brand">180 Countries</span></h2>
      <p class="lead">
        Join a global learning community where passionate tutors connect with motivated students, anywhere, anytime.
      </p>

      <ul class="bullets">
        <li>Interactive Virtual Classrooms</li>
        <li>Smart Scheduling &amp; Reminders</li>
        <li>Consistent Student Flow</li>
        <li>Instant &amp; Secure Payouts</li>
        <li>Dedicated Tutor Support Team</li>
      </ul>

      <a class="btn btn--primary" href="<?= MyUtility::makeUrl('ApplyToTeach','start'); ?>">
        Start Teaching Today
      </a>
    </div>

    <!-- RIGHT: art (two images) -->
    <div class="art">
      <!-- small/narrow image (left) -->
      <!-- <figure class="chip">
        <img
          src="<?= CONF_WEBROOT_URL ?>images/hero/girl.jpg"
          alt="Online lesson snapshot">
      </figure> -->

      <!-- tall portrait image (right) -->
      <figure class="portrait">
        <img
          src="<?= CONF_WEBROOT_URL ?>images/hero/girl.jpg"
          alt="Tutor teaching online">
      </figure>
    </div>
  </div>
</section>

<section class="tutor-two-col">
  <div class="wrap">
    <div class="col">
      <h3>Requirements</h3>
      <ul class="checklist">
        <?php foreach ($requirements as $r): ?>
          <li><?= htmlspecialchars($r) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
    <div class="col">
      <h3>Benefits</h3>
      <div class="benefits">
        <?php foreach ($benefits as $b): ?>
          <div class="benefit">
            <h4><?= htmlspecialchars($b['title']) ?></h4>
            <p><?= htmlspecialchars($b['desc']) ?></p>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
</section>


<!-- <section class="tutor-testimonial">
  <div class="wrap">
    <blockquote>
      “This platform helped me reach students I never could before. The tools make teaching online genuinely effective.”
    </blockquote>
    <div class="who">— Sarah W., Physics Tutor</div>
  </div>
</section> -->

<!-- <section class="tutor-faq">
  <div class="wrap">
    <h3>Frequently Asked Questions</h3>
    <div class="faq">
      <?php foreach ($faqs as $f): ?>
        <div class="faq__item">
          <button class="faq__q"><?= htmlspecialchars($f['q']) ?></button>
          <div class="faq__a"><?= htmlspecialchars($f['a']) ?></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section> -->

<section class="tutor-cta">
  <div class="wrap">
    <h3>Ready to start your tutoring journey?</h3>
    <p>Create your profile, publish your first course, and begin teaching online.</p>
    <a class="btn btn--primary" href="<?= $hero['cta_url'] ?>">Apply to Teach Now</a>
  </div>
</section>
<style>

    /* ===== Apply as Tutor page ===== */
:root{
  --ink:#0A033C; --text:#5F6C76; --blue:#2DADFF; --soft:#F5FAFF; --card:#FFFFFF;
}

/* HERO */
/* ===== Apply as Tutor: HERO (Figma-aligned) ===== */
:root{
  --ink:#0A033C; --text:#4E5566; --blue:#2DADFF; --soft:#F5FAFF;
  --cta:#FF6636; --white:#fff;
}

/* Background wash only; hero image is a real <img> on the right */
.tutor-hero{
  background-image: linear-gradient(180deg, rgba(245,250,255,.92), #fff);
  background-repeat: no-repeat;
  background-size: cover;
}

/* Two-column layout per spec (row gap ~78px, generous side paddings) */
.tutor-hero__inner{
  max-width: 1480px;
  margin: 0 auto;
  padding: clamp(24px, 3vw, 32px) clamp(16px, 6vw, 48px) 0;
  display: grid;
  grid-template-columns: minmax(320px, 1fr) minmax(520px, 640px);
  align-items: center;
  gap: clamp(32px, 5vw, 78px);
  min-height: 710px; /* visual height from Figma */
}

/* Left copy */
.tutor-hero__copy{ max-width: 759px; }
.tutor-hero__title{
  margin: 0 0 6px;
  color: var(--ink);
  font: 700 52px/70px Poppins, system-ui;
  letter-spacing: -0.01em;
}
.tutor-hero__title .accent{ color:#1D9CFD; } /* “Empower Students Globally” */
.tutor-hero__sub{
  margin: 12px 0 22px;
  max-width: 641px;
  color: var(--text);
  font: 400 24px/32px Inter, system-ui;
}

/* CTA (64px tall, 40px horizontal padding) */
.tutor-hero__cta{
  display: inline-flex; align-items: center; justify-content: center;
  height: 64px; padding: 0 40px; gap: 12px;
  background: var(--cta); color: var(--white);
  border: 0; border-radius: 8px;
  font: 600 20px/64px Inter, system-ui; letter-spacing: -0.01em; text-transform: capitalize;
  box-shadow: 0 10px 24px rgba(255,102,54,.25);
  transition: transform .15s ease, filter .15s ease, box-shadow .15s ease;
}
.tutor-hero__cta:hover{ transform: translateY(-2px); filter: brightness(1.02); }
.tutor-hero__cta:focus-visible{ outline: 3px solid rgba(255,102,54,.35); outline-offset: 2px; }

/* Right image with soft inner + outer shadow, size ~683x648 from Figma */
.tutor-hero__art{
  display: grid; place-items: center;
}
.tutor-hero__art img{
  width: clamp(400px, 40vw, 653px);
  height: auto;
  aspect-ratio: 683 / 648;
  object-fit: contain;
  border-radius: 6px; /* optional, to soften */
  box-shadow: inset -12px -24px 40px rgba(0,0,0,.10);
  filter:
    drop-shadow(0 4px 4px rgba(0,0,0,.25))
    drop-shadow(0 4px 4px rgba(0,0,0,.25))
    drop-shadow(-20px 20px 40px rgba(0,0,0,.12));
}

/* ===== Tutor: Benefits ===== */
.tutor-benefits{
  background:#F5F7FA; /* Gray/50 */
}
.tutor-benefits .wrap{
  max-width:1320px;
  margin:0 auto;
  padding:80px 16px; /* Figma: 80 top/btm */
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:16px;
}

/* Pill */
.tutor-benefits .pill{
  height:53px; min-width:133px;
  padding:0 22px;
  border-radius:20px;
  background:#C2E6FF;
  color:#14A3FF;
  display:inline-flex; align-items:center; justify-content:center;
  font:600 20px/21px Inter,system-ui;
}

/* Title + sub */
.tutor-benefits .title{
  text-align:center; margin:0;
  color:#0A033C; /* Gray/900 */
  font:600 clamp(32px,4vw,52px)/75px Poppins,system-ui;
}
.tutor-benefits .title .brand{ color:#1D9CFD; }
.tutor-benefits .sub{
  max-width:883px; margin:0; text-align:center;
  color:#5F6C76; /* Gray/700 */
  font:500 clamp(15px,1.2vw,17px)/30px Inter,system-ui;
}

/* Grid of cards */
.tutor-benefits .grid{
  width:100%;
  margin-top:8px;
  display:grid;
  grid-template-columns:repeat(4,minmax(0,1fr));
  gap:24px;                 /* Figma gap */
}

/* Card */
.benefit-card{
  background:#FFFFFF;       /* Gray/White */
  border:1px solid #EEF2F6; /* subtle border */
  border-radius:12px;
  padding:24px;
  display:flex; flex-direction:column; align-items:center; gap:24px;
  box-shadow:0 10px 24px rgba(10,3,60,.05);
  text-align:center;
}

/* Icon tile 80×80 with soft tint */
.benefit-card .icon{
  width:80px; height:80px; border-radius:12px;
  display:grid; place-items:center;
  background:rgba(86,79,253,.10); /* default indigo */
  color:#564FFD;
}
.benefit-card .icon[data-variant="indigo"]{ background:rgba(86,79,253,.10); color:#564FFD; }
.benefit-card .icon[data-variant="red"]   { background:#FFF0F0;            color:#E34444; }
.benefit-card .icon[data-variant="orange"]{ background:#FFEEE8;            color:#FF6636; }
.benefit-card .icon[data-variant="green"] { background:#E1F7E3;            color:#23BD33; }
.benefit-card .icon svg{ width:40px; height:40px; }

/* Card text */
.benefit-card h3{
  margin:0;
  color:#1D2026;            /* Gray/900 */
  font:500 18px/24px Poppins,system-ui;
}
.benefit-card p{
  margin:0;
  color:#6E7485;            /* Gray/600 */
  letter-spacing:-0.01em;
  font:400 14px/22px Inter,system-ui;
}

/* Responsive */
@media (max-width:1100px){
  .tutor-benefits .grid{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width:560px){
  .tutor-benefits .grid{ grid-template-columns:1fr; }
  .tutor-benefits .title{ line-height:1.25; }
}
/* ===== Tutor: Why Teach (two-image layout) ===== */
.tutor-why{
  background:#fff;
}
.tutor-why .wrap{
  max-width:1320px;
  margin:0 auto;
  padding:80px 16px;
  display:grid;
  grid-template-columns:minmax(0, 1.05fr) minmax(0, .95fr);
  gap:79px; /* from figma */
  align-items:center;
}

/* Copy */
.tutor-why .copy h2{
  margin:0 0 14px;
  color:#1D2026;                 /* Gray/900 */
  font:600 clamp(28px,4vw,52px)/65px Poppins,system-ui; /* 52/65 per figma */
  letter-spacing:-0.01em;
}
.tutor-why .copy .brand{ color:#1D9CFD; }
.tutor-why .copy .lead{
  max-width:646px;
  margin:0 0 18px;
  color:#6E7485;                 /* Gray/600 */
  font:400 clamp(16px,1.6vw,24px)/31px Inter,system-ui;
  letter-spacing:-0.015em;
}
.tutor-why .bullets{
  margin:0 0 24px; padding:0; list-style:none;
  display:grid; gap:12px;
}
.tutor-why .bullets li{
  position:relative; padding-left:28px;
  color:#1D2026;                 /* Gray/900 */
  font:400 clamp(15px,1.4vw,20px)/24px Inter,system-ui;
}
.tutor-why .bullets li::before{
  content:""; position:absolute; left:0; top:6px;
  width:12px; height:12px; border-radius:50%;
  background:#2DADFF;           /* brand dot */
  box-shadow:0 0 0 4px rgba(45,173,255,.18);
}

/* Button (uses your existing .btn.btn--primary base) */
.tutor-why .btn.btn--primary{
  height:56px; padding:0 28px; border-radius:10px;
  font-weight:600; font-size:16px;
}

/* Art (two images on the right) */
.tutor-why .art{
  position:relative;
  min-height:648px;             /* matches portrait height */
}
.tutor-why .art figure{ margin:0; }

/* Tall portrait (right side) */
.tutor-why .portrait{
  position:absolute;

  right:0; top:10%;
  width:500px; height:500px;    /* figma 424×648 */
  border-radius:16px; overflow:hidden;
  box-shadow:0 20px 40px rgba(10,3,60,.12);
  background:#eef3f7;
}
.tutor-why .portrait img{
  width:100%; height:100%; object-fit:cover;
}

/* Slim chip (left) */
.tutor-why .chip{
  position:absolute;
  left:0; top:50%;
  width:200px; height:471px;    /* figma 200×471 */
  transform:translateY(-50%);
  border-radius:16px; overflow:hidden;
  background:#f6fbff;
  box-shadow:0 18px 34px rgba(0,0,0,.10);
  /* subtle white border like figma card feel */
  outline:6px solid #fff;
}
.tutor-why .chip img{
  width:100%; height:100%; object-fit:cover;
}

/* Responsive */
@media (max-width:1100px){
  .tutor-why .wrap{
    grid-template-columns:1fr;
    gap:32px;
  }
  .tutor-why .art{
    min-height:520px;
  }
  .tutor-why .portrait{
    position:relative;
    width:360px; height:540px; margin-left:auto;
  }
  .tutor-why .chip{
    left:8%; width:180px; height:420px;
  }
}
@media (max-width:620px){
  .tutor-why .portrait{ width:290px; height:440px; }
  .tutor-why .chip{ width:150px; height:360px; left:4%; }
}

/* ===== Tutor: Fun Fact / Stats band ===== */
.tutor-stats{
  background:#FFEEE8;
}
.tutor-stats .wrap{
  max-width:1200px;
  margin:0 auto;
  /* Figma paddings ~40px sides, centered row */
  padding:40px 16px;
  display:grid;
  grid-template-columns:repeat(5,minmax(0,1fr));
  gap:24px;
  align-items:center;
  justify-items:center;
}

/* Each stat = icon (40x40) + text column */
.stat{
  display:flex;
  align-items:flex-start;
  gap:16px;                 /* Figma gap */
}

/* Icon ring (40px) with color variants */
.stat__icon{
  position:relative;
  width:40px; height:40px; flex:0 0 40px;
}
.stat__icon::before{
  content:"";
  position:absolute; inset:0; border-radius:50%;
  background:var(--tone, rgba(255,102,54,.20));     /* default primary tone */
}
.stat__icon::after{
  content:"";
  position:absolute; inset:0; border-radius:50%;
  border:2px solid var(--stroke, #FF6636);          /* default primary stroke */
}
.stat__svg{
  position:relative; z-index:1;                     /* above tone layer */
  display:grid; place-items:center;
  width:40px; height:40px;
}
.stat__svg svg{ width:24px; height:24px; }

/* Content */
.stat__content{ display:flex; flex-direction:column; gap:8px; }
.stat .value{
  color:#1D2026;
  font:600 32px/40px Inter,system-ui;               /* Figma 32/40 */
  letter-spacing:-0.01em;
}
.stat .label{
  color:#4E5566;
  font:500 14px/20px Inter,system-ui;               /* Figma 14/20 */
  letter-spacing:-0.01em;
}

/* Variants (match your Figma color tokens) */
.stat[data-variant="primary"]  { --tone: rgba(255,102,54,.20); --stroke:#FF6636; } /* orange */
.stat[data-variant="secondary"]{ --tone: rgba(86,79,253,.20);  --stroke:#564FFD; } /* indigo */
.stat[data-variant="error"]    { --tone: rgba(227,68,68,.20);  --stroke:#E34444; } /* red */
.stat[data-variant="success"]  { --tone: rgba(35,189,51,.20);  --stroke:#23BD33; } /* green */
.stat[data-variant="warn"]     { --tone: rgba(253,142,31,.20); --stroke:#FD8E1F; } /* amber */

/* Responsive */
@media (max-width:1100px){
  .tutor-stats .wrap{ grid-template-columns:repeat(3,minmax(0,1fr)); }
}
@media (max-width:720px){
  .tutor-stats .wrap{ grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media (max-width:480px){
  .tutor-stats .wrap{ grid-template-columns:1fr; }
  .stat{ justify-content:center; }
}

/* Steps / two-col / testimonial / faq / cta — keep your existing rules */
@media (max-width: 1000px){
  .tutor-hero__inner{ grid-template-columns: 1fr; text-align: left; min-height: unset; }
  .tutor-hero__art{ order: -1; margin-bottom: 18px; } /* image above on tablet */
}
@media (max-width: 680px){
  .tutor-hero__title{ font-size: 36px; line-height: 46px; }
  .tutor-hero__sub{ font-size: 18px; line-height: 28px; }
  .tutor-hero__cta{ height: 56px; line-height: 56px; font-size: 18px; padding: 0 28px; }
}

s
/* STEPS */
.tutor-steps .wrap{ max-width:1200px; margin:0 auto; padding:60px 16px; display:grid; grid-template-columns:repeat(4,1fr); gap:24px;}
.step{ background:#fff; border:1px solid #EEF3F7; border-radius:12px; padding:18px; box-shadow:0 10px 26px rgba(0,0,0,.05);}
.step__num{ width:34px;height:34px;border-radius:50%;background:#EAF5FF;color:#1D9CFD;display:grid;place-items:center;font-weight:700; margin-bottom:10px;}
.step h3{ color:var(--ink); font-weight:700; font-size:16px; margin:0 0 6px;}
.step p{ color:var(--text); font-size:14px; }

/* TWO-COL: requirements + benefits */
.tutor-two-col .wrap{ max-width:1200px; margin:0 auto; padding:10px 16px 60px; display:grid; grid-template-columns:1fr 1fr; gap:30px;}
.tutor-two-col h3{ color:var(--ink); margin:0 0 8px;}
.checklist{ list-style:none; padding:0; margin:0; display:grid; gap:10px;}
.checklist li{ padding-left:28px; position:relative; color:var(--text);}
.checklist li::before{ content:'✓'; position:absolute; left:0; top:0; color:#10893e; font-weight:700;}
.benefits{ display:grid; grid-template-columns:repeat(2,1fr); gap:14px;}
.benefit{ background:#fff; border:1px solid #EEF3F7; border-radius:10px; padding:14px; }
.benefit h4{ margin:0 0 6px; color:var(--ink); }
.benefit p{ margin:0; color:var(--text); }

/* TESTIMONIAL */
.tutor-testimonial .wrap{ max-width:900px; margin:0 auto; padding:44px 16px; text-align:center;}
.tutor-testimonial blockquote{ font:600 20px/1.5 Poppins,system-ui; color:var(--ink); margin:0 0 8px;}
.tutor-testimonial .who{ color:var(--text); }

/* FAQ */
.tutor-faq .wrap{ max-width:900px; margin:0 auto; padding:8px 16px 44px; }
.tutor-faq h3{ text-align:center; color:var(--ink); margin-bottom:10px;}
.faq__item{ border:1px solid #E6F1FF; border-radius:10px; background:#F6FBFF; margin:10px 0; }
.faq__q{ width:100%; text-align:left; padding:12px 14px; background:transparent; border:none; font-weight:700; color:var(--ink); cursor:pointer;}
.faq__a{ display:none; padding:0 14px 12px; color:var(--text); }

/* FINAL CTA */
.tutor-cta .wrap{ max-width:900px; margin:0 auto; text-align:center; padding:24px 16px 64px;}
.tutor-cta h3{ color:var(--ink); margin:0 0 6px;}
.tutor-cta p{ color:var(--text); margin:0 0 12px;}

/* Responsive */
@media (max-width:1000px){
  .tutor-stats .wrap{ grid-template-columns:repeat(3,1fr); }
  .tutor-steps .wrap{ grid-template-columns:repeat(2,1fr); }
}
@media (max-width:680px){
  .tutor-stats .wrap{ grid-template-columns:repeat(2,1fr); }
  .tutor-steps .wrap{ grid-template-columns:1fr; }
  .tutor-two-col .wrap{ grid-template-columns:1fr; }
  .benefits{ grid-template-columns:1fr; }
}

</style>
<script>
  // simple accordion
  document.querySelectorAll('.faq__q').forEach(btn => {
    btn.addEventListener('click', () => {
      const a = btn.nextElementSibling;
      a.style.display = a.style.display === 'block' ? 'none' : 'block';
      btn.classList.toggle('is-open');
    });
  });

  // ===== Animated counters for tutor-stats =====
  (function () {
    const counters = document.querySelectorAll('.tutor-stats .value');
    if (!counters.length) return;

    // Start counter when element is visible
    const startWhenVisible = (el) => {
      if ('IntersectionObserver' in window) {
        const io = new IntersectionObserver((entries, obs) => {
          entries.forEach(entry => {
            if (entry.isIntersecting) {
              runCounter(el);
              obs.unobserve(entry.target);
            }
          });
        }, { threshold: 0.4 });
        io.observe(el);
      } else {
        // Fallback: start immediately
        runCounter(el);
      }
    };

    counters.forEach(startWhenVisible);

    function runCounter(el) {
      const original = el.textContent.trim();

      const meta = parseMeta(original); // figure out target number + type
      const duration = 2500; // ms
      const startTime = performance.now();

      function tick(now) {
        const progress = Math.min((now - startTime) / duration, 1);
        const current = meta.target * progress;

        el.textContent = formatValue(current, meta);

        if (progress < 1) {
          requestAnimationFrame(tick);
        } else {
          // Snap to exact original value to avoid rounding differences
          el.textContent = original;
        }
      }

      requestAnimationFrame(tick);
    }

    // Turn "67.1k", "26k", "99.9%", "72" into a clean target number + flags
    function parseMeta(text) {
      let hasK = /k$/i.test(text);
      let hasPercent = /%$/.test(text);

      // Strip k, %, commas
      let clean = text.replace(/[,k%]/gi, '');
      let decimals = (clean.split('.')[1] || '').length;

      let num = parseFloat(clean) || 0;
      if (hasK) num = num * 1000;   // 67.1k -> 67100

      return {
        target: num,
        hasK,
        hasPercent,
        decimals
      };
    }

    // Format the animated value in the same style as original
    function formatValue(value, meta) {
      let v = value;

      if (meta.hasK) {
        // show as "xx.xk"
        v = v / 1000;
        return v.toFixed(meta.decimals || 1) + 'k';
      }

      if (meta.hasPercent) {
        // show as "99.9%"
        return v.toFixed(meta.decimals || 1) + '%';
      }

      if (meta.decimals > 0) {
        // e.g. "12.3"
        return v.toFixed(meta.decimals);
      }

      // plain integer with commas
      return Math.round(v).toLocaleString();
    }
  })();
</script>


