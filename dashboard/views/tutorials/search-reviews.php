<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="reviews-list reviewSrchListJs">
    <!-- [ REVIEWS ========= -->
    <?php
    $records = 0;
    if ($reviews) {
        foreach ($reviews as $review) { ?>
            <div class="review">
                <div class="review__media">
                    <div class="avtar" data-title="<?php echo strtoupper($review['user_first_name'][0]); ?>">
                        <img src="<?php echo MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $review['ratrev_user_id'], Afile::SIZE_SMALL], CONF_WEBROOT_FRONTEND); ?>" alt="<?php echo $review['user_first_name'] . ' ' . $review['user_last_name']; ?>">
                    </div>
                </div>
                <div class="review__content">
                    <span class="review__author">
                        <?php echo $review['user_first_name'] . ' ' . $review['user_last_name']; ?>
                    </span>
                    <div class="review__meta">
                        <div class="review__rating">
                            <div class="rating">
                                <svg class="rating__media">
                                    <use xlink:href="<?php echo CONF_WEBROOT_DASHBOARD ?>images/sprite.svg#rating"></use>
                                </svg>
                                <span class="rating__value">
                                    <?php echo $review['ratrev_overall']; ?>
                                </span>
                            </div>
                        </div>
                        <div class="review__date">
                            <?php echo FatDate::format($review['ratrev_created']); ?>
                        </div>
                    </div>
                    <h6 class="review__title margin-top-6">
                        <?php echo $review['ratrev_title']; ?>
                    </h6>
                    <div class="review__message">
                        <p><?php echo $review['ratrev_detail'] ?></p>
                    </div>
                </div>
            </div><?php
                }
                $records = count($reviews);
            } else {
                $this->includeTemplate('_partial/no-record-found.php', ['msgHeading' => Label::getLabel('LBL_NO_REVIEWS_POSTED_YET')]);
            }
                    ?>
    <!-- ] -->
</div>
<div class="pagination pagination--centered margin-top-10 reviewSrchListJs">
    <?php
    $pagingArr = [
        'page' => $post['pageno'],
        'pageSize' => $pagesize,
        'pageCount' => $pageCount,
        'callBackJsFunc' => 'goToReviewsSearchPage'
    ];

    $this->includeTemplate('_partial/pagination.php', $pagingArr, false);
    echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmReviewsPaging']);
    ?>
</div>
<?php
$pagingLbl = Label::getLabel('LBL_DISPLAYING_REVIEWS_{start-count}_TO_{end-count}_OF_{total}');
$start = ($recordCount > 0) ? (($post['pageno'] - 1) * $pagesize + 1) : 0;
$end = ($recordCount < $start + $pagesize - 1) ? $recordCount : $start + $pagesize - 1;
$pagingLbl = str_replace(
    ['{start-count}', '{end-count}', '{total}'],
    [$start, $end, $recordCount],
    $pagingLbl
);
?>
<script type="text/javascript">
    $(document).ready(function() {
        $('.pagingLblJs').text("<?php echo $pagingLbl ?>");
    });
</script>