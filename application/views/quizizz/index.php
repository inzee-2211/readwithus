<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$activeTab = $_GET['tab'] ?? 'quiz';

// Full list, e.g. "30,31,32"
// Priority: querystring → controller-provided → fallback to session/empty
$setupIdsParam = $_GET['setup_ids'] ?? ($setupIdsParam ?? '');

// keep legacy single ID if you still need it somewhere
$setupId = $_GET['setup_id'] ?? ($_SESSION['setupId'] ?? 0);

$subjectId = $_SESSION['subjectId'] ?? 0;
$subjectNm = $_SESSION['subjectName'] ?? 'Subject';


$topics = $topics ?? [];
$subtopics = $subtopics ?? [];
$levelName = $levelName ?? '';
$examboardName = $examboardName ?? '';
$tierName = $tierName ?? '';
$yearName = $yearName ?? '';

// Your existing DB logic for papers (kept as-is)
// $subtopicids = [];
// if (!empty($topics)) {
//     foreach ($topics as $topic) {
//         $subtopicQuery  = "SELECT id FROM course_topics WHERE parent_id = " . (int)$topic['id'];
//         $subtopicResult = $db->query($subtopicQuery);
//         $subtopicid     = $db->fetchAll($subtopicResult);
//         $subtopicids    = array_merge($subtopicids, array_column($subtopicid, 'id'));
//     }
// }

// if (!empty($subtopicids)) {
//     $idList       = implode(',', array_map('intval', $subtopicids));
//     $paperQuery   = "SELECT id, previous_paper_pdf, video_url, subtopic FROM course_subtopics WHERE subtopic IN ($idList)";
//     $paperResult  = $db->query($paperQuery);
//     $papers       = $db->fetchAll($paperResult);
// } else {
//     $papers = [];
// }
?>

<style>
    <?php include __DIR__ . '/style.css'; ?>
