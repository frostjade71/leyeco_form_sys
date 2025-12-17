<?php
/**
 * LEYECO III Forms Management System
 * Requisition Admin Dashboard
 */

require_once __DIR__ . '/../app/auth_middleware.php';
requireAdmin(); // Only admins can access

require_once __DIR__ . '/../../forms/requisition_form/app/RequisitionController.php';

// Page configuration
$pageTitle = 'Requisition Analytics';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Requisition', 'url' => STAFF_URL . '/requisition/dashboard.php'],
    ['label' => 'Analytics']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/requisition/assets/css/admin.css?v=' . time()
];

// Get database connection
global $conn;

// Get statistics
$controller = new RequisitionController();
$stats = $controller->getStatistics();

// Get department breakdown
$stmt = $conn->query("
    SELECT department, COUNT(*) as count
    FROM requisition_requests
    GROUP BY department
    ORDER BY count DESC
");
$deptStats = $stmt->fetch_all(MYSQLI_ASSOC);

// Get monthly trends (last 6 months)
$stmt = $conn->query("
    SELECT 
        DATE_FORMAT(created_at, '%Y-%m') as month,
        COUNT(*) as count,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
    FROM requisition_requests
    WHERE created_at >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    GROUP BY DATE_FORMAT(created_at, '%Y-%m')
    ORDER BY month DESC
");
$monthlyTrends = $stmt->fetch_all(MYSQLI_ASSOC);

// Get approval level statistics
$stmt = $conn->query("
    SELECT 
        approval_level,
        COUNT(*) as total,
        SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved,
        SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
        SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
    FROM approvals
    GROUP BY approval_level
    ORDER BY approval_level
");
$levelStats = $stmt->fetch_all(MYSQLI_ASSOC);

// Get recent activity
$stmt = $conn->query("
    SELECT 
        a.*,
        r.rf_control_number,
        r.requester_name
    FROM approvals a
    JOIN requisition_requests r ON a.requisition_id = r.id
    WHERE a.status != 'pending'
    ORDER BY a.approved_at DESC
    LIMIT 10
");
$recentActivity = $stmt->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<!-- Statistics Grid -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Requests</span>
            <div class="stat-card-icon total">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
        <div class="stat-card-footer">All time</div>
    </div>

    <div class="stat-card pending">
        <div class="stat-card-header">
            <span class="stat-card-title">Pending</span>
            <div class="stat-card-icon pending">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['pending'] ?? 0); ?></div>
        <div class="stat-card-footer">Awaiting approval</div>
    </div>

    <div class="stat-card approved">
        <div class="stat-card-header">
            <span class="stat-card-title">Approved</span>
            <div class="stat-card-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['approved'] ?? 0); ?></div>
        <div class="stat-card-footer">
            <?php 
            $approvalRate = $stats['total'] > 0 ? round(($stats['by_status']['approved'] / $stats['total']) * 100, 1) : 0;
            echo $approvalRate . '% approval rate';
            ?>
        </div>
    </div>

    <div class="stat-card rejected">
        <div class="stat-card-header">
            <span class="stat-card-title">Rejected</span>
            <div class="stat-card-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['rejected'] ?? 0); ?></div>
        <div class="stat-card-footer">
            <?php 
            $rejectionRate = $stats['total'] > 0 ? round(($stats['by_status']['rejected'] / $stats['total']) * 100, 1) : 0;
            echo $rejectionRate . '% rejection rate';
            ?>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="charts-row">
    <!-- Department Breakdown -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Requests by Department</h3>
        </div>
        <div class="chart-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Department</th>
                        <th>Requests</th>
                        <th>Percentage</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($deptStats as $dept): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($dept['department']); ?></td>
                            <td><strong><?php echo $dept['count']; ?></strong></td>
                            <td>
                                <div class="progress-bar">
                                    <div class="progress-fill" style="width: <?php echo ($dept['count'] / $stats['total']) * 100; ?>%"></div>
                                </div>
                                <?php echo round(($dept['count'] / $stats['total']) * 100, 1); ?>%
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Approval Level Performance -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Approval Level Performance</h3>
        </div>
        <div class="chart-container">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Level</th>
                        <th>Approved</th>
                        <th>Rejected</th>
                        <th>Pending</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($levelStats as $level): ?>
                        <tr>
                            <td><strong>Level <?php echo $level['approval_level']; ?></strong></td>
                            <td><span class="badge badge-success"><?php echo $level['approved']; ?></span></td>
                            <td><span class="badge badge-danger"><?php echo $level['rejected']; ?></span></td>
                            <td><span class="badge badge-warning"><?php echo $level['pending']; ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Monthly Trends -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Monthly Trends (Last 6 Months)</h3>
    </div>
    <div class="chart-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Month</th>
                    <th>Total Requests</th>
                    <th>Approved</th>
                    <th>Rejected</th>
                    <th>Approval Rate</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($monthlyTrends as $trend): ?>
                    <tr>
                        <td><?php echo date('F Y', strtotime($trend['month'] . '-01')); ?></td>
                        <td><strong><?php echo $trend['count']; ?></strong></td>
                        <td><span class="badge badge-success"><?php echo $trend['approved']; ?></span></td>
                        <td><span class="badge badge-danger"><?php echo $trend['rejected']; ?></span></td>
                        <td>
                            <?php 
                            $rate = $trend['count'] > 0 ? round(($trend['approved'] / $trend['count']) * 100, 1) : 0;
                            echo $rate . '%';
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Recent Activity -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Recent Approval Activity</h3>
    </div>
    <div class="chart-container">
        <table class="data-table">
            <thead>
                <tr>
                    <th>RF Number</th>
                    <th>Requester</th>
                    <th>Level</th>
                    <th>Approver</th>
                    <th>Decision</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentActivity as $activity): ?>
                    <tr>
                        <td>
                            <a href="view_request.php?id=<?php echo $activity['requisition_id']; ?>" class="rf-number">
                                <?php echo htmlspecialchars($activity['rf_control_number']); ?>
                            </a>
                        </td>
                        <td><?php echo htmlspecialchars($activity['requester_name']); ?></td>
                        <td>Level <?php echo $activity['approval_level']; ?></td>
                        <td><?php echo htmlspecialchars($activity['approver_name']); ?></td>
                        <td>
                            <span class="status-badge <?php echo $activity['status']; ?>">
                                <?php echo strtoupper($activity['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y h:i A', strtotime($activity['approved_at'])); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
