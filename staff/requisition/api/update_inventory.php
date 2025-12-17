<?php
/**
 * Update Inventory API
 * Updates warehouse inventory, balance for purchase, or remarks for items
 */

require_once __DIR__ . '/../../app/auth_middleware.php';

header('Content-Type: application/json');

try {
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    
    if (!$input) {
        throw new Exception('Invalid request data');
    }
    
    $item_id = (int)($input['item_id'] ?? 0);
    $field = $input['field'] ?? '';
    $value = trim($input['value'] ?? '');
    
    // Validate input
    if (!$item_id || !$field) {
        throw new Exception('Missing required fields');
    }
    
    // Validate field name
    $allowed_fields = ['warehouse_inventory', 'balance_for_purchase', 'remarks'];
    if (!in_array($field, $allowed_fields)) {
        throw new Exception('Invalid field');
    }
    
    // Get database connection
    global $conn;
    
    // Update the item
    $stmt = $conn->prepare("
        UPDATE requisition_items 
        SET $field = ? 
        WHERE id = ?
    ");
    $stmt->bind_param("si", $value, $item_id);
    $stmt->execute();
    
    if ($stmt->affected_rows === 0) {
        throw new Exception('Item not found or no changes made');
    }
    
    $stmt->close();
    
    echo json_encode([
        'success' => true,
        'message' => 'Updated successfully'
    ]);
    
} catch (Exception $e) {
    error_log("Update inventory error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
