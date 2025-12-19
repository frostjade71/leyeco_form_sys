<?php
/**
 * LEYECO III Requisition System - Homepage
 */

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include main configuration for BASE_URL and other constants
require_once __DIR__ . '/../../../config/config.php';

// Include the requisition system configuration
$configPath = realpath(__DIR__ . '/../app/config.php');
if (!file_exists($configPath)) {
    die('Requisition system configuration file not found. Please check your installation.');
}
require_once $configPath;

// Include helper functions
require_once __DIR__ . '/../app/functions.php';

// Include the main database configuration
$dbPath = realpath(__DIR__ . '/../../../config/database.php');
if (!file_exists($dbPath)) {
    die('Database configuration file not found. Please check your installation.');
}
require_once $dbPath;

// Include the controller
$controllerPath = realpath(__DIR__ . '/../app/RequisitionController.php');
if (!file_exists($controllerPath)) {
    die('RequisitionController not found. Please check your installation.');
}
require_once $controllerPath;

// Initialize variables
$stats = [
    'total' => 0,
    'by_status' => [
        'pending' => 0,
        'approved' => 0,
        'rejected' => 0
    ]
];

try {
    // Create controller instance
    $requisitionController = new RequisitionController();
    
    // Get statistics
    $stats = $requisitionController->getStatistics();
    
    // Ensure stats array has required keys
    if (!isset($stats['by_status'])) {
        $stats['by_status'] = [
            'pending' => 0,
            'approved' => 0,
            'rejected' => 0
        ];
    }
} catch (Exception $e) {
    error_log("Error in homepage.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    // Continue with default stats if there's an error
}

$flashMessage = getFlashMessage();

// Page configuration for main header
$page_title = 'Requisition System';
$additional_css = ['/forms/requisition_form/public/homepage.css'];

// Include main header
require_once __DIR__ . '/../../../includes/header.php';
?>

<?php if ($flashMessage): ?>
    <div class="alert alert-<?php echo e($flashMessage['type']); ?>">
        <?php echo e($flashMessage['message']); ?>
    </div>
<?php endif; ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <main class="main">
        <section class="hero">
            <h2>Requisition System</h2>
            <p>Streamlined Material Request and Approval Management</p>
            <p class="hero-desc">Submit requisition requests, track their progress, and manage approvals efficiently through our 5-level sequential approval workflow.</p>
            
            <div class="hero-actions">
                <a href="submit_request.php" class="btn btn-primary btn-large">
                    📝 Submit New Request
                </a>
                <a href="#track-request" class="btn btn-secondary btn-large">
                    🔍 Track Request
                </a>
            </div>
        </section>

        <section class="stats">
            <h3>System Statistics</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo number_format($stats['total'] ?? 0); ?></div>
                    <div class="stat-label">Total Requests</div>
                </div>
                <div class="stat-card stat-pending">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['pending'] ?? 0); ?></div>
                    <div class="stat-label">Pending Review</div>
                </div>
                <div class="stat-card stat-approved">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['approved'] ?? 0); ?></div>
                    <div class="stat-label">Approved</div>
                </div>
                <div class="stat-card stat-rejected">
                    <div class="stat-number"><?php echo number_format($stats['by_status']['rejected'] ?? 0); ?></div>
                    <div class="stat-label">Rejected</div>
                </div>
            </div>
        </section>

        <section class="track-request" id="track-request">
            <h3>Track Your Request</h3>
            <p>Enter your RF control number to view the status of your requisition</p>
            
            <form action="track_request.php" method="GET" class="search-form">
                <input 
                    type="text" 
                    name="rf" 
                    placeholder="Enter RF control number (e.g., RF-20251217-0001)" 
                    required
                    pattern="RF-\d{8}-\d{4}"
                    class="search-input"
                >
                <button type="submit" class="btn btn-primary">Track Request</button>
            </form>
        </section>

        <section class="info">
            <h3>How It Works</h3>
            <div class="steps">
                <div class="step">
                    <div class="step-number">1</div>
                    <h4>Submit Request</h4>
                    <p>Fill out the requisition form with your material requirements. No login required!</p>
                </div>
                <div class="step">
                    <div class="step-number">2</div>
                    <h4>Get RF Number</h4>
                    <p>Receive a unique RF control number for tracking your request</p>
                </div>
                <div class="step">
                    <div class="step-number">3</div>
                    <h4>Track Progress</h4>
                    <p>Monitor your request status in real-time using your RF control number</p>
                </div>
                <div class="step">
                    <div class="step-number">4</div>
                    <h4>Approval Process</h4>
                    <p>Your request goes through a 5-level sequential approval workflow</p>
                </div>
            </div>
        </section>

        <section class="workflow">
            <h3>5-Level Approval Workflow</h3>
            <div class="workflow-steps">
                <?php foreach (REQ_APPROVAL_LEVELS as $level => $description): ?>
                    <?php 
                        $parts = explode(' - ', $description);
                        $action = $parts[0];
                        $role = $parts[1] ?? '';
                    ?>
                    <div class="workflow-step">
                        <div class="workflow-number"><?php echo $level; ?></div>
                        <div class="workflow-details">
                            <h4><?php echo e($action); ?></h4>
                            <p><?php echo e($role); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>

        <section class="important-info">
            <h3>📌 Important Information</h3>
            <ul>
                <li><strong>No Login Required:</strong> Employees can submit and track requests without creating an account.</li>
                <li><strong>Unique RF Number:</strong> Each request receives a unique control number (Format: RF-YYYYMMDD-XXXX).</li>
                <li><strong>Sequential Approval:</strong> All 5 levels must approve in order for the request to be completed.</li>
                <li><strong>Real-time Tracking:</strong> Check your request status anytime using your RF Control Number.</li>
                <li><strong>Approver Access:</strong> Authorized approvers can login to review and process requests at their level.</li>
            </ul>
        </section>
    </main>
</div>

<?php
// Include main footer
require_once __DIR__ . '/../../../includes/footer.php';
?>
