<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();
$activeTab = $_GET['tab'] ?? 'quiz';
$subtopicId = $_GET['subtopic'] ?? ($_SESSION['subtopicId'] ?? 0);
?>
<style>
    /* Subtopic Start Quiz buttons */
    .card .btn-primary {
        background-color: #419ad5;
        border-color: #419ad5;
        color: #fff;
        transition: background-color 0.3s, color 0.3s;
    }
    .card .btn-primary:hover {
        background-color: #000;
        border-color: #000;
        color: #fff;
    }

    /* Topic toggle buttons */
    .topic-toggle {
        padding: 0.9rem 1.25rem;
        font-weight: 600;
        text-align: left;
        display: flex;              /* flex for arrow + text */
        align-items: center;
        gap: 10px;                  /* arrow aur text ke beech space */
        width: 100%;
        border-radius: .35rem;
        border: 1px solid #ddd;
        background-color: #f8f9fa;
        color: #000;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    .topic-toggle:hover {
        background-color: #e9ecef;
    }

    /* Collapsed (band) */
    .topic-toggle.collapsed {
        background-color: #343a40;
        color: #fff;
    }

    /* Expanded (khula) */
    .topic-toggle:not(.collapsed) {
        background-color: #419ad5;
        color: #fff;
        border: 1px solid #343a40;
    }

    /* Dropdown indicator arrow */
    .topic-toggle .dropdown-indicator {
        font-size: 2rem;
        transition: transform 0.3s ease-in-out;
    }
    .topic-toggle:not(.collapsed) .dropdown-indicator {
        transform: rotate(180deg);
    }

    /* Dropdown auto expand - no scroll */
    .show-all-items {
        max-height: none !important;
        overflow: visible !important;
    }

    /* Video button tweak */
    .watch-video-btn {
        font-weight: 600;
    }

    /* Responsive */
    @media (max-width: 576px) {
        .topic-toggle {
            padding-right: 3rem;
        }
    }
</style>

