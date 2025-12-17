<?php
/**
 * LEYECO III Forms Management System
 * Requisition Reports
 */

require_once __DIR__ . '/../app/auth_middleware.php';
requireAdmin(); // Only admins can access

require_once __DIR__ . '/../../forms/requisition_form/app/config.php';

// Page configuration
$pageTitle = 'Requisition Reports';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Requisition', 'url' => STAFF_URL . '/requisition/dashboard.php'],
    ['label' => 'Reports']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/requisition/assets/css/reports.css?v=' . time()
];

// Get database connection
global $conn;

// Handle report generation
$reportData = null;
$reportType = $_GET['type'] ?? '';
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

if ($reportType) {
    switch ($reportType) {
        case 'summary':
            $stmt = $conn->prepare("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
                    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                    department
                FROM requisition_requests
                WHERE created_at BETWEEN ? AND ?
                GROUP BY department
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
            
        case 'detailed':
            $stmt = $conn->prepare("
                SELECT 
                    r.*,
                    (SELECT COUNT(*) FROM requisition_items WHERE requisition_id = r.id) as item_count
                FROM requisition_requests r
                WHERE r.created_at BETWEEN ? AND ?
                ORDER BY r.created_at DESC
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
            
        case 'approval':
            $stmt = $conn->prepare("
                SELECT 
                    a.*,
                    r.rf_control_number,
                    r.requester_name,
                    r.department
                FROM approvals a
                JOIN requisition_requests r ON a.requisition_id = r.id
                WHERE a.approved_at BETWEEN ? AND ?
                AND a.status != 'pending'
                ORDER BY a.approved_at DESC
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Generate Report</h3>
    </div>
    <div style="padding: 24px;">
        <form method="GET" class="report-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="type">Report Type</label>
                    <select id="type" name="type" required>
                        <option value="">Select Report Type</option>
                        <option value="summary" <?php echo $reportType === 'summary' ? 'selected' : ''; ?>>Summary by Department</option>
                        <option value="detailed" <?php echo $reportType === 'detailed' ? 'selected' : ''; ?>>Detailed Request List</option>
                        <option value="approval" <?php echo $reportType === 'approval' ? 'selected' : ''; ?>>Approval Activity</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="date_from">From Date</label>
                    <input type="date" id="date_from" name="date_from" value="<?php echo $dateFrom; ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="date_to">To Date</label>
                    <input type="date" id="date_to" name="date_to" value="<?php echo $dateTo; ?>" required>
                </div>
                
                <div class="form-group">
                    <label>&nbsp;</label>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-chart-bar"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php if ($reportData): ?>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">
                <?php 
                switch ($reportType) {
                    case 'summary': echo 'Summary Report by Department'; break;
                    case 'detailed': echo 'Detailed Request List'; break;
                    case 'approval': echo 'Approval Activity Report'; break;
                }
                ?>
            </h3>
            <button class="btn btn-outline" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
        <div style="padding: 24px;">
            <div class="report-header">
                <p><strong>Period:</strong> <?php echo date('F d, Y', strtotime($dateFrom)); ?> to <?php echo date('F d, Y', strtotime($dateTo)); ?></p>
                <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>
            </div>
            
            <?php if ($reportType === 'summary'): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Department</th>
                            <th>Total</th>
                            <th>Pending</th>
                            <th>Approved</th>
                            <th>Rejected</th>
                            <th>Approval Rate</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><strong><?php echo $row['total']; ?></strong></td>
                                <td><?php echo $row['pending']; ?></td>
                                <td><?php echo $row['approved']; ?></td>
                                <td><?php echo $row['rejected']; ?></td>
                                <td>
                                    <?php 
                                    $rate = $row['total'] > 0 ? round(($row['approved'] / $row['total']) * 100, 1) : 0;
                                    echo $rate . '%';
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php elseif ($reportType === 'detailed'): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RF Number</th>
                            <th>Requester</th>
                            <th>Department</th>
                            <th>Items</th>
                            <th>Status</th>
                            <th>Level</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['rf_control_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['requester_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td><?php echo $row['item_count']; ?></td>
                                <td>
                                    <span class="status-badge <?php echo $row['status']; ?>">
                                        <?php echo strtoupper($row['status']); ?>
                                    </span>
                                </td>
                                <td>Level <?php echo $row['current_approval_level']; ?></td>
                                <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php elseif ($reportType === 'approval'): ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>RF Number</th>
                            <th>Requester</th>
                            <th>Department</th>
                            <th>Level</th>
                            <th>Approver</th>
                            <th>Decision</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reportData as $row): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['rf_control_number']); ?></td>
                                <td><?php echo htmlspecialchars($row['requester_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['department']); ?></td>
                                <td>Level <?php echo $row['approval_level']; ?></td>
                                <td><?php echo htmlspecialchars($row['approver_name']); ?></td>
                                <td>
                                    <span class="status-badge <?php echo $row['status']; ?>">
                                        <?php echo strtoupper($row['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M d, Y h:i A', strtotime($row['approved_at'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>


<?php include __DIR__ . '/../includes/footer.php'; ?>
