<?php

/**
 * Price Slabs Controller is used for Price Slabs handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class PriceSlabsController extends AdminBaseController
{

    /**
     * Initialize Price Slabs
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewPriceSlab();
    }

    public function index()
    {
        $this->set("canEdit", $this->objPrivilege->canEditPriceSlab(true));
        $this->set("frm", $this->getSearchForm());
        $this->_template->render();
    }

    /**
     * Search & List Price Slabs
     */
    public function search()
    {
        $frm = $this->getSearchForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        $srch = PriceSlab::getSearchObject(false);
        $srch->addOrder('prislab_active', 'desc');
        $srch->addOrder('prislab_id', 'desc');
        $srch->setPageNumber($post['pageno']);
        $srch->setPageSize($post['pagesize']);
        $this->set("records", FatApp::getDb()->fetchAll($srch->getResultSet()));
        $this->set("canEdit", $this->objPrivilege->canEditPriceSlab(true));
        $this->set('post', $post);
        $this->set('pageCount', $srch->pages());
        $this->set('recordCount', $srch->recordCount());
        $this->_template->render(false, false);
    }

    /**
     * Render Price Slab Form
     * 
     * @param int $slabId
     */
    public function form(int $slabId = 0)
    {
        $this->objPrivilege->canEditPriceSlab();
        $form = $this->getForm($slabId);
        if (0 < $slabId) {
            $data = PriceSlab::getAttributesById($slabId, ['prislab_id', 'prislab_min', 'prislab_max', 'prislab_active']);
            if ($data === false) {
                FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
            }
            $form->fill($data);
        }
        $this->set('psId', $slabId);
        $this->set('form', $form);
        $this->_template->render(false, false);
    }

    /**
     * Setup Price Slab
     */
    public function setup()
    {
        $this->objPrivilege->canEditPriceSlab();
        $frm = $this->getForm();
        $post = $frm->getFormDataFromArray(FatApp::getPostedData());
        if (false === $post) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        $psId = $post['prislab_id'];
        unset($post['prislab_id']);
        $priceSlab = new PriceSlab($psId);
        if ($priceSlab->isSlapCollapse($post['prislab_min'], $post['prislab_max'])) {
            FatUtility::dieJsonError(Label::getLabel('LBL_YOUR_SLOT_IS_COLLAPSE_WITH_OTHER_SLOTS'));
        }
        $priceSlab->assignValues($post);
        if (!$priceSlab->saveSlab($post['prislab_min'], $post['prislab_max'], $post['prislab_active'])) {
            FatUtility::dieJsonError($priceSlab->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_SLOT_SAVE_SUCCESSFULLY'));
    }

    /**
     * Change Status
     */
    public function changeStatus()
    {
        $this->objPrivilege->canEditPriceSlab();
        $psId = FatApp::getPostedData('psId', FatUtility::VAR_INT, 0);
        $slabData = PriceSlab::getAttributesById($psId, ['prislab_id', 'prislab_active']);
        if ($slabData == false) {
            FatUtility::dieJsonError(Label::getLabel('LBL_INVALID_REQUEST'));
        }
        $PriceSlab = new PriceSlab($psId);
        $status = ($slabData['prislab_active'] == 1) ? 0 : 1;
        if (!$PriceSlab->changeStatus($status)) {
            FatUtility::dieJsonError($PriceSlab->getError());
        }
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_ACTION_PERFORMED_SUCCESSFULLY'));
    }

    /**
     * Get Price Slab Form
     * 
     * @param int $psId
     * @return Form
     */
    private function getForm(int $psId = 0): Form
    {
        $form = new Form('PriceSlabFrm');
        $form->addHiddenField('', 'prislab_id', $psId);
        $minField = $form->addRequiredField(Label::getLabel('LBL_Min'), 'prislab_min');
        $minField->requirements()->setIntPositive();
        $minField->requirements()->setRange(1, 9999);
        $maxField = $form->addRequiredField(Label::getLabel('LBL_max'), 'prislab_max');
        $maxField->requirements()->setIntPositive();
        $maxField->requirements()->setRange(2, 9999);
        $minField->requirements()->setCompareWith('prislab_max', 'lt', Label::getLabel('LBL_max'));
        $maxField->requirements()->setCompareWith('prislab_min', 'gt', Label::getLabel('LBL_min'));
        $fld = $form->addSelectBox(Label::getLabel('LBL_STATUS'), 'prislab_active', AppConstant::getActiveArr(), AppConstant::ACTIVE);
        $fld->requirements()->setRequired();
        $form->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save_Changes'));
        return $form;
    }

    private function getSearchForm(): Form
    {
        $frm = new Form('priceSlabSearchFrm');
        $frm->addHiddenField('', 'pagesize', FatApp::getConfig('CONF_ADMIN_PAGESIZE'))->requirements()->setInt();
        $frm->addHiddenField('', 'pageno', 1)->requirements()->setInt();
        return $frm;
    }

}
