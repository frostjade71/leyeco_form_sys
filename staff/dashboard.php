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
    
    // Requisitions statistics
    $reqStmt = $conn->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending
        FROM requisition_requests
    ");
    $reqStats = $reqStmt->fetch_assoc();

    // Total submissions across all forms
    $totalSubmissions = $complaintsStats['total'] + ($reqStats['total'] ?? 0); // Added requisitions
    
    // Online staffs count
    $onlineStmt = $conn->query("
        SELECT COUNT(DISTINCT user_id) as online_count
        FROM sessions
        WHERE expires_at > NOW()
    ");
    $onlineStats = $onlineStmt->fetch_assoc();
    $onlineStaffs = $onlineStats['online_count'] ?? 0;
    
} catch (Exception $e) {
    error_log("Dashboard stats error: " . $e->getMessage());
    $complaintsStats = ['total' => 0, 'new' => 0, 'investigating' => 0, 'resolved' => 0];
    $totalSubmissions = 0;
    $onlineStaffs = 0;
}

include __DIR__ . '/includes/header.php';
?>

<!-- Welcome Section -->
<div class="welcome-section">
    <div class="welcome-content">
        <h2>Welcome back 👋<br><?php echo htmlspecialchars($currentUser['full_name']); ?></h2>
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
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-signal"></i>
        </div>
        <div class="stat-content">
            <h3>Online Staffs</h3>
            <div class="value"><?php echo number_format($onlineStaffs ?? 0); ?></div>
        </div>
    </div>
    <div class="quick-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);">
            <i class="fas fa-database"></i>
        </div>
        <div class="stat-content">
            <h3>Total Submissions</h3>
            <div class="value"><?php echo number_format($totalSubmissions ?? 0); ?></div>
        </div>
    </div>
    <div class="quick-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);">
            <i class="fas fa-hourglass-half"></i>
        </div>
        <div class="stat-content">
            <h3>Pending Review</h3>
            <div class="value"><?php echo number_format($complaintsStats['new'] ?? 0); ?></div>
        </div>
    </div>
    <div class="quick-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);">
            <i class="fas fa-tasks"></i>
        </div>
        <div class="stat-content">
            <h3>In Progress</h3>
            <div class="value"><?php echo number_format($complaintsStats['investigating'] ?? 0); ?></div>
        </div>
    </div>
    <div class="quick-stat-card">
        <div class="stat-icon" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%);">
            <i class="fas fa-check-double"></i>
        </div>
        <div class="stat-content">
            <h3>Resolved</h3>
            <div class="value"><?php echo number_format($complaintsStats['resolved'] ?? 0); ?></div>
        </div>
    </div>
</div>

