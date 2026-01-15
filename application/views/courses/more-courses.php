<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="rwu-more-courses-slider">
    <div class="slider slider-onethird slider-onethird-js rwu-courses-grid">
        <?php foreach ($moreCourses as $crs) { ?>
            <!-- [ MODERN COURSE CARD ========= -->
            <div class="rwu-course-card-wrapper">
                <div class="rwu-course-card-modern">
                    <!-- Course Image -->
                    <div class="rwu-course-card__media">
                        <div class="rwu-course-image ratio ratio--16by9">
                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_COURSE_IMAGE, $crs['course_id'], 'MEDIUM', $siteLangId], CONF_WEBROOT_FRONT_URL) . '?=' . time(); ?>" alt="<?php echo $crs['course_title']; ?>">
                                <div class="rwu-course-image-overlay"></div>
                            </a>
                        </div>
                        
                        <!-- Favorite Button -->
                        <a href="javascript:void(0)" onclick="toggleCourseFavorite('<?php echo $crs['course_id'] ?>', this)" class="rwu-favorite-btn <?php echo ($crs['is_favorite'] == AppConstant::YES) ? 'is-active' : ''; ?>" data-status="<?php echo $crs['is_favorite']; ?>" tabindex="0">
                            <svg class="rwu-favorite-icon" viewBox="0 0 24 24">
                                <path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/>
                            </svg>
                        </a>

                        <!-- Certificate Badge -->
                        <?php if ($crs['course_certificate'] == AppConstant::YES) { ?>
                            <div class="rwu-certificate-badge">
                                <svg class="rwu-certificate-icon" viewBox="0 0 24 24">
                                    <path d="M12 15l-5 3 1-5.5L4 9h5.5L12 4l2.5 5H20l-4 3.5 1 5.5z"/>
                                </svg>
                                <span><?php echo Label::getLabel('LBL_CERTIFICATE'); ?></span>
                            </div>
                        <?php } ?>
                    </div>

                    <!-- Course Content -->
                    <div class="rwu-course-card__content">
                        <!-- Category -->
                        <div class="rwu-course-category">
                            <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_cate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['cate_name']); ?></a>
                            <?php if (!empty($crs['subcate_name'])) { ?>
                                <span class="rwu-category-separator">/</span>
                                <a href="<?php echo MyUtility::generateUrl('Courses', 'index') . '?catg=' . $crs['course_subcate_id'] ?>"><?php echo CommonHelper::renderHtml($crs['subcate_name']);?></a>
                            <?php } ?>
                        </div>

                        <!-- Title -->
                        <h3 class="rwu-course-title">
                            <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>">
                                <?php echo (strlen($crs['course_title']) > 70) ? CommonHelper::renderHtml(substr($crs['course_title'], 0, 70)) . '...' : CommonHelper::renderHtml($crs['course_title']); ?>
                            </a>
                        </h3>

                        <!-- Course Meta -->
                        <div class="rwu-course-meta">
                            <div class="rwu-meta-item">
                                <svg class="rwu-meta-icon" viewBox="0 0 24 24">
                                    <path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8zm.5-13H11v6l5.25 3.15.75-1.23-4.5-2.67z"/>
                                </svg>
                                <span><?php echo YouTube::convertDuration($crs['course_duration']); ?></span>
                            </div>
                            <div class="rwu-meta-item">
                                <svg class="rwu-meta-icon" viewBox="0 0 24 24">
                                    <path d="M4 6H2v14c0 1.1.9 2 2 2h14v-2H4V6zm16-4H8c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-1 9H9V9h10v2zm-4 4H9v-2h6v2zm4-8H9V5h10v2z"/>
                                </svg>
                                <span><?php echo $crs['course_lectures'] . ' ' . Label::getLabel('LBL_LECTURES'); ?></span>
                            </div>
                            <div class="rwu-meta-item">
                                <!-- <svg class="rwu-meta-icon" viewBox="0 0 24 24">
                                    <path d="M16 11c1.66 0 2.99-1.34 2.99-3S17.66 5 16 5c-1.66 0-3 1.34-3 3s1.34 3 3 3zm-8 0c1.66 0 2.99-1.34 2.99-3S9.66 5 8 5C6.34 5 5 6.34 5 8s1.34 3 3 3zm0 2c-2.33 0-7 1.17-7 3.5V19h14v-2.5c0-2.33-4.67-3.5-7-3.5zm8 0c-.29 0-.62.02-.97.05 1.16.84 1.97 1.97 1.97 3.45V19h6v-2.5c0-2.33-4.67-3.5-7-3.5z"/>
                                </svg> -->
                                <!-- <span><?php echo $crs['course_students'] . ' ' . Label::getLabel('LBL_STUDENTS'); ?></span> -->
                            </div>
                        </div>
                    </div>

                    <!-- Course Footer -->
                    <div class="rwu-course-card__footer">
                        <div class="rwu-course-pricing">
                            <!-- <?php if ($crs['course_type'] != Course::TYPE_FREE) { ?>
                                <div class="rwu-price"><?php echo CourseUtility::formatMoney($crs['course_price']); ?></div>
                            <?php } else { ?>
                                <div class="rwu-price-free"><?php echo Label::getLabel('LBL_FREE'); ?></div>
                            <?php } ?> -->
                            
                            <div class="rwu-course-rating">
                                <div class="rwu-rating-stars">
                                    <svg class="rwu-star-icon" viewBox="0 0 24 24">
                                        <path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/>
                                    </svg>
                                    <span class="rwu-rating-value"><?php echo $crs['course_ratings']; ?></span>
                                </div>
                                <span class="rwu-reviews-count">(<?php echo $crs['course_reviews'] . ' ' . Label::getLabel('LBL_REVIEWS') ?>)</span>
                            </div>
                        </div>
                        
                        <a href="<?php echo MyUtility::makeUrl('Courses', 'view', [$crs['course_slug']]); ?>" class="rwu-course-link">
                            <?php echo Label::getLabel('LBL_VIEW_COURSE'); ?>
                            <svg class="rwu-arrow-icon" viewBox="0 0 24 24">
                                <path d="M12 4l-1.41 1.41L16.17 11H4v2h12.17l-5.58 5.59L12 20l8-8z"/>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
            <!-- ] -->
        <?php } ?>
    </div>
