<?php

/**
 * Image Controller
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class ImageController extends MyAppController
{

    /**
     * Initialize Image
     * 
     * @param string $action
     */
    public function __construct(string $action)
    {
        parent::__construct($action);
    }

    /**
     * Show Image
     * 
     * @param int $fileType
     * @param int $recordId
     * @param string $size
     */
    public function show($fileType, $recordId = 0, $size = '', $langId = 0)
    {
        
        $langId = ($langId < 1) ? MyUtility::getSiteLangId() : $langId;
        $file = new Afile(FatUtility::int($fileType), $langId);
        $file->showByRecordId($size, FatUtility::int($recordId));
    }

    /**
     * Show By Id
     *
     * @param int $fileId
     * @param string $size
     */
    public function showById($fileId, $size = '')
    {
        $file = new Afile(0);
        $file->showByFileId(FatUtility::int($fileId), $size);
    }

    /**
     * Download File
     * 
     * @param int $fileType
     * @param int $recordId
     * @param int $subRecordId
     */
    public function download($fileType, $recordId)
    {
        $file = new Afile(FatUtility::int($fileType), MyUtility::getSiteLangId());
        $file->downloadByRecordId(FatUtility::int($recordId));
    }

    /**
     * Download File By Id
     * 
     * @param int $fileId
     */
    public function downloadById($fileId)
    {
        $file = new Afile(0);
        $file->downloadById(FatUtility::int($fileId));
    }

    /**
     * Render Editor Image
     * 
     * @param string $fileNamewithPath
     */
    public function editorImage($fileNamewithPath)
    {
        /**
         * We have to use new method
         */
        Afile::displayOriginalImage('editor/' . $fileNamewithPath);
    }

    /**
     * Show Image
     * 
     * @param int $fileType
     * @param int $recordId
     */
    public function showVideo($fileType, $recordId, $langId = 0)
    {
        $file = new Afile(FatUtility::int($fileType), $langId);
        $file->showVideo(FatUtility::int($recordId));
    }

    /**
     * Show Image
     * 
     * @param int $fileType
     * @param int $recordId
     */
    public function showPdf($fileType, $recordId, $langId = 0)
    {
        $file = new Afile(FatUtility::int($fileType), $langId);
        $file->showPdf(FatUtility::int($recordId));
    }
}
