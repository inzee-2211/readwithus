<?php

/**
 * This class is used to handle Course Progress
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class CourseProgress extends MyAppModel
{
    const DB_TBL = 'tbl_course_progresses';
    const DB_TBL_PREFIX = 'crspro_';

    const PENDING = 1;
    const IN_PROGRESS = 2;
    const COMPLETED = 3;
    const CANCELLED = 4;

    /**
     * Initialize Course
     *
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'crspro_id', $id);
    }

    /**
     * Get Statuses
     *
     * @param int $key
     * @return string|array
     */
    public static function getStatuses(int $key = null)
    {
        $arr = [
            static::PENDING => Label::getLabel('LBL_PENDING'),
            static::IN_PROGRESS => Label::getLabel('LBL_IN_PROGRESS'),
            static::COMPLETED => Label::getLabel('LBL_COMPLETED'),
            static::CANCELLED => Label::getLabel('LBL_CANCELLED'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Begin Course
     *
     * @param int $ordcrsId
     * @return bool
     */
    public function setup(int $ordcrsId)
    {
        $db = FatApp::getDb();
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $this->assignValues([
            'crspro_ordcrs_id' => $ordcrsId,
            'crspro_status' => static::PENDING,
            'crspro_lecture_id' => 0,
            'crspro_progress' => 0,
        ]);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        return true;
    }
    /**
     * Get or create course progress by (user, course)
     * Works for subscription model (no order dependency)
     */
    /**
     * Get or create course progress by (user, course)
     * Works for subscription model (no order dependency)
     */
    public static function getOrCreateProgressByUserAndCourse(int $userId, int $courseId): ?CourseProgress
    {
        $db = FatApp::getDb();

        // Sanity check to avoid inserting empties
        if ($userId <= 0 || $courseId <= 0) {
            trigger_error("CourseProgress:getOrCreate called with invalid ids (u:$userId, c:$courseId)", E_USER_WARNING);
            return null;
        }

        // 1) Find existing record
        $srch = new SearchBase(static::DB_TBL, 'cp');
        $srch->addCondition('cp.crspro_user_id', '=', $userId);
        $srch->addCondition('cp.crspro_course_id', '=', $courseId);
        $srch->addMultipleFields(['cp.crspro_id']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);

        if ($row = $db->fetch($srch->getResultSet())) {
            return new static((int) $row['crspro_id']);
        }

        // 2) Create new progress record - FIXED VERSION
        $data = [
            'crspro_user_id' => (int) $userId,
            'crspro_course_id' => (int) $courseId,
            'crspro_status' => static::PENDING,
            'crspro_ordcrs_id' => null,
            'crspro_lecture_id' => 0,
            'crspro_progress' => 0,
            'crspro_covered' => json_encode([]),
            'crspro_completed' => null,
            // leave crspro_ordcrs_id NULL for subscription path
        ];

        // Use regular insert instead of insertFromArray with special parameters
        $record = new CourseProgress();
        $record->assignValues($data);

        if (!$record->save()) {
            trigger_error('CourseProgress insert failed: ' . $record->getError(), E_USER_WARNING);

            // Try to fetch again in case of race condition
            $srch2 = new SearchBase(static::DB_TBL, 'cp2');
            $srch2->addCondition('cp2.crspro_user_id', '=', $userId);
            $srch2->addCondition('cp2.crspro_course_id', '=', $courseId);
            $srch2->addMultipleFields(['cp2.crspro_id']);
            $srch2->doNotCalculateRecords();
            $srch2->setPageSize(1);

            if ($row2 = $db->fetch($srch2->getResultSet())) {
                return new static((int) $row2['crspro_id']);
            }
            return null;
        }

        $id = $record->getMainTableRecordId();
        return new static($id);
    }


    /**
     * Set currect lecture
     *
     * @param int $lectureId
     * @return bool
     */
    public function setCurrentLecture(int $lectureId)
    {
        $this->setFldValue('crspro_lecture_id', $lectureId);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    /**
     * Mark/Unmark a lecture as completed
     *
     * @param int $lectureId
     * @param int $markCovered
     * @return bool
     */
    public function setCompletedLectures(int $lectureId, int $markCovered = AppConstant::YES)
    {
        $data = static::getAttributesById($this->getMainTableRecordId(), ['crspro_covered', 'crspro_course_id']);
        $lectures = ($data['crspro_covered']) ? json_decode($data['crspro_covered']) : [];
        if ($markCovered == AppConstant::YES) {
            $lectures = array_unique(array_merge($lectures, [$lectureId]));
        } else {
            $key = array_search($lectureId, $lectures);
            if ($key !== false) {
                unset($lectures[$key]);
                $lectures = array_values($lectures);
            }
        }
        $this->setFldValue('crspro_covered', json_encode($lectures));
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return $this->updateProgress((int) $data['crspro_course_id']);
    }

    /**
     * Get & Format Section's lecture completed stats
     *
     * @param array $sections
     * @return array
     */
    public function getLectureStats(array $sections)
    {
        $lectures = static::getAttributesById($this->getMainTableRecordId(), 'crspro_covered');
        $lectures = ($lectures) ? json_decode($lectures) : [];
        $stats = [];
        foreach ($sections as $section) {
            if (!isset($section['lectures']) || count($section['lectures']) < 1) {
                continue;
            }
            $completed = [];
            foreach ($section['lectures'] as $lecture) {
                if (in_array($lecture['lecture_id'], $lectures)) {
                    $completed[] = $lecture['lecture_id'];
                }
            }
            $stats[$section['section_id']] = $completed;
        }
        return $stats;
    }

    /**
     * Update Course progress
     *
     * @param int $courseId
     * @return bool
     */
    public function updateProgress(int $courseId)
    {
        $progress = $this->getAttributesById($this->getMainTableRecordId(), [
            'crspro_covered',
            'crspro_completed',
            'crspro_ordcrs_id',
        ]);

        $coveredIds = ($progress['crspro_covered']) ? json_decode($progress['crspro_covered'], true) : [];
        if (!is_array($coveredIds)) {
            $coveredIds = [];
        }

        /* Get active lectures for this course */
        $srch = new SearchBase(Lecture::DB_TBL);
        $srch->addCondition('lecture_course_id', '=', $courseId);
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        $srch->addFld('lecture_id');
        $srch->doNotCalculateRecords();
        $rs = $srch->getResultSet();
        $activeLectureIds = [];
        while ($row = FatApp::getDb()->fetch($rs)) {
            $activeLectureIds[] = (int) $row['lecture_id'];
        }

        $courseLecturesCount = count($activeLectureIds);

        // Filter covered IDs to only include those that still exist and belong to this course
        $validCoveredIds = array_intersect($coveredIds, $activeLectureIds);
        $lecturesCoveredCount = count($validCoveredIds);

        // Update crspro_covered if needed (clean up ghost lectures)
        if (count($coveredIds) !== $lecturesCoveredCount) {
            $this->setFldValue('crspro_covered', json_encode(array_values($validCoveredIds)));
        }

        $percent = 0;
        if ($courseLecturesCount > 0) {
            $percent = round(($lecturesCoveredCount * 100) / $courseLecturesCount, 2);
        }
        $this->setFldValue('crspro_progress', $percent);

        /* completed date will be updated only once(first time completed) */
        if (!$progress['crspro_completed'] && $percent == 100.00) {
            $this->setFldValue('crspro_completed', date('Y-m-d'));
            $this->setFldValue('crspro_status', CourseProgress::COMPLETED);
        }

        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }
    /**
     * Get next & previous lecture id
     *
     * @param array   $data
     * @param int $next
     * @return integer
     */
    public function getLecture($data, int $next = AppConstant::YES)
    {
        $lectureId = 0;
        if ($data['crspro_lecture_id'] < 1 && $data['crspro_progress'] > 0) {
            return $lectureId;
        }
        $lectureOrder = Lecture::getAttributesById($data['crspro_lecture_id'], 'lecture_order');
        $lectureOrder = empty($lectureOrder) ? 0 : $lectureOrder;
        /* get next lecture */
        $srch = new SearchBase(Lecture::DB_TBL);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('lecture_id');
        $srch->addCondition('lecture_course_id', '=', $data['crspro_course_id']);
        if ($next == AppConstant::YES) {
            $srch->addCondition('lecture_order', '>', $lectureOrder);
            $srch->addOrder('lecture_order', 'ASC');
        } else {
            $srch->addCondition('lecture_order', '<', $lectureOrder);
            $srch->addOrder('lecture_order', 'DESC');
        }
        $srch->addCondition('lecture_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
        if ($lecture = FatApp::getDb()->fetch($srch->getResultSet())) {
            $lectureId = $lecture['lecture_id'];
        }
        return $lectureId;
    }

    /**
     * Reset course progress to retake
     *
     * @return bool
     */
    public function retake()
    {
        $this->assignValues([
            'crspro_lecture_id' => 0,
            'crspro_progress' => 0,
            'crspro_covered' => NULL,
        ]);
        if (!$this->save()) {
            $this->error = $this->getError();
            return false;
        }
        return true;
    }

    // public function getNextPrevLectures()
    // {
    //     $srch = new SearchBase(CourseProgress::DB_TBL);
    //     $srch->addCondition('crspro_id', '=', $this->getMainTableRecordId());
    //     $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'ordcrs_id = crspro_ordcrs_id');
    //     $srch->addMultipleFields([
    //         'crspro_lecture_id',
    //         'ordcrs_course_id AS crspro_course_id',
    //         'crspro_progress'
    //     ]);
    //     $srch->doNotCalculateRecords();
    //     $srch->setPageSize(1);
    //     if (!$data = FatApp::getDb()->fetch($srch->getResultSet())) {
    //         FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
    //         return false;
    //     }
    //     /* get previous and next lectures */
    //     return [
    //         'next' => $this->getLecture($data),
    //         'previous' => $this->getLecture($data, AppConstant::NO),
    //     ];
    // }

    public function getNextPrevLectures()
    {
        $data = static::getAttributesById(
            $this->getMainTableRecordId(),
            ['crspro_lecture_id', 'crspro_course_id', 'crspro_progress']
        );

        if (!$data) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            return false;
        }

        // Use crspro_course_id directly (no OrderCourse join)
        return [
            'next' => $this->getLecture($data),
            'previous' => $this->getLecture($data, AppConstant::NO),
        ];
    }
    public function isLectureValid(int $lectureId)
    {
        if ($lectureId < 1) {
            return true;
        }

        $db = FatApp::getDb();
        $srch = new SearchBase(static::DB_TBL, 'cp');

        // no more OrderCourse join
        $srch->joinTable(Lecture::DB_TBL, 'INNER JOIN', 'lecture_course_id = cp.crspro_course_id', 'lecture');

        $srch->addCondition('cp.crspro_id', '=', $this->getMainTableRecordId());
        $srch->addCondition('lecture.lecture_id', '=', $lectureId);
        $srch->addFld('cp.crspro_id');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);

        if (!$db->fetch($srch->getResultSet())) {
            return false;
        }

        return true;
    }

    // public function isLectureValid(int $lectureId)
    // {
    //     if($lectureId < 1) {
    //         return true;
    //     }
    //     $srch = new SearchBase(CourseProgress::DB_TBL, 'crspro');
    //     $srch->joinTable(OrderCourse::DB_TBL, 'INNER JOIN', 'ordcrs_id = crspro_ordcrs_id');
    //     $srch->joinTable(Lecture::DB_TBL, 'INNER JOIN', 'lecture_course_id = ordcrs_course_id ');
    //     $srch->addCondition('crspro_id', '=', $this->getMainTableRecordId());
    //     $srch->addCondition('lecture_id', '=', $lectureId);
    //     $srch->addFld('crspro_id');
    //     $srch->doNotCalculateRecords();
    //     $srch->setPageSize(1);
    //     if (!FatApp::getDb()->fetch($srch->getResultSet())) {
    //         return false;
    //     }
    //     return true;
    // }
}
