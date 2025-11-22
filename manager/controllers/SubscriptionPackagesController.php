<?php
class SubscriptionPackagesController extends AdminBaseController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewGeneralSettings(); // adjust to your permission
    }

    public function index()
    {
         $srch = new SearchBase(SubscriptionPackage::DB_TBL, 'p');
    $srch->joinTable('course_levels', 'LEFT JOIN', 'cl.id = p.spackage_level_id', 'cl');
    $srch->addMultipleFields([
        'p.*',
        'cl.level_name'
    ]);
       $srch->addOrder('p.spackage_id', 'ASC');
        $rows = FatApp::getDb()->fetchAll($srch->getResultSet()) ?: [];
        $this->set('rows', $rows);
        $this->_template->render();
    }

    public function form($id = 0)
    {
        $id = FatUtility::int($id);
        $row = $id ? SubscriptionPackage::getById($id) : [];
        $frm = $this->getForm();
        if ($row) $frm->fill($row);
        $this->set('frm', $frm);
        $this->_template->render();
    }

  public function save()
{
    $frm = $this->getForm();
    $posted = FatApp::getPostedData();
$levelId = FatUtility::int($posted['spackage_level_id'] ?? 0);
    if ($levelId <= 0) {
        FatUtility::dieJsonError('Please select a level.');
    }
    if (!$frm->validate($posted)) {
        FatUtility::dieJsonError(current($frm->getValidationErrors()));
    }

    // Only pass actual columns to DB (whitelist)
    $data = [
        'spackage_name'            => trim($posted['spackage_name']),
        'spackage_description'     => trim($posted['spackage_description']),
        'spackage_price_monthly'   => $posted['spackage_price_monthly'],
        'spackage_level_id'        => $levelId,
        'spackage_price_yearly'    => ($posted['spackage_price_yearly'] === '' ? null : $posted['spackage_price_yearly']),
        'spackage_subject_limit'   => $posted['spackage_subject_limit'],
        'stripe_price_id_monthly'  => (trim($posted['stripe_price_id_monthly']) === '' ? null : trim($posted['stripe_price_id_monthly'])),
        'stripe_price_id_yearly'   => (trim($posted['stripe_price_id_yearly']) === '' ? null : trim($posted['stripe_price_id_yearly'])),
        'spackage_status'          => $posted['spackage_status'],
    ];

    $id = FatUtility::int($posted['spackage_id'] ?? 0);

    $rec = new TableRecord(SubscriptionPackage::DB_TBL);

   if ($id > 0) {

        // 🔥 FIXED UPDATE
        $rec->assignValues($data);

        if (!$rec->update(['smt' => 'spackage_id = ?', 'vals' => [$id]])) {
            FatUtility::dieJsonError($rec->getError());
    }
    } else {
        // INSERT using only whitelisted $data
        foreach ($data as $k => $v) {
            $rec->setFldValue($k, $v);
        }
        if (!$rec->addNew()) {
            FatUtility::dieJsonError($rec->getError());
        }
    }

    FatUtility::dieJsonSuccess('Saved');
}


private function getForm(): Form
{
    $f = new Form('frmPkg');
    $f->addHiddenField('', 'spackage_id', 0);

    /* ---- Load levels from course_levels ---- */
    $db = FatApp::getDb();
    $lvlOptions = [];

    $srch = new SearchBase('course_levels', 'cl');
    $srch->addMultipleFields(['cl.id', 'cl.level_name']);
    $srch->addOrder('cl.level_name', 'ASC');
    $rs = $srch->getResultSet();

    while ($row = $db->fetch($rs)) {
        $lvlOptions[(int)$row['id']] = $row['level_name'];
    }
    /* ---------------------------------------- */

    // Name
    $name = $f->addRequiredField('Name', 'spackage_name');
    $name->setFieldTagAttribute('placeholder', 'e.g., Basic / Gold / Premium');

    // Description
    $desc = $f->addTextArea('Description', 'spackage_description');
    $desc->setFieldTagAttribute('placeholder', 'Short summary of what this plan includes');

    // Prices
    $m = $f->addRequiredField('Monthly Price', 'spackage_price_monthly');
    $m->requirements()->setFloatPositive(true);
    $m->setFieldTagAttribute('placeholder', 'e.g., 9.99');

    $y = $f->addRequiredField('Yearly Price', 'spackage_price_yearly');
    $y->requirements()->setFloatPositive(true);
    $y->setFieldTagAttribute('placeholder', 'e.g., 99.00');

    // 🔹 Level (now with real options)
    $levelFld = $f->addSelectBox('Level', 'spackage_level_id', $lvlOptions, '', [], 'Select Level');
    $levelFld->requirements()->setRequired(true);

    // Subject limit
    $lim = $f->addRequiredField('Subject Limit', 'spackage_subject_limit');
    $lim->requirements()->setIntPositive();
    $lim->setFieldTagAttribute('placeholder', 'e.g., 1 for Basic, 2 for Gold, 5 for Premium');

    // Stripe IDs
    $pm = $f->addTextBox('Stripe Price ID (Monthly)', 'stripe_price_id_monthly');
    $pm->setFieldTagAttribute('placeholder', 'price_XXXX (from Stripe)');
    $pm->htmlAfterField = '<small class="note">Use the Stripe <b>Price ID</b> for the MONTHLY plan (not the Product ID).</small>';

    $py = $f->addTextBox('Stripe Price ID (Yearly)', 'stripe_price_id_yearly');
    $py->setFieldTagAttribute('placeholder', 'price_XXXX (from Stripe)');
    $py->htmlAfterField = '<small class="note">Use the Stripe <b>Price ID</b> for the YEARLY plan.</small>';

    // Status
    $f->addSelectBox('Status', 'spackage_status', [1 => 'Active', 0 => 'Inactive'], 1);

    $f->addSubmitButton('', 'btn_submit', 'Save');
    return $f;
}



}
