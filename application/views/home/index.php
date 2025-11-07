<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php
function asset_css($file){
  $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($file,'/');
  return CONF_WEBROOT_URL . $file . '?v=' . (@filemtime($abs) ?: time());
}
?>
<?php
function asset_js($file){
  $abs = CONF_APPLICATION_PATH . 'public/' . ltrim($file,'/');
  return CONF_WEBROOT_URL . $file . '?v=' . (@filemtime($abs) ?: time());
}
$heroBase = CONF_WEBROOT_URL . 'images/hero/';
?>
<link rel="stylesheet" href="<?= asset_css('css/rwu-hero.section.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/rwu-trending.cards.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/rwu-trending.resp.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.secondsection.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.thirdsection.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.requesttutor.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.pricing.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.testimonials.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.blogs.css') ?>">
<link rel="stylesheet" href="<?= asset_css('css/home.cta-banner.css') ?>">


<?php
  $siteLangId = MyUtility::getSiteLangId();
  $countries  = Country::getAll($siteLangId);
  $policyPageId = FatApp::getConfig('CONF_PRIVACY_POLICY_PAGE', FatUtility::VAR_INT, 0);
  $termsPageId  = FatApp::getConfig('CONF_TERMS_AND_CONDITIONS_PAGE', FatUtility::VAR_INT, 0);
  $privacyPolicyLink   = $policyPageId ? MyUtility::makeUrl('Cms', 'view', [$policyPageId]) : 'javascript:void(0)';
  $termsConditionsLink = $termsPageId  ? MyUtility::makeUrl('Cms', 'view', [$termsPageId])  : 'javascript:void(0)';
?>


<section class="rw-hero">
  <div class="rw-hero__inner">
    <!-- LEFT -->
    <div class="rw-hero__left">
      <div class="rw-hero__eyebrow">
        <span>🎓</span><span>Learn. Practice. Achieve.</span>
      </div>

      <h1 class="rw-hero__title">
        Your All-in-One Online <span class="accent">Learning Platform</span>
      </h1>

      <p class="rw-hero__sub">
        Learn from trusted tutors and smart digital tools designed for students across the UK to study anywhere, anytime.
        
      </p>
      <p class="sub-heading">
        Welcome to readwithus.uk. UK's most adaptive platform for student of all ages .Access expert human tutoring, interactive video lessons, AI-assisted practice quizzes, and live exam preparation — all in one place.
      </p>

      <!-- Search rail -->
      <div class="rw-hero__search">
      <button id="openSelector" class="rw-pill" type="button">

          <span>Revise Your Topic</span>
          <img src="<?php echo getBaseUrl(); ?>/assets/img/arrow-2.svg" class="arrow" alt="">
        </button>

        <input id="rwCourseQuery" class="rw-input" type="text" placeholder="Class/Course e.g. GCSE Maths, Algebra, English…"/>
        <button id="rwSearchBtn" class="rw-searchbtn">Search</button>
      </div>
    </div>

    <!-- RIGHT: Art -->
    <div class="rw-hero__art">
      <div class="rw-hero__art-inner">
        <img class="rw-hero__img" src="<?= $heroBase ?>hero-girl.svg" alt="Student with laptop">
<!-- Connector lines (SVG) -->
<!-- <svg class="rw-connectors" viewBox="0 0 100 100" preserveAspectRatio="none" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 1;"> -->
  <!-- Left curve: from BOOK (top:15%, left:-9%) to PERSON (top:50%, left:-7%) -->
  <!-- <path d="M -6,15 C 06,25 0,45 -2,76" 
        stroke="#ECECEC" stroke-width="0.7" fill="none" /> -->
  
  <!-- Right curve: from PRESENTATION (top:22%, right:25%) to BLOCKS (bottom:15%, right:10%) -->
  <!-- <path d="M 65,29 C 69,45 80,70 83,76" 
        stroke="#ECECEC" stroke-width="0.6" fill="none" />
</svg> -->

        <!-- Floating cards -->
        <div class="rw-badge rw-badge--book">
          <img src="<?= $heroBase ?>book.svg" alt="Book">
        </div>
        <div class="rw-badge rw-badge--presentation">
          <img src="<?= $heroBase ?>light-bulb.svg" alt="Light Bulb">
        </div>
        <div class="rw-badge rw-badge--person">
          <img src="<?= $heroBase ?>light.svg" alt="icon">
        </div>
        <div class="rw-badge rw-badge--blocks">
          <img src="<?= $heroBase ?>dice.svg" alt="Blocks">
        </div>
      </div>
    </div>
  </div>

  <!-- ✅ Modal placed OUTSIDE grid for proper Bootstrap layering -->
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
</section>

