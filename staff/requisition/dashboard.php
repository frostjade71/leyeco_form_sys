<?php
/**
 * LEYECO III Forms Management System
 * Requisition Dashboard
 */

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/requisition_form/app/RequisitionController.php';

// Check if user is approver
$isApproverUser = isApprover();

// Page configuration
$pageTitle = 'Requisition Dashboard';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Requisition']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css?v=' . time(),
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/requisition/assets/css/dashboard.css?v=' . time(),
];
$additionalJS = [
    STAFF_URL . '/requisition/assets/js/dashboard.js?v=' . time()
];

// Initialize controller
$controller = new RequisitionController();

// Get statistics
$stats = $controller->getStatistics();

// Get approver role and level for current user
$approverRole = 'Staff';
$approverLevel = null;
$is_admin = $currentUser['role'] === 'admin';

if ($isApproverUser) {
    global $conn;
    $stmt = $conn->prepare("SELECT role, approval_level FROM approvers WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $currentUser['email']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($row = $result->fetch_assoc()) {
        $approverRole = $row['role'];
        // Clean up role name - remove prefixes like "Checked By - "
        if (strpos($approverRole, ' - ') !== false) {
            $parts = explode(' - ', $approverRole);
            $approverRole = end($parts);
        }
        $approverLevel = (int)$row['approval_level'];
    }
    $stmt->close();
}

// Get filters from query string
$filters = [
    'status' => $_GET['status'] ?? '',
    'department' => $_GET['department'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get requests filtered by user's approval level
try {
    global $conn;
    
    // Build query - filter by approval level unless admin
    $sql = "SELECT r.*, 
            (SELECT COUNT(*) FROM requisition_items WHERE requisition_id = r.id) as item_count
            FROM requisition_requests r
            WHERE 1=1";
    
    $params = [];
    $types = "";
    
    // Filter by approval level for non-admin approvers
    if (!$is_admin && $approverLevel !== null) {
        $sql .= " AND r.current_approval_level = ?";
        $params[] = $approverLevel;
        $types .= "i";
    }
    
    if (!empty($filters['status'])) {
        $sql .= " AND r.status = ?";
        $params[] = $filters['status'];
        $types .= "s";
    }
    
    if (!empty($filters['department'])) {
        $sql .= " AND r.department = ?";
        $params[] = $filters['department'];
        $types .= "s";
    }
    
    if (!empty($filters['search'])) {
        $sql .= " AND r.rf_control_number LIKE ?";
        $params[] = '%' . $filters['search'] . '%';
        $types .= "s";
    }
    
    $sql .= " ORDER BY r.created_at DESC LIMIT 20 OFFSET " . (($page - 1) * 20);
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
        $requests = $result->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    } else {
        $result = $conn->query($sql);
        $requests = $result->fetch_all(MYSQLI_ASSOC);
    }
    
} catch (Exception $e) {
    error_log("Error fetching requests: " . $e->getMessage());
    $requests = [];
}

include __DIR__ . '/../includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h2>Welcome back, <?php echo htmlspecialchars($currentUser['full_name']); ?>! 👋</h2>
        <p><?php echo htmlspecialchars($approverRole); ?></p>
    </div>
    <div class="welcome-datetime">
        <div class="datetime-display">
            <div class="date-text" id="currentDate"></div>
            <div class="time-text" id="currentTime"></div>
        </div>
    </div>
</div>

<script>
function updateDateTime() {
    const now = new Date();
    
    // Format date: December 15, 2025
    const dateOptions = { year: 'numeric', month: 'long', day: 'numeric' };
    const dateStr = now.toLocaleDateString('en-US', dateOptions);
    
    // Format time: 1:16 PM
    const timeOptions = { hour: 'numeric', minute: '2-digit', hour12: true };
    const timeStr = now.toLocaleTimeString('en-US', timeOptions);
    
    document.getElementById('currentDate').textContent = dateStr;
    document.getElementById('currentTime').textContent = timeStr;
}

// Update immediately and then every second
updateDateTime();
setInterval(updateDateTime, 1000);
</script>

<?php if (!$isApproverUser): ?>
    <!-- Access Denied Message for Non-Approvers -->
    <div class="card" style="max-width: 600px; margin: 40px auto; text-align: center;">
        <div style="padding: 60px 40px;">
            <div style="width: 80px; height: 80px; margin: 0 auto 24px; background: linear-gradient(135deg, #FEF3C7 0%, #FDE68A 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <i class="fas fa-user-lock" style="font-size: 36px; color: #D97706;"></i>
            </div>
            <h2 style="color: var(--text-primary); margin-bottom: 12px; font-size: 24px;">You are not an Approver</h2>
            <p style="color: var(--text-secondary); font-size: 16px; line-height: 1.6; margin-bottom: 24px;">
                You don't have the required approver role to access the requisition approval system.
                <br>Please contact your administrator if you need access.
            </p>
            <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="btn btn-primary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
<?php else: ?>

<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Requests</span>
            <div class="stat-card-icon total">
                <i class="fas fa-clipboard-list"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
    </div>

    <div class="stat-card pending">
        <div class="stat-card-header">
            <span class="stat-card-title">Pending</span>
            <div class="stat-card-icon pending">
                <i class="fas fa-clock"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['pending'] ?? 0); ?></div>
    </div>

    <div class="stat-card approved">
        <div class="stat-card-header">
            <span class="stat-card-title">Approved</span>
            <div class="stat-card-icon approved">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['approved'] ?? 0); ?></div>
    </div>

    <div class="stat-card rejected">
        <div class="stat-card-header">
            <span class="stat-card-title">Rejected</span>
            <div class="stat-card-icon rejected">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['rejected'] ?? 0); ?></div>
    </div>
