<?php

/**
 * This class is used to handle Course Intended Learners
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class IntendedLearner extends MyAppModel
{
    const DB_TBL = 'tbl_courses_intended_learners';
    const DB_TBL_PREFIX = 'coinle_';

    /* Intended Learners Types */
    const TYPE_LEARNING = 1;
    const TYPE_REQUIREMENTS = 2;
    const TYPE_LEARNERS = 3;

    private $db;

    /**
     * Initialize
     *
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'coinle_id', $id);
        $this->db = FatApp::getDb();
    }
    
    /**
     * Get types
     *
     * @param integer|null $key
     * @return string|array
     */
    public static function getTypes(?int $key = null)
    {
        $arr = [
            static::TYPE_LEARNING => Label::getLabel('LBL_WHAT_WILL_STUDENT_LEARN'),
            static::TYPE_REQUIREMENTS => Label::getLabel('LBL_WHAT_ARE_THE_REQUIREMENTS'),
            static::TYPE_LEARNERS => Label::getLabel('LBL_WHO_IS_THE_COURSE_FOR'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }
    
    /**
     * Get types sub titles
     *
     * @param integer|null $key
     * @return string|array
     */
    public static function getTypesSubTitles(?int $key = null)
    {
        $arr = [
            static::TYPE_LEARNING => Label::getLabel('LBL_WHAT_WILL_STUDENT_LEARN_SUBTITLE'),
            static::TYPE_REQUIREMENTS => Label::getLabel('LBL_WHAT_ARE_THE_REQUIREMENTS_SUBTITLE'),
            static::TYPE_LEARNERS => Label::getLabel('LBL_WHO_IS_THE_COURSE_FOR_SUBTITLE'),
        ];
        return AppConstant::returArrValue($arr, $key);
    }

    /**
     * Setup Data with proper error handling and transaction
     *
     * @param array $data
     * @return bool
     */
    public function setup(array $data): bool
    {
        try {
            // Validate input data
            if (!$this->validateData($data)) {
                return false;
            }

            // Start transaction
            if (!$this->db->startTransaction()) {
                $this->error = Label::getLabel('LBL_TRANSACTION_START_FAILED');
                return false;
            }

            $courseId = (int)$data['course_id'];
            $insertIds = [];

            // Process learnings
            if (!$this->processIntendedLearners(
                $data['type_learnings'], 
                $data['type_learnings_ids'] ?? [], 
                $courseId, 
                self::TYPE_LEARNING, 
                $insertIds
            )) {
                $this->db->rollbackTransaction();
                return false;
            }

            // Process requirements
            if (!$this->processIntendedLearners(
                $data['type_requirements'], 
                $data['type_requirements_ids'] ?? [], 
                $courseId, 
                self::TYPE_REQUIREMENTS, 
                $insertIds
            )) {
                $this->db->rollbackTransaction();
                return false;
            }

            // Process learners
            if (!$this->processIntendedLearners(
                $data['type_learners'], 
                $data['type_learners_ids'] ?? [], 
                $courseId, 
                self::TYPE_LEARNERS, 
                $insertIds
            )) {
                $this->db->rollbackTransaction();
                return false;
            }

            // Update order
            if (!$this->updateOrder($insertIds)) {
                $this->db->rollbackTransaction();
                return false;
            }

            // Commit transaction
            if (!$this->db->commitTransaction()) {
                $this->error = Label::getLabel('LBL_TRANSACTION_COMMIT_FAILED');
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->db->rollbackTransaction();
            $this->error = Label::getLabel('LBL_SYSTEM_ERROR') . ': ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Validate input data
     *
     * @param array $data
     * @return bool
     */
    private function validateData(array $data): bool
    {
        if (empty($data['course_id']) || $data['course_id'] < 1) {
            $this->error = Label::getLabel('LBL_INVALID_COURSE_ID');
            return false;
        }

        // Check if we have at least one entry for each type
        $hasLearnings = !empty($data['type_learnings']) && is_array($data['type_learnings']) && count(array_filter($data['type_learnings'])) > 0;
        $hasRequirements = !empty($data['type_requirements']) && is_array($data['type_requirements']) && count(array_filter($data['type_requirements'])) > 0;
        $hasLearners = !empty($data['type_learners']) && is_array($data['type_learners']) && count(array_filter($data['type_learners'])) > 0;

        if (!$hasLearnings || !$hasRequirements || !$hasLearners) {
            $this->error = Label::getLabel('LBL_INTENDED_LEARNERS_DATA_REQUIRED');
            return false;
        }

        return true;
    }

    /**
     * Process intended learners data
     *
     * @param array $responses
     * @param array $ids
     * @param int $courseId
     * @param int $type
     * @param array $insertIds
     * @return bool
     */
    private function processIntendedLearners(array $responses, array $ids, int $courseId, int $type, array &$insertIds): bool
    {
        foreach ($responses as $key => $response) {
            $response = trim($response);
            if (empty($response)) {
                continue; // Skip empty responses
            }

            $respData = [
                'coinle_course_id' => $courseId,
                'coinle_type' => $type,
                'coinle_response' => $response,
                'coinle_created' => date('Y-m-d H:i:s'),
            ];

            // Handle existing record or new record
            $existingId = $this->getExistingId($ids, $key);
            if ($existingId > 0) {
                $respData['coinle_id'] = $existingId;
                // Use update for existing records
                if (!$this->db->updateFromArray(static::DB_TBL, $respData, ['smt' => 'coinle_id = ?', 'vals' => [$existingId]])) {
                    $this->error = $this->db->getError();
                    return false;
                }
                $newId = $existingId;
            } else {
                // Use insert for new records - don't include coinle_id to let auto-increment work
                unset($respData['coinle_id']);
                if (!$this->db->insertFromArray(static::DB_TBL, $respData)) {
                    $this->error = $this->db->getError();
                    return false;
                }
                $newId = $this->db->getInsertId();
            }

            if ($newId > 0) {
                $insertIds[] = $newId;
            }
        }

        return true;
    }

    /**
     * Get existing ID from the IDs array
     *
     * @param array $ids
     * @param int $key
     * @return int
     */
    private function getExistingId(array $ids, int $key): int
    {
        if (isset($ids[$key]) && !empty($ids[$key])) {
            $id = trim($ids[$key]);
            if (is_numeric($id) && $id > 0) {
                return (int)$id;
            }
        }
        return 0;
    }

    /**
     * Get Intended Learners Formatted Data
     *
     * @param int $courseId
     * @return array
     */
    public function get(int $courseId): array
    {
        try {
            $srch = new SearchBase(static::DB_TBL);
            $srch->addCondition('coinle_course_id', '=', $courseId);
            $srch->addCondition('coinle_deleted', 'IS', 'mysql_func_NULL', 'AND', true);
            $srch->doNotCalculateRecords();
            $srch->addMultipleFields([
                'coinle_id',
                'coinle_type',
                'coinle_course_id',
                'coinle_response',
            ]);
            $srch->addOrder('coinle_type', 'ASC');
            $srch->addOrder('coinle_order', 'ASC');
            
            $resultSet = $srch->getResultSet();
            if (!$resultSet) {
                return [];
            }
            
            $responses = $this->db->fetchAll($resultSet);
            $responseList = [];
            
            if ($responses) {
                foreach ($responses as $resp) {
                    $responseList[$resp['coinle_type']][] = $resp;
                }
            }
            
            return $responseList;

        } catch (Exception $e) {
            // Log error but return empty array to prevent breaking the flow
            error_log("Error fetching intended learners: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Function to remove intended learner
     *
     * @return bool
     */
    public function delete(): bool
    {
        try {
            $intendedId = $this->getMainTableRecordId();
            if ($intendedId < 1) {
                $this->error = Label::getLabel('LBL_INVALID_REQUEST');
                return false;
            }

            // Check if record exists
            $record = static::getAttributesById($intendedId, ['coinle_deleted', 'coinle_course_id']);
            if (!$record) {
                $this->error = Label::getLabel('LBL_INTENDED_LEARNER_NOT_FOUND');
                return false;
            }

            // Check if already deleted
            if (!empty($record['coinle_deleted'])) {
                $this->error = Label::getLabel('LBL_INTENDED_LEARNER_ALREADY_DELETED');
                return false;
            }

            // Mark as deleted
            $this->setFldValue('coinle_deleted', date('Y-m-d H:i:s'));
            if (!$this->save()) {
                $this->error = $this->getError();
                return false;
            }

            return true;

        } catch (Exception $e) {
            $this->error = Label::getLabel('LBL_SYSTEM_ERROR') . ': ' . $e->getMessage();
            return false;
        }
    }

    /**
     * Update order of intended learners
     *
     * @param array $ids
     * @return bool
     */
    public function updateOrder(array $ids): bool
    {
        try {
            if (empty($ids)) {
                return true; // Nothing to update
            }

            $order = 0;
            foreach ($ids as $id) {
                $order++;
                $query = "UPDATE " . static::DB_TBL . " 
                         SET coinle_order = " . $order . " 
                         WHERE coinle_id = " . (int)$id;
                
                if (!$this->db->query($query)) {
                    $this->error = $this->db->getError();
                    return false;
                }
            }
            return true;

        } catch (Exception $e) {
            $this->error = Label::getLabel('LBL_SYSTEM_ERROR') . ': ' . $e->getMessage();
            return false;
        }
    }
}