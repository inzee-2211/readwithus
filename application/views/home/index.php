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
<link
  rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
  crossorigin="anonymous"
  referrerpolicy="no-referrer"
/>

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
  Welcome to ReadWithUs.org.uk, the UK's most adaptive platform for students of all ages.
  Access expert human tutoring, interactive video lessons, AI-assisted practice quizzes,
  and live exam preparation — all in one place.
</p>


      <!-- Search rail -->
      <div class="rw-hero__search">
      <button id="openSelector" class="rw-pill" type="button">

          <span>Revise Topic</span>
          <!-- <img src="<?php echo getBaseUrl(); ?>/assets/img/arrow-2.svg" class="arrow" alt=""> -->
        </button>

        <!-- <input id="rwCourseQuery" class="rw-input" type="text" placeholder="Class/Course e.g. GCSE Maths, Algebra, English…"/> -->
        <button id="rwSearchBtn" class="rw-searchbtn">Explore Courses</button>
      </div>
    </div>

    <!-- RIGHT: Art -->
    <div class="rw-hero__art">
      <div class="rw-hero__art-inner">
<img class="rw-hero__img"
     src="<?= $heroBase ?>hero.png"
     alt="Student studying online with a laptop"
     loading="eager"
     fetchpriority="high"
     decoding="async">

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
  Personalised learning with expert tutors, interactive lessons and measurable progress.
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
  <!-- Card 1: AI Tutoring & Smart Learning -->
  <article class="rw-card rw-card--raised">
    <div class="rw-card__icon">
      <i class="fa-solid fa-robot" aria-hidden="true"></i>
    </div>
    <h4>AI Tutoring &amp; Smart Learning</h4>
    <p>Learn smarter with personalized AI tutors that adapt to your pace and style.</p>
    <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
  </article>

  <!-- Card 2: Quizzes & Lectures -->
  <article class="rw-card">
    <div class="rw-card__icon">
      <i class="fa-solid fa-clipboard-check" aria-hidden="true"></i>
    </div>
    <h4>Quizzes &amp; Lectures</h4>
    <p>Practice with instant-feedback quizzes and access structured lecture content.</p>
    <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
  </article>

  <!-- Card 3: Interactive Video Lessons -->
  <article class="rw-card rw-card--raised">
    <div class="rw-card__icon">
      <i class="fa-solid fa-chalkboard-user" aria-hidden="true"></i>
    </div>
    <h4>Interactive Video Lessons</h4>
    <p>Visual, story-driven lessons that make complex topics easy to understand.</p>
    <a class="rw-link" href="<?php echo MyUtility::makeFullUrl('courses'); ?>">View Course →</a>
  </article>

  <!-- Card 4: Live Exams -->
  <article class="rw-card">
    <div class="rw-card__icon">
      <i class="fa-solid fa-file-circle-check" aria-hidden="true"></i>
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
    <div class="sub" style=" margin:auto; margin-bottom:32px;">
      Explore career-focused online courses built by expert tutors and powered by intelligent learning tools.
      Every course integrates video lessons, guided projects, and AI-supported feedback, making learning measurable,
      flexible, and outcome-driven.
    </div>
    <!-- FILTER BAR -->
<div class="rwu-trending__filters" id="courseFilters">
  <button class="filter is-active" data-level-id="all">
    All
  </button>

  <?php if (!empty($levels)): ?>
    <?php foreach ($levels as $levelId => $levelName): ?>
      <span class="sep" aria-hidden="true"></span>
      <button
        class="filter"
        data-level-id="<?= (int)$levelId; ?>"
        type="button"
      >
        <?= htmlspecialchars($levelName); ?>
      </button>
    <?php endforeach; ?>
  <?php endif; ?>
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
          
            $title       = $c['course_title'] ?? 'Create an LMS Website with LearnPress';
        
            $oldPrice    = $c['old_price'] ?? null;       // e.g., 59.0
            $price       = $c['price']     ?? 0;          // 0 => Free
              $slug      = $c['course_slug'] ?? '';
    $viewLink  = $slug
      ? MyUtility::makeUrl('Courses', 'view', [$slug])
      : MyUtility::makeUrl('Courses'); // fallback to listing if slug missing
          ?>
          <!-- <a class="rwu-course" href="<?= $viewLink ?>"> -->
<?php $courseLevelId = (int)($c['course_level'] ?? 0); ?>
<article
  class="rwu-course <?= $idx === 1 ? 'is-featured' : '' ?>"
  data-level-id="<?= $courseLevelId; ?>"
