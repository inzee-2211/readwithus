<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$levels = Course::getCourseLevels();
?>
<title><?php echo $course['course_title']; ?></title>


<style>
/* ===== CORE COLORS (aligned to project) ===== */
:root {
    --rwu-primary: #2DADFF;
    --rwu-primary-dark: #1992DF;
    --rwu-primary-soft: #e0f2fe;
    --rwu-bg-light: #f8fafc;
    --rwu-bg-lighter: #f1f5f9;
    --rwu-border-soft: #e2e8f0;
    --rwu-text-main: #0f172a;
    --rwu-text-muted: #64748b;
}

/* ===== MODERN COURSE VIEW STYLES ===== */
.rwu-course-modern {
    background: linear-gradient(135deg, var(--rwu-bg-light) 0%, var(--rwu-bg-lighter) 100%);
    min-height: 100vh;
    padding: 32px 0 56px;
}

/* Breadcrumbs */
.rwu-breadcrumbs-modern {
    margin-bottom: 24px;
}

.rwu-breadcrumbs-modern ol {
    display: flex;
    align-items: center;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 13px;
}

.rwu-breadcrumbs-modern li {
    display: flex;
    align-items: center;
    color: var(--rwu-text-muted);
}

.rwu-breadcrumbs-modern li:not(:last-child)::after {
    content: "›";
    margin-left: 10px;
    color: #cbd5e1;
}

.rwu-breadcrumbs-modern a {
    color: var(--rwu-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-breadcrumbs-modern a:hover {
    color: var(--rwu-primary);
}

.rwu-breadcrumbs-modern .active {
    color: var(--rwu-text-main);
    font-weight: 600;
}

/* Course Header */
.rwu-course-header-modern {
    background: #ffffff;
    border-radius: 18px;
    padding: 32px 28px;
    margin-bottom: 26px;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
    border: 1px solid #f1f5f9;
}

.rwu-course-badge {
    margin-bottom: 12px;
}

.rwu-badge-category {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--rwu-bg-lighter);
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
}

.rwu-badge-category a {
    color: #475569;
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-badge-category a:hover {
    color: var(--rwu-primary);
}

.rwu-badge-separator {
    color: #cbd5e1;
}

.rwu-course-title-modern {
    font-size: 2.1rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    line-height: 1.25;
    margin-bottom: 8px;
}

.rwu-course-subtitle-modern {
    font-size: 1.05rem;
    color: var(--rwu-text-muted);
    line-height: 1.6;
    margin-bottom: 22px;
}

.rwu-course-meta-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 18px;
}

.rwu-meta-stats {
    display: flex;
    gap: 22px;
    flex-wrap: wrap;
}

.rwu-stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rwu-stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
}

.rwu-stat-icon .icon {
    width: 18px;
    height: 18px;
    fill: #ffffff;
}

.rwu-stat-content {
    display: flex;
    flex-direction: column;
}

.rwu-stat-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--rwu-text-main);
}

.rwu-stat-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

/* Provider */
.rwu-provider-modern {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--rwu-bg-light);
    padding: 10px 16px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

.rwu-provider-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.rwu-provider-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rwu-provider-info {
    display: flex;
    flex-direction: column;
}

.rwu-provider-by {
    font-size: 0.7rem;
    color: var(--rwu-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.rwu-provider-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--rwu-text-main);
}

/* Main Layout */
.rwu-course-layout-modern {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.9fr);
    gap: 26px;
    align-items: flex-start;
}

.rwu-course-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Preview Section */
.rwu-preview-modern {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.15);
}

.rwu-preview-container {
    position: relative;
}

.rwu-preview-media {
    position: relative;
    width: 100%;
    height: 360px;
    overflow: hidden;
}

.rwu-preview-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rwu-preview-overlay-modern {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(15, 23, 42, 0.1), rgba(15, 23, 42, 0.55));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.rwu-preview-media:hover .rwu-preview-overlay-modern {
    opacity: 1;
}

.rwu-preview-play-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #ffffff;
}

.rwu-play-circle {
    width: 74px;
    height: 74px;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 26px rgba(45, 173, 255, 0.55);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.rwu-preview-play-modern:hover .rwu-play-circle {
    transform: scale(1.05);
    box-shadow: 0 16px 30px rgba(45, 173, 255, 0.7);
}

.rwu-play-icon {
    width: 28px;
    height: 28px;
    fill: var(--rwu-primary);
}

.rwu-play-text {
    font-size: 0.95rem;
    font-weight: 600;
}

/* Tabs */
.rwu-tabs-modern {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.08);
}

.rwu-tabs-nav {
    display: flex;
    background: var(--rwu-bg-light);
    border-bottom: 1px solid var(--rwu-border-soft);
}

.rwu-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 18px;
    background: none;
    border: none;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--rwu-text-muted);
    cursor: pointer;
    transition: all 0.25s ease;
}

.rwu-tab-btn.active {
    background: #ffffff;
    color: var(--rwu-primary-dark);
    border-bottom: 2px solid var(--rwu-primary);
}

.rwu-tab-btn:hover:not(.active) {
    background: rgba(45, 173, 255, 0.06);
    color: var(--rwu-primary-dark);
}

.rwu-tab-icon {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.rwu-tabs-content {
    padding: 0;
}

.rwu-tab-pane {
    display: none;
    padding: 22px 24px 24px;
}

.rwu-tab-pane.active {
    display: block;
    background: #ffffff!important;
}

/* Info Cards */
.rwu-info-card {
    background: var(--rwu-bg-light);
    border-radius: 14px;
    padding: 18px 18px 16px;
    margin-bottom: 16px;
    border: 1px solid var(--rwu-border-soft);
}

.rwu-info-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--rwu-text-main);
    margin-bottom: 12px;
}

.rwu-info-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rwu-info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.9rem;
    color: var(--rwu-text-main);
}

.rwu-check-icon {
    width: 18px;
    height: 18px;
    fill: #10b981;
    flex-shrink: 0;
    margin-top: 1px;
}

.rwu-description-modern {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid var(--rwu-border-soft);
    overflow: hidden;
}

.rwu-tags-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.rwu-tag-modern {
    background: var(--rwu-primary-soft);
    color: var(--rwu-primary-dark);
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Content Tab */
.rwu-content-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 22px;
    padding: 14px 16px;
    background: var(--rwu-bg-light);
    border-radius: 14px;
}

.rwu-content-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 80px;
}

.rwu-content-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--rwu-primary-dark);
}

.rwu-content-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
    margin-top: 2px;
}

/* Sections */
.rwu-sections-modern {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.rwu-section-modern {
    background: #ffffff;
    border: 1px solid var(--rwu-border-soft);
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
}

.rwu-section-modern:hover {
    border-color: var(--rwu-primary-soft);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    transform: translateY(-1px);
}

.rwu-section-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 18px 12px;
    cursor: pointer;
}

.rwu-section-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.rwu-section-number {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--rwu-primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.rwu-section-label {
    font-size: 0.7rem;
    color: var(--rwu-text-muted);
    text-transform: uppercase;
}

.rwu-section-content {
    flex: 1;
}

.rwu-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--rwu-text-main);
    margin-bottom: 6px;
}

.rwu-section-desc {
    color: var(--rwu-text-muted);
    margin-bottom: 8px;
    line-height: 1.5;
    font-size: 0.9rem;
}

.rwu-section-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.rwu-section-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

.rwu-meta-icon {
    width: 16px;
    height: 16px;
    fill: var(--rwu-primary-dark);
}

.rwu-section-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.rwu-section-toggle:hover {
    background: var(--rwu-bg-light);
}

.rwu-toggle-icon {
    width: 20px;
    height: 20px;
    fill: var(--rwu-text-muted);
    transition: transform 0.25s ease;
}

