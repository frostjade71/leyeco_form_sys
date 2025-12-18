<?php
/**
 * LEYECO III Forms Management System
 * View Requisition Request with Approval Form
 */

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/requisition_form/app/config.php';
require_once __DIR__ . '/../../forms/requisition_form/app/RequisitionController.php';

// Check if user is approver
$isApproverUser = isApprover();

// Get request ID
$request_id = $_GET['id'] ?? 0;

if (!$request_id) {
    header('Location: ' . STAFF_URL . '/requisition/dashboard.php');
    exit();
}

// Get database connection
global $conn;

// Get request details
$stmt = $conn->prepare("SELECT * FROM requisition_requests WHERE id = ?");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$request = $result->fetch_assoc();
$stmt->close();

if (!$request) {
    header('Location: ' . STAFF_URL . '/requisition/dashboard.php');
    exit();
}

// Get items
$stmt = $conn->prepare("SELECT * FROM requisition_items WHERE requisition_id = ? ORDER BY id");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$items = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Get approvals
$stmt = $conn->prepare("SELECT * FROM approvals WHERE requisition_id = ? ORDER BY approval_level ASC");
$stmt->bind_param("i", $request_id);
$stmt->execute();
$result = $stmt->get_result();
$approvals = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Determine user permissions
// Get user's approval level from database
$user_level = null;
$is_admin = $currentUser['role'] === 'admin';

if ($isApproverUser && !$is_admin) {
    $stmt = $conn->prepare("SELECT approval_level FROM approvers WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $currentUser['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $user_level = (int)$row['approval_level'];
    }
    $stmt->close();
}

$can_approve = (($user_level !== null && $request['current_approval_level'] == $user_level && $request['status'] == 'pending') || $is_admin);

// Level-specific edit permissions
$can_edit_warehouse = ($user_level == 2) || $is_admin;
$can_edit_balance = ($user_level == 3) || $is_admin;
$can_edit_remarks = ($user_level == 4) || $is_admin;

// Page configuration
$pageTitle = 'View Requisition Request';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Requisition', 'url' => STAFF_URL . '/requisition/dashboard.php'],
    ['label' => $request['rf_control_number']]
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/requisition/assets/css/view_request.css?v=' . time()
];
$additionalJS = [
    STAFF_URL . '/requisition/assets/js/view_request.js?v=' . time()
];

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
                You don't have the required approver role to view requisition requests.
                <br>Please contact your administrator if you need access.
            </p>
            <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
<?php else: ?>

