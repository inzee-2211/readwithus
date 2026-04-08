<?php

/**
 * A Common Sitemap Utility
 *
 * @package YoCoach
 * @author Fatbit
 */
class Sitemap
{
    /**
     * Normalize URL so sitemap never contains double-domain or malformed URLs.
     */
    private static function normalizeUrl(string $url): string
    {
        $url = trim($url);

        if ($url === '') {
            return '';
        }

        // If string contains multiple http(s)://, keep the LAST occurrence.
        // Example: http://site.comhttps://site.com/faq  => https://site.com/faq
        if (preg_match_all('~https?://~i', $url, $m, PREG_OFFSET_CAPTURE) && count($m[0]) > 1) {
            $last = end($m[0]);
            $url  = substr($url, (int)$last[1]);
        }

        // Decode accidental HTML entities and remove spaces
        $url = html_entity_decode($url, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $url = preg_replace('/\s+/', '', $url);

        // If already absolute, return as-is
        if (preg_match('~^https?://~i', $url)) {
            return $url;
        }

        // Otherwise treat it as relative and prepend site root
        return rtrim(CONF_WEBROOT_FRONT_URL, '/') . '/' . ltrim($url, '/');
    }

    /**
     * Get URLs
     *
     * @param int $langId
     * @return array
     */
    public static function getUrls(int $langId)
    {
        $sitemapUrls = [];

        /* Teachers */
        $srch = new TeacherSearch($langId, 0, User::LEARNER);
        $srch->addMultipleFields(['user_username', 'user_first_name', 'user_last_name']);
        $srch->applyPrimaryConditions();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(2000);
        $resultSet = $srch->getResultSet();

        $urls = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $u = MyUtility::makeFullUrl('Teachers', 'view', [$row['user_username']], CONF_WEBROOT_FRONT_URL);
            $urls[] = [
                'value' => $row['user_first_name'] . ' ' . $row['user_last_name'],
                'frequency' => 'weekly',
                'url' => self::normalizeUrl($u),
            ];
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_TEACHERS') => $urls]);

        /* Group Classes */
        $srch = new GroupClassSearch($langId, 0, User::LEARNER);
        $srch->addMultipleFields(['grpcls_id', 'IFNULL(gclang.grpcls_title, grpcls.grpcls_title) as grpcls_title', 'grpcls_slug']);
        $srch->applyPrimaryConditions();
        $srch->applySearchConditions([]);
        $srch->addOrder('grpcls_start_datetime', 'asc');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(2000);
        $resultSet = $srch->getResultSet();

        $urls = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $u = MyUtility::makeFullUrl('GroupClasses', 'view', [$row['grpcls_slug']], CONF_WEBROOT_FRONT_URL);
            $urls[] = [
                'value' => $row['grpcls_title'],
                'frequency' => 'weekly',
                'url' => self::normalizeUrl($u),
            ];
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_GROUP_CLASSES') => $urls]);

        /* Courses */
        $srch = new CourseSearch($langId, 0, User::LEARNER);
        $srch->addMultipleFields(['course_slug', 'course_title']);
        $srch->applyPrimaryConditions();
        $srch->addCondition('course_status', '=', Course::PUBLISHED);
        $srch->addCondition('course_active', '=', AppConstant::ACTIVE);
        $srch->doNotCalculateRecords();
        $srch->setPageSize(2000);
        $resultSet = $srch->getResultSet();

        $urls = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            $u = MyUtility::makeFullUrl('Courses', 'view', [$row['course_slug']], CONF_WEBROOT_FRONT_URL);
            $urls[] = [
                'value' => $row['course_title'],
                'frequency' => 'weekly',
                'url' => self::normalizeUrl($u),
            ];
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_COURSES') => $urls]);

        /* CMS Pages (including external links) */
        $srch = Navigations::getLinkSearchObj($langId);
        $srch->addCondition('nlink_deleted', '=', AppConstant::NO);
        $srch->addCondition('nav_active', '=', AppConstant::ACTIVE);
        $srch->addMultipleFields(['nav_id', 'nlink_type', 'nlink_cpage_id', 'nlink_url', 'nlink_identifier']);
        $srch->addOrder('nlink_order', 'ASC');
        $srch->addGroupBy('nlink_cpage_id');
        $srch->addGroupBy('nlink_url');
        $srch->doNotCalculateRecords();
        $srch->setPageSize(2000);

        $resultSet = $srch->getResultSet();
        $urls = [];

        while ($link = FatApp::getDb()->fetch($resultSet)) {
            // CMS page
            if ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_CMS && $link['nlink_cpage_id']) {
                $u = MyUtility::makeFullUrl('Cms', 'view', [$link['nlink_cpage_id']], CONF_WEBROOT_FRONT_URL);
                $urls[] = [
                    'value' => $link['nlink_identifier'],
                    'frequency' => 'monthly',
                    'url' => self::normalizeUrl($u),
                ];
                continue;
            }

            // External page
            if ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_EXTERNAL_PAGE) {
                $raw = (string)$link['nlink_url'];

                // Replace placeholders if present
                $raw = str_replace(
                    ['{SITEROOT}', '{siteroot}'],
                    [rtrim(CONF_WEBROOT_FRONT_URL, '/'), rtrim(CONF_WEBROOT_FRONT_URL, '/')],
                    $raw
                );

                // Keep legacy processing if needed (but normalize after)
                $raw = CommonHelper::processURLString($raw);

                $finalUrl = self::normalizeUrl($raw);

                $urls[] = [
                    'value' => $link['nlink_identifier'],
                    'frequency' => 'monthly',
                    'url' => $finalUrl,
                ];
            }
        }

        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_CMS_PAGES') => $urls]);

        /* Blogs */
        $blogUrls = [];

        // Blog listing page
        $blogUrls[] = [
            'value' => 'Blog',
            'frequency' => 'weekly',
            'url' => self::normalizeUrl(MyUtility::makeFullUrl('Blog', 'index', [], CONF_WEBROOT_FRONT_URL)),
        ];

        /**
         * Blog posts (published only)
         * IMPORTANT: Your DB uses:
         *  - tbl_blog_post.post_identifier (NOT post_slug)
         *  - tbl_blog_post_lang.postlang_post_title
         */
        if (class_exists('BlogPost')) {
            // joinCategory=false, post_published=true
            $srch = BlogPost::getSearchObject($langId, false, true, false);

            // Join language table for title
            $srch->joinTable(
                BlogPost::DB_LANG_TBL,
                'INNER JOIN',
                'bp_l.postlang_post_id = bp.post_id AND bp_l.postlang_lang_id = ' . (int)$langId,
                'bp_l'
            );

            // Select correct fields based on your DB schema
        $srch->addMultipleFields([
    'bp.post_id',
    'bp.post_identifier AS post_slug',
    'IFNULL(bp_l.post_title, bp.post_identifier) AS post_title',
]);

$srch->doNotCalculateRecords();
$srch->setPageSize(5000);

$rs = $srch->getResultSet();
while ($row = FatApp::getDb()->fetch($rs)) {
    if (empty($row['post_slug'])) {
        continue;
    }

    $u = MyUtility::makeFullUrl('Blog', 'view', [$row['post_slug']], CONF_WEBROOT_FRONT_URL);

    $blogUrls[] = [
        'value' => $row['post_title'] ?? $row['post_slug'],
        'frequency' => 'weekly',
        'url' => self::normalizeUrl($u),
    ];
}

        }

        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_BLOGS') => $blogUrls]);

        return $sitemapUrls;
    }
}