<section class="rw-about">
  <div class="rw-about__wrap">
    <!-- LEFT: text -->
    <div>
      <div class="rw-chip">About Us</div>
      <h2>Empowering Minds Through Technology, Innovation &amp; Learning with Human Tutoring</h2>
      <p class="rw-sublead">
        Empowering Minds Through Technology, Innovation &amp; Learning with Human Tutoring
      </p>

      <div class="rw-quote">
        <p class="mb-0">
          A unified learning experience built for students and educators across the United Kingdom.
          At <strong>ReadWithUs.org.uk</strong>, we combine human expertise, AI-driven learning analytics,
          and interactive study tools to make education personal, measurable, and effective.
          Our goal is to deliver structured, high-quality learning that supports every learner’s pace, style, and ambition.
        </p>
      </div>

      <div class="rw-cta">
        <a class="btn" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">
          Explore More
        </a>
      </div>
    </div>

    <!-- RIGHT: 2×2 cards -->
    <div class="rw-features">
      <!-- Card 1 -->
      <article class="rw-card rw-card--raised">
        <div class="rw-card__icon">
          <img src="<?= $heroBase ?>light.svg" alt="icon" style="width:100%!important;height:100%!important" onerror="this.style.display='none'">
        </div>
        <h4>AI Tutoring &amp; Smart Learning</h4>
        <p>Learn smarter with personalized AI tutors that adapt to your pace and style.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 2 -->
      <article class="rw-card">
        <div class="rw-card__icon">
          <img src="<?= $heroBase ?>light.svg" alt="" style="width:100%!important;height:100%!important" onerror="this.style.display='none'">
        </div>
        <h4>Quizzes &amp; Lectures</h4>
        <p>Practice with instant-feedback quizzes and access structured lecture content.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 3 -->
      <article class="rw-card rw-card--raised">
        <div class="rw-card__icon">
          <img src="<?= $heroBase ?>light.svg" style="width:100%!important;height:100%!important" onerror="this.style.display='none'">
        </div>
        <h4>Interactive Video Lessons</h4>
        <p>Visual, story-driven lessons that make complex topics easy to understand.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 4 -->
      <article class="rw-card">
        <div class="rw-card__icon">
       <img src="<?= $heroBase ?>light.svg" style="width:100%!important;height:100%!important" onerror="this.style.display='none'">
        </div>
        <h4>Live Exams</h4>
        <p>Experience real-time exam sessions to prepare for the real world with confidence.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('group-classes'); ?>">View Course →</a>
      </article>
    </div>
  </div>
</section>

<?php
// Prepare the data BEFORE rendering the section
if (empty($trendingCourses)) {
    if (!empty($courses) && is_array($courses)) {
        // Fallback to your existing $courses list
        $trendingCourses = array_slice($courses, 0, 6);
    } else {
        $trendingCourses = []; // no data
    }
}
?>
<section class="rwu-trending">
  <div class="wrap">
    <div class="pill">Trending Courses</div>
    <h2 class="title">Perfect <span style="color:#1D9CFD">Online Courses</span> for your career</h2>
    <div class="sub">
      Explore career-focused online courses built by expert tutors and powered by intelligent learning tools.
      Every course integrates video lessons, guided projects, and AI-supported feedback, making learning measurable,
      flexible, and outcome-driven.
    </div>
    <!-- FILTER BAR -->
<div class="rwu-trending__filters" id="courseFilters">
  <!-- <button class="filter is-active" data-level="all">All</button> -->
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="gcse">GCSE</button>
  <!-- <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks1">KS1</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks2">KS2</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks3">KS3</button> -->
