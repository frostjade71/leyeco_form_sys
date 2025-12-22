<?php
/**
 * LEYECO III Forms Management System
 * User Profile Page
 */

require_once __DIR__ . '/app/auth_middleware.php';

// Page configuration
$pageTitle = 'My Profile';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'My Profile']
];
$additionalCSS = [STAFF_URL . '/assets/css/profile.css'];

// Get user account details from database
try {
    $userDetailsStmt = $conn->prepare("
        SELECT created_at, last_login 
        FROM users 
        WHERE id = ?
    ");
    $userDetailsStmt->bind_param("i", $currentUser['id']);
    $userDetailsStmt->execute();
    $userDetails = $userDetailsStmt->get_result()->fetch_assoc();
} catch (Exception $e) {
    error_log("Profile details error: " . $e->getMessage());
    $userDetails = ['created_at' => null, 'last_login' => null];
}

// Get user activity statistics
// Initialize default values
$userComplaints = 0;
$userRequisitions = 0;
$userApprovals = 0;

try {
    // Count user's complaint submissions
    $complaintsStmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM complaints 
        WHERE created_by = ?
    ");
    if ($complaintsStmt) {
        $complaintsStmt->bind_param("i", $currentUser['id']);
        $complaintsStmt->execute();
        $userComplaints = $complaintsStmt->get_result()->fetch_assoc()['count'] ?? 0;
    } else {
        error_log("Profile: complaints table query failed - " . $conn->error);
    }
    
    // Count user's requisition submissions (if applicable)
    $requisitionsStmt = $conn->prepare("
        SELECT COUNT(*) as count 
        FROM requisition_requests 
        WHERE created_by = ?
    ");
    if ($requisitionsStmt) {
        $requisitionsStmt->bind_param("i", $currentUser['id']);
        $requisitionsStmt->execute();
        $userRequisitions = $requisitionsStmt->get_result()->fetch_assoc()['count'] ?? 0;
    } else {
        error_log("Profile: requisition_requests table query failed - " . $conn->error);
    }
    
    // Count approvals if user is an approver
    if (strpos(strtolower($currentUser['role']), 'approver') !== false) {
        $approvalsStmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM requisition_approvals 
            WHERE approver_id = ?
        ");
        if ($approvalsStmt) {
            $approvalsStmt->bind_param("i", $currentUser['id']);
            $approvalsStmt->execute();
            $userApprovals = $approvalsStmt->get_result()->fetch_assoc()['count'] ?? 0;
        } else {
            error_log("Profile: requisition_approvals table query failed - " . $conn->error);
        }
    }
    
} catch (Exception $e) {
    error_log("Profile stats error: " . $e->getMessage());
    $userComplaints = 0;
    $userRequisitions = 0;
    $userApprovals = 0;
}

include __DIR__ . '/includes/header.php';
?>

<!-- Profile Container -->
<div class="profile-container">
    
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="profile-avatar">
            <i class="fas fa-user"></i>
        </div>
        <div class="profile-header-info">
            <h1 class="profile-name"><?php echo htmlspecialchars($currentUser['full_name']); ?></h1>
            <p class="profile-role">
                <i class="fas fa-shield-alt"></i>
                <?php 
                    $roles = explode(',', $currentUser['role']);
                    echo htmlspecialchars(implode(', ', array_map('ucfirst', array_map('trim', $roles)))); 
                ?>
            </p>
        </div>
    </div>
    
    <!-- Profile Content Grid -->
    <div class="profile-grid">
        
        <!-- Personal Information Card -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h2><i class="fas fa-user-circle"></i> Personal Information</h2>
            </div>
            <div class="profile-card-body">
                <div class="info-row">
                    <span class="info-label">Full Name</span>
                    <span class="info-value"><?php echo htmlspecialchars($currentUser['full_name']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Email Address</span>
                    <span class="info-value"><?php echo htmlspecialchars($currentUser['email']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Username</span>
                    <span class="info-value"><?php echo htmlspecialchars($currentUser['username']); ?></span>
                </div>
                <div class="info-row">
                    <span class="info-label">Role</span>
                    <span class="info-value">
                        <?php 
                            $roles = explode(',', $currentUser['role']);
                            foreach ($roles as $role) {
                                $roleClass = 'role-' . strtolower(trim($role));
                                echo '<span class="role-badge ' . $roleClass . '">' . htmlspecialchars(ucfirst(trim($role))) . '</span> ';
                            }
                        ?>
                    </span>
                </div>
                
                <!-- Edit Profile Button -->
                <div style="margin-top: 20px; padding-top: 20px; border-top: 1px solid var(--border-color);">
                    <a href="<?php echo STAFF_URL; ?>/settings.php" class="btn-edit-profile">
                        <i class="fas fa-edit"></i>
                        Edit Profile
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Account Details Card -->
        <div class="profile-card">
            <div class="profile-card-header">
                <h2><i class="fas fa-info-circle"></i> Account Details</h2>
            </div>
            <div class="profile-card-body">
                <div class="info-row">
                    <span class="info-label">Account Created</span>
                    <span class="info-value">
                        <?php 
                            if ($userDetails && $userDetails['created_at']) {
                                echo date('F j, Y', strtotime($userDetails['created_at']));
                            } else {
                                echo 'N/A';
                            }
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Last Login</span>
                    <span class="info-value">
                        <?php 
                            if ($userDetails && $userDetails['last_login']) {
                                echo date('F j, Y g:i A', strtotime($userDetails['last_login']));
                            } else {
                                echo 'Never';
                            }
                        ?>
                    </span>
                </div>
                <div class="info-row">
                    <span class="info-label">Account Status</span>
                    <span class="info-value">
                        <span class="status-badge status-active">
                            <i class="fas fa-check-circle"></i> Active
                        </span>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Activity Summary Card -->
        <div class="profile-card profile-card-wide">
            <div class="profile-card-header">
                <h2><i class="fas fa-chart-bar"></i> Activity Summary</h2>
            </div>
            <div class="profile-card-body">
                <div class="coming-soon-message">
                    <i class="fas fa-clock"></i>
                    <p>Coming Soon</p>
                </div>
            </div>
        </div>
        
    </div>
    
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
