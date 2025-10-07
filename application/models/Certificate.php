<?php

use JonnyW\PhantomJs\Client;

/**
 * This class is used to handle certificates
 *
 * @package YoCoach
 * @author Fatbit Team
 */
class Certificate extends MyAppModel
{
    const DB_TBL = 'tbl_order_courses';
    const DB_TBL_PREFIX = 'ordcrs_';
    const CERTIFICATE_NO_PREFIX = 'YC_';
    const TYPE_COURSE = 'course_completion_certificate';
    
    private $userId;
    private $langId;

    /**
     * Initialize certificate
     *
     * @param int $id
     */
    public function __construct(int $id = 0, int $userId = 0, int $langId = 0)
    {
        $this->userId = $userId;
        $this->langId = $langId;
        parent::__construct(static::DB_TBL, 'ordcrs_id', $id);
    }

    /**
     * Generate Certificate Image & PDF
     *
     * @param string $token
     * @return bool
     */
    public function generateCertificate($content, $filename, $preview = false)
    {
        $mpdf = new \Mpdf\Mpdf([
            'orientation' => 'L',
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_top' => 0,
            'margin_bottom' => 0,
            'mirrorMargins' => 0,
            'autoLangToFont' => true,
            'autoScriptToLang' => true,
            'tempDir' => CONF_INSTALLATION_PATH . 'vendor/mpdf/mpdf/cache'
        ]);
        $mpdf->SetDirectionality(Language::getAttributesById($this->langId, 'language_direction'));
        $mpdf->WriteHTML($content);
        $path = $this->getFilePath() . $filename;
        if ($preview == false) {
            $mpdf->Output(CONF_UPLOADS_PATH . $path, \Mpdf\Output\Destination::FILE);
            if (!$this->saveFile(Afile::TYPE_CERTIFICATE_PDF, $filename, $path)) {
                $this->error = Label::getLabel('LBL_AN_ERROR_HAS_OCCURRED_WHILE_GENERATING_CERTIFICATE!');
                return false;
            }
        } else {
            $mpdf->Output($filename, \Mpdf\Output\Destination::INLINE);
        }
        return true;
    }

    public function setupMetaTags(array $data)
    {
        /* get course details */
        $srch = new SearchBase(Course::DB_TBL_LANG);
        $srch->addCondition('course_id', '=', $data['ordcrs_course_id']);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(1);
        $srch->addFld('course_title');
        $course = FatApp::getDb()->fetch($srch->getResultSet());

        /* get user name */
        $username = User::getAttributesById($data['order_user_id'], "CONCAT(user_first_name, ' ', user_last_name)");
        $content = $course['course_title'] . ' | ' . ucwords($username) . ' | ' . FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->langId);

        $meta = new MetaTag();
        $meta->assignValues([
            'meta_controller' => 'Certificates',
            'meta_action' => 'view',
            'meta_type' => MetaTag::META_GROUP_COURSE_CERTIFICATE,
            'meta_record_id' => $data['ordcrs_id'],
            'meta_identifier' => $content
        ]);
        if (!$meta->save()) {
            $this->error = $meta->getError();
            return false;
        }
        if (
            !FatApp::getDb()->insertFromArray(
                MetaTag::DB_LANG_TBL, [
                    'metalang_meta_id' => $meta->getMainTableRecordId(),
                    'metalang_lang_id' => $this->langId,
                    'meta_title' => $content,
                    'meta_og_url' => MyUtility::makeFullUrl('Certificates', 'view', [$data['ordcrs_id']], CONF_WEBROOT_FRONTEND),
                    'meta_og_title' => $content
                ]
            )
        ) {
            $this->error = $meta->getError();
            return false;
        }
        return true;
    }
    
    /**
     * Get Course & User details for certificate
     *
     * @param int     $ordcrsId
     * @return array
     */
    public function getDataForCertificate(int $ordcrsId)
    {
        $srch = new OrderCourseSearch($this->langId, $this->userId, 0);
        $srch->joinTable(CourseLanguage::DB_TBL, 'INNER JOIN', 'clang.clang_id = course.course_clang_id', 'clang');
        $srch->joinTable(
            CourseLanguage::DB_TBL_LANG,
            'LEFT JOIN',
            'clang.clang_id = clanglang.clanglang_clang_id AND clanglang.clanglang_lang_id = ' . $this->langId,
            'clanglang'
        );
        $srch->applyPrimaryConditions();
        $srch->addSearchListingFields();
        $srch->addMultipleFields([
            'crspro_completed',
            'IFNULL(clanglang.clang_name, clang.clang_identifier) AS course_clang_name',
            'learner.user_lang_id',
            'ordcrs_certificate_number',
            'course_duration'
        ]);
        $srch->addCondition('ordcrs_id', '=', $ordcrsId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Get formatted certificate content
     *
     * @param array $data
     * @return array
     */
    public function getFormattedContent(array $data)
    {
        $srch = CertificateTemplate::getSearchObject($data['lang_id']);
        $srch->addCondition('certpl_code', '=', 'course_completion_certificate');
        if (!$template = FatApp::getDb()->fetch($srch->getResultSet())) {
            $this->error = Label::getLabel('LBL_CONTENT_NOT_FOUND');
            return false;
        }
        $content = $template['certpl_body'];
        $title = $data['course_title'];
        $title = htmlentities(stripslashes(utf8_encode($data['course_title'])), ENT_QUOTES);
        $content = str_replace(
            [
                '{learner-name}',
                '{teacher-name}',
                '{course-name}',
                '{course-language}',
                '{course-completed-date}',
                '{certificate-number}',
                '{course-duration}'
            ],
            [
                ucwords($data['learner_first_name'] . ' ' . $data['learner_last_name']),
                ucwords($data['teacher_first_name'] . ' ' . $data['teacher_last_name']),
                '<span class=\"courseNameJs\">' . $title . '</span>',
                $data['course_clang_name'],
                MyDate::formatDate($data['crspro_completed'], 'Y-m-d'),
                $data['cert_number'],
                YouTube::convertDuration($data['course_duration'])
            ],
            $content
        );
        return json_decode($content, true);
    }

    /**
     * Save created files data
     *
     * @param int $type
     * @param string $filename
     * @param string $path
     * @return bool
     */
    public function saveFile(int $type, string $filename, string $path)
    {
        $record = new TableRecord(Afile::DB_TBL);
        $record->assignValues([
            'file_type' => $type,
            'file_record_id' => $this->getMainTableRecordId(),
            'file_name' => $filename,
            'file_path' => $path,
            'file_order' => 0,
            'file_added' => date('Y-m-d H:i:s')
        ]);
        if (!$record->addNew()) {
            $this->error = $record->getError();
            return false;
        }
        /* delete old file */
        $fileId = $record->getId();
        $stmt = [
            'vals' => [$type, 0, $this->getMainTableRecordId(), $fileId],
            'smt' => 'file_type = ? AND file_lang_id = ? AND file_record_id = ? AND file_id != ?'
        ];
        FatApp::getDb()->deleteRecords(Afile::DB_TBL, $stmt);
        return true;
    }

    /**
     * function to get path for file uploading
     *
     * @return string
     */
    private function getFilePath()
    {
        $uploadPath = CONF_UPLOADS_PATH;
        $filePath = date('Y') . '/' . date('m') . '/';
        if (!file_exists($uploadPath . $filePath)) {
            mkdir($uploadPath . $filePath, 0777, true);
        }
        return $filePath;
    }
}
