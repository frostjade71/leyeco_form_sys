<?php
/**
 * Requisition Controller
 * Handles requisition request operations
 */

class RequisitionController {
    private $conn;
    
    public function __construct() {
        // Get database connection from global config
        global $conn;
        if (!$conn) {
            require_once __DIR__ . '/../../../config/database.php';
            global $conn;
        }
        $this->conn = $conn;
    }
    
    /**
     * Generate unique RF control number
     * Format: RF-YYYYMMDD-XXXX
     */
    public function generateRFNumber() {
        $date = date('Ymd');
        $prefix = "RF-{$date}-";
        
        // Get the last RF number for today
        $stmt = $this->conn->prepare("
            SELECT rf_control_number 
            FROM requisition_requests 
            WHERE rf_control_number LIKE ? 
            ORDER BY rf_control_number DESC 
            LIMIT 1
        ");
        $searchPattern = "{$prefix}%";
        $stmt->bind_param("s", $searchPattern);
        $stmt->execute();
        $result = $stmt->get_result();
        $lastRF = $result->fetch_row()[0] ?? null;
        $stmt->close();
        
        if ($lastRF) {
            // Extract the sequence number and increment
            $lastSequence = (int)substr($lastRF, -4);
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1;
        }
        
        return $prefix . str_pad($newSequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Submit a new requisition request
     */
    public function submitRequest($data) {
        try {
            $this->conn->begin_transaction();
            
            // Generate RF control number
            $rfNumber = $this->generateRFNumber();
            
            // Insert main request
            $stmt = $this->conn->prepare("
                INSERT INTO requisition_requests 
                (rf_control_number, requester_name, department, purpose, status, current_approval_level) 
                VALUES (?, ?, ?, ?, 'pending', 1)
            ");
            $stmt->bind_param("ssss", 
                $rfNumber,
                $data['requester_name'],
                $data['department'],
                $data['purpose']
            );
            $stmt->execute();
            $requisitionId = $this->conn->insert_id;
            $stmt->close();
            
            // Insert items
            $itemStmt = $this->conn->prepare("
                INSERT INTO requisition_items 
                (requisition_id, quantity, unit, description) 
                VALUES (?, ?, ?, ?)
            ");
            
            foreach ($data['items'] as $item) {
                $itemStmt->bind_param("iiss",
                    $requisitionId,
                    $item['quantity'],
                    $item['unit'],
                    $item['description']
                );
                $itemStmt->execute();
            }
            $itemStmt->close();
            
            // Create approval records for all 5 levels
            $approvalStmt = $this->conn->prepare("
                INSERT INTO approvals 
                (requisition_id, approval_level, approver_role, status) 
                VALUES (?, ?, ?, 'pending')
            ");
            
            foreach (REQ_APPROVAL_LEVELS as $level => $role) {
                $approvalStmt->bind_param("iis", $requisitionId, $level, $role);
                $approvalStmt->execute();
            }
            $approvalStmt->close();
            
            $this->conn->commit();
            
            return [
                'success' => true,
                'rf_number' => $rfNumber,
                'message' => 'Requisition request submitted successfully!'
            ];
            
        } catch (Exception $e) {
            $this->conn->rollback();
            error_log("Error submitting requisition: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Failed to submit requisition request. Please try again.'
            ];
        }
    }
    
    /**
     * Get request by RF control number
     */
    public function getRequestByRFNumber($rfNumber) {
        try {
            // Get main request
            $stmt = $this->conn->prepare("
                SELECT * FROM requisition_requests 
                WHERE rf_control_number = ?
            ");
            $stmt->bind_param("s", $rfNumber);
            $stmt->execute();
            $result = $stmt->get_result();
            $request = $result->fetch_assoc();
            $stmt->close();
            
            if (!$request) {
                return null;
            }
            
            // Get items
            $stmt = $this->conn->prepare("
                SELECT * FROM requisition_items 
                WHERE requisition_id = ? 
                ORDER BY id
            ");
            $stmt->bind_param("i", $request['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $request['items'] = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            // Get approvals
            $stmt = $this->conn->prepare("
                SELECT * FROM approvals 
                WHERE requisition_id = ? 
                ORDER BY approval_level
            ");
            $stmt->bind_param("i", $request['id']);
            $stmt->execute();
            $result = $stmt->get_result();
            $request['approvals'] = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            
            return $request;
            
        } catch (Exception $e) {
            error_log("Error fetching request: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Get statistics for homepage
     */
    public function getStatistics() {
        try {
            $stats = [
                'total' => 0,
                'by_status' => [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0
                ]
            ];
            
            // Total requests
            $result = $this->conn->query("SELECT COUNT(*) as count FROM requisition_requests");
            $row = $result->fetch_assoc();
            $stats['total'] = (int)$row['count'];
            
            // By status
            $result = $this->conn->query("
                SELECT status, COUNT(*) as count 
                FROM requisition_requests 
                GROUP BY status
            ");
            while ($row = $result->fetch_assoc()) {
                $stats['by_status'][$row['status']] = (int)$row['count'];
            }
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error fetching statistics: " . $e->getMessage());
            return [
                'total' => 0,
                'by_status' => [
                    'pending' => 0,
                    'approved' => 0,
                    'rejected' => 0
                ]
            ];
        }
    }
}
