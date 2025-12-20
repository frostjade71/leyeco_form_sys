<?php
/**
 * LEYECO III Forms Management System
 * Complaints History - Resolved and Closed Complaints
 */

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

// Page configuration
$pageTitle = 'Complaints History';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Complaints', 'url' => STAFF_URL . '/complaints/dashboard.php'],
    ['label' => 'History']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/dashboard.css?v=' . time(),
    STAFF_URL . '/complaints/assets/css/dashboard.css?v=' . time(),
];
$additionalJS = [
    STAFF_URL . '/complaints/assets/js/dashboard.js?v=' . time()
];

// Initialize controller
$controller = new ComplaintController();

// Get filters from query string
$filters = [
    'status' => $_GET['status'] ?? '',
    'type' => $_GET['type'] ?? '',
    'municipality' => $_GET['municipality'] ?? '',
    'date_from' => $_GET['date_from'] ?? '',
    'date_to' => $_GET['date_to'] ?? '',
    'search' => $_GET['search'] ?? ''
];

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Get historical complaints
$result = $controller->getHistoricalComplaints($filters, $page, 20);

// Get statistics for historical complaints only
$stats = $controller->getStatistics();

include __DIR__ . '/../includes/header.php';
?>

<script>
// Make BASE_URL and STAFF_URL available to JavaScript
const BASE_URL = '<?php echo BASE_URL; ?>';
const STAFF_URL = '<?php echo STAFF_URL; ?>';
</script>

<!-- Statistics Cards -->
<div class="stats-grid stats-grid-3">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">Total Historical</span>
            <div class="stat-card-icon total">
                <i class="fas fa-history"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($result['total'] ?? 0); ?></div>
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

    <div class="stat-card closed">
        <div class="stat-card-header">
            <span class="stat-card-title">Closed</span>
            <div class="stat-card-icon closed">
                <i class="fas fa-times-circle"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($stats['by_status']['CLOSED'] ?? 0); ?></div>
    </div>
</div>

<!-- Data Table -->
<div class="data-table-container">
    <div class="table-header">
        <h2 class="table-title"><i class="fas fa-history"></i> Complaint History</h2>
        <div class="table-filters">
            <div class="filter-group">
                <label for="statusFilter">Status:</label>
                <select id="statusFilter" class="filter-select" onchange="applyFilters()">
                    <option value="">All</option>
                    <option value="RESOLVED" <?php echo $filters['status'] === 'RESOLVED' ? 'selected' : ''; ?>>Resolved</option>
                    <option value="CLOSED" <?php echo $filters['status'] === 'CLOSED' ? 'selected' : ''; ?>>Closed</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="dateFrom">From:</label>
                <input type="date" id="dateFrom" class="filter-select" value="<?php echo htmlspecialchars($filters['date_from']); ?>" onchange="applyFilters()">
            </div>
            <div class="filter-group">
                <label for="dateTo">To:</label>
                <input type="date" id="dateTo" class="filter-select" value="<?php echo htmlspecialchars($filters['date_to']); ?>" onchange="applyFilters()">
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

    <div class="table-responsive">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Reference Code</th>
                    <th>Type</th>
                    <th>Municipality</th>
                    <th>Status</th>
                    <th>Resolution Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($result['complaints'])): ?>
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px; color: var(--text-secondary);">
                            <i class="fas fa-inbox" style="font-size: 48px; margin-bottom: 16px; display: block;"></i>
                            No historical complaints found
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
                            <td><?php echo date('M d, Y', strtotime($complaint['updated_at'])); ?></td>
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
    </div>

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

<script>
function applyFilters() {
    const status = document.getElementById('statusFilter').value;
    const dateFrom = document.getElementById('dateFrom').value;
    const dateTo = document.getElementById('dateTo').value;
    const search = document.getElementById('searchInput').value;
    
    const params = new URLSearchParams();
    if (status) params.set('status', status);
    if (dateFrom) params.set('date_from', dateFrom);
    if (dateTo) params.set('date_to', dateTo);
    if (search) params.set('search', search);
    
    window.location.href = 'history.php?' + params.toString();
}

function changePage(page) {
    const params = new URLSearchParams(window.location.search);
    params.set('page', page);
    window.location.href = 'history.php?' + params.toString();
}
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