/* Lectures */
.rwu-lectures-list {
    padding: 0 18px 14px;
}

.rwu-lecture-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 6px;
    border-radius: 10px;
    transition: background-color 0.2s ease;
}

.rwu-lecture-modern:hover {
    background: var(--rwu-bg-light);
}

.rwu-lecture-main {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.rwu-lecture-icon {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: var(--rwu-bg-lighter);
    display: flex;
    align-items: center;
    justify-content: center;
}

.rwu-lecture-play {
    width: 15px;
    height: 15px;
    fill: var(--rwu-text-muted);
}

.rwu-lecture-info {
    display: flex;
    align-items: center;
    gap: 6px;
}

.rwu-lecture-title {
    font-weight: 500;
    color: var(--rwu-text-main);
    font-size: 0.9rem;
}

.rwu-preview-badge {
    background: #dcfce7;
    color: #166534;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.rwu-lecture-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rwu-lecture-duration {
    color: var(--rwu-text-muted);
    font-size: 0.8rem;
}

.rwu-preview-btn {
    background: var(--rwu-primary);
    color: #ffffff;
    border: none;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.rwu-preview-btn:hover {
    background: var(--rwu-primary-dark);
}

.rwu-lecture-preview .rwu-lecture-play {
    fill: var(--rwu-primary);
}

/* Reviews Tab */
.rwu-reviews-modern {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.rwu-reviews-header {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 20px;
    align-items: flex-start;
}

.rwu-reviews-overview {
    display: flex;
    gap: 26px;
    align-items: center;
}

.rwu-rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.rwu-rating-circle {
    width: 74px;
    height: 74px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ffffff;
}

.rwu-rating-score {
    font-size: 1.4rem;
    font-weight: 700;
}

.rwu-rating-stars {
    width: 18px;
    height: 18px;
    fill: #ffffff;
}

.rwu-rating-text {
    text-align: center;
}

.rwu-rating-value {
    display: block;
    font-weight: 600;
    color: var(--rwu-text-main);
    font-size: 0.9rem;
}

.rwu-rating-count {
    display: block;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

.rwu-rating-bars {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rwu-rating-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.8rem;
}

.rwu-rating-star {
    color: var(--rwu-text-muted);
    width: 16px;
}

.rwu-progress-bar {
    flex: 1;
    height: 7px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
}

.rwu-progress-fill {
    height: 100%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    border-radius: inherit;
    transition: width 0.25s ease;
}

.rwu-review-cta-modern {
    background: var(--rwu-bg-light);
    padding: 16px 16px 14px;
    border-radius: 14px;
    text-align: center;
    border: 1px solid var(--rwu-border-soft);
}

.rwu-review-cta-modern p {
    margin-bottom: 8px;
    color: var(--rwu-text-muted);
    font-size: 0.9rem;
}

.rwu-rate-cta {
    background: var(--rwu-primary);
    color: #ffffff;
    border: none;
    padding: 9px 16px;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.rwu-rate-cta:hover {
    background: var(--rwu-primary-dark);
    box-shadow: 0 8px 18px rgba(45, 173, 255, 0.35);
}

/* Sidebar Pricing Card */
/* Sticky behaviour like original page */
.rwu-sidebar-sticky {
    height: auto;
    position: sticky;
    top: 100px; /* adjust to match your header height */
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}


.rwu-pricing-card-modern {
    background: #ffffff;
    border-radius: 18px;
    padding: 22px 20px 20px;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.12);
    border: 1px solid #f1f5f9;
}

.rwu-pricing-header {
    margin-bottom: 18px;
    text-align: center;
}

.rwu-pricing-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    margin: 0;
}

.rwu-features-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.rwu-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
}

.rwu-feature-item:hover {
    background: var(--rwu-bg-light);
}

.rwu-feature-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: var(--rwu-bg-lighter);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.rwu-feature-icon svg {
    width: 18px;
    height: 18px;
    fill: var(--rwu-primary-dark);
}

.rwu-feature-content {
    display: flex;
    flex-direction: column;
    font-size: 0.9rem;
}

.rwu-feature-value {
    font-weight: 600;
    color: var(--rwu-text-main);
}

.rwu-feature-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

/* Pricing Actions */
.rwu-pricing-actions {
    margin-bottom: 18px;
}

.rwu-price-display {
    text-align: center;
    margin-bottom: 16px;
    padding: 14px 12px;
    background: var(--rwu-bg-light);
    border-radius: 12px;
}

.rwu-price-label {
    display: block;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
    margin-bottom: 2px;
}

.rwu-price-amount {
    font-size: 1.7rem;
    font-weight: 700;
    color: var(--rwu-primary-dark);
}

.rwu-price-free {
    font-size: 1.7rem;
    font-weight: 700;
    color: #10b981;
}

.rwu-enroll-btn-modern {
    width: 100%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 999px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: block;
    text-align: center;
}

.rwu-enroll-btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 28px rgba(45, 173, 255, 0.45);
}

.rwu-go-to-course {
    background: linear-gradient(135deg, #10b981, #059669);
}

.rwu-go-to-course:hover {
    box-shadow: 0 10px 28px rgba(16, 185, 129, 0.45);
}

/* Action Buttons */
.rwu-action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rwu-fav-btn-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 12px;
    background: var(--rwu-bg-light);
    border: 1px solid var(--rwu-border-soft);
    border-radius: 999px;
    font-weight: 600;
    color: var(--rwu-text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.85rem;
}

.rwu-fav-btn-modern:hover {
    border-color: var(--rwu-primary);
    color: var(--rwu-primary-dark);
}

.rwu-fav-btn-modern.active {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.rwu-fav-btn-modern.active .rwu-fav-icon {
    fill: #dc2626;
}

.rwu-fav-icon {
    width: 18px;
    height: 18px;
    fill: var(--rwu-text-muted);
    transition: fill 0.2s ease;
}

/* Share block */
.rwu-share-modern {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    background: var(--rwu-bg-light);
    border: 1px solid var(--rwu-border-soft);
    border-radius: 14px;
}

.rwu-share-label {
    font-weight: 600;
    color: var(--rwu-text-muted);
    font-size: 0.85rem;
}

.rwu-share-buttons {
    display: flex;
    gap: 8px;
}

.rwu-share-buttons a {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.rwu-share-buttons a:hover {
    transform: translateY(-1px);
    border-color: var(--rwu-primary-soft);
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
}

.rwu-share-buttons svg {
    width: 15px;
    height: 15px;
    fill: var(--rwu-text-muted);
}

/* More Courses Section */
.rwu-more-courses-modern {
    background: #ffffff;
    padding: 48px 0 56px;
    margin-top: 52px;
}

.rwu-more-courses-header {
    text-align: center;
    margin-bottom: 28px;
}

.rwu-more-courses-header h2 {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    margin: 0;
}

.rwu-more-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .rwu-course-layout-modern {
        grid-template-columns: minmax(0, 1fr);
        gap: 22px;
    }
    
    .rwu-course-sidebar {
        position: static;
    }
    
    .rwu-reviews-header {
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
    }
}

@media (max-width: 768px) {
    .rwu-course-modern {
        padding: 24px 0 40px;
    }

    .rwu-course-header-modern {
        padding: 22px 18px;
        border-radius: 16px;
    }
    
    .rwu-course-title-modern {
        font-size: 1.8rem;
    }
    
    .rwu-course-subtitle-modern {
        font-size: 0.95rem;
    }
    
    .rwu-course-meta-modern {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .rwu-meta-stats {
        gap: 16px;
    }
    
    .rwu-tabs-nav {
        flex-direction: column;
    }
    
    .rwu-tab-btn {
        justify-content: flex-start;
        padding: 12px 18px;
    }
    
    .rwu-content-stats {
        flex-direction: column;
        gap: 14px;
        padding: 12px 14px;
    }
    
    .rwu-section-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .rwu-section-badge {
        flex-direction: row;
        gap: 8px;
    }
    
    .rwu-reviews-overview {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }

    .rwu-preview-media {
        height: 260px;
    }
}

@media (max-width: 480px) {
    .rwu-breadcrumbs-modern {
        margin-bottom: 18px;
    }

    .rwu-course-title-modern {
        font-size: 1.55rem;
    }

    .rwu-tab-pane {
        padding: 18px 16px 20px;
    }
    
    .rwu-pricing-card-modern {
        padding: 18px 16px;
    }
}
</style>

<!-- [ MAIN BODY ========= -->
<section class="section-view rwu-course-modern">
    <div class="container container--narrow">
        <!-- Modern Breadcrumbs -->
        <div class="rwu-breadcrumbs-modern">
            <nav aria-label="breadcrumb">
                <ol>
                    <li><a href="<?php echo MyUtility::makeUrl(); ?>"><?php echo Label::getLabel('LBL_Home'); ?></a></li>
                    <li><a href="<?php echo MyUtility::makeUrl('Courses'); ?>"><?php echo Label::getLabel('LBL_Courses'); ?></a></li>
                    <li class="active"><?php echo $course['course_title']; ?></li>
                </ol>
            </nav>
        </div>

        <div class="rwu-course-hero">
            <!-- Course Header -->
            <div class="rwu-course-header-modern">
                <div class="rwu-course-badge">
                    <span class="rwu-badge-category">
                        <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_cate_id'] ?>"><?php echo $course['cate_name']; ?></a>
                        <?php if (!empty($course['subcate_name'])): ?>
                            <span class="rwu-badge-separator">/</span>
                            <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $course['course_subcate_id'] ?>"><?php echo $course['subcate_name']; ?></a>
                        <?php endif; ?>
                    </span>
                </div>

                <h1 class="rwu-course-title-modern"><?php echo $course['course_title']; ?></h1>
                <p class="rwu-course-subtitle-modern"><?php echo $course['course_subtitle']; ?></p>

                <div class="rwu-course-meta-modern">
                    <div class="rwu-meta-stats">
                        <div class="rwu-stat-item">
                            <div class="rwu-stat-icon">
                                <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#rating"></use></svg>
                            </div>
                            <div class="rwu-stat-content">
                                <span class="rwu-stat-value"><?php echo $course['course_ratings']; ?></span>
                                <span class="rwu-stat-label"><?php echo $course['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEW(S)'); ?></span>
                            </div>
                        </div>

                        <div class="rwu-stat-item">
                            <div class="rwu-stat-icon">
                                <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-students"></use></svg>
                            </div>
                            <div class="rwu-stat-content">
                                <span class="rwu-feature-value"><?php echo (int)($course['active_subscriptions'] ?? 0); ?></span>
    <span class="rwu-feature-label">Active Students</span>
                            </div>
                        </div>

                        <div class="rwu-stat-item">
                            <div class="rwu-stat-icon">
                                <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-level"></use></svg>
                            </div>
                            <div class="rwu-stat-content">
                                <span class="rwu-stat-value"><?php echo Course::getCourseLevels($course['course_level']); ?></span>
                                <span class="rwu-stat-label"><?php echo Label::getLabel('LBL_LEVEL'); ?></span>
                            </div>
                        </div>

                        <div class="rwu-stat-item">
                            <div class="rwu-stat-icon">
                                <svg class="icon"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-globe"></use></svg>
                            </div>
                            <div class="rwu-stat-content">
                                <span class="rwu-stat-value"><?php echo $course['course_clang_name']; ?></span>
                                <span class="rwu-stat-label"><?php echo Label::getLabel('LBL_TEACHING_LANGUAGE'); ?></span>
                        </div>
                    </div>

                  
                    </div>
                </div>
            </div>

            <!-- Main Content Layout -->
            <div class="rwu-course-layout-modern">
                <!-- Left Column - Course Content -->
                <div class="rwu-course-main">
                    <!-- Course Preview -->
                    <div class="rwu-preview-modern">
                        <div class="rwu-preview-container">
                            <div class="rwu-preview-media">
                                <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $course['course_id'], 'LARGE', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="<?php echo $course['course_title']; ?>">
                                <div class="rwu-preview-overlay-modern">
                                    <a href="javascript:void(0);" onclick="showPreviewVideo('<?php echo $course['course_id']; ?>');" class="rwu-preview-play-modern">
                                        <div class="rwu-play-circle">
                                            <svg class="rwu-play-icon" viewBox="0 0 24 24">
                                                <path d="M8 5v14l11-7z"/>
                                            </svg>
                                        </div>
                                        <span class="rwu-play-text"><?php echo Label::getLabel('LBL_PREVIEW_THIS_COURSE'); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabs Navigation -->
                    <div class="rwu-tabs-modern">
                        <nav class="rwu-tabs-nav">
                            <button class="rwu-tab-btn active" data-tab="overview">
                                <svg class="rwu-tab-icon" viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-overview"></use></svg>
                                <?php echo Label::getLabel('LBL_OVERVIEW'); ?>
                            </button>
                            <button class="rwu-tab-btn" data-tab="content">
                                <svg class="rwu-tab-icon" viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-content"></use></svg>
                                <?php echo Label::getLabel('LBL_COURSE_CONTENT'); ?>
                            </button>
                            <button class="rwu-tab-btn" data-tab="reviews">
                                <svg class="rwu-tab-icon" viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-reviews"></use></svg>
                                <?php echo Label::getLabel('LBL_REVIEWS'); ?> (<?php echo $course['course_reviews'] ?>)
                            </button>
                        </nav>

                        <!-- Tab Content -->
                        <div class="rwu-tabs-content">
                            <!-- Overview Tab -->
                            <div class="rwu-tab-pane active" id="overview-tab">
                                <?php $types = IntendedLearner::getTypes(); ?>
                                
                                <?php if (isset($intendedLearners[IntendedLEarner::TYPE_LEARNING])): ?>
                                <div class="rwu-info-card">
                                    <h3 class="rwu-info-title"><?php echo $types[IntendedLEarner::TYPE_LEARNING]; ?></h3>
                                    <div class="rwu-info-list">
                                        <?php foreach ($intendedLearners[IntendedLEarner::TYPE_LEARNING] as $learner): ?>
                                        <div class="rwu-info-item">
                                            <svg class="rwu-check-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                            <span><?php echo $learner['coinle_response'] ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($intendedLearners[IntendedLEarner::TYPE_REQUIREMENTS])): ?>
                                <div class="rwu-info-card">
                                    <h3 class="rwu-info-title"><?php echo $types[IntendedLEarner::TYPE_REQUIREMENTS]; ?></h3>
                                    <div class="rwu-info-list">
                                        <?php foreach ($intendedLearners[IntendedLEarner::TYPE_REQUIREMENTS] as $learner): ?>
                                        <div class="rwu-info-item">
                                            <svg class="rwu-check-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                            <span><?php echo $learner['coinle_response'] ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <?php if (isset($intendedLearners[IntendedLEarner::TYPE_LEARNERS])): ?>
                                <div class="rwu-info-card">
                                    <h3 class="rwu-info-title"><?php echo $types[IntendedLEarner::TYPE_LEARNERS]; ?></h3>
                                    <div class="rwu-info-list">
                                        <?php foreach ($intendedLearners[IntendedLEarner::TYPE_LEARNERS] as $learner): ?>
                                        <div class="rwu-info-item">
                                            <svg class="rwu-check-icon" viewBox="0 0 24 24"><path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/></svg>
                                            <span><?php echo $learner['coinle_response'] ?></span>
                                        </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <div class="rwu-info-card">
                                    <h3 class="rwu-info-title"><?php echo Label::getLabel('LBL_DESCRIPTION'); ?></h3>
                                    <div class="rwu-description-modern">
                                        <iframe srcdoc="<?php echo $course['course_details']; ?>" style="border:none;width: 100%;height: 400px;"></iframe>
                                    </div>
                                </div>

                                <?php if (count($course['course_tags']) > 0): ?>
                                <div class="rwu-info-card">
                                    <h3 class="rwu-info-title"><?php echo Label::getLabel('LBL_COURSE_TAGS'); ?></h3>
                                    <div class="rwu-tags-modern">
                                        <?php foreach ($course['course_tags'] as $tag): ?>
                                        <span class="rwu-tag-modern"><?php echo $tag; ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Content Tab -->
                            <div class="rwu-tab-pane" id="content-tab">
                                <div class="rwu-content-stats">
                                    <div class="rwu-content-stat">
                                        <span class="rwu-content-number"><?php echo $course['course_sections']; ?></span>
                                        <span class="rwu-content-label"><?php echo Label::getLabel('LBL_SECTIONS'); ?></span>
                                    </div>
                                    <div class="rwu-content-stat">
                                        <span class="rwu-content-number"><?php echo $course['course_lectures']; ?></span>
                                        <span class="rwu-content-label"><?php echo Label::getLabel("LBL_LECTURES") ?></span>
                                    </div>
                                    <?php if ($course['course_duration'] > 0): ?>
                                    <div class="rwu-content-stat">
                                        <span class="rwu-content-number"><?php echo YouTube::convertDuration($course['course_duration']); ?></span>
                                        <span class="rwu-content-label"><?php echo Label::getLabel("LBL_TOTAL_LENGTH"); ?></span>
                                    </div>
                                    <?php endif; ?>
                                </div>

                                <?php if (count($sections) > 0): ?>
                                <div class="rwu-sections-modern">
                                    <?php $i = 1; foreach ($sections as $section): ?>
                                    <?php $lectures = ($section['lectures']) ?? []; ?>
                                    <?php if (count($lectures) > 0): ?>
                                    <div class="rwu-section-modern">
                                        <div class="rwu-section-header">
                                            <div class="rwu-section-badge">
                                                <span class="rwu-section-number"><?php echo $section['section_order']; ?></span>
                                                <span class="rwu-section-label"><?php echo Label::getLabel('LBL_SECTION'); ?></span>
                                            </div>
                                            <div class="rwu-section-content">
                                                <h4 class="rwu-section-title"><?php echo $section['section_title']; ?></h4>
                                                <p class="rwu-section-desc"><?php echo $section['section_details']; ?></p>
                                                <div class="rwu-section-meta">
                                                    <div class="rwu-section-meta-item">
                                                        <svg class="rwu-meta-icon" viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-time"></use></svg>
                                                        <span><?php echo YouTube::convertDuration($section['section_duration']); ?></span>
                                                    </div>
                                                    <div class="rwu-section-meta-item">
                                                        <svg class="rwu-meta-icon" viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL ?>images/sprite.svg#icon-lecture"></use></svg>
                                                        <span><?php echo $section['section_lectures']; ?> <?php echo Label::getLabel("LBL_LECTURES") ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                            <button class="rwu-section-toggle" data-section="<?php echo $i; ?>">
                                                <svg class="rwu-toggle-icon" viewBox="0 0 24 24"><path d="M7 10l5 5 5-5z"/></svg>
                                            </button>
                                        </div>
                                        <div class="rwu-lectures-list" id="lectures-<?php echo $i; ?>" style="display: <?php echo ($i == 1) ? 'block' : 'none'; ?>;">
                                            <?php foreach ($section['lectures'] as $lesson): ?>
                                            <?php 
                                            $showPreview = false;
                                            $rsrcId = array_search($lesson['lecture_id'], $videos);
                                            if ($rsrcId && $lesson['lecture_is_trial']) {
                                                $showPreview = true;
                                            }
                                            ?>
                                            <div class="rwu-lecture-modern <?php echo $showPreview ? 'rwu-lecture-preview' : ''; ?>">
                                                <div class="rwu-lecture-main">
                                                    <div class="rwu-lecture-icon">
                                                        <svg class="rwu-lecture-play" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                                    </div>
                                                    <div class="rwu-lecture-info">
                                                        <span class="rwu-lecture-title"><?php echo $lesson['lecture_title']; ?></span>
                                                        <?php if ($showPreview): ?>
                                                        <span class="rwu-preview-badge"><?php echo Label::getLabel('LBL_PREVIEW'); ?></span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="rwu-lecture-meta">
                                                    <span class="rwu-lecture-duration"><?php echo YouTube::convertDuration($lesson['lecture_duration'], true, true, true, false); ?></span>
                                                    <?php if ($showPreview): ?>
                                                    <button class="rwu-preview-btn" onclick="openMedia('<?php echo $rsrcId ?>');">
                                                        <?php echo Label::getLabel('LBL_PREVIEW'); ?>
                                                    </button>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php $i++; endforeach; ?>
                                </div>
                                <?php endif; ?>
                            </div>

                            <!-- Reviews Tab -->
                            <div class="rwu-tab-pane" id="reviews-tab">
                                <div class="rwu-reviews-modern">
                                    <div class="rwu-reviews-header">
                                        <div class="rwu-reviews-overview">
                                            <div class="rwu-rating-display">
                                                <div class="rwu-rating-circle">
                                                    <span class="rwu-rating-score"><?php echo $course['course_ratings']; ?></span>
                                                    <svg class="rwu-rating-stars" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                                </div>
                                                <div class="rwu-rating-text">
                                                    <span class="rwu-rating-value"><?php echo $course['course_ratings']; ?> <?php echo Label::getLabel('LBL_OUT_OF_5'); ?></span>
                                                    <span class="rwu-rating-count"><?php echo $course['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS'); ?></span>
                                                </div>
                                            </div>
                                            <div class="rwu-rating-bars">
                                                <?php foreach ($reviews as $review): ?>
                                                <div class="rwu-rating-bar">
                                                    <span class="rwu-rating-star"><?php echo $review['rating']; ?></span>
                                                    <div class="rwu-progress-bar">
                                                        <div class="rwu-progress-fill" style="width: <?php echo $review['percent']; ?>%"></div>
                                                    </div>
                                                    <span class="rwu-rating-count"><?php echo $review['count']; ?></span>
                                                </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                        <?php if ($canRate): ?>
                                        <div class="rwu-review-cta-modern">
                                            <p><?php echo Label::getLabel('LBL_HAVE_YOU_USED_THIS_COURSE?') ?></p>
                                            <button onclick="feedbackForm('<?php echo $course['ordcrs_id']; ?>')" class="rwu-rate-cta">
                                                <?php echo Label::getLabel('LBL_RATE_IT_NOW') ?>
                                            </button>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php echo $frm->getFormHtml(); ?>
                                    <div class="rwu-reviews-list" id="reviewsListingJs"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Pricing Card -->
                <div class="rwu-course-sidebar">
                     <div class="rwu-pricing-card-modern rwu-sidebar-sticky">
                        <div class="rwu-pricing-header">
                            <h3 class="rwu-pricing-title"><?php echo Label::getLabel('LBL_COURSE_FEATURES'); ?></h3>
                        </div>

                        <div class="rwu-features-list">
                            <?php if ($course['course_duration'] > 0): ?>
                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-video"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-value"><?php echo YouTube::convertDuration($course['course_duration']); ?></span>
                                    <span class="rwu-feature-label"><?php echo Label::getLabel('LBL_VIDEO_CONTENT'); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-lecture"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-value"><?php echo $course['course_lectures']; ?></span>
                                    <span class="rwu-feature-label"><?php echo Label::getLabel("LBL_LECTURES") ?></span>
                                </div>
                            </div>

                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-lecture"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-value"><?php echo $course['section_count']; ?></span>
                                   <span class="rwu-feature-label"><?php echo Label::getLabel("LBL_ASSESSMENTS") ?></span>
                                </div>
                            </div>

                            <?php if ($totalResources > 0): ?>
                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-assets"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-value"><?php echo $totalResources; ?></span>
                                    <span class="rwu-feature-label"><?php echo Label::getLabel("LBL_DOWNLOADABLE_ASSETS") ?></span>
                                </div>
                            </div>
                            <?php endif; ?>

                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-access"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-label"><?php echo Label::getLabel('LBL_FULL_LIFETIME_ACCESS'); ?></span>
                                </div>
                            </div>

                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-tv"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-label"><?php echo Label::getLabel('LBL__ACCESS_ON_MOBILE_AND_TV'); ?></span>
                                </div>
                            </div>

                            <?php if ($course['course_certificate'] == AppConstant::YES): ?>
                            <div class="rwu-feature-item">
                                <div class="rwu-feature-icon">
                                    <svg viewBox="0 0 24 24"><use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#icon-course-certificate"></use></svg>
                                </div>
                                <div class="rwu-feature-content">
                                    <span class="rwu-feature-label"><?php echo Label::getLabel('LBL_CERTIFICATE_ON_COMPLETION'); ?></span>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>

                       

      <div class="rwu-pricing-actions">
    <?php if (!empty($subscriptionMode)): ?>
        <?php
        /** @var array $subGate */
        $subGate = $subGate ?? ['access' => false, 'reason' => 'GUEST'];
        ?>

       
            <div class="rwu-price-display">
                <span class="rwu-price-label">
                    <?php echo Label::getLabel('LBL_ACCESS_VIA_SUBSCRIPTION'); ?>
                </span>
            </div>

            <?php if (!empty($subGate['access'])): ?>
                <?php
                // If controller already created ordcrsId, use it; otherwise use startByCourse
                $ordcrsId  = (int)($subscriptionOrdCrsId ?? 0);
                if ($ordcrsId > 0) {
                    $courseUrl = MyUtility::makeUrl(
                        'Tutorials',
                        'start',
                        [$ordcrsId],
                        CONF_WEBROOT_DASHBOARD
                    );
                } else {
                    $courseUrl = MyUtility::makeUrl(
                        'Tutorials',
                        'startByCourse',
                        [(int)$course['course_id']],
                        CONF_WEBROOT_DASHBOARD
                    );
                }
                ?>
                <a href="<?php echo $courseUrl; ?>" 
                   class="rwu-enroll-btn-modern rwu-go-to-course">
                    <?php echo Label::getLabel("LBL_GO_TO_COURSE"); ?>
                </a>

            <?php else: ?>

                <?php if ($subGate['reason'] === 'GUEST' || $subGate['reason'] === 'NO_ACTIVE_SUB'): ?>
                    <a href="<?php echo $subGate['pricingUrl']; ?>" 
                       class="rwu-enroll-btn-modern">
                        <?php echo Label::getLabel("LBL_SUBSCRIBE_TO_UNLOCK"); ?>
                    </a>
                <?php elseif ($subGate['reason'] === 'SUBJECT_NOT_SELECTED'): ?>
                    <a href="<?php echo $subGate['manageUrl']; ?>" 
                       class="rwu-enroll-btn-modern">
                        <?php echo Label::getLabel("LBL_SELECT_YOUR_SUBJECTS"); ?>
                    </a>
                <?php else: ?>
                    <a href="<?php echo $subGate['pricingUrl']; ?>" 
                       class="rwu-enroll-btn-modern">
                        <?php echo Label::getLabel("LBL_VIEW_PLANS"); ?>
                    </a>
                <?php endif; ?>

            <?php endif; ?>
        <?php endif; ?>

        <!-- Your legacy “buy once” flow here -->
        <?php if (empty($course['is_purchased'])): ?>
            <!-- buy/enroll buttons -->
        <?php else: ?>
          <a class="btn btn-primary"
   href="<?php echo MyUtility::makeUrl('SubscriptionTutorials','start', [ (int)$course['course_id'] ]); ?>">
  <?php echo Label::getLabel('LBL_VIEW'); ?>
</a>
               class="rwu-enroll-btn-modern rwu-go-to-course">
                <?php echo Label::getLabel("LBL_GO_TO_COURSE"); ?>
            </a>
        <?php endif; ?>

</div>



                        <div class="rwu-action-buttons">
                            <button onclick="toggleCourseFavorite('<?php echo $course['course_id'] ?>', this)" class="rwu-fav-btn-modern <?php echo ($course['is_favorite'] == AppConstant::YES) ? 'active' : ''; ?>" data-status="<?php echo $course['is_favorite']; ?>">
                                <svg class="rwu-fav-icon" viewBox="0 0 24 24">
                                    <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                                </svg>
                                <?php echo Label::getLabel("LBL_FAVORITE"); ?>
                            </button>

                            <div class="rwu-share-modern">
                                <span class="rwu-share-label"><?php echo Label::getLabel('LBL_SHARE'); ?></span>
                                <div class="rwu-share-buttons">
                                    <a class="st-custom-button" data-network="facebook" title="<?php echo Label::getLabel('LBL_FACEBOOK'); ?>">
                                        <svg viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                    </a>
                                    <a class="st-custom-button" data-network="twitter" title="<?php echo Label::getLabel('LBL_TWITTER'); ?>">
                                        <svg viewBox="0 0 24 24"><path d="M23.953 4.57a10 10 0 01-2.825.775 4.958 4.958 0 002.163-2.723c-.951.555-2.005.959-3.127 1.184a4.92 4.92 0 00-8.384 4.482C7.69 8.095 4.067 6.13 1.64 3.162a4.822 4.822 0 00-.666 2.475c0 1.71.87 3.213 2.188 4.096a4.904 4.904 0 01-2.228-.616v.06a4.923 4.923 0 003.946 4.827 4.996 4.996 0 01-2.212.085 4.936 4.936 0 004.604 3.417 9.867 9.867 0 01-6.102 2.105c-.39 0-.779-.023-1.17-.067a13.995 13.995 0 007.557 2.209c9.053 0 13.998-7.496 13.998-13.985 0-.21 0-.42-.015-.63A9.935 9.935 0 0024 4.59z"/></svg>
                                    </a>
                                    <a class="st-custom-button" data-network="pinterest" title="<?php echo Label::getLabel('LBL_PINTEREST'); ?>">
                                        <svg viewBox="0 0 24 24"><path d="M12 0C5.373 0 0 5.372 0 12c0 5.084 3.163 9.426 7.627 11.174-.105-.949-.2-2.405.042-3.441.218-.937 1.407-5.965 1.407-5.965s-.359-.719-.359-1.782c0-1.668.967-2.914 2.171-2.914 1.023 0 1.518.769 1.518 1.69 0 1.029-.655 2.568-.994 3.995-.283 1.194.599 2.169 1.777 2.169 2.133 0 3.772-2.249 3.772-5.495 0-2.873-2.064-4.882-5.012-4.882-3.414 0-5.418 2.561-5.418 5.207 0 1.031.397 2.138.893 2.738a.36.36 0 01.083.345l-.333 1.36c-.053.22-.174.267-.402.161-1.499-.698-2.436-2.889-2.436-4.649 0-3.785 2.75-7.262 7.929-7.262 4.163 0 7.398 2.967 7.398 6.931 0 4.136-2.607 7.464-6.227 7.464-1.216 0-2.359-.631-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24 12 24c6.627 0 12-5.373 12-12 0-6.628-5.373-12-12-12z"/></svg>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php if ($moreCourses): ?>
<section class="rwu-more-courses-modern">
    <div class="container container--narrow">
        <div class="rwu-more-courses-header">
            <h2><?php echo Label::getLabel('LBL_MORE_COURSES_FROM') . ' Read With Us'; ?></h2>
        </div>
        <div class="rwu-more-courses-grid">
            <?php
            echo $this->includeTemplate('courses/more-courses.php', [
                'moreCourses' => $moreCourses,
                'siteLangId' => $siteLangId,
                'siteUserId' => $siteUserId,
            ]);
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php echo $this->includeTemplate('_partial/shareThisScript.php'); ?>

<script>
// Tab functionality
document.addEventListener('DOMContentLoaded', function() {
    // Tab switching
    const tabBtns = document.querySelectorAll('.rwu-tab-btn');
    const tabPanes = document.querySelectorAll('.rwu-tab-pane');
    
    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const targetTab = this.getAttribute('data-tab');
            
            // Update active tab button
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Show target tab pane
            tabPanes.forEach(pane => {
                pane.classList.remove('active');
                if (pane.id === targetTab + '-tab') {
                    pane.classList.add('active');
                }
            });
        });
    });
    
    // Section toggle functionality
    const sectionToggles = document.querySelectorAll('.rwu-section-toggle');
    sectionToggles.forEach(toggle => {
        toggle.addEventListener('click', function() {
            const sectionId = this.getAttribute('data-section');
            const lecturesList = document.getElementById('lectures-' + sectionId);
            const icon = this.querySelector('.rwu-toggle-icon');
            
            if (lecturesList.style.display === 'none') {
                lecturesList.style.display = 'block';
                icon.style.transform = 'rotate(180deg)';
            } else {
                lecturesList.style.display = 'none';
                icon.style.transform = 'rotate(0deg)';
            }
        });
    });
    
    // Favorite button animation (UI only; backend handled by PHP/JS function)
    const favBtns = document.querySelectorAll('.rwu-fav-btn-modern');
    favBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            this.classList.toggle('active');
        });
    });
});
</script>

