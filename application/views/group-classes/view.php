<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
$isPackage = ($class['grpcls_type'] == GroupClass::TYPE_PACKAGE);
$bookedSeats = $class['grpcls_booked_seats'] + $class['grpcls_unpaid_seats'];
$teacherName = $class['user_full_name'];
?>
<title><?php echo $class['grpcls_title']; ?></title>
<!-- [ MAIN BODY ========= -->
<section class="section padding-top-0">
    <div class="container container--narrow">
        <div class="breadcrumb-list padding-bottom-0">
            <ul>
                <li><a href="<?php echo MyUtility::makeUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                <li><a href="<?php echo MyUtility::makeUrl('GroupClasses'); ?>"><?php echo Label::getLabel('LBL_Group_Classes'); ?></a></li>
                <li><?php echo $class['grpcls_title']; ?></li>
            </ul>
        </div>
        <div class="view-panel">
            <!-- [ PANEL LARGE 1 ========= -->
            <div class="view-panel__large">
                <h1 class="page-title-h1 margin-bottom-8"><?php echo $class['grpcls_title']; ?></h1>
                <div class="view-panel__media ratio ratio--16by9">
                    <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_GROUP_CLASS_BANNER, $class['grpcls_id'], Afile::SIZE_LARGE]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $class['grpcls_banner'])))); ?>" alt="<?php echo $class['grpcls_title']; ?>" />
                </div>
            </div>
            <!-- ] -->
            <!-- [ PANEL SMALL 1 ========= -->
            <div class="view-panel__small">
                <div class="sticky-side-panel">
                    <div class="view-box">
                        <div class="view-box__head">
                            <div class="profile-meta d-flex align-items-center">
                                <div class="profile-meta__media margin-right-4">
                                    <span class="avtar" data-title="M">
                                        <img src="<?php echo FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $class['grpcls_teacher_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.jpg'); ?>" alt="<?php echo $class['user_first_name'] . ' ' . $class['user_last_name']; ?>" />
                                    </span>
                                </div>
                                <div class="profile-meta__details">
                                    <p class="bold-600 color-black margin-bottom-1"><?php echo $class['user_full_name']; ?></p>
                                    <div class="ratings">
                                        <svg class="icon icon--rating">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating'; ?>"></use>
                                        </svg>
                                        &nbsp;
                                        <span class="value"><?php echo $class['testat_ratings']; ?></span>
                                        <span class="count"><?php echo $class['testat_reviewes'] . ' ' . Label::getLabel('LBL_REVIEW(s)'); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="view-box__body">
                            <div class="view-list">
                                <ul>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">
                                            <svg class="icon icon--language" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M5.388,5.562a6.4,6.4,0,0,0,7.054,10.355,2.765,2.765,0,0,0-.192-1.378,9.843,9.843,0,0,0-1.8-2.275c-.27-.284-.253-.5-.156-1.15l.01-.073c.066-.443.176-.706,1.668-.942a1.024,1.024,0,0,1,1.234.6l.093.138a1.465,1.465,0,0,0,.75.6c.132.06.3.136.516.26.522.3.522.635.522,1.373v.084a4.2,4.2,0,0,1-.078.827,6.4,6.4,0,0,0-2.484-9.873A6.3,6.3,0,0,0,11.26,5.128c-.108.148-.262.906-.76.968a2.934,2.934,0,0,1-.49-.007c-.5-.032-1.178-.076-1.4.515A2.343,2.343,0,0,0,8.9,8.524a.467.467,0,0,1,.037.415.811.811,0,0,1-.234.4,2.307,2.307,0,0,1-.335-.344A2.194,2.194,0,0,0,7.4,8.226c-.147-.041-.309-.074-.466-.108-.439-.092-.936-.2-1.052-.443A1.731,1.731,0,0,1,5.8,6.982a2.547,2.547,0,0,0-.163-1.076,1.021,1.021,0,0,0-.245-.344ZM10,18a8,8,0,1,1,8-8A8,8,0,0,1,10,18Z" transform="translate(2 2)" />
                                            </svg>
                                        </span>
                                        <span class="view-list__item-label"><?php echo Label::getLabel('LBL_SUBJECT'); ?> - <?php echo $class['grpcls_tlang_name']; ?></span>
                                    </li>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">
                                            <svg class="icon icon--calendar" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M12.5,2.4h2.8a.7.7,0,0,1,.7.7V14.3a.7.7,0,0,1-.7.7H2.7a.7.7,0,0,1-.7-.7V3.1a.7.7,0,0,1,.7-.7H5.5V1H6.9V2.4h4.2V1h1.4ZM11.1,3.8H6.9V5.2H5.5V3.8H3.4V6.6H14.6V3.8H12.5V5.2H11.1ZM14.6,8H3.4v5.6H14.6Z" transform="translate(3 4)" />
                                            </svg>
                                        </span>
                                        <span class="view-list__item-label"><?php echo date('M d, Y', strtotime($class['grpcls_start_datetime'])); ?></span>
                                    </li>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">
                                            <svg class="icon icon--time" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M10,18a8,8,0,1,1,8-8A8,8,0,0,1,10,18Zm0-1.6A6.4,6.4,0,1,0,3.6,10,6.4,6.4,0,0,0,10,16.4Zm.8-6.4H14v1.6H9.2V6h1.6Z" transform="translate(2 2)" />
                                            </svg>
                                        </span>
                                        <span class="view-list__item-label">
                                            <?php echo date('H:i', strtotime($class['grpcls_start_datetime'])); ?>
                                            <?php
                                            $str = Label::getLabel('LBL_{minutes}_Minutes');
                                            $str = str_replace('{minutes}', $class['grpcls_duration'], $str);
                                            echo ($class['grpcls_type'] == GroupClass::TYPE_REGULAR) ? '(' . $str . ')' : Label::getLabel('LBL_ONWARDS');
                                            ?>
                                        </span>
                                    </li>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">
                                            <svg class="icon icon--seats" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <path d="M15.375,7.375H14.7V3.664A3.668,3.668,0,0,0,11.039,0H4.961A3.668,3.668,0,0,0,1.3,3.664V7.375H.625A.625.625,0,0,0,0,8v4.875a.625.625,0,0,0,.625.625H3.394L2.57,15.087a.625.625,0,0,0,1.11.576L4.8,13.5h6.4l1.122,2.163a.625.625,0,0,0,1.109-.576L12.606,13.5h2.769A.625.625,0,0,0,16,12.875V8A.625.625,0,0,0,15.375,7.375ZM2.547,3.664A2.416,2.416,0,0,1,4.961,1.25h6.078a2.416,2.416,0,0,1,2.414,2.414V7.375h-.578A.625.625,0,0,0,12.25,8V9.75H3.75V8a.625.625,0,0,0-.625-.625H2.547Zm12.2,8.586H1.25V8.625H2.5v1.75A.625.625,0,0,0,3.125,11h9.75a.625.625,0,0,0,.625-.625V8.625h1.25Z" transform="translate(4 4)" />
                                            </svg>
                                        </span>
                                        <span class="view-list__item-label"><?php echo Label::getLabel('LBL_SEATS'); ?> - <?php echo $class['grpcls_total_seats']; ?></span>
                                    </li>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">
                                            <svg class="icon icon--notes" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <g transform="translate(-2)">
                                            <path d="M16.222,17.556H3.778A.778.778,0,0,1,3,16.778v-14A.778.778,0,0,1,3.778,2H16.222A.778.778,0,0,1,17,2.778v14A.778.778,0,0,1,16.222,17.556ZM15.444,16V3.556H4.556V16ZM6.889,5.889h6.222V7.444H6.889ZM6.889,9h6.222v1.556H6.889Zm0,3.111h6.222v1.556H6.889Z" transform="translate(4 2)" />
                                            </g>
                                            </svg>
                                        </span>
                                        <span class="view-list__item-label"><?php echo Label::getLabel('LBL_LESSONS'); ?> - <?php echo (count($pkgclses) < 1) ? '1' : count($pkgclses); ?></span>
                                    </li>
                                    <li class="view-list__item">
                                        <span class="view-list__item-media margin-right-2">

                                            <svg class="icon icon--tag" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                            <g transform="translate(-1.471)">

                                            <path d="M8.711,2.1l7.615,1.089L17.414,10.8l-7.071,7.071a.769.769,0,0,1-1.088,0L1.639,10.26a.769.769,0,0,1,0-1.088Zm.544,1.632L3.271,9.716,9.8,16.243l5.983-5.983-.815-5.712L9.255,3.732Zm1.631,4.9a1.539,1.539,0,1,1,2.177,0A1.539,1.539,0,0,1,10.886,8.628Z" transform="translate(4.057 1.9)" />
                                            </g>
                                            </svg>

                                        </span>
                                        <span class="view-list__item-label"><?php echo MyUtility::formatMoney($class['grpcls_entry_fee']); ?></span>
                                    </li>
                                    <?php $seatleft = $class['grpcls_total_seats'] - $bookedSeats; ?>
                                    <?php if ($seatleft < 10 && $seatleft > 0) { ?>
                                        <li class="view-list__item is-hurry">
                                            <span class="view-list__item-media margin-right-2">
                                                <svg class="icon icon--fire" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24">
                                                <g transform="translate(-2)">
                                                <path d="M11,20.134a6.5,6.5,0,0,0,6.5-6.5,6.527,6.527,0,0,0-.433-2.141q-2.167,2.141-3.293,2.141c3.462-6.067,1.56-8.667-3.64-12.134.433,4.333-2.423,6.3-3.586,7.4A6.5,6.5,0,0,0,11,20.134Zm.615-15.4c2.809,2.383,2.823,4.236.653,8.038a1.733,1.733,0,0,0,1.505,2.592,3.857,3.857,0,0,0,1.837-.516,4.767,4.767,0,1,1-7.876-4.691c.109-.1.663-.594.687-.615.367-.329.67-.621.969-.941a9.081,9.081,0,0,0,2.224-3.867Z" transform="translate(2 -0.5)" />
                                                </g>
                                                </svg>
                                            </span>
                                            <span class="view-list__item-label"><?php echo str_replace('{seats}', $seatleft, Label::getLabel('LBL_HURRY_ONLY_{seats}_SEATS_LEFT')); ?></span>
                                        </li>
                                    <?php } ?>
                                </ul>
                            </div>
                        </div>
                        <div class="view-box__footer">
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
                            <?php if ($class['grpcls_already_booked']) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_ALREADY_BOOKED') ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($class['grpcls_booked_seats'] >= $class['grpcls_total_seats']) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_FULL') ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($class['grpcls_start_datetime'] < date('Y-m-d H:i:s', strtotime('+' . $bookingBefore . ' minutes', $class['grpcls_currenttime_unix']))) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_BOOKING_CLOSED') ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($siteUserId == $class['grpcls_teacher_id']) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CANNOT_BOOK_OWN_CLASS'); ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($class['grpcls_status'] != GroupClass::SCHEDULED) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_NOT_ACTIVE') ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($bookedSeats >= $class['grpcls_total_seats']) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_PROCESSING_CLASS_ORDER_TEXT') ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($bookedSeats >= $class['grpcls_total_seats']) { ?>
                                <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CLASS_HOLD_INFO'); ?>" class="btn btn--block btn--primary btn--large btn--disabled"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } elseif ($isPackage) { ?>
                                <a href="javascript:void(0);" onclick="cart.addPackage(<?php echo $class['grpcls_id']; ?>)" class="btn btn--block btn--primary btn--large"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } else { ?>
                                <a href="javascript:void(0);" onclick="cart.addClass(<?php echo $class['grpcls_id']; ?>)" class="btn btn--block btn--primary btn--large"><?php echo Label::getLabel("LBL_BOOK_NOW") ?></a>
                            <?php } ?>
                        </div>
                    </div>
                    <div class="sharing-view align-center margin-top-8">
                        <h6><?php echo Label::getLabel('LBL_SHARE_THIS_CLASS'); ?></h6>
                        <ul class="social--share clearfix">
                            <li class="social--fb"><a class='st-custom-button' data-network="facebook" displayText='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>' title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_01.svg" alt="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>"></a></li>
                            <li class="social--tw"><a class='st-custom-button' data-network="twitter" displayText='<?php echo Label::getLabel('LBL_TWITTER'); ?>' title='<?php echo Label::getLabel('LBL_TWITTER'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_02.svg" alt="<?php echo Label::getLabel('LBL_TWITTER'); ?>"></a></li>
                            <li class="social--pt"><a class='st-custom-button' data-network="pinterest" displayText='<?php echo Label::getLabel('LBL_PINTEREST'); ?>' title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_05.svg" alt="<?php echo Label::getLabel('LBL_PINTEREST'); ?>"></a></li>
                            <li class="social--mail"><a class='st-custom-button' data-network="email" displayText='<?php echo Label::getLabel('LBL_EMAIL'); ?>' title='<?php echo Label::getLabel('LBL_EMAIL'); ?>'><img src="<?php echo CONF_WEBROOT_URL; ?>images/social_06.svg" alt="<?php echo Label::getLabel('LBL_EMAIL'); ?>"></a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- ] -->
            <!-- [ PANEL LARGE 2 ========= -->
            <div class="view-panel__large">
                <div class="view-panel__content  margin-bottom-16">
                    <div class="cms-container">
                        <h4><?php echo Label::getLabel('LBL_CLASS_DESCRIPTION'); ?></h4>
                        <p><?php echo nl2br($class['grpcls_description']); ?></p>
                    </div>
                </div>
                <div class="view-panel__content">
                    <?php if (count($pkgclses)) { ?>
                        <h4><?php echo Label::getLabel('LBL_CLASSES') . ' (' . count($pkgclses) . ')'; ?> </h4>
                        <div class="class-list">
                            <?php foreach ($pkgclses as $pkgcls) { ?>
                                <div class="class-list__item">
                                    <h5><?php echo $pkgcls['grpcls_title']; ?></h5>
                                    <?php
                                    $minLbl = Label::getLabel('LBL_{minutes}_Minutes');
                                    $minLbl = str_replace('{minutes}', $class['grpcls_duration'], $minLbl);
                                    ?>
                                    <p><?php echo date('M d, Y - H:i', strtotime($pkgcls['grpcls_start_datetime'])); ?> (<?php echo $minLbl; ?>)</p>
                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>
                </div>
            </div>
            <!-- ] -->
        </div>
    </div>
</section>
<?php if (count($moreClasses) > 0) { ?>
    <section class="section section--gray section--upcoming-class">
        <div class="container container--narrow">
            <div class="section__head d-flex justify-content-between align-items-center">
                <H3>
                    <?php echo Label::getLabel('LBL_MORE_GROUP_CLASSES_FROM'); ?>
                    <strong><?php echo $teacherName; ?></strong>
                </H3>
            </div>
            <div class="section__body">
                <div class="slider slider--onethird slider-onethird-js">
                    <?php
                    foreach ($moreClasses as $class) {
                        $classData = ['class' => $class, 'siteUserId' => $siteUserId, 'bookingBefore' => $bookingBefore, 'cardClass' => 'card-class-cover'];
                        $this->includeTemplate('group-classes/card.php', $classData, false);
                    }
                    ?>
                </div>
            </div>
        </div>
    </section>
<?php } ?>
<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>