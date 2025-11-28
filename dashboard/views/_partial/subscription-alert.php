<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<?php if (!empty($subscriptionBanner['show'])): ?>
    <?php
    $type     = $subscriptionBanner['type'] ?? '';
    $daysLeft = (int)($subscriptionBanner['daysLeft'] ?? 0);
    $userId   = $siteUser['user_id'] ?? 0;

    // key used for localStorage so “Close” is remembered per user+banner type
    $bannerKey = 'rwus_sub_banner_' . $userId . '_' . $type;
    ?>

    <style>
        .sub-alert {
            background: #ff4b4b;
            color: #fff;
            padding: 10px 16px;
            font-size: 14px;
            position: sticky;
            top: 0;
            z-index: 1050; /* above dashboard header/sidebar */
        }
        .sub-alert__content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
        }
        .sub-alert__text {
            font-weight: 500;
        }
        .sub-alert__actions {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }
        .sub-alert__btn {
            background: #fff;
            color: #ff4b4b;
            border-radius: 4px;
            padding: 4px 12px;
            border: none;
            font-size: 13px;
            cursor: pointer;
            font-weight: 500;
        }
        .sub-alert__btn a {
            color: inherit;
        }
        .sub-alert__close {
            background: transparent;
            border: none;
            color: #fff;
            font-size: 18px;
            line-height: 1;
            cursor: pointer;
            padding: 0 4px;
        }
        @media (max-width: 767px) {
            .sub-alert__content {
                flex-direction: column;
                align-items: flex-start;
            }
            .sub-alert__actions {
                width: 100%;
                justify-content: flex-start;
            }
        }
    </style>

    <div class="sub-alert js-sub-alert" data-banner-key="<?php echo $bannerKey; ?>">
        <div class="sub-alert__content">
            <div class="sub-alert__text">
                <?php if ($type === 'expiring'): ?>
                    <?php
                        if ($daysLeft <= 0) {
                            // last hours
                            echo Label::getLabel('LBL_YOUR_SUBSCRIPTION_EXPIRES_TODAY_RENEW_NOW');
                        } else {
                            echo sprintf(
                                Label::getLabel('LBL_YOUR_SUBSCRIPTION_EXPIRES_IN_%s_DAY(S)_PLEASE_RENEW'),
                                $daysLeft
                            );
                        }
                    ?>
                <?php else: ?>
                    <?php echo Label::getLabel('LBL_NO_ACTIVE_SUBSCRIPTION_PLEASE_SUBSCRIBE'); ?>
                <?php endif; ?>
            </div>
            <div class="sub-alert__actions">
                <button class="sub-alert__btn" type="button">
                    <a href="<?php echo MyUtility::makeUrl('MySubscriptions'); ?>">
                        <?php echo Label::getLabel('LBL_VIEW_SUBSCRIPTION_PLANS'); ?>
                    </a>
                </button>
                <button type="button" class="sub-alert__close js-sub-alert-close" aria-label="Close">
                    &times;
                </button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            var bar = document.querySelector('.js-sub-alert');
            if (!bar) return;

            var key = bar.getAttribute('data-banner-key') || '';
            try {
                if (key && window.localStorage && localStorage.getItem(key) === 'hidden') {
                    bar.parentNode.removeChild(bar);
                    return;
                }
            } catch (e) {
                // localStorage not available, ignore
            }

            var closeBtn = bar.querySelector('.js-sub-alert-close');
            if (!closeBtn) return;

            closeBtn.addEventListener('click', function () {
                try {
                    if (key && window.localStorage) {
                        localStorage.setItem(key, 'hidden');
                    }
                } catch (e) {}
                bar.parentNode.removeChild(bar);
            });
        })();
    </script>
<?php endif; ?>
