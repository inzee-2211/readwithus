<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$teacherLanguage = key($teacher['teachLanguages']);
$langId = MyUtility::getSiteLangId();
$websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $langId, FatUtility::VAR_STRING, '');
$teacherLangPrices = [];
$bookingDuration = '';
foreach ($userTeachLangs as $key => $value) {
    if (!array_key_exists($value['utlang_tlang_id'], $teacherLangPrices)) {
        $teacherLangPrices[$value['utlang_tlang_id']] = [];
    }
    $slotSlabKey = $value['ustelgpr_min_slab'] . '-' . $value['ustelgpr_max_slab'];
    if (!array_key_exists($slotSlabKey, $teacherLangPrices[$value['utlang_tlang_id']])) {
        $teacherLangPrices[$value['utlang_tlang_id']][$slotSlabKey] = [
            'title' => sprintf(Label::getLabel('LBL_[%s_-_%s]_Lessons'), $value['ustelgpr_min_slab'], $value['ustelgpr_max_slab']),
            'lang_name' => $value['teachLangName'],
            'langPrices' => []
        ];
    }
    $price = FatUtility::float($value['ustelgpr_price']);
    $teacherLangPrices[$value['utlang_tlang_id']][$slotSlabKey]['langPrices'][] = [
        'teachLangName' => $value['teachLangName'],
        'ustelgpr_slot' => $value['ustelgpr_slot'],
        'ustelgpr_max_slab' => $value['ustelgpr_max_slab'],
        'ustelgpr_min_slab' => $value['ustelgpr_min_slab'],
        'teachLangName' => $value['teachLangName'],
        'utlang_tlang_id' => $value['utlang_tlang_id'],
        'ustelgpr_price' => $price
    ];
}
$disabledClass = '';
$bookNowOnClickClick = 'onclick="cart.langSlots(' . $teacher['user_id'] . ',\'\',\'\');"';
$contactClick = 'onclick="generateThread(' . $teacher['user_id'] . ');"';
if ($siteUserId == $teacher['user_id']) {
    $disabledClass = 'disabled';
    $bookNowOnClickClick = '';
    $contactClick = '';
}
?>
<section class="section section--profile">
    <div class="container container--fixed">
        <div class="profile-cover">
            <div class="profile-head">
                <div class="detail-wrapper">
                    <div class="profile__media">
                        <div class="avtar avtar--xlarge" data-title="<?php echo CommonHelper::getFirstChar($teacher['user_first_name']); ?>">
                            <?php
                            $img = FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $teacher['user_photo']))));
                            echo '<img src="' . $img . '" alt="' . $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] . '" />';
                            ?>
                        </div>
                    </div>
                    <div class="profile-detail">
                        <div class="profile-detail__head">
                            <div class="tutor-name">
                                <h4><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h4>
                                <div class="flag">
                                    <?php if ($teacher['user_country_id'] > 0) { ?>
                                        <img src="<?php echo CONF_WEBROOT_FRONTEND . 'flags/' . strtolower($teacher['user_country_code']) . '.svg'; ?>" alt="<?php echo $teacher['user_country_name']; ?>" style="border: 1px solid #000;" />
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (!empty($teacher['offers'])) { ?>
                                <?php $this->includeTemplate('_partial/offers.php', ['offers' => $teacher['offers']], false); ?>
                            <?php } ?>
                        </div>
                        <div class="profile-detail__body">
                            <div class="info-wrapper">
                                <div class="info-tag location">
                                    <svg class="icon icon--location">
                                        <use xlink:href=" <?php echo CONF_WEBROOT_URL . 'images/sprite.svg#location' ?>"></use>
                                    </svg>
                                    <span class="lacation__name"><?php echo $teacher['user_country_name']; ?></span>
                                </div>
                                <div class="info-tag ratings">
                                    <svg class="icon icon--rating">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating' ?>"></use>
                                    </svg>
                                    <span class="value"><?php echo $teacher['testat_ratings']; ?></span>
                                    <span class="count"><?php echo  $teacher['testat_reviewes'] . ' ' . Label::getLabel('LBL_REVIEW(S)'); ?></span>
                                </div>
                                <div class="info-tag list-count">
                                    <div class="total-count"><span class="value"><?php echo $teacher['testat_students']; ?></span><?php echo Label::getLabel('LBL_Students') ?></div>
                                    <div class="total-count"><span class="value"><?php echo $teacher['testat_lessons'] + $teacher['testat_classes']; ?></span><?php echo Label::getLabel('LBL_SESSIONS'); ?></div>
                                    <div class="total-count"><span class="value"><?php echo $teacher['courses']; ?></span><?php echo Label::getLabel('LBL_COURSES'); ?></div>
                                </div>
                            </div>
                            <div class="har-rate"><?php echo Label::getLabel('LBL_TEACHER_PRICING'); ?><b> <?php echo MyUtility::formatMoney($teacher['testat_minprice']); ?> - <?php echo MyUtility::formatMoney($teacher['testat_maxprice']); ?></b></div>
                            <div class="tutor-lang"><b><?php echo Label::getLabel('LBL_TEACHES:'); ?></b> <?php echo $teacher['teacherTeachLanguageName']; ?></div>
                        </div>
                    </div>
                    <div class="detail-actions">
                        <?php
                        $disabledText = 'disabled';
                        $onclick = "";
                        if ($siteUserId != $teacher['user_id']) {
                            $disabledText = '';
                            $onclick = 'onclick="toggleTeacherFavorite(' . $teacher["user_id"] . ', this)"';
                        }
                        ?>
                        <a href="javascript:void(0);" <?php echo $onclick; ?> class="btn btn--bordered color-black <?php echo $disabledText; ?> <?php echo ($teacher['uft_id']) ? 'is--active' : ''; ?>" <?php echo $disabledText; ?>>
                            <svg class="icon icon--heart">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#heart'; ?>"></use>
                            </svg>
                            <?php echo Label::getLabel('LBL_FAVORITE'); ?>
                        </a>
                        <div class="toggle-dropdown">
                            <a href="#" class="btn btn--bordered color-black toggle-dropdown__link-js">
                                <svg class="icon icon--share">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#share'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_Share'); ?>
                            </a>
                            <div class="toggle-dropdown__target toggle-dropdown__target-js">
                                <h6><?php echo Label::getLabel('LBL_SHARE_ON'); ?></h6>
                                <ul class="social--share clearfix">
                                    <li class="social--fb"><a class='st-custom-button' data-network="facebook" displayText='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg" alt="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>"></a></li>
                                    <li class="social--tw"><a class='st-custom-button' data-network="twitter" displayText='<?php echo Label::getLabel('LBL_TWITTER'); ?>' title='<?php echo Label::getLabel('LBL_TWITTER'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg" alt="<?php echo Label::getLabel('LBL_TWITTER'); ?>"></a></li>
                                    <li class="social--pt"><a class='st-custom-button' data-network="pinterest" displayText='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg" alt="<?php echo Label::getLabel('LBL_PINTEREST'); ?>"></a></li>
                                    <li class="social--mail"><a class='st-custom-button' data-network="email" displayText='<?php echo Label::getLabel('LBL_EMAIL'); ?>' title='<?php echo Label::getLabel('LBL_EMAIL'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg" alt="<?php echo Label::getLabel('LBL_EMAIL'); ?>"></a></li>
                                </ul>
                            </div>
                        </div>
                        <a href="#lessons-prices" class="color-primary btn--link scroll"><?php Label::getLabel('LBL_VIEW_LESSONS_PACKAGES'); ?></a>
                    </div>
                </div>
            </div>
            <div class="profile-primary">
                <div class="panel-cover">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_About'); ?> <?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js" style="display:block;">
                        <div class="content__row">
                            <p><?php echo nl2br($teacher['user_biography']); ?></p>
                        </div>
                        <div class="content__row">
                            <h4><?php echo Label::getLabel('LBL_Speaks'); ?></h4>
                            <p><?php $this->includeTemplate('teachers/_partial/SpeakLanguages.php', $teacher, false); ?></p>
                        </div>
                    </div>
                </div>
                <div class="panel-cover" id="lessons-prices">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_LESSONS_PRICES'); ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js teach-langbody-js
                         ">
                        <div class="panel-head__right">
                            <label><?php echo Label::getLabel('LBL_Select_Language'); ?></label>
                            <div class="select--box">
                                <select name="teachLanguages" id="teachLang">
                                    <?php foreach ($teacher['teachLanguages'] as $langId => $langName) { ?>
                                        <option value="<?php echo $langId; ?>"><?php echo $langName; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php $i = 1; ?>
                        <?php foreach ($teacherLangPrices as $teachLangId => $teachLangPriceSlabs) { ?>
                            <div <?php echo (($i != 1) ? "style='display:none'" : "") ?> data-lang-id="<?php echo $teachLangId; ?>" class="slider slider--onethird slider--prices slider-onethird-js">
                                <?php foreach ($teachLangPriceSlabs as $slab => $slabDetails) { ?>
                                    <div>
                                        <div class="slider__item">
                                            <div class="card">
                                                <div class="card__head">
                                                    <div class="card__title">
                                                        <h4 class="color-primary"><?php echo $slabDetails['title']; ?></h4>
                                                    </div>
                                                </div>
                                                <div class="card__body">
                                                    <div class="lesson-slot-info">
                                                        <ul>
                                                            <?php
                                                            foreach ($slabDetails['langPrices'] as $priceDetails) {
                                                                $onclick = '';
                                                                if ($siteUserId != $teacher['user_id']) {
                                                                    $onclick = "cart.langSlots(" . $teacher['user_id'] . "," . $teachLangId . "," . $priceDetails['ustelgpr_slot'] . ")";
                                                                }
                                                            ?>
                                                                <li>
                                                                    <a href="javascript:void(0);" onclick="<?php echo $onclick; ?>">
                                                                        <div class="lesson lesson--time"><?php echo $priceDetails['ustelgpr_slot'] . ' ' . Label::getLabel('LBL_Mins'); ?></div>
                                                                        <div class="space"></div>
                                                                        <div class="lesson lesson--price"><?php echo MyUtility::formatMoney($priceDetails['ustelgpr_price']); ?></div>
                                                                    </a>
                                                                </li>
                                                            <?php } ?>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php
                            $i++;
                        }
                        ?>
                    </div>
                </div>
                <div class="panel-cover panel--calendar">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js calendar--trigger-js">
                        <h3><?php echo Label::getLabel('LBL_Schedule') ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js">
                        <div class="calendar-wrapper">
                            <div id="availbility" class="calendar-wrapper__body"></div>
                        </div>
                        <div class="-gap"></div>
                        <div class="note note--blank note--vertical-border">
                            <svg class="icon icon--sound">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#sound'; ?>"></use>
                            </svg>
                            <p>
                                <b><?php echo Label::getLabel('LBL_Note:') ?></b>
                                <?php echo Label::getLabel('LBL_NOT_FINDING_YOUR_IDEAL_TIME'); ?>
                                <a class="bold-600" href="javascript:void(0)" <?php echo $contactClick; ?>><?php echo Label::getLabel('LBL_Contact'); ?></a>
                                <?php echo Label::getLabel('LBL_REQUEST_A_SLOT_OUTSIDE_OF_THEIR_CURRENT_SCHEDULE'); ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php if (count($classes) > 0) { ?>

                    <div class="panel-cover">
                        <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                            <h3><?php echo Label::getLabel('LBL_GROUP_CLASSES'); ?></h3>
                        </div>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="slider author-slider slider--onethird slider-onethird-js">
                                <?php
                                foreach ($classes as $class) {
                                    $classData = ['class' => $class, 'siteUserId' => $siteUserId, 'bookingBefore' => $bookingBefore, 'cardClass' => 'card-class-cover'];
                                    $this->includeTemplate('group-classes/card.php', $classData, false);
                                }
                                ?>
                            </div>
                        </div>
                    </div>

                <?php } ?>
                <div class="panel-cover">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_TEACHING_EXPERTISE'); ?></h3>
                    </div>
                    <?php
                    foreach ($preferencesType as $type => $preference) {
                        if (empty($userPreferences[$type])) {
                            continue;
                        }
                    ?>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="content-wrapper content--tick">
                                <div class="content__head">
                                    <h4><?php echo $preference; ?></h4>
                                </div>
                                <div class="content__body">
                                    <div class="tick-listing tick-listing--onethird">
                                        <ul>
                                            <?php foreach ($userPreferences[$type] as $preference) { ?>
                                                <li><?php echo $preference['prefer_title']; ?></li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <div class="panel-cover">
                    <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                        <h3><?php echo Label::getLabel('LBL_TEACHING_QUALIFICATIONS'); ?></h3>
                    </div>
                    <div class="panel-cover__body panel__body-target panel__body-target-js" id="qualificationsList">
                        <?php
                        foreach ($qualificationType as $type => $name) {
                            if (empty($userQualifications[$type])) {
                                continue;
                            }
                            $first = true;
                        ?>
                            <div class="row row--resume">
                                <?php foreach ($userQualifications[$type] as $qualification) { ?>
                                    <div class="col-xl-4 col-lg-4 col-sm-4">
                                        <?php
                                        if ($first) {
                                            $first = false;
                                        ?>
                                            <h4 class="color-dark"><?php echo $name; ?></h4>
                                        <?php } ?>
                                    </div>
                                    <div class="col-xl-8 col-lg-8 col-sm-8">
                                        <div class="resume-wrapper">
                                            <div class="row">
                                                <div class="col-4 col-sm-4">
                                                    <div class="resume__primary"><b><?php echo $qualification['uqualification_start_year']; ?> - <?php echo $qualification['uqualification_end_year']; ?></b></div>
                                                </div>
                                                <div class="col-7 col-sm-7 offset-1">
                                                    <div class="resume__secondary">
                                                        <b><?php echo $qualification['uqualification_title']; ?></b>
                                                        <p class="-no-margin-bottom"><?php echo $qualification['uqualification_institute_name']; ?></p>
                                                        <p class="-no-margin-bottom"><?php echo $qualification['uqualification_institute_address']; ?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    </div>
                </div>
                <?php if ($teacher['testat_reviewes'] > 0) { ?>
                    <div class="panel-cover">
                        <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                            <h3><?php echo Label::getLabel('LBL_REVIEW'); ?></h3>
                        </div>
                        <?php echo $reviewFrm->getFormHtml(); ?>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <div class="rating-details">
                                <div class="rating__count">
                                    <h1><?php echo FatUtility::convertToType($teacher['testat_ratings'], FatUtility::VAR_FLOAT); ?></h1>
                                </div>
                                <div class="rating__info">
                                    <b><?php echo Label::getLabel('LBL_OVERALL_RATINGS'); ?></b>
                                </div>
                            </div>
                            <div class="reviews-wrapper">
                                <div class="reviews-wrapper__head">
                                    <p id="recordToDisplay"></p>
                                    <div class="review__shorting">
                                        <select name="sorting" onchange="loadReviews('<?php echo $teacher['user_id']; ?>', 1)">
                                            <?php $sortArr = RatingReview::getSortTypes(); ?>
                                            <?php foreach ($sortArr as $key => $value) { ?>
                                                <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div id="listing-reviews" class="reviews-wrapper__body"></div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if ($moreCourses) { ?>
                    <div class="panel-cover">
                        <div class="panel-cover__head panel__head-trigger panel__head-trigger-js">
                            <h3>
                                <?php
                                echo Label::getLabel('LBL_COURSES');
                                ?>
                            </h3>
                        </div>
                        <div class="panel-cover__body panel__body-target panel__body-target-js">
                            <?php echo $this->includeTemplate('teachers/courses.php', [
                                'moreCourses' => $moreCourses,
                                'checkoutForm' => $checkoutForm,
                                'siteLangId' => $siteLangId,
                                'siteUserId' => $siteUserId,
                            ]); ?>
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="profile-secondary">
                <div class="right-panel">
                    <?php if (!empty(MyUtility::validateYoutubeUrl($teacher['user_video_link']))) { ?>
                        <div class="dummy-video">
                            <div class="video-media ratio ratio--16by9">
                                <iframe width="100%" height="100%" src="<?php echo MyUtility::validateYoutubeUrl($teacher['user_video_link']); ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                            </div>
                        </div>
                        <div class="-gap"></div>
                    <?php
                    }
                    ?>
                    <div class="box box--book">
                        <div class="book__actions">
                            <a href="javascript:void(0);" class="btn btn--primary btn--xlarge btn--block color-white <?php echo $disabledClass; ?>" <?php echo $bookNowOnClickClick; ?>><?php echo Label::getLabel('LBL_Book_Now'); ?></a>
                            <a href="javascript:void(0);" <?php echo $contactClick; ?> class="btn btn--bordered btn--xlarge btn--block btn--contact color-primary <?php echo $disabledClass; ?>">
                                <svg class="icon icon--envelope">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#envelope'; ?>"></use>
                                </svg>
                                <?php echo Label::getLabel('LBL_CONTACT'); ?>
                            </a>
                            <a href="#availbility" onclick="viewFullAvailbility()" class="color-primary btn--link scroll"><?php echo Label::getLabel('LBL_VIEW_FULL_AVAILBILITY'); ?></a>
                            <div class="-gap"></div>
                            <div class="-gap"></div>
                            <?php
                            if ($freeTrialEnabled) {
                                $btnText = "LBL_YOU_ALREADY_HAVE_AVAILED_THE_TRIAL";
                                $onclick = "";
                                $btnClass = "btn-secondary";
                                $disabledText = "disabled";
                                if (!$isFreeTrailAvailed) {
                                    $disabledText = "";
                                    $onclick = "onclick=\"cart.trailCalendar('" . $teacher['user_id'] . "')\"";
                                    $btnClass = 'btn-primary';
                                    $btnText = "LBL_BOOK_FREE_TRIAL";
                                }
                                if ($siteUserId == $teacher['user_id']) {
                                    $onclick = "";
                                    $disabledText = "disabled";
                                }
                            ?>
                                <a href="javascript:void(0);" <?php echo $onclick; ?> class="btn btn--secondary btn--trial btn--block color-white <?php echo $btnClass . ' ' . $disabledText; ?> " <?php echo $disabledText; ?>>
                                    <span><?php echo Label::getLabel($btnText); ?></span>
                                </a>
                                <p><?php echo Label::getLabel('LBL_TRIAL_LESSON_ONE_TIME'); ?></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        viewFullAvailbility = function() {
            if ($(window).width() < 768) {
                if (!$('.calendar--trigger-js').hasClass('is-active')) {
                    $('.calendar--trigger-js').click();
                }
            }
        };
        viewCalendar(<?php echo $teacher['user_id'] . ', "paid"'; ?>);
        $('.panel__head-trigger-js').click(function() {
            if ($(this).hasClass('is-active')) {
                $(this).removeClass('is-active');
                $(this).siblings('.panel__body-target-js').slideUp();
                return false;
            }
            $('.panel__head-trigger-js').removeClass('is-active');
            $(this).addClass("is-active");
            $('.panel__body-target-js').slideUp();
            $(this).siblings('.panel__body-target-js').slideDown();
            $('.slider-onethird-js').slick('reinit');
            if ($(this).hasClass('calendar--trigger-js')) {
                window.viewOnlyCal.render();
            }
        });
        <?php if ($teacher['testat_reviewes'] > 0) { ?>
            loadReviews('<?php echo $teacher['user_id']; ?>', 1);
        <?php } ?>
    });
</script>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>