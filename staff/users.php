<?php
/**
 * LEYECO III Forms Management System
 * User Management (Admin Only)
 */

require_once __DIR__ . '/app/auth_middleware.php';

// Require admin role
requireAdmin();

// Page configuration
$pageTitle = 'User Management';
$breadcrumbs = [
    ['label' => 'Dashboard', 'url' => STAFF_URL . '/dashboard.php'],
    ['label' => 'User Management']
];
$additionalCSS = [
    STAFF_URL . '/assets/css/components.css',
    STAFF_URL . '/assets/css/users.css'
];
$additionalJS = [STAFF_URL . '/assets/js/users.js'];

// Get all users with their online status
try {
    $stmt = $conn->query("
        SELECT 
            u.id, 
            u.username, 
            u.email, 
            u.full_name, 
            u.role, 
            u.is_active, 
            u.last_login, 
            u.created_at,
            IF(MAX(s.expires_at) > NOW(), 1, 0) as is_online
        FROM users u
        LEFT JOIN sessions s ON u.id = s.user_id
        GROUP BY u.id, u.username, u.email, u.full_name, u.role, u.is_active, u.last_login, u.created_at
        ORDER BY u.created_at DESC
    ");
    $users = $stmt->fetch_all(MYSQLI_ASSOC);
    
    // Calculate statistics
    $totalStaff = count($users);
    $onlineStaff = 0;
    $onlineApprovers = 0;
    
    foreach ($users as $user) {
        if ($user['is_online']) {
            $onlineStaff++;
            
            // Check if user has approver role
            $roles = explode(',', $user['role']);
            $roles = array_map('trim', $roles);
            if (in_array('approver', $roles)) {
                $onlineApprovers++;
            }
        }
    }
} catch (Exception $e) {
    error_log("Get users error: " . $e->getMessage());
    $users = [];
    $totalStaff = 0;
    $onlineStaff = 0;
    $onlineApprovers = 0;
}

include __DIR__ . '/includes/header.php';
?>

<!-- Page Header -->
<div class="page-header">
    <h2 class="page-title">User Management</h2>
    <p class="page-description">Manage staff and admin accounts</p>
</div>

<!-- Statistics Cards -->
<!-- Statistics Cards -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-card-header">
            <span class="stat-card-title">All Staff</span>
            <div class="stat-card-icon total">
                <i class="fas fa-users"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($totalStaff); ?></div>
    </div>
    <div class="stat-card online">
        <div class="stat-card-header">
            <span class="stat-card-title">Online Staffs</span>
            <div class="stat-card-icon online">
                <i class="fas fa-user-check"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($onlineStaff); ?></div>
    </div>
    <div class="stat-card approver">
        <div class="stat-card-header">
            <span class="stat-card-title">Online Approvers</span>
            <div class="stat-card-icon approver">
                <i class="fas fa-user-shield"></i>
            </div>
        </div>
        <div class="stat-card-value"><?php echo number_format($onlineApprovers); ?></div>
    </div>
</div>

<!-- Users Container -->
<div class="users-container" style="margin-bottom: 40px;">
    <div class="users-header">
        <h3 class="users-title">All Users (<?php echo count($users); ?>)</h3>
        <button class="btn-add-user" onclick="openAddUserModal()">
            <i class="fas fa-plus"></i>
            Add New User
        </button>
    </div>

    <div class="table-responsive">
        <table class="users-table">
            <thead>
                <tr>
                    <th class="sortable" data-sort="name">
                        User 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="username">
                        Username 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="role">
                        Role 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="status">
                        Status 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="lastlogin">
                        Last Login 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th class="sortable" data-sort="created">
                        Created 
                        <i class="fas fa-sort sort-icon"></i>
                    </th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="fas fa-users"></i>
                                <p>No users found</p>
                            </div>
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar-small">
                                        <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
                                    </div>
                                    <div class="user-details">
                                        <h4><?php echo htmlspecialchars($user['full_name']); ?></h4>
                                        <p><?php echo htmlspecialchars($user['email']); ?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <code style="background: var(--light-color); padding: 4px 8px; border-radius: 4px; font-size: 13px; color: var(--text-primary); font-family: 'Courier New', monospace;">
                                    <?php echo htmlspecialchars($user['username']); ?>
                                </code>
                            </td>
                            <td>
                                <?php 
                                $roles = explode(',', $user['role']);
                                foreach ($roles as $role): 
                                    $role = trim($role);
                                ?>
                                    <span class="role-badge <?php echo strtolower($role); ?>" style="margin: 2px;">
                                        <?php echo htmlspecialchars($role); ?>
                                    </span>
                                <?php endforeach; ?>
                            </td>
                            <td>
                                <div class="status-indicator">
                                    <?php if ($user['is_online']): ?>
                                        <span class="status-dot online"></span>
                                        Online
                                    <?php elseif ($user['is_active']): ?>
                                        <span class="status-dot offline"></span>
                                        Offline
                                    <?php else: ?>
                                        <span class="status-dot inactive"></span>
                                        Inactive
                                    <?php endif; ?>
                                </div>
                            </td>
                            <td>
                                <?php 
                                if ($user['last_login']) {
                                    echo date('M d, Y g:i A', strtotime($user['last_login']));
                                } else {
                                    echo '<span style="color: var(--text-secondary);">Never</span>';
                                }
                                ?>
                            </td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <button 
                                        class="btn-icon" 
                                        onclick="editUser(<?php echo $user['id']; ?>)"
                                        title="Edit User"
                                    >
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if ($user['id'] != $currentUser['id']): ?>
                                        <button 
                                            class="btn-icon" 
                                            onclick="toggleUserStatus(<?php echo $user['id']; ?>, <?php echo $user['is_active'] ? 'false' : 'true'; ?>)"
                                            title="<?php echo $user['is_active'] ? 'Deactivate' : 'Activate'; ?>"
                                        >
                                            <i class="fas fa-<?php echo $user['is_active'] ? 'ban' : 'check'; ?>"></i>
                                        </button>
                                        <button 
                                            class="btn-icon danger" 
                                            onclick="deleteUser(<?php echo $user['id']; ?>)"
                                            title="Delete User"
                                        >
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div id="userModal" class="modal">
    <div class="modal-content" style="max-width: 600px;">
        <div class="modal-header" style="background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); color: var(--white); border-radius: 12px 12px 0 0; padding: 24px;">
            <h2 class="modal-title" id="modalTitle" style="color: var(--white); margin-bottom: 4px;">Add New User</h2>
            <p style="font-size: 13px; opacity: 0.9; margin: 0;">Fill in the details below to create a new user account</p>
        </div>
        <div class="modal-body" style="padding: 32px;">
            <form id="userForm">
                <input type="hidden" id="userId" name="user_id">
                
                <!-- User Avatar Preview -->
                <div style="text-align: center; margin-bottom: 24px;">
                    <div id="userAvatarPreview" style="width: 80px; height: 80px; margin: 0 auto; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%); display: flex; align-items: center; justify-content: center; color: var(--white); font-size: 32px; font-weight: 700; box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);">
                        <i class="fas fa-user"></i>
                    </div>
                    <p style="margin-top: 8px; font-size: 12px; color: var(--text-secondary);">User Avatar</p>
                </div>

                <!-- Form Grid -->
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="fullName">
                            <i class="fas fa-user" style="margin-right: 6px; color: var(--primary-color);"></i>
                            Full Name *
                        </label>
                        <input 
                            type="text" 
                            id="fullName" 
                            name="full_name" 
                            placeholder="Enter full name"
                            required
                            oninput="updateAvatarPreview()"
                        >
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label for="username">
                            <i class="fas fa-at" style="margin-right: 6px; color: var(--primary-color);"></i>
                            Username *
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            placeholder="Enter username"
                            required
                        >
                    </div>
                </div>

                <div class="form-group">
                    <label for="email">
                        <i class="fas fa-envelope" style="margin-right: 6px; color: var(--primary-color);"></i>
                        Email Address *
                    </label>
                    <input 
                        type="email" 
                        id="email" 
                        name="email" 
                        placeholder="user@leyeco.com"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">
                        <i class="fas fa-lock" style="margin-right: 6px; color: var(--primary-color);"></i>
                        Password <span id="passwordNote" style="color: var(--text-secondary); font-weight: 400; font-size: 12px;">(leave blank to keep current)</span>
                    </label>
                    <div style="position: relative;">
                        <input 
                            type="password" 
                            id="password" 
                            name="password"
                            placeholder="Enter password (min. 6 characters)"
                            style="padding-right: 40px;"
                        >
                        <button 
                            type="button" 
                            onclick="togglePasswordVisibility()" 
                            style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); background: none; border: none; color: var(--text-secondary); cursor: pointer; padding: 5px;"
                            title="Toggle password visibility"
                        >
                            <i class="fas fa-eye" id="passwordToggleIcon"></i>
                        </button>
                    </div>
                    <small style="color: var(--text-secondary); font-size: 11px; display: block; margin-top: 4px;">
                        <i class="fas fa-info-circle"></i> Minimum 6 characters required
                    </small>
                </div>

                <div class="form-group">
                    <label>
                        <i class="fas fa-shield-alt" style="margin-right: 6px; color: var(--primary-color);"></i>
                        Roles * <span style="font-weight: 400; font-size: 12px; color: var(--text-secondary);">(Select one or more)</span>
                    </label>
                    <div class="roles-container">
                        <div class="roles-list">
                            <label class="role-option">
                                <input type="checkbox" name="roles[]" value="staff" class="role-checkbox">
                                <div>
                                    <div class="role-title">Staff</div>
                                    <div class="role-description">Regular access to forms and submissions</div>
                                </div>
                            </label>
                            
                            <label class="role-option role-option-approver">
                                <input type="checkbox" id="approverCheckbox" name="roles[]" value="approver" class="role-checkbox" onchange="toggleApproverLevel()">
                                <div style="flex: 1;">
                                    <div class="role-title">Approver</div>
                                    <div class="role-description">Can approve/reject requisition requests</div>
                                    
                                    <div id="approverLevelContainer" class="approver-level-container">
                                        <label for="approverLevel" class="level-label">
                                            Select Approval Level:
                                        </label>
                                        <select id="approverLevel" name="approver_level" class="level-select">
                                            <option value="">Choose level...</option>
                                            <?php 
                                            require_once __DIR__ . '/../forms/requisition_form/app/config.php';
                                            foreach (REQ_APPROVAL_LEVELS as $level => $role): 
                                            ?>
                                                <option value="<?php echo $level; ?>">Level <?php echo $level; ?> - <?php echo $role; ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="level-hint">
                                            <i class="fas fa-info-circle"></i> This user will be added to the approvers table
                                        </small>
                                    </div>
                                </div>
                            </label>
                            
                            <label class="role-option">
                                <input type="checkbox" name="roles[]" value="admin" class="role-checkbox">
                                <div>
                                    <div class="role-title">Admin</div>
                                    <div class="role-description">Full system access, can manage users and settings</div>
                                </div>
                            </label>
                        </div>
                    </div>
                    <small style="color: var(--text-secondary); font-size: 11px; display: block; margin-top: 8px;">
                        <i class="fas fa-info-circle"></i> Users can have multiple roles. At least one role must be selected.
                    </small>
                </div>

                <!-- Info Box -->
                <div style="background: #FEF3C7; border-left: 4px solid #F59E0B; padding: 12px 16px; border-radius: 6px; margin-bottom: 24px;">
                    <div style="display: flex; align-items: start; gap: 10px;">
                        <i class="fas fa-exclamation-triangle" style="color: #F59E0B; margin-top: 2px;"></i>
                        <div style="font-size: 12px; color: #92400E;">
                            <strong>Important:</strong> New users will receive their credentials and can change their password after first login.
                        </div>
                    </div>
                </div>

                <div class="form-actions" style="border-top: 1px solid var(--border-color); padding-top: 20px; margin-top: 0;">
                    <button type="button" class="btn btn-secondary" onclick="closeUserModal()" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-times"></i>
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: 8px;">
                        <i class="fas fa-save"></i>
                        <span id="submitText">Add User</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Update avatar preview with first letter of name
function updateAvatarPreview() {
    const fullName = document.getElementById('fullName').value.trim();
    const preview = document.getElementById('userAvatarPreview');
    
    if (fullName) {
        preview.innerHTML = fullName.charAt(0).toUpperCase();
    } else {
        preview.innerHTML = '<i class="fas fa-user"></i>';
    }
}

// Toggle password visibility
function togglePasswordVisibility() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.getElementById('passwordToggleIcon');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}

// Toggle approver level dropdown
function toggleApproverLevel() {
    const checkbox = document.getElementById('approverCheckbox');
    const container = document.getElementById('approverLevelContainer');
    const levelSelect = document.getElementById('approverLevel');
    
    if (checkbox.checked) {
        container.style.display = 'block';
        levelSelect.required = true;
    } else {
        container.style.display = 'none';
        levelSelect.required = false;
        levelSelect.value = '';
    }
}
</script>

<script>
// Configuration for JavaScript
window.APP_CONFIG = {
    BASE_URL: '<?php echo BASE_URL; ?>',
    STAFF_URL: '<?php echo STAFF_URL; ?>'
};
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
