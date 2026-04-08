<?php

/**
 * This class is used to handle Notification
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Notification extends FatModel
{

    const DB_TBL = "tbl_notifications";
    const DB_TBL_PREFIX = "notifi_";
    const TYPE_ADMINISTRATOR = 1;
    /* Lessons */
    const TYPE_LESSON_SCHEDULED = 2;
    const TYPE_LESSON_RESCHEDULED = 3;
    const TYPE_LESSON_CANCELLED = 4;
    const TYPE_LESSON_COMPLETED = 5;
    /* Issues */
    const TYPE_ISSUE_REPORTED = 6;
    const TYPE_ISSUE_RESOLVED = 7;
    const TYPE_ISSUE_ESCALATED = 8;
    const TYPE_ISSUE_CLOSED = 9;
    /* Wallet */
    const TYPE_WALLET_CREDIT = 10;
    const TYPE_WALLET_DEBIT = 11;
    /* Other */
    const TYPE_REDEEM_GIFTCARD = 12;
    const TYPE_WITHDRAW_REQUEST = 13;
    const TYPE_TEACHER_APPROVAL = 14;
    const TYPE_CHANGE_PASSWORD = 15;
    /* CLASS */
    const TYPE_CLASS_CANCELLED = 16;
    /* order */
    const TYPE_ORDER_PAID = 17;
    const TYPE_ORDER_CANCELLED = 18;

    const TYPE_SUBSCRIPTION_CANCELLED = 19;

    const TYPE_PACKAGE_CANCELLED = 20;
    
    private $userId;
    private $type;
    private $title;
    private $desc;

    /**
     * Initialize Notification
     * 
     * @param int $userId
     * @param int $type
     */
    public function __construct(int $userId, int $type = 0)
    {
        $this->userId = $userId;
        $this->type = $type;
    }

    /**
     * Send Notification
     *
     * @param array $vars
     * @return bool
     */
    public function sendNotification(array $vars = [], int $userType = 0): bool
    {
        $this->setTitleDesc($vars);
        $record = new TableRecord(static::DB_TBL);
        $record->assignValues([
            'notifi_user_id' => $this->userId,
            'notifi_user_type' => $userType,
            'notifi_type' => $this->type,
            'notifi_title' => $this->title,
            'notifi_desc' => $this->desc,
            'notifi_link' => $vars['{link}'] ?? '',
            'notifi_added' => date('Y-m-d H:i:s'),
        ]);
        if (!$record->addNew()) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Set Title & Description
     * 
     * @param int $userId
     * @param array $vars
     */
    private function setTitleDesc(array $vars)
    {
        $langId = User::getAttributesById($this->userId, 'user_lang_id');
        switch ($this->type) {
            case static::TYPE_LESSON_SCHEDULED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_LESSON_SCHEDULED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_LESSON_SCHEDULED', $langId);
                break;
            case static::TYPE_LESSON_RESCHEDULED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_LESSON_RESCHEDULED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_LESSON_RESCHEDULED', $langId);
                break;
            case static::TYPE_LESSON_CANCELLED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_LESSON_CANCELLED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_LESSON_CANCELLED', $langId);
                break;
            case static::TYPE_CLASS_CANCELLED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_CLASS_CANCELLED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_CLASS_CANCELLED', $langId);
                break;
            case static::TYPE_LESSON_COMPLETED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_LESSON_COMPLETED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_LESSON_COMPLETED', $langId);
                break;
            case static::TYPE_ISSUE_REPORTED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ISSUE_REPORTED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ISSUE_REPORTED', $langId);
                break;
            case static::TYPE_ISSUE_RESOLVED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ISSUE_RESOLVED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ISSUE_RESOLVED', $langId);
                break;
            case static::TYPE_ISSUE_ESCALATED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ISSUE_ESCALATED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ISSUE_ESCALATED', $langId);
                break;
            case static::TYPE_ISSUE_CLOSED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ISSUE_CLOSED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ISSUE_CLOSED', $langId);
                break;
            case static::TYPE_WALLET_CREDIT:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_WALLET_CREDIT', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_WALLET_CREDIT', $langId);
                break;
            case static::TYPE_WALLET_DEBIT:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_WALLET_DEBIT', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_WALLET_DEBIT', $langId);
                break;
            case static::TYPE_REDEEM_GIFTCARD:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_REDEEM_GIFTCARD', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_REDEEM_GIFTCARD', $langId);
                break;
            case static::TYPE_WITHDRAW_REQUEST:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_WITHDRAW_REQUEST', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_WITHDRAW_REQUEST', $langId);
                break;
            case static::TYPE_TEACHER_APPROVAL:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_TEACHER_APPROVAL', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_TEACHER_APPROVAL', $langId);
                break;
            case static::TYPE_CHANGE_PASSWORD:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_CHANGE_PASSWORD', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_CHANGE_PASSWORD', $langId);
                break;
            case static::TYPE_ORDER_PAID:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ORDER_PAID', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ORDER_PAID', $langId);
                break;
            case static::TYPE_ORDER_CANCELLED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_ORDER_CANCELLED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_ORDER_CANCELLED', $langId);
                break;
            case static::TYPE_SUBSCRIPTION_CANCELLED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_SUBSCRIPTION_CANCELLED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_SUBSCRIPTION_CANCELLED', $langId);
                break;
            case static::TYPE_PACKAGE_CANCELLED:
                $title = Label::getLabel('NOTIFI_TITLE_TYPE_PACKAGE_CANCELLED', $langId);
                $desc = Label::getLabel('NOTIFI_DESC_TYPE_PACKAGE_CANCELLED', $langId);
                break;
        }
        $this->title = str_replace(array_keys($vars), $vars, $title);
        $this->desc = str_replace(array_keys($vars), $vars, $desc);
    }

    /**
     * Read Notifications
     * 
     * @param array $notificationIds
     * @return bool
     */
    public function markRead(array $notificationIds): bool
    {
        $notifiIds = array_filter(FatUtility::int($notificationIds));
        $query = 'UPDATE ' . static::DB_TBL . ' SET  notifi_read="' . date('Y-m-d H:i:s') .
                '"  WHERE notifi_id IN (' . implode(",", $notifiIds) . ') and notifi_user_id = ' . $this->userId;
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Read Notifications
     * 
     * @param array $notificationIds
     * @return bool
     */
    public function markUnRead(array $notificationIds): bool
    {
        $notifiIds = array_filter(FatUtility::int($notificationIds));
        $query = 'UPDATE ' . static::DB_TBL . ' SET  notifi_read=null WHERE notifi_id IN (' .
                implode(",", $notifiIds) . ') and notifi_user_id = ' . $this->userId;
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Delete notifications
     * 
     * @param array $notificationIds
     * @return bool
     */
    public function remove(array $notificationIds): bool
    {
        $notifiIds = array_unique(array_filter(FatUtility::int($notificationIds)));
        $query = 'DELETE FROM ' . static::DB_TBL . ' WHERE notifi_id IN (' .
                implode(",", $notifiIds) . ') and notifi_user_id = ' . $this->userId;
        if (!FatApp::getDb()->query($query)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

    /**
     * Get Unread Count
     * 
     * @param int $userType
     * @return int
     */
    public function getUnreadCount(int $userType = 0): int
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition("notifi_user_id", '=', $this->userId);
        if (!empty($userType)) {
            $srch->addCondition("notifi_user_type", 'IN', [0, $userType]);
        }
        $srch->addCondition("notifi_read", 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('COUNT(notifi_id) as unread_count');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(100);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        return FatUtility::int($row['unread_count'] ?? 0);
    }

}
