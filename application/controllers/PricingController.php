<?php
defined('SYSTEM_INIT') or die('Invalid Usage');

class PricingController extends MyAppController
{
    public function index()
    {
        // You can later fetch from DB (tbl_plans) – for now a static seed
        $plans = [
            [
                'slug' => 'starter',
                'name' => 'Starter',
                'tag'  => 'Best for trying us out',
                'price_month' => 9,
                'price_year'  => 84, // 12 * 9 - discount
                'features' => [
                    'Access to 1 course per month',
                    'AI practice quizzes (basic)',
                    'Community Q&A',
                    'Email support',
                ],
                'cta_url' => MyUtility::makeUrl('Courses'),
                'is_popular' => false,
            ],
            [
                'slug' => 'pro',
                'name' => 'Pro',
                'tag'  => 'Most Popular',
                'price_month' => 19,
                'price_year'  => 180,
                'features' => [
                    'Unlimited courses',
                    'AI practice quizzes (advanced)',
                    'Assignments & feedback',
                    'Priority support',
                ],
                'cta_url' => MyUtility::makeUrl('Courses'),
                'is_popular' => true,
            ],
            [
                'slug' => 'teams',
                'name' => 'Teams',
                'tag'  => 'For schools & groups',
                'price_month' => 49,
                'price_year'  => 480,
                'features' => [
                    'All Pro features',
                    'Admin dashboard',
                    'Progress analytics',
                    'Dedicated success manager',
                ],
                'cta_url' => MyUtility::makeUrl('Contact'),
                'is_popular' => false,
            ],
        ];

        $this->set('plans', $plans);
        $this->set('currency', FatApp::getConfig('CONF_SITE_CURRENCY_CODE', FatUtility::VAR_STRING, $this->siteCurrency['currency_code']));
        $this->set('title', Label::getLabel('LBL_PRICING', $this->siteLangId));
        $this->_template->render(true, true, 'pricing/index.php');
    }
}