</div>

<!-- Data Table -->
<div class="data-table-container">
    <div class="table-header">
        <h2 class="table-title">All Requisition Requests</h2>
        <div class="table-filters">
            <div class="filter-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter" class="filter-select" onchange="applyFilters()">
                    <option value="">All</option>
                    <option value="pending" <?php echo $filters['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                    <option value="approved" <?php echo $filters['status'] === 'approved' ? 'selected' : ''; ?>>Approved</option>
                    <option value="rejected" <?php echo $filters['status'] === 'rejected' ? 'selected' : ''; ?>>Rejected</option>
                </select>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search by RF number..." 
                    value="<?php echo htmlspecialchars($filters['search']); ?>"
                    onkeyup="handleSearch(event)"
                >
            </div>
        </div>
    </div>

    <div class="table-responsive">
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
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($requests)): ?>
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            No requests found
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($requests as $request): ?>
                        <tr>
                            <td>
                                <strong class="rf-number"><?php echo htmlspecialchars($request['rf_control_number']); ?></strong>
                            </td>
                            <td><?php echo htmlspecialchars($request['requester_name']); ?></td>
                            <td><span class="dept-badge"><?php echo htmlspecialchars($request['department']); ?></span></td>
                            <td><?php echo $request['item_count']; ?></td>
                            <td>
                                <span class="status-badge <?php echo strtolower($request['status']); ?>">
                                    <?php echo strtoupper(htmlspecialchars($request['status'])); ?>
                                </span>
                            </td>
                            <td>
                                <div class="level-indicator">
                                    Level <?php echo $request['current_approval_level']; ?>
                                </div>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($request['created_at'])); ?></td>
                            <td>
                                <a href="view_request.php?id=<?php echo $request['id']; ?>" class="btn btn-primary btn-sm">
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

<!-- Request Detail Modal -->
<div id="requestModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Request Details</h2>
            <button class="modal-close" onclick="closeModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body" id="modalBody">
            <div style="text-align: center; padding: 40px;">
                <i class="fas fa-spinner fa-spin" style="font-size: 32px; color: var(--primary-color);"></i>
                <p style="margin-top: 16px; color: var(--text-secondary);">Loading...</p>
            </div>
        </div>
    </div>
</div>

<?php endif; // End approver check ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
