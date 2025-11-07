<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<section class="section">
    <div class="sectionhead">
        <h4><?php echo Label::getLabel('LBL_TUTOR_REQUESTS'); ?></h4>
    </div>

    <div class="sectionbody space">
        <?php if (!empty($list)) { ?>
            <div class="tablewrap">
                <table class="table table--hover table-justified">
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
                        $srNo = ($page - 1) * $pageSize;
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

                            $statusClass = 'badge-warning';
                            if ($statusValue === 1) {
                                $statusClass = 'badge-success';
                            } elseif ($statusValue === 2) {
                                $statusClass = 'badge-danger';
                            }
                        ?>
                            <tr>
                                <td><?php echo $srNo; ?></td>
                                <td><?php echo htmlspecialchars($fullName ?: '-'); ?></td>
                                <td>
                                    <?php if (!empty($row['tutreq_email'])) { ?>
                                        <a href="mailto:<?php echo htmlspecialchars($row['tutreq_email']); ?>">
                                            <?php echo htmlspecialchars($row['tutreq_email']); ?>
                                        </a>
                                    <?php } else { ?>
                                        -
                                    <?php } ?>
                                </td>
                                <td><?php echo htmlspecialchars($phone ?: '-'); ?></td>
                                <td style="max-width:250px;">
                                    <?php echo nl2br(htmlspecialchars($row['tutreq_preferred_time'] ?? '-')); ?>
                                </td>
                                <td style="max-width:260px;">
                                    <?php
                                    // NOW showing course titles instead of IDs
                                    echo !empty($row['course_titles'])
                                        ? htmlspecialchars($row['course_titles'])
                                        : '-';
                                    ?>
                                </td>
                                <td>
                                    <span class="badge <?php echo $statusClass; ?>">
                                        <?php echo $statusLabel; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    if (!empty($row['tutreq_added_on'])) {
                                        echo FatDate::format($row['tutreq_added_on']);
                                    } else {
                                        echo '-';
                                    }
                                    ?>
                                </td>
                                <td>
                                    <!-- View -->
                                    <a href="<?php echo MyUtility::makeUrl('TutorRequests', 'view', [$row['tutreq_id']]); ?>"
                                       class="btn btn-clean btn-sm" title="<?php echo Label::getLabel('LBL_VIEW'); ?>">
                                        <i class="fa fa-eye"></i>
                                    </a>

                                    <!-- Accept (Processed) -->
                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'updateStatus'); ?>"
                                          method="post" style="display:inline-block; margin:0 2px;">
                                        <input type="hidden" name="requestId" value="<?php echo (int)$row['tutreq_id']; ?>">
                                        <input type="hidden" name="status" value="1">
                                        <button type="submit" class="btn btn-sm btn-outline-success">
                                            <?php echo Label::getLabel('LBL_ACCEPT'); ?>
                                        </button>
                                    </form>

                                    <!-- Reject -->
                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'updateStatus'); ?>"
                                          method="post" style="display:inline-block; margin:0 2px;">
                                        <input type="hidden" name="requestId" value="<?php echo (int)$row['tutreq_id']; ?>">
                                        <input type="hidden" name="status" value="2">
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            <?php echo Label::getLabel('LBL_REJECT'); ?>
                                        </button>
                                    </form>

                                    <!-- Delete -->
                                    <form action="<?php echo MyUtility::makeUrl('TutorRequests', 'delete'); ?>"
                                          method="post" style="display:inline-block; margin:0 2px;"
                                          onsubmit="return confirm('<?php echo Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_REQUEST'); ?>');">
                                        <input type="hidden" name="requestId" value="<?php echo (int)$row['tutreq_id']; ?>">
                                        <button type="submit" class="btn btn-sm btn-outline-secondary">
                                            <?php echo Label::getLabel('LBL_DELETE'); ?>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>
            </div>

            <!-- Basic pagination info -->
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

            <!-- Simple pagination links -->
            <?php if ($recordCount > $pageSize): ?>
                <div class="pagination">
                    <?php
                    $totalPages = ceil($recordCount / $pageSize);
                    for ($i = 1; $i <= $totalPages; $i++):
                        $active = ($i == $page) ? 'is-active' : '';
                        $url = MyUtility::makeUrl('TutorRequests', 'index', [], '?page=' . $i);
                    ?>
                        <a href="<?php echo $url; ?>" class="page-link <?php echo $active; ?>">
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
