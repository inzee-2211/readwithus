<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$sorting = AppConstant::getSortbyArr();
$hourStringLabel = Label::getLabel('LBL_{hourstring}_HRS');
$offerPriceLabel = Label::getLabel('LBL_{percentages}%_OFF_ON_{duration}_MINUTES_SESSION');
$colorClass = [1 => 'cell-green-40', 2 => 'cell-green-60', 3 => 'cell-green-80', 4 => 'cell-green-100'];
?>
<div class="page-listing__head">
    <div class="row justify-content-between align-items-center">
        <div class="col-sm-8">
            <h4><?php echo str_replace('{recordcount}', $recordCount, Label::getLabel('LBL_FOUND_THE_BEST_{recordcount}_TEACHERS_FOR_YOU')) ?></h4>
        </div>
        <div class="col-xl-auto col-sm-auto">
            <div class="sorting-options">
                <div class="sorting-options__item">
                    <div class="sorting-action">
                        <div class="sorting-action__trigger sort-trigger-js" onclick="toggleSort();">
                            <svg class="svg-icon" viewBox="0 0 16 12.632">
                                <path d="M7.579 9.263v1.684H0V9.263zm1.684-4.211v1.684H0V5.053zM7.579.842v1.684H0V.842zM13.474 12.632l-2.527-3.789H16z"></path>
                                <path d="M12.632 2.105h1.684v7.579h-1.684z"></path>
                                <path d="M13.473 0L16 3.789h-5.053z"></path>
                            </svg>
                            <span class="sorting-action__label"><?php echo Label::getLabel('LBL_SORT_BY'); ?>:</span>
                            <span class="sorting-action__value"><?php echo $sorting[$post['sorting']]; ?></span>
                        </div>
                        <div class="sorting-action__target sort-target-js" style="display: none;">
                            <div class="filter-dropdown">
                                <div class="select-list select-list--vertical select-list--scroll">
                                    <ul>
                                        <?php foreach ($sorting as $id => $name) { ?>
                                            <li>
                                                <label class="select-option">
                                                    <input class="select-option__input" type="radio" name="sorts" value="<?php echo $id; ?>" <?php echo ($id == $post['sorting']) ? 'checked' : ''; ?> onclick="sortsearch(this.value);" />
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
                    <div class="btn btn--filters" onclick="openFilter()">
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
                        <?php
                        $count = 0;
                        foreach ($post as $field) {
                            if (is_array($field)) {
                                $count += count($field);
                            }
                        }
                        if ($count > 0) {
                        ?>
                            <span class="filters-count"><?php echo $count; ?></span>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php if (count($teachers)) { ?>
    <div class="page-listing__body">
        <div class="box-wrapper">
            <?php foreach ($teachers as $teacher) { ?>
                <div class="box box-list">
                    <div class="box__primary">
                        <div class="list__head">
                            <div class="list__media ">
                                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($teacher['user_first_name']); ?>">
                                    <a href="<?php echo MyUtility::makeUrl('teachers', 'view', [$teacher['user_username']]) ?>">
                                        <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $teacher['user_photo'])))); ?>" alt="<?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?>">
                                    </a>
                                </div>
                            </div>
                            <div class="list__price">
                                <p><?php echo MyUtility::formatMoney($teacher['testat_minprice']) . ' - ' . MyUtility::formatMoney($teacher['testat_maxprice']); ?></p>
                            </div>
                        </div>
                        <div class="list__body">
                            <div class="profile-detail">
                                <div class="profile-detail__head">
                                    <a href="<?php echo MyUtility::makeUrl('teachers', 'view', [$teacher['user_username']]) ?>" class="tutor-name">
                                        <h4><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h4>
                                        <div class="flag">
                                            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'flags/' . strtolower($teacher['user_country_code']) . '.svg'; ?>" alt="<?php echo $teacher['user_country_name']; ?>" style="height: 22px;border: 1px solid #000;" />
                                        </div>
                                    </a>
                                    <?php if (!empty($teacher['offers'])) { ?>
                                        <?php $this->includeTemplate('_partial/offers.php', ['offers' => $teacher['offers'], 'offerPriceLabel' => $offerPriceLabel], false); ?>
                                    <?php } ?>
                                    <div class="follow ">
                                        <a class="<?php echo ($teacher['uft_id']) ? 'is--active' : ''; ?>" onClick="toggleTeacherFavorite(<?php echo $teacher['user_id']; ?>, this)" href="javascript:void(0)">
                                            <svg class="icon icon--heart">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#heart'; ?>"></use>
                                            </svg>
                                        </a>
                                    </div>
                                </div>
                                <div class="profile-detail__body">
                                    <div class="info-wrapper">
                                        <div class="info-tag location">
                                            <svg class="icon icon--location">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#location'; ?>"></use>
                                            </svg>
                                            <span class="lacation__name"><?php echo $teacher['user_country_name']; ?></span>
                                        </div>
                                        <div class="info-tag ratings">
                                            <svg class="icon icon--rating">
                                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating'; ?>"></use>
                                            </svg>
                                            <span class="value"><?php echo FatUtility::convertToType($teacher['testat_ratings'], FatUtility::VAR_FLOAT); ?></span>
                                            <span class="count">(<?php echo $teacher['testat_reviewes']; ?>)</span>
                                        </div>
                                        <div class="info-tag list-count">
                                            <div class="total-count"><span class="value"><?php echo $teacher['testat_students']; ?></span><?php echo Label::getLabel('LBL_Students'); ?></div> -
                                            <div class="total-count"><span class="value"><?php echo $teacher['testat_lessons'] + $teacher['testat_classes']; ?></span><?php echo Label::getLabel('LBL_Sessions'); ?></div> -
                                            <div class="total-count"><span class="value"><?php echo $teacher['courses']; ?></span><?php echo Label::getLabel('LBL_COURSES'); ?></div>
                                        </div>
                                    </div>
                                    <div class="tutor-info">
                                        <div class="tutor-info__inner">
                                            <div class="info__title">
                                                <h6><?php echo Label::getLabel('LBL_Teaches'); ?></h6>
                                            </div>
                                            <div class="info__language">
                                                <?php echo $teacher['teacherTeachLanguageName']; ?>
                                            </div>
                                        </div>
                                        <div class="tutor-info__inner">
                                            <div class="info__title">
                                                <h6><?php echo Label::getLabel('LBL_Speaks'); ?></h6>
                                            </div>
                                            <div class="info__language">
                                                <?php echo $teacher['spoken_language_names']; ?>
                                            </div>
                                        </div>
                                        <div class="tutor-info__inner info--about">
                                            <div class="info__title">
                                                <h6><?php echo LABEL::getLabel('LBL_About'); ?></h6>
                                            </div>
                                            <div class="about__detail">
                                                <p><?php echo $teacher['user_biography'] ?></p>
                                                <a href="<?php echo MyUtility::makeUrl('teachers', 'view', [$teacher['user_username']]) ?>"><?php echo Label::getLabel('LBL_View_Profile') ?></a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="list__action">
                            <div class="list__action-btn">
                                <a href="javascript:void(0);" onclick="cart.langSlots('<?php echo $teacher['user_id']; ?>', '', '')" class="btn btn--primary btn--block"><?php echo Label::getLabel('LBL_Book_Now'); ?></a>
                                <a href="javascript:void(0);" onclick="generateThread(<?php echo $teacher['user_id']; ?>);" class="btn btn--bordered color-primary btn--block">
                                    <svg class="icon icon--envelope">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#envelope'; ?>"></use>
                                    </svg>
                                    <?php echo Label::getLabel('LBL_Contact'); ?>
                                </a>
                            </div>
                            <a href="javascript:void(0);" onclick="viewCalendar(<?php echo $teacher['user_id']; ?>, 'paid')" class="link-detail"><?php echo Label::getLabel('LBL_View_Full_availability'); ?></a>
                        </div>
                    </div>
                    <div class="box__secondary">
                        <div class="panel-box">
                            <div class="panel-box__head">
                                <ul>
                                    <li class="is--active">
                                        <a class="panel-action" content="calender" href="javascript:void(0)"><?php echo Label::getLabel('LBL_Availability'); ?></a>
                                    </li>
                                    <?php if (!empty($teacher['user_video_link'])) { ?>
                                        <li>
                                            <a class="panel-action" content="video" href="javascript:void(0)"><?php echo Label::getLabel('LBL_Introduction'); ?></a>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                            <div class="panel-box__body">
                                <div class="panel-content calender">
                                    <div class="custom-calendar">
                                        <table>
                                            <thead>
                                                <tr>
                                                    <th>&nbsp;</th>
                                                    <th><?php echo Label::getLabel('LBL_Sun'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Mon'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Tue'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Wed'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Thu'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Fri'); ?></th>
                                                    <th><?php echo Label::getLabel('LBL_Sat'); ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $timeslots = $teacher['testat_timeslots'] ?? AppConstant::getEmptyDaySlots(); ?>
                                                <?php foreach ($slots as $index => $slot) { ?>
                                                    <tr>
                                                        <td>
                                                            <div class="cal-cell"><?php echo $slot; ?></div>
                                                        </td>
                                                        <?php
                                                        foreach ($timeslots as $day => $hours) {
                                                        ?>
                                                            <?php
                                                            if (!empty($hours[$index])) {
                                                                $hourString = MyDate::getHoursMinutes($hours[$index]);
                                                                $hour = str_replace(":", '.', $hourString);
                                                                $hour = (ceil(FatUtility::float($hour)));
                                                                $hour = ($hour == 0) ? 1 : $hour;
                                                                $hourString = str_replace('{hourstring}', $hourString, $hourStringLabel);
                                                            }
                                                            ?>
                                                            <td class="is-hover">
                                                                <?php if (!empty($hours[$index])) { ?>
                                                                    <div class="cal-cell <?php echo $colorClass[$hour]; ?>"></div>
                                                                    <div class="tooltip tooltip--top bg-black"><?php echo $hourString; ?></div>
                                                                <?php } else { ?>
                                                                    <div class="cal-cell"></div>
                                                                <?php } ?>
                                                            </td>
                                                        <?php } ?>
                                                    </tr>
                                                <?php } ?>
                                            </tbody>
                                        </table>
                                        <a href="javascript:void(0);" onclick="viewCalendar(<?php echo $teacher['user_id']; ?>, 'paid')" class="link-detail"><?php echo Label::getLabel('LBL_View_Full_availability'); ?></a>
                                    </div>
                                </div>
                                <?php if (!empty($teacher['user_video_link'])) { ?>
                                    <div class="panel-content video" data-src="<?php echo $teacher['user_video_link']; ?>" style="display:none;">
                                        <iframe width="100%" height="100%" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="show-more">
                <?php
                echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
                $pagingArr = ['page' => $post['pageno'], 'pageCount' => $pageCount, 'recordCount' => $recordCount, 'callBackJsFunc' => 'gotoPage'];
                $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
                ?>
            </div>
        </div>
    </div>
<?php } else { ?>
    <div class="page-listing__body">
        <div class="box -padding-30" style="margin-bottom: 30px;">
            <div class="message-display">
                <div class="message-display__icon">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 408">
                        <path d="M488.468,408H23.532A23.565,23.565,0,0,1,0,384.455v-16.04a15.537,15.537,0,0,1,15.517-15.524h8.532V31.566A31.592,31.592,0,0,1,55.6,0H456.4a31.592,31.592,0,0,1,31.548,31.565V352.89h8.532A15.539,15.539,0,0,1,512,368.415v16.04A23.565,23.565,0,0,1,488.468,408ZM472.952,31.566A16.571,16.571,0,0,0,456.4,15.008H55.6A16.571,16.571,0,0,0,39.049,31.566V352.891h433.9V31.566ZM497,368.415a0.517,0.517,0,0,0-.517-0.517H287.524c0.012,0.172.026,0.343,0.026,0.517a7.5,7.5,0,0,1-7.5,7.5h-48.1a7.5,7.5,0,0,1-7.5-7.5c0-.175.014-0.346,0.026-0.517H15.517a0.517,0.517,0,0,0-.517.517v16.04a8.543,8.543,0,0,0,8.532,8.537H488.468A8.543,8.543,0,0,0,497,384.455h0v-16.04ZM63.613,32.081H448.387a7.5,7.5,0,0,1,0,15.008H63.613A7.5,7.5,0,0,1,63.613,32.081ZM305.938,216.138l43.334,43.331a16.121,16.121,0,0,1-22.8,22.8l-43.335-43.318a16.186,16.186,0,0,1-4.359-8.086,76.3,76.3,0,1,1,19.079-19.071A16,16,0,0,1,305.938,216.138Zm-30.4-88.16a56.971,56.971,0,1,0,0,80.565A57.044,57.044,0,0,0,275.535,127.978ZM63.613,320.81H448.387a7.5,7.5,0,0,1,0,15.007H63.613A7.5,7.5,0,0,1,63.613,320.81Z"></path>
                    </svg>
                </div>
                <h5><?php echo Label::getLabel('LBL_NO_RESULT_FOUND!'); ?></h5>
            </div>
        </div>
    </div>
<?php } ?>