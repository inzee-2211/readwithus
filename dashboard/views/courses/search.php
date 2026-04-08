<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (count($courses) == 0) {

    // ✅ If learner has no subscription → show custom message
    if (!empty($noActiveSubscription) && $siteUserType == User::LEARNER) { 
        
        // ✅ MUST open application pricing (front), not dashboard
        $pricingUrl = MyUtility::makeUrl('Pricing', 'index', [], CONF_WEBROOT_FRONT_URL);
        ?>
            <style>
        /* ===========================
           RWU: No Subscription State
           Scoped styles (safe to paste)
        ============================ */

        .rwu-empty-wrap {
            position: relative;
            border-radius: 18px;
            overflow: hidden;
            padding: 28px;
            background: linear-gradient(120deg, rgba(0, 190, 180, 0.10), rgba(255, 90, 31, 0.08), rgba(0, 0, 0, 0.02));
            border: 1px solid rgba(0,0,0,.06);
            box-shadow: 0 14px 38px rgba(0,0,0,.06);
        }

        .rwu-empty-wrap:before {
            content: "";
            position: absolute;
            inset: -2px;
            background: radial-gradient(600px 240px at 15% 10%, rgba(0,190,180,.18), transparent 60%),
                        radial-gradient(520px 220px at 85% 0%, rgba(255,90,31,.16), transparent 55%),
                        radial-gradient(700px 320px at 70% 90%, rgba(0,0,0,.06), transparent 60%);
            pointer-events: none;
        }

        .rwu-empty-inner {
            position: relative;
            display: grid;
            grid-template-columns: 1.15fr 0.85fr;
            gap: 20px;
            align-items: start;
        }

        @media (max-width: 991px) {
            .rwu-empty-inner { grid-template-columns: 1fr; }
        }

        .rwu-empty-hero {
            padding: 6px 6px 10px;
        }

        .rwu-pill {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border-radius: 999px;
            background: rgba(255,255,255,.75);
            border: 1px solid rgba(0,0,0,.06);
            backdrop-filter: blur(6px);
            font-weight: 600;
            font-size: 12px;
            letter-spacing: .3px;
        }

        .rwu-empty-title {
            margin: 12px 0 6px;
            font-size: 26px;
            font-weight: 800;
            line-height: 1.15;
            color: #111;
        }

        .rwu-empty-sub {
            margin: 0 0 14px;
            font-size: 14px;
            line-height: 1.7;
            color: rgba(0,0,0,.70);
            max-width: 720px;
        }

        .rwu-benefits {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
            margin-top: 14px;
        }

        @media (max-width: 575px) {
            .rwu-benefits { grid-template-columns: 1fr; }
        }

        .rwu-benefit {
            display: flex;
            gap: 10px;
            padding: 12px 12px;
            border-radius: 14px;
            background: rgba(255,255,255,.74);
            border: 1px solid rgba(0,0,0,.06);
            transition: transform .18s ease, box-shadow .18s ease, border-color .18s ease;
        }

        .rwu-benefit:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(0,0,0,.08);
            border-color: rgba(0,0,0,.10);
        }

        .rwu-dot {
            width: 10px; height: 10px;
            border-radius: 999px;
            margin-top: 5px;
            background: rgba(0, 190, 180, .95);
            box-shadow: 0 0 0 5px rgba(0,190,180,.12);
            flex: 0 0 auto;
        }

        .rwu-benefit strong {
            display: block;
            font-weight: 800;
            color: #111;
            margin-bottom: 2px;
        }

        .rwu-benefit span {
            display: block;
            color: rgba(0,0,0,.68);
            font-size: 13px;
            line-height: 1.55;
        }

        .rwu-cta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 16px;
        }

        .rwu-cta .btn {
            border-radius: 12px;
            transition: transform .15s ease, box-shadow .15s ease, opacity .15s ease;
        }

        .rwu-cta .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 12px 26px rgba(0,0,0,.14);
        }

        .rwu-side {
            padding: 16px;
            border-radius: 16px;
            background: rgba(255,255,255,.78);
            border: 1px solid rgba(0,0,0,.06);
            backdrop-filter: blur(6px);
        }

        .rwu-side h5 {
            margin: 0 0 10px;
            font-size: 14px;
            font-weight: 900;
            color: #111;
            letter-spacing: .2px;
        }

        .rwu-mini {
            display: grid;
            gap: 10px;
        }

        .rwu-mini-card {
            padding: 12px 12px;
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,.06);
            background: #fff;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .rwu-mini-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(0,0,0,.08);
        }

        .rwu-mini-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 6px;
        }

        .rwu-mini-top b {
            font-weight: 900;
            color: #111;
            font-size: 13px;
        }

        .rwu-tag {
            font-size: 11px;
            padding: 5px 8px;
            border-radius: 999px;
            border: 1px solid rgba(0,0,0,.08);
            background: rgba(0,0,0,.03);
            color: rgba(0,0,0,.70);
            white-space: nowrap;
        }

        .rwu-mini-card p {
            margin: 0;
            font-size: 12.5px;
            line-height: 1.6;
            color: rgba(0,0,0,.68);
        }

        /* Modal polish */
        #unlockModalOverlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 12, 16, .62);
            z-index: 9999;
            padding: 16px;
            align-items: center;
            justify-content: center;
        }

        #unlockModalBox {
            width: 100%;
            max-width: 920px;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 20px 60px rgba(0,0,0,.35);
            transform: translateY(10px);
            opacity: 0;
            transition: transform .18s ease, opacity .18s ease;
        }

        #unlockModalOverlay.is-open #unlockModalBox {
            transform: translateY(0);
            opacity: 1;
        }

        .rwu-modal-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 18px;
            border-bottom: 1px solid rgba(0,0,0,.08);
            background: linear-gradient(120deg, rgba(0,190,180,.10), rgba(255,90,31,.08), rgba(0,0,0,.02));
        }

        .rwu-modal-head h4 {
            margin: 0;
            font-size: 16px;
            font-weight: 900;
            color: #111;
        }

        .rwu-close {
            border: 0;
            background: rgba(0,0,0,.06);
            width: 36px;
            height: 36px;
            border-radius: 12px;
            cursor: pointer;
            font-size: 20px;
            line-height: 36px;
        }

        .rwu-close:hover { background: rgba(0,0,0,.10); }

        .rwu-modal-body { padding: 18px; }

        .rwu-feature-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        @media (max-width: 767px) {
            .rwu-feature-grid { grid-template-columns: 1fr; }
        }

        .rwu-feature {
            padding: 14px;
            border-radius: 14px;
            border: 1px solid rgba(0,0,0,.06);
            background: #fff;
            transition: transform .18s ease, box-shadow .18s ease;
        }

        .rwu-feature:hover {
            transform: translateY(-2px);
            box-shadow: 0 14px 26px rgba(0,0,0,.08);
        }

        .rwu-feature h6 {
            margin: 0 0 6px;
            font-size: 13px;
            font-weight: 900;
            color: #111;
        }

        .rwu-feature ul {
            margin: 0;
            padding-left: 18px;
            color: rgba(0,0,0,.70);
            line-height: 1.7;
        }

        .rwu-modal-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            justify-content: flex-end;
            margin-top: 14px;
        }

        @media (prefers-reduced-motion: reduce) {
            .rwu-benefit, .rwu-mini-card, .rwu-cta .btn, #unlockModalBox, .rwu-feature { transition: none !important; }
        }
    </style>
     
    <div class="rwu-empty-wrap">
        <div class="rwu-empty-inner">

            <!-- Left: Hero + Benefits -->
            <div class="rwu-empty-hero">
                <div class="rwu-pill">
                    <svg class="icon icon--clock icon--small" style="width:14px;height:14px;">
                        <use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#search"></use>
                    </svg>
                    <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION'); ?>
                </div>

                <div class="rwu-empty-title">Unlock Courses, Quizzes & a Structured Study Path</div>
                <p class="rwu-empty-sub">
                    <?php echo Label::getLabel('LBL_PLEASE_SUBSCRIBE_TO_ACCESS_COURSES'); ?>
                    Choose a plan to access courses for your subjects and start learning with a clear roadmap.
                </p>

                <div class="rwu-benefits">
                    <div class="rwu-benefit">
                        <span class="rwu-dot"></span>
                        <div>
                            <strong>Unlimited course access</strong>
                            <span>For the subjects included in your plan.</span>
                        </div>
                    </div>

                    <div class="rwu-benefit">
                        <span class="rwu-dot"></span>
                        <div>
                            <strong>Structured learning path</strong>
                            <span>Exam board + tier so you always know what’s next.</span>
                        </div>
                    </div>

                    <div class="rwu-benefit">
                        <span class="rwu-dot"></span>
                        <div>
                            <strong>Quizzes + exam practice</strong>
                            <span>Improve performance with real patterns.</span>
                        </div>
                    </div>

                    <div class="rwu-benefit">
                        <span class="rwu-dot"></span>
                        <div>
                            <strong>Progress tracking</strong>
                            <span>Stay consistent and complete faster.</span>
                        </div>
                    </div>
                </div>

                <div class="rwu-cta">
                    <a class="btn btn--primary" href="<?php echo $pricingUrl; ?>">
                        <?php echo Label::getLabel('LBL_VIEW_PLANS'); ?>
                    </a>

                    <a class="btn btn--bordered color-secondary" href="javascript:void(0);" onclick="openUnlockModal();">
                        See what you’ll unlock
                    </a>
                </div>
            </div>

            <!-- Right: Side cards (interactive) -->
            <aside class="rwu-side">
                <h5>What you get with subscription</h5>
                <div class="rwu-mini">
                    <div class="rwu-mini-card">
                        <div class="rwu-mini-top">
                            <b>New content regularly</b>
                            <span class="rwu-tag">Updated</span>
                        </div>
                        <p>Fresh lessons and practice added across levels and subjects.</p>
                    </div>

                    <div class="rwu-mini-card">
                        <div class="rwu-mini-top">
                            <b>Better value</b>
                            <span class="rwu-tag">Save</span>
                        </div>
                        <p>Cheaper than buying individual courses separately.</p>
                    </div>

                    <div class="rwu-mini-card">
                        <div class="rwu-mini-top">
                            <b>Learn anywhere</b>
                            <span class="rwu-tag">Any device</span>
                        </div>
                        <p>Access your study plan and progress from any device.</p>
                    </div>
                </div>
            </aside>

        </div>
    </div>

    <!-- ✅ Modal (upgraded UI, same functions + IDs) -->
    <div id="unlockModalOverlay" role="dialog" aria-modal="true" aria-label="What you’ll unlock">
        <div id="unlockModalBox">
            <div class="rwu-modal-head">
                <h4>What you’ll unlock with a subscription</h4>
                <button type="button" class="rwu-close" aria-label="Close" onclick="closeUnlockModal();">&times;</button>
            </div>

            <div class="rwu-modal-body">
                <div class="rwu-feature-grid">
                    <div class="rwu-feature">
                        <h6>Learning experience</h6>
                        <ul>
                            <li>Unlimited access to courses in your subscribed subjects</li>
                            <li>Structured tiers (Exam board → Tier) with clear progression</li>
                            <li>Quizzes + exam practice to boost scores</li>
                            <li>Progress tracking to keep you consistent</li>
                        </ul>
                    </div>

                    <div class="rwu-feature">
                        <h6>Value & support</h6>
                        <ul>
                            <li>New lessons and practice material added regularly</li>
                            <li>Better value than purchasing courses individually</li>
                            <li>Access from any device — learn anytime</li>
                            <li>Clear milestones to stay on track</li>
                        </ul>
                    </div>
                </div>

                <div class="rwu-modal-actions">
                    <a class="btn btn--primary" href="<?php echo $pricingUrl; ?>">View Plans</a>
                    <a class="btn btn--bordered" href="javascript:void(0);" onclick="closeUnlockModal();">Not now</a>
                </div>
            </div>
        </div>
    </div>

       <script>
        function openUnlockModal() {
            var overlay = document.getElementById('unlockModalOverlay');
            if (!overlay) return;
            overlay.style.display = 'flex';
            overlay.classList.add('is-open');
        }

        function closeUnlockModal() {
            var overlay = document.getElementById('unlockModalOverlay');
            if (!overlay) return;
            overlay.classList.remove('is-open');
            overlay.style.display = 'none';
        }

        // close on background click
        document.addEventListener('click', function(e){
            var overlay = document.getElementById('unlockModalOverlay');
            if (!overlay || overlay.style.display !== 'flex') return;
            if (e.target === overlay) closeUnlockModal();
        });

        // close on ESC
        document.addEventListener('keydown', function(e){
            var overlay = document.getElementById('unlockModalOverlay');
            if (!overlay || overlay.style.display !== 'flex') return;
            if (e.key === 'Escape') closeUnlockModal();
        });
    </script>
    <?php
        return;
    }

    // otherwise keep default empty state (filters / no courses)
    $this->includeTemplate('_partial/no-record-found.php');
    return;
}

