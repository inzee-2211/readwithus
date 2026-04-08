<?php

defined('SYSTEM_INIT') or die('Invalid Usage.');
$arrFlds = [
    'dragdrop' => '',
    'listserial' => Label::getLabel('LBL_Sr._No'),
    'cate_identifier' => Label::getLabel('LBL_IDENTIFIER'),
    'cate_name' => Label::getLabel('LBL_NAME'),
    'cate_sub_categories' => Label::getLabel('LBL_SUB_CATEGORIES'),
    'cate_records' => Label::getLabel('LBL_COURSES'),
    'cate_created' => Label::getLabel('LBL_ADDED_ON'),
    'status' => Label::getLabel('LBL_STATUS'),
];
if ($postedData['parent_id'] > 0) {
    unset($arrFlds['cate_sub_categories']);
}
if (!$canEdit) {
    unset($arrFlds['dragdrop']);
} else {
    $arrFlds['action'] = Label::getLabel('LBL_ACTION');
}
$tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive', 'id' => 'categoriesList']);
$th = $tbl->appendElement('thead')->appendElement('tr');
foreach ($arrFlds as $val) {
    $e = $th->appendElement('th', [], $val);
}
$srNo = 0;
foreach ($arrListing as $sn => $row) {
    $srNo++;
    $tr = $tbl->appendElement('tr', ['id' => $row['cate_id']]);
    foreach ($arrFlds as $key => $val) {
        $td = $tr->appendElement('td');
        switch ($key) {
            case 'dragdrop':
                if ($row['cate_status'] == AppConstant::YES) {
                    $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                    $td->setAttribute("class", 'dragHandle');
                }
                break;
            case 'listserial':
                $td->appendElement('plaintext', [], $srNo);
                break;
            case 'cate_created':
                $td->appendElement('plaintext', [], MyDate::formatDate($row['cate_created']));
                break;
            case 'cate_sub_categories':
                if ($row['cate_subcategories'] > 0) {
                    $td->appendElement('a', ['href' => MyUtility::makeUrl('Categories', 'index', [$row['cate_id']]), 'class' => 'button small green', 'title' => Label::getLabel('LBL_SUB_CATEGORIES')], $row['cate_subcategories'], true);
                } else {
                    $td->appendElement('plaintext', [], 0);
                }
                break;
            case 'cate_records':
                if ($row['cate_records'] > 0) {
                    if ($row['cate_type'] == Category::TYPE_COURSE) {
                        if ($canViewCourses) {
                            $qryString = '?course_cateid=' . $row['cate_id'];
                            if ($postedData['parent_id'] > 0) {
                                $qryString = '?course_cateid=' . $postedData['parent_id'] . '&course_subcateid=' . $row['cate_id'];
                            }
                            $td->appendElement('a', ['href' => MyUtility::makeUrl('Courses', 'index') . $qryString, 'class' => 'button small green', 'title' => Label::getLabel('LBL_COURSES')], $row['cate_records'], true);
                        } else {
                            $td->appendElement('plaintext', ['title' => Label::getLabel('LBL_COURSES')], $row['cate_records']);
                        }
                    }
                } else {
                    $td->appendElement('plaintext', [], 0);
                }
                break;
            case 'status':
                $active = "active";
                if ($row['cate_status'] == AppConstant::NO) {
                    $active = 'inactive';
                }
                $str = '<label class="statustab ' . $active . '" '. (($canEdit) ? 'onclick="updateStatus(\'' . $row['cate_id'] . '\', \''.$row['cate_status'].'\')"':""). '>
				  <span data-off="' . Label::getLabel('LBL_Active') . '" data-on="' . Label::getLabel('LBL_Inactive') . '" class="switch-labels "></span>
				  <span class="switch-handles"></span>
				</label>';
                $td->appendElement('plaintext', [], $str, true);
                break;
            case 'action':
                $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                $li = $ul->appendElement("li", ['class' => 'droplink']);
                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Action')], '<i class="ion-android-more-horizontal icon"></i>', true);
                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);

                $ul = $td->appendElement("ul", ["class" => "actions"]);
                if ($canEdit) {
                    $langId = !empty($row['catelang_lang_id']) ? $row['catelang_lang_id'] : 0;
                    $actionLi = $innerUl->appendElement("li");
                    $actionLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_EDIT'), "onclick" => "categoryForm(" . $row['cate_id'] . ", '".$langId."')"], Label::getLabel('LBL_EDIT'), true);

                    $actionLi = $innerUl->appendElement("li");
                    $actionLi->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_DELETE'), "onclick" => "remove('" . $row['cate_id'] . "')"], Label::getLabel('LBL_DELETE'), true);
                }
                break;
            default:
                $td->appendElement('plaintext', [], CommonHelper::renderHtml($row[$key] ?? '-'));
                break;
        }
    }
}

if (count($arrListing) == 0) {
    $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arrFlds)], Label::getLabel('LBL_NO_RECORDS_FOUND'));
}
echo $tbl->getHtml();
?>
<script>
    $(document).ready(function() {
        $('#categoriesList').tableDnD({
            onDrop: function(table, row) {
                updateOrder();
            },
            dragHandle: ".dragHandle",
        });
    });
</script>