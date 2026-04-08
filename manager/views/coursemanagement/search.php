<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arr_flds = [
    'qid'           => Label::getLabel('LBL_ID'),
    'subject'       => Label::getLabel('LBL_SUBJECT'),
    'topic'         => Label::getLabel('LBL_TOPIC'),
    'subtopic_name' => Label::getLabel('LBL_SUBTOPIC'),
    'video_url'     => Label::getLabel('LBL_VIDEO_URL'),
    'pdf_path'      => Label::getLabel('LBL_PDF_PATH'),
    'action'        => Label::getLabel('LBL_ACTION'),
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

foreach ($arrListing as $row) {

    $tr = $tbl->appendElement('tr');
    foreach ($arr_flds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'action':
    $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
    $li = $ul->appendElement('li', ['class' => 'droplink']);
    $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_OPTIONS')], '<i class="ion-android-more-horizontal icon"></i>', true);
    $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
    $innerUl  = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

    // View (simple: go to questionBank page as the detail)
    // $innerUl->appendElement('li')->appendElement(
    //     'a',
    //     ['href' => MyUtility::makeUrl('Coursemanagement', 'questionBank', [$row['qid']])],
    //     Label::getLabel('LBL_VIEW'),
    //     true
    // );

    // Edit (open form, pass id)
    $innerUl->appendElement('li')->appendElement(
        'a',
        ['href' => 'javascript:void(0);', 'onclick' => 'categoryForm(' . $row['qid'] . ');'],
        Label::getLabel('LBL_EDIT'),
        true
    );

    // View Question Bank (explicit)
    $innerUl->appendElement('li')->appendElement(
        'a',
        ['href' => MyUtility::makeUrl('Coursemanagement', 'questionBank', [$row['qid']])],
        Label::getLabel('LBL_VIEW_QUESTION_BANK'),
        true
    );

    // Delete
    $innerUl->appendElement('li')->appendElement(
        'a',
        ['href' => 'javascript:void(0);', 'onclick' => 'deleteSubtopic(' . $row['qid'] . ');'],
        Label::getLabel('LBL_DELETE'),
        true
    );
    break;

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
?>
<script>
function deleteSubtopic(id){
    if(!confirm('Delete this subtopic and its questions?')) return;
    fcom.updateWithAjax(fcom.makeUrl('Coursemanagement','deleteSubtopic',[id]), '', function(t){ search(document.frmSearch); });
}
function categoryForm(id){
  fcom.ajax(
    fcom.makeUrl('Coursemanagement','subtopicForm', [id || 0]),
    '',
    function (t) { 
      $.facebox(t);
    }
  );
}

function loadTopics(subjectId) {
    if (!subjectId || subjectId <= 0) {
        $('#quiz_setup_id').html('<option value="">Select Topic</option>');
        return;
    }
    
    fcom.updateWithAjax(
        fcom.makeUrl('Coursemanagement', 'topicsBySubject'),
        {subject_id: subjectId},
        function (response) {
            var options = '<option value="">Select Topic</option>';
            if (response.data && Object.keys(response.data).length > 0) {
                for (var id in response.data) {
                    options += '<option value="' + id + '">' + response.data[id] + '</option>';
                }
            }
            $('#quiz_setup_id').html(options);
        }
    );
}

</script>
