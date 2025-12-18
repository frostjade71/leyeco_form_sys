<?php
/**
 * LEYECO III Forms Management System
 * Complaints Dashboard
 */

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

// Page configuration
$pageTitle = 'Complaints Dashboard';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Complaints']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/dashboard.css',
    STAFF_URL . '/complaints/assets/css/dashboard.css',
];
$additionalJS = [
    STAFF_URL . '/complaints/assets/js/dashboard.js'
];

// Initialize controller
$controller = new ComplaintController();

// Get statistics
$stats = $controller->getStatistics();

// Get filters from query string
$filters = [
    'status' => $_GET['status'] ?? '',
    'type' => $_GET['type'] ?? '',
    'municipality' => $_GET['municipality'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get complaints
$result = $controller->getAll($filters, $page, 20);

include __DIR__ . '/../includes/header.php';
?>

<script>
// Make BASE_URL and STAFF_URL available to JavaScript
const BASE_URL = '<?php echo BASE_URL; ?>';
const STAFF_URL = '<?php echo STAFF_URL; ?>';
</script>


<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Complaints</span>
            <div class="stat-card-icon total">
                <i class="fas fa-list"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['total'] ?? 0); ?></div>
    </div>

    <div class="stat-card new">
        <div class="stat-card-header">
            <span class="stat-card-title">New</span>
            <div class="stat-card-icon new">
                <i class="fas fa-bell"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['NEW'] ?? 0); ?></div>
    </div>

    <div class="stat-card investigating">
        <div class="stat-card-header">
            <span class="stat-card-title">Investigating</span>
            <div class="stat-card-icon investigating">
                <i class="fas fa-search"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['INVESTIGATING'] ?? 0); ?></div>
    </div>

    <div class="stat-card resolved">
        <div class="stat-card-header">
            <span class="stat-card-title">Resolved</span>
            <div class="stat-card-icon resolved">
                <i class="fas fa-check-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['RESOLVED'] ?? 0); ?></div>
    </div>
</div>

<!-- Data Table -->
<div class="data-table-container">
    <div class="table-header">
        <h2 class="table-title">All Complaints</h2>
        <div class="table-filters">
            <div class="filter-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter" class="filter-select" onchange="applyFilters()">
                    <option value="">All</option>
                    <option value="NEW" <?php echo $filters['status'] === 'NEW' ? 'selected' : ''; ?>>New</option>
                    <option value="INVESTIGATING" <?php echo $filters['status'] === 'INVESTIGATING' ? 'selected' : ''; ?>>Investigating</option>
                    <option value="IN_PROGRESS" <?php echo $filters['status'] === 'IN_PROGRESS' ? 'selected' : ''; ?>>In Progress</option>
                    <option value="RESOLVED" <?php echo $filters['status'] === 'RESOLVED' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="CLOSED" <?php echo $filters['status'] === 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input 
                    type="text" 
                    id="searchInput" 
                    placeholder="Search by reference code..." 
                    value="<?php echo htmlspecialchars($filters['search']); ?>"
                    onkeyup="handleSearch(event)"
                >
            </div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Reference Code</th>
                <th>Type</th>
                <th>Municipality</th>
                <th>Status</th>
                <th>Date Submitted</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($result['complaints'])): ?>
                <tr>
                    <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                        <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                        No complaints found
                    </td>
                </tr>
            <?php else: ?>
                <?php foreach ($result['complaints'] as $complaint): ?>
                    <tr>
                        <td>
                            <strong><?php echo htmlspecialchars($complaint['reference_code']); ?></strong>
                        </td>
                        <td><?php echo htmlspecialchars($complaint['type']); ?></td>
                        <td><?php echo htmlspecialchars($complaint['municipality']); ?></td>
                        <td>
                            <span class="status-badge <?php echo strtolower($complaint['status']); ?>">
                                <?php echo htmlspecialchars($complaint['status']); ?>
                            </span>
                        </td>
                        <td><?php echo date('M d, Y', strtotime($complaint['created_at'])); ?></td>
                        <td>
                            <button 
                                class="btn btn-primary btn-sm" 
                                onclick="viewComplaint(<?php echo $complaint['id']; ?>)"
                            >
                                <i class="fas fa-eye"></i> View
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>

    <?php if ($result['total_pages'] > 1): ?>
        <div class="pagination">
            <button 
                onclick="changePage(<?php echo $page - 1; ?>)" 
                <?php echo $page <= 1 ? 'disabled' : ''; ?>
            >
                <i class="fas fa-chevron-left"></i> Previous
            </button>
            <span class="page-info">
                Page <?php echo $page; ?> of <?php echo $result['total_pages']; ?>
            </span>
            <button 
                onclick="changePage(<?php echo $page + 1; ?>)" 
                <?php echo $page >= $result['total_pages'] ? 'disabled' : ''; ?>
            >
                Next <i class="fas fa-chevron-right"></i>
            </button>
        </div>
    <?php endif; ?>
</div>

<!-- Complaint Detail Modal -->
<div id="complaintModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 class="modal-title">Complaint Details</h2>
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

<?php include __DIR__ . '/../includes/footer.php'; ?>