<style>
/* ===== CORE COLORS (aligned to project) ===== */
:root {
    --rwu-primary: #2DADFF;
    --rwu-primary-dark: #1992DF;
    --rwu-primary-soft: #e0f2fe;
    --rwu-bg-light: #f8fafc;
    --rwu-bg-lighter: #f1f5f9;
    --rwu-border-soft: #e2e8f0;
    --rwu-text-main: #0f172a;
    --rwu-text-muted: #64748b;
}

/* ===== MODERN COURSE VIEW STYLES ===== */
.rwu-course-modern {
    background: linear-gradient(135deg, var(--rwu-bg-light) 0%, var(--rwu-bg-lighter) 100%);
    min-height: 100vh;
    padding: 32px 0 56px;
}

/* Breadcrumbs */
.rwu-breadcrumbs-modern {
    margin-bottom: 24px;
}

.rwu-breadcrumbs-modern ol {
    display: flex;
    align-items: center;
    gap: 10px;
    list-style: none;
    padding: 0;
    margin: 0;
    font-size: 13px;
}

.rwu-breadcrumbs-modern li {
    display: flex;
    align-items: center;
    color: var(--rwu-text-muted);
}

.rwu-breadcrumbs-modern li:not(:last-child)::after {
    content: "›";
    margin-left: 10px;
    color: #cbd5e1;
}

