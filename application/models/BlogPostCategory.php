<?php

/**
 * This class is used to handle Blog Post Category
 * 
 * @package YoCoach
 * @author Fatbit Team
 */
class BlogPostCategory extends MyAppModel
{

    const DB_TBL = 'tbl_blog_post_categories';
    const DB_TBL_PREFIX = 'bpcategory_';
    const DB_TBL_LANG = 'tbl_blog_post_categories_lang';
    const DB_LANG_TBL_PREFIX = 'bpcategorylang_';
    const REWRITE_URL_PREFIX = 'blog/category/';

    /**
     * Initialize Blog Post Category
     * 
     * @param int $id
     */
    public function __construct(int $id = 0)
    {
        parent::__construct(static::DB_TBL, 'bpcategory_id', $id);
    }

    /**
     * Get Search Object
     * 
     * @param bool $includeChildCount
     * @param int $langId
     * @param bool $bpcategory_active
     * @return SearchBase
     */
    public static function getSearchObject(bool $includeChildCount = false, int $langId = 0, bool $bpcategory_active = true): SearchBase
    {
        $langId = FatUtility::int($langId);
        $srch = new SearchBase(static::DB_TBL, 'bpc');
        $srch->addOrder('bpc.bpcategory_active', 'DESC');
        if ($includeChildCount) {
            $childSrchbase = new SearchBase(static::DB_TBL);
            $childSrchbase->addCondition('bpcategory_deleted', '=', 0);
            $childSrchbase->doNotCalculateRecords();
            $childSrchbase->doNotLimitRecords();
            $srch->joinTable('(' . $childSrchbase->getQuery() . ')', 'LEFT OUTER JOIN', 's.bpcategory_parent = bpc.bpcategory_id', 's');
            $srch->addGroupBy('bpc.bpcategory_id');
            $srch->addFld('COUNT(s.bpcategory_id) AS child_count');
        }
        if ($langId > 0) {
            $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'bpc_l.bpcategorylang_bpcategory_id = bpc.bpcategory_id and bpc_l.bpcategorylang_lang_id = ' . $langId, 'bpc_l');
        }
        if ($bpcategory_active) {
            $srch->addCondition('bpc.bpcategory_active', '=', AppConstant::ACTIVE);
        }
        $srch->addCondition('bpc.bpcategory_deleted', '=', AppConstant::NO);
        return $srch;
    }

    /**
     * Get Max Order
     * 
     * @param int $parent
     * @return int
     */
    public function getMaxOrder(int $parent = 0): int
    {
        $srch = new SearchBase(static::DB_TBL);
        $srch->addFld("MAX(bpcategory_order) as max_order");
        if ($parent > 0) {
            $srch->addCondition('bpcategory_parent', '=', $parent);
        }
        $srch->doNotCalculateRecords();
        $srch->doNotLimitRecords();
        $rs = $srch->getResultSet();
        $record = FatApp::getDb()->fetch($rs);
        if (!empty($record)) {
            return $record['max_order'] + 1;
        }
        return 1;
    }

    /**
     * Get Category Structure
     * 
     * @param int $bpcategory_id
     * @param array $category_tree_array
     * @return array
     */
    public function getCategoryStructure(int $bpcategory_id, array $category_tree_array = [])
    {
        $srch = static::getSearchObject();
        $srch->addCondition('bpc.bpcategory_deleted', '=', AppConstant::NO);
        $srch->addCondition('bpc.bpcategory_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('bpc.bpcategory_id', '=', $bpcategory_id);
        $srch->addOrder('bpc.bpcategory_order', 'asc');
        $srch->addOrder('bpc.bpcategory_identifier', 'asc');
        $rs = $srch->getResultSet();
        while ($categories = FatApp::getDb()->fetch($rs)) {
            $category_tree_array[] = $categories;
            $category_tree_array = $this->getCategoryStructure($categories['bpcategory_parent'], $category_tree_array);
        }
        sort($category_tree_array);
        return $category_tree_array;
    }

    /**
     * Add Update Blog Post Category Lang
     * 
     * @param array $data
     * @param int $lang_id
     * @param int $bpcategory_id
     * @return bool|int
     */
    public function addUpdateBlogPostCatLang(array $data, int $lang_id, int $bpcategory_id)
    {
        $tbl = new TableRecord(static::DB_TBL_LANG);
        $data['bpcategorylang_bpcategory_id'] = FatUtility::int($bpcategory_id);
        $tbl->assignValues($data);
        if ($this->isExistBlogPostCatLang($lang_id, $bpcategory_id)) {
            if (!$tbl->update(['smt' => 'bpcategorylang_bpcategory_id = ? and bpcategorylang_lang_id = ? ', 'vals' => [$bpcategory_id, $lang_id]])) {
                $this->error = $tbl->getError();
                return false;
            }
            return $bpcategory_id;
        }
        if (!$tbl->addNew()) {
            $this->error = $tbl->getError();
            return false;
        }
        return true;
    }

    /**
     * Exist Blog Post Category Language
     * 
     * @param int $lang_id
     * @param int $bpcategory_id
     * @return bool
     */
    public function isExistBlogPostCatLang(int $lang_id, int $bpcategory_id): bool
    {
        $srch = new SearchBase(static::DB_TBL_LANG);
        $srch->addCondition('bpcategorylang_bpcategory_id', '=', $bpcategory_id);
        $srch->addCondition('bpcategorylang_lang_id', '=', $lang_id);
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row)) {
            return true;
        }
        return false;
    }

    /**
     * Get Parent Tree Structure
     * 
     * @param int $bpCategory_id
     * @param int $level
     * @param string $nameSuffix
     * @return type
     */
    public function getParentTreeStructure(int $bpCategory_id = 0, int $level = 0, string $nameSuffix = '')
    {
        $srch = static::getSearchObject();
        $srch->addFld('bpc.bpcategory_id,bpc.bpcategory_identifier, bpc.bpcategory_parent');
        $srch->addCondition('bpc.bpcategory_deleted', '=', AppConstant::NO);
        $srch->addCondition('bpc.bpcategory_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('bpc.bpCategory_id', '=', FatUtility::int($bpCategory_id));
        $rs = $srch->getResultSet();
        $records = FatApp::getDb()->fetch($rs);
        $name = '';
        $seprator = '';
        if ($level > 0) {
            $seprator = ' &nbsp;&nbsp;&raquo;&raquo;&nbsp;&nbsp;';
        }
        if ($records) {
            $name = $records['bpcategory_identifier'] . $seprator . $nameSuffix;
            if ($records['bpcategory_parent'] > 0) {
                $name = $this->getParentTreeStructure($records['bpcategory_parent'], $level + 1, $name);
            }
        }
        return $name;
    }

    /**
     * Check Category Active
     * 
     * @param int $categoryId
     * @return type
     */
    public static function isCategoryActive(int $categoryId)
    {
        $categoryId = FatUtility::int($categoryId);
        $srch = self::getSearchObject(false, 0, true);
        $srch->addCondition('bpcategory_id', '=', $categoryId);
        $rs = $srch->getResultSet();
        return $srch->recordCount();
    }

    /**
     * Make Associative Array
     * 
     * @param array $arr
     * @param string $prefix
     * @return string
     */
    public function makeAssociativeArray(array $arr, string $prefix = ' » ')
    {
        $out = [];
        $tempArr = [];
        foreach ($arr as $key => $value) {
            $tempArr[] = $key;
            $name = $value['bpcategory_name'];
            $code = str_replace('_', '', $value['bpcategory_code']);
            $hierarchyArr = str_split($code, 6);
            $this_deleted = 0;
            foreach ($hierarchyArr as $node) {
                $node = FatUtility::int($node);
                if (!in_array($node, $tempArr)) {
                    $this_deleted = 1;
                    break;
                }
            }
            if ($this_deleted == 0) {
                $level = strlen($code) / 6;
                for ($i = 1; $i < $level; $i++) {
                    $name = $prefix . $name;
                }
                $out[$key] = $name;
            }
        }
        return $out;
    }

    /**
     * Get Categories For Select Box
     * 
     * @param int $langId
     * @param int $ignoreCategoryId
     * @return array
     */
    public function getCategoriesForSelectBox(int $langId, int $ignoreCategoryId = 0)
    {
        $srch = static::getSearchObject();
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'bpcategorylang_bpcategory_id = bpcategory_id AND bpcategorylang_lang_id = ' . $langId);
        $srch->addMultipleFields(['bpcategory_id', 'IFNULL(bpcategory_name, bpcategory_identifier) AS bpcategory_name', 'GETBLOGCATCODE(bpcategory_id) AS bpcategory_code']);
        $srch->addCondition('bpcategory_deleted', '=', 0);
        $srch->addOrder('GETBLOGCATORDERCODE(bpcategory_id)');
        if ($ignoreCategoryId > 0) {
            $srch->addHaving('bpcategory_code', 'NOT LIKE', '%' . str_pad($ignoreCategoryId, 6, '0', STR_PAD_LEFT) . '%');
        }
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'bpcategory_id');
    }

    /**
     * Get Featured Categories
     * 
     * @param int $langId
     * @return array
     */
    public function getFeaturedCategories(int $langId)
    {
        $srch = static::getSearchObject();
        $srch->joinTable(static::DB_TBL_LANG, 'LEFT OUTER JOIN', 'bpcategorylang_bpcategory_id = bpcategory_id AND bpcategorylang_lang_id = ' . $langId);
        $srch->addCondition('bpcategory_featured', '=', 1);
        $srch->addMultipleFields(['bpcategory_id', 'IFNULL(bpcategory_name, bpcategory_identifier) AS bpcategory_name', 'GETBLOGCATCODE(bpcategory_id) AS bpcategory_code']);
        $srch->addOrder('GETBLOGCATORDERCODE(bpcategory_id)');
        return FatApp::getDb()->fetchAll($srch->getResultSet(), 'bpcategory_id');
    }

    /**
     * Get Blog Post Category Tree Structure
     * 
     * @param int $parent_id
     * @param string $keywords
     * @param int $level
     * @param string $name_prefix
     * @return array
     */
    public function getBlogPostCatTreeStructure(int $parent_id = 0, string $keywords = '', int $level = 0, string $name_prefix = ''): array
    {
        $srch = static::getSearchObject(false, MyUtility::getSiteLangId());
        $srch->addFld('bpc.bpcategory_id, IFNULL(bpc_l.bpcategory_name, bpc.bpcategory_identifier) as bpcategory_identifier');
        $srch->addCondition('bpc.bpcategory_deleted', '=', AppConstant::NO);
        $srch->addCondition('bpc.bpcategory_active', '=', AppConstant::ACTIVE);
        $srch->addCondition('bpc.bpcategory_parent', '=', FatUtility::int($parent_id));
        if (!empty($keywords)) {
            $srch->addCondition('bpc.bpcategory_identifier', 'like', '%' . $keywords . '%');
        }
        $srch->addOrder('bpc.bpcategory_order', 'asc');
        $srch->addOrder('bpcategory_identifier', 'asc');
        $records = FatApp::getDb()->fetchAllAssoc($srch->getResultSet());
        $return = [];
        $seprator = '';
        if ($level > 0) {
            $seprator = '&raquo;&raquo;&nbsp;&nbsp;';
            $seprator = CommonHelper::renderHtml($seprator);
        }
        foreach ($records as $bpcategory_id => $bpcategory_identifier) {
            $name = $name_prefix . $seprator . $bpcategory_identifier;
            $return[$bpcategory_id] = $name;
            $return += $this->getBlogPostCatTreeStructure($bpcategory_id, $keywords, $level + 1, $name);
        }
        return $return;
    }

    /**
     * Get Blog Post Category Parent Child Wise Arr
     * 
     * @param int $langId
     * @param int $parentId
     * @param bool $includeChildCat
     * @param bool $forSelectBox
     * @return type
     */
    public static function getBlogPostCatParentChildWiseArr(int $langId = 0, int $parentId = 0, bool $includeChildCat = true, bool $forSelectBox = false)
    {
        $parentId = FatUtility::int($parentId);
        $langId = FatUtility::int($langId);
        if (!$langId) {
            trigger_error(Label::getLabel('MSG_Language_not_specified'), E_USER_ERROR);
        }
        $bpCatSrch = new SearchBase(BlogPostCategory::DB_TBL, 'bpc');
        $bpCatSrch->joinTable(BlogPostCategory::DB_TBL_LANG, 'LEFT OUTER JOIN', 'bpcategorylang_bpcategory_id = bpc.bpcategory_id AND bpcategorylang_lang_id = ' . $langId, 'bpc_l');
        $bpCatSrch->addMultipleFields(['bpcategory_id', 'ifNull(bpcategory_name,bpcategory_identifier) as bpcategory_name']);
        $bpCatSrch->addCondition('bpcategory_active', '=', AppConstant::ACTIVE);
        $bpCatSrch->addOrder('GETBLOGCATORDERCODE(bpcategory_id)');
        $bpCatSrch->addCondition('bpcategory_deleted', '=', 0);
        $bpCatSrch->addCondition('bpcategory_parent', '=', $parentId);
        $bpCatSrch->doNotCalculateRecords();
        $bpCatSrch->doNotLimitRecords();
        $bpCatSrch->addOrder('bpcategory_order', 'asc');
        if ($forSelectBox) {
            $categoriesArr = FatApp::getDb()->fetchAllAssoc($bpCatSrch->getResultSet());
        } else {
            $categoriesArr = FatApp::getDb()->fetchAll($bpCatSrch->getResultSet());
        }
        if (!$includeChildCat) {
            return $categoriesArr;
        }
        if ($categoriesArr) {
            foreach ($categoriesArr as &$cat) {
                $cat['children'] = self::getBlogPostCatParentChildWiseArr($langId, $cat['bpcategory_id']);
                $childPosts = BlogPost::getBlogPostsUnderCategory($langId, $cat['bpcategory_id']);
                $cat['countChildBlogPosts'] = count($childPosts);
            }
        }
        return $categoriesArr;
    }

    /**
     * Can Mark Record Delete
     * 
     * @param int $bpcategory_id
     * @return boolean
     */
    public function canMarkRecordDelete(int $bpcategory_id)
    {
        $srch = static::getSearchObject();
        $srch->addCondition('bpc.bpcategory_deleted', '=', AppConstant::NO);
        $srch->addCondition('bpc.bpcategory_id', '=', $bpcategory_id);
        $srch->addFld('bpc.bpcategory_id');
        $row = FatApp::getDb()->fetch($srch->getResultSet());
        if (!empty($row) && $row['bpcategory_id'] == $bpcategory_id) {
            return true;
        }
        return false;
    }

}
