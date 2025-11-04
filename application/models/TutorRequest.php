<?php

class TutorRequest extends MyAppModel
{
    public const DB_TBL = 'tbl_tutor_requests';
    public const DB_TBL_PREFIX = 'tutreq_';

    public function __construct($id = 0)
    {
        parent::__construct(static::DB_TBL, static::DB_TBL_PREFIX . 'id', $id);
    }

    public static function saveRequest(array $data, array $courseIds): bool
    {
        $db = FatApp::getDb();
        $db->startTransaction();

        $rec = new TableRecord(static::DB_TBL);
        $rec->assignValues($data);
        if (!$rec->addNew()) {
            $db->rollbackTransaction();
            trigger_error($rec->getError(), E_USER_WARNING);
            return false;
        }
        $reqId = $rec->getId();

        // map courses
        foreach ($courseIds as $cid) {
            $cid = FatUtility::int($cid);
            if ($cid < 1) continue;
            $m = new TableRecord('tbl_tutor_request_courses');
            $m->assignValues([
                'trc_tutreq_id' => $reqId,
                'trc_course_id' => $cid
            ]);
            if (!$m->addNew()) {
                $db->rollbackTransaction();
                trigger_error($m->getError(), E_USER_WARNING);
                return false;
            }
        }

        $db->commitTransaction();
        return true;
    }

    public static function getAdminEmail(): string
    {
        // Fallbacks: site owner or configured email
        $email = FatApp::getConfig('CONF_SITE_OWNER_EMAIL', FatUtility::VAR_STRING, '');
        if (empty($email)) {
            $email = FatApp::getConfig('CONF_CONTACT_EMAIL', FatUtility::VAR_STRING, '');
        }
        return $email;
    }
}
