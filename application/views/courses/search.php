<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$priceSorting = AppConstant::getSortbyArr();

?>
<div class="page-listing__head">
    <div class="row justify-content-between align-items-center">
        <div class="col-sm-8">
            <h4>
                <?php echo str_replace('{recordcount}', $recordCount, Label::getLabel('LBL_FOUND_THE_BEST_{recordcount}_ONLINE_COURSES_FOR_YOU')) ?>
            </h4>
        </div>
        <div class="col-xl-auto col-sm-auto">
            <div class="sorting-options">
                <div class="sorting-options__item">
                    <div class="sorting-action">
                        <div class="sorting-action__trigger sort-trigger-js" onclick="toggleSort(this);">
                            <svg class="svg-icon" viewBox="0 0 16 12.632">
                                <path d="M7.579 9.263v1.684H0V9.263zm1.684-4.211v1.684H0V5.053zM7.579.842v1.684H0V.842zM13.474 12.632l-2.527-3.789H16z"></path>
                                <path d="M12.632 2.105h1.684v7.579h-1.684z"></path>
                                <path d="M13.473 0L16 3.789h-5.053z"></path>
                            </svg>
                            <span class="sorting-action__label">
                                <?php echo Label::getLabel('LBL_SORT_BY'); ?>
                            </span>
                            <span class="sorting-action__value">
                                <?php echo $priceSorting[$post['price_sorting']]; ?>
                            </span>
                        </div>
                        <div class="sorting-action__target sort-target-js" style="display: none;">
                            <div class="filter-dropdown">
                                <div class="select-list select-list--vertical select-list--scroll">
                                    <ul>
                                        <?php foreach ($priceSorting as $id => $name) { ?>
                                            <li>
                                                <label class="select-option">
                                                    <input class="select-option__input" type="radio" name="sorts" value="<?php echo $id; ?>" <?php echo ($id == $post['price_sorting']) ? 'checked' : ''; ?> onclick="priceSortSearch(this.value);" />
                                                    <span class="select-option__item"><?php echo $name; ?></span>
                                                </label>
                                            </li>
                                        <?php } ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="sorting-options__item">
                    <a href="#filter-panel" class="btn btn--filters js-filter-toggle">
                        <span class="svg-icon">
                            <svg version="1.1" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" viewBox="0 0 402.577 402.577" style="enable-background:new 0 0 402.577 402.577;" xml:space="preserve">
                                <g>
                                    <path d="M400.858,11.427c-3.241-7.421-8.85-11.132-16.854-11.136H18.564c-7.993,0-13.61,3.715-16.846,11.136
                                c-3.234,7.801-1.903,14.467,3.999,19.985l140.757,140.753v138.755c0,4.955,1.809,9.232,5.424,12.854l73.085,73.083
                                c3.429,3.614,7.71,5.428,12.851,5.428c2.282,0,4.66-0.479,7.135-1.43c7.426-3.238,11.14-8.851,11.14-16.845V172.166L396.861,31.413
                                C402.765,25.895,404.093,19.231,400.858,11.427z"></path>
                                </g>
                            </svg>
                        </span>
                        <?php echo Label::getLabel('LBL_FILTERS'); ?>
                        <span class="filters-count mobMoreCountJs" style="display:none;"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="page-listing__body">
    <div class="course-results">
        <?php
        if (count($courses)) {
           // echo '<pre>';print_r($courses);die;
            foreach ($courses as $course) { ?>
                <!-- [ COURSE CARD ========= -->
                <div class="course-card">
                    <div class="course-grid">
                        <div class="course-grid__head">
                            <div class="course-media ratio ratio--16by9 11">
                               
                                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="">   
                              
                                <a href="javascript:void(0);" class="course-preview__action" onclick="showPreviewVideo('<?php echo $course['course_id']; ?>');">
                                    <span></span>
                                </a>
                            </div>
                            <a href="javascript:void(0)" onclick="toggleCourseFavorite('<?php echo $course['course_id'] ?>', this)" data-status="<?php echo $course['is_favorite']; ?>" class="mark-option <?php echo ($course['is_favorite'] == AppConstant::YES) ? 'is-active' : ''; ?>">
                                <svg class="fav-icon" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 25.32 25.32">
                                    <g>
                                        <path class="cls-1" d="M17.16,3.41c3.04,0,5.5,2.5,5.5,6,0,7-7.5,11-10,12.5-2.5-1.5-10-5.5-10-12.5,0-3.5,2.5-6,5.5-6,1.86,0,3.5,1,4.5,2,1-1,2.64-2,4.5-2Z" />
                                    </g>
                                </svg>
                            </a>
                            <?php if ($course['course_certificate'] == AppConstant::YES) { ?>
                                <span class="course-tag">
                                    <svg class="icon icon--award icon--small margin-right-1">
                                        <use xlink:href="<?php echo CONF_WEBROOT_FRONT_URL; ?>images/sprite.svg#icon-course-certificate">
                                        </use>
                                    </svg>
                                    <span>
                                        <?php echo Label::getLabel('LBL_CERTIFICATE_ON_COMPLETION'); ?>
                                    </span>
                                </span>
                            <?php } ?>
                        </div>
                        <div class="course-grid__body">
                            <span class="course-card__label">
                                <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_cate_id'] ?>"><?php echo $course['cate_name']; ?></a>
                                <?php
                                if (!empty($course['subcate_name'])) {
                                    echo ' / '; ?>
                                    <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_subcate_id'] ?>"><?php echo $course['subcate_name']; ?></a>
                                <?php } ?>
                            </span>
                            <h4 class="course-card__title">
                                <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$course['course_slug']]); ?>" class="snakeline-hover">
                                    <?php echo $course['course_title']; ?>
                                </a>
                            </h4>
                            <p class="course-card__subtitle ">
                                <?php echo $course['course_subtitle']; ?>
                            </p>
                            <div class="course-stats">
                                <div class="course-stats__item">
                                    <div class="rating">
                                        <svg class="rating__media">
                                            <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#rating"></use>
                                        </svg>
                                        <span class="rating__value">
                                            <?php echo FatUtility::convertToType($course['course_ratings'], FatUtility::VAR_FLOAT); ?>
                                        </span>
                                        <span class="rating__count">
                                            <?php echo '(' . $course['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS') . ')' ?>
                                        </span>
                                    </div>
                                </div>
                                <?php
    $levelId   = (int)$course['course_level'];
    $levelName = isset($levels[$levelId]) ? $levels[$levelId] : Label::getLabel('LBL_LEVEL_NOT_SET');
?>
                                <div class="course-stats__item">
                                    <span>
                                        <?php echo Label::getLabel('LBL_LEVEL:') ?>
                                      <strong><?php echo $levelName; ?></strong>
                                    </span>
                                </div>
                                <div class="course-stats__item">
                                    <span>
                                        <?php echo Label::getLabel('LBL_LECTURES'); ?>:
                                        <strong><?php echo $course['course_lectures']; ?></strong>
                                    </span>
                                </div>
                               
                                <div class="course-stats__item">
                                    <span>
                                        <?php echo Label::getLabel('LBL_TIME') ?>:
                                        <strong><?php echo YouTube::convertDuration($course['course_duration']); ?></strong>
                                    </span>
                                </div>
                                <div class="course-stats__item">
                                    <span>
                                        <?php echo Label::getLabel('LBL_Students'); ?>:
                                        <strong><?php echo $course['course_students'] * 20; ?></strong>
                                    </span>
                                </div>
                                <div class="course-stats__item">
                                    <span>
                                        <?php echo Label::getLabel('LBL_QUIZ'); ?>:
                                        <strong><?php echo $course['section_count']; ?></strong>
                                    </span>
                                </div> 
                            </div>
                            <div class="course-actions border-top padding-top-3">
                                <div class="course-actions__grid course-actions__grid-left">
                                    <a href="#" class="profile-meta d-flex align-items-center">
                                        <div class="profile-meta__media margin-right-4">
                                        <img src="<?php echo 'http://readwithus.org.uk/image/show/6/0/LARGE'; ?>" style="width:70%" alt="Readwithus">
                                     
                                            <!-- <span class="avtar avtar--xsmall avtar--round" data-title="<?php echo CommonHelper::getFirstChar($course['teacher_first_name']); ?>">
                                                 <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $course['teacher_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $course['teacher_first_name'] . ' ' . $course['teacher_last_name']; ?>">  
                                          
                                                <img src="<?php echo '/cache/92\50341ba1c5b4fb4efd6eec3733248f.jpg'; ?>" alt="<?php echo $course['teacher_first_name'] . ' ' . $course['teacher_last_name']; ?>">
                                            </span> -->
                                        </div>
                                        <!-- <div class="profile-meta__details">
                                            <span class="color-black style-italic">
                                            <?php  echo  'Read With Us'; ?>
                                                <?php //echo ucwords($course['teacher_first_name'] . ' ' . $course['teacher_last_name']); ?>
                                            </span>
                                        </div> -->
                                    </a>
                                </div>
                                <div class="course-actions__grid course-actions__grid-right">
                                    <!-- <div class="course-controls">
                                        <div class="course-controls__item">
                                            <?php if (!$course['is_purchased']) { ?>
                                                <?php if ($course['course_type'] == Course::TYPE_FREE) { ?>
                                                    <h4 class="free-text color-red">
                                                        <?php echo Label::getLabel('LBL_FREE'); ?>
                                                    </h4>
                                                <?php } else { ?>
                                                    <h4 class="color-primary bold-700">
                                                        <?php echo CourseUtility::formatMoney($course['course_price']); ?>
                                                    </h4>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="course-controls__item">
                                            <?php if (!$course['is_purchased']) { ?>
                                                <?php if ($course['course_type'] == Course::TYPE_FREE) { ?>
                                                    <a href="javascript:void(0);" onclick="cart.addFreeCourse('<?php echo $course['course_id'] ?>');" class="btn btn--primary">
                                                        <?php echo Label::getLabel('LBL_ENROLL_NOW'); ?>
                                                    </a>
                                                <?php } else { ?>
                                                    <a href="javascript:void(0);" onclick="cart.addCourse('<?php echo $course['course_id'] ?>');" class="btn btn--primary">
                                                        <?php echo Label::getLabel('LBL_ENROLL_NOW'); ?>
                                                    </a>
                                                <?php } ?>
                                            <?php } else { ?>
                                                <a href="<?php echo MyUtility::makeUrl('Tutorials', 'start', [$course['ordcrs_id']], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--primary">
                                                    <?php echo Label::getLabel('LBL_GO_TO_COURSE'); ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                        <div class="course-controls__item">
                                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$course['course_slug']]); ?>" class="btn btn--bordered color-gray-500">
                                                <span class="color-black">
                                                    <?php echo Label::getLabel('LBL_VIEW_DETAILS'); ?>
                                                </span>
                                            </a>
                                        </div>
                                    </div> -->

                                    <div class="course-controls">

  <?php if (!empty($subscriptionMode) && $subscriptionMode === true): ?>
      <!-- Subscription UI -->
      <div class="course-controls__item">
        <?php if ($course['course_type'] == Course::TYPE_FREE): ?>
          <h4 class="free-text"><?php echo Label::getLabel('LBL_FREE'); ?></h4>
        <?php else: ?>
          <span class="badge rounded-pill" style="background:#e0f2fe;color:#0369a1;padding:6px 10px;font-weight:600;">
            <?php echo Label::getLabel('LBL_INCLUDED_IN_SUBSCRIPTION'); ?>
          </span>
        <?php endif; ?>
      </div>
<div class="course-controls__item">
  <?php if (empty($hasActiveSub)): ?>
    <?php
      // Build level-aware pricing URL per course
      $courseLevelId = (int)$course['course_level'];   // this is already available in $course
      $levelPricingUrl = MyUtility::makeUrl('Pricing', 'index') . '?level_id=' . $courseLevelId;
    ?>
    <a href="<?php echo $levelPricingUrl; ?>" class="btn btn--primary">
      <?php echo Label::getLabel('LBL_SUBSCRIBE_TO_UNLOCK'); ?>
    </a>
  <?php else: ?>
 <a href="<?php echo MyUtility::makeUrl(
                        'Tutorials',
                        'startByCourse',
                        [ $course['course_id'] ],   // ← you MUST pass courseId as param
                        CONF_WEBROOT_DASHBOARD
                ); ?>" class="btn btn--primary">
      <?php echo Label::getLabel('LBL_GO_TO_COURSE'); ?>
    </a>
  <?php endif; ?>
</div>

      <div class="course-controls__item">
        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$course['course_slug']]); ?>" class="btn btn--bordered color-gray-500">
          <span class="color-black"><?php echo Label::getLabel('LBL_VIEW_DETAILS'); ?></span>
        </a>
      </div>

  <?php else: ?>
      <!-- Legacy buy/enroll UI -->
      <div class="course-controls__item">
        <?php if (!$course['is_purchased']): ?>
          <?php if ($course['course_type'] == Course::TYPE_FREE): ?>
            <h4 class="free-text color-red"><?php echo Label::getLabel('LBL_FREE'); ?></h4>
          <?php else: ?>
            <h4 class="color-primary bold-700"><?php echo CourseUtility::formatMoney($course['course_price']); ?></h4>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <div class="course-controls__item">
        <?php if (!$course['is_purchased']): ?>
          <?php if ($course['course_type'] == Course::TYPE_FREE): ?>
            <a href="javascript:void(0);" onclick="cart.addFreeCourse('<?php echo $course['course_id'] ?>');" class="btn btn--primary">
              <?php echo Label::getLabel('LBL_ENROLL_NOW'); ?>
            </a>
          <?php else: ?>
            <a href="javascript:void(0);" onclick="cart.addCourse('<?php echo $course['course_id'] ?>');" class="btn btn--primary">
              <?php echo Label::getLabel('LBL_ENROLL_NOW'); ?>
            </a>
          <?php endif; ?>
        <?php else: ?>
          <a href="<?php echo MyUtility::makeUrl('Tutorials', 'start', [$course['ordcrs_id']], CONF_WEBROOT_DASHBOARD); ?>" class="btn btn--primary">
            <?php echo Label::getLabel('LBL_GO_TO_COURSE'); ?>
          </a>
        <?php endif; ?>
      </div>

      <div class="course-controls__item">
        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$course['course_slug']]); ?>" class="btn btn--bordered color-gray-500">
          <span class="color-black"><?php echo Label::getLabel('LBL_VIEW_DETAILS'); ?></span>
        </a>
      </div>
  <?php endif; ?>

