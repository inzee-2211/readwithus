<?php

/**
 * Notifications Controller is used for handling Notifications
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class NotificationsController extends DashboardController
{

    /**
     * Initialize Notifications
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render Notifications Page
     */
    public function index()
    {
        $this->set('frm', $this->getSearchForm());
        $this->_template->render();
    }

    /**
     * Search & List Notifications
     */
    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $srch = new SearchBase(Notification::DB_TBL);
        $srch->addCondition("notifi_user_id", '=', $this->siteUserId);
        $srch->addCondition("notifi_user_type", 'IN', [0, $this->siteUserType]);
        $srch->addOrder("notifi_id", 'desc');
        $srch->setPageNumber($post['pageno']);
        $srch->setPageSize($post['pagesize']);
        $this->set('list', FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->set('post', $post);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    /**
     * Mark Read Notification
     * 
     * @param int $notificationId
     */
    public function readNotification(int $notificationId)
    {
        $notificationId = FatUtility::int($notificationId);
        $srch = new SearchBase(Notification::DB_TBL);
        $srch->addCondition("notifi_user_id", '=', $this->siteUserId);
        $srch->addCondition("notifi_id", '=', $notificationId);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $notification = FatApp::getDb()->fetch($srch->getResultSet());
        if (empty($notification)) {
            FatApp::redirectUser(MyUtility::makeUrl('notifications', '', [], CONF_WEBROOT_DASHBOARD));
        }
        $link = MyUtility::makeUrl('notifications');
        if (!empty($notification['notifi_link'])) {
            $link = $notification['notifi_link'];
        }
        if ($notification['notifi_read'] === NULL) {
            $notifi = new Notification($this->siteUserId);
            $notifi->markRead([$notificationId]);
        }
        FatApp::redirectUser($link);
    }

    /**
     * Delete Records
     */
    public function deleteRecords()
    {
        $notificationIds = FatApp::getPostedData('record_ids');
        $notificationIds = explode(',', $notificationIds);
        $notifi = new Notification($this->siteUserId);
        if (!$notifi->remove($notificationIds)) {
            FatUtility::dieJsonError(Label::getLabel('ERROR_UNBALE_TO_DELETE'));
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_NOTIFICATION_DELETED_SUCCESSFULLY'));
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $notificationIds = FatApp::getPostedData('record_ids');
        $notificationIds = explode(',', $notificationIds);
        $markread = FatApp::getPostedData('status', FatUtility::VAR_INT, 0);
        $updateFunction = 'markUnRead';
        if ($markread == AppConstant::YES) {
            $updateFunction = 'markRead';
        }
        $notifi = new Notification($this->siteUserId);
        if (!$notifi->$updateFunction($notificationIds)) {
            FatUtility::dieJsonError($notifi->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_STATUS_UPDATED_SUCCESSFULLY!'));
    }

    /**
     * Get Unread Count
     */
    public function getUnreadCount()
    {
        $notiCount = (new Notification($this->siteUserId))->getUnreadCount($this->siteUserType);
        FatUtility::dieJsonSuccess(['notiCount' => $notiCount]);
    }

    private function getSearchForm()
    {
        $frm = new Form('frmNotificationSrch');
        $frm->addHiddenField('', 'pagesize', AppConstant::PAGESIZE)->requirements()->setIntPositive();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setIntPositive();
        return $frm;
    }

}
