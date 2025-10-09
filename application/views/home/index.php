<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<style>
 input,select{font-size:16px;cursor:pointer}.containernew{display:flex;gap:15px;align-items:center;justify-content:center;padding:20px;background-color:#f4f4f4;border-radius:8px}input,select{border:1px solid #ed6852;border-radius:6px;background-color:#fff;color:#333;transition:.3s}input:hover,select:hover{border-color:#ed6852}input:focus,select:focus{outline:0;box-shadow:0 0 5px rgba(0,123,255,.5)}.menu-item{border-radius:5px;transition:.3s}.menu-item:hover{background:#e3e3e3}select{width:100%;border:1px solid #ed6852;border-radius:6px;background:url('data:image/svg+xml;utf8,<svg fill="%23333" height="16" viewBox="0 0 24 24" width="16" xmlns="http://www.w3.org/2000/svg"><path d="M7 10l5 5 5-5z"/></svg>') right 10px center/16px no-repeat #fff;appearance:none;-webkit-appearance:none;-moz-appearance:none;text-align:center}.menu,.submenu{background:#fff;box-shadow:0 4px 8px rgba(0,0,0,.2);border-radius:5px}.menu-item:hover,select:hover{border-color:#ed6852;background-color:#fafafa}.menu{display:none;position:absolute;top:100%;left:0;width:220px;padding:5px;z-index:1000}.menu-item{padding:12px 16px;cursor:pointer;font-size:15px;position:relative;display:flex;justify-content:space-between;align-items:center}.menu-item::after{content:'▶';font-size:12px;color:#999;transition:transform .3s}.menu-item:hover::after{transform:rotate(90deg);color:#ed6852}.submenu{display:none;position:absolute;left:100%;top:0;width:200px;padding:10px 0}.menu-item:hover>.submenu{display:block}.popup-overlay{position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.6);display:flex;justify-content:center;align-items:center;z-index:1000}.popup-content{background:#fff;padding:25px;border-radius:12px;text-align:center;box-shadow:0 4px 10px rgba(0,0,0,.2);width:300px;max-width:90%;animation:.3s ease-in-out fadeIn}.start-button,.start-button:hover{background:#3589c2}.popup-content h2{margin:0 0 10px;font-size:22px;color:#333}.popup-content p{font-size:16px;color:#666;margin-bottom:20px}.button-container{display:flex;justify-content:space-between;gap:10px}.popup-content button{padding:10px 18px;border:none;font-size:14px;cursor:pointer;border-radius:6px;transition:.2s ease-in-out}.start-button{color:#fff}.cancel-button{background:#f44336;color:#fff}.cancel-button:hover{background:#d32f2f}@keyframes fadeIn{from{opacity:0;transform:translateY(-10px)}to{opacity:1;transform:translateY(0)}}


.back-btn  
{
    margin-top:3px;
    margin-bottom:3px;
}

  .subtopic-btn
{
    margin-top:3px;
    margin-bottom:3px;
}
</style>

<div class="hero-section">

   <div class="floating-background">
    <div class="float-item">📚</div> <!-- Book -->
    <div class="float-item">📐</div> <!-- Geometry set -->
    <div class="float-item">🔬</div> <!-- Microscope -->
    <div class="float-item">🧮</div> <!-- Abacus -->
    <div class="float-item">📝</div> <!-- Notebook -->
    <div class="float-item">📊</div> <!-- Bar chart -->
    <div class="float-item">📏</div> <!-- Ruler -->
    <div class="float-item">➗</div> <!-- Division -->
    <div class="float-item">➕</div> <!-- Plus -->
    <div class="float-item">➖</div> <!-- Minus -->
    <div class="float-item">✖️</div> <!-- Multiplication -->
    <div class="float-item">📎</div> <!-- Paperclip -->
    <div class="float-item">📅</div> <!-- Calendar -->
    <div class="float-item">📓</div> <!-- Closed notebook -->
    <div class="float-item">🎓</div> <!-- Graduation cap -->
  </div>
  <p class="tagline">🎓 Learn. Practice. Achieve.</p>
  <h1 class="main-heading">
    Your All-in-One Online <span>Learning Platform</span>
  </h1>
  <p class="description ft-gothic">
    Join interactive <b class="ft-book">courses</b>, connect with expert <b class="ft-book">tutors</b>, attend
    <b class="ft-book">live group classes</b>, and sharpen your skills with <b class="ft-book">topic-based quizzes</b>.
    <br>Earn <b class="ft-book">certificates</b> as you level up — all in one place at <b class="ft-book">ReadWithUs.org.uk</b>.
  </p>
<br>
<br>
  <button class="cta-primary" id="openSelector">
  Revise Your Topic
<img src="<?php echo getBaseUrl(); ?>public/assets/img/arrow-2.svg" alt="Arrow icon" class="arrow">
</button>
<br>
<div class="course-drop-select" id="dropDownOptions">
  
</div>
<br>
  <div class="secondary-buttons">
    <a href="<?php echo MyUtility::makeFullUrl('courses'); ?>">
      <button class="secondary-btn">Explore Courses</button>
    </a>
    <button class="secondary-btn" data-bs-toggle="modal" data-bs-target="#quizSignupModal">Find a Tutor</button>
    <a href="<?php echo MyUtility::makeFullUrl('teachers'); ?>">
      <button class="secondary-btn">Join Group Classes</button>
    </a>
  </div>
</div>

<?php
 
 
if (!empty($whyUsBlock)) {
    echo html_entity_decode($whyUsBlock);
}
if (!empty($whyWeEffectiveBlock)) {
    echo html_entity_decode($whyWeEffectiveBlock);
}
if (!empty($popularLanguages)) {
?>
<section class="section section--language">
    <div class="container container--narrow">
        <div class="section__head ">
            <h2><?php echo Label::getLabel('LBL_WHAT_LANGUAGE_YOU_WANT_TO_LEARN?'); ?></h2>
        </div>
        <div class="section__body">
            <div class="flag-wrapper">
                <?php foreach ($popularLanguages as $language) { ?>
                <div class="flag__box">
                    <div class="flag__media">
                        <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_TEACHING_LANGUAGES, $language['tlang_id'], Afile::SIZE_SMALL]), CONF_IMG_CACHE_TIME, '.jpg'); ?>"
                            alt="<?php echo $language['tlang_name']; ?>">
                    </div>
                    <div class="flag__name">
                        <span><?php echo $language['tlang_name'] ?></span>
                        <div class="lesson-count"></div>
                    </div>
                    <a class="flag__action"
                        href="<?php echo MyUtility::makeUrl('Teachers', 'languages', [$language['tlang_slug']]); ?>"></a>
                </div>
                <?php } ?>
            </div>
            <div class="more-info align-center">
                <p><?php echo Label::getLabel("LBL_DIFFERENT_LANGUAGE_NOTE"); ?> <a
                        href="<?php echo MyUtility::makeUrl('teachers'); ?>"><?php echo Label::getLabel('LBL_BROWSE_THEM_NOW'); ?></a>
                </p>
            </div>





            <div class="modal fade" id="quizSignupModal" tabindex="-1" aria-labelledby="quizSignupModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content p-4" style="margin:auto;">
                        <div class="modal-body text-center">
                            <h2 class="fw-bold mb-2" id="quizSignupModalLabel">Find a Tutor</h2>
                            <p class="mb-4" style="font-size: 1rem;">Tell us what you're looking for, and we'll connect you with a qualified tutor tailored to your learning needs.</p>
                                <form id="quizSignupForm" class="form-knowledge" novalidate>
                                <input type="text" name="full_name" class="form-control mb-3" placeholder="Enter Name" required>
                                <input type="email" name="email" class="form-control mb-3" placeholder="Enter E-mail" required>
                                <input type="email" name="parent_email" class="form-control mb-3" placeholder="Enter Parent's E-mail" required>
                                <input type="text" name="phone" class="form-control mb-3" placeholder="Enter Phone Number" required>
                                <div class="mb-3">
                                    <select name="subject" class="form-control modern-input" required>
                                        <option value="" disabled selected>Select Subject</option>
                                        <option value="Biology">Biology</option>
                                        <option value="Chemistry">Chemistry</option>
                                        <option value="Physics">Physics</option>
                                        <option value="Maths">Maths</option>
                                        <option value="English">English</option>
                                    </select>
                                </div>

                                <input type="text" name="preferred_time" class="form-control mb-3" placeholder="Enter Preferred Time" required>

                                <button type="button" class="start-quiz-btn w-100 py-2">Submit</button>
                                </form>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
