<?php
/**
 * LEYECO III Forms Management System
 * Approval History - Shows requests approved/rejected by current user
 */

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/requisition_form/app/RequisitionController.php';

// Check if user is approver
$isApproverUser = isApprover();

// Page configuration
$pageTitle = 'My Approval History';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Requisition', 'url' => STAFF_URL . '/requisition/dashboard.php'],
    ['label' => 'History']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/requisition/assets/css/dashboard.css?v=' . time()
];

// Get current user's approval history
global $conn;

$approver_name = $currentUser['full_name'] ?? $currentUser['username'];

$stmt = $conn->prepare("
    SELECT 
        a.*,
        r.rf_control_number,
        r.requester_name,
        r.department,
        r.status as request_status
    FROM approvals a
    JOIN requisition_requests r ON a.requisition_id = r.id
    WHERE a.approver_name = ?
    AND a.status != 'pending'
    ORDER BY a.approved_at DESC
    LIMIT 50
");
$stmt->bind_param("s", $approver_name);
$stmt->execute();
$result = $stmt->get_result();
$history = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

include __DIR__ . '/../includes/header.php';
?>

<?php if (!$isApproverUser): ?>
    <!-- Access Denied Message for Non-Approvers -->
    <div class="card" style="max-width: 600px; margin: 40px auto; text-align: center;">
        <div style="padding: 60px 40px;">
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-lock" style="font-size: 36px; color: #D97706;"></i>
            </div>
            <h2 style="color: var(--text-primary); margin-bottom: 12px; font-size: 24px;">You are not an Approver</h2>
            <p style="color: var(--text-secondary); font-size: 16px; line-height: 1.6; margin-bottom: 24px;">
                You don't have the required approver role to view approval history.
                <br>Please contact your administrator if you need access.
            </p>
            <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
<?php else: ?>

<div class="data-table-container">
    <div class="table-header">
        <h2 class="table-title">My Approval History</h2>
        <p class="table-description">Showing your recent approval decisions</p>
    </div>

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>RF Number</th>
                    <th>Requester</th>
                    <th>Department</th>
                    <th>Level</th>
                    <th>My Decision</th>
                    <th>Date</th>
                    <th>Request Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($history)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="fas fa-history" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            No approval history found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($history as $item): ?>
                        <tr>
                            <td>
                                <strong class="rf-number"><?php echo htmlspecialchars($item['rf_control_number']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($item['requester_name']); ?></td>
                            <td><span class="dept-badge"><?php echo htmlspecialchars($item['department']); ?></span></td>
                            <td>Level <?php echo $item['approval_level']; ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($item['status']); ?>">
                                    <?php echo strtoupper(htmlspecialchars($item['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo date('M d, Y h:i A', strtotime($item['approved_at'])); ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($item['request_status']); ?>">
                                    <?php echo strtoupper(htmlspecialchars($item['request_status'])); ?>
                                </span>
                            </td>
                            <td>
                                <a href="view_request.php?id=<?php echo $item['requisition_id']; ?>" class="btn btn-primary btn-sm">
                                    <i class="fas fa-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php endif; // End approver check ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
