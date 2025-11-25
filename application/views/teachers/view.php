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
<style>
/* ===== MODERN TEACHER PROFILE STYLES ===== */
.rwu-teacher-profile {
    background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    min-height: 100vh;
    padding: 40px 0;
}

.rwu-teacher-header {
    background: white;
    border-radius: 20px;
    margin-bottom: 30px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
    overflow: hidden;
}

.rwu-teacher-hero {
    padding: 40px;
    background: radial-gradient(circle at top left, rgba(45, 173, 255, 0.06), transparent 55%);
}

.rwu-teacher-main-info {
    display: grid;
    grid-template-columns: auto 1fr auto;
    gap: 30px;
    align-items: start;
}

.rwu-teacher-avatar {
    text-align: center;
}

.rwu-avatar-large {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    overflow: hidden;
    margin-bottom: 12px;
    border: 4px solid #f1f5f9;
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
}

.rwu-avatar-large img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rwu-teacher-flag {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: rgba(148, 163, 184, 0.08);
    padding: 6px 12px;
    border-radius: 999px;
    font-size: 0.875rem;
    color: #64748b;
    border: 1px solid rgba(148, 163, 184, 0.3);
}

.rwu-teacher-flag img {
    width: 20px;
    height: 15px;
    border-radius: 2px;
}

.rwu-teacher-details {
    padding: 0 20px;
}

.rwu-teacher-name h1 {
    font-size: 2.5rem;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 8px;
    line-height: 1.2;
}

.rwu-teacher-name small {
    display: block;
    font-size: 0.9rem;
    color: #64748b;
    margin-bottom: 16px;
}

.rwu-teacher-stats {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px 24px;
    margin-bottom: 20px;
}

.rwu-stat-item {
    display: flex;
    align-items: center;
    gap: 12px;
}

/* UPDATED: cleaner, "transparent" icon style (no blue box) */
.rwu-stat-icon {
    width: 40px;
    height: 40px;
    border-radius: 999px;
    background: rgba(148, 163, 184, 0.06); /* very subtle, almost transparent */
    border: 1px solid rgba(148, 163, 184, 0.35);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    backdrop-filter: blur(6px);
}

.rwu-stat-icon svg {
    width: 20px;
    height: 20px;
    fill: #2DADFF; /* brand colour on icon, not background */
}

.rwu-stat-content {
    display: flex;
    flex-direction: column;
}

.rwu-stat-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: #0f172a;
}

.rwu-stat-label {
    font-size: 0.875rem;
    color: #64748b;
}

.rwu-teacher-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    padding-top: 4px;
    border-top: 1px dashed #e2e8f0;
    margin-top: 10px;
}

.rwu-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
}

.rwu-meta-label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.9rem;
}

.rwu-meta-value {
    color: #0f172a;
    font-weight: 500;
    font-size: 0.95rem;
}

.rwu-teacher-actions {
    display: flex;
    flex-direction: column;
    gap: 12px;
    min-width: 220px;
}

.rwu-action-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 10px 16px;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    background: rgba(255, 255, 255, 0.9);
    color: #475569;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
}

.rwu-action-btn:hover {
    border-color: #2DADFF;
    color: #2DADFF;
    box-shadow: 0 8px 20px rgba(45, 173, 255, 0.15);
    transform: translateY(-1px);
}