<!-- Main Dashboard Content -->
<div class="dashboard-content-grid">
    <!-- Left Column: Recent Activity -->
    <div class="dashboard-left-column">
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-exclamation-triangle"></i> Recent Complaints</h3>
                <a href="<?php echo STAFF_URL; ?>/complaints/dashboard.php" class="section-link">
                    View All <i class="fas fa-arrow-right"></i>
                </a>
            </div>
            <div class="recent-items-list">
                <?php
                // Get recent complaints
                try {
                    $recentStmt = $conn->query("
                        SELECT id, reference_code, type, status, created_at 
                        FROM complaints 
                        ORDER BY created_at DESC 
                        LIMIT 5
                    ");
                    $hasComplaints = false;
                    while ($complaint = $recentStmt->fetch_assoc()):
                        $hasComplaints = true;
                        $statusClass = strtolower($complaint['status']);
                        $icon = 'fa-file-alt';
                        if ($complaint['type'] == 'Power Outage') $icon = 'fa-bolt';
                        elseif ($complaint['type'] == 'Billing Issue') $icon = 'fa-file-invoice-dollar';
                        elseif ($complaint['type'] == 'Service Quality') $icon = 'fa-star';
                        
                        // Create the link to complaints dashboard
                        $complaintLink = STAFF_URL . '/complaints/dashboard.php?view=' . $complaint['id'];
                ?>
                <a href="<?php echo $complaintLink; ?>" class="recent-item recent-item-link">
                    <div class="recent-item-icon">
                        <i class="fas <?php echo $icon; ?>"></i>
                    </div>
                    <div class="recent-item-content">
                        <div class="recent-item-title"><?php echo htmlspecialchars($complaint['reference_code']); ?></div>
                        <div class="recent-item-meta"><?php echo date('M d, g:i a', strtotime($complaint['created_at'])); ?></div>
                    </div>
                    <div class="recent-item-status">
                        <span class="status-badge status-<?php echo $statusClass; ?>">
                            <?php echo htmlspecialchars($complaint['status']); ?>
                        </span>
                    </div>
                </a>
                <?php 
                    endwhile;
                    if (!$hasComplaints):
                ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <p>No recent complaints</p>
                </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error loading complaints</p></div>';
                }
                ?>
            </div>
        </div>

        <!-- Online Staffs Section -->
        <div class="section-card" style="margin-top: 24px;">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-users"></i> Online Staffs</h3>
                <span class="online-count-badge"><i class="fas fa-user"></i> <?php echo $onlineStaffs; ?></span>
            </div>
            <div class="online-staffs-list">
                <?php
                // Get all staff members with online status and last activity
                try {
                    // Check if last_activity column exists
                    $checkColumn = $conn->query("SHOW COLUMNS FROM sessions LIKE 'last_activity'");
                    $hasLastActivity = $checkColumn && $checkColumn->num_rows > 0;
                    
                    if ($hasLastActivity) {
                        // New query with last_activity
                        $onlineStaffStmt = $conn->query("
                            SELECT 
                                u.id, 
                                u.full_name, 
                                u.role, 
                                u.last_login,
                                MAX(CASE WHEN s.expires_at > NOW() THEN 1 ELSE 0 END) as is_online,
                                MAX(s.last_activity) as last_activity
                            FROM users u
                            LEFT JOIN sessions s ON u.id = s.user_id
                            WHERE (u.role LIKE '%admin%' OR u.role LIKE '%staff%' OR u.role LIKE '%approver%')
                            GROUP BY u.id, u.full_name, u.role, u.last_login
                            ORDER BY is_online DESC, last_activity DESC, u.last_login DESC
                            LIMIT 5
                        ");
                    } else {
                        // Fallback query without last_activity (for servers without migration)
                        $onlineStaffStmt = $conn->query("
                            SELECT 
                                u.id, 
                                u.full_name, 
                                u.role, 
                                u.last_login,
                                MAX(CASE WHEN s.expires_at > NOW() THEN 1 ELSE 0 END) as is_online,
                                NULL as last_activity
                            FROM users u
                            LEFT JOIN sessions s ON u.id = s.user_id
                            WHERE (u.role LIKE '%admin%' OR u.role LIKE '%staff%' OR u.role LIKE '%approver%')
                            GROUP BY u.id, u.full_name, u.role, u.last_login
                            ORDER BY is_online DESC, u.last_login DESC
                            LIMIT 5
                        ");
                    }
                    $hasStaff = false;
                    while ($staff = $onlineStaffStmt->fetch_assoc()):
                        $hasStaff = true;
                        $roleClass = strtolower($staff['role']);
                        $roleDisplay = ucfirst($staff['role']);
                        $isOnline = $staff['is_online'] == 1;
                        
                        // Calculate activity status
                        if ($isOnline && $staff['last_activity']) {
                            // User has active session - use last_activity from sessions table
                            $lastActivity = strtotime($staff['last_activity']);
                            $timeAgo = time() - $lastActivity;
                            
                            if ($timeAgo < 300) { // Less than 5 minutes
                                $activityText = 'Active Now';
                                $activityClass = 'active-now';
                            } elseif ($timeAgo < 1800) { // 5-30 minutes
                                $minutes = floor($timeAgo / 60);
                                $activityText = 'Active ' . $minutes . ' min' . ($minutes > 1 ? 's' : '') . ' ago';
                                $activityClass = 'active-recent';
                            } else {
                                // Should rarely happen (session should expire after 30 min)
                                $activityText = 'Active Now';
                                $activityClass = 'active-now';
                            }
                        } elseif ($staff['last_login']) {
                            // User is offline - show when they last logged in
                            $lastLogin = strtotime($staff['last_login']);
                            $timeAgo = time() - $lastLogin;
                            
                            if ($timeAgo < 3600) { // Less than 1 hour
                                $minutes = floor($timeAgo / 60);
                                $activityText = 'Inactive for ' . $minutes . ' min' . ($minutes > 1 ? 's' : '');
                                $activityClass = 'inactive';
                            } elseif ($timeAgo < 86400) { // Less than 1 day
                                $hours = floor($timeAgo / 3600);
                                $activityText = 'Inactive for ' . $hours . ' hour' . ($hours > 1 ? 's' : '');
                                $activityClass = 'inactive';
                            } else {
                                $days = floor($timeAgo / 86400);
                                $activityText = 'Inactive for ' . $days . ' day' . ($days > 1 ? 's' : '');
                                $activityClass = 'inactive';
                            }
                        } else {
                            $activityText = 'Never logged in';
                            $activityClass = 'inactive';
                        }
                ?>
                <div class="online-staff-item">
                    <div class="online-indicator <?php echo $isOnline ? 'online' : 'offline'; ?>"></div>
                    <div class="staff-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="staff-info">
                        <div class="staff-name"><?php echo htmlspecialchars($staff['full_name']); ?></div>
                        <div class="staff-meta">
                            <?php 
                            $roles = explode(',', $staff['role']);
                            foreach ($roles as $role):
                                $role = trim($role);
                                $roleClass = strtolower($role);
                                $roleDisplay = ucfirst($role);
                            ?>
                            <span class="staff-role role-<?php echo $roleClass; ?>"><?php echo $roleDisplay; ?></span>
                            <?php endforeach; ?>
                            <span class="staff-activity">• <?php echo $activityText; ?></span>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                    if (!$hasStaff):
                ?>
                <div class="empty-state">
                    <i class="fas fa-user-slash"></i>
                    <p>No staff members found</p>
                </div>
                <?php 
                    endif;
                } catch (Exception $e) {
                    error_log("Online staff error: " . $e->getMessage());
                    echo '<div class="empty-state"><i class="fas fa-exclamation-triangle"></i><p>Error loading staff</p></div>';
                }
                ?>
            </div>
        </div>
    </div>

    <!-- Right Column: Form Cards -->
    <div class="dashboard-right-column">
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title"><i class="fas fa-th-large"></i> Quick Access</h3>
            </div>
            <div class="form-cards-compact">
                <!-- Complaints Card -->
                <a href="<?php echo STAFF_URL; ?>/complaints/dashboard.php" class="form-card-compact">
                    <div class="form-card-compact-icon complaints">
                        <i class="fas fa-exclamation-triangle"></i>
                    </div>
                    <div class="form-card-compact-content">
                        <h4>Complaints</h4>
                        <p><?php echo number_format($complaintsStats['total'] ?? 0); ?> Total</p>
                    </div>
                    <?php if (($complaintsStats['new'] ?? 0) > 0): ?>
                        <span class="compact-badge badge-danger">
                            <?php echo $complaintsStats['new']; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Requisitions Card -->
                <a href="<?php echo STAFF_URL; ?>/requisition/dashboard.php" class="form-card-compact">
                    <div class="form-card-compact-icon requisitions">
                        <i class="fas fa-clipboard-list"></i>
                    </div>
                    <div class="form-card-compact-content">
                        <h4>Requisitions</h4>
                        <p><?php echo number_format($reqStats['total'] ?? 0); ?> Total</p>
                    </div>
                    <?php if (($reqStats['pending'] ?? 0) > 0): ?>
                        <span class="compact-badge badge-warning">
                            <?php echo $reqStats['pending']; ?>
                        </span>
                    <?php endif; ?>
                </a>

                <!-- Service Requests Card -->
                <a href="#" class="form-card-compact" onclick="alert('Service Requests coming soon!'); return false;">
                    <div class="form-card-compact-icon service">
                        <i class="fas fa-plug"></i>
                    </div>
                    <div class="form-card-compact-content">
                        <h4>Service Requests</h4>
                        <p>Coming Soon</p>
                    </div>
                </a>

                <!-- Reconnections Card -->
                <a href="#" class="form-card-compact" onclick="alert('Reconnections coming soon!'); return false;">
                    <div class="form-card-compact-icon reconnection">
                        <i class="fas fa-power-off"></i>
                    </div>
                    <div class="form-card-compact-content">
                        <h4>Reconnections</h4>
                        <p>Coming Soon</p>
                    </div>
                </a>

                <!-- Meter Replacements Card -->
                <a href="#" class="form-card-compact" onclick="alert('Meter Replacements coming soon!'); return false;">
                    <div class="form-card-compact-icon meter">
                        <i class="fas fa-tachometer-alt"></i>
                    </div>
                    <div class="form-card-compact-content">
                        <h4>Meter Replacements</h4>
                        <p>Coming Soon</p>
                    </div>
                </a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
