
 
<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'qid' => Label::getLabel('LBL_ID'),
    'level_name' => Label::getLabel('LBL_LEVEL'),
    'subject' => Label::getLabel('LBL_SUBJECT'),
    'topic' => Label::getLabel('LBL_TOPIC'),
    'tier' => Label::getLabel('LBL_TIER'),
    'type' => Label::getLabel('LBL_TYPE'),
      'examBoards' => Label::getLabel('LBL_EXAMBOARDS'),
    // 'subcategory' => Label::getLabel('LBL_SUBCATEGORY'),
  
    'action' => Label::getLabel('LBL_ACTION'),
];

?>
<div class="buttons-group" style="padding: 4px;align-content: right;text-align: right;">
<a href="javascript:void(0);" onclick="categoryForm();" class="btn-primary">Add New</a>
</div>
<?php

$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arr_flds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$paymentMethod = OrderPayment::getMethods();

//echo '<pre>';print_r($arrListing);die;
foreach ($arrListing as $row) {

   
    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_OPTIONS')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

                $innerLiEdit = $innerUl->appendElement('li');
                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'view("'.$row['qid'].'")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_DELETE')], Label::getLabel('LBL_DELETE'), true);
              
              
                break;
              //    $innerLiEdit = $innerUl->appendElement('li');
             //   $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'onclick' => 'userLogin("' . $row['course_teacher_id'] . '", "' . $row['question_id'] . '", "preview")', 'class' => 'button small green', 'title' => Label::getLabel('LBL_PREVIEW')], Label::getLabel('LBL_PREVIEW'), true);
                //   if ($canEdit && $row['course_status'] != Course::PUBLISHED) {
                //     $innerLiEdit = $innerUl->appendElement('li');
                //     $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0);', 'class' => 'button small green', 'title' => Label::getLabel('LBL_VIEW'), 'onclick' => 'userLogin("'.$row['course_teacher_id'].'", "'.$row['course_id'].'", "edit")'], Label::getLabel('LBL_Edit'), true);
                // }  
            //     
            // case 'level_name':
            //     $td->appendElement('plaintext', [], $row['level_name'] . ' ' . $row['level_name'], true);
            //     break;
            // case 'coapre_updated':
            //     $td->appendElement('plaintext', [], MyDate::formatDate($row[$key]), true);
            //     break;
                // case 'question_status':
                //     $status = $row['question_status'] == 1 ? 'Active' : 'Deactive'; // Check the status value
                //     $td->appendElement('plaintext', [], $status, true); // Append "Active" or "Deactive"
                //     break;
                
            // case 'course_active':
            //     $active = "active";
            //     if ($row['course_active'] == AppConstant::NO) {
            //         $active = 'inactive';
            //     }
            //     $str = '<label class="statustab ' . $active . '" ' . (($canEdit) ? 'onclick="updateStatus(\'' . $row['id'] . '\', \'' . $row['course_active'] . '\')"' : "") . '>
			// 	  <span data-off="' . Label::getLabel('LBL_Active') . '" data-on="' . Label::getLabel('LBL_Inactive') . '" class="switch-labels "></span>
			// 	  <span class="switch-handles"></span>
			// 	</label>';
            //     $td->appendElement('plaintext', [], $str, true);
            //     break;
              default:
                $td->appendElement('plaintext', [], $row[$key], true);
                break;
        }
    }
}
if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmPaging']);
$pagingArr = ['pageCount' => ceil($recordCount / $post['pagesize']), 'pageSize' => $post['pagesize'], 'page' => $post['page'], 'recordCount' => $recordCount];
$this->includeTemplate('_partial/pagination.php', $pagingArr, false);


