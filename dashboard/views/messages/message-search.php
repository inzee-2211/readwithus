<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');
if ($page == 1) {
    $frm->setFormTagAttribute('onSubmit', 'sendMessage(this); return false;');
    $frm->setFormTagAttribute('class', 'form');
    $frm->developerTags['colClassPrefix'] = 'col-md-';
    $frm->developerTags['fld_default_col'] = 12;
    $messageBox = $frm->getField('message_text');
    $messageBox->addFieldTagAttribute('placeholder', Label::getLabel('LBL_TYPE_A_MESSAGE_HERE'));

    $file = $frm->getField('message_text');
    $file->setFieldTagAttribute('id', 'message_text');

    $file = $frm->getField('message_file');
    $file->setFieldTagAttribute('id', 'message_file');
    $file->setFieldTagAttribute('onchange', 'selectFile(this)');
}
$nextPage = $page + 1;
$userTimeZone = MyUtility::getSiteTimezone();
$senderImage = '';
$senderName = $otherUserDetail['user_first_name'] . ' ' . $otherUserDetail['user_last_name'];
if (!empty($userImage)) {
    $senderImage = '<img src="' . FatCache::getCachedUrl(MyUtility::makeUrl('Image', 'show', array(Afile::TYPE_USER_PROFILE_IMAGE, $otherUserDetail['user_id'], Afile::SIZE_SMALL), CONF_WEBROOT_FRONT_URL), CONF_DEF_CACHE_TIME, '.jpg') . '?t=' . time() . '" alt="' . $senderName . '" />';
}
if ($page == 1) {
    ?>
    <div class="chat-room">
        <div class="chat-room__head">
            <div class="d-flex justify-content-between">
                <div>
                    <div class="msg-list align-items-center">
                        <div class="msg-list__left">
                            <div class="avtar" data-title="<?php echo CommonHelper::getFirstChar($otherUserDetail['user_first_name']); ?>">
                                <?php echo $senderImage; ?>
                            </div>
                        </div>
                        <div class="msg-list__right">
                            <h6><?php echo $senderName; ?></h6>
                        </div>
                    </div>
                </div>
                <div>
                    <a href="javascript:void(0);" onclick='closethread();' class="close msg-close-js"></a>
                </div>
            </div>
        </div>
        <div class="chat-room__body">
            <div class="chat-list margin-top-auto">
            <?php } ?>
            <?php if ($nextPage <= $pageCount) { ?>
                <div class="load-more-js chat chat--info ">
                    <a id="loadMoreBtn" href="javascript:void(0)" onClick="getThread(<?php echo $threadId . ', ' . $nextPage; ?>);" class="loadmore box box--white" title="<?php echo Label::getLabel('LBL_Load_Previous'); ?>"><i class="fa fa-history"></i>&nbsp;<?php echo Label::getLabel('LBL_Load_Previous'); ?></a>
                </div>
            <?php } ?>
            <?php
            $date = '';
            foreach ($arrListing as $row) {
                $fromMe = ($row['message_from_user_id'] == $siteUserId);
                $msgDate = MyDate::formatDate($row['message_date']);
                $msgDateUnix = strtotime($msgDate);
                if (empty($date) || ($date != date('Ymd', $msgDateUnix))) {
                    $date = date('Ymd', $msgDateUnix);
                }
                ?>
                <div class="chat <?php echo (!$fromMe) ? 'chat--incoming' : 'chat--outgoing'; ?>" id="msgRow<?php echo $row['message_id'] ?>">
                    <?php if (!$fromMe) { ?>
                        <div class="chat__media">
                            <div class="avtar avtar--small" data-title="<?php echo CommonHelper::getFirstChar($row['message_from_name']); ?>">
                                <?php echo $senderImage; ?>
                            </div>
                        </div>
                    <?php } ?>
                    <div class="chat__content">
                        <div class="chat__message">
                            <?php
                            if (isset($attachmentsList[$row['message_id']])) {
                                echo nl2br($row['message_text']);
                                ?>
                                <div class="chat-attachment">
                                    <div class="chat-attachment__item">
                                        <div class="chat-attachment__media">
                                            <?php
                                            $attachmentData = $attachmentsList[$row['message_id']];
                                            echo Common::getFileTypeIcon($attachmentData['file_name']);
                                            ?>
                                        </div>
                                        <div class="chat-attachment__content">
        <?php echo $attachmentData['file_name'] ?>
                                        </div>
                                        <div class="chat-attachment__actions">
                                            <a target="_blank" href="<?php echo MyUtility::makeUrl('Messages', 'downloadAttachment', [$attachmentData['file_id']]); ?>" class="btn btn--small btn--transparent btn--equal color-black chat-attachment__trigger">
                                                <svg class="icon icon--download icon--small" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                    <path d="M13 10h5l-6 6-6-6h5V3h2v7zm-9 9h16v-7h2v8a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-8h2v7z"></path>
                                                </svg>
                                            </a>
                                            <?php
                                            $msgTime = new DateTime($row['message_date']);
                                            $currentTime = new DateTime();
                                            $difference = $msgTime->diff($currentTime);
                                            $minutes = $difference->format('%i');
                                            if ($fromMe && $minutes <= FatApp::getConfig('CONF_DELETE_ATTACHMENT_ALLOWED_DURATION')) {
                                                ?>
                                                <a href="javascript:void(0)" class="btn btn--small btn--transparent btn--equal color-black chat-attachment__trigger" onclick="deleteAttachment('<?php echo $row['message_id'] ?>')">
                                                    <svg class="icon icon--close icon--small" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
                                                        <path d="M12 10.586l4.95-4.95 1.414 1.414-4.95 4.95 4.95 4.95-1.414 1.414-4.95-4.95-4.95 4.95-1.414-1.414 4.95-4.95-4.95-4.95L7.05 5.636z"></path>
                                                    </svg>
                                                </a><?php
                                            }
                                            ?>
                                        </div>
                                    </div>
                                </div><?php
                            } else {
                                echo nl2br($row['message_text']);
                            }
                            ?>
                        </div>
                        <div class="chat__meta flex align-items--center font-xsmall color-light margin-top-3">
                            <span class="chat__user color-<?php echo ($fromMe) ? 'black' : 'primary' ?> bold-600 margin-right-2"><?php echo $row['message_from_name']; ?></span>
                            <time class="chat__time"><?php echo date('Y-m-d H:i', $msgDateUnix); ?></time>
                        </div>
                    </div>
                </div>
            <?php } ?>
