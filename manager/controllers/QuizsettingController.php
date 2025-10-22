<?php
class QuizsettingController extends AdminBaseController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCategories();
    }

    public function index()
    {
        $db = FatApp::getDb();

        // Fetch all data with proper error handling
        $levels = $this->fetchAllSafe($db, "SELECT id, level_name, created_at FROM course_levels ORDER BY level_name ASC");
        $subjects = $this->fetchAllSafe($db, 
            "SELECT s.id, s.subject, l.level_name, s.created_at
             FROM course_subjects s
             LEFT JOIN course_levels l ON l.id = s.level_id
             ORDER BY l.level_name ASC, s.subject ASC"
        );
        $types = $this->fetchAllSafe($db, "SELECT id, name, created_at FROM course_type ORDER BY name ASC");
        $examBoards = $this->fetchAllSafe($db, "SELECT id, name, created_at FROM course_examboards ORDER BY name ASC");
        $tiers = $this->fetchAllSafe($db,
            "SELECT t.id, t.name, eb.name AS examboard_name, t.created_at
             FROM course_tier t
             LEFT JOIN course_examboards eb ON eb.id = t.examboard_id
             ORDER BY eb.name ASC, t.name ASC"
        );

        $this->sets([
            'levels'      => $levels,
            'subjects'    => $subjects,
            'types'       => $types,
            'examBoards'  => $examBoards,
            'tiers'       => $tiers,
            'canEdit'     => $this->objPrivilege->canEditCategories(true),
        ]);
        $this->_template->render();
    }

    /**
     * Universal form handler for all entity types
     */
    public function form()
    {
        $this->objPrivilege->canEditCategories();
        
        $type = FatApp::getQueryStringData('type') ?? '';
        $id = FatUtility::int(FatApp::getQueryStringData('id') ?? 0);
        
        $validTypes = ['level', 'subject', 'type', 'examboard', 'tier'];
        if (!in_array($type, $validTypes)) {
            FatUtility::dieJsonError('Invalid form type');
        }

        $db = FatApp::getDb();
        $row = [];
        $formConfig = $this->getFormConfig($type);
        
    $row = [];
if ($id > 0) {
    $result = $db->query($formConfig['select_query'], [$id]);
    $row = $result ? $db->fetch($result) : [];
}


        // in form()
$frm = $this->buildForm($type, $id, $formConfig, $row);  // pass $row
$frm->fill($row); // keep this too

        
        $this->set('frm', $frm);
        $this->set('type', $type);
        $this->set('formTitle', $formConfig['title']);
        $this->_template->render(false, false, 'quizsetting/form.php');
    }

    /**
     * Universal save handler for all entity types
     */
   public function save()
{
    $this->objPrivilege->canEditCategories();

    $post = FatApp::getPostedData();
    $type = $post['entity_type'] ?? '';
    $id   = FatUtility::int($post['id'] ?? 0);

    $validTypes = ['level','subject','type','examboard','tier'];
    if (!in_array($type, $validTypes)) {
        FatUtility::dieJsonError('Invalid entity type');
    }

    $formConfig       = $this->getFormConfig($type);
    $validationRules  = $formConfig['validation_rules'];
    $fieldMap         = $formConfig['field_map'] ?? []; // <-- generic map

    // Validate required fields
    foreach ($validationRules as $field => $rules) {
        if (!empty($rules['required']) && empty(trim($post[$field] ?? ''))) {
            FatUtility::dieJsonError($rules['error_message']);
        }
    }

    $db   = FatApp::getDb();
    $data = [];

    // Build data using generic mapping
    foreach (array_keys($validationRules) as $field) {
        $column = $fieldMap[$field] ?? $field;      // <-- use map if present
        $data[$column] = trim($post[$field] ?? '');
    }

    try {
        $db->startTransaction();

        $tableName = $formConfig['table'];
        if ($id > 0) {
            // do not include created_at on update
            unset($data['created_at']);
            $success = $db->updateFromArray($tableName, $data, ['smt' => 'id = ?', 'vals' => [$id]]);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $success = $db->insertFromArray($tableName, $data);
        }

        if (!$success) {
            throw new Exception($db->getError());
        }

        $db->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SAVED_SUCCESSFULLY'));
    } catch (Exception $e) {
        $db->rollbackTransaction();
        FatUtility::dieJsonError($e->getMessage());
    }
}


    /**
     * Universal delete handler for all entity types
     */
    public function delete()
    {
        $this->objPrivilege->canEditCategories();
        
        $post = FatApp::getPostedData();
        $type = $post['entity_type'] ?? '';
        $id = FatUtility::int($post['id'] ?? 0);
        
        if ($id <= 0) {
            FatUtility::dieJsonError('Invalid ID');
        }

        $validTypes = ['level', 'subject', 'type', 'examboard', 'tier'];
        if (!in_array($type, $validTypes)) {
            FatUtility::dieJsonError('Invalid entity type');
        }

        $formConfig = $this->getFormConfig($type);
        $db = FatApp::getDb();

        try {
            // Check if entity is in use
            if (!empty($formConfig['check_usage_query'])) {
            $result = $db->query($formConfig['check_usage_query'], [$id]);
$row = $result ? $db->fetch($result) : [];
                if ($result && $row = $db->fetch($result)) {
                    if (($row['usage_count'] ?? 0) > 0) {
                        FatUtility::dieJsonError($formConfig['in_use_message']);
                    }
                }
            }

            if (!$db->deleteRecords($formConfig['table'], ['smt' => 'id = ?', 'vals' => [$id]])) {
                throw new Exception($db->getError());
            }

            FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
            
        } catch (Exception $e) {
            FatUtility::dieJsonError($e->getMessage());
        }
    }

    /**
     * Helper method to safely fetch all records
     */
    private function fetchAllSafe($db, $query, $params = [])
    {
        try {
           $result = empty($params) ? $db->query($query) : $db->query($query, $params);
return $result ? $db->fetchAll($result) : [];
            if (!empty($params)) {
                $stmt->setParameters($params);
            }
            $result = $stmt->execute();
            return $result ? $db->fetchAll($result) : [];
        } catch (Exception $e) {
            return [];
        }
    }

    /**
     * Configuration for different entity types
     */
    private function getFormConfig($type)
    {
        $configs = [
            'level' => [
                'table' => 'course_levels',
                'title' => Label::getLabel('LBL_LEVEL'),
                'select_query' => 'SELECT id, level_name as name FROM course_levels WHERE id = ?',
                'check_usage_query' => 'SELECT COUNT(*) as usage_count FROM course_subjects WHERE level_id = ?',
                'in_use_message' => 'Cannot delete: Level in use by subjects',
                'validation_rules' => [
                    'name' => [
                        'required' => true,
                        'error_message' => 'Level name is required'
                    ]
                ],
                'field_map' => ['name' => 'level_name']
            ],
            'subject' => [
                'table' => 'course_subjects',
                'title' => Label::getLabel('LBL_SUBJECT'),
                'select_query' => 'SELECT id, subject as name, level_id FROM course_subjects WHERE id = ?',
                'validation_rules' => [
                    'name' => [
                        'required' => true,
                        'error_message' => 'Subject name is required'
                    ],
                    'level_id' => [
                        'required' => true,
                        'error_message' => 'Level is required'
                    ]
                ],
                'field_map' => ['name' => 'subject']
            ],
            'type' => [
                'table' => 'course_type',
                'title' => Label::getLabel('LBL_TYPE'),
                'select_query' => 'SELECT id, name FROM course_type WHERE id = ?',
                'validation_rules' => [
                    'name' => [
                        'required' => true,
                        'error_message' => 'Type name is required'
                    ]
                ]
            ],
            'examboard' => [
                'table' => 'course_examboards',
                'title' => Label::getLabel('LBL_EXAM_BOARD'),
                'select_query' => 'SELECT id, name FROM course_examboards WHERE id = ?',
                'check_usage_query' => 'SELECT COUNT(*) as usage_count FROM course_tier WHERE examboard_id = ?',
                'in_use_message' => 'Cannot delete: Exam board in use by tiers',
                'validation_rules' => [
                    'name' => [
                        'required' => true,
                        'error_message' => 'Exam board name is required'
                    ]
                ]
            ],
            'tier' => [
                'table' => 'course_tier',
                'title' => Label::getLabel('LBL_TIER'),
                'select_query' => 'SELECT id, name, examboard_id FROM course_tier WHERE id = ?',
                'validation_rules' => [
                    'name' => [
                        'required' => true,
                        'error_message' => 'Tier name is required'
                    ],
                    'examboard_id' => [
                        'required' => true,
                        'error_message' => 'Exam board is required'
                    ]
                ]
            ]
        ];

        return $configs[$type] ?? [];
    }

    /**
     * Build form dynamically based on type
     */
 // change the signature
