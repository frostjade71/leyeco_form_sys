/**
 * LEYECO III Forms Management System
 * User Management JavaScript
 */

// Open add user modal
function openAddUserModal() {
    document.getElementById('userModal').classList.add('active');
    document.getElementById('modalTitle').textContent = 'Add New User';
    document.getElementById('modalTitle').nextElementSibling.textContent = 'Fill in the details below to create a new user account';
    document.getElementById('submitText').textContent = 'Add User';
    document.getElementById('passwordNote').style.display = 'none';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('userAvatarPreview').innerHTML = '<i class="fas fa-user"></i>';
    document.getElementById('password').required = true;
}

// Close user modal
function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
    document.getElementById('password').type = 'password';
    document.getElementById('passwordToggleIcon').classList.remove('fa-eye-slash');
    document.getElementById('passwordToggleIcon').classList.add('fa-eye');
}

// Edit user
async function editUser(id) {
    try {
        const response = await fetch(`${window.location.origin}/staff/api/users.php?id=${id}`);
        const data = await response.json();
        
        if (data.success) {
            const user = data.user;
            
            document.getElementById('userModal').classList.add('active');
            document.getElementById('modalTitle').textContent = 'Edit User';
            document.getElementById('modalTitle').nextElementSibling.textContent = 'Update user information and permissions';
            document.getElementById('submitText').textContent = 'Update User';
            document.getElementById('passwordNote').style.display = 'inline';
            
            document.getElementById('userId').value = user.id;
            document.getElementById('fullName').value = user.full_name;
            document.getElementById('email').value = user.email;
            document.getElementById('username').value = user.username;
            document.getElementById('password').value = '';
            document.getElementById('password').required = false;
            document.getElementById('role').value = user.role;
            
            // Update avatar preview
            document.getElementById('userAvatarPreview').innerHTML = user.full_name.charAt(0).toUpperCase();
        } else {
            alert('Error: ' + (data.error || 'Failed to load user'));
        }
    } catch (error) {
        console.error('Error loading user:', error);
        alert('Failed to load user details');
    }
}

// Toggle user status
async function toggleUserStatus(id, activate) {
    const action = activate ? 'activate' : 'deactivate';
    if (!confirm(`Are you sure you want to ${action} this user?`)) {
        return;
    }
    
    try {
        const response = await fetch(`${window.location.origin}/staff/api/users.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'toggle_status',
                id: id,
                is_active: activate
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert(`User ${action}d successfully!`);
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to update user status'));
        }
    } catch (error) {
        console.error('Error updating user status:', error);
        alert('Failed to update user status');
    }
}

// Delete user
async function deleteUser(id) {
    if (!confirm('Are you sure you want to delete this user? This action cannot be undone.')) {
        return;
    }
    
    try {
        const response = await fetch(`${window.location.origin}/staff/api/users.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                action: 'delete',
                id: id
            })
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('User deleted successfully!');
            window.location.reload();
        } else {
            alert('Error: ' + (data.error || 'Failed to delete user'));
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        alert('Failed to delete user');
    }
}

// Handle form submission
document.getElementById('userForm')?.addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const userId = formData.get('user_id');
    const action = userId ? 'update' : 'create';
    
    const data = {
        action: action,
        full_name: formData.get('full_name'),
        email: formData.get('email'),
        username: formData.get('username'),
        role: formData.get('role')
    };
    
    if (userId) {
        data.id = userId;
    }
    
    const password = formData.get('password');
    if (password) {
        data.password = password;
    }
    
    try {
        const response = await fetch(`${window.location.origin}/staff/api/users.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert(`User ${action === 'create' ? 'created' : 'updated'} successfully!`);
            window.location.reload();
        } else {
            alert('Error: ' + (result.error || `Failed to ${action} user`));
        }
    } catch (error) {
        console.error(`Error ${action}ing user:`, error);
        alert(`Failed to ${action} user`);
    }
});

// Close modal when clicking outside
document.getElementById('userModal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closeUserModal();
    }
});