.rwu-breadcrumbs-modern a {
    color: var(--rwu-text-muted);
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-breadcrumbs-modern a:hover {
    color: var(--rwu-primary);
}

.rwu-breadcrumbs-modern .active {
    color: var(--rwu-text-main);
    font-weight: 600;
}

/* Course Header */
.rwu-course-header-modern {
    background: #ffffff;
    border-radius: 18px;
    padding: 32px 28px;
    margin-bottom: 26px;
    box-shadow: 0 10px 35px rgba(15, 23, 42, 0.08);
    border: 1px solid #f1f5f9;
}

.rwu-course-badge {
    margin-bottom: 12px;
}

.rwu-badge-category {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--rwu-bg-lighter);
    padding: 7px 14px;
    border-radius: 999px;
    font-size: 13px;
    font-weight: 500;
}

.rwu-badge-category a {
    color: #475569;
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-badge-category a:hover {
    color: var(--rwu-primary);
}

.rwu-badge-separator {
    color: #cbd5e1;
}

.rwu-course-title-modern {
    font-size: 2.1rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    line-height: 1.25;
    margin-bottom: 8px;
}

.rwu-course-subtitle-modern {
    font-size: 1.05rem;
    color: var(--rwu-text-muted);
    line-height: 1.6;
    margin-bottom: 22px;
}

.rwu-course-meta-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 18px;
}