>
            <div class="media">
              <img src="<?= $imgUrl ?>" alt="<?= htmlspecialchars($title) ?>">
              <!-- <div class="chip">Photography</div> -->
            </div>

            <div class="body">
              <!-- <div class="by"><?= htmlspecialchars($teacherName) ?></div> -->
              <a class="name <?= $idx === 1 ? 'link' : '' ?>" href="<?= $viewLink ?>">
                <?= htmlspecialchars($title) ?>
              </a>
              <div class="meta">
                <span>AI Assissted Learning</span>
                <!-- <span>Multiple Assessments</span> -->
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
  <a class="now-green"
     href="<?= MyUtility::makeUrl('Pricing', 'index'); ?>">
     Subscribe to unlock
  </a>
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
<img src="<?= $heroBase ?>Section2.jpg" alt="" role="presentation" loading="lazy" decoding="async" />
  </div>

  <div class="rwu-request-content">
    <!-- LEFT: Text block -->
    <div class="rwu-request-left">
      <div class="pill">Tutor Request</div>
      <h2 class="title">
        Request Your Tutor ...<br />
        Get Matched with Qualified & Experienced Teachers
      </h2>
      <p class="desc">
        • Learn Smarter. Get Personalized Tutoring Tailored to Your Goals.<br />
        • Experience personalised lessons supported by smart learning insights.<br />
        • Improve performance through real-time feedback and progress tracking.<br />
        • Learn online, anytime, with flexible scheduling built around your routine.
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
<!-- LEVEL -->
<div class="row row--inline-label">
  <label for="tutreq_level_id" class="label">Level *</label>
  <select name="tutreq_level_id" id="tutreq_level_id" required>
    <option value=""><?= Label::getLabel('LBL_SELECT_LEVEL'); ?></option>
    <?php if (!empty($levels)): ?>
      <?php foreach ($levels as $levelId => $levelName): ?>
        <option value="<?= (int)$levelId; ?>">
          <?= htmlspecialchars($levelName); ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</div>

<!-- SUBJECT -->
<div class="row row--inline-label">
  <label for="tutreq_subject_id" class="label">Subject *</label>
  <select name="tutreq_subject_id" id="tutreq_subject_id" required disabled>
    <option value=""><?= Label::getLabel('LBL_SELECT_SUBJECT'); ?></option>
  </select>
</div>

<!-- EXAM BOARD -->
<div class="row row--inline-label">
  <label for="tutreq_examboard_id" class="label">Exam Board</label>
  <select name="tutreq_examboard_id" id="tutreq_examboard_id">
    <option value=""><?= Label::getLabel('LBL_SELECT_EXAM_BOARD'); ?></option>
    <?php if (!empty($examBoards)): ?>
      <?php foreach ($examBoards as $eb): ?>
        <option value="<?= (int)$eb['id']; ?>">
          <?= htmlspecialchars($eb['name']); ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
</div>

<!-- TIER -->
<div class="row row--inline-label">
  <label for="tutreq_tier_id" class="label">Tier</label>
  <select name="tutreq_tier_id" id="tutreq_tier_id">
    <option value=""><?= Label::getLabel('LBL_SELECT_TIER'); ?></option>
    <?php if (!empty($tiers)): ?>
      <?php foreach ($tiers as $tr): ?>
        <option value="<?= (int)$tr['id']; ?>">
          <?= htmlspecialchars($tr['name']); ?>
        </option>
      <?php endforeach; ?>
    <?php endif; ?>
  </select>
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



<?php
// --- Pricing data wiring (same idea as pricing/index.php) ---

$symbolLeft  = $siteCurrency['currency_symbol_left'] ?? '£';
$symbolRight = $siteCurrency['currency_symbol_right'] ?? '';

$plans             = $plans ?? [];
$hasActiveSubscription = $hasActiveSubscription ?? false;
$currentPackageId      = $currentPackageId ?? 0;
$userDetail = $userDetail ?? [];


// Base path for decorative images used in home hero cards
// (adjust if your assets live somewhere else)
$heroBase = $heroBase ?? CONF_WEBROOT_URL . 'images/home/';