</div>


      <div class="grid">
        <?php foreach ($trendingCourses as $idx => $c): ?>
          <?php
            // Try to resolve a course image via your Image controller; fallback to a generic.
            $imgUrl = FatCache::getCachedUrl(
              MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $c['course_id'] ?? 0, Afile::SIZE_MEDIUM]),
              CONF_DEF_CACHE_TIME,
              '.jpg'
            );
            if (empty($imgUrl)) { $imgUrl = CONF_WEBROOT_URL . 'images/defaults/course-4by3.jpg'; }
            // Little helpers (adjust to your data keys if different)
            $teacherName = $c['teacher_name'] ?? 'by Determined-Poitras';
            $title       = $c['course_title'] ?? 'Create an LMS Website with LearnPress';
            $weeks       = $c['duration_weeks'] ?? '2 Weeks';
            $students    = ($c['enrolled'] ?? 156) . ' Students';
            $oldPrice    = $c['old_price'] ?? null;       // e.g., 59.0
            $price       = $c['price']     ?? 0;          // 0 => Free
              $slug      = $c['course_slug'] ?? '';
    $viewLink  = $slug
      ? MyUtility::makeUrl('Courses', 'view', [$slug])
      : MyUtility::makeUrl('Courses'); // fallback to listing if slug missing
          ?>
          <!-- <a class="rwu-course" href="<?= $viewLink ?>"> -->
          <article class="rwu-course <?= $idx === 1 ? 'is-featured' : '' ?>">
            <div class="media">
              <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($title) ?>">
              <div class="chip">Photography</div>
            </div>

            <div class="body">
              <div class="by"><?= htmlspecialchars($teacherName) ?></div>
              <a class="name <?= $idx === 1 ? 'link' : '' ?>" href="<?= $viewLink ?>">
                <?= htmlspecialchars($title) ?>
              </a>
              <div class="meta">
                <span><?= htmlspecialchars($weeks) ?></span>
                <span><?= htmlspecialchars($students) ?></span>
              </div>
              <div class="line"></div>
              <div class="footer">
                <div class="price">
                  <?php if (!empty($oldPrice)): ?>
                    <span class="was">$<?= number_format((float)$oldPrice, 1) ?></span>
                  <?php endif; ?>
                  <?php if ((float)$price > 0): ?>
                    <span class="now-blue">$<?= number_format((float)$price, 1) ?></span>
                  <?php else: ?>
                    <span class="now-green">Free</span>
                  <?php endif; ?>
                </div>
                <a class="more" href="<?= $viewLink ?>">View course</a>
              </div>
            </div>
          </article>
          <!-- </a> -->
        <?php endforeach; ?>
      </div>
    

    <a class="all-btn" href="<?= MyUtility::makeUrl('Courses') ?>">
      All Courses
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M7 4l10 8-10 8V4z"/></svg>
    </a>
  </div>
  <?php
$trendingCourses = isset($courses) && is_array($courses) ? array_slice($courses, 0, 6) : [];
?>
</section>

<section class="rwu-request-tutor">
  <div class="rwu-request-bg">
     <img src="<?= $heroBase ?>Section2.jpg"  alt="Background" />
  </div>

  <div class="rwu-request-content">
    <!-- LEFT: Text block -->
    <div class="rwu-request-left">
      <div class="pill">Tutor Request</div>
      <h1 class="title">
        Request Your Tutor ...<br />
        Get Matched with Qualified & Experienced Teachers
      </h1>
      <p class="desc">
        • Learn Smarter. Get Personalized Tutoring Tailored to Your Goals.<br />
        • Experience personalised lessons supported by smart learning insights.<br />
        • Improve performance through real-time feedback and progress tracking.<br />
        • Learn online, anytime flexible scheduling built around your routine.
      </p>

    <a href="<?= MyUtility::makeUrl('Teachers'); ?>" class="btn-main">
          Our Tutors
        <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
        </svg>
      </a>

      <div class="rwu-stats">
   <div class="stat">
     <img class="icon" src="<?= $heroBase ?>Expert-tutors.svg" alt="Expert Tutors">
     <span class="num">4k+</span>
     <span class="label">Expert Tutors</span>
   </div>
   <div class="stat">
     <img class="icon" src="<?= $heroBase ?>Active-Students.svg" alt="Active Students">
     <span class="num">10k+</span>
     <span class="label">Active Students</span>
   </div>
   <div class="stat">
     <img class="icon" src="<?= $heroBase ?>Subject-Covered.svg" alt="Subjects Covered">
     <span class="num">50+</span>
     <span class="label">Subjects Covered</span>
   </div>
   <div class="stat">
     <img class="icon" src="<?= $heroBase ?>Satisfied-Learners.svg" alt="Satisfied Learners">
     <span class="num">1.5k+</span>
     <span class="label">Satisfied Learners</span>
   </div>
 </div>
      
    </div>

    <!-- RIGHT: Registration Form -->
    <div class="rwu-request-form">
  <form action="<?= MyUtility::makeUrl('TutorRequest','create'); ?>" method="post" onsubmit="submitTutorReq(this); return false;">
    <h3>Fill your Request</h3>
