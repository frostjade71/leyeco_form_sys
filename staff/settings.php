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
$additionalCSS = [STAFF_URL . '/assets/css/profile.css'];

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

<!-- Change Password Card -->
<div class="profile-card" style="max-width: 600px; margin: 0 auto;">
    <div class="profile-card-header">
        <h2><i class="fas fa-key"></i> Change Password</h2>
    </div>
    <div class="profile-card-body">
        <form method="POST" action="">
            <input type="hidden" name="action" value="change_password">
            
            <div class="form-group">
                <label for="currentPassword">Current Password</label>
                <input 
                    type="password" 
                    id="currentPassword" 
                    name="current_password" 
                    required
                    style="width: 100%; padding: 10px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 14px;"
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
                    style="width: 100%; padding: 10px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 14px;"
                >
                <small style="color: var(--text-secondary); font-size: 12px; display: block; margin-top: 4px;">Minimum 6 characters</small>
            </div>

            <div class="form-group">
                <label for="confirmPassword">Confirm New Password</label>
                <input 
                    type="password" 
                    id="confirmPassword" 
                    name="confirm_password" 
                    minlength="6"
                    required
                    style="width: 100%; padding: 10px; border: 2px solid var(--border-color); border-radius: 8px; font-size: 14px;"
                >
            </div>

            <button type="submit" class="btn-change-password">
                <i class="fas fa-key"></i>
                Change Password
            </button>
        </form>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