<section>
    <div class="container">
        <div class="row mt-5 justify-content-center mb-5">
            <div class="col-md-6">
                <div class="logo-w-head d-flex align-items-baseline">
                    <img src="<?php echo getBaseUrl(); ?>assets/img/math.jpg">
                    <?php echo $_SESSION['subtopicName']; ?>
                </div>
                <div class="">
                    <h6 class="fw-bold brd-crumb"><?php echo $_SESSION['subtopicName']; ?></h6>
                </div>
                <p class="mb-3">
                    The video above has been specially created to guide you through the key concepts of this course...
                </p>
                <p class="mb-3">
                    After watching the video, explore the topics listed below...
                </p>
            </div>
            <div class="col-md-6">
                <div class="vide-wrap embed-responsive embed-responsive-16by9">
                    <img src="<?php echo getBaseUrl(); ?>images/Maths.jpg"
                        alt="Maths Image"
                        class="img-fluid"
                        style="width:100%; height:auto; border-radius:8px;">
                </div>
            </div>
        </div>
    </div>

    <div class="pt-4">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-md-9">
                    <!-- Custom Tab Bar -->
                    <div class="tab-wrapper">
                        <div class="tabs w-75 p-1">
                            <a href="?tab=video&subtopic=<?= $subtopicId ?>" class="tab pt-4 <?= ($activeTab == 'video') ? 'active' : '' ?>">Video Lessons</a>
                            <a href="?tab=quiz&subtopic=<?= $subtopicId ?>" class="tab pt-4 <?= ($activeTab == 'quiz') ? 'active' : '' ?>">Quiz</a>
                            <a href="?tab=paper&subtopic=<?= $subtopicId ?>" class="tab pt-4 <?= ($activeTab == 'paper') ? 'active' : '' ?>">Past Paper</a>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $subjectId = $_SESSION['subtopicId'];
            $db = FatApp::getDb();

            // Fetch topics
            $topicQuery = "SELECT id, topic FROM course_topics WHERE subject_id = " . (int)$subjectId;
            $topicResult = $db->query($topicQuery);
            $topics = $db->fetchAll($topicResult);

            $subtopicids = [];
            if (!empty($topics)) {
                foreach ($topics as $topic) {
                    $subtopicQuery = "SELECT id FROM course_topics WHERE parent_id = " . (int)$topic['id'];
                    $subtopicResult = $db->query($subtopicQuery);
                    $subtopicid = $db->fetchAll($subtopicResult);
                    $subtopicids = array_merge($subtopicids, array_column($subtopicid, 'id'));
                }
            }

            if (!empty($subtopicids)) {
                $idList = implode(',', array_map('intval', $subtopicids));
                $paperQuery = "SELECT id, previous_paper_pdf, video_url, subtopic FROM course_subtopics WHERE subtopic IN ($idList)";
                $paperResult = $db->query($paperQuery);
                $papers = $db->fetchAll($paperResult);
            } else {
                $papers = [];
            }
            ?>

            <!-- Tab Contents -->
            <div class="pt-4">
                <?php if ($activeTab === 'video'): ?>
                <div id="video" class="tab-content active">
                    <h4>Video Lessons</h4>
                    <p>This section contains video lessons grouped by topic.</p>
                    <?php
                    if (empty($topics)) {
                        echo '<p>No topics found.</p>';
                    } else {
                        foreach ($topics as $topicIndex => $topic):
                            $topicId = (int)$topic['id'];
                            $topicName = htmlspecialchars($topic['topic']);
                    ?>
                        <div class="mb-3">
                            <button class="topic-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#topic-<?php echo $topicId; ?>">
                                <span class="dropdown-indicator">▾</span>
                                <span class="topic-text"><?php echo 'Topic ' . ($topicIndex + 1) . ' - ' . $topicName; ?></span>
                            </button>
                        </div>
                        <div class="collapse mb-4" id="topic-<?php echo $topicId; ?>">
                            <div class="row">
                                <?php
                                $subtopicQuery = "SELECT id, topic AS subtopic FROM course_topics WHERE parent_id = " . $topicId;
                                $subtopicResult = $db->query($subtopicQuery);
                                $subtopics = $db->fetchAll($subtopicResult);

                                if (!empty($subtopics)):
                                    foreach ($subtopics as $subIndex => $sub):
                                        $subId = (int)$sub['id'];
                                        $subName = htmlspecialchars($sub['subtopic']);
                                        $videoQuery = "SELECT video_url FROM course_subtopics WHERE subtopic = " . $subId . " LIMIT 1";
                                        $videoResult = $db->query($videoQuery);
                                        $videoRow = $db->fetch($videoResult);
                                        $videoUrl = $videoRow['video_url'] ?? '';
                                        $embedUrl = '';
                                        if (!empty($videoUrl) && preg_match('%(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})%', $videoUrl, $matches)) {
                                            $videoId = $matches[1];
                                            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                                        }
                                ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card shadow-sm">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo ($topicIndex + 1) . '.' . ($subIndex + 1) . ' ' . $subName; ?></h6>
                                                    <?php if (!empty($embedUrl)): ?>
                                                        <div class="video-container" id="video-<?php echo $subId; ?>" style="display:none;">
                                                            <div class="ratio ratio-16x9">
                                                                <iframe src="<?php echo $embedUrl; ?>" frameborder="0" allowfullscreen></iframe>
                                                            </div>
                                                        </div>
                                                        <button class="btn btn-outline-secondary mt-2 w-100 watch-video-btn" data-target="#video-<?php echo $subId; ?>">Watch Video</button>
                                                    <?php else: ?>
                                                        <p class="text-muted">No video found for <?php echo $subName; ?>.</p>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                <?php
                                    endforeach;
                                else:
                                    echo '<div class="col-12"><p class="text-muted">No subtopics found.</p></div>';
                                endif;
                                ?>
                            </div>
                        </div>
                    <?php endforeach; } ?>
                </div>
                <script>
                document.querySelectorAll('.watch-video-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const targetId = this.getAttribute('data-target');
                        const target = document.querySelector(targetId);
                        if(target.style.display === 'none'){
                            target.style.display = 'block';
                            this.textContent = 'Hide Video';
                        } else {
                            target.style.display = 'none';
                            this.textContent = 'Watch Video';
                        }
                    });
                });
                </script>
                <?php endif; ?>

                <?php if ($activeTab === 'quiz'): ?>
                <div id="quiz" class="tab-content active">
                    <?php
                    if (empty($topics)) {
                        echo '<p class="text-center">No topics found.</p>';
                    } else {
                        foreach ($topics as $topicIndex => $topic) {
                            $topicId = $topic['id'];
                            $topicName = htmlspecialchars($topic['topic']);
                    ?>
                        <div class="mb-3">
                            <button class="topic-toggle collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#topic-<?php echo $topicId; ?>">
                                <span class="dropdown-indicator">▾</span>
                                <span class="topic-text"><?php echo 'Topic ' . ($topicIndex + 1) . ' - ' . $topicName; ?></span>
                            </button>
                        </div>
                        <div class="collapse mb-4" id="topic-<?php echo $topicId; ?>">
                            <div class="row">
                                <?php
                                $subtopicQuery = "SELECT id, topic as subtopic FROM course_topics WHERE parent_id = " . (int)$topicId;
                                $subtopicResult = $db->query($subtopicQuery);
                                $subtopics = $db->fetchAll($subtopicResult);
                                if (!empty($subtopics)) {
                                    foreach ($subtopics as $subIndex => $sub) {
                                ?>
                                    <div class="col-md-4 mb-3">
                                        <div class="card h-100 shadow-sm">
                                            <div class="card-body d-flex flex-column justify-content-between">
                                                <h6 class="card-title"><?php echo ($topicIndex + 1) . '.' . ($subIndex + 1) . ' ' . htmlspecialchars($sub['subtopic']); ?></h6>
                                                <?php if (isset($_SESSION['quiz_user']['id']) && !empty($_SESSION['quiz_user']['id'])) { ?>
                                                    <a href="<?php echo 'quizfocus?subtopic=' . $sub['id']; ?>" class="btn btn-primary mt-2">Start Quiz</a>
                                                <?php } else { ?>
                                                    <button class="btn btn-primary mt-2 btn-prnt-inner" data-bs-toggle="modal" data-bs-target="#quizSignupModal" data-subtopic-id="<?php echo (int)$sub['id']; ?>">Start Quiz</button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } } else {
                                    echo '<div class="col-12"><p class="text-muted">No subtopics found.</p></div>';
                                } ?>
                            </div>
                        </div>
                    <?php } } ?>
                </div>
                <?php endif; ?>
                 
		<?php if ($activeTab === 'paper'): ?>
                    <div id="paper" class="tab-content active">
                        <div class="accordion accordian-past-paper" id="accordionExample">
                            <div class="accordion-item mb-3">
                                <h2 class="accordion-header" id="headingOne">
                                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                        Paper 2025
                                    </button>
                                </h2>
                                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne">
                                    <div class="accordion-body">
                                        <?php if (!empty($papers)): ?>
                                            <?php $hasPaper = false; ?>
                                            <?php foreach ($papers as $index => $paper): ?>
                                                <?php if (!empty($paper['previous_paper_pdf'])): ?>
                                                    <?php
                                                        $hasPaper = true;
                                                        $paperTitle = date('F') . ' 25 Paper ' . ($index + 1);
                                                        $pdfPath = htmlspecialchars($paper['previous_paper_pdf']);
                                                    ?>
                                                    <div class="paper-list-download mb-2">
                                                        <span><?= $paperTitle ?></span>
                                                        <a href="<?= $pdfPath ?>" target="_blank" rel="noopener noreferrer">Download Question Paper</a>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>

                                            <?php if (!$hasPaper): ?>
                                                <p>No previous papers found.</p>
                                            <?php endif; ?>

                                        <?php else: ?>
                                            <p>No previous papers found.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>		
            </div>
        </div>
    </div>

    <!-- modal-start-quiz -->
    <div class="modal fade" id="quizSignupModal" tabindex="-1" aria-labelledby="quizSignupModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content p-4">
                <div class="modal-body text-center">
                    <h2 class="fw-bold mb-2">Ready to test your Knowledge</h2>
                    <p class="mb-4">Create an account to access the quiz.<br>We'll send your quiz result directly to your gmail</p>
                    <form id="quizSignupForm" novalidate>
                        <input type="text" name="full_name" class="form-control mb-3" placeholder="Enter full name" required>
                        <input type="email" name="email" class="form-control mb-3" placeholder="Enter e-mail" required>
                        <input type="email" name="parent_email" class="form-control mb-3" placeholder="Enter Parents e-mail" required>
                        <input type="tel" name="phone" class="form-control mb-4" placeholder="Enter phone number" required>
                        <input type="hidden" id="subtopicIdField" name="subtopic_id" value="">
                        <button type="button" class="start-quiz-btn w-100 py-2">Start Quiz</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    // set hidden subtopic id when open modal
    document.querySelectorAll('.btn[data-subtopic-id]').forEach(button => {
        button.addEventListener('click', function() {
            const subtopicId = this.getAttribute('data-subtopic-id');
            document.getElementById('subtopicIdField').value = subtopicId;
        });
    });

    $('.start-quiz-btn').on('click', function() {
        const form = $('#quizSignupForm');
        const formData = form.serialize();
        fcom.ajax(fcom.makeUrl('Quizizz', 'submitSignup'), formData, function(response) {
            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }
                if (response.status == 1 && response.subtopicid) {
                    window.location.href = '/quizfocus?subtopic=' + encodeURIComponent(response.subtopicid);
                } else {
                    alert(response.msg || "Something went wrong.");
                }
            } catch (e) {
                console.error("Invalid JSON:", e);
                alert("Failed to parse server response.");
            }
        });
    });