<div id="tutreqAlert" class="rw-alert-slot" aria-live="polite"></div>

    <div class="row">
      <input type="text" name="tutreq_first_name" placeholder="First name *" required />
      <input type="text" name="tutreq_last_name"  placeholder="Last name" />
    </div>

    <div class="row">
      <input type="email" name="tutreq_email" placeholder="Email Address *" required />
    </div>

    <div class="row">
      <select name="tutreq_phone_code" required>
        <option value=""><?= Label::getLabel('LBL_SELECT'); ?></option>
        <?php foreach ($countries as $c): ?>
          <option value="<?= $c['country_id']; ?>">+<?= $c['phone_code']; ?></option>
        <?php endforeach; ?>
      </select>
      <input type="tel" name="tutreq_phone_number" placeholder="Phone *" required />
    </div>

    <!-- MULTI-SELECT COURSES -->
<div class="row">
  <label for="req_courses" class="label">Select Course(s)</label>
  <div class="custom-select" id="courseDropdown">
    <div class="select-trigger">Select Courses </div>
    <div class="select-options">
      <?php if (!empty($courses)): foreach ($courses as $crs): ?>
        <label class="option">
          <input type="checkbox" name="course_ids[]" value="<?= (int)$crs['course_id']; ?>">
          <?= htmlspecialchars($crs['course_title']); ?>
        </label>
      <?php endforeach; endif; ?>
    </div>
  </div>
</div>

<!-- Selected courses chips (auto-filled by JS) -->
<div class="row">
  <div id="selectedCourses" class="selected-courses" aria-live="polite"></div>
</div>

  <div class="row">
  <label for="start_time" class="label">Preferred Time</label>
  <div class="time-selects">
    <select id="start_time" class="time-select">
      <option value="">Start Time</option>
      <?php for ($h = 0; $h < 24; $h++): 
        $time = date('g:i A', strtotime("$h:00"));
      ?>
        <option value="<?= $time ?>"><?= $time ?></option>
      <?php endfor; ?>
    </select>

    <select id="end_time" class="time-select">
      <option value="">End Time</option>
      <?php for ($h = 0; $h < 24; $h++): 
        $time = date('g:i A', strtotime("$h:00"));
      ?>
        <option value="<?= $time ?>"><?= $time ?></option>
      <?php endfor; ?>
    </select>

    <!-- hidden field that backend expects -->
    <input type="hidden" name="tutreq_preferred_time" id="tutreq_preferred_time">
  </div>
</div>

    <label class="terms">
      <input type="checkbox" required />
      I accept the <a href="<?= $termsConditionsLink; ?>" target="_blank">Terms & Conditions</a> 
      and <a href="<?= $privacyPolicyLink; ?>" target="_blank">Privacy Policy</a>
    </label>

    <button type="submit" class="btn-submit">
      Send Request
      <!-- <svg ...>...</svg> -->
    </button>
  </form>
</div>
  </div>
</section>



