<?php

/**
 * This class is used to handle Teachers class Planning
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Plan extends MyAppModel
{

    const DB_TBL = 'tbl_plans';
    const DB_TBL_PREFIX = 'plan_';
    const DB_TBL_LESSON = 'tbl_plan_lessons';
    const DB_TBL_GCLASS = 'tbl_plan_classes';
    /* Levels */
    const LEVEL_BEGINNER = 1;
    const LEVEL_UPPER_BEGINNER = 2;
    const LEVEL_INTERMEDIATE = 3;
    const LEVEL_UPPER_INTERMEDIATE = 4;
    const LEVEL_ADVANCED = 5;
    const PLAN_TYPE_LESSONS = 1;
    const PLAN_TYPE_CLASSES = 2;
    const LISTING_TYPE = 1;

    /**
     * Initialize Plan 
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'plan_id', $id);
    }

    /**
     * Get Plan Levels
     * 
     * @param int $key
     * @return string|array
     */
    public static function getLevels(int $key = null)
    {
        $arr = [
            static::LEVEL_BEGINNER => Label::getLabel('LBL_BEGINNER'),
            static::LEVEL_UPPER_BEGINNER => Label::getLabel('LBL_UPPER_BEGINNER'),
            static::LEVEL_INTERMEDIATE => Label::getLabel('LBL_INTERMEDIATE'),
            static::LEVEL_UPPER_INTERMEDIATE => Label::getLabel('LBL_UPPER_INTERMEDIATE'),
            static::LEVEL_ADVANCED => Label::getLabel('LBL_ADVANCED'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Get Lesson Plans
     * 
     * @param array $lessonIds
     * @return array
     */
    public static function getLessonPlans(array $lessonIds): array
    {
        if (count($lessonIds) == 0) {
            return [];
        }
        $srch = new SearchBase(static::DB_TBL, 'plan');
        $srch->joinTable(static::DB_TBL_LESSON, 'LEFT JOIN', 'planles.planles_plan_id = plan.plan_id', 'planles');
        $srch->addMultipleFields(['planles_ordles_id', 'plan_id', 'plan_title', 'plan_detail', 'plan_level', 'plan_links']);
        $srch->addCondition('planles.planles_ordles_id', 'IN', array_unique($lessonIds));
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'planles_ordles_id');
    }

    /**
     * Get Class Plans
     * 
     * @param array $classIds
     * @return array
     */
    public static function getGclassPlans(array $classIds): array
    {
        if (count($classIds) == 0) {
            return [];
        }
        $srch = new SearchBase(static::DB_TBL, 'plan');
        $srch->joinTable(static::DB_TBL_GCLASS, 'LEFT JOIN', 'plancls.plancls_plan_id = plan.plan_id', 'plancls');
        $srch->addMultipleFields(['plancls_grpcls_id', 'plan_id', 'plan_title', 'plan_detail', 'plan_level', 'plan_links']);
        $srch->addCondition('plancls.plancls_grpcls_id', 'IN', array_unique($classIds));
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'plancls_grpcls_id');
    }

}
