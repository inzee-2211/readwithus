<?php

/**
 * This class is used to handle Meeting Tool
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class MeetingTool extends MyAppModel
{

    const DB_TBL = 'tbl_meeting_tools';
    const DB_TBL_PREFIX = 'metool_';
    /* Meeting Tools */
    const ATOM_CHAT = 'AtomChat';
    const LESSON_SPACE = 'LessonSpace';
    const ZOOM_MEETING = 'ZoomMeeting';

    /**
     * Initialize Meeting Tool
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'metool_id', $id);
    }

    /**
     * Get Statues
     * 
     * @param int $key
     * @return string|array
     */
    public static function getStatues(int $key = null)
    {
        $arr = [
            AppConstant::ACTIVE => Label::getLabel('LBL_ACTIVE'),
            AppConstant::INACTIVE => Label::getLabel('LBL_INACTIVE'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Setup Tool
     * 
     * @param array $post
     * @return bool
     */
    public function setup(array $post): bool
    {
        $settings = [];
        foreach ($post['metool_settings'] as $key => $value) {
            array_push($settings, ['key' => $key, 'value' => $value]);
        }
        $this->assignValues($post);
        $this->setFldValue('metool_settings', json_encode($settings));
        if (!$this->save()) {
            return false;
        }
        return true;
    }

    /**
     * Update Status
     * 
     * @param int $status
     * @return bool
     */
    public function updateStatus(int $status): bool
    {
        $this->setFldValue('metool_status', $status);
        return $this->save();
    }

    public function getDetail()
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('metool_id', '=', $this->mainTableRecordId);
        $srch->addMultipleFields(['metool_id', 'metool_code', 'metool_settings']);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get By Code
     * 
     * @param string $code
     * @return null|array
     */
    public static function getByCode(string $code)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('metool_code', '=', $code);
        $srch->addMultipleFields(['metool_id', 'metool_code', 'metool_settings']);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Tools
     * 
     * @param int $key
     * @return string|array
     */
    public static function getTools(int $key = null)
    {
        $arr = [
            static::ATOM_CHAT => Label::getLabel('LBL_COMET_CHAT'),
            static::ZOOM_MEETING => Label::getLabel('LBL_ZOOM_MEETING'),
            static::LESSON_SPACE => Label::getLabel('LBL_LESSON_SPACE'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    public static function getActiveTool()
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('metool_status', '=', AppConstant::ACTIVE);
        $srch->addMultipleFields(['metool_id', 'metool_code', 'metool_settings']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

}