</section>
<?php }
 /*   if ($topRatedTeachers) { ?>
<section class="section padding-bottom-5">
    <div class="container container--narrow">
        <div class="section__head">
            <h2><?php echo Label::getLabel('LBL_TOP_RATED_TEACHERS', $siteLangId); ?></h2>
        </div>
        <div class="section__body">
            <div class="teacher-wrapper">
                <div class="row">
                    <?php foreach ($topRatedTeachers as $teacher) { ?>
                    <div class="col-auto col-sm-6 col-md-6 col-lg-4 col-xl-3">
                        <div class="tile">
                            <div class="tile__head">
                                <div class="tile__media ratio ratio--1by1">
                                    <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM]), CONF_IMG_CACHE_TIME, '.jpg') ?>"
                                        alt="<?php echo $teacher['full_name']; ?>">
                                </div>
                            </div>
                            <div class="tile__body">
                                <a class="tile__title"
                                    href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$teacher['user_username']]); ?>">
                                    <h4><?php echo CommonHelper::truncateCharacters($teacher['full_name'], 60); ?></h4>
                                </a>
                                <div class="info-wrapper">
                                    <div class="info-tag location">
                                        <svg class="icon icon--location">
                                            <use
                                                xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#location'; ?>">
                                            </use>
                                        </svg>
                                        <span
                                            class="lacation__name"><?php echo $teacher['country_name']['name'] ?? ''; ?></span>
                                    </div>
                                    <div class="info-tag ratings">
                                        <svg class="icon icon--rating">
                                            <use
                                                xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating' ?>">
                                            </use>
                                        </svg>
                                        <span class="value"><?php echo $teacher['testat_ratings']; ?></span>
                                        <span class="count">(<?php echo $teacher['testat_reviewes']; ?>)</span>
                                    </div>
                                </div>
                                <div class="card__row--action ">
                                    <a href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$teacher['user_username']]); ?>"
                                        class="btn btn--primary btn--block"><?php echo Label::getLabel('LBL_VIEW_DETAILS', $siteLangId); ?></a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php
}*/
if (!empty($browseTutorPage)) {
?>
<?php echo html_entity_decode($browseTutorPage); ?>
<?php
}
if (count($classes) > 0) {
?>
<section class="section section--gray section--upcoming-class">
    <div class="container container--narrow">
        <div class="section__head d-flex justify-content-between align-items-center">
            <h2><?php echo Label::getLabel('LBL_UPCOMING_GROUP_CLASSES'); ?></h2>
            <a class="view-all"
                href="<?php echo MyUtility::makeUrl('GroupClasses'); ?>"><?php echo Label::getLabel("LBL_VIEW_ALL", $siteLangId); ?></a>
        </div>
        <div class="section__body">
            <div class="slider slider--onethird slider-onethird-js">
                <?php
                    foreach ($classes as $class) {
                        $classData = ['class' => $class, 'siteUserId' => $siteUserId, 'bookingBefore' => $bookingBefore, 'cardClass' => 'card-class-cover'];
                        $this->includeTemplate('group-classes/card.php', $classData, false);
                    }
                    ?>
            </div>
        </div>
    </div>
</section>
<?php }if (count($courses) > 0) { ?>
<section class="section section--gray padding-bottom-20 section--popular-courses">
    <div class="container container--narrow">
        <div class="section__head d-flex justify-content-between align-items-center">
            <h2><?php echo Label::getLabel('LBL_POPULAR_COURSES'); ?></h2>
            <a class="view-all"
                href="<?php echo MyUtility::makeUrl('Courses'); ?>"><?php echo Label::getLabel("LBL_VIEW_ALL", $siteLangId); ?></a>
        </div>
        <div class="section__body">
            <?php echo $this->includeTemplate('home/_partial/popularCourses.php', ['moreCourses' => $courses, 'siteLangId' => $siteLangId, 'siteUserId' => $siteUserId]); ?>
        </div>
    </div>
</section>
<?php
}
if ($testmonialList) {
?>
<section class="section section--quote">
    <div class="container container--narrow">
        <div class="quote-slider">
            <div class="slider slider--quote slider-quote-js">
                <?php foreach ($testmonialList as $testmonialDetail) { ?>
                <div>
                    <div class="slider__item">
                        <div class="quote">
                            <div class="quote__media">
                                <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_TESTIMONIAL_IMAGE, $testmonialDetail['testimonial_id'], Afile::SIZE_LARGE]), CONF_DEF_CACHE_TIME, '.jpg'); ?>"
                                    alt="<?php echo $testmonialDetail['testimonial_user_name']; ?>">
                                <div class="quote__box">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="36" height="30.857"
                                        viewBox="0 0 36 30.857">
                                        <g transform="translate(0 -29.235)">
                                            <path
                                                d="M233.882,29.235V44.664h10.286a10.3,10.3,0,0,1-10.286,10.286v5.143a15.445,15.445,0,0,0,15.429-15.429V29.235Z"
                                                transform="translate(-213.311)" />
                                            <path
                                                d="M0,44.664H10.286A10.3,10.3,0,0,1,0,54.949v5.143A15.445,15.445,0,0,0,15.429,44.664V29.235H0Z"
                                                transform="translate(0 0)" />
                                        </g>
                                    </svg>
                                </div>
                            </div>
                            <div class="quote__content">
                                <p><?php echo $testmonialDetail['testimonial_text']; ?></p>
                                <div class="quote-info">
                                    <h4><?php echo $testmonialDetail['testimonial_user_name']; ?></h4>
                                </div>
                                <div class="quote__icon">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="138" height="118.286"
                                        viewBox="0 0 138 118.286">
                                        <g transform="translate(0 -29.235)">
                                            <path
                                                d="M233.882,29.235V88.378H273.31a39.474,39.474,0,0,1-39.429,39.429v19.714a59.208,59.208,0,0,0,59.143-59.143V29.235Z"
                                                transform="translate(-155.025 0)" />
                                            <path class="b"
                                                d="M0,88.378H39.429A39.474,39.474,0,0,1,0,127.806v19.714A59.208,59.208,0,0,0,59.143,88.378V29.235H0Z"
                                                transform="translate(0 0)" />
                                        </g>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</section>
