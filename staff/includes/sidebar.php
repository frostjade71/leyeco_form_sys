<?php
/**
 * LEYECO III Forms Management System
 * Staff Dashboard Sidebar
 */

$currentPage = basename($_SERVER['PHP_SELF']);
$currentDir = basename(dirname($_SERVER['PHP_SELF']));
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="sidebar-logo">
            <img src="/assets/images/logoL3iii.webp" alt="LEYECO III Logo">
            <div class="sidebar-logo-text">
                <h2>LEYECO III</h2>
                <p>Staff Portal</p>
            </div>
        </a>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-home"></i>
                <span>Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Forms Management</div>
            <a href="<?php echo STAFF_URL; ?>/complaints/dashboard.php" class="nav-item <?php echo $currentDir === 'complaints' ? 'active' : ''; ?>">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Complaints</span>
            </a>
            <a href="#" class="nav-item" title="Coming soon">
                <i class="fas fa-file-alt"></i>
                <span>Requisition Requests</span>
            </a>
            <a href="#" class="nav-item" title="Coming soon">
                <i class="fas fa-plug"></i>
                <span>Service Requests</span>
            </a>
            <a href="#" class="nav-item" title="Coming soon">
                <i class="fas fa-power-off"></i>
                <span>Reconnections</span>
            </a>
            <a href="#" class="nav-item" title="Coming soon">
                <i class="fas fa-tachometer-alt"></i>
                <span>Meter Replacements</span>
            </a>
        </div>
        
        <?php if ($currentUser['role'] === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <a href="<?php echo STAFF_URL; ?>/analytics.php" class="nav-item <?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>System Analytics</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/users.php" class="nav-item <?php echo $currentPage === 'users.php' ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>User Management</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/settings.php" class="nav-item <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                <span>Settings</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
</aside>
