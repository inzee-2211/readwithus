<?php

/**
 * This class is used to handle Message Search
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class MessageSearch extends SearchBase
{

    private $joinThreadMessage = false;

    /**
     * Initialize Message Search
     */
    public function __construct()
    {
        parent::__construct(Thread::DB_TBL, 'tth');
    }

    /**
     * Join Thread Message
     * 
     * @return void
     */
    public function joinThreadMessage(): void
    {
        $this->joinThreadMessage = true;
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'tth.thread_id = ttm.message_thread_id', 'ttm');
    }

    /**
     * Join Latest Thread Message
     * 
     * @return void
     */
    public function joinLatestThreadMessage(): void
    {
        $this->joinThreadMessage = true;
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'tth.thread_id = ttm.message_thread_id', 'ttm');
        $this->joinTable(Thread::DB_TBL_THREAD_MESSAGES, 'LEFT OUTER JOIN', 'ttm_temp.message_id > ttm.message_id AND ttm_temp.message_thread_id = ttm.message_thread_id', 'ttm_temp');
        $this->addDirectCondition('ttm_temp.message_id IS NULL');
    }

    /**
     * Join Message Posted From User
     * 
     * @return void
     */
    public function joinMessagePostedFromUser(): void
    {
        if (!$this->joinThreadMessage) {
            trigger_error('You have not joined joinThreadMessage.', E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL, 'LEFT JOIN', 'ttm.message_from = tfr.user_id', 'tfr');
        $this->addMultipleFields(['tfr.user_id as message_from_user_id', 'CONCAT(tfr.user_first_name, " ", tfr.user_last_name) as message_from_name', 'tfr.user_last_name as message_from_last_name', 'tfr.user_email as message_from_email', 'tfr.user_username as message_from_username']);
    }

    /**
     * Join Message Posted To User
     * 
     * @return void
     */
    public function joinMessagePostedToUser(): void
    {
        if (!$this->joinThreadMessage) {
            trigger_error('You have not joined joinThreadMessage.', E_USER_ERROR);
        }
        $this->joinTable(User::DB_TBL, 'LEFT OUTER JOIN', 'ttm.message_to = tfto.user_id', 'tfto');
        $this->addMultipleFields(['tfto.user_id as message_to_user_id', 'CONCAT(tfto.user_first_name, " ", tfto.user_last_name) as message_to_name', 'tfto.user_last_name as message_to_last_name', 'tfto.user_email as message_to_email', 'tfto.user_username as message_to_username']);
    }

}