</div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- ] -->
            <?php } ?>
        <?php } else { ?>
            <div class="page-listing__body">
                <div class="box -padding-30" style="margin-bottom: 30px;">
                    <div class="message-display">
                        <div class="message-display__icon">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                                <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
                            </svg>
                        </div>
                        <h5><?php echo Label::getLabel('LBL_NO_COURSE_FOUND!'); ?></h5>
                    </div>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="pagination pagination--centered margin-top-10">
        <?php
        echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
        $pagingArr = ['page' => $post['pageno'], 'pageCount' => $pageCount, 'recordCount' => $recordCount, 'callBackJsFunc' => 'gotoPage'];
        $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
        ?>
    </div>
    <!-- <?php
    $checkoutForm->setFormTagAttribute('class', 'd-none');
    $checkoutForm->setFormTagAttribute('name', 'frmCheckout');
    $checkoutForm->setFormTagAttribute('id', 'frmCheckout');
    echo $checkoutForm->getFormHtml();
    ?> -->
</div>
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
</script>
<style>
    /* COURSE CARD WRAPPER */
.course-results .course-card {
  border-radius: 12px;
  box-shadow: 0 10px 25px rgba(12, 42, 85, 0.04);
  transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease;
  overflow: hidden;
}

.course-results .course-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 20px 45px rgba(12, 42, 85, 0.12);
  border-color: #2DADFF;
}


