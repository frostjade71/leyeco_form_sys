<?php
/**
 * LEYECO III Forms Management System
 * User Profile & Settings
 */

require_once __DIR__ . '/app/auth_middleware.php';

// Page configuration
$pageTitle = 'Profile & Settings';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'Profile & Settings']
];
$additionalCSS = [STAFF_URL . '/assets/css/users.css'];

$success = '';
$error = '';

// Check for success message from redirect
if (isset($_GET['success'])) {
    if ($_GET['success'] === 'password') {
        $success = 'Password changed successfully!';
    }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    
    if ($action === 'change_password') {
        $currentPassword = $_POST['current_password'] ?? '';
        $newPassword = $_POST['new_password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        
        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $error = 'All password fields are required';
        } elseif ($newPassword !== $confirmPassword) {
            $error = 'New passwords do not match';
        } elseif (strlen($newPassword) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            // Verify current password
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $currentUser['id']);
            $stmt->execute();
            $result = $stmt->get_result()->fetch_assoc();
            
            if (!password_verify($currentPassword, $result['password'])) {
                $error = 'Current password is incorrect';
            } else {
                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
                $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt->bind_param("si", $hashedPassword, $currentUser['id']);
                
                if ($stmt->execute()) {
                    // Redirect to refresh the page with success message
                    header('Location: ' . STAFF_URL . '/settings.php?success=password');
                    exit;
                } else {
                    $error = 'Failed to change password';
                }
            }
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h2 class="page-title">Security Settings</h2>
    <p class="page-description">Manage your password and view account information</p>
</div>

<?php if ($success): ?>
    <div class="alert alert-success" style="margin-bottom: 24px; padding: 14px 16px; background: #d1fae5; color: #065f46; border: 1px solid #a7f3d0; border-radius: 8px;">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error" style="margin-bottom: 24px; padding: 14px 16px; background: #fee2e2; color: #991b1b; border: 1px solid #fecaca; border-radius: 8px;">
        <i class="fas fa-exclamation-circle"></i>
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: 24px;">
    <!-- User Profile Display -->
    <div class="users-container">
        <div class="users-header">
            <h3 class="users-title">Your Profile</h3>
        </div>
        <div style="padding: 24px;">
            <div style="text-align: center; margin-bottom: 24px;">
                <div style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 32px; font-weight: 700; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
                    <?php echo strtoupper(substr($currentUser['full_name'], 0, 1)); ?>
                </div>
            </div>
            
            <div class="form-group">
                <label>Full Name</label>
                <input 
                    type="text" 
                    value="<?php echo htmlspecialchars($currentUser['full_name']); ?>"
                    disabled
                    style="background: var(--light-color); cursor: not-allowed;"
                >
            </div>

            <div class="form-group">
                <label>Email</label>
                <input 
                    type="email" 
                    value="<?php echo htmlspecialchars($currentUser['email']); ?>"
                    disabled
                    style="background: var(--light-color); cursor: not-allowed;"
                >
            </div>

            <div class="form-group">
                <label>Username</label>
                <input 
                    type="text" 
                    value="<?php echo htmlspecialchars($currentUser['username']); ?>"
                    disabled
                    style="background: var(--light-color); cursor: not-allowed;"
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label>Role</label>
                <input 
                    type="text" 
                    value="<?php echo ucfirst(htmlspecialchars($currentUser['role'])); ?>"
                    disabled
                    style="background: var(--light-color); cursor: not-allowed;"
                >
            </div>

            <div style="margin-top: 16px; padding: 12px; background: #FEF3C7; border-left: 4px solid #F59E0B; border-radius: 6px;">
                <div style="font-size: 12px; color: #92400E;">
                    <i class="fas fa-info-circle"></i>
                    <strong>Note:</strong> To update your profile information, please contact an administrator.
                </div>
            </div>
        </div>
    </div>

    <!-- Change Password -->
    <div class="users-container">
        <div class="users-header">
            <h3 class="users-title">Change Password</h3>
        </div>
        <div style="padding: 24px;">
            <form method="POST" action="">
                <input type="hidden" name="action" value="change_password">
                
                <div class="form-group">
                    <label for="currentPassword">Current Password</label>
                    <input 
                        type="password" 
                        id="currentPassword" 
                        name="current_password" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="newPassword">New Password</label>
                    <input 
                        type="password" 
                        id="newPassword" 
                        name="new_password" 
                        minlength="6"
                        required
                    >
                    <small style="color: var(--text-secondary); font-size: 12px;">Minimum 6 characters</small>
                </div>

                <div class="form-group">
                    <label for="confirmPassword">Confirm New Password</label>
                    <input 
                        type="password" 
                        id="confirmPassword" 
                        name="confirm_password" 
                        minlength="6"
                        required
                    >
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%;">
                    <i class="fas fa-key"></i>
                    Change Password
                </button>
            </form>
        </div>
    </div>
</div>

<!-- Account Information -->
<div class="users-container" style="margin-top: 24px;">
    <div class="users-header">
        <h3 class="users-title">Account Information</h3>
    </div>
    <div style="padding: 24px;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px;">
            <div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Account Created</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">
                    <?php
                    $stmt = $conn->prepare("SELECT created_at FROM users WHERE id = ?");
                    $stmt->bind_param("i", $currentUser['id']);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    echo date('F d, Y', strtotime($result['created_at']));
                    ?>
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Last Login</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--text-primary);">
                    <?php
                    $stmt = $conn->prepare("SELECT last_login FROM users WHERE id = ?");
                    $stmt->bind_param("i", $currentUser['id']);
                    $stmt->execute();
                    $result = $stmt->get_result()->fetch_assoc();
                    if ($result['last_login']) {
                        echo date('F d, Y g:i A', strtotime($result['last_login']));
                    } else {
                        echo 'Never';
                    }
                    ?>
                </div>
            </div>
            <div>
                <div style="font-size: 12px; color: var(--text-secondary); margin-bottom: 4px;">Account Status</div>
                <div style="font-size: 14px; font-weight: 600; color: var(--success-color);">
                    <i class="fas fa-check-circle"></i> Active
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
