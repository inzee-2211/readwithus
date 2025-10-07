<?php

/**
 * PWA Controller is used for Progressive Web Apps handling
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class PwaController extends AdminBaseController
{

    /**
     * Initialize PWA
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
        $this->objPrivilege->canViewPwaSettings();
    }

    /**
     * Render PWA Form
     */
    public function index()
    {
        $frm = $this->getForm();
        $record = Configurations::getConfigurations(['CONF_ENABLE_PWA', 'CONF_PWA_SETTINGS']);
        if (!empty($record['CONF_PWA_SETTINGS'])) {
            $data = [
                'pwa_settings' => json_decode($record['CONF_PWA_SETTINGS'], true),
                'CONF_ENABLE_PWA' => $record['CONF_ENABLE_PWA']
            ];
            $frm->fill($data);
        }
        $file = new Afile(Afile::TYPE_PWA_APP_ICON);
        $iconData = $file->getFile();
        $file = new Afile(Afile::TYPE_PWA_SPLASH_ICON);
        $splashIconData = $file->getFile();
        $this->sets([
            'canEdit' => $this->objPrivilege->canEditPwaSettings(true),
            'frm' => $frm,
            'iconData' => $iconData,
            'splashIconData' => $splashIconData
        ]);
        $this->_template->render();
    }

    /**
     * Setup PWA 
     * 
     * @return type
     */
    public function setup()
    {
        $this->objPrivilege->canEditPwaSettings();
        $frm = $this->getForm();
        if (!$post = $frm->getFormDataFromArray(FatApp::getPostedData())) {
            FatUtility::dieJsonError(current($frm->getValidationErrors()));
        }
        FatApp::getDb()->startTransaction();
        if (!empty($_FILES['icon']['name'])) {
            $file = new Afile(Afile::TYPE_PWA_APP_ICON);
            if (!$file->saveFile($_FILES['icon'], 0, true)) {
                FatApp::getDb()->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }
        if (!empty($_FILES['splash_icon']['name'])) {
            $file = new Afile(Afile::TYPE_PWA_SPLASH_ICON);
            if (!$file->saveFile($_FILES['splash_icon'], 0, true)) {
                FatApp::getDb()->rollbackTransaction();
                FatUtility::dieJsonError($file->getError());
            }
        }
        $pwaSettings = json_encode($post['pwa_settings']);
        $configurations = new Configurations();
        if (!$configurations->update(['CONF_PWA_SETTINGS' => $pwaSettings, 'CONF_ENABLE_PWA' => $post['CONF_ENABLE_PWA']])) {
            FatApp::getDb()->rollbackTransaction();
            FatUtility::dieJsonError(FatApp::getDb()->getError());
        }
        FatApp::getDb()->commitTransaction();
        FatUtility::dieJsonSuccess(Label::getLabel('LBL_PWA_SETTINGS_UPDATED'));
    }

    /**
     * Get PWA Form
     * 
     * @return Form
     */
    private function getForm(): Form
    {
        $frm = new Form('pwaFrm');
        $frm->addCheckBox(Label::getLabel('PWALBL_Enable_PWA'), 'CONF_ENABLE_PWA', 1, [], false, 0);
        $fld = $frm->addRequiredField(Label::getLabel('PWALBL_App_Name'), 'pwa_settings[name]');
        $fld->requirements()->setLength(1, 50);
        $fld = $frm->addRequiredField(Label::getLabel('PWALBL_App_Short_Name'), 'pwa_settings[short_name]');
        $fld->requirements()->setLength(1, 15);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_APP_SHORT_NAME') . '</small>';
        $fld = $frm->addTextBox(Label::getLabel('PWALBL_Description'), 'pwa_settings[description]');
        $fld->requirements()->setLength(1, 200);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Description') . '</small>';
        $fld = $frm->addFileUpload(Label::getLabel('PWALBL_App_Icon'), 'icon', ['accept' => 'image/png']);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_App_Icon') . '</small>';
        $fld->attachField($frm->addHTML('', 'icon_img', ''));
        $fld = $frm->addFileUpload(Label::getLabel('PWALBL_Splash_Icon'), 'splash_icon', ['accept' => 'image/png']);
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Spash_Icon') . '</small>';
        $fld->attachField($frm->addHTML('', 'splash_icon_img', ''));
        $frm->addRequiredField(Label::getLabel('PWALBL_Background_Color'), 'pwa_settings[background_color]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Background_color') . '</small>';
        $frm->addRequiredField(Label::getLabel('PWALBL_Theme_Color'), 'pwa_settings[theme_color]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Theme_Color') . '</small>';
        $frm->addRequiredField(Label::getLabel('PWALBL_Start_Page'), 'pwa_settings[start_url]')
                ->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Start_Page') . '</small>';
        $orientation = ['portrait' => Label::getLabel('PWALBL_PORTRAIT'), 'landscape' => Label::getLabel('PWALBL_LANDSCAPE')];
        $fld = $frm->addSelectBox(Label::getLabel('PWALBL_Orientation'), 'pwa_settings[orientation]', $orientation, '', [], '');
        $fld->requirements()->setRequired();
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_orientation') . '</small>';
        $fld = $frm->addSelectBox(Label::getLabel('PWALBL_Display'), 'pwa_settings[display]', static::getDisplaySize(), '', [], '');
        $fld->requirements()->setRequired();
        $fld->htmlAfterField = '<small>' . Label::getLabel('HTMLAFTER_PWA_Display') . '</small>';
        $frm->addSubmitButton('', 'btn_submit', Label::getLabel('LBL_Save'));
        return $frm;
    }

    /**
     * Get Screen Sizes
     * 
     * @return array
     */
    public static function getDisplaySize(): array
    {
        return [
            'fullscreen' => Label::getLabel('PWALBL_FULL_SCREEN'),
            'standalone' => Label::getLabel('PWALBL_STANDALONE'),
            'minimal-ui' => Label::getLabel('PWALBL_MINIMAL_UI'),
            'browser' => Label::getLabel('PWALBL_BROWSER')
        ];
    }

}
