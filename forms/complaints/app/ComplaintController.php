<?php
/**
 * LEYECO III Complaints System Updated
 * Complaint Controller
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/functions.php';

class ComplaintController {
    private $db;

    public function __construct() {
        // Use the existing database connection from the main config
        global $conn;
        $this->db = $conn;
        
        // Check if the connection is valid
        if ($this->db->connect_error) {
            error_log("Database connection failed: " . $this->db->connect_error);
            die("Database connection failed. Please try again later.");
        }
    }

    /**
     * Create new complaint
     */
    public function create($data) {
        try {
            $referenceCode = $this->generateReferenceCode();
            
            // Assign all values to variables for bind_param (requires pass by reference)
            $reporterName = $data['reporter_name'] ?? null;
            $contact = $data['contact'] ?? null;
            $description = $data['description'];
            $type = $data['type'];
            $municipality = $data['municipality'];
            $barangay = $data['barangay'];
            $address = $data['address'];
            $lat = $data['lat'] ?? null;
            $lon = $data['lon'] ?? null;
            $photoPath = $data['photo_path'] ?? null;
            
            $stmt = $this->db->prepare("
                INSERT INTO complaints (reference_code, reporter_name, contact, description, type, municipality, barangay, address, lat, lon, photo_path)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $stmt->bind_param(
                'ssssssssdds',
                $referenceCode,
                $reporterName,
                $contact,
                $description,
                $type,
                $municipality,
                $barangay,
                $address,
                $lat,
                $lon,
                $photoPath
            );
            
            $stmt->execute();
            $complaintId = $this->db->insert_id;
            $stmt->close();

            // Add initial comment
            $this->addComment($complaintId, null, "Complaint submitted");

            logAudit('COMPLAINT_CREATED', "Complaint {$referenceCode} created");

            return ['success' => true, 'reference_code' => $referenceCode, 'id' => $complaintId];
        } catch (Exception $e) {
            error_log("Complaint creation error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to create complaint'];
        }
    }

    /**
     * Generate a unique reference code in format: CLN{year}{month}{day}-{4 digit number}
     * Example: CLN20251212-0001
     */
    private function generateReferenceCode() {
        $date = date('Ymd'); // Format: 20251212
        
        // Get the count of complaints created today
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count 
            FROM complaints 
            WHERE DATE(created_at) = CURDATE()
        ");
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        
        // Increment count and format with leading zeros (4 digits)
        $sequentialNumber = str_pad($row['count'] + 1, 4, '0', STR_PAD_LEFT);
        
        return "CLN{$date}-{$sequentialNumber}";
    }

    /**
     * Get complaint by reference code
     */
    public function getByReferenceCode($referenceCode) {
        try {
            $referenceCode = trim($referenceCode);
            $stmt = $this->db->prepare("
                SELECT c.*, u.full_name as assigned_to_name
                FROM complaints c
                LEFT JOIN users u ON c.assigned_to = u.id
                WHERE UPPER(c.reference_code) = UPPER(?)
            ");
            $stmt->bind_param('s', $referenceCode);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaint = $result->fetch_assoc();
            $stmt->close();

            if ($complaint) {
                $complaint['comments'] = $this->getComments($complaint['id']);
            }

            return $complaint;
        } catch (Exception $e) {
            error_log("Get complaint error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get all complaints with filters
     */
    public function getAll($filters = [], $page = 1, $perPage = 20) {
        try {
            $where = [];
            $filterParams = [];
            $filterTypes = '';

            if (!empty($filters['status'])) {
                $where[] = "status = ?";
                $filterParams[] = $filters['status'];
                $filterTypes .= 's';
            }

            if (!empty($filters['municipality'])) {
                $where[] = "municipality = ?";
                $filterParams[] = $filters['municipality'];
                $filterTypes .= 's';
            }

            if (!empty($filters['type'])) {
                $where[] = "type = ?";
                $filterParams[] = $filters['type'];
                $filterTypes .= 's';
            }

            if (!empty($filters['search'])) {
                $where[] = "(reference_code LIKE ? OR description LIKE ? OR address LIKE ?)";
                $searchTerm = "%{$filters['search']}%";
                $filterParams[] = $searchTerm;
                $filterParams[] = $searchTerm;
                $filterParams[] = $searchTerm;
                $filterTypes .= 'sss';
            }

            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

            // Get total count
            $countSql = "SELECT COUNT(*) as total FROM complaints $whereClause";
            $countStmt = $this->db->prepare($countSql);
            
            if (!empty($filterParams)) {
                $bindParams = [];
                $bindParams[] = & $filterTypes;
                foreach ($filterParams as $key => $value) {
                    $bindParams[] = & $filterParams[$key];
                }
                call_user_func_array([$countStmt, 'bind_param'], $bindParams);
            }
            
            $countStmt->execute();
            $total = $countStmt->get_result()->fetch_assoc()['total'];
            $countStmt->close();

            // Get paginated results
            $offset = ($page - 1) * $perPage;
            $sql = "
                SELECT c.*, u.full_name as assigned_to_name
                FROM complaints c
                LEFT JOIN users u ON c.assigned_to = u.id
                $whereClause
                ORDER BY c.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $stmt = $this->db->prepare($sql);
            
            // Combine filter parameters with pagination parameters
            $allParams = $filterParams;
            $allParams[] = $perPage;
            $allParams[] = $offset;
            $allTypes = $filterTypes . 'ii';
            
            // Bind all parameters
            $bindParams = [];
            $bindParams[] = & $allTypes;
            foreach ($allParams as $key => $value) {
                $bindParams[] = & $allParams[$key];
            }
            call_user_func_array([$stmt, 'bind_param'], $bindParams);
            
            $stmt->execute();
            $result = $stmt->get_result();
            $complaints = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();

            return [
                'complaints' => $complaints,
                'total' => $total,
                'page' => $page,
                'per_page' => $perPage,
                'total_pages' => ceil($total / $perPage)
            ];
        } catch (Exception $e) {
            error_log("Get complaints error: " . $e->getMessage());
            // DEBUG: Display error on screen
            echo '<div style="background: #fee; color: #c00; padding: 10px; border: 1px solid #fcc; margin: 10px;">Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
            return ['complaints' => [], 'total' => 0, 'page' => 1, 'per_page' => $perPage, 'total_pages' => 0];
        }
    }

    /**
     * Update complaint status
     */
    public function updateStatus($complaintId, $status, $userId = null) {
        try {
            $stmt = $this->db->prepare("UPDATE complaints SET status = ? WHERE id = ?");
            $stmt->bind_param('si', $status, $complaintId);
            $stmt->execute();
            $stmt->close();

            // Get complaint reference code
            $complaint = $this->getById($complaintId);
            
            // Add comment about status change
            $this->addComment($complaintId, $userId, "Status changed to: " . $status);

            logAudit('COMPLAINT_STATUS_UPDATED', "Complaint {$complaint['reference_code']} status changed to {$status}", $userId);

            return ['success' => true];
        } catch (Exception $e) {
            error_log("Update status error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update status'];
        }
    }

    /**
     * Add comment to complaint
     */
    public function addComment($complaintId, $userId, $message) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO complaint_comments (complaint_id, user_id, message) 
                VALUES (?, ?, ?)
            ");
            $userId = $userId ?: null;
            $stmt->bind_param('iis', $complaintId, $userId, $message);
            $stmt->execute();
            $stmt->close();
            return ['success' => true];
        } catch (Exception $e) {
            error_log("Add comment error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to add comment'];
        }
    }

    /**
     * Get comments for a complaint
     */
    public function getComments($complaintId) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, u.full_name as user_name
                FROM complaint_comments c
                LEFT JOIN users u ON c.user_id = u.id
                WHERE c.complaint_id = ?
                ORDER BY c.created_at ASC
            ");
            $stmt->bind_param('i', $complaintId);
            $stmt->execute();
            $result = $stmt->get_result();
            $comments = $result->fetch_all(MYSQLI_ASSOC);
            $stmt->close();
            return $comments;
        } catch (Exception $e) {
            error_log("Get comments error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Assign technician to complaint
     */
    public function assignTechnician($complaintId, $userId, $assignedBy = null) {
        try {
            $stmt = $this->db->prepare("
                UPDATE complaints 
                SET assigned_to = ? 
                WHERE id = ?
            ");
            $stmt->bind_param('ii', $userId, $complaintId);
            $stmt->execute();
            $stmt->close();

            $userStmt = $this->db->prepare("SELECT full_name FROM users WHERE id = ?");
            $userStmt->bind_param('i', $userId);
            $userStmt->execute();
            $user = $userStmt->get_result()->fetch_assoc();
            $userStmt->close();

            // Add comment
            $this->addComment($complaintId, $assignedBy, "Assigned to: " . $user['full_name']);

            $complaint = $this->getById($complaintId);
            logAudit('COMPLAINT_ASSIGNED', "Complaint {$complaint['reference_code']} assigned to user {$userId}", $assignedBy);

            return ['success' => true];
        } catch (Exception $e) {
            error_log("Assign technician error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to assign technician'];
        }
    }

    /**
     * Update dispatch details
     */
    public function updateDispatch($complaintId, $data, $userId = null) {
        try {
            // Validate dispatch mode
            $validModes = ['Handcarried', 'Radio/SMS/Chat/E-mail', 'Others'];
            if (!empty($data['dispatch_mode']) && !in_array($data['dispatch_mode'], $validModes)) {
                return ['success' => false, 'error' => 'Invalid dispatch mode'];
            }

            // Prepare variables for bind_param
            $dispatchTo = $data['dispatch_to'] ?? null;
            $dispatchMode = $data['dispatch_mode'] ?? null;
            $dispatchBy = $userId;
            $dispatchDate = !empty($data['dispatch_date']) ? $data['dispatch_date'] : date('Y-m-d H:i:s');
            $actionTaken = $data['action_taken'] ?? null;
            $acknowledgedBy = $data['acknowledged_by'] ?? null;
            $dateSettled = $data['date_settled'] ?? null;

            $stmt = $this->db->prepare("
                UPDATE complaints 
                SET dispatch_to = ?, 
                    dispatch_mode = ?, 
                    dispatch_by = ?, 
                    dispatch_date = ?,
                    action_taken = ?,
                    acknowledged_by = ?,
                    date_settled = ?
                WHERE id = ?
            ");
            
            $stmt->bind_param(
                'ssissssi',
                $dispatchTo,
                $dispatchMode,
                $dispatchBy,
                $dispatchDate,
                $actionTaken,
                $acknowledgedBy,
                $dateSettled,
                $complaintId
            );
            
            $stmt->execute();
            $stmt->close();

            // Get complaint reference code
            $complaint = $this->getById($complaintId);
            
            // Add comment about dispatch
            $this->addComment($complaintId, $userId, "Dispatch details updated - Dispatched to: " . ($dispatchTo ?? 'N/A'));

            logAudit('COMPLAINT_DISPATCH_UPDATED', "Complaint {$complaint['reference_code']} dispatch details updated", $userId);

            return ['success' => true];
        } catch (Exception $e) {
            error_log("Update dispatch error: " . $e->getMessage());
            return ['success' => false, 'error' => 'Failed to update dispatch details'];
        }
    }

    /**
     * Get complaint by ID
     */
    public function getById($id) {
        try {
            $stmt = $this->db->prepare("
                SELECT c.*, 
                       u.full_name as assigned_to_name,
                       u2.full_name as dispatch_by_name
                FROM complaints c
                LEFT JOIN users u ON c.assigned_to = u.id
                LEFT JOIN users u2 ON c.dispatch_by = u2.id
                WHERE c.id = ?
            ");
            $stmt->bind_param('i', $id);
            $stmt->execute();
            $result = $stmt->get_result();
            $complaint = $result->fetch_assoc();
            $stmt->close();
            
            if ($complaint) {
                $complaint['comments'] = $this->getComments($complaint['id']);
            }
            
            return $complaint;
        } catch (Exception $e) {
            error_log("Get complaint by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Get statistics
     */
    public function getStatistics() {
        try {
            $stats = [];

            // Total complaints
            $result = $this->db->query("SELECT COUNT(*) as total FROM complaints");
            $stats['total'] = $result->fetch_assoc()['total'];

            // By status
            $result = $this->db->query("SELECT status, COUNT(*) as count FROM complaints GROUP BY status");
            $stats['by_status'] = [];
            while ($row = $result->fetch_assoc()) {
                $stats['by_status'][$row['status']] = $row['count'];
            }

            // By type
            $result = $this->db->query("SELECT type, COUNT(*) as count FROM complaints GROUP BY type");
            $stats['by_type'] = [];
            while ($row = $result->fetch_assoc()) {
                $stats['by_type'][$row['type']] = $row['count'];
            }

            // By municipality
            $result = $this->db->query("
                SELECT municipality, COUNT(*) as count 
                FROM complaints 
                GROUP BY municipality 
                ORDER BY count DESC 
                LIMIT 10
            ");
            $stats['by_municipality'] = [];
            while ($row = $result->fetch_assoc()) {
                $stats['by_municipality'][] = $row;
            }

            // Recent complaints (last 7 days)
            $result = $this->db->query("
                SELECT COUNT(*) as count 
                FROM complaints 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ");
            $stats['recent'] = $result->fetch_assoc()['count'];

            return $stats;
        } catch (Exception $e) {
            error_log("Get statistics error: " . $e->getMessage());
            return [];
        }
    }
}