<section class="rwu-pricing" aria-labelledby="pricing-title">
  <div class="wrap">
    <div class="pill">Our Packages</div>

    <h2 id="pricing-title" class="title">
      Choose the <span style="color:#2DADFF">Best Pakages</span> for your learning
    </h2>

    <p class="sub">
      Explore flexible plans crafted for every learner – from quick revisions to full-length courses, all at affordable rates.
    </p>

    <div class="grid">
      <!-- FREE -->
      <article class="rwu-plan" aria-labelledby="plan-free">
        <!-- Optional decorative art -->
        <img class="rwu-plan__art" src="<?= $heroBase ?? '' ?>price__1.png" alt="" role="presentation" />
        <h3 id="plan-free" class="rwu-plan__name">FREE</h3>

        <div class="rwu-price" aria-label="$0 per month">
          <span class="rwu-price__currency">$</span>
          <span class="rwu-price__amount">0</span>
          <span class="rwu-price__period">/ month</span>
        </div>

        <p class="rwu-plan__tag">Perfect for startup</p>

        <ul class="rwu-list">
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true">
              <!-- check -->
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="rwu-li__text">2 user</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="rwu-li__text">Learning Scope</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="rwu-li__text">Team collaboration</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="rwu-li__text">Export HTML code</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg>
            </span>
            <span class="rwu-li__text">Upload Your Logo</span>
          </li>
        </ul>

        <button class="rwu-plan__cta" type="button" aria-label="Get Started with Free">
          Get Started
        </button>
        <div class="rwu-plan__fine">No credit card required</div>
      </article>

      <!-- BASIC -->
      <article class="rwu-plan" aria-labelledby="plan-basic">
        <img class="rwu-plan__art" src="<?= $heroBase ?? '' ?>price__2.png" alt="" role="presentation" />
        <h3 id="plan-basic" class="rwu-plan__name">BASIC</h3>

        <div class="rwu-price" aria-label="$29 per month">
          <span class="rwu-price__currency">$</span>
          <span class="rwu-price__amount">29</span>
          <span class="rwu-price__period">/ month</span>
        </div>

        <p class="rwu-plan__tag">Perfect for startup</p>

        <ul class="rwu-list">
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">5 user</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Learning Scope</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Team collaboration</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Export HTML code</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Upload Your Logo</span>
          </li>
        </ul>

        <button class="rwu-plan__cta" type="button" aria-label="Get Started with Basic">
          Get Started
        </button>
        <div class="rwu-plan__fine">No credit card required</div>
      </article>

      <!-- PRO -->
      <article class="rwu-plan" aria-labelledby="plan-pro">
        <img class="rwu-plan__art" src="<?= $heroBase ?? '' ?>price__3.png" alt="" role="presentation" />
        <h3 id="plan-pro" class="rwu-plan__name">PRO</h3>

        <div class="rwu-price" aria-label="$59 per month">
          <span class="rwu-price__currency">$</span>
          <span class="rwu-price__amount">59</span>
          <span class="rwu-price__period">/ month</span>
        </div>

        <p class="rwu-plan__tag">Perfect for startup</p>

        <ul class="rwu-list">
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">2 user</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Learning Scope</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Team collaboration</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--muted" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Export HTML code</span>
          </li>
          <li class="rwu-li">
            <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
            <span class="rwu-li__text">Upload Your Logo</span>
          </li>
        </ul>

        <button class="rwu-plan__cta" type="button" aria-label="Get Started with Pro">
          Get Started
        </button>
        <div class="rwu-plan__fine">No credit card required</div>
      </article>
    </div>
  </div>
</section>

  <!-- TESTIMONIALS / WHAT THEY SAY -->
  <section class="rwu-testimonials" aria-label="What they say about us">
    <div class="rwu-t-container">
      <!-- LEFT PANEL -->
      <div class="rwu-t-left">
        <div class="rwu-pill" aria-hidden="true">
          <span>Trusted by Parents</span>
        </div>

        <h2 class="rwu-t-title">
          What they says <br />
          <span class="accent">About us</span>
        </h2>

        <p class="rwu-t-copy">
          At ReadWithUs, we take pride in transforming education through real connections and results.
          Hear from learners and parents who experienced personalized tutoring, interactive lessons,
          and academic growth guided by passionate educators.
        </p>

        <div class="rwu-t-cta">
<a class="rwu-btn" href="<?= MyUtility::makeUrl('Testimonials'); ?>">
  Read All Testimonials →
