<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
    .quiz-results-card {
        background: #ffffff;
        border-radius: 18px;
        padding: 18px 20px;
        box-shadow: 0 8px 20px rgba(15, 23, 42, 0.08);
        border: 1px solid #e5e7eb;
        overflow: hidden;
    }

    .quiz-results-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
    }
    .quiz-results-title {
        font-size: 18px;
        font-weight: 600;
        color: #111827; /* dark heading */
    }
    .quiz-results-subtitle {
        font-size: 13px;
        color: #6b7280; /* muted gray */
    }

    .quiz-results-table-wrap {
        width: 100%;
        overflow-x: auto;
    }

    .quiz-results-table {
        width: 100%;
        border-collapse: collapse;
        min-width: 640px;
    }

    .quiz-results-table thead tr {
        background: #f3f4f6; /* light gray header */
    }

    .quiz-results-table th,
    .quiz-results-table td {
        padding: 10px 12px;
        text-align: left;
        font-size: 13px;
        border-bottom: 1px solid #e5e7eb;
        white-space: nowrap;
    }

    .quiz-results-table th {
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.06em;
        font-size: 11px;
    }

    .quiz-results-table tbody tr {
        transition: background 0.15s ease;
    }

    .quiz-results-table tbody tr:hover {
        background: #f9fafb; /* subtle hover */
    }

    .quiz-course-link {
        color: #111827;
        font-weight: 500;
        text-decoration: none;
    }

    .quiz-course-link span {
        font-size: 11px;
        color: #9ca3af;
        margin-left: 4px;
    }

    .quiz-course-link:hover {
        text-decoration: underline;
    }

    .quiz-badge {
        display: inline-flex;
        align-items: center;
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 0.06em;
        text-transform: uppercase;
    }

    .quiz-badge--pass {
        background: #ecfdf3;
        color: #16a34a;
        border: 1px solid #bbf7d0;
    }

    .quiz-badge--fail {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .quiz-score-text {
        font-weight: 500;
        color: #111827;
    }

    .quiz-score-text span {
        font-size: 11px;
        color: #9ca3af;
        margin-left: 3px;
    }

    .quiz-percentage-pill {
        border-radius: 999px;
        padding: 4px 10px;
        font-size: 12px;
        font-weight: 600;
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .quiz-date-text {
        font-size: 12px;
        color: #374151;
    }

    .quiz-date-text span {
        display: block;
        font-size: 11px;
        color: #9ca3af;
    }

    .quiz-pagination {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-top: 12px;
        gap: 6px;
        font-size: 12px;
        color: #6b7280;
    }

    .quiz-pagination button {
        background: #ffffff;
        border-radius: 999px;
        border: 1px solid #d1d5db;
        padding: 4px 10px;
        color: #374151;
        cursor: pointer;
        font-size: 12px;
    }

    .quiz-pagination button:hover:not(:disabled) {
        background: #f3f4f6;
    }

    .quiz-pagination button:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>


<?php
/** @var array $quizResults */
/** @var int   $recordCount */
/** @var int   $pageCount */
/** @var array $post */

if (empty($quizResults)) {
    $this->includeTemplate('_partial/no-record-found.php');
    return;
}

$currentPage = (int)($post['pageno'] ?? 1);
?>

<div class="quiz-results-card">
    <div class="quiz-results-header">
        <div>
            <div class="quiz-results-title">
                <?php echo Label::getLabel('LBL_YOUR_EXAM_HISTORY'); ?>
            </div>
            <div class="quiz-results-subtitle">
                <?php echo Label::getLabel('LBL_VIEW_ALL_COMPLETED_QUIZZES_AND_THEIR_RESULTS'); ?>
            </div>
        </div>
        <div class="quiz-results-subtitle">
            <?php echo sprintf(Label::getLabel('LBL_TOTAL_ATTEMPTS_:_%d'), $recordCount); ?>
        </div>
    </div>

    <div class="quiz-results-table-wrap">
        <table class="quiz-results-table">
            <thead>
                <tr>
                    <th><?php echo Label::getLabel('LBL_COURSE'); ?></th>
                    <th><?php echo Label::getLabel('LBL_DATE'); ?></th>
                    <th><?php echo Label::getLabel('LBL_SCORE'); ?></th>
                    <th><?php echo Label::getLabel('LBL_PERCENTAGE'); ?></th>
                    <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($quizResults as $row) {
                    $courseTitle = !empty($row['course_title']) ? $row['course_title'] : $row['course_slug'];
                    $attemptDate = !empty($row['quiz_added_on'])
                        ? date('d M Y', strtotime($row['quiz_added_on']))
                        : '-';

                    $attemptTime = !empty($row['quiz_added_on'])
                        ? date('H:i', strtotime($row['quiz_added_on']))
                        : '';

                    $percentageLabel = $row['percentage'] !== null && $row['percentage'] !== ''
                        ? rtrim($row['percentage'], '%') . '%'
                        : '-';

                    $scoreText = ($row['score'] ?? '') . ' / ' . ($row['total_marks'] ?? '');
                    $isPass = (strtolower($row['result_status']) === 'pass');
                    $courseUrl = !empty($row['course_slug'])
                        ? MyUtility::makeUrl('Courses', 'view', [$row['course_slug']])
                        : 'javascript:void(0)';
                    ?>
                    <tr>
                        <td>
                            <?php if (!empty($row['course_slug'])) { ?>
                                <a href="<?php echo $courseUrl; ?>" class="quiz-course-link">
                                    <?php echo htmlspecialchars($courseTitle); ?>
                                    <span>#<?php echo (int)$row['course_id']; ?></span>
                                </a>
                            <?php } else { ?>
                                <span class="quiz-course-link">
                                    <?php echo htmlspecialchars($courseTitle ?: Label::getLabel('LBL_UNTITLED_COURSE')); ?>
                                    <span>#<?php echo (int)$row['course_id']; ?></span>
                                </span>
                            <?php } ?>
                        </td>
                        <td>
                            <div class="quiz-date-text">
                                <?php echo $attemptDate; ?>
                                <?php if ($attemptTime) { ?>
                                    <span><?php echo $attemptTime; ?></span>
                                <?php } ?>
                            </div>
                        </td>
                        <td>
                            <div class="quiz-score-text">
                                <?php echo $scoreText; ?>
                                <span><?php echo Label::getLabel('LBL_RAW_SCORE'); ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="quiz-percentage-pill">
                                <?php echo $percentageLabel; ?>
                            </span>
                        </td>
                        <td>
                            <span class="quiz-badge <?php echo $isPass ? 'quiz-badge--pass' : 'quiz-badge--fail'; ?>">
                                <?php echo $isPass ? Label::getLabel('LBL_PASS') : Label::getLabel('LBL_FAIL'); ?>
                            </span>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <?php if ($pageCount > 1) { ?>
        <div class="quiz-pagination">
            <span>
                <?php
                echo sprintf(
                    Label::getLabel('LBL_PAGE_%d_OF_%d'),
                    $currentPage,
                    $pageCount
                );
                ?>
            </span>
            <button
                type="button"
                onclick="goToQuizPage(<?php echo max(1, $currentPage - 1); ?>)"
                <?php echo ($currentPage <= 1) ? 'disabled' : ''; ?>
            >
                <?php echo Label::getLabel('LBL_PREV'); ?>
            </button>
            <button
                type="button"
                onclick="goToQuizPage(<?php echo min($pageCount, $currentPage + 1); ?>)"
                <?php echo ($currentPage >= $pageCount) ? 'disabled' : ''; ?>
            >
                <?php echo Label::getLabel('LBL_NEXT'); ?>
            </button>
        </div>
    <?php } ?>
</div>

<script>
    function goToQuizPage(page) {
        if (!document.frmLessonSearch) return;
        document.frmLessonSearch.pageno.value = page;
        search(document.frmLessonSearch);
    }
</script>
