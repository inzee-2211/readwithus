<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($images)) { ?>
    <ul class="grids--onethird" id="<?php if ($canEdit) { ?>sortable<?php } ?>">
        <?php
        $count = 1;
        foreach ($images as $file_id => $row) {
            ?>
            <li id="<?php echo $row['file_id']; ?>">
                <div class="logoWrap">
                    <div class="logothumb"> 
                        <img src="<?php echo MyUtility::makeUrl('Image', 'showById', [$row['file_id'], Afile::SIZE_MEDIUM]) . '?' . time(); ?>" title="<?php echo $row['file_name']; ?>" alt="<?php echo $row['file_name']; ?>"> 
                        <?php if ($canEdit) { ?> 
                            <a class="deleteLink white" href="javascript:void(0);" title="Delete <?php echo $row['file_name']; ?>" onclick="deleteImage(<?php echo $row['file_record_id']; ?>, <?php echo $row['file_id']; ?>, <?php echo $row['file_lang_id']; ?>);" class="delete"><i class="ion-close-round"></i></a>
                            <?php } ?>
                    </div>
                    <?php
                    $lang_name = Label::getLabel('LBL_All');
                    if ($row['file_lang_id'] > 0) {
                        $lang_name = $languages[$row['file_lang_id']];
                        ?>
                    <?php } ?>
                    <small class=""><strong> <?php echo Label::getLabel('LBL_Language'); ?>:</strong> <?php echo $lang_name; ?></small>
                </div>
            </li>
            <?php
            $count++;
        }
        ?>
    </ul>
<?php } ?>