.rwu-meta-stats {
    display: flex;
    gap: 22px;
    flex-wrap: wrap;
}

.rwu-stat-item {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rwu-stat-icon {
    width: 38px;
    height: 38px;
    border-radius: 12px;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    display: flex;
    align-items: center;
    justify-content: center;
}

.rwu-stat-icon .icon {
    width: 18px;
    height: 18px;
    fill: #ffffff;
}

.rwu-stat-content {
    display: flex;
    flex-direction: column;
}

.rwu-stat-value {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--rwu-text-main);
}

.rwu-stat-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

/* Provider */
.rwu-provider-modern {
    display: flex;
    align-items: center;
    gap: 10px;
    background: var(--rwu-bg-light);
    padding: 10px 16px;
    border-radius: 14px;
    border: 1px solid #e5e7eb;
}

.rwu-provider-avatar {
    width: 44px;
    height: 44px;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(15, 23, 42, 0.15);
}

.rwu-provider-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rwu-provider-info {
    display: flex;
    flex-direction: column;
}

.rwu-provider-by {
    font-size: 0.7rem;
    color: var(--rwu-text-muted);
    text-transform: uppercase;
    letter-spacing: 0.04em;
}

.rwu-provider-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--rwu-text-main);
}

/* Main Layout */
.rwu-course-layout-modern {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(320px, 0.9fr);
    gap: 26px;
    align-items: flex-start;
}

