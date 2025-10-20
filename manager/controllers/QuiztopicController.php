<?php
class QuiztopicController extends AdminBaseController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewCategories();
    }

    /* LIST */
    public function index()
    {
        $db = FatApp::getDb();
        $rows = $db->fetchAll($db->query("
            SELECT qs.id, qs.topic_name,
                   l.level_name,
                   s.subject,
                   t.name  AS type_name,
                   tr.name AS tier_name,
                   eb.name AS examboard_name,
                   y.name  AS year_name,
                   qs.created_at
            FROM tbl_quiz_setup qs
            LEFT JOIN course_levels     l  ON l.id  = qs.level_id
            LEFT JOIN course_subjects   s  ON s.id  = qs.subject_id
            LEFT JOIN course_type       t  ON t.id  = qs.type_id
            LEFT JOIN course_tier       tr ON tr.id = qs.tier_id
            LEFT JOIN course_examboards eb ON eb.id = qs.examboard_id
            LEFT JOIN course_year       y  ON y.id  = qs.year_id
            ORDER BY qs.id DESC
        "));

        $this->sets([
            'rows'    => $rows,
            'canEdit' => $this->objPrivilege->canEditCategories(true),
        ]);
        $this->_template->render();
    }

    /* FORM (ADD/EDIT) */
   // QuiztopicController::form (or wherever you build $frm)
public function form(int $id = 0)
{
    $db = FatApp::getDb();
    $row = [];
    if ($id > 0) {
       $id  = (int) $id;
$sql = "SELECT id, level_id, subject_id, type_id, tier_id, examboard_id, year_id, topic_name
        FROM tbl_quiz_setup WHERE id = {$id}";
$row = $db->fetch($db->query($sql)) ?: [];
    }

    // Build option maps WITHOUT embedding a 'Select' placeholder
    $levels     = $db->fetchAll($db->query("SELECT id, level_name FROM course_levels ORDER BY level_name"));
    $types      = $db->fetchAll($db->query("SELECT id, name FROM course_type ORDER BY name"));
    $tiers      = $db->fetchAll($db->query("SELECT id, name FROM course_tier ORDER BY name"));
    $examboards = $db->fetchAll($db->query("SELECT id, name FROM course_examboards ORDER BY name"));
    $years      = $db->fetchAll($db->query("SELECT id, name FROM course_year ORDER BY name"));

    $optLevels     = $levels     ? array_column($levels, 'level_name', 'id') : [];
    $optTypes      = $types      ? array_column($types, 'name', 'id')       : [];
    $optTiers      = $tiers      ? array_column($tiers, 'name', 'id')       : [];
    $optBoards     = $examboards ? array_column($examboards, 'name', 'id')  : [];
    $optYears      = $years      ? array_column($years, 'name', 'id')       : [];

    $frm = new Form('frmQuizSetup');
    $frm->setFormTagAttribute('id', 'frmQuizSetup');
    $frm->setFormTagAttribute('class', 'web_form form_horizontal'); // theme classes

    // make the grid pretty (Yo!Coach/FATbit dev tags)
    $frm->developerTags['colClassPrefix']  = 'col-md-';
    $frm->developerTags['fld_default_col'] = 6;

    if ($id > 0) { $frm->addHiddenField('', 'id', $id); }

    // Level
    $fld = $frm->addSelectBox(Label::getLabel('LBL_LEVEL'), 'level_id', $optLevels, $row['level_id'] ?? '', [], Label::getLabel('LBL_SELECT'));
    $fld->setFieldTagAttribute('id', 'level_id');

    // Subject (start empty; we’ll populate via AJAX)
    $fld = $frm->addSelectBox(Label::getLabel('LBL_SUBJECT'), 'subject_id', [], $row['subject_id'] ?? '', [], Label::getLabel('LBL_SELECT'));
    $fld->setFieldTagAttribute('id', 'subject_id');

    // Type
    $frm->addSelectBox(Label::getLabel('LBL_TYPE'), 'type_id', $optTypes, $row['type_id'] ?? '', [], Label::getLabel('LBL_SELECT'));

    // Tier
    $frm->addSelectBox(Label::getLabel('LBL_TIER'), 'tier_id', $optTiers, $row['tier_id'] ?? '', [], Label::getLabel('LBL_SELECT'));

    // Exam board
    $frm->addSelectBox(Label::getLabel('LBL_EXAM_BOARD'), 'examboard_id', $optBoards, $row['examboard_id'] ?? '', [], Label::getLabel('LBL_SELECT'));

    // Year
    $frm->addSelectBox(Label::getLabel('LBL_YEAR'), 'year_id', $optYears, $row['year_id'] ?? '', [], Label::getLabel('LBL_SELECT'));

    // Topic name
    $frm->addRequiredField(Label::getLabel('LBL_TOPIC_NAME'), 'topic_name', $row['topic_name'] ?? '');

    $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_SAVE'));

    $this->set('frm', $frm);
    $this->set('data', $row);
    $this->_template->render();
}


    /* SAVE */
    public function save()
    {
        $this->objPrivilege->canEditCategories();

        $p = FatApp::getPostedData();
        $id = FatUtility::int($p['id'] ?? 0);

        $level_id    = FatUtility::int($p['level_id'] ?? 0);
        $subject_id  = FatUtility::int($p['subject_id'] ?? 0);
        $type_id     = FatUtility::int($p['type_id'] ?? 0);
        $tier_id     = FatUtility::int($p['tier_id'] ?? 0);
        $examboard_id= isset($p['examboard_id']) && $p['examboard_id'] !== '' ? FatUtility::int($p['examboard_id']) : null;
        $year_id     = isset($p['year_id'])      && $p['year_id']      !== '' ? FatUtility::int($p['year_id'])      : null;
        $topic_name  = trim($p['topic_name'] ?? '');

        if ($level_id <= 0 || $subject_id <= 0 || $type_id <= 0 || $tier_id <= 0 || $topic_name === '') {
            FatUtility::dieJsonError(Label::getLabel('LBL_REQUIRED_FIELDS_MISSING'));
        }

        $db = FatApp::getDb();
        $data = [
            'level_id'    => $level_id,
            'subject_id'  => $subject_id,
            'type_id'     => $type_id,
            'tier_id'     => $tier_id,
            'examboard_id'=> $examboard_id,
            'year_id'     => $year_id,
            'topic_name'  => $topic_name,
        ];

        $ok = $id > 0
            ? $db->updateFromArray('tbl_quiz_setup', $data, ['smt' => 'id = ?', 'vals' => [$id]])
            : $db->insertFromArray('tbl_quiz_setup', $data + ['created_at' => date('Y-m-d H:i:s')]);

        if (!$ok) {
            // Likely the UNIQUE key clash etc.
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SAVED_SUCCESSFULLY'));
    }

    /* DELETE */
    public function delete(int $id = 0)
    {
        $this->objPrivilege->canEditCategories();
        if ($id <= 0) { $id = FatUtility::int(FatApp::getPostedData('id', FatUtility::VAR_INT, 0)); }
        if ($id <= 0) { FatUtility::dieJsonError('Invalid ID'); }

        $db = FatApp::getDb();
        if (!$db->deleteRecords('tbl_quiz_setup', ['smt' => 'id = ?', 'vals' => [(int)$id]])) {
            FatUtility::dieJsonError($db->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_RECORD_DELETED_SUCCESSFULLY'));
    }

    /* AJAX: subjects by level */
    public function subjectsByLevel()
    {
        $levelId = FatUtility::int(FatApp::getPostedData('level_id', FatUtility::VAR_INT, 0));
        if ($levelId <= 0) {
            FatUtility::dieJsonSuccess(['data' => []]); // return empty, not an error
        }
        $opts = $this->pair("SELECT id, subject AS name FROM course_subjects WHERE level_id = {$levelId} ORDER BY subject ASC");
        
        FatUtility::dieJsonSuccess(['data' => $opts]);
    }

    /* small helper to get id=>name pairs */
    private function pair(string $sql): array
    {
        $db = FatApp::getDb();
        $rows = $db->fetchAll($db->query($sql));
        return $rows ? array_column($rows, 'name', 'id') : [];
    }
}
