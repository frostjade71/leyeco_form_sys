<?php
/**
 * LEYECO III Requisition System - Track Request
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include main configuration
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../app/RequisitionController.php';

$request = null;
$error = null;

// Handle RF number search
if (isset($_GET['rf']) && !empty($_GET['rf'])) {
    $rfNumber = trim($_GET['rf']);
    
    try {
        $controller = new RequisitionController();
        $request = $controller->getRequestByRFNumber($rfNumber);
        
        if (!$request) {
            $error = "No request found with RF Control Number: " . e($rfNumber);
        }
    } catch (Exception $e) {
        error_log("Error tracking request: " . $e->getMessage());
        $error = "An error occurred while retrieving the request. Please try again.";
    }
}

// Page configuration
$page_title = 'Track Request - Requisition System';
$additional_css = [BASE_URL . '/forms/requisition_form/public/track_request.css'];

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="track-container">
        <div class="search-card">
            <h2>🔍 Track Your Requisition Request</h2>
            <p>Enter your RF Control Number to view the status of your requisition</p>
            
            <form method="GET" class="search-form">
                <input 
                    type="text" 
                    name="rf" 
                    placeholder="Enter RF Control Number (e.g., RF-20251217-0001)" 
                    value="<?php echo isset($_GET['rf']) ? e($_GET['rf']) : ''; ?>"
                    required
                    pattern="RF-\d{8}-\d{4}"
                    class="search-input"
                >
                <button type="submit" class="btn btn-primary">Track Request</button>
            </form>
        </div>

        <?php if ($error): ?>
            <div class="error-card">
                <div class="error-icon">❌</div>
                <h3>Request Not Found</h3>
                <p><?php echo e($error); ?></p>
                <a href="submit_request.php" class="btn btn-primary">Submit New Request</a>
            </div>
        <?php endif; ?>

        <?php if ($request): ?>
            <!-- Request Details -->
            <div class="details-card">
                <div class="card-header">
                    <h3>Request Details</h3>
                    <div class="status-badge status-<?php echo e($request['status']); ?>">
                        <?php echo strtoupper(e($request['status'])); ?>
                    </div>
                </div>
                
                <div class="details-grid">
                    <div class="detail-item">
                        <label>RF Control Number</label>
                        <div class="rf-number"><?php echo e($request['rf_control_number']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <label>Requester Name</label>
                        <div><?php echo e($request['requester_name']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <label>Department</label>
                        <div><?php echo e($request['department']); ?></div>
                    </div>
                    
                    <div class="detail-item">
                        <label>Date Submitted</label>
                        <div><?php echo date('F d, Y h:i A', strtotime($request['created_at'])); ?></div>
                    </div>
                    
                    <div class="detail-item full-width">
                        <label>Purpose</label>
                        <div><?php echo nl2br(e($request['purpose'])); ?></div>
                    </div>
                </div>
            </div>

            <!-- Requisition Items -->
            <div class="items-card">
                <h3>Requisition Items</h3>
                <div class="table-responsive">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Qty</th>
                                <th>Unit</th>
                                <th>Description</th>
                                <th>Warehouse Inventory</th>
                                <th>Balance for Purchase</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($request['items'] as $item): ?>
                                <tr>
                                    <td><?php echo e($item['quantity']); ?></td>
                                    <td><?php echo e($item['unit']); ?></td>
                                    <td><?php echo e($item['description']); ?></td>
                                    <td><?php echo $item['warehouse_inventory'] ? e($item['warehouse_inventory']) : '-'; ?></td>
                                    <td><?php echo $item['balance_for_purchase'] ? e($item['balance_for_purchase']) : '-'; ?></td>
                                    <td><?php echo $item['remarks'] ? e($item['remarks']) : '-'; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Approval Timeline -->
            <div class="timeline-card">
                <h3>Approval Timeline</h3>
                <div class="timeline">
                    <?php foreach ($request['approvals'] as $approval): ?>
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
                                    <h4>Level <?php echo $approval['approval_level']; ?>: <?php echo e(explode(' - ', $approval['approver_role'])[0]); ?></h4>
                                    <span class="timeline-status status-<?php echo $approval['status']; ?>">
                                        <?php echo strtoupper($approval['status']); ?>
                                    </span>
                                </div>
                                <p class="timeline-role"><?php echo e(explode(' - ', $approval['approver_role'])[1] ?? ''); ?></p>
                                
                                <?php if ($approval['status'] !== 'pending'): ?>
                                    <div class="timeline-details">
                                        <?php if ($approval['approver_name']): ?>
                                            <p><strong>Approver:</strong> <?php echo e($approval['approver_name']); ?></p>
                                        <?php endif; ?>
                                        <?php if ($approval['approved_at']): ?>
                                            <p><strong>Date:</strong> <?php echo date('F d, Y h:i A', strtotime($approval['approved_at'])); ?></p>
                                        <?php endif; ?>
                                        <?php if ($approval['remarks']): ?>
                                            <p><strong>Remarks:</strong> <?php echo e($approval['remarks']); ?></p>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="actions-card">
                <a href="homepage.php" class="btn btn-outline">Back to Homepage</a>
                <a href="submit_request.php" class="btn btn-primary">Submit Another Request</a>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
