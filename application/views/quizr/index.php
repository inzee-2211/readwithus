<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();



?>

<style>
    .hidden {
        display: none;
    }

    .quiz-container {
        width: 900px;
        margin: auto;
        /* background: #fff; */
        padding: 20px;
        border-radius: 10px;
        /* box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1); */
        text-align: center;
    }

    .quiz-question h3 {
        font-size: 20px;
        color: #333;
        margin-bottom: 15px;
    }

    .quiz-hint {
        color: #007bff;
        font-style: italic;
        margin-top: 10px;
    }

    .quiz-explanation {
        color: #28a745;
        font-weight: bold;
        margin-top: 10px;
    }

    .quiz-options {
        text-align: left;
    }

    .quiz-option {
        display: block;
        background: #f5f5f5;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        cursor: pointer;
    }

    .quiz-option input {
        margin-right: 8px;
    }

    .quiz-navigation {
        margin-top: 20px;
    }

    .btn {
        padding: 10px 20px;
        border: none;
        font-size: 14px;
        cursor: pointer;
        border-radius: 6px;
        transition: 0.2s;
    }

    .btn--primary {
        background: #4CAF50;
        color: white;
    }

    .btn--primary:hover {
        background: #45a049;
    }

    .btn--secondary {
        background: #ccc;
        color: black;
    }

    .btn--secondary:hover {
        background: #bbb;
    }

    .btn--info {
        background: #007bff;
        color: white;
        margin-bottom: 10px;
    }

    .btn--info:hover {
        background: #0056b3;
    }

    .quiz-header {
        text-align: center;
        font-size: 18px;
        font-weight: bold;
        color: red;
    }
</style>