<!-- Request Info -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Request Information</h3>
        <span class="status-badge <?php echo strtolower($request['status']); ?>">
            <?php echo strtoupper($request['status']); ?>
        </span>
    </div>
    
    <div class="request-details">
        <div class="detail-item">
            <div class="detail-label">RF Control Number</div>
            <div class="detail-value rf-number"><?php echo htmlspecialchars($request['rf_control_number']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Requester Name</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['requester_name']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Department</div>
            <div class="detail-value"><?php echo htmlspecialchars($request['department']); ?></div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Current Level</div>
            <div class="detail-value">Level <?php echo $request['current_approval_level']; ?> of 5</div>
        </div>
        <div class="detail-item">
            <div class="detail-label">Submitted Date</div>
            <div class="detail-value"><?php echo date('F d, Y h:i A', strtotime($request['created_at'])); ?></div>
        </div>
        <div class="detail-item" style="grid-column: 1 / -1;">
            <div class="detail-label">Purpose</div>
            <div class="detail-value" style="font-weight: normal;"><?php echo nl2br(htmlspecialchars($request['purpose'])); ?></div>
        </div>
    </div>
</div>

<!-- Items -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Requisition Items</h3>
        <?php if ($can_edit_warehouse && !$can_edit_balance && !$can_edit_remarks): ?>
            <span class="edit-hint">💡 Edit Warehouse Stock before Approving</span>
        <?php elseif ($can_edit_balance && !$can_edit_warehouse && !$can_edit_remarks): ?>
            <span class="edit-hint">💡 Edit Balance to Purchase before Approving</span>
        <?php elseif ($can_edit_remarks && !$can_edit_warehouse && !$can_edit_balance): ?>
            <span class="edit-hint">💡 Edit Remarks Status before Approving</span>
        <?php elseif ($can_edit_warehouse && $can_edit_balance && $can_edit_remarks): ?>
            <span class="edit-hint">💡 All fields are editable</span>
        <?php endif; ?>
    </div>
    
    <div class="table-responsive">
        <table id="itemsTable" class="data-table">
            <thead>
                <tr>
                    <th>Qty</th>
                    <th>Unit</th>
                    <th>Description</th>
                    <th>Warehouse Stock</th>
                    <th>Balance to Purchase</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $item): ?>
                    <tr data-item-id="<?php echo $item['id']; ?>">
                        <td><?php echo $item['quantity']; ?></td>
                        <td><?php echo htmlspecialchars($item['unit']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td class="<?php echo $can_edit_warehouse ? 'editable-cell' : ''; ?>" 
                            data-field="warehouse_inventory" 
                            data-value="<?php echo $item['warehouse_inventory'] ?: ''; ?>">
                            <?php echo $item['warehouse_inventory'] ?: 'N/A'; ?>
                            <?php if ($can_edit_warehouse): ?>
                                <span class="edit-icon">✏️</span>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo $can_edit_balance ? 'editable-cell' : ''; ?>" 
                            data-field="balance_for_purchase" 
                            data-value="<?php echo $item['balance_for_purchase'] ?: ''; ?>">
                            <?php 
                            $bal = $item['balance_for_purchase'];
                            if ($bal && strpos($bal, '₱') === false && preg_match('/[\d]/', $bal)) {
                                echo '₱' . $bal;
                            } else {
                                echo $bal ?: 'N/A';
                            }
                            ?>
                            <?php if ($can_edit_balance): ?>
                                <span class="edit-icon">✏️</span>
                            <?php endif; ?>
                        </td>
                        <td class="<?php echo $can_edit_remarks ? 'editable-cell' : ''; ?>" 
                            data-field="remarks" 
                            data-value="<?php echo htmlspecialchars($item['remarks'] ?? '') ?: ''; ?>">
                            <?php echo htmlspecialchars($item['remarks'] ?? '') ?: '-'; ?>
                            <?php if ($can_edit_remarks): ?>
                                <span class="edit-icon">✏️</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Approval Actions -->
<?php if ($can_approve): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Approval Decision</h3>
        </div>
        
        <form id="approvalForm">
            <input type="hidden" name="requisition_id" value="<?php echo $request['id']; ?>">
            <input type="hidden" name="approval_level" value="<?php echo $request['current_approval_level']; ?>">
            
            <div class="form-group">
                <label for="remarks">Comments (Optional)</label>
                <textarea id="remarks" name="remarks" rows="4" placeholder="Enter your comment"></textarea>
            </div>
            
            <div class="approval-actions">
                <button type="button" class="btn btn-danger" onclick="handleApproval('rejected')">
                    ❌ Reject Request
                </button>
                <button type="button" class="btn btn-success btn-lg" onclick="handleApproval('approved')">
                    ✅ Approve Request
                </button>
            </div>
        </form>
    </div>
<?php endif; ?>

<!-- Approval Timeline -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Approval Timeline</h3>
    </div>
    
    <div class="timeline">
        <?php foreach ($approvals as $approval): ?>
            <div class="timeline-item <?php echo $approval['status']; ?>">
                <div class="timeline-marker">
                    <?php if ($approval['status'] === 'approved'): ?>
                        ✓
                    <?php elseif ($approval['status'] === 'rejected'): ?>
                        ✗
                    <?php else: ?>
                        <?php echo $approval['approval_level']; ?>
                    <?php endif; ?>
                </div>
                <div class="timeline-content">
                    <div class="timeline-header">
                        <div>
                            <div class="timeline-level">Level <?php echo $approval['approval_level']; ?></div>
                            <div class="timeline-title"><?php echo htmlspecialchars($approval['approver_role']); ?></div>
                        </div>
                        <span class="status-badge <?php echo $approval['status']; ?>">
                            <?php echo strtoupper($approval['status']); ?>
                        </span>
                    </div>
                    
                    <?php if ($approval['status'] != 'pending'): ?>
                        <div class="timeline-meta">
                            <?php if ($approval['approver_name']): ?>
                                <div><strong>Approver:</strong> <?php echo htmlspecialchars($approval['approver_name']); ?></div>
                            <?php endif; ?>
                            <?php if ($approval['approved_at']): ?>
                                <div><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($approval['approved_at'])); ?></div>
                            <?php endif; ?>
                            <?php if ($approval['remarks']): ?>
                                <div class="timeline-remarks">
                                    <strong>Comment:</strong> <?php echo nl2br(htmlspecialchars($approval['remarks'])); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php endif; // End approver check ?>

<!-- Confirmation Modal -->
<div id="confirmModal" class="modal" style="display: none;">
    <div class="modal-content confirm-modal-content" style="max-width: 400px;">
        <div class="modal-header confirm-modal-header" style="background: linear-gradient(135deg, #DC2626 0%, #991B1B 100%); color: white; padding: 16px 20px; border-radius: 8px 8px 0 0;">
            <h3 id="confirmTitle" style="margin: 0; font-size: 1.1rem;">Confirm Action</h3>
        </div>
        <div class="modal-body confirm-modal-body" style="padding: 24px 20px; text-align: center;">
            <div id="confirmIcon" style="font-size: 2.5rem; margin-bottom: 16px;">⚠️</div>
            <p id="confirmMessage" style="font-size: 1rem; color: var(--text-primary); margin-bottom: 0;"></p>
        </div>
        <div class="modal-footer confirm-modal-footer" style="padding: 16px 20px; display: flex; gap: 10px; justify-content: center; background: #f9fafb; border-radius: 0 0 8px 8px;">
            <button id="confirmCancel" class="btn btn-outline" style="min-width: 100px; padding: 10px 20px;">Cancel</button>
            <button id="confirmOk" class="btn btn-primary" style="min-width: 100px; padding: 10px 20px;">Confirm</button>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
