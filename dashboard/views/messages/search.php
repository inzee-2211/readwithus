<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if (empty($arr_listing)) {
    $this->includeTemplate('_partial/no-record-found.php');
} else {
    foreach ($arr_listing as $sn => $row) {
        $imgUserId = $row['message_from_user_id'];
        $imgUserName = $row['message_from_name'];
        if ($row['message_from_user_id'] == $siteUserId) {
            $imgUserId = $row['message_to_user_id'];
            $imgUserName = $row['message_to_name'];
        }
        $liClass = 'is-read';
        if ($row['message_is_unread'] == Thread::MESSAGE_IS_UNREAD && $row['message_to'] == $siteUserId) {
            $liClass = '';
        }
        ?>
        <div class="msg-list <?php echo 'msg-list-' . $row['thread_id']; ?> <?php echo $liClass; ?>">
            <div class="msg-list__left">
                <div class="avtar avtar--centered" data-title="<?php echo CommonHelper::getFirstChar($imgUserName); ?>">
                    <?php
                    if (!empty(['file_id'])) {
                        echo '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $imgUserId, Afile::SIZE_SMALL], CONF_WEBROOT_FRONT_URL),CONF_DEF_CACHE_TIME, '.jpg') . '" alt="' . $imgUserName . '" />';
                    }
                    ?>
                </div>
            </div>
            <div class="msg-list__right">
                <h6><?php echo $imgUserName; ?></h6>
                <p><?php echo CommonHelper::truncateCharacters($row['message_text'], 280); ?></p>
                <date><?php echo MyDate::formatDate($row['message_date'], 'Y-m-d'); ?></date>
            </div>
            <a href="javascript:void(0);" onclick="getThread(<?php echo $row['thread_id']; ?>, 1);" class="msg-list__action msg-list__action-js"></a>
        </div>
        <?php
    }
}
