<?php

/**
 * This class is used to handle Flash Card
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Flashcard extends MyAppModel
{

    const DB_TBL = 'tbl_flashcards';
    const DB_TBL_PREFIX = 'flashcard_';

    /* Card Types */
    const TYPE_OTHER = 0;
    const TYPE_LESSON = 1;
    const TYPE_GCLASS = 2;
    /* View Types */
    const VIEW_FULL = 1;
    const VIEW_SHORT = 2;

    /**
     * Initialize Flashcard
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'flashcard_id', $id);
    }

    /**
     * Save Record
     * 
     * @return bool
     */
    public function save(): bool
    {
        if ($this->mainTableRecordId == 0) {
            $this->setFldValue('flashcard_addedon', date('Y-m-d H:i:s'));
        }
        return parent::save();
    }

    /**
     * Get Form
     * 
     * @param int $langId
     * @return Form
     */
    public static function getForm(int $langId): Form
    {
        $frm = new Form('flashcardFrm');
        $frm->addHiddenField('', 'flashcard_id', 0)->requirements()->setInt();
        $frm->addHiddenField('', 'flashcard_type', 0)->requirements()->setInt();
        $frm->addHiddenField('', 'flashcard_type_id', 0)->requirements()->setInt();
        $frm->addSelectBox(Label::getLabel('LBL_Language'), 'flashcard_tlang_id', TeachLanguage::getAllLangs($langId))->requirements()->setRequired();
        $frm->addTextBox(Label::getLabel('LBL_Title'), 'flashcard_title')->requirements()->setRequired();
        $frm->addTextArea(Label::getLabel('LBL_Detail'), 'flashcard_detail')->requirements()->setRequired();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        $frm->addButton('', 'btn_cancel', Label::getLabel('LBL_Cancel'));
        return $frm;
    }

    /**
     * Get Search Form
     * 
     * @param int $langId
     * @return Form
     */
    public static function getSearchForm(int $langId): Form
    {
        $frm = new Form('searchFlashcardFrm');
        $frm->addTextBox(Label::getLabel('LBL_KEYWORD'), 'keyword', '', ['placeholder' => Label::getLabel('LBL_Keyword')]);
        $frm->addSelectBox(Label::getLabel('LBL_LANGUAGE'), 'flashcard_tlang_id', TeachLanguage::getAllLangs($langId));
        $frm->addHiddenField(Label::getLabel('LBL_PAGESIZE'), 'pagesize', 10)->requirements()->setInt();
        $frm->addHiddenField(Label::getLabel('LBL_PAGENO'), 'pageno', 1)->requirements()->setInt();
        $frm->addHiddenField(Label::getLabel('LBL_VIEW'), 'view', static::VIEW_FULL)->requirements()->setInt();
        $frm->addHiddenField('', 'flashcard_type_id', '')->requirements();
        $frm->addHiddenField('', 'flashcard_type', '')->requirements();
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Search'));
        $frm->addResetButton('', 'btn_reset', Label::getLabel('LBL_Clear'));
        return $frm;
    }

}