<?php
}
if (!empty($startLearning)) {
    echo html_entity_decode($startLearning);
}
?>
<section class="section section--faq">
    <div class="container container--narrow">
        <div class="section__head">
            <h2><?php echo Label::getLabel('LBL_POPULAR_FAQS', $siteLangId); ?></h2>
        </div>
        <div class="faq-cover faq-container">
            <?php foreach ($popularFaqList as $faqId => $faqDetails) { ?>
            <div class="faq-row faq-group-js">
                <a href="javascript:void(0)" class="faq-title faq__trigger faq__trigger-js">
                    <h5 class="text-center"><?php echo $faqDetails['faq_title']; ?></h5>
                </a>
                <div class="faq-answer faq__target faq__target-js">
                    <iframe srcdoc="<?php echo $faqDetails['faq_description']; ?>"
                        style="border:none;width: 100%;height: 100%;"></iframe>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</section>
<?php
if ($blogPostsList) {
?>
<section class="section">
    <div class="container container--narrow">
        <div class="section__head d-flex justify-content-between align-items-center">
            <h2><?php echo Label::getLabel('LBL_Latest_Blogs'); ?></h2>
            <a class="view-all"
                href="<?php echo MyUtility::makeUrl('Blog') ?>"><?php echo Label::getLabel('LBL_View_Blogs'); ?></a>
        </div>
        <div class="section__body">
            <div class="blog-wrapper">
                <div class="slider slider--onehalf slider-onehalf-js">
                    <?php foreach ($blogPostsList as $postDetail) { ?>
                    <div>
                        <div class="slider__item">
                            <div class="blog-card">
                                <div class="blog__head">
                                    <div class="blog__media ratio ratio--4by3">
                                        <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_BLOG_POST_IMAGE, $postDetail['post_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.jpg') ?>"
                                            alt="<?php echo $postDetail['post_title']; ?>">
                                    </div>
                                </div>
                                <div class="blog__body">
                                    <div class="blog__detail">
                                        <div class="tags-inline__item"><?php echo $postDetail['bpcategory_name']; ?>
                                        </div>
                                        <div class="blog__title">
                                            <h3><?php echo $postDetail['post_title'] ?></h3>
                                        </div>
                                        <div class="blog__date">
                                            <svg class="icon icon--calendar">
                                                <use
                                                    xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#calendar' ?>">
                                                </use>
                                            </svg>
                                            <span><?php echo MyDate::formatDate($postDetail['post_published_on']); ?>
                                            </span>
                                        </div>
                                        <a href="<?php echo MyUtility::makeUrl('Blog', 'PostDetail', [$postDetail['post_id']]); ?>" class="btn btn--secondary"><?php echo Label::getLabel('LBL_VIEW_BLOG'); ?></a>
                                    </div>
                                </div>
                                <a href="<?php echo MyUtility::makeUrl('Blog', 'PostDetail', [$postDetail['post_id']]); ?>"
                                    class="blog__action"></a>
                            </div>
                        </div>
                    </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</section>
<?php } ?>



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
const baseUrlRaw = "<?php echo getBaseUrl(); ?>"; // site base URL
const baseUrl = baseUrlRaw.endsWith('/') ? baseUrlRaw : baseUrlRaw + '/'; //line added by rehan to ensure trailing slash in baseUrl
let steps = [
  { title: "Select Level", options: [], url: "api.php?url=getCourses", paramKey: null },
  { title: "Select Subject", options: [], url: "api.php?url=getSubjects", paramKey: "levelId" }
];

const selectedValues = {
  levelId: null,
  subjectId: null,
  examboardId: null,
  tierId: null,
  yearId: null,
};

// breadcrumb ke liye selected steps
const selectedSteps = [];

let currentStep = 0;
const container = document.getElementById('dropDownOptions');
const openBtn = document.getElementById('openSelector');

function fetchOptionsForStepWithParam(stepIndex, params) {
  return new Promise((resolve) => {
    const step = steps[stepIndex];
    if (!step.url) {
      resolve();
      return;
    }
      const rel = step.url.replace(/^\//, ''); // strip leading slash ADDED BY REHAN
     const url = new URL(rel, baseUrl);       
    
    if (step.paramKey && params[step.paramKey]) {
      url.searchParams.append(step.paramKey, params[step.paramKey]);
    }
     console.log(`[Step ${stepIndex}] → ${step.title}`, url.toString(), 'params:', params);

    fetch(url.toString())
      .then(res => res.json())
      .then(json => {
        if (json.status === 1 && Array.isArray(json.data)) {
          step.options = json.data.map(item => ({ id: item.id, name: item.name }));
        } else {
          step.options = [{ id: "error", name: "Error loading options" }];
        }
        resolve();
      })
      .catch(() => {
        step.options = [{ id: "error", name: "Error loading options" }];
        resolve();
      });
  });
}

function renderStep(stepIndex) {
  const step = steps[stepIndex];
  container.innerHTML = '';

  // Header row with breadcrumb
  const headerRow = document.createElement('div');
  headerRow.style.display = "flex";
  headerRow.style.alignItems = "center";
  headerRow.style.justifyContent = "center";
  headerRow.style.position = "relative";
  headerRow.style.marginTop = "15px";
  headerRow.style.marginBottom = "10px";

  const breadcrumb = document.createElement('div');
  breadcrumb.className = 'breadcrumb-nav';
  breadcrumb.style.display = 'flex';
  breadcrumb.style.gap = '8px';
  breadcrumb.style.fontSize = '14px';
  breadcrumb.style.cursor = 'pointer';

  selectedSteps.forEach((sel, idx) => {
    if (sel) {
      const crumb = document.createElement('span');
      crumb.textContent = sel.name;
      crumb.style.color = idx === stepIndex ? '#333' : '#3589c2';
      crumb.style.fontWeight = idx === stepIndex ? 'bold' : 'normal';

      crumb.onclick = () => {
        // Reset current step
        currentStep = idx;

        // Clear all selections after this step
        selectedSteps.splice(idx + 1);
        if (idx < 1) {
          selectedValues.subjectId = null;
          selectedValues.examboardId = null;
          selectedValues.tierId = null;
          selectedValues.yearId = null;
        } else if (idx < 2) {
          selectedValues.examboardId = null;
          selectedValues.tierId = null;
          selectedValues.yearId = null;
        } else if (idx < 3) {
          selectedValues.tierId = null;
          selectedValues.yearId = null;
        }

        renderStep(currentStep);
      };

      breadcrumb.appendChild(crumb);

      if (idx < stepIndex) {
        const arrow = document.createElement('span');
        arrow.textContent = '›';
        breadcrumb.appendChild(arrow);
      }
    }
  });

  headerRow.appendChild(breadcrumb);
  container.appendChild(headerRow);

  // Heading of current step
  const heading = document.createElement('h5');
  heading.className = 'ft-gothic';
  heading.style.textAlign = 'center';
  heading.style.marginBottom = '15px';
  heading.textContent = step.title;
  container.appendChild(heading);

  // Options
  step.options.forEach(opt => {
    const div = document.createElement('div');
    div.className = 'loop-wrap-btn';
    const btn = document.createElement('button');
    btn.className = 'ft-gothic';

    const displayName = opt.name;
    const optId = opt.id;

  btn.innerHTML = `<span>${displayName}</span>
                 <img src="${baseUrl.replace(/\/$/, '/') }public/assets/img/right-arrow.svg" alt="Arrow" class="arrow">`;


    btn.onclick = () => {
      selectedSteps[stepIndex] = { id: optId, name: displayName };

      if (step.title === "Select Level") {
        selectedValues.levelId = optId;

        if (displayName === "GCSE") {
          // GCSE → Examboard + Tier
          steps = [
            { title: "Select Level", options: [], url: "api.php?url=getCourses", paramKey: null },
            { title: "Select Subject", options: [], url: "api.php?url=getSubjects", paramKey: "levelId" },
            { title: "Select Examboard", options: [], url: "api.php?url=getExamboards", paramKey: "subjectId" },
            { title: "Select Tier", options: [], url: "api.php?url=getTiers", paramKey: "examboardId" },
          ];
        } else {
          // Non-GCSE → Year
          steps = [
            { title: "Select Level", options: [], url: "api.php?url=getCourses", paramKey: null },
            { title: "Select Subject", options: [], url: "api.php?url=getSubjects", paramKey: "levelId" },
            { title: "Select Year", options: [], url: "api.php?url=getYears", paramKey: "subjectId" },
          ];
        }

      } else if (step.title === "Select Subject") {
        selectedValues.subjectId = optId;
      } else if (step.title === "Select Examboard") {
        selectedValues.examboardId = optId;
      } else if (step.title === "Select Tier") {
        selectedValues.tierId = optId;
      } else if (step.title === "Select Year") {
        selectedValues.yearId = optId;
      }

      // Next step
      currentStep++;
      if (currentStep < steps.length) {
        fetchOptionsForStepWithParam(currentStep, selectedValues).then(() => renderStep(currentStep));
      } else {
        container.style.display = 'none';
        currentStep = 0;
        var subtopic = selectedValues.subjectId;
        var url = fcom.makeUrl('quizizz') + '?subtopic=' + encodeURIComponent(subtopic);
        window.location.href = url;
      }
    };

    div.appendChild(btn);
    container.appendChild(div);
  });

  container.style.display = 'block';
}

openBtn.addEventListener('click', () => {
  if (container.style.display === 'block') {
    container.style.display = 'none';
    currentStep = 0;
  } else {
    if (steps[0].url && steps[0].options.length === 0) {
      fetchOptionsForStepWithParam(0, selectedValues).then(() => renderStep(0));
    } else {
      renderStep(0);
    }
  }
});
</script>