.rwu-course-main {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Preview Section */
.rwu-preview-modern {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.15);
}

.rwu-preview-container {
    position: relative;
}

.rwu-preview-media {
    position: relative;
    width: 100%;
    height: 360px;
    overflow: hidden;
}

.rwu-preview-media img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.rwu-preview-overlay-modern {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, rgba(15, 23, 42, 0.1), rgba(15, 23, 42, 0.55));
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.25s ease;
}

.rwu-preview-media:hover .rwu-preview-overlay-modern {
    opacity: 1;
}

.rwu-preview-play-modern {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    color: #ffffff;
}

.rwu-play-circle {
    width: 74px;
    height: 74px;
    border-radius: 50%;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 12px 26px rgba(45, 173, 255, 0.55);
    transition: transform 0.25s ease, box-shadow 0.25s ease;
}

.rwu-preview-play-modern:hover .rwu-play-circle {
    transform: scale(1.05);
    box-shadow: 0 16px 30px rgba(45, 173, 255, 0.7);
}

.rwu-play-icon {
    width: 28px;
    height: 28px;
    fill: var(--rwu-primary);
}

.rwu-play-text {
    font-size: 0.95rem;
    font-weight: 600;
}

/* Tabs */
.rwu-tabs-modern {
    background: #ffffff;
    border-radius: 18px;
    overflow: hidden;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.08);
}

