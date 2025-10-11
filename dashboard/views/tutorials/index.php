<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
echo $this->includeTemplate('tutorials/head-section.php', [
    'progress' => $progress,
    'progressId' => $progressId,
    'siteLangId' => $siteLangId,
    'siteUserId' => $siteUserId,
    'siteUserType' => $siteUserType,
    'course' => $course,
    'controllerName' => $controllerName,
    'action' => $actionName
]);
?>
<!-- [ BODY ========= -->
<div class="body">
    <!-- [ BODY PANEL ========= -->
    <div class="body-panel">
        <div class="section-intro videoContentJs">

        </div>
        <div class="section-layout">
            <div class="section-layout__head">
                <div class="container">
                    <div class="breadcrumbs">
                        <ul>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Learner') ?>">
                                    <?php echo Label::getLabel('LBL_DASHBOARD') ?>
                                </a>
                            </li>
                            <li>
                                <a href="<?php echo MyUtility::makeUrl('Courses') ?>">
                                    <?php echo Label::getLabel('LBL_MY_COURSES'); ?>
                                </a>
                            </li>
                            <li><?php echo $course['course_title']; ?></li>
                        </ul>
                    </div>
                    <h2 class="page-subtitle margin-bottom-6 lectureTitleJs"></h2>
                    <div class="section-links">
                        <div class="section-links__left">
                            <nav class="tabs tabs--line border-bottom-0 tabs-scrollable-js tutorialTabsJs">
                                <ul>
                                    <li class="d-xl-none d-block responsive-toggle-js">
                                        <a href="javascript:void(0);">
                                            <?php echo Label::getLabel('LBL_COURSE_LECTURES'); ?>
                                        </a>
                                    </li>
                                    <li class="is-active">
                                        <a href="javascript:void(0);" class="crsDetailTabJs" onclick="loadLecture(0);">
                                            <?php echo Label::getLabel('LBL_LECTURE_DETAIL'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <?php // echo '<pre';print_r($course);die; ?>
                                        <a href="javascript:void(0);" onclick="getNotes('<?php echo $progress['crspro_ordcrs_id']; ?>');">
                                            <?php echo Label::getLabel('LBL_NOTES'); ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="javascript:void(0);" onclick="getReviews();">
                                            <?php echo Label::getLabel('LBL_REVIEWS') . ' (' . $course['course_reviews'] . ')'; ?>
                                        </a>
                                    </li>
                                    <!-- <li>
                                        <a href="javascript:void(0);" onclick="getTutorInfo();">
                                            <?php echo stripslashes(Label::getLabel("LBL_TUTOR'S_INFO")); ?>
                                        </a>
                                    </li> -->
                                  
                                </ul>
                            </nav>
                        </div>
                        <div class="section-links__right">
                        </div>
                    </div>
                </div>
            </div>
            <div class="section-layout__body">
                <div class="container">
                    <!-- [ BODY RIGHT PANEL ========= -->
                    <sidebar class="body-side">
                        <div class="toggle-control-list responsive-target-js sidebarPanelJs">
                            <?php
                            if ($sections) {
                                $i = 1;
                                foreach ($sections as $section) { ?>
                                    <div class="toggle-control control-group-js sectionListJs">
                                        <div class="toggle-control__action control-trigger-js">
                                            <h6>
                                                <?php
                                                echo Label::getLabel('LBL_SECTION') . ' ' . $section['section_order'] . ': ';
                                                echo $section['section_title'];
                                                ?>
                                            </h6>
                                            <p>
                                                <span class="completedLecture<?php echo $section['section_id'] ?>">
                                                    <?php echo isset($lectureStats[$section['section_id']]) ? count($lectureStats[$section['section_id']]) : 0; ?>
                                                </span>
                                                <?php
                                                echo ' / ' . $section['section_lectures'];
                                                $duration = YouTube::convertDuration($section['section_duration']);
                                                echo !empty($duration) ? ' | ' . $duration : '';
                                                ?>
                                            </p>
                                        </div>
                                        <div class="toggle-control__target control-target-js">
                                            <div class="lecture-list lecturesListJs">
                                                <!-- [ LECTURE ========= -->
                                                <?php
                                                if (isset($section['lectures']) && count($section['lectures']) > 0) {
 

                                                    foreach ($section['lectures'] as $lesson) {

                                                         
                                                        $isCovered = (in_array($lesson['lecture_id'], $lectureStats[$section['section_id']])) ? true : false;
                                                        $isActive = ($progress['crspro_lecture_id'] == $lesson['lecture_id']) ? 'is-active' : '';
                                                ?>
                                                        <div class="lecture <?php echo $isActive; ?>" id="lectureJs<?php echo $lesson['lecture_id']; ?>">
                                                            <div class="lecture__control is-hover">
                                                            <?php  if(isset($lesson['quiz_attempt_status']) && $lesson['quiz_attempt_status']=='locked'){
                                                            }else{
                                                            ?>
                                                                <label class="lecture-checkbox">
                                                                    <input type="checkbox" name="lecture_id" data-section="<?php echo $section['section_id']; ?>" value="<?php echo $lesson['lecture_id']; ?>" <?php echo ($isCovered) ? 'checked="checked"' : ''; ?>>
                                                                    <i class="lecture-checkbox__view"></i>
                                                                </label>
                                                                <?php } ?>
                                                                <div class="tooltip tooltip--right bg-black">
                                                                    <?php echo Label::getLabel('LBL_MARK_READ'); ?>
                                                                </div>
                                                            </div>

                                                           <?php  if(isset($lesson['quiz_attempt_status']) && $lesson['quiz_attempt_status']=='locked'){
                                                            ?>
                                                             <div class="lecture__content" onclick="loadLectureShowmsg('<?php echo $lesson['lecture_id']; ?>');">
                                                              
                                                          <?php }else{ ?> 
                                                            <div class="lecture__content" onclick="loadLecture('<?php echo $lesson['lecture_id']; ?>');">
                                                                <?php } ?>
                                                                <p class="lectureName">
                                                                    <?php echo $lesson['lecture_order'] . '. ' . $lesson['lecture_title'] ?>
                                                                </p>
                                                                <div class="lecture-meta">
                                                                    <div class="lecture-meta__item d-flex align-items-center">
                                                                        <svg class="icon icon--play icon--xsmall margin-right-1">
                                                                            <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#icon-play">
                                                                            </use>
                                                                        </svg>
                                                                        <span>
                                                                            <?php echo YouTube::convertDuration($lesson['lecture_duration']); ?>
                                                                        </span>
                                                                    </div>
                                                                    <?php
                                                                    if (isset($lesson['resources']) && count($lesson['resources']) > 0) { ?>
                                                                        <div class="lecture-meta__item d-flex align-items-center">
                                                                            <svg class="icon icon--attachment">
                                                                                <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#icon-attachments">
                                                                                </use>
                                                                            </svg>
                                                                            <span>
                                                                                <?php echo count($lesson['resources']); ?>
                                                                                <?php echo Label::getLabel('LBL_RESOURCES'); ?>
                                                                            </span>
                                                                        </div>
                                                                    <?php
                                                                    }
                                                                    ?>
                                                                    <?php 

                                                                    if(isset($section['section_quiz_id']) && $section['section_quiz_id']!=0){ ?>
                                                                    <div class="lecture-meta__item d-flex align-items-center">
                                                                    <svg class="icon icon--play icon--xsmall margin-right-1">
                                                                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#icon-play">
                                                                    </use>
                                                                    </svg>
                                                                    <span>
                                                                    <?php echo 1; ?>
                                                                    <?php echo Label::getLabel('LBL_EXAM'); ?>
                                                                    </span>
                                                                    </div>
                                                                    <?php } ?>
                                                                </div>
                                                            </div>
                                                        </div><?php
                                                                $i++;
                                                            }
                                                        }
                                                                ?>
                                            </div>
                                        </div>
                                    </div><?php
                                        }
                                    }
                                            ?>
                        </div>
                    </sidebar>
                    <!-- ] -->
                    <!-- [ TAB CONTENT PANEL ========= -->
                    <div class="content-area responsive-target-js tabsPanelJs">
                        <div class="lectureDetailJs" style="display: none;">
                        </div>
                        <div class="row justify-content-center notesJs" style="display: none;"></div>
                        <div class="row justify-content-center reviewsJs" style="display: none;"></div>
                        <div class="row justify-content-center tutorInfoJs" style="display: none;"></div>
                        <div class="row justify-content-center quizJs" style="display: none;"></div>
                    </div>
                    <!-- ] -->
                </div>
            </div>

            <?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>
            <input type="hidden" id="progressId" name="progress_id" value="<?php echo $progressId; ?>">
            <script>
                var currentLectureId = "<?php echo $progress['crspro_lecture_id'] ?>";
                var courseId = "<?php echo $course['course_id'] ?>";
            </script>
            <script src="//www.youtube.com/player_api"></script>