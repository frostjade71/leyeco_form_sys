<?php
/**
 * LEYECO III Forms Management System
 * Complaints Sidebar
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="sidebar-logo">
            <img src="<?php echo BASE_URL; ?>/assets/images/logoL3iii.webp" alt="LEYECO III Logo">
            <div class="sidebar-logo-text">
                <h2>LEYECO III</h2>
                <p>Complaints System</p>
            </div>
        </a>
        <button class="sidebar-close" onclick="toggleSidebar()" aria-label="Close sidebar">
            <i class="fas fa-times"></i>
        </button>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="nav-item">
                <i class="fas fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </a>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Complaints</div>
            <a href="<?php echo STAFF_URL; ?>/complaints/dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-list"></i>
                <span>All Complaints</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/complaints/history.php" class="nav-item <?php echo $currentPage === 'history.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>History</span>
            </a>
        </div>
        
        <?php if ($currentUser['role'] === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <a href="<?php echo STAFF_URL; ?>/complaints/analytics.php" class="nav-item <?php echo $currentPage === 'analytics.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/complaints/reports.php" class="nav-item <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-export"></i>
                <span>Reports</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
</aside>
