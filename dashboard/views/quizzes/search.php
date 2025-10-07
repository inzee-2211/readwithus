<?php
defined('SYSTEM_INIT') or die('Invalid Usage.');


if (count($courses) == 0) {
    $link = MyUtility::makeFullUrl('Teachers', '', [], CONF_WEBROOT_FRONTEND);
    $variables = ['msgHeading' => Label::getLabel('LBL_NO_QUIZ_FOUND')];
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
            <!-- <th><?php echo $nameLabel = ($siteUserType == User::LEARNER) ? Label::getLabel('LBL_TEACHER') : Label::getLabel('LBL_LEARNER'); ?></th> -->
            <th><?php echo $titleLabel = Label::getLabel('LBL_TITLE'); ?></th>
            <th><?php echo $typeLabel = Label::getLabel('LBL_TYPE'); ?></th>
            <th><?php echo $languageLabel = Label::getLabel('LBL_NO_OF_QUESTIONS'); ?></th>
            <th><?php echo $lessonLabel = Label::getLabel('LBL_DURATION'); ?></th>
            <th><?php echo $lessonLabel = Label::getLabel('LBL_PASS_PERCENTAGE'); ?></th>
            <th><?php echo $statusLabel = Label::getLabel('LBL_STATUS'); ?></th>
            <th><?php echo $added_onLabel = Label::getLabel('LBL_DATE'); ?></th>
            <th><?php echo $actionLabel = Label::getLabel('LBL_ACTIONS'); ?></th>
        </tr>
        <?php
        $naLabel = Label::getLabel('LBL_N/A');
        $statuses = Subscription::getStatuses();
 
        foreach ($courses as $quiz) {
            ?>
            <tr>
               
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $titleLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php echo $quiz['quiz_title']; ?>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo $typeLabel; ?></div>
                        <div class="flex-cell__content">
                            <?php   echo 'Auto Graded';
                            ?>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'No of Questions'; ?></div>
                        <div class="flex-cell__content"><?php echo $quiz['question_count']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Duration'; ?></div>
                        <div class="flex-cell__content"><?php echo $quiz['quiz_duration'].' min'; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Pass Percent'; ?></div>
                        <div class="flex-cell__content"><?php echo $quiz['quiz_pass_percentage'].'%'; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Status'; ?></div>
                        <div class="flex-cell__content">
                        <?php 
                        if($quiz['quiz_status']==1){
                            echo 'Published';
                        }else{
                            echo 'Drafted';
                        } ?>
                    
                        </div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo added_onLabel; ?></div>
                        <div class="flex-cell__content"><?php echo $quiz['quiz_created']; ?></div>
                    </div>
                </td>
                <td>
                    <div class="flex-cell">
                        <div class="flex-cell__label"><?php echo 'Action'; ?></div>
                        <div class="flex-cell__content">

                        <!-- <a href="javascript:void(0);" onclick="addForm('<?php echo $quiz['quiz_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                        <svg class="icon icon--edit icon--small">
                                        <use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#edit'; ?>"></use>
                                        </svg>
                                        <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_EDIT'); ?></div>
                                    </a> -->

                                    <a href="<?php echo MyUtility::makeUrl('Quizzes', 'form', [$quiz['quiz_id']]); ?>" title="<?php echo Label::getLabel('LBL_EDIT'); ?>" class="btn btn--equal btn--shadow btn--bordered is-hover margin-1">
                            <svg class="icon icon--edit icon--small">
                                <use xlink:href="<?php echo CONF_WEBROOT_URL; ?>images/sprite.svg#edit"></use>
                            </svg>
                            <div class="tooltip tooltip--top bg-black">
                                <?php echo Label::getLabel('LBL_EDIT'); ?>
                            </div>
                        </a>

                       
                            <?php   ?>
                                <a href="javascript:void(0);" onclick="cancelForm('<?php echo $quiz['quiz_id']; ?>');" class="btn btn--bordered btn--shadow btn--equal margin-1 is-hover">
                                    <svg class="icon icon--cancel icon--small"><use xlink:href="<?php echo CONF_WEBROOT_URL . 'images/sprite.svg#delete-icon'; ?>"></use></svg>
                                    <div class="tooltip tooltip--top bg-black"><?php echo Label::getLabel('LBL_DELETE'); ?></div>
                                </a>
                            <?php   ?>
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













 

 