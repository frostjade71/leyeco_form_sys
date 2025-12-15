<?php
/**
 * LEYECO III Forms Management System
 * Complaints API
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../app/auth_middleware.php';
require_once __DIR__ . '/../../forms/complaints/app/ComplaintController.php';

$controller = new ComplaintController();

// Get request method
$method = $_SERVER['REQUEST_METHOD'];

// Handle GET requests
if ($method === 'GET') {
    $action = $_GET['action'] ?? '';
    
    if ($action === 'get' && isset($_GET['id'])) {
        // Get single complaint
        $complaint = $controller->getById($_GET['id']);
        
        if ($complaint) {
            echo json_encode([
                'success' => true,
                'complaint' => $complaint
            ]);
        } else {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'error' => 'Complaint not found'
            ]);
        }
    } elseif ($action === 'statistics') {
        // Get statistics
        $stats = $controller->getStatistics();
        echo json_encode([
            'success' => true,
            'statistics' => $stats
        ]);
    } else {
        // Get all complaints with filters
        $filters = [
            'status' => $_GET['status'] ?? '',
            'type' => $_GET['type'] ?? '',
            'municipality' => $_GET['municipality'] ?? '',
            'search' => $_GET['search'] ?? ''
        ];
        
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $perPage = isset($_GET['per_page']) ? (int)$_GET['per_page'] : 20;
        
        $result = $controller->getAll($filters, $page, $perPage);
        
        echo json_encode([
            'success' => true,
            'data' => $result
        ]);
    }
}

// Handle POST requests
elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $input['action'] ?? '';
    
    if ($action === 'update_status' && isset($input['id']) && isset($input['status'])) {
        // Update complaint status
        $result = $controller->updateStatus(
            $input['id'],
            $input['status'],
            $currentUser['id']
        );
        
        echo json_encode($result);
    } elseif ($action === 'add_comment' && isset($input['id']) && isset($input['comment'])) {
        // Add comment
        $result = $controller->addComment(
            $input['id'],
            $currentUser['id'],
            $input['comment']
        );
        
        echo json_encode($result);
    } elseif ($action === 'assign' && isset($input['id']) && isset($input['user_id'])) {
        // Assign technician
        $result = $controller->assignTechnician(
            $input['id'],
            $input['user_id'],
            $currentUser['id']
        );
        
        echo json_encode($result);
    } elseif ($action === 'update_dispatch' && isset($input['id'])) {
        // Update dispatch details
        $result = $controller->updateDispatch(
            $input['id'],
            $input,
            $currentUser['id']
        );
        
        echo json_encode($result);
    } else {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => 'Invalid action or missing parameters'
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