</a>
        </div>
      </div>

      <!-- RIGHT GRID -->
           <!-- RIGHT GRID -->
    <div class="rwu-t-right">
  <?php if (!empty($homeTestimonials)) { ?>
      <?php foreach ($homeTestimonials as $t) {

          $imgUrl = FatCache::getCachedUrl(
              MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_TESTIMONIAL_IMAGE, $t['testimonial_id'], Afile::SIZE_SMALL]),
              CONF_DEF_CACHE_TIME,
              '.jpg'
          );

          if (empty($imgUrl)) {
              $imgUrl = CONF_WEBROOT_URL . 'images/defaults/user.jpg';
          }
      ?>
          <article class="rwu-quote-card">
            <div class="rwu-quote-badge" aria-hidden="true">
              <svg viewBox="0 0 24 24" class="rwu-quote-icon" aria-hidden="true">
                <path d="M8.9 6C6.7 6 5 7.8 5 10v8h7v-8H9.9c.1-1.1.6-2 1.8-2V6H8.9zm9 0c-2.2 0-3.9 1.8-3.9 4v8H21v-8h-2.1c.1-1.1.6-2 1.8-2V6h-1.8z"/>
              </svg>
            </div>

            <div class="rwu-quote-body">
              “<?= nl2br(FatUtility::decodeHtmlEntities($t['testimonial_text'])) ?>”
            </div>

            <div class="rwu-person">
              <div class="rwu-avatar">
                <img src="<?= $imgUrl; ?>" 
                     alt="<?= htmlspecialchars($t['testimonial_user_name']); ?>" />
              </div>
              <div class="rwu-person-meta">
                <div class="rwu-name">
                  <?= htmlspecialchars($t['testimonial_user_name']); ?>
                </div>
                <div class="rwu-role">
                  <?= htmlspecialchars($t['testimonial_identifier']); ?>
                </div>
              </div>
            </div>
          </article>
      <?php } ?>
  <?php } else { ?>
      <article class="rwu-quote-card">
        <div class="rwu-quote-body">
          <?= Label::getLabel('LBL_NO_TESTIMONIALS_TO_SHOW_YET'); ?>
        </div>
      </article>
  <?php } ?>
</div>


    </div>
  </section>
  <?php
  // Prepare data
  $blogs = is_array($blogPostsList ?? null) ? array_slice($blogPostsList, 0, 3) : [];
?>
<section class="rwu-blogs" aria-labelledby="blogs-title">
  <div class="rwu-blogs__wrap">
    <div class="rwu-blogs__head">
      <div class="rwu-blogs__title">
        <h2 id="blogs-title" class="rb-title">Latest Blogs</h2>
        <p class="rb-sub">Explore our latest blogs</p>
      </div>

      <a class="rb-all" href="<?php echo MyUtility::makeUrl('Blog'); ?>">
        All Blogs
      </a>
    </div>

    <div class="rwu-blogs__grid">
      <?php foreach ($blogs as $post): 
        $postId   = (int)($post['post_id'] ?? 0);
        $title    = trim($post['post_title'] ?? 'Blog');
        $cat      = trim($post['bpcategory_name'] ?? '');
        $img      = FatCache::getCachedUrl(
                      MyUtility::makeFullUrl('Image','show',[Afile::TYPE_BLOG_POST_IMAGE,$postId,Afile::SIZE_MEDIUM]),
                      CONF_DEF_CACHE_TIME,
                      '.jpg'
                    );
        $dateStr  = !empty($post['post_published_on']) 
                    ? MyDate::formatDate($post['post_published_on']) 
                    : '';
        $detailUrl = MyUtility::makeUrl('Blog', 'PostDetail', [$postId]);
        // Optional excerpt (if your list has it); otherwise derive short version from title
        $excerpt = trim(strip_tags($post['post_short_description'] ?? '')) ?: $title;
        if (mb_strlen($excerpt) > 120) { $excerpt = mb_substr($excerpt, 0, 120) . '…'; }
      ?>
      <article class="rb-card">
        <a href="<?php echo $detailUrl; ?>" class="rb-media" aria-label="<?php echo htmlspecialchars($title); ?>">
          <img
            src="<?php echo $img; ?>"
            alt="<?php echo htmlspecialchars($title); ?>"
            loading="lazy"
            onerror="this.src='<?= CONF_WEBROOT_URL ?>images/defaults/blog-4by3.jpg';"
          >
        </a>

        <div class="rb-content">
          <div class="rb-meta">
            <?php if ($cat) { ?><span class="rb-chip"><?php echo htmlspecialchars($cat); ?></span><?php } ?>
            <?php if ($dateStr) { ?>
              <span class="rb-date" aria-label="Published on"><?php echo $dateStr; ?></span>
            <?php } ?>
          </div>

          <h3 class="rb-h4">
            <a href="<?php echo $detailUrl; ?>"><?php echo htmlspecialchars($title); ?></a>
          </h3>

          <p class="rb-excerpt"><?php echo htmlspecialchars($excerpt); ?></p>

          <div class="rb-footer">
            <a class="rb-link" href="<?php echo $detailUrl; ?>">Read More →</a>
          </div>
        </div>
      </article>
      <?php endforeach; ?>

      <?php if (empty($blogs)) { ?>
        <div class="rb-empty">No blogs found. Please check back soon.</div>
      <?php } ?>
    </div>
  </div>
