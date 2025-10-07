<?php

/**
 * PWA Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class PwaController extends MyAppController
{

    /**
     * Initialize PWA
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    public function index()
    {
        $pwaSettings = FatApp::getConfig('CONF_PWA_SETTINGS');
        $pwaManifest = [];
        if (!empty($pwaSettings)) {
            $pwaManifest = json_decode(FatApp::getConfig('CONF_PWA_SETTINGS'), true);
        }
        $pwaManifest['icons'] = [
            [
                "sizes" => "144x144", "type" => "image/png",
                "src" => MyUtility::makeUrl('Image', 'show', [Afile::TYPE_PWA_APP_ICON, 0, Afile::SIZE_LARGE])
            ],
            [
                "sizes" => "512x512", "type" => "image/png",
                "src" => MyUtility::makeUrl('Image', 'show', [Afile::TYPE_PWA_SPLASH_ICON, 0, Afile::SIZE_LARGE])
            ]
        ];
        unset($pwaManifest['offline_page']);
        die(stripslashes(json_encode($pwaManifest)));
    }

}
