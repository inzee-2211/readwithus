<?php

/**
 * This class is used to handle Meta Tags
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class MetaTag extends MyAppModel
{

    const DB_TBL = 'tbl_meta_tags';
    const DB_TBL_PREFIX = 'meta_';
    const DB_LANG_TBL = 'tbl_meta_tags_lang';
    const DB_LANG_TBL_PREFIX = 'metalang_';
    const META_GROUP_DEFAULT = -1;
    const META_GROUP_OTHER = 0;
    const META_GROUP_TEACHER = 1;
    const META_GROUP_GRP_CLASS = 2;
    const META_GROUP_CMS_PAGE = 3;
    const META_GROUP_BLOG_CATEGORY = 4;
    const META_GROUP_BLOG_POST = 5;
    const META_GROUP_COURSE = 6;
    const META_GROUP_COURSE_CERTIFICATE = 7;

    /**
     * Initialize MetaTag
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'meta_id', $id);
    }

    /**
     * Get Tabs Array
     * 
     * @return array
     */
    public static function getTabsArr(): array
    {
        return [
            static::META_GROUP_DEFAULT => ['name' => Label::getLabel('METALBL_Default'), 'controller' => 'Default', 'action' => 'Default'],
            static::META_GROUP_OTHER => ['name' => Label::getLabel('METALBL_Others'), 'controller' => '', 'action' => ''],
            static::META_GROUP_TEACHER => ['name' => Label::getLabel('METALBL_Teachers'), 'controller' => 'Teachers', 'action' => 'view'],
            static::META_GROUP_GRP_CLASS => ['name' => Label::getLabel('METALBL_Group_Classes'), 'controller' => 'GroupClasses', 'action' => 'view'],
            static::META_GROUP_CMS_PAGE => ['name' => Label::getLabel('METALBL_CMS_Page'), 'controller' => 'Cms', 'action' => 'view'],
            static::META_GROUP_BLOG_CATEGORY => ['name' => Label::getLabel('METALBL_Blog_Categories'), 'controller' => 'Blog', 'action' => 'category'],
            static::META_GROUP_BLOG_POST => ['name' => Label::getLabel('METALBL_Blog_Posts'), 'controller' => 'Blog', 'action' => 'postDetail'],
            static::META_GROUP_COURSE => ['name' => Label::getLabel('METALBL_Courses'), 'controller' => 'Courses', 'action' => 'view'],
        ];
    }

    /**
     * Get Original URL From Components
     * 
     * @param array $row
     * @return boolean
     */
    public static function getOrignialUrlFromComponents(array $row)
    {
        if (empty($row) || $row['meta_controller'] == '') {
            return false;
        }
        $url = '';
        foreach ([$row['meta_controller'], $row['meta_action'], $row['meta_record_id']] as $value) {
            if ($value != '0' && $value != '') {
                $url .= $value . '/';
            }
        }
        return rtrim($url, '/');
    }

}