</script>

<style>
/* Dropdown auto expand - no scroll */
.show-all-items {
    max-height: none !important;
    overflow: visible !important;
}
</style>
<script>
    var questions = <?php echo json_encode($questionData ?? []); ?>;
</script>
<script>
    // When any button with .btn-prnt-inner is clicked
    document.querySelectorAll('.btn-prnt-inner').forEach(button => {
        button.addEventListener('click', function() {
            const subtopicId = this.getAttribute('data-subtopic-id');

            // Set it inside the modal field
            const hiddenField = document.getElementById('subtopicIdField');
            if (hiddenField) {
                hiddenField.value = subtopicId;
            }

            // (Optional) Also show in a visible span
            const displaySpan = document.getElementById('showSubtopicId');
            if (displaySpan) {
                displaySpan.textContent = subtopicId;
            }
        });
    });
</script>

<!-- <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>-->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>  -->
<script>
    $('.start-quiz-btn').on('click', function() {
        const form = $('#quizSignupForm');


        // Get actual form data manually (for testing)
        const formData = form.serialize();

        fcom.ajax(fcom.makeUrl('Quizizz', 'submitSignup'), formData, function(response) {

            try {
                // Parse JSON if it's a string
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                console.log('AJAX response:', response.subtopicid);

                // Optional: Check for success before redirecting
                if (response.status == 1 && response.subtopicid) {
                    window.location.href = '/quizfocus?subtopic=' + encodeURIComponent(response.subtopicid);
                } else {
                    alert(response.msg || "Something went wrong.");
                }

            } catch (e) {
                console.error("Invalid JSON:", e);
                alert("Failed to parse server response.");
            }

        });



    });







    var _body = $('body');
    var _toggle = $('.js-filter-toggle');
    _toggle.each(function() {
        var _this = $(this),
            _target = $(_this.attr('href'));

        _this.on('click', function(e) {
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


    var userSessionId = "<?php echo $_SESSION['subtopicId']; ?>";



    function openTab(event, tabId) {
        // Remove active class from all tabs and content
        document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
        document.querySelectorAll('.tab-content').forEach(c => c.classList.remove('active'));

        // Add active class to the selected tab and content
        event.target.classList.add('active');
        document.getElementById(tabId).classList.add('active');
    }
</script>