.rwu-tabs-nav {
    display: flex;
    background: var(--rwu-bg-light);
    border-bottom: 1px solid var(--rwu-border-soft);
}

.rwu-tab-btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 14px 18px;
    background: none;
    border: none;
    font-size: 0.95rem;
    font-weight: 600;
    color: var(--rwu-text-muted);
    cursor: pointer;
    transition: all 0.25s ease;
}

.rwu-tab-btn.active {
    background: #ffffff;
    color: var(--rwu-primary-dark);
    border-bottom: 2px solid var(--rwu-primary);
}

.rwu-tab-btn:hover:not(.active) {
    background: rgba(45, 173, 255, 0.06);
    color: var(--rwu-primary-dark);
}

.rwu-tab-icon {
    width: 18px;
    height: 18px;
    fill: currentColor;
}

.rwu-tabs-content {
    padding: 0;
}

.rwu-tab-pane {
    display: none;
    padding: 22px 24px 24px;
}

.rwu-tab-pane.active {
    display: block;
    background: #ffffff!important;
}

/* Info Cards */
.rwu-info-card {
    background: var(--rwu-bg-light);
    border-radius: 14px;
    padding: 18px 18px 16px;
    margin-bottom: 16px;
    border: 1px solid var(--rwu-border-soft);
}

.rwu-info-title {
    font-size: 1.05rem;
    font-weight: 600;
    color: var(--rwu-text-main);
    margin-bottom: 12px;
}

.rwu-info-list {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rwu-info-item {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    font-size: 0.9rem;
    color: var(--rwu-text-main);
}

.rwu-check-icon {
    width: 18px;
    height: 18px;
    fill: #10b981;
    flex-shrink: 0;
    margin-top: 1px;
}

.rwu-description-modern {
    background: #ffffff;
    border-radius: 12px;
    border: 1px solid var(--rwu-border-soft);
    overflow: hidden;
}

.rwu-tags-modern {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
}

.rwu-tag-modern {
    background: var(--rwu-primary-soft);
    color: var(--rwu-primary-dark);
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.8rem;
    font-weight: 500;
}

/* Content Tab */
.rwu-content-stats {
    display: flex;
    gap: 20px;
    margin-bottom: 22px;
    padding: 14px 16px;
    background: var(--rwu-bg-light);
    border-radius: 14px;
}

.rwu-content-stat {
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    min-width: 80px;
}

.rwu-content-number {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--rwu-primary-dark);
}

.rwu-content-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
    margin-top: 2px;
}

/* Sections */
.rwu-sections-modern {
    display: flex;
    flex-direction: column;
    gap: 14px;
}

.rwu-section-modern {
    background: #ffffff;
    border: 1px solid var(--rwu-border-soft);
    border-radius: 14px;
    overflow: hidden;
    transition: box-shadow 0.25s ease, border-color 0.25s ease, transform 0.2s ease;
}

.rwu-section-modern:hover {
    border-color: var(--rwu-primary-soft);
    box-shadow: 0 8px 24px rgba(15, 23, 42, 0.12);
    transform: translateY(-1px);
}

.rwu-section-header {
    display: flex;
    align-items: flex-start;
    gap: 14px;
    padding: 18px 18px 12px;
    cursor: pointer;
}

.rwu-section-badge {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 4px;
    flex-shrink: 0;
}

.rwu-section-number {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background: var(--rwu-primary);
    color: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 700;
    font-size: 0.95rem;
}

.rwu-section-label {
    font-size: 0.7rem;
    color: var(--rwu-text-muted);
    text-transform: uppercase;
}

.rwu-section-content {
    flex: 1;
}

.rwu-section-title {
    font-size: 1rem;
    font-weight: 600;
    color: var(--rwu-text-main);
    margin-bottom: 6px;
}

.rwu-section-desc {
    color: var(--rwu-text-muted);
    margin-bottom: 8px;
    line-height: 1.5;
    font-size: 0.9rem;
}

.rwu-section-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 12px;
}

.rwu-section-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

.rwu-meta-icon {
    width: 16px;
    height: 16px;
    fill: var(--rwu-primary-dark);
}

.rwu-section-toggle {
    background: none;
    border: none;
    cursor: pointer;
    padding: 6px;
    border-radius: 8px;
    transition: background-color 0.2s ease;
}

.rwu-section-toggle:hover {
    background: var(--rwu-bg-light);
}

.rwu-toggle-icon {
    width: 20px;
    height: 20px;
    fill: var(--rwu-text-muted);
    transition: transform 0.25s ease;
}

/* Lectures */
.rwu-lectures-list {
    padding: 0 18px 14px;
}

.rwu-lecture-modern {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 6px;
    border-radius: 10px;
    transition: background-color 0.2s ease;
}

.rwu-lecture-modern:hover {
    background: var(--rwu-bg-light);
}

.rwu-lecture-main {
    display: flex;
    align-items: center;
    gap: 10px;
    flex: 1;
}

.rwu-lecture-icon {
    width: 30px;
    height: 30px;
    border-radius: 9px;
    background: var(--rwu-bg-lighter);
    display: flex;
    align-items: center;
    justify-content: center;
}

.rwu-lecture-play {
    width: 15px;
    height: 15px;
    fill: var(--rwu-text-muted);
}

.rwu-lecture-info {
    display: flex;
    align-items: center;
    gap: 6px;
}

.rwu-lecture-title {
    font-weight: 500;
    color: var(--rwu-text-main);
    font-size: 0.9rem;
}

.rwu-preview-badge {
    background: #dcfce7;
    color: #166534;
    padding: 2px 7px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 500;
}

.rwu-lecture-meta {
    display: flex;
    align-items: center;
    gap: 10px;
}

.rwu-lecture-duration {
    color: var(--rwu-text-muted);
    font-size: 0.8rem;
}

