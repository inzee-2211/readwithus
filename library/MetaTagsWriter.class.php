<?php

class MetaTagsWriter
{

    static function getMetaTags($controller, $action, $arrParameters)
    {
        $langId = MyUtility::getSiteLangId();
        if (!$langId) {
            $langId = FatApp::getConfig('CONF_DEFAULT_LANG', FatUtility::VAR_INT, 1);
        }
        $websiteName = FatApp::getConfig('CONF_WEBSITE_NAME_' . $langId, FatUtility::VAR_STRING, '');
        $controller = explode('-', FatUtility::camel2dashed($controller));
        array_pop($controller);
        $controllerName = implode('-', $controller);
        $controllerName = ucfirst(FatUtility::dashed2Camel($controllerName));
        if ($controllerName == 'Teachers' && $action == 'view') {
            $teacherName = $arrParameters[0];
            $teacher = User::getByUsername($teacherName);
            if (!empty($teacher)) {
                $techLangArr = TeacherSearch::getTeachLangs($langId, [$teacher['user_id']]);
                $title = 'Book Online Reading Class or 1 to 1 Session with ' . $teacher['user_first_name'] . " " . $teacher['user_last_name'] . ' | ' . $websiteName;
                $desc =  'Book online reading classes for your preschool kids, kindergarten, KS1, KS2 and reception students with ' . $teacher['user_first_name'] . " " . $teacher['user_last_name']  . ' Teaches ' . $techLangArr[$teacher['user_id']];
                echo '<title>' . $title . '</title>' . "\n";
                echo '<meta name="description" content="' . $desc . '" />';
                echo '<meta property="og:title" content="' . $title . '">
                <meta property="og:type" content="website">';
                if(User::isProfilePicUploaded($teacher['user_id'])){
                    $img =  MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_USER_PROFILE_IMAGE, $teacher['user_id'], Afile::SIZE_MEDIUM], CONF_WEBROOT_FRONTEND);              
                }
                else{
                    $img = 'http://readwithus.org.uk/images/online-reading-tutors-for-your-child.png';
                }
                 echo '<meta property="og:url" content="https://www.facebook.com/Read-With-Us-104487818591433">
                <meta property="og:image" content="' . $img . '">
                <meta property="og:site_name" content="Read with Us">';
            }
        } else {
            $srch = new MetaTagSearch($langId);
            $srch->doNotCalculateRecords();
            $srch->setPageSize(1);
            $srch->addMultipleFields([
                'meta_id', 'IFNULL(meta_title, meta_identifier) as meta_title',
                'meta_keywords', 'meta_description', 'meta_other_meta_tags', 'meta_og_title', 'meta_og_url', 'meta_og_description'
            ]);
            $defSearch = clone $srch;
            $srch->addCondition('meta_controller', '=', $controllerName);
            $srch->addCondition('meta_action', '=', $action);
            if (!empty($arrParameters)) {
                if (isset($arrParameters[0]) && $arrParameters[0] != '') {
                    $cond = $srch->addCondition('meta_record_id', '=', $arrParameters[0]);
                }
            }
            $rs = $srch->getResultSet();
            if ($metas = FatApp::getDb()->fetch($rs)) {
                /* --Get opengraph image- */
                $title = $metas['meta_title'] . ' | ' . $websiteName;
                echo '<title>' . $title . '</title>' . "\n";
                if (isset($metas['meta_description']))
                    echo '<meta name="description" content="' . $metas['meta_description'] . '" />';
                if (isset($metas['meta_keywords']))
                    echo '<meta name="keywords" content="' . $metas['meta_keywords'] . '" />';
                if (isset($metas['meta_other_meta_tags']))
                    echo CommonHelper::renderHtml($metas['meta_other_meta_tags'], ENT_QUOTES, 'UTF-8');
                if (isset($metas['meta_og_title']))
                    echo '<meta property="og:title" content="' . $metas['meta_og_title'] . '" />';
                if (isset($metas['meta_og_url']))
                    echo '<meta property="og:url" content="' . $metas['meta_og_url'] . '" />';
                if (isset($metas['meta_og_description']))
                    echo '<meta property="og:description" content="' . $metas['meta_og_description'] . '" />';
                echo '<meta property="og:image"  itemprop="image" content="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_OPENGRAPH_IMAGE, $metas['meta_id'], Afile::SIZE_MEDIUM]) . '" />';
            } else {
                $defSearch->addCondition('meta_type', '=', MetaTag::META_GROUP_DEFAULT);
                if ($metas = FatApp::getDb()->fetch($defSearch->getResultSet())) {
                    $title = $metas['meta_title'] . ' | ' . $websiteName;
                    echo '<title>' . $title . '</title>' . "\n";
                    if (isset($metas['meta_description']))
                        echo '<meta name="description" content="' . $metas['meta_description'] . '" />';
                    if (isset($metas['meta_keywords']))
                        echo '<meta name="keywords" content="' . $metas['meta_keywords'] . '" />';
                    if (isset($metas['meta_other_meta_tags']))
                        echo CommonHelper::renderHtml($metas['meta_other_meta_tags'], ENT_QUOTES, 'UTF-8');
                    if (isset($metas['meta_og_title']))
                        echo '<meta property="og:title" content="' . $metas['meta_og_title'] . '" />';
                    if (isset($metas['meta_og_url']))
                        echo '<meta property="og:url" content="' . $metas['meta_og_url'] . '" />';
                    if (isset($metas['meta_og_description']))
                        echo '<meta property="og:description" content="' . $metas['meta_og_description'] . '" />';
                        echo '<meta property="og:image" content="http://readwithus.org.uk/images/online-reading-tutors-for-your-child.png">';
                  //  echo '<meta property="og:image"  itemprop="image" content="' . MyUtility::makeFullUrl('Image', 'show', [Afile::TYPE_OPENGRAPH_IMAGE, $metas['meta_id'], Afile::SIZE_MEDIUM]) . '" />';
                } else {
                    return '<title>' . $websiteName . '</title>';
                }
            }
        }
    }
}
