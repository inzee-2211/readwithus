<?php defined('SYSTEM_INIT') or die('Invalid Usage.'); ?>

<style>
    :root {
        --primary-gradient: linear-gradient(135deg, #2dadff 0%, #1a9fff 100%);
        --surface-glass: rgba(255, 255, 255, 0.7);
        --border-glass: rgba(255, 255, 255, 0.3);
        --shadow-premium: 0 20px 40px rgba(0, 0, 0, 0.05);
        --text-main: #1e293b;
        --text-muted: #64748b;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .parent-requests-page {
        padding: 40px 0;
        animation: fadeInUp 0.6s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .page-title-premium {
        font-size: 2.75rem;
        font-weight: 900;
        letter-spacing: -0.04em;
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        margin-bottom: 8px;
    }

    .page-subtitle {
        font-size: 1.125rem;
        color: var(--text-muted);
        margin-bottom: 40px;
    }

    .premium-card {
        background: #fff;
        border-radius: 32px;
        border: 1px solid #f1f5f9;
        overflow: hidden;
        box-shadow: var(--shadow-premium);
    }

    .table-premium thead th {
        background: #f8fafc;
        padding: 24px 32px;
        color: var(--text-muted);
        font-weight: 800;
        font-size: 0.813rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        border-bottom: 1px solid #f1f5f9;
    }

    .table-premium tbody td {
        padding: 32px;
        vertical-align: middle;
        border-bottom: 1px solid #f8fafc;
    }

    .parent-meta {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .parent-avatar-pill {
        width: 54px;
        height: 54px;
        border-radius: 18px;
        background: var(--primary-gradient);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        font-weight: 800;
        box-shadow: 0 8px 16px rgba(45, 173, 255, 0.2);
    }

    .parent-name {
        font-weight: 800;
        color: var(--text-main);
        font-size: 1.125rem;
        display: block;
    }

    .parent-email {
        font-size: 0.875rem;
        color: var(--text-muted);
        display: block;
    }

    .relation-badge {
        background: #f1f5f9;
        color: var(--text-main);
        padding: 8px 16px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.938rem;
    }

    .badge-premium {
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 800;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .badge-pending {
        background: #fffbeb;
        color: #d97706;
        border: 1px solid #fef3c7;
    }

    .badge-approved {
        background: #f0fdf4;
        color: #16a34a;
        border: 1px solid #dcfce7;
    }

    .badge-rejected {
        background: #fef2f2;
        color: #dc2626;
        border: 1px solid #fee2e2;
    }

    .btn-action-premium {
        padding: 12px 24px;
        border-radius: 14px;
        font-weight: 800;
        font-size: 0.875rem;
        transition: all 0.3s;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        border: none;
    }

    .btn-accept {
        background: var(--primary-gradient);
        color: #fff !important;
        box-shadow: 0 8px 16px rgba(45, 173, 255, 0.2);
    }

    .btn-accept:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 24px rgba(45, 173, 255, 0.3);
    }

    .btn-reject {
        background: #f8fafc;
        color: #ef4444 !important;
        border: 1px solid #f1f5f9;
    }

    .btn-reject:hover {
        background: #fef2f2;
        border-color: #fee2e2;
    }

    .empty-state-premium {
        padding: 100px 40px;
        text-align: center;
    }

    .empty-icon-wrap {
        width: 120px;
        height: 120px;
        background: #f8fafc;
        border-radius: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 32px;
        color: #cbd5e1;
        font-size: 4rem;
    }
</style>

<div class="parent-requests-page">
    <div class="container container--fixed">
        <div class="page-header mb-5">
            <h1 class="page-title-premium"><?php echo Label::getLabel('LBL_PARENT_REQUESTS'); ?></h1>
            <p class="page-subtitle"><?php echo Label::getLabel('LBL_MANAGE_YOUR_FAMILY_CONNECTIONS_WITH_EASE'); ?></p>
        </div>

        <div class="premium-card">
            <?php if (empty($requests)) { ?>
                <div class="empty-state-premium">
                    <div class="empty-icon-wrap">
                        <i class="ion-person-stalker"></i>
                    </div>
                    <h3 style="font-size: 1.75rem; font-weight: 800; color: var(--text-main); margin-bottom: 12px;">
                        <?php echo Label::getLabel('LBL_NO_REQUESTS_FOUND'); ?>
                    </h3>
                    <p style="font-size: 1.125rem; color: var(--text-muted); max-width: 500px; margin: 0 auto;">
                        <?php echo Label::getLabel('LBL_WHEN_A_PARENT_REQUESTS_TO_LINK_YOUR_ACCOUNT_IT_WILL_APPEAR_HERE'); ?>
                    </p>
                </div>
            <?php } else { ?>
                <div class="table-responsive">
                    <table class="table table-premium mb-0">
                        <thead>
                            <tr>
                                <th><?php echo Label::getLabel('LBL_PARENT_DETAILS'); ?></th>
                                <th><?php echo Label::getLabel('LBL_RELATION'); ?></th>
                                <th><?php echo Label::getLabel('LBL_STATUS'); ?></th>
                                <th class="text-end"><?php echo Label::getLabel('LBL_ACTIONS'); ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($requests as $req) {
                                $parentName = htmlspecialchars($req['parent_first_name'] . ' ' . $req['parent_last_name']);
                                $initial = strtoupper(substr($req['parent_first_name'], 0, 1));
                                $statusClass = '';
                                $statusLabel = '';
                                switch ($req['parstd_status']) {
                                    case ParentRequestsController::STATUS_PENDING:
                                        $statusClass = 'badge-pending';
                                        $statusLabel = Label::getLabel('LBL_PENDING');
                                        break;
                                    case ParentRequestsController::STATUS_APPROVED:
                                        $statusClass = 'badge-approved';
                                        $statusLabel = Label::getLabel('LBL_APPROVED');
                                        break;
                                    case ParentRequestsController::STATUS_REJECTED:
                                        $statusClass = 'badge-rejected';
                                        $statusLabel = Label::getLabel('LBL_REJECTED');
                                        break;
                                }
                                ?>
                                <tr>
                                    <td>
                                        <div class="parent-meta">
                                            <div class="parent-avatar-pill"><?php echo $initial; ?></div>
                                            <div>
                                                <span class="parent-name"><?php echo $parentName; ?></span>
                                                <span
                                                    class="parent-email"><?php echo htmlspecialchars($req['parent_email']); ?></span>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="relation-badge">
                                            <?php echo htmlspecialchars($req['parstd_relation']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge-premium <?php echo $statusClass; ?>">
                                            <?php echo $statusLabel; ?>
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <?php if ($req['parstd_status'] == ParentRequestsController::STATUS_PENDING) { ?>
                                            <button onclick="updateRequest(<?php echo $req['parstd_id']; ?>, 'accept')"
                                                class="btn-action-premium btn-accept">
                                                <i class="ion-checkmark-round"></i>
                                                <?php echo Label::getLabel('LBL_ACCEPT'); ?>
                                            </button>
                                            <button onclick="updateRequest(<?php echo $req['parstd_id']; ?>, 'reject')"
                                                class="btn-action-premium btn-reject ms-2">
                                                <i class="ion-close-round"></i>
                                                <?php echo Label::getLabel('LBL_REJECT'); ?>
                                            </button>
                                        <?php } else { ?>
                                            <button onclick="removeLink(<?php echo $req['parstd_id']; ?>)"
                                                class="btn btn-link text-danger fw-bold text-decoration-none">
                                                <i class="ion-trash-a me-1"></i>
                                                <?php echo Label::getLabel('LBL_REMOVE_CONNECTION'); ?>
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