.rwu-preview-btn {
    background: var(--rwu-primary);
    color: #ffffff;
    border: none;
    padding: 5px 11px;
    border-radius: 999px;
    font-size: 0.75rem;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.rwu-preview-btn:hover {
    background: var(--rwu-primary-dark);
}

.rwu-lecture-preview .rwu-lecture-play {
    fill: var(--rwu-primary);
}

/* Reviews Tab */
.rwu-reviews-modern {
    display: flex;
    flex-direction: column;
    gap: 22px;
}

.rwu-reviews-header {
    display: grid;
    grid-template-columns: minmax(0, 1.5fr) minmax(0, 1fr);
    gap: 20px;
    align-items: flex-start;
}

.rwu-reviews-overview {
    display: flex;
    gap: 26px;
    align-items: center;
}

.rwu-rating-display {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 6px;
}

.rwu-rating-circle {
    width: 74px;
    height: 74px;
    border-radius: 50%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    color: #ffffff;
}

.rwu-rating-score {
    font-size: 1.4rem;
    font-weight: 700;
}

.rwu-rating-stars {
    width: 18px;
    height: 18px;
    fill: #ffffff;
}

.rwu-rating-text {
    text-align: center;
}

.rwu-rating-value {
    display: block;
    font-weight: 600;
    color: var(--rwu-text-main);
    font-size: 0.9rem;
}

.rwu-rating-count {
    display: block;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

.rwu-rating-bars {
    flex: 1;
    display: flex;
    flex-direction: column;
    gap: 6px;
}

.rwu-rating-bar {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.8rem;
}

.rwu-rating-star {
    color: var(--rwu-text-muted);
    width: 16px;
}

.rwu-progress-bar {
    flex: 1;
    height: 7px;
    background: #e5e7eb;
    border-radius: 999px;
    overflow: hidden;
}

.rwu-progress-fill {
    height: 100%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    border-radius: inherit;
    transition: width 0.25s ease;
}

.rwu-review-cta-modern {
    background: var(--rwu-bg-light);
    padding: 16px 16px 14px;
    border-radius: 14px;
    text-align: center;
    border: 1px solid var(--rwu-border-soft);
}

.rwu-review-cta-modern p {
    margin-bottom: 8px;
    color: var(--rwu-text-muted);
    font-size: 0.9rem;
}

.rwu-rate-cta {
    background: var(--rwu-primary);
    color: #ffffff;
    border: none;
    padding: 9px 16px;
    border-radius: 999px;
    font-weight: 600;
    font-size: 0.85rem;
    cursor: pointer;
    transition: background-color 0.2s ease, box-shadow 0.2s ease;
}

.rwu-rate-cta:hover {
    background: var(--rwu-primary-dark);
    box-shadow: 0 8px 18px rgba(45, 173, 255, 0.35);
}

/* Sidebar Pricing Card */
/* Sticky behaviour like original page */
.rwu-sidebar-sticky {
    height: auto;
    position: sticky;
    top: 100px; /* adjust to match your header height */
    max-height: calc(100vh - 120px);
    overflow-y: auto;
}


.rwu-pricing-card-modern {
    background: #ffffff;
    border-radius: 18px;
    padding: 22px 20px 20px;
    box-shadow: 0 10px 32px rgba(15, 23, 42, 0.12);
    border: 1px solid #f1f5f9;
}

.rwu-pricing-header {
    margin-bottom: 18px;
    text-align: center;
}

.rwu-pricing-title {
    font-size: 1.2rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    margin: 0;
}

.rwu-features-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-bottom: 20px;
}

.rwu-feature-item {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 10px;
    border-radius: 12px;
    transition: background-color 0.2s ease;
}

.rwu-feature-item:hover {
    background: var(--rwu-bg-light);
}

.rwu-feature-icon {
    width: 36px;
    height: 36px;
    border-radius: 12px;
    background: var(--rwu-bg-lighter);
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.rwu-feature-icon svg {
    width: 18px;
    height: 18px;
    fill: var(--rwu-primary-dark);
}

.rwu-feature-content {
    display: flex;
    flex-direction: column;
    font-size: 0.9rem;
}

.rwu-feature-value {
    font-weight: 600;
    color: var(--rwu-text-main);
}

.rwu-feature-label {
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
}

/* Pricing Actions */
.rwu-pricing-actions {
    margin-bottom: 18px;
}

.rwu-price-display {
    text-align: center;
    margin-bottom: 16px;
    padding: 14px 12px;
    background: var(--rwu-bg-light);
    border-radius: 12px;
}

.rwu-price-label {
    display: block;
    font-size: 0.8rem;
    color: var(--rwu-text-muted);
    margin-bottom: 2px;
}

.rwu-price-amount {
    font-size: 1.7rem;
    font-weight: 700;
    color: var(--rwu-primary-dark);
}

.rwu-price-free {
    font-size: 1.7rem;
    font-weight: 700;
    color: #10b981;
}

.rwu-enroll-btn-modern {
    width: 100%;
    background: linear-gradient(135deg, var(--rwu-primary), var(--rwu-primary-dark));
    color: #ffffff;
    border: none;
    padding: 12px 20px;
    border-radius: 999px;
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.25s ease;
    text-decoration: none;
    display: block;
    text-align: center;
}

.rwu-enroll-btn-modern:hover {
    transform: translateY(-1px);
    box-shadow: 0 10px 28px rgba(45, 173, 255, 0.45);
}

.rwu-go-to-course {
    background: linear-gradient(135deg, #10b981, #059669);
}

.rwu-go-to-course:hover {
    box-shadow: 0 10px 28px rgba(16, 185, 129, 0.45);
}

/* Action Buttons */
.rwu-action-buttons {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.rwu-fav-btn-modern {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 7px;
    padding: 10px 12px;
    background: var(--rwu-bg-light);
    border: 1px solid var(--rwu-border-soft);
    border-radius: 999px;
    font-weight: 600;
    color: var(--rwu-text-muted);
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.85rem;
}

.rwu-fav-btn-modern:hover {
    border-color: var(--rwu-primary);
    color: var(--rwu-primary-dark);
}

.rwu-fav-btn-modern.active {
    background: #fef2f2;
    border-color: #fecaca;
    color: #dc2626;
}

.rwu-fav-btn-modern.active .rwu-fav-icon {
    fill: #dc2626;
}

.rwu-fav-icon {
    width: 18px;
    height: 18px;
    fill: var(--rwu-text-muted);
    transition: fill 0.2s ease;
}

/* Share block */
.rwu-share-modern {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 10px 12px;
    background: var(--rwu-bg-light);
    border: 1px solid var(--rwu-border-soft);
    border-radius: 14px;
}

.rwu-share-label {
    font-weight: 600;
    color: var(--rwu-text-muted);
    font-size: 0.85rem;
}

.rwu-share-buttons {
    display: flex;
    gap: 8px;
}

.rwu-share-buttons a {
    width: 30px;
    height: 30px;
    border-radius: 10px;
    background: #ffffff;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    border: 1px solid transparent;
}

.rwu-share-buttons a:hover {
    transform: translateY(-1px);
    border-color: var(--rwu-primary-soft);
    box-shadow: 0 4px 10px rgba(15, 23, 42, 0.12);
}

.rwu-share-buttons svg {
    width: 15px;
    height: 15px;
    fill: var(--rwu-text-muted);
}

/* More Courses Section */
.rwu-more-courses-modern {
    background: #ffffff;
    padding: 48px 0 56px;
    margin-top: 52px;
}

.rwu-more-courses-header {
    text-align: center;
    margin-bottom: 28px;
}

.rwu-more-courses-header h2 {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--rwu-text-main);
    margin: 0;
}

.rwu-more-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: 20px;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .rwu-course-layout-modern {
        grid-template-columns: minmax(0, 1fr);
        gap: 22px;
    }
    
    .rwu-course-sidebar {
        position: static;
    }
    
    .rwu-reviews-header {
        grid-template-columns: minmax(0, 1fr);
        gap: 18px;
    }
}

@media (max-width: 768px) {
    .rwu-course-modern {
        padding: 24px 0 40px;
    }

    .rwu-course-header-modern {
        padding: 22px 18px;
        border-radius: 16px;
    }
    
    .rwu-course-title-modern {
        font-size: 1.8rem;
    }
    
    .rwu-course-subtitle-modern {
        font-size: 0.95rem;
    }
    
    .rwu-course-meta-modern {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .rwu-meta-stats {
        gap: 16px;
    }
    
    .rwu-tabs-nav {
        flex-direction: column;
    }
    
    .rwu-tab-btn {
        justify-content: flex-start;
        padding: 12px 18px;
    }
    
    .rwu-content-stats {
        flex-direction: column;
        gap: 14px;
        padding: 12px 14px;
    }
    
    .rwu-section-header {
        flex-direction: column;
        gap: 10px;
    }
    
    .rwu-section-badge {
        flex-direction: row;
        gap: 8px;
    }
    
    .rwu-reviews-overview {
        flex-direction: column;
        gap: 16px;
        text-align: center;
    }

    .rwu-preview-media {
        height: 260px;
    }
}

@media (max-width: 480px) {
    .rwu-breadcrumbs-modern {
        margin-bottom: 18px;
    }

    .rwu-course-title-modern {
        font-size: 1.55rem;
    }

    .rwu-tab-pane {
        padding: 18px 16px 20px;
    }
    
    .rwu-pricing-card-modern {
        padding: 18px 16px;
    }
}
</style>