</div>

<style>
/* ===== MODERN COURSE CARD STYLES ===== */
.rwu-more-courses-slider {
    padding: 20px 0;
}

.rwu-courses-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 24px;
    margin: 0 -12px;
}

.rwu-course-card-wrapper {
    padding: 0 12px;
}

.rwu-course-card-modern {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    border: 1px solid #f1f5f9;
    transition: all 0.3s ease;
    height: 100%;
    width: auto;
    display: flex;
    flex-direction: column;
}

.rwu-course-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    border-color: #2DADFF;
}

/* Course Media */
.rwu-course-card__media {
    position: relative;
}

.rwu-course-image {
    position: relative;
    overflow: hidden;
}

.rwu-course-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.rwu-course-card-modern:hover .rwu-course-image img {
    transform: scale(1.05);
}

.rwu-course-image-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(180deg, rgba(0,0,0,0) 0%, rgba(0,0,0,0.1) 100%);
    pointer-events: none;
}

/* Favorite Button */
.rwu-favorite-btn {
    position: absolute;
    top: 12px;
    right: 12px;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    z-index: 2;
}

.rwu-favorite-btn:hover {
    background: white;
    transform: scale(1.1);
}

.rwu-favorite-btn.is-active {
    background: #fef2f2;
}

.rwu-favorite-icon {
    width: 18px;
    height: 18px;
    fill: #94a3b8;
    transition: fill 0.3s ease;
}

.rwu-favorite-btn:hover .rwu-favorite-icon,
.rwu-favorite-btn.is-active .rwu-favorite-icon {
    fill: #dc2626;
}

/* Certificate Badge */
.rwu-certificate-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    padding: 6px 10px;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 4px;
    backdrop-filter: blur(10px);
    z-index: 2;
}

