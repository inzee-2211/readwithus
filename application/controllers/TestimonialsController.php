<?php

/**
 * Frontend Testimonials Controller
 *
 * @package YoCoach
 */
class TestimonialsController extends MyAppController
{
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * List all active testimonials (frontend page)
     */
    public function index()
    {
        $langId = $this->siteLangId;

        $srch = Testimonial::getSearchObject($langId, false);
        $srch->addCondition('testimonial_active', '=', AppConstant::YES);

        // If you have a deleted flag, uncomment this:
        // $srch->addCondition(Testimonial::tblFld('deleted'), '=', AppConstant::NO);

        $srch->addMultipleFields([
            't.testimonial_id',
            't.testimonial_user_name',
            't.testimonial_identifier',
            't_l.testimonial_text',
            't.testimonial_added_on',
        ]);
        $srch->addOrder('testimonial_added_on', 'DESC');

        $db = FatApp::getDb();
        $testimonials = $db->fetchAll($srch->getResultSet(), 'testimonial_id');

        $this->set('testimonials', $testimonials);
        $this->set('siteLangId', $langId);

        $this->_template->render();
    }
}
