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
        <button id="openSelector" class="rw-pill" type="button" data-bs-toggle="modal" data-bs-target="#reviseTopicModal">
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
<svg class="rw-connectors" viewBox="0 0 800 800" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
  <!-- top-left to girl -->
  <path d="M100,140 C760,80 300,60 430,200" stroke="#ECECEC" stroke-width="2" fill="none"/>
  <!-- left-middle to girl -->
  <path d="M80,380 C220,400 360,360 460,420" stroke="#ECECEC" stroke-width="2" fill="none"/>
  <!-- bottom-right to girl -->
  <path d="M540,520 C620,580 700,620 760,700" stroke="#ECECEC" stroke-width="2" fill="none"/>
</svg>

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
          <img src="<?php echo $secBase; ?>icon-analytics.png" alt="" style="width:36px;height:36px" onerror="this.style.display='none'">
        </div>
        <h4>AI Tutoring &amp; Smart Learning</h4>
        <p>Learn smarter with personalized AI tutors that adapt to your pace and style.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 2 -->
      <article class="rw-card">
        <div class="rw-card__icon">
          <img src="<?php echo $secBase; ?>icon-quiz.png" alt="" style="width:36px;height:36px" onerror="this.style.display='none'">
        </div>
        <h4>Quizzes &amp; Lectures</h4>
        <p>Practice with instant-feedback quizzes and access structured lecture content.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 3 -->
      <article class="rw-card rw-card--raised">
        <div class="rw-card__icon">
          <img src="<?php echo $secBase; ?>icon-video.png" alt="" style="width:36px;height:36px" onerror="this.style.display='none'">
        </div>
        <h4>Interactive Video Lessons</h4>
        <p>Visual, story-driven lessons that make complex topics easy to understand.</p>
        <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
      </article>

      <!-- Card 4 -->
      <article class="rw-card">
        <div class="rw-card__icon">
          <img src="<?php echo $secBase; ?>icon-live.png" alt="" style="width:36px;height:36px" onerror="this.style.display='none'">
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
  <button class="filter is-active" data-level="all">All</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="gcse">GCSE</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks1">KS1</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks2">KS2</button>
  <span class="sep" aria-hidden="true"></span>
  <button class="filter" data-level="ks3">KS3</button>
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
            $viewLink    = MyUtility::makeUrl('Courses'); // safe: listing link (replace with detail if you have it)
          ?>
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
                <a class="more" href="<?= $viewLink ?>">View more</a>
              </div>
            </div>
          </article>
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

      <a href="#" class="btn-main">
        See Our Tutors
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
      <form>
        <h3>Fill your Request</h3>
        <input type="text" placeholder="Your Name" />
        <div class="row">
          <input type="email" placeholder="Email Address" />
          <input type="tel" placeholder="Phone" />
        </div>
        <input type="text" placeholder="course list" />
        <textarea placeholder="Preffered time"></textarea>
        <button type="submit" class="btn-submit">
          Sign Up
          <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
          </svg>
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
          <a class="rwu-btn" href="#all-testimonials">Read All Testimonials →</a>
        </div>
      </div>

      <!-- RIGHT GRID -->
      <div class="rwu-t-right">
        <!-- CARD 1 -->
        <article class="rwu-quote-card">
          <div class="rwu-quote-badge" aria-hidden="true">
            <!-- quotation icon -->
            <svg viewBox="0 0 24 24" class="rwu-quote-icon" aria-hidden="true">
              <path d="M8.9 6C6.7 6 5 7.8 5 10v8h7v-8H9.9c.1-1.1.6-2 1.8-2V6H8.9zm9 0c-2.2 0-3.9 1.8-3.9 4v8H21v-8h-2.1c.1-1.1.6-2 1.8-2V6h-1.8z"/>
            </svg>
          </div>

          <div class="rwu-quote-body">
            “The tutors at ReadWithUs are amazing! My mentor helped me understand tough topics in a way
            that finally made sense. The progress tracking and regular feedback made me feel supported
            every step of the way.”
          </div>

          <div class="rwu-person">
            <div class="rwu-avatar">
              <img src="https://via.placeholder.com/80" alt="Photo of Robind Jon" />
            </div>
            <div class="rwu-person-meta">
              <div class="rwu-name">Robind Jon</div>
              <div class="rwu-role">Designer TechBoot</div>
            </div>
          </div>
        </article>

        <!-- CARD 2 -->
        <article class="rwu-quote-card">
          <div class="rwu-quote-badge" aria-hidden="true">
            <svg viewBox="0 0 24 24" class="rwu-quote-icon" aria-hidden="true">
              <path d="M8.9 6C6.7 6 5 7.8 5 10v8h7v-8H9.9c.1-1.1.6-2 1.8-2V6H8.9zm9 0c-2.2 0-3.9 1.8-3.9 4v8H21v-8h-2.1c.1-1.1.6-2 1.8-2V6h-1.8z"/>
            </svg>
          </div>

          <div class="rwu-quote-body">
            “I’m truly impressed by how quickly ReadWithUs connected my daughter with the perfect tutor.
            The human approach combined with progress reports gives us confidence that she’s in great hands.”
          </div>

          <div class="rwu-person">
            <div class="rwu-avatar">
              <img src="https://via.placeholder.com/80" alt="Photo of Robind Jon" />
            </div>
            <div class="rwu-person-meta">
              <div class="rwu-name">Robind Jon</div>
              <div class="rwu-role">Designer TechBoot</div>
            </div>
          </div>
        </article>
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