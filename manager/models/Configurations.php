<?php

/**
 * Admin Class is used to handle System Configuration
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class Configurations extends FatModel
{

    const DB_TBL = 'tbl_configurations';
    const DB_TBL_PREFIX = 'conf_';
    const FORM_GENERAL = 1;
    const FORM_LOCAL = 2;
    const FORM_SEO = 3;
    const FORM_OPTIONS = 4;
    const FORM_LIVE_CHAT = 5;
    const FORM_THIRD_PARTY = 6;
    const FORM_EMAIL = 7;
    const FORM_MEDIA = 8;
    const FORM_SERVER = 9;
    const FORM_SECURITY = 10;
    const MODERATE = 0;
    const HIGH = 1;

    /**
     * Initialize Configurations
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get Lang Type Form
     * 
     * @return array
     */
    public static function getLangTypeForms(): array
    {
        return [
            Configurations::FORM_GENERAL,
            Configurations::FORM_MEDIA,
        ];
    }

    /**
     * Get Security Settings
     * 
     * @return array
     */
    public static function getSecuritySettings(): array
    {
        return [
            Configurations::MODERATE => Label::getLabel('LBL_MODERATE'),
            Configurations::HIGH => Label::getLabel('LBL_High')
        ];
    }

    /**
     * Get Setting Tabs
     * 
     * @return array
     */
    public static function getTabs(): array
    {
        $configurationArr = [
            Configurations::FORM_GENERAL => Label::getLabel('MSG_General'),
            Configurations::FORM_LOCAL => Label::getLabel('MSG_Local'),
            Configurations::FORM_SEO => Label::getLabel('MSG_Seo'),
            Configurations::FORM_OPTIONS => Label::getLabel('MSG_Options'),
            Configurations::FORM_LIVE_CHAT => Label::getLabel('MSG_Live_Chat'),
            Configurations::FORM_THIRD_PARTY => Label::getLabel('MSG_THIRD_PARTY'),
            Configurations::FORM_EMAIL => Label::getLabel('MSG_Email'),
            Configurations::FORM_MEDIA => Label::getLabel('MSG_Media'),
            Configurations::FORM_SERVER => Label::getLabel('MSG_Server'),
            Configurations::FORM_SECURITY => Label::getLabel('MSG_SECURITY')
        ];

        return $configurationArr;
    }

    /**
     * Get Configurations
     * 
     * @param array $configs
     * @return array
     */
    public static function getConfigurations(array $configs = []): array
    {
        $srch = new SearchBase(static::DB_TBL, 'conf');
        $configs && $srch->addCondition('conf_name', 'IN', $configs);
        $srch->addMultipleFields(['UPPER(conf_name) conf_name', 'conf_val']);
        $rs = $srch->getResultSet();
        return FatApp::getDb()->fetchAllAssoc($rs);
    }

    /**
     * Update Configurations
     * 
     * @param array $data
     * @return bool
     */
    public function update(array $data): bool
    {
        foreach ($data as $key => $val) {
            $assignValues = ['conf_name' => $key, 'conf_val' => $val];
            FatApp::getDb()->insertFromArray(static::DB_TBL, $assignValues, false, [], $assignValues);
        }
        return true;
    }

    /**
     * Update Configurations
     * 
     * @param string $key
     * @param type $value
     * @return bool
     */
    public function updateConf(string $key, $value): bool
    {
        $assignValues = ['conf_name' => $key, 'conf_val' => $value];
        if (!FatApp::getDb()->insertFromArray(Configurations::DB_TBL, $assignValues, false, [], $assignValues)) {
            $this->error = FatApp::getDb()->getError();
            return false;
        }
        return true;
    }

}