// Normalize $plans:
// If controller passed DB rows (spackage_*), map them to card shape.
if (!empty($plans) && is_array($plans)) {
    foreach ($plans as $idx => $p) {
        if (isset($p['spackage_id'])) {
            $plans[$idx] = [
                'id'          => (int)$p['spackage_id'],
                'name'        => (string)$p['spackage_name'],
                'tag'         => (string)($p['spackage_description'] ?? ''),
                'price_month' => (float)$p['spackage_price_monthly'],
                'price_year'  => (float)$p['spackage_price_yearly'],
                'trial_days'  => isset($p['spackage_trial_days']) ? (int)$p['spackage_trial_days'] : 0,
                'features'    => [
                    'Access to ' . (int)$p['spackage_subject_limit'] . ' subjects',
                    'Unlimited courses in selected subjects',
                    'Email/priority support',
                ],
            ];
        } else {
            // seeded / static data safety
            $plans[$idx]['id']          = $plans[$idx]['id']          ?? ($idx+1);
            $plans[$idx]['name']        = $plans[$idx]['name']        ?? ('Plan '.($idx+1));
            $plans[$idx]['tag']         = $plans[$idx]['tag']         ?? '';
            $plans[$idx]['price_month'] = (float)($plans[$idx]['price_month'] ?? 0);
            $plans[$idx]['price_year']  = (float)($plans[$idx]['price_year'] ?? ($plans[$idx]['price_month']*12));
            $plans[$idx]['features']    = $plans[$idx]['features']    ?? [];
            $plans[$idx]['trial_days']  = (int)($plans[$idx]['trial_days'] ?? 0);
        }
    }

    // Add checkout URL (monthly only, home page is a simple entry point)
    foreach ($plans as $idx => $p) {
        $plans[$idx]['cta_month_url'] = MyUtility::makeUrl(
            'Subscription',
            'selectSubjects',
            [ (int)$p['id'], 'monthly' ]
        );
    }
}

?>

<section class="rwu-pricing" aria-labelledby="pricing-title">
  <div class="wrap">
    <div class="pill">Our Packages</div>

    <h2 id="pricing-title" class="title">
  Choose the <span style="color:#2DADFF">Best Packages</span> for your learning
</h2>

    <p class="sub">
      Explore flexible plans crafted for every learner – from quick revisions to full-length courses, all at affordable rates.
    </p>

    <?php if (!empty($levels)): ?>
        <div class="level-filter">
    <label for="levelSelect">Select your level</label>
      <div class="level-tabs">
        <?php foreach ($levels as $levelId => $levelName): ?>
          <?php
            $active = ((int)$selectedLevelId === (int)$levelId) ? 'active' : '';
            // home URL with level_id param
            $url = MyUtility::makeUrl('Home') . '?level_id=' . (int)$levelId;
          ?>
   <a href="<?= $url; ?>"
   class="level-tab <?= $active; ?>"
   data-level-id="<?= (int)$levelId; ?>">
  <?= htmlspecialchars($levelName); ?>
