<?php
/**
 * Process Approval API
 * Handles approval/rejection of requisition requests
 */

require_once __DIR__ . '/../../app/auth_middleware.php';
require_once __DIR__ . '/../../../forms/requisition_form/app/config.php';

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid request data');
    }
    
    $requisition_id = (int)($input['requisition_id'] ?? 0);
    $approval_level = (int)($input['approval_level'] ?? 0);
    $action = $input['action'] ?? '';
    $remarks = trim($input['remarks'] ?? '');
    
    // Validate input
    if (!$requisition_id || !$approval_level || !in_array($action, ['approved', 'rejected'])) {
        throw new Exception('Missing required fields');
    }
    
    // Get database connection
    global $conn;
    
    // Start transaction
    $conn->begin_transaction();
    
    // Get current request status
    $stmt = $conn->prepare("
        SELECT status, current_approval_level 
        FROM requisition_requests 
        WHERE id = ?
    ");
    $stmt->bind_param("i", $requisition_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
    
    if (!$request) {
        throw new Exception('Request not found');
    }
    
    if ($request['status'] !== 'pending') {
        throw new Exception('Request is no longer pending');
    }
    
    if ($request['current_approval_level'] != $approval_level) {
        throw new Exception('Invalid approval level');
    }
    
    // Update approval record
    $stmt = $conn->prepare("
        UPDATE approvals 
        SET status = ?, 
            approver_name = ?, 
            remarks = ?, 
            approved_at = NOW() 
        WHERE requisition_id = ? 
        AND approval_level = ?
    ");
    
    $approver_name = $currentUser['full_name'] ?? $currentUser['username'];
    $stmt->bind_param("sssii", 
        $action, 
        $approver_name, 
        $remarks, 
        $requisition_id, 
        $approval_level
    );
    $stmt->execute();
    $stmt->close();
    
    // Update request status
    if ($action === 'rejected') {
        // If rejected, mark entire request as rejected
        $stmt = $conn->prepare("
            UPDATE requisition_requests 
            SET status = 'rejected' 
            WHERE id = ?
        ");
        $stmt->bind_param("i", $requisition_id);
        $stmt->execute();
        $stmt->close();
        
        $message = 'Request has been rejected';
    } else {
        // If approved, check if this is the last level
        if ($approval_level == 5) {
            // Final approval - mark as approved
            $stmt = $conn->prepare("
                UPDATE requisition_requests 
                SET status = 'approved', 
                    current_approval_level = 5 
                WHERE id = ?
            ");
            $stmt->bind_param("i", $requisition_id);
            $stmt->execute();
            $stmt->close();
            
            $message = 'Request has been fully approved!';
        } else {
            // Move to next level
            $next_level = $approval_level + 1;
            $stmt = $conn->prepare("
                UPDATE requisition_requests 
                SET current_approval_level = ? 
                WHERE id = ?
            ");
            $stmt->bind_param("ii", $next_level, $requisition_id);
            $stmt->execute();
            $stmt->close();
            
            $message = "Request approved";
        }
    }
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => $message
    ]);
    
} catch (Exception $e) {
    if (isset($conn)) {
        $conn->rollback();
    }
    
    error_log("Approval error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
