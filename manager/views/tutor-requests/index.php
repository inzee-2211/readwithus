<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<div class="page">
    <div class="fixed_container">
        <div class="row">
            <div class="space">

                <!-- Page Title + Breadcrumb -->
                <div class="page__title">
                    <div class="row">
                        <div class="col--first">
                            <span class="page__icon"><i class="ion-android-contact"></i></span>
                            <h5><?php echo Label::getLabel('LBL_TUTOR_REQUESTS'); ?></h5>
                            <?php $this->includeTemplate('_partial/header/header-breadcrumb.php'); ?>
                        </div>
                        <div class="col--last">
                            <!-- If you want a button later (export etc.), put it here -->
                        </div>
                    </div>
                </div>

                <!-- Listing Section -->
                <section class="section">
                    <div class="sectionhead">
                        <h4><?php echo Label::getLabel('LBL_Listing'); ?></h4>
                    </div>

                    <div class="sectionbody">
                        <?php if (!empty($list)) { ?>

                            <div class="tablewrap">
                                <table class="table table--hovered table-responsive">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th><?php echo Label::getLabel('LBL_NAME'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_EMAIL'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_PHONE'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_PREFERRED_SCENARIO'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_PACKAGES'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_ADDED_ON'); ?></th>
                                            <th><?php echo Label::getLabel('LBL_ACTIONS'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $srNo      = ($page - 1) * $pageSize;
                                        $statusArr = $statusArr ?? [];

                                        foreach ($list as $row) {
                                            $srNo++;
                                            $fullName = trim(($row['tutreq_first_name'] ?? '') . ' ' . ($row['tutreq_last_name'] ?? ''));
                                            $phone    = '';
                                            if (!empty($row['tutreq_phone_code']) || !empty($row['tutreq_phone_number'])) {
                                                $phone = '+' . $row['tutreq_phone_code'] . ' ' . $row['tutreq_phone_number'];
                                            }

                                            $statusValue = (int)($row['tutreq_status'] ?? 0);
                                            $statusLabel = $statusArr[$statusValue] ?? Label::getLabel('LBL_PENDING');

                                            // YoCoach-ish badge classes
                                            $statusClass = 'label--warning';
                                            if ($statusValue === 1) {
                                                $statusClass = 'label--success';
                                            } elseif ($statusValue === 2) {
                                                $statusClass = 'label--danger';
                                            }

                                            // group Level / Subject / Exam board / Tier
                                            $levelId     = (int)($row['tutreq_level_id']     ?? 0);
                                            $subjectId   = (int)($row['tutreq_subject_id']   ?? 0);
                                            $examboardId = (int)($row['tutreq_examboard_id'] ?? 0);
                                            $tierId      = (int)($row['tutreq_tier_id']      ?? 0);

                                            $hasStructured = ($levelId || $subjectId || $examboardId || $tierId);
                                        ?>
                                            <tr>
                                                <td><?php echo $srNo; ?></td>

                                                <!-- Name -->
                                                <td><?php echo htmlspecialchars($fullName ?: '-'); ?></td>

                                                <!-- Email -->
                                                <td>
                                                    <?php if (!empty($row['tutreq_email'])) { ?>
                                                        <a href="mailto:<?php echo htmlspecialchars($row['tutreq_email']); ?>">
                                                            <?php echo htmlspecialchars($row['tutreq_email']); ?>
                                                        </a>
                                                    <?php } else { ?>
                                                        -
                                                    <?php } ?>
                                                </td>

                                                <!-- Phone -->
                                                <td><?php echo htmlspecialchars($phone ?: '-'); ?></td>

                                                <!-- Preferred scenario / notes -->
                                                <td style="max-width:260px;">
                                                    <?php echo nl2br(htmlspecialchars($row['tutreq_preferred_time'] ?? '-')); ?>
                                                </td>

                                                <!-- Packages (group Level / Subject / Exam Board / Tier) -->
                                                <td style="max-width:260px;">
                                                    <?php if ($hasStructured) { ?>
                                                        <div class="tutreq-packages">
                                                            <?php if ($levelId) { ?>
                                                                <div class="tutreq-packages__row">
                                                                    <span class="tutreq-packages__label">
                                                                        <?php echo Label::getLabel('LBL_LEVEL'); ?>:
                                                                    </span>
                                                                    <span class="tutreq-packages__value">
                                                                        #<?php echo $levelId; ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if ($subjectId) { ?>
                                                                <div class="tutreq-packages__row">
                                                                    <span class="tutreq-packages__label">
                                                                        <?php echo Label::getLabel('LBL_SUBJECT'); ?>:
                                                                    </span>
                                                                    <span class="tutreq-packages__value">
                                                                        #<?php echo $subjectId; ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if ($examboardId) { ?>
                                                                <div class="tutreq-packages__row">
                                                                    <span class="tutreq-packages__label">
                                                                        <?php echo Label::getLabel('LBL_EXAM_BOARD'); ?>:
                                                                    </span>
                                                                    <span class="tutreq-packages__value">
                                                                        #<?php echo $examboardId; ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if ($tierId) { ?>
                                                                <div class="tutreq-packages__row">
                                                                    <span class="tutreq-packages__label">
                                                                        <?php echo Label::getLabel('LBL_TIER'); ?>:
                                                                    </span>
                                                                    <span class="tutreq-packages__value">
                                                                        #<?php echo $tierId; ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>

                                                            <?php if (!empty($row['course_titles'])) { ?>
                                                                <div class="tutreq-packages__row tutreq-packages__row--courses">
                                                                    <span class="tutreq-packages__label">
                                                                        <?php echo Label::getLabel('LBL_COURSES'); ?>:
                                                                    </span>
                                                                    <span class="tutreq-packages__value">
                                                                        <?php echo htmlspecialchars($row['course_titles']); ?>
                                                                    </span>
                                                                </div>
                                                            <?php } ?>
                                                        </div>
                                                    <?php } else { ?>
                                                        <?php
                                                        // Fallback for old data where only course_titles exist
                                                        echo !empty($row['course_titles'])
                                                            ? htmlspecialchars($row['course_titles'])
                                                            : '-';
                                                        ?>
                                                    <?php } ?>
                                                </td>

                                                <!-- Status -->
                                                <td>
                                                    <span class="label <?php echo $statusClass; ?>">
                                                        <?php echo $statusLabel; ?>
                                                    </span>
                                                </td>

                                                <!-- Added On -->
                                                <td>
                                                    <?php
                                                    if (!empty($row['tutreq_added_on'])) {
                                                        echo FatDate::format($row['tutreq_added_on']);
                                                    } else {
                                                        echo '-';
                                                    }
                                                    ?>
                                                </td>

                                                <!-- Actions -->
                                                <td>
                                                    <!-- View -->
                                                    <a href="<?php echo MyUtility::makeUrl('TutorRequests', 'view', [$row['tutreq_id']]); ?>"
                                                       class="btn btn--sm btn--bordered"
                                                       title="<?php echo Label::getLabel('LBL_VIEW'); ?>">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <!-- Accept (Processed) -->
                                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'updateStatus'); ?>"
                                                          method="post"
                                                          style="display:inline-block; margin:0 2px;">
                                                        <input type="hidden" name="requestId"
                                                               value="<?php echo (int)$row['tutreq_id']; ?>">
                                                        <input type="hidden" name="status" value="1">
                                                        <button type="submit"
                                                                class="btn btn--sm btn--primary-outline">
                                                            <?php echo Label::getLabel('LBL_ACCEPT'); ?>
                                                        </button>
                                                    </form>

                                                    <!-- Reject -->
                                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'updateStatus'); ?>"
                                                          method="post"
                                                          style="display:inline-block; margin:0 2px;">
                                                        <input type="hidden" name="requestId"
                                                               value="<?php echo (int)$row['tutreq_id']; ?>">
                                                        <input type="hidden" name="status" value="2">
                                                        <button type="submit"
                                                                class="btn btn--sm btn--danger-outline">
                                                            <?php echo Label::getLabel('LBL_REJECT'); ?>
                                                        </button>
                                                    </form>

                                                    <!-- Delete -->
                                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'delete'); ?>"
                                                          method="post"
                                                          style="display:inline-block; margin:0 2px;"
                                                          onsubmit="return confirm('<?php echo Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_REQUEST'); ?>');">
                                                        <input type="hidden" name="requestId"
                                                               value="<?php echo (int)$row['tutreq_id']; ?>">
                                                        <button type="submit"
                                                                class="btn btn--sm btn--bordered">
                                                            <?php echo Label::getLabel('LBL_DELETE'); ?>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination info -->
                            <div class="footinfo">
                                <?php
                                echo sprintf(
                                    Label::getLabel('LBL_SHOWING_%s_TO_%s_OF_%s'),
                                    ($page - 1) * $pageSize + 1,
                                    min($page * $pageSize, $recordCount),
                                    $recordCount
                                );
                                ?>
                            </div>

                            <!-- Simple pagination -->
                            <?php if ($recordCount > $pageSize): ?>
                                <div class="pagination">
                                    <?php
                                    $totalPages = ceil($recordCount / $pageSize);
                                    for ($i = 1; $i <= $totalPages; $i++):
                                        $active = ($i == $page) ? 'is-active' : '';
                                        $url    = MyUtility::makeUrl('TutorRequests', 'index', [], '?page=' . $i);
                                    ?>
                                        <a href="<?php echo $url; ?>"
                                           class="page-link <?php echo $active; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    <?php endfor; ?>
                                </div>
                            <?php endif; ?>

                        <?php } else { ?>
                            <div class="message message--info">
                                <?php echo Label::getLabel('LBL_NO_TUTOR_REQUESTS_FOUND'); ?>
                            </div>
                        <?php } ?>
                    </div>
                </section>
            </div>
        </div>
    </div>
</div>

<style>
/* Tiny helper styles to keep it neat and YoCoach-ish */
.tutreq-packages__row {
    display: flex;
    flex-wrap: wrap;
    font-size: 12px;
    margin-bottom: 3px;
}
.tutreq-packages__label {
    font-weight: 500;
    color: #777;
    margin-right: 4px;
}
.tutreq-packages__value {
    font-weight: 600;
    color: #333;
}
.tutreq-packages__row--courses .tutreq-packages__label {
    margin-top: 2px;
}
</style>
