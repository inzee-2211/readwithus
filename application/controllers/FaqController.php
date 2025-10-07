<?php

/**
 * FAQ Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class FaqController extends MyAppController
{

    /**
     * Initialize FAQ
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Render FAQs
     */
    public function index()
    {
        $srch = Faq::getSearchObject($this->siteLangId);
        $srch->addMultipleFields(['faq_id', 'faq_category',
            'IFNULL(faq_title, faq_identifier) as faq_title',
            'IFNULL(faqcat_name, faqcat_identifier) as faqcat_name', 'faq_description']);
        $srch->joinTable(FaqCategory::DB_TBL, 'INNER JOIN', 'faqcat_id = faq_category');
        $srch->joinTable(FaqCategory::DB_LANG_TBL, 'LEFT JOIN',
                'faqcatlang_faqcat_id = faqcat_id and faqcatlang_lang_id =' . $this->siteLangId);
        $srch->addCondition('faqcat_active', '=', AppConstant::YES);
        $srch->addOrder('faqcat_order');
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $data = FatApp::getDb()->fetchAll($srch->getResultSet());
        $faqs = [];
        $typeArr = [];
        foreach ($data as $val) {
            $faqs[$val['faq_category']][] = $val;
            $typeArr[$val['faq_category']] = $val['faqcat_name'];
        }
        $this->sets(['faqs' => $faqs, 'typeArr' => $typeArr]);
        $this->_template->render();
    }

}