<div class="page-listing__body">

    <div class="container mb-5">
        <div class="row mt-5">
            <div class="col-12 text-center">
                <?php


                if (!empty($attemptresult[0]['result'])) {
                  $resultText = strtolower(trim($attemptresult[0]['result'])); // ✅ Yahan define karo
                $subtopic_id = $attemptresult[0]['subtopic_id'];
                $textColor = $resultText === 'fail' ? 'text-red' : 'text-success';                  
                ?>
                    <h1 class="">Quiz Result: <span class="<?= $textColor; ?>"><?= ucfirst($resultText); ?></span></h1>
                <?php
                } else {
                    echo '<h1 class="">Quiz Result: <span class="text-muted">Not available</span></h1>';
                }
                ?>

            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                  <div class="header mb-3">
                    <h2>
                        <?php if (isset($subtopicName)) { ?>
                            Result for Subtopic: <?php echo htmlspecialchars($subtopicName); ?>
                        <?php } else { ?>
                            Your Quiz Result
                        <?php } ?>
                    </h2>
		</div>

                <div class="d-flex align-items-center justify-content-start gap-4 mb-3">
                    <div class="d-flex align-items-center justify-content-start gap-4">


                        <?php
                        if (!empty($attemptresult[0]['created_at'])) {
                            $datetime = new DateTime($attemptresult[0]['created_at']);

                            $dateFormatted = $datetime->format('M d, Y');

                            $timeFormatted = $datetime->format('h:i A');
                        ?>
                            <p class="mb-0">Finished <?= $dateFormatted; ?></p>
                            <p class="mb-0"><?= $timeFormatted; ?></p>
                        <?php
                        } else {
                            echo '<p class="mb-0">No finish time available.</p>';
                        }
                        ?>

                    </div>
                    <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img//mail.svg" alt="" class="img-aug">
                        <?php
                        if (!empty($attemptresult[0]['total_questions'])) {
                            $totalQuestions = (int) $attemptresult[0]['total_questions'];
                            echo '<p class="mb-0">' . $totalQuestions . ' Questions</p>';
                        } else {
                            echo '<p class="mb-0">No questions found</p>';
                        }
                        ?>

                    </div>
                </div>



                <div class="row p-4 gap-quiz">
                    <?php
                    $baseUrl = getBaseUrl();
                    $tickIcons = [
                        1 => 'green-tick.svg', // Correct
                        0 => 'red-tick.svg',   // Incorrect
                    ];

                    foreach ($attemptquestions as $index => $question) {
                        $questionNumber = $index + 1;
                        $tick = $tickIcons[$question['is_correct']] ?? 'red-tick.svg'; // Fallback to red
                    ?>
                        <div class="col-auto">
                            <h3><?php echo $questionNumber; ?>
                                <img src="<?php echo $baseUrl . 'assets/img/' . $tick; ?>" alt="" class="img-tick">
                            </h3>
                        </div>
                    <?php } ?>
                </div>



                <div class="quiz-result-lst d-flex align-items-center justify-content-start gap-5">
                    <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/g-1.svg" alt="" class="img-aug">
                        <?php
                        if (!empty($attemptresult[0]['total_correct'])) {
                            $correct = (int) $attemptresult[0]['total_correct'];
                            echo '<p class="mb-0">Correct ' . $correct . '</p>';
                        } else {
                            echo '<p class="mb-0">Correct 0</p>';
                        }
                        ?>

                    </div>
                    <div class="d-flex align-items-center justify-content-start gap-4">
		      	<img src="<?php echo getBaseUrl(); ?>assets/img/greyy.svg" alt="" class="img-aug">
                           <p class="mb-0">
                            <?php echo isset($correct) ? $correct : 0; ?>0%
			</p> 

                    </div>
                    <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/r-1.svg" alt="" class="img-aug">
                        <?php
                        $totalQuestions = count($attemptquestions);
                        $correct = 0;

                        if (!empty($attemptquestions)) {
                            foreach ($attemptquestions as $question) {
                                if (!empty($question['is_correct']) && $question['is_correct'] == 1) {
                                    $correct++;
                                }
                            }
                        }

                        $incorrect = $totalQuestions - $correct;
                        ?>

                        <p class="mb-0">Incorrect <?= $incorrect; ?></p>
                    </div>
                    <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/dot-grey.svg" alt="" class="">
                        <p class="mb-0"><?php echo $incorrect; ?>0%</p>
                    </div>
                    <!-- <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/greyy.svg" alt="" class="img-aug">
                        <p class="mb-0">Skiped 0</p>
                    </div> -->
                    <!-- <div class="d-flex align-items-center justify-content-start gap-4">
                        <img src="<?php echo getBaseUrl(); ?>assets/img/dot-grey.svg" alt="" class="">
                        <p class="mb-0">0%</p>
                    </div> -->
                </div>

            </div>
            <div class="col-md-4">
                <div class="content mb-4">
                    <div class="d-flex justify-content-end gap-4 bar-result-wrap">
                        <div class="d-flex flex-column justify-content-center">


                            <?php
                            $totalQuestions = count($attemptquestions);
                            $correct = 0;

                            foreach ($attemptquestions as $question) {
                                if (!empty($question['is_correct']) && $question['is_correct'] == 1) {
                                    $correct++;
                                }
                            }

                            $accuracy = $totalQuestions > 0 ? round(($correct / $totalQuestions) * 100) : 0;
                            ?>

                            <div class="">
                                <h4>Accuracy</h4>
                                <div class="d-flex bar-wrap align-items-center">
                                    <div class="circle-wrap position-relative">
                                        <svg viewBox="0 0 80 80">
                                            <circle class="circle-bg" cx="40" cy="40" r="35" />
                                            <circle class="circle-progress" cx="40" cy="40" r="35"
                                                style="stroke-dasharray: 220; stroke-dashoffset: <?= 220 - (220 * $accuracy / 100); ?>;" />
                                        </svg>
                                        <div class="inside-text" id="percentText"><?= $accuracy ?>%</div>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="">
                                <h4>Score</h4>
                            </div>
                            <div class="text-center">
                                <h4><?php echo $correct; ?>/10</h4>
                            </div>
                        </div>
                        <div class="d-flex flex-column justify-content-center">
                            <div class="">
                                <h4>Answered</h4>
                            </div>
                            <div class="text-center">
                                <h4>10/10</h4>
                            </div>
                        </div>
                    </div>
                </div>

                 <?php
                    $db = FatApp::getDb();
                    $query = "SELECT video_url FROM course_subtopics WHERE subtopic = " . (int)$subtopic_id . " LIMIT 1";
                    $result = $db->query($query);
                    $videoUrl = '';

                    if ($result) {
                        $row = $db->fetch($result);
                        $videoUrl = $row['video_url'] ?? '';
                    }
                    ?>

                    <div class="vide-quiz-wrap embed-responsive embed-responsive-16by9">
                        <?php if (!empty($videoUrl)) {
                            // extract YouTube video id
                            $videoId = '';
                            if (preg_match('%(?:youtube\.com/(?:watch\?v=|embed/)|youtu\.be/)([a-zA-Z0-9_-]{11})%', $videoUrl, $matches)) {
                                $videoId = $matches[1];
                            }
                            $embedUrl = 'https://www.youtube.com/embed/' . $videoId;
                            ?>
                            <iframe width="560" height="315" src="<?php echo htmlspecialchars($embedUrl); ?>" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                allowfullscreen></iframe>
                        <?php } else { ?>
                            <!-- fallback video sirf jab DB me subtopic ki video nahi ho -->
                            <iframe width="928" height="522" src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw"
                                title="Default Recommended Video" frameborder="0"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen></iframe>
                        <?php } ?>
                    </div>

                    <div class="text-center pt-3">
                        <h5>Recommended Video For<br>
                            <?php echo htmlspecialchars($_SESSION['subtopicName'] ?? 'Topic Preparation'); ?>
                        </h5>
                    </div>
            </div>
        </div>
        <div class="row pt-3 justify-content-center">
    <div class="col-md-10">
        <div class="card card-quiz p-4">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-between">
                    <p class="mb-0">
                        Thank you for completing the quiz! Review your answers and keep practicing to strengthen your understanding of this subject.
                    </p>

                       <?php if (isset($resultText) && isset($currentSubtopicId)): ?>
                        <?php if ($resultText === 'pass'): ?>
                            <a href="https://www.readwithus.org.uk/quizizz?subtopic=<?= $currentSubtopicId ?>" class="btn btn-success p-3">Next Suggested Quiz</a>
                        <?php else: ?>
                            <a href="https://www.readwithus.org.uk/quizattemptall?subtopic=<?= $currentSubtopicId ?>" class="btn btn-brown p-3">Requiz</a>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

    </div>



    <section class="tutors-section py-4">
        <div class="container">
            <div class="row py-4 slider-parent-55">

                <?php
                $filteredCourses = array_filter($coursesslider, function ($course) {
                    return isset($course['course_details']) && strlen(trim($course['course_details'])) >= 50; // adjust threshold as needed
                });

                // If any valid courses found, pick one at random
                if (!empty($filteredCourses)) {
                    $filteredCourses = array_values($filteredCourses); // reindex to prevent array_rand error
                    $randomKey = array_rand($filteredCourses);
                    $randomCourse = $filteredCourses[$randomKey];
                }
                ?>


                <div class="col-md-6 col-left">
                    <div class="header mb-3">
                        <h1 class="mb-2">Recommended <br> courses</h1>
                        <h3 class="mb-2"> <?php
                                            echo (strlen($randomCourse['course_title']) > 70) ? CommonHelper::renderHtml(substr($randomCourse['course_title'], 0, 70)) . '...' : CommonHelper::renderHtml($randomCourse['course_title']); ?></h3>
                        <p class="mb-4">


                            <iframe srcdoc="<?php echo $randomCourse['course_details']; ?>" style="border:none;width: 100%;height: 100%;"></iframe>
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                            <button class="btn btn-brown p-3"><a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$randomCourse['course_slug']]); ?>"> Start Learning </a></button>
                            <h2 class="mb-0"> <?php echo CourseUtility::formatMoney($randomCourse['course_price']); ?></h2>
                        </div>
                    </div>
                </div>



                <div class="col-md-6 col-right">
                    <div class="owl-custom owl-carousel owl-theme best-buy">




                        <?php foreach ($coursesslider as $crs) { ?>
                            <div class="item">
                                <div>
                                    <img style="min-height: 330px;" src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="<?php echo $crs['course_title']; ?>" alt="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                    <div class="font-6 d-flex  justify-content-end mt-3">
                                        <div class="rating">
                                            <svg class="rating__media">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#rating"></use>
                                            </svg>
                                            <span class="rating__value">
                                                <?php echo $crs['course_ratings']; ?>
                                            </span>
                                            <span class="rating__count">
                                                <?php echo '(' . $crs['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')'; ?>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="font-6 ">
                                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>"> <?php
                                                                                                                                echo (strlen($crs['course_title']) > 70) ? CommonHelper::renderHtml(substr($crs['course_title'], 0, 70)) . '...' : CommonHelper::renderHtml($crs['course_title']); ?> </a>
                                    </div>
                                    <div class="font-6 ">
                                        <?php echo CommonHelper::renderHtml($crs['subcate_name']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php } ?>




                    </div>
                </div>
            </div>
        </div>
    </section>
    <!--  -->
    <section class="py-4">
        <div class="container">
            <div class="row py-4 slider-parent-55">
                <div class="col-md-6 col-left">
                    <img src="<?php echo getBaseUrl(); ?>/assets/img/user.png">
                </div>

                <?php
                $filteredCourses = array_filter($coursesslider, function ($course) {
                    return isset($course['course_details']) && strlen(trim($course['course_details'])) >= 50; // adjust threshold as needed
                });

                // If any valid courses found, pick one at random
                if (!empty($filteredCourses)) {
                    $filteredCourses = array_values($filteredCourses); // reindex to prevent array_rand error
                    $randomKey = array_rand($filteredCourses);
                    $randomCourse = $filteredCourses[$randomKey];
                }
                ?>

                <div class="col-md-6 col-right">
                    <div class="header mb-3">
                        <h1 class="mb-2">Recommended <br> Tutor</h1>
                        <p class="mb-4">

                            <!-- <iframe srcdoc="<?php echo $randomCourse['course_details']; ?>" style="border:none;width: 100%;height: 100%;"></iframe> -->

                            The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, 
                            as opposed to using 'Content here, content hereThe point of using Lorem Ipsum is that it has 
                            a more-or-less normal distribution of letters, as opposed to using 'Content here, content hereThe 
                            point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed 
                            to using 'Content here, content hereThe point of using Lorem Ipsum is that it hacontent hIpsum is that 
                            it has a more-or-less normal distribution of letters, as opposed to using 'Content 
                        </p>
                        <div class="d-flex align-items-center justify-content-between">
                        <button class="btn btn-black p-3" data-bs-toggle="modal" data-bs-target="#quizSignupModal">Find a Tutor</button>
                           
 
                            <!-- <button class="btn btn-black p-3"><a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$randomCourse['course_slug']]); ?>"> Start Learning </a></button>
                             -->
                            
                            <!-- <h2 class="mb-0"><?php echo CourseUtility::formatMoney($randomCourse['course_price']); ?></h2> -->
                        </div>
                    </div>
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
                                <input type="text" name="subject" class="form-control mb-3" placeholder="Enter Subject" required>
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
   <!-- <section class="testimonial-wrap">
        <div class="d-flex justify-content-center flex-column align-items-center">
            <h5 class="test-head">TESTIMONIAL</h5>
            <h3 class="what-heading ft-book">What our clients say about us.</h3>
            <div class="container mt-5">
                <div class="owl-custom owl-carousel owl-theme testimonial-slider">
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                    <div class="item">
                        <div class="testimonial-box">
                            <img src="<?php echo getBaseUrl(); ?>/assets/img/user-fake.png" alt="" class="img-testimonial">
                            <h4 class="ft-book">James Pattinson</h4>
                            <div class="d-flex justify-content-center align-items-center">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-gold.svg">
                                <img src="<?php echo getBaseUrl(); ?>/assets/img/star-grey.svg">
                            </div>
                            <p>“Lobortis leo pretium facilisis amet nisl at nec. Scelerisque risus tortor donec ipsum consequat semper consequat adipiscing ultrices.”</p>


                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-center align-items-center mt-5">
                    <button class="get_quote_btn mt-5" data-bs-toggle="modal" data-bs-target="#modal-multi-video">Get a quote</button>
                </div>
            </div>
        </div>
    </section>-->


    <div class="modal fade" id="modal-multi-video" tabindex="-1" aria-labelledby="modal-multi-videoLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content modal-transparent">
                <div class="modal-body">
                    <div class="row justify-content-center">
                        <div class="col-md-10 ">
                            <div class="vid-multi-wrap">
                                <iframe src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw" title="Sundriver - Pangea [Silk Music]" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                            </div>
                            <div class="row mt-4">
                                <div class="col-md-4">
                                    <div class="vid-multi-child">
                                        <iframe src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw" title="Sundriver - Pangea [Silk Music]" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="vid-multi-child">
                                        <iframe src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw" title="Sundriver - Pangea [Silk Music]" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="vid-multi-child">
                                        <iframe src="https://www.youtube.com/embed/rLCn1aO_4Kw?list=RDrLCn1aO_4Kw" title="Sundriver - Pangea [Silk Music]" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>

    <!-- <div class="pagination pagination--centered margin-top-10">
        <?php

        echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
        $pagingArr = ['page' => $post['pageno'], 'pageCount' => $pageCount, 'recordCount' => $recordCount, 'callBackJsFunc' => 'gotoPage'];
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
        ?>
    </div> -->
    <?php
    // $checkoutForm->setFormTagAttribute('class', 'd-none');
    // $checkoutForm->setFormTagAttribute('name', 'frmCheckout');
    // $checkoutForm->setFormTagAttribute('id', 'frmCheckout');
    // echo $checkoutForm->getFormHtml();
    ?>
