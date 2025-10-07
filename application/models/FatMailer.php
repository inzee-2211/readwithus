<?php

use PHPMailer\PHPMailer\PHPMailer;

/**
 * This class is used to handle Fat Mailer
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class FatMailer extends FatModel
{

    const DB_TBL = 'tbl_email_templates';
    const DB_TBL_ARCHIVE = 'tbl_email_archives';

    private $toArr;
    private $ccArr;
    private $bccArr;
    private $fromArr;
    private $variables;
    private $attachments;
    private $archiveId;
    private $langId;
    private $template;

    /**
     * Initialize Mailer
     * 
     * @param int $langId
     * @param string $template
     */
    public function __construct(int $langId, string $template)
    {
        $this->toArr = [];
        $this->ccArr = [];
        $this->bccArr = [];
        $this->fromArr = [];
        $this->variables = [];
        $this->attachments = [];
        $this->archiveId = null;
        $this->langId = $langId;
        $this->template = $template;
    }

    /**
     * Set Email From
     * 
     * @param string $email
     * @param string $name
     */
    public function setFrom(string $email, string $name = '')
    {
        if (!empty($email)) {
            $this->fromArr = [$email, $name];
        }
    }

    /**
     * Set Email CC
     * 
     * @param array $ccArr
     */
    public function setCc(array $ccArr)
    {
        $this->ccArr = $ccArr;
    }

    /**
     * Set Email BCC
     * 
     * @param array $bccArr
     */
    public function setBcc(array $bccArr = [])
    {
        $this->bccArr = $bccArr;
    }

    /**
     * Set Variables:
     * Can be called multiple times
     *
     * @param array $variables
     */
    public function setVariables(array $variables)
    {
        foreach ($variables as $key => $value) {
            $this->variables[$key] = $value;
        }
    }

    /**
     * Set Email Attachments:
     * Can be called multiple times
     *
     * @param array $attachments
     */
    public function setAttachments(array $attachments)
    {
        foreach ($attachments as $attachment) {
            array_push($this->attachments, $attachment);
        }
    }

    /**
     * Send Email
     *
     * @param array $toArr
     * @return bool
     */
    public function sendMail(array $toArr): bool
    {
        $this->toArr = $toArr;
        if (count(array_merge($this->toArr, $this->ccArr, $this->bccArr)) < 1) {
            $this->error = Label::getLabel('LBL_TO_EMAIL_ADDRESS_IS_REQUIRED!');
            return false;
        }
        $tempalte = $this->getTemplate();
        if ($tempalte == null) {
            $this->error = Label::getLabel('LBL_EMAIL_TEMPLATE_NOT_FOUND!');
            return false;
        }
        if (empty($this->fromArr)) {
            $this->fromArr = [
                FatApp::getConfig('CONF_FROM_EMAIL'),
                FatApp::getConfig('CONF_FROM_NAME_' . $this->langId, FatUtility::VAR_STRING, '')
            ];
        }

        $layout = $this->getLayout();
        $this->setVariables(['{email_body}' => $tempalte['etpl_body']]);
        $body = $this->replaceVariables($layout['etpl_body']);

        $this->setCommonVariables();
        $body = $this->replaceVariables($body);

        $subject = $this->replaceVariables($tempalte['etpl_subject']);
        if (!$this->addToArchive($subject, $body)) {
            return false;
        }
        if (
                !ALLOW_EMAILS || $tempalte['etpl_quick_send'] == AppConstant::NO ||
                FatApp::getConfig('CONF_SEND_EMAIL') == AppConstant::NO
        ) {
            return true;
        }
        if (!$this->sendByPhpMailer($subject, $body)) {
            return false;
        }
        $this->markArchiveSent();
        return true;
    }

    private function getLayout()
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('etpl_code', '=', 'emails_header_footer_layout');
        $srch->addCondition('etpl_lang_id', '=', $this->langId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Send By PHP Mailer
     * 
     * @param string $subject
     * @param string $body
     * @return bool
     */
    private function sendByPhpMailer(string $subject, string $body): bool
    {
        $mail = new PHPMailer();
        if (FatApp::getConfig('CONF_SEND_SMTP_EMAIL')) {
            $mail->IsSMTP();
            $mail->SMTPAuth = true;
            $mail->Host = FatApp::getConfig("CONF_SMTP_HOST");
            $mail->Port = FatApp::getConfig("CONF_SMTP_PORT");
            $mail->Username = FatApp::getConfig("CONF_SMTP_USERNAME");
            $mail->Password = FatApp::getConfig("CONF_SMTP_PASSWORD");
            $mail->SMTPSecure = FatApp::getConfig("CONF_SMTP_SECURE");
        } else {
            $mail->isMail();
        }
        $mail->IsHTML();
        $mail->CharSet = 'UTF-8';
        $mail->setFrom(...$this->fromArr);
        foreach ($this->toArr as $toEmail) {
            $mail->addAddress($toEmail);
        }
        foreach ($this->ccArr as $ccEmail) {
            $mail->addCC($ccEmail);
        }
        foreach ($this->bccArr as $bccEmail) {
            $mail->addBCC($bccEmail);
        }
        foreach ($this->attachments as $attachment) {
            $mail->addAttachment($attachment);
        }
        $mail->msgHTML($body);
        $mail->Subject = $subject;
        if (!$mail->send()) {
            $this->error = $mail->ErrorInfo;
            return false;
        }
        return true;
    }

    /**
     * Get email template
     *
     * @return null|array   Will return null in case template not found
     */
    private function getTemplate()
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addCondition('etpl_code', '=', $this->template);
        $srch->addCondition('etpl_lang_id', '=', $this->langId);
        return FatApp::getDb()->fetch($srch->getResultSet());
    }

    /**
     * Add to archives
     *
     * @param string $subject
     * @param string $body
     * @return bool
     */
    private function addToArchive(string $subject, string $body): bool
    {
        $archiveRecord = [
            'earch_from_name' => $this->fromArr[1] ?? '',
            'earch_from_email' => $this->fromArr[0] ?? '',
            'earch_to_email' => implode(',', $this->toArr),
            'earch_cc_email' => implode(',', $this->ccArr),
            'earch_bcc_email' => implode(',', $this->bccArr),
            'earch_tpl_name' => $this->template,
            'earch_subject' => $subject,
            'earch_body' => $body,
            'earch_attachemnts' => implode(',', $this->attachments),
            'earch_added' => date('Y-m-d H:i:s')
        ];
        $record = new TableRecord(static::DB_TBL_ARCHIVE);
        $record->assignValues($archiveRecord);
        if (!$record->addNew()) {
            $this->error = $record->getError();
            return false;
        }
        $this->archiveId = $record->getId();
        return true;
    }

    /**
     * Mark archived email sent
     *
     * @return bool
     */
    private function markArchiveSent(): bool
    {
        $record = new TableRecord(static::DB_TBL_ARCHIVE);
        $record->setFldValue('earch_senton', date('Y-m-d H:i:s'));
        $where = ['smt' => 'earch_id = ?', 'vals' => [$this->archiveId]];
        if (!$record->update($where)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Mark archived email attempted
     *
     * @return bool
     */
    private function markArchiveAttempted(): bool
    {
        $record = new TableRecord(static::DB_TBL_ARCHIVE);
        $record->setFldValue('earch_attempted', date('Y-m-d H:i:s'));
        $where = ['smt' => 'earch_id = ?', 'vals' => [$this->archiveId]];
        if (!$record->update($where)) {
            $this->error = $record->getError();
            return false;
        }
        return true;
    }

    /**
     * Replace template variables
     *
     * @param string $string
     * @return string
     */
    private function replaceVariables(string $string): string
    {
        foreach ($this->variables as $key => $value) {
            $string = str_replace($key, $value, $string);
        }
        return $string;
    }

    private function setCommonVariables()
    {
        $siteUrl = MyUtility::makeFullUrl('', '', [], CONF_WEBROOT_FRONT_URL);
        $commonVars = [
            '{website_url}' => $siteUrl,
            '{Company_Logo}' => '<img style="max-width: 160px;" src="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_EMAIL_LOGO], CONF_WEBROOT_FRONT_URL) . '" />',
            '{website_name}' => FatApp::getConfig('CONF_WEBSITE_NAME_' . $this->langId, FatUtility::VAR_STRING, ''),
            '{contact_us_url}' => MyUtility::makeFullUrl('contact', '', [], CONF_WEBROOT_FRONT_URL),
            '{notifcation_email}' => FatApp::getConfig('CONF_FROM_EMAIL'),
            '{social_media_icons}' => $this->getSocialMediaLink(),
            '{current_date}' => date('M d, Y'),
            '{current_year}' => date('Y'),
        ];
        $this->setVariables($commonVars);
        $this->setThemeVars();
    }

    private function setThemeVars()
    {
        $fields = [
            'theme_primary_color',
            'theme_secondary_color',
            'theme_secondary_inverse_color'
        ];
        $themeData = Theme::getAttributesById(FatApp::getConfig('CONF_ACTIVE_THEME'), $fields);

        $this->setVariables([
            '{primary-color}' => '#' . $themeData['theme_primary_color'],
            '{secondary-color}' => '#' . $themeData['theme_secondary_color'],
            '{secondary-inverse-color}' => '#' . $themeData['theme_secondary_inverse_color']
        ]);
    }

    /**
     * Get Social Media Link
     * 
     * @return string
     */
    private function getSocialMediaLink(): string
    {
        $socialIcons = '';
        $rows = SocialPlatform::getAll();
        foreach ($rows as $name => $link) {
            $target = empty($link) ? '' : 'target="_blank"';
            $url = empty($link) ? 'javascript:void(0)' : $link;
            $title = Label::getLabel('LBL_' . $name, $this->langId);
            $img = MyUtility::makeFullUrl('images', strtolower($name) . '.png', [], CONF_WEBROOT_FRONTEND);
            $socialIcons .= '<a style="display:inline-block;vertical-align:top; width:35px; height:35px; margin:0 0 0 5px; border-radius:100%;" href="' . $url . '" ' . $target . '>'
                    . '<img src="' . $img . '" style="width: 25px;height: 25px; margin:5px auto 0; display:block;" /></a>';
        }
        return $socialIcons;
    }

    /**
     * Send Archived Email
     *
     * @param array $archive
     * @return bool
     */
    public function sendArchivedMail(array $archive): bool
    {
        if (FatApp::getConfig('CONF_SEND_EMAIL') == AppConstant::NO || !ALLOW_EMAILS) {
            return true;
        }
        $this->toArr = explode(',', $archive['earch_to_email']);
        $this->ccArr = explode(',', $archive['earch_cc_email']);
        $this->bccArr = explode(',', $archive['earch_bcc_email']);
        $this->attachments = array_filter(explode(',', $archive['earch_attachemnts']));
        $this->setFrom($archive['earch_from_email'], $archive['earch_from_name']);
        if (empty($this->fromArr)) {
            $this->fromArr = [FatApp::getConfig('CONF_FROM_EMAIL'),
                FatApp::getConfig('CONF_FROM_NAME_' . $this->langId, FatUtility::VAR_STRING, '')];
        }
        $this->archiveId = $archive['earch_id'];
        if (
                (count(array_merge($this->toArr, $this->ccArr, $this->bccArr)) < 1) ||
                !$this->sendByPhpMailer($archive['earch_subject'], $archive['earch_body'])
        ) {
            return $this->markArchiveAttempted();
        }
        return $this->markArchiveSent();
    }

    /**
     * Send SMTP Test Email
     *
     * @param array $smtp = [CONF_SMTP_HOST, CONF_SMTP_PORT, CONF_SMTP_USERNAME, CONF_SMTP_PASSWORD, CONF_SMTP_SECURE]
     * @return bool
     */
    public function sendSmtpTestEmail(array $smtp): bool
    {
        if (FatApp::getConfig('CONF_SEND_EMAIL') == AppConstant::NO) {
            return true;
        }
        $tempalte = $this->getTemplate();
        if ($tempalte == null) {
            $this->error = Label::getLabel('LBL_EMAIL_TEMPLATE_NOT_FOUND!');
            return false;
        }
        $mail = new PHPMailer();
        $mail->IsSMTP();
        $mail->IsHTML();
        $mail->CharSet = 'UTF-8';
        $mail->SMTPAuth = true;
        $mail->Host = $smtp['CONF_SMTP_HOST'];
        $mail->Port = $smtp['CONF_SMTP_PORT'];
        $mail->Username = $smtp['CONF_SMTP_USERNAME'];
        $mail->Password = $smtp['CONF_SMTP_PASSWORD'];
        $mail->SMTPSecure = $smtp['CONF_SMTP_SECURE'];
        $mail->setFrom(FatApp::getConfig('CONF_FROM_EMAIL'), FatApp::getConfig('CONF_FROM_NAME_' . $this->langId, FatUtility::VAR_STRING, ''));
        $mail->addAddress(FatApp::getConfig('CONF_SITE_OWNER_EMAIL'));
        $mail->msgHTML($this->replaceVariables($tempalte['etpl_body']));
        $mail->Subject = $this->replaceVariables($tempalte['etpl_subject']);
        if (!$mail->send()) {
            $this->error = $mail->ErrorInfo;
            return false;
        }
        return true;
    }

}