// change the signature stays the same:
private function buildForm($type, $id, $config, array $defaults = [])
{
    $frm = new Form('frm' . ucfirst($type));
    $frm->addHiddenField('', 'id', $id);
    $frm->addHiddenField('', 'entity_type', $type);

    $validationRules = $config['validation_rules'];

    foreach ($validationRules as $field => $rules) {
        $label = Label::getLabel('LBL_' . strtoupper($field));
        $def   = isset($defaults[$field]) ? $defaults[$field] : '';

        if ($field === 'level_id') {
            $db = FatApp::getDb();
            $levels = $this->fetchAllSafe($db, "SELECT id, level_name FROM course_levels ORDER BY level_name ASC");
            $levelOptions = ['' => Label::getLabel('LBL_SELECT_LEVEL')] + array_column($levels, 'level_name', 'id');

            // IMPORTANT: pass default as 4th arg
            $fld = $frm->addSelectBox($label, $field, $levelOptions, $def);
        } elseif ($field === 'examboard_id') {
            $db = FatApp::getDb();
            $boards = $this->fetchAllSafe($db, "SELECT id, name FROM course_examboards ORDER BY name ASC");
            $boardOptions = ['' => Label::getLabel('LBL_SELECT_EXAM_BOARD')] + array_column($boards, 'name', 'id');

            // IMPORTANT: pass default as 4th arg
            $fld = $frm->addSelectBox($label, $field, $boardOptions, $def);
        } else {
            // YoCoach Form: addRequiredField($caption, $name, $value = '')
            $fld = $frm->addRequiredField($label, $field, $def);
        }

        $fld->requirements()->setRequired();
    }

    $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));
    return $frm;
}


}