/* .course-results .btn.btn--primary {
  background: #2DADFF !important;
  color: #fff !important;
  border: none !important;
  box-shadow: 0 6px 16px rgba(45, 173, 255, 0.4);
  transition: all 0.25s ease;
} */

/* .course-results .btn.btn--primary:hover {
  background: #1992DF !important;
  box-shadow: 0 8px 22px rgba(45, 173, 255, 0.6);
  transform: translateY(-2px);
} */

/* FOOTER WRAPPER */
.course-results .course-actions {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  gap: 10px;
  border-top: 1px solid #e5e7eb;
  padding-top: 10px;
}

/* Teacher / brand */
.course-results .course-actions__grid-left .profile-meta {
  display: inline-flex;
  align-items: center;
  gap: 8px;
}

.course-results .course-actions__grid-left .profile-meta__media img {
  width: 32px;
  height: 32px;
  border-radius: 999px;
  object-fit: cover;
}

/* If you re-enable name text later */
.course-results .profile-meta__details span {
  font-size: 12px;
  color: #0f172a;
  font-weight: 500;
}

/* Price + buttons layout */
.course-results .course-controls {
  display: flex;
  flex-wrap: wrap;
  justify-content: flex-end;
  gap: 6px 10px;
  align-items: center;
}

/* PRICE */
.course-results .course-controls__item h4 {
  margin: 0;
}

