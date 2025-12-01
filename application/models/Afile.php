<?php

use Aws\S3\S3Client;

/**
 * This class is used to handle all types of media uploads in application
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class Afile extends FatModel
{

    const DB_TBL = 'tbl_attached_files';
    const DB_TBL_PREFIX = 'file_';
    const TYPE_TEACHER_APPROVAL_IMAGE = 1;
    const TYPE_TEACHER_APPROVAL_PROOF = 2;
    const TYPE_PAYIN = 3;
    const TYPE_USER_PROFILE_IMAGE = 4;
    const TYPE_FRONT_LOGO = 6;
    const TYPE_BANNER = 9;
    const TYPE_ADMIN_LOGO = 10;
    const TYPE_EMAIL_LOGO = 11;
    const TYPE_FAVICON = 12;
    const TYPE_PAYMENT_PAGE_LOGO = 14;
    const TYPE_ADMIN_PROFILE_IMAGE = 15;
    const TYPE_APPLE_TOUCH_ICON = 18;
    const TYPE_BLOG_POST_IMAGE = 23;
    const TYPE_CATEGORY_COLLECTION_BG_IMAGE = 25;
    const TYPE_TESTIMONIAL_IMAGE = 26;
    const TYPE_CPAGE_BACKGROUND_IMAGE = 27;
    const TYPE_USER_QUALIFICATION_FILE = 30;
    const TYPE_LESSON_PACKAGE_IMAGE = 32;
    const TYPE_LESSON_PLAN_FILE = 33;
    const TYPE_LESSON_PLAN_IMAGE = 34;
    const TYPE_BLOG_CONTRIBUTION = 36;
    const TYPE_BANNER_SECOND_IMAGE = 38;
    const TYPE_TEACHING_LANGUAGES = 42;
    const TYPE_BLOG_PAGE_IMAGE = 43;
    const TYPE_LESSON_PAGE_IMAGE = 44;
    const TYPE_PWA_APP_ICON = 46;
    const TYPE_PWA_SPLASH_ICON = 47;
    const TYPE_OPENGRAPH_IMAGE = 48;
    const TYPE_HOME_BANNER_DESKTOP = 49;
    const TYPE_HOME_BANNER_MOBILE = 50;
    const TYPE_HOME_BANNER_IPAD = 51;
    const TYPE_APPLY_TO_TEACH_BANNER = 52;
    const TYPE_MESSAGE_ATTACHMENT = 53;
    const TYPE_ORDER_PAY_RECEIPT = 54;
    const TYPE_GROUP_CLASS_BANNER = 55;
    const TYPE_CERTIFICATE_BACKGROUND_IMAGE = 56;
    const TYPE_COURSE_IMAGE = 57;
    const TYPE_COURSE_PREVIEW_VIDEO = 58;
    const TYPE_CERTIFICATE_IMAGE = 59;
    const TYPE_CERTIFICATE_PDF = 60;
    const TYPE_CERTIFICATE_LOGO = 61;
    const TYPE_COURSE_REQUEST_IMAGE = 62;
    const TYPE_COURSE_REQUEST_PREVIEW_VIDEO = 63;
    const TYPE_LESSON_QUESTIONS_FILE = 64;

    /* Image Sizes */
    const SIZE_SMALL = 'SMALL';
    const SIZE_MEDIUM = 'MEDIUM';
    const SIZE_LARGE = 'LARGE';
    const SIZE_ORIGINAL = 'ORIGINAL';

    private $type;
    private $langId;
    private $fileData;

    /**
     * Initialize Attached file
     * 
     * @param int $type             File type represent to type of getting|uploading media, eg. TYPE_ADMIN_LOGO, TYPE_FRONT_LOGO
     * @param int $langId           Language id is language for which file getting|uploading file
     */
    public function __construct(int $type, int $langId = 0)
    {
        parent::__construct();
        $this->type = $type;
        $this->langId = $langId;
        $this->fileData = [];
    }

    /**
     * Get By Id
     *
     * @param int $fileId           It is a primary key of table Afile::DB_TBL
     * @return null|array           It will return a row from table Afile::DB_TBL, in case of failure it returns null
     */
    public static function getById(int $fileId)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('file_id', '=', $fileId);
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get By Type
     *
     * @param int $recordId         It is primary key of entity for which file getting|uploading
     * @return null|array           It will return a row from table Afile::DB_TBL, in case of failure it returns null
     */
    public function getFilesByType(int $recordId = 0)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('file_type', '=', $this->type);
        $srch->addCondition('file_record_id', '=', $recordId);
        $srch->addOrder('file_order', 'ASC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Get Single File
     * 
     * @param int $recordId         It is primary key of entity for which file getting|uploading
     * @param bool $universal       Pass it true, If getting|uploading file for all languages
     * @return null|array           It will return a row from table Afile::DB_TBL, in case of failure it returns null
     */
    public function getFile(int $recordId = 0, bool $universal = true)
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('file_type', '=', $this->type);
        $srch->addCondition('file_record_id', '=', $recordId);
        $cnd = $srch->addCondition('file_lang_id', '=', $this->langId);
        if ($universal) {
            $cnd->attachCondition('file_lang_id', '=', 0);
            $srch->addOrder('file_lang_id', 'DESC');
        }
        $srch->addOrder('file_order', 'ASC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get Multiple Files
     * 
     * @param int $recordId         It is primary key of entity for which getting file
     * @param bool $universal       Pass it true, If getting file for all languages
     * @return array                It will return array of rows from table Afile::DB_TBL, in case of failure it return empty array
     */
    public function getFiles(int $recordId = 0, bool $universal = true): array
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('file_type', '=', $this->type);
        $srch->addCondition('file_record_id', '=', $recordId);
        $cnd = $srch->addCondition('file_lang_id', '=', $this->langId);
        if ($universal) {
            $cnd->attachCondition('file_lang_id', '=', 0);
            $srch->addOrder('file_lang_id', 'DESC');
        }
        $srch->addOrder('file_order', 'ASC');
        $srch->doNotCalculateRecords();
        return FatApp::getDb()->fetchAll($srch->getResultSet());
    }

    /**
     * Save File
     * 
     * @param array $file           It represent a row(array) of table Afile::DB_TBL
     * @param int $recordId         It is primary key of entity for which uploading file 
     * @param bool $unique          Pass it true if entity has unique file, It will delete older i exists
     * @return bool                 Return true on success and false on failure
     */
    public function saveFile(array $file, int $recordId, bool $unique = false): bool
    {
        
        if (!$this->validateUploadedFile($file)) {
            
            return false;
        }
        $uploadPath = CONF_UPLOADS_PATH;
       
        $filePath = date('Y') . '/' . date('m') . '/';
        if (!file_exists($uploadPath . $filePath)) {
            mkdir($uploadPath . $filePath, 0777, true);
        }
       
        $fileName = preg_replace('/[^a-zA-Z0-9.]/', '', $file['name']);
        while (file_exists($uploadPath . $filePath . $fileName)) {
            $fileName = time() . '-' . $fileName;
        }
        $filePath = $filePath . $fileName;
 
        if (!move_uploaded_file($file['tmp_name'], $uploadPath . $filePath)) {
           
            $this->error = Label::getLabel('FILE_COULD_NOT_SAVE_FILE');
            return false;
        }
 
        if ($unique) {
            $oldFile = $this->getFile($recordId, false);
        }
      
        $this->fileData = [
            'file_type' => $this->type,
            'file_lang_id' => $this->langId,
            'file_record_id' => $recordId,
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_order' => 0,
            'file_added' => date('Y-m-d H:i:s')
        ];
      
         
        $db = FatApp::getDb();
       
        $record = new TableRecord(static::DB_TBL);
        $record->assignValues($this->fileData);
       
        if (!$record->addNew()) {
            $this->error = $db->getError();
            return false;
        }
 
        $this->fileData['file_id'] = $record->getId();
       
        $fileId = $record->getId();
 
        if ($unique) {

            $stmt = [
                'vals' => [$this->type, $this->langId, $recordId, $this->fileData['file_id']],
                'smt' => 'file_type = ? AND file_lang_id = ? AND file_record_id = ? AND file_id != ?'
            ];
             
            $db->deleteRecords(static::DB_TBL, $stmt);
        }
        // echo '<pre>';print_r($this->fileData); 
        // echo $this->fileData['file_id'];die;
        if (
                MyUtility::isDemoUrl() == false &&
                $unique && !empty($oldFile['file_path']) &&
                file_exists(CONF_UPLOADS_PATH . $oldFile['file_path'])
        ) {
            unlink(CONF_UPLOADS_PATH . $oldFile['file_path']);
        }
        $fileId = (!empty($oldFile['file_id'])) ? $oldFile['file_id'] : 0;
       // echo $fileId;die;
        $this->clearCache($recordId, $fileId);
        return true;
    }

    /**
     * Validate Uploaded File Content
     * 
     * @param array $file           It represent a row(array) of table Afile::DB_TBL
     * @return bool                 Return true on success and false on failure
     */
    private function validateUploadedFile(array $file): bool
    {
        if (empty($file['name'])) {
            $this->error = Label::getLabel('FILE_INVALID_FILE_NAME');
            return false;
        }
        if (empty($file['type'])) {
            $this->error = Label::getLabel('FILE_INVALID_FILE_TYPE');
            return false;
        }
        if (empty($file['tmp_name'])) {
            $this->error = Label::getLabel('FILE_FILE_NOT_UPLOADED');
            return false;
        }
        if (empty($file['size'])) {
            $this->error = Label::getLabel('FILE_EMPTY_FILE_UPLOADED');
            return false;
        }
        $allowedSize = static::getAllowedUploadSize($this->type);
        if ($file["size"] > $allowedSize) {
            $label = Label::getLabel('LBL_FILE_SIZE_SHOULD_BE_LESS_THEN_{size}_MB');
            $this->error = str_replace('{size}', MyUtility::convertBitesToMb($allowedSize), $label);
            return false;
        }
        $fileExt = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
//         $allowedExts = static::getAllowedExts($this->type);
// echo 'Allowed Extensions: ';
// print_r($allowedExts);
// die();
        if (!in_array($fileExt, static::getAllowedExts($this->type))) {
            $this->error = Label::getLabel('FILE_INVALID_FILE_EXTENSION');
            return false;
        }
        if (static::getContentType($fileExt) != mime_content_type($file['tmp_name'])) {
            $this->error = Label::getLabel('FILE_INVALID_FILE_CONTENT');
            return false;
        }
        return true;
    }

    /**
     * Return current saved file data
     * 
     * @return array $fileData      It represent a row(array) of table Afile::DB_TBL
     */
    public function getSavedFile(): array
    {
        return $this->fileData;
    }

    /**
     * Show Image By Record Id
     * 
     * @param string $size          Size of image to be displayed eg. SIZE_SMALL,SIZE_MEDIUM
     * @param int $recordId         It is primary key of entity for which getting file
     */
    public function showByRecordId(string $size, int $recordId = 0)
    {
        $file = $this->getFile($recordId);
        if (empty($size) || strtoupper($size) == 'ORIGINAL') {
            $this->showOriginalImage($file);
        } else {
            $this->showImage($file, $size);
        }
    }

    /**
     * Show Image By File Id
     * 
     * @param int $fileId           It is a primary key of table Afile::DB_TBL
     * @param string $size          Size of image to be displayed eg. SIZE_SMALL,SIZE_MEDIUM
     */
    public function showByFileId(int $fileId, string $size)
    {
        $file = static::getById($fileId);
        if (empty($size) || strtoupper($size) == 'ORIGINAL') {
            $this->showOriginalImage($file);
        } else {
            if (empty($this->type) && !empty($file['file_type'])) {
                $this->type = $file['file_type'];
            }
            $this->showImage($file, $size);
        }
    }

    /**
     * Show Image
     * 
     * @param array $file       It represent a row(array) of table Afile::DB_TBL
     * @param string $size      Size of image to be displayed eg. SIZE_SMALL,SIZE_MEDIUM
     */
    private function showImage(array $file = null, string $size = null)
    {
        
        ob_end_clean();
        ini_set('memory_limit', -1);
        $sizes = $this->getImageSizes($size);
        if (empty($file) || !file_exists(CONF_UPLOADS_PATH . $file['file_path'])) {
            $img = new ImageResize(CONF_INSTALLATION_PATH . 'public/images/noimage.jpg');
            $img->setResizeMethod(ImageResize::IMG_RESIZE_RESET_DIMENSIONS);
            $img->setMaxDimensions($sizes[0], $sizes[1]);
            $img->displayImage();
            exit;
        }
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        $headers = FatApp::getApacheRequestHeaders();
        if (strtotime($headers['If-Modified-Since'] ?? '') == filemtime($filePath)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 200);
        header("Expires: " . date('r', strtotime("+30 Day")));
        header('Cache-Control: public');
        header("Pragma: public");

        $img = new ImageResize($filePath);
        $img->setResizeMethod(ImageResize::IMG_RESIZE_RESET_DIMENSIONS);
        $img->setMaxDimensions($sizes[0], $sizes[1]);
        if (CONF_USE_FAT_CACHE) {
            ob_start();
            $img->displayImage();
            $imgData = ob_get_clean();
            $imgExt = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
            FatCache::set($_SERVER['REQUEST_URI'], $imgData, '.' . $imgExt);
            echo $imgData;
        } else {
            $img->displayImage();
        }
      
    }

    /**
     * Show Original Image
     * 
     * @param array $file       It represent a row(array) of table Afile::DB_TBL
     * @return void
     */
    private function showOriginalImage(array $file = null)
    {
        
        ob_end_clean();
        ini_set('memory_limit', -1);
        if (empty($file) || !file_exists(CONF_UPLOADS_PATH . $file['file_path'])) {
            $img = new ImageResize(CONF_INSTALLATION_PATH . 'public/images/noimage.jpg');
            $img->setResizeMethod(ImageResize::IMG_RESIZE_RESET_DIMENSIONS);
            $img->displayImage(100);
            exit;
        }
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        $imgExt = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
        header("Content-Type: " . static::getContentType($imgExt));
        $headers = FatApp::getApacheRequestHeaders();
        if (strtotime($headers['If-Modified-Since'] ?? '') == filemtime($filePath)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 200);
        header("Expires: " . date('r', strtotime("+30 Day")));
        header('Cache-Control: public');
        header("Pragma: public");
        $imgData = file_get_contents($filePath);
        if (CONF_USE_FAT_CACHE) {
            FatCache::set($_SERVER['REQUEST_URI'], $imgData, '.' . $imgExt);
        }
        echo $imgData;
    }

    /**
     * Download By Record Id
     * 
     * @param int $recordId         It is primary key of entity for which getting file
     * @param bool $universal       Pass it true, If getting file for all languages
     */
    public function downloadByRecordId(int $recordId = 0, bool $universal = true)
    {
        $file = $this->getFile($recordId, $universal);
        $this->downloadFile($file);
    }

    /**
     * Download By File Id
     * 
     * @param int $fileId           It is a primary key of table Afile::DB_TBL
     */
    public function downloadById(int $fileId)
    {
        $file = static::getById($fileId);
        $this->downloadFile($file);
    }

    /**
     * Download File
     *
     * @param array $file           It represent a row(array) of table Afile::DB_TBL
     */
    private function downloadFile($file)
    {
        if (empty($file) || !file_exists(CONF_UPLOADS_PATH . $file['file_path'])) {
            FatUtility::exitWithErrorCode(404);
        }
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        if (!$contentType = mime_content_type($filePath)) {
            FatUtility::exitWithErrorCode(500);
        }
        ob_end_clean();
        header('Expires: 0');
        header('Pragma: public');
        header("Content-Type: " . $contentType);
        header('Content-Description: File Transfer');
        header('Content-Length: ' . filesize($filePath));
        header('Content-Disposition: attachment; filename="' . $file['file_name'] . '"');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        echo file_get_contents($filePath);
    }

    /**
     * Remove (AND|OR) unlink File
     * 
     * @param int $recordId         It is primary key of entity for which getting file
     * @param type $unlink          Pass it true, If need to remove physical file
     * @return bool                 Return true on success and false on failure
     */
    public function removeFile(int $recordId, $unlink = false): bool
    {
        $file = $this->getFile($recordId);
        if (empty($file) || !file_exists(CONF_UPLOADS_PATH . $file['file_path'])) {
            $this->error = Label::getLabel('LBL_FILE_NOT_FOUND');
            return false;
        }
        return $this->remove($file, $unlink);
    }

    /**
     * Remove By File Id
     *
     * @param int $fileId           It is a primary key of table Afile::DB_TBL
     * @param bool $unlink          Pass it true, If need to remove physical file
     * @return bool                 Return true on success and false on failure
     */
    public function removeById(int $fileId, bool $unlink = false): bool
    {
        $file = static::getById($fileId);
        if (empty($file)) {
            return true;
        }
        $this->type = $file['file_type'];
        return $this->remove($file, $unlink);
    }

    /**
     * Remove File
     *
     * @param array $file           It represent a row(array) of table Afile::DB_TBL
     * @param bool $unlink          Pass it true, If need to remove physical file
     * @return bool                 Return true on success and false on failure
     */
    private function remove(array $file, bool $unlink = false): bool
    {
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        $db = FatApp::getDb();
        $deleteStmt = ['smt' => 'file_id = ?', 'vals' => [$file['file_id']]];
        if (!$db->startTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        if (!$db->deleteRecords(static::DB_TBL, $deleteStmt)) {
            $this->error = $db->getError();
            $db->rollbackTransaction();
            return false;
        }
        if (!MyUtility::isDemoUrl() && $unlink && file_exists($filePath) && !unlink($filePath)) {
            $this->error = Label::getLabel('LBL_NOT_ABLE_TO_DELETE_FILE');
            $db->rollbackTransaction();
            return false;
        }
        if (!$db->commitTransaction()) {
            $this->error = $db->getError();
            return false;
        }
        $this->clearCache($file['file_record_id'], $file['file_id']);
        return true;
    }

    /**
     * Clear Cache Data
     * 
     * @param int $recordId         It is primary key of entity for which getting file
     * @param int $fileId           It is a primary key of table Afile::DB_TBL
     */
    private function clearCache(int $recordId, int $fileId = 0)
    {
        FatCache::delete(MyUtility::makeUrl('Image', 'show', [$this->type, $recordId]));
        FatCache::delete(MyUtility::makeUrl('Image', 'show', [$this->type, $recordId, static::SIZE_ORIGINAL]));
        if (!empty($fileId)) {
            FatCache::delete(MyUtility::makeUrl('Image', 'showById', [$fileId]));
            FatCache::delete(MyUtility::makeUrl('Image', 'showById', [$fileId, static::SIZE_ORIGINAL]));
        }
        $sizes = array_keys($this->getImageSizes());
        foreach ($sizes as $size) {
            FatCache::delete(MyUtility::makeUrl('Image', 'show', [$this->type, $recordId, $size]));
            if (!empty($fileId)) {
                FatCache::delete(MyUtility::makeUrl('Image', 'showById', [$fileId, $size]));
            }
        }
    }

    /**
     * Register S3 Client for Stream upload|download
     * 
     * @return type
     */
    public static function registerS3ClientStream()
    {
        if (strpos(CONF_UPLOADS_PATH, 's3://') === false) {
            return;
        }
        if (!defined('S3_KEY')) {
            trigger_error('S3 Settings not found.', E_USER_ERROR);
        }
        $client = S3Client::factory([
                    'version' => 'latest',
                    'region' => AWS_S3_REGION,
                    'credentials' => [
                        'key' => AWS_S3_KEY,
                        'secret' => AWS_S3_SECRET
                    ]
        ]);
        $client->registerStreamWrapper();
    }

    /**
     * Get Content Type
     * 
     * @param string $ext           File Extension
     * @return string               It return file content type
     */
    private static function getContentType(string $ext): string
    {

        switch ($ext) {
            case 'png':
                return 'image/png';
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'gif':
                return 'image/gif';
            case 'svg':
                return 'image/svg+xml';
            case 'txt':
                return 'text/plain';
            case 'pdf':
                return 'application/pdf';
            case 'doc':
                return 'application/msword';
            case 'docx':
                return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
            case 'ico':
                return 'image/vnd.microsoft.icon';
            case 'zip':
                return 'application/zip';
            case 'mp4':
                return 'video/mp4';
            default:
                return '';
        }
    }

    /**
     * Get Allowed Extension
     * 
     * @param int $type             File type represent to type of getting|uploading media, eg. TYPE_ADMIN_LOGO, TYPE_FRONT_LOGO   
     * @return array                Return array of allowed File Extensions or given file type
     */
    public static function getAllowedExts(int $type): array
    {
        switch ($type) {
            case static::TYPE_USER_PROFILE_IMAGE:
            case static::TYPE_ADMIN_PROFILE_IMAGE:
            case static::TYPE_TEACHER_APPROVAL_IMAGE:
                return ['png', 'jpg', 'jpeg', 'gif', 'bmp'];
            case static::TYPE_BLOG_POST_IMAGE:
            case static::TYPE_LESSON_PAGE_IMAGE:
            case static::TYPE_CPAGE_BACKGROUND_IMAGE:
            case static::TYPE_BLOG_PAGE_IMAGE:
            case static::TYPE_TESTIMONIAL_IMAGE:
            case static::TYPE_APPLY_TO_TEACH_BANNER:
            case static::TYPE_CERTIFICATE_LOGO:
                return ['png', 'jpg', 'jpeg'];
            case static::TYPE_PAYIN:
            case static::TYPE_BANNER:
            case static::TYPE_FRONT_LOGO:
            case static::TYPE_ADMIN_LOGO:
            case static::TYPE_EMAIL_LOGO:
            case static::TYPE_APPLE_TOUCH_ICON:
            case static::TYPE_PAYMENT_PAGE_LOGO:
            case static::TYPE_CATEGORY_COLLECTION_BG_IMAGE:
            case static::TYPE_LESSON_PACKAGE_IMAGE:
            case static::TYPE_LESSON_PLAN_IMAGE:
            case static::TYPE_BANNER_SECOND_IMAGE:
            case static::TYPE_TEACHING_LANGUAGES:
            case static::TYPE_OPENGRAPH_IMAGE:
                return ['png', 'jpg', 'jpeg', 'gif', 'svg'];
            case static::TYPE_BLOG_CONTRIBUTION:
            case static::TYPE_USER_QUALIFICATION_FILE:
            case static::TYPE_TEACHER_APPROVAL_PROOF:
            case static::TYPE_LESSON_PLAN_FILE:
                case static::TYPE_LESSON_QUESTIONS_FILE:
            case static::TYPE_ORDER_PAY_RECEIPT:
                return ['png', 'jpg', 'jpeg', 'txt', 'doc', 'docx', 'pdf'];
            case static::TYPE_PWA_APP_ICON:
            case static::TYPE_PWA_SPLASH_ICON:
                return ['png'];
            case static::TYPE_GROUP_CLASS_BANNER:
            case static::TYPE_HOME_BANNER_DESKTOP:
            case static::TYPE_HOME_BANNER_MOBILE:
            case static::TYPE_HOME_BANNER_IPAD:
            case static::TYPE_CERTIFICATE_BACKGROUND_IMAGE:
                return ['png', 'jpg', 'jpeg'];
            case static::TYPE_FAVICON:
                return ['ico', 'png'];
            case static::TYPE_MESSAGE_ATTACHMENT:
                return ['png', 'jpeg', 'jpg', 'gif', 'pdf', 'doc', 'docx', 'zip', 'txt'];
            case static::TYPE_COURSE_IMAGE:
                return ['png', 'jpeg', 'jpg', 'gif'];
            case static::TYPE_COURSE_PREVIEW_VIDEO:
                return ['mp4'];
            default:
                return [];
        }
    }

    /**
     * Get Image Sizes
     * 
     * @param string $size              Size of image to be displayed eg. SIZE_SMALL,SIZE_MEDIUM
     * @return array [$width, $height]  Return image size array as [$width, $height]
     */
    public function getImageSizes(string $size = null): array
    {
        $arr = [
            static::TYPE_TEACHER_APPROVAL_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_TEACHER_APPROVAL_PROOF => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_PAYIN => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_USER_PROFILE_IMAGE => [
                static::SIZE_SMALL => [160, 160],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_FRONT_LOGO => [
                static::SIZE_SMALL => [100, 50],
                static::SIZE_MEDIUM => [140, 70],
                static::SIZE_LARGE => [200, 100]
            ],
            static::TYPE_APPLY_TO_TEACH_BANNER => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [2000, 900]
            ],
            static::TYPE_HOME_BANNER_DESKTOP => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [2000, 900]
            ],
            static::TYPE_HOME_BANNER_MOBILE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [800, 600]
            ],
            static::TYPE_HOME_BANNER_IPAD => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [1200, 800]
            ],
            static::TYPE_BANNER => [
                static::SIZE_SMALL => [100, 70],
                static::SIZE_MEDIUM => [500, 500],
                static::SIZE_LARGE => [800, 500]
            ],
            static::TYPE_ADMIN_LOGO => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_CERTIFICATE_LOGO => [
                static::SIZE_SMALL => [100, 33],
                static::SIZE_MEDIUM => [140, 47],
                static::SIZE_LARGE => [168, 56]
            ],
            static::TYPE_EMAIL_LOGO => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_FAVICON => [
                static::SIZE_SMALL => [16, 16],
                static::SIZE_MEDIUM => [16, 16],
                static::SIZE_LARGE => [16, 16]
            ],
            static::TYPE_PAYMENT_PAGE_LOGO => [
                static::SIZE_SMALL => [100, 37],
                static::SIZE_MEDIUM => [100, 37],
                static::SIZE_LARGE => [168, 37]
            ],
            static::TYPE_ADMIN_PROFILE_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_APPLE_TOUCH_ICON => [
                static::SIZE_SMALL => [16, 16],
                static::SIZE_MEDIUM => [16, 16],
                static::SIZE_LARGE => [16, 16]
            ],
            static::TYPE_BLOG_POST_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [600, 450],
                static::SIZE_LARGE => [950, 535]
            ],
            static::TYPE_CATEGORY_COLLECTION_BG_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_TESTIMONIAL_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [150, 150],
                static::SIZE_LARGE => [300, 300]
            ],
            static::TYPE_CPAGE_BACKGROUND_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [1400, 700]
            ],
            static::TYPE_USER_QUALIFICATION_FILE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_LESSON_PACKAGE_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_LESSON_PLAN_FILE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_LESSON_QUESTIONS_FILE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_LESSON_PLAN_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_BLOG_CONTRIBUTION => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_BANNER_SECOND_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_TEACHING_LANGUAGES => [
                static::SIZE_SMALL => [60, 60],
                static::SIZE_MEDIUM => [120, 120],
                static::SIZE_LARGE => [240, 240]
            ],
            static::TYPE_BLOG_PAGE_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [2000, 600]
            ],
            static::TYPE_LESSON_PAGE_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [1000, 600],
                static::SIZE_LARGE => [2000, 900]
            ],
            static::TYPE_PWA_APP_ICON => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [150, 150],
                static::SIZE_LARGE => [192, 192]
            ],
            static::TYPE_PWA_SPLASH_ICON => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [512, 512]
            ],
            static::TYPE_OPENGRAPH_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_MESSAGE_ATTACHMENT => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_CERTIFICATE_BACKGROUND_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [2070, 1680]
            ],
            static::TYPE_CERTIFICATE_IMAGE => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [2070, 1680]
            ],
            static::TYPE_ORDER_PAY_RECEIPT => [
                static::SIZE_SMALL => [100, 100],
                static::SIZE_MEDIUM => [300, 300],
                static::SIZE_LARGE => [600, 600]
            ],
            static::TYPE_COURSE_IMAGE => [
                static::SIZE_SMALL => [300, 169],
                static::SIZE_MEDIUM => [500, 281],
                static::SIZE_LARGE => [1000, 563]
            ],
            static::TYPE_COURSE_PREVIEW_VIDEO => [],
            static::TYPE_GROUP_CLASS_BANNER => [
                static::SIZE_SMALL => [300, 169],
                static::SIZE_MEDIUM => [500, 281],
                static::SIZE_LARGE => [1000, 563]
            ]
        ];
        if ($size === null) {
            return $arr[$this->type];
        }
        return $arr[$this->type][strtoupper($size)] ?? [200, 200];
    }

    /**
     * Get File Types For Image Attributes
     * 
     * @return array
     */
    public static function getFileTypesArrForImgAttrs(): array
    {
        return [
            static::TYPE_BANNER => Label::getLabel('IMGA_Banner'),
            static::TYPE_HOME_BANNER_DESKTOP => Label::getLabel('IMGA_Home_Page_Banner'),
            static::TYPE_CPAGE_BACKGROUND_IMAGE => Label::getLabel('IMGA_CPAGE_BACKGROUND_IMAGE'),
            static::TYPE_TEACHING_LANGUAGES => Label::getLabel('IMGA_TEACHING_LANGUAGES'),
            static::TYPE_BLOG_POST_IMAGE => Label::getLabel('IMGA_BLOG_POST_IMAGE'),
        ];
    }

    /**
     * Display Original Image
     * 
     * @param string $image_name        Image name to e displayed
     * @param string $no_image          No Image name in case provided image not found
     * @param string $uploadedFilePath  Uploaded Image path
     * @param bool $cache               Pass true if want o cache image content
     * @return type
     */
    public static function displayOriginalImage(string $image_name, string $no_image = '', string $uploadedFilePath = '', bool $cache = false)
    {
        
        ob_end_clean();
        if ($no_image == '') {
            $no_image = CONF_INSTALLATION_PATH . 'public/images/noimage.jpg';
        } else {
            $no_image = CONF_UPLOADS_PATH . 'defaults/' . $no_image;
        }
        if (trim($uploadedFilePath) != '') {
            $uploadedFilePath = CONF_UPLOADS_PATH . $uploadedFilePath;
        } else {
            $uploadedFilePath = CONF_UPLOADS_PATH;
        }
        $imagePath = $uploadedFilePath . $image_name;
        $contentType = static::getContentType($imagePath);
        header("content-type: " . $contentType);
        $cacheKey = $_SERVER['REQUEST_URI'];
        if (empty($image_name) || !file_exists($imagePath)) {
            echo file_get_contents($no_image);
            return;
        }
        $headers = FatApp::getApacheRequestHeaders();
        if (isset($headers['If-Modified-Since']) && (strtotime($headers['If-Modified-Since']) == filemtime($imagePath))) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($imagePath)) . ' GMT', true, 304);
            exit;
        }
        try {
            header('Cache-Control: public');
            header("Pragma: public");
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($imagePath)) . ' GMT', true, 200);
            header("Expires: " . date('r', strtotime("+30 Day")));
            $fileContents = file_get_contents($imagePath);
            echo $fileContents;
            if ($cache) {
                FatCache::set($cacheKey, $fileContents, '.jpg');
            }
        } catch (Exception $e) {
            $fileContents = file_get_contents($no_image);
            FatCache::set($cacheKey, $fileContents, '.jpg');
            echo $fileContents;
        }
    }

    /**
     * Get Allowed Upload Size
     * 
     * @param int $type
     * @return int
     */
    public static function getAllowedUploadSize(int $type = null): int
    {
        $maxUploadSizeAllowed = CommonHelper::getMaximumFileUploadSize(true);
        switch ($type) {
            case static::TYPE_TEACHER_APPROVAL_IMAGE:
            case static::TYPE_PAYIN:
            case static::TYPE_USER_PROFILE_IMAGE:
            case static::TYPE_BANNER:
            case static::TYPE_FRONT_LOGO:
            case static::TYPE_ADMIN_LOGO:
            case static::TYPE_EMAIL_LOGO:
            case static::TYPE_APPLE_TOUCH_ICON:
            case static::TYPE_BLOG_POST_IMAGE:
            case static::TYPE_PAYMENT_PAGE_LOGO:
            case static::TYPE_ADMIN_PROFILE_IMAGE:
            case static::TYPE_TESTIMONIAL_IMAGE:
            case static::TYPE_CPAGE_BACKGROUND_IMAGE:
            case static::TYPE_CATEGORY_COLLECTION_BG_IMAGE:
            case static::TYPE_LESSON_PACKAGE_IMAGE:
            case static::TYPE_LESSON_PLAN_IMAGE:
            case static::TYPE_BLOG_CONTRIBUTION:
            case static::TYPE_BANNER_SECOND_IMAGE:
            case static::TYPE_TEACHING_LANGUAGES:
            case static::TYPE_BLOG_PAGE_IMAGE:
            case static::TYPE_LESSON_PAGE_IMAGE:
            case static::TYPE_OPENGRAPH_IMAGE:
            case static::TYPE_APPLY_TO_TEACH_BANNER:
            case static::TYPE_USER_QUALIFICATION_FILE:
            case static::TYPE_TEACHER_APPROVAL_PROOF:
            case static::TYPE_LESSON_PLAN_FILE:
                case static::TYPE_LESSON_QUESTIONS_FILE:
            case static::TYPE_PWA_APP_ICON:
            case static::TYPE_PWA_SPLASH_ICON:
            case static::TYPE_HOME_BANNER_DESKTOP:
            case static::TYPE_HOME_BANNER_MOBILE:
            case static::TYPE_HOME_BANNER_IPAD:
            case static::TYPE_FAVICON:
            case static::TYPE_MESSAGE_ATTACHMENT:
            case static::TYPE_CERTIFICATE_BACKGROUND_IMAGE:
            case static::TYPE_ORDER_PAY_RECEIPT:
            case static::TYPE_COURSE_IMAGE:
            case static::TYPE_COURSE_PREVIEW_VIDEO:
            case static::TYPE_GROUP_CLASS_BANNER:
            case static::TYPE_CERTIFICATE_LOGO:
            default:
                /* 4194304 -- 4 mb  */
                return min($maxUploadSizeAllowed, 6194304);
        }
    }

    /**
     * Show Video
     *
     * @param integer $recordId
     * @param integer $subRecordId
     * @return video
     */
    public function showVideo(int $recordId, int $subRecordId = 0)
    {
        ob_end_clean();
        $file = $this->getFile($recordId, $subRecordId);
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        $fileExt = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
        header("Content-Type: " . static::getContentType($fileExt));
        $headers = FatApp::getApacheRequestHeaders();
        if (strtotime($headers['If-Modified-Since'] ?? '') == filemtime($filePath)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 200);
        header("Expires: " . date('r', strtotime("+30 Day")));
        header('Cache-Control: public');
        header("Pragma: public");
        $fileData = file_get_contents($filePath);
        if (CONF_USE_FAT_CACHE) {
            FatCache::set($_SERVER['REQUEST_URI'], $fileData, '.' . $fileExt);
        }
        echo $fileData;
    }
    /**
     * Show Video
     *
     * @param integer $recordId
     * @param integer $subRecordId
     * @return video
     */
    public function showPdf(int $recordId, int $subRecordId = 0)
    {
        ob_end_clean();
        $file = $this->getFile($recordId, $subRecordId);
        $filePath = CONF_UPLOADS_PATH . $file['file_path'];
        $fileExt = strtolower(pathinfo($file['file_name'], PATHINFO_EXTENSION));
        header("Content-Type: " . static::getContentType($fileExt));
        $headers = FatApp::getApacheRequestHeaders();
        if (strtotime($headers['If-Modified-Since'] ?? '') == filemtime($filePath)) {
            header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 304);
            exit;
        }
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s', filemtime($filePath)) . ' GMT', true, 200);
        header("Expires: " . date('r', strtotime("+30 Day")));
        header('Cache-Control: public');
        header("Pragma: public");
        $fileData = file_get_contents($filePath);
        if (CONF_USE_FAT_CACHE) {
            FatCache::set($_SERVER['REQUEST_URI'], $fileData, '.' . $fileExt);
        }
        echo $fileData;
    }
}