</div>
<script>

    $('.start-quiz-btn').on('click', function() {
        const form = $('#quizSignupForm');
        const formData = form.serialize();

        fcom.ajax(fcom.makeUrl('Quizizz', 'submitfindatutor'), formData, function(response) {

            try {
                if (typeof response === 'string') {
                    response = JSON.parse(response);
                }

                console.log('AJAX response:', response.subtopicid);
               if (response.status == 1 && response.insertedid) {
                  form[0].reset();
                
    $('#quizSignupModal').modal('hide');
   $('.modal-backdrop').remove();
 $('body').removeClass('modal-open');
    $('body').css('overflow', 'auto');
                   //  window.location.reload();
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
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> -->
 <!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" /> -->
     
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>


<script src="<?php echo getBaseUrl(); ?>/assets/js/custom-web.js"></script>
 -->


<script>
    var questions = <?php echo json_encode($questionData ?? []); ?>;
</script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
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

    function fetchQuestions() {
        $.ajax({
            url: fcom.makeUrl('Quizattempt', 'getQuestions'),
            type: "POST",
            data: {
                pageno: 1,
                subtopicid: userSessionId
            },
            dataType: "json",
            success: function(response) {
                if (response.success) {
                    questions = response.data;
                    loadQuestion(0);
                    startTimer();
                } else {
                    alert("No questions found.");
                }
            },
            error: function(xhr, status, error) {
                console.error("Error fetching questions:", error);
            }
        });
    }

    // Function to start the timer (ensuring only one instance runs)
    function startTimer() {
        if (timerInterval !== null) return;

        timerInterval = setInterval(() => {
            if (timerDuration <= 0) {
                clearInterval(timerInterval);
                alert("Time is up! Submitting quiz...");
                submitQuiz();
            }

            let minutes = Math.floor(timerDuration / 60);
            let seconds = timerDuration % 60;
            document.getElementById("timer").innerText =
                `Time Left: ${minutes}:${seconds < 10 ? "0" : ""}${seconds}`;

            timerDuration--;
        }, 1000);
    }




    function loadQuestion(index) {
        if (!questions || !questions[index]) {
            console.error("❌ Invalid question index:", index);
            return;
        }

        const questionData = questions[index];
        const optionsContainer = document.getElementById("quiz-options");
        optionsContainer.innerHTML = "";

        document.getElementById("question-text").innerText = `Question ${index + 1}: ${questionData.text}`;

        // === SINGLE & MULTI CHOICE ===
        if (questionData.type === "Single-Choice" || questionData.type === "Multiple-Choice") {
            questionData.options.forEach((option, i) => {
                const optionId = `q${index}_opt${i}`;
                const wrapper = document.createElement("div");
                wrapper.className = "quiz-option";

                const input = document.createElement("input");
                input.type = questionData.type === "Single-Choice" ? "radio" : "checkbox";
                input.name = `question-${index}`;
                input.id = optionId;
                input.value = String.fromCharCode(65 + i); // A, B, C...

                const savedAnswer = userAnswers[index];

                // restore answer
                if (questionData.type === "Single-Choice" && savedAnswer === input.value) {
                    input.checked = true;
                }
                if (questionData.type === "Multiple-Choice" && Array.isArray(savedAnswer) && savedAnswer.includes(
                        input.value)) {
                    input.checked = true;
                }

                input.addEventListener("change", () => {
                    if (questionData.type === "Single-Choice") {
                        userAnswers[index] = input.value;
                    } else {
                        const selected = Array.from(optionsContainer.querySelectorAll(
                                "input[type='checkbox']:checked"))
                            .map(el => el.value);
                        userAnswers[index] = selected;
                    }
                    console.log("✅ Updated userAnswers:", JSON.parse(JSON.stringify(userAnswers)));
                });

                const label = document.createElement("label");
                label.setAttribute("for", optionId);
                label.textContent = `${input.value}) ${option}`;

                wrapper.appendChild(input);
                wrapper.appendChild(label);
                optionsContainer.appendChild(wrapper);
            });
        }

        // === STORY-BASED / TEXT ===
        if (questionData.type === "Story-Based") {
            const input = document.createElement("input");
            input.type = "text";
            input.id = "text-answer";
            input.placeholder = "Type your answer here...";
            input.value = userAnswers[index] || "";

            input.addEventListener("input", function() {
                userAnswers[index] = this.value;
                console.log("✅ Updated userAnswers:", JSON.parse(JSON.stringify(userAnswers)));
            });

            optionsContainer.appendChild(input);
        }

        // Hint & Explanation UI
        document.getElementById("hint-text").innerText = questionData.hint || "";
        document.getElementById("explanation-text").innerText = questionData.explanation || "";
        document.getElementById("hint-btn").classList.toggle("hidden", !questionData.hint);

        // Buttons
        document.getElementById("prev-btn").disabled = index === 0;
        document.getElementById("next-btn").innerText = index === questions.length - 1 ? "Submit" : "Next";
    }


    document.addEventListener("DOMContentLoaded", function() {
        const quizOptions = document.getElementById("quiz-options");
        if (quizOptions) {
            quizOptions.addEventListener("change", function(event) {
                const answer = event.target.value;
                const questionIndex = parseInt(event.target.getAttribute("data-index"));

                if (questionData.type === "Multiple-Choice") {
                    if (!userAnswers[questionIndex]) {
                        userAnswers[questionIndex] = [];
                    }
                    if (!userAnswers[questionIndex].includes(answer)) {
                        userAnswers[questionIndex].push(answer);
                    }
                } else {
                    userAnswers[questionIndex] = answer;
                }
            });
        } else {
            console.warn("Element with ID 'quiz-options' not found.");
        }
    });



    // document.getElementById(`text-answer-${index}`).addEventListener("input", function () {
    //     userAnswers[index] = this.value.trim();
    // });



    // Show Hint
    document.getElementById("hint-btn").addEventListener("click", function() {
        document.getElementById("hint-text").classList.toggle("hidden");
    });

    // Show Explanation when an answer is selected
    document.getElementById("quiz-options").addEventListener("change", function() {
        document.getElementById("explanation-text").classList.remove("hidden");
    });

    // Submit Quiz Function
    // function submitQuiz() {
    //     clearInterval(timerInterval); // Stop timer on submit
    //     alert("Quiz Submitted!");
    //     window.location.href = "quizresults"; // Redirect to results page
    // }


    function submitQuiz() {

        clearInterval(timerInterval);
        // document.getElementById("submit-btn").disabled = true;
        saveAnswer(currentQuestion);
        console.log("User Answers:", userAnswers);

        $.ajax({
            url: fcom.makeUrl('Quizattempt', 'submitAnswers'),
            type: "POST",
            data: {
                answers: JSON.stringify(userAnswers),
                subtopicid: userSessionId
            },
            dataType: "json",
            success: function(response) {
                console.log("✅ Server Response:", response);

                if (response.success) {

                    const resultStatus = response.status;
                    const marks = response.marksObtained; // assuming `marks` is returned
                    const userName = response.userName || 'User';

                    if (resultStatus === 'pass') {

                        // Swal.fire({
                        //     icon: 'success',
                        //     title: '🎉 Yay! You Passed!',
                        //     html: `
                        //         <p style="font-size: 16px;">You scored <strong style="color: green;">${marks}</strong> points!</p>
                        //         <img src="https://media.giphy.com/media/111ebonMs90YLu/giphy.gif" alt="Congrats" style="margin:auto;max-width:200px;" />
                        //         <p style="font-size: 14px;">Ready for a <strong>Certification Exam (£10)</strong> or want to <strong>explore more fun topics</strong>? 🤓</p>
                        //     `,
                        //     showCancelButton: true,
                        //     confirmButtonText: '🎖️ Take Exam',
                        //     cancelButtonText: '🧠 Explore More',
                        //     background: '#e6fffa',
                        //     confirmButtonColor: '#4caf50',
                        //     cancelButtonColor: '#03a9f4',
                        // }).then((result) => {
                        //     if (result.isConfirmed) {
                        //         window.location.href = '/certification-exam'; // ✅ Update to your actual exam route
                        //     } else if (result.dismiss === Swal.DismissReason.cancel) {
                        //         window.location.href = '/courses'; // ✅ Update to your advanced courses page
                        //     }
                        // });


                    } else {

                        // Swal.fire({
                        //     icon: 'error',
                        //     title: '😢 Oops! You didn\'t pass!',
                        //     html: `
                        //         <p style="font-size: 16px;">You scored <strong style="color: red;">${marks}</strong> points.</p>
                        //          <img src="https://media.giphy.com/media/3og0IPxMM0erATueVW/giphy.gif" alt="Try Again" style="margin:auto;max-width:200px; " />
                        //         <p style="font-size: 14px;">Would you like help from a tutor or try some videos? 📚</p>
                        //     `,
                        //     showCancelButton: true,
                        //     confirmButtonText: '👨‍🏫 Get Tutor Help',
                        //     cancelButtonText: '🎬 Watch Videos',
                        //     background: '#fff3f3',
                        //     confirmButtonColor: '#f44336',
                        //     cancelButtonColor: '#ff9800',
                        // }).then((result) => {
                        //     if (result.isConfirmed) {
                        //         window.location.href = '/teachers'; // ✅ Update to your tutor list page
                        //     } else if (result.dismiss === Swal.DismissReason.cancel) {
                        //         window.location.href = '/struggle-topics-videos'; // ✅ Update to your video suggestions page
                        //     }
                        // });


                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Submission Failed',
                        text: '❌ Failed to submit quiz. Try again.',
                    });
                }

            },
            error: function(xhr, status, error) {
                console.error("🚨 Error submitting quiz:", error);
                console.warn("📦 Raw Response:", xhr.responseText);
                alert("An error occurred while submitting the quiz.");
            }
        });
    }


    function calculateGrade(score) {
        const percentage = (score / questions.length) * 100;
        if (percentage >= 90) return "A";
        if (percentage >= 80) return "B";
        if (percentage >= 70) return "C";
        if (percentage >= 60) return "D";
        return "F";
    }



    function saveAnswer(index) {
        const question = questions[index];
        let answer = null;

        if (question.type === "Single-Choice") {
            const selectedOption = document.querySelector(`input[name="question-${index}"]:checked`);
            if (selectedOption) {
                answer = selectedOption.value;
            }
        }

        if (question.type === "Multiple-Choice") {
            const selectedOptions = document.querySelectorAll(`input[name="question-${index}"]:checked`);
            answer = Array.from(selectedOptions).map(opt => opt.value);
        }

        if (question.type === "Story-Based") {
            const textInput = document.getElementById("text-answer");
            if (textInput) {
                answer = textInput.value.trim();
            }
        }

        // Save as object: questionId + answer
        userAnswers[index] = {
            questionId: question.id,
            answer: answer
        };

        console.log("✅ Updated userAnswers:", userAnswers);
    }





    document.getElementById("next-btn").addEventListener("click", function() {
        const isValid = validateAnswer(currentQuestion);
        if (!isValid) {
            alert("Please answer the question before proceeding.");
            return;
        }

        saveAnswer(currentQuestion);

        if (currentQuestion < questions.length - 1) {
            currentQuestion++; // 👈 increment it here!
            loadQuestion(currentQuestion);
        } else {
            submitQuiz();
        }
    });



    function validateAnswer(index) {
        const question = questions[index];
        console.log("🔍 Validating Q#", index, "Type:", question.type);

        if (question.type === "Single-Choice") {
            const selected = document.querySelector(`input[name="question-${index}"]:checked`);
            console.log("Selected single option:", selected);
            return !!selected;
        }

        if (question.type === "Multiple-Choice") {
            const selected = document.querySelectorAll(`input[name="question-${index}"]:checked`);
            console.log("Selected multiple options:", selected.length);
            return selected.length > 0;
        }

        if (question.type === "Story-Based") {
            const input = document.getElementById("text-answer");
            console.log("Story input value:", input?.value);
            return input && input.value.trim().length > 0;
        }

        return false;
    }




    document.getElementById("prev-btn").addEventListener("click", function() {
        saveAnswer(currentQuestion);
        if (currentQuestion > 0) {
            currentQuestion--;
            loadQuestion(currentQuestion);
        }
    });





    // Load the first question and start the timer (only once)
    window.onload = function() {
        loadQuestion(0);
    };
    startTimer();
    fetchQuestions();
</script>
