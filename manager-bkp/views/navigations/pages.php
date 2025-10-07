<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if ($shortview != AppConstant::YES) { ?>
    <div class='page'>
        <div class='fixed_container'>
            <div class="row">
                <div class="space">
                    <div class="page__title">
                        <div class="row">
                            <div class="col--first col-lg-auto">
                                <span class="page__icon"><i class="ion-android-star"></i></span>
                                <h5><?php echo Label::getLabel('LBL_NAVIGATION_PAGES'); ?> </h5>
                                <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                            </div>
                            <?php if ($canEdit) { ?>
                                <div class="col-lg-auto">
                                    <div class="buttons-group">
                                        <a href="<?php echo MyUtility::makeUrl('Navigations'); ?>" class="btn-primary"><?php echo Label::getLabel('LBL_BACK'); ?></a>
                                        <a href="javascript:void(0);" onclick="addNavigationLinkForm('<?php echo $nav_id; ?>', 0);" class="btn-primary"><?php echo Label::getLabel('LBL_ADD_NEW'); ?></a>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                    <section class="section" id="listing">
                    <?php } ?>
                    <div class="sectionbody">
                        <div class="tablewrap" >
                            <?php
                            $arr_flds = [
                                'dragdrop' => '',
                                'listserial' => Label::getLabel('LBL_Sr._No'),
                                'nlink_identifier' => Label::getLabel('LBL_caption'),
                                'action' => Label::getLabel('LBL_Action'),
                            ];
                            if (!$canEdit) {
                                unset($arr_flds['dragdrop']);
                            }
                            $tbl = new HtmlElement('table', ['width' => '100%', 'class' => 'table table-responsive table--hovered', 'id' => 'pageList']);
                            $th = $tbl->appendElement('thead')->appendElement('tr');
                            foreach ($arr_flds as $val) {
                                $e = $th->appendElement('th', [], $val);
                            }
                            $sr_no = 0;
                            foreach ($arrListing as $sn => $row) {
                                $sr_no++;
                                $tr = $tbl->appendElement('tr');
                                $tr->setAttribute("id", $row['nlink_id']);
                                foreach ($arr_flds as $key => $val) {
                                    $td = $tr->appendElement('td');
                                    switch ($key) {
                                        case 'dragdrop':
                                            $td->appendElement('i', ['class' => 'ion-arrow-move icon']);
                                            $td->setAttribute("class", 'dragHandle');
                                            break;
                                        case 'listserial':
                                            $td->appendElement('plaintext', [], $sr_no);
                                            break;
                                        case 'nlink_identifier':
                                            if ($row['nlink_caption'] != '') {
                                                $td->appendElement('plaintext', [], $row['nlink_caption'], true);
                                                $td->appendElement('br', []);
                                                $td->appendElement('plaintext', [], '(' . $row[$key] . ')', true);
                                            } else {
                                                $td->appendElement('plaintext', [], $row[$key], true);
                                            }
                                            break;
                                        case 'action':
                                            $ul = $td->appendElement("ul", ["class" => "actions actions--centered"]);
                                            if ($canEdit) {
                                                $li = $ul->appendElement("li", ['class' => 'droplink']);
                                                $li->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit')], '<i class="ion-android-more-horizontal icon"></i>', true);
                                                $innerDiv = $li->appendElement('div', ['class' => 'dropwrap']);
                                                $innerUl = $innerDiv->appendElement('ul', ['class' => 'linksvertical']);
                                                $innerLiEdit = $innerUl->appendElement('li');
                                                $innerLiEdit->appendElement('a', ['href' => 'javascript:void(0)', 'class' => 'button small green', 'title' => Label::getLabel('LBL_Edit'), "onclick" => "addNavigationLinkForm(" . $row['nlink_nav_id'] . ", " . $row['nlink_id'] . ")"], Label::getLabel('LBL_Edit'), true);
                                                $innerLiDelete = $innerUl->appendElement('li');
                                                $innerLiDelete->appendElement('a', ['href' => "javascript:void(0)", 'class' => 'button small green', 'title' => Label::getLabel('LBL_Delete'), "onclick" => "deleteNavigationLink(" . $row['nlink_nav_id'] . ", " . $row['nlink_id'] . ")"], Label::getLabel('LBL_Delete'), true);
                                            }
                                            break;
                                        default:
                                            $td->appendElement('plaintext', [], $row[$key], true);
                                            break;
                                    }
                                }
                            }
                            /* } */
                            if (count($arrListing) == 0) {
                                $tbl->appendElement('tr')->appendElement('td', ['colspan' => count($arr_flds)], Label::getLabel('LBL_No_Records_Found'));
                            }
                            echo $tbl->getHtml();
                            ?>
                        </div> 
                    </div>
                    <?php if ($shortview != AppConstant::YES) { ?>
                    </section>
                </div>
            </div>
        </div>
    </div>
<?php } ?>
<script>
    $(document).ready(function () {
        $('#pageList').tableDnD({
            onDrop: function (table, row) {
                var order = $.tableDnD.serialize('id');
                fcom.ajax(fcom.makeUrl('Navigations', 'updateNlinkOrder'), order, function (res) { reloadlist(); });
            },
            dragHandle: ".dragHandle",
        });
    });
</script>