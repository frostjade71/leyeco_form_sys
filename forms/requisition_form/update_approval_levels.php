<?php
/**
 * Update Approval Levels in Database
 * This script updates existing approval records to reflect the new approval level roles
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/app/config.php';

echo "Starting approval levels update...\n\n";

try {
    // Start transaction
    $conn->begin_transaction();
    
    // Update Level 4: Internal Auditor
    // This handles both old role names that might be at level 4
    $stmt = $conn->prepare("
        UPDATE approvals 
        SET approver_role = ? 
        WHERE approval_level = 4
    ");
    $level4Role = 'Checked By - Internal Auditor';
    $stmt->bind_param("s", $level4Role);
    $stmt->execute();
    $level4Updated = $stmt->affected_rows;
    $stmt->close();
    
    echo "Updated Level 4 records: $level4Updated\n";
    
    // Update Level 5: General Manager
    // This handles both old role names that might be at level 5
    $stmt = $conn->prepare("
        UPDATE approvals 
        SET approver_role = ? 
        WHERE approval_level = 5
    ");
    $level5Role = 'Approved By - General Manager';
    $stmt->bind_param("s", $level5Role);
    $stmt->execute();
    $level5Updated = $stmt->affected_rows;
    $stmt->close();
    
    echo "Updated Level 5 records: $level5Updated\n";
    
    // Commit transaction
    $conn->commit();
    
    echo "\n✅ Successfully updated approval levels!\n";
    echo "Total records updated: " . ($level4Updated + $level5Updated) . "\n";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "\n❌ Error updating approval levels: " . $e->getMessage() . "\n";
}

$conn->close();

