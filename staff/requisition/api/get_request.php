<?php
/**
 * Get Request API
 * Returns request details by ID
 */

require_once __DIR__ . '/../../app/auth_middleware.php';
require_once __DIR__ . '/../../../forms/requisition_form/app/RequisitionController.php';

header('Content-Type: application/json');

try {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Request ID is required');
    }
    
    $id = (int)$_GET['id'];
    
    // Get request from database
    global $conn;
    $stmt = $conn->prepare("
        SELECT r.*, 
               (SELECT COUNT(*) FROM requisition_items WHERE requisition_id = r.id) as item_count
        FROM requisition_requests r
        WHERE r.id = ?
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request = $result->fetch_assoc();
    $stmt->close();
    
    if (!$request) {
        throw new Exception('Request not found');
    }
    
    // Get items
    $stmt = $conn->prepare("
        SELECT * FROM requisition_items 
        WHERE requisition_id = ? 
        ORDER BY id
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request['items'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    // Get approvals
    $stmt = $conn->prepare("
        SELECT * FROM approvals 
        WHERE requisition_id = ? 
        ORDER BY approval_level
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $request['approvals'] = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'request' => $request
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
