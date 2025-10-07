<?php

/**
 * This class is used to handle Thread
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Thread extends MyAppModel
{

    const DB_TBL = 'tbl_threads';
    const DB_TBL_PREFIX = 'thread_';
    const DB_TBL_THREAD_MESSAGES = 'tbl_thread_messages';
    const DB_TBL_THREAD_USERS = 'tbl_thread_users';
    const MESSAGE_IS_READ = 0;
    const MESSAGE_IS_UNREAD = 1;

    private $userId = 0;

    /**
     * Initialize Thread
     * 
     * @param int $id
     * @param int $userId
     */
    public function __construct(int $id = 0, int $userId = 0)
    {
        $this->userId = $userId;
        parent::__construct(static::DB_TBL, 'thread_id', $id);
    }

    /**
     * Add Thread Messages
     * 
     * @param array $data
     * @return bool|int
     */
    public function addThreadMessages(array $data)
    {
        if (empty($data)) {
            return false;
        }
        if (!FatApp::getDb()->insertFromArray(Thread::DB_TBL_THREAD_MESSAGES, $data)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return FatApp::getDb()->getInsertId();
    }

    /**
     * Add Thread Messages
     * 
     * @param int $threadId
     * @param int $userId
     * @return bool
     */
    public function markUserMessageRead(int $threadId, int $userId): bool
    {
        if (FatApp::getDb()->updateFromArray('tbl_thread_messages', ['message_is_unread' => self::MESSAGE_IS_READ],
                        ['smt' => '`message_thread_id`=? AND `message_to`=? ', 'vals' => [$threadId, $userId]])) {
            return true;
        }
        $this->error = FatApp::getDb()->getError();
        return false;
    }

    /**
     * Get Thread Id
     * 
     * @param array $userArr
     * @return bool|int
     */
    public function getThreadId(array $userArr)
    {
        $srch = new SearchBase(static::DB_TBL_THREAD_USERS);
        $srch->addCondition('threaduser_id', 'IN', $userArr);
        $srch->addHaving('mysql_func_count(distinct threaduser_id)', '>', 1, 'AND', true);
        $srch->addMultipleFields(['threaduser_thread_id']);
        $srch->addGroupBy('threaduser_thread_id');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $res = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($res['threaduser_thread_id'])) {
            return self::isThreadExist($res['threaduser_thread_id']);
        }
        return $this->createThread($userArr);
    }

    /**
     * Create Thread
     * 
     * @param array $data
     * @return bool|int
     */
    public function createThread(array $data)
    {
        if (empty($data)) {
            return false;
        }
        $db = FatApp::getDb();
        $db->startTransaction();
        $threadObj = new Thread();
        $threadDataToSave = ['thread_start_date' => date('Y-m-d H:i:s')];
        $threadObj->assignValues($threadDataToSave);
        if (!$threadObj->save()) {
            $this->error = $threadObj->getError();
            return false;
        }
        foreach ($data as $id) {
            $threadUserArr = [];
            $threadUserArr['threaduser_id'] = $id;
            $threadUserArr['threaduser_thread_id'] = $threadObj->mainTableRecordId;
            if (!$db->insertFromArray(Thread::DB_TBL_THREAD_USERS, $threadUserArr)) {
                $this->error = $db->getError();
                return false;
            }
        }
        $db->commitTransaction();
        return $threadObj->mainTableRecordId;
    }

    /**
     * Thread Exist
     * 
     * @param int $threadId
     * @return type
     */
    public static function isThreadExist(int $threadId)
    {
        $srch = new SearchBase(static::DB_TBL, 't');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $srch->addCondition('thread_id', '=', $threadId);
        $res = FatApp::getDb()->fetch($srch->getResultSet());
        return $res['thread_id'] ?? 0;
    }

    /**
     * Get Threads
     * 
     * @param int $userId
     * @return MessageSearch
     */
    public static function getThreads(int $userId)
    {
        $srch = new MessageSearch();
        $srch->joinLatestThreadMessage();
        $srch->joinMessagePostedFromUser();
        $srch->joinMessagePostedToUser();
        $srch->addMultipleFields(['tth.*', 'ttm.message_id', 'ttm.message_text', 'ttm.message_date',
            '(CASE WHEN tfr.user_id = ' . $userId . ' THEN 0 ELSE ttm.message_is_unread END) AS message_is_unread', 'ttm.message_to',]);
        $srch->addCondition('ttm.message_deleted', '=', 0);
        $cnd = $srch->addCondition('ttm.message_from', '=', $userId);
        $cnd->attachCondition('ttm.message_to', '=', $userId, 'OR');
        $srch->addOrder('ttm.message_date', 'Desc');
        return $srch;
    }

    /**
     * Get Thread Users
     * 
     * @param int $threadId
     * @return array
     */
    public static function getThreadUsers(int $threadId)
    {
        $srch = new SearchBase(static::DB_TBL_THREAD_USERS);
        $srch->addCondition('threaduser_thread_id', '=', $threadId);
        $srch->addMultipleFields(['threaduser_id']);
        $res = FatApp::getDb()->fetchAll($srch->getResultSet(), 'threaduser_id');
        return array_keys($res);
    }

    /**
     * Get Unread Count
     * 
     * @param int $threadId
     * @return int
     * 
     */
    public function getUnreadCount(int $threadId = 0): int
    {
        $srch = new MessageSearch();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(100);
        $srch->joinTable(static::DB_TBL_THREAD_USERS, 'INNER JOIN', 'threaduser.threaduser_thread_id =  tth.thread_id', 'threaduser');
        $srch->joinTable(static::DB_TBL_THREAD_MESSAGES, 'INNER JOIN', 'message.message_thread_id =  tth.thread_id', 'message');
        $srch->addCondition('message.message_to', '=', $this->userId);
        $srch->addCondition('threaduser.threaduser_id', '=', $this->userId);
        $srch->addCondition('message.message_is_unread', '=', AppConstant::YES);
        if ($threadId > 0) {
            $srch->addCondition('tth.thread_id', '=', $threadId);
        }
        $srch->addFld('count(*) unread_count');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        return FatUtility::int($row['unread_count'] ?? 0);
    }

    /**
     * Function to send email notifications to users for their unread msgs
     * Unread duration can be configured in admin panel
     *
     * @return bool
     * @todo This method required Redo, Too much tasks in a single method
     */
    public function sendUnreadMsgsNotifications()
    {
        if (FatApp::getConfig('CONF_ENABLE_UNREAD_MSG_NOTIFICATION') == AppConstant::NO) {
            return 'Module is disabled';
        }

        /* get all thread ids having unread msgs more than the specified settings time */
        $srch = new MessageSearch();
        $srch->joinThreadMessage();
        $srch->addCondition('message_is_unread', '=', AppConstant::YES);
        $srch->addCondition('message_deleted', '=', AppConstant::NO);
        $srch->addCondition('message_email_sent', '=', AppConstant::NO);
        $srch->addCondition('message_date', '<=', date('Y-m-d H:i:s', strtotime('-' . FatApp::getConfig('CONF_UNREAD_MSG_NOTIFICATION_DURATION') . ' minutes')));
        $srch->addFld('message_thread_id');
        $srch->addGroupBy('message_to');
        $srch->addGroupBy('message_thread_id');
        $threadsList = FatApp::getDb()->fetchAll($srch->getResultSet(), 'message_thread_id');
        if (!$threadsList) {
            return 'No threads with unread msgs';
        }
        /* get count of all unread msgs and last msg id to show last msg */
        $subQry = new SearchBase(static::DB_TBL_THREAD_MESSAGES);
        $subQry->addMultipleFields(
                [
                    'MAX(message_id) as message_id',
                    'COUNT(message_id) as total_unread_messages',
                    'GROUP_CONCAT(message_id) as unread_msgs_ids'
                ]
        );
        $subQry->addDirectCondition('message_thread_id IN (' . implode(',', array_keys($threadsList)) . ')');
        $subQry->addCondition('message_is_unread', '=', AppConstant::YES);
        $subQry->addCondition('message_deleted', '=', AppConstant::NO);
        $subQry->addCondition('message_email_sent', '=', AppConstant::NO);
        $subQry->addGroupBy('message_to');
        $subQry->addGroupBy('message_thread_id');
        $msgsData = FatApp::getDb()->fetchAll($subQry->getResultSet(), 'message_id');
        if (!$msgsData) {
            return 'No unread msgs';
        }

        /* get unread msgs and threads details for the filtered results with previous queries */
        $srch1 = new MessageSearch();
        $srch1->joinThreadMessage();
        $srch1->joinMessagePostedToUser();
        $srch1->joinMessagePostedFromUser();
        $srch1->addMultipleFields(['message_thread_id', 'message_id', 'message_text']);
        $srch1->addDirectCondition('message_id IN (' . implode(',', array_keys($msgsData)) . ')');
        $threadsData = FatApp::getDb()->fetchAll($srch1->getResultSet(), 'message_id');
        if (!$threadsData) {
            return 'No unread msgs';
        }

        /* get message ids */
        $messageIds = array_keys($threadsData);
        /* get attachments for messages */
        $fileSrch = new SearchBase(Afile::DB_TBL, 'af');
        $fileSrch->addMultipleFields(['file_record_id']);
        $fileSrch->addCondition('file_type', '=', Afile::TYPE_MESSAGE_ATTACHMENT);
        $fileSrch->addDirectCondition('file_record_id IN (' . implode(',', $messageIds) . ')');
        $fileSrch->doNotCalculateRecords();
        $fileSrch->doNotLimitRecords();
        $attachmentsList = FatApp::getDb()->fetchAll($fileSrch->getResultSet(), 'file_record_id');

        /* format data according to user ids */
        $userThreadsList = [];
        foreach ($threadsData as $thread) {
            $thread['total_unread_messages'] = $msgsData[$thread['message_id']]['total_unread_messages'];
            $userThreadsList[$thread['message_to_user_id']][$thread['message_id']] = $thread;
        }

        /* prepare email content and send notification */
        $mail = new FatMailer(MyUtility::getSiteLangId(), 'unread_messages_email');
        $totalEmails = 100;

        foreach ($userThreadsList as $userThread) {
            $threadsContent = '<table style="border:1px solid #ddd;" cellspacing="0" cellpadding="0" border="0"><tbody>';
            $totalUnreadCount = 0;
            $unreadMsgsIds = [];
            foreach ($userThread as $thread) {
                $unreadMsgsIds = array_merge($unreadMsgsIds, explode(',', $msgsData[$thread['message_id']]['unread_msgs_ids']));
                $threadsContent .= '<tr>';
                $threadsContent .= '<td style="padding:10px;font-size:13px; color:#333; border-bottom: 1px solid #ddd;" width="50">
                    <img src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $thread['message_from_user_id'], 'SIZE_SMALL'], CONF_WEBROOT_FRONTEND) . '" style="border-radius: 25px; width:50px;">
                </td>';

                $msg = '';
                if (isset($attachmentsList[$thread['message_id']])) {
                    if (!empty($thread['message_text'])) {
                        $msg = (strlen($thread['message_text']) > 40) ? substr($thread['message_text'], 0, 40) . '...' : $thread['message_text'] . '<br>';
                    }
                    $msg .= '<svg style="width: 16px;float: left;height: 16px;padding: 6px 0 0 0;" class="icon icon--arrow icon--small color-white"">
                        <use xlink:href="' . CONF_WEBROOT_URL . 'images/sprite.svg#attach"></use>
                        </svg><span style="float: left;width: 100px;padding: 6px;">' . Label::getLabel('LBL_Attachment') . '<span></span></span>';
                } else {
                    $msg = (strlen($thread['message_text']) > 60) ? substr($thread['message_text'], 0, 60) . '...' : $thread['message_text'];
                }

                $threadsContent .= '<td style="padding:10px;font-size:13px; color:#333; border-bottom: 1px solid #ddd;" width="153"><b>' . ucfirst($thread['message_from_name']) . '</b><br>' . $msg . '</td>';

                $label = Label::getLabel('LBL_{msg-count}_Message(s)');
                $count = ($thread['total_unread_messages'] > 99) ? '99+' : $thread['total_unread_messages'];
                $threadsContent .= '<td style="padding:10px;font-size:13px; color:#333; border-bottom: 1px solid #ddd;" width="153">' . str_replace('{msg-count}', $count, $label) . '</td>';

                $threadsContent .= '<td style="padding:10px;font-size:13px; color:#333; border-bottom: 1px solid #ddd;" width="153">';
                $threadsContent .= '<a target="_blank" href="' . MyUtility::makeFullUrl('Messages', 'index', [$thread['message_thread_id']], CONF_WEBROOT_DASHBOARD) . '" style="background:{secondary-color}; color:{secondary-inverse-color}; text-decoration:none;font-size:16px; font-weight:500;padding:10px 30px;display:inline-block;border-radius:3px;">' . Label::getLabel('LBL_View') . '</a>';
                $threadsContent .= '</td>';
                $threadsContent .= '</tr>';

                $totalUnreadCount += $thread['total_unread_messages'];
            }
            $threadsContent .= '</tbody></table>';

            $vars = [
                '{user_full_name}' => ucwords($thread['message_to_name']),
                '{unread_messages_count}' => ($totalUnreadCount > 99) ? '99+' : $totalUnreadCount,
                '{messages_detail}' => $threadsContent,
            ];

            $mail->setVariables($vars);
            if ($mail->sendMail([$thread['message_to_email']])) {
                /* update the email sent status for message */
                $data = ['message_email_sent' => AppConstant::YES];
                $valCount = trim(str_repeat("?,", count($unreadMsgsIds)), ',');
                $where = ['smt' => 'message_id IN (' . $valCount . ')', 'vals' => $unreadMsgsIds];
                FatApp::getDb()->updateFromArray(Thread::DB_TBL_THREAD_MESSAGES, $data, $where);
            }
            $totalEmails--;
            /* allow to send only 100 email per execution */
            if ($totalEmails <= 0) {
                break;
            }
        }
        return 'Notifications sent successfully';
    }

}