</section>
<section class="rwu-cta" aria-labelledby="rwu-cta-title">
  <div class="rwu-cta__wrap">
    <!-- Left: Icon -->
    <div class="rwu-cta__icon" aria-hidden="true">
      <!-- Simple graduation-cap icon (inline SVG so no external load) -->
      <svg viewBox="0 0 24 24" class="cap">
        <path d="M12 3L1 9l11 6 9-4.909V17h2V9L12 3zM7 12.5v3c0 .828 2.686 1.5 6 1.5s6-.672 6-1.5v-3l-6 3-6-3z"/>
      </svg>
    </div>

    <!-- Middle: Title -->
    <div class="rwu-cta__title">
      <h3 id="rwu-cta-title">Let’s Start With Readwithus</h3>
    </div>

    <!-- Right: Actions -->
    <div class="rwu-cta__actions">
      <a class="btn-pill btn--ghost" href="<?php echo getBaseUrl(); ?>/teachers">
        I’m A Student
      </a>
      <a class="btn-pill btn--primary" href="<?php echo getBaseUrl(); ?>/apply-to-teach">
        Become An Instructor
      </a>
    </div>
  </div>
</section>



<!-- somewhere in your base layout before home.js -->
<script src="<?= CONF_WEBROOT_URL ?>assets/js/bootstrap.bundle.min.js"></script>

<script>
LANGUAGES = <?php echo json_encode($teachLangs); ?>;
$(".faq__trigger-js").click(function(e) {
    e.preventDefault();
    if ($(this).parents('.faq-group-js').hasClass('is-active')) {
        $(this).siblings('.faq__target-js').slideUp();
        $('.faq-group-js').removeClass('is-active');
    } else {
        $('.faq-group-js').removeClass('is-active');
        $(this).parents('.faq-group-js').addClass('is-active');
        $('.faq__target-js').slideUp();
        $(this).siblings('.faq__target-js').slideDown(0);
    }
    var height = $(this).siblings('.faq__target-js').children('iframe').contents().height() + 40;
    $(this).siblings('.faq__target-js').css('height', height + 'px');
});
</script>  


<script>
  window.RWU_CONFIG = {
    baseUrl: <?= json_encode(getBaseUrl()) ?>  
  };
</script>

