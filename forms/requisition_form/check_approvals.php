<?php
/**
 * Test Database Connection and Check Approval Records
 */

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Testing database connection...\n\n";

// Try to include database config
try {
    require_once __DIR__ . '/../../config/database.php';
    echo "✅ Database config loaded\n";
    
    if (isset($conn) && $conn) {
        echo "✅ Database connection established\n\n";
        
        // Check current approval records
        $result = $conn->query("
            SELECT approval_level, approver_role, COUNT(*) as count 
            FROM approvals 
            GROUP BY approval_level, approver_role 
            ORDER BY approval_level
        ");
        
        echo "Current approval records in database:\n";
        echo "=====================================\n";
        while ($row = $result->fetch_assoc()) {
            echo "Level {$row['approval_level']}: {$row['approver_role']} ({$row['count']} records)\n";
        }
        
    } else {
        echo "❌ Database connection not established\n";
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
