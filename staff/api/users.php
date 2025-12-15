<?php
/**
 * LEYECO III Forms Management System
 * Users API
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/auth_middleware.php';

// Require admin role
requireAdmin();

$method = $_SERVER['REQUEST_METHOD'];

// Handle GET requests
if ($method === 'GET') {
    if (isset($_GET['id'])) {
        // Get single user
        $stmt = $conn->prepare("SELECT id, username, email, full_name, role, is_active FROM users WHERE id = ?");
        $stmt->bind_param("i", $_GET['id']);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        
        if ($user) {
            echo json_encode([
                'success' => true,
                'user' => $user
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'User not found'
            ]);
        }
    } else {
        // Get all users
        $stmt = $conn->query("SELECT id, username, email, full_name, role, is_active, last_login, created_at FROM users ORDER BY created_at DESC");
        $users = $stmt->fetch_all(MYSQLI_ASSOC);
        
        echo json_encode([
            'success' => true,
            'users' => $users
        ]);
    }
}

// Handle POST requests
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'create') {
        // Create new user
        $fullName = $input['full_name'] ?? '';
        $email = $input['email'] ?? '';
        $username = $input['username'] ?? '';
        $password = $input['password'] ?? '';
        $role = $input['role'] ?? 'staff';
        
        if (empty($fullName) || empty($email) || empty($username) || empty($password)) {
            echo json_encode([
                'success' => false,
                'error' => 'All fields are required'
            ]);
            exit;
        }
        
        // Check if username or email already exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Username or email already exists'
            ]);
            exit;
        }
        
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        
        // Insert user
        $stmt = $conn->prepare("INSERT INTO users (username, password, email, full_name, role) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $username, $hashedPassword, $email, $fullName, $role);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'User created successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to create user'
            ]);
        }
    }
    
    elseif ($action === 'update') {
        // Update user
        $id = $input['id'] ?? 0;
        $fullName = $input['full_name'] ?? '';
        $email = $input['email'] ?? '';
        $username = $input['username'] ?? '';
        $role = $input['role'] ?? 'staff';
        
        if (empty($id) || empty($fullName) || empty($email) || empty($username)) {
            echo json_encode([
                'success' => false,
                'error' => 'All fields are required'
            ]);
            exit;
        }
        
        // Check if username or email already exists for other users
        $stmt = $conn->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
        $stmt->bind_param("ssi", $username, $email, $id);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            echo json_encode([
                'success' => false,
                'error' => 'Username or email already exists'
            ]);
            exit;
        }
        
        // Update user
        if (isset($input['password']) && !empty($input['password'])) {
            // Update with new password
            $hashedPassword = password_hash($input['password'], PASSWORD_BCRYPT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, password = ?, email = ?, full_name = ?, role = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $username, $hashedPassword, $email, $fullName, $role, $id);
        } else {
            // Update without changing password
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, role = ? WHERE id = ?");
            $stmt->bind_param("ssssi", $username, $email, $fullName, $role, $id);
        }
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'User updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update user'
            ]);
        }
    }
    
    elseif ($action === 'toggle_status') {
        // Toggle user active status
        $id = $input['id'] ?? 0;
        $isActive = $input['is_active'] ?? true;
        
        $stmt = $conn->prepare("UPDATE users SET is_active = ? WHERE id = ?");
        $stmt->bind_param("ii", $isActive, $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'User status updated successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to update user status'
            ]);
        }
    }
    
    elseif ($action === 'delete') {
        // Delete user
        $id = $input['id'] ?? 0;
        
        // Don't allow deleting yourself
        if ($id == $currentUser['id']) {
            echo json_encode([
                'success' => false,
                'error' => 'You cannot delete your own account'
            ]);
            exit;
        }
        
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            echo json_encode([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'error' => 'Failed to delete user'
            ]);
        }
    }
    
    else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action'
        ]);
    }
}

// Handle unsupported methods
else {
    http_response_code(405);
    echo json_encode([
        'success' => false,
        'error' => 'Method not allowed'
    ]);
}
?>