</style>
<section class="quiz-topic-page">
    <div class="container">
        <!-- HEADER: SUBJECT + RIGHT INSTRUCTIONS -->
        <div class="row align-items-start mb-4">
            <!-- LEFT: subject & overview -->
            <div class="col-lg-7 mb-3 mb-lg-0">
                <div class="subject-header">
                    <div class="subject-badge">
                        <?php echo strtoupper(substr($subjectNm, 0, 1)); ?>
                    </div>
                    <div>
                        <!-- MAIN HEADING = SUBJECT -->
                        <h1 class="subject-title">
                            <?php echo htmlspecialchars($subjectNm); ?>
                        </h1>

                        <!-- PATH META: GCSE · Edexcel · Higher · Year 11 · Topic: Fractions -->
                        <?php
                        $pathBits = [];
                        if (!empty($levelName)) {
                            $pathBits[] = htmlspecialchars($levelName);
                        }
                        if (!empty($examboardName)) {
                            $pathBits[] = htmlspecialchars($examboardName);
                        }
                        if (!empty($tierName)) {
                            $pathBits[] = htmlspecialchars($tierName);
                        }
                        if (!empty($yearName)) {
                            $pathBits[] = htmlspecialchars($yearName);
                        }
                        // if (!empty($topicTitle))    { $pathBits[] = 'Topic: ' . htmlspecialchars($topicTitle); }
                        ?>

                        <?php if (!empty($pathBits)) { ?>
                            <p class="subject-meta subject-meta-path">
                                <?php echo implode(' · ', $pathBits); ?>
                            </p>
                        <?php } ?>

                        <p class="subject-meta subject-meta-main">
                            Browse subtopics to watch videos, take quizzes and download past papers.
                        </p>
                        <p class="subject-meta subject-meta-secondary">
                            Start with the videos to learn the ideas, then check your understanding with quick quizzes
                            and finish by practising with exam-style past papers.
                        </p>
                    </div>
                </div>
                <!-- 
        <?php if (!empty($topicTitle)) { ?>
            <p class="subject-meta mt-2">
                You’re currently viewing: <strong><?php echo htmlspecialchars($topicTitle); ?></strong>
            </p>
        <?php } ?> -->

                <!-- <ul class="subject-highlights mt-2">
            <li>Short, focused videos for each subtopic.</li>
            <li>Auto-marked quizzes with instant feedback.</li>
            <li>Printable question &amp; answer papers for exam practice.</li>
        </ul> -->
            </div>

            <!-- RIGHT: how to use this page -->
            <div class="col-lg-5">
                <div class="study-multi-card study-guide-card">
                    <div class="study-multi-icon">?</div>
                    <div>
                        <div class="study-multi-title">How to use this topic</div>
                        <ol class="study-steps">
                            <li><strong>Choose a tab</strong> – Videos, Quiz or Past papers.</li>
                            <li><strong>Open the topic</strong> – click <em>Topic –
                                    <?php echo htmlspecialchars($topicTitle ?? 'Numbers'); ?></em> to see the subtopics.
                            </li>
                            <li><strong>Work left to right</strong> – watch the video, take the quiz, then attempt the
                                paper.</li>
                        </ol>
                        <p class="study-tip">
                            Tip: aim to complete at least <strong>3 subtopics</strong> each study session.
                        </p>
                    </div>
                </div>
            </div>
        </div>


        <!-- TABS -->
        <div class="row justify-content-center">
            <div class="col-md-11">
                <div class="rwu-tabs-wrapper">
                    <div class="rwu-tabs">
                        <a href="?tab=video&setup_ids=<?= htmlspecialchars($setupIdsParam) ?>"
                            class="rwu-tab <?= ($activeTab == 'video') ? 'active' : '' ?>"> Video lessons
                        </a>
                        <a href="?tab=quiz&setup_ids=<?= htmlspecialchars($setupIdsParam) ?>"
                            class="rwu-tab <?= ($activeTab == 'quiz') ? 'active' : '' ?>"> Quiz
                        </a>
                        <a href="?tab=paper&setup_ids=<?= htmlspecialchars($setupIdsParam) ?>"
                            class="rwu-tab <?= ($activeTab == 'paper') ? 'active' : '' ?>"> Past papers
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- TAB CONTENTS (your original logic, just wrapped in Cognito-style topic) -->
        <div class="row justify-content-center">
            <div class="col-md-11">

                <!-- VIDEO TAB -->
                <?php if ($activeTab === 'video'): ?>
                    <div id="video" class="tab-content active">
                        <h4>Video lessons</h4>
                        <p>This section contains video lessons grouped under each topic.</p>

                        <?php if (empty($topics)) { ?>
                            <p>No topics found.</p>
                        <?php } else { ?>
                            <?php foreach ($topics as $tIndex => $topic): ?>
                                <?php
                                $subs = $topic['subtopics'] ?? [];
                                $topicId = (int) $topic['setup_id'];
                                $topicName = htmlspecialchars($topic['topic_name']);
                                if (empty($subs))
                                    continue;
                                ?>
                                <div class="mb-3">
                                    <button class="topic-toggle collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#topic-video-<?php echo $topicId; ?>">
                                        <span class="dropdown-indicator">▾</span>
                                        <span class="topic-text">
                                            <?php echo 'Topic - ' . $topicName; ?>
                                        </span>
                                    </button>
                                </div>

                                <div class="collapse mb-4" id="topic-video-<?php echo $topicId; ?>">
                                    <div class="topic-body">
                                        <div class="row">
                                            <?php foreach ($subs as $idx => $s):
                                                $subId = (int) $s['id'];
                                                $subName = htmlspecialchars($s['name']);
                                                $videoUrl = trim((string) $s['video_url']);
                                                $videoUrl = filter_var($videoUrl, FILTER_SANITIZE_URL);

                                                $embedUrl = '';
                                                if (
                                                    !empty($videoUrl) &&
                                                    preg_match('%(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})%', $videoUrl, $m)
                                                ) {
                                                    $embedUrl = 'https://www.youtube.com/embed/' . $m[1];
                                                }
                                                ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card shadow-sm">
                                                        <div class="card-body">
                                                            <h6 class="card-title">
                                                                <?php echo ($idx + 1) . '. ' . $subName; ?>
                                                            </h6>
                                                            <?php if ($embedUrl) { ?>
                                                               <button type="button" class="watch-video-btn" data-target="#video-<?php echo $subId; ?>">
    Watch video
</button>

                                                                <div class="video-container mt-2" id="video-<?php echo $subId; ?>"
                                                                    style="display:none;">
                                                                 <div class="ratio ratio-16x9">
  <iframe
    data-src="<?php echo $embedUrl . (strpos($embedUrl, '?') === false ? '?' : '&') . 'playsinline=1'; ?>"
    src=""
    frameborder="0"
    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen"
    allowfullscreen
  ></iframe>
</div>

                                                                </div>
                                                            <?php } else { ?>
                                                                <p class="text-muted mb-0">No video found for <?php echo $subName; ?>.</p>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php } ?>
                    </div>
                <?php endif; ?>


                <!-- QUIZ TAB -->
                <?php if ($activeTab === 'quiz'): ?>
                    <div id="quiz" class="tab-content active">
                        <h4>Quiz</h4>
                        <p>This section contains quizzes grouped under each topic.</p>

                        <?php if (empty($topics)) { ?>
                            <p class="text-center">No topics found.</p>
                        <?php } else { ?>
                            <?php foreach ($topics as $topic): ?>
                                <?php
                                $subs = $topic['subtopics'] ?? [];
                                $topicId = (int) $topic['setup_id'];
                                $topicName = htmlspecialchars($topic['topic_name']);
                                if (empty($subs))
                                    continue;
                                ?>
                                <div class="mb-3">
                                    <button class="topic-toggle collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#topic-quiz-<?php echo $topicId; ?>">
                                        <span class="dropdown-indicator">▾</span>
                                        <span class="topic-text">
                                            <?php echo 'Topic - ' . $topicName; ?>
                                        </span>
                                    </button>
                                </div>

                                <div class="collapse mb-4" id="topic-quiz-<?php echo $topicId; ?>">
                                    <div class="topic-body">
                                        <div class="row">
                                            <?php foreach ($subs as $idx => $s): ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body d-flex flex-column justify-content-between">
                                                            <h6 class="card-title">
                                                                <?php echo ($idx + 1) . '. ' . htmlspecialchars($s['name']); ?>
                                                            </h6>
                                                            <?php if (isset($_SESSION['quiz_user']['id']) && !empty($_SESSION['quiz_user']['id'])) { ?>
                                                                <button class="btn btn-primary mt-2 start-quiz-logged-in"
                                                                    data-subtopic-id="<?php echo (int) $s['id']; ?>">
                                                                    Start quiz
                                                                </button>
                                                            <?php } else { ?>
                                                                <button class="btn btn-primary mt-2 btn-prnt-inner" data-bs-toggle="modal"
                                                                    data-bs-target="#quizSignupModal"
                                                                    data-subtopic-id="<?php echo (int) $s['id']; ?>">
                                                                    Start quiz
                                                                </button>
                                                            <?php } ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php } ?>
                    </div>
                <?php endif; ?>

                <!-- PAST PAPER TAB -->
                <?php if ($activeTab === 'paper'): ?>
                    <div id="paper" class="tab-content active">
                        <h4>Past papers</h4>
                        <p>This section contains question and answer papers grouped under each topic.</p>

                        <?php if (empty($topics)) { ?>
                            <p class="text-center">No topics found.</p>
                        <?php } else { ?>
                            <?php foreach ($topics as $topic): ?>
                                <?php
                                $subs = $topic['subtopics'] ?? [];
                                $topicId = (int) $topic['setup_id'];
                                $topicName = htmlspecialchars($topic['topic_name']);
                                if (empty($subs))
                                    continue;
                                ?>
                                <div class="mb-3">
                                    <button class="topic-toggle collapsed" type="button" data-bs-toggle="collapse"
                                        data-bs-target="#topic-paper-<?php echo $topicId; ?>">
                                        <span class="dropdown-indicator">▾</span>
                                        <span class="topic-text">
                                            <?php echo 'Topic - ' . $topicName; ?>
                                        </span>
                                    </button>
                                </div>

                                <div class="collapse mb-4" id="topic-paper-<?php echo $topicId; ?>">
                                    <div class="topic-body">
                                        <div class="row">
                                            <?php
                                            $hasPaper = false;
                                            $cardIndex = 0;

                                            foreach ($subs as $s) {
                                                $questionPdfRaw = trim((string) ($s['previous_paper_pdf'] ?? ''));
                                                $answerPdfRaw = trim((string) ($s['answer_pdf_path'] ?? ''));

                                                if ($questionPdfRaw === '' && $answerPdfRaw === '') {
                                                    continue;
                                                }

                                                $hasPaper = true;
                                                $cardIndex++;
                                                $paperTitle = htmlspecialchars($s['name']);

                                                $questionPdfPath = $questionPdfRaw !== '' ? htmlspecialchars($questionPdfRaw) : '#';
                                                $answerPdfPath = $answerPdfRaw !== '' ? htmlspecialchars($answerPdfRaw) : '#';
                                                ?>
                                                <div class="col-md-4 mb-3">
                                                    <div class="card h-100 shadow-sm">
                                                        <div class="card-body d-flex flex-column justify-content-between">
                                                            <h6 class="card-title" style="font-size: 14px;">
                                                                <?php echo $cardIndex . '. ' . $paperTitle; ?>
                                                            </h6>

                                                            <div class="d-flex flex-wrap gap-2 mt-2">
                                                                <a href="<?php echo $questionPdfPath; ?>" target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="start-quiz-btn flex-fill text-center">
                                                                    Question Paper
                                                                </a>

                                                                <a href="<?php echo $answerPdfPath; ?>" target="_blank"
                                                                    rel="noopener noreferrer"
                                                                    class="start-quiz-btn flex-fill text-center">
                                                                    Answer Paper
                                                                </a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php } ?>

                                            <?php if (!$hasPaper) { ?>
                                                <p class="text-muted mb-0">No papers found for this topic.</p>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php } ?>
                    </div>
                <?php endif; ?>



            </div>
        </div>
    </div>

    <!-- QUIZ SIGNUP MODAL -->
    <div class="modal fade" id="quizSignupModal" tabindex="-1" aria-labelledby="quizSignupModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-body text-center">
                    <h2 class="fw-bold mb-2">Ready to test your Knowledge</h2>
                    <p class="mb-4">Create an account to access the quiz.<br>We'll send your quiz result directly to
                        your gmail</p>
                    <form id="quizSignupForm" novalidate>
                        <input type="text" name="full_name" class="form-control mb-3" placeholder="Enter full name"
                            required>
                        <input type="email" name="email" class="form-control mb-3" placeholder="Enter e-mail" required>
                        <input type="email" name="parent_email" class="form-control mb-3"
                            placeholder="Enter Parents e-mail" required>
                        <input type="tel" name="phone" class="form-control mb-4" placeholder="Enter phone number"
                            required>
                        <input type="hidden" id="subtopicIdField" name="subtopic_id" value="">
                        <button type="button" class="start-quiz-btn">Start Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>
<script>
    // Front webroot e.g. "http://localhost/readwithus/readwithus/public/"
    // or "https://readwithus.org.uk/"
    var FRONT_WEBROOT = '<?php echo CONF_WEBROOT_FRONTEND; ?>';
</script>

<script>
    // Show / hide video inside cards
    // document.querySelectorAll('.watch-video-btn').forEach(function (button) {
    //     button.addEventListener('click', function () {
    //         const target = document.querySelector(this.getAttribute('data-target'));
    //         if (!target) return;
    //         const show = (target.style.display === 'none' || !target.style.display);
    //         target.style.display = show ? 'block' : 'none';
    //         this.textContent = show ? 'Hide video' : 'Watch video';
    //     });
    // });
    document.querySelectorAll('.watch-video-btn').forEach(function (button) {
  button.addEventListener('click', function (e) {
    e.preventDefault();
    e.stopPropagation();

    const target = document.querySelector(this.getAttribute('data-target'));
    if (!target) return;

    const iframe = target.querySelector('iframe');
    const show = (target.style.display === 'none' || !target.style.display);

    if (show) {
      // set src only once
      if (iframe && !iframe.getAttribute('src')) {
        iframe.setAttribute('src', iframe.getAttribute('data-src') || '');
      }
      target.style.display = 'block';
      this.textContent = 'Hide video';
    } else {
      target.style.display = 'none';
      this.textContent = 'Watch video';
    }
  });
});


    // Put subtopic id into hidden field when opening modal
    // document.querySelectorAll('.btn-prnt-inner').forEach(function (button) {
    //     button.addEventListener('click', function () {
    //         const subtopicId = this.getAttribute('data-subtopic-id');
    //         const hiddenField = document.getElementById('subtopicIdField');
    //         if (hiddenField) {
    //             hiddenField.value = subtopicId;
    //         }
    //     });
    // });
    // Always set subtopic_id when modal is about to open (most reliable)
$('#quizSignupModal').on('show.bs.modal', function (event) {
  const btn = event.relatedTarget; // the button that triggered the modal
  const subtopicId = btn ? $(btn).data('subtopic-id') : '';

  $('#subtopicIdField').val(subtopicId || '');

  // Optional: clear previous invalid styling/messages
  $('#quizSignupForm .form-control').removeClass('is-invalid');
});


    // AJAX submit for quiz signup (single handler, your old logic)
    if (typeof $ !== 'undefined' && typeof fcom !== 'undefined') {
       let quizSignupBusy = false;

$(document).on('click', '#quizSignupForm .start-quiz-btn', function (e) {
  e.preventDefault();
  e.stopPropagation();

  if (quizSignupBusy) return;

  const form = $('#quizSignupForm');

  // ✅ Ensure subtopic_id is present
  const subtopicId = ($('#subtopicIdField').val() || '').trim();
  if (!subtopicId) {
    alert('Please close the popup and click "Start quiz" again (subtopic missing).');
    return;
  }

  const formData = form.serialize();

  // ---- FRONTEND VALIDATION ----
  let valid = true;
  const nameField = form.find('[name="full_name"]');
  const emailField = form.find('[name="email"]');
  const pEmailField = form.find('[name="parent_email"]');
  const phoneField = form.find('[name="phone"]');

  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  const phoneRegex = /^[0-9]{10,15}$/;

  form.find('.form-control').removeClass('is-invalid');

  if (nameField.val().trim().length < 3) { nameField.addClass('is-invalid'); valid = false; }
  if (!emailRegex.test(emailField.val().trim())) { emailField.addClass('is-invalid'); valid = false; }
  if (!emailRegex.test(pEmailField.val().trim())) { pEmailField.addClass('is-invalid'); valid = false; }
  if (!phoneRegex.test(phoneField.val().trim())) { phoneField.addClass('is-invalid'); valid = false; }

  if (!valid) {
    form.addClass('shake');
    setTimeout(() => form.removeClass('shake'), 500);
    alert('Please enter valid information before starting the quiz.');
    return;
  }

  quizSignupBusy = true;
  const btn = $(this);
  btn.prop('disabled', true).text('Starting...');

  fcom.ajax(fcom.makeUrl('Quizizz', 'submitSignup'), formData, function (response) {
  // always unlock UI
  quizSignupBusy = false;
  btn.prop('disabled', false).text('Start Quiz');

  try {
    if (typeof response === 'string') response = JSON.parse(response);

    // ✅ FAIL / BLOCKED
    if (!response || response.status != 1) {
      alert(response?.msg || 'Signup failed. Please try again.');
      if (response?.redirect_url) window.location.href = response.redirect_url;
      return;
    }

    // ✅ REQUIRE subtopicid
    const subId = (response.subtopicid || '').toString().trim();
    if (!subId) {
      alert('Signup succeeded but subtopic is missing. Please close and click "Start quiz" again.');
      return;
    }

    // ✅ Close modal (optional but recommended)
    try { $('#quizSignupModal').modal('hide'); } catch (e) {}

    // ✅ SUBSCRIBED / UNLIMITED
    if (response.is_subscribed == 1 || response.quota === 'unlimited') {
      // No quota alert for unlimited users
      window.location.href = 'quizfocus?subtopic=' + encodeURIComponent(subId);
      return;
    }

    // ✅ QUOTA FLOW (submitSignup already reserved +1 attempt)
    const attemptsLeft = Number(response.attempts_left);
    const quota = response.quota;

    // If backend says 0 left, force upgrade
    if (!Number.isFinite(attemptsLeft) || attemptsLeft <= 0) {
      alert(response.msg || 'Your free quiz quota is expired. Please subscribe to continue.');
      window.location.href = response.redirect_url
        ? response.redirect_url
        : FRONT_WEBROOT.replace(/\/+$/, '') + '/pricing';
      return;
    }

    // ✅ Show remaining quota THEN redirect
    alert('You have ' + attemptsLeft + ' free quizzes left out of ' + quota + '.');
    window.location.href = 'quizfocus?subtopic=' + encodeURIComponent(subId);

  } catch (err) {
    console.error('submitSignup response parse error:', err, response);
    alert('Unexpected server response. Please try again.');
  }
});

});
    }

    // keep your existing globals
    var questions = <?php echo json_encode($questionData ?? []); ?>;
    var _body = $('body');
    var _toggle = $('.js-filter-toggle');
    _toggle.each(function () {
        var _this = $(this),
            _target = $(_this.attr('href'));

        _this.on('click', function (e) {
            e.preventDefault();
            _target.toggleClass('is-filter-visible');
            _this.toggleClass('is-active');
            _body.toggleClass('is-filter-show');
        });
    });

    let currentQuestion = 0;
    let timerDuration = 10 * 60;
    let timerInterval = null;
    let userAnswers = {};
    var userSessionId = "<?php echo $_SESSION['subtopicId'] ?? ''; ?>";

    function openTab(event, tabId) {
        // kept for compatibility if you call it elsewhere
        document.querySelectorAll('.rwu-tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
    // Handler for logged-in users to check quota before starting
 $(document).on('click', '.start-quiz-logged-in', function () {
    const subtopicId = $(this).data('subtopic-id');

    // checkQuota now also RESERVES (increments) an attempt
    fcom.ajax(fcom.makeUrl('Quizizz', 'checkQuota'), 'subtopic_id=' + encodeURIComponent(subtopicId), function (res) {
        let response = res;

        if (typeof res === 'string') {
            try { response = JSON.parse(res); } catch (e) {
                console.error("Non-JSON response from checkQuota:", res);
                alert("Failed to parse server response.");
                return;
            }
        }

        const status = response.status ?? response.data?.status;
        const allowed = response.allowed ?? response.data?.allowed;
        const msg = response.msg ?? response.data?.msg;
        const redirectUrl = response.redirect_url ?? response.data?.redirect_url;

        const attemptsLeft = response.attempts_left ?? response.data?.attempts_left;
        const quota = response.quota ?? response.data?.quota;

       if (status == 1 && allowed) {

    if (response.is_subscribed == 1 || response.quota === 'unlimited') {
        // no quota alert for subscribed users
        window.location.href = 'quizfocus?subtopic=' + encodeURIComponent(subtopicId);
        return;
    }

    if (typeof attemptsLeft !== 'undefined' && typeof quota !== 'undefined') {
        alert('You have ' + attemptsLeft + ' free quizzes left out of ' + quota);
    }

    window.location.href = 'quizfocus?subtopic=' + encodeURIComponent(subtopicId);
    return;
}


        alert(msg || 'Quota check failed');
        if (redirectUrl) window.location.href = redirectUrl;
    });
});

</script>