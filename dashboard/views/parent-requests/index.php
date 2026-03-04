<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<section class="section">
    <div class="container container--fixed">
        <div class="page-header">
            <div class="page-title">
                <h1>
                    <?php echo Label::getLabel('LBL_PARENT_REQUESTS'); ?>
                </h1>
                <p>
                    <?php echo Label::getLabel('LBL_MANAGE_YOUR_PARENT_CONNECTIONS_AND_REQUESTS'); ?>
                </p>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div class="card-modern shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-body p-0">
                        <?php if (empty($requests)) { ?>
                            <div class="text-center py-5">
                                <div class="empty-state-icon mb-3" style="font-size: 3rem; color: #cbd5e1;">
                                    <i class="ion-person-stalker"></i>
                                </div>
                                <h3 class="h5 text-slate-600">
                                    <?php echo Label::getLabel('LBL_NO_REQUESTS_FOUND'); ?>
                                </h3>
                                <p class="text-slate-400">
                                    <?php echo Label::getLabel('LBL_WHEN_A_PARENT_REQUESTS_TO_LINK_YOUR_ACCOUNT_IT_WILL_APPEAR_HERE'); ?>
                                </p>
                            </div>
                        <?php } else { ?>
                            <div class="table-responsive">
                                <table class="table table-modern align-middle mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="ps-4 py-3 text-uppercase fs-xs fw-bold text-slate-500">
                                                <?php echo Label::getLabel('LBL_PARENT_DETAILS'); ?>
                                            </th>
                                            <th class="py-3 text-uppercase fs-xs fw-bold text-slate-500">
                                                <?php echo Label::getLabel('LBL_RELATION'); ?>
                                            </th>
                                            <th class="py-3 text-uppercase fs-xs fw-bold text-slate-500">
                                                <?php echo Label::getLabel('LBL_STATUS'); ?>
                                            </th>
                                            <th class="pe-4 py-3 text-uppercase fs-xs fw-bold text-slate-500 text-end">
                                                <?php echo Label::getLabel('LBL_ACTIONS'); ?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($requests as $req) {
                                            $parentName = $req['parent_first_name'] . ' ' . $req['parent_last_name'];
                                            $statusClass = '';
                                            $statusLabel = '';
                                            switch ($req['parstd_status']) {
                                                case ParentRequestsController::STATUS_PENDING:
                                                    $statusClass = 'badge-warning-soft';
                                                    $statusLabel = Label::getLabel('LBL_PENDING');
                                                    break;
                                                case ParentRequestsController::STATUS_APPROVED:
                                                    $statusClass = 'badge-success-soft';
                                                    $statusLabel = Label::getLabel('LBL_APPROVED');
                                                    break;
                                                case ParentRequestsController::STATUS_REJECTED:
                                                    $statusClass = 'badge-danger-soft';
                                                    $statusLabel = Label::getLabel('LBL_REJECTED');
                                                    break;
                                            }
                                            ?>
                                            <tr>
                                                <td class="ps-4 py-4">
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm rounded-circle bg-primary-soft text-primary d-flex align-items-center justify-content-center fw-bold me-3"
                                                            style="width: 40px; height: 40px;">
                                                            <?php echo strtoupper(substr($req['parent_first_name'], 0, 1)); ?>
                                                        </div>
                                                        <div>
                                                            <div class="fw-bold text-slate-700">
                                                                <?php echo htmlspecialchars($parentName); ?>
                                                            </div>
                                                            <div class="fs-sm text-slate-400">
                                                                <?php echo htmlspecialchars($req['parent_email']); ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-4">
                                                    <span class="text-slate-600 fw-medium">
                                                        <?php echo htmlspecialchars($req['parstd_relation']); ?>
                                                    </span>
                                                </td>
                                                <td class="py-4">
                                                    <span
                                                        class="badge <?php echo $statusClass; ?> rounded-pill px-3 py-2 fw-bold fs-xs">
                                                        <?php echo $statusLabel; ?>
                                                    </span>
                                                </td>
                                                <td class="pe-4 py-4 text-end">
                                                    <?php if ($req['parstd_status'] == ParentRequestsController::STATUS_PENDING) { ?>
                                                        <button onclick="updateRequest(<?php echo $req['parstd_id']; ?>, 'accept')"
                                                            class="btn btn-success btn-sm rounded-3 px-3 me-2">
                                                            <i class="ion-checkmark-round me-1"></i>
                                                            <?php echo Label::getLabel('LBL_ACCEPT'); ?>
                                                        </button>
                                                        <button onclick="updateRequest(<?php echo $req['parstd_id']; ?>, 'reject')"
                                                            class="btn btn-outline-danger btn-sm rounded-3 px-3">
                                                            <i class="ion-close-round me-1"></i>
                                                            <?php echo Label::getLabel('LBL_REJECT'); ?>
                                                        </button>
                                                    <?php } else { ?>
                                                        <button onclick="removeLink(<?php echo $req['parstd_id']; ?>)"
                                                            class="btn btn-link text-danger btn-sm text-decoration-none">
                                                            <i class="ion-trash-a me-1"></i>
                                                            <?php echo Label::getLabel('LBL_REMOVE'); ?>
                                                        </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<style>
    .card-modern {
        border: 1px solid #edf2f7;
        background: #fff;
    }

    .primary-soft {
        background-color: #e0f2fe;
        color: #0ea5e9;
    }

    .badge-success-soft {
        background-color: #dcfce7;
        color: #16a34a;
    }

    .badge-warning-soft {
        background-color: #fef3c7;
        color: #d97706;
    }

    .badge-danger-soft {
        background-color: #fee2e2;
        color: #dc2626;
    }

    .text-slate-700 {
        color: #334155;
    }

    .text-slate-600 {
        color: #475569;
    }

    .text-slate-500 {
        color: #64748b;
    }

    .text-slate-400 {
        color: #94a3b8;
    }

    .fs-xs {
        font-size: 0.75rem;
    }

    .avatar-sm {
        font-size: 1rem;
    }

    .bg-primary-soft {
        background: #eff6ff;
        color: #2563eb;
    }

    .bg-light {
        background-color: #f8fafc !important;
    }
</style>

<script>
    function updateRequest(id, action) {
        if (!confirm('<?php echo Label::getLabel('LBL_ARE_YOU_SURE_?'); ?>')) return;

        let url = action === 'accept' ?
            fcom.makeUrl('ParentRequests', 'acceptRequest', [id]) :
            fcom.makeUrl('ParentRequests', 'rejectRequest', [id]);

        fcom.updateWithAjax(url, '', function (res) {
            location.reload();
        });
    }

    function removeLink(id) {
        if (!confirm('<?php echo Label::getLabel('LBL_ARE_YOU_SURE_YOU_WANT_TO_REMOVE_THIS_LINK_?'); ?>')) return;

        fcom.updateWithAjax(fcom.makeUrl('ParentRequests', 'removeLink', [id]), '', function (res) {
            location.reload();
        });
    }
</script>