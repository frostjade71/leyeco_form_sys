<?php
/**
 * LEYECO III Forms Management System
 * Staff Dashboard Homepage
 */

require_once __DIR__ . '/app/auth_middleware.php';

// Page configuration
$pageTitle = 'Dashboard';
$breadcrumbs = [
    ['label' => 'Dashboard']
];
$additionalCSS = [STAFF_URL . '/assets/css/dashboard.css'];

// Get statistics for all forms
try {
    // Complaints statistics
    $complaintsStmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'NEW' THEN 1 ELSE 0 END) as new,
            SUM(CASE WHEN status = 'INVESTIGATING' THEN 1 ELSE 0 END) as investigating,
            SUM(CASE WHEN status = 'RESOLVED' THEN 1 ELSE 0 END) as resolved
        FROM complaints
    ");
    $complaintsStats = $complaintsStmt->fetch_assoc();
    
    // Total submissions across all forms
    $totalSubmissions = $complaintsStats['total']; // Will add other forms later
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $complaintsStats = ['total' => 0, 'new' => 0, 'investigating' => 0, 'resolved' => 0];
    $totalSubmissions = 0;
}

include __DIR__ . '/includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h2>Welcome back, <?php echo htmlspecialchars($currentUser['full_name']); ?>! 👋</h2>
        <p>Here's an overview of all form submissions and their current status.</p>
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

<!-- Quick Stats -->
<div class="quick-stats">
    <div class="quick-stat-card">
        <h3>Total Submissions</h3>
        <div class="value"><?php echo number_format($totalSubmissions ?? 0); ?></div>
    </div>
    <div class="quick-stat-card">
        <h3>Pending Review</h3>
        <div class="value"><?php echo number_format($complaintsStats['new'] ?? 0); ?></div>
    </div>
    <div class="quick-stat-card">
        <h3>In Progress</h3>
        <div class="value"><?php echo number_format($complaintsStats['investigating'] ?? 0); ?></div>
    </div>
    <div class="quick-stat-card">
        <h3>Resolved</h3>
        <div class="value"><?php echo number_format($complaintsStats['resolved'] ?? 0); ?></div>
    </div>
</div>

<!-- Page Header -->
<div class="page-header">
    <h2 class="page-title">Forms Management</h2>
    <p class="page-description">Select a form type to view and manage submissions</p>
</div>

<!-- Form Cards Grid -->
<div class="dashboard-grid">
    <!-- Complaints Card -->
    <a href="<?php echo STAFF_URL; ?>/complaints/dashboard.php" class="form-card">
        <div class="form-card-header">
            <div class="form-card-icon complaints">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <?php if (($complaintsStats['new'] ?? 0) > 0): ?>
                <span class="form-card-badge badge-danger">
                    <?php echo $complaintsStats['new']; ?> New
                </span>
            <?php endif; ?>
        </div>
        <h3 class="form-card-title">Complaints</h3>
        <p class="form-card-description">
            Manage customer complaints about billing, service quality, power outages, and more.
        </p>
        <div class="form-card-stats">
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($complaintsStats['total'] ?? 0); ?></span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value"><?php echo number_format($complaintsStats['investigating'] ?? 0); ?></span>
                <span class="stat-label">Active</span>
            </div>
        </div>
    </a>

    <!-- Service Requests Card -->
    <a href="#" class="form-card" onclick="alert('Service Requests dashboard coming soon!'); return false;">
        <div class="form-card-header">
            <div class="form-card-icon service">
                <i class="fas fa-plug"></i>
            </div>
            <span class="form-card-badge badge-info">Coming Soon</span>
        </div>
        <h3 class="form-card-title">Service Requests</h3>
        <p class="form-card-description">
            Process new service connection applications and installation requests.
        </p>
        <div class="form-card-stats">
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Active</span>
            </div>
        </div>
    </a>

    <!-- Reconnections Card -->
    <a href="#" class="form-card" onclick="alert('Reconnections dashboard coming soon!'); return false;">
        <div class="form-card-header">
            <div class="form-card-icon reconnection">
                <i class="fas fa-power-off"></i>
            </div>
            <span class="form-card-badge badge-success">Coming Soon</span>
        </div>
        <h3 class="form-card-title">Reconnections</h3>
        <p class="form-card-description">
            Handle electric service reconnection requests and process payments.
        </p>
        <div class="form-card-stats">
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Active</span>
            </div>
        </div>
    </a>

    <!-- Meter Replacements Card -->
    <a href="#" class="form-card" onclick="alert('Meter Replacements dashboard coming soon!'); return false;">
        <div class="form-card-header">
            <div class="form-card-icon meter">
                <i class="fas fa-tachometer-alt"></i>
            </div>
            <span class="form-card-badge badge-warning">Coming Soon</span>
        </div>
        <h3 class="form-card-title">Meter Replacements</h3>
        <p class="form-card-description">
            Manage electric meter replacement and upgrade requests from customers.
        </p>
        <div class="form-card-stats">
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Total</span>
            </div>
            <div class="stat-item">
                <span class="stat-value">0</span>
                <span class="stat-label">Active</span>
            </div>
        </div>
    </a>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