<!-- then include your JS files -->
<script src="<?= CONF_WEBROOT_URL ?>js/home.js"></script>
<!-- <select name="course_ids[]" id="req_courses" multiple class="multi" required></select> -->
<script>
// Global so onsubmit="submitTutorReq(this)" still works
window.submitTutorReq = async function(form) {
  const alertSlot = form.querySelector('#tutreqAlert');
  const submitBtn = form.querySelector('.btn-submit');
  const emailVal = form.tutreq_email?.value || '';
  const coursesEl = form.querySelector('#req_courses');

  // Helper to show alert
  const showAlert = (type, html) => {
    alertSlot.innerHTML = `
      <div class="rw-alert rw-alert--${type}" role="alert">
        ${html}
        <button type="button" class="rw-alert__close" aria-label="Close" onclick="this.parentNode.remove()">×</button>
      </div>`;
  };

  try {
    // UI: loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = 'Sending… <span class="spinner"></span>';

    // Build FormData
    const formData = new FormData(form);

    // Handle course selection when using <select multiple>
    if (coursesEl && coursesEl.selectedOptions) {
      const selectedCourses = Array.from(coursesEl.selectedOptions).map(option => option.value);
      formData.delete('course_ids[]');
      selectedCourses.forEach(courseId => formData.append('course_ids[]', courseId));
    }

    console.log('Submitting tutor request...');

    const response = await fetch(form.action, {
      method: 'POST',
      body: formData,
      credentials: 'same-origin'
    });

    console.log('Response status:', response.status);

    let result;
    try {
      const responseText = await response.text();
      console.log('Raw response:', responseText);
      result = JSON.parse(responseText);
      console.log('Parsed result:', result);
    } catch (parseError) {
      console.error('JSON parse error:', parseError);
      showAlert('error', 'Invalid response from server. Please try again.');
      return;
    }

    // Handle response
    if (result.status === 1) {
      showAlert(
        'success',
        ` ${result.msg || 'Request submitted successfully!'} Our team will contact you at <strong>${emailVal}</strong>.`
      );
      form.reset();

      // Clear course chips
      const chipsContainer = document.getElementById('selectedCourses');
      if (chipsContainer) {
        chipsContainer.innerHTML = '';
      }

      // Clear course selections if using <select multiple>
      if (coursesEl) {
        Array.from(coursesEl.options).forEach(option => {
          option.selected = false;
        });
      }

      // Scroll to show success message
      alertSlot.scrollIntoView({ behavior: 'smooth', block: 'center' });
    } else {
      showAlert('error', result.msg || 'Something went wrong. Please try again.');
      alertSlot.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

  } catch (error) {
    console.error('Request failed:', error);
    showAlert('error', 'Network error. Please check your connection and try again.');
  } finally {
    // Restore button state
    submitBtn.disabled = false;
    submitBtn.innerHTML =
      'Send Request <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>';
  }
};

document.addEventListener('DOMContentLoaded', () => {
  /* =========================================
   * 1) Fetch courses into #req_courses (if present)
   * ========================================= */
  (async function(){
    try {
      const res = await fetch('<?= MyUtility::makeUrl('TutorRequest','courses'); ?>', {
        credentials: 'same-origin'
      });
      const json = await res.json();
      if (json.status == 1 && Array.isArray(json.data.courses)) {
        const sel = document.getElementById('req_courses');
        if (sel) {
          json.data.courses.forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.course_id;
            opt.textContent = c.course_title;
            sel.appendChild(opt);
          });
        }
      }
    } catch(e){
      console.error('Error loading courses:', e);
    }
  })();

  /* =========================================
   * 2) Multi-select chips for <select id="req_courses" multiple>
   * ========================================= */
  (function(){
    const sel  = document.getElementById('req_courses');
    const list = document.getElementById('selectedCourses');
    if(!sel || !list) return;

    // Make <select multiple> behave like tag toggles
    sel.addEventListener('mousedown', function(e){
      const opt = e.target;
      if (opt && opt.tagName === 'OPTION') {
        e.preventDefault();                 // stop native selection behavior
        opt.selected = !opt.selected;       // toggle
        sel.focus();
        sel.dispatchEvent(new Event('change', { bubbles: true }));
      }
    });

    function renderChips(){
      list.innerHTML = '';
      const selected = Array.from(sel.options).filter(o => o.selected);
      selected.forEach(o => {
        const chip = document.createElement('div');
        chip.className = 'course-chip';
        chip.dataset.value = o.value;
        chip.innerHTML = `
          <span class="label">${o.textContent}</span>
          <button type="button" class="remove" aria-label="Remove ${o.textContent}">×</button>
        `;
        chip.querySelector('.remove').addEventListener('click', () => {
          o.selected = false;
          sel.dispatchEvent(new Event('change', { bubbles: true }));
        });
        list.appendChild(chip);
      });
    }

    sel.addEventListener('change', renderChips);
    renderChips();
  })();

  /* =========================================
   * 3) Custom course dropdown + preferred time sync
   *    (for your new UI)
   * ========================================= */
  // Course dropdown toggle (for .select-trigger / .select-options UI)
  const trigger = document.querySelector('.select-trigger');
  const options = document.querySelector('.select-options');

  if (trigger && options) {
    trigger.addEventListener('click', () => {
      options.style.display = options.style.display === 'block' ? 'none' : 'block';
    });

    // Update trigger text dynamically when checkboxes change
    options.addEventListener('change', () => {
      const selected = Array.from(
        options.querySelectorAll('input[type="checkbox"]:checked')
      ).map(opt => opt.parentNode.textContent.trim());

      trigger.textContent = selected.length
        ? selected.join(', ')
        : 'Select Courses ▼';
    });

    // Close when clicking outside
    document.addEventListener('click', e => {
      if (!e.target.closest('#courseDropdown')) {
        options.style.display = 'none';
      }
    });
  }

  // Time field synchronization (start_time + end_time → tutreq_preferred_time)
  const startSelect = document.getElementById('start_time');
  const endSelect = document.getElementById('end_time');
  const hiddenTime = document.getElementById('tutreq_preferred_time');

  function updatePreferredTime() {
    if (!hiddenTime || !startSelect || !endSelect) return;
    const start = startSelect.value;
    const end = endSelect.value;
    hiddenTime.value = start && end ? `${start} - ${end}` : '';
  }

  if (startSelect && endSelect && hiddenTime) {
    startSelect.addEventListener('change', updatePreferredTime);
    endSelect.addEventListener('change', updatePreferredTime);
  }
});

</script>
