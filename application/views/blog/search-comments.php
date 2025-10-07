<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($blogPostComments) && $page != 1) {
    return;
}
?>
<?php if (empty($blogPostComments)) { ?>
    <div class="comment box box--white box--space">
        <?php echo Label::getLabel('MSG_NO_COMMENTS_ON_THIS_BLOG_POST'); ?>
    </div>
<?php
    return;
}
?>

<?php
foreach ($blogPostComments as $comment) {
?>
    <div class="comments-list">
        <div class="avtar avtar--small avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($comment['bpcomment_author_name']); ?>">
            <?php echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $comment['bpcomment_user_id'], Afile::SIZE_SMALL]), CONF_DEF_CACHE_TIME, '.jpg') . '"  alt="' . $comment['bpcomment_author_name'] . '"/>'; ?>
        </div>
        <span class="date"><?php echo MyDate::formatDate($comment['bpcomment_added_on']); ?></span>
        <h5><strong><?php echo ucfirst($comment['bpcomment_author_name']); ?></strong></h5>
        <div class="comment__desc"><?php echo nl2br($comment['bpcomment_content']); ?></div>
    </div>
<?php } ?>
<?php
$nextPage = $page + 1;
if ($nextPage <= $pageCount) {
?>
    <div class="text-center">
        <a id="loadMoreBtn" href="javascript:void(0);" onClick="searchComments(<?php echo $blogId ?>, <?php echo $nextPage; ?>);" class="loadmore btn btn--medium"><?php echo Label::getLabel('LBL_LOAD_PREVIOUS'); ?></a>
    </div>
<?php } ?>