.rwu-action-btn.active {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.rwu-action-btn.active .rwu-action-icon {
    fill: #dc2626;
}

.rwu-action-icon {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.rwu-share-dropdown {
    position: relative;
}

.rwu-share-menu {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 16px;
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    display: none;
    z-index: 1000;
    margin-top: 8px;
}

.rwu-share-menu.active {
    display: block;
}

.rwu-share-menu h6 {
    margin: 0 0 12px 0;
    font-size: 0.875rem;
    color: #64748b;
    text-align: center;
}

.rwu-share-buttons {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}

.rwu-share-buttons a {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 8px;
    border-radius: 8px;
    background: #f8fafc;
    transition: all 0.2s ease;
}

.rwu-share-buttons a:hover {
    background: #2DADFF;
    transform: translateY(-1px);
}

.rwu-share-buttons a:hover svg {
    fill: white;
}

.rwu-share-buttons svg {
    width: 16px;
    height: 16px;
    fill: #64748b;
}

.rwu-action-link {
    color: #2DADFF;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    padding: 6px 10px;
    border-radius: 999px;
    transition: all 0.2s ease;
    font-size: 0.9rem;
}

.rwu-action-link:hover {
    background: #f0f9ff;
    color: #1992DF;
}

/* Main Layout */
.rwu-teacher-layout {
    display: grid;
    grid-template-columns: 1fr 400px;
    gap: 30px;
    align-items: start;
}

.rwu-teacher-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

.rwu-teacher-sidebar {
    position: sticky;
    top: 100px;
}

/* Panel Styles */
.rwu-panel-modern {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.rwu-panel-header {
    padding: 24px 30px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
    cursor: pointer;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.rwu-panel-header h2 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 600;
    color: #0f172a;
}

.rwu-panel-body {
    padding: 30px;
    display: none;
}

.rwu-panel-body:first-child {
    display: block;
}

.rwu-panel-header.is-active + .rwu-panel-body {
    display: block;
}

/* Language Selector */
.rwu-language-selector {
    display: flex;
    align-items: center;
    gap: 12px;
}

.rwu-language-selector label {
    font-weight: 600;
    color: #64748b;
    font-size: 0.875rem;
}

.rwu-select-modern {
    padding: 8px 12px;
    height: auto;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    background: white;
    color: #0f172a;
    font-size: 0.875rem;
}

/* Pricing Grid */
.rwu-pricing-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.rwu-pricing-card {
    background: #f8fafc;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.rwu-pricing-card:hover {
    border-color: #2DADFF;
    box-shadow: 0 8px 25px rgba(45, 173, 255, 0.15);
}

.rwu-pricing-header {
    padding: 20px;
    background: white;
    border-bottom: 1px solid #e2e8f0;
}

.rwu-pricing-header h4 {
    margin: 0;
    color: #2DADFF;
    font-size: 1.125rem;
    font-weight: 600;
}

.rwu-pricing-body {
    padding: 20px;
}

.rwu-pricing-options {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.rwu-option-btn {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px;
    background: white;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.2s ease;
    width: 100%;
}

.rwu-option-btn:hover:not(:disabled) {
    border-color: #2DADFF;
    background: #f0f9ff;
}

.rwu-option-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.rwu-option-duration {
    font-weight: 600;
    color: #0f172a;
}

.rwu-option-price {
    font-weight: 700;
    color: #2DADFF;
    font-size: 1.125rem;
}

/* Calendar */
.rwu-calendar-wrapper {
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.rwu-calendar {
    min-height: 400px;
    position: relative;
}

/* Note */
.rwu-note-modern {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    padding: 20px;
    background: #f0f9ff;
    border: 1px solid #bae6fd;
    border-radius: 12px;
    margin-top: 20px;
}

.rwu-note-icon {
    width: 24px;
    height: 24px;
    fill: #0ea5e9;
    flex-shrink: 0;
    margin-top: 2px;
}

.rwu-note-content {
    flex: 1;
    color: #0369a1;
    line-height: 1.5;
}

.rwu-note-link {
    background: none;
    border: none;
    color: #0ea5e9;
    font-weight: 600;
    cursor: pointer;
    text-decoration: underline;
}

.rwu-note-link:hover {
    color: #0284c7;
}

/* Classes Grid */
.rwu-classes-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* Expertise */
.rwu-expertise-section {
    margin-bottom: 24px;
}

.rwu-expertise-section h4 {
    margin: 0 0 16px 0;
    color: #0f172a;
    font-size: 1.125rem;
}

.rwu-expertise-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 12px;
}

.rwu-expertise-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    background: #f8fafc;
    border-radius: 8px;
}

.rwu-check-icon {
    width: 16px;
    height: 16px;
    fill: #10b981;
    flex-shrink: 0;
}

/* Qualifications */
.rwu-qualification-section {
    margin-bottom: 32px;
}

.rwu-qualification-section h4 {
    margin: 0 0 20px 0;
    color: #0f172a;
    font-size: 1.25rem;
    font-weight: 600;
}

.rwu-qualification-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.rwu-qualification-item {
    display: grid;
    grid-template-columns: 120px 1fr;
    gap: 20px;
    padding: 20px;
    background: #f8fafc;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
}

.rwu-qualification-period {
    font-weight: 600;
    color: #2DADFF;
}

.rwu-qualification-details h5 {
    margin: 0 0 8px 0;
    color: #0f172a;
    font-size: 1.125rem;
}

.rwu-qualification-details p {
    margin: 0 0 4px 0;
    color: #64748b;
}

.rwu-qualification-location {
    font-size: 0.875rem;
    color: #94a3b8;
}

/* Reviews */
.rwu-reviews-overview {
    display: flex;
    align-items: center;
    gap: 30px;
    margin-bottom: 30px;
    padding: 24px;
    background: #f8fafc;
    border-radius: 16px;
}

.rwu-rating-display {
    display: flex;
    align-items: center;
    gap: 16px;
}

.rwu-rating-circle {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    background: linear-gradient(135deg, #2DADFF, #14A3FF);
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: white;
    position: relative;
}

.rwu-rating-score {
    font-size: 1.5rem;
    font-weight: 700;
}

.rwu-rating-stars {
    width: 20px;
    height: 20px;
    fill: white;
}

.rwu-rating-text {
    display: flex;
    flex-direction: column;
}

.rwu-rating-value {
    font-weight: 600;
    color: #0f172a;
    font-size: 1.125rem;
}

.rwu-rating-count {
    color: #64748b;
    font-size: 0.875rem;
}

.rwu-reviews-container {
    background: white;
    border-radius: 16px;
    border: 1px solid #e2e8f0;
    overflow: hidden;
}

.rwu-reviews-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px;
    background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}

.rwu-reviews-sorting {
    min-width: 200px;
}

.rwu-reviews-list {
    padding: 24px;
}

/* Sidebar */
.rwu-video-modern {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    margin-bottom: 24px;
}

.rwu-video-container {
    border-radius: 16px;
    overflow: hidden;
}

.rwu-booking-card {
    background: white;
    border-radius: 20px;
    padding: 24px;
    box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
}

.rwu-booking-actions {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.rwu-book-btn {
    background: linear-gradient(135deg, #2DADFF 0%, #14A3FF 100%);
    color: white;
    border: none;
    padding: 16px 24px;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rwu-book-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(45, 173, 255, 0.4);
}

.rwu-book-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.rwu-contact-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 24px;
    border: 1px solid #2DADFF;
    border-radius: 12px;
    background: white;
    color: #2DADFF;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.rwu-contact-btn:hover:not(:disabled) {
    background: #f0f9ff;
    transform: translateY(-1px);
}

.rwu-contact-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.rwu-contact-icon {
    width: 20px;
    height: 20px;
    fill: currentColor;
}

.rwu-availability-link {
    color: #2DADFF;
    text-decoration: none;
    font-weight: 600;
    text-align: center;
    padding: 8px;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.rwu-availability-link:hover {
    background: #f0f9ff;
}

.rwu-trial-section {
    padding-top: 20px;
    border-top: 1px solid #e2e8f0;
    text-align: center;
}

.rwu-trial-btn {
    width: 100%;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    margin-bottom: 8px;
}

.rwu-trial-btn--primary {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
}

.rwu-trial-btn--primary:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(16, 185, 129, 0.4);
}

.rwu-trial-btn--secondary {
    background: #6b7280;
    color: white;
}

.rwu-trial-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

.rwu-trial-note {
    font-size: 0.875rem;
    color: #64748b;
    margin: 0;
    line-height: 1.4;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .rwu-teacher-layout {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .rwu-teacher-sidebar {
        position: static;
        order: -1;
    }

    .rwu-teacher-main-info {
        grid-template-columns: 1fr;
        text-align: center;
    }

    .rwu-teacher-details {
        padding: 0;
    }

    .rwu-teacher-actions {
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
    }

    .rwu-teacher-meta {
        border-top: none;
        margin-top: 4px;
    }
}

@media (max-width: 768px) {
    .rwu-teacher-hero {
        padding: 24px;
    }

    .rwu-teacher-stats {
        grid-template-columns: 1fr 1fr;
    }

    .rwu-panel-header {
        padding: 20px;
        flex-direction: column;
        gap: 16px;
        align-items: flex-start;
    }

    .rwu-panel-body {
        padding: 20px;
    }

    .rwu-pricing-grid {
        grid-template-columns: 1fr;
    }

    .rwu-qualification-item {
        grid-template-columns: 1fr;
        gap: 12px;
    }

    .rwu-reviews-overview {
        flex-direction: column;
        text-align: center;
    }
}

@media (max-width: 480px) {
    .rwu-teacher-name h1 {
        font-size: 2rem;
    }

    .rwu-avatar-large {
        width: 100px;
        height: 100px;
    }

    .rwu-booking-card {
        padding: 20px;
    }

    .rwu-teacher-stats {
        grid-template-columns: 1fr;
    }
}
</style>

<section class="rwu-teacher-profile">
    <div class="container container--fixed">
        <!-- Teacher Header -->
        <div class="rwu-teacher-header">
            <div class="rwu-teacher-hero">
                <div class="rwu-teacher-main-info">
                    <div class="rwu-teacher-avatar">
                        <div class="rwu-avatar-large">
                            <?php
                            $img = FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM]), CONF_DEF_CACHE_TIME, '.' . current(array_reverse(explode(".", $teacher['user_photo']))));
                            echo '<img src="' . $img . '" alt="' . $teacher['user_first_name'] . ' ' . $teacher['user_last_name'] . '" />';
                            ?>
                        </div>
                        <?php if ($teacher['user_country_id'] > 0) { ?>
                        <div class="rwu-teacher-flag">
                            <img src="<?php echo CONF_WEBROOT_FRONTEND . 'flags/' . strtolower($teacher['user_country_code']) . '.svg'; ?>" alt="<?php echo $teacher['user_country_name']; ?>" />
                            <span><?php echo $teacher['user_country_name']; ?></span>
                        </div>
                        <?php } ?>
                    </div>

                    <div class="rwu-teacher-details">
                        <div class="rwu-teacher-name">
                            <h1><?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h1>
                            <small><?php echo Label::getLabel('LBL_Tutor_on'); ?> <?php echo $websiteName; ?></small>
                            <?php if (!empty($teacher['offers'])) { ?>
                                <?php $this->includeTemplate('_partial/offers.php', ['offers' => $teacher['offers']], false); ?>
                            <?php } ?>
                        </div>

                        <div class="rwu-teacher-stats">
                            <div class="rwu-stat-item">
                                <div class="rwu-stat-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating' ?>"></use></svg>
                                </div>
                                <div class="rwu-stat-content">
                                    <span class="rwu-stat-value"><?php echo $teacher['testat_ratings']; ?></span>
                                    <span class="rwu-stat-label"><?php echo $teacher['testat_reviewes'] . ' ' . Label::getLabel('LBL_REVIEW(S)'); ?></span>
                                </div>
                            </div>

                            <div class="rwu-stat-item">
                                <div class="rwu-stat-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#icon-students' ?>"></use></svg>
                                </div>
                                <div class="rwu-stat-content">
                                    <span class="rwu-stat-value"><?php echo $teacher['testat_students']; ?></span>
                                    <span class="rwu-stat-label"><?php echo Label::getLabel('LBL_Students') ?></span>
                                </div>
                            </div>

                            <div class="rwu-stat-item">
                                <div class="rwu-stat-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#icon-lecture' ?>"></use></svg>
                                </div>
                                <div class="rwu-stat-content">
                                    <span class="rwu-stat-value"><?php echo $teacher['testat_lessons'] + $teacher['testat_classes']; ?></span>
                                    <span class="rwu-stat-label"><?php echo Label::getLabel('LBL_SESSIONS'); ?></span>
                                </div>
                            </div>

                            <div class="rwu-stat-item">
                                <div class="rwu-stat-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#icon-course' ?>"></use></svg>
                                </div>
                                <div class="rwu-stat-content">
                                    <span class="rwu-stat-value"><?php echo $teacher['courses']; ?></span>
                                    <span class="rwu-stat-label"><?php echo Label::getLabel('LBL_COURSES'); ?></span>
                                </div>
                            </div>
                        </div>

                        <div class="rwu-teacher-meta">
                            <div class="rwu-meta-item">
                                <span class="rwu-meta-label"><?php echo Label::getLabel('LBL_TEACHER_PRICING'); ?>:</span>
                                <span class="rwu-meta-value"><?php echo MyUtility::formatMoney($teacher['testat_minprice']); ?> - <?php echo MyUtility::formatMoney($teacher['testat_maxprice']); ?></span>
                            </div>
                            <div class="rwu-meta-item">
                                <span class="rwu-meta-label"><?php echo Label::getLabel('LBL_TEACHES'); ?>:</span>
                                <span class="rwu-meta-value"><?php echo $teacher['teacherTeachLanguageName']; ?></span>
                            </div>
                        </div>
                    </div>

                    <div class="rwu-teacher-actions">
                        <?php
                        $disabledText = 'disabled';
                        $onclick = "";
                        if ($siteUserId != $teacher['user_id']) {
                            $disabledText = '';
                            $onclick = 'onclick="toggleTeacherFavorite(' . $teacher["user_id"] . ', this)"';
                        }
                        ?>
                        <button <?php echo $onclick; ?> class="rwu-action-btn rwu-favorite-btn <?php echo $disabledText; ?> <?php echo ($teacher['uft_id']) ? 'active' : ''; ?>" <?php echo $disabledText; ?>>
                            <svg class="rwu-action-icon" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                            <?php echo Label::getLabel('LBL_FAVORITE'); ?>
                        </button>

                        <div class="rwu-share-dropdown">
                            <button class="rwu-action-btn rwu-share-btn">
                                <svg class="rwu-action-icon" viewBox="0 0 24 24">
                                    <path d="M18 16.08c-.76 0-1.44.3-1.96.77L8.91 12.7c.05-.23.09-.46.09-.7s-.04-.47-.09-.7l7.05-4.11c.54.5 1.25.81 2.04.81 1.66 0 3-1.34 3-3s-1.34-3-3-3-3 1.34-3 3c0 .24.04.47.09.7L8.04 9.81C7.5 9.31 6.79 9 6 9c-1.66 0-3 1.34-3 3s1.34 3 3 3c.79 0 1.5-.31 2.04-.81l7.12 4.16c-.05.21-.08.43-.08.65 0 1.61 1.31 2.92 2.92 2.92 1.61 0 2.92-1.31 2.92-2.92s-1.31-2.92-2.92-2.92z"/>
                                </svg>
                                <?php echo Label::getLabel('LBL_Share'); ?>
                            </button>
                            <div class="rwu-share-menu">
                                <h6><?php echo Label::getLabel('LBL_SHARE_ON'); ?></h6>
                                <div class="rwu-share-buttons">
                                    <a class='st-custom-button' data-network="facebook" title='<?php echo Label::getLabel('LBL_FACEBOOK'); ?>'>
                                        <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                    <a class='st-custom-button' data-network="twitter" title='<?php echo Label::getLabel('LBL_TWITTER'); ?>'>
                                        <svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                    <a class='st-custom-button' data-network="pinterest" title='<?php echo Label::getLabel('LBL_PINTEREST'); ?>'>
                                        <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                                    </a>
                                    <a class='st-custom-button' data-network="email" title='<?php echo Label::getLabel('LBL_EMAIL'); ?>'>
                                        <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <a href="#lessons-prices" class="rwu-action-link scroll">
                            <?php echo Label::getLabel('LBL_VIEW_LESSONS_PACKAGES'); ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Layout -->
        <div class="rwu-teacher-layout">
            <!-- Left Column - Main Content -->
            <div class="rwu-teacher-main">
                <!-- About Section -->
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_About'); ?> <?php echo $teacher['user_first_name'] . ' ' . $teacher['user_last_name']; ?></h2>
                    </div>
                    <div class="rwu-panel-body">
                        <div class="rwu-content-section">
                            <p><?php echo nl2br($teacher['user_biography']); ?></p>
                        </div>
                        <div class="rwu-content-section">
                            <h4><?php echo Label::getLabel('LBL_Speaks'); ?></h4>
                            <?php $this->includeTemplate('teachers/_partial/SpeakLanguages.php', $teacher, false); ?>
                        </div>
                    </div>
                </div>

                <!-- Lessons Prices -->
                <div class="rwu-panel-modern" id="lessons-prices">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_LESSONS_PRICES'); ?></h2>
                        <div class="rwu-language-selector">
                            <label><?php echo Label::getLabel('LBL_Select_Language'); ?></label>
                            <select name="teachLanguages" id="teachLang" class="rwu-select-modern">
                                <?php foreach ($teacher['teachLanguages'] as $langId => $langName) { ?>
                                    <option value="<?php echo $langId; ?>"><?php echo $langName; ?></option>
                                <?php } ?>
                            </select>
                        </div>
                    </div>
                    <div class="rwu-panel-body">
                        <?php $i = 1; ?>
                        <?php foreach ($teacherLangPrices as $teachLangId => $teachLangPriceSlabs) { ?>
                            <div <?php echo (($i != 1) ? "style='display:none'" : "") ?> data-lang-id="<?php echo $teachLangId; ?>" class="rwu-pricing-grid">
                                <?php foreach ($teachLangPriceSlabs as $slab => $slabDetails) { ?>
                                    <div class="rwu-pricing-card">
                                        <div class="rwu-pricing-header">
                                            <h4><?php echo $slabDetails['title']; ?></h4>
                                        </div>
                                        <div class="rwu-pricing-body">
                                            <div class="rwu-pricing-options">
                                                <?php
                                                foreach ($slabDetails['langPrices'] as $priceDetails) {
                                                    $onclick = '';
                                                    if ($siteUserId != $teacher['user_id']) {
                                                        $onclick = "cart.langSlots(" . $teacher['user_id'] . "," . $teachLangId . "," . $priceDetails['ustelgpr_slot'] . ")";
                                                    }
                                                ?>
                                                    <div class="rwu-pricing-option">
                                                        <button onclick="<?php echo $onclick; ?>" class="rwu-option-btn" <?php echo ($siteUserId == $teacher['user_id']) ? 'disabled' : ''; ?>>
                                                            <span class="rwu-option-duration"><?php echo $priceDetails['ustelgpr_slot'] . ' ' . Label::getLabel('LBL_Mins'); ?></span>
                                                            <span class="rwu-option-price"><?php echo MyUtility::formatMoney($priceDetails['ustelgpr_price']); ?></span>
                                                        </button>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php $i++; } ?>
                    </div>
                </div>

                <!-- Schedule -->
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_Schedule') ?></h2>
                    </div>
                    <div class="rwu-panel-body">
                        <div class="rwu-calendar-wrapper">
                            <div id="availbility" class="rwu-calendar"></div>
                        </div>
                        <div class="rwu-note-modern">
                            <svg class="rwu-note-icon" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm1 15h-2v-2h2v2zm0-4h-2V7h2v6z"/>
                            </svg>
                            <div class="rwu-note-content">
                                <strong><?php echo Label::getLabel('LBL_Note:') ?></strong>
                                <?php echo Label::getLabel('LBL_NOT_FINDING_YOUR_IDEAL_TIME'); ?>
                                <button class="rwu-note-link" <?php echo $contactClick; ?>><?php echo Label::getLabel('LBL_Contact'); ?></button>
                                <?php echo Label::getLabel('LBL_REQUEST_A_SLOT_OUTSIDE_OF_THEIR_CURRENT_SCHEDULE'); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Group Classes -->
                <?php if (count($classes) > 0) { ?>
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_GROUP_CLASSES'); ?></h2>
                    </div>
                    <div class="rwu-panel-body">
                        <div class="rwu-classes-grid">
                            <?php
                            foreach ($classes as $class) {
                                $classData = ['class' => $class, 'siteUserId' => $siteUserId, 'bookingBefore' => $bookingBefore, 'cardClass' => 'rwu-class-card'];
                                $this->includeTemplate('group-classes/card.php', $classData, false);
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Teaching Expertise -->
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_TEACHING_EXPERTISE'); ?></h2>
                    </div>
                    <div class="rwu-panel-body">
                        <?php
                        foreach ($preferencesType as $type => $preference) {
                            if (empty($userPreferences[$type])) {
                                continue;
                            }
                        ?>
                            <div class="rwu-expertise-section">
                                <h4><?php echo $preference; ?></h4>
                                <div class="rwu-expertise-list">
                                    <?php foreach ($userPreferences[$type] as $preference) { ?>
                                        <div class="rwu-expertise-item">
                                            <svg class="rwu-check-icon" viewBox="0 0 24 24">
                                                <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                                            </svg>
                                            <span><?php echo $preference['prefer_title']; ?></span>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Qualifications -->
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_TEACHING_QUALIFICATIONS'); ?></h2>
                    </div>
                    <div class="rwu-panel-body" id="qualificationsList">
                        <?php
                        foreach ($qualificationType as $type => $name) {
                            if (empty($userQualifications[$type])) {
                                continue;
                            }
                        ?>
                            <div class="rwu-qualification-section">
                                <h4><?php echo $name; ?></h4>
                                <div class="rwu-qualification-list">
                                    <?php foreach ($userQualifications[$type] as $qualification) { ?>
                                        <div class="rwu-qualification-item">
                                            <div class="rwu-qualification-period">
                                                <?php echo $qualification['uqualification_start_year']; ?> - <?php echo $qualification['uqualification_end_year']; ?>
                                            </div>
                                            <div class="rwu-qualification-details">
                                                <h5><?php echo $qualification['uqualification_title']; ?></h5>
                                                <p><?php echo $qualification['uqualification_institute_name']; ?></p>
                                                <p class="rwu-qualification-location"><?php echo $qualification['uqualification_institute_address']; ?></p>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>

                <!-- Reviews -->
                <?php if ($teacher['testat_reviewes'] > 0) { ?>
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_REVIEW'); ?></h2>
                    </div>
                    <?php echo $reviewFrm->getFormHtml(); ?>
                    <div class="rwu-panel-body">
                        <div class="rwu-reviews-overview">
                            <div class="rwu-rating-display">
                                <div class="rwu-rating-circle">
                                    <span class="rwu-rating-score"><?php echo FatUtility::convertToType($teacher['testat_ratings'], FatUtility::VAR_FLOAT); ?></span>
                                    <svg class="rwu-rating-stars" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                </div>
                                <div class="rwu-rating-text">
                                    <span class="rwu-rating-value"><?php echo FatUtility::convertToType($teacher['testat_ratings'], FatUtility::VAR_FLOAT); ?> <?php echo Label::getLabel('LBL_OUT_OF_5'); ?></span>
                                    <span class="rwu-rating-count"><?php echo $teacher['testat_reviewes'] . ' ' . Label::getLabel('LBL_REVIEWS'); ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="rwu-reviews-container">
                            <div class="rwu-reviews-header">
                                <p id="recordToDisplay"></p>
                                <div class="rwu-reviews-sorting">
                                    <select name="sorting" onchange="loadReviews('<?php echo $teacher['user_id']; ?>', 1)" class="rwu-select-modern">
                                        <?php $sortArr = RatingReview::getSortTypes(); ?>
                                        <?php foreach ($sortArr as $key => $value) { ?>
                                            <option value="<?php echo $key; ?>"><?php echo $value; ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                            <div id="listing-reviews" class="rwu-reviews-list"></div>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <!-- Courses -->
                <?php if ($moreCourses) { ?>
                <div class="rwu-panel-modern">
                    <div class="rwu-panel-header">
                        <h2><?php echo Label::getLabel('LBL_COURSES'); ?></h2>
                    </div>
                    <div class="rwu-panel-body">
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

            <!-- Right Column - Sidebar -->
            <div class="rwu-teacher-sidebar">
                <?php if (!empty(MyUtility::validateYoutubeUrl($teacher['user_video_link']))) { ?>
                <div class="rwu-video-modern">
                    <div class="rwu-video-container">
                        <iframe width="100%" height="200" src="<?php echo MyUtility::validateYoutubeUrl($teacher['user_video_link']); ?>" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
                    </div>
                </div>
                <?php } ?>

                <div class="rwu-booking-card">
                    <div class="rwu-booking-actions">
                        <!-- <button onclick="<?php echo str_replace('onclick="', '', str_replace('"', '', $bookNowOnClickClick)); ?>" class="rwu-book-btn <?php echo $disabledClass; ?>" <?php echo $disabledClass ? 'disabled' : ''; ?>>
                            <?php echo Label::getLabel('LBL_Book_Now'); ?>
                        </button> -->

                        <button onclick="<?php echo str_replace('onclick="', '', str_replace('"', '', $contactClick)); ?>" class="rwu-contact-btn <?php echo $disabledClass; ?>" <?php echo $disabledClass ? 'disabled' : ''; ?>>
                            <svg class="rwu-contact-icon" viewBox="0 0 24 24">
                                <path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/>
                            </svg>
                            <?php echo Label::getLabel('LBL_CONTACT'); ?>
                        </button>

                        <a href="#availbility" onclick="viewFullAvailbility()" class="rwu-availability-link scroll">
                            <?php echo Label::getLabel('LBL_VIEW_FULL_AVAILBILITY'); ?>
                        </a>

                        <?php if ($freeTrialEnabled) { ?>
                        <div class="rwu-trial-section">
                            <?php
                            $btnText = "LBL_YOU_ALREADY_HAVE_AVAILED_THE_TRIAL";
                            $onclick = "";
                            $btnClass = "rwu-trial-btn--secondary";
                            $disabledText = "disabled";
                            if (!$isFreeTrailAvailed) {
                                $disabledText = "";
                                $onclick = "onclick=\"cart.trailCalendar('" . $teacher['user_id'] . "')\"";
                                $btnClass = 'rwu-trial-btn--primary';
                                $btnText = "LBL_BOOK_FREE_TRIAL";
                            }
                            if ($siteUserId == $teacher['user_id']) {
                                $onclick = "";
                                $disabledText = "disabled";
                            }
                            ?>
                            <button <?php echo $onclick; ?> class="rwu-trial-btn <?php echo $btnClass; ?> <?php echo $disabledText; ?>" <?php echo $disabledText; ?>>
                                <?php echo Label::getLabel($btnText); ?>
                            </button>
                            <p class="rwu-trial-note"><?php echo Label::getLabel('LBL_TRIAL_LESSON_ONE_TIME'); ?></p>
                        </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
<script type="text/javascript">
    $(document).ready(function() {
        // Remove any existing tab/accordion handlers
        $('.rwu-panel-header').off('click');

        function initializePanels() {
            // Close all panels except the first one
            $('.rwu-panel-body').hide();
            $('.rwu-panel-header').removeClass('is-active');

            var $firstPanel = $('.rwu-panel-modern').first();
            $firstPanel.find('.rwu-panel-header').addClass('is-active');
            $firstPanel.find('.rwu-panel-body').show();

            // Attach accordion handler
            $('.rwu-panel-header').on('click', function(e) {
                e.preventDefault();
                e.stopImmediatePropagation();

                var $header = $(this);
                var $panel  = $header.closest('.rwu-panel-modern');
                // IMPORTANT: find body within the same panel, not only "next"
                var $body   = $panel.children('.rwu-panel-body');

                if (!$body.length) {
                    // Nothing to toggle, bail out gracefully
                    return;
                }

                if ($header.hasClass('is-active')) {
                    // Close this panel
                    $header.removeClass('is-active');
                    $body.slideUp(300);
                } else {
                    // Close all other panels
                    $('.rwu-panel-header').removeClass('is-active');
                    $('.rwu-panel-body').slideUp(300);

                    // Open this one
                    $header.addClass('is-active');
                    $body.slideDown(300, function() {
                        handlePanelOpen($header, $body);
                    });
                }
            });
        }

        function handlePanelOpen($header, $body) {
            // Schedule panel: re-render calendar when opened
            // Safer check: look for #availbility inside this body
            if ($body.find('#availbility').length && typeof window.viewOnlyCal !== 'undefined') {
                setTimeout(function() {
                    window.viewOnlyCal.render();
                }, 50);
            }

            // Reinitialize sliders if any
            if (typeof $.fn.slick !== 'undefined' && $body.find('.slider-onethird-js').length) {
                setTimeout(function() {
                    $body.find('.slider-onethird-js').slick('resize');
                }, 100);
            }
        }

        // Initialize everything
        initializePanels();
        viewCalendar(<?php echo $teacher['user_id'] . ', "paid"'; ?>);

        <?php if ($teacher['testat_reviewes'] > 0) { ?>
            loadReviews('<?php echo $teacher['user_id']; ?>', 1);
        <?php } ?>
    });
</script>



<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>