</a>


        <?php endforeach; ?>
      </div>
      </div>
    <?php endif; ?>


    <div class="grid">
      <?php if (!empty($plans)): ?>
        <?php foreach ($plans as $idx => $p): ?>
          <?php
            // Pick decorative image by index (1,2,3...) – fallback safe
            $artIndex = $idx + 1;
            $artSrc   = $heroBase . 'price__' . $artIndex . '.png';

            $planId   = (int)$p['id'];
            $price    = (float)$p['price_month'];
            $name     = (string)$p['name'];
            $tag      = (string)($p['tag'] ?? '');
            $features = $p['features'] ?? [];

            // CTA defaults
                        // CTA defaults
            $ctaHref  = htmlspecialchars($p['cta_month_url'] ?? '#');
            $ctaLabel = 'Get Started';
            $fineText = 'No contracts. Cancel anytime.';

            $hasSub    = !empty($hasActiveSubscription);
            $isCurrent = $hasSub && ($planId === (int)$currentPackageId);

            // Trial context (same logic as pricing page, but simpler)
            $isLogged          = UserAuth::isUserLogged();
            $userTrialEligible = !empty($userDetail['user_trial_eligible'] ?? 0);
            $trialDays         = (int)($p['trial_days'] ?? 0);

            $canStartTrial = $isLogged && !$hasSub && $trialDays > 0 && $userTrialEligible;

            if ($isCurrent) {
                $ctaHref  = MyUtility::makeUrl('Courses');
                $ctaLabel = 'Go to my courses';
                $fineText = 'You’re currently on this plan.';
            } elseif ($hasSub) {
                // user has some other plan – keep same URL but change label if you like
                $ctaLabel = 'Change plan';
                $fineText = 'You can switch plans anytime from your account.';
            } elseif ($canStartTrial) {
                // show trial text, keep same monthly selectSubjects flow
                $ctaLabel = sprintf('Start %d-day free trial', $trialDays);
                $fineText = 'Your card will be charged after the trial ends unless you cancel.';
            }

          ?>

          <article class="rwu-plan" aria-labelledby="plan-<?= $planId; ?>">
            <img class="rwu-plan__art"
                 src="<?= $artSrc; ?>"
                 alt=""
                 role="presentation" />

            <h3 id="plan-<?= $planId; ?>" class="rwu-plan__name">
              <?= htmlspecialchars($name); ?>
            </h3>

            <div class="rwu-price" aria-label="<?= $symbolLeft . $price ?> per month">
              <span class="rwu-price__currency"><?= $symbolLeft; ?></span>
              <span class="rwu-price__amount">
                <?= number_format($price, 0); ?>
              </span>
              <span class="rwu-price__period">/ month</span>
            </div>

            <?php if ($tag !== ''): ?>
              <p class="rwu-plan__tag"><?= htmlspecialchars($tag); ?></p>
            <?php else: ?>
              <p class="rwu-plan__tag">Perfect for focused study</p>
            <?php endif; ?>

            <ul class="rwu-list">
              <?php foreach ($features as $feat): ?>
                <li class="rwu-li">
                  <span class="rwu-bullet rwu-bullet--ok" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                      <path d="M20 6L9 17l-5-5"
                            stroke="currentColor"
                            stroke-width="3"
                            stroke-linecap="round"
                            stroke-linejoin="round" />
                    </svg>
                  </span>
                  <span class="rwu-li__text"><?= htmlspecialchars($feat); ?></span>
                </li>
              <?php endforeach; ?>
            </ul>

            <a class="rwu-plan__cta"
               href="<?= $ctaHref; ?>"
               aria-label="<?= $ctaLabel; ?> for <?= htmlspecialchars($name); ?>">
              <?= $ctaLabel; ?>
            </a>

            <div class="rwu-plan__fine"><?= $fineText; ?></div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <!-- Fallback if no plans yet -->
        <p style="text-align:center; width:100%; margin-top:24px;">
          Subscription plans are being updated. Please check back shortly.
        </p>
      <?php endif; ?>
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
          What they say <br />
          <span class="accent">About us</span>
        </h2>

        <p class="rwu-t-copy">
          At Read With Us, we take pride in transforming education through real connections and results.
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
      <h3 id="rwu-cta-title">Let’s Start With Read With Us</h3>
    </div>

    <!-- Right: Actions -->
    <div class="rwu-cta__actions">
      <a class="btn-pill btn--ghost" href="<?php echo getBaseUrl(); ?>/teachers">
        I’m A Student
      </a>
      <a class="btn-pill btn--primary" href="<?php echo getBaseUrl(); ?>/apply-to-teach">
        Become an Instructor
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
<script>
document.getElementById("rwSearchBtn").addEventListener("click", function () {
    window.location.href = "<?php echo MyUtility::makeUrl('Courses'); ?>";
});
</script>
<script>
(function() {
  var levelSelect   = document.getElementById('tutreq_level_id');
  var subjectSelect = document.getElementById('tutreq_subject_id');

  if (!levelSelect || !subjectSelect) return;

  levelSelect.addEventListener('change', function() {
    var levelId = this.value;

    // reset subject select
    subjectSelect.innerHTML = '<option value=""><?= addslashes(Label::getLabel('LBL_SELECT_SUBJECT')); ?></option>';
    subjectSelect.disabled = true;

    if (!levelId) {
      return;
    }

    // show loading info
    var loadingOpt = document.createElement('option');
    loadingOpt.value = '';
    loadingOpt.textContent = 'Loading subjects...';
    subjectSelect.appendChild(loadingOpt);

    fetch('<?= MyUtility::makeUrl('Home', 'getsubjectsforlevel'); ?>', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
        'X-REQUESTED-WITH': 'XMLHttpRequest'
      },
      body: 'levelId=' + encodeURIComponent(levelId)
    })
    .then(function(res) { return res.json(); })
    .then(function(json) {
      subjectSelect.innerHTML = '<option value=""><?= addslashes(Label::getLabel('LBL_SELECT_SUBJECT')); ?></option>';

      if (!json || json.status !== 1 || !Array.isArray(json.data)) {
        var opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'No subjects found';
        subjectSelect.appendChild(opt);
        return;
      }

      json.data.forEach(function(subj) {
        var opt = document.createElement('option');
        opt.value = subj.id;
        opt.textContent = subj.name;
        subjectSelect.appendChild(opt);
      });

      subjectSelect.disabled = false;
    })
    .catch(function(err) {
      console.error(err);
      subjectSelect.innerHTML = '<option value="">Error loading subjects</option>';
    });
  });
})();
</script>
<script>
  document.addEventListener('DOMContentLoaded', function () {
  const filterBar = document.getElementById('courseFilters');
  if (!filterBar) return;

  const buttons = filterBar.querySelectorAll('.filter');
  const cards = document.querySelectorAll('.rwu-course');

  buttons.forEach(btn => {
    btn.addEventListener('click', function () {
      const selectedLevel = this.dataset.levelId; // "all" or numeric

      // Active state
      buttons.forEach(b => b.classList.remove('is-active'));
      this.classList.add('is-active');

      // Show/hide cards
      cards.forEach(card => {
        const cardLevel = card.dataset.levelId;
        if (!selectedLevel || selectedLevel === 'all' || cardLevel === selectedLevel) {
          card.style.display = '';
        } else {
          card.style.display = 'none';
        }
      });
    });
  });
});

</script>
