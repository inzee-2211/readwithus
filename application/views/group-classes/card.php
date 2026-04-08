<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<!-- [ GROUP CLASS CELL ========= -->
<div class="<?php echo $cardClass; ?>">

    <div class="card-class">
        <div class="card-class__head">
            <div class="card-class__media ratio ratio--16by9">
                <a href="<?php echo MyUtility::makeUrl('GroupClasses', 'view', [$class['grpcls_slug']]); ?>">
                    <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_GROUP_CLASS_BANNER, $class['grpcls_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $class['grpcls_banner'])))); ?>" alt="<?php echo $class['grpcls_title']; ?>" />
                </a>
            </div>
            <div class="card-class__elements">
                <?php if ($class['grpcls_type'] == GroupClass::TYPE_PACKAGE && $class['grpcls_sub_classes'] > 0) { ?>
                    <span class="card-class__tag">
                        <span class="card-class__tag-media">
                            <svg class="icon icon--bundle icon--small margin-right-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 18 16.16">
                                <g transform="translate(0 -0.006)">
                                    <g transform="translate(0 0.006)">
                                        <g transform="translate(0 0)">
                                            <path d="M9.006,8.509A4.8,4.8,0,0,1,7.43,8.216l-6.2-2.26a1.79,1.79,0,0,1,0-3.4q3.1-1.13,6.2-2.262A4.381,4.381,0,0,1,10.5.272q3.154,1.136,6.3,2.3a1.79,1.79,0,0,1-.051,3.4c-1.84.667-3.676,1.346-5.518,2-.523.187-1.061.333-1.6.476a3.7,3.7,0,0,1-.623.062Z" transform="translate(0 -0.006)" fill="#ff5200"></path>
                                            <path d="M10.228,144.363a10.439,10.439,0,0,1-1.208-.271c-2.366-.829-4.726-1.676-7.088-2.518a.886.886,0,0,1-.64-.962.861.861,0,0,1,.776-.78,1.288,1.288,0,0,1,.528.086q3.438,1.215,6.869,2.447a2.134,2.134,0,0,0,1.509,0q3.431-1.235,6.868-2.453a.907.907,0,0,1,1.22.436.89.89,0,0,1-.541,1.221c-.811.3-1.626.582-2.44.872-1.522.542-3.041,1.094-4.569,1.619a13.025,13.025,0,0,1-1.285.306Z" transform="translate(-1.215 -132.009)" fill="#ff5200"></path>
                                            <path d="M10.119,212.336a11.988,11.988,0,0,1-1.257-.3c-2.341-.819-4.674-1.656-7.01-2.488a.9.9,0,0,1-.6-1.162.889.889,0,0,1,1.195-.525c2.31.82,4.622,1.637,6.928,2.468a2.127,2.127,0,0,0,1.507,0q3.46-1.244,6.928-2.47a.895.895,0,1,1,.616,1.678c-.91.331-1.824.651-2.735.976-1.437.512-2.87,1.035-4.313,1.53a12.019,12.019,0,0,1-1.256.289Z" transform="translate(-1.129 -196.176)" fill="#ff5200"></path>
                                        </g>
                                    </g>
                                </g>
                            </svg>
                        </span>
                        <span class="card-class__tag-label"><?php echo $class['grpcls_sub_classes']; ?> <?php echo Label::getLabel('LBL_CLASSES'); ?></span>
                    </span>
                <?php } ?>
                <span>
                    <?php if ($class['grpcls_type'] == GroupClass::TYPE_REGULAR && !empty($class['class_offer'])) { ?>
                        <div class="offers-ui">
                            <span class="offers-ui__trigger cursor-default">
                                <svg class="icon icon--offers icon--small margin-right-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 392.11 408.86">
                                    <g />
                                    <g>
                                        <g>
                                            <g>
                                                <path d="M200.05,408.86h-7.99c-10.76-1.73-18.95-7.76-26.33-15.37-6.02-6.21-12.44-12.02-18.61-18.08-3.39-3.33-7.34-4.62-12.01-3.93-10.13,1.48-20.28,2.83-30.4,4.41-21.76,3.38-38.7-8.19-43.05-29.79-2.02-10.03-3.79-20.12-5.31-30.24-.91-6.07-3.76-10.17-9.28-12.92-9.04-4.5-17.93-9.32-26.81-14.13-18.96-10.28-25.31-29.78-16.04-49.25,4.46-9.36,8.99-18.68,13.68-27.92,2.5-4.92,2.48-9.54-.02-14.45-4.64-9.12-9.09-18.33-13.52-27.56-9.51-19.8-3.09-39.38,16.31-49.78,9.02-4.84,18.02-9.72,27.15-14.35,4.98-2.52,7.55-6.38,8.41-11.76,1.58-9.84,3.28-19.66,5.05-29.47,3.97-22,19.81-33.94,41.95-31.41,10.17,1.16,20.3,2.7,30.4,4.35,5.58,.91,10.11-.3,14.16-4.36,7.33-7.35,14.86-14.5,22.37-21.67,15.58-14.87,36.21-14.88,51.79-.02,7.7,7.35,15.36,14.75,22.96,22.21,3.51,3.44,7.57,4.72,12.38,4,10.13-1.5,20.27-2.91,30.39-4.43,21.61-3.24,38.4,8.3,42.76,29.74,2.07,10.15,3.77,20.39,5.39,30.63,.9,5.71,3.55,9.72,8.82,12.36,8.91,4.46,17.64,9.29,26.44,13.98,20.03,10.68,26.34,30.21,16.38,50.66-4.3,8.84-8.52,17.73-13.02,26.47-2.67,5.17-2.72,9.99-.06,15.18,4.66,9.11,9.11,18.32,13.51,27.56,9.29,19.51,2.98,38.97-15.98,49.26-9.11,4.95-18.25,9.85-27.5,14.53-4.96,2.51-7.62,6.31-8.49,11.71-1.59,9.84-3.3,19.66-5.05,29.47-3.95,22.15-20.06,34.21-42.26,31.5-10.16-1.24-20.3-2.73-30.4-4.36-5.44-.87-9.88,.36-13.8,4.31-5.91,5.95-12.2,11.52-18.02,17.54-7.38,7.62-15.57,13.64-26.33,15.37ZM286.51,127.25c-.88-7.22-6.17-12.4-12.84-12.36-4.07,.03-6.95,2.19-9.65,4.9-50.88,50.9-101.78,101.79-152.67,152.68-.94,.94-1.92,1.88-2.66,2.96-2.63,3.82-3,7.95-.9,12.07,2.1,4.11,5.58,6.26,10.26,6.43,4.58,.16,7.63-2.47,10.62-5.46,44.86-44.89,89.74-89.75,134.61-134.62,6.02-6.02,12.2-11.89,17.98-18.13,2.22-2.39,3.53-5.63,5.25-8.48Zm-43.41,96.93c-24.01,.08-43.18,19.48-43.06,43.56,.12,23.39,19.63,42.76,43.12,42.81,23.81,.05,43.34-19.59,43.23-43.47-.12-23.85-19.42-42.99-43.29-42.91Zm-94.07-39.49c23.82-.1,43.08-19.43,43.06-43.19-.02-23.81-19.77-43.41-43.53-43.21-23.69,.2-42.94,19.75-42.82,43.48,.12,23.82,19.49,43.03,43.3,42.92Z" />
                                                <path d="M243.35,286.82c-10.85,.06-19.53-8.42-19.72-19.29-.19-10.78,8.87-19.94,19.66-19.87,10.67,.07,19.55,8.96,19.57,19.61,.02,10.75-8.7,19.48-19.51,19.55Z" />
                                                <path d="M168.48,141.39c.12,10.63-8.65,19.62-19.34,19.81-10.7,.19-19.93-8.96-19.89-19.7,.04-10.77,8.78-19.45,19.58-19.48,10.83-.03,19.52,8.54,19.65,19.37Z" />
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="offers-ui__label"><?php echo $class['class_offer'] . '% ' . Label::getLabel('LBL_OFF'); ?></span>
                            </span>
                        </div>
                    <?php } elseif ($class['grpcls_type'] == GroupClass::TYPE_PACKAGE && !empty($class['package_offer'])) { ?>
                        <div class="offers-ui">
                            <span class="offers-ui__trigger cursor-default">
                                <svg class="icon icon--offers icon--small margin-right-2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 392.11 408.86">
                                    <g />
                                    <g>
                                        <g>
                                            <g>
                                                <path d="M200.05,408.86h-7.99c-10.76-1.73-18.95-7.76-26.33-15.37-6.02-6.21-12.44-12.02-18.61-18.08-3.39-3.33-7.34-4.62-12.01-3.93-10.13,1.48-20.28,2.83-30.4,4.41-21.76,3.38-38.7-8.19-43.05-29.79-2.02-10.03-3.79-20.12-5.31-30.24-.91-6.07-3.76-10.17-9.28-12.92-9.04-4.5-17.93-9.32-26.81-14.13-18.96-10.28-25.31-29.78-16.04-49.25,4.46-9.36,8.99-18.68,13.68-27.92,2.5-4.92,2.48-9.54-.02-14.45-4.64-9.12-9.09-18.33-13.52-27.56-9.51-19.8-3.09-39.38,16.31-49.78,9.02-4.84,18.02-9.72,27.15-14.35,4.98-2.52,7.55-6.38,8.41-11.76,1.58-9.84,3.28-19.66,5.05-29.47,3.97-22,19.81-33.94,41.95-31.41,10.17,1.16,20.3,2.7,30.4,4.35,5.58,.91,10.11-.3,14.16-4.36,7.33-7.35,14.86-14.5,22.37-21.67,15.58-14.87,36.21-14.88,51.79-.02,7.7,7.35,15.36,14.75,22.96,22.21,3.51,3.44,7.57,4.72,12.38,4,10.13-1.5,20.27-2.91,30.39-4.43,21.61-3.24,38.4,8.3,42.76,29.74,2.07,10.15,3.77,20.39,5.39,30.63,.9,5.71,3.55,9.72,8.82,12.36,8.91,4.46,17.64,9.29,26.44,13.98,20.03,10.68,26.34,30.21,16.38,50.66-4.3,8.84-8.52,17.73-13.02,26.47-2.67,5.17-2.72,9.99-.06,15.18,4.66,9.11,9.11,18.32,13.51,27.56,9.29,19.51,2.98,38.97-15.98,49.26-9.11,4.95-18.25,9.85-27.5,14.53-4.96,2.51-7.62,6.31-8.49,11.71-1.59,9.84-3.3,19.66-5.05,29.47-3.95,22.15-20.06,34.21-42.26,31.5-10.16-1.24-20.3-2.73-30.4-4.36-5.44-.87-9.88,.36-13.8,4.31-5.91,5.95-12.2,11.52-18.02,17.54-7.38,7.62-15.57,13.64-26.33,15.37ZM286.51,127.25c-.88-7.22-6.17-12.4-12.84-12.36-4.07,.03-6.95,2.19-9.65,4.9-50.88,50.9-101.78,101.79-152.67,152.68-.94,.94-1.92,1.88-2.66,2.96-2.63,3.82-3,7.95-.9,12.07,2.1,4.11,5.58,6.26,10.26,6.43,4.58,.16,7.63-2.47,10.62-5.46,44.86-44.89,89.74-89.75,134.61-134.62,6.02-6.02,12.2-11.89,17.98-18.13,2.22-2.39,3.53-5.63,5.25-8.48Zm-43.41,96.93c-24.01,.08-43.18,19.48-43.06,43.56,.12,23.39,19.63,42.76,43.12,42.81,23.81,.05,43.34-19.59,43.23-43.47-.12-23.85-19.42-42.99-43.29-42.91Zm-94.07-39.49c23.82-.1,43.08-19.43,43.06-43.19-.02-23.81-19.77-43.41-43.53-43.21-23.69,.2-42.94,19.75-42.82,43.48,.12,23.82,19.49,43.03,43.3,42.92Z" />
                                                <path d="M243.35,286.82c-10.85,.06-19.53-8.42-19.72-19.29-.19-10.78,8.87-19.94,19.66-19.87,10.67,.07,19.55,8.96,19.57,19.61,.02,10.75-8.7,19.48-19.51,19.55Z" />
                                                <path d="M168.48,141.39c.12,10.63-8.65,19.62-19.34,19.81-10.7,.19-19.93-8.96-19.89-19.7,.04-10.77,8.78-19.45,19.58-19.48,10.83-.03,19.52,8.54,19.65,19.37Z" />
                                            </g>
                                        </g>
                                    </g>
                                </svg>
                                <span class="offers-ui__label"><?php echo $class['package_offer'] . '% ' . Label::getLabel('LBL_OFF'); ?></span>
                            </span>
                        </div>
                    <?php } ?>
                </span>
            </div>
        </div>
        <div class="card-class__body">
            <span class="card-class__subtitle"><?php echo $class['grpcls_tlang_name'] ?></span>
            <div class="card-class__title">
                <a href="<?php echo MyUtility::makeUrl('GroupClasses', 'view', [$class['grpcls_slug']]); ?>"><?php echo $class['grpcls_title']; ?></a>
            </div>
            <div class="card-element">
                <div class="card-element__item">
                    <span><?php echo date('M d, Y', strtotime($class['grpcls_start_datetime'])); ?></span>
                </div>
                <div class="card-element__item">
                    <span><?php echo date('H:i', strtotime($class['grpcls_start_datetime'])); ?>
                        <?php
                        $str = Label::getLabel('LBL_{minutes}_Minutes');
                        $str = str_replace('{minutes}', $class['grpcls_duration'], $str);
                        echo ($class['grpcls_type'] == GroupClass::TYPE_REGULAR) ?  '(' . $str . ')' : Label::getLabel('LBL_ONWARDS');
                        ?>
                    </span>
                </div>

                <div class="card-element__item">
                    <span><?php echo $class['grpcls_total_seats']; ?> <?php echo Label::getLabel('LBL_SEATS'); ?></span>
                </div>
            </div>

            <h4 class="card-price color-primary margin-top-5 bold-700"><?php echo MyUtility::formatMoney($class['grpcls_entry_fee']); ?></h4>

        </div>
        <div class="card-class__footer">
            <div class="row justify-content-between align-items-center">
                <div class="col-sm-7">
                    <a href="<?php echo MyUtility::makeUrl('Teachers', 'view', [$class['user_username']]) ?>" class="profile-meta d-flex align-items-center">
                        <div class="profile-meta__media margin-right-4">
                            <span class="avtar" data-title="M"><img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $class['grpcls_teacher_id'], Afile::SIZE_SMALL]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $class['user_photo'])))); ?>" alt="<?php echo $class['user_full_name'] ?>" /></span>
                        </div>
                        <div class="profile-meta__details">
                            <p class="bold-600 color-black margin-bottom-1 style-ellipsis"><?php echo $class['user_full_name'] ?></p>
                            <div class="ratings">
                                <svg class="icon icon--rating">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating'; ?>"></use>
                                </svg>&nbsp;
                                <span class="value"><?php echo $class['testat_ratings']; ?></span>
                                <span class="count">(<?php echo $class['testat_reviewes']; ?>)</span>
                            </div>
                        </div>
                    </a>
                </div>
                <div class="col-sm-5">
                    <div class="btn-group d-flex d-sm-block">
                        <a href="<?php echo MyUtility::makeUrl('GroupClasses', 'view', [$class['grpcls_slug']]); ?>" class="btn btn--primary-bordered btn--block d-block d-sm-none margin-right-1"><?php echo Label::getLabel('LBL_VIEW_DETAILS') ?></a>
                        <?php if ($class['grpcls_already_booked']) { ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_ALREADY_BOOKED') ?>" tabindex="0" class="btn btn--primary btn--block margin-left-1 btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } elseif ($class['grpcls_booked_seats'] >= $class['grpcls_total_seats']) { ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_FULL'); ?>" tabindex="0" class="btn btn--primary btn--block margin-left-1 btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } elseif ($class['grpcls_start_datetime'] < date('Y-m-d H:i:s', strtotime('+' . $bookingBefore . ' minutes', $class['grpcls_currenttime_unix']))) { ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_BOOKING_CLOSED') ?>" class="btn btn--primary btn--block margin-left-1 btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } elseif ($siteUserId == $class['grpcls_teacher_id']) { ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CANNOT_BOOK_OWN_CLASS'); ?>" class="btn btn--primary btn--block margin-left-1 btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } elseif ($class['grpcls_booked_seats'] + $class['grpcls_unpaid_seats'] >= $class['grpcls_total_seats']) { ?>
                            <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_HOLD_INFO'); ?>" class="btn btn--primary btn--block margin-left-1 btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } elseif ($class['grpcls_type'] == GroupClass::TYPE_PACKAGE) { ?>
                            <a href="javascript:void(0);" onclick="cart.addPackage(<?php echo $class['grpcls_id']; ?>)" class="btn btn--primary btn--block margin-left-1"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } else { ?>
                            <a href="javascript:void(0);" onclick="cart.addClass(<?php echo $class['grpcls_id']; ?>)" class="btn btn--primary btn--block margin-left-1"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- ] -->