.course-results .course-controls__item h4.color-primary {
  font-size: 18px;
  font-weight: 800;
}

/* Free label */
.course-results .course-controls__item .free-text {
  font-size: 16px;
  font-weight: 700;
  color: #22c55e;
}

/* Primary button */
.course-results .course-controls__item .btn.btn--primary {
  border-radius: 999px;
  padding: 8px 16px;
  font-size: 13px;
  font-weight: 600;
  background: linear-gradient(135deg, #2DADFF 0%, #14A3FF 100%);
  border: none;
  box-shadow: 0 8px 18px rgba(20, 163, 255, 0.35);
  transition: transform 0.2s ease, box-shadow 0.2s ease, opacity 0.2s ease;
}

.course-results .course-controls__item .btn.btn--primary:hover {
  transform: translateY(-1px);
  box-shadow: 0 12px 28px rgba(20, 163, 255, 0.5);
}

/* Ghost button */
.course-results .course-controls__item .btn.btn--bordered {
  border-radius: 999px;
  font-size: 13px;
  padding: 8px 16px;
  border-color: #cbd5f5;
}

/* Mobile: full-width buttons */
@media (max-width: 768px) {
  .course-results .course-controls {
    width: 100%;
  }
  .course-results .course-controls__item {
    flex: 1 1 100%;
  }
  .course-results .course-controls__item .btn {
    width: 100%;
    text-align: center;
    justify-content: center;
  }
}


.course-results .course-preview__action span {
  position: relative;
}
.course-results .course-preview__action span::before {
  content: "";
  border-style: solid;
  border-width: 8px 0 8px 14px;
  
  display: block;
  margin-left: 3px;
}

</style>