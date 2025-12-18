<?php
/**
 * LEYECO III Requisition System - Submit Request
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include main configuration
require_once __DIR__ . '/../../../config/config.php';
require_once __DIR__ . '/../app/config.php';
require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../app/RequisitionController.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Validate input
        if (empty($_POST['requester_name']) || empty($_POST['department']) || empty($_POST['purpose'])) {
            throw new Exception('Please fill in all required fields.');
        }
        
        if (empty($_POST['items']) || !is_array($_POST['items'])) {
            throw new Exception('Please add at least one item to the requisition.');
        }
        
        // Prepare data
        $data = [
            'requester_name' => trim($_POST['requester_name']),
            'department' => trim($_POST['department']),
            'purpose' => trim($_POST['purpose']),
            'items' => []
        ];
        
        // Process items
        foreach ($_POST['items'] as $item) {
            if (!empty($item['quantity']) && !empty($item['unit']) && !empty($item['description'])) {
                $data['items'][] = [
                    'quantity' => (int)$item['quantity'],
                    'unit' => trim($item['unit']),
                    'description' => trim($item['description'])
                ];
            }
        }
        
        if (empty($data['items'])) {
            throw new Exception('Please add at least one valid item.');
        }
        
        // Submit request
        $controller = new RequisitionController();
        $result = $controller->submitRequest($data);
        
        if ($result['success']) {
            // Return JSON response
            header('Content-Type: application/json');
            echo json_encode($result);
            exit;
        } else {
            throw new Exception($result['message']);
        }
        
    } catch (Exception $e) {
        header('Content-Type: application/json');
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
        exit;
    }
}

// Page configuration
$page_title = 'Submit Request - Requisition System';
$additional_css = ['/forms/requisition_form/public/request_form.css'];
$additional_js = ['/forms/requisition_form/assets/js/request_form.js'];

require_once __DIR__ . '/../../../includes/header.php';
?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div class="form-container">
        <div class="card">
            <div class="card-header">
                <h2 class="card-title">📝 Requisition Request Form</h2>
                <p>Fill out the form below to submit a new material requisition request. All fields marked with <span class="required">*</span> are required.</p>
            </div>
            
            <form id="requisitionForm">
                <!-- Requester Information -->
                <div class="form-section">
                    <h3>Requester Information</h3>
                    
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label for="requester_name" class="required">Requester Name</label>
                                <input type="text" id="requester_name" name="requester_name" required>
                            </div>
                        </div>
                        
                        <div class="col-6">
                            <div class="form-group">
                                <label for="department" class="required">Department</label>
                                <select id="department" name="department" required>
                                    <option value="">-- Select Department --</option>
                                    <?php foreach (REQ_DEPARTMENTS as $dept): ?>
                                        <option value="<?php echo e($dept); ?>">
                                            <?php echo e($dept); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="purpose" class="required">Purpose of Request</label>
                        <textarea id="purpose" name="purpose" rows="4" placeholder="Provide detailed purpose for the materials requested..." required></textarea>
                    </div>
                </div>
                
                <!-- Requisition Items -->
                <div class="form-section">
                    <div class="section-header">
                        <h3>Requisition Items</h3>
                        <button type="button" class="btn btn-secondary btn-sm" id="addItemBtn">
                            ➕ Add Item
                        </button>
                    </div>
                    
                    <div class="table-responsive">
                        <table id="itemsTable">
                            <thead>
                                <tr>
                                    <th style="width: 10%;">Qty</th>
                                    <th style="width: 15%;">Unit</th>
                                    <th style="width: 65%;">Description</th>
                                    <th style="width: 10%;">Action</th>
                                </tr>
                            </thead>
                            <tbody id="itemsTableBody">
                                <!-- Items will be added dynamically -->
                            </tbody>
                        </table>
                    </div>
                    
                    <p class="help-text">
                        <strong>Note:</strong> Warehouse inventory and balance for purchase will be filled by the Warehouse Section Head and Budget Officer during approval.
                    </p>
                </div>
                
                <!-- Submit Button -->
                <div class="form-actions">
                    <button type="button" class="btn btn-outline" onclick="window.location.href='<?php echo BASE_URL; ?>/forms/requisition_form/public/homepage.php'">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                        Submit Request
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div id="successModal" class="modal" style="display: none;">
    <div class="modal-content">
        <div class="success-icon">✅</div>
        <h2>Request Submitted Successfully!</h2>
        <p>Your requisition request has been submitted and is now pending approval.</p>
        
        <div class="rf-number-display">
            <label>Your RF Control Number:</label>
            <div class="rf-number" id="rfNumber"></div>
        </div>
        
        <p class="help-text">
            Please save this RF Control Number for tracking your request.
        </p>
        
        <div class="modal-actions">
            <button class="btn btn-outline" onclick="window.location.href='<?php echo BASE_URL; ?>/forms/requisition_form/public/track_request.php'">
                Track Request
            </button>
            <button class="btn btn-primary" onclick="window.location.href='<?php echo BASE_URL; ?>/forms/requisition_form/public/submit_request.php'">
                Submit Another Request
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../../../includes/footer.php'; ?>
