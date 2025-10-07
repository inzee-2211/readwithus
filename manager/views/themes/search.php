<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php
$arr_flds = array(
    'theme_title' => Label::getLabel('LBL_Theme_Color'),
    'theme_primary_color' => Label::getLabel('LBL_Primary_Color'),
    'theme_primary_inverse_color' => Label::getLabel('LBL_Primary_Inverse_Color'),
    'theme_secondary_color' => Label::getLabel('LBL_Secondary_Color'),
    'theme_secondary_inverse_color' => Label::getLabel('LBL_Secondary_Inverse_Color'),
    'theme_footer_color' => Label::getLabel('LBL_Footer_Color'),
    'theme_footer_inverse_color' => Label::getLabel('LBL_Footer_Inverse_Color')
);
if ($canEdit) {
    $arr_flds['action'] = Label::getLabel('LBL_Action');
}
$tbl = new HtmlElement('table', array('width' => '100%', 'class' => 'table table-responsive table--hoevered'));
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', array(), $val);
}

$activeThemeId = FatApp::getConfig('CONF_ACTIVE_THEME');
$sr_no = $page == 1 ? 0 : $pageSize * ($page - 1);
foreach ($arr_listing as $sn => $row) {
    $sr_no++;
    $tr = $tbl->appendElement('tr');
    $tr->setAttribute("id", $row['theme_id']);

    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'listserial':
                $td->appendElement('plaintext', array(), $sr_no);
                break;
            case 'theme_title':
                $activeString = ($activeThemeId == $row['theme_id']) ? ' <i class="icon ion-checkmark-circled is--active"></i>' : '';
                $td->appendElement('plaintext', array(), $row['theme_title'] . $activeString, true);
                break;
            case 'theme_primary_color':
            case 'theme_primary_inverse_color':
            case 'theme_secondary_color':
            case 'theme_secondary_inverse_color':
            case 'theme_footer_color':
            case 'theme_footer_inverse_color':
                $content = "<a href='javascript:void(0)' class = 'button small green' style='background-color:#" . $row[$key] . "; height: 11px;width: 11px;display: inline-block;'></a> ";
                $td->appendElement('plaintext', [], $content . '#' . $row[$key], true);
                break;
            case 'action':
                if ($canEdit) {
                    $ul = $td->appendElement("ul", array("class" => "actions actions--centered"));
                    $li = $ul->appendElement("li", array('class' => 'droplink'));

                    $li->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')), '<i class="ion-android-more-horizontal icon"></i>', true);
                    $innerDiv = $li->appendElement('div', array('class' => 'dropwrap'));
                    $innerUl = $innerDiv->appendElement('ul', array('class' => 'linksvertical'));

                    $innerLiEdit = $innerUl->appendElement('li');

                    /* Edit */
                    if ($row['theme_is_default'] == AppConstant::NO) {
                        $innerLiEdit->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "edit(" . $row['theme_id'] . ", 'update')"), Label::getLabel('LBL_Edit'), true);

                        /* Delete */
                        $innerLiDelete = $innerUl->appendElement('li');
                        $innerLiDelete->appendElement('a', array('href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteTheme(" . $row['theme_id'] . ")"), Label::getLabel('LBL_Delete'), true);
                    }

                    /* Clone */
                    $innerLiClone = $innerUl->appendElement('li');
                    $innerLiClone->appendElement('a', array(
                        'href' => 'javascript:void(0)', 'class' => 'button small green',
                        'title' => Label::getLabel('LBL_Clone'), "onclick" => "edit(" . $row['theme_id'] . ", 'clone')"
                    ), Label::getLabel('LBL_Clone'), true);

                    /* Preview */
                    $innerLiPreview = $innerUl->appendElement('li');
                    $url = MyUtility::makeUrl('Themes', 'preview', array($row['theme_id']));
                    $innerLiPreview->appendElement('a', array('href' => $url, 'class' => 'button small green', 'title' => Label::getLabel('LBL_Preview'), 'target' => '_blank'), Label::getLabel('LBL_Preview'), true);

                    /* Activate */
                    if ($activeThemeId != $row['theme_id']) {
                        $innerLiActivate = $innerUl->appendElement('li');
                        $lbl = Label::getLabel('LBL_Click_To_Activate');
                        $innerLiActivate->appendElement('a', array('href' => 'javascript:void(0)', 'class' => "button small", 'title' => $lbl, "onclick" => "activate(" . $row['theme_id'] . ")"), $lbl, true);
                    }
                }
                break;

            default:
                $td->appendElement('plaintext', array(), $row[$key], true);
                break;
        }
    }
}
if (count($arr_listing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', array('colspan' => count($arr_flds)), Label::getLabel('LBL_No_Records_Found'));
}
echo $tbl->getHtml();
$postedData['page'] = $page;
echo FatUtility::createHiddenFormFromData($postedData, array(
    'name' => 'frmThemeSearchPaging'
));
$pagingArr = array('pageCount' => $pageCount, 'page' => $page, 'recordCount' => $recordCount, 'pageSize' => $pageSize, 'adminLangId');
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
?>
