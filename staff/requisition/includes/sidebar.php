<?php
/**
 * LEYECO III Forms Management System
 * Requisition Sidebar
 */

$currentPage = basename($_SERVER['PHP_SELF']);
?>
<aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
        <a href="<?php echo STAFF_URL; ?>/dashboard.php" class="sidebar-logo">
            <img src="/assets/images/logoL3iii.webp" alt="LEYECO III Logo">
            <div class="sidebar-logo-text">
                <h2>LEYECO III</h2>
                <p>Requisition System</p>
            </div>
        </a>
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
            <div class="nav-section-title">Requisition</div>
            <a href="<?php echo STAFF_URL; ?>/requisition/dashboard.php" class="nav-item <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-clipboard-list"></i>
                <span>All Requests</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/requisition/history.php" class="nav-item <?php echo $currentPage === 'history.php' ? 'active' : ''; ?>">
                <i class="fas fa-history"></i>
                <span>My History</span>
            </a>
        </div>
        
        <?php if ($currentUser['role'] === 'admin'): ?>
        <div class="nav-section">
            <div class="nav-section-title">Administration</div>
            <a href="<?php echo STAFF_URL; ?>/requisition/admin_dashboard.php" class="nav-item <?php echo $currentPage === 'admin_dashboard.php' ? 'active' : ''; ?>">
                <i class="fas fa-chart-line"></i>
                <span>Analytics</span>
            </a>
            <a href="<?php echo STAFF_URL; ?>/requisition/reports.php" class="nav-item <?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>">
                <i class="fas fa-file-export"></i>
                <span>Reports</span>
            </a>
        </div>
        <?php endif; ?>
    </nav>
</aside>
