<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>
<?php if (!empty($image)) { ?>
    <ul class="grids--onethird" id="<?php if ($canEdit) { ?>sortable<?php } ?>">
        <li id="<?php echo $image['file_id']; ?>">
            <div class="logoWrap">
                <div class="logothumb">
                    <img src="<?php echo MyUtility::makeUrl('image', 'show', [Afile::TYPE_OPENGRAPH_IMAGE, $image['file_record_id'], Afile::SIZE_SMALL, $image['file_lang_id']]) . '?' . time(); ?>" title="<?php echo $image['file_name']; ?>" alt="<?php echo $image['file_name']; ?>">
                    <?php if ($canEdit) { ?>
                        <a class="deleteLink white" href="javascript:void(0);" title="Delete <?php echo $image['file_name']; ?>" onclick="deleteImage(<?php echo $image['file_record_id']; ?>, <?php echo $image['file_lang_id']; ?>, );" class="delete"><i class="ion-close-round"></i></a>
                        <?php } ?>
                </div>
            </div>
        </li>
    </ul>
<?php } ?>