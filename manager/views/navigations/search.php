<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<div class="sectionbody">
    <div class="tablewrap" >
        <?php
        $arr_flds = [
            'listserial' => Label::getLabel('LBL_SRNO'),
            'nav_identifier' => Label::getLabel('LBL_Title'),
            'nav_active' => Label::getLabel('LBL_Status'),
        ];
        if ($canEdit) {
            $arr_flds['action'] = Label::getLabel('LBL_Action');
        }
        $tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered']);
        $th = $tbl->appendElement('thead')->appendElement('tr');
        foreach ($arr_flds as $val) {
            $e = $th->appendElement('th', [], $val);
        }
        $sr_no = 0;
        foreach ($arr_listing as $sn => $row) {
            $sr_no++;
            $tr = $tbl->appendElement('tr', []);
            foreach ($arr_flds as $key => $val) {
                $td = $tr->appendElement('td');
                switch ($key) {
                    case 'listserial':
                        $td->appendElement('plaintext', [], $sr_no);
                        break;
                    case 'nav_identifier':
                        if ($row['nav_name'] != '') {
                            $td->appendElement('plaintext', [], $row['nav_name'], true);
                            $td->appendElement('br', []);
                            $td->appendElement('plaintext', [], '(' . $row[$key] . ')', true);
                        } else {
                            $td->appendElement('plaintext', [], $row[$key], true);
                        }
                        break;
                    case 'nav_active':
                        $active = "";
                        if ($row['nav_active']) {
                            $active = 'checked';
                        }
                        $statusAct = ( $canEdit === true ) ? 'toggleStatus(event,this,' . AppConstant::YES . ')' : 'toggleStatus(event,this,' . AppConstant::NO . ')';
                        $statusClass = ( $canEdit === false ) ? 'disabled' : '';
                        $str = '<label class="statustab -txt-uppercase">
                     <input ' . $active . ' type="checkbox" id="switch' . $row['nav_id'] . '" value="' . $row['nav_id'] . '" onclick="' . $statusAct . '" class="switch-labels"/>
                    <i class="switch-handles ' . $statusClass . '"></i> </label>';
                        $td->appendElement('plaintext', [], $str, true);
                        break;
                    case 'action':
                        $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                        $li = $ul->appendElement("li", ['class' => 'droplink']);
                        $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                        $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                        $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                        if ($canEdit) {
                            $innerLiEdit = $innerUl->appendElement('li');
                            $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addFormNew(" . $row['nav_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                        }
                        $innerLiPages = $innerUl->appendElement("li");
                        $innerLiPages->appendElement('a', ['href' => MyUtility::makeUrl('Navigations', 'pages', [$row['nav_id']]), 'class' => 'button small green', 'title' => Label::getLabel('LBL_Pages')], Label::getLabel('LBL_Pages'), true);
                        break;
                    default:
                        $td->appendElement('plaintext', [], $row[$key], true);
                        break;
                }
            }
        }
        if (count($arr_listing) == 0) {
            $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
        }
        echo $tbl->getHtml();
        ?>
    </div> 
</div>	