<?php
/**
 * LEYECO III Forms Management System
 * Complaints Reports
 */

require_once __DIR__ . '/../app/auth_middleware.php';
requireAdmin(); // Only admins can access

require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

// Page configuration
$pageTitle = 'Complaints Reports';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Complaints', 'url' => STAFF_URL . '/complaints/dashboard.php'],
    ['label' => 'Reports']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/complaints/assets/css/reports.css?v=' . time()
];

// Get database connection
global $conn;

// Initialize controller
$controller = new ComplaintController();

// Handle report generation
$reportData = null;
$reportType = $_GET['type'] ?? '';
$dateFrom = $_GET['date_from'] ?? date('Y-m-01');
$dateTo = $_GET['date_to'] ?? date('Y-m-d');

if ($reportType) {
    switch ($reportType) {
        case 'municipality':
            $stmt = $conn->prepare("
                SELECT 
                    municipality,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'NEW' THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'INVESTIGATING' THEN 1 ELSE 0 END) as investigating,
                    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'RESOLVED' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'CLOSED' THEN 1 ELSE 0 END) as closed
                FROM complaints
                WHERE DATE(created_at) BETWEEN ? AND ?
                GROUP BY municipality
                ORDER BY total DESC
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
            
        case 'type':
            $stmt = $conn->prepare("
                SELECT 
                    type,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'NEW' THEN 1 ELSE 0 END) as new_count,
                    SUM(CASE WHEN status = 'INVESTIGATING' THEN 1 ELSE 0 END) as investigating,
                    SUM(CASE WHEN status = 'IN_PROGRESS' THEN 1 ELSE 0 END) as in_progress,
                    SUM(CASE WHEN status = 'RESOLVED' THEN 1 ELSE 0 END) as resolved,
                    SUM(CASE WHEN status = 'CLOSED' THEN 1 ELSE 0 END) as closed
                FROM complaints
                WHERE DATE(created_at) BETWEEN ? AND ?
                GROUP BY type
                ORDER BY total DESC
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
            
        case 'detailed':
            $stmt = $conn->prepare("
                SELECT 
                    c.*,
                    u.full_name as assigned_to_name
                FROM complaints c
                LEFT JOIN users u ON c.assigned_to = u.id
                WHERE DATE(c.created_at) BETWEEN ? AND ?
                ORDER BY c.created_at DESC
            ");
            $stmt->bind_param("ss", $dateFrom, $dateTo);
            $stmt->execute();
            $reportData = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            break;
            
        case 'staff_performance':
            $reportData = $controller->getStaffPerformance($dateFrom, $dateTo);
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
                        <option value="municipality" <?php echo $reportType === 'municipality' ? 'selected' : ''; ?>>Summary by Municipality</option>
                        <option value="type" <?php echo $reportType === 'type' ? 'selected' : ''; ?>>Summary by Complaint Type</option>
                        <option value="detailed" <?php echo $reportType === 'detailed' ? 'selected' : ''; ?>>Detailed Complaint List</option>
                        <option value="staff_performance" <?php echo $reportType === 'staff_performance' ? 'selected' : ''; ?>>Staff Performance Report</option>
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
                    case 'municipality': echo 'Summary Report by Municipality'; break;
                    case 'type': echo 'Summary Report by Complaint Type'; break;
                    case 'detailed': echo 'Detailed Complaint List'; break;
                    case 'staff_performance': echo 'Staff Performance Report'; break;
                }
                ?>
            </h3>
            <button class="btn btn-info" onclick="window.print()">
                <i class="fas fa-print"></i> Print Report
            </button>
        </div>
        <div style="padding: 24px;">
            <div class="report-header">
                <p><strong>Period:</strong> <?php echo date('F d, Y', strtotime($dateFrom)); ?> to <?php echo date('F d, Y', strtotime($dateTo)); ?></p>
                <p><strong>Generated:</strong> <?php echo date('F d, Y h:i A'); ?></p>
            </div>
            
            <?php if ($reportType === 'municipality'): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Municipality</th>
                                <th>Total</th>
                                <th>New</th>
                                <th>Investigating</th>
                                <th>In Progress</th>
                                <th>Resolved</th>
                                <th>Closed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['municipality']); ?></td>
                                    <td><strong><?php echo $row['total']; ?></strong></td>
                                    <td><?php echo $row['new_count']; ?></td>
                                    <td><?php echo $row['investigating']; ?></td>
                                    <td><?php echo $row['in_progress']; ?></td>
                                    <td><?php echo $row['resolved']; ?></td>
                                    <td><?php echo $row['closed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($reportType === 'type'): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Complaint Type</th>
                                <th>Total</th>
                                <th>New</th>
                                <th>Investigating</th>
                                <th>In Progress</th>
                                <th>Resolved</th>
                                <th>Closed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                                    <td><strong><?php echo $row['total']; ?></strong></td>
                                    <td><?php echo $row['new_count']; ?></td>
                                    <td><?php echo $row['investigating']; ?></td>
                                    <td><?php echo $row['in_progress']; ?></td>
                                    <td><?php echo $row['resolved']; ?></td>
                                    <td><?php echo $row['closed']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($reportType === 'detailed'): ?>
                <div class="table-responsive">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Tracking #</th>
                                <th>Consumer Name</th>
                                <th>Municipality</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Assigned To</th>
                                <th>Date Filed</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reportData as $row): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['reference_code']); ?></td>
                                    <td><?php echo htmlspecialchars($row['consumer_name'] ?? 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($row['municipality']); ?></td>
                                    <td><?php echo htmlspecialchars($row['type']); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo strtolower($row['status']); ?>">
                                            <?php echo $row['status']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($row['assigned_to_name'] ?? 'Unassigned'); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($row['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
            <?php elseif ($reportType === 'staff_performance'): ?>
                            <th>Status Updates</th>
                            <th>Dispatch Updates</th>
                            <th>Comments</th>
                            <th>Total Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($reportData)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center; padding: 40px;">
                                    No staff activity found for the selected period
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($reportData as $staff): ?>
                                <tr>
                                    <td><strong><?php echo htmlspecialchars($staff['full_name']); ?></strong></td>
                                    <td><?php echo $staff['status_updates']; ?></td>
                                    <td><?php echo $staff['dispatch_updates']; ?></td>
                                    <td><?php echo $staff['comment_count']; ?></td>
                                    <td><strong><?php echo $staff['total_actions']; ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
