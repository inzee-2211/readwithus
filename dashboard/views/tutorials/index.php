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
                                  <li>
                                        <a href="javascript:void(0);" onclick="getQuiz();">
                                            <?php echo stripslashes(Label::getLabel("LBL_QUIZ")); ?>
                                        </a>
                                    </li>
                                     <li>
                                        <a href="javascript:void(0);" onclick="getAI();">
                                            <?php echo stripslashes(Label::getLabel("LBL_AI_TUTOR")); ?>
                                        </a>
                                    </li>
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
                                        <div class="toggle-control__action control-trigger-js d-flex justify-content-between align-items-center">
    <div>
        <h6 class="m-0">
            <?php
            echo Label::getLabel('LBL_SECTION') . ' ' . $section['section_order'] . ': ';
            echo $section['section_title'];
            ?>
        </h6>
        <p class="mb-0 small text-muted">
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
<?php if (!empty($section['section_quiz_id'])): ?>
  <a
     id="attemptExamBtn_<?php echo (int)$section['section_id']; ?>"
     data-section-id="<?php echo (int)$section['section_id']; ?>"
     data-quiz-id="<?php echo (int)$section['section_quiz_id']; ?>"
     data-course-id="<?php echo (int)$course['course_id']; ?>"
     data-first-lecture-id="<?php echo (int)($section['lectures'][0]['lecture_id'] ?? 0); ?>"
     href="javascript:void(0);"
     onclick="<?php echo $section['can_attempt_exam']
        ? 'AttampQuiz(\'' . $section['section_quiz_id'] . '\', \'' . $course['course_id'] . '\', \'' . ($section['lectures'][0]['lecture_id'] ?? 0) . '\')'
        : 'showExamLockedMessage()'; ?>"
     class="btn btn--primary btn--xs <?php echo !$section['can_attempt_exam'] ? 'btn--disabled' : ''; ?>"
     style="background-color:#00796b; color:#fff; border-radius:4px; padding:5px 10px; font-size:13px;">
     <?php echo Label::getLabel('LBL_ATTEMPT_EXAM'); ?>
     <?php if (!$section['can_attempt_exam']): ?>
       <i class="icon icon--lock margin-left-1"></i>
     <?php endif; ?>
  </a>
<?php endif; ?>


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
if (isset($section['section_quiz_id']) && $section['section_quiz_id'] != 0) { ?>
  <div class="lecture-meta__item d-flex align-items-center">
    <svg class="icon icon--play icon--xsmall margin-right-1">
      <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#icon-play"></use>
    </svg>
    <span>
      <?php echo 1; ?>
      <?php echo Label::getLabel('LBL_QUIZ'); ?>
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

                function showLockedMessage() {
    alert('Please complete the previous section exam before accessing this lecture.');
}

function showExamLockedMessage() {
    alert('Please complete all lectures in this section before attempting the exam. Make sure to mark each lecture as completed after watching.');
}
function validateLectureCompletion(lectureId) {
    var isCompleted = $('input[name="lecture_id"][value="' + lectureId + '"]').is(':checked');
    if (!isCompleted) { 
        alert('Please mark the lecture as completed before proceeding to the next one.');
        return false;
    }
    return true;
}
                var currentLectureId = "<?php echo $progress['crspro_lecture_id'] ?>";
                var courseId = "<?php echo $course['course_id'] ?>";

                function enableExamIfReady(sectionId){
  // find this section’s block
  const $group = $('.sectionListJs').filter(function(){
    return $(this).find('.completedLecture' + sectionId).length > 0;
  }).first();

  if ($group.length === 0) return;

  // are ALL lectures for this section checked?
  const $inputs = $group.find('input[name="lecture_id"][data-section="' + sectionId + '"]');
  if ($inputs.length === 0) return;

  const allChecked = $inputs.filter(':checked').length === $inputs.length;
  if (!allChecked) return;

  // flip the button to enabled state
  const $btn = $('#attemptExamBtn_' + sectionId);
  if ($btn.length) {
    $btn.removeClass('btn--disabled');
    const quizId = $btn.data('quiz-id');
    const courseId = $btn.data('course-id');
    const firstLectureId = $btn.data('first-lecture-id') || 0;
    $btn.attr('onclick', "AttampQuiz('" + quizId + "','" + courseId + "','" + firstLectureId + "')");
    $btn.find('.icon--lock').remove();
  }
}

/* after a pass, mark the lecture as covered in the sidebar and update counters */
function markLectureCoveredInSidebar(lectureId){
  const $cb = $('.body-side input[name="lecture_id"][value="' + lectureId + '"]');
  if ($cb.length) {
    if (!$cb.is(':checked')) {
      $cb.prop('checked', true); // mirror server state
      const sectionId = $cb.data('section');
      const $counter = $('.completedLecture' + sectionId);
      if ($counter.length) {
        const current = parseInt(($counter.text() || '0').trim(), 10) || 0;
        $counter.text(current + 1);
      }
      enableExamIfReady(sectionId);
    } else {
      // even if it was already checked, still try enabling exam (covers race conditions)
      enableExamIfReady($cb.data('section'));
    }
  }
}
            </script>
            <script src="//www.youtube.com/player_api"></script>