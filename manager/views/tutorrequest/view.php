<div class="sectionhead">
    <h4><?php echo Label::getLabel('LBL_COURSE_MATERIALS_WITH_QUESTIONS'); ?></h4>
</div>
<div class="tabs_panel">
    <?php if (!empty($finalData)) { ?>
        <?php foreach ($finalData as $material) { ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5><?php echo Label::getLabel('LBL_SUBTOPIC'); ?>: <?php
                    
                     $db = FatApp::getDb();
        $query = "SELECT id, topic FROM course_topics where id=".$material['subtopic']; // Replace with actual table name
        $result = $db->query($query);
         $levels = $db->fetchAll($result);
         echo ucfirst($levels[0]['topic']);
            
                    
                    
                    ?></h5>
                    <p>
                        <strong><?php echo Label::getLabel('LBL_VIDEO_URL'); ?>:</strong> 
                        <a href="<?php echo $material['video_url']; ?>" target="_blank"><?php echo $material['video_url']; ?></a>
                        <br>
                        <strong><?php echo Label::getLabel('LBL_PREVIOUS_PAPER'); ?>:</strong> 
                        <?php if (!empty($material['previous_paper_pdf'])) { ?>
                            <?php
$baseUrl = FatUtility::generateFullUrl('', '', [], CONF_WEBROOT_FRONT_URL); 
$pdfPath = $baseUrl .'' . $material['previous_paper_pdf'];
?>
<a href="<?php echo $pdfPath; ?>" target="_blank">Download</a>
                        <?php } else { echo Label::getLabel('LBL_NA'); } ?>
                    </p>
                </div>
                <div class="card-body">
                    <?php if (!empty($material['questions'])) { ?>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th><?php echo Label::getLabel('LBL_QUESTION'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_OPTIONS'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_CORRECT_ANSWER'); ?></th>
                                    <th><?php echo Label::getLabel('LBL_DIFFICULTY'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($material['questions'] as $index => $question) { ?>
                                    <tr>
                                        <td><?php echo $index + 1; ?></td>
                                        <td><?php echo $question['question_title']; ?></td>
                                        <td>
                                            A. <?php echo $question['answer_a']; ?><br>
                                            B. <?php echo $question['answer_b']; ?><br>
                                            C. <?php echo $question['answer_c']; ?><br>
                                            D. <?php echo $question['answer_d']; ?>
                                        </td>
                                        <td><?php echo $question['correct_answer']; ?></td>
                                        <td><?php echo $question['difficult_level']; ?></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    <?php } else { ?>
                        <p><?php echo Label::getLabel('LBL_NO_QUESTIONS_FOUND'); ?></p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p><?php echo Label::getLabel('LBL_NO_COURSE_MATERIAL_FOUND'); ?></p>
    <?php } ?>
</div>
