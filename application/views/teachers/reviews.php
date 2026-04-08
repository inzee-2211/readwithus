<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php foreach ($reviews as $review) { ?>
    <div class="row">
        <div class="col-xl-4 col-lg-4 col-sm-4">
            <div class="review-profile">
                <div class="avatar avatar-md" data-title="<?php echo $review['user_first_name']; ?>">
                    <img src="<?php echo MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $review['ratrev_user_id'], Afile::SIZE_SMALL]); ?>" alt="<?php echo $review['user_first_name']; ?>" />
                </div>
                <div class="user-info"><b><?php echo $review['user_first_name'] . ' ' . $review['user_last_name']; ?></b>
                    <p><?php MyDate::formatDate($review['ratrev_created']); ?></p>
                </div>
            </div>
        </div>
        <div class="col-xl-8 col-lg-8 col-sm-8">
            <div class="review-content">
                <div class="review-content__head">
                    <h6><?php echo $review['ratrev_title']; ?></span></h6>
                    
                        <div class="ratings">
                            <svg class="icon icon--rating margin-right-2"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#rating'; ?>"></use></svg>
                            <span class="value"><?php echo FatUtility::convertToType($review['ratrev_overall'], FatUtility::VAR_FLOAT); ?></span>
                        </div>
                    
                </div>
                <div class="review-content__body">
                    <p><?php echo $review['ratrev_detail']; ?></p>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<?php if ($postedData['pageno'] < $pageCount) { ?>
    <?php $nextPage = $postedData['pageno'] + 1; ?>
    <div class="reviews-wrapper__foot show-more-container">
        <div class="show-more">
            <a href="javascript:void(0);" class="btn btn--show" onclick="loadReviews(<?php echo $postedData['teacher_id']; ?>,<?php echo $nextPage; ?>)"><?php echo Label::getLabel('Lbl_SHOW_MORE'); ?></a>
        </div>
    </div>
<?php } ?>