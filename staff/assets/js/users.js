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
        const baseUrl = window.APP_CONFIG?.BASE_URL || '';
        const response = await fetch(`${baseUrl}/staff/api/users.php?id=${id}`);
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
            
            // Handle multiple roles
            const userRoles = user.role.split(',').map(r => r.trim());
            document.querySelectorAll('.role-checkbox').forEach(checkbox => {
                checkbox.checked = userRoles.includes(checkbox.value);
            });
            
            // Handle approver level
            if (userRoles.includes('approver')) {
                document.getElementById('approverLevelContainer').style.display = 'block';
                document.getElementById('approverLevel').required = true;
                // Set approver level if exists
                if (user.approver_level) {
                    document.getElementById('approverLevel').value = user.approver_level;
                }
            } else {
                document.getElementById('approverLevelContainer').style.display = 'none';
                document.getElementById('approverLevel').required = false;
            }
            
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
        const baseUrl = window.APP_CONFIG?.BASE_URL || '';
        const response = await fetch(`${baseUrl}/staff/api/users.php`, {
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
        const baseUrl = window.APP_CONFIG?.BASE_URL || '';
        const response = await fetch(`${baseUrl}/staff/api/users.php`, {
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
    
    // Get selected roles
    const selectedRoles = [];
    document.querySelectorAll('.role-checkbox:checked').forEach(checkbox => {
        selectedRoles.push(checkbox.value);
    });
    
    // Validate at least one role is selected
    if (selectedRoles.length === 0) {
        alert('Please select at least one role');
        return;
    }
    
    // Validate approver level if approver role is selected
    if (selectedRoles.includes('approver')) {
        const approverLevel = formData.get('approver_level');
        if (!approverLevel) {
            alert('Please select an approval level for the approver role');
            return;
        }
    }
    
    const data = {
        action: action,
        full_name: formData.get('full_name'),
        email: formData.get('email'),
        username: formData.get('username'),
        role: selectedRoles.join(',') // Join roles with comma
    };
    
    // Add approver level if approver role is selected
    if (selectedRoles.includes('approver')) {
        data.approver_level = formData.get('approver_level');
    }
    
    if (userId) {
        data.id = userId;
    }
    
    const password = formData.get('password');
    if (password) {
        data.password = password;
    }
    
    try {
        const baseUrl = window.APP_CONFIG?.BASE_URL || '';
        const response = await fetch(`${baseUrl}/staff/api/users.php`, {
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

// ============================================
// TABLE SORTING FUNCTIONALITY
// ============================================

// Initialize table sorting
document.addEventListener('DOMContentLoaded', function() {
    const table = document.querySelector('.users-table');
    if (!table) return;
    
    const headers = table.querySelectorAll('th.sortable');
    let currentSort = { column: null, direction: 'asc' };
    
    headers.forEach(header => {
        header.addEventListener('click', function() {
            const sortType = this.getAttribute('data-sort');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Determine sort direction
            if (currentSort.column === sortType) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.direction = 'asc';
            }
            currentSort.column = sortType;
            
            // Remove sort classes from all headers
            headers.forEach(h => {
                h.classList.remove('sort-asc', 'sort-desc');
            });
            
            // Add sort class to current header
            this.classList.add(currentSort.direction === 'asc' ? 'sort-asc' : 'sort-desc');
            
            // Sort rows
            rows.sort((a, b) => {
                let aValue, bValue;
                
                switch(sortType) {
                    case 'name':
                        aValue = a.querySelector('.user-details h4')?.textContent.trim().toLowerCase() || '';
                        bValue = b.querySelector('.user-details h4')?.textContent.trim().toLowerCase() || '';
                        break;
                    case 'username':
                        aValue = a.querySelectorAll('td')[1]?.textContent.trim().toLowerCase() || '';
                        bValue = b.querySelectorAll('td')[1]?.textContent.trim().toLowerCase() || '';
                        break;
                    case 'role':
                        aValue = a.querySelectorAll('td')[2]?.textContent.trim().toLowerCase() || '';
                        bValue = b.querySelectorAll('td')[2]?.textContent.trim().toLowerCase() || '';
                        break;
                    case 'status':
                        const statusOrder = { 'online': 3, 'offline': 2, 'inactive': 1 };
                        aValue = statusOrder[a.querySelector('.status-indicator')?.textContent.trim().toLowerCase()] || 0;
                        bValue = statusOrder[b.querySelector('.status-indicator')?.textContent.trim().toLowerCase()] || 0;
                        break;
                    case 'lastlogin':
                        const aLogin = a.querySelectorAll('td')[4]?.textContent.trim();
                        const bLogin = b.querySelectorAll('td')[4]?.textContent.trim();
                        aValue = aLogin === 'Never' ? 0 : new Date(aLogin).getTime() || 0;
                        bValue = bLogin === 'Never' ? 0 : new Date(bLogin).getTime() || 0;
                        break;
                    case 'created':
                        aValue = new Date(a.querySelectorAll('td')[5]?.textContent.trim()).getTime() || 0;
                        bValue = new Date(b.querySelectorAll('td')[5]?.textContent.trim()).getTime() || 0;
                        break;
                    default:
                        aValue = '';
                        bValue = '';
                }
                
                if (typeof aValue === 'string') {
                    return currentSort.direction === 'asc' 
                        ? aValue.localeCompare(bValue)
                        : bValue.localeCompare(aValue);
                } else {
                    return currentSort.direction === 'asc' 
                        ? aValue - bValue
                        : bValue - aValue;
                }
            });
            
            // Re-append sorted rows
            rows.forEach(row => tbody.appendChild(row));
        });
    });
});
