<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');

if (count($allClasses) == 0) {
    $link = MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND);
    $variables = ['msgHeading' => Label::getLabel('LBL_NO_QUESTION_FOUND')];
    if ($siteUserType == User::LEARNER) {
        $variables['btn'] = '<a href="' . $link . '" class="btn btn--primary">' . Label::getLabel('LBL_FIND_TEACHER') . '</a>';
    }
    $this->includeTemplate('_partial/no-record-found.php', $variables, false);
    return;
}
?>

<div class="table-scroll">
    <table class="table table--styled table--responsive table--aligned-middle">
        <tr class="title-row">
            <th>
                <input type="checkbox" id="selectAllQuestions" onclick="toggleSelectAllQuestions(this);">
            </th>
            <th><?php echo $titleLabel = Label::getLabel('LBL_TITLE'); ?></th>
            <th><?php echo $typeLabel = Label::getLabel('LBL_TYPE'); ?></th>
            <th><?php echo $languageLabel = Label::getLabel('LBL_CATEGORY'); ?></th>
            <th><?php echo $lessonLabel = Label::getLabel('LBL_SUBCATGORY'); ?></th>
            <th><?php echo $statusLabel = Label::getLabel('LBL_STATUS'); ?></th>
            <th><?php echo $added_onLabel = Label::getLabel('LBL_ADDED_ON'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_ACTIONS'); ?></th>
        </tr>
        <?php
        $naLabel = Label::getLabel('LBL_N/A');
        $statuses = Subscription::getStatuses();

        foreach ($allClasses as $question) {
            ?>
            <tr>
                <!-- Checkbox column -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"></div>
                        <div class="flex-cell__content">
                            <input
                                type="checkbox"
                                class="question-select"
                                name="question_ids[]"
                                value="<?php echo $question['question_id']; ?>"
                            >
                        </div>
                    </div>
                </td>

                <!-- Title -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $titleLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php echo $question['question_title']; ?>
                        </div>
                    </div>
                </td>

                <!-- Type -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $typeLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php
                            $question_type = $question['question_type'];
                            if ($question_type == 1) {
                                echo 'Single Choice Question.';
                            } elseif ($question_type == 2) {
                                echo 'Multiple Choice Question.';
                            } elseif ($question_type == 3) {
                                echo 'Text-based Question.';
                            } else {
                                echo 'Invalid question type.';
                            }
                            ?>
                        </div>
                    </div>
                </td>

                <!-- Category -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Category'; ?></div>
                        <div class="flex-cell__content"><?php echo $question['catname']; ?></div>
                    </div>
                </td>

                <!-- Subcategory -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'SubCategory'; ?></div>
                        <div class="flex-cell__content"><?php echo $question['subcatname']; ?></div>
                    </div>
                </td>

                <!-- Status -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Status'; ?></div>
                        <div class="flex-cell__content">
                            <?php
                            if ($question['question_status'] == 1) {
                                echo 'Active';
                            } else {
                                echo 'Inactive';
                            }
                            ?>
                        </div>
                    </div>
                </td>

                <!-- Added On -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $added_onLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $question['question_added_on']; ?></div>
                    </div>
                </td>

                <!-- Actions -->
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Action'; ?></div>
                        <div class="flex-cell__content">
                            <!-- Single delete (existing logic kept as-is) -->
                            <a href="javascript:void(0);" onclick="deleteForm('<?php echo $question['question_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                <svg class="icon icon--cancel icon--small">
                                    <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#delete-icon'; ?>"></use>
                                </svg>
                                <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_DELETE'); ?></div>
                            </a>
                        </div>
                    </div>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>
<?php

$pagingArr = [
    'pageSize' => $post['pagesize'],
    'page' => $post['pageno'],
    'recordCount' => $recordCount,
    'pageCount' => ceil($recordCount / $post['pagesize'])
];

$this->includeTemplate('_partial/pagination.php', $pagingArr, false);
echo FatUtility::createHiddenFormFromData($post, ['name' => 'frmSearchPaging']);
?>
