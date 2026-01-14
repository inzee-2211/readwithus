<?php

/**
 * A Common Sitemap Utility  
 *  
 * @package YoCoach
 * @author Fatbit Team
 */
class Sitemap
{

    /**
     * Get URLs
     * 
     * @param int $langId
     * @return array
     */
    public static function getUrls(int $langId)
    {
        $sitemapUrls = [];
        $srch = new TeacherSearch($langId, 0, User::LEARNER);
        $srch->addMultipleFields(['user_username', 'user_first_name', 'user_last_name']);
        $srch->applyPrimaryConditions();
        $srch->doNotCalculateRecords();
        $srch->setPageSize(2000);
        $resultSet = $srch->getResultSet();
        $urls = [];
        while ($row = FatApp::getDb()->fetch($resultSet)) {
            array_push($urls, [
                'value' => $row['user_first_name'] . ' ' . $row['user_last_name'],
                'frequency' => 'weekly',
                'url' => MyUtility::makeFullUrl('Teachers', 'view', [$row['user_username']], CONF_WEBROOT_FRONT_URL)
            ]);
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_TEACHERS') => $urls]);
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
            array_push($urls, [
                'value' => $row['grpcls_title'],
                'frequency' => 'weekly',
                'url' => MyUtility::makeFullUrl('GroupClasses', 'view', [$row['grpcls_slug']], CONF_WEBROOT_FRONT_URL)
            ]);
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_GROUP_CLASSES') => $urls]);
        /* ] */
        /* Courses [ */
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
            array_push($urls, [
                'value' => $row['course_title'],
                'frequency' => 'weekly',
                'url' => MyUtility::makeFullUrl('Courses', 'view', [$row['course_slug']], CONF_WEBROOT_FRONT_URL)
            ]);
        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_COURSES') => $urls]);
        /* ] */
        /* CMS Pages [ */
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
            if ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_CMS && $link['nlink_cpage_id']) {
                array_push($urls, [
                    'value' => $link['nlink_identifier'], 'frequency' => 'monthly',
                    'url' => MyUtility::makeFullUrl('Cms', 'view', [$link['nlink_cpage_id']], CONF_WEBROOT_FRONT_URL)
                ]);
            // } elseif ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_EXTERNAL_PAGE) {
            //     $url = str_replace(['{SITEROOT}', '{siteroot}'], [CONF_WEBROOT_FRONT_URL, CONF_WEBROOT_FRONT_URL], $link['nlink_url']);
            //     $url = CommonHelper::processURLString($url);
            //     array_push($urls, ['url' => CommonHelper::getUrlScheme() . $url, 'value' => $link['nlink_identifier'], 'frequency' => 'monthly']);
            // }
            } elseif ($link['nlink_type'] == NavigationLinks::NAVLINK_TYPE_EXTERNAL_PAGE) {

    $url = str_replace(
        ['{SITEROOT}', '{siteroot}'],
        [rtrim(CONF_WEBROOT_FRONT_URL, '/'), rtrim(CONF_WEBROOT_FRONT_URL, '/')],
        $link['nlink_url']
    );

    $url = CommonHelper::processURLString($url);
    $url = trim($url);

    // ✅ If already absolute, keep it.
    if (preg_match('~^https?://~i', $url)) {
        $finalUrl = $url;
    } else {
        // else build absolute properly
        $finalUrl = rtrim(CONF_WEBROOT_FRONT_URL, '/') . '/' . ltrim($url, '/');
    }

    array_push($urls, [
        'url' => $finalUrl,
        'value' => $link['nlink_identifier'],
        'frequency' => 'monthly'
    ]);
}

        }
        $sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_CMS_PAGES') => $urls]);
        /* Blogs [ */
$blogUrls = [];

// Include blog listing page
$blogUrls[] = [
    'value' => 'Blog',
    'frequency' => 'weekly',
    'url' => MyUtility::makeFullUrl('Blog', 'index', [], CONF_WEBROOT_FRONT_URL)
];

// If YoCoach has BlogPostSearch, use it (best)
if (class_exists('BlogPostSearch')) {

    $srch = new BlogPostSearch($langId);
    // Adjust fields if your project uses different column names
    $srch->addMultipleFields([
        'post_id',
        'post_slug',
        'IFNULL(bplang.post_title, bp.post_title) as post_title'
    ]);

    // Typical YoCoach conditions (adjust if constants differ)
    if (defined('AppConstant::NO')) {
        $srch->addCondition('post_deleted', '=', AppConstant::NO);
    }
    if (defined('AppConstant::ACTIVE')) {
        $srch->addCondition('post_active', '=', AppConstant::ACTIVE);
    }

    $srch->doNotCalculateRecords();
    $srch->setPageSize(5000);
    $rs = $srch->getResultSet();

    while ($row = FatApp::getDb()->fetch($rs)) {
        if (!empty($row['post_slug'])) {
            $blogUrls[] = [
                'value' => $row['post_title'] ?? $row['post_slug'],
                'frequency' => 'weekly',
                'url' => MyUtility::makeFullUrl('Blog', 'view', [$row['post_slug']], CONF_WEBROOT_FRONT_URL)
            ];
        }
    }

} else {
    // Fallback: query table directly (adjust table/fields if needed)
    $db = FatApp::getDb();

    // Try common YoCoach blog table names (pick the correct one)
    $possibleTables = ['tbl_blog_posts', 'blog_posts'];

    foreach ($possibleTables as $tbl) {
        $qry = "SHOW TABLES LIKE '" . $db->escape($tbl) . "'";
        $rs  = $db->query($qry);
        if ($db->fetch($rs)) {
            $sql = "SELECT post_slug, post_title
                    FROM $tbl
                    WHERE IFNULL(post_deleted,0)=0
                    AND IFNULL(post_active,1)=1
                    LIMIT 5000";
            $r2 = $db->query($sql);
            while ($row = $db->fetch($r2)) {
                if (!empty($row['post_slug'])) {
                    $blogUrls[] = [
                        'value' => $row['post_title'] ?? $row['post_slug'],
                        'frequency' => 'weekly',
                        'url' => MyUtility::makeFullUrl('Blog', 'view', [$row['post_slug']], CONF_WEBROOT_FRONT_URL)
                    ];
                }
            }
            break;
        }
    }
}

$sitemapUrls = array_merge($sitemapUrls, [Label::getLabel('LBL_BLOGS') => $blogUrls]);
/* ] */

        return $sitemapUrls;
    }

}