.rwu-certificate-icon {
    width: 14px;
    height: 14px;
    fill: currentColor;
}

/* Course Content */
.rwu-course-card__content {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}

.rwu-course-category {
    margin-bottom: 8px;
}

.rwu-course-category a {
    font-size: 0.75rem;
    font-weight: 500;
    color: #2DADFF;
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-course-category a:hover {
    color: #1992DF;
    text-decoration: underline;
}

.rwu-category-separator {
    color: #cbd5e1;
    margin: 0 4px;
}

.rwu-course-title {
    margin: 0 0 16px 0;
    font-size: 1.125rem;
    font-weight: 600;
    line-height: 1.4;
    color: #0f172a;
    flex: 1;
}

.rwu-course-title a {
    color: inherit;
    text-decoration: none;
    transition: color 0.2s ease;
}

.rwu-course-title a:hover {
    color: #2DADFF;
}

/* Course Meta */
.rwu-course-meta {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-top: auto;
}

.rwu-meta-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.875rem;
    color: #64748b;
}

.rwu-meta-icon {
    width: 16px;
    height: 16px;
    fill: #64748b;
}

/* Course Footer */
.rwu-course-card__footer {
    padding: 16px 20px 20px;
    border-top: 1px solid #f1f5f9;
    background: #f8fafc;
}

.rwu-course-pricing {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 12px;
}

.rwu-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: #2DADFF;
}

.rwu-price-free {
    font-size: 1.25rem;
    font-weight: 700;
    color: #10b981;
}

.rwu-course-rating {
    display: flex;
    align-items: center;
    gap: 6px;
}

.rwu-rating-stars {
    display: flex;
    align-items: center;
    gap: 4px;
}

.rwu-star-icon {
    width: 16px;
    height: 16px;
    fill: #fbbf24;
}

.rwu-rating-value {
    font-size: 0.875rem;
    font-weight: 600;
    color: #0f172a;
}

.rwu-reviews-count {
    font-size: 0.75rem;
    color: #64748b;
    padding-left: 8px;
}

.rwu-course-link {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 10px 16px;
    background: #2DADFF;
    color: white;
    text-decoration: none;
    border-radius: 8px;
    font-weight: 600;
    font-size: 0.875rem;
    transition: all 0.3s ease;
}

.rwu-course-link:hover {
    background: #1992DF;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(45, 173, 255, 0.3);
    color: white;
}

.rwu-arrow-icon {
    width: 16px;
    height: 16px;
    fill: currentColor;
    transition: transform 0.2s ease;
}

.rwu-course-link:hover .rwu-arrow-icon {
    transform: translateX(2px);
}

/* Slider Overrides */
.slider-onethird-js {
    margin: 0 -12px;
    padding: 0 12px;
}

.slider-onethird-js .slick-list {
    padding: 20px 0;
}

.slider-onethird-js .slick-slide {
    padding: 0 12px;
}

/* Responsive Design */
@media (max-width: 768px) {
    .rwu-courses-grid {
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
    }
    
    .rwu-course-card__content {
        padding: 16px;
    }
    
    .rwu-course-card__footer {
        padding: 12px 16px 16px;
    }
    
    .rwu-course-title {
        font-size: 1rem;
    }
}

@media (max-width: 480px) {
    .rwu-courses-grid {
        grid-template-columns: 1fr;
    }
    
    .rwu-course-pricing {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .rwu-course-rating {
        align-self: flex-end;
    }
}

/* Ensure slider compatibility */
.slider-onethird-js .slick-track {
    display: flex !important;
}

.slider-onethird-js .slick-slide {
    height: auto;
}

.slider-onethird-js .slick-slide > div {
    height: 100%;
}
</style>

<script>
// Initialize slider if needed
document.addEventListener('DOMContentLoaded', function() {
    // If you're using a slider library like Slick, initialize it here
    // $('.slider-onethird-js').slick({ ... });
    
    // Add any custom interactive behavior
    const courseCards = document.querySelectorAll('.rwu-course-card-modern');
    
    courseCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.zIndex = '5';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.zIndex = '1';
        });
    });
});
</script>