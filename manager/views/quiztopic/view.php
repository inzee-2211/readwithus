<div class="sectionhead">
    <h4><?php echo Label::getLabel('LBL_COURSE_MATERIALS_WITH_QUESTIONS'); ?></h4>
</div>
<div class="tabs_panel">
    <?php if (!empty($finalData)) { ?>
        <?php foreach ($finalData as $material) { ?>
            <div class="card mb-4">
              


<div class="card-header bg-light border-bottom rounded-top">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <!-- Left: Subtopic and resource info -->
                <div><div class="buttons-group" style="padding: 4px;align-content: right;text-align: right;">
                <a href="javascript:void(0);" onclick="deleteMaterial(<?php echo (int)$material['id']; ?>)" class="btn-primary">Delete</a>
                </div>
            <h5 class="mb-2">
                <?php
                $db = FatApp::getDb();
                $query = "SELECT topic FROM course_topics WHERE id = " . (int)$material['subtopic'];
                $result = $db->query($query);
                $levels = $db->fetchAll($result);
                echo Label::getLabel('LBL_SUBTOPIC') . ': ' . ucfirst($levels[0]['topic']);
                ?>
            </h5>
 
            <p class="mb-1">
                <strong><?php echo Label::getLabel('LBL_VIDEO_URL'); ?>:</strong>
                <a href="<?php echo $material['video_url']; ?>" target="_blank"><?php echo $material['video_url']; ?></a>
            </p>

   


            <p class="mb-0">
                <strong><?php echo Label::getLabel('LBL_PREVIOUS_PAPER'); ?>:</strong>
                <?php if (!empty($material['previous_paper_pdf'])) {
                    $baseUrl = FatUtility::generateFullUrl('', '', [], CONF_WEBROOT_FRONT_URL);
                    $pdfPath = $baseUrl . $material['previous_paper_pdf'];
                    ?>
                    <a href="<?php echo $pdfPath; ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                        <?php echo Label::getLabel('LBL_DOWNLOAD'); ?>
                    </a>
                <?php } else {
                    echo '<span class="text-muted">' . Label::getLabel('LBL_NA') . '</span>';
                } ?>
            </p>
        </div>

        <!-- Right: Icon-only upload button -->
        <div class="text-end mt-3">
        <form id="csvUploadForm" enctype="multipart/form-data">
        <input type="hidden" name="subtopic_id" value="<?php echo (int)$material['subtopic']; ?>">
        <input type="hidden" name="course_id" value="<?php echo (int)$material['course_id']; ?>">

        <!-- Stylish Upload Button -->
        <label for="uploadCsvFile" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1"
        title="Upload Question Bank CSV">
        <i class="fas fa-upload"></i> Upload CSV
        </label>

        <!-- Hidden input -->
        <input type="file" name="question_csv" id="uploadCsvFile_<?php echo $material['subtopic']; ?>"
        accept=".csv"  style="border:none;">
     </form>
    
        </div>

    </div>
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

<script>
    $(document).ready(function () {
    $('input[type="file"][name="question_csv"]').on('change', function () {
        const form = this.closest('form');
        uploadQuestionBank(form);
    });
});

uploadQuestionBank = function (frm) {
    if (!frm) return;

    let formData = new FormData(frm);

    $.ajax({
        url: fcom.makeUrl('Coursemanagement', 'uploadQuestionBank'),
        type: 'POST',
        data: formData,
        contentType: false,
        processData: false,
        dataType: 'json',
        success: function (res) {
             location.reload(); 
            if (res.status === 1) {
                  location.reload(); 
               // $.mbsmessage(res.msg, true, 'alert--success');
            } else {
             //   $.mbsmessage(res.msg || 'Upload failed', false, 'alert--danger');
            }
        },
        error: function (xhr) {
            console.error(xhr.responseText);
           // $.mbsmessage('Server error during upload.', false, 'alert--danger');
        }
    });
};

</script>