$requestStatuses = Course::getRefundStatuses();
?>

 
<div class="course-group">
    <!-- [ COURSE CARD ========= -->
    <?php 
    
   
    foreach ($courses as $course) {
            $course['can_view_course'] = $course['can_view_course'] ?? true;
        $course['can_edit_course'] = $course['can_edit_course'] ?? false;
        $course['can_delete_course'] = $course['can_delete_course'] ?? false;
        $course['can_cancel_course'] = $course['can_cancel_course'] ?? false;
        $course['can_rate_course'] = $course['can_rate_course'] ?? false;
        $course['can_retake_course'] = $course['can_retake_course'] ?? false;
        $course['can_download_certificate'] = $course['can_download_certificate'] ?? false;
        $course['crspro_progress'] = $course['crspro_progress'] ?? 0;
        $course['crspro_status'] = $course['crspro_status'] ?? CourseProgress::PENDING;
        $course['ordcrs_id'] = $course['ordcrs_id'] ?? 0;
        $course['crspro_id'] = $course['crspro_id'] ?? 0; ?>
        <div class="card-course">
            <div class="card-course__colum card-course__colum--first">
                <div class="ratio ratio--16by9">
                    <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL); ?>" alt="">
                </div>
            </div>
            <div class="card-course__colum card-course__colum--second">
                <div class="card-course__head">
                    <small class="card-course__subtitle uppercase color-gray-900">
                        <?php echo $course['cate_name'] ?>
                        <?php echo !empty($course['subcate_name']) ? ' / ' . $course['subcate_name'] : ''; ?>
                    </small>
                    <span class="card-course__title">
                        <?php echo $course['course_title'] ?>
                    </span>
                </div>
                <div class="card-course__body">
                    <div class="course-stats">
                        
                        <span class="course-stats__item">
                            <?php echo Label::getLabel('LBL_LECTURES') ?>
                            <strong> <?php echo $course['course_lectures'] ?></strong>
                        </span>
                  
                       
                         
                        
                        
                        </div>

                   
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php
                        $color = 'color-warning';
                        if ($course['course_status'] == Course::PUBLISHED) {
                            $color = 'color-success';
                        } elseif ($course['course_status'] == Course::SUBMITTED) {
                            $color = 'color-info';
                        }
                        ?>
                        <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                            <?php echo $courseStatuses[$course['course_status']]; ?>
                        </span>
                    <?php } else { ?>
                        <?php
                        $color = 'color-success';
                        if ($course['crspro_status'] == CourseProgress::CANCELLED) {
                            $color = 'color-danger';
                        } elseif ($course['crspro_status'] == CourseProgress::PENDING) {
                            $color = 'color-warning';
                        } elseif ($course['crspro_status'] == CourseProgress::IN_PROGRESS) {
                            $color = 'color-info';
                        }
                        ?>
                        <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                            <?php echo $orderStatuses[$course['crspro_status']]; ?>
                        </span>                        
                    <?php } ?>
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php if ($course['course_active'] == AppConstant::INACTIVE) { ?>
                            <span class="card-landscape__status badge color-danger badge--curve badge--small margin-left-0">
                                <?php echo AppConstant::getActiveArr($course['course_active']); ?>
                            </span>
                        <?php } ?>
                    <?php } ?>
                    <?php if ($siteUserType == User::LEARNER) { ?>
                        <?php if (isset($course['corere_status'])) { ?>
                            <?php
                            $color = 'color-success';
                            if ($course['corere_status'] == Course::REFUND_DECLINED) {
                                $color = 'color-danger';
                            } elseif ($course['corere_status'] == Course::REFUND_PENDING) {
                                $color = 'color-warning';
                            }
                            ?>
                            <span class="card-landscape__status badge <?php echo $color; ?> badge--curve badge--small margin-left-0">
                                <?php echo $requestStatuses[$course['corere_status']]; ?>
                            </span>
                        <?php } ?>
                    <?php } ?>

                   

                    <?php if ($siteUserType == User::LEARNER && (!isset($course['corere_status']) || $course['corere_status'] != Course::REFUND_APPROVED)) { ?>
                    <div class="course-progress margin-top-2">
                        <div class="course-progress__value"><?php echo Label::getLabel('LBL_COURSE_PROGRESS'); ?></div>
                        <div class="course-progress__content">
                            <div class="progress progress--xsmall progress--round">
                                <?php if ( $course['crspro_progress'] > 0) { ?>
                                <div class="progress__bar bg-green" role="progressbar" style="width:<?php echo $course['crspro_progress']; ?>%" aria-valuenow="25" aria-valuemin="0" aria-valuemax="100"></div>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="course-progress__value"><?php echo $course['crspro_progress']; ?>%</div>
                    </div>
                    <?php } ?> 
                </div>
               
            </div>
            <div class="card-course__colum card-course__colum--third">
                <div class="actions-group">
                    <?php if ($siteUserType == User::TEACHER) { ?>
                        <?php if ($course['course_sections'] > 0 && $course['course_lectures'] > 0) { ?>
                            <a href="<?php echo MyUtility::makeUrl('CoursePreview', 'index', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_PREVIEW'); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                <svg class="icon icon--enter icon--18">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#view-icon"></use>
                                </svg>
                                <div class="tooltip tooltip--top bg-black">
                                    <?php echo Label::getLabel('LBL_PREVIEW'); ?>
                                </div>
                            </a>
                        <?php } ?>
                    <?php } elseif ($course['can_view_course']) { ?>
                       <a href="<?php echo MyUtility::makeUrl('Tutorials', 'startByCourse', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_VIEW'); ?>" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                            <svg class="icon icon--enter icon--18">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#view-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_VIEW'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_edit_course']) { ?>
                        <a href="<?php echo MyUtility::makeUrl('Courses', 'form', [$course['course_id']]); ?>" title="<?php echo Label::getLabel('LBL_EDIT'); ?>" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#edit"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_EDIT'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_delete_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_DELETE'); ?>" onclick="remove('<?php echo $course['course_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#delete-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_DELETE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_cancel_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_CANCEL'); ?>" onclick="cancelForm('<?php echo $course['ordcrs_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#cancel"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_CANCEL'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_rate_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_RATE'); ?>" onclick="feedbackForm('<?php echo $course['ordcrs_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#review-star"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_RATE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_retake_course']) { ?>
                        <a href="javascript:void(0);" title="<?php echo Label::getLabel('LBL_RETAKE'); ?>" onclick="retake('<?php echo $course['crspro_id']; ?>')" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#retake"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_RETAKE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                    <?php if ($course['can_download_certificate']) { ?>
                        <a href="<?php echo MyUtility::makeUrl('Certificates', 'index', [$course['crspro_id']], CONF_WEBROOT_DASHBOARD); ?>" target="_blank" title="<?php echo Label::getLabel('LBL_DOWNLOAD_CERTIFICATE'); ?>" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#download-icon"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_DOWNLOAD_CERTIFICATE'); ?>
                            </div>
                        </a>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
    <!-- ] ========= -->
</div>
<?php
$pagingArr = [
    'page' => $post['page'],
    'pageSize' => $post['pagesize'],
    'pageCount' => ceil($recordCount / $post['pagesize']),
    'recordCount' => $recordCount,
    'callBackJsFunc' => 'goToSearchPage'
];

$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmPaging']);
?>
<script>
function openUnlockModal() {
    var overlay = document.getElementById('unlockModalOverlay');
    if (overlay) overlay.style.display = 'flex';
}
function closeUnlockModal() {
    var overlay = document.getElementById('unlockModalOverlay');
    if (overlay) overlay.style.display = 'none';
}
// close on background click
document.addEventListener('click', function(e){
    var overlay = document.getElementById('unlockModalOverlay');
    if (!overlay || overlay.style.display !== 'flex') return;
    if (e.target === overlay) closeUnlockModal();
});
// close on ESC
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeUnlockModal();
});
</script>