<?php if ($page == 1) { ?>
            </div>
        </div>
            <?php if (empty($otherUserDetail['user_deleted'])) { ?>
            <div class="chat-room__footer">
        <?php echo $frm->getFormTag(); ?>
                <div class="chat-form">
                    <div class="chat-form__item">
        <?php echo $messageBox->getHTML(); ?>
                        <div id = "selectedFilesList"></div>
                    </div>
                    <div class="chat-form__actions">
                        <div class="attach-button">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" class="icon icon--attachment color-black" title="<?php echo Label::getLabel('LBL_Send_Message'); ?>">
                                <path d="M14.828 7.757l-5.656 5.657a1 1 0 1 0 1.414 1.414l5.657-5.656A3 3 0 1 0 12 4.929l-5.657 5.657a5 5 0 1 0 7.071 7.07L19.071 12l1.414 1.414-5.657 5.657a7 7 0 1 1-9.9-9.9l5.658-5.656a5 5 0 0 1 7.07 7.07L12 16.244A3 3 0 1 1 7.757 12l5.657-5.657 1.414 1.414z"/>
                            </svg>
        <?php echo $frm->getFieldHtml('message_file'); ?>
                        </div>
                        <div class="send-button">
                            <svg class="icon icon--arrow icon--small color-white" title="<?php echo Label::getLabel('LBL_SEND_MESSAGE'); ?>">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#up-arrow'; ?>"></use>
                            </svg>
                            <?php echo $frm->getFieldHtml('message_thread_id'); ?>
        <?php echo $frm->getFieldHtml('btn_submit'); ?>
                        </div>
                    </div>
                </div>
                </form>
        <?php echo $frm->getExternalJS(); ?>
                <small class="style-italic margin-top-3 d-flex">
                    <svg class="icon icon--arrow icon--small margin-right-2" title="Send Message" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24"><path d="M12 22C6.477 22 2 17.523 2 12S6.477 2 12 2s10 4.477 10 10-4.477 10-10 10zm0-2a8 8 0 1 0 0-16 8 8 0 0 0 0 16zM11 7h2v2h-2V7zm0 4h2v6h-2v-6z"></path></svg>
                    <strong class="margin-right-1"><?php echo Label::getLabel('LBL_Note:'); ?></strong> 
                    <?php
                    $sizeLabel = Label::getLabel('LBL_FILE_SIZE_SHOULD_BE_LESS_THAN_{FILE-SIZE}_MB');
                    $sizeLabel = str_replace('{file-size}', MyUtility::convertBitesToMb($fileSize), $sizeLabel);
                    $formatsLabel = Label::getLabel('LBL_SUPPORTED_FILE_FORMATS_ARE_{file-formats}');
                    $formatsLabel = str_replace('{file-formats}', implode(', ', $allowedExtensions), $formatsLabel);
                    echo $sizeLabel . ' & ' . $formatsLabel;
                    ?>
                </small>
            </div>
    <?php } ?>
    </div>
    <script>
        var page = 1;
        function updatesessionStorage(obj) {
            checked = false;
            if ($(obj).is(":checked")) {
                checked = true;
            }
            localStorage.setItem('is_enter', checked);
        }
        $(document).ready(function () {
            if (localStorage.getItem('is_enter') == true || localStorage.getItem('is_enter') == "true") {
                $('input[name=is_enter]').prop('checked', true);
            } else {
                $('input[name=is_enter]').prop('checked', false);
            }
            $('textarea[name=message_text]').keydown(function (event) {
                is_enter = localStorage.getItem('is_enter');
                if (event.keyCode == 13 && !event.shiftKey && (is_enter == "true" || is_enter == true)) {
                    $('#frm_fat_id_frmSendMessage').submit();
                }
            });
            $('.chat-room__body').scroll(function () {
                var scrollAmount = $(this).scrollTop();
                var documentHeight = $(this).height();
                if (scrollAmount == 35) {
                }
            });
        })
    </script>
<?php } ?>
<style>
    .chat-form__item {
        background: #fff;
        border : 1px solid var(--color-primary);
    }
    .chat-room__footer textarea {
        border : none;
    }
    .attachment__item {
        padding: 0 1em 1em;
        font-weight: bold;
        display: inline-block;
    }
    .attachment__item_remove {
        margin: 0 10px;
    }
</style>